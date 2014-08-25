<?php

# /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/xml_import.php [leverancier-xml-nummer] (optioneel: 1 t/m 26...)

#
# Script wordt elke minuut gerund, maar alleen volledig afgelopen om:
#

/*


Temporarily: every supplier at 03, 11, 15 and 19 h. Posarelli and Interhome only at 03h.



Not right now:
-----------------------
Marche Holiday om 0, 3, 6, 9, 12, 15, 18 en 22 uur
Direkt Holidays om 0, 3, 6, 10, 13, 16, 19 en 22 uur
Posarelli om 3 uur
Alle andere leveranciers: tussen 8 en 20 uur elk uur, daarnaast ook om 0, 3, 6 en 22 uur.
-----------------------

*/

#
# IP-adres server bij opvragen XML: 87.250.157.198
#


# Tijdelijk bepaalde leverancierid uitzetten
#$vars["leverancierid_tijdelijk_niet_importeren"]="4";

$current_hour = date("H");
$cron_xml_import = true;


function filter_xml_data($xml, $xml_type, $value, $type_id, $shorter_seasons, $wzt, $seizoenen) {
	if(is_array($xml[$xml_type][$value])) {
		foreach($xml[$xml_type][$value] as $week => $status){
			$season_availability = $seizoenen[$wzt][$week];
			if(isset($shorter_seasons[$type_id][$season_availability])){
				if(($week < $shorter_seasons[$type_id][$season_availability]['begin']) or ($week > $shorter_seasons[$type_id][$season_availability]['end'])){
					unset($xml[$xml_type][$value][$week]);
				}
			}
		}
	}

	return $xml;
}

function get_slow_suppliers($xml_type) {
	//
	// Determine if Posarelli (8), Marche Holiday (14) and Direkt Holidays (24) should be downloaded (because downloading these suppliers takes a long time)
	//

	// temporaily: Marche Holiday (14) and Direkt Holidays (24): always true
	global $current_hour;

	if($xml_type==8) {
		// Posarelli (8)
		if($current_hour==3) {
			return true;
		} else {
			return false;
		}
	} elseif($xml_type==14) {
		// Marche Holiday (14)
		if($current_hour==0 or $current_hour==3 or $current_hour==6 or $current_hour==9 or $current_hour==12 or $current_hour==15 or $current_hour==18 or $current_hour==22) {
			return true;
		} else {
			// return false;
			return true;
		}
	} elseif($xml_type==23) {
		// Interhome (23)
		if($current_hour==3) {
			return true;
		} else {
			return false;
		}
	} elseif($xml_type==24) {
		// Direkt Holidays (24)
		if($current_hour==0 or $current_hour==3 or $current_hour==6 or $current_hour==10 or $current_hour==13 or $current_hour==16 or $current_hour==19 or $current_hour==22) {
			return true;
		} else {
			// return false;
			return true;
		}
	}
}

$track_time = microtime(true);
function track_time($text) {
	global $track_time_text, $track_time;

	// $track_time_text .= "\n".$text.": ".number_format(microtime(true)-floatval($track_time), 3, ",", ".")." seconden";
	$track_time_text .= "\n".$text.": ".ceil(microtime(true)-floatval($track_time))." seconden";

	$track_time = microtime(true);
}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $_SERVER["HTTP_HOST"]=="chalet-dev.web.netromtest.ro") {
	# Lokaal testen
	$testsysteem=true;
	header("Content-type: text/plain; charset=utf-8");
} else {
	# Voorkomen dat er multiple instances van het script runnen
	$PID=`ps aux|grep xml_import.php`;
	$PID=split("\n",$PID);
	while(list($key,$value)=each($PID)) {
		if(preg_match("/php.*xml_import\.php/",$value) and !preg_match("/\/bin\/sh/",$value)) {
			$pidteller++;
#				echo $value."\n";
		}
	}
	if($pidteller>1) {
#		echo "xml_import.php draait al\n";
		exit;
	}
	sleep(1);
}

set_time_limit(0);
ini_set("default_socket_timeout", 20);

if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	$unzip="/usr/bin/unzip";
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		$tmpdir="/home/webtastic/html/chalet/tmp/";
		$test_tmpdir="/tmp/";
	} elseif($_SERVER["HTTP_HOST"]=="chalet-dev.web.netromtest.ro") {
		$tmpdir="/var/www/chalet/chalet-dev.web.netromtest.ro/tmp/";
		$test_tmpdir="/var/www/chalet/chalet-dev.web.netromtest.ro/tmp/";
	} else {
		$tmpdir="/tmp/";
	}
} elseif(preg_match("@/html_test/@",$_SERVER["SCRIPT_FILENAME"])) {
	$unixdir="/var/www/chalet.nl/html_test/";
	$tmpdir="/var/www/chalet.nl/html_test/tmp/";
	$unzip="/usr/bin/unzip";
} else {
	$unixdir="/var/www/chalet.nl/html/";
	$tmpdir="/var/www/chalet.nl/html/tmp/";
	$unzip="/usr/bin/unzip";
}
$cron=true;
include($unixdir."admin/vars.php");
include($unixdir."admin/vars_xmlimport.php");

// make it possible to calculate the changed "vanaf"-prices
$voorraad_gekoppeld=new voorraad_gekoppeld;
$voorraad_gekoppeld->koppeling_uitvoeren_na_einde_script();

#
# Vaste run, of handmatig gestarte run via https://www.chalet.nl/cms_diversen.php?t=3?
#
if($argv[1]=="test.chalet.nl") {
	$db->query("SELECT handmatige_xmlimport_id FROM diverse_instellingen WHERE handmatige_xmlimport_id>0;");
	if($db->next_record()) {
		$argv[1]=$db->f("handmatige_xmlimport_id");
		$db->query("UPDATE diverse_instellingen SET handmatige_xmlimport_id=0;");
	} else {
		exit;
	}
} else {
	if(!$argv[1] and !$testsysteem) {
		if(date("i")==5 and $current_hour>=8 and $current_hour<=20 and $NU_EVEN_NIET) {
			# Alle leveranciers worden doorlopen tussen 8 en 20 uur
		} elseif(date("i")==5 and ($current_hour==3 or $current_hour==11 or $current_hour==15 or $current_hour==19)) {
			// temporarily: only import at 3, 11, 15 and 19 h
		} elseif(date("i")==5 and ($current_hour==0 or $current_hour==3 or $current_hour==6 or $current_hour==22)) {
			# Alle leveranciers worden doorlopen om 0, 3, 6 en 22 uur
		} elseif($current_hour==10 and date("i")==11 and $NU_EVEN_NIET) {
			# Om alle leveranciers als test allemaal te kunnen nalopen: (testtijd staat op 10:11 uur, zie $current_hour en date("i") op de regel hierboven)

		} else {
			$db->query("SELECT handmatige_xmlimport_id FROM diverse_instellingen WHERE handmatige_xmlimport_id>0;");
			if($db->next_record()) {
				$argv[1]=$db->f("handmatige_xmlimport_id");
				$db->query("UPDATE diverse_instellingen SET handmatige_xmlimport_id=0;");
			} else {
				exit;
			}
		}
	}
}

if($testsysteem) {
	echo "<pre>";
}

echo "Start: ".date("r")."\nChalet.nl XML Import - ";
if($argv[1]) {
	echo $vars["xml_type"][$argv[1]];
} else {
	echo "alle XML-leveranciers";
}
echo "\n\n\n";
flush();

# Temp-gegevens wissen
$db->query("DELETE FROM xml_import_flex_temp;");

if(($current_hour==9 and !$argv[1]) or $argv[1]=="5") {
	if(!$testsysteem) {
		#
		# CSV downloaden bij P&V Pierre & Vacances (pas beschikbaar vanaf de ochtend)
		#
		$dispo="DISPO_TO3_".date("d").strtoupper(date("M")).date("Y");

		# Zip-file downloaden
		if(@filemtime($tmpdir."dispo.zip")<(time()-3600)) {
			@unlink($tmpdir."dispo.zip");
			$opts = stream_context_create(array(
				'http' => array(
					'timeout' => 120
					)
				)
			);
			if($zip=@file_get_contents("ftp://to3:PnV#cmq2@mutpv.pierreetvacances.com/".$dispo.".zip", false, $opts)) {
				$fh=fopen($tmpdir."dispo.zip","w",false);
				fwrite($fh,$zip);
				fclose($fh);

				# Zip-file uitpakken
				@unlink($tmpdir."dispo.csv");
				exec($unzip." ".$tmpdir."dispo.zip -d ".$tmpdir);
				rename($tmpdir.$dispo.".csv",$tmpdir."dispo.csv");

			}
		}
		$csv_urls[5]=$tmpdir."dispo.csv";
	}

	track_time("P&V gedownload");
}


#
# XML-gegevens ophalen van sites die de gegevens via 1 XML-bestand aanleveren
#
# (de namen die horen bij deze URL's opnemen in $vars["xml_type"])
#
# 1 = beschikbaarheid
# 2 = tarieven
#
#

# Huetten
#$xml_urls[1][1]="Huetten"; (beschikbaarheid en tarieven werken met losse XML's per accommodatie)

# Alpenchalets
$xml_urls[2][1]="http://alpenchaletsbookings.com/avSync.phtml?asId=1";

# Ski France
$xml_urls[3][1]="https://ski-france-db.com/jobs/avSync.phtml?asId=1";

# CGH
$xml_urls[4][1]="http://www.cgh-partenaires.com/results.xml";

# P&V Pierre & Vacances
#$xml_urls[5][1]="Pierre et Vacances"; (beschikbaarheid werkt met CSV's)

# Frosch
$xml_urls[6][1]="http://f0038e54:chaletnl@www.frosch-info.de/schnittstelle/chaletnl/daily/Vakanzen.xml";
$xml_urls[6][2]="http://f0038e54:chaletnl@www.frosch-info.de/schnittstelle/chaletnl/daily/Preise.xml";

# CIS / Bellecôte Chalets (VVE) (via Arkiane)
$xml_urls[7][1]="http://xml.arkiane.com/xml_v2.asp?app=LS&clt=112&top=8700&qry=extr_plng@top_id='CHALE'";
#$xml_urls[7][2]="CIS / Bellecôte Chalets (VVE)" (tarieven werken met losse XML's per accommodatie)

# Posarelli
if(get_slow_suppliers(8)) {
	$xml_urls[8][1]="http://export.posarellivillas.com/availability.xml";
	$xml_urls[8][2]="http://export.posarellivillas.com/unitrates.xml";
	$xml_urls[8][3]="http://export.posarellivillas.com/unit.xml"; # lastminutes
}

# Maisons Vacances
#$xml_urls[9][1]="Maisons Vacances tarieven (beschikbaarheid werkt met losse XML's per accommodatie)";
#$xml_urls[9][2]="http://www.rent-villas-france.com/servicespub/rent/prix/fr";
$xml_urls[9][2]="http://www.rent-villas-france.com/servicespub/rent/prix/nl";

# CIS Immobilier (via Arkiane)
$xml_urls[10][1]="http://xml.arkiane.com/xml_v2.asp?app=LS&clt=112&top=87&qry=extr_plng@top_id='CHALE'";
#$xml_urls[10][2]="CIS Immobilier" (tarieven werken met losse XML's per accommodatie)

# Odalys
$xml_urls[11][1]="ftp://chalet_nl:chAl0603$!@ftp-xml.odalys.travel/PAC/PAC_CHALET_NL.xml";

# Deux Alpes Voyages (via Arkiane)
$xml_urls[12][1]="http://xml.arkiane.com/xml_v2.asp?app=LS&clt=122&top=3037&qry=extr_plng@top_id='CHALE'";
#$xml_urls[12][2]="Deux Alpes Voyages" (tarieven werken met losse XML's per accommodatie)

# Eurogroup MET SOAP
$soap_urls[13]="http://www.eto.madamevacances.resalys.com/rsl/wsdl_distrib";

# Marche Holiday
if(get_slow_suppliers(14)) {
	$soap_urls[14] = $unixdir."suppliers/marche/index.php";
}

# Des Neiges MET SOAP
$soap_urls[15]="http://chaletdesneiges.resalys.com/rsl/wsdl_distrib";

# Almliesl
$xml_urls[16][1]="http://www.almliesl.com/export_chalet_nl_occupancy_de_w.xml"; # beschikbaarheid winter
$xml_urls[16][2]="http://www.almliesl.com/export_chalet_nl_prices_de_w.xml"; # prijzen winter
#$xml_urls[16][3]="http://www.almliesl.com/export_chalet_nl_occupancy_de_s.xml"; # beschikbaarheid zomer - Tijdelijk uitgezet omdat winter- en zomerfeed hetzelfde zijn (4 juni 2013)
$xml_urls[16][4]="http://www.almliesl.com/export_chalet_nl_prices_de_s.xml"; # prijzen zomer
# http://www.almliesl.com/export_chalel_nl_verfuegbarkeiten_so_wi_occupancy_de_w.xml

# Flaine Immobilier (via Arkiane)
$xml_urls[17][1]="http://resa.alpes-skiresa.com/xml/xml_v2.asp?app=LS&clt=142&top=227&qry=extr_plng@top_id='FICHA'";
#$xml_urls[17][2]="Flaine Immobilier" (tarieven werken met losse XML's per accommodatie)

# Agence des Belleville (via Arkiane)
$xml_urls[18][1]="http://resa.alpes-skiresa.com/xml/xml_v2.asp?app=LS&clt=141&top=58&qry=extr_plng@top_id='CHALE'";
#$xml_urls[18][2]="Agence des Belleville" (tarieven werken met losse XML's per accommodatie)

# Oxygène Immobilier (via Arkiane)
$xml_urls[19][1]="http://xml.arkiane.com/xml_v2.asp?app=LS&clt=23&top=6&qry=extr_plng@top_id='CHANL'";
#$xml_urls[19][2]="Oxygène Immobilier" (tarieven werken met losse XML's per accommodatie)

# Centrale des Hauts Forts (via Arkiane)
$xml_urls[20][1]="http://xml.arkiane.com/xml_v2.asp?app=LS&clt=169&top=7&qry=extr_plng@top_id='CHALE'";
#$xml_urls[20][2]="Centrale des Hauts Forts" (tarieven werken met losse XML's per accommodatie)

# Ville in Italia
$xml_urls[21][1]="https://secure.villeinitalia.com/protAgency/AvailableFile.jsp"; # beschikbaarheid
$xml_urls[21][2]="https://secure.villeinitalia.com/protAgency/DbXmlFile.jsp"; # prijzen
$http_login[21]="italissima:italissima2144";

# Nexity (via Arkiane)
$xml_urls[22][1]="http://xml.arkiane.com/xml_v2.asp?app=LS&clt=238&top=22&qry=extr_plng@top_id='CHANL'";
#$xml_urls[22][2]="Nexity" (tarieven werken met losse XML's per accommodatie)

