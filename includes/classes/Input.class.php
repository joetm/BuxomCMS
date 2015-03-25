<?php

/* **************************************************************
 *  File: Input.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Input Class
*
*/
class Input
{
	const TYPE_NOCLEAN	= 'NOCLEAN';	// no cleaning
	const TYPE_BOOL		= 'BOOL';	// boolean
	const TYPE_INT		= 'INT';	// integer
	const TYPE_FLOAT	= 'FLOAT';	// float
	const TYPE_UINT		= 'UINT';	// unsigned integer
	const TYPE_NUM		= 'NUM';	// number
	const TYPE_UNUM		= 'UNUM';	// unsigned number
	const TYPE_UNIXTIME	= 'UNIXTIME';	// unix datestamp (unsigned integer)
	const TYPE_DATE		= 'DATE';	// date
	const TYPE_LATLNG	= 'LATLNG';	// "<latitude>,<longitude>"
	const TYPE_STR		= 'STR';	// trimmed string
	const TYPE_NOBR		= 'NOBR';	// trimmed string with no linebreaks
	const TYPE_NOTRIM	= 'NOTRIM';	// non-trimmed string
	const TYPE_NOHTML	= 'NOHTML';	// trimmed string with HTML made safe except for the quotes
	const TYPE_NOHTML_QUOTES= 'NOHTML_QUOTES';//trimmed string with HTML made safe, including quotes
	const TYPE_ARRAY	= 'ARRAY';	// array
	const TYPE_FILE		= 'FILE'; 	// file
	const TYPE_FILENAME	= 'FILENAME';	// filename (for example SEO slug)
	const TYPE_PATH		= 'PATH';	// absolute path
	const TYPE_BINARY	= 'BINARY'; 	// binary
	const TYPE_NOHTMLCOND	= 'NOHTMLCOND'; // trimmed with HTML made safe if unsafe

	const TYPE_ARRAY_BOOL		= 'ARRAY_BOOL';		// array types (unused)
	const TYPE_ARRAY_INT		= 'ARRAY_INT';
	const TYPE_ARRAY_UINT		= 'ARRAY_UINT';
	const TYPE_ARRAY_NUM		= 'ARRAY_NUM';
	const TYPE_ARRAY_UNUM		= 'ARRAY_UNUM';
	const TYPE_ARRAY_UNIXTIME	= 'ARRAY_UNIXTIME';
	const TYPE_ARRAY_STR		= 'ARRAY_STR';
	const TYPE_ARRAY_NOTRIM		= 'ARRAY_NOTRIM';
	const TYPE_ARRAY_NOHTML		= 'ARRAY_NOHTML';
	const TYPE_ARRAY_ARRAY		= 'ARRAY_ARRAY';
	const TYPE_ARRAY_FILE		= 'ARRAY_FILE';
	const TYPE_ARRAY_BINARY		= 'ARRAY_BINARY';
	const TYPE_ARRAY_NOHTMLCOND	= 'ARRAY_NOHTMLCOND';
	const TYPE_ARRAY_KEYS_INT	= 'ARRAY_KEYS_INT';
	const TYPE_ARRAY_KEYS_STR	= 'ARRAY_KEYS_STR';

	public static $booltypes = array("1", "yes", "y", "true");
	public static $LINEBREAKS = array("\r\n", "\r", "\n", "<br />", "<br>", "<br/>");

	private static $SRC = array(
		'g' => '_GET',
		'p' => '_POST',
		'r' => '_REQUEST',
		'c' => '_COOKIE',
		's' => '_SERVER',
		'e' => '_ENV',
		'f' => '_FILES'
	);

	/* variables that have already been cleaned */
	public static $cleaned_vars = array();








