<?php

/* **************************************************************
 *  File: step2video.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

/*** include the init.php file ***/
require_once '../../_init.php';

/*--------------------------------------------------------------*/
/*						Authentification						*/
/*--------------------------------------------------------------*/
		if(!Authentification::Login())
		{
			echo Authentification::GetError();
			die();
		}
		Authentification::CheckPermission('administrator');
/*--------------------------------------------------------------*/


$image = Path::Get('path:admin').'/lib/img/structure.jpg';

if(!file_exists($image))
{
	header("HTTP/1.0 404 Not Found");
	die("File not Found.");
}

ob_start();

	$imagedata = file_get_contents($image);

	//set headers
	$length = strlen($imagedata);
	header('Last-Modified: '.date('r'));
	header('Accept-Ranges: bytes');
	header('Content-Length: '.$length);
	header('Content-Type: image/jpeg');

	print($imagedata);

ob_end_flush();
