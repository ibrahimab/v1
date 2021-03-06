<?php

#
#
# Dit script wordt op elk heel uur gerund op de server web01.chalet.nl met het account chalet01.
#
# /usr/bin/php /var/www/chalet.nl/html/cron/elkuur.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

} else {
	sleep(10);
}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	echo "<pre>";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/elkuur.php") {
	$unixdir="/home/webtastic/html/chalet/";
} elseif (defined('wt_test') && wt_test === true) {
	$unixdir = dirname(dirname(__FILE__)) . '/';
} else {
	$unixdir="/var/www/chalet.nl/html/";
#	mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkuur","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

$huidig_uur = date("H");

#echo date("d-m-Y H:i")."u.<pre>\n\nChalet.nl elk uur\n\n\n";
#flush();

#wt_mail("jeroen@webtastic.nl","elk uur","mail elk uur");

// aanbetaling1 vastzetten na 15 dagen
if($huidig_uur==0 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test12") {
	$vijftien_dagen_geleden = date("Y-m-d", mktime(0,0,0,date("m"), date("d")-15, date("Y")));
	$db->query("UPDATE boeking SET aanbetaling1_vastgezet=1 WHERE aanbetaling1_vastgezet=0 AND factuurdatum_eerste_factuur IS NOT NULL AND factuurdatum_eerste_factuur<='".$vijftien_dagen_geleden."';");
}

#
# Automatische kortingen (vanwege Zwitserse Franken CHF) doorrekenen
#
if($huidig_uur==0 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test2") {
	$db->query("SELECT seizoen_id, zwitersefranken_kortingspercentage, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen;");
	while($db->next_record()) {
		$seizoen_zwitersefranken_kortingspercentage[$db->f("seizoen_id")]=$db->f("zwitersefranken_kortingspercentage");
		$seizoen_begin[$db->f("seizoen_id")]=$db->f("begin");
		$seizoen_eind[$db->f("seizoen_id")]=$db->f("eind");
	}
	$db->query("UPDATE korting SET delete_after_automatische_korting=1 WHERE automatische_korting=1;");
	$db->query("SELECT DISTINCT ta.seizoen_id, ta.type_id FROM tarief ta, type t WHERE ta.type_id=t.type_id AND t.leverancier_id IN (SELECT leverancier_id FROM leverancier WHERE zwitersefranken=1) AND ta.week>'".time()."';");
	while($db->next_record()) {
		$db2->query("SELECT korting_id FROM korting WHERE automatische_korting=1 AND type_id='".addslashes($db->f("type_id"))."' AND seizoen_id='".addslashes($db->f("seizoen_id"))."';");
		if($db2->next_record()) {
			$kortingid=$db2->f("korting_id");
			$db3->query("UPDATE korting SET delete_after_automatische_korting=0, editdatetime=NOW() WHERE automatische_korting=1 AND korting_id='".addslashes($db2->f("korting_id"))."';");
		} else {
			$db2->query("INSERT INTO korting SET actief=1, type_id='".addslashes($db->f("type_id"))."', seizoen_id='".addslashes($db->f("seizoen_id"))."', naam='Automatisch: Zwitserse Franken', van=FROM_UNIXTIME(".mktime(0,0,0,date("m"),date("d")-1,date("Y"))."), tot=FROM_UNIXTIME(".$seizoen_eind[$db->f("seizoen_id")]."), toonexactekorting=0, aanbiedingskleur=0, toon_abpagina=0, automatische_korting=1, adddatetime=NOW(), editdatetime=NOW();");
			$kortingid=$db2->insert_id();
		}
		if($kortingid) {
			$db2->query("UPDATE korting_tarief SET inkoopkorting_percentage='".$seizoen_zwitersefranken_kortingspercentage[$db->f("seizoen_id")]."', aanbieding_acc_percentage='".$seizoen_zwitersefranken_kortingspercentage[$db->f("seizoen_id")]."' WHERE korting_id='".addslashes($kortingid)."';");
			$week=$seizoen_begin[$db->f("seizoen_id")];
			while($week<=$seizoen_eind[$db->f("seizoen_id")]) {
				$db2->query("INSERT INTO korting_tarief SET korting_id='".addslashes($kortingid)."', week='".$week."', inkoopkorting_percentage='".$seizoen_zwitersefranken_kortingspercentage[$db->f("seizoen_id")]."', aanbieding_acc_percentage='".$seizoen_zwitersefranken_kortingspercentage[$db->f("seizoen_id")]."', opgeslagen=NOW();");
				$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
			}

		}
	}
	# automatische kortingen die niet meer actief zijn wissen
	$db->query("DELETE FROM korting_tarief WHERE korting_id IN (SELECT korting_id FROM korting WHERE delete_after_automatische_korting=1 AND automatische_korting=1);");
	$db->query("DELETE FROM korting WHERE delete_after_automatische_korting=1 AND automatische_korting=1;");
	$db->query("UPDATE korting SET delete_after_automatische_korting=0;");

	unset($seizoen_zwitersefranken_kortingspercentage,$seizoen_begin,$seizoen_eind,$week,$kortingid);
}

#
# Alle kortingen/aanbiedingen doorrekenen
#
if($huidig_uur==0 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test2") {
	// $db->query("UPDATE cache_vanafprijs_type SET wis=1 WHERE 1=1;");
	include($unixdir."cron/tarieven_berekenen.php");
}

#
# Calculate vanaf-prijzen
#
if($huidig_uur==0 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test2") {
	$voorraad_gekoppeld=new voorraad_gekoppeld;
	$db->query("SELECT type_id FROM type WHERE 1=1 ORDER BY type_id;");
	while($db->next_record()) {
		$voorraad_gekoppeld->vanaf_prijzen_berekenen($db->f("type_id"));
	}
	$voorraad_gekoppeld->koppeling_uitvoeren_na_einde_script();

	// use new vanafprijs-class
	$vanafprijs = new vanafprijs;
	$vanafprijs->set_all_types_to_calculate();

}

#
# Dagelijks CSV-export van alle financiele cijfers maken (wordt opgeslagen in de map csv/)
#
if($huidig_uur==0 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test4") {
	$a=file_get_contents("https://www.chalet.nl/cms_financien.php?marges=1&csv=1&bedrijf=chalet");
	$a=file_get_contents("https://www.chalet.nl/cms_financien.php?marges=1&csv=1&bedrijf=venturasol");
	unset($a);
}

//
// tonen_in_mijn_boeking uitzetten 10 dagen na aankomst (indien betalingen zijn voldaan)
//
$db->query("SELECT boeking_id, vertrekdatum_exact FROM boeking WHERE bevestigdatum IS NOT NULL AND vertrekdatum_exact<".(time()-(86400*3))." AND tonen_in_mijn_boeking=1 ORDER BY boeking_id;");
while($db->next_record()) {

	$gegevens=get_boekinginfo($db->f("boeking_id"));

	$booking_payment = new booking_payment($gegevens);
	$booking_payment->get_amounts();

	if($booking_payment->amount["eindbetaling"]>0) {
		// nog openstaand bedrag
	} else {
		// voldaan: boeking mag "dicht"
		$db2->query("UPDATE boeking SET tonen_in_mijn_boeking=0 WHERE boeking_id='".intval($db->f("boeking_id"))."';");
	}
}
unset($gegevens);

#
# Uitnodigingen voor de enquete versturen (niet aan wederverkoop-boekingen)
#
if(($huidig_uur>12 and $huidig_uur<20 and date("w")==4) or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test2") {
	unset($teller);
	$db->query("SELECT b.boeking_id, b.boekingsnummer, b.naam_accommodatie, b.aankomstdatum_exact, b.vertrekdatum_exact, b.goedgekeurd FROM boeking b WHERE b.goedgekeurd=1 AND b.geannuleerd=0 AND b.stap_voltooid=5 AND b.vertrekdatum_exact<'".(time()-172800)."' AND b.vertrekdatum_exact>'".(time()-1209600)."' AND b.mailblokkeren_enquete=0 AND b.mailverstuurd_enquete IS NULL AND reisbureau_user_id IS NULL ORDER BY b.aankomstdatum_exact, b.naam_accommodatie;");
	if($db->num_rows()) {
		while($db->next_record()) {
			$teller++;
			if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and $teller>1) {
				continue;
			}
			$gegevens=get_boekinginfo($db->f("boeking_id"));


			if($vars["fotofabriek_code_na_enquete"]) {
				$mailbody=txt("mail_body_kortingscode","enquete");
			} else {
				$mailbody=txt("mail_body","enquete");
			}

			$link=$gegevens["stap1"]["website_specifiek"]["basehref"]."enquete.php?bid=".$gegevens["stap1"]["boekingid"]."&ch=".substr(sha1($gegevens["stap1"]["boekingid"]."kkSLlejkd"),0,8);
			$mailbody=ereg_replace("\[LINK_ENQUETE\]",$link,$mailbody);
#			$mailbody=ereg_replace("\[WEBSITE\]",$gegevens["stap1"]["website_specifiek"]["websitenaam"],$mailbody);
			$mailbody=ereg_replace("\[NAAM\]",trim($gegevens["stap2"]["voornaam"]),$mailbody);
			$mailbody=ereg_replace("\[ACHTERNAAM\]",wt_naam("", $gegevens["stap2"]["tussenvoegsel"], $gegevens["stap2"]["achternaam"]),$mailbody);
			$mailbody=ereg_replace("\[ACCOMMODATIENAAM\]",trim($gegevens["stap1"]["accinfo"]["accnaam"]),$mailbody);
			$mailbody=ereg_replace("\[LINK_FOTOFABRIEK\]","http://www.fotofabriek.nl/chalet-actie/",$mailbody);

			$subject=txt("mail_subject","enquete");

			if(!preg_match("/MISSING/",$subject)) {

				$settings=array("convert_to_html"=>true);

				# Mail versturen (met opmaak)
				verstuur_opmaakmail($gegevens["stap1"]["website"],$gegevens["stap2"]["email"],wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]),$subject,$mailbody,$settings);

				echo "enquete-uitnodiging verstuurd aan ".$gegevens["stap2"]["email"]." ".date("d-m-Y",$gegevens["stap1"]["aankomstdatum_exact"])." - ".date("d-m-Y",$gegevens["stap1"]["vertrekdatum_exact"]).": ".$gegevens["stap1"]["boekingsnummer"]."\n\n";
				flush();

				$db2->query("UPDATE boeking SET mailverstuurd_enquete=NOW() WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				chalet_log("enqu�te-verzoek verstuurd aan ".$gegevens["stap2"]["email"],false,true);
			}
		}
	}
}

