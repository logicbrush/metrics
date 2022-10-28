<?php

include __DIR__.'/../vendor/autoload.php';

if (!class_exists('Logicbrush\Metrics\Annotator\MetricsBootstrap')) {
    require_once __DIR__ . '/Annotator/MetricsBootstrap.php';
}

try {
    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
        throw new \ErrorException('PHP Version is lower than 8.0.0. Please upgrade your runtime.');
    }
    return Logicbrush\Metrics\Annotator\MetricsBootstrap::createAnnotator();
} catch (Exception $e) {
    printf( $e->getMessage() );
    exit(1);
}
