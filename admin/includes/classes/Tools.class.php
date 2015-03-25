<?php

/* **************************************************************
 *  File: Tools.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Tools Class
*
*/
class Tools {

	private static $translations = array(
		"_accounts" => "Accounts",
		"_activitylog" => "Activity Log",
		"_admininterface" => "Admin Interface",
		"_billing" => "Billing",
		"_cache" => "Cache",
		"_comments" => "Comments",
		"_editupdates" => "Edit Updates",
		"_faq" => "Faq",
		"_general" => "General",
		"_home" => "Home",
		"_loginhistory" => "Login History",
		"_logout" => "Log Out",
		"_member" => "Member",
		"_members" => "Members",
		"_mobile" => "Mobile",
		"_models" => "Models",
		"_newmodel" => "New Model",
		"_newupdate" => "New Update",
		"_options" => "Options",
		"_pictures" => "Pictures",
		"_ratings" => "Ratings",
		"_showstructure" => "Show Database Structure",
		"_social" => "Social",
		"_stats" => "Stats",
		"_tags" => "Tags",
		"_template_editor" => "Template Editor",
		"_theme" => "Theme",
		"_updates" => "Updates",
		"_videos" => "Videos",
		"_visitsite" => "Visit Site",

		//datatables language
		"_first" => "First",
		"_last" => "Last",
		"_next" => "Next",
		"_previous" => "Previous",
		"_sInfo" => "sInfo",
		"_sInfoEmpty" => "sInfoEmpty",
		"_sInfoFiltered" => "sInfoFiltered",
		"_sLengthMenu" => "sLengthMenu",
		"_sProcessing" => "sProcessing",
		"_sZeroRecords" => "sZeroRecords",
	);

/**
* Get Common Admin Translations
*
* @access	public
* @return	array
*/
	public static function GetCommonAdminTranslations()
	{
		if(Config::Get('showadmintooltips')){

			self::$translations = self::$translations +
				array(
					"_addnewmodel" => "Add New Model",
					"_addnewupdate" => "Add New Update",
					"_editcomments" => "Edit Comments",
					"_editfaqitems" => "Edit Faq Items",
					"_editmembers" => "Edit Members",
					"_editmodels" => "Edit Models",
					"_editoptions" => "Edit Options",
					"_edittags" => "Edit Tags",
				);
		}

		//translate the array!
		$translate = Zend_Registry::get('translate');
		self::$translations = $translate->translateArray(self::$translations);

		return self::$translations;

	} //GetCommonAdminTranslations

/**
* Slug Exists Check
*
* @access	public
* @param	string	$slug
* @param	string	$type
* @return	bool
*/
	public static function slug_exists_check(& $slug, $type='') {

		//checks if the slug exists and updates it with a unique slug

		/***database connect***/
		$db = DB::getInstance();

			//slug exists check
			$slugcheck = $db->FetchAll("SELECT `slug` FROM `bx_content` WHERE `slug` = ? LIMIT 1", array($slug));

			if(!empty($slugcheck)){ //slug already exists, but must be unique!

				//suggest a unique new slug based on the existing slugs
				$slug = $db->unique_slug($slug, $type);

				return false;
			}
			else
				return true;

	} //slug_exists_check

/**
* Delete Update
*
* @access	public
*/
	public static function DeleteUpdate()
	{
		//permission check
		Authentification::CheckPermission('administrator', 'editor');

		//get translation
		$translate = Zend_Registry::get('translate');

		if(isset($_POST['checkbox']))
		{
			/***get connection***/
			$db = DB::getInstance();

			//get the updates/models that are marked for deletion
			$input = Input::clean_array('p', array('checkbox' => 'UINT'));
			$del_ids = '';
			foreach($input['checkbox'] as $c){
				$del_ids .= $db->Prepare("?,", array($c));
			}
			$del_ids = rtrim($del_ids, ",");


				/***delete 2257 records***/

					$db->Update("DELETE FROM `bx_2257` WHERE `content_id` IN (".$del_ids.")");

				/***delete comments***/

					$db->Update("DELETE FROM `bx_comment` WHERE `content_id` IN (".$del_ids.")");

				/***delete tags in bx_tag_content***/

					$db->Update("DELETE FROM `bx_tag_content` WHERE `content_id` IN (".$del_ids.")");

				/***delete ratings***/

					$db->Update("DELETE FROM `bx_rating` WHERE `content_id` IN (".$del_ids.")");

				/***delete favorites***/

					$db->Update("DELETE FROM `bx_favorite` WHERE `content_id` IN (".$del_ids.")");

				/***delete metadata***/

					$db->Update("DELETE FROM `bx_metadata` WHERE `content_id` IN (".$del_ids.")");

				/***delete model_has_set***/

					$db->Update("DELETE FROM `bx_model_has_set` WHERE `content_id` IN (".$del_ids.")");

				/***delete videos (if any)***/

					$db->Update("DELETE FROM `bx_video` WHERE `content_id` IN (".$del_ids.")");

				/***delete pictures***/

					//get free pics of update
					$freepics = $db->FetchAll("SELECT p.filename, u.slug, u.type
						FROM `bx_picture` AS `p`
						JOIN `bx_content` AS `u` ON (u.id = p.content_id)
						WHERE `content_id` IN (".$del_ids.")
						AND `freepicture`='1'");
/*
						AND `theme` = ?,
						array(Template::$theme));
*/

					//delete the free pics of the update
					if($freepics)
					{
						foreach($freepics as $fp)
						{
							$type = Path::Get($fp['type']); //ex.: 'set' => 'pics'

							$dir = Path::Get("path:free/$type") . DIR_SEP .Input::clean($fp['slug'],'FILENAME');

							if(is_dir($dir))
							{
								@Filehandler::DeleteFile($dir. DIR_SEP  .$fp['filename']);

								//if the dir is empty now, delete it
								if(Filehandler::is_empty_folder($dir))
									@Filehandler::RemoveDir($dir, false); //not recursive
							}
						}
					}

					//delete pics from db
					$db->Update("DELETE FROM `bx_picture` WHERE `content_id` IN (".$del_ids.")");

				//***delete thumbnail files***/

					$deletes = $db->FetchAll("SELECT th.path AS `path`,
							u.slug, u.type
							FROM `bx_thumbnail` AS `th`
							JOIN `bx_content` AS `u` ON (u.id = th.content_id)
							AND `content_id` IN (".$del_ids.")");

					if($deletes)
					{

						foreach($deletes as $del)
						{
							$type = Path::Get($del['type']); //ex.: 'set' => 'pics'

							$internal_slug = Input::clean($del['slug'], 'FILENAME');

							//delete the uploaded thumbnail file
							@Filehandler::DeleteFile(Path::Get('path:site') . String::Slash($del['path'],1,0));

							$folder = false;

							if($type)
							{
								$folder = Path::Get("path:thumbs/$type") . DIR_SEP . $internal_slug;
//FIX: Ugle double 'thumbs' in url
								$thumbdir = $folder.'/thumbs';
								if(is_dir($thumbdir))
								{
									@Filehandler::EmptyDir($thumbdir,  false); //no sub-dirs
									@Filehandler::RemoveDir($thumbdir, false); //not recursive
								}

								if(@Filehandler::is_empty_folder($folder)) //only delete if empty
									@Filehandler::RemoveDir($folder, false); //not recursive
							}
						} //foreach

					} //if $deletes

				/***delete thumbnails***/

					$db->Update("DELETE FROM `bx_thumbnail` WHERE `content_id` IN (".$del_ids.")");

				//finally:
				/***delete update/model***/

					$db->Update("DELETE FROM `bx_content` WHERE `id` IN (".$del_ids.")");


				//update the header stats
				Caching::ClearStatsCache();

				//***sucess and error messages***/
/*
				//get tpl
				$tpl = Zend_Registry::get('tpl');

				if($alldeleted){
					//all thumbs were successfully removed
					$tpl->successmessage($translate->_('All thumbnails removed').'<br>');
				}
				else{
					$tpl->errormessage("There was an error with removing the thumbnails of this model.<br>You will have to remove them manually from ".$folder."<br>");
				}
				if($fdeleted){
					//folder was also successfully removed
						$tpl->successmessage("Model folder(s) was empty and successfully deleted.<br>");
				}
				else{
					$tpl->errormessage("The folder(s) was not removed, because it is not empty.<br>".$folder."<br>");
				}
*/

				unset($_POST);

		} //if

	} //DeleteUpdate

/**
* Flush Buffers
*
* @access	public
*/
	public static function flush_buffers(){

		while (ob_get_level()) {
			@ob_end_flush();
			@ob_flush();
			@flush();
		}

	} //flush_buffers

/**
* Save Options
*
* @access	public
*/
	public static function SaveOptions()
	{
		if ($_POST['securitytoken'] != Session::GetToken())
			die($translate->_('Security token mismatch'));

		//permission check
		//editors do not have access to options
		Authentification::CheckPermission('administrator');

		unset($_POST['securitytoken']);


		if(!isset($_POST['options']))
			return false;
//			throw new Exception('no options submitted');

		/***database connect***/
		$db = DB::getInstance();

		foreach ($_POST['options'] as $key => $value)
		{
			$key = Input::clean($key, 'NOHTML');
			if(!is_array($value))
			{
				if ($value == '') $value = null;
				$value = Input::clean($value, 'NOHTML');
			}
			else
			{
				$value = Input::clean($value, 'ARRAY_NOHTML');
				if(empty($value)) $value = null;
				$value = serialize($value);
			}

			//save the new options to the database
			$db->Update("UPDATE `bx_options` SET `value` = ? WHERE `key` = ?", array($value, $key));
		}

		//delete Config cache and file cache
		Config::ClearCache();

	} //SaveOptions

/**
 * Get a list of directories in a directory
 *
 * Reads the specified directory and builds an array
 * representation of it. Sub-folders are not mapped.
 *
 * @access	public
 * @param	string	$source_dir		path to source
 * @return	mixed	array | bool
 */
	public static function getDirectories($source_dir)
	{
		if ($fp = @opendir($source_dir))
		{
			$source_dir = rtrim($source_dir, '/') . '/';

			$data = array();

			while (false !== ($entry = readdir($fp)))
			{
				if ((strncmp($entry, '.', 1) == 0) OR ($entry == '.' || $entry == '..'))
				{
					continue;
				}
				else
				{
					if(@is_dir($source_dir.$entry))
					{
						$data[] = $entry;
					}
					else
						continue;
				}
			}

			closedir($fp);
			return $data;
		}
		else
		{
			return false;
		}
	} //getDirectories

/**
* Ping
*
* @access	public
* @param	string	$url		url to search engine, formatted to add ping url at the end of the line.
* @param	string	$notification	the content of the notification to send
* @return	bool	true|false	success or failure
*/
	public static function Ping($pingurl, $url, $args = array(), $method='weblogUpdates.ping'){

		$return = false;

		//get XML RPC lib
		require_once Path::Get('path:site')."/includes/classes/Zend/XmlRpc/Client.php";

		// do not send cookies
		$_COOKIE = array();

		$client = new Zend_XmlRpc_Client($pingurl);

		try {
			$return = $client->call($method, $args);

		} catch (Zend_XmlRpc_Client_HttpException $e) {
			// $e->getCode() = 404
			// $e->getMessage() = "Not Found"
		}
		catch (Zend_XmlRpc_Client_FaultException $e) {
			// $e->getCode() = 1
			// $e->getMessage() = "Unknown method"
		}

		return $return;

	} //Ping

/**
* Output Buffer Circumvention for Debug Messages
*
* @access	public
* @param	string	Message
*/
	public function debug_msg($msg, $linebreak = true)
	{
		echo str_pad($msg, 1024, ' ', STR_PAD_RIGHT) . ($linebreak ? '<br>'. PHP_EOL : '');

		self::flush_buffers();
	}

} //class