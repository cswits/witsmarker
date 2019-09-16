<?php

/**
 * @file lib.php
 * General library routines.
 */
// Include global configurations
require_once("configPrototype.php");
const output_max_length = 20000;
const result_correct = ASSIGNFEEDBACK_WITSOJ_STATUS_ACCEPTED;        		///< Correct Submission
const result_incorrect = ASSIGNFEEDBACK_WITSOJ_STATUS_INCORRECT;     		///< Incorrect Submission
const result_compile_error = ASSIGNFEEDBACK_WITSOJ_STATUS_COMPILEERROR;     	///< Compile Error
const result_presentation_error = ASSIGNFEEDBACK_WITSOJ_STATUS_PRESENTATIONERROR; ///< Presentation Error
const result_time_limit = ASSIGNFEEDBACK_WITSOJ_STATUS_TIMELIMIT;       	///1< Exceeded Time Limit
const result_marker_error = ASSIGNFEEDBACK_WITSOJ_STATUS_MARKERERROR;	    	///< Marker Error
const result_mixed = ASSIGNFEEDBACK_WITSOJ_STATUS_MIXED;	    		///< Submission has been graded
const result_runtime=ASSIGNFEEDBACK_WITSOJ_STATUS_RUNTIMEERROR;
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
//https://hotexamples.com/examples/-/-/recurse_copy/php-recurse_copy-function-examples.html
function recurse_copy($source, $dest)
{
	// Check for symlinks
	if (is_link($source)) {
		return symlink(readlink($source), $dest);
	}
	// Simple copy for a file
	if (is_file($source)) {
		return copy($source, $dest);
	}
	// Make destination directory
	if (!is_dir($dest)) {
		mkdir($dest);
	}
	// Loop through the folder
	$dir = dir($source);
	while (false !== ($entry = $dir->read())) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		// Deep copy directories
		recurse_copy("{$source}/{$entry}", "{$dest}/{$entry}");
	}
	// Clean up
	$dir->close();
	return true;
}
?>