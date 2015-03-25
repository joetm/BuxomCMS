<?php

/*** Zend path setup ***/
require '../_init.php';

/***database connect***/
$db = DB::getInstance();


$db->Update("TRUNCATE TABLE `bx_2257`");
$db->Update("TRUNCATE TABLE `bx_content`");
$db->Update("TRUNCATE TABLE `bx_location`");
$db->Update("TRUNCATE TABLE `bx_rating`");
$db->Update("TRUNCATE TABLE `bx_tag`");
$db->Update("TRUNCATE TABLE `bx_tag_content`");
$db->Update("TRUNCATE TABLE `bx_thumbnail`");

Filehandler::RemoveDir(__SITE_PATH.'/thumbs/models/bianca');

echo "done";
?>