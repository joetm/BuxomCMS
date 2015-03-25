<?php

/* **************************************************************
 *  File: Request.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Request Class
*
*/
class Request
{
	private static $singleton = false;

/**
* Sanitize Request
*
* @access	public
*/
	public static function Setup()
	{
		if(!self::$singleton)
		{
			self::$singleton = true;

			//disable the deprecated and annoying magic quotes
			if( get_magic_quotes_gpc() == 1 )
			{
				$_GET = String::RemoveSlashes($_GET);
				$_POST = String::RemoveSlashes($_POST);
				$_COOKIE = String::RemoveSlashes($_COOKIE);
			}
		}

	} //Setup

} //class