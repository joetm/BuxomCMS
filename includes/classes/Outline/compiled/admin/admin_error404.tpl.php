<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

	<div id="admincontent">

		<div align="center" width="100%">

			<div align="center" width="100%" class="bubblebox" style="margin:25px 0px 25px 0px;">
				<?php echo $_error404; ?>
			</div>

		</div>

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>