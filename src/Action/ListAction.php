<?php
namespace Action;

use Hook\Request;
use Hook\Response;

final class ListAction extends AbstractAction
{
    public function action(Request $query) : Response
    {
        $res = $query->toResponse();

        $list = $this->api->getOpenOrders([
            'symbol' => 'CAKEUSDT',
            'timestamp' => $this->api->time()
        ]);

        $list = json_decode($list->getBody(), true);
        if (!$list) {
            $res->addText('No orders.');
        } else {
            foreach ($list as $resp) {
                $res->addText(vsprintf('%u: %s %.2f CAKE for %.2f USDT - %s', [
                    $resp['orderId'], $resp['side'], $resp['origQty'], $resp['price'], $resp['status']
                ]));
            }
        }

        return $res;
    }
}
