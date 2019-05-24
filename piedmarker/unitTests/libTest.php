<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
class libTest extends TestCase{
    public function test_Test_2(){
        $temp=new mork;
        $tester=temp->Test();
        $result = $tester;
        $this->assertEquals(2,$result,"correct!");
    }
 
 
}
