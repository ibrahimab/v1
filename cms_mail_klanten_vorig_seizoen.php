<?php

set_time_limit(0);
$mustlogin=true;
include("admin/vars.php");

if(!$login->has_priv("24")) {
	header("Location: cms.php");
	exit;
}

if($_POST["boekingsgegevens"]) {
	# Gegevens bestaande klanten (mail n.a.v. boeking vorig seizoen) opslaan in database (afkomstig van content/bsys.html)

	if($_POST["day"] and $_POST["month"] and $_POST["year"] and ($_POST["status_klanten_vorig_seizoen"]==2 or $_POST["status_klanten_vorig_seizoen"]==3)) {
		$status_vanaf_klanten_vorig_seizoen="FROM_UNIXTIME('".mktime(0,0,0,$_POST["month"],$_POST["day"],$_POST["year"])."')";
	} else {
		$status_vanaf_klanten_vorig_seizoen="NULL";
	}
	$db->query("UPDATE boeking SET status_klanten_vorig_seizoen='".addslashes($_POST["status_klanten_vorig_seizoen"])."', opmerkingen_klanten_vorig_seizoen='".addslashes($_POST["opmerkingen_klanten_vorig_seizoen"])."', status_vanaf_klanten_vorig_seizoen=".$status_vanaf_klanten_vorig_seizoen." WHERE boeking_id='".addslashes($_POST["boekingid"])."';");
#	echo $db->lastquery;
#	echo wt_dump($_POST);
#	exit;
}

$layout->display_all($cms->page_title);

?>