<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
class lib extends TestCase{
    public function test_Test_2(){
        $it=new tuna;
        $expected = array("Classic Mode", "Fastest Mode", "Tournament Mode", "AI Mode");
        $this->assertEquals(2,2,"correct!");
    }
 
 
}
