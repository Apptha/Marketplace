var userDevices = (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ? 'mobile' : 'desktop';
var $j=jQuery.noConflict();
$j(document).ready(function(){
	var idvalue;
	
	
$j(".blue_clr").click(function(){
	$j("body").addClass("blue_theme");
	$j("body").removeClass("red_theme");
	$j("body").removeClass("green_theme");
	$j("body").removeClass("violet_theme");
	$j("body").removeClass("blk_theme");
	 idvalue="blue_theme";
	localStorage.setItem('currenttheme', idvalue);
    });
$j(".red_clr").click(function(){
	$j("body").addClass("red_theme");
	$j("body").removeClass("blue_theme");
	$j("body").removeClass("green_theme");
	$j("body").removeClass("violet_theme");
	$j("body").removeClass("blk_theme");
	idvalue="red_theme";
	localStorage.setItem('currenttheme', idvalue);
	});
$j(".green_clr").click(function(){
	$j("body").addClass("green_theme");
	$j("body").removeClass("blue_theme");
	$j("body").removeClass("red_theme");
	$j("body").removeClass("violet_theme");
	$j("body").removeClass("blk_theme");
	idvalue="green_theme";
	localStorage.setItem('currenttheme', idvalue);
	
    });
$j(".blk_clr").click(function(){
	$j("body").addClass("blk_theme");
	$j("body").removeClass("blue_theme");
	$j("body").removeClass("red_theme");
	$j("body").removeClass("violet_theme");
	$j("body").removeClass("green_theme");
	idvalue="blk_theme";
	localStorage.setItem('currenttheme', idvalue);
	
    });
$j(".violet_clr").click(function(){
	$j("body").addClass("violet_theme");
$j("body").removeClass("blue_theme");
$j("body").removeClass("red_theme");
$j("body").removeClass("green_theme");
$j("body").removeClass("blk_theme");
idvalue="violet_theme";
localStorage.setItem('currenttheme', idvalue);

});

$j("#cpToggle").click(function(){
	$j(".color_theme ul").slideToggle();
	
});
});