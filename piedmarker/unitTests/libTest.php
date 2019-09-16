<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class lib extends TestCase{
    public function test_test(){
    	///home/travis/build/maniac22/

    	require("app_prototypes/test.php");
       // $expected = array("Classic Mode", "Fastest Mode", "Tournament Mode", "AI Mode");
        $this->expectOutputString('40');
        //$this->assertEquals("2","2","correct");
    }
     public function test_test4(){
     	$this->assertEquals("2","2","correct");
    }
 
}
