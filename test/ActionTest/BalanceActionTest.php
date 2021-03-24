<?php
namespace ActionTest;

use Action\BalanceAction;

class BalanceActionTest extends \AbstractTest
{
    public function testBalance()
    {
        /** @var \Binance $api */
        $api = self::$sm->get(\Binance::class);
        $api->getBalances();

        $sut = self::$sm->get(BalanceAction::class);
    }
}
