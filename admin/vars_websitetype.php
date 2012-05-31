<?php


#
#
# Bepalen welke website wordt opgevraagd
#
#

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $_SERVER["WINDIR"]<>"") {
	$vars["lokale_testserver"]=true;
}

#
# CMS-link
#
if($vars["lokale_testserver"]) {

	# Testsite bepalen indien niet bekend
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and !$vars["testsite"]) {
		$vars["cms_basehref"]="http://ss.postvak.net/chalet/";

		if(!$vars["testsite"]) {
			$vars["testsite"]=@file_get_contents("/home/webtastic/html/chalet/tmp/testsite.txt");
		}
	}
	
	# Testsite bepalen voor Miguel
	if($_SERVER["WINDIR"]<>"") {
		$vars["cms_basehref"]="http://localhost/chalet/";
		if(!$vars["testsite"]) {
			$vars["testsite"]=@file_get_contents($_SERVER["DOCUMENT_ROOT"]."chalet/tmp/testsite.txt");
		}
		if(!$vars["testsite"]) {
			$vars["testsite"]="chaletnl";
		}
	}
} else {
	$vars["cms_basehref"]="http://www.chalet.nl/";
}


#
# Websitetype en seizoentype bepalen
#
$_SERVER["HTTP_HOST"]=strtolower($_SERVER["HTTP_HOST"]);
if(substr($_SERVER["HTTP_HOST"],-3)==":80") $_SERVER["HTTP_HOST"]=substr($_SERVER["HTTP_HOST"],0,-3);
if(substr($_SERVER["HTTP_HOST"],-1)==".") $_SERVER["HTTP_HOST"]=substr($_SERVER["HTTP_HOST"],0,-1);
if($cron or $_SERVER["HTTP_HOST"]=="www.chalet.nl" or $_SERVER["HTTP_HOST"]=="www2.chalet.nl" or $_SERVER["HTTP_HOST"]=="www3.chalet.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="chaletnl")) {
	# Chalet.nl Winter
	$vars["websitetype"]=1;
	$vars["websitenaam"]="Chalet.nl";
	$vars["langewebsitenaam"]="Chalet.nl B.V.";
	$vars["seizoentype"]=1;
	$vars["website"]="C";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.chalet.nl/";
	$vars["email"]="info@chalet.nl";
	$basehref="http://www.chalet.nl/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-1";
	$vars["facebook_pageid"]="156825034385110";
	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
} elseif($_SERVER["HTTP_HOST"]=="www.wintersportaccommodaties.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="wsa")) {
	# Wintersportaccommodaties
	$vars["websitetype"]=2;
	$vars["websitenaam"]="Wintersportaccommodaties.nl";
	$vars["langewebsitenaam"]="Wintersportaccommodaties.nl";
	$vars["seizoentype"]=1;
	$vars["website"]="W";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["basehref"]="http://www.wintersportaccommodaties.nl/";
	$vars["email"]="info@wintersportaccommodaties.nl";
	$basehref="http://www.wintersportaccommodaties.nl/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-8";
	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
} elseif($_SERVER["HTTP_HOST"]=="www.chalet.eu" or ($vars["lokale_testserver"] and $vars["testsite"]=="chaleteu")) {
	# Winter Chalet.eu Engelstalig
	$vars["websitetype"]=1;
	$vars["websitenaam"]="Chalet.eu";
	$vars["langewebsitenaam"]="Chalet.eu";
	$vars["seizoentype"]=1;
	$vars["website"]="E";
	$vars["taal"]="en";
	$vars["websiteland"]="en";
	$vars["ttv"]="_en";
	$vars["basehref"]="http://www.chalet.eu/";
	$vars["email"]="info@chalet.eu";
	$basehref="http://www.chalet.eu/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-6";
	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["wederverkoop"]=true;
} elseif($_SERVER["HTTP_HOST"]=="www.chalettour.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="chalettour")) {
	# Chalettour Winter
	$vars["websitetype"]=4;
	$vars["websitenaam"]="Chalettour.nl";
	$vars["langewebsitenaam"]="Chalettour.nl";
	$vars["seizoentype"]=1;
	$vars["website"]="T";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.chalettour.nl/";
	$vars["email"]="info@chalettour.nl";
	$basehref="http://www.chalettour.nl/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-7";
	$vars["mailingmanagerid"]="re16dsas";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
} elseif($_SERVER["HTTP_HOST"]=="www.chalet.be" or ($vars["lokale_testserver"] and $vars["testsite"]=="chaletbe")) {
	# Chalet.be Winter
	$vars["websitetype"]=1;
	$vars["websitenaam"]="Chalet.be";
	$vars["langewebsitenaam"]="Chalet.be";
	$vars["seizoentype"]=1;
	$vars["website"]="B";
	$vars["taal"]="nl";
	$vars["websiteland"]="be";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.chalet.be/";
	$vars["email"]="info@chalet.be";
	$basehref="http://www.chalet.be/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-3";
	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
} elseif($_SERVER["HTTP_HOST"]=="www.zomerhuisje.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="zomerhuisjenl")) {
	# Zomerhuisje.nl
	$vars["websitetype"]=3;
	$vars["websitenaam"]="Zomerhuisje.nl";
	$vars["langewebsitenaam"]="Zomerhuisje.nl";
	$vars["seizoentype"]=2;
	$vars["website"]="Z";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.zomerhuisje.nl/";
	$vars["email"]="info@zomerhuisje.nl";
	$basehref="http://www.zomerhuisje.nl/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-2";
	$vars["facebook_pageid"]="168449903215909";
	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;

	# Tijdelijk
	$vars["nieuwevormgeving"]=true;

} elseif($_SERVER["HTTP_HOST"]=="www.zomerhuisje.eu" or ($vars["lokale_testserver"] and $vars["testsite"]=="zomerhuisjeeu")) {
	# Zomerhuisje.eu
	$vars["websitetype"]=3;
	$vars["websitenaam"]="Zomerhuisje.eu";
	$vars["langewebsitenaam"]="Zomerhuisje.eu";
	$vars["seizoentype"]=2;
	$vars["website"]="N";
	$vars["taal"]="nl";
	$vars["websiteland"]="be";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.zomerhuisje.eu/";
	$vars["email"]="info@zomerhuisje.eu";
	$basehref="http://www.zomerhuisje.eu/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-5";
	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;

	# Tijdelijk
	$vars["nieuwevormgeving"]=true;

} elseif($_SERVER["HTTP_HOST"]=="www.chaletsinvallandry.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="vallandrynl")) {
	# Chaletsinvallandry.nl
	$vars["websitetype"]=6;
	$vars["websitenaam"]="Chalets in Vallandry";
	$vars["langewebsitenaam"]="Chalets in Vallandry";
	$vars["seizoentype"]=1;
	$vars["website"]="V";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.chaletsinvallandry.nl/";
	$vars["email"]="info@chaletsinvallandry.nl";
	$basehref="http://www.chaletsinvallandry.nl/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-10";
#	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["nieuwevormgeving"]=true;
} elseif($_SERVER["HTTP_HOST"]=="www.chaletsinvallandry.com" or ($vars["lokale_testserver"] and $vars["testsite"]=="vallandrycom")) {
	# Chaletsinvallandry.com
	$vars["websitetype"]=6;
	$vars["websitenaam"]="Chalets in Vallandry";
	$vars["langewebsitenaam"]="Chalets in Vallandry";
	$vars["seizoentype"]=1;
	$vars["website"]="Q";
	$vars["taal"]="en";
	$vars["websiteland"]="en";
	$vars["ttv"]="_en";
	$vars["basehref"]="http://www.chaletsinvallandry.com/";
	$vars["email"]="info@chaletsinvallandry.com";
	$basehref="http://www.chaletsinvallandry.com/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-9";
#	$vars["mailingmanagerid"]="cmkdlo9d";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["nieuwevormgeving"]=true;
} elseif($_SERVER["HTTP_HOST"]=="www.italissima.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="italissima")) {
	# Italissima
	$vars["websitetype"]=7;
	$vars["websitenaam"]="Italissima";
	$vars["langewebsitenaam"]="Italissima";
	$vars["seizoentype"]=2;
	$vars["website"]="I";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.italissima.nl/";
	$vars["email"]="info@italissima.nl";
	$basehref="http://www.italissima.nl/";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-11";
	$vars["facebook_pageid"]="272671556122756";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;

	# Tijdelijk
	$vars["nieuwevormgeving"]=true;
} else {
	# Onbekend welke site er wordt opgevraagd
	if(!$css) {
#		wt_mail("jeroen@webtastic.nl","Onbekende HTTP_HOST bij Chalet",$_SERVER["HTTP_HOST"]."\n".$_SERVER["REQUEST_URI"]);
	}
	if(ereg("chalet\.nl",$_SERVER["HTTP_HOST"])) {
		header("Location: http://www.chalet.nl/");
	} elseif(ereg("chalet\.eu",$_SERVER["HTTP_HOST"])) {
		header("Location: http://www.chalet.eu/");
	} elseif(ereg("wintersportaccommodaties\.nl",$_SERVER["HTTP_HOST"])) {
		header("Location: http://www.wintersportaccommodaties.nl/");
	} else {
		header("Location: http://www.chalet.nl/");
	}
	exit;
}

