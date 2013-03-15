<?php

$boeking_wijzigen=true;
$robot_noindex=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;

include("admin/vars.php");

$breadcrumbs["last"]=txt("title_bsys");

## Chalettour.nl : boeking wijzigen niet toegestaan
#if($vars["websitetype"]==4 or $vars["websitetype"]==5) {
#	header("Location: ".$path."reisagent.php");
#	exit;
#}

if($login->logged_in) {
#echo "Pad:".$path;
	header("Location: ".$path."bsys.php");
	exit;
}

include "content/opmaak.php";

?>