<?php
/**
 *
 * @author John Learn <https://github.com/logicbrush>
 */

namespace Logicbrush\Metrics\Annotator;

/**
 * Bootstrap class for the Metrics annotator
 *
 */
class MetricsBootstrap
{
    /**
     * @return unknown
     */
    public static function createAnnotator( string $clover, string $file )
    {
        $annotator = new MetricsAnnotator( $clover, $file );

        return $annotator;
    }

}
