<?php

require_once __DIR__.'/../../../autoload.php';

if (!class_exists('Logicbrush\Metrics\Bootstrap')) {
    require_once __DIR__ . '/Metrics/Bootstrap.php';
}

try {
    // if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    //     throw new \ErrorException('PHP Version is lower than 8.0.0. Please upgrade your runtime.');
    // }
    if ( isset($argv[1]) && ($argv[1] == 'annotate') &&
         isset($argv[2]) && ($clover = $argv[2]) && 
         isset($argv[3]) && ($file = $argv[3]) ) {
        return Logicbrush\Metrics\Bootstrap::createAnnotator( $clover, $file );
    }
    throw new \ErrorException('You must include path to coverage file and file to be annotated as arg 1 and arg 2 respectively.');

} catch (Exception $e) {
    printf( $e->getMessage() );
    exit(1);
}
