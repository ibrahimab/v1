
var ZoomlevelBepalen = new function() {

	var OFFSET = 268435456;
	var RADIUS = OFFSET / Math.PI;

	/**
	 * Gets the minimum zoom level that entirely contains the Lat/Lon bounding rectangle given.
	 *
	 * @param {google.maps.LatLngBounds} boundary the Lat/Lon bounding rectangle to be contained
	 * @param {number} mapWidth the width of the map in pixels
	 * @param {number} mapHeight the height of the map in pixels
	 * @return {number} the minimum zoom level that entirely contains the given Lat/Lon rectangle boundary
	 */
	this.getMinimumZoomLevelContainingBounds = function ( boundary, mapWidth, mapHeight ) {

		var zoomIndependentSouthWestPoint = latLonToZoomLevelIndependentPoint( boundary.getSouthWest() );
		var zoomIndependentNorthEastPoint = latLonToZoomLevelIndependentPoint( boundary.getNorthEast() );
		var zoomIndependentNorthWestPoint = { x: zoomIndependentSouthWestPoint.x, y: zoomIndependentNorthEastPoint.y };
		var zoomIndependentSouthEastPoint = { x: zoomIndependentNorthEastPoint.x, y: zoomIndependentSouthWestPoint.y };
		var zoomLevelDependentSouthEast, zoomLevelDependentNorthWest, zoomLevelWidth, zoomLevelHeight;
		for( var zoom = 21; zoom >= 0; --zoom ) {
			zoomLevelDependentSouthEast = zoomLevelIndependentPointToMapCanvasPoint( zoomIndependentSouthEastPoint, zoom );
			zoomLevelDependentNorthWest = zoomLevelIndependentPointToMapCanvasPoint( zoomIndependentNorthWestPoint, zoom );
			zoomLevelWidth = zoomLevelDependentSouthEast.x - zoomLevelDependentNorthWest.x;
			zoomLevelHeight = zoomLevelDependentSouthEast.y - zoomLevelDependentNorthWest.y;
			if( zoomLevelWidth <= mapWidth && zoomLevelHeight <= mapHeight )
				return zoom;
		}
		return 0;
	};

	function latLonToZoomLevelIndependentPoint ( latLon ) {
		return { x: lonToX( latLon.lng() ), y: latToY( latLon.lat() ) };
	}

	function zoomLevelIndependentPointToMapCanvasPoint ( point, zoomLevel ) {
		return {
			x: zoomLevelIndependentCoordinateToMapCanvasCoordinate( point.x, zoomLevel ),
			y: zoomLevelIndependentCoordinateToMapCanvasCoordinate( point.y, zoomLevel )
		};
	}

	function zoomLevelIndependentCoordinateToMapCanvasCoordinate ( coordinate, zoomLevel ) {
		return coordinate >> ( 21 - zoomLevel );
	}

	function latToY ( lat ) {
		return OFFSET - RADIUS * Math.log( ( 1 + Math.sin( lat * Math.PI / 180 ) ) / ( 1 - Math.sin( lat * Math.PI / 180 ) ) ) / 2;
	}

	function lonToX ( lon ) {
		return OFFSET + RADIUS * lon * Math.PI / 180;
	}

};

function switch_website($mode) {
	chalet_createCookie("siteVer",$mode,1);
	window.location.href = "/";
	return false;
}

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

var mapDefaults = {
	googlemaps_lat: 0,
	googlemaps_long: 0,
	zoomlevel: 11,
	gps_coordinaten_bekend: 0,
	googlemaps_selected_icon: 1,
	googlemaps_init: false,
	googlemaps_base: '/',
	googlemaps_naam: '',
	googlemaps_plaatsland: '',
	googlemaps_aantalpersonen: '',
	googlemaps_afbeelding: '',
	googlemaps_icon: {
		1: 'chalet.png',
		2: 'chalet.png',
		3: 'zomerhuisje.png',
		4: 'chalet.png',
		6: 'vallandry.png',
		7: 'italissima.png',
		71: 'italissima_plaats.png',
		8: 'superski.png',
		9: 'venturasol.png'
	},
	googlemaps_icon_ander: {
		1: 'chaletander.png',
		2: 'chaletander.png',
		3: 'zomerhuisjeander.png',
		4: 'chaletander.png',
		6: 'vallandryander.png',
		7: 'italissimaander.png',
		71: 'italissima_plaatsander.png',
		8: 'superskiander.png',
		9: 'venturasolander.png'
	},
	googlemaps_zoekenboek: false,
	googlemaps_marker: [],
	googlemaps_marker_2: [],
	googlemaps_marker_id: 0,
	googlemaps_counter: 0,
	googlemaps_skigebiedid: 0,
	googlemaps_kwaliteit: '',
	googlemaps_omschrijving: '',
	googlemaps_tarief: 0,
	googlemaps_infowindow: null
};

var map_back_url='';
var zoekblok_tekst='';
var lokale_testserver=false;
var absolute_path='/';
if(location.href.indexOf("/chalet/")>1) {
	var absolute_path='/chalet/';
	var lokale_testserver=true;
}
if(typeof isMobile != "undefined" && isMobile == true) {
	var $isMobile = true;
} else {
	var $isMobile = false;
}

function initialize_googlemaps(mapSettings) {

	mapOptions = $.extend( {}, mapDefaults, mapSettings );

	//http://code.google.com/intl/nl/apis/maps/documentation/javascript/reference.html
	if(mapOptions.gps_coordinaten_bekend==1) {

		// If it is search page, set default zoom
		if(typeof mapOptions.googlemaps_zoekenboek == "undefined"){
			mapOptions.googlemaps_zoekenboek = false;
		} else if(mapOptions.googlemaps_zoekenboek != false) {
			var bound=new google.maps.LatLngBounds(new google.maps.LatLng(mapOptions.googlemaps_lat_min,mapOptions.googlemaps_long_min),new google.maps.LatLng(mapOptions.googlemaps_lat_max,mapOptions.googlemaps_long_max));
			mapOptions.zoomlevel=ZoomlevelBepalen.getMinimumZoomLevelContainingBounds(bound,670,380);
			if(mapOptions.zoomlevel>10) mapOptions.zoomlevel=10;
		}

		if(typeof mapOptions.googlemaps_skigebiedid == "undefined") {
			mapOptions.googlemaps_skigebiedid=false;
		}
		var mapCookie = chalet_getCookie("map_regio");
		// Check to see if we should restore map on initial state
		if(typeof mapCookie != "undefined") {
			mapCookie = JSON.parse(mapCookie);

			var pageUrl = location.href.split('?')[0];
			pageUrl = decodeURIComponent(pageUrl.replace(location.hash,""));

			// Applies on search and region pages
			if((mapOptions.googlemaps_skigebiedid>0 || mapOptions.googlemaps_zoekenboek) && (pageUrl==mapCookie.url || pageUrl==mapCookie.url.split('?')[0])) {

				// previous zoom information
				if(mapCookie.z) {
					mapOptions.zoomlevel=parseInt(mapCookie.z,10);
				}

				// previous latitude and longitude information
				if(mapCookie.lt && mapCookie.lg) {
					mapOptions.googlemaps_lat=mapCookie.lt;
					mapOptions.googlemaps_long=mapCookie.lg;
				}

				// previous marker information
				if(mapCookie.m) {
					mapOptions.googlemaps_marker_id = mapCookie.m;
				}

				// Delete the cookie information
				chalet_createCookie("map_regio", "", -1);

			} else if(mapOptions.googlemaps_skigebiedid===0 && pageUrl==mapCookie.url) {
				// Applies on accommodation page only
				var fromCookie = true;
			}
		}

		// Default map options
		var myOptions = {
			zoom: mapOptions.zoomlevel,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scaleControl: true,
			panControl: true,
			overviewMapControl: true,
			streetViewControl: true,
			mapTypeControl: true,
			zoomControl: true,
			scrollwheel: false,
			center: new google.maps.LatLng(mapOptions.googlemaps_lat,mapOptions.googlemaps_long)
		};
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		// Accommodation page only
		if(!mapOptions.googlemaps_zoekenboek && !mapOptions.googlemaps_init && mapOptions.googlemaps_skigebiedid===0) {

			// set marker and check if other accommodations should be displayed as well
			var value = [];
			value[1]=mapOptions.googlemaps_lat;
			value[2]=mapOptions.googlemaps_long;
			value['naamhtml']=mapOptions.googlemaps_naam;
			value['plaatsland']=mapOptions.googlemaps_plaatsland;
			value['aantalpersonen']=mapOptions.googlemaps_aantalpersonen;
			value['afbeelding']=mapOptions.googlemaps_afbeelding;
			value['kwaliteit']=mapOptions.googlemaps_kwaliteit;
			value['omschrijving']=mapOptions.googlemaps_omschrijving;
			value['tarief']=mapOptions.googlemaps_tarief;

			googlemaps_createmarker(value,true,true);

			$("#googlemaps_other").css("visibility","visible");
			mapOptions.googlemaps_init=true;

			// If it is a return action in the accommodation page
			if(fromCookie == true) {
				// previous marker information
				if(mapCookie.m) {
					mapOptions.googlemaps_marker_id = mapCookie.m;
				}

				// display other accommodations
				googlemaps_show_other_acc();

				map.setCenter(new google.maps.LatLng(mapCookie.lt,mapCookie.lg));
				map.setZoom(mapCookie.z);

				// Delete the cookie information
				chalet_createCookie("map_regio", "", -1);
			}
		}

		// Search page only
		if(mapOptions.googlemaps_zoekenboek) {
			// markers op basis van zoekresultaten plaatsen
			$("div.data_zoeken_op_kaart").each(function() {
				var value = [];
				value[1]=$(this).data("gps_lat");
				value[2]=$(this).data("gps_long");
				value["naamhtml"]=$(this).data("naamhtml");
				value["plaatsland"]=$(this).data("plaatsland");
				value["aantalpersonen"]=$(this).data("aantalpersonen");
				value["afbeelding"]=$(this).data("afbeelding");
				value["tarief"]=$(this).data("tarief");
				value["url"]=$(this).data("url");
				value["kwaliteit"]=$(this).data("kwaliteit");
				value["omschrijving"]=$(this).data("omschrijving");
				value["key"]=$(this).data("key");

				googlemaps_createmarker(value,true,false);
			});

		}

		// region page only
		if(mapOptions.googlemaps_skigebiedid>0) {
			// Italissima: plaatsen op de kaart zetten
			googlemaps_show_region_acc();
		}
	}
}

