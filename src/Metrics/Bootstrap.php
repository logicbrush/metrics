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
class Bootstrap {

	/**
	 *
	 * @Metrics( crap = 2, uncovered = true )
	 * @return unknown
	 */
	public static function createAnnotator( string $clover, string $file ) {
		$annotator = new AnnotatorImpl( $clover, $file );
		return $annotator;
	}


}
