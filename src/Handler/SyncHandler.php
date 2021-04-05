<?php
namespace Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Db\TableGateway;
use Laminas\Log\Logger;
use Laminas\Diactoros\Response\EmptyResponse;
use Larislackers\BinanceApi\Exception\BinanceApiException;
use Db\Exception\RecordExists;

class SyncHandler implements RequestHandlerInterface
{
    public function __construct(\Binance $api, \Db $db, Logger $log, array $config)
    {
        $this->api = $api;
        $this->db = $db;
        $this->log = $log;
        $this->fee = $config['fee'];
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        // look for status updates among NEW orders in db
        $orders = new TableGateway('order', $this->db);
        $new = $orders->select(['status' => 'NEW']);
        foreach ($new as $order) {
            $apiOrder = $this->getOrder($order['id']);
            if ($apiOrder) {
                switch ($apiOrder['status']) {
                    case 'NEW': break; // no changes

                    case 'FILLED':
                        $this->filledOrder($apiOrder);
                        // no break to save all statuses

                    default:
                        $orders->update(['status' => $apiOrder['status']], ['id' => $order['id']]);
                }
            }
            else {
                $this->log->warn('Failed to get order: '.$order['id']);
                $orders->update(['status' => 'ENOTEXIST'], ['id' => $order['id']]);
                continue;
            }
        }

        return new EmptyResponse(200);
    }

    private function getOrder(int $id) : ?array
    {
        try {
            $order = $this->api->getOrder([
                'symbol'    => $this->db->getSymbol(),
                'orderId'   => $id,
                'timestamp' => $this->api->time()
            ]);
            return json_decode($order->getBody(), true);
        }
        catch (BinanceApiException $e) {
            $this->log->err($e->getMessage());
            return null;
        }
    }

    private function filledOrder(array $order)
    {
        $trades = $this->api->getTrades([
            'symbol' => $order['symbol'],
            'orderId' => $order['orderId'],
            'timestamp' => $this->api->time()
        ]);
        $trades = json_decode($trades->getBody(), true);

        $table = new TableGateway('trade', $this->db);
        foreach ($trades as $trade) {
            try {
                $fee = round($trade['qty'] * $this->fee - $trade['commission'], 8, PHP_ROUND_HALF_UP);
                $table->insert([
                    'id' => $trade['id'],
                    'order_id' => $trade['orderId'],
                    'asset' => $asset =& $trade['commissionAsset'],
                    'fee' => $fee,
                    'raw' => json_encode($trade),
                    'created' => (new \DateTime())->setTimestamp(round($trade['time'] / 1000))->format('Y-m-d H:i:s')
                ]);
                $this->api->assetToMaster($asset, $fee);
            } catch (RecordExists $e) {
                // do nothing, already paid
            }
        }

    }
}

