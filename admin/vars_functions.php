<?php

function txt($id,$page="",$settings="",$html=false) {
	global $vars,$txt,$txta,$path,$gegevens,$boeking_bepaalt_taal,$txt_al_getoond,$woorden_toegestaan;
	if($boeking_bepaalt_taal and is_array($gegevens)) {
		$taal=$gegevens["stap1"]["taal"];
		$websitetype=$gegevens["stap1"]["website_specifiek"]["websitetype"];
		$websiteland=$gegevens["stap1"]["website_specifiek"]["websiteland"];
		$website=$gegevens["stap1"]["website"];
	} else {
		$taal=$vars["taal"];
		$websitetype=$vars["websitetype"];
		$websiteland=$vars["websiteland"];
		$website=$vars["website"];
	}
	if(!$id) {
		trigger_error("op page ".$page." wordt een lege \$id aangeroepen",E_USER_NOTICE);
	}
	if($page) {
		if(!isset($txt["nl"][$page][$id])) {
			trigger_error("txt[nl][".$page."][".$id."] niet beschikbaar",E_USER_NOTICE);
		}
		if($websitetype==5 and isset($txt[$taal."_z_t"][$page][$id])) {
			# Chalettour zomer (niet in gebruik)
			$return=$txt[$taal."_z_t"][$page][$id];
		} elseif($websitetype==9 and $website=="Y" and isset($txt[$taal."_y"][$page][$id])) {
			# Venturasol Vacances
			$return=$txt[$taal."_y"][$page][$id];
		} elseif($websitetype==8 and isset($txt[$taal."_w"][$page][$id])) {
			# SuperSki
			$return=$txt[$taal."_w"][$page][$id];
		} elseif($websitetype==7 and isset($txt[$taal."_i"][$page][$id])) {
			# Italissima
			$return=$txt[$taal."_i"][$page][$id];
		} elseif(($websitetype==3 or $websitetype==5 or $websitetype==7) and isset($txt[$taal."_z"][$page][$id])) {
			# Zomerhuisje.nl / Italissima
			$return=$txt[$taal."_z"][$page][$id];
		} elseif(($websitetype==4 or $websitetype==5) and isset($txt[$taal."_t"][$page][$id])) {
			# Chalettour.nl
			$return=$txt[$taal."_t"][$page][$id];
		} elseif($websitetype==6 and isset($txt[$taal."_v"][$page][$id])) {
			# Chalets in Vallandry
			$return=$txt[$taal."_v"][$page][$id];
		} elseif($websiteland=="be" and isset($txt[$taal."_b"][$page][$id])) {
			# Belgie
			$return=$txt[$taal."_b"][$page][$id];
		} else {
			$return=$txt[$taal][$page][$id];
		}
	} else {
		if(!isset($txta[$taal][$id])) {
			trigger_error("txta[".$taal."][".$id."] niet beschikbaar",E_USER_NOTICE);
		}
		if($websitetype==5 and isset($txta[$taal."_z_t"][$id])) {
			# Chalettour zomer (niet in gebruik)
			$return=$txta[$taal."_z_t"][$id];
		} elseif($websitetype==9 and $website=="Y" and isset($txta[$taal."_y"][$id])) {
			# Venturasol
			$return=$txta[$taal."_y"][$id];
		} elseif($websitetype==8 and isset($txta[$taal."_w"][$id])) {
			# SuperSki
			$return=$txta[$taal."_w"][$id];
		} elseif($websitetype==7 and isset($txta[$taal."_i"][$id])) {
			# Italissima
			$return=$txta[$taal."_i"][$id];
		} elseif(($websitetype==3 or $websitetype==5 or $websitetype==7) and isset($txta[$taal."_z"][$id])) {
			# Zomerhuisje.nl / Italissima
			$return=$txta[$taal."_z"][$id];
		} elseif(($websitetype==4 or $websitetype==5) and isset($txta[$taal."_t"][$id])) {
			# Chalettour.nl
			$return=$txta[$taal."_t"][$id];
		} elseif($websitetype==6 and isset($txta[$taal."_v"][$id])) {
			# Chalets in Vallandry NL
			$return=$txta[$taal."_v"][$id];
		} elseif($websiteland=="be" and isset($txta[$taal."_b"][$id])) {
			# Belgie
			$return=$txta[$taal."_b"][$id];
		} else {
			$return=$txta[$taal][$id];
		}
	}
	if($return=="") {
		if($taal=="nl")	trigger_error("txt[nl][".$page."][".$id."] is leeg",E_USER_NOTICE);
#		$return="[[MISSING: ".$id."_".$taal."]]";
		if($vars["lokale_testserver"]) {
			$return="-".preg_replace("@_@", " ", $id)."-";
			if(is_array($settings)) {
				foreach ($settings as $key => $value) {
					if(preg_match("@^v_@", $key)) {
						$return .= " [[".$key."]]";
					}
				}
			}
		} else {
			$return="-";
		}
	}
	if($html) {
		$return=wt_he($return);
	}
	if(is_array($settings)) {
		while(list($key,$value)=each($settings)) {
		$value=strval($value);
			if(ereg("^(l[0-9]+)$",$key,$regs)) {
				# Links
				if(substr($value,0,1)=="#") {
					$return=ereg_replace("\[\[".$regs[1]."\]\]","<a href=\"".$value."\">",$return);
				} else {
					# Wat voor soort link?
					if(ereg("^javascript",$value,$regs2))  {
						# Javascript
						$link=$value;
					} else {

						# Wel of geen aname
						if(ereg("(.*)#(.*)",$value,$regs2)) {
							$value=$regs2[1];
							$aname="#".$regs2[2];
						} else {
							$aname="";
						}

						# Wel of geen query-string
						if(ereg("(.*)\?(.*)",$value,$regs2)) {
							$value=$regs2[1];
							$qs="?".$regs2[2];
						} else {
							$qs="";
						}
						if(ereg("^http_(.*)",$value,$regs2))  {
							# Met volledag pad ervoor
							$link=$vars["basehref"].txt("menu_".$regs2[1]).".php".$qs.$aname;
						} else {
							# Gewone interne link
							$link=$path.txt("menu_".$value).".php".$qs.$aname;
						}
					}
					$return=ereg_replace("\[\[".$regs[1]."\]\]","<a href=\"".$link."\">",$return);
				}
				$return=ereg_replace("\[\[/".$regs[1]."\]\]","</a>",$return);
			} elseif(ereg("^(v_[a-z0-9]+)$",$key,$regs)) {
				# Vars
				$return=ereg_replace("\[\[".$regs[1]."\]\]",($html ? wt_he($value) : $value),$return);
			} elseif(ereg("^(h_[a-z0-9]+)$",$key,$regs)) {
				# HTML
				$return=ereg_replace("\[\[".$regs[1]."\]\]",$value,$return);
			}
		}
	}
	return $return;
}

function html($id,$page="",$settings="") {
	return txt($id,$page,$settings,true);
}

function accinfo($typeid,$aankomstdatum=0,$aantalpersonen=0,$options=array()) {
	global $vars,$mustlogin,$unixdir;
	if($options["gebruik_gegevensvar_niet"]) {

	} else {
		global $gegevens;
	}

	# Als het een boeking betreft, taal afstemmen op boeking
	if($gegevens["stap1"]["website_specifiek"]["ttv"]) {
		$ttv=$gegevens["stap1"]["website_specifiek"]["ttv"];
	} else {
		$ttv=$vars["ttv"];
	}
	if($gegevens["stap1"]["wederverkoop"] or $options["wederverkoop"]) {
		$wederverkoop=true;
	} else {
		if($mustlogin and $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$wederverkoop=false;
		} else {
			$wederverkoop=$vars["wederverkoop"];
		}
	}

	$db=new DB_sql;
	$db2=new DB_sql;
	$db->query("SELECT a.wzt, a.naam AS accommodatie, a.bestelnaam AS abestelnaam, a.soortaccommodatie, a.toonper, a.flexibel, t.websites, a.vertrekinfo_seizoengoedgekeurd, a.vertrekinfo_seizoengoedgekeurd_en, a.accommodatie_id, t.leverancier_id, a.aankomst_plusmin, a.vertrek_plusmin, a.receptie".$ttv." AS receptie, a.telefoonnummer, a.voucherinfo".$ttv." AS avoucherinfo, a.mailtekst_id, a.optiedagen_klanten_vorig_seizoen, a.korteomschrijving".$ttv." AS akorteomschrijving, t.korteomschrijving".$ttv." AS tkorteomschrijving, t.voucherinfo".$ttv." AS tvoucherinfo, a.inclusief".$ttv." AS inclusief, t.inclusief".$ttv." AS tinclusief, a.exclusief".$ttv." AS exclusief, t.exclusief".$ttv." AS texclusief, a.tonen AS atonen, t.tonen AS ttonen, t.type_id, t.naam".$ttv." AS type, t.naam AS tnaam, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, t.slaapkamers, t.slaapkamersextra".$ttv." AS slaapkamersextra, t.badkamers, t.badkamersextra".$ttv." AS badkamersextra, t.oppervlakte, t.oppervlakteextra".$ttv." AS oppervlakteextra, t.zomerwinterkoppeling_accommodatie_id, a.kwaliteit AS akwaliteit, t.kwaliteit AS tkwaliteit, t.aangepaste_min_tonen, t.leverancierscode, t.beheerder_id, t.eigenaar_id, t.verzameltype, t.verzameltype_parent, t.voorraad_gekoppeld_type_id, p.naam AS plaats, p.plaats_id, l.naam".$ttv." AS land, l.begincode, s.naam AS skigebied, lev.aflopen_allotment FROM type t, accommodatie a, plaats p, land l, leverancier lev, skigebied s WHERE p.skigebied_id=s.skigebied_id AND t.leverancier_id=lev.leverancier_id AND t.type_id='".addslashes($typeid)."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id;");
	if($db->next_record()) {
		while(list($key,$value)=each($db->Record)) {
			if(!ereg("^[0-9]$",$key)) $return[$key]=$value;
		}
		$return["naam"]=$return["accommodatie"].($return["type"] ? " ".$return["type"] : "");
		$return["accnaam"]=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : "");
		$return["soortaccommodatie"]=$vars["soortaccommodatie"][$db->f("soortaccommodatie")];
		$return["aantalpersonen"]=$return["optimaalaantalpersonen"].($return["optimaalaantalpersonen"]<>$return["maxaantalpersonen"] ? " - ".$return["maxaantalpersonen"] : "")." ".($return["optimaalaantalpersonen"]==1 ? txt("persoon") : txt("personen"));
		$return["naam_ap"]=$return["naam"]." (".$return["optimaalaantalpersonen"].($return["optimaalaantalpersonen"]<>$return["maxaantalpersonen"] ? " - ".$return["maxaantalpersonen"] : "")." ".txt("pers").")";
		if($db->f("accommodatie")<>$db->f("abestelnaam")) {
			# afwijkende bestelnaam (leveranciersnaam)
			$return["bestelnaam"]=$db->f("abestelnaam").($db->f("tnaam") ? " ".$db->f("tnaam") : "");
		}
		$return["url"]=$vars["basehref"].txt("menu_accommodatie")."/".$return["begincode"].$return["type_id"]."/";
		$return["url_seo"]=seo_acc_url($return["begincode"].$return["type_id"],$db->f("soortaccommodatie"),$db->f("accommodatie"),$db->f("type"));
		$return["plaats_url"]=$vars["basehref"].txt("canonical_accommodatiepagina")."/".txt("menu_plaats")."/".wt_convert2url_seo($return["plaats"])."/";
		$return["url_zonderpad"]=txt("menu_accommodatie")."/".$return["begincode"].$return["type_id"]."/";
		$return["plaats_url_zonderpad"]=txt("canonical_accommodatiepagina")."/".txt("menu_plaats")."/".wt_convert2url_seo($return["plaats"])."/";
		$return["skigebied"]=$db->f("skigebied");
		$return["cms_typenaam"]=$return["type"];
		$return["accommodatieid"]=$db->f("accommodatie_id");
		$return["typeid"]=$return["type_id"];
		$return["leverancierid"]=$return["leverancier_id"];
		$return["beheerderid"]=$db->f("beheerder_id");
		$return["eigenaarid"]=$db->f("eigenaar_id");
		$return["verzameltype"]=$db->f("verzameltype");
		$return["verzameltype_parent"]=$db->f("verzameltype_parent");
		$return["aankomst_plusmin"]=$return["aankomst_plusmin"];
		$return["vertrek_plusmin"]=$return["vertrek_plusmin"];
		$return["aflopen_allotment"]=$return["aflopen_allotment"];
		if(ereg("T",$return["websites"]) or ereg("O",$return["websites"]) or ereg("Z",$return["websites"]) or ereg("E",$return["websites"])) {
			$return["wederverkoop"]=true;
		}
		if($return["tkwaliteit"]) {
			$return["kwaliteit"]=$return["tkwaliteit"];
		} elseif($return["akwaliteit"]) {
			$return["kwaliteit"]=$return["akwaliteit"];
		}

#		$return["leverancierscode"]=$return["leverancierscode"];

		if($db->f("atonen") and $db->f("ttonen")) $return["tonen"]=true;

		if($return["cms_typenaam"]) {
			$return["cms_typenaam"].=" (".$return["code"]." ".$return["aantalpersonen"].")";
		} else {
			$return["cms_typenaam"]=$return["code"]." (".$return["aantalpersonen"].")";
		}

		# korteomschrijving
		if($db->f("akorteomschrijving") or $db->f("tkorteomschrijving")) {
			if($db->f("tkorteomschrijving")) {
				$return["korteomschrijving"]=$db->f("tkorteomschrijving");
			} else {
				$return["korteomschrijving"]=$db->f("akorteomschrijving");
			}
		}

		if($return["optimaalaantalpersonen"]>12) {
			if($return["aangepaste_min_tonen"]) {
				$return["min_tonen"]=$return["aangepaste_min_tonen"];
			} else {
				$return["min_tonen"]=floor($return["optimaalaantalpersonen"]*.5);
			}
		} else {
			$return["min_tonen"]=1;
		}
		if($return["toonper"]==3 or $wederverkoop) {
			$beginmet=1;
		} else {
			$beginmet=$return["min_tonen"];
		}
		for($i=$beginmet;$i<=$return["maxaantalpersonen"];$i++) {
			$return["aantalpersonen_array"][$i]=$i." ".($i==1 ? txt("persoon") : txt("personen"));
		}

		# Hoofdfoto bepalen
		if(file_exists($unixdir."pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
			$return["hoofdfoto"]="pic/cms/types_specifiek/".$db->f("type_id").".jpg";
		} elseif(file_exists($unixdir."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
			$return["hoofdfoto"]="pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
		}

		# Vertekdagen bepalen
		include($unixdir."content/vertrekdagaanpassing.html");

		if($return["toonper"]==3) {
#			$db2->query("SELECT t.week, s.tonen, t.c_bruto, t.c_verkoop_site, t.beschikbaar, s.seizoen_id FROM tarief t, seizoen s WHERE t.seizoen_id=s.seizoen_id AND s.type='".$vars["seizoentype"]."' AND t.type_id='".addslashes($typeid)."';");
			$db2->query("SELECT t.week, s.tonen, t.c_bruto, t.c_verkoop_site, t.beschikbaar, t.blokkeren_wederverkoop, t.wederverkoop_verkoopprijs, s.seizoen_id FROM tarief t, seizoen s WHERE t.seizoen_id=s.seizoen_id AND t.type_id='".addslashes($typeid)."';");
			if(!$db2->num_rows()) {
				if($mustlogin and !$vars["temp_error_geen_tarieven"]) trigger_error("geen tarieven bekend (toonper==3) bij function accinfo - typeid ".$typeid,E_USER_NOTICE);
				$vars["temp_error_geen_tarieven"]=true;
			}
			while($db2->next_record()) {
				if($vertrekdag[$db2->f("seizoen_id")][date("dm",$db2->f("week"))] or $return["aankomst_plusmin"]) {
					$aankomstdatum_unixtime=mktime(0,0,0,date("m",$db2->f("week")),date("d",$db2->f("week"))+$vertrekdag[$db2->f("seizoen_id")][date("dm",$db2->f("week"))]+$return["aankomst_plusmin"],date("Y",$db2->f("week")));
				} else {
					$aankomstdatum_unixtime=$db2->f("week");
				}
				$return["aankomstdatum"][$db2->f("week")]=datum("DAG D MAAND JJJJ",$aankomstdatum_unixtime,$vars["taal"]);
				$return["aankomstdatum_dmj"][$db2->f("week")]=date("d/m/Y",$aankomstdatum_unixtime);
				$return["aankomstdatum_unixtime"][$db2->f("week")]=$aankomstdatum_unixtime;
				if($db2->f("tonen")>1 and $db2->f("c_bruto")>0 and $db2->f("c_verkoop_site") and $db2->f("beschikbaar")==1 and ($db2->f("blokkeren_wederverkoop")==0 or !$wederverkoop) and ($db2->f("wederverkoop_verkoopprijs")>0 or !$wederverkoop)) {
					$return["aankomstdatum_beschikbaar"][$db2->f("week")]=datum("DAG D MAAND JJJJ",$aankomstdatum_unixtime,$vars["taal"]);
				}
				if($db2->f("c_verkoop_site")>0 and ($db2->f("wederverkoop_verkoopprijs")>0 or !$wederverkoop)) {
					$return["aankomstdatum_tariefingevoerd"][$db2->f("week")]=datum("DAG D MAAND JJJJ",$aankomstdatum_unixtime,$vars["taal"]);
				}
			}
		} else {
#			$db2->query("SELECT t.week, s.tonen, t.bruto, t.arrangementsprijs, t.beschikbaar, s.seizoen_id FROM tarief t, seizoen s WHERE t.seizoen_id=s.seizoen_id AND s.type='".$vars["seizoentype"]."' AND t.type_id='".addslashes($typeid)."';");
			$db2->query("SELECT t.week, s.tonen, t.bruto, t.arrangementsprijs, t.beschikbaar, t.blokkeren_wederverkoop, t.wederverkoop_verkoopprijs, t.wederverkoop_opslag_percentage, s.seizoen_id FROM tarief t, seizoen s WHERE t.seizoen_id=s.seizoen_id AND t.type_id='".addslashes($typeid)."';");
			if(!$db2->num_rows()) {
#				if($mustlogin and !$vars["temp_error_geen_tarieven"]) trigger_error("geen tarieven bekend (toonper<>3) bij typeid ".$typeid,E_USER_NOTICE);
				$vars["temp_error_geen_tarieven"]=true;
			}
			while($db2->next_record()) {
				if($vertrekdag[$db2->f("seizoen_id")][date("dm",$db2->f("week"))] or $return["aankomst_plusmin"]) {
					$aankomstdatum_unixtime=mktime(0,0,0,date("m",$db2->f("week")),date("d",$db2->f("week"))+$vertrekdag[$db2->f("seizoen_id")][date("dm",$db2->f("week"))]+$return["aankomst_plusmin"],date("Y",$db2->f("week")));
				} else {
					$aankomstdatum_unixtime=$db2->f("week");
				}
				$return["aankomstdatum"][$db2->f("week")]=datum("DAG D MAAND JJJJ",$aankomstdatum_unixtime,$vars["taal"]);
				$return["aankomstdatum_dmj"][$db2->f("week")]=date("d/m/Y",$aankomstdatum_unixtime);
				$return["aankomstdatum_unixtime"][$db2->f("week")]=$aankomstdatum_unixtime;
				if($db2->f("tonen")>1 and ($db2->f("bruto")>0 or $db2->f("arrangementsprijs")>0) and $db2->f("beschikbaar")==1 and ($db2->f("blokkeren_wederverkoop")==0 or !$wederverkoop) and ($db2->f("wederverkoop_verkoopprijs")>0 or !$wederverkoop)) {
					$return["aankomstdatum_beschikbaar"][$db2->f("week")]=datum("DAG D MAAND JJJJ",$aankomstdatum_unixtime,$vars["taal"]);
				}
				if(($db2->f("bruto")>0 or $db2->f("arrangementsprijs")>0) and ($db2->f("wederverkoop_verkoopprijs")>0 or !$wederverkoop)) {
					$return["aankomstdatum_tariefingevoerd"][$db2->f("week")]=datum("DAG D MAAND JJJJ",$aankomstdatum_unixtime,$vars["taal"]);
				}
				if($db2->f("wederverkoop_verkoopprijs")>0) {
					$return["accprijs_perweek"][$db2->f("week")]=$db2->f("wederverkoop_verkoopprijs");
					# Opslag-percentage er weer vanaf
					if($db2->f("wederverkoop_opslag_percentage")>0 and $db2->f("bruto")) {
						$return["accprijs_perweek"][$db2->f("week")]-=(($db2->f("wederverkoop_opslag_percentage")/100)*$db2->f("bruto"));
						$return["accprijs_perweek"][$db2->f("week")]=floor(($return["accprijs_perweek"][$db2->f("week")])/5)*5;
					}
				}
			}
		}
		@ksort($return["aankomstdatum_beschikbaar"]);
		@ksort($return["aankomstdatum"]);
		@ksort($return["aankomstdatum_dmj"]);

		# Vertrekdatum bepalen
		if($aankomstdatum) {
			$weeklater=mktime(0,0,0,date("m",$aankomstdatum),date("d",$aankomstdatum)+7,date("Y",$aankomstdatum));
			if($return["aankomstdatum_unixtime"][$weeklater]) {
				$return["vertrekdatum"]=$return["aankomstdatum_unixtime"][$weeklater];
			} elseif(!$return["aankomstdatum"][$weeklater] and $return["vertrek_plusmin"]<>0 and $return["aankomst_plusmin"]==$return["vertrek_plusmin"]) {
				// end of season: Sunday-Sunday
				$return["vertrekdatum"]=$weeklater;
				$return["vertrekdatum"]=mktime(0,0,0,date("m",$return["vertrekdatum"]),date("d",$return["vertrekdatum"])+$return["vertrek_plusmin"],date("Y",$return["vertrekdatum"]));
			} else {
				$return["vertrekdatum"]=$weeklater;
			}

			# Andere verblijfsduur?
			if($return["vertrek_plusmin"]<>0) {
				$return["vertrekdatum"]=mktime(0,0,0,date("m",$return["vertrekdatum"]),date("d",$return["vertrekdatum"])-$return["aankomst_plusmin"]+$return["vertrek_plusmin"],date("Y",$return["vertrekdatum"]));
			}
		}

		# Skipas-naam uit database halen
		if($return["toonper"]<>3 and !$wederverkoop) {
			$db2->query("SELECT s.skipas_id, s.naam, s.naam_voorkant, s.aantaldagen FROM skipas s, accommodatie a WHERE a.skipas_id=s.skipas_id AND a.accommodatie_id='".addslashes($return["accommodatie_id"])."';");
			if($db2->next_record()) {
				$return["skipasid"]=$db2->f("skipas_id");
				$return["skipas_naam"]=$db2->f("naam_voorkant");
				$return["skipas_aantaldagen"]=$db2->f("aantaldagen");
			}
		}

		# Tarieven bepalen
		if($aankomstdatum and $aantalpersonen) {
			# Tarieven
			if($return["toonper"]==3 or $wederverkoop) {
#				$db2->query("SELECT t.c_verkoop_site, s.seizoen_id, s.annuleringsverzekering_poliskosten, s.annuleringsverzekering_percentage, s.reisverzekering_poliskosten FROM tarief t, seizoen s WHERE t.type_id=".addslashes($typeid)." AND t.seizoen_id=s.seizoen_id AND s.type='".$vars["seizoentype"]."' AND t.week='".addslashes($aankomstdatum)."';");
				$db2->query("SELECT t.c_verkoop_site, t.wederverkoop_verkoopprijs, t.wederverkoop_commissie_agent, s.seizoen_id, s.annuleringsverzekering_poliskosten, s.annuleringsverzekering_percentage_1, s.annuleringsverzekering_percentage_2, s.annuleringsverzekering_percentage_3, s.annuleringsverzekering_percentage_4, s.schadeverzekering_percentage, s.reisverzekering_poliskosten, s.verzekeringen_poliskosten FROM tarief t, seizoen s WHERE t.type_id='".addslashes($typeid)."' AND t.seizoen_id=s.seizoen_id AND t.week='".addslashes($aankomstdatum)."';");
				if($db2->next_record()) {
					if($wederverkoop) {
						$tarief=$db2->f("wederverkoop_verkoopprijs");
					} else {
						$tarief=$db2->f("c_verkoop_site");
					}
					$return["seizoenid"]=$db2->f("seizoen_id");

					# geen losse poliskosten meer per verzekering
#					$return["annuleringsverzekering_poliskosten"]=$db2->f("annuleringsverzekering_poliskosten");

					$return["annuleringsverzekering_percentage_1"]=$db2->f("annuleringsverzekering_percentage_1");
					$return["annuleringsverzekering_percentage_2"]=$db2->f("annuleringsverzekering_percentage_2");
					$return["annuleringsverzekering_percentage_3"]=$db2->f("annuleringsverzekering_percentage_3");
					$return["annuleringsverzekering_percentage_4"]=$db2->f("annuleringsverzekering_percentage_4");
					$return["schadeverzekering_percentage"]=$db2->f("schadeverzekering_percentage");

					# geen losse poliskosten meer per verzekering
#					$return["reisverzekering_poliskosten"]=$db2->f("annuleringsverzekering_poliskosten");

					$return["verzekeringen_poliskosten"]=$db2->f("verzekeringen_poliskosten");

					if($wederverkoop) {
						$return["commissie"]=$db2->f("wederverkoop_commissie_agent");
					}
				}
			} else {
#				$db2->query("SELECT tp.prijs, s.seizoen_id, s.annuleringsverzekering_poliskosten, s.annuleringsverzekering_percentage, s.reisverzekering_poliskosten FROM tarief_personen tp, seizoen s WHERE tp.type_id=".$db->f("type_id")." AND tp.seizoen_id=s.seizoen_id AND s.type='".$vars["seizoentype"]."' AND tp.week='".addslashes($aankomstdatum)."' AND personen=".addslashes($aantalpersonen).";");
				$db2->query("SELECT tp.prijs, s.seizoen_id, s.annuleringsverzekering_poliskosten, s.annuleringsverzekering_percentage_1, s.annuleringsverzekering_percentage_2, s.annuleringsverzekering_percentage_3, s.annuleringsverzekering_percentage_4, s.schadeverzekering_percentage, s.reisverzekering_poliskosten, s.verzekeringen_poliskosten FROM tarief_personen tp, seizoen s WHERE tp.type_id=".$db->f("type_id")." AND tp.seizoen_id=s.seizoen_id AND tp.week='".addslashes($aankomstdatum)."' AND personen=".addslashes($aantalpersonen).";");
				if($db2->next_record()) {
					$tarief=$db2->f("prijs");
					$return["seizoenid"]=$db2->f("seizoen_id");

					# geen losse poliskosten meer per verzekering
#					$return["annuleringsverzekering_poliskosten"]=$db2->f("annuleringsverzekering_poliskosten");

					$return["annuleringsverzekering_percentage_1"]=$db2->f("annuleringsverzekering_percentage_1");
					$return["annuleringsverzekering_percentage_2"]=$db2->f("annuleringsverzekering_percentage_2");
					$return["annuleringsverzekering_percentage_3"]=$db2->f("annuleringsverzekering_percentage_3");
					$return["annuleringsverzekering_percentage_4"]=$db2->f("annuleringsverzekering_percentage_4");
					$return["schadeverzekering_percentage"]=$db2->f("schadeverzekering_percentage");

					# geen losse poliskosten meer per verzekering
#					$return["reisverzekering_poliskosten"]=$db2->f("annuleringsverzekering_poliskosten");

					$return["verzekeringen_poliskosten"]=$db2->f("verzekeringen_poliskosten");
				}
			}

			# Zijn er aanbiedingen van toepassing?
			$aanbieding=aanbiedinginfo($typeid,$return["seizoenid"],$aankomstdatum);
			if($aanbieding["typeid_sort"][$typeid]["bedrag"][$aankomstdatum]) {
				# aanbieding toepassen op tarief
				$tarief_org=$tarief;
				$tarief=verwerk_aanbieding($tarief,$aanbieding["typeid_sort"][$typeid],$aankomstdatum);
				if($tarief_org>$tarief) $return["aanbieding"]=true;
			}
			$return["tarief"]=$tarief;

			# accprijs bepalen
			if($return["toonper"]==3 or $wederverkoop) {
				# bij accommodatie
				$return["accprijs"]=$tarief;
			} else {
				# bij arrangement
				if($aanbieding["typeid_sort"][$typeid]["bedrag"][$aankomstdatum]) {
					# aanbieding toepassen op accprijs
					$accprijs=verwerk_aanbieding($return["accprijs_perweek"][$aankomstdatum],$aanbieding["typeid_sort"][$typeid],$aankomstdatum);
				} else {
					$accprijs=$return["accprijs_perweek"][$aankomstdatum];
				}
				$return["accprijs"]=round($accprijs,2);
			}
		}
		return $return;
	} else {
		return false;
	}
}

function aanbiedinginfo($typeid,$seizoenid,$aankomstdatum=0) {
	#
	# Aanbieding van toepassing?
	#
	global $vars,$mustlogin,$id,$voorkant_cms;
	$db=new DB_sql;

	$korting_query_from["accommodaties"]="aanbieding_accommodatie aa";
	$korting_query_where["accommodaties"]="aa.aanbieding_id=a.aanbieding_id AND aa.accommodatie_id=ac.accommodatie_id";

	$korting_query_from["types"]="aanbieding_type at";
	$korting_query_where["types"]="at.aanbieding_id=a.aanbieding_id AND at.type_id=t.type_id";

#	if($_SERVER["REMOTE_ADDR"]<>"82.173.186.802") {
#		$korting_query_from["leveranciers"]="aanbieding_leverancier al";
#		$korting_query_where["leveranciers"]="al.aanbieding_id=a.aanbieding_id AND al.leverancier_id=t.leverancier_id";
#	}

	if($aankomstdatum) {
#		$extra_where.=" AND ta.week='".addslashes($aankomstdatum)."' AND ta.beschikbaar=1 AND (ta.bruto>0 OR ta.c_bruto>0)";
		$extra_where.=" AND ta.week='".addslashes($aankomstdatum)."'";
	}
	if(!$vars["aanbiedinginfo_binnen_cms"] and $vars["seizoentype"] and $vars["websitetype"]<>6) {
		$extra_where.=" AND ac.wzt='".addslashes($vars["seizoentype"])."'";
	}

	if(!$vars["aanbiedinginfo_binnen_cms"]) {
		$extra_where.=" AND ad.week>".(time())." AND a.begindatum<='".date("Y-m-d")."' AND (a.einddatum>='".date("Y-m-d")."' OR a.einddatum IS NULL) AND a.tonen=1";
	}

	# typeid omzetten naar cijfer (om foute query te voorkomen bij rare URL)
	if(!preg_match("/,/",$typeid)) $typeid=intval($typeid);

	unset($return);
	while(list($key,$value)=each($korting_query_from)) {
		$db->query("SELECT a.aanbieding_id, a.naam, a.onlinenaam".$vars["ttv"]." AS onlinenaam, a.omschrijving".$vars["ttv"]." AS omschrijving, a.tonen, a.archief, a.soort, a.toon_abpagina, a.bedrag, a.bedrag_soort, UNIX_TIMESTAMP(a.begindatum) AS begindatum, UNIX_TIMESTAMP(a.einddatum) AS einddatum, a.toonkorting, a.toon_als_aanbieding, ad.week, t.type_id, ac.naam AS accommodatie, ac.tonen AS atonen, t.tonen AS ttonen, t.websites, ac.soortaccommodatie, ac.toonper, ac.accommodatie_id, t.naam".$vars["ttv"]." AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, s.naam AS skigebied, l.naam".$vars["ttv"]." AS land, l.begincode FROM aanbieding a, accommodatie ac, aanbieding_aankomstdatum ad, type t, tarief ta, plaats p, land l, skigebied s, ".$korting_query_from[$key]." WHERE ta.week=ad.week AND ta.type_id=t.type_id AND ta.beschikbaar=1 AND ac.plaats_id=p.plaats_id AND p.land_id=l.land_id AND p.skigebied_id=s.skigebied_id AND ad.aanbieding_id=a.aanbieding_id AND t.accommodatie_id=ac.accommodatie_id AND (a.seizoen1_id='".addslashes($seizoenid)."' OR a.seizoen2_id='".addslashes($seizoenid)."' OR a.seizoen3_id='".addslashes($seizoenid)."' OR a.seizoen4_id='".addslashes($seizoenid)."')".$extra_where.($typeid ? " AND t.type_id IN (".addslashes($typeid).")" : "")." AND ".$korting_query_where[$key]." ORDER BY a.soort, a.onlinenaam".$vars["ttv"].", a.begindatum, ac.naam, t.optimaalaantalpersonen, t.maxaantalpersonen, ad.week, t.naam;");
#		echo $db->lastquery."<hr>";
		while($db->next_record()) {

			$soort=($db->f("soort")==2 ? $vars["aanbieding_soort"][$db->f("soort")] : ($db->f("onlinenaam") ? $db->f("onlinenaam") : ""));
			# aanbiedingid vullen
			if(!$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["id"]) {
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["id"]=$db->f("aanbieding_id");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["naam"]=$db->f("naam");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["onlinenaam"]=$db->f("onlinenaam");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["omschrijving"]=$db->f("omschrijving");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["soort"]=$soort;
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["soortid"]=$db->f("soort");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["toon_abpagina"]=$db->f("toon_abpagina");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["bedrag"]=$db->f("bedrag");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["bedrag_soort"]=$db->f("bedrag_soort");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["begindatum"]=$db->f("begindatum");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["einddatum"]=$db->f("einddatum");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["aankomstdatum"]=$db->f("week");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["toonkorting"]=$db->f("toonkorting");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["toon_als_aanbieding"]=$db->f("toon_als_aanbieding");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["tonen"]=$db->f("tonen");
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["archief"]=$db->f("archief");
# uitgezet op 28-3-2012 (want: leek mij een fout)
#				if($db->f("atonen") and $db->f("ttonen")) $return["aanbiedingid_sort"][$db->f("aanbieding_id")]["tonen"]=1;
			}
			if($typeid) {
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["weken"][$db->f("week")]=$db->f("bedrag");
			} else {
				$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["acc"][$db->f("type_id")][$db->f("week")]=$db->f("bedrag");
			}

			# Bepalen of het om een lastminute-aanbieding gaat
			if($db->f("soort")==2 and $db->f("week")>time()) {
				if(!isset($return["aanbiedingid_sort"][$db->f("aanbieding_id")]["lastminute"])) $return["aanbiedingid_sort"][$db->f("aanbieding_id")]["lastminute"]=true;
				if($db->f("week")>(time()+(86400*21))) {
					$return["aanbiedingid_sort"][$db->f("aanbieding_id")]["lastminute"]=false;
				}
			}

			# typeid vullen
			if(!$return["typeid_sort"][$db->f("type_id")]["accnaam"]) {
				$return["typeid_sort"][$db->f("type_id")]["accnaam"]=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : "");
				$return["typeid_sort"][$db->f("type_id")]["accommodatieid"]=$db->f("accommodatie_id");
				$return["typeid_sort"][$db->f("type_id")]["typeid"]=$db->f("type_id");
				$return["typeid_sort"][$db->f("type_id")]["soortaccommodatie"]=$db->f("soortaccommodatie");
				$return["typeid_sort"][$db->f("type_id")]["plaats"]=$db->f("plaats");
				$return["typeid_sort"][$db->f("type_id")]["skigebied"]=$db->f("skigebied");
				$return["typeid_sort"][$db->f("type_id")]["land"]=$db->f("land");
				$return["typeid_sort"][$db->f("type_id")]["url"]=$vars["path"]."accommodatie/".$db->f("begincode").$db->f("type_id")."/";
				$return["typeid_sort"][$db->f("type_id")]["toonper"]=$db->f("toonper");
				$return["typeid_sort"][$db->f("type_id")]["optimaalaantalpersonen"]=$db->f("optimaalaantalpersonen");
				$return["typeid_sort"][$db->f("type_id")]["maxaantalpersonen"]=$db->f("maxaantalpersonen");
				$return["typeid_sort"][$db->f("type_id")]["aantalpersonen"]=$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? " - ".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? txt("persoon") : txt("personen"));
				if($db->f("atonen") and $db->f("ttonen")) $return["typeid_sort"][$db->f("type_id")]["tonen"]=1;
				$return["typeid_sort"][$db->f("type_id")]["websites"]=$db->f("websites");
				$return["typeid_sort"][$db->f("type_id")]["toonkorting"]=$db->f("toonkorting");
				$return["typeid_sort"][$db->f("type_id")]["toon_als_aanbieding"]=$db->f("toon_als_aanbieding");
			}

			if(!$controleer_vertrekdagen[$db->f("type_id")]) {
				if($controleer_vertrekdagen_inquery) $controleer_vertrekdagen_inquery.=",".$db->f("type_id"); else $controleer_vertrekdagen_inquery=$db->f("type_id");
			}
			$controleer_vertrekdagen[$db->f("type_id")][$db->f("week")]=true;

			$return["typeid_sort"][$db->f("type_id")]["bedrag"][$db->f("week")]+=$db->f("bedrag");
			$return["typeid_sort"][$db->f("type_id")]["soort"][$db->f("week")]=$soort;
			if($db->f("bedrag_soort")==1) {
				# Korting in euro's
				$return["typeid_sort"][$db->f("type_id")]["korting_euro"][$db->f("week")]+=$db->f("bedrag");
			} elseif($db->f("bedrag_soort")==2) {
				# Kortingspercentage
				$return["typeid_sort"][$db->f("type_id")]["korting_percentage"][$db->f("week")]+=$db->f("bedrag");
			} elseif($db->f("bedrag_soort")==3) {
				# Exact bedrag
				$return["typeid_sort"][$db->f("type_id")]["exact_bedrag"][$db->f("week")]+=$db->f("bedrag");
			}
		}
	}
	if($controleer_vertrekdagen_inquery and $id=="aanbiedingen") {
		$db->query("SELECT t.type_id, v.soort, v.afwijking FROM type t, accommodatie a, vertrekdagtype v, accommodatie_seizoen az WHERE t.accommodatie_id=a.accommodatie_id AND v.seizoen_id='".addslashes($seizoenid)."' AND az.vertrekdagtype_id=v.vertrekdagtype_id AND az.seizoen_id=v.seizoen_id AND az.accommodatie_id=a.accommodatie_id AND t.type_id IN (".$controleer_vertrekdagen_inquery.");");
#		echo $db->lastquery."<hr>";
		while($db->next_record()) {
			while(list($key,$value)=@each($controleer_vertrekdagen[$db->f("type_id")])) {
				$return["typeid_sort"][$db->f("type_id")]["exacte_aankomstdatum"][$key]=vertrekdagaanpassing($key,$db->f("soort"),$db->f("afwijking"));
			}
		}
	}
	return $return;
}

