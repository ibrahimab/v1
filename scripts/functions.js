
// Hides the tabs + zoekblok during initialization
document.write('<style type="text/css">	#tabs { visibility: hidden; } #body_zoek-en-boek #zoekblok { visibility: hidden; } </style>');

// detecteer een mobiel apparaat (tablet of smartphone)
function detect_mobile() {
	return (
		//Detect iPhone
		(navigator.userAgent.match(/iPhone/i) !== null) ||
		//Detect iPod
		(navigator.userAgent.match(/iPad/i) !== null) ||
		//Detect Android
		(navigator.userAgent.match(/Android/i) !== null)
	);
}

// querystring aan URL toevoegen
function updateURLParameter(url, param, paramVal){
	var newAdditionalURL = "";
	var tempArray = url.split("?");
	var baseURL = tempArray[0];
	var additionalURL = tempArray[1];
	var temp = "";
	if (additionalURL) {
		tempArray = additionalURL.split("&");
		for (i=0; i<tempArray.length; i++){
			if(tempArray[i].split('=')[0] != param){
				newAdditionalURL += temp + tempArray[i];
				temp = "&";
			}
		}
	}

	var rows_txt = temp + "" + param + "=" + paramVal;
	return baseURL + "?" + newAdditionalURL + rows_txt;
}

