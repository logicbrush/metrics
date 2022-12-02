A<?php

use Logicbrush\Metrics\Impl\AnnotatorImpl;
use PHPUnit\Framework\TestCase;

class AnnotatorTest extends TestCase {
    private $path_to_clover, $path_to_file;

    public function setUp() : void {
        parent::setUp();
    }


    public function tearDown() : void {
        unlink( $this->path_to_clover );
        unlink( $this->path_to_file );
        parent::tearDown();
    }


    /**
     * @covers AnnotatorImpl::run
     */
    public function test_annotating_a_method() {

        $this->path_to_clover = $this->createCloverFile( 'testClass', ['testMethod'] );
        $this->path_to_file = $this->createSourceFile( true );

        $annotator = new AnnotatorImpl( $this->path_to_clover, $this->path_to_file );
        $annotator->run();

        $this->assertStringContainsString(
            '@Metrics( crap = 2, uncovered = true )',
            file_get_contents( $this->path_to_file )
        );
    }


    /**
     * @covers AnnotatorImpl::run
     */
    public function test_a_method_without_docblock_will_not_be_annotated() {
        $this->path_to_clover = $this->createCloverFile( 'testClass', ['testMethod'] );
        $this->path_to_file = $this->createSourceFile( false );

        $annotator = new AnnotatorImpl( $this->path_to_clover, $this->path_to_file );
        $annotator->run();

        $this->assertStringNotContainsString(
            '@Metrics( crap = 2, uncovered = true )',
            file_get_contents( $this->path_to_file )
        );
    }


    protected function createCloverFile( $class, $methods = [] ) {
        $lines = '';

        foreach ( $methods as $method ) {
            $lines .= '<line num="" type="method" name="'.$method.'" complexity="6" crap="2" count="0"/>';
        }

        $xml =
'<?xml version="1.0" encoding="UTF-8"?>
<coverage generated="1666990245">
  <project timestamp="1666990245">
    <package name="">
      <file name="">
        <class name="Tests\\'.$class.'" namespace="Tests">
          <metrics complexity="5" methods="'.count($methods).'" coveredmethods="0" conditionals="0" coveredconditionals="0" statements="0" coveredstatements="0" elements="'.count($methods).'" coveredelements="0"/>
        </class>
        '.$lines.'
        <metrics loc="82" ncloc="55" classes="1" methods="'.count($methods).'" coveredmethods="0" conditionals="0" coveredconditionals="0" statements="0" coveredstatements="0" elements="'.count($methods).'" coveredelements="0"/>
      </file>
    </package>
    <metrics files="1" loc="82" ncloc="55" classes="1" methods="'.count($methods).'" coveredmethods="0" conditionals="0" coveredconditionals="0" statements="0" coveredstatements="0" elements="'.count($methods).'" coveredelements="0"/>
  </project>
</coverage>';

        $file = tempnam( __DIR__, 'clover_' );
        $handle = fopen($file, "w");
        fwrite($handle, $xml);
        fclose($handle);

        return $file;

    }


    protected function createSourceFile( $docBlock = true ) {
        $content =
'<?php
/**
 * @package default
 */

namespace Tests;

class testClass {';

    if ( $docBlock ) {
        $content .=
'
    /**
     *
     */';
    }

    $content .=
    'public function testMethod() {
        return true;
    }
}';

        $file = tempnam( __DIR__, 'source_' );
        $handle = fopen($file, "w");
        fwrite($handle, $content);
        fclose($handle);

        return $file;
    }
    

}