#Interhome
if(get_slow_suppliers(23)) {
	$soap_urls[23] = $unixdir."suppliers/interhome/index.php";
}

# Direkt Holidays
if(get_slow_suppliers(24)) {
	$soap_urls[24] = $unixdir."suppliers/direktholidays/index.php";
}

# Alpin Rentals
$soap_urls[25] = $unixdir."suppliers/newyseservice/index.php";


# 3 Vallées Immobilier (via Arkiane)
$xml_urls[26][1]="http://resa.alpes-skiresa.com/xml/xml_v2.asp?app=LS&clt=264&top=58&qry=extr_plng@top_id='CHALE'";
#$xml_urls[26][2]="3 Vallées Immobilier" (tarieven werken met losse XML's per accommodatie)


#
# Voor testsysteem
#
if($testsysteem) {
	unset($xml_urls);
	unset($soap_urls);
	// $xml_urls[2][]=$test_tmpdir."alpenchalets.xml";
	// $xml_urls[3][]=$test_tmpdir."skifrance.xml";
	// $xml_urls[4][]=$test_tmpdir."results.xml";
	// $csv_urls[5]=$test_tmpdir."dispo.csv";
	// $xml_urls[6][1]=$test_tmpdir."Vakanzen.xml";
	// $xml_urls[6][2]=$test_tmpdir."Preise.xml";
	// $xml_urls[7][1]=$test_tmpdir."bel.xml";
	// $xml_urls[7][2]=$test_tmpdir."belt.xml";
	$xml_urls[8][1]=$test_tmpdir."availability.xml";
	$xml_urls[8][2]=$test_tmpdir."unitrates.xml";
	$xml_urls[8][3]=$test_tmpdir."unit.xml";
	// $xml_urls[9][2]=$test_tmpdir."nl";
	// $xml_urls[10][1]=$test_tmpdir."1.xml";
	// $xml_urls[11][1]=$test_tmpdir."PAC_CHALET_NL.xml"; # Odalys
	// $xml_urls[11][2]=$test_tmpdir."PAC_CHALET_NL.xml"; # Odalys
	// $xml_urls[12][1]=$test_tmpdir."deuxalpes.xml";
	// $soap_urls[13]="http://www.eto.madamevacances.resalys.com/rsl/wsdl_distrib";
	// $xml_urls[14][1]=$test_tmpdir."deuxalpes.xml";
	// $soap_urls[15]="http://chaletdesneiges.resalys.com/rsl/wsdl_distrib";
	// $xml_urls[16][1]=$test_tmpdir."export_chalet_nl_occupancy_de_w.xml";
	// $xml_urls[16][2]=$test_tmpdir."export_chalet_nl_prices_de_w.xml";
	// $xml_urls[16][3]=$test_tmpdir."export_chalet_nl_occupancy_de_s.xml";
	// $xml_urls[16][4]=$test_tmpdir."export_chalet_nl_prices_de_s.xml";
	// $xml_urls[17][1]=$test_tmpdir."lev.xml";
	// $xml_urls[18][1]=$test_tmpdir."agence.xml";
	// $xml_urls[19][1]=$test_tmpdir."/tmp/oxy.xml";
	// $xml_urls[20][1]="/tmp/locative.xml";
	// $xml_urls[21][1]="/tmp/ville_avail.xml"; # beschikbaarheid
	// $xml_urls[21][2]="/tmp/ville_prices.xml"; # prijzen
	// $xml_urls[22][1]="/tmp/nexity.xml"; # prijzen
	unset($http_login[21]);
}


#
# Bepalen welke flexibele xmlcodes van toepassing zijn
#
$db->query("SELECT l.xml_type, t.leverancierscode FROM type t, accommodatie a, leverancier l WHERE t.accommodatie_id=a.accommodatie_id AND t.leverancier_id=l.leverancier_id AND l.xml_type>0 AND a.flexibel=1;");
while($db->next_record()) {
	$flexibele_xmlcodes[$db->f("xml_type")][$db->f("leverancierscode")]=true;
}


if($testsysteem) {
	while(list($key,$value)=@each($xml_urls)) {
		$test_xmlids.=",".$key;
	}
	while(list($key,$value)=@each($soap_urls)) {
		$test_xmlids.=",".$key;
	}
	$test_leverancierids="0";
	if($test_xmlids) {
		$db->query("SELECT leverancier_id FROM leverancier WHERE xml_type IN (".substr($test_xmlids,1).");");
		while($db->next_record()) {
			$test_leverancierids.=",".$db->f("leverancier_id");
		}
	}
	// $test_leverancierids="325";
}


#
# Indien argv[1] opgegeven: alle andere $xml_urls en $soap_urls wissen
#
if(intval($argv[1])>0) {
	$temp_xml_urls=$xml_urls;
	while(list($key,$value)=each($temp_xml_urls)) {
		if($key<>intval($argv[1])) {
			unset($xml_urls[$key]);
		}
	}
	$temp_soap_urls=$soap_urls;
	while(list($key,$value)=each($temp_soap_urls)) {
		if($key<>intval($argv[1])) {
			unset($soap_urls[$key]);
		}
	}
}

#
# XML-url's verwerken
#
@reset($xml_urls);
while(list($key,$value)=@each($xml_urls)) {
	unset($xml);
	while(list($key2,$value2)=each($value)) {
		if($http_login[$key]) {
			# gebruik CURL (vanwege http-login)
			unset($ch);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $value2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, $http_login[$key]);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
			if($output) {
				$xml=@simplexml_load_string($output);
			}
		} else {
			# gebruik simplexml_load_file
			if($xml=@simplexml_load_file($value2)) {

			} else {
				unset($xml);
				if(!$testsysteem) sleep(30);
				if($xml=@simplexml_load_file($value2)) {

				} else {
					unset($xml);
				}
			}
		}

		if(is_object($xml)) {



			$correct_gedownload[$key]=true;
			if($key==2 or $key==3) {
				#
				# Leveranciers Alpenchalets en France Reisen (Ski France)
				#
				foreach($xml->availability as $value3) {
					if(ereg("^([0-9]{2})\.([0-9]{2})\.([0-9]{4})",$value3->dateOfArrival,$regs)) {
						$unixtime=mktime(0,0,0,$regs[2],$regs[1],$regs[3]);
						$xml_beschikbaar[$key][trim($value3->apartmentId)][$unixtime]=true;

						# Tarieven Alpenchalets en France Reisen
						$xml_brutoprijs[$key][trim($value3->apartmentId)][$unixtime]=trim($value3->price);

						$xml_laatsteimport_leverancier[$key]=true;
					}
				}
			} elseif($key==4) {
				#
				# Leverancier CGH
				#
				foreach($xml->item as $value3) {
					if(ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2})",trim($value3->start_date),$regs)) {
						$unixtime=mktime(0,0,0,$regs[2],$regs[3],$regs[1]);
						$xml_beschikbaar[$key][trim($value3->etab_id)."_".trim($value3->room_type_code)][$unixtime]+=intval($value3->allotment_availability);
						$xml_laatsteimport_leverancier[$key]=true;
					}
				}
			} elseif($key==5) {
				#
				# Leverancier P&V Pierre & Vacances : geen XML (maar CSV)
				#

			} elseif($key==6) {
				#
				# Leverancier Frosch
				#
				foreach($xml->ROWDATA->ROW as $value3) {
					if($key2==1) {
						# Beschikbaarheid
						if(trim($value3->Status)=="F") {
							$datum_begin=strtotime($value3->Vom);
							$datum_eind=strtotime($value3->Bis);

							# Frosch stuurt meestal zaterdagen, soms zondagen: omzetten naar zaterdag
							if(date("w",$datum_begin)<>6) {
								$datum_begin=dichtstbijzijnde_zaterdag($datum_begin);
							}
							if(date("w",$datum_eind)<>6) {
								$datum_eind=dichtstbijzijnde_zaterdag($datum_eind);
							}

							# Doorlopen van begin tot eind
							$week=$datum_begin;
							while($week<$datum_eind) {
								$xml_beschikbaar[$key][trim($value3->ObjektId)][$week]+=1;
								$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
								$xml_laatsteimport_leverancier[$key]=true;
							}
						}
					} elseif($key2==2) {
						# Tarieven
						if(trim($value3->Preisart)=="Wo") {
							$datum_begin=strtotime($value3->Vom);
							$datum_eind=strtotime($value3->Bis);

							# Frosch stuurt meestal zaterdagen, soms zondagen: omzetten naar zaterdag
							if(date("w",$datum_begin)<>6) {
								$datum_begin=dichtstbijzijnde_zaterdag($datum_begin);
							}
							if(date("w",$datum_eind)<>6) {
								$datum_eind=dichtstbijzijnde_zaterdag($datum_eind);
							}

							# Doorlopen van begin tot eind
							$week=$datum_begin;
							while($week<$datum_eind) {
								if($value3->ZPersPreis==0) {
									$xml_brutoprijs[$key][trim($value3->ObjektId)][$week]=trim($value3->Preis);
								}
								$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
								$xml_laatsteimport_leverancier[$key]=true;
							}
						}
					}
				}
			} elseif($key==7 or $key==10 or $key==12 or $key==17 or $key==18 or $key==19 or $key==20 or $key==22 or $key==26) {

				/*

				Arkiane-leveranciers:

				Leverancier CIS / Bellecôte Chalets (VVE)
				CIS Immobilier
				Flaine Immobilier
				Deux Alpes Voyages
				Agence des Belleville
				Oxygène Immobilier
				Centrale des Hauts Forts
				Nexity
				3 Vallées Immobilier

				*/

				foreach($xml->LINE as $value3) {
					$datum_begin=strtotime(ereg_replace("/","-",$value3->ocpt_debut));
					$datum_eind=strtotime(ereg_replace("/","-",$value3->ocpt_fin));

					if(date("Y",$datum_eind)>(date("Y")+1)) {
						# hoge jaartallen: niet meenemen
						$datum_eind=time()+(86400*365*2);
					}

					#
					# $plusdag uitgezet (vanwege conflict met afwijkende vertrekdagtypes). Hopelijk sturen ze voortaan gewoon juiste datums, zodat functie overbodig is (4 augustus 2010)
					#
					# Bellecôte stuurt soms foute XML-gegevens (en stuurt datum op zondag): omzetten naar zaterdag
					if(date("w",$datum_begin)<>6) {
						$plusdag=0;
						if(date("w",$datum_begin)==0) {
#							$plusdag=-1;
						} elseif(date("w",$datum_begin)==5) {
#							$plusdag=1;
						}
						if($plusdag) {
							$datum_begin=mktime(0,0,0,date("m",$datum_begin),date("d",$datum_begin)+$plusdag,date("Y",$datum_begin));
						}
					}

					if(date("w",$datum_eind)<>6) {
						$plusdag=0;
						if(date("w",$datum_eind)==0) {
#							$plusdag=-1;
						} elseif(date("w",$datum_eind)==5) {
#							$plusdag=1;
						}
						if($plusdag) {
							$datum_eind=mktime(0,0,0,date("m",$datum_eind),date("d",$datum_eind)+$plusdag,date("Y",$datum_eind));
						}
					}

					# Doorlopen van begin tot eind
					$week=$datum_begin;
					while($week<$datum_eind) {

						// actual arrival date: niet_beschikbaar
						$xml_niet_beschikbaar[$key][trim($value3->lot_ref)][$week]=true;

						// also state Saturdays as niet_beschikbaar
						if(date("w",$week)<>6) {
							$alt_week = dichtstbijzijnde_zaterdag($week);
							$xml_niet_beschikbaar[$key][trim($value3->lot_ref)][$alt_week]=true;
						}

						$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
						$xml_laatsteimport_leverancier[$key]=true;
					}
				}
			} elseif($key==8) {
				#
				# Leverancier Posarelli Villas
				#
				if($key2==1) {
					# Beschikbaarheid
					$xml=xml_structure_convert($xml);
					while(list($key3,$value3)=@each($xml)) {

						$datum_begin=strtotime($value3["offsetdate"]);

						# Doorlopen van begin tot eind
						unset($temp_beschikbaar);
						for($i=0;$i<=strlen($value3["days"]);$i++) {
							$dag=mktime(0,0,0,date("m",$datum_begin),date("d",$datum_begin)+$i,date("Y",$datum_begin));

							# Dag omzetten naar week (zaterdagen)
							if(date("w",$dag)==6) {
								$week=$dag;
							} else {
								$week=mktime(0,0,0,date("m",$dag),date("d",$dag)-(date("w",$dag)+1),date("Y",$dag));
							}

							# Beschikbaar op dag (0=beschikbaar)
							if(substr($value3["days"],$i,1)=="0") {
								# wel beschikbaar
								$temp_beschikbaar[$week]++;
								if($flexibele_xmlcodes[$key][trim($value3["unique_serial"])]) {
									// if(!$testsysteem) {
										xml_tempsave($key,trim($value3["unique_serial"]),$dag,"beschikbaar","1");
									// }
								}
							} else {
								# niet beschikbaar
								if($flexibele_xmlcodes[$key][trim($value3["unique_serial"])]) {
									// if(!$testsysteem) {
										xml_tempsave($key,trim($value3["unique_serial"]),$dag,"beschikbaar","0");
									// }
								}
							}
							if(date("w",$dag)==5) {
								# zaterdag-zaterdag
								if($temp_beschikbaar[$week]==7) {
									$xml_beschikbaar[$key][trim($value3["unique_serial"])][$week]+=1;
									$xml_laatsteimport_leverancier[$key]=true;
								}
							}

							# Laatste import bijhouden (ook als er geen enkele beschikbaarheid was)
							$xml_laatsteimport_gezien[$key][trim($value3["unique_serial"])]=true;

						}
					}
				} elseif($key2==2) {
					# Tarieven
					$xml=xml_structure_convert($xml);
					while(list($key3,$value3)=each($xml)) {

						# weekly ==> bedrag per week
						if($value3["weekly"]) {
							$datum_begin=strtotime($value3["start_period"]);
							$datum_eind=strtotime($value3["end_period"]);

							# Doorlopen van begin tot eind
							$week=$datum_begin;
							while($week<$datum_eind) {
								$xml_brutoprijs[$key][trim($value3["unique_serial"])][$week]=trim($value3["weekly"]);
								$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
								$xml_laatsteimport_leverancier[$key]=true;
							}
						}
						# daily ==> bedrag per dag
						if($value3["daily"] and $flexibele_xmlcodes[$key][trim($value3["unique_serial"])]) {

							# Doorlopen van begin tot eind
							$dag=$datum_begin;
							while($dag<$datum_eind) {
								// if(!$testsysteem) {
									xml_tempsave($key,trim($value3["unique_serial"]),$dag,"minnachten",trim($value3["minimum"]));
									xml_tempsave($key,trim($value3["unique_serial"]),$dag,"brutoprijs",trim($value3["daily"]));
								// }
								$dag=mktime(0,0,0,date("m",$dag),date("d",$dag)+1,date("Y",$dag));
							}
						}
					}
				} elseif($key2==3) {
					# Lastminutes
					$xml=xml_structure_convert($xml);
					while(list($key3,$value3)=each($xml)) {
						if($value3["lastminute_flag"]==1) {
							if(trim($value3["lastminute_minimum_stay"])==7) {
								$xml_lastminute[$key][trim($value3["unique_serial"])]=trim($value3["lastminute_discount"])."_".trim($value3["lastminute_before"]);
							}
						}
					}
				}
				xml_tempsave("save_last_values", "", "", "", "");
			} elseif($key==9) {
				#
				# Leverancier Maisons Vacances
				#
				if($key2==1) {
					# Beschikbaarheid
					# losse XML's per accommodatie

				} elseif($key2==2) {
					# Tarieven
					foreach($xml->periode as $value3) {
						$datum_begin=strtotime($value3->datedebut);
						$datum_eind=strtotime($value3->datefin);

						# Doorlopen van begin tot eind
						$week=$datum_begin;
						while($week<$datum_eind) {
#							$xml_brutoprijs[$key][trim($value3->ref)][$week]=floatval(trim($value3->prix_base));
							$xml_brutoprijs[$key][trim($value3->ref)][$week]=floatval(trim($value3->prix));
							$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
							$xml_laatsteimport_leverancier[$key]=true;
						}
					}
				}
			} elseif($key==11) {
				#
				# Leverancier Odalys
				#

				# Tarieven en beschikbaarheid
				foreach($xml->Segments->Segment as $value3) {
					$leverancierscode_a=trim($value3->Code->attributes()->Value);
#					foreach($value3->Segments->Segment->Begins->Begin as $value4) {
					foreach($value3->Segments->Segment as $value4) {
						$leverancierscode_t=substr(trim($value4->attributes()->Ref),1);
						foreach($value4->Begins->Begin as $value5) {
							if(trim($value5->Duration->attributes()->Ref)=="D7") {
								$week=strtotime(trim($value5->attributes()->Value));

								# Tarief
								$xml_brutoprijs[$key][$leverancierscode_a."_".$leverancierscode_t][$week]=floatval(trim($value5->Price->attributes()->Value)/100);

								# Beschikbaarheid
								# B0 = 0 Available
								# BS = Some Available
								# BM = Many Available

								$aantalbeschikbaar=trim($value5->attributes()->Ref);
								if($aantalbeschikbaar=="BS") {
									$xml_beschikbaar[$key][$leverancierscode_a."_".$leverancierscode_t][$week]=1;
								} elseif($aantalbeschikbaar=="BM") {
#									$xml_beschikbaar[$key][$leverancierscode_a."_".$leverancierscode_t][$week]=3;

									# ze sturen tegenwoordig bij alle datums "BM" mee, vandaar: voorraad=1 (3 september 2010)
									$xml_beschikbaar[$key][$leverancierscode_a."_".$leverancierscode_t][$week]=1;
								}
								$xml_laatsteimport_leverancier[$key]=true;
							}
						}
					}
				}
			} elseif($key==16) {
				#
				# Leverancier Almliesl
				#

				# Tarieven en beschikbaarheid


				if($key2==1 or $key2==3) {
					unset($hulp_array[$key]);

					# Beschikbaarheid (winter en zomer)
					foreach($xml as $value3) {
						foreach($value3->lodging as $value4) {

							$use_key=trim($value3->code)."_".trim($value4->id);

							# lodging-code en id aan elkaar koppelen
							$hulp_array[$key][trim($value3->code)."_".trim($value4->no)]=trim($value4->id);

							$datum_begin=strtotime($value4->startday);
							$datum_eind=strtotime($value4->endday);

							# Doorlopen van begin tot eind
							unset($temp_beschikbaar);
							for($i=0;$i<=strlen($value4->availability);$i++) {
								$dag=mktime(0,0,0,date("m",$datum_begin),date("d",$datum_begin)+$i,date("Y",$datum_begin));
								# Dag omzetten naar week (zaterdagen)
								if(date("w",$dag)==6) {
									$week=$dag;
								} else {
									$week=mktime(0,0,0,date("m",$dag),date("d",$dag)-(date("w",$dag)+1),date("Y",$dag));
								}

								# Beschikbaar op dag (0=beschikbaar)
								if(substr($value4->availability,$i,1)=="Y") {
									# wel beschikbaar
									$temp_beschikbaar[$week]++;
#									if($flexibele_xmlcodes[$key][trim($value3["unique_serial"])]) {
#										if(!$testsysteem) {
#											xml_tempsave($key,trim($value3["unique_serial"]),$dag,"beschikbaar","1");
#										}
#									}
								} else {
									# niet beschikbaar
#									if($flexibele_xmlcodes[$key][trim($value3["unique_serial"])]) {
#										if(!$testsysteem) {
#											xml_tempsave($key,trim($value3["unique_serial"]),$dag,"beschikbaar","0");
#										}
#									}
								}
								if(date("w",$dag)==5 and substr($value4->changeover,$i-6,1)=="Y") {
									# op vrijdag kijken of de voorgaande week vrij was, en of 6 dagen geleden (=zaterdag) een wisseldag was
									if($temp_beschikbaar[$week]==7) {
										$xml_beschikbaar[$key][$use_key][$week]+=1;
										$xml_laatsteimport_leverancier[$key]=true;
									}
								}

								# Laatste import bijhouden (ook als er geen enkele beschikbaarheid was)
								$xml_laatsteimport_gezien[$key][$use_key]=true;

							}
						}
					}
				} elseif($key2==2 or $key2==4) {
					# Tarieven (winter en zomer)
					foreach($xml as $value3) {
						foreach($value3->lodging as $value4) {

							$use_key=trim($value3->code)."_".trim($hulp_array[$key][trim($value3->code)."_".trim($value4->no)]);

							if(is_object($value4->prices->price)) {
								foreach($value4->prices->price as $value5) {

									$datum_begin=strtotime($value5->fromdate);
									$datum_eind=strtotime($value5->todate);

									# Doorlopen van begin tot eind
									$week=$datum_begin;
									while($week<$datum_eind) {
										unset($tempprijs,$hoogste_prijs);

										# Kijken wat de hoogste prijs is (er worden altijd 3 bedragen verzonden)
										if(is_object($value5->price1->amount)) {
											$hoogste_prijs[1]=floatval(trim($value5->price1->amount));
										}
										if(is_object($value5->price2->amount)) {
											$hoogste_prijs[2]=floatval(trim($value5->price2->amount));
										}
										if(is_object($value5->price3->amount)) {
											$hoogste_prijs[3]=floatval(trim($value5->price3->amount));
										}
										if(is_array($hoogste_prijs)) {
											$tempprijs=max($hoogste_prijs);
										} else {
											$tempprijs=0;
										}
										if($tempprijs) {
											$xml_brutoprijs[$key][$use_key][$week]=$tempprijs*7;
										}
										$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
										$xml_laatsteimport_leverancier[$key]=true;
									}
								}
							}
						}
					}
				}
				xml_tempsave("save_last_values", "", "", "", "");
			} elseif($key==21) {
				#
				# Leverancier Ville in Italia
				#
				if($key2==1) {
					# Beschikbaarheid

					foreach($xml->property as $property) {

						$use_key=trim($property->object->objectCode);

						foreach($property->object->freePeriod as $freePeriod) {

							$datum_begin=strtotime(trim($freePeriod->start));
							$datum_eind=strtotime(trim($freePeriod->end));

							# Doorlopen van begin tot eind
							$week=$datum_begin;
							while($week<$datum_eind) {
								$xml_beschikbaar[$key][trim($use_key)][$week]=1;
								$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
								$xml_laatsteimport_leverancier[$key]=true;
							}
						}
					}

				} elseif($key2==2) {
					# Tarieven
					foreach($xml->Group as $group) {
						foreach($group->Resources->Resource as $resource) {
							$use_key=trim($resource->attributes()->code);
							foreach($resource->Prices->Price as $price) {
								// var_dump($price);
								$datum_begin=intval($price->attributes()->from);
								$datum_eind=intval($price->attributes()->to);

								# Doorlopen van begin tot eind
								$week=$datum_begin;
								while($week<$datum_eind) {
									$tarief=floatval($price->Weekly);
									if($tarief>0) {
										$xml_brutoprijs[$key][$use_key][$week]=$tarief;
										$xml_laatsteimport_leverancier[$key]=true;
									}
									$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
								}
							}
						}
					}
				}
			}
		} else {
			// trigger_error("_notice: URL ".$value2." onbereikbaar of geen valide XML",E_USER_NOTICE);
		}
	}

	track_time("XML-url ".$vars["xml_type"][$key]." gedownload");

}

