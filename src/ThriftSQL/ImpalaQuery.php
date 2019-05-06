<?php

namespace ThriftSQL;

class ImpalaQuery implements \ThriftSQLQuery {

  private $_handle;
  private $_client;
  private $_ready;
  private $_lastResponse;

  public function __construct( $queryStr, $client ) {
    $queryCleaner = new \ThriftSQL\Utils\QueryCleaner();

    $this->_client = $client;
    $this->_ready = false;
    $this->_handle = $this->_client->query( new \ThriftSQL\Query( array(
      'query' => $queryCleaner->clean( $queryStr ),
    ) ) );
  }

  public function wait() {
    if ( $this->_ready ) {
      return $this;
    }

    // Wait for query to be ready
    $sleeper = new \ThriftSQL\Utils\Sleeper();
    $sleeper->reset();
    do {
      $slept = $sleeper->sleep()->getSleptSecs();
      if ( $slept > 18000 ) { // 5 Hours
        // TODO: Actually kill the query then throw exception.
        throw new \ThriftSQL\Exception( 'Impala Query Killed!' );
      }

      $state = $this
        ->_client
        ->get_state( $this->_handle );

      if ( $this->_isOperationFinished( $state ) ) {
        break;
      }

      if ( $this->_isOperationRunning( $state ) ) {
        continue;
      }

      // Query in error state
      throw new \ThriftSQL\Exception(
        'Query is in an error state: ' . \ThriftSQL\QueryState::$__names[ $state ]
      );

    } while (true);

    // Wait for results by fetching some rows -- triggers query to run
    $this->_fetchResponse( 2 );

    $this->_ready = true;
    return $this;
  }

  public function fetch( $maxRows ) {
    $result = array();
    if ( !$this->_ready ) {
      throw new \ThriftSQL\Exception( "Query is not ready. Call `->wait()` before `->fetch()`" );
    }
    try {
      if ( !( $this->_lastResponse instanceof \ThriftSQL\Results ) ) {
        $this->_fetchResponse( $maxRows );
      }

      $result = $this->_parseResponse( $this->_lastResponse );
      $this->_lastResponse = null;
    } catch( Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }

    return $result;
  }

  private function _fetchResponse( $maxRows ) {
    $sleeper = new \ThriftSQL\Utils\Sleeper();
    $sleeper->reset();

    do {
      $this->_lastResponse = $this->_client->fetch( $this->_handle, false, $maxRows );
      if ( $this->_lastResponse->ready ) {
        return;
      }

      if ( $sleeper->sleep()->getSleptSecs() > 60 ) { // 1 minute
        throw new \ThriftSQL\Exception( 'Impala Query took too long to fetch!' );
      }

    } while ( true );
  }

  private function _parseResponse( $response ) {
    $result = array();
    foreach ( $response->data as $row => $rawValues ) {
      $values = explode( "\t", $rawValues, count( $response->columns ) );

      foreach ( $response->columns as $col => $dataType ) {
        switch ($dataType) {
          case 'int':
          case 'bigint':
          case 'smallint':
          case 'tinyint':
            $result[ $row ][ $col ] = intval( $values[ $col ] );
            break;

          case 'decimal':
          case 'double':
          case 'float':
          case 'real':
            $result[ $row ][ $col ] = floatval( $values[ $col ] );
            break;

          case 'boolean':
            $result[ $row ][ $col ] = ( 'true' === $values[ $col ] );
            break;

          case 'char':
          case 'string':
          case 'varchar':
          case 'timestamp':
          default:
            $result[ $row ][ $col ] = $values[ $col ];
            break;
        }
      }
    }
    return $result;
  }

  private function _isOperationFinished( $state ) {
    return ( \ThriftSQL\QueryState::FINISHED == $state );
  }

  private function _isOperationRunning( $state ) {
    return in_array(
      $state,
      array(
        \ThriftSQL\QueryState::CREATED,     // 0
        \ThriftSQL\QueryState::INITIALIZED, // 1
        \ThriftSQL\QueryState::COMPILED,    // 2
        \ThriftSQL\QueryState::RUNNING,     // 3
      )
    );
  }
}
