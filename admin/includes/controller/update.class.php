<?php

/* **************************************************************
 *  File: update.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class update extends BaseController
{
	//set this to false to turn off automatically filling of html5 video urls
	const _PREFILL = true;

	//accessed from template
	public static $id = 0;

	private static $update_id;







	private static $internal_slug = '';

	private static $internal_type; //ex.: 'pics', 'videos' or 'models'








	//required variables
	//cannot be merged into options array
	//is accessed from the templates
	public static $mandatory = array();

	//options array
	private static $o = array(
		'cropped' => false
		);

	private static $token = false;

	private static $output = array();

	private static $success = false;
	private static $errors = array();
	private static $errorcss = array();

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
		$tpl->title = $translate->_("New Update");
		/***page setup***/

		if(empty($_POST)){
		//first page load
		//make sure that temp dir is empty
			Filehandler::EmptyTempDir();
		}

		/***database connect***/
		$db = DB::getInstance();

			/***prefill model names selector***/
				$models = array();
				$models = $db->FetchAll("SELECT m.id, m.title AS `modelname`, m.slug
							FROM `bx_content` AS `m`
							WHERE m.type = 'model'
							ORDER BY m.title");
				if (count($models) === 0) {
					if (mysql_error()){
					    die($translate->_('Database Query Failed')
					    .(Config::Get('debug')===true)?mysql_error():"");
					}
					else{ //No models found in database.
					    $tpl->errormessage($translate->_('Add at least one model').' <a href="'.Path::Get('url:admin').'/model">'.$translate->_('Add a model').'</a>.');
				    }
				}

			/***prefill the country select box***/
				$cache = Caching::Setup('element', true);
				if(!$countries = $cache->load('countries')) {
					$countries = $db->FetchAll("SELECT `country` FROM `bx_country` ORDER BY `country`");
					if(is_array($countries)) $cache->save($countries, 'countries');
				}

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***database disconnect***/
		unset($db);


		/***TEMPLATE ASSIGNMENTS***/

		if(isset(self::$update_id)) $tpl->assign("updateid", self::$update_id);

		//mark selected country
		if(!isset(self::$output['country']))
			self::$output['country'] = Config::Get('default_country');

		//prefill models
		$tpl->assign("models", $models);

		//options
		$tpl->assign("options", self::$o);

		//output
		if (isset(self::$output['type']))
			if (self::$output['type'] == 'video')
				self::$output['video_thumbs'] = self::$output['thumbs'];
			else
				self::$output['set_thumbs'] = self::$output['thumbs'];
		$tpl->assign("output", self::$output);

		//error/success
		if (self::$success || isset($_GET['success']))
			$tpl->successmessage($translate->_("Done"));
		if (isset($_GET['abort']))
			$tpl->errormessage($translate->_("Aborted"));
		if (self::$errors)
		{
			if(count(self::$errors) > 1) $tpl->assign("errors", self::$errors);
			else $tpl->errormessage(self::$errors[0]);
		}
		if (self::$errorcss) $tpl->assign("errorcss", self::$errorcss);

		if (isset($_GET['error']))
		{
			$error = urldecode(Input::clean_single('g', 'error', 'NOHTML'));
			$tpl->errormessage($translate->_($error));
		}

		//videourl prefill
		$tpl->assign("_videourl", Path::Get('url:member/videos') . '/');
		//trailer url prefill
		$tpl->assign("_freevideourl",  Path::Get('url:free/videos') . '/');

		//array with mandatory items of the form
		if(isset(self::$mandatory))
		{
			self::$mandatory = array_flip(self::$mandatory);
			$tpl->assign("mandatory", self::$mandatory);
		}

		//form remove check
		$_frm = array();
		$form_remove_update = Config::Get('form_remove_update');
		foreach ($form_remove_update as $thekey)
		{
			$_frm["form_".$thekey] = true;
		}
		$tpl->apply($_frm);

		//type, min, max of rating and step size
		$tpl->assign("rating", Config::Get('rating'));

		//supported file types
		$tpl->assign("allowed_mime_types", implode(", ", Config::Get('image_extensions')));

		//thumbsizes
		$tpl->assign("picturethumbsizes", self::$o['picture_thumbnailsize']);
		//thumbsizes
		$tpl->assign("videothumbsizes", self::$o['video_thumbnailsize']);

		//prefill countries
		if(isset($countries)) $tpl->assign("countries", $countries);

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"2257url" => "2257 URL",
			"aliases" => "Aliases",
			"country" => "Country",
			"date" => "Date",
			"description" => "Description",
			"gotostep2" => "Go to Step 2",
			"preparing_framegrab" => "Preparing to Grab Frames",
			"identificationurl" => "Identification Url",
			"image" => "Image",
			"initialrating" => "Initial Rating",
			"lat" => "Lat",
			"listoftags" => "List of tags",
			"lng" => "Lng",
			"location" => "Location",
			"miscurl" => "Misc Url",
			"model" => "Model",
			"modelreleaseurl" => "Model Release Url",
			"models" => "Models",
			"notes" => "Notes",
			"optional" => "Optional",
			"pictures" => "Pictures",
			"productiondate" => "Production Date",
			"realname" => "Real Name",
			"rating" => "Rating",
			"reset" => "Reset",
			"save" => "Save",
			"scheduleddate" => "Scheduled Date",
			"slug" => "Slug",
			"state" => "State",
			"submitupdate" => "Submit Update",
			"success" => "Success",
			"supportedfiletypes" => "Supported File Types",
			"tags" => "Tags",
			"this_can_take_minutes" => "This can take several minutes",
			"thumbnail" => "Thumbnail",
			"thumbnails" => "Thumbnails",
			"title" => "Title",
			"update" => "Update",
			"updateid" => "Update-ID",
			"upload" => "Upload",
			"uploadimage" => "Upload Image",
			"urlto2257" => "Url to the 2257",
			"video" => "Video",
			"zipcode" => "Zipcode",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

	} //index