// popup in opgegeven width en height
function popwindow(xsize,ysize,url,align) {
	var wWidth, wHeight, wLeft, wTop;
	wWidth = xsize;
	if(ysize>0) {
		wHeight = ysize;
	} else {
		wHeight = wWidth*0.75;
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
googlemaps_icon[8] = 'superski.png';
googlemaps_icon_ander[1] = 'chaletander.png';
googlemaps_icon_ander[2] = 'chaletander.png';
googlemaps_icon_ander[3] = 'zomerhuisjeander.png';
googlemaps_icon_ander[4] = 'chaletander.png';
googlemaps_icon_ander[6] = 'vallandryander.png';
googlemaps_icon_ander[7] = 'italissimaander.png';
googlemaps_icon_ander[71] = 'italissima_plaatsander.png';
googlemaps_icon_ander[8] = 'superskiander.png';
var googlemaps_marker = [];
var googlemaps_marker_2 = [];
var googlemaps_infowindow;
var googlemaps_counter=0;
var googlemaps_skigebiedid=0;
var zoekblok_tekst='';
var lokale_testserver=false;
var absolute_path='/';
if(location.href.indexOf("/chalet/")>1) {
	var absolute_path='/chalet/';
	var lokale_testserver=true;
}

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

		// positie goedzetten
		if(googlemaps_init) {
			google.maps.event.trigger(map, 'resize');
			map.setCenter(new google.maps.LatLng(googlemaps_lat,googlemaps_long));
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

function show_ajaxloader(only_show_2_seconds) {
	// ajaxloader tonen
	$("#ajaxloader_page").show();

	if(only_show_2_seconds) {
		// na 2 seconden weer verbergen indien only_show_2_seconds=true
		setTimeout(function() {
			$("#ajaxloader_page").hide();
		},2000);
	}
	return true;
}

function zoekblok_submit() {
	// zoekformulier submitten (en vooraf ajaxloader tonen)
	if($("input[name=fzt]").val()==$("input[name=fzt]").data("placeholder")) {
		$("input[name=fzt]").val("");
	}
	show_ajaxloader(false);
	$("#zoeken").submit();
}

var tonen_of_niet;
var form_gewijzigd;
var selectbox_actief=0;
var showhidelink_text='';
var gebruik_jquery=true;
var landkaartklikbaar_info_hoverkleur="#636f07";
var eerste_tab_getoond=false;
var google_analytics_tab_verstuurd=false;

$(document).ready(function() {

	if(gebruik_jquery===true) {
		//
		// jquery
		//

		if($().tabs) {
			//
			// tabs
			//

			// http://stackoverflow.com/questions/243794/jquery-ui-tabs-causing-screen-to-jump

			var $tabs = $('#tabs').tabs({
				fx: { opacity: 'toggle', duration:'fast'},
				select: function(event, ui) {
					$(this).css('height', $(this).height());
					$(this).css('overflow', 'hidden');
				},
				show: function(event, ui) {
					$(this).css('height', 'auto');
					$(this).css('overflow', 'visible');

					// zorgen dat IE bij rechtstreeks openen van een hash-pagina naar boven scrollt
					if(eerste_tab_getoond===false) {
						window.scrollTo(0, 0);
						setTimeout(function() {
							if (location.hash) {
								window.scrollTo(0, 0);
							}
						},1);
						setTimeout(function() {
							if (location.hash) {
								window.scrollTo(0, 0);
							}
						},100);
						setTimeout(function() {
							if (location.hash) {
								window.scrollTo(0, 0);
							}
						},250);
						setTimeout(function() {
							if (location.hash) {
								window.scrollTo(0, 0);
							}
						},500);
					}
					eerste_tab_getoond=true;
				}
			});

			if($().address) {
				//
				// juiste verwerking hashes bij de tabs
				//

				// lege function zodat address-plugin niet zelf Google Analytics gebruikt
				$.address.tracker(function(){

				});

				// na wijzigen hash: juiste tab tonen
				$.address.change(function(event) {

					// Google Analytics bij switchen tussen tabs
					if (typeof _gaq != "undefined") {

						var canonical_link;
						if($("#body_toonaccommodatie").length!==0) {
							try {
								canonical_link = $('link[rel=canonical]').attr('href').split(location.hostname)[1] || window.location.pathname;
							} catch(e) {
								canonical_link = window.location.pathname;
							}
						} else {
							try {
								canonical_link = $('link[rel=canonical]').attr('href').split(location.hostname)[1] || undefined;
							} catch(e) {
								canonical_link = undefined;
							}
						}
						if(window.location.hash.length>1) {
							canonical_link=canonical_link+ '/tab-' + window.location.hash.substr(1);
						}
						if($("#body_toonaccommodatie").length!==0 || google_analytics_tab_verstuurd===false) {
							_gaq.push(['_trackPageview', canonical_link]);
							google_analytics_tab_verstuurd=true;
						}
					}

					// tab switchen
					$("#tabs").tabs("select",window.location.hash);

					// links naar andere types van deze accommodatie aanpassen
					if(window.location.hash) {
						$(".linkandertype").each(function() {
							$(this).attr("href",$(this).attr("href").replace(/#.*/,window.location.hash));
						});
					}
				});

				// klikken op tab: hash veranderen
				$("#tabs > ul li a").click(function(event) {

					// div met deze id verwijderen, dan hash aanpassen, dan div weer terugzetten (voorkomt scrollen tijdens klikken)
					hash = $(this).attr("href").replace( /^#/, '' );
					var node = $( '#' + hash );
					if ( node.length ) {
						node.attr( 'id', '' );
					}
					window.location.hash = $(this).attr("href");
					if ( node.length ) {
						node.attr( 'id', hash );
					}
				});

				// zorgen dat pagina niet naar beneden scrollt na eerste keer oproepen
				window.scrollTo(0, 0);
				setTimeout(function() {
					if (location.hash) {
						window.scrollTo(0, 0);
					}
				},1);

				// zorgen dat na verzenden formulieren de juiste tab wordt geopend
				var myFile = document.location.toString();
				if (myFile.match('selecttab=([a-z0-9]+)')) {
					a = myFile.match('selecttab=([a-z0-9]+)');
					if(a[1]) {
						$tabs.tabs('select','#'+a[1]);

						// naar beneden scrollen
						window.scrollTo(0,$("#scroll_to_tabs").position().top);
					}
				} else if (myFile.match('otsid=') && myFile.match('&optie_datum=')) {
					$tabs.tabs('select','#extraopties');
					window.location.hash = '#extraopties';
				}
			}

			// tabs pas tonen als alles klaar is
			$("#tabs").css("visibility","visible");

		}

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
				'overlayOpacity' :	0.8
			});

			// beoordeling-popup via fancybox
			$("#beoordeling_link").fancybox({
				'type'			: 'inline',
				'width'			: 830,
				'height'		: 540,
				'autoDimensions': false,
				'autoScale'		: false,
				'transitionIn'	:	'elastic',
				'transitionOut'	:	'elastic',
				'speedIn'		: 300,
				'speedOut'		: 300,
				'padding' :		0,
				'margin' :		0,
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'onComplete'	:	function() {

				},
				'overlayOpacity' :	0.8
			});

			// popup_fancybox via fancybox
			$(".popup_fancybox").fancybox({
				'type'			: 'inline',
				'width'			: 430,
				'height'		: 240,
				'autoDimensions': false,
				'autoScale'		: false,
				'transitionIn'	:	'elastic',
				'transitionOut'	:	'elastic',
				'speedIn'		: 300,
				'speedOut'		: 300,
				'padding' :		0,
				'margin' :		0,
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'onComplete'	:	function() {

				},
				'overlayOpacity' :	0.8
			});

			// vimeo_fancybox via fancybox
			$(".vimeo_fancybox").fancybox({
				'type'			:   'iframe',
				'transitionIn'	:	'elastic',
				'transitionOut'	:	'elastic',
				'width'			: 900,
				'height'		: 510,
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
				'cyclic'	:	true,
				'overlayOpacity' :	0.8

			});

			// tijdelijk: fancybox-popup bij Zomerhuisje (melding over koerswijziging)
			if($("#zomerhuisje_popup").length!==0) {
				$("#zomerhuisje_popup").fancybox({
					'type'			: 'inline',
					'width'			: 670,
					'height'		: 420,
					'autoDimensions': false,
					'autoScale'		: false,
					'transitionIn'	:	'elastic',
					'transitionOut'	:	'elastic',
					'speedIn'		: 300,
					'speedOut'		: 300,
					'padding' :		0,
					'margin' :		0,
					'overlayShow'	:	true,
					'hideOnContentClick' :	false,
					'overlayColor' :	'#454545',
					'onComplete'	:	function() {

					},
					'overlayOpacity' :	0.8
				});

				$.fancybox.init();

				// popup direct tonen bij openen pagina
				$("#zomerhuisje_popup").click();
			}
		}

		if($("#submenu_zomerhuisje").length!==0) {
			// tijdelijk: cookie zh_kw (Zomerhuisje-koerswijziging) plaatsen zodat popup maar 1x wordt getoond
			chalet_createCookie("zh_kw","1",3650);
		}

		// automatisch submitten zoekformulier
		$(".onchangesubmit").change(function() {
			zoekblok_submit();
		});


		// automatisch submitten na wijzigen aankomstdatum (dag/maand/jaar)
		if($("div.zoekblok_aankomstdatum select[name=fadf_d]").length!==0) {
			$("div.zoekblok_aankomstdatum select").change(function() {
				if($("select[name=fadf_d]").val()>0 && $("select[name=fadf_m]").val()>0 && $("select[name=fadf_y]").val()>0) {
					zoekblok_submit();
				}
				if($("select[name=fadf_d]").val()==="" && $("select[name=fadf_m]").val()==="" && $("select[name=fadf_y]").val()==="") {
					zoekblok_submit();
				}
			});
		}

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
		};

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
			};

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
		if($(".reviewblok_afbreken").length!==0) {
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

		// Twitter share-window
		$(".twitter_share_window").click( function() {
			popwindowXY(700,450,$(this).attr("href"),true);
			var _gaq = _gaq || [];
			_gaq.push(['_trackSocial', 'twitter', 'share', '']);
			return false;
		});

		// Google+ share-window
		$(".googleplus_share_window").click( function() {
			popwindowXY(700,450,$(this).attr("href"),true);
			var _gaq = _gaq || [];
			_gaq.push(['_trackSocial', 'googleplus', 'share', '']);
			return false;
		});

		// bug in tarieventabel bij IE9
		if($(".tarieventabel_div").length!==0) {
			if($("#weektarieven").length!==0) {

			} else {
				$('.tarieventabel_div').each(function() {
					// workaround bug in IE9 (tarieventabel 'groeit' bij onmouseover op de tarieven)
					$(this).css("height",$(this).height());
				});
			}
		}

		// zoekformulier
		if($("#zoekblok").length!==0) {

			$("#zoekblok input[name=fzt]").focus(function() {
				if($("#zoekblok input[name=fzt]").val()!=$("#zoekblok input[name=fzt]").data("placeholder")) {
					zoekblok_tekst=$("#zoekblok input[name=fzt]").val();
				}
			});

			// na blur op tekstzoeken: form submit
			$("#zoekblok input[name=fzt]").blur(function() {
				if(zoekblok_tekst!=$("#zoekblok input[name=fzt]").val()) {
					zoekblok_submit();
				}
			});

			$("#zoekblok input[name=fzt]").keypress(function(e) {
				var code = (e.keyCode ? e.keyCode : e.which);
				if(code==13) {
					zoekblok_submit();
				} else {
					return true;
				}
			});

			//
			// jQuery Chosen
			//

			// al gekozen bestemmingen: grijs kleuren in pulldown
			$("div.zoekblok_bestemming_actief_item").each(function() {
//				alert($(this).data("bestemming_actief_value"));
//				alert($("option[value="+$(this).data("bestemming_actief_value")+"]").val());
				$("select[name=fsg_invoer] > option[value="+$(this).data("bestemming_actief_value")+"]").addClass("option_actief");
			});

			if(detect_mobile()) {
				//
				// Chosen voor mobiele apparaten
				//

				// Chosen: bestemming
				$("#zoekblok_field_bestemming").chosen({disable_search_threshold:20, search_contains:true, allow_single_deselect: true, autofocus_search_field: false});

				// Chosen-vormgeving toepassen op selectvelden
				$("#zoekblok select").not(".flexibel_datum").addClass("zoekblok_select_mobile");

				// kalender ietsje verplaatsen
				$(".zoekblok_aankomstdatum_calendar").css("margin-bottom","-3px");

				$("#zoekblok select").not(".flexibel_datum").find("option:first").html($("#zoekblok .zoekblok_aantalpersonen select").data("placeholder"));

				// pijltje naar beneden: verbergen bij Android (want: staat er al via de "select")
				if(navigator.userAgent.match(/Android/i) !== null) {
					$(".zoekblok_select_mobile").addClass("zoekblok_select_mobile_android");
				} else {

					// placeholder-tekst bij "bestemming" iets verkleinen
					$(".chzn-container-single .chzn-single span").css("font-size","0.85em");

				}

			} else {
				//
				// Chosen voor desktop-bezoekers
				//

				// Chosen: bestemming
				$("#zoekblok_field_bestemming").chosen({disable_search_threshold:20, search_contains:true, allow_single_deselect: true, autofocus_search_field: true});

				// Chosen: leverancier (incl. deselect-functie)
				$("select[name=lev]").chosen({allow_single_deselect: true, search_contains:true, autofocus_search_field: true});

				// Chosen: diverse selects (zonder tekstzoeken)
				$(".zoekblok_aankomstdatum select, .zoekblok_aantalpersonen select, .zoekblok_aantalslaapkamers select, .zoekblok_verblijfsduur select").chosen({disable_search: true,allow_single_deselect: true});
				$(".zoekblok_aankomstdatum .chzn-search, .zoekblok_aantalpersonen .chzn-search, .zoekblok_aantalslaapkamers .chzn-search, .zoekblok_verblijfsduur .chzn-search").hide();
			}

			// Multiple-bestemming verwerken
			$(".zoekblok_select").change(function() {
				show_ajaxloader(false);
				var nieuwe_waarde="";
				var al_gehad = [];
				if($("input[name=fsg]").val().length>0) {
					nieuwe_waarde+=","+$("input[name=fsg]").val();
					al_gehad = $("input[name=fsg]").val().split(",");
				}
				if($("select[name=fsg_invoer]").val().length>0) {
					var in_array=$.inArray($("select[name=fsg_invoer]").val(),al_gehad);
					if(in_array>-1) {

					} else {
						nieuwe_waarde+=","+$("select[name=fsg_invoer]").val();
					}
				}
				if(nieuwe_waarde) {
					nieuwe_waarde=nieuwe_waarde.substr(1);
				}
				$("input[name=fsg]").val(nieuwe_waarde);
				zoekblok_submit();
			});

			// Multiple-bestemming wissen
			$(".zoekblok_bestemming_actief_item a").click(function(){
				var nieuwe_waarde="";
				var te_wissen_waarde=$(this).attr("rel");
				var actief = $("input[name=fsg]").val().split(",");
				$.each(actief, function(key, value) {
					if(te_wissen_waarde!=value) {
						nieuwe_waarde+=","+value;
					}
				});

				if(nieuwe_waarde) {
					nieuwe_waarde=nieuwe_waarde.substr(1);
				}
				$("input[name=fsg]").val(nieuwe_waarde);
				zoekblok_submit();
				return false;
			});

			// placeholder fzt italics weergeven
			$("#zoekblok input[name=fzt]").focus(function(){
				if($("#zoekblok input[name=fzt]").val()==$("#zoekblok input[name=fzt]").data("placeholder")) {
					$("#zoekblok input[name=fzt]").val("");
					$("#zoekblok input[name=fzt]").removeClass("zoekblok_tekst_placeholder");
				}
			});
			$("#zoekblok input[name=fzt]").blur(function(){
				if($("#zoekblok input[name=fzt]").val()==="") {
				$("#zoekblok input[name=fzt]").addClass("zoekblok_tekst_placeholder");
					$("#zoekblok input[name=fzt]").val($("#zoekblok input[name=fzt]").data("placeholder"));
				}
			});
			if($("#zoekblok input[name=fzt]").val()===""||$("#zoekblok input[name=fzt]").val()==$("#zoekblok input[name=fzt]").data("placeholder")) {
				$("#zoekblok input[name=fzt]").addClass("zoekblok_tekst_placeholder");
				$("#zoekblok input[name=fzt]").val($("#zoekblok input[name=fzt]").data("placeholder"));
			}

			// ajaxloader tonen bij formsubmit
			$("#zoeken").submit( function() {
				if($("input[name=fzt]").val()==$("input[name=fzt]").data("placeholder")) {
					$("input[name=fzt]").val("");
				}
				show_ajaxloader(false);
			});

			// scroll-y-positie opslaan (vanuit formulier)
			$(document).on("click","#zoekblok", function() {
				$("input[name=scrolly]").val($(window).scrollTop());
				return true;
			});

			// scroll-y-positie opslaan (vanuit links)
			$("div#verfijn a, div.zoekblok_item_verfijn a, div.zoekblok_alleswissen a, div#zoekresultaten_sorteer a").click(function() {

				show_ajaxloader(true);

				var nieuwe_url=$(this).attr("href");
//				nieuwe_url = nieuwe_url.replace(/#[a-z0-9]+/,"");
				nieuwe_url=updateURLParameter(nieuwe_url,"scrolly",$(window).scrollTop());
				$(this).attr("href",nieuwe_url);
				return true;
			});

			// scroll-y-positie opslaan (vanuit zoekresultaat-klik)
			$("a.zoekresultaat, a.zoekresultaat_type").click(function() {

				show_ajaxloader(true);

				var nieuwe_url=$(this).attr("href");

				// cnt-querystrings weghalen (zijn niet meer nodig)
				// nieuwe_url = nieuwe_url.replace(/\&cnt+=[0-9]+/,"");
				// nieuwe_url = nieuwe_url.replace(/\&cnt[0-9]+=[0-9]+/,"");

				if(/scrolly%3D/.test(nieuwe_url)) {
					nieuwe_url = nieuwe_url.replace(/scrolly%3D[0-9]+/,"scrolly%3D"+$(window).scrollTop());
				} else {
					if(/%3F/.test(nieuwe_url)) {
						// back-url bevat al een vraagteken
						nieuwe_url = nieuwe_url+"%26scrolly%3D"+$(window).scrollTop();
					} else {
						// back-url bevat nog geen vraagteken
						nieuwe_url = nieuwe_url+"%3Fscrolly%3D"+$(window).scrollTop();
					}
				}

				$(this).attr("href",nieuwe_url);
				return true;
			});

			// naar de juiste positie scrollen
			if($("input[name=scrolly]").val()>0) {
				window.scrollTo(0,$("input[name=scrolly]").val());
			}

			//
			// autocomplete zoekformulier
			//
			$( "input[name=fzt]" ).autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: absolute_path+"rpc_json.php",
						dataType: "json",
						data: {
							t: 3,
							q: request.term
						},
						success: function( data ) {
							response( $.map( data.results, function( item ) {
								if(item.name!=='') {
									return {
										label: item.name,
										value: item.name
									};
								}
							}));
						}
					});
				},
				minLength: 2,
				select: function( event, ui ) {
					// waarde in input-field plaatsen
					$("input[name=fzt]").val(ui.item.value);

					// form submitten
					zoekblok_submit();
				},
				open: function() {
					$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
				},
				close: function() {
					$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
				}
			});
		}

		// zoeken binnen andere site: kijken of alle resultaten extern zijn (van Chalet.nl naar SuperSki)
		if($(".zoekresultaat_andere_site_superski").length!==0) {
			$(".zoekresultaat_andere_site_superski").each(function() {
				var alle_resultaten_zijn_extern=true;
				$(this).parent().parent().find(".zoekresultaat_type_titel").each(function() {
					if(!$(this).hasClass("zoekresultaat_andere_site_superski")) {
						alle_resultaten_zijn_extern=false;
					}
				});
				if(alle_resultaten_zijn_extern) {
					$(this).parent().parent().addClass("zoekresultaat_andere_site_superski_alle_resultaten");
				}
			});
			$(".zoekresultaat_andere_site_superski_alle_resultaten").parent().parent().find(".zoekresultaat_titel").append(' - via SuperSki.nl');
		}

		// zoeken binnen andere site: kijken of alle resultaten extern zijn (van Chalet.nl naar SuperSki)
		if($(".zoekresultaat_andere_site_chalet").length!==0) {
			$(".zoekresultaat_andere_site_chalet").each(function() {
				var alle_resultaten_zijn_extern=true;
				$(this).parent().parent().find(".zoekresultaat_type_titel").each(function() {
					if(!$(this).hasClass("zoekresultaat_andere_site_chalet")) {
						alle_resultaten_zijn_extern=false;
					}
				});
				if(alle_resultaten_zijn_extern) {
					$(this).parent().parent().addClass("zoekresultaat_andere_site_chalet_alle_resultaten");
				}
			});
			$(".zoekresultaat_andere_site_chalet_alle_resultaten").parent().parent().find(".zoekresultaat_titel").append(' - via Chalet.nl');
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
		if($(".zoekresultaat_type_aanbieding").length!==0) {

			$(".zoekresultaat_type_aanbieding").each(function() {
				if($(this).prev(".zoekresultaat_type_typenaam_aanbieding").html().length!==0) {
					var nieuwebreedte=230-10-$(this).outerWidth(true);
					$(this).prev(".zoekresultaat_type_typenaam_aanbieding").css("width",nieuwebreedte);
				}
			});
		}

		// sluiten cookie-bar
		$("#cookie_bottombar_close").click(function () {
			$("#cookie_bottombar").css("display","none");
			chalet_createCookie("cookiemelding_gelezen","1",3650);
			return false;
		});

		// sluiten opval-bar
		$("#opval_bottombar_close").click(function () {
			$("#opval_bottombar").css("display","none");
			chalet_createCookie("opvalmelding_gelezen","1",4);
			return false;
		});

		// uitgebreid zoeken in zoek-en-boek-blok: formulier verzenden
		$("#uitgebreidzoeken").click(function () {
			$("#form_zoekenboeklinks").submit();
			return false;
		});

		// hover bij klassering-regel accommodatiespagina: opacity op info-icon
		$("#toonaccommodatie_kenmerken_klassering").mouseover(function() {
			$("#toonaccommodatie_kenmerken_klassering_info").addClass("hover_opacity");
		});

		$("#toonaccommodatie_kenmerken_klassering").mouseout(function() {
			$("#toonaccommodatie_kenmerken_klassering_info").removeClass("hover_opacity");
		});

		// // nieuwsbrief 'per wanneer' tonen via openklappen
		// if($("#nieuwsbrief_per_wanneer_row").length!==0) {

		// 	// keuze tonen indien vinkje 'nieuwsbrief' aan staat
		// 	if($("#yesnonieuwsbrief").is(":checked")) {
		// 		$("#nieuwsbrief_per_wanneer_row").css("display","block");
		// 	}

		// 	// toggle bij wijzigen vinkje
		// 	$("#yesnonieuwsbrief").change(function() {
		// 		$("#nieuwsbrief_per_wanneer_row").slideToggle("slow");
		// 	});
		// }


		// CAPTCHA: invoer controleren via rpc_json
		$("input[name=captcha]").keyup(function() {
			if($(this).val().length>=5) {
				$.getJSON(absolute_path+"rpc_json.php", {"t": 10,"input":$(this).val()}, function(data) {
					if(data.ok) {
						if(data.captcha_okay) {
							$("#captcha_onjuist").hide();
							$("#captcha_juist").show();
							accommodatiemail_correct["captcha"]=true;
						} else {
							$("#captcha_juist").hide();
							$("#captcha_onjuist").show();
							accommodatiemail_correct["captcha"]=false;
						}
					}
				});
			}
		});

		// CAPTCHA reloaden
		$("#captcha_reload").click(function(event) {
			$("input[name=captcha]").val("");
			$("#captcha_onjuist").css("display","none");
			d = new Date();
			$("#captcha_img").attr("src",$("#captcha_img").attr("src")+d.getTime());
			return false;
		});


		// controleren invoer formulier accommodatiemail

		var accommodatiemail_correct=[];
		$("#message_juist").show();
		$("#accommodatiemail input, #accommodatiemail textarea").blur(function() {
			var fieldname=$(this).attr("name");
			if(fieldname!="captcha") {
				$.getJSON(absolute_path+"rpc_json.php", {"t": 11,"name":$(this).attr("name"), "input":$(this).val()}, function(data) {
					if(data.ok) {
						if(data.field_okay) {
							$("#"+fieldname+"_onjuist").hide();
							$("#"+fieldname+"_juist").show();
							accommodatiemail_correct[fieldname]=true;
						} else {
							$("#"+fieldname+"_juist").hide();
							$("#"+fieldname+"_onjuist").html(data.foutmelding);
							$("#"+fieldname+"_onjuist").show();
							accommodatiemail_correct[fieldname]=false;
						}
					}
				});
			}
		});

		// verzendbutton: klopt alles?
		$("#accommodatiemail_submit").click(function() {
			if(accommodatiemail_correct["from"]===true && accommodatiemail_correct["to"]===true && accommodatiemail_correct["captcha"]===true) {
				$("#submit_onjuist").hide();
				return true;
			} else {
				$("#submit_onjuist").show();

				setTimeout(function() {
					$("#submit_onjuist").hide();
				},3000);
				return false;
			}
		});

		// zoek-en-boek links: link selecteer bestemming
		$(".zoekenboek_invulveld_bestemming").click(function(){
			$("input[name=selb]").val("1");
			$("#form_zoekenboeklinks").submit();
			return false;
		});

		if($().chosen) {
			// zoek-en-boek homepage
			$("#form_zoekenboeklinks").submit(function(){
				show_ajaxloader(true);
			});
			if(detect_mobile()) {
				$(".zoekenboek_invulveld select").addClass("zoekblok_select_mobile_smal");
				$(".zoekenboek_invulveld select").each(function(){
					$(this).find("option:first").html($(this).data("placeholder"));
				});
			} else {
				$(".zoekenboek_invulveld select").chosen({disable_search: true,allow_single_deselect: false});
			}
		}

		// bij focus tekstzoeken: vergrootglas weghalen
		$( ".tekstzoeken" ).focus(function(){
			$(this).addClass("tekstzoeken_leeg");
		})

		//
		// autocomplete zoekformulier
		//
		$( ".tekstzoeken" ).autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: absolute_path+"rpc_json.php",
					dataType: "json",
					data: {
						t: 3,
						q: request.term
					},
					success: function( data ) {
						response( $.map( data.results, function( item ) {
							if(item.name!=='') {
								return {
									label: item.name,
									value: item.name
								};
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				// waarde in input-field plaatsen
				$("input[name=fzt]").val(ui.item.value);

				// form submitten
				// zoekblok_submit();
			},
			open: function() {
				$(this).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
				$(this).autocomplete('widget').css('z-index', 100);
			},
			close: function() {
				$(this).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}
		});
	}
});

