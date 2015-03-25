<?php

/***page setup***/
//number of updates
//one latest update by default
	$numupdates = 6;
//number of "coming soon" updates
	$numcomingsoon = 2;
//number of comments
	$commentlimit = 3;
/***page setup***/

/***database connect***/
$db = DB::getInstance();

	/***content query***/

	/***updates***/
	$numpicsets = 0;
	$numvideos = 0;
	$sceneratings = 0;
	//we query one extra update as latest update

	$updates = $db->FetchAll("SELECT u.id, u.title, u.slug, u.count, u.dateline,
					th.path, th.width, th.height
					FROM `bx_content` AS `u`
					JOIN `bx_thumbnail` AS `th` ON (th.content_id = u.id AND th.theme = ?)
					WHERE th.internal_id = '0'
					AND (th.type ='pics' OR th.type = 'videos')
					AND u.dateline <= '".DB::UNIXNOW()."'
					ORDER BY `id` DESC LIMIT ".$numupdates++,
					array(Template::$theme)); //"


/*
	$s = count($updates);
	if ($s != 0){
		for ($i = 0; $i < $s; $i++)
		{
			if ($updates[$i]['type'] == 'pics') {$numpicsets++;}else{$numvideos++;}
			$sceneratings += $updates[$i]['rating'];
		}
		$avgscenerating = $sceneratings / $s;
	}
*/

	/***latest update***/
	if (!empty($updates)) $latestupdate = array_shift($updates);

	/***comments***/
	$comments = $db->FetchAll("SELECT * FROM `bx_comment` WHERE `status` = 'approved' ORDER BY `dateline` LIMIT ".$commentlimit);

	/***coming soon***/
	$comingsoon = $db->FetchAll("SELECT * FROM `bx_content` WHERE `dateline` > ? ORDER BY `id` ASC LIMIT ".$numcomingsoon, array( DB::UNIXNOW() ) );

	/***models***/
	$models = $db->FetchAll("SELECT m.id, m.title, m.slug, m.count, m.title AS `modelname`,
					th.path, th.width, th.height
					FROM `bx_content` AS `m`
					JOIN `bx_thumbnail` AS `th` ON (m.id = th.content_id AND th.theme = ?)
					WHERE th.internal_id = '0'
					AND m.dateline <= '".DB::UNIXNOW()."'
					AND m.type = 'model'
					ORDER BY `id` DESC LIMIT ".$numupdates++,
					array(Template::$theme)); //"

	/***content query***/

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

/***database disconnect***/
unset($db);


/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"blogger"  => "Blogger",
	"coming_soon"  => "Coming Soon",
	"flickr"  => "Flickr",
	"followus"  => "Follow us",
	"followuson"  => "Follow us on",
	"lastupdate"  => "Latest Update",
	"more"  => "More",
	"myspace"  => "Myspace",
	"newestcomments"  => "Newest Comments",
	"news"  => "News",
	"nocomments"  => "No Comments",
	"nomodels"  => "No Models",
	"noupdates"  => "No Updates",
	"ourmodels"  => "Our Models",
	"rating"  => "Rating",
	"recentupdates"  => "Recent Updates",
	"reportcomment"  => "Report Comment",
	"said"  => "said",
	"twitter"  => "Twitter",
	"welcomemessage"  => "Welcome to ".__sitename,
	"youtube"  => "Youtube",
));
$tpl->assign("_t", $_t);

	/***social network details***/
//	$tpl->assign("myspace_user","myspace_pimp");
	$tpl->assign("twitter_user","duesynapse");
	$tpl->assign("youtube_user","pornster_woot");
	$tpl->assign("blogger_user","pornster");
	$tpl->assign("flickr_user","pornfeeder");
	$tpl->assign("tumblr_user","jade38h");

/* content */
if (isset($comingsoon)) $tpl->assign("comingsoon", $comingsoon);
if (isset($comments)) $tpl->assign("comments", $comments);
if (isset($latestupdate)) $tpl->assign("latestupdate", $latestupdate);
if (isset($models)) $tpl->assign("models", $models);
if (isset($updates)) $tpl->assign("updates", $updates);
