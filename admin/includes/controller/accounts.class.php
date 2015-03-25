<?php

/* **************************************************************
 *  File: accounts.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class accounts extends BaseController
{

	private static $errors = '';
	private static $errorcss = array();
	private static $success = false;

	private static $token;

	private static $account = false;

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
		$tpl->title = $translate->_("Accounts");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			//get admin accounts
			$users = $db->FetchAll("SELECT `username`, `email`, `name`, `role`
						FROM `bx_administrator`");

		/***disconnect***/
		unset($db);

		$tpl->assign("users", $users);

		//error/success
		if (self::$success) $tpl->successmessage($translate->_("Done"));
		if (self::$errors) 	$tpl->errormessage(self::$errors);
		if (self::$errorcss)$tpl->assign("errorcss", self::$errorcss);

		//internationalization
		$_t = $translate->translateArray(array(
			"addnewaccount" => "Add New Account",
			"delete" => "Delete",
			"email" => "Email",
			"name" => "Name",
			"optional" => "Optional",
			"password" => "Password",
			"role" => "Role",
			"username" => "Username",
		));
		$tpl->assign("_t", $_t);

		//prefill
		if(self::$account) $tpl->assign("account", self::$account);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* addnew
*
* @access private
*/
	private function addnew()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

			self::$account = Input::clean_array('p',array(
				"accountpassword" => "STR",	//cannot be password
				"accountusername" => "NOHTML",	//cannot be username
				"email" => "NOHTML",
				"name" => "NOHTML",
				"role" => "NOHTML",
			));

			if(empty(self::$account['accountusername']))
			{
				self::$errors .= "Missing username<br>";
				self::$errorcss['accountname'] = " error";
			}
			elseif(empty(self::$account['accountpassword']))
			{
				self::$errors .= "Missing password<br>";
				self::$errorcss['accountpassword'] = " error";
			}
			elseif(empty(self::$account['email']))
			{
				self::$errors .= "Missing email<br>";
				self::$errorcss['email'] = " error";
			}
			elseif(empty(self::$account['role']))
			{
				self::$errors .= "Missing role<br>";
				self::$errorcss['role'] = " error";
			}


			if(empty(self::$errors))
			{
				$salt = Session::CreateToken();
				$password = md5($salt . self::$account['accountpassword']) ;
				$password_htaccess = crypt(self::$account['accountpassword']) ;

				/***database connect***/
				$db = DB::getInstance();

				$db->Update("INSERT INTO `bx_administrator`
				(`username`,`password`, `password_htaccess`, `salt`, `email`, `name`, `role`) VALUES (
					?,?,?,?,?,?,?
				)", array(
					self::$account['accountusername'], $password, $password_htaccess, $salt, self::$account['email'], self::$account['name'], self::$account['role']
				));

				self::$success = true;
				Logger::AdminActivity('added admin account', self::$account['accountusername']); //'success'
			}

		unset($_POST);

	} //addnew

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

		$checkbox = $_POST['checkbox'];

		foreach($checkbox as $cb)
		{
			$del_ids = $db->Prepare("?,", array($cb));
		}
		$del_ids = rtrim($del_ids, ',');

		if ($del_ids)
		{
			$theadmin = $db->Column("SELECT `username` FROM `bx_administrator` WHERE `role`='administrator' LIMIT 1");

			//first administrator account and accounts with name "admin" are undeletable
			if (!in_array($theadmin, $checkbox, true) && !in_array('admin', $checkbox, true))
			{
				$db->Update("DELETE FROM `bx_administrator` WHERE `username` IN (".$del_ids.")");

				//show the message
				self::$success = true;
				//log activity
				Logger::AdminActivity('deleted admin account(s)', $del_ids); //'success'
			}
			else
				self::$error = 'Administrator account cannot be deleted.';
		}

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
		//editors do not have access to add/delete users
		Authentification::CheckPermission('administrator');

		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try
			{
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if(isset($_POST['_action']))
				{
					# add new account
						if($_POST['_action'] == 'addnew') $this->addnew();
					# delete account
						elseif($_POST['_action'] == 'delete') $this->deletion();
				}
			}
			catch (Exception $e)
			{
					echo $translate->_("Error").': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();

	} //showIndex

} //class
