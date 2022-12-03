A<?php

use Logicbrush\Metrics\Impl\AnnotatorImpl;
use PHPUnit\Framework\TestCase;

class AnnotatorTest extends TestCase {

    private string $coverage_file, $source_file;

    
    public function tearDown() : void {
        @unlink( $this->coverage_file );
        @unlink( $this->source_file );
        parent::tearDown();
    }


    public function test_annotating_a_method_in_an_unnamespaced_class() {

        $this->source_file = $this->withSource( <<<EOF
            <?php
            class TestClass {
                /**
                 * method 'testMethod'
                 */    
                public function testMethod() {
                    return true;
                }
            };
EOF
        );
        $this->coverage_file = $this->withCoverage( 'TestClass', ['testMethod'] );

        $annotator = new AnnotatorImpl( $this->coverage_file, $this->source_file );
        $annotator->run();

        $this->assertStringContainsString(
            '@Metrics( crap = 2, uncovered = true )',
            file_get_contents( $this->source_file )
        );
    }

    public function test_annotating_a_method_in_a_namespaced_class() {

        $this->source_file = $this->withSource( <<<EOF
            <?php
            namespace Tests;            
            class TestClass {
                /**
                 * method 'testMethod'
                 */    
                public function testMethod() {
                    return true;
                }
            };
EOF
        );
        $this->coverage_file = $this->withCoverage( 'TestClass', ['testMethod'], 'Tests' );

        $annotator = new AnnotatorImpl( $this->coverage_file, $this->source_file );
        $annotator->run();

        $this->assertStringContainsString(
            '@Metrics( crap = 2, uncovered = true )',
            file_get_contents( $this->source_file )
        );
    }


    public function test_a_method_without_docblock_will_not_be_annotated() {

        $this->source_file = $this->withSource( <<<EOF
            <?php
            class TestClass {
                public function testMethod() {
                    return true;
                }
            };
EOF
        );
        $this->coverage_file = $this->withCoverage( 'TestClass', ['testMethod'], 'Tests' );

        $annotator = new AnnotatorImpl( $this->coverage_file, $this->source_file );
        $annotator->run();

        $this->assertStringNotContainsString(
            '@Metrics( crap = 2, uncovered = true )',
            file_get_contents( $this->source_file )
        );
    }


    protected function withCoverage( string $className, array $methods = [], string $namespace = null ) {
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
        <class name="'.($namespace ? $namespace ."\\" : '').$className.'" namespace="'.($namespace ?: 'global').'">
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


    protected function withSource( string $content ) {       

        $file = tempnam( __DIR__, 'source_' );
        $handle = fopen($file, "w");
        fwrite($handle, $content);
        fclose($handle);

        return $file;

    }
    

}
