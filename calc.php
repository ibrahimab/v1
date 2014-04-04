<?php

#
# Totaalbedrag van een vakantie berekenen zonder boeken
#

session_start();

$robot_nofollow=true;
$robot_noindex=true;

include("admin/vars.php");

if($_GET["calcid"] and $vars["wederverkoop"] and $login_rb->logged_in) {
	$_GET["calcid"]=intval($_GET["calcid"]);
	if($login_rb->vars["inzicht_prijsberekeningen"]) {
		$reisbureau_user_id_inquery="SELECT user_id FROM reisbureau_user WHERE reisbureau_id=(SELECT reisbureau_id FROM reisbureau_user WHERE user_id='".addslashes($login_rb->user_id)."')";
	} else {
		$reisbureau_user_id_inquery=$login_rb->user_id;
	}
	$db->query("SELECT boeking_id, type_id FROM boeking WHERE boeking_id='".addslashes($_GET["calcid"])."' AND reisbureau_user_id IN (".$reisbureau_user_id_inquery.");");
	if($db->next_record()) {
		$_GET["tid"]=$db->f("type_id");
		$_SESSION["calc"][$_GET["tid"]]["boekingid"]=$db->f("boeking_id");
		$_GET["stap"]=3;
	}
}
if(!$_GET["tid"]) {
	header("Location: ".$path);
	exit;
}

if(!$_SESSION["calc"][$_GET["tid"]] and $_GET["stap"]>1) {
	$_GET["stap"]=1;
}

if(!$_GET["stap"]) {
	$_GET["stap"]=1;
	unset($_SESSION["calc"][$_GET["tid"]]);
}

if($_POST["input"]["aankomstdatum"]) {
	$accinfo=accinfo($_GET["tid"],$_POST["input"]["aankomstdatum"],$_POST["input"]["aantalpersonen"]);
} elseif($_POST["input"]["aankomstdatum_flex"]["day"] and $_POST["input"]["aankomstdatum_flex"]["month"] and $_POST["input"]["aankomstdatum_flex"]["year"]) {
	$tempdatum=mktime(0,0,0,$_POST["input"]["aankomstdatum_flex"]["month"],$_POST["input"]["aankomstdatum_flex"]["day"],$_POST["input"]["aankomstdatum_flex"]["year"]);
	$accinfo=accinfo($_GET["tid"],dichtstbijzijnde_zaterdag($tempdatum),$_POST["input"]["aantalpersonen"]);
	unset($tempdatum);
} else {
	$accinfo=accinfo($_GET["tid"]);
}

#	echo wt_dump($accinfo);
#	exit;


if($_SESSION["calc"][$_GET["tid"]]["boekingid"]) {
	$gegevens=get_boekinginfo($_SESSION["calc"][$_GET["tid"]]["boekingid"]);
}

# flexibel
if($gegevens["stap1"]["flexibel"]) {
	$vars["reisverzekering_mogelijk"]=false;
}

if($vars["wederverkoop"] and $login_rb->logged_in) {

	if($_GET["bewaren"]==1) {
		if($_SESSION["calc"][$_GET["tid"]]["boekingid"]) {
			$db->query("UPDATE boeking SET calc_bewaren=1 WHERE boeking_id='".addslashes($_SESSION["calc"][$_GET["tid"]]["boekingid"])."';");
		}
		header("Location: ".ereg_replace("bewaren=1","bewaren=0",$_SERVER["REQUEST_URI"]));
		exit;
	}

	if($_GET["wissen"]==1) {
		if($_SESSION["calc"][$_GET["tid"]]["boekingid"]) {
			$db->query("UPDATE boeking SET calc_bewaren=0 WHERE boeking_id='".addslashes($_SESSION["calc"][$_GET["tid"]]["boekingid"])."';");
		}
		header("Location: ".ereg_replace("wissen=1","wissen=0",$_SERVER["REQUEST_URI"]));
		exit;
	}
}

