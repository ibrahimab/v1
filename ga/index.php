<?php

# Bezoekers aan %3Ft%3D doorsturen naar ?t=
if(ereg("/ga/%3Ft%3D([0-9]+)$",$_SERVER["REQUEST_URI"],$regs)) {
	header("Location: /ga/?t=".$regs[1]);
	exit;
}

include("../admin/allfunctions.php");

#	 1=>"Google AdWords algemeen",
#	 2=>"Google AdWords Oostenrijk",
#	 3=>"Google AdWords Frankrijk",
#	 4=>"Google AdWords Zwitserland",
#	 5=>"Google AdWords Snowboard",
#	 6=>"Google AdWords Aanbiedingen",
#	 7=>"Google AdWords Les Arcs",
#	 8=>"Google AdWords Banner 468x60",
#	 9=>"Tourploeg-banner",
#	10=>"Tourploeg-maillink"
#	11=>"Mailingmanager"
#	20=>"TradeTracker"

if(!$_GET["t"]) $_GET["t"]="1";
$_GET["chad"]=$_GET["t"];

header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');


# Bezoekers-statistieken opslaan
#$mysqlsettings["name"]["remote"]="chalet";	# Databasenaam bij provider
$mysqlsettings["name"]["local"]="dbtest_chalet";		# Optioneel: Databasenaam lokaal (alleen invullen indien anders dan database bij provider)
$mysqlsettings["host"]="localhost";# Hostname bij provider
$mysqlsettings["localhost"]="ss.postvak.net";# Hostname voor lokaal gebruik
#$mysqlsettings["user"]="chalet";		# Username bij provider
#$mysqlsettings["password"]="20012002";		# Password bij provider


$mysqlsettings["host"]="localhost";# Hostname bij provider
$mysqlsettings["name"]["remote"]="db_chalet";	# Databasenaam bij provider
$mysqlsettings["user"]="chaletdb";		# Username bij provider
$mysqlsettings["password"]="kskL2K2kaQ";		# Password bij provider


$mysqlsettings["halt_on_error"]="yes";		# Wat te doen bij MySQL-foutmelding bij provider ("yes" = foutmelding weergeven en stoppen, "no" = geen foutmelding en gewoon doorgaan, "mail" = geen foutmelding op het scherm maar wel een melding aan jeroen@webtastic.nl
require("../admin/class.mysql.php");
include("../admin/trackercookie.php");

#$cookieExpire=time()+(86400*30);
#setcookie("ga",$_GET["t"],$cookieExpire,"/");
#setcookie("ga_t",time(),$cookieExpire,"/");
#setcookie("ga_tp",$type[$_GET["t"]],$cookieExpire,"/");
#setcookie("ga_tp_tmp",$type[$_GET["t"]],(time()+120),"/");

if($_GET["t"]==2) {
	header("Location: /accommodaties.php?filled=1&fsg=2-0");
} elseif($_GET["t"]==3) {
	header("Location: /accommodaties.php?filled=1&fsg=1-0");
} elseif($_GET["t"]==4) {
	header("Location: /accommodaties.php?filled=1&fsg=3-0");
} elseif($_GET["t"]==5) {
	header("Location: /");
} elseif($_GET["t"]==6) {
	header("Location: /aanbiedingen.php");
} elseif($_GET["t"]==7) {
	header("Location: /accommodaties.php?filled=1&fsg=1-8");
} elseif($_GET["t"]==8) {
	header("Location: /");
} elseif($_GET["t"]==9) {
	# Tourploeg
	header("Location: http://www.zomerhuisje.nl/accommodatie/F849/");
} elseif($_GET["t"]==10) {
	# Tourploeg
	header("Location: http://www.zomerhuisje.nl/accommodatie/F849/");
} elseif($_GET["t"]==11) {
	header("Location: /");
} elseif($_GET["t"]==12) {
	header("Location: /");
} elseif($_GET["t"]==13) {
	header("Location: /");
} else {
	header("Location: /");
}

?>