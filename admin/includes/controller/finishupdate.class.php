<?php

/* **************************************************************
 *  File: finishupdate.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class finishupdate extends BaseController
{
	private static $input = array();

	private static $token = false;

	private static $success = false;
	private static $error = false;

	//gets filled with data for social posting
	private static $update = array();

	//gif animation configuration
		//delay for gif animation
		const GIF_DELAY = 50;
		//number of gif anim iterations
		//set to zero to animate indefinitely
		const LOOPS = 0;

/**
* index
*
* @access private
*/
	private function index()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		if (@$_POST['securitytoken'] != self::$token)
			die($translate->_('Security token mismatch'));

		$imgAdapter = new Img_Adapter;

		if(!defined('TIME_NOW'))
			define('TIME_NOW', time());

		/***database connect***/
		$db = DB::getInstance();

		try
		{
			/*********************/
			/****picture update***/
			/*********************/
			if($_POST['type'] == 'set')
			{
				//get temporary data from DB
				$data = $db->FetchAll("SELECT `value` FROM `bx_temp`
							WHERE `key`='internal_slug'
							OR `key`='updateid'
							OR `key`='folder'");

					if(!$data)
						throw new Exception("Could not retrieve temporary data.");
					if(!isset($data[0]['value']) || !isset($data[1]['value']) || !isset($data[2]['value']))
						throw new Exception("Could not retrieve temporary data.");

					//images
					self::$input = Input::clean_array('p', array(
									'img' => 'ARRAY_NOHTML',
									));

					self::$input['folder'] = $data[0]['value'];
					self::$input['internal_slug'] = Input::clean($data[1]['value'], 'FILENAME');
					self::$input['updateid'] = intval($data[2]['value']);
				unset($data);


				//check for abort
				if(isset($_POST['abort']))
					self::abortUpdate(self::$input['updateid']);


				//get the member and free directory of this picture update
				$paths = $db->Row("SELECT `memberpath`, `freepath`,
						`title`, `description`, `type`
						FROM `bx_content`
						WHERE `id` = ?
						LIMIT 1", array(self::$input['updateid']));
				if(!$paths) throw new Exception("Update not found in database.");


				$picturecount = count(self::$input['img']);


				$sql = '';
				if($picturecount > 0)
				{
					//use the actual picture count
					//reset:
					$picturecount = 0;

					//write pics to database

					//check if pictures with this updateid already exist
					$ids = $db->FetchAll("SELECT `id`
							FROM `bx_picture`
							WHERE `content_id` = ?
							AND `theme` = ?",
							array(
								self::$input['updateid'],
								Template::$theme
							));

					//delete the existing pics
					self::predelete('picture', $ids);

					//construct sql
					foreach(self::$input['img'] as $im)
					{
						$picturepath = rtrim(self::$input['folder'], "/") . DIR_SEP . basename($im['filename']);

						$freepath = Path::Get('path:free/pics') . DIR_SEP . self::$input['internal_slug'] . DIR_SEP . basename($im['filename']);
						$freeurl = Path::Get('url:free/pics') . '/' . self::$input['internal_slug'] . '/' .basename($im['filename']);

						if(!file_exists($picturepath))
						{
							continue; //something is wrong
						}
						else
						{
							$filesize = filesize($picturepath);
							if(!$filesize)
								continue; //something is wrong

							$size = $imgAdapter->GetImgSize($picturepath);
							if(!$size[0][0] || !$size[0][1])
								continue; //something is wrong
						}

						//move free images to free directory
						if($im['mark'] == 1)
						{
							//free pics from update step 2
							if(!@Filehandler::smartCopy($picturepath, $freepath))
							{
								//free image could not be created.
								//mark it as not free
								$im['mark'] = 0;
								//die("Could not copy the image to the free area.");
							}
						}

						$picturecount++;

						$sql .= $db->Prepare("(?,?,?,?,?,?,?,?,'set',?,?),", array(
							NULL,
							self::$input['updateid'],
							basename($im['filename']),
							intval($im['mark']),
							$size[0][0],
							$size[0][1],
							$filesize,
							$size[0]['mime'],
							Template::$theme,
							TIME_NOW));
					}
					$sql = rtrim($sql, ",");

					if(!empty($sql))
					{
						$sql = "INSERT INTO `bx_picture` (
							`id`,
							`content_id`,
							`filename`,
							`freepicture`,
							`width`,
							`height`,
							`size`,
							`mimetype`,
							`type`,
							`theme`,
							`dateline`
							) VALUES ".$sql;

							//insert pictures
							$db->Update($sql);
							unset($sql);
					}

				} //!empty pictures

				//update picture count and dateline
				$db->Update("UPDATE `bx_content` SET `count`=?,`dateline`=? WHERE `id` = ?",
				array($picturecount, time(), self::$input['updateid']));

				self::$success = true;

			}
			/*********************/
			/*****video update****/
			/*********************/
			elseif($_POST['type'] == 'video')
			{
				//temporary data from DB
				$data = $db->FetchAll("SELECT `value` FROM `bx_temp`
							WHERE `key` = 'outputfolder'
							OR `key` = 'updateid'");

					if(!$data)
						throw new Exception("Could not retrieve temporary data.");
					if(!isset($data[0]['value']) || !isset($data[1]['value']))
						throw new Exception("Could not retrieve temporary data.");

					self::$input['outputfolder'] = Input::clean($data[0]['value'], 'STR');
					self::$input['updateid'] = Input::clean($data[1]['value'], 'UINT');
				unset($data);

				//check for abort
				if(isset($_POST['abort']))
					self::abortUpdate(self::$input['updateid']);

				//video thumbnails
				$i = 0;
				if(isset($_POST['vidthumb']))
				{
					$thumbdata = array();
					foreach($_POST['vidthumb'] as $thumb)
					{
						$thumbdata[$i]['mark'] = Input::clean($thumb['mark'], 'UINT');
						$thumbdata[$i]['filename'] = Input::clean($thumb['filename'], 'FILENAME');

						$i++;
					} //foreach
				} //vidthumb

				//videos
				if(isset($_POST['video']))
				{
					$i = 0;
					$videodata = array();
					foreach($_POST['video'] as $video)
					{
						$videodata[$i]['filename'] = Input::clean($video['filename'], 'FILENAME');
						$videodata[$i]['fps'] = Input::clean($video['fps'], 'UNUM');
						$videodata[$i]['bitrate'] = Input::clean($video['bitrate'], 'UINT');
						$videodata[$i]['length'] = Input::clean($video['length'], 'UINT');
						$videodata[$i]['height'] = Input::clean($video['height'], 'UINT');
						$videodata[$i]['width'] = Input::clean($video['width'], 'UINT');
						$videodata[$i]['format'] = Input::clean($video['format'], 'NOHTML');

						$i++;
					} //foreach
				} //videos


				//write video thumbs to database
				if(!empty($thumbdata))
				{
					$thumbrelpath = Path::Get('rel:thumbs/videos') . '/' . self::$input['internal_slug'];

					//check if thumbnails with this updateid already exist
					$ids = $db->FetchAll("SELECT `id`
								FROM `bx_picture`
								WHERE `content_id` = ?
								AND `theme` = ?",
								array(self::$input['updateid'], Template::$theme));

					self::predelete('picture', $ids);

	//FIX: ugly double 'thumbs' in url!
					$newpath = Path::Get('path:thumbs/videos') . DIR_SEP . self::$input['internal_slug'] . DIR_SEP . 'thumbs';
					//create the directory that will hold the thumbnails
					if(!@Filehandler::MkDir($newpath))
						throw new Exception("Could not create thumbnail directory.");


					$make_anim = Config::GetDBOptions('make_anim');
					if($make_anim)
					{
						//create gif animation of thumbnails
						exec('convert -delay '.escapeshellarg(self::GIF_DELAY).' -loop '.escapeshellarg(self::LOOPS).'  '.escapeshellarg(self::$input['outputfolder'].'/*.jpg').'  '.escapeshellarg(self::$input['outputfolder'].'/anim.gif'));
					}


					//construct sql
					$picturecount = 0;
					foreach($thumbdata as $im)
					{
						$im['filename'] = basename($im['filename']);
						$ext = String::GetFileExtension($im['filename']);

						$currentpath = self::$input['outputfolder'] . DIR_SEP . $im['filename'];
						$thumbname = $picturecount . "." . $ext;
						$movepath = $newpath . DIR_SEP . $thumbname;

						if(!file_exists($currentpath))
							continue; //something is wrong

						//get file size
						$filesize = filesize($currentpath);
						if(!$filesize)
							continue; //something is wrong

						$size = $imgAdapter->GetImgSize($currentpath);
						if(!$size[0][0] || !$size[0][1])
							continue; //something is wrong

						if($im['mark'] == 0)
						{
							//discard the thumbnail
							Filehandler::DeleteFile($currentpath);
							continue;
						}

						//move the file to its destination
						if(!@Filehandler::MoveFile($currentpath, $movepath))
							continue; //something went wrong with the thumbnail move. Abort this thumb. Do not add to db.

						$sql .= $db->Prepare("(?,?,?,?,?,?,?,'video',?,?),", array(
							NULL,
							self::$input['updateid'],
							$thumbname,
							$size[0][0],
							$size[0][1],
							$filesize,
							$size[0]['mime'],
							Template::$theme,
							TIME_NOW));

						$picturecount++;
					}


					if($make_anim)
					{
						//add the gif animation
						$anim_filename = 'anim.gif';

						$animpath = self::$input['outputfolder'] . '/' . $anim_filename;
						if(file_exists($animpath))
						{
							//get file size
							$filesize = filesize($animpath);
							$size = $imgAdapter->GetImgSize($animpath);

							//move gif animation
							$movepath = $newpath . '/' . $anim_filename;

							if(@Filehandler::MoveFile($animpath, $movepath))
							{
								//add to database
								$sql .= $db->Prepare("(NULL,?,?,?,?,?,?,'anim'),", array(
									self::$input['updateid'],
									$anim_filename,
									$size[0][0],
									$size[0][1],
									$filesize,
									$size[0]['mime']
								));
							}
						}
					}
					$sql = rtrim($sql, ",");

					if(!empty($sql))
					{
						//add video thumbnail pictures
						$sql = "INSERT INTO `bx_picture` (
							`id`,
							`content_id`,
							`filename`,
							`width`,
							`height`,
							`size`,
							`mimetype`,
							`type`,
							`theme`,
							`dateline`
						) VALUES " . $sql;

						//insert pictures (execute sql query)
						$db->Update($sql);
						unset($sql);
					}
					else
					{
						//abort
						throw new Exception("Could not write thumbnails to database.");
					}

					//update picture count and dateline
					$db->Update("UPDATE `bx_content` SET `count`=?, `dateline` = ? WHERE `id` = ?", array($picturecount, time(), self::$input['updateid']));

				} //!empty videothumbnails

				//check if videos with this updateid already exist
				$ids = array();
				$ids = $db->FetchAll("SELECT `id` FROM `bx_video` WHERE `content_id` = ?", array(self::$input['updateid']));
				//delete those videos
				self::predelete('video', $ids);

				//construct sql
				foreach($videodata as $vid)
				{
					$videopath = rtrim(self::$input['internal_videodirectory'], '/') . DIR_SEP . $vid['filename'];

					$filesize = null;
					if(file_exists($videopath))
					{
						//get file size
						$filesize = filesize($videopath);
						if(!$filesize)
							continue; //something is wrong
					}
					else
						continue; //something is wrong

					$sql .= $db->Prepare("(?,?,?,?,?,?,?,?),", array(NULL, self::$input['updateid'], $vid['filename'], $vid['format'], $vid['width'], $vid['height'], $filesize, $vid['length'] ));
				}
				$sql = trim($sql, ",");
				if(!empty($sql))
				{
					$sql = "INSERT INTO `bx_video` (`id`, `content_id`, `filename`, `format`, `width`, `height`, `size`, `length`) VALUES ".$sql;

					//insert videos in video table
					$db->Update($sql);
					unset($sql);
				}
				else
				{
					//abort
					throw new Exception("Could not insert video into database.");
				}
	/*
				//move thumbnail directory
				$currentpath = self::$input['outputfolder'];
//FIX: ugly double 'thumbs' in url
				$newpath = Path::Get('path:thumbs/videos') . DIR_SEP . self::$input['internal_slug'] . DIR_SEP. 'thumbs';
				@Filehandler::MoveDir($currentpath, $newpath);
	*/

				self::$success = true;

			} //video set
			else //not recognized type
				throw new Exception("Invalid input data.");

		}//try
		catch (Exception $e)
		{
			self::$success === false;

			self::$error = $e->getMessage();

			//abort update
			self::abortUpdate(self::$input['updateid']);
		}

		//clear stats
		if(self::$success === true)
		{
			//clear temp
			$db->Update("TRUNCATE TABLE `bx_temp`");

			//update the header stats
			Caching::ClearStatsCache();


			//fork: redirect to social posting if social plugins enabled

			//get the social plugins
			$plugin_options = SocialPlugin::GetSocialOptions();
			$ping_urls = Config::GetDBOptions('ping_urls');
			if(empty($ping_urls) && count($plugin_options) == 0) //no social plugins found!
			{
				//finished. redirect now.
				header('Location: '.Path::Get('url:admin').'/updates?success');
				exit;
			}
			else
			{
				//SOCIAL POSTING

				//get the correct maximum for the progress bar
				$maxprogress = 0;
				//cycle through plugins
				foreach($plugin_options as $po)
				{
					//plugin cannot post on new update
					if(!$po['updateposting_possible']) continue;
					//plugin is not set to post on new update, so skip it
					if(!$po['postonupdate']) continue;
					//skip plugins that require auth but are not authenticated
					if($po['requires_auth'] && empty($po['access_token']))
						continue;

					if($po['picturepost'] == true)
						$maxprogress = $maxprogress + $numfreepics; //full number of freebie pictures
					else
						$maxprogress++; //only the update
				}

				if(!$maxprogress) //nothing to post -> redirect
				{
					header('Location: '.Path::Get('url:admin').'/updates?success');
					exit;
				}

				$tpl->assign('maxprogress', $maxprogress);

				$templatename = "admin_socialposting";
				$tpl = new Template('admin:'.$templatename, 0); //__tpl_cache_time

				$translations = Tools::GetCommonAdminTranslations();
				$tpl->apply($translations);

				$tpl->assign('update_id', self::$input['updateid']);

				$tpl->display();

				$tpl->debug();

				exit;
			}
		}

	} //function index

