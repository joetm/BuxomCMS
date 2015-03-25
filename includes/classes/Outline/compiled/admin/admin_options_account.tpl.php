<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<?php $outline_include_1 = new Outline('includes/options_submenu'); require $outline_include_1->get(); ?>

<script type="text/javascript"><!--
$(document).ready(function() {
	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 1500);
});
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

		<?php if ($_page[errormessage]) { ?>
			<div id="errors" class="c">
				<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[errormessage]; ?>
			</div>
		<?php } ?>
		<?php if ($_page[successmessage]) { ?>
			<div id="success" class="c">
			<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[successmessage]; ?>
			</div>
		<?php } ?>

		<form action="<?php echo $__adminurl; ?>/options_account" method="post" name="options" id="options" autocomplete="off">
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

			<div class="optiondiv">
				<h3><?php echo $_t['change_password']; ?></h3>
				<fieldset>
					<div class="mbot5">
						<?php echo $_t['current_password']; ?>
					</div>
					<div class="mbot10">
						<input type="password" value="" class="forminput" name="old_password" />
					</div>

					<div class="mbot5">
						<?php echo $_t['new_password']; ?>
					</div>
					<div class="mbot10">
						<input type="password" value="" class="forminput" name="new_password[0]" />
					</div>

					<div class="mbot5">
						<?php echo $_t['repeat_password']; ?>
					</div>
					<div class="mbot10">
						<input type="password" value="" class="forminput" name="new_password[1]" />
					</div>
				</fieldset>
			</div>

			<div>
				<input type="submit" id="submitall" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
			</div>

		</form>

	</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>