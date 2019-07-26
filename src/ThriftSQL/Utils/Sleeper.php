<?php

namespace ThriftSQL\Utils;

/**
 * Util to do exponential sleeping.
 */

class Sleeper {

  private $_iterations = 0;
  private $_slept = 0;

  public function reset() {
    $this->_iterations = 0;
    $this->_slept = 0;

    return $this;
  }

  public function sleep() {
    $mSecs = $this->_getSleepMS();

    usleep( $mSecs * 1000 );

    $this->_iterations++;
    $this->_slept += $mSecs;

    return $this;
  }

  public function getSleptSecs() {
    return $this->_slept / 1000;
  }

  private function _getSleepMS() {
    if ( 14 < $this->_iterations ) {
      return 30000; // Max out at 30 second sleep per check
    }

    return pow( 2, $this->_iterations );
  }

}
