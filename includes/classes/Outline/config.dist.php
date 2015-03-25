<?php

/*
This is the Template configuration file for the Outline template engine.
*/

// Root path of your Outline installation
define("OUTLINE_SYSTEM_PATH", dirname(__FILE__));

// Root path of your application
//not used
define("OUTLINE_SCRIPT_PATH", OUTLINE_SYSTEM_PATH . "/app");

// Path to Outlines system classes
define("OUTLINE_CLASS_PATH",  OUTLINE_SYSTEM_PATH . "/class");

// If set, displays various debugging messages during load/compile
define("OUTLINE_DEBUG", false);

// If set, compiles templates unconditionally, on every run. Setting this to true will speed up page load by about 5 times.
define("OUTLINE_ALWAYS_COMPILE", false);

// * Default OutlineEngine configuration settings:

// Path to folder containing templates
//not used
define("OUTLINE_TEMPLATE_PATH", "../../../templates");

// Folder containing compiled templates
define("OUTLINE_COMPILED_PATH", __CACHE_PATH . '/templates'); //old: OUTLINE_SYSTEM_PATH . "/compiled");

// suffix for cache files
define("OUTLINE_CACHE_SUFFIX", ".html");

// Default cache time (in seconds)
define("OUTLINE_CACHE_TIME", 0); //60*60*24

//get permission defaults
$fileperms = Config::Get('__filePermission');
if(empty($fileperms)) $fileperms = 0777;
$folderperms = Config::Get('__folderPermission');
if(empty($folderperms)) $folderperms = 0777;

// Permission flag for created files
define("OUTLINE_FILE_MODE", $fileperms);

// Permission flag for directories
define("OUTLINE_DIR_MODE", $folderperms);


// * Debug function:

function OutlineDebug($msg) {
	echo "<div style=\"color:#980000\"><strong>Template</strong>: $msg</div>";
}

// * Load the engine and modifiers:

require_once OUTLINE_CLASS_PATH . "/engine.php";
require_once OUTLINE_CLASS_PATH . "/modifiers.php";
