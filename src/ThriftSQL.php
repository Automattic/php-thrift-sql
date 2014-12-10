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
  * @return null
  */
  public function disconnect();
}
