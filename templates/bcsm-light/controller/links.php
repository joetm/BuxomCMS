<?php

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"blogs"  => "Blogs",
	"links"  => "Links",
	"reviewsites"  => "Review Sites",
));
$tpl->assign("_t", $_t);

//dirty hack
$tpl->templatename = "about";
