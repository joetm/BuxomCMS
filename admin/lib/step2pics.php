<?php

/* **************************************************************
 *  File: step2pics.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*** include the init.php file ***/
require_once '../../_init.php';

/*--------------------------------------------------------------*/
/*						Authentification						*/
/*--------------------------------------------------------------*/
		if(!Authentification::Login())
		{
			echo Authentification::GetError();
			die();
		}
/*--------------------------------------------------------------*/

$translate = new Translator("admin");
Zend_Registry::set('translate', $translate);

/***database connect***/
$db = DB::getInstance();

	//get temp data
	$temp = $db->FetchAll("SELECT `value` FROM `bx_temp` WHERE `key`='folder' OR `key`='internal_slug'");

	$input = array();
	$input['f'] = Input::clean($temp[0]['value'],'STR');
	$input['slug'] = Input::clean($temp[1]['value'],'FILENAME');

	unset($temp);

/***database disconnect***/
unset($db);

if (!isset($input['f']))
	die($translate->_('No folder specified'));
else
	$folder = $input['f'];

if (!isset($input['slug']))
	die($translate->_('No folder specified'));
else
	$internal_slug = $input['slug'];

$role = Authentification::GetRole();
if('administrator' === $role || 'editor' === $role)
{
	//ERROR HANDLING!









	try
	{
		$step2 = new Step2Process($folder, $internal_slug);
		$step2->run();
	}
	catch(Exception $e)
	{
		die($e->getMessage());
	}
}

?>