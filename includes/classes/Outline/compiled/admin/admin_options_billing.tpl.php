<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>


<?php $outline_include_1 = new Outline('includes/options_submenu'); require $outline_include_1->get(); ?>


	<a name="top"></a>
	<div id="admincontent">

		<form action="<?php echo $__adminurl; ?>/options_billing" method="post" name="options" id="options">
		<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />


		<div class="optiondiv">
			<h3>Billing Information</h3>

			<fieldset>
				<div>
					Processor:
					<br />
					<select name="options[processor]" class="forminput">
						<?php foreach ($processors as $p) { ?>
							<option<?php if ($p==$options[processor]) { ?> selected="selected"<?php } ?>><?php echo $p; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="c mbot5"></div>

				<div>
					<?php echo $_t['approval_url']; ?>:
					<br />
					<input type="text" name="options[approval_url]" class="forminput" size="50" value="<?php echo $options['approval_url']; ?>" />
				</div>
				<div class="c mbot5"></div>

				<div>
					<?php echo $_t['denial_url']; ?>:
					<br />
					<input type="text" name="options[denial_url]" class="forminput" size="50" value="<?php echo $options['denial_url']; ?>" />
				</div>
				<div class="c mbot5"></div>

				<div>
					<?php echo $_t['error_url']; ?>:
					<br />
					<input type="text" name="options[error_url]" class="forminput" size="50" value="<?php echo $options['error_url']; ?>" />
				</div>

			</fieldset>
		</div>


		<div>
			<input type="submit" id="submitall" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
		</div>

		</form>

	</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>