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

}

settings::$temp = "/tmp/marker";
settings::$keep_files = true;
?>
