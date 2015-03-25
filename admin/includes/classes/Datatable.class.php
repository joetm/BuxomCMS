<?php

/* **************************************************************
 *  File: Datatable.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Datatable Lookup Helper Class
*
*/
class Datatable {

/**
* Get Table
*
* translate internal input into database table
* obfuscates the database structure and therefore adds security
*
* @access	public
* @param	string	$input
* @return	string
*/
	public static function GetTable($input)
	{
		$table = '';

		switch ($input)
		{
			case 'tags':
				$table = 'bx_tag';
				break;
			case 'faq':
				$table = 'bx_faq';
				break;
			case 'comments':
				$table = 'bx_comment';
				break;
			case 'members':
				$table = 'bx_member';
				break;
			case 'model':
				$table = 'bx_content'; //models are stored in bx_content, together with the updates
				break;
			case 'updates':
				$table = 'bx_content';
				break;
			case 'ratings':
				$table = 'bx_rating';
				break;
			case 'login_history':
				$table = 'bx_administrator_activitylog';
				break;
			case 'activity_log':
				$table = 'bx_administrator_activitylog';
				break;
			case '2257docs':
				$table = 'bx_2257';
				break;
		}

		return $table;
	}

/**
* Get Column
*
* This is used for column sorting.
* The first column defines the default sorting behavior
*
* @access	public
* @param	integer	$i
* @param	string	$post
* @return	string
*/
	public static function GetColumn($i, $post)
	{
		//convert id to column name

		$i = intval($i);
		$arr = array();

		switch($post)
		{
			//first column is the default sort ordering

			case 'model': //id column is hidden
					$arr = array('id', 'id', 'title', 'slug', 'rating', 'comments');
				break;
			case 'comments':
					$arr = array('c.id', 'c.id', 'c.parent_id', 'u.id', 'dateline', 'comment', 'status', 'karma', 'username');
				break;
			case 'updates':
					$arr = array('u.id', 'u.id', 'u.title', 'u.slug', 'u.dateline', 'u.type');
				break;
			case 'tags':
					$arr = array('tag', 'id', 'tag', 'description');
				break;
			case 'ratings':
					//contains some hidden columns
					$arr = array('r.id', 'r.id', 'r.id', 'rating', 'm.username', 'IP', 'r.dateline', 'c.id', 'c.type');
				break;
			case 'faq':
					$arr = array('id','id','status','name','question','answer','username','email','dateline');
				break;
			case 'members':
					$arr = array('id', 'id', 'username', 'email', 'join_date', 'last_login','expiration_date', 'IP');
				break;
			case 'login_history':
					$arr = array('dateline', 'username', 'IP', 'IP', 'info', 'dateline');
				break;
			case 'activity_log':
					$arr = array('dateline', 'username', 'action', 'IP', 'dateline');
				break;
			case '2257docs':
					$arr = array('id', 'id', 'id', 'title', 'f.notes');
				break;

		} //switch $post

		//return the table column names (used for sorting with ajax)
		return $arr[$i];

	} //GetColumn

} //class Datatable
