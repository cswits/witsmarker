<?php
require_once("lib.php");
$outputs = run('gcc -o /tmp/marker/myProgram /var/www/mark/prog.c', '');
var_dump($outputs);

$outputs = run('/tmp/marker/myProgram', '5', 10);
var_dump($outputs);


?>
