<?php $outline = Outline::get_context(); ?>		<div class="bottom-decobar"></div>

		<div class="right">
			<?php if ($_page[templatename] != 'admin_login') { ?>
				<small>
					<a href="<?php echo $__adminurl; ?>/editor"><?php echo strtolower($_template_editor); ?></a>
					|
					<a href="<?php echo $__adminurl; ?>/accounts"><?php echo strtolower($_accounts); ?></a>
					|
					<a href="<?php echo $__adminurl; ?>/login_history"><?php echo strtolower($_loginhistory); ?></a>
					|
					<a href="<?php echo $__adminurl; ?>/activitylog"><?php echo strtolower($_activitylog); ?></a>
					|
					<a href="<?php echo $__adminurl; ?>/structure"><?php echo strtolower($_showstructure); ?></a>
				</small>
			<?php } ?>
		</div>
		<div id="disclaimer" class="left">
			<small><a href="<?php echo $__siteurl; ?>/">
			<?php echo $_admininterface; ?> by <a href="http://www.buxomCMS.com/">BuxomCMS.com</a></small>
		</div>
		<div class="c"></div>

	</div><!-- /content -->

</div><!-- /align center-->
</body>
</html><?php Outline::finish(); ?>