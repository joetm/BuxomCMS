<?php $outline = Outline::get_context(); ?><!-- rating-->
<script src='<?php echo $__siteurl; ?>/js/rating/jquery.MetaData.js' type="text/javascript"></script>
<script src="<?php echo $__siteurl; ?>/js/rating/jquery.rating.pack.js" type="text/javascript"></script>
<link type="text/css" href="<?php echo $__siteurl; ?>/js/rating/jquery.rating.css" rel="stylesheet" />

<script type="text/javascript"><!--
$(document).ready(function(){
	
	$('.star').rating('readOnly', true); //true or false
});
//-->
</script><?php Outline::finish(); ?>