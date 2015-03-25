<?php

/* **************************************************************
 *  File: Template.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

require_once OUTLINE_CLASS_PATH."/tpl.php";

if(defined('__CHARSET'))
	header("Content-type: text/html; charset=".__CHARSET);

/**
* Template Engine Class
*
*/
class Template extends OutlineTpl
{
	public $templatename;
	public $title;
	public static $theme = 'default';

	public $arrErrors = array();
	public $errorcss = array();

	public $successmessage = '';
	public $errormessage = '';

	private static $prefix;
	private $templatedir;
	private $template_file;

//	private static $Instance;
	private $debug = array();

/**
* Constructor
*
* @access	public
* @param	string	$templatename
* @param	integer	$cache_time
* @param	string	$frontend_theme
* @return	string
*/
	public function __construct ($templatename, $cache_time = 3600, $frontend_theme = '')
	{
		//set the theme
		if(!$frontend_theme)
		{
			if(defined('__theme')) self::$theme = __theme;
			else	self::$theme = 'default';
		}
		else
		{
			//allows individual output with different theme
			self::$theme = $frontend_theme;
		}

//		$this->templatename = OutlineUtil::clean($templatename, 'FILENAME');
//		$this->templatename = Input::clean($templatename, 'FILENAME');

		if(__tpl_cache_time != false && __tpl_cache_time != 0)
			$cache_time = intval(__tpl_cache_time); //set caching
		else
			$cache_time = intval($cache_time); //default caching is 1 hour

//		if(empty($templatename))
//		die('No template name specified.');

		if(empty($templatename))
			$templatename = 'index';

		//prefix for template names
		$tplargs = explode(":", $templatename, 2);
		if(count($tplargs)==1) //no ":" found -> must be a normal template
			{
				switch ($templatename)
				{
					case 'join':
						self::$prefix = 'join';
						$this->templatename = 'join';

						//database connect
						$db = DB::getInstance();

						//Get the Processor and urls
						$options['processor'] = Config::GetDBOptions('processor');

					break;
					default:
						self::$prefix = 'tpl';
						$this->templatename = $tplargs[0];
					break;
				}
			}
		elseif(count($tplargs)==2 && $tplargs[0]=='admin')
			{
				self::$prefix = 'admin';
				$this->templatename = $tplargs[1];
			}
		else	die("no valid template specified");

		//template directory
		switch (self::$prefix)
		{
			case 'admin':
				$this->templatedir = Path::Get('path:admin') . DIR_SEP . 'templates';
				$this->template_file = $this->templatedir. DIR_SEP .$this->templatename . '.tpl.html';
			break;
			case 'join':
				$this->templatedir = Path::Get('path:site/signup'). DIR_SEP . $options['processor'];
				$this->template_file = $this->templatedir. DIR_SEP .$this->templatename.'.tpl.html';
			break;
			default:
				$this->templatedir = Path::Get('path:site') . DIR_SEP . 'templates' . DIR_SEP . self::$theme;
				$this->template_file = $this->templatedir. DIR_SEP .$this->templatename.'.'.self::$prefix.'.html';
			break;
		}


		//check if exists
		if(!file_exists($this->template_file))
		{
			self::$prefix = 'tpl';
			$this->templatename = '_404';
		}

		//roots
		$roots = array(
			"tpl" =>  Path::Get('path:site') . DIR_SEP . 'templates'. DIR_SEP .self::$theme,
			"admin" => Path::Get('path:admin') . DIR_SEP . 'templates'
		);
		if(self::$prefix == 'join')
			$roots["join"] = $this->templatedir;

		parent::__construct(self::$prefix.':'.$this->templatename, array(
			"cache_time" => $cache_time,
			//defines the error_reporting level (in /includes/Outline/class/engine.php);
			//'quiet' => true,
			"quiet" => Config::Get('debug'),
			"bracket_open" =>        '{{',
			"bracket_close" =>       '}}',
			"bracket_comment" =>     '{{*',
			"bracket_end_comment" => '*}}',
			"bracket_ignore" =>     '{{ignore}}',
			"bracket_end_ignore" => '{{/ignore}}',
			"roots" => $roots,
			"plugins" => array(
/*
				//database template queries
					"Queries" => Path::Get('path:site') . "/includes/classes/Outline/plugins/Queries.php",

				//template translations
					"TplTranslation" => Path::Get('path:site') . "/includes/classes/Outline/plugins/Translation.php",

				//your custom tags
					"UserQueries" => Path::Get('path:site') . "/includes/classes/Outline/plugins/UserQueries.php",
*/
			)
		));


		/***some common variables***/
		// {{$__siteurl}} can also be accessed like this: {{#Path::Get('url:site')}}
		// {{$__adminurl}} can also be accessed like this: {{#Path::Get('url:admin')}} or {{#Path::Get('rel:admin')}}
		$this->assign("__sitename", Config::GetDBOptions('sitename'));
		$this->assign("__siteurl", Path::Get('url:site'));
		$this->assign("__adminurl", Path::Get('url:admin')); // default: "/admin"
/*
		$this->assign("__templateurl", (self::$prefix=='admin') ? Path::Get('url:admin') . '/templates' : Path::Get('url:site').'/templates/'.self::$theme);
*/
		$this->assign("__templateurl", Path::Get('url:site').'/templates/'.self::$theme);


		Zend_Registry::set('tpl', $this);

		/*--------------------------------------------------------------*/
		/*						Mobile Redirect						 	*/
		/*--------------------------------------------------------------*/

		/***Mobile Redirect***/
		if(Config::Get('mobile_device_redirect') && class_exists('Mobiledetect') && !defined('BX_CONTROL_PANEL') && self::$prefix != 'admin'){
			Mobiledetect::Redirect();
		}

	}

/**
* Copyright
*
* @access	public
*/
	public function Copyright(){

		echo PHP_EOL."<!-- Copyright ".date('Y')." ".__sitename.". Downloaded from http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']." -->";
	}

/**
* Display
*
* @access	public
* @return	string
*/
	public function display()
	{
		$_page = array(
				'successmessage' => $this->successmessage,
				'errormessage' => $this->errormessage,
				"templatename" => $this->templatename,
				"title" => $this->title,
//				"_PAGE_GET_VAR" => _PAGE_GET_VAR,
				);

		$this->assign("_page", $_page);

		try
		{
			return ( parent::display() );
		}
		catch (OutlineException $e)
		{
			//template was not found! Dammit!
//			var_dump($e);

			//add the error to the debug output
			if(Config::Get('debug') === true){
				$this->debug[] = $e->getMessage();
			}
		}
	}

/**
* Load PHP logic for the template
*
* @access	public
*/
/*
//deprecated -> now in Router
	public function controller(){

		global $tpl, $translate;

		$controller = $this->templatedir.'/'._controller_dir.'/'.$this->templatename.'.php';

		if(file_exists($controller))
			include_once $controller;
		else
			require_once $this->templatedir.'/'._controller_dir."/_404.php";
	}
*/

