<!doctype html>
<head>
<title>Database Error</title>
<style type="text/css">
body {
	background-color:	#fff;
	margin:			20px;
	font-family:		sans-serif;
	font-size:		14px;
	color:			#000;
}
h3{
	color:#900000;
}
</style>
</head>
<body>
	<div>
		<h3><?php echo $heading ?></h3>
		<?php echo $msg ?><br>

		<?php
		if(Config::Get('debug'))
		{
			if(strlen($this->error) > 0) echo "MySQL Error".($this->errno?' #'.$this->errno:'').": ".$this->error.PHP_EOL;

			if ($line != '') echo "<br>DB.class on line: ".intval($line).PHP_EOL;

			if ($query != '') echo "<br><br>Query: <br><i><quote>".htmlentities($query)."</quote></i>".PHP_EOL;
		}
		?>
	</div>
</body>
</html>