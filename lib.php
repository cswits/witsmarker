<?php

require_once("config.php");

        const result_correct = 1;
        const result_incorrect = 11;
        const result_compile_error = 3;
        const result_presentation_error = 7;
        const result_time_limit = 10;

/**
 * Object representing a source code file.
 */
class program_file {

    public $path;       ///< Folder containing the file
    public $filename;   ///< Filename without extension
    public $extension;  ///< Extension, based on the language
    public $fullname;   ///< Absolute Path and Filename
    public $sourcefile; ///< Replaces sourcefile in commands
    public $id;         ///< Submission ID from Database
    public $commands;   ///< Commands with Keywords Replaced
    public $tests;       ///< Tests with Keywords Replaced    

    /**
     * Constructor
     * @global mysqli $data Connection to the Database
     * @param type $lang Array containing information about the Language
     * @param string $sourcecode All the sourcecode to be written to the file
     */

    function program_file($lang, $sourcecode, $input = "") {
        global $data;

        // Get filename extension from $lang
        $this->extension = $lang['extension'];
        // All files are called source
        $this->filename = "source";
        $this->sourcefile = "$this->filename.$this->extension";

        // Create a submission in the database
        $query = $data->prepare("INSERT INTO submissions (status) VALUES (0)");
        $query->execute();

        // Get the Submission ID
        $this->id = $query->insert_id;
        // Construct the path
        $this->path = settings::$temp;
        $this->path = "$this->path/$this->extension/$this->id";
        // Construct the full path/file
        $this->fullname = "$this->path/$this->filename.$this->extension";

        // Create the folder
        mkdir($this->path, 0777, $recursive = true);
        // Save the code
        file_put_contents($this->fullname, $sourcecode);
        file_put_contents($this->path."/input", $input);

        // setup commands
        $this->commands = $this->setup_commands($lang['commands']);
        $this->tests = $this->setup_commands($lang['tests']);
    }

    function setup_commands($comm) {
        $temp = $comm;
        foreach ($temp as $key => $value) {
            $value = str_replace("~sourcefile~", $this->sourcefile, $value);
            $value = str_replace("~sourcefile_noex~", $this->filename, $value);
            $value = str_replace("~input~", "input", $value);

            $temp[$key] = $value;
        }
        return $temp;
    }

}

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
                posix_kill($pid, 9); //9 is the SIGKILL signal
            }
        }

        return proc_close($process);
    }
}

function run($path, $program, $input, $limit = -1) {
    $descriptorspec = array(
        0 => array('pipe', 'r'), // stdin is a pipe that the child will read from
        1 => array('pipe', 'w'), // stdout is a pipe that the child will write to
        2 => array('pipe', 'w')  // stderr is a pipe the child will write to
    );

    if ($limit == -1) {
        $execString = "cd $path; $program";
    } else {
        $execString = getcwd() . "/timeout_runner.sh '$path' '$program' $limit";
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
    $res = killprocess($process);

    return array('stdout' => $output, 'stderr' => $stderr, "result" => $res);
}

function mark($language, $sourcecode, $input, $output, $timelimit) {
    $string = file_get_contents("languages.json");
    $languages = json_decode($string, true);

    $lang = $languages[$language];
    $code = new program_file($lang, $sourcecode, $input);

    foreach ($code->commands as $key => $command) {
        if ($key == "run") {
            $outputs = run($code->path, $command, $input, $timelimit);
        } else {
            $outputs = run($code->path, $command, $input);
        }
        if (array_key_exists($key, $code->tests)) {
            $filename = $code->path . "/" . $code->tests[$key];
            if (!file_exists($filename)) {
                break;
            }
        }
    }

    if ($key == "run") {
        if ($outputs["stderr"] == "Time limit exceeded") {
            $outputs["result"] = result_time_limit;
        }else{
            $outputs["result"] = test_output($output, $outputs['stdout']);
        }
    }else{
        $outputs["result"] = result_compile_error;
    }
    return $outputs;
}

function test_output($correct, $progoutput) {
    if ($correct == $progoutput) {
        return result_correct;
    }

    $correct = strtolower($correct);
    $correct = str_replace(" ", "", $correct);
    $correct = str_replace("\t", "", $correct);
    $correct = str_replace("\n", "", $correct);
    
    $progoutput = strtolower($progoutput);
    $progoutput = str_replace(" ", "", $progoutput);
    $progoutput = str_replace("\t", "", $progoutput);
    $progoutput = str_replace("\n", "", $progoutput);

    if ($correct == $progoutput) {
        return result_presentation_error;
    } else {
        return result_incorrect;
    }
}

?>
