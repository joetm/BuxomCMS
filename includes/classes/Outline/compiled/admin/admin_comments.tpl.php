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

	$('#ctoggle').toggle(
		function(){
			$('#datepickerwrap').show('fast');
			$('#cimg').attr('src','<?php echo $__siteurl; ?>/img/icons/cancel.png');
		},
		function() {
			$('#datepickerwrap').hide('fast');
			$('#cimg').attr('src','<?php echo $__siteurl; ?>/img/icons/calendar.png');
		}
	);

	$('#addnew').toggle(
		function(){
			$('.newcommentdiv').show('fast');
			$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_close.png');
		},
		function() {
           	$('.newcommentdiv').hide('fast');
           	$('#addicon').attr('src','<?php echo $__siteurl; ?>/img/icons/details_open.png');
		}
	);
	<?php if ($_page[errorcss] || $_GET[id]) { ?>
		$(".newcommentdiv").show('fast');
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
			"sDom": '<"top"<"right"f>><"bottom"rt<"deletebutton">lpi><"clear">',
			"aoColumns": [
						{"bSearchable": false ,"bSortable": false},	//icon
						null,										//id
						{"sClass": "tabledate"},					//date
						null,										//comment
						{"sClass": "center"},						//update
						null,										//status
						{"bSearchable": false},						//karma
						null,										//name
						null,										//ip
//						null,										//host
						{"bSearchable": false ,"bSortable": false},	//preview
						{"bSearchable": false ,"bSortable": false},	//edit
						{"bSearchable": false ,"bSortable": false}	//checkbox
			],
			"sAjaxSource": "<?php echo $__adminurl; ?>/lib/datatables.php",
			"fnServerData": function ( sSource, aoData, fnCallback ) {

						aoData.push(
							{"name":"table", "value":"comments"},
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
						$('#table tbody td:nth-child(3),#table tbody td:nth-child(4),#table tbody td:nth-child(7)').editable('<?php echo $__adminurl; ?>/lib/editable_ajax.php', {
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
											"type": "comments",
											"securitytoken": "<?php echo $securitytoken; ?>"
										};
							},
							"height": "14px",
							indicator: '<img src="<?php echo $__siteurl; ?>/img/loader.gif">'
						});

						//status column
						$('#table tbody td:nth-child(6)').editable('<?php echo $__adminurl; ?>/lib/editable_ajax.php', {
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
											"type": "comments",
											"securitytoken": "<?php echo $securitytoken; ?>"
										};
							},
						    submit: "OK",
						    data: "{'approved':'approved','spam':'spam','queued':'queued'}",
						    style: "inherit",
						    type: "select",
							"height": "14px",
							indicator: '<img src="<?php echo $__siteurl; ?>/img/loader.gif">'
						});

			} //fnDrawCallback
	});

	$("div.deletebutton").html('<div id="deletebutt" class="right deletebutt"><input type="submit" id="delete" name="delete" value="<?php echo $_t['delete']; ?>" /></div><div id="approvebutt" class="right approvebutt"><input type="submit" id="approve" name="approve" value="<?php echo $_t['bulkapprove']; ?>" /></div>');

});
//-->
</script>


	<a name="top"></a>
	<div id="admincontent">

				<div class="left">
					<div class="left">
						<a href="#" id="addnew" class="left mright5"><img id="addicon" src="<?php echo $__siteurl; ?>/img/icons/details_open.png" width="20" height="20" hspace="5" alt="" title="<?php echo $_t['addcomment']; ?>" border="0" /></a>
					</div>
					<div class="newcommentdiv left mleft5 mtop5 hidden">
						<label for="comment" class="<?php echo $_page[errorcss][comment]; ?>"><?php echo $_t['editaddcomment']; ?>:</label>
					</div>
				</div>

				<?php if ($_page[successmessage]) { ?>
				<!-- success -->
					<div id="success" class="left">
					<img src="<?php echo $__siteurl; ?>/img/icons/success.gif" width="16" height="16" hspace="5" alt="" border="0" />
						<?php echo $_page[successmessage]; ?>
					</div>
				<?php } ?>

				<div class="newcommentdiv hidden" style="width:730px;">

					<div class="c"></div>

					<form action="<?php echo $__adminurl; ?>/comments" method="post" name="editaddform">
					<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

						<!-- datepicker -->
						<div id="datepickerwrap" class="right hidden">



							<div id="datepicker"></div>

						</div>

					<textarea name="comment" id="comment" class="forminput" cols="64" rows="5"><?php echo $c['content']; ?></textarea>

					<div>

						<span class="<?php echo $_page[errorcss][name]; ?>">
						<label for="name"><?php echo $_t['commenter']; ?>:</label>
						<input name="name" type="text" class="forminput" id="name" size="12" value="<?php echo $c['name']; ?>" />
						</span>

						<span class="<?php echo $_page[errorcss][updateid]; ?>">
						<label for="update"><?php echo $_t['update_id']; ?>:</label>
						<input name="contentid" type="text" class="forminput" id="update" size="2" value="<?php echo $c['updateid']; ?>" />
						</span>

						<span class="<?php echo $_page[errorcss][date]; ?>">
						<label for="date"><?php echo $_t['date']; ?>:</label>
						<a href="#" id="ctoggle" class="mtop4" title="<?php echo $_t['pickfromcalendar']; ?>">
						<img src="<?php echo $__siteurl; ?>/img/icons/calendar.png" id="cimg" width="16" height="16" alt="" border="0" /></a>
						<input name="date" type="text" class="forminput" id="date" size="8" value="<?php echo $c['date']; ?>" />
						</span>

						<input type="hidden" name="commentid" value="<?php echo $c['id']; ?>" />

						<input type="submit" id="updatecomment" name="updatecomment" value="<?php if (@c.commentid) { echo $_t['update']; } else { ?>&nbsp;&nbsp;<?php echo $_t['add']; ?>&nbsp;&nbsp;<?php } ?>" />

					</div>

					</form>
				</div> <!-- /addcomment-->


				<?php if ($_page[errormessage]) { ?>
				<!-- error -->
				<div id="errors">
					<img src="<?php echo $__siteurl; ?>/img/icons/warning.png" width="16" height="16" hspace="5" alt="" border="0" />
					<strong><?php echo $_page[errormessage]; ?></strong><br />
				</div>
				<?php } ?>

			<div id="commentslist">

				<form action="<?php echo $__adminurl; ?>/comments" method="post" name="updform">
				<input type="hidden" name="securitytoken" value="<?php echo $securitytoken; ?>" />

					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="display f12" id="table">
					<thead>
					<tr>
						<th width="3%" nowrap="nowrap">&nbsp;</th>
						<th width="4%" align="left"><?php echo $_t['id']; ?></th>
						<th align="left"><?php echo $_t['date']; ?></th>
						<th width="28%" align="left"><?php echo $_t['comment']; ?></th>
						<th align="left"><?php echo $_t['update']; ?></th>
						<th align="left"><?php echo $_t['status']; ?></th>
						<th align="left"><?php echo $_t['karma']; ?></th>
						<th align="left"><?php echo $_t['name']; ?></th>
						<th width="5%" align="left"><?php echo $_t['ip']; ?></th>
						
						<th width="5%" align="left">&nbsp;</th>
						<th width="6%" align="left"><?php echo $_t['edit']; ?></th>
						<th width="2%"><input type="checkbox" name="checkboxall" id="checkboxall" /></th>
					</tr>
					</thead>
					<tbody>

					</tbody>
					</table>

				</form>

				<div class="c"></div>

			</div><!-- /commentslist-->

	</div><!-- admincontent-->

<?php $outline_include_1 = new Outline('_adminfooter'); require $outline_include_1->get(); Outline::finish(); ?>