if($vars["websitetype"]==1 or $vars["websitetype"]==4) {
	$vars["nieuwevormgeving"]=true;
}

#
# Opmaak bepalen
#


if($vars["websitetype"]==1 or $vars["websitetype"]==4) {
	# Chalet.nl / Chalet.eu winter
	$bordercolor="#0D3E88";
	$rood="#D3033A";
	$hover="#0D3E88";
	$hr="#D5E1F9";
	$table="#0D3E88";
	$font="Tahoma, Helvetica, sans-serif;";
	$bodybgcolor="#0D3E88";
	$thfontcolor="#ffffff";
	$thfontsize="0.8em";
	
	# Nieuwe vormgeving
	if($vars["nieuwevormgeving"]) {
		$bordercolor="#d5e1f9";
		$rood="#D3033A";
		$hover="#d40139";
		$hr="#D5E1F9";
		$table="#d5e1f9";
		$font="Verdana, Arial, Helvetica, sans-serif;";
		$bodybgcolor="#d5e1f9";
		$thfontcolor="#003366";
		$thfontsize="0.9em";
		$activetabcolor="#003366";
		$inactivetabcolor="#d5e1f9";
		$inactivetabfontcolor="#003366";
		$css_aanbiedingkleur="#d40139";
	}
	
} elseif($vars["websitetype"]==2) {
	# Wintersportaccommodaties.nl
	$bordercolor="#BAC5D6";
	$rood="#CC0033";
	$hover="#00cc00";
	$hr="#BAC5D6";
	$table="#BAC5D6";
	$font="Arial, Helvetica, sans-serif;";
	$thfontcolor="#ffffff";
	$thfontsize="0.8em";
	$css_aanbiedingkleur="#d40139";	
} elseif($vars["websitetype"]==6) {
	# Chalets in Vallandry
	$bordercolor="#6699cc";
	$rood="#6699cc";
	$hover="#666600";
	$hr="#6699cc";
	$table="#6699cc";
	$font="Verdana, Arial, Helvetica, sans-serif;";
	$bodybgcolor="#ffffff";
	$thfontcolor="#ffffff";
	$thfontsize="0.9em";
	$activetabcolor="#6699cc";
	$inactivetabcolor="#666600";
	$inactivetabfontcolor="#ffffff";
	$css_aanbiedingkleur="#666600";
} elseif($vars["websitetype"]==7) {
	# Italissima
	$bordercolor="#ffd38f";
	$rood="#ffd38f";
	$hover="#ff9900";
	$hr="#ffd38f";
	$table="#ffd38f";
	$font="Verdana, Arial, Helvetica, sans-serif;";
	$bodybgcolor="#ffffff";
	$thfontcolor="#661700";
	$thfontsize="0.9em";
	$activetabcolor="#ff9900";
	$inactivetabcolor="#ffd38f";
	$inactivetabfontcolor="#661700";
	$css_aanbiedingkleur="#661700";
} else {
	# Chalet.nl Zomer / Chalet.eu Summer / Zomerhuisje
	$bordercolor="#FF6600";
	$rood="#FF6600";
	$hover="#FFCC00";
	$hr="#FF6600";
	$table="#FF6600";
	$font="Tahoma, Helvetica, sans-serif;";
	$bodybgcolor="#95DDEC";
	$thfontcolor="#ffffff";
	$thfontsize="0.8em";

	# Nieuwe vormgeving
	if($vars["nieuwevormgeving"]) {
		$bordercolor="#5f227b";
		$rood="#5f227b";
		$hover="#cbd328";
		$hr="#eff2be";
		$table="#5f227b";
		$font="Verdana, Arial, Helvetica, sans-serif;";
		$bodybgcolor="#eff2be";
		$thfontcolor="#ffffff";
		$thfontsize="0.9em";
		$activetabcolor="#5f227b";
		$inactivetabcolor="#cbd328";
		$inactivetabfontcolor="#ffffff";
		$css_aanbiedingkleur="#636f07";
		
		# paars: #5f227b
		# groen: #cbd328
	}
}


