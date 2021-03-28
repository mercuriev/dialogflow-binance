<?php
namespace Db;

abstract class AbstractModel implements \ArrayAccess
{
    public function __construct(array $values = [])
    {
        foreach ($values as $k => $v) {
            $this->$k = $v;
        }
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function toArray()
    {
        return (array) $this;
    }
    public function toJson()
    {
        return json_encode_pretty($this);
    }

    public function offsetExists ($offset)
    {
        return property_exists($this, $offset);
    }
    public function & offsetGet ($offset)
    {
        return $this->$offset;
    }
    public function offsetSet ($offset, $value)
    {
        $this->$offset = $value;
    }
    public function offsetUnset ($offset)
    {
        unset($this->$offset);
    }
}
