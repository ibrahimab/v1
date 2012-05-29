<?php

#
#
#

$mustlogin=true;

include("admin/vars.php");

if($_POST["filled"]) {
	# Gegevens opslaan in database
	while(list($key,$value)=each($vars["sjabloon_skipas"])) {
		reset($_POST["skipas_".$value]);
		while(list($key2,$value2)=each($_POST["skipas_".$value])) {
			$savequery[$key2].=", ".$value."='".addslashes($value2)."'";
			if($value=="wederverkoop_commissie_agent") {

				# Wederverkoop-percentage skipasuitbreidingen wijzigen
				$db->query("UPDATE optie_tarief SET wederverkoop_commissie_agent='".addslashes($value2)."' WHERE week='".$key2."' AND optie_onderdeel_id IN (SELECT DISTINCT optie_onderdeel_id FROM view_optie WHERE skipas_id='".addslashes($_GET["spid"])."');");

				# Wederverkoop-percentage wederverkoop-skipassen wijzigen
				$db->query("UPDATE optie_tarief SET wederverkoop_commissie_agent='".addslashes($value2)."' WHERE week='".$key2."' AND wederverkoop_skipas_id='".addslashes($_GET["spid"])."';");
			}
		}
	}

	# Eerst gegevens wissen
	$db->query("DELETE FROM skipas_tarief WHERE skipas_id='".addslashes($_GET["spid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	
	# Dan opslaan
	while(list($key,$value)=each($savequery)) {
		$db->query("INSERT INTO skipas_tarief SET skipas_id='".addslashes($_GET["spid"])."', seizoen_id='".addslashes($_GET["sid"])."', week='".$key."'".$value.";");
	}

	# Opmerkingen opslaan
	$db->query("SELECT opmerkingen FROM skipas_seizoen WHERE skipas_id='".addslashes($_GET["spid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
		$db->query("UPDATE skipas_seizoen SET opmerkingen='".addslashes($_POST["opmerkingen"])."' WHERE skipas_id='".addslashes($_GET["spid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	} else {
		$db->query("INSERT INTO skipas_seizoen SET opmerkingen='".addslashes($_POST["opmerkingen"])."', skipas_id='".addslashes($_GET["spid"])."', seizoen_id='".addslashes($_GET["sid"])."';");
	}
	
	# Accommodatietarieven aanpassen
	$skipastarieven_verwerken=true;
	unset($_POST);
	$db3->query("SELECT DISTINCT t.type_id FROM type t, accommodatie a, tarief ta WHERE t.type_id=ta.type_id AND t.accommodatie_id=a.accommodatie_id AND a.skipas_id='".addslashes($_GET["spid"])."' AND ta.seizoen_id='".addslashes($_GET["sid"])."'");
	while($db3->next_record()) {
		unset($seizoen,$acc,$skipas);
		$_GET["tid"]=$db3->f("type_id");
		include("cms_tarieven.php");
		reset($seizoen["weken"]);
		$db4->query("DELETE FROM tarief_personen WHERE type_id='".$db3->f("type_id")."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		while(list($key,$value)=each($seizoen["weken"])) {
			while(list($key2,$value2)=@each($value["verkoop_site"])) {
				$db4->query("INSERT INTO tarief_personen SET type_id='".$db3->f("type_id")."', seizoen_id='".addslashes($_GET["sid"])."', week='".$key."', personen='".addslashes($key2)."', prijs='".addslashes($value2)."', afwijking='".addslashes($value["verkoop_afwijking"][$key2])."';");
#				echo $db4->lastquery."<hr>";
			}
		}
	}

	if($_GET["from"]) {
		header("Location: ".$_GET["from"]);
	} else {
		header("Location: cms_skipassen.php?show=10&10k0=".$_GET["spid"]);
	}
	exit;
} else {
	# Gegevens ophalen uit database

	# Seizoengegevens laden
	$db->query("SELECT naam, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
		$seizoen["naam"]=$db->f("naam");
		$seizoen["begin"]=$db->f("begin");
		$seizoen["eind"]=$db->f("eind");
	}
	
	# Skipasgegevens laden
	$db->query("SELECT naam FROM skipas WHERE skipas_id='".addslashes($_GET["spid"])."';");
	if($db->next_record()) {
		$skipas["naam"]=$db->f("naam");
	}
	
	# Skipas-tarieven
	$db->query("SELECT week, bruto, netto_ink, korting, verkoopkorting, prijs, omzetbonus, wederverkoop_commissie_agent, netto FROM skipas_tarief WHERE skipas_id='".addslashes($_GET["spid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	while($db->next_record()) {
		reset($vars["sjabloon_skipas"]);
		while(list($key,$value)=each($vars["sjabloon_skipas"])) {
			if($db->f($value)>0) $skipas["weken"][$db->f("week")][$value]=$db->f($value);
		}
	}
	
	# Opmerkingen laden
	$db->query("SELECT opmerkingen FROM skipas_seizoen WHERE skipas_id='".addslashes($_GET["spid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
		$opmerkingen=$db->f("opmerkingen");
	}

	include("content/cms_skipastarieven.html");
}

?>