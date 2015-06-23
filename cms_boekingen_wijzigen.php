<?php

if(!$boeking_wijzigen) $mustlogin=true;

include("admin/vars.php");

$gegevens=boekinginfo($_GET["bid"]);
if($gegevens["stap1"]["boekingid"]) {

	$accinfo=accinfo($gegevens["stap1"]["typeid"]);

	if($_POST["copy_personal_data_filled"] and $_POST["copy_personal_data"]) {

		// copy personal data from other booking
		$db->query("SELECT boeking_id FROM boeking WHERE boekingsnummer<>'' AND (boekingsnummer='".wt_as($_POST["copy_personal_data"])."' OR boekingsnummer LIKE '".wt_as($_POST["copy_personal_data"])." %');");
		if( $db->next_record() ) {

			$bron_boeking_id = $db->f( "boeking_id" );

			$db->query("SELECT land FROM boeking_persoon WHERE persoonnummer=1 AND status=2 AND boeking_id='".intval($bron_boeking_id)."';");
			if( $db->next_record() ) {
				$land_hoofdboeker = $db->f( "land" );
			}

			$db->query("SELECT persoonnummer, voornaam, tussenvoegsel, achternaam, geslacht, geboortedatum, plaats, land FROM boeking_persoon WHERE persoonnummer>1 AND status=2 AND boeking_id='".intval($bron_boeking_id)."';");
			while( $db->next_record() ) {

				unset($setquery);

				$setquery .= ", voornaam='".wt_as($db->f( "voornaam" ))."'";
				$setquery .= ", tussenvoegsel='".wt_as($db->f( "tussenvoegsel" ))."'";
				$setquery .= ", achternaam='".wt_as($db->f( "achternaam" ))."'";
				$setquery .= ", geslacht='".wt_as($db->f( "geslacht" ))."'";
				if($db->f( "geboortedatum" )=="") {
					$setquery .= ", geboortedatum=NULL";
				} else {
					$setquery .= ", geboortedatum='".wt_as($db->f( "geboortedatum" ))."'";
				}
				$setquery .= ", plaats='".wt_as($db->f( "plaats" ))."'";
				if ( $db->f( "land2" ) ) {
					$setquery .= ", land='".wt_as($db->f( "land" ))."'";
				} else {
					$setquery .= ", land='".wt_as($land_hoofdboeker)."'";
				}

				$setquery = substr($setquery, 2);
				$db2->query("UPDATE boeking_persoon SET ".$setquery." WHERE boeking_id='".intval($_GET["bid"])."' AND persoonnummer='".$db->f( "persoonnummer" )."';");
			}

			$_SESSION["wt_popupmsg"]="Persoonsgegevens gekopieerd en opgeslagen.";

		} else {
			$_SESSION["copy_personal_data_error"]="Boeking ".wt_he($_POST["copy_personal_data"])." niet gevonden.";
		}

		header("Location: ".$_SERVER["REQUEST_URI"]);
		exit;
	}

	// Alle accommodaties laden
	$db->query("SELECT a.naam AS accommodatie, a.soortaccommodatie, a.toonper, a.accommodatie_id, t.type_id, t.naam AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, t.verzameltype, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id".($accinfo["wzt"] ? " AND a.wzt='".$accinfo["wzt"]."'" : "")." ORDER BY p.naam, a.naam, t.type_id, t.naam, t.maxaantalpersonen;");
	while($db->next_record()) {
		$vars["alle_types"][$db->f("type_id")]=substr($db->f("plaats")." - ".$db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : ""),0,50)." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p - ".$db->f("begincode").$db->f("type_id").") ".($db->f("verzameltype") ? "(V) " : "");
		if($db->f("maxaantalpersonen")>$maxaantalpersonen) $maxaantalpersonen=$db->f("maxaantalpersonen");
	}

	// Alle aankomstdata-array vullen
	$db->query("SELECT seizoen_id, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' ORDER BY begin, eind;");
	while($db->next_record()) {
		// Aankomstdatum-array vullen
		$timeteller=$db->f("begin");
		while($timeteller<=$db->f("eind")) {
			$vars["alle_aankomstdatum_weekend"][$timeteller]="weekend ".date("j",$timeteller)." ".datum("MAAND JJJJ",$timeteller);
			$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
		}
	}

	// Alle aantalpersonen-array vullen
	for($i=1;$i<=$gegevens["stap1"]["accinfo"]["maxaantalpersonen"];$i++) {
		$vars["alle_aantalpersonen_array"][$i]=$i." ".($i==1 ? "persoon" : "personen");
	}
}

include("boeken.php");

?>