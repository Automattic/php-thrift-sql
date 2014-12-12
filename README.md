PHP ThriftSQL
=============

The `ThriftSQL.phar` archive aims to provide access to SQL-on-Hadoop frameworks for PHP. It bundles Thrift and various service packages together and exposes a common interface for running queries over the various frameworks.

Currently the following engines are supported:

* *Hive* -- Over the HiveServer2 Thrift interface, SASL is not supported.
* *Impala* -- Over the Impala Service Thrift interface which extends the Beeswax protocol.

Usage Example
-------------

```php
// Load this lib
require_once __DIR__ . '/ThriftSQL.phar';

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
```

Notes
-----

Because there is not SASL support in the official PHP Thrift libs SASL auth will need to turned off for Hive / HiveServer2.

```xml
<property>
  <name>hive.server2.authentication</name>
  <value>NOSASL</value>
</property>
```
