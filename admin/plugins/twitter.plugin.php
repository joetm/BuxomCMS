<?php

/* **************************************************************
 *  File: twitter.plugin.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*** include the init.php file ***/
require_once dirname(__FILE__).'/../../_init.php';

require_once 'Zend/Oauth.php';
require_once 'Zend/Oauth/Consumer.php';


//class name of plugin must be in the form of "<name>_plugin"
class twitter_plugin extends SocialPlugin {

	public $updateposting_possible = true;

	public $picturepost = false;

	public $infotext = 'Automatically posts link to new update to twitter.';

	public $auth_type = "oauth";

	private static $requires_auth = 1;

	const CHARLIMIT = 140;

	public $defaults = array();

	private static $configuration = array(
		'signatureMethod' => 'HMAC-SHA1',
		'siteUrl' => 'https://api.twitter.com/oauth',
		'requestTokenUrl' => 'https://api.twitter.com/oauth/request_token',
		'authorizeUrl' => 'https://api.twitter.com/oauth/authorize',
		'accessTokenUrl' => 'https://api.twitter.com/oauth/access_token',
		'version' => '1.0',
	);

	public static function GetConfig()
	{
		/***database connect***/
		$db = DB::getInstance();

		$plugin_options = unserialize($db->Column("SELECT `value` FROM `bx_options` WHERE `key`='twitter_plugin'"));

		//runtime config
		self::$configuration = array_merge(self::$configuration,
			array(
			'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
			'callbackUrl' => Path::Get('url:admin').'/plugins/twitter.plugin.php',
			'consumerKey' => $plugin_options['key'],
			'consumerSecret' => $plugin_options['secret'],
			),
			$plugin_options
		);

		return self::$configuration;
	}


	//executed when new update is successfully added
	public function NewUpdate($update = null)
	{
		if(!is_array($update) || empty($update) || empty($update['title']) || empty($update['type']) || empty($update['slug']))
		{
			parent::UpdateProgressBar('FAIL');
			return false;
		}

		//get access token
		$config = self::GetConfig();

		require_once "Zend/Service/Twitter.php";

		$twitter = new Zend_Service_Twitter(array(
		    'username' => $config['username'],
		    'accessToken' => $config['access_token'],
		));

		if($twitter)
		{
			$tweet = '';

			$baseurl = '';
			//prepare the tweet
			//construct the url
			switch($update['type'])
			{
				case 'set':
				case 'video':
				case 'model':
					$baseurl = Path::Get("url:site/$update[type]");
					break;
				default:
					$baseurl = Path::Get('url:site');
					break;
			}
			$url = $baseurl . '/' . ltrim($update['slug'], "/");

			$remaining = self::CHARLIMIT - strlen($url) + 1; //we need a separating character between title and url

			if($remaining > 0)
			{
				if(strlen($update['title']) > $remaining)
				{
					//trim the title
					$update['title'] = substr($update['title'], 0, $remaining - 3) . "..."; //three dots
				}
			}
			else //abort, because url is too long
			{
				parent::UpdateProgressBar('FAIL');
				return false;
			}

			if(!empty($url) && !empty($update['title']))
			{
				$tweet = $update['title'] . ' ' . $url;

				if(Config::Get('debug')) Tools::debug_msg( 'Tweet: "'.$tweet.'"', true ); //with br

				//send tweet
				$response   = $twitter->status->update( $tweet );

				/*
				//an auth error response will look like this:
				object(Zend_Rest_Client_Result)#16 (2) {
				    ["_sxml:protected"]=> object(SimpleXMLElement)#21 (2) {
					["request"]=> string(31) "/account/verify_credentials.xml"
					["error"]=> string(27) "Could not authenticate you."
				    }
				    ["_errstr:protected"]=> NULL
				}
				*/

				if(!empty($response->sxml->error))
				{
					if(Config::Get('debug')) Tools::debug_msg( $response->sxml->error, true ); //with br

					parent::UpdateProgressBar('FAIL');
				}
				else
				{
					if(Config::Get('debug')) Tools::debug_msg( "OK", true ); //with br

					parent::UpdateProgressBar('OK');
				}
			}
		}
		else
		{
			parent::UpdateProgressBar('FAIL');
			return false;
		}

	} //NewUpdate


