<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<?php $outline_include_1 = new Outline('includes/options_submenu'); require $outline_include_1->get(); ?>

<script type="text/javascript" src="<?php echo $__adminurl; ?>/js/jquery.numeric.js"></script>
<script type="text/javascript"><!--
$(
	function(){
		$(".numeric").numeric();
	}
);

function toggleStatus() {
	if ($('#mailselector').attr('value') == 'sendmail') {
		$('#emailblock :input').attr('disabled', 'disabled');
		$("#emailblock :input").addClass("gray");
		$("#emailblock .lbl").addClass("gray");
	} else {
		$('#emailblock :input').removeAttr('disabled');
		$("#emailblock :input").removeClass("gray");
		$("#emailblock .lbl").removeClass("gray");
	}
}
$(document).ready(function(){
	$('#mailselector').change(function() {
			toggleStatus();
	});

	//initial
	toggleStatus();
});
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

		<form action="<?php echo $__adminurl; ?>/options_general" method="post" name="options" id="options">
		<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />


		<div class="optiondiv">
		<h3><?php echo $_t['sitedetails']; ?></h3>
			<fieldset>
				<div style="width:120px;float:left" class="mtop5"><?php echo $_t['sitename']; ?>:</div>
				<input type="text" size="30" name="options[sitename]" value="<?php echo $options['sitename']; ?>" class="forminput" /> <span class="tinyfont gray">(ex.: Buxom Curves)</span>
				<div class="c mbot5"></div>

				<div style="width:120px;float:left" class="mtop5"><?php echo $_t['siteshortname']; ?>:</div>
				<input type="text" size="30" name="options[shortname]" value="<?php echo $options['shortname']; ?>" class="forminput" /> <span class="tinyfont gray">(ex.: BuxomCurves.com)</span>
				<div class="c mbot5"></div>
			</fieldset>
		</div>


		<div class="optiondiv">
		<h3><?php echo $_t['user_authentification']; ?></h3>
			<fieldset>
				<div style="width:120px;float:left" class="mtop5"><label for="authselector"><?php echo $_t['authmethod']; ?>:</label></div>
				<select name="options[auth_method]" id="authselector" class="forminput">
					<option<?php if ($options[auth_method]=='htaccess') { ?> selected="selected"<?php } ?> value="htaccess">htaccess</option>
					<option<?php if ($options[auth_method]=='database') { ?> selected="selected"<?php } ?> value="database">database</option>
				</select>
				<div class="c mbot5"></div>
			</fieldset>
		</div>

		<div class="optiondiv">
		<h3><?php echo $_t['new_update']; ?></h3>
			<fieldset>
				<div class="mbot5 mtop5"><?php echo $_t['floating_thumbnails']; ?>:</div>
									<div class="c"></div>
									<input type="radio" value="true" name="options[floating_thumbs]"<?php if ($options[floating_thumbs] == 'true') { ?> checked="checked"<?php } ?> /> Yes
									<input type="radio" value="false" name="options[floating_thumbs]"<?php if ($options[floating_thumbs] == 'false') { ?> checked="checked"<?php } ?> /> No
				<div class="c mbot5"></div>
			</fieldset>
		</div>


		<div class="optiondiv">
			<h3><?php echo $_t['email']; ?></h3>

			<fieldset>
				<div style="width:120px;float:left" class="mtop5"><?php echo $_t['email']; ?>:</div>
				<input type="text" size="30" name="options[email]" value="<?php echo $options['email']; ?>" class="forminput" />
				<span class="tinyfont gray">(Main email, used for internal and external communication.)</span>
				<div class="c mbot5"></div>

				<div style="width:120px;float:left" class="mtop5">Email Name:</div>
				<input type="text" size="30" name="options[emailname]" value="<?php echo $options['emailname']; ?>" class="forminput" />
				<div class="c mbot5"></div>

				<div style="width:120px;float:left" class="mtop5">Mail method:</div>
				<select name="options[mailmethod]" id="mailselector" class="forminput">
					<option<?php if ($options[mailmethod]=='sendmail') { ?> selected="selected"<?php } ?>>sendmail</option>
					<option<?php if ($options[mailmethod]=='smtp') { ?> selected="selected"<?php } ?>>smtp</option>
				</select>
				<div class="c mbot5"></div>

				<div id="emailblock">
					<div class="lbl mtop5" style="width:120px;float:left">SMTP host:</div>
					<input type="text" size="30" name="options[smtphost]" value="<?php echo $options['smtphost']; ?>" class="forminput" />
					<div class="c mbot5"></div>

					<div class="lbl mtop5" style="width:120px;float:left">SMTP user:</div>
					<input type="text" size="30" name="options[smtpuser]" value="<?php echo $options['smtpuser']; ?>" class="forminput" />
					<div class="c mbot5"></div>

					<div class="lbl mtop5" style="width:120px;float:left">SMTP pass:</div>
					<input type="password" size="30" name="options[smtppass]" value="<?php echo $options['smtppass']; ?>" class="forminput" />
					<div class="c mbot5"></div>

					<div class="lbl mtop5" style="width:120px;float:left">SMTP port:</div>
					<input type="text" size="30" name="options[smtpport]" value="<?php echo $options['smtpport']; ?>" class="forminput numeric" />
					<div class="c mbot5"></div>
				</div>







				<div style="width:120px;float:left" class="mtop5">Batch Email Size:</div>
				<input type="text" size="30" name="options[email_pp]" value="<?php echo $options['email_pp']; ?>" class="forminput numeric" />
				<span class="tinyfont gray">(Number of emails to send in batch when sending emails to members.)</span>
				<div class="c mbot5"></div>

			</fieldset>

		</div>


		<div class="optiondiv">
			<h3><?php echo $_t['companyinformation']; ?></h3>

			<fieldset>

				Enter your company info for the 2257 page.
				An image will be created from this info which makes it a bit harder for spammers to get your email.
				If you change your company info here, delete <?php echo $__siteurl; ?>/img/2257.jpg.
				Take a look at <a href="<?php echo $__siteurl; ?>/2257.php">2257.php</a> to check the result.

				<div class="c"></div>

				<textarea name="options[2257info]" cols="50" rows="4" class="forminput"><?php echo $options['2257info']; ?></textarea>

			</fieldset>

		</div>


		<div>
			<input type="submit" id="submitall" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
		</div>

		</form>

	</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>