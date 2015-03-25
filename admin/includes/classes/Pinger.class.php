<?php

/* **************************************************************
 *  File: Pinger.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Pinger Class
*
*/
class Pinger
{

/**
* Ping Search Engines
*
* @access	public
* @param	string	url of update
* @return	bool
*/
	public static function Ping($url) {

		//get details from database
		$ping_urls = Config::GetDBOptions('ping_urls');

		if(!defined('PHP_EOL')) define('PHP_EOL', "\n");

		$ping_urls = explode(PHP_EOL, $ping_urls);

		//trim
		$ping_urls =  array_map('trim', $ping_urls);

		if(!$ping_urls)	return false;

		if(is_array($url))
		{
			foreach($url as $u)
			{
				if(!$u) continue;
				self::PingIt($u, $ping_urls);
			}
		}
		else
		{
			if(!$url) return false;
			self::PingIt($url, $ping_urls);
		}

		return true;

	} //Ping

/**
* PingIt Helper Function
*
* @access	private
* @param	string	$input
* @return	bool
*/
	private static function PingIt($what, $ping_urls) {

		if(!is_array($ping_urls))
		{
			$target = $ping_urls.$what;

			//ping single url
			@file_get_contents($target);
		}
		else
		{
			//ping multiple urls
			foreach($ping_urls as $pu)
			{
				$target = $pu.$what;

				//ping single url
				@file_get_contents($target);
			}
		}

		return true;

	} //PingIt

} //class Pinger