#
# Hoogseizoen-controle
#
$doorloop_array=array(1=>"bruto",3=>"c_bruto");
$db->query("SELECT s.seizoen_id, sw.week, sw.hoogseizoen FROM seizoen s, seizoen_week sw WHERE sw.seizoen_id=s.seizoen_id AND UNIX_TIMESTAMP(s.eind)>'".(time())."' ORDER BY s.seizoen_id, sw.week");
while($db->next_record()) {
	$hoogseizoen[$db->f("seizoen_id")][$db->f("week")]=$db->f("hoogseizoen");
}

while(list($key,$value)=each($hoogseizoen)) {
	unset($inquery_buitenhoogseizoen,$inquery_hoogseizoen);
	while(list($key2,$value2)=each($value)) {
		if(!$value2) {
			$inquery_buitenhoogseizoen.=",".$key2;
		}
		if($value2==1) {
			$inquery_hoogseizoen.=",".$key2;
		}
	}
	if(!$inquery_buitenhoogseizoen) $inquery_buitenhoogseizoen=",0";
	if(!$inquery_hoogseizoen) $inquery_hoogseizoen=",0";

	reset($doorloop_array);
	while(list($toonper,$dbfield)=each($doorloop_array)) {

		unset($laagseizoen);
		# hoogste prijs buiten hoogseizoen bepalen
		$db2->query("SELECT t.type_id, MAX(ta.".$dbfield.") AS maximum FROM tarief ta, type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND ta.".$dbfield.">0 AND ta.seizoen_id='".$key."' AND ta.type_id=t.type_id AND a.toonper='".$toonper."' AND ta.week IN (".substr($inquery_buitenhoogseizoen,1).") GROUP BY type_id;");
		while($db2->next_record()) {
			$laagseizoen[$db2->f("type_id")]=$db2->f("maximum");
		}

		# laagste hoogseizoen-prijs opvragen (en kijken of deze hoger is dan max-bedrag)
		$db2->query("SELECT t.type_id, MIN(ta.".$dbfield.") AS hoogseizoentarief FROM tarief ta, type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND ta.".$dbfield.">0 AND ta.seizoen_id='".$key."' AND ta.type_id=t.type_id AND a.toonper='".$toonper."' AND ta.week IN (".substr($inquery_hoogseizoen,1).") AND ta.week>'".time()."' GROUP BY type_id;");
		while($db2->next_record()) {
			if($db2->f("hoogseizoentarief")<$laagseizoen[$db2->f("type_id")]) {
#				echo "Te laag bedrag typeid ".$db2->f("type_id")." seizoen ".$key."\n";
				$db3->query("UPDATE type_seizoen SET hoogseizoen_onjuiste_tarieven=1 WHERE type_id='".$db2->f("type_id")."' AND seizoen_id='".$key."';");
			} else {
				$db3->query("UPDATE type_seizoen SET hoogseizoen_onjuiste_tarieven=0 WHERE type_id='".$db2->f("type_id")."' AND seizoen_id='".$key."';");
			}
		}
	}
}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" or $argv[1]=="test") {
	exit;
}

