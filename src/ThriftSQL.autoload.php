<?php
// for local dev & debug purpose
spl_autoload_register(function($name) {
    static $thriftExtraLoaded = false;
    $prefix = 'Thrift';
    $prefixLen = strlen($prefix);
    if(strncmp($name, $prefix, $prefixLen) == 0) {
        $fn = __DIR__ . '/' . str_replace('\\', '/', $name) . '.php';
        if (file_exists($fn)) {
            require $fn;
            if (!$thriftExtraLoaded) {
                $thriftExtraLoaded = true;
                require __DIR__ . '/ThriftExtras/Transport/TSaslClientTransport.php';
            }
        }
    }
});
