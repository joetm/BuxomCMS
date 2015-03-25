<?php

/* **************************************************************
 *  File: tumblr.plugin.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*** include the init.php file ***/
require_once dirname(__FILE__).'/../../_init.php';

//require_once 'Zend/Oauth.php';
//require_once 'Zend/Oauth/Consumer.php';


//class name of plugin must be in the form of "<name>_plugin"
class tumblr_plugin extends SocialPlugin {

	public $updateposting_possible = true;

	public $picturepost = true;

	public $infotext = 'Automatically posts the free images of a new update to tumblr. Tumblr requires you to enter your email and password.';

	public $auth_type = "rest";

	private static $requires_auth = 0;

	public $defaults = array();
	public $options = array();
	private static $configuration = array(
		'method' => 'POST',
		'rest_endpoint' => 'http://www.tumblr.com/api/write',
	);


	//add images to tumblr
	//executed when new update is successfully added
	public function NewUpdate($update = null)
	{
		if(!is_array($update) || empty($update['freeimages']))
			return false;

		//for tumblr, we work with the freeimage array

		//get plugin options
		$this->options = parent::GetDetails('tumblr_plugin');

		if(empty($this->options['email']) || empty($this->options['password']))
		{
			return false;
		}


		$post_type  = 'photo';

		$result = array();

		foreach($update['freeimages'] as $photo)
		{
			$request_data = http_build_query(
			    array(
				'email'     => $this->options['email'],
				'password'  => $this->options['password'],
				'type'      => $post_type,
				'title'     => "$update[title]",
				'body'      => "$update[description]",
				'source'    => "$photo[url]",
				'generator' => Config::GetDBOptions('sitename') . ' (BXCMS 1.0)',
				'tags'   => "$update[tags]",
				'private'   => 0,
			    )
			);

			if (function_exists('curl_init'))
			{
				// use curl
				$c = curl_init(self::$configuration['rest_endpoint']);
				curl_setopt($c, CURLOPT_POST, true);
				curl_setopt($c, CURLOPT_POSTFIELDS, $request_data);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($c);
				$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
				curl_close($c);
			}
			else
			{
				$fp = @pfsockopen($matches[1], 80);
				if (!$fp) {
					die('Could not connect to the web service');
				}
				fputs ($fp,'POST ' . $matches[2] . " HTTP/1.1\n");
				fputs ($fp,'Host: ' . $matches[1] . "\n");
				fputs ($fp,"Content-type: application/x-www-form-urlencoded\n");
				fputs ($fp,"Content-length: ".strlen($request_data)."\n");
				fputs ($fp,"Connection: close\r\n\r\n");
				fputs ($fp,$request_data . "\n\n");
				$response = "";
				while(!feof($fp)) {
					$response .= fgets($fp, 1024);
				}
				fclose ($fp);
				$status = trim(substr($response, 0, strpos($response, "\n")));
			}

			if ($status == 201) {
			    $result[] = "Success! The new post ID is $response.\n";
			} else if ($status == 403) {
			    $result[] = 'Bad email or password';
			} else {
			    $result[] = "Error: $response\n";
			}



			parent::UpdateProgressBar('DATA');
		}

		return $result;

	} //NewUpdate


	public static function GetConfig()
	{
		/***database connect***/
		$db = DB::getInstance();

		$plugin_options = unserialize($db->Column("SELECT `value` FROM `bx_options` WHERE `key`='flickr_plugin'"));

		//runtime config
		self::$configuration = array_merge(self::$configuration,
			array(
			'callbackUrl' => Path::Get('url:admin').'/plugins/tumblr.plugin.php',
			'email' => $plugin_options['email'],
			'password' => $plugin_options['password'],
			));

		return self::$configuration;
	}

	//get empty configuration defaults
	public function GetDefaults($name)
	{
		$this->defaults = array(
			'name' => $name, //get_class($this),
			'username' => NULL,
			'postonupdate' => false,
			'picturepost' => $this->picturepost,
			'requires_auth' => self::$requires_auth,
			'email' => NULL,
			'password' => NULL,
			"updateposting_possible" => ($this->updateposting_possible ? $this->updateposting_possible : false),
			"auth_type" => ($this->auth_type ? $this->auth_type : NULL),
			'infotext' => ($this->infotext ? $this->infotext : NULL),
			);
		return $this->defaults;
	}

	//this function must be present in each social posting plugin
	//no changes in this function are necessary if you want to create a new plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	} //GetDetails

} //class


//returning from tumblr
if(!empty($_GET['frob']))
{
	$translate = new Translator("admin");
	Zend_Registry::set('translate', $translate);

			/*--------------------------------------------------------------*/
			/*						Authentification						*/
			/*--------------------------------------------------------------*/
	//				Authentification::check();
					if(!Authentification::Login())
					{
						echo Authentification::GetError();
						die();
					}
					Authentification::CheckPermission('administrator');
			/*--------------------------------------------------------------*/


	//returning from flickr auth
	//continuing where we left off
	$plugin = new flickr_plugin();
	$plugin->Authorize();
}
//request to remove auth
elseif(isset($_POST['plugin'])
	&& isset($_POST['remove_auth'])
	&& isset($_POST['securitytoken'])
	&& $_POST['remove_auth'] == '1')
{
	$translate = new Translator("admin");
	Zend_Registry::set('translate', $translate);

			/*--------------------------------------------------------------*/
			/*						Authentification						*/
			/*--------------------------------------------------------------*/
	//				Authentification::check();
					if(!Authentification::Login())
					{
						echo Authentification::GetError();
						die();
					}
			/*--------------------------------------------------------------*/

	if('administrator' != Authentification::GetRole())
		Template::PermissionDenied();

	if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] !== Session::GetToken())
		die($translate->_('Security token mismatch'));

	//remove Auth
	SocialPlugin::DeAuthorize('flickr_plugin');
}
