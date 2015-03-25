<?php

/* **************************************************************
 *  File: email.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*
 * initialize the email process
 */

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
		/*--------------------------------------------------------------*/

$translate = new Translator("admin");

	if($_SERVER['REQUEST_METHOD'] == 'POST' && Session::GetToken() === @$_POST['securitytoken'])
	{

		$type = 'text';
		if($_POST['type'] == 'html') $type = 'html';

		$members = '';
		switch($_POST['members'])
		{
			case 'all':
				$members = 'all';
				break;
			case 'inactive':
				$members = 'inactive';
				break;
			case 'active':
			default:
				$members = 'active';
				break;
		}

		$subject = __sitename; //default value to never send out emails without subject
		if($_POST['subject']) $subject = addslashes(Input::clean_single('p', 'subject', 'NOHTML'));

		if($type == 'html')


		//ADDSLASHES!!!

			$body = addslashes(Input::clean_single('p', 'body', 'STR'));
		else


		//ADDSLASHES!!!

			$body = addslashes(strip_tags(Input::clean_single('p', 'body', 'NOHTML')));









		/***database connect***/
		$db = DB::getInstance();

			$where = "";
			switch($members)
			{
				case 'inactive':
				case 'chargeback':
				case 'active':
					$where = $members;
					break;
				default:
					$where = "active";
					break;
			}

			//maximum number of emails to send
			$countrows = $db->Column("SELECT COUNT(*)
							FROM `bx_member_email` AS `e`
							JOIN `bx_member` AS `m`
							USING (`id`)
							WHERE m.status = ?
							AND e.no_emails != '1'", array($where));

			//abort if no emails in database
			if(!$countrows) die('error');


			//write body etc. to database
/*
			$sql = "INSERT INTO `bx_temp` (`subject`,`mailbody`,`type`,`members`,`max`) VALUES ('".$subject."','".$body."','".$type."','".$members."','".$countrows."')";
*/
			$status = $db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES ('subject',?) ON DUPLICATE KEY UPDATE `value`=?", array($subject, $subject));
			//if(!$status) die("Could not write to database.");

			$status = $db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES ('mailbody',?) ON DUPLICATE KEY UPDATE `value`=?", array($body, $body));
			//if(!$status) die("Could not write to database.");

			$status = $db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES ('type',?) ON DUPLICATE KEY UPDATE `value`=?", array($type, $type));
			//if(!$status) die("Could not write to database.");

			$status = $db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES ('members',?) ON DUPLICATE KEY UPDATE `value`=?", array($members, $members));
			//if(!$status) die("Could not write to database.");

			$status = $db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES ('max',?) ON DUPLICATE KEY UPDATE `value`=?", array($countrows, $countrows));
			//if(!$status) die("Could not write to database.");

		/***database disconnect***/
		unset($db);

		echo 'success: '.$countrows.' emails (lib/email.php)';

	}
	else
		die($translate->_('Security token mismatch'));

?>