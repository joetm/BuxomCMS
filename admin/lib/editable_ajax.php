<?php

/* **************************************************************
 *  File: editable_ajax.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
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


$translate = new Translator("admin");
if(@$_POST['securitytoken'] !== Session::GetToken())
	die($translate->_('Security token mismatch'));

//constants
define("APPROVE","approve");
define("COLUMN", "column");
define("ID",	 "id");
define("TYPE",	 "type");
define("VALUE",	 "value");

//default return
$return = '#'.$translate->_('Error').'#';
$status = false;


//error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	global $return;

	if(Config::Get('debug'))
	switch ($errno) {
		case E_USER_ERROR:
				echo "<b>ERROR:</b> $errstr".PHP_EOL;
				//echo "  Fatal error on line $errline in file $errfile";
				die();
		break;

		case E_USER_WARNING:
			$return = "<b>WARNING:</b> $errstr".PHP_EOL;
			break;

		case E_USER_NOTICE:
			$return = "<b>NOTICE:</b> $errstr".PHP_EOL;
			break;

		case E_WARNING:
			//discard all warnings
			break;

		default:
			$return = "Unknown error [$errno]: $errstr, line: $errline".PHP_EOL; //($errfile)
			break;
	}


/*
	if ( ($errno & E_USER_ERROR) == E_USER_ERROR)
	{
        echo "<b>ERROR</b> $errstr<br>".PHP_EOL; //[$errno]
        echo "  Fatal error on line $errline in file $errfile";
        die();
	}
	elseif ( ($errno & E_USER_WARNING) == E_USER_WARNING)
	{
        echo "<b>WARNING</b> $errstr<br>".PHP_EOL; //[$errno]
	}
	elseif ( ($errno & E_USER_NOTICE) == E_USER_NOTICE)
	{
        echo "<b>NOTICE</b> $errstr<br>".PHP_EOL; //[$errno]
	}
	else
	{
        echo "Unknown error type: $errstr<br>".PHP_EOL; //[$errno]
	}
*/


    /* Do not execute PHP internal error handler */
    return true;
}

$old_error_handler = set_error_handler("myErrorHandler");


