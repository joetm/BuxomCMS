<?php

/* **************************************************************
 *  File: members.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class members extends BaseController
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
		$tpl->title = $translate->_("Members");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***database disconnect***/
		unset($db);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//prefill
		if(!isset($_POST['no_emails']))
		{
			$tpl->assign("var", array(
				'no_emails' => 0,
			));
		}

		//defaults
		$tpl->assign("_defaults", array(
			'username' => 'John Doe',
			'password' => 'password',
			'email' => 'john@doe.com',
			'IP' => '000.000.000.000',
			));


		//internationalization
		$_t = $translate->translateArray(array(
			"activate" => "Activate",
			"addnewmember" => "Add New Member",
			"days" => "Days",
			"deactivate" => "Deactivate",
			"delete" => "Delete",
			"edit" => "Edit",
			"edit_member" => "Edit Member",
			"email" => "Email",
			"emailmembers" => "Email Members",
			"expiry" => "Expiry",
			"export" => "Export",
			"exportactive" => "Export Active Members",
			"exportinactive" => "Export Inactive Members",
			"ID" => "ID",
			"IP" => "IP",
			"join_date" => "Join Date",
			"last_login" => "Last Login",
			"membername" => "Member Name",
			"password" => "Password",
			"receive_emails" => "Receive Emails",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

	} //function index

/**
* addnew
*
* @access private
*/
	private function addnew()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

			/***database connect***/
			$db = DB::getInstance();

			//get tpl
			$tpl = Zend_Registry::get('tpl');

			$var = array();
			$var = Input::clean_array('p',array(
				'new_username' => 'NOHTML',
				'new_password' => 'NOHTML',
				'new_email' => 'NOHTML',
				'join_date' => 'NOHTML',
				'expire_time' => 'UINT',	//in days
				'IP' => 'NOHTML',
				'no_emails' => 'UINT',		//include in newsletter or not?
				));

			$expire_seconds = $var['expire_time'] * 24 * 60 * 60; //account expires in ($expire_seconds) seconds

			//convert dates to timestamps
			$var['join_date'] = strtotime($var['join_date']);


			if(empty($tpl->arrErrors)){
				//add new member to database

				$status = false;

				//create user salt
				$salt = Session::CreateToken(3); //length=3

				//add the new members email
				$status = $db->Update("INSERT INTO `bx_member_email` (`id`, `email`, `no_emails`, `email_status`, `last_mailing`, `num_mailings`) VALUES
				(?,?,?,?,?,?)",
				array(
					NULL, $var['new_email'], $var['no_emails'], 'unmailed', NULL, 0
				));

				$email_id = $db->LastInsertId();

				if($status && $email_id)
				{
					$status = $db->Update("INSERT INTO `bx_member` (`id`,`username`, `password`, `password_htaccess` ,`salt`, `email_id`, `join_date`) VALUES
					(?,?,?,?,?,?,?)",
					array(
						NULL,
						$var['new_username'],
						md5($salt . $var['new_password']),
						crypt($var['new_password']),
						$salt,
						$email_id,
						$var['join_date'],
					));

					if(!$status)
					{
						//delete email again
						$db->Update("DELETE FROM `bx_member_email` WHERE `id` = ?", array($email_id));
					}
					else
					{
						$member_id = $db->LastInsertId();
					}

					if(!$var['currency'] = Config::Get('default_currency'))
						$var['currency'] = 'USD';

					$status = $db->Update("INSERT INTO `bx_join` (
						`id`,
						`member_id`,
						`username`,
						`status`,
						`currency`,
						`signup_IP`,
						`join_date`,
						`expiration_date`,
						`num_rebills`,
						`dateline`
					) VALUES (?,?,?,?,?,?,?,?,?,?)",
					array(
						NULL,
						$member_id,
						$var['new_username'],
						'active',
						$var['currency'],
						$var['IP'],
						time(),
						NULL,
						0,
						time() + $expire_seconds,
					));

				}

				if(!$status)
				{
					$tpl->errormessage($translate->_("New Member error"));
				}
				else
				{

					$tpl->successmessage($translate->_("Done"));
					unset($var);
					unset($_POST['checkbox']);

					Logger::AdminActivity('added member', $member_id);
				}
			}
			else
			{
				//prefill
				$tpl->assign("var", $var);

			}

		unset($_POST);

	} //addnew

