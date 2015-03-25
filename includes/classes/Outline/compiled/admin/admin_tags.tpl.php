<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script type="text/javascript"><!--
function send( id )
{
	document.forms["updform"].elements["checkbox" + id + ""].checked = true;
	document.forms["updform"].submit();
	return false;
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
					"sPaginationType": "full_numbers",
					"bInfo": true,
					"bFilter": true,
					"bLengthChange": true,
					"bStateSave": false,
					"bAutoWidth": false,
					"bSort": true,
					"aaSorting": [[0,'desc']],
					"sDom": '<"top"<"right"f>><"bottom"rt<"deletebutton">lpi><"clear">',
					"aoColumns": [
						{"bSearchable": false ,"bSortable": false},
						null,
						null,
						null,
						{"bSearchable": false ,"bSortable": false}
					],
					"sAjaxSource": "<?php echo $__adminurl; ?>/lib/datatables.php",
					"fnServerData": function ( sSource, aoData, fnCallback ) {

						aoData.push(
							{"name":"table", "value":"tags"},
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
						$('#table tbody td:nth-child(3), #table tbody td:nth-child(4)').editable('<?php echo $__adminurl; ?>/lib/editable_ajax.php', {
							"callback": function( sValue, y ) {
								var aPos = oTable.fnGetPosition( this );
								oTable.fnUpdate( sValue, aPos[0], aPos[1] );
							},
							"submitdata": function ( value, settings ) {
								var aPos = oTable.fnGetPosition( this );
								var aData = oTable.fnGetData( aPos[0] );
								return	{
											"column": aPos[1],
											"id": aData[1],    //id
											"type": "tags",
											"securitytoken": "<?php echo $securitytoken; ?>"
										};
							},
							"height": "14px",
							"indicator": '<img src="<?php echo $__siteurl; ?>/img/loader.gif">'
						});

					}
/*
					,
					"fnDrawCallback":function(){
						//remove pagination
						if (!$('#table_paginate span span.paginate_button').size()) {
							$('#table_paginate')[0].style.display = "block";
						} else {
							$('#table_paginate')[0].style.display = "none";
						}
					}
*/
			});

			$("div.deletebutton").html('<input class="deletebutt" type="submit" id="delete" name="delete" value="<?php echo $_t['delete']; ?>" />');
});
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

			<?php if ($_page[errormessage]) { ?>
				<div id="errors" class="left">
					<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
					<strong><?php echo $_page[errormessage]; ?></strong><br />
				</div>
			<?php } ?>

			<?php if ($_page[successmessage]) { ?>
				<span id="success" class="left">
					<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
					<?php echo $_page[successmessage]; ?>
				</span>
			<?php } ?>


			<div id="tagslist">

				<form action="<?php echo $__adminurl; ?>/tags<?php if ($update) { ?>?update=<?php echo $update; } ?>" method="post" name="updform">
				<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="display f12" id="table">
					<thead>
					<tr>
						<th width="3%">&nbsp;</th>
						<th width="4%" align="left"><?php echo $_t['id']; ?></th>
						<th width="25%" align="left"><?php echo $_t['tag']; ?></th>
						<th align="left"><?php echo $_t['description']; ?></th>
						<th width="2%"><input type="checkbox" name="checkboxall" id="checkboxall" /></th>
					</tr>
					</thead>
					<tbody>

					</tbody>
					</table>

				</form>

				<div class="c"></div>

			</div><!-- /tagslist-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>