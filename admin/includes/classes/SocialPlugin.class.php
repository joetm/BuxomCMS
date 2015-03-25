<?php

/* **************************************************************
 *  File: SocialPlugin.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Social Plugin Class
*
*/
abstract class SocialPlugin
{
	private static $details = array();

	private static $socialoptions = array();

/**
* Get Details
*
* @access	public
* @param	string	$classname
* @return	mixed	array | bool
*/
	public static function GetDetails($classname = null) {

		/***database connect***/
		$db = DB::getInstance();

		//get details from database
		self::$details = unserialize( $db->Column("SELECT `value` FROM `bx_options` WHERE `key` = ?", array( $classname )) );

		if(!self::$details)
			return false;
		else
						//return clean output (just in case)
			return self::$details;	//Input::clean(self::$details, 'NOHTML');

	} //GetDetails

/**
* Set Details
*
* @access	public
* @param	array	$input
* @return	bool
*/
	public static function SetDetails(array $input) {

		$input = serialize( Input::clean($input,'NOHTML') );

		/***database connect***/
		$db = DB::getInstance();

		$status = false;
		//check if options for this update exist
		if($db->Column("SELECT `value` FROM `bx_options` WHERE `key`=?", array( $classname )) )
			$status = $db->Update("UPDATE `bx_options` SET `key`=?, `value`=?, `autoload`='0'", array( $classname, $input ));
		else
			$status = $db->Insert("UPDATE `bx_options` FROM `bx_options` WHERE `key`=?", array( $classname ));

		return $status;

	} //SetDetails

/**
* Get Social Options
*
* @access	public
* @return	array
*/
	public static function GetSocialOptions()
	{
		//caching
		if(!empty(self::$socialoptions))
			return self::$socialoptions;


		//permission check
		//editors do not have access to options
		Authentification::CheckPermission('administrator');


		//get admin plugins
		$plugins = AdminPlugin::GetPlugins();

		$details = array();
		$options = array();
		foreach($plugins as $plugin)
		{
				$op = new $plugin['classname']();
				try
				{
					if(is_callable(array($op, 'GetDetails')))
						$details = $op->GetDetails();
					else	//not an option plugin
						continue;
				}
				catch (Exception $e)
				{
					//some error
					$tpl->errormessage('Error in plugin: '.Input::clean( $e->getMessage(), 'NOHTML'));

					//skip
					continue;
				}

				if($details)
				{
					//mark updateposting_possible in array
					if(isset($op->updateposting_possible))
						$details['updateposting_possible'] = $op->updateposting_possible;
					else //just in case of messy plugin
						$details['updateposting_possible'] = false;

					if(isset($op->auth_type))
						$details['auth_type'] = $op->auth_type;

					if(isset($op->infotext))
						$details['infotext'] = $op->infotext;

					$options[] = $details;
				}
				else
				{
					//details for this plugin were not found in database
					//try to install the empty default code in the db
					$details = $op->GetDefaults($plugin['name']);

					//prime the database with default values

					/***database connect***/
					$db = DB::getInstance();

					$status = $db->Update("INSERT INTO `bx_options` (`key`, `value`, `autoload`) VALUES (?,?,'0')", array($plugin['classname'], serialize($details))); //ex.: ('twitter_plugin', <serialized default details>, 0)

					if($status) //default code successfully inserted
						$options[] = $details;
					else
						//not a valid plugin (maybe a rogue file in the plugin folder)?
						throw new Exception ('not a valid plugin');
				}

		} //foreach

		return self::$socialoptions = $options;

	} //GetSocialOptions


	//remove Authentification
	public static function DeAuthorize($pluginname)
	{
		/***database connect***/
		$db = DB::getInstance();

//		$pluginname = get_class($this);

		//received request to remove authorizations
		$op = $db->Column("SELECT `value` FROM `bx_options` WHERE `key` = ?", array($pluginname));
		//config found
		if($op)
		{
			$options = unserialize($op);

			unset($options['access_token']);

			$status = $db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`=?",
			array(serialize($options), $pluginname));
		}
//if it's not in the database, then we must be de-authorized already
//		else
//		{
//			die('not found in db');
//		}

		//ajax output
		if($status)
			exit('ok');
		else
			die('error');


	} //DeAuthorize

	public function UpdateProgressBar($data = null)
	{
		//update the progress bar if we can access it
		if(class_exists('SocialPosting') &&!is_null(SocialPosting::$progressBar) && isset(SocialPosting::$progress))
		{
			SocialPosting::$progress++;
			SocialPosting::$progressBar->update(SocialPosting::$progress, $data);
		}

	} //UpdateProgressBar

} //class SocialPlugin