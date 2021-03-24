<?php
namespace Action;

use Hook\Request;
use Hook\Response;
use Larislackers\BinanceApi\Exception\BinanceApiException;

final class OrderAction extends AbstractAction
{
    public function action(Request $query) : Response
    {
        $res = $query->toResponse();

        $amount = floatval($query->getParam('amount'));
        $price  = floatval($query->getParam('price'));

        $params = [
            'symbol'        => 'CAKEUSDT',
            'side'          => strtoupper($query->getParam('way')),
            'type'          => 'LIMIT',
            'quantity'      => $amount,
            'price'         => $price,
            'timeInForce'   => 'gtc',
            'timestamp'     => $this->api->time()
        ];

        try {
            $resp = $this->api->postOrder($params);
        } catch (BinanceApiException $e) {
            $this->log->err($e->getMessage());
            $res->addText($e->getMessage());
            return $res;
        }

        $resp = json_decode($resp->getBody(), true);
        if (!$resp) throw new \RuntimeException($resp->getBody());

        #$res->addText(json_encode_pretty($resp));
        $res->addText(vsprintf('%u: %s %.2f CAKE for %.2f USDT - %s', [
            $resp['orderId'], $resp['side'], $resp['origQty'], $resp['price'], $resp['status']
        ]));

        return $res;
    }
}

