
function MM_findObj(n, d) { //v3.0
var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}
/* Functions that swaps images. */
function MM_swapImage() { //v3.0
var layer1,i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
;
}
function MM_swapImgRestore() { //v3.0
var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
/* Functions that handle preload. */
function MM_preloadImages() { //v3.0
var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function popwindow(xsize,ysize,url,align) {
	var wWidth, wHeight, wLeft, wTop;
	wWidth = xsize;
	if(ysize>0) {
		wHeight = ysize;
	} else {
		wHeight = wWidth*.75;
	}
	if(align=='center') {
		wLeft = (screen.width-wWidth)/2;
		wTop = (screen.height-wHeight)/2;
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight+',left='+wLeft+',top='+wTop);
	} else {
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight);
	}
	Scherm.focus();
}

function getStyleClass (className) {
	if (document.all) {
		for (var s = 0; s < document.styleSheets.length; s++)
		for (var r = 0; r < document.styleSheets[s].rules.length; r++)
		if (document.styleSheets[s].rules[r].selectorText == '.' + className)
		return document.styleSheets[s].rules[r];
	} else if (document.getElementById) {
		for (var s = 0; s < document.styleSheets.length; s++)
		for (var r = 0; r < document.styleSheets[s].cssRules.length; r++)
		if (document.styleSheets[s].cssRules[r].selectorText == '.' + className)
		return document.styleSheets[s].cssRules[r];
	}
	return null;
}

function toggle_tonen_verbergen(classname) {
	if(tonen_of_niet) {
		$("."+classname).css("display","none");
		tonen_of_niet=0;
	} else {
		$("."+classname).css("display","table-row");
		tonen_of_niet=1;
	}
}

function popwindowXY(wWidth,wHeight,url,align) {
	var wLeft, wTop;
	if(align=='center') {
		wLeft = (screen.width-wWidth)/2;
		wTop = (screen.height-wHeight)/2;
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight+',left='+wLeft+',top='+wTop);
	} else {
		var Scherm = window.open(url, '_blank', 'scrollbars=yes,width='+wWidth+',height='+wHeight);
	}
	Scherm.focus();
}

function fotopopup(wWidth,wHeight,url,align) {
	var wLeft, wTop;
	if(align=='center') {
		wLeft = (screen.width-wWidth)/2;
		wTop = (screen.height-wHeight)/2;
		var Scherm = window.open(url, '_blank', 'scrollbars=no,width='+wWidth+',height='+wHeight+',left='+wLeft+',top='+wTop);
	} else {
		var Scherm = window.open(url, '_blank', 'scrollbars=no,width='+wWidth+',height='+wHeight);
	}
	Scherm.focus();
}

function fotopopuplarge(wWidth,wHeight,url,potitionleft) {
	var wLeft, wTop;
	url=url+'&sw='+screen.width+'&sh='+screen.height;
	if(potitionleft==true) {
		wLeft = 20;
		wTop = 20;
		var Scherm = window.open(url, '_blank', 'scrollbars=no,width='+wWidth+',height='+wHeight+',left='+wLeft+',top='+wTop);
	} else {
		var Scherm = window.open(url, '_blank', 'scrollbars=no,width='+wWidth+',height='+wHeight);
	}
	Scherm.focus();
}

function controle_form_gewijzigd() {
	if(form_gewijzigd) {
		alert('De gewijzigde gegevens zijn nog niet opgeslagen. Wilt u deze wijzigingen annuleren?');
	}
}

function annverz(aantalpersonen,state) {
	var z = 0;
	for(z=1;z<=aantalpersonen;z++) {
		if(state) {
			if(document.forms['frm'].elements['input[annverz_'+z+']'].value==0) {
				document.forms['frm'].elements['input[annverz_'+z+']'].value=2;
			}
		} else {
			document.forms['frm'].elements['input[annverz_'+z+']'].value=0;
		}
	}
}

function fieldcopy(field1,field2) {
	document.frm.elements['input['+field2+']'].value=document.frm.elements['input['+field1+']'].value;
}

