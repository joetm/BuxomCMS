<?php

/***defaults***/
$amount = 9;
$internal_id = 0;

$input = new Input;
$input->GetSortOrder();

/***database connect***/
$db = DB::getInstance();

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

	/***pagination***/
	$p = new Pagination;
	$query = "SELECT count(*) FROM `bx_content` WHERE `type`='videoset'";
	$p->paginate($query,$amount,'videos.php');
	$p->assign($tpl);
	/***end pagination***/

	/***content query***/
	$videos = array();
	//image with internal_id = 0 is used as thumbnail
	$videos = $db->FetchAll("SELECT v.id, v.title, v.slug, v.dateline,
					th.path, th.width, th.height
					FROM `bx_content` AS `v`
					LEFT JOIN `bx_thumbnail` AS `th` ON (th.content_id = v.id AND th.theme=?)
					WHERE th.internal_id=?
					AND th.type = 'videoset'
					AND th.type = v.type
					AND v.dateline <= ".DB::UNIXNOW().
					$input->order.
					$p->pagination['limit'],
					array(Template::$theme, $internal_id));
	/***content query***/

/***database disconnect***/
unset($db);


/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"date"  => "Date",
	"rating"  => "Rating",
	"sortby"  => "Sort by",
	"title"  => "Title",
	"videos"  => "Videos",
	"noupdates"  => "No Updates",
));
$tpl->assign("_t", $_t);

//videos
$tpl->assign("videos", $videos);

//sorting
if(isset($input->sorting)) $tpl->assign("sorting", $input->sorting);
