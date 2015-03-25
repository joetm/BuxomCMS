<?php

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/


/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"rememberme"  => "Remember Me",
));
$tpl->assign("_t", $_t);
