<?php

$mustlogin=true;

include("admin/vars.php");

if(($_GET["goedkeuren"] or $_GET["afkeuren"]) and $_GET["confirmed"]) {
	$gegevens=boekinginfo($_GET["21k0"]);
	$accinfo=accinfo($gegevens["stap1"]["typeid"],$gegevens["stap1"]["aankomstdatum"],$gegevens["stap1"]["aantalpersonen"]);
	if($_GET["goedkeuren"]) {
		$db->query("SELECT boeking_id FROM boeking_optie WHERE status=2 AND boeking_id='".addslashes($_GET["21k0"])."';");
		if($db->num_rows()) {
			$db->query("DELETE FROM boeking_optie WHERE status=1 AND boeking_id='".addslashes($_GET["21k0"])."';");
			$db->query("UPDATE boeking_optie SET status=1 WHERE boeking_id='".addslashes($_GET["21k0"])."';");
		}
		$db->query("UPDATE boeking SET gewijzigd='', factuur_versturen='".($gegevens["stap1"]["factuur_tewijzigen"] ? 1 : 0)."', factuur_tewijzigen=0 WHERE boeking_id='".addslashes($_GET["21k0"])."';");
		$db->query("UPDATE boeking_persoon SET annverz_voorheen=NULL WHERE boeking_id='".addslashes($_GET["21k0"])."';");

		chalet_log("onderdelen goedgekeurd",true,true);
	} elseif($_GET["afkeuren"]) {
		$db->query("DELETE FROM boeking_optie WHERE status=2 AND boeking_id='".addslashes($_GET["21k0"])."';");
		if(ereg("ANN_UIT",$gegevens["stap1"]["gewijzigd"])) {
			$db->query("UPDATE boeking SET annuleringsverzekering=1 WHERE boeking_id='".addslashes($_GET["21k0"])."';");
		} elseif(ereg("ANN_AAN",$gegevens["stap1"]["gewijzigd"])) {
			$db->query("UPDATE boeking SET annuleringsverzekering=0 WHERE boeking_id='".addslashes($_GET["21k0"])."';");
		}
		if(ereg("SCHADEVERZEKERING",$gegevens["stap1"]["gewijzigd"])) {
			if($gegevens["stap1"]["schadeverzekering"]) {
				$db->query("UPDATE boeking SET schadeverzekering=0 WHERE boeking_id='".addslashes($_GET["21k0"])."';");
			} else {
				$db->query("UPDATE boeking SET schadeverzekering=1 WHERE boeking_id='".addslashes($_GET["21k0"])."';");
			}
		}

		if(ereg("AANTAL_PERSONEN_VAN_([0-9]+)",$gegevens["stap1"]["gewijzigd"],$regs)) {
			if($accinfo["toonper"]<>3 and !$gegevens["stap1"]["wederverkoop"]) {
				$db->query("SELECT verkoop FROM boeking_tarief WHERE aantalpersonen='".addslashes($gegevens["stap1"]["aantalpersonen"])."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."'");
				if($db->next_record()) {
					$nieuw_tarief=$db->f("verkoop");
				}
			} else {
				$nieuw_tarief=$gegevens["stap1"]["verkoop"];
			}
			$db->query("UPDATE boeking SET aantalpersonen='".addslashes($regs[1])."', verkoop='".addslashes($nieuw_tarief)."' WHERE boeking_id='".addslashes($_GET["21k0"])."';");
		}
		$db->query("SELECT persoonnummer, annverz_voorheen FROM boeking_persoon WHERE annverz_voorheen IS NOT NULL AND boeking_id='".addslashes($_GET["21k0"])."';");
		while($db->next_record()) {
			$db2->query("UPDATE boeking_persoon SET annverz='".$db->f("annverz_voorheen")."', annverz_voorheen=NULL WHERE boeking_id='".addslashes($_GET["21k0"])."' AND persoonnummer='".$db->f("persoonnummer")."';");
		}
		$db->query("UPDATE boeking SET gewijzigd='', factuur_tewijzigen=0 WHERE boeking_id='".addslashes($_GET["21k0"])."';");
		bereken_bijkomendekosten($_GET["21k0"]);
		chalet_log("onderdelen afgekeurd",true,true);
	}
#	header("Location: ".$path."cms_boekingen.php?show=21&bc=".$_GET["bc"]."&21k0=".$_GET["21k0"]);
	$url=$_SERVER["REQUEST_URI"];
	$url=ereg_replace("&goedkeuren=1","",$url);
	$url=ereg_replace("&afkeuren=1","",$url);
	$url=ereg_replace("&confirmed=1","",$url);
	header("Location: ".$url);
	exit;
}

if($_GET["deblock"]) {
	$db->query("UPDATE boekinguser SET wrongtime=0, wrongcount=0, wronghost='' WHERE user_id='".addslashes($_GET["deblock"])."'");
	header("Location: ".$_GET["back"]);
	exit;
}

if(!$_GET["21k0"] and $_GET["bt"]==3) {
	# Oude onafgeronde boekingen wissen (14 dagen en ouder)
	unset($inquery);
	$db->query("SELECT boeking_id FROM boeking WHERE bevestigdatum IS NULL AND boekingsnummer='' AND goedgekeurd=0 AND UNIX_TIMESTAMP(invuldatum)<'".(time()-86400*14)."';");
	while($db->next_record()) {
		if($inquery) $inquery.=",".$db->f("boeking_id"); else $inquery=$db->f("boeking_id");
	}
	if($inquery) {
		$db->query("DELETE FROM boeking_optie WHERE boeking_id IN (".$inquery.");");
		$db->query("DELETE FROM boeking_persoon WHERE boeking_id IN (".$inquery.");");
		$db->query("DELETE FROM extra_optie WHERE boeking_id IN (".$inquery.");");
		$db->query("DELETE FROM boeking_tarief WHERE boeking_id IN (".$inquery.");");
		$db->query("DELETE FROM boeking WHERE boeking_id IN (".$inquery.");");
	}
}

function bestelstatus_tekst($bestelstatus, $opmerkingen_intern, $besteluser_id) {

	global $vars, $werknemer;

	if($_GET["bt"]==8) {
		if($besteluser_id) {
			$return.=wt_he($werknemer[$besteluser_id]);
		}
	} elseif($bestelstatus<>1) {
		$return.=preg_replace("@ @","&nbsp;",$vars["bestelstatus"][$bestelstatus]);
	}
	if($opmerkingen_intern) {
		if($return) {
			$return.="<br/>";
		}
		$return.="<a href=\"#\" onclick=\"return false;\" class=\"opm\"><span class=\"balloon_small2\">".nl2br(wt_he($opmerkingen_intern))."</span>opmerking</a>";
	}

	if(!$return and $_GET["bt"]<>8) {
		$return="<span class=\"bestelstatus_hele_tr_opvallend\"></span>";
	}

	return $return;

}

if($_GET["show"]<>21) {
	# Boekingsgegevens laden (gewone boekingen)
	$db->query("SELECT bp.boeking_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.email, b.toonper, b.wederverkoop, b.bestelstatus, b.besteluser_id, b.opmerkingen_intern FROM boeking b, boeking_persoon bp WHERE bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.reisbureau_user_id IS NULL;");
	while($db->next_record()) {
		$boekingsgegevens["hoofdboeker"][$db->f("boeking_id")]=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"))." (".$db->f("email").")";
		$boekingsgegevens["acc_of_arrangement"][$db->f("boeking_id")]=($db->f("toonper")==3||$db->f("wederverkoop") ? "Acc" : "Arr");
		$boekingsgegevens["bestelstatus"][$db->f("boeking_id")]=bestelstatus_tekst($db->f("bestelstatus"), $db->f("opmerkingen_intern"), $db->f("besteluser_id"));
	}

	# Boekingsgegevens laden (wederverkoop-boekingen)
	$db->query("SELECT bp.boeking_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.email, r.naam, b.reisbureau_user_id, b.bestelstatus, b.besteluser_id, b.opmerkingen_intern FROM boeking b, boeking_persoon bp, reisbureau r, reisbureau_user ru WHERE bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.reisbureau_user_id=ru.user_id AND ru.reisbureau_id=r.reisbureau_id;");
	while($db->next_record()) {
		$boekingsgegevens["hoofdboeker"][$db->f("boeking_id")]=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"))." (".$db->f("naam").")";
		$boekingsgegevens["acc_of_arrangement"][$db->f("boeking_id")]=($db->f("reisbureau_user_id") ? "Acc + Com" : "Acc");
		$boekingsgegevens["bestelstatus"][$db->f("boeking_id")]=bestelstatus_tekst($db->f("bestelstatus"), $db->f("opmerkingen_intern"), $db->f("besteluser_id"));
	}

	# Boekingsgegevens laden (enquetes)
	if($_GET["archief"]==1) {
		$db->query("SELECT boeking_id FROM boeking_enquete;");
		while($db->next_record()) {
			$boekingsgegevens["enquete"][$db->f("boeking_id")]="ja";
		}
	}
}

if($_GET["21k0"]) {
	$db->query("SELECT type_id, aankomstdatum, seizoen_id, log, website FROM boeking WHERE boeking_id='".addslashes($_GET["21k0"])."';");
	if($db->next_record()) {
		$vars["typeid"]=$db->f("type_id");
		$aankomstdatum=$db->f("aankomstdatum");
		$seizoenid=$db->f("seizoen_id");
		$vars["log"]=$db->f("log");

		if(!$vars["lokale_testserver"] and !$vars["acceptatie_testserver"] and !$vars["backup_server"] and !$vars["wwwtest"] and $login->userlevel<10) {
			if($vars["websites_wzt"][2][$db->f("website")]) {
				$layout->settings["extra_cssfiles"][]="css/cms_layout_bgcolor.css.phpcache?bg=95ddec";
			} elseif($db->f("website")=="Y") {
				$layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_bgcolor.css.phpcache?bg=fff093";
			}
		}
	}
	$accinfo=accinfo($vars["typeid"]);

	#
	# Aankomstdata laden
	#
	# Vertrekdagaanpassingen verwerken
#	include("content/vertrekdagaanpassing.html");
#	$db->query("SELECT t.week, s.seizoen_id FROM tarief t, seizoen s WHERE t.seizoen_id=s.seizoen_id AND s.seizoen_id='".addslashes($seizoenid)."' AND t.type_id='".addslashes($vars["typeid"])."';");
#	while($db->next_record()) {
#		if($vertrekdag[$db->f("seizoen_id")][date("dm",$db->f("week"))]) {
#			$aankomstdatum_unixtime=mktime(0,0,0,date("m",$db->f("week")),date("d",$db->f("week"))+$vertrekdag[$db->f("seizoen_id")][date("dm",$db->f("week"))],date("Y",$db->f("week")));
#		} else {
#			$aankomstdatum_unixtime=$db->f("week");
#		}
#		$vars["aankomstdatum_boeking"][$db->f("week")]=datum("DAG D MAAND JJJJ",$aankomstdatum_unixtime);
#	}
}

# Alle accommodaties laden
$db->query("SELECT a.naam AS accommodatie, a.soortaccommodatie, a.toonper, a.accommodatie_id, t.type_id, t.naam AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l WHERE t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id ORDER BY p.naam, a.naam, t.type_id, t.naam, t.maxaantalpersonen;");
while($db->next_record()) {
	$vars["alle_types"][$db->f("type_id")]=substr($db->f("plaats")." - ".$db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : ""),0,50)." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p - ".$db->f("begincode").$db->f("type_id").") ";
}

