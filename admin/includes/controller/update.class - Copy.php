<?php

/* **************************************************************
 *  File: update.class.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

class update extends BaseController
{

	public static $success = false;
	public static $errors = array();
	public static $errorcss = array();

	public static $output = array();
	public static $picturethumbsizes = array();
	public static $videothumbsizes = array();

	public static $id = 0;
	public static $internal_slug = '';

	public static $internal_type; //ex.: 'pics'

	public static $update_id;

	//required variables
	public static $mandatory = array();

	private static $token = false;

	private static $cropped = false;

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
				$models = $db->FetchAll("SELECT `m`.`id`,`m`.`title` AS `modelname`,`m`.`slug` FROM `bx_content` AS `m` WHERE `m`.`type`='model' ORDER BY `m`.`title`");
				if (count($models) === 0) {
					if (mysql_error()){
					    die($translate->_('Database Query Failed'). (Config::Get('debug')===true)?mysql_error():"");
					}
					else{ //No models found in database.
					    $tpl->errormessage($translate->_('Add at least one model').' <a href="'.__admindir.'/model">'.$translate->_('Add a model').'</a>.');
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

		//output
		if (isset(self::$output['type']))
			if (self::$output['type'] == 'videoset')
				self::$output['videothumbs'] = self::$output['thumbs'];
			else
				self::$output['picthumbs'] = self::$output['thumbs'];
		$tpl->assign("output", self::$output);

		//error/success
		if (self::$success || isset($_GET['success']))
			$tpl->successmessage($translate->_("Done"));
		if (isset($_GET['abort']))
			$tpl->errormessage($translate->_("Aborted"));
		if (self::$errors) 	$tpl->assign("errors", self::$errors);
		if (self::$errorcss)$tpl->assign("errorcss", self::$errorcss);

		if (isset($_GET['error']))
		{
			$error = urldecode(Input::clean_single('g', 'error', 'NOHTML'));
			$tpl->errormessage($translate->_($error));
		}

		//memberpath prefill
		$tpl->assign("_memberpath", __memberpath . __memberpicdir . '/');
		//videodirectory prefill
		$tpl->assign("_videodirectory", __SITE_PATH . __memberdir . __membervideodir . '/');
		//videourl prefill
		$tpl->assign("_videourl", __siteurl . __memberdir . __membervideodir . '/');
		//trailer url prefill
		$tpl->assign("_freevideourl", __siteurl . __freeurl . '/');

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

		//min, max rating and step size
		$tpl->assign("rating", Config::Get('rating'));

		//supported file types
		$tpl->assign("allowed_mime_types", implode(", ", Config::Get('image_extensions')));

		//thumbsizes
		$tpl->assign("picturethumbsizes", self::$picturethumbsizes);
		//thumbsizes
		$tpl->assign("videothumbsizes", self::$videothumbsizes);

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
			"grabbing_frames" => "Grabbing frames",
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
			self::$output = $db->Row("SELECT `u`.`type` AS `type`,`u`.`title`,`u`.`memberpath` AS `picturefolder`, `u`.`freepath`, `u`.`slug`, `u`.`description`, DATE(FROM_UNIXTIME(`u`.`dateline`)) AS `date`,
			`l`.`location`, `l`.`zipcode`, `l`.`state`, `l`.`LatLng`,
			`c`.`country`,
			AVG(`r`.`rating`) AS `rating`
			FROM `bx_content` AS `u`
			LEFT JOIN `bx_location` AS `l` ON (`l`.`id`=`u`.`location_id`)
			LEFT JOIN `bx_country` AS `c` ON (`c`.`iso`=`l`.`country_iso`)
			LEFT JOIN `bx_rating` AS `r` ON (`r`.`content_id`=`u`.`id`)
			WHERE `u`.`id`=?
			GROUP BY `u`.`id`", array(self::$id));

			if(!self::$output) throw new Exception("ID does not exist");

			//internal type (for paths)
			self::$internal_type = (self::$output['type']=='pictureset') ? 'pics':'video';

			/* selected models (prefill is in index) */
			self::$output['models'] = array();
			$_temp = $db->FetchAll("SELECT `A`.`content_id` AS `id`
			FROM `bx_2257` AS `A`, `bx_2257` AS `B`
			WHERE `A`.`id`=`B`.`parent` AND `B`.`content_id`=?",
			array(self::$id));
			//reduce dimension
			foreach($_temp as $t)
			self::$output['models'][] = $t['id'];
			unset($_temp);

			/* selected models 2257 info */
			$ids = implode(",", self::$output['models']);
			$_temp = $db->FetchAll("SELECT `f`.`modelrelease_path` AS `modelreleaseurl`,
			`f`.`misc_url` AS `miscurl`, `f`.`parent` AS `id`, `f`.`production_date`, `f`.`notes`
			FROM `bx_2257` AS `f`
			WHERE `f`.`content_id`=?",array(self::$id));
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
			$_temp = $db->FetchAll("SELECT `t`.`tag`
			FROM `bx_tag` AS `t`, `bx_tag_content` AS `tc`
			WHERE `tc`.`content_id`=?
			AND `t`.`id`=`tc`.`tag_id`", array(self::$id));

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
			if(self::$output['type']=='pictureset')
			{
				self::$output['videourl'] = ' ';
				self::$output['freevideourl'] = ' ';
			}
			else
			{
				self::$output['picturefolder'] = ' ';
			}

			/* thumbnails */
			$thumbnails = $db->FetchAll("SELECT `th`.`path` AS `path`, `th`.`internal_id`
			FROM `bx_thumbnail` AS `th`, `bx_content` AS `c`
			WHERE `th`.`content_id`=`c`.`id`
			AND `c`.`id`=?
			ORDER BY `th`.`internal_id` ASC", array(self::$id));


			//check if thumbs exist
			for ($i=0, $s=count($thumbnails); $i<$s; $i++)
			{
				//remove from thumbnail array and check later
				if(!file_exists( __SITE_PATH . $thumbnails[$i]['path'] )) unset($thumbnails[$i]);
			}

			//merge the thumbnails into output array
			self::$output = array_merge(self::$output, array('thumbs' => $thumbnails));

			//tell the template what to do (for showing the existing thumbnails)
			self::$output = array_merge(self::$output, array('action' => 'edit'));

			/*** check for errors ***/
				//remove mandatory items for other type
				if(self::$output['type'] == 'pictureset')
				{
					unset(self::$mandatory['videodirectory']);
					unset(self::$mandatory['videourl']);
					unset(self::$mandatory['freevideourl']);

					//don't trip the mandatory alarm
					self::$output['videodirectory'] = ' ';
					self::$output['videourl'] = ' ';
					self::$output['freevideourl'] = ' ';
				}
				elseif(self::$output['type'] == 'videoset')
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
				for($i=0, $s = count( (self::$output['type']=='pictureset' ? self::$picturethumbsizes : self::$videothumbsizes) ); $i<$s; $i++)
				{
					if(!isset(self::$output['thumbs'][$i]))
					{
						self::$errors[] = "Missing thumbnail ".$i;
						self::$errorcss['thumbs'][$i] = ' error';
					}
				}

		//that is it.
		//updating the content is handled in addnew

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
				'date'				=>	'DATE',
				'description'		=>	'STR',
//				'duration'			=>	'NOHTML',
				'freevideourl'		=>	'NOHTML',
				'picturefolder'		=>	'NOHTML',
				'rating'			=>	'UNUM',
				'slug'				=>	'NOHTML',
				'tags'				=>	'NOHTML',
				'title'				=>	'NOHTML',
				'type'				=>	'NOHTML',
				'videourl'			=>	'NOHTML',
				'videodirectory'	=>	'NOHTML',

			//2257 data
				'country'			=>	'NOHTML',
				'LatLng'			=>	'LATLNG',
				'location'			=>	'NOHTML',
				'models'			=>	'ARRAY_NOHTML',
				'notes'				=>	'NOHTML',
				'productiondate'	=>	'DATE',
				'state'				=>	'NOHTML',
				'zipcode'			=>	'UINT',
				'miscurl'			=>	'ARRAY_NOHTML',
				'modelreleaseurl'	=>	'ARRAY_NOHTML',
			));
			if (self::$output['rating'] == null) self::$output['rating'] = 0;

			self::$output['videodirectory'] = String::RemoveTrailingPrecedingSlash(self::$output['videodirectory']);

			self::$output['internal_videodirectory'] = String::AddTrailingSlash(__memberpath.__membervideodir).String::RemoveTrailingPrecedingSlash(self::$output['videodirectory']);

			//internal type (for paths)
			self::$internal_type = (self::$output['type']=='pictureset') ? 'pics':'video';
			if (!self::$internal_type) die("Invalid update type");

			//internally, we only deal with a sanitized version of the slug directory
			$noref = self::$output['slug'];
			self::$internal_slug = Input::clean($noref, 'FILENAME');

			//nicEdit adds an annoying "<br>" at end of text that needs to be removed
			self::$output['description'] = String::rTrimBr(self::$output['description']);

			//thumbs
			self::$output = array_merge(self::$output, Input::clean_array('p',array(
				'picthumbs' => 		'ARRAY_NOHTML',
				'videothumbs' => 	'ARRAY_NOHTML'
			)));

			self::$output['picturefolder'] = String::AddTrailingPrecedingSlash(self::$output['picturefolder']);

			self::$output['picturepath'] = __memberpath . __memberpicdir . self::$output['picturefolder'];


			/*** check for errors ***/
				//mandatory variable check
				foreach(array_flip(self::$mandatory) as $key => $value)
				{
					if(!self::$output[$key])
					{
						//error name
//						self::$errors[] = $value;
						//error css
						self::$errorcss[$key] = ' error';
					}
				}

				//thumbnail check
				//all thumbs are mandatory
				if (self::$output['type'] == 'pictureset')
					for($i=0, $s = count(self::$picturethumbsizes); $i<$s; $i++)
					{
						if(!isset(self::$output['picthumbs'][$i]))
						{
							self::$errors[] = "Missing thumbnail ".($i+1);
							self::$errorcss['picthumbs'][$i] = ' error';
						}
					}
				if (self::$output['type'] == 'videoset')
					for($i=0, $s = count(self::$videothumbsizes); $i<$s; $i++)
					{
						if(!isset(self::$output['videothumbs'][$i]))
						{
							self::$errors[] = "Missing thumbnail ".($i+1);
							self::$errorcss['videothumbs'][$i] = ' error';
						}
					}

				//check paths
				if (self::$output['type'] == 'pictureset')
				{
					if(String::AddTrailingSlash(self::$output['picturepath']) == __memberpath . __memberpicdir . '/')
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

					self::$output['picturepath'] = String::AddTrailingSlash(self::$output['picturepath']);

					//rename thumbs
					self::$output['thumbs'] = self::$output['picthumbs'];

					//unset old
					if(isset(self::$output['videothumbs'])) unset(self::$output['videothumbs']);
					if(isset(self::$output['picthumbs'])) unset(self::$output['picthumbs']);
				}
				if (self::$output['type'] == 'videoset')
				{
					if(String::AddTrailingSlash(self::$output['videourl']) == __siteurl . __memberdir . __membervideodir . '/')
					{
						self::$output['videourl'] = '';
						self::$errorcss['videourl'] = ' error';
					}
/*
					//check if trailer url is mandatory
					if(String::AddTrailingSlash(self::$output['freevideourl']) == __siteurl . __freeurl . '/')
					{
						self::$output['freevideourl'] = '';
						self::$errorcss['freevideourl'] = ' error';
					}
*/
					if(isset(self::$errorcss['picturefolder'])) unset(self::$errorcss['picturefolder']);
					if(isset(self::$output['picturefolder'])) unset(self::$output['picturefolder']);

					//rename thumbs
					self::$output['thumbs'] = self::$output['videothumbs'];

					//unset old
					if(isset(self::$output['videothumbs'])) unset(self::$output['videothumbs']);
					if(isset(self::$output['picthumbs'])) unset(self::$output['picthumbs']);
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

				if (!self::$output['type'])	throw new Exception('no update type specified');

				define("DATELINE", time());


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
						$check = $db->Row("SELECT `id`,`slug` FROM `bx_content` WHERE `id`=? LIMIT 1", array(self::$id));

						if (!$check) throw new Exception('update does not exist');


						/* update type cannot be changed */


						/* picturefolder and videourl/freevideourl change */ //ok

						$oldpaths = $db->Row("SELECT `memberpath`,`freepath` FROM `bx_content` WHERE `id`=?", array(self::$id));

						if($oldpaths)
						{
							if(self::$output['type'] == 'pictureset')
							{
								Rollback::$log['path_update'] = $db->Update("UPDATE `bx_content` SET `memberpath`=?,`freepath`=? WHERE `id`=?",array(self::$output['picturepath'], __freeurl . __memberpicdir.'/'.self::$internal_slug.'/', self::$id));
							}
							else
							{
								Rollback::$log['path_update'] = $db->Update("UPDATE `bx_content` SET `memberpath`=?,`freepath`=? WHERE `id`=?",array(self::$output['videourl'], self::$output['freevideourl'], self::$id));
							}

							//for now: manual moves in members area necessary



						}

						/* slug and thumbs */

							//check if the slug was updated in the edit
							$oldslug = $check['slug'];
							$newslug = self::$output['slug'];

							$oldinternalslug = Input::clean($oldslug, 'FILENAME');
							$newinternalslug = Input::clean($newslug, 'FILENAME');

							$oldinternalslugpath = __thumbpath. String::AddTrailingPrecedingSlash(self::$internal_type).$oldinternalslug;
							$newinternalslugpath = __thumbpath. String::AddTrailingPrecedingSlash(self::$internal_type).$newinternalslug;

							if($oldinternalslugpath == $newinternalslugpath)
							{
								//slug was not updated, we will simply use the existing value

								//check if the old slug folder exists

								if (!is_dir( $oldinternalslugpath ))
								{
									//try to create missing slug directory
									if(!Filehandler::MkDir($newinternalslugpath, 0777, true))
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
									if(!@Filehandler::MkDir($newinternalslugpath, 0777, true))
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
								Rollback::$log['slugdbupdate'] = $db->Update("UPDATE `bx_content` SET `slug`=? WHERE `id`=? LIMIT 1",
								array(self::$output['slug'], self::$id));
							}

							//we now have a valid newinternalslugpath


							//rename thumbnails as they come!

							$oldthumbs = $db->FetchAll("SELECT * FROM `bx_thumbnail` WHERE `content_id`=? AND `type`=?", array(self::$id, self::$output['type']));

							for ($i=0, $s=count(self::$output['thumbs']); $i<$s; $i++)
							{
								$ext = String::GetFileExtension(self::$output['thumbs'][$i]);

								//safety copy
								@Filehandler::SmartCopy($oldinternalslugpath.'/'.$i.'.'.$ext, _ADMINPATH.'/temp/'.$i.'.'.$ext);
								Rollback::$log['thumbintemp'][$i]['temppath'] = _ADMINPATH.'/temp/'.$i.'.'.$ext;
								Rollback::$log['thumbintemp'][$i]['dest'] = $oldinternalslugpath.'/'.$i.'.'.$ext;

								if(!preg_match("~^".String::AddTrailingPrecedingSlash(__thumburl)."~", self::$output['thumbs'][$i]))
								{
									//the thumbnail was updated

									//delete the old thumbnail
									@Filehandler::DeleteFile($newinternalslugpath . '/'.$i.'.'.$ext);

									//thumb was changed and needs to be moved
									if(!@Filehandler::MoveFile( _ADMINPATH.'/temp/'.self::$output['thumbs'][$i],
									String::RemoveTrailingSlash($newinternalslugpath).'/'.$i.'.'.$ext))
									{
										//error marking
										self::$errorcss['thumbs'][$i] = ' error';
										throw new Exception ("could not add new thumbnail");
									}
									//else
										//$thumbmove[$i]['current'] = String::RemoveTrailingSlash($newinternalslugpath).'/'.$i.'.'.$ext;
										//$thumbmove[$i]['dest'] = _ADMINPATH.'/temp/'.self::$output['thumbs'][$i];

									//update the database

										//delete old thumbnail
										$db->Update("DELETE FROM `bx_thumbnail` WHERE `internal_id`=? AND `type`=? AND `content_id`=? LIMIT 1",
										array($i, self::$output['type'], self::$id));

										$th = __thumburl . String::AddTrailingPrecedingSlash(self::$internal_type) .$newinternalslug.'/'.$i.'.'.$ext;
										//add new thumbnail
										$db->Update("INSERT INTO `bx_thumbnail` (
										`id`,`content_id`,`path`,`internal_id`,`type`,`dateline`
										) VALUES (
										?,?,?,?,?,?
										)",array(
										NULL, self::$id, $th, $i, self::$output['type'], DATELINE
										));
								}
								else
								{
									//thumbnail was not changed

									//update the path of the thumbnail if slug was changed
									if($oldinternalslugpath != $newinternalslugpath)
									{
										$db->Update("UPDATE `bx_thumbnail` SET `path`=? WHERE `internal_id`=? AND `type`=? AND `content_id`=?",
										array(__thumburl . String::AddTrailingPrecedingSlash(self::$internal_type) .$newinternalslug.'/'.$i.'.'.$ext, $i, self::$output['type'], self::$id));
									}
								}
							}

							Rollback::$log['thumbupdate'] = true;

						/* country */ //ok

							//get the country iso code
							$countryiso = $db->QuerySingleColumn("SELECT `iso` FROM `bx_country` WHERE `country`=?", array(self::$output['country']));

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
							$location_id = $db->QuerySingleColumn("SELECT `l`.`id`
							FROM `bx_location` AS `l`, `bx_content` AS `c`
							WHERE `l`.`id`=`c`.`location_id`
							AND `c`.`id`=?
							LIMIT 1",
							array(self::$id));

							if(!$location_id)
							{
								//write new location

								//location description is not used for now
								Rollback::$log['location_insert'] = $db->Update("INSERT INTO `bx_location` (
								`id`,`location`,`state`,`zipcode`,`description`,`LatLng`,`country_iso`
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
								$oldlocation = $db->Row("SELECT * FROM `bx_location` WHERE `id`=?", array($location_id));

								//location description is not used for now
								Rollback::$log['location_update'] = $db->Update("UPDATE `bx_location` SET
								`location`=?,
								`state`=?,
								`zipcode`=?,
								`description`=?,
								`LatLng`=?,
								`country_iso`=?
								WHERE `id`=?", array(
								self::$output['location'],
								self::$output['state'],
								self::$output['zipcode'],
								NULL,
								self::$output['LatLng'],
								$countryiso,
								$location_id));
							}

						/* update */ //ok

							//safety copy for rollback
							$oldupdate = $db->Row("SELECT * FROM `bx_content` WHERE `id`=?", array(self::$id));

							if(self::$output['type'] == 'videoset')
							{
								Rollback::$log['content_update'] = $db->Update("UPDATE `bx_content` SET
								`slug`=?,
								`title`=?,
								`memberpath`=?,
								`freepath`=?,
								`description`=?,
								`dateline` = UNIX_TIMESTAMP(?),
								`location_id`=?
								WHERE `id`=?", array(
								self::$output['slug'],
								self::$output['title'],
								self::$output['videourl'],
								self::$output['freevideourl'],
								self::$output['description'],
								self::$output['date'],
								$location_id,
								self::$id));
							}
							elseif(self::$output['type'] == 'pictureset')
							{
								$path = self::$output['picturefolder'];
								$freepath = __freeurl . __memberpicdir .'/' . self::$internal_slug . '/';

								Rollback::$log['content_update'] = $db->Update("UPDATE `bx_content` SET
								`slug`=?,
								`title`=?,
								`memberpath`=?,
								`freepath`=?,
								`description`=?,
								`dateline` = UNIX_TIMESTAMP(?),
								`location_id`=?
								WHERE `id`=?", array(
								self::$output['slug'],
								self::$output['title'],
								$path,
								$freepath,
								self::$output['description'],
								self::$output['date'],
								$location_id,
								self::$id));
							}

						/* 2257 entry */ //ok

							//backup old
							$old2257 = $db->FetchAll("SELECT * FROM `bx_2257` WHERE `content_id`=?", array(self::$id));

							//delete old
							$db->Update("DELETE FROM `bx_2257` WHERE `content_id`=?",array(self::$id));

							//insert new
								//get parent 2257 entries (model ids)
								$models2257 = array();
								for($i=0, $s=count(self::$output['models']); $i<$s; $i++)
								{
									$models2257[self::$output['models'][$i]] = $db->QuerySingleColumn("SELECT `id` FROM `bx_2257` WHERE `content_id`=?",array(self::$output['models'][$i]));
								}

								//one 2257 update entry for every model
								foreach(self::$output['models'] AS $m)
								{
									$db->Update("INSERT INTO `bx_2257` (
									`id`, `content_id`, `modelrelease_path`, `misc_url`, `location_id`, `production_date`, `notes`, `parent`, `dateline`
									) VALUES (
									?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
									NULL, self::$id, self::$output['modelreleaseurl'][$m['id']], self::$output['miscurl'][$m['id']], $location_id, self::$output['productiondate'], self::$output['notes'], $models2257[$m['id']], DATELINE));

									Rollback::$log['_2257_ids'][] = $db->LastInsertId();
								}

						/* model_has_set relation */

							//backup old
							$old_has_set = $db->FetchAll("SELECT * FROM `bx_model_has_set` WHERE `content_id`=?", array(self::$id));

							//delete old
							$db->Update("DELETE FROM `bx_model_has_set` WHERE `content_id`=?", array(self::$id));

							$sql = "INSERT INTO `bx_model_has_set` (`model_id`,`content_id`) VALUES ";
							foreach(self::$output['models'] AS $m)
							{
								$sql .= $db->Prepare("(?,?),", array($m['id'], self::$update_id));
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
								WHERE `content_id`=?", array(self::$id));
;
								//remove old tags
								Rollback::$log['tag_content_update'] = $db->Update("DELETE FROM `bx_tag_content`
								WHERE `content_id`=?", array(self::$id));

								foreach($tags as $tag)
								{
									if($tag != '')
									{
										//check if tag exists
										$exists = $db->QuerySingleColumn("SELECT `id` FROM `bx_tag` WHERE `tag`=? LIMIT 1", array($tag));

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
										`content_id`,`tag_id`,`dateline`
										) VALUES (
										?,?,?
										)", array(self::$id, $tagid, DATELINE));

									}
								} //foreach

								Rollback::$log['tag_inserts'] = rtrim(Rollback::$log['tag_inserts'], ",");
							}

						/* rating */

							//resetting the rating will remove all user ratings!