/**
* edit
*
* @access private
*/
	private function edit()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		//get tpl
		$tpl = Zend_Registry::get('tpl');

		/*** get prefill data from database ***/

		/***database connect***/
		$db = DB::getInstance();

			/* update data */
			self::$output = $db->Row("SELECT u.type AS `type`, u.title, u.memberpath AS `picturefolder`, u.freepath, u.slug, u.description,
						DATE(FROM_UNIXTIME(u.dateline)) AS date,
						l.location, l.zipcode, l.state, l.LatLng,
						c.country,
						AVG(r.rating) AS `rating`
						FROM `bx_content` AS `u`
						LEFT JOIN `bx_location` AS `l` ON (l.id=u.location_id)
						LEFT JOIN `bx_country` AS `c` ON (c.iso=l.country_iso)
						LEFT JOIN `bx_rating` AS `r` ON (r.content_id=u.id)
						WHERE u.id = ?
						GROUP BY u.id", array(self::$id));
			if(!self::$output) throw new Exception("ID does not exist");

			//internal type (for paths)
			self::$internal_type = self::$output['type']; //ex.: 'set'

			/* selected models (prefill is in index) */
			self::$output['models'] = array();
			$_temp = $db->FetchAll("SELECT A.content_id AS `id`
						FROM `bx_2257` AS `A`, `bx_2257` AS `B`
						WHERE A.id=B.parent AND B.content_id = ?",
						array(self::$id));
			//reduce dimension
			foreach($_temp as $t)
				self::$output['models'][] = $t['id'];
			unset($_temp);

			/* selected models 2257 info */
			$ids = implode(",", self::$output['models']);
			$_temp = $db->FetchAll("SELECT f.modelrelease_path AS `modelreleaseurl`,
						f.misc_url AS `miscurl`, f.parent AS `id`, f.production_date, f.notes
						FROM `bx_2257` AS `f`
						WHERE f.content_id = ?",array(self::$id));
			//build output
			foreach($_temp as $t)
			{
				self::$output['modelreleaseurl'][$t['id']] = $t['modelreleaseurl'];
				self::$output['miscurl'][$t['id']] = $t['miscurl'];
			}
			//get one production date and notes
			self::$output['productiondate'] = Input::clean($_temp[0]['production_date'],'NOHTML');
			self::$output['notes'] = Input::clean($_temp[0]['notes'],'NOHTML');
			unset($_temp);

			/* tags */
			//get the tags for the update
			$_temp = $db->FetchAll("SELECT t.tag
						FROM `bx_tag` AS `t`, `bx_tag_content` AS `tc`
						WHERE tc.content_id = ?
						AND t.id=tc.tag_id", array(self::$id));

			//reduce tag array
			$tags = "";
			$i = 0;
			foreach ($_temp as $key => $value)
			{
				$tags .= $_temp[$i]['tag']. ", ";
				$i++;
			}
			$tags = substr_replace($tags, "", -2);
			unset($_temp);

			self::$output = self::$output + array("tags" => $tags);

			//fill the fields that we don't need with stuff so that the mandatory check below is not set off...
			if(self::$output['type']== 'set')
			{
				self::$output['videourl'] = ' ';
				self::$output['freevideourl'] = ' ';
			}
			else
			{
				self::$output['picturefolder'] = ' ';
			}

			/* thumbnails */
			$thumbnails = $db->FetchAll("SELECT th.path AS `path`, th.internal_id
						FROM `bx_thumbnail` AS `th`
						JOIN `bx_content` AS `c` ON (th.content_id = c.id)
						WHERE c.id = ?
						AND th.theme = ?
						ORDER BY th.internal_id ASC", array(
							self::$id,
							self::$o['frontend_theme']
						));

			//check if thumbs exist
			for ($i=0, $s=count($thumbnails); $i<$s; $i++)
			{
				//remove from thumbnail array and check later
				if(!file_exists( Path::Get('path:site') . $thumbnails[$i]['path'] )) unset($thumbnails[$i]);
			}

			//merge the thumbnails into output array
			self::$output['thumbs'] = $thumbnails;

			//tell the template what to do (for showing the existing thumbnails)
			self::$output['action'] = 'edit';

			/*** check for errors ***/
				//remove mandatory items for other type
				if(self::$output['type'] == 'set')
				{
					unset(self::$mandatory['videodirectory']);
					unset(self::$mandatory['videourl']);
					unset(self::$mandatory['freevideourl']);

					//don't trip the mandatory alarm
					self::$output['videodirectory'] = ' ';
					self::$output['videourl'] = ' ';
					self::$output['freevideourl'] = ' ';
				}
				elseif(self::$output['type'] == 'video')
				{
					unset(self::$mandatory['picturefolder']);

					//don't trip the mandatory alarm
					self::$output['picturefolder'] = ' ';
				}

				//mandatory variable check
				foreach(self::$mandatory as $check)
				{
					if(!isset(self::$output[$check]))
					{
						self::$errors[] = "Missing ".$check;
						self::$errorcss[$check] = ' error';
					}
					elseif(!self::$output[$check])
					{
						self::$errors[] = "Missing ".$check;
						self::$errorcss[$check] = ' error';
					}
				}

				//thumbnail check
				//all thumbs are mandatory
				for($i=0, $s = count( (self::$output['type']=='set' ? self::$o['picture_thumbnailsize'] : self::$o['video_thumbnailsize']) ); $i<$s; $i++)
				{
					if(!isset(self::$output['thumbs'][$i]))
					{
						self::$errors[] = "Missing thumbnail ".$i;
						self::$errorcss['thumbs'][$i] = ' error';
					}
				}

		//that is it.
		//updating the content is handled in addnew()

	} //edit

/**
* addnew
*
* @access private
*/
	private function addnew()
	{
		//get tpl
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

			/*** get input data ***/
			self::$output = Input::clean_array('p',array(
				'date'			=>	'DATE',
				'description'		=>	'STR',
//				'duration'		=>	'NOHTML',
				'freevideourl'		=>	'NOHTML',
				'picturefolder'		=>	'NOHTML',
				'rating'		=>	'UNUM',
				'slug'			=>	'NOHTML',
				'tags'			=>	'NOHTML',
				'title'			=>	'NOHTML',
				'type'			=>	'NOHTML',
				'videourl'		=>	'NOHTML',
				'videodirectory'	=>	'NOHTML',

				'basepath'		=>	'NOHTML',

			//2257 data
				'country'		=>	'NOHTML',
				'LatLng'		=>	'LATLNG',
				'location'		=>	'NOHTML',
				'models'		=>	'ARRAY_NOHTML',
				'notes'			=>	'NOHTML',
				'productiondate'	=>	'DATE',
				'state'			=>	'NOHTML',
				'zipcode'		=>	'UINT',
				'miscurl'		=>	'ARRAY_NOHTML',
				'modelreleaseurl'	=>	'ARRAY_NOHTML',
			));
//			if (self::$output['rating'] == null) self::$output['rating'] = '-';

			//BASE
				//path
				if (!self::$output['basepath'])	throw new Exception('missing base path');
				//no slash!
				self::$output['basepath'] = rtrim(self::$output['basepath'], "/");
				self::$output['basepath'] = rtrim(self::$output['basepath'], DIR_SEP);










//!!! this needs to be used somewhere?

				//relative path (for db)
				self::$output['baserel'] = String::Slash(str_replace(Path::Get('path:site'), "", self::$output['basepath']), 1, 0);
				if(empty(self::$output['baserel'])) self::$output['baserel'] = '/';

			//VIDEO
				self::$output['videodirectory'] = String::Slash(self::$output['videodirectory'],1,1);
				self::$output['internal_videodirectory'] = self::$output['basepath'].self::$output['videodirectory'];

			//PICS
				self::$output['picturefolder'] = String::Slash(self::$output['picturefolder'],1,1);
				self::$output['picturepath'] = self::$output['basepath'] . self::$output['picturefolder'];

			//type
			if (!self::$output['type'])	throw new Exception('no update type specified');

			//internal type (for paths)
			self::$internal_type = Path::Get(self::$output['type']); //set => pics
			if (!self::$internal_type) die("Invalid update type");

			//internally, we only deal with a sanitized version of the slug directory
			$noref = self::$output['slug'];
			self::$internal_slug = strtolower(Input::clean($noref, 'FILENAME'));
			unset($noref);

			//nicEdit adds an annoying "<br>" at end of text that needs to be removed
			self::$output['description'] = String::rTrimBr(self::$output['description']);

			//thumbs
			self::$output = array_merge(self::$output, Input::clean_array('p',array(
				'set_thumbs'   => 'ARRAY_NOHTML',
				'video_thumbs' => 'ARRAY_NOHTML'
			)));


			/*** check for errors ***/
				//mandatory variable check
				foreach(array_flip(self::$mandatory) as $key => $value)
				{
					if(!self::$output[$key])
					{
						//error name
						self::$errors[] = $key;
						//error css
						self::$errorcss[$key] = ' error';
					}
				}

				if (self::$output['type'] == 'set')
				{
					//thumbnail check
					//all thumbs are mandatory
					for($i=0, $s = count(self::$o['picture_thumbnailsize']); $i<$s; $i++)
					{
						if(!isset(self::$output['set_thumbs'][$i]))
						{
							self::$errors[] = "Missing thumbnail ".($i+1);
							self::$errorcss['set_thumbs'][$i] = ' error';
						}
					}

					//check paths
					if(rtrim(self::$output['picturepath'], '/') == Path::Get('path:member/pics'))
					{
						self::$output['picturefolder'] = '';
						self::$errorcss['picturefolder'] = ' error';
						self::$errors[] = "Folder empty.";
					}
					elseif(!is_dir(self::$output['picturepath']) || Filehandler::is_empty_folder(self::$output['picturepath']))
					{
						self::$errorcss['picturefolder'] = ' error';
						self::$errors[] = "Folder does not exist.";
					}
					if(isset(self::$errorcss['videodirectory'])) unset(self::$errorcss['videodirectory']);
					if(isset(self::$output['videodirectory'])) unset(self::$output['videodirectory']);
					if(isset(self::$errorcss['videourl'])) unset(self::$errorcss['videourl']);
					if(isset(self::$output['videourl'])) unset(self::$output['videourl']);
					if(isset(self::$errorcss['freevideourl'])) unset(self::$errorcss['freevideourl']);
					if(isset(self::$output['freevideourl'])) unset(self::$output['freevideourl']);

					self::$output['picturepath'] = rtrim(self::$output['picturepath'], "/").'/';

					//rename thumbs
					self::$output['thumbs'] = self::$output['set_thumbs'];

					//unset old
					if(isset(self::$output['video_thumbs'])) unset(self::$output['video_thumbs']);
					if(isset(self::$output['set_thumbs'])) unset(self::$output['set_thumbs']);


					//get thumbsizes
					$thumbsizes = self::$o['picture_thumbnailsize'];
				}
				elseif (self::$output['type'] == 'video')
				{
					//thumbnail check
					//all thumbs are mandatory
					for($i=0, $s = count(self::$o['video_thumbnailsize']); $i<$s; $i++)
					{
						if(!isset(self::$output['video_thumbs'][$i]))
						{
							self::$errors[] = "Missing thumbnail ".($i+1);
							self::$errorcss['video_thumbs'][$i] = ' error';
						}
					}

					//check paths
					if(String::Slash(self::$output['videourl'], 1, 0) == Path::Get('url:member/videos'))
					{
						self::$output['videourl'] = '';
						self::$errorcss['videourl'] = ' error';
					}
/*
					//check if trailer url is mandatory
					if(String::AddSlashes(self::$output['freevideourl'], 1, 1) == path::Get('url:free') . '/')
					{
						self::$output['freevideourl'] = '';
						self::$errorcss['freevideourl'] = ' error';
					}
*/
					if(isset(self::$errorcss['picturefolder'])) unset(self::$errorcss['picturefolder']);
					if(isset(self::$output['picturefolder'])) unset(self::$output['picturefolder']);

					//rename thumbs
					self::$output['thumbs'] = self::$output['video_thumbs'];

					//unset old
					if(isset(self::$output['video_thumbs'])) unset(self::$output['video_thumbs']);
					if(isset(self::$output['set_thumbs'])) unset(self::$output['set_thumbs']);


					//get thumbsizes
					$thumbsizes = self::$o['video_thumbnailsize'];
				}

				//LatLng check
				if(is_null(self::$output['LatLng']) && in_array('LatLng',self::$mandatory))
				{
					self::$errors[] = "Lat/Lng must be two float numbers separated by comma.";
					self::$errorcss['LatLng'] = ' error';
				}
			/*** /check for errors ***/



			if (count(self::$errorcss) === 0)
			{
			        // If the error array is empty, there were no errors.
				// all required variables were filled

				//$thumbmove = array();
				$old2257 = array();


				/***database connect***/
				$db = DB::getInstance();

				try {
					/************************/
					/* edit existing update */
					/************************/
					if(isset(self::$id) && self::$id != 0)
					{

						//check if update exists
						$check = $db->Row("SELECT `id`, `slug` FROM `bx_content` WHERE `id` = ? LIMIT 1", array(self::$id));

						if (!$check) throw new Exception('update does not exist');


						/* update type cannot be changed */


						/* picturefolder and videourl/freevideourl change */ //ok

						$oldpaths = $db->Row("SELECT `memberpath`, `freepath` FROM `bx_content` WHERE `id` = ?", array(self::$id));

						if($oldpaths)
						{
							if(self::$output['type'] == 'set')
							{
								Rollback::$log['path_update'] = $db->Update("UPDATE `bx_content`
										SET `memberpath` = ?,
										`freepath` = ?
										WHERE `id` = ?",
									array(
									self::$output['picturepath'],
									Path::Get('rel:free/pics').'/'.self::$internal_slug.'/',
									self::$id
								));
							}
							else
							{
								Rollback::$log['path_update'] = $db->Update("UPDATE `bx_content`
										SET `memberpath` = ?,
										`freepath` = ?
										WHERE `id` = ?",
										array(
											self::$output['videourl'],
											self::$output['freevideourl'],
											self::$id
										));
							}

							//for now: manual moves in members area necessary



						}

						/* slug and thumbs */

							//check if the slug was updated in the edit
							$oldslug = $check['slug'];
							$newslug = strtolower(self::$output['slug']);

							$oldinternalslug = strtolower(Input::clean($oldslug, 'FILENAME'));
							$newinternalslug = strtolower(Input::clean($newslug, 'FILENAME'));

							$oldinternalslugpath = Path::Get('path:thumbs') . String::Slash(self::$internal_type, 1, 1).$oldinternalslug;
							$newinternalslugpath = Path::Get('path:thumbs') . String::Slash(self::$internal_type, 1, 1).$newinternalslug;

							if($oldinternalslugpath == $newinternalslugpath)
							{
								//slug was not updated, we will simply use the existing value

								//check if the old slug folder exists

								if (!is_dir( $oldinternalslugpath ))
								{
									//try to create missing slug directory
									if(!Filehandler::MkDir($newinternalslugpath, true))
									{
										//could not create the thumbnail directory
										throw new Exception('could not create slug directory');
									}
									else
										Rollback::$log['newslugcreation'] = true;
								}
							}
							else //the slug was changed
							{
								//try to rename the old slug directory

								if(!@Filehandler::MoveDir($oldinternalslugpath, $newinternalslugpath))
								{
									//could not simply rename the old slug directory

									//try to at least create the new directory
									if(!@Filehandler::MkDir($newinternalslugpath, true))
									{
										//could not create the thumbnail directory
										throw new Exception('could not create slug directory');
									}
									else
										Rollback::$log['newslugcreation'] = true;
								}
								else
									Rollback::$log['slugrenamed'] = true;

								//update entry in database
								Rollback::$log['slugdbupdate'] = $db->Update("UPDATE `bx_content`
												SET `slug` = ?
												WHERE `id` = ?
												LIMIT 1",
												array(
													$newslug,
													self::$id,
												));
							}

							//we now have a valid newinternalslugpath


							//rename thumbnails as they come!

							$oldthumbs = $db->FetchAll("SELECT * FROM `bx_thumbnail`
											WHERE `content_id` = ?
											AND `type` = ?
											AND `theme` = ?",
											array(
												self::$id,
												self::$output['type'],
												self::$o['frontend_theme']
											));

							for ($i=0, $s=count(self::$output['thumbs']); $i<$s; $i++)
							{
								$ext = String::GetFileExtension(self::$output['thumbs'][$i]);

								//safety copy
								@Filehandler::SmartCopy($oldinternalslugpath. DIR_SEP .$i.'.'.$ext, Path::Get('path:admin/temp'). DIR_SEP .$i.'.'.$ext);
								Rollback::$log['thumbintemp'][$i]['temppath'] = Path::Get('path:admin/temp'). DIR_SEP .$i.'.'.$ext;
								Rollback::$log['thumbintemp'][$i]['dest'] = $oldinternalslugpath. DIR_SEP .$i.'.'.$ext;

								if(!preg_match("~^".String::Slash(Path::Get('rel:thumbs'),1,1)."~", self::$output['thumbs'][$i]))
								{
									//the thumbnail was updated

									//delete the old thumbnail
									@Filehandler::DeleteFile($newinternalslugpath . DIR_SEP .$i.'.'.$ext);

									//thumb was changed and needs to be moved
									if(!@Filehandler::MoveFile(Path::Get('path:admin/temp') . DIR_SEP . self::$output['thumbs'][$i],
									rtrim($newinternalslugpath, '/'). DIR_SEP . $i . '.' . $ext))
									{
										//error marking
										self::$errorcss['thumbs'][$i] = ' error';
										throw new Exception ("could not add new thumbnail");
									}
									//else
										//$thumbmove[$i]['current'] = String::Slash($newinternalslugpath,1,1).$i.'.'.$ext;
										//$thumbmove[$i]['dest'] = Path::Get('path:admin/temp'). DIR_SEP .self::$output['thumbs'][$i];

									//update the database

										//delete old thumbnail
										$db->Update("DELETE FROM `bx_thumbnail`
											WHERE `internal_id` = ?
											AND `type` = ?
											AND `content_id` = ?
											AND `theme` = ?
											LIMIT 1",
											array(
												$i,
												self::$output['type'],
												self::$id,
												self::$o['frontend_theme']
											));

										$th = path::Get('rel:thumbs') . String::Slash(self::$internal_type, 1, 1) .$newinternalslug.'/'.$i.'.'.$ext;
										//add new thumbnail
										$db->Update("INSERT INTO `bx_thumbnail` (
											`id`,
											`content_id`,
											`path`,
											`internal_id`,
											`type`,
											`width`,
											`height`,
											`theme`,
											`dateline`
										) VALUES (
											?,?,?,?,?,?,?,?,?
										)",array(
											NULL,
											self::$id,
											$th,
											$i,
											self::$output['type'],
											$thumbsizes[$i]['width'],
											$thumbsizes[$i]['height'],
											self::$o['frontend_theme'],
											TIME_NOW,
										));
								}
								else
								{
									//thumbnail was not changed

									//update the path of the thumbnail if slug was changed
									if($oldinternalslugpath != $newinternalslugpath)
									{
										$db->Update("UPDATE `bx_thumbnail`
												SET `path` = ?
												WHERE `internal_id` = ?
												AND `type` = ?
												AND `content_id` = ?
												AND `theme` = ?",
												array(
												Path::Get('rel:thumbs') . String::Slash(self::$internal_type,1,1) .$newinternalslug.'/'.$i.'.'.$ext,
												$i,
												self::$output['type'],
												self::$id,
												self::$o['frontend_theme']
										));
									}
								}
							}

							Rollback::$log['thumbupdate'] = true;

						/* country */ //ok

							//get the country iso code
							$countryiso = $db->Column("SELECT `iso` FROM `bx_country` WHERE `country` = ?", array(self::$output['country']));

							if(!$countryiso) {
								//check if country is mandatory
								if(in_array('country', array_flip(self::$mandatory)))
								{
									//abort update process, because every location/model must have a country
									throw new Exception('missing country');
								}
								else
								{
									$countryiso = null;
								}
							}

						/* location */ //ok

							//check if location is already in database
							$location_id = $db->Column("SELECT l.id
							FROM `bx_location` AS `l`, `bx_content` AS `c`
							WHERE l.id = c.location_id
							AND c.id = ?
							LIMIT 1",
							array(self::$id));

							if(!$location_id)
							{
								//write new location

								//location description is not used for now
								Rollback::$log['location_insert'] = $db->Update("INSERT INTO `bx_location` (
								`id`, `location`, `state`, `zipcode`, `description`, `LatLng`, `country_iso`
								) VALUES (
								?, ?, ?, ?, NULL, ?, ?
								)", array(
								NULL, self::$output['location'], self::$output['state'], self::$output['zipcode'], self::$output['LatLng'], $countryiso));

								$location_id = $db->LastInsertId();
								if(!$location_id) $location_id = null;
							}
							else
							{
								//update existing location

								//safety copy for rollback
								$oldlocation = $db->Row("SELECT * FROM `bx_location` WHERE `id` = ?", array($location_id));

								//location description is not used for now
								Rollback::$log['location_update'] = $db->Update("UPDATE `bx_location` SET
								`location` = ?,
								`state` = ?,
								`zipcode` = ?,
								`description` = ?,
								`LatLng` = ?,
								`country_iso` = ?
								WHERE `id` = ?", array(
								self::$output['location'],
								self::$output['state'],
								self::$output['zipcode'],
								NULL,
								self::$output['LatLng'],
								$countryiso,
								$location_id));
							}

						/* update */

							//safety copy for rollback
							$oldupdate = $db->Row("SELECT * FROM `bx_content` WHERE `id` = ?", array(self::$id));

							if(self::$output['type'] == 'video')
							{
								Rollback::$log['content_update'] = $db->Update("UPDATE `bx_content` SET
								`slug` = ?,
								`title` = ?,
								`memberpath` = ?,
								`freepath` = ?,
								`description` = ?,
								`dateline` = UNIX_TIMESTAMP(?),
								`location_id` = ?
								WHERE `id` = ?", array(
								self::$output['slug'],
								self::$output['title'],
								self::$output['videourl'],
								self::$output['freevideourl'],
								self::$output['description'],
								self::$output['date'],
								$location_id,
								self::$id));
							}
							elseif(self::$output['type'] == 'set')
							{
								Rollback::$log['content_update'] = $db->Update("UPDATE `bx_content` SET
								`slug` = ?,
								`title` = ?,
								`memberpath` = ?,
								`freepath` = ?,
								`description` = ?,
								`dateline` = UNIX_TIMESTAMP(?),
								`location_id` = ?
								WHERE `id` = ?", array(
								self::$output['slug'],
								self::$output['title'],
								self::$output['picturefolder'],
								Path::Get('rel:free/pics') .'/' . self::$internal_slug . '/',
								self::$output['description'],
								self::$output['date'],
								$location_id,
								self::$id));
							}

						/* 2257 entry */ //ok

							//backup old
							$old2257 = $db->FetchAll("SELECT * FROM `bx_2257` WHERE `content_id` = ?", array(self::$id));

							//delete old
							$db->Update("DELETE FROM `bx_2257` WHERE `content_id` = ?",array(self::$id));

							//insert new
								//get parent 2257 entries (model ids)
								$models2257 = array();
								for($i=0, $s=count(self::$output['models']); $i<$s; $i++)
								{
									$models2257[self::$output['models'][$i]] = $db->Column("SELECT `id` FROM `bx_2257` WHERE `content_id` = ?",array(self::$output['models'][$i]));
								}

								//one 2257 update entry for every model
								foreach(self::$output['models'] AS $m)
								{
									$db->Update("INSERT INTO `bx_2257` (
									`id`, `content_id`, `modelrelease_path`, `misc_url`, `location_id`, `production_date`, `notes`, `parent`, `dateline`
									) VALUES (
									?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
									NULL,
									self::$id, self::$output['modelreleaseurl'][$m],
									self::$output['miscurl'][$m],
									$location_id,
									self::$output['productiondate'],
									self::$output['notes'],
									$models2257[$m],
									TIME_NOW));

									Rollback::$log['_2257_ids'][] = $db->LastInsertId();
								}

						/* model_has_set relation */

							//backup old
							$old_has_set = $db->FetchAll("SELECT * FROM `bx_model_has_set` WHERE `content_id` = ?", array(self::$id));

							//delete old
							$db->Update("DELETE FROM `bx_model_has_set` WHERE `content_id` = ?", array(self::$id));

							$sql = "INSERT INTO `bx_model_has_set` (`model_id`, `content_id`) VALUES ";
							foreach(self::$output['models'] AS $m)
							{
								$sql .= $db->Prepare("(?,?),", array($m, self::$update_id));
							}
							$sql = trim($sql, ",");

							Rollback::$log['model_has_set_update'] = true;

						/* tags */ //ok

							if(!empty(self::$output['tags']))
							{
								$tags = explode(",", self::$output['tags']);

								//remove duplicate tags in array
								$tags = array_unique($tags);

								//trim array
								$tags = Arr::Trim($tags);

								//safety copy
								$oldtag_content = $db->FetchAll("SELECT * FROM `bx_tag_content`
								WHERE `content_id` = ?", array(self::$id));
;
								//remove old tags
								Rollback::$log['tag_content_update'] = $db->Update("DELETE FROM `bx_tag_content`
								WHERE `content_id` = ?", array(self::$id));

								foreach($tags as $tag)
								{
									if($tag != '')
									{
										//check if tag exists
										$exists = $db->Column("SELECT `id` FROM `bx_tag` WHERE `tag` = ? LIMIT 1", array($tag));

										$tagid = 0;
										if(!$exists)
										{
											//write tag if not exists
											//description of tags is not used for now.
											$db->Update("INSERT INTO `bx_tag` (
											`id`, `tag`, `description`
											) VALUES (
											?, ?, ?
											)", array(NULL, Input::clean($tag,'NOHTML'), NULL));

											$tagid = $db->LastInsertId();
											Rollback::$log['tag_inserts'] .= "'".$tagid . "',";
										}
										else
										{
											//else get id of tag query from above
											$tagid = $exists;
										}

										$db->Update("INSERT INTO `bx_tag_content` (
										`content_id`, `tag_id`, `dateline`
										) VALUES (
										?,?,?
										)", array(self::$id, $tagid, TIME_NOW));

									}
								} //foreach

								Rollback::$log['tag_inserts'] = rtrim(Rollback::$log['tag_inserts'], ",");
							}

						/* rating */

							if(self::$output['rating'] != null && isset($_POST['resetrating']) && $_POST['resetrating'] == '1')
							{
								//backup old
								$old_ratings = $db->FetchAll("SELECT * FROM `bx_rating` WHERE `content_id`=?", array(self::$id));

								//resetting the rating will remove all user ratings!

								$db->Update("DELETE FROM `bx_rating` WHERE `content_id`=?",
								array(self::$id));

								//member #1 = usually admin user account
								if(self::$o['admin_member_account'])
								{
									$db->Update("INSERT INTO `bx_rating` (
										`id`,
										`content_id`,
										`rating`,
										`member_id`,
										`dateline`
									) VALUES (
										?, ?, ?, ?, ?
									)", array(
										NULL,
										self::$id,
										self::$output['rating'],
										self::$o['admin_member_account'],
										TIME_NOW
									));

									Rollback::$log['ratings'] = true;
								}
							}

						//success!
						self::$success = true;

						Logger::AdminActivity('edited update', self::$update_id);

					} //edit update
					/******************/
					/* add new update */
					/******************/
					else
					{
						/* slug */ //ok

							//slug is mandatory
							//slug exists check
							$slugcheck = Tools::slug_exists_check(self::$output['slug']);

							if(!$slugcheck){ //slug already exists, but must be unique!
								//error marking
								self::$errorcss['slug'] = ' error';
								throw new Exception('slug already exists');
							}

						/* country */ //ok

							//get the country iso code
							$countryiso = $db->Column("SELECT `iso` FROM `bx_country` WHERE `country`=?", array(self::$output['country']));

							if(!$countryiso) {
								//check if country is mandatory
								if(in_array('country', array_flip(self::$mandatory)))
								{
									//abort update process, because every location/update must have a country
									throw new Exception('missing country');
								}
								else
									$countryiso = null;
							}

						/* location */ //ok

							//check if location is already in database
							//checks country, too, to allow duplicate locations in different countries
							$addlocation_id = $db->Column("SELECT `id`
									FROM `bx_location`
									WHERE `location`=?
									AND `country_iso`=?
									LIMIT 1",
									array(self::$output['location'], $countryiso));

							if(!$addlocation_id) //not yet in database
							{
								//write new location

								//location description is not used for now
								Rollback::$log['location_insert'] = $db->Update("INSERT INTO `bx_location` (
								`id`,`location`,`state`,`zipcode`,`description`,`LatLng`,`country_iso`
								) VALUES (
								?, ?, ?, ?, NULL, ?, ?
								)", array(NULL, self::$output['location'], self::$output['state'], self::$output['zipcode'], self::$output['LatLng'], $countryiso));

								$addlocation_id = (!$addlocation_id ? null : $db->LastInsertId());
							}

						/* update */ //ok

							//use the autoincrement id to insert the update into the bx_content table

							if(self::$output['type'] == 'set')
							{
								Rollback::$log['update_insert'] = $db->Update("INSERT INTO `bx_content` (
									`id`, `type`, `slug`, `title`, `memberpath`, `freepath`, `description`, `count`, `dateline`, `location_id`
								) VALUES (
									?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(?), ?
								)", array(
									NULL,
									'set',
									self::$output['slug'],
									self::$output['title'],
									self::$output['picturefolder'],
									Path::Get('rel:free/pics') . '/' . self::$internal_slug . '/',
									self::$output['description'],
									'0',
									self::$output['date'],
									$addlocation_id
								));
							}
							else //video
							{
								Rollback::$log['update_insert'] = $db->Update("INSERT INTO `bx_content` (
									`id`,
									`type`,
									`slug`,
									`title`,
									`memberpath`,
									`freepath`,
									`description`,
									`count`,
									`dateline`,
									`location_id`
								) VALUES (
									?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(?), ?
								)", array(
									NULL,
									'video',
									self::$output['slug'],
									self::$output['title'],
									self::$output['videourl'],
									self::$output['freevideourl'],
									self::$output['description'],
									'0',
									self::$output['date'],
									$addlocation_id
								));
							}

							self::$update_id = $db->LastInsertId();

							if(!self::$update_id) {
								//abort update process, because we have no content_id
								throw new Exception('missing update id');
							}

						/* 2257 entry */ //ok

							//get parent 2257 entries (model ids)
							$models2257 = array();
							for($i=0, $s=count(self::$output['models']); $i<$s; $i++)
							{
								$models2257[self::$output['models'][$i]] = $db->Column("SELECT `id`
										FROM `bx_2257`
										WHERE `content_id`=?",array(
											self::$output['models'][$i]
										));
							}

							//one 2257 update entry for each model
							foreach(self::$output['models'] AS $m)
							{
								$db->Update("INSERT INTO `bx_2257` (
								`id`, `content_id`, `modelrelease_path`, `misc_url`, `location_id`, `production_date`, `notes`, `parent`, `dateline`
								) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
								NULL,
								self::$update_id,
								self::$output['modelreleaseurl'][$m],
								self::$output['miscurl'][$m],
								$addlocation_id,
								self::$output['productiondate'],
								self::$output['notes'],
								$models2257[$m],
								TIME_NOW));

								Rollback::$log['_2257_ids'][] = $db->LastInsertId();
							}

						/* model_has_set relation */

							foreach(self::$output['models'] AS $m)
							{
								$db->Update("INSERT INTO `bx_model_has_set` (
								`model_id`,`content_id`) VALUES (?,?)", array(
								$m, self::$update_id));
							}

							Rollback::$log['model_has_set_insert'] = true;

						/* thumbnails moving */ //ok

							if(self::$output['type'] == 'set')
								$thumbfolder = Path::Get('path:thumbs/pics') . DIR_SEP . self::$internal_slug . DIR_SEP;
							else
								$thumbfolder = Path::Get('path:thumbs/videos') . DIR_SEP . self::$internal_slug . DIR_SEP;

							//create the subfolder in the thumb directory
							if(!@Filehandler::MkDir($thumbfolder, true))
							{
								//could not create the thumbnail directory
								throw new Exception('could not create slug directory.<br>'.
								"Check ".Path::Get('rel:thumbs/pics').'/'.self::$internal_slug.'/');
							}
							else
								Rollback::$log['thumbfolder_creation'] = true;

							//for each thumbnail
							for($i=0, $s = count(self::$output['thumbs']); $i<$s; $i++)
							{

								$ext = String::GetFileExtension(self::$output['thumbs'][$i]);

								//thumb filename
								$thfilename = $i . '.' .$ext;

								//the thumbnail
								$thumbpath = $thumbfolder . $thfilename;

								//file exists check
									$k=0;
									while(is_file($thumbpath))
									{
										$thfilename = $thfilename.  "-" . $k . '.' .$ext;
										//the thumbnail
										$thumbpath = $thumbfolder . $thfilename;
										$k++;
									}

								//url (written to database)
								if(self::$output['type'] == 'set')
								{
									$thumburl = Path::Get('rel:thumbs/pics') . '/' . self::$internal_slug . '/' . $thfilename;
								}
								else
								{
									$thumburl = Path::Get('rel:thumbs/videos') . '/' . self::$internal_slug . '/' . $thfilename;
								}

								//move uploaded thumbnail
								if(!@Filehandler::SmartCopy(Path::Get('path:admin/temp'). DIR_SEP .self::$output['thumbs'][$i], $thumbpath))
								{
									@Filehandler::DeleteFile($thumbpath);
									throw new Exception('thumbnail could not be moved');
								}

								$db->Update("INSERT INTO `bx_thumbnail` (
									`id`, `content_id`, `path`, `internal_id`, `type`, `width`, `height`, `theme`, `dateline`
								) VALUES (
									?,?,?,?,?,?,?,?,?
								)", array(
									NULL,
									self::$update_id,
									$thumburl,
									$i,
									self::$output['type'],
									$thumbsizes[$i]['width'],
									$thumbsizes[$i]['height'],
									self::$o['frontend_theme'],
									TIME_NOW
								));

								Rollback::$log['thumbnail_insert_ids'] .= "'".$db->LastInsertId()."',";
							}
							//remove trailing comma
							Rollback::$log['thumbnail_insert_ids'] = rtrim(Rollback::$log['thumbnail_insert_ids'], ",");

						/* tags */ //ok

							if(!empty(self::$output['tags']))
							{
								$tags = explode(",", self::$output['tags']);

								//remove duplicate tags in array
								$tags = array_unique($tags);

								foreach($tags as $tag)
								{
									if($tag != '')
									{
										//check if tag exists
										$exists = $db->Column("SELECT `id` FROM `bx_tag` WHERE `tag`=? LIMIT 1", array($tag));

										$tagid = 0;
										if(!$exists)
										{
											//write tag if not exists
											//description of tags is not used for now.
											$db->Update("INSERT INTO `bx_tag` (
											`id`, `tag`, `description`
											) VALUES (
											?, ?, ?
											)", array(NULL, Input::clean($tag,'NOHTML'), NULL));

											$tagid = $db->LastInsertId();
											Rollback::$log['tag_inserts'] .= "'".$tagid."',";
										}
										else
										{
											//else get id of tag query from above
											$tagid = $exists;
										}

										$db->Update("INSERT INTO `bx_tag_content` (
										`content_id`,`tag_id`,`dateline`
										) VALUES (
										?,?,?)", array(self::$update_id, $tagid, TIME_NOW));

									}
								} //foreach

								Rollback::$log['tag_inserts'] = rtrim(Rollback::$log['tag_inserts'], ",");
								Rollback::$log['tag_content_insert'] = true;
							}

						/* rating */ //ok

							//member #1 = usually admin user account

							if(self::$output['rating'] != null && self::$output['rating'] != '-')
							{
								if(self::$o['admin_member_account'])
								{
									Rollback::$log['rating_insert'] = $db->Update("INSERT INTO `bx_rating` (
									`id`, `content_id`, `rating`, `member_id`, `IP`, `dateline`
									) VALUES (
									?, ?, ?, ?, ?, ?
									)", array(
										NULL,
										self::$update_id,
										self::$output['rating'],
										self::$o['admin_member_account'],
										Session::FetchIP(),
										TIME_NOW
									));

									$rating_id = $db->LastInsertId();
								}
							}


						//success!
						self::$success = true;

						Logger::AdminActivity('added update', self::$update_id);

					} //add new update

				} //try
				catch (Exception $e) {
					$tpl->errormessage($translate->_("Error").': '.$e->getMessage().PHP_EOL);
					self::$success = false;

					//ROLLBACK!
					if(Rollback::$log['location_insert'])
						$db->Update("DELETE FROM `bx_location` WHERE `id`=?", array($addlocation_id));
					if(Rollback::$log['update_insert'])
						$db->Update("DELETE FROM `bx_content` WHERE `id`=?", array(self::$update_id));
					if(Rollback::$log['rating_insert'])
						$db->Update("DELETE FROM `bx_rating` WHERE `id`=?", array($rating_id));
					if(Rollback::$log['_2257_ids'])
					{
						Rollback::Undo('2257_delete', Rollback::$log['_2257_ids']);
						Rollback::Undo('2257_insert', $old2257, self::$id);
					}

					if(Rollback::$log['model_has_set_update'])
						Rollback::Undo('model_has_set_update', $old_has_set, self::$id);
					if(Rollback::$log['model_has_set_insert'])
						$db->Update("DELETE FROM `bx_model_has_set` WHERE `content_id`=?", array(self::$update_id));

					if(Rollback::$log['tag_inserts'])
						$db->Update("DELETE FROM `bx_tag` WHERE `id` IN (".Rollback::$log['tag_inserts'].")");
					if(Rollback::$log['tag_content_insert'])
						$db->Update("DELETE FROM `bx_tag_content` WHERE `content_id`=?", array(self::$update_id));
					if(Rollback::$log['thumbfolder_creation'])
						@Filehandler::RemoveDir($thumbfolder);
					if(Rollback::$log['thumbnail_insert_ids'])
						$db->Update("DELETE FROM `bx_thumbnail` WHERE `id` IN (".Rollback::$log['thumbnail_insert_ids'].")");
					if(Rollback::$log['path_update'])
						Rollback::Undo('path_update', $oldpaths, self::$id);
					if(Rollback::$log['newslugcreation'])
						@Filehandler::RemoveDir($newinternalslugpath);
					if(Rollback::$log['slugrenamed'])
						@Filehandler::MoveDir($newinternalslugpath,$oldinternalslugpath);
					if(Rollback::$log['slugdbupdate'])
						$db->Update("UPDATE `bx_content` SET `slug`=? WHERE `id`=? LIMIT 1", array($oldslug, self::$id));
					//restore tag_content
					if(Rollback::$log['tag_content_update'])
						Rollback::Undo('tag_content_insert', $oldtag_content, self::$id);
					if(Rollback::$log['location_update'])
						Rollback::Undo('location_update', $oldlocation, $location_id);
					if(Rollback::$log['content_update'])
						Rollback::Undo('content_update', $oldupdate, self::$id);
					if(Rollback::$log['ratings'])
						Rollback::Undo('ratings', $old_ratings, self::$id);
					if(Rollback::$log['thumbupdate'])
						Rollback::Undo('thumbnail_delete', $oldthumbs, array(self::$id, self::$output['type']));
					if(Rollback::$log['thumbintemp']) // || $thumbmove)
					{
						@Filehandler::RemoveDir($newinternalslugpath);
						@Filehandler::MkDir($oldinternalslugpath);

						//undo safety copy
						for ($i=0, $s=count(self::$output['thumbs']); $i<$s; $i++)
						{
							@Filehandler::MoveFile(Rollback::$log['thumbintemp'][$i]['temppath'], Rollback::$log['thumbintemp'][$i]['dest']);
						}
					}

					self::$success = false;
				} //ROLLBACK



			} //if (count(self::$errorcss) === 0)



			if(self::$success && !self::$id)
			{
				//redirect to step 2 (only if we were not editing)
				$this->step2();
			}
