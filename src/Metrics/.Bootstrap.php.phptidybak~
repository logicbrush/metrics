<?php
/**
 *
 * @author John Learn <https://github.com/logicbrush>
 */

namespace Logicbrush\Metrics;

use Logicbrush\Metrics\Impl\AnnotatorImpl;

/**
 * Bootstrap class for the Metrics annotator
 *
 */
class Bootstrap
{
    /**
     * @return unknown
     *
     * @Metrics( crap = 2, uncovered = true )
     */
    public static function createAnnotator( string $clover, string $file )
    {
        $annotator = new AnnotatorImpl( $clover, $file );
        return $annotator;
    }

}