#
# Aanbiedingen die zijn afgelopen op tonen=0 zetten
#
$db->query("UPDATE aanbieding SET tonen=0 WHERE tonen=1 AND einddatum IS NOT NULL AND UNIX_TIMESTAMP(einddatum)<'".mktime(0,0,0,date("m"),date("d"),date("Y"))."';");


#
# Controleren op XML-imports: al 24 uur geen import
#
if($huidig_uur==0 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2") {

	$db->query("SELECT leverancier_id, naam, UNIX_TIMESTAMP(xml_laatsteimport) AS xml_laatsteimport FROM leverancier WHERE xml_type>0 ORDER BY naam;");
	// check one-time-per-day xml-imports: last 24hours no import
 	$dailyImportSuppliers = [23];
 	$gisteren = mktime((in_array($db->f('xml_type'), $dailyImportSuppliers) ? 0 : 12), 0, 0, date('m'), date('d') - 1, date('Y'));
	while($db->next_record()) {
		if(date("Ymd",$db->f("xml_laatsteimport"))<date("Ymd",$gisteren)) {
			$leverancier_geen_import.="<li><a href=\"https://www.chalet.nl/cms_leveranciers.php?show=8&beheerder=0&8k0=".$db->f("leverancier_id")."\" target=\"_blank\">".wt_he($db->f("naam"))."</a></li>";
		}
	}
	if($leverancier_geen_import) {

		$mail=new wt_mail;
		$mail->fromname="Chalet.nl XML-systeem";
		$mail->from="system@chalet.nl";
		$mail->toname="Chalet.nl";
		$mail->to="info@chalet.nl";
		$mail->bcc="chaletmailbackup+systemlog@gmail.com";
		$mail->subject="Geen XML-import";

		$mail->plaintext="";

		$mail->html_top="";
		$mail->html="<B>Bij de volgende leverancier(s) heeft op ".DATUM("DAG D MAAND JJJJ",$gisteren)." de hele dag geen XML-import plaatsgevonden:</B><ul>".$leverancier_geen_import."</ul>";
		$mail->html_bottom="";

		$mail->send();

	}

}