/**
* Edit existing Member
*
* @access private
*/
	private function edit()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

			/***database connect***/
			$db = DB::getInstance();

			//get tpl
			$tpl = Zend_Registry::get('tpl');

			$var = array();
			$var = Input::clean_array('p',array(
				'new_username' => 'NOHTML',
				'new_password' => 'NOHTML',
				'new_email' => 'NOHTML',
				'join_date' => 'NOHTML',
				'expire_time' => 'UINT',	//in days
				'IP' => 'NOHTML',
				'member_id' => 'UINT',
				'no_emails' => 'UINT',		//include in newsletter or not?
				));

			$expire_seconds = $var['expire_time'] * 24 * 60 * 60; //account expires in ($expire_seconds) seconds

			//convert dates to timestamps
			$var['join_date'] = strtotime($var['join_date']);


			if(empty($tpl->arrErrors)){
				//add new member to database

				//create user salt
				$salt = Session::CreateToken(3); //length=3

				//add the new members email
				$status = $db->Update("INSERT INTO `bx_member_email` (`id`,`email`,`no_emails`,`email_status`,`last_mailing`,`num_mailings`) VALUES
				(?,?,?,?,?,?)",
				array(
					NULL, $var['new_email'], $var['no_emails'], 'unmailed', NULL, 0
				));

				$email_id = $db->LastInsertId();

				if($status)
				{
					$status = $db->Update("UPDATE `bx_member`
					SET `username`=?,
					".(!empty($var['password']) ? "
						`password`=?,
						`password_htaccess`=?,
						`salt`=?,"
					: '')."
					`email_id`=?,
					`join_date`=?,
					`status`=?,
					`expiration_date`=?
					WHERE `id`=?",
					(!empty($var['password']) ?
						array(
							$var['new_username'],
							md5($salt.$var['new_password']),
							crypt($var['new_password']),
							$salt,
							$email_id,
							$var['join_date'],
							'active',
							time()+$expire_seconds,
							$var['member_id'])
					:
						array(
							$var['new_username'],
							$email_id,
							$var['join_date'],
							'active',
							time()+$expire_seconds,
							$var['member_id'])
					)
					);

					if(!$status)
					{
						//delete email again
						$db->Update("DELETE FROM `bx_member_email` WHERE `id` = ?", array($email_id));
					}
					else
					{
						$member_id = $db->LastInsertId();
					}
				}

				if(!$status)
				{
					$tpl->errormessage($translate->_("New Member error"));
				}
				else
				{

					$tpl->successmessage($translate->_("Done"));
					unset($var);
					unset($_POST['checkbox']);

					Logger::AdminActivity('added member', $member_id);
				}
			}
			else
			{
				//prefill
				$tpl->assign("var", $var);

			}

		unset($_POST);

	} //edit

