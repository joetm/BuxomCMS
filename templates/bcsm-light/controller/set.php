<?php

/***page setup***/
$commentlimit = 2;
$picsperpage = 25;
$rating_decimal = Config::Get('rating_decimal');
/***page setup***/

/***get variables***/
$get = array();
$get = Input::clean_array('g',array(
	'id'	=> 'UINT',
	'slug'	=> 'NOHTML',
));
/***get variables***/

/***database connect***/
$db = DB::getInstance();

	if(!$rating_decimal) $rating_decimal = 2;

	$thumbsizes = Config::Get('picturegrab_thumbnailsize');

	//update data
	if(!$get['id'] && !$get['slug']) $set = false;
	else
		$set = $db->Row("SELECT c.*,
			TRUNCATE(AVG(r.rating), $rating_decimal) as `rating`
			FROM `bx_content` as `c`
			LEFT JOIN `bx_rating` as `r` ON (r.content_id = c.id)
			WHERE ".($get['slug'] ? "c.slug = ?" : "c.id = ?")."
			AND c.type = 'set'
			GROUP BY r.content_id",
			array(($get['slug'] ? $get['slug'] : $get['id']))
		);
	if (!$set) {
		//Update not found!

		die('Update not found');

		//do some other stuff instead





		//header("HTTP/1.0 404 Not Found");
		//header("Location: /"); //Redirect browser
	}

	$internal_slug = Input::clean($set['slug'], 'FILENAME');

	//pictures
	$set['pictures'] = $db->FetchAll("SELECT SQL_CALC_FOUND_ROWS
			p.*,
			c.freepath
			FROM `bx_picture` as `p`
			JOIN `bx_content` as `c` ON (p.content_id = c.id)
			WHERE p.content_id = ?
			AND p.type = 'set'
			AND p.theme = ?",
			array(
				$set['id'],
				Template::$theme,
			));
			//c.memberpath

	$set['totalpics'] = $db->Column("SELECT FOUND_ROWS()");


//var_dump($set['pictures']);


	//process the individual images
	$set['picsize'] = 0;
	for($i = 0, $s = $set['totalpics']; $i < $s; $i++)
	{
		//src
		$set['pictures'][$i]['thumb']['src'] = Path::Get('rel:thumbs/set').'/'.$internal_slug.'/thumbs/'.$set['pictures'][$i]['filename'];

		$set['pictures'][$i]['thumb']['width'] = $thumbsizes['width'];
		$set['pictures'][$i]['thumb']['height'] = $thumbsizes['height'];

		//link
		//if it is a free image, use the url to free path
		//if it is a member-only image, don't output a link
		if($set['pictures'][$i]['freepicture'])
		{
			//free image
			$set['pictures'][$i]['link'] = String::Slash($set['pictures'][$i]['freepath'],1,0).'/'.$set['pictures'][$i]['slug'].$set['pictures'][$i]['filename'];

			$set['pictures'][$i]['thumb']['class'] = '';
		}
		else
		{
			//member image
			//no link
				//$set['pictures'][$i]['link'] = false;
			//or alternatively: join link
				$set['pictures'][$i]['link'] = rtrim(Config::Get('__siteurl'),"/").'/join';

			$set['pictures'][$i]['thumb']['class'] = 'inactivethumb';
		}

		//picsize (sum)
		$set['picsize'] += $set['pictures'][$i]['size'];
	}

	//second query: modelnames
	$set['models'] = $db->FetchAll("SELECT m.title as modelname, m.slug, m.id,
				t.path, t.width, t.height
				FROM `bx_model_has_set` AS `hs`,
				`bx_content` AS `m`
				LEFT JOIN `bx_thumbnail` as `t` ON (t.content_id = m.id AND t.theme=?)
				WHERE hs.content_id = ?
				AND hs.model_id = m.id
				AND m.type = 'model'
				AND t.type = 'model'
				AND t.internal_id = '0'",
				array(Template::$theme, $set['id']));

	if (!$set['models']) {
			//no matching model found

			die('No model for update found in database!');

			//header("HTTP/1.0 404 Not Found");
			//header("Location: /"); //Redirect browser





	}
	else
	{
		$modelstring = '';
		foreach($set['models'] as $m)
		{
			$modelstring .= '<a href="'.Path::Get('url:site/model').'/'.$m[slug].'">'.$m['modelname'].'</a>,';
		}
		$set['modelnames'] = rtrim($modelstring, ',');
		unset($modelstring);
	}

	//comments
	$set['comments'] = $db->FetchAll("SELECT *
				FROM `bx_comment`
				WHERE `content_id` = ?
				AND `dateline` <= ?
				LIMIT 6",
				array($set['id'], TIME_NOW));

	//tags
	$set['tags'] = $db->FetchAll("SELECT t.*
			FROM `bx_tag_content` as `tc`
			JOIN `bx_tag` as `t` ON (tc.tag_id = t.id)
			WHERE tc.content_id = ?",
			array($set['id']));

	if($set['tags']){
		$tagstring = '';
		foreach($set['tags'] as $t)
		{
			$t['tag'] = strtolower($t['tag']);
			$tagstring .= '<a href="'.Path::Get('url:site').'/tag/'.$t[tag].'">'.$t['tag'].'</a>,';
		}
		$set['tags'] = rtrim($tagstring, ',');
		unset($tagstring);
	}

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/


/***database disconnect***/
unset($db);


//nicer date
$set['date'] = String::convert_dateline($set['dateline'], 'm-d-Y');

//readable picture set size
$set['picsize'] = String::readablefilesize($set['picsize'], 2);


//if (isset($pics)){

	/*array pagination
	$pagination = new ArrayPagination;
	$picPages = $pagination->generate($pics, $picsperpage);

	$pageNumbers = $pagination->links();
	$tpl->assign("pageNumbers", $pageNumbers);
	$tpl->assign("picPages", $picPages);
	*/
//}


/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"aboutthisscene"  => "About this Scene",
	"addedon"  => "Added on",
	"currentrating"  => "Current Rating",
	"downloadoptions"  => "Download Options",
	"model"  => "Model",
	"no_pictures_found" => "No Pictures Found",
	"photocount"  => "Photo Count",
	"photos"  => "Photos",
	"rating"  => "Rating",
	"recentcomments"  => "Recent Comments",
	"said"  => "said",
	"statistics"  => "Statistics",
	"tags"  => "Tags",
));
$tpl->assign("_t", $_t);

/***pass variables to template***/
$tpl->assign('update', $set);

$tpl->assign('thumbsizes', $thumbsizes);