<?php

/* **************************************************************
 *  File: email.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class email extends BaseController
{
	private static $token = false;
	private static $error = false;

	private static $email = array();

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
		$tpl->title = $translate->_("Email Members");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			//email templates
			$templates = array();
			$templates = $db->FetchAll("SELECT * FROM `bx_email_template`");

			//prefill actions
			$actions = $db->FetchAll("SELECT `action` FROM `bx_email_template`");

		/***database disconnect***/
		unset($db);


		//preselect
		$var['type'] = "html";
		$var['members'] = "all";
		$tpl->assign("var", $var);

		//per page setting for email process
		$tpl->assign("pp", Config::GetDBOptions('email_pp'));

		//prefill email
		if(!empty(self::$email))
			$tpl->assign("email", self::$email);

		//email templates
		if(!empty($templates)) $tpl->assign("templates", $templates);

		//prefill actions
		if(!empty($actions)) $tpl->assign("actions", $actions);

		//error
		if(self::$error) $tpl->assign("error", self::$error);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"error" => "Error",
			"mailprocess_finished" => "Mail Process Finished Successfully",
			"members" => "Members",
			"save" => "Save",
			"send" => "Send",
			"subject" => "Subject",
			"success" => "Success",
			"testemail" => "Test Email",
			"type" => "Type",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

	} //index

/**
* Save Email Template
*
* @access private
*/
	private function saveTemplate()
	{
		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

		self::$email = Input::clean_array('p', array(
				'action' => 'STR',
				'actionselector' => 'STR',
				'body' => 'STR',
				'locale' => 'NOHTML',
				'subject' => 'STR',
				'title' => 'STR',
				));

		if(empty(self::$email['action']) && empty(self::$email['actionselector']))
		{
			self::$error = 'No action specified.';
		}
		else
		{
			//new action hat Vorrang
			$action = (self::$email['action'] ? self::$email['action'] : self::$email['actionselector']);
		}

		if(empty(self::$email['title']))
		{
			self::$error = 'Templatename not specified.';
		}


		if(empty(self::$error))
		{

			/***database connect***/
			$db = DB::getInstance();

			//check db to see if such a template already exists
			$exists = $db->Row("SELECT `id` FROM `bx_email_template` WHERE `title` = ? AND `action` = ? AND `role` = 'member' LIMIT 1", array(self::$email['title'], $action));

			if($exists)
			{

				//overwrite existing template
				$db->Update("UPDATE `bx_email_template`
						SET `content` = ?
						WHERE `id` = ?",
						array(
							self::$email['content'],
							$exists['id']
						));
			}

			else
			{
				$db->Update("INSERT INTO `bx_email_template` (
						`id`,
						`subject`,
						`content`,
						`title`,
						`action`,
						`role`
					) VALUES (
						?,?,?,?,?,?)",
						array(
							NULL,
							self::$email['subject'],
							self::$email['body'],
							self::$email['title'], // = templatename
							$action,
							'member'
						));	//only allow editing of member emails
			}

		}

	} //saveTemplate

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
			if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
				throw new Exception($translate->_('Security Token mismatch'));

			if( Authentification::CheckPermission('administrator') )
			{
				/***save template***/
				if (!empty($_POST['action']) || !empty($_POST['actionselector']))
					$this->saveTemplate();
			}
		}
		else
			/***load existing model***/
			if (isset($_GET['edit']))
				$this->edit();

		$this->index();
	}

} //class
