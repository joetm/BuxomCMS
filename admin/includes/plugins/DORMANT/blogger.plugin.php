<?php

/* **************************************************************
 *  File: blogger.plugin.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

//class name of plugin must be in the form of "<name>_plugin"
class blogger_plugin extends SocialPlugin {

	public $updateposting_possible = false;

	public $auth_type = "rest";

	private static $configuration = array(
		'' => '',
	);


	//this function must be present in each social posting plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	} //GetDetails

} //class
