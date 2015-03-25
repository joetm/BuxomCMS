<?php

/* **************************************************************
 *  File: Rollback.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Rollback Class
*
*/
class Rollback {

	public static $log = array(
		'_2257_ids' => array(),
		'_2257_insert' => false,
		'_2257_update' => false,
		'content_update' => false,
		'location_insert' => false,
		'location_update' => false,
		'model_has_set_insert' => false,
		'model_has_set_update' => false,
		'model_insert' => false,
		'model_update' => false,
		'newslugcreation' => false,
		'path_update' => false,
		'rating_insert' => false,
		'slugdbupdate' => false,
		'slugrenamed' => false,
		'ratings' => false,
		'tag_content_insert' => false,
		'tag_content_update' => false,
		'tag_inserts' => '',
		'thumbfolder_creation' => false,
		'thumbintemp' => array(),
		'thumbmove' => array(),
		'thumbnail_insert_ids' => '',
		'thumbupdate' => false,
		'update_insert' => false,
	);

/**
* Undo
*
* @access	public
* @param	string	$what
* @param	array	$old
* @param	string	$where
*/
	public static function Undo($what, Array $old, $where='')
	{
		/***database connect***/
		$db = DB::getInstance();

		switch ($what)
		{
			case 'location_update':
				$db->Update("UPDATE `bx_location` SET
					`location`=?,
					`state`=?,
					`zipcode`=?,
					`description`=?,
					`LatLng`=?,
					`country_iso`=?
					WHERE `id`=?", array(
					$old['location'],
					$old['state'],
					$old['zipcode'],
					$old['description'],
					$old['LatLng'],
					$old['country_iso'],
					$where));
				break;
			case 'content_update':
				$db->Update("UPDATE `bx_content` SET
					`slug`=?,
					`title`=?,
					`description`=?,
					`dateline`=?,
					`location_id`=?
					WHERE `id`=?", array(
					$old['slug'],
					$old['title'],
					$old['description'],
					$old['dateline'],
					$old['location_id'],
					$where));
				break;
			case 'tag_content_insert':
					$db->Update("DELETE FROM `bx_tag_content` WHERE `content_id`=?", array($where));
					$sql = "INSERT INTO `bx_tag_content` (`content_id`,`tag_id`,`dateline`) VALUES ";
					foreach($old as $o)
					{
						$sql .= $db->Prepare("(?,?,?),",array(
						$o['content_id'],$o['tag_id'],$o['dateline']));
					}
					$sql = rtrim($sql, ",");
					$db->Update($sql);
				break;
			case 'ratings':
					$db->Update("DELETE FROM `bx_rating` WHERE `content_id`=?", array($where));
					$sql = "INSERT INTO `bx_rating` (`id`,`content_id`,`rating`,`member_id`,`dateline`) VALUES ";
					foreach($old as $o)
					{
						$sql .= $db->Prepare("(?,?,?,?,?),",array(
						$o['id'],$o['content_id'],$o['rating'],$o['member_id'],$o['dateline']));
					}
					$sql = rtrim($sql, ",");
					$db->Update($sql);
				break;
			case '2257_delete':
					$db->Update("DELETE FROM `bx_2257` WHERE `id` IN ('" . implode("','", $old) . "')");
				break;
			case '2257_insert':
					foreach($old as $o)
					{
						$db->Update("INSERT INTO `bx_2257` (`id`,`content_id`,`real_name`,`aliases`,`birthdate`,`gender`,`passport_id`,`modelrelease_path`,`identification_path`,`misc_url`,`production_date`,`location_id`,`notes`,`dateline`,`parent`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
						$o['id'],
						$o['content_id'],
						$o['real_name'],
						$o['aliases'],
						$o['birthdate'],
						$o['gender'],
						$o['passport_id'],
						$o['modelrelease_path'],
						$o['identification_path'],
						$o['misc_url'],
						$o['production_date'],
						$o['location_id'],
						$o['notes'],
						$o['dateline'],
						$o['parent']));
					}
				break;
			case 'model_has_set_update':
					$db->Update("DELETE FROM `bx_model_has_set` WHERE `content_id`=?", array($where));

					$sql = "INSERT INTO `bx_model_has_set` (`model_id`,`content_id`) VALUES ";
					foreach($old AS $o)
					{
						$sql .= $db->Prepare("(?,?),",array(
						$o['model_id'],$o['content_id']));
					}
					$sql = rtrim($sql, ",");
					$db->Update($sql);
				break;
			case 'thumbnail_delete':
					$db->Update("DELETE FROM `bx_thumbnail` WHERE `content_id` = ? AND `type` = ? AND `theme` = ?", array(
						$where[0],
						$where[1],
						$where[2]
					));

					$sql = "INSERT INTO `bx_thumbnail` (`id`, `content_id`, `path`, `internal_id`, `type`, `theme`, `dateline`) VALUES ";
					foreach($old AS $o)
					{
						$sql .= $db->Prepare("(?,?,?,?,?,?,?),",array(
							$o['id'],
							$o['content_id'],
							$o['path'],
							$o['internal_id'],
							$o['type'],
							$o['theme'],
							$o['dateline']
						));
					}
					$sql = rtrim($sql,",");
					$db->Update($sql);
				break;
			case '2257_model_update':
					$db->Update("UPDATE `bx_2257` SET
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
							$old['real_name'],
							$old['aliases'],
							$old['birthdate'],
							$old['gender'],
							$old['passport_id'],
							$old['identification_path'],
							$old['misc_url'],
							$old['location_id'],
							$old['notes'],
							$old['dateline'],
							$where
						));
				break;
			case 'path_update':
					$db->Update("UPDATE `bx_content` SET `memberpath`=?, `freepath`=? WHERE `id`=?",array($old['memberpath'], $old['freepath'], $where));
				break;
			default:
				return;
				break;

		} //switch

	} //Undo

} //class