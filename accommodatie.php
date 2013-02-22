<?php

$vars["jquery_fancybox"]=true;
$vars["verberg_linkerkolom"]=true;
$vars["page_with_tabs"]=true;
$id="toonaccommodatie";

# Google Maps
$vars["googlemaps"]=true;
$onload="initialize_googlemaps();";

#$vars["jquery_scrollto"]=true;
include_once "admin/vars.php";

if($_POST["ookbeschikbaarkeuze"]) {
	$location=eregi_replace("/".txt("menu_accommodatie")."/[a-zA-Z0-9]+/","/".txt("menu_accommodatie")."/".urlencode($_POST["ookbeschikbaarkeuze"])."/",$_SERVER["REQUEST_URI"]);
	header("Location: ".$location);
	exit;
}

if($_GET["accid"]) {
	# verwijzing uit htaccess (bijv. http://www.zomerhuisje.nl/vakantiehuizen/o7164/Vakantiepark-Schonleitn-begane-grond) goed afhandelen
	$url[0]=$_GET["accid"];

	# $_GET["accid"] hierna wissen
	unset($_GET["accid"]);
	if($_SERVER["QUERY_STRING"]) {
		$_SERVER["QUERY_STRING"]=preg_replace("/accid=[a-z][0-9]{1,}&?/","",$_SERVER["QUERY_STRING"]);
	}
}

if($url[0]) {

# 	uitgezet (op 16-08-2012 trad hierbij ineens een oneindige redirect op)
#	if(substr($_SERVER["REQUEST_URI"],-1)<>"/" and !eregi("\?",$_SERVER["REQUEST_URI"]) and !eregi("#",$_SERVER["REQUEST_URI"]) and !$url[1] and !$url[2] and !$_GET["accid"]) {
#		header("Location: ".$_SERVER["REQUEST_URI"]."/",true,301);
#		exit;
#	}

	if(eregi("^([A-Z]{1,2})([0-9]+)",$url[0],$regs)) {
		$begincode=$regs[1];
		$typeid=$regs[2];
	} elseif(ereg("^([0-9]+)$",$url[0],$regs) and $vars["taal"]=="nl") {
		$db->query("SELECT l.begincode, t.type_id FROM type t, land l, plaats p, accommodatie a WHERE l.land_id=p.land_id AND t.accommodatie_id=a.accommodatie_id AND p.plaats_id=a.plaats_id AND t.oude_accommodatie_id='".addslashes($regs[1])."';");
		if($db->next_record()) {
			header("Location: ".$path.txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/",true,301);
			exit;
		}
#	} elseif(eregi("^([a-z]{1,2})([0-9]+)",$url[0],$regs) and !$_GET["accid"]) {
#		# Accommodatiecode met kleine letters (f1234) doorsturen (naar F1234)
#		header("Location: ".$path.txt("menu_accommodatie")."/".strtoupper($regs[1]).$regs[2]."/",true,301);
#		exit;
	}
	$db->query("SELECT a.accommodatie_id, a.naam, a.wzt, t.naam".$vars["ttv"]." AS tnaam, t.websites, a.toonper, a.soortaccommodatie, p.naam AS plaats, l.begincode, l.naam".$vars["ttv"]." AS land, s.naam AS skigebied, s.skigebied_id, a.tonen, t.tonen AS ttonen, t.optimaalaantalpersonen, t.maxaantalpersonen FROM accommodatie a, plaats p, land l, type t, skigebied s WHERE p.skigebied_id=s.skigebied_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND t.type_id='".addslashes($typeid)."' AND t.websites LIKE '%".$vars["website"]."%' AND a.plaats_id=p.plaats_id;");
	if($db->next_record()) {
		$title["toonaccommodatie"]=htmlentities(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")]))." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." ".txt("pers").") - ".$db->f("plaats");
		if($db->f("tonen")==1 and $db->f("ttonen")==1) {
			$acc_aanwezig=true;
			$temp_accid=$db->f("accommodatie_id");
			$toonper=$db->f("toonper");
			if($db->f("begincode")<>strtoupper($begincode)) {
				header("Location: ".$path.txt("menu_accommodatie")."/".$db->f("begincode").$typeid."/",true,301);
				exit;
			}
			if($vars["websitetype"]==6) {
				$vars["seizoentype"]=$db->f("wzt");
			}

			if($vars["websitetype"]==7) {
				# topfoto bepalen
				$file="pic/cms/skigebieden_topfoto/".$db->f("skigebied_id").".jpg";

				if(file_exists($file)) {
					$vars["italissima_topfoto"]=$file;
				}
			}
		}

		if($vars["websitetype"]==6) {
			# canonical voor ChaletsinVallandry
			$vars["canonical"]=$vars["basehref"].txt("menu_accommodatie")."/".$url[0]."/";
		} elseif($vars["website"]=="Z" and preg_match("/I/",$db->f("websites"))) {
			# Italiaanse accommodaties op Zomerhuisje: canonical naar www.italissima.nl
			$vars["canonical"]=seo_acc_url($url[0],$db->f("soortaccommodatie"),$db->f("naam"),$db->f("tnaam"));
			$vars["canonical"]=str_replace("www.zomerhuisje.nl","www.italissima.nl",$vars["canonical"]);
		} elseif($vars["website"]=="W" and preg_match("/C/",$db->f("websites"))) {
			# Accommodaties op SuperSki die ook op Chalet.nl beschikbaar zijn: canonical naar www.chalet.nl
			$vars["canonical"]=seo_acc_url($url[0],$db->f("soortaccommodatie"),$db->f("naam"),$db->f("tnaam"));
			$vars["canonical"]=str_replace("www.superski.nl","www.chalet.nl",$vars["canonical"]);
		} else {
			# canonical met accommodatienaam is URL bepalen
			$vars["canonical"]=seo_acc_url($url[0],$db->f("soortaccommodatie"),$db->f("naam"),$db->f("tnaam"));
		}

		if($vars["taal"]=="en") {
			$meta_description=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." - ".ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." for ".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? " to ".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? "person" : "persons")." in ".$db->f("plaats").", ".$db->f("skigebied").", ".$db->f("land");
		} else {
			$meta_description=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." - ".ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." voor ".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? " tot ".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? "persoon" : "personen")." in ".$db->f("plaats").", ".$db->f("skigebied").", ".$db->f("land");
		}

#		$breadcrumbs[txt("menu_skigebieden").".php"]=ucfirst(txt("menu_skigebieden"));
		if($vars["websitetype"]==7) {
			$breadcrumbs["bestemmingen.php"]="Bestemmingen";
		} else {
			$breadcrumbs[txt("menu_land")."/".wt_convert2url($db->f("land"))."/"]=$db->f("land");
		}
		$breadcrumbs[txt("menu_skigebied")."/".wt_convert2url($db->f("skigebied"))."/"]=$db->f("skigebied");
		$breadcrumbs[txt("menu_plaats")."/".wt_convert2url($db->f("plaats"))."/"]=$db->f("plaats");
		$breadcrumbs["last"]=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam");
	}
} else {
#	if($_SERVER["HTTP_REFERER"]) trigger_error("accommodatie niet beschikbaar",E_USER_NOTICE);
	header("Location: ".$path.txt("menu_accommodaties").".php");
	exit;
}

