<?php

/* **************************************************************
 *  File: mailprocess.php
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
//				Authentification::check();
				if(!Authentification::Login())
				{
					echo Authentification::GetError();
					die();
				}
		/*--------------------------------------------------------------*/

$translate = new Translator("admin");
Zend_Registry::set('translate',$translate);


//if(@$_GET['securitytoken'] !== Session::GetToken())
//	die($translate->_('Security token mismatch'));

if('administrator' === Authentification::GetRole() || 'editor' === Authentification::GetRole())
{

	if(!isset($_GET['do'])) die();

	$mailqueue = new Mailqueue;

	if($_GET['do'] == 'init')
	{
		$mailqueue->Init();
	}

	if($_GET['do'] == 'create_queue')
	{
		//first run
		$mailqueue->setFrom(Config::GetDBOptions('email'));
		if($_GET['startat'] == 0)
			$mailqueue->Prepare();

		$mailqueue->Create_Queue();
	}

	if($_GET['do'] == 'run')
	{
		$mailqueue->Run();
	}

	if($_GET['do'] == 'success')
	{
		$mailqueue->Success();
	}


	/***database disconnect***/
	unset($db);

} //auth

?>