/*
							$db->Update("DELETE FROM `bx_rating` WHERE `content_id`=?",
							array($model_id));

							//member #1 = usually admin user account
							$db->Update("INSERT INTO `bx_rating` (
							`id`,`content_id`,`rating`,`member_id`,`dateline`
							) VALUES (
							?, ?, ?, ?, ?
							)", array(NULL, $model_id, self::$output['rating'], 1, DATELINE));
*/


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
							$countryiso = $db->QuerySingleColumn("SELECT `iso` FROM `bx_country` WHERE `country`=?", array(self::$output['country']));

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
							$addlocation_id = $db->QuerySingleColumn("SELECT `id`
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

							if(self::$output['type'] == 'pictureset')
							{
								$path = self::$output['picturefolder'];
								$freepath = __freeurl . __memberpicdir .'/' . self::$internal_slug . '/';

								Rollback::$log['update_insert'] = $db->Update("INSERT INTO `bx_content` (
								`id`, `type`, `slug`, `title`, `memberpath`, `freepath`, `description`, `count`, `dateline`, `location_id`
								) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(?), ?
								)", array(
								NULL, 'pictureset', self::$output['slug'], self::$output['title'], $path, $freepath, self::$output['description'], '0', self::$output['date'], $addlocation_id));
							}
							else //video
							{
								Rollback::$log['update_insert'] = $db->Update("INSERT INTO `bx_content` (
								`id`, `type`, `slug`, `title`, `memberpath`, `freepath`, `description`, `count`, `dateline`, `location_id`
								) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(?), ?
								)", array(
								NULL, 'videoset', self::$output['slug'], self::$output['title'], self::$output['videourl'], self::$output['freevideourl'], self::$output['description'], '0', self::$output['date'], $addlocation_id));
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
								$models2257[self::$output['models'][$i]] = $db->QuerySingleColumn("SELECT `id` FROM `bx_2257` WHERE `content_id`=?",array(self::$output['models'][$i]));
							}

							//one 2257 update entry for every model
							foreach(self::$output['models'] AS $m)
							{
								$db->Update("INSERT INTO `bx_2257` (
								`id`, `content_id`, `modelrelease_path`, `misc_url`, `location_id`, `production_date`, `notes`, `parent`, `dateline`
								) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
								NULL, self::$update_id, self::$output['modelreleaseurl'][$m['id']], self::$output['miscurl'][$m['id']], $addlocation_id, self::$output['productiondate'], self::$output['notes'], $models2257[$m['id']], DATELINE));

								Rollback::$log['_2257_ids'][] = $db->LastInsertId();
							}

						/* model_has_set relation */

							foreach(self::$output['models'] AS $m)
							{
								$db->Update("INSERT INTO `bx_model_has_set` (
								`model_id`,`content_id`) VALUES (?,?)", array(
								$m['id'], self::$update_id));
							}

							Rollback::$log['model_has_set_insert'] = true;

						/* thumbnails moving */ //ok

							if(self::$output['type'] == 'pictureset')
								$thumbfolder = __thumbpath . String::AddTrailingSlash(__memberpicdir) . self::$internal_slug . '/';
							else
								$thumbfolder = __thumbpath . String::AddTrailingSlash(__membervideodir) . self::$internal_slug . '/';

							//create the subfolder in the thumb directory
							if(!@Filehandler::MkDir($thumbfolder, 0777, true))
							{
								//could not create the thumbnail directory
								throw new Exception('could not create slug directory.<br />'.
								"Check ".'/thumbs/'. String::RemoveTrailingPrecedingSlash(__memberpicdir).'/'.self::$internal_slug.'/');
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
								if(self::$output['type'] == 'pictureset')
								{
									$thumburl = __thumburl . String::AddTrailingSlash(__memberpicdir) . self::$internal_slug . '/' . $thfilename;
								}
								else
								{
									$thumburl = __thumburl . String::AddTrailingSlash(__membervideodir) . self::$internal_slug . '/' . $thfilename;
								}

								//move uploaded thumbnail
								if(!@Filehandler::SmartCopy(_ADMINPATH.'/temp/'.self::$output['thumbs'][$i], $thumbpath))
								{
									@Filehandler::DeleteFile($thumbpath);
									throw new Exception('thumbnail could not be moved');
								}

var_dump($_POST);
die();


								$db->Update("INSERT INTO `bx_thumbnail` (
									`id`, `content_id`, `path`, `internal_id`,`type`, `dateline`
								) VALUES (
									?, ?, ?, ?, ?, ?
								)", array(NULL, self::$update_id, $thumburl, $i, self::$output['type'], DATELINE));

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
										$exists = $db->QuerySingleColumn("SELECT `id` FROM `bx_tag` WHERE `tag`=? LIMIT 1", array($tag));

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
										?,?,?)", array(self::$update_id, $tagid, DATELINE));

									}
								} //foreach

								Rollback::$log['tag_inserts'] = rtrim(Rollback::$log['tag_inserts'], ",");
								Rollback::$log['tag_content_insert'] = true;
							}

						/* rating */ //ok

							//member #1 = usually admin user account

							Rollback::$log['rating_insert'] = $db->Update("INSERT INTO `bx_rating` (
							`id`, `content_id`, `rating`, `member_id`, `IP`, `dateline`
							) VALUES (
							?, ?, ?, ?, ?, ?
							)", array(NULL, self::$update_id, self::$output['rating'], '1', Session::FetchIP(), DATELINE));

							$rating_id = $db->LastInsertId();


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
				//update successfully edited
				if(!headers_sent())
				{
					header('Location: '.__ADMIN_URL."/update?edit=".self::$id."&success");
					die();
				}
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

		if(isset($_GET['step2']))
		{
			//get working data
			self::$output = $db->Row("SELECT `id`,`slug`,`memberpath`,`type` FROM `bx_content` WHERE `id`=?", array(intval($_GET['step2'])));

			if(!self::$output)
				die("invalid update");

			self::$update_id = self::$output['id'];
			$noref = self::$output['slug'];
			self::$internal_slug = Input::clean($noref, 'FILENAME');
			self::$output['picturepath'] = __memberpath . __memberpicdir . String::AddPrecedingSlash(self::$output['memberpath']);
		}

		if(self::$output['type']=='pictureset')
		/********************/
		/*** PICTURE UPDATE ***/
		/********************/
		{
			//now the fun part:
			//picture cropping and visible marking

			$templatename = "admin_newupdate_step2_pics";
			$tpl = new Template('admin:'.$templatename, 0); //__tpl_cache_time
//			//set tpl
//			Zend_Registry::set('tpl', $tpl);

			$translations = Tools::GetCommonAdminTranslations();
			$tpl->apply($translations);

			//no path to process?
			if (!isset(self::$output['picturepath']))
				die($translate->_("No folder specified"));
			if (!is_dir(self::$output['picturepath']))
				die($translate->_("Invalid Directory"));

			//clear temp table
			$db->Update("TRUNCATE TABLE `bx_temp`");

			//store temp data
			$db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES ('folder',?),('internal_slug',?),('updateid',?)", array(self::$output['picturepath'], self::$internal_slug, self::$update_id));

			/***database disconnect***/
			unset($db);

//			$tpl->assign("_folder", urlencode(self::$output['picturepath']));

//			$aspectratio = $_config['thumbnailsizes']['width'] / $_config['thumbnailsizes']['height'];
//			$tpl->assign("aspectratio", $aspectratio);

			//leave this in!
			$tpl->assign("_thumbsizes", Config::GetSingleDBOption('picturegrab_thumbnailsize'));

		}
		elseif(self::$output['type']=='videoset')
		/********************/
		/*** VIDEO UPDATE ***/
		/********************/
		{
			$templatename = "admin_newupdate_step2_video";
			$tpl = new Template('admin:'.$templatename, 0); //__tpl_cache_time

			$translations = Tools::GetCommonAdminTranslations();
			$tpl->apply($translations);

			//no path to process?
			if (!isset(self::$output['videodirectory']))
				die($translate->_("No folder specified"));
			if (!is_dir(self::$output['internal_videodirectory']))
				die($translate->_("Invalid Directory"));

			$videoArray = array();
			//get video list
			$video_mime_types = Config::Get('video_extensions');


			//clear temp table
			$db->Update("TRUNCATE TABLE `bx_temp`");

//			self::$output['videodirectory'] = String::AddTrailingSlash(self::$output['videodirectory']);

			$dir = opendir(self::$output['internal_videodirectory']);
				while( $entry = readdir( $dir ))
				{
					if (is_dir($entry)) continue;

					if( in_array(strtolower(substr($entry, strrpos($entry, '.') + 1)), array_map('strtolower', $video_mime_types)) )
						$videoArray[] = $entry;
				}
			closedir($dir);

			if(empty($videoArray))
					throw new Exception('no videos found');


set_time_limit(18000);


			try
			{

				$videodata = array();
				for($i=0, $s = count($videoArray); $i<$s; $i++)
				{

					$vp = new Video(self::$output['internal_videodirectory'].'/'.$videoArray[$i]);

					//get video info
					$videodata[$i] = $vp->GetInfo();

					if($videodata[$i])
					{
						$videodata[$i]['id']= $i;

						if(self::$cropped == false)
						{
							//outputfolder
							$outputfolder = './temp/framegrab'.time(); //important: no trailing slash!

							//to finish update later, save the thumbnail folder
							$db->Update("INSERT INTO `bx_temp` (`key`,`value`) VALUES
							('outputfolder',?), ('internal_slug',?), ('internal_videodirectory',?), ('updateid',?)", array(
								$outputfolder, self::$internal_slug, self::$output['internal_videodirectory'], self::$update_id
							));

							//set output folder
							$vp->SetOutputDir($outputfolder);

							$start = time();
								//extract screencaps
								$thumbnails = $vp->GrabFrames();
							$end = time();

							$cropping_time = ($end-$start);
							$tpl->assign('cropping_time', $cropping_time);

							self::$cropped = true;

						} //cropped


						//tempfolder
						$tempfolder = './temp/'.$i;
						//set output folder
						$vp->SetOutputDir($tempfolder);

						//grab one single frame as preview (will not be used for anything else)
						$thumb = $vp->GrabSingleFrame(5); //5 seconds into the video
						$videodata[$i]['preview'] = $thumb['url'];


						$videodata[$i]['filepath'] = self::$output['internal_videodirectory'].'/'.$videoArray[$i];
						$videodata[$i]['filename'] = $videoArray[$i];

					}
					else
					{
						//videodata empty

						continue;

					}

				} //for

			}//try framegrabbing
			catch(FrameGrabException $e)
			{
				$tpl->errormessage( $e->getMessage() );
			}

			//output
			if(!empty($videodata)) $tpl->assign('videos', $videodata);

			//assign thumbnails
			if(!empty($thumbnails)) $tpl->assign('thumbnails', $thumbnails );

			//assign width/height
			$tpl->apply( Config::GetSingleDBOption('videograb_thumbnailsize') );

		}
		else
		{
			die('invalid update type');
		}

		/* page setup */
		if(isset($_GET['step2']))
			$tpl->title = $translate->_("Re-Cropping Images");
		else
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

		die(); //do not show index() page

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

		//get the mandatory form fields
		self::$mandatory = Config::Get('mandatory_update');

		/***database connect***/
		$db = DB::getInstance();

		/***get new update id***/
			self::$update_id = $db->QuerySingleColumn("SELECT MAX(`id`) FROM `bx_content`") + 1;
