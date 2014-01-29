<?php

set_time_limit(0);

if($_GET["testsite"]) {
	if($_SERVER["HTTP_HOST"]=="chalet-dev.web.netromtest.ro") {
		file_put_contents("/var/www/chalet/chalet-dev.web.netromtest.ro/tmp/testsite.txt",$_GET["testsite"]);
	} else {
		file_put_contents("/home/webtastic/html/chalet/tmp/testsite.txt",$_GET["testsite"]);
	}
	if($_GET["gotourl"]) {
		if(preg_match("/^http:\/\//",$_GET["gotourl"])) {
			header("Location: ".$_GET["gotourl"]);
			exit;
		} elseif($_GET["gotourl"]<>"cms.php") {
			header("Location: /chalet/".$_GET["gotourl"]);
			exit;
		}
	} else {
		header("Location: /chalet/");
		exit;
	}
}

if($_GET["logout"]==1) {
	setcookie("flc","_l_",time()+3600,"/");
	$_COOKIE["flc"]="_l_";
	mail("chaletmailbackup+systemlog@gmail.com","Chalet.nl uitgelogd (http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].")",date("r")."\nUitgelogd: userid ".$_COOKIE["loginuser"]["chalet"]);
}

if($_GET["delflc"]==1) {
	setcookie("flc","_l_",time()+3600,"/");
	$_COOKIE["flc"]="_l_";
	header("Location: /");
	exit;
}

$mustlogin=true;
include("admin/vars.php");

# eenmaliggecontroleerd
if($_GET["eenmaliggecontroleerd"]) {
	$_GET["meldingen"]=1;
	$db->query("UPDATE boeking SET eenmaliggecontroleerd=1 WHERE boeking_id='".addslashes($_GET["eenmaliggecontroleerd"])."';");
}

# hoogseizoen-meldingen van een leverancier uitzetten
if($_GET["hoogseizoenlev"]) {
	if($_GET["confirmed"]) {
		$db->query("UPDATE type_seizoen SET hoogseizoencontrole=0 WHERE type_id IN (SELECT type_id FROM type WHERE leverancier_id='".addslashes($_GET["hoogseizoenlev"])."');");
	}
	header("Location: ".$vars["path"]."cms.php");
	exit;
}


# CMS-taal
if($_GET["cmstaal"]) {
#	setcookie("cmstaal",$_GET["cmstaal"]);
	$_SESSION["cmstaal"]=$_GET["cmstaal"];
	header("Location: ".$vars["path"]."cms.php");
	exit;
} elseif(!$_SESSION["cmstaal"]) {
#	setcookie("cmstaal","nl");
	$_SESSION["cmstaal"]="nl";
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

# Cookie plaatsen
if($login->logged_in) {
	if(in_array($_SERVER["REMOTE_ADDR"],$vars["vertrouwde_ips"])) {
		$cookie_expire=mktime(3,0,0,date("m"),date("d"),date("Y")+1);
	} else {
		$cookie_expire=0;
	}
	if($_GET["logout"]<>1) {
		setcookie("flc",substr(md5($_SERVER["REMOTE_ADDR"]."XhjL"),0,8),$cookie_expire,"/");
	}
}

if($_GET["cmssearch"]) {
	$_GET["cmssearch"]=trim($_GET["cmssearch"]);
	if(ereg("^[A-Za-z]([0-9]{6})$",$_GET["cmssearch"],$regs)) {
		# Zoeken op boekingsnummer oude methode (1e deel)
		$db->query("SELECT boeking_id FROM boeking WHERE boekingsnummer LIKE '".addslashes(strtoupper($_GET["cmssearch"]))."%' AND CHAR_LENGTH(boekingsnummer)=16;");
		if($db->next_record()) {
			header("Location: cms_boekingen.php?show=21&21k0=".$db->f("boeking_id"));
			exit;
		}
	} elseif(ereg("^([0-9]{6})$",$_GET["cmssearch"],$regs)) {
		# Zoeken op boekingsnummer oude methode (2e deel)
		$db->query("SELECT boeking_id FROM boeking WHERE boekingsnummer LIKE '%".addslashes(strtoupper($_GET["cmssearch"]))."' AND CHAR_LENGTH(boekingsnummer)=16;");
		if($db->next_record()) {
			header("Location: cms_boekingen.php?show=21&21k0=".$db->f("boeking_id"));
			exit;
		}

		# niet gevonden: dan zoeken in garanties
		$db->query("SELECT garantie_id FROM garantie WHERE reserveringsnummer_extern='".addslashes(strtoupper($regs[1]))."';");
		if($db->next_record()) {
			header("Location: cms_garanties.php?edit=34&34k0=".$db->f("garantie_id"));
			exit;
		}
	} elseif(ereg("^([0-9]{7})$",$_GET["cmssearch"],$regs)) {
		# Zoeken op garantienummer
		$db->query("SELECT garantie_id FROM garantie WHERE reserveringsnummer_extern='".addslashes(strtoupper($regs[1]))."';");
		if($db->next_record()) {
			header("Location: cms_garanties.php?edit=34&34k0=".$db->f("garantie_id"));
			exit;
		}
	} elseif(ereg("^[A-Za-z]([0-9]{8})$",$_GET["cmssearch"],$regs) or ereg("^([0-9]{8})$",$_GET["cmssearch"],$regs)) {
		# Zoeken op boekingsnummer nieuwe methode (met voorloopletter)
		$db->query("SELECT boeking_id FROM boeking WHERE SUBSTR(boekingsnummer,2,8)='".addslashes(strtoupper($regs[1]))."' AND CHAR_LENGTH(boekingsnummer)=9;");
		if($db->next_record()) {
			header("Location: cms_boekingen.php?show=21&21k0=".$db->f("boeking_id"));
			exit;
		}
	} elseif(ereg("^[A-Za-z]?([0-9]+)$",$_GET["cmssearch"],$regs)) {
		# Zoeken op typecode
		$db->query("SELECT a.accommodatie_id, t.type_id FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($regs[1])."';");
		if($db->next_record()) {
			header("Location: cms_types.php?show=2&bc=".$_GET["bc"]."&1k0=".$db->f("accommodatie_id")."&2k0=".$db->f("type_id"));
			exit;
		}
	} elseif(ereg("^_([0-9]+)$",$_GET["cmssearch"],$regs)) {
		# Zoeken op accommodatiecode
		header("Location: cms_accommodaties.php?show=1&1k0=".$regs[1]);
		exit;
	} elseif(ereg("^boeking([0-9]+)$",$_GET["cmssearch"],$regs)) {
		# Zoeken op boekingcode
		header("Location: cms_boekingen.php?show=21&21k0=".$regs[1]);
		exit;
	} elseif(ereg("@",$_GET["cmssearch"])) {
		# Zoeken op e-mailadres
		header("Location: cms_boekingen.php?boekingsearch=".$_GET["cmssearch"]);
		exit;
	} else {
		# Zoeken op voornaam/achternaam
		header("Location: cms_boekingen.php?boekingsearch=".$_GET["cmssearch"]);
		exit;
	}
}

$layout->display_all();

?>