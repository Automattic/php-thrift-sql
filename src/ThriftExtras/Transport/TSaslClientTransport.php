<?php
/*
 * @package thrift.transport
 */

namespace Thrift\Transport;

use Thrift\Transport\TTransport;
use Thrift\Exception\TTransportException;
use Thrift\Factory\TStringFuncFactory;

/**
 * TTransport that wraps another TTransport and provides SASL.
 *
 * @package thrift.transport
 */
class TSaslClientTransport extends TTransport {

  const START    = 0x01;
  const OK       = 0x02;
  const BAD      = 0x03;
  const ERROR    = 0x04;
  const COMPLETE = 0x05;
  const METHOD   = 'PLAIN';

  /**
   * The TTransport to wrap
   *
   * @var TTransport
   */
  private $transport_;

  /**
   * SASL auth username
   *
   * @var string
   */
  protected $username_ = '';

  /**
   * SASL auth password
   *
   * @var string
   */
  protected $password_ = '';

  /**
   * The SASL handshake is complete
   *
   * @var boolean
   */
  private $saslComplete_ = false;

  /**
   * Buffer for reads
   *
   * @var bytes
   */
  private $readBuffer_ = '';

  /**
   * Buffer for writes
   *
   * @var bytes
   */
  private $writeBuffer_ = '';

  /**
   * Constructor
   *
   * @param TTransport $transport Transport object to be wrapped
   * @param string     $username  SASL username
   * @param string     $password  SASL password
   */
  public function __construct( TTransport $transport, $username, $password ) {
    $this->transport_ = $transport;
    $this->username_ = $username;
    $this->password_ = $password;
  }

  /**
   * Whether this transport is open.
   *
   * @return boolean true if open
   */
  public function isOpen() {
    return $this->saslComplete_;
  }

  /**
   * Open the transport for reading/writing
   *
   * @throws TTransportException if cannot open
   */
  public function open() {
    if ( !$this->transport_->isOpen() ) {
      $this->transport_->open();
    }

    try{
      $auth =  '' . chr(0) . $this->username_ . chr(0) . $this->password_;
      $this->saslWrite_( self::METHOD, self::START );
      $this->saslWrite_( $auth, self::COMPLETE );

      $saslFrame = $this->saslRead_( true );
      $this->saslComplete_ = ( self::COMPLETE == $saslFrame['status'] );

      if ( !$this->saslComplete_ ) {
        throw new TTransportException( 'Could not perform SASL auth.' );
      }
    } catch ( TTransportException $e ) {
      throw $e;
    } catch ( Exception $e ) {
      throw new TTransportException( 'SASL Auth failed: ',  $e->getMessage() );
    }

    return true;
  }

  /**
   * Close the transport.
   */
  public function close() {
    return $this->transport_->close();
  }

  /**
   * Read some data into the array.
   *
   * @param int $len How much to read
   * @return string The data that has been read
   * @throws TTransportException if cannot read any more data
   */
  public function read($len) {
    if ( 0 === TStringFuncFactory::create()->strlen( $this->readBuffer_ ) ) {
      // No more buffered data go fetch a SASL frame
      $saslFrame = $this->saslRead_();
      $this->readBuffer_ = $saslFrame['payload'];
    }

    return $this->getFromBuffer_( $len );
  }

  /**
   * Writes the given data out.
   *
   * @param string $buf  The data to write
   * @throws TTransportException if writing fails
   */
  public function write($buf) {
    $this->writeBuffer_ .= $buf;
  }

  /**
  * Flushes any pending data out of a buffer
  *
  * @throws TTransportException if a writing error occurs
  */
  public function flush() {
    $this->saslWrite_( $this->writeBuffer_ );
    $this->writeBuffer_ = '';
  }

  public function __call($name, $arguments) {
    return call_user_func_array( array( $this->transport_, $name ), $arguments );
  }

  private function saslWrite_( $payload, $status = null ) {
    $header = '';
    if ( null !== $status ) {
      $header .= pack( 'C', $status );
    }
    $header .= pack( 'N', TStringFuncFactory::create()->strlen( $payload ) );
    $this->transport_->write( $header . $payload );
    $this->transport_->flush();
  }

  private function saslRead_($statusByte = false) {
    // Read SASL Header
    if ( $statusByte ) {
      $frame = unpack( 'Cstatus/Nlength', $this->transport_->readAll( 5 ) );
    } else {
      $frame = unpack( 'Nlength', $this->transport_->readAll( 4 ) );
    }

    // Read SASL Payload
    $frame['payload'] = $this->transport_->readAll( $frame['length'] );

    return $frame;
  }

  private function getFromBuffer_($len) {
    if ( TStringFuncFactory::create()->strlen( $this->readBuffer_ ) <= $len ) {
      $return = $this->readBuffer_;
      $this->readBuffer_ = '';
    } else {
      $return = TStringFuncFactory::create()->substr( $this->readBuffer_, 0, $len );
      $this->readBuffer_ = TStringFuncFactory::create()->substr( $this->readBuffer_, $len );
    }

    return $return;
  }
}
