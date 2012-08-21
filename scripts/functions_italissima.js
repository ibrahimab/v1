
function onloadfunction() {
	// kijken welke tarieventabellen te breed zijn
	if(document.getElementById('tarieventabel_table[1]')) {
		var aantaltarieventabellen=document.forms['frm_aantaltarieventabellen'].elements['aantaltarieventabellen'].value;
		if(aantaltarieventabellen>0) {
			for(var i=1;i<=aantaltarieventabellen;i++) {
				var tarieventabel = document.getElementById('tarieventabel_table['+i+']');
				var tarieventabel_width = tarieventabel.offsetWidth;
//				if(tarieventabel_width>690) {
//					document.getElementById('tarieventabel_div['+i+']').style.width='640px';
//				} else {
//					document.getElementById('tarieventabel_links['+i+']').style.display='none';
//					document.getElementById('tarieventabel_rechts['+i+']').style.display='none';
//				}
			}
		}
	}
}

var scrollleft_actief=0;
var scrollright_actief=0;

function scrollleft(i) {
	if(scrollleft_actief==1) {
		var tarieventabel = document.getElementById('tarieventabel_div['+i+']');
		scrollLeftwaarde=tarieventabel.scrollLeft-4;
		tarieventabel.scrollLeft=scrollLeftwaarde;
		var t=setTimeout("scrollleft("+i+")",1);
	}
	return false;
}

function scrollright(i) {
	if(scrollright_actief==1) {
		var tarieventabel = document.getElementById('tarieventabel_div['+i+']');
		scrollLeftwaarde=tarieventabel.scrollLeft+4;
		tarieventabel.scrollLeft=scrollLeftwaarde;
		var t=setTimeout("scrollright("+i+")",1);
	}
	return false;
}

function scrollright2(i) {
	if(scrollleft_actief==1) {
		var tarieventabel = document.getElementById('tarieventabel_div['+i+']');
		scrollLeftwaarde=tarieventabel.scrollLeft-40;
		$(tarieventabel).animate({ scrollLeft: scrollLeftwaarde }, "fast",function(){
			var t=setTimeout("scrollleft("+i+")",200);
		});
	}
	return false;
}

function aanbieding_openklap(accid) {
	window.location.hash='aanbiedingblok'+accid;
	$('#bekijkalle_'+accid).slideUp('400',function() {
		$('#aanbieding_openklap_'+accid).slideDown('1000',function() {
			$('#aanbieding_verberg_'+accid).css('visibility','visible').hide().fadeIn();
		});
	});
}

function aanbieding_dichtklap(accid) {
	$('#aanbieding_openklap_'+accid).slideUp('1000',function() {
		$('#bekijkalle_'+accid).slideDown('400',function() {
			$('#aanbieding_verberg_'+accid).css('visibility','hidden');
		});
	});
}

//window.onload=onloadfunction;


//Get cookie routine by Shelley Powers 
function get_cookie(Name) {
  var search = Name + "="
  var returnvalue = "";
  if (document.cookie.length > 0) {
    offset = document.cookie.indexOf(search)
    // if cookie exists
    if (offset != -1) { 
      offset += search.length
      // set index of beginning of value
      end = document.cookie.indexOf(";", offset);
      // set index of end of cookie value
      if (end == -1) end = document.cookie.length;
      returnvalue=unescape(document.cookie.substring(offset, end))
      }
   }
  return returnvalue;
}

// hoofdpagina-carousel
var hoofdpagina_blok_teller=1;
var hoofdpagina_sla_1_ronde_over=0;
function hoofdpagina_carousel(teller) {
	if(teller==0 && hoofdpagina_sla_1_ronde_over==1) {
		hoofdpagina_sla_1_ronde_over=0;
	} else {
//		$("#blok_"+hoofdpagina_blok_teller).css("display","none");
		$("#blok_"+hoofdpagina_blok_teller).fadeOut("slow");
		$("#hoofdpagina_blok_teller_"+hoofdpagina_blok_teller).removeClass("hoofdpagina_blok_teller_active");
		if(teller==0) {
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
//		$("#blok_"+teller).css("display","block");
		$("#blok_"+teller).fadeIn("slow");
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

		$(".hoofdpagina_blok_content a").click(function(e) {
			e.preventDefault();
		});
		
		$(".hoofdpagina_blok_content").mouseenter(function() {
			$(this).find("img").css("border","1px solid #ffffff");
		});

		$(".hoofdpagina_blok_content").mouseleave(function() {
			$(this).find("img").css("border","1px solid #ff9900");
		});
		
		$(".hoofdpagina_blok_content").click(function() {
			document.location.href=$(this).find("a").attr("href");
		});
	}

	$(".skigebied_omschrijving_openklappen").click(function(e) {
		e.preventDefault();
		$("#skigebied_omschrijving_openklappen").html("");
		$("#skigebied_omschrijving_afgebrokendeel").slideToggle("slow",function() {
			if($("#skigebied_omschrijving_afgebrokendeel").is(":hidden")) {
				$("#skigebied_omschrijving_openklappen").html("Lees verder &raquo;");
			} else {
				$("#skigebied_omschrijving_openklappen").html("Dichtklappen &laquo;");
			}
		});
	});
	
	// hover "lees meer" bij hoofdpagina_vraagonsadvies
	$("#hoofdpagina_vraagonsadvies").mouseenter(function() {
		$("#hoofdpagina_vraagonsadvies_leesmeer").css("color","#ffffff");
	});

	$("#hoofdpagina_vraagonsadvies").mouseleave(function() {
		$("#hoofdpagina_vraagonsadvies_leesmeer").css("color","#661700");
	});

	// hover "bekijk het aanbod" bij hoofdpagina_tarievenalbekend
	$("#hoofdpagina_tarievenalbekend").mouseenter(function() {
		$("#hoofdpagina_tarievenalbekend_bekijk").css("color","#ffffff");
	});

	$("#hoofdpagina_tarievenalbekend").mouseleave(function() {
		$("#hoofdpagina_tarievenalbekend_bekijk").css("color","#661700");
	});
});
