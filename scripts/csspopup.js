function toggle(div_id) {
	var el = document.getElementById(div_id);
	if ( el.style.display == 'none' ) {	el.style.display = 'block';}
	else {el.style.display = 'none';}
}
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
function popup(windowname) {
	toggle("blanket");
	toggle(windowname);
}

function getfavsandCheckAvailability(klantid, typeid){
	$.getJSON("/chalet/rpc_json.php", {
		"t": 4,
		"klantID": klantid,
		"action": "getfavs"
	}, function(data) {
		if(data.ok) {
			$("#favorietenaantal").html(data.aantal);
			currentTID=typeid;
			if(data.favs==0){
				document.getElementById("favadd").style.display='inline';
				document.getElementById("favremove").style.display='none';
			}
			for(i=0;i<data.favs.length;i++){
				if(data.favs[i]==currentTID){
					//echo"			window.alert(data.favs[i]);";
					document.getElementById("favadd").style.display='none';
					document.getElementById("favremove").style.display='inline';
					break;
				}
				else{
					document.getElementById("favadd").style.display='inline';
					document.getElementById("favremove").style.display='none';
					break;
				}
			}
		}
	});
}
function ajaxFunctionUpdateFav(klantid, typeid){
	$.getJSON("/chalet/rpc_json.php", {
		"t": 4,
		"klantID": klantid,
		"action": 'getfavs'
	}, function(data) {
		if(data.ok) {
			$("#favorietenaantal").html(data.aantal);
			currentTID=typeid;

			if(data.favs==0){
				document.getElementById("favadd").style.display='inline';
				document.getElementById("favremove").style.display='none';
			}
			for(i=0;i<data.favs.length;i++){
				if(data.favs[i]==currentTID){
					//echo"			window.alert(data.favs[i]);";
					document.getElementById("favadd").style.display='none';
					document.getElementById("favremove").style.display='inline';
					break;
				}
				else{
					document.getElementById("favadd").style.display='inline';
					document.getElementById("favremove").style.display='none';
					break;
				}
			}
		}
	});
}

function ajaxFunction(klantid, accid, action){
	$.getJSON("/chalet/rpc_json.php", {
	"t": 4,
	"klantID": klantid,
	"accommodatie": accid,
	"action": action
	}, function(data) {
		if(data.ok) {
			if(data.aantal==1){
				popup('popUpDiv');
			}
			$("#favorietenaantal").html(data.aantal);
		}
	});
}
