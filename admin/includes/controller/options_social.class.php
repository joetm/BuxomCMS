<?php

/* **************************************************************
 *  File: options_social.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_social extends BaseController
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

			/***regular options***/
			$options = array();
			$options = Config::GetDBOptions(array(
					'blacklist_words',
					'comment_use_blacklist',
					'nested_comments',
					));
			/***regular options***/

			/***plugins***/
			$plugins = array();
			$ping_options = array();
			try
			{
				$plugins = SocialPlugin::GetSocialOptions();
				$ping_options = Config::GetDBOptions(array(
					'ping_engines',
					'ping_urls',
					));
			}
			catch(Exception $e)
			{
				$tpl->errormessage($e->getMessage());
			}
			/***plugins***/

		/***disconnect***/
		unset($db);


		/***TEMPLATE ASSIGNMENTS***/

		//output
		if(!empty($plugins)) $tpl->assign("plugins", $plugins);
		if(!empty($options)) $tpl->assign("options", $options);

		//ping_options
		if(!empty($ping_options['ping_urls']))
			$ping_options['ping_urls'] = implode(PHP_EOL, $ping_options['ping_urls']);
		if(!empty($ping_options)) $tpl->assign("ping_options", $ping_options);

		//internationalization
		$_t = $translate->translateArray(array(
			"nested_comments" => "Nested Comments",
			"password" => "Password",
			"ping_search_engines" => "Ping search engines",
			"socialnetworkfeatures" => "Social Network Features",
			"submit" => "Submit",
			"use_blacklist" => "Use Blacklist",
			"username" => "Username",
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

		/***database connect***/
		$db = DB::getInstance();

		//save the regular options
		Tools::SaveOptions();

		foreach($_POST['plugins'] as $key => $op)
		{
			//check for social network plugin
			if(is_array($op) && isset($op['name']))
			{
//				$op['name'] = Input::clean($op['name'], 'NOHTML');

				$option_name = $op['name']."_plugin";


//problem! access_token wird bei speichern ueberschrieben!!!












				//serialize
				$op = serialize($op);

				//write data
				$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`=?", array($op, $option_name));
			}
		}

		$ping_urls = serialize(explode(PHP_EOL, Input::clean($_POST['ping_options']['ping_urls'], 'STR')));
		if($ping_urls)
			$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`='ping_urls'", array($ping_urls));

		$ping_engines = Input::clean($_POST['ping_options']['ping_engines'], 'HTML');
		if($ping_engines)
			$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`='ping_engines'", array($ping_engines));

		$blacklist_words = explode(",", Input::clean($_POST['blacklist_words'], 'STR'));
		$blacklist_words = Arr::Trim($blacklist_words);
		if($blacklist_words)
		{
			sort($blacklist_words);
			$blacklist_words = strtolower( implode(",", $blacklist_words) );

			if(!empty($blacklist_words))
			$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`='blacklist_words'", array($blacklist_words));

			unset($blacklist_words);
		}

		unset($_POST);

		//delete Config cache
		Config::ClearCache();

		Logger::AdminActivity('changed options', 'social');

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
				if($_POST['securitytoken'] != self::$token || isset($_GET['securitytoken']))
					throw new Exception($translate->_('Security Token mismatch'));

				if(isset($_POST['submit'])) $this->SaveOptions();
			}
			catch (Exception $e) {
				echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
				die();
			}
		}

		if(!empty($_GET))
		{
			//redirect to plugin auth

			if(isset($_GET['plugin'])) //what to show
			{
				$input = Input::clean_array('g',array(
					'plugin'          => 'FILENAME',
					'key'    => 'STR',
					'secret' => 'STR',
				));

				//redirect
				if(!empty($input['plugin']))
				{
					//fire the action which will redirect to the Service for Authentification

					$plugin_path = Path::Get('path:admin').DIR_SEP."plugins".DIR_SEP."$input[plugin].plugin.php";

					if(file_exists($plugin_path))
					{
						require_once($plugin_path);

						$classname = $input['plugin'].'_plugin';
						if(class_exists($classname))
						{
							$plugin = new $classname;

							if(is_callable(array($plugin, 'Authorize')))
							{
								$result = $plugin->Authorize();
								if($result) exit();
								//if empty variables came as result,
								//show the index
							}
							else
								die('Could not call Authorize.');
						}
						else
							die("Plugin not found.");
					}
					else
						die("Plugin not found.");
				}

			} //authorize
		}

		$this->index();

	} //showIndex

} //class
