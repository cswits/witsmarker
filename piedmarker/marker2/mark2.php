<?php
require_once("config.php"); // Include Global Configuration
require_once("lib.php");    // Include Library Functions

// $inputJSON =<<<JSN
// {"userid":"2","n":"5","language":"4","cpu_limit":"0.1","mem_limit":"0","pe_ratio":"0","callback":"1710409.ms.wits.ac.za/moodleDev/mod/assign/feedback/customfeedback/update_record.php?assign_id=28&question_id=0","testcase":{"url":"http://1710409.ms.wits.ac.za/opti.zip","contenthash":"371f4d2751056d8ee42faebc7e6e5f3e5c0a245b","pathnamehash":"31231126c5d3f8a617652fbe92b73dbf6cd8a98f"},"source":{"content":"cHJpbnQoInR1bmEiKQ==","ext":"py"},"evaluator":{"content":"Yz1zdHIoaW5wdXQoKSkKcHJpbnQoYyxsZW4oYykp","ext":"py"},"mode":"1","customfeedback_token":"1e6947ac7fb3a9529a9726eb692c8cc5","markerid":"1","firstname":"mpho","lastname":"mpho"}
// JSN;
$inputJSON = file_get_contents('php://input');//Get input from the client
$input = json_decode($inputJSON, TRUE); // Decode the JSON object
$markerid = $input["markerid"];
echo $inputJSON;
$auth = $input["customfeedback_token"];
$userid = $input["userid"];
$firstname = $input["firstname"];
$lastname = $input["lastname"];
$language = $input["language"];
$cpu_limit = $input["cpu_limit"];
$mem_limit = $input["mem_limit"];
$pe_ratio = $input["pe_ratio"];
$n=$input["n"];
$type =$input["mode"];
$callback = $input["callback"]; // Post back to this address after marking
$testcase = $input["testcase"]; // url, contenthash, pathnamehash
$source = base64_decode($input["source"]["content"]);     // Decode the Base64
settings::$temp .= "/$markerid";
// Start buffering output
//ob_start();
// var_dump(settings::$temp);

if(!isset($input["evaluator"]["content"]) and $type==1)die("{Evaluator source not provided}");
if($auth != settings::$auth_token['customfeedback_token']){
	error_log('{"status" : "Bad auth"}');
	die('{"status" : "Bad auth"}');
}

$tests = testcases($testcase);
$test_count = count($tests["yml"]["test_cases"]);
$output = array("status" => "0", "test_count" => $test_count);
//echo json_encode($tests);

// Send all the output back to moodle
// $size =ob_get_length();
// header("Content-Encoding: none");
// header("Content-Length: {$size}");
// header("Connection: close");
// ob_end_flush();
// ob_flush();
// flush();
// Now continue with the marking work.
error_log("Closed moodle connection. Starting to mark....");

//student's code! 
$evaluate=false;
$student_marker_data = mark($source, $tests, $language, $userid, $firstname, $lastname, $markerid, $cpu_limit, $mem_limit, floatval($pe_ratio),$n,$type,$evaluate,"");
$status = $student_marker_data["status"];
$oj_feedback = $student_marker_data["oj_feedback"];
$outputs = $student_marker_data["outputs"];

//evaluator!
if($type==1){
	$evaluate=TRUE;
	$eval_code=base64_decode($input["evaluator"]["content"]);
	$eval_input=$student_marker_data["outputs"][0]['stdout'];
	$evaluator_marker_data = mark($eval_code, $tests, $language, $userid, $firstname, $lastname, $markerid, $cpu_limit, $mem_limit, floatval($pe_ratio),$n,$type,$evaluate,$eval_input);
	$status = $evaluator_marker_data["status"];;
	$oj_feedback = $evaluator_marker_data["oj_feedback"];
	$outputs = $evaluator_marker_data["outputs"];
	$score=$evaluator_marker_data["outputs"][0]['stdout'];
	//echo $score;
}
//echo json_encode($outputs);

return_grade($callback, $markerid, $userid, $grade, $status, json_encode($outputs), $oj_feedback,$type,$score);
error_log("Grade sent.");


?>
