<?php

$vars["jquery_fancybox"]=true;
$id="toonplaats";
$vars["page_with_tabs"]=true;

include("admin/vars.php");


// if(get_magic_quotes_gpc()) {
// 	$url[0]=stripslashes($url[0]);
// }
// $url[0]=urldecode($url[0]);

if($_GET["naamplaats"]) {
	$url[0]=$_GET["naamplaats"];
}

if($vars["websitetype"]==6) {
	if($url[0]=="Vallandry-Winter") {
		# Vallandry: winterinfo tonen
		$vars["seizoentype"]=1;
		$url[0]="Vallandry";
	} else {
		# Vallandry: zomerinfo tonen
		$vars["seizoentype"]=2;
	}
}

if($url[0]) {
	$vars["canonical"]=$vars["basehref"].txt("menu_plaats")."/".$url[0]."/";
} else {
#	if($_SERVER["HTTP_REFERER"]) trigger_error("plaats niet beschikbaar",E_USER_NOTICE);
	header("Location: ".$path.txt("menu_accommodaties").".php");
	exit;
}

$plaatsid=0;
$db->query("SELECT plaats_id, naam, descriptiontag".$vars["ttv"]." AS descriptiontag FROM plaats WHERE wzt='".addslashes($vars["seizoentype"])."';");
while($db->next_record()) {
	if(wt_convert2url_oud($url[0])<>wt_convert2url($url[0])) {
		# heel oude url's forwarden
		header("Location: ".$path.txt("menu_plaats")."/".wt_convert2url_seo($url[0]),true,301);
		exit;
	} elseif(preg_match("/_/",$url[0])) {
		# minder oude url's forwarden
		header("Location: ".$path.txt("menu_plaats")."/".preg_replace("/_/","-",$url[0])."/",true,301);
		exit;
	} elseif(wt_convert2url_seo($url[0])==wt_convert2url_seo($db->f("naam"))) {
		$plaatsid=$db->f("plaats_id");

		# meta-tag description
		if($db->f("descriptiontag")) {
			$meta_description=$db->f("descriptiontag");
		}
		break;
	} elseif(strtolower(wt_convert2url_seo($url[0]))==strtolower(wt_convert2url_seo($db->f("naam")))) {
		header("Location: ".$vars["path"].txt("menu_plaats")."/".wt_convert2url_seo($db->f("naam"))."/",true,301);
		exit;
	}
}

