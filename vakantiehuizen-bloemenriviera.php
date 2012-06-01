<?php

$title["vakantiehuizen-gardameer"]="Vakantiehuizen Bloemenrivièra";
$laat_titel_weg=true;
$meta_description="Op zoek naar een vakantiehuis aan de Bloemenrivièra in Italië? Bekijk hier ons aanbod van agriturismi en overige vakantiehuizen.";

include("admin/vars.php");

if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

include("content/opmaak.php");

?>