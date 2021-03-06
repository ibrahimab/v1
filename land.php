<?php


include("admin/vars.php");

if($vars["websitetype"]==7) {
	# geen landenpagina bij Italissima (want: alleen Italië)
	header("Location: ".$vars["path"]."bestemmingen.php",true,301);
	exit;
}

$land = '';
if ($_GET['land']) {
	$land = $_GET['land'];
}

// 301 if no txt("canonical_accommodatiepagina")
if ($vars['websitetype'] !== 3) {

	if (!preg_match('@' . txt('canonical_accommodatiepagina') . '@', $_SERVER['REQUEST_URI'])) {

		$new_url = $vars['basehref'] . txt('canonical_accommodatiepagina') . '/' . txt('menu_land') . '/' . $land . '/';

		if(preg_match('@(\?.*)@', $_SERVER['REQUEST_URI'], $regs)) {
			$new_url .= $regs[1];
		}

		header('Location: ' . $new_url, true, 301);
		exit;
	}
}

$laat_titel_weg=true;

$plaatsid=0;
$db->query("SELECT land_id, naam".$vars["ttv"]." AS naam, titel".$vars["ttv"]." AS titel, descriptiontag".$vars["ttv"]." AS descriptiontag, zomerdescriptiontag".$vars["ttv"]." AS zomerdescriptiontag, omschrijving".$vars["ttv"]." AS omschrijving, omschrijving_openklap".$vars["ttv"]." AS omschrijving_openklap, zomeromschrijving_openklap, praktischeinfo".$vars["ttv"]." AS praktischeinfo, accommodatiecodes, kleurcode, zomeromschrijving".$vars["ttv"]." AS zomeromschrijving FROM land;");
while($db->next_record()) {
	if(wt_convert2url_seo($db->f("naam"))==wt_convert2url_seo($_GET["land"])) {
		$landinfo["id"]=$db->f("land_id");
		$landinfo["naam"]=$db->f("naam");
		$landinfo["titel"]=$db->f("titel");
		$landinfo["omschrijving"]=$db->f("omschrijving");
		if($vars["seizoentype"]==2) {
			$landinfo["omschrijving_openklap"]=$db->f("zomeromschrijving_openklap");
		} else {
			$landinfo["omschrijving_openklap"]=$db->f("omschrijving_openklap");
		}
		$landinfo["praktischeinfo"]=$db->f("praktischeinfo");

		if($vars["seizoentype"]==2) {
			$breadcrumbs[txt("menu_bestemmingen").".php"]=ucfirst(txt("menu_bestemmingen"));
		} else {
			$breadcrumbs[txt("menu_skigebieden").".php"]=ucfirst(txt("menu_skigebieden"));
		}
		$breadcrumbs["last"]=$db->f("naam");

		if(ereg("\?",$_SERVER["REQUEST_URI"])) {
			$vars["canonical"]=ereg_replace("\?.*","",$_SERVER["REQUEST_URI"]);
		}

		# meta-tag description
		if($vars["seizoentype"]==2) {
			if($db->f("zomerdescriptiontag")) {
				$meta_description=$db->f("zomerdescriptiontag");
			}
		} else {
			if($db->f("descriptiontag")) {
				$meta_description=$db->f("descriptiontag");
			}
		}


		if($vars["seizoentype"]==2) {
			$themalandinfo["soort"]="land";
			$themalandinfo["accommodatiecodes"]=$db->f("accommodatiecodes");
			$themalandinfo["kleurcode"]=$db->f("kleurcode");
			$themalandinfo["id"]=$db->f("land_id");
			$themalandinfo["toelichting"]=$db->f("zomeromschrijving");
			$themalandinfo["naam"]=$db->f("naam");
			$themalandinfo["padhoofdafbeelding"]="zomerlanden";

			$vars["extracss"][]="thema_zomerhuisje.css";

			# Foto's bepalen
			$dir="pic/cms/zomerlanden_top/";
			$d=dir($dir);
			while($entry=$d->read()) {
				if(ereg("^".$db->f("land_id")."-([0-9]{1,3})\.jpg$",$entry,$regs)) {
					$vars["topfoto"][]="pic/cms/zomerlanden_top/".$entry;
				}
			}
			@asort($vars["topfoto"]);
		}
		break;
	}
}
#if($landinfo["titel"] and $vars["seizoentype"]==1) {
#	$title["land"]=$landinfo["titel"];
#} else {
#	$title["land"]=$landinfo["naam"];
#}
if($vars["seizoentype"]==1) {
	$title["land"]=txt("wintersport-chalets-appartementen","vars",array("v_land"=>$landinfo["naam"]));
} else {
	$title["land"]=ucfirst(txt("vakantiehuizen"))." ".$landinfo["naam"];
	$vars["jquery_maphilight"]=true;
}

if($landinfo["naam"] and !$meta_description) {
	if($vars["taal"]=="en") {
		$meta_description="Overview of our accommodations in ".$landinfo["naam"];
	} else {
		$meta_description="Overzicht van onze accommodaties in ".$landinfo["naam"];
	}
}

if($vars["websitetype"]==3 and $themalandinfo["naam"]=="Spanje") {
	# Zomerhuisje: geen landenpagina Spanje meer (geen accommodaties in Spanje)
	header("Location: ".$vars["path"]."bestemmingen.php",true,301);
	exit;
}


include "content/opmaak.php";

?>