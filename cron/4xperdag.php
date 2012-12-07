<?php

#
#
# Dit script wordt op elke dag om 6.00, 12.00, 18.00 en 0.00. gerund op de server srv01.chalet.nl met het account chalet01.
#
# /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/4xperdag.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

} else {
	sleep(10);
}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	echo "<pre>";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/4xperdag.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	if($_SERVER["PWD"]=="/var/www/chalet.nl") {
		$unixdir="/var/www/chalet.nl/html/";
	} else {
		$unixdir="/home/sites/chalet.nl/html/";
	}
#	mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkuur","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

echo date("d-m-Y H:i")."u.<pre>\n\nChalet.nl 4x per dag\n\n\n";
flush();

#
# Skipassen overzetten naar wederverkoop-skipassen
#

# actieve seizoenen bepalen
$db->query("SELECT seizoen_id FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".(time()-864000)."' ORDER BY seizoen_id;");
while($db->next_record()) {
	if($actieve_seizoenen) $actieve_seizoenen.=",".$db->f("seizoen_id"); else $actieve_seizoenen=$db->f("seizoen_id");
}


# Zorgen dat overbodige gegevens achteraf gewist kunnen worden
$db->query("UPDATE optie_accommodatie SET wis_na_bijwerken=1 WHERE wederverkoop_skipas_id IS NOT NULL;");
$db->query("SELECT skipas_id, naam, website_omschrijving, website_omschrijving_en, wederverkoop_naam, wederverkoop_naam_en, naam_voucher, naam_voucher_en, omschrijving_voucher, begindag, einddag FROM skipas ORDER BY skipas_id;");
while($db->next_record()) {

	# Optie-groepen
	$setquery="naam='".addslashes($db->f("naam"))."', skipas_id='".$db->f("skipas_id")."', wederverkoop_skipas_id='".$db->f("skipas_id")."', naam_voucher='".addslashes($db->f("naam_voucher"))."', naam_voucher_en='".addslashes($db->f("naam_voucher_en"))."', optie_soort_id=41";
	$db2->query("SELECT optie_groep_id FROM optie_groep WHERE wederverkoop_skipas_id='".$db->f("skipas_id")."';");
	if($db2->next_record()) {
		$db3->query("UPDATE optie_groep SET ".$setquery." WHERE optie_groep_id='".$db2->f("optie_groep_id")."';");
		$optie_groep_id=$db2->f("optie_groep_id");
	} else {
		$db3->query("INSERT INTO optie_groep SET ".$setquery.";");
		$optie_groep_id=$db3->insert_id();
	}

	# Optie-onderdelen
	if($db->f("wederverkoop_naam")) {
		$naam_nl=$db->f("wederverkoop_naam");
	} else {
		$naam_nl=trim(ereg_replace("\(.*$","",$db->f("website_omschrijving")));
	}
	if($db->f("wederverkoop_naam_en")) {
		$naam_en=$db->f("wederverkoop_naam_en");
	} else {
		$naam_en=trim(ereg_replace("\(.*$","",$db->f("website_omschrijving_en")));
	}
	$setquery="naam='".addslashes($naam_nl)."', naam_en='".addslashes($naam_en)."', omschrijving_voucher='".addslashes($db->f("omschrijving_voucher"))."', begindag='".addslashes($db->f("begindag"))."', einddag='".addslashes($db->f("einddag"))."', volgorde=1, wederverkoop_skipas_id='".$db->f("skipas_id")."', optie_groep_id='".$optie_groep_id."'";
	$db2->query("SELECT optie_onderdeel_id FROM optie_onderdeel WHERE wederverkoop_skipas_id='".$db->f("skipas_id")."' AND optie_groep_id='".$optie_groep_id."';");
	if($db2->next_record()) {
		$db3->query("UPDATE optie_onderdeel SET ".$setquery." WHERE wederverkoop_skipas_id='".$db->f("skipas_id")."' AND optie_groep_id='".$optie_groep_id."';");
		$optie_onderdeel_id=$db2->f("optie_onderdeel_id");
	} else {
		$db3->query("INSERT INTO optie_onderdeel SET ".$setquery.";");
		$optie_onderdeel_id=$db3->insert_id();
	}

	# Optie-tarieven (skipastarieven)
	$db2->query("SELECT skipas_id, seizoen_id, week, bruto, netto_ink, korting, verkoopkorting, prijs, omzetbonus, netto, wederverkoop_commissie_agent FROM skipas_tarief WHERE seizoen_id IN (".$actieve_seizoenen.") AND skipas_id='".$db->f("skipas_id")."';");
	while($db2->next_record()) {

		# inkoopprijs bepalen
		if($db2->f("korting")<>0) {
			$inkoop=$db2->f("bruto")*(1-($db2->f("korting")/100));
		} else {
			$inkoop=$db2->f("netto_ink");
		}
		$setquery="seizoen_id='".addslashes($db2->f("seizoen_id"))."', week='".addslashes($db2->f("week"))."', beschikbaar=1, verkoop='".addslashes($db2->f("bruto"))."', inkoop='".addslashes($inkoop)."', korting='".addslashes($db2->f("korting"))."', omzetbonus='".addslashes($db2->f("omzetbonus"))."', wederverkoop_commissie_agent='".addslashes($db2->f("wederverkoop_commissie_agent"))."', wederverkoop_skipas_id='".$db->f("skipas_id")."', optie_onderdeel_id='".$optie_onderdeel_id."'";

		$db3->query("SELECT optie_onderdeel_id FROM optie_tarief WHERE wederverkoop_skipas_id='".$db->f("skipas_id")."' AND optie_onderdeel_id='".$optie_onderdeel_id."' AND seizoen_id='".addslashes($db2->f("seizoen_id"))."' AND week='".addslashes($db2->f("week"))."';");
		if($db3->next_record()) {
			$db4->query("UPDATE optie_tarief SET ".$setquery." WHERE wederverkoop_skipas_id='".$db->f("skipas_id")."' AND optie_onderdeel_id='".$optie_onderdeel_id."' AND seizoen_id='".addslashes($db2->f("seizoen_id"))."' AND week='".addslashes($db2->f("week"))."';");
		} else {
			$db4->query("INSERT INTO optie_tarief SET ".$setquery.";");
		}
	}

	# Optie-accommodatie
	$db2->query("SELECT accommodatie_id FROM accommodatie WHERE skipas_id='".$db->f("skipas_id")."';");
	while($db2->next_record()) {
		$setquery="wis_na_bijwerken=0, accommodatie_id='".addslashes($db2->f("accommodatie_id"))."', optie_soort_id=41, optie_groep_id='".$optie_groep_id."', wederverkoop_skipas_id='".$db->f("skipas_id")."'";

		$db3->query("SELECT accommodatie_id FROM optie_accommodatie WHERE wederverkoop_skipas_id='".$db->f("skipas_id")."' AND accommodatie_id='".addslashes($db2->f("accommodatie_id"))."' AND optie_soort_id=41 AND optie_groep_id='".$optie_groep_id."'");
		if($db3->next_record()) {
			$db4->query("UPDATE optie_accommodatie SET ".$setquery." WHERE wederverkoop_skipas_id='".$db->f("skipas_id")."' AND accommodatie_id='".addslashes($db2->f("accommodatie_id"))."' AND optie_soort_id=41 AND optie_groep_id='".$optie_groep_id."'");
		} else {
			$db4->query("INSERT INTO optie_accommodatie SET ".$setquery.";");
		}
	}
}
# Gegevens die niet zijn bijgewekt: wissen
$db2->query("DELETE FROM optie_accommodatie WHERE wis_na_bijwerken=1;");



?>