function verwerk_aanbieding($bedrag,$aanbieding,$week) {

	if($bedrag>0) {
		# Exact bedrag
		if($aanbieding["exact_bedrag"][$week]>0) {
			$bedrag=$aanbieding["exact_bedrag"][$week];
		} else {
			$bedrag=$bedrag;
		}

		# Kortingspercentage
		if($aanbieding["korting_percentage"][$week]>0) {
			$bedrag=floor(($bedrag*(1-$aanbieding["korting_percentage"][$week]/100))/5)*5;
		}

		# Korting in euro
		if($aanbieding["korting_euro"][$week]>0) {
			$bedrag=$bedrag-$aanbieding["korting_euro"][$week];
		}
	}

	if($bedrag>0) {
		return $bedrag;
	} else {
		return false;
	}
}

function boekinginfo($boekingid) {
	global $vars;
	$db=new DB_sql;
	# Algemene boekingsgegevens ophalen
	$db->query("SELECT boeking_id, boekingsnummer, bezoeker_id, leverancierscode, leverancierscode_oud, leverancier_id, beheerder_id, eigenaar_id, type_id, aankomstdatum, aankomstdatum_exact, vertrekdatum_exact, seizoen_id, aantalpersonen, stap_voltooid, annuleringsverzekering, toonper, flexibel, verblijfsduur, verkoop, verkoop_gewijzigd, commissie, btw_over_commissie, btw_over_commissie_percentage, wederverkoop, reserveringskosten, annuleringsverzekering_poliskosten, annuleringsverzekering_percentage_1, annuleringsverzekering_percentage_2, annuleringsverzekering_percentage_3, annuleringsverzekering_percentage_4, schadeverzekering, schadeverzekering_percentage, accprijs, reisverzekering_poliskosten, verzekeringen_poliskosten, annuleringsverzekering_korting, opmerkingen_boeker, opmerkingen_opties, opmerkingen_intern, opmerkingen_voucher, opmerkingen_vertreklijst, opmerkingen_klant, factuur_tekstvak1, factuur_tekstvak2, factuur_tekstvak3, factuur_tekstvak4, factuur_tekstvak5, debiteurnummer, landcode, factuur_versturen, factuur_tewijzigen, factuur_bedrag_wijkt_af, UNIX_TIMESTAMP(factuurdatum) AS factuurdatum, UNIX_TIMESTAMP(factuurdatum_eerste_factuur) AS factuurdatum_eerste_factuur, vraag_ondertekening, UNIX_TIMESTAMP(factuur_ondertekendatum) AS factuur_ondertekendatum, UNIX_TIMESTAMP(aanbetaling_betaaldatum) AS aanbetaling_betaaldatum, aanbetaling_bedrag, UNIX_TIMESTAMP(restbetaling_betaaldatum) AS restbetaling_betaaldatum, restbetaling_bedrag, gezien, goedgekeurd, geannuleerd, wijzigen_dagen, website, taal, UNIX_TIMESTAMP(zit_in_beheersysteem) AS zit_in_beheersysteem, gewijzigd, log, UNIX_TIMESTAMP(invuldatum) AS invuldatum, UNIX_TIMESTAMP(bevestigdatum) AS bevestigdatum, UNIX_TIMESTAMP(bewerkdatetime) AS bewerkdatetime, referentiekeuze, lasteditor, reisbureau_user_id, aanbetaling1, aanbetaling1_vastgezet, aanbetaling1_gewijzigd, aanbetaling1_dagennaboeken, aanbetaling2, UNIX_TIMESTAMP(aanbetaling2_datum) AS aanbetaling2_datum, totale_reissom, totale_reissom_inkoop, totale_reissom_inkoop_actueel, totale_reissom_dagenvooraankomst, kortingscode_id, mailtekst_opties, mailtekst_klanten_vorig_seizoen, mailverstuurd_klanten_vorig_seizoen, aanmaning_tekst, aanmaning_mailblokkeren, mailblokkeren_ontvangenbetaling, UNIX_TIMESTAMP(mailverstuurd_opties) AS mailverstuurd_opties, mailblokkeren_opties, mailverstuurd_persoonsgegevens_dagenvoorvertrek, UNIX_TIMESTAMP(mailverstuurd_persoonsgegevens) AS mailverstuurd_persoonsgegevens, mailblokkeren_persoonsgegevens, mailblokkeren_klanten_vorig_seizoen, mailblokkeren_enquete, mailverstuurd_enquete, pdfplattegrond_nietnodig, status_klanten_vorig_seizoen, UNIX_TIMESTAMP(status_vanaf_klanten_vorig_seizoen) AS status_vanaf_klanten_vorig_seizoen, mailverstuurd_klanten_vorig_seizoen, mailverstuurd2_klanten_vorig_seizoen, opmerkingen_klanten_vorig_seizoen, voucherstatus, accommodatievoucher_sturen, verzendmethode_reisdocumenten, optieaanvraag_id, verzameltype, verzameltype_gekozentype_id, calc, calc_bewaren, goedgekeurde_betaling, bestelstatus, UNIX_TIMESTAMP(besteldatum) AS besteldatum, besteluser_id, UNIX_TIMESTAMP(inkoopfactuurdatum) AS inkoopfactuurdatum, bestelstatus_schriftelijk_later, bestelstatus_schriftelijk_later_aanvinkmoment, inkoopnetto, inkoopbruto, inkoopcommissie, inkooptoeslag, inkoopkorting, inkoopkorting_euro, inkoopkorting_percentage, inkoopaanbetaling_gewijzigd, totaalfactuurbedrag, totaal_volgens_ontvangen_factuur, betalingsverschil, factuurnummer_leverancier, factuur_opmerkingen, factuurbedrag_gecontroleerd, eenmaliggecontroleerd, aan_leverancier_doorgegeven_naam, tonen_in_mijn_boeking, vervallen_aanvraag, voorraad_afboeken FROM boeking WHERE boeking_id='".addslashes($boekingid)."';");
	if($db->next_record()) {
		$accinfo=accinfo($db->f("type_id"),$db->f("aankomstdatum"),$db->f("aantalpersonen"),array("gebruik_gegevensvar_niet"=>true));
		$return["stap1"]["accinfo"]=$accinfo;

#		$return["stap1"]["accinfo"]["toonper"]=$db->f("toonper");

		$return["stap1"]["boekingid"]=$db->f("boeking_id");
		$return["stap1"]["boekingsnummer"]=trim($db->f("boekingsnummer"));
		$return["stap1"]["bezoekerid"]=$db->f("bezoeker_id");
		$return["stap1"]["leverancierscode"]=$db->f("leverancierscode");
		$return["stap1"]["leverancierscode_oud"]=$db->f("leverancierscode_oud");
		$return["stap1"]["leverancierid"]=$db->f("leverancier_id");
		$return["stap1"]["beheerderid"]=$db->f("beheerder_id");
		$return["stap1"]["eigenaarid"]=$db->f("eigenaar_id");
		$return["stap1"]["typeid"]=$db->f("type_id");
		$return["stap1"]["aantalpersonen"]=$db->f("aantalpersonen");
		$return["stap1"]["aankomstdatum"]=$db->f("aankomstdatum");
		$return["stap1"]["vertrekdatum"]=$accinfo["vertrekdatum"];
		$return["stap1"]["aankomstdatum_exact"]=$db->f("aankomstdatum_exact");
		$return["stap1"]["vertrekdatum_exact"]=$db->f("vertrekdatum_exact");
		$return["stap1"]["aantalnachten"]=round(($return["stap1"]["vertrekdatum_exact"]-$return["stap1"]["aankomstdatum_exact"])/86400);
		$return["stap1"]["seizoenid"]=$db->f("seizoen_id");
#		$return["stap1"]["annuleringsverzekering"]=$db->f("annuleringsverzekering");
		$return["stap1"]["flexibel"]=$db->f("flexibel");
		$return["stap1"]["verblijfsduur"]=$db->f("verblijfsduur");
		$return["stap1"]["verkoop_gewijzigd"]=$db->f("verkoop_gewijzigd");
		$return["stap1"]["verkoop_ongewijzigd"]=$db->f("verkoop");
		$return["stap1"]["bewerkdatetime"]=$db->f("bewerkdatetime");
		$return["stap1"]["lasteditor"]=$db->f("lasteditor");
		$return["stap1"]["reisbureau_user_id"]=$db->f("reisbureau_user_id");
		$return["stap1"]["wederverkoop"]=$db->f("wederverkoop");
		$return["stap1"]["commissie"]=$db->f("commissie");
		$return["stap1"]["btw_over_commissie"]=$db->f("btw_over_commissie");
		$return["stap1"]["btw_over_commissie_percentage"]=$db->f("btw_over_commissie_percentage");
		$return["stap1"]["calc"]=$db->f("calc");
		$return["stap1"]["calc_bewaren"]=$db->f("calc_bewaren");
		if($db->f("goedgekeurde_betaling")>0) $return["stap1"]["goedgekeurde_betaling"]=$db->f("goedgekeurde_betaling");

		if($db->f("verkoop_gewijzigd")) {
			$return["stap1"]["verkoop"]=$db->f("verkoop_gewijzigd");
		} else {
			if($db->f("verkoop")=="") {

				# Doorgeven aan funtion accinfo of het wederverkoop betreft
#				$vars["accinfo_wederverkoop"]=$db->f("wederverkoop");

				$temp_accinfo=accinfo($return["stap1"]["typeid"],$return["stap1"]["aankomstdatum"],$return["stap1"]["aantalpersonen"],array("wederverkoop"=>$db->f("wederverkoop")));
				$return["stap1"]["verkoop"]=$temp_accinfo["tarief"];
				$return["stap1"]["commissie"]=$temp_accinfo["commissie"];

				$commissie_opnieuw_opgehaald=true;

				if($return["stap1"]["flexibel"] or $return["stap1"]["verblijfsduur"]>1) {
					if($return["stap1"]["flexibel"]) {
						$flextarief=bereken_flex_tarief($return["stap1"]["typeid"],$return["stap1"]["aankomstdatum_exact"],0,$return["stap1"]["vertrekdatum_exact"]);
					} else {
						$flextarief=bereken_flex_tarief($return["stap1"]["typeid"],$return["stap1"]["aankomstdatum"],$return["stap1"]["verblijfsduur"]);
					}
					$return["stap1"]["verkoop"]=$flextarief["tarief"];
				}
			} else {
				$return["stap1"]["verkoop"]=$db->f("verkoop");
			}
		}
		if($db->f("accprijs")=="") {
			if($return["stap1"]["flexibel"] or $return["stap1"]["verblijfsduur"]>1) {
				$return["stap1"]["accprijs"]=$return["stap1"]["verkoop"];
			} else {
				if(!$temp_accinfo) {
#					$vars["accinfo_wederverkoop"]=$db->f("wederverkoop");
					$temp_accinfo=accinfo($return["stap1"]["typeid"],$return["stap1"]["aankomstdatum"],$return["stap1"]["aantalpersonen"],array("wederverkoop"=>$db->f("wederverkoop")));
				}
				$return["stap1"]["accprijs"]=$temp_accinfo["accprijs"];
			}
		} else {
			$return["stap1"]["accprijs"]=$db->f("accprijs");
		}

		$return["stap1"]["website"]=$db->f("website");

		$return["stap1"]["annuleringsverzekering_poliskosten"]=$db->f("annuleringsverzekering_poliskosten");
		$return["stap1"]["annuleringsverzekering_percentage_1"]=$db->f("annuleringsverzekering_percentage_1");
		$return["stap1"]["annuleringsverzekering_percentage_2"]=$db->f("annuleringsverzekering_percentage_2");
		$return["stap1"]["annuleringsverzekering_percentage_3"]=$db->f("annuleringsverzekering_percentage_3");
		$return["stap1"]["annuleringsverzekering_percentage_4"]=$db->f("annuleringsverzekering_percentage_4");
		$return["stap1"]["annuleringsverzekering_korting"]=$db->f("annuleringsverzekering_korting");

		$return["stap1"]["schadeverzekering"]=$db->f("schadeverzekering");
		if($db->f("schadeverzekering")) {
			// make sure clients that already got a schadeverzekering, can still make changes
			$vars["schadeverzekering_mogelijk"] = 1;
			$vars["websiteinfo"]["schadeverzekering_mogelijk"][$return["stap1"]["website"]] = 1;
		}

		$return["stap1"]["schadeverzekering_percentage"]=$db->f("schadeverzekering_percentage");
		$return["stap1"]["reisverzekering_poliskosten"]=$db->f("reisverzekering_poliskosten");
		$return["stap1"]["verzekeringen_poliskosten"]=$db->f("verzekeringen_poliskosten");
		$return["stap1"]["reserveringskosten"]=$db->f("reserveringskosten");
		$return["stap1"]["log"]=$db->f("log");
		$return["stap1"]["gezien"]=$db->f("gezien");
		$return["stap1"]["gewijzigd"]=$db->f("gewijzigd");
		$return["stap1"]["taal"]=$db->f("taal");
		$return["stap1"]["zit_in_beheersysteem"]=$db->f("zit_in_beheersysteem");
		$return["stap1"]["bevestigdatum"]=$db->f("bevestigdatum");
		$return["stap1"]["invuldatum"]=$db->f("invuldatum");
		$return["stap1"]["opmerkingen_boeker"]=$db->f("opmerkingen_boeker");
		$return["stap1"]["opmerkingen_opties"]=$db->f("opmerkingen_opties");
		$return["stap1"]["opmerkingen_intern"]=$db->f("opmerkingen_intern");
		$return["stap1"]["opmerkingen_voucher"]=$db->f("opmerkingen_voucher");
		$return["stap1"]["opmerkingen_vertreklijst"]=$db->f("opmerkingen_vertreklijst");
		$return["stap1"]["opmerkingen_klant"]=$db->f("opmerkingen_klant");
#		$return["stap1"]["factuur_tekstvak1"]=$db->f("factuur_tekstvak1");
#		$return["stap1"]["factuur_tekstvak2"]=$db->f("factuur_tekstvak2");
#		$return["stap1"]["factuur_tekstvak3"]=$db->f("factuur_tekstvak3");
#		$return["stap1"]["factuur_tekstvak4"]=$db->f("factuur_tekstvak4");
#		$return["stap1"]["factuur_tekstvak5"]=$db->f("factuur_tekstvak5");
		$return["stap1"]["debiteurnummer"]=$db->f("debiteurnummer");
		$return["stap1"]["landcode"]=$db->f("landcode");
		$return["stap1"]["factuur_versturen"]=$db->f("factuur_versturen");
		$return["stap1"]["factuur_tewijzigen"]=$db->f("factuur_tewijzigen");
		$return["stap1"]["factuur_bedrag_wijkt_af"]=$db->f("factuur_bedrag_wijkt_af");
		$return["stap1"]["factuurdatum"]=$db->f("factuurdatum");
		$return["stap1"]["factuurdatum_eerste_factuur"]=$db->f("factuurdatum_eerste_factuur");
		$return["stap1"]["vraag_ondertekening"]=$db->f("vraag_ondertekening");
		$return["stap1"]["factuur_ondertekendatum"]=$db->f("factuur_ondertekendatum");
		$return["stap1"]["aanbetaling_betaaldatum"]=$db->f("aanbetaling_betaaldatum");
		$return["stap1"]["aanbetaling_bedrag"]=$db->f("aanbetaling_bedrag");
		$return["stap1"]["restbetaling_betaaldatum"]=$db->f("restbetaling_betaaldatum");
		$return["stap1"]["restbetaling_bedrag"]=$db->f("restbetaling_bedrag");
		$return["stap1"]["stap_voltooid"]=$db->f("stap_voltooid");
		$return["stap1"]["goedgekeurd"]=$db->f("goedgekeurd");
		$return["stap1"]["geannuleerd"]=$db->f("geannuleerd");
		$return["stap1"]["referentiekeuze"]=$db->f("referentiekeuze");
		$return["stap1"]["wijzigen_dagen"]=$db->f("wijzigen_dagen");
		$return["stap1"]["mailtekst_opties"]=$db->f("mailtekst_opties");
		$return["stap1"]["mailtekst_klanten_vorig_seizoen"]=$db->f("mailtekst_klanten_vorig_seizoen");
		$return["stap1"]["mailverstuurd_klanten_vorig_seizoen"]=$db->f("mailverstuurd_klanten_vorig_seizoen");
		$return["stap1"]["aanmaning_tekst"]=$db->f("aanmaning_tekst");
		$return["stap1"]["aanmaning_mailblokkeren"]=$db->f("aanmaning_mailblokkeren");
		$return["stap1"]["mailblokkeren_ontvangenbetaling"]=$db->f("mailblokkeren_ontvangenbetaling");
		$return["stap1"]["mailverstuurd_opties"]=$db->f("mailverstuurd_opties");
		$return["stap1"]["mailblokkeren_opties"]=$db->f("mailblokkeren_opties");
		$return["stap1"]["mailverstuurd_persoonsgegevens_dagenvoorvertrek"]=$db->f("mailverstuurd_persoonsgegevens_dagenvoorvertrek");
		$return["stap1"]["mailverstuurd_persoonsgegevens"]=$db->f("mailverstuurd_persoonsgegevens");
		$return["stap1"]["mailblokkeren_persoonsgegevens"]=$db->f("mailblokkeren_persoonsgegevens");
		$return["stap1"]["mailblokkeren_klanten_vorig_seizoen"]=$db->f("mailblokkeren_klanten_vorig_seizoen");
		$return["stap1"]["mailblokkeren_enquete"]=$db->f("mailblokkeren_enquete");
		$return["stap1"]["mailverstuurd_enquete"]=$db->f("mailverstuurd_enquete");
		$return["stap1"]["pdfplattegrond_nietnodig"]=$db->f("pdfplattegrond_nietnodig");
		$return["stap1"]["status_klanten_vorig_seizoen"]=$db->f("status_klanten_vorig_seizoen");
		$return["stap1"]["status_vanaf_klanten_vorig_seizoen"]=$db->f("status_vanaf_klanten_vorig_seizoen");
		$return["stap1"]["mailverstuurd_klanten_vorig_seizoen"]=$db->f("mailverstuurd_klanten_vorig_seizoen");
		$return["stap1"]["mailverstuurd2_klanten_vorig_seizoen"]=$db->f("mailverstuurd2_klanten_vorig_seizoen");
		$return["stap1"]["opmerkingen_klanten_vorig_seizoen"]=$db->f("opmerkingen_klanten_vorig_seizoen");
		$return["stap1"]["voucherstatus"]=$db->f("voucherstatus");
		$return["stap1"]["accommodatievoucher_sturen"]=$db->f("accommodatievoucher_sturen");
		$return["stap1"]["verzendmethode_reisdocumenten"]=$db->f("verzendmethode_reisdocumenten");
		$return["stap1"]["optieaanvraag_id"]=$db->f("optieaanvraag_id");
		$return["stap1"]["verzameltype"]=$db->f("verzameltype");
		$return["stap1"]["verzameltype_gekozentype_id"]=$db->f("verzameltype_gekozentype_id");
		$return["stap1"]["bestelstatus"]=$db->f("bestelstatus");
		$return["stap1"]["besteldatum"]=$db->f("besteldatum");
		$return["stap1"]["besteluser_id"]=$db->f("besteluser_id");
		$return["stap1"]["inkoopfactuurdatum"]=$db->f("inkoopfactuurdatum");
		$return["stap1"]["bestelstatus_schriftelijk_later"]=$db->f("bestelstatus_schriftelijk_later");
		$return["stap1"]["bestelstatus_schriftelijk_later_aanvinkmoment"]=$db->f("bestelstatus_schriftelijk_later_aanvinkmoment");
		$return["stap1"]["inkoopnetto"]=$db->f("inkoopnetto");
		$return["stap1"]["inkoopbruto"]=$db->f("inkoopbruto");
		$return["stap1"]["inkoopcommissie"]=$db->f("inkoopcommissie");
		$return["stap1"]["inkooptoeslag"]=$db->f("inkooptoeslag");
		$return["stap1"]["inkoopkorting"]=$db->f("inkoopkorting");
		$return["stap1"]["inkoopkorting_euro"]=$db->f("inkoopkorting_euro");
		$return["stap1"]["inkoopkorting_percentage"]=$db->f("inkoopkorting_percentage");
		$return["stap1"]["inkoopaanbetaling_gewijzigd"]=$db->f("inkoopaanbetaling_gewijzigd");
		$return["stap1"]["totaalfactuurbedrag"]=$db->f("totaalfactuurbedrag"); # = totaalbedrag inkoop accommodatie
		$return["stap1"]["totaal_volgens_ontvangen_factuur"]=$db->f("totaal_volgens_ontvangen_factuur");
		$return["stap1"]["betalingsverschil"]=$db->f("betalingsverschil");
		$return["stap1"]["factuurnummer_leverancier"]=$db->f("factuurnummer_leverancier");
		$return["stap1"]["factuur_opmerkingen"]=$db->f("factuur_opmerkingen");
		$return["stap1"]["factuurbedrag_gecontroleerd"]=$db->f("factuurbedrag_gecontroleerd");
		$return["stap1"]["aan_leverancier_doorgegeven_naam"]=$db->f("aan_leverancier_doorgegeven_naam");
		$return["stap1"]["tonen_in_mijn_boeking"]=$db->f("tonen_in_mijn_boeking");
		$return["stap1"]["vervallen_aanvraag"]=$db->f("vervallen_aanvraag");
		$return["stap1"]["voorraad_afboeken"]=$db->f("voorraad_afboeken");

		if($accinfo["aankomstdatum_unixtime"][$return["stap1"]["aankomstdatum"]]) {
			$return["stap1"]["dagen_voor_vertrek"]=round(($accinfo["aankomstdatum_unixtime"][$return["stap1"]["aankomstdatum"]]-mktime(0,0,0,date("m"),date("d"),date("Y")))/(60*60*24));
		} else {
			$return["stap1"]["dagen_voor_vertrek"]=round(($return["stap1"]["aankomstdatum"]-mktime(0,0,0,date("m"),date("d"),date("Y")))/(60*60*24));
		}
		if($return["stap1"]["dagen_voor_vertrek"]>=$return["stap1"]["wijzigen_dagen"]) {
			$return["stap1"]["wijzigen_toegestaan"]=true;
		}
		if(!$return["stap1"]["bevestigdatum"] or $return["stap1"]["bevestigdatum"]>(time()-86400*10)) {
			$return["stap1"]["annuleringsverzekering_wijzigen_toegestaan"]=true;
		}
		$return["stap1"]["kortingscode_id"]=$db->f("kortingscode_id");

		# Aanbetalingen
		$return["stap1"]["aanbetaling1"]=$db->f("aanbetaling1");
		$return["stap1"]["aanbetaling1_vastgezet"]=$db->f("aanbetaling1_vastgezet");
		$return["stap1"]["aanbetaling1_gewijzigd"]=$db->f("aanbetaling1_gewijzigd");
		$return["stap1"]["aanbetaling1_dagennaboeken"]=$db->f("aanbetaling1_dagennaboeken");
		$return["stap1"]["aanbetaling2"]=$db->f("aanbetaling2");
		$return["stap1"]["aanbetaling2_datum"]=$db->f("aanbetaling2_datum");
		$return["stap1"]["totale_reissom"]=$db->f("totale_reissom");
		$return["stap1"]["totale_reissom_inkoop"]=$db->f("totale_reissom_inkoop"); # = totale reissom inkoop op moment van aanmaken klantfactuur
		$return["stap1"]["totale_reissom_inkoop_actueel"]=$db->f("totale_reissom_inkoop_actueel"); # = totale reissom inkoop op moment van opslaan "Inkoopgegevens accommodatie"

		$return["stap1"]["totale_reissom_dagenvooraankomst"]=$db->f("totale_reissom_dagenvooraankomst");
		$return["stap1"]["eenmaliggecontroleerd"]=$db->f("eenmaliggecontroleerd");

		# Taal bepalen
		if($return["stap1"]["taal"]=="nl") {
			$return["stap1"]["website_specifiek"]["ttv"]="";
		} else {
			$return["stap1"]["website_specifiek"]["ttv"]="_".$return["stap1"]["taal"];
		}

		# Type website bepalen
		if(!$return["stap1"]["website"]) $return["stap1"]["website"]="C";
		$return["stap1"]["website_specifiek"]["websitenaam"]=$vars["websiteinfo"]["websitenaam"][$return["stap1"]["website"]];
		$return["stap1"]["website_specifiek"]["langewebsitenaam"]=$vars["websiteinfo"]["langewebsitenaam"][$return["stap1"]["website"]];
		$return["stap1"]["website_specifiek"]["email"]=$vars["websiteinfo"]["email"][$return["stap1"]["website"]];
		$return["stap1"]["website_specifiek"]["basehref"]=$vars["websiteinfo"]["basehref"][$return["stap1"]["website"]];
		$return["stap1"]["website_specifiek"]["websitetype"]=$vars["websiteinfo"]["websitetype"][$return["stap1"]["website"]];
		$return["stap1"]["website_specifiek"]["verzekering_mogelijk"]=$vars["websiteinfo"]["verzekering_mogelijk"][$return["stap1"]["website"]];
		$return["stap1"]["website_specifiek"]["wederverkoop"]=$vars["websiteinfo"]["wederverkoop"][$return["stap1"]["website"]];
		$return["stap1"]["website_specifiek"]["websiteland"]=$vars["websiteinfo"]["websiteland"][$return["stap1"]["website"]];
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$return["stap1"]["website_specifiek"]["basehref"]=ereg_replace("https://www\.[a-z]+\.[a-z]+/","http://".$_SERVER["HTTP_HOST"]."/chalet/",$return["stap1"]["website_specifiek"]["basehref"]);
		}

		# Om te testen
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		#	$return["stap1"]["reisbureau_aanmaning_email_1"]="jeroen1@webtastic.nl";
		#	$return["stap1"]["reisbureau_aanmaning_email_2"]="jeroen2@webtastic.nl";
		}

		if($return["stap1"]["reisbureau_user_id"]) {
			$db->query("SELECT r.reisbureau_id, u.voornaam, u.tussenvoegsel, u.achternaam, u.code, r.naam, u.naam AS usernaam, u.email, r.email_facturen, r.bevestiging_naar_reisbureau, r.aanmaning_naar_reisbureau, r.verzendmethode_reisdocumenten, u.bevestiging_naar_gebruiker, u.aanmaning_naar_gebruiker, r.reserveringskosten, r.adres, r.postcode, r.plaats, r.land, r.aanpassing_commissie, r.btwnummer FROM reisbureau r, reisbureau_user u WHERE u.reisbureau_id=r.reisbureau_id AND u.user_id='".addslashes($return["stap1"]["reisbureau_user_id"])."';");
			if($db->next_record()) {
				$return["stap1"]["reisbureau_id"]=$db->f("reisbureau_id");
				$return["stap1"]["reisbureau_naam"]=$db->f("naam");
#				$return["stap1"]["reisbureau_usernaam"]=$db->f("usernaam");
				$return["stap1"]["reisbureau_usernaam"]=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
				$return["stap1"]["reisbureau_useremail"]=$db->f("email");
				$return["stap1"]["reisbureau_uservoornaam"]=$db->f("voornaam");
				$return["stap1"]["reisbureau_usertussenvoegsel"]=$db->f("tussenvoegsel");
				$return["stap1"]["reisbureau_userachternaam"]=$db->f("achternaam");
				$return["stap1"]["reisbureau_usercode"]=$db->f("code");
				$return["stap1"]["reisbureau_verzendmethode_reisdocumenten"]=$db->f("verzendmethode_reisdocumenten");

				if($db->f("bevestiging_naar_reisbureau") and $db->f("bevestiging_naar_gebruiker")) {
					# bevestiging naar reisbureau en gebruiker (agent)
					$return["stap1"]["reisbureau_bevestiging_email_1"]=$db->f("email");
					if($db->f("email_facturen")) {
						$return["stap1"]["reisbureau_bevestiging_email_2"]=$db->f("email_facturen");
					}
				} elseif($db->f("bevestiging_naar_reisbureau") and $db->f("email_facturen")) {
					# bevestiging naar reisbureau
					$return["stap1"]["reisbureau_bevestiging_email_1"]=$db->f("email_facturen");
				} else {
					# bevestiging naar gebruiker (agent)
					$return["stap1"]["reisbureau_bevestiging_email_1"]=$db->f("email");
				}
				# Dubbele mailadressen bevestiging voorkomen
				if($return["stap1"]["reisbureau_bevestiging_email_1"] and $return["stap1"]["reisbureau_bevestiging_email_2"]) {
					if($return["stap1"]["reisbureau_bevestiging_email_1"]==$return["stap1"]["reisbureau_bevestiging_email_2"]) {
						unset($return["stap1"]["reisbureau_bevestiging_email_2"]);
					}
				}

				if($db->f("aanmaning_naar_reisbureau") and $db->f("aanmaning_naar_gebruiker")) {
					# aanmaning naar reisbureau en gebruiker (agent)
					$return["stap1"]["reisbureau_aanmaning_email_1"]=$db->f("email");
					if($db->f("email_facturen")) {
						$return["stap1"]["reisbureau_aanmaning_email_2"]=$db->f("email_facturen");
					}
				} elseif($db->f("aanmaning_naar_reisbureau") and $db->f("email_facturen")) {
					# aanmaning naar reisbureau
					$return["stap1"]["reisbureau_aanmaning_email_1"]=$db->f("email_facturen");
				} else {
					# aanmaning naar gebruiker (agent)
					$return["stap1"]["reisbureau_aanmaning_email_1"]=$db->f("email");
				}

				# Dubbele mailadressen aanmaning voorkomen
				if($return["stap1"]["reisbureau_aanmaning_email_1"] and $return["stap1"]["reisbureau_aanmaning_email_2"]) {
					if($return["stap1"]["reisbureau_aanmaning_email_1"]==$return["stap1"]["reisbureau_aanmaning_email_2"]) {
						unset($return["stap1"]["reisbureau_aanmaning_email_2"]);
					}
				}
				$return["stap1"]["reisbureau_adres"]=$db->f("adres");
				$return["stap1"]["reisbureau_postcode"]=$db->f("postcode");
				$return["stap1"]["reisbureau_plaats"]=$db->f("plaats");
				$return["stap1"]["reisbureau_land"]=$db->f("land");
				$return["stap1"]["reisbureau_aanpassing_commissie"]=$db->f("aanpassing_commissie");
				$return["stap1"]["reisbureau_reserveringskosten"]=$db->f("reserveringskosten");
				$return["stap1"]["reisbureau_btwnummer"]=$db->f("btwnummer");

				if($commissie_opnieuw_opgehaald) {
					$return["stap1"]["commissie"]=$return["stap1"]["commissie"]+$db->f("aanpassing_commissie");
				}
			}
		}
	}
	if($return["stap1"]["zit_in_beheersysteem"]>(time()-1200)) {
		$return["stap1"]["zit_in_beheersysteem_melding"]="<span class=\"error\"><b>Let op bij het aanbrengen van wijzigingen bij deze boeking: de boeker is momenteel ingelogd in &quot;Mijn boeking&quot; (".date("H:i",$return["stap1"]["zit_in_beheersysteem"])."u.).</b></span>";
	}

	# Persoonlijke gegevens uit database halen
	$db->query("SELECT persoonnummer, status, voornaam, tussenvoegsel, achternaam, adres, postcode, plaats, land, telefoonnummer, mobielwerk, email, geboortedatum, geslacht, annverz, annverz_voorheen, annverz_verzekerdbedrag FROM boeking_persoon WHERE boeking_id='".addslashes($boekingid)."' ORDER BY status, persoonnummer;");
	while($db->next_record()) {
		if($db->f("persoonnummer")==1) {
			# Hoofdboeker
			$return["stap2"][$db->f("status")]["voornaam"]=$db->f("voornaam");
			$return["stap2"][$db->f("status")]["tussenvoegsel"]=$db->f("tussenvoegsel");
			$return["stap2"][$db->f("status")]["achternaam"]=$db->f("achternaam");
			$return["stap2"][$db->f("status")]["adres"]=$db->f("adres");
			$return["stap2"][$db->f("status")]["postcode"]=$db->f("postcode");
			$return["stap2"][$db->f("status")]["plaats"]=$db->f("plaats");
			$return["stap2"][$db->f("status")]["land"]=$db->f("land");
			$return["stap2"][$db->f("status")]["telefoonnummer"]=$db->f("telefoonnummer");
			$return["stap2"][$db->f("status")]["mobielwerk"]=$db->f("mobielwerk");
			if($return["stap1"]["reisbureau_useremail"]) {
				$return["stap2"][$db->f("status")]["email"]=$return["stap1"]["reisbureau_useremail"];
			} else {
				$return["stap2"][$db->f("status")]["email"]=$db->f("email");
			}
			$return["stap2"][$db->f("status")]["geslacht"]=$db->f("geslacht");
			$return["stap2"][$db->f("status")]["geboortedatum"]=$db->f("geboortedatum");
		}
		# Alle personen
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["voornaam"]=$db->f("voornaam");
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["tussenvoegsel"]=$db->f("tussenvoegsel");
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["achternaam"]=$db->f("achternaam");
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["plaats"]=$db->f("plaats");
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["land"]=$db->f("land");
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["geslacht"]=$db->f("geslacht");
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["geboortedatum"]=$db->f("geboortedatum");

		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["annverz"]=$db->f("annverz");
		if($db->f("annverz")) {
			$return["stap1"]["annverz_aantalpersonen"]++;
			$return["stap1"]["heeft_verzekering_per_persoon"][$db->f("persoonnummer")]=true;
		}
		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["annverz_voorheen"]=$db->f("annverz_voorheen");

		$return["stap3"][$db->f("status")][$db->f("persoonnummer")]["annverz_verzekerdbedrag"]=$db->f("annverz_verzekerdbedrag");

		if(isset($return["stap3"][$db->f("status")][$db->f("persoonnummer")]["geboortedatum"])) {
			$return["stap3"][$db->f("status")]["geboortedatum_ingevuld"][$db->f("persoonnummer")]=true;
			if($return["stap3"][$db->f("status")][$db->f("persoonnummer")]["voornaam"] and $return["stap3"][$db->f("status")][$db->f("persoonnummer")]["achternaam"]) {
				$return["stap3"][$db->f("status")]["ingevuld"][$db->f("persoonnummer")]=true;
			}
		}
	}

	# Geselecteerde opties uit database halen
	$db->query("SELECT s.optie_soort_id, s.annuleringsverzekering, s.reisverzekering, s.persoonsgegevensgewenst, s.algemeneoptie, s.optiecategorie, o.optie_onderdeel_id, oo.naam".$return["stap1"]["website_specifiek"]["ttv"]." AS onderdeelnaam, oo.min_leeftijd, oo.max_leeftijd, oo.min_deelnemers, oo.arrangement_zonder_skipas, oo.bijkomendekosten_id, oo.hoort_bij_accommodatieinkoop, o.hoort_bij_accommodatieinkoop AS ohoort_bij_accommodatieinkoop, o.verkoop, o.commissie, o.persoonnummer, o.status, s.naam_enkelvoud".$return["stap1"]["website_specifiek"]["ttv"]." AS naam_enkelvoud FROM boeking_optie o, optie_soort s, optie_groep g, optie_onderdeel oo WHERE o.optie_onderdeel_id=oo.optie_onderdeel_id AND g.optie_soort_id=s.optie_soort_id AND oo.optie_groep_id=g.optie_groep_id AND o.boeking_id='".addslashes($boekingid)."' ORDER BY s.volgorde;");
	while($db->next_record()) {
		if($db->f("algemeneoptie")) {
			# Algemene opties

			$return["stap4"][$db->f("status")]["algemene_optie_zelfgekozen"][$db->f("optie_soort_id")]=true;

			$return["stap4"][$db->f("status")]["algemene_optie"]["soort"]["alg".$db->f("optie_soort_id")]=$db->f("naam_enkelvoud");
			$return["stap4"][$db->f("status")]["algemene_optie"]["naam"]["alg".$db->f("optie_soort_id")]=$db->f("onderdeelnaam");
			$return["stap4"][$db->f("status")]["algemene_optie"]["verkoop"]["alg".$db->f("optie_soort_id")]=$db->f("verkoop");
			if(!$db->f("ohoort_bij_accommodatieinkoop")) {
				$return["stap4"][$db->f("status")]["algemene_optie"]["optiecategorie"]["alg".$db->f("optie_soort_id")]=$db->f("optiecategorie");
			}

		#	$return["stap4"][$db->f("status")]["algemene_optie"]["toonnul"]["alg".$db->f("optie_soort_id")]=$db->f("toonnul");
		#	$return["stap4"][$db->f("status")]["algemene_optie"]["kortingscode"]["alg".$db->f("optie_soort_id")]=$db->f("kortingscode");
			$return["stap4"][$db->f("status")]["algemene_optie"]["bijkomendekosten_id"]["alg".$db->f("optie_soort_id")]=$db->f("bijkomendekosten_id");
			$return["stap4"][$db->f("status")]["algemene_optie"]["bewerkbaar"]["alg".$db->f("optie_soort_id")]=true;

			$return["stap4"][$db->f("status")]["algemene_optie"]["optie_onderdeel_id"]["alg".$db->f("optie_soort_id")]=$db->f("optie_onderdeel_id");
			$return["stap4"][$db->f("status")]["algemene_optie"]["naam_op_onderdeel_id"][$db->f("optie_onderdeel_id")]=$db->f("naam_enkelvoud").": ".$db->f("onderdeelnaam");
			$return["stap4"][$db->f("status")]["algemene_optie"]["verkoop_op_onderdeel_id"][$db->f("optie_onderdeel_id")]=$db->f("verkoop");

#			$return["stap4"][$db->f("status")]["algemene_optie"]["hoort_bij_accommodatieinkoop"][$db->f("optie_onderdeel_id")]=$db->f("hoort_bij_accommodatieinkoop");

			$return["stap4"][$db->f("status")]["opties_totaalprijs"]+=$db->f("verkoop");
			$return["stap4"][$db->f("status")]["optie_bedrag_binnen_annuleringsverzekering"]+=$db->f("verkoop");
			for($j=1;$j<=$return["stap1"]["aantalpersonen"];$j++) {
				$return["stap4"][$db->f("status")][$j]["annverz_verzekerdbedrag_actueel"]+=round($db->f("verkoop")/$return["stap1"]["aantalpersonen"],2);

				if($db->f("persoonsgegevensgewenst")) {
					$return["stap4"][$db->f("status")]["persoonsgegevensgewenst"][$j]=true;
				}
				if($db->f("arrangement_zonder_skipas")) {
					$return["stap4"][$db->f("status")]["arrangement_zonder_skipas"][$j]=true;
				}
			}

			# Commmissie bij algemene opties
			$return["stap4"][$db->f("status")]["commissie_opties_totaalbedrag"]+=($db->f("commissie")/100)*$db->f("verkoop");
			$return["stap4"][$db->f("status")]["opties_commissie_precentages"][$db->f("commissie")]=true;

		} else {

			if($db->f("persoonsgegevensgewenst")) {
				$return["stap4"][$db->f("status")]["persoonsgegevensgewenst"][$db->f("persoonnummer")]=true;
			}
			if($db->f("arrangement_zonder_skipas")) {
				$return["stap4"][$db->f("status")]["arrangement_zonder_skipas"][$db->f("persoonnummer")]=true;
			}

			# Bijkomende kosten?
			if($db->f("bijkomendekosten_id")) {
				if($return["stap4"][$db->f("status")]["bijkomendekosten"][$db->f("bijkomendekosten_id")]) {
					$return["stap4"][$db->f("status")]["bijkomendekosten"][$db->f("bijkomendekosten_id")].=",".$db->f("persoonnummer");
				} else {
					$return["stap4"][$db->f("status")]["bijkomendekosten"][$db->f("bijkomendekosten_id")]=$db->f("persoonnummer");
				}
			}

			$return["stap4"][$db->f("status")]["opties"][$db->f("persoonnummer")][$db->f("optie_soort_id")]=$db->f("optie_onderdeel_id");
			$return["stap4"][$db->f("status")]["optie_onderdeelid"][$db->f("persoonnummer")][$db->f("optie_onderdeel_id")]=true;
			$return["stap4"][$db->f("status")]["optie_onderdeelid_minleeftijd"][$db->f("optie_onderdeel_id")]=$db->f("min_leeftijd");
			$return["stap4"][$db->f("status")]["optie_onderdeelid_maxleeftijd"][$db->f("optie_onderdeel_id")]=$db->f("max_leeftijd");
			$return["stap4"][$db->f("status")]["optie_onderdeelid_mindeelnemers"][$db->f("optie_onderdeel_id")]=$db->f("min_deelnemers");

			if($db->f("hoort_bij_accommodatieinkoop")) {
				$return["stap4"][$db->f("status")]["optie_hoort_bij_accommodatieinkoop"][$db->f("optie_onderdeel_id")]=true;
				$return["stap4"][$db->f("status")]["optie_hoort_bij_accommodatieinkoop_aantal"][$db->f("optie_onderdeel_id")]++;
			}

			$return["stap4"][$db->f("status")]["optie_onderdeelid_teller"][$db->f("optie_onderdeel_id").$db->f("verkoop")]++;
			$return["stap4"][$db->f("status")]["optie_onderdeelid_verkoop_key"][$db->f("optie_onderdeel_id").$db->f("verkoop")]=$db->f("optie_onderdeel_id");
			$return["stap4"][$db->f("status")]["optie_onderdeelid_verkoop_key_verkoop"][$db->f("optie_onderdeel_id").$db->f("verkoop")]=$db->f("verkoop");

			$return["stap4"][$db->f("status")]["optie_annuleringsverzekering"][$db->f("optie_onderdeel_id")]=$db->f("annuleringsverzekering");
			if(!$db->f("ohoort_bij_accommodatieinkoop")) {
				$return["stap4"][$db->f("status")]["optie_onderdeelid_optiecategorie"][$db->f("optie_onderdeel_id")]=$db->f("optiecategorie");
			}

			# Commmissie bij opties
			$return["stap4"][$db->f("status")]["commissie_opties_totaalbedrag"]+=($db->f("commissie")/100)*$db->f("verkoop");
			$return["stap4"][$db->f("status")]["opties_commissie_precentages"][$db->f("commissie")]=true;
			$return["stap4"][$db->f("status")]["optie_onderdeelid_commissie_persoonnummer"][$db->f("optie_onderdeel_id")][$db->f("persoonnummer")]=$db->f("commissie");

			if($db->f("annuleringsverzekering")) {
				$return["stap4"][$db->f("status")]["optie_bedrag_binnen_annuleringsverzekering"]+=$db->f("verkoop");
				$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["annverz_verzekerdbedrag_actueel"]+=$db->f("verkoop");
			} elseif(!$db->f("reisverzekering")) {
				$return["stap4"][$db->f("status")]["optie_bedrag_buiten_annuleringsverzekering"]+=$db->f("verkoop");
			}
			$return["stap4"][$db->f("status")]["optie_onderdeelid_naam"][$db->f("optie_onderdeel_id")]=$db->f("naam_enkelvoud").": ".$db->f("onderdeelnaam");
			$return["stap4"][$db->f("status")]["optie_onderdeelid_verkoop_persoonnummer"][$db->f("optie_onderdeel_id")][$db->f("persoonnummer")]=$db->f("verkoop");
			$return["stap4"][$db->f("status")]["opties_namen"][$db->f("naam_enkelvoud")]=true;
			$return["stap4"][$db->f("status")]["opties_totaalprijs"]+=$db->f("verkoop");

			if($db->f("reisverzekering")) {

				// make sure clients that already got a reisverzekering, can still make changes
				$vars["reisverzekering_mogelijk"]=1;

				$return["stap4"][$db->f("status")]["reisverzekering"]=true;

				$return["stap1"]["heeft_verzekering_per_persoon"][$db->f("persoonnummer")]=true;
				$return["stap4"][$db->f("status")]["reisverzekering_per_persoon"][$db->f("persoonnummer")]=true;
				$return["stap4"][$db->f("status")]["optie_onderdeelid_reisverzekering"][$db->f("optie_onderdeel_id")]=true;
				$return["stap4"][$db->f("status")]["persoonsgegevensgewenst"][$db->f("persoonnummer")]=true;
			}
			$return["stap4"][$db->f("status")]["verkoop_optie_onderdeelid"][$db->f("optie_onderdeel_id")]=$db->f("verkoop");

			$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["opties_perpersoon"][]=$db->f("naam_enkelvoud").": ".$db->f("onderdeelnaam");
			$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["optiesoort_perpersoon"][]=$db->f("naam_enkelvoud");
			$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["optieonderdeel_perpersoon"][]=$db->f("onderdeelnaam");
			$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["optieonderdeel_verkoop"][]=$db->f("verkoop");
			$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["optieonderdeel_id"][]=$db->f("optie_onderdeel_id");
			$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["optieonderdeel_reisverzekering"][]=$db->f("reisverzekering");
			$return["stap4"][$db->f("status")][$db->f("persoonnummer")]["optieonderdeel_extra"][]=false;
			if($db->f("verkoop")<0) $return["stap4"][$db->f("status")]["korting"]=true;
		}
	}
	if($return["stap4"][2]) $doorloop_status=2; else $doorloop_status=1;

	# Handmatige opties
	$db->query("SELECT extra_optie_id, soort, naam, verkoop, inkoop, korting, commissie, persoonnummer, deelnemers, toonnul, persoonsgegevensgewenst, kortingscode, bijkomendekosten_id, hoort_bij_accommodatieinkoop, skipas_id, optieleverancier_id, optiecategorie FROM extra_optie WHERE boeking_id='".addslashes($boekingid)."' AND verberg_voor_klant=0 ORDER BY soort, naam;");
	if($db->num_rows()) {
		for($i=1;$i<=$doorloop_status;$i++) {
			$db->seek();
			while($db->next_record()) {
				$return["stap4"][$i]["extra_opties"]=true;
				if($db->f("verkoop")<0) $return["stap4"][$i]["korting"]=true;
				if($db->f("persoonnummer")=="alg") {
					$return["stap4"][$i]["algemene_optie"]["soort"][$db->f("extra_optie_id")]=$db->f("soort");
					$return["stap4"][$i]["algemene_optie"]["naam"][$db->f("extra_optie_id")]=$db->f("naam");
					$return["stap4"][$i]["algemene_optie"]["toonnul"][$db->f("extra_optie_id")]=$db->f("toonnul");
					$return["stap4"][$i]["algemene_optie"]["kortingscode"][$db->f("extra_optie_id")]=$db->f("kortingscode");
					$return["stap4"][$i]["algemene_optie"]["bijkomendekosten_id"][$db->f("extra_optie_id")]=$db->f("bijkomendekosten_id");
					if(!$db->f("hoort_bij_accommodatieinkoop")) {
						$return["stap4"][$i]["algemene_optie"]["optiecategorie"][$db->f("extra_optie_id")]=$db->f("optiecategorie");
					}

					# algemene optie - financiele gegevens
					$return["stap4"][$i]["algemene_optie"]["verkoop"][$db->f("extra_optie_id")]=$db->f("verkoop");
					$return["stap4"][$i]["algemene_optie"]["inkoop"][$db->f("extra_optie_id")]=$db->f("inkoop");
					$return["stap4"][$i]["algemene_optie"]["korting"][$db->f("extra_optie_id")]=$db->f("korting");
					$return["stap4"][$i]["algemene_optie"]["commissie"][$db->f("extra_optie_id")]=$db->f("commissie");
					if(!$db->f("skipas_id") and !$db->f("optieleverancier_id")) {
						if($db->f("hoort_bij_accommodatieinkoop")) {
							$return["stap4"][$i]["algemene_optie"]["hoort_bij_accommodatieinkoop"][$db->f("extra_optie_id")]=$db->f("hoort_bij_accommodatieinkoop");
						} else {
							$return["stap4"][$i]["algemene_optie"]["hoort_niet_bij_accommodatieinkoop"][$db->f("extra_optie_id")]=$db->f("hoort_bij_accommodatieinkoop");
						}
					}

					$return["stap4"][$i]["opties_totaalprijs"]+=$db->f("verkoop");
					$return["stap4"][$i]["optie_bedrag_binnen_annuleringsverzekering"]+=$db->f("verkoop");
					for($j=1;$j<=$return["stap1"]["aantalpersonen"];$j++) {
						$return["stap4"][$i][$j]["annverz_verzekerdbedrag_actueel"]+=round($db->f("verkoop")/$return["stap1"]["aantalpersonen"],2);
					}

					# Commmissie bij handmatige opties
					$return["stap4"][$i]["commissie_opties_totaalbedrag"]+=($db->f("commissie")/100)*$db->f("verkoop");
					$return["stap4"][$i]["opties_commissie_precentages"][$db->f("commissie")]=true;

				} else {

					$return["stap4"][$i]["optie_onderdeelid_verkoop_key"]["eo".$db->f("extra_optie_id")]="eo".$db->f("extra_optie_id");
					$return["stap4"][$i]["optie_onderdeelid_verkoop_key_verkoop"]["eo".$db->f("extra_optie_id")]=$db->f("verkoop");

					$return["stap4"][$i]["optie_onderdeelid_naam"]["eo".$db->f("extra_optie_id")]=($db->f("soort") ? $db->f("soort").": " : "").$db->f("naam");
					$return["stap4"][$i]["optie_onderdeelid_toonnul"]["eo".$db->f("extra_optie_id")]=$db->f("toonnul");
					if(!$db->f("hoort_bij_accommodatieinkoop")) {
						$return["stap4"][$i]["optie_onderdeelid_optiecategorie"]["eo".$db->f("extra_optie_id")]=$db->f("optiecategorie");
					}
					$return["stap4"][$i]["verkoop_optie_onderdeelid"]["eo".$db->f("extra_optie_id")]=$db->f("verkoop");
					if($db->f("persoonnummer")=="pers") {


						$tempdeelnemers=@split(",",$db->f("deelnemers"));
						while(list($key,$value)=@each($tempdeelnemers)) {
							if($value) {

								# persoonsoptie - financiele gegevens
								$return["stap4"][$i]["persoonsoptie_fin"]["naam"][$db->f("extra_optie_id")]=$db->f("naam");
								$return["stap4"][$i]["persoonsoptie_fin"]["verkoop"][$db->f("extra_optie_id")]+=$db->f("verkoop");
								$return["stap4"][$i]["persoonsoptie_fin"]["inkoop"][$db->f("extra_optie_id")]+=$db->f("inkoop");
								$return["stap4"][$i]["persoonsoptie_fin"]["korting"][$db->f("extra_optie_id")]=$db->f("korting");
								$return["stap4"][$i]["persoonsoptie_fin"]["commissie"][$db->f("extra_optie_id")]=$db->f("commissie");
								$return["stap4"][$i]["persoonsoptie_fin"]["aantal"][$db->f("extra_optie_id")]++;
								if(!$db->f("skipas_id") and !$db->f("optieleverancier_id")) {
									if($db->f("hoort_bij_accommodatieinkoop")) {
										$return["stap4"][$i]["persoonsoptie_fin"]["hoort_bij_accommodatieinkoop"][$db->f("extra_optie_id")]=$db->f("hoort_bij_accommodatieinkoop");
									} else {
										$return["stap4"][$i]["persoonsoptie_fin"]["hoort_niet_bij_accommodatieinkoop"][$db->f("extra_optie_id")]=$db->f("hoort_bij_accommodatieinkoop");
									}
								}

								if($db->f("persoonsgegevensgewenst")) {
									$return["stap4"][$i]["persoonsgegevensgewenst"][$value]=true;
								}

								$return["stap4"][$i]["optie_onderdeelid_teller"]["eo".$db->f("extra_optie_id")]++;
								$return["stap4"][$i]["opties_totaalprijs"]+=$db->f("verkoop");
								$return["stap4"][$i]["optie_bedrag_binnen_annuleringsverzekering"]+=$db->f("verkoop");

								$return["stap4"][$i][$value]["annverz_verzekerdbedrag_actueel"]+=$db->f("verkoop");
								$return["stap4"][$i][$value]["optiesoort_perpersoon"][]=$db->f("soort");
								$return["stap4"][$i][$value]["optieonderdeel_perpersoon"][]=$db->f("naam");
								$return["stap4"][$i][$value]["optieonderdeel_verkoop"][]=$db->f("verkoop");
								$return["stap4"][$i][$value]["optieonderdeel_extra"][]=true;
								$return["stap4"][$i][$value]["extra_opties"]["soort"][$db->f("extra_optie_id")]=$db->f("soort");
								$return["stap4"][$i][$value]["extra_opties"]["bijkomendekosten_id"][$db->f("extra_optie_id")]=$db->f("bijkomendekosten_id");
								$return["stap4"][$i][$value]["extra_opties"]["naam"][$db->f("extra_optie_id")]=$db->f("naam");
								$return["stap4"][$i][$value]["extra_opties"]["verkoop"][$db->f("extra_optie_id")]=$db->f("verkoop");

								# Commmissie bij handmatige opties
								$return["stap4"][$i]["commissie_opties_totaalbedrag"]+=($db->f("commissie")/100)*$db->f("verkoop");
								$return["stap4"][$i]["opties_commissie_precentages"][$db->f("commissie")]=true;
							}
						}
					}
				}
			}
		}
	}

	# Kijken of er een skipas aan deze boeking is gekoppeld (toonper=1 of toonper=2, geen wederverkoop)
