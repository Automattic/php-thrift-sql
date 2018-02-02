<?php

// Generated by './vendor/bin/php-generate-autoload' 'src/autoload.php'

\spl_autoload_register(function ($class) {
  static $map = array (
  'ThriftSQL' => 'ThriftSQL.php',
  'ThriftSQLQuery' => 'ThriftSQLQuery.php',
  'ThriftSQL\\BeeswaxException' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\BeeswaxServiceClient' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxServiceIf' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_clean_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_clean_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_close_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_close_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_dump_config_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_dump_config_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_echo_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_echo_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_executeAndWait_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_executeAndWait_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_explain_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_explain_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_fetch_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_fetch_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_default_configuration_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_default_configuration_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_log_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_log_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_results_metadata_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_results_metadata_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_state_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_get_state_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_query_args' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\BeeswaxService_query_result' => 'Packages/Beeswax/BeeswaxService.php',
  'ThriftSQL\\ConfigVariable' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\Constant' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\Exception' => 'ThriftSQL/Exception.php',
  'ThriftSQL\\Hive' => 'ThriftSQL/Hive.php',
  'ThriftSQL\\HiveQuery' => 'ThriftSQL/HiveQuery.php',
  'ThriftSQL\\Impala' => 'ThriftSQL/Impala.php',
  'ThriftSQL\\ImpalaHiveServer2ServiceClient' => 'Packages/ImpalaService/ImpalaHiveServer2Service.php',
  'ThriftSQL\\ImpalaHiveServer2ServiceIf' => 'Packages/ImpalaService/ImpalaHiveServer2Service.php',
  'ThriftSQL\\ImpalaHiveServer2Service_GetExecSummary_args' => 'Packages/ImpalaService/ImpalaHiveServer2Service.php',
  'ThriftSQL\\ImpalaHiveServer2Service_GetExecSummary_result' => 'Packages/ImpalaService/ImpalaHiveServer2Service.php',
  'ThriftSQL\\ImpalaHiveServer2Service_GetRuntimeProfile_args' => 'Packages/ImpalaService/ImpalaHiveServer2Service.php',
  'ThriftSQL\\ImpalaHiveServer2Service_GetRuntimeProfile_result' => 'Packages/ImpalaService/ImpalaHiveServer2Service.php',
  'ThriftSQL\\ImpalaQuery' => 'ThriftSQL/ImpalaQuery.php',
  'ThriftSQL\\ImpalaServiceClient' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaServiceIf' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_Cancel_args' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_Cancel_result' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_CloseInsert_args' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_CloseInsert_result' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_GetExecSummary_args' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_GetExecSummary_result' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_GetRuntimeProfile_args' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_GetRuntimeProfile_result' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_PingImpalaService_args' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_PingImpalaService_result' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_ResetCatalog_args' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_ResetCatalog_result' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_ResetTable_args' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\ImpalaService_ResetTable_result' => 'Packages/ImpalaService/ImpalaService.php',
  'ThriftSQL\\Query' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\QueryExplanation' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\QueryHandle' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\QueryNotFoundException' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\QueryState' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\Results' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\ResultsMetadata' => 'Packages/Beeswax/Types.php',
  'ThriftSQL\\TArrayTypeEntry' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TBinaryColumn' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TBoolColumn' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TBoolValue' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TByteColumn' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TByteValue' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCLIServiceClient' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIServiceIf' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CancelDelegationToken_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CancelDelegationToken_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CancelOperation_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CancelOperation_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CloseOperation_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CloseOperation_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CloseSession_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_CloseSession_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_ExecuteStatement_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_ExecuteStatement_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_FetchResults_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_FetchResults_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetCatalogs_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetCatalogs_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetColumns_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetColumns_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetDelegationToken_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetDelegationToken_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetFunctions_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetFunctions_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetInfo_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetInfo_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetOperationStatus_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetOperationStatus_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetResultSetMetadata_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetResultSetMetadata_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetSchemas_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetSchemas_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetTableTypes_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetTableTypes_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetTables_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetTables_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetTypeInfo_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_GetTypeInfo_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_OpenSession_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_OpenSession_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_RenewDelegationToken_args' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCLIService_RenewDelegationToken_result' => 'Packages/TCLIService/TCLIService.php',
  'ThriftSQL\\TCancelDelegationTokenReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCancelDelegationTokenResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCancelOperationReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCancelOperationResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCloseOperationReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCloseOperationResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCloseSessionReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TCloseSessionResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TColumn' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TColumnDesc' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TColumnValue' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TDoubleColumn' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TDoubleValue' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TExecuteStatementReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TExecuteStatementResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TFetchOrientation' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TFetchResultsReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TFetchResultsResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetCatalogsReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetCatalogsResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetColumnsReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetColumnsResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetDelegationTokenReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetDelegationTokenResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetExecSummaryReq' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TGetExecSummaryResp' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TGetFunctionsReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetFunctionsResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetInfoReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetInfoResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetInfoType' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetInfoValue' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetOperationStatusReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetOperationStatusResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetResultSetMetadataReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetResultSetMetadataResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetRuntimeProfileReq' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TGetRuntimeProfileResp' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TGetSchemasReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetSchemasResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetTableTypesReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetTableTypesResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetTablesReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetTablesResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetTypeInfoReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TGetTypeInfoResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\THandleIdentifier' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TI16Column' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TI16Value' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TI32Column' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TI32Value' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TI64Column' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TI64Value' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TImpalaQueryOptions' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TInsertResult' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TMapTypeEntry' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TOpenSessionReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TOpenSessionResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TOperationHandle' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TOperationState' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TOperationType' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TPingImpalaServiceResp' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TPrimitiveTypeEntry' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TProtocolVersion' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TRenewDelegationTokenReq' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TRenewDelegationTokenResp' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TResetTableReq' => 'Packages/ImpalaService/Types.php',
  'ThriftSQL\\TRow' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TRowSet' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TSessionHandle' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TStatus' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TStatusCode' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TStringColumn' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TStringValue' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TStructTypeEntry' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TTableSchema' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TTypeDesc' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TTypeEntry' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TTypeId' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TTypeQualifierValue' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TTypeQualifiers' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TUnionTypeEntry' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\TUserDefinedTypeEntry' => 'Packages/TCLIService/Types.php',
  'ThriftSQL\\Utils\\QueryCleaner' => 'ThriftSQL/Utils/QueryCleaner.php',
  'ThriftSQL\\Utils\\Sleeper' => 'ThriftSQL/Utils/Sleeper.php',
  'Thrift\\Base\\TBase' => 'Thrift/Base/TBase.php',
  'Thrift\\ClassLoader\\ThriftClassLoader' => 'Thrift/ClassLoader/ThriftClassLoader.php',
  'Thrift\\Exception\\TApplicationException' => 'Thrift/Exception/TApplicationException.php',
  'Thrift\\Exception\\TException' => 'Thrift/Exception/TException.php',
  'Thrift\\Exception\\TProtocolException' => 'Thrift/Exception/TProtocolException.php',
  'Thrift\\Exception\\TTransportException' => 'Thrift/Exception/TTransportException.php',
  'Thrift\\Factory\\TBinaryProtocolFactory' => 'Thrift/Factory/TBinaryProtocolFactory.php',
  'Thrift\\Factory\\TCompactProtocolFactory' => 'Thrift/Factory/TCompactProtocolFactory.php',
  'Thrift\\Factory\\TJSONProtocolFactory' => 'Thrift/Factory/TJSONProtocolFactory.php',
  'Thrift\\Factory\\TProtocolFactory' => 'Thrift/Factory/TProtocolFactory.php',
  'Thrift\\Factory\\TStringFuncFactory' => 'Thrift/Factory/TStringFuncFactory.php',
  'Thrift\\Factory\\TTransportFactory' => 'Thrift/Factory/TTransportFactory.php',
  'Thrift\\Protocol\\JSON\\BaseContext' => 'Thrift/Protocol/JSON/BaseContext.php',
  'Thrift\\Protocol\\JSON\\ListContext' => 'Thrift/Protocol/JSON/ListContext.php',
  'Thrift\\Protocol\\JSON\\LookaheadReader' => 'Thrift/Protocol/JSON/LookaheadReader.php',
  'Thrift\\Protocol\\JSON\\PairContext' => 'Thrift/Protocol/JSON/PairContext.php',
  'Thrift\\Protocol\\TBinaryProtocol' => 'Thrift/Protocol/TBinaryProtocol.php',
  'Thrift\\Protocol\\TBinaryProtocolAccelerated' => 'Thrift/Protocol/TBinaryProtocolAccelerated.php',
  'Thrift\\Protocol\\TCompactProtocol' => 'Thrift/Protocol/TCompactProtocol.php',
  'Thrift\\Protocol\\TJSONProtocol' => 'Thrift/Protocol/TJSONProtocol.php',
  'Thrift\\Protocol\\TMultiplexedProtocol' => 'Thrift/Protocol/TMultiplexedProtocol.php',
  'Thrift\\Protocol\\TProtocol' => 'Thrift/Protocol/TProtocol.php',
  'Thrift\\Protocol\\TProtocolDecorator' => 'Thrift/Protocol/TProtocolDecorator.php',
  'Thrift\\Serializer\\TBinarySerializer' => 'Thrift/Serializer/TBinarySerializer.php',
  'Thrift\\Server\\TForkingServer' => 'Thrift/Server/TForkingServer.php',
  'Thrift\\Server\\TServer' => 'Thrift/Server/TServer.php',
  'Thrift\\Server\\TServerSocket' => 'Thrift/Server/TServerSocket.php',
  'Thrift\\Server\\TServerTransport' => 'Thrift/Server/TServerTransport.php',
  'Thrift\\Server\\TSimpleServer' => 'Thrift/Server/TSimpleServer.php',
  'Thrift\\StoredMessageProtocol' => 'Thrift/TMultiplexedProcessor.php',
  'Thrift\\StringFunc\\Core' => 'Thrift/StringFunc/Core.php',
  'Thrift\\StringFunc\\Mbstring' => 'Thrift/StringFunc/Mbstring.php',
  'Thrift\\StringFunc\\TStringFunc' => 'Thrift/StringFunc/TStringFunc.php',
  'Thrift\\TMultiplexedProcessor' => 'Thrift/TMultiplexedProcessor.php',
  'Thrift\\Transport\\TBufferedTransport' => 'Thrift/Transport/TBufferedTransport.php',
  'Thrift\\Transport\\TCurlClient' => 'Thrift/Transport/TCurlClient.php',
  'Thrift\\Transport\\TFramedTransport' => 'Thrift/Transport/TFramedTransport.php',
  'Thrift\\Transport\\THttpClient' => 'Thrift/Transport/THttpClient.php',
  'Thrift\\Transport\\TMemoryBuffer' => 'Thrift/Transport/TMemoryBuffer.php',
  'Thrift\\Transport\\TNullTransport' => 'Thrift/Transport/TNullTransport.php',
  'Thrift\\Transport\\TPhpStream' => 'Thrift/Transport/TPhpStream.php',
  'Thrift\\Transport\\TSaslClientTransport' => 'ThriftExtras/Transport/TSaslClientTransport.php',
  'Thrift\\Transport\\TSocket' => 'Thrift/Transport/TSocket.php',
  'Thrift\\Transport\\TSocketPool' => 'Thrift/Transport/TSocketPool.php',
  'Thrift\\Transport\\TTransport' => 'Thrift/Transport/TTransport.php',
  'Thrift\\Type\\TConstant' => 'Thrift/Type/TConstant.php',
  'Thrift\\Type\\TMessageType' => 'Thrift/Type/TMessageType.php',
  'Thrift\\Type\\TType' => 'Thrift/Type/TType.php',
);

  if (isset($map[$class])) {
    require_once __DIR__ . '/' . $map[$class];
  }
}, true, false);

require_once __DIR__ . '/Thrift/Transport/TSocketPool.php';