function accommodatiepagina_flexibel(e) {
	var foo = new Date; // Generic JS date object
	var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
	var unixtime = parseInt(unixtime_ms / 1000);

	if(e.forms['zoeken'].elements['fadf_d'].value>0 && e.forms['zoeken'].elements['fadf_m'].value>0 && e.forms['zoeken'].elements['fadf_y'].value>0) {
		unixtime=wt_mktime(0,0,0,e.forms['zoeken'].elements['fadf_m'].value,e.forms['zoeken'].elements['fadf_d'].value,e.forms['zoeken'].elements['fadf_y'].value);
	}
	if(e.forms['zoeken'].elements['flad']) {
		e.forms['zoeken'].elements['flad'].value=unixtime;
	}
}

function accommodatiepagina_flexibel_onchange() {
	if(document.forms['zoeken'].elements['fadf_d'].value>0 && document.forms['zoeken'].elements['fadf_m'].value>0 && document.forms['zoeken'].elements['fadf_y'].value>0) {
		accommodatiepagina_flexibel(document);
		document.zoeken.submit();
	}
}

function wt_mktime () {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: baris ozdil
    // +      input by: gabriel paderni
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: FGFEmperor
    // +      input by: Yannoo
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: jakes
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Marc Palau
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: 3D-GRAF
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Chris
    // +    revised by: Theriault
    // %        note 1: The return values of the following examples are
    // %        note 1: received only if your system's timezone is UTC.
    // *     example 1: mktime(14, 10, 2, 2, 1, 2008);
    // *     returns 1: 1201875002
    // *     example 2: mktime(0, 0, 0, 0, 1, 2008);
    // *     returns 2: 1196467200
    // *     example 3: make = mktime();
    // *     example 3: td = new Date();
    // *     example 3: real = Math.floor(td.getTime() / 1000);
    // *     example 3: diff = (real - make);
    // *     results 3: diff < 5
    // *     example 4: mktime(0, 0, 0, 13, 1, 1997)
    // *     returns 4: 883612800 
    // *     example 5: mktime(0, 0, 0, 1, 1, 1998)
    // *     returns 5: 883612800 
    // *     example 6: mktime(0, 0, 0, 1, 1, 98)
    // *     returns 6: 883612800 
    // *     example 7: mktime(23, 59, 59, 13, 0, 2010)
    // *     returns 7: 1293839999
    // *     example 8: mktime(0, 0, -1, 1, 1, 1970)
    // *     returns 8: -1
    var d = new Date(),
        r = arguments,
        i = 0,
        e = ['Hours', 'Minutes', 'Seconds', 'Month', 'Date', 'FullYear'];

    for (i = 0; i < e.length; i++) {
        if (typeof r[i] === 'undefined') {
            r[i] = d['get' + e[i]]();
            r[i] += (i === 3); // +1 to fix JS months.
        } else {
            r[i] = parseInt(r[i], 10);
            if (isNaN(r[i])) {
                return false;
            }
        }
    }

    // Map years 0-69 to 2000-2069 and years 70-100 to 1970-2000.
    r[5] += (r[5] >= 0 ? (r[5] <= 69 ? 2e3 : (r[5] <= 100 ? 1900 : 0)) : 0);

    // Set year, month (-1 to fix JS months), and date.
    // !This must come before the call to setHours!
    d.setFullYear(r[5], r[3] - 1, r[4]);

    // Set hours, minutes, and seconds.
    d.setHours(r[0], r[1], r[2]);

    // Divide milliseconds by 1000 to return seconds and drop decimal.
    // Add 1 second if negative or it'll be off from PHP by 1 second.
    return (d.getTime() / 1e3 >> 0) - (d.getTime() < 0);
}