#	if(($accinfo["toonper"]==1 or $accinfo["toonper"]==2) and !$return["stap1"]["wederverkoop"]) {
#		# Ja, dus: persoonsgegevensgewenst
#		$return["stap1"]["persoonsgegevensgewenst"]=true;
#	}


	# Annuleringsverzekering berekenen (voor status 1 en 2)
	for($i=1;$i<=$doorloop_status;$i++) {
		for($j=1;$j<=$return["stap1"]["aantalpersonen"];$j++) {
			if($accinfo["toonper"]==3 or $return["stap1"]["wederverkoop"]) {
				$return["stap4"][$i][$j]["annverz_verzekerdbedrag_actueel"]+=round($return["stap1"]["verkoop"]/$return["stap1"]["aantalpersonen"],2);
			} else {
				$return["stap4"][$i][$j]["annverz_verzekerdbedrag_actueel"]+=$return["stap1"]["verkoop"];
			}
			if($return["stap3"][2][$j]["annverz"]) {
				$return["stap4"][$i]["annuleringsverzekering"]=true;
#				$return["stap4"][$i][$j]["annverz_persoon"]=round($return["stap3"][2][$j]["annverz_verzekerdbedrag"]*($return["stap1"]["annuleringsverzekering_percentage_".$return["stap3"][2][$j]["annverz"]]/100),2);
#echo $return["stap3"][2][$j]["annverz_verzekerdbedrag"]."%<br>";
				$return["stap4"][$i][$j]["annverz_persoon"]=$return["stap3"][2][$j]["annverz_verzekerdbedrag"]*($return["stap1"]["annuleringsverzekering_percentage_".$return["stap3"][2][$j]["annverz"]]/100);
#				echo round(($return["stap1"]["annuleringsverzekering_percentage_".$return["stap3"][2][$j]["annverz"]]/100),2)." * ".$return["stap3"][2][$j]["annverz_verzekerdbedrag"]." = ".$return["stap4"][$i][$j]["annverz_persoon"]."<br>";
				$return["stap4"][$i]["annuleringsverzekering_soorten"][$return["stap3"][2][$j]["annverz"]]=true;
				$return["stap4"][$i]["annuleringsverzekering_bedragen"][$return["stap3"][2][$j]["annverz"]]+=$return["stap3"][2][$j]["annverz_verzekerdbedrag"];
				$return["stap4"][$i]["totaal_annverz"]+=$return["stap4"][$i][$j]["annverz_persoon"];
#echo $i." ".$return["stap4"][$i][$j]["annverz_persoon"]."<br>";
				$return["stap4"][$i]["totaal_annverz_".$return["stap3"][2][$j]["annverz"]]+=$return["stap4"][$i][$j]["annverz_persoon"];
			}
		}
	}

	# financieel berekenen (voor status 1 en 2)
	for($i=1;$i<=$doorloop_status;$i++) {
		$return["fin"][$i]["accommodatie_verkoop"]=$return["stap1"]["verkoop"];
		if($accinfo["toonper"]==3 or $return["stap1"]["wederverkoop"]) {
			$return["fin"][$i]["accommodatie_totaalprijs"]=$return["stap1"]["verkoop"];
		} else {
			$return["fin"][$i]["accommodatie_totaalprijs"]=$return["stap1"]["verkoop"]*$return["stap1"]["aantalpersonen"];
		}
		if(is_array($return["stap4"][$i]["opties"]) or $return["stap4"][$i]["extra_opties"] or $return["stap4"][$i]["algemene_optie_zelfgekozen"]) {
			$return["fin"][$i]["opties"]=true;
			$return["fin"][$i]["opties_totaalprijs"]=$return["stap4"][$i]["opties_totaalprijs"];
			if($return["stap1"]["reisbureau_user_id"]) {
				$return["fin"][$i]["commissie_opties"]=$return["stap4"][$i]["commissie_opties_totaalbedrag"];
			}
		}

		# Commissie
		if($return["stap1"]["reisbureau_user_id"]) {
			$return["fin"][$i]["commissie_accommodatie"]=round(($return["stap1"]["commissie"]/100)*$return["fin"][$i]["accommodatie_totaalprijs"],2);
		}

		# Annuleringsverzekering
#		if($return["stap1"]["annuleringsverzekering"]) {
#			$return["fin"][$i]["binnen_annuleringsverzekering"]=$return["fin"][$i]["accommodatie_totaalprijs"]+$return["stap4"][$i]["optie_bedrag_binnen_annuleringsverzekering"]-$return["stap1"]["annuleringsverzekering_korting"];
#			$return["fin"][$i]["annuleringsverzekering_percentage"]=round($return["fin"][$i]["binnen_annuleringsverzekering"]*($return["stap1"]["annuleringsverzekering_percentage"]/100),2);
#			$return["fin"][$i]["annuleringsverzekering_poliskosten"]=$return["stap1"]["annuleringsverzekering_poliskosten"];
#		}

		if($return["stap1"]["annverz_aantalpersonen"]) {
			$return["fin"][$i]["annuleringsverzekering_variabel_1"]=$return["stap4"][$i]["totaal_annverz_1"];
			$return["fin"][$i]["annuleringsverzekering_variabel_2"]=$return["stap4"][$i]["totaal_annverz_2"];
			$return["fin"][$i]["annuleringsverzekering_variabel_3"]=$return["stap4"][$i]["totaal_annverz_3"];
			$return["fin"][$i]["annuleringsverzekering_variabel_4"]=$return["stap4"][$i]["totaal_annverz_4"];

			$return["fin"][$i]["annuleringsverzekering_variabel"]=$return["stap4"][$i]["totaal_annverz"];
			if($return["stap1"]["annuleringsverzekering_poliskosten"]) {
				$return["fin"][$i]["annuleringsverzekering_poliskosten"]=$return["stap1"]["annuleringsverzekering_poliskosten"];
			}
		}

		# Schadeverzekering
		if($return["stap1"]["schadeverzekering"]) {
			$return["fin"][$i]["schadeverzekering_variabel"]=round($return["stap1"]["accprijs"]*($return["stap1"]["schadeverzekering_percentage"]/100),2);
		}

		# Reisverzekering poliskosten (geldt alleen voor oude boekingen)
		if($return["stap4"][$i]["reisverzekering"]) {
			$return["fin"][$i]["reisverzekering_poliskosten"]=$return["stap1"]["reisverzekering_poliskosten"];
		}

		# Poliskosten alle verzekeringen samen
		if($return["stap1"]["heeft_verzekering_per_persoon"] or $return["stap1"]["schadeverzekering"]) {
			$return["fin"][$i]["verzekeringen_poliskosten"]=$return["stap1"]["verzekeringen_poliskosten"];
		}

		# Reserveringskosten
		$return["fin"][$i]["reserveringskosten"]=$return["stap1"]["reserveringskosten"];

		# Subtotaal
		$return["fin"][$i]["subtotaal"]=$return["fin"][$i]["accommodatie_totaalprijs"]+$return["fin"][$i]["opties_totaalprijs"];

		# Verzekerd bedrag mag niet hoger zijn dan subtotaal
		$temp_subtotaal=$return["fin"][$i]["accommodatie_totaalprijs"]+$return["stap4"][$i]["optie_bedrag_binnen_annuleringsverzekering"]+$return["stap4"][$i]["optie_bedrag_buiten_annuleringsverzekering"];
#		if($return["stap4"][$i]["annuleringsverzekering_bedragen"][1]>$temp_subtotaal) $return["stap4"][$i]["annuleringsverzekering_bedragen"][1]=$temp_subtotaal;
#		if($return["stap4"][$i]["annuleringsverzekering_bedragen"][2]>$temp_subtotaal) $return["stap4"][$i]["annuleringsverzekering_bedragen"][2]=$temp_subtotaal;
#		if($return["stap4"][$i]["annuleringsverzekering_bedragen"][3]>$temp_subtotaal) $return["stap4"][$i]["annuleringsverzekering_bedragen"][3]=$temp_subtotaal;
#		if($return["stap4"][$i]["annuleringsverzekering_bedragen"][4]>$temp_subtotaal) $return["stap4"][$i]["annuleringsverzekering_bedragen"][4]=$temp_subtotaal;

		# Totaalbedrag commissie
		$return["fin"][$i]["commissie_opties"]=round($return["fin"][$i]["commissie_opties"],2);
		$return["fin"][$i]["commissie_totaal"]=$return["fin"][$i]["commissie_accommodatie"]+$return["fin"][$i]["commissie_opties"];

		# BTW over commissie berekenen
		if($return["stap1"]["btw_over_commissie"] and $return["stap1"]["btw_over_commissie_percentage"]>0) {
			$return["fin"][$i]["commissie_btw"]=round($return["fin"][$i]["commissie_totaal"]*($return["stap1"]["btw_over_commissie_percentage"]/100),2);
			$return["fin"][$i]["commissie_totaal"]=$return["fin"][$i]["commissie_totaal"]+$return["fin"][$i]["commissie_btw"];
		}

		# Totale reissom
#		$return["fin"][$i]["totale_reissom"]=$return["fin"][$i]["accommodatie_totaalprijs"]+$return["fin"][$i]["opties_totaalprijs"]+$return["fin"][$i]["annuleringsverzekering_percentage"]+$return["fin"][$i]["annuleringsverzekering_poliskosten"]+$return["fin"][$i]["reisverzekering_poliskosten"]+$return["fin"][$i]["reserveringskosten"];
		$return["fin"][$i]["totale_reissom"]=round($return["fin"][$i]["accommodatie_totaalprijs"]+$return["fin"][$i]["opties_totaalprijs"]+$return["fin"][$i]["annuleringsverzekering_variabel"]+$return["fin"][$i]["schadeverzekering_variabel"]+$return["fin"][$i]["annuleringsverzekering_poliskosten"]+$return["fin"][$i]["reisverzekering_poliskosten"]+$return["fin"][$i]["verzekeringen_poliskosten"]+$return["fin"][$i]["reserveringskosten"]-$return["fin"][$i]["commissie_totaal"],2);


		$return["fin"][$i]["totale_reissom_zonder_commissie_aftrek"]=$return["fin"][$i]["totale_reissom"]+$return["fin"][$i]["commissie_totaal"];

#echo $return["fin"][$i]["opties_totaalprijs"];
#exit;
		# Aanbetaling
#		$aanbetaling=$accinfo["maxaantalpersonen"]*100;
#		if($aanbetaling>$return["fin"][$i]["totale_reissom"]) {
#			$aanbetaling=$return["fin"][$i]["totale_reissom"];
#		}
#		if($aanbetaling<($return["fin"][$i]["subtotaal"]*.3)) {
			$aanbetaling=round($return["fin"][$i]["subtotaal"]*.3);
#		}

#		$aanbetaling+=$return["fin"][$i]["annuleringsverzekering_percentage"]+$return["fin"][$i]["annuleringsverzekering_poliskosten"]+$return["fin"][$i]["reserveringskosten"];
		$aanbetaling+=$return["fin"][$i]["annuleringsverzekering_variabel"]+$return["fin"][$i]["annuleringsverzekering_poliskosten"]+$return["fin"][$i]["verzekeringen_poliskosten"]+$return["fin"][$i]["reserveringskosten"];

		$return["fin"][$i]["aanbetaling_ongewijzigd"]=$aanbetaling;

		if(isset($return["stap1"]["aanbetaling1_gewijzigd"])) {
			$return["fin"][$i]["aanbetaling"]=$return["stap1"]["aanbetaling1_gewijzigd"];
		} elseif($return["stap1"]["aanbetaling1"]) {
			$return["fin"][$i]["aanbetaling"]=$return["stap1"]["aanbetaling1"];
		} else {
			$return["fin"][$i]["aanbetaling"]=$aanbetaling;
		}
		if($return["fin"][$i]["aanbetaling"]) {
			$return["fin"][$i]["aanbetaling"]=round($return["fin"][$i]["aanbetaling"],2);
		}
	}
	return $return;
}

