<?php

if(!$boeking_wijzigen) $mustlogin=true;

include("admin/vars.php");

$gegevens=boekinginfo($_GET["bid"]);
if($gegevens["stap1"]["boekingid"]) {

	$accinfo=accinfo($gegevens["stap1"]["typeid"]);

	# Alle accommodaties laden
#	$db->query("SELECT a.naam AS accommodatie, a.soortaccommodatie, a.toonper, a.accommodatie_id, t.type_id, t.naam AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, t.verzameltype, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id ORDER BY p.naam, a.naam, t.type_id, t.naam, t.maxaantalpersonen;");
	$db->query("SELECT a.naam AS accommodatie, a.soortaccommodatie, a.toonper, a.accommodatie_id, t.type_id, t.naam AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, t.verzameltype, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id".($accinfo["wzt"] ? " AND a.wzt='".$accinfo["wzt"]."'" : "")." ORDER BY p.naam, a.naam, t.type_id, t.naam, t.maxaantalpersonen;");
	while($db->next_record()) {
		$vars["alle_types"][$db->f("type_id")]=substr($db->f("plaats")." - ".$db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : ""),0,50)." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p - ".$db->f("begincode").$db->f("type_id").") ".($db->f("verzameltype") ? "(V) " : "");
		if($db->f("maxaantalpersonen")>$maxaantalpersonen) $maxaantalpersonen=$db->f("maxaantalpersonen");
	}
	
	# Alle aankomstdata-array vullen
	$db->query("SELECT seizoen_id, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' ORDER BY begin, eind;");
	while($db->next_record()) {
		# Aankomstdatum-array vullen
		$timeteller=$db->f("begin");
		while($timeteller<=$db->f("eind")) {
			$vars["alle_aankomstdatum_weekend"][$timeteller]="weekend ".date("j",$timeteller)." ".datum("MAAND JJJJ",$timeteller);
			$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
		}
	}
	
	# Alle aantalpersonen-array vullen
	for($i=1;$i<=$gegevens["stap1"]["accinfo"]["maxaantalpersonen"];$i++) {
		$vars["alle_aantalpersonen_array"][$i]=$i." ".($i==1 ? "persoon" : "personen");
	}
}

include("boeken.php");

?>