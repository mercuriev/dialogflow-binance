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
        if (!$list) throw new \RuntimeException($list->getBody());

        $res->addText(json_encode_pretty($list));

        return $res;
    }
}
