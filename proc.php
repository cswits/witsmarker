<?php
require_once("config.php");
require_once("lib.php");

$val = mark(5, '#include <stdio.h>
    int main(){printf("hellofdthdhfgt");}', 'test', 'Hello', 3);

var_dump($val);

?>
