<?php
namespace ThriftSQL;

class Impala implements \ThriftSQL {

  private $_host;
  private $_port;
  private $_username;
  private $_password;
  private $_timeout;
  private $_transport;
  private $_client;

  public function __construct( $host, $port = 21000, $username = null, $password = null, $timeout = null ) {
    $this->_host = $host;
    $this->_port = $port;
    $this->_username = $username; // not used
    $this->_password = $password; // not used
    $this->_timeout = $timeout;
  }

  public function connect() {

    // Check if we have already connected
    if ( null !== $this->_client ) {
      return $this;
    }

    try {
      $this->_transport = new \Thrift\Transport\TSocket( $this->_host, $this->_port );

      if ( null !== $this->_timeout ) {
        $this->_transport->setSendTimeout( $this->_timeout * 1000 );
        $this->_transport->setRecvTimeout( $this->_timeout * 1000 );
      }

      $this->_transport->open();

      $this->_client = new \ThriftSQL\ImpalaServiceClient(
        new \Thrift\Protocol\TBinaryProtocol(
          $this->_transport
        )
      );
    } catch( Exception $e ) {
      $this->_client = null;
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }

    return $this;

  }

  public function queryAndFetchAll( $queryStr ) {
    try {
      $QueryHandle = $this->_client->query( new \ThriftSQL\Query( array(
        'query' => $queryStr,
      ) ) );

      // Wait for results
      $iteration = 0;
      do {

        usleep( $this->_getSleepUsec( $iteration ) );

        if ( $iteration > 250 ) {
          // TODO: Actually kill the query then throw exception.
          throw new \ThriftSQL\Exception( 'Impala Query Killed!' );
        }

        $state = $this
          ->_client
          ->get_state( $QueryHandle );

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

      // Collect results
      $resultTuples = array();
      do {

        $Results = $this->_client->fetch( $QueryHandle, false, -1 );

        if ( !$Results->ready ) {
          // Not sure if this is possible but sleep for 1ms if it does
          usleep( 1000 );
          continue;
        }

        $responseTuples = array();
        foreach ( $Results->data as $row => $rawValues ) {
          $values = explode( "\t", $rawValues, count( $Results->columns ) );

          foreach ( $Results->columns as $col => $dataType ) {
            switch ($dataType) {
              case 'int':
              case 'bigint':
              case 'smallint':
              case 'tinyint':
                $responseTuples[ $row ][ $col ] = intval( $value );
                break;

              case 'decimal':
              case 'double':
              case 'float':
              case 'real':
                $responseTuples[ $row ][ $col ] = floatval( $value );
                break;

              case 'boolean':
                $responseTuples[ $row ][ $col ] = ( 'true' === $value );
                break;

              case 'char':
              case 'string':
              case 'varchar':
              case 'timestamp':
              default:
                $responseTuples[ $row ][ $col ] = $value;
                break;
            }
          }
        }

        $resultTuples = array_merge( $resultTuples, $responseTuples );

      } while ( $Results->has_more );

      return $resultTuples;

    } catch( Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }
  }

  public function disconnect() {

    // Clear out the client
    $this->_client = null;

    // Close the socket
    if ( null !== $this->_transport ) {
      $this->_transport->close();
    }
    $this->_transport = null;

  }

  private function _getSleepUsec( $iteration ) {
    // Max out at 30 second sleep per check
    if ( 14 < $iteration ) {
      return 30000000;
    }

    return pow( 2, $iteration ) * 1000;
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
