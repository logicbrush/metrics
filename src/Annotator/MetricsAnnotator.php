<?php

namespace Logicbrush\Metrics\Annotator;

/**
 * Metrics implementation of the Annotator interfce
 *
 * This class will examine the clover metrics for the project and will update
 * the method Docblocks in the provided source file with `@Metric` annotations.
 *
 * @noRector
 *
 * @package default
 *
 */
class MetricsAnnotator implements Annotator
{
    /**
     *
     */
    public function run() {
        defined( 'STDIN' ) or die( 'command line only.' );


        $file = $argv[1];
        $code = file_get_contents( $file );
        $tokens = \PhpToken::tokenize( $code, TOKEN_PARSE );

        $clover = new SimpleXMLElement( file_get_contents( __DIR__ . "/../build/coverage/clover.xml" ) );

        $function = null;
        $class = null;
        $namespace = null;

        $depth = 0;

        while ( pop_token( $tokens, $token, $depth, $key ) ) {
            handle_token:
            switch ( $token[0] ) {
            case T_NAMESPACE:
                if ( $depth == 0 ) {
                    $namespace = "";
                    while ( pop_token( $tokens, $token, $depth ) ) {
                        switch ( $token[0] ) {
                        case T_STRING:
                        case T_NS_SEPARATOR:
                            $namespace .= $token[1];
                            break;
                        case T_WHITESPACE:
                            break;
                        default:
                            goto handle_token;
                        }
                    }
                }
                break;
            case T_CLASS:
                if ( $depth == 0 ) {
                    $class = null;
                    while ( pop_token( $tokens, $token, $depth ) ) {
                        switch ( $token[0] ) {
                        case T_STRING:
                            $class .= $token[1];
                            break;
                        case T_WHITESPACE:
                            break;
                        default:
                            goto handle_token;
                        }
                    }
                }
                break;
            case T_FUNCTION:
                if ( $depth == 1 ) {
                    $function = null;
                    while ( pop_token( $tokens, $token, $depth ) ) {
                        switch ( $token[0] ) {
                        case T_STRING:
                            $function .= $token[1];
                            break;
                        case T_WHITESPACE:
                            break;
                        default:
                            if ( ( $metrics = metrics( $clover, $function, $class, $namespace ) ) !== null ) {
                                annotate( $tokens, $key, $metrics );
                            }
                            goto handle_token;
                        }
                    }
                }
                break;
            }
        }

        file_put_contents( $file, array_reduce( $tokens, fn( $output, $token ) => $output . ( is_array( $token ) ? $token[1] : $token ), '' ) );
    }

    /**
     *
     * @param array            $tokens  (reference)
     * @return unknown
     */
    protected function annotate( array &$tokens, int $key, SimpleXMLElement $metrics ) {
        while ( $key >= 1 && ( $token = $tokens[--$key] ) ) {
            if ( is_array( $token ) ) {
                switch ( $token[0] ) {
                case T_DOC_COMMENT:
                    $uncovered = $metrics['count'] > 0 ? "" : ", uncovered = true";
                    $tag = "@Metrics( crap = {$metrics['crap']}{$uncovered} )";
                    $first = true;
                    if ( ! ( ( $tokens[$key][1] = preg_replace_callback(
                                    '#^((\s*\*)\s*)@Metrics(.*)\s*$#m',
                                    function( $array ) use ( &$first, $tag ) {
                                        if ( $first ) {
                                            $first = false;
                                            return "{$array[1]}{$tag}";
                                        }
                                        return "{$array[1]}";
                                    },
                                    $token[1],
                                    -1, $count
                                ) ) && $count ) ) {
                        ( $tokens[$key][1] = preg_replace(
                                '#(^\s*\*)(/\s*)$#m',
                                "$1\n$1 {$tag}\n$1$2",
                                $token[1],
                                1, $count
                            ) ) && $count;
                    }
                    return true;
                case T_WHITESPACE:
                case T_PUBLIC:
                case T_PROTECTED:
                case T_PRIVATE:
                case T_STATIC;
                    break;
                default:
                    return false;
                }
            }
        }
        return false;
    }


    /**
     *
     * @param string           $function
     * @param string           $class
     * @param string           $namespace
     * @return unknown
     */
    protected function metrics( SimpleXMLElement $clover, ?string $function, ?string $class, ?string $namespace ): ?SimpleXMLElement {

        if ( $function && $class && $namespace ) {
            $path = "//class[@name='{$namespace}\\{$class}']/following-sibling::line[@type='method'][@name='{$function}']";
            if ( $node = @$clover->xpath( $path )[0] ) {
                return $node->attributes();
            }
        }

        return null;
    }


    /**
     *
     * @param array   $array (reference)
     * @param unknown $token (reference)
     * @param int     $depth (reference)
     * @param int     $key   (optional, reference)
     * @return unknown
     */
    protected function pop_token( array &$array, &$token, int &$depth, int &$key = null ) : bool {

        while ( ( $key = key( $array ) ) !== null ) {

            $token = current( $array );
            next( $array );

            if ( is_array( $token ) ) {
                return true;
            }

            switch ( $token ) {
            case '{': ++$depth; break;
            case '}': --$depth; break;
            }

            $token = [ -1, $token, -1];
            return true;

        }

        return false;
    }
}
