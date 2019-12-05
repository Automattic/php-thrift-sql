<?php

namespace ThriftSQL\Utils;

use ThriftSQL\ResultRow;

class Iterator implements \Iterator {

  const BUFFER_ROWS = 64;

  /**
   * @var \ThriftSQL
   */
  private $thriftSQL;

  /**
   * @var \ThriftSQL\Query
   */
  private $thriftSQLQuery;

  private $queryStr;
  private $buffer;
  private $location;

  private $queryCount = 0;
  private $rewindCount = 0;
  private $allowRerun = false;
  private $useSchema = false;

  public function __construct( \ThriftSQL $thriftSQL, $queryStr ) {
    $this->thriftSQL = $thriftSQL;
    $this->queryStr = $queryStr;

    $this->queryCount = 1;
    $this->doQuery();
  }

  private function doQuery() {
    $this->buffer = array();
    $this->location = 0;
    $this->thriftSQLQuery = $this->thriftSQL->query( $this->queryStr )->wait();
  }

  public function allowRerun( $value ) {
    $this->allowRerun = (bool) $value;
    return $this;
  }

  public function useSchema( $value ) {
    $this->useSchema = (bool) $value;
    return $this;
  }

  public function schema() {
    return $this->thriftSQLQuery->schema();
  }

  /**
   * @return array|ResultRow
   */
  public function current() {
    return $this->useSchema ? new ResultRow($this->schema(), $this->buffer[0]) : $this->buffer[0];
  }

  /**
   * Move forward to next element
   * @return void Any returned value is ignored.
   */
  public function next() {
    $this->location++;
    array_shift( $this->buffer );
  }

  /**
   * Return the key of the current element
   * @return mixed scalar on success, or null on failure.
   */
  public function key() {
    return $this->location;
  }

  /**
   * Checks if current position is valid
   * @return boolean The return value will be casted to boolean and then evaluated.
   *         Returns true on success or false on failure.
   * @throws \ThriftSQL\Exception
   */
  public function valid() {
    if ( ! empty( $this->buffer ) ) {
      return true;
    }

    // Buffer is empty let's refill it
    $this->buffer = $this->thriftSQLQuery->fetch( self::BUFFER_ROWS );

    // Buffer is full again!
    if ( ! empty( $this->buffer ) ) {
      return true;
    }

    $this->thriftSQLQuery->close();
    return false;
  }

  /**
   * Rewind the Iterator to the first element
   * @return void Any returned value is ignored.
   * @throws \ThriftSQL\Exception
   */
  public function rewind() {
    if ( $this->rewindCount > 1 && !$this->allowRerun ) {
      throw new \ThriftSQL\Exception(
        'Iterator rewound, this will cause the ThriftSQL to execute again. ' .
        'Set `' . __CLASS__ . '::allowRerun(true)` to allow this behavior.'
      );
    }

    $this->rewindCount++;
    if ($this->rewindCount > $this->queryCount) {
      $this->queryCount++;
      $this->doQuery();
    }
    $this->thriftSQLQuery->wait();
  }
}
