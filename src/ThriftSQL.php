<?php

interface ThriftSQL {
  /**
  * @return self
  * @throws \ThriftSQL\Exception
  */
  public function connect();

  /**
  * The simplest use case; takes a query string executes it synchronously and
  * returns the entire result set after collecting it from the server.
  *
  * @param string $queryStr
  * @return array
  * @throws \ThriftSQL\Exception
  */
  public function queryAndFetchAll( $queryStr );

  /**
   * Get's a memory efficient iterator that you can use in a foreach loop.
   * If there's an error with the query, it will simply stop iterating.
   *
   * @param string $queryStr
   *
   * @return \ThriftSQL\Utils\Iterator
   * @throws \ThriftSQL\Exception
   */
  public function getIterator( $queryStr );

  /**
  * Sends a query string for execution on the server and returns a
  * ThriftSQLQuery object for fetching the results manually.
  *
  * @param string $queryStr
  * @return \ThriftSQLQuery
  * @throws \ThriftSQL\Exception
  */
  public function query( $queryStr );

  /**
  * @return null
  */
  public function disconnect();
}