	public $sorting = "";
	public $order = " ORDER by `id` ASC";


/**
* Get Sort Order
*
*/
	public function GetSortOrder(){

		/***get variables***/
		//get variables
		if(empty($_GET['sort'])) $_GET['sort'] = 'id';

		switch ($_GET['sort']) {
			case 'title':
				$this->sorting = 'title';
				$this->order = " ORDER BY `title` ASC";
				break;
			case 'date':
				$this->sorting = 'date';
				$this->order = " ORDER BY `dateline` DESC";
				break;
			case 'rating':
				$this->sorting = 'rating';
				$this->order = " ORDER BY `rating` DESC";
				break;
			case 'modelname':
				$this->sorting = 'name';
				$this->order = " ORDER BY `modelname` DESC";
				break;
			default:
				$this->order = " ORDER BY `id` ASC";
				break;
		}
//		$tpl->assign("sorting", $this->sorting);
		/***get variables***/

//		return $this->order;

	} //getSortOrder

/**
* Sanitize String
*
* @ deprecated
*
*/
	public static function sanitize_str($input){

		$db = DB::getInstance();

		if(is_object($db)){
			if($db->is_connected()){
				return mysql_real_escape_string(strip_tags(trim($input)));
			}
		}
		else{
			return strip_tags(trim($input));
		}
	}

/**
* Sanitize Folder
*
* @deprecated
*
*/
	public static function sanitize_folder($folder)
	{
		$folder = strip_tags($folder);
		$folder = escapeshellcmd($folder);
		return $folder;
	}

/**
* Sanitize HTML
*
* @deprecated
*
*/
	public static function sanitize_html($input){

		$db = DB::getInstance();

		if(is_object($db)){
			if($db->is_connected()){
				return mysql_real_escape_string(htmlentities(trim($input)));
			}
		}
		else{
			return htmlentities(trim($input));
		}
	}

/**
* Clean URL
*
*/
	function cleanURL($string)
	{
		/*
		Useful for SEO blog posts for instance, where a title can be something like
		"Its over 100 degrees today!"
		which would translate to
		"its-over-100-degrees-today". So you can store the string and call it like http://examplesite.com/news/2008/its-over-100-degrees-today
		*/

	    $url = str_replace("'", '', $string);
	    $url = str_replace('%20', ' ', $url);
	    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url); // substitutes anything but letters, numbers and '_' with separator
	    $url = trim($url, "-");
	    $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);  // you may opt for your own custom character map for encoding.
	    $url = strtolower($url);
	    $url = preg_replace('~[^-a-z0-9_]+~', '', $url); // keep only letters, numbers, '_' and separator
	    return $url;

	// echo cleanURL("Shelly's%20Greatest%20Poem%20(2008)");  // shellys-greatest-poem-2008
	}


/**
* Get Post Variable
*
* @deprecated
*
*/
	public function getVar($name)
	{
	    $value = isset($_POST[$name]) ? $_POST[$name] : null;

	    if (is_string($value)) {
	        return $value = str_replace("\0", '', $value);
	    }
	}

/**
* Get Per Page
*
*/
	public static function getPerPage($input)
	{

		switch($input)
		{
			case 'tags':
				$location = 'tags';
				break;
			case 'comments':
				$location = 'comments';
				break;
			case 'models':
				$location = 'models';
				break;
			case 'faq':
				$location = 'faq';
				break;
			case 'updates':
			default:
				$location = 'updates';
				break;
		}

		//per page settings
		if (!isset($_POST['perpage']) || $_POST['perpage'] == 0){

			if (isset($_COOKIE['perpage'.$location])){
				return intval($_COOKIE['perpage'.$location]); //$perpage
			}
			else{
				//default fallback
				return 20; //$perpage
			}
		}
		else{
			$perpage = intval($_POST['perpage']);
			setcookie("perpage_".$location, $perpage);
			return $perpage;
		}

	} //getPerPage

/**
* Sorting
*
*/
	public static function sorting()
	{
		//sortorder
		$sortorder = 'DESC';
		if ( isset($_POST['sortorder'])){
			if($_POST['sortorder'] == 'ASC'){
				$sortorder = 'ASC';
			}
		}
		return $sortorder;

	} //sorting function














/**
* Clean Array
*
*/
	public static function clean_array($source, $inputarray)
	{
		$ret = array();

		$G =& $GLOBALS[self::$SRC[$source]];

		foreach ($inputarray AS $name => $type)
		{
			if (!isset(self::$cleaned_vars[$name]) OR self::$cleaned_vars[$name] !== $type)
			{
				$ret[$name] = self::clean($G[$name], $type, isset($G[$name]));
				self::$cleaned_vars[$name] = $type;
			}
		}

		return $ret;

	} //clean_array

/**
* Clean Single
*
*/
	public static function &clean_single($source, $name, $type='CLEAN')
	{

		$ret = '';

		if (!isset(self::$cleaned_vars[$name]) OR self::$cleaned_vars[$name] !== $type)
		{
			$G =& $GLOBALS[self::$SRC[$source]];

			$ret =& self::clean($G[$name], $type, isset($G[$name]));
			self::$cleaned_vars[$name] = $type;
		}

		return $ret;

	} //clean_single

/**
* Clean Variable
*
*/
	public static function &clean(&$var, $type = 'NOCLEAN', $exists = true)
	{
		if ($exists)
		{
			if (is_array($var))
			{
				foreach (array_keys($var) AS $key)
				{
					self::clean_up($var[$key], $type);
				}
			}
			else
			{
				self::clean_up($var, $type);
			}
		}
		else //input does not exist
		{
			$var = null;
		}

		return $var;
	}

