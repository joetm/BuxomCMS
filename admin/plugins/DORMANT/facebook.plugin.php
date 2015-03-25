<?php

/* **************************************************************
 *  File: facebook.plugin.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

//class name of plugin must be in the form of "<name>_plugin"
class facebook_plugin extends SocialPlugin {

	public $updateposting_possible = false;

	public $auth_type = "oauth";

	private static $configuration = array(
		'' => '',
	);


	//this function must be present in each social posting plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	}

} //class
