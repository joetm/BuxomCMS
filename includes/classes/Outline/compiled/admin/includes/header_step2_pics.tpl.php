<?php $outline = Outline::get_context(); ?><style type="text/css">
.container{
/*
	width: <?php echo $_thumbsizes[width]; ?>px;
	height:<?php echo $_thumbsizes[height]; ?>px;
*/
	border:1px solid #666666;
	float:left;
	padding:5px;
}
.container img{
    opacity:0.5;
}
#long-running-process {
	position: absolute;
	left: -200px;
	top: -500px;
	width: 1px;
	height: 1px;
}
#zend-progressbar-container {
    width: 100%;
    height: 30px;
	text-align:left;
    border: 1px solid #2f2f2f;
    background-color: #ffffff;
    margin:10px 0 20px 0;
}
#zend-progressbar-done {
    width: 0px;
    height: 30px;
    background-color: #FF3430;
}
#finish-button{
	padding:10px 0px 0px 0px;
	display:none;
}
#thumbwrap{
/*	width:<?php echo $_thumbsizes[width]*8+2*8+(2*8*5); ?>px; */
	margin:auto;
}
</style>

<!--[if IE]>
<style type="text/css">
.container img{
    filter:alpha(opacity=0.5);
}
</style>
<![endif]-->

<script type="text/javascript"><!--

$(document).ready(function(){
	var contwidth = $("#admincontent").width();
	var n = Math.floor((contwidth-30) / <?php echo $_thumbsizes[width]; ?>);
	$("#thumbwrap").css("width", function(index) {
		var w = <?php echo $_thumbsizes[width]; ?>*n + 2*n + 2*n*5;
		return w;
	});
});
//-->
</script>
<?php Outline::finish(); ?>