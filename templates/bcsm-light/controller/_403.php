<?php

	/***TEMPLATE ASSIGNMENTS***/

	//internationalization
	$_t = $translate->translateArray(array(
		"error403forbidden"  => "Error 403 Forbidden",
		"accessdenied"  => "Access denied",
		"nopermission"=> "No Permission",
		"error404"=> "Error 404",
		"filenotfound"=> "File Not Found",
	));
	$tpl->assign("_t", $_t);

/***TEMPLATE ASSIGNMENTS***/

$tpl->assign("templatename", $templatename);
