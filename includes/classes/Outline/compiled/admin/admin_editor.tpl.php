<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript"><!--
$(document).ready(function() {
	$('#templateselector').change(function(){
			alert('Editing '+$(this).val());
			$("#editor_form").submit();
	});





	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 1500);
});
//-->
</script>

<script type="text/javascript" src="/js/jquery.textarearesizer.compressed.js"></script>
<style type="text/css">
div.grippie {
background:#EEEEEE url(<?php echo $__siteurl; ?>/img/grip.png) no-repeat scroll center 2px;
border-color:#DDDDDD;
border-style:solid;
border-width:0pt 1px 1px;
cursor:s-resize;
height:9px;
overflow:hidden;
}
.resizable-textarea textarea {
display:block;
margin-bottom:0pt;
width:95%;
height: 20%;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$(".resizable:not(.processed)").TextAreaResizer();
});
</script>


	<a name="top"></a>
	<div id="admincontent">

		<?php if ($_page[errormessage]) { ?>
			<div id="errors">
				<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
				<strong><?php echo $_page[errormessage]; ?></strong>
			</div>
		<?php } ?>
		<?php if ($_page[successmessage]) { ?>
			<div id="success" class="left">
			<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[successmessage]; ?>
			</div>
		<?php } ?>

		<div class="c"></div>

		<div id="templates">

			<form action="<?php echo $__adminurl; ?>/editor" id="editor_form" method="POST">
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

				<div class="left mbot10 mtop5">
				<label for="templateselector"><?php echo $_t['select_template']; ?>:</label>
				<select name="template" id="templateselector">
					<?php foreach ($templates as $t) { ?>
						<option value="<?php echo $t; ?>"<?php if ($t==$template) { ?> selected="selected"<?php } ?>><?php echo $t; ?></option>
					<?php } ?>
				</select>
				<noscript>
					<input type="submit" value="<?php echo $_t['select']; ?>" />
				</noscript>
				</div>

				<div class="right gray mbot10 mtop10">editing: <?php echo $template; ?></div>

				<div class="c"></div>

				<?php if ($templatecontent) { ?>
				<div>
					<textarea rows="28" class="resizable" cols="108" style="background-color:#F0F0F0;width:100%" id="editor" name="templatecontent"><?php echo $templatecontent; ?></textarea>
				</div>
				<?php } ?>

				<?php if ($templatecontent) { ?>
				<div align="center" class="mtop10">
					<input type="submit" value="<?php echo $_t['save']; ?>" class="submitbutton">
				</div>
				<?php } ?>

			</form>

			<div class="c"></div>

		</div><!-- templates-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>