/*
			elseif(self::$success && self::$id)
			{
				$tpl->redirect(Path::Get('url:admin')."/update?edit=".self::$id."&success");
			}
*/

	} //addnew

/**
* step2
*
* @access public
*/
	public function step2()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		/***database connect***/
		$db = DB::getInstance();

		//recropping an existing image set?
		if(isset($_GET['step2']) && $_GET['step2'] == 'pics')
		{
			//get working data
			self::$output = $db->Row("SELECT `id`,`slug`,`memberpath`,`type`
						FROM `bx_content`
						WHERE `id`=?",
						array(intval($_GET['step2'])));

			if(!self::$output)
				die("invalid update");

			self::$update_id = self::$output['id'];

			$noref = self::$output['slug'];
			self::$internal_slug = Input::clean($noref, 'FILENAME');
			unset($noref);
			self::$output['picturepath'] = Path::Get('path:member/pics') . String::Slash(self::$output['memberpath'], 1, 0);
		}


		if(self::$output['type']=='set')
		/********************/
		/*** PICTURE UPDATE ***/
		/********************/
		{
			//now the fun part:
			//picture cropping and freebie marking

			//clear temp table
			$db->Update("TRUNCATE TABLE `bx_temp`");

			$templatename = "admin_newupdate_step2_pics";
			$tpl = new Template('admin:'.$templatename, 0); //__tpl_cache_time

			$translations = Tools::GetCommonAdminTranslations();
			$tpl->apply($translations);

			//no path to process?
			if (!isset(self::$output['picturepath']))
				die($translate->_("No folder specified"));
			if (!is_dir(self::$output['picturepath']))
				die($translate->_("Invalid Directory"));

			//store temp data
			$db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES
				('folder', ?),
				('internal_slug', ?),
				('updateid', ?)",
				array(
					self::$output['picturepath'],
					self::$internal_slug,
					self::$update_id
				));

			//leave this in!
			$tpl->assign("_thumbsizes", self::$o['picturegrab_thumbnailsize']);

		}
		elseif(self::$output['type']=='video')
		/********************/
		/*** VIDEO UPDATE ***/
		/********************/
		{
			//video framegrabbing

			//clear temp table
			$db->Update("TRUNCATE TABLE `bx_temp`");

			$templatename = "admin_newupdate_step2_video";
			$tpl = new Template('admin:'.$templatename, 0); //__tpl_cache_time

			$translations = Tools::GetCommonAdminTranslations();
			$tpl->apply($translations);

			//no path to process?
			//paths were defined above during the update process
			if (!isset(self::$output['videodirectory']))
				die($translate->_("No folder specified"));
			if (!is_dir(self::$output['internal_videodirectory']))
				die($translate->_("Invalid Directory"));


			//store temp data
			$db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES
				('folder', ?),
				('internal_slug', ?),
				('updateid', ?)",
				array(
					self::$output['basepath'].self::$output['videodirectory'],
					self::$output['slug'],
					self::$update_id,
				));

		}
		else
		{
			die('invalid update type');
		}

		/***database disconnect***/
		unset($db);


		/* page setup */
		if(isset($_GET['step2']) && $_GET['step2'] == 'pics') //recropping

			$tpl->title = $translate->_("Re-Cropping Images");

		else //initial

			$tpl->title = $translate->_("New Update - Step 2");

		$tpl->barcolor = "999999";
		/* page setup */

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"finalizeupdate" => "Finalize Update",
			"abort" => "Abort",
			"alsodeletefile" => "Also Delete File",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

		exit(); //do not show index() page

	} //Step2

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		self::$o = Config::GetDBOptions(array(
				'frontend_theme',
				'picture_thumbnailsize',
				'video_thumbnailsize',
					//cache priming (used later)
					'floating_thumbs',
					'html5',
					'admin_member_account',
					'picturegrab_thumbnailsize',
					'videograb_thumbnailsize',
					'default_base',
			));

		//get the mandatory form fields
		self::$mandatory = Config::Get('mandatory_update');

		/***database connect***/
		$db = DB::getInstance();

		//id for edit
		if(isset($_GET['edit'])) self::$id = intval($_GET['edit']);

		/***get new update id***/
		if(isset(self::$id))
			self::$update_id = $db->Column("SELECT MAX(`id`) FROM `bx_content`") + 1;
		else
			self::$update_id = self::$id;


		self::$token = Session::GetToken();

		try {
				if(!empty($_POST))
				{
					if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
						throw new Exception($translate->_('Security Token mismatch'));

					if( Authentification::CheckPermission('administrator', 'editor') )
					{
						/***add new update***/
						if (isset($_POST['_action']) && $_POST['_action'] == 'addnew')
							$this->addnew();
					}
				}

				/***edit existing update***/
				if (isset($_GET['edit']))
					$this->edit();

				/***recrop existing picture set***/
				elseif (isset($_GET['step2']))
					$this->step2();
		}
		catch (Exception $e) {
				echo $translate->_("Error").': '.$e->getMessage()."<br>".PHP_EOL;
				die();
		}

		$this->index();
	}

} //class
