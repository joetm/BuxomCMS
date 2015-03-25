<?php $outline = Outline::get_context(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="EN" xml:lang="EN">






<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtoupper(__CHARSET); ?>" />

<title><?php echo $_pagetitle; ?> <?php echo $modelname; ?></title>

<meta name="robots" content="INDEX,FOLLOW" />

<meta name="title" content="<?php echo $_pagetitle; ?>" />

<meta name="keywords" content="<?php echo $_keywords; ?>" />
<meta name="description" content="<?php echo $_description; ?>" />

<meta name="date" content="<?php echo date('m-d-Y'); ?>" />

<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />

<meta http-equiv="imagetoolbar" content="false" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<meta http-equiv="content-language" content="EN" />






<meta name="publisher" content="<?php echo $__sitename; ?>" />
<meta name="DC.Publisher" content="<?php echo $__sitename; ?>" />

<meta name="gg.show-translate" content="no" />
<meta name="gg.show-archive" content="no" />
<meta name="gg.show-similar" content="no" />
<meta name="gg.show-url" content="yes" />

<meta name="revisit-after" content="7 days" />
<meta name="author" content="<?php echo $__sitename; ?> - <?php echo $__siteurl; ?>" />
<meta name="copyright" content="<?php echo $__shortname; ?>" />
<meta name="distribution" content="global" />
<meta name="resource-type" content="document" />

	<link rel="stylesheet" id="site-css" href="<?php echo $__templateurl; ?>/style.css" type="text/css" media="all" />
<?php if ($_page[templatename] == 'login') { ?>
	<link rel="stylesheet" id="login-css" href="<?php echo $__templateurl; ?>/login.css" type="text/css" media="all" />
<?php } ?>

<link rel="shortcut icon" href="<?php echo $__siteurl; ?>/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $__siteurl; ?>/favicon.gif" type="image/gif" />

<script type="text/javascript"><!-- 
	if (window.top!=window.self){window.top.location=window.self.location;}
//-->
</script>

<script type="text/javascript" src="<?php echo $__siteurl; ?>/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $__siteurl; ?>/js/jquery.equalheights.js"></script>

<?php if ($_page[templatename] == 'video') { ?>
	<!-- VideoJS Library -->
	<script src="<?php echo $__siteurl; ?>/player/videoJS/video.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" charset="utf-8">
		$(function(){
			VideoJS.setup();
		})
	</script>
	<!-- VideoJS Stylesheet -->
	<link rel="stylesheet" href="<?php echo $__siteurl; ?>/player/videoJS/video-js.css" type="text/css" media="screen" title="Video JS" charset="utf-8">
<?php } ?>







<script type="text/javascript"><!--
$(document).ready(function() {
	<?php if ($_page[templatename] == 'photos') { ?>
		$(".g-title").equalHeights(10,40);
	<?php } else if ($_page[templatename] == 'videos') { ?>
		$(".g-title").equalHeights(10,20);
	<?php } else if ($_page[templatename] == 'models') { ?>
		$(".g-title").equalHeights(10,20);
	<?php } else if ($_page[templatename] == 'modelprofile') { ?>
		$(".g-title").equalHeights(18,35);
	<?php } else if ($_page[templatename] == 'map') { ?>
		$(".g-title").equalHeights(10,20);
	<?php } ?>

	<?php if ($_page[templatename] == 'set' OR $_page[templatename] == 'video') { ?>




	<?php } ?>

	<?php if ($_page[templatename] == 'home') { ?>
	/*Page Flip on hover*/
	$("#pageflip").hover(function() {
		$("#pageflip img , .msg_block").stop()
			.animate({
				width: '307px',
				height: '319px'
			}, 500);
		} , function() {
		$("#pageflip img").stop()
			.animate({
				width: '50px',
				height: '52px'
			}, 220);
		$(".msg_block").stop()
			.animate({
				width: '50px',
				height: '50px'
			}, 200);
	});
	<?php } ?>

});
//-->
</script>

<?php if ($_page[templatename] == 'home') { ?>

<style type="text/css"><!--
/*IE png fix*/
img { behavior: url(<?php echo $__siteurl; ?>/js/iepngfix.htc) }
#pageflip {
	position: relative;
	right: 0;
	top: 0;
	float: right;
	z-index:99;
}
#pageflip img {
	width: 50px;
	height: 52px;
	z-index: 99;
	position: absolute;
	right: 0;
	top: 0;
	-ms-interpolation-mode: bicubic;
}
#pageflip .msg_block {
	width: 50px;
	height: 50px;
	overflow: hidden;
	position: absolute;
	right: 0;
	top: 0;
	background: url(<?php echo $__siteurl; ?>/img/peel_video-formats.png) no-repeat right top;
}
#pageflip a img{
	border:0px;
}
//-->
</style>
<?php } ?>

