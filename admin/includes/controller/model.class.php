<?php

/* **************************************************************
 *  File: model.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class model extends BaseController
{
	//content
	public static $c = array(
			'id' => 0,
			'internal_slug' => null,
		);
	private static $misc = array();
	public static $location = array();
	public static $_2257 = array();

	//messages
	public static $success = false;
	public static $errors = array();
	public static $errorcss = array();

	//securitytoken
	private static $token = false;

	//an array with the relative paths to the thumbnails
	//used only in the upload_area when a thumbnail is not found
	private static $errorpaths = array();

	//mandatory form fields
	//this is accessed from the templates and
	//cannot be merged into the options array
	public static $mandatory = false;

	//options array
	private static $o = array(
		'frontend_theme' => 'default'
		);

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
		$tpl->title = $translate->_("Add New Model");
		/***page setup***/

		if(empty($_POST)){
		//first page load
		//make sure that temp dir is empty
			@Filehandler::EmptyTempDir();
		}

		/***database connect***/
		$db = DB::getInstance();

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

		//mark selected country
		if(!isset(self::$location['country']))
			self::$location['country'] = Config::Get('default_country');

		//output
		$tpl->assign("misc", self::$misc);
		$tpl->assign("location", self::$location);
		$tpl->assign("content", self::$c);
		$tpl->assign("_2257", self::$_2257);

		//floating thumbs
		$tpl->assign("options", self::$o);

		//error/success
		if (self::$success || isset($_GET['success']))
			$tpl->successmessage($translate->_("Done"));
		if (self::$errors)
		{
			if(is_array(self::$errors) && count(self::$errors) > 1)
				$tpl->assign("errors", self::$errors);
			else
				if(is_array(self::$errors))
					$tpl->errormessage(self::$errors[0]);
				else
					$tpl->errormessage(self::$errors);
		}
		if (self::$errorcss)$tpl->assign("errorcss", self::$errorcss);

		//form remove check
		$_frm = array();
		$form_remove_model = Config::Get('form_remove_model');
		foreach ($form_remove_model as $thekey)
		{
			$_frm["form_".$thekey] = true;
		}
		$tpl->apply($_frm);

		//min, max rating and step size
		$tpl->assign("rating", Config::Get('rating'));

		//supported file types
		$tpl->assign("allowed_mime_types", implode(", ", Config::Get('image_extensions')));

		//thumbsizes
		$tpl->assign("thumbsizes", self::$o['thumbsizes']);

		//if thumbnail was not found, self::$errorpaths contains the relative path to thumbnail
		if(!empty(self::$errorpaths)) $tpl->assign("errorpaths", self::$errorpaths);

		//prefill countries
		if(isset($countries)) $tpl->assign("countries", $countries);

		//array with mandatory items of the form
		if(isset(self::$mandatory))
		{
			self::$mandatory = array_flip(self::$mandatory);
			$tpl->assign("mandatory", self::$mandatory);
		}

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"aliases" => "Aliases",
			"birthdate" => "Birthdate",
			"country" => "Country",
			"description" => "Description",
			"fileuploading" => "File Uploading",
			"gender" => "Gender",
			"idurl" => "Identification Url",
			"image" => "Image",
			"initialrating" => "Initial Rating",
			"lat" => "Lat",
			"lng" => "Lng",
			"location" => "Location",
			"miscurl" => "Misc Url",
			"modelname" => "Model Name",
			"modelreleaseurl" => "Model Release Url",
			"notes" => "Notes",
			"passport_id" => "Passport ID",
			"pleasewait" => "Please Wait",
			"productiondate" => "Production Date",
			"realname" => "Real Name",
			"slug" => "Slug",
			"state" => "State",
			"submit" => "Submit",
			"success" => "Success",
			"supportedfiletypes" => "Supported File Types",
			"tags" => "Tags",
			"thumbnail" => "Thumbnail",
			"thumbnails" => "Thumbnails",
			"thumbnailuploaderror" => "Thumbnail Upload Error",
			"upload" => "Upload",
			"uploadimage" => "Upload Image",
			"urlto2257" => "Url to the 2257",
			"zipcode" => "Zipcode",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

	} //index

