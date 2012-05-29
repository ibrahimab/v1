<?php

#
#
#

$mustlogin=true;

include("admin/vars.php");


if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and $_GET["wisboekingen"] and $_GET["confirmed"]) {
	$db->query("DELETE FROM boeking;");
	$db->query("DELETE FROM boekinguser;");
	$db->query("DELETE FROM boeking_betaling;");
	$db->query("DELETE FROM boeking_optie;");
	$db->query("DELETE FROM boeking_persoon;");
	$db->query("DELETE FROM boeking_tarief;");
	$db->query("DELETE FROM extra_optie;");
	$db->query("DELETE FROM factuur;");
	$db->query("DELETE FROM factuurregel;");
	header("Location: cms_testpaginas.php");
	exit;
}

$layout->display_all($cms->page_title);

?>