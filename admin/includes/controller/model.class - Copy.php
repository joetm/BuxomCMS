<?php

/* **************************************************************
 *  File: model.class.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

class model extends BaseController
{

	public static $success = false;
	public static $errors = array();
	public static $errorcss = array();

	private static $output = array();
	private static $thumbsizes = array();

	public static $id = 0;
	private static $internal_slug = '';

	//required variables
	public static $mandatory = array();

	private static $token = false;

	private static $log = array();

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
			Filehandler::EmptyTempDir();
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
		if(!isset(self::$output['country']))
			self::$output['country'] = Config::Get('default_country');

		//output
		$tpl->assign("output", self::$output);

		//error/success
		if (self::$success || isset($_GET['success']))
			$tpl->successmessage($translate->_("Done"));
		if (self::$errors) 	$tpl->assign("errors", self::$errors);
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
		$tpl->assign("thumbsizes", self::$thumbsizes);

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
				'LatLng' 		=>	'LATLNG',
				'country'		=>	'NOHTML',
				'description'	=>	'STR',
				'gender'		=>	'NOHTML',
				'location'		=>	'NOHTML',
				'modelname'		=>	'NOHTML',
				'rating'		=>	'UNUM',
				'slug'			=>	'NOHTML',
				'state'			=>	'NOHTML',
				'tags'			=>	'NOHTML',
				'zipcode'		=>	'NOHTML',
			//2257 data
				'aliases'		=>	'NOHTML',
				'birthdate'		=>	'DATE',
				'gender'		=>	'NOHTML',
				'idurl'			=>	'NOHTML',
				'miscurl'		=>	'NOHTML',
				'notes' 		=>	'NOHTML',
				'passport_id'	=>	'NOHTML',
				'productiondate'=>	'NOHTML',
				'realname' 		=>	'NOHTML',
			));
			if (self::$output['rating'] == null) self::$output['rating'] = 0;

			//internally, we only deal with a sanitized version of the slug directory
			$noref = self::$output['slug'];
			self::$internal_slug = Input::clean($noref, 'FILENAME');

			//nicEdit adds an annoying "<br>" at end of text that needs to be removed
			self::$output['description'] = String::rTrimBr(self::$output['description']);

			//thumbs
			self::$output = array_merge(self::$output, Input::clean_array('p',array(
				'thumbs' => 'ARRAY_NOHTML'
			)));


			/*** check for errors ***/
				//mandatory variable check
				foreach(array_flip(self::$mandatory) as $key => $value)
				{
					if(!isset(self::$output[$key]))
					{
						//error name
						self::$errors[] = "Missing ".$key;
						//error css
						self::$errorcss[$key] = ' error';
					}
					elseif(!self::$output[$key])
					{
						//error name
						self::$errors[] = "Missing ".$key;
						//error css
						self::$errorcss[$key] = ' error';
					}
				}

				//thumbnail check
				//all thumbs are mandatory
				for($i=0, $s = count(self::$thumbsizes); $i<$s; $i++)
				{
					if(!isset(self::$output['thumbs'][$i]))
					{
						self::$errors[] = "Missing thumbnail ".($i+1);
						self::$errorcss['thumbs'][$i] = ' error';
					}
					//check the thumbnail path!

				}

				//LatLng check
				if(is_null(self::$output['LatLng']) && in_array('LatLng',self::$mandatory))
				{
					self::$errors[] = "Lat/Lng must be two float numbers separated by comma.";
					self::$errorcss['LatLng'] = ' error';
				}
			/*** /check for errors ***/



		    if (count(self::$errorcss) === 0) {

		        // If the error array is empty, there were no errors.
				// all required variables were filled
				// all thumbnails were found
		        // now write the data

				define("DATELINE", time());

				/***database connect***/
				$db = DB::getInstance();

				$oldthumbs = array();

				try {

					/***********************/
					/* edit existing model */
					/***********************/
					if(isset(self::$id) && self::$id != 0)
					{

						//check if model exists
						$check = $db->Row("SELECT `id`,`slug` FROM `bx_content` WHERE `id`=? LIMIT 1", array(self::$id));

						if (!$check)	throw new Exception('model does not exist');


						/* slug and thumbs */ //ok

							//check if the slug was updated in the edit
							$oldslug = $check['slug'];
							$newslug = self::$output['slug'];

							$oldinternalslug = Input::clean($oldslug, 'FILENAME');
							$newinternalslug = Input::clean($newslug, 'FILENAME');

							$oldinternalslugpath = __thumbpath. '/models/'.$oldinternalslug;
							$newinternalslugpath = __thumbpath. '/models/'.$newinternalslug;

							if($oldinternalslugpath == $newinternalslugpath)
							{
								//slug was not updated, we will simply use the existing value

								//check if the old slug folder exists

								if (!is_dir( $newinternalslugpath ))
								{
									//create slug directory
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

							$oldthumbs = $db->FetchAll("SELECT * FROM `bx_thumbnail` WHERE `content_id`=? AND `type`='model'", array(self::$id));

							for ($i=0, $s=count(self::$output['thumbs']); $i<$s; $i++)
							{
								$ext = String::GetFileExtension(self::$output['thumbs'][$i]);

								//savety copy
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
									else
										Rollback::$log['thumbmove'][$i]['current'] = String::RemoveTrailingSlash($newinternalslugpath).'/'.$i.'.'.$ext;
										Rollback::$log['thumbmove'][$i]['dest'] = _ADMINPATH.'/temp/'.self::$output['thumbs'][$i];

									//update the database

										//delete old thumbnail
										$db->Update("DELETE FROM `bx_thumbnail` WHERE `internal_id`=? AND `type`=? AND `content_id`=? LIMIT 1",
										array($i, 'model', self::$id));

										$th = __thumburl . '/models/'.$newinternalslug.'/'.$i.'.'.$ext;
										//add new thumbnail
										$db->Update("INSERT INTO `bx_thumbnail` (
										`id`,`content_id`,`path`,`internal_id`,`type`,`dateline`
										) VALUES (
										?,?,?,?,?,?
										)",array(
										NULL, self::$id, $th, $i, 'model', DATELINE
										));
								}
								else
								{
									//thumbnail was not changed

									//update the path of the thumbnail if slug was changed
									if($oldinternalslugpath != $newinternalslugpath)
									{
										$db->Update("UPDATE `bx_thumbnail` SET `path`=? WHERE `internal_id`=? AND `type`=? AND `content_id`=?",
										array(__thumburl . '/models/'.$newinternalslug.'/'.$i.'.'.$ext, $i, 'model', self::$id));
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
								if(!$location_id)
								{
									$location_id = null;
								}
							}
							else
							{
								//update existing location

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

						/* model */ //ok

							//use the autoincrement id to insert the model into the bx_content table

							$oldmodel = $db->Row("SELECT * FROM `bx_content` WHERE `id`=?", array(self::$id));

							Rollback::$log['$model_update'] = $db->Update("UPDATE `bx_content` SET
							`slug`=?,
							`title`=?,
							`description`=?,
							`dateline`=?,
							`location_id`=?
							WHERE `id`=?", array(
							self::$output['slug'],
							self::$output['modelname'],
							self::$output['description'],
							DATELINE,
							$location_id,
							self::$id));

						/* 2257 entry */ //ok

							$old2257 = $db->Row("SELECT * FROM `bx_2257` WHERE `content_id`=?", array(self::$id));

							if($old2257)
							{
								Rollback::$log['_2257_update'] = $db->Update("UPDATE `bx_2257` SET
								`real_name`=?,
								`aliases`=?,
								`birthdate`=?,
								`gender`=?,
								`passport_id`=?,
								`identification_path`=?,
								`misc_url`=?,
								`location_id`=?,
								`notes`=?,
								`dateline`=?
								WHERE `content_id`=?", array(
								self::$output['realname'],
								self::$output['aliases'],
								self::$output['birthdate'],
								self::$output['gender'],
								self::$output['passport_id'],
								self::$output['idurl'],
								self::$output['miscurl'],
								$location_id,
								self::$output['notes'],
								DATELINE,
								self::$id
								));

								if(!Rollback::$log['_2257_update']) throw new Exception('could not write 2257 entry');
							}
							else //add new
							{
								Rollback::$log['_2257_insert'] = $db->Update("INSERT INTO `bx_2257` (
								`id`, `content_id`, `real_name`, `aliases`, `birthdate`, `gender`, `passport_id`, `modelrelease_path`, `identification_path`, `misc_url`, `production_date`, `location_id`, `notes`, `dateline`
								) VALUES (
								?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
								)", array(NULL, self::$id, self::$output['realname'], self::$output['aliases'], self::$output['birthdate'], self::$output['gender'], self::$output['passport_id'], NULL, self::$output['idurl'], self::$output['miscurl'], self::$output['productiondate'], $location_id, self::$output['notes'], DATELINE));

								$id2257 = $db->LastInsertId();

								if(!$id2257) throw new Exception('could not write 2257 entry');
							}

						/* tags */ //ok

							if(!empty(self::$output['tags']))
							{
								$tags = explode(",", self::$output['tags']);

								//remove duplicate tags in array
								$tags = array_unique($tags);

								//trim array
								$tags = Arr::Trim($tags);

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
						Logger::AdminActivity('edited model', self::$id);

					}
					/*****************/
					/* add new model */
					/*****************/
					else
					{
						/* slug */ //ok

							//slug is mandatory
							//slug exists check
							$slugcheck = Tools::slug_exists_check(self::$output['slug'], 'model');

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
									//abort update process, because every location/model must have a country
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

								$addlocation_id = $db->LastInsertId();
								if(!$addlocation_id) $addlocation_id = null;
							}

						/* model */ //ok

							//nicEdit adds an annoying "<br>" at end of text that needs to be removed
							self::$output['description'] = String::rTrimBr(self::$output['description']);

							//use the autoincrement id to insert the model into the bx_content table

							Rollback::$log['model_insert'] = $db->Update("INSERT INTO `bx_content` (
							`id`, `type`, `slug`, `title`, `description`, `count`, `dateline`, `location_id`
							) VALUES (
							NULL, 'model', ?, ?, ?, '0', ?, ?
							)", array(self::$output['slug'], self::$output['modelname'], self::$output['description'], DATELINE, $addlocation_id));

							$model_id = $db->LastInsertId();

							if(!$model_id) {
								//abort update process, because we have no content_id
								throw new Exception('missing model id');
							}

						/* 2257 entry */ //ok

							Rollback::$log['_2257_insert'] = $db->Update("INSERT INTO `bx_2257` (
							`id`, `content_id`, `real_name`, `aliases`, `birthdate`, `gender`, `passport_id`, `modelrelease_path`, `identification_path`, `misc_url`, `production_date`, `location_id`, `notes`, `dateline`
							) VALUES (
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
							)", array(NULL, $model_id, self::$output['realname'], self::$output['aliases'], self::$output['birthdate'], self::$output['gender'], self::$output['passport_id'], NULL, self::$output['idurl'], self::$output['miscurl'], self::$output['productiondate'], $addlocation_id, self::$output['notes'], DATELINE));

							$id2257 = $db->LastInsertId();

							if(!$id2257) throw new Exception('could not write 2257 entry');

						/* thumbnails moving */ //ok

							//clear-up
							$db->Update("DELETE FROM `bx_thumbnail` WHERE `content_id`=?", array($model_id));

							$thumbfolder = __thumbpath . '/models/' . self::$internal_slug . '/';

							//create the subfolder in the thumb directory
							if(!Filehandler::MkDir($thumbfolder, 0777, true))
							{
								//could not create the thumbnail directory
								throw new Exception('could not create slug directory');
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
								$thumburl = __thumburl . '/models/' . self::$internal_slug . '/' . $thfilename;

								//move uploaded thumbnail
								if(!@Filehandler::SmartCopy(_ADMINPATH.'/temp/'.self::$output['thumbs'][$i], $thumbpath))
/*
								if(!Filehandler::MoveFile(_ADMINPATH.'/temp/'.self::$output['thumbs'][$i], $thumbpath))
*/
								{
									@Filehandler::DeleteFile($thumbpath);
									throw new Exception('thumbnail could not be moved');
								}

								$db->Update("INSERT INTO `bx_thumbnail` (
									`id`, `content_id`, `path`, `internal_id`,`type`,`dateline`
								) VALUES (
									?, ?, ?, ?, ?, ?
								)", array(NULL, $model_id, $thumburl, $i, 'model', DATELINE));

								Rollback::$log['thumbnail_insert_ids'] .= $db->LastInsertId().",";

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
										)", array($model_id, $tagid, DATELINE));

									}
//									else{
//										break;
//									}
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
							)", array(NULL, $model_id, self::$output['rating'], '1', Session::FetchIP(), DATELINE));

							$rating_id = $db->LastInsertId();

							if(!$rating_id) {
/*
								//undo
								$db->Update("DELETE FROM `bx_content` WHERE `id`=?", array($model_id));
								$db->Update("DELETE FROM `bx_2257` WHERE `id`=?", array($id2257));
								$db->Update("DELETE FROM `bx_thumbnail` WHERE `content_id`=?", array($model_id));
*/
								throw new Exception('could not write 2257 entry');
							}


						//success!
						self::$success = true;
						Logger::AdminActivity('added model', $model_id);

					} //edit or add new

				} //try
				catch (Exception $e) {
					$tpl->errormessage($translate->_('Error').': '.$e->getMessage().PHP_EOL);

					self::$success = false;

					//rollback!
					if(Rollback::$log['location_insert'])
						$db->Update("DELETE FROM `bx_location` WHERE `id`=?", array($addlocation_id));
					if(Rollback::$log['model_insert'])
						$db->Update("DELETE FROM `bx_content` WHERE `id`=?", array($model_id));
					if(Rollback::$log['_2257_insert'])
						$db->Update("DELETE FROM `bx_2257` WHERE `id`=?", array($id2257));
					if(Rollback::$log['rating_insert'])
						$db->Update("DELETE FROM `bx_rating` WHERE `id`=?", array($rating_id));
					if(Rollback::$log['tag_content_insert'])
						$db->Update("DELETE FROM `bx_tag_content` WHERE `content_id`=?", array($model_id));
					if(Rollback::$log['tag_inserts'])
						$db->Update("DELETE FROM `bx_tag` WHERE `id` IN (".Rollback::$log['tag_inserts'].")");
					if(Rollback::$log['thumbfolder_creation'])
						Filehandler::RemoveDir($thumbfolder);
					if(Rollback::$log['thumbnail_insert_ids'])
						$db->Update("DELETE FROM `bx_thumbnail` WHERE `id` IN (".Rollback::$log['thumbnail_insert_ids'].")");
					if(Rollback::$log['_2257_update'])
						Rollback::Undo('2257_model_update', $old2257, self::$id);
					if(Rollback::$log['location_update'])
						Rollback::Undo('location_update', $oldlocation, $location_id);
					if(Rollback::$log['model_update'])
						Rollback::Undo('content_update', $oldmodel, self::$id);
					if(Rollback::$log['newslugcreation'])
						@Filehandler::RemoveDir($newinternalslugpath);
					if(Rollback::$log['slugrenamed'])
						@Filehandler::MoveDir($newinternalslugpath,$oldinternalslugpath);
					if(Rollback::$log['slugdbupdate'])
						$db->Update("UPDATE `bx_content` SET `slug`=? WHERE `id`=?", array($oldslug, self::$id));
					if(Rollback::$log['thumbupdate'])
						Rollback::Undo('thumbnail_delete', $oldthumbs, array(self::$id, 'model'));
					if(Rollback::$log['thumbintemp'] || Rollback::$log['thumbmove'])
					{
						@Filehandler::RemoveDir($newinternalslugpath);
						@Filehandler::MkDir($oldinternalslugpath);

						//undo safety copy
						for ($i=0, $s=count(self::$output['thumbs']); $i<$s; $i++)
						{
							@Filehandler::MoveFile(Rollback::$log['thumbintemp'][$i]['temppath'], Rollback::$log['thumbintemp'][$i]['dest']);
						}
					}
					//restore tag_content
					if(Rollback::$log['tag_content_update'])
						Rollback::Undo('tag_content_insert', $oldtag_content, self::$id);

					self::$success = false;
				} //ROLLBACK



				//all done!
				if(self::$success === true)
				{
					if(self::$id)
					{
						//redirect to models list to prevent resending form when hitting back button
						header('Location: '.__ADMIN_URL.'/model?edit='.self::$id."&success");
						die();
					}
					else
					{
						//redirect to blank form to prevent resending form when hitting back button
						header('Location: '.__ADMIN_URL.'/model?success');
						die();
					}
				}

			} //if no errors

	} //addnew

