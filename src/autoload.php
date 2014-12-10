<?php
// Autoload from classmap
spl_autoload_register( function ( $class ) {
  $classmap = require __DIR__ . '/autoload_classmap.php';
  if( array_key_exists( $class, $classmap ) ) {
    require_once $classmap[ $class ];
  }
} );
