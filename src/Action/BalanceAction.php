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
            $msg .= sprintf("%-6s: %.18g\n", $coin['asset'], $coin['free'] + $coin['locked']);
        }
        return $msg;
    }
}
