<?php

#
#
# CSV-export van alle accommodaties t.b.v. Traffic4U
#
# ========================>>>>>>>>>>>>>>>> LET OP: bij deze file wordt een cache gebruikt! <<<<<<<<<<<<<<<<==================================
#
# Elke dag om 03:00 uur wordt de nieuwe cache aangemaakt (via cron/elkuur.php)
#
# handmatig starten aanmaken cache: /usr/bin/php /var/www/chalet.nl/html/cron/elkuur.php traffic4u
#
#
#


// Premature end of script headers
// mod_fcgid: read data timeout in 40 seconds
// http://expressionengine.stackexchange.com/questions/7467/mod-fcgid-read-data-timeout-in-45-seconds-premature-end-of-script-headers

/*

URL's or all feeds:

https://www.chalet.nl/xml/traffic4u.php?feed=accommodaties
https://www.chalet.nl/xml/traffic4u.php?feed=bestemmingen
https://www.chalet.nl/xml/traffic4u.php?feed=bestemmingen-aantal-personen
https://www.chalet.nl/xml/traffic4u.php?feed=land-aantal-personen
https://www.chalet.nl/xml/traffic4u.php?feed=aantal-personen

https://www.italissima.nl/xml/traffic4u.php?feed=accommodaties
https://www.italissima.nl/xml/traffic4u.php?feed=bestemmingen
https://www.italissima.nl/xml/traffic4u.php?feed=bestemmingen-aantal-personen

*/

// minimaal aantal zoekresultaten om een combinatie in de feed op te nemen
$vars["min_aantal_resultaten_traffic4u"]=3;


set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");
$cachefile=$unixdir."cache/feed_traffic4u_".basename($_GET["feed"])."_".$vars["website"].".csv";

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	header("Content-Type: text/plain; charset=utf-8");
	header("Content-Disposition: attachment; filename=\"".basename($_GET["feed"]).".csv\";" );

	# UTF-8 BOM
	echo "\xEF\xBB\xBF";
} elseif(!$_GET["nocache"] and ($_GET["feed"]=="bestemmingen" or $_GET["feed"]=="bestemmingen-aantal-personen" or $_GET["feed"]=="land-aantal-personen" or $_GET["feed"]=="aantal-personen")) {
	header("Content-Type: application/octet-stream; charset=utf-8");
	header("Content-Disposition: attachment; filename=\"".basename($_GET["feed"]).".csv\";" );
	$content=file_get_contents($cachefile);
	echo $content;
	exit;
} else {
	header("Content-Type: application/octet-stream; charset=utf-8");
	header("Content-Disposition: attachment; filename=\"".basename($_GET["feed"]).".csv\";" );

	# UTF-8 BOM
	echo "\xEF\xBB\xBF";
}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2") {
	define(wt_csvconvert_delimiter,",");
} else {
	define(wt_csvconvert_delimiter,";");
}