function get_boekinginfo($boekingid) {
	$temp_gegevens=boekinginfo($boekingid);

	$return["stap1"]=$temp_gegevens["stap1"];

	# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
	if($temp_gegevens["stap2"][2]) {
		$return["stap2"]=$temp_gegevens["stap2"][2];
	} elseif($temp_gegevens["stap2"][1]) {
		$return["stap2"]=$temp_gegevens["stap2"][1];
	}

	# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
	if($temp_gegevens["stap3"][2][2]) {
		$return["stap3"]=$temp_gegevens["stap3"][2];
	} elseif($temp_gegevens["stap3"][1][2]) {
		$return["stap3"]=$temp_gegevens["stap3"][1];
	}

	# Controle op status Geselecteerde opties (2 heeft voorkeur boven 1)
	if($temp_gegevens["stap4"][2]) {
		$return["stap4"]=$temp_gegevens["stap4"][2];
		$return["stap4"]["actieve_status"]=2;
		$return["fin"]=$temp_gegevens["fin"][2];
	} else {
		$return["stap4"]=$temp_gegevens["stap4"][1];
		$return["stap4"]["actieve_status"]=1;
		$return["fin"]=$temp_gegevens["fin"][1];
	}
	$return["stap5"]=$temp_gegevens["stap5"];
	return $return;
}

function bedrag_korting_tekst($bedrag,$korting_percentage1,$korting_percentage2,$korting_euro) {
	if($korting_percentage1<>0 or $korting_percentage2<>0 or $korting_euro<>0) {
		$return="€ ".number_format($bedrag,2,',','.');
		if($korting_percentage1<>0) {
			$korting_percentage1=floatval($korting_percentage1);
			$return.=" -".preg_replace("/\./",",",$korting_percentage1)."%";
		}
		if($korting_percentage2<>0) {
			$korting_percentage2=floatval($korting_percentage2);
			$return.=" -".preg_replace("/\./",",",$korting_percentage2)."%";
		}
		if($korting_euro>0) $return.=" - € ".number_format($korting_euro,2,',','.');
		if($korting_euro<0) $return.=" + € ".number_format(abs($korting_euro),2,',','.');
	} else {
		$return="nettoprijs";
	}
	return $return;
}

function reissom_tabel_korting_of_min_tekst($bedrag,$inkoop) {
	if($bedrag<0) {
		if($inkoop) {
			$return="-/-";
		} else {
			$return=html("korting","vars");
		}
	} else {
		$return="&nbsp;";
	}
	return $return;
}

function reissom_tabel($gegevens,$accinfo,$opties="",$inkoop=false) {
	global $vars, $isMobile;
	$return="";
	$db=new DB_sql;

	if($inkoop) {
		$extra_td="<td>&nbsp;</td>";
		$extra_colspan=1;
	} else {
		$extra_td="";
		$extra_colspan=0;
	}

	# Tarief accommodatie/arrangement
	if(!$isMobile) {
		$return.="<tr style=\"background-color:#ebebeb\"><td style=\"padding-right:10px;width:70%\">";

		if($accinfo["toonper"]==3 or $gegevens["stap1"]["wederverkoop"]) {
			$return.=html("accommodatie","vars");
		} else {
			$return.=html("accommodatieplusskipas","vars");
		}

		$return.="</td>".$extra_td."<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["accommodatie_verkoop"],2,',','.')."</td>";
		$return.="<td style=\"padding-right:10px;white-space:nowrap;\"> x ".($accinfo["toonper"]==3||$gegevens["stap1"]["wederverkoop"] ? "1" : $gegevens["stap1"]["aantalpersonen"])."</td><td style=\"padding-right:10px\">=</td>";
		$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["accommodatie_totaalprijs"],2,',','.')."</td><td>&nbsp;</td>";
		$return.="</tr>";
	} else {
		$return.="<tr class=\"mobile_row\" style=\"background-color:#ebebeb\"><td>";
		if($accinfo["toonper"]==3 or $gegevens["stap1"]["wederverkoop"]) {
			$return.=html("accommodatie","vars").":";
		} else {
			$return.=html("accommodatieplusskipas","vars").":";
		}
		$return.="<div class=\"costs\">&euro; ".number_format($gegevens["fin"]["accommodatie_verkoop"],2,',','.')."";
		$return.=" x ".($accinfo["toonper"]==3||$gegevens["stap1"]["wederverkoop"] ? "1" : $gegevens["stap1"]["aantalpersonen"])." = ";
		$return.="&euro; ".number_format($gegevens["fin"]["accommodatie_totaalprijs"],2,',','.')."</div></td>";
		$return.="</tr>";
	}

	if($inkoop) {
		$return_inkoop.="<tr style=\"background-color:#ebebeb\"><td style=\"padding-right:10px;width:70%\">Huurprijs accommodatie";
		$return_inkoop.="</td><td style=\"vertical-align:top;white-space:nowrap;\">(".bedrag_korting_tekst($gegevens["stap1"]["inkoopbruto"],$gegevens["stap1"]["inkoopcommissie"],$gegevens["stap1"]["inkoopkorting_percentage"],$gegevens["stap1"]["inkoopkorting"]+$gegevens["stap1"]["inkoopkorting_euro"]).")</td><td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["stap1"]["inkoopnetto"],2,',','.')."</td>";
		$return_inkoop.="<td style=\"padding-right:10px;white-space:nowrap;\"> x 1</td><td style=\"padding-right:10px\">=</td>";
		$return_inkoop.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["stap1"]["inkoopnetto"],2,',','.')."</td><td>&nbsp;</td>";
		$return_inkoop.="</tr>";
		$inkoop_totaal+=$gegevens["stap1"]["inkoopnetto"];
		if($accinfo["toonper"]==3 or $gegevens["stap1"]["wederverkoop"]) {

		} else {
			# Skipasgegevens

			# Kijken hoeveel deelnemers een afwijkende skipas hebben
			$afwijkende_skipas=0;

			# Gewone opties
			$db->query("SELECT COUNT(bo.persoonnummer) AS aantal FROM boeking_optie bo, optie_groep og, optie_onderdeel oo WHERE (oo.wederverkoop_skipas_id=0 OR oo.wederverkoop_skipas_id IS NULL) AND og.skipas_id>0 AND bo.status=".($gegevens["stap4"]["actieve_status"]==2 ? "2" : "1")." AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			if($db->next_record()) {
				$afwijkende_skipas+=$db->f("aantal");
			}
			# Handmatige opties
			$db->query("SELECT deelnemers FROM extra_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND skipas_id>0 AND verberg_voor_klant=0;");
			while($db->next_record()) {
				$tempdeelnemers=@split(",",$db->f("deelnemers"));
				while(list($key,$value)=@each($tempdeelnemers)) {
					$afwijkende_skipas++;
				}
			}
			if($afwijkende_skipas>$gegevens["stap1"]["aantalpersonen"]) $afwijkende_skipas=$gegevens["stap1"]["aantalpersonen"];

			$db->query("SELECT bruto, netto, korting, verkoopkorting, netto_ink FROM skipas_tarief WHERE skipas_id='".addslashes($gegevens["stap1"]["accinfo"]["skipasid"])."' AND week='".addslashes($gegevens["stap1"]["aankomstdatum"])."'");
			if($db->next_record()) {
				$kleurteller_inkoop++;
				if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
				if($db->f("netto_ink")>0) {
					$netto=$db->f("netto");
					$bruto=$db->f("bruto");
					$korting_percentage=0;
					$korting_euro=$db->f("bruto")-$db->f("netto_ink");
				} else {
					$netto=$db->f("netto");
					$bruto=$db->f("bruto");
					$korting_percentage=$db->f("korting");
					$korting_euro=$db->f("verkoopkorting");
				}
				$return_inkoop.="<tr style=\"background-color:#ffffff;\"><td style=\"padding-right:10px;width:70%\">".$gegevens["stap1"]["accinfo"]["skipas_aantaldagen"]."-daagse skipas ".wt_he($gegevens["stap1"]["accinfo"]["skipas_naam"]);
				$return_inkoop.="</td><td style=\"vertical-align:top;white-space:nowrap;\">(".bedrag_korting_tekst($bruto,$korting_percentage,0,$korting_euro).")</td><td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($netto,2,',','.')."</td>";
				$return_inkoop.="<td style=\"padding-right:10px;white-space:nowrap;\"> x ".($gegevens["stap1"]["aantalpersonen"]-$afwijkende_skipas)."</td><td style=\"padding-right:10px\">=</td>";
				$return_inkoop.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($netto*($gegevens["stap1"]["aantalpersonen"]-$afwijkende_skipas),2,',','.')."</td><td>&nbsp;</td>";
				$return_inkoop.="</tr>";
				$inkoop_totaal+=round($netto*($gegevens["stap1"]["aantalpersonen"]-$afwijkende_skipas),2);

				$inkoop_opties[3]+=round($netto*($gegevens["stap1"]["aantalpersonen"]-$afwijkende_skipas),2);
			} else {
#				trigger_error("geen skipastarief gevonden (skipasid ".$gegevens["stap1"]["accinfo"]["skipasid"].", week ".$gegevens["stap1"]["aankomstdatum"].", toonper:".$accinfo["toonper"],E_USER_NOTICE);
			}
		}
	}

	# Algemene opties
	while(list($key,$value)=@each($gegevens["stap4"]["algemene_optie"]["soort"])) {
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);
		$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(($value ? ucfirst($value).": " : "").$gegevens["stap4"]["algemene_optie"]["naam"][$key]);
		$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($gegevens["stap4"]["algemene_optie"]["verkoop"][$key]),2,',','.')."</td>";
		$return.="<td nowrap style=\"padding-right:10px;vertical-align:top;\"> x 1</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
		$return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($gegevens["stap4"]["algemene_optie"]["verkoop"][$key]),2,',','.')."</td>";
		$return.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($gegevens["stap4"]["algemene_optie"]["verkoop"][$key],$inkoop)."</td>";
		$return.="</tr>";

		# Inkoop
		if($inkoop) {
			$kleurteller_inkoop++;
			if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
			$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(($value ? ucfirst($value).": " : "").$gegevens["stap4"]["algemene_optie"]["naam"][$key]);
			$inkoopbedrag=$gegevens["stap4"]["algemene_optie"]["inkoop"][$key];
			$korting=$gegevens["stap4"]["algemene_optie"]["korting"][$key];
			$inkoop_netto=round($inkoopbedrag*(1-$korting/100),2);
			$return_inkoop.="</td><td style=\"vertical-align:top;\">(".bedrag_korting_tekst($inkoopbedrag,$korting,0,0).")</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;text-align:right;vertical-align:top;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
			$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x 1</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
			$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;text-align:right;vertical-align:top;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
			$return_inkoop.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($inkoop_netto,$inkoop)."</td>";
			$return_inkoop.="</tr>";
			$inkoop_totaal+=$inkoop_netto;
			if($gegevens["stap4"]["algemene_optie"]["optiecategorie"][$key]) {
				$inkoop_opties[$gegevens["stap4"]["algemene_optie"]["optiecategorie"][$key]]+=$inkoop_netto;
			}
		}
	}

	# Opties per persoon
	while(list($key,$value)=@each($gegevens["stap4"]["optie_onderdeelid_teller"])) {
		$bedrag=$gegevens["stap4"]["optie_onderdeelid_verkoop_key_verkoop"][$key];
		$key=$gegevens["stap4"]["optie_onderdeelid_verkoop_key"][$key];

		if($gegevens["stap4"]["optie_onderdeelid_reisverzekering"][$key]) {
			$reisverzekeringen["aantal"][$key.$bedrag]=$value;
			$reisverzekeringen["bedrag"][$key.$bedrag]=$bedrag;
			$reisverzekeringen["naam"][$key.$bedrag]=$gegevens["stap4"]["optie_onderdeelid_naam"][$key];
			$reisverzekeringen["optieonderdeelid"][$key.$bedrag]=$key;
#			$reisverzekering_value[]=$value;
		} else {
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);

			if(!$isMobile) {
				$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(ucfirst($gegevens["stap4"]["optie_onderdeelid_naam"][$key]));
				$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($bedrag),2,',','.')."</td>";
				$return.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".$value."</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
				$return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($bedrag*$value),2,',','.')."</td>";
				$return.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($bedrag,$inkoop)."</td>";
				$return.="</tr>";
			} else {
				$return.="<tr class=\"mobile_row\"".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : " style=\"background-color:#ffffff\"")."><td>".wt_he(ucfirst($gegevens["stap4"]["optie_onderdeelid_naam"][$key])).":";
				$return.="<div class=\"costs\">&euro; ".number_format(abs($bedrag),2,',','.');
				$return.=" x ".$value." = ";
				$return.=" &euro; ".number_format(abs($bedrag*$value),2,',','.');
				$return.=" ".trim(reissom_tabel_korting_of_min_tekst($bedrag,$inkoop),"&nbsp;")."</div></td>";
				$return.="</tr>";
			}


			# Inkoop
			if($inkoop) {
				if(preg_match("/^eo([0-9]+)$/",$key,$regs)) {
					# handmatige optie (extra_optie)
					$db->query("SELECT inkoop, korting, netto_inkoop, optiecategorie, hoort_bij_accommodatieinkoop FROM extra_optie WHERE extra_optie_id='".addslashes($regs[1])."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."'");
					if($db->next_record()) {
						$kleurteller_inkoop++;
						if($kleurteller_inkoop>1) unset($kleurteller_inkoop);

						if($db->f("netto_inkoop")>0) {
							# Indien "Netto inkoop (zelf invoeren)" is ingevuld in het optietarieven-CMS: alleen dit bedrag gebruiken (geen korting van toepassing)
							$inkoopbedrag=$db->f("netto_inkoop");
							$korting=0;
						} else {
							$inkoopbedrag=$db->f("inkoop");
							$korting=$db->f("korting");
						}
						$inkoop_netto=round($inkoopbedrag*(1-$korting/100),2);
						$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(ucfirst($gegevens["stap4"]["optie_onderdeelid_naam"][$key]));
						$return_inkoop.="</td><td style=\"vertical-align:top;\">(".bedrag_korting_tekst($inkoopbedrag,$korting,0,0).")</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
						$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".$value."</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
						$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto*$value),2,',','.')."</td>";
						$return_inkoop.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($inkoop_netto,$inkoop)."</td>";
						$return_inkoop.="</tr>";

						$inkoop_totaal+=$inkoop_netto*$value;
						if(!$db->f("hoort_bij_accommodatieinkoop")) {
							$inkoop_opties[$db->f("optiecategorie")]+=$inkoop_netto*$value;
						}
					} else {
						trigger_error("geen optietarief (extra_optie_id".$key.") gevonden",E_USER_NOTICE);
					}
				} else {
					$db->query("SELECT inkoop, korting, netto_ink FROM optie_tarief WHERE optie_onderdeel_id='".addslashes($key)."' AND week='".addslashes($gegevens["stap1"]["aankomstdatum"])."'");
					if($db->next_record()) {
						$kleurteller_inkoop++;
						if($kleurteller_inkoop>1) unset($kleurteller_inkoop);

						if($db->f("netto_ink")>0) {
							# Indien "Netto inkoop (zelf invoeren)" is ingevuld in het optietarieven-CMS: alleen dit bedrag gebruiken (geen korting van toepassing)
							$inkoopbedrag=$db->f("netto_ink");
							$korting=0;
						} else {
							$inkoopbedrag=$db->f("inkoop");
							$korting=$db->f("korting");
						}
						$inkoop_netto=round($inkoopbedrag*(1-$korting/100),2);

						$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(ucfirst($gegevens["stap4"]["optie_onderdeelid_naam"][$key]));
						$return_inkoop.="</td><td style=\"vertical-align:top;\">(".bedrag_korting_tekst($inkoopbedrag,$korting,0,0).")</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
						$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".$value."</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
						$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto*$value),2,',','.')."</td>";
						$return_inkoop.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($inkoop_netto,$inkoop)."</td>";
						$return_inkoop.="</tr>";

						$inkoop_totaal+=$inkoop_netto*$value;
						if($gegevens["stap4"]["optie_onderdeelid_optiecategorie"][$key]) {
							$inkoop_opties[$gegevens["stap4"]["optie_onderdeelid_optiecategorie"][$key]]+=$inkoop_netto*$value;
						}
					} else {
						trigger_error("geen optietarief (optie_id".$key.") gevonden",E_USER_NOTICE);
					}
				}
			}
		}
	}

	# Inkoop - verbergen voor klant
	if($inkoop) {
		$db->query("SELECT persoonnummer, deelnemers, soort, naam, inkoop, korting, optiecategorie, hoort_bij_accommodatieinkoop FROM extra_optie WHERE verberg_voor_klant=1 AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' ORDER BY soort, naam");
		if($db->num_rows()) {
			while($db->next_record()) {
				if($db->f("persoonnummer")=="alg") {
					$kleurteller_inkoop++;
					if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
					$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he($db->f("soort").": ".$db->f("naam"));
					$inkoopbedrag=$db->f("inkoop");
					$korting=$db->f("korting");
					$inkoop_netto=round($inkoopbedrag*(1-$korting/100),2);
					$return_inkoop.="</td><td style=\"vertical-align:top;\">(".bedrag_korting_tekst($inkoopbedrag,$korting,0,0).")</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
					$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x 1</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
					$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
					$return_inkoop.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($inkoop_netto,$inkoop)."</td>";
					$return_inkoop.="</tr>";

					$inkoop_totaal+=$inkoop_netto;
					if(!$db->f("hoort_bij_accommodatieinkoop")) {
						$inkoop_opties[$db->f("optiecategorie")]+=$inkoop_netto;
					}
				} elseif($db->f("persoonnummer")=="pers") {
					$tempdeelnemers=@split(",",$db->f("deelnemers"));

					if(is_array($tempdeelnemers)) {

						$tempaantaldeelnemers=count($tempdeelnemers);

						$kleurteller_inkoop++;
						if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
						$inkoopbedrag=$db->f("inkoop");
						$korting=$db->f("korting");
						$inkoop_netto=round($inkoopbedrag*(1-$korting/100),2);
						$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he($db->f("soort").": ".$db->f("naam"));
						$return_inkoop.="</td><td style=\"vertical-align:top;\">(".bedrag_korting_tekst($inkoopbedrag,$korting,0,0).")</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
						$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".$tempaantaldeelnemers."</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
						$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto*$tempaantaldeelnemers),2,',','.')."</td>";
						$return_inkoop.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($inkoop_netto,$inkoop)."</td>";
						$return_inkoop.="</tr>";

						$inkoop_totaal+=$inkoop_netto*$tempaantaldeelnemers;
						if(!$db->f("hoort_bij_accommodatieinkoop")) {
							$inkoop_opties[$db->f("optiecategorie")]+=$inkoop_netto;
						}
					}
				}
			}
		}
	}

	# Subtotaal
	if($gegevens["stap4"]["reisverzekering"] or $gegevens["stap4"]["annuleringsverzekering"]) {
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";
		} else {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>&nbsp;</td></tr>";
		}

		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr style=\"font-style:italic;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("subtotaal","vars");
			$return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"]+$gegevens["stap4"]["optie_bedrag_buiten_annuleringsverzekering"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";
		} else {
			$return.="<tr class=\"mobile_row\" style=\"font-style:italic;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"><td>".html("subtotaal","vars").":";
			$return.="<div class=\"costs\">&euro; ".number_format($gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"]+$gegevens["stap4"]["optie_bedrag_buiten_annuleringsverzekering"],2,',','.');
			$return.="</div></td>";
			$return.="</tr>";
		}
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";
		} else {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>&nbsp;</td></tr>";
		}
	} elseif($gegevens["fin"]["reserveringskosten"]>0) {
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);
		if(!$isMobile) {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";
		} else {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>&nbsp;</td></tr>";
		}
	}
	# Inkoop
	if($inkoop) {
		$kleurteller_inkoop++;
		if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
		$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";
	}

	# Reisverzekering
	if($gegevens["stap4"]["reisverzekering"]) {
		while(list($key2,$value2)=@each($reisverzekeringen["naam"])) {
			# Gewone kosten reisverzekering
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);
	            if(!$isMobile){
	                $return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(ucfirst($value2));
	                $return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($reisverzekeringen["bedrag"][$key2]),2,',','.')."</td>";
	                $return.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".$reisverzekeringen["aantal"][$key2]."</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
	                $return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($reisverzekeringen["bedrag"][$key2]*$reisverzekeringen["aantal"][$key2]),2,',','.')."</td>";
	                $return.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($reisverzekeringen["bedrag"][$key2],$inkoop)."</td>";
	                $return.="</tr>";
	            }else {
	                $return.="<tr class='mobile_row' ".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(ucfirst($value2));
	                $return.="<div class='costs'>";
	                $return.=number_format(abs($reisverzekeringen["bedrag"][$key2]),2,',','.')." x ".$reisverzekeringen["aantal"][$key2]. " = &euro; ".number_format(abs($reisverzekeringen["bedrag"][$key2]*$reisverzekeringen["aantal"][$key2]),2,',','.')." ".reissom_tabel_korting_of_min_tekst($reisverzekeringen["bedrag"][$key2],$inkoop);
	                $return.="</div></td>";
	                $return.="</tr>";
	            }
			# Inkoop
			if($inkoop) {
				$db->query("SELECT inkoop, korting FROM optie_tarief WHERE optie_onderdeel_id='".addslashes($reisverzekeringen["optieonderdeelid"][$key2])."' AND week='".addslashes($gegevens["stap1"]["aankomstdatum"])."';");
				if($db->next_record()) {
					$kleurteller_inkoop++;
					if($kleurteller_inkoop>1) unset($kleurteller_inkoop);

					$inkoopbedrag=$db->f("inkoop");
					$korting=$db->f("korting");
					$inkoop_netto=round($inkoopbedrag*(1-$korting/100),2);

					$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".wt_he(ucfirst($value2));
					$return_inkoop.="</td><td style=\"vertical-align:top;\">(".bedrag_korting_tekst($inkoopbedrag,$korting,0,0).")</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto),2,',','.')."</td>";
					$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".$reisverzekeringen["aantal"][$key2]."</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
					$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format(abs($inkoop_netto*$reisverzekeringen["aantal"][$key2]),2,',','.')."</td>";
					$return_inkoop.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($reisverzekeringen["bedrag"][$key2],$inkoop)."</td>";
					$return_inkoop.="</tr>";

					$inkoop_totaal+=$inkoop_netto*$reisverzekeringen["aantal"][$key2];
					$inkoop_opties[8]+=$inkoop_netto*$reisverzekeringen["aantal"][$key2];
				} else {
					trigger_error("geen optietarief (optieid".$key.") gevonden",E_USER_NOTICE);
				}
			}
		}

		if($gegevens["fin"]["reisverzekering_poliskosten"]<>0) {
			# Poliskosten reisverzekering
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("poliskostenreisverzekering","vars");
			$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["reisverzekering_poliskosten"],2,',','.')."</td>";
			$return.="<td style=\"padding-right:10px;white-space:nowrap;\"> x 1</td><td style=\"padding-right:10px\">=</td>";
			$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["reisverzekering_poliskosten"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";
		}
	}

	# Tarief annuleringsverzekering
	if($gegevens["stap4"]["annuleringsverzekering"]) {
		# Percentage annuleringsverzekering
		ksort($gegevens["stap4"]["annuleringsverzekering_soorten"]);
		while(list($key,$value)=each($gegevens["stap4"]["annuleringsverzekering_soorten"])) {
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);

			if(!$isMobile) {
				$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("annuleringsverzekering","vars")." ".wt_he($vars["annverz_soorten"][$key]);
				if(abs(floatval($gegevens["stap4"]["annuleringsverzekering_bedragen"][$key]-$gegevens["fin"]["accommodatie_totaalprijs"]-$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"]))<=0.03) {
					$toon_annuleringsverzekering_bedragen=$gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"];
				} else {
					$toon_annuleringsverzekering_bedragen=$gegevens["stap4"]["annuleringsverzekering_bedragen"][$key];
				}
				$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($toon_annuleringsverzekering_bedragen,2,',','.')."</td>";
				$return.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".ereg_replace("\.",",",$gegevens["stap1"]["annuleringsverzekering_percentage_".$key])."%</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
				$return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["annuleringsverzekering_variabel_".$key],2,',','.')."</td>";
				$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
				$return.="</tr>";
			} else {
				$return.="<tr class=\"mobile_row\"".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>".html("annuleringsverzekering","vars")." ".wt_he($vars["annverz_soorten"][$key]).":";
				if(abs(floatval($gegevens["stap4"]["annuleringsverzekering_bedragen"][$key]-$gegevens["fin"]["accommodatie_totaalprijs"]-$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"]))<=0.03) {
					$toon_annuleringsverzekering_bedragen=$gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"];
				} else {
					$toon_annuleringsverzekering_bedragen=$gegevens["stap4"]["annuleringsverzekering_bedragen"][$key];
				}
				$return.="<div class=\"costs\">&euro; ".number_format($toon_annuleringsverzekering_bedragen,2,',','.');
				$return.=" x ".ereg_replace("\.",",",$gegevens["stap1"]["annuleringsverzekering_percentage_".$key])."% = ";
				$return.=" &euro; ".number_format($gegevens["fin"]["annuleringsverzekering_variabel_".$key],2,',','.');
				$return.="</div></td>";
				$return.="</tr>";
			}

			# Inkoop
			if($inkoop) {
				$db->query("SELECT annuleringsverzekering_percentage_".$key."_korting AS korting, annuleringsverzekering_percentage_".$key."_basis AS basis, annuleringsverzekering_percentage_".$key."_berekend AS berekend FROM seizoen WHERE seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."';");
				if($db->next_record()) {

					if($gegevens["stap1"]["seizoenid"]==20 and date("Y",$gegevens["stap1"]["bevestigdatum"])<2013) {
						# Tijdelijke oplossing voor midden in het seizoen ophogen van assurantiebelasting van 9.7% naar 21% (02-01-2013)
						$temp_ann_verz_berekend=$db->f("basis")*1.097;
					} else {
						$temp_ann_verz_berekend=$db->f("berekend");
					}
					$percentage=$db->f("basis")*(1-$db->f("korting")/100)+($temp_ann_verz_berekend-$db->f("basis"));
					$inkoopbedrag=round($toon_annuleringsverzekering_bedragen*($percentage/100),2);

					$kleurteller_inkoop++;
					if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
					$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("annuleringsverzekering","vars")." ".wt_he($vars["annverz_soorten"][$key]);
					$return_inkoop.="</td><td>&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($toon_annuleringsverzekering_bedragen,2,',','.')."</td>";
					$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\"> x ".ereg_replace("\.",",",$percentage)."%</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
					$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($inkoopbedrag,2,',','.')."</td>";
					$return_inkoop.="<td style=\"vertical-align:top;\">&nbsp;</td>";
					$return_inkoop.="</tr>";

					$inkoop_totaal+=$inkoopbedrag;
					$inkoop_opties[8]+=$inkoopbedrag;
				} else {
					trigger_error("seizoen niet gevonden",E_USER_NOTICE);
				}
			}
		}


		if($gegevens["fin"]["annuleringsverzekering_poliskosten"]<>0) {
			# Poliskosten annuleringsverzekering
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("poliskostenannuleringsverzekering","vars");
			$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["annuleringsverzekering_poliskosten"],2,',','.')."</td>";
			$return.="<td style=\"padding-right:10px\"> x 1</td><td style=\"padding-right:10px\">=</td>";
			$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["annuleringsverzekering_poliskosten"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";
		}
	}

	if($gegevens["stap1"]["schadeverzekering"]) {
		# schadeverzekering
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);
        if(!$isMobile){
            $return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("schadeverzekering","vars");
            $return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["stap1"]["accprijs"],2,',','.')."</td>";
            $return.="<td style=\"padding-right:10px;vertical-align:top;\" nowrap> x ".number_format($gegevens["stap1"]["schadeverzekering_percentage"],2,',','.')."%</td><td style=\"padding-right:10px\">=</td>";
            $return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["schadeverzekering_variabel"],2,',','.')."</td>";
            $return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
            $return.="</tr>";
        }else {
            $return.="<tr class='mobile_row' ".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("schadeverzekering","vars");
            $return.="<div class='costs'>";
            $return.="&euro; ".number_format($gegevens["stap1"]["accprijs"],2,',','.')." x ".number_format($gegevens["stap1"]["schadeverzekering_percentage"],2,',','.')."% = &euro; ".number_format($gegevens["fin"]["schadeverzekering_variabel"],2,',','.');
            $return."</div></td>";
            $return.="</tr>";
        }
		# Inkoop
		if($inkoop) {
			$db->query("SELECT schadeverzekering_percentage_korting AS korting, schadeverzekering_percentage_basis AS basis, schadeverzekering_percentage_berekend AS berekend FROM seizoen WHERE seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."';");
			if($db->next_record()) {

				if($gegevens["stap1"]["seizoenid"]==20 and date("Y",$gegevens["stap1"]["bevestigdatum"])<2013) {
					# Tijdelijke oplossing voor midden in het seizoen ophogen van assurantiebelasting van 9.7% naar 21% (02-01-2013)
					$temp_schade_verz_berekend=$db->f("basis")*1.097;
				} else {
					$temp_schade_verz_berekend=$db->f("berekend");
				}

				$percentage=$db->f("basis")*(1-$db->f("korting")/100)+($temp_schade_verz_berekend-$db->f("basis"));
				$inkoopbedrag=round($gegevens["stap1"]["accprijs"]*($percentage/100),2);

				$kleurteller_inkoop++;
				if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
				$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("schadeverzekering","vars");
				$return_inkoop.="</td><td>&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["stap1"]["accprijs"],2,',','.')."</td>";
				$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\" nowrap> x ".ereg_replace("\.",",",$percentage)."%</td><td style=\"padding-right:10px\">=</td>";
				$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($inkoopbedrag,2,',','.')."</td>";
				$return_inkoop.="<td style=\"vertical-align:top;\">&nbsp;</td>";
				$return_inkoop.="</tr>";

				$inkoop_totaal+=$inkoopbedrag;
				$inkoop_opties[8]+=$inkoopbedrag;

			} else {
				trigger_error("seizoen niet gevonden",E_USER_NOTICE);
			}
		}
	}

	# Poliskosten (voor alle verzekeringen samen)
	if($gegevens["fin"]["verzekeringen_poliskosten"]<>0) {
		# Poliskosten alle verzekeringen
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("poliskostenverzekeringen","vars");
			$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["verzekeringen_poliskosten"],2,',','.')."</td>";
			$return.="<td style=\"padding-right:10px\"> x 1</td><td style=\"padding-right:10px\">=</td>";
			$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["verzekeringen_poliskosten"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";
		} else {
			$return.="<tr class=\"mobile_row\"".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>".html("poliskostenverzekeringen","vars").":";
			$return.="<div class=\"costs\">&euro; ".number_format($gegevens["fin"]["verzekeringen_poliskosten"],2,',','.');
			$return.=" x 1 = ";
			$return.="&euro; ".number_format($gegevens["fin"]["verzekeringen_poliskosten"],2,',','.');
			$return.="</div></td>";
			$return.="</tr>";
		}

		# Inkoop
		if($inkoop) {
			$kleurteller_inkoop++;
			if($kleurteller_inkoop>1) unset($kleurteller_inkoop);

			$db->query("SELECT assurantiebelasting, verzekeringen_poliskosten_basis FROM seizoen WHERE seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."';");
			if($db->next_record()) {
				if($gegevens["stap1"]["seizoenid"]==20 and date("Y",$gegevens["stap1"]["bevestigdatum"])<2013) {
					# Tijdelijke oplossing voor midden in het seizoen ophogen van assurantiebelasting van 9.7% naar 21% (02-01-2013)
					$temp_assurantiebelasting=9.7;
				} else {
					$temp_assurantiebelasting=$db->f("assurantiebelasting");
				}

				$inkoop_poliskosten=round($db->f("verzekeringen_poliskosten_basis")-($db->f("verzekeringen_poliskosten_basis")/(1+($temp_assurantiebelasting/100))),2);
			}
			$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("poliskostenverzekeringen","vars");
			$return_inkoop.="</td><td>&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($inkoop_poliskosten,2,',','.')."</td>";
			$return_inkoop.="<td style=\"padding-right:10px\"> x 1</td><td style=\"padding-right:10px\">=</td>";
			$return_inkoop.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($inkoop_poliskosten,2,',','.')."</td>";
			$return_inkoop.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return_inkoop.="</tr>";

			$inkoop_totaal+=$inkoop_poliskosten;
			$inkoop_opties[8]+=$inkoop_poliskosten;
		}
	}

	if($gegevens["fin"]["reserveringskosten"]>0) {
		# Reserveringskosten
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("reserveringskosten","vars");
			$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["reserveringskosten"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";
		} else {
			$return.="<tr class=\"mobile_row\" ".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>".html("reserveringskosten","vars").":";
			$return.="<div class=\"costs\">&euro; ".number_format($gegevens["fin"]["reserveringskosten"],2,',','.');
			$return.="</div></td>";
			$return.="</tr>";
		}

		# Inkoop
		if($inkoop) {
			$kleurteller_inkoop++;
			if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
			$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("reserveringskosten","vars");
			$return_inkoop.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">-</td>";
			$return_inkoop.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return_inkoop.="</tr>";
		}
	}

	if($gegevens["fin"]["commissie_accommodatie"]<>0 or $gegevens["fin"]["commissie_opties"]<>0) {

		# Commissie reisbureau

		# Totale reissom klant
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";
		} else {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>&nbsp;</td></tr>";
		}

		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr style=\"font-weight:bold;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("totalereissom_klant","vars");
			$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["totale_reissom_zonder_commissie_aftrek"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";
		} else {
			$return.="<tr style=\"font-weight:bold;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"><td>".html("totalereissom_klant","vars").":";
			$return.="<div class=\"costs\">&euro; ".number_format($gegevens["fin"]["totale_reissom_zonder_commissie_aftrek"],2,',','.');
			$return.="</div></td>";
			$return.="</tr>";
		}

		if($opties["tonen_verbergen"]) {
			$temp_class=" class=\"tonen_verbergen_1\"";
		} else {
			$temp_class="";
		}
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);
		$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "").$temp_class."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";

		if($gegevens["fin"]["commissie_accommodatie"]<>0) {
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "").$temp_class."><td style=\"padding-right:10px;vertical-align:top;\">".html("commissie_accommodatie","vars");
			$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".getal_met_juist_aantal_decimalen_weergeven($gegevens["stap1"]["commissie"])."%</td>";
			$return.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\">&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
			$return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["commissie_accommodatie"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($bedrag,$inkoop)."</td>";
			$return.="</tr>";
		}

		if($gegevens["fin"]["commissie_opties"]<>0) {
			# Commissie opties
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "").$temp_class."><td style=\"padding-right:10px;vertical-align:top;\">".html("commissie_opties","vars");
			$return.="</td>".$extra_td;
			if(count($gegevens["stap4"]["opties_commissie_precentages"])==1) {
				$return.="<td style=\"padding-right:10px;vertical-align:top;\">&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">";
				reset($gegevens["stap4"]["opties_commissie_precentages"]);
				list($temp_key,$temp_value)=each($gegevens["stap4"]["opties_commissie_precentages"]);
				$return.=getal_met_juist_aantal_decimalen_weergeven($temp_key)."%";
				$return.="</td>";
				$return.="<td style=\"padding-right:10px;vertical-align:top;white-space:nowrap;\">&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;\">=</td>";
			} else {
				$return.="<td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(4+($extra_td ? -1 : 0)+$extra_colspan)."\" nowrap>";
				$return.=html("commissie_diverse_percentages","vars");
				$return.="</td>";
			}
			$return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["commissie_opties"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;white-space:nowrap;\">".reissom_tabel_korting_of_min_tekst($bedrag,$inkoop)."</td>";
			$return.="</tr>";
		}

		if($gegevens["stap1"]["btw_over_commissie"]) {
			# Commissie BTW
			$kleurteller++;
			if($kleurteller>1) unset($kleurteller);
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("commissie_btw","vars");
			$return.="</td>".$extra_td."<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["commissie_accommodatie"]+$gegevens["fin"]["commissie_opties"],2,',','.')."</td>";
			$return.="<td style=\"padding-right:10px;vertical-align:top;\" nowrap> x ".getal_met_juist_aantal_decimalen_weergeven($gegevens["stap1"]["btw_over_commissie_percentage"])."%</td><td style=\"padding-right:10px\">=</td>";
			$return.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["commissie_btw"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";

			# Inkoop (negatieve inkoop btw)
			if($inkoop) {

				$kleurteller_inkoop++;
				if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
				$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb\"" : "")."><td style=\"padding-right:10px;vertical-align:top;\">".html("commissie_btw","vars");
				$return_inkoop.="</td><td>&nbsp;</td><td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;vertical-align:top;text-align:right;\">".number_format($gegevens["fin"]["commissie_accommodatie"]+$gegevens["fin"]["commissie_opties"],2,',','.')."</td>";
				$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\" nowrap> x ".getal_met_juist_aantal_decimalen_weergeven($gegevens["stap1"]["btw_over_commissie_percentage"])."%</td><td style=\"padding-right:10px\">=</td>";
				$return_inkoop.="<td style=\"padding-right:10px;vertical-align:top;\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format(0-$gegevens["fin"]["commissie_btw"],2,',','.')."</td>";
				$return_inkoop.="<td style=\"vertical-align:top;\">&nbsp;</td>";
				$return_inkoop.="</tr>";


				// $kleurteller_inkoop++;
				// if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
				// $return_inkoop.="<tr style=\"".(!$kleurteller_inkoop ? "background-color:#ebebeb" : "")."\"><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("commissie_btw","vars");
				// $return_inkoop.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format(0-$gegevens["fin"]["commissie_btw"],2,',','.')."</td>";
				// $return_inkoop.="<td style=\"vertical-align:top;\">&nbsp;</td>";
				// $return_inkoop.="</tr>";

				$inkoop_totaal=$inkoop_totaal-$gegevens["fin"]["commissie_btw"];
			}

		}

		# Commissie totaal
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);
		$return.="<tr style=\"font-weight:bold;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"".$temp_class."><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("commissie_totaal","vars");
		$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["commissie_totaal"],2,',','.')."</td>";
		$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
		$return.="</tr>";

		# Totale reissom (bij commissie)
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);
		$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "").$temp_class."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);
		$return.="<tr style=\"font-weight:bold;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"".$temp_class."><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("totalereissom","vars");
		$return.="</td><td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["totale_reissom"],2,',','.')."</td>";
		$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
		$return.="</tr>";

		if($temp_class) {
			$return.="<tr style=\"font-size:0.8em;\"><td colspan=\"".(4+$extra_colspan)."\">&nbsp;<p><a href=\"javascript:toggle_tonen_verbergen('tonen_verbergen_1');\">".html("wederverkoop_tooncommissie","vars")."</a></td></tr>";
		}
	} else {
		# Totale reissom
		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";
		} else {
			$return.="<tr".(!$kleurteller ? " style=\"background-color:#ebebeb\"" : "")."><td>&nbsp;</td></tr>";
		}

		$kleurteller++;
		if($kleurteller>1) unset($kleurteller);

		if(!$isMobile) {
			$return.="<tr style=\"font-weight:bold;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("totalereissom","vars");
			$return.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($gegevens["fin"]["totale_reissom"],2,',','.')."</td>";
			$return.="<td style=\"vertical-align:top;\">&nbsp;</td>";
			$return.="</tr>";
		} else {
			$return.="<tr class=\"mobile_row\" style=\"font-weight:bold;".(!$kleurteller ? "background-color:#ebebeb" : "")."\"><td>".html("totalereissom","vars").":";
			$return.="<div class=\"costs\">&euro; ".number_format($gegevens["fin"]["totale_reissom"],2,',','.');
			$return.="</div></td>";
			$return.="</tr>";
		}

	}

	# Inkoop
	if($inkoop) {
		$kleurteller_inkoop++;
		if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
		$return_inkoop.="<tr".(!$kleurteller_inkoop ? " style=\"background-color:#ebebeb;\"" : "")."><td colspan=\"".(8+$extra_colspan)."\">&nbsp;</td></tr>";

		$kleurteller_inkoop++;
		if($kleurteller_inkoop>1) unset($kleurteller_inkoop);
		$return_inkoop.="<tr style=\"font-weight:bold;".(!$kleurteller_inkoop ? "background-color:#ebebeb" : "")."\"><td style=\"padding-right:10px;vertical-align:top;\" colspan=\"".(5+$extra_colspan)."\">".html("totalereissom","vars");
		$return_inkoop.="<td style=\"padding-right:10px\">&euro;</td><td style=\"padding-right:10px;text-align:right;\">".number_format($inkoop_totaal,2,',','.')."</td>";
		$return_inkoop.="<td style=\"vertical-align:top;\">&nbsp;</td>";
		$return_inkoop.="</tr>";
	}

	#
	# inkoop-return bepalen
	#
	if($inkoop) {
		$return_verkoop=$return;
		unset($return);
		$return["verkoop"]=$return_verkoop;
		$return["inkoop"]=$return_inkoop;

		$return["bedragen"]["inkoop"]=$inkoop_totaal;
		$return["bedragen"]["verkoop"]=$gegevens["fin"]["totale_reissom"];
		$return["bedragen"]["optieinkoop"]=$inkoop_opties;

		# marge bepalen
		if($return["bedragen"]["verkoop"]) {
			$marge_percentage=($return["bedragen"]["verkoop"]-$return["bedragen"]["inkoop"])/$return["bedragen"]["verkoop"]*100;
		} elseif($return["bedragen"]["inkoop"]) {
			$marge_percentage=-100;
		} else {
			$marge_percentage=0;
		}
		$return["bedragen"]["marge_euro"]=$return["bedragen"]["verkoop"]-$return["bedragen"]["inkoop"];
		$return["bedragen"]["marge_percentage"]=$marge_percentage;
	}
	return $return;
}

