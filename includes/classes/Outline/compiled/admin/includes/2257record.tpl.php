<?php $outline = Outline::get_context(); ?><script type="text/javascript"><!--
$(document).ready(function(){

		function changeVals() {
			var values = $("#modelselector").val() || [];

			$.each(values, function(key, value) {

				<?php if (!$form_modelreleaseurl) { ?>
					if(!$("#modelreleaseurl_"+value).length){
						//add release field
						$("#fieldcontainer").append("<div id='modelreleaseurl_"+value+"' class='formitem dynamic__formfield<?php echo $errorcss['modelreleaseurl']; ?>'><label for='modelreleaseurl_input_"+value+"'><?php echo $_t['modelreleaseurl']; ?> <span class='f10'>for "+$('#modelselector option[value='+value+']').text()+"</span> <?php Outline::dispatch('optional', array("id" => 'modelreleaseurl')); ?></label><br /><input id='modelreleaseurl_input_"+value+"' name='modelreleaseurl["+value+"]' tabindex='53' type='text' class='forminput' value='' size='74' /></div>");
					}
				<?php } ?>
				<?php if (!$form_miscurl) { ?>
					if(!$("#miscurl_"+value).length)
					{
						//add misc field
						$("#fieldcontainer").append("<div id='miscurl_"+value+"' class='formitem dynamic__formfield<?php echo $errorcss['miscurl']; ?>'><label for='miscurl_input_"+value+"'><?php echo $_t['miscurl']; ?> <span class='f10'>for "+$('#modelselector option[value='+value+']').text()+"</span> <?php Outline::dispatch('optional', array("id" => 'miscurl')); ?></label><br /><input name='miscurl["+value+"]' id='miscurl_input_"+value+"' type='text' tabindex='53' class='forminput' value='' size='74' /></div>");
					}
				<?php } ?>
			});

			//remove all not selected fields
			var preg = '';
			$.each(values, function(k, v) {
				preg = preg + " #modelreleaseurl_"+v+","+" #miscurl_"+v+",";
			});
			//remove trailing comma
			preg = preg.slice(0,preg.length-1);
			$("#fieldcontainer > :not("+preg+")").remove();
		}
		$("#modelselector").change(changeVals);
		changeVals();

		//prefill
		<?php foreach ($output[modelreleaseurl] as $key => $val) { ?>
			$("#modelreleaseurl_input_<?php echo $key; ?>").val("<?php echo addslashes($val); ?>");
		<?php } ?>
		<?php foreach ($output[miscurl] as $key => $val) { ?>
			$("#miscurl_input_<?php echo $key; ?>").val("<?php echo addslashes($val); ?>");
		<?php } ?>
});
//-->
</script>