# header
if($_GET["feed"]=="accommodaties") {
	#
	# Feed 1: accommodaties
	#

	if($vars["seizoentype"]==1) {
		if($vars["website"] == "D"){
			echo "Land".wt_csvconvert_delimiter."Skigebied".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Soort accommodatie".wt_csvconvert_delimiter."Accommodatienaam".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."Accommodatie-URL".wt_csvconvert_delimiter."Klassering\n";
		} else {
			echo "Land".wt_csvconvert_delimiter."Skigebied".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Soort accommodatie".wt_csvconvert_delimiter."Accommodatienaam".wt_csvconvert_delimiter."Typenaam".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."Accommodatie-URL".wt_csvconvert_delimiter."Afstand tot de piste".wt_csvconvert_delimiter."Afstand tot restaurant".wt_csvconvert_delimiter."Afstand tot winkels".wt_csvconvert_delimiter."Klassering\n";
		}
	} else {
		echo "Land".wt_csvconvert_delimiter."Regio".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Soort accommodatie".wt_csvconvert_delimiter."Accommodatienaam".wt_csvconvert_delimiter."Typenaam".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."Accommodatie-URL".wt_csvconvert_delimiter."Afstand tot restaurant".wt_csvconvert_delimiter."Afstand tot winkels".wt_csvconvert_delimiter."Klassering\n";
	}
	$db->query("SELECT DISTINCT t.type_id, a.accommodatie_id, a.toonper, a.naam, a.kenmerken AS akenmerken, t.naam AS tnaam, a.zoekvolgorde AS azoekvolgorde, a.omschrijving, a.kwaliteit, a.gps_lat, a.gps_long, a.afstandwinkel, a.afstandwinkelextra, a.afstandrestaurant, a.afstandrestaurantextra, a.afstandpiste, a.afstandpisteextra, a.afstandskilift, a.afstandskiliftextra, a.afstandloipe, a.afstandloipeextra, a.afstandskibushalte, a.afstandskibushalteextra, a.afstandstrand, a.afstandstrandextra, a.afstandzwembad, a.afstandzwembadextra, a.afstandzwemwater, a.afstandzwemwaterextra, a.afstandgolfbaan, a.afstandgolfbaanextra, t.kwaliteit AS tkwaliteit, t.omschrijving AS tomschrijving, t.zoekvolgorde AS tzoekvolgorde, lv.zoekvolgorde AS lzoekvolgorde, t.optimaalaantalpersonen, t.maxaantalpersonen, a.soortaccommodatie, t.slaapkamers, t.badkamers, t.kenmerken AS tkenmerken, s.skigebied_id, s.naam AS skigebied, l.naam AS land, l.begincode, p.naam AS plaats, p.plaats_id FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".$vars["website"]."%' AND a.tonen=1 AND a.archief=0 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0".($aanbieding_inquery ? " AND t.type_id IN (".substr($aanbieding_inquery,1).")" : "")." ORDER BY t.type_id".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? " LIMIT 0,10" : "").";");
	while($db->next_record()) {
		for($i=$db->f("optimaalaantalpersonen");$i<=$db->f("maxaantalpersonen");$i++) {
			echo wt_csvconvert(utf8_encode($db->f("land"))).wt_csvconvert_delimiter;
			echo wt_csvconvert(utf8_encode($db->f("skigebied"))).wt_csvconvert_delimiter;
			echo wt_csvconvert(utf8_encode($db->f("plaats"))).wt_csvconvert_delimiter;

			echo wt_csvconvert(utf8_encode(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")]))).wt_csvconvert_delimiter;

			// $accnaam=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "");
			echo wt_csvconvert(utf8_encode($db->f("naam"))).wt_csvconvert_delimiter;
			if($vars["website"] !== "D"){
				echo wt_csvconvert(utf8_encode($db->f("tnaam"))).wt_csvconvert_delimiter;
			}
			echo wt_csvconvert($i).wt_csvconvert_delimiter;
			echo wt_csvconvert(utf8_encode($vars["basehref"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/")).wt_csvconvert_delimiter;

			if($vars["seizoentype"]==1) {

					if($db->f("afstandpiste")) {
						if($vars["website"] !== "D") {
							echo wt_csvconvert(utf8_encode(toonafstand($db->f("afstandpiste"),$db->f("afstandpisteextra"),txt("meter","toonaccommodatie")))).wt_csvconvert_delimiter;
						}
					} else {
						echo wt_csvconvert_delimiter;
					}
			}

			if($db->f("afstandrestaurant")) {
				if($vars["website"] !== "D"){
					echo wt_csvconvert(utf8_encode(toonafstand($db->f("afstandrestaurant"),$db->f("afstandrestaurantextra"),txt("meter","toonaccommodatie")))).wt_csvconvert_delimiter;
				}
			} else {
				echo wt_csvconvert_delimiter;
			}

			if($db->f("afstandwinkel")) {
				if($vars["website"] !== "D"){
					echo wt_csvconvert(utf8_encode(toonafstand($db->f("afstandwinkel"),$db->f("afstandwinkelextra"),txt("meter","toonaccommodatie")))).wt_csvconvert_delimiter;
				}
			} else {
				echo wt_csvconvert_delimiter;
			}

			$kwaliteit="";
			if($db->f("kwaliteit") or $db->f("tkwaliteit")) {
				if($db->f("tkwaliteit")) {
					$kwaliteit=$db->f("tkwaliteit");
				} else {
					$kwaliteit=$db->f("kwaliteit");
				}
			}
			echo wt_csvconvert($kwaliteit);

			echo "\n";
		}
	}
} elseif($_GET["feed"]=="bestemmingen" or $_GET["feed"]=="bestemmingen-aantal-personen") {
	#
	# Feed 2: bestemmingen en thema's
	# Feed 3: bestemmingen en aantal personen
	#


	if($vars["seizoentype"]==1) {
		# winter

		if($_GET["feed"]=="bestemmingen-aantal-personen") {
			$doorloop_array=array(
				"fap=2"=>"2",
				"fap=3"=>"3",
				"fap=4"=>"4",
				"fap=5"=>"5",
				"fap=6"=>"6",
				"fap=7"=>"7",
				"fap=8"=>"8",
				"fap=9"=>"9",
				"fap=10"=>"10",
				"fap=11"=>"11",
				"fap=12"=>"12",
				"fap=13"=>"13",
				"fap=14"=>"14",
				"fap=15"=>"15",
				"fap=16"=>"16",
				"fap=17"=>"17",
				"fap=18"=>"18",
				"fap=19"=>"19",
				"fap=20"=>"20"
			);
		} else {

			$doorloop_array=array(
				"vf_piste1=1"=>"aan de piste",
				"vf_kenm2=1"=>"catering mogelijk",
				"vf_kenm4=1"=>"sauna",
				"vf_kenm43=1"=>"goed voor kids",
				"vf_kenm6=1"=>"huisdieren toegestaan",
			);
		}

		if($_GET["feed"]=="bestemmingen-aantal-personen") {
			echo "Land".wt_csvconvert_delimiter."Skigebied".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."URL skigebied + aantal personen".wt_csvconvert_delimiter."Aantal accommodaties skigebied + aantal personen".wt_csvconvert_delimiter."URL plaats + aantal personen".wt_csvconvert_delimiter."Aantal accommodaties plaats + aantal personen\n";
		} else {
			echo "Land".wt_csvconvert_delimiter."Skigebied".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Thema".wt_csvconvert_delimiter."URL skigebied".wt_csvconvert_delimiter."URL plaats".wt_csvconvert_delimiter."URL skigebied + thema".wt_csvconvert_delimiter."Aantal accommodaties skigebied + thema".wt_csvconvert_delimiter."URL plaats + thema".wt_csvconvert_delimiter."Aantal accommodaties plaats + thema\n";
		}

		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$db4->query("SELECT DISTINCT land_id, land, skigebied, skigebied_id, plaats_id, plaats FROM view_accommodatie WHERE websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 AND skigebied_id=2 ORDER BY skigebied_id, plaats_id;");
		} else {
			$db4->query("SELECT DISTINCT land_id, land, skigebied, skigebied_id, plaats_id, plaats FROM view_accommodatie WHERE websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 ORDER BY skigebied_id, plaats_id;");
		}

	} else {
		# Italissima

		if($_GET["feed"]=="bestemmingen-aantal-personen") {
			$doorloop_array=array(
				"fap=2"=>"2",
				"fap=3"=>"3",
				"fap=4"=>"4",
				"fap=5"=>"5",
				"fap=6"=>"6",
				"fap=7"=>"7",
				"fap=8"=>"8",
				"fap=9"=>"9",
				"fap=10"=>"10"
			);
		} else {
			$doorloop_array=array(
				"vf_kenm21=1"=>"kindvriendelijk",
				"vf_kenm18=1"=>"privé-zwembad",
				"vf_kenm8=1"=>"gemeenschappelijk zwembad",
				"vf_kenm37=1"=>"restaurant",
				"vf_kenm19=1"=>"huisdieren toegestaan",
			);
		}


		if($_GET["feed"]=="bestemmingen-aantal-personen") {
			echo "Land".wt_csvconvert_delimiter."Regio".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."URL regio + aantal personen".wt_csvconvert_delimiter."Aantal accommodaties regio + aantal personen\n";
		} else {
			echo "Land".wt_csvconvert_delimiter."Regio".wt_csvconvert_delimiter."Thema".wt_csvconvert_delimiter."URL regio".wt_csvconvert_delimiter."URL regio + thema".wt_csvconvert_delimiter."Aantal accommodaties regio + thema\n";
		}

		$db4->query("SELECT DISTINCT land_id, land, skigebied, skigebied_id FROM view_accommodatie WHERE websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 ORDER BY skigebied_id;");
		// $db4->query("SELECT DISTINCT land_id, land, skigebied, skigebied_id FROM view_accommodatie WHERE websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 AND skigebied_id IN (124);");

	}
	$result_teller=0;
	while($db4->next_record()) {

		$result_teller++;

		// if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and $_GET["feed"]=="bestemmingen" and $result_teller>=4) {
		// 	continue;
		// }

		reset($doorloop_array);

		foreach ($doorloop_array as $key99 => $value99) {


			# skigebied-url
			if($skigebied_al_gehad[$db4->f("skigebied_id").$key99]) {

				$temp_skigebied_url=$skigebied_al_gehad_url[$db4->f("skigebied_id").$key99];
				$aantal_resultaten_skigebied=$skigebied_al_gehad_aantal[$db4->f("skigebied_id").$key99];

			} else {

				$skigebied_al_gehad[$db4->f("skigebied_id").$key99]=true;

				unset($aantal_resultaten_skigebied);

				$temp_skigebied_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&".$key99."&fsg=".$db4->f("land_id")."-".$db4->f("skigebied_id");
				if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
					$content='aaa data-aantalgevonden="88" aa';
				} else {
					$content=file_get_contents($temp_skigebied_url);
				}

				if(preg_match("/data-aantalgevonden=\"([0-9]+)\"/",$content,$regs)) {
					if($regs[1]>0) {
						$aantal_resultaten_skigebied=$regs[1];

						$skigebied_al_gehad_url[$db4->f("skigebied_id").$key99]=$temp_skigebied_url;
						$skigebied_al_gehad_aantal[$db4->f("skigebied_id").$key99]=$aantal_resultaten_skigebied;

					}
				}
			}

			if($vars["seizoentype"]==1) {
				# plaats-url
				unset($aantal_resultaten_plaats);
				$temp_plaats_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&".$key99."&fsg=pl".$db4->f("plaats_id");

				if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
					$content="aaa data-aantalgevonden=\"88\" aa";
				} else {
					$content=file_get_contents($temp_plaats_url);
				}

				if(preg_match("/data-aantalgevonden=\"([0-9]+)\"/",$content,$regs)) {
					if($regs[1]>0) {
						$aantal_resultaten_plaats=$regs[1];
					}
				}
			}

			if($aantal_resultaten_skigebied>=$vars["min_aantal_resultaten_traffic4u"] or $aantal_resultaten_plaats>=$vars["min_aantal_resultaten_traffic4u"]) {
				echo wt_csvconvert(utf8_encode($db4->f("land"))).wt_csvconvert_delimiter;
				echo wt_csvconvert(utf8_encode($db4->f("skigebied"))).wt_csvconvert_delimiter;
				if($vars["seizoentype"]==1) {
					echo wt_csvconvert(utf8_encode($db4->f("plaats"))).wt_csvconvert_delimiter;
				}
				echo wt_csvconvert(utf8_encode($value99)).wt_csvconvert_delimiter;


				if($_GET["feed"]=="bestemmingen") {
					//
					// show direct links to regions and vilages
					//

					// region
					$regio_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&fsg=".$db4->f("land_id")."-".$db4->f("skigebied_id");
					echo wt_csvconvert(utf8_encode($regio_url)).wt_csvconvert_delimiter;

					if($vars["seizoentype"]==1) {
						// village (only winter sites)
						$plaats_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&fsg=pl".$db4->f("plaats_id");
						echo wt_csvconvert(utf8_encode($plaats_url)).wt_csvconvert_delimiter;
					}
				}

				if($aantal_resultaten_skigebied>=$vars["min_aantal_resultaten_traffic4u"]) {
					echo wt_csvconvert(utf8_encode($temp_skigebied_url));
				}

				echo wt_csvconvert_delimiter;

				# Aantal accommodaties skigebied + thema
				if($aantal_resultaten_skigebied>=$vars["min_aantal_resultaten_traffic4u"]) {
					echo intval($aantal_resultaten_skigebied);
				} else {
					echo "0";
				}

				if($vars["seizoentype"]==1) {
					echo wt_csvconvert_delimiter;

					if($aantal_resultaten_plaats>=$vars["min_aantal_resultaten_traffic4u"]) {
						echo wt_csvconvert(utf8_encode($temp_plaats_url));
					}
					echo wt_csvconvert_delimiter;;

					# Aantal accommodaties plaats + thema
					if($aantal_resultaten_plaats>=$vars["min_aantal_resultaten_traffic4u"]) {
						echo intval($aantal_resultaten_plaats);
					} else {
						echo "0";
					}
				}

				echo "\n";

			}

			if($_SERVER["DOCUMENT_ROOT"]!="/home/webtastic/html") {
				sleep(1);
			}

		}
	}
} elseif( $_GET["feed"]=="land-aantal-personen" ) {

	//
	// feed land-aantal personen
	//

	$doorloop_array=array(
		"fap=2"=>"2",
		"fap=3"=>"3",
		"fap=4"=>"4",
		"fap=5"=>"5",
		"fap=6"=>"6",
		"fap=7"=>"7",
		"fap=8"=>"8",
		"fap=9"=>"9",
		"fap=10"=>"10",
		"fap=11"=>"11",
		"fap=12"=>"12",
		"fap=13"=>"13",
		"fap=14"=>"14",
		"fap=15"=>"15",
		"fap=16"=>"16",
		"fap=17"=>"17",
		"fap=18"=>"18",
		"fap=19"=>"19",
		"fap=20"=>"20"
	);

	echo "Land".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."URL land + aantal personen".wt_csvconvert_delimiter."Aantal accommodaties land + aantal personen\n";

	$db4->query("SELECT DISTINCT land_id, land FROM view_accommodatie WHERE websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 ORDER BY land;");
	$result_teller=0;
	while($db4->next_record()) {

		$result_teller++;

		reset($doorloop_array);
		foreach ($doorloop_array as $key99 => $value99) {

			$aantal_resultaten_land=0;

			$temp_land_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&".$key99."&fsg=".$db4->f("land_id")."-0";

			if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
				$content="aaa data-aantalgevonden=\"88\" aa";
			} else {
				$content=file_get_contents($temp_land_url);
			}

			if(preg_match("/data-aantalgevonden=\"([0-9]+)\"/",$content,$regs)) {
				if($regs[1]>0) {
					$aantal_resultaten_land=$regs[1];
				}
			}

			if($aantal_resultaten_land>=$vars["min_aantal_resultaten_traffic4u"]) {
				echo wt_csvconvert(utf8_encode($db4->f("land"))).wt_csvconvert_delimiter;
				echo wt_csvconvert(utf8_encode($value99)).wt_csvconvert_delimiter;;
				echo wt_csvconvert(utf8_encode($temp_land_url)).wt_csvconvert_delimiter;;
				echo wt_csvconvert(utf8_encode($aantal_resultaten_land));
				echo "\n";
			}
		}
	}
} elseif( $_GET["feed"]=="aantal-personen" ) {

	//
	// feed land-aantal personen
	//

	$doorloop_array=array(
		"fap=2"=>"2",
		"fap=3"=>"3",
		"fap=4"=>"4",
		"fap=5"=>"5",
		"fap=6"=>"6",
		"fap=7"=>"7",
		"fap=8"=>"8",
		"fap=9"=>"9",
		"fap=10"=>"10",
		"fap=11"=>"11",
		"fap=12"=>"12",
		"fap=13"=>"13",
		"fap=14"=>"14",
		"fap=15"=>"15",
		"fap=16"=>"16",
		"fap=17"=>"17",
		"fap=18"=>"18",
		"fap=19"=>"19",
		"fap=20"=>"20",
		"fap=21"=>"21",
		"fap=22"=>"22",
		"fap=23"=>"23",
		"fap=24"=>"24",
		"fap=25"=>"25",
		"fap=26"=>"26",
		"fap=27"=>"27",
		"fap=28"=>"28",
		"fap=29"=>"29",
		"fap=30"=>"30",
		"fap=31"=>"31",
		"fap=32"=>"32",
		"fap=33"=>"33",
		"fap=34"=>"34",
		"fap=35"=>"35",
		"fap=36"=>"36",
		"fap=37"=>"37",
		"fap=38"=>"38",
		"fap=39"=>"39",
		"fap=40"=>"40"
	);

	echo "Aantal personen".wt_csvconvert_delimiter."URL aantal personen".wt_csvconvert_delimiter."Aantal accommodaties aantal personen\n";

	reset($doorloop_array);
	foreach ($doorloop_array as $key99 => $value99) {

		$aantal_resultaten=0;

		$temp_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&".$key99;

		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$content="aaa data-aantalgevonden=\"88\" aa";
		} else {
			$content=file_get_contents($temp_url);
		}

		if(preg_match("/data-aantalgevonden=\"([0-9]+)\"/",$content,$regs)) {
			if($regs[1]>0) {
				$aantal_resultaten=$regs[1];
			}
		}

		if($aantal_resultaten>=$vars["min_aantal_resultaten_traffic4u"]) {
			echo wt_csvconvert(utf8_encode($value99)).wt_csvconvert_delimiter;;
			echo wt_csvconvert(utf8_encode($temp_url)).wt_csvconvert_delimiter;;
			echo wt_csvconvert(utf8_encode($aantal_resultaten));
			echo "\n";
		}
	}
}


?>