function nawcookie($voornaam,$tussenvoegsel,$achternaam,$adres,$postcode,$plaats,$land,$telefoonnummer,$mobielwerk,$email,$geboortedatum='not',$nieuwsbrief,$geslacht=0) {
	global $vars,$voorkant_cms;

	if(((!$voorkant_cms or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") and !$vars["chalettour_logged_in"]) or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		if($_COOKIE["sch"]) {
			$db=new DB_sql;
			if($nieuwsbrief) $nieuwsbrief=1; else $nieuwsbrief="";
			$db->query("UPDATE bezoeker SET voornaam='".addslashes($voornaam)."', tussenvoegsel='".addslashes($tussenvoegsel)."', achternaam='".addslashes($achternaam)."', adres='".addslashes($adres)."', postcode='".addslashes($postcode)."', plaats='".addslashes($plaats)."', land='".addslashes($land)."', telefoonnummer='".addslashes($telefoonnummer)."', mobielwerk='".addslashes($mobielwerk)."', email='".addslashes($email)."'".($geboortedatum=="not" ? "" : ", geboortedatum=FROM_UNIXTIME('".addslashes($geboortedatum)."')").($nieuwsbrief ? ", nieuwsbrief='1'" : "").($geslacht ? ", geslacht='".addslashes($geslacht)."'" : "").", gewijzigd=NOW() WHERE bezoeker_id='".addslashes($_COOKIE["sch"])."';");

			# Cookies wissen (is dadelijk niet meer nodig)
			$cookietime=time()-86400;
			setcookie("naw[voornaam]","",$cookietime,"/");
			setcookie("naw[tussenvoegsel]","",$cookietime,"/");
			setcookie("naw[achternaam]","",$cookietime,"/");
			setcookie("naw[adres]","",$cookietime,"/");
			setcookie("naw[postcode]","",$cookietime,"/");
			setcookie("naw[plaats]","",$cookietime,"/");
			setcookie("naw[land]","",$cookietime,"/");
			setcookie("naw[telefoonnummer]","",$cookietime,"/");
			setcookie("naw[mobielwerk]","",$cookietime,"/");
			setcookie("naw[email]","",$cookietime,"/");
			setcookie("naw[geboortedatum]","",$cookietime,"/");
			setcookie("naw[nieuwsbrief]","",$cookietime,"/");
		}
	}
}

function getnaw() {
	if($_COOKIE["sch"]) {
		$db=new DB_sql;
		$db->query("SELECT voornaam, tussenvoegsel, achternaam, adres, postcode, plaats, land, telefoonnummer, mobielwerk, email, UNIX_TIMESTAMP(geboortedatum) AS geboortedatum, nieuwsbrief, geslacht FROM bezoeker WHERE bezoeker_id='".addslashes($_COOKIE["sch"])."';");
		if($db->next_record()) {
			$return["voornaam"]=$db->f("voornaam");
			$return["tussenvoegsel"]=$db->f("tussenvoegsel");
			$return["achternaam"]=$db->f("achternaam");
			$return["adres"]=$db->f("adres");
			$return["postcode"]=$db->f("postcode");
			$return["plaats"]=$db->f("plaats");
			$return["land"]=$db->f("land");
			$return["telefoonnummer"]=$db->f("telefoonnummer");
			$return["mobielwerk"]=$db->f("mobielwerk");
			$return["email"]=$db->f("email");
			$return["geboortedatum"]=$db->f("geboortedatum");
			$return["nieuwsbrief"]=$db->f("nieuwsbrief");
			$return["geslacht"]=$db->f("geslacht");
			return $return;
		}
	}
}

function getreferer($bezoeker_id) {
	global $vars;
	if($bezoeker_id) {
		$db=new DB_sql;
		$db->query("SELECT UNIX_TIMESTAMP(datumtijd) AS datumtijd, ad, referer FROM bezoek WHERE bezoeker_id='".addslashes($bezoeker_id)."' AND (ad<>0 OR referer<>'') ORDER BY datumtijd;");
		if($db->num_rows()) {
			while($db->next_record()) {
				$teller++;

				# Tekstuele opsomming
				$return["opsomming"].=datum("DAG D MAAND JJJJ, UU:ZZ",$db->f("datumtijd"))."u: ";
				if($db->f("referer")) $return["opsomming"].="<a href=\"".wt_he($db->f("referer"))."\" target=\"_blank\">";
				if($db->f("ad")) {
					$return["opsomming"].=wt_he($vars["ads"][$db->f("ad")]);
				} elseif(ereg("http://www\.google\.",$db->f("referer"))) {
					$return["opsomming"].="Google";
				} else {
					$return["opsomming"].="website";
				}
				if($db->f("referer")) $return["opsomming"].="</a>";
				$return["opsomming"].="<br>";

				# Lijst in array
				$return["lijst"][$teller]["datumtijd"]=$db->f("datumtijd");
				$return["lijst"][$teller]["ad"]=$db->f("ad");
				$return["lijst"][$teller]["referer"]=$db->f("referer");

			}
			return $return;
		}
	}
}

function chaletmail() {

}

function volledigeaccnaam($typeid) {
	global $vars;
	$db=new DB_sql;

	if(!$vars["volledigeaccnaam"]) {
		$db->query("SELECT a.naam AS accommodatie, a.soortaccommodatie, t.type_id, t.naam".$vars["ttv"]." AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l WHERE t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id ORDER BY t.type_id;");
		while($db->next_record()) {
			unset($naam);
			$naam.=$db->f("begincode").$db->f("type_id")." ";
#			$naam.=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ";
			$naam.=$db->f("accommodatie");
			$aantalpersonen=$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." pers";
			if($db->f("type")) {
				$naam.=" ".$db->f("type")." ".$db->f("code")." ".$aantalpersonen."";

			} else {
				$naam.=" ".$db->f("code")." ".$aantalpersonen."";
			}
			$naam.=" - ".$db->f("plaats");
			$vars["volledigeaccnaam"][$db->f("type_id")]=$naam;
#			echo $db->f("type_id")." ".$vars["volledigeaccnaam"][$db->f("type_id")]."<br>";
		}
	}
	return $vars["volledigeaccnaam"][$typeid];
}

function weekend_voluit($timeteller,$toon_za_en_zo=true) {
	global $vars;

	$timeteller=intval($timeteller);

	if($timeteller>0) {

	} else {
		unset($timeteller);
	}

	if($toon_za_en_zo) {
		$return=txt("weekend_van")." ";
		# Hoe worden de weekenden genoemd?
		$timetellerdaglater=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+1,date("Y",$timeteller));
		if(date("m",$timeteller)==date("m",$timetellerdaglater)) {
			$return.=date("j",$timeteller)." ".txt("en")." ".date("j",$timetellerdaglater)." ".datum("MAAND JJJJ",$timeteller,$vars["taal"]);
		} else {
			$return.=date("j",$timeteller)." ".datum("MAAND",$timeteller,$vars["taal"])." ".(date("m",$timeteller)==12 ? date("Y",$timeteller) : "")." en ".date("j",$timetellerdaglater)." ".datum("MAAND JJJJ",$timetellerdaglater,$vars["taal"]);
		}
	} else {
		$return=txt("weekend")." ";
		$return.=datum("D MAAND JJJJ",$timeteller,$vars["taal"]);
	}
	return $return;
}

function bereken($optie,$seizoen,$week,$acc,$skipas) {
	global $tarieventabel_opmerkingen,$cms_kortingen_tarieven,$cron,$foutgemeld,$skipastarieven_verwerken,$vars,$cron;
	$bewaar_seizoen=$seizoen;
	$skipas=$skipas["weken"][$week];
	$seizoen=$seizoen["weken"][$week];

	if($optie==1) {
		# Toonper = 1 (arrangement)
		if($seizoen["bruto"]) {
			$return["inkoop_min_korting"]=$seizoen["bruto"]*(1-$seizoen["korting_percentage"]/100);

			$return["netto"]=$return["inkoop_min_korting"]+$seizoen["toeslag"]-$seizoen["korting_euro"];

			$return["netto"]=$return["netto"]-($return["inkoop_min_korting"]*($seizoen["vroegboekkorting_percentage"]/100));
			$return["netto"]=$return["netto"]-$seizoen["vroegboekkorting_euro"];

			if(!$cms_kortingen_tarieven) {

				# inkoopkorting percentage verwerken
				if($seizoen["inkoopkorting_percentage"]) {
					$return["netto"]=$return["netto"]*(1-$seizoen["inkoopkorting_percentage"]/100);
				}

				# inkoopkorting euro verwerken
				if($seizoen["inkoopkorting_euro"]) {
					$return["netto"]=$return["netto"]-$seizoen["inkoopkorting_euro"];
				}
			}

			# Wederverkoop
			$return["wederverkoop_verkoopprijs"]=$seizoen["bruto"];

			# aanbieding_acc_percentage verwerken bij wederverkoop
			if($seizoen["aanbieding_acc_percentage"]) {
				$return["wederverkoop_verkoopprijs"]=$return["wederverkoop_verkoopprijs"]*(1-$seizoen["aanbieding_acc_percentage"]/100);
			}

			# aanbieding_acc_euro verwerken bij wederverkoop
			if($seizoen["aanbieding_acc_euro"]) {
				$return["wederverkoop_verkoopprijs"]=$return["wederverkoop_verkoopprijs"]-$seizoen["aanbieding_acc_euro"];
			}

			$return["wederverkoop_verkoopprijs"]=$return["wederverkoop_verkoopprijs"]+($return["wederverkoop_verkoopprijs"]*($seizoen["opslag_accommodatie"]/100));

#			$return["wederverkoop_verkoopprijs"]=$return["wederverkoop_verkoopprijs"]+($return["wederverkoop_verkoopprijs"]*($seizoen["wederverkoop_opslag_percentage"]/100));
			$return["wederverkoop_verkoopprijs"]=$return["wederverkoop_verkoopprijs"]+($seizoen["bruto"]*($seizoen["wederverkoop_opslag_percentage"]/100));

			# Afronden op 5 euro (naar beneden)
			$return["wederverkoop_verkoopprijs"]=floor($return["wederverkoop_verkoopprijs"]/5)*5;

			$return["wederverkoop_verkoopprijs"]=$return["wederverkoop_verkoopprijs"]+$seizoen["wederverkoop_opslag_euro"];

			$return["wederverkoop_nettoprijs_agent"]=$return["wederverkoop_verkoopprijs"]-($return["wederverkoop_verkoopprijs"]*($seizoen["wederverkoop_commissie_agent"]/100));
			$return["wederverkoop_resterende_marge"]=$return["wederverkoop_nettoprijs_agent"]-$return["netto"];
			if($return["wederverkoop_nettoprijs_agent"]) {
				$return["wederverkoop_marge"]=$return["wederverkoop_resterende_marge"]/$return["wederverkoop_nettoprijs_agent"]*100;
				if($return["wederverkoop_marge"]<>0 and $return["wederverkoop_marge"]<10) $tarieventabel_opmerkingen="Let op! Wederverkoopmarge lager dan 10%!";
			}

			if($seizoen["bruto"]) {
				$verkoop_accommodatie=$seizoen["bruto"];
				if($seizoen["aanbieding_acc_percentage"]) {
					$verkoop_accommodatie=$verkoop_accommodatie*(1-$seizoen["aanbieding_acc_percentage"]/100);
				}
				if($seizoen["aanbieding_acc_euro"]) {
					$verkoop_accommodatie=$verkoop_accommodatie-$seizoen["aanbieding_acc_euro"];
				}
				$return["verkoop_accommodatie"]=$verkoop_accommodatie;
			}
			if($skipas["bruto"]) {
				$verkoop_skipas=$skipas["bruto"];
				if($seizoen["aanbieding_skipas_percentage"]) {
					$verkoop_skipas=$verkoop_skipas*(1-$seizoen["aanbieding_skipas_percentage"]/100);
				}
				if($seizoen["aanbieding_skipas_euro"]) {
					$verkoop_skipas=$verkoop_skipas-$seizoen["aanbieding_skipas_euro"];
				}
			}

			if($acc["min_tonen"] and $acc["max"] and $verkoop_skipas) {
				for($i=$acc["min_tonen"];$i<=$acc["max"];$i++) {
					$return["verkoop"][$i]=($verkoop_accommodatie/$i)+$verkoop_skipas;
					$return["verkoop"][$i]=$return["verkoop"][$i]+(($seizoen["opslag_accommodatie"]/100)*($return["netto"]/$i));
					$return["verkoop"][$i]=$return["verkoop"][$i]+(($seizoen["opslag_skipas"]/100)*$skipas["netto"]);

					$return["inkoop"][$i]=($return["netto"]/$i)+$skipas["prijs"];

					$marge_percentage_afronding=($return["verkoop"][$i]-$return["inkoop"][$i])/$return["verkoop"][$i]*100;
					if($acc["flexibel_afronden_op_hele_euros"]) {
						if($marge_percentage_afronding>12.5) {
							$return["verkoop_afgerond"][$i]=floor($return["verkoop"][$i]);
						} else {
							$return["verkoop_afgerond"][$i]=ceil($return["verkoop"][$i]);
						}
					} else {
						if($marge_percentage_afronding>12.5) {
							$return["verkoop_afgerond"][$i]=floor($return["verkoop"][$i]/5)*5;
						} else {
							$return["verkoop_afgerond"][$i]=ceil($return["verkoop"][$i]/5)*5;
						}
					}

					if(!$return["verkoop_afgerond"][$i]) $return["verkoop_afgerond"][$i]=5;
					$return["verkoop_site"][$i]=$return["verkoop_afgerond"][$i]+$seizoen["verkoop_afwijking"][$i];

#
# Tijdelijk! (18-11-2010) Kijken naar verschil tussen database en berekening
#
if(!$skipastarieven_verwerken and $GLOBALS["id"]<>"cms_kortingen_tarieven" and !$_GET["xmlgoedkeuren"] and !$seizoen["xml_tarievenimport"] and !$foutgemeld[$_GET["tid"]] and $_GET["tid"] and $seizoen["beschikbaar"] and $seizoen["verkoop_site"][$i] and $return["verkoop_site"][$i] and $seizoen["verkoop_site"][$i]<>$return["verkoop_site"][$i]) {
	trigger_error("verschil in database- en berekentarief ".$acc["land_begincode"].$_GET["tid"].": ".date("d/m/Y",$week)." - ".$i." personen db:".$seizoen["verkoop_site"][$i]." - bereken:".$return["verkoop_site"][$i]."",E_USER_NOTICE);
	$foutgemeld[$_GET["tid"]]=true;
}
					if($return["verkoop_site"][$i]>0) {
						$return["marge_percentage"][$i]=($return["verkoop_site"][$i]-$return["inkoop"][$i])/$return["verkoop_site"][$i]*100;
					} else {
						$return["marge_percentage"][$i]=0;
					}
					$return["marge_euro"][$i]=$return["verkoop_site"][$i]-$return["inkoop"][$i];
				}
				$verkoop_min_skipas=$return["verkoop_site"][$acc["max"]]-$verkoop_skipas;
				if($verkoop_min_skipas) $return["marge_accommodatie"][$acc["max"]]=($verkoop_min_skipas-($return["netto"]/$acc["max"]))/$verkoop_min_skipas*100;
				$return["verkoop_min_skipas"]=$verkoop_min_skipas*$acc["max"];
#				echo (($return["verkoop_min_skipas"]-$return["netto"])/$return["verkoop_min_skipas"]*100)."<br>";
			}
		}
	} elseif($optie==2) {
		# Toonper = 2
		if($seizoen["arrangementsprijs"]) {
			$return["inkoop_arrangementsprijs"]=$seizoen["arrangementsprijs"]*(1-($seizoen["korting_arrangement_bed_percentage"]/100))+$seizoen["toeslag_arrangement_euro"]-$seizoen["korting_arrangement_euro"];
			$return["netto_inkoop_arrangementsprijs"]=$return["inkoop_arrangementsprijs"]*(1-($seizoen["vroegboekkorting_arrangement_percentage"]/100))-$seizoen["vroegboekkorting_arrangement_euro"];
		}
		if($seizoen["onbezet_bed"]) {
			$return["inkoop_onbezet_bed"]=$seizoen["onbezet_bed"]*(1-($seizoen["korting_arrangement_bed_percentage"]/100))+$seizoen["toeslag_bed_euro"]-$seizoen["korting_bed_euro"];
			$return["netto_inkoop_onbezet_bed"]=$return["inkoop_onbezet_bed"]*(1-($seizoen["vroegboekkorting_bed_percentage"]/100))-$seizoen["vroegboekkorting_bed_euro"];
		}
		if($acc["min_tonen"] and $acc["max"] and $return["netto_inkoop_arrangementsprijs"] and $return["netto_inkoop_onbezet_bed"]) {
			for($i=$acc["min_tonen"];$i<=$acc["max"];$i++) {

				$return["inkoop"][$i]=$return["netto_inkoop_arrangementsprijs"]+(($acc["max"]-$i)/$i*$return["inkoop_onbezet_bed"]);

				$return["verkoop"][$i]=$return["inkoop"][$i]/(1-($seizoen["opslag"]/100));

				$marge_percentage_afronding=($return["verkoop"][$i]-$return["inkoop"][$i])/$return["verkoop"][$i]*100;
				if($marge_percentage_afronding>12.5) {
					$return["verkoop_afgerond"][$i]=floor($return["verkoop"][$i]/5)*5;
				} else {
					$return["verkoop_afgerond"][$i]=ceil($return["verkoop"][$i]/5)*5;
				}
				if(!$return["verkoop_afgerond"][$i]) $return["verkoop_afgerond"][$i]=5;
				$return["verkoop_site"][$i]=$return["verkoop_afgerond"][$i]+$seizoen["verkoop_afwijking"][$i];
				if($return["verkoop_site"][$i]>0) {
					$return["marge_percentage"][$i]=($return["verkoop_site"][$i]-$return["inkoop"][$i])/$return["verkoop_site"][$i]*100;
				} else {
					$return["marge_percentage"][$i]=0;
				}
				$return["marge_euro"][$i]=$return["verkoop_site"][$i]-$return["inkoop"][$i];
			}
		}
	} elseif($optie==3) {
		# Toonper = 3
		if($seizoen["c_bruto"]) {
			$return["c_inkoop_min_korting"]=$seizoen["c_bruto"]*(1-$seizoen["c_korting_percentage"]/100);

			$return["c_netto"]=$return["c_inkoop_min_korting"]+$seizoen["c_toeslag"]-$seizoen["c_korting_euro"];
			$return["c_netto"]=$return["c_netto"]-($return["c_inkoop_min_korting"]*($seizoen["c_vroegboekkorting_percentage"]/100));
			$return["c_netto"]=$return["c_netto"]-$seizoen["c_vroegboekkorting_euro"];

			$return["c_netto_zonder_inkoopkorting"]=$return["c_netto"];

			# Verkoopprijs bepalen
			$return["c_verkoop"]=$seizoen["c_bruto"];
			$return["c_verkoop"]=$return["c_verkoop"]+($seizoen["c_opslag_accommodatie"]/100)*$return["c_netto"];
			$return["c_verkoop_zonder_kortingen"]=$return["c_verkoop"];

			if(!$cms_kortingen_tarieven) {

				# inkoopkorting percentage verwerken
				if($seizoen["inkoopkorting_percentage"]) {
					$return["c_netto"]=$return["c_netto"]*(1-$seizoen["inkoopkorting_percentage"]/100);
				}

				# inkoopkorting euro verwerken
				if($seizoen["inkoopkorting_euro"]) {
					$return["c_netto"]=$return["c_netto"]-$seizoen["inkoopkorting_euro"];
				}
			}

			if(!$cms_kortingen_tarieven) {

				# aanbieding_acc_percentage verwerken
				if($seizoen["aanbieding_acc_percentage"]) {
					$return["c_verkoop"]=$return["c_verkoop"]*(1-$seizoen["aanbieding_acc_percentage"]/100);
				}

				# aanbieding_acc_euro verwerken
				if($seizoen["aanbieding_acc_euro"]) {
					$return["c_verkoop"]=$return["c_verkoop"]-$seizoen["aanbieding_acc_euro"];
				}
			}

			$return["c_inkoop"]=$return["c_netto"];

			if($return["c_verkoop"]<>0) {
				$marge_percentage_afronding=($return["c_verkoop"]-$return["c_inkoop"])/$return["c_verkoop"]*100;
			}
			if($acc["flexibel_afronden_op_hele_euros"]) {
				if($marge_percentage_afronding>12.5) {
					$return["c_verkoop_afgerond"]=floor($return["c_verkoop"]);
				} else {
					$return["c_verkoop_afgerond"]=ceil($return["c_verkoop"]);
				}
			} else {
				if($marge_percentage_afronding>12.5) {
					$return["c_verkoop_afgerond"]=floor($return["c_verkoop"]/5)*5;
				} else {
					$return["c_verkoop_afgerond"]=ceil($return["c_verkoop"]/5)*5;
				}
			}
			if(!$return["c_verkoop_afgerond"]) $return["c_verkoop_afgerond"]=5;
			$return["c_verkoop_site"]=$return["c_verkoop_afgerond"]+$seizoen["c_verkoop_afwijking"];
			if($return["c_verkoop_site"]>0) {
				$return["c_marge_percentage"]=($return["c_verkoop_site"]-$return["c_inkoop"])/$return["c_verkoop_site"]*100;
			} else {
				$return["c_marge_percentage"]=0;
			}
			$return["c_marge_euro"]=$return["c_verkoop_site"]-$return["c_inkoop"];

			# Wederverkoop
#			$return["wederverkoop_verkoopprijs"]=$seizoen["c_verkoop_site"]+($seizoen["c_bruto"]*($seizoen["wederverkoop_opslag_percentage"]/100));

			# Werkt dit??? (30-12-2009)
			if(isset($seizoen["c_verkoop_site"]) and !$seizoen["xml_tarievenimport"] and $vars["id"]<>"cms_kortingen_tarieven" and !$cron) {
				$return["wederverkoop_verkoopprijs"]=$seizoen["c_verkoop_site"]+($seizoen["c_bruto"]*($seizoen["wederverkoop_opslag_percentage"]/100));
			} else {
				$return["wederverkoop_verkoopprijs"]=$return["c_verkoop_site"]+($return["c_bruto"]*($seizoen["wederverkoop_opslag_percentage"]/100));
			}
#echo $return["wederverkoop_verkoopprijs"]."===".$return["c_verkoop_site"]."===".$seizoen["c_verkoop_site"]."<br>";

			if($acc["flexibel_afronden_op_hele_euros"]) {
				$return["wederverkoop_verkoopprijs"]=floor($return["wederverkoop_verkoopprijs"]);
			} else {
				# Afronden op 5 euro (naar beneden)
				$return["wederverkoop_verkoopprijs"]=floor($return["wederverkoop_verkoopprijs"]/5)*5;
			}

			$return["wederverkoop_verkoopprijs"]=$return["wederverkoop_verkoopprijs"]+$seizoen["wederverkoop_opslag_euro"];

			$return["wederverkoop_nettoprijs_agent"]=$return["wederverkoop_verkoopprijs"]-($return["wederverkoop_verkoopprijs"]*($seizoen["wederverkoop_commissie_agent"]/100));
			$return["wederverkoop_resterende_marge"]=$return["wederverkoop_nettoprijs_agent"]-$return["c_netto"];
			if($return["wederverkoop_nettoprijs_agent"]) {
				$return["wederverkoop_marge"]=$return["wederverkoop_resterende_marge"]/$return["wederverkoop_nettoprijs_agent"]*100;
				if($return["wederverkoop_marge"]<>0 and $return["wederverkoop_marge"]<10) $tarieventabel_opmerkingen="Let op! Wederverkoopmarge lager dan 10%!";
			}
		}
	}

	$doorloop_array=$return;
	$return=$bewaar_seizoen["weken"][$week];
	while(list($key,$value)=@each($doorloop_array)) {
		if(is_array($value)) {
			while(list($key2,$value2)=each($value)) {
				$return[$key][$key2]=ereg_replace(",",".",sprintf("%01.2f",$value2));
			}
		} else {
			$return[$key]=ereg_replace(",",".",sprintf("%01.2f",$value));
		}
	}
	return $return;
}

