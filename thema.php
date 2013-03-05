<?php


$laat_titel_weg=true;
$vars["verberg_zoekenboeklinks"]=true;

include("admin/vars.php");

# Bij WSA en Italissima zijn geen thema's beschikbaar
if($vars["website"]=="W" or $vars["websitetype"]==7) {
	header("Location: ".$vars["path"]);
	exit;
}

# jQuery UI theme laden (t.b.v. autocomplete)
$vars["page_with_jqueryui"]=true;

# jQuery Chosen laden
$vars["jquery_chosen"]=true;

# Zoeken op accommodatiecode: redirect naar die accommodatie
if(ereg("^[A-Za-z]([0-9]+)$",trim($_GET["fzt"]),$regs)) {
	$db->query("SELECT type_id, begincode FROM view_accommodatie WHERE type_id='".addslashes($regs[1])."' AND websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 AND wzt='".$vars["seizoentype"]."'");
	if($db->next_record()) {
		header("Location: ".$path."accommodatie/".$db->f("begincode").$db->f("type_id")."/");
		exit;
	}
}

if($vars["seizoentype"]==1) {
	# winter

} elseif($vars["website"]=="Z") {
	# zomer

}

if(substr($_SERVER["REQUEST_URI"],-1)<>"/" and !eregi("\?",$_SERVER["REQUEST_URI"]) and !eregi("#",$_SERVER["REQUEST_URI"])) {
	header("Location: ".$_SERVER["REQUEST_URI"]."/",true,301);
	exit;
}

$db->query("SELECT thema_id, naam".$vars["ttv"]." AS naam, titletag".$vars["ttv"]." AS titletag, descriptiontag".$vars["ttv"]." AS descriptiontag, url".$vars["ttv"]." AS url, typekenmerk, accommodatiekenmerk, plaatskenmerk, skigebiedkenmerk, zoekterm, tarievenbekend_seizoen_id, uitgebreidzoeken_url FROM thema WHERE wzt='".addslashes($vars["seizoentype"])."' AND url".$vars["ttv"]."='".addslashes($_GET["thema"])."' AND actief=1;");
if($db->next_record()) {
	if($db->f("titletag")) {
		$title["thema"]=$db->f("titletag");
	} else {
		$title["thema"]=$db->f("naam");
	}
	$vars["themainfo"]["naam"]=$db->f("naam");
	$vars["themainfo"]["descriptiontag"]=$db->f("descriptiontag");
	$vars["themainfo"]["thema_id"]=$db->f("thema_id");
	$vars["themainfo"]["typekenmerk"]=$db->f("typekenmerk");
	$vars["themainfo"]["accommodatiekenmerk"]=$db->f("accommodatiekenmerk");
	$vars["themainfo"]["plaatskenmerk"]=$db->f("plaatskenmerk");
	$vars["themainfo"]["skigebiedkenmerk"]=$db->f("skigebiedkenmerk");
	$vars["themainfo"]["zoekterm"]=$db->f("zoekterm");
	$vars["themainfo"]["tarievenbekend_seizoen_id"]=$db->f("tarievenbekend_seizoen_id");
	$vars["themainfo"]["uitgebreidzoeken_url"]=$db->f("uitgebreidzoeken_url");

	# breadcrumbs
	$breadcrumbs[txt("menu_themas").".php"]=txt("title_themas");
	$breadcrumbs["last"]=$db->f("naam");

	# meta-tag description
	if($vars["themainfo"]["descriptiontag"]) {
		$meta_description=$vars["themainfo"]["descriptiontag"];
	}

	$vars["canonical"]=$vars["path"].txt("canonical_accommodatiepagina")."/".txt("menu_thema")."/".$db->f("url")."/";

	if($vars["seizoentype"]==2) {
		$db->query("SELECT thema_id, naam".$vars["ttv"]." AS naam, toelichting".$vars["ttv"]." AS toelichting, uitgebreid".$vars["ttv"]." AS uitgebreid, kleurcode, accommodatiecodes FROM thema WHERE wzt=2 AND url".$vars["ttv"]."='".addslashes($_GET["thema"])."';");
		if($db->next_record()) {
			$themalandinfo["soort"]="thema";
			$themalandinfo["accommodatiecodes"]=$db->f("accommodatiecodes");
			$themalandinfo["kleurcode"]=$db->f("kleurcode");
			$themalandinfo["id"]=$db->f("thema_id");
			$themalandinfo["toelichting"]=$db->f("toelichting");
			$themalandinfo["naam"]=$db->f("naam");
			$themalandinfo["uitgebreid"]=$db->f("uitgebreid");
			$themalandinfo["padhoofdafbeelding"]="themas_hoofdpagina";
		}

		# Foto's bepalen
		$dir="pic/cms/themas/";
		$d=dir($dir);
		while($entry=$d->read()) {
			if(ereg("^".$db->f("thema_id")."-([0-9]{1,3})\.jpg$",$entry,$regs)) {
				$vars["topfoto"][]="pic/cms/themas/".$entry;
			}
		}
		@asort($vars["topfoto"]);
	}
} else {
	header("Location: ".$vars["path"].txt("menu_themas").".php".wt_convert2url($url[0]));
	exit;
}

if($vars["themainfo"]["tarievenbekend_seizoen_id"]) {
	# $vars["aankomstdatum_weekend"] opnieuw vullen (met het specifieke seizoen voor dit thema)
	unset($vars["aankomstdatum_weekend"]);
	$db->query("SELECT UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind, seizoen_id FROM seizoen WHERE seizoen_id='".$vars["themainfo"]["tarievenbekend_seizoen_id"]."' ORDER BY begin, eind;");
	if($db->num_rows()) {
		$vars["aankomstdatum_weekend"][0]=$vars["geenvoorkeur"];
		while($db->next_record()) {
			# Aankomstdatum-array vullen
			$timeteller=$db->f("begin");
			while($timeteller<=$db->f("eind")) {
				# aankomstdatum_weekend vullen (alleen indien niet langer dan 8 dagen geleden)
				if($timeteller>=time()-691200) {
					$vars["aankomstdatum_weekend"][$timeteller]=txt("weekend","vars")." ".date("j",$timeteller)." ".datum("MAAND JJJJ",$timeteller,$vars["taal"]);
				}
				$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
			}
		}
		@ksort($vars["aankomstdatum_weekend"]);
	}
}

include "content/opmaak.php";

?>