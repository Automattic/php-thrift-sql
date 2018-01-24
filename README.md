PHP ThriftSQL
=============

The `ThriftSQL.phar` archive aims to provide access to SQL-on-Hadoop frameworks for PHP. It bundles Thrift and various service packages together and exposes a common interface for running queries over the various frameworks.

Currently the following engines are supported:

* *Hive* -- Over the HiveServer2 Thrift interface, SASL is enabled by default so username and password must be provided however this can be turned off with the `setSasl()` method before calling `connect()`.
* *Impala* -- Over the Impala Service Thrift interface which extends the Beeswax protocol.

Building Phar
-------------

```
$ php -c php.ini build.php
```

Usage Example
-------------

```php
// Load this lib
require_once __DIR__ . '/ThriftSQL.phar';

// Try out a Hive query
$hive = new \ThriftSQL\Hive( 'hive.host.local', 10000, 'user', 'pass' );
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

// Don't forget to clear the client and close socket.
$hive->disconnect();
$impala->disconnect();
```

Use the memory efficient iterator:

```php
// Load this lib
require_once __DIR__ . '/ThriftSQL.phar';

// Try out a Hive query
$hiveIterator = new \ThriftSQL\Hive( 'hive.host.local', 10000, 'user', 'pass' );
$hiveTables = $hive
  ->connect()
  ->getIterator('SHOW TABLES');
  
foreach($hiveIterator as $rowNum => $row) {
    print_r( $row );
}
```
