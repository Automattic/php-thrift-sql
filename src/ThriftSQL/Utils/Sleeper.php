<?php

namespace ThriftSQL\Utils;

/**
 * Util to do exponential sleeping.
 */
class Sleeper
{

  private $iterations = 0;
  private $slept = 0;

  public function reset()
  {
    $this->iterations = 0;
    $this->slept = 0;

    return $this;
  }

  public function sleep()
  {
    $mSecs = $this->getSleepMs();

    usleep($mSecs * 1000);

    $this->iterations++;
    $this->slept += $mSecs;

    return $this;
  }

  public function getSleptSecs()
  {
    return $this->slept / 1000;
  }

  private function getSleepMs()
  {
    if (14 < $this->iterations) {
      return 30000; // Max out at 30 second sleep per check
    }

    return pow(2, $this->iterations);
  }

}
