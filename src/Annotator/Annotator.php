<?php

namespace Logicbrush\Metrics\Annotator;

/**
 * Annotator interface
 *
 */
interface Annotator
{
    public function __construct(  string $clover, string $file );
    /**
     *
     */
    public function annotate( array &$tokens, int $key, SimpleXMLElement $metrics );

    /**
     *
     */
    public function metrics( SimpleXMLElement $clover, ?string $function, ?string $class, ?string $namespace ) : ?SimpleXMLElement;

    /**
     *
     */
    public function pop_token( array &$array, &$token, int &$depth, int &$key = null ) : bool;

}
