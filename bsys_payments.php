<?php

$mustlogin=true;
$boeking_wijzigen=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;

include("admin/vars.php");

$breadcrumbs["last"] = txt("payment", "bsys");

#
# Indien _GET leeg is (en dus alleen boeken.php is opgevraagd): terug naar hoofdpagina
#
if(!$_GET) {
	header("Location: ".$path);
	exit;
}

include "content/opmaak.php";

?>