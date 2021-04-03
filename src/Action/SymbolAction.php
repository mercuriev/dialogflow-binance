<?php
namespace Action;

use Hook\Request;
use Hook\Response;

final class SymbolAction extends AbstractAction
{
    public function action(Request $query) : Response
    {
        $newSymbol = strtoupper($query->getParam('symbol'));
        if ($newSymbol) {
            $this->db->setSymbol($newSymbol);
        }

        $symbol = $this->db->getSymbol();
        $text = "Active symbol is $symbol";

        $res = $query->toResponse()->addText($text);
        return $res;
    }
}
