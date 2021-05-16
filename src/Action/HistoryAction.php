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
                $price = $resp['price'] > 0 ? $resp['price'] : ($resp['cummulativeQuoteQty'] / $resp['executedQty']);
                $res->addText(vsprintf('%s %s %u: %.12g for %.12g - %s', [
                    $resp['side'], $symbol, $resp['orderId'], $resp['origQty'], $price, $resp['status']
                ]));
            }
        }

        return $res;
    }
}
