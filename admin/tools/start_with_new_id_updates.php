<?php

/***database connect***/
require_once "../../includes/connect.inc.php";

		//do not allow inflation of ids
		$sql = "ALTER TABLE `bx_update` AUTO_INCREMENT = 0";
		$result = mysql_query($sql) or die("Error: ".mysql_error());
		unset($result);

/***close***/
mysql_close($conn);

echo "Okay. autoincrement-IDs for the updates table has been reset to 0.";


?>