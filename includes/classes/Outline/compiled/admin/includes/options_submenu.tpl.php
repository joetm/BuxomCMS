<?php $outline = Outline::get_context(); ?>
<div id="submenu">
				<ul class="menu">
					<li class="menu-item<?php if ($_page[templatename] == 'admin_options_general') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_general"><?php echo $_general; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_options_billing') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_billing"><?php echo $_billing; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_options_theme') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_theme"><?php echo $_theme; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_options_social') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_social"><?php echo $_social; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_options_mobile') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_mobile"><?php echo $_mobile; ?></a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_options_local') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_local">L10n &amp; i18n</a>
					</li>
					<li class="menu-item<?php if ($_page[templatename] == 'admin_options_account') { ?> menuselected<?php } ?>">
						<a href="<?php echo $__adminurl; ?>/options_account">Account</a>
					</li>
				</ul><!-- /menu -->
</div>
<div class="c"></div>
<?php Outline::finish(); ?>