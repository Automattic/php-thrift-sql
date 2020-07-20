<?php
require __DIR__ . '/src/ThriftSQL.autoload.php';

$hive = new \ThriftSQL\Hive('127.0.0.1', 10000, 'root', '');

$hive->setTimeout(10)->enableResultSchema()->connect();
$result = $hive->query("select 1 as a, 'test' as B");

/** @var \ThriftSQL\ResultRowSchema $row */
foreach( $result as $row ) {
  var_dump( $row['a'] );
  var_dump( $row->b );
  print_r( $row->schemaArray() );
}

$hive->disconnect();
