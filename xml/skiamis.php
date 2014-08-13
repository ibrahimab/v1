<?php

#
#
# XML-export voor Ski Amis
#
# property list: 	https://www.chalet.eu/xml/skiamis.php?app=LS&clt=111&top=999&qry=lotst_list@top_id='AMIS',@st='0'
# property prices:	https://www.chalet.eu/xml/skiamis.php?app=LS&clt=111&top=999&qry=tarif_lotref@top_id='AMIS',@lot_ref='F249'
# property availability	https://www.chalet.eu/xml/skiamis.php?app=LS&clt=111&top=999&qry=extr_plng@top_id='AMIS'
#
#

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");

$db->query("SELECT reisbureau_id, xmlfeed_winter, xmlfeed_zomer, xmlfeed_log FROM reisbureau WHERE xmlfeed=1 AND actief=1 AND reisbureau_id='38';");
if($db->next_record()) {
	$reisbureau["reisbureau_id"]=$db->f("reisbureau_id");
	$reisbureau["xmlfeed_winter"]=$db->f("xmlfeed_winter");
	$reisbureau["xmlfeed_zomer"]=$db->f("xmlfeed_zomer");
	$reisbureau["xmlfeed_log"]=$db->f("xmlfeed_log");
}

if($reisbureau["reisbureau_id"] and $_GET["qry"]) {

	# Loggen
	$reisbureau["xmlfeed_log"]=date("d-m-Y H:i:s")." - ".$_GET["qry"]." - ".$_SERVER["REMOTE_ADDR"]." - ".$_SERVER["HTTP_USER_AGENT"]."\n".$reisbureau["xmlfeed_log"];
	$new_xmlfeed_log_array=split("\n",$reisbureau["xmlfeed_log"]);
	while(list($key,$value)=each($new_xmlfeed_log_array)) {
		if($key<10) {
			$new_xmlfeed_log.=$value."\n";
		}
	}
	$db->query("UPDATE reisbureau SET xmlfeed_log='".addslashes($new_xmlfeed_log)."' WHERE reisbureau_id='".addslashes($reisbureau["reisbureau_id"])."';");

	# querystring ontleden
	$qry_tmp=preg_split("/[@,]/",$_GET["qry"]);
	while(list($key,$value)=each($qry_tmp)) {
		if($value) {
			$qry[$value]=true;
			if(preg_match("/lot_ref='[A-Za-z]{1,2}(.+)'/",$value,$regs)) {
				$lot_ref=$regs[1];
			}
		}
	}

	$wzt=0;
	if($vars["websitetype"]=="6") {
		if($reisbureau["xmlfeed_winter"] and $reisbureau["xmlfeed_zomer"]) {
			$wzt="1,2";
		} elseif($reisbureau["xmlfeed_winter"]) {
			$wzt="1";
		} elseif($reisbureau["xmlfeed_zomer"]) {
			$wzt="2";
		}
	} else {
		if($vars["seizoentype"]==1 and $reisbureau["xmlfeed_winter"]) {
			$wzt="1";
		} elseif($vars["seizoentype"]==2 and $reisbureau["xmlfeed_zomer"]) {
			$wzt="2";
		}
	}

	# land
	$db->query("SELECT land_id FROM reisbureau_xml_land WHERE wzt IN (".$wzt.") AND reisbureau_id='".addslashes($reisbureau["reisbureau_id"])."';");
	while($db->next_record()) {
		$inquery["land"].=",".$db->f("land_id");
	}
	# leverancier
	$db->query("SELECT leverancier_id FROM reisbureau_xml_leverancier WHERE wzt IN (".$wzt.") AND reisbureau_id='".addslashes($reisbureau["reisbureau_id"])."';");
	while($db->next_record()) {
		$inquery["leverancier"].=",".$db->f("leverancier_id");
	}
	# accommodatie
	$db->query("SELECT accommodatie_id FROM reisbureau_xml_accommodatie WHERE wzt IN (".$wzt.") AND reisbureau_id='".addslashes($reisbureau["reisbureau_id"])."';");
	while($db->next_record()) {
		$inquery["accommodatie"].=",".$db->f("accommodatie_id");
	}
	# type
	$db->query("SELECT type_id FROM reisbureau_xml_type WHERE wzt IN (".$wzt.") AND reisbureau_id='".addslashes($reisbureau["reisbureau_id"])."';");
	while($db->next_record()) {
		$inquery["type"].=",".$db->f("type_id");
	}

	# Alle type_id's ophalen op basis van alle $inquery's
	if(is_array($inquery)) {
		while(list($key,$value)=each($inquery)) {
			$where.=" OR ".$key."_id IN (".substr($value,1).")";
		}
		$db->query("SELECT type_id FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND archief=0 AND (".substr($where,4).");");
		while($db->next_record()) {
			$alletypes.=",".$db->f("type_id");
		}
		$alletypes=substr($alletypes,1);
	}

	if(!$alletypes) {
		trigger_error("feed opgevraagd met lege var 'alletypes'",E_USER_NOTICE);
		exit;
	}

	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		header("Content-Type: text/plain; charset=utf-8");
	} else {
		header("Content-Type: text/xml; charset=utf-8");
		echo "<";
		echo "?xml version=\"1.0\" encoding=\"utf-8\"?";
		echo ">\n";
		echo "<QRY-CLT111-TOP999>\n";
	}

	if($qry["lotst_list"]) {
		#
		# accommodation-units
		#
		$db->query("SELECT t.type_id, l.begincode, a.accommodatie_id, a.soortaccommodatie, a.naam AS anaam, a.wzt, t.naam".$vars["ttv"]." AS naam, t.optimaalaantalpersonen, t.maxaantalpersonen, t.kwaliteit, a.omschrijving".$vars["ttv"]." AS aomschrijving, t.omschrijving".$vars["ttv"]." AS tomschrijving, a.indeling".$vars["ttv"]." AS aindeling, t.indeling".$vars["ttv"]." AS tindeling, t.inclusief".$vars["ttv"]." AS inclusief, t.exclusief".$vars["ttv"]." AS exclusief, t.indeling".$vars["ttv"]." AS indeling, t.oppervlakte, t.oppervlakteextra".$vars["ttv"]." AS oppervlakteextra, t.slaapkamers, t.slaapkamersextra".$vars["ttv"]." AS slaapkamersextra, t.badkamers, t.badkamersextra".$vars["ttv"]." AS badkamersextra, p.naam AS plaats FROM accommodatie a, type t, plaats p, skigebied s, land l WHERE p.land_id=l.land_id AND p.skigebied_id=s.skigebied_id AND a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND a.wzt IN (".$wzt.")".($alletypes ? " AND t.type_id IN (".$alletypes.")" : "").($lot_ref ? " AND type_id='".addslashes($lot_ref)."'" : "").($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? " AND type_id=252" : "")." ORDER BY t.type_id;");
		while($db->next_record()) {

			echo "<Lot>\n";
			echo "<lot_imme_no>".xml_text($db->f("accommodatie_id"))."</lot_imme_no>\n";
			echo "<lot_no>".xml_text($db->f("type_id"))."</lot_no>\n";
			echo "<lot_ref>".xml_text($db->f("begincode").$db->f("type_id"))."</lot_ref>\n";
			echo "<imme_nom>".xml_text($db->f("anaam").($db->f("naam") ? " ".$db->f("naam") : ""))."</imme_nom>\n";
			echo "<lot_desi>".xml_text($vars["soortaccommodatie"][$db->f("soortaccommodatie")])."</lot_desi>\n";
			echo "<lot_ville>".xml_text($db->f("plaats"))."</lot_ville>\n";
			echo "<lot_pax>".xml_text($db->f("maxaantalpersonen"))."</lot_pax>\n";
			echo "<crit1_libelle></crit1_libelle>\n";
			echo "<crit2_libelle></crit2_libelle>\n";
			echo "<crit3_libelle></crit3_libelle>\n";
			echo "<lot_dvd></lot_dvd>\n";
			echo "<lot_tv></lot_tv>\n";
			echo "<lot_cheminee></lot_cheminee>\n";
			echo "<lot_wifi></lot_wifi>\n";


			# afbeeldingen

			echo "<ImageInter></ImageInter>\n";

			# hoofdfoto
			if(file_exists("../pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
				echo "<ImageResid>".xml_text($vars["basehref"]."pic/cms/types_specifiek/".$db->f("type_id").".jpg")."</ImageResid>\n";
			} elseif(file_exists("../pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
				echo "<ImageResid>".xml_text($vars["basehref"]."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")."</ImageResid>\n";
			} else {
				echo "<ImageResid></ImageResid>\n";
			}
			echo "<ImageVue></ImageVue>\n";
			echo "<ImagePlan></ImagePlan>\n";
			echo "<ImageInve1></ImageInve1>\n";
			echo "<ImageInve2></ImageInve2>\n";

			# Aanvullende afbeeldingen
			$foto=imagearray(array("accommodaties_aanvullend","types","accommodaties_aanvullend_onderaan","accommodaties_aanvullend_breed","types_breed"),array($db->f("accommodatie_id"),$db->f("type_id"),$db->f("accommodatie_id"),$db->f("accommodatie_id"),$db->f("type_id")),"../");
			if(is_array($foto["pic"])) {
				$fototeller=0;
				while(list($key,$value)=each($foto["pic"])) {
					$fototeller++;
					echo "<ImageDivers".$fototeller.">".xml_text($vars["basehref"]."pic/cms/".$value)."</ImageDivers".$fototeller.">\n";
				}
			}
			if($fototeller<1) echo "<ImageDivers1></ImageDivers1>\n";
			if($fototeller<2) echo "<ImageDivers2></ImageDivers2>\n";
			echo "<lot_desc></lot_desc>\n";
			unset($lot_desc);
			$lot_desc=trim($db->f("aomschrijving")).($db->f("aomschrijving")&&$db->f("tomschrijving") ? "\n\n" : "").trim($db->f("tomschrijving"));
			if($db->f("aindeling") or $db->f("tindeling")) {
				if($lot_desc) $lot_desc.="\n\n";
				$lot_desc.=trim($db->f("aindeling")).($db->f("aindeling")&&$db->f("tindeling") ? "\n\n" : "").trim($db->f("tindeling"));
			}
#			$lot_desc=preg_replace("/\n\n\n/","",$lot_desc);
			echo "<lot_desc_com>".xml_text($lot_desc)."</lot_desc_com>\n";
			echo "</Lot>\n";
		}

	} elseif($qry["tarif_lotref"] or $qry["extr_plng"]) {
		#
		# property prices / availability
		#

		if($qry["tarif_lotref"]) {
			$show_prices=true;
		}

		if($qry["extr_plng"]) {
			$show_availability=true;
		}

		# Afwijkende vertrekdagen?
		if($alletypes) {
			$typeid_inquery_vertrekdag=$alletypes;
		} else {
			$typeid_inquery_vertrekdag="ALL";
		}
		include("../content/vertrekdagaanpassing.html");

		$db->query("SELECT t.type_id, ta.wederverkoop_verkoopprijs, ta.beschikbaar, ta.week, ta.seizoen_id, ta.c_bruto, ta.bruto, ta.voorraad_garantie, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, ta.voorraad_optie_leverancier, ta.voorraad_xml, ta.voorraad_request, ta.voorraad_optie_klant, l.begincode, a.toonper, a.accommodatie_id, a.aankomst_plusmin, a.vertrek_plusmin FROM tarief ta, accommodatie a, type t, plaats p, skigebied s, land l WHERE ta.week>'".time()."' AND ta.blokkeren_wederverkoop=0 AND ta.type_id=t.type_id AND p.land_id=l.land_id AND p.skigebied_id=s.skigebied_id AND a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND a.wzt IN (".$wzt.")".($alletypes ? " AND t.type_id IN (".$alletypes.")" : "").($lot_ref ? " AND t.type_id='".addslashes($lot_ref)."'" : "")." ORDER BY t.type_id, ta.week;");
		while($db->next_record()) {
			$aanbieding[$db->f("seizoen_id")]=aanbiedinginfo($db->f("type_id"),$db->f("seizoen_id"),$db->f("week"));
			if($show_prices or (($db->f("toonper")==3 and $db->f("c_bruto")>0) or ($db->f("toonper")==1 and $db->f("bruto")>0))) {

				# Exacte aankomstdatum
				$week=$db->f("week");
				if($vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)] or $db->f("aankomst_plusmin")) {
					$aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)]+$db->f("aankomst_plusmin"),date("Y",$week));
					$exacte_unixtime=$aangepaste_unixtime;
				} else {
					$exacte_unixtime=$week;
				}

				# Aantal nachten

				# Afwijkende vertrekdag
				unset($aantalnachten_afwijking);
				$aantalnachten_afwijking=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)];
				$volgendeweek=mktime(0,0,0,date("n",$week),date("j",$week)+7,date("Y",$week));
				$aantalnachten_afwijking-=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$volgendeweek)];
				# Afwijkende verblijfsduur
				$aantalnachten_afwijking=$aantalnachten_afwijking+$db->f("aankomst_plusmin")-$db->f("vertrek_plusmin");
				$nachten=7-$aantalnachten_afwijking;

				# vertrekdag
				$vertrekdag=mktime(0,0,0,date("n",$exacte_unixtime),date("j",$exacte_unixtime)+$nachten,date("Y",$exacte_unixtime));


				# Beschikbaarheid
				$availability=3; # standaard: niet beschikbaar
				if($db->f("wederverkoop_verkoopprijs")>0) {
					# 1 = groen, 2 = oranje, 3 = zwart
					if($db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")-$db->f("voorraad_optie_klant")>=1) {
						$availability=1;
					} elseif($db->f("voorraad_request")>=1 or $db->f("voorraad_optie_klant")>=1) {
						$availability=2;
					}
				}

				if($availability==3 and $show_availability) {
					echo "<lot_ref>".xml_text($db->f("begincode").$db->f("type_id"))."</lot_ref>\n";
					echo "<ocpt_debut>".xml_text(date("d/m/Y",$exacte_unixtime))."</ocpt_debut>\n";
					echo "<ocpt_fin>".xml_text(date("d/m/Y",$vertrekdag))."</ocpt_fin>\n";
				}

				if($availability<>3) {
					# Prijs

					# aanbiedingen verwerken
					$prijs=verwerk_aanbieding($db->f("wederverkoop_verkoopprijs"),$aanbieding[$db->f("seizoen_id")]["typeid_sort"][$db->f("type_id")],$db->f("week"));
					if($show_prices and $prijs>0) {
						echo "<Tarif>\n";
						echo "<lot_ref>".xml_text($db->f("begincode").$db->f("type_id"))."</lot_ref>\n";
						echo "<ptar_debut>".xml_text(date("d/m/Y",$exacte_unixtime))."</ptar_debut>\n";
						echo "<ptar_fin>".xml_text(date("d/m/Y",$vertrekdag))."</ptar_fin>\n";
						echo "<ptar_montant>".xml_text(number_format($prijs,2,".",""))."</ptar_montant>\n";
						echo "</Tarif>\n";
					}
				}
			}
		}
	}
	echo "</QRY-CLT111-TOP999>\n";

} else {
	echo "Error: wrong URL. Please contact us.";
}

?>