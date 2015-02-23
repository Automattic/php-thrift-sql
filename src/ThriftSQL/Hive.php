<?php
namespace ThriftSQL;

class Hive implements \ThriftSQL {

  private $_host;
  private $_port;
  private $_username;
  private $_password;
  private $_timeout;
  private $_transport;
  private $_client;
  private $_sessionHandle;

  protected $_sasl = true;

  public function __construct( $host, $port = 10000, $username = null, $password = null, $timeout = null ) {
    $this->_host = $host;
    $this->_port = $port;
    $this->_username = $username;
    $this->_password = $password;
    $this->_timeout = $timeout;
  }

  public function setSasl( $bool ) {
    $this->_sasl = (bool) $bool;
    return $this;
  }

  public function connect() {

    // Check if we have already connected and have a session
    if ( null !== $this->_sessionHandle ) {
      return $this;
    }

    try {
      $this->_transport = new \Thrift\Transport\TSocket( $this->_host, $this->_port );

      if ( null !== $this->_timeout ) {
        $this->_transport->setSendTimeout( $this->_timeout * 1000 );
        $this->_transport->setRecvTimeout( $this->_timeout * 1000 );
      }

      if ( $this->_sasl ) {
        $this->_transport = new \Thrift\Transport\TSaslClientTransport(
          $this->_transport,
          $this->_username,
          $this->_password
        );
      }

      $this->_transport->open();

      $this->_client = new \ThriftSQL\TCLIServiceClient(
        new \Thrift\Protocol\TBinaryProtocol(
          $this->_transport
        )
      );

      $TOpenSessionReq = new \ThriftSQL\TOpenSessionReq();
      if ( null !== $this->_username && null !== $this->_password ) {
        $TOpenSessionReq->username = $this->_username;
        $TOpenSessionReq->password = $this->_password;
      }

      // Ok, let's try to start a session
      $this->_sessionHandle = $this
        ->_client
        ->OpenSession( $TOpenSessionReq )
        ->sessionHandle;
    } catch( Exception $e ) {
      $this->_sessionHandle = null;
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }

    return $this;

  }

  public function queryAndFetchAll( $queryStr ) {
    try {
      $sleeper = new \ThriftSQL\Utils\Sleeper();
      $queryCleaner = new \ThriftSQL\Utils\QueryCleaner();

      $TExecuteStatementResp = $this->_client->ExecuteStatement( new \ThriftSQL\TExecuteStatementReq( array(
        'sessionHandle' => $this->_sessionHandle,
        'statement' => $queryCleaner->clean( $queryStr ),
        'runAsync' => true,
      ) ) );

      // Check for errors
      if ( \ThriftSQL\TStatusCode::ERROR_STATUS === $TExecuteStatementResp->status->statusCode ) {
        throw new \ThriftSQL\Exception( "HIVE QUERY ERROR: {$TExecuteStatementResp->status->errorMessage}" );
      }

      // Wait for results
      $sleeper->reset();
      do {

        $slept = $sleeper->sleep()->getSleptSecs();

        if ( $slept > 18000 ) { // 5 Hours
          // TODO: Actually kill the query then throw exception.
          throw new \ThriftSQL\Exception( 'Hive Query Killed!' );
        }

        $state = $this
          ->_client
          ->GetOperationStatus( new \ThriftSQL\TGetOperationStatusReq( array(
            'operationHandle' => $TExecuteStatementResp->operationHandle,
          ) ) )
          ->operationState;

        if ( $this->_isOperationFinished( $state ) ) {
          break;
        }

        if ( $this->_isOperationRunning( $state ) ) {
          continue;
        }

        // Query in error state
        throw new \ThriftSQL\Exception(
          'Query is in an error state: ' . \ThriftSQL\TOperationState::$__names[ $state ]
        );

      } while (true);

      // Collect results
      $resultTuples = array();
      do {

        $TFetchResultsResp = $this->_client->FetchResults( new \ThriftSQL\TFetchResultsReq( array(
          'operationHandle' => $TExecuteStatementResp->operationHandle,
          'maxRows' => 100,
        ) ) );

        /**
         * NOTE: $TFetchResultsResp->hasMoreRows appears broken, it's always
         * false so we want to keep fetching until we run out of data.
         */

        $responseTuples = array();
        foreach ( $TFetchResultsResp->results->columns as $col => $TColumn ) {
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
            $responseTuples[ $row ][ $col ] = $value;
          }
        }

        // No more data we're done
        if ( empty( $responseTuples ) ) {
          return $resultTuples;
        }

        $resultTuples = array_merge( $resultTuples, $responseTuples );

      } while (true);

    } catch( Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }
  }

  public function disconnect() {

    // Close session if we have one
    if ( null !== $this->_sessionHandle ) {
      $this->_client->CloseSession( new \ThriftSQL\TCloseSessionReq( array(
        'sessionHandle' => $this->_sessionHandle,
      ) ) );
    }
    $this->_sessionHandle = null;

    // Clear out the client
    $this->_client = null;

    // Close the socket
    if ( null !== $this->_transport ) {
      $this->_transport->close();
    }
    $this->_transport = null;

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
