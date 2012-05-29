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

include "content/opmaak.php";

?>