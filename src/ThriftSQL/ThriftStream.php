<?php

namespace ThriftSQL;

class ThriftStream implements \Iterator {
	/**
	 * @var \ThriftSQL
	 */
	private $query;
	private $buffer;
	private $location;

	/**
	 * @var \ThriftSQLQuery
	 */
	private $stream;
	private $queryStr;

	public function __construct( $query, $queryStr ) {
		$this->query    = $query;
		$this->queryStr = $queryStr;
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
		$this->location ++;
		array_pop( $this->buffer );
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

		try {
			$this->buffer = array_merge( $this->buffer, $this->stream->fetch( 100 ) );
			if ( empty( $this->buffer ) ) {
				return false;
			}

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 * @throws Exception
	 */
	public function rewind() {
		try {
			$this->stream   = $this->query->query( $this->queryStr );
			$this->buffer   = array();
			$this->location = 0;
			$this->stream->wait();
		} catch ( \Exception $exception ) {
			throw new Exception( $exception->getMessage() );
		}
	}
}