function chalet_createCookie(name,value,days) {
	// functie om eenvoudig cookies te plaatsen
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function chalet_getCookie(c_name) {
	// functie om eenvoudig cookies uit te lezen
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++) {
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name) {
			return unescape(y);
		}
	}
}

function weektarieven_openklappen() {
	$('#weektarieven').slideDown('slow','linear', function() {
		// workaround bug in IE9 (tarieventabel 'groeit' bij onmouseover op de tarieven)
		$(".tarieventabel_div").css("height",$(".tarieventabel_div").height());
	});
	$('#bekijkookweektarieven').css('visibility','hidden');
	return false;
}

function favorieten_opslaan_verwijderen(begincode, typeid, action) {
	// functie om favorieten op te slaan en te verwijderen
	$.getJSON(absolute_path+"rpc_json.php", {
	"t": 4,
	"typeid": typeid,
	"action": action
	}, function(data) {
		if(data.ok) {
			$("#favorietenaantal").html(data.aantal);
			if(action=="insert") {
				// plaats en verwijderbuttons uit/aanzetten
				$("#favadd").css("display","none");
				$("#favremove").css("display","block");
				if(data.aantal==1) {
					// popup tonen
					if(chalet_getCookie("favorietenpopup")!="1"||lokale_testserver) {
						$("#favorieten_popup").css("display","inline");
						chalet_createCookie("favorietenpopup","1",3650);
					}
				} else {
//					$("#favorietenaantal").parent("a").css("background-color","yellow");
//					$("#favorietenaantal").parent("a").delay(1000).css("background-color","white");
				}
				var google_analytics_event="favoriet toevoegen";
			} else if(action=="delete") {
				// plaats en verwijderbuttons aan/uitzetten
				$("#favremove").css("display","none");
				$("#favadd").css("display","block");

				if($("#fav_table_"+typeid).length!==0) {
					// favorietenpagina: fadeOut van het accommodatieblok
					$("#fav_table_"+typeid).fadeTo("slow",0,function() {
						$("#fav_table_"+typeid).slideUp("normal", function() {
							if(data.aantal===0) {
								// indien pagina hierna leeg is: herladen (zodat melding getoond kan worden)
								location.reload();
							}
						});
					});
				}
				var google_analytics_event="favoriet verwijderen";
			}
			if (typeof _gaq != "undefined") {
				_gaq.push(['_trackEvent', 'bezoekers-acties', google_analytics_event, begincode+typeid]);
			}
		}
	});
	return false;
}

