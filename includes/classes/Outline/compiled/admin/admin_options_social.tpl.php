<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<?php $outline_include_1 = new Outline('includes/options_submenu'); require $outline_include_1->get(); ?>

<script type="text/javascript"><!--
function toggleStatus() {
	if ($('.sdsdsdsdsd').attr('value') == 'sendmail') {
		$('.deactivate').attr('disabled', 'disabled');
		$(".deactivate").addClass("gray");
	} else {
		$('.deactivate').removeAttr('disabled');
		$(".deactivate").removeClass("gray");
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

		<?php if ($_page[errormessage]) { ?>
			<div id="errors">
				<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[errormessage]; ?>
			</div>
		<?php } ?>
		<?php if ($_page[successmessage]) { ?>
			<div id="success">
			<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[successmessage]; ?>
				<?php echo $modellink; ?>
			</div>
		<?php } ?>

		<form action="<?php echo $__adminurl; ?>/options_social" method="post" name="options" id="options">
		<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />


		<div class="optiondiv">
			<h3><?php echo $_t['socialnetworkfeatures']; ?></h3>

			<fieldset>

				<?php Outline::register_function('outline__user_admin_admin_options_social_optionfields', 'optionfields'); if (!function_exists('outline__user_admin_admin_options_social_optionfields')) { function outline__user_admin_admin_options_social_optionfields($args) { extract($args+array("options" => '', "i" => '', "_t" => ''));  ?>
					<input type="hidden" name="options[<?php echo $i; ?>][name]" value="<?php echo $options['name']; ?>"/>

					<div style="float:left;width:170px;" class="mot10">
						<h4><?php echo ucwords($options[name]); ?></h4>
						<div><?php echo $_t['username']; ?>:</div>
						<input type="text" name="options[<?php echo $i; ?>][username]" value="<?php echo $options['username']; ?>" class="forminput" />
						<div class="c mbot5"></div>
							
							<?php if ($options[updateposting_possible] == true) { ?>
								<div style="height:58px;">
									Automatically post to <?php echo strtolower($options[name]); ?> on new update?
									<div class="c"></div>
									<input type="radio" value="true" name="options[<?php echo $i; ?>][postonupdate]"<?php if ($options[postonupdate] == 'true') { ?> checked="checked"<?php } ?> /> Yes
									<input type="radio" value="false" name="options[<?php echo $i; ?>][postonupdate]"<?php if ($options[postonupdate] == 'false') { ?> checked="checked"<?php } ?> /> No
								</div>
								<div class="c mbot5"></div>
									<div>Consumer Key:</div>
									<input type="text" name="options[<?php echo $i; ?>][consumer_key]" value="<?php echo $options['consumer_key']; ?>" class="forminput" />
								<div class="c mbot5"></div>
									<div>Consumer Secret:</div>
									<input type="text" name="options[<?php echo $i; ?>][consumer_secret]" value="<?php echo $options['consumer_secret']; ?>" class="forminput" />
								<div class="c mbot5"></div>
									<div>Application ID:</div>
									<input type="text" name="options[<?php echo $i; ?>][application_id]" value="<?php echo $options['application_id']; ?>" class="forminput" />
							<?php } ?>
					</div>
				<?php } } ?>


				<?php $i=0; ?>
				<?php foreach ($options as $o) { ?>
					<?php Outline::dispatch('optionfields', array("options" => $o, "i" => $i, "_t" => $_t)); ?>
					<?php $i++; ?>
				<?php } ?>


				<div class="c"></div>

				<div class="mtop10 gray">
					Leave empty to deactivate.
					


				</div>

			</fieldset>

		</div>

		<div class="optiondiv">
			<h3><?php echo $_t['ping_search_engines']; ?></h3>
			<fieldset>
				<div class="mbot5">
					<?php echo $_t['ping_search_engines']; ?>?
					<br>
					<input type="radio" name="ping_options[ping_engines]"<?php if ($ping_options['ping_engines'] == 'true') { ?> checked="checked"<?php } ?> value="true"> yes
					<input type="radio" name="ping_options[ping_engines]"<?php if ($ping_options['ping_engines'] == 'false') { ?> checked="checked"<?php } ?> value="false"> no
				</div>

				<div class="mbot5">
					URLs to ping when a new update is successfully added. One per line.
					<br />
					<div class="gray">
					The default method used in the XML-RPC call is "weblogUpdates.ping".<br />
					You can specify a different method for each service using this syntax:<br />
					methodname|http://url/to/service
					</div>
				</div>
				<textarea name="ping_options[ping_urls]" cols="70" style="width:99%;" rows="4" class="forminput"><?php echo $ping_options[ping_urls]; ?></textarea>
			</fieldset>
		</div>


		<div>
			<input type="submit" id="submitall" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
		</div>

		</form>

	</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>