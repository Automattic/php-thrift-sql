<?php

abstract class ThriftSQL {

  const VERSION = '0.3.1';
  const USERNAME_DEFAULT = 'php-thrift-sql';

  /**
  * @return self
  * @throws \ThriftSQL\Exception
  */
  abstract public function connect();

  /**
  * Sends a query string for execution on the server and returns a
  * \ThriftSQL\Query object for fetching the results manually.
  *
  * @param string $queryStr
  * @return \ThriftSQL\Query
  * @throws \ThriftSQL\Exception
  */
  abstract public function query( $queryStr );

  /**
  * @return null
  */
  abstract public function disconnect();

  /**
  * The simplest use case; takes a query string executes it synchronously and
  * returns the entire result set after collecting it from the server.
  *
  * @param string $queryStr
  * @return array
  * @throws \ThriftSQL\Exception
  */
  public function queryAndFetchAll( $queryStr ) {
    $iterator = $this->getIterator( $queryStr );

    $resultTuples = array();
    foreach( $iterator as $rowNum => $row ) {
      $resultTuples[] = $row;
    }

    return $resultTuples;
  }

  /**
   * Gets a memory efficient iterator that you can use in a foreach loop.
   *
   * @param string $queryStr
   * @return \ThriftSQL\Utils\Iterator
   * @throws \ThriftSQL\Exception
   */
  public function getIterator( $queryStr ) {
    return new \ThriftSQL\Utils\Iterator( $this, $queryStr );
  }
}