function testgegevens() {
	document.forms['frm'].elements['input[voornaam]'].value='Jan';
	document.forms['frm'].elements['input[tussenvoegsel]'].value='de';
	document.forms['frm'].elements['input[achternaam]'].value='Boer';
	document.forms['frm'].elements['input[plaats]'].value='Amsterdam';
	document.forms['frm'].elements['input[telefoonnummer]'].value='020-1234567';
	document.forms['frm'].elements['input[geboortedatum][day]'].value='15';
	document.forms['frm'].elements['input[geboortedatum][month]'].value='5';
	document.forms['frm'].elements['input[geboortedatum][year]'].value='1985';
	document.forms['frm'].elements['input[geslacht]'][0].checked=true;
	document.forms['frm'].elements['input[mobielwerk]'].value='06-12345678';
	document.forms['frm'].elements['input[email]'].value='noreply@webtastic.nl';
	document.forms['frm'].elements['input[adres]'].value='Testpad 11';
	document.forms['frm'].elements['input[postcode]'].value='1111 AA';
}

function check_hash() {
	if($().tabs) {
	
		var $tabs = $('#tabs').tabs();
		
		// accommodatiepagina
		if(location.hash=="#overzicht") {
			$tabs.tabs('select','#tabs_overzicht');
		}
		if(location.hash=="#indeling") {
			$tabs.tabs('select','#tabs_indeling');
		}
		if(location.hash=="#fotos") {
			$tabs.tabs('select','#tabs_fotos');
		}
		if(location.hash=="#ligging") {
			$tabs.tabs('select','#tabs_ligging');
		}
		if(location.hash=="#extraopties") {
			$tabs.tabs('select','#tabs_opties');
		}
		if(location.hash=="#prijsinformatie") {
			$tabs.tabs('select','#tabs_tarieven');
		}
	
		// regiopagina / plaatspagina
		if(location.hash=="#beschrijving") {
			$tabs.tabs('select','#tabs_beschrijving');
		}
	}
}

//
// Google Maps
//
var geocoder;
var map;
var googlemaps_lat=0;
var googlemaps_long=0;
var zoomlevel=11;
var gps_coordinaten_bekend=0;
var googlemaps_selected_icon=1;
var googlemaps_init=false;
var googlemaps_base='/';
var googlemaps_naam='';
var googlemaps_plaatsland='';
var googlemaps_aantalpersonen='';
var googlemaps_afbeelding='';
var googlemaps_icon = [];
var googlemaps_icon_ander = [];
googlemaps_icon[1] = 'chalet.png';
googlemaps_icon[2] = 'chalet.png';
googlemaps_icon[3] = 'zomerhuisje.png';
googlemaps_icon[4] = 'chalet.png';
googlemaps_icon[6] = 'vallandry.png';
googlemaps_icon[7] = 'italissima.png';
googlemaps_icon[71] = 'italissima_plaats.png';
googlemaps_icon_ander[1] = 'chaletander.png';
googlemaps_icon_ander[2] = 'chaletander.png';
googlemaps_icon_ander[3] = 'zomerhuisjeander.png';
googlemaps_icon_ander[4] = 'chaletander.png';
googlemaps_icon_ander[6] = 'vallandryander.png';
googlemaps_icon_ander[7] = 'italissimaander.png';
googlemaps_icon_ander[71] = 'italissima_plaatsander.png';
var googlemaps_marker = [];
var googlemaps_marker_2 = [];
var googlemaps_infowindow;
var googlemaps_counter=0;
var googlemaps_skigebiedid=0;
var zoekblok_tekst='';

function initialize_googlemaps() {
	//http://code.google.com/intl/nl/apis/maps/documentation/javascript/reference.html
	if(gps_coordinaten_bekend==1) {
//		geocoder = new google.maps.Geocoder();
		var myOptions = {
			zoom: zoomlevel,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scaleControl: true,
			panControl: true,
			overviewMapControl: true,
			streetViewControl: true,
			mapTypeControl: true,
			panControl: true,
			zoomControl: true,
			scrollwheel: false,
			center: new google.maps.LatLng(googlemaps_lat,googlemaps_long)
		}
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		google.maps.event.addListener(map, 'bounds_changed', function(event) {
			if(!googlemaps_init&&googlemaps_skigebiedid==0) {
				var value = [];
				value[1]=googlemaps_lat;
				value[2]=googlemaps_long;
				value['naamhtml']=googlemaps_naam;
				value['plaatsland']=googlemaps_plaatsland;
				value['aantalpersonen']=googlemaps_aantalpersonen;
				value['afbeelding']=googlemaps_afbeelding;
				googlemaps_createmarker(value,true);

				$("#googlemaps_other").css("visibility","visible");
				googlemaps_init=true;
			}
		});
		if(googlemaps_skigebiedid>0) {
			// Italissima: plaatsen op de kaart zetten
			googlemaps_show_villages();
		}
	}
}

