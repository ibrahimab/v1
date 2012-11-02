<?php

include "admin/vars.php";

session_start();

if ( $vars["websitetype"]==1 or $vars["websitetype"]==4 ) {
	$vars["balkkleur"]="#d5e1f9";
	$vars["backcolor"]="#d5e1f9";
	$vars["textColor"]="#003366";
	$vars["korte_omschrijving_kleur"]="#003366";
	//straks kijken moet een css knop worden
#	$leesmeerKnopMail="text-decoration:none;background-color:#003366;display:inline-block;color:#ffffff;font-family:arial;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;";
} elseif ( $vars["websitetype"]==3 ) {
	$vars["balkkleur"]="#cfbcd8";
	$vars["backcolor"]="#eaeda9";
	$vars["textColor"]="#5f227b";
	$vars["korte_omschrijving_kleur"]="#5f227b";
#	$leesmeerKnopMail="background-color:#cbd328;display:inline-block;color:#5f227b;font-family:arial;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;text-decoration:none;";
} elseif ( $vars["websitetype"]==7 ) {
	$vars["balkkleur"]="#ffd38f";
	$vars["backcolor"]="#FFFFFF";
	$vars["textColor"]="#661700";
	$vars["korte_omschrijving_kleur"]="#661700";
#	$leesmeerKnopMail="background-color:#ff9900;display:inline-block;color:#ffffff;font-family:verdana;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;text-decoration:none;";
} elseif ( $vars["websitetype"]==8 ) {
	# SuperSki
	$vars["balkkleur"]="#a1bddb";
	$vars["backcolor"]="#ffffff";
	$vars["textColor"]="#003366";
	$vars["korte_omschrijving_kleur"]="#660066";
	$leesmeerKnopMail="background-color:#e6007e;display:inline-block;color:#ffffff;font-family:verdana;font-size:15px;font-weight:bold;padding:6px 24px;text-decoration:none;cursor:pointer;text-decoration:none;";
}

include "content/opmaak.php";

?>