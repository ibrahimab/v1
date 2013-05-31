<?php

#
#
# CSV-export van alle accommodaties t.b.v. Traffic4U
#
# ========================>>>>>>>>>>>>>>>> LET OP: bij deze file wordt een cache gebruikt! <<<<<<<<<<<<<<<<==================================
#
# Elke dag om 04:00 uur en 18:00 uur wordt de nieuwe cache aangemaakt (via cron/elkuur.php)
#
# handmatig starten aanmaken cache: /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/elkuur.php xmlopnieuw
#
#
#

# TODO: nagaan of cache opzetten in cron/elkuur.php nodig is
# CACHE NOG NIET ACTIE

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");
$cachefile=$unixdir."cache/feed_traffic4u_".basename($_GET["feed"])."_".$vars["website"].".csv";

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	header("Content-Type: text/plain; charset=utf-8");

	# UTF-8 BOM
	echo "\xEF\xBB\xBF";
} elseif(file_exists($cachefile) and filemtime($cachefile)>=(time()-129600) and !$_GET["nocache"]) {
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

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	define(wt_csvconvert_delimiter,",");
} else {
	define(wt_csvconvert_delimiter,";");
}

# header
if($_GET["feed"]=="accommodaties") {
#	echo "Land".wt_csvconvert_delimiter."Gebied".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Accommodatienaam".wt_csvconvert_delimiter."Soort accommodatie".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."Accommodatie-URL".wt_csvconvert_delimiter."Vanafprijs per persoon".wt_csvconvert_delimiter."Afstand tot de piste".wt_csvconvert_delimiter."Afstand tot restaurant".wt_csvconvert_delimiter."Afstand tot winkels".wt_csvconvert_delimiter."Klassering\n";
	echo "Land".wt_csvconvert_delimiter."Gebied".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Soort accommodatie".wt_csvconvert_delimiter."Accommodatienaam".wt_csvconvert_delimiter."Aantal personen".wt_csvconvert_delimiter."Accommodatie-URL".wt_csvconvert_delimiter."Afstand tot de piste".wt_csvconvert_delimiter."Afstand tot restaurant".wt_csvconvert_delimiter."Afstand tot winkels".wt_csvconvert_delimiter."Klassering\n";
	$db->query("SELECT DISTINCT t.type_id, a.accommodatie_id, a.toonper, a.naam, a.kenmerken AS akenmerken, t.naam AS tnaam, a.zoekvolgorde AS azoekvolgorde, a.omschrijving, a.kwaliteit, a.gps_lat, a.gps_long, a.afstandwinkel, a.afstandwinkelextra, a.afstandrestaurant, a.afstandrestaurantextra, a.afstandpiste, a.afstandpisteextra, a.afstandskilift, a.afstandskiliftextra, a.afstandloipe, a.afstandloipeextra, a.afstandskibushalte, a.afstandskibushalteextra, a.afstandstrand, a.afstandstrandextra, a.afstandzwembad, a.afstandzwembadextra, a.afstandzwemwater, a.afstandzwemwaterextra, a.afstandgolfbaan, a.afstandgolfbaanextra, t.kwaliteit AS tkwaliteit, t.omschrijving AS tomschrijving, t.zoekvolgorde AS tzoekvolgorde, lv.zoekvolgorde AS lzoekvolgorde, t.optimaalaantalpersonen, t.maxaantalpersonen, a.soortaccommodatie, t.slaapkamers, t.badkamers, t.kenmerken AS tkenmerken, s.skigebied_id, s.naam AS skigebied, l.naam AS land, l.begincode, p.naam AS plaats, p.plaats_id FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".$vars["website"]."%' AND a.tonen=1 AND a.archief=0 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0".($aanbieding_inquery ? " AND t.type_id IN (".substr($aanbieding_inquery,1).")" : "")." ORDER BY t.type_id".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? " LIMIT 0,10" : "").";");
	while($db->next_record()) {
		for($i=$db->f("optimaalaantalpersonen");$i<=$db->f("maxaantalpersonen");$i++) {
			echo wt_csvconvert(utf8_encode($db->f("land"))).wt_csvconvert_delimiter;
			echo wt_csvconvert(utf8_encode($db->f("skigebied"))).wt_csvconvert_delimiter;
			echo wt_csvconvert(utf8_encode($db->f("plaats"))).wt_csvconvert_delimiter;

			echo wt_csvconvert(utf8_encode(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")]))).wt_csvconvert_delimiter;

			$accnaam=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "");
			echo wt_csvconvert(utf8_encode($accnaam)).wt_csvconvert_delimiter;

			echo wt_csvconvert($i).wt_csvconvert_delimiter;
			echo wt_csvconvert(utf8_encode($vars["basehref"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/")).wt_csvconvert_delimiter;

			if($db->f("afstandpiste")) {
				echo wt_csvconvert(utf8_encode(toonafstand($db->f("afstandpiste"),$db->f("afstandpisteextra"),txt("meter","toonaccommodatie")))).wt_csvconvert_delimiter;
			} else {
				echo wt_csvconvert_delimiter;
			}

			if($db->f("afstandrestaurant")) {
				echo wt_csvconvert(utf8_encode(toonafstand($db->f("afstandrestaurant"),$db->f("afstandrestaurantextra"),txt("meter","toonaccommodatie")))).wt_csvconvert_delimiter;
			} else {
				echo wt_csvconvert_delimiter;
			}

			if($db->f("afstandwinkel")) {
				echo wt_csvconvert(utf8_encode(toonafstand($db->f("afstandwinkel"),$db->f("afstandwinkelextra"),txt("meter","toonaccommodatie")))).wt_csvconvert_delimiter;
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
} elseif($_GET["feed"]=="bestemmingen") {

	$thema_array=array(
		"vf_piste1=1"=>"aan de piste",
		"vf_kenm2=1"=>"catering mogelijk",
		"vf_kenm4=1"=>"sauna",
		"vf_kenm43=1"=>"goed voor kids",
		"vf_kenm6=1"=>"huisdieren toegestaan",
	);

	echo "Land".wt_csvconvert_delimiter."Skigebied".wt_csvconvert_delimiter."Plaats".wt_csvconvert_delimiter."Thema".wt_csvconvert_delimiter."URL skigebied + thema".wt_csvconvert_delimiter."URL plaats + thema\n";
#	$db4->query("SELECT DISTINCT land_id, land, skigebied, skigebied_id, plaats_id, plaats FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND archief=0 AND plaats_id IN (28,30,120);");
	$db4->query("SELECT DISTINCT land_id, land, skigebied, skigebied_id, plaats_id, plaats FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND archief=0 AND plaats_id IN (28,6);");
	while($db4->next_record()) {

		foreach ($thema_array as $key99 => $value99) {


			# skigebied-url
			if(!$skigebied_al_gehad[$db4->f("skigebied_id").$key99]) {

				$skigebied_al_gehad[$db4->f("skigebied_id").$key99]=true;

				unset($aantal_resultaten_skigebied);

				$temp_skigebied_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&".$key99."&fsg=".$db4->f("land_id")."-".$db4->f("skigebied_id");
				$content=file_get_contents($temp_skigebied_url);

				if(preg_match("/data-aantalgevonden=\"([0-9]+)\"/",$content,$regs)) {
					if($regs[1]>0) {
						$aantal_resultaten_skigebied=$regs[1];
					}
				}
			}


			# plaats-url
			unset($aantal_resultaten_plaats);
			$temp_plaats_url=$vars["basehref"].txt("menu_zoek-en-boek").".php?filled=1&".$key99."&fsg=pl".$db4->f("plaats_id");
			$content=file_get_contents($temp_plaats_url);

			if(preg_match("/data-aantalgevonden=\"([0-9]+)\"/",$content,$regs)) {
				if($regs[1]>0) {
					$aantal_resultaten_plaats=$regs[1];
				}
			}

			if($aantal_resultaten_skigebied>0 or $aantal_resultaten_plaats>0) {
				echo wt_csvconvert(utf8_encode($db4->f("land"))).wt_csvconvert_delimiter;
				echo wt_csvconvert(utf8_encode($db4->f("skigebied"))).wt_csvconvert_delimiter;
				echo wt_csvconvert(utf8_encode($db4->f("plaats"))).wt_csvconvert_delimiter;
				echo wt_csvconvert(utf8_encode($value99)).wt_csvconvert_delimiter;
				if($aantal_resultaten_skigebied>0) {
					echo wt_csvconvert(utf8_encode($temp_skigebied_url)).wt_csvconvert_delimiter;
				} else {
#					echo wt_csvconvert(utf8_encode($temp_skigebied_url)).wt_csvconvert_delimiter;
					echo wt_csvconvert_delimiter;
				}
				if($aantal_resultaten_plaats>0) {
					echo wt_csvconvert(utf8_encode($temp_plaats_url));
				}

				if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
					echo wt_csvconvert_delimiter.intval($aantal_resultaten_skigebied).wt_csvconvert_delimiter.intval($aantal_resultaten_plaats);
				}

				echo "\n";

			}

			sleep(1);

		}
	}
}



?>