function wt_convert2url_oud($text) {
	$text=ereg_replace("/","|",$text);
	return urlencode(ereg_replace(" ","_",wt_stripaccents($text)));
}

function seo_acc_url($typecode,$soortaccommodatie,$accnaam,$typenaam) {
	global $vars;
	$return=$vars["basehref"].txt("canonical_accommodatiepagina")."/".strtolower($typecode)."/".wt_convert2url_seo(ucfirst($vars["soortaccommodatie"][$soortaccommodatie])." ".$accnaam.($typenaam ? " ".$typenaam : ""));
	return $return;
}

function htmlentities_uitgebreid($text,$li=false) {
	global $vars;
	$text=wt_he($text,ENT_COMPAT,cp1252);
	$text=ereg_replace("&euro; ","&euro;&nbsp;",$text);

	if($li) {
		$text=ereg_replace("^- ","<li>",$text);
		$text=ereg_replace(chr(10)."- ","<li>",$text);
	}
	$text=ereg_replace("&amp;","&",$text);

	# http klikbaar maken
	$text=eregi_replace("^(https?://[a-z0-9\./?&%=\-]+)","<a href=\"\\1\" target=\"_blank\" rel=\"nofollow\">\\1</a>",$text);
	$text=eregi_replace("[^=>\"](https?://[a-z0-9\./?&%=\-]+)","<a href=\"\\1\" target=\"_blank\" rel=\"nofollow\">\\1</a>",$text);

	# E-mail klikbaar maken
	$text=eregi_replace("([^a-z0-9]|^)([0-9a-z][-_0-9a-z.]*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4})([^a-z]|$)","\\1<a href=\"mailto:\\2\">\\2</a>\\4",$text);

	# www klikbaar maken
	$text=ereg_replace("([^/])(www\.[a-z0-9-]+\.[a-z]{1,4})([^a-z0-9])","\\1<a href=\"http://\\2/\" target=\"_blank\" rel=\"nofollow\">\\2</a>\\3",$text);

	$text=ereg_replace("([^/])(www\.[a-z0-9-]+\.[a-z]{1,4})([^a-z0-9]|$)","\\1<a href=\"http://\\2/\" target=\"_blank\" rel=\"nofollow\">\\2</a>\\3",$text);

	# externe [link=http://url/]tekst[/link] omzetten
	$text=ereg_replace("\[link=(http[^]]+)\]([^[]+)\[/link\]","<a href=\"\\1\" target=\"_blank\" rel=\"nofollow\">\\2</a>",$text);

	# interne [link=/url/]tekst[/link] omzetten
	$text=ereg_replace("\[link=([^]]+)\]([^[]+)\[/link\]","<a href=\"".ereg_replace("/$","",$vars["path"])."\\1\">\\2</a>",$text);

	# [b] bold maken
	$text=ereg_replace("\[b\]([^[]+)\[/b\]","<b>\\1</b>",$text);

	# [i] italics maken
	$text=ereg_replace("\[i\]([^[]+)\[/i\]","<i>\\1</i>",$text);

	$text=nl2br($text);

	return $text;
}

function imagetable($onderdeel,$id) {
	global $path;
	$mappen=array("","_breed");
	while(list($key,$value)=each($onderdeel)) {
		$onderdeel_teller++;
		reset($mappen);
		$dir="pic/cms/".$value;
		if(file_exists($dir)) {
			$d=dir($dir);
			while($entry=$d->read()) {
				if(ereg("^".$id[$key]."-([0-9]{1,3})\.jpg$",$entry,$regs)) {
					if(ereg("_breed",$value)) $soort="picbreed"; else $soort="pic";
					$temp[$soort][$onderdeel_teller."_".substr("000000".$regs[1],-7)]=$value."/".$entry;
				}
			}
		}
	}
	@ksort($temp["pic"]);
	@ksort($temp["picbreed"]);
	if(is_array($temp["pic"]) or is_array($temp["picbreed"])) {
		unset($counter);

		$foto_table="";
		if(is_array($temp["pic"])) {
			$foto_table.="<TR>";
			while(list($key,$value)=@each($temp["pic"])) {
				$trcounter++;
				$counter++;
	#			if($trcounter==3) {
				if($counter % 2 != 0) {
					$foto_table.="</TR><TR>";
					unset($trcounter);
				}
				$foto_table.="<TD align=\"center\"";
				if(count($temp["pic"])==$counter and $trcounter==0) $foto_table.=" colspan=\"2\"";
				$foto_table.=">";
				$size=getimagesize("pic/cms/".$value);
				if($size[0]>300) {
					$foto_table.="<div class=\"foto_doorklik\"><a href=\"javascript:fotopopup(".($size[0]+0).",".($size[1]+0).",'".$path."foto.php?f=".urlencode("pic/cms/".$value)."',true)\">";
					$foto_table.="<img src=\"".$path."thumbnail.php?file=".urlencode($value)."\" alt=\"".html("klikopdefoto","imagetable")."\" width=\"200\" height=\"150\" border=\"0\">";
					$foto_table.="<br><img src=\"".$path."pic/foto_doorklik.png\" alt=\"\" width=\"15\" height=\"11\" align=\"right\" border=\"0\" style=\"margin-top:2px;margin-right:4px\" class=\"noprint\"></a>&nbsp;</div>";
					$vergroting=true;
				} else {
					if($size[0]>200) {
						$foto_table.="<img src=\"".$path."thumbnail.php?file=".urlencode($value)."\" alt=\"".html("klikopdefoto","imagetable")."\" width=\"200\" height=\"150\" border=\"0\">";
					} else {
						$foto_table.="<img src=\"".$path."pic/cms/".$value."\" alt=\"\" width=\"200\" height=\"150\">";
					}
				}
				$foto_table.="</TD>";
			}
			$foto_table.="</TR>";
		}
		while(list($key,$value)=@each($temp["picbreed"])) {
			$foto_table.="<TR><TD colspan=\"2\" align=\"center\"><img src=\"".$path."pic/cms/".$value."\" alt=\"\" width=\"400\" height=\"150\"></TD></TR>";
		}

		echo "<TABLE width=\"660\" border=\"0\" class=\"toonacctabel\" cellspacing=\"0\">";
		echo "<TR><TH>".html("fotos","vars")."</TH><TH style=\"text-align:right;font-size:0.7em;\" class=\"noprint\">";
		if($vergroting) {
			echo "<img src=\"".$path."pic/foto_doorklik.png\" alt=\"\" width=\"15\" height=\"11\" border=\"0\"> = ".html("klikvoorvergroting","imagetable");
		} else {
			echo "&nbsp;";
		}
		echo "</TH></TR>";
		echo "<TR><TD colspan=\"2\"><TABLE border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"8\" class=\"geenborders\">";

		echo $foto_table;

		echo "</TABLE></TD></TR>";
		echo "<TR><TD colspan=\"2\" class=\"tabelkleur\"><FONT color=\"#FFFFFF\" size=\"1\"><B>".html("fotosindicatief","imagetable")."</B></FONT></TD></TR>";
		echo "</TABLE>";
	}
}

function imagearray($onderdeel,$id,$dirprefix="") {
	$mappen=array("","_breed");
	while(list($key,$value)=each($onderdeel)) {
		$onderdeel_teller++;
		reset($mappen);
		$dir=$dirprefix."pic/cms/".$value;
		if(file_exists($dir)) {
			$d=dir($dir);
			while($entry=$d->read()) {
#				if(ereg("^".$id[$key]."-([0-9]{1,3})\.jpg$",$entry,$regs)) {
				if(preg_match("/^".$id[$key]."-([0-9]{1,3})\.jpg$/",$entry,$regs)) {
#					if(ereg("_breed",$value)) $soort="picbreed"; else $soort="pic";
					if(preg_match("/_breed/",$value)) $soort="picbreed"; else $soort="pic";
					$temp[$soort][$onderdeel_teller."_".substr("000000".$regs[1],-7)]=$value."/".$entry;
				}
			}
		}
	}
	@ksort($temp["pic"]);
	@ksort($temp["picbreed"]);
	return $temp;
}

function imageurl($file,$width="",$height="") {
	global $vars, $unixdir;
	if($vars["accommodatie_word"]) {
		# voor aanmaken Word-bestand (reisagenten): origineel gebruiken
		$return=$vars["path"]."pic/cms/".$file;
	} else {
		# de rest: nagaan of er cache of thumbnail moet worden getoond
		$cachefile="pic/cms/_imgcache/".$width."x".$height."-".preg_replace("/\//","-",$file);
		if(file_exists($unixdir.$cachefile) and filemtime($unixdir.$cachefile)==@filemtime($unixdir."pic/cms/".$file)) {
			$return=$vars["path"].$cachefile."?cache=".filemtime($cachefile);
		} else {
			$return=$vars["path"]."thumbnail.php?file=".urlencode($file);
			if($width or $height) {
				$return.="&w=".$width."&h=".$height;
			}
		}
	}
	$return=wt_he($return);
	return $return;
}

function balk() {
	global $pageid,$path,$vars,$hr,$flex_tarief;
#	$bgcolor="#0D3E88";
	if($hr) {
		$bgcolor=$hr;
	} else {
		$bgcolor="#D5E1F9";
	}
	if(($_GET["fap"] and $_GET["fad"]) or $pageid=="top10" or $pageid=="aanbiedingen" or $pageid=="specialeselectie" or (($vars["id"]=="accommodaties" or $vars["id"]=="zoek-en-boek" or $vars["id"]=="chalets") and $_GET["fad"] and $vars["wederverkoop"])) {
		$colspan=5;
	} elseif($flex_tarief) {
		$colspan=5;
	} else {
		$colspan=4;
	}
	return "<TR style=\"background-color:#FFFFFF;\"><TD colspan=\"".$colspan."\"><TABLE width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><TR><TD bgcolor=\"".$bgcolor."\"><img src=\"".$path."pic/leeg.gif\" width=\"1\" height=\"2\" alt=\"\"></TD></TR></TABLE></TD></TR>";
}

function bereken_tarief($typeid,$seizoen,$week,$aantalpersonen) {
	global $vars;
	if(!$week) {
		# Alle tarieven laden
		$db->query("SELECT p.personen, p.week, p.prijs FROM tarief_personen p WHERE p.type_id='".addslashes($typeid)."' AND p.seizoen_id='".addslashes($seizoenid)."' ORDER BY personen DESC, week;");
		while($db->next_record()) {
			$return[$db->f("personen")][$db->f("week")]=$db->f("prijs");
		}
	}
	return $return;
}

function bereken_flex_tarief($typeid,$flex_aankomstdatum,$verblijfsduur,$flex_vertrekdatum=0) {
	global $vars;
	$db=new DB_sql;
	if($flex_vertrekdatum) {
		$aantalnachten=round(($flex_vertrekdatum-$flex_aankomstdatum)/86400);
	} else {
		if(preg_match("/([0-9]+)n/",$verblijfsduur,$regs)) {
			$aantalnachten=$regs[1];
		} else {
			$aantalnachten=$verblijfsduur*7;
			$weken=true;
		}
	}
	if($aantalnachten>0) {
		if($aantalnachten==1) {
			$return["aantalnachten_text"]="1 ".txt("nacht","vars");
		} else {
			if($aantalnachten==7 and $weken) {
				$return["aantalnachten_text"]="1 ".txt("week","vars");
			} else {
				if(fmod($aantalnachten,7)==0 and $weken) {
					$return["aantalnachten_text"]=($aantalnachten/7)." ".txt("weken","vars");
				} else {
					$return["aantalnachten_text"]=$aantalnachten." ".txt("nachten","vars");
				}
			}
		}
	}

	$return["aantalnachten"]=$aantalnachten;

	# kijken of het flexibele datums zijn
	if(@date("w",$flex_aankomstdatum)==6 and fmod($aantalnachten,7)==0) {
		# niet flexibel
		unset($weekinquery);
		for($i=0;$i<round($aantalnachten/7);$i++) {
			$unixtime=mktime(0,0,0,date("m",$flex_aankomstdatum),date("d",$flex_aankomstdatum)+(7*$i),date("Y",$flex_aankomstdatum));
			$weekinquery.=",".$unixtime;
			$verblijfsduur_meerdere_weken_array[$unixtime]=true;
		}

		# tarief per week opvragen
		if($weekinquery) {
			$db->query("SELECT DISTINCT tr.week, tr.c_verkoop_site, tr.seizoen_id FROM tarief tr WHERE tr.type_id='".addslashes($typeid)."' AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tr.week IN (".substr($weekinquery,1).");");
			if($db->num_rows()>0 and $db->num_rows()==@count($verblijfsduur_meerdere_weken_array)) {
				while($db->next_record()) {
					$tarief_meerdere_weken[$typeid][$db->f("week")]=$db->f("c_verkoop_site");
					$te_doorlopen_seizoenen[$db->f("seizoen_id")]=true;
				}
			}
		}

		# aanbiedingen voor alle datums ophalen
		@reset($te_doorlopen_seizoenen);
		while(list($key,$value)=@each($te_doorlopen_seizoenen)) {
			$aanbieding[$key]=aanbiedinginfo($typeid,$key);
		}

		unset($tarief);
		if(is_array($tarief_meerdere_weken)) {
			reset($verblijfsduur_meerdere_weken_array);
			while(list($key,$value)=each($verblijfsduur_meerdere_weken_array)) {
				$tarief_temp=$tarief_meerdere_weken[$typeid][$key];

				# Zijn er aanbiedingen van toepassing?
				@reset($te_doorlopen_seizoenen);
				while(list($key2,$value2)=@each($te_doorlopen_seizoenen)) {
					if($aanbieding[$key2]["typeid_sort"][$typeid]["bedrag"][$key]) {
						$tarief_org=$tarief_temp;
						$tarief_temp=verwerk_aanbieding($tarief_temp,$aanbieding[$key2]["typeid_sort"][$typeid],$key);
					}
					$tarief=$tarief+$tarief_temp;
				}
			}
		}
		if($tarief) {
			$return["tarief"]=$tarief;
		} else {
#			trigger_error("tarief=0 bij tarief_meerdere_weken in functie bereken_flex_tarief",E_USER_NOTICE);
		}
	} else {
		# wel flexibel
		unset($andquery);
		for($i=0;$i<$aantalnachten;$i++) {
			$dag=mktime(0,0,0,date("m",$flex_aankomstdatum),date("d",$flex_aankomstdatum)+$i,date("Y",$flex_aankomstdatum));
			if($i==0) {
				$andquery.=" OR (dag='".$dag."' AND aankomstdag=1 AND minimum_aantal_nachten<=".$aantalnachten." AND verkoop_site>0 AND beschikbaar=1)";
			} else {
				$andquery.=" OR (dag='".$dag."' AND verkoop_site>0 AND beschikbaar=1)";
			}
		}

		unset($inquery);
		if($andquery) {
			$db->query("SELECT count(type_id) AS aantal, sum(verkoop_site) AS verkoop_site FROM tarief_flex WHERE type_id='".addslashes($typeid)."' AND (".substr($andquery,3).") GROUP BY type_id;");
			while($db->next_record()) {
				if($db->f("aantal")==$aantalnachten) {
					$return["tarief"]=$db->f("verkoop_site");
				}
			}
		}
	}

	return $return;
}

function toonafstand($afstand,$extra,$maat) {
	$return=$afstand;
	if($extra) {
		if(ereg("^[0-9]+$",$extra)) {
			$return.=" - ".$extra." ".$maat;
		} else {
			$return.=" ".$maat." (".$extra.")";
		}
	} else {
		$return.=" ".$maat;
	}
	return $return;
}

function vertrekdagaanpassing($aankomstdatum,$soort,$afwijking="") {
	$return=$aankomstdatum;
	if($soort==1) {
		# Unieke afwijking
		$data=@split("\n",$afwijking);
		while(list($key,$value)=@each($data)) {
			$value=trim($value);
			if(ereg("^([0-9]{4}) (.[0-9])$",$value,$regs)) {
				if(date("dm",$aankomstdatum)==$regs[1]) {
					$return=mktime(0,0,0,date("m",$aankomstdatum),date("d",$aankomstdatum)+intval($regs[2]),date("Y",$aankomstdatum));
				}
			}
		}
	} elseif($soort==2) {
		# Zondag-zondag
		$return=mktime(0,0,0,date("m",$aankomstdatum),date("d",$aankomstdatum)+1,date("Y",$aankomstdatum));
	}
	return $return;
}

