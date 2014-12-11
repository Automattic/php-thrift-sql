<?php

  // Cleanup
  if ( file_exists( __DIR__ . 'ThriftSQL.phar' ) ) {
    Phar::unlinkArchive( __DIR__ . 'ThriftSQL.phar' );
  }

  // Create Phar
  $phar = new Phar( 'ThriftSQL.phar', null, 'ThriftSQL.phar' );
  $phar->buildFromDirectory( __DIR__ . '/src' );
  $phar->setStub( $phar->createDefaultStub(
    'autoload.php',
    'autoload.php'
  ) );
