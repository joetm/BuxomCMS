<?php

/* **************************************************************
 *  File: file.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/* loads protected member area pictures */
/* only needed if authentification method is database/session cookies */

/***config***/
require_once "./_init.php";
//$translate = new Translator();
//$translate->CommonTranslations();
/***config***/

/* check user authentification */







/* config */

set_time_limit(0);
error_reporting(0);






/* load image data */

//we get the id of the image
$id = intval($_GET['p']);

	/***database connect***/
	$db = DB::getInstance();

		$imageinfo = array();
		$imageinfo = $db->Row("SELECT i.filename, c.memberpath
					FROM `bx_picture` AS `i`,
					`bx_content` AS `c`
					WHERE i.content_id = c.id
					AND i.id=?
					LIMIT 1", array($id));

			//get image for the THEME!!!!!!!!










	/***database disconnect***/
	unset($db);

if(!$imageinfo) {
	header('Location: '.Path::Get('url:site').'/error.php?http=404');
	die();
}

//get internal slug
//$imageinfo['slug'] = Input::clean($imageinfo['slug'], 'FILENAME');


$path = Path::Get('path:member/pics') . String::Slash($imageinfo['memberpath'],1,1) . $imageinfo['filename'];


if (!file_exists($path) || !is_file($path)){
   header('HTTP/1.0 404 Not Found');
   echo "File Not Found.";
   die();
}
if(!is_readable($path)){
	header('HTTP/1.0 403 Forbidden');
	echo "Access Denied.";
	die();
}

$imagetype = Filehandler::getFileType($path);


//if (empty($imagetype)) $imagetype = 'jpeg';


/* headers */

//header('Expires: ');
//header('Cache-Control: ');
//header('Pragma: ');

header ("Content-type: image/$imagetype");
header ("Content-Length: ".filesize($path));

/* output */

//readfile() uses twice the memory of the file
if(Filehandler::readfile_chunked($path, false) === false)
{
	header('HTTP/1.0 500 Internal Server Error');
	echo "Internal Server Error.";
	die();
}
else
{
	header('HTTP/1.0 200 OK');
}

die();