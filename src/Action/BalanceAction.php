<?php
namespace Action;

use Hook\Request;
use Hook\Response;

final class BalanceAction extends AbstractAction
{
    public function action(Request $req) : Response
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
            $float = round($coin['free'] + $coin['locked'], 12);
            $msg .= sprintf("%-6s: $float\n", $coin['asset'], $float);
        }
        return $msg;
    }
}
