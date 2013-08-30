<?php

$cronmap=true;

set_time_limit(0);

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	echo "<pre>";
} else {
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=debiteuren.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
}

$unixdir="../";
include($unixdir."admin/vars.php");

unset($query);

# query voor gewone boekingen
#$query[1]="SELECT b.debiteurnummer, p.voornaam, p.tussenvoegsel, p.achternaam, p.adres, p.postcode, p.plaats, b.landcode, p.telefoonnummer, p.geslacht, b.taal, UNIX_TIMESTAMP(b.invuldatum) AS invuldatum FROM boeking b, boeking_persoon p WHERE b.wederverkoop=0 AND p.persoonnummer=1 AND p.boeking_id=b.boeking_id AND b.debiteurnummer>0 AND b.goedgekeurd=1 ORDER BY b.boeking_id;";


// alleen kijken naar boekingen van -2 weken tot ooit
$boekingen_vanaf=mktime(0,0,0,date("m"),date("d")-14,date("Y"));

$query[1]="SELECT b.debiteurnummer, p.voornaam, p.tussenvoegsel, p.achternaam, p.adres, p.postcode, p.plaats, b.landcode, p.telefoonnummer, p.geslacht, b.taal, UNIX_TIMESTAMP(b.invuldatum) AS invuldatum FROM boeking b, boeking_persoon p WHERE b.reisbureau_user_id IS NULL AND p.persoonnummer=1 AND p.boeking_id=b.boeking_id AND b.debiteurnummer>0 AND b.goedgekeurd=1 AND b.aankomstdatum>=".$boekingen_vanaf." ORDER BY b.boeking_id;";

# query voor reisbureaus
$query[2]="SELECT b.debiteurnummer, r.naam, r.adres, r.postcode, r.plaats, b.landcode, r.telefoonnummer, b.taal, UNIX_TIMESTAMP(b.invuldatum) AS invuldatum FROM boeking b, reisbureau r, reisbureau_user ru WHERE b.reisbureau_user_id=ru.user_id AND ru.reisbureau_id=r.reisbureau_id AND b.wederverkoop=1 AND b.debiteurnummer>0 AND b.goedgekeurd=1 AND b.aankomstdatum>=".$boekingen_vanaf." ORDER BY b.boeking_id;";

while(list($key,$value)=each($query)) {
	$db->query($value);
	while($db->next_record()) {
		if($db->f("geslacht")==1) {
			$geslacht="M";
		} elseif($db->f("geslacht")==1) {
			$geslacht="V";
		} else {
			$geslacht="O";
		}
		if($db->f("debiteurnummer")<1000) {
			$debiteurnummer="130".substr("0000".$db->f("debiteurnummer"),-4);
		} else {
			$debiteurnummer="130".$db->f("debiteurnummer");
		}
		if($key==1) {
			# gewone boekingen
			$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
			$voornaam=$db->f("voornaam");
		} else {
			# reisbureaus
			$naam=$db->f("naam");
			$voornaam=$db->f("naam");
		}
		// $naam=preg_replace("@,@","/",$naam);
		// $voornaam=preg_replace("@,@","/",$voornaam);
		// $adres=preg_replace("@,@","/",$db->f("adres"));
		// $postcode=preg_replace("@,@","/",$db->f("postcode"));
		// $plaats=preg_replace("@,@","/",$db->f("plaats"));
		$adres=$db->f("adres");
		$postcode=$db->f("postcode");
		$plaats=$db->f("plaats");

		$csv[$db->f("debiteurnummer")]=$debiteurnummer.",".wt_csvconvert($naam).",".wt_csvconvert($adres).",,".wt_csvconvert($postcode).",".wt_csvconvert($plaats).",".$vars["landcodes_boekhouding_kort"][$db->f("landcode")].",,Eur,,,".wt_csvconvert($naam).",".$geslacht.",,".wt_csvconvert(substr($voornaam,0,10)).",,,,,,,,,,,,,,,,,,,,,,,,".strtoupper($db->f("taal")).",,,,,,,,,,,,,,".date("dmy",$db->f("invuldatum"));
	}
}

ksort($csv);

while(list($key,$value)=@each($csv)) {
	echo $value."\n";
}

?>