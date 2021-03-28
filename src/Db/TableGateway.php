<?php
namespace Db;

use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Delete;

class TableGateway extends \Laminas\Db\TableGateway\TableGateway
{
    public function __call($method, $arguments)
    {
        $field = @strtolower(sscanf($method, 'findBy%s')[0]);
        if ($field) {
            $model = $this->select([strtolower($field) => $arguments[0]])->current();
            if ($model) return $model;
            else throw new Exception\RecordNotFound(sprintf('No %s with %s = %s', $this->getTable(), $field, $arguments[0]));
        }
        else {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$method.'()', E_USER_ERROR);
        }
    }

    public function __toString()
    {
        return $this->table;
    }

    public function find($id)
    {
        return $this->findById($id);
    }

    /**
     * Support direct model insert.
     *
     * @param array|AbstractModel $set
     * @param bool $refresh Query for the inserted model and return it
     * @throws Exception\RecordExists If an unique key in table trigger error.
     * @throws Exception\ForeignKey
     * @return mixed|AbstractModel  Created record or it's uuid if array used.
     */
    public function insert($set, $fetchRecord = true)
    {
        if ($set instanceof AbstractModel) {
            $hydrator = $this->getResultSetPrototype()->getHydrator();
            $set = $hydrator->extract($set);
        }

        $rows = parent::insert($set);
        if (!$rows) throw new \RuntimeException($rows);

        $id = $this->lastInsertValue ?: @$set['id'];
        return $fetchRecord ? $this->find($id) : $id;
    }

    /**
     * Support direct model insert.
     *
     * @param array|AbstractModel $set
     * @return mixed|AbstractModel  Created record or it's uuid if array used.
     */
    public function update($set, $where = null, array $joins = null)
    {
        if ($set instanceof AbstractModel) {
            $set = $this->getResultSetPrototype()->getHydrator()->extract($set);
            if (!$set['id']) throw new \InvalidArgumentException('ID is not available');
            parent::update($set, ['id' => $set['id']], $joins);
            return $this->find($set['id']);
        }

        return parent::update($set, $where, $joins);
    }

    public function refresh(AbstractModel $model)
    {
        return $this->find($model->id);
    }


    protected function executeInsert(Insert $insert)
    {
        try {
            return parent::executeInsert($insert);
        }
        catch (InvalidQueryException $e) {
            throw $this->identifyError($e);
        }
    }

    protected function executeSelect(Select $select)
    {
        try {
            return parent::executeSelect($select);
        }
        catch (InvalidQueryException $e) {
            throw $this->identifyError($e);
        }
    }

    protected function executeUpdate(Update $update)
    {
        try {
            return parent::executeUpdate($update);
        }
        catch (InvalidQueryException $e) {
            throw $this->identifyError($e);
        }
    }

    protected function executeDelete(Delete $delete)
    {
        try {
            return parent::executeDelete($delete);
        }
        catch (InvalidQueryException $e) {
            throw $this->identifyError($e);
        }
    }

    /**
     * Parse error message and return our own Exception class if defined.
     * Useful to catch specific SQL errors in client code.
     *
     * @param InvalidQueryException $e
     * @return InvalidQueryException
     */
    private function identifyError(InvalidQueryException $e) : InvalidQueryException
    {
        preg_match('~\b\d{4}\b~', $e->getMessage(), $m);
        $code = @intval($m[0]) ?: 0;

        switch ($code) {
            case 1054: return new Exception\UnknownColumn($e->getMessage(), $code, $e);
            case 1062: return new Exception\RecordExists( $e->getMessage(), $code, $e);
            case 1452: return new Exception\ForeignKey(   $e->getMessage(), $code, $e);
            default: return $e;
        }
    }
}