//			$tpl->assign("updateid", $updateid);

		//get thumbsizes
		self::$picturethumbsizes = Config::GetSingleDBOption('picture_thumbnailsize');
		self::$videothumbsizes = Config::GetSingleDBOption('video_thumbnailsize');

//internal_id debuggen!!!










		//id for edit
		if(isset($_GET['edit'])) self::$id = intval($_GET['edit']);

		self::$token = Session::GetToken();

		try {
				if(!empty($_POST))
				{
					if ($_POST['securitytoken'] == self::$token && !isset($_GET['securitytoken']))
					{
						$role = Authentification::GetRole();
						if('administrator' == $role || 'editor' == $role)
						{
							/***add new update***/
							if (isset($_POST['_action']))
								if ($_POST['_action'] == 'addnew')
									$this->addnew();
						}
					}
					else
						throw new Exception($translate->_('Security token mismatch'));
				}

				/***edit existing update***/
				if (isset($_GET['edit']))
					$this->edit();

				/***recrop existing pictureset***/
				elseif (isset($_GET['step2']))
					$this->step2();
		}
		catch (Exception $e) {
								echo"<br />NormalException<br />";
				echo $translate->_("Error").': '.$e->getMessage()."<br />".PHP_EOL;
				die();
		}

		$this->index();
	}

} //class
