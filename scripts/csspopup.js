//het zichtbaar of onzichtbaar maken van een de popup
function toggle(div_id) {
	var el = document.getElementById(div_id);
	if ( el.style.display == 'none' ) {	el.style.display = 'block';}
	else {el.style.display = 'none';}
}
//deze algoritme heb ik niet geschreven. maar zo te zien bepaalt deze de plaatsing van de achtergrond div die het scherm zwart kleurde. deze div(Blanket) wordt niet meer gebruikt.
function blanket_size(popUpDivVar) {
	if (typeof window.innerWidth != 'undefined') {
		viewportheight = window.innerHeight;
	} else {
		viewportheight = document.documentElement.clientHeight;
	}
	if ((viewportheight > document.body.parentNode.scrollHeight) && (viewportheight > document.body.parentNode.clientHeight)) {
		blanket_height = viewportheight;
	} else {
		if (document.body.parentNode.clientHeight > document.body.parentNode.scrollHeight) {
			blanket_height = document.body.parentNode.clientHeight;
		} else {
			blanket_height = document.body.parentNode.scrollHeight;
		}
	}
	var blanket = document.getElementById('blanket');
	blanket.style.height = blanket_height + 'px';
	var popUpDiv = document.getElementById(popUpDivVar);
	popUpDiv_height=blanket_height/2-150;//150 is half popup's height
	popUpDiv.style.top = popUpDiv_height + 'px';
}
//deze algoritme heb ik niet geschreven. maar zo te zien bepaalt deze de plaatsing van de achtergrond div die het scherm zwart kleurde. deze div(Blanket) wordt niet meer gebruikt.
function window_pos(popUpDivVar) {
	if (typeof window.innerWidth != 'undefined') {
		viewportwidth = window.innerHeight;
	} else {
		viewportwidth = document.documentElement.clientHeight;
	}
	if ((viewportwidth > document.body.parentNode.scrollWidth) && (viewportwidth > document.body.parentNode.clientWidth)) {
		window_width = viewportwidth;
	} else {
		if (document.body.parentNode.clientWidth > document.body.parentNode.scrollWidth) {
			window_width = document.body.parentNode.clientWidth;
		} else {
			window_width = document.body.parentNode.scrollWidth;
		}
	}
	var popUpDiv = document.getElementById(popUpDivVar);
	window_width=window_width/2-150;//150 is half popup's width
	popUpDiv.style.left = window_width + 'px';
}
//het zichtbaar of onzichtbaar maken van de informatie popup.
function popup(windowname) {
	toggle(windowname);
}

//deze code bepaalt wanneer de informatie popup moet worden getoond
//window.alert(sessionStorage.click);
function showpopupYesno(aantal){
	if(sessionStorage.click==1){
		if(aantal==1){	
			popup('popUpDiv');
			sessionStorage.click++;
		}
	}
}
//deze methode is verantwoordelijk voor het bepalen van welke knop der moet worden weergegeven. de invoer of de verwijder knop.
//deze methode zet ook de aantallen op de menu balk en roept een anedere methode die verantwoordelijk is voor het checken of de informatie popup moet worden getoond of niet
function getfavsandCheckAvailability(typeid){
	$.getJSON("/chalet/rpc_json.php", {
		"t": 4,
		"action": "getfavs"
	}, function(data) {
		if(data.ok) {
			$("#favorietenaantal").html(data.aantal);
			showpopupYesno(data.aantal);
			if(typeid != null || typeid != ""){
				currentTID=typeid;
				gevonden=0;
				if(data.favs==0){
					document.getElementById("favadd").style.display='inline';
					document.getElementById("favremove").style.display='none';
				}
				for(i=0;i<data.favs.length;i++){
					if(data.favs[i]==currentTID){
						gevonden = 1;
					}
				}
				if(gevonden==1){
					document.getElementById("favadd").style.display='none';
					document.getElementById("favremove").style.display='inline';
				}
				else{
					document.getElementById("favadd").style.display='inline';
					document.getElementById("favremove").style.display='none';
				}
			}
		}
	});
}

//Omdat er op deze paginas geen divs staan met ids favadd en favremove. dat zorgde ervoor dat de boel niet juiste werkte
function getfavsandCheckAvailabilityFavorietenPagina(typeid){
	$.getJSON("/chalet/rpc_json.php", {
		"t": 4,
		"action": "getfavs"
	}, function(data) {
		if(data.ok) {
			$("#favorietenaantal").html(data.aantal);
//			if(typeid != null || typeid != ""){
//				currentTID=typeid;
//				gevonden=0;
//				if(data.favs==0){
//					document.getElementById("favadd").style.display='inline';
//					document.getElementById("favremove").style.display='none';
//				}
//				for(i=0;i<data.favs.length;i++){
//					if(data.favs[i]==currentTID){
//						gevonden = 1;
//					}
//				}
//				if(gevonden==1){
//					document.getElementById("favadd").style.display='none';
//					document.getElementById("favremove").style.display='inline';
//				}
//				else{
//					document.getElementById("favadd").style.display='inline';
//					document.getElementById("favremove").style.display='none';
//				}
//			}
		}
	});
}
//Deze methode zorgt voor het invoeren en verwijderen van favorieten. accid is de type en action staat voor een insert of een delete action
function ajaxFunctionInsertDelete(accid, action){
	$.getJSON("/chalet/rpc_json.php", {
	"t": 4,
	"accommodatie": accid,
	"action": action
	}, function(data) {
		if(data.ok) {
			//$("#favorietenaantal").html(data.aantal);
			sessionStorage.click++;
			getfavsandCheckAvailability(accid);
		}
	});
}


