<?php

namespace ThriftSQL\Utils;

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

  private $runCount = 0;
  private $allowRerun = false;

  public function __construct( \ThriftSQL $thriftSQL, $queryStr ) {
    $this->thriftSQL = $thriftSQL;
    $this->queryStr = $queryStr;
  }

  public function allowRerun( $value ) {
    $this->allowRerun = (bool) $value;
    return $this;
  }

  /**
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   * @since 5.0.0
   */
  public function current() {
    return $this->buffer[0];
  }

  /**
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function next() {
    $this->location++;
    array_shift( $this->buffer );
  }

  /**
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   * @since 5.0.0
   */
  public function key() {
    return $this->location;
  }

  /**
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   * @since 5.0.0
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
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   * @throws \ThriftSQL\Exception
   */
  public function rewind() {
    if ( $this->runCount > 0 && !$this->allowRerun ) {
      throw new \ThriftSQL\Exception(
        'Iterator rewound, this will cause the ThriftSQL to execute again. ' .
        'Set `' . __CLASS__ . '::allowRerun(true)` to allow this behavior.'
      );
    }
    $this->runCount++;

    $this->buffer = array();
    $this->location = 0;
    $this->thriftSQLQuery = $this->thriftSQL->query( $this->queryStr )->wait();
  }
}