/**
* predelete
*
* @param	string	$type
* @param	array	$ids
*
* @access private
*/
	private static function predelete($type, $ids)
	{
		/***database connect***/
		$db = DB::getInstance();

		$table = '';
		switch($type)
		{
			case 'picture':
				$table = 'bx_picture';
				break;
			case 'video':
				$table = 'bx_video';
				break;
			default:
				return false;
				break;
		}

		//delete ids
		if (!empty($ids))
		{
			$del_ids = '';
			foreach($ids as $c){
				$del_ids .= $db->Prepare("?,", array($c));
			}
			$del_ids = rtrim($del_ids, ",");

			//delete the existing pics from this update
			$db->Update("DELETE FROM # WHERE `id` IN (".$del_ids.")", array($table));
		}

	} //predelete

/**
* abortUpdate
*
* @param	integer	$updateid
*
* @access private
*/
	private static function abortUpdate($updateid)
	{
		Authentification::CheckPermission('administrator', 'editor');

		if($updateid)
		{
			if(isset($_POST['checkbox'])) unset($_POST['checkbox']); //for security reasons
			//set checkbox array to updateid
			$_POST['checkbox'] = array(0 => intval($updateid));

			Tools::DeleteUpdate();

			$error = (self::$error?'error='.urlencode(self::$error):'abort');

			if($error == 'abort')
				Logger::AdminActivity('aborted update process', '');
			else
				Logger::AdminActivity('update error: '.self::$error, '');

			//redirect to form on abort
			header('Location: '.Path::Get('url:admin').'/update?'.$error);
			// "?abort" shows abort message
			exit;
		}

	} //abortUpdate

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		self::$token = Session::GetToken();
		$this->index();

	} //showindex

}//class
