<?php
require_once("config.php");
require_once("lib.php");

$inputJSON = file_get_contents('php://input');
$input= json_decode( $inputJSON, TRUE );

$source =  base64_decode($input['source']);
//echo json_encode($source);


$val = mark($input['language'], $source, 
        $input['input'], $input['output'], $input["timelimit"]);

echo json_encode($val);

/* $outputs = run("/var/www/mark",'gcc -o myProgram prog.c', '');
  var_dump($outputs);

  $outputs = run('/var/www/mark','./myProgram', '5', 10);
  var_dump($outputs);
 */


?>
