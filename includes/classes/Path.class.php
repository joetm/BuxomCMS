<?php

/* **************************************************************
 *  File: Path.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Path Class
*
*/
class Path
{
	/*---*/

	//definition of the temporary directory in the admin folder
	const TEMP = 'temp';

	/*---*/

	//cache the paths
	private static $cache = array();


	//slug
	public $slug = array();


/**
* Construct the Paths used throughout the script
* Uses the base paths that were defined in _config.php
*
* @access	public
* @param	string	$path
* @return	mixed	path or false if not found
*/
	public static function Get($short)
	{
		if(isset(self::$cache[$short]))
			return self::$cache[$short];

		//get prefix
		$parts = explode(":", $short);
		if(count($parts) === 1)
			//no prefix
			$prefix = null;
		else
			$prefix = $parts[0];
		unset($parts);


		//paths without prefix
		if($prefix === null)
		{
				$types = Config::Get('types');

				if(isset($types[$short]))
					self::$cache[$short] = $types[$short]; //ex.: $types['set'] = 'pics'
				else
					switch($short)
					{
						case 'temp':
							self::$cache[$short] = self::TEMP;
							break;

						//these are fallback values if the paths were messed up in the options.
						//usually, the check of the types array above should be enough
						# sub-directories used in /'members', /'thumbs', and /'free' (by default; see below)
						case 'pics':
						case 'set': //alias used in database
						case 'pictureset': //compatibility
							self::$cache[$short] = 'pics';
							break;
						case 'videos':
						case 'video': //alias used in database
						case 'videoset': //compatibility
						case 'anim': //animation is stored in video thumbnail directory
							self::$cache[$short] = 'videos';
							break;
						case 'model': //
						case 'models':
							self::$cache[$short] = 'models';
							break;
					}
		}
		//relative paths
		elseif($prefix == 'rel')
		{
				switch($short)
				{
					//path to processor plugins
					case 'rel:site/signup':
					case 'rel:/signup':
						self::$cache[$short] = String::Slash(Config::Get('_processor_scripts'),1,0);
						break;

					# admin dir // default: "/admin"
					case 'rel:admin':
						self::$cache[$short] = String::Slash(Config::Get('__admindir'), 1, 0);
						break;
					# admin temp path
					case 'rel:admin/temp':
						self::$cache[$short] = self::Get('rel:admin') . '/' . self::TEMP;
						break;
					# members directory
					case 'rel:member':
						self::$cache[$short] = String::Slash(Config::Get('__memberdir'), 1, 0);
						break;
					# freely accessible content
					# member pictures directory
					case 'rel:member/pics':
					case 'rel:member/set':
						self::$cache[$short] = self::Get('rel:member') . '/' . self::Get('set');
						break;
					# member video directory
					case 'rel:member/videos':
					case 'rel:member/video':
						self::$cache[$short] = self::Get('rel:member') . '/' . self::Get('video');
						break;
					# member model directory
					case 'rel:member/models': //!!!
					case 'rel:member/model': //!!!







						self::$cache[$short] = self::Get('rel:member') . '/' . 'model';
						break;
					# member model directory
					case 'rel:site/models': //!!!
					case 'rel:site/model': //!!!







						self::$cache[$short] = '/' . 'model';
						break;
					case 'rel:free':
						self::$cache[$short] = String::Slash(Config::Get('__freedir'), 1, 0);
						break;
					# freely accessible pictures
					case 'rel:free/pics':
					case 'rel:free/set':
						self::$cache[$short] = self::Get('rel:free') . '/' . self::Get('set');
						break;
					# freely accessible videos
					case 'rel:free/videos':
					case 'rel:free/video':
						self::$cache[$short] = self::Get('rel:free') . '/' . self::Get('video');
						break;
					# thumbnail directory
					case 'rel:thumbs':
						self::$cache[$short] = String::Slash(Config::Get('__thumbdir'), 1, 0);
						break;
					# thumbnails for picture sets
					case 'rel:thumbs/pics':
					case 'rel:thumbs/set':
						self::$cache[$short] = self::Get('rel:thumbs') . '/' . self::Get('set');
						break;
					# thumbnails for video sets
					case 'rel:thumbs/videos':
					case 'rel:thumbs/video':
						self::$cache[$short] = self::Get('rel:thumbs') . '/' . self::Get('video');
						break;
					# thumbnails for models
					case 'rel:thumbs/models':
					case 'rel:thumbs/model':
						self::$cache[$short] = self::Get('rel:thumbs') . '/' . self::Get('model');
						break;
				} //rel
		}
		//urls
		elseif($prefix == 'url')
		{
				switch($short)
				{
					case 'url:site':
					case 'url:':
						self::$cache[$short] = rtrim(Config::Get('__siteurl'), "/");
						break;

					//url rewrite
					case 'url:site/set':
					case 'url:/set':
						self::$cache[$short] = self::Get('url:site') . String::Slash(Config::Get('__url_set'), 1, 0);
						break;
					case 'url:site/video':
					case 'url:/video':
						self::$cache[$short] = self::Get('url:site') . String::Slash(Config::Get('__url_video'), 1, 0);
						break;
					case 'url:site/model':
					case 'url:/model':
						self::$cache[$short] = self::Get('url:site') . String::Slash(Config::Get('__url_model'), 1, 0);
						break;
					//path to processor plugins
					case 'url:site/signup':
					case 'url:/signup':
						self::$cache[$short] = self::Get('url:site'). self::Get('rel:site/signup');
						break;

					case 'url:admin':
						self::$cache[$short] = self::Get('url:site') . self::Get('rel:admin');
						break;
					case 'url:admin/temp':
						self::$cache[$short] = self::Get('url:admin') . '/' . self::TEMP;
						break;
					case 'url:member':
						self::$cache[$short] = rtrim(Config::Get('__memberurl'), "/");
						break;
					case 'url:member/pics':
					case 'url:member/set':
						self::$cache[$short] = self::Get('url:member') . '/' . self::Get('set');
						break;
					case 'url:member/videos':
					case 'url:member/video':
						self::$cache[$short] = self::Get('url:member') . '/' . self::Get('video');
						break;
					case 'url:free':
						self::$cache[$short] = self::Get('url:site') . self::Get('rel:free');
						break;
					case 'url:free/pics':
					case 'url:free/set':
						self::$cache[$short] = self::Get('url:free') . '/' . self::Get('set');
						break;
					case 'url:free/videos':
					case 'url:free/video':
						self::$cache[$short] = self::Get('url:free') . '/' . self::Get('video');
						break;
					case 'url:thumbs':
						self::$cache[$short] = self::Get('url:site') . self::Get('rel:thumbs');
						break;
					case 'url:thumbs/pics':
					case 'url:thumbs/set':
						self::$cache[$short] = self::Get('url:thumbs') . '/' . self::Get('set');
						break;
					case 'url:thumbs/videos':
					case 'url:thumbs/video':
						self::$cache[$short] = self::Get('url:thumbs') . '/' . self::Get('video');
						break;
					case 'url:thumbs/models':
					case 'url:thumbs/model':
						self::$cache[$short] = self::Get('url:thumbs') . '/' . self::Get('model');
						break;
				} //url
		}
		//absolute paths
		elseif($prefix == 'path')
		{
				switch($short)
				{
					case 'path:site':
					case 'path:':
						self::$cache[$short] = rtrim(__sitepath, "/");
						break;

					//path to processor plugins
					case 'path:site/signup':
					case 'path:/signup':
						self::$cache[$short] = self::Get('path:site') . self::Get('rel:site/signup');
						break;

					case 'path:admin':
						self::$cache[$short] = self::Get('path:site') . DIR_SEP . String::Slash(self::Get('rel:admin'), 0, 0);
						break;
					case 'path:admin/temp':
						self::$cache[$short] = self::Get('path:admin') . DIR_SEP . self::TEMP;
						break;
					case 'path:member':
						self::$cache[$short] = trim(Config::Get('__memberpath'), "/");
						break;
					case 'path:member/pics':
					case 'path:member/set':
						self::$cache[$short] = self::Get('path:member') . DIR_SEP . self::Get('set'); // ex.: /root/member/pics
						break;
					case 'path:member/videos':
					case 'path:member/video':
						self::$cache[$short] = self::Get('path:member') . DIR_SEP . self::Get('video');
						break;
					case 'path:free':
						self::$cache[$short] = self::Get('path:site') . DIR_SEP . String::Slash( self::Get('rel:free'), 0, 0);
						break;
					case 'path:free/pics':
					case 'path:free/set':
						self::$cache[$short] = self::Get('path:free') . DIR_SEP . self::Get('set');
						break;
					case 'path:free/videos':
					case 'path:free/video':
						self::$cache[$short] = self::Get('path:free') . DIR_SEP . self::Get('video');
						break;
					case 'path:thumbs':
						self::$cache[$short] = self::Get('path:site') . DIR_SEP . String::Slash(self::Get('rel:thumbs'), 0, 0);
						break;
					case 'path:thumbs/pics':
					case 'path:thumbs/set':
						self::$cache[$short] = self::Get('path:thumbs') . DIR_SEP . String::Slash(self::Get('set'), 0, 0);
						break;
					case 'path:thumbs/videos':
					case 'path:thumbs/video':
						self::$cache[$short] = self::Get('path:thumbs') . DIR_SEP . String::Slash(self::Get('video'), 0, 0);
						break;
					case 'path:thumbs/models':
					case 'path:thumbs/model':
						self::$cache[$short] = self::Get('path:thumbs') . DIR_SEP . String::Slash(self::Get('model'), 0, 0);
						break;
				} //path
		} //prefix

		if(isset(self::$cache[$short]))
			return self::$cache[$short];
		else
			return false;

	} //Get

} //class