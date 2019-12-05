<?php

namespace ThriftSQL;

abstract class Query implements \IteratorAggregate
{
  const RESULT_ARRAY = 0;
  const RESULT_SCHEMA = 1;

  protected $resultMode = 0;

  public function setResultMode($v)
  {
    $this->resultMode = $v;
  }

  /**
   * Starts executing the given query
   * @return self
   */
  abstract public function exec();

  /**
   * Waits for the query to complete execution
   * @return self
   */
  abstract public function wait();

  /**
   * Fetches `n` rows from query results.
   * Before calling this method, you must call `wait()`.
   *
   * @param int $numRows
   * @return array|object[]|ResultRowSchema[]
   */
  abstract public function fetch($numRows);

  /**
   * Close the current query context.
   */
  abstract public function close();

  /**
   * @return array
   */
  abstract public function schema();

  /** @return iterable|object[]|ResultRowSchema[] */
  public function getIterator()
  {
    return new \ThriftSQL\Utils\Iterator($this);
  }

  /**
   * @return array|object[]|ResultRowSchema[]
   */
  public function fetchAll()
  {
    $iterator = $this->getIterator();
    $result = array();
    foreach ($iterator as $row) {
      $result[] = $row;
    }
    return $result;
  }
}
