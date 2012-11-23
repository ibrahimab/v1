<?php

include("admin/vars.php");

if($vars["website"]!="C" and $vars["website"]!="I") {
	header("Location: ".$vars["path"],true,301);
	exit;
} else {

	if($vars["website"]=="C" and $_SERVER["HTTPS"]<>"on" and !$vars["lokale_testserver"]) {
		# deze pagina altijd via https
		header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
		exit;
	}
	include "content/opmaak.php";
}

?>