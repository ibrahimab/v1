<?php

#
# Alle kortingen/aanbiedingen doorrekenen
#
# indien $typeid_berekenen_inquery is gevuld:	alleen die types
# anders:					alle types (met toonper=1 en toonper=3)
#


#
# wordt 1x per dag gerund om 0.00u. (via cron/elkuur.php)
#

if(!$unixdir) {
	if($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/tarieven_berekenen.php" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		set_time_limit(0);
		$unixdir="/home/webtastic/html/chalet/";
	} else {
		$unixdir="/var/www/chalet.nl/html/";
	}
}
if(!$vars) {
	$cron=true;
	$geen_tracker_cookie=true;
	$boeking_bepaalt_taal=true;
	include($unixdir."admin/vars.php");
}
$vandaag=mktime(0,0,0,date("m"),date("d"),date("Y"));

// zorgen dat vanaf-prijzen worden doorgerekend
$voorraad_gekoppeld=new voorraad_gekoppeld;
$voorraad_gekoppeld->koppeling_uitvoeren_na_einde_script();


# kortingen uit db halen
unset($korting);
$db->query("SELECT k.type_id, k.toonexactekorting, k.aanbiedingskleur, k.toon_abpagina, k.toon_als_aanbieding, kt.week, kt.inkoopkorting_percentage, kt.aanbieding_acc_percentage, kt.aanbieding_skipas_percentage, kt.inkoopkorting_euro, kt.aanbieding_acc_euro, kt.aanbieding_skipas_euro FROM korting k, korting_tarief kt WHERE k.actief=1 AND kt.korting_id=k.korting_id AND UNIX_TIMESTAMP(k.van)<='".$vandaag."' AND UNIX_TIMESTAMP(k.tot)>='".$vandaag."'".($typeid_berekenen_inquery ? " AND k.type_id IN (".$typeid_berekenen_inquery.")" : "").";");
while($db->next_record()) {
	$korting[$db->f("type_id")][$db->f("week")]["inkoopkorting_percentage"]+=$db->f("inkoopkorting_percentage");
	$korting[$db->f("type_id")][$db->f("week")]["inkoopkorting_euro"]+=$db->f("inkoopkorting_euro");
	$korting[$db->f("type_id")][$db->f("week")]["aanbieding_acc_percentage"]+=$db->f("aanbieding_acc_percentage");
	$korting[$db->f("type_id")][$db->f("week")]["aanbieding_acc_euro"]+=$db->f("aanbieding_acc_euro");
	$korting[$db->f("type_id")][$db->f("week")]["aanbieding_skipas_percentage"]+=$db->f("aanbieding_skipas_percentage");
	$korting[$db->f("type_id")][$db->f("week")]["aanbieding_skipas_euro"]+=$db->f("aanbieding_skipas_euro");
	if($db->f("toonexactekorting")) $korting[$db->f("type_id")][$db->f("week")]["toonexactekorting"]=$db->f("toonexactekorting");
	if($db->f("aanbiedingskleur")) $korting[$db->f("type_id")][$db->f("week")]["aanbiedingskleur"]=$db->f("aanbiedingskleur");
	if($db->f("toon_abpagina")) $korting[$db->f("type_id")][$db->f("week")]["toon_abpagina"]=$db->f("toon_abpagina");
	if($db->f("toon_als_aanbieding")) $korting[$db->f("type_id")][$db->f("week")]["toon_als_aanbieding"]=$db->f("toon_als_aanbieding");

	// zorgen dat vanaf-prijzen worden doorgerekend
	$voorraad_gekoppeld->vanaf_prijzen_berekenen($db->f("type_id"));
}

# kortingen opslaan
while(list($key,$value)=@each($korting)) {
	while(list($key2,$value2)=each($value)) {
		$db->query("UPDATE tarief SET kortingenverwerkt=1, kortingactief=1, inkoopkorting_percentage='".addslashes($value2["inkoopkorting_percentage"])."', aanbieding_acc_percentage='".addslashes($value2["aanbieding_acc_percentage"])."', aanbieding_skipas_percentage='".addslashes($value2["aanbieding_skipas_percentage"])."', inkoopkorting_euro='".addslashes($value2["inkoopkorting_euro"])."', aanbieding_acc_euro='".addslashes($value2["aanbieding_acc_euro"])."', aanbieding_skipas_euro='".addslashes($value2["aanbieding_skipas_euro"])."', toonexactekorting='".addslashes($value2["toonexactekorting"])."', aanbiedingskleur_korting='".addslashes($value2["aanbiedingskleur"])."', korting_toon_abpagina='".addslashes($value2["toon_abpagina"])."', korting_toon_als_aanbieding='".addslashes($value2["toon_als_aanbieding"])."' WHERE type_id='".addslashes($key)."' AND week='".addslashes($key2)."';");
#		echo $db->lastquery."<br>\n";
	}
}

$db->query("UPDATE tarief SET inkoopkorting_percentage=0, aanbieding_acc_percentage=0, aanbieding_skipas_percentage=0, inkoopkorting_euro=0, aanbieding_acc_euro=0, aanbieding_skipas_euro=0, toonexactekorting=0, aanbiedingskleur_korting=0, korting_toon_abpagina=0, korting_toon_als_aanbieding=0 WHERE kortingenverwerkt=0".($typeid_berekenen_inquery ? " AND type_id IN (".$typeid_berekenen_inquery.")" : "").";");

