<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

	<a name="top"></a>
	<div id="admincontent">

			<div id="dashboard">

				<div class="mbot5">
					<div class="right mtop10"><?php echo $_t['quickstats']; ?>: <?php echo $num[member]; ?> <?php echo $_t['activemembers']; ?>, <?php echo $num[set][picture]; ?> <?php echo $_t['pictureupdates']; ?>, <?php echo $num[set][video]; ?> <?php echo $_t['videoupdates']; ?>, <?php echo $num['model']; ?> <?php echo $_t['models']; ?>, <?php echo $num['comment']; ?> <?php echo $_t['comments']; ?>, <?php echo $num['tag']; ?> <?php echo $_t['tags']; ?></div>
					<div class="left"><h1><?php echo $_t['siteoverview']; ?></h1></div>
					<div class="c"></div>
				</div>

				<?php if ($feed) { ?>
					<div id="dashboard_feed" style="width:19%">

						<?php if ($__newestversion) { ?>
						<div align="center" class="mbot10">The latest version is <a href='http://buxomcms.com/latest' title="Download and upgrade"><?php echo $__newestversion; ?></a>.</div>
						<?php } ?>

						<h3 class="mbot5"><?php echo $feed[title]; ?></h3>
						<hr />
						<?php foreach ($feed[items] as $item) { ?>
							<?php echo $item; ?>
						<?php } ?>
					</div>
				<?php } ?>

<div style="float:left;width:79%">
				<table border="0" class="dashboard f12" cellpadding="0" cellspacing="0">
				<thead>
				<tr>
				<th width="50%" align="left">
					<h2><a href="<?php echo $__adminurl; ?>/updates" title="<?php echo $_t['editupdates']; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/image_edit.png" alt="" width="16" height="16" border="0" /></a> <?php echo $_t['updates']; ?></h2>
				</th>
				<th>&nbsp;</th>
				<th width="50%" align="left">
<!--
					<span class="right mtop5" style="font-weight:normal;">
						<?php echo $_t['id']; ?>
					</span>
