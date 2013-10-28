<?php

$laat_titel_weg=true;
$breadcrumbs["last"]="Actie Kixx Online";
$robot_noindex=true;
include("admin/vars.php");


if($vars["website"]=="C") {
	$title["actie-kixx"]="€ 100,- korting op je wintersportvakantie bij Chalet.nl!";
	include "content/opmaak.php";
} else {
	header("Location: /");
}

?>