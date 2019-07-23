<?php

  // Check we have dev tools
  if ( !file_exists( __DIR__ . '/vendor/bin/php-generate-autoload' ) ) {
    echo "Please install dev tools with:\n";
    echo "$ composer install\n";
    exit(1);
  }

  // Check we can write phar
  if ( !Phar::canWrite() ) {
    echo "Can not write phar please run build script with:\n";
    echo "$ php -d phar.readonly=0 {$argv[0]}\n";
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
  $pharFilename = "ThriftSQL.phar";
  if ( file_exists( $pharFilename ) ) {
    unlink( $pharFilename );
  }
  $phar = new Phar( $pharFilename );
  $phar->buildFromDirectory( __DIR__ . '/src' );
  $phar->setStub( $stub );
  $phar->setSignatureAlgorithm( Phar::SHA256 );
  $phar->compressFiles( Phar::GZ );

  echo "Built!\n";
