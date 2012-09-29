<?php

include("admin/vars.php");


if($vars["zoekform_aanbiedingen"] and $vars["seizoentype"]==2) {
	# Zomer: doorsturen naar zomer-aanbiedingensysteem
	header("Location: ".$vars["path"].txt("menu_aanbiedingen")."/",true,301);
	exit;
}


#$robot_noindex=true;
#$robot_nofollow=true;
#$vars["verberg_linkerkolom"]=true;

$breadcrumbs["last"]=txt("title_zoekenboek");

$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
if($vars["websitetype"]<>6) {
	$vars["verfijnen_aanbieden"]=true;
}

# jQuery UI theme laden (t.b.v. autocomplete)
$vars["page_with_jqueryui"]=true;

if($_GET["fzt"]=="-- ".html("trefwoord","index")." --") {
	$_GET["fzt"]="";
}

if(ereg("^[A-Za-z]([0-9]+)$",trim($_GET["fzt"]),$regs)) {
	$db->query("SELECT t.type_id, l.begincode FROM type t, accommodatie a, plaats p, land l WHERE t.type_id='".addslashes($regs[1])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id;");
	if($db->next_record()) {
		header("Location: ".$path."accommodatie/".$db->f("begincode").$db->f("type_id")."/");
		exit;
	}
}

# Zoekopdracht_id aanmaken (en plaatsen in $_GET["z"])
if($_GET["filled"] and !$vars["zoekform_aanbiedingen"]) {
	if(!$voorkant_cms and !in_array($_SERVER["REMOTE_ADDR"],$vars["vertrouwde_ips"])) {
		if($_GET["z"]) {
			$zoekopdrachtid=intval($_GET["z"]);
		} else {
			$db->query("INSERT INTO zoekstatistiek SET wzt='".$vars["seizoentype"]."', website='".$vars["website"]."', url='".addslashes("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."', datumtijd=NOW();");
			if($db->insert_id()) {
				header("Location: ".$_SERVER["REQUEST_URI"]."&z=".$db->insert_id());
				exit;
			}
		}
	}
	
#	if($_GET["z"]) {
#		$vars["canonical"]=ereg_replace("&z=[0-9]+","",$_SERVER["REQUEST_URI"]);
#	}
}

if($_GET["fsg"]>0) {
	$landgebied=explode("-",$_GET["fsg"]);
	if($landgebied[1]>0) {
		$db->query("SELECT naam FROM skigebied WHERE skigebied_id='".addslashes($landgebied[1])."';");
		if($db->next_record()) {
			$zoekresultaten_title.=$db->f("naam");
		}
		if($vars["websitetype"]==7) {
			# topfoto bepalen
			$file="pic/cms/skigebieden_topfoto/".$landgebied[1].".jpg";
	
			if(file_exists($file)) {
				$vars["italissima_topfoto"]=$file;
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

if($vars["websitetype"]==1 or $vars["websitetype"]==4) {
	$laat_titel_weg=true;
}

include "content/opmaak.php";

?>