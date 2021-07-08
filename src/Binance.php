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

    public function allIn(string $symbol, string $side) : float
    {
        $side = strtoupper($side);
        $balances = $this->getBalances();
        foreach ($balances as $balance) {
            // Symbol starts with asset name
            if ($side == 'SELL' && 0 === strpos($symbol, $balance['asset'])) {
                $all = $balance['free'];
            }
            // Symbol ends with asset name
            if ($side == 'BUY' && preg_match("/{$balance['asset']}$/i", $symbol)) {
                $all = $balance['free'];
            }
        }
        if (isset($all)) {
            switch (strtoupper($symbol)) {
                case 'SHIBUSDT': $all = (int) $all;
                default:
                    return $all;
            }
        }
        else {
            throw new \UnderflowException("No free assets to $side on $symbol.");
        }
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

    public function assetToPeer(string $symbol, float $amount) : ?int
    {
        try {
            $res = $this->_makeApiRequest('POST', 'asset/transfer', 'SAPI_SIGNED', $req = [
                'type'      => 'MAIN_C2C',
                'asset'     => $symbol,
                'amount'    => $amount,
                'timestamp' => $this->time()
            ]);
            $res = json_decode($res->getBody(), true);
            $this->log->debug("Sent to master: $amount $symbol");
            return (int) $res['tranId'];
        }
        catch (BinanceApiException $e) {
            $this->log->err($e->getCode() . ': ' . $e->getMessage() . '. Req: '.json_encode_pretty($req));
            return null;
        }
    }

    /**
     * Tx ID or null.
     */
    public function assetToMaster(string $symbol, float $amount) : ?int
    {
        try {
            $res = $this->_makeApiRequest('POST', 'transfer/subToMaster', 'SAPI_SIGNED', $req = [
                'asset' => $symbol,
                'amount' => $amount,
                'timestamp' => $this->time()
            ]);
            $res = json_decode($res->getBody(), true);
            $this->log->debug("Sent to master: $amount $symbol");
            return (int) $res['txnId'];
        }
        catch (BinanceApiException $e) {
            $this->log->err($e->getCode() . ': ' . $e->getMessage() . '. Req: '.json_encode_pretty($req));
            return null;
        }
    }

    public function time() : int
    {
        return intval(microtime(true) * 1000);
    }
}