#echo wt_dump($xml_lastminute);
#echo wt_dump_with_unixtime($xml_beschikbaar[6]["CH 290.003"]);
#echo wt_dump_with_unixtime($xml_beschikbaar);
#echo wt_dump_with_unixtime($xml_niet_beschikbaar);
#echo wt_dump_with_unixtime($xml_beschikbaar_flex);
#echo wt_dump_with_unixtime($xml_brutoprijs);
#echo wt_dump_with_unixtime($xml_minimum_aantal_nachten);
#exit;

# geheugen vrijmaken
unset($xml,$hulp_array);

#
# CSV-url's verwerken (P&V Pierre & Vacances)
#
while(list($key,$value)=@each($csv_urls)) {
	$f=@fopen($value,"r");
	if($f) {
		while(!feof($f)) {
			$value2=fgets($f);
			$split=split(";",$value2);
			if($split[4]==7) {
				$unixtime=strtotime($split[3]);
				if($split[5]==1) {
					$aantal=1;
				} elseif($split[5]==2) {
					$aantal=6;
				} else {
					$aantal=0;
				}
				$typeid=trim($split[0])."_".trim($split[1]);
				$xml_beschikbaar[$key][$typeid][$unixtime]=$aantal;
				$xml_brutoprijs[$key][$typeid][$unixtime]=trim($split[7]);
				$xml_laatsteimport_leverancier[$key]=true;
			}
		}
	}
	fclose($f);
	unset($unixtime,$aantal,$value2,$split,$typeid);

	track_time("CSV-url ".$vars["xml_type"][$key]." verwerkt");

}


