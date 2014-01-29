<?php

//
// database-credentials
//

if(netrom_testserver) {
	$mysqlsettings["name"]["remote"]="chalet.chalet-nl.dev";	# Databasenaam bij provider
	$mysqlsettings["user"]="chaletuser";		# Username bij provider
	$mysqlsettings["password"]="cHekE4Rutr5t";		# Password bij provider
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