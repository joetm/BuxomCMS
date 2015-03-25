<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript"><!--
function chgimgopacity(id, value)
{
	$("#"+id).fadeTo(200, value);
}

function unmark(id) {
	var numeric_id = id.replace("vidthumb","");
	theimginput = "[name=vidthumb\\["+numeric_id+"\\]\\[mark\\]]";
	current = $(theimginput).attr("value");
	opacity = $("#"+id).css('opacity');

//	alert(id);
//	alert(numeric_id);
//	alert(theimginput);
//	alert(current);
//	alert(opacity);

	if (opacity < 1 && current == 0)
	{
		chgimgopacity(id, 1.0);
		$(theimginput).attr("value", 1);
	}
	else
	{
		chgimgopacity(id, 0.3);
		$(theimginput).attr("value", 0);
	}
} //unmark

function removeitem(item)
{
	//remove div container
	$(item).slideUp('fast', function() {
		// Animation complete.
		$(item).remove();
	});
}

$('.deletefromupdate').live('click',function() {

	var numeric_id = $(this).attr('id').replace("x[", "").replace("]", "");

	var check = $("[name=x"+numeric_id+"]").attr("checked");

	if (!check)
	{
		//just remove the item, but do not delete video file
		removeitem('#item'+numeric_id);
	}
	else
	{
		//remove item and delete video

		var filename = $("[name=video\\["+numeric_id+"\\]\\[filename\\]]").attr("value");

		$.ajax({
			type: 'POST',
			async: false,
			url: "<?php echo $__adminurl; ?>/lib/step2video.php",
			data: ({"filename": filename,
					"securitytoken":- '<?php echo $securitytoken; ?>' }),
			dataType: "text",
			global: false,
			error: function(XMLHttpRequest, text, errorThrown){
				$('#errormessage').html("Error: "+text);
				$('#errormessage').show('fast');
			},
			success: function(ret, text, XMLHttpRequest){
				//alert(ret);
				if(ret != "success")
				{
					$('#errormessage').html("Error: "+ret);
					$('#errormessage').show('fast');
				}
				removeitem('#item'+numeric_id);
			} //success function
		}); //ajax

	} //else

});

$('.imageclick').live('click',function() {
	unmark($(this).attr("id"));
});

$('.imageclick').live('mouseover',function() {
		$(".imageclick").css("cursor","pointer");
});

function sec2hms(sec)
{
	var hms = "";

	var hours = Math.floor(sec / 3600);

	hms = hours + ":";

	minutes = Math.floor(Math.floor(sec / 60) % 60);

	//pad minutes
	if(minutes<9)
		minutes = '0'+minutes;

	hms = hms + minutes + ":";

	seconds = Math.floor(sec % 60);

	hms = hms + seconds;

	return hms;
}

function humannumber(num) {
	var precision = 3;



	if(num>1000000)
	{
		newnum = Math.floor(num/1000000);
		residue = '';//num.slice(1);
		newnum = newnum+residue+'m';
	}
	else if(num>1000)
	{
		newnum = Math.floor(num/1000);
		residue = '';//num.slice(1);
		newnum = newnum+residue+'k';
	}
	else
		newnum = num;

	return newnum;
}


