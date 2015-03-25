<?php

/* **************************************************************
 *  File: SocialPosting.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*** include the init.php file ***/
require '../../_init.php';

		/*--------------------------------------------------------------*/
		/*						Authentification						*/
		/*--------------------------------------------------------------*/
//				Authentification::check();
				if(!Authentification::Login())
				{
					echo Authentification::GetError();
					die();
				}
				if('administrator' !== Authentification::GetRole() && 'editor' !== Authentification::GetRole())
					die('auth error');
		/*--------------------------------------------------------------*/


class SocialPosting
{
	private static $token = false;

	private static $error = '';

	public static $progressBar = null;
	public static $progress = 0;
	public $maxprogress = 0;

	public static $debug = null;

	public static $update = array();

	public function __construct()
	{
		if(is_null(self::$debug))
			self::$debug = Config::Get('debug');
	}

/**
* errormsg
*
* @access private
*/
	private function errormsg()
	{
		if(is_array(self::$error))
		{
			$error_out = '';
			foreach(self::$error as $err)
				$error_out .= $err.'<br>';
		}
		else
			$error_out = self::$error;

		die($error_out);

	} //errormsg function

/**
* Get Update
*
* @access private
*/
	public function get_update()
	{
		if(empty($_GET['id']))
			return false;

		/***database connect***/
		$db = DB::getInstance();

		$update = $db->Row("SELECT `id`, `type`, `slug`,
					`freepath`, `title`,
					`description`, `dateline`
					FROM `bx_content`
					WHERE `id`=?",
					array(
						intval($_GET['id'])
					));

		//get tags
		if($update)
		{
			$tagquery = $db->FetchAll("SELECT t.tag as `tag`
					FROM `bx_tag_content` AS `bxtc`
					JOIN `bx_tag` AS `t` ON (t.id = bxtc.tag_id)
					WHERE bxtc.content_id = ?",
					array(
						intval($_GET['id'])
					));

			if($tagquery)
			{
				$tags = array();
				foreach($tagquery as $t)
				{
					$tags[] = $t['tag'];
				}
				$update['tags'] = $tags; //tags (as an array)
				//also adds empty array if no tags available
			}
		}

		return $update;
	}

/**
* ping
*
* @access private
*/
	public function send_pings()
	{

		/*ping search  engines*/
		$ping_options = Config::GetDBOptions(array(
			'ping_engines', 'ping_urls'));

		//setup progress bar
		if(is_null(self::$progressBar))
			self::$progressBar = $this->setupProgressBar(0, count($ping_options['ping_urls']));
		//reset progress
		self::$progress = 0;

		if(!self::$update) //social posting was skipped
		{
			self::$update = $this->get_update();
		}

		if(!empty(self::$update))
		{
			switch(self::$update['type'])
			{
				case 'video':
					self::$update['updateurl'] = Path::Get('url:site/video') . '/' . self::$update['slug'];
				break;
				case 'model':
					self::$update['updateurl'] = Path::Get('url:site/model') . '/' . self::$update['slug'];
				break;
				case 'set':
				default:
					self::$update['updateurl'] = Path::Get('url:site/set') . '/' . self::$update['slug'];
				break;
			}

			if($ping_options['ping_engines'])
			{
				//reset progressbar
				self::$progressbar->update(0);

				//ping the engines in the list
				foreach($ping_options['ping_urls'] as $purl)
				{
					//default method
					$method = 'weblogUpdates.ping';
					//check to see if optional method was specified
					//optional method format:
					//$pingurl = 'method|url_to_ping'
					$parts = explode("|", $purl, 2);
					if(count($parts) > 1)
					{
						$method = $parts[0];
						$purl = $parts[1];
					}
					unset($parts);

					self::$progress++;

					//ping it
					try{

						self::$progressBar->update(self::$progress);

						Tools::Ping($purl, array($updateinfo['title'], $updateinfo['updateurl']), $method);
					}
					catch(Exception $e)
					{
						//could not ping
//								self::$error[] = "Could not ping ".$purl;















					}
				}
			}

		} //success = true

	} //send_pings function

/**
* setupProgressBar
*
* @access private
*/
	private function setupProgressBar($min, $max)
	{
		//setup progress bar
		require_once "Zend/ProgressBar.php";
		require_once "Zend/ProgressBar/Adapter/JsPush.php";
		$adapter = new Zend_ProgressBar_Adapter_JsPush(array(
			'updateMethodName' => 'ProgressBar_Update',
			'finishMethodName' => 'ProgressBar_Finished')
		);
		return new Zend_ProgressBar($adapter, $min, $max);
	}

/**
* post
*
* @access private
*/
	public function social_postings()
	{
		if(!self::$update)
		{
			self::$error = 'Update not found';
			$this->errormsg();
			exit();
		}

		//get the social plugins
		$plugin_options = SocialPlugin::GetSocialOptions();

		if(!count($plugin_options)) //no social plugins found!
		{
			self::$error = 'No social plugins found';
			$this->errormsg();
		}

		//setup progress bar for social postings
		self::$progressBar = $this->setupProgressBar(0, $this->maxprogress);


		self::$progress = 1; //first item
		//check social plugins for NewUpdate Posting
		foreach($plugin_options as $po)
		{
			//plugin is not set to post on new update, so skip it
			if(!$po['postonupdate']) continue;

			//skip plugins that require auth but are not authenticated
			if($po['requires_auth'] && empty($po['access_token']))
				continue;

			try{
				if(self::$debug) Tools::debug_msg( "Posting to ".$po['name']."...", true ); //with br

				//new update
				$plugin_name = $po['name']."_plugin";
				$plugin = new $plugin_name;

				//hand the NewUpdate method the data
				$plugin->NewUpdate(self::$update);
				//progress bar is updated in the plugins!
			}
			catch(Exception $e)
			{

			}
		} //foreach


		//finish progressbar
		self::$progressBar->finish( "FINAL" );

	} //social_postings function

}//Class


if(!empty($_GET['id']))
{
	/***database connect***/
	$db = DB::getInstance();

	$poster = new SocialPosting;

	if(SocialPosting::$debug) Tools::debug_msg("Posting to Social Sites");

	$plugin_options = SocialPlugin::GetSocialOptions();

	//get update from database
	SocialPosting::$update = $poster->get_update();

	//get the freebie images (if any)
	SocialPosting::$update['freeimages'] = $db->FetchAll("SELECT *
				FROM `bx_picture`
				WHERE `freepicture`='1'
				AND `content_id`=?",
				array(
					SocialPosting::$update['id']
				));

	$numfreepics = count(SocialPosting::$update['freeimages']);

	//build the urls and paths to freebie pictures
	if($numfreepics)
	{
		$internalslug = '/' . Input::clean(SocialPosting::$update['slug'], 'FILENAME') . '/';
		for($i=0; $i<$numfreepics; $i++)
		{
			SocialPosting::$update['freeimages'][$i]['url'] = Path::Get('url:free/'.SocialPosting::$update['type']) . $internalslug . SocialPosting::$update['freeimages'][$i]['filename'];
			SocialPosting::$update['freeimages'][$i]['path'] = Path::Get('path:free/'.SocialPosting::$update['type']) . $internalslug . SocialPosting::$update['freeimages'][$i]['filename'];
		}
	}

	//get the correct maximum for the progress bar
	//from finishupdate.class.php
	$poster->maxprogress = intval($_GET['maxprogress']);

	if(SocialPosting::$debug) Tools::debug_msg("Progress set to 0/".$poster->maxprogress);

	$poster->social_postings();
	$poster->send_pings();
}
