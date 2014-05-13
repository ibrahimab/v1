<?php

#
#
# XML-export van alle accommodaties t.b.v. TradeTracker
#
# ========================>>>>>>>>>>>>>>>> LET OP: bij deze file wordt een cache gebruikt! <<<<<<<<<<<<<<<<==================================
#
# Elke dag om 04:00 uur en 18:00 uur wordt de nieuwe cache aangemaakt (via cron/elkuur.php)
#
# handmatig starten aanmaken cache: /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/elkuur.php xmlopnieuw
#
#
#

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");
if($_GET["aanbiedingen"]) {
	$cachefile=$unixdir."cache/feed_tradetracker_aanbiedingen_".$vars["website"].".xml";
} else {
	$cachefile=$unixdir."cache/feed_tradetracker_".$vars["website"].".xml";
}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	header("Content-Type: text/plain; charset=utf-8");
} elseif(@filemtime($cachefile)>=(time()-86400) and !$_GET["nocache"]) {
	header("Content-Type: text/xml; charset=utf-8");
	$content=file_get_contents($cachefile);
	echo $content;
	exit;
} else {
	header("Content-Type: text/xml; charset=utf-8");
	echo "<";
	echo "?xml version=\"1.0\" encoding=\"utf-8\"?";
	echo ">\n";
}

#echo "<productFeed xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"http://www.cleafs.com/tracker/productsfeed.xsd\">\n";
echo "<productFeed version=\"1.0\" timestamp=\"".date("Ymd:H:i:s")."\">\n";


# aanbiedingen
unset($aanbieding_inquery,$aanbieding);
$db->query("SELECT seizoen_id FROM seizoen WHERE tonen>=2 AND type IN (".addslashes($vars["seizoentype"]).") AND eind>='".date("Y-m-d")."' ORDER BY begin;");
while($db->next_record()) {

	# gewone aanbiedingen ophalen
	$aanbieding[$db->f("seizoen_id")]=aanbiedinginfo(0,$db->f("seizoen_id"));

	if($_GET["aanbiedingen"]) {
		# Aleen aanbiedingen tonen
		while(list($key,$value)=@each($aanbieding[$db->f("seizoen_id")]["typeid_sort"])) {
			if($key and $aanbieding[$db->f("seizoen_id")]["typeid_sort"][$key]["toon_als_aanbieding"]) {
				$aanbieding_inquery.=",".$key;
			}
		}
	}
}

if($_GET["aanbiedingen"]) {
	# aanbiedingen uit kortingensysteem ophalen
	$db2->query("SELECT DISTINCT type_id FROM tarief WHERE 1=1 AND (c_bruto>0 OR bruto>0) AND beschikbaar=1 AND week>'".time()."' AND aanbiedingskleur_korting=1 AND (aanbieding_acc_percentage>0 OR aanbieding_acc_euro>0) AND kortingactief=1;");
	while($db2->next_record()) {
		$aanbieding_inquery.=",".$db2->f("type_id");
	}
}

