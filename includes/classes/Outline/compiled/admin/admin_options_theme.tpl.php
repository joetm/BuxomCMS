<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript" src="<?php echo $__adminurl; ?>/js/jquery.numeric.js"></script>
<script type="text/javascript"><!--
$(
	function(){
		$(".numeric").numeric();
	}
);
//-->
</script>


<script type="text/javascript"><!--
$(document).ready(function(){

	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 1500);

	/*-----------------------*/
	/* REMOVE THUMBNAIL SIZE */
	/*-----------------------*/
	$(".btn").live('click',function(){

		var that = this;
		var internal_id = $(this).parents("tr").find("td:first").html();

		//decrease id on add button
		$(that).parents("table").find(".add").attr("id", internal_id);

		//show the previous delete button
		$(that).parents("table").find(".btn"+(internal_id-1)).show();

		//remove the row on success
		$(that).parent().parent().remove();

/*
				else
				{
					//we had an error removing the thumbnail row
					$(that).parent().parent().next().find("td:first").html('error');;
				}
*/
	});

	/*--------------------*/
	/* ADD THUMBNAIL SIZE */
	/*--------------------*/
	$(".add").live('click',function(){

		var that = this;

		var type = $(this).parents("table").attr("id");

		var width = parseInt($(this).parents("table").find(".width").val());
		var height = parseInt($(this).parents("table").find(".height").val());

		var newid = parseInt($(this).attr('id'));

		//add the row
		$(that).parents("tr").before("<tr><td>"+newid+"</td><td align='center'><input type='hidden' name='options["+type+"_thumbnailsize]["+newid+"][width]' value='"+width+"' />"+width+"</td><td align='center'><input type='hidden' name='options["+type+"_thumbnailsize]["+newid+"][height]' value='"+height+"' />"+height+"</td><td><a href='#thumbnails' class='btn"+newid+" btn' id='"+newid+"'><img src='<?php echo $__siteurl; ?>/img/icons/cancel.png' width='16' height='16' alt='' border='0' /></a></td></tr>");

		//increase id on add button
		$(that).attr("id", newid+1);

		//remove the previous delete button
		$(that).parents("table").find(".btn"+(newid-1)).hide();
	});

});
//-->
</script>

