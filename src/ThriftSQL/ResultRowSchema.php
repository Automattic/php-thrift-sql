<?php

namespace ThriftSQL;

class ResultRowSchema implements \ArrayAccess
{
  private $row;
  private $schema;

  public function __construct($schema, $row)
  {
    $this->row = $row;
    $this->schema = $schema;
  }

  public function schemaArray()
  {
    $r = [];
    foreach ($this->schema as $k=> $i) {
      $r[$k] = $this->row[$i];
    }
    return $r;
  }

  private function offsetToIndex($offset)
  {
    if(is_int($offset)) {
      return 0 <= $offset && $offset < count($this->row) ? $offset : null;
    } else if (is_string($offset)) {
      return ($this->schema[strtolower($offset)] ?? null);
    }
    return null;
  }
  /**
   * @inheritDoc
   */
  public function offsetExists($offset)
  {
    return array_key_exists($this->offsetToIndex($offset), $this->row);
  }

  /**
   * @inheritDoc
   */
  public function offsetGet($offset)
  {
    $i = $this->offsetToIndex($offset);
    if ($i !== null) {
      return $this->row[$i];
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