/**
* add new
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
		self::$misc = Input::clean_array('p', array(
			'rating'		=>	'UNUM',
			'tags'			=>	'NOHTML',
		));
		if (is_null(self::$misc['rating'])) self::$misc['rating'] = 0;

		//content
		self::$c = Input::clean_array('p', array(
			//id is not set here
			//id is only set if editing model
			//therefore id defines if jumped into 'edit' or 'add new'
			'description'		=>	'STR',
			'slug'			=>	'STR',
			'title'			=>	'NOHTML',
		));

		//location
		self::$location = Input::clean_array('p', array(
			'location'		=>	'NOHTML',
			'state'			=>	'NOHTML',
			'zipcode'		=>	'NOHTML',
			'LatLng' 		=>	'LATLNG',
			'country'		=>	'NOHTML',
		));

		//2257
		self::$_2257 = Input::clean_array('p', array(
			'aliases'		=>	'NOHTML',
			'birthdate'		=>	'DATE',
			'gender'		=>	'NOHTML',
			'idurl'			=>	'NOHTML',
			'miscurl'		=>	'NOHTML',
			'notes' 		=>	'NOHTML',
			'passport_id'		=>	'NOHTML',
			'productiondate'	=>	'NOHTML',
			'realname' 		=>	'NOHTML',
		));

		//aliases
		$aliases = explode(",", self::$_2257['aliases']);
		sort( $aliases );
		self::$_2257['aliases'] = '';
		foreach($aliases as $al)
		{
			$al = trim($al);
			if(!empty($al)) self::$_2257['aliases'] .= $al . ',';
		}
		self::$_2257['aliases'] = rtrim(self::$_2257['aliases'], ",");
		unset($aliases);

		//thumbs
		//example:
		//["thumbs"]=> array(1) { [0]=> string(36) "7375fb07906e49fa65eeb779ff7d5ee4.jpg" }
		self::$misc = array_merge(self::$misc, Input::clean_array('p', array(
			'_thumbs' => 'ARRAY_NOHTML' //uploads arrive as '_thumbs' from ./lib/ajaxupload.php
		)));
		//rename for compatibility and readability
		self::$misc['thumbs'] = self::$misc['_thumbs'];
		unset(self::$misc['_thumbs']);

		//internally, we only deal with a sanitized version of the slug directory!
		//for example: slug: /modelname/sexy/birth-day/party -> internal: modelname-sexy-birth-day-party
		//the internal representation is used for all folders
		//while the actual slug is saved in the database
		$noref = self::$c['slug'];
		self::$c['internal_slug'] = Input::clean($noref, 'FILENAME');
		unset($noref);

		//nicEdit adds an annoying "<br>" at end of text that needs to be removed
		self::$c['description'] = String::rTrimBr(self::$c['description']);


		/*** check for errors ***/
		//mandatory variable check
		foreach(array_flip(self::$mandatory) as $key => $value)
		{
			if(empty(self::$c[$key])
				//check in the other arrays, too
				&& empty(self::$misc[$key])
				&& empty(self::$location[$key])
				&& empty(self::$_2257[$key]))
				{
					//error name
					self::$errors[] = "Missing ".$key;
					//error css
					self::$errorcss[$key] = ' error';
				}
		}

		//thumbnail check
		//all thumbs are mandatory (if they are defined)!
		for($i=0, $s = count(self::$o['thumbsizes']); $i < $s; $i++)
		{
			if(empty(self::$misc['thumbs'][$i]))
			{
				self::$errors[] = "Missing thumbnail ".($i+1); //on the template, the user wants to see "1" instead of "0"
				self::$errorcss['thumbs'][$i] = ' error';
			}
		}

		//LatLng check
		if(is_null(self::$location['LatLng']) && in_array('LatLng', self::$mandatory))
		{
			self::$errors[] = "Lat/Lng must be two float numbers separated by comma.";
			self::$errorcss['LatLng'] = ' error';
		}
		/*** /check for errors ***/


		//all mandatory fields are present
		if (count(self::$errorcss) === 0) {

			/***database connect***/
			$db = DB::getInstance();

		        // If the error array is empty, there were no errors in the submitted data.
			// all required variables were filled
			// all thumbnails were found
		        // now write the data

			//for rollback
			$oldthumbs = array();

			//id for edit
			//need to get this again, because it was overwritten by the input above.
			//this is used to fork into edit or add below
			if(isset($_GET['edit'])) self::$c['id'] = intval($_GET['edit']);


			try {

					/***********************/
					/* edit existing model */
					/***********************/
					if(isset(self::$c['id']) && self::$c['id'] !== 0)
					{

						//check if model exists
						$check = $db->Row("SELECT `id`, `slug`
								FROM `bx_content`
								WHERE `id` = ?
								LIMIT 1",
								array(self::$c['id']));

						if (!$check)	throw new Exception('model does not exist');
								//no rollback required


						//slug

							//check if the slug was updated in the edit
							$oldslug = $check['slug']; //from query above
								unset($check); //don't need this anymore
							$newslug = self::$c['slug']; //from input

							$oldinternalslug = Input::clean($oldslug, 'FILENAME');
							$newinternalslug = Input::clean($newslug, 'FILENAME');

							//these have no trailing slashes
							$oldinternalslugpath = Path::Get('path:thumbs/models') . DIR_SEP . $oldinternalslug;
							$newinternalslugpath = Path::Get('path:thumbs/models') . DIR_SEP . $newinternalslug;

							if($oldinternalslugpath === $newinternalslugpath)
							{
								//slug was not updated, we will simply use the existing value

								//check if the old slug folder exists
								if(!is_dir( $oldinternalslugpath ))
								{
									//create slug directory
									if(!Filehandler::MkDir($oldinternalslugpath, true))
									{
										//could not create the thumbnail directory
										throw new Exception('could not create slug directory');
									}
									else
										Rollback::$log['newslugcreation'] = false;
										//creating the old slug directory does not need to be rolled back
								}
							}
							else
							//the slug was changed
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
														self::$c['slug'],
														self::$c['id']
													));
							}

							//yay for comment overload!

							//we now have a valid newinternalslugpath


						//thumbs

							//rename the individual thumbnails as they come!

							//first backup the old thumbnail database entries
							$oldthumbs = $db->FetchAll("SELECT * FROM `bx_thumbnail` WHERE `content_id` = ? AND `type` = 'model' AND `theme` = ?", array(self::$c['id'], self::$o['frontend_theme']));

							//loop through the thumbnails
							for ($i=0, $s=count(self::$misc['thumbs']); $i < $s; $i++)
							{
								//get extension
								$ext = String::GetFileExtension(self::$misc['thumbs'][$i]);

								//fork: thumbnail changed or not?
								if(!preg_match("~^".String::Slash(Path::Get('rel:thumbs'),1,1)."~", self::$misc['thumbs'][$i]))
								{
									//this is a new thumbnail that sits in the temp directory

									//important:
									//in this case, self::$misc['thumbs'][$i] has this form:
									//array(1) { [0]=> string "cd4a16fc316fb8f56240196f46dd276a.jpg" }

									//safety copy of existing old thumbnail
									//the existing thumbnail is moved to the temp directory
									//so that it can later be restored
									@Filehandler::SmartCopy(
										$oldinternalslugpath. DIR_SEP . $i . '.' . $ext,
										Path::Get('path:admin/temp'). DIR_SEP .$i.'.'.$ext
									);
									//we moved the old thumb into the temp directory
									//add the path to the rollback log
									Rollback::$log['thumbintemp'][$i]['temppath'] = Path::Get('path:admin/temp'). DIR_SEP .$i.'.'.$ext;
									//add the undo destination for the rollback process
									Rollback::$log['thumbintemp'][$i]['dest'] = $oldinternalslugpath. DIR_SEP .$i.'.'.$ext;

									//delete existing thumbnail to make room for the new one
									@Filehandler::DeleteFile($newinternalslugpath .  DIR_SEP . $i.'.'.$ext);

									//thumbnail needs to be moved
									if(!@Filehandler::MoveFile(
										Path::Get('path:admin/temp') . DIR_SEP . self::$misc['thumbs'][$i],
										$newinternalslugpath . DIR_SEP . $i . '.' . $ext
									))
									{
										//error marking
										self::$errorcss['thumbs'][$i] = ' error';
										throw new Exception ("could not add new thumbnail");
									}
									else
									{
										//add the move to the Rollback log
										//thumbnail was moved from the temp directory to the thumbs folder
										//the current position of the thumbnail
										Rollback::$log['thumbmove'][$i]['current'] = $newinternalslugpath . DIR_SEP . $i.'.'.$ext;
										//where it needs to be moved if update is rolled back
										Rollback::$log['thumbmove'][$i]['dest'] = Path::Get('path:admin/temp'). DIR_SEP .self::$misc['thumbs'][$i];
									}

									//update the database

									//delete old thumbnail
									$db->Update("DELETE FROM `bx_thumbnail` WHERE `internal_id` = ? AND `type` = 'model' AND `content_id` = ? AND `theme` = ? LIMIT 1",
									array($i,
									self::$c['id'],
									self::$o['frontend_theme']));

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
										self::$c['id'], //XXX
										Path::Get('rel:thumbs/models') . '/' .$newinternalslug . '/' . $i.'.'.$ext,
										$i,
										'model',
										self::$o['thumbsizes'][$i]['width'],
										self::$o['thumbsizes'][$i]['height'],
										self::$o['frontend_theme'],
										TIME_NOW
									));

								}
								else
								{
									//thumbnail was not changed
									//and resides in the /thumb/models/<slug> folder

									//important
									//in this case, self::$misc['thumbs'][$i] has this form:
									//array(1) { [0]=> string "/thumbs/models/sdsewwe/0.jpg" }

									//update the path of the thumbnail if slug directory was changed
									if($oldinternalslugpath !== $newinternalslugpath)
									{
										$db->Update("UPDATE `bx_thumbnail`
												SET `path` = ?
												WHERE `content_id` = ?
												AND `internal_id` = ?
												AND `type` = ?
												AND `theme` = ?",
										array(
											Path::Get('rel:thumbs/models') . '/'.$newinternalslug . '/' . $i.'.'.$ext,
											self::$c['id'],
											$i,
											'model',
											self::$o['frontend_theme']));

										//reset for the right output
										self::$misc['thumbs'][$i] = Path::Get('rel:thumbs/models') . '/'.$newinternalslug . '/' . $i.'.'.$ext;
									}
								}
							}

							Rollback::$log['thumbupdate'] = true;

						/* country */ //ok

							//get the country iso code
							self::$location['iso'] = $db->Column("SELECT `iso` FROM `bx_country` WHERE `country` = ?", array(self::$location['country']));

							if(!self::$location['iso']) {
								//check if country is mandatory
								if(in_array('country', array_flip(self::$mandatory)))
								{
									//abort update process, because every location/model must have a country
									throw new Exception('missing country');
								}
								else
								{
									self::$location['iso'] = null;
								}
							}

						/* location */ //ok

							//check if location is already in database
							$location_id = $db->Column("SELECT l.id
							FROM `bx_location` AS `l`, `bx_content` AS `c`
							WHERE l.id = c.location_id
							AND c.id = ?
							LIMIT 1",
							array(self::$c['id']));

							if(!$location_id)
							{
								//write new location

								//location description is not used for now
								Rollback::$log['location_insert'] = $db->Update("INSERT INTO `bx_location` (
									`id`,
									`location`,
									`state`,
									`zipcode`,
									`description`,
									`LatLng`,
									`country_iso`
								) VALUES (
									?, ?, ?, ?, NULL, ?, ?
								)", array(
									NULL,
									self::$location['location'],
									self::$location['state'],
									self::$location['zipcode'],
									self::$location['LatLng'],
									self::$location['iso']
								));

								$location_id = $db->LastInsertId();
								if(!$location_id)
									$location_id = null;
							}
							else
							{
								//update existing location

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
								self::$location['location'],
								self::$location['state'],
								self::$location['zipcode'],
								NULL,
								self::$location['LatLng'],
								self::$location['iso'],
								$location_id));
							}

						/* model */ //ok

							//use the autoincrement id to insert the model into the bx_content table

							$oldmodel = $db->Row("SELECT * FROM `bx_content` WHERE `id` = ?", array(self::$c['id']));

							Rollback::$log['$model_update'] = $db->Update("UPDATE `bx_content` SET
							`slug` = ?,
							`memberpath` = ?,
							`freepath` = ?,
							`title` = ?,
							`description` = ?,
							`dateline` = ?,
							`location_id` = ?
							WHERE `id` = ?", array(
							self::$c['slug'],
							String::Slash(Path::Get('rel:member/model'),1,1).self::$c['slug'].'/',
							String::Slash(Path::Get('rel:site/model'),1,1).self::$c['slug'].'/',
							self::$c['title'],
							self::$c['description'],
							TIME_NOW,
							$location_id,
							self::$c['id']));

						/* 2257 entry */ //ok

							$old2257 = $db->Row("SELECT * FROM `bx_2257` WHERE `content_id` = ?", array(self::$c['id']));

							if($old2257)
							{
								Rollback::$log['_2257_update'] = $db->Update("UPDATE `bx_2257` SET
								`real_name` = ?,
								`aliases` = ?,
								`birthdate` = ?,
								`gender` = ?,
								`passport_id` = ?,
								`identification_path` = ?,
								`misc_url` = ?,
								`location_id` = ?,
								`notes` = ?,
								`dateline` = ?
								WHERE `content_id` = ?", array(
								self::$_2257['realname'],
								self::$_2257['aliases'],
								self::$_2257['birthdate'],
								self::$_2257['gender'],
								self::$_2257['passport_id'],
								self::$_2257['idurl'],
								self::$_2257['miscurl'],
								$location_id,
								self::$_2257['notes'],
								TIME_NOW,
								self::$c['id']
								));

								if(!Rollback::$log['_2257_update']) throw new Exception('could not write 2257 entry');
							}
							else //add new
							{
								Rollback::$log['_2257_insert'] = $db->Update("INSERT INTO `bx_2257` (
									`id`,
									`content_id`,
									`real_name`,
									`aliases`,
									`birthdate`,
									`gender`,
									`passport_id`,
									`modelrelease_path`,
									`identification_path`,
									`misc_url`,
									`production_date`,
									`location_id`,
									`notes`,
									`dateline`
								) VALUES (
									?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
								)", array(
									NULL,
									self::$c['id'],
									self::$_2257['realname'],
									self::$_2257['aliases'],
									self::$_2257['birthdate'],
									self::$_2257['gender'],
									self::$_2257['passport_id'],
									NULL,
									self::$_2257['idurl'],
									self::$_2257['miscurl'],
									self::$_2257['productiondate'],
									$location_id,
									self::$_2257['notes'],
									TIME_NOW
								));

								$id2257 = $db->LastInsertId();

								if(!$id2257) throw new Exception('could not write 2257 entry');
							}

						/* tags */ //ok

							if(!empty(self::$misc['tags']))
							{
								$tags = explode(",", self::$misc['tags']);

								//remove duplicate tags in array
								$tags = array_unique($tags);

								//trim array
								$tags = Arr::Trim($tags);

								$oldtag_content = $db->FetchAll("SELECT * FROM `bx_tag_content`
								WHERE `content_id` = ?", array(self::$c['id']));
;
								//remove old tags
								Rollback::$log['tag_content_update'] = $db->Update("DELETE FROM `bx_tag_content`
								WHERE `content_id` = ?", array(self::$c['id']));

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
												`id`,
												`tag`,
												`description`
											) VALUES (
												?, ?, ?
											)", array(
												NULL,
												Input::clean($tag,'NOHTML'),
												NULL
											));

											$tagid = $db->LastInsertId();
											Rollback::$log['tag_inserts'] .= "'".$tagid . "',";
										}
										else
										{
											//else get id of tag query from above
											$tagid = $exists;
										}

										$db->Update("INSERT INTO `bx_tag_content` (
											`content_id`,
											`tag_id`,
											`dateline`
										) VALUES (
											?,?,?
										)", array(
											self::$c['id'],
											$tagid,
											TIME_NOW
										));

									}
								} //foreach

								Rollback::$log['tag_inserts'] = rtrim(Rollback::$log['tag_inserts'], ",");
							}

						/* rating */

							//resetting the rating will remove all user ratings!