# Opmaak CMS
if($_GET["cmscss"]) {
	$bordercolor="#0D3E88";
	$rood="#D3033A";
	$hover="#0D3E88";
	$hr="#D5E1F9";
	$table="#0D3E88";
	$font="Tahoma, Helvetica, sans-serif;";
	$bodybgcolor="#0D3E88";
	$thfontcolor="#ffffff";
	$thfontsize="0.8em";
}

$vars["cmspath"]="http://www.chalet.nl/";
if($vars["lokale_testserver"]) {
	$vars["basehref"]="http://".$_SERVER["HTTP_HOST"]."/chalet".$path;
	$basehref="http://".$_SERVER["HTTP_HOST"]."/chalet".$path;
	$path="/chalet".$path;
	$vars["cmspath"]=$path;
}

#
# Websites-info-array
#	
$vars["websiteinfo"]["websitenaam"]["W"]="Wintersportaccommodaties.nl";
$vars["websiteinfo"]["langewebsitenaam"]["W"]="Chalet.nl B.V. / Wintersportaccommodaties.nl";
$vars["websiteinfo"]["email"]["W"]="info@wintersportaccommodaties.nl";
$vars["websiteinfo"]["basehref"]["W"]="http://www.wintersportaccommodaties.nl/";
$vars["websiteinfo"]["websitetype"]["W"]=2;
$vars["websiteinfo"]["verzekering_mogelijk"]["W"]=1;
$vars["websiteinfo"]["websiteland"]["W"]="nl";
$vars["websiteinfo"]["taal"]["W"]="nl";

