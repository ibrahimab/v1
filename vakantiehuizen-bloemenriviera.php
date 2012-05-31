<?php

$title["vakantiehuizen-gardameer"]="Vakantiehuizen Gardameer";
$laat_titel_weg=true;
$meta_description="Op zoek naar een vakantiehuis aan de westkant van het Gardameer? Bekijk hier ons aanbod van vakantiehuizen en vakantievilla's.";

include("admin/vars.php");

if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

include("content/opmaak.php");

?>