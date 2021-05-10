<?php
namespace ActionTest;

use Action\OrderAction;
use Hook\Request;

class OrderActionTest extends \AbstractTest
{
    public function testMarket()
    {
        $sut = self::$sm->get(OrderAction::class);

        $req = Request::fromFile(__DIR__ . '/../samples/buy.market.json');
        $res = $sut->action($req);
    }
}
