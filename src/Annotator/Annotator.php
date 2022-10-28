<?php

namespace Logicbrush\Metrics\Annotator;

/**
 * Annotator interface
 *
 */
interface Annotator
{
    /**
     *
     */
    protected function annotate();

    /**
     *
     */
    protected function metrics() : ?SimpleXMLElement;

    /**
     *
     */
    protected function pop_token() : bool;

}
