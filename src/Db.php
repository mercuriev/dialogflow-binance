<?php
use Laminas\Db\Adapter\Adapter;

final class Db extends Adapter
{
    public function __construct(array $config = null)
    {
        return parent::__construct($config['db']);
    }

    public function getSymbol() : string
    {
        $row = $this->query('SELECT value FROM config WHERE id = "symbol"')->execute();
        if ($row->count()) {
            return $row->current()['value'];
        } else {
            throw new \RuntimeException('No symbol in db.');
        }
    }

    public function setSymbol(string $symbol) : self
    {
        $this->query('UPDATE config SET value = ? WHERE id = "symbol"')->execute([$symbol]);
        return $this;
    }

    public function getLastOrderId() : ?int
    {
        $oid = $this->query('SELECT MAX(order_id) AS oid FROM trade')->execute()->current();
        return $oid['oid'];
    }
}
