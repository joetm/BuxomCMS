<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>


<?php Outline::register_function('outline__user_admin_admin_model_optional', 'optional'); if (!function_exists('outline__user_admin_admin_model_optional')) { function outline__user_admin_admin_model_optional($args) { extract($args+array("id" => ''));  if (!isset(model::$mandatory[$id])) { ?><span class='f10 optional'>* (optional)</span><?php } } } ?>
<?php Outline::register_function('outline__user_admin_admin_model_optional_short', 'optional_short'); if (!function_exists('outline__user_admin_admin_model_optional_short')) { function outline__user_admin_admin_model_optional_short($args) { extract($args+array("id" => ''));  if (!isset(model::$mandatory[$id])) { ?><span class='f10 optional'>*</span><?php } } } ?>


<script type="text/javascript"><!--
$(document).ready(function(){

	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 1500);

	//change rating selector on star click
	$(".star").click(function () {
		var v = $('input[name=rating2]:checked').val();
//		if (v == ''){
//			$("#ratingselector").val('0');
//		}
//		else{
			$("#ratingselector").val(v);
//		}
	});
	//change stars on rating selector click
	$("#ratingselector").change(function () {
		var id = $('#ratingselector').val();
		$('.star').rating('select',id)
	});

	
		var contwidth = $("#admincontent").width();
		var lcw = $(".left_col").width();
		var bw = contwidth - lcw - 20; //padding of 20
		$('#box').css('width', bw);

});
//-->
</script>

<script type="text/javascript" src="<?php echo $__siteurl; ?>/js/nicEdit.js"></script>
<script type="text/javascript"><!--
//<![CDATA[
bkLib.onDomLoaded(function() {
	new nicEditor({iconsPath:'<?php echo $__siteurl; ?>/js/nicEditorIcons.gif',buttonList : ['bold','italic','underline','left','center','right','ol','ul','link','unlink','xhtml']}).panelInstance('description');
});
//]]>
//-->
</script>

<?php if ($floating == 'true') { ?>
<script src="<?php echo $__siteurl; ?>/js/jquery.floatobject-1.0.js" type="text/javascript" ></script>
<script type="text/javascript"><!--
$(document).ready(main);
	function main()
	{
		$("#box").makeFloat({x:"current",y:"current",alwaysVisible:true});
	}





























//-->
</script>
<?php } ?>

