<?php

/* **************************************************************
 *  File: options_types.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_types extends BaseController
{
	private static $token = false;

	private static $success = false;

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
		$tpl->title = $translate->_("Post Types")." ".$translate->_("Options");
		/***page setup***/

		//clear cache
		Config::ClearCache();

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			/***options***/
			$types = Config::GetDBOptions(array(
				'types',
			));
			/***options***/

		/***disconnect***/
		unset($db);


//		if(String::is_serialized($types))
//			$types = unserialize($types);

//var_dump($types);


		/***TEMPLATE ASSIGNMENTS***/

		//options
		if(isset($types)) $tpl->assign("types", $types);

		//error/success
		if (self::$success || isset($_GET['success']))
			$tpl->successmessage($translate->_("Done"));

		//internationalization
		$_t = $translate->translateArray(array(
			'post_types' => 'Post Types',
			'submit' => 'Submit',
		));
		$tpl->assign("_t", $_t);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* Load Options
*
* @access private
*/
	private function LoadDefaults()
	{
		//remove
		if(isset($_POST['options']))
			unset($_POST['options']);

		//build defaults
		$_POST['options']['types'] = array(
				//form of: 'seoslug'(= `type` in database) => 'folder'
				'model' => 'models',
				'set' => 'pics',
				'video' => 'videos',
			);

		//save defaults
		$this->SaveOptions();

		unset($_POST);

	} //LoadDefaults

/**
* Save Options
*
* @access private
*/
	private function SaveOptions()
	{
		if(isset($_POST['options']))
		{
			$options = $_POST['options'];
			unset($_POST['options']);

			//check if something was changed
			foreach($options as $key => $val)
			{
				if(!empty($val) && !empty($val['slug']) && !empty($val['folder']))
				{
					$_POST['options']['types'][$val['slug']] = $val['folder'];
				}
			}

			Tools::SaveOptions();

			unset($_POST);

			Logger::AdminActivity('changed options', 'types');
		}

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

				if(isset($_POST['defaults']) && $_POST['defaults'] == '1')
					$this->LoadDefaults();
				elseif(isset($_POST['submit']))
					$this->SaveOptions();
			}
			catch (Exception $e) {
				echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
				die();
			}
		}

		$this->index();

	} //showIndex

} //class
