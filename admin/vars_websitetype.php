<?php


#
#
# Bepalen welke website wordt opgevraagd
#
#

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $_SERVER["HTTP_HOST"]=="chalet-dev.web.netromtest.ro" or defined("wt_test")) {
	$vars["lokale_testserver"]=true;
}

#
# CMS-link
#
if($vars["lokale_testserver"]) {

	if(!$unixdir) {
		$unixdir = dirname(dirname(__FILE__)) . "/";
	}

	# Testsite bepalen indien niet bekend
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		$vars["cms_basehref"]="http://ss.postvak.net/chalet/";

		if(!$vars["testsite"]) {
			$vars["testsite"]=@file_get_contents("/home/webtastic/html/chalet/tmp/testsite.txt");
		}
	}

	# Testsite bepalen indien niet bekend
	if(defined("wt_test_hostname")) {
		$vars["cms_basehref"]="http://".constant("wt_test_hostname")."/chalet/";

		if(!$vars["testsite"]) {
			$vars["testsite"]=@file_get_contents($unixdir."tmp/testsite.txt");
		}
	}

	# Testsite bepalen
	if($_SERVER["HTTP_HOST"]=="chalet-dev.web.netromtest.ro") {
		$vars["cms_basehref"]="http://chalet-dev.web.netromtest.ro/";
		if(!$vars["testsite"]) {
			$vars["testsite"]=@file_get_contents("/var/www/chalet/chalet-dev/tmp/testsite.txt");
		}
		if(!$vars["testsite"]) {
			$vars["testsite"]="C";
		}
	}
} else {
	$vars["cms_basehref"]="https://www.chalet.nl/";
}


