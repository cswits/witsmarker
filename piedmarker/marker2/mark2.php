<?php

/**
 * @file mark.php
 * Web service interface.
 */
require_once("config.php"); // Include Global Configuration
require_once("lib.php");    // Include Library Functions
/*$inputJSON =<<<JSN
{"userid":"2","language":"4","cpu_limit":"0.1","mem_limit":"0","pe_ratio":"0","callback":"1710409.ms.wits.ac.za/moodleDev/mod/assign/feedback/customfeedback/update_record.php?assign_id=21&question_id=0","testcase":{"url":"1710409.ms.wits.ac.za/lab.zip","contenthash":"5bcf9b30d2e5bda7920c4187b7d9976f316a9294","pathnamehash":"6653cacf2de49d5ccdb1f9fa4ca5f293e3a469e5"},"source":{"content":"cHJpbnQoImhlbGxvIik=","ext":"txt"},"customfeedback_token":"1e6947ac7fb3a9529a9726eb692c8cc5","markerid":"1","firstname":"mpho","lastname":"mpho"}
JSN;
*/
$inputJSON = file_get_contents('php://input');  // Get input from the client
$input = json_decode($inputJSON, TRUE);        // Decode the JSON object
$markerid = $input["markerid"];
$auth = $input["customfeedback_token"];
$userid = $input["userid"];
$firstname = $input["firstname"];
$lastname = $input["lastname"];
$language = $input["language"];
$cpu_limit = $input["cpu_limit"];
$mem_limit = $input["mem_limit"];
$pe_ratio = $input["pe_ratio"];
error_log("PE_RATIO: " . $pe_ratio);
$callback = $input["callback"]; // Post back to this address after marking
$testcase = $input["testcase"]; // url, contenthash, pathnamehash
$source = base64_decode($input["source"]["content"]);     // Decode the Base64
settings::$temp .= "/$markerid";
// Start buffering output
ob_start();
//die($auth);
if($auth != settings::$auth_token['customfeedback_token']){
	error_log('{"status" : "Bad auth"}');
	die('{"status" : "Bad auth"}');
}
//die(settings::$auth_token['customfeedback_token']);
// Get the test case
//   If they are already cached locally, use that
//   If not, download and extract the file from moodle
//       This will die("{status : feedback}") on error.
$tests = testcases($testcase);

//print(json_encode($tests));

$tests = testcases($testcase);
$test_count = count($tests["yml"]["test_cases"]);
$output = array("status" => "0", "test_count" => $test_count);

//print(json_encode($output));

// Send all the output back to moodle

$size =ob_get_length();
header("Content-Encoding: none");
header("Content-Length: {$size}");
header("Connection: close");
ob_end_flush();
ob_flush();
flush();

// TODO
// Now continue with the marking work.
error_log("Closed moodle connection. Starting to mark....");


$marker_data = mark($source, $tests, $language, $userid, $firstname, $lastname, $markerid, $cpu_limit, $mem_limit, floatval($pe_ratio));
$status = $marker_data["status"];

$oj_feedback = $marker_data["oj_feedback"];
$grade = $marker_data["grade"];
$outputs = $marker_data["outputs"];
error_log("Finished Marking... Sending grade to moodle..." . $grade);
return_grade($callback, $markerid, $userid, $grade, $status, json_encode($outputs), $oj_feedback);
var_dump("sent data to update_record.");

error_log("Grade sent.");


?>
