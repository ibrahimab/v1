
 // set constants
var $pageheight = 189; // our single page height
var $pagewidth = 146; // our single page width

var $pageYpos = 0; // current Y position of our bg-image (in both pages)

var folderteller=0;
var files=[];

function slide1In() {
	$("#slide1").fadeIn("fast",function(){
		setTimeout("slide1Out()",2000);
	});
}

function slide1Out() {
	$("#slide1").fadeOut("fast",function(){
		slide2In();
	});
}

function slide2In() {
	$("#slide2").fadeIn("fast",function(){
		setTimeout("slide2Out()",2000);
	});
}

function slide2Out() {
	$("#slide2").fadeOut("fast",function(){
		slide1In();
	});
}

$(document).ready(function(){ // When the page is ready

	/* left page turner */
	$("#leftpageA").click( function() {
		$pageYpos = $pageYpos + $pageheight; // update Y postion
		$("#leftpage").css("background-position", "0px "+$pageYpos+"px");// move the background position
		
		setTimeout ('$("#flip").css("background-position", "top center");', 200);
		setTimeout ('$("#rightpage").css("background-position", "146px "+$pageYpos+"px");', 200);
	
	}); // close leftpage click function
	
	/* right page turner */
	$("#rightpageA").click( function() {
		$pageYpos = $pageYpos - $pageheight; // note: minus page height
		$("#rightpage")
		.css("background-position", "0px "+$pageYpos+"px");
		
		$("#flip").css("background-position", "top left");
		setTimeout ('$("#flip").css("background-position", "top center");', 200);
		setTimeout ('$("#leftpage").css("background-position", "146px "+$pageYpos+"px");', 200);
	}); // close rightpage click function

	$("#nextbutton").click( function() {
	
		$("#nextbutton").attr('disabled','disabled');
		$("#nextbutton").css("cursor","default");

		folderteller=folderteller+2;

		$("#rightimg").attr("src","pic/folder_tn/"+files[folderteller+1]);
		$("#link26").attr("href","pic/folder/"+files[folderteller+1]);

		$("#link25").attr("href","pic/folder/"+files[folderteller]);
		setTimeout('$("#leftimg").attr("src","pic/folder_tn/"+files[folderteller]);',200);
		setTimeout('$("#nextbutton").removeAttr("disabled");$("#nextbutton").css("cursor","pointer");',500);
		
		if(folderteller>0) {
			$("#previous").css({"visibility":"visible"});
		}
		if(folderteller>=files.length-2) {
			$("#next").css({"visibility":"hidden"});
		}
		
		// zorgen dat fancybox-links kloppen
		for(var i=0;i<50;i++) {
			var waarde=folderteller+i-25;
			if(waarde>0&&waarde<25) {
				$("#link"+i).attr("href","pic/folder/"+files[folderteller+i-25]);
				$("#link"+i).attr("rel","group1");
			} else {
				$("#link"+i).attr("href","pic/folder/folder_page_001.jpg");
				$("#link"+i).attr("rel","group2");
			}
		}
	});

	$("#previousbutton").click( function() {
		$("#previousbutton").attr("disabled","disabled");
		$("#previousbutton").css("cursor","default");

		folderteller=folderteller-2;

		$("#link25").attr("href","pic/folder/"+files[folderteller]);
		$("#leftimg").attr("src","pic/folder_tn/"+files[folderteller]);
		
		$("#link26").attr("href","pic/folder/"+files[folderteller+1]);
		setTimeout('$("#rightimg").attr("src","pic/folder_tn/"+files[folderteller+1]);',200);
		setTimeout('$("#previousbutton").removeAttr("disabled");$("#previousbutton").css("cursor","pointer");',500);

		if(folderteller==0) {
			$("#previous").css({"visibility":"hidden"});
		}
		if(folderteller<files.length) {
			$("#next").css({"visibility":"visible"});
		}
		// zorgen dat fancybox-links kloppen
		for(var i=0;i<50;i++) {
			var waarde=folderteller+i-25;
			if(waarde>0&&waarde<25) {
				$("#link"+i).attr("href","pic/folder/"+files[folderteller+i-25]);
				$("#link"+i).attr("rel","group1");
			} else {
				$("#link"+i).attr("href","pic/folder/folder_page_001.jpg");
				$("#link"+i).attr("rel","group2");
			}
		}
	});
	
	for(var i=0;i<50;i++) {
		$("#link"+i).fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		: 300, 
			'speedOut'		: 300, 
			'padding' :		0,
			'margin' :		0,
			'autoScale' :		false,
			'overlayShow'	:	true,
			'hideOnContentClick' :	true,
			'overlayColor' :	'#454545',
			'onComplete'	:	function() {

			},
			'overlayOpacity' :	.8
		});
	}
	
	slide1In();
}); // close doc ready 

