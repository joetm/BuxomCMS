<?php

/* **************************************************************
 *  File: options_cache.class.php
 *  Version: 1.0
 *  Copyright � 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_cache extends BaseController
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

			/***options***/
			Config::ClearCache();

			$options = array();
			$options = Config::GetDBOptions(array(
					'caching',
					'cache_engine'
			));
			/***options***/

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***disconnect***/
		unset($db);

		/***TEMPLATE ASSIGNMENTS***/

		//options
		$tpl->assign("options", $options);

		//internationalization
		$_t = $translate->translateArray(array(
			"cache_system" => "Cache System",
			"caching" => "Caching",
			"caching_options" => "Caching Options",
			"submit" => "Submit",
		));
		$tpl->assign("_t", $_t);

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
		Tools::SaveOptions();

		Logger::AdminActivity('changed options', 'cache');

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
