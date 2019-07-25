PHP ThriftSQL
=============

The `ThriftSQL.phar` archive aims to provide access to SQL-on-Hadoop frameworks for PHP. It bundles Thrift and various service packages together and exposes a common interface for running queries over the various frameworks.

Currently the following engines are supported:

* *Hive* -- Over the HiveServer2 Thrift interface, SASL is enabled by default so username and password must be provided however this can be turned off with the `setSasl()` method before calling `connect()`.
* *Impala* -- Over the Impala Service Thrift interface which extends the Beeswax protocol.

Version Compatibility
---------------------

This library is currently compiled against the Thrift definitions of the following database versions:

- Apache Hive `1.1.0` ([Mar 2015](https://github.com/apache/hive/tree/release-1.1.0))
- Apache Impala `2.12.0` ([Apr 2018](https://github.com/apache/impala/tree/2.12.0))

Using the compiler and base PHP classes of:

- Apache Thrift `0.12.0` ([Oct 2018](https://github.com/apache/thrift/tree/v0.12.0))

Usage Example
-------------

The recommended way to use this library is to get results from Hive/Impala via the memory efficient iterator which will keep the connection open and scroll through the results a couple rows at a time. This allows the processing of large result datasets one record at a time minimizing PHP's memory consumption.

```php
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
```

The downside to using the memory efficient iterator is that we can only iterate over the result set once. If a second `foreach` is called on the same iterator object an exception is thrown by default to prevent the same query from executing on Hive/Impala again as results are not cached within the PHP client. This can be turned off however be aware iterating over the same iterator object may produce different results as the query is rerun.

Consider the following example:

```php
// Connect to hive and get a rerun-able iterator
$hive = new \ThriftSQL\Hive( 'hive.host.local', 10000, 'user', 'pass' );
$results = $hive
  ->connect()
  ->getIterator( 'SELECT UNIX_TIMESTAMP()' )
  ->allowRerun( true );

// Execute the Hive query and get results
foreach( $results as $rowNum => $row ) {
  echo "Hive server time is: {$v[0]}\n";
}

sleep(3);

// Execute the Hive query a second time
foreach( $results as $rowNum => $row ) {
  echo "Hive server time is: {$v[0]}\n";
}
```

Which will output something like:

```
Hive server time is: 1517875200
Hive server time is: 1517875203
```

If the result set is small and it would be easier to load all of it into PHP memory the `queryAndFetchAll()` method can be used which will return a plain numeric multidimensional array of the full result set.

```php
// Try out a small Hive query
$hive = new \ThriftSQL\Hive( 'hive.host.local', 10000, 'user', 'pass' );
$hiveTables = $hive
  ->connect()
  ->queryAndFetchAll( 'SHOW TABLES' );
$hive->disconnect();

// Print out the cached results
print_r( $hiveTables );
```

```php
// Try out a small Impala query
$impala = new \ThriftSQL\Impala( 'impala.host.local' );
$impalaTables = $impala
  ->connect()
  ->queryAndFetchAll( 'SHOW TABLES' );
$impala->disconnect();

// Print out the cached results
print_r( $impalaTables );
```

Developing & Contributing
-------------------------

In order to rebuild this library you will need [Composer](https://getcomposer.org/) to install dev dependencies and [Apache Thrift](https://thrift.apache.org/) to compile client libraries from the Thrift interface definition files.

Once dev tools are installed the phar can be rebuilt using `make`:

```
$ make clean && make phar
```
