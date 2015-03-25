<?php

/* **************************************************************
 *  File: activitylog.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class activitylog extends BaseController
{
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
		$tpl->title = $translate->_("Activity Log");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***disconnect***/
		unset($db);

		//internationalization
		$_t = $translate->translateArray(array(
			"action" => "Action",
			"clearhistory" => "Clear History",
			"IP" => "IP",
			"date" => "Date",
			"link" => "Link",
			"username" => "Username",
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
		//get translation
		$translate = Zend_Registry::get('translate');

			/***database connect***/
			$db = DB::getInstance();

			$db->Update("DELETE FROM `bx_administrator_activitylog`
					WHERE `action` NOT IN ('logout', 'login', 'failed login')");

		unset($_POST);

	} //deletion

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//permission check
		//editors do not have access to options
		Authentification::CheckPermission('administrator');

		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				# delete list
				if (isset($_POST['_action']) && $_POST['_action'] == 'delete')
					$this->deletion();

			}
			catch (Exception $e) {
					echo $translate->_("Error").': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();

	} //showIndex

} //class
