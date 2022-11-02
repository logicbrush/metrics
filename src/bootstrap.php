<?php

require_once __DIR__.'/../../../autoload.php';

if (!class_exists('Logicbrush\Metrics\Annotator\MetricsBootstrap')) {
    require_once __DIR__ . '/Annotator/MetricsBootstrap.php';
}

try {
    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
        throw new \ErrorException('PHP Version is lower than 8.0.0. Please upgrade your runtime.');
    }
    if ( isset($argv[1]) && ($clover = $argv[1]) && isset($argv[2]) && ($file = $argv[2]) ) {
        return Logicbrush\Metrics\Annotator\MetricsBootstrap::createAnnotator( $clover, $file );
    }
    throw new \ErrorException('You must include path to coverage file and file to be annotated as arg 1 and arg 2 respectively.');

} catch (Exception $e) {
    printf( $e->getMessage() );
    exit(1);
}
