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
            $this->log->debug(json_encode_pretty($list));
            foreach ($list as $resp) {
                $res->addText(vsprintf('%s %s %u: %.8g for %.8g - %s', [
                    $resp['side'], $symbol, $resp['orderId'], $resp['origQty'], $resp['price'], $resp['status']
                ]));
            }
        }

        return $res;
    }
}