#
# Websitetype en seizoentype bepalen
#
$_SERVER["HTTP_HOST"]=strtolower($_SERVER["HTTP_HOST"]);
if(substr($_SERVER["HTTP_HOST"],-3)==":80") $_SERVER["HTTP_HOST"]=substr($_SERVER["HTTP_HOST"],0,-3);
if(substr($_SERVER["HTTP_HOST"],-1)==".") $_SERVER["HTTP_HOST"]=substr($_SERVER["HTTP_HOST"],0,-1);
if($cron or $_SERVER["HTTP_HOST"]=="www.chalet.nl" or $_SERVER["HTTP_HOST"]=="www2.chalet.nl" or $_SERVER["HTTP_HOST"]=="wwwtest.chalet.nl" or $_SERVER["HTTP_HOST"]=="test.chalet.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="C")) {
	# Chalet.nl Winter
	$vars["websitetype"]=1;
	$vars["websitenaam"]="Chalet.nl";
	$vars["langewebsitenaam"]="Chalet.nl B.V.";
	$vars["seizoentype"]=1;
	$vars["website"]="C";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.chalet.nl/";
	$vars["email"]="info@chalet.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-1";
	$vars["facebook_pageid"]="156825034385110";
	$vars["twitter_user"]="ChaletNL";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["nieuwsbrief_aanbieden"]=true;
	$vars["nieuwsbrief_tijdelijk_kunnen_afmelden"]=false;
	$vars["livechat_code"]="2-PTFmcHUgtM";
	$vars["trustpilot_code"]="bd82d1c7@trustpilotservice.com";
	$vars["valt_onder_bedrijf"]=1;

	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_idl" => array(
				"title"	=> 	"iDEAL",
				"icon"	=>	"pic/payment_icons/ideal.png",
				"by"	=> 	"idl",
				"country" => array("NL")
			),
			"docdata_mrc" => array(
				"title" =>	"MrCash",
				"icon"	=>	"pic/payment_icons/mrcash.png",
				"by"	=>	"mrc",
				"country" => array("BE")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.chalet.eu" or $_SERVER["HTTP_HOST"]=="test.chalet.eu" or ($vars["lokale_testserver"] and $vars["testsite"]=="E")) {
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
	$vars["schadeverzekering_mogelijk"]=0;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	$vars["trustpilot_code"]="5aa723efa5@trustpilotservice.com";
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_vi" => array(
				"title"	=> 	"Visa",
				"icon"	=>	"pic/payment_icons/visa.png",
				"by"	=> 	"vi"
			),
			"docdata_mc" => array(
				"title"	=> 	"MasterCard",
				"icon"	=>	"pic/payment_icons/mastercard.png",
				"by"	=> 	"mc"
			),
			"docdata_idl" => array(
				"title"	=> 	"iDEAL",
				"icon"	=>	"pic/payment_icons/ideal.png",
				"by"	=> 	"idl",
				"country" => array("NL")
			),
			"docdata_mrc" => array(
				"title" =>	"Mister Cash",
				"icon"	=>	"pic/payment_icons/mrcash.png",
				"by"	=>	"mrc",
				"country" => array("BE")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.chaletonline.de" or $_SERVER["HTTP_HOST"]=="test.chaletonline.de" or ($vars["lokale_testserver"] and $vars["testsite"]=="D")) {
	# Chaletonline.de (Duitstalig)
	$vars["websitetype"]=1;
	$vars["websitenaam"]="Chaletonline.de";
	$vars["langewebsitenaam"]="Chaletonline.de";
	$vars["seizoentype"]=1;
	$vars["website"]="D";
	$vars["taal"]="de";
	$vars["websiteland"]="de";
	$vars["ttv"]="_de";
	$vars["basehref"]="https://www.chaletonline.de/";
	$vars["email"]="info@chaletonline.de";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-20";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["schadeverzekering_mogelijk"]=0;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	// $vars["trustpilot_code"]="5aa723efa5@trustpilotservice.com";
	$vars["valt_onder_bedrijf"]=1;
	// $vars["docdata_payments"] = array(
	// 	"docdata_vi" => array(
	// 		"title"	=> 	"Visa",
	// 		"icon"	=>	"pic/payment_icons/visa.png",
	// 		"by"	=> 	"vi"
	// 	),
	// 	"docdata_mc" => array(
	// 		"title"	=> 	"MasterCard",
	// 		"icon"	=>	"pic/payment_icons/mastercard.png",
	// 		"by"	=> 	"mc"
	// 	),
	// 	"docdata_idl" => array(
	// 		"title"	=> 	"iDEAL",
	// 		"icon"	=>	"pic/payment_icons/ideal.png",
	// 		"by"	=> 	"idl",
	// 		"country" => array("NL")
	// 	),
	// 	"docdata_mrc" => array(
	// 		"title" =>	"Mister Cash",
	// 		"icon"	=>	"pic/payment_icons/mrcash.png",
	// 		"by"	=>	"mrc",
	// 		"country" => array("BE")
	// 	)
	// );
} elseif($_SERVER["HTTP_HOST"]=="www.chalettour.nl" or $_SERVER["HTTP_HOST"]=="test.chalettour.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="T")) {
	# Chalettour Winter
	$vars["websitetype"]=4;
	$vars["websitenaam"]="Chalettour.nl";
	$vars["langewebsitenaam"]="Chalettour.nl";
	$vars["seizoentype"]=1;
	$vars["website"]="T";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.chalettour.nl/";
	$vars["email"]="info@chalettour.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-7";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	$vars["valt_onder_bedrijf"]=1;
} elseif($_SERVER["HTTP_HOST"]=="www.chalet.be" or $_SERVER["HTTP_HOST"]=="test.chalet.be" or ($vars["lokale_testserver"] and $vars["testsite"]=="B")) {
	# Chalet.be Winter
	$vars["websitetype"]=1;
	$vars["websitenaam"]="Chalet.be";
	$vars["langewebsitenaam"]="Chalet.be";
	$vars["seizoentype"]=1;
	$vars["website"]="B";
	$vars["taal"]="nl";
	$vars["websiteland"]="be";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.chalet.be/";
	$vars["email"]="info@chalet.be";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-3";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["nieuwsbrief_aanbieden"]=true;
	$vars["nieuwsbrief_tijdelijk_kunnen_afmelden"]=false;
	$vars["livechat_code"]="3-eex4-wCgtM";
	$vars["trustpilot_code"]="959caf69@trustpilotservice.com";
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_mrc" => array(
				"title" => 	"Mister Cash",
				"icon"	=>	"pic/payment_icons/mrcash.png",
				"by"	=>	"mrc",
				"country" => array("BE")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.zomerhuisje.nl" or $_SERVER["HTTP_HOST"]=="test.zomerhuisje.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="Z")) {
	# Zomerhuisje.nl
	$vars["websitetype"]=3;
	$vars["websitenaam"]="Zomerhuisje.nl";
	$vars["langewebsitenaam"]="Zomerhuisje.nl";
	$vars["seizoentype"]=2;
	$vars["website"]="Z";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.zomerhuisje.nl/";
	$vars["email"]="info@zomerhuisje.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-2";
	$vars["facebook_pageid"]="168449903215909";
	$vars["twitter_user"]="Zomerhuisje";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	$vars["trustpilot_code"]="47c47023@trustpilotservice.com";
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_idl" => array(
				"title"	=> 	"iDEAL",
				"icon"	=>	"pic/payment_icons/ideal.png",
				"by"	=> 	"idl",
				"country" => array("NL")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.zomerhuisje.eu" or $_SERVER["HTTP_HOST"]=="test.zomerhuisje.eu" or ($vars["lokale_testserver"] and $vars["testsite"]=="N")) {
	# Zomerhuisje.eu (NIET MEER IN GEBRUIK!)
	$vars["websitetype"]=3;
	$vars["websitenaam"]="Zomerhuisje.eu";
	$vars["langewebsitenaam"]="Zomerhuisje.eu";
	$vars["seizoentype"]=2;
	$vars["website"]="N";
	$vars["taal"]="nl";
	$vars["websiteland"]="be";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.zomerhuisje.eu/";
	$vars["email"]="info@zomerhuisje.eu";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-5";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["livechat_code"]=false;
	$vars["valt_onder_bedrijf"]=1;
} elseif($_SERVER["HTTP_HOST"]=="www.chaletsinvallandry.nl" or $_SERVER["HTTP_HOST"]=="test.chaletsinvallandry.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="V")) {
	# Chaletsinvallandry.nl
	$vars["websitetype"]=6;
	$vars["websitenaam"]="Chalets in Vallandry";
	$vars["langewebsitenaam"]="Chalets in Vallandry";
	$vars["seizoentype"]=1;
	$vars["website"]="V";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.chaletsinvallandry.nl/";
	$vars["email"]="info@chaletsinvallandry.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-10";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_idl" => array(
				"title"	=> 	"iDEAL",
				"icon"	=>	"pic/payment_icons/ideal.png",
				"by"	=> 	"idl",
				"country" => array("NL")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.chaletsinvallandry.com" or $_SERVER["HTTP_HOST"]=="test.chaletsinvallandry.com" or $_SERVER["HTTP_HOST"]=="chalet-chaletsinvallandry.web.netromtest.ro" or ($vars["lokale_testserver"] and $vars["testsite"]=="Q")) {
	# Chaletsinvallandry.com
	$vars["websitetype"]=6;
	$vars["websitenaam"]="Chalets in Vallandry";
	$vars["langewebsitenaam"]="Chalets in Vallandry";
	$vars["seizoentype"]=1;
	$vars["website"]="Q";
	$vars["taal"]="en";
	$vars["websiteland"]="en";
	$vars["ttv"]="_en";
	$vars["basehref"]="https://www.chaletsinvallandry.com/";
	$vars["email"]="info@chaletsinvallandry.com";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-9";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["schadeverzekering_mogelijk"]=0;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_vi" => array(
				"title"	=> 	"Visa",
				"icon"	=>	"pic/payment_icons/visa.png",
				"by"	=> 	"vi"
			),
			"docdata_mc" => array(
				"title"	=> 	"MasterCard",
				"icon"	=>	"pic/payment_icons/mastercard.png",
				"by"	=> 	"mc"
			),
			"docdata_idl" => array(
				"title"	=> 	"iDeal",
				"icon"	=>	"pic/payment_icons/ideal.png",
				"by"	=> 	"idl",
				"country" => array("NL")
			),
			"docdata_mrc" => array(
				"title" =>	"MrCash",
				"icon"	=>	"pic/payment_icons/mrcash.png",
				"by"	=>	"mrc",
				"country" => array("BE")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.italissima.nl" or $_SERVER["HTTP_HOST"]=="test.italissima.nl" or $_SERVER["HTTP_HOST"]=="chalet-italissima.web.netromtest.ro" or ($vars["lokale_testserver"] and $vars["testsite"]=="I")) {
	# Italissima.nl
	$vars["websitetype"]=7;
	$vars["websitenaam"]="Italissima";
	$vars["langewebsitenaam"]="Italissima";
	$vars["seizoentype"]=2;
	$vars["website"]="I";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.italissima.nl/";
	$vars["email"]="info@italissima.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-11";
	$vars["facebook_pageid"]="272671556122756";
	$vars["twitter_user"]="Italissima";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["nieuwsbrief_aanbieden"]=true;
	$vars["livechat_code"]="1-ePbASwCpnf";
	$vars["trustpilot_code"]="b69417c8@trustpilotservice.com";
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_idl" => array(
				"title"	=> 	"iDEAL",
				"icon"	=>	"pic/payment_icons/ideal.png",
				"by"	=> 	"idl",
				"country" => array("NL")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.italissima.be" or $_SERVER["HTTP_HOST"]=="test.italissima.be" or $_SERVER["HTTP_HOST"]=="chalet-italissimabe.web.netromtest.ro" or ($vars["lokale_testserver"] and $vars["testsite"]=="K")) {
	# Italissima.be
	$vars["websitetype"]=7;
	$vars["websitenaam"]="Italissima";
	$vars["langewebsitenaam"]="Italissima";
	$vars["seizoentype"]=2;
	$vars["website"]="K";
	$vars["taal"]="nl";
	$vars["websiteland"]="be";
	$vars["ttv"]="";
	$vars["basehref"]="https://www.italissima.be/";
	$vars["email"]="info@italissima.be";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-14";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=false;
	$vars["nieuwsbrief_aanbieden"]=true;
	$vars["livechat_code"]="1-ePbASwCpnf";
	$vars["trustpilot_code"]="eaacc6b4@trustpilotservice.com";
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		$vars["docdata_payments"] = array(
			"docdata_mrc" => array(
				"title" =>	"MrCash",
				"icon"	=>	"pic/payment_icons/mrcash.png",
				"by"	=>	"mrc",
				"country" => array("BE")
			)
		);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.italyhomes.eu" or $_SERVER["HTTP_HOST"]=="test.italyhomes.eu" or $_SERVER["HTTP_HOST"]=="chalet-italyhomes.web.netromtest.ro" or ($vars["lokale_testserver"] and $vars["testsite"]=="H")) {

	# Italyhomes.eu
	$vars["websitetype"]=7;
	$vars["websitenaam"]="Italyhomes";
	$vars["langewebsitenaam"]="Italyhomes";
	$vars["seizoentype"]=2;
	$vars["website"]="H";
	$vars["taal"]="en";
	$vars["websiteland"]="en";
	$vars["ttv"]="_en";
	$vars["basehref"]="https://www.italyhomes.eu/";
	$vars["email"]="info@italyhomes.eu";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-17";
	// $vars["facebook_pageid"]="272671556122756";
	// $vars["twitter_user"]="Italissima";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=0;
	$vars["schadeverzekering_mogelijk"]=0;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	$vars["trustpilot_code"]=false;
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
		// $vars["docdata_payments"] = array(
		// 	"docdata_idl" => array(
		// 		"title"	=> 	"iDEAL",
		// 		"icon"	=>	"pic/payment_icons/ideal.png",
		// 		"by"	=> 	"idl",
		// 		"country" => array("NL")
		// 	)
		// );
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.superski.nl" or $_SERVER["HTTP_HOST"]=="test.superski.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="W")) {
	# SuperSki
	$vars["websitetype"]=8;
	$vars["websitenaam"]="SuperSki";
	$vars["langewebsitenaam"]="SuperSki";
	$vars["seizoentype"]=1;
	$vars["website"]="W";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["basehref"]="https://www.superski.nl/";
	$vars["email"]="info@superski.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-13";
	$vars["facebook_pageid"]="290286354404681";
	$vars["twitter_user"]="SuperSkiNL";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["livechat_code"]=false;
	$vars["valt_onder_bedrijf"]=1;
} elseif($_SERVER["HTTP_HOST"]=="www.venturasol.nl" or $_SERVER["HTTP_HOST"]=="test.venturasol.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="X")) {
	# Venturasol Wintersport
	$vars["websitetype"]=9;
	$vars["websitenaam"]="Venturasol Wintersport";
	$vars["langewebsitenaam"]="Venturasol Wintersport";
	$vars["seizoentype"]=1;
	$vars["website"]="X";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["basehref"]="https://www.venturasol.nl/";
	$vars["email"]="info@venturasol.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-15";
	$vars["facebook_pageid"]="162914327091136";
	$vars["twitter_user"]="Venturasol_nl";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["livechat_code"]=false;
	$vars["valt_onder_bedrijf"]=1;
	// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="213.125.152.154" or $vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	// 	$vars["docdata_payments"] = array(
	// 		"docdata_idl" => array(
	// 			"title"	=> 	"iDEAL",
	// 			"icon"	=>	"pic/payment_icons/ideal.png",
	// 			"by"	=> 	"idl",
	// 			"country" => array("NL")
	// 		)
	// 	);
	// }
} elseif($_SERVER["HTTP_HOST"]=="www.venturasolvacances.nl" or $_SERVER["HTTP_HOST"]=="test.venturasolvacances.nl" or ($vars["lokale_testserver"] and $vars["testsite"]=="Y")) {
	# Venturasol Vacances
	$vars["websitetype"]=9;
	$vars["websitenaam"]="Venturasol Vacances";
	$vars["langewebsitenaam"]="Venturasol Vacances";
	$vars["seizoentype"]=1;
	$vars["website"]="Y";
	$vars["taal"]="nl";
	$vars["websiteland"]="nl";
	$vars["basehref"]="https://www.venturasolvacances.nl/";
	$vars["email"]="info@venturasolvacances.nl";
	$path="/";
	$vars["googleanalytics"]="UA-2078202-16";
	$vars["annverzekering_mogelijk"]=1;
	$vars["reisverzekering_mogelijk"]=1;
	$vars["schadeverzekering_mogelijk"]=1;
	$vars["wederverkoop"]=true;
	$vars["livechat_code"]=false;
	$vars["valt_onder_bedrijf"]=2;
} else {
	# Onbekend welke site er wordt opgevraagd
	if(ereg("chalet\.nl",$_SERVER["HTTP_HOST"])) {
		header("Location: https://www.chalet.nl/");
	} elseif(ereg("chalet\.eu",$_SERVER["HTTP_HOST"])) {
		header("Location: https://www.chalet.eu/");
	} else {
		header("Location: https://www.chalet.nl/");
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
	# Italissima / Italyhomes
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

$vars["cmspath"]="https://www.chalet.nl/";
if($vars["lokale_testserver"]) {
	$vars["basehref"]="http://".$_SERVER["HTTP_HOST"]."/chalet".$path;
	$path="/chalet".$path;
	$vars["cmspath"]=$path;
}
if($vars["acceptatie_testserver"]) {
	$vars["basehref"] = preg_replace("@^https?://www\.@","http://test.", $vars["basehref"]);
}

#
# Websites-info-array
#

$vars["websiteinfo"]["websitenaam"]["W"]="SuperSki";
$vars["websiteinfo"]["langewebsitenaam"]["W"]="SuperSki";
$vars["websiteinfo"]["email"]["W"]="info@superski.nl";
$vars["websiteinfo"]["basehref"]["W"]="https://www.superski.nl/";
$vars["websiteinfo"]["websitetype"]["W"]=8;
$vars["websiteinfo"]["verzekering_mogelijk"]["W"]=1;
$vars["websiteinfo"]["websiteland"]["W"]="nl";
$vars["websiteinfo"]["taal"]["W"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["W"]=false;
$vars["websiteinfo"]["livechat_code"]["W"]=false;
$vars["websiteinfo"]["seizoentype"]["W"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["W"]=1;

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
$vars["websiteinfo"]["seizoentype"]["E"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["E"]=0;

$vars["websiteinfo"]["websitenaam"]["D"]="Chaletonline.de";
$vars["websiteinfo"]["langewebsitenaam"]["D"]="Chalet.nl B.V. / Chaletonline.de";
$vars["websiteinfo"]["email"]["D"]="info@chaletonline.de";
$vars["websiteinfo"]["basehref"]["D"]="https://www.chaletonline.de/";
$vars["websiteinfo"]["websitetype"]["D"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["D"]=0;
$vars["websiteinfo"]["wederverkoop"]["D"]=true;
$vars["websiteinfo"]["websiteland"]["D"]="de";
$vars["websiteinfo"]["taal"]["D"]="de";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["D"]=false;
$vars["websiteinfo"]["livechat_code"]["D"]=false;
$vars["websiteinfo"]["seizoentype"]["D"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["D"]=0;

$vars["websiteinfo"]["websitenaam"]["Z"]="Zomerhuisje.nl";
$vars["websiteinfo"]["langewebsitenaam"]["Z"]="Chalet.nl B.V. / Zomerhuisje.nl";
$vars["websiteinfo"]["email"]["Z"]="info@zomerhuisje.nl";
$vars["websiteinfo"]["basehref"]["Z"]="https://www.zomerhuisje.nl/";
$vars["websiteinfo"]["websitetype"]["Z"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["Z"]=1;
$vars["websiteinfo"]["wederverkoop"]["Z"]=true;
$vars["websiteinfo"]["websiteland"]["Z"]="nl";
$vars["websiteinfo"]["taal"]["Z"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["Z"]=false;
$vars["websiteinfo"]["livechat_code"]["Z"]=false;
$vars["websiteinfo"]["seizoentype"]["Z"]=2;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["Z"]=1;

$vars["websiteinfo"]["websitenaam"]["S"]="Chalet.eu";
$vars["websiteinfo"]["langewebsitenaam"]["S"]="Chalet.nl B.V. / Chalet.eu";
$vars["websiteinfo"]["email"]["S"]="info@chalet.eu";
$vars["websiteinfo"]["basehref"]["S"]="https://www.chalet.eu/summer/";
$vars["websiteinfo"]["websitetype"]["S"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["S"]=0;
$vars["websiteinfo"]["websiteland"]["S"]="en";
$vars["websiteinfo"]["taal"]["S"]="en";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["S"]=false;
$vars["websiteinfo"]["livechat_code"]["S"]=false;
$vars["websiteinfo"]["seizoentype"]["S"]=2;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["S"]=0;

$vars["websiteinfo"]["websitenaam"]["T"]="Chalettour.nl";
$vars["websiteinfo"]["langewebsitenaam"]["T"]="Chalet.nl B.V. / Chalettour.nl";
$vars["websiteinfo"]["email"]["T"]="info@chalettour.nl";
$vars["websiteinfo"]["basehref"]["T"]="https://www.chalettour.nl/";
$vars["websiteinfo"]["websitetype"]["T"]=4;
$vars["websiteinfo"]["verzekering_mogelijk"]["T"]=1;
$vars["websiteinfo"]["wederverkoop"]["T"]=true;
$vars["websiteinfo"]["websiteland"]["T"]="nl";
$vars["websiteinfo"]["taal"]["T"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["T"]=false;
$vars["websiteinfo"]["livechat_code"]["T"]=false;
$vars["websiteinfo"]["seizoentype"]["T"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["T"]=1;

$vars["websiteinfo"]["websitenaam"]["O"]="Chalettour.nl";
$vars["websiteinfo"]["langewebsitenaam"]["O"]="Chalet.nl B.V. / Chalettour.nl";
$vars["websiteinfo"]["email"]["O"]="info@chalettour.nl";
$vars["websiteinfo"]["basehref"]["O"]="https://www.chalettour.nl/zomer/";
$vars["websiteinfo"]["websitetype"]["O"]=5;
$vars["websiteinfo"]["verzekering_mogelijk"]["O"]=1;
$vars["websiteinfo"]["wederverkoop"]["O"]=true;
$vars["websiteinfo"]["websiteland"]["O"]="nl";
$vars["websiteinfo"]["taal"]["O"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["O"]=false;
$vars["websiteinfo"]["livechat_code"]["O"]=false;
$vars["websiteinfo"]["seizoentype"]["O"]=2;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["O"]=1;

$vars["websiteinfo"]["websitenaam"]["B"]="Chalet.be";
$vars["websiteinfo"]["langewebsitenaam"]["B"]="Chalet.nl B.V. / Chalet.be";
$vars["websiteinfo"]["email"]["B"]="info@chalet.be";
$vars["websiteinfo"]["basehref"]["B"]="https://www.chalet.be/";
$vars["websiteinfo"]["websitetype"]["B"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["B"]=1;
$vars["websiteinfo"]["websiteland"]["B"]="be";
$vars["websiteinfo"]["taal"]["B"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["B"]=true;
$vars["websiteinfo"]["livechat_code"]["B"]="3-eex4-wCgtM";
$vars["websiteinfo"]["seizoentype"]["B"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["B"]=1;

$vars["websiteinfo"]["websitenaam"]["N"]="Zomerhuisje.eu";
$vars["websiteinfo"]["langewebsitenaam"]["N"]="Chalet.nl B.V. / Zomerhuisje.eu";
$vars["websiteinfo"]["email"]["N"]="info@zomerhuisje.eu";
$vars["websiteinfo"]["basehref"]["N"]="https://www.zomerhuisje.eu/";
$vars["websiteinfo"]["websitetype"]["N"]=3;
$vars["websiteinfo"]["verzekering_mogelijk"]["N"]=1;
$vars["websiteinfo"]["websiteland"]["N"]="be";
$vars["websiteinfo"]["taal"]["N"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["N"]=false;
$vars["websiteinfo"]["livechat_code"]["N"]=false;
$vars["websiteinfo"]["seizoentype"]["N"]=2;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["N"]=1;

$vars["websiteinfo"]["websitenaam"]["V"]="Chalets in Vallandry";
$vars["websiteinfo"]["langewebsitenaam"]["V"]="Chalets in Vallandry";
$vars["websiteinfo"]["email"]["V"]="info@chaletsinvallandry.nl";
$vars["websiteinfo"]["basehref"]["V"]="https://www.chaletsinvallandry.nl/";
$vars["websiteinfo"]["websitetype"]["V"]=6;
$vars["websiteinfo"]["verzekering_mogelijk"]["V"]=1;
$vars["websiteinfo"]["wederverkoop"]["V"]=true;
$vars["websiteinfo"]["websiteland"]["V"]="nl";
$vars["websiteinfo"]["taal"]["V"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["V"]=false;
$vars["websiteinfo"]["livechat_code"]["V"]=false;
$vars["websiteinfo"]["seizoentype"]["V"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["V"]=1;

$vars["websiteinfo"]["websitenaam"]["Q"]="Chalets in Vallandry";
$vars["websiteinfo"]["langewebsitenaam"]["Q"]="Chalets in Vallandry";
$vars["websiteinfo"]["email"]["Q"]="info@chaletsinvallandry.com";
$vars["websiteinfo"]["basehref"]["Q"]="https://www.chaletsinvallandry.com/";
$vars["websiteinfo"]["websitetype"]["Q"]=6;
$vars["websiteinfo"]["verzekering_mogelijk"]["Q"]=1;
$vars["websiteinfo"]["wederverkoop"]["Q"]=true;
$vars["websiteinfo"]["websiteland"]["Q"]="en";
$vars["websiteinfo"]["taal"]["Q"]="en";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["Q"]=false;
$vars["websiteinfo"]["livechat_code"]["Q"]=false;
$vars["websiteinfo"]["seizoentype"]["Q"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["Q"]=0;

$vars["websiteinfo"]["websitenaam"]["I"]="Italissima";
$vars["websiteinfo"]["langewebsitenaam"]["I"]="Chalet.nl B.V. / Italissima";
$vars["websiteinfo"]["email"]["I"]="info@italissima.nl";
$vars["websiteinfo"]["basehref"]["I"]="https://www.italissima.nl/";
$vars["websiteinfo"]["websitetype"]["I"]=7;
$vars["websiteinfo"]["verzekering_mogelijk"]["I"]=1;
$vars["websiteinfo"]["wederverkoop"]["I"]=true;
$vars["websiteinfo"]["websiteland"]["I"]="nl";
$vars["websiteinfo"]["taal"]["I"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["I"]=true;
$vars["websiteinfo"]["livechat_code"]["I"]="1-ePbASwCpnf";
$vars["websiteinfo"]["seizoentype"]["I"]=2;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["I"]=1;

$vars["websiteinfo"]["websitenaam"]["K"]="Italissima";
$vars["websiteinfo"]["langewebsitenaam"]["K"]="Chalet.nl B.V. / Italissima";
$vars["websiteinfo"]["email"]["K"]="info@italissima.be";
$vars["websiteinfo"]["basehref"]["K"]="https://www.italissima.be/";
$vars["websiteinfo"]["websitetype"]["K"]=7;
$vars["websiteinfo"]["verzekering_mogelijk"]["K"]=1;
$vars["websiteinfo"]["wederverkoop"]["K"]=false;
$vars["websiteinfo"]["websiteland"]["K"]="be";
$vars["websiteinfo"]["taal"]["K"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["K"]=true;
$vars["websiteinfo"]["livechat_code"]["K"]="1-ePbASwCpnf";
$vars["websiteinfo"]["seizoentype"]["K"]=2;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["K"]=1;

$vars["websiteinfo"]["websitenaam"]["H"]="Italyhomes";
$vars["websiteinfo"]["langewebsitenaam"]["H"]="Chalet.nl B.V. / Italyhomes";
$vars["websiteinfo"]["email"]["H"]="info@italyhomes.eu";
$vars["websiteinfo"]["basehref"]["H"]="https://www.italyhomes.eu/";
$vars["websiteinfo"]["websitetype"]["H"]=7;
$vars["websiteinfo"]["verzekering_mogelijk"]["H"]=0;
$vars["websiteinfo"]["wederverkoop"]["H"]=true;
$vars["websiteinfo"]["websiteland"]["H"]="en";
$vars["websiteinfo"]["taal"]["H"]="en";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["H"]=false;
$vars["websiteinfo"]["livechat_code"]["H"]=false;
$vars["websiteinfo"]["seizoentype"]["H"]=2;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["H"]=0;

$vars["websiteinfo"]["websitenaam"]["X"]="Venturasol Wintersport";
$vars["websiteinfo"]["langewebsitenaam"]["X"]="Venturasol Wintersport";
$vars["websiteinfo"]["email"]["X"]="info@venturasol.nl";
$vars["websiteinfo"]["basehref"]["X"]="https://www.venturasol.nl/";
$vars["websiteinfo"]["websitetype"]["X"]=9;
$vars["websiteinfo"]["verzekering_mogelijk"]["X"]=1;
$vars["websiteinfo"]["wederverkoop"]["X"]=false;
$vars["websiteinfo"]["websiteland"]["X"]="nl";
$vars["websiteinfo"]["taal"]["X"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["X"]=false;
$vars["websiteinfo"]["livechat_code"]["X"]=false;
$vars["websiteinfo"]["seizoentype"]["X"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["X"]=1;

$vars["websiteinfo"]["websitenaam"]["Y"]="Venturasol Vacances";
$vars["websiteinfo"]["langewebsitenaam"]["Y"]="Venturasol Vacances";
$vars["websiteinfo"]["email"]["Y"]="info@venturasolvacances.nl";
$vars["websiteinfo"]["basehref"]["Y"]="https://www.venturasolvacances.nl/";
$vars["websiteinfo"]["websitetype"]["Y"]=9;
$vars["websiteinfo"]["verzekering_mogelijk"]["Y"]=1;
$vars["websiteinfo"]["wederverkoop"]["Y"]=true;
$vars["websiteinfo"]["websiteland"]["Y"]="nl";
$vars["websiteinfo"]["taal"]["Y"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["Y"]=false;
$vars["websiteinfo"]["livechat_code"]["Y"]=false;
$vars["websiteinfo"]["seizoentype"]["Y"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["Y"]=1;

$vars["websiteinfo"]["websitenaam"]["C"]="Chalet.nl";
$vars["websiteinfo"]["langewebsitenaam"]["C"]="Chalet.nl B.V.";
$vars["websiteinfo"]["email"]["C"]="info@chalet.nl";
$vars["websiteinfo"]["basehref"]["C"]="https://www.chalet.nl/";
$vars["websiteinfo"]["websitetype"]["C"]=1;
$vars["websiteinfo"]["verzekering_mogelijk"]["C"]=1;
$vars["websiteinfo"]["websiteland"]["C"]="nl";
$vars["websiteinfo"]["taal"]["C"]="nl";
$vars["websiteinfo"]["nieuwsbrief_aanbieden"]["C"]=true;
$vars["websiteinfo"]["livechat_code"]["C"]="2-PTFmcHUgtM";
$vars["websiteinfo"]["seizoentype"]["C"]=1;
$vars["websiteinfo"]["schadeverzekering_mogelijk"]["C"]=1;

# Diverse vars

$vars["websites"]=array("C"=>"Chalet.nl Winter","Z"=>"Zomerhuisje.nl","W"=>"SuperSki (niet meer actief)","E"=>"Chalet.eu Engelstalig Winter","S"=>"Chalet.eu Engelstalig Zomer (niet meer actief)","T"=>"Chalettour.nl Winter","O"=>"Chalettour.nl Zomer (niet meer actief)","B"=>"Chalet.be Winter","D"=>"Chaletonline.de Duitstalig Winter","N"=>"Zomerhuisje.eu (niet meer actief)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be","H"=>"Italyhomes.eu","X"=>"Venturasol Wintersport","Y"=>"Venturasol Vacances");
$vars["websites_actief"]=array("C"=>"Chalet.nl Winter","Z"=>"Zomerhuisje.nl","E"=>"Chalet.eu Engelstalig Winter","T"=>"Chalettour.nl Winter","B"=>"Chalet.be Winter","D"=>"Chaletonline.de Duitstalig Winter","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be","H"=>"Italyhomes.eu","X"=>"Venturasol Wintersport","Y"=>"Venturasol Vacances");
$vars["websites_basehref"]=array("C"=>"https://www.chalet.nl/","Z"=>"https://www.zomerhuisje.nl/","W"=>"https://www.superski.nl/","E"=>"https://www.chalet.eu/","S"=>"https://www.chalet.nl/summer/","T"=>"https://www.chalettour.nl/","O"=>"https://www.chalettour.nl/zomer/","B"=>"https://www.chalet.be/","D"=>"https://www.chaletonline.de/","N"=>"https://www.zomerhuisje.eu/","V"=>"https://www.chaletsinvallandry.nl/","Q"=>"https://www.chaletsinvallandry.com/","I"=>"https://www.italissima.nl/","K"=>"https://www.italissima.be/","H"=>"https://www.italyhomes.eu/","X"=>"https://www.venturasol.nl/","Y"=>"https://www.venturasolvacances.nl/");
$vars["websites_inactief"]=array("S"=>true, "O"=>true, "N"=>true,"W"=>true);
#$vars["websites_basehref_siteid"]=array(1=>"https://www.chalet.nl/",2=>"https://www.superski.nl/",3=>"https://www.zomerhuisje.nl/",4=>"https://www.chalettour.nl/",5=>"https://www.chalettour.nl/zomer/",6=>"https://www.chaletsinvallandry.nl/");
#$vars["websites_wzt_siteid"]=array(1=>1,2=>1,3=>2,4=>1,5=>2,6=>1);
#$vars["websitetype_namen"]=array(1=>"Chalet.nl / Chalet.eu winter",2=>"SuperSki",3=>"Chalet.nl / Chalet.eu zomer",4=>"Chalettour.nl winter",5=>"Chalettour.nl zomer");
#$vars["wederverkoop_sites"]=array("T","O","Z");

$vars["websites_wzt"][1]=array("C"=>"Chalet.nl","E"=>"Chalet.eu (Engelstalig)","T"=>"Chalettour.nl (wederverkoop)","B"=>"Chalet.be","D"=>"Chaletonline.de Duitstalig (nog niet actief)","W"=>"SuperSki (niet meer actief)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","X"=>"Venturasol Wintersport","Y"=>"Venturasol Vacances");
$vars["websites_wzt"][2]=array("Z"=>"Zomerhuisje.nl","N"=>"Zomerhuisje.eu (niet meer actief)","S"=>"Chalet.eu Engelstalig Zomer (niet meer actief)","O"=>"Chalettour.nl Zomer (niet meer actief)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be","H"=>"Italyhomes.eu (Engelstalig)");

$vars["websites_wzt_actief"][1]=array("C"=>"Chalet.nl","W"=>"SuperSki","E"=>"Chalet.eu (Engelstalig)","T"=>"Chalettour.nl (wederverkoop)","B"=>"Chalet.be","D"=>"Chaletonline.de (Duitstalig)","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","X"=>"Venturasol Wintersport","Y"=>"Venturasol Vacances");
$vars["websites_wzt_actief"][2]=array("Z"=>"Zomerhuisje.nl","V"=>"Chalets in Vallandry (.nl)","Q"=>"Chalets in Vallandry (.com)","I"=>"Italissima.nl","K"=>"Italissima.be","H"=>"Italyhomes.eu");

#$vars["websitetype_namen_oud"]=array(1=>"Chalet.nl/.eu/.be winter",2=>"SuperSki",3=>"Zomerhuisje.nl/.eu",4=>"Chalettour.nl (wederverkoop)",5=>"Chalettour.nl zomer (niet meer actief)",6=>"Chalets in Vallandry (.nl en .com)");
$vars["websitetype_namen"]=array(1=>"Chalet.nl/.eu/.be/Chaletonline.de",2=>"SuperSki",3=>"Zomerhuisje.nl",4=>"Chalettour.nl",6=>"Chalets in Vallandry (.nl en .com)",7=>"Italissima (.nl en .be) / Italyhomes.eu",9=>"Venturasol");

# websitetype_namen koppelen aan seizoentype (1=winter, 2=zomer)
$vars["websitetype_namen_wzt"]=array(1=>1,2=>1,3=>2,4=>1,5=>2,6=>1,7=>2,9=>1);


// Acceptance-server: change URL's ("https://www" becomes "http://test")
if($vars["acceptatie_testserver"]) {
	foreach ($vars["websiteinfo"]["basehref"] as $key => $value) {
		$vars["websiteinfo"]["basehref"][$key] = preg_replace("@^https?://www\.@","http://test.",$value);
	}

	foreach ($vars["websites_basehref"] as $key => $value) {
		$vars["websites_basehref"][$key] = preg_replace("@^https?://www\.@","http://test.",$value);
	}
}

// Backup-server: change URL's ("https://www" becomes "http://www2")
if($vars["backup_server"]) {
	foreach ($vars["websiteinfo"]["basehref"] as $key => $value) {
		$vars["websiteinfo"]["basehref"][$key] = preg_replace("@^https?://www\.@","http://www2.",$value);
	}

	foreach ($vars["websites_basehref"] as $key => $value) {
		$vars["websites_basehref"][$key] = preg_replace("@^https?://www\.@","http://www2.",$value);
	}
}

// Live-test-server: change URL's ("https://www" becomes "http://wwwtest")
if($vars["wwwtest"]) {
	foreach ($vars["websiteinfo"]["basehref"] as $key => $value) {
		$vars["websiteinfo"]["basehref"][$key] = preg_replace("@^https?://www\.@","http://wwwtest.",$value);
	}

	foreach ($vars["websites_basehref"] as $key => $value) {
		$vars["websites_basehref"][$key] = preg_replace("@^https?://www\.@","http://wwwtest.",$value);
	}
}

?>