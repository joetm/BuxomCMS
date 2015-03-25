<?php

/* **************************************************************
 *  File: crop_popup.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class crop_popup extends BaseController
{
	private static $input = array();

	private static $token = false;

/**
* index
*
* @access private
*/
	private function index()
	{
		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

		//get config
		$_config['thumbnailsizes'] = Config::GetDBOptions('picturegrab_thumbnailsize');

		/***page setup***/
		$tpl->title = $translate->_("Crop Thumbnail");
		/***page setup***/

		//get input
		self::$input = Input::clean_array('g', array(
							"iw" => "UINT",
							"ih" => "UINT",
							"img_id" => "NOHTML",
							"f" => "NOHTML",
							));

		/***database connect***/
		$db = DB::getInstance();

			//get temp data
			$temp = $db->FetchAll("SELECT `value` FROM `bx_temp` WHERE `key`='folder' OR `key`='internal_slug'");

			self::$input['folder'] = rtrim(Input::clean($temp[0]['value'],'NOHTML'),"/");
			self::$input['internal_slug'] = Input::clean($temp[1]['value'],'FILENAME');

			unset($temp);

		/***database disconnect***/
		unset($db);



		//if the image is cropped for a second time, it has random stuff attached to prevent caching
		//remove this random stuff now:
		$str = explode("?", basename(self::$input['f']));
		self::$input['f'] = Input::clean($str[0], 'FILENAME');

		$tpl->assign("filename", self::$input['f']);
		$tpl->assign("internal_slug", self::$input['internal_slug']);
		$tpl->assign("img_id", self::$input['img_id']);

		//make sure that temp directory is empty
//		Filehandler::EmptyTempDir();

		$temppath = Path::Get('path:admin/temp'). DIR_SEP .self::$input['f'];
		$tempurl = Path::Get('rel:admin/temp').'/'.self::$input['f'];
		$tpl->assign("tempurl", $tempurl);

//		echo "Member dir: ".String::Slash(self::$input['folder'],1,0).'/'.self::$input['f']."<br>";
//		echo "Temp dir: ".Path::Get('path:admin/temp').'/'.self::$input['f']."<br>";



		//copy the original image from the member area to the temp area
		if(!Filehandler::smartCopy(self::$input['folder'].DIR_SEP.self::$input['f'],
		$temppath))
		{
			die("Could not copy the image to the temp area.");
		}

		//image size
		$img_width  = intval($_GET['iw']);
		$img_height = intval($_GET['ih']);
		$tpl->assign("img_width", $img_width);
		$tpl->assign("img_height", $img_height);

		//div 0 check!
		if ($img_height != 0)
		{
			$img_whratio = $img_width / $img_height;
		}
		else
		{
			$img_whratio = $_config['thumbnailsizes']['width'] / $_config['thumbnailsizes']['height'];
		}
		$tpl->assign("img_whratio", $img_whratio);

		$aspectratio = $_config['thumbnailsizes']['width'] / $_config['thumbnailsizes']['height'];
		$tpl->assign("aspectratio", $aspectratio);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"selectvisible" => "Select visible pics",
			"visiblepicsexplain" => "Visible pics explanation",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		//$tpl->debug();

	} //index

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		self::$token = Session::GetToken();

		$this->index();
	}

} //class