	public function getTemplateDir()
	{
		return $this->templatedir;
	}

/**
* Debug
*
* @access	public
*/
	public function debug()
	{
		//cancel caching if debug mode
		Caching::cancel();

		$dmsg = '';

		if(Config::Get('debug') === true){
			$dmsg = "<div class='debug'>";

			if(!empty($this->debug))
			{
				foreach($this->debug as $dm)
					$dmsg .= $dm."<br>";
			}

			$dmsg .= "Loaded in: ".(microtime(true) - __firstload). " seconds.".PHP_EOL;

			if (function_exists('memory_get_usage'))
			{
				$dmsg .= ' Memory Usage: ' . number_format(memory_get_usage() / 1024) . 'KB';
			}

			$dmsg .= "<br>".PHP_EOL;

			//add 1 extra query to query counter for ajax in admin area
//			$tpl = Zend_Registry::get('tpl');
			//ajax query debug text
			$ajaxonpage = false;
			if(self::$prefix == 'admin')
			{
				switch($this->templatename)
				{
					case 'admin_accounts':
					case 'admin_activitylog':
					case 'admin_comments':
					case 'admin_docs':
					case 'admin_faq':
					case 'admin_login_history':
					case 'admin_members':
					case 'admin_models':
					case 'admin_ratings':
					case 'admin_tags':
					case 'admin_updates':
						$ajaxonpage = true;
					break;
					default:
						$ajaxonpage = false;
					break;
				}
			}
			if($ajaxonpage)	//preg_match("~admin~", $tpl->templatename)
				$_numqueries = DB::$qcount + 1;
			else
				$_numqueries = DB::$qcount;

			$dmsg .= "Database queries: " . $_numqueries;
			if($ajaxonpage) $dmsg .= " (1 ajax query)".PHP_EOL;

			if(__showdebugqueries)
			{
				$dmsg .= "<br>";
				$dmsg .= DB::$queries;
			}

			$dmsg .= "<br><br>";
			$dmsg .= "</div>";

			echo $dmsg;

		}
	}//debug

/**
* Error Message
*
* @access	public
* @param	string	$msg
*/
	public function errormessage($msg){
		if (isset($this->errormessage)){
			$this->errormessage .= $msg;
		}
		else{
			$this->errormessage = $msg;
		}
	}
/**
* Success Message
*
* @access	public
* @param	string	$msg
*/
	public function successmessage($msg){
		if (isset($this->successmessage)){
			$this->successmessage .= $msg;
		}
		else{
			$this->successmessage = $msg;
		}
	}

/**
* Redirect
*
* @access	public
* @param	string	$url
*/
	public function redirect($url){
		if(!headers_sent())
		{
			header('Location: '.$url);
			exit();
		}
	}

/**
* Show Permission Denied Error Page
*
*/
	public static function PermissionDenied()
	{
		$_403 = new permissionerror();
		$_403->ShowIndex();
		die();
	}

	/* overwrite the assign and apply methods to sanitize the output */

/*
	public function assign($var, $value) {

		$this->vars[$var] = Arr::array_htmlentities($value);

	}

	public function assign_by_ref($var, &$value) {

		$this->vars[$var] = Arr::array_htmlentities(&$value);

	}

	public function apply($array, $overwrite = true) {

		$array = Arr::array_htmlentities($array);

		$this->vars = $overwrite ? ($array + $this->vars) : ($this->vars + $array);

	}
*/


} //class
