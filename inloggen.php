<?php

$boeking_wijzigen=true;
$robot_noindex=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;

include("admin/vars.php");

if($_GET["directlogin"] and $_GET["user_id"] and $_GET["code"]) {

	if($login->logged_in) {
		$login->logout();
	}

	$directlogin = new directlogin;
	$directlogin->check_wrongcount=true;

	if($directlogin->code($_GET["user_id"])==$_GET["code"]) {
		$login->log_user_in($_GET["user_id"]);
	} else {
		# Opslaan dat er een foute inlogpoging was
		$db->query("UPDATE boekinguser SET wrongcount=wrongcount+1 WHERE user_id='".intval($_GET["user_id"])."';");
	}
	if($_GET["soort"]==2) {
		# doorlinken naar "boeking goedkeuren"
		header("Location: ".$vars["path"].txt("menu_inloggen").".php");
	} else {
		header("Location: ".$vars["path"].txt("menu_inloggen").".php");
	}
	exit;
}

$breadcrumbs["last"]=txt("title_bsys");

## Chalettour.nl : boeking wijzigen niet toegestaan
#if($vars["websitetype"]==4 or $vars["websitetype"]==5) {
#	header("Location: ".$path."reisagent.php");
#	exit;
#}

if($login->logged_in) {
#echo "Pad:".$path;
	header("Location: ".$path."bsys.php");
	exit;
}

include "content/opmaak.php";

?>