<?php $outline = Outline::get_context(); ?><!-- includes for newmodel and newupdate -->
<script type="text/javascript" src="<?php echo $__adminurl; ?>/js/ajaxupload.js"></script>
<!-- THIS CSS MAKES THE IFRAME NOT JUMP -->
<style type="text/css">
iframe {
	display:none;
}
</style><?php Outline::finish(); ?>