<?php

/* **************************************************************
 *  File: step2video.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*** include the init.php file ***/
require_once '../../_init.php';

/*--------------------------------------------------------------*/
/*                       Authentification                       */
/*--------------------------------------------------------------*/
	if(!Authentification::Login())
	{
		echo Authentification::GetError();
		die();
	}
	$role = Authentification::GetRole();
	if('administrator' !== $role && 'editor' !== $role)
		die('auth error');
/*--------------------------------------------------------------*/

$translate = new Translator("admin");
Zend_Registry::set('translate', $translate);

/***database connect***/
$db = DB::getInstance();

//get temp data
$input = array();

$tempdata = $db->FetchAll("SELECT `value` FROM `bx_temp`
			WHERE `key`='folder'
			OR `key`='internal_slug'
			OR `key`='updateid'");

$input['folder'] = Input::clean($tempdata[0]['value'], 'NOHTML');
//ex.: testfolder
$input['internal_slug'] = Input::clean($tempdata[1]['value'],'FILENAME');
//ex.: sfsdfg
$input['updateid'] = Input::clean($tempdata[2]['value'],'FILENAME');

unset($tempdata);

if (empty($input['folder']))
	die('No folder specified');

if (empty($input['internal_slug']))
	die($translate->_('Invalid slug'));

if (empty($input['updateid']))
	die("Invalid Update ID");


try
{
	$step2 = new Step2Process($input['folder'], $input['internal_slug'], 'video');
	$step2->run();
}
catch(Exception $e)
{
	die($e->getMessage());
}
