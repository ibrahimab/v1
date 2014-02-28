<?php

include("admin/vars.php");
if($vars["seizoentype"]==1) {
	header("Location: ".$vars["path"]);
	exit;
}

if(!preg_match("/\?/",$_SERVER["REQUEST_URI"]) and substr($_SERVER["REQUEST_URI"],-1)<>"/") {
	header("Location: ".$_SERVER["REQUEST_URI"]."/",true,301);
	exit;
}

if($vars["websitetype"]==7) {
	$land["id"]=5;
	$land["naam"] = txt("italie", "index");
	$title["aanbiedingen_zomerhuisje"] = txt("aanbiedingen-italie", "aanbiedingen");
} else {
	$land["id"]=0;
	$db->query("SELECT land_id, naam".$vars["ttv"]." AS naam FROM land WHERE zomertonen=1;");
	while($db->next_record()) {
		if(wt_convert2url_seo($db->f("naam"))==wt_convert2url_seo($_GET["land"])) {
			$land["id"]=$db->f("land_id");
			$land["naam"]=$db->f("naam");
			$title["aanbiedingen_zomerhuisje"] = txt("aanbiedingen", "aanbiedingen")." ".$db->f("naam");

			$breadcrumbs[txt("menu_aanbiedingen")."/"] = txt("aanbiedingen", "aanbiedingen");
			$breadcrumbs["last"] = $db->f("naam");

		}
	}
}

if($vars["seizoentype"]==2 and $_GET["aid"]) {
	# Gegevens van 1 specifieke aanbieding ophalen
	$db->query("SELECT onlinenaam".$vars["ttv"]." AS onlinenaam FROM aanbieding WHERE wzt='".addslashes($vars["seizoentype"])."' AND aanbieding_id='".addslashes($_GET["aid"])."' AND accommodaties_beschikbaar=1 AND tonen=1;");
	if($db->next_record()) {
		$title["aanbiedingen_zomerhuisje"]="Aanbieding ".$land["naam"]." - ".$db->f("onlinenaam");

		unset($breadcrumbs);
		$breadcrumbs[txt("menu_aanbiedingen")."/"] = txt("aanbiedingen", "aanbiedingen");
		$breadcrumbs[txt("menu_aanbiedingen")."/".wt_convert2url_seo($land["naam"])."/"]=$land["naam"];
		$breadcrumbs["last"]=$db->f("onlinenaam");

	} else {
		if($land["naam"]) {
			header("Location: ".$vars["path"].txt("menu_aanbiedingen")."/".wt_convert2url_seo($land["naam"])."/",true,301);
		} else {
			header("Location: ".$vars["path"].txt("menu_aanbiedingen")."/",true,301);
		}
		exit;
	}
}

if(!$title["aanbiedingen_zomerhuisje"]) $title["aanbiedingen_zomerhuisje"] = txt("aanbiedingen", "aanbiedingen");

include "content/opmaak.php";

?>