#echo wt_dump($vars["aankomstdatum"]);

$cms->settings[21]["list"]["show_icon"]=true;
$cms->settings[21]["list"]["edit_icon"]=false;
$cms->settings[21]["list"]["add_link"]=false;
$cms->settings[21]["list"]["delete_icon"]=false;

if($_GET["bt"]==1) {
	# Aangevraagd
	$cms->db[21]["where"]="stap_voltooid=5 AND goedgekeurd=0 AND vervallen_aanvraag=0";
	if($login->has_priv("2")) $cms->settings[21]["list"]["delete_icon"]=true;
} elseif($_GET["bt"]==2) {
	# Bevestigd
	$cms->db[21]["where"]="geannuleerd=0 AND stap_voltooid=5 AND goedgekeurd=1 AND aankomstdatum_exact>'".(time()-86400*8)."'";
	if($login->has_priv("2")) $cms->settings[21]["list"]["delete_icon"]=true;
} elseif($_GET["bt"]==3) {
	# Recent onafgerond
	$cms->db[21]["where"]="bevestigdatum IS NULL AND stap_voltooid>=1 AND calc=0";
	if($login->has_priv("2")) $cms->settings[21]["list"]["delete_icon"]=true;
} elseif($_GET["bt"]==4) {
	# Archief
	$cms->db[21]["where"]="stap_voltooid=5 AND goedgekeurd=1 AND aankomstdatum_exact<'".(time()-86400*8)."'";
	$cms->settings[21]["list"]["delete_icon"]=false;
} elseif($_GET["bt"]==5) {
	# Alleen actuele boekingen (tussen aankomstdatum_exact 8 dagen geleden en over 20 dagen)
	$cms->db[21]["where"]="geannuleerd=0 AND stap_voltooid=5 AND goedgekeurd=1 AND aankomstdatum_exact>'".(time()-86400*8)."' AND aankomstdatum_exact<'".(time()+86400*20)."'";
	$cms->settings[21]["list"]["delete_icon"]=false;
} elseif($_GET["bt"]==6) {
	# Geannuleerd
	$cms->db[21]["where"]="geannuleerd=1";
	if($login->has_priv("2")) $cms->settings[21]["list"]["delete_icon"]=true;
} elseif($_GET["bt"]==7) {
	// vervallen aanvragen
	$cms->db[21]["where"]="vervallen_aanvraag=1";
} elseif($_GET["bt"]==8) {
	// bestelstatus tonen
	if(!$_GET["bestelstatus"]) {
		$_GET["bestelstatus"] = 1;
	}
	$cms->db[21]["where"] = "bestelstatus='".intval($_GET["bestelstatus"])."' AND geannuleerd=0 AND stap_voltooid=5 AND goedgekeurd=1";
	$cms->settings[21]["list"]["delete_icon"]=false;
} elseif($_GET["boekingsearch"]) {
	if(ereg("@",$_GET["boekingsearch"])) {
		$db->query("SELECT boeking_id FROM boeking_persoon WHERE email LIKE '%".addslashes(strtolower($_GET["boekingsearch"]))."%';");
		if($db->num_rows()) {
			unset($boekingen_inquery);
			while($db->next_record()) {
				if($boekingen_inquery) $boekingen_inquery.=",".$db->f("boeking_id"); else $boekingen_inquery=$db->f("boeking_id");
			}
		}
	} elseif(ereg("^_[0-9]+$",$_GET["boekingsearch"])) {
		unset($type_inquery,$boekingen_inquery);
#		$_GET["boekingsearch"]=substr($_GET["boekingsearch"],1);
		$db->query("SELECT type_id FROM type WHERE accommodatie_id='".addslashes(substr($_GET["boekingsearch"],1))."';");
		while($db->next_record()) {
			if($type_inquery) $type_inquery.=",".$db->f("type_id"); else $type_inquery=$db->f("type_id");
		}
		if($type_inquery) {
			$db->query("SELECT boeking_id FROM boeking WHERE type_id IN (".$type_inquery.");");
			while($db->next_record()) {
				if($boekingen_inquery) $boekingen_inquery.=",".$db->f("boeking_id"); else $boekingen_inquery=$db->f("boeking_id");
			}
		}
	} else {
		$db->query("SELECT boeking_id FROM boeking_persoon WHERE (voornaam LIKE '%".addslashes(strtolower($_GET["boekingsearch"]))."%' OR achternaam LIKE '%".addslashes(strtolower($_GET["boekingsearch"]))."%' OR email LIKE '%".addslashes(strtolower($_GET["boekingsearch"]))."%');");
		if($db->num_rows()) {
			unset($boekingen_inquery);
			while($db->next_record()) {
				if($boekingen_inquery) $boekingen_inquery.=",".$db->f("boeking_id"); else $boekingen_inquery=$db->f("boeking_id");
			}
		}
		$db->query("SELECT b.boeking_id FROM boeking b, reisbureau r, reisbureau_user ru WHERE b.reisbureau_user_id=ru.user_id AND ru.reisbureau_id=r.reisbureau_id AND (r.naam LIKE '%".addslashes(strtolower($_GET["boekingsearch"]))."%' OR ru.naam LIKE '%".addslashes(strtolower($_GET["boekingsearch"]))."%' OR ru.email LIKE '%".addslashes(strtolower($_GET["boekingsearch"]))."%');");
		if($db->num_rows()) {
			while($db->next_record()) {
				if($boekingen_inquery) $boekingen_inquery.=",".$db->f("boeking_id"); else $boekingen_inquery=$db->f("boeking_id");
			}
		}
	}
	if(!$boekingen_inquery) $boekingen_inquery="0";
	$cms->db[21]["where"]="boeking_id IN (".addslashes($boekingen_inquery).") AND goedgekeurd=1";
	$cms->settings[21]["list"]["delete_icon"]=false;
}

