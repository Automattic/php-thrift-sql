<?php

  // Load this lib
  require __DIR__ . '/src/autoload.php';

  // Try out a Hive query
  $hive = new \ThriftSQL\Hive( 'hive.host.local' );
  $hiveTables = $hive
    ->connect()
    ->queryAndFetchAll( 'SHOW TABLES' );
  print_r( $hiveTables );

  // Try out an Impala query
  $impala = new \ThriftSQL\Impala( 'impala.host.local' );
  $impalaTables = $impala
    ->connect()
    ->queryAndFetchAll( 'SHOW TABLES' );
  print_r( $impalaTables );
