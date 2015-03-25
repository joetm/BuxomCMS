<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>


<?php Outline::register_function('outline__user_admin_admin_update_optional', 'optional'); if (!function_exists('outline__user_admin_admin_update_optional')) { function outline__user_admin_admin_update_optional($args) { extract($args+array("id" => ''));  if (!isset(update::$mandatory[$id])) { ?><span class='f10 optional'>* (optional)</span><?php } } } ?>
<?php Outline::register_function('outline__user_admin_admin_update_optional_short', 'optional_short'); if (!function_exists('outline__user_admin_admin_update_optional_short')) { function outline__user_admin_admin_update_optional_short($args) { extract($args+array("id" => ''));  if (!isset(update::$mandatory[$id])) { ?><span class='f10 optional'>*</span><?php } } } ?>


<script type='text/javascript' src='<?php echo $__adminurl; ?>/js/jquery.autocomplete.min.js'></script>
<link rel="stylesheet" type="text/css" href="<?php echo $__adminurl; ?>/js/jquery.autocomplete.css" />


<script type="text/javascript"><!--
function ClearInput(value, id){
	var input = document.getElementById(id);
	if(value == input.value){
		input.value = '';
	}else{
		input.value = input.value;
	}
}

function hideallvideo()
{
		$(".videoonly").hide();
		$(".pictureonly").show();
		<?php if (!update::$id) { ?>
			$("#submitall").attr("value","<?php echo $_t['gotostep2']; ?>");
		<?php } else { ?>
			$("#submitall").attr("value","<?php echo $_t['save']; ?>");
		<?php } ?>
		$("#slugurl").html("set");
}
function hideallpics()
{
		$(".pictureonly").hide();
		$(".videoonly").show();
//		$("#submitall").attr("value","<?php echo $_t['submitupdate']; ?>");
		$("#slugurl").html("video");
}