#
# Controleren op wijzigingen in XML-imports
#
if(($huidig_uur==5 and date("w")==0) or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $argv[1]=="test") {
		echo "via cms_xmlnewimport.html controleren op XML-imports<br>";
	}
	while(list($key,$value)=each($vars["xmlnewimport_leveranciers"])) {
		unset($new,$verberg_xmlcode,$xml_importvalues,$skigebied_xmlnamen,$plaats_xmlnamen,$type_xmlcode,$gewijzigd_acc,$gewijzigd_type,$verberg_xmlcode,$levinfo);
		$_GET["lev"]=$key;
		$_GET["checkforchanges"]=1;
		include($unixdir."content/cms_xmlnewimport.html");
	}
}
if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $argv[1]=="test") {
	exit;
}

#
# Volgorde van blokken hoofdpagina resetten (zodat deze weer 10, 20, 30, etc... wordt)
#
#if($huidig_uur==3 or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2") {
#	$wzt_array=array(1,2);
#	while(list($key,$value)=each($wzt_array)) {
#		$volgorde=0;
#		$db->query("SELECT blokhoofdpagina_id FROM blokhoofdpagina WHERE wzt='".$value."' ORDER BY volgorde;");
#		while($db->next_record()) {
#			$volgorde=$volgorde+10;
#			$db2->query("UPDATE blokhoofdpagina SET volgorde='".$volgorde."' WHERE blokhoofdpagina_id='".$db->f("blokhoofdpagina_id")."';");
#		}
#	}
#}


#
# Vervallen allotments verplaatsen naar voorraad_vervallen_allotment
#
$db->query("SELECT t.type_id, ta.seizoen_id, ta.week, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, l.aflopen_allotment, c.aflopen_allotment AS caflopen_allotment, ta.aflopen_allotment AS taaflopen_allotment FROM tarief ta, accommodatie a, type t, leverancier l, calculatiesjabloon_week c WHERE t.verzameltype=0 AND c.seizoen_id=ta.seizoen_id AND c.week=ta.week AND c.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND t.leverancier_id=l.leverancier_id AND ta.voorraad_allotment>0 AND (l.aflopen_allotment IS NOT NULL OR c.aflopen_allotment IS NOT NULL OR ta.aflopen_allotment IS NOT NULL) ORDER BY week DESC;");
while($db->next_record()) {
	if($db->f("taaflopen_allotment")<>"") {
		$aflopen_allotment=$db->f("taaflopen_allotment");
	} elseif($db->f("caflopen_allotment")<>"") {
		$aflopen_allotment=$db->f("caflopen_allotment");
	} elseif($db->f("aflopen_allotment")<>"") {
		$aflopen_allotment=$db->f("aflopen_allotment");
	} else {
		$aflopen_allotment=0;
	}
	$aflopen_allotment_time=mktime(0,0,0,date("m",$db->f("week")),date("d",$db->f("week"))-$aflopen_allotment,date("Y",$db->f("week")));
	if($aflopen_allotment_time<time()) {
		$teller++;
		echo $teller.". ".$db->f("voorraad_allotment")." allotment ".date("d-m-Y",$db->f("week"))." (is ".$aflopen_allotment." dagen daarvoor vervallen op ".date("d-m-Y",$aflopen_allotment_time)."): ".$db->f("type_id")."<br>\n";
#		$db2->query("UPDATE tarief SET voorraad_vervallen_allotment='".addslashes($db->f("voorraad_allotment"))."' WHERE type_id='".addslashes($db->f("type_id"))."' AND seizoen_id='".addslashes($db->f("seizoen_id"))."' AND week='".addslashes($db->f("week"))."';");
		voorraad_bijwerken($db->f("type_id"),$db->f("week"),true,"",(0-$db->f("voorraad_allotment")),$db->f("voorraad_allotment"),"","","","",false,8);
	}
}


#
# Controle op onjuiste wederverkoop-tarieven
#
#if($huidig_uur==8 or $huidig_uur==18) {
	$db->query("SELECT DISTINCT ta.type_id, ta.seizoen_id FROM tarief ta, type t, accommodatie a WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND ta.c_bruto>0 AND ta.c_verkoop_site>0 AND ta.wederverkoop_verkoopprijs>0 AND (ta.wederverkoop_verkoopprijs<(ta.c_verkoop_site-10));");
	if($db->num_rows()) {
		wt_mail("jeroen@webtastic.nl","Onjuiste wederverkoop-tarieven Chalet.nl","Er zijn onjuiste wederverkoop-tarieven aangetroffen (via cron/elkuur.php) op Chalet.nl.\n\nOpen de volgende pagina en wacht tot alles is verwerkt:\n\nhttps://www.chalet.nl/cms_tarieven_autosubmit.php?check=1&t=99&confirmed=1\n\n");
	}
#}

