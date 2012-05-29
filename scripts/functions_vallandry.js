
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

var rotatephoto_teller=1;

function rotatephotos() {
	$("#rotatediv"+rotatephoto_teller).fadeOut("slow");
	rotatephoto_teller=rotatephoto_teller+1;
	if(rotatephoto_teller>19) {
		rotatephoto_teller=1;
	}
	$("#rotatediv"+rotatephoto_teller).fadeIn("slow",function(){
		var t=setTimeout("rotatephotos()",2500);
	});
}
