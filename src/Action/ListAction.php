<?php
namespace Action;

use Hook\Request;
use Hook\Response;

final class ListAction extends AbstractAction
{
    public function action(Request $query) : Response
    {
        $res = $query->toResponse();

        $symbol = $this->db->getSymbol();

        $list = $this->api->getOpenOrders([
            'symbol' => $symbol,
            'timestamp' => $this->api->time()
        ]);

        $list = json_decode($list->getBody(), true);
        if (!$list) {
            $res->addText('No orders.');
        } else {
            foreach ($list as $resp) {
                $price = (float) $resp['price'];
                $res->addText(vsprintf("%s %s %u: %.8g for $price", [
                    $resp['side'], $symbol, $resp['orderId'], $resp['origQty'],
                ]));
            }
        }

        return $res;
    }
}
