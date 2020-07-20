<?php

abstract class ThriftSQL {

  const VERSION = '0.3.1';
  const USERNAME_DEFAULT = 'php-thrift-sql';

  /**
  * @return self
  * @throws \ThriftSQL\ThriftSQLException
  */
  abstract public function connect();

  /**
  * Sends a query string for execution on the server and returns a
  * \ThriftSQL\Query object for fetching the results manually.
  *
  * @param string $statement
  * @return \ThriftSQL\Query
  * @throws \ThriftSQL\ThriftSQLException
  */
  abstract public function query( $statement );


  abstract public function disconnect();
}
