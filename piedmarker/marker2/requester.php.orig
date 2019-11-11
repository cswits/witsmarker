<?php
/**
 * @file requester.php
 * Sample client form to submit and test a program.
 */
// Show errors/warnings
error_reporting(E_ALL);
ini_set('display_errors', '1');
// The test program

$source=<<<EOF

print("Heloofddf");
EOF;
// Always base64_encode the source
$source = base64_encode($source);
// languageid, source, input, output and timelimit
$data = array("language" => 4,"source"=>$source,
        "input"=> 'test', "output" => 'Heloo', "timelimit" =>3);
// json_encode the object to send to the marker
$data_string = json_encode($data);                                                                                   

// Post the data to the marker
$options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => $data_string ,
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
$url = "http://1710409.ms.wits.ac.za/marker2/mark.php";

$context  = stream_context_create( $options );
$result = file_get_contents( $url, false, $context );
$response = json_decode( $result );

// Show the response
var_dump($response);

?>
