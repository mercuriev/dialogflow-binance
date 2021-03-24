<?php
namespace Action;

use Hook\Request;
use Hook\Response;
use Larislackers\BinanceApi\Exception\BinanceApiException;

final class CancelAction extends AbstractAction
{
    public function action(Request $query) : Response
    {
        $res = $query->toResponse();

        try {
            $this->api->_makeApiRequest('DELETE', 'openOrders');
        } catch (BinanceApiException $e) {
            $this->log->err($e->getMessage());
            $res->addText($e->getMessage());
            return $res;
        }

        $res->addText('Canceled all orders.');

        return $res;
    }
}

