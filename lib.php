<?php

function killprocess($process) {
    $status = proc_get_status($process);
    if ($status['running'] == true) { //process ran too long, kill it
        //close all pipes that are still open
        fclose($pipes[1]); //stdout
        fclose($pipes[2]); //stderr
        //get the parent pid of the process we want to kill
        $ppid = $status['pid'];
        //use ps to get all the children of this process, and kill them
        $pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);
        foreach ($pids as $pid) {
            if (is_numeric($pid)) {
                echo "Killing $pid\n";
                posix_kill($pid, 9); //9 is the SIGKILL signal
            }
        }

        proc_close($process);
    }
}

function run($program, $input, $limit = -1) {
    $descriptorspec = array(
        0 => array('pipe', 'r'), // stdin is a pipe that the child will read from
        1 => array('pipe', 'w'), // stdout is a pipe that the child will write to
        2 => array('pipe', 'w')  // stderr is a pipe the child will write to
    );

    if($limit == -1){
        $execString = $program;
    }else{
        $execString = getcwd() ."/timeout_runner.sh '$program' $limit";
    }
    $process = proc_open($execString, $descriptorspec, $pipes);
    if (!is_resource($process)) {
        throw new Exception('bad_program could not be started.');
    }
    //pass some input to the program
    fwrite($pipes[0], $input);
    //close stdin. By closing stdin, the program should exit
    //after it finishes processing the input
    fclose($pipes[0]);

    //do some other stuff ... the process will probably still be running
    //if we check on it right away
    $output = '';
    if (is_resource($process)) {
        while (!feof($pipes[1])) {
            $return_message = fgets($pipes[1], 1024);
            if (strlen($return_message) == 0)
                break;

            $output .= $return_message;
            ob_flush();
            flush();
        }
    }
    $stderr = '';
    if (is_resource($process)) {
        while (!feof($pipes[2])) {
            $return_message = fgets($pipes[2], 1024);
            if (strlen($return_message) == 0)
                break;

            $stderr .= $return_message;
            ob_flush();
            flush();
        }
    }
    killprocess($process);

    return array('stdout' => $output, 'stderr' => $stderr);
}
?>
