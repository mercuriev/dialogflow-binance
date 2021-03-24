<?php
namespace Action;

use Hook\Request;

final class BalanceAction extends AbstractAction
{
    private \Binance $api;

    public function __construct(\Binance $api)
    {
        $this->api = $api;
    }

    public function action(Request $req)
    {
        $reply = $req->toResponse();
        $text = $this->buildReply();
        $reply->addText($text);
        return $reply;
    }

    private function buildReply() : string
    {
        $balances = $this->api->getBalances();
        $msg = "Available coins:\n";
        foreach ($balances as $coin) {
            $msg .= sprintf("%s: %.8f\n", $coin['asset'], $coin['free']);
        }
        return $msg;
    }
}
