<?php

/**
 * @file config.php
 * Global configurations. This file is included in all scripts.
 */
// Error reporting/warning must be off for the web service to work
//  - they interfere with sending JSON strings.
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 * Statically wraps around global variables
 */
class settings {

    public static $temp;        ///< Prefix for temp folder
    public static $keep_files;  ///< Delete folders when the marker completes
    public static $testcases; ///< Folder to store downloaded test cases
    public static $auth_token; ///< Folder to store downloaded test cases
}

settings::$temp = "/tmp/marker2";
settings::$keep_files = true;
settings::$testcases = "/tmp/marker2/testcases";
settings::$auth_token = array("customfeedback_token" => "1e6947ac7fb3a9529a9726eb692c8cc5", "customfeedback_name" => "1710409.ms.wits.ac.za");

define('ASSIGNFEEDBACK_WITSOJ_STATUS_PENDING', 0);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_JUDGING', 1);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_COMPILEERROR', 2);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_PRESENTATIONERROR', 3);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_ACCEPTED', 4);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_MIXED', 5);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_INCORRECT', 6);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_MARKERERROR', 7);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_TIMELIMIT', 8);
define('ASSIGNFEEDBACK_WITSOJ_STATUS_RUNTIMEERROR', 12);



?>
