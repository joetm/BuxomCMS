<?php $outline = Outline::get_context(); $_pagetitle="BuxomCurves.com"; ?>
<?php $_keywords="buxom,buxom girls, buxom women, chubby, bbw, fat, fat girls, cute plumpers, fat belly, fat women"; ?>
<?php $_description="BuxomCurves.com - Because chubbies are better!"; ?>
<?php $headerimgheight="204"; ?>
<?php $barcolor="261512"; ?>

<?php $blogger="Blogger"; $comingsoon="Coming Soon"; $flickr="Flickr"; $followus="Follow us"; $followuson="Follow us on"; $lastupdate="Latest Update"; $more="more"; $myspace="Myspace"; $newestcomments="Newest Comments"; $news="News"; $nocomments="No comments, yet."; $ourmodels="Our Models"; $rating="Rating"; $recentupdates="Recent Updates"; $reportcomment="Report this comment"; $said="said"; $twitter="Twitter"; $youtube="Youtube"; $welcomemessage="Welcome"; ?>

<?php $outline_include_0 = new Outline('_header'); require $outline_include_0->get(); ?>

	<div id="headerimg">
		<div style="float:right;margin:0px;margin-top:32px;margin-right:45px;color:#FFFFFF;text-align:right;">
			<div>
				<span class="f18">bux·om [b&#365;k&#769;s&#600;m]</span>
			</div>
			<div>
				<span class="f14">
					<strong>a.</strong> Healthily plump and ample of figure.<br />
					<strong>b.</strong> Full-bosomed.<br />
					<br />
					<div style="width:350px;font-style:italic;">"A generation ago, fat babes were considered healthy and buxom actresses were popular, but society has since come to worship thinness." (Robert A. Hamilton)</div>
				</span>
			</div>
		</div>
	</div><!-- /headerimg-->

	<div class="pagecontent">

			<div id="content-right">

				<div class="headline_bar">
					<?php echo $welcomemessage; ?>!
				</div>
				<div align="justify">
					Welcome to the newly re-booted, re-launched and revamped PinupFiles/PinupGlam, the biggest and most comprehensive modern-day big bust pinup site on the web! At long last, we have merged BOTH sites PinupFiles.Com and PinupGlam.Com together into one big mega-site to feature ALL of our photos and videos that we've ever done in our entire six and a half year history! We specialize in bringing you nothing but the hottest, sexiest, the most beautiful big-bust gals on the planet, and our lineup of amazing ladies is second-to-none. We have tons of brand new features including search functions, ratings and ranking systems, a personal favorites section where you can make your very own exclusive playlists, and much more!

					With over 100 gigabytes of amazing photos and videos of our incomparable models, we will be be adding new photo sets and videos every single day for the entire month of September, in addition to our regular new weekly updates. Feel free to click around and check everything out and let us know what you think, as all content now has a feedback feature where you can rank your favorite videos and photo sets so we know which gals you like best and what we should feature more often.

					Want to find out more about us and our incredible lineup of amazing models? Take our tour below...
				</div>


				<div class="headline_bar">
						<span class="right"><a href="<?php echo $__siteurl; ?>/models.php"><?php echo $more; ?>...</a></span>
						<span class="left bold"><?php echo $ourmodels; ?></span>
				</div>

				<?php foreach ($models as $model) { ?>
			 	<div class="thumbnail" style="width:220px">
			 		<a href="<?php echo $__siteurl; ?>/model.php?id=<?php echo $model['id']; ?>">
			 			<img src="<?php echo $model['path']; ?>" width="<?php echo $model['width']; ?>" height="<?php echo $model['height']; ?>" alt="" border="1" />
			 		</a>
			 		<div class="right smallfont" style="padding-right:10px;"><?php echo $rating; ?>: <?php echo $model['rating']; ?></div>
		 			<div class="left" style="padding-left:10px"><?php echo $model['modelname']; ?></div>
					<div class="c"></div>
			 	</div>
				<?php } ?>
				<div class="c"></div>

				<?php if ($myspace_user || $twitter_user || $youtube_user || $blogger_user || $flickr_user) { ?>
					<div class="headline_bar">
						<?php echo $followus; ?>...
					</div>
					<?php if ($myspace_user) { ?>
						<a href="http://www.myspace.com/<?php echo $myspace_user; ?>"><img src="<?php echo $__siteurl; ?>/img/logos/myspace.png" class="socneticon" width="104" height="28" alt="<?php echo $myspace_user; ?>" border="0" /></a>
					<?php } ?>
					<?php if ($twitter_user) { ?>
						<a href="http://twitter.com/<?php echo $twitter_user; ?>"><img src="<?php echo $__siteurl; ?>/img/logos/twitter.png" class="socneticon" width="116" height="24" alt="<?php echo $twitter_user; ?>" border="0" /></a>
					<?php } ?>
					<?php if ($youtube_user) { ?>
						<a href="http://www.youtube.com/user/<?php echo $youtube_user; ?>"><img src="<?php echo $__siteurl; ?>/img/logos/youtube.png" class="socneticon" width="70" height="28" alt="<?php echo $youtube_user; ?>" border="0" /></a>
					<?php } ?>
					<?php if ($blogger_user) { ?>
						<a href="<?php echo $blogger_user; ?>" target="_blank"><img src="<?php echo $__siteurl; ?>/img/logos/blogger.png" class="socneticon" width="98" height="28" alt="<?php echo $blogger_user; ?>" border="0" /></a>
					<?php } ?>
					<?php if ($flickr_user) { ?>
						<a href="<?php echo $flickr_user; ?>" target="_blank"><img src="<?php echo $__siteurl; ?>/img/logos/flickr.gif" class="socneticon" width="98" height="28" alt="<?php echo $flickr_user; ?>" border="0" /></a>
					<?php } ?>
				<?php } ?>

				<div class="headline_bar">
						<span class="right"><a href="<?php echo $__siteurl; ?>/photos.php"><?php echo $more; ?>...</a></span>
						<span class="left bold"><?php echo $recentupdates; ?></span>
				</div>

				<div class="thumbs">
					<?php foreach ($updates as $update) { ?>
			 		<div class="thumbnail">
			 		
			 			<a href="<?php echo $__siteurl; ?>/<?php if ($update.type == 'video') { ?>video<?php } else { ?>set<?php } ?>.php?id=<?php echo $update['id']; ?>">
			 				<img src="<?php echo $update['path']; ?>" width="<?php echo $update['width']; ?>" height="<?php echo $update['height']; ?>" alt="" border="1" />
			 				<div class="g-title"><?php echo $update['title']; ?></div>
			 			</a>
			 			<div class="right smallfont" style="padding-right:10px;"><?php echo $rating; ?>: <?php echo $update['rating']; ?></div>
			 			<div class="left smallfont" style="padding-left:10px;"><?php echo $update['date']; ?></div>
						<div class="c"></div>
			 		</div>
					<?php } ?>
					<div class="c"></div>
				</div><!-- /thumbs-->

			</div><!-- /content-right-->


			<div id="sidebar">

				<div>
					<div class="headline_bar">
						<?php echo $lastupdate; ?>
					</div>

					<div class="subbox">

						<?php if ($latestupdate) { ?>
							<div>
							<a href="<?php echo $__siteurl; ?>/<?php if ($latestupdate[type] == 'pics') { ?>set<?php } else { ?>video<?php } ?>.php?id=<?php echo $latestupdate['id']; ?>">
								<div style="padding-bottom:7px;"><?php echo $latestupdate['title']; ?></div>
								<img src="<?php echo $latestupdate['path']; ?>" width="<?php echo $latestupdate['width']; ?>" height="<?php echo $latestupdate['height']; ?>" alt="" border="1" />
							</a>
							</div>
						<?php } ?>

					</div><!-- /subbox-->
				</div>

				<?php if ($twitts) { ?>
					<div class="headline_bar">
						<?php echo $news; ?>
					</div>
					
					<div class="subbox">
							<div class="comment">
								
								<?php foreach ($twitts as $twit) { ?>
									<?php echo $twit['user']; ?>@<?php echo $twit['date']; ?>: <?php echo $twit['message']; ?> <br />
									<hr />
								<?php } ?>
							</div>
					</div>
				<?php } ?>

					<div class="headline_bar">
						<?php echo $newestcomments; ?>
					</div>
					<div class="subbox f11">
							<?php if ($comments) { ?>
								<?php foreach ($comments as $comment) { ?>
								<div class="comment">
									
									


									<div class="left">
										<span class="cdate">On <?php echo $comment['date']; ?></span> <span class="cname"><?php echo $comment['name']; ?></span> <?php echo $said; ?>:
										<div class="c"></div>
										<?php echo $comment['content']; ?>
									</div>
									<div class="right">
										<a href="<?php echo $__siteurl; ?>/report.php?id=<?php echo $comment['id']; ?>" class="report"><img src="<?php echo $__siteurl; ?>/img/icons/warning.png" title="<?php echo $reportcomment; ?>" width="12" height="12" border="0" /></a>
									</div>
									<div class="c">
										<hr />
									</div>
								</div>
								<?php } ?>
							<?php } else { ?>
								<div class="smallfont"><?php echo $nocomments; ?></div>
							<?php } ?>
					</div><!-- /subbox-->

					<?php if ($comingsoon) { ?>
						<div class="headline_bar">
							<?php echo $comingsoon; ?>
						</div>
						<div class="subbox">
							<?php foreach ($comingsoon as $cs) { ?>
							<div>
								<a href="<?php echo $__siteurl; ?>/<?php if ($cs[type] == 'pics') { ?>set<?php } else { ?>video<?php } ?>.php?id=<?php echo $cs['id']; ?>">
									<div style="padding-bottom:7px;"><span class="right f10" style="padding-right:10px;"><?php echo $cs['date']; ?></span><span class="left"><?php echo $cs['title']; ?></span></div>
									<img src="<?php echo $cs['thumb3']; ?>" width="225" height="285" alt="" border="1" />
								</a>
							</div>
							<?php } ?>
						</div><!-- /subbox-->
					<?php } ?>

			</div><!-- /sidebar-->

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

<?php $outline_include_1 = new Outline('_footer'); require $outline_include_1->get(); ?>
<?php Outline::finish(); ?>