<script type="text/javascript" src="<?php echo $__adminurl; ?>/js/jquery.numeric.js"></script>
<script type="text/javascript"><!--
$(
	function(){
		$(".numeric").numeric();
	}
);
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

		<?php if ($_page[errormessage] || $errors) { ?>
			<div id="errors" class="c">
				<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[errormessage]; ?>

				<?php foreach ($errors as $err) { ?>
					<div><?php echo $err; ?></div>
				<?php } ?>
			</div>
		<?php } ?>

		<form action="<?php echo $__adminurl; ?>/model<?php if (model::$id) { ?>?edit=<?php echo model::$id; } ?>" method="post" name="newmodel" id="newmodelform">
			<input type="hidden" name="_action" value="addnew" />
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

			<!-- thumbnails -->
			<div id="box" class="right_col mtop4">
				
				<?php if ($output[action]=='edit') { ?>
					<?php $outline_for_k = new OutlineIterator(0, count($thumbsizes)-1, 1); while ($outline_for_k->next()) { $k = $outline_for_k->index; ?>
						<div id="upload_area<?php echo $k; ?>" class="upload_area<?php echo $errorcss[thumbs][$k]; ?>">
							<?php if ($output[thumbs][$k]) { ?> 
								<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -4px;" /> <?php echo $_t['success']; ?>!<br />
								<img src="<?php echo $output[thumbs][$k][path]; ?>" border="0" /><br />
								<input type="text" name="thumbs[]" value="<?php echo $output[thumbs][$k][path]; ?>" />
							<?php } else { ?>
								<?php echo $_t['thumbnail']; ?> <?php echo $thumbsizes[$k][width]; ?>x<?php echo $thumbsizes[$k][height]; ?> (WxH)
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
				
					<?php $outline_for_k = new OutlineIterator(0, count($thumbsizes)-1, 1); while ($outline_for_k->next()) { $k = $outline_for_k->index; ?>
						<div id="upload_area<?php echo $k; ?>" class="upload_area<?php echo $errorcss[thumbs][$k]; ?>">
							<?php if ($output[thumbs][$k]) { ?> 
								<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -4px;" /> <?php echo $_t['success']; ?>!<br />
								<img src="temp/<?php echo $output[thumbs][$k]; ?>" border="0" /><br />
								<input type="text" name="thumbs[]" value="<?php echo $output[thumbs][$k]; ?>" />
							<?php } else { ?>
								<?php echo $_t['thumbnail']; ?> <?php echo $thumbsizes[$k][width]; ?>x<?php echo $thumbsizes[$k][height]; ?> (WxH)
							<?php } ?>
						</div>
					<?php } ?>
				<?php } ?>
			</div> <!-- /box-->

			<?php if ($_page[successmessage]) { ?>
				<div id="success">
				<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
					<?php echo $_page[successmessage]; ?>
					<?php echo $modellink; ?>
				</div>
			<?php } ?>

			<div class="left_col">

				<div class="itembox">

						<!-- model name -->
						<div class="formitem<?php echo $errorcss['modelname']; ?>">
							<div class="ititle">
								<label for="modelname"><?php echo $_t['modelname']; ?> <?php Outline::dispatch('optional_short', array("id" => 'modelname')); ?>
								</label>
							</div>
							<div class="right">
								<input name="modelname" type="text" class="forminput" id="modelname" value="<?php echo $output['modelname']; ?>" size="50" />
							</div>
							<div class="cl"></div>
						</div>

						<!-- slug -->
						<div class="formitem<?php echo $errorcss['slug']; ?>">
							<div class="ititle">
								<label for="slug"><?php echo $_t['slug']; ?> <?php Outline::dispatch('optional', array("id" => 'slug')); ?></label>
							</div>
							<div class="right">
								<span style="color:#8f8f8f">/model/</span><input name="slug" type="text" class="forminput" id="slug" value="<?php echo $output['slug']; ?>" size="27" />
							</div>
							<div class="cl"></div>
						</div>

					<!-- description -->
<?php if (!$form_description) { ?>
					<div class="formitem<?php echo $errorcss['description']; ?>">
						<label for="description"><?php echo $_t['description']; ?> <?php Outline::dispatch('optional', array("id" => 'description')); ?></label><br />
						<textarea name="description" id="description" class="forminput" cols="57" rows="6"><?php echo $output['description']; ?></textarea>
					</div>
<?php } ?>

				</div><!-- itembox-->

				<div class="itembox">
					<!-- tags -->
<?php if (!$form_tags) { ?>
					<div class="formitem<?php echo $errorcss['tags']; ?>">
						<label for="tags"><?php echo $_t['tags']; ?> <?php Outline::dispatch('optional', array("id" => 'tags')); ?></label>
						<span class="f10">(comma separated)</span>
						<br />
						<input name="tags" type="text" class="forminput" id="tags" value="<?php echo $output['tags']; ?>" size="74" />
					</div>
<?php } ?>
				</div>

				<div class="itembox">

					<!-- location -->
<?php if (!$form_location) { ?>
					<div class="formitem<?php echo $errorcss['location']; ?>">
						<div class="ititle">
							<label for="location"><?php echo $_t['location']; ?> <?php Outline::dispatch('optional', array("id" => 'location')); ?></label>
						</div>
						<div class="right">
							<div class="right">
								<input name="location" type="text" class="forminput" id="location" value="<?php echo $output['location']; ?>" size="50" />
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
								<select name="country" style="width:220px;" id="country" class="forminput f12">
								<?php foreach ($countries as $c) { ?><option<?php if ($c[country]==$output[country]) { ?> selected="selected"<?php } ?>><?php echo $c[country]; ?></option><?php } ?>
								</select>
							</div>
						</div>
<?php } ?>
<?php if (!$form_LatLng) { ?>
						<div class="right<?php if ($output[location]) { echo $errorcss['LatLng']; } ?>">
							<div class="formitem right">
								<input type="text" name="LatLng" class="forminput right" id="LatLng" value="<?php echo $output['LatLng']; ?>" size="10" />
							</div>
							<div class="ititle right<?php echo $errorcss['LatLng']; ?>">
								<label for="LatLng"><?php echo $_t['lat']; ?>,<?php echo $_t['lng']; ?>
								<?php Outline::dispatch('optional_short', array("id" => 'LatLng')); ?>
								</label> &nbsp;
							</div>
						</div>
<?php } ?>
						<div class="cl"></div>
					</div>

					<?php if (!model::$id) { ?>
					<div class="formitem">
						<div class="sep"></div>
					</div>
					<!-- rating -->
<?php if (!$form_rating) { ?>
					<div class="formitem">
						<div class="left">
							<label for="ratingselector" class="<?php echo $errorcss['rating']; ?>"><?php echo $_t['initialrating']; ?></label>
						    <select name="rating" id="ratingselector" class="selector">
						    	<?php $outline_for_var = new OutlineIterator($rating[min], $rating[max], $rating[step]); while ($outline_for_var->next()) { $var = $outline_for_var->index; ?>
									<option<?php if ($output[rating] == $var) { ?> selected="selected"<?php } ?>><?php echo $var; ?></option>
								<?php } ?>
						    </select>
							<?php Outline::dispatch('optional', array("id" => 'rating')); ?>
						</div>
						<div class="mtop5 right mright5">
					    	<?php $outline_for_var = new OutlineIterator($rating[min]+0.5, $rating[max], $rating[step]); while ($outline_for_var->next()) { $var = $outline_for_var->index; ?>
								<input type="radio" class="star {split:2}" name="rating2"<?php if ($output[rating] == $var) { ?> checked='checked'<?php } ?> value="<?php echo $var; ?>" />
							<?php } ?>
						</div>
						<div class="cl"></div>
						<?php if (model::$id) { ?>
							<div class="f10" style="color:#A0A0A0">Resetting the rating will delete all user ratings!</div>
						<?php } ?>
					</div>
<?php } ?>
					<?php } ?>

				</div>


				<!-- 2257record -->
				<?php $outline_include_1 = new Outline('includes/2257record'); require $outline_include_1->get(); ?>


				<div class="c"><br /></div>

				
				<div>
					<input type="submit" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
				</div>


			</div><!--left_col-->
		</form>
		<div class="cl"></div>
		<div class="left_col">


			<div class="itembox">
				<div class="left"><h4><?php echo $_t['thumbnails']; ?></h4></div>
				<div class="right italic mtop10">
					<small><?php echo $_t['supportedfiletypes']; ?>: <?php echo $allowed_mime_types; ?></small>
				</div>
				<div class="c"></div>

				<?php $outline_for_k = new OutlineIterator(0, count($thumbsizes)-1, 1); while ($outline_for_k->next()) { $k = $outline_for_k->index; ?>
					<fieldset style="padding:7px;" class="<?php echo $errorcss[thumbs][$k]; ?>">
						<legend>
							<?php echo $_t['upload']; ?> <?php echo $thumbsizes[$k][width]; ?>x<?php echo $thumbsizes[$k][height]; ?> (W x H) <?php echo $_t['image']; ?>
							<?php if ($output[thumbs][$k]) { ?><img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -3px;" /> <?php } ?>
						</legend>
						<form action="<?php echo $__adminurl; ?>/lib/ajaxupload.php" method="post" name="w<?php echo $thumbsizes[$k][width]; ?>h<?php echo $thumbsizes[$k][height]; ?>" id="w<?php echo $thumbsizes[$k][width]; ?>h<?php echo $thumbsizes[$k][height]; ?>" enctype="multipart/form-data" class="fuploadform">
						<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
							<input type="hidden" name="maxSize" value="9999999999" />
							<input type="hidden" name="maxW" value="<?php echo $thumbsizes[$k][width]; ?>" />
							<input type="hidden" name="maxH" value="<?php echo $thumbsizes[$k][height]; ?>" />
							<input type="hidden" name="type" value="model" />
							<input type="hidden" name="colorR" value="255" />
							<input type="hidden" name="colorG" value="255" />
							<input type="hidden" name="colorB" value="255" />
							<input type="hidden" name="thnumber" value="<?php echo $k; ?>" />
							<input type="hidden" name="filename" value="<?php echo outline__default($output[thumbs][$k], 'filename'); ?>" />
							<p><input type="file" class="forminput<?php echo $errorcss[thumbs][$k]; ?>" name="filename" size="53" value="" onchange="ajaxUpload(this.form,'<?php echo $__adminurl; ?>/lib/ajaxupload.php','upload_area<?php echo $k; ?>','<?php echo $_t['fileuploading']; ?>. <?php echo $_t['pleasewait']; ?>...&lt;br /&gt;&lt;img src=\'<?php echo $__siteurl; ?>/img/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;','&lt;img src=\'<?php echo $__siteurl; ?>/img/icons/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; <?php echo $_t['thumbnailuploaderror']; ?>'); return false;" /></p>
							<noscript>
								<p><input type="submit" name="submit" value="<?php echo $_t['uploadimage']; ?>" /></p>
							</noscript>
						</form>
					</fieldset>
				<?php } ?>
			</div> <!--itembox-->

		</div><!-- left_col-->

	<div class="c"><br /></div>

</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>