<?php

  // Load this lib
  require_once __DIR__ . '/ThriftSQL.phar';

  // Try out a Hive query
  $hive = new \ThriftSQL\Hive( 'hive.host.local' );
  $hiveTables = $hive
    ->setSasl( false ) // To turn SASL auth off, on by default
    ->connect()
    ->queryAndFetchAll( 'SHOW TABLES' );
  print_r( $hiveTables );

  // Try out an Impala query
  $impala = new \ThriftSQL\Impala( 'impala.host.local' );
  $impalaTables = $impala
    ->connect()
    ->queryAndFetchAll( 'SHOW TABLES' );
  print_r( $impalaTables );
  
  $impala->disconnect();  //Don't forget to clear the client and close socket.
