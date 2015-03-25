<?php

/***config***/
require_once "../_init.php";
$templatename = "login";
$tpl = new Template($templatename, __tpl_cache_time); //'buxomcurves'
$translate = new Translator();
$translate->CommonTranslations();
/***config***/

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

/***TEMPLATE ASSIGNMENTS***/

$tpl->display();

$tpl->debug();

//this will include a copyright notice comment in the output
//you can savely remove this
$tpl->Copyright();

?>