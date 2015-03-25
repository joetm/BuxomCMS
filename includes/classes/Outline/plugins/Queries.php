<?php

/***************************************************************
 * File: Queries.php
 * Version: 1.0
 *
 * Database plugin for the template engine
 *
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

//one function for every database table

class Queries extends OutlinePlugin {

	public static function register (&$compiler)
	{
		$compiler->registerBlock('config', 'config_block');

		$compiler->registerTag('models', 'db_models');
		$compiler->registerTag('videos', 'db_videos');
		$compiler->registerTag('photos', 'db_photos');
		$compiler->registerTag('members', 'db_members');
		$compiler->registerTag('comments', 'db_comments');
		$compiler->registerTag('faq', 'db_faq');
		$compiler->registerTag('favorites', 'db_favorites');
		$compiler->registerTag('ratings', 'db_ratings');
		$compiler->registerTag('tags', 'db_tags');
	}

	// * the database queries:


	/* models */
	public function db_models($_args) {

		// (defaults)
			$description = false;	//
			$thumbnails = true;		//model thumbnail
			$internal_id = 0;		//internal_id of the thumbnail
			$amount = 12;			//number of models
			$count = true; 			//picture and video count (?)
			$date = false;
			$location = false;
			$birthdate = false;
			$rating = false;		//
			$paginate = false;		//

		$_args = String::stripChars($_args);

		/*assign args*/
		$args = explode(" ", $_args);
		foreach($args as $value)
		{
			$temp = explode("=", $value, 2);
			${$temp[0]} = String::boolval($temp[1]);
		}
		/*assign args*/

		//get template
		$tpl = $this->GetTpl();

		/***database connect***/
		$db = DB::getInstance();

			$input = new Input;
			$input->GetSortOrder();

			/***pagination***/
			if($paginate == true)
			{
				$p = new Pagination;
				$query = "SELECT count(*) FROM `bx_content` WHERE `type` = 'model'";
				$p->paginate($query, $amount, 'models.php');
				$p->assign($tpl);
			}
			/***pagination***/

			$rating_decimal = Config::Get('rating_decimal');

			$ts_ids = array();
			$ts_ids = explode(",",$internal_id);
			if(count($ts_ids) > 1)
			{
				$internal_id = '';
				foreach($ts_ids as $id)
				{
					$internal_id .= $db->Prepare("?,",array($id));
				}
				$internal_id = rtrim($internal_id, ",");
			}





//needs to be fixed and simplified


			$models = array();
			$models = $db->FetchAll("SELECT `m`.`id`, `m`.`title` AS `modelname`, `m`.`slug`".
									($description == 'true'?",`m`.`description` ":"").
									($rating == 'true'?",TRUNCATE(AVG(`r`.`rating`),?) AS `rating`":"").
									" FROM `bx_content` AS `m`".
									($rating == 'true'?" LEFT JOIN `bx_rating` AS `r` ON (`r`.`content_id`=`m`.`id`)":"").
									" WHERE `m`.`type`='model'".
									" GROUP BY `m`.`id`".
									$input->order.
									($paginate ? $p->pagination['limit'] : " LIMIT 0,?")
									,array( (!is_null($rating_decimal) ? $rating_decimal : 2),

										$amount));
			$tpl->assign("models", $models);

			//sorting
			if(isset($input->sorting)) $tpl->assign("sorting", $input->sorting);

		$this->compiler->code(' ');

	} //models


	public function db_videos($_args) {

		/***defaults***/
		$amount = 9;
		$internal_id = 0;

		/*assign args*/
		$args = explode(" ", $_args);
		foreach($args as $value)
		{
			$temp = explode("=", $value, 2);
			${$temp[0]} = String::boolval($temp[1]);
		}
		/*assign args*/

		//get template
		$tpl = $this->GetTpl();

		/***database connect***/
		$db = DB::getInstance();

			$input = new Input;
			$input->GetSortOrder();

			if($paginate == 'true')
			{
				/***pagination***/
				$p = new Pagination;
				$query = "SELECT count(*) FROM `bx_content` WHERE `type`='videoset'";
				$p->paginate($query,$amount,'videos.php');
				$p->assign($tpl);
				/***end pagination***/
			}





//needs to be fixed and simplified

			/***content query***/
			$videos = array();
			//image with internal_id = 0 is used as thumbnail
			$videos = $db->FetchAll("SELECT v.id, v.title, v.slug, v.dateline,
							th.path, th.width, th.height
							FROM `bx_content` AS `v`
							LEFT JOIN `bx_thumbnail` AS `th` ON (th.content_id = v.id)
							WHERE th.internal_id=?
							AND th.type = 'videoset'
							AND th.type = v.type
							AND v.dateline <= ".DB::UNIXNOW().
							$input->order.
							$p->pagination['limit'],
							array($internal_id));
			/***content query***/

		/***TEMPLATE ASSIGNMENTS***/

		//videos
		$tpl->assign("videos", $videos);

		//sorting
		if(isset($input->sorting)) $tpl->assign("sorting", $input->sorting);

		$this->compiler->code(' ');

	} //videos

	public function db_photos($_args) {

		/***default***/
		$amount = 12;
		$internal_id = 0;

		/*assign args*/
		$args = explode(" ", $_args);
		foreach($args as $value)
		{
			$temp = explode("=", $value, 2);
			${$temp[0]} = String::boolval($temp[1]);
		}
		/*assign args*/

		//get template
		$tpl = $this->GetTpl();

		/***database connect***/
		$db = DB::getInstance();

			$input = new Input;
			$input->GetSortOrder();

			if($paginate == true)
			{
				/***pagination***/
				$p = new Pagination;
				$query = "SELECT count(*) FROM `bx_content` WHERE `type`='pics'";
				$p->paginate($query, $amount, 'photos.php');
				$p->assign($tpl);
				/***pagination***/
			}




//needs to be fixed and simplified

			/***content query***/
			$updates = array();
			$updates = $db->FetchAll("SELECT u.id, u.title,
							th.path, th.width, th.height
							FROM `bx_content` AS `u`
							LEFT JOIN `bx_thumbnail` AS `th` ON (th.content_id = u.id)
							WHERE th.internal_id=?
							AND th.type = 'pics'
							AND th.type = u.type
							AND u.dateline <= ".DB::UNIXNOW().
							$input->order.
							$p->pagination['limit'],
							array($internal_id));
			/***content query***/


		/***TEMPLATE ASSIGNMENTS***/

		//updates
		if(isset($updates)) $tpl->assign("updates", $updates);

		//sorting
		if(isset($input->sorting)) $tpl->assign("sorting", $input->sorting);

		$this->compiler->code(' ');

	} //photos

	public function db_members($_args) {

	}

	public function db_comments($_args) {

	}

	public function db_faq($_args) {

	}

	public function db_favorites($_args) {

	}

	public function db_ratings($_args) {

	}

	public function db_tags($_args) {

	}

	private function GetTpl()
	{
		if (Zend_Registry::isRegistered('tpl'))
			return Zend_Registry::get('tpl');
		else
			return new Template;
	}

} //class