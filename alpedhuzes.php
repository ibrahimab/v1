<?php

$laat_titel_weg=true;
include("admin/vars.php");

if(preg_match("/alpedhuzes\.php/",$_SERVER["REQUEST_URI"])) {
	header("Location: ".$vars["path"]."alpe-d-huzes",true,301);
	exit;
}

if($vars["website"]=="C" or $vars["website"]=="T" or $vars["website"]=="Z") {
	include "content/opmaak.php";
} else {
	header("Location: /");
	exit;
}

?>