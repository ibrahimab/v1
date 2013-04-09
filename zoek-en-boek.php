<?php

include_once("admin/vars.php");

if(!session_id()) session_start();

if($vars["zoekform_aanbiedingen"] and $vars["seizoentype"]==2) {
	# Zomer: doorsturen naar zomer-aanbiedingensysteem
	header("Location: ".$vars["path"].txt("menu_aanbiedingen")."/",true,301);
	exit;
}

# referer naar zoek-en-boek bepalen
if($_GET["referer"] and !preg_match("/scrolly/",$_SERVER["REQUEST_URI"]) and !preg_match("/vf_/",$_SERVER["REQUEST_URI"])) {
	$referer_zoekenboek=intval($_GET["referer"]);
} elseif(!$_GET) {
	# geen referer: begonnen op de pagina zelf
	$referer_zoekenboek=1;
}

#$robot_noindex=true;
#$robot_nofollow=true;
#$vars["verberg_linkerkolom"]=true;

# Google Maps: zoeken op kaart
if($vars["websitetype"]==3 or $vars["websitetype"]==7) {
	$vars["googlemaps"]=true;
	$vars["zoeken_op_kaart"]=true;
}

if($vars["themainfo"]["tarievenbekend_seizoen_id"]) {
	$breadcrumbs["last"]=$vars["themainfo"]["naam"];
} else {
	$breadcrumbs["last"]=txt("title_zoekenboek");
}

$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
if($vars["websitetype"]<>6) {
	$vars["verfijnen_aanbieden"]=true;
}

# jQuery UI theme laden (t.b.v. autocomplete)
$vars["page_with_jqueryui"]=true;

# jQuery Chosen laden
$vars["jquery_chosen"]=true;

# Zoeken op accommodatiecode: redirect naar die accommodatie
if(ereg("^[A-Za-z]([0-9]+)$",trim($_GET["fzt"]),$regs) and !$_GET["saved"]) {
	$db->query("SELECT type_id, begincode FROM view_accommodatie WHERE type_id='".addslashes($regs[1])."' AND websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 AND wzt='".$vars["seizoentype"]."'");
	if($db->next_record()) {
		header("Location: ".$path."accommodatie/".$db->f("begincode").$db->f("type_id")."/");
		exit;
	}
}

# Zoeken op reserveringsnummer: redirect naar "Mijn boeking"
if(preg_match("/^[A-Za-z][0-9]{8}$/",trim($_GET["fzt"]),$regs)) {
	header("Location: ".$vars["path"].txt("menu_inloggen").".php");
	exit;
}

# tekstzoeken: zoekopdracht "opschonen"
if($_GET["fzt"]==txt("tekstzoeken","zoek-en-boek")) {
	# als tekstzoekopdracht bestaat uit de placeholder-tekst: tekstzoekopdracht wissen
	unset($_GET["fzt"]);
}
if($_GET["fzt"]=="-- ".html("trefwoord","index")." --") {
	# als tekstzoekopdracht bestaat uit de placeholder-tekst: tekstzoekopdracht wissen
	$_GET["fzt"]="";
}
if($_GET["fzt"]==html("zoekoptekst","index")) {
	# als tekstzoekopdracht bestaat uit de placeholder-tekst: tekstzoekopdracht wissen
	$_GET["fzt"]="";
}
if(strlen($_GET["fzt"])>0) {
	$_GET["fzt"]=trim($_GET["fzt"]);
}

