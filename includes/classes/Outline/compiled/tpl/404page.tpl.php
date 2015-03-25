<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_header'); require $outline_include_0->get(); ?>

<!--
	<div id="headerimg">
	</div>
-->

	<a name="404"></a>
	<div id="page-404"align="left" width="100%">

			<div class="bubblebox">
				<?php echo $_t['error404']; ?>
				<br />
				<?php echo $_t['filenotfound']; ?>
			</div>

			<div>
				<form>
					Sign up for our free weekly email with free hot pics and videos.<br />
					We will never, ever rent, sell or giveaway your email address to anyone. Ever.
					<br />
					We value your privacy.
					<input type="text" value="" />
				</form>
			</div>

	</div><!-- /404-page-->

<?php $outline_include_1 = new Outline('_footer'); require $outline_include_1->get(); Outline::finish(); ?>