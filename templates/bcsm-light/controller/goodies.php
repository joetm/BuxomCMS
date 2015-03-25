<?php

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"wallpaper" => "Wallpaper",
	"wallpapers" => "Wallpapers",
	"free" => "Free",
	"screensaver" => "Screensaver",
));
$tpl->assign("_t", $_t);
