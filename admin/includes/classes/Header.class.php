<?php

/* **************************************************************
 *  File: Header.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Header Class
*
*/
class Header
{

/**
* Set Headers
*
* @access	public
*/
	public static function SetHeaders()
	{
		header("Content-type: text/html; charset: UTF-8");

		//no-cache headers
//		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	}

} //class Header
