<?php $outline = Outline::get_context(); ?><script type="text/javascript"><!--
$(document).ready(function(){
	$(".forminput").focus(function () {
		$(this).css('border','2px solid yellow');
    });
	$(".forminput").blur(function () {
		$(this).css('border','1px solid #e5e5e5');
    });
});
//-->
</script>

<!--[if lt IE 7]>
<style type="text/css">
/*IE png fix*/
img { behavior: url(<?php echo $__siteurl; ?>/js/iepngfix.htc) }
</style>
<![endif]-->

<!--[if IE]>
<style type="text/css">
.inactivethumb{
	filter:alpha(opacity=60);
}
</style>
<![endif]-->
<?php Outline::finish(); ?>