<?php

$vars["jquery_fancybox"]=true;
$id="toonskigebied";
$vars["page_with_tabs"]=true;

include("admin/vars.php");

if($vars["websitetype"]==7) {
	# Bij Italissima: geen titel tonen
	$laat_titel_weg=true;

	$vars["googlemaps"]=true;
	$data_onload="initialize_googlemaps";
}

// #if($url[1]) $url[0].=$url[1];
// if(get_magic_quotes_gpc()) {
// 	$url[0]=stripslashes($url[0]);
// }

// $url[0]=urldecode($url[0]);

if($_GET["naamskigebied"]) {
	$url[0]=$_GET["naamskigebied"];
}


if($url[0]) {
	$vars["canonical"]=$vars["basehref"].txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".$url[0]."/";
} else {
#	if($_SERVER["HTTP_REFERER"]) trigger_error("skigebied/regio niet beschikbaar",E_USER_NOTICE);
	header("Location: ".$path.txt("menu_skigebieden").".php");
	exit;
}

$skigebiedid=0;
$db->query("SELECT skigebied_id, naam AS naam_org, naam".$vars["ttv"]." AS naam, descriptiontag".$vars["ttv"]." AS descriptiontag FROM skigebied WHERE wzt='".addslashes($vars["seizoentype"])."';");
while($db->next_record()) {
	if(wt_convert2url_oud($url[0])<>wt_convert2url($url[0]) and wt_convert2url($db->f("naam"))==wt_convert2url($url[0])) {
		# heel oude url's forwarden
		header("Location: ".$path.txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($url[0]),true,301);
		exit;
	} elseif(preg_match("/_/",$url[0])) {
		# minder oude url's forwarden
		header("Location: ".$path.txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".preg_replace("/_/","-",$url[0])."/",true,301);
		exit;
	} elseif(strpos("-".$_SERVER["REQUEST_URI"],txt("canonical_accommodatiepagina"))===false) {
		# url's zonder forwarden
		header("Location: ".$path.txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".preg_replace("/_/","-",$url[0])."/",true,301);
		exit;
	} elseif(wt_convert2url_seo($url[0])==wt_convert2url_seo($db->f("naam"))) {
		$skigebiedid=$db->f("skigebied_id");
		$skigebiednaam=$db->f("naam");

		# meta-tag description
		if($db->f("descriptiontag")) {
			$meta_description=$db->f("descriptiontag");
		}

		if($vars["websitetype"]==7) {
			# topfoto bepalen
			$file="pic/cms/skigebieden_topfoto/".$db->f("skigebied_id").".jpg";

			if(file_exists($file)) {
				$vars["italissima_topfoto"]=$file;
			}
		}

		break;
	} elseif(strtolower(wt_convert2url_seo($url[0]))==strtolower(wt_convert2url_seo($db->f("naam")))) {
		header("Location: ".$path.txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("naam"))."/",true,301);
		exit;
	} elseif($vars["ttv"] and $db->f("naam_org")<>$db->f("naam") and strtolower(wt_convert2url_seo($url[0]))==strtolower(wt_convert2url_seo($db->f("naam_org")))) {
		// non-English region name: redirect to English name
		header("Location: ".$path.txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("naam"))."/",true,301);
		exit;
	}
}