/*
							$db->Update("DELETE FROM `bx_rating` WHERE `content_id` = ?",
							array($model_id));

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
									$model_id,
									self::$misc['rating'],
									self::$o['admin_member_account'],
									TIME_NOW
								));
							}
*/


						//success!
						self::$success = true;
						Logger::AdminActivity('edited model', self::$c['id']);

					}
					/*****************/
					/* add new model */
					/*****************/
					else
					{
						/* slug */ //ok

							//slug is mandatory
							//slug exists check
							$slugcheck = Tools::slug_exists_check(self::$c['slug'], 'model');

							if(!$slugcheck){ //slug already exists, but must be unique!
								//error marking
								self::$errorcss['slug'] = ' error';
								throw new Exception('slug already exists');
							}

						/* country */ //ok

							//get the country iso code
							self::$location['iso'] = $db->Column("SELECT `iso` FROM `bx_country` WHERE `country` = ?", array(self::$location['country']));

							if(!self::$location['iso']) {
								//check if country is mandatory
								if(in_array('country', array_flip(self::$mandatory)))
								{
									//abort update process, because country was set to mandatory for every location/model
									throw new Exception('missing country');
								}
								else
									self::$location['iso'] = null;
							}

						/* location */ //ok

							//check if location is already in database
							//checks country, too, to allow duplicate locations in different countries
							$addlocation_id = $db->Column("SELECT `id`
									FROM `bx_location`
									WHERE `location` = ?
									AND `country_iso` = ?
									LIMIT 1",
									array(
										self::$location['location'],
										self::$location['iso']
									));

							if(!$addlocation_id) //not yet in database
							{
								//write new location

								//location description is not used for now
								Rollback::$log['location_insert'] = $db->Update("INSERT INTO `bx_location` (
									`id`, `location`, `state`, `zipcode`, `description`, `LatLng`, `country_iso`
								) VALUES (
									?, ?, ?, ?, NULL, ?, ?
								)", array(
									NULL,
									self::$location['location'],
									self::$location['state'],
									self::$location['zipcode'],
									self::$location['LatLng'],
									self::$location['iso']
								));

								$addlocation_id = $db->LastInsertId();
								if(!$addlocation_id) $addlocation_id = null;
							}

						/* model */ //ok

							//nicEdit adds an annoying "<br>" at end of text that needs to be removed
							self::$c['description'] = String::rTrimBr(self::$c['description']);

							//use the autoincrement id to insert the model into the bx_content table

							Rollback::$log['model_insert'] = $db->Update("INSERT INTO `bx_content` (
								`id`,
								`type`,
								`slug`,
								`memberpath`,
								`freepath`,
								`title`,
								`description`,
								`count`,
								`dateline`,
								`location_id`
							) VALUES (
								NULL, 'model', ?, ?, ?, ?, ?, '0', ?, ?
							)", array(
								self::$c['slug'],
								String::Slash(Path::Get('rel:member/model'),1,1).self::$c['slug'].'/',
								String::Slash(Path::Get('rel:site/model'),1,1).self::$c['slug'].'/',
								self::$c['title'],
								self::$c['description'],
								TIME_NOW,
								$addlocation_id
							));

							$model_id = $db->LastInsertId();

							if(!$model_id) {
								//abort update process, because we have no content_id
								throw new Exception('missing model id');
							}

						/* 2257 entry */

							Rollback::$log['_2257_insert'] = $db->Update("INSERT INTO `bx_2257` (
								`id`, `content_id`, `real_name`, `aliases`, `birthdate`, `gender`, `passport_id`, `modelrelease_path`, `identification_path`, `misc_url`, `production_date`, `location_id`, `notes`, `dateline`
							) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
							)", array(NULL,
								$model_id,
								self::$_2257['realname'],
								self::$_2257['aliases'],
								self::$_2257['birthdate'],
								self::$_2257['gender'],
								self::$_2257['passport_id'],
								NULL,
								self::$_2257['idurl'],
								self::$_2257['miscurl'],
								self::$_2257['productiondate'],
								$addlocation_id,
								self::$_2257['notes'],
								TIME_NOW
							));

							$id2257 = $db->LastInsertId();

							if(!$id2257) throw new Exception('could not write 2257 entry');

						/* thumbnails moving */

							//clear-up
							$db->Update("DELETE FROM `bx_thumbnail` WHERE `content_id` = ? AND `theme` = ?", array($model_id, self::$o['frontend_theme']));

							$thumbfolder = Path::Get('path:thumbs/models') . DIR_SEP . self::$c['internal_slug'] . DIR_SEP;

							//create the subfolder in the thumb directory
							if(!Filehandler::MkDir($thumbfolder, true))
							{
								//could not create the thumbnail directory
								throw new Exception('could not create slug directory');
							}
							else
								Rollback::$log['thumbfolder_creation'] = true;

							//for each thumbnail
							for($i=0, $s = count(self::$misc['thumbs']); $i < $s; $i++)
							{
								$ext = String::GetFileExtension(self::$misc['thumbs'][$i]);

								//thumb filename
								$thfilename = $i;

								//the thumbnail
								$thumbpath = $thumbfolder . $thfilename. '.' .$ext;

								//file exists check
									$k=0;
									while(is_file($thumbpath))
									{
										//the thumbnail
										$thumbpath = $thumbfolder . $thfilename.  "-" . $k . '.' .$ext;
										$k++;
									}
								if($k != 0)
									$thfilename = $thfilename . '-' . $k . '.' . $ext;
								else
									$thfilename = $thfilename . '.' . $ext;


								//move uploaded thumbnail
								if(!@Filehandler::SmartCopy(Path::Get('path:admin/temp').DIR_SEP.self::$misc['thumbs'][$i],
								$thumbpath))
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
									$model_id,
									Path::Get('rel:thumbs/models') . '/' . self::$c['internal_slug'] . '/' . $thfilename,
									$i,
									'model',
									self::$o['thumbsizes'][$i]['width'],
									self::$o['thumbsizes'][$i]['height'],
									self::$o['frontend_theme'],
									TIME_NOW
								));

								Rollback::$log['thumbnail_insert_ids'] .= $db->LastInsertId().",";

							}
							//remove trailing comma
							Rollback::$log['thumbnail_insert_ids'] = rtrim(Rollback::$log['thumbnail_insert_ids'], ",");

						/* tags */ //ok

							if(!empty(self::$misc['tags']))
							{
								$tags = explode(",", self::$misc['tags']);

								//remove duplicate tags in array
								$tags = array_unique($tags);

								//trim array
								$tags = Arr::Trim($tags);

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
										)", array($model_id, $tagid, TIME_NOW));

									}