function chalet_log($text,$aangepast=false,$save_in_db=false) {
	global $gegevens,$mustlogin,$login,$cron;
	if($_COOKIE["loginuser"]["chalet"]<>1 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		if($gegevens["stap1"]["log"]) $gegevens["stap1"]["log"].="\n";
		$gegevens["stap1"]["log"].=time()."-";
		if($mustlogin or $_COOKIE["loginuser"]["chalet"]) {
			$gegevens["stap1"]["log"].="c".($_COOKIE["loginuser"]["chalet"]<>3 ? $_COOKIE["loginuser"]["chalet"] : "")."-";
			$lasteditor=$_COOKIE["loginuser"]["chalet"];
		} elseif($cron) {
			$gegevens["stap1"]["log"].="s-";
			$lasteditor=0;
		} else {
			$gegevens["stap1"]["log"].="k-";
			$lasteditor=0;
		}
		if($gegevens["stap_voltooid"][$_GET["stap"]] or $aangepast) {
			$gegevens["stap1"]["log"].="a-";
		} else {
			$gegevens["stap1"]["log"].="i-";
		}
		$gegevens["stap1"]["log"].=" ".$text;
		$db=new DB_sql;
		if($save_in_db) {
			$db->query("UPDATE boeking SET log='".addslashes($gegevens["stap1"]["log"])."', bewerkdatetime=NOW(), lasteditor='".addslashes($lasteditor)."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		} else {
			$db->query("UPDATE boeking SET bewerkdatetime=NOW(), lasteditor='".addslashes($lasteditor)."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		}

		# ook opslaan in cmslog (specialtype=2)
		if($_COOKIE["loginuser"]["chalet"] and $gegevens["stap1"]["boekingid"]) {
			$db->query("INSERT INTO cmslog SET user_id='".addslashes($_COOKIE["loginuser"]["chalet"])."', specialtype=2, cms_id='0', cms_name='boeking', record_id='".addslashes($gegevens["stap1"]["boekingid"])."', record_name='".($gegevens["stap1"]["boekingsnummer"] ? $gegevens["stap1"]["boekingsnummer"] : "aanvraagnummer ".$gegevens["stap1"]["boekingid"])."', table_name='boeking', field='', field_name='', field_type='', previous='', now='', url='".addslashes($_SERVER["REQUEST_URI"])."', boekinglogtekst='".addslashes($text)."', savedate=NOW();");
		}
	}
	return;
}


/*
 * Function boeking_log
 *
 * saves a log entry for a booking (saves it immediately, the function chalet_log() can be used to create multiple entries, without saving).
 *
 * @param integer $boeking_id the id of the booking
 * @param string $text the text to add to the log
 * @return boolean Whether the boeking_id could be found
 */
function boeking_log($boeking_id, $text) {

	global $mustlogin, $login, $cron;

	$db=new DB_sql;

	if($_COOKIE["loginuser"]["chalet"]<>1 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

		$db->query("SELECT log, boekingsnummer FROM boeking WHERE boeking_id='".intval($boeking_id)."';");
		if($db->next_record()) {

			$log = $db->f("log");
			$boekingsnummer = $db->f("boekingsnummer");

			if($log) $log.="\n";
			$log.=time()."-";
			if($mustlogin or $_COOKIE["loginuser"]["chalet"]) {
				$log.="c".($_COOKIE["loginuser"]["chalet"]<>3 ? $_COOKIE["loginuser"]["chalet"] : "")."-";
				$lasteditor=$_COOKIE["loginuser"]["chalet"];
			} elseif($cron) {
				$log.="s-";
				$lasteditor=0;
			} else {
				$log.="k-";
				$lasteditor=0;
			}

			$log.="i-";
			$log.=" ".$text;
			$db->query("UPDATE boeking SET log='".addslashes($log)."', bewerkdatetime=NOW(), lasteditor='".addslashes($lasteditor)."' WHERE boeking_id='".addslashes($boeking_id)."';");

			# ook opslaan in cmslog (specialtype=2)
			if($_COOKIE["loginuser"]["chalet"] and $boeking_id) {
				$db->query("INSERT INTO cmslog SET user_id='".addslashes($_COOKIE["loginuser"]["chalet"])."', specialtype=2, cms_id='0', cms_name='boeking', record_id='".addslashes($boeking_id)."', record_name='".($boekingsnummer ? $boekingsnummer : "aanvraagnummer ".$boeking_id)."', table_name='boeking', field='', field_name='', field_type='', previous='', now='', url='".addslashes($_SERVER["REQUEST_URI"])."', boekinglogtekst='".addslashes($text)."', savedate=NOW();");
			}

			return true;

		} else {
			return false;
		}
	}
}

function boeking_veiligheid($boekingid) {
	return substr(md5("_WT_SECURITY_".$boekingid),0,8);
}

function terugnaaracc() {
	global $vars,$path;
	$db=new DB_sql;
	if($_GET["backtypeid"]) {
		$db->query("SELECT a.soortaccommodatie, a.naam, t.naam AS tnaam, t.type_id, l.begincode FROM accommodatie a, type t, plaats p, land l WHERE t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND t.type_id='".addslashes($_GET["backtypeid"])."';");
		if($db->next_record()) {
			$accnaam=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "");
			echo "<div style=\"padding-bottom:15px;font-size:0.8em;\"><a href=\"".$path.txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/".($_GET["backqs"] ? "?".$_GET["backqs"] : "")."\">&lt; &lt; ".html("terugnaaracc","vars",array("v_accommodatienaam"=>$accnaam))."</a></div>";
		}
	}
}

function voorraad_bijwerken($type_id,$week,$plusmin,$garantie,$allotment,$vervallen_allotment,$optie_leverancier,$xml,$request,$optie_klant,$alleen_wijzigen_indien_brutoprijs=true,$logvia=0) {
	global $vars,$login;
	$db=new DB_sql;
	$db2=new DB_sql;
	$db->query("SELECT t.type_id, t.verzameltype, t.verzameltype_parent, ta.voorraad_garantie, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, ta.voorraad_optie_leverancier, ta.voorraad_xml, ta.voorraad_request, ta.voorraad_optie_klant, ta.voorraad_bijwerken, ta.beschikbaar, ta.seizoen_id FROM tarief ta, type t WHERE ta.type_id=t.type_id AND t.type_id='".addslashes($type_id)."' AND ta.week='".addslashes($week)."';");
	if($db->next_record()) {
		if($db->f("verzameltype")) {
#			trigger_error("functie voorraad_bijwerken toegepast op verzameltype ".$db->f("type_id"),E_USER_NOTICE);
		} else {
			$new_garantie=$db->f("voorraad_garantie");
			$new_allotment=$db->f("voorraad_allotment");
			$new_vervallen_allotment=$db->f("voorraad_vervallen_allotment");
			$new_optie_leverancier=$db->f("voorraad_optie_leverancier");
			$new_xml=$db->f("voorraad_xml");
			$new_request=$db->f("voorraad_request");
			$new_optie_klant=$db->f("voorraad_optie_klant");
			$beschikbaar=$db->f("beschikbaar");
			$beschikbaar_voorheen=$db->f("beschikbaar");
			$bijwerken=$db->f("voorraad_bijwerken");
			$seizoenid=$db->f("seizoen_id");
			if($plusmin) {
				if(wt_has_value($garantie)) $new_garantie=$new_garantie+$garantie;
				if(wt_has_value($allotment)) $new_allotment=$new_allotment+$allotment;
				if(wt_has_value($vervallen_allotment)) $new_vervallen_allotment=$new_vervallen_allotment+$vervallen_allotment;
				if(wt_has_value($optie_leverancier)) $new_optie_leverancier=$new_optie_leverancier+$optie_leverancier;
				if(wt_has_value($xml)) $new_xml=$new_xml+$xml;
				if(wt_has_value($request)) $new_request=$new_request+$request;
				if(wt_has_value($optie_klant)) $new_optie_klant=$new_optie_klant+$optie_klant;

				if($new_garantie<0) $new_garantie=0;
				if($new_xml<0) $new_xml=0;

				# wijzigingen bepalen (voor in archief)
				if(wt_has_value($garantie)) $wijzig_garantie=$garantie;
				if(wt_has_value($allotment)) $wijzig_allotment=$allotment;
				if(wt_has_value($vervallen_allotment)) $wijzig_vervallen_allotment=$vervallen_allotment;
				if(wt_has_value($optie_leverancier)) $wijzig_optie_leverancier=$optie_leverancier;
				if(wt_has_value($xml)) $wijzig_xml=$xml;
				if(wt_has_value($request)) $wijzig_request=$request;
				if(wt_has_value($optie_klant)) $wijzig_optie_klant=$optie_klant;

			} else {
				if(wt_has_value($garantie)) $new_garantie=$garantie;
				if(wt_has_value($allotment)) $new_allotment=$allotment;
				if(wt_has_value($vervallen_allotment)) $new_vervallen_allotment=$vervallen_allotment;
				if(wt_has_value($optie_leverancier)) $new_optie_leverancier=$optie_leverancier;
				if(wt_has_value($xml)) $new_xml=$xml;
				if(wt_has_value($request)) $new_request=$request;
				if(wt_has_value($optie_klant)) $new_optie_klant=$optie_klant;

				# wijzigingen bepalen (voor in archief)
				if(wt_has_value($garantie)) $wijzig_garantie=$garantie-$db->f("voorraad_garantie");
				if(wt_has_value($allotment)) $wijzig_allotment=$allotment-$db->f("voorraad_allotment");
				if(wt_has_value($vervallen_allotment)) $wijzig_vervallen_allotment=$vervallen_allotment-$db->f("voorraad_vervallen_allotment");
				if(wt_has_value($optie_leverancier)) $wijzig_optie_leverancier=$optie_leverancier-$db->f("voorraad_optie_leverancier");
				if(wt_has_value($xml)) $wijzig_xml=$xml-$db->f("voorraad_xml");
				if(wt_has_value($request)) $wijzig_request=$request-$db->f("voorraad_request");
				if(wt_has_value($optie_klant)) $wijzig_optie_klant=$optie_klant-$db->f("voorraad_optie_klant");
			}
			$bovenste5=$new_garantie+$new_allotment+$new_vervallen_allotment+$new_optie_leverancier+$new_xml+$new_request;
			unset($wijzig_beschikbaar);
			if($bijwerken) {
				if($bovenste5>0) {
					$beschikbaar=1;
				} else {
					$beschikbaar=0;
				}
				if($beschikbaar_voorheen<>$beschikbaar) {
					if($beschikbaar==1) {
						# 1 = gewijzigd naar "beschikbaar"
						$wijzig_beschikbaar=1;
					} else {
						# 2 = gewijzigd naar "niet beschikbaar"
						$wijzig_beschikbaar=2;
					}
				}
			}
			$query="UPDATE tarief SET voorraad_garantie='".addslashes($new_garantie)."', voorraad_allotment='".addslashes($new_allotment)."', voorraad_vervallen_allotment='".addslashes($new_vervallen_allotment)."', voorraad_optie_leverancier='".addslashes($new_optie_leverancier)."', voorraad_xml='".addslashes($new_xml)."', voorraad_request='".addslashes($new_request)."', voorraad_optie_klant='".addslashes($new_optie_klant)."', beschikbaar='".addslashes($beschikbaar)."' WHERE type_id='".addslashes($type_id)."' AND week='".addslashes($week)."'";
			if($alleen_wijzigen_indien_brutoprijs) {
				$query.=" AND (bruto>0 OR c_bruto>0 OR arrangementsprijs>0);";
			}
			$db->query($query);

			# logquery uitvoeren indien UPDATE-query is uitgevoerd
			if($db->affected_rows()>0) {
				# wijzigingen loggen
				if(is_object($login)) {
					$werknemerid=$login->user_id;
				} else {
					$werknemerid="0";
				}
				if($_SERVER["REQUEST_URI"]) {
					$request=$_SERVER["REQUEST_URI"];
				} else {
					$request=$_SERVER["PHP_SELF"];
				}
				$db2->query("INSERT INTO beschikbaar_archief SET type_id='".addslashes($type_id)."', seizoen_id='".addslashes($seizoenid)."', week='".addslashes($week)."', datumtijd=NOW(), beschikbaar='".addslashes($wijzig_beschikbaar)."', garantie='".addslashes($wijzig_garantie)."', allotment='".addslashes($wijzig_allotment)."', vervallen_allotment='".addslashes($wijzig_vervallen_allotment)."', optie_leverancier='".addslashes($wijzig_optie_leverancier)."', xml='".addslashes($wijzig_xml)."', request='".addslashes($wijzig_request)."', optie_klant='".addslashes($wijzig_optie_klant)."', totaal='".addslashes($bovenste5)."', user_id='".addslashes($werknemerid)."', via='".$logvia."', request_uri='".addslashes($request)."';");
			}

			if($db->f("verzameltype_parent")) {
				#
				# Hoort bij een verzameltype: voorraad bovenliggend verzameltype bijwerken
				#

				$verzameltype_parent=$db->f("verzameltype_parent");
				unset($verzameltype_inquery);
				$db->query("SELECT type_id FROM type WHERE verzameltype_parent='".addslashes($verzameltype_parent)."';");
				while($db->next_record()) {
					if($verzameltype_inquery) $verzameltype_inquery.=",".$db->f("type_id"); else $verzameltype_inquery=$db->f("type_id");
				}

				$db->query("SELECT week, beschikbaar, voorraad_garantie, voorraad_allotment, voorraad_vervallen_allotment, voorraad_optie_leverancier, voorraad_xml, voorraad_request, voorraad_optie_klant FROM tarief WHERE type_id IN (".$verzameltype_inquery.") AND week='".addslashes($week)."';");
				while($db->next_record()) {
					if($db->f("beschikbaar")) {
						$opslaan[$db->f("week")]["beschikbaar"]=1;
					}
					$opslaan[$db->f("week")]["voorraad_garantie"]+=$db->f("voorraad_garantie");
					$opslaan[$db->f("week")]["voorraad_allotment"]+=$db->f("voorraad_allotment");
					$opslaan[$db->f("week")]["voorraad_vervallen_allotment"]+=$db->f("voorraad_vervallen_allotment");
					$opslaan[$db->f("week")]["voorraad_optie_leverancier"]+=$db->f("voorraad_optie_leverancier");
					$opslaan[$db->f("week")]["voorraad_xml"]+=$db->f("voorraad_xml");
					$opslaan[$db->f("week")]["voorraad_request"]+=$db->f("voorraad_request");
					$opslaan[$db->f("week")]["voorraad_optie_klant"]+=$db->f("voorraad_optie_klant");

					$weekgehad[$db->f("week")]=true;
				}
				if(is_array($weekgehad)) {
					while(list($key,$value)=each($weekgehad)) {
						$db->query("UPDATE tarief SET beschikbaar='".addslashes($opslaan[$key]["beschikbaar"])."', voorraad_garantie='".addslashes($opslaan[$key]["voorraad_garantie"])."', voorraad_allotment='".addslashes($opslaan[$key]["voorraad_allotment"])."', voorraad_vervallen_allotment='".addslashes($opslaan[$key]["voorraad_vervallen_allotment"])."', voorraad_optie_leverancier='".addslashes($opslaan[$key]["voorraad_optie_leverancier"])."', voorraad_xml='".addslashes($opslaan[$key]["voorraad_xml"])."', voorraad_request='".addslashes($opslaan[$key]["voorraad_request"])."', voorraad_optie_klant='".addslashes($opslaan[$key]["voorraad_optie_klant"])."' WHERE type_id='".$verzameltype_parent."' AND week='".$key."';");
						if($db->affected_rows()>0 and $opslaan[$key]["beschikbaar"]<>$beschikbaar_voorheen) {
							# beschikaarheid loggen
							if(is_object($login)) {
								$werknemerid=$login->user_id;
							} else {
								$werknemerid="0";
							}
#							$db2->query("INSERT INTO beschikbaar_archief SET type_id='".addslashes($type_id)."', seizoen_id='".addslashes($seizoenid)."', week='".addslashes($week)."', datumtijd=NOW(), beschikbaar='".addslashes($opslaan[$key]["beschikbaar"])."', user_id='".addslashes($werknemerid)."', via='4', request_uri='".addslashes($_SERVER["REQUEST_URI"])."';");
						}
					}
				}
			}

			// gekoppelde voorraad bijwerken
			$voorraad_gekoppeld=new voorraad_gekoppeld;
			$voorraad_gekoppeld->vanaf_prijzen_berekenen($type_id);
			$voorraad_gekoppeld->koppeling_uitvoeren_na_einde_script();

			return $query."\n";
		}
	}
}

function boekjaar($unixtime) {
	if(date("m",$unixtime)>=7) {
		$return=date("Y",$unixtime);
	} else {
		$return=(date("Y",$unixtime)-1);
	}
	return $return;
}

function boekjaar_unixtime($unixtime) {
	# geeft de unixtime van de laatste seconde van het huidige (via unixtime opgegeven) boekjaar
	if(date("m",$unixtime)>=7) {
		$jaar=date("Y",$unixtime)+1;
	} else {
		$jaar=date("Y",$unixtime);
	}
	$return=mktime(23,59,59,6,30,$jaar);
	return $return;
}

function boeking_bewerkbeveiliging($boekingid,$type) {
	$db=new DB_sql;
	$db->query("UPDATE boeking SET");
	return true;
}

function bereken_bijkomendekosten($boekingid) {
	global $vars;

	$gegevens=get_boekinginfo($boekingid);

	$db=new DB_sql;
	$db2=new DB_sql;

	while(list($key,$value)=@each($gegevens["stap4"]["bijkomendekosten"])) {
		if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$key; else $bijkomendekosten_inquery=$key;
	}

	# Oude gegevens inlezen
	$db->query("SELECT bijkomendekosten_id, naam, verkoop, inkoop, korting, omzetbonus, hoort_bij_accommodatieinkoop, optiecategorie FROM extra_optie WHERE bijkomendekosten_id IS NOT NULL AND boeking_id='".$gegevens["stap1"]["boekingid"]."';");
	while($db->next_record()) {
		$voorheen[$db->f("bijkomendekosten_id")]["naam"]=$db->f("naam");
		$voorheen[$db->f("bijkomendekosten_id")]["verkoop"]=$db->f("verkoop");
		$voorheen[$db->f("bijkomendekosten_id")]["inkoop"]=$db->f("inkoop");
		$voorheen[$db->f("bijkomendekosten_id")]["korting"]=$db->f("korting");
		$voorheen[$db->f("bijkomendekosten_id")]["omzetbonus"]=$db->f("omzetbonus");
		$voorheen[$db->f("bijkomendekosten_id")]["hoort_bij_accommodatieinkoop"]=$db->f("hoort_bij_accommodatieinkoop");
		$voorheen[$db->f("bijkomendekosten_id")]["optiecategorie"]=$db->f("optiecategorie");
	}

	# Bijkomende kosten gekoppeld aan accommodatie en type
	$db->query("SELECT a.bijkomendekosten1_id, a.bijkomendekosten2_id, a.bijkomendekosten3_id, a.bijkomendekosten4_id, a.bijkomendekosten5_id, a.bijkomendekosten6_id, t.bijkomendekosten1_id AS tbijkomendekosten1_id, t.bijkomendekosten2_id AS tbijkomendekosten2_id, t.bijkomendekosten3_id AS tbijkomendekosten3_id, t.bijkomendekosten4_id AS tbijkomendekosten4_id, t.bijkomendekosten5_id AS tbijkomendekosten5_id, t.bijkomendekosten6_id AS tbijkomendekosten6_id FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($gegevens["stap1"]["typeid"])."';");
	if($db->next_record()) {
		for($i=1;$i<=6;$i++) {
			if($db->f("bijkomendekosten".$i."_id")) {
				if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db->f("bijkomendekosten".$i."_id"); else $bijkomendekosten_inquery=$db->f("bijkomendekosten".$i."_id");
			}
			if($db->f("tbijkomendekosten".$i."_id")) {
				if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db->f("tbijkomendekosten".$i."_id"); else $bijkomendekosten_inquery=$db->f("tbijkomendekosten".$i."_id");
			}
		}
	}

	# Bijkomende kosten gekoppeld aan skipas
	if($gegevens["stap1"]["accinfo"]["skipasid"]) {
		$db->query("SELECT bijkomendekosten_id FROM skipas WHERE skipas_id='".addslashes($gegevens["stap1"]["accinfo"]["skipasid"])."';");
		if($db->next_record()) {
			if($db->f("bijkomendekosten_id")) {
				if($bijkomendekosten_inquery) $bijkomendekosten_inquery.=",".$db->f("bijkomendekosten_id"); else $bijkomendekosten_inquery=$db->f("bijkomendekosten_id");
			}
		}
	}

	# Oude gegevens wissen (alleen bij bijkomende kosten die nu ook nog aanwezig zijn bij de accommodatie/type)
	if($bijkomendekosten_inquery) {
		$db->query("DELETE FROM extra_optie WHERE bijkomendekosten_id IS NOT NULL AND bijkomendekosten_id IN (".$bijkomendekosten_inquery.") AND boeking_id='".$gegevens["stap1"]["boekingid"]."';");
	}

	# Alle deelnemers in $alle_deelnemers
	for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
		if($alle_deelnemers) $alle_deelnemers.=",".$i; else $alle_deelnemers=$i;
	}

	if($bijkomendekosten_inquery) {
		$db->query("SELECT b.bijkomendekosten_id, b.gekoppeldaan, b.hoort_bij_accommodatieinkoop, b.optiecategorie, bt.verkoop, bt.inkoop, bt.korting, bt.omzetbonus, b.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam, b.perboekingpersoon, b.min_leeftijd, b.max_leeftijd, b.zonderleeftijd FROM bijkomendekosten b, bijkomendekosten_tarief bt WHERE bt.bijkomendekosten_id=b.bijkomendekosten_id AND bt.seizoen_id='".$gegevens["stap1"]["seizoenid"]."' AND bt.week='".$gegevens["stap1"]["aankomstdatum"]."' AND b.bijkomendekosten_id IN (".$bijkomendekosten_inquery.");");
		while($db->next_record()) {
			unset($save);
			if($db->f("perboekingpersoon")==1) {
				$save["persoonnummer"]="alg";
			} else {
				$save["persoonnummer"]="pers";
				if($db->f("gekoppeldaan")==3) {
					$save["deelnemers"]=$gegevens["stap4"]["bijkomendekosten"][$db->f("bijkomendekosten_id")];
				} else {
					if($db->f("min_leeftijd") or $db->f("max_leeftijd")) {
						for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
							if(isset($gegevens["stap3"][$i]["geboortedatum"])) {
								$leeftijd=wt_leeftijd($gegevens["stap3"][$i]["geboortedatum"],mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
								if($db->f("min_leeftijd") and $db->f("max_leeftijd")) {
									if($leeftijd>=$db->f("min_leeftijd") and $leeftijd<=$db->f("max_leeftijd")) {
										if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
									}
								} elseif($db->f("min_leeftijd")) {
									if($leeftijd>=$db->f("min_leeftijd")) {
										if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
									}
								} elseif($db->f("max_leeftijd")) {
									if($leeftijd<=$db->f("max_leeftijd")) {
										if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
									}
								}
							} elseif($db->f("zonderleeftijd")) {
								if($save["deelnemers"]) $save["deelnemers"].=",".$i; else $save["deelnemers"]=$i;
							}
						}
					} else {
						$save["deelnemers"]=$alle_deelnemers;
					}
				}
			}
			if($voorheen[$db->f("bijkomendekosten_id")]) {
				$save["naam"]=$voorheen[$db->f("bijkomendekosten_id")]["naam"];
				$save["verkoop"]=$voorheen[$db->f("bijkomendekosten_id")]["verkoop"];
				$save["inkoop"]=$voorheen[$db->f("bijkomendekosten_id")]["inkoop"];
				$save["korting"]=$voorheen[$db->f("bijkomendekosten_id")]["korting"];
				$save["omzetbonus"]=$voorheen[$db->f("bijkomendekosten_id")]["omzetbonus"];
				$save["hoort_bij_accommodatieinkoop"]=$voorheen[$db->f("bijkomendekosten_id")]["hoort_bij_accommodatieinkoop"];
				$save["optiecategorie"]=$voorheen[$db->f("bijkomendekosten_id")]["optiecategorie"];
			} else {
				$save["naam"]=$db->f("naam");
				$save["verkoop"]=$db->f("verkoop");
				$save["inkoop"]=$db->f("inkoop");
				$save["korting"]=$db->f("korting");
				$save["omzetbonus"]=$db->f("omzetbonus");
				$save["hoort_bij_accommodatieinkoop"]=$db->f("hoort_bij_accommodatieinkoop");
				$save["optiecategorie"]=$db->f("optiecategorie");
			}
			if($save["verkoop"]<>0 or $save["verkoop"]=="0.00") {
				# Alleen opslaan als verkoopprijs is gezet
				$db2->query("INSERT INTO extra_optie SET boeking_id='".$gegevens["stap1"]["boekingid"]."', persoonnummer='".$save["persoonnummer"]."', deelnemers='".$save["deelnemers"]."', naam='".addslashes($save["naam"])."', verkoop='".addslashes($save["verkoop"])."', inkoop='".addslashes($save["inkoop"])."', korting='".addslashes($save["korting"])."', omzetbonus='".addslashes($save["omzetbonus"])."', hoort_bij_accommodatieinkoop='".addslashes($save["hoort_bij_accommodatieinkoop"])."', optiecategorie='".addslashes($save["optiecategorie"])."', bijkomendekosten_id='".addslashes($db->f("bijkomendekosten_id"))."';");
			}
		}
	}

	# Kijken of het factuurbedrag afwijkt van de berekende totale reissom (en dan "factuur_bedrag_wijkt_af" aanpassen)
	if($gegevens["stap1"]["totale_reissom"]>0) {
		$gegevens=get_boekinginfo($boekingid);
		if($gegevens["fin"]["totale_reissom"]>0) {
			$verschil=round($gegevens["stap1"]["totale_reissom"]-$gegevens["fin"]["totale_reissom"],2);
			if(abs($verschil)>0.01) {
				$factuur_bedrag_wijkt_af=1;
			} else {
				$factuur_bedrag_wijkt_af=0;
			}
			if($gegevens["stap1"]["aankomstdatum"]>time() and (($gegevens["stap1"]["factuur_bedrag_wijkt_af"] and !$factuur_bedrag_wijkt_af) or (!$gegevens["stap1"]["factuur_bedrag_wijkt_af"] and $factuur_bedrag_wijkt_af))) {
				$db2->query("UPDATE boeking SET factuur_bedrag_wijkt_af='".$factuur_bedrag_wijkt_af."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			}
		}
	}
}

function gekoppelde_acc($accid) {
	$db=new DB_sql;
	$num_rows=true;
	$gekoppeld[$accid]=true;
	while($num_rows) {
		$num_rows=false;
		reset($gekoppeld);
		while(list($key,$value)=each($gekoppeld)) {
			if(!$al_gehad[$key]) {
				$al_gehad[$key]=true;
				$db->query("SELECT accommodatie1_id, accommodatie2_id FROM accommodatie_koppeling WHERE accommodatie1_id='".addslashes($key)."' OR accommodatie2_id='".addslashes($key)."';");
				if($db->num_rows()) {
					$num_rows=true;
					while($db->next_record()) {
						$gekoppeld[$db->f("accommodatie1_id")]=$db->f("accommodatie2_id");
						$gekoppeld[$db->f("accommodatie2_id")]=$db->f("accommodatie1_id");
					}
				}
			}
		}
	}
	return $gekoppeld;
}

function gekoppelde_acc_opslaan_voor_zoekfunctie() {
	# gekoppelde accommodaties opslaan in tabel "accommodatie_koppeling_zoekfunctie" (is nodig voor zoek-en-boek)
	$db=new DB_sql;
	$db->query("SELECT accommodatie1_id FROM accommodatie_koppeling ORDER BY accommodatie1_id;");
	while($db->next_record()) {
		unset($gekoppeld);
		$gekoppeld=gekoppelde_acc($db->f("accommodatie1_id"));
		if(is_array($gekoppeld)) {
			ksort($gekoppeld);
			unset($mainkey);
			while(list($key,$value)=each($gekoppeld)) {
				if(!$mainkey) {
					$mainkey=$key;
				} else {
					$gekoppeld_opslaan[$mainkey][$key]=true;
				}
			}
		}
	}
	$db->query("DELETE FROM accommodatie_koppeling_zoekfunctie;");
	if(is_array($gekoppeld_opslaan)) {
		while(list($key,$value)=each($gekoppeld_opslaan)) {
			if(is_array($value)) {
				unset($savequery);
				while(list($key2,$value2)=each($value)) {
					$savequery.=",".$key2;
				}
				if($savequery) {
					$db->query("INSERT INTO accommodatie_koppeling_zoekfunctie SET accommodatie_id='".addslashes($key)."', gekoppeld='".addslashes(substr($savequery,1))."';");
				}
			}
		}
	}
}

function blokkenhoofdpagina($checkdate="") {
	global $vars,$txt,$txta;
	if(!$checkdate) $checkdate=time();
	$checkdate=mktime(0,0,0,date("m",$checkdate),date("d",$checkdate),date("Y",$checkdate));

	if($vars["seizoentype"]==1) {
		# Wintersites
		$andquery_website=" AND websites LIKE '%".$vars["website"]."%'";
	} elseif($vars["websitetype"]==7) {
		# Italissima
		$andquery_website=" AND websites LIKE '%".$vars["website"]."%'";
	}

	$db=new DB_sql;
	$db->query("SELECT blokhoofdpagina_id, titel".$vars["ttv"]." AS titel, omschrijving".$vars["ttv"]." AS omschrijving, link, UNIX_TIMESTAMP(begindatum) AS begindatum, UNIX_TIMESTAMP(einddatum) AS einddatum FROM blokhoofdpagina WHERE wzt='".$vars["seizoentype"]."'".($vars["websitetype"]==7 ? " AND italissima=1" : " AND italissima=0")." AND tonen=1".$andquery_website." ORDER BY volgorde, titel".$vars["ttv"].";");
	while($db->next_record()) {
		if(file_exists("pic/cms/blokkenhoofdpagina/".$db->f("blokhoofdpagina_id").".jpg")) {

			if($db->f("titel")<>"-") {
				$binnendatum=true;

				if($db->f("begindatum") and $db->f("begindatum")>$checkdate) {
					$binnendatum=false;
				}

				if($db->f("einddatum") and $db->f("einddatum")<$checkdate) {
					$binnendatum=false;
				}

				if($binnendatum) {
					$teller++;
					if(($vars["seizoentype"]==1 and $teller<=9) or ($vars["seizoentype"]==2 and $teller<=9)) {
						$return[$teller]["id"]=$db->f("blokhoofdpagina_id");
						$return[$teller]["titel"]=$db->f("titel");
						$return[$teller]["omschrijving"]=$db->f("omschrijving");
						$link=$db->f("link");
						$link=ereg_replace("&z=[0-9]+","",$link);
						$link=ereg_replace("/zoek-en-boek\.php","/".txt("menu_zoek-en-boek").".php",$link);
						$link=ereg_replace("/accommodatie/","/".txt("menu_accommodatie")."/",$link);
						if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
							$link="/chalet".$link;
						}
						$return[$teller]["link"]=$link;
					}
				}
			}
		}
	}
	return $return;
}

function tooninterneopmerkingen($text) {
	$return=$text;
#	"<wbr>";
	$return=wt_htmlent($return,true);
#	$return=wt_he($return);
#	$return=ereg_replace("/","/<wbr>",$return);
	$return=nl2br($return);
	return $return;
}

function check_kenmerken($all,$one) {
	$all_split=preg_split("@,@",$all);
	if(in_array($one,$all_split)) {
		return true;
	} else {
		return false;
	}
}

function cmshoofdpagina_inuitklappen($title,$html) {

}

function laatstezaterdag($time=0) {
	if(!$time) $time=time();
	if(date("w",$time)==6) {
		$return=mktime(0,0,0,date("m",$time),date("d",$time),date("Y",$time));
	} else {
		$minus=1+date("w",$time);
		$return=mktime(0,0,0,date("m",$time),date("d",$time)-$minus,date("Y",$time));
	}
	return $return;
}

function komendezaterdag($time=0) {
	if(!$time) $time=time();
	if(date("w",$time)==6) {
		$return=mktime(0,0,0,date("m",$time),date("d",$time),date("Y",$time));
	} else {
		$plus=abs(date("w",$time)-6);
		$return=mktime(0,0,0,date("m",$time),date("d",$time)+$plus,date("Y",$time));
	}
	return $return;
}

function dichtstbijzijnde_zaterdag($time) {
	if(date("w",$time)==0) $plusmin=-1;
	if(date("w",$time)==1) $plusmin=-2;
	if(date("w",$time)==2) $plusmin=-3;
	if(date("w",$time)==3) $plusmin=3;
	if(date("w",$time)==4) $plusmin=2;
	if(date("w",$time)==5) $plusmin=1;
	if(date("w",$time)==6) $plusmin=0;

	$return=mktime(0,0,0,date("m",$time),date("d",$time)+$plusmin,date("Y",$time));
	return $return;
}

function flex_bereken_vertrekdatum($aankomstdatum,$verblijfsduur) {
	if(preg_match("/([0-9]+)n/",$verblijfsduur,$regs)) {
		$aantalnachten=$regs[1];
	} else {
		$aantalnachten=intval($verblijfsduur)*7;
	}
	if($aankomstdatum) {
		$vertrekdatum=mktime(0,0,0,date("m",$aankomstdatum),date("d",$aankomstdatum)+$aantalnachten,date("Y",$aankomstdatum));
	}
	if($vertrekdatum) {
		return $vertrekdatum;
	}
}

function flex_is_dit_flexibel($aankomstdatum,$verblijfsduur) {
	$flexibel=false;
	if(date("w",$aankomstdatum)<>6) {
		$flexibel=true;
	}
	if(date("w",flex_bereken_vertrekdatum($aankomstdatum,$verblijfsduur))<>6) {
		$flexibel=true;
	}
#	if(round((flex_bereken_vertrekdatum($aankomstdatum,$verblijfsduur)-$aankomstdatum)/86400)>7) {
#		$flexibel=true;
#	}
	if($verblijfsduur<>"1" and $verblijfsduur<>"7n") {
		$flexibel=true;
	}
	return $flexibel;
}

function facebook_opengraph($info="") {
	# toon meta-tags in header t.b.v. Facebook en Twitter
	global $vars,$title,$id,$meta_description,$typeid,$temp_accid;
	if($vars["facebook_title"]) {
		$return.="<meta property=\"og:title\" content=\"".wt_he($vars["facebook_title"])."\" />\n";
	} else {
		$return.="<meta property=\"og:title\" content=\"".wt_he($vars["websitenaam"])."\" />\n";
	}
	$return.="<meta property=\"og:type\" content=\"website\" />\n";
	if($vars["canonical"]) {
		$return.="<meta property=\"og:url\" content=\"".wt_he($vars["canonical"])."\" />\n";
	} else {
		$return.="<meta property=\"og:url\" content=\"".wt_he(substr($vars["basehref"],0,-1).$_SERVER["REQUEST_URI"])."\" />\n";
	}
	$return.="<meta property=\"og:site_name\" content=\"".wt_he($vars["websitenaam"])."\" />\n";
	$return.="<meta property=\"fb:admins\" content=\"100002331327337\" />\n";
	if($vars["facebook_pageid"]) {
		$return.="<meta property=\"fb:page_id\" content=\"".wt_he($vars["facebook_pageid"])."\" />\n";
	}
	if($vars["facebook_opengraph_image"]) {
		if(is_array($vars["facebook_opengraph_image"])) {
			while(list($key,$value)=each($vars["facebook_opengraph_image"])) {
				$return.="<meta property=\"og:image\" content=\"".wt_he($value)."\" />\n";
			}
		} else {
			$return.="<meta property=\"og:image\" content=\"".wt_he($vars["facebook_opengraph_image"])."\" />\n";
		}
	} else {
		if($id=="toonaccommodatie") {
			if(file_exists("pic/cms/types_specifiek/".$typeid.".jpg")) {
				$afbeelding="types_specifiek/".$typeid;
			} elseif(file_exists("pic/cms/accommodaties/".$temp_accid.".jpg")) {
				$afbeelding="accommodaties/".$temp_accid;
			}
		} elseif($id=="blog" and $_GET["b"]) {
			$afbeelding="blog/".intval($_GET["b"])."-1";
		}
		if($afbeelding) {
			$return.="<meta property=\"og:image\" content=\"".wt_he($vars["basehref"]."pic/cms/".$afbeelding.".jpg")."\" />\n";
		} else {
			# logo als afbeelding
			if($vars["website"]=="Z") {
				# Zomerhuisje
				$logo_afbeelding="logo_zomerhuisje.gif";
			} elseif($vars["website"]=="N") {
				# Zomerhuisje.eu
				$logo_afbeelding="logo_zomerhuisje_eu.gif";
			} elseif($vars["website"]=="B") {
				# Chalet.be
				$logo_afbeelding="logo_chalet_be.gif";
			} elseif($vars["website"]=="T") {
				# Chalettour.nl
				$logo_afbeelding="logo_chalet_tour.gif";
			} elseif($vars["website"]=="E") {
				# Chalet.eu
				$logo_afbeelding="logo_chalet_eu.gif";
			} elseif($vars["website"]=="C") {
				# Chalet.nl
				$logo_afbeelding="logo_chalet.gif";
			} elseif($vars["website"]=="I") {
				# Italissima
				$logo_afbeelding="logo_italissima.gif";
			} elseif($vars["website"]=="K") {
				# Italissima
				$logo_afbeelding="logo_italissima.gif";
			} elseif($vars["website"]=="H") {
				# Italyhomes
				$logo_afbeelding="logo_italyhomes.gif";
			} elseif($vars["website"]=="X") {
				# Venturasol Wintersport
				$logo_afbeelding="logo_venturasol.png";
			} elseif($vars["website"]=="Y") {
				# Venturasol Vacances
				$logo_afbeelding="logo_venturasolvacances.png";
			}
			if($logo_afbeelding) {
				$return.="<meta property=\"og:image\" content=\"".wt_he($vars["basehref"]."pic/".$logo_afbeelding)."\" />\n";
			}
		}
	}

	if($meta_description) {
		$return.="<meta property=\"og:description\" content=\"".wt_he($meta_description)."\" />\n";
	} else {
		$return.="<meta property=\"og:description\" content=\"".wt_he(($meta_description ? $meta_description : ($title[$id]&&$id&&$id<>"index" ? $title[$id] : txt("description"))))."\" />\n";
	}

	# Twitter
	$return.="<meta property=\"twitter:card\" content=\"summary\" />\n";
	if($meta_description) {
		$return.="<meta property=\"twitter:description\" content=\"".wt_he($meta_description)."\" />\n";
	} else {
		$return.="<meta property=\"twitter:description\" content=\"".wt_he(($meta_description ? $meta_description : ($title[$id]&&$id&&$id<>"index" ? $title[$id] : txt("description"))))."\" />\n";
	}
	if($vars["twitter_user"]) {
		$return.="<meta property=\"twitter:site\" content=\"@".wt_he($vars["twitter_user"])."\" />\n";
	}

	return $return;
}

function affiliate_tracking($sale=false,$toon_tradetracker=true,$toon_cleafs=true,$data) {

	global $vars,$voorkant_cms,$gegevens;

	$db=new DB_sql;

	if($vars["chalettour_logged_in"] or $voorkant_cms or $gegevens["stap1"]["kortingscode_id"]) {
		# affiliate blokkeren indien:
		#	- boeking door reisbureau
		#	- boeking door chalet-medewerker
		#	- boeking met kortingscode
		#	- boeking door testsysteem
		unset($toon_tradetracker,$toon_cleafs);
	}

	// check for higher payout TradeTracker
	if($data["ordernummer"] and is_int($data["ordernummer"])) {
		$db->query("SELECT v.begincode, v.type_id FROM view_accommodatie v INNER JOIN boeking b USING (type_id) WHERE b.boeking_id=".intval($data["ordernummer"]).";");
		if($db->next_record()) {
			$accommodatiecode = $db->f("begincode").$db->f("type_id");
			$db->query("SELECT tradetracker_higher_payout FROM diverse_instellingen WHERE diverse_instellingen_id=1;");
			if($db->next_record()) {
				$tradetracker_higher_payout_array = preg_split("@\n@", $db->f("tradetracker_higher_payout"));
				if(is_array($tradetracker_higher_payout_array)) {
					foreach ($tradetracker_higher_payout_array as $key => $value) {
						$value = trim($value);
						if($value and $accommodatiecode and $value==$accommodatiecode) {
							$tradetracker_higher_payout = true;
						}
					}
				}
			}
		}
	}


	// # Bepalen wie voorrang krijgt: TradeTracker of Cleafs
	// if($toon_tradetracker and $toon_cleafs) {
	// 	if($_COOKIE["cleafs"] and $_COOKIE["tradetracker"]) {
	// 		if(intval($_COOKIE["cleafs"])>intval($_COOKIE["tradetracker"])) {
	// 			unset($toon_tradetracker);
	// 		} else {
	// 			unset($toon_cleafs);
	// 		}
	// 	} else {
	// 		if($_COOKIE["cleafs"]) {
	// 			unset($toon_tradetracker);
	// 		}
	// 		if($_COOKIE["tradetracker"]) {
	// 			unset($toon_cleafs);
	// 		}
	// 	}
	// }


	if($vars["website"]=="W") {
		#
		# TradeTracker SuperSki
		#

		# campaignID
		$tradetracker_campaignID="9318";

		# productID
		if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
			$tradetracker_productID="14181";
		} elseif($data["ordernummer"]=="contactaanvraag") {
			$tradetracker_productID="14180";
		} elseif($data["ordernummer"]=="vraagonsadvies") {
			$tradetracker_productID="14180";
		} else {
			$tradetracker_productID="14114";
		}

		# bedrag
		$tradetracker_bedrag="";

	} elseif($vars["website"]=="Z") {
		#
		# TradeTracker Zomerhuisje.nl
		#
		# campaignID
		$tradetracker_campaignID="958";

		# productID
		if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
			$tradetracker_productID="14253";
		} elseif($data["ordernummer"]=="contactaanvraag") {
			$tradetracker_productID="14252";
		} elseif($data["ordernummer"]=="vraagonsadvies") {
			$tradetracker_productID="14252";
		} else {
			$tradetracker_productID="1151";
		}

		# bedrag
		if($sale) {
			$tradetracker_bedrag=number_format($data["bedrag"],2,".","");
		} else {
			$tradetracker_bedrag=$data["bedrag"];
		}
	} elseif($vars["website"]=="C") {

		#
		# TradeTracker Chalet.nl
		#

		# campaignID
		$tradetracker_campaignID="202";

		# productID
		if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
			$tradetracker_productID="11786";
		} elseif($data["ordernummer"]=="contactaanvraag") {
			$tradetracker_productID="14239";
		} elseif($data["ordernummer"]=="vraagonsadvies") {
			$tradetracker_productID="16873";
		} else {
			if($tradetracker_higher_payout) {
				$tradetracker_productID="204";
			} else {
				$tradetracker_productID="204";
			}
		}

		# bedrag
		$tradetracker_bedrag="";
	} elseif($vars["website"]=="B") {

		#
		# TradeTracker Chalet.be
		#
		# campaignID
		$tradetracker_campaignID="8901";

		# productID
		if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
			$tradetracker_productID="13611";
		} elseif($data["ordernummer"]=="contactaanvraag") {
			$tradetracker_productID="14272";
		} elseif($data["ordernummer"]=="vraagonsadvies") {
			$tradetracker_productID="16911";
		} else {
			$tradetracker_productID="13471";
		}

		# bedrag
		$tradetracker_bedrag="";

	} elseif($vars["website"]=="E") {

		#
		# TradeTracker Chalet.eu
		#
		# campaignID
		$tradetracker_campaignID="15831";

		# productID
		if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
			$tradetracker_productID="23682";
		} elseif($data["ordernummer"]=="contactaanvraag") {
			$tradetracker_productID="23680";
		} elseif($data["ordernummer"]=="vraagonsadvies") {
			$tradetracker_productID="23679";
		} else {
			$tradetracker_productID="23671";
		}

		# bedrag
		$tradetracker_bedrag="";

		$tradetracker_dot_net = true;

	} elseif($vars["website"]=="I") {

		#
		# TradeTracker Italissima.nl
		#
		# campaignID
		$tradetracker_campaignID="7038";

		# productID
		if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
			$tradetracker_productID="14251";
		} elseif($data["ordernummer"]=="contactaanvraag") {
			$tradetracker_productID="14250";
		} elseif($data["ordernummer"]=="vraagonsadvies") {
			$tradetracker_productID="14250";
		} else {
			$tradetracker_productID="10696";
		}

		# bedrag
		if($sale) {
			$tradetracker_bedrag=number_format($data["bedrag"],2,".","");
		} else {
			$tradetracker_bedrag=$data["bedrag"];
		}
	} elseif($vars["website"]=="K") {

		#
		# TradeTracker Italissima.be
		#
		# campaignID
		$tradetracker_campaignID="9737";

		# productID
		if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
			$tradetracker_productID="14744";
		} elseif($data["ordernummer"]=="contactaanvraag") {
			$tradetracker_productID="14745";
		} elseif($data["ordernummer"]=="vraagonsadvies") {
			$tradetracker_productID="14745";
		} else {
			$tradetracker_productID="14727";
		}

		# bedrag
		if($sale) {
			$tradetracker_bedrag=number_format($data["bedrag"],2,".","");
		} else {
			$tradetracker_bedrag=$data["bedrag"];
		}
	}

	if($toon_tradetracker and $tradetracker_campaignID) {
		#
		# TradeTracker
		#

		// Get tracking data from the session created on the landingpage
		$trackingData = $_SESSION["TT2_" . $tradetracker_campaignID];
		$trackingType = "1";

		// If tracking data is empty
		if(!$trackingData) {
			// Get tracking data from the cookie created on the landingpage
			$trackingData = $_COOKIE["TT2_" . $tradetracker_campaignID];
			$trackingType = "2";
		}

		// URL encode tracking data
		$trackingData = urlencode($trackingData);

		$wanneer_binnengekomen=@split("::",$_COOKIE["TT2_202"]);
		if($wanneer_binnengekomen[2]>0) {
			$additional = "Doorklikmoment: ".date("d-m-Y H:i",$wanneer_binnengekomen[2])."u. - ";
		}
		$additional.=$extrainfo_tradetracker;

		// URL encode transaction information
		if($sale) {
			$orderID = urlencode("Aanvraagnummer_".$data["ordernummer"]);
		} else {
			$orderID = urlencode($data["ordernummer"]);
		}
		$orderAmount = urlencode($tradetracker_bedrag);
		$email = urlencode($email);
		$additional = urlencode($additional);

		// Send the complete report to TradeTracker
		if($vars["lokale_testserver"]) {
			if($tradetracker_dot_net) {
				echo "<img src=\"t".($sale ? "s" : "l").".tradetracker.net/?cid=$tradetracker_campaignID&amp;pid=$tradetracker_productID&amp;tid=$orderID".($sale ? "&amp;tam=$orderAmount" : "")."&amp;eml=$email&descrMerchant=additional&descrAffiliate=additional\" width=\"1\" height=\"1\" style=\"border:0;\" alt=\"\" />\n";
			} else {
				echo "<img src=\"ss.postvak.net/tradetrackertest/$tradetracker_campaignID/$tradetracker_productID/?trackingData=$trackingData&amp;conversionType=".($sale ? "sales" : "lead")."&amp;orderID=".$orderID.($sale ? "&amp;orderAmount=".$orderAmount : "")."&amp;email=$email&amp;additional=$additional\" width=\"1\" height=\"1\" style=\"border:0;\" alt=\"\" />\n";
			}
		} elseif($tradetracker_dot_net) {
			echo "<img src=\"https://t".($sale ? "s" : "l").".tradetracker.net/?cid=$tradetracker_campaignID&amp;pid=$tradetracker_productID&amp;tid=$orderID".($sale ? "&amp;tam=$orderAmount" : "")."&amp;eml=$email&descrMerchant=additional&descrAffiliate=additional\" width=\"1\" height=\"1\" style=\"border:0;\" alt=\"\" />\n";
		} else {
			echo "<img src=\"https://t".($sale ? "s" : "l").".tradetracker.nl/$tradetracker_campaignID/$tradetracker_productID/?trackingData=$trackingData&amp;conversionType=".($sale ? "sales" : "lead")."&amp;orderID=".$orderID.($sale ? "&amp;orderAmount=".$orderAmount : "")."&amp;email=$email&amp;additional=$additional\" width=\"1\" height=\"1\" style=\"border:0;\" alt=\"\" />\n";
		}
	}

	// Google AdWords / Analytics code

	if($data["ordernummer"]=="beschikbaarheidsaanvraag") {
		# Optie-aanvraag
		$google_conversion_label = "GsSQCP_TmwQQ94jL_gM";
	} elseif($data["ordernummer"]=="contactaanvraag") {
		# Contactaanvraag
		$google_conversion_label = "3DAzCPfUmwQQ94jL_gM";
	} elseif($data["ordernummer"]=="vraagonsadvies") {
		# Vraag ons advies
		$google_conversion_label = "Qj9RCO_VmwQQ94jL_gM";
	} else {
		# Boeking
		$google_conversion_label = "RSUICI_SmwQQ94jL_gM";
	}

	echo '<!-- Google Code for Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1070777463;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "'.$google_conversion_label.'";