if($plaatsid) {
	if($voorkant_cms) {
		# Ook bij geen accommodaties: gegevens ophalen
		$db->query("SELECT p.naam, s.naam AS skigebied, l.naam".$vars["ttv"]." AS land, l.land_id, s.skigebied_id FROM plaats p, skigebied s, land l WHERE p.plaats_id='".addslashes($plaatsid)."' AND p.land_id=l.land_id AND s.skigebied_id=p.skigebied_id AND p.wzt='".addslashes($vars["seizoentype"])."';");
	} else {
		# Alleen bij accommodaties: gegevens ophalen
		$db->query("SELECT p.naam, s.naam AS skigebied, l.naam".$vars["ttv"]." AS land, l.land_id, s.skigebied_id FROM plaats p, skigebied s, land l, accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.plaats_id='".addslashes($plaatsid)."' AND p.land_id=l.land_id AND s.skigebied_id=p.skigebied_id AND p.wzt='".addslashes($vars["seizoentype"])."' AND a.tonen=1 AND t.tonen=1 AND a.archief=0;");
	}

	if($db->next_record()) {


		# TIJDELIJK: Zomerhuisje: regio's die niet op bestemmingenpagina staan: 301-forward naar bestemmingenpagina
		if($vars["website"]=="Z") {
			if(!file_exists("pic/cms/bestemmingen_zomerhuisje/".intval($db->f("skigebied_id")).".jpg")) {
				header("Location: ".$vars["path"]."bestemmingen.php",true,301);
				exit;
			}
		}

		if($vars["seizoentype"]==1) {
			$header["toonplaats"]=txt("wintersport","toonplaats")." ".$db->f("naam");
			// $title["toonplaats"]=txt("wintersport","toonplaats")." ".$db->f("naam").", ".$db->f("skigebied")." - ".$db->f("land");
			if($db->f("skigebied")==$db->f("naam")) {
				$title["toonplaats"]=txt("chaletsvoorwintersportin","vars",array("v_land"=>$db->f("naam").", ".$db->f("land")));
			} else {
				$title["toonplaats"]=txt("chaletsvoorwintersportin","vars",array("v_land"=>$db->f("naam").", ".$db->f("skigebied").", ".$db->f("land")));
			}
			// $title["toonplaats"]=txt("chaletsvoorwintersportin","vars",array("v_land"=>$db->f("naam")));

			$breadcrumbs[txt("menu_skigebieden").".php"]=ucfirst(txt("menu_skigebieden"));
			$breadcrumbs[txt("menu_land")."/".wt_convert2url_seo($db->f("land"))."/"]=$db->f("land");
			$breadcrumbs[txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("skigebied"))."/"]=$db->f("skigebied");
			$breadcrumbs["last"]=$db->f("naam");
		} elseif($vars["websitetype"]==7) {
			$header["toonplaats"]=$db->f("naam");
			$title["toonplaats"]=$db->f("naam").", ".$db->f("skigebied")." - ".$db->f("land");

			$breadcrumbs["bestemmingen.php"]="Bestemmingen";
			$breadcrumbs[txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("skigebied"))."/"]=$db->f("skigebied");
			$breadcrumbs["last"]=$db->f("naam");

		} else {
			$header["toonplaats"]=$db->f("naam");
			$title["toonplaats"]=$db->f("naam").", ".$db->f("skigebied")." - ".$db->f("land");

			$breadcrumbs[txt("menu_bestemmingen").".php"]=ucfirst(txt("menu_bestemmingen"));
			$breadcrumbs[txt("menu_land")."/".wt_convert2url_seo($db->f("land"))."/"]=$db->f("land");
			$breadcrumbs[txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("skigebied"))."/"]=$db->f("skigebied");
			$breadcrumbs["last"]=$db->f("naam");

		}
		if(!$meta_description) {
			if($vars["taal"]=="en") {
				$meta_description="Overview of our accommodations in ".$db->f("naam").", ".$db->f("skigebied").", ".$db->f("land");
			} else {
				$meta_description="Overzicht van onze accommodaties in ".$db->f("naam").", ".$db->f("skigebied").", ".$db->f("land");
			}
		}

		if($vars["websitetype"]==7) {
			# topfoto bepalen
			$file="pic/cms/skigebieden_topfoto/".$db->f("skigebied_id").".jpg";

			if(file_exists($file)) {
				$vars["italissima_topfoto"]=$file;
			}
		}
		$skigebiedid=$db->f("skigebied_id");

		# Tijdelijk: doorsturen naar Italissima
		if($db->f("land_id")==5 and $db->f("skigebied_id")<>135 and $db->f("skigebied_id")<>142 and $db->f("skigebied_id")<>168 and ($vars["website"]=="Z" or $vars["website"]=="N")) {
			header("Location: http://www.italissima.nl".$_SERVER["REQUEST_URI"],true,301);
			exit;
		}
		$toplinks="<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url_seo($db->f("land"))."/\">".htmlentities($db->f("land"))."</a> - <a href=\"".$vars["path"].txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("skigebied"))."/#beschrijving\">".htmlentities($db->f("skigebied"))."</a> - ".htmlentities($db->f("naam"));

	} else {
		$db->query("SELECT s.naam FROM plaats p, skigebied s WHERE p.plaats_id='".addslashes($plaatsid)."' AND p.wzt='".addslashes($vars["seizoentype"])."' AND p.skigebied_id=s.skigebied_id;");
		if($db->next_record()) {
			$url=$vars["path"].txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("naam"))."/";
			header("Location: ".$url,true,301);
			exit;
		} else {
			$url=$vars["path"].txt("menu_zoek-en-boek").".php";
			header("Location: ".$url,true,301);
			exit;
		}
	}
} else {
	$db->query("SELECT p.naam AS nieuwenaam, c.previous FROM plaats p, cmslog c WHERE c.table_name='plaats' AND c.field='naam' AND c.previous<>'' AND c.record_id=p.plaats_id AND p.wzt='".addslashes($vars["seizoentype"])."' ORDER BY c.savedate DESC;");
	while($db->next_record()) {
		if(wt_convert2url_seo($db->f("previous"))==wt_convert2url_seo($url[0])) {
			header("Location: ".$path.txt("menu_plaats")."/".wt_convert2url_seo($db->f("nieuwenaam"))."/",true,301);
			exit;
		}
	}
	header("HTTP/1.0 404 Not Found");
	unset($vars["canonical"]);
	$robot_noindex=true;
	// trigger_error("_notice: plaats niet beschikbaar",E_USER_NOTICE);
}

# Foto en landkaart
$pic1="plaatsen/".$plaatsid."-1.jpg";
$dir="pic/cms/plaatsen/";
if(file_exists($dir)) {
	$d=dir($dir);
	while($entry=$d->read()) {
		if(ereg("^".$plaatsid."-([0-9]{1,3})\.jpg$",$entry,$regs)) {
			$pic1="plaatsen/".$plaatsid."-".$regs[1].".jpg";
			break;
		}
	}
}

$pic2="plaatsen_landkaarten/".$plaatsid.".gif";
if(file_exists("pic/cms/".$pic1) and file_exists("pic/cms/".$pic2)) $laat_titel_weg=true;

include("content/opmaak.php");

?>