$(document).ready(function(){

	
	<?php Outline::register_function('outline__user_admin_admin_update_autocomplete', 'autocomplete'); if (!function_exists('outline__user_admin_admin_update_autocomplete')) { function outline__user_admin_admin_update_autocomplete($args) { extract($args+array("id" => '', "type" => '', "securitytoken" => $securitytoken));  ?>
		$("#<?php echo $id; ?>").autocomplete("lib/autocomplete.php", {
			width: 320,
			max: 8,
			highlight: false,
			scroll: true,
			cacheLength: 8,
			scrollHeight: 100,
			extraParams: {securitytoken: '<?php echo $securitytoken; ?>',
						type: '<?php echo $type; ?>'},
			formatItem: function(data, i, n, value) {
				return value;
			}
		});
	<?php } } ?>
	<?php Outline::dispatch('autocomplete', array("id" => 'picturefolder', "type" => 'pics', "securitytoken" => $securitytoken)); ?>
	<?php Outline::dispatch('autocomplete', array("id" => 'videodirectory', "type" => 'video', "securitytoken" => $securitytoken)); ?>
	

	<?php if ($_page[errormessage]) { ?>
		$('#submitall').removeAttr("disabled");
		//hide loading img
		$('#loading_anim').hide();
	<?php } ?>

	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 1500);

	//calculate a dynamic width for the thumbnail boxes
		var contwidth = $("#admincontent").width();
		var lcw = $(".left_col").width();
		var bw = contwidth - lcw - 20; //padding of 20
		$('#box').css('width', bw);

	$("#typepics").click(function () {
		hideallvideo();
    });
	$("#typevideo").click(function () {
		hideallpics();
    });

	if($("#typevideo").is(":checked"))
		hideallpics();
	else
		hideallvideo();










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

	
	$("#newupdateform").submit(function(){
		$('#submitall').attr("disabled", "disabled");

		//show loading img
		if($("#typevideo").is(":checked"))	$('#loading_anim').show();

		//animated dots
		var dots = '';
		window.setInterval(function(){
			if($('#dots').html() == '.....'){
				dots = '.';
			}
			else{
				dots += '.';
			}
			$('#dots').html(dots);
		}, 1000);

		return true;
	});

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

<script src="<?php echo $__siteurl; ?>/js/jquery.floatobject-1.0.js" type="text/javascript" ></script>
<script type="text/javascript"><!--
$(document).ready(main);
	function main()
	{
		$("#box").makeFloat({x:"current",y:"current",alwaysVisible:true});
	}
//-->
</script>

<script type="text/javascript" src="<?php echo $__adminurl; ?>/js/jquery.numeric.js"></script>
<script type="text/javascript"><!--
$(
	function(){ $(".numeric").numeric(); }
);
//-->
</script>

		<a name="top"></a>
		<div id="admincontent">

			<?php if ($_page[errormessage] || $errors) { ?>
				<div id="errors" class="c">
					<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
					<?php echo $_page[errormessage]; ?>
				</div>

				<?php foreach ($errors as $err) { ?>
					<div><?php echo $err; ?></div>
				<?php } ?>
			<?php } ?>

			<form action="<?php echo $__adminurl; ?>/update<?php if (update::$id) { ?>?edit=<?php echo update::$id; } ?>" method="post" name="newupdate" id="newupdateform">
				<input type="hidden" name="_action" value="addnew" />
				<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

				<div id="box" class="right_col mtop4">
					<div class="pictureonly">
						<!-- thumbnail -->
						picture
						<?php if ($output[action]=='edit') { ?>
							<?php $outline_for_k = new OutlineIterator(0, count($picturethumbsizes)-1, 1); while ($outline_for_k->next()) { $k = $outline_for_k->index; ?>
								<div id="picupload_area<?php echo $k; ?>" class="upload_area<?php echo $errorcss[picthumbs][$k]; ?>">
									<?php if ($output[picthumbs][$k]) { ?> 
										<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -4px;" /> <?php echo $_t['success']; ?>!<br />
										<img src="<?php echo $output[picthumbs][$k][path]; ?>?<?php echo time(); ?>" border="0" /><br />
										<input type="text" name="picthumbs[]" value="<?php echo $output[picthumbs][$k][path]; ?>" />
									<?php } else { ?>
										<?php echo $_t['thumbnail']; ?> <?php echo $picturethumbsizes[$k][width]; ?>x<?php echo $picturethumbsizes[$k][height]; ?> (WxH)
									<?php } ?>
								</div>
							<?php } ?>
						<?php } else { ?>
							<?php $outline_for_k = new OutlineIterator(0, count($picturethumbsizes)-1, 1); while ($outline_for_k->next()) { $k = $outline_for_k->index; ?>
								<div id="picupload_area<?php echo $k; ?>" class="upload_area<?php echo $errorcss[picthumbs][$k]; ?>">
									<?php if ($output[picthumbs][$k]) { ?> 
										<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -4px;" /> <?php echo $_t['success']; ?>!<br />
										<img src="temp/<?php echo $output[picthumbs][$k]; ?>?<?php echo time(); ?>" border="0" /><br />
										<input type="text" name="picthumbs[]" value="<?php echo $output[picthumbs][$k]; ?>" />
									<?php } else { ?>
										<?php echo $_t['thumbnail']; ?> <?php echo $picturethumbsizes[$k][width]; ?>x<?php echo $picturethumbsizes[$k][height]; ?> (WxH)
									<?php } ?>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
					<!-- thumbnail -->
					<!-- thumbnail -->
					<div class="videoonly">
						video
						<?php if ($output[action]=='edit') { ?>
							<?php $outline_for_k = new OutlineIterator(0, count($videothumbsizes)-1, 1); while ($outline_for_k->next()) { $k = $outline_for_k->index; ?>
								<div id="videoupload_area<?php echo $k; ?>" class="upload_area<?php echo $errorcss[videothumbs][$k]; ?>">
									<?php if ($output[videothumbs][$k]) { ?> 
										<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -4px;" /> <?php echo $_t['success']; ?>!<br />
										<img src="<?php echo $output[videothumbs][$k][path]; ?>?<?php echo time(); ?>" border="0" /><br />
										<input type="hidden" name="videothumbs[]" value="<?php echo $output[videothumbs][$k][path]; ?>" />
									<?php } else { ?>
										<?php echo $_t['thumbnail']; ?> <?php echo $videothumbsizes[$k][width]; ?>x<?php echo $videothumbsizes[$k][height]; ?> (WxH)
									<?php } ?>
								</div>
							<?php } ?>
						<?php } else { ?>
							<?php $outline_for_k = new OutlineIterator(0, count($videothumbsizes)-1, 1); while ($outline_for_k->next()) { $k = $outline_for_k->index; ?>
								<div id="videoupload_area<?php echo $k; ?>" class="upload_area<?php echo $errorcss[videothumbs][$k]; ?>">
									<?php if ($output[videothumbs][$k]) { ?> 
										<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -4px;" /> <?php echo $_t['success']; ?>!<br />
										<img src="temp/<?php echo $output[videothumbs][$k]; ?>?<?php echo time(); ?>" border="0" /><br />
										<input type="hidden" name="videothumbs[]" value="<?php echo $output[videothumbs][$k]; ?>" />
									<?php } else { ?>
										<?php echo $_t['thumbnail']; ?> <?php echo $videothumbsizes[$k][width]; ?>x<?php echo $videothumbsizes[$k][height]; ?> (WxH)
									<?php } ?>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
					<!-- thumbnail -->
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

						<div class="right gray"><?php echo $_t['updateid']; ?>: <?php echo $updateid; ?></div>

						<!-- type -->
						<div class="formitem formgroup<?php echo $errorcss['type']; ?>">
							<label>Type</label>
								<input id="typepics" type="radio" name="type" tabindex="1" value="pictureset"<?php if ($output[type] != 'videoset') { ?> checked="checked"<?php } if (update::$id && $output[type] == 'videoset') { ?> disabled="disabled" /> <span class="gray"><?php echo $_t['pictures']; ?></span><?php } else { ?> /> <?php echo $_t['pictures']; } ?>
								<input id="typevideo" type="radio" name="type" tabindex="2" value="videoset"<?php if ($output[type] == 'videoset') { ?> checked="checked"<?php } if (update::$id && $output[type] != 'videoset') { ?> disabled="disabled" /> <span class="gray"><?php echo $_t['video']; ?></span><?php } else { ?> /> <?php echo $_t['video']; } ?>
						</div>

						<!-- video url or picture folder -->
						<div id="dvideourl" class="hidden videoonly formitem">
							<div class="<?php echo $errorcss['videodirectory']; ?>">
								<div class="ititle">
									<label for="videodirectory">Video directory
									<span class="f10">(path to member video directory)</span></label>
								</div>
								<div class="left">
									<span class="gray"><?php echo $_videodirectory; ?></span>...<input id="videodirectory" name="videodirectory" type="text" tabindex="3" class="forminput autocomplete" value="<?php echo $output['videodirectory']; ?>" size="73" maxlength="1000" />
								</div>
							</div>
							<div class="cl"></div>
							<div class="<?php echo $errorcss['videourl']; ?>">
								<div class="ititle">
									<label for="videourl">Video url
									<span class="f10">(url to streaming video in member area)</span></label>
								</div>
								<?php $html5video = false; ?>
								<?php if (!$html5video) { ?>
									<div class="left">
										<input name="videourl" type="text" tabindex="3" class="forminput" id="videourl" value="<?php echo outline__default($output['videourl'], $_videourl); ?>" size="73" maxlength="1000" />
									</div>
								<?php } else { ?>
									<div class="c">
										<div class="left mtop5">video/mp4:</div>
										<div class="right">
											<input name="videourl[mp4]" type="text" tabindex="3" class="forminput" value="<?php echo outline__default($output['videourl'], $_videourl); ?>" size="57" maxlength="1000" />
										</div>
									</div>
									<div class="c">
										<div class="left mtop5">video/ogg:</div>
										<div class="right">
											<input name="videourl[ogg]" type="text" tabindex="3" class="forminput" value="<?php echo outline__default($output['videourl'], $_videourl); ?>" size="57" maxlength="1000" />
										</div>
									</div>
									<div class="c">
										<div class="left mtop5">video/webm:</div>
										<div class="right">
											<input name="videourl[webm]" type="text" tabindex="3" class="forminput" value="<?php echo outline__default($output['videourl'], $_videourl); ?>" size="57" maxlength="1000" />
										</div>
									</div>
								<?php } ?>
							</div>
							<div class="cl"></div>
							<?php if (!$form_freevideourl) { ?>
								<div class="<?php echo $errorcss['freevideourl']; ?>">
									<div class="ititle">
										<label for="freevideourl">Free trailer video url
										<span class="f10">(outside of member area)</span></label>
									</div>
									<?php if (!$html5video) { ?>
										<div class="left">
											<input name="freevideourl" type="text" tabindex="4" class="forminput" id="freevideourl" value="<?php echo outline__default($output['freevideourl'], $_freevideourl); ?>" size="73" maxlength="1000" />
										</div>
									<?php } else { ?>
										<div class="c">
											<div class="left mtop5">video/mp4:</div>
											<div class="right">
												<input name="freevideourl[mp4]" type="text" tabindex="3" class="forminput" value="<?php echo outline__default($output['freevideourl'], $_freevideourl); ?>" size="57" maxlength="1000" />
											</div>
										</div>
										<div class="c">
											<div class="left mtop5">video/ogg:</div>
											<div class="right">
												<input name="freevideourl[ogg]" type="text" tabindex="3" class="forminput" value="<?php echo outline__default($output['freevideourl'], $_freevideourl); ?>" size="57" maxlength="1000" />
											</div>
										</div>
										<div class="c">
											<div class="left mtop5">video/webm:</div>
											<div class="right">
												<input name="videourl[webm]" type="text" tabindex="3" class="forminput" value="<?php echo outline__default($output['freevideourl'], $_freevideourl); ?>" size="57" maxlength="1000" />
											</div>
										</div>
									<?php } ?>
								</div>
								<div class="cl"></div>
							<?php } ?>
						</div>
						<div id="dpicturefolder" class="pictureonly formitem<?php echo $errorcss['picturefolder']; ?>">
							<div class="ititle">
								<label for="picturefolder">Picture folder url
								<span class="f10">(relative path in member picture folder)</span></label>
							</div>
							<div class="left">
								<span style="color:#8f8f8f"><?php echo $_memberpath; ?></span>...<input name="picturefolder" type="text" tabindex="5" class="forminput autocomplete" id="picturefolder" value="<?php echo $output['picturefolder']; ?>" size="73" maxlength="1000" />
							</div>
							<div class="cl"></div>
						</div>

						<div class="c formitem" style="margin-bottom:15px;">
							<div class="sep"></div>
						</div>

						<!-- datepicker -->
						<div id="datepickerwrap">
							<label><?php echo $_t['scheduleddate']; ?>:</label><br />
							<div id="datepicker"></div>
							<div class="formitem<?php echo $errorcss['date']; ?>">
								<label><?php echo $_t['date']; ?></label><br />
								<input name="date" value="<?php echo $output['date']; ?>" tabindex="12" type="text" id="date" size="8" />
							</div>
						</div>

						<div class="formgroup">

							<!-- update title -->
							<div class="formitem<?php echo $errorcss['title']; ?>">
								<div class="ititle">
									<label for="title"><?php echo $_t['title']; ?> <?php Outline::dispatch('optional_short', array("id" => 'title')); ?></label>
								</div>
								<div class="right">
									<input name="title" style="width:230px" tabindex="10" type="text" class="forminput" id="title" value="<?php echo $output['title']; ?>" />
								</div>
								<div class="cl"></div>
							</div>

							<!-- slug -->
							<div class="formitem<?php echo $errorcss['slug']; ?>">
								<div class="ititle">
									<label for="slug"><?php echo $_t['slug']; ?> <?php Outline::dispatch('optional', array("id" => 'slug')); ?></label>
								</div>
								<div class="right">
									<span style="color:#8f8f8f">/<span id="slugurl">set</span>/</span>
									<input name="slug" style="width:180px" tabindex="11" type="text" class="forminput" id="slug" value="<?php echo $output['slug']; ?>" />
								</div>
								<div class="cl"></div>
							</div>

							<div class="formitem">
								<div class="sep"></div>
							</div>

							<div id="selbox">












































							</div><!-- /selbox-->

							<div class="formitem">
								<div class="sep"></div>
							</div>

						</div> <!-- /formgroup -->

						<div class="c"></div>

<?php if (!$form_rating && $rating['type']=='stars') { ?>
						<!-- rating -->
						<div class="formitem">
							<div class="left">
								<label for="ratingselector" class="<?php echo $errorcss['rating']; ?>"><?php echo $_t['initialrating']; ?> </label>
							    <select name="rating" tabindex="30" id="ratingselector" class="selector">
							    	<?php $outline_for_var = new OutlineIterator($rating[min], $rating[max], $rating[step]); while ($outline_for_var->next()) { $var = $outline_for_var->index; ?>
										<option<?php if ($output[rating] == $var) { ?> selected="selected"<?php } ?>><?php echo $var; ?></option>
									<?php } ?>
							    </select>
								<?php Outline::dispatch('optional', array("id" => 'rating')); ?>

								
								<?php if (isset($_GET['edit'])) { ?>
									<input type="checkbox" class="forminput" style="margin-right:0px;" value="1" name="resetrating" /> Reset?
								<?php } ?>

							</div>
							<div class="mtop5 right mright5">
						    	<?php $outline_for_var = new OutlineIterator($rating[min]+0.5, $rating[max], $rating[step]); while ($outline_for_var->next()) { $var = $outline_for_var->index; ?>
									<input type="radio" class="star {split:2}" name="rating2"<?php if ($output[rating] == $var) { ?> checked='checked'<?php } ?> value="<?php echo $var; ?>" />
								<?php } ?>
							</div>
							<div class="cl"></div>
						</div>
<?php } ?>
					</div>

<?php if (!$form_description || !$form_tags) { ?>
					<div class="itembox">
	<?php if (!$form_description) { ?>
						<!-- description -->
						<div class="formitem<?php echo $errorcss['description']; ?>">
							<label for="description"><?php echo $_t['description']; ?> <?php Outline::dispatch('optional', array("id" => 'description')); ?></label><br />
							<textarea name="description" tabindex="40" id="description" class="forminput" cols="57" rows="6"><?php echo $output['description']; ?></textarea>
						</div>
	<?php } ?>
	<?php if (!$form_tags) { ?>
						<!-- tags -->
						<div class="formitem<?php echo $errorcss['tags']; ?>">
							<label for="tags"><?php echo $_t['tags']; ?> <?php Outline::dispatch('optional', array("id" => 'tags')); ?></label>
							<span class="f10">(comma separated)</span>
							<br />
							<input name="tags" tabindex="41" type="text" class="forminput" id="tags" value="<?php echo $output['tags']; ?>" size="74" />
						</div>
	<?php } ?>
					</div>
<?php } ?>

					<!-- 2257record -->
					<?php $outline_include_1 = new Outline('includes/2257record'); require $outline_include_1->get(); ?>

					<div class="c"><br /></div>
					<div class="left">
						<input type="submit" id="submitall" tabindex="99" class="submitbutton" name="submit" value="<?php echo $_t['gotostep2']; ?>" />
					</div>
					<div class="left hidden gray mtop10 mleft10" id="loading_anim"><img src="<?php echo $__adminurl; ?>/js/indicator.gif" style="position:relative;top:3px;" width="16" height="16" alt="loading" /> <?php echo $_t['grabbing_frames']; ?>. <?php echo $_t['this_can_take_minutes']; ?><span id="dots">...</span></div>

				</div>
			</form>
			<div class="cl"></div>
			<div class="left_col">


				<div class="itembox">
					<div class="left"><h4><?php echo $_t['thumbnails']; ?></h4></div>
					<div class="right italic mtop10">
						<small class="right italic"><?php echo $_t['supportedfiletypes']; ?>: <?php echo $allowed_mime_types; ?></small>
					</div>
					<div class="c"></div>

					<div class="pictureonly">
						<?php $outline_for_i = new OutlineIterator(0, count($picturethumbsizes)-1, 1); while ($outline_for_i->next()) { $i = $outline_for_i->index; ?>
							<fieldset style="padding:7px;" class="<?php echo $errorcss[picthumbs][$k]; ?>">
								<legend><?php echo $_t['upload']; ?> <?php echo $picturethumbsizes[$i][width]; ?>x<?php echo $picturethumbsizes[$i][height]; ?> (W x H) <?php echo strtolower($_t['image']); ?></legend>
								<form action="<?php echo $__adminurl; ?>/lib/ajaxupload.php" method="post" name="w<?php echo $picturethumbsizes[$i][width]; ?>h<?php echo $picturethumbsizes[$i][height]; ?>" id="w<?php echo $picturethumbsizes[$i][width]; ?>h<?php echo $picturethumbsizes[$i][height]; ?>" enctype="multipart/form-data" class="fuploadform">
									<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
									<input type="hidden" name="maxSize" value="9999999999" />
									<input type="hidden" name="maxW" value="<?php echo $picturethumbsizes[$i][width]; ?>" />
									<input type="hidden" name="maxH" value="<?php echo $picturethumbsizes[$i][height]; ?>" />
									<input type="hidden" name="type" value="update" />
									<input type="hidden" name="colorR" value="255" />
									<input type="hidden" name="colorG" value="255" />
									<input type="hidden" name="colorB" value="255" />
									<input type="hidden" name="thnumber" value="<?php echo $i; ?>" />
									<input type="hidden" name="filename" value="<?php echo outline__default($output[picthumbs][$i], 'filename'); ?>" />
									<p><input type="file" class="forminput<?php echo $errorcss[$i]; ?>" tabindex="80" name="filename" size="50" value="" onchange="ajaxUpload(this.form,'<?php echo $__adminurl; ?>/lib/ajaxupload.php?filename=filename&amp;type=pics','picupload_area<?php echo $i; ?>','File Uploading Please Wait...&lt;br /&gt;&lt;img src=\'<?php echo $__siteurl; ?>/img/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;','&lt;img src=\'<?php echo $__siteurl; ?>/img/icons/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; Error in Upload, check settings and path info in source code.'); return false;" />
									<?php if ($output[picthumbs][$i]) { ?><img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -3px;" /> <?php } ?>
									</p>
									<noscript>
										<p><input type="submit" name="submit" value="<?php echo $_t['uploadimage']; ?>" /></p>
									</noscript>
								</form>
							</fieldset>
						<?php } ?>
					</div>

					<div class="videoonly">
						<?php $outline_for_i = new OutlineIterator(0, count($videothumbsizes)-1, 1); while ($outline_for_i->next()) { $i = $outline_for_i->index; ?>
							<fieldset style="padding:7px;" class="<?php echo $errorcss[videothumbs][$k]; ?>">
								<legend><?php echo $_t['upload']; ?> <?php echo $videothumbsizes[$i][width]; ?>x<?php echo $videothumbsizes[$i][height]; ?> (W x H) <?php echo strtolower($_t['image']); ?></legend>
								<form action="<?php echo $__adminurl; ?>/lib/ajaxupload.php" method="post" name="w<?php echo $videothumbsizes[$i][width]; ?>h<?php echo $videothumbsizes[$i][height]; ?>" id="w<?php echo $videothumbsizes[$i][width]; ?>h<?php echo $videothumbsizes[$i][height]; ?>" enctype="multipart/form-data" class="fuploadform">
								<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
									<input type="hidden" name="maxSize" value="9999999999" />
									<input type="hidden" name="maxW" value="<?php echo $videothumbsizes[$i][width]; ?>" />
									<input type="hidden" name="maxH" value="<?php echo $videothumbsizes[$i][height]; ?>" />
									<input type="hidden" name="type" value="update" />
									<input type="hidden" name="colorR" value="255" />
									<input type="hidden" name="colorG" value="255" />
									<input type="hidden" name="colorB" value="255" />
									<input type="hidden" name="thnumber" value="<?php echo $i; ?>" />
									<input type="hidden" name="filename" value="<?php echo outline__default($output[videothumbs][$i], 'filename'); ?>" />
									<p><input type="file" class="forminput<?php echo $errorcss[$i]; ?>" name="filename" tabindex="81" size="50" value="" onchange="ajaxUpload(this.form,'<?php echo $__adminurl; ?>/lib/ajaxupload.php?filename=filename&amp;type=video','videoupload_area<?php echo $i; ?>','File Uploading Please Wait...&lt;br /&gt;&lt;img src=\'<?php echo $__siteurl; ?>/img/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' /&gt;','&lt;img src=\'<?php echo $__siteurl; ?>/img/icons/error.gif\' width=\'16\' height=\'16\' border=\'0\' /&gt; Error in Upload, check settings and path info in source code.'); return false;" />
									<?php if ($output[videothumbs][$i]) { ?><img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -3px;" /> <?php } ?>
									</p>
									<noscript>
										<p><input type="submit" name="submit" value="<?php echo $_t['uploadimage']; ?>" /></p>
									</noscript>
								</form>
							</fieldset>
						<?php } ?>
					</div>
				</div><!-- /itembox -->

			</div><!-- /left_col -->

			<div class="c"><br /></div>

		</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>