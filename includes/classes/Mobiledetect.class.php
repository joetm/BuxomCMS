<?php

/* **************************************************************
 *  File: Mobiledetect.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Mobile Device Detection Class
*
*/
class Mobiledetect
{
	//default mobile redirect
	private static $url = '';

	private static $s = '';
	private static $protocol = 'http';
	private static $port = '80';

/**
* Page URL
*
* @access	public
* @return	string
*/
	private static function pageURL()
	{
		self::$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		self::$protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . self::$s;
		self::$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		//this is the current url
		return self::$protocol."://".$_SERVER['SERVER_NAME']. self::$port . $_SERVER['REQUEST_URI'];
	}

/**
* Mobile URL
*
* @access	public
* @return	string
*/
	private static function mobileURL()
	{
		self::$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		self::$protocol = self::strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . self::$s;
		self::$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		//this is the mobile url
		return self::$url . $_SERVER['REQUEST_URI'];
	}

/**
* Str Left
*
* @access	public
* @param	string	$s1
* @param	string	$s2
* @return	array
*/
	private static function strleft($s1, $s2) {
			return substr($s1, 0, strpos($s1, $s2));
	}

/**
* Redirect
*
* @access	public
*/
	public static function Redirect()
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$accept = $_SERVER['HTTP_ACCEPT'];

		$is_mobile = false;

		@include( Path::Get('path:site').'/includes/mobile_devices.php' );
		$_mdevices  = array();

//debug
//$user_agent = "test iphone test";

		$_config = Config::GetDBOptions(array(
				'mobile_device_redirect',
				'mobile_device_redirect_urls',
			   ));

		switch(true){

			case (eregi('ipod', $user_agent) || eregi('iphone', $user_agent)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['iphoneurl'];
				break;

			case (eregi('android',$user_agent)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['androidurl'];
				break;

			case (eregi('opera mini',$user_agent)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['operaurl'];
				break;

			case (eregi('blackberry',$user_agent)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['blackberryurl'];
				break;

			case (preg_match('/(palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['palmurl'];
				break;

			case (preg_match('/(windows ce; ppc;|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['windowsurl'];
				break;

			case (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|pda|psp|treo)/i',$user_agent)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['mobileurl'];
				break;

			case ((strpos($accept,'text/vnd.wap.wml') > 0) || (strpos($accept,'application/vnd.wap.xhtml+xml') > 0)):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['mobileurl'];
				break;

			case (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['mobileurl'];
			    break;

			case (in_array(strtolower(substr($user_agent,0,4)), $_mdevices )):
				$is_mobile = true;
				self::$url = $_config['mobile_device_redirect_urls']['mobileurl'];
				break;

		} // end switch

		//do not redirect if mobile url is called
		if(stripos(self::pageURL(), self::$url) !== false || self::$url == ''){
			$mobile_device_redirect = false;
		}

		/***the redirect***/
		if($is_mobile === true && $_config['mobile_device_redirect']){
//			if(substr(self::$url, 0, 4) == 'http'){ // must be a url

				header('Cache-Control: no-transform');
				header('Vary: User-Agent, Accept');

				header('Location: ' . self::mobileURL());
				exit;

//			}
		}
		/***the redirect***/

	} // function Redirect()

} //class
