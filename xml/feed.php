<?php

#
#
# XML-export voor diverse partners (uit databasetabel "reisbureau")
#
#
#


# /xml/accommodations.xml
# /xml/accommodation-units.xml
# /xml/availability-and-prices.xml

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");

if($_GET["c"]) {
	$db->query("SELECT reisbureau_id, xmlfeed_winter, xmlfeed_zomer, xmlfeed_log FROM reisbureau WHERE xmlfeed=1 AND actief=1 AND xmlfeed_toegangscode='".addslashes($_GET["c"])."';");
	if($db->next_record()) {
		$reisbureau["reisbureau_id"]=$db->f("reisbureau_id");
		$reisbureau["xmlfeed_winter"]=$db->f("xmlfeed_winter");
		$reisbureau["xmlfeed_zomer"]=$db->f("xmlfeed_zomer");
		$reisbureau["xmlfeed_log"]=$db->f("xmlfeed_log");
	}
}

if($reisbureau["reisbureau_id"] and $_GET["feedtype"]) {

	# Loggen
	if($_SERVER["REMOTE_ADDR"]<>"82.173.186.80") {
		$reisbureau["xmlfeed_log"]=date("d-m-Y H:i:s")." - ".$_GET["feedtype"]." - ".$_SERVER["REMOTE_ADDR"]." - ".$_SERVER["HTTP_USER_AGENT"]."\n".$reisbureau["xmlfeed_log"];
		$new_xmlfeed_log_array=split("\n",$reisbureau["xmlfeed_log"]);
		while(list($key,$value)=each($new_xmlfeed_log_array)) {
			if($key<10) {
				$new_xmlfeed_log.=$value."\n";
			}
		}
		$db->query("UPDATE reisbureau SET xmlfeed_log='".addslashes($new_xmlfeed_log)."' WHERE reisbureau_id='".addslashes($reisbureau["reisbureau_id"])."';");
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
	}

	if($_GET["feedtype"]=="accommodations") {
		#
		# accommodations
		#
		echo "<accommodations>\n";
		$db->query("SELECT DISTINCT a.accommodatie_id, a.naam, a.wzt, a.soortaccommodatie, p.naam AS plaats, s.naam AS skigebied, l.naam".$vars["ttv"]." AS land, a.omschrijving".$vars["ttv"]." AS omschrijving, a.inclusief".$vars["ttv"]." AS inclusief, a.exclusief".$vars["ttv"]." AS exclusief, a.afstandwinkel, a.afstandwinkelextra".$vars["ttv"]." AS afstandwinkelextra, a.afstandrestaurant, a.afstandrestaurantextra".$vars["ttv"]." AS afstandrestaurantextra, a.afstandpiste, a.afstandpisteextra".$vars["ttv"]." AS afstandpisteextra, a.afstandskilift, a.afstandskiliftextra".$vars["ttv"]." AS afstandskiliftextra, a.afstandloipe, a.afstandloipeextra".$vars["ttv"]." AS afstandloipeextra, a.afstandskibushalte, a.afstandskibushalteextra".$vars["ttv"]." AS afstandskibushalteextra, a.afstandstrand, a.afstandstrandextra".$vars["ttv"]." AS afstandstrandextra, a.afstandzwembad, a.afstandzwembadextra".$vars["ttv"]." AS afstandzwembadextra, a.afstandzwemwater, a.afstandzwemwaterextra".$vars["ttv"]." AS afstandzwemwaterextra, a.afstandgolfbaan, a.afstandgolfbaanextra".$vars["ttv"]." AS afstandgolfbaanextra FROM accommodatie a, type t, plaats p, skigebied s, land l WHERE p.land_id=l.land_id AND p.skigebied_id=s.skigebied_id AND a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND a.wzt IN (".$wzt.")".($alletypes ? " AND t.type_id IN (".$alletypes.")" : "")." ORDER BY a.accommodatie_id;");
		while($db->next_record()) {
			echo "	<accommodation>\n";
			echo "		<accommodation_id>".xml_text($db->f("accommodatie_id"))."</accommodation_id>\n";
			echo "		<name>".xml_text($db->f("naam"))."</name>\n";
			echo "		<resort>".xml_text($db->f("plaats"))."</resort>\n";
			echo "		<region>".xml_text($db->f("skigebied"))."</region>\n";
			echo "		<country>".xml_text($db->f("land"))."</country>\n";
			echo "		<kind>".xml_text($vars["soortaccommodatie"][$db->f("soortaccommodatie")])."</kind>\n";
			if($vars["websitetype"]=="6") {
				# ChaletsInVallandry: season
				echo "		<season>".xml_text(($db->f("wzt")==1 ? "winter" : "summer"))."</season>\n";
			}
			echo "		<description>".xml_text($db->f("omschrijving"))."</description>\n";
			echo "		<including>".xml_text($db->f("inclusief"))."</including>\n";
			echo "		<excluding>".xml_text($db->f("exclusief"))."</excluding>\n";
			if($db->f("wzt")==1) {
				# winter
				$doorloop_afstanden=array(
					"afstandwinkel"=>"distance_shop",
					"afstandrestaurant"=>"distance_restaurant",
					"afstandpiste"=>"distance_piste",
					"afstandskilift"=>"distance_skilift",
					"afstandloipe"=>"distance_crosscountry",
					"afstandskibushalte"=>"distance_skibusstop"
				);
			} elseif($db->f("wzt")==2) {
				# zomer
				$doorloop_afstanden=array(
					"afstandwinkel"=>"distance_shop",
					"afstandrestaurant"=>"distance_restaurant",
					"afstandstrand"=>"distance_beach",
					"afstandzwembad"=>"distance_pool",
					"afstandzwemwater"=>"distance_swimmingwater",
					"afstandgolfbaan"=>"distance_golfcourse"
				);
			}
			while(list($key,$value)=each($doorloop_afstanden)) {
				echo "		<".$value.">".($db->f($key) ? xml_text($db->f($key)) : "")."</".$value.">\n";
				echo "		<".$value."_extra>".xml_text($db->f($key."extra"))."</".$value."_extra>\n";
			}

			# Foto's
			echo "		<photos>\n";

			# hoofdfoto
			if(file_exists("../pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
				echo "			<main>".xml_text($vars["basehref"]."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")."</main>\n";
			}
			# extra foto's
			$foto=imagearray(array("accommodaties_aanvullend","accommodaties_aanvullend_onderaan","accommodaties_aanvullend_breed"),array($db->f("accommodatie_id"),$db->f("accommodatie_id"),$db->f("accommodatie_id")),"../");
			if(is_array($foto["pic"])) {
				while(list($key,$value)=each($foto["pic"])) {
					echo "			<extra>".xml_text($vars["basehref"]."pic/cms/".$value)."</extra>\n";
				}
			}
			if(is_array($foto["picbreed"])) {
				while(list($key,$value)=each($foto["picbreed"])) {
					echo "			<extra_wide>".xml_text($vars["basehref"]."pic/cms/".$value)."</extra_wide>\n";
				}
			}
			echo "		</photos>\n";

			echo "	</accommodation>\n";
		}

		echo "</accommodations>\n";

	} elseif($_GET["feedtype"]=="accommodation-units") {
		#
		# accommodation-units
		#
		echo "<accommodation_units>\n";
		$db->query("SELECT t.type_id, l.begincode, a.accommodatie_id, a.naam AS anaam, a.wzt, t.naam".$vars["ttv"]." AS naam, t.optimaalaantalpersonen, t.maxaantalpersonen, t.kwaliteit, t.omschrijving".$vars["ttv"]." AS omschrijving, t.inclusief".$vars["ttv"]." AS inclusief, t.exclusief".$vars["ttv"]." AS exclusief, t.indeling".$vars["ttv"]." AS indeling, t.oppervlakte, t.oppervlakteextra".$vars["ttv"]." AS oppervlakteextra, t.slaapkamers, t.slaapkamersextra".$vars["ttv"]." AS slaapkamersextra, t.badkamers, t.badkamersextra".$vars["ttv"]." AS badkamersextra FROM accommodatie a, type t, plaats p, skigebied s, land l WHERE p.land_id=l.land_id AND p.skigebied_id=s.skigebied_id AND a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND a.wzt IN (".$wzt.")".($alletypes ? " AND t.type_id IN (".$alletypes.")" : "")." ORDER BY t.type_id;");
		while($db->next_record()) {
			echo "	<accommodation_unit>\n";
			echo "		<unit_id>".xml_text($db->f("begincode").$db->f("type_id"))."</unit_id>\n";
			echo "		<accommodation_id>".xml_text($db->f("accommodatie_id"))."</accommodation_id>\n";
			echo "		<accommodation_name>".xml_text($db->f("anaam"))."</accommodation_name>\n";
			echo "		<unit_name>".xml_text($db->f("naam"))."</unit_name>\n";
			if($vars["websitetype"]=="6") {
				# ChaletsInVallandry: season
				echo "		<season>".xml_text(($db->f("wzt")==1 ? "winter" : "summer"))."</season>\n";
			}
			echo "		<capacity>".xml_text($db->f("optimaalaantalpersonen"))."</capacity>\n";
			echo "		<max_capacity>".xml_text($db->f("maxaantalpersonen"))."</max_capacity>\n";
			echo "		<quality>".($db->f("kwaliteit") ? xml_text($db->f("kwaliteit")) : "")."</quality>\n";
			echo "		<description>".xml_text($db->f("omschrijving"))."</description>\n";
			echo "		<including>".xml_text($db->f("inclusief"))."</including>\n";
			echo "		<excluding>".xml_text($db->f("exclusief"))."</excluding>\n";
			echo "		<layout>".xml_text($db->f("indeling"))."</layout>\n";
			echo "		<area>".($db->f("oppervlakte") ? xml_text($db->f("oppervlakte")) : "")."</area>\n";
			echo "		<area_extra>".xml_text($db->f("oppervlakteextra"))."</area_extra>\n";
			echo "		<bedrooms>".xml_text($db->f("slaapkamers"))."</bedrooms>\n";
			echo "		<bedrooms_extra>".xml_text($db->f("slaapkamersextra"))."</bedrooms_extra>\n";
			echo "		<bathrooms>".xml_text($db->f("badkamers"))."</bathrooms>\n";
			echo "		<bathrooms_extra>".xml_text($db->f("badkamersextra"))."</bathrooms_extra>\n";
			echo "	</accommodation_unit>\n";

			# Foto's
			echo "		<photos>\n";

			# hoofdfoto
			if(file_exists("../pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
				echo "			<main>".xml_text($vars["basehref"]."pic/cms/types_specifiek/".$db->f("type_id").".jpg")."</main>\n";
			}

			# extra foto's
			$foto=imagearray(array("types","types_breed"),array($db->f("type_id"),$db->f("type_id")),"../");

			if(is_array($foto["pic"])) {
				while(list($key,$value)=each($foto["pic"])) {
					echo "			<extra>".xml_text($vars["basehref"]."pic/cms/".$value)."</extra>\n";
				}
			}
			if(is_array($foto["picbreed"])) {
				while(list($key,$value)=each($foto["picbreed"])) {
					echo "			<extra_wide>".xml_text($vars["basehref"]."pic/cms/".$value)."</extra_wide>\n";
				}
			}
			echo "		</photos>\n";

		}
		echo "</accommodation_units>\n";
	} elseif($_GET["feedtype"]=="availability-and-prices") {
		#
		# availability-and-prices
		#
		echo "<availability_and_prices>\n";

		$availability_keuzes[1]="directly";
		$availability_keuzes[2]="request";
		$availability_keuzes[3]="unavailable";

		# Afwijkende vertrekdagen?
		if($alletypes) {
			$typeid_inquery_vertrekdag=$alletypes;
		} else {
			$typeid_inquery_vertrekdag="ALL";
		}
		include("../content/vertrekdagaanpassing.html");

		$db->query("SELECT t.type_id, ta.wederverkoop_verkoopprijs, ta.beschikbaar, ta.week, ta.seizoen_id, ta.c_bruto, ta.bruto, ta.voorraad_garantie, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, ta.voorraad_optie_leverancier, ta.voorraad_xml, ta.voorraad_request, ta.voorraad_optie_klant, l.begincode, a.toonper, a.accommodatie_id, a.aankomst_plusmin, a.vertrek_plusmin FROM tarief ta, accommodatie a, type t, plaats p, skigebied s, land l WHERE ta.week>'".time()."' AND ta.blokkeren_wederverkoop=0 AND ta.type_id=t.type_id AND p.land_id=l.land_id AND p.skigebied_id=s.skigebied_id AND a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND a.wzt IN (".$wzt.")".($alletypes ? " AND t.type_id IN (".$alletypes.")" : "")." ORDER BY t.type_id, ta.week;");
		while($db->next_record()) {
			$aanbieding[$db->f("seizoen_id")]=aanbiedinginfo($db->f("type_id"),$db->f("seizoen_id"),$db->f("week"));
			if(($db->f("toonper")==3 and $db->f("c_bruto")>0) or ($db->f("toonper")==1 and $db->f("bruto")>0)) {
				echo "	<accommodation_unit>\n";
				echo "		<unit_id>".xml_text($db->f("begincode").$db->f("type_id"))."</unit_id>\n";

				# Exacte aankomstdatum
				$week=$db->f("week");
				if($vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)] or $db->f("aankomst_plusmin")) {
					$aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)]+$db->f("aankomst_plusmin"),date("Y",$week));
					$exacte_unixtime=$aangepaste_unixtime;
				} else {
					$exacte_unixtime=$week;
				}
				echo "		<date>".xml_text(date("Y-m-d",$exacte_unixtime))."</date>\n";

				# Aantal nachten

				# Afwijkende vertrekdag
				unset($aantalnachten_afwijking);
				$aantalnachten_afwijking=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)];
				$volgendeweek=mktime(0,0,0,date("n",$week),date("j",$week)+7,date("Y",$week));
				$aantalnachten_afwijking-=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$volgendeweek)];
				# Afwijkende verblijfsduur
				$aantalnachten_afwijking=$aantalnachten_afwijking+$db->f("aankomst_plusmin")-$db->f("vertrek_plusmin");
				$nachten=7-$aantalnachten_afwijking;
				echo "		<nights>".xml_text($nachten)."</nights>\n";


				# Beschikbaarheid
				$availability=3; # standaard: niet beschikbaar
				if($db->f("wederverkoop_verkoopprijs")>0) {
					# 1 = groen, 2 = oranje, 3 = zwart
					if($db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")-$db->f("voorraad_optie_klant")>=1) {
						$availability=1;
					} elseif($db->f("voorraad_request")>=1 or $db->f("voorraad_optie_klant")>=1) {
						# 'request' uitgezet op verzoek van Barteld (23-10-2012)
#						$availability=2;
					}
				}
				if($availability<>3) {
					# Prijs
					# aanbiedingen verwerken
					$prijs=verwerk_aanbieding($db->f("wederverkoop_verkoopprijs"),$aanbieding[$db->f("seizoen_id")]["typeid_sort"][$db->f("type_id")],$db->f("week"));
					echo "		<price>".xml_text(number_format($prijs,2,".",""))."</price>\n";
					if($prijs>0 and $prijs<>$db->f("wederverkoop_verkoopprijs")) {
#						echo "		<special_offer>true</special_offer>\n";
					} else {
#						echo "		<special_offer>false</special_offer>\n";
					}
				}
				echo "		<availability>".xml_text($availability_keuzes[$availability])."</availability>\n";

				echo "	</accommodation_unit>\n";
			}
		}
		echo "</availability_and_prices>\n";
	}
} else {
	echo "Error: wrong URL. Please contact us.";
}

?>