#
# SOAP-urls verwerken
#
@reset($soap_urls);
while(list($key,$value)=@each($soap_urls)) {

	if($key == 14) {
		//
		// Class Marche Holiday
		//
		if(file_exists($value)) {

			// Marche Holiday
			require_once($value);

			$marcheHolidays = new MarcheHolidays();

			// Get the last dates for each season type (winter=1, summer=2)
			$q = "SELECT eind AS end, begin as begin, type FROM `seizoen` WHERE eind>NOW()";

			$db->query($q);
			while($db->next_record()) {
				$endDate[$db->f("type")][] = $db->f("end");
				$startDate[$db->f("type")][] = $db->f("begin");
			}

			// Get all accommodations from Marche Holiday (229)
			$q = "SELECT t.leverancierscode, t.leverancierscode_negeertarief, a.wzt FROM `type` t JOIN `accommodatie` a USING(accommodatie_id) WHERE t.`leverancier_id` = '229' AND t.`leverancierscode` IS NOT NULL AND t.`leverancierscode` <> ''";
			$db->query($q);

			// Loop through all the database accommodations
			while($db->next_record()) {

				if($db->f("leverancierscode_negeertarief") != NULL) {
					$leverancierscode_negeertarief = explode(",",$db->f("leverancierscode_negeertarief"));
					$leverancierscode = explode(",",$db->f("leverancierscode"));

					$accCode = array_diff($leverancierscode, $leverancierscode_negeertarief);
					$accCode = array_shift($accCode);
				} else {
					$accCode = $db->f("leverancierscode");
				}

				$seasonId = $db->f("wzt");

				foreach($endDate[$seasonId] as $endDatekey => $endDateValue) {
					$x = strtotime($endDateValue);
					$end_date = strtotime("+ 7 days", $x);
					$end_date = date("Y-m-d", $end_date);

					$start_date = $startDate[$seasonId][$endDatekey];
					if(strtotime($startDate[$seasonId][$endDatekey]) < time()) {
						$start_date = date("Y-m-d");
					}

					if($availability = $marcheHolidays->getAvailability($accCode, $start_date, $end_date)) {
						// Get the availability
						if(isset($xml_beschikbaar[$key][$accCode])) {
								$xml_beschikbaar[$key][$accCode] = $xml_beschikbaar[$key][$accCode] + $availability;
						} else {
								$xml_beschikbaar[$key][$accCode] = $availability;
						}
					}
				}
				if(!isset($xml_beschikbaar[$key][$accCode])) $xml_beschikbaar[$key][$accCode] = array();
			}

			// Update last import date for supplier.
			$xml_laatsteimport_leverancier[$key]=true;
		}
	} elseif($key == 23) {
		//
		// Class Interhome
		//
		if(file_exists($value)) {

			require_once($value);

			// Instantiate the Interhome class
			$interHome = new InterHome();

			// Get the Interhome server status
			$iHomeServerStatus = current($interHome->getStatus())->getCheckServerHealthResult();

			// Check for SOAP server services
			if($iHomeServerStatus->getAvailability() != "OK" || $iHomeServerStatus->getPriceCheck() != "OK"){
				print_r($iHomeServerStatus->getMessages());
				die();
			}
			// Get the last dates for each season type (winter=1, summer=2)
			$q = "SELECT eind AS end, begin as begin, type FROM `seizoen` WHERE eind>NOW()";

			$db->query($q);
			while($db->next_record()) {
				$endDate[$db->f("type")][] = $db->f("end");
				$startDate[$db->f("type")][] = $db->f("begin");
			}

			// Get all accommodations from Interhome (421)
			$q = "SELECT t.leverancierscode, t.leverancierscode_negeertarief, a.wzt FROM `type` t JOIN `accommodatie` a USING(accommodatie_id) WHERE t.`leverancier_id` = '421' AND t.`leverancierscode` IS NOT NULL AND t.`leverancierscode` <> ''";
			$db->query($q);

			// Loop through all the database accommodations
			while($db->next_record()) {

				if($db->f("leverancierscode_negeertarief") != NULL) {
					$leverancierscode_negeertarief = explode(",",$db->f("leverancierscode_negeertarief"));
					$leverancierscode = explode(",",$db->f("leverancierscode"));

					$accCode = array_diff($leverancierscode, $leverancierscode_negeertarief);
					$accCode = array_shift($accCode);
				} else {
					$accCode = $db->f("leverancierscode");
				}

				$seasonId = $db->f("wzt");

				foreach($endDate[$seasonId] as $endDatekey => $endDateValue){
					$x = strtotime($endDateValue);
					$end_date = strtotime("+ 7 days", $x);
					$end_date = date("Y-m-d", $end_date);

					$start_date = $startDate[$seasonId][$endDatekey];
					if(strtotime($startDate[$seasonId][$endDatekey]) < time()) {
						$start_date = date("Y-m-d");
					}

					if($availability = $interHome->getAvailability($accCode, $start_date, $end_date)) {
						// Get the availability
						if(isset($xml_beschikbaar[$key][$accCode])) {
							$xml_beschikbaar[$key][$accCode] = $xml_beschikbaar[$key][$accCode] + $availability;
						} else {
							$xml_beschikbaar[$key][$accCode] = $availability;
						}
					}
				}
				if(!isset($xml_beschikbaar[$key][$accCode])) $xml_beschikbaar[$key][$accCode] = array();
			}

			file_put_contents($unixdir."tmp/availabilities_1.txt", var_export($xml_beschikbaar[23], true));

			// Get the prices
			$xml_brutoprijs[$key] = $interHome->processPrices($xml_beschikbaar[$key]);

			$xml_laatsteimport_leverancier[$key]=true;
			$xml_discounts[$key] = $interHome->getDiscounts();

		}
	} elseif($key == 24) {
		//
		// Class Direkt Holidays
		//
		if(file_exists($value)) {

			require_once($value);

			// Instantiate the DirektHolidays class
			$direktHolidays = new DirektHolidays("http://www.direktholidays.at/index.php?id=3&L=2");

			// Get the last dates for each season type (winter=1, summer=2)
			$q = "SELECT eind AS end, begin as begin, type FROM `seizoen` WHERE eind>NOW()";
			$db->query($q);
			while($db->next_record()) {
				$endDate[$db->f("type")][] = $db->f("end");
				$startDate[$db->f("type")][] = $db->f("begin");
			}
			// Get all accommodations from Direkt Holidays (35)
			$q = "SELECT t.leverancierscode, t.leverancierscode_negeertarief, a.wzt FROM `type` t JOIN `accommodatie` a USING(accommodatie_id) WHERE t.`leverancier_id` = '35' AND t.`leverancierscode` IS NOT NULL AND t.`leverancierscode` <> '';";
			$db->query($q);

			// Loop through all the database accommodations
			while($db->next_record()) {

				if($db->f("leverancierscode_negeertarief") != NULL) {
					$leverancierscode_negeertarief = explode(",",$db->f("leverancierscode_negeertarief"));
					$leverancierscode = explode(",",$db->f("leverancierscode"));

					$accCode = array_diff($leverancierscode, $leverancierscode_negeertarief);
					$accCode = array_shift($accCode);
				} else {
					$accCode = $db->f("leverancierscode");
				}

				$seasonId = $db->f("wzt");
				foreach($endDate[$seasonId] as $endDatekey => $endDateValue) {
					$x = strtotime($endDateValue);
					$end_date = strtotime("+ 7 days", $x);
					$end_date = date("Y-m-d", $end_date);

					$url = $direktHolidays->getAccommodationURL($accCode);
					$html = $direktHolidays->curlPricesRequest($url);
					$start_date = $startDate[$seasonId][$endDatekey];
					if(strtotime($startDate[$seasonId][$endDatekey]) < time()) {
						$start_date = date("Y-m-d");
					}

					if($availability = $direktHolidays->getAvailability($html, $start_date, $end_date)) {
						// Get the availability
						if(isset($xml_beschikbaar[$key][$accCode])) {
								$xml_beschikbaar[$key][$accCode] = $xml_beschikbaar[$key][$accCode] + $availability;
						} else {
								$xml_beschikbaar[$key][$accCode] = $availability;
						}
					}

					if($prices = $direktHolidays->getPrices($html, $start_date, $end_date)) {
						// Get the prices
						if(isset($xml_brutoprijs[$key][$accCode])) {
								$xml_brutoprijs[$key][$accCode] = $xml_brutoprijs[$key][$accCode] + $prices;
						} else {
								$xml_brutoprijs[$key][$accCode] = $prices;
						}
					}
				}
			}
			$xml_laatsteimport_leverancier[$key]=true;
		}
	} elseif($key==25) {
		//
		// Class Alpin Rentals
		//
		if(file_exists($value)) {
			require_once($value);
			$alpinRentals = new AlpinRentals();
			$q = "SELECT eind AS end, begin as begin, type FROM `seizoen` WHERE eind>NOW()";

			$db->query($q);


			while($db->next_record()) {
				$endDate[$db->f("type")][] = $db->f("end");
				$startDate[$db->f("type")][] = $db->f("begin");
			}

			 // Get all accommodations from AlpinRentals (245)
			 $q = "SELECT t.leverancierscode, t.leverancierscode_negeertarief, a.wzt FROM `type` t JOIN `accommodatie` a USING(accommodatie_id) WHERE t.`tonen`=1 AND t.`leverancier_id` = '245' AND t.`leverancierscode` IS NOT NULL AND t.`leverancierscode` <> ''";

			 $db->query($q);
			 // Loop through all the database accommodations
			 while($db->next_record()) {
				 $accCode = $db->f("leverancierscode");
				 $seasonId = $db->f("wzt");

				 foreach($endDate[$seasonId] as $endDatekey => $endDateValue){
					 $x = strtotime($endDateValue);
					 $end_date = strtotime("+ 7 days", $x);
					 $end_date = date("Y-m-d", $end_date);

					 $start_date = $startDate[$seasonId][$endDatekey];
					 if(strtotime($startDate[$seasonId][$endDatekey]) < time()) {
							 $start_date = date("Y-m-d");
					 }

					 if($availability = $alpinRentals->getAvailability($accCode, $start_date, $end_date)) {
							 // Get the availability
							 if(isset($xml_beschikbaar[$key][$accCode])) {
									 $xml_beschikbaar[$key][$accCode] = $xml_beschikbaar[$key][$accCode] + $availability;
							 } else {
									 $xml_beschikbaar[$key][$accCode] = $availability;
							 }
					 }


					 $prices = $alpinRentals->getPrices($accCode, $start_date, $end_date);
					 if(isset($xml_brutoprijs[$key][$accCode])){
						 $xml_brutoprijs[$key][$accCode] =$xml_brutoprijs[$key][$accCode] + $prices;
					 }else {
						 $xml_brutoprijs[$key][$accCode] = $prices;
					 }

				 }
				 if(!isset($xml_beschikbaar[$key][$accCode])) {
				 	$xml_beschikbaar[$key][$accCode] = array();
				 }
			 }
			 $xml_laatsteimport_leverancier[$key]=true;
		 }
	} else {
		//
		// Other SOAP suppliers (Eurogroup and Des Neiges)
		//
		@unlink($tmpdir."soapfile_".$key.".txt");
		$opts = stream_context_create(array(
			'http' => array(
				'timeout' => 20
				)
			)
		);
		$soapcontent=@file_get_contents($value, false, $opts);
		if(strlen($soapcontent)>100) {
			file_put_contents($tmpdir."soapfile_".$key.".txt",$soapcontent);
		}
		if(file_exists($tmpdir."soapfile_".$key.".txt")) {
			$client = @new SoapClient($tmpdir."soapfile_".$key.".txt",array('trace'=>1));
			if(is_object($client)) {

				if($key==13 or $key==15) {
					#
					# Eurogroup
					#
					if($key==13) {
						$te_doorlopen_partnercodes=array("REBERE002","REBERE004");
						$soap_baseid="eurogroup";
						$soap_username="chaletnl";
						$soap_password="partner";
						#$soap_conventionid="1894"; # conventionid is alleen nodig in de zomer!
						$soap_conventionid="";
						$soap_allotment="";
					}

					#
					# Des Neiges
					#
					if($key==15) {
						$te_doorlopen_partnercodes=array("CHNL");
						$soap_baseid="cdn";
						$soap_username="CHNL";
						$soap_password="chnl";
						$soap_conventionid="";
						$soap_allotment="0";
					}

					while(list($key2,$value2)=each($te_doorlopen_partnercodes)) {
						unset($soap_error);
						try {
							$result=$client->getDistribProposals2($soap_baseid,$soap_username,$soap_password,$value2,$soap_conventionid,$soap_allotment);
						} catch (Exception $e) {
							#echo($e->getMessage());

							if(!$soap_error_getoond[$key]) {
								// trigger_error("_notice: SOAP-id ".$key." onbereikbaar: ".$e->getMessage(),E_USER_NOTICE);
								$soap_error_getoond[$key]=true;
							}
							$soap_error=true;
						}
						if(!$soap_error) {
							if(is_array($result->distribProposal)) {
								foreach($result->distribProposal as $value3) {
									$typeid=utf8_decode($value3->etab_id)."_".utf8_decode($value3->room_type_code);

									$datum_begin=strtotime($value3->start_date);
									$datum_eind=strtotime($value3->end_date);

									$aantal=$value3->allotment_availability;

									$aantaldagen=round(($datum_eind-$datum_begin)/86400);

									if($aantaldagen>=6 and $aantaldagen<=9) {
										$xml_beschikbaar[$key][$typeid][$datum_begin]+=$aantal;
										$xml_brutoprijs[$key][$typeid][$datum_begin]=utf8_decode($value3->public_price);
										$xml_laatsteimport_leverancier[$key]=true;
									}
								}
							}
						}
					}
				}
			}
		}
		unset($client,$result,$datum_begin,$datum_eind,$aantal,$aantaldagen,$value3,$typeid);
	}

	track_time("SOAP-url ".$vars["xml_type"][$key]." gedownload en verwerkt");

}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
#	echo wt_dump_with_unixtime($xml_beschikbaar);
#	echo wt_dump_with_unixtime($xml_brutoprijs);
#	exit;
}

#
# DEZE WEER ACTIVEREN? (maar dan op een andere plaats in de code)
#
# Oude XML-tarieven wissen
#$db->query("DELETE FROM xml_tarievenimport WHERE type_id='".addslashes($db->f("type_id"))."' AND week<'".time()."';");

# geheugen vrijmaken
unset($csv);

# Even opnieuw verbinding maken met de database
$db->query("SELECT 1;");

# Afwijkende vertrekdagtypes laden
$db->query("SELECT acs.accommodatie_id, v.vertrekdagtype_id, v.seizoen_id, v.afwijking FROM vertrekdagtype v, accommodatie_seizoen acs WHERE v.seizoen_id=acs.seizoen_id AND v.vertrekdagtype_id=acs.vertrekdagtype_id AND v.soort=1;");
while($db->next_record()) {
	$vertrekdagtype[$db->f("accommodatie_id")][$db->f("seizoen_id")]=$db->f("afwijking");
}
$geen_vertrekdagaanpassing_leverancier=array(6);

# Seizoenen koppelen aan datums
$db->query("SELECT seizoen_id, type, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".time()."' ORDER BY begin;");
while($db->next_record()) {
	$season_last_day[$db->f("seizoen_id")] = $db->f("eind");
	if(!$eerste_datum_alle_seizoenen) $eerste_datum_alle_seizoenen=$db->f("begin");
	$week=$db->f("begin");

	# Begin seizoen bepalen
	if(!$beginseizoen[$db->f("type")][$db->f("seizoen_id")]) {
		$beginseizoen[$db->f("type")][$db->f("seizoen_id")]=$week;
	}

	# weekseizoen
	while($week<=$db->f("eind")) {
		$seizoenen[$db->f("type")][$week]=$db->f("seizoen_id");
		$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));

		# Het actieve seizoen bepalen
		if(!$actieve_seizoen[$db->f("type")]) {
			$actieve_seizoen[$db->f("type")]=$db->f("seizoen_id");
		}
	}

	# dagseizoen
	$dag=$db->f("begin");
	while($dag<=$db->f("eind")) {
		$seizoenen_dag[$db->f("type")][$dag]=$db->f("seizoen_id");
		$dag=mktime(0,0,0,date("m",$dag),date("d",$dag)+1,date("Y",$dag));
	}
}