//									else{
//										break;
//									}
								} //foreach

								Rollback::$log['tag_inserts'] = rtrim(Rollback::$log['tag_inserts'], ",");
								Rollback::$log['tag_content_insert'] = true;
							}

						/* rating */

							//member #1 = usually admin user account
							if(self::$o['admin_member_account'])
							{
								Rollback::$log['rating_insert'] = $db->Update("INSERT INTO `bx_rating` (
									`id`,
									`content_id`,
									`rating`,
									`member_id`,
									`IP`,
									`dateline`
								) VALUES (
									?, ?, ?, ?, ?, ?
								)", array(
									NULL,
									$model_id,
									self::$misc['rating'],
									self::$o['admin_member_account'],
									Session::FetchIP(),
									TIME_NOW
								));

								$rating_id = $db->LastInsertId();

								if(!$rating_id)
									throw new Exception('could not write rating');
							}


						//success!
						self::$success = true;
						Logger::AdminActivity('added model', $model_id);

					} //edit or add new

			} //try
			catch (Exception $e) {
 			//ROLLBACK

 				$tpl->errormessage($translate->_('Error').': '.$e->getMessage().PHP_EOL);

				self::$success = false;

				//rollback!
				if(Rollback::$log['location_insert'])
					$db->Update("DELETE FROM `bx_location` WHERE `id` = ?", array($addlocation_id));
				if(Rollback::$log['model_insert'])
					$db->Update("DELETE FROM `bx_content` WHERE `id` = ?", array($model_id));
				if(Rollback::$log['_2257_insert'])
					$db->Update("DELETE FROM `bx_2257` WHERE `id` = ?", array($id2257));
				if(Rollback::$log['rating_insert'])
					$db->Update("DELETE FROM `bx_rating` WHERE `id` = ?", array($rating_id));
				if(Rollback::$log['tag_content_insert'])
					$db->Update("DELETE FROM `bx_tag_content` WHERE `content_id` = ?", array($model_id));
				if(Rollback::$log['tag_inserts'])
					$db->Update("DELETE FROM `bx_tag` WHERE `id` IN (".Rollback::$log['tag_inserts'].")");
				if(Rollback::$log['thumbfolder_creation'])
					Filehandler::RemoveDir($thumbfolder);
				if(Rollback::$log['thumbnail_insert_ids'])
					$db->Update("DELETE FROM `bx_thumbnail` WHERE `id` IN (".Rollback::$log['thumbnail_insert_ids'].")");
				if(Rollback::$log['_2257_update'])
					Rollback::Undo('2257_model_update', $old2257, self::$c['id']);
				if(Rollback::$log['location_update'])
					Rollback::Undo('location_update', $oldlocation, $location_id);
				if(Rollback::$log['model_update'])
					Rollback::Undo('content_update', $oldmodel, self::$c['id']);
				if(Rollback::$log['newslugcreation'])
					@Filehandler::RemoveDir($newinternalslugpath);
				if(Rollback::$log['slugrenamed'])
					@Filehandler::MoveDir($newinternalslugpath,$oldinternalslugpath);
				if(Rollback::$log['slugdbupdate'])
					$db->Update("UPDATE `bx_content` SET `slug` = ? WHERE `id` = ?", array($oldslug, self::$c['id']));
				if(Rollback::$log['thumbupdate'])
					Rollback::Undo('thumbnail_delete', $oldthumbs, array(self::$c['id'], 'model', self::$o['frontend_theme']));
				if(Rollback::$log['thumbintemp'] || Rollback::$log['thumbmove'])
				{
					@Filehandler::RemoveDir($newinternalslugpath);
					@Filehandler::MkDir($oldinternalslugpath);

					//undo safety copy
					for ($i=0, $s=count(self::$misc['thumbs']); $i<$s; $i++)
					{
						@Filehandler::MoveFile(
							Rollback::$log['thumbintemp'][$i]['temppath'],
							Rollback::$log['thumbintemp'][$i]['dest']
						);
					}
				}
				//restore tag_content
				if(Rollback::$log['tag_content_update'])
					Rollback::Undo('tag_content_insert', $oldtag_content, self::$c['id']);

				self::$success = false;
			} //ROLLBACK



			//all done!
			if(self::$success === true)
			{
				if(!empty(self::$c['id']))
				{
					//redirect to models list to prevent resending form when hitting back button
					$tpl->redirect(Path::Get('url:admin').'/model?edit='.self::$c['id']."&success");
				}
				else
				{
					$tpl->redirect(Path::Get('url:admin').'/model?success');
				}
			}

		} //if no errors

	} //addnew

