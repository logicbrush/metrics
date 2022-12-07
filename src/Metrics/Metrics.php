<?php

namespace Logicbrush\Metrics;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 *
 * @Annotation
 * @Target("METHOD")
 * @package default
 */


class Metrics {

	/** @Required */
	public float $crap;

	public bool $uncovered = false;

}