<?php if ($_page[templatename] == 'video') { ?>
    <script type="text/javascript" src="<?php echo $__siteurl; ?>/js/jquery.lightbox-0.5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $__siteurl; ?>/img/lightbox/jquery.lightbox-0.5.css" media="screen" />
    <script type="text/javascript"><!--
    $(function() {
        $('#videocontainer a').lightBox();
    });
    //-->
    </script>
<?php } ?>

<style type="text/css"><!--
<?php if ($barcolor) { ?>
	#headerimg, .headline_bar {
		background-color:#<?php echo $barcolor; ?>;
	}
<?php } ?>
<?php if ($barfontcolor) { ?>
	.headline_bar a{
		color:#<?php echo $barfontcolor; ?>;
	}
<?php } ?>

<?php if ($_page[templatename]) { ?>
#headerimg{
	background-image:url(<?php echo $__siteurl; ?>/img/header/<?php if ($_page[templatename] == 'modelprofile') { echo strtolower($shortmodelname); } else { echo strtolower($_page[templatename]); } ?>.jpg);
	height:<?php echo $headerimgheight; ?>px;
}
<?php } ?>
//-->
</style>

<!--ICRA+RTA-->
<meta name="RATING" content="RTA-5042-1996-1400-1577-RTA" />
<link rel="meta" href="<?php echo $__siteurl; ?>/labels.rdf" type="application/rdf+xml" title="ICRA labels" />
<meta http-equiv="pics-Label" content='(pics-1.1 "http://www.icra.org/pics/vocabularyv03/" l gen true for "http://buxomcurves.com" r (n 3 s 3 v 3 l 3 oa 2 ob 2 oc 2 od 2 oe 2 of 2 og 2 oh 2 c 3) gen true for "http://www.buxomcurves.com" r (n 3 s 3 v 3 l 3 oa 2 ob 2 oc 2 od 2 oe 2 of 2 og 2 oh 2 c 3))' />
<!--/ICRA+RTA-->

</head>
<body>


<?php if ($_page[templatename] == 'home') { ?>
	<div id="pageflip">
		<a href="./join.php"><img src="<?php echo $__siteurl; ?>/img/peel_page_flip.png" alt="" border="0" /></a>
		<div class="msg_block"></div>
	</div>
<?php } ?>


<a name="top"></a>
<div align="center">


<div id="content">

	<div id="top-header">

		<!-- top-right-->
		<div id="#login">
			<form name="loginform" id="loginform" action="/members/" method="post">
				<label><?php echo $_username; ?>
				<input name="username" id="user_login" class="input" value="" size="20" tabindex="101" type="text" /></label>

				<label><?php echo $_password; ?>
				<input name="password" id="user_pass" class="input" value="" size="20" tabindex="102" type="password" /></label>

				<input name="submit" id="submit" class="login-button" value="<?php echo $_login; ?>" tabindex="103" type="submit" />
			</form>
		</div><!-- /login-->

		<!-- statistics -->
		<div id="headerstatistics">
			





			<?php if ($total_size) { echo $total_size; } if ($total_size && $total_pics) { ?>, <?php } ?>
			<?php if ($total_pics) { echo $total_pics; ?> <?php echo $_pictures; } if ($total_pics && $total_videos) { ?>, <?php } ?>
			<?php if ($total_videos) { echo $total_videos; ?> <?php echo $_videos; } ?>
		</div>
		<!-- statistics -->
		<!-- /top-right-->

	</div><!-- /top-header -->


	<div id="menu-container">
		<ul class="menu">
			<li>
				<a href="<?php echo $__siteurl; ?>/index.php"><?php echo $_home; ?></a>
			</li>
			<li>
				<a href="<?php echo $__siteurl; ?>/photos.php"><?php echo $_photos; ?></a>
			</li>
			<li>
				<a href="<?php echo $__siteurl; ?>/videos.php"><?php echo $_videos; ?></a>
			</li>
			<li>
				<a href="<?php echo $__siteurl; ?>/models.php"><?php echo $_models; ?></a>
			</li>
			<li>
				<a href="<?php echo $__siteurl; ?>/about.php"><?php echo $_about; ?></a>
			</li>
			<li>
				<a href="<?php echo $__siteurl; ?>/goodies.php"><?php echo $_goodies; ?></a>
			</li>
			<li>
				<a href="<?php echo $__siteurl; ?>/members/"><?php echo $_members; ?></a>
			</li>
			<li>
				<a href="<?php echo $__siteurl; ?>/join.php"><?php echo $_join; ?>!</a>
			</li>
		</ul><!-- /menu -->
	</div><!-- /menu-container-->
<?php Outline::finish(); ?>