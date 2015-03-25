<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript"><!--
function ToggleBox()
{
	$("#mail-process").toggle('fast');
	$("#body").toggle('fast');

	$(".submitbutton").each (function () {
		var stat = $(this).attr("disabled");
		if (false == stat) {
			$(this).attr("disabled" , "disabled");
		}
		else {
			$(this).removeAttr("disabled");
		}
	});
}

function Unwind($res)
{
	if ($res == 'success')
	{
		//successfully finished
		ToggleBox();
		//show success
		$("#success").toggle("slow");
	}
	else
	{
		//error
		ToggleBox();
		//show error
		$("#error").toggle("slow");
	}
}

$(document).ready(function(){

	//page refresh fix
	$("#mail-process").attr("src", "<?php echo $__adminurl; ?>/lib/mailprocess.php?do=init");

	$("#submitall").click(function(){

		ToggleBox();

		//get the variables
		var body = $("#emailbody").val();
		var type = 'text';
		var members = 'all';
		var subject = $("#emailsubject").val();;

		// write data to temp database
		$.ajax({
			url: '<?php echo $__adminurl; ?>/lib/email.php',
			async: false,
			dataType: 'text',
			type: 'POST',
			data: ({"subject": subject,
				"body": body,
				"type": type,
				"members": members,
				"securitytoken": '<?php echo $securitytoken; ?>' }),
			success: function(data) {

				alert(data);

				if(data == 'success')
				{
					//start mail process
					$("#mail-process").attr("src", "<?php echo $__adminurl; ?>/lib/mailprocess.php?do=create_queue&startat=0&pp=<?php echo $pp; ?>");
				}
				else
				{
					//could not start mailqueue
					//show error message and reverse toggle

					Unwind('error');





				}
			}
		});


	});
});
//-->
</script>

	<a name="top"></a>
	<div id="admincontent">

			<div class="hidden" id="success"><?php echo $_t['success']; ?>!<br /><small><?php echo $_t['mailprocess_finished']; ?>.</small></div>

			<div class="hidden" id="error"><?php echo $_t['error']; ?>.</div>


			<form action="<?php echo $__adminurl; ?>/email" method="post" name="emailmembers">
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />





				<div class="right mleft5 mright5">
					<div class="right mtop5">
						<label><?php echo $_t['type']; ?>:</label>
					    <input type="radio" name="type" value="html"<?php if ($var[type] == 'html') { ?> checked="checked"<?php } ?> /> html
					    <input type="radio" name="type" value="text"<?php if ($var[type] == 'text') { ?> checked="checked"<?php } ?> /> text
					</div>
					<div class="right">
						<label for="members_selector"><?php echo $_t['members']; ?>:</label>
						<select name="members" id="members_selector" class="forminput">
							<option<?php if ($var[members]=='all') { ?> selected="selected"<?php } ?> value="all">All</option>
							<option<?php if ($var[members]=='active') { ?> selected="selected"<?php } ?> value="active">Active</option>
							<option<?php if ($var[members]=='inactive') { ?> selected="selected"<?php } ?> value="inactive">Inactive</option>
							<option<?php if ($var[members]=='chargeback') { ?> selected="selected"<?php } ?> value="chargeback">Chargeback</option>
						</select>
					</div>
				</div>

					<div id="subject"><label for="emailsubject"><?php echo $_t['subject']; ?>:</label>
					    <input type="text" class="forminput" style="width:400px;margin-bottom:5px;" id="emailsubject" name="emailsubject" value="<?php if ($var[subject]) { echo $var[subject]; } else { echo $__sitename; } ?>" />
					</div>

					<div id="body">
						<textarea rows="22" cols="90" class="forminput" style="width:99%" id="emailbody" name="emailbody"><?php echo $var[content]; ?></textarea>
					</div>

					<iframe src="<?php echo $__adminurl; ?>/lib/mailprocess.php?do=init" id="mail-process" width="99%" height="200" class="hidden"></iframe>

					<div class="mtop10">
						<input type="button" id="submitall" class="submitbutton" name="send" value="<?php echo $_t['send']; ?>" />

						<input type="button" id="testemail" class="submitbutton" name="testemail" value="<?php echo $_t['testemail']; ?>" />
					</div>

			</form>

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>