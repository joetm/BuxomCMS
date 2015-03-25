<?php

/* **************************************************************
 *  File: options_general.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_general extends BaseController
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

			/***options***/
			$options = array();
			$options = Config::GetDBOptions(array(
				'2257info',
				'email','emailname',
				'email_pp',
				'floating_thumbs',
				'mailmethod','smtphost','smtpuser','smtppass','smtpport',
				'sendemailonnewfaqitem',
				'sitename',
			));
			/***options***/

		/***disconnect***/
		unset($db);

		//parse new line characters
		//and return as single string
		$options['2257info'] = String::ReplaceNewLine($options['2257info']);


		/***TEMPLATE ASSIGNMENTS***/

		//internationalization
		$_t = $translate->translateArray(array(
			"companyinformation" => "Company Information",
			"email" => "Email",
			"floating_thumbnails" => "Floating Thumbnails",
			"new_update" => "New Update",
			"sitedetails" => "Site Details",
			"sitename" => "Site Name",
			"siteurl" => "Site url",
			"submit" => "Submit",
		));
		$tpl->assign("_t", $_t);



		//options
		if(isset($options)) $tpl->assign("options", $options);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* Save Options
*
* @access private
*/
	private function SaveOptions()
	{
		//special: replace the line breaks in 2257info with "\n"
		if(!empty($_POST['options']['2257info']))
			$_POST['options']['2257info'] = String::LineBreakEncode($_POST['options']['2257info']);

		Tools::SaveOptions();

		Logger::AdminActivity('changed options', 'general');

	} //SaveOptions

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

				if(isset($_POST['submit'])) $this->SaveOptions();
			}
			catch (Exception $e) {
				echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
				die();
			}
		}

		$this->index();

	} //showIndex

} //class
