<?php

/***config***/
require_once "./_init.php";
$router = new Router;
$templatename = $router->getController();
$tpl = new Template($templatename, __tpl_cache_time);
$translate = new Translator();
$translate->CommonTranslations();
/***config***/

$router->controller();

//$tpl->controller();

//var_dump($_GET);
//die();

$tpl->display();

//this will add debug output if in debug mode
$tpl->debug();

//this will include a copyright notice comment of your site in the output
//you can savely remove this
$tpl->Copyright();

?>