<?php

require_once "_init.php";

/***config***/
// Turn off all error reporting
error_reporting(0);
require_once Path::Get('path:site')."/includes/classes/Zend/Feed/Writer/Feed.php";
//require_once Path::Get('path:site')."/includes/classes/Zend/Date.php";
/***config***/

/***page setup***/
//number of items in feed
$numitems = 10;
/***page setup***/

/***feed type***/
//$type can be 'rss' or 'atom'
$type = 'rss';
if($_GET['type'] == 'atom')
{
	$type = 'atom';
}

/***content type***/
switch ($_GET['action']) //feeds for updates, models, or comments
{
	case 'models':
		$action = 'models';
		$what = 'bx_models';
		break;
	case 'comments':
		$action = 'comments';
		$what = 'bx_comments WHERE status="approved"';
		break;
	case 'updates':
	default:
		$action = 'updates';
		$what = 'bx_updates WHERE date <= '.DB::UNIXNOW();
		break;
}
//assign action to registry
Zend_Registry::set('action', $action);

	/***database connect***/
	$db = DB::getInstance();

	//fetch data from database
	$sql = "SELECT * FROM `".$what."` LIMIT ".$numitems;
	$data = $db->query3d($sql);

	$cache->save($data, $action);

	/***database disconnect***/
	unset($sql);
	unset($db);

//assign data to registry
Zend_Registry::set('data', $data);

//build the feed
//modifyable in /includes/classes/FeedBuilder.class.php
$feed = FeedBuilder::Build();

//output
echo $feed->export($type);

?>