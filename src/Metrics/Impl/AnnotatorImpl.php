<?php

namespace Logicbrush\Metrics\Impl;

use Logicbrush\Metrics\Annotator;
use SimpleXMLElement;

// These constants are only defined in PHP 8.0+.
defined( 'T_NAME_QUALIFIED' ) or define( 'T_NAME_QUALIFIED', -1 );

/**
 * Metrics implementation of the Annotator interfce
 *
 * This class will examine the clover metrics for the project and will update
 * the method Docblocks in the provided source file with `@Metrics` annotations.
 *
 * @noRector
 *
 *
 * @package default
 */
class AnnotatorImpl implements Annotator
{
	private $path_to_clover, $path_to_file;

	/**
	 *
	 * @Metrics( crap = 1 )
	 */
	public function __construct( string $clover, string $file ) {
		$this->path_to_clover = $clover;
		$this->path_to_file = $file;
	}


	/**
	 *
	 * @Metrics( crap = 26.12 )
	 */
	public function run() {
		defined( 'STDIN' ) or die( 'command line only.' );


		$file = $this->path_to_file;
		$code = file_get_contents( $file );
		$tokens = token_get_all( $code, TOKEN_PARSE );
		$token = [];

		$clover = new SimpleXMLElement( file_get_contents( $this->path_to_clover ) );

		$function = null;
		$class = null;
		$namespace = null;

		$depth = 0;

		while ( $this->pop_token( $tokens, $token, $depth, $key ) ) {
			handle_token:
			switch ( $token[0] ) {
			case T_NAMESPACE:
				if ( $depth == 0 ) {
					$namespace = "";
					while ( $this->pop_token( $tokens, $token, $depth ) ) {
						switch ( $token[0] ) {
						case T_STRING:
						case T_NS_SEPARATOR:
						case T_NAME_QUALIFIED:
							if ( $token[1] != ';' ) {
								$namespace .= $token[1];
							}
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
					while ( $this->pop_token( $tokens, $token, $depth ) ) {
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
					while ( $this->pop_token( $tokens, $token, $depth ) ) {
						switch ( $token[0] ) {
						case T_STRING:
							$function .= $token[1];
							break;
						case T_WHITESPACE:
							break;
						default:
							if ( ( $metrics = $this->metrics( $clover, $function, $class, $namespace ) ) !== null ) {
								$this->annotate( $tokens, $key, $metrics );
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
	 * @Metrics( crap = 17.82 )
	 * @param array   $tokens (reference)
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
									'#^((\s*\*)\s*)@(?:Logicbrush\\\\)?Metrics(.*)\s*$#m',
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
	 * @Metrics( crap = 5.58 )
	 * @param string  $function
	 * @param string  $class
	 * @param string  $namespace
	 * @return unknown
	 */
	protected function metrics( SimpleXMLElement $clover, ?string $function, ?string $class, ?string $namespace ): ?SimpleXMLElement {

		if ( $function && $class ) {
			if ( $namespace ) {
				$path = "//class[@name='{$namespace}\\{$class}']/following-sibling::line[@type='method'][@name='{$function}']";
			} else {
				$path = "//class[@name='{$class}'][@namespace='global']/following-sibling::line[@type='method'][@name='{$function}']";

			}
			if ( $node = @$clover->xpath( $path )[0] ) {
				return $node->attributes();
			}
		}

		return null;
	}


	/**
	 *
	 * @Metrics( crap = 7.10 )
	 * @param array   $array (reference)
	 * @param unknown $token (reference)
	 * @param int     $depth (reference)
	 * @param int     $key   (optional, reference)
	 * @return unknown
	 */
	protected function pop_token( array &$array,  &$token, int &$depth, int &$key = null ) : bool {

		while ( ( $key = key( $array ) ) !== null ) {

			$token = current( $array );
			next( $array );

			// var_dump($token);

			if ( is_array( $token ) ) {
				switch ( $token[0] ) {
				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
					++$depth;
					break;
				}
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
