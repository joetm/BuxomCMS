<?php

/* **************************************************************
 *  File: slug_check.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*
 * check if slug exists
 */

//error_reporting(0);

/*** include the init.php file ***/
require '../../_init.php';

		/*--------------------------------------------------------------*/
		/*						Authentification						*/
		/*--------------------------------------------------------------*/
//				Authentification::check();
				if(!Authentification::Login())
				{
					echo Authentification::GetError();
					die();
				}
				if('administrator' !== Authentification::GetRole() && 'editor' !== Authentification::GetRole())
					die('auth error');
		/*--------------------------------------------------------------*/

$translate = new Translator("admin");

if(@$_POST['securitytoken'] !== Session::GetToken())
	die($translate->_('Security token mismatch'));

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

		$response = array(); //'suggestion' => false
		$foundindb = false;
		$memberpath = false;
		$freepath = false;
		$thumbnailpath = false;

		$input = Input::clean_array('p', array(
					'type' => 'STR',
					'slug' => 'FILENAME'
					));

		$input['slug'] = String::Slash($input['slug'], 1, 0);

		//table
		//and
		//create the correct paths
		$table = false;

		$types = Config::Get('types');

		if(isset($types[$input['type']])) //(Path::Get($input['type']))
		{
			$type = $types[$input['type']]['name']; //Path::Get($input['type']);
			$thumbnailpath = Path::Get('path:thumbs'). '/'. $types[$input['type']]['folder'] . String::Slash($input['slug'],1,0);
		}
		else
		{
			$type = false;
			$thumbnailpath = false;
		}
/*
		switch($input['type'])
		{
			case 'model':
				$type = 'model';
				$thumbnailpath = Path::Get('path:thumbs/models') . String::Slash($input['slug'],1,0);
			break;
			case 'picture set':
				$type = 'pictureset';
				$thumbnailpath = Path::Get('path:thumbs/pics') . String::Slash($input['slug'],1,0);
			break;
			case 'video set':
				$type = 'videoset';
				$thumbnailpath = Path::Get('path:thumbs/videos') . String::Slash($input['slug'],1,0);
			break;
			default:
				$type = false;
				$thumbnailpath = false;
			break;
		}
*/

		if(!$type || !$thumbnailpath) die('error');


		/***database connect***/
		$db = DB::getInstance();

		//check the database
		$dbslug = $db->Row("SELECT `id`, `memberpath`, `freepath`
				FROM `bx_content`
				WHERE `slug` = ?
				AND `type` = ?", array($input['slug'], $type));

		$response['foundindb'] = ($dbslug ? 1 : 0);


		if(file_exists("$thumbnailpath"))
			$dir = true; //slug exists
		else
			$dir = false; //slug does not exist

		//additional checks maybe in the future...

		$response['direxists'] = ($dir ? 1 : 0);

		//create a suggestion
		if($response['direxists'])
		{
			$input['slug'] = String::Slash($input['slug'],0,0);
			$response['suggestion'] = $db->unique_slug($input['slug'], $type);
		}

		echo json_encode($response);
		exit();

} //request_method == post

die('error');

?>