<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
$source=<<<EOF
        #include <stdio.h>
        int main(){
            printf("Hello");
         }
EOF;
$source = base64_encode($source);
        
$data = array("language" => 5,"source"=>$source,
        "input"=> 'test', "output" => 'Hello', "timelimit" =>3);

                                                                  
$data_string = json_encode($data);                                                                                   

$options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => $data_string ,
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
$url = "http://127.0.0.1/mark/mark.php";

$context  = stream_context_create( $options );
$result = file_get_contents( $url, false, $context );
$response = json_decode( $result );

var_dump($response);

?>