<!-- 2257 record -->
<div class="itembox">

	<h4 class="mbot5">2257 Record</h4>
	<?php if ($_page[templatename] == 'admin_update') { ?>
		<div class="left formitem<?php echo $errorcss['models']; ?>">
			<!-- model name -->
			<label><?php echo $_t['models']; ?> <?php Outline::dispatch('optional', array("id" => 'models')); ?>
			</label><br />
		    <select name="models[]" size="5" style="width:190px;" multiple="multiple" class="<?php echo $errorcss['models']; ?>" tabindex="50" id="modelselector">
				<?php foreach ($models as $m) { ?>
					<option id="model_<?php echo $m['id']; ?>" value="<?php echo $m[id]; ?>"<?php if (in_array($m[id], $output[models])) { ?> selected="selected"<?php } ?>><?php echo $m['modelname']; ?> <?php if ($m[slug]) { ?>(<?php echo $m['slug']; ?>)<?php } ?></option>
				<?php } ?>
			</select>
		</div>

		<?php if (!$form_notes) { ?>
			<div class="right formitem<?php echo $errorcss['notes']; ?>">
				<label for="notes"><?php echo $_t['update']; ?> <?php echo $_t['notes']; ?> <?php Outline::dispatch('optional', array("id" => 'notes')); ?>
				</label><br />
				<textarea name="notes" tabindex="51" class="forminput" id="notes" cols="29" style="height:75px;width:265px;" rows="3"><?php echo $output['notes']; ?></textarea>
			</div>
		<?php } ?>
		<div class="c"></div>
	<?php } ?>
	<?php if ($_page[templatename] != 'admin_update') { ?>
						<!-- birthdate -->
<?php if (!$form_birthdate) { ?>
						<div class="formitem<?php echo $errorcss['birthdate']; ?>">
							<div class="ititle">
								<label for="birthdate"><?php echo $_t['birthdate']; ?> <?php Outline::dispatch('optional', array("id" => 'birthdate')); ?></label>
							</div>
							<div class="right">
								<input name="birthdate" type="text" class="forminput" id="birthdate" value="<?php echo $output['birthdate']; ?>" size="8" />
							</div>
							<div class="cl"></div>
						</div>
<?php } ?>
						<!-- gender -->
<?php if (!$form_gender) { ?>
						<div class="formitem<?php echo $errorcss['gender']; ?>">
							<div class="ititle">
								<label for="gender"><?php echo $_t['gender']; ?> <?php Outline::dispatch('optional', array("id" => 'gender')); ?></label>
							</div>
							<div class="right">
								<select name="gender" id="gender" class="forminput" style="width:80px">
									<option<?php if ($output[gender] == 'female') { ?> selected="selected"<?php } ?>>female</option>
									<option<?php if ($output[gender] == 'male') { ?> selected="selected"<?php } ?>>male</option>
									<option<?php if ($output[gender] == 'trans') { ?> selected="selected"<?php } ?>>trans</option>
								</select>
							</div>
							<div class="cl"></div>
						</div>
<?php } ?>
		<?php if (!$form_realname) { ?>
			<div class="formitem<?php echo $errorcss['realname']; ?> left">
				<label for="realname"><?php echo $_t['realname']; ?> <?php Outline::dispatch('optional', array("id" => 'realname')); ?>
				</label><br />
				<input name="realname" type="text" tabindex="50" class="forminput" id="realname" value="<?php echo $output['realname']; ?>" size="46" />
			</div>
		<?php } ?>
		<?php if (!$form_passport_id) { ?>
			<div class="formitem<?php echo $errorcss['passport_id']; ?> right">
				<label for="passport_id"><?php echo $_t['passport_id']; ?> <?php Outline::dispatch('optional', array("id" => 'passport_id')); ?>
				</label><br />
				<input name="passport_id" type="text" tabindex="52" class="forminput" id="passport_id" value="<?php echo $output['passport_id']; ?>" size="20" />
			</div>
		<?php } ?>
		<div class="c"></div>
		<?php if (!$form_aliases) { ?>
			<div class="formitem<?php echo $errorcss['aliases']; ?>">
				<label for="aliases"><?php echo $_t['aliases']; ?> <?php Outline::dispatch('optional', array("id" => 'aliases')); ?>
				</label><br />
				<input name="aliases" type="text" tabindex="51" class="forminput" id="aliases" value="<?php echo $output['aliases']; ?>" size="74" />
			</div>
		<?php } ?>

		<?php if (!$form_idurl) { ?>
			<div class="formitem<?php echo $errorcss['idurl']; ?>">
				<label for="idurl"><?php echo $_t['idurl']; ?> <?php Outline::dispatch('optional', array("id" => 'idurl')); ?>
				</label><br />
				<input name="idurl" type="text" tabindex="52" class="forminput" id="idurl" value="<?php echo $output['idurl']; ?>" size="74" />
			




			</div>
		<?php } ?>
		<?php if (!$form_miscurl) { ?>
			<div class="formitem<?php echo $errorcss['miscurl']; ?>">
				<label for="miscurl"><?php echo $_t['miscurl']; ?> <?php Outline::dispatch('optional', array("id" => 'miscurl')); ?>
				</label><br />
				<input name="miscurl" type="text" tabindex="53" class="forminput" id="miscurl" value="<?php echo $output['miscurl']; ?>" size="74" />
			</div>
		<?php } ?>
	<?php } ?>
	<?php if ($_page[templatename] != 'admin_model') { ?>

		
		<div id="fieldcontainer"></div>

		<?php if (!$form_productiondate) { ?>
			<div class="formitem<?php echo $errorcss['productiondate']; ?>">
				<label for="productiondate"><?php echo $_t['productiondate']; ?> <?php Outline::dispatch('optional', array("id" => 'productiondate')); ?>
				</label><br />
				<input name="productiondate" tabindex="55" type="text" class="forminput" id="productiondate" value="<?php echo $output['productiondate']; ?>" size="8" />
			</div>
		<?php } ?>
		<?php if (!$form_location) { ?>
			<!-- location -->
			<div class="formitem<?php echo $errorcss['location']; ?>">
				<div class="ititle">
					<label for="location"><?php echo $_t['location']; ?> <?php Outline::dispatch('optional', array("id" => 'location')); ?></label>
				</div>
				<div class="right">
					<div class="right">
						<input name="location" tabindex="56" type="text" class="forminput" id="location" value="<?php echo $output['location']; ?>" size="50" />
					</div>
				</div>
				<div class="cl"></div>
			</div>
		<?php } ?>

		<div class="formitem">
			<div class="sep"></div>
		</div>

		<!-- country, LatLng -->
		<div class="formitem">
			<?php if (!$form_country) { ?>
				<div class="left<?php if ($output[location]) { echo $errorcss['country']; } ?>">
					<div class="ititle left">
						<label for="country"><?php echo $_t['country']; ?> <?php Outline::dispatch('optional_short', array("id" => 'country')); ?>
						</label> &nbsp;
					</div>
					<div class="right">
						<select name="country" tabindex="57" id="country" class="forminput f12">
						<?php foreach ($countries as $c) { ?><option<?php if ($c[country]==$output[country]) { ?> selected="selected"<?php } ?>><?php echo $c[country]; ?></option><?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if (!$form_LatLng) { ?>
				<div class="right<?php if ($output[location]) { echo $errorcss['LatLng']; } ?>">
					<div class="formitem right">
						<input type="text" tabindex="59" name="LatLng" class="forminput right" id="LatLng" value="<?php echo $output['LatLng']; ?>" size="10" />
					</div>
					<div class="ititle right<?php echo $errorcss['LatLng']; ?>">
						<label for="LatLng"><?php echo $_t['lat']; ?>,<?php echo $_t['lng']; ?>
						<?php Outline::dispatch('optional_short', array("id" => 'LatLng')); ?>
						</label> &nbsp;
					</div>
				</div>
			<?php } ?>
		</div>

		<div class="formitem">
			<div class="sep"></div>
		</div>
	<?php } ?>
	<!-- zipcode, state -->
	<div class="formitem">
		<?php if (!$form_state) { ?>
			<div class="left<?php if ($output[location]) { echo $errorcss['state']; } ?>">
				<div class="formitem right">
					<input type="text" tabindex="58" name="state" class="forminput right" id="state" value="<?php echo $output['state']; ?>" size="30" />
				</div>
				<div class="ititle right<?php echo $errorcss['state']; ?>">
					<label for="state"><?php echo $_t['state']; ?>
					<?php Outline::dispatch('optional_short', array("id" => 'state')); ?>
					</label> &nbsp;
				</div>
			</div>
		<?php } ?>
		<?php if (!$form_zipcode) { ?>
			<div class="right<?php if ($output[location]) { echo $errorcss['zipcode']; } ?>">
				<div class="ititle left">
					<label for="zipcode"><?php echo $_t['zipcode']; ?> <?php Outline::dispatch('optional_short', array("id" => 'zipcode')); ?>
					</label> &nbsp;
				</div>
				<div class="right">
					<input type="text" tabindex="60" name="zipcode" class="numeric forminput right" id="zipcode" value="<?php echo $output['zipcode']; ?>" size="10" />
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="c"></div>
	<?php if ($_page[templatename] != 'admin_update') { ?>
		<?php if (!$form_notes) { ?>
			<div class="formitem<?php echo $errorcss['notes']; ?>">
				<label for="notes"><?php echo $_t['notes']; ?> <?php Outline::dispatch('optional', array("id" => 'notes')); ?>
				</label><br />
				<textarea name="notes" class="forminput" id="notes" cols="59" style="width:465px;" rows="3"><?php echo $output['notes']; ?></textarea>
			</div>
		<?php } ?>
	<?php } ?>

</div> <!-- 2257 record --><?php Outline::finish(); ?>