<?php

/* **************************************************************
 *  File: flickr.plugin.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

//class name of plugin must be in the form of "<name>_plugin"
class flickr_plugin extends SocialPlugin {

	public $updateposting_possible = true;

	public $auth_type = "rest";

	private static $configuration = array(
	);


	public static function PicturePost()
	{

//flickr API key
//(get from db)
//		'consumerKey' => 'consumerKeygoeshere',
//		'consumerSecret' => 'consumerSecretgoeshere',

	}


	//this function must be present in each social posting plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	} //GetDetails

} //class
