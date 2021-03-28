<?php

final class BinanceTest extends \AbstractTest
{
    public function testAssetsToMaster()
    {
        /** @var Binance $api */
        $api = self::$sm->get(Binance::class);
        $api->assetToMaster('USDT', 0.0001);
    }
}
