<?php

/* **************************************************************
 *  File: home.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class home extends BaseController
{
	private static $token = false;

	//limit the results
	CONST LIMIT = 10;

	//padding for numbers
	CONST ZEROPADDING = 6;

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
		$tpl->title = $translate->_("Admin Homepage"); //="Admin Dashboard"
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		$strError = array();
		/***updates***/
			$order = ' ORDER BY `dateline` DESC';
			$updates = $db->FetchAll("SELECT `id`, `title`, `type`,
			`slug`, `dateline`
			FROM `bx_content`
			WHERE `type` != 'model'" .
			$order . " LIMIT ".self::LIMIT);

			if (count($updates) <= 0) {
				$strError['updates'] = $translate->_("No updates found");
			}

			//limit the length of the titles for dashboard display
			for($i = 0, $s = count($updates); $i < $s; $i++)
			{
				$updates[$i]['title'] = String::TrimText($updates[$i]['title'], 45, true);
			}

		/***models***/
			$order = ' ORDER BY `id` DESC';
			$models = $db->FetchAll("SELECT `id`, `title` AS `modelname`,
			`slug`, `type`, `dateline`
			FROM `bx_content`
			WHERE `type` = 'model'" .
			$order . " LIMIT ".self::LIMIT);

			if (count($models) <= 0) {
				$strError['models'] = $translate->_("No models found");
			}

			//limit the length of the modelname for dashboard display
			for($i = 0, $s = count($models); $i < $s; $i++)
			{
				$models[$i]['modelname'] = String::TrimText($models[$i]['modelname'],45,$dots=true);
				//convert dateline to date
				$models[$i]['dateline'] = String::ConvertDatelineToDate($models[$i]['dateline']);
			}

		/***comments***/
			$order = ' ORDER BY `c`.`dateline` DESC';
			$comments = $db->FetchAll("SELECT c.id, c.comment, c.status, c.dateline,
			u.username FROM `bx_comment` AS `c`
			JOIN `bx_member` AS `u` ON (c.member_id = u.id)"
			. $order
			." LIMIT ".self::LIMIT);

			if (count($comments) <= 0) {
				$strError['comments'] = $translate->_("No comments found");
			}

			//limit the length of the comments for the dashboard
			for($i = 0, $s = count($comments); $i < $s; $i++)
			{
				$comments[$i]['comment'] = String::TrimText($comments[$i]['comment'],160,$dots=true);
			}

		/***members***/
			$order = ' ORDER BY `id` DESC';
			$members = $db->FetchAll("SELECT `id`, `username`, `status`
							FROM `bx_member`"
							.$order . "
							LIMIT ".self::LIMIT);

			if (count($members) <= 0) {
				$strError['members'] = $translate->_("No members found");
			}

		/***add leading zeros for nicer display***/
//		$updates 	= Arr::addArrayPadding($updates,'id', self::ZEROPADDING);
//		$comments 	= Arr::addArrayPadding($comments,'id', self::ZEROPADDING);
//		$models 	= Arr::addArrayPadding($models,'id', self::ZEROPADDING);
		$members 	= Arr::addArrayPadding($members,'id', self::ZEROPADDING);


		/***quick statistics:***/
			$num = $db->Row("SELECT count(c1.id) AS `picture`,
						count(c2.id) AS `video`,
						count(c3.id) AS `model`,
						count(co.id) AS `comment`,
						count(t.id) AS `tag`
					FROM `bx_content` AS `c1`,
						`bx_content` AS `c2`,
						`bx_content` AS `c3`,
						`bx_comment` AS `co`,
						`bx_tag` AS `t`
					WHERE c1.type = ?
					AND c2.type = ?
					AND c3.type = ?",
					array(
						'set',
						'video',
						'model',
					));
			$mem = $db->Row("SELECT count(m.id) AS `member`
					FROM `bx_member` AS `m`
					WHERE m.status = 'active'");

			$num = $num + $mem;

		/***disconnect***/
		unset($db);


		/***RSS News Feed***/
		$feedurl = 'http://buxomcms.com/feed/bcmsnews.xml';
		@require_once "Zend/Feed/Rss.php";
		$__newestversion = false;
		if(class_exists('Zend_Feed'))
		{
			//caching:
			$cacheId = 'news_feed';
			$feedError = false;
			$cache = Caching::Setup('element', true, 86400); //with serialization, cache for 24 hours
			if(!$feed = $cache->load($cacheId)) {

				try {
					if(defined(__VERSION))
						$feed = Zend_Feed::import($feedurl.'?version='.__VERSION);
					else
						$feed = Zend_Feed::import($feedurl);
				} catch (Exception $e) {
					$feedError = true;
				}

				if(!$feedError && is_object($feed))
				{
					$cache->save($feed, $cacheId, array('_dashboardfeed','_dashboardnews'));

					$feed->__wakeup();
				}
			}

			$rss = array();
			if (!empty($feed) && $feed->count())
			{
				$rss['title'] = $feed->title();

				$__newestversion = $feed->generator();
				if ($__newestversion == '') $__newestversion = false;

				foreach ($feed as $ci) {
				    $item = "<strong>".$ci->title() . "</strong><br>";
				    //$item .= $ci->description() . "<br>";
				    $item .= "<span class='left mbot5'><a href='$ci->link'>View</a></span>";
				    $date = date(Config::Get('date_string'), strtotime($ci->pubDate()));
				    $item .= "<span class='right mbot5'>".$date."</span>";
					$item .= "<hr class='c mbot5' />".PHP_EOL;
					$rss['items'][] = $item;
				}
			} //feed->count
		}
		/***RSS News Feed***/


		/***TEMPLATE ASSIGNMENTS***/

		//version
		if (defined('__VERSION') && $__newestversion)
		{
			$tpl->assign("__VERSION", __VERSION);
			$tpl->assign("__newestversion", $__newestversion);
			if((string) __VERSION != (string) $__newestversion)
				$tpl->assign("update_available", true);
			else
				$tpl->assign("update_available", false);
		}

		//rss news feed
		if (isset($rss) && !$feedError) $tpl->assign("feed", $rss);

		//content
		if (isset($updates)) $tpl->assign("updates", $updates);
		if (isset($models))  $tpl->assign("models", $models);
		if (isset($comments)) $tpl->assign("comments", $comments);
		if (isset($members)) $tpl->assign("members", $members);

		//quick statistics
		if(isset($num)) $tpl->assign("num", $num);

		//error message
		if(isset($strError)) $tpl->errormessage = $strError;

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"activemembers" => "Active Members",
			"approvecomment" => "Approve Comment",
			"approved" => "Approved",
			"comments" => "Comments",
			"editcomment" => "Edit Comment",
			"editcomments" => "Edit Comments",
			"editmembers" => "Edit Members",
			"editmodel" => "Edit Model",
			"editmodels" => "Edit Models",
			"edittags" => "Edit Tags",
			"editupdate" => "Edit Update",
			"editupdates" => "Edit Updates",
			"id" => "ID",
			"markasspam" => "Mark as Spam",
			"members" => "Members",
			"models" => "Models",
			"pictureupdates" => "Picture Updates",
			"queuecomment" => "Queue Comment",
			"queued" => "Queued",
			"quickstats" => "Quick Stats",
			"siteoverview" => "Site Overview",
			"spam" => "Spam",
			"tags" => "Tags",
			"updates" => "Updates",
			"videoupdates" => "Video Updates",
			"view" => "View",
			"viewupdate" => "View Update",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

	} //function index

/**
* approve
*
* @access private
*/
	private function approve(){

		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

			$id = intval($_POST['comment']);

			/***database connect***/
			$db = DB::getInstance();

			$db->Update("UPDATE `bx_comment` SET `status` = 'approved' WHERE `id` = ?", array($id));

			Logger::AdminActivity('approved comment', '');

		unset($_POST);

	} //approve
/**
* queue
*
* @access private
*/
	private function queue(){

		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

			$id = intval($_POST['comment']);

			/***database connect***/
			$db = DB::getInstance();

			$db->Update("UPDATE `bx_comment` SET `status` = 'queued' WHERE `id` = ?", array($id));

			Logger::AdminActivity('queued comment', '');

		unset($_POST);

	} //queue
/**
* spam
*
* @access private
*/
	private function spam(){

		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

			$id = intval($_POST['comment']);

			/***database connect***/
			$db = DB::getInstance();

			$db->Update("UPDATE `bx_comment` SET `status` = 'spam' WHERE `id` = ?", array($id));

			Logger::AdminActivity('marked comment as spam', '');

		unset($_POST);

	} //spam

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try {
				if( Authentification::CheckPermission('administrator', 'editor') )
				{
					if (isset($_POST['_action']))
					{
						if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
							throw new Exception($translate->_('Security Token mismatch'));

						/***approve comment***/
						if ($_POST['_action'] == 'approve')
							$this->approve();
						/***mark as spam comment***/
						elseif ($_POST['_action'] == 'spam')
							$this->spam();
						/***queue comment***/
						elseif ($_POST['_action'] == 'queue')
							$this->queue();
					}
				}
			}
			catch (Exception $e) {
					echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();
	}

} //class