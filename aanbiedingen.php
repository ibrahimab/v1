<?php

if(!$_GET["nocache"]) {
	# nieuw aanbiedingensysteem (via zoekformulier)
	$vars["zoekform_aanbiedingen"]=true;
	$otherid="zoek-en-boek";
	$page_uniqueID = "offerspage";
	$vars["active_menu_item"]="aanbiedingen";
	$_GET["aab"]=1;
	if(!$_GET["filled"]) {
		$vars["aanbiedingenpagina_voor_de_eerste_keer_geopend"]=true;
		$_GET["faab"]=1;
	}
	if($_GET["d"]) {
#		$_GET["fad"]=$_GET["d"];
	}
	$_GET["filled"]=1;
	include("zoek-en-boek.php");
} else {

	if($_GET["d"]) $_GET["d"]=intval($_GET["d"]);
	include("admin/vars.php");

	# breadcrumb bepalen
	if($_GET["d"]) {
		$breadcrumbs[txt("menu_aanbiedingen").".php"]=txt("title_aanbiedingen");
		$breadcrumbs["last"]=ucfirst(txt("aankomst")).": ".weekend_voluit($_GET["d"]);
	}

	if(($vars["website"]=="Z" or $vars["website"]=="N") and preg_match("/\.php/",$_SERVER["REQUEST_URI"])) {
		header("Location: ".$path.txt("menu_aanbiedingen")."/",true,301);
		exit;
	}

	if($_GET["d"] and $_GET["d"]<(time()-604800)) {
		header("Location: ".$path.txt("menu_aanbiedingen").".php");
		exit;
	}

	include "content/opmaak.php";
}

?>