<?php $outline_include_1 = new Outline('includes/options_submenu'); require $outline_include_1->get(); ?>

	<a name="top"></a>
	<div id="admincontent">

		<?php if ($_page[successmessage]) { ?>
			<div id="success">
				<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[successmessage]; ?>
			</div>
		<?php } ?>

		<form action="<?php echo $__adminurl; ?>/options_theme" method="post" name="options" id="options">
		<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

		<div class="optiondiv">
			<h3><?php echo $_t['theme']; ?></h3>

			<fieldset>
				<div class="c">
					<div class="left mtop5" style="width:230px;"><?php echo $_t['frontend_theme']; ?>:</div>
					<div class="left">
						<select name="options[frontend_theme]" class="forminput">
							<?php foreach ($themes as $theme) { ?>
								<option<?php if ($theme==$options[frontend_theme]) { ?> selected="selected"<?php } ?>><?php echo $theme; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</fieldset>

		</div>

		<div class="optiondiv">
			<h3><?php echo $_t['thumbnailprocessing']; ?></h3>

			<fieldset>
				<div class="c">
					<div class="left mtop5" style="width:230px;"><?php echo $_t['sharpeningamount']; ?>:</div>
					<div class="left">
						<input type="text" name="options[thumbnailsharpen]" class="forminput" size="2" value="<?php echo $options['thumbnailsharpen']; ?>" />
						<span class="gray">(0...1)</span>
					</div>
				</div>

				<div class="c">
					<div class="left mtop5" style="width:230px;"><?php echo $_t['thumbnailquality']; ?>:</div>
					<div class="left">
						<input type="text" name="options[thumbnailquality]" class="forminput numeric" size="2" value="<?php echo $options['thumbnailquality']; ?>" />
						<span class="gray">(0...100)</span>
					</div>
				</div>

				<div class="c">
					<div class="left mtop5" style="width:230px;"><?php echo $_t['num_video_framegrabs']; ?>:</div>
					<div class="left">
						<input type="text" name="options[num_video_screencaps]" class="forminput numeric" size="2" value="<?php echo $options['num_video_screencaps']; ?>" />
						<span class="gray">(default: 20)</span>
					</div>
				</div>

				<div class="c">
					<div class="left mtop5" style="width:230px;"><?php echo $_t['make_anim']; ?>:</div>
					<div class="left mtop5">
						<input type="radio" value="true" name="options[make_anim]"<?php if ($options[make_anim] == 'true') { ?> checked="checked"<?php } ?> /> Yes
						<input type="radio" value="false" name="options[make_anim]"<?php if ($options[make_anim] == 'false') { ?> checked="checked"<?php } ?> /> No
					</div>
				</div>
			</fieldset>

		</div>

		<div class="optiondiv">
			<h3>Thumbnailing &amp; Framegrabbing</h3>
			<div class="optiondescription">
				Sizes of the automatically created thumbnails.
			</div>

			<fieldset>
				<div class="c">
					<div class="left mtop5" style="width:230px;">Picture Thumbnails:</div>
					<div class="left">
						Width:
						<input type="text" name="options[picturegrab_thumbnailsize][width]" class="forminput numeric" size="2" value="<?php echo $options['picturegrab_thumbnailsize']['width']; ?>" />
						Height:
						<input type="text" name="options[picturegrab_thumbnailsize][height]" class="forminput numeric" size="2" value="<?php echo $options['picturegrab_thumbnailsize']['height']; ?>" />
					</div>
				</div>

				<div class="c">
					<div class="left mtop5" style="width:230px;">Video Framegrabs:</div>
					<div class="left">
						Width:
						<input type="text" name="options[videograb_thumbnailsize][width]" class="forminput numeric" size="2" value="<?php echo $options['videograb_thumbnailsize']['width']; ?>" />
						Height:
						<input type="text" name="options[videograb_thumbnailsize][height]" class="forminput numeric" size="2" value="<?php echo $options['videograb_thumbnailsize']['height']; ?>" />
					</div>
				</div>
			</fieldset>

		</div>

		<div class="optiondiv">
			<a name="thumbnails"></a>

			<h3 class="mbot10"><?php echo $_t['thumbnailsizes']; ?></h3>
			<div class="optiondescription">
				Define the sizes of the manually uploaded update thumbnails.
			</div>

			<fieldset>
				<?php Outline::register_function('outline__user_admin_admin_options_theme_thumboption', 'thumboption'); if (!function_exists('outline__user_admin_admin_options_theme_thumboption')) { function outline__user_admin_admin_options_theme_thumboption($args) { extract($args+array("title" => '', "arr" => '', "_t" => ''));  ?>
				<div class="left mbot5 mtop5" style="margin-right:20px;">
					<h4 class="center"><?php echo ucwords($title); ?> <?php echo $_t['update']; ?></h4>

					<table border="0" cellpadding="0" cellspacing="0" id="<?php echo $title; ?>" class="thumbtable">
					<thead>
						<th class="normalfont"><?php echo $_t['internal_id']; ?></th>
						<th class="normalfont" align="center"><?php echo $_t['width']; ?></th>
						<th class="normalfont" align="center"><?php echo $_t['height']; ?></th>
						<th></th>
					</thead>
					<tbody>
					<?php $num = count($arr) - 1; ?>
					<?php $i = 0; ?>
					<?php foreach ($arr as $key => $ths) { ?>
						<tr id="thumbtr<?php echo $key; ?>">
							<td><?php echo $key; ?></td>
							<td align="center">
								<input type="hidden" name="options[<?php echo strtolower($title); ?>_thumbnailsize][<?php echo $i; ?>][width]" value="<?php echo $ths['width']; ?>" />
								<?php echo $ths['width']; ?>
							</td>
							<td align="center">
								<input type="hidden" name="options[<?php echo strtolower($title); ?>_thumbnailsize][<?php echo $i; ?>][height]" value="<?php echo $ths['height']; ?>" />
								<?php echo $ths['height']; ?>
							</td>
							<td>
								<a href="javascript:void(0);" class="btn<?php echo $key; ?> btn<?php if ($num != $i) { ?> hidden<?php } ?>" id="<?php echo $key; ?>"><img src="<?php echo $__siteurl; ?>/img/icons/cancel.png" width="16" height="16" alt="" border="0" /></a>
							</td>
						</tr>
						<?php $i++; ?>
					<?php } ?>
					<tr>
						<td class="noborder" class="ajaxerror"></td>
						<td class="noborder" align="center">
							<input class="numeric forminput width center" type="text" size="3" value="" />
						</td>
						<td class="noborder" align="center">
							<input class="numeric forminput height center" type="text" size="3" value="" />
						</td>
						<td class="noborder">
							<a href="javascript:void(0);" class="add" id="<?php echo $i; ?>">
								<img src="<?php echo $__siteurl; ?>/img/icons/add.png" alt="" border="0" width="16" height="16" />
							</a>
						</td>
					</tr>
					</tbody>
					</table>
				</div>
				<?php } } ?>

				<?php Outline::dispatch('thumboption', array("title" => "model", "arr" => $options[model_thumbnailsize], "_t" => $_t)); ?>

				<?php Outline::dispatch('thumboption', array("title" => "picture", "arr" => $options[picture_thumbnailsize], "_t" => $_t)); ?>

				<?php Outline::dispatch('thumboption', array("title" => "video", "arr" => $options[video_thumbnailsize], "_t" => $_t)); ?>

			</fieldset>
		</div>

		<div>
			<input type="submit" id="submitall" class="submitbutton" name="submit" value="<?php echo $_t['submit']; ?>" />
		</div>

		</form>

	</div><!-- admincontent-->

<?php $outline_include_2 = new Outline('_adminfooter'); require $outline_include_2->get(); Outline::finish(); ?>