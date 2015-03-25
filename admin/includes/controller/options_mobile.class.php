<?php

/* **************************************************************
 *  File: options_mobile.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_mobile extends BaseController
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
			$options = Config::GetDBOptions(array(
				'mobile_device_redirect',
				'mobile_device_redirect_urls'
			));

//var_dump($options);

			//unserialize
//			$options['mobile_device_redirect_urls'] = unserialize($options['mobile_device_redirect_urls']);
			/***options***/

		/***disconnect***/
		unset($db);


		/***TEMPLATE ASSIGNMENTS***/

		//options
		if(isset($options)) $tpl->assign("options", $options);

		//internationalization
		$_t = $translate->translateArray(array(
			"globalredirection" => "Global Redirection",
			"mobiledeviceredirections" => "Mobile Device Redirections",
			"othermobiledevices" => "Other mobile devices",
			"redirects" => "Redirects",
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
		if(isset($_POST['output']['mobile_device_redirect_urls']))
			$_POST['output']['mobile_device_redirect_urls'] = unserialize($_POST['output']['mobile_device_redirect_urls']);

//		Tools::SaveOptions();

		/***database connect***/
		$db = DB::getInstance();

		$input = array();
		$input['mobile_device_redirect'] = Input::clean($_POST['options']['mobile_device_redirect'],'BINARY');
		$input['mobile_device_redirect_urls'] = Input::clean($_POST['options']['mobile_device_redirect_urls'],'NOHTML');

		//serialize urls
		$input['mobile_device_redirect_urls'] = serialize($input['mobile_device_redirect_urls']);

		//write data
		$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`='mobile_device_redirect'", array($input['mobile_device_redirect']));
		$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`='mobile_device_redirect_urls'", array($input['mobile_device_redirect_urls']));

		unset($_POST);

		Logger::AdminActivity('changed options', 'mobile');

		//delete Config cache
		Config::ClearCache();

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