$db->query("SELECT type_id, seizoen_id, begin, eind from type_seizoen WHERE eind>NOW();");
while($db->next_record()){
	if($db->f("begin") != null){
		$shorter_seasons[$db->f("type_id")][$db->f("seizoen_id")]["begin"] = strtotime($db->f("begin"));
		$shorter_seasons[$db->f("type_id")][$db->f("seizoen_id")]["end"] = strtotime($db->f("eind"));
	}
}
if($testsysteem) {
	# test-query
#	$db->query("SELECT la.begincode, t.type_id, t.leverancierscode, t.leverancierscode_negeertarief, a.leverancierscode AS aleverancierscode, t.leverancier_id, a.naam, a.flexibel, a.accommodatie_id, t.xmltarievenimport, t.naam AS tnaam, t.optimaalaantalpersonen, t.maxaantalpersonen, a.wzt, l.xml_type, p.naam AS plaats, l.naam AS leverancier FROM type t, accommodatie a, leverancier l, land la, plaats p WHERE a.tonen=1 AND t.tonen=1 AND a.archief=0 AND a.plaats_id=p.plaats_id AND p.land_id=la.land_id AND t.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.xml_type IS NOT NULL AND t.leverancierscode IS NOT NULL AND t.type_id=3394 ORDER BY t.leverancier_id;");

	$db->query("SELECT la.begincode, t.type_id, t.leverancierscode, t.leverancierscode_negeertarief, a.leverancierscode AS aleverancierscode, t.leverancier_id, a.naam, a.flexibel, a.accommodatie_id, a.aankomst_plusmin, t.xmltarievenimport, t.naam AS tnaam, t.optimaalaantalpersonen, t.maxaantalpersonen, a.wzt, l.xml_type, p.naam AS plaats, l.naam AS leverancier FROM type t, accommodatie a, leverancier l, land la, plaats p WHERE a.tonen=1 AND t.tonen=1 AND a.archief=0 AND a.plaats_id=p.plaats_id AND p.land_id=la.land_id AND t.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.xml_type IS NOT NULL AND t.leverancierscode IS NOT NULL AND t.leverancier_id IN (".$test_leverancierids.") ORDER BY t.leverancier_id;");

#	$db->query("SELECT la.begincode, t.type_id, t.leverancierscode, t.leverancierscode_negeertarief, a.leverancierscode AS aleverancierscode, t.leverancier_id, a.naam, a.flexibel, a.accommodatie_id, a.aankomst_plusmin, t.xmltarievenimport, t.naam AS tnaam, t.optimaalaantalpersonen, t.maxaantalpersonen, a.wzt, l.xml_type, p.naam AS plaats, l.naam AS leverancier FROM type t, accommodatie a, leverancier l, land la, plaats p WHERE a.tonen=1 AND t.tonen=1 AND a.archief=0 AND a.plaats_id=p.plaats_id AND p.land_id=la.land_id AND t.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.xml_type IS NOT NULL AND t.leverancierscode IS NOT NULL AND t.type_id=5175 ORDER BY t.leverancier_id;");

#	$db->query("SELECT la.begincode, t.type_id, t.leverancierscode, t.leverancierscode_negeertarief, a.leverancierscode AS aleverancierscode, t.leverancier_id, a.naam, a.flexibel, a.accommodatie_id, a.aankomst_plusmin, t.xmltarievenimport, t.naam AS tnaam, t.optimaalaantalpersonen, t.maxaantalpersonen, a.wzt, l.xml_type, p.naam AS plaats, l.naam AS leverancier FROM type t, accommodatie a, leverancier l, land la, plaats p WHERE a.tonen=1 AND t.tonen=1 AND a.archief=0 AND a.plaats_id=p.plaats_id AND p.land_id=la.land_id AND t.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.xml_type IS NOT NULL AND t.leverancierscode IS NOT NULL AND t.leverancier_id IN (30) ORDER BY t.leverancier_id;");

#	$db->query("SELECT la.begincode, t.type_id, t.leverancierscode, t.leverancierscode_negeertarief, t.xmltarievenimport, a.leverancierscode AS aleverancierscode, t.leverancier_id, a.naam, a.flexibel, a.accommodatie_id, a.aankomst_plusmin, t.naam AS tnaam, t.optimaalaantalpersonen, t.maxaantalpersonen, a.wzt, l.xml_type, p.naam AS plaats, l.naam AS leverancier FROM type t, accommodatie a, leverancier l, land la, plaats p WHERE a.tonen=1 AND t.tonen=1 AND a.archief=0 AND a.plaats_id=p.plaats_id AND p.land_id=la.land_id AND t.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.xml_type IS NOT NULL AND t.leverancierscode IS NOT NULL AND a.accommodatie_id=2015 ORDER BY t.leverancier_id, a.wzt;");
	// echo $db->lq."<p>kk";
	// exit;
} else {
	# echte query
	if(intval($argv[1])>0) {
		$andquery="AND l.xml_type='".intval($argv[1])."'";
	} else {
		$andquery="";
	}
	$db->query("SELECT la.begincode, t.type_id, t.leverancierscode, t.leverancierscode_negeertarief, t.xmltarievenimport, a.leverancierscode AS aleverancierscode, t.leverancier_id, a.naam, a.flexibel, a.accommodatie_id, a.aankomst_plusmin, t.naam AS tnaam, t.optimaalaantalpersonen, t.maxaantalpersonen, a.wzt, l.xml_type, p.naam AS plaats, l.naam AS leverancier FROM type t, accommodatie a, leverancier l, land la, plaats p WHERE a.tonen=1 AND t.tonen=1 AND a.archief=0 AND a.plaats_id=p.plaats_id AND p.land_id=la.land_id AND t.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.xml_type IS NOT NULL ".$andquery.($vars["leverancierid_tijdelijk_niet_importeren"] ? " AND l.leverancier_id NOT IN (".$vars["leverancierid_tijdelijk_niet_importeren"].")" : "")." AND t.leverancierscode IS NOT NULL ORDER BY t.leverancier_id, a.wzt;");
}
while($db->next_record()) {

	$leverancier_verwerkt_aantal[$db->f("leverancier_id")] = true;

	if(count($leverancier_verwerkt_aantal)>=2 and !$leverancier_verwerkt[$db->f("leverancier_id")]) {
		track_time("Leverancier ".$laatste_leverancier_naam." verwerkt");
		$leverancier_verwerkt[$db->f("leverancier_id")] = true;
	}
	$laatste_leverancier_naam = $vars["xml_type"][$db->f("xml_type")];

	unset($totaaltarief);
	if($db->f("xml_type")==4 or $db->f("xml_type")==5 or $db->f("xml_type")==11 or $db->f("xml_type")==16) {
		# samenvoegen accommodatiecode en typecode bij bepaalde leveranciers
		$levcode=$db->f("aleverancierscode")."_".$db->f("leverancierscode");
	} else {
		# alleen type-code gebruiken
		$levcode=$db->f("leverancierscode");
	}
	$leverancierscodes=split(",",$levcode);
	$leverancierscodes_aantal=count($leverancierscodes);
	unset($leverancierscodes_teller);

	# Kijken of er leverancierscodes zijn die niet moeten worden opgeteld bij het totaalbedrag (in geval van multiple leverancierscodes)
	if($db->f("leverancierscode_negeertarief")) {
		$leverancierscode_negeertarief=explode(",",$db->f("leverancierscode_negeertarief"));
		$leverancierscodes_aantal=$leverancierscodes_aantal-count($leverancierscode_negeertarief);
	} else {
		unset($leverancierscode_negeertarief);
	}

	while(list($key,$value)=each($leverancierscodes)) {
#		$type_namen[$db->f("type_id")]="<a href=\"http://".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? "ss.postvak.net/chalet" : "www.chalet.nl")."/cms_tarieven.php?xmlgoedkeuren=1&from=%2Fcms_types.php%3Fshow%3D2%26wzt%3D".$db->f("wzt")."%261k0%3D".$db->f("accommodatie_id")."%262k0%3D".$db->f("type_id")."&sid=_SEIZOEN_ID_&tid=".$db->f("type_id")."\" target=\"_blank\">".$db->f("begincode").$db->f("type_id")." - ".wt_he($db->f("plaats")." - ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : ""))." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." pers.)</a>";
		$type_namen[$db->f("type_id")]="<a href=\"http://".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? "ss.postvak.net/chalet" : "www.chalet.nl")."/cms_tarieven.php?xmlgoedkeuren=1&sid=_SEIZOEN_ID_&tid=".$db->f("type_id")."\" target=\"_blank\">".$db->f("begincode").$db->f("type_id")." - ".wt_he($db->f("plaats")." - ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : ""))." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." pers.)</a>";
		$wzt[$db->f("type_id")]=$db->f("wzt");


		# Bij sommige leveranciers: extra informatie uit leverancierscode halen
		unset($extra_info_leverancierscode);
		if($db->f("xml_type")==1) {
			if(preg_match("/^(.*)-(.*)$/",$value,$regs)) {
				$value=trim($regs[1]);
				$extra_info_leverancierscode=trim($regs[2]);
			}
		}

		# Kijken naar laatste import (puur voor het overzicht van laatste imports)
		if($xml_laatsteimport_gezien[$db->f("xml_type")][$value]) {
			$xml_laatsteimport[$db->f("type_id")]=true;
		}

		if($db->f("xml_type")==1) {

			if($testsysteem) {
#				continue;
			}

			#
			# Leverancier Huetten
			#


			# Beschikbaarheid Huetten
			$aantal_beschikbaar[$db->f("xml_type")][$db->f("type_id")]++;

			$xml_url="https://cst-sync-hms.viomassl.com/xml_booking_overview.php?PartnerId=info@chalet.nl&Pwd=6LSuMaJ4&xmlFile=%3Cparameter%3E%3CLodgeId%3E".$value."%3C/LodgeId%3E%3CPeriod%20StartDate=%22".date("d-m-Y",time()+86400)."%22%20EndDate=%2201-01-".(date("Y")+3)."%22/%3E%3C/parameter%3E";
			// echo "Beschikbaarheid: ".$xml_url."\n";
#			echo $db->f("begincode").$db->f("type_id")." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." leverancierid ".$value.":\n".$xml_url."\n";

			flush();
			if($xml=@simplexml_load_file($xml_url)) {
				$xml=wt_getxml($xml_url);
			}
			if(is_array($xml["buchungs_liste"]["huette"]["booking_info"]["trip"])) {
				$correct_gedownload[$db->f("xml_type")]=true;
				$xml_laatsteimport_leverancier[$db->f("xml_type")]=true;
				if($xml["buchungs_liste"]["huette"]["booking_info"]["trip"]["anreise"]) {
					$xml["buchungs_liste"]["huette"]["booking_info"]["trip"][0]=$xml["buchungs_liste"]["huette"]["booking_info"]["trip"];
				}
				while(list($key2,$value2)=each($xml["buchungs_liste"]["huette"]["booking_info"]["trip"])) {
					if(is_array($value2) and ereg("([0-9][0-9])\.([0-9][0-9])\.([0-9][0-9][0-9][0-9])",$value2["anreise"],$regs)) {
						$datum_begin=mktime(0,0,0,$regs[2],$regs[1],$regs[3]);
						if(ereg("([0-9][0-9])\.([0-9][0-9])\.([0-9][0-9][0-9][0-9])",$value2["abreise"],$regs)) {
							$datum_eind=mktime(0,0,0,$regs[2],$regs[1],$regs[3]);
							if($value2["buchbar"]=="false") {
								$value2["buchbar"]=false;
							} elseif($value2["buchbar"]=="true") {
								$value2["buchbar"]=true;
							}
							# Doorlopen
							$week=$datum_begin;
							while($week<$datum_eind) {
								if($value2["buchbar"]) {

								} else {
									$nietbeschikbaar[$db->f("xml_type")][$db->f("type_id")][$week]++;
								}
								$xml_laatsteimport[$db->f("type_id")]=true;
								$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
							}
						}
					}
				}
			}

			# Tarieven Huetten
			$xml_url="https://cst-sync-hms.viomassl.com/xml_masterdata.php?PartnerId=info@chalet.nl&Pwd=6LSuMaJ4&xmlFile=%3Cparameter%3E%3CLodgeId%3E".$value."%3C/LodgeId%3E%3C/parameter%3E";
			// echo "Tarieven typeid ".$db->f("type_id").": ".$xml_url."\n";
			unset($xml,$season);

			if($xml=@simplexml_load_file($xml_url)) {

			}
			if(is_object($xml)) {

				unset($tarief_season);
				if(is_object($xml->huette->prices->price_group->price)) {

					# Tarieven in $tarief_season zetten (op basis van season-id)
					foreach($xml->huette->prices->price_group as $value3) {
						# Indien $extra_info_leverancierscode is ingesteld: alleen voor dat aantal personen de tarieven ophalen
						if(!$extra_info_leverancierscode or ($extra_info_leverancierscode and $value3->attributes()->persons==$extra_info_leverancierscode)) {

							foreach($value3->price as $value4) {
								if(trim($value4->attributes()->perPerson)=="false") {
									if($tarief_season[trim($value4->attributes()->season)]<trim($value4)) {
										$tarief_season[trim($value4->attributes()->season)]=trim($value4);
									}
								}
							}
						}
					}
				}

				# Seasons doorlopen
				if(is_object($xml->huette->prices->seasons->season)) {
					foreach($xml->huette->prices->seasons->season as $value3) {
						$datum_begin=strtotime($value3->duration->attributes()->begin_date);
						$datum_eind=strtotime($value3->duration->attributes()->end_date);

#						$datum_begin=$value4["begin"];
#						$datum_eind=$value4["eind"];

						# Doorlopen van begin tot eind
						$week=$datum_begin;
						while($week<$datum_eind) {
							# controle op "bookable_divided" uigezet op verzoek van Barteld zodat tarieven rond kerst ook binnenkomen (20-02-2013)
#							if(trim($value3->attributes()->bookable_divided)=="true") {
								$xml_brutoprijs[$db->f("xml_type")][$value][$week]=$tarief_season[trim($value3->attributes()->id)];
#							}
							$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
						}

					}
				}
			}
		} elseif($db->f("xml_type")==2 or $db->f("xml_type")==3) {
			#
			# Leveranciers Alpenchalets en France Reisen (Ski France)
			#
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]++;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
		} elseif($db->f("xml_type")==4) {
			#
			# Leverancier CGH
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
		} elseif($db->f("xml_type")==5) {
			#
			# Leverancier P&V Pierre et Vacances
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
		} elseif($db->f("xml_type")==6) {
			#
			# Leverancier Frosch
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
		} elseif($db->f("xml_type")==7 or $db->f("xml_type")==10 or $db->f("xml_type")==12 or $db->f("xml_type")==17 or $db->f("xml_type")==18 or $db->f("xml_type")==19 or $db->f("xml_type")==20 or $db->f("xml_type")==22 or $db->f("xml_type")==26) {
			/*

			Arkiane-leveranciers:

			Leverancier CIS / Bellecôte Chalets (VVE)
			CIS Immobilier
			Flaine Immobilier
			Deux Alpes Voyages
			Agence des Belleville
			Oxygène Immobilier
			Centrale des Hauts Forts
			Nexity
			3 Vallées Immobilier

			*/


			$aantal_beschikbaar[$db->f("xml_type")][$db->f("type_id")]++;

			# Beschikbaarheid
			if(is_array($xml_niet_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_niet_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_niet_beschikbaar[$db->f("xml_type")][$value])) {
#					$nietbeschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]=$value2;
					$nietbeschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]++;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}

			# Tarieven
			unset($xml);
			if($db->f("xml_type")==7) {
				# CIS / Bellecôte Chalets (VVE)
				$xml_url="http://xml.arkiane.com/xml_v1.asp?app=LS&clt=112&top=8700&qry=tarif_lotref@top_id='CHALE',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==10) {
				# CIS Immobilier
				$xml_url="http://xml.arkiane.com/xml_v1.asp?app=LS&clt=112&top=87&qry=tarif_lotref@top_id='CHALE',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==12) {
				# Deux Alpes Voyages
				$xml_url="http://xml.arkiane.com/xml_v1.asp?app=LS&clt=122&top=3037&qry=tarif_lotref@top_id='CHALE',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==17) {
				# Flaine Immobilier
				$xml_url="http://resa.alpes-skiresa.com/xml/xml_v1.asp?app=LS&clt=142&top=227&qry=tarif_lotref@top_id='FICHA',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==18) {
				# Agence des Belleville
				$xml_url="http://resa.alpes-skiresa.com/xml/xml_v1.asp?app=LS&clt=141&top=58&qry=tarif_lotref@top_id='CHALE',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==19) {
				# Oxygène Immobilier
				$xml_url="http://xml.arkiane.com/xml_v1.asp?app=LS&clt=23&top=6&qry=tarif_lotref@top_id='CHANL',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==20) {
				# Centrale des Hauts Forts
				$xml_url="http://xml.arkiane.com/xml_v1.asp?app=LS&clt=169&top=7&qry=tarif_lotref@top_id='CHALE',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==22) {
				# Nexity
				$xml_url="http://xml.arkiane.com/xml_v1.asp?app=LS&clt=238&top=22&qry=tarif_lotref@top_id='CHANL',@lot_ref='".$value."'";
			} elseif($db->f("xml_type")==26) {
				# 3 Vallées Immobilier
				$xml_url="http://resa.alpes-skiresa.com/xml/xml_v1.asp?app=LS&clt=264&top=58&qry=tarif_lotref@top_id='CHALE',@lot_ref='".$value."'";
			}
			if($xml=@simplexml_load_file($xml_url)) {

			}
			if(is_object($xml)) {
				foreach($xml->Tarif as $value3) {
					$unixtime=strtotime(ereg_replace("/","-",$value3->ptar_debut));
					$xml_brutoprijs[$db->f("xml_type")][trim($value3->lot_ref)][$unixtime]=trim($value3->ptar_montant);
				}
			}

		} elseif($db->f("xml_type")==8) {
			#
			# Leverancier Posarelli Villas
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}

			# Aanbiedingen (lastminutes)
			if($xml_lastminute[$db->f("xml_type")][$value]) {
				$lastminute[$db->f("xml_type")][$xml_lastminute[$db->f("xml_type")][$value]][$db->f("wzt")][$db->f("type_id")]=true;
				$namen_leveranciers[$db->f("xml_type")]=$db->f("leverancier");
			}

			# Tarieven (zie hieronder bij "Tarieven bijwerken")

		} elseif($db->f("xml_type")==9) {
			#
			# Leverancier Maisons Vacances
			#

			# Beschikbaarheid
			unset($xml);
			$xml_url="http://www.rent-villas-france.com/servicespub/rent/reservations-".$value;
			if($xml=@simplexml_load_file($xml_url)) {

			}
			if(is_object($xml)) {
				if($xml->error) {
					# bij XML-error van Maisons Vacances: alle datums als bezet noteren
					echo "Fout bij Maisons Vacances accommodatie F".$db->f("type_id")." (".$value."): ".$xml->error->message."\n";

					# $nietbeschikbaar voor dit type wissen
					unset($nietbeschikbaar[$db->f("xml_type")][$db->f("type_id")]);

					# $beschikbaar voor dit type vullen (met lege week)
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][(time()-86400)]=0;

				} else {

#					echo "XML correct bij Maisons Vacances accommodatie F".$db->f("type_id")."\n<br>";

					$aantal_beschikbaar[$db->f("xml_type")][$db->f("type_id")]++;

					$xml_laatsteimport[$db->f("type_id")]=true;
					foreach($xml->reservation as $value3) {

						$datum_begin=strtotime($value3->datedebut);
						$datum_eind=strtotime($value3->datefin);

						# Niet-zaterdagen omzetten naar zaterdagen
						if(date("w",$datum_begin)<>6) {
							$datum_begin=mktime(0,0,0,date("m",$datum_begin),date("d",$datum_begin)-(date("w",$datum_begin)+1),date("Y",$datum_begin));
						}

						# Niet-vrijdagen omzetten naar vrijdagen
						if(date("w",$datum_eind)<>5) {
							if(date("w",$datum_eind)==6) {
								$datum_eind=mktime(0,0,0,date("m",$datum_eind),date("d",$datum_eind)-1,date("Y",$datum_eind));
							} else {
								$datum_eind=mktime(0,0,0,date("m",$datum_eind),date("d",$datum_eind)-(date("w",$datum_eind)+2),date("Y",$datum_eind));
							}
						}

						# Doorlopen van begin tot eind
						$week=$datum_begin;
						while($week<$datum_eind) {
							$nietbeschikbaar[$db->f("xml_type")][$db->f("type_id")][$week]++;
							$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
						}
					}
				}
			}

			# Tarieven (zie hieronder bij "Tarieven bijwerken")

		} elseif($db->f("xml_type")==11) {
			#
			# Leverancier Odalys
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
		} elseif($db->f("xml_type")==13) {
			#
			# Leverancier Eurogroup
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
		} elseif($db->f("xml_type")==15) {
			#
			# Leverancier Den Neiges
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}

			# Tarieven: verwerking gebeurt onderaan bij het algemene gedeelte "Tarieven bijwerken"

		} elseif($db->f("xml_type")==16) {
			#
			# Leverancier Almliesl
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}

			# Tarieven: verwerking gebeurt onderaan bij het algemene gedeelte "Tarieven bijwerken"

		} elseif($db->f("xml_type")==17) {
			#
			# Leverancier Alpin Rentals Kaprun
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}

			# Tarieven: verwerking gebeurt onderaan bij het algemene gedeelte "Tarieven bijwerken"

		} elseif($db->f("xml_type")==21) {
			#
			# Leverancier Ville in Italia
			#

			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}

			# Tarieven: verwerking gebeurt onderaan bij het algemene gedeelte "Tarieven bijwerken"

		} elseif($db->f("xml_type")==23) {
			#
			# Leverancier Interhome
			#
			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
			# Tarieven: verwerking gebeurt onderaan bij het algemene gedeelte "Tarieven bijwerken"

		} elseif($db->f("xml_type")==24) {

			#
			# Leverancier Interhome
			#
			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
			# Tarieven: verwerking gebeurt onderaan bij het algemene gedeelte "Tarieven bijwerken"

		} elseif($db->f("xml_type")==25) {

			#
			# Leverancier AlpinRentals
			#
			# Beschikbaarheid
			if(is_array($xml_beschikbaar[$db->f("xml_type")][$value])) {
				reset($xml_beschikbaar[$db->f("xml_type")][$value]);
				while(list($key2,$value2)=each($xml_beschikbaar[$db->f("xml_type")][$value])) {
					$beschikbaar[$db->f("xml_type")][$db->f("type_id")][$key2]+=$value2;
					$xml_laatsteimport[$db->f("type_id")]=true;
				}
			}
			# Tarieven: verwerking gebeurt onderaan bij het algemene gedeelte "Tarieven bijwerken"

		}

		#
		# Tarieven bijwerken
		#
		if($db->f("xmltarievenimport")==1) {

			#
			# week-tarieven
			#
			if($db->f("xml_type")==1 or $db->f("xml_type")==2 or $db->f("xml_type")==3 or $db->f("xml_type")==5 or $db->f("xml_type")==6 or $db->f("xml_type")==7 or $db->f("xml_type")==8 or $db->f("xml_type")==9 or $db->f("xml_type")==10 or $db->f("xml_type")==11 or $db->f("xml_type")==12 or $db->f("xml_type")==13 or $db->f("xml_type")==14 or $db->f("xml_type")==15 or $db->f("xml_type")==16 or $db->f("xml_type")==17 or $db->f("xml_type")==18 or $db->f("xml_type")==19 or $db->f("xml_type")==20 or $db->f("xml_type")==21 or $db->f("xml_type")==22 or $db->f("xml_type")==23 or $db->f("xml_type")==24 or $db->f("xml_type")==25 or $db->f("xml_type")==26) {
				#
				# Leveranciers Huetten (1), Alpenchalets (2), Ski France (3), P&V Pierre et Vacances (5), Frosch (6), Bellecôte (7), Posarelli Villas (8), Maisons Vacances Ann Giraud (9) , CIS Immobilier (10), Odalys Résidences (11), Deux Alpes Voyages (12), Eurogroup (13), Marche Holiday (14), Des Neiges (15), Almliesl (16), Flaine Immobilier (17), Agence des Belleville (18), Oxygène Immobilier (19), Centrale des Hauts Forts (20), Ville in Italia (21), Nexity (22), Interhome (23), Direkt Holidays (24), Alpin Rentals Kaprun (25), 3 Vallées Immobilier (26)
				#

				if(isset($shorter_seasons[$db->f("type_id")])){
					//filter $xml_brutoprijs
					$xml_brutoprijs = filter_xml_data($xml_brutoprijs, $db->f("xml_type"),$value, $db->f("type_id"), $shorter_seasons, $db->f("wzt"), $seizoenen);
				}

				if(is_array($xml_brutoprijs[$db->f("xml_type")][$value])) {
					reset($xml_brutoprijs[$db->f("xml_type")][$value]);
					while(list($key2,$value2)=each($xml_brutoprijs[$db->f("xml_type")][$value])) {

						# Alleen tarieven van datums die nog niet voorbij zijn importeren
						if($key2>(time()-604800)) {

							# Rekening houden met afwijkende vertrekdagen
							unset($zaterdag,$welk_seizoen,$zaterdag_wijziging_toegepast);
							if(date("w",$key2)<>6) {
								if(date("w",$key2)<3) {
									$plusmin=0-(date("w",$key2)+1);
								} else {
									$plusmin=6-date("w",$key2);
								}
								$zaterdag=mktime(0,0,0,date("m",$key2),date("d",$key2)+$plusmin,date("Y",$key2));
								$welk_seizoen=$seizoenen[$wzt[$db->f("type_id")]][$zaterdag];
							}
							if(!in_array($db->f("leverancier_id"),$geen_vertrekdagaanpassing_leverancier) and $vertrekdagtype[$db->f("accommodatie_id")][$welk_seizoen] and $zaterdag) {
								$databaseweek=vertrekdagaanpassing($zaterdag,1,$vertrekdagtype[$db->f("accommodatie_id")][$welk_seizoen]);
								if($key2==$databaseweek) {
#									echo date("d-m-Y",$zaterdag)." wordt ".date("d-m-Y",$key2)."<br>\n";
								}
								$key2=$zaterdag;
								$zaterdag_wijziging_toegepast=true;
							}

							# Rekening houden met afwijkende verblijfsduur ("Aankomst (afwijking in dagen)" op accommodatieniveau)
							if($db->f("aankomst_plusmin") and !$zaterdag_wijziging_toegepast) {
								$key2=mktime(0,0,0,date("m",$key2),date("d",$key2)-$db->f("aankomst_plusmin"),date("Y",$key2));
							}


							# Alleen tarieven met datum in de toekomst (nu -7 dagen) importeren
							if($key2>(time()-(86400*7))) {

								# Oude tarief opvragen
								$oudtarief=0;
								unset($oudseizoen,$seizoen_opslaan,$blokkeerxml);
								$db2->query("SELECT bruto, c_bruto, seizoen_id, blokkeerxml FROM tarief WHERE week='".addslashes($key2)."' AND type_id='".addslashes($db->f("type_id"))."';");
								if($db2->next_record()) {
									if($db2->f("bruto")>0) {
										$oudtarief=$db2->f("bruto");
										$oudseizoen=$db2->f("seizoen_id");
									} elseif($db2->f("c_bruto")>0) {
										$oudtarief=$db2->f("c_bruto");
										$oudseizoen=$db2->f("seizoen_id");
									}
									if($db2->f("blokkeerxml")) {
										$blokkeerxml=true;
									}
								}

								if(!$blokkeerxml) {

									# Oude XML-tarief opvragen
									unset($xmltarief_al_in_db,$seizoen_al_in_db);
									$db2->query("SELECT bruto, seizoen_id FROM xml_tarievenimport WHERE week='".addslashes($key2)."' AND type_id='".addslashes($db->f("type_id"))."';");
									if($db2->next_record()) {
										$xmltarief_al_in_db=$db2->f("bruto");
										$seizoen_al_in_db=$db2->f("seizoen_id");
									}

									# Kijken of er leverancierscodes zijn die niet moeten worden opgeteld bij het totaalbedrag (in geval van multiple leverancierscodes)
									if(!@in_array($value,$leverancierscode_negeertarief)) {
										$totaaltarief[$key2."_".$db->f("type_id")]+=$value2;
										$leverancierscodes_teller[$key2]++;
									} else {

									}
									$nieuwxmltarief=$totaaltarief[$key2."_".$db->f("type_id")];

									# Alleen opslaan indien $leverancierscodes_teller[$key2] gelijk is aan $leverancierscodes_aantal (bij optellen tarieven: alleen opslaan indien alle tarieven bekend zijn)
									if($leverancierscodes_teller[$key2]==$leverancierscodes_aantal) {

										# Seizoen bepalen om op te slaan in database
										if($oudseizoen) {
											$seizoen_opslaan=$oudseizoen;
										} else {
											$seizoen_opslaan=$seizoenen[$wzt[$db->f("type_id")]][$key2];
										}

										if($nieuwxmltarief>0 and floor($oudtarief)<>floor($nieuwxmltarief) and (floor($xmltarief_al_in_db)<>floor($nieuwxmltarief) or $seizoen_al_in_db<>$seizoen_opslaan)) {

											// Direkt Holidays summer season custom start / end dates
											if($db->f("xml_type") == 24) {
												$tmp_date = getdate($key2);
												// For the summer season (2), change start date to 01/04 and end date to 30/10
												if(($wzt[$db->f("type_id")] == 2) && ($tmp_date["mon"] < 4 || $tmp_date["mon"] > 10)) {
													continue;
												}
												// For the winter season (1), change the end date to 12/04
												if(($wzt[$db->f("type_id")] == 1) && ($tmp_date["mon"] == 4 && $tmp_date["mday"] > 12)) {
													continue;
												}
											}

											$tarievenquery="week='".addslashes($key2)."', bruto='".addslashes($nieuwxmltarief)."', type_id='".addslashes($db->f("type_id"))."', seizoen_id='".addslashes($seizoen_opslaan)."', importmoment=NOW()";
											if(isset($xmltarief_al_in_db)) {
												$db2->query("UPDATE xml_tarievenimport SET ".$tarievenquery." WHERE week='".addslashes($key2)."' AND type_id='".addslashes($db->f("type_id"))."';");
											} else {
												$db2->query("INSERT INTO xml_tarievenimport SET ".$tarievenquery.";");
											}
											if($seizoen_opslaan) {
												$tarievenbijgewerkt[$db->f("xml_type")."_".$wzt[$db->f("type_id")]][$db->f("type_id")][$key2]=true;
												if($oudseizoen) {
													$tarievenbijgewerkt_seizoen[$db->f("type_id")][$key2]=$oudseizoen;
												}
#												$xml_laatsteimport[$db->f("type_id")]=true;
												$xml_laatstewijziging[$db->f("type_id")]=true;
											}
										}
									}
								}
							}
						}
					}
				}

				// Update discount percentages for interHome
				if(is_array($xml_discounts[$db->f("xml_type")][$value]) && ($db->f("xml_type") == 23)) {

					foreach($xml_discounts[$db->f("xml_type")][$value] as $week => $lev_discount){
						$discount_seasonid = $seizoenen[$wzt[$db->f("type_id")]][$week];
						$new_insert[$discount_seasonid] = false;
						$all_weeks[] = $week;

						$discount_order = 100000-intval($lev_discount);

						$sql_fields =     " editdatetime=NOW(), "
										. " seizoen_id ='".intval($discount_seasonid) ."', "
										. " gekoppeld_code=0, "
										. " xml_korting=2, "
										. " delete_after_xml_korting=0, "
										. " volgorde='".$discount_order."', "
										. " toonexactekorting=1, "
										. " aanbiedingskleur=1, "
										. " interne_opmerkingen='', "
										. " toon_als_aanbieding=1, "
										. " onlinenaam_de='', "
										. " onlinenaam_en='".$txt["en"]["vars"]["xml_discount_message"]."$lev_discount %', "
										. " onlinenaam_fr ='', "
										. " omschrijving = '', "
										. " onlinenaam = '".str_replace("[DISCOUNTVALUE]",$lev_discount, $txt["nl"]["vars"]["xml_discount_message"])."', "
										. " toon_abpagina=1, "
										. " naam='Import kortings percentage - " .$lev_discount. "'";

						$db2->query("SELECT * from korting where xml_korting=2 AND type_id=".$db->f("type_id")." AND seizoen_id = '".intval($discount_seasonid)."'");

						if($db2->next_record()){
							if($new_insert[$discount_seasonid] != true)
								$db2->query("UPDATE korting set " .$sql_fields. " WHERE type_id =".$db->f("type_id")." AND seizoen_id='".$discount_seasonid."'");

							$discountid = $db2->f("korting_id");
						} else {

							$first_week = date("Y-m-d");

							$last_discount_week = date("Y-m-d", $season_last_day[$discount_seasonid]);

							$query =  "INSERT INTO korting set van='$first_week', tot='$last_discount_week', actief=1, adddatetime=NOW(), type_id=".$db->f("type_id").", ".$sql_fields;
							$db2->query($query);

									$new_insert[$discount_seasonid] = true;

									$discountid = $db2->insert_id();
								}

								$all_kortings[] = $discountid;
								$discount_updates[] = $db->f("type_id");
								$db2->query("SELECT * from korting_tarief where korting_id = ".$discountid. " AND week=".$week);
								$sql_tarief_fields =  "inkoopkorting_percentage = $lev_discount,"
													. "week = $week, "
													. "korting_id=$discountid, "
													. "aanbieding_acc_percentage = $lev_discount, "
													. "opgeslagen = NOW() ";

								if($db2->next_record()){
										$db2->query("UPDATE korting_tarief SET ".$sql_tarief_fields." WHERE korting_id = ".$discountid);
								} else {
									$db2->query("INSERT korting_tarief SET ".$sql_tarief_fields);
								}

							}

							$xml_laatstewijziging[$db->f("type_id")]=true;
							$all_weeks_updated = implode(", ", $all_weeks);
							$all_korting_updated = implode(", ", $all_kortings);
							$db2->query("DELETE FROM korting_tarief where week NOT IN ($all_weeks_updated) AND korting_id IN ($all_korting_updated) ");

								unset($all_kortings, $all_weeks, $new_insert);

						} elseif($db->f("xml_type") == 23) {
							$db2->query("SELECT korting_id from korting where xml_korting=2 AND type_id=".$db->f("type_id"));
							while($db2->next_record()){
								$db3->query("DELETE FROM korting_tarief where korting_id=".$db2->f("korting_id"));
							}

							$db2->query("DELETE FROM korting where xml_korting=2 AND type_id=".$db->f("type_id"));
								$discount_updates[] = $db->f("type_id");
							$xml_laatstewijziging[$db->f("type_id")]=true;
						}
			}

			#
			# dag-tarieven
			#
			if($db->f("xml_type")==8 and $db->f("flexibel")) {

				# bestaande tarieven, beschikbaar, voorraad_bijwerken voorraad uit database halen
				unset($flex_bruto,$flex_beschikbaar,$flex_voorraad_bijwerken,$flex_voorraad,$flex_voorraad_xml);
				$db2->query("SELECT bruto, dag, beschikbaar, voorraad_bijwerken, voorraad_garantie, voorraad_allotment, voorraad_vervallen_allotment, voorraad_optie_leverancier, voorraad_request, voorraad_xml FROM tarief_flex WHERE type_id='".$db->f("type_id")."' AND dag>='".$eerste_datum_alle_seizoenen."';");
				while($db2->next_record()) {
					$flex_bruto[$db2->f("dag")]=$db2->f("bruto");
					$flex_beschikbaar[$db2->f("dag")]=$db2->f("beschikbaar");
					$flex_voorraad_bijwerken[$db2->f("dag")]=$db2->f("voorraad_bijwerken");
					$flex_voorraad[$db2->f("dag")]=$db2->f("voorraad_garantie")+$db2->f("voorraad_allotment")+$db2->f("voorraad_vervallen_allotment")+$db2->f("voorraad_optie_leverancier")+$db2->f("voorraad_request");
					$flex_voorraad_xml[$db2->f("dag")]=$db2->f("voorraad_xml");
				}

				# tarieven opslaan in xml_tarievenimport_flex
				$db2->query("SELECT dag, waarde FROM xml_import_flex_temp WHERE xml_type='".$db->f("xml_type")."' AND xmlcode='".addslashes($value)."' AND var='brutoprijs';");
				while($db2->next_record()) {
					if($flex_bruto[$db2->f("dag")]<>$db2->f("waarde")) {
						$welk_seizoen=$seizoenen_dag[$db->f("wzt")][$db2->f("dag")];
						if($welk_seizoen>0) {
							$tarievenquery="week='".addslashes($key2)."', bruto='".addslashes($nieuwxmltarief)."', type_id='".addslashes($db->f("type_id"))."', seizoen_id='".addslashes($seizoen_opslaan)."', importmoment=NOW()";

							// $db3->query("INSERT INTO xml_tarievenimport_flex SET type_id='".$db->f("type_id")."', dag='".addslashes($db2->f("dag"))."', bruto='".addslashes($db2->f("waarde"))."', seizoen_id='".addslashes($welk_seizoen)."', importmoment=NOW();");
							// if($db3->Errno==1062) {
							// 	$db3->query("UPDATE xml_tarievenimport_flex SET bruto='".addslashes($db2->f("waarde"))."', seizoen_id='".addslashes($welk_seizoen)."', importmoment=NOW() WHERE type_id='".$db->f("type_id")."' AND dag='".addslashes($db2->f("dag"))."';");
							// }

							$db3->query("SELECT bruto, seizoen_id FROM xml_tarievenimport_flex WHERE type_id='".$db->f("type_id")."' AND dag='".addslashes($db2->f("dag"))."'");
							if($db3->next_record()) {
								if($db3->f("bruto")<>$db2->f("waarde") or $db3->f("seizoen_id")<>$welk_seizoen) {
									$db3->query("UPDATE xml_tarievenimport_flex SET bruto='".addslashes($db2->f("waarde"))."', seizoen_id='".addslashes($welk_seizoen)."', importmoment=NOW() WHERE type_id='".$db->f("type_id")."' AND dag='".addslashes($db2->f("dag"))."';");
								}
							} else {
								$db3->query("INSERT INTO xml_tarievenimport_flex SET type_id='".$db->f("type_id")."', dag='".addslashes($db2->f("dag"))."', bruto='".addslashes($db2->f("waarde"))."', seizoen_id='".addslashes($welk_seizoen)."', importmoment=NOW();");
							}
						}
					}
				}

				# minimum_aantal_nachten opslaan
				$db2->query("SELECT dag, waarde FROM xml_import_flex_temp WHERE xml_type='".$db->f("xml_type")."' AND xmlcode='".addslashes($value)."' AND var='minnachten';");
				while($db2->next_record()) {
					$db3->query("UPDATE tarief_flex SET minimum_aantal_nachten='".addslashes(intval($db2->f("waarde")))."' WHERE type_id='".$db->f("type_id")."' AND dag='".addslashes($db2->f("dag"))."';");
				}

				# beschikbaarheid opslaan
				$db2->query("SELECT dag, waarde FROM xml_import_flex_temp WHERE xml_type='".$db->f("xml_type")."' AND xmlcode='".addslashes($value)."' AND var='beschikbaar';");
				while($db2->next_record()) {
					if($db2->f("waarde")<>$flex_voorraad_xml[$db2->f("dag")] and isset($flex_voorraad_bijwerken[$db2->f("dag")])) {
						unset($tempbeschikbaar);
						if($flex_voorraad_bijwerken[$db2->f("dag")]) {
							if(($flex_voorraad[$db2->f("dag")]+$db2->f("waarde"))>0) {
								$tempbeschikbaar=1;
							} else {
								$tempbeschikbaar=0;
							}
						}
						if(isset($tempbeschikbaar)) {
							$db3->query("UPDATE tarief_flex SET beschikbaar='".addslashes($tempbeschikbaar)."', voorraad_xml='".addslashes(intval($db2->f("waarde")))."' WHERE type_id='".$db->f("type_id")."' AND dag='".addslashes($db2->f("dag"))."';");
						} else {
							$db3->query("UPDATE tarief_flex SET voorraad_xml='".addslashes(intval($db2->f("waarde")))."' WHERE type_id='".$db->f("type_id")."' AND dag='".addslashes($db2->f("dag"))."';");
						}
						// echo $db3->lastquery."<br>";
					}
				}
			}
		}
	}
}

