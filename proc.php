<?php
require_once("lib.php");
$outputs = run('gcc -o /var/www/mark/myProgram /var/www/mark/prog.c', '');
var_dump($outputs);

$outputs = run('/var/www/mark/myProgram', '5', 10);
var_dump($outputs);


?>