-->
					<h2><a href="<?php echo $__adminurl; ?>/models" title="<?php echo $_t['editmodels']; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/user_edit.png" alt="" width="16" height="16" border="0" /></a> <?php echo $_t['models']; ?></h2>
				</th>
				</tr>
				</thead>


			<tr>
			<td width="50%" valign="top">

				<?php if ($_page[errormessage][updates]) { ?>
					<?php echo $_page[errormessage][updates]; ?>
				<?php } else { ?>

				<?php $i = 0; ?>
				<?php foreach ($updates as $u) { ?>
				<div width="100%" class="indexrow <?php if (($i % 2 == 0)) { ?>odd<?php } else { ?>even<?php } ?>">
					<div class="left lpad5" style="width:100%">
						<a href="<?php echo $__siteurl; ?>/<?php if ($u[type]=='videoset') { ?>video<?php } else { ?>set<?php } ?>/<?php echo $u['slug']; ?>" title="<?php echo $_t['view']; ?>" target="_blank">
						<img src="<?php echo $__siteurl; ?>/img/icons/link_go.png" border="0" width="16" height="16" alt="<?php echo $_t['view']; ?>" /></a>

						<?php echo $u['title']; ?>
					<div class="right rpad5">

						<?php if ($u[type]=='videoset') { ?>
							<img src="<?php echo $__siteurl; ?>/img/icons/video.png" alt="<?php echo $u['type']; ?>" title="(<?php echo $u['type']; ?>)" border="0" width="16" height="16" />
						<?php } else { ?>
							<img src="<?php echo $__siteurl; ?>/img/icons/images.png" alt="<?php echo $u['type']; ?>" title="(<?php echo $u['type']; ?>)" border="0" width="16" height="16" />
						<?php } ?>

						&nbsp;
						<a href="<?php echo $__adminurl; ?>/update?edit=<?php echo $u['id']; ?>" title="<?php echo $_t['editupdate']; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/pencil.png" width="16" height="16" alt="" border="0" /></a>
					</div>
					</div>
				<div class="c"></div>
				</div>
				<?php $i++; ?>
				<?php } ?>

				<?php } ?>

			</td>
			<td>&nbsp;</td>
			<td width="50%" valign="top">

			<?php if ($_page[errormessage][models]) { ?>
				<?php echo $_page[errormessage][models]; ?>
			<?php } else { ?>

				<?php $i = 0; ?>
				<?php foreach ($models as $m) { ?>
				<div width="100%" class="indexrow <?php if (($i % 2 == 0)) { ?>odd<?php } else { ?>even<?php } ?>">
					<div class="left lpad5">
						<a href="<?php echo $__siteurl; ?>/model/<?php echo $m['slug']; ?>" title="<?php echo $_t['view']; ?>" target="_blank">
						<img src="<?php echo $__siteurl; ?>/img/icons/link_go.png" alt="<?php echo $_t['view']; ?>" width="16" height="16" border="0" /></a>

						<?php echo $m['modelname']; ?>
					</div>
					<div class="right rpad5">
						&nbsp;
						<a href="<?php echo $__adminurl; ?>/model?edit=<?php echo $m['id']; ?>" title="<?php echo $_t['editmodel']; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/pencil.png" width="16" height="16" border="0" alt="" /></a>
					</div>
					<div class="right rpad5">
						&nbsp;
						<?php echo $m['dateline']; ?>
					</div>
				<div class="c"></div>
				</div>
				<?php $i++; ?>
				<?php } ?>

			<?php } ?>

			</td>
			</tr>
			</table>

			<table border="0" class="dashboard f12" cellpadding="0" cellspacing="0">
			<thead>
			<tr>
			<th width="50%" align="left">
				<h2><a href="<?php echo $__adminurl; ?>/comments" title="<?php echo $_t['editcomments']; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/comment_edit.png" alt="" width="16" height="16" border="0" /></a> <?php echo $_t['comments']; ?></h2>
			</th>
			<th>&nbsp;</th>
			<th width="50%" align="left">
				<div class="right mtop5" style="font-weight:normal;">
					<?php echo $_t['id']; ?>
				</div>
				<div class="left">
					<h2><a href="<?php echo $__adminurl; ?>/members" title="<?php echo $_t['editmembers']; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/group.png" alt="" width="16" height="16" border="0" /></a> <?php echo $_t['members']; ?></h2>
				</div>
				<div class="c"></div>
			</th>
			</tr>
			</thead>


			<tr>
			<td width="50%" valign="top">

			<?php if ($_page[errormessage][comments]) { ?>
				<?php echo $_page[errormessage][comments]; ?>
			<?php } else { ?>

				<?php $i = 0; ?>
				<?php foreach ($comments as $c) { ?>
				<div width="100%" class="indexrow <?php if (($i % 2 == 0)) { ?>odd<?php } else { ?>even<?php } ?>">
				<form action="<?php echo $__adminurl; ?>/" method="post" name="editaddform">
				<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
					<div class="left lpad5" style="width:100%">
						<a href="<?php echo $__siteurl; ?>/<?php if ($c[type] == 'video') { ?>video<?php } else { ?>set<?php } ?>.php?id=<?php echo $c['id']; ?>" title="<?php echo $_t['viewupdate']; ?>" target="_blank">
						<img src="<?php echo $__siteurl; ?>/img/icons/link_go.png" border="0" alt="<?php echo $_t['view']; ?>" width="16" height="16" /></a>

						<?php echo $c['comment']; ?>
					<div class="right rpad5">
						(by <?php echo $c['username']; ?>)
						<input type="hidden" name="comment" value="<?php echo $c['id']; ?>" />

						<?php if ($c[status] == 'queued') { ?>
							<img src="<?php echo $__siteurl; ?>/img/icons/queued.png" alt="<?php echo $_t['queued']; ?>" title="<?php echo $_t['queued']; ?>" border="0" width="16" height="16" />
						<?php } else { ?>
							<input type="image" src="<?php echo $__siteurl; ?>/img/icons/queued_deactivated.png" value="queue" alt="" name="_action" title="<?php echo $_t['queuecomment']; ?>" />
						<?php } ?>
						<?php if ($c[status] == 'approved') { ?>
							<img src="<?php echo $__siteurl; ?>/img/icons/accept.png" alt="<?php echo $_t['approved']; ?>" title="<?php echo $_t['approved']; ?>" border="0" width="16" height="16" />
						<?php } else { ?>
							<input type="image" src="<?php echo $__siteurl; ?>/img/icons/lock_break_deactivated.png" value="approve" alt="" name="_action" title="<?php echo $_t['approvecomment']; ?>" />
						<?php } ?>
						<?php if ($c[status] == 'spam') { ?>
							<img src="<?php echo $__siteurl; ?>/img/icons/stop.png" alt="<?php echo $_t['spam']; ?>" title="<?php echo $_t['spam']; ?>" width="16" height="16" border="0" />
						<?php } else { ?>
							<input type="image" src="<?php echo $__siteurl; ?>/img/icons/stop_deactivated.png" value="spam" alt="" name="_action" title="<?php echo $_t['markasspam']; ?>" />
						<?php } ?>

						&nbsp;

						<a href="<?php echo $__adminurl; ?>/comments?id=<?php echo $c['id']; ?>" title="<?php echo $_t['editcomment']; ?> #<?php echo $c['id']; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/pencil.png" width="16" height="16" alt='' border="0" /></a>
					</div>
					</div>
				</form>
				<div class="c"></div>
				</div>
				<?php $i++; ?>
				<?php } ?>

			<?php } ?>

			</td>
			<td>&nbsp;</td>
			<td width="50%" valign="top">

			<?php if ($_page[errormessage][members]) { ?>
				<?php echo $_page[errormessage][members]; ?>
			<?php } else { ?>

				<?php $i = 0; ?>
				<?php foreach ($members as $m) { ?>
				<div width="100%" class="indexrow <?php if (($i % 2 == 0)) { ?>odd<?php } else { ?>even<?php } ?>">
					<div class="left lpad5">
						<img src="<?php echo $__siteurl; ?>/img/icons/<?php echo $m['status']; ?>.png" alt="<?php echo $m['status']; ?>" title="<?php echo $m['status']; ?>" width="16" height="16" border="0" />

						<?php echo $m['username']; ?>
					</div>
					<div class="right rpad5">
						<?php echo $m['padded']['id']; ?>
					</div>
				<div class="c"></div>
				</div>
				<?php $i++; ?>
				<?php } ?>

			<?php } ?>

			</td>
			</tr>
			</table>
</div>

			<div class="c">&nbsp;</div>

			</div><!-- /dashboard-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>