if($skigebiedid) {
	if($voorkant_cms) {
		# land bepalen
		# Ook bij geen accommodaties: gegevens ophalen
		$db->query("SELECT naam".$vars["ttv"]." AS naam FROM skigebied WHERE skigebied_id='".addslashes($skigebiedid)."' AND wzt='".addslashes($vars["seizoentype"])."';");
	} else {
		# Alleen bij accommodaties: gegevens ophalen
		$db->query("SELECT skigebied".$vars["ttv"]." AS naam, land".$vars["ttv"]." AS land, land_id FROM view_accommodatie WHERE skigebied_id='".addslashes($skigebiedid)."' AND wzt='".addslashes($vars["seizoentype"])."' AND atonen=1 AND ttonen=1 AND archief=0 AND websites LIKE '%".$vars["website"]."%';");
	}
	if($db->next_record()) {


		# TIJDELIJK: Zomerhuisje: regio's die niet op bestemmingenpagina staan: 301-forward naar bestemmingenpagina
		if($vars["website"]=="Z") {
			if(!file_exists("pic/cms/bestemmingen_zomerhuisje/".intval($skigebiedid).".jpg")) {
				header("Location: ".$vars["path"]."bestemmingen.php",true,301);
				exit;
			}
		}

		if($vars["seizoentype"]==1) {
			// $title["toonskigebied"]=txt("title_toonskigebied")." ".$db->f("naam");
			$title["toonskigebied"]=txt("chaletsvoorwintersportin","vars",array("v_land"=>$db->f("naam")));
			if(!$meta_description) {
				$meta_description=txt("title_toonskigebied")." ".$db->f("naam")." in ".$db->f("land");
			}
		} else {
			if($vars["websitetype"]==7) {
				$title["toonskigebied"]=txt("je-ideale-agriturismo-in","vars",array("v_locatie"=>$db->f("naam")));
			} else {
				$title["toonskigebied"]=ucfirst(txt("vakantiehuizen"))." ".$db->f("naam");
			}
			if(!$meta_description) {
				if($vars["taal"]=="en") {
					$meta_description="Overview of our accommodations in ".$db->f("naam").", ".$db->f("land");
				} else {
					$meta_description="Overzicht van ons aanbod in ".$db->f("naam").", ".$db->f("land");
				}
			}
		}

		# Tijdelijk: doorsturen naar Italissima
		// if($db->f("land_id")==5 and $skigebiedid<>135 and $skigebiedid<>142 and $skigebiedid<>168 and ($vars["website"]=="Z" or $vars["website"]=="N")) {
		// 	header("Location: https://www.italissima.nl".$_SERVER["REQUEST_URI"],true,301);
		// 	exit;
		// }
	} else {
		$db->query("SELECT DISTINCT l.naam".$vars["ttv"]." AS land FROM plaats p, land l WHERE l.land_id=p.land_id AND p.skigebied_id=".$skigebiedid.";");
		if($db->next_record()) {
			$url=$vars["path"].txt("menu_land")."/".wt_convert2url_seo($db->f("land"))."/";
			header("Location: ".$url,true,301);
			exit;
		} else {
			$url=$vars["path"].txt("menu_zoek-en-boek").".php";
			header("Location: ".$url,true,301);
			exit;
		}
	}
} else {
	$db->query("SELECT s.naam AS nieuwenaam, c.previous FROM skigebied s, cmslog c WHERE c.table_name='skigebied' AND c.field='naam' AND c.previous<>'' AND c.record_id=s.skigebied_id AND s.wzt='".addslashes($vars["seizoentype"])."' ORDER BY c.savedate DESC;");
	while($db->next_record()) {
		if(wt_convert2url_seo($db->f("previous"))==wt_convert2url_seo($url[0])) {
			header("Location: ".$path.txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("nieuwenaam"))."/",true,301);
			exit;
		}
	}
	header("HTTP/1.0 404 Not Found");
	unset($vars["canonical"]);
	$robot_noindex=true;
	// trigger_error("_notice: skigebied/regio niet beschikbaar",E_USER_NOTICE);
}

# Foto en landkaart
$pic1="skigebieden/".$skigebiedid."-1.jpg";
$dir="pic/cms/skigebieden/";
if(file_exists($dir)) {
	$d=dir($dir);
	while($entry=$d->read()) {
		if(ereg("^".$skigebiedid."-([0-9]{1,3})\.jpg$",$entry,$regs)) {
			$pic1="skigebieden/".$skigebiedid."-".$regs[1].".jpg";
			break;
		}
	}
}

$pic2="skigebieden_landkaarten/".$skigebiedid.".gif";

include("content/opmaak.php");

?>