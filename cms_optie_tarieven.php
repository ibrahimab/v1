<?php

#
# Via dit onderdeel worden zowel optie-tarieven als bijkomendekosten-tarieven opgeslagen
#

$mustlogin=true;

include("admin/vars.php");

if($_GET["bkid"]) {
	# Bijkomende kosten
	$optie_of_bijkomendekosten["velden"]=$vars["bijkomendekosten_velden"];
	$optie_of_bijkomendekosten["table"]="bijkomendekosten_tarief";
	$optie_of_bijkomendekosten["primkey"]="bijkomendekosten_id";
	$optie_of_bijkomendekosten["primkey_value"]=$_GET["bkid"];
} else {
	# Opties
	$optie_of_bijkomendekosten["velden"]=$vars["tarief_optie_velden"];
	$optie_of_bijkomendekosten["table"]="optie_tarief";
	$optie_of_bijkomendekosten["primkey"]="optie_onderdeel_id";
	$optie_of_bijkomendekosten["primkey_value"]=$_GET["ooid"];
}

if($_POST["filled"]) {

	# Gegevens opslaan in database
	reset($optie_of_bijkomendekosten["velden"]);
	while(list($key,$value)=each($optie_of_bijkomendekosten["velden"])) {
		@reset($_POST[$value]);
		while(list($key2,$value2)=@each($_POST[$value])) {
			$savequery[$key2].=", ".$value."='".addslashes($value2)."'";
		}
	}

	# Eerst gegevens wissen
	$db->query("DELETE FROM ".$optie_of_bijkomendekosten["table"]." WHERE ".$optie_of_bijkomendekosten["primkey"]."='".addslashes($optie_of_bijkomendekosten["primkey_value"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");

	# Dan opslaan
	while(list($key,$value)=each($savequery)) {
		$db->query("INSERT INTO ".$optie_of_bijkomendekosten["table"]." SET ".$optie_of_bijkomendekosten["primkey"]."='".addslashes($optie_of_bijkomendekosten["primkey_value"])."', seizoen_id='".addslashes($_GET["sid"])."', week='".$key."'".$value.";");
	}

	if($_GET["bkid"]) {
		$bijkomendekosten = new bijkomendekosten;
		$bijkomendekosten->pre_calculate_variable_costs($_GET["bkid"]);
	}

	header("Location: ".$_GET["from"]);
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

	if($_GET["bkid"]) {
		# Bijkomendekosten-gegevens laden
		$db->query("SELECT internenaam FROM bijkomendekosten WHERE bijkomendekosten_id='".addslashes($_GET["bkid"])."';");
		if($db->next_record()) {
			$optie["naam"]="Bijkomende kosten: ".$db->f("internenaam");
		}

	} else {
		# Optiegegevens laden
		$db->query("SELECT os.naam AS osnaam, og.naam AS ognaam, oo.naam AS oonaam, os.optie_soort_id FROM optie_soort os, optie_groep og, optie_onderdeel oo WHERE oo.optie_onderdeel_id='".addslashes($_GET["ooid"])."' AND oo.optie_groep_id=og.optie_groep_id AND og.optie_soort_id=os.optie_soort_id;");
		if($db->next_record()) {
			$optie["naam"]="Optie: ".$db->f("osnaam")." > ".$db->f("ognaam")." > ".$db->f("oonaam");
			if($db->f("optie_soort_id")==41) {

				# bewerken niet mogelijk: hoort bij optiesoort "Losse skipassen (voor wederverkoop, gekoppeld)" die is gekoppeld aan de skipassen
				$losse_skipassen_wederverkoop=true;
			}
		}

		# Is er een skipaskoppeling?
		if(!$losse_skipassen_wederverkoop) {
			$db->query("SELECT og.skipas_id, st.week, st.prijs FROM optie_groep og, optie_onderdeel oo, skipas_tarief st WHERE og.skipas_id=st.skipas_id AND oo.optie_onderdeel_id='".addslashes($_GET["ooid"])."' AND oo.optie_groep_id=og.optie_groep_id AND st.seizoen_id='".addslashes($_GET["sid"])."';");
			if($db->num_rows()) {
				$seizoen["skipas_koppeling"]=true;
				while($db->next_record()) {
					$seizoen["weken"][$db->f("week")]["skipas_netto_inkoop"]=$db->f("prijs");
				}
			}
		}
	}


	$db->query("SELECT ".$optie_of_bijkomendekosten["primkey"]." FROM ".$optie_of_bijkomendekosten["table"]." WHERE ".$optie_of_bijkomendekosten["primkey"]."='".addslashes($optie_of_bijkomendekosten["primkey_value"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
		# Tarieven uit tabel tarief
		$db->query("SELECT week, beschikbaar, verkoop, netto_ink, inkoop, korting, korting_euro, omzetbonus, wederverkoop_commissie_agent FROM ".$optie_of_bijkomendekosten["table"]." WHERE ".$optie_of_bijkomendekosten["primkey"]."='".addslashes($optie_of_bijkomendekosten["primkey_value"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		while($db->next_record()) {
			reset($optie_of_bijkomendekosten["velden"]);
			while(list($key,$value)=each($optie_of_bijkomendekosten["velden"])) {
				if($db->f($value)<>0) $seizoen["weken"][$db->f("week")][$value]=$db->f($value);
			}

			$verkoop_totaal+=$db->f("verkoop");

			# Tarieven doorrekenen
			$seizoen["weken"][$db->f("week")]["inkoop_netto"]=$seizoen["weken"][$db->f("week")]["inkoop"];
			if($seizoen["weken"][$db->f("week")]["netto_ink"]<>0) {
				$seizoen["weken"][$db->f("week")]["inkoop_netto"]=$seizoen["weken"][$db->f("week")]["netto_ink"];
			} else {
				if($seizoen["weken"][$db->f("week")]["korting"]) {
					$seizoen["weken"][$db->f("week")]["inkoop_netto"]=$seizoen["weken"][$db->f("week")]["inkoop_netto"]*(1-$seizoen["weken"][$db->f("week")]["korting"]/100);
				}
				if($seizoen["weken"][$db->f("week")]["korting_euro"]) {
					$seizoen["weken"][$db->f("week")]["inkoop_netto"]=$seizoen["weken"][$db->f("week")]["inkoop_netto"]-$seizoen["weken"][$db->f("week")]["korting_euro"];
				}
			}
			$seizoen["weken"][$db->f("week")]["subtotaal"]=$seizoen["weken"][$db->f("week")]["inkoop_netto"]-$seizoen["weken"][$db->f("week")]["skipas_netto_inkoop"];

			if($seizoen["weken"][$db->f("week")]["omzetbonus"]) {
				$seizoen["weken"][$db->f("week")]["inkoop_netto"]=$seizoen["weken"][$db->f("week")]["subtotaal"]*(1-$seizoen["weken"][$db->f("week")]["omzetbonus"]/100);
			} else {
				$seizoen["weken"][$db->f("week")]["inkoop_netto"]=$seizoen["weken"][$db->f("week")]["subtotaal"];
			}
			if($seizoen["weken"][$db->f("week")]["verkoop"]<>0) {
				$seizoen["weken"][$db->f("week")]["marge_euro"]=$seizoen["weken"][$db->f("week")]["verkoop"]-$seizoen["weken"][$db->f("week")]["inkoop_netto"];
				$seizoen["weken"][$db->f("week")]["marge_percentage"]=$seizoen["weken"][$db->f("week")]["marge_euro"]/$seizoen["weken"][$db->f("week")]["verkoop"]*100;
				$marge_gemiddeld=$marge_gemiddeld+$seizoen["weken"][$db->f("week")]["marge_percentage"];
				$marge_gemiddeld_teller++;
			}

			# Wederverkoop doorrekenen
			if($seizoen["weken"][$db->f("week")]["verkoop"]>0 and $seizoen["weken"][$db->f("week")]["inkoop"]>0 and $seizoen["weken"][$db->f("week")]["wederverkoop_commissie_agent"]>0) {
				$seizoen["weken"][$db->f("week")]["wederverkoop_nettoprijs_agent"]=$seizoen["weken"][$db->f("week")]["verkoop"]-$seizoen["weken"][$db->f("week")]["verkoop"]*($seizoen["weken"][$db->f("week")]["wederverkoop_commissie_agent"]/100);
				$seizoen["weken"][$db->f("week")]["wederverkoop_resterende_marge"]=$seizoen["weken"][$db->f("week")]["wederverkoop_nettoprijs_agent"]-$seizoen["weken"][$db->f("week")]["inkoop_netto"];
				if($seizoen["weken"][$db->f("week")]["wederverkoop_nettoprijs_agent"]>0) {
					$seizoen["weken"][$db->f("week")]["wederverkoop_marge"]=$seizoen["weken"][$db->f("week")]["wederverkoop_resterende_marge"]/$seizoen["weken"][$db->f("week")]["wederverkoop_nettoprijs_agent"]*100;
				}
			}

			$seizoen["weken"][$db->f("week")]["subtotaal"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["subtotaal"]));
			$seizoen["weken"][$db->f("week")]["inkoop_netto"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["inkoop_netto"]));
			$seizoen["weken"][$db->f("week")]["marge_euro"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["marge_euro"]));
			$seizoen["weken"][$db->f("week")]["marge_percentage"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["marge_percentage"]));

			$seizoen["weken"][$db->f("week")]["wederverkoop_commissie_agent"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["wederverkoop_commissie_agent"]));
			if($seizoen["weken"][$db->f("week")]["verkoop"]>0 and $seizoen["weken"][$db->f("week")]["inkoop"]>0 and $seizoen["weken"][$db->f("week")]["wederverkoop_commissie_agent"]>0) {
				$seizoen["weken"][$db->f("week")]["wederverkoop_nettoprijs_agent"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["wederverkoop_nettoprijs_agent"]));
				$seizoen["weken"][$db->f("week")]["wederverkoop_resterende_marge"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["wederverkoop_resterende_marge"]));
				$seizoen["weken"][$db->f("week")]["wederverkoop_marge"]=ereg_replace(",",".",sprintf("%01.2f",$seizoen["weken"][$db->f("week")]["wederverkoop_marge"]));
			}
		}
		if($marge_gemiddeld_teller) {
			$seizoen["marge_gemiddeld"]=ereg_replace(",",".",sprintf("%01.2f",($marge_gemiddeld/$marge_gemiddeld_teller)));
		}

	} else {
		$seizoen["leeg"]=true;

		$week=$seizoen["begin"];
		while($week<=$seizoen["eind"]) {
			$regelteller++;
			$seizoen["weken"][$week]["wederverkoop_commissie_agent"]="5.00";
			$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}
	}

	include("content/cms_optie_tarieven.html");
}

?>