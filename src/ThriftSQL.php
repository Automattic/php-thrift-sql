<?php

abstract class ThriftSQL {
  /**
  * @return self
  * @throws \ThriftSQL\Exception
  */
  abstract public function connect();

  /**
  * The simplest use case; takes a query string executes it synchronously and
  * returns the entire result set after collecting it from the server.
  *
  * @param string $queryStr
  * @return array
  * @throws \ThriftSQL\Exception
  */
  abstract public function queryAndFetchAll( $queryStr );

  /**
  * Sends a query string for execution on the server and returns a
  * ThriftSQLQuery object for fetching the results manually.
  *
  * @param string $queryStr
  * @return \ThriftSQLQuery
  * @throws \ThriftSQL\Exception
  */
  abstract public function query( $queryStr );

  /**
  * @return null
  */
  abstract public function disconnect();

  /**
   * Get's a memory efficient iterator that you can use in a foreach loop.
   * If there's an error with the query, it will simply stop iterating.
   *
   * @param string $queryStr
   *
   * @return \ThriftSQL\Utils\Iterator
   * @throws \ThriftSQL\Exception
   */
  public function getIterator( $queryStr ) {
    try {
      return new \ThriftSQL\Utils\Iterator( $this, $queryStr );
    } catch ( \Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }
  }
}
