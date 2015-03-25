<?php

/* **************************************************************
 *  File: options_account.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_account extends BaseController
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
		$tpl->title = $translate->_("Options");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***disconnect***/
		unset($db);


		/***TEMPLATE ASSIGNMENTS***/

		//internationalization
		$_t = $translate->translateArray(array(
			"change_password" => "Change Password",
			"current_password" => "Current Password",
			"new_password" => "New Password",
			"repeat_password" => "Repeat Password",
			"submit" => "Submit",
		));
		$tpl->assign("_t", $_t);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* Save Password
*
* @access private
*/
	private function SavePassword()
	{
		if(!isset($_POST['old_password']) || !isset($_POST['new_password']))
			throw new Exception($translate->_('Password Empty'));

		/***database connect***/
		$db = DB::getInstance();

		//get translation
		$translate = Zend_Registry::get('translate');

		//get tpl
		$tpl = Zend_Registry::get('tpl');

		//get username
		$username = Authentification::GetUsername();
		$role = Authentification::GetRole();

		if(!$username || !$role)
			throw new Exception($translate->_("Authentification error"));

		//get current password and salt for user
		$currentdetails = $db->Row("SELECT `password`,`salt` FROM `bx_administrator` WHERE `username`=? AND `role`=?", array($username, $role));

		//compare passwords

		if(md5($currentdetails['salt'].$_POST['old_password']) == $currentdetails['password'])
		{
			//check for new password match
			if(empty($_POST['new_password'][0]) || empty($_POST['new_password'][1]))
			{
				throw new Exception($translate->_("Password Empty"));
			}
			//repeat password check
			elseif($_POST['new_password'][0] === $_POST['new_password'][1])
			{
				//save new password

				//renew salt and encoded password
				$salt = Session::CreateToken();
				if(!$db->Update("UPDATE `bx_administrator` SET `salt`=?,`password`=? WHERE `username`=? AND `role`=?", array($salt, md5($salt.$_POST['new_password'][0]), $username, $role)))
					throw new Exception ("Could not update the password");
				else
					$tpl->successmessage($translate->_('Done'));
			}
			else
				throw new Exception($translate->_('Password Mismatch'));
		}
		else
			throw new Exception ($translate->_('Wrong Current Password'));

		unset($_POST);

		Logger::AdminActivity('changed password', $username);

	} //SaveOptions

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//permission check
		Authentification::CheckPermission('administrator', 'editor');

		//get translation
		$translate = Zend_Registry::get('translate');

		//get tpl
		$tpl = Zend_Registry::get('tpl');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if(isset($_POST['submit'])) $this->SavePassword();
			}
			catch (Exception $e) {
				$tpl->errormessage($translate->_('Error').': '. $e->getMessage(). "<br>".PHP_EOL);
			}
		}

		$this->index();

	} //showIndex

} //class