function googlemaps_createmarker(value,main,use_animation) {

	mapOptions.googlemaps_counter=mapOptions.googlemaps_counter+1;
	var myLatLng = new google.maps.LatLng(value[1],value[2]);

	var animation;
	if(use_animation) {
		animation="google.maps.Animation.DROP";
	} else {
		animation="";
	}
	var marker = mapOptions.googlemaps_marker[mapOptions.googlemaps_counter];
	marker = new google.maps.Marker({
		map: map,
		position: myLatLng,
		draggable: false,
		icon: mapOptions.googlemaps_base+'pic/googlemapsicons/'+(main ? mapOptions.googlemaps_icon[mapOptions.googlemaps_selected_icon] : mapOptions.googlemaps_icon_ander[mapOptions.googlemaps_selected_icon]),
		animation: animation,
		title: value['naam'],
		zIndex: (main ? 1000 : mapOptions.googlemaps_counter)
	});

	mapOptions.googlemaps_marker_2.push(marker);
	google.maps.event.addListener(marker, 'click', function(event) {
		if(mapOptions.googlemaps_infowindow) mapOptions.googlemaps_infowindow.close();

		mapOptions.googlemaps_infowindow = new google.maps.InfoWindow({maxWidth: 400, minHeight: 400});
		mapOptions.googlemaps_infowindow.open(map, marker);
		mapOptions.googlemaps_infowindow.setContent(googlemaps_marker_content(value));
	});

	if(mapOptions.googlemaps_marker_id) {
		if(value['key'] == mapOptions.googlemaps_marker_id) {
			mapOptions.googlemaps_infowindow = new google.maps.InfoWindow({
				content: googlemaps_marker_content(value)
			});
			setTimeout(function(){mapOptions.googlemaps_infowindow.open(map,marker);}, 1000);
		}
	}
}

function googlemaps_marker_content(value) {
	var tmpcontent="";
	// Bekijk alle resultaten op een kaart
	tmpcontent=	'<div class="googlemaps_infowindow clearfix">';
	if(value['afbeelding']) tmpcontent+='<div class="ileft">';
	tmpcontent+='<p>';
	if(value['url']) tmpcontent+='<a href="'+value['url']+'" data-marker="'+value['key']+'" onclick="return map_zoek_en_boek_click(this);">';
	if(value['naamhtml']) tmpcontent+='<b>'+value['naamhtml']+'</b>';
	if(value['url']) tmpcontent+='</a>&nbsp;&nbsp;&nbsp;';
	if(value['plaatsland']) tmpcontent+='<br />'+value['plaatsland'];
	if(value['aantalpersonen']) tmpcontent+='<br />'+value['aantalpersonen'];
	if(value['tarief']) tmpcontent+='<br />'+value['tarief'];
	if(value['kwaliteit']) {
		var w = 17 * value['kwaliteit'];
		tmpcontent+='<br /><span class="sterren" style="width:'+w+'px;">&nbsp;</span>';
	}
	if(value['omschrijving']) tmpcontent+='<br /><br />'+value['omschrijving'];
	tmpcontent+='</p>';
	if(value['afbeelding']) tmpcontent+='</div>';

	if(value['afbeelding']) tmpcontent+='<div class="iright"><img src="'+value['afbeelding']+'"></div>';
	tmpcontent+='<div class="clear">&nbsp;</div>';
	tmpcontent+='</div>';

	var c = $(tmpcontent);
	return c[0];
}

function googlemaps_deletemarkers() {
	for (var i = 1; i < mapOptions.googlemaps_marker_2.length; i++) {
		mapOptions.googlemaps_marker_2[i].setMap(null);
	}
	$("#googlemaps_deleteother").css("visibility","hidden");
	$("#googlemaps_other").css("visibility","visible");
}

function googlemaps_show_region_acc() {
	$.getJSON(mapOptions.googlemaps_base+"rpc_json.php", {
		"t": 1,
		"skigebiedid": mapOptions.googlemaps_skigebiedid
	}, function(data) {
		if(data.ok&&data.aantal>0) {
			$.each(data.acc, function(key, value) {
				value.key = key;
				if(value.skigebied_id == mapOptions.googlemaps_skigebiedid) {
					googlemaps_createmarker(value,true,true);
				} else {
					googlemaps_createmarker(value,false,true);
				}
			});
		}
	});
}

function googlemaps_show_other_acc() {
	if(mapOptions.googlemaps_init) {
		for (var i = 1; i < mapOptions.googlemaps_marker_2.length; i++) {
			mapOptions.googlemaps_marker_2[i].setMap(null);
		}
		$.getJSON(mapOptions.googlemaps_base+"rpc_json.php", {
			"t": 1,
			"accid":mapOptions.googlemaps_accid,
			"typeid":mapOptions.googlemaps_typeid
		}, function(data) {
			if(data.ok&&data.aantal>0) {
				$.each(data.acc, function(key, value) {
					value.key = key;
					googlemaps_createmarker(value,false,true);
				});
				$("#googlemaps_deleteother").css("visibility","visible");
				$("#googlemaps_other").css("visibility","hidden");
			}
		});
	}
}


