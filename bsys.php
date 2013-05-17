<?php

$boeking_wijzigen=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;

include("admin/vars.php");

if($_GET["laatstefactuur"] and $_GET["bid"]) {
	#
	# Factuur tonen bij accorderen factuur
	#
	$db->query("SELECT boeking_id FROM boeking WHERE boeking_id IN (".$boekingid_inquery.") AND boeking_id='".intval($_GET["bid"])."';");
	if($db->next_record()) {
		$db2->query("SELECT factuur_id, filename FROM factuur WHERE boeking_id='".intval($_GET["bid"])."' ORDER BY factuur_id DESC, datum DESC LIMIT 0,1;");
		if($db2->next_record()) {
			$file=$vars["unixdir"]."pdf/facturen/".$db2->f("filename");
			if(file_exists($file)) {
				header('Content-type: application/pdf');
				readfile($file);
			} else {
				trigger_error("factuur-bestand niet aanwezig",E_USER_NOTICE);
			}
		} else {
			trigger_error("factuur niet aanwezig in database",E_USER_NOTICE);
		}
	}
	exit;
}

if($_POST["factuurakkoord"] and $_POST["goedkeur1"] and $_POST["goedkeur2"]) {
	#
	# Factuur is geaccordeerd
	#

	$gegevens=get_boekinginfo($_GET["bid"]);

	# Ondertekendatum aanpassen
	$db->query("UPDATE boeking SET factuur_ondertekendatum=NOW(), vraag_ondertekening=0 WHERE boeking_id IN (".$boekingid_inquery.") AND boeking_id='".intval($_GET["bid"])."';");

	# goedkeuring in logbestand plaatsen
	chalet_log("factuur door de klant goedgekeurd via \"Mijn boeking\"",true,true);

	header("Location: ".$vars["path"]."bsys.php?menu=3&bid=".intval($_GET["bid"])."&akkoord=1");
	exit;
}

include("content/opmaak.php");

?>