if($_GET["stap"]==1) {

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm");
	$form->settings["fullname"]="calc";
	$form->settings["layout"]["css"]=false;
	$form->settings["layout"]["goto_aname"]="kop";
	$form->settings["language"]=$vars["taal"];
	$form->settings["goto"]=ereg_replace("stap=[0-9]","stap=2",$_SERVER["REQUEST_URI"]);
	$form->settings["message"]["submitbutton"][$vars["taal"]]=strtoupper(txt("volgende","calc"));

	unset($temp_min_aantalpersonen,$temp_max_aantalpersonen);
	if($accinfo["toonper"]<>3 and !$vars["wederverkoop"]) {
		# Minimum aantal te boeken personen bepalen (op basis van opgeslagen tarieven in tabel tarief_persoon)
		unset($inquery_aankomstdata);
		@reset($accinfo["aankomstdatum_beschikbaar"]);
		while(list($key,$value)=@each($accinfo["aankomstdatum_beschikbaar"])) {
			if($key>time()) {
				if($inquery_aankomstdata) $inquery_aankomstdata.=",".$key; else $inquery_aankomstdata=$key;
			}
		}
		if($inquery_aankomstdata) {
			$db->query("SELECT DISTINCT personen FROM tarief_personen WHERE type_id='".addslashes($accinfo["typeid"])."' AND week IN (".$inquery_aankomstdata.") ORDER BY personen;");
			while($db->next_record()) {
				if(!$temp_min_aantalpersonen) $temp_min_aantalpersonen=$db->f("personen");
				$temp_max_aantalpersonen=$db->f("personen");
			}
			if($temp_max_aantalpersonen>$accinfo["maxaantalpersonen"]) $temp_max_aantalpersonen=$accinfo["maxaantalpersonen"];
			for($i=$temp_min_aantalpersonen;$i<=$temp_max_aantalpersonen;$i++) {
				$temp_aantalpersonen_array[$i]=$i." ".($i==1 ? txt("persoon") : txt("personen"));
			}
		}
	}

	if(!is_array($temp_aantalpersonen_array)) {
		$temp_aantalpersonen_array=$accinfo["aantalpersonen_array"];
	}
	$form->field_noedit("accnaam",txt("accommodatie","boeken"),"",array("text"=>$accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"]));
	$form->field_noedit("accplaats",txt("plaats","boeken"),"",array("text"=>$accinfo["plaats"].", ".$accinfo["land"]));
	$form->field_select(1,"aantalpersonen",txt("aantalpersonen","boeken"),"",array("selection"=>($gegevens["stap1"]["aantalpersonen"] ? $gegevens["stap1"]["aantalpersonen"] : $_GET["ap"])),array("selection"=>$temp_aantalpersonen_array));


	# Aankomstdata-array bepalen (geen data uit het verleden en geen data zonder optietarieven)
	$db->query("SELECT DISTINCT t.week, s.optie_soort_id FROM optie_tarief t, optie_onderdeel o, optie_accommodatie a, optie_soort s, type ty WHERE a.optie_soort_id=s.optie_soort_id AND t.optie_onderdeel_id=o.optie_onderdeel_id AND ty.accommodatie_id=a.accommodatie_id AND ty.type_id='".addslashes($_GET["tid"])."' AND a.optie_groep_id=o.optie_groep_id;");
	while($db->next_record()) {
		$datum_optietarieven[$db->f("week")]++;
	}


	@reset($accinfo["aankomstdatum_beschikbaar"]);
	unset($temp_aankomstdata);
	while(list($key,$value)=@each($accinfo["aankomstdatum_beschikbaar"])) {
		if($key>time()) {
			$temp_aankomstdatum_beschikbaar=true;
			if(is_array($datum_optietarieven) and ($datum_optietarieven[$key]>1 or $datum_optietarieven[$key]==max($datum_optietarieven))) {
				$temp_aankomstdata[$key]=$value;
				$temp_optietarieven_beschikbaar=true;
			}
		}
	}

	if($accinfo["wzt"]==2) {
		# flexibel

		# aankomstdatum
		if($_GET["flad"]) {
			$temp_aankomstdatum_exact=$_GET["flad"];
		} elseif($_GET["d"]) {
			$temp_aankomstdatum_exact=$_GET["d"];
		}

		if($accinfo["flexibel"]) {
			$form->field_date(1,"aankomstdatum_flex",txt("aankomstdatum","beschikbaarheid"),"",array("time"=>$temp_aankomstdatum_exact),array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
		} else {
			$form->field_select(1,"aankomstdatum",txt("aankomstdatum","boeken"),"",array("selection"=>($gegevens["stap1"]["aankomstdatum"] ? $gegevens["stap1"]["aankomstdatum"] : $_GET["d"])),array("selection"=>$temp_aankomstdata));
		}

		# verblijfsduur
		unset($vars["verblijfsduur"]);
		$vars["verblijfsduur"]["1"]="1 ".txt("week","vars");
		$vars["verblijfsduur"]["2"]="2 ".txt("weken","vars");
		$vars["verblijfsduur"]["3"]="3 ".txt("weken","vars");
		$vars["verblijfsduur"]["4"]="4 ".txt("weken","vars");
		if($accinfo["flexibel"]) {
			// $vars["verblijfsduur"]["1n"]="1 ".txt("nacht","vars");
			for($i=3;$i<=$vars["flex_max_aantalnachten"];$i++) {
				$vars["verblijfsduur"][$i."n"]=$i." ".txt("nachten","vars");
			}
		}
		if($_GET["fldu"]) {
			$temp_verblijfsduur=$_GET["fldu"];
		} elseif($_GET["fdu"]>0 and !$_GET["fldu"]) {
			$temp_verblijfsduur=$_GET["fdu"];
		} else {
			$temp_verblijfsduur=1;
		}
		$form->field_select(1,"verblijfsduur",txt("verblijfsduur","boeken"),"",array("selection"=>$temp_verblijfsduur),array("selection"=>$vars["verblijfsduur"],"optgroup"=>array("1"=>txt("aantalweken"),"3n"=>txt("aantalnachten"))));
		$temp_aankomstdatum_beschikbaar=true;
		$temp_optietarieven_beschikbaar=true;
	} else {

		$form->field_select(1,"aankomstdatum",txt("aankomstdatum","boeken"),"",array("selection"=>($gegevens["stap1"]["aankomstdatum"] ? $gegevens["stap1"]["aankomstdatum"] : $_GET["d"])),array("selection"=>$temp_aankomstdata));
	}

	$form->check_input();

	if($form->filled) {
		if($accinfo["flexibel"] or $form->input["verblijfsduur"]>1) {
			# flexibel - controle op tarief/beschikbaarheid
			if($accinfo["flexibel"]) {
				$flextarief=bereken_flex_tarief($_GET["tid"],$form->input["aankomstdatum_flex"]["unixtime"],0,flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]));
			} else {
				$flextarief=bereken_flex_tarief($_GET["tid"],$form->input["aankomstdatum"],0,flex_bereken_vertrekdatum($form->input["aankomstdatum"],$form->input["verblijfsduur"]));
			}
			if($flextarief["tarief"]>0) {

			} else {
				if($accinfo["flexibel"]) {
					$form->error("aankomstdatum_flex",txt("gekozenperiodenietbeschikbaar","boeken"));
				} else {
					$form->error("aankomstdatum",txt("gekozenperiodenietbeschikbaar","boeken"));
				}
				$form->error("verblijfsduur",txt("gekozenperiodenietbeschikbaar","boeken"));
			}
		}
	}

	if($form->okay) {
		if($_GET["stap"]==1) {
			if(!$_SESSION["calc"][$_GET["tid"]]["boekingid"]) {
				if($accinfo["flexibel"]) {
					$form->input["aankomstdatum"]=dichtstbijzijnde_zaterdag($form->input["aankomstdatum_flex"]["unixtime"]);
				}

				$db->query("SELECT seizoen_id FROM tarief WHERE type_id='".addslashes($_GET["tid"])."' AND week='".addslashes($form->input["aankomstdatum"])."';");
				if($db->next_record()) {
					$seizoenid=$db->f("seizoen_id");
				}
				unset($extrasetquery);
				if($vars["wederverkoop"] and $login_rb->logged_in) {
					$extrasetquery=", reisbureau_user_id='".addslashes($login_rb->user_id)."'";
				}

				if($accinfo["wzt"]==2) {

					if($accinfo["flexibel"]) {
						$extrasetquery.=", aankomstdatum_exact='".addslashes($form->input["aankomstdatum_flex"]["unixtime"])."', vertrekdatum_exact='".addslashes(flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]))."'";
					} else {
						$extrasetquery.=", aankomstdatum_exact='".addslashes($form->input["aankomstdatum"])."', vertrekdatum_exact='".addslashes(flex_bereken_vertrekdatum($form->input["aankomstdatum"],$form->input["verblijfsduur"]))."'";
					}

					# Kijken of dit een flexibele boeking is
					if($accinfo["flexibel"]) {
						if(flex_is_dit_flexibel($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"])) {
							$extrasetquery.=", flexibel=1, verblijfsduur='".addslashes($form->input["verblijfsduur"])."'";
						}
					} else {
						$extrasetquery.=", flexibel=0, verblijfsduur='".addslashes($form->input["verblijfsduur"])."'";
					}

					if($flextarief["tarief"]>0) {
						$extrasetquery.=", accprijs='".addslashes($flextarief["tarief"])."'";
					}
				} else {
					if($accinfo["accprijs"]) {
						$extrasetquery.=", accprijs='".addslashes($accinfo["accprijs"])."'";
					}
					$extrasetquery.=", aankomstdatum_exact='".addslashes($accinfo["aankomstdatum_unixtime"][$form->input["aankomstdatum"]])."', vertrekdatum_exact='".addslashes($accinfo["vertrekdatum"])."'";
				}
				$db->query("INSERT INTO boeking SET calc=1, type_id='".addslashes($_GET["tid"])."', seizoen_id='".addslashes($seizoenid)."', taal='".$vars["taal"]."', aantalpersonen='".addslashes($form->input["aantalpersonen"])."', reserveringskosten='".addslashes($vars["reserveringskosten"])."', website='".addslashes($vars["website"])."', aankomstdatum='".addslashes($form->input["aankomstdatum"])."', annuleringsverzekering_poliskosten='".addslashes($accinfo["annuleringsverzekering_poliskosten"])."', annuleringsverzekering_percentage_1='".addslashes($accinfo["annuleringsverzekering_percentage_1"])."', annuleringsverzekering_percentage_2='".addslashes($accinfo["annuleringsverzekering_percentage_2"])."', annuleringsverzekering_percentage_3='".addslashes($accinfo["annuleringsverzekering_percentage_3"])."', annuleringsverzekering_percentage_4='".addslashes($accinfo["annuleringsverzekering_percentage_4"])."', schadeverzekering_percentage='".addslashes($accinfo["schadeverzekering_percentage"])."', reisverzekering_poliskosten='".addslashes($accinfo["reisverzekering_poliskosten"])."', verzekeringen_poliskosten='".addslashes($accinfo["verzekeringen_poliskosten"])."', toonper='".addslashes($accinfo["toonper"])."', wederverkoop='".($vars["wederverkoop"] ? "1" : "0")."', naam_accommodatie='".addslashes($accinfo["begincode"].$accinfo["typeid"]." - ".$accinfo["plaats"]." - ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"])."', invuldatum=NOW(), leverancier_id='".addslashes($accinfo["leverancierid"])."', valt_onder_bedrijf='".intval($vars["valt_onder_bedrijf"])."', aanbetaling1_dagennaboeken='".addslashes($temp_aanbetaling1_dagennaboeken)."', totale_reissom_dagenvooraankomst='".addslashes($temp_totale_reissom_dagenvooraankomst)."'".$extrasetquery.";");
				$_SESSION["calc"][$_GET["tid"]]["boekingid"]=$db->insert_id();
			}
		}
	}
	$form->end_declaration();

} elseif($_GET["stap"]==2) {
	#
	# Opties uit database halen
	#
	$db->query("SELECT s.optie_soort_id, s.algemeneoptie, s.naam_enkelvoud".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam_enkelvoud, s.omschrijving".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS omschrijving, s.annuleringsverzekering, s.reisverzekering, s.gekoppeld_id FROM optie_soort s, optie_accommodatie a WHERE ".($gegevens["stap1"]["website_specifiek"]["wederverkoop"] ? "s.beschikbaar_wederverkoop=1 AND " : "s.beschikbaar_directeklanten=1 AND ")."s.naam_enkelvoud".$gegevens["stap1"]["website_specifiek"]["ttv"]."<>'' AND a.accommodatie_id='".addslashes($accinfo["accommodatieid"])."' AND a.optie_soort_id=s.optie_soort_id ORDER BY s.volgorde, s.naam;");
	if($db->num_rows()) {
		unset($optie_soort,$optie_onderdeel,$optie_soort_algemeen);
		while($db->next_record()) {
			if(!$db->f("reisverzekering") or $vars["reisverzekering_mogelijk"]) {
				if($db->f("algemeneoptie")) {
					$optie_soort_algemeen["naam_enkelvoud"][$db->f("optie_soort_id")]=ucfirst($db->f("naam_enkelvoud"));
					$optie_soort_algemeen[$db->f("optie_soort_id")]["annuleringsverzekering"]=$db->f("annuleringsverzekering");
					$optie_soort_algemeen[$db->f("optie_soort_id")]["reisverzekering"]=$db->f("reisverzekering");
				} else {
					$optie_soort["naam_enkelvoud"][$db->f("optie_soort_id")]=ucfirst($db->f("naam_enkelvoud"));
					$optie_soort[$db->f("optie_soort_id")]["annuleringsverzekering"]=$db->f("annuleringsverzekering");
					$optie_soort[$db->f("optie_soort_id")]["reisverzekering"]=$db->f("reisverzekering");
				}

				$db2->query("SELECT o.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam, o.optie_onderdeel_id, o.min_leeftijd, o.max_leeftijd, o.min_deelnemers, o.actief, g.optie_groep_id, g.omschrijving".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS omschrijving, t.verkoop, t.wederverkoop_commissie_agent FROM optie_onderdeel o, optie_groep g, optie_tarief t, optie_soort s, optie_accommodatie a, seizoen sz WHERE o.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]."<>'' AND o.te_selecteren=1 AND o.te_selecteren_door_klant=1 AND o.actief=1 AND sz.seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND a.accommodatie_id='".addslashes($accinfo["accommodatieid"])."' AND a.optie_soort_id=s.optie_soort_id AND a.optie_groep_id=g.optie_groep_id AND t.optie_onderdeel_id=o.optie_onderdeel_id AND t.seizoen_id=sz.seizoen_id AND t.week='".addslashes($gegevens["stap1"]["aankomstdatum"])."' AND t.beschikbaar=1 AND g.optie_soort_id='".$db->f("optie_soort_id")."' AND g.optie_soort_id=s.optie_soort_id AND g.optie_groep_id=o.optie_groep_id ORDER BY o.volgorde, o.naam;");

				while($db2->next_record()) {
					if($db2->f("min_leeftijd") or $db2->f("max_leeftijd")) {
						$optie_soort["leeftijdsgebonden"][$db->f("optie_soort_id")]=true;
					}
					$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["naam"]=$db2->f("naam");

					$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["verkoop"]=$db2->f("verkoop");
					$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["commissie"]=$db2->f("wederverkoop_commissie_agent");
					$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["min_leeftijd"]=$db2->f("min_leeftijd");
					$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["max_leeftijd"]=$db2->f("max_leeftijd");
					$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["min_deelnemers"]=$db2->f("min_deelnemers");
				}
			}
		}
	}
	# Annuleringsverzekering
	if($vars["annverzekering_mogelijk"]) {
		$optie_soort["naam_enkelvoud"]["ann"]=txt("annuleringsverzekering","boeken");

		# Europeesche Standaard: is nog niet beschikbaar
		unset($vars["annverz_soorten"][1]);

		# Europeesche Garantie: is niet meer beschikbaar
		// unset($vars["annverz_soorten"][2]);

		# Europeesche Allrisk: is nog niet beschikbaar
		unset($vars["annverz_soorten"][3]);

		# Europeesche Garantie waarneming: is niet meer beschikbaar
		unset($vars["annverz_soorten"][4]);

		reset($vars["annverz_soorten"]);
		while(list($key,$value)=each($vars["annverz_soorten"])) {
			$optie_onderdeel["ann"][$key]["naam"]=$value.": ".txt("annverz_reissomplusperboeking","boeken",array("v_percentage"=>number_format(($gegevens["stap1"]["annuleringsverzekering_percentage_".$key]>0 ? $gegevens["stap1"]["annuleringsverzekering_percentage_".$key] : $accinfo["annuleringsverzekering_percentage_".$key]),2,',','.'),"v_poliskosten"=>number_format($gegevens["stap1"]["verzekeringen_poliskosten"],2,',','.')));
		}
	}

	# Algemene opties
	if(is_array($optie_soort_algemeen["naam_enkelvoud"])) {
		# Algemene opties selecteren
#		$form->field_htmlrow("","<b>".txt("algemeneopties","boeken")."</b>");
		$koptekst_algemeneopties_getoond=true;
		reset($optie_soort_algemeen["naam_enkelvoud"]);
		while(list($key,$value)=each($optie_soort_algemeen["naam_enkelvoud"])) {
			unset($optie_keuzes);
			@reset($optie_onderdeel[$key]);
			while(list($key2,$value2)=@each($optie_onderdeel[$key])) {
				if($gegevens["stap4"]["algemene_optie"]["verkoop_op_onderdeel_id"][$key2]) {
					$verkoop=$gegevens["stap4"]["algemene_optie"]["verkoop_op_onderdeel_id"][$key2];
				} else {
					$verkoop=$value2["verkoop"];
				}
				$optie_keuzes[$key2]=$value2["naam"].": ".($verkoop<0 ? txt("korting","boeken")." " : "")."€ ".number_format(abs($verkoop),2,',','.');
			}
		}
	}

	# Controle op ingevulde aantallen
	if($_POST["filled"]) {

		if($vars["wederverkoop"] and $login_rb->logged_in) {
			if($_POST["referentie"]) {
				$_SESSION["calc"][$_GET["tid"]]["referentie"]=$_POST["referentie"];
			}
			if($_POST["opmerkingen"]) {
				$_SESSION["calc"][$_GET["tid"]]["opmerkingen"]=$_POST["opmerkingen"];
			}
		}

		reset($_POST);
		while(list($key,$value)=@each($_POST["input"])) {
			while(list($key2,$value2)=each($value)) {
				$totaal[$key]+=intval($value2);
				if($totaal[$key]>$gegevens["stap1"]["aantalpersonen"] and !$teveel[$key]) {
					$teveel[$key]=true;
					$error[]=$optie_soort["naam_enkelvoud"][$key].": ".txt("teveelpersonen","calc",array("v_aantalpersonen"=>$gegevens["stap1"]["aantalpersonen"]));
				}
			}
		}

		# Opslaan
		if(!$error) {
			# Gegevens wissen
			$db->query("DELETE FROM boeking_persoon WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			$db->query("DELETE FROM boeking_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

			# Schadeverzekering opslaan
			$db->query("UPDATE boeking SET schadeverzekering='".addslashes($_POST["schadeverzekering"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

			# Algemene opties opslaan
			@reset($_POST["inputalg"]);
			while(list($key,$value)=@each($_POST["inputalg"])) {
				if(intval($value)>0) {
					$db->query("INSERT INTO boeking_optie SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', optie_onderdeel_id='".$value."', persoonnummer='1', status=2, verkoop='".$optie_onderdeel[$key][$value]["verkoop"]."', commissie='".$optie_onderdeel[$key][$value]["commissie"]."';");
				}
			}

			# Gewone opties opslaan
			@reset($_POST["input"]);
			while(list($key,$value)=@each($_POST["input"])) {
				$persoonnummer=0;
				while(list($key2,$value2)=each($value)) {
					if(intval($value2)>0) {
						for($i=1;$i<=$value2;$i++) {
							$persoonnummer=$persoonnummer+1;
							if($key=="ann") {
								$db->query("INSERT INTO boeking_persoon SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', persoonnummer='".$persoonnummer."', status=2, annverz='".$key2."';");

								# Verzekerd bedrag bepalen en opslaan
								$temp_gegevens2=get_boekinginfo($gegevens["stap1"]["boekingid"]);
								$db->query("UPDATE boeking_persoon SET annverz_verzekerdbedrag='".addslashes($temp_gegevens2["stap4"][$i]["annverz_verzekerdbedrag_actueel"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer='".addslashes($persoonnummer)."';");
							} else {
								$db->query("INSERT INTO boeking_optie SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', optie_onderdeel_id='".$key2."', persoonnummer='".$persoonnummer."', status=2, verkoop='".$optie_onderdeel[$key][$key2]["verkoop"]."', commissie='".$optie_onderdeel[$key][$key2]["commissie"]."';");
							}
						}
					}
				}
			}

			$gegevens=get_boekinginfo($gegevens["stap1"]["boekingid"]);

			# Bijkomende kosten bepalen
			bereken_bijkomendekosten($gegevens["stap1"]["boekingid"]);

			# Doorsturen naar overzichtspagina
			header("Location: ".ereg_replace("stap=[0-9]","stap=3",$_SERVER["REQUEST_URI"]));
			exit;
		}
	}

} elseif($_GET["stap"]==3) {

	unset($overzicht);
	$overzicht.="<table cellspacing=\"0\" cellpadding=\"4\" style=\"width: 700px;background-color: #FFFFFF;border: 1px solid ".$table.";\">";
	$overzicht.="<tr><td>".html("accommodatie","boeken")."</td><td><a href=\"".$accinfo["url"]."\" target=\"_blank\">".htmlentities(ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"])."</a></td></tr>";
	$overzicht.="<tr><td>".html("plaats","boeken")."</td><td>".htmlentities($accinfo["plaats"].", ".$accinfo["land"])."</td></tr>";
	$overzicht.="<tr><td>".html("aantalpersonen","boeken")."</td><td>".htmlentities($gegevens["stap1"]["aantalpersonen"])."</td></tr>";
	if($gegevens["stap1"]["flexibel"] or $gegevens["stap1"]["verblijfsduur"]>1) {
		$overzicht.="<tr><td>".html("verblijfsperiode","boeken")."</td><td>".htmlentities(DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$vars["taal"])." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum_exact"],$vars["taal"]))."</td></tr>";
	} else {
		$overzicht.="<tr><td>".html("verblijfsperiode","boeken")."</td><td>".htmlentities($accinfo["aankomstdatum"][$gegevens["stap1"]["aankomstdatum"]]." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum"],$vars["taal"]))."</td></tr>";
	}
	$overzicht.="<tr><td colspan=\"2\"><hr style=\"color: ".$hr.";background-color:".$hr.";height: 1px;border: 0px;\"><b>".txt("samenstellingreissom","boeken")."</b><p><table cellspacing=\"0\" width=\"100%\">";
	$overzicht.="_WT_REISSOM_TABEL_";

	if($vars["wederverkoop"] and $login_rb->logged_in) {
		$reissomtabel_met_javascript=reissom_tabel($gegevens,$accinfo,array("tonen_verbergen"=>true));
		$reissomtabel_zonder_javascript=reissom_tabel($gegevens,$accinfo);
	} else {
		$reissomtabel_met_javascript=reissom_tabel($gegevens,$accinfo);
		$reissomtabel_zonder_javascript=$reissomtabel_met_javascript;
	}

	$overzicht.="</table></td></tr>";
	$overzicht.="</table>";
	$overzicht.="<p>";
	if($gegevens["stap1"]["flexibel"]) {
		$overzicht.="<a href=\"".$vars["basehref"].txt("menu_boeken").".php?tid=".htmlentities($_GET["tid"])."&flad=".$gegevens["stap1"]["aankomstdatum_exact"]."&fldu=".$gegevens["stap1"]["verblijfsduur"]."&ap=".$gegevens["stap1"]["aantalpersonen"]."\">".html("boekdeze","calc")."&nbsp;&gt;</a><p>";
	} elseif($gegevens["stap1"]["verblijfsduur"]>1) {
		$overzicht.="<a href=\"".$vars["basehref"].txt("menu_boeken").".php?tid=".htmlentities($_GET["tid"])."&d=".$gegevens["stap1"]["aankomstdatum"]."&ap=".$gegevens["stap1"]["aantalpersonen"]."&fldu=".$gegevens["stap1"]["verblijfsduur"]."\">".html("boekdeze","calc")."&nbsp;&gt;</a><p>";
	} else {
		$overzicht.="<a href=\"".$vars["basehref"].txt("menu_boeken").".php?tid=".htmlentities($_GET["tid"])."&d=".$gegevens["stap1"]["aankomstdatum"]."&ap=".$gegevens["stap1"]["aantalpersonen"]."\">".html("boekdeze","calc")."&nbsp;&gt;</a><p>";
	}

	if(($_POST["mail"] and $_POST["filled"] and wt_validmail($_POST["mail"])) or ($vars["wederverkoop"] and $login_rb->logged_in)) {
		if(!$_SESSION["calc"][$_GET["tid"]]["mail_naar_chalet_verstuurd"]) {
			#
			# Mail naar Chalet
			#

			# Mail aan Chalet.nl
			$mail=new wt_mail;
			$mail->from=$vars["email"];
			$mail->fromname=$vars["websitenaam"];
			$mail->to=$_POST["mail"];
			$mail->subject=txt("mail_subject","calc",array("v_website"=>$vars["websitenaam"]));
			$mail->to="info@chalet.nl";
			$mail->fromname="Website ".$vars["websites"][$vars["website"]];

			if($vars["wederverkoop"] and $login_rb->logged_in) {
				$db->query("SELECT r.naam FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id AND ru.user_id='".addslashes($login_rb->user_id)."';");
				if($db->next_record()) {
					$rbnaam="<b>".htmlentities(wt_naam($login_rb->vars["voornaam"],$login_rb->vars["tussenvoegsel"],$login_rb->vars["achternaam"]))."</b> van <b>".htmlentities($db->f("naam"))."</b>";
				} else {
					$rbnaam=htmlentities($_POST["mail"]);
				}
				$mail->html=$rbnaam." heeft via de site de volgende prijsberekening uitgevoerd:<p>";
				if($_SESSION["calc"][$_GET["tid"]]["referentie"] or $_SESSION["calc"][$_GET["tid"]]["opmerkingen"]) {
					$mail->html.="<table cellspacing=\"0\" cellpadding=\"4\" style=\"width: 700px;background-color: #FFFFFF;border: 1px solid ".$table.";\">";
					if($_SESSION["calc"][$_GET["tid"]]["referentie"]) $mail->html.="<tr><td valign=\"top\" style=\"width:165px;\">Referentie</td><td valign=\"top\">".htmlentities($_SESSION["calc"][$_GET["tid"]]["referentie"])."</td></tr>";
					if($_SESSION["calc"][$_GET["tid"]]["opmerkingen"]) $mail->html.="<tr><td valign=\"top\" style=\"width:165px;\" nowrap>Vragen of opmerkingen</td><td valign=\"top\">".nl2br(htmlentities($_SESSION["calc"][$_GET["tid"]]["opmerkingen"]))."</td></tr>";
					$mail->html.="</table><p>";
				}
				$mail->html.=ereg_replace("_WT_REISSOM_TABEL_",$reissomtabel_zonder_javascript,$overzicht);
			} else {
				$mail->html="<a href=\"mailto:".htmlentities($_POST["mail"])."\">".htmlentities($_POST["mail"])."</a> heeft via de site het volgende totaalbedrag laten berekenen:<p>".ereg_replace("_WT_REISSOM_TABEL_",$reissomtabel_zonder_javascript,$overzicht);
			}
			$mail->send();

			$_SESSION["calc"][$_GET["tid"]]["mail_naar_chalet_verstuurd"]=true;
		}
	}

	if($_POST["mail"] and $_POST["filled"]) {
		#
		# Mail naar klant
		#
		if(wt_validmail($_POST["mail"])) {

			$mail=new wt_mail;
			$mail->from=$vars["email"];
			$mail->fromname=$vars["websitenaam"];
			$mail->to=$_POST["mail"];
			$mail->subject=txt("mail_subject","calc",array("v_website"=>$vars["websitenaam"]));
			$mail->html="<br><div style=\"width:700px;\">".nl2br(html("mail_inleiding","calc",array("v_websitenaam"=>$vars["websitenaam"])))."</div><p>".ereg_replace("_WT_REISSOM_TABEL_",$reissomtabel_zonder_javascript,$overzicht);

			$mail->send();

			$melding="<i>".html("verstuurd","calc",array("v_email"=>$_POST["mail"]))."</i>";
		} else {
			$error=html("ongeldig","calc");
		}
	}
	$overzicht=ereg_replace("_WT_REISSOM_TABEL_",$reissomtabel_met_javascript,$overzicht);
}

include "content/opmaak.php";

?>