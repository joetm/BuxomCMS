<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script src="<?php echo $__adminurl; ?>/js/jquery.jeditable.mini.js" type="text/javascript"></script>
<script src="<?php echo $__adminurl; ?>/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript"><!--
var oTable;
$(document).ready(function(){

	 setTimeout(function(){ $('#success').fadeOut('slow'); }, 1500);














	$("#checkboxall").click(function(){
		var checked_status = this.checked;
			$("input[@name=checkbox\\[\\]]").each(function()
			{
				this.checked = checked_status;
			});
	});

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
			"sDom": '<"top"<"right"f>><"bottom"rt<"deletebutton">lpi><"clear">',
			"aoColumns": [
						{"bSearchable": false ,"bSortable": false},	//icon
//						{"bVisible": false},						//id
						null,										//rating
						null,										//username
						null,										//IP
						null,										//date
						null,										//content ID
						{"bSearchable": false},						//type
						{"bSearchable": false ,"bSortable": false},	//preview
						{"bSearchable": false ,"bSortable": false},	//edit
						{"bSearchable": false ,"bSortable": false}	//checkbox
			],
			"sAjaxSource": "<?php echo $__adminurl; ?>/lib/datatables.php",
			"fnServerData": function ( sSource, aoData, fnCallback ) {

						aoData.push(
							{"name":"table", "value":"ratings"},
							{"name":"securitytoken", "value":"<?php echo $securitytoken; ?>"}
						);

						$.ajax({
							"dataType": 'json',
							"type": 'POST',
							"url": sSource,
							"data": aoData,
							"success": fnCallback
						});
			},
			"fnDrawCallback": function () {
						//edit in place
						$('#table tbody td:nth-child(3)').editable('<?php echo $__adminurl; ?>/lib/editable_ajax.php', {
							"callback": function( sValue, y ) {
								var aPos = oTable.fnGetPosition( this );
								oTable.fnUpdate( sValue, aPos[0], aPos[1] );
							},
							"submitdata": function ( value, settings ) {
								var aPos = oTable.fnGetPosition( this );
								var aData = oTable.fnGetData( aPos[0] );
								return	{
											"column": aPos[1],
											"id": aData[1],
											"type": "ratings",
											"securitytoken": "<?php echo $securitytoken; ?>"
										};
							},
							"height": "14px",
							indicator: '<img src="<?php echo $__siteurl; ?>/img/loader.gif">'
						});
			} //fnDrawCallback
	});

	$("div.deletebutton").html('<div id="deletebutt" class="right deletebutt"><input type="submit" id="delete" name="delete" value="<?php echo $_t['delete']; ?>" /></div>');

});
//-->
</script>

<script type="text/javascript"><!--
function send( id )
{
	document.forms["updform"].elements["checkbox" + id + ""].checked = true;
	document.forms["updform"].submit();
	return false;
}
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

		<?php if ($_page[successmessage]) { ?>
		<!-- success -->
			<div id="success" class="left">
			<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[successmessage]; ?>
			</div>
		<?php } ?>

		<?php if ($_page[errormessage]) { ?>
		<!-- error -->
			<div id="errors">
				<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
				<strong><?php echo $_page[errormessage]; ?></strong><br />
			</div>
		<?php } ?>


		<div id="ratinglist">

			<form action="<?php echo $__adminurl; ?>/ratings" method="post" name="updform">
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="display f12" id="table">
				<thead>
					<tr>
						<th width="3%" nowrap="nowrap">&nbsp;</th>



						<th width="8%" align="left"><?php echo $_t['rating']; ?></th>
						<th align="left"><?php echo $_t['username']; ?></th>
						<th align="left"><?php echo $_t['ip']; ?></th>
						<th width="16%" align="left"><?php echo $_t['date']; ?></th>
						<th width="10%" align="left"><?php echo $_t['contentid']; ?></th>
						<th width="7%" align="left"><?php echo $_t['type']; ?></th>
						<th width="8%" align="left"><?php echo $_t['preview']; ?></th>
						<th width="6%" align="left"><?php echo $_t['edit']; ?></th>
						<th width="2%"><input type="checkbox" name="checkboxall" id="checkboxall" /></th>
					</tr>
				</thead>
				<tbody>

				</tbody>
				</table>

			</form>
			<div class="c"></div>

		</div><!-- /ratinglist-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>