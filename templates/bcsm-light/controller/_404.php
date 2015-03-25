<?php

	/***page setup***/
	$tpl->title = "BuxomCurves.com - Error 404: File Not Found.";
	$tpl->headerimgheight = "325";
	$tpl->barcolor = "261512";
	$tpl->_keywords = "buxom,buxom girls, buxom women, chubby, bbw, fat, fat girls, cute plumpers, fat belly, fat women";
	$tpl->_description = "BuxomCurves description";
	/***page setup***/

	/***database connect***/
	$db = DB::getInstance();

		/***stats***/
		$stats = new Stats($tpl);
		/***stats***/

	/***database disconnect***/
	unset($db);

/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"filenotfound"  => "File Not Found",
	"error404"  => "Error 404",
));
$tpl->assign("_t", $_t);
