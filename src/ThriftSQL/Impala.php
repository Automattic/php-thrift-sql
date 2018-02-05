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

  public function query( $queryStr ) {
    try {
      return new ImpalaQuery( $queryStr, $this->_client );
    } catch ( Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }
  }

  public function queryAndFetchAll( $queryStr ) {
    try {
      $query = $this->query( $queryStr );
      $query->wait();
      $result = array();
      do {
        $rows = $query->fetch( 100 );
        if ( empty( $rows ) ) {
          break;
        }
        $result = array_merge( $result, $rows );
      } while ( true );
      return $result;
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

  /**
   * Get's a memory efficient iterator that you can use in a foreach loop.
   * If there's an error with the query, it will simply stop iterating.
   *
   * @param string $queryStr
   *
   * @return ThriftStream
   * @throws Exception
   */
  public function getIterator( $queryStr ) {
    try {
      return new ThriftStream( $this, $queryStr );
    } catch ( \Exception $e ) {
      throw new \ThriftSQL\Exception( $e->getMessage() );
    }
  }
}
