<?php

/* **************************************************************
 *  File: index.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/***config***/
error_reporting(E_ALL | E_NOTICE | E_STRICT);
/***config***/

/*** include the init.php file ***/
require realpath( dirname(__FILE__).'/../_init.php' );

define('BX_CONTROL_PANEL', true);

/*** headers ***/
Header::SetHeaders();

/*** load the router ***/
$router = new AdminRouter;
/*** load the controller ***/
$router->loader();

?>