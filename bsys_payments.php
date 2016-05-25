<?php

$my_booking = true;
$boeking_wijzigen=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$vars["verberg_breadcrumbs"]=true;

include("admin/vars.php");

$breadcrumbs["last"] = txt("payment", "bsys");

// if(!$vars["docdata_payments"]) {
// 	header("Location: ".$path);
// 	exit;
// }

$gegevens=get_boekinginfo($_GET["bid"]);
// bij geen factuur: terug naar homepage "Mijn boeking"
if(!$gegevens["stap1"]["factuurdatum"]) {
	header("Location: ".$vars["path"]."bsys.php?bid=".$_GET["bid"]);
	exit;
}
// bij ontbrekende goedkeuring: naar "factuur goedkeuren"
if($gegevens["stap1"]["vraag_ondertekening"]) {
	header("Location: ".$vars["path"]."bsys.php?menu=3&bid=".$_GET["bid"]);
	exit;
}
unset($gegevens);


#
# Indien _GET leeg is (en dus alleen boeken.php is opgevraagd): terug naar hoofdpagina
#
if(!$_GET) {
	header("Location: ".$path);
	exit;
}

include "content/opmaak.php";
