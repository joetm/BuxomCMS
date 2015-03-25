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
					{"bSearchable": false ,"bSortable": false}, //icon
					{"bSearchable": false ,"bSortable": false, "bVisible": false}, //id
					null,										//modelname
					null,										//slug
					{"bSearchable": false}, 					//rating
					{"bSearchable": false},						//birthdate
					{"bSearchable": false ,"bSortable": false}, //link
					{"bSearchable": false ,"bSortable": false}, //edit
					{"bSearchable": false ,"bSortable": false}, //preview
					{"bSearchable": false ,"bSortable": false} 	//checkbox
				],
				"sAjaxSource": "<?php echo $__adminurl; ?>/lib/datatables.php",
				"fnServerData": function ( sSource, aoData, fnCallback ) {

					aoData.push(
						{"name":"table", "value":"models"},
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
					$('#table tbody td:nth-child(2),#table tbody td:nth-child(3)').editable('<?php echo $__adminurl; ?>/lib/editable_ajax.php', {
						"callback": function( sValue, y ) {
							var aPos = oTable.fnGetPosition( this ) + 1; 
							oTable.fnUpdate( aPos[1], aPos[0], aPos[1] );
						},
						"submitdata": function ( value, settings ) {
							var aPos = oTable.fnGetPosition( this );
							var aData = oTable.fnGetData( aPos[0] );
							return	{
									"column": aPos[1],
									"id": aData[1], 	//hidden id column
									"type": "models",
									"securitytoken": "<?php echo $securitytoken; ?>"
									};
						},
						"height": "14px",
						indicator: '<img src="<?php echo $__siteurl; ?>/img/loader.gif">'
					});

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

	$("div.deletebutton").html('<div id="deletebutt" class="right deletebutt"><input type="submit" id="delete" name="delete" value="<?php echo $_t['delete']; ?>" /></div>');

});
//-->
</script>

	<a name="top"></a>
	<div id="admincontent">

		<?php if ($_page[successmessage]) { ?>
			<div id="success">
			<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
				<?php echo $_page[successmessage]; ?>
				<?php echo $updatelink; ?>
			</div>
		<?php } ?>

		<?php if ($_page[errormessage]) { ?>
			<div id="errors">
				<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
				<strong><?php echo $_page[errormessage]; ?></strong>
			</div>
		<?php } else { ?>
			<div class="left mright5">
				<a href="<?php echo $__adminurl; ?>/model" id="addnew"><img id="addicon" src="<?php echo $__siteurl; ?>/img/icons/details_open.png" width="20" height="20" hspace="5" alt="" title="<?php echo $_t['addnewmodel']; ?>" border="0" /></a>
			</div>
		<?php } ?>

		<div id="modelslist">

			<form action="<?php echo $__adminurl; ?>/models" method="post" name="updform">
			<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="display f12" id="table">
				<thead>
				<tr>
					<th width="3%">&nbsp;</th>
					<th><?php echo $_t['ID']; ?></th>
					<th width="30%" nowrap="nowrap" align="left"><?php echo $_t['modelname']; ?></th>
					<th width="20%" align="left" nowrap="nowrap"><?php echo $_t['slug']; ?></th>
					<th width="7%" align="left"><?php echo $_t['rating']; ?></th>
					<th width="10%" align="left" nowrap="nowrap"><?php echo $_t['birthdate']; ?></th>
					<th width="6%" align="left" nowrap="nowrap"><?php echo $_t['link']; ?></th>
					<th width="7%" align="left" nowrap="nowrap"><?php echo $_t['edit']; ?></th>
					<th width="8%" align="left" nowrap="nowrap"><?php echo $_t['preview']; ?></th>
					<th width="2%"><input type="checkbox" name="checkboxall" id="checkboxall" /></th>
				</tr>
				</thead>
				<tbody>

				</tbody>
				</table>

			</form>

			<div class="c"></div>

		</div><!-- /modelslist-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>