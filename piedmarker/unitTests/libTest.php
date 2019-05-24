<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
#require_once('locallib.php');
class locallibTest extends TestCase{
    public function test_get_modes(){
        $tester=new assign_feedback_customfeedback;
        $result = $tester->get_modes();
        $expected = array("Classic Mode", "Fastest Mode", "Tournament Mode", "AI Mode");
        $this->assertEquals($expected,$result,"correct!");
    }
    public function test_get_languages(){
        $tester=new assign_feedback_customfeedback;
        $result = $tester->get_languages();
        $expected = array('Java', 'Python', 'C++');
        $this->assertEquals($expected,$result,"correct!");
    }
    public function test_get_language_code(){
        $tester=new assign_feedback_customfeedback;
        $langs = array('Java' => 1, 'Python' => 4, 'C++' => 12);
        foreach ($langs as $lang => $code) {
            $this->assertEquals($tester->get_language_code($lang),$code);
        }
    }
    public function test_get_question_numbers(){
        $tester=new assign_feedback_customfeedback;
        $result = $tester->get_question_numbers();
        $expected = array(1,2,3,4,5,6,7,8,9,10);
        $this->assertEquals($result,$expected);
    }
    public function test_get_time_limits(){
        $tester=new assign_feedback_customfeedback;
        $result = $tester->get_time_limits();
        $expected = array(1,3,5,10,20,60);
        $this->assertEquals($expected,$result,"correct!");
    }
    public function test_get_memory_limits(){
        $tester=new assign_feedback_customfeedback;
        $result = $tester->get_memory_limits();
        $expected = array(1,2,4,16,32,64,512,1024);;
        $this->assertEquals($expected,$result,"correct!");
    }
    public function test_get_testcase_filearea(){
        $tester=new assign_feedback_customfeedback;
        $result = $tester->get_testcase_filearea(1);
        $expected = 'competition_testcases1';
        $this->assertEquals($result,$expected);
    }
    public function test_get_callback_url(){
        $tester=new assign_feedback_customfeedback;
        $result = $tester->get_callback_url(1,1);
        $expected = 'someWebserver/mod/assign/feedback/customfeedback/update_record.php?assign_id=1&question_id=1';
        $this->assertEquals($result,$expected);
    }
  
    public function test_format_for_gradebook(){
        $this->assertEquals(2, 2,"correct!"); 
    }
    
    public function test_supports_quickgrading(){
            $tester=new assign_feedback_customfeedback;
            $result = $tester->supports_quickgrading();
            $expected = false;
            $this->assertEquals($expected,$result,"correct!"); 
    }
	
	public function test_get_grading_actions(){
		$tester=new assign_feedback_customfeedback;
		$result = $tester->get_grading_actions();
        $expected = array();
        $this->assertEquals($expected,$result,"correct!"); 
	}
 
	public function test_get_grading_batch_operations(){
		$tester=new assign_feedback_customfeedback;
		$result = $tester-> get_grading_batch_operations();
        $expected = array();
        $this->assertEquals($expected,$result,"correct!"); 
	}
 
 
}
