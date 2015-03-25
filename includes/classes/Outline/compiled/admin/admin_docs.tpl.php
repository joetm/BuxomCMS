<?php $outline = Outline::get_context(); $outline_include_0 = new Outline('_adminheader'); require $outline_include_0->get(); ?>

<style>
.modelname{
	font-size:18px;
}
</style>

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

	$("#delete").live('click',function(){
		if(confirm("Note: Birthday and gender belong to the 2257 entry and will be deleted if you delete a 2257 entry.\n Continue anyway?"))
		{
//			$("#docsform").submit();
		}
		else
		{
			return false;
		}
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
					{"bSearchable": false ,"bSortable": false}, //icon
					null,										//models
					null,										//model notes
					{"bSearchable": false ,"bSortable": false}	//checkbox/editicon
				],
				"sAjaxSource": "<?php echo $__adminurl; ?>/lib/datatables.php",
				"fnServerData": function ( sSource, aoData, fnCallback ) {

					aoData.push(
						{"name":"table", "value":"2257docs"},
						{"name":"securitytoken", "value":"<?php echo $securitytoken; ?>"}
					);

					$.ajax({
						"dataType": 'json',
						"type": "POST",
						"url": sSource,
						"data": aoData,
						"success": fnCallback
					});
				}
	});






});
//-->
</script>

<div id="overDiv"></div>


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


		<div id="docs2257">

			<form action="<?php echo $__adminurl; ?>/docs" method="post" name="docsform" id="docsform">
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />
			<input type="hidden" name="_action" value="delete" />

				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="display f12" id="table" style="word-wrap:break-word;">
				<thead>
				<tr>
					<th width="2%">&nbsp;</th>
					<th align="left"><?php echo $_t['model']; ?></th>
					<th width="26%" align="left"><?php echo $_t['model']; ?> <?php echo $_t['notes']; ?></th>
					<th width="2%">
					</th>
				</tr>
				</thead>
				<tbody>

				</tbody>
				</table>

			</form>

			<div class="c"></div>

		</div><!-- docs2257-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>