#
# Top10's controleren
#
# NIET MEER NODIG: Top10's zijn niet meer acief
#
#while(list($key,$value)=each($vars["websites_basehref_siteid"])) {
#	$db->query("SELECT DISTINCT t.week, s.seizoen_id, s.type FROM top10_week t, seizoen s WHERE UNIX_TIMESTAMP(s.eind)>'".time()."' AND t.seizoen_id=s.seizoen_id ORDER BY t.week;");
#	while($db->next_record()) {
#		if($db->f("type")==$vars["websites_wzt_siteid"][$key]) {
#			$url=$value."top10.php?cronseizoen=".$db->f("seizoen_id")."&d=".$db->f("week");
#			echo $url."\n";
#			$content=file_get_contents($url);
#			sleep(1);
#		}
#	}
#}

#
# Zoekstatistieken
#
# OM DE TOP'S OPNIEUW AAN TE MAKEN:
#$db->query("DELETE FROM zoekstatistiek_top;");
#$db->query("UPDATE zoekstatistiek SET intopopgeslagen=0;");

$db->query("SELECT zoekstatistiek_id, wzt, zoekopdracht, tekstzoekopdracht, aantalresultaten, UNIX_TIMESTAMP(datumtijd) AS datumtijd FROM zoekstatistiek WHERE compleet=1 AND intopopgeslagen=0 AND UNIX_TIMESTAMP(datumtijd)<'".(time()-3600)."';");
while($db->next_record()) {
	$maand=date("Ym");

	# Gewone zoekopdracht
	if($db->f("zoekopdracht")) {
		$db2->query("INSERT INTO zoekstatistiek_top SET type=1, wzt='".$db->f("wzt")."', maand='".$maand."', zoekopdracht='".addslashes($db->f("zoekopdracht"))."', aantal=1;");
		if($db2->Errno==1062) {
			$db2->query("UPDATE zoekstatistiek_top SET aantal=aantal+1 WHERE type=1 AND wzt='".$db->f("wzt")."' AND maand='".$maand."' AND zoekopdracht='".addslashes($db->f("zoekopdracht"))."';");
		}
	}

	# Tekstzoekopdracht
	if($db->f("tekstzoekopdracht")) {
		$db2->query("INSERT INTO zoekstatistiek_top SET type=2, wzt='".$db->f("wzt")."', maand='".$maand."', zoekopdracht='".addslashes($db->f("tekstzoekopdracht"))."', aantal=1;");
		if($db2->Errno==1062) {
			$db2->query("UPDATE zoekstatistiek_top SET aantal=aantal+1 WHERE type=2 AND wzt='".$db->f("wzt")."' AND maand='".$maand."' AND zoekopdracht='".addslashes($db->f("tekstzoekopdracht"))."';");
		}
	}

	# Zonder resultaten
	if(($db->f("zoekopdracht") or $db->f("tekstzoekopdracht")) and $db->f("aantalresultaten")==0) {
		$zoekopdracht=$db->f("zoekopdracht");
		if($db->f("tekstzoekopdracht")) {
			if($zoekopdracht) $zoekopdracht.=" - ";
			$zoekopdracht.="tekst: ".$db->f("tekstzoekopdracht");
		}
		$db2->query("INSERT INTO zoekstatistiek_top SET type=3, wzt='".$db->f("wzt")."', maand='".$maand."', zoekopdracht='".addslashes($zoekopdracht)."', aantal=1;");
		if($db2->Errno==1062) {
			$db2->query("UPDATE zoekstatistiek_top SET aantal=aantal+1 WHERE type=3 AND wzt='".$db->f("wzt")."' AND maand='".$maand."' AND zoekopdracht='".addslashes($zoekopdracht)."';");
		}
	}

	# Geen doorklik
	if(($db->f("zoekopdracht") or $db->f("tekstzoekopdracht")) and $db->f("aantalresultaten")>0) {
		$db2->query("SELECT zoekstatistiek_id FROM zoekstatistiek_doorklik WHERE zoekstatistiek_id='".$db->f("zoekstatistiek_id")."';");
		if(!$db2->num_rows()) {
			$zoekopdracht=$db->f("zoekopdracht");
			if($db->f("tekstzoekopdracht")) {
				if($zoekopdracht) $zoekopdracht.=" - ";
				$zoekopdracht.="tekst: ".$db->f("tekstzoekopdracht");
			}
			$db2->query("INSERT INTO zoekstatistiek_top SET type=4, wzt='".$db->f("wzt")."', maand='".$maand."', zoekopdracht='".addslashes($zoekopdracht)."', aantal=1;");
			if($db2->Errno==1062) {
				$db2->query("UPDATE zoekstatistiek_top SET aantal=aantal+1 WHERE type=4 AND wzt='".$db->f("wzt")."' AND maand='".$maand."' AND zoekopdracht='".addslashes($zoekopdracht)."';");
			}
		}
	}

	$db2->query("UPDATE zoekstatistiek SET intopopgeslagen=1 WHERE zoekstatistiek_id='".$db->f("zoekstatistiek_id")."';");

}

