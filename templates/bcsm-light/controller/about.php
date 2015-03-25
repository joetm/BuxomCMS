<?php

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/


/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"aboutsite"  => "About Site",
	"joinnow"  => "Join Now",
));
$tpl->assign("_t", $_t);