var google_conversion_value = '.($data["bedrag"] ? number_format($data["bedrag"],2,".","") : "0").';
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1070777463/?value=0&amp;label='.$google_conversion_label.'&amp;guid=ON&amp;script=0"/>
</div>
</noscript>';

}

function nieuwsbrief_inschrijven($wzt,$nieuwsbrief_waardes) {
	#
	# nieuwsbrieflid inschrijven
	#
	global $vars;
	$db=new DB_sql;

	# Opslaan in database niet meer nodig (inschrijven loopt nu rechtstreeks via Blinker - 04-12-2012)
	// $db->query("SELECT nieuwsbrieflid_id FROM nieuwsbrieflid WHERE email='".addslashes($nieuwsbrief_waardes["email"])."';");
	// if($db->next_record()) {
	// 	$db->query("UPDATE nieuwsbrieflid SET bezoeker_id='".addslashes($_COOKIE["tch"])."', editdatetime=NOW() WHERE nieuwsbrieflid_id='".addslashes($db->f("nieuwsbrieflid_id"))."'");
	// } else {
	// 	$db->query("INSERT INTO nieuwsbrieflid SET email='".addslashes($nieuwsbrief_waardes["email"])."', voornaam='".addslashes($nieuwsbrief_waardes["voornaam"])."', tussenvoegsel='".addslashes($nieuwsbrief_waardes["tussenvoegsel"])."', achternaam='".addslashes($nieuwsbrief_waardes["achternaam"])."', bezoeker_id='".addslashes($_COOKIE["tch"])."', adddatetime=NOW(), editdatetime=NOW();");
	// }

	if($vars["website"]=="C") {
		# Chalet.nl-nieuwsbrief
		if($nieuwsbrief_waardes["per_wanneer"]==2) {
			$data['field10934'] = "ja";
		}
		$data['field1038'] = utf8_encode($nieuwsbrief_waardes["email"]);
		$data['field1040'] = utf8_encode($nieuwsbrief_waardes["voornaam"]);
		$data['field1041'] = utf8_encode($nieuwsbrief_waardes["tussenvoegsel"]);
		$data['field1036'] = utf8_encode($nieuwsbrief_waardes["achternaam"]);
		$data['userId']="31300179";
		$data['formEncId']="MwJLgCnDPkS9LWs";
		$data['redir']="formAdmin2";
		$data['viewMode']="STATICINTEGRATION";
	} elseif($vars["website"]=="B") {
		# Chalet.be-nieuwsbrief
		if($nieuwsbrief_waardes["per_wanneer"]==2) {
			$data['field10950'] = "ja";
		}
		$data['field10946'] = utf8_encode($nieuwsbrief_waardes["email"]);
		$data['field10947'] = utf8_encode($nieuwsbrief_waardes["voornaam"]);
		$data['field10948'] = utf8_encode($nieuwsbrief_waardes["tussenvoegsel"]);
		$data['field10949'] = utf8_encode($nieuwsbrief_waardes["achternaam"]);
		$data['userId']="31300179";
		$data['formEncId']="mfR7fLLhFtS3ppW";
		$data['redir']="formAdmin2";
		$data['viewMode']="STATICINTEGRATION";
	} elseif($vars["website"]=="I") {
		# Italissima.nl-nieuwsbrief
		$data['field10751'] = utf8_encode($nieuwsbrief_waardes["email"]);
		$data['field10761'] = utf8_encode($nieuwsbrief_waardes["voornaam"]);
		$data['field10771'] = utf8_encode($nieuwsbrief_waardes["tussenvoegsel"]);
		$data['field10781'] = utf8_encode($nieuwsbrief_waardes["achternaam"]);
		$data['userId']="31300179";
		$data['formEncId']="J4XyB4nwd7v3yi8";
		$data['redir']="formAdmin2";
		$data['viewMode']="STATICINTEGRATION";
	} elseif($vars["website"]=="K") {
		# Italissima.be-nieuwsbrief
		$data['field10952'] = utf8_encode($nieuwsbrief_waardes["email"]);
		$data['field10953'] = utf8_encode($nieuwsbrief_waardes["voornaam"]);
		$data['field10954'] = utf8_encode($nieuwsbrief_waardes["tussenvoegsel"]);
		$data['field10955'] = utf8_encode($nieuwsbrief_waardes["achternaam"]);
		$data['userId']="31300179";
		$data['formEncId']="HN6Txi7YfZvamjD";
		$data['redir']="formAdmin2";
		$data['viewMode']="STATICINTEGRATION";
	}

	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		echo wt_dump($data);
		exit;
	}

	$result=nieuwsbrief_inschrijven_bij_blinker("http://m16.mailplus.nl/genericservice/code/servlet/Redirect",$data);
}

function nieuwsbrief_inschrijven_bij_blinker($URL,$data, $referrer="") {
	$URL_Info=parse_url($URL);

	$referrer=$_SERVER["SCRIPT_URI"];

	// making string from $data
	foreach($data as $key=>$value)
	$values[]="$key=".urlencode($value);
	$data_string=implode("&",$values);

	// building POST-request:
	$request.="POST ".$URL_Info["path"]." HTTP/1.1\n";
	$request.="Host: ".$URL_Info["host"]."\n";
	$request.="Referer: $referer\n";
	$request.="Content-type: application/x-www-form-urlencoded\n";
	$request.="Content-length: ".strlen($data_string)."\n";
	$request.="Connection: close\n";
	$request.="\n";
	$request.=$data_string."\n";

	$fp = fsockopen($URL_Info["host"], 80, $errno, $errstr, 15);

	fputs($fp, $request);
	while(!feof($fp)) {
		$result .= fgets($fp, 128);
	}
	fclose($fp);

	return $result;
}


function inkoopprijs_bepalen($typeid,$aankomstdatum) {
	global $vars;
	$db=new DB_sql;
	$db->query("SELECT a.toonper, ta.bruto, ta.korting_percentage, ta.korting_euro, ta.toeslag, ta.vroegboekkorting_percentage, ta.vroegboekkorting_euro, ta.c_bruto, ta.c_korting_percentage, ta.c_toeslag, ta.c_korting_euro, ta.c_vroegboekkorting_percentage, ta.c_vroegboekkorting_euro, ta.inkoopkorting_percentage, ta.inkoopkorting_euro FROM accommodatie a, type t, tarief ta WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id=ta.type_id AND ta.type_id='".addslashes($typeid)."' AND ta.week='".addslashes($aankomstdatum)."'");
	if($db->next_record()) {

		if($db->f("toonper")==1) {
			#
			# arrangement
			#

			# korting_percentage = commissie
			# toeslag = toeslag
			# korting_euro = korting

			$return["inkoop_min_korting"]=$db->f("bruto")*(1-$db->f("korting_percentage")/100);

			$return["netto"]=$return["inkoop_min_korting"]+$db->f("toeslag")-$db->f("korting_euro");

			$return["netto"]=$return["netto"]-($return["inkoop_min_korting"]*($db->f("vroegboekkorting_percentage")/100));
			$return["netto"]=$return["netto"]-$db->f("vroegboekkorting_euro");

			# inkoopkorting percentage verwerken
			if($db->f("inkoopkorting_percentage")) {
				$return["netto"]=$return["netto"]*(1-$db->f("inkoopkorting_percentage")/100);
			}

			# inkoopkorting euro verwerken
			if($db->f("inkoopkorting_euro")) {
				$return["netto"]=$return["netto"]-$db->f("inkoopkorting_euro");
			}

			# return-gegevens
			$return["bruto"]=$db->f("bruto");
			$return["netto"]=$return["netto"];

			$return["inkoopcommissie"]=$db->f("korting_percentage");
#			$return["inkooptoeslag"]=$db->f("toeslag");
			$return["inkoopkorting"]=$db->f("korting_euro")-$db->f("toeslag");

			$return["inkoopkorting_euro"]=$db->f("inkoopkorting_euro")+$db->f("vroegboekkorting_euro");
			$return["inkoopkorting_percentage"]=$db->f("inkoopkorting_percentage")+$db->f("vroegboekkorting_percentage");

		} elseif($db->f("toonper")==3) {
			#
			# losse accommodatie
			#

			# c_korting_percentage = commissie
			# c_toeslag = toeslag
			# c_korting_euro = korting
			$return["c_inkoop_min_korting"]=$db->f("c_bruto")*(1-$db->f("c_korting_percentage")/100);


			$return["c_netto"]=$return["c_inkoop_min_korting"]+$db->f("c_toeslag")-$db->f("c_korting_euro");
			$return["c_netto"]=$return["c_netto"]-($return["c_inkoop_min_korting"]*($db->f("c_vroegboekkorting_percentage")/100));
			$return["c_netto"]=$return["c_netto"]-$db->f("c_vroegboekkorting_euro");

			$return["c_netto_zonder_inkoopkorting"]=$return["c_netto"];

			# inkoopkorting percentage verwerken
			if($db->f("inkoopkorting_percentage")) {
				$return["c_netto"]=$return["c_netto"]*(1-$db->f("inkoopkorting_percentage")/100);
			}

			# inkoopkorting euro verwerken
			if($db->f("inkoopkorting_euro")) {
				$return["c_netto"]=$return["c_netto"]-$db->f("inkoopkorting_euro");
			}

			# return-gegevens
			$return["bruto"]=$db->f("c_bruto");
			$return["netto"]=$return["c_netto"];

			$return["inkoopcommissie"]=$db->f("c_korting_percentage");
#			$return["inkooptoeslag"]=$db->f("c_toeslag");
			$return["inkoopkorting"]=$db->f("c_korting_euro")-$db->f("c_toeslag");

			$return["inkoopkorting_percentage"]=$db->f("inkoopkorting_percentage")+$db->f("c_vroegboekkorting_percentage");
			$return["inkoopkorting_euro"]=$db->f("inkoopkorting_euro")+$db->f("c_vroegboekkorting_euro");
		}
	}
	return $return;
}

function inkoopprijs_opslaan($boekingid) {
	#
	# Eenmalig
	#
	global $vars;
	$db=new DB_sql;
	$db->query("SELECT * FROM garantie WHERE boeking_id='".addslashes($boekingid)."';");
	if($db->next_record()) {
		# Garantie
		$inkoop["netto"]=$db->f("netto");
		$inkoop["bruto"]=$db->f("bruto");
		$inkoop["inkoopcommissie"]=$db->f("korting_percentage");
		$inkoop["inkoopkorting"]=$db->f("korting_euro");
		$inkoop["inkoopkorting_euro"]=$db->f("inkoopkorting_euro");
		$inkoop["inkoopkorting_percentage"]=$db->f("inkoopkorting_percentage");
		echo " - GARANTIE ".$db->f("garantie_id")." ";
	} else {
		# Gewone boeking
		$db->query("SELECT type_id, aankomstdatum FROM boeking WHERE boeking_id='".addslashes($boekingid)."';");
		if($db->next_record()) {
			$inkoop=inkoopprijs_bepalen($db->f("type_id"),$db->f("aankomstdatum"));
		}
	}
	if($inkoop["bruto"]>0 and $inkoop["netto"]>0) {
		if($inkoop["inkoopcommissie"]<>0) {
			$setquery.=", inkoopcommissie='".addslashes($inkoop["inkoopcommissie"])."'";
		} else {
			$setquery.=", inkoopcommissie=NULL";
		}
		if($inkoop["inkooptoeslag"]<>0) {
			$setquery.=", inkooptoeslag='".addslashes($inkoop["inkooptoeslag"])."'";
		} else {
			$setquery.=", inkooptoeslag=NULL";
		}
		if($inkoop["inkoopkorting"]<>0) {
			$setquery.=", inkoopkorting='".addslashes($inkoop["inkoopkorting"])."'";
		} else {
			$setquery.=", inkoopkorting=NULL";
		}
		if($inkoop["inkoopkorting_euro"]<>0) {
			$setquery.=", inkoopkorting_euro='".addslashes($inkoop["inkoopkorting_euro"])."'";
		} else {
			$setquery.=", inkoopkorting_euro=NULL";
		}
		if($inkoop["inkoopkorting_percentage"]<>0) {
			$setquery.=", inkoopkorting_percentage='".addslashes($inkoop["inkoopkorting_percentage"])."'";
		} else {
			$setquery.=", inkoopkorting_percentage=NULL";
		}

		$db->query("UPDATE boeking SET inkoopnetto='".addslashes($inkoop["netto"])."', inkoopbruto='".addslashes($inkoop["bruto"])."'".$setquery." WHERE boeking_id='".addslashes($boekingid)."';");
#		echo $db->lq;
	} else {
		trigger_error("onbekende inkoopprijs bij boekingid ".$boekingid." bij function 'inkoopprijs_opslaan'",E_USER_NOTICE);
	}
}

function googleanalytics_old() {
	global $vars,$voorkant_cms,$id;

	$test_analytics=true;

	if($test_analytics)	{
		if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
			$vars["googleanalytics"]="UA-2078202-12";
		} else {
			$test_analytics=false;
		}
	}

	# gegevens voor in opmaak.php
	if($test_analytics or ($vars["googleanalytics"] and !$voorkant_cms and !in_array($_SERVER["REMOTE_ADDR"],$vars["vertrouwde_ips"]) and !$vars["lokale_testserver"] and !$_GET["wtfatalerror"])) {

		if($_COOKIE["abt"]) {
			$extra.="_gaq.push(['_setCustomVar', 1, 'AB-testing', '".$_COOKIE["abt"]."', 1]);\n";
		}

		// removed on 13-12-2013:
		//		_gaq.push(['_gat._anonymizeIp']);

		$return="<script type=\"text/javascript\">

		var page_with_tabs=false;
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '".$vars["googleanalytics"]."']);

		".$vars["googleanalytics_extra"].$extra."

		var canonical_link;
		try {
			canonical_link = $('link[rel=canonical]').attr('href').split(location.hostname)[1] || undefined;
		} catch(e) {
			canonical_link = undefined;
		}
		// bij zoek-en-boek: toch de complete URL
		".($id=="zoek-en-boek" ? "\ncanonical_link = undefined;\n" : "").($vars["page_with_tabs"] ? "page_with_tabs=true;\n" : "")."

		if(page_with_tabs) {
			// tabs: niks doen (wordt via tabfunctie geregeld)
		} else {
			_gaq.push(['_trackPageview', canonical_link]);
		}


		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;";

			$doubleclick_versie=true; // aangezet op verzoek van Bjorn: 11-07-2013

			if($test_analytics) {
				// debug-versie Google Analytics
				$return.="ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/u/ga_debug.js';\n";
			} elseif($doubleclick_versie) {
				// DoubleClick-versie Google Analytics
				$return.="ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';\n";
			} else {
				// gewone versie Google Analytics
				$return.="ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
			}

			$return.="var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();

		</script>\n";
		$vars["googleanalytics_actief"]=true;
	}
	return $return;
}

function googleanalytics() {
	global $vars,$voorkant_cms,$id;

	$test_analytics=true;

	if($test_analytics)	{
		if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
			$vars["googleanalytics"]="UA-2078202-12";
		} else {
			$test_analytics=false;
		}
	}

	$vertrouwde_ips = $vars["vertrouwde_ips"];
	if($_SERVER["REMOTE_ADDR"]=="31.223.173.113") {
		// IP Jeroen: ignore vertrouwde_ips
		$vertrouwde_ips = array("");
	}

	if($test_analytics or ($vars["googleanalytics"] and !$voorkant_cms and !in_array($_SERVER["REMOTE_ADDR"],$vertrouwde_ips) and !$vars["lokale_testserver"] and !$_GET["wtfatalerror"])) {

		if($_COOKIE["abt"]) {
			$extra.="ga('set', 'AB-testing', '".$_COOKIE["abt"]."');\n";
		}

		$return = "<script>

		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		var page_with_tabs=false;

		var canonical_link;
		try {
			canonical_link = $('link[rel=canonical]').attr('href').split(location.hostname)[1] || undefined;
		} catch(e) {
			canonical_link = undefined;
		}

		// bij zoek-en-boek: toch de complete URL
		".($id=="zoek-en-boek" ? "\ncanonical_link = undefined;\n" : "").($vars["page_with_tabs"] ? "page_with_tabs=true;\n" : "")."

		ga('create', '".$vars["googleanalytics"]."', 'auto');
		ga('require', 'displayfeatures');".$vars["googleanalytics_extra"].$extra."

		if(page_with_tabs) {
			// tabs: niks doen (wordt via tabfunctie geregeld)
		} else {
			ga('send', 'pageview', canonical_link);
		}

		</script>";

		$vars["googleanalytics_actief"]=true;
	}
	return $return;
}

function acc_zoekresultaat($data,$newresultsminmax,$newresults_multiple,$aanbieding_acc,$settings="") {
	global $vars;
	if($settings["url"]) {
		$url=$settings["url"];
	} else {
		$url=$vars["path"].txt("menu_accommodatie")."/".$data["begincode"].$data["type_id"]."/".$querystring;
	}
	$return.="<div class=\"zoekresultaat_block boxshadow\">";
	$return.="<a href=\"".wt_he($url)."\" class=\"zoekresultaat\">";
		$return.="<div class=\"zoekresultaat_top\">";
			$return.="<div class=\"zoekresultaat_titel\">".wt_he(ucfirst($vars["soortaccommodatie"][$data["soortaccommodatie"]])." ".$data["naam"].(!$newresults_multiple&&$data["tnaam"] ? " ".$data["tnaam"] : ""))."</div>";
			$return.="<div class=\"zoekresultaat_locatie\">".wt_he($data["land"]." - ".$data["skigebied"]." - ".$data["plaats"])."</div>";
		$return.="</div>";
		$return.="<div>";
			# afbeelding bepalen
			$img="accommodaties/0.jpg";
			if($newresults_multiple) {
				if(file_exists("pic/cms/accommodaties/".$data["accommodatie_id"].".jpg")) {
					$img="accommodaties/".$data["accommodatie_id"].".jpg";
				} elseif(file_exists("pic/cms/types_specifiek/".$data["type_id"].".jpg")) {
					$img="types_specifiek/".$data["type_id"].".jpg";
				}
			} else {
				if(file_exists("pic/cms/types_specifiek/".$data["type_id"].".jpg")) {
					$img="types_specifiek/".$data["type_id"].".jpg";
				} elseif(file_exists("pic/cms/accommodaties/".$data["accommodatie_id"].".jpg")) {
					$img="accommodaties/".$data["accommodatie_id"].".jpg";
				}
			}
			$return.="<div class=\"zoekresultaat_img\"><img src=\"".wt_he($vars["path"]."pic/cms/".$img)."\"></div>";

			$return.="<div class=\"zoekresultaat_content\">";
				$return.="<div>".$newresultsminmax["minpersonen"].($newresultsminmax["maxpersonen"]>$newresultsminmax["minpersonen"] ? " - ".$newresultsminmax["maxpersonen"] : "")." ".($newresultsminmax["maxpersonen"]==1 ? html("persoon") : html("personen"))."</div>";
				$return.="<div>".$newresultsminmax["minslaapkamers"].($newresultsminmax["maxslaapkamers"]>$newresultsminmax["minslaapkamers"] ? " - ".$newresultsminmax["maxslaapkamers"] : "")." ".($newresultsminmax["maxslaapkamers"]==1 ? html("slaapkamer") : html("slaapkamers"))."</div>";
				$return.="<div>".$newresultsminmax["minbadkamers"].($newresultsminmax["maxbadkamers"]>$newresultsminmax["minbadkamers"] ? " - ".$newresultsminmax["maxbadkamers"] : "")." ".($newresultsminmax["maxbadkamers"]==1 ? html("badkamer") : html("badkamers"))."</div>";
				if($newresultsminmax["minkwaliteit"]) {
					$return.="<div>";
					for($i=1;$i<=$newresultsminmax["minkwaliteit"];$i++) {
						$return.="<img src=\"".$vars["path"]."pic/ster_".$vars["websitetype"].".png\">";
					}
					if($newresultsminmax["maxkwaliteit"]>$newresultsminmax["minkwaliteit"]) {
						$return.="<img src=\"".$vars["path"]."pic/ster-scheidingsteken.gif\">";
						for($i=1;$i<=$newresultsminmax["maxkwaliteit"];$i++) {
							$return.="<img src=\"".$vars["path"]."pic/ster_".$vars["websitetype"].".png\">";
						}
					}
					$return.="</div>";
				}

				$return.="<div class=\"zoekresultaat_omschrijving".($data["type_id_trclass"]&&!$newresults_multiple ? " ".$data["type_id_trclass"] : "")."\">".wt_he((!$newresults_multiple&&$data["tkorteomschrijving"] ? $data["tkorteomschrijving"] : $data["korteomschrijving"]))."</div>";

			if($aanbieding_acc) {
				$return.="<div class=\"zoekresultaat_aanbieding\"><img src=\"".$vars["path"]."pic/aanbieding_groot_".$vars["websitetype"].".gif\">".html("aanbieding","accommodaties")."</div>";
			}

			$return.="</div>";


			$return.="<div class=\"zoekresultaat_prijs\">";
				if($data["tarief"]) {
					if($newresults_multiple and $newresultsminmax["maxtarief"]>$newresultsminmax["mintarief"]) {
						$return.="<div class=\"zoekresultaat_prijs_vanaf\">".html("vanaf")."</div>";
					} else {
						$return.="<div class=\"zoekresultaat_prijs_vanaf\">&nbsp;</div>";
					}
					$return.="<div class=\"zoekresultaat_prijs_bedrag".($aanbieding_acc ? " zoekresultaat_prijs_bedrag_aanbieding" : "")."\">&euro;&nbsp;".number_format($newresultsminmax["mintarief"],0,",",".")."</div>";
					$return.="<div class=\"zoekresultaat_prijs_per\">";
					if($data["toonper"]==3 or $vars["wederverkoop"]) {
						$return.=html("peraccommodatie","zoek-en-boek");
					} else {
						$return.=html("perpersoon","zoek-en-boek")."<br>".html("inclusiefskipas","zoek-en-boek");
					}
					$return.="</div>";
				}
			$return.="</div>";
			$return.="<div class=\"clear\"></div>";
		$return.="</div>";
	$return.="</a>";
	$return.="</div>";

	return $return;
}

function xml_text($text,$use_cdata=true) {
	#
	# function om tekst om te zetten naar correcte XML-inhoud
	#
	$return=$text;
	if($return) {
		$return=iconv("Windows-1252","UTF-8",$return);
		if(strpos(" ".$return,"<") or strpos(" ".$return,">") or strpos(" ".$return,"&") or strpos(" ".$return,'"') or strpos(" ".$return,"'")) {
			if($use_cdata) {
				$return="<![CDATA[".$return."]]>";
			} else {
				$return=str_replace("&","&amp;",$return);
				$return=str_replace("<","&lt;",$return);
				$return=str_replace(">","&gt;",$return);
				$return=str_replace('"',"&quot;",$return);
				$return=str_replace("'","&apos;",$return);
			}
		}
	}
	return $return;
}

#
# Opval-blok
#
function opvalblok() {

	global $vars,$id,$themalandinfo,$land,$skigebiedid,$plaatsid;

	$db=new DB_sql;


	# limit (standaard: 1 resultaat tonen)
	$limit="0,1";
	$order_by="RAND()";

	# where bepalen
	if($id=="index") {
		$where="b.hoofdpagina=1";
	} elseif($id=="thema") {
		$where="b.thema_id='".intval($themalandinfo["id"])."'";
	} elseif($id=="land") {
		$where="b.land_id='".intval($themalandinfo["id"])."'";
	} elseif($id=="themas") {
		$where="b.themaoverzicht=1";
	} elseif($id=="bestemmingen") {
		$where="b.bestemmingen=1";
	} elseif($id=="toonskigebied") {
		$where="b.skigebied_id='".intval($skigebiedid)."'";
	} elseif($id=="toonplaats") {
		$where="b.plaats_id='".intval($plaatsid)."'";
	} elseif($id=="aanbiedingen_zomerhuisje") {
		$where="b.aanbiedingenpagina=1";

		if($land["id"] and $vars["websitetype"]<>7) {
			# alleen blokken uit gekozen land tonen
			$where.=" AND b.land_id='".intval($land["id"])."'";
		}

		if($vars["websitetype"]==3 and !$land["id"]) {
			# Zomerhuisje overzichtpagina: 3 resultaten tonen
			$limit="0,3";
		} else {
			# de rest: 10 resultaten tonen
			$limit="0,10";
		}
		$order_by="volgorde";
	} else {
		$where="1=1";
	}

	$checkdate=mktime(0,0,0,date("m"),date("d"),date("Y"));
	$db->query("SELECT b.regel1, b.regel2, b.regel3, b.begindatum, b.einddatum, t.type_id, a.accommodatie_id, l.begincode FROM blokaccommodatie b, accommodatie a, type t, plaats p, land l WHERE b.websites LIKE '%".$vars["website"]."%' AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND b.tonen=1 AND a.tonen=1 AND t.tonen=1 AND (b.begindatum IS NULL OR b.begindatum<=NOW()) AND (b.einddatum IS NULL OR b.einddatum>=NOW()) AND ".$where." ORDER BY ".$order_by." LIMIT ".$limit.";");

	while($db->next_record()) {

		unset($afbeelding);
		if(file_exists("pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
			$afbeelding="pic/cms/types_specifiek/".$db->f("type_id").".jpg";
		} elseif(file_exists("pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
			$afbeelding="pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
		} elseif($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$afbeelding="pic/cms/accommodaties/1097.jpg";
		}

		if(file_exists($afbeelding)) {
			$opvalblok_teller++;

			$url=$vars["path"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/";

			$return.="<div class=\"opvalblok\" onclick=\"document.location.href='".wt_he($url)."'\">";
			$return.="<div class=\"opvalblok_regel1\">".wt_he($db->f("regel1"))."</div>";
			$return.="<div class=\"opvalblok_regel2\">".wt_he($db->f("regel2"))."</div>";
			$return.="<div class=\"overlay_foto\">";
			$return.="<img src=\"".wt_he($vars["path"].$afbeelding)."\" alt=\"\">";
			$return.="<div class=\"opvalblok_regel3\">".wt_he($db->f("regel3"))."</div>";
			$return.="</div>";
			$return.="</div>"; # afsluiten class opvalblok
		}
	}

	return $return;
}

function getal_met_juist_aantal_decimalen_weergeven($getal) {
	//
	// getal tonen zonder eind-nullen (en met komma + duizendtallen-scheiding)
	//
	$return = number_format($getal, wt_aantal_decimalen($getal), ",", ".");

	// laatste nullen strippen
	$return = preg_replace("@(,[0-9]*)0{1,}$@","\\1",$return);

	// comma strippen indien nodig
	$return = preg_replace("@,$@","",$return);

	return $return;
}


function verstuur_opmaakmail($website,$to,$toname,$subject,$body,$settings) {

	#
	# Functie om opgemaakte mail (met header-afbeelding) te verzenden
	#

	global $vars,$unixdir;


	if(!$website) $website=$vars["website"];

	$topfoto="pic/mailheader/".$website.".jpg";

	if(file_exists($vars["unixtime"].$topfoto)) {
		$topfoto_size=getimagesize($vars["unixtime"].$topfoto);
	}

	if($settings["convert_to_html"]) {

		$body=wt_he($body);

		$body=nl2br($body);

		# [link=http://url/]tekst[/link] omzetten
		$body=preg_replace("/\[link=(https?[^]]+)\](.*)\[\/link\]/","<a href=\"\\1\">\\2</a>",$body);

		# [b] bold
		$body=preg_replace("/\[b\](.*)\[\/b\]/","<b>\\1</b>",$body);

		# [i] italics
		$body=preg_replace("/\[i\](.*)\[\/i\]/","<i>\\1</i>",$body);

		# [ul] u-list
		$body=preg_replace("/\[ul\](.*)\[\/ul\]/s","<ul>\\1</ul>",$body);

		# [li] list-item
		$body=preg_replace("/\[li\](.*)\[\/li\]/","<li style=\"margin-bottom:1.5em;\">\\1</li>",$body);

		$body=str_replace("</li><br />\n<li>","</li><li>",$body);
		$body=str_replace("</li><br />\n<li ","</li><li ",$body);
	}

	if(preg_match("/\[ondertekening\]/",$body)) {

		$temp_taal=$vars["taal"];

		$vars["taal"]=$vars["websiteinfo"]["taal"][$website];
		$ondertekening=html("hetteamvan","vars",array("v_websitenaam"=>$vars["websiteinfo"]["websitenaam"][$website],"h_1"=>"<a href=\"".wt_he($vars["websiteinfo"]["basehref"][$website].$settings["add_to_basehref"])."\">","h_2"=>"</a>"))."<br/>".html("telefoonnummer");
		$body=preg_replace("/\[ondertekening\]/",$ondertekening,$body);

		$vars["taal"]=$temp_taal;

	}

	$mail=new wt_mail;

	# header-attachment toevoegen
	if(!$settings["no_header_image"]) {
		$cid=$mail->attachment($unixdir.$topfoto,"image/jpeg",true);
	}

	# attachments toevoegen
	if(is_array($settings["attachment"])) {
		foreach ($settings["attachment"] as $key => $value) {
			$mail->attachment($key,"",false,$value);
		}
	}

	# from
	if($settings["from"]) {
		$mail->from=$settings["from"];
	} else {
		$mail->from=$vars["websiteinfo"]["email"][$website];
	}

	# fromname
	if($settings["fromname"]) {
		$mail->fromname=$settings["fromname"];
	} else {
		$mail->fromname=$vars["websiteinfo"]["websitenaam"][$website];
	}

	# subject
	$mail->subject=$subject;

	# to
	$mail->to=$to;


	// if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	// 	$mail->to="jeroen@webtastic.nl";
	// 	$mail->to="mmtest@postvak.net";
	// 	$mail->test=false;
	// }

	# toname
	if($toname) {
		$mail->toname=$toname;
	}

	# bcc versturen?
	if($settings["bcc"]) {
		$mail->bcc=$settings["bcc"];
	}

	$mail->plaintext=""; # deze leeg laten bij een opmaak-mailtje
	$mail->html_top="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\"/><style><!--\na:hover { color:#888888; }\n--></style>\n</head><body bgcolor=\"#ffffff\" style=\"background-color:#ffffff;margin:0;padding:0;\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" bgcolor=\"#ffffff\" style=\"background-color:#ffffff;width:100%;\"><tr><td align=\"center\" width=\"100%\" style=\"background-color:#ffffff;width:100%;\"><br><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"681\" style=\"background-color:#ffffff;\"><tr><td>";


	# topfoto
	if($settings["no_header_image"]) {
		$mail->html_top.="&nbsp;";
	} else {
		$mail->html_top.="<a href=\"".wt_he($vars["websiteinfo"]["basehref"][$website].$settings["add_to_basehref"])."\">";
		if($cid) {
			$mail->html_top.="<img src=\"cid:".$cid."\" ".$topfoto_size[3]." alt=\"".wt_he($vars["websiteinfo"]["websitenaam"][$website])."\" border=\"0\">";
		} else {
			$mail->html_top.="<img src=\"".wt_he($vars["websiteinfo"]["basehref"][$website]).$topfoto."\" ".$topfoto_size[3]." alt=\"".wt_he($vars["websiteinfo"]["websitenaam"][$website])."\" border=\"0\">";
		}
		$mail->html_top.="</a><br/>&nbsp;";
	}

	$mail->html_top.="</td></tr><tr><td style=\"font-family: Verdana, Helvetica, Arial, sans-serif;line-height: 14pt;font-size: 10pt;padding-top:10px;\">\n";

	$mail->html_bottom="<br/>&nbsp;</td></tr></table></td></tr></table></body></html>\n";

	$mail->html=$body;
	$mail->send();

}

?>