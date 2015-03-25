<?php

/* **************************************************************
 *  File: CacheVars.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Cache Variables Class
*
*/
class CacheVars {

	private static $cache = null;

/**
* Get
*
*/
	public static function Get($str)
	{
		return (isset(self::$cache[$str]) ? self::$cache[$str] : null);
	}

/**
* Set
*
*/
	public static function Set($key,$value)
	{
		if(!isset(self::$cache[$key]))
			self::$cache[$key] = $value;
	}

/**
* Flush All
*
*/
	public function Flush() {
		self::$cache = null;
	}

} //class