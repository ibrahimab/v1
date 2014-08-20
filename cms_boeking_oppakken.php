<?php

$vars["cmslog_pagina_niet_opslaan"]=true;
$mustlogin=true;
include("admin/vars.php");
wt_session_start();

if($_GET["boekingid"]) {
	$gegevens=get_boekinginfo($_GET["boekingid"]);
	if(!ereg($_SERVER["HTTP_HOST"],$gegevens["stap1"]["website_specifiek"]["basehref"])) {
		header("Location: ".ereg_replace("/[a-z]+/","/",$gegevens["stap1"]["website_specifiek"]["basehref"])."cms_boeking_oppakken.php?boekingid=".$_GET["boekingid"]);
		exit;
	}

	#echo $gegevens["stap1"]["website_specifiek"]["basehref"]."boeken.php?bfbid=".$_GET["boekingid"];

	# Oude sessie wissen
	unset($_SESSION["boeking"]["boekingid"]);

	# Cookie plaatsen
	setcookie("CHALET[boeking][boekingid]",$_GET["boekingid"]."_".boeking_veiligheid($_GET["boekingid"]),time()+259200);
	header("Location: ".$gegevens["stap1"]["website_specifiek"]["basehref"]."boeken.php?bfbid=".$_GET["boekingid"]);
	exit;
} else {
#	header("Location: ".$path."cms.php");
}

?>