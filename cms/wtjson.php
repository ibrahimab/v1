<?php

#
# JSON communicatie met de database voor het Chalet.nl-CMS
#

if($_GET["t"]=="keep_session_alive" or $_GET["t"]=="bk_new" or $_GET["t"]=="bk_save") {
	$vars["cms_geen_aankomstdata_nodig"]=true;
}

$vars["cmslog_pagina_niet_opslaan"]=true;
$mustlogin=true;
$geen_tracker_cookie = true;
$unixdir="../";
include("../admin/vars.php");

#wt_jabber("boschman@gmail.com",$vars["basehref"].$_SERVER["REQUEST_URI"]);
#mail("jeroen@webtastic.nl","json","json ".wt_dump($_GET,false));


if($_GET["t"]=="keep_session_alive") {
	// keep PHP-session alive (cms_functions.js connects to this every 5 minutes)

	$_SESSION["keep_session_alive"] = $_SERVER["REQUEST_TIME_FLOAT"];

	$json["ok"] = true;

	echo json_encode($json);
	// wt_mail("systeembeheer@webtastic.nl", "keep_session_alive (test)", date("r")." ".$login->vars["voornaam"]." ".$_SERVER["REQUEST_URI"]);

} elseif($_GET["t"]==1) {
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

	$db->query("SELECT toonper FROM view_accommodatie WHERE type_id='".intval($_GET["tid"])."';");
	if($db->next_record()) {
		$toonper = $db->f("toonper");
	} else {
		$toonper = $_GET["toonper"];
	}

	if($_GET["toonper"]==1) {
		$as_name = "";
	} else {
		$as_name = "c_";
	}

	if($toonper==1) {
		$db->query("SELECT week, bruto AS ".$as_name."bruto, toeslag AS ".$as_name."toeslag, korting_euro AS ".$as_name."korting_euro FROM tarief WHERE type_id='".intval($_GET["tid"])."' AND seizoen_id='".intval($_GET["sid"])."' ORDER BY week;");
	} else {
		$db->query("SELECT week, c_bruto AS ".$as_name."bruto, c_toeslag AS ".$as_name."toeslag, c_korting_euro AS ".$as_name."korting_euro FROM tarief WHERE type_id='".intval($_GET["tid"])."' AND seizoen_id='".intval($_GET["sid"])."' ORDER BY week;");
	}
	if($db->num_rows()) {
		while($db->next_record()) {
			foreach ($db->Record as $key => $value) {
				if(!is_int($key) and $key!="week") {
					$include_field = false;

					if(($key=="bruto" or $key=="c_bruto") and $_GET["bruto"]) $include_field = true;
					if(($key=="toeslag" or $key=="c_toeslag") and $_GET["toeslag"]) $include_field = true;
					if(($key=="korting_euro" or $key=="c_korting_euro") and $_GET["korting"]) $include_field = true;

					if($include_field) {
						$json["week"][$db->f("week")][$key] = $value;
						$prices = true;
					}
				}
			}
		}
	}

	if($prices) {
		$json["prices"] = true;
	}

	echo json_encode($json);

} elseif($_GET["t"]=="bk_new") {

	// add bijkomende kosten-item


	$json["ok"] = true;


	$bijkomendekosten = new bijkomendekosten($_GET["id"], $_GET["soort"]);
	$json["html"] = $bijkomendekosten->cms_new_row($_GET["bk_soort_id"], true);

	echo json_encode($json);
} elseif($_GET["t"]=="bk_save") {
	//
	// save bijkomende kosten
	//

// if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
// 	sleep(1);
// }

	$json["ok"] = true;

	if($_GET["soort"]=="accommodatie" or $_GET["soort"]=="type") {
		if($_GET["start"]) {
			$db->query("UPDATE bk_".$_GET["soort"]." SET delete_after=1 WHERE ".$_GET["soort"]."_id='".intval($_GET["id"])."' AND seizoen_id='".intval($_GET["seizoen_id"])."' AND bk_soort_id NOT IN (".addslashes($_GET["not_inquery"]).");");
			if($_GET["soort"]=="accommodatie") {
				$db->query("UPDATE bk_type SET delete_after=1 WHERE type_id IN (SELECT type_id FROM type WHERE accommodatie_id='".intval($_GET["id"])."') AND seizoen_id='".intval($_GET["seizoen_id"])."' AND bk_soort_id NOT IN (".addslashes($_GET["not_inquery"]).");");
			}
			$json["saved"] = true;
		} elseif($_GET["stop"]) {
			$db->query("DELETE FROM bk_".$_GET["soort"]." WHERE delete_after=1 AND ".$_GET["soort"]."_id='".intval($_GET["id"])."' AND seizoen_id='".intval($_GET["seizoen_id"])."';");
			if($_GET["soort"]=="accommodatie") {
				$db->query("DELETE FROM bk_type WHERE delete_after=1 AND type_id IN (SELECT type_id FROM type WHERE accommodatie_id='".intval($_GET["id"])."') AND seizoen_id='".intval($_GET["seizoen_id"])."';");
			}

			$db->query("UPDATE ".$_GET["soort"]." SET tmp_teksten_omgezet='".intval($_GET["tmp_teksten_omgezet"])."' WHERE ".$_GET["soort"]."_id='".intval($_GET["id"])."';");


			// recalculate Redis-cache
			$bijkomendekosten = new bijkomendekosten();
			if($_GET["soort"]=="accommodatie") {
				$bijkomendekosten->pre_calculate_accommodation($_GET["id"]);
			} else {
				$bijkomendekosten->pre_calculate_type($_GET["id"]);
			}


			// save log
			$bijkomendekosten = new bijkomendekosten($_GET["id"], $_GET["soort"]);
			$bijkomendekosten->seizoen_id = $_GET["seizoen_id"];
			$all_rows = $bijkomendekosten->cms_all_rows();
			$json["all_rows_for_log"] = $bijkomendekosten->all_rows_for_log[$_GET["seizoen_id"]];

			if($_GET["soort"]=="accommodatie") {
				$cms_id=1;
			} else {
				$cms_id=2;
			}

			$_GET["all_rows_for_log"] = wt_utf8_decode($_GET["all_rows_for_log"]);

			if($_GET["all_rows_for_log"]<>$bijkomendekosten->all_rows_for_log[$_GET["seizoen_id"]]) {
				$db->query("INSERT INTO cmslog SET user_id='".addslashes($login->user_id)."', cms_id='".intval($cms_id)."', cms_name='".addslashes($cms->settings[$cms_id]["type_single"])."', record_id='".intval($_GET["id"])."', record_name='".addslashes($cmslog_recordname)."', table_name='".addslashes($cms->db[$counter]["maintable"])."', field='', field_name='bijkomende kosten', field_type='', previous='".addslashes($_GET["all_rows_for_log"])."', now='".addslashes($bijkomendekosten->all_rows_for_log[$_GET["seizoen_id"]])."', url='".addslashes($_SERVER["REQUEST_URI"])."', savedate=NOW();");
			}

			$json["saved"] = true;

		} else {

			if($_GET["bk_soort_id"]>0) {

				$query_key .= ", bk_soort_id='".intval($_GET["bk_soort_id"])."'";
				$query_key .= ", ".$_GET["soort"]."_id='".intval($_GET["id"])."'";
				$query_key .= ", seizoen_id='".intval($_GET["seizoen_id"])."'";

				$query .= ", delete_after=0";

				$query .= ", inclusief='".($_GET["inclusief"]=="undefined" ? "NULL" : intval($_GET["inclusief"]))."'";
				if($_GET["inclusief"]==1) {
					// inclusief
					$query .= ", verplicht=NULL";
					$query .= ", ter_plaatse=NULL";
					$query .= ", eenheid=NULL";
					$query .= ", borg_soort=NULL";
					$query .= ", bedrag=NULL";
				} else {
					$query .= ", verplicht='".($_GET["verplicht"]=="undefined" ? "NULL" : intval($_GET["verplicht"]))."'";
					$query .= ", borg_soort='".($_GET["borg_soort"]=="undefined" ? "NULL" : intval($_GET["borg_soort"]))."'";

					if($_GET["verplicht"]==3) {
						// zelf te verzorgen
						$query .= ", ter_plaatse=NULL";
						$query .= ", eenheid=NULL";
						$query .= ", bedrag=NULL";
					} elseif($_GET["borg_soort"]==4 or $_GET["borg_soort"]==5) {
						// borg: niet van toepassing / bedrag onbekend
						$query .= ", ter_plaatse=NULL";
						$query .= ", eenheid=NULL";
						$query .= ", bedrag=NULL";
					} else {
						$query .= ", ter_plaatse='".($_GET["ter_plaatse"]=="undefined" ? "NULL" : intval($_GET["ter_plaatse"]))."'";
						$query .= ", eenheid='".($_GET["eenheid"]=="undefined" ? "NULL" : intval($_GET["eenheid"]))."'";

						if($_GET["bedrag"]=="undefined" or !isset($_GET["bedrag"])) {
							$query .= ", bedrag=NULL";
						} else {
							$_GET["bedrag"] = preg_replace("@,@", ".", $_GET["bedrag"]);
							$query .= ", bedrag='".addslashes($_GET["bedrag"])."'";
						}
					}
				}

				$db->query("INSERT INTO bk_".$_GET["soort"]." SET ".substr($query_key,1).$query.", adddatetime=NOW(), editdatetime=NOW() ON DUPLICATE KEY UPDATE ".substr($query,1).", editdatetime=NOW()");

				if($_GET["soort"]=="accommodatie") {

					$db->query("SELECT type_id FROM type WHERE accommodatie_id='".intval($_GET["id"])."';");
					while($db->next_record()) {
						unset($query_key);
						$query_key .= ", bk_soort_id='".intval($_GET["bk_soort_id"])."'";
						$query_key .= ", type_id='".intval($db->f("type_id"))."'";
						$query_key .= ", seizoen_id='".intval($_GET["seizoen_id"])."'";

						$db2->query("INSERT INTO bk_type SET ".substr($query_key,1).$query.", adddatetime=NOW(), editdatetime=NOW() ON DUPLICATE KEY UPDATE ".substr($query,1).", editdatetime=NOW()");

					}

				}
			}
			$json["saved"] = true;
		}

	}

	echo json_encode($json);
} elseif($_GET["t"]=="bk_copy") {
	// copy bijkomende kosten from other type

	$bijkomendekosten = new bijkomendekosten(intval($_GET["type_id"]), "type");
	$bijkomendekosten->seizoen_id = intval($_GET["sid"]);
	$bijkomendekosten->copy = true;

	$json["cms_bk_all_rows"] = $bijkomendekosten->cms_all_rows();


	echo json_encode($json);
} elseif($_GET["t"]=="bk_opmerkingen_intern") {

	if($_GET["soort"]=="type") {
		$db->query("SELECT accommodatie_id FROM type WHERE type_id='".intval($_GET["id"])."';");
		if($db->next_record()) {
			$accommodatie_id = $db->f("accommodatie_id");
		}

	} else {
		$accommodatie_id = $_GET["id"];
	}
	$a = iconv("CP1252", "UTF-8", $a);


	if($accommodatie_id) {
		$db->query("UPDATE accommodatie SET bk_opmerkingen_intern='".addslashes(wt_utf8_decode($_POST["bk_opmerkingen_intern"]))."' WHERE accommodatie_id='".intval($accommodatie_id)."';");
	}

	$json["saved"] = true;
	echo json_encode($json);
}

?>