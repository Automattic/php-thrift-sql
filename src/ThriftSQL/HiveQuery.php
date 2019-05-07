<?php

namespace ThriftSQL;

class HiveQuery implements \ThriftSQLQuery {

  private $_resp;
  private $_client;
  private $_ready;

  public function __construct( $response, $client ) {
    $this->_resp = $response;
    $this->_ready = false;
    $this->_client = $client;
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
        // TODO: Actually kill the query then throw exception.
        throw new \ThriftSQL\Exception( 'Hive Query Killed!' );
      }
      $TGetOperationStatusResp = $this
        ->_client
        ->GetOperationStatus( new \ThriftSQL\TGetOperationStatusReq( array(
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
        'Hive ' . \ThriftSQL\TOperationState::$__names[ $TGetOperationStatusResp->operationState ] . "\n" .
        "Error Message: {$TGetOperationStatusResp->errorMessage}"
      );
    } while ( true );

    // Check for errors
    if ( \ThriftSQL\TStatusCode::ERROR_STATUS === $this->_resp->status->statusCode ) {
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
      $TFetchResultsResp = $this->_client->FetchResults( new \ThriftSQL\TFetchResultsReq( array(
        'operationHandle' => $this->_resp->operationHandle,
        'maxRows' => $maxRows,
      ) ) );
      /**
       * NOTE: $TFetchResultsResp->hasMoreRows appears broken, it's always
       * false so one needs to keep fetching until they run out of data.
       */
      if ( $TFetchResultsResp->results instanceof \ThriftSQL\TRowSet && !empty( $TFetchResultsResp->results->columns ) ) {
        return $this->_colsToRows( $TFetchResultsResp->results->columns );
      }
      return array();
    } catch ( Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage() );
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
    return ( \ThriftSQL\TOperationState::FINISHED_STATE == $state );
  }

  private function _isOperationRunning( $state ) {
    return in_array(
      $state,
      array(
        \ThriftSQL\TOperationState::INITIALIZED_STATE, // 0
        \ThriftSQL\TOperationState::RUNNING_STATE,     // 1
        \ThriftSQL\TOperationState::PENDING_STATE,     // 7
      )
    );
  }
}
