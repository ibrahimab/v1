<?php



/**
*
*/
class vertrekinfo {

	function __construct() {

	}

	function create($gegevens,$save_pdffile="") {

		# Testboeking: C12105342

		global $vars;
		$db=new DB_sql;
		$db2=new DB_sql;
		$db3=new DB_sql;

		# Kijken of het om een anderstalige boeking gaat
		if($gegevens["stap1"]["website_specifiek"]["ttv"]) {
			$ttv=$gegevens["stap1"]["website_specifiek"]["ttv"];
			$taal_streepje=strtoupper(substr($gegevens["stap1"]["website_specifiek"]["ttv"],1))."-";
		}

		# onderliggend verzameltype: de accommodatie-gegevens die boven dat gekozen type liggen gebruiken
		if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
			# onderliggend verzameltype
			$db->query("SELECT accommodatie_id FROM type WHERE type_id='".addslashes($gegevens["stap1"]["verzameltype_gekozentype_id"])."';");
			if($db->next_record()) {
				$gegevens["stap1"]["accinfo"]["accommodatie_id"]=$db->f("accommodatie_id");
			}
		}

		# Gegevens per niveau ophalen

		# Niveau: Leverancier
		$query[1]="SELECT leverancier_id, vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, vertrekinfo_incheck_sjabloon_id, vertrekinfo_soortbeheer, vertrekinfo_soortbeheer_aanvulling".$ttv." AS vertrekinfo_soortbeheer_aanvulling, vertrekinfo_telefoonnummer, vertrekinfo_inchecktijd, vertrekinfo_uiterlijkeinchecktijd, vertrekinfo_uitchecktijd, vertrekinfo_inclusief".$ttv." AS vertrekinfo_inclusief, vertrekinfo_exclusief".$ttv." AS vertrekinfo_exclusief, vertrekinfo_route".$ttv." AS vertrekinfo_route, vertrekinfo_soortadres, vertrekinfo_adres, vertrekinfo_plaatsnaam_beheer, vertrekinfo_gps_lat, vertrekinfo_gps_long FROM leverancier WHERE leverancier_id='".addslashes($gegevens["stap1"]["leverancierid"])."';";

		# Niveau: Accommodatie
		$query[2]="SELECT accommodatie_id, vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, vertrekinfo_incheck_sjabloon_id, vertrekinfo_soortbeheer, vertrekinfo_soortbeheer_aanvulling".$ttv." AS vertrekinfo_soortbeheer_aanvulling, vertrekinfo_telefoonnummer, vertrekinfo_inchecktijd, vertrekinfo_uiterlijkeinchecktijd, vertrekinfo_uitchecktijd, inclusief".$ttv." AS inclusief, vertrekinfo_inclusief".$ttv." AS vertrekinfo_inclusief, exclusief".$ttv." AS exclusief, vertrekinfo_exclusief".$ttv." AS vertrekinfo_exclusief, vertrekinfo_route".$ttv." AS vertrekinfo_route, vertrekinfo_soortadres, vertrekinfo_adres, vertrekinfo_plaatsnaam_beheer, gps_lat, vertrekinfo_gps_lat, gps_long, vertrekinfo_gps_long FROM accommodatie WHERE accommodatie_id='".addslashes($gegevens["stap1"]["accinfo"]["accommodatie_id"])."';";

		# Niveau: Type
		$query[3]="SELECT type_id, accommodatie_id, vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, vertrekinfo_incheck_sjabloon_id, vertrekinfo_soortbeheer, vertrekinfo_soortbeheer_aanvulling".$ttv." AS vertrekinfo_soortbeheer_aanvulling, vertrekinfo_telefoonnummer, vertrekinfo_inchecktijd, vertrekinfo_uiterlijkeinchecktijd, vertrekinfo_uitchecktijd, inclusief".$ttv." AS inclusief, vertrekinfo_inclusief".$ttv." AS vertrekinfo_inclusief, exclusief".$ttv." AS exclusief, vertrekinfo_exclusief".$ttv." AS vertrekinfo_exclusief, vertrekinfo_route".$ttv." AS vertrekinfo_route, vertrekinfo_soortadres, vertrekinfo_adres, vertrekinfo_plaatsnaam_beheer, gps_lat, vertrekinfo_gps_lat, gps_long, vertrekinfo_gps_long FROM type WHERE type_id='".addslashes($gegevens["stap1"]["typeid"])."';";