track_time("Leverancier ".$laatste_leverancier_naam." verwerkt + query afgerond");


// Move and interhome discounts to tarief table.
if(is_array($discount_updates)) {
	$typeid_berekenen_inquery = implode(", ", $discount_updates);
	include("tarieven_berekenen.php");
	unset($typeid_berekenen_inquery);

	track_time("Interhome-kortingen verwerkt");
}

#echo wt_dump($xml_brutoprijs[1][342]);
#echo wt_dump_with_unixtime($nietbeschikbaar);
#echo wt_dump($aantal_beschikbaar);

#echo wt_dump_with_unixtime($xml_brutoprijs);
#echo wt_dump_with_unixtime($beschikbaar);

#echo wt_dump($tarief_season);
#exit;

#echo "Brutoprijs:";
#ksort($xml_brutoprijs[1][337]);
#reset($xml_brutoprijs[1][337]);
#while(list($key,$value)=each($xml_brutoprijs[1][337])) {
#	echo date("d-m-Y",$key)." ".$value."<br>";
#}
#exit;

#echo wt_dump($vertrekdagtype);
#exit;
#

#echo wt_dump_with_unixtime($beschikbaar);
#exit;


#
# voorraad aanpassen op basis van $beschikbaar
#
@reset($beschikbaar);
while(list($key,$value)=@each($beschikbaar)) {
	while(list($key2,$value2)=@each($value)) {
		$db->query("SELECT ta.seizoen_id, ta.voorraad_garantie, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, ta.voorraad_optie_leverancier, ta.voorraad_xml, ta.voorraad_request, ta.voorraad_bijwerken, ta.beschikbaar, ta.seizoen_id, ta.type_id, ta.week, t.accommodatie_id, t.leverancier_id FROM tarief ta, type t WHERE ta.type_id=t.type_id AND ta.type_id='".$key2."' AND ta.week>'".time()."' AND (ta.bruto>0 OR ta.c_bruto>0) AND ta.blokkeerxml=0 ORDER BY ta.week;");
		while($db->next_record()) {
			# Rekening houden met afwijkende vertrekdagen
			if(!in_array($db->f("leverancier_id"),$geen_vertrekdagaanpassing_leverancier) and $vertrekdagtype[$db->f("accommodatie_id")][$db->f("seizoen_id")]) {
				$databaseweek=vertrekdagaanpassing($db->f("week"),1,$vertrekdagtype[$db->f("accommodatie_id")][$db->f("seizoen_id")]);
				if($db->f("week")<>$databaseweek) {
#					echo date("d-m-Y",$db->f("week"))." wordt ".date("d-m-Y",$databaseweek)."<br>\n";
				}
			} else {
				$databaseweek=$db->f("week");
			}
			if($value2[$databaseweek]) {
				$aantal=$value2[$databaseweek];
			} else {
				$aantal=0;
			}
			unset($wijzig_beschikbaar);
			if($aantal<>$db->f("voorraad_xml")) {
				$bovenste5=$db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_request")+$aantal;
				if($db->f("voorraad_bijwerken")) {
					if($bovenste5>0) {
						$tempbeschikbaar=1;
						if(!$db->f("beschikbaar")) {
							$mailtxt[$key."_".$wzt[$key2]].=ereg_replace("_SEIZOEN_ID_",$db->f("seizoen_id"),$type_namen[$key2])." ".date("d-m-Y",$databaseweek).": van <i>niet beschikbaar</i> naar <i>beschikbaar</i><br>\n";
							$wijzig_beschikbaar=1;

							// re-calculate "vanaf"-prices
							$voorraad_gekoppeld->vanaf_prijzen_berekenen($key2);
						}

					} else {
						$tempbeschikbaar=0;
						if($db->f("beschikbaar")) {
							$mailtxt[$key."_".$wzt[$key2]].=ereg_replace("_SEIZOEN_ID_",$db->f("seizoen_id"),$type_namen[$key2])." ".date("d-m-Y",$databaseweek).": van <i>beschikbaar</i> naar <i>niet beschikbaar</i><br>\n";
							$wijzig_beschikbaar=2;

							// re-calculate "vanaf"-prices
							$voorraad_gekoppeld->vanaf_prijzen_berekenen($key2);
						}
					}
				} else {
					$tempbeschikbaar=$db->f("beschikbaar");
				}

				$query="UPDATE tarief SET voorraad_xml='".addslashes($aantal)."', beschikbaar='".addslashes($tempbeschikbaar)."' WHERE type_id='".addslashes($key2)."' AND week='".addslashes($db->f("week"))."';";
				$xml_laatsteimport[$key2]=true;
				$xml_laatstewijziging[$db->f("type_id")]=true;
				$db2->query($query);

				if($testsysteem) {
					echo "Voorraad: ".$query."\n";
				}

				if($db2->affected_rows()>0) {
					# beschikaarheid en voorraad loggen
					$wijzig_xml=$aantal-$db->f("voorraad_xml");
					$db2->query("INSERT INTO beschikbaar_archief SET type_id='".addslashes($key2)."', seizoen_id='".addslashes(addslashes($db->f("seizoen_id")))."', week='".addslashes($db->f("week"))."', datumtijd=NOW(), beschikbaar='".addslashes($wijzig_beschikbaar)."', xml='".addslashes($wijzig_xml)."', totaal='".addslashes($bovenste5)."', user_id='0', via='3';");
				}

				flush();
			}
		}
	}
}
track_time("voorraad aangepast op basis van 'beschikbaar'");