/**
* Make API Call
*
* @access	private
* @param	string		Comma-separated list of subscription_ids
*/
	private function Merchant_Call($action, $ids, $reasoncode = 11){

			$tpl = Zend_Registry::get('tpl');

			/***get connection***/
			$db = DB::getInstance();

			//a request to the processor needs to be sent

			$subscriptions = $db->FetchAll("SELECT subscription_id as code
						FROM `bx_member`
						WHERE `id` IN (".$ids.")");


			require_once(Path::Get('path:site/signup') . DIR_SEP . String::Slash(Config::GetDBOptions('processor'), 0, 0) . DIR_SEP . 'Merchant.php');

			if(class_exists('Merchant') && is_callable('CALL_API'))
			{
				$merchant = new Merchant();

				$errors = array();
				foreach($subscriptions as $id)
				{
					//no subscription code found
	//				if(!$id['code']) continue;

					$res = $merchant->CALL_API($action, $id['code'], $reasoncode);

					//process the results to check for errors

					if(!$res)
					{
						$tpl->errormessage('Errors sending request for Subscription ID '.$id['code']);
						$status = 'failed';
					}
					else
					{
						$tpl->successmessage('Successfully sent request for Subscription ID '.$id['code']);
						$status = 'success';
					}

					Logger::AdminActivity("sent cancel request ($status)", $id['code']);
				}
			}
			else
			{
				$tpl->errormessage('Could not make API call. Method not found in Merchant API plugin.');
			}

	} //Merchant_Call

/**
* Deletion
*
* @access private
*/
	private function deletion(){

		//get translation
		$translate = Zend_Registry::get('translate');

			if(isset($_POST['checkbox']))
			{
				$tpl = Zend_Registry::get('tpl');

				/***get connection***/
				$db = DB::getInstance();

				//get the ratings that are marked for deletion
				$del_ids = Input::GetCheckBoxes();

/*
//emails are now stored with the same id as member!

				//Get the email of the users
				$emails = $db->FetchAll("SELECT e.id FROM `bx_member_email` AS `e`,`bx_member` AS `m` WHERE m.email_id=e.id AND m.id IN (".$del_ids.")");

				//delete the emails of the members
				$email_ids = '';
				foreach($emails as $e)
				{
					$email_ids .= intval($e).',';
				}
				$email_ids = rtrim($email_ids, ',');
*/

				$status = $db->Update("DELETE FROM `bx_member_email` WHERE `id` IN (".$del_ids.")");

				//delete the ratings that are marked for deletion
				$status = $db->Update("DELETE FROM `bx_member` WHERE `id` IN (".$del_ids.")");


				//make a request to processing company?
				$api = Input::clean_single('p', 'api_call', 'BOOL');
				if($api && $action == 'inactive')
				{
					$this->Merchant_Call('CANCEL', $ids);
				}


				//is plural?
				if(!explode(",",$del_ids,2))
					$delerror = $translate->_("Member deletion error");
				else
					$delerror = $translate->_("Member deletion errors");

				if(!$status) {
					$tpl->errormessage($delerror); //"Could not delete member(s)."
				}
				else{
					$tpl->successmessage($translate->_("Done"));
					Logger::AdminActivity('deleted member(s)', $del_ids);
				}
			}

		unset($_POST);

	} //deletion

/**
* activate
*
* @access private
*/
	private function activate_switch(){

		//get translation
		$translate = Zend_Registry::get('translate');

		//get tpl
		$tpl = Zend_Registry::get('tpl');

		$ids = Input::GetCheckboxes();

		//activate or deactivate?
		if(!empty($_POST['activate']))
			//activate
			$action = 'active';
		elseif(!empty($_POST['deactivate']))
			//deactivate
			$action = 'inactive';
		else
			//no action found
			return;

		/***database connect***/
		$db = DB::getInstance();

		//expire members
		$num_rows = $db->Update("UPDATE `bx_member` SET `status` = '".$action."' WHERE `id` IN (".$ids.")");

		//is plural?
		$experror = array();
		if(!explode(",",$ids, 2))
		{
			$experror['active'] = "Could not activate member";
			$experror['inactive'] = "Could not expire member";
		}
		else
		{
			$experror['active'] = "Could not activate members";
			$experror['inactive'] = "Could not expire members";
		}

		if($num_rows == -1)
		{
			$tpl->errormessage($translate->_($experror[$action]));
		}
		else
		{
			$tpl->successmessage($translate->_("Done"));
			Logger::AdminActivity('changed member status', $action);
		}


		//make a request to processing company?
		$api = Input::clean_single('p', 'api_call', 'BOOL');
		if($api && $action == 'inactive')
		{
			$this->Merchant_Call('CANCEL', $ids);
		}


		//do nothing else
		unset($_POST);

	} //activate switch

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
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if( Authentification::CheckPermission('administrator', 'editor') )
				{
					/***add new member***/
					if (isset($_POST['member_id']) && $_POST['member_id'])
						$this->edit();
					/***add new member***/
					if (!empty($_POST['addnewmember']))
						$this->addnew();
					/***delete members***/
					if (!empty($_POST['delete']))
						$this->deletion();
					/***activate members***/
					if (!empty($_POST['activate']) || !empty($_POST['deactivate']))
						$this->activate_switch();
				}
			}
			catch (Exception $e) {
					echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();
	}

} //class
