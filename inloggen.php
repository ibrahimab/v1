<?php


$boeking_wijzigen=true;
$robot_noindex=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;

if($_GET["directlogin"]) {
	$vars["huidige_user_uitloggen"]=true;
}


include("admin/vars.php");


if($_GET["directlogin"] and $_GET["user_id"] and ($_GET["code"] or $_GET["newcode"])) {

	if($login->logged_in) {
		$login->logout();
	}

	$directlogin = new directlogin;
	$directlogin->check_wrongcount=true;


	if($_GET["code"] and $directlogin->code_old($_GET["user_id"])==$_GET["code"]) {
		$login->log_user_in($_GET["user_id"]);
	} elseif($_GET["newcode"] and $directlogin->code($_GET["user_id"])==$_GET["newcode"]) {
		$login->log_user_in($_GET["user_id"]);
	} else {
		// Opslaan dat er een foute inlogpoging was
		$db->query("UPDATE boekinguser SET wrongcount=wrongcount+1 WHERE user_id='".intval($_GET["user_id"])."';");

		// trigger error-message
		trigger_error( "_notice: inloggen user_id ".intval($_GET["user_id"])." mislukt",E_USER_NOTICE );
	}

	unset($querystring);
	if($_GET["bid"]) {
		$querystring.="bid=".intval($_GET["bid"]);
	}

	if($_GET["soort"]==2) {
		# doorlinken naar "Betaaloverzicht"
		header("Location: ".$vars["path"]."bsys_payments.php?menu=3".($querystring ? "&".$querystring : ""));
	} elseif($_GET["soort"]==3) {
		# doorlinken naar "factuur goedkeuren"
		header("Location: ".$vars["path"]."bsys.php?menu=3".($querystring ? "&".$querystring : ""));
	} else {
		header("Location: ".$vars["path"]."bsys.php".($querystring ? "?".$querystring : ""));
	}
	exit;
}

$breadcrumbs["last"]=txt("title_bsys");

if($login->logged_in) {
	header("Location: ".$path."bsys.php");
	exit;
}

include "content/opmaak.php";
