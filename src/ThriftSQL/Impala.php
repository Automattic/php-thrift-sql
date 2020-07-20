<?php

namespace ThriftSQL;

class ImpalaClientInternal
{
  /** @var string */
  public $username;

  /** @var array */
  public $options;

  /** @var \ThriftGenerated\ImpalaServiceIf */
  public $client;
}

class Impala extends \ThriftSQL
{

  private $host;
  private $port;
  private $username;
  private $password;
  private $timeout;

  /** @var \Thrift\Transport\TSocket */
  private $transport;

  /** @var ImpalaClientInternal */
  private $clientInternal;

  public function __construct($host, $port = 21000, $username = null, $password = null)
  {
    $this->host = $host;
    $this->port = $port;
    $this->username = $username;
    $this->password = $password; // not used -- we impersonate on the query level

    $this->clientInternal = new ImpalaClientInternal();
    $this->clientInternal->username = $username;
  }

  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
    return $this;
  }

  public function setOption($key, $value)
  {
    // Normalize key
    $key = strtoupper($key);

    if (null === $value) {
      // NULL means unset
      unset($this->clientInternal->options[$key]);
    } elseif (true === $value) {
      $this->clientInternal->options[$key] = 'true';
    } elseif (false === $value) {
      $this->clientInternal->options[$key] = 'false';
    } else {
      $this->clientInternal->options[$key] = (string)$value;
    }

    return $this;
  }

  public function connect()
  {
    // Check if we have already connected
    if (null !== $this->clientInternal->client) {
      return $this;
    }

    // Make sure we have a username set
    if (empty($this->username)) {
      $this->username = self::USERNAME_DEFAULT;
    }

    try {
      $this->transport = new \Thrift\Transport\TSocket($this->host, $this->port);

      if (null !== $this->timeout) {
        $this->transport->setSendTimeout($this->timeout * 1000);
        $this->transport->setRecvTimeout($this->timeout * 1000);
      }

      $this->transport->open();

      $this->clientInternal->client = new \ThriftGenerated\ImpalaServiceClient(
        new \Thrift\Protocol\TBinaryProtocol(
          $this->transport
        )
      );
    } catch (\Thrift\Exception\TException $e) {
      $this->clientInternal->client = null;
      throw new \ThriftSQL\ThriftSQLException($e->getMessage(), $e->getCode(), $e);
    }

    return $this;
  }

  public function query($statement)
  {
    try {
      $options = array();
      foreach ($this->clientInternal->options as $key => $value) {
        $options[] = "{$key}={$value}";
      }
      $query = new ImpalaQuery($this->clientInternal, $statement);
      $query->exec();
      return $query;
    } catch (ThriftSQLException $e) {
      throw new \ThriftSQL\ThriftSQLException($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function disconnect()
  {
    // Clear out the client
    $this->clientInternal->client = null;

    // Close the socket
    if (null !== $this->transport) {
      $this->transport->close();
    }
    $this->transport = null;
  }
}
