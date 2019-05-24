<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
require_once('/home/travis/build/maniac22/PiedMarker2/marker2/app_Prototype/libPrototype.php');
class libTest extends TestCase{
    public function test_Test(){
        $tester=Test();
        $result = $tester;
        $expected = array("Classic Mode", "Fastest Mode", "Tournament Mode", "AI Mode");
        $this->assertEquals(2,$result,"correct!");
    }
 
 
}