# alle tarieven berekenen (indien week>nu of kortingactief=1)
$skipastarieven_verwerken=true;
unset($_POST);

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test") {
#	$db3->query("SELECT DISTINCT t.type_id, t.seizoen_id FROM tarief ta, type t, accommodatie a WHERE (ta.kortingactief=1 OR ta.week>='".(time())."') AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND t.type_id IN (SELECT t.type_id FROM type t, accommodatie a WHERE a.toonper IN (1,3) AND t.accommodatie_id=a.accommodatie_id AND t.verzameltype=0)".($typeid_berekenen_inquery ? " AND ta.type_id IN (".$typeid_berekenen_inquery.")" : "")." ORDER BY ta.type_id;");

	$typeid_berekenen_inquery="5312";

	$db3->query("SELECT DISTINCT t.type_id, ta.seizoen_id, a.toonper FROM tarief ta, type t, accommodatie a WHERE (ta.kortingactief=1 OR ta.week>='".(time())."') AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND a.toonper IN (1,3) AND t.verzameltype=0".($typeid_berekenen_inquery ? " AND ta.type_id IN (".$typeid_berekenen_inquery.")" : "")." ORDER BY ta.type_id;");
} else {
	$db3->query("SELECT DISTINCT t.type_id, ta.seizoen_id, a.toonper FROM tarief ta, type t, accommodatie a WHERE (ta.kortingactief=1 OR ta.week>='".(time())."') AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND a.toonper IN (1,3) AND t.verzameltype=0".($typeid_berekenen_inquery ? " AND ta.type_id IN (".$typeid_berekenen_inquery.")" : "")." ORDER BY ta.type_id;");
}
while($db3->next_record()) {
	if($cron) {
		echo "Bereken type_id ".$db3->f("type_id")." (seizoen_id ".$db3->f("seizoen_id").")<br>\n";
		flush();
	}

	// zorgen dat vanaf-prijzen worden doorgerekend
	$voorraad_gekoppeld->vanaf_prijzen_berekenen($db3->f("type_id"));

	unset($seizoen,$acc,$skipas);
	$_GET["tid"]=$db3->f("type_id");
	$_GET["sid"]=$db3->f("seizoen_id");
	include($unixdir."cms_tarieven.php");
	if(is_array($seizoen["weken"])) {
		reset($seizoen["weken"]);
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2") {
			# niks opslaan
			while(list($key,$value)=each($seizoen["weken"])) {
				while(list($key2,$value2)=@each($value["verkoop_site"])) {
					echo("INSERT INTO tarief_personen SET type_id='".$db3->f("type_id")."', seizoen_id='".addslashes($_GET["sid"])."', week='".$key."', personen='".addslashes($key2)."', prijs='".addslashes($value2)."', afwijking='".addslashes($value["verkoop_afwijking"][$key2])."';")."<br>";
#					echo $db4->lastquery."<hr>\n";
				}
			}
		} else {
			$db4->query("DELETE FROM tarief_personen WHERE type_id='".$db3->f("type_id")."' AND seizoen_id='".addslashes($_GET["sid"])."';");
			while(list($key,$value)=each($seizoen["weken"])) {
				if($db3->f("toonper")==1) {
					# toonper=1 (arrangementen): tarief per persoon opslaan
					while(list($key2,$value2)=@each($value["verkoop_site"])) {
						$db4->query("INSERT INTO tarief_personen SET type_id='".$db3->f("type_id")."', seizoen_id='".addslashes($_GET["sid"])."', week='".$key."', personen='".addslashes($key2)."', prijs='".addslashes($value2)."', afwijking='".addslashes($value["verkoop_afwijking"][$key2])."';");
	#					echo $db4->lastquery."<hr>\n";
					}
					$db4->query("UPDATE tarief SET wederverkoop_verkoopprijs='".addslashes($value["wederverkoop_verkoopprijs"])."', verkoop_accommodatie='".addslashes($value["verkoop_accommodatie"])."' WHERE type_id='".$db3->f("type_id")."' AND seizoen_id='".addslashes($_GET["sid"])."' AND week='".$key."';");
				} elseif($db3->f("toonper")==3) {
					# toonper=3: tarief opslaan
					$db4->query("UPDATE tarief SET wederverkoop_verkoopprijs='".addslashes($value["wederverkoop_verkoopprijs"])."', c_verkoop_site='".addslashes($value["c_verkoop_site"])."' WHERE type_id='".$db3->f("type_id")."' AND seizoen_id='".addslashes($_GET["sid"])."' AND week='".$key."';");
				}
			}
		}
	}
}

# Verzameltype berekenen
$db3->seek();
while($db3->next_record()) {
	verzameltype_berekenen($db3->f("seizoen_id"),$db3->f("type_id"));
}

# kortingactief en kortingenverwerkt uitzetten
$db->query("UPDATE tarief SET kortingactief=0 WHERE kortingenverwerkt=0".($typeid_berekenen_inquery ? " AND type_id IN (".$typeid_berekenen_inquery.")" : "").";");
$db->query("UPDATE tarief SET kortingenverwerkt=0".($typeid_berekenen_inquery ? " WHERE type_id IN (".$typeid_berekenen_inquery.")" : "").";");


?>