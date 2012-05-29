<?php

include("admin/vars.php");

$_GET["grizzlyfile"]=basename($_GET["grizzlyfile"]);
if(file_exists("vakantie/".$_GET["grizzlyfile"].".php")) {
	$grizzly_html=file_get_contents("vakantie/".$_GET["grizzlyfile"].".php");

	if(eregi("<title>([^<>]*)</title>",$grizzly_html,$regs)) {
		$grizzly_title=$regs[1];
	}
	$grizzly_body=$grizzly_html;
	$grizzly_body=preg_replace("'.*<div style=\"margin-right:12px;\">'si"," ",$grizzly_body);
	$grizzly_body=preg_replace("'.*<div id=\"contentrechts\">'si"," ",$grizzly_body);
	$grizzly_body=preg_replace("'<div id=\"terugnaarboven\" .*'si"," ",$grizzly_body);
	$grizzly_body=ereg_replace("&z=[0-9]{1,}","",$grizzly_body);
	$vars["extracss"]=array("index_zomerhuisje.css");
} else {
	header("Location: ".$vars["path"]);
	exit;
}

include "content/opmaak.php";

?>