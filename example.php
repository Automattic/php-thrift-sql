<?php

  // Load this lib
  require_once __DIR__ . '/ThriftSQL.phar';

  // Try out a Hive query via iterator object
  $hive = new \ThriftSQL\Hive( 'hive.host.local', 10000, 'user', 'pass' );
  $hiveTables = $hive
    ->connect()
    ->getIterator( 'SHOW TABLES' );

  // Try out an Impala query via iterator object
  $impala = new \ThriftSQL\Impala( 'impala.host.local' );
  $impalaTables = $impala
    ->connect()
    ->getIterator( 'SHOW TABLES' );

  // Execute the Hive query and iterate over the result set
  foreach( $hiveTables as $rowNum => $row ) {
    print_r( $row );
  }

  // Execute the Impala query and iterate over the result set
  foreach( $impalaTables as $rowNum => $row ) {
    print_r( $row );
  }

  // Don't forget to close socket connection once you're done with it
  $hive->disconnect();
  $impala->disconnect();
