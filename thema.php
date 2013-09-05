<?php


$laat_titel_weg=true;
$vars["verberg_zoekenboeklinks"]=true;

include("admin/vars.php");

# Bij Italissima, SuperSki en Venturasol zijn geen thema's beschikbaar
if(($vars["websitetype"]==7 or $vars["websitetype"]==8 or $vars["websitetype"]==9) and $_GET["thema"]<>"2013-2014" and $_GET["thema"]<>"2014-2015") {
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

if(substr($_SERVER["REQUEST_URI"],-1)<>"/" and !eregi("\?",$_SERVER["REQUEST_URI"]) and !eregi("#",$_SERVER["REQUEST_URI"])) {
	header("Location: ".$_SERVER["REQUEST_URI"]."/",true,301);
	exit;
}

$db->query("SELECT thema_id, naam".$vars["ttv"]." AS naam, titletag".$vars["ttv"]." AS titletag, descriptiontag".$vars["ttv"]." AS descriptiontag, url".$vars["ttv"]." AS url, typekenmerk, accommodatiekenmerk, plaatskenmerk, skigebiedkenmerk, zoekterm, tarievenbekend_seizoen_id, uitgebreidzoeken_url, toelichting".$vars["ttv"]." AS toelichting, titelhoofdpagina".$vars["ttv"]." AS titelhoofdpagina FROM thema WHERE wzt='".addslashes($vars["seizoentype"])."' AND url".$vars["ttv"]."='".addslashes($_GET["thema"])."' AND actief=1;");
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
	$vars["themainfo"]["toelichting"]=$db->f("toelichting");
	$vars["themainfo"]["titelhoofdpagina"]=$db->f("titelhoofdpagina");

	# thema-kenmerk: via URL in plaats van "Gekoppelde kenmerken"
	if($vars["themainfo"]["uitgebreidzoeken_url"] and !$vars["themainfo"]["typekenmerk"] and !$vars["themainfo"]["accommodatiekenmerk"] and !$vars["themainfo"]["plaatskenmerk"] and !$vars["themainfo"]["skigebiedkenmerk"] and !$vars["themainfo"]["zoekterm"] and !$vars["themainfo"]["tarievenbekend_seizoen_id"]) {
		if(preg_match("/^(vf_[a-z0-9]+)=(.*)$/",$vars["themainfo"]["uitgebreidzoeken_url"],$regs)) {
			$_GET[$regs[1]]=$regs[2];
			$vars["themainfo"]["kenmerken_via_url"]=true;
		}
	}

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
	header("Location: ".$vars["path"].txt("menu_themas").".php");
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

	# Totaal aantal accommodaties opvragen: kan getoond worden bij "tarievenbekend_seizoen_id"
	$db->query("SELECT DISTINCT COUNT(t.type_id) AS aantal FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".addslashes($vars["website"])."%' AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0;");
	if($db->next_record()) {
		$totaal_tarievenbekend_seizoen_id=$db->f("aantal");
	}

	$analytics_zoekopdracht=$vars["themainfo"]["titelhoofdpagina"];
	$vars["zoekform_thema"]=true;
	$id="zoek-en-boek";
	include("zoek-en-boek.php");
	exit;
}

include "content/opmaak.php";

?>