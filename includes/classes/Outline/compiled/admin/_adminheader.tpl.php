<?php $outline = Outline::get_context(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtoupper(__CHARSET); ?>" />

<title>Admininstration - <?php echo $_page[title]; ?></title>

<meta name="title" content="<?php echo $_page[title]; ?>" />
<meta name="description" content="BuxomCMS Administration Panel" />

<meta name="robots" content="NOINDEX,NOFOLLOW" />
<meta name="author" content="<?php echo $__sitename; ?> - <?php echo $__siteurl; ?>" />
<meta name="copyright" content="<?php echo $__shortname; ?>" />
<meta name="generator" content="BuxomCMS <?php echo __VERSION; ?>" />

<link href="<?php echo $__siteurl; ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href="<?php echo $__siteurl; ?>/favicon.gif" rel="shortcut icon" type="image/gif" />

<script type="text/javascript"><!--
	if (window.top!=window.self){window.top.location=window.self.location;}
//-->
</script>

<link href="<?php echo $__adminurl; ?>/admin.css" type="text/css" rel="stylesheet" media="all" />
<?php $outline_include_0 = new Outline('includes/header_login'); require $outline_include_0->get(); ?>

<script type="text/javascript" src="<?php echo $__siteurl; ?>/js/jquery.js"></script>

<?php if ($_page[templatename] == 'admin_update' || $_page[templatename] == 'admin_model') { ?>
	<?php $outline_include_1 = new Outline('includes/header_new'); require $outline_include_1->get(); ?>
<?php } ?>

<?php if ($_page[templatename] == 'admin_newupdate_step2_pics') { ?>
	<?php $outline_include_2 = new Outline('includes/header_step2_pics'); require $outline_include_2->get(); ?>
<?php } ?>

<?php if ($_page[templatename] == 'admin_editpics') { ?>
	<?php $outline_include_3 = new Outline('includes/header_editpics'); require $outline_include_3->get(); ?>
<?php } ?>

<?php if ($_page[templatename] != 'admin_home' && $_page[templatename] != 'admin_tags' && $_page[templatename] != 'admin_newupdate_step2_pics' && $_page[templatename] != 'admin_newupdate_step2_video') { ?>
	<?php $outline_include_4 = new Outline('includes/header_masks'); require $outline_include_4->get(); ?>
<?php } ?>

<?php if ($_page[templatename] == 'admin_update' || $_page[templatename] == 'admin_faq' || $_page[templatename] == 'admin_comments') { ?>
	<?php $outline_include_5 = new Outline('includes/header_datepicker'); require $outline_include_5->get(); ?>
<?php } ?>

<?php if ($_page[templatename] == 'admin_update' || $_page[templatename] == 'admin_model') { ?>
	<?php $outline_include_6 = new Outline('includes/header_rating'); require $outline_include_6->get(); ?>
<?php } ?>

<?php if ($_page[templatename] != 'admin_update' && $_page[templatename] != 'admin_model' && $_page[templatename] != 'admin_structure' && $_page[templatename] != 'admin_login' && $_page[templatename] != 'admin_newupdate_step2_pics' && $_page[templatename] != 'admin_newupdate_step2_video') { ?>
	<link type="text/css" href="<?php echo $__siteurl; ?>/img/datatable/table.css" rel="stylesheet" />
<?php } ?>

<?php if ($_page[templatename] != 'admin_structure' && $_page[templatename] != 'admin_newupdate_step2_pics' && $_page[templatename] != 'admin_newupdate_step2_video' && $_page[templatename] != 'admin_home') { ?>
	<?php $outline_include_7 = new Outline('includes/header_all'); require $outline_include_7->get(); ?>
<?php } ?>

<?php if ($_page[templatename] == 'admin_docs') { ?>
	<script type="text/javascript" src="<?php echo $__siteurl; ?>/js/overlib/overlib_mini.js"></script>
<?php } ?>

</head>
<body>
<div align="center">
	<a name="top"></a>


	<div id="content">

		<div id="top-header">

			<div id="login">
				<a href="<?php echo $__siteurl; ?>"><?php echo $_visitsite; ?></a>
			</div><!-- /login-->

			<!-- top-right-->
			<div class="adminheaderstats">
				<?php if ($total_size) { echo $total_size; } if ($total_size && $num_pics) { ?>, <?php } ?>
				<?php if ($num_pics) { echo $num_pics; ?> <?php echo $_pictures; } if ($num_pics && $num_videos) { ?>, <?php } ?>
				<?php if ($num_videos) { echo $num_videos; ?> <?php echo $_videos; } ?>
			</div>
			<!-- /top-right-->

		</div><!-- /top-header -->

		<?php if ($_page[templatename] != 'admin_login') { ?>
			<div id="menu-container" align="left">
				<ul class="menu">
					<li class="menu-item<?php if ($_page[templatename] == 'admin_dashboard') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/home"><?php echo $_home; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_members') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/members" title="<?php echo $_editmembers; ?>"><?php echo $_members; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_updates') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/updates" title="<?php echo $_editupdates; ?>"><?php echo $_updates; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_update') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/update" title="<?php echo $_addnewupdate; ?>"><?php echo $_newupdate; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_models') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/models" title="<?php echo $_editmodels; ?>"><?php echo $_models; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_model') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/model" title="<?php echo $_addnewmodel; ?>"><?php echo $_newmodel; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_comments') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/comments" title="<?php echo $_editcomments; ?>"><?php echo $_comments; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_tags') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/tags" title="<?php echo $_edittags; ?>"><?php echo $_tags; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_ratings') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/ratings" title="<?php echo $_ratings; ?>"><?php echo $_ratings; ?></a>
					</li>





					<li class="menu-item<?php if ($_page[templatename] == 'admin_docs') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/docs" title="2257">2257</a>
					</li>
					<li class="menu-item<?php if (preg_match('~^admin_options_~', $_page[templatename])) { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_general" title="<?php echo $_options; ?>"><?php echo $_options; ?></a>
					</li>
					<li class="menu-item">
						<a href="<?php echo $__adminurl; ?>/logout"><?php echo $_logout; ?></a>
					</li>
				</ul><!-- /menu -->

			</div><!-- /menu-container-->
		<?php } ?>

		<div class="c"></div><?php Outline::finish(); ?>