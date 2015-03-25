<?php

/* **************************************************************
 *  File: options_thumbnails.class.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

class options_thumbnails extends BaseController
{
	const INTERNAL_ID = 'internal_id';
	const WIDTH = 'width';
	const HEIGHT = 'height';

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
		$tpl->title = $translate->_("Thumbnail")." ".$translate->_("Options");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			/***options***/
			$options = array();
			$options = Config::GetDBOptions(array(
				'thumbnailsharpen', 'thumbnailquality', 'num_video_screencaps'
			));
			/***options***/

			/***thumbsize***/
			//thumbnail with internal_id = 3 is used
			$thumbsizes = array();
			$thumbsizes = $db->FetchAll("SELECT `internal_id`,`type`,`width`,`height` FROM `bx_thumbsize` ORDER BY `type`, `internal_id`");

			//build three different thumbsize arrays
			$modelsizes = array();
			$videosizes = array();
			$picturesizes = array();
			foreach($thumbsizes as $ths)
			{
				if($ths['type'] == 'pictureset')
				{
					$picturesizes[$ths[self::INTERNAL_ID]] = array(
						self::INTERNAL_ID=>$ths[self::INTERNAL_ID],
						self::WIDTH=>$ths[self::WIDTH],
						self::HEIGHT=>$ths[self::HEIGHT]
					);
				}
				elseif($ths['type'] == 'videoset')
				{
					$videosizes[$ths[self::INTERNAL_ID]] = array(
						self::INTERNAL_ID=>$ths[self::INTERNAL_ID],
						self::WIDTH=>$ths[self::WIDTH],
						self::HEIGHT=>$ths[self::HEIGHT]
					);
				}
				elseif($ths['type'] == 'model')
				{
					$modelsizes[$ths[self::INTERNAL_ID]] = array(
						self::INTERNAL_ID=>$ths[self::INTERNAL_ID],
						self::WIDTH=>$ths[self::WIDTH],
						self::HEIGHT=>$ths[self::HEIGHT]
					);
				}
			}
			/***thumbsize***/

		/***disconnect***/
		unset($db);


		/***TEMPLATE ASSIGNMENTS***/

		//thumbsizes
		if(isset($modelsizes)) $tpl->assign("modelsizes", $modelsizes);
		if(isset($videosizes)) $tpl->assign("videosizes", $videosizes);
		if(isset($picturesizes)) $tpl->assign("picturesizes", $picturesizes);

		//internationalization
		$_t = $translate->translateArray(array(
			"height" => "Height",
			"internal_id" => "Internal ID",
			"num_video_framegrabs" => "Number of Video Framegrabs",
			"sharpeningamount" => "Sharpening amount of thumbnails",
			"submit" => "Submit",
			"thumbnailprocessing" => "Thumbnail Processing",
			"thumbnailquality" => "Thumbnail Quality",
			"update" => "Update",
			"thumbnailsizes" => "Thumbnail Sizes",
			"width" => "Width",
		));
		$tpl->assign("_t", $_t);



		//options
		if(isset($options)) $tpl->assign("options", $options);

		/***security token***/
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
		Tools::SaveOptions();

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
		if('administrator' != Authentification::GetRole())
			Template::PermissionDenied();

		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try {
				if($_POST['securitytoken'] == self::$token && !isset($_GET['securitytoken']))
					if(isset($_POST['submit'])) $this->SaveOptions();
				else
					throw new Exception($translate->_('Security Token mismatch'));
			}
			catch (Exception $e) {
				echo $translate->_('Error').': ',  $e->getMessage(), "<br />".PHP_EOL;
				die();
			}
		}

		$this->index();

	} //showIndex

} //class