$vars["websiteinfo"]["websitenaam"]["E"]="Chalet.eu";
$vars["websiteinfo"]["langewebsitenaam"]["E"]="Chalet.nl B.V. / Chalet.eu";
$vars["websiteinfo"]["email"]["E"]="info@chalet.eu";
$vars["websiteinfo"]["basehref"]["E"]="http://www.chalet.eu/";
$vars["websiteinfo"]["websitetype"]["E"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["E"]=0;
$vars["websiteinfo"]["wederverkoop"]["E"]=true;
$vars["websiteinfo"]["websiteland"]["E"]="en";
$vars["websiteinfo"]["taal"]["E"]="en";

$vars["websiteinfo"]["websitenaam"]["Z"]="Zomerhuisje.nl";
$vars["websiteinfo"]["langewebsitenaam"]["Z"]="Chalet.nl B.V. / Zomerhuisje.nl";
$vars["websiteinfo"]["email"]["Z"]="info@zomerhuisje.nl";
$vars["websiteinfo"]["basehref"]["Z"]="http://www.zomerhuisje.nl/";
$vars["websiteinfo"]["websitetype"]["Z"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["Z"]=1;
$vars["websiteinfo"]["wederverkoop"]["Z"]=true;
$vars["websiteinfo"]["websiteland"]["Z"]="nl";
$vars["websiteinfo"]["taal"]["Z"]="nl";

$vars["websiteinfo"]["websitenaam"]["S"]="Chalet.eu";
$vars["websiteinfo"]["langewebsitenaam"]["S"]="Chalet.nl B.V. / Chalet.eu";
$vars["websiteinfo"]["email"]["S"]="info@chalet.eu";
$vars["websiteinfo"]["basehref"]["S"]="http://www.chalet.eu/summer/";
$vars["websiteinfo"]["websitetype"]["S"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["S"]=0;
$vars["websiteinfo"]["websiteland"]["S"]="en";
$vars["websiteinfo"]["taal"]["S"]="en";

$vars["websiteinfo"]["websitenaam"]["T"]="Chalettour.nl";
$vars["websiteinfo"]["langewebsitenaam"]["T"]="Chalet.nl B.V. / Chalettour.nl";
$vars["websiteinfo"]["email"]["T"]="info@chalettour.nl";
$vars["websiteinfo"]["basehref"]["T"]="http://www.chalettour.nl/";
$vars["websiteinfo"]["websitetype"]["T"]=4;
$vars["websiteinfo"]["verzekering_mogelijk"]["T"]=1;
$vars["websiteinfo"]["wederverkoop"]["T"]=true;
$vars["websiteinfo"]["websiteland"]["T"]="nl";
$vars["websiteinfo"]["taal"]["T"]="nl";

$vars["websiteinfo"]["websitenaam"]["O"]="Chalettour.nl";
$vars["websiteinfo"]["langewebsitenaam"]["O"]="Chalet.nl B.V. / Chalettour.nl";
$vars["websiteinfo"]["email"]["O"]="info@chalettour.nl";
$vars["websiteinfo"]["basehref"]["O"]="http://www.chalettour.nl/zomer/";
$vars["websiteinfo"]["websitetype"]["O"]=5;
$vars["websiteinfo"]["verzekering_mogelijk"]["O"]=1;
$vars["websiteinfo"]["wederverkoop"]["O"]=true;
$vars["websiteinfo"]["websiteland"]["O"]="nl";
$vars["websiteinfo"]["taal"]["O"]="nl";

$vars["websiteinfo"]["websitenaam"]["B"]="Chalet.be";
$vars["websiteinfo"]["langewebsitenaam"]["B"]="Chalet.nl B.V. / Chalet.be";
$vars["websiteinfo"]["email"]["B"]="info@chalet.be";
$vars["websiteinfo"]["basehref"]["B"]="http://www.chalet.be/";
$vars["websiteinfo"]["websitetype"]["B"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["B"]=1;
$vars["websiteinfo"]["websiteland"]["B"]="be";
$vars["websiteinfo"]["taal"]["B"]="nl";

$vars["websiteinfo"]["websitenaam"]["N"]="Zomerhuisje.eu";
$vars["websiteinfo"]["langewebsitenaam"]["N"]="Chalet.nl B.V. / Zomerhuisje.eu";
$vars["websiteinfo"]["email"]["N"]="info@zomerhuisje.eu";
$vars["websiteinfo"]["basehref"]["N"]="http://www.zomerhuisje.eu/";
$vars["websiteinfo"]["websitetype"]["N"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["N"]=1;
$vars["websiteinfo"]["websiteland"]["N"]="be";
$vars["websiteinfo"]["taal"]["N"]="nl";

$vars["websiteinfo"]["websitenaam"]["V"]="Chalets in Vallandry";
$vars["websiteinfo"]["langewebsitenaam"]["V"]="Chalets in Vallandry";
$vars["websiteinfo"]["email"]["V"]="info@chaletsinvallandry.nl";
$vars["websiteinfo"]["basehref"]["V"]="http://www.chaletsinvallandry.nl/";
$vars["websiteinfo"]["websitetype"]["V"]=6;
$vars["websiteinfo"]["verzekering_mogelijk"]["V"]=1;
$vars["websiteinfo"]["wederverkoop"]["V"]=true;
$vars["websiteinfo"]["websiteland"]["V"]="nl";
$vars["websiteinfo"]["taal"]["V"]="nl";

$vars["websiteinfo"]["websitenaam"]["Q"]="Chalets in Vallandry";
$vars["websiteinfo"]["langewebsitenaam"]["Q"]="Chalets in Vallandry";
$vars["websiteinfo"]["email"]["Q"]="info@chaletsinvallandry.com";
$vars["websiteinfo"]["basehref"]["Q"]="http://www.chaletsinvallandry.com/";
$vars["websiteinfo"]["websitetype"]["Q"]=6;
$vars["websiteinfo"]["verzekering_mogelijk"]["Q"]=1;
$vars["websiteinfo"]["wederverkoop"]["Q"]=true;
$vars["websiteinfo"]["websiteland"]["Q"]="en";
$vars["websiteinfo"]["taal"]["Q"]="en";

$vars["websiteinfo"]["websitenaam"]["I"]="Italissima";
$vars["websiteinfo"]["langewebsitenaam"]["I"]="Chalet.nl B.V. / Italissima";
$vars["websiteinfo"]["email"]["I"]="info@italissima.nl";
$vars["websiteinfo"]["basehref"]["I"]="http://www.italissima.nl/";
$vars["websiteinfo"]["websitetype"]["I"]=7;
$vars["websiteinfo"]["verzekering_mogelijk"]["I"]=1;
$vars["websiteinfo"]["wederverkoop"]["I"]=true;
$vars["websiteinfo"]["websiteland"]["I"]="nl";
$vars["websiteinfo"]["taal"]["I"]="nl";

$vars["websiteinfo"]["websitenaam"]["C"]="Chalet.nl";
$vars["websiteinfo"]["langewebsitenaam"]["C"]="Chalet.nl B.V.";
$vars["websiteinfo"]["email"]["C"]="info@chalet.nl";
$vars["websiteinfo"]["basehref"]["C"]="http://www.chalet.nl/";
$vars["websiteinfo"]["websitetype"]["C"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["C"]=1;
$vars["websiteinfo"]["websiteland"]["C"]="nl";
$vars["websiteinfo"]["taal"]["C"]="nl";


# Diverse vars

$vars["websites"]=array("C"=>"Chalet.nl Winter","Z"=>"Zomerhuisje.nl","W"=>"Wintersportaccommodaties.nl","E"=>"Chalet.eu Engelstalig Winter","S"=>"Chalet.eu Engelstalig Zomer (niet meer actief)","T"=>"Chalettour.nl Winter","O"=>"Chalettour.nl Zomer (niet meer actief)","B"=>"Chalet.be Winter","N"=>"Zomerhuisje.eu","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima");
$vars["websites_actief"]=array("C"=>"Chalet.nl Winter","Z"=>"Zomerhuisje.nl","W"=>"Wintersportaccommodaties.nl","E"=>"Chalet.eu Engelstalig Winter","T"=>"Chalettour.nl Winter","B"=>"Chalet.be Winter","N"=>"Zomerhuisje.eu","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima");
$vars["websites_basehref"]=array("C"=>"http://www.chalet.nl/","Z"=>"http://www.zomerhuisje.nl/","W"=>"http://www.wintersportaccommodaties.nl/","E"=>"http://www.chalet.eu/","S"=>"http://www.chalet.nl/summer/","T"=>"http://www.chalettour.nl/","O"=>"http://www.chalettour.nl/zomer/","B"=>"http://www.chalet.be/","N"=>"http://www.zomerhuisje.eu/","V"=>"http://www.chaletsinvallandry.nl/","Q"=>"http://www.chaletsinvallandry.com/","I"=>"http://www.italissima.nl/");
$vars["websites_inactief"]=array("S"=>true,"O"=>true);
#$vars["websites_basehref_siteid"]=array(1=>"http://www.chalet.nl/",2=>"http://www.wintersportaccommodaties.nl/",3=>"http://www.zomerhuisje.nl/",4=>"http://www.chalettour.nl/",5=>"http://www.chalettour.nl/zomer/",6=>"http://www.chaletsinvallandry.nl/");
#$vars["websites_wzt_siteid"]=array(1=>1,2=>1,3=>2,4=>1,5=>2,6=>1);
#$vars["websitetype_namen"]=array(1=>"Chalet.nl / Chalet.eu winter",2=>"Wintersportaccommodaties.nl",3=>"Chalet.nl / Chalet.eu zomer",4=>"Chalettour.nl winter",5=>"Chalettour.nl zomer");
#$vars["wederverkoop_sites"]=array("T","O","Z");

$vars["websites_wzt"][1]=array("C"=>"Chalet.nl","W"=>"Wintersportaccommodaties.nl","E"=>"Chalet.eu (Engelstalig)","T"=>"Chalettour.nl (wederverkoop)","B"=>"Chalet.be","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)");
$vars["websites_wzt"][2]=array("Z"=>"Zomerhuisje.nl","N"=>"Zomerhuisje.eu (gericht op België)","S"=>"Chalet.eu Engelstalig Zomer (niet meer actief)","O"=>"Chalettour.nl Zomer (niet meer actief)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima");

$vars["websites_wzt_actief"][1]=array("C"=>"Chalet.nl","W"=>"Wintersportaccommodaties.nl","E"=>"Chalet.eu (Engelstalig)","T"=>"Chalettour.nl (wederverkoop)","B"=>"Chalet.be","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)");
$vars["websites_wzt_actief"][2]=array("Z"=>"Zomerhuisje.nl","N"=>"Zomerhuisje.eu (gericht op België)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima");

#$vars["websitetype_namen_oud"]=array(1=>"Chalet.nl/.eu/.be winter",2=>"Wintersportaccommodaties.nl",3=>"Zomerhuisje.nl/.eu",4=>"Chalettour.nl (wederverkoop)",5=>"Chalettour.nl zomer (niet meer actief)",6=>"Chalets in Vallandry (.nl en .com)");
$vars["websitetype_namen"]=array(1=>"Chalet.nl/.eu/.be",2=>"Wintersportaccommodaties.nl",3=>"Zomerhuisje.nl/.eu",4=>"Chalettour.nl",6=>"Chalets in Vallandry (.nl en .com)",7=>"Italissima");

$vars["websitetype_namen_wzt"]=array(1=>1,2=>1,3=>2,4=>1,5=>2,6=>1,7=>2);

?>