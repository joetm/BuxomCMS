<?php

/* **************************************************************
 *  File: Helpers.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Helpers Class
*
*/
class Helpers
{
	public static $_lynx = false;
	public static $_gecko = false;
	public static $_winIE = false;
	public static $_macIE = false;
	public static $_opera = false;
	public static $_NS4 = false;
	public static $_safari = false;
	public static $_chrome = false;
	public static $_iphone = false;


/**
* Browser Detection
*
* @access	public
*/
	public function DetectBrowser()
	{
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx') !== false){self::$_lynx = true;}
			elseif(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'chrome') !== false){
				self::$_chrome = true;}
			elseif(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false ){
				self::$_safari = true;}
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false){
				self::$_gecko = true;}
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && strpos($_SERVER[		'HTTP_USER_AGENT'], 'Win') !== false){
				self::$_winIE = true;}
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false){
				self::$_macIE = true;}
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false){
				self::$_opera = true;}
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Nav') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/4.') !== false){
				self::$_NS4 = true;}
		}

		if(self::$_safari && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') !== false)
			self::$_iphone = true;

			self::$is_IE = (self::$_macIE || self::$_winIE);

	} //DetectBrowser

} //class