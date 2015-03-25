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

function isEmpty(inputStr) { if ( null == inputStr || "" == inputStr || 0 == inputStr.length ) { return true; } return false; }

function EmailMember()
{
	var a = new Array();
	$('input:checkbox:checked').each(function(i) {
		if($(this).attr("id") != 'checkboxall')
		{
			a[i] = $(this).closest('tr').children('.emailaddress').html();
		}
		else a[i] = null;
	});

	if(!isEmpty(a))
	{
		var loc = 'mailto:';
		for(i=0; i<a.length; i++)
		{
			if(a[i]!=null)
			{
				loc += a[i];
				if (i == 0 && a.length > 1) loc += '?bcc=';
				if (i >= 1 && !(i==a.length - 1)) loc += '&amp;bcc=';
			}
		}

	/*
	loc = "mailto:test@web.de&bcc=sfsdg@rdh.de&bcc=john@doe.com&bcc=john@doe.com&bcc=john@doe.com&bcc=john@doe.com";
	*/

	if(confirm('Send email? '+loc))
		location.href = loc;

	//alert(loc);

	}

}
//-->
</script>

<script src="<?php echo $__adminurl; ?>/js/jquery.jeditable.mini.js" type="text/javascript"></script>
<script src="<?php echo $__adminurl; ?>/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript"><!--
var oTable;
$(document).ready(function(){

	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 1000);

	$("#checkboxall").click(function(){
		var checked_status = this.checked;
			$("input[name=checkbox\\[\\]]").each(function()
			{
				this.checked = checked_status;
			});
	});

	$('#addnew').toggle(
		function(){
			$('#newmember').show('fast');
			$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_close.png');
		},
		function() {
           	$('#newmember').hide('fast');
           	$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_open.png');
		}
	);
	<?php if ($_page[errorcss]) { ?>
		$("#newmember").show('fast');
		$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_close.png');
	<?php } ?>

	/* Init DataTables */
	oTable = $('#table').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"bPaginate": true,
					"bInfo": true,
					"bFilter": true,
					"bLengthChange": true,
					"bStateSave": false,
					"sPaginationType": "full_numbers",
					"bAutoWidth": false,
					"bSort": true,
					"aaSorting": [[0,'desc']],
					"sDom": '<"top"<"right"f>><"bottom"rt<"actions">lpi><"clear">',
					"aoColumns": [
						{"bSearchable": false},		//icon
						null,						//ID
						null,						//username
						{"sClass": "emailaddress"},	//email
						{"sClass": "tabledate"},	//join date
						{"sClass": "tabledate"},	//last_login
						{"sClass": "tabledate"},	//expire
						null,						//IP
						{"bSearchable": false ,"bSortable": false}
					],
					"sAjaxSource": "<?php echo $__adminurl; ?>/lib/datatables.php",
					"fnServerData": function ( sSource, aoData, fnCallback ) {

						aoData.push(
							{"name":"table", "value":"members"},
							{"name":"securitytoken", "value":"<?php echo $securitytoken; ?>"}
						);

						$.ajax({
							"dataType": 'json',
							"type": "POST",
							"url": sSource,
							"data": aoData,
							"success": fnCallback
						});
					},
					"fnDrawCallback": function () {
						//edit in place
						$('#table tbody td:nth-child(2),#table tbody td:nth-child(3),#table tbody td:nth-child(4)').editable('<?php echo $__adminurl; ?>/lib/editable_ajax.php', {
							"callback": function( sValue, y ) {
								var aPos = oTable.fnGetPosition( this );
								oTable.fnUpdate( sValue, aPos[0], aPos[1] );
							},
							"submitdata": function ( value, settings ) {
								var aPos = oTable.fnGetPosition( this );
								var aData = oTable.fnGetData( aPos[0] );
								return	{
											"column": aPos[1], //translate to columname with fnColumnToField function!
											"id": aData[1],    //id
											"type": "members",
											"securitytoken": "<?php echo $securitytoken; ?>"
										};
							},
							"height": "14px",
							indicator: '<img src="<?php echo $__siteurl; ?>/img/loader.gif">'
						});

//						$(".tabledate").mask("9999-99-99 99:99:99",{placeholder:"_"});
					}
/*
					,
					"fnDrawCallback":function(){
						if ( $('#table_paginate span span.paginate_button').size()) {
							$('#table_paginate')[0].style.display = "block";
						} else {
							$('#table_paginate')[0].style.display = "none";
						}
					}
*/
	});

	$("div.actions").html('<div class="right mtop5"><div id="expirebutt" class="right deletebutt"><input type="submit" id="expire" name="expire" value="<?php echo $_t['expire']; ?>" /></div><div id="activatebutt" class="right deletebutt"><input type="submit" id="activate" name="activate" value="<?php echo $_t['activate']; ?>" /></div><div id="deletebutt" class="right deletebutt"><input type="submit" id="delete" name="delete" value="<?php echo $_t['delete']; ?>" /></div><div id="emailbutt" class="right deletebutt"><input type="button" id="emailmember" onclick="javascript:EmailMember()" name="emailmember" value="<?php echo $_t['email']; ?>" /></div></div>');

});
//-->
</script>

	<a name="top"></a>
	<div id="admincontent">

				<div class="right mbot5">
					<form action="<?php echo $__adminurl; ?>/export" method="post">
					<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
						<div id="export" class="right"><input type="submit" name="export" value="<?php echo $_t['export']; ?>" /></div>
					</form>
				</div>

				<div class="right mbot5">
					<form action="<?php echo $__adminurl; ?>/email" method="post">
					<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

						<div id="emailbutt" class="right"><input type="submit" name="emailmembers" value="<?php echo $_t['emailmembers']; ?>" /></div>

						







					</form>
				</div>

				<div class="left mright5">
					<a href="#" class="mtop5" id="addnew"><img id="addicon" src="<?php echo $__siteurl; ?>/img/icons/details_open.png" style="position:relative;top:3px;" width="20" height="20" hspace="5" alt="" title="<?php echo $_t['addnewmember']; ?>" border="0" /></a>
					<?php if ($_page[errormessage]) { ?>
					<div id="errors">
						<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
						<strong><?php echo $_page[errormessage]; ?></strong><br />
					</div>
					<?php } else { ?>
						<?php if ($_page[successmessage]) { ?>
						<span id="success">
							<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
							<?php echo $_page[successmessage]; ?>
						</span>
						<?php } ?>
					<?php } ?>
				</div>

				<div class="c"></div>

				<div id="newmember" class="hidden">

					<form action="<?php echo $__adminurl; ?>/members" method="post" name="addnew">
					<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

						<div class="left">
							<div>
								<label for="username" class="<?php echo $_page[errorcss][username]; ?>"><?php echo $_t['membername']; ?>:</label><br />
								<input type="text" class="forminput" id="username" size="26" name="new_username" value="<?php echo outline__default($var[username], "John Doe"); ?>" onclick="ClearInput('John Doe', this.id);" />
							</div>
							<div class="mtop5">
								<label for="password" class="<?php echo $_page[errorcss][password]; ?>"><?php echo $_t['password']; ?>:</label><br />
								<input type="text" class="forminput" id="password" size="26" name="new_password" value="<?php echo outline__default($var[password], "password"); ?>" onclick="ClearInput('password', this.id);" />
							</div>
						</div>
						<div class="left">
							<div>
								<label for="email" class="<?php echo $_page[errorcss][email]; ?>"><?php echo $_t['email']; ?>:</label><br />
								<input type="text" class="forminput" id="email" size="26" name="new_email" value="<?php echo outline__default($var[email], 'john@doe.com'); ?>" onclick="ClearInput('john@doe.com', this.id);" />
							</div>
							<div class="mtop5">
								<label for="IP" class="<?php echo $_page[errorcss][IP]; ?>"><?php echo $_t['IP']; ?>:</label><br />
								<input type="text" class="forminput" id="IP" size="12" name="IP" value="<?php echo outline__default($var[IP], '000.000.000.000'); ?>" onclick="ClearInput('000.000.000.000', this.id);" />
							</div>
						</div>
						<div class="left">
							<div class="left">
								<label for="join_date" class="<?php echo $_page[errorcss][join_date]; ?>"><?php echo $_t['join_date']; ?>:</label><br />
								<input type="text" class="forminput date" id="join_date" size="8" name="join_date" value="<?php if ($var[join_date]) { echo $var[join_date]; } else { echo date(Config::Get('date_string')); } ?>" />
							</div>
							<div class="left">
									<label for="expire_time" class="<?php echo $_page[errorcss][expire_time]; ?>"><?php echo $_t['expiry']; ?>:</label><br />
									<input type="text" class="forminput" id="expire_time" size="3" name="expire_time" value="<?php if ($var[expire_time]) { echo $var[expire_date]; } else { ?>30<?php } ?>" /> <?php echo strtolower($_t['days']); ?>
							</div>

							<div class="c"></div>
							<div class="mtop5">
								<label class="<?php echo $_page[errorcss][no_emails]; ?>"><?php echo $_t['receive_emails']; ?>:</label><br />
									<input type="radio" value="0" name="no_emails"<?php if ($var[no_emails] == 0) { ?> checked="checked"<?php } ?> /> Yes
									<input type="radio" value="1" name="no_emails"<?php if ($var[no_emails] == 1) { ?> checked="checked"<?php } ?> /> No
							</div>
						</div>

						<div class="c"></div>
						<div class="mtop5">
							<input type="submit" id="addnewmember" name="addnewmember" value="<?php echo $_t['addnewmember']; ?>" />
						</div>
					</form>
				</div>


			<div id="memberlist">

				<form action="<?php echo $__adminurl; ?>/members" method="post" name="memberform">
				<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="display f12" id="table">
					<thead>
					<tr>
						<th width="3%">&nbsp;</th>
						<th width="4%" align="left"><?php echo $_t['ID']; ?></th>
						<th align="left" nowrap="nowrap"><?php echo $_t['membername']; ?></th>
						<th width="15%" align="left"><?php echo $_t['email']; ?></th>
						<th width="10%" align="left" nowrap="nowrap"><?php echo $_t['join_date']; ?></th>
						<th width="10%" align="left" nowrap="nowrap"><?php echo $_t['last_login']; ?></th>
						<th width="10%" align="left" nowrap="nowrap"><?php echo $_t['expire']; ?></th>
						<th width="14%" align="left"><?php echo $_t['IP']; ?></th>
						<th width="2%"><input type="checkbox" name="checkboxall" id="checkboxall" /></th>
					</tr>
					</thead>
					<tbody>

					</tbody>
					</table>

				</form>

				<div class="c"></div>

			</div><!-- /memberlist-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>