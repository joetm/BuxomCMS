<?php

/* **************************************************************
 *  File: options_billing.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_billing extends BaseController
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
				'approval_url','denial_url','error_url','processor'
			));
			/***options***/

			$processorpath = Path::Get('path:site/signup');
			if(is_dir($processorpath))
				$processors = Tools::getDirectories($processorpath);

		/***disconnect***/
		unset($db);

		/***TEMPLATE ASSIGNMENTS***/

		//options
		if(isset($options)) $tpl->assign("options", $options);

		//processors
		if(isset($processors)) $tpl->assign("processors", $processors);

		//internationalization
		$_t = $translate->translateArray(array(
			"approval_url" => "Approval URL",
			"denial_url" => "Denial URL",
			"error_url" => "Error URL",
			"internal_id" => "Internal ID",
			"submit" => "Submit",
		));
		$tpl->assign("_t", $_t);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index

/**
* Save Options
*
* @access private
*/
	private function SaveOptions()
	{
		Tools::SaveOptions();

		Logger::AdminActivity('changed options', 'billing');

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
			catch (Exception $e)
			{
				echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
				die();
			}
		}

		$this->index();

	} //showIndex

} //class
