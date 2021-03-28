<?php
use Larislackers\BinanceApi\BinanceApiContainer;
use Laminas\Log\Logger;
use Larislackers\BinanceApi\Exception\BinanceApiException;

final class Binance extends BinanceApiContainer
{
    private Logger $log;

    public function __construct(Logger $log, array $config)
    {
        $this->log = $log;
        parent::__construct(
            $config['binance']['key'],
            $config['binance']['secret']
        );
    }

    public function getBalances() : array
    {
        $res = $this->getAccount(['timestamp' => $this->time()]);
        $res = json_decode($res->getBody(), true) ?? [];

        $res = array_filter($res['balances'], function($el) {
            return ($el['free'] > 0 || $el['locked'] > 0);
        });

        return $res;
    }

    /**
     * Tx ID or null.
     */
    public function assetToMaster(string $symbol, float $amount) : ?int
    {
        try {
            $res = $this->_makeApiRequest('POST', 'transfer/subToMaster', 'SAPI_SIGNED', [
                'asset' => $symbol,
                'amount' => $amount,
                'timestamp' => $this->time()
            ]);
            $res = json_decode($res->getBody(), true);
            return (int) $res['txnId'];
        }
        catch (BinanceApiException $e) {
            $this->log->err($e->getCode() . ': ' . $e->getMessage());
            return null;
        }
    }

    public function time() : int
    {
        return intval(microtime(true) * 1000);
    }
}
