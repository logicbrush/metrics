<?php

namespace Logicbrush;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Metrics {

    /** @Required */
    public float $crap;

    public bool $uncovered = false;

}