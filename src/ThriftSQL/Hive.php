<?php

namespace ThriftSQL;

class HiveSessionInternal
{
  /** @var \ThriftGenerated\TCLIServiceClient */
  public $client;
  /** @var \ThriftGenerated\TSessionHandle */
  public $session;
}

class Hive extends \ThriftSQL
{
  private $host;
  private $port;
  private $username;
  private $password;
  private $timeout;
  private $useSasl = true;
  protected $resultMode = 0;


  /** @var \Thrift\Transport\TSocket|\Thrift\Transport\TSaslClientTransport */
  private $transport;

  /** @var HiveSessionInternal */
  private $sessionInternal;


  public function __construct($host, $port = 10000, $username = null, $password = null)
  {
    $this->host = $host;
    $this->port = $port;
    $this->username = $username;
    $this->password = $password;
    $this->sessionInternal = new HiveSessionInternal();
  }

  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
    return $this;
  }

  public function setUseSasl($bool)
  {
    $this->useSasl = (bool)$bool;
    return $this;
  }

  public function enableResultSchema()
  {
    $this->resultMode = Query::RESULT_SCHEMA;
    return $this;
  }

  public function connect()
  {
    // Check if we have already connected and have a session
    if ($this->sessionInternal->session) {
      return $this;
    }

    // Make sure we have auth info set
    if (empty($this->username)) {
      $this->username = self::USERNAME_DEFAULT;
    }

    if (empty($this->password)) {
      $this->password = 'ANY-PASSWORD';
    }

    try {
      $this->transport = new \Thrift\Transport\TSocket($this->host, $this->port);
      if (null !== $this->timeout) {
        $this->transport->setSendTimeout($this->timeout * 1000);
        $this->transport->setRecvTimeout($this->timeout * 1000);
      }
      if ($this->useSasl) {
        $this->transport = new \Thrift\Transport\TSaslClientTransport(
          $this->transport,
          $this->username,
          $this->password
        );
      }
      $this->transport->open();
      $this->sessionInternal->client = new \ThriftGenerated\TCLIServiceClient(
        new \Thrift\Protocol\TBinaryProtocol(
          $this->transport
        )
      );
      $TOpenSessionReq = new \ThriftGenerated\TOpenSessionReq();
      $TOpenSessionReq->username = $this->username;
      $TOpenSessionReq->password = $this->password;

      // Ok, let's try to start a session
      $this->sessionInternal->session = $this->sessionInternal->client->OpenSession($TOpenSessionReq)->sessionHandle;
    } catch (\Thrift\Exception\TException $e) {
      $this->sessionInternal->session = null;
      throw new \ThriftSQL\ThriftSQLException($e->getMessage(), $e->getCode(), $e);
    }
    return $this;
  }

  /**
   * @param string $statement
   * @return HiveQuery
   */
  public function query($statement)
  {
    $query = new HiveQuery($this->sessionInternal, $statement);
    $query->setResultMode($this->resultMode);
    $query->exec();
    return $query;
  }

  public function disconnect()
  {
    // Close session if we have one
    if ($this->sessionInternal->session) {
      $this->sessionInternal->client->CloseSession(new \ThriftGenerated\TCloseSessionReq(array(
        'sessionHandle' => $this->sessionInternal->session,
      )));
    }
    $this->sessionInternal->client = null;
    $this->sessionInternal->session = null;
    // Close the socket
    if (null !== $this->transport) {
      $this->transport->close();
    }
    $this->transport = null;
  }
}
