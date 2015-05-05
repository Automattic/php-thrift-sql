<?php

  // Cleanup
  if ( file_exists( __DIR__ . 'ThriftSQL.phar' ) ) {
    Phar::unlinkArchive( __DIR__ . 'ThriftSQL.phar' );
  }

  // Create Stub
  $stub = <<<EOF
<?php
  include 'phar://' . __FILE__ . '/autoload.php';
  __HALT_COMPILER();
EOF;

  // Create Phar
  $phar = new Phar( 'ThriftSQL.phar', null, 'ThriftSQL.phar' );
  $phar->buildFromDirectory( __DIR__ . '/src' );
  $phar->setStub( $stub );
