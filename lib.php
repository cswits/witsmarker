<?php

/**
 * @file lib.php
 * General library routines.
 */
// Include global configurations
require_once("config.php");
	const output_max_length = 20000;
        const result_correct = 1;           ///< Correct Submission
        const result_incorrect = 11;        ///< Incorrect Submission
        const result_compile_error = 3;     ///< Compile Error
        const result_presentation_error = 7; ///< Presentation Error
        const result_time_limit = 10;       ///< Exceeded Time Limit

/**
 * Recursively delete a directory
 * @param string $dir Directory to Delete
 * @return boolean Success/Failure
 */
function deleteDirectory($dir) {
    // If the folder/file doesn't exist return
    if (!file_exists($dir))
        return true;
    // If it isn't a directory, remove and return
    if (!is_dir($dir) || is_link($dir))
        return unlink($dir);
    // For each item in the directory
    foreach (scandir($dir) as $item) {
        // Ignore special folders
        if ($item == '.' || $item == '..')
            continue;
        // Recursively delete items in the folder
        if (!deleteDirectory($dir . "/" . $item)) {
            //chmod($dir . "/" . $item, 0777);
            if (!deleteDirectory($dir . "/" . $item))
                return false;
        };
    }
    return rmdir($dir);
}

/**
 * Object representing a source code file.
 */
class program_file {

    public $path;       ///< Folder containing the file
    public $filename;   ///< Filename without extension
    public $extension;  ///< Extension, based on the language
    public $fullname;   ///< Absolute Path and Filename
    public $sourcefile; ///< Replaces sourcefile in commands
    public $id;         ///< Submission ID
    public $commands;   ///< Commands with Keywords Replaced
    public $tests;       ///< Tests with Keywords Replaced 
    public $timelimit;   ///< Timelimit. Currently only used by the matlab marker   

    /**
     * Constructor
     * @param array $lang Array containing information about the Language
     * @param string $sourcecode All the sourcecode to be written to the file
     * @param string $input Optional Input data to be written to file
     */

    function program_file($lang, $sourcecode, $timelimit, $input = "") {
        // Get filename extension from $lang
        $this->extension = $lang['extension'];
        // All files are called source
        $this->filename = "source";
        $this->sourcefile = "$this->filename.$this->extension";
        $this->timelimit=$timelimit;

        // Get the Submission ID
        $this->id = uniqid("", $more_entropy = true);

        // Construct the path
        $this->path = settings::$temp;
        $this->path = "$this->path/$this->extension/$this->id";
        // Construct the full path/file
        $this->fullname = "$this->path/$this->filename.$this->extension";

        // Create the folder
        mkdir($this->path, 0777, $recursive = true);
        // Save the code
        file_put_contents($this->fullname, $sourcecode);
        file_put_contents($this->path . "/input", $input);

        // setup commands
        $this->commands = $this->setup_commands($lang['commands']);
        $this->tests = $this->setup_commands($lang['tests']);
    }

    /**
     * Iterates through commands from the language description and replaces 
     * keywords with the relevant paths
     * @param array $comm Array of commands
     * @return Array of commands with keywords replaced
     */
    function setup_commands($comm) {
        $temp = $comm;
        foreach ($temp as $key => $value) {
            $value = str_replace("~sourcefile~", $this->sourcefile, $value);
            $value = str_replace("~sourcefile_noex~", $this->filename, $value);
            $value = str_replace("~input~", "input", $value);
            $value = str_replace("~markers~", getcwd(), $value);
            $value = str_replace("~path~", $this->path, $value);
            $value = str_replace("~timeout~", $this->timelimit, $value);
            $temp[$key] = $value;
        }
        return $temp;
    }

    /**
     * Destructor deletes the relevant directory unless settings::$keep_files is
     * set to true.
     */
    function __destruct() {
        if (!settings::$keep_files) {
            deleteDirectory($this->path);
        }
    }

}

/**
 * Kill a process and all of its children. 
 * TODO: This function needs some testing with regards to programs
 * with threads and/or forks.
 * Is this code necessary if the bash script killer runs?
 * @param int $process PID of the process to kill
 * @return int exit code of the process
 */
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

function mark_log($text){
    file_put_contents(settings::$temp."/log.txt", $text);
}

/**
 * Runs a program with a timelimit and input.
 * @param string $path  Working directory of the program. 
 *      The system cd's to this path before running the program.
 * @param type $program The program within $path that should execute
 * @param type $input   Input to the program on stdin
 * @param type $limit   Optional Time limit in seconds
 * @return Array containing stdout, stderr and exit code (result).
 * @throws Exception if the program cannot be started.
 */
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
    $len = strlen($output);
    if ($len>output_max_length)
    	$output = substr($output, output_max_length);
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
    $len = strlen($stderr);
    if ($len>output_max_length)
    	$stderr = substr($stderr, output_max_length);

    $res = killprocess($process);

    return array('stdout' => $output, 'stderr' => $stderr, "result" => $res, "exec" => $execString);
}

/**
 * The main marking function. This is called from the webservice, saves the
 * source code, runs the commands and checks the output.
 * @param int $language    Language ID found in the languages.json file.
 * @param string $sourcecode  The source code/binary the program.
 * @param string $input   Input to the program on STDIN and the input file.
 * @param string $output  The expected output of the program on STDOUT.
 * @param int $timelimit   Time limit for the "run" command.
 * @return string array containing STDERR, STDOUT and the result.
 */
function mark($language, $sourcecode, $input, $output, $timelimit) {
    $string = file_get_contents("languages.json");
    $languages = json_decode($string, true);

    $lang = $languages[$language];
    $code = new program_file($lang, $sourcecode, $timelimit, $input);

    foreach ($code->commands as $key => $command) {
        //$runner = (strpos($key, 'run')==0);
        $runner = (($key=="run")||(strpos($key, "time")===0));
        if ($runner) {
            $outputs = run($code->path, $command, $input, $timelimit);
            if(strpos($outputs["stderr"], 'Time limit exceeded') != FALSE){
                break;
            }
            
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

    if ($runner) {
        if(strpos($outputs["stderr"], 'Time limit exceeded') != FALSE){
            $outputs["result"] = result_time_limit;
        } else {
            $output = str_replace("\r", "", $output);
            $outputs['stdout'] = str_replace("\r", "", $outputs['stdout']);

            $outputs["result"] = test_output($output, $outputs['stdout']);
            $outputs["modelout"] = trim($output);
            $outputs["progout"] = trim($outputs['stdout']);
        }
    } else {
        $outputs["result"] = result_compile_error;
    }
    return $outputs;
}

/**
 * Compare ideal and program outputs. Checks for an exact match 
 * then for presentation errors.
 * @param string $correct Ideal output.
 * @param string $progoutput Program output.
 * @return int result code.
 */
function test_output($correct, $progoutput) {
    $correct = trim($correct);
    $progoutput = trim($progoutput);
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