$db->query("SELECT DISTINCT t.type_id, a.accommodatie_id, a.toonper, a.naam, a.kenmerken AS akenmerken, t.naam AS tnaam, a.zoekvolgorde AS azoekvolgorde, a.omschrijving, a.kwaliteit, a.gps_lat, a.gps_long, a.afstandwinkel, a.afstandwinkelextra, a.afstandrestaurant, a.afstandrestaurantextra, a.afstandpiste, a.afstandpisteextra, a.afstandskilift, a.afstandskiliftextra, a.afstandloipe, a.afstandloipeextra, a.afstandskibushalte, a.afstandskibushalteextra, a.afstandstrand, a.afstandstrandextra, a.afstandzwembad, a.afstandzwembadextra, a.afstandzwemwater, a.afstandzwemwaterextra, a.afstandgolfbaan, a.afstandgolfbaanextra, t.kwaliteit AS tkwaliteit, t.omschrijving AS tomschrijving, t.zoekvolgorde AS tzoekvolgorde, lv.zoekvolgorde AS lzoekvolgorde, t.optimaalaantalpersonen, t.maxaantalpersonen, a.soortaccommodatie, t.slaapkamers, t.badkamers, t.kenmerken AS tkenmerken, s.skigebied_id, s.naam AS skigebied, l.naam AS land, l.begincode, p.naam AS plaats, p.plaats_id FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".$vars["website"]."%' AND a.tonen=1 AND a.archief=0 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0".($aanbieding_inquery ? " AND t.type_id IN (".substr($aanbieding_inquery,1).")" : "").($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? " LIMIT 0,10" : "").";");
while($db->next_record()) {

	# Prijs bepalen
	unset($prijs, $alleprijzen, $korting_euro, $korting_percentage);
	if($db->f("toonper")==3) {
		$db2->query("SELECT c_verkoop_site AS prijs, week, seizoen_id, aanbiedingskleur_korting, aanbieding_acc_percentage, aanbieding_acc_euro, kortingactief, toonexactekorting FROM tarief WHERE type_id='".$db->f("type_id")."' AND week>'".(time()+604800)."' AND c_verkoop_site>0 AND beschikbaar=1;");
		while($db2->next_record()) {
			$alleprijzen[$db2->f("week")]=$db2->f("prijs");
			# Aanbiedingen verwerken
			$alleprijzen[$db2->f("week")]=verwerk_aanbieding($alleprijzen[$db2->f("week")],$aanbieding[$db2->f("seizoen_id")]["typeid_sort"][$db->f("type_id")],$db2->f("week"));
			if($alleprijzen[$db2->f("week")]==0) unset($alleprijzen[$db2->f("week")]);

			# Maximale korting bepalen
			if($db2->f("kortingactief") and $db2->f("aanbiedingskleur_korting") and $db2->f("toonexactekorting")) {
				if($db2->f("aanbieding_acc_percentage")>0) {
					if($korting_percentage<$db2->f("aanbieding_acc_percentage")) $korting_percentage=$db2->f("aanbieding_acc_percentage");
				}
				if($db2->f("aanbieding_acc_euro")>0) {
					if($korting_euro<$db2->f("aanbieding_acc_euro")) $korting_euro=$db2->f("aanbieding_acc_euro");
				}
			}

		}
		if(is_array($alleprijzen)) {
			$prijs=min($alleprijzen);
		}
	} else {
		$db2->query("SELECT tp.prijs AS prijs, t.week, t.seizoen_id, t.aanbiedingskleur_korting, t.aanbieding_acc_percentage, t.aanbieding_acc_euro, t.kortingactief, t.toonexactekorting FROM tarief t, tarief_personen tp WHERE tp.type_id='".$db->f("type_id")."' AND tp.week>'".(time()+604800)."' AND tp.prijs>0 AND tp.personen='".$db->f("maxaantalpersonen")."' AND t.beschikbaar=1 AND t.type_id=tp.type_id AND t.week=tp.week AND t.seizoen_id=tp.seizoen_id;");
		while($db2->next_record()) {
			$alleprijzen[$db2->f("week")]=$db2->f("prijs");
			# Aanbiedingen verwerken
			$alleprijzen[$db2->f("week")]=verwerk_aanbieding($alleprijzen[$db2->f("week")],$aanbieding[$db2->f("seizoen_id")]["typeid_sort"][$db->f("type_id")],$db2->f("week"));
			if($alleprijzen[$db2->f("week")]==0) unset($alleprijzen[$db2->f("week")]);

			# Maximale korting bepalen
			if($db2->f("kortingactief") and $db2->f("aanbiedingskleur_korting") and $db2->f("toonexactekorting")) {
				if($db2->f("aanbieding_acc_percentage")>0) {
					if($korting_percentage<$db2->f("aanbieding_acc_percentage")) $korting_percentage=$db2->f("aanbieding_acc_percentage");
				}
#				if($db2->f("aanbieding_acc_euro")>0) {
#					if($korting_euro<$db2->f("aanbieding_acc_euro")) $korting_euro=$db2->f("aanbieding_acc_euro");
#				}
			}
		}
		if(is_array($alleprijzen)) {
			$prijs=min($alleprijzen);
		}
	}

	if($prijs) {
		echo "<product id=\"".$db->f("type_id")."\">\n";

		$aantalpersonen=$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? "persoon" : "personen");
		$accnaam=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." - ".$aantalpersonen;

		echo "<name>".xml_text($accnaam)."</name>\n";
		echo "<price>".$prijs."</price>\n";

		unset($description);
		if($db->f("omschrijving") or $db->f("tomschrijving")) {
			$description=$db->f("omschrijving");
			if($db->f("omschrijving") and $db->f("tomschrijving")) {
				$description.="\n\n";
			}
			$description.=$db->f("tomschrijving");
			echo "<description>".xml_text($description)."</description>\n";
		}

		$url=$vars["basehref"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/";
		echo "<productURL>".xml_text($url)."</productURL>\n";

		$imgurl="";
		if(file_exists("../pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
			$imgurl=$vars["basehref"]."pic/cms/types_specifiek/".$db->f("type_id").".jpg";
		} elseif(file_exists("../pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
			$imgurl=$vars["basehref"]."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
		}
		if($imgurl) {
			echo "<imageURL>".xml_text($imgurl)."</imageURL>\n";
		}
		echo "<additional>\n";
		echo "<field name=\"country\" value=\"".xml_text($db->f("land"),false)."\" />\n";
		if($vars["seizoentype"]==1) {
			echo "<field name=\"ski_area\" value=\"".xml_text($db->f("skigebied"),false)."\" />\n";
			echo "<field name=\"ski_area_id\" value=\"".xml_text($db->f("skigebied_id"),false)."\" />\n";
			echo "<field name=\"resort\" value=\"".xml_text($db->f("plaats"),false)."\" />\n";
			echo "<field name=\"resort_id\" value=\"".xml_text($db->f("plaats_id"),false)."\" />\n";
		} else {
			echo "<field name=\"region\" value=\"".xml_text($db->f("skigebied"),false)."\" />\n";
			echo "<field name=\"region_id\" value=\"".xml_text($db->f("skigebied_id"),false)."\" />\n";
			echo "<field name=\"city\" value=\"".xml_text($db->f("plaats"),false)."\" />\n";
			echo "<field name=\"city_id\" value=\"".xml_text($db->f("plaats_id"),false)."\" />\n";
		}
		echo "<field name=\"persons_from\" value=\"".xml_text($db->f("optimaalaantalpersonen"),false)."\" />\n";
		echo "<field name=\"persons_to\" value=\"".xml_text($db->f("maxaantalpersonen"),false)."\" />\n";
		if($db->f("toonper")==3) {
			echo "<field name=\"price_description\" value=\"".xml_text("vanaf ".number_format($prijs,2,",",".")." euro per accommodatie",false)."\" />\n";
		} else {
			echo "<field name=\"price_description\" value=\"".xml_text("vanaf ".number_format($prijs,2,",",".")." euro per persoon incl. skipas",false)."\" />\n";
		}
		if($korting_percentage) {
			echo "<field name=\"special_offer_percentage\" value=\"".xml_text(floor($korting_percentage),false)."\" />\n";
			echo "<field name=\"special_offer_description\" value=\"".xml_text("Boek nu met een korting tot ".floor($korting_percentage)."%",false)."\" />\n";
		} elseif($korting_euro) {
			echo "<field name=\"special_offer_euro\" value=\"".xml_text($korting_euro,false)."\" />\n";
			echo "<field name=\"special_offer_description\" value=\"".xml_text("Boek nu met een korting tot ".$korting_euro." euro",false)."\" />\n";
		} elseif($_GET["aanbiedingen"]) {
			echo "<field name=\"special_offer_description\" value=\"".xml_text("Boek nu met korting",false)."\" />\n";
		}
		echo "<field name=\"number_of_nights\" value=\"".xml_text("7",false)."\" />\n";

		if($db->f("kwaliteit") or $db->f("tkwaliteit")) {
			if($db->f("tkwaliteit")) {
				$kwaliteit=$db->f("tkwaliteit");
			} else {
				$kwaliteit=$db->f("kwaliteit");
			}
			echo "<field name=\"stars\" value=\"".xml_text($kwaliteit,false)."\" />\n";
		}

		if($db->f("gps_lat") and $db->f("gps_long")) {
			echo "<field name=\"gps_latitude\" value=\"".xml_text($db->f("gps_lat"),false)."\" />\n";
			echo "<field name=\"gps_longitude\" value=\"".xml_text($db->f("gps_long"),false)."\" />\n";
		}

		# Afstanden
		if($vars["seizoentype"]==1) {
			# winter
			$doorloop_afstanden=array(
				"afstandwinkel"=>"distance_shop",
				"afstandrestaurant"=>"distance_restaurant",
				"afstandpiste"=>"distance_piste",
				"afstandskilift"=>"distance_skilift",
				"afstandloipe"=>"distance_crosscountry",
				"afstandskibushalte"=>"distance_skibusstop"
			);
		} elseif($vars["seizoentype"]==2) {
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
		while(list($key,$value)=@each($doorloop_afstanden)) {
			if($db->f($key)) {
				echo "<field name=\"".$value."\" value=\"".xml_text(toonafstand($db->f($key),$db->f($key."extra"),txt("meter","toonaccommodatie")),false)."\" />\n";
			}
		}

		# Aanvullende afbeeldingen
		$foto=imagearray(array("accommodaties_aanvullend","types","accommodaties_aanvullend_onderaan","accommodaties_aanvullend_breed","types_breed"),array($db->f("accommodatie_id"),$db->f("type_id"),$db->f("accommodatie_id"),$db->f("accommodatie_id"),$db->f("type_id")),"../");
		if(is_array($foto["pic"])) {
			$fototeller=0;
			while(list($key,$value)=each($foto["pic"])) {
				$fototeller++;

				# Op Chalet.be maximaal 10 foto's (op verzoek van TradeTracker)
				if($fototeller<=10 or $vars["website"]<>"B") {
					echo "<field name=\"extra_image_".$fototeller."\" value=\"".xml_text($vars["basehref"]."pic/cms/".$value,false)."\" />\n";
				}
			}
		}
		if(is_array($foto["picbreed"])) {
			$fototeller=0;
			while(list($key,$value)=each($foto["picbreed"])) {
				$fototeller++;
				echo "<field name=\"extra_image_wide_".$fototeller."\" value=\"".xml_text($vars["basehref"]."pic/cms/".$value,false)."\" />\n";
			}
		}

		// prices and dates
		if(!$_GET["aanbiedingen"]) {
			$prijzen_teller=0;
			foreach ($alleprijzen as $key => $value) {
				$prijzen_teller++;
				echo "<field name=\"arrival_date_".$prijzen_teller."\" value=\"".date("d-m-Y", $key)."\" />\n";
				echo "<field name=\"price_".$prijzen_teller."\" value=\"".$value."\" />\n";
			}
		}


		echo "</additional>\n";
		echo "<categories>\n";
		$categorie=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")]);
		echo "<category name=\"".xml_text($categorie.(eregi("appartement",$categorie)||eregi("vakantiewoning",$categorie) ? "en" : "s"))."\" />\n";
		echo "</categories>\n";
		echo "</product>\n";
	}
}

echo "</productFeed>\n";

?>