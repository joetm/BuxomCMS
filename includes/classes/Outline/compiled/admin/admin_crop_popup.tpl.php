<?php $outline = Outline::get_context(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Crop</title>

<style type="text/css">
body{
	margin:5px;padding:0px;
	text-align:center;
}
#loading_anim{
	display:none;
}
</style>

<script type="text/javascript" src="<?php echo $__siteurl; ?>/js/jquery-1.4.2.min.js"></script>

<script src="<?php echo $__adminurl; ?>/js/jquery.Jcrop.min.js" type="text/javascript"></script>
<link href="<?php echo $__adminurl; ?>/js/jquery.Jcrop.css" rel="stylesheet" type="text/css" />

<script type="text/javascript"><!--
function submit()
{
	//ajax submit

	//show loading img
	$('#loading_anim').show();
	$('#submitbutton').attr("disabled" , "disabled");

	//get crop values
	var ix = $('#x').val();
	var iy = $('#y').val();
	var iw = $('#w').val();
	var ih = $('#h').val();

	var filename = $('#filename').val();
	var internal_slug = $('#internal_slug').val();

	//crop file
	$.ajax({
		type: 'POST',
		async: false,
		url: "<?php echo $__adminurl; ?>/lib/crop.php",
		data: ({"x": ix,
				"y": iy,
				"w": iw,
				"h": ih,
				"internal_slug": internal_slug,
				"filename": '<?php echo $filename; ?>',
				"securitytoken": '<?php echo $securitytoken; ?>' }),
		dataType: "text",
		global: false,
		error: function(req,text){
			$('#loading_anim').hide();
			$('#submitbutton').removeAttr("disabled");
			$('#error').html(text);
			$('#error').show('fast');
		},
		success: function(ret){

			if(ret == "success")
			{
				$('#loading_anim').hide();
				$('#submitbutton').removeAttr("disabled");

				//switch img src in parent window
				switch_src();

				window.close(); //close crop window
			}
			else
			{
				$('#loading_anim').hide();
				$('#submitbutton').removeAttr("disabled");
				$('#error').html(ret);
				$('#error').show('fast');
			}

		} //success function
	}); //ajax

}

function switch_src()
{
	//switch src of parent window thumbnail by reloading
	window.opener.switchSrc('<?php echo $img_id; ?>')
}

function init()
{

	//close with ESC key
	window.onkeyup = function (event) {
		if (event.keyCode == 27) {
			window.close (); //self
		}
	}
	//submit form with ENTER key
	window.onkeyup = function (event) {
		if (event.keyCode == 13) {
			submit();
		}
	}

	var theimg = new Object;
	var thumbimg = new Object;

	var wvp = new Object;
	wvp.height = $(window).height();
	wvp.width  = $(window).width();

	theimg.width = <?php echo $img_width; ?>;
	theimg.height = <?php echo $img_height; ?>;

	if (theimg.height > theimg.width)
	{ //portrait

		thumbimg.height = (wvp.height - 65);
		thumbimg.width  = (thumbimg.height * <?php echo $img_whratio; ?>).toFixed(0);
	}
	else
	{ //landscape

		thumbimg.height = (wvp.height - 100);
		thumbimg.width  = (thumbimg.height * <?php echo $img_whratio; ?>).toFixed(0);
	}

	$("#cropbox").attr("height", thumbimg.height);
	$("#cropbox").attr("width",  thumbimg.width);


//alert("Img.width:"+theimg.width+"\nImg.height"+theimg.height+"\nthumbimg.width"+thumbimg.width+"\nthumbimg.height"+thumbimg.height+"\naspectratio"+<?php echo $aspectratio; ?>+"\nimg_whratio"+<?php echo $img_whratio; ?>);

	//initialize the cropper
	jQuery(function(){
		$('#cropbox').Jcrop({
			aspectRatio: <?php echo $aspectratio; ?>,
			onSelect: updateCoords
		});
	});

} //init
//-->
</script>

<script type="text/javascript">
<!--
	function updateCoords(c)
	{

		w = $('#cropbox').attr("width");
		h = $('#cropbox').attr("height");

		ix = c.x * (<?php echo $img_width; ?> / w);
		ix = ix.toFixed(0);
		iy = c.y * (<?php echo $img_height; ?> / h);
		iy = iy.toFixed(0);
		iw = c.w * (<?php echo $img_width; ?> / w);
		iw = iw.toFixed(0);
		ih = c.h * (<?php echo $img_height; ?> / h);
		ih = ih.toFixed(0);

		$('#x').val(ix);
		$('#y').val(iy);
		$('#w').val(iw);
		$('#h').val(ih);
	};

	function checkCoords()
	{
		if (parseInt($('#w').val())) return true;
		alert('Please select a crop region then press submit.');
		return false;
	};
//-->
</script>
</head>
<body onload="init()">


<div id="error" class="hidden"></div>

<div id="cropper">
	<div align="center">
		<img src="<?php echo $tempurl; ?>" border="0" alt="" id="cropbox" />
			<form action="javascript:submit();" onsubmit="return checkCoords();">
				<input type="hidden" id="x" name="x" />
				<input type="hidden" id="y" name="y" />
				<input type="hidden" id="w" name="w" />
				<input type="hidden" id="h" name="h" />
				<input type="hidden" id="internal_slug" name="internal_slug" value="<?php echo $internal_slug; ?>" />
				<input type="submit" id="submitbutton" value="Crop Image" />
				<img id="loading_anim" src="<?php echo $__adminurl; ?>/js/indicator.gif" width="16" height="16" alt="cropping..." />
			</form>
	</div>
</div> <!-- /cropper-->

</body>
</html>
<?php Outline::finish(); ?>