function recordOutboundPopup(category, action) {
	try {
		if (typeof ga != "undefined") {
			ga('send', 'event', category, action);
		}
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

/**
 * Remove window.location hash without affecting the scrolling position
 */
function removeHash () {
	var scrollV, scrollH, loc = window.location;
	if ("replaceState" in history)
		history.replaceState("", document.title, loc.pathname + loc.search);
	else {
		var docElem = document.documentElement ? document.documentElement : document.body;
		// Prevent scrolling by storing the page's current scroll offset
		scrollV = docElem.scrollTop;
		scrollH = docElem.scrollLeft;

		loc.hash = "";

		// Restore the scroll offset, should be flicker free
		docElem.scrollTop = scrollV;
		docElem.scrollLeft = scrollH;
	}
}

var tonen_of_niet;
var form_gewijzigd;
var selectbox_actief=0;
var showhidelink_text='';
var gebruik_jquery=true;
var landkaartklikbaar_info_hoverkleur="#636f07";
var eerste_tab_getoond=false;
var google_analytics_tab_verstuurd=false;
var maxHeight = 0;
var zoek_en_boek_links_active = true;


$(document).ready(function() {
	setTimeout(function() {
		if(typeof ReadyEvent != 'undefined') ReadyEvent.trigger('PushEvent');
	}, 500);
	if($("body").data("onload")) {
		var functionName = $("body").data("onload");
		if(functionName == 'initialize_googlemaps' && typeof mapOptions != "undefined") {
			initialize_googlemaps(mapOptions);
		}
	}

        if($(".wtform_error").scrollTop() != null) {

                    $("#hide_form").css("display", "block");


                    position =  $(".wtform_error").offset().top;

                    $("html, body").animate({ scrollTop: position }, 600,'swing');
      }

	if(gebruik_jquery===true) {
		//
		// jquery
		//


		// "Alle types van deze accommodatie" elk blokje dezelfde hoogte
		$("div.alletypes_typenaam").each(function() {
                        $(this).parent().parent().parent().parent().show();
			maxHeight = Math.max(maxHeight, $(this).height());
                        $(this).parent().parent().parent().parent().hide();
		}).height(maxHeight);


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
					if (typeof ga != "undefined") {

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
							ga('send', 'pageview', canonical_link);
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
				'transitionIn'	:	(($isMobile)?'none':'elastic'),
				'transitionOut'	:	(($isMobile)?'none':'elastic'),
				'speedIn'		: 300,
				'speedOut'		: 300,
				'padding' :		0,
				'margin' :		(($isMobile)?20:0),
				'autoScale' :		true,
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'autoDimensions': true,
				'onComplete'	:	function() {

				},
				'cyclic'	:	true,
				'overlayOpacity' :	0.8
			});

			// beoordeling-popup via fancybox
			$("#beoordeling_link").fancybox({
				'type'			: 'inline',
				'width'			: (($isMobile)?'auto':830),
				'height'		: (($isMobile)?'auto':540),
				'autoDimensions': false,
				'autoScale'		: false,
				'transitionIn'	:	(($isMobile)?'none':'elastic'),
				'transitionOut'	:	(($isMobile)?'none':'elastic'),
				'speedIn'		: 300,
				'speedOut'		: 300,
				'padding' :		0,
				'margin' :		(($isMobile)?20:0),
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'onComplete'	:	function() {
                                            $.fancybox.resize();
				},
				'overlayOpacity' :	0.8
			});

			// popup_fancybox via fancybox
			$(".popup_fancybox").fancybox({
				'type'			: 'inline',
				'width'			: (($isMobile)?'auto':430),
				'height'		: (($isMobile)?'auto':240),
				'autoDimensions': false,
				'autoScale'		: false,
				'transitionIn'	:	(($isMobile)?'none':'elastic'),
				'transitionOut'	:	(($isMobile)?'none':'elastic'),
				'speedIn'		: 300,
				'speedOut'		: 300,
				'padding' :		0,
				'margin' :		(($isMobile)?20:0),
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'onComplete'	:	function() {
                                 $.fancybox.resize();
                                $(window).unbind("resize.fb scroll.fb");
				},
				'overlayOpacity' :	0.8
			});

			// vimeo_fancybox via fancybox
			$(".vimeo_fancybox").fancybox({
				'type'			:   'iframe',
				'transitionIn'	:	(($isMobile)?'none':'elastic'),
				'transitionOut'	:	(($isMobile)?'none':'elastic'),
				'width'			: (($isMobile)?'300':900),
				'height'		: (($isMobile)?'100':510),
				'speedIn'		: 300,
				'speedOut'		: 300,
				'padding' :		0,
				'margin' :		(($isMobile)?20:0),
				'autoScale' :		(($isMobile)?true:false),
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'onComplete'	:	function() {

				},
				'cyclic'	:	true,
				'overlayOpacity' :	0.8

			});

			$(".fotopopup_border").fancybox({
				'transitionIn'	:	(($isMobile)?'none':'elastic'),
				'transitionOut'	:	(($isMobile)?'none':'elastic'),
				'speedIn'		: 300,
				'speedOut'		: 300,
				'autoScale' :		true,
				'overlayShow'	:	true,
				'hideOnContentClick' :	true,
				'overlayColor' :	'#454545',
				'onComplete'	:	function() {

				},
				'cyclic'	:	true,
				'overlayOpacity' :	0.8
			});

			// klikken op foto's op overzicht-tab: fancybox openen
			$(".fotopopup_tab_overzicht").click(function() {
				$(".fotopopup[data-teller="+$(this).data("teller")+"]").trigger("click");
				return false;
			});

			// foto's op overzicht-tab zonder corresponderende popup: "a href" verwijderen
			if($(".fotopopup_tab_overzicht").length!==0) {
				$(".fotopopup_tab_overzicht").each(function(){
					var fotopopup_tab_overzicht=this;
					if($(".fotopopup[data-teller="+$(this).data("teller")+"]").length===0) {
						$(fotopopup_tab_overzicht).removeClass("fotopopup_tab_overzicht");
						// $(fotopopup_tab_overzicht).remove();
						$(fotopopup_tab_overzicht).replaceWith(function(){
							return $("<span>" + $(this).html() + "</span>");
						});
					}
				});
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
			if (typeof ga != "undefined") {
				ga('send', 'social', 'facebook', 'share', '');
			}
			return false;
		});

		// Twitter share-window
		$(".twitter_share_window").click( function() {
			popwindowXY(700,450,$(this).attr("href"),true);
			if (typeof ga != "undefined") {
				ga('send', 'social', 'twitter', 'share', '');
			}
			return false;
		});

		// Google+ share-window
		$(".googleplus_share_window").click( function() {
			popwindowXY(700,450,$(this).attr("href"),true);
			if (typeof ga != "undefined") {
				ga('send', 'social', 'googleplus', 'share', '');
			}
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

			$("#verfijn .alle_selecties_wissen").hover(
				function () {
					$(".keuzeopsomming").addClass("keuzeopsomming_tijdelijk_leeg");
				},
				function () {
					$(".keuzeopsomming").removeClass("keuzeopsomming_tijdelijk_leeg");
				}
			);


			// bij focus op tekstzoeken:
			$("#zoekblok input[name=fzt]").focus(function() {
				if($("#zoekblok input[name=fzt]").val()!=$("#zoekblok input[name=fzt]").data("placeholder")) {
					zoekblok_tekst=$("#zoekblok input[name=fzt]").val();
				}
			});

			// na blur op tekstzoeken: form submit
			$("#zoekblok input[name=fzt]").blur(function() {
				if(zoekblok_tekst!=$("#zoekblok input[name=fzt]").val()) {
					setTimeout(function(){
						zoekblok_submit();
					},300);
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
				$("select[name=fsg_invoer] > option[value="+$(this).data("bestemming_actief_value")+"]").addClass("option_actief");
			});

			if(detect_mobile()) {

				// extra ruimte bij verfijnblok voor touch-devices
				$("#verfijn .keuzeopsomming").addClass("keuzeopsomming_touchdevices");


				//
				// Chosen voor mobiele apparaten
				//
 				if($("div.datadiv").data("websitetype")=="7") {
					// Italissima: zoeken via typen niet mogelijk
					$("#zoekblok_field_bestemming").chosen({width: "100%", disable_search:20, allow_single_deselect: true});

				} else {
					$("#zoekblok_field_bestemming").chosen({width: "100%", disable_search_threshold:20, search_contains:true, allow_single_deselect: true, autofocus_search_field: true});
				}
                                // Chosen-vormgeving toepassen op selectvelden
				$("#zoekblok select").addClass("zoekblok_select_mobile");

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
                                //
				// Chosen: bestemming
				if($("div.datadiv").data("websitetype")=="7") {
					// Italissima: zoeken via typen niet mogelijk
					$("#zoekblok_field_bestemming").chosen({width: "100%", disable_search:20, allow_single_deselect: true});
				} else {
					$("#zoekblok_field_bestemming").chosen({width: "100%", disable_search_threshold:20, search_contains:true, allow_single_deselect: true, autofocus_search_field: true});
				}

				// Chosen: leverancier (incl. deselect-functie)
				$("select[name=lev]").chosen({width: "100%", allow_single_deselect: true, search_contains:true, autofocus_search_field: true});

				// Chosen: diverse selects (zonder tekstzoeken)
				$(".zoekblok_aankomstdatum select, .zoekblok_aantalpersonen select, .zoekblok_aantalslaapkamers select, .zoekblok_verblijfsduur select").chosen({width: "100%", disable_search: true,allow_single_deselect: true});
				$(".zoekblok_aankomstdatum .chzn-search, .zoekblok_aantalpersonen .chzn-search, .zoekblok_aantalslaapkamers .chzn-search, .zoekblok_verblijfsduur .chzn-search").hide();
			}


			if($().noUiSlider) {
				//
				// Prijsklasse-slider
				//

				var slider_active=false;
				var slider_mouseactive=false;
				var slider_timer;

				// klikken met muis (of touch starten): form niet automatisch submitten
				$(document).bind("touchstart mousedown", function(event) {
					slider_mouseactive = true;
				});

				// muis loslaten (of touch loslaten): form automatisch submitten
				$(document).bind("touchend mouseup", function(event) {
					slider_mouseactive = false;

					if(slider_active) {
						clearTimeout(slider_timer);
						$("input[name=scrolly]").val($(window).scrollTop());
						// $("input[name=fpk]").val("2");
						zoekblok_submit();
					}
				});

				// kijk of ingevoerde velden kloppen
				$("input[name=fpk_van],input[name=fpk_tot]").change(function(){
					clearTimeout(slider_timer);
					$("input[name=scrolly]").val($(window).scrollTop());
					// $("input[name=fpk]").val("2");

					if($(this).attr("name")=="fpk_van") {
						if($("input[name=fpk_tot]").val()==="") {
							$("#zoekblok_prijsklasse_enmeer").show();
							$("input[name=fpk_enmeer]").val("1");
							$("input[name=fpk_tot]").val($(".zoekblok_prijsklasse").data("max"));
						}
					}
					if($(this).attr("name")=="fpk_tot") {
						if(parseInt($("input[name=fpk_tot]").val(),10)>parseInt($(".zoekblok_prijsklasse").data("max"),10)) {
							$("input[name=fpk_tot]").val($(".zoekblok_prijsklasse").data("max"));
							$("input[name=fpk_enmeer]").val("1");
							$("#zoekblok_prijsklasse_enmeer").show();
						} else {
							$("input[name=fpk_enmeer]").val("0");
							$("#zoekblok_prijsklasse_enmeer").hide();
						}
						if($("input[name=fpk_van]").val()==="") {
							$("input[name=fpk_van]").val($(".zoekblok_prijsklasse").data("min"));
						}
					}

					zoekblok_submit();
				});

				// allow only numeric input
				$("input[name=fpk_van],input[name=fpk_tot]").keydown(function(event) {
					// Allow: backspace, delete, tab, escape, and enter
					if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || event.keyCode == 116 ||
						// Allow: Ctrl+A
						(event.keyCode == 65 && event.ctrlKey === true) ||
						// Allow: home, end, left, right
						(event.keyCode >= 35 && event.keyCode <= 39)) {
						// let it happen, don't do anything
						return;
					}
					else {
						// Ensure that it is a number and stop the keypress
						if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
							event.preventDefault();
						}
					}
				});

				var slider_max_en_meer=$(".zoekblok_prijsklasse").data("max")+$(".zoekblok_prijsklasse").data("stappen");
				var onSlide = function() {
						// if($("#fpk_tot").val()==="") {
						// 	$("input[name=fpk_enmeer]").val("1");
						// }
						if($("#fpk_tot_hidden").val()==slider_max_en_meer) {
							$("#fpk_van").val($("#fpk_van_hidden").val());
							$("#fpk_tot").val($(".zoekblok_prijsklasse").data("max"));
							$("input[name=fpk_enmeer]").val("1");
						} else {
							$("#fpk_van").val($("#fpk_van_hidden").val());
							$("#fpk_tot").val($("#fpk_tot_hidden").val());
							$("input[name=fpk_enmeer]").val("0");
						}

						// wel/niet tonen "en meer"
						if($("input[name=fpk_enmeer]").val()=="1") {
							$("#zoekblok_prijsklasse_enmeer").show();
						} else {
							$("#zoekblok_prijsklasse_enmeer").hide();
						}


						if($("#fpk_tot").val()>$(".zoekblok_prijsklasse").data("max_show")) {
							$("#fpk_tot").val($(".zoekblok_prijsklasse").data("max_show"));
						}
						slider_active=true;
						clearTimeout(slider_timer);
						slider_timer=setTimeout(function(){
							if(slider_active && !slider_mouseactive) {
								slider_active=false;
								$("input[name=scrolly]").val($(window).scrollTop());
								$("input[name=fpk]").val("2");
								zoekblok_submit();
							}
						},700);
					}
				$(".slidernoui").noUiSlider({
					range: {'min': [$(".zoekblok_prijsklasse").data("min")], 'max': [slider_max_en_meer]},
					start: [$("#fpk_van_hidden").val(), $("#fpk_tot_hidden").val()],
					handles: 2,
                                        connect: true,
					step: $(".zoekblok_prijsklasse").data("stappen"),

					serialization: {
                                                lower:[ $.Link({
                                                    target: $("#fpk_van_hidden")
                                                })],
                                                upper:[ $.Link({
                                                    target: $("#fpk_tot_hidden")
                                                })],
						resolution: 1
					},


				});
                                $(".slidernoui").on("slide", onSlide);
			}


			// Multiple-bestemming verwerken
			$(".zoekblok_select").change(function() {
				show_ajaxloader(false);
				var nieuwe_waarde="";
				var al_gehad = [];
				if($("input[name=fsg]").length>0 && $("input[name=fsg]").val().length>0) {
					nieuwe_waarde+=","+$("input[name=fsg]").val();
					al_gehad = $("input[name=fsg]").val().split(",");
				}
				if($("select[name=fsg_invoer]").length>0 && $("select[name=fsg_invoer]").val().length>0) {
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

			// aankomstdatum (d/m/Y) wissen via wis-icoontje
			$("#wis_aankomstdatum_flex").click(function(){

				$(this).hide();

				$("select[name=fadf_d]").val("");
				$("select[name=fadf_m]").val("");
				$("select[name=fadf_y]").val("");

				$(".flexibel_datum").trigger("liszt:updated");
				zoekblok_submit();

				return false;
			});

			// fzt wissen via wis-icoontje
			$("#wis_zoekblok_tekst").click(function(){

				$(this).hide();

				$("input[name=fzt]").val("");
				zoekblok_submit();

				return false;
			});

			$("input[name=fzt]").keyup(function() {
				if($(this).val().length>=1) {
					$(this).addClass("geen_vergrootglas");
					$("#wis_zoekblok_tekst").show();
				} else {
					$(this).removeClass("geen_vergrootglas");
				$("#wis_zoekblok_tekst").hide();
				}
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

				// get-value scrolly updaten
				nieuwe_url=updateURLParameter(nieuwe_url,"scrolly",$(window).scrollTop());

				// get-value map updaten
				nieuwe_url=nieuwe_url.replace("map=1","");
				if($(".zoekresultaten_zoeken_op_kaart_map").is(":visible")) {
					if(nieuwe_url.indexOf("?")) {
						// nieuwe_url bevat al een vraagteken
						nieuwe_url=nieuwe_url+"&map=1";
					} else {
						// nieuwe_url bevat nog geen vraagteken
						nieuwe_url=nieuwe_url+"?map=1";
					}
				}

				$(this).attr("href",nieuwe_url);
				return true;
			});

                        $(".alle_selecties_wissen").unbind('click');

			// scroll-y-positie opslaan (vanuit zoekresultaat-klik)
			$("a.zoekresultaat, a.zoekresultaat_type").click(function(event) {

				if(event.ctrlKey||event.which==2) {
					// link openen in nieuw venster: geen ajaxloader tonen
				} else {
					show_ajaxloader(true);
				}

				var nieuwe_url=$(this).attr("href");


				// retrieving the price based on the selected container: accommodation or type
				var match_price = 0;
				if ($(this).attr('class') == 'zoekresultaat_type') {
					var price_str = $(this).find('.zoekresultaat_type_prijs').html();
					match_price = price_str.match(/[0-9]+/g);
				} else if($(this).attr('class') == 'zoekresultaat') {
					var price_str = $(this).find('.zoekresultaat_prijs_bedrag').html();
					match_price = price_str.match(/[0-9]+/g);
				}

				// get-value scrolly updaten
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
				obj = {'scrolly': $(window).scrollTop()};
				url = window.location.href;
				new_url = updateURLParameter(url, 'scrolly', $(window).scrollTop());
				if ((typeof(history.pushState) != 'undefined')) history.pushState(obj, '', new_url);

				// get-value map updaten
				nieuwe_url=nieuwe_url.replace("%26map%3D1","");
				if($(".zoekresultaten_zoeken_op_kaart_map").is(":visible")) {
					if(/map%3D1/.test(nieuwe_url)) {
						// back-url bevat al map=1
					} else {
						if(/%3F/.test(nieuwe_url)) {
							// back-url bevat al een vraagteken
							nieuwe_url = nieuwe_url+"%26map%3D1";
						} else {
							// back-url bevat nog geen vraagteken
							nieuwe_url = nieuwe_url+"%3Fmap%3D1";
						}
					}
				}

				$(this).attr("href",nieuwe_url);

				// klik doorgeven aan Analytics
				if($(this).attr('class')=="zoekresultaat") {
					zoekopdracht_naar_analytics_sturen("doorklik naar accommodatiepagina","via accommodatie-blok");
				} else if($(this).attr('class')=="zoekresultaat_type") {
					zoekopdracht_naar_analytics_sturen("doorklik naar accommodatiepagina","via type-regel onder accommodatie-blok");
				}

				if(event.ctrlKey||event.which==2) {
					// link is geklikt via CTRL-click: laat de link z'n gang gaan
					return true;
				} else {
					// anders: even wachten om Analytics te kunnen sturen
					setTimeout(function() {
						// heel even wachten zodat Analytics kan laden
						if(nieuwe_url.match(/zoek-en-boek/g) || nieuwe_url.match(/aanbiedingen/g)){
							var list = 'Zoek en boek';
							if (nieuwe_url.match(/aanbiedingen/g)) list = 'Aanbiedingen';
							var url = nieuwe_url.split('/');
							if(lokale_testserver) {
								var match_type = url[3].match("^([A-Z]{1,2})([0-9]+)");
							} else {
								var match_type = url[2].match("^([A-Z]{1,2})([0-9]+)");
							}
							$.ajaxSetup({ scriptCharset: "utf-8" , contentType: "application/json; charset=utf-8"});
							var product_clicks_type = 0;
							var product_clicks_price = 0;
							if(match_type != null) {
								product_clicks_type = match_type[2];
							}
							if(match_price != null) {
								product_clicks_price = match_price[0];
							}
							$.getJSON(absolute_path+"rpc_json.php", {"t": "product_clicks","type_id":product_clicks_type, "price":product_clicks_price, "url":nieuwe_url, "list":list}, function(data){
								if(data.ok && typeof dataLayer !== 'undefined' && dataLayer !== null) {
									var callBackJson={'eventCallback': function(){
										document.location = nieuwe_url;
										}};
									var object = $.extend({}, data.dataLayer, callBackJson);
									dataLayer.push(object);
								} else {
									document.location.href = nieuwe_url;
								}
							});
						} else {
							document.location.href = nieuwe_url;
						}
					},100);

					return false;
				}
			});

			// naar de juiste positie scrollen
			if($("input[name=scrolly]").val()>0 && $("div.datadiv").data("paginanummer")=="1") {
				window.scrollTo(0,$("input[name=scrolly]").val());
			}

			//
			// autocomplete zoekformulier zoek-en-boek
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

			// zoektijd tonen aan ingelogde CMS-gebruikers
			if($("div#page_load_time").length!==0 && $("span#zoektijd").length!==0) {
				// alert('a');
				$("span#zoektijd").text($("div#page_load_time").data("time"));
			}


			$( "div.zoekresultaat_prijs_info_bijkomendekosten" ).hover(
				function() {
					zoek_en_boek_links_active = false;
				}, function() {
					zoek_en_boek_links_active = true;
				}
			);

			// show bijkomende kosten
			$("div.zoekresultaat_prijs_info_bijkomendekosten").click(function(event) {

				var popup = $("div.zoekresultaat_prijs_info_bijkomendekosten_popup");
				var new_popup = $("div.zoekresultaat_prijs_info_bijkomendekosten_empty").clone();
				var icon = $(this);

				if(icon.data("active")==="1") {
					popup.fadeOut("fast");
					icon.data("active", "0");
				} else {

					// hide all active popups
					popup.hide();
					$("div.zoekresultaat_prijs_info_bijkomendekosten").data("active", "0");


					$.getJSON(absolute_path+"rpc_json.php", {"t": "get_content_zoekresultaat_prijs_info_bijkomendekosten_popup","type_id":icon.data("type_id"),"seizoen_id":icon.data("seizoen_id"),"arrangement":icon.data("arrangement"),"ap":icon.data("ap"),"ad":icon.data("ad")}, function(data) {
						if(data.ok) {
							if(data.html) {

								icon.data("active", "1");

								new_popup.removeClass("zoekresultaat_prijs_info_bijkomendekosten_empty");

								new_popup.find("div").html(data.html);
								new_popup.appendTo("html");

								var position_left = icon.offset().left - $(window).scrollLeft(); //get the offset top of the element
								var position_top = icon.offset().top;

								// have the popup show at the lower right corner
								position_left = position_left - new_popup.outerWidth();
								position_top = position_top - new_popup.outerHeight();

								new_popup.css("top", position_top+"px");
								new_popup.css("left", position_left+"px");
								new_popup.fadeIn("normal");

								if(new_popup.offset().top - $(window).scrollTop() - 10 < 0) {
									// console.log("buiten");
									$('html, body').animate({scrollTop: new_popup.offset().top - 10}, 500);
								}
							} else {

							}
						}
					});
				}
			});

			// click somewhere: hide zoekresultaat_prijs_info_bijkomendekosten_popup
			$("html").click(function(event) {

				if (!$(event.target).hasClass("close") && $(event.target).closest(".zoekresultaat_prijs_info_bijkomendekosten_popup").length) {
					return false;
				}
				var popup = $("div.zoekresultaat_prijs_info_bijkomendekosten_popup");
				popup.fadeOut("fast");
				$("div.zoekresultaat_prijs_info_bijkomendekosten").data("active", "0");
			});

			$(document).on("click","div.zoekresultaat_prijs_info_bijkomendekosten_popup img.close",function(event) {
				var popup = $("div.zoekresultaat_prijs_info_bijkomendekosten_popup");
				popup.fadeOut("fast");
				$("div.zoekresultaat_prijs_info_bijkomendekosten").data("active", "0");
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

		/* $(".zoekresultaat_type_aanbieding").each(function() {
				if($(this).prev().prev().prev(".zoekresultaat_type_typenaam_aanbieding").html().length!==0) {
					var nieuwebreedte=230-10-$(this).outerWidth(true);
					$(this).prev(".zoekresultaat_type_typenaam_aanbieding").css("width",nieuwebreedte);
				}
			}); */
		}

		// sluiten cookie-bar
		$("#cookie_bottombar_close").click(function () {
			// $("#cookie_bottombar").css("display","none");
			$("#cookie_bottombar").animate({bottom:"-100px"},600,function() {});
			chalet_createCookie("cookiemelding_gelezen","1",3650);
			return false;
		});

                $("#notification_bottombar_close").click(function () {
			// $("#cookie_bottombar").css("display","none");
			$("#notification_bottombar").animate({top:"-150px"},600,function() {});
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

			// bij klikken op "uitgebreid zoeken": referer=3
			$("input[name=referer]").val("3");

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

		// snelzoeken zoek-en-boek links: link selecteer bestemming
		$(".zoekenboek_invulveld_bestemming").click(function(){
			$("input[name=selb]").val("1");

			// bij klikken op "selecteer bestemming": referer=4
			$("input[name=referer]").val("4");

			$("#form_zoekenboeklinks").submit();
			return false;
		});

		if($().chosen) {
			// zoek-en-boek homepage
			$("#form_zoekenboeklinks").submit(function(){
				show_ajaxloader(true);
				return true;
			});
			if(detect_mobile()) {
				$(".zoekenboek_invulveld select").addClass("zoekblok_select_mobile_smal");

				// pijltje naar beneden: verbergen bij Android (want: staat er al via de "select")
				if(navigator.userAgent.match(/Android/i) !== null) {
					$(".zoekblok_select_mobile_smal").addClass("zoekblok_select_mobile_android");
				}

				$(".zoekenboek_invulveld select").each(function(){
					$(this).find("option:first").html($(this).data("placeholder"));
				});
			} else {
				$(".zoekenboek_invulveld select").chosen({width: "100%", disable_search: true,allow_single_deselect: false});
			}
		}

		// bij focus tekstzoeken: vergrootglas weghalen
		$( ".tekstzoeken" ).focus(function(){
			$(this).addClass("tekstzoeken_leeg");
		})

		//
		// autocomplete snelzoeken
		//
		$( ".tekstzoeken, .tekstzoeken_geen_chosen_layout" ).autocomplete({
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


		// zoek op kaart
		$(".zoekresultaten_zoeken_op_kaart").click(function(){
			google.maps.event.trigger(map, 'resize');
			map.setCenter(new google.maps.LatLng(mapOptions.googlemaps_lat,mapOptions.googlemaps_long));
			$(".zoekresultaten_zoeken_op_kaart_map").slideToggle("slow",function() {
				if($(this).is(":visible")) {
					$(".zoekresultaten_zoeken_op_kaart i").removeClass("icon-arrow-down");
					$(".zoekresultaten_zoeken_op_kaart i").addClass("icon-arrow-up");
					$(".zoekresultaten_zoeken_op_kaart_map_canvas").show();
					google.maps.event.trigger(map, 'resize');
					map.setCenter(new google.maps.LatLng(mapOptions.googlemaps_lat,mapOptions.googlemaps_long));
					$("input[name=map]").val("1");
				} else {
					$(".zoekresultaten_zoeken_op_kaart i").removeClass("icon-arrow-up");
					$(".zoekresultaten_zoeken_op_kaart i").addClass("icon-arrow-down");
					$(".zoekresultaten_zoeken_op_kaart_map_canvas").hide();
					$("input[name=map]").val("0");
				}
			});
		});

		// link "toon op kaart"
		$(".toonopkaart").click(function(event){
			google.maps.event.trigger(map, 'resize');
			map.setCenter(new google.maps.LatLng(mapOptions.googlemaps_lat,mapOptions.googlemaps_long));
			$(".zoekresultaten_zoeken_op_kaart_map").slideToggle("slow",function() {
				if($(this).is(":visible")) {
					// $(".zoekresultaten_zoeken_op_kaart i").removeClass("icon-arrow-down");
					// $(".zoekresultaten_zoeken_op_kaart i").addClass("icon-arrow-up");
					$(".zoekresultaten_zoeken_op_kaart_map_canvas").show();
					google.maps.event.trigger(map, 'resize');
					map.setCenter(new google.maps.LatLng(mapOptions.googlemaps_lat,mapOptions.googlemaps_long));
					$("input[name=map]").val("1");

					$(".toonopkaart a").html($(".toonopkaart a").data("toggle"));
					window.location.hash = 'kaart';

				} else {
					// $(".zoekresultaten_zoeken_op_kaart i").removeClass("icon-arrow-up");
					// $(".zoekresultaten_zoeken_op_kaart i").addClass("icon-arrow-down");

					$(".zoekresultaten_zoeken_op_kaart_map_canvas").hide();
					$("input[name=map]").val("0");

					$(".toonopkaart a").html($(".toonopkaart a").data("org"));

					// remove the kaart hash
					if(window.location.hash == '#kaart') {
						removeHash();
					}

				}
			});
			event.preventDefault();
		});

		//
		// zoeken op kaart
		//
		if($(".zoekresultaten_zoeken_op_kaart_map").length!==0) {

			if(location.hash == '#kaart') {
				$(".toonopkaart a").html($(".toonopkaart a").data("toggle"));
				$(".zoekresultaten_zoeken_op_kaart_map_canvas, .zoekresultaten_zoeken_op_kaart_map").show();
			}

			// start google map
			initialize_googlemaps(mapOptions);
		}

		// factuur goedkeuren door klant: button actief/inactief
		$("input[name=goedkeur1], input[name=goedkeur2]").change(function() {
			if($("input[name=goedkeur1]").is(":checked") && $("input[name=goedkeur2]").is(":checked")) {
				$("#factuurakkoordsubmit").removeAttr("disabled");
			} else {
				$("#factuurakkoordsubmit").attr("disabled","disabled");
			}
		});

		// scroll to top
		var scroll_to_top_text="Naar boven";

		try {
			$.scrollUp({
				scrollName: 'scrollUp-arrow',
				scrollText: '',
				animation: 'slide'

			});
		} catch(err) {

		}

		// meer foto's reisblog Italissima
		$("a#blog_foto_aanvullend_meer_link").click(function(){

			$("#blog_foto_aanvullend_minder_link").show();
			$("#blog_foto_aanvullend_meer_link").hide();

			$("#blog_foto_aanvullend_meer").slideDown("slow");
			return false;
		});

		// minder foto's reisblog Italissima
		$("a#blog_foto_aanvullend_minder_link").click(function(){
			$("#blog_foto_aanvullend_meer").slideUp("slow",function(){
				$("#blog_foto_aanvullend_minder_link").hide();
				$("#blog_foto_aanvullend_meer_link").show();
			});
			return false;
		});

		if($(".trigger_livechat_button").length!==0) {

			//
			// trigger_livechat_button klikbaar maken indien chat online is
			//
			if($(".livechat_button").length!==0) {
				setTimeout(function() {
					if($("div.livechat_button img").length!==0) {
						if($("div.livechat_button img").attr("src").indexOf("online")>0) {
							$("span.trigger_livechat_button").replaceWith(function() {
								var text = $.trim($(this).text());
								return "<a href='#' class='trigger_livechat_button'>" + text + "</a>";
							});
						}
					}
				},1500);

				//
				// trigger click op chatbutton
				//
				$("a.trigger_livechat_button").live("click",function(event) {
					event.preventDefault();
					$(".livechat_button a").trigger("click");
					return false;
				});
			}
		}

		//
		// tarieventabel-functies
		//
		if($(".tarieventabel_wrapper").length!==0) {

			if($(".tarieventabel_datumbalk[data-counter=1]").length!==0) {
				// positionering tarieventabel_pijl_boven bepalen
				$(".tarieventabel_pijl_boven").css("top",$(".tarieventabel_datumbalk[data-counter=1]").position().top+"px");
				$(".tarieventabel_pijl_boven").css("display","block");
			}

			if($(".tarieventabel_datumbalk[data-counter=2]").length!==0) {
				// positionering tarieventabel_pijl_onder bepalen
				$(".tarieventabel_pijl_onder").css("top",$(".tarieventabel_datumbalk[data-counter=2]").position().top+"px");
				$(".tarieventabel_pijl_onder").css("display","block");
			}

			//
			// naar juiste positie scrollen bij openen tarieventabel
			//
			var actieve_kolom = parseInt($(".tarieventabel_wrapper").data("actieve-kolom"),10);
			if(actieve_kolom>=5) {


				var position = actieve_kolom * 67;

				position=position-(4*67);

				$(".tarieventabel_wrapper_rechts").scrollLeft(position);
			}

			//
			// click on .tarieventabel_jump_jaarmaand
			//
			$("a.tarieventabel_jump_jaarmaand").click(function(event) {

				event.preventDefault();

				var jaarmaand = $(this).data("jaarmaand");

				var actieve_kolom = parseInt($("td[data-jaarmaand="+jaarmaand+"]").data("maand-kolom"),10);

				var new_position = actieve_kolom * 67;

				new_position=new_position-(4*67);
				// $(".tarieventabel_wrapper_rechts").scrollLeft(new_position);

				var pixels_to_scroll = Math.abs(new_position-$(".tarieventabel_wrapper_rechts").scrollLeft());
				var animate_time = 1200;

				if(pixels_to_scroll<200) {
					animate_time = 200;
				} else if(pixels_to_scroll < 600) {
					animate_time = 600;
				} else if(pixels_to_scroll < 10) {
					animate_time = 1;
				}


				// animatie: scrollen tabel in 1200ms
				$(".tarieventabel_wrapper_rechts").animate({scrollLeft: new_position}, animate_time, function(){
					tarieventabel_controleer_scrollbuttons();
				});

				// highlight
				$("td").filter(function() {
					return  $(this).data("jaarmaand") >= jaarmaand;
				}).effect("highlight", {}, 3000 + animate_time);

			});


			// tarieventabel: meer/minder personen tonen
			$(".tarieventabel_toggle_toon_verberg a").click(function(event) {

				event.preventDefault();

				var deze=$(this);

				// horizontale scrollpositie onthouden
				var leftPos = parseInt($(".tarieventabel_wrapper_rechts").scrollLeft(),10);


				if ( $(".tarieventabel_verbergen").is(":visible") ) {
					// inklappen
					$(".tarieventabel_verbergen").fadeOut(700,function(){
						$(".tarieventabel_verbergen").hide();

						$(".tarieventabel_toggle_toon_verberg span").html(deze.data("default"));

						$(".tarieventabel_toggle_toon_verberg i").removeClass("icon-chevron-sign-up");
						$(".tarieventabel_toggle_toon_verberg i").addClass("icon-chevron-sign-down");

					});
				} else {

					// openklappen

					// $(".tarieventabel_verbergen").effect("highlight",{},1000);

					$(".tarieventabel_verbergen").fadeIn(700,function(){
						$(".tarieventabel_verbergen").show();
					});

					$(".tarieventabel_toggle_toon_verberg span").html(deze.data("hide"));

					$(".tarieventabel_toggle_toon_verberg i").removeClass("icon-chevron-sign-down");
					$(".tarieventabel_toggle_toon_verberg i").addClass("icon-chevron-sign-up");

				}


				// weer scrollen naar oude scrollpositie
				$(".tarieventabel_wrapper_rechts").scrollLeft(leftPos);

				return false;

			});

			// tarieventabel: te verbergen tr's verbergen (laag aantal personen/commissie bij reisagent)
			$(".tarieventabel_verbergen").hide();

			// tarieventabel: klikken op bedrag naar boeken leiden
			$("td.tarieventabel_tarieven_beschikbaar").click(function(event){
				event.preventDefault();

				var url='';
				if($(this).parent().data("aantalpersonen")>0) {
					url=$(".tarieventabel_wrapper").data("boek-url")+"&d="+$(this).data("week")+"&ap="+$(this).parent().data("aantalpersonen");
				} else {
					url=$(".tarieventabel_wrapper").data("boek-url")+"&d="+$(this).data("week");
				}
				document.location.href=url;

			});

			//
			// tarieventabel: scrollen
			//
			$("#prijsinformatie").on("click", ".tarieventabel_pijl",  function(event) {

				var actieve_pijl=$(this);

				// kijken of de animatie al bezig is
				if( $(".tarieventabel_wrapper_rechts").is(":animated") ) {
					// zo ja: click over 200ms opnieuw starten
					setTimeout(function() {
						actieve_pijl.trigger("click");
					},200);
				} else {

					var leftPos = parseInt($(".tarieventabel_wrapper_rechts").scrollLeft(),10);
					var pijl_links=false;

					if($(this).hasClass("tarieventabel_pijl_links")) {
						pijl_links=true;
					}

					var eindpos = 0;
					var nieuwpos = 0;
					var maxpos = 0;
					$(".tarieventabel_maanden td").each(function() {
						if($(this).data("maand-kolom")) {

							eindpos=parseInt($(this).data("maand-kolom"))*67;
							maxpos=parseInt($(this).data("maand-kolom"))*67;

							if(pijl_links) {
								if(eindpos<leftPos) {
									nieuwpos=eindpos;
								}
							} else {
								if(nieuwpos==0 && eindpos>leftPos) {
									nieuwpos=eindpos;
								}
							}
						}
					});
					if((pijl_links && nieuwpos==0 && leftPos==0) || (!pijl_links && maxpos<= (leftPos+500) )) {
						// bij klikken als eind al is bereikt: rood oplichten
						actieve_pijl.addClass("tarieventabel_pijl_scrollstop").delay(500).queue(function(next){
							$(this).removeClass("tarieventabel_pijl_scrollstop");
							next();
						});
					} else {
						if((nieuwpos+500)>maxpos) {
							// bij bereiken eind: rood oplichten
							actieve_pijl.delay(200).queue(function(next){
								actieve_pijl.addClass("tarieventabel_pijl_scrollstop").delay(500).queue(function(next2){
									$(this).removeClass("tarieventabel_pijl_scrollstop");
									next2();
								});
								next();
							});
						}

						// animatie: scrollen tabel in 600ms
						$(".tarieventabel_wrapper_rechts").animate({scrollLeft: nieuwpos}, 600, function(){
							// tarieventabel_controleer_scrollbuttons();
						});
					}
				}
				event.preventDefault();
			});
		}

		if($(".tarieventabel_toelichting_active_season").length!==0) {

			// handle multiple seasons of bijkomendekosten

			var active_season_div = $(".tarieventabel_toelichting_one_season[data-seizoen_id="+$("select.tarieventabel_toelichting_switch_seasons").val()+"]");
			$(".tarieventabel_toelichting_active_season").html( active_season_div.html() );
			$(".tarieventabel_toelichting_all_seasons").height(active_season_div.height());

			$("select.tarieventabel_toelichting_switch_seasons").change(function(event) {
				tarieventabel_toelichting_change_season($(this).val());
			});
		}

		$(".tarieventabel_wrapper_rechts").scroll(function() {
			// kijken of de scrollbuttons uitgeschakeld moeten worden bij scrollen
			tarieventabel_controleer_scrollbuttons();
		});

		// kijken of de scrollbuttons uitgeschakeld moeten worden
		// tarieventabel_controleer_scrollbuttons();


		// externe links als Anayltics-event opslaan
		$("a.analytics_track_external_click").click(function(event) {

			var external_url=$(this).attr("href");

			if (typeof ga != "undefined") {
				ga('send', 'event', 'doorklik naar externe site', 'url', external_url);
			}

			return true;
		});



		// switchen tussen valuta

		if($.msDropdown) {
			$(".currency_select").msDropdown();
		}

		$(".tarieventabel_top_valuta select").change(function(event) {
			var currency=$(this).val();

			// juiste tarieven tonen
			$(".tarieventabel_tarieven_div div[data-euro]").each(function() {
				$(this).text($(this).data(currency));
			});

			// juiste teksten tonen
			$(".tarieventabel_top_valutanaam").html($(".tarieventabel_top_valutanaam").data(currency));
			$(".tarieventabel_top_valuta_toelichting1").html($(".tarieventabel_top_valuta_toelichting1").data(currency));
			$(".tarieventabel_top_valuta_toelichting2").html($(".tarieventabel_top_valuta_toelichting2").data(currency));

			// juiste vlag tonen
			$(".tarieventabel_top_valuta select").removeClass();
			$(".tarieventabel_top_valuta select").addClass("option_valuta_"+currency);

			// event naar Analytics sturen
			event_naar_analytics_sturen("bezoekers-acties", "valuta gewijzigd", currency);

		});


		// open/close faq-questions
		$(".faq_vraag").click(function(event){
			$(this).next().slideToggle("fast");
		})
		var faq_open_all = 0;
		$(".faq_open_all").click(function(event) {
			if(faq_open_all==1) {
				$(".faq_antwoord").slideUp("fast");
				faq_open_all = 0;
			} else {
				$(".faq_antwoord").slideDown("fast");
				faq_open_all = 1;
			}
		})


		// newpricesmail
		$(".tarieventabel_newpricesmail a").click(function(event) {

			event.preventDefault();

			$(".tarieventabel_newpricesmail_form").slideToggle();
		});

		$(".tarieventabel_newpricesmail_form form").submit(function(event) {

			event.preventDefault();
			$(".tarieventabel_newpricesmail_form img").hide();
			$(".tarieventabel_newpricesmail_form input[type=submit]").attr("disabled","disabled");

			$("#ajaxloader_page").show();

			$.getJSON(absolute_path+"rpc_json.php", {
			"t": "newpricesmail",
			"email": $(".tarieventabel_newpricesmail_form input[type=email]").val(),
			"type_id": $(".tarieventabel_newpricesmail_form").data("type_id"),
			"seizoen_id": $(".tarieventabel_newpricesmail_form").data("seizoen_id")
			}, function(data) {
				if(data.ok) {
					$("#ajaxloader_page").hide();
					$(".tarieventabel_newpricesmail_form input[type=submit]").removeAttr("disabled");
				}
				if(data.added) {
					$(".tarieventabel_newpricesmail_form img.okay").fadeIn("slow", function() {
						$(this).delay(2000).fadeOut("slow");

						// event naar Analytics sturen
						event_naar_analytics_sturen("bezoekers-acties", "mail mij over prijzen nieuw seizoen", $(".tarieventabel_newpricesmail_form").data("seizoen_name"));

					});
					$(".tarieventabel_newpricesmail_form input[type=email]").val("");
				} else {
					$(".tarieventabel_newpricesmail_form img.notokay").fadeIn("fast", function() {
						$(this).delay(2000).fadeOut("slow");
					});
				}
			});
		});
	}

	// slide up/down
	$(".slide_more").click(function(event) {
		event.preventDefault();
		if($(".slide_more_div").is(":visible")) {
			$(".slide_more_div").slideUp("normal", function(){
				$(".slide_more").html($(".slide_more").data("open"));
			});
		} else {
			$(".slide_more_div").slideDown("normal", function(){
				$(".slide_more").html($(".slide_more").data("close"));
			});
		}

		return false;
	});
       $("#map_canvas").css("display","none").delay(2000);
});


var tarieventabel_maxpos = 0;
var tarieventabel_toelichting_active_season = 0;

function tarieventabel_toelichting_check_season() {
	//
	// check which bijkomendekosten-season has to be shown (based on scroll position)
	//

	if($(".tarieventabel_datadiv").length!==0) {
		var leftPos = parseInt($(".tarieventabel_wrapper_rechts").scrollLeft(),10);

		if($(".tarieventabel_datadiv").data("begin_seizoen")) {
			var seizoenwissel = (parseInt($(".tarieventabel_datadiv").data("begin_seizoen"))-5) * 67;
			if(leftPos>seizoenwissel) {
				tarieventabel_toelichting_change_season($(".tarieventabel_datadiv").data("last_seizoen_id"));
			} else {
				tarieventabel_toelichting_change_season($(".tarieventabel_datadiv").data("first_seizoen_id"));
			}
		}
	}
}

function tarieventabel_toelichting_change_season(seizoen_id) {

	//
	// switch season of bijkomendekosten
	//

	if($(".tarieventabel_toelichting_active_season").length!==0 && tarieventabel_toelichting_active_season != seizoen_id) {

		$("select.tarieventabel_toelichting_switch_seasons").val(seizoen_id);

		var active_season_div = $(".tarieventabel_toelichting_one_season[data-seizoen_id="+$("select.tarieventabel_toelichting_switch_seasons").val()+"]");

		var new_height = active_season_div.height();
		$(".tarieventabel_toelichting_all_seasons").animate({height: new_height}, 400);

		$(".tarieventabel_toelichting_active_season").fadeOut(400, function() {

			$(".tarieventabel_toelichting_active_season").html( active_season_div.html() );
			$(".tarieventabel_toelichting_active_season").fadeIn(700);
			tarieventabel_toelichting_active_season = seizoen_id;
		});
	}
}

function tarieventabel_controleer_scrollbuttons() {

	var leftPos = parseInt($(".tarieventabel_wrapper_rechts").scrollLeft(),10);

	if(tarieventabel_maxpos==0) {
		$(".tarieventabel_maanden td").each(function() {
			if($(this).data("maand-kolom")) {
				eindpos=parseInt($(this).data("maand-kolom"))*67;
				tarieventabel_maxpos=parseInt($(this).data("maand-kolom"))*67;
			}
		});
	}

	// alert(tarieventabel_maxpos);

	var actieve_pijl;
	var pijl_links=false;


	$(".tarieventabel_pijl").each(function() {

		actieve_pijl=$(this);

		if(actieve_pijl.hasClass("tarieventabel_pijl_links")) {
			pijl_links=true;
		} else {
			pijl_links=false;
		}

		// alert(leftPos+" "+tarieventabel_maxpos);
		if((pijl_links && leftPos==0) || (!pijl_links && (leftPos+500)>=tarieventabel_maxpos)) {
			actieve_pijl.addClass("tarieventabel_pijl_scroll_greyed_out");
		} else {
			actieve_pijl.removeClass("tarieventabel_pijl_scroll_greyed_out");
		}
	});

	tarieventabel_toelichting_check_season();

}

function map_zoek_en_boek_click(e) {
	// klikken op accommodatie bij zoeken op kaart
	show_ajaxloader(true);

	var back_url=location.href;
	if(!mapOptions.googlemaps_zoekenboek && mapOptions.googlemaps_skigebiedid===0) {
		back_url = back_url.replace(location.hash,"");
		if(back_url.indexOf('?back=') > -1) {
			map_back_url = decodeURIComponent(back_url.split('?back=')[0]);
			back_url = decodeURIComponent(back_url.split('?back=')[1]);
		} else {
			back_url = decodeURIComponent(back_url);
			map_back_url = back_url;
		}
		var nieuwe_url=$(e).attr("href")+"?back="+encodeURIComponent(back_url).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");

	} else {
		back_url=updateURLParameter(back_url,"scrolly",$(window).scrollTop());
		back_url=updateURLParameter(back_url,"map",1);

		if(back_url.indexOf('#kaart') > -1) {
			back_url = back_url.replace("#kaart", "");
			back_url += "#kaart";
		}
		map_back_url = back_url;
		var nieuwe_url=$(e).attr("href")+"?back="+encodeURIComponent(back_url).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
	}

	$(e).attr("href",nieuwe_url);

	var marker = $(e).data("marker");
	// huidige Google Maps-zoom en -center opslaan
	map_regio_click(marker);

	// klik doorgeven aan Analytics
	zoekopdracht_naar_analytics_sturen("doorklik naar accommodatiepagina","via kaart");

	setTimeout(function() {
		// heel even wachten zodat Analytics kan laden
		document.location.href = nieuwe_url;
	},100);

	return false;
}

function map_regio_click(marker) {
	// klikken op plaats bij regio-Google Maps (Italissima)

	// huidige Google Maps-zoom en -center opslaan
	var mapCentre=map.getCenter();
	var mapLat=mapCentre.lat();
	var mapLng=mapCentre.lng();
	if(marker) {
		var mapMarker=marker;
	} else {
		var mapMarker=false;
	}

	var url = location.href;
	if(typeof map_back_url != "undefined" && map_back_url != '') {
		url = map_back_url;
		map_back_url = '';
	}

	var mapCookie= {lt: mapLat, lg: mapLng, z: map.getZoom(), url: url, m: mapMarker };

	chalet_createCookie("map_regio",JSON.stringify(mapCookie));
	return true;
}


function chalet_createCookie(name,value,days) {
	// functie om eenvoudig cookies te plaatsen
	var expires="";
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		expires = "; expires="+date.toGMTString();
	}
	else expires = "";
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
						chalet_createCookie("favorietenpopup","1",3650);

						if(!$isMobile) {
							// scroll to top (to see the favorites-popup)
							$("html, body").animate({ scrollTop: 0 }, 600,'swing',function(){
								$("#favorieten_popup").css("display","inline");
							});
						} else {

                                                        $('div#menu-left').trigger("open");
						}
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
			if (typeof ga != "undefined") {
				ga('send', 'event', 'bezoekers-acties', 'url', google_analytics_event, begincode+typeid);
			}
		}
	});
	return false;
}

function zoekopdracht_naar_analytics_sturen(omschrijving,zoekopdracht) {
	// stuur de zoekopdracht naar Google Analytics
	if (typeof ga != "undefined") {
		ga('send', 'event', 'zoekfunctie', omschrijving, zoekopdracht);
	}
}

function zoekopdracht_naar_analytics_sturen_inclusief_aantal(omschrijving,zoekopdracht,aantal) {
	// stuur de zoekopdracht naar Google Analytics (en neem een aantal op als extra Value)
	if (typeof ga != "undefined") {
		ga('send', 'event', 'zoekfunctie', omschrijving, zoekopdracht, aantal);
	}
}

function event_naar_analytics_sturen(categorie, omschrijving, waarde, aantal) {
	// stuur de zoekopdracht naar Google Analytics
	if (typeof ga != "undefined") {
		if(typeof(aantal)==="undefined") {
			ga('send', 'event', categorie, omschrijving, waarde);
		} else {
			ga('send', 'event', categorie, omschrijving, waarde, aantal);
		}
	}
}

// zaken die pas na het laden van alles uitgevoerd moeten worden:
$(document).ready(function() {

	if(gebruik_jquery===true) {

		// zoekblok pas tonen als alles klaar is
		$("#zoekblok, #verfijn, #zoekenboek_overlay, #zoekenboek, .hide_during_pageload").css("visibility","visible");

		// indien _GET["selb"]==1 : bestemming-pulldown openklappen
		if(location.href.indexOf("&selb=1") > -1 && $("div.datadiv").data("referer_zoekenboek")!="0") {
			$('#zoekblok_field_bestemming').trigger('liszt:open');
		}


		// if($("#zoekblok").length!==0 && parseInt($("div.datadiv").data("nieuwezoekopdracht"),10)==1 && $("body").attr("id")=="body_zoek-en-boek" && $("div.zoekblok_zoek_alleen_aanbiedingen").length==0) {
		if($("#zoekblok").length!==0 && parseInt($("div.datadiv").data("nieuwezoekopdracht"),10)==1 && $("body").attr("id")=="body_zoek-en-boek") {

			//
			// zoekopdracht naar Google Analytics sturen
			//

			var analytics_complete_zoekopdracht='';
			var analytics_bestemming='';
			var analytics_aankomstdatum='';
			var analytics_verblijfsduur='';
			var analytics_aantalpersonen='';
			var analytics_aantalslaapkamers='';
			var analytics_prijsklasse='';
			var analytics_tekstzoeken='';
			var analytics_verfijnen='';
			var analytics_verfijnen_item='';
			var analytics_url='';
			var analytics_niet_verzenden_indien_alleen='';


			// extra zoekopdracht (bijv. tarieven volgend seizoen)
			if($("div.datadiv").data("analytics_zoekopdracht")) {
				analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - "+$("div.datadiv").data("analytics_zoekopdracht");
				analytics_niet_verzenden_indien_alleen=$("div.datadiv").data("analytics_zoekopdracht");
			}

			// Zoek alleen naar aanbiedingen
			if($("input[name=faab]").length!==0) {
				if($("input[name=faab]").is(":checked")) {
					analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Zoek alleen naar aanbiedingen";
					analytics_niet_verzenden_indien_alleen="Zoek alleen naar aanbiedingen";
					zoekopdracht_naar_analytics_sturen("zoek alleen naar aanbiedingen","ja");
				} else {
					zoekopdracht_naar_analytics_sturen("zoek alleen naar aanbiedingen","nee");
				}
			}

			// bestemming
			if($("div.zoekblok_bestemming_actief_item").length!==0) {

				var analytics_bestemming_teller_land=0;
				var analytics_bestemming_teller_regio=0;
				var analytics_bestemming_teller_plaats=0;
				var analytics_bestemming_teller_tekst="";

				$("div.zoekblok_bestemming_actief_item").each(function() {
					if($(this).data("bestemming_actief_name").length!==0) {
						if(analytics_bestemming) {
							analytics_bestemming=analytics_bestemming+", ";
						}
						analytics_bestemming=analytics_bestemming+$(this).data("bestemming_actief_name");

						if($(this).data("bestemming_actief_value").match(/^[0-9]-0$/)) {
							// land
							analytics_bestemming_teller_land++;
						} else if($(this).data("bestemming_actief_value").match(/^[0-9]-[[0-9]+$/)) {
							// regio
							analytics_bestemming_teller_regio++;
						} else if($(this).data("bestemming_actief_value").match(/^pl/)) {
							// plaats
							analytics_bestemming_teller_plaats++;
						}

					}
				});

				if(analytics_bestemming) {
					zoekopdracht_naar_analytics_sturen("bestemming",analytics_bestemming);
					analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Bestemming: "+analytics_bestemming;
				}

				if(analytics_bestemming_teller_land>0 || analytics_bestemming_teller_regio>0 || analytics_bestemming_teller_plaats>0) {

					if(analytics_bestemming_teller_land>0) {
						zoekopdracht_naar_analytics_sturen_inclusief_aantal("soort bestemming","land",analytics_bestemming_teller_land);
						analytics_bestemming_teller_tekst=analytics_bestemming_teller_tekst+" - landen: "+analytics_bestemming_teller_land;
					}
					if(analytics_bestemming_teller_regio>0) {
						zoekopdracht_naar_analytics_sturen_inclusief_aantal("soort bestemming","regio",analytics_bestemming_teller_regio);
						analytics_bestemming_teller_tekst=analytics_bestemming_teller_tekst+" - regio's: "+analytics_bestemming_teller_regio;
					}
					if(analytics_bestemming_teller_plaats>0) {
						zoekopdracht_naar_analytics_sturen_inclusief_aantal("soort bestemming","plaats",analytics_bestemming_teller_plaats);
						analytics_bestemming_teller_tekst=analytics_bestemming_teller_tekst+" - plaatsen: "+analytics_bestemming_teller_plaats;
					}

					// complete zoekopdracht: ' - ' weghalen
					if(analytics_bestemming_teller_tekst) {
						analytics_bestemming_teller_tekst=analytics_bestemming_teller_tekst.substr(3);
						zoekopdracht_naar_analytics_sturen('soort bestemming - compleet',analytics_bestemming_teller_tekst);
					}
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

			// prijsklasse
			if($("input[name=fpk_van]").length!==0) {
				if($("input[name=fpk_van]").val().length!==0 && $("input[name=fpk_tot]").val().length!==0) {
					analytics_prijsklasse=$("input[name=fpk_van]").val()+" tot "+$("input[name=fpk_tot]").val();
					if($("input[name=fpk_enmeer]").val()=="1") {
						analytics_prijsklasse=analytics_prijsklasse+" en meer";
					} else {
						zoekopdracht_naar_analytics_sturen_inclusief_aantal("prijsklasse - losse ingevulde waarden","Prijsklasse tot",parseInt($("input[name=fpk_tot]").val(),10));
					}
					if(parseInt($("input[name=fpk_van]").val(),10)>0) {
						zoekopdracht_naar_analytics_sturen_inclusief_aantal("prijsklasse - losse ingevulde waarden","Prijsklasse van",parseInt($("input[name=fpk_van]").val(),10));
					}

					zoekopdracht_naar_analytics_sturen("prijsklasse",analytics_prijsklasse);
					analytics_complete_zoekopdracht=analytics_complete_zoekopdracht+" - Prijsklasse: "+analytics_prijsklasse;
				}
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
				if(analytics_complete_zoekopdracht==analytics_niet_verzenden_indien_alleen) {
					// complete zoekopdracht niet verzenden: bestaat alleen uit een niet door gebruiker ingevoerd veld
				} else {
					// aantal zoekresultaten uit data-element halen
					zoekopdracht_naar_analytics_sturen_inclusief_aantal("complete zoekopdracht",analytics_complete_zoekopdracht,parseInt($("div#aantalgevonden").data("aantalgevonden"),10));
				}

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
				zoekopdracht_naar_analytics_sturen("gebruikte zoekfunctie","zoek-en-boek-pagina");
			} else if($("div.datadiv").data("referer_zoekenboek")=="2") {
				zoekopdracht_naar_analytics_sturen("gebruikte zoekfunctie","snel zoeken");
			} else if($("div.datadiv").data("referer_zoekenboek")=="3") {
				zoekopdracht_naar_analytics_sturen("gebruikte zoekfunctie","klik op 'uitgebreid zoeken' bij snel zoeken");
			} else if($("div.datadiv").data("referer_zoekenboek")=="4") {
				zoekopdracht_naar_analytics_sturen("gebruikte zoekfunctie","klik op 'selecteer bestemming' bij snel zoeken");
			} else if($("div.datadiv").data("referer_zoekenboek")=="5") {
				zoekopdracht_naar_analytics_sturen("gebruikte zoekfunctie","klik op 'uitgebreid zoeken' bij een thema");
			}
		}
	}
});
