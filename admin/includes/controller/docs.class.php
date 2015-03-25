<?php

/* **************************************************************
 *  File: docs.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class docs extends BaseController
{
	private static $errors = '';
	private static $errorcss = array();
	private static $success = false;

	private static $token = false;

/**
* index
*
* @access private
*/
	private function index()
	{
		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

		/***page setup***/
		$tpl->title = $translate->_("2257 Documents");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***disconnect***/
		unset($db);

		//error/success
		if (self::$success) 	$tpl->successmessage($translate->_("Done"));
		if (self::$errors) 	$tpl->errormessage(self::$errors);
		if (self::$errorcss)	$tpl->assign("errorcss", self::$errorcss);

		//internationalization
		$_t = $translate->translateArray(array(
			"delete" => "Delete",
			"updates" => "Updates",
			"model" => "Model",
			"notes" => "Notes",
		));
		$tpl->assign("_t", $_t);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* deletion
*
* @access private
*/
	private function deletion()
	{
		//warning: 2257 record deletion could lead to inconsistencies with the updates.

		//get translation
		$translate = Zend_Registry::get('translate');

			/***database connect***/
			$db = DB::getInstance();

			$del_ids = Input::GetCheckBoxes();

			if($del_ids)
			{
				$db->Update("DELETE FROM `bx_2257` WHERE `id` IN (".$del_ids.")");
				self::$success = true;
				Logger::AdminActivity('deleted 2257 record(s)','success');
			}
			else
				self::$errors = 'No 2257 entry selected.';

		unset($_POST);

	} //deletion

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try
			{
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if(isset($_POST['_action']) && Authentification::CheckPermission('administrator', 'editor'))
				{
					# delete
						if($_POST['_action'] == 'delete') $this->deletion();
				}
			}
			catch (Exception $e)
			{
				echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();

	} //showIndex

} //class
