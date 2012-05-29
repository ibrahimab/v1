
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


$(document).ready(function() {
	// openklap-functie bij landenpagina
	$(".omschrijving_openklappen").click(function(e) {
		e.preventDefault();
		$("#omschrijving_openklappen").html("");
		$("#omschrijving_afgebrokendeel").slideToggle("normal",function() {
			if($("#omschrijving_afgebrokendeel").is(":hidden")) {
				$("#omschrijving_openklappen").html("Lees verder &raquo;");
			} else {
//				$("#omschrijving_openklappen").html("Dichtklappen &laquo;");
			}
		});
	});
});

//window.onload=onloadfunction;