/********************/
/*		 EDIT	    */
/********************/

	private function edit()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		//get tpl
		$tpl = Zend_Registry::get('tpl');

		/*** get prefill data from database ***/

		/***database connect***/
		$db = DB::getInstance();

			/* model data */
			self::$output = $db->Row("SELECT `m`.`title` AS `modelname`, `m`.`slug`, `m`.`description`,
			`f`.`birthdate`, `f`.`gender`, `f`.`passport_id`, `f`.`real_name` AS `realname`, `f`.`aliases`,
			`f`.`identification_path` AS `idurl`, `f`.`misc_url` AS `miscurl`, `f`.`notes`,
			`l`.`location`, `l`.`zipcode`, `l`.`state`, `l`.`LatLng`,
			`c`.`country`,
			AVG(`r`.`rating`) AS `rating`
			FROM `bx_content` AS `m`
			LEFT JOIN `bx_2257` as `f` ON (`f`.`content_id`=`m`.`id`)
			LEFT JOIN `bx_location` AS `l` ON (`l`.`id`=`m`.`location_id`)
			LEFT JOIN `bx_country` AS `c` ON (`c`.`iso`=`l`.`country_iso`)
			LEFT JOIN `bx_rating` AS `r` ON (`r`.`content_id`=`m`.`id`)
			WHERE `m`.`id`=?
			GROUP BY `m`.`id`", array(self::$id));

			if(!self::$output) throw new Exception("ID does not exist");

			/* tags */
			//get the tags for the model
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

			/* thumbnails */

			//get the thumbnails
			$thumbnails = $db->FetchAll("SELECT `th`.`path`, `th`.`internal_id`
			FROM `bx_thumbnail` AS `th`, `bx_content` AS `m`
			WHERE `th`.`content_id`=`m`.`id`
			AND `m`.`id`=?
			ORDER BY `th`.`internal_id` ASC", array(self::$id));

			//check if files exist
			for ($i=0, $s=count($thumbnails); $i<$s; $i++)
			{
				if(!file_exists( __SITE_PATH . $thumbnails[$i]['path'] )) unset($thumbnails[$i]);
			}

			//merge the thumbnails into output array
			self::$output = array_merge(self::$output, array('thumbs' => $thumbnails));

			//tell the template what to do (for showing the thumbnails)
			self::$output = array_merge(self::$output, array('action' => 'edit'));


			/*** check for errors ***/
				//mandatory variable check

				foreach(self::$mandatory as $check)
				{
					if(!self::$output[$check])
					{
						self::$errors[] = "Missing ".$check;
						self::$errorcss[$check] = ' error';
					}
				}

				//thumbnail check
				//all thumbs are mandatory
				for($i=0, $s = count(self::$thumbsizes); $i<$s; $i++)
				{
					if(!isset(self::$output['thumbs'][$i]))
					{
						self::$errors[] = "Missing thumbnail ".$i;
						self::$errorcss['thumbs'][$i] = ' error';
					}
					else
					{
						//check the thumbnail path!




					}
				}

		//that is it.
		//updating the content is handled in addnew

	} //edit

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//get the mandatory form fields
		self::$mandatory = Config::Get('mandatory_model');

		//get translation
		$translate = Zend_Registry::get('translate');

		/***database connect***/
		$db = DB::getInstance();

		//get thumbsizes
		self::$thumbsizes = Config::Get('model_thumbnailsize');


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
							/***add new model***/
							if (isset($_POST['_action']))
								if ($_POST['_action'] == 'addnew')
									$this->addnew();
						}
					}
					else
						throw new Exception($translate->_('Security token mismatch'));
				}

				/***edit existing model***/
				if (isset($_GET['edit']))
					$this->edit();
		}
		catch (Exception $e) {
				echo $translate->_('Error').': '.  $e->getMessage(). "<br />".PHP_EOL;
				die();
		}

		$this->index();

	} //ShowIndex

} //class
