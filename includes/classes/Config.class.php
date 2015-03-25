<?php

/* **************************************************************
 *  File: Config.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*--------------------------------------------------------------*/

/**
*	SAFETY
*
*	@default:	__sitepath
*/
	@ini_set("open_basedir", __sitepath);

/**
*	CACHING
*
*	@default:	__sitepath."/includes/cache"
*/
	define("__CACHE_PATH", __sitepath . "/includes/cache");

/**
*	Error Reporting Levels (used in Template Engine)
*
*	@default:	__sitepath."/includes/cache"
*/
	define('__ERROR_REPORTING_QUIET', 'E_ALL & ~E_NOTICE & ~E_STRICT');
	define('__ERROR_REPORTING_DEBUG', 'E_ALL | E_NOTICE | E_STRICT');


//load template configuration
require_once dirname(__FILE__)."/Outline/config.dist.php";


/**
* Config Class
*
*/
final class Config {

	private static $config = null;

	private static $cache = null;

/**
* Constructor
*
* @access	public
*/
	public function __construct()
	{
		//get $_config array
		require realpath(dirname(__FILE__)."/../../_config.php");

		self::$config = $_config;
	}

/**
* Defaults
*
* Tries to set some environment variables.
*
* @access	public
*/
    public static function Defaults()
    {
		if(self::$config['debug']) define('__firstload', microtime(true));

		try
		{
			@set_magic_quotes_runtime(0);
			@set_time_limit(0);
		    @ini_set('zend.ze1_compatibility_mode', 'Off');
		//    @ini_set('pcre.backtrack_limit', 1000000);
		    @ini_set('memory_limit', -1);
		}
		catch(Exception $e) {
			die('Could not load defaults.<br />' . __FILE__ . ' Line ' . __LINE__);
		}

    } //Defaults

/**
* Autoload
*
* @access	public
* @return	string
*/
	public static function Autoload()
	{

		//get autoload database options
		self::GetAllDBOptions(true); //true = only autoload

		/*--------------------------------------------------------------*/
		/*                        Site Variables                        */
		/*--------------------------------------------------------------*/

		if(isset(self::$cache['sitename']))
			define('__sitename', self::$cache['sitename']);

		//add to config
		foreach(self::$cache as $key => $val)
		{
			self::$config[$key] = $val;
		}

		/*--------------------------------------------------------------*/
		/*                      Theme Configuration                     */
		/*--------------------------------------------------------------*/

		/***theme set to use for frontend***/
		if(isset(self::$cache['frontend_theme']) && !defined('__theme'))
			define('__theme',  self::$cache['frontend_theme'] );
		else
			define('__theme', 'default');

		/***show statistics in header***/
		// Showing the header statistics ( ex.: "23.87 GB, 23451 pictures, 54 videos")
		// Will increase the page load time.
		// You should use element caching when showing the header stats.
		if(isset(self::$cache['showheaderstats']))
			define('__showheaderstats', true );
		else
			define('__showheaderstats', false );

		/*--------------------------------------------------------------*/
		/*					Mobile Redirect Config						*/
		/*--------------------------------------------------------------*/

		/***Mobile Configuration***/
		//Globally turn off all mobile redirects by setting this to false.
//		if(isset(self::$cache['mobile_device_redirect']))
//			self::$config['mobile_device_redirect'] = self::$cache['mobile_device_redirect'];
//		else
//			self::$config['mobile_device_redirect'] = false;

		//You can define different redirects depending on the matching devices.
		//Urls can be relative or even point to another website.

		if(String::is_serialized(self::$cache['mobile_device_redirect_urls']))
			self::$config['redirect_url'] = unserialize(self::$cache['mobile_device_redirect_urls']);
		else
			self::$config['redirect_url'] = self::$cache['mobile_device_redirect_urls'];

//		unset(self::$cache['mobile_device_redirect_urls']);

		# IPhone and IPod Touch:	['redirect_url']['_iphoneurl']
		# Android Phones:		['redirect_url']['_androidurl']
		# Opera Mini:			['redirect_url']['_operaurl']
		# Blackberry:			['redirect_url']['_blackberryurl']
		# Palm:				['redirect_url']['_palmurl']
		# Windows Mobile:		['redirect_url']['_windowsurl']
		# Other mobile devices:		['redirect_url']['_mobileurl']
		/***end Mobile Configuration***/

	} //Autoload

/********************/
/*	  Config Vars	*/
/********************/

/**
* Get Single Config Setting
*
* @access	public
* @param	string	$setting
* @return	string
*/
    public static function Get($setting)
    {
        return isset(self::$config[$setting]) ? self::$config[$setting] : null;
    }

/**
* Get Full Config Array
*
* @access	public
*/
    public static function GetAll()
    {
        return self::$config;
    }

/**
* Set Config Setting
*
* @access	public
* @param	string
* @param	string
*/
    public static function Set($key, $value)
    {
		self::$config[$key] = $value;
    }

/********************/
/*	  	 Cache		*/
/********************/

/**
* Cache Exists
*
* @access	public
* @return	bool
*/
	public static function CacheExists($what)
	{
		if(isset(self::$cache[$what]))
			return true;
		else
			return false;
	}

/**
* Clear Single Cache Item
*
* @access	public
*/
	public static function ClearCacheItem($what)
	{
		if(self::CacheExists($what))
			unset(self::$cache[$what]);
	}

/**
* Clear Cache
*
* @access	public
*/
	public static function ClearCache()
	{
		if(isset(self::$cache))
			foreach(self::$cache as $key => $val)
				unset(self::$cache[$key]);

		//file cache
		$cache = Caching::Setup('element', true, 86000);
		$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	}

/********************/
/* DB Config Options */
/********************/

/**
* Get All Options
*
* @access	public
* @param	bool	$autoloadonly
* @return	array
*/
	public static function GetAllDBOptions($autoloadonly = true)
	{
//		if($autoloadonly === true && self::$cache) return self::$cache;

		//caching
		$cache = Caching::Setup('element', true, 86000);

		$identifier = ($autoloadonly === true ? 'autoload':'dboptions');

		if(!$options = $cache->load($identifier)) {

			/*** connect ***/
			$db = DB::getInstance();

			$where = ($autoloadonly === true)?" WHERE `autoload` = '1'":"";

			$result = $db->query("SELECT `key`, `value` FROM `bx_options`".$where);

			$options = array();
			while($row = mysql_fetch_assoc($result))
			{
				//parse new line characters in output
				//currently, only 2257info contains new line breaks
//				if($row['key'] == '2257info')
//					$row['value'] = String::ReplaceNewLine($row['value']);



				//is serialized data?
				if(String::is_serialized($row['value']))
					$row['value'] = unserialize($row['value']);

				$options[$row['key']] = $row['value'];
			}

			$cache->save($options, $identifier, array($identifier, 'DBOptions', 'options'));
		}

		foreach($options as $key => $value)
				self::$cache[$key] = $value;

		return $options;

	} //GetAllDBOptions

/**
* Get Options
*
* @access	public
* @param	array	$what
* @return	array
*/
	public static function GetDBOptions($what)
	{
		if(empty($what)) return false;

		/*** connect ***/
		$db = DB::getInstance();

		/*** only one option ***/
		if (!is_array($what) || count($what) === 1)
		{
			//reduce array
			if(is_array($what)) $what = $what[0];

			if(isset(self::$cache[ $what ]))
			{
				//is serialized data?
				if(String::is_serialized( self::$cache[$what] ))
					return unserialize( self::$cache[$what] );
				else
					return self::$cache[$what];
			}
			else
			{
				$value = $db->Column("SELECT `value` FROM `bx_options` WHERE `key` =?", array($what));

				//is serialized data?
				if(String::is_serialized($value))
					self::$cache[$what] = unserialize($value);
				else
					self::$cache[$what] = $value;

				return self::$cache[$what];
			}
		}
		/*** more than one option item ***/
		else
		{
			$s = "";
			$arr = array();
			foreach($what as $w){

				if(empty($w)) continue;

				if(isset(self::$cache[$w]))
				{
					//skip database query
					$cached[$w] = self::$cache[$w];
				}
				else
				{
					$s .= "`key`=? OR ";
					$arr[] = $w;
				}
			}
			//remove trailing " OR "
			$s = substr_replace($s, "", -4);
			$s = $db->Prepare($s, $arr);
			unset($arr);

			$options = array();
			if(!empty($s))
			{
				$result = $db->query("SELECT `key`,`value` FROM `bx_options` WHERE ".$s);

				while($row = mysql_fetch_assoc($result))
				{
					//is serialized data?
					if(String::is_serialized($row['value']))
						$row['value'] = unserialize($row['value']);

					$options[$row['key']] = self::$cache[$row['key']] = $row['value'];
				}
			}

			if(isset($cached))
				$options = array_merge($options, $cached);

			return $options;
		}

	} //GetDBOptions

/**
* Write Template XML
*
* @access	public
*/
	public static function WriteTemplateXML()
	{
		$theme = Input::clean($_POST['options']['frontend_theme'], 'FILENAME');
//		$theme = self::GetDBOptions('frontend_theme');

		if(!$theme) return false;

		$path = Path::Get('path:site').'/templates/'.$theme.'/_config.xml';

		if(!file_exists($path))
			die('Could not find the _config.xml in template directory');

		require "Zend/Config/Writer/Xml.php";
		$Zendconf = new Zend_Config(array(), true);

		//root
		$Zendconf->templateconfig = array();

		//autmatic thumbnails grabbing size:
		//only one size pair each.
			$Zendconf->templateconfig->picturegrab_thumbnailsize = array();
			if(isset($_POST['options']['picturegrab_thumbnailsize']))
				$Zendconf->templateconfig->picturegrab_thumbnailsize = self::$config['picturegrab_thumbnailsize'] = Input::clean($_POST['options']['picturegrab_thumbnailsize'], 'UINT');

			$Zendconf->templateconfig->videograb_thumbnailsize = array();
			if(isset($_POST['options']['videograb_thumbnailsize']))
				$Zendconf->templateconfig->videograb_thumbnailsize = self::$config['videograb_thumbnailsize'] = Input::clean($_POST['options']['videograb_thumbnailsize'], 'UINT');

		//model sizes
			$insert = array(); //multiple sizes ordered by internal_id
			$Zendconf->templateconfig->model_thumbnailsize = array();
			$Zendconf->templateconfig->model_thumbnailsize->size = array();
			if(isset($_POST['options']['model_thumbnailsize']))
			{
				foreach($_POST['options']['model_thumbnailsize'] as $pot)
					$insert[] = self::$config['model_thumbnailsize'] = Input::clean($pot, 'UINT');
			}
			if(!empty($insert))
				$Zendconf->templateconfig->model_thumbnailsize->size = $insert;


		//picture set sizes
			$insert = array(); //multiple sizes ordered by internal_id
			$Zendconf->templateconfig->picture_thumbnailsize = array();
			$Zendconf->templateconfig->picture_thumbnailsize->size = array();
			if(isset($_POST['options']['picture_thumbnailsize']))
			{
				foreach($_POST['options']['picture_thumbnailsize'] as $pot)
					$insert[] = self::$config['picture_thumbnailsize'] = Input::clean($pot, 'UINT');
			}
			if(!empty($insert))
				$Zendconf->templateconfig->picture_thumbnailsize->size = $insert;

		//video set sizes
			$insert = array(); //multiple sizes ordered by internal_id
			$Zendconf->templateconfig->video_thumbnailsize = array();
			$Zendconf->templateconfig->video_thumbnailsize->size = array();
			if(isset($_POST['options']['video_thumbnailsize']))
			{
				foreach($_POST['options']['video_thumbnailsize'] as $pot)
					$insert[] = self::$config['video_thumbnailsize'] = Input::clean($pot, 'UINT');
			}
			if(!empty($insert))
				$Zendconf->templateconfig->video_thumbnailsize->size = $insert;


		/* write */
		$writer = new Zend_Config_Writer_Xml();
		$writer->write($path, $Zendconf);

		unset($Zendconf);
		unset($writer);

	} //WriteTemplateXML

/**
* Read Template XML
*
* @access	public
*/
	public static function ReadTemplateXML()
	{
		$theme = self::GetDBOptions('frontend_theme');

		if(!$theme) return false;

		$path = Path::Get('path:site').'/templates/'.$theme.'/_config.xml';

		if(!file_exists($path))
			die('Could not find the _config.xml in template directory');

		require_once "Zend/Config/Xml.php";

		try{
			$config = new Zend_Config_Xml($path, 'templateconfig');
		}
		catch(Zend_Config_Exception $e)
		{

		}

		$options = $config->toArray();


		foreach($options as $key => $val)
		{
			switch($key)
			{
				case 'model_thumbnailsize':
				case 'picture_thumbnailsize':
				case 'video_thumbnailsize':

					//remove the size key
					if(isset($options[$key]['size']))
					{
						$options[$key] = $options[$key]['size'];
					}

					//add the internal_id "0" for single array elements
					if(isset($options[$key]['width']))
					{
						$val = $options[$key];
						unset($options[$key]);
						$options[$key][0] = $val;
					}
					continue;
					break;
				case 'picturegrab_thumbnailsize':
				case 'videograb_thumbnailsize':
				default:
					//no processing needed
					break;
			}
		}

		return $options;

	} //ReadTemplateXML

} //class
