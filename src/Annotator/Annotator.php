<?php

namespace Logicbrush\Metrics\Annotator;

use SimpleXMLElement;

/**
 * Annotator interface
 *
 */
interface Annotator
{
    
    public function __construct(  string $clover, string $file );
    public function run();

}
