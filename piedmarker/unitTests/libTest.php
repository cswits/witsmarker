<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
class lib extends TestCase{
    public function test_test(){
    	require("nan.php");
        $expected = array("Classic Mode", "Fastest Mode", "Tournament Mode", "AI Mode");
       $this->expectOutputString('3');
    }
 
 
}
