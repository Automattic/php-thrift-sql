<?php

namespace ThriftSQL\Utils;

/**
 * Util to clean up queries; e.g. remove trailing `;`.
 */

class QueryCleaner {

  public function clean( $queryStr ) {
    // Very simplistic
    return trim( $queryStr, "; \t\n\r\0\x0B" );
  }

}
