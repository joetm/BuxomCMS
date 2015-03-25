<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript"><!--
function ProgressBar_Update(data)
{
	if (data.text != null)
	{
		document.getElementById('zend-progressbar-done').style.width = data.percent + '%';

		container = "<div class='container'><img src='"+data.text.url+"' border='0' class='imageclick' id='img"+data.text.id+"' /><input type='hidden' name='img["+data.text.id+"][mark]' value='0' /><input type='hidden' name='img["+data.text.id+"][filename]' value='"+data.text.filename+"' /><input type='hidden' name='img["+data.text.id+"][orig_width]' value='"+data.text.orig_width+"' /><input type='hidden' name='img["+data.text.id+"][orig_height]' value='"+data.text.orig_height+"' /></div>";

		$('#thumbnails').append(container);

		chgimgopacity('img'+data.text.id, 0.5);
	}
}
function ProgressBar_Finished(data)
{
	document.getElementById('zend-progressbar-done').style.width = '100%';
	$('#finish-button').show();
}

function ErrorMsg(error)
{
	$('#errormessage').html(error);
	$('#errormessage').show();
}

function chgimgopacity(id, value)
{
	$("#"+id).fadeTo(200, value);
}

function mark(id) {

	var numeric_id = id.replace("img","");

	theimg = "[name=img\\["+numeric_id+"\\]\\[mark\\]]";
	current = $(theimg).attr("value");

	opacity = $("#"+id).css('opacity');

	if (opacity < 1 && current == 0)
	{
		chgimgopacity(id, 1.0);
		$(theimg).attr("value", 1);
	}
	else
	{
		chgimgopacity(id, 0.5);
		$(theimg).attr("value", 0);
	}
} //mark

$('.imageclick').live('click', function() {
	mark($(this).attr("id"));
});
$('.imageclick').live('mouseover',function() {
		$(".imageclick").css("cursor","pointer");
});
$('.imageclick').live('mouseout',function() {
		$(".imageclick").css("cursor","default");
});

function openwindow(filename, width, height, imgwidth, imgheight, imgid)
{
	window.open("<?php echo $__adminurl; ?>/crop_popup?f="+filename+"&iw="+imgwidth+"&ih="+imgheight+"&img_id="+imgid, "_blank","height="+height+", width="+width+", status=no,toolbar=no,menubar=no,location=no,scrollbars=yes")
}

function Img() {
	if (Img.caller != Img.getInstance) {
		throw new Error("There is no public constructor for Img.");
	}
}
Img.__instance__ = null;  //define the static property
Img.getInstance = function () {
	if (this.__instance__ == null) {
		this.__instance__ = new Img();
	}
	return this.__instance__;
}

$('.imageclick').live('dblclick', function() {

	var img = Img.getInstance;

//	img.filename = $(this).attr("src"); //.replace("/thumb_", "");
	img.id = $(this).attr("id");
	var numeric_id = img.id.replace("img","");

	//get the file resolution (new) via jquery
	//needed for size the crop popup window
	img.width = $("[name=img\\["+numeric_id+"\\]\\[orig_width\\]]").attr("value");
	img.height = $("[name=img\\["+numeric_id+"\\]\\[orig_height\\]]").attr("value");

	img.filename = $("[name=img\\["+numeric_id+"\\]\\[filename\\]]").attr("value");

	//window size
	var wvp = new Object;
	wvp.height = $(window).height();
	wvp.width  = $(window).width();

	var height = 0.9 * wvp.height;

	if (img.width > img.height)
	{ //landscape
		var width  = img.width /img.height * height;
		//alert(width);
	}
	else
	{//portrait
		var width  = img.height / img.width * height;
		//alert(width);
	}
	//open popup window
	openwindow(img.filename, width, height, img.width, img.height, img.id);

});

function switchSrc(imgid)
{
	//attach some random stuff to refresh the image
	newsrc = $('#'+imgid).attr("src")+'?'+Math.random();

	//switch
	$('#'+imgid).attr("src", newsrc);
}
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

			<div id="errormessage" class="hidden error" align="center"></div>

			<div id="thumbwrap" align="center">

				<h3><?php echo $_t['selectvisible']; ?></h3>
				<small><?php echo $_t['visiblepicsexplain']; ?></small>

				<div id="zend-progressbar-container">
					<div id="zend-progressbar-done"></div>
				</div>

				<iframe src="<?php echo $__adminurl; ?>/lib/step2process.php"  width="100%" height="100" class="hidden"></iframe>

				<form action="<?php echo $__adminurl; ?>/finishupdate" method="post" name="newupdate" id="finishupdate">
					<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
					<input type="hidden" name="type" value="pictureset" />






					<div id="thumbnails"></div>

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