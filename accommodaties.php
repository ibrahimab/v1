<?php

include("admin/vars.php");

trigger_error("accommodaties.php opgevraagd",E_USER_NOTICE);

if(ereg("/accommodaties\.php",$_SERVER["REQUEST_URI"])) {
	$goto=ereg_replace("/accommodaties\.php","/zoek-en-boek.php",$_SERVER["REQUEST_URI"]);
	header("Location: ".$goto,true,301);
	exit;
}

if(ereg("/accommodations\.php",$_SERVER["REQUEST_URI"])) {
	$goto=ereg_replace("/accommodations\.php","/search-and-book.php",$_SERVER["REQUEST_URI"]);
	header("Location: ".$goto,true,301);
	exit;
}

header("Location: ".$vars["path"]."zoek-en-boek.php",true,301);
exit;

if($_GET["fzt"]=="-- ".html("trefwoord","index")." --") {
	$_GET["fzt"]="";
}

if(ereg("^[A-Za-z]([0-9]+)$",trim($_GET["fzt"]),$regs)) {
	$db->query("SELECT t.type_id, l.begincode FROM type t, accommodatie a, plaats p, land l WHERE t.type_id='".addslashes($regs[1])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id;");
	if($db->next_record()) {
		header("Location: ".$path."accommodatie/".$db->f("begincode").$db->f("type_id")."/");
		exit;
	}
}

# Zoekopdracht_id aanmaken (en plaatsen in $_GET["z"])
if($_GET["filled"]) {
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or (!$voorkant_cms and !in_array($_SERVER["REMOTE_ADDR"],$vars["vertrouwde_ips"]))) {
		if($_GET["z"]) {
			$zoekopdrachtid=intval($_GET["z"]);
		} else {
			$db->query("INSERT INTO zoekstatistiek SET wzt='".$vars["seizoentype"]."', website='".$vars["website"]."', url='".addslashes("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."', datumtijd=NOW();");
			if($db->insert_id()) {
				header("Location: ".$_SERVER["REQUEST_URI"]."&z=".$db->insert_id());
				exit;
			}
		}
	}
}

if($_GET["fsg"]>0) {
	$landgebied=explode("-",$_GET["fsg"]);
	if($landgebied[1]>0) {
		$db->query("SELECT naam FROM skigebied WHERE skigebied_id='".addslashes($landgebied[1])."';");
		if($db->next_record()) {
			$zoekresultaten_title.=$db->f("naam");
		}
	}

	$db->query("SELECT naam".$vars["ttv"]." AS naam FROM land WHERE land_id='".addslashes($landgebied[0])."';");
	if($db->next_record()) {
		if($zoekresultaten_title) $zoekresultaten_title.=" - ".$db->f("naam"); else $zoekresultaten_title=$db->f("naam");
	}
#	addwhere("l.land_id='".addslashes($landgebied[0])."'");
#	if($landgebied[1]>0) addwhere("p.skigebied_id='".addslashes($landgebied[1])."'");
}

if($zoekresultaten_title) {
	$title["accommodaties"].=" - ".$zoekresultaten_title;
}

include "content/opmaak.php";

?>