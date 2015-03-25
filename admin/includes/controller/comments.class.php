<?php

/* **************************************************************
 *  File: comments.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class comments extends BaseController
{

	private static $comment = array();
	private static $token = false;

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
		$tpl->title = $translate->_("List Comments");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			/***prefill edit comment***/
			//edit existing comment
			if (isset($_GET['id']))
			{
				$id = intval($_GET['id']);
				$comment = $db->FetchObject("SELECT * FROM `bx_comment` WHERE `id` = ? LIMIT 1", array($id), 'comment');

				if (!$comment) {
					throw new Exception($translate->_("Could not find comment with ID"));
				}
				$tpl->assign("c", Arr::ParseObjectToArray($comment));
			}

			$options = Config::GetDBOptions('nested_comments');

		/***disconnect***/
		unset($db);

		//internationalization
		$_t = $translate->translateArray(array(
			"add" => "Add",
			"addcomment" => "Add Comment",
			"bulkapprove" => "Bulk Approve",
			"comment" => "Comment",
			"comment_id" => "Comment-ID",
			"commenter" => "Commenter",
			"date" => "Date",
			"delete" => "Delete",
			"edit" => "Edit",
			"editcomment" => "Edit Comment",
			"host" => "Host",
			"id" => "ID",
			"ip" => "IP",
			"karma" => "Karma",
			"name" => "Name",
			"parent" => "Parent",
			"pickfromcalendar" => "Pick from calendar",
			"preview" => "Preview",
			"reply" => "Reply",
			"reply_to_comment" => "Reply to Comment",
			"scheduleddate" => "Scheduled Date",
			"status" => "Status",
			"update" => "Update",
			"update_id" => "Update-ID",
		));
		$tpl->assign("_t", $_t);

		//options
		if ($options) 	$tpl->assign('options', $options);

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

/**
* deletion
*
* @access private
*/
	private function deletion()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

			if(isset($_POST['checkbox']))
			{
				$tpl = Zend_Registry::get('tpl');

				/***get connection***/
				$db = DB::getInstance();

				//get the ratings that are marked for deletion
				$del_ids = Input::GetCheckBoxes();

				//delete the ratings that are marked for deletion
				$status = $db->Update("DELETE FROM `bx_comment` WHERE `id` IN (".$del_ids.")");

				//is plural?
				if(!explode(",",$del_ids,2))
					$delerror = $translate->_("Comment deletion error");
				else
					$delerror = $translate->_("Comment deletion errors");

				if(!$status) {
					$tpl->errormessage($delerror); //"Could not delete comment(s)."
				}
				else{
					$tpl->successmessage($translate->_("Done"));
					Logger::AdminActivity('deleted comment(s)', '');
				}
			}

		unset($_POST);

	} //deletion function

/**
* add_edit
*
* @access private
*/
	private function add_edit()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		$tpl = Zend_Registry::get('tpl');

		//inputs
		$comment = Input::clean_array('p',array(
			"comment"  => "NOHTML",
			"name"	   => "NOHTML",
			"commentid"=> "UINT",
			"contentid"=> "UINT",
			"parent_id"=> "UINT",
			"date"	   => "DATE",
		));

		//convert date to dateline
		if(empty($comment['date']))
			$comment['date'] = time();
		else
			$comment['date'] = String::date_to_dateline($comment['date']);

		/***database connect***/
		$db = DB::getInstance();


		//check if a member with that username exists
		$member = $db->Row("SELECT `id`
				FROM `bx_member`
				WHERE `username` = ?",
				array($comment['name']));
		if(empty($member['id'])) $member['id'] = '';

		if(empty($comment['parent_id'])) $comment['parent_id'] = 0;


		/****************/
		/* EDIT COMMENT */
		/****************/
		if(!empty($comment['comment']) && !$comment['parent_id'] && !empty($comment['commentid']))
		{
			$status = $db->Update("UPDATE `bx_comment`
						SET `comment` = ?,
						`member_id` = ?,
						`username` = ?,
						`dateline` = ?
						WHERE `id` = ?",
						array(
							$comment['comment'],
							$member['id'],
							$comment['name'],
							$comment['date'],
							$comment['commentid']
						));
			Logger::AdminActivity('edited comment', $comment['commentid']);
			if($status)
				$tpl->successmessage($translate->_("Comment edited successfully"));
			else
				$tpl->errormessage($translate->_("Could not edit comment"));
		}
		/*******************/      /****************/
		/* ADD NEW COMMENT  /  or  /     REPLY     */
		/*******************/      /****************/
		elseif(!empty($comment['comment']) && (!empty($comment['contentid']) || $comment['parent_id']))
		{
			$status = $db->Update("INSERT INTO `bx_comment` (
						`id`,
						`content_id`,
						`comment`,
						`status`,
						`member_id`,
						`username`,
						`parent_id`,
						`dateline`
						) VALUES (
							NULL,
							?,
							?,
							'approved',
							?,
							?,
							?,
							?
						)",
						array(
							$comment['contentid'],
							$comment['comment'],
							$member['id'],
							$comment['name'],
							intval($comment['parent_id']),
							$comment['date']
						));
			Logger::AdminActivity('added comment', $db->LastInsertId());

			if($comment['parent_id'])
				if($status)
					$tpl->successmessage($translate->_("Replied successfully"));
				else
					$tpl->errormessage($translate->_("Could not add reply"));
			else
				if($status)
					$tpl->successmessage($translate->_("Comment added successfully"));
				else
					$tpl->errormessage($translate->_("Could not add comment"));
		}
		else
		{
			$tpl->errormessage($translate->_("Missing input variables"));
		}

		//remove prefill
		unset($comment);
		unset($member);

		unset($_POST);

	} //add_edit

/**
* approve
*
* @access private
*/
	private function approve(){

		//get translation
		$translate = Zend_Registry::get('translate');

			/***database connect***/
			$db = DB::getInstance();

			$tpl = Zend_Registry::get('tpl');

			//aprove comment
			if(isset($_POST['checkbox'])){

				$approve_ids = Input::getCheckboxes();

				//approve comment
				$status = $db->Update("UPDATE `bx_comment` SET `status` = 'approved' WHERE `id` IN (".$approve_ids.")");

				if ($status == -1) {
					$tpl->errormessage($translate->_("Error").". ".mysql_error());
				}
				else{
					$tpl->successmessage($translate->_("Successfully approved comment"));
					Logger::AdminActivity('approved comment(s)', '');
				}

				//remove Prefill
				unset($comment);

				unset($approve_ids);
			}
			//remove prefill

		unset($_POST);

	} //approve function

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
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if( Authentification::CheckPermission('administrator', 'editor') )
				{
					# deletion handling
					if (isset($_POST['delete'])) $this->deletion();
					# approve comment
					if(isset($_POST['approve'])) $this->approve();
					#edit existing or add new
					if(isset($_POST['updatecomment'])) $this->add_edit();
				}
			}
			catch (Exception $e) {
				echo $translate->_("Error").': ',  $e->getMessage(), PHP_EOL."<br>";
			}
		}

		$this->index();

	} //showIndex

} //class
