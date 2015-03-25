<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<?php $outline_include_1 = new Outline('includes/options_submenu'); require $outline_include_1->get(); ?>

	<a name="top"></a>
	<div id="admincontent">

		<form action="<?php echo $__adminurl; ?>/options_mobile" method="post" name="options" id="options">
		<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />


		<div class="optiondiv">
			<h3><?php echo $_t['mobiledeviceredirections']; ?></h3>

			<fieldset>

				<div class="mbot10">
					<h4><?php echo $_t['globalredirection']; ?></h4>
				</div>

				<div>
					Globally turn off all mobile redirects by setting this to false.
					<br />
					<input type="radio" value="true" name="options[mobile_device_redirect]"<?php if ($options['mobile_device_redirect'] == 'true') { ?> checked="checked"<?php } ?> /> true
					<input type="radio" value="false" name="options[mobile_device_redirect]"<?php if ($options['mobile_device_redirect'] == 'false') { ?> checked="checked"<?php } ?> /> false
				</div>

				<div class="mtop10">

					<div class="mbot10">
						<h4><?php echo $_t['redirects']; ?></h4>
					</div>

					You can define different redirects depending on the matching devices. Set the following variables to the redirect url or leave them blank to not redirect for the mobile device. Urls can be relative or even point to another website.

					<div class="mtop10">
						IPhone and IPod Touch:
						<br />
						<input type="text" name="options[mobile_device_redirect_urls][iphoneurl]" class="forminput" size="50" value="<?php echo $options['mobile_device_redirect_urls']['iphoneurl']; ?>" />
					</div>
					<div class="mtop5">
						Android Phones:
						<br />
						<input type="text" name="options[mobile_device_redirect_urls][androidurl]" class="forminput" size="50" value="<?php echo $options['mobile_device_redirect_urls']['androidurl']; ?>" />
					</div>
					<div class="mtop5">
						Opera Mini:
						<br />
						<input type="text" name="options[mobile_device_redirect_urls][operaurl]" class="forminput" size="50" value="<?php echo $options['mobile_device_redirect_urls']['operaurl']; ?>" />
					</div>
					<div class="mtop5">
						Blackberry:
						<br />
						<input type="text" name="options[mobile_device_redirect_urls][blackberryurl]" class="forminput" size="50" value="<?php echo $options['mobile_device_redirect_urls']['blackberryurl']; ?>" />
					</div>
					<div class="mtop5">
						Palm:
						<br />
						<input type="text" name="options[mobile_device_redirect_urls][palmurl]" class="forminput" size="50" value="<?php echo $options['mobile_device_redirect_urls']['palmurl']; ?>" />
					</div>
					<div class="mtop5">
						Windows Mobile:
						<br />
						<input type="text" name="options[mobile_device_redirect_urls][windowsurl]" class="forminput" size="50" value="<?php echo $options['mobile_device_redirect_urls']['windowsurl']; ?>" />
					</div>
					<div class="mtop5">
						<?php echo $_t['othermobiledevices']; ?>:
						<br />
						<input type="text" name="options[mobile_device_redirect_urls][mobileurl]" class="forminput" size="50" value="<?php echo $options['mobile_device_redirect_urls']['mobileurl']; ?>" />
					</div>

				</div>

			</fieldset>
		</div>


		<div>
			<input type="submit" id="submitall" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
		</div>

		</form>

	</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>