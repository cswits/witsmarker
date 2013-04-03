<?php
require_once("lib.php");

$descriptorspec = array(
0 => array('pipe', 'r'),  // stdin is a pipe that the child will read from
1 => array('pipe', 'w'),  // stdout is a pipe that the child will write to
2 => array('pipe', 'w')   // stderr is a pipe the child will write to
);
$execString = '/var/www/mark/test.sh'
$process = proc_open('/var/www/mark/test.sh ', $descriptorspec, $pipes);
if(!is_resource($process)) {
    throw new Exception('bad_program could not be started.');
}
//pass some input to the program
$lots_of_data = "7";
fwrite($pipes[0], $lots_of_data);
//close stdin. By closing stdin, the program should exit
//after it finishes processing the input
fclose($pipes[0]);

//do some other stuff ... the process will probably still be running
//if we check on it right away
$output='';
if (is_resource($process))
{
    while( ! feof($pipes[1]))
    {
        $return_message = fgets($pipes[1], 1024);
        if (strlen($return_message) == 0) break;

        $output .= $return_message;
        ob_flush();
        flush();
    }
}

killprocess($process);

echo "Output: $output";
?>