/**
* Edit
*
* loads the data if we want to edit an existing model
*
* @access public
*/
	public function edit()
	{
		/***database connect***/
		$db = DB::getInstance();

		//rating
		self::$misc['rating'] = $db->Column("SELECT AVG(r.rating) AS `rating`
					FROM `bx_rating` AS `r`
					WHERE r.content_id = ?
					GROUP BY r.content_id", array(self::$c['id']));

		//tags
		//get the tags for the model
		$_temp = $db->FetchAll("SELECT t.tag
					FROM `bx_tag` AS `t`
					JOIN `bx_tag_content` AS `tc` ON (t.id = tc.tag_id)
					WHERE tc.content_id = ?", array(self::$c['id']));
		//reduce tag array
		$tags = "";
		for($i = 0, $s = count($_temp); $i < $s; $i++)
		{
			$tags .= $_temp[$i]['tag']. ",";
		}
		$tags = rtrim($tags, ",");
		unset($_temp);

		self::$misc = self::$misc + array("tags" => $tags);

		//content
		self::$c = $db->Row("SELECT m.id, m.title, m.slug, m.description
					FROM `bx_content` AS `m`
					WHERE m.id = ?", array(self::$c['id']));

		if(!self::$c) throw new Exception("ID does not exist");

		//location
		self::$location = $db->Row("SELECT l.location, l.zipcode, l.state, l.LatLng,
					c.country
					FROM `bx_content` AS `m`
					LEFT JOIN `bx_location` AS `l` ON (l.id = m.location_id)
					LEFT JOIN `bx_country` AS `c` ON (c.iso = l.country_iso)
					WHERE m.id = ?", array(self::$c['id']));

		//2257
		self::$_2257 = $db->Row("SELECT f.birthdate, f.gender, f.passport_id, f.real_name AS `realname`,
					f.aliases, f.identification_path AS `idurl`, f.misc_url AS `miscurl`, f.notes
					FROM `bx_2257` AS `f`
					WHERE f.content_id = ?", array(self::$c['id']));

		//thumbnails
		$thumbnails = $db->FetchAll("SELECT th.path, th.internal_id
					FROM `bx_thumbnail` AS `th`
					JOIN `bx_content` AS `m` ON (th.content_id = m.id)
					WHERE m.id = ?
					AND th.theme = ?
					ORDER BY th.internal_id ASC",
					array(self::$c['id'], self::$o['frontend_theme']));

		//check if files exist
		for ($i=0, $s = count($thumbnails); $i < $s; $i++)
		{
			$key = $thumbnails[$i]['internal_id'];

			if(!file_exists( Path::Get('path:site') . String::Slash($thumbnails[$i]['path'], 1, 0) ))
			{
				//thumbnail not found!

				//add the relative path to the errorpaths array
				self::$errorpaths[$key] = $thumbnails[$i]['path'];

			}
			else
			{
				//thumbnail found

				self::$misc['thumbs'][ $key ] = $thumbnails[$i]['path']; //relative path!
			}
		}
		unset($thumbnails);

		//tell the template what to do (for showing the thumbnails)
		//used in template
		self::$misc = array_merge(self::$misc, array('_action' => 'edit'));

		//that's it. The rest is done in addnew() [only if isset($_POST)]

	} //edit

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		self::$o = Config::GetDBOptions(array(
			//prime cache (used later)
			'frontend_theme',
			'floating_thumbs',
			'admin_member_account',
			));

		//get thumbsizes
		self::$o['thumbsizes'] = Config::Get('model_thumbnailsize');

		//get the mandatory form fields
		//(required variables)
		self::$mandatory = Config::Get('mandatory_model');

		//get translation
		$translate = Zend_Registry::get('translate');

		/***database connect***/
		$db = DB::getInstance();

		//session
		self::$token = Session::GetToken();

		//id
		if(isset($_GET['edit'])) self::$c['id'] = intval($_GET['edit']);

		try {
				if(!empty($_POST))
				{
					if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
						throw new Exception($translate->_('Security Token mismatch'));

					if( Authentification::CheckPermission('administrator', 'editor') )
					{
						/***add new model***/
						if (isset($_POST['_action']) && $_POST['_action'] == 'addnew')
							$this->addnew();
					}
				}
				/***load existing model***/
				//does nothing else but to fill the form fields
				elseif(isset($_GET['edit']))
					$this->edit();
		}
		catch (Exception $e) {
				echo $translate->_('Error').': '.  $e->getMessage(). "<br>".PHP_EOL;
				die();
		}

		$this->index();

	} //ShowIndex

} //class
