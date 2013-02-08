<?php

# Vallandry

include("admin/vars.php");
if($vars["websitetype"]<>6) {
	header("Location: ".$vars["path"]);
	exit;
}
if($_GET["chaletpark"]) {
	if(!$txt["nl"]["chalets"][$_GET["chaletpark"]]) {
		header("Location: ".$vars["path"]);
		exit;
	} else {
		$title["chalets"]=ucfirst(txt($_GET["chaletpark"],"chalets"));
	}
}
if($_GET["seizoen"]) {
	if($_GET["seizoen"]=="winter") {
		$vars["seizoentype"]=1;
	} else {
		$vars["seizoentype"]=2;
	}
}

# jQuery UI theme laden (t.b.v. autocomplete)
$vars["page_with_jqueryui"]=true;

# jQuery Chosen laden
$vars["jquery_chosen"]=true;

include "content/opmaak.php";

?>