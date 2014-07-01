<?php

#
# JSON communicatie met de database voor het Chalet.nl-CMS
#


$vars["cmslog_pagina_niet_opslaan"]=true;
$mustlogin=true;
$unixdir="../";
include("../admin/vars.php");

#wt_jabber("boschman@gmail.com",$vars["basehref"].$_SERVER["REQUEST_URI"]);
#mail("jeroen@webtastic.nl","json","json ".wt_dump($_GET,false));

if($_GET["t"]==1) {
	#
	# Beschikbaar reserveringsnummer_2 opvragen bij garanties
	#
	if($_GET["leverancier_id"] and $_GET["aankomstdatum_exact"]) {
		$json["leverancier_id"]=$_GET["leverancier_id"];
		$json["aankomstdatum_exact"]=$_GET["aankomstdatum_exact"];
		$json["reserveringsnummer_2"]=get_reserveringsnummer_2($_GET["leverancier_id"],$_GET["aankomstdatum_exact"]);
		echo json_encode($json);
	}
} elseif($_GET["t"]==2) {
	#
	# Vertaling afvinken
	#
	$db->query("SELECT table_name, record_id FROM cmslog WHERE cmslog_id='".addslashes($_GET["cmslog_id"])."'");
	if($db->next_record()) {
		if(!$vars["cmstaal"]) $vars["cmstaal"]="en";
		$db2->query("UPDATE cmslog SET vertaald_".$vars["cmstaal"]."=1 WHERE table_name='".$db->f("table_name")."' AND record_id='".$db->f("record_id")."';");
	}

	$json["afgevinkt"]=1;
	echo json_encode($json);
} elseif($_GET["t"]==3) {
	#
	# Garantienummer bepalen
	#
	if($_GET["leverancier_id"] and $_GET["aankomstdatum_exact"]) {
		$json["leverancier_id"]=$_GET["leverancier_id"];
		$json["aankomstdatum_exact"]=$_GET["aankomstdatum_exact"];

		$lev_nr=substr("000".$_GET["leverancier_id"],-3);

#		$json["reserveringsnummer_2"]=get_reserveringsnummer_2($_GET["leverancier_id"],$_GET["aankomstdatum_exact"]);

		if(boekjaar($_GET["aankomstdatum_exact"])==2011) {
			$begincijfer=1;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2012) {
			$begincijfer=2;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2013) {
			$begincijfer=3;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2014) {
			$begincijfer=4;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2015) {
			$begincijfer=5;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2016) {
			$begincijfer=6;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2017) {
			$begincijfer=7;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2018) {
			$begincijfer=8;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2019) {
			$begincijfer=9;
		} elseif(boekjaar($_GET["aankomstdatum_exact"])==2020) {
			$begincijfer=0;
		} else {
			$begincijfer="XXX";
			trigger_error("garantienummer bepalen: boekjaar ".boekjaar($_GET["aankomstdatum_exact"])." niet bekend",E_USER_NOTICE);
		}

		# Hoogste nummer uit garantie boeking halen
		$db->query("SELECT SUBSTRING(reserveringsnummer_extern,5,3) AS reserveringsnummer_extern FROM garantie WHERE SUBSTRING(reserveringsnummer_extern,4,1)='".$begincijfer."' AND SUBSTRING(reserveringsnummer_extern,1,3)='".addslashes($lev_nr)."' AND CHAR_LENGTH(reserveringsnummer_extern)=7 ORDER BY SUBSTRING(reserveringsnummer_extern,5,3) DESC LIMIT 0,1;");
#		echo $db->lastquery;
		if($db->next_record()) {
			if($db->f("reserveringsnummer_extern")) {
				$boekingsnummer=intval($db->f("reserveringsnummer_extern")+1);
			}
		}
#echo $db->f("reserveringsnummer_extern")." ".$boekingsnummer;

		if($boekingsnummer) {
			$garantienummer=$lev_nr.$begincijfer.substr("000".strval($boekingsnummer),-3);
		} else {
			$garantienummer=$lev_nr.$begincijfer."001";
		}
		$json["garantienummer"]=$garantienummer;
		echo json_encode($json);
	}
} elseif($_GET["t"]==4) {
	//
	// copy prices on the fly
	//

	$json["ok"] = true;

	if($_GET["toonper"]==1) {
		$db->query("SELECT week, bruto FROM tarief WHERE type_id='".intval($_GET["tid"])."' AND seizoen_id='".intval($_GET["sid"])."' ORDER BY week;");
	} else {
		$db->query("SELECT week, c_bruto FROM tarief WHERE type_id='".intval($_GET["tid"])."' AND seizoen_id='".intval($_GET["sid"])."' ORDER BY week;");
	}
	if($db->num_rows()) {
		$json["prices"] = true;
		while($db->next_record()) {
			foreach ($db->Record as $key => $value) {
				if(!is_int($key) and $key!="week") {
					$json["week"][$db->f("week")][$key] = $value;
				}
			}
		}
	}
	echo json_encode($json);
}

?>