if($_GET["edit"]==21 or $_GET["show"]==21) {
	unset($cms->db[21]["where"]);
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(21,"noedit","boeking_id");
$cms->db_field(21,"text","website");
$cms->db_field(21,"select","boekingsgegevens","boeking_id",array("selection"=>$boekingsgegevens["hoofdboeker"]));
$cms->db_field(21,"select","acc_of_arrangement","boeking_id",array("selection"=>$boekingsgegevens["acc_of_arrangement"]));
if($_GET["archief"]==1) {
	$cms->db_field(21,"select","enquete","boeking_id",array("selection"=>$boekingsgegevens["enquete"]));
}

if($_GET["bt"]==1 or $_GET["bt"]==7) {
	$cms->db_field(21,"select","bestelstatus","boeking_id",array("selection"=>$boekingsgegevens["bestelstatus"]));
} elseif($_GET["bt"]==8) {
	$cms->db_field(21,"select","besteluser_id","boeking_id",array("selection"=>$boekingsgegevens["bestelstatus"]));
}


$cms->db_field(21,"text","boekingsnummer");
$cms->db_field(21,"select","type_id","",array("selection"=>$vars["alle_types"]));
#$cms->db_field(21,"select","aankomstdatum","",array("selection"=>($vars["aankomstdatum_boeking"] ? $vars["aankomstdatum_boeking"] : $vars["aankomstdatum_weekend_alleseizoenen"])));
$cms->db_field(21,"date","aankomstdatum");
$cms->db_field(21,"date","aankomstdatum_exact");
$cms->db_field(21,"date","invuldatum");
$cms->db_field(21,"select","aantalpersonen","",array("selection"=>$accinfo["aantalpersonen"]));
$cms->db_field(21,"select","stap_voltooid","",array("selection"=>$vars["boeken"]));
if($_GET["bt"]==8) {
	// $cms->db_field(21,"select","besteluser_id","",array("othertable"=>"25","otherkeyfield"=>"user_id","otherfield"=>"voornaam"));
}

#$cms->list_sort[21]=array("boeking_id");
$cms->list_sort_desc[21]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
if($_GET["boekingsearch"]) {
	// na zoekopdracht
	$cms->list_field(21,"website","Site");
	$cms->list_field(21,"boekingsnummer","Nr",array("sort_substring"=>array(1)));
	$cms->list_sort[21]=array("aankomstdatum_exact","website");
	$cms->list_sort_desc[21]=true;
} elseif($_GET["bt"]==1 or $_GET["bt"]==7) {
	// aangevraagd
	$cms->list_field(21,"website","Site");
	$cms->list_field(21,"boeking_id","Nr");
	$cms->list_field(21,"bestelstatus","Bestelstatus","",array("html"=>true));
	$cms->list_sort[21]=array("boeking_id");
	$cms->list_sort_desc[21]=true;
} elseif($_GET["bt"]==2) {
	// bevestigd
	$cms->list_field(21,"website","Site");
	$cms->list_field(21,"boekingsnummer","Nr",array("sort_substring"=>array(1)));
	$cms->list_sort[21]=array("boekingsnummer","website");
	$cms->list_sort_desc[21]=true;
} elseif($_GET["bt"]==3) {
	// recent onafgerond
	$cms->list_field(21,"website","Site");
	$cms->list_field(21,"invuldatum","Ingevuld",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_sort[21]=array("invuldatum");
	$cms->list_sort_desc[21]=true;
} elseif($_GET["bt"]==4) {
	$cms->list_field(21,"website","Site");
	$cms->list_field(21,"boekingsnummer","Nr",array("sort_substring"=>array(1)));
	$cms->list_sort[21]=array("boekingsnummer","website");
	$cms->list_sort_desc[21]=false;
} elseif($_GET["bt"]==5) {
	// actueel
	$cms->list_field(21,"boekingsnummer","Nr",array("sort_substring"=>array(1)));
	$cms->list_sort[21]=array("aankomstdatum_exact");
	$cms->list_sort_desc[21]=false;
} elseif($_GET["bt"]==8) {
	// bestelstatus
	$cms->list_field(21,"website","Site");
	$cms->list_field(21,"boekingsnummer","Nr",array("sort_substring"=>array(1)));

	if($_GET["bestelstatus"]==2) {
		$cms->list_field(21,"besteluser_id","Besteld door","",array("html"=>true));
	}

	$cms->list_sort[21]=array("aankomstdatum_exact","website");
	$cms->list_sort_desc[21]=false;
} else {
	// alle andere
	$cms->list_field(21,"website","Site");
	$cms->list_field(21,"invuldatum","Ingevuld",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_sort[21]=array("invuldatum");
	$cms->list_sort_desc[21]=true;
}
$cms->list_field(21,"type_id","Accommodatie");
$cms->list_field(21,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJ"));
$cms->list_field(21,"boekingsgegevens","Hoofdboeker");
$cms->list_field(21,"acc_of_arrangement","Ac/Ar/Co");
if($_GET["archief"]==1) {
	$cms->list_field(21,"enquete","Enquête");
}



# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(21,1,"type_id","Accommodatie","",array("selection"=>$vars["alle_types"]));
$cms->edit_field(21,1,"aankomstdatum","Aankomstdatum","",array("selection"=>$vars["aankomstdatum_boeking"]));

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(21);
if($cms_form[21]->filled) {

}

# Controle op delete-opdracht
if($_GET["delete"]==21 and $_GET["21k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(21)) {
	$db->query("DELETE FROM boeking_optie WHERE boeking_id='".addslashes($_GET["21k0"])."';");
	$db->query("DELETE FROM boeking_persoon WHERE boeking_id='".addslashes($_GET["21k0"])."';");
	$db->query("DELETE FROM extra_optie WHERE boeking_id='".addslashes($_GET["21k0"])."';");
	$db->query("DELETE FROM boeking_tarief WHERE boeking_id='".addslashes($_GET["21k0"])."';");
	mail("chaletmailbackup+systemlog@gmail.com","Chalet-boeking gewist",$login->username." heeft via de delete-knop boeking ".$_GET["21k0"]." gewist.");
}

# End declaration
$cms->end_declaration();

// use boekingsnummer as title
if($_GET["21k0"] and $_GET["show"]==21) {
	$db->query("SELECT boekingsnummer FROM boeking WHERE boeking_id='".intval($_GET["21k0"])."';");
	if($db->next_record()) {
		$cms->page_title = "Boeking ".$db->f("boekingsnummer");
	}
}

$layout->display_all($cms->page_title);

?>