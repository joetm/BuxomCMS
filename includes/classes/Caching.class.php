<?php

/* **************************************************************
 *  File: Caching.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Caching Class
*
*	<code>
*	$cache = Caching::Setup('element', false, 3600); //seconds are optional
*	//type: 'element' or 'page'
*	//automatic serialization: false or true
*	//cache for 3600 seconds
*	if(!$content = $cache->load('identifier')) {
*		$content = '//do something';
*		$cache->save($content, 'identifier', array('tagA','tagB'));
*	}
*	</code>
*
*/
class Caching {

	const PAGE = "page";
	const ELEMENT = "element";

	private static $Pageinstance = false;
	private static $Coreinstance = false;

	public static $cache;

	public static $elementcache = false;
	public static $pagecache = false;

/**
* Setup
*
*/
	public static function Setup ($type, $asz = false, $seconds = false)
	{
/*
//problem: wird noch vor dem Autoload geladen!!!

		//only if options->caching = true
		if(Config::GetDBOptions('caching'))
		{










			//would be better to turn it all off...
			$seconds = 0;
		}
*/

			require_once "Zend/Cache.php";

			//automatic serialization
			$serialization = false;
			if($asz === true) $serialization = true;

			if($type == self::PAGE && __pagecache)
			{
				//already created instance?
				if(self::$pagecache) return self::$pagecache;

				if($seconds)	$cachetime = intval($seconds);
				else		$cachetime = __pagecache;

				$debugheader = true;
				$prefix = "page_";
				$func = "Page";
			}
			elseif($type == self::ELEMENT && __elementcache)
			{
				//already created instance?
				if(self::$elementcache) return self::$elementcache;

				if($seconds)	$cachetime = intval($seconds);
				else		$cachetime = __elementcache;

				$debugheader = false;
				$prefix = "core_";
				$func = "Core";
			}
			else
				return false;

			$fileperms = Config::Get('__filePermission');
			if(empty($fileperms)) $fileperms = 0777;
			$folderperms = Config::Get('__folderPermission');
			if(empty($folderperms)) $folderperms = 0777;

			//cache setup
			$frontendOptions = array(
						'lifetime' => $cachetime,
						'debug_header' => $debugheader, // will break layout in IE ('Quirks Mode')
						'cache_id_prefix' => $prefix,
						'automatic_serialization' => $serialization,
						'ignore_user_abort' => true,
						);
			$backendOptions = array(
						'cache_dir' => __CACHE_PATH,
//						'hashed_directory_level' => 1,
						'hashed_directory_umask' => $folderperms,
						'cache_file_umask' => $fileperms,
						'file_name_prefix' => 'bx',
						);

			if($type == self::PAGE && __pagecache && !self::$Pageinstance) //do not cache the page twice
			{
				self::$pagecache = Zend_Cache::factory(
									$func,
									'File',
									$frontendOptions,
									$backendOptions);

				self::$Pageinstance = true;

				//save cache object for later use
				Zend_Registry::set('pagecache', self::$pagecache);

				//cache the whole page
				self::$pagecache->start();

				//return cache object
				return self::$pagecache;
			}
			if($type == self::ELEMENT && __elementcache) //&& !self::$Coreinstance
			{

				self::$elementcache = Zend_Cache::factory(
									$func,
									'File',
									$frontendOptions,
									$backendOptions);

				self::$Coreinstance = true;

				//save cache object for later use
//				Zend_Registry::set('corecache', self::$elementcache);

				//return cache object
				return self::$elementcache;
			}

	}//Setup

/**
* Cancel Caching
*
*/
	public static function cancel(){

		if(self::$pagecache)
			self::$pagecache->cancel();

	} //cancel

/**
* Clear Statistics Cache
*
*/
	public static function ClearStatsCache(){
		//clear stats cache
		if(!Zend_Registry::isRegistered('headercache'))
			$headercache = self::Setup('element', true, __headerstatscache); //with serialization
		else
			$headercache = Zend_Registry::get('headercache');

			$headercache->remove('headerstats');

		unset($headercache);

	} //ClearStatsCache

} //class
