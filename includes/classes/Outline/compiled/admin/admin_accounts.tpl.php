<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript"><!--
function ClearInput(value, id){
	var input = document.getElementById(id);
	if(value == input.value){
		input.value = '';
	}else{
		input.value = input.value;
	}
}

$(document).ready(function(){

	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 2000);

	$("#checkboxall").click(function(){
		var checked_status = this.checked;
			$("input[@name=checkbox\\[\\]]").each(function()
			{
				this.checked = checked_status;
			});
	});

	$('#addnew').toggle(
		function(){
			$('#newaccount').show('fast');
			$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_close.png');
		},
		function() {
           	$('#newaccount').hide('fast');
           	$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_open.png');
		}
	);

	<?php if ($errorcss) { ?>
			$('#newaccount').show('fast');
			$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_close.png');
	<?php } ?>
});
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

		<?php if ($_page[errormessage]) { ?>
			<div id="errors">
				<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
				<strong><?php echo $_page[errormessage]; ?></strong>
			</div>
		<?php } else { ?>
			<div class="left mright5 mbot10" style="height:18px">
				<a href="#" id="addnew"><img id="addicon" src="<?php echo $__siteurl; ?>/img/icons/details_open.png" width="20" height="20" hspace="5" alt="" title="<?php echo $_t['addnewaccount']; ?>" border="0" /></a>
			</div>
			<?php if ($_page[successmessage]) { ?>
				<div id="success" class="left">
				<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
					<?php echo $_page[successmessage]; ?>
				</div>
			<?php } ?>
		<?php } ?>

		<div class="c"></div>

		<div id="newaccount" class="hidden">

					<form action="<?php echo $__adminurl; ?>/accounts" method="post" name="addnewaccount">
					<input type="hidden" name="_action" value="addnew" />
					<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

						<div class="left">
							<div>
								<label for="username" class="<?php echo $errorcss['accountname']; ?>"><?php echo $_t['username']; ?></label><br />
								<input type="text" class="forminput" id="username" size="26" name="accountname" value="<?php echo $var[accountname]; ?>" />
							</div>
						</div>
						<div class="left">
							<div>
								<label for="accountpassword" class="<?php echo $errorcss['accountpassword']; ?>"><?php echo $_t['password']; ?></label><br />
								<input type="text" class="forminput" id="accountpassword" size="26" name="accountpassword" value="<?php echo $var[accountpassword]; ?>" />
							</div>
						</div>
						<div class="left">
							<div>
								<label for="email" class="<?php echo $errorcss['email']; ?>"><?php echo $_t['email']; ?></label><br />
								<input type="text" class="forminput" id="email" size="26" name="email" value="<?php echo outline__default($var[email], 'john@doe.com'); ?>" onclick="ClearInput('john@doe.com', this.id);" />
							</div>
						</div>
						<div class="left">
							<div>
								<label for="role" class="<?php echo $errorcss['role']; ?>"><?php echo $_t['role']; ?></label><br />
								<select class="forminput" name="role" id="role">
									<option>editor</option>
									<option>administrator</option>
								</select>
							</div>
						</div>
						<div class="left">
							<div>
								<label for="name" class="<?php echo $errorcss['name']; ?>"><?php echo $_t['name']; ?></label>
								<span class="f11">(<?php echo strtolower($_t['optional']); ?>)</span><br />
								<input type="text" class="forminput" id="name" size="26" name="name" value="<?php echo $var[name]; ?>" />
							</div>
						</div>
						<div class="c"></div>
						<div class="mtop5">
							<input type="submit" id="addnewmember" name="addnewmember" value="<?php echo $_t['addnewaccount']; ?>" />
						</div>
					</form>
			<div class="c"><br /></div>
		</div>


		<div id="accounts">

			<form action="<?php echo $__adminurl; ?>/accounts" method="post" name="login_history">
				<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
				<input type="hidden" name="_action" value="delete" />

				<table cellspacing="0" cellpadding="0" border="0" width="100%" id="table" class="display f12">
					<thead>
					<tr>
						<th align="left" width="18%" class="sorting_disabled"><?php echo $_t['username']; ?></th>
						<th align="left" width="18%" class="sorting_disabled"><?php echo $_t['role']; ?></th>
						<th align="left" width="25%" class="sorting_disabled"><?php echo $_t['email']; ?></th>
						<th align="left" class="sorting_disabled"><?php echo $_t['name']; ?> (<?php echo $_t['optional']; ?>)</th>
						<th width="2%" class="sorting_disabled"><input type="checkbox" id="checkboxall" name="checkboxall"></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($users as $key => $user) { ?>
						<tr class="<?php if (($key % 2) == 0) { ?>odd<?php } else { ?>even<?php } ?>">
							<td><?php echo $user['username']; ?></td>
							<td><?php echo $user['role']; ?></td>
							<td><?php echo $user['email']; ?></td>
							<td><?php echo outline__default($user['name'], ' '); ?></td>
							<td>
								<?php if ($key != '0') { ?>
									<input type="checkbox" value="<?php echo $user['username']; ?>" id="checkbox_<?php echo $key; ?>" name="checkbox[]">
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<div class="deletebutton"><div class="right deletebutt" id="deletebutt"><input type="submit" value="Delete" name="delete" id="deletebutt"></div></div>
			</form>

			<div class="c"></div>

		</div><!-- adminaccounts-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>