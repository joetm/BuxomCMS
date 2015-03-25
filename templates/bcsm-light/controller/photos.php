<?php

/***database connect***/
$db = DB::getInstance();

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

	/*updates*/
	$updates = array();
	$updates = $db->FetchAll("SELECT u.id, u.type, u.slug, u.title, u.description, u.count, u.dateline,
				th.path, th.width, th.height
				FROM `bx_content` AS `u`
				JOIN `bx_thumbnail` AS `th` ON (u.id = th.content_id AND th.theme=?)
				WHERE u.type = 'set'
				AND th.internal_id = '0'
				ORDER BY u.dateline
				LIMIT 12",
				array(Template::$theme));

/***database disconnect***/
unset($db);

/*page config
$tpl->assign('_pagetitle', 'BuxomCurves Pictures');
$tpl->assign('_keywords', 'buxom,buxom girls, buxom women, chubby, bbw, fat, fat girls, cute plumpers, fat belly, fat women');
$tpl->assign('_description', 'BuxomCurves description');
$tpl->assign('headerimgheight', '325');
$tpl->assign('barcolor', '961512');
page config*/

/*translations*/
$_t = $translate->translateArray(array(
	"date" => "Date",
	"noupdates" => "No Updates",
	"photogalleries" => "Photo Galleries",
	"rating" => "Rating",
	"sortby" => "Sort by",
	"title" => "Title",
));
$tpl->assign("_t", $_t);

/*updates*/
$tpl->assign('updates', $updates);
