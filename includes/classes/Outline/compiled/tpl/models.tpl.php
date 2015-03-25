<?php $outline = Outline::get_context(); $_pagetitle="BuxomCurves Models"; ?>
<?php $_keywords="buxom,buxom girls, buxom women, chubby, bbw, fat, fat girls, cute plumpers, fat belly, fat women"; ?>
<?php $_description="BuxomCurves description"; ?>
<?php $headerimgheight="292"; ?>

<?php $barcolor="F76474"; ?>

<?php $date="Date"; $name="Name"; $rating="Rating"; $sortby="Sort by"; ?>
<?php   ?>
<?php $outline_include_0 = new Outline('_header'); require $outline_include_0->get(); ?>

	<div id="headerimg"></div>

	<a name="videos"></a>
	<div class="headline_bar" align="left">
			<span class="right f11"><?php echo $sortby; ?>
			<?php if ($sorting != 'rating') { ?><a href="./models.php?sort=rating"><?php echo $rating; ?></a> |<?php } else { echo $rating; ?> |<?php } ?>
			<?php if ($sorting != 'modelname') { ?><a href="./models.php?sort=name"><?php echo $name; ?></a> |<?php } else { echo $name; ?> |<?php } ?>
			<?php if ($sorting != 'id') { ?><a href="./models.php?sort=id"><?php echo $date; ?></a><?php } else { echo $date; } ?></span>
			<span class="left bold"><?php echo ucwords($_page[templatename]); ?></span>
	</div>

	<div class="pagecontent">

			<?php foreach ($models as $model) { ?>
			<div class="thumbnail">
				<div>
					<a href="<?php echo $__siteurl; ?>/models/<?php echo $model['slug']; ?>">
						<div><span class="bold"><?php echo $model['modelname']; ?></span></div>
						<img src="<?php echo $__siteurl; echo $model['path']; ?>" width="<?php echo $model['width']; ?>" height="<?php echo $model['height']; ?>" alt="" border="1" />
					</a>
					<div id="test"><?php echo $model['description']; ?></div>
				</div>
				<?php if ($model[rating]) { ?>
					<div>
						<?php echo $rating; ?>: <?php echo $model['rating']; ?>
					</div>
				<?php } ?>
			</div>
			<?php } if (empty($models)) { ?>
				No models found.
			<?php } ?>

			<div class="c"></div>

			<?php if ($pagination) { ?>
			<div class="pagination">
				<?php echo $pagination['links']; ?>
			</div>
			<?php } ?>

			<div id="bottom">
				<div class="bottom-a">
					<a href="#">Get instant access to all the photos sets above and more by joining now!</a>
				</div>
				<div class="bottom-text">
					Your membership gives you full access to all our pinup gals, extended photo sets, exclusive behind-the-scenes shots, videos and more!
				</div>
			</div><!-- /bottom-->

	</div><!-- /pagecontent-->

<?php $outline_include_1 = new Outline('_footer'); require $outline_include_1->get(); Outline::finish(); ?>