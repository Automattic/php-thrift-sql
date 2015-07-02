<?php

interface ThriftSQLQuery {
  /**
   * Waits for the query to complete execution
   * @return self
   * @throws \ThriftSQL\Exception
   */
  public function wait();

  /**
  * Fetches `n` rows from query results. 
  * Before calling this method, you must call `wait()`.
  *
  * @param int $numRows
  * @return array
  * @throws \ThriftSQL\Exception
  */
  public function fetch( $numRows );
}
