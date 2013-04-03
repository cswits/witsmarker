<?php
require_once("config.php");
require_once("lib.php");
echo "Hello";
$val = mark(0, '#include <stdio.h>
    int main(){printf("hellofdthdhfgt");}', 'test', 'Hello', 3);
echo "Val: $val";

/* $outputs = run("/var/www/mark",'gcc -o myProgram prog.c', '');
  var_dump($outputs);

  $outputs = run('/var/www/mark','./myProgram', '5', 10);
  var_dump($outputs);
 */
?>
