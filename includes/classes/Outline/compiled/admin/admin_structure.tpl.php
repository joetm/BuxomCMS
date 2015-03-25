<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

	<a name="top"></a>
	<div id="admincontent">

		<div id="descrtext" class="mtop10 mbot10">
			The structure of your database is listed below.
			This will help you with constructing your own database queries.
			Click <a href="<?php echo $__adminurl; ?>/lib/dblayout.php" target="_blank">here</a> to view the graphical layout.
		</div>

		<div class="c"><br /></div>

		<div id="structure">

			<?php foreach ($output as $key => $t) { ?>
				<div><span class="bold f14"><?php echo outline__upper($key); ?></span> <span class="tinyfont" style="color:#888888">(<?php echo $t[description]; ?>)</span></div>
				<div>
					<?php foreach ($t[columns] as $k) { ?>
						<div class="dbcolumn f12">
							<?php if ($k[type] === 'PRI') { ?>
								<span style='border-bottom:1px dotted #FF0000'><?php echo $k['name']; ?></span>
							<?php } else if ($k[type] === 'MUL') { ?>
								<span style='border-bottom:1px dotted #00FF00'><?php echo $k['name']; ?></span>
							<?php } else { ?>
								<?php echo $k['name']; ?>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<div class="c"><br /></div>
			<?php } if (empty($output)) { ?>
				Missing output array.
			<?php } ?>

			<div class="c"></div>

		</div><!-- /structure-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>