<?php

/* **************************************************************
 *  File: options_theme.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class options_theme extends BaseController
{
	const INTERNAL_ID = 'internal_id';
	const WIDTH = 'width';
	const HEIGHT = 'height';

	private static $token = false;

	private static $success = false;

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
		$tpl->title = $translate->_("Thumbnail")." ".$translate->_("Options");
		/***page setup***/

		//clear cache
		Config::ClearCache();

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			/***options***/
			$options = array();
			$options = Config::GetDBOptions(array(
				'frontend_theme',
				'thumbnailsharpen', 'thumbnailquality', 'num_video_screencaps',
				'model_thumbnailsize', 'picture_thumbnailsize', 'video_thumbnailsize',
				'picturegrab_thumbnailsize', 'videograb_thumbnailsize',
				'make_anim',
				'html5',
				'default_base',
			));
			/***options***/


			//check if there are missing entries
			if(empty($options['model_thumbnailsize'])
				|| empty($options['picture_thumbnailsize'])
				|| empty($options['video_thumbnailsize'])
				|| empty($options['picturegrab_thumbnailsize'])
				|| empty($options['videograb_thumbnailsize']))
			{
				//could not find info about thumbnails in database
				//check if the info is stored in the template folders _config.xml
				//so this piece of code is run on first load of theme options page

				$tplconfig = Config::ReadTemplateXML();

				//write the info to the database
				//next time, the values will be fetched from the database instead of the xml file

				if(!empty($tplconfig))
				{
					//make room for new values
					$db->Update("DELETE FROM `bx_options`
							WHERE `key` LIKE '%_thumbnailsize'");

					$sql = "INSERT INTO `bx_options` (`key`, `value`, `autoload`) VALUES ";
					foreach($tplconfig as $key => $val)
					{
						$sql .= $db->Prepare("(?, ?, '1'),", array($key, serialize($val)));
					}
					$sql = rtrim($sql, ",");
					$db->Update($sql);
					unset($sql);
				}

				//merge the config that we got from the template into the database config
				$options = array_merge($options, $tplconfig);
			}

			//var_dump($options['model_thumbnailsize']);
			//echo "<br>";

		/***disconnect***/
		unset($db);

		//themes
		$themes = array();
		//get directories in template folder
		$themes = Tools::getDirectories( Path::Get('path:site').DIR_SEP.'templates' );

		/***TEMPLATE ASSIGNMENTS***/

		//options
		if(isset($options)) $tpl->assign("options", $options);

		//themes
		if(isset($themes)) $tpl->assign("themes", $themes);

		//error/success
		if (self::$success || isset($_GET['success']))
			$tpl->successmessage($translate->_("Done"));

		//internationalization
		$_t = $translate->translateArray(array(
			"frontend_theme" => "Frontend Theme",
			"height" => "Height",
			"internal_id" => "Internal ID",
			"num_video_framegrabs" => "Number of Video Framegrabs",
			"submit" => "Submit",
			"theme" => "Theme",
			"thumbnail_sharpening" => "Thumbnail Sharpening",
			"thumbnailprocessing" => "Thumbnail Processing",
			"thumbnailquality" => "Thumbnail Quality",
			"thumbnailsizes" => "Thumbnail Sizes",
			"update" => "Update",
			"width" => "Width",
			'make_anim' => 'Create GIF Animation',
		));
		$tpl->assign("_t", $_t);

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* Save Options
*
* @access private
*/
	private function SaveOptions()
	{
		//save the thumbnail sizes to the template's _config.xml file
		//store the values in the database for easier access


		//check if frontend theme was changed.
		//if theme was changed, do not save the thumbnail info
		//it would otherwise overwrite the other template

		if($_POST['options']['frontend_theme'] === Config::GetDBOptions('frontend_theme'))
		//newtheme = oldtheme
		{
			//write XML
			//->makes the theme and its thumbnail sizes transferrable
			Config::WriteTemplateXML();


			//clean input
//			Input::clean(@$_POST['options']['model_thumbnailsize'],'UINT');
//			Input::clean(@$_POST['options']['picture_thumbnailsize'],'UINT');
//			Input::clean(@$_POST['options']['video_thumbnailsize'],'UINT');
//			Input::clean(@$_POST['options']['picturegrab_thumbnailsize'],'UINT');
//			Input::clean(@$_POST['options']['videograb_thumbnailsize'],'UINT');







			//store new config
//			Config::Set('picturegrab_thumbnailsize',$_POST['options']['picturegrab_thumbnailsize']);
//			Config::Set('videograb_thumbnailsize',	$_POST['options']['videograb_thumbnailsize']);
//			Config::Set('model_thumbnailsize',	$_POST['options']['model_thumbnailsize']);
//			Config::Set('picture_thumbnailsize',	$_POST['options']['picture_thumbnailsize']);
//			Config::Set('video_thumbnailsize',	$_POST['options']['video_thumbnailsize']);

//VAR_DUMP($_POST['options']['model_thumbnailsize']) . "<br>";

			//serialize before saving
			$_POST['options']['model_thumbnailsize'] = serialize($_POST['options']['model_thumbnailsize']);
			$_POST['options']['picture_thumbnailsize'] = serialize($_POST['options']['picture_thumbnailsize']);
			$_POST['options']['video_thumbnailsize'] = serialize($_POST['options']['video_thumbnailsize']);
			$_POST['options']['picturegrab_thumbnailsize'] = serialize($_POST['options']['picturegrab_thumbnailsize']);
			$_POST['options']['videograb_thumbnailsize'] = serialize($_POST['options']['videograb_thumbnailsize']);


			//VAR_DUMP($_POST['options']['model_thumbnailsize']);

		} //theme not changed
		else
		{
			//theme changed
			//discard all thumbnail info before saving

			unset($_POST['options']['picturegrab_thumbnailsize'],
				$_POST['options']['videograb_thumbnailsize'],
				$_POST['options']['picture_thumbnailsize'],
				$_POST['options']['video_thumbnailsize'],
				$_POST['options']['model_thumbnailsize']
				);
		}

		//save the thumbnail sizes to database
		Tools::SaveOptions();

		//successfully saved
//		header('Location: '.Path::Get('url:admin')."/options_theme?success");
//		die();

		Logger::AdminActivity('changed options', 'theme');

	} //SaveOptions

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//permission check
		//editors do not have access to options
		Authentification::CheckPermission('administrator');

		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if(isset($_POST['submit'])) $this->SaveOptions();
			}
			catch (Exception $e) {
				echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
				die();
			}
		}

		$this->index();

	} //showIndex

} //class
