<?php

#
# Tarieven-module flexibele tarieven
#

if(!$skipastarieven_verwerken) {
	$mustlogin=true;
	
	include("admin/vars.php");
}

unset($wederverkoop_opgeslagen,$oudtarief,$oudtarief_bekend);

if($_POST["filled"]) {

	# Datumtijd vaststellen
	$datetime=time();
	
	# In welke var staat de brutoprijs?
	if($_POST["toonper"]==1) {
		$naam_var_bruto="bruto";
	} else {
		$naam_var_bruto="c_bruto";
	}
	
	# Gegevens opslaan in database
	reset($vars["tarief_flex_velden"]);
	while(list($key,$value)=each($vars["tarief_flex_velden"])) {
		@reset($_POST[$value]);
		if($vars["tarief_flex_velden_omzetten"][$value]) {
			$check_key=$vars["tarief_flex_velden_omzetten"][$value];
		} else {
			$check_key=$value;
		}
		while(list($key2,$value2)=@each($_POST[$check_key])) {
			if($check_key=="aflopen_allotment") {
				if($value2<>"") {
					$savequery[$key2].=", ".$value."='".addslashes($value2)."'";
				} else {
					$savequery[$key2].=", ".$value."=NULL";
				}
			} else {
				$savequery[$key2].=", ".$value."='".addslashes($value2)."'";
			}
			# Gegevens opslaan in tarief_archief
#			if($naam_var_bruto==$value) {
#				if($_POST["huidigbruto"][$key2]<>$_POST[$naam_var_bruto][$key2]) {
#					$db->query("INSERT INTO tarief_archief SET type_id='".addslashes($_GET["tid"])."', seizoen_id='".addslashes($_GET["sid"])."', week='".addslashes($key2)."', datumtijd=FROM_UNIXTIME('".$datetime."'), bruto='".addslashes($_POST[$naam_var_bruto][$key2])."', user_id='".addslashes($login->user_id)."', autoxml='".($_POST["voorheen"][$key2] ? "1" : "0")."';");
#				}
#			}
		}
	}

	# Eerst gegevens wissen
	$db->query("DELETE FROM tarief_flex WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	
	# Dan opslaan
	while(list($key,$value)=@each($savequery)) {
		$db->query("INSERT INTO tarief_flex SET type_id='".addslashes($_GET["tid"])."', seizoen_id='".addslashes($_GET["sid"])."', dag='".$key."', opgeslagen=NOW()".$value.";");
#		echo $db->lastquery."<br>";
	}
#exit;

/*
	# Gegevens in type_seizoen opslaan (o.a. log)
	$db->query("SELECT type_id, log FROM type_seizoen WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	$log=$login->vars["voornaam"]." (".$login->user_id.") - ".date("d/m/Y, H:i")."u.";
	if($_GET["xmlgoedkeuren"]) $log.=" - handmatig XML goedgekeurd";
	if($_GET["check"] and $_GET["confirmed"]) {
		$log.=" - automatisch XML goedgekeurd";
	}
	if($db->next_record()) {
		$log=$log."\n".$db->f("log");
		$db->query("UPDATE type_seizoen SET optie='".addslashes($_POST["optie"])."', melding_geen_tarieven_verbergen='".($_POST["melding_geen_tarieven_tonen"] ? "0" : "1")."', tarievenopgeslagen=NOW(), log='".addslashes($log)."' WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	} else {
		$db->query("INSERT INTO type_seizoen SET type_id='".addslashes($_GET["tid"])."', seizoen_id='".addslashes($_GET["sid"])."', optie='".addslashes($_POST["optie"])."', melding_geen_tarieven_verbergen='".($_POST["melding_geen_tarieven_tonen"] ? "0" : "1")."', tarievenopgeslagen=NOW(), log='".addslashes($log)."'".$vroegboekdatum_query.";");
	}

*/

/*	
	# Beschikbaarheid-archief opslaan
	while(list($key,$value)=@each($_POST["beschikbaar_voorheen"])) {
		unset($wijzig_beschikbaar,$wijzig_garantie,$wijzig_allotment,$wijzig_vervallen_allotment,$wijzig_optie_leverancier,$wijzig_xml,$wijzig_request,$wijzig_optie_klant);
		if($_POST["beschikbaar"][$key]) {
			$temp_beschikbaar="1";
		} else {
			$temp_beschikbaar="0";
		}
		if($value<>$temp_beschikbaar) {
			if($temp_beschikbaar=="1") {
				# 1 = gewijzigd naar "beschikbaar"
				$wijzig_beschikbaar=1;
			} else {
				# 2 = gewijzigd naar "niet beschikbaar"
				$wijzig_beschikbaar=2;
			}
		}
		if(intval($_POST["voorraad_garantie"][$key])<>intval($_POST["gar"][$key])) $wijzig_garantie=intval($_POST["voorraad_garantie"][$key])-intval($_POST["gar"][$key]);
		if(intval($_POST["voorraad_allotment"][$key])<>intval($_POST["voorraad_allotment_voorheen"][$key])) $wijzig_allotment=intval($_POST["voorraad_allotment"][$key])-intval($_POST["voorraad_allotment_voorheen"][$key]);
		if(intval($_POST["voorraad_vervallen_allotment"][$key])<>intval($_POST["voorraad_vervallen_allotment_voorheen"][$key])) $wijzig_vervallen_allotment=intval($_POST["voorraad_vervallen_allotment"][$key])-intval($_POST["voorraad_vervallen_allotment_voorheen"][$key]);
		if(intval($_POST["voorraad_optie_leverancier"][$key])<>intval($_POST["voorraad_optie_leverancier_voorheen"][$key])) $wijzig_optie_leverancier=intval($_POST["voorraad_optie_leverancier"][$key])-intval($_POST["voorraad_optie_leverancier_voorheen"][$key]);
		if(intval($_POST["voorraad_request"][$key])<>intval($_POST["voorraad_request_voorheen"][$key])) $wijzig_request=intval($_POST["voorraad_request"][$key])-intval($_POST["voorraad_request_voorheen"][$key]);
		if(intval($_POST["voorraad_optie_klant"][$key])<>intval($_POST["voorraad_optie_klant_voorheen"][$key])) $wijzig_optie_klant=intval($_POST["voorraad_optie_klant"][$key])-intval($_POST["voorraad_optie_klant_voorheen"][$key]);
		
		if($wijzig_beschikbaar or $wijzig_garantie or $wijzig_allotment or $wijzig_vervallen_allotment or $wijzig_optie_leverancier or $wijzig_xml or $wijzig_request or $wijzig_optie_klant) {
			$bovenste5=intval($_POST["voorraad_garantie"][$key])+intval($_POST["voorraad_allotment"][$key])+intval($_POST["voorraad_vervallen_allotment"][$key])+intval($_POST["voorraad_optie_leverancier"][$key])+intval($_POST["voorraad_xml"][$key])+intval($_POST["voorraad_request"][$key]);

#			$db->query("INSERT INTO beschikbaar_archief SET type_id='".addslashes($_GET["tid"])."', seizoen_id='".addslashes($_GET["sid"])."', week='".addslashes($key)."', datumtijd=FROM_UNIXTIME('".$datetime."'), beschikbaar='".$temp_beschikbaar."', user_id='".addslashes($login->user_id)."', via='".($_GET["autosave"] ? "2" : "1")."', request_uri='".addslashes($_SERVER["REQUEST_URI"])."';");
			$db->query("INSERT INTO beschikbaar_archief SET type_id='".addslashes($_GET["tid"])."', seizoen_id='".addslashes($_GET["sid"])."', week='".addslashes($key)."', datumtijd=FROM_UNIXTIME('".$datetime."'), beschikbaar='".addslashes($wijzig_beschikbaar)."', garantie='".addslashes($wijzig_garantie)."', allotment='".addslashes($wijzig_allotment)."', vervallen_allotment='".addslashes($wijzig_vervallen_allotment)."', optie_leverancier='".addslashes($wijzig_optie_leverancier)."', xml='".addslashes($wijzig_xml)."', request='".addslashes($wijzig_request)."', optie_klant='".addslashes($wijzig_optie_klant)."', totaal='".addslashes($bovenste5)."', user_id='".addslashes($login->user_id)."', via='".($_GET["autosave"] ? "2" : "1")."', request_uri='".addslashes($_SERVER["REQUEST_URI"])."', opmerkingen='".addslashes($_POST["opmerkingen_voorraad"])."';");
		}
	}
*/

	# taantekeningen opslaan
	$db->query("UPDATE type SET aantekeningen='".addslashes($_POST["taantekeningen"])."' WHERE type_id='".addslashes($_GET["tid"])."';");


	# XML-import tarieven wissen
	if(is_array($_POST["voorheen"])) {
		while(list($key,$value)=each($_POST["voorheen"])) {
			$db->query("DELETE FROM xml_tarievenimport_flex WHERE dag='".addslashes($key)."' AND type_id='".addslashes($_GET["tid"])."';");
		}
	}

/*
	# Nieuwe garanties aanmaken
	while(list($key,$value)=@each($_POST["gar"])) {
		$aantal_nieuwe_garanties=intval($_POST["voorraad_garantie"][$key])-$value;
		if($aantal_nieuwe_garanties>0) {
			for($i=1;$i<=$aantal_nieuwe_garanties;$i++) {
				$accinfo=accinfo($_GET["tid"],$key);
				if($_POST["toonper"]==1) {
					$bruto=$_POST["bruto"][$key];
					$netto=$_POST["netto"][$key];
					$korting_percentage=$_POST["korting_percentage"][$key];
					$korting_euro=$_POST["korting_euro"][$key];
				} else {
					$bruto=$_POST["c_bruto"][$key];
					$netto=$_POST["c_netto"][$key];
					$korting_percentage=$_POST["c_korting_percentage"][$key];
					$korting_euro=$_POST["c_korting_euro"][$key];
				}

				$leverancierid=$_POST["leverancierid"];
				$aankomstdatum_exact=$_POST["aankomstdatum_exact"][$key];
				
				$db->query("INSERT INTO garantie SET type_id='".addslashes($_GET["tid"])."', aankomstdatum='".addslashes($key)."', aankomstdatum_exact='".addslashes($aankomstdatum_exact)."', vertrekdatum_exact='".addslashes($accinfo["vertrekdatum"])."', naam='Stock Chalet.nl', bruto='".addslashes($bruto)."', netto='".addslashes($netto)."', korting_percentage='".addslashes($korting_percentage)."', korting_euro='".addslashes($korting_euro)."', leverancier_id='".addslashes($leverancierid)."';");
			}
		}
	}

*/

	# Verzameltype berekenen
#	verzameltype_berekenen($_GET["sid"],$_GET["tid"]);
	
	if($vars["bezoeker_is_jeroen"]) {
#		exit;
	}

	if($_GET["from"]) {
		header("Location: ".$_GET["from"]);
	} else {
		header("Location: cms_types.php?show=2&2k0=".$_GET["tid"]);
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
	
	# Accommodatiegegevens laden
	$db->query("SELECT a.accommodatie_id, a.wzt, a.naam AS anaam, a.toonper, a.flexibel, t.leverancier_id, a.skipas_id, a.aankomst_plusmin, a.vertrek_plusmin, t.websites, t.naam AS tnaam, t.leverancierscode, t.onderverdeeld_in_nummers, t.code, p.naam AS plaats, t.optimaalaantalpersonen, t.maxaantalpersonen, t.aangepaste_min_tonen, l.begincode, lev.naam AS leverancier, lev.opmerkingen_intern, lev.aflopen_allotment, a.aantekeningen, t.aantekeningen AS taantekeningen, t.verzameltype FROM accommodatie a, type t, plaats p, land l, leverancier lev WHERE t.leverancier_id=lev.leverancier_id AND l.land_id=p.land_id AND a.plaats_id=p.plaats_id AND a.accommodatie_id=t.accommodatie_id AND t.type_id='".addslashes($_GET["tid"])."';");
	if($db->next_record()) {
		$acc["accommodatie_id"]=$db->f("accommodatie_id");
		$acc["wzt"]=$db->f("wzt");
		$acc["naam"]=$db->f("anaam").($db->f("tnaam") ? " ".$db->f("tnaam") : "");
		$acc["plaats"]=$db->f("plaats");
		$acc["min"]=$db->f("optimaalaantalpersonen");
		$acc["max"]=$db->f("maxaantalpersonen");
		$acc["toonper"]=$db->f("toonper");
		$acc["flexibel"]=$db->f("flexibel");
		$acc["flexibel_afronden_op_hele_euros"]=true;
		$acc["code"]=$db->f("code");
		$acc["onderverdeeld_in_nummers"]=$db->f("onderverdeeld_in_nummers");
		$acc["land_begincode"]=$db->f("begincode");
		if($db->f("optimaalaantalpersonen")>12) {
			if($db->f("aangepaste_min_tonen")) {
				$acc["min_tonen"]=$db->f("aangepaste_min_tonen");
			} else {
				$acc["min_tonen"]=floor($db->f("optimaalaantalpersonen")*.5);
			}
		} else {
			$acc["min_tonen"]=1;
		}
		$acc["leverancier_id"]=$db->f("leverancier_id");
		$acc["leverancier"]=$db->f("leverancier");
		$acc["skipas_id"]=$db->f("skipas_id");
		$acc["xml_leverancierscode"]=$db->f("leverancierscode");
		$acc["opmerkingen_intern"]=$db->f("opmerkingen_intern");
		$acc["aantekeningen"]=$db->f("aantekeningen");
		$acc["taantekeningen"]=$db->f("taantekeningen");
		$acc["aankomst_plusmin"]=$db->f("aankomst_plusmin");
		$acc["vertrek_plusmin"]=$db->f("vertrek_plusmin");
		$acc["aflopen_allotment"]=$db->f("aflopen_allotment");
		$acc["verzameltype"]=$db->f("verzameltype");
		$acc["websites"]=$db->f("websites");
		$acc["wederverkoop"]=true;
	}

	# Skipas-tarieven
#	$db->query("SELECT week, bruto, netto_ink, korting, verkoopkorting, prijs, omzetbonus, netto FROM skipas_tarief WHERE skipas_id='".addslashes($acc["skipas_id"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
#	while($db->next_record()) {
#		reset($vars["sjabloon_skipas"]);
#		while(list($key,$value)=each($vars["sjabloon_skipas"])) {
#			if($db->f($value)>0) $skipas["weken"][$db->f("week")][$value]=$db->f($value);
#		}
#	}

	if(!$skipastarieven_verwerken) {	
		# Eerdere boekingen
#		$db->query("SELECT aankomstdatum, aankomstdatum_exact, vertrekdatum_exact FROM boeking WHERE goedgekeurd=1 AND geannuleerd=0 AND (type_id='".addslashes($_GET["tid"])."' OR verzameltype_gekozentype_id='".addslashes($_GET["tid"])."') AND seizoen_id='".addslashes($_GET["sid"])."';");
#		while($db->next_record()) {
#			$aantal_geboekt[$db->f("aankomstdatum")]++;
#			
#			# Kijken of de boeking langer dan 1 week is (en dan ook de volgende week/weken ophogen met 1)
#			$aantalwekengeboekt=round(($db->f("vertrekdatum_exact")-$db->f("aankomstdatum_exact"))/86400)/7;
#			if($aantalwekengeboekt>1) {
#				for($i=2;$i<=$aantalwekengeboekt;$i++) {
#					$aantalplusdagen=($i-1)*7;
#					$aantal_geboekt[mktime(0,0,0,date("m",$db->f("aankomstdatum")),date("d",$db->f("aankomstdatum"))+$aantalplusdagen,date("Y",$db->f("aankomstdatum")))]++;
#				}
#			}
#		}
	
		# XML-tarieven
		$db->query("SELECT dag, bruto FROM xml_tarievenimport_flex WHERE type_id='".addslashes($_GET["tid"])."';");
		while($db->next_record()) {
			$xml_tarieven[$db->f("dag")]=$db->f("bruto");
		}

#echo wt_dump($xml_tarieven);
#exit;		
		# Voornamen Chalet-medewerkers (voor tonen archief)
		$db->query("SELECT user_id, voornaam FROM user;");
		while($db->next_record()) {
			$werknemers[$db->f("user_id")]=$db->f("voornaam");
		}
	
		# Archief-tarieven
#		$db->query("SELECT week, UNIX_TIMESTAMP(datumtijd) AS datumtijd, bruto, user_id, autoxml FROM tarief_archief WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."' ORDER BY datumtijd DESC;");
#		while($db->next_record()) {
#			$archief[$db->f("week")].=date("d-m-Y",$db->f("datumtijd")).": ".$werknemers[$db->f("user_id")]." - � ".number_format($db->f("bruto"),2,",","").($db->f("autoxml") ? " (na XML-import)" : "")."\n";
#			if(!$archief_laatstewijziging[$db->f("week")]) {
#				$archief_laatstewijziging[$db->f("week")]=$db->f("datumtijd");
#			}
#		}

/*
		# Archief-beschikbaarheid
		$db->query("SELECT week, UNIX_TIMESTAMP(datumtijd) AS datumtijd, beschikbaar, garantie, allotment, vervallen_allotment, optie_leverancier, xml, request, optie_klant, totaal, user_id, via, opmerkingen, request_uri FROM beschikbaar_archief WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."' ORDER BY datumtijd DESC;");
		while($db->next_record()) {
			$archief_beschikbaarheid[$db->f("week")].=date("d-m-Y",$db->f("datumtijd"))." ";
			
			# via:
			# 0 = onbekend
			# 1 = cms_tarieven
			# 2 = autosave cms_tarieven
			# 3 = XML-import
			# 4 = automatisch
			# 5 = xmlvoorraadreset
			# 6 = afboeken voorraad bij boeking
			# 7 = garantie wissen
			# 8 = automatisch vervallen allotments
			
			if($werknemers[$db->f("user_id")] and $db->f("via")<>2) {
				$archief_beschikbaarheid[$db->f("week")].=$werknemers[$db->f("user_id")];
			} elseif($db->f("via")==3 or $db->f("via")==2) {
				$archief_beschikbaarheid[$db->f("week")].="XML-import";
			} else {
				$archief_beschikbaarheid[$db->f("week")].="Automatisch";
#				trigger_error("onbekende wijziging (via ".$db->f("via").": ".$db->f("request_uri").") beschikbaarheid in archief",E_USER_NOTICE);
			}

			unset($temp_archief_beschikbaarheid);
			if($db->f("garantie")) {
				$temp_archief_beschikbaarheid.=", garantie (".($db->f("garantie")>0 ? "+" : "").$db->f("garantie").") ";
			}
			if($db->f("allotment")) {
				$temp_archief_beschikbaarheid.=", allotment (".($db->f("allotment")>0 ? "+" : "").$db->f("allotment").") ";
			}
			if($db->f("vervallen_allotment")) {
				$temp_archief_beschikbaarheid.=", vervallen allotment (".($db->f("vervallen_allotment")>0 ? "+" : "").$db->f("vervallen_allotment").") ";
			}
			if($db->f("optie_leverancier")) {
				$temp_archief_beschikbaarheid.=", optie leverancier (".($db->f("optie_leverancier")>0 ? "+" : "").$db->f("optie_leverancier").") ";
			}
			if($db->f("xml")) {
				$temp_archief_beschikbaarheid.=", xml (".($db->f("xml")>0 ? "+" : "").$db->f("xml").") ";
			}
			if($db->f("request")) {
				$temp_archief_beschikbaarheid.=", request (".($db->f("request")>0 ? "+" : "").$db->f("request").") ";
			}
			if($db->f("optie_klant")) {
				$temp_archief_beschikbaarheid.=", optie_klant (".($db->f("optie_klant")>0 ? "+" : "").$db->f("optie_klant").") ";
			}

			$archief_beschikbaarheid[$db->f("week")].=": ".substr($temp_archief_beschikbaarheid,2);
			if(date("Ymd",$db->f("datumtijd"))>=20110225) {
				# pas vanaf 25-02-2011 totaal tonen (want daarvoor werd dat nog niet opgeslagen in de database)
				$archief_beschikbaarheid[$db->f("week")].=" - totaal: ".$db->f("totaal");
			}
			
			if($db->f("beschikbaar")==1) {
				$archief_beschikbaarheid[$db->f("week")].=" - beschikbaar";
			} elseif($db->f("beschikbaar")==2) {
				$archief_beschikbaarheid[$db->f("week")].=" - niet beschikbaar";
			}
			if($db->f("via")==5) {
				$archief_beschikbaarheid[$db->f("week")].=" (XML-voorraad met 1 klik op 0 gezet)";
			} elseif($db->f("via")==6) {
				$archief_beschikbaarheid[$db->f("week")].=" (afgeboekte voorraad bij boeking)";
			} elseif($db->f("via")==8) {
				$archief_beschikbaarheid[$db->f("week")].=" (automatisch vervallen allotment)";
			}
			
			if($db->f("opmerkingen")) {
				$archief_beschikbaarheid[$db->f("week")].=" - ".$db->f("opmerkingen");
			}
			
			$archief_beschikbaarheid[$db->f("week")].="\n";
			if(!$archief_beschikbaarheid_laatstewijziging[$db->f("week")]) {
				$archief_beschikbaarheid_laatstewijziging[$db->f("week")]=$db->f("datumtijd");
			}
		}

*/
		# aflopen_allotment uit calculatiesjabloon halen
#		$db->query("SELECT week, aflopen_allotment FROM calculatiesjabloon_week WHERE seizoen_id='".addslashes($_GET["sid"])."' AND leverancier_id='".addslashes($acc["leverancier_id"])."';");
#		while($db->next_record()) {
#			if($db->f("aflopen_allotment")<>"") $aflopen_allotment[$db->f("week")]=$db->f("aflopen_allotment");
#		}
	}

	# Accommodatie tarieven + voorraad
	$db->query("SELECT type_id FROM tarief_flex WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
#echo "bestaand<hr>";	
		# Tarieven uit tabel tarief
		$db->query("SELECT * FROM tarief_flex WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		while($db->next_record()) {
			reset($vars["tarief_flex_velden"]);
			while(list($key,$value)=each($vars["tarief_flex_velden"])) {
				if($value=="aflopen_allotment") {
					if($db->f($value)<>"") $seizoen["weken"][$db->f("dag")][$value]=$db->f($value);
				} elseif($vars["tarief_flex_velden_omzetten"][$value]) {
					if($db->f($value)<>0) $seizoen["weken"][$db->f("dag")][$vars["tarief_flex_velden_omzetten"][$value]]=$db->f($value);
				} else {
					if($db->f($value)<>0) $seizoen["weken"][$db->f("dag")][$value]=$db->f($value);
				}
				
				# Voorraad
				$seizoen["weken"][$db->f("dag")]["voorraad_totaal"]=$db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")+$db->f("voorraad_request")-$db->f("voorraad_optie_klant");
				if($acc["aflopen_allotment"]<>"" or $aflopen_allotment[$db->f("dag")]<>"" or $db->f("aflopen_allotment")<>"") {
					if($db->f("aflopen_allotment")<>"") {
						$temp_aflopen_allotment=$db->f("aflopen_allotment");
					} elseif($aflopen_allotment[$db->f("dag")]<>"") {
						$temp_aflopen_allotment=$aflopen_allotment[$db->f("dag")];
					} else {
						$temp_aflopen_allotment=$acc["aflopen_allotment"];
					}
					$seizoen["weken"][$db->f("dag")]["voorraad_aflopen_allotment"]=mktime(0,0,0,date("m",$db->f("dag")),date("d",$db->f("dag"))-$temp_aflopen_allotment,date("Y",$db->f("dag")));
					$seizoen["weken"][$db->f("dag")]["voorraad_aflopen_allotment_dagen"]=$temp_aflopen_allotment;
				}
				if($db->f("voorraad_bijwerken")) {
					if(($db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")+$db->f("voorraad_request"))>0) {
						$seizoen["weken"][$db->f("dag")]["beschikbaar"]=1;
					} else {
						$seizoen["weken"][$db->f("dag")]["beschikbaar"]=0;
					}
				}
			}
			
			# Overschrijven met XML-tarief?
			
			# Indien geboekt: autoimportxmltarief uitzetten
			if($aantal_geboekt[$db->f("dag")]>0) $seizoen["weken"][$db->f("dag")]["autoimportxmltarief"]=0;

			# Indien optie: autoimportxmltarief uitzetten
			if($seizoen["weken"][$db->f("dag")]["voorraad_optie_klant"]>0) $seizoen["weken"][$db->f("dag")]["autoimportxmltarief"]=0;
			
			if($xml_tarieven[$db->f("dag")]>0) {
				if($acc["toonper"]==1) {
					# Arrangement (A)
					$oudtarief[$db->f("dag")]=$seizoen["weken"][$db->f("dag")]["bruto"];
					$seizoen["weken"][$db->f("dag")]["bruto"]=$xml_tarieven[$db->f("dag")];
					$seizoen["weken"][$db->f("dag")]["xml_tarievenimport"]=true;
				} elseif($acc["toonper"]==3) {
					# Accommodatie (C)
					$oudtarief[$db->f("dag")]=$seizoen["weken"][$db->f("dag")]["c_bruto"];
					$seizoen["weken"][$db->f("dag")]["c_bruto"]=$xml_tarieven[$db->f("dag")];
					$seizoen["weken"][$db->f("dag")]["xml_tarievenimport"]=true;
				}
				$oudtarief_bekend[$db->f("dag")]=true;
			}
		}
		

#echo wt_dump($seizoen["weken"]);
#exit;
		
		# Optie uit tabel type_seizoen
		$db->query("SELECT optie, melding_geen_tarieven_verbergen, log FROM type_seizoen WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		if($db->next_record()) {
			if($db->f("optie")) $seizoen["optie"]=$db->f("optie");
			if($db->f("log")) $tarievenlog=$db->f("log");
			if($db->f("melding_geen_tarieven_verbergen")) $melding_geen_tarieven_verbergen=true;
			
			reset($vars["tarief_datum_velden"]);
			while(list($key,$value)=each($vars["tarief_datum_velden"])) {
				if($db->f($value)) {
					$seizoen[$value]=date("d-m-Y",$db->f($value));
					
					# Indien datum verstreken: vroegboekkorting_percentage wissen
					if($db->f($value)<mktime(0,0,0,date("m"),date("d"),date("Y"))) {
						$doorloop_array=$seizoen["weken"];
						reset($doorloop_array);
						while(list($key2,$value2)=each($doorloop_array)) {
							$seizoen["weken"][$key2][substr($value,0,-6)]="";
						}
					}
				}
			}
		}
		# Tarieven uit tabel tarief_personen
		$db->query("SELECT week, personen, prijs, afwijking FROM tarief_personen WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		while($db->next_record()) {
			if($seizoen["weken"][$db->f("dag")]["bruto"]>0) {
				if($db->f("prijs")>0) $seizoen["weken"][$db->f("dag")]["verkoop_site"][$db->f("personen")]=$db->f("prijs");
				if($db->f("afwijking")<>"0.00") $seizoen["weken"][$db->f("dag")]["verkoop_afwijking"][$db->f("personen")]=$db->f("afwijking");
			}
		}
		
		# Tarieven doorrekenen
		$doorloop_array=$seizoen["weken"];
		while(list($key,$value)=@each($doorloop_array)) {
			$seizoen["weken"][$key]=bereken($acc["toonper"],$seizoen,$key,$acc,$skipas);
		}
		
		# Marge gemiddeld berekenen
		unset($marge_gemiddeld,$marge_gemiddeld_teller);
		@reset($seizoen["weken"]);
		while(list($key,$value)=@each($seizoen["weken"])) {
			if($value["bruto"]) {
				$marge_gemiddeld=$marge_gemiddeld+$value["marge_percentage"][$acc["max"]];
				$marge_gemiddeld_teller++;
			}
		}
		if($marge_gemiddeld_teller) {
			$seizoen["marge_gemiddeld"]=ereg_replace(",",".",sprintf("%01.2f",($marge_gemiddeld/$marge_gemiddeld_teller)));
		}
		
		if(!$skipastarieven_verwerken) {
			# Sjabloon leverancier
			$db->query("SELECT week FROM calculatiesjabloon_week WHERE leverancier_id='".addslashes($acc["leverancier_id"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
			if($db->num_rows()) {
				$vars["calculatiesjabloon_aanwezig"]=true;
			}
		}
	} else {
#echo "nieuw<hr>";	
	
		#
		# Nog geen tarieven bekend
		#	
		$seizoen["leeg"]=true;
		
		# Sjabloon leverancier
		$db->query("SELECT week, korting_percentage, toeslag, korting_euro, vroegboekkorting_percentage, vroegboekkorting_euro, opslag_accommodatie, opslag_skipas, korting_arrangement_bed_percentage, toeslag_arrangement_euro, korting_arrangement_euro, toeslag_bed_euro, korting_bed_euro, vroegboekkorting_arrangement_percentage, vroegboekkorting_arrangement_euro, vroegboekkorting_bed_percentage, vroegboekkorting_bed_euro, opslag, c_korting_percentage, c_toeslag, c_korting_euro, c_vroegboekkorting_percentage, c_vroegboekkorting_euro, c_opslag_accommodatie, wederverkoop_opslag_euro, wederverkoop_opslag_percentage, wederverkoop_commissie_agent FROM calculatiesjabloon_week WHERE leverancier_id='".addslashes($acc["leverancier_id"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		if($db->num_rows()) {
			$vars["calculatiesjabloon_aanwezig"]=true;
			while($db->next_record()) {
				reset($vars["sjabloon_velden"]);
				while(list($key,$value)=each($vars["sjabloon_velden"])) {
					if($db->f($value)>0) {
						for($i=0;$i<=6;$i++) {
							$dag=mktime(0,0,0,date("m",$db->f("week")),date("d",$db->f("week"))+$i,date("Y",$db->f("week")));
							$seizoen["weken"][$dag][$value]=$db->f($value);
						}
					}
				}
			}
		}
#echo wt_dump($seizoen["weken"]);
		
		# Vroegboekkorting-datums uit tabel calculatiesjabloon
		reset($vars["tarief_datum_velden"]);
		unset($datum_velden);
		while(list($key,$value)=each($vars["tarief_datum_velden"])) {
			$datum_velden.=", UNIX_TIMESTAMP(".$value.") AS ".$value;
		}
		$db->query("SELECT ".substr($datum_velden,2)." FROM calculatiesjabloon WHERE leverancier_id='".addslashes($acc["leverancier_id"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		if($db->next_record()) {
			reset($vars["tarief_datum_velden"]);
			while(list($key,$value)=each($vars["tarief_datum_velden"])) {
				if($db->f($value)) {
					$seizoen[$value]=date("d-m-Y",$db->f($value));
					
					# Indien datum verstreken: vroegboekkorting_percentage wissen
					if($db->f($value)<mktime(0,0,0,date("m"),date("d"),date("Y"))) {
						$doorloop_array=$seizoen["weken"];
						reset($doorloop_array);
						while(list($key2,$value2)=each($doorloop_array)) {
							$seizoen["weken"][$key2][substr($value,0,-6)]="";
						}
					}
				}
			}
		}
		
		# XML-importtarieven bekend?
		if(is_array($xml_tarieven)) {
			$dag=$seizoen["begin"];
			while($dag<=$seizoen["eind"]) {
				if($xml_tarieven[$dag]>0) {
					if($acc["toonper"]==1) {
						# Arrangement (A)
						$seizoen["weken"][$dag]["bruto"]=$xml_tarieven[$dag];
						$xmlimport[$dag]=true;
					} elseif($acc["toonper"]==3) {
						# Accommodatie (C)
						$seizoen["weken"][$dag]["c_bruto"]=$xml_tarieven[$dag];
						$xmlimport[$dag]=true;
					}
				}
	
				if($xmlimport[$dag]) {
					# Tarieven doorrekenen
					$seizoen["weken"][$dag]=bereken($acc["toonper"],$seizoen,$dag,$acc,$skipas);
				}
				
				$dag=mktime(0,0,0,date("m",$dag),date("d",$dag)+1,date("Y",$dag));
			}
			if($xmlimport) {
				if($tarieventabel_opmerkingen) {
					$tarieventabel_opmerkingen.="<br>";
				}
				$tarieventabel_opmerkingen.="Let op! Dit zijn nieuwe tarieven via een XML-import.";
			}
		}

/*
		# Afloopdatum allotment		
		$week=$seizoen["begin"];
		while($week<=$seizoen["eind"]) {
			if($aflopen_allotment[$week]) {
			
				if($aflopen_allotment[$week]) {
					$temp_aflopen_allotment=$aflopen_allotment[$week];
				} else {
					$temp_aflopen_allotment=$acc["aflopen_allotment"];
				}
				$seizoen["weken"][$week]["voorraad_aflopen_allotment"]=mktime(0,0,0,date("m",$week),date("d",$week)-$temp_aflopen_allotment,date("Y",$week));
				$seizoen["weken"][$week]["voorraad_aflopen_allotment_dagen"]=$temp_aflopen_allotment;
			}
			$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}
*/

	}
	
	# weektarieven uit database halen	
	$db->query("SELECT week, beschikbaar, blokkeren_wederverkoop, bruto, korting_percentage, toeslag, korting_euro, vroegboekkorting_percentage, vroegboekkorting_euro, opslag_accommodatie, opslag_skipas, afwijking_alle, arrangementsprijs, onbezet_bed, toeslag_arrangement_euro, korting_arrangement_euro, toeslag_bed_euro, korting_bed_euro, vroegboekkorting_arrangement_percentage, vroegboekkorting_arrangement_euro, vroegboekkorting_bed_percentage, vroegboekkorting_bed_euro, opslag, c_bruto, c_korting_percentage, c_toeslag, c_korting_euro, c_vroegboekkorting_percentage, c_vroegboekkorting_euro, c_opslag_accommodatie, c_verkoop_afwijking, c_verkoop_site, voorraad_garantie, voorraad_allotment, voorraad_vervallen_allotment, voorraad_optie_leverancier, voorraad_xml, voorraad_request, voorraad_optie_klant, voorraad_bijwerken, wederverkoop_opslag_euro, wederverkoop_opslag_percentage, wederverkoop_commissie_agent, aanbiedingskleur, autoimportxmltarief, blokkeerxml, aflopen_allotment, aanbieding_acc_percentage, aanbieding_acc_euro, toonexactekorting, aanbieding_skipas_percentage, aanbieding_skipas_euro, inkoopkorting_percentage, inkoopkorting_euro FROM tarief WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	while($db->next_record()) {
		$seizoen["weken"][$db->f("week")]["bruto"]=$db->f("c_bruto");
	}
	
	include("content/cms_tarieven_flex.html");
}

?>