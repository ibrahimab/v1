<?php

include "admin/vars.php";

session_start();

if ( $vars["websitetype"]==1 ) {
	$vars["balkkleur"]="#d5e1f9";
	$vars["backcolor"]="#d5e1f9";
	$vars["textColor"]="#003366";
	$popup=$vars["basehref"]."pic/popBack.png";
	$vars["korteOmschrijvinkleur"]="#003366";
	$onderwerpText=html( "onderwerpTextChalet", "favorieten" );
	//straks kijken moet een css knop worden
	$knopLeesmeer="LeesMeerChalet";
	$vars["mail_topfoto"]=$vars["basehref"]."pic/topfoto/headerChalet.png";
	$doormailText=html( "doormailTextChalet", "favorieten" )." ".wt_he( $vars["websitenaam"] ).".";
	$leesmeerKnopMail="text-decoration:none;background-color:#003366;display:inline-block;color:#ffffff;font-family:arial;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;";
	if ( $vars["website"]=="E" ) {
		$vars["mail_topfoto"]=$vars["basehref"]."pic/topfoto/headerChaletEU.png";
	}
} elseif ( $vars["websitetype"]==3 or $vars["websitetype"]==7 ) {
	$onderwerpText="Mijn favoriete vakantiehuizen";
	$doormailText="Ik heb een aantal leuke vakantiehuizen gevonden op ".wt_he( $vars["websitenaam"] ). ". Dit moet je zien!";
	if ( $vars["websitetype"]==3 ) {
		$vars["balkkleur"]="#cfbcd8";
		$vars["backcolor"]="#eaeda9";
		$vars["textColor"]="#5f227b";
		$vars["korteOmschrijvinkleur"]="#5f227b";
		$knopLeesmeer="LessmeerZomerHuis";
		$vars["mail_topfoto"]=$vars["basehref"]."pic/topfoto/headerZomerhuisje.png";
		$popup=$vars["basehref"]."pic/popBackZomer.png";
		$leesmeerKnopMail="background-color:#cbd328;
	display:inline-block;
	color:#5f227b;
	font-family:arial;
	font-size:15px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	cursor:pointer;text-decoration:none;";

	} elseif ( $vars["websitetype"]==7 ) {
		$vars["balkkleur"]="#ffd38f";
		$vars["backcolor"]="#FFFFFF";
		$vars["textColor"]="#661700";
		$vars["korteOmschrijvinkleur"]="#661700";
		$vars["mail_topfoto"]=$vars["basehref"]."pic/topfoto/headerItalissima.png";
		$knopLeesmeer="LessmeerZomerItalissima";
		$popup=$vars["basehref"]."pic/popBackItal.png";
		$leesmeerKnopMail="background-color:#ff9900;
	display:inline-block;
	color:#ffffff;
	font-family:verdana;
	font-size:15px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	cursor:pointer;text-decoration:none;";
	}
}

include "content/opmaak.php";

?>