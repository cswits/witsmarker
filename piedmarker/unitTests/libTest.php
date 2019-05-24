<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
require_once('../libPrototype.php');
class libTest extends TestCase{
    public function test_Test(){
        $tester=Test();
        $result = $tester;
        $expected = array("Classic Mode", "Fastest Mode", "Tournament Mode", "AI Mode");
        $this->assertEquals(2,$result,"correct!");
    }
 
 
}