$(document).ready(function(){
	$(".dynamic").change(function () {

//		alert($(this).val());
//		alert($(this).attr("id"));

		var arr = $(this).attr("id").split("_");

		var data = $(this).val();

		//fork type
		if(arr[0]=='duration')
		{
			var newval = sec2hms(data);
		}
		else if(arr[0]=='bitrate')
		{
			var newval = humannumber(data);
		}

		$('input[name=video\\['+arr[1]+'\\]\\['+arr[0]+'_human\\]]').val(newval);
	});
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

			<?php if ($cropping_time) { ?>
				<div class="right gray">Cropping time: <?php echo $cropping_time; ?> sec</div>
			<?php } ?>
			<div id="errormessage" class="hidden error left" align="center"></div>
			<div class="c"></div>

			<div id="thumbwrap" align="center">

				<div id="zend-progressbar-container">
					<div id="zend-progressbar-done"></div>
				</div>

				<form action="<?php echo $__adminurl; ?>/finishupdate" method="post" name="newupdate" id="finishupdate">
					<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
					<input type="hidden" name="type" value="videoset" />

					<div class="itembox c mtop10">
						<?php $j = 0; ?>
						<?php foreach ($thumbnails as $thumb) { ?>
							<?php if ($thumb[filename]) { ?>
								<div class="left" style="padding:3px">
									
									<img src="<?php echo $thumb[url]; ?>?<?php echo time(); ?>" class="imageclick" id="vidthumb<?php echo $j; ?>" alt="" border="0" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />

									<input type='hidden' name='vidthumb[<?php echo $j; ?>][mark]' value='1' />
									<input type='hidden' name='vidthumb[<?php echo $j; ?>][filename]' value='<?php echo $thumb[filename]; ?>' />

								</div>

								<?php $j++; ?>

							<?php } ?>
						<?php } ?>
						<div class="c"></div>
					</div>

					<?php foreach ($videos as $video) { ?>
						<?php if ($video[filename]) { ?>
							<div class="itembox" style="text-align:left" id="item<?php echo $video['id']; ?>">

								<input type="hidden" name="video[<?php echo $video['id']; ?>]" value="<?php echo $video['id']; ?>" />
								<input type="hidden" name="video[<?php echo $video['id']; ?>][filename]" value="<?php echo $video['filename']; ?>" />

								<div class="right mright10 mtop10">
									<div>
										<a href="javascript:void(0);" id="x[<?php echo $video['id']; ?>]" class="deletefromupdate">Remove from Update</a>
									</div>
									<div class="mtop5">
										<?php echo $_t['alsodeletefile']; ?> <input type="checkbox" name="x<?php echo $video['id']; ?>" />
									</div>
								</div>

								<div class="left mright10">
									
									<img src="<?php echo $video['preview']; ?>?<?php echo time(); ?>" border="2" alt="preview" />
								</div>

								<div class="left mright10" style="width:320px">
									<div class="mbot5">Filename: <?php echo $video['filename']; ?></div>

									<div class="ititle">
										<label for="format_<?php echo $video['id']; ?>">Format:</label>
									</div>
									<div class="right">
										<input name="video[<?php echo $video['id']; ?>][format]" type="text" tabindex="4" class="forminput" id="format_<?php echo $video['id']; ?>" value="<?php echo outline__default($video['video_format'], 'N/A'); ?>" size="30" maxlength="50" />
									</div>
									<div class="c"></div>

									<div class="ititle">
										<label for="width_<?php echo $video['id']; ?>">Width:</label>
									</div>
									<div class="right">
										<input name="video[<?php echo $video['id']; ?>][width]" type="text" tabindex="4" class="forminput" id="width_<?php echo $video['id']; ?>" value="<?php echo outline__default($video[video_width], 'N/A'); ?>" size="30" maxlength="50" />
									</div>
									<div class="c"></div>

									<div class="ititle">
										<label for="height_<?php echo $video['id']; ?>">Height:</label>
									</div>
									<div class="right">
										<input name="video[<?php echo $video['id']; ?>][height]" type="text" tabindex="4" class="forminput" id="height_<?php echo $video['id']; ?>" value="<?php echo outline__default($video[video_height], 'N/A'); ?>" size="30" maxlength="50" />
									</div>
									<div class="c"></div>

									<div class="ititle">
										<label for="duration_<?php echo $video['id']; ?>">Duration:</label>
									</div>
									<div class="right">
										<input name="video[<?php echo $video['id']; ?>][length]" type="text" tabindex="4" class="forminput dynamic" id="duration_<?php echo $video['id']; ?>" value="<?php echo $video['length']; ?>" size="12" maxlength="50" />

										<input name="video[<?php echo $video['id']; ?>][duration_human]" type="text" tabindex="4" class="forminput dynamic gray" id="durationhuman_<?php echo $video['id']; ?>" value="<?php echo $video['duration']; ?>" size="11" maxlength="50" style="background-color:#FFFFFF;border:0px;" disabled="disabled" />
									</div>
									<div class="c"></div>

									<div class="ititle">
										<label for="bitrate_<?php echo $video['id']; ?>">Bitrate:</label>
									</div>
									<div class="right">
										<input name="video[<?php echo $video['id']; ?>][bitrate]" type="text" tabindex="4" class="forminput dynamic" id="bitrate_<?php echo $video['id']; ?>" value="<?php echo outline__default($video[bitrate], 'N/A'); ?>" size="12" maxlength="50" />

										<input name="video[<?php echo $video['id']; ?>][bitrate_human]" type="text" tabindex="4" class="forminput dynamic gray" id="bitratehuman_<?php echo $video['id']; ?>" value="<?php echo $video[human_bitrate]; ?>" size="11" maxlength="50" style="background-color:#FFFFFF;border:0px;" disabled="disabled" />
									</div>
									<div class="c"></div>

									<div class="ititle">
										<label for="fps_<?php echo $video['id']; ?>">Fps:</label>
									</div>
									<div class="right">
										<input name="video[<?php echo $video['id']; ?>][fps]" type="text" tabindex="4" class="forminput" id="fps_<?php echo $video['id']; ?>" value="<?php echo outline__default($video['video_fps'], 'N/A'); ?>" size="30" maxlength="50" />
									</div>
									<div class="c"></div>

								</div>

								<div class="c"></div>
							</div>
						<?php } ?>
					<?php } ?>

					<div class="c"><br /></div>

					<div class="finish-button">
						<?php if (!isset($_GET['step2'])) { ?>
							<input type="submit" id="abortbutton" class="submitbutton" name="abort" value="<?php echo $_t['abort']; ?>" />
						<?php } ?>

						<input type="submit" id="submitbutton" class="submitbutton" name="submit" value="<?php echo $_t['finalizeupdate']; ?>" />
					</div>
				</form>
			</div>

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>