<?php

define ('__INSTALLDIR', realpath(dirname(__FILE__)));
define ('__sitepath', realpath(dirname(__FILE__)."/.."));

//get the config variables
require_once __sitepath."/_config.php";

?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="EN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>BUXOM CMS Installation</title>
<style type="text/css">
body{
	margin:0px auto;
	text-align:center;
	font-size:1em;
	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
.buttonpanel{
	margin:20px 0px 10px 0px;
}
.buttonpanel input{
	padding:7px;
}
table{
	clear:both;
	margin-bottom:20px;
	width: 90%;
	background-color:#FFFBFB;
}
#page{
	border:2px solid #808080;
	-moz-border-radius:10px;-webkit-border-radius:10px;-o-border-radius:10px;
	-ms-border-radius:10px;-khtml-border-radius:6px;border-radius:10px;
	padding:20px;
	width:75%;
	margin:0px auto;
	margin-bottom:40px;
	background-color:#F0F0F0;
}

.fancy-button-reset-base-class,.fancy-button-base,button{
	font-family:"Lucida Grande",Lucida,Arial,sans-serif;background:url('/forum/images/button_bg.png') repeat-x bottom left;margin:0;width:auto;overflow:visible;display:inline-block;cursor:pointer;text-decoration:none;border-style:solid;font-weight:bold
}
.fancy-button-reset-base-class::-moz-focus-inner,.fancy-button-base::-moz-focus-inner,button::-moz-focus-inner{
	border:none;padding:0
}
.fancy-button-reset-base-class:focus,.fancy-button-base:focus,button:focus{outline:none}
.fancy-button-base,button{
	-moz-border-radius:6px;-webkit-border-radius:6px;-o-border-radius:6px;-ms-border-radius:6px;-khtml-border-radius:6px;border-radius:6px;font-size:18px;line-height:1.2em;padding:0.3em 1em;border-width:1px;background-color:#444;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #989898), color-stop(10%, #6a6a6a), color-stop(50%, #3c3c3c), color-stop(50%, #353535), color-stop(100%, #4e4e4e));background-image:-moz-linear-gradient(top, #989898 0%, #6a6a6a 10%, #3c3c3c 50%, #353535 50%, #4e4e4e 100%);background-image:linear-gradient(top, #989898 0%, #6a6a6a 10%, #3c3c3c 50%, #353535 50%, #4e4e4e 100%);border-color:#1e1e1e;text-shadow:#040404 0px 1px 1px;-moz-box-shadow:rgba(255,255,255,0.267) 0 0 0.1em 1px inset;-webkit-box-shadow:rgba(255,255,255,0.267) 0 0 0.1em 1px inset;-o-box-shadow:rgba(255,255,255,0.267) 0 0 0.1em 1px inset;box-shadow:rgba(255,255,255,0.267) 0 0 0.1em 1px inset;-moz-background-clip:padding;-webkit-background-clip:padding;-o-background-clip:padding-box;-ms-background-clip:padding-box;-khtml-background-clip:padding-box;background-clip:padding-box;margin:0 2px;vertical-align:middle}
.fancy-button-base,button,.fancy-button-base:visited,button:visited{
	color:#fff
}
.fancy-button-base:hover,button:hover,.fancy-button-base:focus,button:focus{
	background-color:#3c3c3c;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #919191), color-stop(10%, #636363), color-stop(50%, #353535), color-stop(50%, #2d2d2d), color-stop(100%, #474747));background-image:-moz-linear-gradient(top, #919191 0%, #636363 10%, #353535 50%, #2d2d2d 50%, #474747 100%);background-image:linear-gradient(top, #919191 0%, #636363 10%, #353535 50%, #2d2d2d 50%, #474747 100%);border-color:#161616;text-shadow:#000 0px 1px 1px
}
.fancy-button-base:hover,button:hover,.fancy-button-base:hover:visited,button:hover:visited,.fancy-button-base:focus,button:focus,.fancy-button-base:focus:visited,button:focus:visited{
	color:#fff
}
.fancy-button-base:active,button:active{
	background-color:#353535;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #353535), color-stop(30%, #3a3a3a), color-stop(50%, #303030), color-stop(50%, #282828), color-stop(100%, #494949));background-image:-moz-linear-gradient(top, #353535 0%, #3a3a3a 30%, #303030 50%, #282828 50%, #494949 100%);background-image:linear-gradient(top, #353535 0%, #3a3a3a 30%, #303030 50%, #282828 50%, #494949 100%);border-color:#0e0e0e;text-shadow:#000 0px -1px -1px;-moz-box-shadow:#1e1e1e 0 0.08em 0.1em 1px inset;-webkit-box-shadow:#1e1e1e 0 0.08em 0.1em 1px inset;-o-box-shadow:#1e1e1e 0 0.08em 0.1em 1px inset;box-shadow:#1e1e1e 0 0.08em 0.1em 1px inset
}
.fancy-button-base:active,button:active,.fancy-button-base:active:visited,button:active:visited{
	color:#fff
}
.light_button{
	background-color:#fff;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #ffffff), color-stop(100%, #cecece));background-image:-moz-linear-gradient(top, #ffffff 0%, #cecece 100%);background-image:linear-gradient(top, #ffffff 0%, #cecece 100%);border-color:#b7b7b7;text-shadow:#fff 0px 1px 1px;-moz-box-shadow:rgba(255,255,255,0.867) 0 0 0.1em 1px inset;-webkit-box-shadow:rgba(255,255,255,0.867) 0 0 0.1em 1px inset;-o-box-shadow:rgba(255,255,255,0.867) 0 0 0.1em 1px inset;box-shadow:rgba(255,255,255,0.867) 0 0 0.1em 1px inset;-moz-background-clip:padding;-webkit-background-clip:padding;-o-background-clip:padding-box;-ms-background-clip:padding-box;-khtml-background-clip:padding-box;background-clip:padding-box
}
.light_button,.light_button:visited{
	color:#222
}
.light_button:hover,.light_button:focus{
	background-color:#fff;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #fcfcfc), color-stop(100%, #c6c6c6));background-image:-moz-linear-gradient(top, #fcfcfc 0%, #c6c6c6 100%);background-image:linear-gradient(top, #fcfcfc 0%, #c6c6c6 100%);border-color:#afafaf;text-shadow:#fff 0px 1px 1px
}
.light_button:hover,.light_button:hover:visited,.light_button:focus,.light_button:focus:visited
{
	color:#222
}
.light_button:active{
	background-color:#fff;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #cbcbcb), color-stop(40%, #d3d3d3), color-stop(85%, #d3d3d3), color-stop(100%, #e5e5e5));background-image:-moz-linear-gradient(top, #cbcbcb 0%, #d3d3d3 40%, #d3d3d3 85%, #e5e5e5 100%);background-image:linear-gradient(top, #cbcbcb 0%, #d3d3d3 40%, #d3d3d3 85%, #e5e5e5 100%);border-color:#a7a7a7;text-shadow:#fff 0px -1px -1px;-moz-box-shadow:#b7b7b7 0 0.08em 0.1em 1px inset;-webkit-box-shadow:#b7b7b7 0 0.08em 0.1em 1px inset;-o-box-shadow:#b7b7b7 0 0.08em 0.1em 1px inset;box-shadow:#b7b7b7 0 0.08em 0.1em 1px inset
}
.light_button:active,.light_button:active:visited
{
	color:#222
}
.red_button{
	background-color:#b70300;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #e08180), color-stop(10%, #ce3836), color-stop(50%, #872321), color-stop(50%, #7b201e), color-stop(100%, #a42a28));background-image:-moz-linear-gradient(top, #e08180 0%, #ce3836 10%, #872321 50%, #7b201e 50%, #a42a28 100%);background-image:linear-gradient(top, #e08180 0%, #ce3836 10%, #872321 50%, #7b201e 50%, #a42a28 100%);border-color:#561615;text-shadow:#380100 0px 1px 1px;-moz-box-shadow:rgba(255,255,255,0.359) 0 0 0.1em 1px inset;-webkit-box-shadow:rgba(255,255,255,0.359) 0 0 0.1em 1px inset;-o-box-shadow:rgba(255,255,255,0.359) 0 0 0.1em 1px inset;box-shadow:rgba(255,255,255,0.359) 0 0 0.1em 1px inset;-moz-background-clip:padding;-webkit-background-clip:padding;-o-background-clip:padding-box;-ms-background-clip:padding-box;-khtml-background-clip:padding-box;background-clip:padding-box
}
.red_button,.red_button:visited
{
	color:#fff
}
.red_button:hover,.red_button:focus{
	background-color:#a80300;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #dd7573), color-stop(10%, #c43330), color-stop(50%, #7b201e), color-stop(50%, #6e1c1b), color-stop(100%, #972725));background-image:-moz-linear-gradient(top, #dd7573 0%, #c43330 10%, #7b201e 50%, #6e1c1b 50%, #972725 100%);background-image:linear-gradient(top, #dd7573 0%, #c43330 10%, #7b201e 50%, #6e1c1b 50%, #972725 100%);border-color:#491312;text-shadow:#280100 0px 1px 1px
}
.red_button:hover,.red_button:hover:visited,.red_button:focus,.red_button:focus:visited
{
color:#fff
}
.red_button:active{
	background-color:#990300;background-image:-webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(0%, #7b201e), color-stop(30%, #832220), color-stop(50%, #721d1c), color-stop(50%, #661a19), color-stop(100%, #9b2826));background-image:-moz-linear-gradient(top, #7b201e 0%, #832220 30%, #721d1c 50%, #661a19 50%, #9b2826 100%);background-image:linear-gradient(top, #7b201e 0%, #832220 30%, #721d1c 50%, #661a19 50%, #9b2826 100%);border-color:#3d100f;text-shadow:#190000 0px -1px -1px;-moz-box-shadow:#561615 0 0.08em 0.1em 1px inset;-webkit-box-shadow:#561615 0 0.08em 0.1em 1px inset;-o-box-shadow:#561615 0 0.08em 0.1em 1px inset;box-shadow:#561615 0 0.08em 0.1em 1px inset
}
.red_button:active,.red_button:active:visited
{
	color:#fff
}
</style>
</head>
<body align="center">

	<h1>BUXOM CMS Installation</h1>

<div id="page">

<?php


if(isset($_POST['step']) && $_POST['step']=='5')
{
//--------------------------
//step 4: Final (warning) Message and Links
//--------------------------
?>

	<h2>All done!</h2>

	<img src="/admin/img/icons/triangle_error.gif" alt="WARNING!" width="16" height="16">
	Delete the installation directory now!

	<br>
	<br>

	<a href="<?php echo $_config['__admindir'] ?>">Go to the Admin Dashboard</a>
<br>
	or
<br>
	<a href="<?php echo $_config['__siteurl'] ?>">Visit the Site</a>


<?php
}
elseif(isset($_POST['step']) && $_POST['step']=='4')
{
//--------------------------
//step 4: Admin Accounts (frontend / backend)
//--------------------------
?>
	<h4>Step 4/4</h4>


	<h4>Admin Panel Login</h4>

	<div>
		<strong>Username</strong>
	</div>
	<div>
		<input type="text" value="<?php if(isset($_POST['password'])) echo htmlspecialchars(strip_tags($_POST['password']), ENT_QUOTES); else echo 'admin'; ?>" size="20">
	</div>

	<div>
		<strong>Password</strong>
	</div>
	<div>
		<input type="password" value="" size="20">
	</div>

	<div>
		<strong>Confirm password</strong>
	</div>
	<div>
		<input type="password_2" value="" size="20">
	</div>


	<h4>Admin Member (Member #1)</h4>

	<div>
		<strong>Username</strong>
	</div>
	<div>
		<input type="text" value="<?php if(isset($_POST['password'])) echo htmlspecialchars(strip_tags($_POST['password']), ENT_QUOTES); else echo 'admin'; ?>" size="20">
	</div>

	<div>
		<strong>Password</strong>
	</div>
	<div>
		<input type="text" value="" size="20">
	</div>


	<form action="./index.php" method="post">
	<div class="buttonpanel">
		<input class="light_button" type="submit" value=" Continue ">
		<input name="step" type="hidden" value="5">
	</div>
	</form>
<?php
}
elseif(isset($_POST['step']) && $_POST['step']=='3')
{
//--------------------------
//step 3: Database inserts
//--------------------------
?>

	<h4>Step 3/4</h4>

	<h4>Insert Default Data</h4>



	<form action="./index.php" method="post">
	<div class="buttonpanel">
		<input class="light_button" type="submit" value=" Continue ">
		<input name="step" type="hidden" value="4">
	</div>
	</form>
<?php
}
elseif(isset($_POST['step']) && $_POST['step']=='2')
{
//--------------------------
//step 2: Database creation
//--------------------------
?>

	<h4>Step 2/4</h4>

	<h4>Database Creation</h4>



	<form action="./index.php" method="post">
	<div class="buttonpanel">
		<input class="light_button" type="submit" value=" Continue ">
		<input name="step" type="hidden" value="3">
	</div>
	</form>
<?php
}
else
{
//--------------------------
//step 1: Requirements check
//--------------------------

	$requirements = array();

	//php version
       $requirements['php']['title'] = "PHP";
	$requirements['php']['version'] = substr(PHP_VERSION, 0, 6);
	if($requirements['php']['version'] >= 5.2) {
	       $requirements['php']['pass'] = 1;
	}
	else{
		$requirements['php']['pass'] = 0;
	}

	//curl
       $requirements['curl']['title'] = "Curl Library";
	if(function_exists('curl_version'))
	{
		$curlinfo = curl_version();
	       $requirements['curl']['version'] = $curlinfo['version']."<br>".$curlinfo['ssl_version'];
	}
	else
	       $requirements['curl']['version'] = '';
	if (!extension_loaded('curl')){
	       $requirements['curl']['pass'] = 0;

	}
	else
	       $requirements['curl']['pass'] = 1;

	//apache mod rewrite
	$requirements['mod_rewrite']['title'] = "Apache Mod Rewrite";
	$ver = split("[/ ]", htmlentities(strip_tags($_SERVER['SERVER_SOFTWARE'])));
	$apver = "$ver[1] $ver[2]";
	$requirements['mod_rewrite']['version'] = $apver;
	if (in_array('mod_rewrite',apache_get_modules()))
	       $requirements['mod_rewrite']['pass'] = 1;
	else
	       $requirements['mod_rewrite']['pass'] = 0;

	//check imagemagick
	require_once __sitepath."/admin/includes/classes/Img_Adapter.class.php";
	Img_Adapter::detectImageLibrary();
	$requirements['imagemagick']['title'] = "Imagemagick Library";
	$requirements['imagemagick']['version'] = "requires ImageMagick 6.3.8-2+";
	if (Img_Adapter::HasImagick())
	       $requirements['imagemagick']['pass'] = 1;
	else
	       $requirements['imagemagick']['pass'] = 0;

	$gdinfo = gd_info();
	//check GD
	$requirements['gd']['title'] = "GD Library - alternative to ImageMagick, but soo much slower!";
	$requirements['gd']['version'] = $gdinfo['GD Version'];
	if (Img_Adapter::HasGD())
	       $requirements['gd']['pass'] = 1;
	else
	       $requirements['gd']['pass'] = 0;

	//mplayer
	$requirements['mplayer']['title'] = "Mplayer - used to extract video screenshots";
	$requirements['mplayer']['version'] = "";
	$mplayer = $_config['_mplayerpath'];		//path to mplayer executable
	if ($mplayer && is_executable($mplayer))
	       $requirements['mplayer']['pass'] = 1;
       else
	       $requirements['mplayer']['pass'] = 0;

	//ffmpeg
	$requirements['ffmpeg']['title'] = "FFMpeg - slow as a turtle compared to Mplayer!";
	$requirements['ffmpeg']['version'] = "";
	$ffmpeg = $_config['_ffmpegpath'];			//path to ffmpeg executable
	if ($ffmpeg && is_executable($ffmpeg))
	       $requirements['ffmpeg']['pass'] = 1;
       else
	       $requirements['ffmpeg']['pass'] = 0;

	//yamdi
	$requirements['yamdi']['title'] = "Yamdi - used to read and inject FLV metadata";
	$requirements['yamdi']['version'] = "";
	$yamdi = $_config['_yamdipath'];			//path to yamdi executable
	if ($yamdi && is_executable($yamdi))
	       $requirements['yamdi']['pass'] = 1;
       else
	       $requirements['yamdi']['pass'] = 0;


	//directory permissions
	$directories = array(
		'cache' => array(
			'title'=>'Cache directory',
			'path'=> __sitepath.'/includes/cache'
			),
		'free' => array(
			'title'=>'Free content directory',
			'path' => __sitepath.$_config['__freedir'],
			),
		'thumb' => array(
			'title'=>'Thumbnail directory',
			'path' => __sitepath.$_config['__thumbdir'],
			),
		'member' => array(
			'title'=>'Member directory',
			'path' => $_config['__memberpath'],
			),
		'admintemp' => array(
			'title'=>'Admin temporary directory',
			'path' => __sitepath.$_config['__admindir'].'/temp',
			),
		);

	foreach($directories as $key => $val)
	{
		if(is_writable($val['path'])) {
			$directories[$key]['pass'] = 1;
		}
		else
			$directories[$key]['pass'] = 0;
	}


	$ok = "<img src='/admin/img/icons/accept.png' alt='ok' border='0' width='16' height='16'>";
	$fail = "<img src='/admin/img/icons/delete.gif' alt='error' border='0' width='16' height='16'>";

?>
	<h4>Step 1/4</h4>


	First <strong>edit $_config.php</strong>. Go through all the options in that file.


	<br>
	<br>
	<br>


	A quick requirements check shows that...<br>
	<table border="1" align="center" cellpadding="10" cellspacing="0">
	<thead>
	<th width="50%">Requirement</th>
	<th width="25%">Version</th>
	<th>Passed</th>
	</thead>
	<tbody>
	<?php foreach($requirements as $r)
	{
		echo "<tr><td>" . $r['title'] . "</td><td>" . $r['version'] . "</td><td>" . ($r['pass'] ? $ok : $fail) . "</td></tr>";
	} ?>
	<tr><td colspan="3" align="center">Directories (must be writable by server - start with `chmod 777` and try lower permissions, if possible)</td></tr>
	<?php foreach($directories as $d)
	{
		echo "<tr><td>" . $d['title'] . "</td><td>" . $d['path'] . "</td><td>" . ($d['pass'] ? $ok : $fail) . "</td></tr>";
	} ?>
	<tr><td colspan="3" align="left">Database Connection</td></tr>
	<?php
	$msg = '';
	if (!mysql_connect())
	{
		$msg = mysql_error();
		$db['pass'] = 0;
	}
	else
	{
		$db['pass'] = 1;
	}

	$anon_pass = '';
	for($i = 0, $s = strlen($_config['db']['__dbpass']); $i<$s; $i++)
	{
		$anon_pass .= '*';
	}

	echo "<tr><td colspan='2' align='left'>Database Connection<br>
		Host: ".$_config['db']['__dbhost']."<br>
		User: ".$_config['db']['__dbuser']."<br>
		Pass: ".$anon_pass."<br>
		Database: ".$_config['db']['__dbname']."
		<br>" . $msg . "</td><td>".
		($db['pass'] ? $ok : $fail) . "</td></tr>";
	?>
	</tbody>
	</table>


	<form action="./index.php" method="post">
	<div class="buttonpanel">

		<input class="light_button" type="submit" value=" Continue ">
		<input name="step" type="hidden" value="2">

	</div>
	</form>

</div>

</body>
</html>
<?php
} //requirements

