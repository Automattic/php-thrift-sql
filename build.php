<?php

  // Check we have dev tools
  if ( !file_exists( __DIR__ . '/vendor/bin/php-generate-autoload' ) ) {
    echo "Please install dev tools with:\n\n$ composer install\n";
    exit(1);
  }

  // Update autoload file
  echo "Updating 'src/autoload.php'\n";
  echo "\t" . preg_replace( '/\n/', "\n\t", shell_exec(
    './vendor/bin/php-generate-autoload src/autoload.php'
  ) ) . "\n";

  // Create Stub
  echo "Updating 'ThriftSQL.phar'... ";
  $stub = <<<EOF
<?php
  include 'phar://' . __FILE__ . '/autoload.php';
  __HALT_COMPILER();
EOF;

  // Create Phar
  $phar = new Phar( 'ThriftSQL.phar', null, 'ThriftSQL.phar' );
  $phar->buildFromDirectory( __DIR__ . '/src' );
  $phar->setStub( $stub );

  echo "Built!\n";
