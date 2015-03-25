<?php

/***page setup***/
$commentlimit = 5;
$updatesperpage = 25;
/***end page setup***/

/***get variables***/
$get = Input::clean_array('g',array(
	'id'	=> 'UINT',
	'slug'	=> 'NOHTML',
));
/***get variables***/


/***database connect***/
$db = DB::getInstance();

	//update data
	if(!isset($get['id']) && !isset($get['slug'])) $model = false;
	else
		$model = $db->Row("SELECT m.*,
			l.id AS location_id, l.location, l.state, l.zipcode, l.city, l.description, l.LatLng, l.country_iso,
			c.country,
			TRUNCATE(AVG(r.rating),2) as `rating`
			FROM `bx_content` AS `m`
			LEFT JOIN `bx_location` AS `l` ON (l.id = m.location_id)
			LEFT JOIN `bx_country` AS `c` ON (c.iso = l.country_iso)
			LEFT JOIN `bx_rating` AS `r` ON (r.content_id = m.id)".
			//we allow look-up by id or slug
			" WHERE (m.slug = ? OR m.id = ?)
			AND m.type = 'model'
			GROUP BY r.content_id",
			array($get['slug'], $get['id'])
		);

//var_dump($model);
//die();

	if (!$model) {
		//Model not found!

		die('Model not found');

		//do some other stuff instead





		//header("HTTP/1.0 404 Not Found");
		//header("Location: /"); //Redirect browser
	}

	//get the thumbnails
	$model['thumbnails'] = $db->FetchAll("SELECT t.*
				FROM `bx_thumbnail` AS `t`
				WHERE t.content_id = ?
				AND t.theme = ?
				ORDER BY t.internal_id ASC",
				array(
					$model['id'],
					Template::$theme
				));

	//get the updates of this model
	$model['updates'] = $db->FetchAll("SELECT c.*,
				t.path, t.width, t.height
				FROM `bx_content` AS `c`
				LEFT JOIN `bx_thumbnail` AS `t` ON (t.content_id = c.id AND t.theme = ? AND t.internal_id = '0')
				WHERE c.id IN (
					SELECT content_id
					FROM `bx_model_has_set` AS `hs`
					WHERE `model_id` = ?
					)",
				array(
					Template::$theme,
					$model['id']
				));

	//statistics for updates
	$model['stats'] = $db->Row("SELECT COUNT(c.id) AS `setnum`,
				COUNT(p.id) AS `numpics`,
				COUNT(v.id) AS `numvideos`
				FROM `bx_model_has_set` AS `hs`
				LEFT JOIN `bx_content` AS `c` ON (c.id = hs.content_id AND c.type='pics')
				LEFT JOIN `bx_content` AS `v` ON (v.id = hs.content_id AND v.type='videos')
				LEFT JOIN `bx_picture` AS `p` ON (p.content_id = hs.content_id AND p.type='pics' AND p.theme = ?)
				WHERE hs.model_id = ?",
				array(Template::$theme, $model['id']));

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

/***database disconnect***/
unset($db);


/***TEMPLATE ASSIGNMENTS***/

//model
if(!empty($model)) $tpl->assign("model", $model);

//comments
if(isset($comments)) $tpl->assign("comments", $comments);

//internationalization
$_t = $translate->translateArray(array(
	"averagescenerating"  => "Average Scene Rating",
	"country"  => "Country",
	"location"  => "Location",
	"model"  => "Model",
	"modelrating"  => "Model Rating",
	"no_comments" => "No Comments",
	"pictures"  => "Pictures",
	"pictureupdates"  => "Picture Updates",
	"rating"  => "Rating",
	"recentcomments"  => "Recent Comments",
	"reportcomment"  => "Report comment",
	"statistics"  => "Statistics",
	"updates"  => "Updates",
	"videos"  => "Videos",
));
$tpl->assign("_t", $_t);