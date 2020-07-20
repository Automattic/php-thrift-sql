<?php

namespace ThriftSQL;

class ImpalaQuery extends \ThriftSQL\Query
{
  private $ready;

  /** @var \ThriftGenerated\QueryHandle */
  private $queryHandle;

  /** @var ImpalaClientInternal */
  private $clientInternal;
  private $statement;

  public function __construct($clientInternal, $statement)
  {
    $this->clientInternal = $clientInternal;
    $this->statement = $statement;
  }

  public function exec()
  {
    $this->ready = false;
    $this->queryHandle = $this->clientInternal->client->query(new \ThriftGenerated\Query(array(
      'query' => $this->statement,
      'hadoop_user' => $this->clientInternal->username,
      'configuration' => $this->clientInternal->options,
    )));

    return $this;
  }

  public function wait()
  {
    if ($this->ready) {
      return $this;
    }

    // Wait for query to be ready
    $sleeper = new \ThriftSQL\Utils\Sleeper();
    $sleeper->reset();
    do {
      if ($sleeper->sleep()->getSleptSecs() > 18000) { // 5 Hours
        try {
          // Fire and forget cancel operation, ignore the returned:
          // \ThriftGenerated\TStatus
          $this->clientInternal->client->Cancel($this->queryHandle);
        } finally {
          throw new \ThriftSQL\ThriftSQLException('Impala Query Killed!');
        }
      }

      $state = $this->clientInternal->client->get_state($this->queryHandle);

      if ($this->checkOperationFinished($state)) {
        break;
      }

      if ($this->checkOperationRunning($state)) {
        continue;
      }

      // Query in error state
      throw new \ThriftSQL\ThriftSQLException(
        'Query is in an error state: ' . \ThriftGenerated\QueryState::$__names[$state]
      );

    } while (true);

    $this->ready = true;
    return $this;
  }

  public function schema()
  {
    throw new \RuntimeException("not implemented");
  }

  public function fetch($maxRows)
  {
    if (!$this->ready) {
      $this->wait();
    }
    try {
      $sleeper = new \ThriftSQL\Utils\Sleeper();
      $sleeper->reset();

      do {
        $response = $this->clientInternal->client->fetch($this->queryHandle, false, $maxRows);
        if ($response->ready) {
          break;
        }

        if ($sleeper->sleep()->getSleptSecs() > 60) { // 1 Minute
          throw new \ThriftSQL\ThriftSQLException('Impala Query took too long to fetch!');
        }

      } while (true);

      return $this->parseResponse($response);
    } catch (ThriftSQLException $e) {
      throw new \ThriftSQL\ThriftSQLException($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function close()
  {
    // Fire close operation and ignore exceptions
    $this->clientInternal->client->close($this->queryHandle);
  }

  private function parseResponse($response)
  {
    $result = array();
    foreach ($response->data as $row => $rawValues) {
      $values = explode("\t", $rawValues, count($response->columns));

      foreach ($response->columns as $col => $dataType) {
        switch ($dataType) {
          case 'int':
          case 'bigint':
          case 'smallint':
          case 'tinyint':
            $result[$row][$col] = intval($values[$col]);
            break;

          case 'decimal':
          case 'double':
          case 'float':
          case 'real':
            $result[$row][$col] = floatval($values[$col]);
            break;

          case 'boolean':
            $result[$row][$col] = ('true' === $values[$col]);
            break;

          case 'char':
          case 'string':
          case 'varchar':
          case 'timestamp':
          default:
            $result[$row][$col] = $values[$col];
            break;
        }
      }
    }
    return $result;
  }

  private function checkOperationFinished($state)
  {
    return (\ThriftGenerated\QueryState::FINISHED == $state);
  }

  private function checkOperationRunning($state)
  {
    return in_array(
      $state,
      array(
        \ThriftGenerated\QueryState::CREATED,     // 0
        \ThriftGenerated\QueryState::INITIALIZED, // 1
        \ThriftGenerated\QueryState::COMPILED,    // 2
        \ThriftGenerated\QueryState::RUNNING,     // 3
      )
    );
  }
}
