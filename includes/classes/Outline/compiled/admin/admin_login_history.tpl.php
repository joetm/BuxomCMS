<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<script src="<?php echo $__adminurl; ?>/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript"><!--
var oTable;
$(document).ready(function(){

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
						{"bSearchable": false},
						null,
						null,
						null,
						null,
						null
			],
			"sAjaxSource": "<?php echo $__adminurl; ?>/lib/datatables.php",
			"fnServerData": function ( sSource, aoData, fnCallback ) {

						aoData.push(
							{"name":"table", "value":"login_history"},
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
	});

	$("div.deletebutton").html('<div id="deletebutt" class="right deletebutt"><input type="submit" id="delete" name="delete" value="<?php echo $_t['clearhistory']; ?>" /></div>');

});
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

		<div id="ratinglist">

			<form action="<?php echo $__adminurl; ?>/login_history" method="post" name="login_history">
				<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
				<input type="hidden" name="_action" value="delete" />

					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="display f12" id="table">
					<thead>
					<tr>
						<th width="3%" nowrap="nowrap">&nbsp;</th>
						<th width="20%"><?php echo $_t['username']; ?></th>
						<th width="20%"><?php echo $_t['ip']; ?></th>
						<th width="20%"><?php echo $_t['host']; ?></th>
						<th width="20%"><?php echo $_t['status']; ?></th>
						<th width="30%"><?php echo $_t['lastlogin']; ?></th>
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