<?php

//
// database-credentials
//

// is this the acceptation-testserver?
if(preg_match("@^test\.@",$_SERVER["HTTP_HOST"]) or preg_match("@/html_test/@",$_SERVER["SCRIPT_FILENAME"])) {
	$vars["acceptatie_testserver"]=true;

	if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="37.34.56.191") {
		// IP WebTastic
		$vars["lokale_testserver_mailadres"]="testform_ss@webtastic.nl";
	} elseif($_SERVER["REMOTE_ADDR"]=="213.125.152.154") {
		// IP Chalet
		$vars["lokale_testserver_mailadres"]="bjorn@chalet.nl";
	} elseif($_SERVER["REMOTE_ADDR"]=="82.77.165.60" or $_SERVER["REMOTE_ADDR"]=="194.102.98.240") {
		$vars["lokale_testserver_mailadres"]="chalet@netrom.ro";
	} else {
		$vars["lokale_testserver_mailadres"]="testform_ss@webtastic.nl";
	}
}

if(netrom_testserver) {
	$mysqlsettings["name"]["remote"]="dbtest_chalet";	# Databasenaam bij provider
	$mysqlsettings["user"]="chalet-nl-usr";		# Username bij provider
	$mysqlsettings["password"]="m9prepHaGetr";		# Password bij provider
	$mysqlsettings["host"]="192.168.192.45";# Hostname bij provider
} else {
	$mysqlsettings["name"]["remote"]="db_chalet";	# Databasenaam bij provider
	$mysqlsettings["user"]="chaletdb";		# Username bij provider
	$mysqlsettings["password"]="kskL2K2kaQ";		# Password bij provider
	$mysqlsettings["host"]="localhost";# Hostname bij provider
}
if($vars["acceptatie_testserver"]) {
	$mysqlsettings["name"]["remote"]="dbtest_chalet"; # database-name for acceptance-server
}
$mysqlsettings["name"]["local"]="dbtest_chalet"; # database-name for testing purposes
$mysqlsettings["localhost"]="ss.postvak.net";# hostname for testing purposes


?>