/**
* Clean_up Helper function
*
* @access	public
* @param	mixed	$data
* @param	string	$type
* @return	mixed
*/
	public static function &clean_up(&$data, $type)
	{
		switch ($type)
		{
			case self::TYPE_INT:
				$data = intval($data);
				break;
			case self::TYPE_UINT:
				$data = ($data = intval($data)) <= 0 ? 0 : $data;
				break;
			case self::TYPE_FLOAT:
				$data = floatval($data);
				break;
			case self::TYPE_NUM:
				$data = strval($data) + 0;
				break;
			case self::TYPE_UNUM:
				$data = strval($data) + 0;
				$data = ($data <= 0) ? NULL : $data;
				break;
			case self::TYPE_DATE:
				$data = trim(strval($data));
				if (preg_match("~^[1-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]$~", $data) === 0)
				{
					$data = null;	//no valid date
				}
				break;
			case self::TYPE_LATLNG:
				$data = trim(strval($data));
				$pieces = explode(",", $data);
				$s = count($pieces);
				if($s != 2)
					$data = null;
				else
				{
					for($i=0; $i<$s; $i++)
					{
						$pieces[$i] = trim($pieces[$i]);
					}
					$data = implode(",", $pieces);
					if (preg_match("~^\-?\d+\.?\d*,\-?\d+\.?\d*$~", $data) == 0)
					{
						$data = null;	//no valid "<lat>,<lng>"
					}
				}
				break;
			case self::TYPE_BINARY:
				$data = strval($data);
				break;
			case self::TYPE_STR:
				$data = trim(strval($data));
				break;
			case self::TYPE_NOBR:
				$data = trim(strval($data));
				//remove linebreaks
				$data = str_ireplace(self::$LINEBREAKS, "", $data);
				break;
			case self::TYPE_NOTRIM:
				$data = strval($data);
				break;

			case self::TYPE_NOHTML:
				$data = htmlspecialchars(trim(strval($data)), ENT_NOQUOTES);
				break;

			case self::TYPE_NOHTML_QUOTES:
				$data = htmlspecialchars(trim(strval($data)));
				break;

/*
			case self::TYPE_NOHTML:
				$data = strip_tags(trim(strval($data)));
				break;
*/
			case self::TYPE_BOOL:
				$data = in_array(strtolower($data), self::$booltypes) ? 1 : 0;
				break;
			case self::TYPE_ARRAY:
				$data = (is_array($data)) ? $data : array();
				break;
			case self::TYPE_NOHTMLCOND:
			{
				$data = trim(strval($data));
				if (strcspn($data, '<>"') < strlen($data) OR (strpos($data, '&') !== false AND !preg_match('/&(#[0-9]+|amp|lt|gt|quot);/si', $data)))
				{
					// data is not htmlspecialchars because it still has characters or entities it should not
					$data = htmlspecialchars($data);
				}
				break;
			}
			case self::TYPE_FILENAME:
				$data = String::sanitize_file_name($data);
				break;

			case self::TYPE_PATH:

				//check if path only contains valid characters
				$status = String::path_chars_valid($data);

				if(!$status)	$data = null;

//				$data = String::sanitize_path($data);

				break;

			case self::TYPE_FILE:
			{
				if (is_array($data))
				{
					if (is_array($data['name']))
					{
						$files = count($data['name']);
						for ($index = 0; $index < $files; $index++)
						{
							$data['name'][$index] = trim(strval($data['name'][$index]));
							$data['type'][$index] = trim(strval($data['type'][$index]));
							$data['tmp_name'][$index] = trim(strval($data['tmp_name'][$index]));
							$data['error'][$index] = intval($data['error'][$index]);
							$data['size'][$index] = intval($data['size'][$index]);
						}
					}
					else
					{
						$data['name'] = trim(strval($data['name']));
						$data['type'] = trim(strval($data['type']));
						$data['tmp_name'] = trim(strval($data['tmp_name']));
						$data['error'] = intval($data['error']);
						$data['size'] = intval($data['size']);
					}
				}
				else
				{
					$data = array(
						'name'     => '',
						'type'     => '',
						'tmp_name' => '',
						'error'    => 0,
						'size'     => 4, // UPLOAD_ERR_NO_FILE
					);
				}
				break;
			}
			case self::TYPE_UNIXTIME:
			{
//missing



			}

			default:
			case self::TYPE_NOCLEAN:
			{
				break;
			}
		} //switch

		// remove nullbytes
		switch ($type)
		{
			case self::TYPE_STR:
			case self::TYPE_NOTRIM:
			case self::TYPE_NOHTML:
			case self::TYPE_NOHTMLCOND:
				$data = str_replace(chr(0), '', $data);
		}

		return $data;
	}

/**
* Get Checkboxes
*
* @access	public
* @return	mixed
*/
	public static function GetCheckboxes()
	{
		$input = Input::clean_array('p',array('checkbox' => 'UINT'));

		if($input['checkbox'] == null)
			return null;

		$ids = '';
		foreach($input['checkbox'] as $i){
			$ids .= "'".intval($i)."',";
		}
		if($ids)
		{
			return rtrim($ids, ",");
		}
		else
			return null;

	} //GetCheckboxes

/**
* XSS Cleaner
*
* @deprecated
*
* @access	public
* @param	string	$var
* @return	string
*/
	public function XSSCleaner($var)
	{
		/* Remove HTML characters and scripting tags */

		$find    = array('#^javascript#i', '#^vbscript#i');
		$replace = array('java script',   'vb script');

		$var = htmlspecialchars(trim($var));

		return preg_replace($find, $replace, $var);
	}

} //class