if($_COOKIE["tch"] and !$voorkant_cms and ($vars["zoekform_aanbiedingen"] or strpos($_SERVER["REQUEST_URI"],txt("menu_zoek-en-boek")))) {
	#
	# Zoekopdracht bewaren
	#
	$te_bewaren_velden=array("fsg","fad","fadf_d","fadf_m","fadf_y","fap","fas","fzt","fdu");

	if(preg_match("/\?/",$_SERVER["REQUEST_URI"])) {
		# Zoekopdracht opslaan
		if(!$_GET["saved"]) {
			while(list($key,$value)=each($te_bewaren_velden)) {
				if($_GET[$value]) {
					$json_save[$value]=utf8_encode($_GET[$value]);
				}
			}
			if(isset($_GET["scrolly"]) and count($_GET)==1) {
				# Er is geklikt op "alles wissen": eerdere zoekopdracht uit database verwijderen
				$db->query("UPDATE bezoeker SET last_zoekopdracht=NULL, last_zoekopdracht_datetime=NULL, gewijzigd=NOW() WHERE bezoeker_id='".addslashes($_COOKIE["tch"])."';");
			} else {
				if($json_save) {
					$last_zoekopdracht=trim(json_encode($json_save));
					if($last_zoekopdracht=="null") {
						trigger_error("_notice: zoekopdracht='null'",E_USER_NOTICE);
					} else {
						$db->query("UPDATE bezoeker SET last_zoekopdracht='".addslashes($last_zoekopdracht)."', last_zoekopdracht_datetime=NOW(), gewijzigd=NOW() WHERE bezoeker_id='".addslashes($_COOKIE["tch"])."';");
					}
				}
			}
		}
	} else {
		# Eerdere zoekopdracht uit database halen
		$db->query("SELECT last_zoekopdracht, UNIX_TIMESTAMP(last_zoekopdracht_datetime) AS last_zoekopdracht_datetime FROM bezoeker WHERE bezoeker_id='".addslashes($_COOKIE["tch"])."';");
		if($db->next_record()) {
			if($db->f("last_zoekopdracht_datetime")>mktime(0,0,0,date("m"),date("d")-60,date("Y"))) {
				$json_load=json_decode($db->f("last_zoekopdracht"),true);
				if(is_array($json_load)) {
					$querystring="?filled=1&saved=1";
					while(list($key,$value)=each($te_bewaren_velden)) {
						if($json_load[$value]) {
							$querystring.="&".$value."=".urlencode(utf8_decode($json_load[$value]));
						}
					}
				}
				if($querystring) {
					$herlaad_url=$_SERVER["REQUEST_URI"];
					$herlaad_url=preg_replace("/\?.*/","",$herlaad_url);
					$herlaad_url.=$querystring;

					header("Location: ".$herlaad_url);
					exit;
				}
			}
		}
	}
}

if($_GET["fsg"] and !preg_match("/,/",$_GET["fsg"])) {
	if(preg_match("/^pl([0-9]+)$/",$_GET["fsg"],$regs)) {
		$db->query("SELECT naam FROM plaats WHERE plaats_id='".intval($regs[1])."';");
		if($db->next_record()) {
			$zoekresultaten_title=$db->f("naam");
		}
	} else {
		$landgebied=explode("-",$_GET["fsg"]);
		if($landgebied[1]>0) {
			$db->query("SELECT naam FROM skigebied WHERE skigebied_id='".addslashes($landgebied[1])."';");
			if($db->next_record()) {
				$zoekresultaten_title.=$db->f("naam");
			}
			if($vars["websitetype"]==7) {
				# topfoto Italissima bepalen
				$file="pic/cms/skigebieden_topfoto/".$landgebied[1].".jpg";

				if(file_exists($file)) {
					$vars["italissima_topfoto"]=$file;
				}
			}
		}
	}

	$db->query("SELECT naam".$vars["ttv"]." AS naam FROM land WHERE land_id='".addslashes($landgebied[0])."';");
	if($db->next_record()) {
		if($zoekresultaten_title) $zoekresultaten_title.=" - ".$db->f("naam"); else $zoekresultaten_title=$db->f("naam");
	}
#	addwhere("l.land_id='".addslashes($landgebied[0])."'");
#	if($landgebied[1]>0) addwhere("p.skigebied_id='".addslashes($landgebied[1])."'");
}

$vars["canonical"]=$vars["basehref"].txt("menu_zoek-en-boek").".php";

if($zoekresultaten_title) {
	$title["zoek-en-boek"].=" - ".$zoekresultaten_title;
}
if($vars["zoekform_aanbiedingen"]) {
	# zorgen dat title, breadcrumbs en canonical op "aanbiedingen" staan
	if($_GET["fad"]) {
		$title["zoek-en-boek"]=txt("title_aanbiedingen")." - ".ucfirst(txt("aankomst"))." ".weekend_voluit($_GET["fad"],false);
		$vars["canonical"]=$vars["basehref"].txt("menu_aanbiedingen").".php?d=".intval($_GET["fad"]);
		unset($breadcrumbs);
		$breadcrumbs[txt("menu_aanbiedingen").".php"]=txt("title_aanbiedingen");
		$breadcrumbs["last"]=ucfirst(txt("aankomst"))." ".weekend_voluit($_GET["fad"],false);
	} else {
		$title["zoek-en-boek"]=txt("title_aanbiedingen");
		$vars["canonical"]=$vars["basehref"].txt("menu_aanbiedingen").".php";
		$breadcrumbs["last"]=txt("title_aanbiedingen");
	}
}

if($vars["websitetype"]<>6) {
	$laat_titel_weg=true;
}

include "content/opmaak.php";

?>