<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<style type="text/css">
.export_option{
	float:left;
	width:170px;
	margin-right:10px;
}
.h4heading{
	margin-top:10px;
	margin-bottom:10px;
}
</style>

<script type="text/javascript"><!--
$(document).ready(function(){

	//expansion animation
	$("#exportbody").toggle('slow');


	$("#checkboxall").click(function(){
		var checked_status = this.checked;
			$("input[name^='data']").each(function()
			{
				this.checked = checked_status;
			});
	});

	$("#exportbutton").click(function(){
		$("#exportbody").hide('fast');
	});

});
//-->
</script>

	<a name="top"></a>
	<div id="admincontent">

			<div class="hidden" id="success"><?php echo $_t['success']; ?>!</div>

			<?php if (!empty($error)) { ?>
				<div id="errors">
					<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
					<strong><?php echo $error; ?></strong>
				</div>
			<?php } ?>

			<?php if (!empty($export)) { ?>
			<div class="hidden" id="exportbody">
				<textarea name="exportbody" style="width: 99%; border: 1px solid rgb(229, 229, 229);" class="forminput" cols="90" rows="13"><?php echo $export; ?></textarea>
			</div>
			<?php } ?>

			<form action="<?php echo $__adminurl; ?>/export" method="post" name="exportmembers">
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

				<div class="optiondiv">

					<h3><?php echo $_t['export_member_data']; ?></h3>

					<fieldset>

						<div class="right mtop5"><?php echo $_t['select_all']; ?> <input type="checkbox"<?php if ($checkboxall=='on') { ?> checked='checked'<?php } ?> id="checkboxall" name="checkboxall" /></div>

						<div class="mtop5">
							<div>
								<label for="members_selector"><?php echo $_t['members']; ?>:</label>
								<select name="members" id="members_selector" class="forminput">
									<option<?php if ($members=='all') { ?> selected="selected"<?php } ?> value="all">All</option>
									<option<?php if ($members=='active') { ?> selected="selected"<?php } ?> value="active">Active</option>
									<option<?php if ($members=='inactive') { ?> selected="selected"<?php } ?> value="inactive">Inactive</option>
									<option<?php if ($members=='chargeback') { ?> selected="selected"<?php } ?> value="chargeback">Chargeback</option>
								</select>
							</div>
						</div>

						<div class="c mbot10"></div>

							<h4 class="h4heading">user details</h4>

							<div class="export_option"><input type="checkbox"<?php if ($data[member][username]==1) { ?> checked="checked"<?php } ?> name="data[member][username]" value="1" /> username</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][id]==1) { ?> checked="checked"<?php } ?> name="data[member][id]" value="1" /> id</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][status]==1) { ?> checked="checked"<?php } ?> name="data[member][status]" value="1" /> status</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][firstname]==1) { ?> checked="checked"<?php } ?> name="data[member][firstname]" value="1" /> firstname</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][lastname]==1) { ?> checked="checked"<?php } ?> name="data[member][lastname]" value="1" /> lastname</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][birthdate]==1) { ?> checked="checked"<?php } ?> name="data[member][birthdate]" value="1" /> birthdate</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][gender]==1) { ?> checked="checked"<?php } ?> name="data[member][gender]" value="1" /> gender</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][meta_locale]==1) { ?> checked="checked"<?php } ?> name="data[member][meta_locale]" value="1" /> locale</div>

							<div class="c"></div>

							<h4 class="h4heading">session details</h4>

							<div class="export_option"><input type="checkbox"<?php if ($data[member][signup_IP]==1) { ?> checked="checked"<?php } ?> name="data[member][signup_IP]" value="1" /> signup IP</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[activity][action]==1) { ?> checked="checked"<?php } ?> name="data[activity][action]" value="1" /> last activity</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[activity][info]==1) { ?> checked="checked"<?php } ?> name="data[activity][info]" value="1" /> last activity info</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[activity][IP]==1) { ?> checked="checked"<?php } ?> name="data[activity][IP]" value="1" /> last activity IP</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[session][browser]==1) { ?> checked="checked"<?php } ?> name="data[session][browser]" value="1" /> browser hash</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[session][last_login]==1) { ?> checked="checked"<?php } ?> name="data[session][last_login]" value="1" /> last login</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[session][dateline]==1) { ?> checked="checked"<?php } ?> name="data[session][dateline]" value="1" /> session date</div>

							<div class="c"></div>

							<h4 class="h4heading">billing details</h4>

							<div class="export_option"><input type="checkbox"<?php if ($data[member][processor]==1) { ?> checked="checked"<?php } ?> name="data[member][processor]" value="1" /> processor</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][name_on_cc]==1) { ?> checked="checked"<?php } ?> name="data[member][name_on_cc]" value="1" /> name on creditcard</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][card_hash]==1) { ?> checked="checked"<?php } ?> name="data[member][card_hash]" value="1" /> creditcard hash</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][num_rebills]==1) { ?> checked="checked"<?php } ?> name="data[member][num_rebills]" value="1" /> number of rebills</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][join_date]==1) { ?> checked="checked"<?php } ?> name="data[member][join_date]" value="1" /> join_date</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][expiration_date]==1) { ?> checked="checked"<?php } ?> name="data[member][expiration_date]" value="1" /> expiration_date</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][expiration_reason]==1) { ?> checked="checked"<?php } ?> name="data[member][expiration_reason]" value="1" /> expiration_reason</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[member][lifetime_revenue]==1) { ?> checked="checked"<?php } ?> name="data[member][lifetime_revenue]" value="1" /> lifetime_revenue</div>

							<div class="c"></div>

							<h4 class="h4heading">emailings</h4>

							<div class="export_option"><input type="checkbox"<?php if ($data[email][email]==1) { ?> checked="checked"<?php } ?> name="data[email][email]" value="1" /> email address</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[email][no_emails]==1) { ?> checked="checked"<?php } ?> name="data[email][no_emails]" value="1" /> no emails</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[email][email_status]==1) { ?> checked="checked"<?php } ?> name="data[email][email_status]" value="1"/> email status</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[email][last_mailing]==1) { ?> checked="checked"<?php } ?> name="data[email][last_mailing]" value="1"/> last mailing</div>
							<div class="export_option"><input type="checkbox"<?php if ($data[email][num_mailings]==1) { ?> checked="checked"<?php } ?> name="data[email][num_mailings]" value="1"/> number of mailings</div>

							<div class="c mbot5"></div>

						</div>

					</fieldset>
				</div>

				<div class="optiondiv">

					<h3><?php echo $_t['output_format']; ?></h3>

					<fieldset>
						<div>
							<input type="radio" name="outputformat"<?php if ($outputformat == 'CSV') { ?> checked="checked"<?php } ?> value="CSV" /> CSV
							<input type="radio" name="outputformat"<?php if ($outputformat == 'XML') { ?> checked="checked"<?php } ?> value="XML" /> XML
							<input type="radio" name="outputformat"<?php if ($outputformat == 'JSON') { ?> checked="checked"<?php } ?> value="JSON" /> JSON
							<br />
							<input type="checkbox" name="onscreen" value="yes"<?php if ($onscreen == 'yes') { ?> checked="checked"<?php } ?> /> screen output
							<input type="text" name="separator" size="1" style="padding-left:5px;width:15px;" value="|" /> text separator
						</div>
					</fieldset>
				</div>

				<div>
					<input type="submit" id="exportbutton" class="submitbutton" name="submit" value="<?php echo $_t['export']; ?>" />
				</div>

			</form>

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>