#
# Tabellen bezoek en bezoeker opschonen
#
if($huidig_uur==3) {

	# Bepalen welke records in "bezoek" gekoppeld zijn aan een boeking
	$db->query("SELECT bezoeker_id FROM boeking WHERE bezoeker_id<>'';");
	while($db->next_record()) {
		$db2->query("UPDATE bezoek SET gekoppeld_aan_boeking=1 WHERE bezoeker_id='".$db->f("bezoeker_id")."';");
	}

	# Bezoeken van meer dan 12 maanden oud die niet zijn gekoppeld aan een boeking wissen
	$db->query("DELETE FROM bezoek WHERE UNIX_TIMESTAMP(datumtijd)<'".mktime(0,0,0,date("m")-12,date("d"),date("Y"))."' AND gekoppeld_aan_boeking=0;");

	# Bezoeken zonder ad of referer wissen
	$db->query("DELETE FROM bezoek WHERE UNIX_TIMESTAMP(datumtijd)<'".(time()-86400)."' AND ad=0 AND referer='';");

	# Browser-info wissen na 1 dag
	$db->query("UPDATE bezoek SET browser='' WHERE UNIX_TIMESTAMP(datumtijd)<'".(time()-86400)."';");

	# Tabel bezoeker zonder gegevens bijwerken (na 1 dag wissen)
	$db->query("DELETE FROM bezoeker WHERE achternaam IS NULL AND last_acc IS NULL AND UNIX_TIMESTAMP(gewijzigd)<'".(time()-86400)."';");

	# Tabel bezoeker met alleen last_acc bijwerken (na 12 maanden wissen)
	$db->query("DELETE FROM bezoeker WHERE achternaam IS NULL AND UNIX_TIMESTAMP(gewijzigd)<'".mktime(0,0,0,date("m")-12,date("d"),date("Y"))."';");

	$db->query("OPTIMIZE TABLE bezoek;");
	$db->query("OPTIMIZE TABLE bezoeker;");
}

#
# Cache Tradetracker
#
#
# handmatig starten: /usr/bin/php /var/www/chalet.nl/html/cron/elkuur.php xmlopnieuw
#
if($huidig_uur==4 or $huidig_uur==18 or $argv[1]=="xmlopnieuw") {
	if($argv[1]=="xmlopnieuw") {
		echo "xmlopnieuw:\n\n";
	}

	// new TradeTracker-feeds
	$doorloop_array=array(
		"https://www.chalet.nl/xml/tradetracker-new.php?save_cache=1",
		"https://www.chalet.nl/xml/tradetracker-new.php?aanbiedingen=1&save_cache=1",
		"https://www.chalet.be/xml/tradetracker-new.php?save_cache=1",
		"https://www.chalet.be/xml/tradetracker-new.php?aanbiedingen=1&save_cache=1",
		"https://www.chaletonline.de/xml/tradetracker-new.php?save_cache=1",
		"https://www.chaletonline.de/xml/tradetracker-new.php?aanbiedingen=1&save_cache=1",
		"https://www.zomerhuisje.nl/xml/tradetracker-new.php?save_cache=1",
		"https://www.zomerhuisje.nl/xml/tradetracker-new.php?aanbiedingen=1&save_cache=1",
		"https://www.italissima.nl/xml/tradetracker-new.php?save_cache=1",
		"https://www.italissima.nl/xml/tradetracker-new.php?aanbiedingen=1&save_cache=1",
		"https://www.italissima.be/xml/tradetracker-new.php?save_cache=1",
		"https://www.italissima.be/xml/tradetracker-new.php?aanbiedingen=1&save_cache=1"
	);
	foreach ($doorloop_array as $key => $value) {
		if($argv[1]=="xmlopnieuw") {
			echo $value."\n";
		}
		$feed=file_get_contents($value);
	}

	// old TradeTracker-feeds
	$doorloop_array=array(
		"tradetracker_C"=>"https://www.chalet.nl/xml/tradetracker.php?nocache=1",
		"tradetracker_B"=>"https://www.chalet.be/xml/tradetracker.php?nocache=1",
		"tradetracker_Z"=>"https://www.zomerhuisje.nl/xml/tradetracker.php?nocache=1",
		"tradetracker_I"=>"https://www.italissima.nl/xml/tradetracker.php?nocache=1",
		"tradetracker_K"=>"https://www.italissima.be/xml/tradetracker.php?nocache=1",
		"tradetracker_aanbiedingen_C"=>"https://www.chalet.nl/xml/tradetracker.php?aanbiedingen=1&nocache=1",
		"tradetracker_aanbiedingen_B"=>"https://www.chalet.be/xml/tradetracker.php?aanbiedingen=1&nocache=1",
		"tradetracker_aanbiedingen_Z"=>"https://www.zomerhuisje.nl/xml/tradetracker.php?aanbiedingen=1&nocache=1",
		"tradetracker_aanbiedingen_I"=>"https://www.italissima.nl/xml/tradetracker.php?aanbiedingen=1&nocache=1",
		"tradetracker_aanbiedingen_K"=>"https://www.italissima.be/xml/tradetracker.php?aanbiedingen=1&nocache=1"
	);
	while(list($key,$value)=each($doorloop_array)) {
		if($argv[1]=="xmlopnieuw") {
			echo $key."\n";
		}
		$feed=file_get_contents($value);
		$filename=$unixdir."cache/feed_".$key.".xml";
		save_data_to_file($filename,$feed);

		unset($filename,$feed);
	}
}