function googlemaps_createmarker(value,main) {
	googlemaps_counter=googlemaps_counter+1;
	var myLatLng = new google.maps.LatLng(value[1],value[2]);
	if(googlemaps_skigebiedid>0) {
		if(value.binnengebied==1) {
			main=true;
		} else {
			main=false;
		}
	}
	var marker = googlemaps_marker[googlemaps_counter];
	marker = new google.maps.Marker({
		map: map, 
		position: myLatLng,
		draggable: false,
		icon: googlemaps_base+'pic/googlemapsicons/'+(main ? googlemaps_icon[googlemaps_selected_icon] : googlemaps_icon_ander[googlemaps_selected_icon]),
		animation: google.maps.Animation.DROP,
		title: value['naam'],
		zIndex: (main ? 1000 : googlemaps_counter)
	});
	googlemaps_marker_2.push(marker);
	google.maps.event.addListener(marker, 'click', function() {
		if(googlemaps_infowindow) googlemaps_infowindow.close();
		if(googlemaps_skigebiedid>0) {
			var tmpcontent=	'<div class="googlemaps_infowindow">'+
				        '<b>'+value['naamhtml']+'&nbsp;&nbsp;&nbsp;</b><br>'+
					''+value['aantalacc']+'<br>'+
					value['skigebied']+
					'</div>';
		} else {
			if(main) {
				var tmpcontent=	'<div class="googlemaps_infowindow">'+
						'<img src="'+value['afbeelding']+'">'+
					        '<b>'+value['naamhtml']+'&nbsp;&nbsp;&nbsp;</b><br>'+
						''+value['plaatsland']+'<br>'+
						value['aantalpersonen']+
						'</div>';
			} else {
				var tmpcontent=	'<div class="googlemaps_infowindow">'+
						'<img src="'+value['afbeelding']+'">'+
					        '<b><a href="'+value['url']+'">'+value['naamhtml']+'</a>&nbsp;&nbsp;&nbsp;</b><br>'+
						''+value['plaatsland']+'<br>'+
						value['aantalpersonen']+
						'</div>';
			}
		}
		googlemaps_infowindow = new google.maps.InfoWindow({
			content: tmpcontent
		});
		googlemaps_infowindow.open(map,marker);
	});
}

function googlemaps_deletemarkers() {
	for (var i = 1; i < googlemaps_marker_2.length; i++) {
		googlemaps_marker_2[i].setMap(null);
	}
	$("#googlemaps_deleteother").css("visibility","hidden");
	$("#googlemaps_other").css("visibility","visible");
}

function googlemaps_show_other_acc() {
	if(googlemaps_init) {
		for (var i = 1; i < googlemaps_marker_2.length; i++) {
			googlemaps_marker_2[i].setMap(null);
		}
		$.getJSON(googlemaps_base+"rpc_json.php", {
			"t": 1,
			"lat1":map.getBounds().getNorthEast().lat(),
			"long1":map.getBounds().getNorthEast().lng(),
			"lat2":map.getBounds().getSouthWest().lat(),
			"long2":map.getBounds().getSouthWest().lng(),
			"accid":googlemaps_accid,
			"typeid":googlemaps_typeid
		}, function(data) {
			if(data.ok&&data.aantal>0) {
				$.each(data.acc, function(key, value) {
					googlemaps_createmarker(value,false);
				});
				$("#googlemaps_deleteother").css("visibility","visible");
				$("#googlemaps_other").css("visibility","hidden");
			}
		});
	}
}

