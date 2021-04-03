<?php
namespace Action;

use Hook\Request;
use Hook\Response;
use Db\TableGateway;
use Db\Exception\RecordExists;

/**
 * Calculates fees for comleted orders.
 *
 */
final class FeeAction extends AbstractAction
{
    const FEE = 0.0025;

    public function action(Request $query) : Response
    {
        $res = $query->toResponse();
        $symbol = $this->db->getSymbol();

        $params = [
            'symbol' => $symbol,
            #'limit' => 100,
            #'startTime' => (new \DateTime('2021-03-20'))->getTimestamp() * 1000,
            #'orderId' => $lastOrder + 1, // do not include fee'd order itself
            'timestamp' => $this->api->time()
        ];

        $lastOrder = $this->db->getLastOrderId();
        if ($lastOrder) {
            $params['orderId'] = $lastOrder + 1;
        }

        $trades = $this->api->getTrades($params);
        $trades = json_decode($trades->getBody(), true);

        $unpaidFees = [];
        $table = new TableGateway('trade', $this->db);
        foreach ($trades as $trade) {
            try {
                $fee = round($trade['qty'] * self::FEE, 8, PHP_ROUND_HALF_UP);
                $table->insert([
                    'id' => $trade['id'], 'order_id' => $trade['orderId'],
                    'asset' => $asset =& $trade['commissionAsset'], 'fee' => $fee,
                    'raw' => json_encode($trade),
                    'created' => (new \DateTime())->setTimestamp(round($trade['time'] / 1000))->format('Y-m-d H:i:s')
                ]);
                @$unpaidFees[$asset] += $fee;
            } catch (RecordExists $e) {
                // do nothing, already paid
            }
        }

        if (count($unpaidFees)) {
            foreach ($unpaidFees as $asset => $fee) {
                $msg = sprintf('Unpaid fee %0.8f of %s', $fee, $asset);
                $res->addText($msg);
            }
        } else {
            $res->addText('No new fees.');
        }

        return $res;
    }
}
