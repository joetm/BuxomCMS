<?php $outline = Outline::get_context(); $_pagetitle="BuxomCurves Goodies: Free sexy buxom Wallpapers"; ?>
<?php $_keywords=""; ?>
<?php $_description="BuxomCurves BBW Wallpapers"; ?>
<?php $headerimgheight="265"; ?>
<?php $barcolor="c33a41"; ?>



<?php $outline_include_0 = new Outline('_header'); require $outline_include_0->get(); ?>

	<div id="headerimg">
		<div style="float:left;margin:0px;margin-top:155px;margin-left:45px;color:#000000;" align="left">
			<div class="f32">Uhhh yeah!</div>
			<div class="f12">You found the good spot!</div>
		</div>
	</div><!-- /headerimg-->

	<div class="headline_bar" align="left">
		<div class="bold"><?php echo $_t['wallpapers']; ?></div>
	</div>

	<div class="pagecontent">

			<?php Outline::register_function('outline__user_tpl_goodies_wpcontainer', 'wpcontainer'); if (!function_exists('outline__user_tpl_goodies_wpcontainer')) { function outline__user_tpl_goodies_wpcontainer($args) { extract($args+array("name" => '', "id" => ''));  ?>
				<div class="wpcontainer">
					<img src="/wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/thumb.jpg" alt="" width="148" height="111" border="2" />
				 	<ul>
						<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1920x1200.jpg">1920 x 1200</a></li>
						<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1920x1080.jpg">1920 x 1080</a></li>
						<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1680x1050.jpg">1680 x 1050</a></li>
						<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1440x960.jpg">1440 x 960</a></li>
						<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1440x900.jpg">1440 x 900</a></li>
						<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1280x1024.jpg">1280 x 1024</a></li>
						<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1280x800.jpg">1280 x 800</a></li>
				 		<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/1024x768.jpg">1024 x 768</a></li>
				 		<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/800x600.jpg">800 x 600</a></li>
				 		<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/480x320.jpg">480 x 320</a></li>
				 		<li><a href="./wallpapers/<?php echo $name; ?>/<?php echo $id; ?>/320x240.jpg">320 x 240</a></li>
					</ul>
				</div>
			<?php } } ?>

			<!--bianca-->
			<?php for ($i=1; $i<=2; $i+=1) { ?> 
				<?php Outline::dispatch('wpcontainer', array("name" => "bianca", "id" => $i)); ?>
			<?php } ?>
			<!--vanessa-->
			<?php for ($i=1; $i<=1; $i+=1) { ?> 
				<?php Outline::dispatch('wpcontainer', array("name" => "vanessa", "id" => $i)); ?>
			<?php } ?>
			<!--milla-->
			<?php for ($i=1; $i<=2; $i+=1) { ?>
				<?php Outline::dispatch('wpcontainer', array("name" => "milla", "id" => $i)); ?>
			<?php } ?>
			<!--juliette-->
			<?php for ($i=1; $i<=2; $i+=1) { ?>
				<?php Outline::dispatch('wpcontainer', array("name" => "juliette", "id" => $i)); ?>
			<?php } ?>
			<!--silvie-->
				<?php Outline::dispatch('wpcontainer', array("name" => "silvie", "id" => 1)); ?>
			<!--lucy-->
			<?php for ($i=1; $i<=2; $i+=1) { ?>
				<?php Outline::dispatch('wpcontainer', array("name" => "lucy", "id" => $i)); ?>
			<?php } ?>


			<div class="c"></div>

			<div id="bottom">
				<div class="bottom-a">
					<a href="./#">Get instant access to all the photos sets above and more by joining now!</a>
				</div>
				<div class="bottom-text">
					Your membership gives you full access to all our pinup gals, extended photo sets, exclusive behind-the-scenes shots, videos and more!
				</div>
			</div><!-- /bottom-->

	</div><!-- /pagecontent-->

<?php $outline_include_1 = new Outline('_footer'); require $outline_include_1->get(); Outline::finish(); ?>