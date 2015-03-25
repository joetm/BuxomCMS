<?php

$amount = 12;			//number of models
$thumbnails = true;		//model thumbnail
$internal_id = 0;		//internal_id of the thumbnail to use as preview on this page
$description = false;		//query the description?
$count = true; 			//picture and video count (?)
$location = false;		//query the location?
$birthdate = false;		//query birthdate?
$rating = true;			//query rating?
$paginate = true;		//paginate results?


$input = new Input;
$input->GetSortOrder();


/***database connect***/
$db = DB::getInstance();

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

	/***pagination***/
	if($paginate){
		$p = new Pagination;
		$query = "SELECT count(*) FROM `bx_content` WHERE `type` = 'model'";
		$p->paginate($query, $amount, 'models');
		$p->assign($tpl);
	}
	/***pagination***/

	$rating_decimal = Config::Get('rating_decimal');

	//query multiple thumbnails?
	$ts_ids = array();
	$ts_ids = explode(",",$internal_id);
	if(count($ts_ids) > 1)
	{
		$internal_id = '';
		foreach($ts_ids as $id)
		{
			$internal_id .= $db->Prepare("?,", array($id));
		}
		$internal_id = rtrim($internal_id, ",");
	}


	$models = array();
	$models = $db->FetchAll("SELECT u.id, u.title AS `modelname`, u.slug".
				($thumbnails ? ", th.path, th.width, th.height" : '').
				($description ? ", u.description,":'').
				($rating ? ", TRUNCATE(AVG(r.rating),".intval($rating_decimal).") AS `rating`" : '').
				" FROM `bx_content` AS `u`".
				($thumbnails ? " JOIN `bx_thumbnail` AS `th` ON (u.id = th.content_id AND th.theme = '".mysql_real_escape_string(Template::$theme)."')" : '').
				($rating ? " LEFT JOIN `bx_rating` AS `r` ON (r.content_id = u.id)" : '').
				" WHERE u.type = 'model'
				AND th.internal_id = '0'
				GROUP BY u.id
				ORDER BY th.internal_id DESC".
				$p->pagination['limit']
				);


/***database disconnect***/
unset($db);


/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"date"  => "Date",
	"name"  => "Name",
	"rating"  => "Rating",
	"sortby"  => "Sort by",
));
$tpl->assign("_t", $_t);

//sorting
if(isset($input->sorting)) $tpl->assign("sorting", $input->sorting);

//content
$tpl->assign("models", $models);