<?php

/* **************************************************************
 *  File: datatables.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*** include the init.php file ***/
require '../../_init.php';

//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
error_reporting(0);

		/*--------------------------------------------------------------*/
		/*						Authentification						*/
		/*--------------------------------------------------------------*/
//				Authentification::check();
				if(!Authentification::Login())
				{
					echo Authentification::GetError();
					die();
				}
		/*--------------------------------------------------------------*/

$translate = new Translator("admin");
Zend_Registry::set('translate', $translate);

if(@$_POST['securitytoken'] !== Session::GetToken())
	die($translate->_('Security token mismatch'));

//the internal id of the preview image that is shown in the admin backend
//default: 0
define("__PREVIEWIMG", 0);

define("SITEURL", rtrim(Path::Get('url:site'), '/'));

define("DATE_STRING", Config::Get('date_string'));
//define("DATETIME_STRING", Config::Get('datetime_string'));

define('RATING_PRECISION', Config::Get('rating_decimal'));

define('FRONTEND_THEME', Config::GetDBOptions('frontend_theme'));

/***clean input***/
//$_POST = array_map('trim', $_POST);
$_POST = Input::clean($_POST, 'NOHTML');


class DataQuery
{
	private static $quick_variable_check = "There are missing values.";

	const ACTION = "action";
	const ANSWER = "answer";
	const BIRTHDATE = "birthdate";
	const CATEGORY = "category";
	const COMMENT = "comment";
	const CONTENTID = "contentid";
	const DATELINE = "dateline";
	const DESCRIPTION = "description";
	const EMAIL = "email";
	const EXPIRATION_DATE = "expiration_date";
	const HOST = "host";
	const ID = "id";
	const INFO = "info";
	const IP = "IP";
	const JOIN_DATE = "join_date";
	const KARMA = "karma";
	const LAST_LOGIN = "last_login";
	const MODELNAME = "title";
	const MODELID = "modelid";
	const PATH = "path";
	const QUESTION = "question";
	const RATING = "rating";
	const SLUG = "slug";
	const STATUS = "status";
	const TAG = "tag";
	const TITLE = "title";
	const TYPE = "type";
	const UPDATEID = "updateid";
	const USERNAME = "username";
	const PARENT_ID = "parent_id";

	private static $table = '';
	private static $rResult = array();

	private static $mandatory = array();
	private static $rowkeycache = array();

	private static $iTotal = 0;
	private static $iFilteredTotal = 0;


	private static function addslashes($str)
	{
		return addcslashes($str, '"\\');
	}

	public function Run()
	{
		$translate = Zend_Registry::get('translate');

		/***database connect***/
		$db = DB::getInstance();

		/***table type***/
		self::$table = Datatable::GetTable($_POST['table']); //translate post data into real table name ("bx_...")

		/* Paging */
		$sLimit = "";
		if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".intval( $_POST['iDisplayLength'] );
		}

		/* Ordering */
		$sOrder = "";
		if (isset($_POST['iSortCol_0']))
		{
			$sOrder = "ORDER BY ";
			for ( $i=0, $s = intval($_POST['iSortingCols']); $i<$s; $i++ )
			{
				$sOrder .= Datatable::GetColumn($_POST['iSortCol_'.$i], $_POST['table'])
				." ".( strtolower($_POST['sSortDir_'.$i]) == 'desc'? 'DESC' : 'ASC' ) .", ";
			}
			$sOrder = rtrim($sOrder, ", ");
		}

		/* Search Filtering */
		$sWhere = "";
		/************/
		if ($_POST['sSearch'] != "")
		{
			$sSearch = mysql_escape_string(Input::clean($_POST['sSearch'], 'STR'));

			switch ($_POST['table'])
			{
				case 'tags':
					$sWhere = " WHERE (`tag` LIKE '%".$sSearch."%' OR
							`description` LIKE '%".$sSearch."%' OR
							`id` LIKE '".$sSearch."')";
/*
					$sWhere = "WHERE MATCH(`tag`) AGAINST ('".$sSearch."')";
*/
				break;

				case 'faq':
					$sWhere = " AND (f.question LIKE '%".$sSearch."%' OR
							f.answer LIKE '%".$sSearch."%' OR
							c.name LIKE '%".$sSearch."%' OR
							f.email LIKE '%".$sSearch."%')";
				break;

