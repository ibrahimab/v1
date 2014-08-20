<?php

//
// JSON-communicatie met de database voor het Chalet.nl-CMS
//


set_time_limit( 30 );
ignore_user_abort( false );

$mustlogin=false;
$geen_tracker_cookie=true;

include "admin/vars.php";

wt_session_start();

if($vars["lokale_testserver"]) {
#	wt_mail( "jeroen@webtastic.nl", "rpc_json.php", "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] );
}

if ( $_GET["test"] ) {

} else {
	header( 'Cache-Control: no-cache, must-revalidate' );
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Content-type: application/json; charset=UTF-8' );
}

if ( $_GET["t"]==1 ) {

	//
	// Op Google Maps accommodaties plaatsen
	//
	$db->query( "SELECT accommodatie_id, tnaam".$vars["ttv"]." AS tnaam, COUNT(type_id) AS aantal FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' GROUP BY accommodatie_id;" );
	while ( $db->next_record() ) {
		if ( $db->f( "aantal" )==1 ) {
			$typenaam[$db->f( "accommodatie_id" )]=$db->f( "tnaam" );
		}
		$typenaam_alle_acc[$db->f( "accommodatie_id" )]=$db->f( "tnaam" );
	}

	// $db->query("SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen FROM view_accommodatie WHERE gps_lat<='".addslashes($_GET["lat1"])."' AND gps_lat>='".addslashes($_GET["lat2"])."' AND gps_long<='".addslashes($_GET["long1"])."' AND gps_long>='".addslashes($_GET["long2"])."' AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' AND accommodatie_id<>'".addslashes($_GET["accid"])."' GROUP BY accommodatie_id ORDER BY accommodatie_id;");
	// $db->query("SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen FROM view_accommodatie WHERE gps_lat<=".addslashes($_GET["lat1"])." AND gps_lat>=".addslashes($_GET["lat2"])." AND gps_long<=".addslashes($_GET["long1"])." AND gps_long>=".addslashes($_GET["long2"])." AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' AND accommodatie_id<>'".addslashes($_GET["accid"])."' GROUP BY accommodatie_id ORDER BY accommodatie_id;");
	// $db->query("SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, tgps_lat, tgps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen FROM view_accommodatie WHERE ((gps_lat IS NOT NULL AND gps_long IS NOT NULL) OR ((tgps_lat IS NOT NULL AND tgps_long IS NOT NULL))) AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' GROUP BY accommodatie_id ORDER BY accommodatie_id;");

	$query["types"]="SELECT accommodatie_id, toonper, soortaccommodatie, skigebied_id, akorteomschrijving".$vars["ttv"]." AS akorteomschrijving, tkorteomschrijving".$vars["ttv"]." AS tkorteomschrijving, akwaliteit, tkwaliteit, view_accommodatie.type_id, gps_lat, gps_long, tgps_lat, tgps_long, naam, plaats, skigebied, land, begincode, optimaalaantalpersonen, maxaantalpersonen, cache_vanafprijs_type.prijs FROM view_accommodatie RIGHT JOIN `cache_vanafprijs_type` ON view_accommodatie.type_id=cache_vanafprijs_type.type_id WHERE tgps_lat IS NOT NULL AND tgps_long IS NOT NULL AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' ORDER BY accommodatie_id;";
	$query["accommodaties"]="SELECT accommodatie_id, toonper, soortaccommodatie, skigebied_id, akorteomschrijving".$vars["ttv"]." AS akorteomschrijving, tkorteomschrijving".$vars["ttv"]." AS tkorteomschrijving, akwaliteit, tkwaliteit, view_accommodatie.type_id, gps_lat, gps_long, tgps_lat, tgps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen, cache_vanafprijs_type.prijs FROM view_accommodatie RIGHT JOIN `cache_vanafprijs_type` ON view_accommodatie.type_id=cache_vanafprijs_type.type_id WHERE gps_lat IS NOT NULL AND gps_long IS NOT NULL AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' GROUP BY accommodatie_id ORDER BY accommodatie_id, type_id;";

	while ( list( $key, $value )=each( $query ) ) {
		$db->query( $value );
		//  wt_jabber("boschman@gmail.com",$db->lastquery);
		$return["ok"]=true;
		if ( $db->num_rows() ) {
			while ( $db->next_record() ) {
				if ( $key=="types" ) {
					$gps_lat=$db->f( "tgps_lat" );
					$gps_long=$db->f( "tgps_long" );
				} else {
					$gps_lat=$db->f( "gps_lat" );
					$gps_long=$db->f( "gps_long" );
				}
				if ( !$algehad[$gps_lat."_".$gps_long] ) {
					$return["aantal"]++;
					if ( $key=="types" ) {
						// gps-gegevens op type-niveau
						if ( $db->f( "type_id" )<>$_GET["typeid"] ) {
							$return["acc"][$db->f( "type_id" )][1]=$gps_lat;
							$return["acc"][$db->f( "type_id" )][2]=$gps_long;
							$return["acc"][$db->f( "type_id" )]["naam"]=utf8_encode( ucfirst( $vars["soortaccommodatie"][$db->f( "soortaccommodatie" )] )." ".$db->f( "naam" )." ".$typenaam_alle_acc[$db->f( "accommodatie_id" )] );
							$return["acc"][$db->f( "type_id" )]["naamhtml"]=wt_he( ucfirst( $vars["soortaccommodatie"][$db->f( "soortaccommodatie" )] )." ".$db->f( "naam" )." ".$typenaam_alle_acc[$db->f( "accommodatie_id" )] );
							if ( file_exists( "pic/cms/types_specifiek/".$db->f( "type_id" ).".jpg" ) ) {
								$return["acc"][$db->f( "type_id" )]["afbeelding"]=imageurl( "types_specifiek/".$db->f( "type_id" ).".jpg", 170, 127 );
							} elseif ( file_exists( "pic/cms/accommodaties/".$db->f( "accommodatie_id" ).".jpg" ) ) {
								$return["acc"][$db->f( "type_id" )]["afbeelding"]=imageurl( "accommodaties/".$db->f( "accommodatie_id" ).".jpg", 170, 127 );
							}
							$return["acc"][$db->f( "type_id" )]["plaatsland"]=utf8_encode( wt_he( $db->f( "plaats" ).", ".$db->f( "skigebied" ) ) );
							$return["acc"][$db->f( "type_id" )]["aantalpersonen"]=utf8_encode( $db->f( "optimaalaantalpersonen" ).( $db->f( "maxaantalpersonen" )>$db->f( "optimaalaantalpersonen" ) ? " - ".$db->f( "maxaantalpersonen" ) : "" )." ".html( "personen" ) );
							$return["acc"][$db->f( "type_id" )]["url"]=utf8_encode( $vars["path"].txt( "menu_accommodatie" )."/".$db->f( "begincode" ).$db->f( "type_id" )."/" );
							$return["acc"][$db->f( "type_id" )]["skigebied_id"]=$db->f("skigebied_id");

							if($db->f("tkwaliteit") && $db->f("tkwaliteit")!=0) {
								$return["acc"][$db->f( "type_id" )]["kwaliteit"]=(int)$db->f("tkwaliteit");
							} elseif($db->f("akwaliteit") && $db->f("akwaliteit")!=0) {
								$return["acc"][$db->f( "type_id" )]["kwaliteit"]=(int)$db->f("akwaliteit");
							}

							if($db->f("tkorteomschrijving") && $db->f("tkorteomschrijving")!='') {
								$return["acc"][$db->f( "type_id" )]["omschrijving"]=utf8_encode( wt_he($db->f("tkorteomschrijving")) );
							} elseif($db->f("akorteomschrijving") && $db->f("akorteomschrijving")!=null) {
								$return["acc"][$db->f( "type_id" )]["omschrijving"]=utf8_encode( wt_he($db->f("akorteomschrijving")) );
							}

							$prijs=$db->f("prijs");
							if($prijs && $prijs!=0) {
								$return["acc"][$db->f( "type_id" )]["tarief"]=html("vanaf")." &euro;&nbsp;".number_format($prijs,0,",",".");
								if($db->f("toonper")==3 or $vars["wederverkoop"]) {
									$return["acc"][$db->f( "type_id" )]["tarief"].= utf8_encode(" ".html("peraccommodatie","zoek-en-boek"));
								} else {
									$return["acc"][$db->f( "type_id" )]["tarief"].= utf8_encode(" ".html("perpersoon","zoek-en-boek")."<br>".html("inclusiefskipas","zoek-en-boek"));
								}
							}
						}
					} else {
						// gps-gegevens op accommodatie-niveau
						if ( $db->f( "accommodatie_id" )<>$_GET["accid"] ) {
							$return["acc"][$db->f( "type_id" )][1]=$gps_lat;
							$return["acc"][$db->f( "type_id" )][2]=$gps_long;
							$return["acc"][$db->f( "type_id" )]["naam"]=utf8_encode( ucfirst( $vars["soortaccommodatie"][$db->f( "soortaccommodatie" )] )." ".$db->f( "naam" ).( $typenaam[$db->f( "accommodatie_id" )] ? " ".$typenaam[$db->f( "accommodatie_id" )] : "" ) );
							$return["acc"][$db->f( "type_id" )]["naamhtml"]=wt_he( ucfirst( $vars["soortaccommodatie"][$db->f( "soortaccommodatie" )] )." ".$db->f( "naam" ).( $typenaam[$db->f( "accommodatie_id" )] ? " ".$typenaam[$db->f( "accommodatie_id" )] : "" ) );
							if ( file_exists( "pic/cms/accommodaties/".$db->f( "accommodatie_id" ).".jpg" ) ) {
								$return["acc"][$db->f( "type_id" )]["afbeelding"]=imageurl( "accommodaties/".$db->f( "accommodatie_id" ).".jpg", 170, 127 );
							}
							$return["acc"][$db->f( "type_id" )]["plaatsland"]=utf8_encode( wt_he( $db->f( "plaats" ).", ".$db->f( "skigebied" ) ) );
							$return["acc"][$db->f( "type_id" )]["aantalpersonen"]=utf8_encode( $db->f( "optimaalaantalpersonen" ).( $db->f( "maxaantalpersonen" )>$db->f( "optimaalaantalpersonen" ) ? " - ".$db->f( "maxaantalpersonen" ) : "" )." ".html( "personen" ) );
							$return["acc"][$db->f( "type_id" )]["url"]=utf8_encode( $vars["path"].txt( "menu_accommodatie" )."/".$db->f( "begincode" ).$db->f( "type_id" )."/" );
							$return["acc"][$db->f( "type_id" )]["skigebied_id"]=$db->f("skigebied_id");

							if($db->f("akwaliteit") && $db->f("akwaliteit")!=0) {
								$return["acc"][$db->f( "type_id" )]["kwaliteit"]=(int)$db->f("akwaliteit");
							} elseif($db->f("tkwaliteit") && $db->f("tkwaliteit")!=0) {
								$return["acc"][$db->f( "type_id" )]["kwaliteit"]=(int)$db->f("tkwaliteit");
							}

							if($db->f("tkorteomschrijving") && $db->f("tkorteomschrijving")!='') {
								$return["acc"][$db->f( "type_id" )]["omschrijving"]=utf8_encode( wt_he($db->f("tkorteomschrijving")) );
							} elseif($db->f("akorteomschrijving") && $db->f("akorteomschrijving")!='') {
								$return["acc"][$db->f( "type_id" )]["omschrijving"]=utf8_encode( wt_he($db->f("akorteomschrijving")) );
							}

							$prijs=$db->f("prijs");
							if($prijs && $prijs!=0) {
								$return["acc"][$db->f( "type_id" )]["tarief"]=html("vanaf")." &euro;&nbsp;".number_format($prijs,0,",",".");

								if($db->f("toonper")==3 or $vars["wederverkoop"]) {
									$return["acc"][$db->f( "type_id" )]["tarief"].= utf8_encode(" ".html("peraccommodatie","zoek-en-boek"));
								} else {
									$return["acc"][$db->f( "type_id" )]["tarief"].= utf8_encode( " ".html("perpersoon","zoek-en-boek")."<br>".html("inclusiefskipas","zoek-en-boek"));
								}
							}
						}
					}

					$algehad[$gps_lat."_".$gps_long]=true;
				}
			}
		}
	}
} elseif ( $_GET["t"]==2 ) {
	//
	// Op Google Maps plaatsen plaatsen (Italissima)
	//

	// Aantal
	// $db->query("SELECT plaats_id, skigebied_id, naam, gps_lat, gps_long FROM plaats WHERE gps_lat IS NOT NULL AND gps_long IS NOT NULL AND wzt=2 AND websites LIKE '%".$vars["website"]."%' ORDER BY plaats_id;");

	unset( $andquery );
	if ( preg_match( "/MSIE [67]/", $_SERVER["HTTP_USER_AGENT"] ) ) {
		// Bij MSIE 6 en 7: alleen plaatsen uit de actieve regio tonen (vanwege de Javascript-traagheid van die browsers)
		$andquery.=" AND p.skigebied_id='".intval( $_GET["skigebiedid"] )."'";
	}

	$db->query( "SELECT count(t.type_id) AS aantal, p.plaats_id, p.skigebied_id, p.naam, p.gps_lat, p.gps_long, s.naam AS skigebied FROM plaats p, land l, type t, accommodatie a, skigebied s WHERE p.skigebied_id=s.skigebied_id AND a.weekendski=0 AND t.accommodatie_id=a.accommodatie_id AND t.tonen=1 AND a.tonen=1 AND t.websites LIKE '%".$vars["website"]."%' AND a.plaats_id=p.plaats_id AND l.land_id=p.land_id".$andquery." GROUP BY p.plaats_id;" );
	// wt_jabber("boschman@gmail.com",$db->lastquery);
	if ( $db->num_rows() ) {
		$return["ok"]=true;
		while ( $db->next_record() ) {
			$return["plaats"][$db->f( "plaats_id" )][1]=$db->f( "gps_lat" );
			$return["plaats"][$db->f( "plaats_id" )][2]=$db->f( "gps_long" );
			$return["plaats"][$db->f( "plaats_id" )]["naam"]=utf8_encode( $db->f( "naam" ) );
			$return["plaats"][$db->f( "plaats_id" )]["naamhtml"]=wt_he( $db->f( "naam" ) );
			if ( $db->f( "skigebied_id" )==$_GET["skigebiedid"] ) {
				$return["plaats"][$db->f( "plaats_id" )]["binnengebied"]=1;
			} else {
				$return["plaats"][$db->f( "plaats_id" )]["binnengebied"]=0;
			}
			$return["plaats"][$db->f( "plaats_id" )]["aantalacc"]="<a href=\"".$vars["path"]."plaats/".wt_convert2url_seo( $db->f( "naam" ) )."/\" onclick=\"return map_regio_click(this);\">".$db->f( "aantal" )." ".( $db->f( "aantal" )==1 ? "vakantiehuis" : "vakantiehuizen" )."</a>";
			$return["plaats"][$db->f( "plaats_id" )]["skigebied"]=wt_he( $db->f( "skigebied" ) );
			$return["aantal"]++;
		}
	}
} elseif ( $_GET["t"]==3 ) {
	//
	// Autocomplete zoekformulier
	//
	require $unixdir."admin/class.search.php";

	$search=new search;
	$search->settings["delete_ignorewords"]=false;
	$search->settings["only_whole_words"]=false;
	$search->wordsplit( $_GET["q"] );

	if($vars["websitetype"]==6) {
		# Vallandry: ook typenaam doorzoeken
		$andquery1=$search->regexpquery( array( "naam", "tnaam" ) );
	} else {
		# andere sites: alleen accommodatienaam doorzoeken
		$andquery1=$search->regexpquery( array( "naam") );
	}
#	$andquery2=$search->regexpquery( array( "plaats", "plaats_altnaam" ) );
#	$andquery3=$search->regexpquery( array( "skigebied", "skigebied_altnaam" ) );
	$andquery4=$search->regexpquery( array( "woord" ) );

	if ( $andquery1 ) {
		if($vars["websitetype"]==6) {
			# Vallandry: ook typenaam doorzoeken
			$query[1]="SELECT naam AS result, tnaam AS result2 FROM view_accommodatie WHERE (".$andquery1.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%';";
		} else {
			$query[1]="SELECT naam AS result FROM view_accommodatie WHERE (".$andquery1.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%';";
		}
#		$query[2]="SELECT plaats AS result FROM view_accommodatie WHERE (".$andquery2.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%';";
#		$query[3]="SELECT skigebied AS result FROM view_accommodatie WHERE (".$andquery3.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%';";
		$query[4]="SELECT woord AS result FROM woord_autocomplete WHERE wzt='".$vars["seizoentype"]."' AND taal='".$vars["taal"]."' AND (".$andquery4.");";

		while ( list( $key, $value )=each( $query ) ) {
			$db->query( $value );
			while ( $db->next_record() ) {
				if($db->f("result2")) {
					$results[$db->f( "result" )." ".$db->f( "result2" )]=true;
				} elseif ( $db->f( "result" ) ) {
					$results[$db->f( "result" )]=true;
				}
			}
		}
	}

	#
	# Tekst-verfijningen
	#
	function verfijning_tekst_opschonen($text,$search_query=false) {
		# haal accenten, streepjes, punten weg uit zoekopdracht t.b.v. een betere match

		global $vars;

		$text=wt_stripaccents($text);
		$text=preg_replace("/[-\.\/]/","",$text);
		$text=trim($text);
		$text=strtolower($text);

		# algemene woorden weglaten
		if($vars["taal"]=="en") {
			$ignore_words=array("on","at","the","with");
		} else {
			$ignore_words=array("met","bij","de");
		}
		while(list($key,$value)=each($ignore_words)) {
			$text=trim(preg_replace("/\b".$value."\b/","",$text));
		}
		$text=trim($text);

		return $text;
	}
	include($vars["unixdir"]."content/vars/autocomplete.php");
	$_GET["q"]=verfijning_tekst_opschonen($_GET["q"],true);
	if(strlen($_GET["q"])>0) {
		if ( is_array( $autocomplete ) ) {
			while ( list( $key, $value )=each( $autocomplete ) ) {
				if(strpos("-".verfijning_tekst_opschonen($key),$_GET["q"])!==false) {
					$results[$key]=true;
				}
			}
		}
	}

	$return["ok"]=true;
	if ( is_array( $results ) ) {

		setlocale( LC_COLLATE, "nl_NL.ISO8859-1" );
		ksort( $results, SORT_LOCALE_STRING );
		setlocale( LC_COLLATE, "C" );

		while ( list( $key, $value )=each( $results ) ) {
			if ( $key and $return["totalResultsCount"]<=10 ) {
				$return["totalResultsCount"]++;
				$return["results"][$return["totalResultsCount"]]["name"]=utf8_encode( $key );
			}
		}
	} else {
		$return["results"][1]["name"]="";
	}
} elseif ( $_GET["t"]==4 ) {
	//
	// Favorietenfunctie
	//
	if($_COOKIE["sch"]) {
		$sch_value = $_COOKIE["sch"];
	} elseif($_SESSION["sch_when_cookies_disabled"]) {
		$sch_value = $_SESSION["sch_when_cookies_disabled"];
	}

	if ( $_GET["action"]=="insert" ) {
		if($sch_value) {
			$db->query( "INSERT INTO bezoeker_favoriet(bezoeker_id, type_id, adddatetime, editdatetime) VALUES ('".addslashes( $sch_value )."','".addslashes( $_GET["typeid"] )."',NOW(), NOW());" );
		}
	} elseif ( $_GET["action"]=="delete" ) {
		$db->query( "DELETE FROM bezoeker_favoriet WHERE bezoeker_id='".addslashes( $sch_value )."' AND type_id='".addslashes( $_GET["typeid"] )."';" );
	}

	$db->query( "SELECT COUNT(b.type_id) AS aantal FROM bezoeker_favoriet b, view_accommodatie v WHERE b.bezoeker_id='".addslashes( $sch_value )."' AND b.type_id=v.type_id AND v.websites LIKE '%".$vars["website"]."%' AND v.atonen=1 AND v.ttonen=1 AND v.archief=0;" );
	if ( $db->next_record() ) {
		$return["aantal"]=$db->f( "aantal" );
	}

	if ( !$return["aantal"] ) $return["aantal"]=0;
	$return["ok"]=true;
} elseif ( $_GET["t"]==5 ) {
	//
	// NIET IN GEBRUIK
	//

} elseif ( $_GET["t"]==6 ) {
	//
	// NIET IN GEBRUIK
	//


} elseif ($_GET["t"]==7 ) {
	//
	// favorieten mailen
	//

	if ($_SESSION["captcha_okay"]) {

		# ReCAPTCHA maximaal 1x geldig
		unset($_SESSION["captcha_okay"]);

		$db->query("SELECT DISTINCT b.type_id FROM bezoeker_favoriet b, view_accommodatie v WHERE b.bezoeker_id='".addslashes($_COOKIE["sch"])."' AND b.type_id=v.type_id AND v.websites LIKE '%".$vars["website"]."%' AND v.atonen=1 AND v.ttonen=1 AND v.archief=0;");
		$klantfavs=array();
		while($db->next_record()){
			array_push($klantfavs,$db->f("type_id"));
		}

		if ( $vars["websitetype"]==1 or $vars["websitetype"]==4 ) {
			# Chalet-vormgeving
			$vars["balkkleur"]="#d5e1f9";
			$vars["backcolor"]="#d5e1f9";
			$vars["textColor"]="#003366";
			$vars["textColor_bericht"]="#003366";
			$vars["korte_omschrijving_kleur"]="#003366";
			//straks kijken moet een css knop worden
			$leesmeerKnopMail="text-decoration:none;background-color:#003366;display:inline-block;color:#ffffff;font-family:arial;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;";
		} elseif ( $vars["websitetype"]==3 ) {
			# Zomerhuisje
			$vars["balkkleur"]="#cfbcd8";
			$vars["backcolor"]="#eaeda9";
			$vars["textColor"]="#5f227b";
			$vars["textColor_bericht"]="#5f227b";
			$vars["korte_omschrijving_kleur"]="#5f227b";
			$leesmeerKnopMail="background-color:#cbd328;display:inline-block;color:#5f227b;font-family:arial;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;text-decoration:none;";
		} elseif ( $vars["websitetype"]==7 ) {
			# Italissima
			$vars["balkkleur"]="#ffd38f";
			$vars["backcolor"]="#ffffff";
			$vars["textColor"]="#661700";
			$vars["textColor_bericht"]="#661700";
			$vars["korte_omschrijving_kleur"]="#661700";
			$leesmeerKnopMail="background-color:#ff9900;display:inline-block;color:#ffffff;font-family:verdana;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;text-decoration:none;";
		} elseif ( $vars["websitetype"]==8 ) {
			# SuperSki
			$vars["balkkleur"]="#003366";
			$vars["backcolor"]="#ffffff";
			$vars["textColor"]="#ffffff";
			$vars["textColor_bericht"]="#660066";
			$vars["korte_omschrijving_kleur"]="#660066";
			$vars["linkkleur"]="#003366";
			$leesmeerKnopMail="background-color:#e6007e;display:inline-block;color:#ffffff;font-family:verdana;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;text-decoration:none;";
		}

		unset($mail_content);
		$mail_content.="<html>";
		$mail_content.="<head>";
		$mail_content.="</head>";
		$mail_content.="<body>";
		$mail_content.="<div style=\"width:681px; position:absolute; left:10%;\"><!-- kopiemelding -->";
		$mail_content.="<table style=\"font-family:Verdana, Arial, Helvetica, sans-serif;color:#003366;\" border=\"0\" width=\"681\"><tr><td>";

		if(file_exists($vars["unixtime"]."pic/mailheader/".$vars["website"].".jpg")) {
			$topfoto_size=getimagesize($vars["unixtime"]."pic/mailheader/".$vars["website"].".jpg");
		}

		$mail_content.="<a href=\"".$vars["basehref"]."?utm_source=Favorietenfunctie&utm_medium=Favorietenfunctie&utm_campaign=Favorietenfunctie\"><img src=\"".wt_he($vars["basehref"]."pic/mailheader/".$vars["website"].".jpg")."\" border=\"0\" ".$topfoto_size[3]."></a></td></tr>";
		$bericht=$_GET["bericht"];
		$bericht=trim($_GET["bericht"]);
		$bericht=utf8_decode($bericht);
		$bericht=wt_he($bericht);

		# sitenaam klikbaar maken
		$link_naar_site="<a href=\"".$vars["basehref"]."?utm_source=Favorietenfunctie&utm_medium=Favorietenfunctie&utm_campaign=Favorietenfunctie\" style=\"".($vars["linkkleur"] ? "color:".$vars["linkkleur"].";" : "")."\">".wt_he($vars["websitenaam"])."</a>";
		$bericht=preg_replace("/\b".str_replace("\.","\.",$vars["websitenaam"])."\b/",$link_naar_site,$bericht);

		$mail_content.="<tr><td style=\"text-align:center; color:".$vars["textColor_bericht"].";font-size:14px;\"><br/><br/>".nl2br($bericht)."<br/><br/><br/>";
		$mail_content.="</td></tr>";
		$mail_content.="</table>";

		$db->query("SELECT b.type_id, a.accommodatie_id, a.kwaliteit AS akwaliteit, t.kwaliteit AS tkwaliteit, a.korteomschrijving".$vars["ttv"]." AS akorteomschrijving, t.korteomschrijving".$vars["ttv"]." AS tkorteomschrijving, t.optimaalaantalpersonen, t.maxaantalpersonen, t.slaapkamers,t.badkamers, a.naam, a.wzt, t.naam".$vars["ttv"]." AS tnaam, a.soortaccommodatie, p.naam AS plaats, l.begincode, l.naam".$vars["ttv"]." AS land, s.naam AS skigebied FROM bezoeker_favoriet b, type t, accommodatie a, plaats p, skigebied s, land l WHERE t.accommodatie_id=a.accommodatie_id AND b.type_id=t.type_id AND a.plaats_id=p.plaats_id AND p.skigebied_id=s.skigebied_id AND p.land_id=l.land_id AND t.websites LIKE '%".$vars["website"]."%' AND a.tonen=1 AND t.tonen=1 AND a.archief=0 AND b.bezoeker_id='".addslashes($_COOKIE["sch"])."';");

		while($db->next_record()) {
			$accid=$db->f("accommodatie_id");
			if(file_exists("pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
				$afbeelding="types_specifiek/".$db->f("type_id");
			} elseif(file_exists("pic/cms/accommodaties/".$accid.".jpg")) {
				$afbeelding="accommodaties/".$accid;
			} else {
				$afbeelding="accommodaties/0";
			}
			$mail_content.="<table cellspacing=\"0\" cellpadding=\"0\" width=\"681\" style=\"font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px;\">";
			$mail_content.="<tr><td colspan=\"5\" align=\"left\">";
			$mail_content.="<div style=\"background-color: white; width: 681px;border:1px solid ".$vars["balkkleur"].";\">";
			$mail_content.="<div style=\"background-color:".$vars["balkkleur"].";padding: 5px;\">";
			$mail_content.="<div style=\"color:".$vars["textColor"].";font-size: 1.2em;font-weight: bold; font-family:Verdana, Arial, Helvetica, sans-serif;\">";
			$mail_content.=wt_he(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : ""));
			$mail_content.="</div>";
			$mail_content.="</div>";

			$mail_content.="<table style=\"display:inline-table;\" font-family:Verdana, Arial, Helvetica, sans-serif;\" text-align=\"left\" border=\"0\" width=\"100%\">";
			$mail_content.="<tr><td valign=\"top\" rowspan=\"8\"><img style=\"padding-right:5px;\" src=\"".$vars["basehref"]."pic/cms/".$afbeelding.".jpg\" width=\"200\" height=\"150\" border=\"0\"></td>";
			$mail_content.="<td valign=\"top\" colspan=\"2\" style=\"font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;\">".$db->f("land")."</td></tr><tr>";
			$mail_content.="<td valign=\"top\" colspan=\"2\" style=\"font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px\">".$db->f("plaats")."</td></tr><tr>";
			$mail_content.="<td valign=\"top\" colspan=\"2\" style=\"font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px\">".$db->f("skigebied")."</td></tr><tr>";
			$mail_content.="<td colspan=\"2\">";

			if($db->f("akwaliteit") or $db->f("tkwaliteit")) {
				if($db->f("tkwaliteit")) {
					$kwaliteit=$db->f("tkwaliteit");
				} else {
					$kwaliteit=$db->f("akwaliteit");
				}
				for($i=0; $i<$kwaliteit;$i++) {
					$mail_content.="<img src=\"".$vars["basehref"]."pic/ster_".$vars["websitetype"].".png\">";
				}
			} else {
				$mail_content.="&nbsp;";
			}
			$mail_content.="</td></tr>";

			unset($korteomschrijving);
			if($db->f("akorteomschrijving") or $db->f("tkorteomschrijving")) {
				if($db->f("tkorteomschrijving")) {
					$korteomschrijving=$db->f("tkorteomschrijving");
				} else {
					$korteomschrijving=$db->f("akorteomschrijving");
				}
			}

			$mail_content.="<tr><td valign=\"top\" width=\"100%\" colspan=\"2\" style=\"font-family:Verdana, Arial, Helvetica, sans-serif; color:".$vars["korte_omschrijving_kleur"].";font-size:12px;\"><i>".wt_he($korteomschrijving)."</i></td></tr>";
			if($db->f("optimaalaantalpersonen")==$db->f("maxaantalpersonen")) {
				$rev=$db->f("optimaalaantalpersonen");
			} else {
				$rev=$db->f("optimaalaantalpersonen")."-".$db->f("maxaantalpersonen");
			}
			$mail_content.="<tr><td valign=\"top\" colspan=\"2\" style=\"font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px;\">".$rev." ".html("personen")."</td></tr>";
			$mail_content.="<tr><td valign=\"top\" style=\"font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px\">".$db->f("slaapkamers")." ".($db->f("slaapkamers")==1 ? html("slaapkamer") : html("slaapkamers"))."</td><td valign=\"top\" rowspan=\"2\" align=\"right\"><a style=\"".$leesmeerKnopMail."text-decoration:none;\" href=\"".wt_he($vars["basehref"]."accommodatie/".$db->f("begincode").$db->f("type_id")."/?utm_source=Favorietenfunctie&utm_medium=Favorietenfunctie&utm_campaign=Favorietenfunctie")."\">".html("buttonLeesmeer","favorieten")."</a></td></tr>";
			$mail_content.="<tr><td style=\"font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px\">".$db->f("badkamers")." ".($db->f("badkamers")==1 ? html("badkamer") : html("badkamers"))."</td></tr>";
			$mail_content.="</table>";
			$mail_content.= "<br />";
			$mail_content.="</div>";
			$mail_content.="</td></tr>";
			$mail_content.="</table>";
			$mail_content.="<br />";
		}
		$mail_content.="</div>";
		$mail_content.="</body>";
		$mail_content.="</html>";

		$emails = explode(" ",$_GET["EmailOntvanger"]);
		for($i = 0; $i<sizeof($emails); $i++) {

			$mail=new wt_mail;
			$mail->from=$_GET["verzenderAdres"];
			$mail->fromname=$_GET["verzenderAdres"];
			$mail->subject=html("mailonderwerp","favorieten");
			$mail->to=$emails[$i];

			$mail->plaintext=""; # deze leeg laten bij een opmaak-mailtje
			$mail->html_top="";
			$mail->html_bottom="";

			$mail->html=$mail_content;
			$mail->send();
		}

		if($_GET["kopie"]=="1") {
			#
			# Kopie naar afzender sturen
			#
			$mail=new wt_mail;
			$mail->from=$_GET["verzenderAdres"];
			$mail->fromname=$_GET["verzenderAdres"];
			$mail->subject=html("mailonderwerp","favorieten");
			$mail->to=$_GET["verzenderAdres"];

			$mail->plaintext=""; # deze leeg laten bij een opmaak-mailtje
			$mail->html_top="";
			$mail->html_bottom="";

			# melding over kopie toevoegen
			$mail_content=str_replace("<!-- kopiemelding -->","<table style=\"font-family:Verdana, Arial, Helvetica, sans-serif;color:#003366;font-size:14px;\" border=\"0\" width=\"681\"><tr><td><i>".html("kopie", "favorieten")." ".wt_he($vars["websitenaam"])."</i><br/><br><br></td></tr><tr><td>",$mail_content);

			$mail->html=$mail_content;
			$mail->send();
		}
	}

} elseif($_GET["t"]==8) {

	#	Ideal betalingssysteem Sisow

	#het ophalen van een lijst met alle aangesloten banken.
	if($_GET["action"]=="getBanks"){
		//header('Content-type: application/xml');
		$bankenArr=array();
		$daurl = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/DirectoryRequest?test=true';
		$handle = fopen($daurl, "r");
		$xml="";
		if ($handle) {
			while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
				$xml.= $buffer;
			}
			fclose($handle);
		}
		$xmle=simplexml_load_string($xml);
		//print_r($xmle);
		$i=0;
		foreach($xmle->directory->issuer as $bank){
			$banknaam["id"]=(string)$bank->issuerid;
			$banknaam["naam"]=(string)$bank->issuername;
			$bankenArr[$i]=$banknaam;
			$i++;
		}
		$return["banken"]=$bankenArr;
	} elseif($_GET["action"]=="doTransaction") {
	#Verzoek tot het uitvoeren van een transactie.
		$signature=sha1($_GET["kenmerk"]."uniqueentrance".$_GET["bedrag"]."25374805605a3f9de17a1ac057e637aeaba1afedd2070d75ba");
		$daurl = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/TransactionRequest?shopid=&merchantid=2537480560&payment=&purchaseid='.$_GET["kenmerk"].'&amount='.$_GET["bedrag"].'&entrancecode=uniqueentrance&description=IdealAfbetaling&issuerid='.$_GET["bankID"].'&returnurl=http://192.168.1.32/chalet/checkout.php?success=true&cancelurl=http://192.168.1.32/chalet/checkout.php?canceled=true&callbackurl=http://192.168.1.32/chalet/checkout.php?callback=true&sha1='.$signature.'&testmode=true';
		$handle = fopen($daurl, "r");
		$xml="";
		if ($handle) {
			while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
				$xml.= $buffer;
			}
			fclose($handle);
		}
		$xmle=simplexml_load_string($xml);
		$return["transaction"]["trxid"]=(string)$xmle->transaction->trxid;
		$return["transaction"]["issuerurl"]=(string)$xmle->transaction->issuerurl;
		//print_r($xmle);
	} elseif($_GET["action"]=="getstatus") {
	#het opvragen van de status van een transactie zodat nagegaan kan owrden of de bataling goed gegaan is of niet
		$signature=sha1($_GET["trxid"]."25374805605a3f9de17a1ac057e637aeaba1afedd2070d75ba");
		$daurl = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/StatusRequest?trxid='.$_GET["trxid"].'&shopid=&merchantid=2537480560&sha1='.$signature;
		$handle = fopen($daurl, "r");
		$xml="";
		if ($handle) {
			while (!feof($handle)) {
				$buffer = fgets($handle, 4096);
				$xml.= $buffer;
			}
			fclose($handle);
		}
		$xmle=simplexml_load_string($xml);
		$return["transaction"]["trxid"]=(string)$xmle->transaction->trxid;
		$return["transaction"]["status"]=(string)$xmle->transaction->status;
		$return["transaction"]["amount"]=(string)$xmle->transaction->amount;
		$return["transaction"]["purchaseid"]=(string)$xmle->transaction->purchaseid;
		$return["transaction"]["description"]=(string)$xmle->transaction->description;
		$return["transaction"]["timestamp"]=(string)$xmle->transaction->timestamp;
		$return["transaction"]["consumername"]=(string)$xmle->transaction->consumername;
		$return["transaction"]["consumeraccount"]=(string)$xmle->transaction->consumeraccount;
		$return["transaction"]["consumercity"]=(string)$xmle->transaction->consumercity;
		//print_r($xmle);
	}
} elseif($_GET["t"]==9) {
	// t=9 is no longer in use (available for new rpc_json-request)


} elseif($_GET["t"]==10) {
	#
	# CAPTCHA-systeem: input controleren
	# Op basis van afbeelding pic/captcha_image.php
	#
	$_SESSION["captcha_okay"]=false;

	if($_GET["input"]) {
		$_GET["input"]=strtoupper($_GET["input"]);
		if($_GET["input"]==$_SESSION["captcha_random_number"]) {
			$_SESSION["captcha_okay"]=true;
			$return["captcha_okay"]=true;
		}
		$return["input"]=$_SESSION["captcha_random_number"]."==".$_GET["input"];
	}
} elseif($_GET["t"]==11) {
	#
	# Controleren invoer formulier accommodatiemail
	#
	if($_GET["name"]=="from") {
		if(wt_validmail($_GET["input"])) {
			$return["field_okay"]=true;
		} elseif($_GET["input"]) {
			$return["foutmelding"]=html("onjuistmailadres","accommodatiemail");
		} else {
			$return["foutmelding"]=html("verplichtveld","accommodatiemail");
		}
	} elseif($_GET["name"]=="to") {
		$mailadressen=preg_split("/,/",trim($_GET["input"]));
		if(is_array($mailadressen)) {
			while(list($key,$value)=each($mailadressen)) {
				if(!wt_validmail($value)) {
					$return["foutmelding"].=", ".wt_he($value);
				}
			}
			if($_GET["input"]) {
				if($return["foutmelding"]) {
					if(count($mailadressen)==1) {
						$return["foutmelding"]=html("onjuistmailadres","accommodatiemail");
					} else {
						$return["foutmelding"]=html("onjuistmailadres","accommodatiemail").": ".substr($return["foutmelding"],1);
					}
				} else {
					$return["field_okay"]=true;
				}
			} else {
				$return["foutmelding"]=html("verplichtveld","accommodatiemail");
			}
		} else {
			$return["foutmelding"]=html("verplichtveld","accommodatiemail");
		}
	} elseif($_GET["name"]=="message") {
		if($_GET["input"]) {
			$return["field_okay"]=true;
		} else {
			$return["foutmelding"]=html("verplichtveld","accommodatiemail");
		}
	}

} elseif($_GET["t"]=="newpricesmail") {
	if($_GET["seizoen_id"] and $_GET["type_id"] and $_GET["email"]) {
		$db->query("INSERT INTO newpricesmail SET seizoen_id='".intval($_GET["seizoen_id"])."', type_id='".intval($_GET["type_id"])."', website='".addslashes($vars["website"])."', email='".addslashes($_GET["email"])."', adddatetime=NOW();");

		$db->query("SELECT newpricesmail_id FROM newpricesmail WHERE seizoen_id='".intval($_GET["seizoen_id"])."' AND type_id='".intval($_GET["type_id"])."' AND email='".addslashes($_GET["email"])."';");
		if($db->next_record()) {
			$return["added"] = true;
		}
	}
	usleep(500000);
}

$return["ok"]=true;
echo json_encode( $return );

?>
