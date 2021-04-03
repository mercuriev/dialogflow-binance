<?php
namespace Action;

use Hook\Request;
use Hook\Response;

final class HistoryAction extends AbstractAction
{
    public function action(Request $query) : Response
    {
        $res = $query->toResponse();

        $symbol = $this->db->getSymbol();

        $list = $this->api->getOrders([
            'symbol' => $symbol,
            'limit' => 10,
            'timestamp' => $this->api->time()
        ]);

        $list = json_decode($list->getBody(), true);
        if (!$list) {
            $res->addText('No orders.');
        } else {
            foreach ($list as $resp) {
                $res->addText(vsprintf('%s %s %u: %0.6f for %0.6f - %s', [
                    $resp['side'], $symbol, $resp['orderId'], $resp['origQty'], $resp['price'], $resp['status']
                ]));
            }
        }

        return $res;
    }
}