function googlemaps_show_villages() {
	// plaatsen tonen op regiopagina Italissima
	for (var i = 1; i < googlemaps_marker_2.length; i++) {
		googlemaps_marker_2[i].setMap(null);
	}
	$.getJSON(googlemaps_base+"rpc_json.php", {
		"t": 2,
		"skigebiedid":googlemaps_skigebiedid
	}, function(data) {
		if(data.ok&&data.aantal>0) {
			$.each(data.plaats, function(key, value) {
				googlemaps_createmarker(value,false);
			});
		}
	});
}

function recordOutboundPopup(category, action) {
	try {
		var myTracker=_gat._getTrackerByName();
		_gaq.push(['myTracker._trackEvent',category,action]);
	} catch(err) {}
}

var tonen_of_niet;
var form_gewijzigd;
var selectbox_actief=0;
var showhidelink_text='';
var txtzoeken='';
var gebruik_jquery=true;
var landkaartklikbaar_info_hoverkleur="#636f07";

$(document).ready(function() {

	if(gebruik_jquery==true) {
		// jquery
		
		
		if($().tabs) {
			// fotoslide hoofdpagina
		//	$("#featured").tabs({fx:{opacity: "toggle"}}).tabs("rotate", 5000, true);
			$("#featured > ul").tabs({event: 'mouseover',fx:{opacity: "toggle"}}).tabs("rotate", 5000, true);  
		
			// tabs	
			var $tabs = $('#tabs').tabs();
		
		//	$(".tabclick").click(function () {
		//		window.location.hash = $(this).attr("href");
		//	});
		
			$('#meerfotos').click(function() {
				$tabs.tabs('select','#tabs_fotos');
				return false;
			});
		
			$('.gototabs_tarieven').click(function() {
				$tabs.tabs('select','#tabs_tarieven');
				return false;
			});
	
			$('.gototabs_ligging').click(function() {
				$tabs.tabs('select','#tabs_ligging');
				return false;
			});
		}
			
		var myFile = document.location.toString();
		if (myFile.match('#')) {
			var myAnchor = myFile.split('#')[1];
			var selecttab = myAnchor.split('_')[0];
			if(selecttab=='selecttab') {
				var selecttab_value = myAnchor.split('_')[1];
				$tabs.tabs('select','#tabs_'+selecttab_value);
			}
		} else if (myFile.match('selecttab=([a-z0-9]+)')) {
			a = myFile.match('selecttab=([a-z0-9]+)');
			if(a[1]) {
				$tabs.tabs('select','#tabs_'+a[1]);
			}
		} else if (myFile.match('otsid=') && myFile.match('&optie_datum=')) {
			$tabs.tabs('select','#tabs_opties');
		}
	
		check_hash();
	
	//	$("#tabs a").click(function () {
	//		if(this.hash=="#tabs_overzicht") {
	//			window.location.hash = "#overzicht";
	//		}
	//		if(this.hash=="#tabs_indeling") {
	//			window.location.hash = "#indeling";
	//		}
	//		if(this.hash=="#tabs_fotos") {
	//			window.location.hash = "#fotos";
	//		}
	//		if(this.hash=="#tabs_ligging") {
	//			window.location.hash = "#ligging";
	//		}
	//		if(this.hash=="#tabs_opties") {
	//			window.location.hash = "#extraopties";
	//		}
	//		if(this.hash=="#tabs_tarieven") {
	//			window.location.hash = "#prijsinformatie";
	//		}
	//	});
	
		// show/hide toggle
		$(".showhidelink").click(function () {
			$(".showhide").slideToggle("slow",function() {
				if(showhidelink_text) {
					$(".showhidelink").html(showhidelink_text);
					showhidelink_text='';
				} else {
					showhidelink_text=$(".showhidelink").html();
					$(".showhidelink").html("&lt;&lt;");
				}
			});
			return false;
		});
	
		// show click
		$(".showlink").click(function () {
			$(".showlinkdiv").slideDown("slow",function() {
				$(".showlink").css("visibility","hidden");
			});
			return false;
		});
	

		if($().fancybox) {
		
			// foto-popups via fancybox
			$(".fotopopup").fancybox({
				'transitionIn'	:	'elastic',
				'transitionOut'	:	'elastic',
				'speedIn'		: 300,
				'speedOut'		: 300,
				'padding' :		0,
				'margin' :		0,
				'autoScale' :		true,
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'onComplete'	:	function() {
		
				},
				'cyclic'	:	true,
				'overlayOpacity' :	.8
			});
		}
		
		// functies voor zoekformulier	
		$(".onchangesubmit").change(function() {
			this.form.submit();
		});
	
		$(".txtzoeken").change(function() {
			txtzoeken=this.value;
		});
		$(".keuzeopsomming a").click(function () {
			if(txtzoeken) {
				key = 'fzt';
				value = escape(txtzoeken).replace(/\+/g,'%2B').replace(/%20/g, '+').replace(/\*/g, '%2A').replace(/\//g, '%2F').replace(/@/g, '%40');
				var kvp = this.href.split('&');
				var i=kvp.length; var x; while(i--) {
					x = kvp[i].split('=');
					if (x[0]==key) {
						x[1] = value;
						kvp[i] = x.join('=');
						break;
					}
				}
				if(i<0) {kvp[kvp.length] = [key,value].join('=');}
				nieuwelink = kvp.join('&');
				this.href=nieuwelink;
			}
			return true;
		});


		$(".laatstbekeken_acc").hover(
			function() {
				$(this).children(".laatstbekeken_tekst").addClass("laatstbekeken_tekst_hover");
//				$(this).children("img").addClass("laatstbekeken_img_hover");
			},
			function() {
				$(this).children(".laatstbekeken_tekst").removeClass("laatstbekeken_tekst_hover");
//				$(this).children("img").removeClass("laatstbekeken_img_hover");
		});
		
		// img hover on / off inclusief preload
		$(".img-swap").hover(
			function(){this.src = this.src.replace("_off","_on");},
			function(){this.src = this.src.replace("_on","_off");
		});
		
		$.fn.preload = function() {
			this.each(function(){
				$('<img/>')[0].src = this;
			});
		}

		// Declare the array variable
		var imgSwap = [];
		// Select all images used in the image swap function - in our case class "img-swap"

		$(".img-swap").each(function(){
			// Loop through all images which are used in our image swap function
			// Get the file name of the active images to be loaded by replacing _off with _on
			imgUrl = this.src.replace("_off","_on");
	
			// Store the file name in our array
			imgSwap.push(imgUrl);
		});
		// Pass the array to our preload function
		$(imgSwap).preload();
		
		// landkaarten Zomerhuisje: imagemap en onmouseover
		if($().maphilight && $(".map").length!=0) {
			$.fn.maphilight.defaults = {
				fill: true,
				fillColor: landkaart_hoverkleur,
				fillOpacity: 1.0,
				stroke: true,
				strokeColor: landkaart_hoverkleur,
				strokeOpacity: 1,
				strokeWidth: 1,
				fade: true,
				alwaysOn: false,
				neverOn: false,
				groupBy: false,
				wrapClass: true,
				shadow: false,
				shadowX: 0,
				shadowY: 0,
				shadowRadius: 6,
				shadowColor: '000000',
				shadowOpacity: 0.8,
				shadowPosition: 'outside',
				shadowFrom: false
			}

			$(".map").maphilight();
			
			
			$("#landkaartklikbaar_namen div").mouseover(function() {
				$("#area"+$(this).attr("rel")).mouseover();
			});

			$("#landkaartklikbaar_namen div").mouseout(function() {
				$("#area"+$(this).attr("rel")).mouseout();
			});

			$(".maphref").mouseover(function() {
				$("#regio_"+$(this).attr("rel")+" a").css("color",landkaartklikbaar_info_hoverkleur);
				$("#landkaartklikbaar_info").html($("#area"+$(this).attr("rel")).attr("title"));
			});
			$(".maphref").mouseout(function() {
				$("#regio_"+$(this).attr("rel")+" a").css("color","");
				$("#landkaartklikbaar_info").html("");
			});
			
			$(".nomaphref").click(function() {
				return false;
			});
		}
		
		// afbreken reviews
		if($(".reviewblok_afbreken").length!=0) {
			$('.reviewblok_afbreken').each(function() {
				if($(this).find(".reviewblok_afbreken_content").height()>85) {
					$(this).next(".reviewblok_afbreken_openklap").css("display","block");
				}
			});
			
			$(".reviewblok_afbreken_openklap").click(function () {
				if($(this).prev(".reviewblok_afbreken").css("max-height")=="3000px") {
					$(this).prev(".reviewblok_afbreken").css("max-height","85px");
					$(this).html("<a href=\"#\" onclick=\"return false;\">Lees verder</a>");
					
				} else {
					$(this).prev(".reviewblok_afbreken").css("max-height","3000px");
					$(this).html("<a href=\"#\" onclick=\"return false;\">Beoordeling inklappen</a>");
				}
			});
		}

		// Facebook share-window
		$(".facebook_share_window").click( function() {
			popwindowXY(700,450,$(this).attr("href"),true);
			var _gaq = _gaq || [];
			_gaq.push(['_trackSocial', 'facebook', 'share', '']);
			return false;
		});
		
		// bug in tarieventabel bij IE9
		if($(".tarieventabel_div").length!=0) {
			if($("#weektarieven").length!=0) {

			} else {
				// workaround bug in IE9 (tarieventabel 'groeit' bij onmouseover op de tarieven)
				$(".tarieventabel_div").css("height",$(".tarieventabel_div").height());
			}
		}
		
		// Opmaak zoekbox via jQuery Chosen
		if($().chosen) {
			$("#zoekblok select:not(.zoekblok_select_date)").chosen({allow_single_deselect:true,search_contains:true}).change(function() {
				$("#zoeken").submit();
			});

			$("#zoekblok .zoekblok_select_date").chosen({disable_search_threshold:1000,allow_single_deselect:true,search_contains:true}).change(function() {
				if($("select[name=fadf_d]").val() && $("select[name=fadf_m]").val() && $("select[name=fadf_y]").val()) {
					$("#zoeken").submit();
				}
			});

			$("#zoekblok input[name=fzt]").focus(function() {
				zoekblok_tekst=$("#zoekblok input[name=fzt]").val();
			});

			// na blur op tekstzoeken: form submit			
			$("#zoekblok input[name=fzt]").blur(function() {
				if(zoekblok_tekst!=$("#zoekblok input[name=fzt]").val()) {
					$("#zoeken").submit();
				}
			});
		
			$("#zoekblok input[name=fzt]").keypress(function(e) {
				var code = (e.keyCode ? e.keyCode : e.which);
				if(code==13) {
					$("#zoeken").submit();
				} else {
					return true;
				}
			});
		}
		
		// zoekresultaten: ie8-bug m.b.t. hover en border
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
		
		// bij zoekresultaten met aanbiedingen: breedte zoekresultaat_type_typenaam_aanbieding aanpassen aan de hand van breedte zoekresultaat_type_aanbieding
		if($(".zoekresultaat_type_aanbieding").length!=0) {

			$(".zoekresultaat_type_aanbieding").each(function() {
				if($(this).prev(".zoekresultaat_type_typenaam_aanbieding").html().length!=0) {
					var nieuwebreedte=230-10-$(this).outerWidth(true);
					$(this).prev(".zoekresultaat_type_typenaam_aanbieding").css("width",nieuwebreedte);
				}
			});
		}
	}
});

function weektarieven_openklappen() {
	$('#weektarieven').slideDown('slow','linear', function() {
		// workaround bug in IE9 (tarieventabel 'groeit' bij onmouseover op de tarieven)
		$(".tarieventabel_div").css("height",$(".tarieventabel_div").height());
	});
	$('#bekijkookweektarieven').css('visibility','hidden');
	return false;
}

//window.onhashchange = check_hash;