		# Niveau: Gekozen onderliggend type
		if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
			$query[4]="SELECT type_id, accommodatie_id, vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, vertrekinfo_incheck_sjabloon_id, vertrekinfo_soortbeheer, vertrekinfo_soortbeheer_aanvulling".$ttv." AS vertrekinfo_soortbeheer_aanvulling, vertrekinfo_telefoonnummer, vertrekinfo_inchecktijd, vertrekinfo_uiterlijkeinchecktijd, vertrekinfo_uitchecktijd, inclusief".$ttv." AS inclusief, vertrekinfo_inclusief".$ttv." AS vertrekinfo_inclusief, exclusief".$ttv." AS exclusief, vertrekinfo_exclusief".$ttv." AS vertrekinfo_exclusief, vertrekinfo_route".$ttv." AS vertrekinfo_route, vertrekinfo_soortadres, vertrekinfo_adres, vertrekinfo_plaatsnaam_beheer, gps_lat, vertrekinfo_gps_lat, gps_long, vertrekinfo_gps_long FROM type WHERE type_id='".addslashes($gegevens["stap1"]["verzameltype_gekozentype_id"])."';";
		}

		while(list($querykey,$queryvalue)=each($query)) {

			unset($seizoencontrole,$temp_inclusief,$temp_exclusief);
			$db->query($queryvalue);
			if($db->next_record()) {

				if($db->f("vertrekinfo_incheck_sjabloon_id")) {
					$vertrekinfo_incheck_sjabloon_id=$db->f("vertrekinfo_incheck_sjabloon_id");
					$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_soortbeheer")) {
					$variabelen["type_beheer"]=$vars["vertrekinfo_soortbeheer_sjabloontekst"][$db->f("vertrekinfo_soortbeheer")];
					$variabelen["type_beheer_kort"]=$vars["vertrekinfo_soortbeheer"][$db->f("vertrekinfo_soortbeheer")];
					$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_soortbeheer_aanvulling")) {
					$variabelen["beheer_aanvulling"]=trim($db->f("vertrekinfo_soortbeheer_aanvulling"));
					$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_telefoonnummer")) {
					$variabelen["telefoonnummer"]=trim($db->f("vertrekinfo_telefoonnummer"));
					$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_inchecktijd")) {
					$variabelen["inchecktijd"]=trim($db->f("vertrekinfo_inchecktijd"));
					$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_uiterlijkeinchecktijd")) {
					$variabelen["uiterlijke_inchecktijd"]=trim($db->f("vertrekinfo_uiterlijkeinchecktijd"));
					$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_uitchecktijd")) {
					$variabelen["uitchecktijd"]=trim($db->f("vertrekinfo_uitchecktijd"));
					$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_route")) {
					$route_beheer=trim($db->f("vertrekinfo_route"));
					$seizoencontrole=true;
				}

				if($db->f("vertrekinfo_adres")) {
					$vertrekinfo_soortadres=trim($db->f("vertrekinfo_soortadres"));
					$vertrekinfo_adres=trim($db->f("vertrekinfo_adres"));
					$seizoencontrole=true;
				}

				if($db->f("vertrekinfo_plaatsnaam_beheer")) {
					$vertrekinfo_plaatsnaam_beheer=trim($db->f("vertrekinfo_plaatsnaam_beheer"));
					$seizoencontrole=true;
				}

				# GPS-coördinaten
				if($db->f("vertrekinfo_gps_lat")) {
						$gps_lat_beheer=trim($db->f("vertrekinfo_gps_lat"));
						$seizoencontrole=true;
				}
				if($db->f("vertrekinfo_gps_long")) {
						$gps_long_beheer=trim($db->f("vertrekinfo_gps_long"));
						$seizoencontrole=true;
				}
				if($db->f("gps_lat")) {
						$gps_lat=trim($db->f("gps_lat"));
				}
				if($db->f("gps_long")) {
						$gps_long=trim($db->f("gps_long"));
				}

				if($seizoencontrole) {
					if(!preg_match("/\b(".$gegevens["stap1"]["seizoenid"].")\b/",$db->f("vertrekinfo_goedgekeurd_seizoen"))) {

						if($querykey==1) {
							# leverancier goedkeuren
							$error[]="de ".$taal_streepje."leverancier-teksten zijn nog niet <a href=\"".$vars["path"]."cms_leveranciers.php?edit=8&beheerder=0&8k0=".$db->f("leverancier_id")."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
						} elseif($querykey==2) {
							# accommodatie goedkeuren
							$error[]="de ".$taal_streepje."accommodatie-teksten zijn nog niet <a href=\"".$vars["path"]."cms_accommodaties.php?edit=1&archief=0&1k0=".$db->f("accommodatie_id")."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
						} elseif($querykey==3) {
							# type goedkeuren
							$error[]="de ".$taal_streepje."type-teksten zijn nog niet <a href=\"".$vars["path"]."cms_types.php?edit=2&archief=0&1k0=".$db->f("accommodatie_id")."&2k0=".$db->f("type_id")."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
						} elseif($querykey==4) {
							# onderliggend type goedkeuren
							$error[]="de onderliggend gekozen ".$taal_streepje."type-teksten zijn nog niet <a href=\"".$vars["path"]."cms_types.php?edit=2&archief=0&1k0=".$db->f("accommodatie_id")."&2k0=".$db->f("type_id")."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
						}
					}
				}
			}
		}

		# Skipas_id bepalen
		$skipas_id=$gegevens["stap1"]["accinfo"]["skipasid"];

		# Opties
		$db->query("SELECT og.vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, og.optie_groep_id, og.vertrekinfo_optiegroep".$ttv." AS vertrekinfo_optiegroep, og.skipas_id, og.optieleverancier_id, os.optie_soort_id, os.naam, os.optiecategorie FROM optie_groep og, optie_soort os, optie_accommodatie oa WHERE oa.accommodatie_id='".addslashes($gegevens["stap1"]["accinfo"]["accommodatie_id"])."' AND oa.optie_groep_id=og.optie_groep_id AND oa.optie_soort_id=os.optie_soort_id ORDER BY FIND_IN_SET(optiecategorie,'3,4,5,1,2,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20'), length(og.vertrekinfo_optiegroep) DESC;");
		while($db->next_record()) {
			if(!$skipas_id and $db->f("skipas_id")) {
				# Skipas koppelen
				$skipas_id=$db->f("skipas_id");
			}

			if(strlen(trim($db->f("vertrekinfo_optiegroep")))<4) {
				# Materiaalhuur (4) en Skilessen (5): melden indien geen tekst aanwezig is
				if($db->f("optiecategorie")==4 or $db->f("optiecategorie")==5) {
					if(!$nog_niet_ingevoerd_melding_optietekst[$db->f("optiecategorie")] and !$optiecategorie_gehad[$db->f("optiecategorie")]) {
						# Geen tekst ingevoerd
						$error[]="de ".$taal_streepje."optie-tekst '".html("naam_optiecategorie".$db->f("optiecategorie"),"vertrekinfo")."' (".wt_he($db->f("naam")).") is nog niet <a href=\"".$vars["path"]."cms_optie_groepen.php?edit=12&11k0=".$db->f("optie_soort_id")."&12k0=".$db->f("optie_groep_id")."#vertrekinfo\" target=\"_blank\">ingevoerd</a>";
						$nog_niet_ingevoerd_melding_optietekst[$db->f("optiecategorie")]=true;
					}
				}
			} elseif($optiecategorie_gehad[$db->f("optiecategorie")]) {
				# Foutmelding: optiecategorie al gehad!
				$error[]="optiecategorie '".html("naam_optiecategorie".$db->f("optiecategorie"),"vertrekinfo")."' bevat meerdere teksten.";
			} else {

				# Kijken of teksten zijn goedgekeurd
				if(!preg_match("/\b(".$gegevens["stap1"]["seizoenid"].")\b/",$db->f("vertrekinfo_goedgekeurd_seizoen"))) {
					$error[]="de ".$taal_streepje."optie-tekst '".txt("naam_optiecategorie".$db->f("optiecategorie"),"vertrekinfo")."' is nog niet <a href=\"".$vars["path"]."cms_optie_groepen.php?edit=12&11k0=".$db->f("optie_soort_id")."&12k0=".$db->f("optie_groep_id")."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
				} else {
					$opties[$db->f("optiecategorie")]["naam"]=txt("naam_optiecategorie".$db->f("optiecategorie"),"vertrekinfo");
					$opties[$db->f("optiecategorie")]["tekst"]=trim($db->f("vertrekinfo_optiegroep"));
					$optiecategorie_gehad[$db->f("optiecategorie")]=true;
					$optieleverancier_id=$db->f("optieleverancier_id");

					if($db->f("optiecategorie")==3) {
						$skipas_optie_link="<a href=\"".$vars["path"]."cms_optie_groepen.php?edit=12&11k0=".$db->f("optie_soort_id")."&12k0=".$db->f("optie_groep_id")."#vertrekinfo\" target=\"_blank\">";
					}

					# [optieleverancier-plaats] vervangen
					if(preg_match("/\[optieleverancier-plaats\]/",$opties[$db->f("optiecategorie")]["tekst"])) {
						$db2->query("SELECT vertrekinfo_optiegroep_variabele FROM plaats_optieleverancier WHERE optieleverancier_id='".intval($optieleverancier_id)."' AND plaats_id='".intval($gegevens["stap1"]["accinfo"]["plaats_id"])."' AND vertrekinfo_optiegroep_variabele IS NOT NULL AND vertrekinfo_optiegroep_variabele<>'';");
						if($db2->next_record()) {
							$opties[$db->f("optiecategorie")]["tekst"]=preg_replace("/\[optieleverancier-plaats\]/",$db2->f("vertrekinfo_optiegroep_variabele"),$opties[$db->f("optiecategorie")]["tekst"]);
						} else {
							# Foutmelding: optieleverancier-plaats is niet gevuld voor deze optieleverancier/plaats
							$error[]="vertrekinfo-variabele is niet gevuld voor deze <a href=\"".$vars["path"]."cms_plaatsen.php?show=4&4k0=".intval($gegevens["stap1"]["accinfo"]["plaats_id"])."&highlight_optieleverancier_id=".intval($optieleverancier_id)."\" target=\"_blank\">optieleverancier/plaats</a>";
						}
					}
				}
			}
		}

		# Skipasgegevens
		unset($seizoencontrole);
		if($skipas_id) {
			$db->query("SELECT skipas_id, vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, vertrekinfo_skipas".$ttv." AS vertrekinfo_skipas FROM skipas WHERE skipas_id='".intval($skipas_id)."' AND vertrekinfo_skipas".$ttv." IS NOT NULL;");
			if($db->next_record()) {
				if($db->f("vertrekinfo_skipas")) {
					$skipassen=$db->f("vertrekinfo_skipas");
					$seizoencontrole=true;
				}

				if($seizoencontrole) {
					if(!preg_match("/\b(".$gegevens["stap1"]["seizoenid"].")\b/",$db->f("vertrekinfo_goedgekeurd_seizoen"))) {
						$error[]="de ".$taal_streepje."skipas-tekst is nog niet <a href=\"".$vars["path"]."cms_skipassen.php?edit=10&10k0=".$db->f("skipas_id")."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
					}
				}
				if($optiecategorie_gehad[3]) {
					$error[]="de alinea 'Skipassen' is dubbel aanwezig: zowel op <a href=\"".$vars["path"]."cms_skipassen.php?edit=10&10k0=".$db->f("skipas_id")."#vertrekinfo\" target=\"_blank\">skipas-</a> als ".$skipas_optie_link."optieniveau</a>";
				}
			} else {
				$error[]="er is nog geen ".$taal_streepje."skipas-tekst <a href=\"".$vars["path"]."cms_skipassen.php?edit=10&10k0=".$skipas_id."#vertrekinfo\" target=\"_blank\">ingevoerd</a>";
			}
		}


		# Routebeschrijving land
		if($gegevens["stap1"]["accinfo"]["wzt"]==2) $pre_zomer="zomer";

		$db->query("SELECT land_id, ".$pre_zomer."vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, ".$pre_zomer."vertrekinfo_landroute".$ttv." AS vertrekinfo_landroute FROM land WHERE begincode='".addslashes($gegevens["stap1"]["accinfo"]["begincode"])."' AND ".$pre_zomer."vertrekinfo_landroute".$ttv." IS NOT NULL;");
		if($db->next_record()) {
			$route_land=trim($db->f("vertrekinfo_landroute"));
			if(!preg_match("/\b(".$gegevens["stap1"]["seizoenid"].")\b/",$db->f("vertrekinfo_goedgekeurd_seizoen"))) {
				$error[]="de ".$taal_streepje."land-routebeschrijving is nog niet <a href=\"".$vars["path"]."cms_landen.php?edit=6&bc=84&wzt=".$gegevens["stap1"]["accinfo"]["wzt"]."&6k0=".$db->f("land_id")."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
			}
		} else {
			$error[]="er is nog geen ".$taal_streepje."land-routebeschrijving voor ".wt_he($gegevens["stap1"]["accinfo"]["land"])." <a href=\"".$vars["path"]."cms_landen.php?wzt=".$gegevens["stap1"]["accinfo"]["wzt"]."\" target=\"_blank\">ingevoerd</a>";
		}


		# Routebeschrijving plaats
		$db->query("SELECT vertrekinfo_goedgekeurd_seizoen".$ttv." AS vertrekinfo_goedgekeurd_seizoen, vertrekinfo_plaatsroute".$ttv." AS vertrekinfo_plaatsroute FROM plaats WHERE plaats_id='".intval($gegevens["stap1"]["accinfo"]["plaats_id"])."' AND vertrekinfo_plaatsroute".$ttv." IS NOT NULL;");
		if($db->next_record()) {
			$route_plaats=trim($db->f("vertrekinfo_plaatsroute"));
			if(!preg_match("/\b(".$gegevens["stap1"]["seizoenid"].")\b/",$db->f("vertrekinfo_goedgekeurd_seizoen"))) {
				$error[]="de ".$taal_streepje."plaats-routebeschrijving is nog niet <a href=\"".$vars["path"]."cms_plaatsen.php?edit=4&4k0=".$gegevens["stap1"]["accinfo"]["plaats_id"]."#vertrekinfo\" target=\"_blank\">goedgekeurd</a> voor dit seizoen";
			}
		} else {
			$error[]="er is nog geen ".$taal_streepje."plaats-routebeschrijving <a href=\"".$vars["path"]."cms_plaatsen.php?edit=4&4k0=".$gegevens["stap1"]["accinfo"]["plaats_id"]."#vertrekinfo\" target=\"_blank\">ingevoerd</a>";
		}

		# seizoennaam bepalen
		$db->query("SELECT naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam FROM seizoen WHERE seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."';");
		if($db->next_record()) {
			$seizoennaam=$db->f("naam");
		}



		#
		# Start vertrekinformatie-html
		#

		# Logo linksboven
		if($gegevens["stap1"]["website_specifiek"]["websitetype"]==3) {
			# Zomerhuisje
			if($gegevens["stap1"]["website_specifiek"]["websiteland"]=="be") {
				# .be
				$logo="factuur_logo_zomerhuisje.png";
			} elseif($gegevens["stap1"]["website_specifiek"]["websiteland"]=="en") {
				# .eu
				$logo="factuur_logo_eu.png";
			} else {
				# .nl
				$logo="factuur_logo_zomerhuisje.png";
			}
		} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==6) {
			# Vallandry
			$logo="factuur_logo_vallandry.png";
		} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==4 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==5) {
			# Chalettour
			$logo="factuur_logo_chalettour.png";
		} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==7) {
			if($gegevens["stap1"]["website_specifiek"]["websiteland"]=="en") {
				$logo="factuur_logo_italyhomes.png";
			} else {
				$logo="factuur_logo_italissima.png";
			}
		} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
			# SuperSki
			$logo="factuur_logo_superski.png";
		} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==9) {
			if($gegevens["stap1"]["website"]=="Y") {
				# Venturasol Vacances
				$logo="factuur_logo_venturasolvacances.png";
			} else {
				# Venturasol Wintersport
				$logo="factuur_logo_venturasol.png";
			}
		} else {
			# Chalet Winter
			if($gegevens["stap1"]["website_specifiek"]["websiteland"]=="be") {
				# .be
				$logo="factuur_logo_be.png";
			} elseif($gegevens["stap1"]["website_specifiek"]["websiteland"]=="en") {
				# .eu
				$logo="factuur_logo_eu.png";
			} elseif($gegevens["stap1"]["website_specifiek"]["websiteland"]=="de") {
				# .de
				$logo="factuur_logo_de.png";
			} else {
				# .nl
				$logo="factuur_logo.png";
			}
		}

		$content.="<table cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%\"><tr><td><img src=\"pic/".$logo."\" style=\"width:170px;\"><br/><br/></td>";
		$content.="<td style=\"text-align:right;\">";
		if($gegevens["stap1"]["website_specifiek"]["websiteland"]=="nl") {
			if($gegevens["stap1"]["website_specifiek"]["websitetype"]==9) {

				if($gegevens["stap1"]["website"]=="Y") {
					# Adres voor Venturasol Vacances
					$content.=$gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."<br/>Wipmolenlaan 3<br/>3447 GJ Woerden<br/><br/><b>Tel.: 0541 532798</b><br/><b>E-mail: ".$gegevens["stap1"]["website_specifiek"]["email"]."</b>";
				} else {
					# Adres voor Venturasol Wintersport
					$content.=$gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."<br/>Wipmolenlaan 3<br/>3447 GJ Woerden<br/><br/><b>Tel.: 088 8112233</b><br/><b>E-mail: ".$gegevens["stap1"]["website_specifiek"]["email"]."</b>";
				}
			} else {
				# Adres voor Nederlanders
				$content.=$gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."<br/>Wipmolenlaan 3<br/>3447 GJ Woerden<br/><br/><b>Tel.: 0348 434649</b><br/><b>Fax: 0348 690752</b><br/><b>E-mail: ".$gegevens["stap1"]["website_specifiek"]["email"]."</b>";
			}
		} else {
			if($gegevens["stap1"]["taal"]=="en") {
				# Adres voor Engelstalige buitenlanders
				$content.=$gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."<br/>Wipmolenlaan 3<br/>3447 GJ Woerden<br/>The Netherlands<br/><br/><b>Tel.: +31 348 434649</b><br/><b>Fax: +31 348 690752</b><br/><b>Email: ".$gegevens["stap1"]["website_specifiek"]["email"]."</b>";
			} elseif($gegevens["stap1"]["taal"]=="de") {
				# Adres voor Duitstalige buitenlanders
				$content.=$gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."<br/>Wipmolenlaan 3<br/>3447 GJ Woerden<br/>Niederlande<br/><br/><b>Tel.: +31 348 434649</b><br/><b>Fax: +31 348 690752</b><br/><b>Email: ".$gegevens["stap1"]["website_specifiek"]["email"]."</b>";
			} else {
				# Adres voor Nederlandstalige buitenlanders
				$content.=$gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."<br/>Wipmolenlaan 3<br/>3447 GJ Woerden<br/>Nederland<br/><br/><b>Tel.: +31 348 434649</b><br/><b>Fax: +31 348 690752</b><br/><b>E-mail: ".$gegevens["stap1"]["website_specifiek"]["email"]."</b>";
			}
		}
		$content.="</td></tr></table>";

		# Koptekst
		$content.="<span style=\"font-size:1.5em;font-weight:bold;\">".html("vertrekinformatie","vertrekinfo",array("v_accommodatie"=>$gegevens["stap1"]["accinfo"]["accommodatie"],"v_plaats"=>$gegevens["stap1"]["accinfo"]["plaats"]))."</span>";

		# Te doorlopen
		$te_doorlopen_variabelen=array("type_beheer","telefoonnummer","inchecktijd","uiterlijke_inchecktijd","uitchecktijd","beheer_aanvulling","optieleverancier-plaats");

		# Sjabloon inchecken
		if($vertrekinfo_incheck_sjabloon_id) {
			$db->query("SELECT naam, tekst".$ttv." AS tekst FROM vertrekinfo_sjabloon WHERE vertrekinfo_sjabloon_id='".addslashes($vertrekinfo_incheck_sjabloon_id)."';");
			if($db->next_record()) {
				$inchecken=trim($db->f("tekst"));
			}
			if(!$inchecken) {
				$error[]="is is nog geen tekst bij het gekozen ".$taal_streepje."incheck-sjabloon <a href=\"".$vars["path"]."cms_vertrekinfo_sjablonen.php?edit=54&wzt=".$gegevens["stap1"]["accinfo"]["wzt"]."&54k0=".intval($vertrekinfo_incheck_sjabloon_id)."\" target=\"_blank\">ingevuld</a>";
			}
			# Variabelen sjabloon vullen
			while(list($key,$value)=each($te_doorlopen_variabelen)) {
				if($variabelen[$value]) {
					$inchecken=preg_replace("/\[".preg_replace("/\//","\/",$value)."\]/",$variabelen[$value],$inchecken);
				} elseif(preg_match("/\[".$value."\]/",$inchecken)) {
					$error[]="variabele [".$value."] kan nog niet worden <a href=\"".$vars["path"]."cms_accommodaties.php?edit=1&archief=0&1k0=".$gegevens["stap1"]["accinfo"]["accommodatie_id"]."#vertrekinfo\" target=\"_blank\">gevuld bij deze accommodatie</a>";
				}
			}
			$content.="<p><b>".html("inchecken","vertrekinfo").":</b><br/>".nl2br(wt_he($inchecken))."</p>";
		} else {
			$error[]="er is nog geen incheck-sjabloon <a href=\"".$vars["path"]."cms_accommodaties.php?edit=1&archief=0&1k0=".$gegevens["stap1"]["accinfo"]["accommodatie_id"]."#vertrekinfo\" target=\"_blank\">gekozen bij deze accommodatie</a>";
		}

		// bijkomendekosten
		$bijkomendekosten = new bijkomendekosten($gegevens["stap1"]["typeid"], "type");
		$bijkomendekosten->seizoen_id = $gegevens["stap1"]["seizoenid"];
		$bijkomendekosten->vertrekinfo = true;
		$bk = $bijkomendekosten->get_booking_data($gegevens);

		$kosten = $bijkomendekosten->get_costs();

		if(is_array($bk["voldaan"])) {
			$bk_html .= "<b>".html("inclusief","vertrekinfo").":</b>";
			$bk_html .= "<ul style=\"margin-top:0;padding-left:13px;\">";
			foreach ($bk["voldaan"] as $key => $value) {
				$bk_html .= "<li style=\"line-height:10px;\">".wt_he($value["naam"])."</li>";
			}
			$bk_html .= "</ul>";
		}
		if(is_array($bk["ter_plaatse"])) {
			$bk_html .= "<b>".html("verplichttevoldoen","vertrekinfo").":</b>";
			$bk_html .= "<ul style=\"margin-top:0;padding-left:13px;\">";
			foreach ($bk["ter_plaatse"] as $key => $value) {
				$bk_html .= "<li style=\"line-height:10px;\">".wt_he($value["naam"]);
				if($value["toonbedrag"]) {
					$bk_html .= " (".wt_he($value["toonbedrag"]).")";
				}
				$bk_html .= "</li>";
			}
			$bk_html .= "</ul>";
		}
		if(is_array($bk["uitbreiding"])) {
			$bk_html .= "<b>".html("optioneel","vertrekinfo").":</b>";
			$bk_html .= "<ul style=\"margin-top:0;padding-left:13px;\">";
			foreach ($bk["uitbreiding"] as $key => $value) {
				$bk_html .= "<li style=\"line-height:10px;\">".wt_he($value["naam"]);
				if($value["toonbedrag"]) {
					$bk_html .= " (".wt_he($value["toonbedrag"]).")";
				}
				$bk_html .= "</li>";
			}
			$bk_html .= "</ul>";
		}
		if(is_array($bk["diversen"])) {
			$bk_html .= "<b>".html("diversen","vertrekinfo").":</b>";
			$bk_html .= "<ul style=\"margin-top:0;padding-left:13px;\">";
			foreach ($bk["diversen"] as $key => $value) {
				$bk_html .= "<li style=\"line-height:10px;\">".wt_he($value["naam"]);
				if($value["toonbedrag"]) {
					$bk_html .= " (".wt_he($value["toonbedrag"]).")";
				}
				$bk_html .= "</li>";
			}
			$bk_html .= "</ul>";
		}

		if($bk_html) {
			// add bijkomendekosten-info
			$content.="<p>".html("bij-boeking-de-volgende-incl-excl","vertrekinfo").":</p>";
			$content .= $bk_html;

			if(is_array($bk["ter_plaatse"]) or is_array($bk["uitbreiding"]) or is_array($bk["diversen"])) {
				$content.="<p>".html("verplichtekostenondervoorbehoud","vertrekinfo")."</p>";
			}
		}

		if($skipassen) {
			$content.="<p><b>".html("skipassen","vertrekinfo").":</b><br/>".nl2br(wt_he($skipassen))."</p>";
		}

		if(is_array($opties)) {
			while(list($key,$value)=each($opties)) {
				$content.="<p><b>".wt_he($value["naam"]).":</b><br/>".nl2br(wt_he($value["tekst"]))."</p>";
			}
		}

		# Routebeschrijving
		$content.="<!-- newpage -->";

		# Koptekst routebeschrijving
		$content.="<div style=\"font-size:1.5em;font-weight:bold;\">";
		if($vertrekinfo_plaatsnaam_beheer) {
			$content.=html("routebeschrijvingnaarbeheer","vertrekinfo",array("v_beheer"=>$variabelen["type_beheer"],"v_plaatsnaambeheer"=>$vertrekinfo_plaatsnaam_beheer));
		} else {
			$content.=html("routebeschrijvingnaar","vertrekinfo",array("v_accommodatie"=>$gegevens["stap1"]["accinfo"]["accommodatie"],"v_plaats"=>$gegevens["stap1"]["accinfo"]["plaats"]));
		}
		$content.="</div>";

		$content.="<p><b>".html("routebeschrijving_inleiding","vertrekinfo")."</b></p>";

		$content.="<p><b><u>".html("enkeleaanwijzingen","vertrekinfo").":</u></b><br/>".nl2br(wt_he($route_land))."</p>";

		$content.="<p><b>".html("routenaarplaats","vertrekinfo",array("v_plaats"=>$gegevens["stap1"]["accinfo"]["plaats"])).":</b><br/>".nl2br(wt_he($route_plaats))."</p>";

		$content.="<p><b>".html("routenaarbeheer","vertrekinfo",array("v_beheer"=>$variabelen["type_beheer"],"v_accommodatie"=>$gegevens["stap1"]["accinfo"]["accommodatie"])).":</b><br/>".nl2br(wt_he($route_beheer))."</p>";

		# GPS-coördinaten
		if(($gps_lat and $gps_long) or ($gps_lat_beheer and $gps_long_beheer)) {
			if($gps_lat and $gps_lat_beheer) {
				$content.="<p><b>".html("gps_coordinaten_beheer","vertrekinfo",array("v_beheer"=>$variabelen["type_beheer_kort"],"v_gpslat"=>$gps_lat_beheer,"v_gpslong"=>$gps_long_beheer))."</b><br/>";
				$content.="<b>".html("gps_coordinaten_accommodatie","vertrekinfo",array("v_gpslat"=>$gps_lat,"v_gpslong"=>$gps_long))."</b><br/>";
				$content.=nl2br(html("gps_letop","vertrekinfo"))."</p>";
			} elseif($gps_lat) {
				$content.="<p><b>".html("gps_coordinaten","vertrekinfo",array("v_gpslat"=>$gps_lat,"v_gpslong"=>$gps_long))."</b><br/>".nl2br(html("gps_letop","vertrekinfo"))."</p>";
			} elseif($gps_lat_beheer) {
				$content.="<p><b>".html("gps_coordinaten","vertrekinfo",array("v_gpslat"=>$gps_lat_beheer,"v_gpslong"=>$gps_long_beheer))."</b><br/>".nl2br(html("gps_letop","vertrekinfo"))."</p>";
			}
		}

		# Adres en telefoon

		if(!$variabelen["telefoonnummer"]) {
			$error[]="het beheer-telefoonnummer is nog niet <a href=\"".$vars["path"]."cms_accommodaties.php?edit=1&archief=0&1k0=".$gegevens["stap1"]["accinfo"]["accommodatie_id"]."#vertrekinfo\" target=\"_blank\">gevuld bij deze accommodatie</a>";
		}
		if($vertrekinfo_soortadres) {
			$content.="<p><b>";
			if($vertrekinfo_soortadres==1) {
				# Adres accommodatie
				$content.=html("adresaccommodatie","vertrekinfo");
			} else {
				# Sleuteladres
				$content.=html("sleuteladres","vertrekinfo");
			}
			$content.=":</b><br/>".nl2br(wt_he($vertrekinfo_adres))."<br/>";
			if($variabelen["telefoonnummer"]) {
				$content.=html("telefoonnummer","vertrekinfo",array("v_beheer"=>$variabelen["type_beheer"])).": ".wt_he($variabelen["telefoonnummer"]);
			}
			$content.="</p>";
		} else {
			$error[]="het adres is nog niet <a href=\"".$vars["path"]."cms_accommodaties.php?edit=1&archief=0&1k0=".$gegevens["stap1"]["accinfo"]["accommodatie_id"]."#vertrekinfo\" target=\"_blank\">gevuld bij deze accommodatie</a>";
		}

		# controle op niet herkende variabelen
		if(preg_match_all("/\[([^[:space:]]+)\]/",$content,$regs)) {
			while(list($key,$value)=each($regs[1])) {
				if(!in_array($value,$te_doorlopen_variabelen)) {
					$error[]="variabele [".$value."] is niet bekend in het systeem";
				}
			}
		}

		# Gegevens voor vouchers aan $return toevoegen
		$return["vouchergegevens"]["beheer_aanvulling"]=$variabelen["beheer_aanvulling"];
		$return["vouchergegevens"]["telefoonnummer"]=$variabelen["telefoonnummer"];


		if(is_array($error)) {
			$return["error"]="<p><b style=\"color:red;\">De vertrekinfo kon niet worden aangemaakt:</b><ul>";
			while(list($key,$value)=each($error)) {
				$return["error"].="<li>".$value."</li>";
			}
			$return["error"].="</ul></p>";

			# Opslaan dat vertrekinfo errors bevat
			$db2->query("UPDATE boeking SET vertrekinfo_error=1 WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

		} elseif($content) {

			# Opslaan dat vertrekinfo geen errors bevat
			$db2->query("UPDATE boeking SET vertrekinfo_error=0 WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

			$return["content"]=$content;




			#
			# PDF aanmaken
			#
			if($save_pdffile) {

				// create new PDF document
				$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

				$pdf->seizoennaam=utf8_encode($seizoennaam);

				// set document information
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor($gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]);
				#$pdf->SetTitle('');

				// set default header data
				$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

				// set header and footer fonts
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

				// set default monospaced font
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

				//set margins
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

				//set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

				//set image scale factor
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

				//set some language-dependent strings
				$pdf->setLanguageArray($l);

				$htmlcontent=utf8_encode($content);
				$pages=preg_split("/<!-- newpage -->/",$htmlcontent);

				if(is_array($pages)) {
					while(list($key,$value)=each($pages)) {

						// set font
						$pdf->SetFont('helvetica', '', 9);

						$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);

						// add a page
						$pdf->AddPage("P");

						$pdf->writeHTML($value, true, 0, true, 0);
					}
				}
				$pdf->Output($save_pdffile,"F");
			}
		}

		return $return;

	}

}



require_once($vars["unixdir"]."admin/tcpdf/config/lang/eng.php");
require_once($vars["unixdir"]."admin/tcpdf/tcpdf.php");

define ('PDF_PAGE_FORMAT', 'A4');
define ('PDF_PAGE_ORIENTATION', "P"); # Portraint orientation
define ("PDF_CREATOR", 'TCPDF');

if(!class_exists('MYPDF')) {
	class MYPDF extends TCPDF {
		//Page header
		public function Header() {

		}

		// Page footer
		public function Footer() {
			// Position at 2.0 cm from bottom
			$this->SetY(-20);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			$this->Cell(0, 0, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'C');

			// seizoennaam
			$this->SetY(-20);
			$this->Cell(0, 0, $this->seizoennaam, 0, 0, 'L');

		}
	}
}


?>