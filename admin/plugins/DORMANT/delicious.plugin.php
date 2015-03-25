<?php

/* **************************************************************
 *  File: delicious.plugin.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

//class name of plugin must be in the form of "<name>_plugin"
class delicious_plugin extends SocialPlugin {

	public $updateposting_possible = true;

	public $infotext = 'Automatically saves bookmark to new update on delicious.';

	public $auth_type = "oauth";

	public $defaults = array();

	private static $configuration = array(
		'signatureMethod' => 'hmac-sha1',
		'requestTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
		'authorizeUrl' => 'https://api.twitter.com/oauth/authorize',
		'accessTokenUrl' => 'https://api.twitter.com/oauth/access_token',
		'version' => '1.0',
	);





	//get empty configuration defaults
	public function GetDefaults($name)
	{
		$this->defaults = array(
			'name' => $name, //get_class($this),
			'username' => NULL,
			'postonupdate' => false,
			'consumer_key' => NULL,
			'shared_secret' => NULL,
			"updateposting_possible" => ($this->updateposting_possible ? $this->updateposting_possible : false),
			"auth_type" => ($this->auth_type ? $this->auth_type : NULL),
			'infotext' => ($this->infotext ? $this->infotext : NULL),
		);

		return $this->defaults;
	}

	//this function must be present in each social posting plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	} //GetDetails

} //class
