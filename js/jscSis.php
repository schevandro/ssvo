<!-- ARQUIVOS -->
<script type="text/javascript" src="<?php setHome();?>/js/jquery.js" /></script>
<script type="text/javascript" src="<?php setHome();?>/js/cycle.js" /></script>
<script type="text/javascript" src="<?php setHome();?>/js/shadowbox/shadowbox.js"></script>
<script type="text/javascript" src="<?php setHome();?>/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	

<!-- ESTILOS -->
<link rel="stylesheet" type="text/css" href="<?php setHome();?>/js/shadowbox/shadowbox.css"/>
<link rel="stylesheet" type="text/css" href="<?php setHome();?>/js/fancybox/jquery.fancybox-1.3.4.css"/>

<!-- FUNÇÕES -->
<script type="text/javascript">

Shadowbox.init();

$(document).ready(function() {
	$("a.openmodal").fancybox({
		'overlayShow'	: false,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic',
		'titlePosition'	: 'over'
	});
});

$(document).ready(function() {
	$("a[rel=openmodal]").fancybox({
		'overlayShow'	: false,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic',
		'titlePosition'	: 'over',
		'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
			return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
		}
	});
});

$('.small img').click(function() {
	$('.gbimgimg').attr('src', $(this).attr('src'));
	$('.gblink').attr('href', $(this).attr('src'));
});

$(function(){
   $('.slide ul').cycle({
     fx: 'fade',
     speed: 1000,
     timeout: 3000,
	 pager: '.slidenav'
   })
});
 
</script>