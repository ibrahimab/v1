<?php

if($_SERVER["REQUEST_URI"]=="/werkenbij.php") {
	header("Location: /werkenbij", true, 301);
	exit;
}
$title["werkenbij"]="Werken bij ons";
// $breadcrumbs["last"]="Werken bij ons";
$vars["verberg_zoekenboeklinks"]=true;
include("admin/vars.php");
include "content/opmaak.php";

?>