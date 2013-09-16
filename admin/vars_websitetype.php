<?php


#
#
# Bepalen welke website wordt opgevraagd
#
#

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $_SERVER["HTTP_HOST"]=="chalet-nl-dev.web.netromtest.ro") {
	$vars["lokale_testserver"]=true;
}

#
# CMS-link
#
if($vars["lokale_testserver"]) {

	# Testsite bepalen indien niet bekend
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		$vars["cms_basehref"]="http://ss.postvak.net/chalet/";

		if(!$vars["testsite"]) {
			$vars["testsite"]=@file_get_contents("/home/webtastic/html/chalet/tmp/testsite.txt");
		}
	}

	# Testsite bepalen voor Miguel
	if($_SERVER["HTTP_HOST"]=="chalet-nl-dev.web.netromtest.ro") {
		$vars["cms_basehref"]="http://chalet-nl-dev.web.netromtest.ro/";
		if(!$vars["testsite"]) {
			$vars["testsite"]=@file_get_contents("/var/www/chalet/chalet-nl-dev/tmp/testsite.txt");
		}
		if(!$vars["testsite"]) {
			$vars["testsite"]="C";
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
if($cron or $_SERVER["HTTP_HOST"]=="www.chalet.nl" or $_SERVER["HTTP_HOST"]=="www2.chalet.nl" or $_SERVER["HTTP_HOST"]=="www3.chalet.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="C")) {
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-1";
	$vars["facebook_pageid"]="156825034385110";
	$vars["twitter_user"]="ChaletNL";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["nieuwsbrief_aanbieden"]=true;
#	$vars["nieuwsbrief_tijdelijk_kunnen_afmelden"]=true;
	$vars["livechat_code"]="2-PTFmcHUgtM";
} elseif($_SERVER["HTTP_HOST"]=="www.chalet.eu" or ($vars["lokale_testserver"] and $vars["testsite"]=="E")) {
	# Winter Chalet.eu Engelstalig
	$vars["websitetype"]=1;
	$vars["websitenaam"]="Chalet.eu";
	$vars["langewebsitenaam"]="Chalet.eu";
	$vars["seizoentype"]=1;
	$vars["website"]="E";
	$vars["taal"]="en";
	$vars["websiteland"]="en";
	$vars["ttv"]="_en";
	$vars["basehref"]="https://www.chalet.eu/";
	$vars["email"]="info@chalet.eu";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-6";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.chalettour.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="T")) {
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-7";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.chalet.be" or ($vars["lokale_testserver"] and $vars["testsite"]=="B")) {
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-3";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["nieuwsbrief_aanbieden"]=true;
#	$vars["nieuwsbrief_tijdelijk_kunnen_afmelden"]=true;
	$vars["livechat_code"]="3-eex4-wCgtM";
} elseif($_SERVER["HTTP_HOST"]=="www.zomerhuisje.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="Z")) {
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-2";
	$vars["facebook_pageid"]="168449903215909";
	$vars["twitter_user"]="Zomerhuisje";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.zomerhuisje.eu" or ($vars["lokale_testserver"] and $vars["testsite"]=="N")) {
	# Zomerhuisje.eu (NIET MEER IN GEBRUIK!)
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-5";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.chaletsinvallandry.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="V")) {
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-10";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.chaletsinvallandry.com" or ($vars["lokale_testserver"] and $vars["testsite"]=="Q")) {
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-9";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.italissima.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="I")) {
	# Italissima.nl
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
	$path="/";
	$vars["googleanalytics"]="UA-2078202-11";
	$vars["facebook_pageid"]="272671556122756";
	$vars["twitter_user"]="Italissima";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["nieuwsbrief_aanbieden"]=true;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.italissima.be" or ($vars["lokale_testserver"] and $vars["testsite"]=="K")) {
	# Italissima.be
	$vars["websitetype"]=7;
	$vars["websitenaam"]="Italissima";
	$vars["langewebsitenaam"]="Italissima";
	$vars["seizoentype"]=2;
	$vars["website"]="K";
	$vars["taal"]="nl";
	$vars["websiteland"]="be";
	$vars["ttv"]="";
	$vars["basehref"]="http://www.italissima.be/";
	$vars["email"]="info@italissima.be";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-14";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=false;
	$vars["nieuwsbrief_aanbieden"]=true;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="www.superski.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="W")) {
	# SuperSki
	$vars["websitetype"]=8;
	$vars["websitenaam"]="SuperSki";
	$vars["langewebsitenaam"]="SuperSki";
	$vars["seizoentype"]=1;
	$vars["website"]="W";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["basehref"]="http://www.superski.nl/";
	$vars["email"]="info@superski.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-13";
	$vars["facebook_pageid"]="290286354404681";
	$vars["twitter_user"]="SuperSkiNL";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="test.venturasol.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="X")) {
	# Venturasol
	$vars["websitetype"]=9;
	$vars["websitenaam"]="Venturasol Vacances";
	$vars["langewebsitenaam"]="Venturasol Vacances";
	$vars["seizoentype"]=1;
	$vars["website"]="X";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["basehref"]="http://test.venturasol.nl/";
	$vars["email"]="info@venturasol.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-15";
	$vars["facebook_pageid"]="162914327091136";
	$vars["twitter_user"]="Venturasol_nl";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["livechat_code"]=false;
} elseif($_SERVER["HTTP_HOST"]=="partner.venturasol.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="Y")) {
	# Venturasol-partner
	$vars["websitetype"]=9;
	$vars["websitenaam"]="Venturasol-partner";
	$vars["langewebsitenaam"]="Venturasol-partner";
	$vars["seizoentype"]=1;
	$vars["website"]="Y";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["basehref"]="http://partner.venturasol.nl/";
	$vars["email"]="info@venturasol.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-16";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
} else {
	# Onbekend welke site er wordt opgevraagd
	if(ereg("chalet\.nl",$_SERVER["HTTP_HOST"])) {
		header("Location: http://www.chalet.nl/");
	} elseif(ereg("chalet\.eu",$_SERVER["HTTP_HOST"])) {
		header("Location: https://www.chalet.eu/");
	} else {
		header("Location: http://www.chalet.nl/");
	}
	exit;
}

#
# Opmaak bepalen
#


if($vars["websitetype"]==1 or $vars["websitetype"]==4) {
	# Chalet.nl / Chalet.eu winter
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
} elseif($vars["websitetype"]==8) {
	# SuperSki
	$bordercolor="#003366";
	$rood="#e6007e";
	$hover="#e6007e";
	$hr="#003366";
	$table="#003366";
	$font="Verdana, Arial, Helvetica, sans-serif;";
	$bodybgcolor="#ebebeb";
	$thfontcolor="#ffffff";
	$thfontsize="0.9em";
	$activetabcolor="#e6007e";
	$inactivetabcolor="#003366";
	$inactivetabfontcolor="#ffffff";
	$css_aanbiedingkleur="#e6007e";
} elseif($vars["websitetype"]==9) {
	# Venturasol
	$bordercolor="#0412b1";
	$rood="#0412b1";
	$hover="#0412b1";
	$hr="#0412b1";
	$table="#0412b1";
	$font="Verdana, Arial, Helvetica, sans-serif;";
	$bodybgcolor="#ebebeb";
	$thfontcolor="#ffffff";
	$thfontsize="0.9em";
	$activetabcolor="#ffdc00";
	$inactivetabcolor="#0412b1";
	$inactivetabfontcolor="#ffffff";
	$css_aanbiedingkleur="#e6007e";
} else {
	# Chalet.nl Zomer / Chalet.eu Summer / Zomerhuisje: NIET MEER IN GEBRUIK
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
	$path="/chalet".$path;
	$vars["cmspath"]=$path;
}

#
# Websites-info-array
#

$vars["websiteinfo"]["websitenaam"]["W"]="SuperSki";
$vars["websiteinfo"]["langewebsitenaam"]["W"]="SuperSki";
$vars["websiteinfo"]["email"]["W"]="info@superski.nl";
$vars["websiteinfo"]["basehref"]["W"]="http://www.superski.nl/";
$vars["websiteinfo"]["websitetype"]["W"]=8;
$vars["websiteinfo"]["verzekering_mogelijk"]["W"]=1;
$vars["websiteinfo"]["websiteland"]["W"]="nl";
$vars["websiteinfo"]["taal"]["W"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["W"]=false;
$vars["websiteinfo"]["livechat_code"]["W"]=false;

$vars["websiteinfo"]["websitenaam"]["E"]="Chalet.eu";
$vars["websiteinfo"]["langewebsitenaam"]["E"]="Chalet.nl B.V. / Chalet.eu";
$vars["websiteinfo"]["email"]["E"]="info@chalet.eu";
$vars["websiteinfo"]["basehref"]["E"]="https://www.chalet.eu/";
$vars["websiteinfo"]["websitetype"]["E"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["E"]=0;
$vars["websiteinfo"]["wederverkoop"]["E"]=true;
$vars["websiteinfo"]["websiteland"]["E"]="en";
$vars["websiteinfo"]["taal"]["E"]="en";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["E"]=false;
$vars["websiteinfo"]["livechat_code"]["E"]=false;

$vars["websiteinfo"]["websitenaam"]["Z"]="Zomerhuisje.nl";
$vars["websiteinfo"]["langewebsitenaam"]["Z"]="Chalet.nl B.V. / Zomerhuisje.nl";
$vars["websiteinfo"]["email"]["Z"]="info@zomerhuisje.nl";
$vars["websiteinfo"]["basehref"]["Z"]="http://www.zomerhuisje.nl/";
$vars["websiteinfo"]["websitetype"]["Z"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["Z"]=1;
$vars["websiteinfo"]["wederverkoop"]["Z"]=true;
$vars["websiteinfo"]["websiteland"]["Z"]="nl";
$vars["websiteinfo"]["taal"]["Z"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["Z"]=false;
$vars["websiteinfo"]["livechat_code"]["Z"]=false;

$vars["websiteinfo"]["websitenaam"]["S"]="Chalet.eu";
$vars["websiteinfo"]["langewebsitenaam"]["S"]="Chalet.nl B.V. / Chalet.eu";
$vars["websiteinfo"]["email"]["S"]="info@chalet.eu";
$vars["websiteinfo"]["basehref"]["S"]="http://www.chalet.eu/summer/";
$vars["websiteinfo"]["websitetype"]["S"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["S"]=0;
$vars["websiteinfo"]["websiteland"]["S"]="en";
$vars["websiteinfo"]["taal"]["S"]="en";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["S"]=false;
$vars["websiteinfo"]["livechat_code"]["S"]=false;

$vars["websiteinfo"]["websitenaam"]["T"]="Chalettour.nl";
$vars["websiteinfo"]["langewebsitenaam"]["T"]="Chalet.nl B.V. / Chalettour.nl";
$vars["websiteinfo"]["email"]["T"]="info@chalettour.nl";
$vars["websiteinfo"]["basehref"]["T"]="http://www.chalettour.nl/";
$vars["websiteinfo"]["websitetype"]["T"]=4;
$vars["websiteinfo"]["verzekering_mogelijk"]["T"]=1;
$vars["websiteinfo"]["wederverkoop"]["T"]=true;
$vars["websiteinfo"]["websiteland"]["T"]="nl";
$vars["websiteinfo"]["taal"]["T"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["T"]=false;
$vars["websiteinfo"]["livechat_code"]["T"]=false;

$vars["websiteinfo"]["websitenaam"]["O"]="Chalettour.nl";
$vars["websiteinfo"]["langewebsitenaam"]["O"]="Chalet.nl B.V. / Chalettour.nl";
$vars["websiteinfo"]["email"]["O"]="info@chalettour.nl";
$vars["websiteinfo"]["basehref"]["O"]="http://www.chalettour.nl/zomer/";
$vars["websiteinfo"]["websitetype"]["O"]=5;
$vars["websiteinfo"]["verzekering_mogelijk"]["O"]=1;
$vars["websiteinfo"]["wederverkoop"]["O"]=true;
$vars["websiteinfo"]["websiteland"]["O"]="nl";
$vars["websiteinfo"]["taal"]["O"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["O"]=false;
$vars["websiteinfo"]["livechat_code"]["O"]=false;

$vars["websiteinfo"]["websitenaam"]["B"]="Chalet.be";
$vars["websiteinfo"]["langewebsitenaam"]["B"]="Chalet.nl B.V. / Chalet.be";
$vars["websiteinfo"]["email"]["B"]="info@chalet.be";
$vars["websiteinfo"]["basehref"]["B"]="http://www.chalet.be/";
$vars["websiteinfo"]["websitetype"]["B"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["B"]=1;
$vars["websiteinfo"]["websiteland"]["B"]="be";
$vars["websiteinfo"]["taal"]["B"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["B"]=true;
$vars["websiteinfo"]["livechat_code"]["B"]="3-eex4-wCgtM";

$vars["websiteinfo"]["websitenaam"]["N"]="Zomerhuisje.eu";
$vars["websiteinfo"]["langewebsitenaam"]["N"]="Chalet.nl B.V. / Zomerhuisje.eu";
$vars["websiteinfo"]["email"]["N"]="info@zomerhuisje.eu";
$vars["websiteinfo"]["basehref"]["N"]="http://www.zomerhuisje.eu/";
$vars["websiteinfo"]["websitetype"]["N"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["N"]=1;
$vars["websiteinfo"]["websiteland"]["N"]="be";
$vars["websiteinfo"]["taal"]["N"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["N"]=false;
$vars["websiteinfo"]["livechat_code"]["N"]=false;

$vars["websiteinfo"]["websitenaam"]["V"]="Chalets in Vallandry";
$vars["websiteinfo"]["langewebsitenaam"]["V"]="Chalets in Vallandry";
$vars["websiteinfo"]["email"]["V"]="info@chaletsinvallandry.nl";
$vars["websiteinfo"]["basehref"]["V"]="http://www.chaletsinvallandry.nl/";
$vars["websiteinfo"]["websitetype"]["V"]=6;
$vars["websiteinfo"]["verzekering_mogelijk"]["V"]=1;
$vars["websiteinfo"]["wederverkoop"]["V"]=true;
$vars["websiteinfo"]["websiteland"]["V"]="nl";
$vars["websiteinfo"]["taal"]["V"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["V"]=false;
$vars["websiteinfo"]["livechat_code"]["V"]=false;

$vars["websiteinfo"]["websitenaam"]["Q"]="Chalets in Vallandry";
$vars["websiteinfo"]["langewebsitenaam"]["Q"]="Chalets in Vallandry";
$vars["websiteinfo"]["email"]["Q"]="info@chaletsinvallandry.com";
$vars["websiteinfo"]["basehref"]["Q"]="http://www.chaletsinvallandry.com/";
$vars["websiteinfo"]["websitetype"]["Q"]=6;
$vars["websiteinfo"]["verzekering_mogelijk"]["Q"]=1;
$vars["websiteinfo"]["wederverkoop"]["Q"]=true;
$vars["websiteinfo"]["websiteland"]["Q"]="en";
$vars["websiteinfo"]["taal"]["Q"]="en";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["Q"]=false;
$vars["websiteinfo"]["livechat_code"]["Q"]=false;

$vars["websiteinfo"]["websitenaam"]["I"]="Italissima";
$vars["websiteinfo"]["langewebsitenaam"]["I"]="Chalet.nl B.V. / Italissima";
$vars["websiteinfo"]["email"]["I"]="info@italissima.nl";
$vars["websiteinfo"]["basehref"]["I"]="http://www.italissima.nl/";
$vars["websiteinfo"]["websitetype"]["I"]=7;
$vars["websiteinfo"]["verzekering_mogelijk"]["I"]=1;
$vars["websiteinfo"]["wederverkoop"]["I"]=true;
$vars["websiteinfo"]["websiteland"]["I"]="nl";
$vars["websiteinfo"]["taal"]["I"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["I"]=true;
$vars["websiteinfo"]["livechat_code"]["I"]=false;

$vars["websiteinfo"]["websitenaam"]["K"]="Italissima";
$vars["websiteinfo"]["langewebsitenaam"]["K"]="Chalet.nl B.V. / Italissima";
$vars["websiteinfo"]["email"]["K"]="info@italissima.be";
$vars["websiteinfo"]["basehref"]["K"]="http://www.italissima.be/";
$vars["websiteinfo"]["websitetype"]["K"]=7;
$vars["websiteinfo"]["verzekering_mogelijk"]["K"]=1;
$vars["websiteinfo"]["wederverkoop"]["K"]=false;
$vars["websiteinfo"]["websiteland"]["K"]="be";
$vars["websiteinfo"]["taal"]["K"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["K"]=true;
$vars["websiteinfo"]["livechat_code"]["K"]=false;

$vars["websiteinfo"]["websitenaam"]["X"]="Venturasol Vacances";
$vars["websiteinfo"]["langewebsitenaam"]["X"]="Venturasol Vacances";
$vars["websiteinfo"]["email"]["X"]="info@venturasol.nl";
$vars["websiteinfo"]["basehref"]["X"]="http://test.venturasol.nl/";
$vars["websiteinfo"]["websitetype"]["X"]=9;
$vars["websiteinfo"]["verzekering_mogelijk"]["X"]=1;
$vars["websiteinfo"]["wederverkoop"]["X"]=false;
$vars["websiteinfo"]["websiteland"]["X"]="nl";
$vars["websiteinfo"]["taal"]["X"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["X"]=false;
$vars["websiteinfo"]["livechat_code"]["X"]=false;

$vars["websiteinfo"]["websitenaam"]["Y"]="Venturasol-partner";
$vars["websiteinfo"]["langewebsitenaam"]["Y"]="Venturasol-partner";
$vars["websiteinfo"]["email"]["Y"]="info@venturasol.nl";
$vars["websiteinfo"]["basehref"]["Y"]="http://partner.venturasol.nl/";
$vars["websiteinfo"]["websitetype"]["Y"]=9;
$vars["websiteinfo"]["verzekering_mogelijk"]["Y"]=1;
$vars["websiteinfo"]["wederverkoop"]["Y"]=true;
$vars["websiteinfo"]["websiteland"]["Y"]="nl";
$vars["websiteinfo"]["taal"]["Y"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["Y"]=false;
$vars["websiteinfo"]["livechat_code"]["Y"]=false;

$vars["websiteinfo"]["websitenaam"]["C"]="Chalet.nl";
$vars["websiteinfo"]["langewebsitenaam"]["C"]="Chalet.nl B.V.";
$vars["websiteinfo"]["email"]["C"]="info@chalet.nl";
$vars["websiteinfo"]["basehref"]["C"]="http://www.chalet.nl/";
$vars["websiteinfo"]["websitetype"]["C"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["C"]=1;
$vars["websiteinfo"]["websiteland"]["C"]="nl";
$vars["websiteinfo"]["taal"]["C"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["C"]=true;
$vars["websiteinfo"]["livechat_code"]["C"]="2-PTFmcHUgtM";

# Diverse vars

$vars["websites"]=array("C"=>"Chalet.nl Winter","Z"=>"Zomerhuisje.nl","W"=>"SuperSki","E"=>"Chalet.eu Engelstalig Winter","S"=>"Chalet.eu Engelstalig Zomer (niet meer actief)","T"=>"Chalettour.nl Winter","O"=>"Chalettour.nl Zomer (niet meer actief)","B"=>"Chalet.be Winter","D"=>"Chalet Duitstalig Winter","N"=>"Zomerhuisje.eu (niet meer actief)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be","X"=>"Venturasol","Y"=>"Venturasol-partner");
$vars["websites_actief"]=array("C"=>"Chalet.nl Winter","Z"=>"Zomerhuisje.nl","W"=>"SuperSki","E"=>"Chalet.eu Engelstalig Winter","T"=>"Chalettour.nl Winter","B"=>"Chalet.be Winter","D"=>"Chalet Duitstalig Winter","N"=>"Zomerhuisje.eu","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be","X"=>"Venturasol","Y"=>"Venturasol-partner");
$vars["websites_basehref"]=array("C"=>"http://www.chalet.nl/","Z"=>"http://www.zomerhuisje.nl/","W"=>"http://www.superski.nl/","E"=>"http://www.chalet.eu/","S"=>"http://www.chalet.nl/summer/","T"=>"http://www.chalettour.nl/","O"=>"http://www.chalettour.nl/zomer/","B"=>"http://www.chalet.be/","D"=>"http://www.chalet.eu/","N"=>"http://www.zomerhuisje.eu/","V"=>"http://www.chaletsinvallandry.nl/","Q"=>"http://www.chaletsinvallandry.com/","I"=>"http://www.italissima.nl/","K"=>"http://www.italissima.be/","X"=>"http://test.venturasol.nl/","Y"=>"http://partner.venturasol.nl/");
$vars["websites_inactief"]=array("S"=>true,"O"=>true,"N"=>true);
#$vars["websites_basehref_siteid"]=array(1=>"http://www.chalet.nl/",2=>"http://www.superski.nl/",3=>"http://www.zomerhuisje.nl/",4=>"http://www.chalettour.nl/",5=>"http://www.chalettour.nl/zomer/",6=>"http://www.chaletsinvallandry.nl/");
#$vars["websites_wzt_siteid"]=array(1=>1,2=>1,3=>2,4=>1,5=>2,6=>1);
#$vars["websitetype_namen"]=array(1=>"Chalet.nl / Chalet.eu winter",2=>"SuperSki",3=>"Chalet.nl / Chalet.eu zomer",4=>"Chalettour.nl winter",5=>"Chalettour.nl zomer");
#$vars["wederverkoop_sites"]=array("T","O","Z");

$vars["websites_wzt"][1]=array("C"=>"Chalet.nl","E"=>"Chalet.eu (Engelstalig)","T"=>"Chalettour.nl (wederverkoop)","B"=>"Chalet.be","D"=>"Chalet (Duitstalig)","W"=>"SuperSki","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","X"=>"Venturasol","Y"=>"Venturasol-partner");
$vars["websites_wzt"][2]=array("Z"=>"Zomerhuisje.nl","N"=>"Zomerhuisje.eu (niet meer actief)","S"=>"Chalet.eu Engelstalig Zomer (niet meer actief)","O"=>"Chalettour.nl Zomer (niet meer actief)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be");

$vars["websites_wzt_actief"][1]=array("C"=>"Chalet.nl","W"=>"SuperSki","E"=>"Chalet.eu (Engelstalig)","T"=>"Chalettour.nl (wederverkoop)","B"=>"Chalet.be","D"=>"Chalet Duitstalig","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","X"=>"Venturasol","Y"=>"Venturasol-partner");
$vars["websites_wzt_actief"][2]=array("Z"=>"Zomerhuisje.nl","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be");

#$vars["websitetype_namen_oud"]=array(1=>"Chalet.nl/.eu/.be winter",2=>"SuperSki",3=>"Zomerhuisje.nl/.eu",4=>"Chalettour.nl (wederverkoop)",5=>"Chalettour.nl zomer (niet meer actief)",6=>"Chalets in Vallandry (.nl en .com)");
$vars["websitetype_namen"]=array(1=>"Chalet.nl/.eu/.be",2=>"SuperSki",3=>"Zomerhuisje.nl",4=>"Chalettour.nl",6=>"Chalets in Vallandry (.nl en .com)",7=>"Italissima (.nl en .be)",9=>"Venturasol");

# websitetype_namen koppelen aan seizoentype (1=winter, 2=zomer)
$vars["websitetype_namen_wzt"]=array(1=>1,2=>1,3=>2,4=>1,5=>2,6=>1,7=>2,9=>1);

?>