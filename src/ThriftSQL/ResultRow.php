<?php

namespace ThriftSQL;

class ResultRow implements \ArrayAccess
{
  private $_row;
  private $_schema;

  public function __construct($schema, $row)
  {
    $this->_row = $row;
    $this->_schema = $schema;
  }

  public function schemaArray()
  {
    $r = [];
    foreach ($this->_schema as $k=>$i) {
      $r[$k] = $this->_row[$i];
    }
    return $r;
  }

  private function offsetToIndex($offset)
  {
    if(is_int($offset)) {
      return 0 <= $offset && $offset < count($this->_row);
    } else if (is_string($offset)) {
      return ($this->_schema[strtolower($offset)] ?? null);
    }
    return null;
  }
  /**
   * @inheritDoc
   */
  public function offsetExists($offset)
  {
    return array_key_exists($this->offsetToIndex($offset), $this->_row);
  }

  /**
   * @inheritDoc
   */
  public function offsetGet($offset)
  {
    $i = $this->offsetToIndex($offset);
    if ($i !== null) {
      return $this->_row[$i];
    }
    throw new \RuntimeException("invalid row key: $offset");
  }

  /**
   * @inheritDoc
   */
  public function offsetSet($offset, $value)
  {
    throw new \RuntimeException("set row item is not allowed");
  }

  /**
   * @inheritDoc
   */
  public function offsetUnset($offset)
  {
    throw new \RuntimeException("unset row item is not allowed");
  }

  public function __get($name)
  {
    return $this->offsetGet($name);
  }
}
