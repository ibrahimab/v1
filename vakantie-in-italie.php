<?php

$title["vakantie-in-italie"]="Vakantie in Itali� - La bella Italia";
$laat_titel_weg=true;
$meta_description="Op zoek naar een vakantiehuis in Itali�? Bekijk hier bijna 1.000 karakteristieke vakantiehuizen, vakantievilla's en agriturismi verdeeld over heel Itali�.";

include("admin/vars.php");

if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

include("content/opmaak.php");

?>