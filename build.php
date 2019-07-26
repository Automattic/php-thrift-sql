<?php

  // Check we can write phar
  if ( !Phar::canWrite() ) {
    echo "Can not write phar please run build script with:\n";
    echo "$ php -d phar.readonly=0 {$argv[0]}\n";
    exit(1);
  }

  // Check for autoload file
  if ( ! file_exists( 'src/autoload.php' ) ) {
    echo "Could not find generated autoload file, please use make tool:\n";
    echo "$ make phar\n";
    exit(1);
  }

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
