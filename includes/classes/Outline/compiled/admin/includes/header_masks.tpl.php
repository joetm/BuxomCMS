<?php $outline = Outline::get_context(); ?><script src="<?php echo $__adminurl; ?>/js/jquery.maskedinput.js" type="text/javascript"></script>
<script type="text/javascript"><!--
jQuery(function($){
	$("#date").mask("9999-99-99",{placeholder:"_"});
	$("#birthdate").mask("9999-99-99",{placeholder:"_"});
	$("#productiondate").mask("9999-99-99",{placeholder:"_"});
	$("#duration").mask("99:99:99",{placeholder:"_"});
	<?php if ($_page[templatename] == 'admin_members') { ?>
		$("#IP").mask("999.999.999.999",{placeholder:"_"});
		$(".date").mask("9999-99-99",{placeholder:"_"});
	<?php } ?>
});
//-->
</script><?php Outline::finish(); ?>