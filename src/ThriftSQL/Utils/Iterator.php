<?php
namespace ThriftSQL\Utils;

class Iterator implements \Iterator
{
  const BUFFER_ROWS = 64;

  /** @var \ThriftSQL\Query */
  private $query;

  private $buffer;
  private $location;

  private $queryCount = 0;
  private $rewindCount = 0;

  public function __construct(\ThriftSQL\Query $query)
  {
    $this->query = $query;
    $this->doQuery();
  }

  private function doQuery()
  {
    $this->queryCount++;
    $this->buffer = array();
    $this->location = 0;
    $this->query->exec();
  }

  public function schema()
  {
    return $this->query->schema();
  }

  /**
   * @return array|object|\ThriftSQL\ResultRowSchema
   */
  public function current()
  {
    return $this->buffer[0];
  }

  /**
   * Move forward to next element
   * @return void Any returned value is ignored.
   */
  public function next()
  {
    $this->location++;
    array_shift($this->buffer);
  }

  /**
   * Return the key of the current element
   * @return mixed scalar on success, or null on failure.
   */
  public function key()
  {
    return $this->location;
  }

  /**
   * Checks if current position is valid
   * @return boolean The return value will be casted to boolean and then evaluated.
   *         Returns true on success or false on failure.
   * @throws \ThriftSQL\ThriftSQLException
   */
  public function valid()
  {
    if (!empty($this->buffer)) {
      return true;
    }

    // Buffer is empty let's refill it
    $this->buffer = $this->query->fetch(self::BUFFER_ROWS);

    // Buffer is full again!
    if (!empty($this->buffer)) {
      return true;
    }

    $this->query->close();
    return false;
  }

  /**
   * Rewind the Iterator to the first element
   * @return void Any returned value is ignored.
   * @throws \ThriftSQL\ThriftSQLException
   */
  public function rewind()
  {
    if ($this->rewindCount > 0) {
      throw new \ThriftSQL\ThriftSQLException('Query already executed.');
    }

    $this->rewindCount++;
    if ($this->rewindCount > $this->queryCount) {
      $this->doQuery();
    }
    $this->query->wait();
  }
}
