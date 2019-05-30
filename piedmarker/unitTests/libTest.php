<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
class lib extends TestCase{
    public function test_test(){
    	require("/home/travis/build/maniac22/PiedMarker2/app_prototypes/test.php");
        $expected = array("Classic Mode", "Fastest Mode", "Tournament Mode", "AI Mode");
       $this->expectOutputString('40');
    }
 
 
}
