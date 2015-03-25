<?php
/*** include the init.php file ***/
require realpath( dirname(__FILE__).'/../_init.php' );

echo "url:site/set<br>";
var_dump(Path::Get('url:site/set'));