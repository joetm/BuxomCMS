<?php $outline = Outline::get_context(); ?><!-- datepicker-->
<link  href="<?php echo $__siteurl; ?>/js/jquery-ui/themes/base/ui.all.css" type="text/css" rel="stylesheet" />
<script src="<?php echo $__siteurl; ?>/js/jquery-ui/ui/ui.core.js" type="text/javascript"></script>
<script src="<?php echo $__siteurl; ?>/js/jquery-ui/ui/ui.datepicker.js" type="text/javascript"></script>
<script type="text/javascript"><!--
$(document).ready(function(){
	$("#datepicker").datepicker({ defaultDate: <?php if ($output[date]) { ?>$.datepicker.parseDate("y-m-d", '<?php echo $output['date']; ?>')<?php } else { ?>null<?php } ?>, altField: '#date', altFormat: 'yy-mm-dd', dateFormat: 'yy-mm-dd' });
});

$(window).unload(function() {
	$('#datepicker').datepicker('destroy');
});
//-->
</script><?php Outline::finish(); ?>