# Cache Traffic4U (2 uur 's nachts elke maandag + donderdag)
# handmatig starten: /usr/bin/php /var/www/chalet.nl/html/cron/elkuur.php traffic4u
if(($huidig_uur==2 and (date("w")==1 or date("w")==4)) or $argv[1]=="traffic4u") {

	if (date("w")==1) {

		// Monday: Chalet.nl and Italissima.nl
		$doorloop_array=array(
			"feed_traffic4u_bestemmingen_C"=>"https://www.chalet.nl/xml/traffic4u.php?feed=bestemmingen&nocache=1",
			"feed_traffic4u_bestemmingen-aantal-personen_C"=>"https://www.chalet.nl/xml/traffic4u.php?feed=bestemmingen-aantal-personen&nocache=1",
			"feed_traffic4u_land-aantal-personen_C"=>"https://www.chalet.nl/xml/traffic4u.php?feed=land-aantal-personen&nocache=1",
			"feed_traffic4u_aantal-personen_C"=>"https://www.chalet.nl/xml/traffic4u.php?feed=aantal-personen&nocache=1",

			"feed_traffic4u_bestemmingen_I"=>"https://www.italissima.nl/xml/traffic4u.php?feed=bestemmingen&nocache=1",
			"feed_traffic4u_bestemmingen-aantal-personen_I"=>"https://www.italissima.nl/xml/traffic4u.php?feed=bestemmingen-aantal-personen&nocache=1",
		);
	} elseif (date("w")==4) {

		// Thursday: Chaletonline.de
		$doorloop_array=array(
			"feed_traffic4u_bestemmingen_D"=>"https://www.chaletonline.de/xml/traffic4u.php?feed=bestemmingen&nocache=1",
			"feed_traffic4u_bestemmingen-aantal-personen_D"=>"https://www.chaletonline.de/xml/traffic4u.php?feed=bestemmingen-aantal-personen&nocache=1",
			"feed_traffic4u_land-aantal-personen_D"=>"https://www.chaletonline.de/xml/traffic4u.php?feed=land-aantal-personen&nocache=1",
			"feed_traffic4u_aantal-personen_D"=>"https://www.chaletonline.de/xml/traffic4u.php?feed=aantal-personen&nocache=1",
		);
	}

	ini_set("default_socket_timeout",7200);
	while(list($key,$value)=each($doorloop_array)) {
		$feed=file_get_contents($value);
		$filename=$unixdir."cache/".$key.".csv";
		if(strlen($feed)>10) {
			save_data_to_file($filename,$feed);
		} else {
			trigger_error("feed ".$value." is leeg",E_USER_NOTICE);
		}
	}
	ini_set("default_socket_timeout",60);
}

if($huidig_uur==5 or $argv[1]=="test") {
	//
	// Controleren of er nieuw te verzenden roominglists zijn
	//
	$roominglist = new roominglist;
	$roominglist->vergelijk_lijsten();

}

if($huidig_uur==6 or $huidig_uur==12 or $huidig_uur==16 or $argv[1]=="test") {
	//
	// Controleren of er nieuw te verzenden aankomstlijsten zijn
	//
	$roominglist = new roominglist;
	$roominglist->vergelijk_lijsten_arrivals();
}


#
# Kijken of alle seizoenen aanwezig zijn in de tabel cmshoofdpagina
#
$db->query("SELECT seizoen_id, naam FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".time()."' ORDER BY seizoen_id;");
while($db->next_record()) {
	$db2->query("SELECT cmshoofdpagina_id FROM cmshoofdpagina WHERE onderdeel='ontbrekendetarieven' AND subonderdeel='seizoen".$db->f("seizoen_id")."';");
	if(!$db2->num_rows()) {
		$db2->query("INSERT INTO cmshoofdpagina SET onderdeel='ontbrekendetarieven', subonderdeel='seizoen".$db->f("seizoen_id")."', omschrijving='Geen prijzen bekend ".addslashes($db->f("naam"))."', tonen=1, rol=12;");
		echo $db2->lastquery."<br>";
	}
}


#
# Bekijken hoeveel tarieven voor het nieuwe seizoen al bekend zijn
#
$db->query("SELECT url, url_en FROM thema WHERE actief=1 AND wzt=1 AND tarievenbekend_seizoen_id>0 ORDER BY adddatetime DESC;");
#echo $db->lastquery;
if($db->next_record()) {
#	echo $db->f("url")."\n<br>";
	if($db->f("url")) $a=file_get_contents("https://www.chalet.nl/thema/".$db->f("url")."/?save_tarievenbekend=1");
	if($db->f("url")) $a=file_get_contents("https://www.chalet.be/thema/".$db->f("url")."/?save_tarievenbekend=1");
	if($db->f("url")) $a=file_get_contents("https://www.chalettour.nl/thema/".$db->f("url")."/?save_tarievenbekend=1");
	if($db->f("url_en")) $a=file_get_contents("https://www.chalet.eu/theme/".$db->f("url_en")."/?save_tarievenbekend=1");
}

# Opslaan hoeveel accommodaties er zijn
$a=file_get_contents("https://www.chalet.nl/zoek-en-boek.php?save_aantalaccommodaties=1");



