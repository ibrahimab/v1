
var slideteller=1;

function wissel_blok() {
	vorigeslideteller=slideteller;
	slideteller=slideteller+1;
	if(slideteller>4) slideteller=1;
	$(".fotoslide"+vorigeslideteller).fadeOut("fast",function(){
		$(".slide"+vorigeslideteller).hide();
		$(".fotoslide"+slideteller).fadeIn("fast",function(){
			$(".slide"+slideteller).show();
		});
	});
}

$(document).ready(function() {
	// fotoslide hoofdpagina
	if($(".fotoslide1").length!=0) {
		setInterval(wissel_blok,4000);
	}
	
	// zoekvormgeving: hovers	
	$(".zoekresultaat_type").hover(
		function() {
			var bordercolor=$("#hulp_zoekresultaat_hover").css("color");
			$(this).prev("a").css("border-bottom-color",bordercolor);
			$(this).css("border-left-color",bordercolor);
			$(this).css("border-right-color",bordercolor);
			$(this).css("border-bottom-color",bordercolor);
			$(this).css("color","#000000");
		},
		function() {
			$(this).prev("a").removeAttr("style");
			$(this).removeAttr("style");
		}
	);
	
	$(".zoekresultaat").hover(
		function() {
			$(this).find(".zoekresultaat_type_een_resultaat").css("color","#000000");
		},
		function() {
			$(this).find(".zoekresultaat_type_een_resultaat").removeAttr("style");
		}
	);
});
