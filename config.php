<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

class settings {

    public static $temp;

}

settings::$temp = "/tmp/marker";

$user = "root";
$pass = "ipo123";
$host = "127.0.0.1";
$name = "marker";

$data = new mysqli($host, $user, $pass, $name);
if ($data->connect_errno) {
    echo "Failed to connect to MySQL: " . $data->connect_error;
}
?>
