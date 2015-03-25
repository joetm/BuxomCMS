<?php

/* **************************************************************
 *  File: Session.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Session Class
*
*/
class Session
{

	private static $securitytoken;

	private static $permissions;


/**
* Create Token
*
* @access	public
* @return	string
*/
	public static function CreateToken($length = 12)
	{
		$length = intval($length);
		if(!$length) $length = 12;

		//generate securitytoken
		$tok = null;
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
		for ($i=0; $i < $length; $i++) {
			$tok .= $chars[rand(0,66)];
		}
		return $tok;
	}

/**
* Set Token
*
* @access	public
* @param	string	$token
*/
	public static function SetToken($token)
	{
		self::$securitytoken = $token;
	}
/**
* Get Token
*
* @access	public
* @return	string
*/
	public static function GetToken()
	{
		return self::$securitytoken;
	}

/*
	public static function FetchIP()
	{
		//Fetches the IP address of the current visitor
		return $_SERVER['REMOTE_ADDR'];
	}
*/

/**
* Fetch IP Address
*
* @access	public
* @return	string
*/
	public static function FetchIP($checkProxy = true)
	{
		/* Fetch IP address of current visitor. Also attempts to detect proxies etc. */

		if ($checkProxy && isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != null)
		{
			$the_ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif ($checkProxy && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != null && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
		{
				// don't use internal IP addresses (proxies)
				$internal = array(
					'10.0.0.0/8' => array(ip2long('10.0.0.0'), ip2long('10.255.255.255')),
					'127.0.0.0/8' => array(ip2long('127.0.0.0'), ip2long('127.255.255.255')),
					'169.254.0.0/16' => array(ip2long('169.254.0.0'), ip2long('169.254.255.255')),
					'172.16.0.0/12' => array(ip2long('172.16.0.0'), ip2long('172.31.255.255')),
					'192.168.0.0/16' => array(ip2long('192.168.0.0'), ip2long('192.168.255.255')),
				);
				foreach ($matches[0] AS $ip)
				{
					$ip_long = ip2long($ip);
					if ($ip_long === false OR $ip_long == -1)
					{
						continue;
					}

					$private_ip = false;
					foreach ($internal AS $range)
					{
						if ($ip_long >= $range[0] AND $ip_long <= $range[1])
						{
							$private_ip = true;
							break;
						}
					}

					if (!$private_ip)
					{
						$the_ip = $ip;
						break;
					}
				}
		}
		elseif (isset($_SERVER['HTTP_FROM']))
		{
			$the_ip = $_SERVER['HTTP_FROM'];
		}
		else
		{
			$the_ip = $_SERVER['REMOTE_ADDR'];
		}

		return $the_ip;

	} //FetchIP

} //class