#
# voorraad aanpassen op basis van $nietbeschikbaar
#
@reset($aantal_beschikbaar);
while(list($key,$value)=@each($aantal_beschikbaar)) {
	if($correct_gedownload[$key]) {
		while(list($key2,$value2)=@each($value)) {
			$db->query("SELECT t.accommodatie_id, ta.seizoen_id, ta.voorraad_garantie, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, ta.voorraad_optie_leverancier, ta.voorraad_xml, ta.voorraad_request, ta.voorraad_bijwerken, ta.beschikbaar, ta.seizoen_id, ta.type_id, ta.week, t.leverancier_id FROM tarief ta, type t WHERE ta.type_id=t.type_id AND ta.type_id='".$key2."' AND ta.week>'".time()."' AND (ta.bruto>0 OR ta.c_bruto>0) AND ta.blokkeerxml=0 ORDER BY ta.week;");
			while($db->next_record()) {

				# Rekening houden met afwijkende vertrekdagen
				if(!in_array($db->f("leverancier_id"),$geen_vertrekdagaanpassing_leverancier) and $vertrekdagtype[$db->f("accommodatie_id")][$db->f("seizoen_id")]) {
					$databaseweek=vertrekdagaanpassing($db->f("week"),1,$vertrekdagtype[$db->f("accommodatie_id")][$db->f("seizoen_id")]);
					if($db->f("week")<>$databaseweek) {
#						echo date("d-m-Y",$db->f("week"))." wordt ".date("d-m-Y",$databaseweek)."<br>\n";
					}
				} else {
					$databaseweek=$db->f("week");
				}

				$aantal=$value2-$nietbeschikbaar[$key][$key2][$databaseweek];
				if(!$aantal or $aantal<0) $aantal=0;
				if($aantal<>$db->f("voorraad_xml")) {
					$bovenste5=$db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_request")+$aantal;
					if($db->f("voorraad_bijwerken")) {
						if($bovenste5>0) {
							$tempbeschikbaar=1;
							if(!$db->f("beschikbaar")) {
								$mailtxt[$key."_".$wzt[$key2]].=ereg_replace("_SEIZOEN_ID_",$db->f("seizoen_id"),$type_namen[$key2])." ".date("d-m-Y",$databaseweek).": van <i>niet beschikbaar</i> naar <i>beschikbaar</i><br>\n";
								$wijzig_beschikbaar=1;

								// re-calculate "vanaf"-prices
								$voorraad_gekoppeld->vanaf_prijzen_berekenen($key2);
							}
						} else {
							$tempbeschikbaar=0;
							if($db->f("beschikbaar")) {
								$mailtxt[$key."_".$wzt[$key2]].=ereg_replace("_SEIZOEN_ID_",$db->f("seizoen_id"),$type_namen[$key2])." ".date("d-m-Y",$databaseweek).": van <i>beschikbaar</i> naar <i>niet beschikbaar</i><br>\n";
								$wijzig_beschikbaar=2;

								// re-calculate "vanaf"-prices
								$voorraad_gekoppeld->vanaf_prijzen_berekenen($key2);
							}
						}
					} else {
						$tempbeschikbaar=$db->f("beschikbaar");
					}
					$query="UPDATE tarief SET voorraad_xml='".addslashes($aantal)."', beschikbaar='".addslashes($tempbeschikbaar)."' WHERE type_id='".$key2."' AND week='".$db->f("week")."';";
					$xml_laatsteimport[$key2]=true;
					$xml_laatstewijziging[$db->f("type_id")]=true;

					$db2->query($query);

					if($db2->affected_rows()>0) {
						# beschikaarheid en voorraad loggen
						$wijzig_xml=$aantal-$db->f("voorraad_xml");
						$db2->query("INSERT INTO beschikbaar_archief SET type_id='".addslashes($key2)."', seizoen_id='".addslashes(addslashes($db->f("seizoen_id")))."', week='".addslashes($db->f("week"))."', datumtijd=NOW(), beschikbaar='".addslashes($wijzig_beschikbaar)."', xml='".addslashes($wijzig_xml)."', totaal='".addslashes($bovenste5)."', user_id='0', via='3';");
					}
					if($testsysteem) {
#						echo $query."\n";
					}
					flush();
				}
			}
		}
	}
}
track_time("voorraad aangepast op basis van 'niet beschikbaar'");

