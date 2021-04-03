<?php
namespace Action;

use Hook\Request;
use Hook\Response;
use Larislackers\BinanceApi\Exception\BinanceApiException;
use Db\TableGateway;
use Db\Exception\RecordExists;

final class OrderAction extends AbstractAction
{
    public function action(Request $query) : Response
    {
        $res = $query->toResponse();

        $amount = floatval($query->getParam('amount'));
        $price  = floatval($query->getParam('price'));

        $symbol = $this->db->getSymbol();

        $params = [
            'symbol'        => $symbol,
            'side'          => strtoupper($query->getParam('way')),
            'type'          => 'LIMIT',
            'quantity'      => $amount,
            'price'         => $price,
            'timeInForce'   => 'gtc',
            'timestamp'     => $this->api->time()
        ];

        try {
            $order = $this->api->postOrder($params);
            $order = json_decode($order->getBody(), true);
            if (!$order) throw new \RuntimeException($order->getBody());
        } catch (BinanceApiException $e) {
            $this->log->err($e->getMessage());
            $res->addText($e->getMessage());
            return $res;
        }

        $orders = new TableGateway('order', $this->db);
        try {
            $orders->insert([
                'id' => $order['orderId'],
                'symbol' => $order['symbol'],
                'raw'   => json_encode($order)
            ]);
        } catch (RecordExists $e) {
            $this->log->warn($e->getMessage());
        }

        $res->addText(vsprintf('%s %s %u: %.7g for %.7g', [
            $order['side'], $symbol, $order['orderId'], $order['origQty'], $order['price'],
        ]));

        return $res;
    }
}

