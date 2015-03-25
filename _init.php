<?php

/* **************************************************************
 *  File: _init.php
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/


/*THIS FILE WILL BE ENCRYPTED???*/

/*** define the paths ***/
define ('__sitepath', 	realpath(dirname(__FILE__)) );

//version
@include_once __sitepath."/includes/version.php";

if(!defined('DIRECTORY_SEPARATOR'))
	define('DIRECTORY_SEPARATOR', '/');
//shorter alias
define('DIR_SEP', DIRECTORY_SEPARATOR);

if (!defined('PHP_EOL'))
	define('PHP_EOL', (DIRECTORY_SEPARATOR == '/') ? "\n" : "\r\n");

define("TIME_NOW", time());

//template directory for controller php-files
define("_controller_dir", "controller");

/*--------------------------------------------------------------*/
/*                      Configuration Array                     */
/*--------------------------------------------------------------*/

/***get configuration***/
require "includes/classes/Config.class.php";
$config = new Config;

/*--------------------------------------------------------------*/
/*                            Paths                             */
/*--------------------------------------------------------------*/

/**
* Sanitize the paths from _config.php and turn them into constants
*
*/

require_once(__sitepath."/includes/classes/String.class.php");


# absolute path to admin directory
define ('__adminpath',	__sitepath . DIR_SEP . String::Slash(Config::Get('__admindir'), 0, 0));


/*---*/

// seo urls
//must match the rewrite rules in htaccess file
//!!!!!!!





/*--------------------------------------------------------------*/
/*                            Misc                              */
/*--------------------------------------------------------------*/

//create empty db object
$db = NULL;

//base admin controller class
require_once __sitepath.'/includes/classes/BaseController.class.php';

/*--------------------------------------------------------------*/
/*                         Autoloader                           */
/*--------------------------------------------------------------*/

/**
* Autoloader Path Setup Helper Function
*
*/
function pathsetup($className)
{
		//Directories added here must be
		//relative to the script going to use this file.
		//New entries can be added to this list
		$directories = array(
			__sitepath  . '/includes/classes/',
			__sitepath  . '/includes/classes/Zend/',
			__adminpath . '/includes/classes/',
			__adminpath . '/includes/controller/'
		);

		//file naming formats
		$fileNameFormats = array(
			'%s.class.php',
			'%s.php'
		);

		foreach($directories as $directory){
			foreach($fileNameFormats as $fileNameFormat){
				$path = $directory.sprintf($fileNameFormat, $className);
				if(file_exists($path)){
					require_once $path;
					return;
				}
			}
		}
} //pathsetup

/**
* Autoloading
*
*/
if( function_exists('spl_autoload_register') )
{
	function BXautoLoader($className){
		pathsetup($className);
	}
	spl_autoload_register('BXautoLoader');
}
else
{
	function __autoload($className)
	{
		pathsetup($className);
	}
}


/*--------------------------------------------------------------*/
/*						License Check							*/
/*--------------------------------------------------------------*/

/*
if(isset($_SERVER['HTTP_HOST'])){

	//script copyright:
	//-> eine wichtige Datei verschluesseln und diesen Code darin platzieren

	//$_SERVER['HTTP_HOST'] (both www und non-www)
	//$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']); //von IP

	$host = $_SERVER['HTTP_HOST'];
	//filter www:
	$host = str_replace('www.','',$host);
	if($host != 'mysite.com'){
		//send http request to licensing server; log it on licensing server


		if( function_exists('fopen') && function_exists('fclose') )
		{
			$fp = @fopen('http://licenseserver.com/index.php', 'r');
			@fclose($fp);
		}
		elseif( function_exists('file_get_contents') )
		{

			//file_get_contents + empty file

			$f = file_get_contents('http://licenseserver.com/index.php');

			unset($f);

		}
		elseif( function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec') && function_exists('curl_close') )
		{
			//use cURL

			$ch = @curl_init("http://licenseserver.com/");

//			@curl_setopt($ch, CURLOPT_HEADER, false);
			@curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			@curl_setopt($ch, CURLOPT_FAILONERROR, true);
			@curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
			@curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			@curl_setopt($ch, CURLOPT_HTTPGET, true);
			@curl_setopt($ch, CURLOPT_MUTE, true);
			@curl_setopt($ch, CURLOPT_NOBODY, true);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$str = curl_exec($ch);
			@curl_close($ch);

			unset($ch);
			unset($str);

		}


	}
	unset($host);

}
else{
	//other solution here (if available)

}
*/





/*--------------------------------------------------------------*/
/*						Zend Includes							*/
/*--------------------------------------------------------------*/

/***Zend config***/
//set Zend include path for all those nasty Zend files.
$zend_path = __sitepath . "/includes/classes/"; //includes/classes/ + Zend/...
// Include Pfad setzen, bzw. um neuen Include Pfad erweitern
set_include_path($zend_path. PATH_SEPARATOR . get_include_path());

/*--------------------------------------------------------------*/
/*						Registry Array							*/
/*--------------------------------------------------------------*/

/***registry***/
//registry holds objects that need to be accessed globally.
//Zend registry implements singleton pattern
//USAGE:
//set registry items
//Zend_Registry::set('key', $obj);
//get items from Registry:
//$obj = Zend_Registry::get('key');

require_once __sitepath."/includes/classes/Zend/Registry.php";
$registry = new Zend_Registry(); //class Zend_Registry
Zend_Registry::setInstance($registry); //singleton

/*--------------------------------------------------------------*/
/*			Config Defaults and Autoload Database Options		*/
/*--------------------------------------------------------------*/

//some default configuration
Config::Defaults();
//autoload config options
Config::Autoload();

/*--------------------------------------------------------------*/
/*						Error Handling							*/
/*--------------------------------------------------------------*/

/*
if (Config::Get('debug')) {
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

    set_error_handler ('debug_error_handler');
}
else {
    set_error_handler ('production_error_handler');
}
*/

/*--------------------------------------------------------------*/
/*				CACHING INIT							*/
/*--------------------------------------------------------------*/

//caching
//everything from hereon will be cached if __pagecache != false
if(__pagecache) Caching::Setup('page');

/*--------------------------------------------------------------*/
/*                           Request                            */
/*--------------------------------------------------------------*/

//strip slashes if magic quotes is on
Request::Setup();

/*--------------------------------------------------------------*/
/*			Locales			 		*/
/*--------------------------------------------------------------*/

require_once "Zend/Locale.php";
$default_locale = Config::Get('default_locale');
if($default_locale && $default_locale != false)
{
	Zend_Locale::setDefault($default_locale);
	$locale = new Zend_Locale(Config::Get('l10n_detection'));
	Zend_Registry::set('Zend_Locale', $locale);
}