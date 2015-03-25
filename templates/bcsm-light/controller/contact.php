<?php

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"contactus"  => "Contact Us",
	"webmaster"  => "Webmaster",
));
$tpl->assign("_t", $_t);

//get the email
$tpl->assign("_email", Config::GetDBOptions('email'));

