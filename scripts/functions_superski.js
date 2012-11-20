
var scrollleft_actief=0;
var scrollright_actief=0;

//Get cookie routine by Shelley Powers
function get_cookie(Name) {
	var search = Name + "=";
	var returnvalue = "";
	if (document.cookie.length > 0) {
		offset = document.cookie.indexOf(search);
		// if cookie exists
		if (offset != -1) {
			offset += search.length;
			// set index of beginning of value
			end = document.cookie.indexOf(";", offset);
			// set index of end of cookie value
			if (end == -1) end = document.cookie.length;
			returnvalue=unescape(document.cookie.substring(offset, end));
		}
	}
	return returnvalue;
}

// hoofdpagina-carousel
var hoofdpagina_blok_teller=1;
var hoofdpagina_sla_1_ronde_over=0;
function hoofdpagina_carousel(teller) {
	if(teller===0 && hoofdpagina_sla_1_ronde_over==1) {
		hoofdpagina_sla_1_ronde_over=0;
	} else {
		$("#blok_"+hoofdpagina_blok_teller).fadeOut("slow");
		$(".hoofdpagina_blok_superskideal").fadeOut("slow");
		$("#hoofdpagina_blok_teller_"+hoofdpagina_blok_teller).removeClass("hoofdpagina_blok_teller_active");
		if(teller===0) {
			hoofdpagina_blok_teller=hoofdpagina_blok_teller+1;
			if($("#blok_"+hoofdpagina_blok_teller).length>0) {

			} else {
				hoofdpagina_blok_teller=1;
			}
			teller=hoofdpagina_blok_teller;
		} else {
			hoofdpagina_blok_teller=teller;
		}
		document.cookie="hoofdpagina_blok_teller="+hoofdpagina_blok_teller;
		$("#blok_"+teller).fadeIn("slow");
		$(".hoofdpagina_blok_superskideal").fadeIn("slow");
		$("#hoofdpagina_blok_teller_"+teller).addClass("hoofdpagina_blok_teller_active");
	}
}

$(document).ready(function() {

	// hoofdpagina-carousel starten
	if($("#hoofdpagina_blok").length>0) {
		var temp_teller=parseInt(get_cookie("hoofdpagina_blok_teller"));
		if(temp_teller>0 && $("#blok_"+temp_teller).length>0) {
			hoofdpagina_carousel(temp_teller);
		}
		if($("#blok_2").length>0) {
			var intervalID = setInterval(function(){
				hoofdpagina_carousel(0);
			},5000);
			$(".hoofdpagina_blok_teller").click(function() {
				clearInterval(intervalID);
				hoofdpagina_carousel(parseInt($(this).data("value")));
				hoofdpagina_sla_1_ronde_over=1;
				intervalID = setInterval(function(){
					hoofdpagina_carousel(0);
				},5000);
			});
		}

		// hoofdlink niet actief bij klikken op wissel-puntje
		$(".hoofdpagina_blok_content a").click(function(e) {
			e.preventDefault();
		});

		// hele blok klikbaar maken
		$(".hoofdpagina_blok_content").click(function() {
			document.location.href=$(this).find("a").attr("href");
		});
	}

});