	public function Authorize()
	{

		/***database connect***/
		$db = DB::getInstance();

		//get config
		self::GetConfig();

		//check if variables are filled in?
		if(empty(self::$configuration['consumerKey']) || empty(self::$configuration['consumerSecret']))
		{
			return false;
		}

		$consumer = new Zend_Oauth_Consumer(self::$configuration);

		//get request token
		$request_token = $db->Column("SELECT `value` FROM `bx_temp` WHERE `key`='twitter_request_token'");

		// If we do not have a request token, generate one now
		if (!$request_token) {

			// fetch a request token
			$request_token = $consumer->getRequestToken();

			// Save the token for when the user returns to this page.
			// This will be used to get the user's access token.
			$db->Update("INSERT INTO `bx_temp` (`key`, `value`) VALUES ('twitter_request_token',?)", array(serialize($request_token)));

			// Send the user off to Twitter to grant our application access
			$consumer->redirect();
			exit();
		}


		/*BREAK: returning from twitter auth*/


		//get request token
		$request_token = $db->Column("SELECT `value` FROM `bx_temp` WHERE `key`='twitter_request_token'");

		if(!$request_token) die('Missing request token.');


		// If we made it here, the user has been to Twitter to grant our
		// application access and now we must get an access token that
		// will allow us to make API calls on behalf of the user.
		$access_token = $consumer->getAccessToken($_GET, unserialize($request_token));


		if(!empty($access_token))
		{
			//we have a twitter access token
			//it only needs to be saved now
			$op = $db->Column("SELECT `value` FROM `bx_options` WHERE `key` = 'twitter_plugin'");
			//config found
			if($op)
			{
				$twitter_options = unserialize($op);

				$twitter_options['access_token'] = $access_token;

				$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`='twitter_plugin'", array(serialize($twitter_options)));
			}

		} //access_token



		// We no longer need the request token so remove it
		//this also allows to start over if something failed...
		$db->Update("DELETE FROM `bx_temp` WHERE `key`='twitter_request_token'");


		//final redirect
		header('Location: '.Path::Get('url:admin').'/options_social');
		exit();

	} //Authorize


	//get empty configuration defaults
	public function GetDefaults($name)
	{
		$this->defaults = array(
			'name' => $name, // get_class($this),
			'username' => NULL,
			'postonupdate' => false,
			'picturepost' => $this->picturepost,
			'requires_auth' => self::$requires_auth,
			'key' => NULL,
			'secret' => NULL,
			'application_id' => NULL,
			"updateposting_possible" => ($this->updateposting_possible ? $this->updateposting_possible : false),
			"auth_type" => ($this->auth_type ? $this->auth_type : NULL),
			'infotext' => ($this->infotext ? $this->infotext : NULL),
		);

		return $this->defaults;

	} //GetDefaults

	//this function must be present in each option social plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	} //GetDetails

} //class


//returning from twitter
if(!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier']))
{
	$translate = new Translator("admin");
	Zend_Registry::set('translate', $translate);

		/*--------------------------------------------------------------*/
		/*                       Authentification                       */
		/*--------------------------------------------------------------*/
//				Authentification::check();
				if(!Authentification::Login())
				{
					echo Authentification::GetError();
					die();
				}
				Authentification::CheckPermission('administrator');
		/*--------------------------------------------------------------*/


	//returning from twitter auth
	//continuing where we left off
	$plugin = new twitter_plugin();
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
		/*                       Authentification                       */
		/*--------------------------------------------------------------*/
//				Authentification::check();
				if(!Authentification::Login())
				{
					echo Authentification::GetError();
					die();
				}
				Authentification::CheckPermission('administrator');
		/*--------------------------------------------------------------*/


	if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] !== Session::GetToken())
		die($translate->_('Security token mismatch'));

	//remove Auth
	SocialPlugin::DeAuthorize('twitter_plugin');
}
