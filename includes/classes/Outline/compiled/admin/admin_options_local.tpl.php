<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<?php $outline_include_1 = new Outline('includes/options_submenu'); require $outline_include_1->get(); ?>

	<a name="top"></a>
	<div id="admincontent">

		<form action="<?php echo $__adminurl; ?>/options_local" method="post" name="options" id="options">
		<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />



Local



		<div>
			<input type="submit" id="submitall" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
		</div>

		</form>

	</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>