<?php

/***page setup***/
$perpage = 4;
/***page setup***/

/***database connect***/
$db = DB::getInstance();

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

	/***content query***/
	$order = ' ORDER BY u.id';
	//thumbnail with internal_id = 0 will be used as thumbnail
	$models = $db->FetchAll("SELECT u.id, u.title as `modelname`, u.slug,
					th.path, th.width, th.height,
					l.location, l.LatLng, c.country
				FROM `bx_content` AS `u`
				JOIN `bx_thumbnail` AS `th` ON (u.id = th.content_id AND th.theme = ?)
				JOIN `bx_location` AS `l` ON (u.location_id = l.id)
				LEFT JOIN `bx_country` AS `c` ON (l.country_iso = c.iso)
				WHERE u.type = 'model'
				AND th.internal_id = 0".
				$order,
				array(Template::$theme));

	//Latitude,Longitude breakdown
	for($i=0, $s=count($models); $i < $s; $i++){
		if($latlng = explode(",", $models[$i]['LatLng']))
		{
			$models[$i]['Lat'] = $latlng[0];
			$models[$i]['Lng'] = $latlng[1];
			unset($models[$i]['LatLng']);
		}
	}
	unset($latlng);
	/***content query***/

/***database disconnect***/
unset($db);


/***TEMPLATE ASSIGNMENTS***/

//models
$tpl->assign("models", $models);
