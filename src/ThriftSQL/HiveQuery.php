<?php

namespace ThriftSQL;

class HiveQuery extends \ThriftSQL\Query
{

  /** @var HiveSessionInternal */
  private $sessionInternal;

  /** @var string */
  private $statement;

  /** @var bool */
  private $ready;

  /** @var \ThriftGenerated\TExecuteStatementResp */
  private $resp;

  /** @var array */
  private $schema;

  public function __construct($sessionInternal, $statement)
  {
    $this->sessionInternal = $sessionInternal;
    $this->statement = $statement;
  }

  public function exec()
  {
    $this->ready = false;
    $this->resp = $this->sessionInternal->client->ExecuteStatement(new \ThriftGenerated\TExecuteStatementReq(array(
      'sessionHandle' => $this->sessionInternal->session,
      'statement' => $this->statement,
      'runAsync' => true,
    )));

    if (\ThriftGenerated\TStatusCode::ERROR_STATUS === $this->resp->status->statusCode) {
      throw new \ThriftSQL\ThriftSQLException($this->resp->status->errorMessage);
    }

    return $this;
  }

  public function wait()
  {
    if ($this->ready) {
      return $this;
    }

    // Wait for results
    $sleeper = new \ThriftSQL\Utils\Sleeper();
    $sleeper->reset();
    do {
      if ($sleeper->sleep()->getSleptSecs() > 18000) { // 5 Hours
        try {
          // Fire and forget cancel operation, ignore the returned:
          // \ThriftGenerated\TCancelOperationResp
          $this->sessionInternal->client->CancelOperation(new \ThriftGenerated\TCancelOperationReq(array(
            'operationHandle' => $this->resp->operationHandle,
          )));
        } finally {
          throw new \ThriftSQL\ThriftSQLException('Hive Query Killed!');
        }
      }

      $TGetOperationStatusResp = $this->sessionInternal->client
        ->GetOperationStatus(new \ThriftGenerated\TGetOperationStatusReq(array(
          'operationHandle' => $this->resp->operationHandle,
        )));
      if ($this->checkOperationFinished($TGetOperationStatusResp->operationState)) {
        break;
      }
      if ($this->checkOperationRunning($TGetOperationStatusResp->operationState)) {
        continue;
      }
      // Query in error state
      throw new \ThriftSQL\ThriftSQLException(
        'Hive ' . \ThriftGenerated\TOperationState::$__names[$TGetOperationStatusResp->operationState] . ". Error Message: {$TGetOperationStatusResp->errorMessage}"
      );
    } while (true);

    // Check for errors
    if (\ThriftGenerated\TStatusCode::ERROR_STATUS === $this->resp->status->statusCode) {
      throw new \ThriftSQL\ThriftSQLException("HIVE QUERY ERROR: {$this->resp->status->status->errorMessage}");
    }

    $this->ready = true;
    return $this;
  }

  public function schema()
  {
    if (!$this->ready) {
      $this->wait();
    }

    if (!$this->schema) {
      $TGetResultSetMetadataResp = $this->sessionInternal->client->GetResultSetMetadata(new \ThriftGenerated\TGetResultSetMetadataReq(array(
        'operationHandle' => $this->resp->operationHandle,
      )));

      $i = 0;
      foreach ($TGetResultSetMetadataResp->schema->columns as $column) {
        // $column->typeDesc->types are very complex, not useful for daily query
        $this->schema[$column->columnName] = $i++;
      }
    }
    return $this->schema;
  }

  public function fetch($maxRows)
  {
    if (!$this->ready) {
      $this->wait();
    }
    try {
      $TFetchResultsResp = $this->sessionInternal->client->FetchResults(new \ThriftGenerated\TFetchResultsReq(array(
        'operationHandle' => $this->resp->operationHandle,
        'maxRows' => $maxRows,
      )));
      /**
       * NOTE: $TFetchResultsResp->hasMoreRows appears broken, it's always
       * false so one needs to keep fetching until they run out of data.
       */
      if ($TFetchResultsResp->results instanceof \ThriftGenerated\TRowSet && !empty($TFetchResultsResp->results->columns)) {
        return $this->colsToRows($TFetchResultsResp->results->columns);
      }
      return array();
    } catch (ThriftSQLException $e) {
      throw new \ThriftSQL\ThriftSQLException($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function close()
  {
    // Fire close operation and ignore return
    $this->sessionInternal->client->CloseOperation(new \ThriftGenerated\TCloseOperationReq(array(
      'operationHandle' => $this->resp->operationHandle,
    )));
  }

  private function colsToRows($columns)
  {
    $result = array();
    foreach ($columns as $col => $TColumn) {
      $values = array();
      if (!is_null($TColumn->boolVal)) {
        $values = $TColumn->boolVal->values;
      } else if (!is_null($TColumn->byteVal)) {
        $values = $TColumn->byteVal->values;
      } else if (!is_null($TColumn->i16Val)) {
        $values = $TColumn->i16Val->values;
      } else if (!is_null($TColumn->i32Val)) {
        $values = $TColumn->i32Val->values;
      } else if (!is_null($TColumn->i64Val)) {
        $values = $TColumn->i64Val->values;
      } else if (!is_null($TColumn->doubleVal)) {
        $values = $TColumn->doubleVal->values;
      } else if (!is_null($TColumn->stringVal)) {
        $values = $TColumn->stringVal->values;
      } else if (!is_null($TColumn->binaryVal)) {
        $values = $TColumn->binaryVal->values;
      } else {
        throw new \RuntimeException("FIXME unexpected condition. null values?");
      }
      foreach ($values as $row => $value) {
        $result[$row][$col] = $value;
      }
    }
    if ($this->resultMode == Query::RESULT_SCHEMA) {
      foreach ($result as $k=>$v) {
        $result[$k] = new ResultRowSchema($this->schema(), $v);
      }
    }
    return $result;
  }

  private function checkOperationFinished($state)
  {
    return $state == \ThriftGenerated\TOperationState::FINISHED_STATE;
  }

  private function checkOperationRunning($state)
  {
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