if($acc_aanwezig) {
	$teller=0;
	$cookie_doorlopen=@split(",",$vars["last_acc_bezoeker"]);
	while(list($key,$value)=@each($cookie_doorlopen)) {
		if($value>0 and (count($cookie_doorlopen)-$teller)<=20) {
			if($value<>$typeid) {
				if($newcookie) $newcookie.=",".$value; else $newcookie=$value;
			}
		}
		$teller++;
	}
	if($newcookie) $newcookie.=",".$typeid; else $newcookie=$typeid;
	if($_COOKIE["sch"] and $newcookie and $newcookie<>$vars["last_acc_bezoeker"]) {
		$db->query("UPDATE bezoeker SET last_acc='".addslashes($newcookie)."', gewijzigd=NOW() WHERE bezoeker_id='".addslashes($_COOKIE["sch"])."';");
	}
} else {
	#
	# Accommodatie niet gevonden
	#

	# Kijken of er een doorsturen_naar_type_id van toepassing is
	$db->query("SELECT t.doorsturen_naar_type_id FROM type t, accommodatie a WHERE t.doorsturen_naar_type_id IS NOT NULL AND t.doorsturen_naar_type_id<>'' AND t.type_id='".addslashes($typeid)."' AND t.websites LIKE '%".$vars["website"]."%' AND t.accommodatie_id=a.accommodatie_id;");
	if($db->next_record()) {
		header("Location: ".$path.txt("menu_accommodatie")."/".$db->f("doorsturen_naar_type_id")."/".($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : ""));
		exit;
	}
#	header("HTTP/1.0 404 Not Found");
	$robot_noindex=true;
	unset($vars["canonical"]);

	if($vars["website"]=="Z" or $vars["website"]=="N") {
		# Alle Italiaanse accommodaties die niet worden gevonden doorsturen naar Italissima
		if(strtolower($begincode)=="i") {

			$db->query("SELECT type_id FROM view_accommodatie WHERE type_id='".addslashes($typeid)."' AND atonen=1 AND ttonen=1 AND websites LIKE '%I%';");
			if($db->next_record()) {
				header("Location: http://www.italissima.nl".$_SERVER["REQUEST_URI"],true,301);
				exit;
			} else {
				header("Location: ".$vars["path"],true,301);
				exit;
			}
		} else {
			#
			# Tijdelijk: bij Zomerhuisje alle niet gevonden accommodaties doorsturen naar de homepage (vanwege koerswijziging: alleen berg-accommodaties)
			# 26-10-2012
			#
			header("Location: ".$vars["path"],true,301);
			exit;
		}
	}
}


include "content/opmaak.php";

?>