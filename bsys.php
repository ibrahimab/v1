<?php

$boeking_wijzigen=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$vars["verberg_breadcrumbs"]=true;

if($_GET["reisbureaulogin"]) {
	$vars["reisbureau_mustlogin"]=true;
}

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
                header('Content-Disposition: atachment; filename=factuur-' . $_GET['bid'] . '.pdf');
				readfile($file);
                
			} else {
				trigger_error("factuur-bestand niet aanwezig",E_USER_NOTICE);
			}
		} else {
			trigger_error("factuur niet aanwezig in database",E_USER_NOTICE);
		}
	} else {
		trigger_error("boeking niet gevonden met boekingid_inquery ".$boekingid_inquery,E_USER_NOTICE);
	}
	exit;
}

if($_POST["factuurakkoord"] and $_POST["goedkeur1"] and $_POST["goedkeur2"]) {
	#
	# Factuur is geaccordeerd
	#

	$gegevens=get_boekinginfo($_GET["bid"]);

	# Ondertekendatum aanpassen
	if($boekingid_inquery) {
		$db->query("UPDATE boeking SET factuur_ondertekendatum=NOW(), vraag_ondertekening=0 WHERE boeking_id IN (".$boekingid_inquery.") AND boeking_id='".intval($_GET["bid"])."';");
	} elseif($reisbureau_user_id_inquery) {
		$db->query("UPDATE boeking SET factuur_ondertekendatum=NOW(), vraag_ondertekening=0 WHERE boeking_id='".intval($_GET["bid"])."' AND reisbureau_user_id IN (".$reisbureau_user_id_inquery.");");
	} else {
		trigger_error("factuur_ondertekendatum niet aangepast: boekingid_inquery en reisbureau_user_id_inquery allebei leeg",E_USER_NOTICE);
	}

	# goedkeuring in logbestand plaatsen
	chalet_log("factuur door de klant goedgekeurd via \"Mijn boeking\"",true,true);

	if($vars["trustpilot_code"] and !$gegevens["stap1"]["reisbureau_user_id"] and !$vars["toon_bijkomendekosten_stap1"]) {

		$mail=new wt_mail;
		$mail->from=$vars["email"];
		$mail->fromname=$vars["websitenaam"];
		$mail->to=$vars["trustpilot_code"];
		$mail->subject="Order number ".$gegevens["stap1"]["boekingsnummer"];
		// $mail->settings["plaintext_utf8"]=true;

		// $mail->plaintext="Customer Email: ".utf8_encode($gegevens["stap2"]["email"])."\n\nCustomer Name: ".utf8_encode(wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]))."\n\n";

		if($vars["taal"]=="en") {
			$tp_lang = "en-GB";
			$tp_tld = "com";
		} else {
			$tp_lang = "nl-NL";
			$tp_tld = "nl";
		}
		$mail->html="<!--
		tp_lang: ".$tp_lang."
		tp_tld: ".$tp_tld."
		-->
		Customer Email: ".wt_he($gegevens["stap2"]["email"])."<br/>\nCustomer Name: ".wt_he(wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]))."<br/>\n";

		$mail->send();

		chalet_log("klantgegevens zijn doorgestuurd naar Trustpilot",true,true);
	}

	header("Location: ".$vars["path"]."bsys.php?menu=3&bid=".intval($_GET["bid"])."&akkoord=1");
	exit;
}

include("content/opmaak.php");

?>