#
# Kijken bij welke boekingen factuurbedrag afwijkt van berekende totale reissom (en dan "factuur_bedrag_wijkt_af" aanpassen)
#
if($huidig_uur==4) {
	$db->query("SELECT boeking_id, totale_reissom, boekingsnummer FROM boeking WHERE boekingsnummer<>'' AND geannuleerd=0 AND aankomstdatum>'".time()."' ORDER BY aankomstdatum;");
	while($db->next_record()) {
		$gegevens=get_boekinginfo($db->f("boeking_id"));
		if($gegevens["stap1"]["totale_reissom"]>0 and $gegevens["fin"]["totale_reissom"]>0) {
			$verschil=round($gegevens["stap1"]["totale_reissom"]-$gegevens["fin"]["totale_reissom"],2);
			if(abs($verschil)>0.01) {
				$factuur_bedrag_wijkt_af=1;
			} else {
				$factuur_bedrag_wijkt_af=0;
			}
			if(($gegevens["stap1"]["factuur_bedrag_wijkt_af"] and !$factuur_bedrag_wijkt_af) or (!$gegevens["stap1"]["factuur_bedrag_wijkt_af"] and $factuur_bedrag_wijkt_af)) {
				$db2->query("UPDATE boeking SET factuur_bedrag_wijkt_af='".$factuur_bedrag_wijkt_af."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			}
		}
		#
		# Kijken of er boekingen zijn waarbij de vertrekinfo niet kon worden samengesteld (bij een error wordt dat direct opgeslagen)
		#
		$vars["vertrekinfo_boeking"]=vertrekinfo_boeking($gegevens);
	}
}




#
# Opschoon-acties en database-optimaliseren
#
if($huidig_uur==5) {

	#
	# Controle op garantie-voorraad
	#
	$db->query("SELECT type_id, count(type_id) AS aantal, aankomstdatum FROM garantie WHERE boeking_id=0 AND aankomstdatum_exact>'".time()."' GROUP BY type_id, aankomstdatum ORDER BY aankomstdatum;");
	while($db->next_record()) {
		$db2->query("SELECT type_id, voorraad_garantie FROM tarief WHERE type_id='".$db->f("type_id")."' AND week='".$db->f("aankomstdatum")."';");
		if($db2->next_record()) {
			if($db2->f("voorraad_garantie")<>$db->f("aantal")) {
				voorraad_bijwerken($db->f("type_id"),$db->f("aankomstdatum"),false,$db->f("aantal"),"","","","","","",false,10);
				// wt_mail("jeroen@webtastic.nl","Ter info: onjuist garantie-aantal","\n\nType: ".$db->f("type_id")." - ".date("d-m-Y",$db->f("aankomstdatum")).": ".$db2->f("voorraad_garantie")." moet worden: ".$db->f("aantal"));
			}
		}
	}

	#
	# Tabellen opschonen waar geen bijbehorende id meer van is
	#
	$db->query("DELETE FROM korting_tarief WHERE korting_id NOT IN (SELECT korting_id FROM korting);");
	$db->query("DELETE FROM aanbieding_type WHERE aanbieding_id NOT IN (SELECT aanbieding_id FROM aanbieding);");
	$db->query("DELETE FROM aanbieding_accommodatie WHERE aanbieding_id NOT IN (SELECT aanbieding_id FROM aanbieding);");
	$db->query("DELETE FROM aanbieding_leverancier WHERE aanbieding_id NOT IN (SELECT aanbieding_id FROM aanbieding);");

	$db->query("SHOW TABLE STATUS;");
	while($db->next_record()) {
		if ($db->f("Data_length")<200000000) {
			$db2->query("ANALYZE TABLE `".$db->f("Name")."`;");
			$db2->query("OPTIMIZE TABLE `".$db->f("Name")."`;");
			echo $db->f("Name")."<br>";
		} else {
			echo $db->f("Name")." - too large<br>";
		}
	}
}


#wt_mail("jeroen@webtastic.nl","elk uur afgerond","mail elk uur");

for($i=1;$i<=0;$i++) {

	$mail=new wt_mail;
	$mail->fromname="Chalet.be";
	$mail->from="info@chalet.be";
	$mail->returnpath="info@chalet.be";

	# geen BCC-trackmail
	$mail->send_bcc=false;

	$mail->subject="Testmail ".date("H:i");

	$mail->plaintext="";

	$mail->html_top="";
	$mail->html="<B>Hallo, dit is een test.</B><br/>".date("H:i");
	$mail->html_bottom="";

	$mail->toname="Jeroen Boschman";
	$mail->to="boschman@outlook.com";
	$mail->send();

	$mail->toname="Jeroen Boschman";
	$mail->to="jeroen_boschman@hotmail.com";
	$mail->send();

	$mail->toname="Jeroen Boschman";
	$mail->to="boschman@live.nl";
	$mail->send();

	$mail->toname="Robert Jansen";
	$mail->to="fastjansen@hotmail.com";
	$mail->send();

	sleep(10);
	$mail->toname="Jeroen Boschman";
	$mail->to="boschman@outlook.com";
	$mail->send();

	// sleep(90);

}


?>