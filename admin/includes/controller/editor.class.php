<?php

/* **************************************************************
 *  File: editor.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class editor extends BaseController
{
	private static $errors = '';
	private static $errorcss = array();
	private static $success = false;

	private static $fileArray = array();

	private static $frontend_theme = 'default';
	private static $tpl_folder = '../';

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

		/***page setup***/
		$tpl->title = $translate->_("Templates");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***disconnect***/
		unset($db);


		//assign template list
		sort(self::$fileArray); //show header / footer on top
		if (!empty(self::$fileArray)) $tpl->assign("templates", self::$fileArray);


		if(isset($_POST['template']))
		{
			$template = Input::clean($_POST['template'], 'FILENAME');

			if(is_readable(self::$tpl_folder.DIR_SEP.$template))
				$templatecontent = file_get_contents(self::$tpl_folder.DIR_SEP.$template);
			elseif(is_readable(self::$tpl_folder.DIR_SEP.$template."/includes/"))
				$templatecontent = file_get_contents(self::$tpl_folder."/includes/".$template);

		}

		if (isset($templatecontent)) $tpl->assign("templatecontent", htmlspecialchars($templatecontent, ENT_NOQUOTES));

		//for pre-selection
		if (isset($template)) $tpl->assign("template", $template);

		//error/success
		if (self::$success) $tpl->successmessage($translate->_("Done"));
		if (self::$errors)  $tpl->errormessage(self::$errors);
		if (self::$errorcss)$tpl->assign("errorcss", self::$errorcss);

		//internationalization
		$_t = $translate->translateArray(array(
			"select_template" => "Select Template",
			"save" => "Save",
			"select" => "Select",
		));
		$tpl->assign("_t", $_t);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* GetFileList
*
* @access private
*/
	private function GetFileList()
	{
		//check if folder exists
		if(!self::$tpl_folder)
			throw new Exception("Template folder not found.");

		if(!is_readable(self::$tpl_folder))
			throw new Exception("Cannot read template folder.");


		$mime_type = ".tpl.html";
		$dir = opendir(self::$tpl_folder);
			while( $entry = readdir( $dir ))
			{
				if($entry == "." || $entry == "..") continue;

				if ( substr($entry, -9) == $mime_type )
					self::$fileArray[] = $entry;
			}
		closedir($dir);

		//extra: include dir
		$includes_folder = self::$tpl_folder."/includes";
		//check if folder exists
		if(is_readable($includes_folder)) //includes folder is optional
		{
			$mime_type = ".tpl.html";
			$dir = opendir($includes_folder);
				while( $entry = readdir( $dir ))
					{
					if($entry == "." || $entry == "..") continue;

					if ( substr($entry, -9) == $mime_type )
						self::$fileArray[] = $entry;
				}
			closedir($dir);
		}
	} //GetFileList

/**
* save
*
* @access private
*/
	private function save()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		$templatename = String::Slash(Input::clean_single('p','template','NOHTML'), 0, 0);

		//security check
		if(!in_array($templatename, self::$fileArray))
			throw new Exception("Invalid template name.");

		$templatepath = self::$tpl_folder . DIR_SEP . $templatename;

		$templatecontent = htmlspecialchars_decode(
		Input::clean_single('p','templatecontent','STR'), ENT_NOQUOTES);

		if(!file_exists($templatepath))
			throw new Exception($translate->_("Template Not Found"));
		elseif(is_writable($templatepath))
		{
			$fp = fopen($templatepath, 'w');

			//lock file
			flock($fp, LOCK_EX | LOCK_NB, $blocked);
			if(!$blocked)
			{
				//write
				if (fwrite($fp, $templatecontent) === false) {
					throw new Exception("Could not write to file.");
   				}
   				else
   				{
					self::$success = true;
					Logger::AdminActivity('saved template: '.$templatename, $templatename);
				}
			}
			fclose($fp);
		}
		else
			throw new Exception($translate->_("Template Not Writable"));

		unset($_POST);

	} //save

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		Authentification::CheckPermission('administrator');

		//get the current template set's folder
		self::$frontend_theme = String::Slash(Config::Get('frontend_theme'), 0, 0);
		self::$tpl_folder = Path::Get('path:site').DIR_SEP."templates".DIR_SEP.self::$frontend_theme;

		//get translation
		$translate = Zend_Registry::get('translate');

		//get templates
		try
		{
			self::GetFileList();
		}
		catch (Exception $e){
			self::$errors = $e->getMessage();
		}

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try
			{
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if(isset($_POST['submit']) && Authentification::CheckPermission('administrator'))
				{
					# save
					$this->save();
				}
			}
			catch (Exception $e)
			{
				self::$errors = $translate->_('Error').': '. $e->getMessage() . PHP_EOL;
			}
		}

		$this->index();

	} //showIndex

} //class