if(is_array($lastminute) and $current_hour==3) {
	#
	# lastminutes vewerken (bestaat alleen voor Posarelli)
	#

	unset($seizoen_eind,$kortingid);

	// eind van alle seizoenen bepalen (kortingen lopen tot eind van het seizoen)
	$db->query("SELECT seizoen_id, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen;");
	while($db->next_record()) {
		$seizoen_eind[$db->f("seizoen_id")]=$db->f("eind");
	}

	# Zorgen dat gewiste kortingen straks ook worden doorgerekend
	$db->query("SELECT DISTINCT type_id FROM korting WHERE xml_korting=1;");
	while($db->next_record()) {
		$typeid_berekenen_inquery.=",".$db->f("type_id");
	}

	# Kortingen die niet meer gelden straks kunnen wissen
	$db->query("UPDATE korting SET delete_after_xml_korting=1 WHERE xml_korting=1;");

	while(list($key,$value)=@each($lastminute)) {
		while(list($key2,$value2)=each($value)) {
			while(list($key3,$value3)=each($value2)) {
				unset($aanbiedinginfo);
				# $key3 = winter of zomer

				unset($percentage,$before,$korting_aankomstdata,$korting_seizoen_inquery);

				# percentage en aantal dagen vooraf bepalen
				if(ereg("^([0-9]+)_([0-9]+)$",$key2,$regs)) {
					$percentage=$regs[1];
					$before=$regs[2];
				}

				# Aankomstdata bepalen
				$korting_seizoen_inquery=",0";
				reset($seizoenen[$key3]);
				while(list($key4,$value4)=each($seizoenen[$key3])) {
					if($key4>time() and $key4<=mktime(0,0,0,date("m"),date("d")+$before,date("Y"))) {

						# aankomstdata
						$korting_aankomstdata[$value4][$key4]=true;

						# te doorlopen seizoenen
						$korting_seizoen_inquery.=",".$value4;
					}
				}

				# Teksten korting
				$korting_internenaam="XML -/-".$percentage."% ".$before." dagen voor aankomst";
				$onlinenaam="Last minute korting van ".$percentage."%";
				$omschrijving="Voor reserveringen (van tenminste 7 nachten) die binnen ".$before." dagen voor aankomst gemaakt worden geldt een last minute korting van ".$percentage."%.";

				if($korting_aankomstdata) {

					# Alle types doorlopen
					while(list($key4,$value4)=each($value3)) {

						$typeteller++;
						if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and $typeteller>10) {
#							continue;
						}

						# tarieven van dit type dadelijk opnieuw berekenen
						$typeid_berekenen_inquery.=",".$key4;

						# alle seizoenen die het betreft doorlopen
						$db->query("SELECT DISTINCT seizoen_id FROM tarief WHERE type_id='".$key4."' AND seizoen_id IN (".substr($korting_seizoen_inquery,1).");");
						while($db->next_record()) {

							$volgorde=100000-intval($percentage);

							# Kijken wat de $kortingid is (bestaande korting of nieuwe korting aanmaken)
							$db2->query("SELECT korting_id FROM korting WHERE xml_korting=1 AND type_id='".addslashes($key4)."' AND seizoen_id='".addslashes($db->f("seizoen_id"))."' AND naam='".addslashes($korting_internenaam)."';");
							if($db2->next_record()) {
								# Bestaande korting
								$kortingid=$db2->f("korting_id");
								$db3->query("UPDATE korting SET actief=1, onlinenaam='".addslashes($onlinenaam)."', volgorde='".intval($volgorde)."', omschrijving='".addslashes($omschrijving)."', delete_after_xml_korting=0, editdatetime=NOW() WHERE xml_korting=1 AND korting_id='".addslashes($db2->f("korting_id"))."';");
							} else {
								# Nieuwe korting aanmaken
								$db3->query("INSERT INTO korting SET actief=1, type_id='".addslashes($key4)."', volgorde='".intval($volgorde)."', seizoen_id='".addslashes($db->f("seizoen_id"))."', naam='".addslashes($korting_internenaam)."', onlinenaam='".addslashes($onlinenaam)."', omschrijving='".addslashes($omschrijving)."', van=FROM_UNIXTIME(".mktime(0,0,0,date("m"),date("d")-1,date("Y"))."), tot=FROM_UNIXTIME(".$seizoen_eind[$db->f("seizoen_id")]."), toonexactekorting=1, aanbiedingskleur=1, toon_abpagina=1, xml_korting=1, adddatetime=NOW(), editdatetime=NOW();");
								$kortingid=$db3->insert_id();
							}

							if($kortingid) {

								# oude tarieven wissen
								$db2->query("DELETE FROM korting_tarief WHERE korting_id='".addslashes($kortingid)."';");

								# alle betreffende aankomstdata doorlopen
								if(is_array($korting_aankomstdata[$db->f("seizoen_id")])) {
									reset($korting_aankomstdata[$db->f("seizoen_id")]);
									while(list($key5,$value5)=each($korting_aankomstdata[$db->f("seizoen_id")])) {
										$db2->query("INSERT INTO korting_tarief SET korting_id='".addslashes($kortingid)."', week='".$key5."', inkoopkorting_percentage='".$percentage."', aanbieding_acc_percentage='".$percentage."', opgeslagen=NOW();");
									}
								}
							}
						}
					}
				}
			}
		}
		track_time("lastminute ".$key." verwerkt");

	}

	# xml-kortingen die niet meer actief zijn wissen
	$db->query("DELETE FROM korting_tarief WHERE korting_id IN (SELECT korting_id FROM korting WHERE delete_after_xml_korting=1 AND xml_korting=1);");
	$db->query("DELETE FROM korting WHERE delete_after_xml_korting=1 AND xml_korting=1;");
	$db->query("UPDATE korting SET delete_after_xml_korting=0;");

	# Tarieven doorrekenen na wijzigen kortingen
	if($typeid_berekenen_inquery) {
		$typeid_berekenen_inquery=substr($typeid_berekenen_inquery,1);
		include("tarieven_berekenen.php");
	}
}

# Laatste importmoment opslaan in xml_laatsteimport
if($xml_laatsteimport) {
	unset($inquery);
	while(list($key,$value)=each($xml_laatsteimport)) {
		if($inquery) $inquery.=",".$key; else $inquery=$key;
	}
	if($inquery) {
		$db->query("UPDATE type SET xml_laatsteimport=NOW() WHERE type_id IN (".addslashes($inquery).");");
#		echo $db->lastquery."<br>";
	}
}

if($xml_laatstewijziging) {
	unset($inquery);
	while(list($key,$value)=each($xml_laatstewijziging)) {
		if($inquery) $inquery.=",".$key; else $inquery=$key;
	}
	if($inquery) {
		$db->query("UPDATE type SET xml_laatstewijziging=NOW() WHERE type_id IN (".addslashes($inquery).");");
#		echo $db->lastquery."<br>";
	}
}

if($xml_laatsteimport_leverancier) {
	unset($inquery);
	while(list($key,$value)=each($xml_laatsteimport_leverancier)) {
		if($key) {
			if($inquery) $inquery.=",".$key; else $inquery=$key;
		}
	}
	if($inquery) {
		$db->query("UPDATE leverancier SET xml_laatsteimport=NOW() WHERE xml_type IN (".addslashes($inquery).");");
#		echo $db->lastquery."<br>";
	}
}

if($mailtxt or $tarievenbijgewerkt) {
	# Tekst gewijzigde beschikbaarheid
	while(list($key,$value)=@each($mailtxt)) {
		if(!$xmltype_gehad[$key]) {
			if(ereg("^([0-9]+)_([0-9]+)$",$key,$regs)) {
				$sendmailtxt1.="&nbsp;<br><b>".wt_he($vars["xml_type"][$regs[1]])." - ".($regs[2]==2 ? "zomer" : "winter")."</b><br>\n";
				$xmltype_gehad[$key]=true;
			}
		}
		$sendmailtxt1.=$value;
	}

	# Tekst gewijzigde tarieven
	while(list($key,$value)=@each($tarievenbijgewerkt)) {
		while(list($key2,$value2)=each($value)) {
			while(list($key3,$value3)=each($value2)) {
				unset($seizoenid);
				if($tarievenbijgewerkt_seizoen[$key2][$key3]) {
					$seizoenid=$tarievenbijgewerkt_seizoen[$key2][$key3];
				} else {
					# Seizoen bepalen
					if($seizoenen[$wzt[$key2]][$key3]) {
						$seizoenid=$seizoenen[$wzt[$key2]][$key3];
					}
				}
				if($seizoenid and !$tarievenbijgewerkt_getoond[$seizoenid."_".$key2]) {
					# Leverancier tonen
					if(!$xmltypetarief_gehad[$key]) {
						if(ereg("^([0-9]+)_([0-9]+)$",$key,$regs)) {
							$sendmailtxt2.="&nbsp;<br><b>".wt_he($vars["xml_type"][$regs[1]])." - ".($regs[2]==2 ? "zomer" : "winter")."</b><br>\n";
							$xmltypetarief_gehad[$key]=true;
						}
					}
					# Link tonen
					$sendmailtxt2.=ereg_replace("_SEIZOEN_ID_",$seizoenid,$type_namen[$key2])."<br>\n";
					$tarievenbijgewerkt_getoond[$seizoenid."_".$key2]=true;
				}
			}
		}
	}

	if($sendmailtxt1 or $sendmailtxt2) {

		$mail=new wt_mail;
		$mail->fromname="Chalet.nl XML-systeem";
		$mail->from="system@chalet.nl";
		$mail->toname="Chalet.nl";
		$mail->to="info@chalet.nl";
		if($sendmailtxt1 and $sendmailtxt2) {
			$mail->subject="Beschikbaarheid gewijzigd + nieuwe tarieven";
		} elseif($sendmailtxt1) {
			$mail->subject="Beschikbaarheid gewijzigd";
		} elseif($sendmailtxt2) {
			$mail->subject="Nieuwe tarieven";
		}

		if($sendmailtxt1) {
			$mail->html="<b><i>Bij de volgende accommodatie(s) is op basis van XML-gegevens de beschikbaarheid gewijzigd:</i></b><br>".$sendmailtxt1."<p>";
		}
		if($sendmailtxt2) {
			if($sendmailtxt1) {
				$mail->html.="<hr>";
			}
			$mail->html.="<b><i>Bij de volgende accommodatie(s) zijn nieuwe tarieven bekend (moeten nog worden goedgekeurd):</i></b><br>".$sendmailtxt2."<p>";
		}

		# Ook naar systemlog mailen
		$mail->bcc="chaletmailbackup+systemlog@gmail.com";

		$mail->send();

		if($testsysteem) {
			echo "\n\n---------------------------------\n\nXML-import - naar info@chalet.nl gemaild:\n\n".$mail->html;
		} else {
			echo "\nXML-import - naar info@chalet.nl gemaild\n\n";
		}
	}
}

if(!$testsysteem) {
	# Temp-gegevens wissen
	$db->query("DELETE FROM xml_import_flex_temp;");

	while(list($key,$value)=@each($temp_filename)) {
		unlink($value);
	}
	echo "\n\nTrack time:\n".$track_time_text;
} else {
	echo "\n\n---------------------------------\n\nTrack time:\n".$track_time_text;

	echo "\n\nKlaar.\n";
	echo "</pre>\n\n";
}

function wt_dump_with_unixtime($array,$html=true) {
	ob_start();
	if(is_array($array)) {
		print_r($array);
	} else {
		echo "Geen array: ".$array;
	}

	$return=ob_get_contents();
	ob_end_clean();
	if($return) {
		$aanpassen=$return;
		while(ereg("([0-9]{10,})",$aanpassen,$regs)) {
			if($regs[1]) {
				$aanpassen=ereg_replace($regs[1],date("d-m-Y",$regs[1]),$aanpassen);
			}
		}
		$return=$aanpassen;
	}
	if($html) {
		$return="<hr><PRE>".nl2br(wt_he($return))."</PRE><hr>";
	}
	return $return;
}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	echo "0: ".wt_dump($db0->querycounter)."<br/>";
	echo "1: ".wt_dump($db->querycounter)."<br/>";
	echo "2: ".wt_dump($db2->querycounter)."<br/>";
	echo "3: ".wt_dump($db3->querycounter)."<br/>";
	echo "4: ".wt_dump($db4->querycounter)."<br/>";
}

echo "\n\nFinish: ".date("r")."\n";

?>