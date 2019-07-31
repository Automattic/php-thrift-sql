<?php

namespace ThriftSQL;

class HiveQuery implements \ThriftSQL\Query {

  private $_client;
  private $_sessionHandle;
  private $_ready;
  private $_resp;

  public function __construct( \ThriftGenerated\TCLIServiceIf $client, \ThriftGenerated\TSessionHandle $sessionHandle ) {
    $this->_client = $client;
    $this->_sessionHandle = $sessionHandle;
  }

  public function exec( $queryStr ) {
    $queryCleaner = new \ThriftSQL\Utils\QueryCleaner();

    $this->_ready = false;
    $this->_resp = $this->_client->ExecuteStatement( new \ThriftGenerated\TExecuteStatementReq( array(
      'sessionHandle' => $this->_sessionHandle,
      'statement' => $queryCleaner->clean( $queryStr ),
      'runAsync' => true,
    ) ) );

    if ( \ThriftGenerated\TStatusCode::ERROR_STATUS === $this->_resp->status->statusCode ) {
      throw new \ThriftSQL\Exception( $this->_resp->status->errorMessage );
    }

    return $this;
  }

  public function wait() {
    if ( $this->_ready ) {
      return $this;
    }

    // Wait for results
    $sleeper = new \ThriftSQL\Utils\Sleeper();
    $sleeper->reset();
    do {
      if ( $sleeper->sleep()->getSleptSecs() > 18000 ) { // 5 Hours
        try {
          // Fire and forget cancel operation, ignore the returned:
          // \ThriftGenerated\TCancelOperationResp
          $this->_client->CancelOperation( new \ThriftGenerated\TCancelOperationReq( array(
            'operationHandle' => $this->_resp->operationHandle,
          ) ) );
        }  finally {
          throw new \ThriftSQL\Exception( 'Hive Query Killed!' );
        }
      }

      $TGetOperationStatusResp = $this
        ->_client
        ->GetOperationStatus( new \ThriftGenerated\TGetOperationStatusReq( array(
          'operationHandle' => $this->_resp->operationHandle,
        ) ) );
      if ( $this->_isOperationFinished( $TGetOperationStatusResp->operationState ) ) {
        break;
      }
      if ( $this->_isOperationRunning( $TGetOperationStatusResp->operationState ) ) {
        continue;
      }
      // Query in error state
      throw new \ThriftSQL\Exception(
        'Hive ' . \ThriftGenerated\TOperationState::$__names[ $TGetOperationStatusResp->operationState ] . "\n" .
        "Error Message: {$TGetOperationStatusResp->errorMessage}"
      );
    } while ( true );

    // Check for errors
    if ( \ThriftGenerated\TStatusCode::ERROR_STATUS === $this->_resp->status->statusCode ) {
      throw new \ThriftSQL\Exception( "HIVE QUERY ERROR: {$this->_resp->status->status->errorMessage}" );
    }

    $this->_ready = true;
    return $this;
  }

  public function fetch( $maxRows ) {
    if ( !$this->_ready ) {
      throw new \ThriftSQL\Exception( "Query is not ready. Call `->wait()` before `->fetch()`" );
    }
    try {
      $TFetchResultsResp = $this->_client->FetchResults( new \ThriftGenerated\TFetchResultsReq( array(
        'operationHandle' => $this->_resp->operationHandle,
        'maxRows' => $maxRows,
      ) ) );
      /**
       * NOTE: $TFetchResultsResp->hasMoreRows appears broken, it's always
       * false so one needs to keep fetching until they run out of data.
       */
      if ( $TFetchResultsResp->results instanceof \ThriftGenerated\TRowSet && !empty( $TFetchResultsResp->results->columns ) ) {
        return $this->_colsToRows( $TFetchResultsResp->results->columns );
      }
      return array();
    } catch ( Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage(), $e->getCode(), $e );
    }
  }

  public function close() {
    try {
      // Fire close operation and ignore return
      $this->_client->CloseOperation( new \ThriftGenerated\TCloseOperationReq( array(
        'operationHandle' => $this->_resp->operationHandle,
      ) ) );
    } finally {
      return $this;
    }
  }

  private function _colsToRows( $columns ) {
    $result = array();
    foreach ( $columns as $col => $TColumn ) {
      $values = array();
      if ( !is_null( $TColumn->boolVal ) ) {
        $values = $TColumn->boolVal->values;
      }
      if ( !is_null( $TColumn->byteVal ) ) {
        $values = $TColumn->byteVal->values;
      }
      if ( !is_null( $TColumn->i16Val ) ) {
        $values = $TColumn->i16Val->values;
      }
      if ( !is_null( $TColumn->i32Val ) ) {
        $values = $TColumn->i32Val->values;
      }
      if ( !is_null( $TColumn->i64Val ) ) {
        $values = $TColumn->i64Val->values;
      }
      if ( !is_null( $TColumn->doubleVal ) ) {
        $values = $TColumn->doubleVal->values;
      }
      if ( !is_null( $TColumn->stringVal ) ) {
        $values = $TColumn->stringVal->values;
      }
      if ( !is_null( $TColumn->binaryVal ) ) {
        $values = $TColumn->binaryVal->values;
      }
      foreach ( $values as $row => $value ) {
        $result[ $row ][ $col ] = $value;
      }
    }
    return $result;
  }

  private function _isOperationFinished( $state ) {
    return ( \ThriftGenerated\TOperationState::FINISHED_STATE == $state );
  }

  private function _isOperationRunning( $state ) {
    return in_array(
      $state,
      array(
        \ThriftGenerated\TOperationState::INITIALIZED_STATE, // 0
        \ThriftGenerated\TOperationState::RUNNING_STATE,     // 1
        \ThriftGenerated\TOperationState::PENDING_STATE,     // 7
      )
    );
  }
}