function zoekopdracht_naar_analytics_sturen(omschrijving,zoekopdracht) {
	if (typeof _gaq != "undefined") {
		_gaq.push(['_trackEvent', 'zoekfunctie', omschrijving, zoekopdracht]);
	}
}

// zaken die pas na het laden van alles uitgevoerd moeten worden:
$(document).ready(function() {

	if(gebruik_jquery===true) {

		// zoekblok pas tonen als alles klaar is
		$("#zoekblok").css("visibility","visible");

		// indien _GET["selb"]==1 : bestemming-pulldown openklappen
		if(location.href.indexOf("&selb=1") > -1) {
			$('#zoekblok_field_bestemming').trigger('liszt:open');
		// alert('ja');
		}

		if($("#zoekblok").length!==0 && parseInt($("div.datadiv").data("nieuwezoekopdracht"),10)==1 && $("body").attr("id")=="body_zoek-en-boek" && $("div.zoekblok_zoek_alleen_aanbiedingen").length==0) {

			//
			// zoekopdracht naar Google Analytics sturen
			//

			var analytics_complete_zoekopdracht='';
			var analytics_bestemming='';
			var analytics_aankomstdatum='';
			var analytics_verblijfsduur='';
			var analytics_aantalpersonen='';
			var analytics_aantalslaapkamers='';
			var analytics_tekstzoeken='';
			var analytics_verfijnen='';
			var analytics_verfijnen_item='';
			var analytics_url='';

			// bestemming
			if($("div.zoekblok_bestemming_actief_item").length!==0) {

				$("div.zoekblok_bestemming_actief_item").each(function() {
					if($(this).data("bestemming_actief_name").length!==0) {
						if(analytics_bestemming) {
							analytics_bestemming=analytics_bestemming+", ";
						}
						analytics_bestemming=analytics_bestemming+$(this).data("bestemming_actief_name");
					}
				});

				if(analytics_bestemming) {
					zoekopdracht_naar_analytics_sturen("bestemming",analytics_bestemming);
					analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Bestemming: "+analytics_bestemming;
				}
			}

			// aankomstdatum: select
			if($("select[name=fad]").length!==0 && $("select[name=fad]").val()>0) {
				analytics_aankomstdatum=$("select[name=fad] option:selected").text();

				zoekopdracht_naar_analytics_sturen("aankomstdatum",analytics_aankomstdatum);
				analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Aankomstdatum: "+analytics_aankomstdatum;
			}

			// aankomstdatum: d/m/y
			if($("select[name=fadf_d]").length!==0 && $("select[name=fadf_d]").val()>0 && $("select[name=fadf_m]").val()>0 && $("select[name=fadf_y]").val()>0) {
				analytics_aankomstdatum=$("select[name=fadf_d] option:selected").text()+" "+$("select[name=fadf_m] option:selected").text()+" "+$("select[name=fadf_y] option:selected").text();

				zoekopdracht_naar_analytics_sturen("aankomstdatum",analytics_aankomstdatum);
				analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Aankomstdatum: "+analytics_aankomstdatum;
			}

			// verblijfsduur
			if($("select[name=fdu]").length!==0) {
				analytics_verblijfsduur=$("select[name=fdu] option:selected").text();

				if(analytics_verblijfsduur) {
					zoekopdracht_naar_analytics_sturen("verblijfsduur",analytics_verblijfsduur);
					analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Verblijfsduur: "+analytics_verblijfsduur;
				}
			}

			// aantal personen
			if($("select[name=fap]").length!==0 && $("select[name=fap]").val()>0) {
				analytics_aantalpersonen=$("select[name=fap] option:selected").text();

				zoekopdracht_naar_analytics_sturen("aantal personen",analytics_aantalpersonen);
				analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Aantal personen: "+analytics_aantalpersonen;
			}

			// aantal slaapkamers
			if($("select[name=fas]").length!==0 && $("select[name=fas]").val()>0) {
				analytics_aantalslaapkamers=$("select[name=fas] option:selected").text();

				zoekopdracht_naar_analytics_sturen("aantal slaapkamers",analytics_aantalslaapkamers);
				analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Aantal slaapkamers: "+analytics_aantalslaapkamers;
			}

			// tekstzoeken
			if($("input[name=fzt]").length!==0 && $("input[name=fzt]").val()!=="" && $("input[name=fzt]").val()!==$("input[name=fzt]").data("placeholder")) {
				analytics_tekstzoeken=$("input[name=fzt]").val();

				analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Tekstzoeken: "+analytics_tekstzoeken;
			}

			// verfijnen: categorie
			if($("div.zoekblok_verfijn_actief_categorie").length!==0) {
				$("div.zoekblok_verfijn_actief_categorie").each(function() {
					zoekopdracht_naar_analytics_sturen('verfijn-categorie',$(this).data("verfijn-categorie"));
				});
			}

			// verfijnen: items
			if($("div.zoekblok_verfijn_actief_item").length!==0) {
				$("div.zoekblok_verfijn_actief_item").each(function() {

					analytics_verfijnen_item=$(this).parent().parent().find("div.zoekblok_verfijn_actief_categorie").data("verfijn-categorie")+": "+$(this).data("verfijn-item");
					if(analytics_verfijnen) {
						analytics_verfijnen=analytics_verfijnen+", ";
					}
					analytics_verfijnen=analytics_verfijnen+analytics_verfijnen_item;
					zoekopdracht_naar_analytics_sturen('verfijnen',analytics_verfijnen_item);
				});
				if(analytics_verfijnen) {
					analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - "+analytics_verfijnen;
				}
			}

			// complete zoekopdracht: ' - ' weghalen
			if(analytics_complete_zoekopdracht) {
				analytics_complete_zoekopdracht=analytics_complete_zoekopdracht.substr(3);

				// complete zoekopdracht naar Analytics sturen
				zoekopdracht_naar_analytics_sturen("complete zoekopdracht",analytics_complete_zoekopdracht);

			} else {
				analytics_complete_zoekopdracht="overal 'geen voorkeur'";
			}

			// geen zoekresultaten
			if($("div#geenresultaten").length!==0) {
				zoekopdracht_naar_analytics_sturen("zoekopdracht zonder resultaten",analytics_complete_zoekopdracht);

				analytics_url=document.location.href;
				analytics_url=analytics_url.replace(/scrolly=[0-9]+&/,"");
				zoekopdracht_naar_analytics_sturen("zoekopdracht zonder resultaten - URL",analytics_url);
			}

			// referer
			if($("div.datadiv").data("referer_zoekenboek")=="1") {
				zoekopdracht_naar_analytics_sturen("eerst gebruikte zoekformulier","zoek-en-boek-pagina");
			} else if($("div.datadiv").data("referer_zoekenboek")=="2") {
				zoekopdracht_naar_analytics_sturen("eerst gebruikte zoekformulier","zoek-en-boek-blokje links");
			}
		}
	}
});


