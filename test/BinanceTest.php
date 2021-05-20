<?php

final class BinanceTest extends \AbstractTest
{
    public function testAssetsToMaster()
    {
        /** @var Binance $api */
        $api = self::$sm->get(Binance::class);
        $api->assetToMaster('USDT', 0.0001);
    }

    public function testGetBalances()
    {
        /** @var Binance $api */
        $api = self::$sm->get(Binance::class);
        $res = $api->allIn('BTCUSDT', 'SELL');

        var_dump($res);
    }
}
