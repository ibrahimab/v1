<?php

$laat_titel_weg=true;
$breadcrumbs["last"]="Gratis reiskookboek";
include("admin/vars.php");


if($vars["website"]=="I") {
	$title["actie-kookboek"]="Boek nu en ontvang een gratis reiskookboek!";
	include "content/opmaak.php";
} else {
	header("Location: /");
}

?>