if(!empty($_POST))
{

	//preprocessing
	$_POST = array_map('trim', $_POST);

	//sanitize input
	$input = Input::clean_array('p',array(
		COLUMN		=> "UINT",
		ID		=> "UINT",
		TYPE		=> "STR",
		VALUE		=> "NOHTML",	//this is the return
	));

	if(!empty($input[TYPE])){

		$value = $input[VALUE];
		$table = $input[TYPE];
		$column = intval($input[COLUMN]);
		//hidden id column
		$id = intval($input[ID]);


		/***database connect***/
		$db = DB::getInstance();


		/**************/
		/*** models ***/
		/**************/
		if($input[TYPE] == 'model')
		{
			//column name //hidden column is skewing position (+1)
			$columnname = Datatable::GetColumn($column+1, $table);
			$table = Datatable::GetTable($table); // (internal table name)

			if(!empty($value) && !empty($table) && !empty($columnname) && !empty($id))
			{

				if($columnname == 'title' || $columnname == 'slug')
				{
					//if slug: move thumbnail directory
					if($columnname == 'slug')
					{
						$oldslug = $db->Column("SELECT `slug` FROM `bx_content` WHERE `id`=?", array($id));

						if(!$oldslug) die("Invalid Model ID.");

						//slugs are lower case
						$value = strtolower($value);

						//internal representations of slugs
						$old = Path::Get('path:thumbs/models') . DIR_SEP . strtolower(Input::clean($oldslug,'FILENAME')) . DIR_SEP;
						$valueold = $value; //no ref
						$new = Path::Get('path:thumbs/models') . DIR_SEP . strtolower(Input::clean($value,'FILENAME')) . DIR_SEP;
						$value = $valueold;

						//try to rename the existing thumbnail directory

						//move to subfolder must be okay if slug contains multiple '/'
						//so create the directory first
						if (!is_dir($new)){

							if(!@rename($old, $new))
							{
									$status = false;
									$return = '';
									trigger_error($translate->_('Could not rename directory'), E_USER_ERROR);
							}
							else
							{
									//write data to database
									//(only if move was successful)
									$status = $db->Update("UPDATE `bx_content` SET `slug`=? WHERE `id`=?", array(
									$value, $id));


									//update thumbnails as well!

									//get old thumbnails
									$thumbnails = $db->FetchAll("SELECT * FROM `bx_thumbnail` WHERE `content_id`=?", array($id));

									if($thumbnails)
									{
										//replace the path with new path
//										$type = $thumbnails[0]['type'];

										$relpath = dirname($thumbnails[0]['path']);
										$newrelpath = Path::Get('rel:thumbs/models') . '/' .
										strtolower(Input::clean($value,'FILENAME'));

										if($newrelpath != $relpath)
										{
											$keys = array_keys($thumbnails[0]);

											foreach($thumbnails as $thumb)
											{
												//replace path
												$thumb['path'] = $newrelpath . '/' . basename($thumb['path']);
												//this is all that's replaced
												//the rest is written to db from the original values

												//remove associative indexes
												$tt = array();
												foreach($thumb as $t)
													$tt[] = $t;

												//add to sql
												$sql .= $db->Prepare(
														"(".implode(',', array_fill(0, count($keys), '?')).'),',
													$tt
													);

											}
											$sql = rtrim($sql, ',');

											if(!empty($sql)) //safety
											{
												$sql = "INSERT INTO `bx_thumbnail` (`".implode('`,`', $keys)."`) VALUES ".$sql;

												//make room for new thumbnails
												$db->Update("DELETE FROM `bx_thumbnail` WHERE `content_id`=?", array($id));

												//insert new thumbnails
												$db->Update($sql);
												unset($sql);
											}
										}
									}

							}
						}
						else
						{
								if($old != $new) //check to see if not old slug was submitted.
								{
									$status = false;
									$return = '';
									trigger_error("Slug already exists", E_USER_ERROR);
								}
								else
								{
									$status = true;
								}
						}
					}
					else //title
					{
						//write data to database
						$status = $db->Update("UPDATE `bx_content` SET # = ? WHERE `id` = ?", array(
						$columnname, $value, $id));
					}
				}
				elseif($columnname == 'birthdate')
				{
					//if birthdate: update the 2257 record
					$status = $db->Update("UPDATE `bx_2257` SET `birthdate` = ? WHERE `content_id` = ?", array($value, $id));
				}

				if($status)
				{
					$return = $value;
				}

			} //if variables

		} //models


		/***************/
		/*** updates ***/
		/***************/
		elseif($input[TYPE] == 'updates')
		{
			$columnname = Datatable::GetColumn($column, $table); //column name
			$table = Datatable::GetTable($table); // (internal table name)

			if(!empty($value) && !empty($table) && !empty($columnname) && !empty($id))
			{
				if($columnname == 'title' || $columnname == 'slug')
				{
					//if slug: move thumbnail directories
					if($columnname == 'slug')
					{
						$oldslug = $db->Row("SELECT `slug`,`type` FROM `bx_content` WHERE `id`=?",array($id));

						if(!$oldslug) die("Invalid Content ID.");

						//slugs are lower case
						$value = strtolower($value);

						//internal representations of slugs
						$old = Path::Get("path:thumbs/$oldslug[type]") . DIR_SEP . strtolower(Input::clean($oldslug['slug'],'FILENAME')) . DIR_SEP;
						$valueold = $value; //no ref
						$new = Path::Get("path:thumbs/$oldslug[type]") . DIR_SEP . strtolower(Input::clean($value,'FILENAME')) . DIR_SEP;
						$value = $valueold;

						//free dirs
						$oldfree = Path::Get("path:free/$oldslug[type]") . DIR_SEP . strtolower(Input::clean($oldslug['slug'],'FILENAME')) . DIR_SEP;
						$newfree = Path::Get("path:free/$oldslug[type]") . DIR_SEP . strtolower(Input::clean($value,'FILENAME')) . DIR_SEP;
						$value = $valueold;

						//try to rename the existing thumbnail directory

						//move to subfolder must be okay if slug contains multiple '/'
						//so create the directory first
						if (!is_dir($new)){

							if(!@rename($old, $new))
							{
									$status = false;
									$return = '';
									trigger_error($translate->_('Could not rename directory'), E_USER_ERROR);
							}
							else
							{
									//write data to database
									//(only if move was successful)
									$status = $db->Update("UPDATE `bx_content` SET # = ? WHERE `id` = ?", array(
									$columnname, $value, $id));
							}
						}
						else
						{
								if($old != $new) //check to see if not old slug was submitted.
								{
									$status = false;
									$return = '';
									trigger_error("Slug already exists", E_USER_ERROR);
								}
								else
								{
									$status = true;
								}
						}

						//try to move the free thumbs folder if it exists
						if(!is_dir($newfree))
							@rename($oldfree, $newfree);

					}
					else	//everything but the slug
					{
						//write data to database
						$status = $db->Update("UPDATE `bx_content` SET # = ? WHERE `id` = ?", array(
						$columnname, $value, $id));
					}
				}
				elseif($columnname == 'dateline')
				{
					$status = $db->Update("UPDATE `bx_content` SET `dateline` = UNIX_TIMESTAMP(?) WHERE `id` = ?", array($value, $id));

					$return = $value;
				}

				if($status)
				{
					$return = $value;
				}

			} //if variables

		} //updates


		/****************/
		/*** comments ***/
		/****************/
		elseif($input[TYPE] == 'comments')
		{
			$columnname = Datatable::GetColumn($column, $table); //column name

			if(!is_null($value) && !empty($columnname) && !empty($id))
			{
				if($columnname == 'status')
				{
					if($value == 'queued' || $value == 'approved' || $value == 'spam')
					{
						$status = $db->Update("UPDATE `bx_comment` SET #=? WHERE `id`=?", array($columnname,$value,$id));
					}
					else
					{
						$status = false;
					}
				}
				else
				{
					$status = $db->Update("UPDATE `bx_comment` SET #=? WHERE `id`=?", array($columnname,$value,$id));
				}

				if($status)
				{
					$return = $value;
				}
			}

		} //comments

		/***************/
		/*** ratings ***/
		/***************/
		elseif($input[TYPE] == 'ratings')
		{
			$columnname = Datatable::GetColumn($column, $table); //column name

			if(!is_null($value) && !empty($id))
			{
				//check if rating is in allowed range
				$range = Config::Get('rating');

				if($value >= $range['min'] && $value <= $range['max'])
				{
					$status = $db->Update("UPDATE `bx_rating` SET `rating`=? WHERE `id`=?", array($value,$id));
					$return = intval($value);
				}
				else
				{
					$status = false;
					$return = '';
					trigger_error('Rating is out of limits', E_USER_WARNING);
				}
			}

		} //ratings

		/************/
		/*** tags ***/
		/************/
		elseif($input[TYPE] == 'tags')
		{

			$columnname = Datatable::GetColumn($column, $table); //column name
			$id = intval($_POST['id']);

			if(!is_null($value) && !empty($id))
			{

				if($db->Update("UPDATE `bx_tag` SET #=? WHERE `id`=?", array($columnname,$value,$id)))
				{
					$return = $value;
				}

			}

		} //tags

		/***************/
		/*** members ***/
		/***************/
		elseif($input[TYPE] == 'members')
		{
			$columnname = Datatable::GetColumn($column, $table); //column name
			$id = intval($_POST['id']);

			if(!is_null($value) && !empty($id))
			{
				if($columnname == 'email')
				{
					if(!String::IsEmail($value))
					{
						die($translate->_("Error").": ".$translate->_("Not a valid email").PHP_EOL.
						$value
						);
					}
					else
					{
						$emailid = $db->Column("SELECT `email_id` FROM `bx_member` WHERE `id`=?", array($id));

						if($db->Update("UPDATE `bx_member_email` SET `email`=? WHERE `id`=?", array(
						$value,$emailid)))
						{
							$return = $value;
						}
					}
				}
				else
				{
					if($db->Update("UPDATE `bx_member` SET #=? WHERE `id`=?", array($columnname,$value,$id)))
					{
						$return = $value;
					}
				}
			}

		} //members

	} //isset $input[TYPE]


	/***OUTPUT***/
	echo $return;
	exit;

} //auth