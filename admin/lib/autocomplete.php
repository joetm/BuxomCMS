<?php

/* **************************************************************
 *  File: autocomplete.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

/*** include the init.php file ***/
require '../../_init.php';

	/*--------------------------------------------------------------*/
	/*						Authentification						*/
	/*--------------------------------------------------------------*/
			//Authentification::check();
			if(!Authentification::Login())
			{
				echo Authentification::GetError();
				die();
			}
			if('administrator' !== Authentification::GetRole() && 'editor' !== Authentification::GetRole())
				die('auth error');
	/*--------------------------------------------------------------*/

$translate = new Translator("admin");

if(@$_GET['securitytoken'] !== Session::GetToken())
	die($translate->_('Security token mismatch'));


//$term = substr(strrchr(Input::clean_single('r', 'q', 'NOHTML'), '/'), 1);

$term = String::Slash(Input::clean_single('g', 'q', 'STR'), 0, 0);
//$type = Input::clean_single('g', 'type', 'STR');


function smartstripslashes($str) {
  $cd1 = substr_count($str, "\"");
  $cd2 = substr_count($str, "\\\"");
  $cs1 = substr_count($str, "'");
  $cs2 = substr_count($str, "\\'");
  $tmp = strtr($str, array("\\\"" => "", "\\'" => ""));
  $cb1 = substr_count($tmp, "\\");
  $cb2 = substr_count($tmp, "\\\\");
  if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2) {
    return strtr($str, array("\\\"" => "\"", "\\'" => "'", "\\\\" => "\\"));
  }
  return $str;
}

$base = smartstripslashes(Input::clean_single('g', 'base', 'STR'));
if(!$base) die('invalid base');

$base = dirname($base);

function _get_contents($path, $relativepath = '', $discardsearch = false)
{
	global $term;

	$contents = array_slice(scandir($path), 2);

	foreach($contents as $value)
	{
		if(!is_dir($path.DIR_SEP.$value))
			continue;

		if($value == '.' || $value == '..')
			continue;

		//skip some directories
		if($value == 'admin' || $value == 'language' || $value == 'includes')
			continue;

		if( strpos($value, $term) === 0 )
		{
			echo $relativepath . $value . "\n";

			//scan subdir
			_get_contents($path. DIR_SEP .$value, $value. DIR_SEP, true);
		}
		elseif($discardsearch)
			echo $relativepath . $value . "\n";

	} //foreach
} //function


//$the_path = Path::Get("path:member/$type"); //path:member/set => /rootpath/member/pics
//if(!$the_path) die("invalid path");

$contents = _get_contents($base);
