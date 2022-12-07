<?php

namespace Logicbrush\Metrics;

/**
 * Annotator interface
 *
 * @package default
 */


interface Annotator
{



	/**
	 *
	 * @param string  $clover
	 * @param string  $file
	 */
	public function __construct(  string $clover, string $file );

	/**
	 *
	 */
	public function run();

}