				case 'comments':
					$sWhere = " WHERE (c.comment LIKE '%".$sSearch."%' OR
							u.id LIKE '%".$sSearch."%')";
				break;

				case 'members':
					$sWhere = " AND (m.username LIKE '".$sSearch."%' OR
							s.IP LIKE '".$sSearch."%' OR
							e.email LIKE '%".$sSearch."%')";
				break;

				case 'ratings':
					$sWhere = " AND (r.rating LIKE '".$sSearch."%' OR
							`content_id` = '".$sSearch."' OR
							r.IP LIKE '".$sSearch."%')";
				break;

				case 'model':
					$sWhere = " AND (m.title LIKE '%".$sSearch."%' OR
							m.slug LIKE '%".$sSearch."%')";
				break;

				case 'updates':
					$sWhere = " AND (u.title LIKE '%".$sSearch."%' OR
							u.id = '".$sSearch."' OR
							u.slug LIKE '%".$sSearch."%')";
				break;

				case 'login_history':
					$sWhere = " AND (`username` LIKE '%".$sSearch."%' OR
							`IP` LIKE '%".$sSearch."%' OR
							`status` LIKE '%".$sSearch."%' OR
							`dateline` LIKE '%".$sSearch."%')";
				break;

				case '2257docs':
					$sWhere = " AND (`real_name` LIKE '%".$sSearch."%' OR
							`aliases` LIKE '%".$sSearch."%' OR
							`gender` LIKE '%".$sSearch."%' OR
							`passport_id` LIKE '%".$sSearch."%' OR
							`notes` LIKE '%".$sSearch."%' OR
							`birthdate` LIKE '%".$sSearch."%')";
				break;

				case 'activity_log':
					$sWhere = " AND (`username` LIKE '%".$sSearch."%' OR
							`IP` LIKE '%".$sSearch."%' OR
							`info` LIKE '%".$sSearch."%')";
				break;

			} //switch
		} //search


		/************/
		$sQuery = "";
		switch($_POST['table'])
		{
			case 'tags':
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				`id`, `tag`, `description`
				FROM `bx_tag`
					$sWhere
					$sOrder
					$sLimit";
				break;
			/***************/
			case 'ratings':
				$sQuery = "SELECT DISTINCT SQL_CALC_FOUND_ROWS
				r.id, r.rating, r.dateline, r.picture_id, r.content_id, r.IP,
				r.content_id AS `contentid`,
				m.username,
				c.type, c.slug,
				th.path
				FROM `bx_rating` AS `r`
				LEFT JOIN `bx_thumbnail` AS `th` USING (`content_id`)
				LEFT JOIN `bx_content` AS `c` ON (c.id = r.content_id)
				LEFt JOIN `bx_member` AS `m` ON (m.id = r.member_id)
				WHERE th.internal_id = '".__PREVIEWIMG."'
				AND th.theme = '".FRONTEND_THEME."'
					$sWhere
					$sOrder
					$sLimit";
				break;
			/***************/
			case 'faq':
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				f.id, f.question, f.answer, f.username, f.email, f.dateline, f.status,
				c.name AS `category`
				FROM `bx_faq` AS `f` LEFT JOIN `bx_faq_category` AS `c` ON (c.id = f.category_id)
					$sWhere
					$sOrder
					$sLimit";
				break;
			/***************/
			case 'comments':

				//use hierarchical comments
				$nested = Config::GetDBOptions('nested_comments');
				if($nested)
				{
					$sQuery = "SELECT SQL_CALC_FOUND_ROWS
					c.id, c.comment, c.status, c.IP, c.karma, c.parent_id, c.dateline,
					c.username,
					th.path,
					u.slug, u.type, u.id AS `updateid`
					FROM `bx_comment` AS `c`
					LEFT JOIN `bx_content` AS `u` ON (u.id = c.content_id)
					LEFT JOIN `bx_thumbnail` AS `th` ON (c.content_id = th.content_id AND th.internal_id = '".__PREVIEWIMG."' AND th.theme = '".FRONTEND_THEME."')
						$sWhere
						$sOrder
						$sLimit";














				}
				else
				{
					$sQuery = "SELECT SQL_CALC_FOUND_ROWS
					c.id, c.comment, c.status, c.IP, c.karma, c.parent_id, c.dateline,
					c.username,
					th.path,
					u.slug, u.type, u.id AS `updateid`
					FROM `bx_comment` AS `c`
					LEFT JOIN `bx_content` AS `u` ON (u.id = c.content_id)
					LEFT JOIN `bx_thumbnail` AS `th` ON (c.content_id = th.content_id AND th.internal_id = '".__PREVIEWIMG."' AND th.theme = '".FRONTEND_THEME."')
						$sWhere
						$sOrder
						$sLimit";
				}
				break;
			/***************/
			case 'model':
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				m.id, m.title, m.slug,
				th.path, AVG(r.rating) AS `rating`,
				f.gender,
				COUNT(c.id) AS `comments`
				FROM `bx_content` AS `m`
				LEFT JOIN `bx_rating` AS `r` ON (r.content_id = m.id)
				LEFT JOIN `bx_2257`   AS `f` ON (f.content_id = m.id)
				LEFT JOIN `bx_thumbnail` AS `th` ON (th.content_id = m.id AND th.internal_id = '".__PREVIEWIMG."' AND th.theme = '".FRONTEND_THEME."')
				LEFT JOIN `bx_comment` AS `c` ON (c.content_id = m.id)
				WHERE m.type = 'model'
					$sWhere
					GROUP BY m.id
					$sOrder
					$sLimit";
				break;
			/***************/
			case 'updates':
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				u.id, u.title, u.slug, u.dateline, u.type,
				th.path,
				COUNT(p.id) AS `count`
				FROM `bx_content` AS `u`
				LEFT JOIN `bx_thumbnail` AS `th` ON (th.content_id = u.id AND th.internal_id = '".__PREVIEWIMG."' AND th.theme = '".FRONTEND_THEME."')
				LEFT JOIN `bx_picture` AS `p` ON (p.content_id = u.id AND p.theme = '".FRONTEND_THEME."')
				WHERE u.type != 'model'
					$sWhere
					GROUP BY u.id
					$sOrder
					$sLimit";
				break;
			/***************/
			case 'members':
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				m.id, m.username, m.join_date, m.birthdate,
				j.status, j.signup_IP, j.expiration_date,
				e.email,
				s.dateline, s.IP
				FROM `bx_member_email` as `e`,
				`bx_member` as `m`
				LEFT JOIN `bx_join` AS `j` ON (j.member_id = m.id)
				LEFT JOIN `bx_member_session` AS `s` ON (s.member_id = m.id)
				WHERE e.id = m.email_id
					$sWhere
					$sOrder
					$sLimit";
				break;
			/***************/
			case 'login_history':
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				`username`, `action`,`IP`,`info`, `dateline`
				FROM `bx_administrator_activitylog`
				WHERE `action` IN ('login', 'failed login', 'logout')
					$sWhere
					$sOrder
					$sLimit";
				break;
			/***************/
			case 'activity_log':
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				`username`, `action`, `info`, `IP`, `dateline`
				FROM `bx_administrator_activitylog`
				WHERE `action` NOT IN ('login', 'failed login', 'logout')
					$sWhere
					$sOrder
					$sLimit";
				break;
			/***************/
			case '2257docs':
				//query 1: models
				$sQuery = "SELECT SQL_CALC_FOUND_ROWS
				f.id, f.content_id AS `modelid`,
				f.real_name AS `realname`, f.aliases, f.birthdate, f.gender, f.passport_id, f.identification_path, f.misc_url,  f.notes, f.dateline,
				u.title, u.type,
				l.location, c.country
				FROM `bx_2257` AS `f`
				LEFT JOIN `bx_content` AS `u` ON (u.id = f.content_id)
				LEFT JOIN `bx_location` AS `l` ON (l.id = f.location_id)
				LEFT JOIN `bx_country` AS `c` ON (c.iso = l.country_iso)
				WHERE f.parent IS NULL
					$sWhere
					$sOrder
					$sLimit";
				break;

		}//switch table

		//output
		self::$rResult = $db->FetchAll($sQuery);

		/***clean output***/
		foreach(array_keys(self::$rResult) as $key)
			self::$rResult[$key] = Input::clean(self::$rResult[$key], 'NOHTML');

		self::$iFilteredTotal = $db->Column("SELECT FOUND_ROWS()");

		//get total
		$sQuery = "SELECT COUNT(*) FROM `".self::$table."`";
		if($_POST['table']=='model')
			$sQuery .= " WHERE `type` = 'model'";
		elseif($_POST['table']=='updates')
			$sQuery .= " WHERE `type` != 'model'";

		self::$iTotal = $db->Column($sQuery);

	} //Run


	public function output ()
	{
		$translate = Zend_Registry::get('translate');

		/***database connect***/
		$db = DB::getInstance();

		/***output***/
		$sOutput = array();

		//header data
		$sOutput['sEcho'] = intval($_POST['sEcho']);
		$sOutput['iTotalRecords'] = self::$iTotal;
		$sOutput['iTotalDisplayRecords'] = self::$iFilteredTotal;

		$sOutput['aaData'] = array();
		$t = array();

		/************/
		switch($_POST['table'])
		{

		/**************/
		//	  TAG	  //
		/**************/
			case 'tags':
				foreach ( self::$rResult as $Row )
				{
					$Row[self::ID] = intval($Row[self::ID]);

					$t = array();

					//0 icon
						$t[] = '';
					//1 id
						$t[] = $Row[self::ID];
					//2 tag
						$t[] = self::addslashes($Row[self::TAG]);
					//3 description
						$t[] = self::addslashes($Row[self::DESCRIPTION]);
					//4 checkbox
						$t[] = '';

					$sOutput['aaData'][] = $t;
				}
			break;
		/**************/
		//	 COMMENT  //
		/**************/
			case 'comments':
				foreach ( self::$rResult as $Row )
				{
					if(!String::IsIPAddress($Row[self::IP])) $Row[self::IP] = "";

					$t = array();

					//0 icon
							if (!$Row[self::ID] OR !$Row[self::COMMENT] OR !$Row[self::STATUS] OR !$Row[self::DATELINE] OR !$Row[self::USERNAME])
							{
								$icon = 0;
							}
							else
								if ($Row[self::STATUS] != 'approved')
								{
									$icon = 'approve';
								}else{
									$icon = 'success';
								}
						$t[] = $icon;
					//1 id
						$t[] = intval($Row[self::ID]);
					//2 parent_id
						$t[] = intval($Row[self::PARENT_ID]);
					//3 updateid
						$t[] = intval($Row['updateid']);
					//4 dateline
						$t[] = String::convert_dateline($Row[self::DATELINE]);
					//5 comment
						$t[] = self::addslashes($Row[self::COMMENT]);
					//6 status
						$t[] = $Row[self::STATUS];
					//7 karma
						$t[] = $Row[self::KARMA];
					//8 name
						$t[] = self::addslashes($Row[self::USERNAME]);
					//9 IP
						$t[] = self::addslashes($Row[self::IP]);
		/*
					//10 host
						$sOutput .= LINE_START.$Row[self::HOST].LINE_END;
		*/
					//10 update
							if($Row[self::TYPE] == 'video')
								$page = Path::Get('url:site/video');
							elseif($Row[self::TYPE] == 'set')
								$page = Path::Get('url:site/set');
							else
								$page = Path::Get('url:site/model');

						$t[] = self::addslashes($page.'/'.$Row[self::SLUG]);
					//11 preview
						$t[] = self::addslashes( ($Row[self::PATH] ? $Row[self::PATH] : 0) );
					//12 edit
						$t[] = '';
					//13 reply
						$t[] = '';
					//14 checkbox
						$t[] = '';

					//check if this a nested comment
					if(Config::GetDBOptions('nested_comments') && $Row[self::PARENT_ID] > 0)
					{
						//if it has a parent_id, add it to
						//the additional_rows array instead of aaData
//						$t['nested'] = 1;
						$sOutput['additional_rows'][$Row[self::PARENT_ID]][] = $t;
					}
					else
					{
						//add it to regular output
						$sOutput['aaData'][] = $t;
					}
				}
			break;
		/**************/
		//	 RATING   //
		/**************/
			case 'ratings':
				foreach ( self::$rResult as $Row )
				{
					if(!String::IsIPAddress($Row[self::IP])) $Row[self::IP] = "";

					$t = array();

					//0 icon
						$t[] = '';
					//1 id (hidden)
						$t[] = $Row[self::ID];
					//2 slug (hidden)
						$t[] = self::addslashes($Row[self::SLUG]);
					//3 rating
						$t[] = $Row[self::RATING];
					//4 username
						$t[] = self::addslashes($Row[self::USERNAME]);
					//5 IP
						$t[] = $Row[self::IP];
					//6 dateline
						$t[] = String::convert_dateline($Row[self::DATELINE]);
					//7 content-id
						$t[] = $Row[self::CONTENTID];
					//8 type
						$t[] = $Row[self::TYPE];
					//9 preview
						$t[] = self::addslashes( ($Row[self::PATH] ? $Row[self::PATH] : 0) );
					//10 edit
						$t[] = ($Row[self::TYPE] == 'model' ? "model" : "update");
					//11 checkbox
						$t[] = '';

					$sOutput['aaData'][] = $t;
				}
			break;
		/**************/
		//	   FAQ	  //
		/**************/
			case 'faq':
				foreach ( self::$rResult as $Row )
				{
					$t = array();

					//0 icon
							$icon = '<a href="javascript:void(0)" onclick="javascript:send('.$Row[self::ID].');"><img src="'.Path::Get('url:admin').'/img/icons/cross.png" alt="" width="16" height="16" title="'.$translate->_('Delete Tag').'" border="0"></a>';
						$t[] = self::addslashes($icon);
					//1 id
						$t[] = $Row[self::ID];
					//2 status
							if($Row[self::STATUS] == 'approved')
								{
									$status = '<a href="javascript:void(0)" onclick="javascript:approveitem('.$Row[self::ID].',false);"><img src="'.Path::Get('url:admin').'/img/icons/lightbulb.png" id="icon'.$Row[self::ID].'" width="16" height="16" alt="'.$translate->_("Deactivate").'" title="'.$translate->_("Deactivate Item").'" border="0"></a>';
								}
							else
								{
									$status = '<a href="javascript:void(0)" onclick="javascript:approveitem('.$Row[self::ID].',true);"><img src="'.Path::Get('url:admin').'/img/icons/lightbulb_off.png" id="icon'.$Row[self::ID].'" width="16" height="16" alt="'.$translate->_("Approve").'" title="'.$translate->_("Approve Item").'" border="0"></a>';
							}
						$t[] = self::addslashes($status);
					//3 category
						$t[] = $Row[self::CATEGORY];
					//4 question
						$t[] = self::addslashes($Row[self::QUESTION]);
					//5 answer
						$t[] = self::addslashes($Row[self::ANSWER]);
					//6 name
						$t[] = self::addslashes($Row[self::USERNAME]);
					//7 email
						$t[] = $Row[self::EMAIL];
					//8 date
						$t[] = String::convert_dateline($Row[self::DATELINE]);
					//9 checkbox
						$checkboxhtml = '<input type="checkbox" name="checkbox[]" id="checkbox'.$Row[self::ID].'" value="'.$Row[self::ID].'">';
						$t[] = self::addslashes($checkboxhtml);

					$sOutput['aaData'][] = $t;
				}
			break;
		/**************/
		//	 UPDATES  //
		/**************/
			case 'updates':

				self::$mandatory = Config::Get('mandatory_update');

				foreach ( self::$rResult as $Row )
				{
					$t = array();

					//0 icon
						//mark future updates
						if (intval($Row[self::DATELINE]) > DB::UNIXNOW())
							$futureupdate = 1;
						else
							$futureupdate = 0;

						$icon = '';
						if(empty(self::$rowkeycache)) self::$rowkeycache = array_keys($Row);
						foreach(self::$mandatory as $m)
						{
							if(in_array($m, self::$rowkeycache) && empty($Row[$m]))
							{
								$icon = 0;
								break;
							}
						}

/*
						if (!$Row[self::ID] OR !$Row[self::TITLE] OR !$Row[self::SLUG] OR !$Row[self::DATELINE] OR !$Row[self::TYPE] OR !$Row[self::PATH])
						{
							$icon = 0;
						}
*/
						if ($futureupdate == 1)
						{
							$icon = 1;
						}else{
							$icon = '';
						}
						$t[] = $icon;
					//1 id
						$t[] = $Row[self::ID];
					//2 title
						$t[] = self::addslashes($Row[self::TITLE]);
					//3 slug
						$t[] = self::addslashes($Row[self::SLUG]);
					//4 date
						$t[] = String::convert_dateline($Row[self::DATELINE]);
					//5 type
							$typeimg = '';
							if($Row[self::TYPE] == 'set')
							{
								$typeimg = $Row['count'] . " pics";
							}
							if($Row[self::TYPE] == 'video')
							{
								if($Row['count'] > 1)
									$typeimg = $Row['count']." videos";
								else
									$typeimg = "1 video";
							}
						$t[] = self::addslashes($typeimg);
					//6 link
						if ($Row[self::TYPE] == 'video')
							$utype = Path::Get('url:site/video');
						elseif ($Row[self::TYPE] == 'set')
							$utype = Path::Get('url:site/set');
						else
							continue;
						$link = $utype.'/'.$Row[self::SLUG];

						$t[] = self::addslashes($link);
					//7 edit
							if($Row[self::TYPE] != 'video')
								$edit = 1; //shows the additional pictureset-edit-button
							else
								$edit = '';

						$t[] = $edit;
					//8 preview
							if ($Row[self::PATH])
								$preview = $Row[self::PATH];
							else //no picture
								$preview = '';
							if($preview && !file_exists(Path::Get('path:site') . String::Slash($Row[self::PATH],1,0)))
								$preview = 0;
						$t[] = self::addslashes($preview);
					//9 checkbox
						$t[] = '';

					$sOutput['aaData'][] = $t;
				}
			break;
		/**************/
		//	 MODEL	  //
		/**************/
			case 'model':

				self::$mandatory = Config::Get('mandatory_model');

				foreach ( self::$rResult as $Row )
				{
					$t = array();

					//0 icon
						//only show the warning icon if mandatory check fails
						$icon = 1;
						if(empty(self::$rowkeycache)) self::$rowkeycache = array_keys($Row);
						foreach(self::$mandatory as $m)
						{
							if(in_array($m, self::$rowkeycache) && empty($Row[$m]))
							{
								$icon = 0;
								break;
							}
						}
/*
						if (!$Row[self::PATH] OR !$Row[self::BIRTHDATE] OR !$Row[self::MODELNAME] OR !$Row[self::SLUG] OR !$Row[self::BIRTHDATE])
						{
							$icon = 0;
						}else{
							$icon = 1;
						}
*/
						$t[] = $icon;
					//1 id (hidden column)
						$t[] = $Row[self::ID];
					//2 modelname (stored in title but renamed to modelname)
						$t[] = self::addslashes($Row[self::MODELNAME]);
					//3 slug
						$t[] = self::addslashes($Row[self::SLUG]);
					//4 rating
						$t[] = round($Row[self::RATING], RATING_PRECISION);
					//5 number of comments
						$t[] = $Row['comments'];
					//6 link
						$t[] = '';
					//7 edit
						$t[] = '';
					//8 preview
						if ($Row[self::PATH]){
							$preview = $Row[self::PATH];
						}else{ //no picture
							$preview = 0;
						}
						if($preview && !file_exists(Path::Get('path:site') . String::Slash($Row[self::PATH],1,0)))
							$preview = 0;
						$t[] = self::addslashes($preview);
					//9 checkbox
						$t[] = '';

					$sOutput['aaData'][] = $t;
				}
			break;
		/***************/
		//	 MEMBERS   //
		/***************/
			case 'members':
				foreach ( self::$rResult as $Row )
				{
					$t = array();

					if(!String::IsIPAddress($Row[self::IP]))
						$Row[self::IP] = (!empty($Row['signup_IP']) ? $Row['signup_IP'] : " ");

					//0 status icon
						$t[] = $Row[self::STATUS];
					//1 id
						$t[] = $Row[self::ID];
					//2 username
						$t[] = self::addslashes($Row[self::USERNAME]);
					//3 email
						$t[] = $Row[self::EMAIL];
					//4 join_date
						$t[] = String::convert_dateline($Row[self::JOIN_DATE], DATE_STRING);
					//5 last_login
							if($Row[self::DATELINE])
								$lastlogin = String::convert_dateline($Row[self::DATELINE]);
							else
								$lastlogin = "-";
						$t[] = $lastlogin;
					//6 expiration_date
							if($Row[self::EXPIRATION_DATE])
							{
								$expiration = String::convert_dateline($Row[self::EXPIRATION_DATE]);
							}
							else
								$expiration = " ";
						$t[] = $expiration;
					//7 IP
						$t[] = ( empty($Row[self::IP])?$Row['signup_IP']:$Row[self::IP] );
					//8 edit
						$t[] = '';
					//9 checkbox
						$t[] = '';

					$sOutput['aaData'][] = $t;
				}
			break;
		/**********************/
		//	 LOGIN_HISTORY   //
		/**********************/
			case 'login_history':
				foreach ( self::$rResult as $Row )
				{
					$t = array();

					if(!String::IsIPAddress($Row[self::IP])) $Row[self::IP] = "";

					//1 icon
						$t[] = '';
					//2 username
						$t[] = self::addslashes($Row[self::USERNAME]);
					//4 IP
						$t[] = $Row[self::IP];
					//5 ISP
						$t[] = gethostbyaddr($Row[self::IP]);
					//6 status
						$t[] = ($Row[self::INFO] != 'success' ? self::addslashes('<span class="error">'.$Row[self::INFO].'</span>') : $Row[self::ACTION]);
					//7 last_login
						$t[] = String::convert_dateline($Row[self::DATELINE]);

					$sOutput['aaData'][] = $t;
				}
			break;
		/**********************/
		//	  ACTIVITY_LOG    //
		/**********************/
			case 'activity_log':
				foreach ( self::$rResult as $Row )
				{
					if(!String::IsIPAddress($Row[self::IP])) $Row[self::IP] = "";

//					$Row[self::INFO] = Input::clean($Row[self::INFO],'NOHTML');
//					$Row[self::ACTION] = Input::clean($Row[self::ACTION],'NOHTML');

					$t = array();

					//1 link
						$t[] = '';
					//2 username
						$t[] = self::addslashes($Row[self::USERNAME]);
					//3 action
						$info = '';
						if(($Row[self::ACTION]=='edited model'
							|| $Row[self::ACTION]=='added model')
						&& !empty($Row[self::INFO]))
							$info = '<a href=\'/model/'.$Row[self::INFO].'\'>'.$Row[self::ACTION].'</a>';
						elseif(($Row[self::ACTION]=='edited update'
							|| $Row[self::ACTION]=='added update')
						&& !empty($Row[self::INFO]))
							$info = '<a href=\'/update/'.$Row[self::INFO].'\'>'.$Row[self::ACTION].'</a>';
						elseif($Row[self::ACTION]=='changed options' && !empty($Row[self::INFO]))
							$info = $Row[self::ACTION].": ".$Row[self::INFO];
						else
							$info = $Row[self::ACTION];
						$t[] = self::addslashes($info);
					//5 IP
						$t[] = $Row[self::IP];
					//6 dateline
						$t[] = String::convert_dateline($Row[self::DATELINE]);

					$sOutput['aaData'][] = $t;
				}
			break;
		/**********************/
		//	    2257docs      //
		/**********************/
			case '2257docs':

				$ids = '';
				foreach( self::$rResult as $Row )
				{
					$ids .= $db->Prepare("?,", array($Row[self::ID]));
				}
				$ids = rtrim($ids, ",");

				$update_2257 = array();
				if($ids != '')
				{
					//query 2: update 2257 data
					$update_2257 = $db->FetchAll("SELECT
					f.id,f.parent, f.production_date, f.location_id,
					f.misc_url, f.modelrelease_path,
					f.notes, f.dateline,
					c.title, c.type, c.id AS `id_content`
					FROM `bx_2257` AS `f`
					LEFT JOIN `bx_content` AS `c` ON (f.content_id = c.id)
					WHERE f.parent IN (".$ids.")");
				}


				foreach (self::$rResult as $Row)
				{
					$t = array();

					$_2257info = array();
					//merge the 2257 info into the model array
					foreach($update_2257 as $d)
					{
						if($d['parent'] === $Row['id'])
						{
							$_2257info[] = array(
									'id' => $d['id'],
									'title' => $d['title'],
									'production_date' => $d['production_date'],
									'location_id' => $d['location_id'],
									'notes' => $d['notes'],
									'type' => $d['type'],
									'id_content' => $d['id_content'],
									'dateline' => $d['dateline'],
									);
						}
					}

					//0 icon
						if(!empty($Row[self::TYPE]))
						{
							if($Row[self::TYPE]=='model'){
								if($Row['gender']=='female')
									$icon_type = 'female';
								elseif($Row['gender']=='male')
									$icon_type = 'male';
							}
						}
						else
							$icon_type = 'model';

					//1 model id (see below)

					//2 model
							$content = '<div class="mbot5">';
							if(!empty($Row[self::MODELNAME]))
								$content .= '<a href="'.Path::Get('url:admin').'/model?edit='.$Row[self::MODELID].'" class="modelname">'.$Row[self::MODELNAME].'</a><br>';
							if(!empty($Row['realname']))
								$content .= "Real Name: ".$Row['realname'];
							if(!empty($Row['aliases']))
								$content .= "<br>Aliases: ".$Row['aliases'];
							if(!empty($Row['birthdate']))
								$content .= "<br>Birthdate: ".$Row['birthdate'];
							if(!empty($Row['passport_id']))
								$content .= "<br>Passport-ID: ".$Row['passport_id'];

/*
							if(!empty($Row['identification_path']))
							{
								$content .= '<br><a href="javascript:void(0);" onmouseover="return overlib(\'<a href=\''.$Row['identification_path'].'\'><img src=\''.Path::Get('url:admin').'/img/icons/link.png\' border=\'0\' width=\'16\' height=\'16\'></a> <input type=\'text\' value=\''.$Row['identification_path'].'\'></input>\', FULLHTML, STICKY, MOUSEOFF);" onmouseout="return nd();">Identification</a>';
							}
*/
/*
							if(!empty($Row['misc_url']))
							{
								$content .= '<br><a href="javascript:void(0);" onmouseover="return overlib(\'<a href=\''.$Row['misc_url'].'\'><img src=\''.Path::Get('url:admin').'/img/icons/link.png\' border=\'0\' width=\'16\' height=\'16\'></a> <input type=\'text\' value=\''.$Row['misc_url'].'\'></input>\', FULLHTML, STICKY, MOUSEOFF);" onmouseout="return nd();">Misc Url</a>';
							}
*/
							$content .= '</div>';

							if(!empty($_2257info))
							{
								$_2257 = '';
								foreach($_2257info as $R)
								{

									$_2257 .= '<table width="100%" class="doctable2257" border="0"><tr><td class="col_icon"></td><td class="firstcol2257">';

									$icon = '';
									if($R[self::TYPE]=='set')
									{
										$icon = '<img src="'.Path::Get('url:admin').'/img/icons/images.png" alt="" width="16" height="16" title="picture set" border="0">';
									}
									else
									{
										$icon = '<img src="'.Path::Get('url:admin').'/img/icons/video.png" alt="" width="16" height="16" title="video" border="0">';
									}
									$_2257 .= $icon;

									if(isset($R['title']))
										$_2257 .= ' <a href="'.Path::Get('url:admin').'/update?edit='.$R['id_content'].'" class="nounderline" title="'.$translate->_('Edit Update').'">Title: '.$R['title'].'</a></div>';
									if(isset($R['production_date']))
										$_2257 .= "<br>Production date: ".$R['production_date'];
									if(isset($R['location']))
										$_2257 .= " Location: ".$R['location'];
									if(isset($R['country']))
										$_2257 .= " Country: ".$R['country'];
/*
									if(!empty($R['modelrelease_path']))
									{
										$Row['modelrelease_path'] = addcslashes(self::addslashes($Row['modelrelease_path']),'\\');

										$content .= '<br><a href=\"'.$Row['modelrelease_path'].'\" onmouseover=\"return overlib(\'<a href=\\\\\'#\\\\\'><img src=\\\\\''.Path::Get('url:admin').'/img/icons/link.png\\\\\' border=\\\\\'0\\\\\' width=\\\\\'16\\\\\' height=\\\\\'16\\\\\'></a> <input type=\\\\\'text\\\\\' value=\\\\\''.$Row['modelrelease_path'].'\\\\\'></input>\', FULLHTML, STICKY, MOUSEOFF);\" onmouseout=\"return nd();\">Release</a>';
									}
*/
/*
									if(!empty($R['misc_url']))
									{
										$Row['misc_url'] = addcslashes(self::addslashes($Row['misc_url']),'\\');

										$content .= '<br><a href=\"'.$Row['misc_url'].'\" onmouseover=\"return overlib(\'<a href=\\\\\'#\\\\\'><img src=\\\\\''.Path::Get('url:admin').'/img/icons/link.png\\\\\' border=\\\\\'0\\\\\' width=\\\\\'16\\\\\' height=\\\\\'16\\\\\'></a> <input type=\\\\\'text\\\\\' value=\\\\\''.$Row['misc_url'].'\\\\\'></input>\', FULLHTML, STICKY, MOUSEOFF);\" onmouseout=\"return nd();\">Misc Url</a>';
									}
*/
									if($R['dateline'] > time())
										$_2257 .= "<br>Scheduled for: ".String::convert_dateline($R['dateline']);
									else
										$_2257 .= "<br>Posted on: ".String::convert_dateline($R['dateline']);

									if(!empty($R['notes']))
										$_2257 .= "</td><td><img src='".Path::Get('url:admin')."/img/icons/page_white_magnify.png'> Notes: ".$R['notes'];
									else
										$_2257 .= "</td><td>";

									$_2257 .= '</td><td class="col_edit_long"><a href="'.Path::Get('url:admin').'/update?edit='.$R['id_content'].'" class="nounderline" title="'.$translate->_('Edit Update').'"><img src="'.Path::Get('url:admin').'/img/icons/pencil.png" alt="" width="16" height="16" border="0"></a>';

									$_2257 .= "</td></tr></table>";
								}
							}
							else
								$_2257 = $translate->_('No updates found');

						//1 expand column
						$t[] = (!empty($_2257) ? '1' : '0');

						//3 model id
						$t[] = $Row[self::MODELID];

						//2 gender icon
						$t[] = $icon_type;

//add the updates to additional rows that are expanded on click
//						$content .= $_2257;
						if(!empty($content))
							$sOutput['additional_rows'][] = $_2257;

						$t[] = (!empty($content) ? $content : ' ');

					//3 model notes
						$t[] = (!empty($Row['notes']) ? $Row['notes'] : ' ');
/*
//deletion of 2257 entries can cause integrity problems
					//4 checkbox
						$checkboxhtml = '<input type="checkbox" name="checkbox[]" id="checkbox'.$Row[self::ID].'" value="'.$Row[self::ID].'">';
						$t[] = self::addslashes($checkboxhtml);
*/
					//4 editicon
						$t[] = $Row[self::MODELID];

					$sOutput['aaData'][] = $t;
				}
			break;

		/**********************/
		//   empty response   //
		/**********************/
			default:
				//$sOutput .= OPEN . CLOSE;
			break;

		} //switch

		echo json_encode($sOutput);

		/***disconnect***/
		unset($db);

		exit;

	} //output

} //class DataQuery


$role = Authentification::GetRole();
if('administrator' === $role || 'editor' === $role)
{
	//output
	$dq = new DataQuery;
	$dq->run();
	$dq->output();
}