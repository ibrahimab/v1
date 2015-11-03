<?php

//
// database-credentials
//

/*

db01.chalet.nl/87.250.157.200 (Echte IP voor database server 1)
db02.chalet.nl/87.250.157.201 (Echte IP voor database server 2)
87.250.157.202 (Virtuele IP voor CRUD)

*/


if(netrom_testserver) {
	$mysqlsettings["name"]["remote"]="chalet.chalet-nl.dev";	# Databasenaam bij provider
	$mysqlsettings["user"]="chaletuser";		# Username bij provider
	$mysqlsettings["password"]="cHekE4Rutr5t";		# Password bij provider
	$mysqlsettings["host"]="192.168.192.45";# Hostname bij provider
} else {
	$mysqlsettings["name"]["remote"]="db_chalet";	# Databasenaam bij provider
	$mysqlsettings["user"]="chaletdb";		# Username bij provider
	$mysqlsettings["password"]="kskL2K2kaQ";		# Password bij provider
	if(defined("wt_server_name") and (wt_server_name=="backup" or wt_server_name=="dev")) {
		// backup-server / dev-server
		$mysqlsettings["host"]="localhost";# Hostname bij provider

		if(wt_server_name=="dev") {
			$mysqlsettings["name"]["remote"]="dbtest_chalet";	# Databasenaam bij provider
		}

	} else {
		// all other servers
		$mysqlsettings["host"]="87.250.157.202";# Hostname bij provider
	}
}
if($vars["acceptatie_testserver"]) {
	$mysqlsettings["name"]["remote"]="dbtest_chalet"; # database-name for acceptance-server
}
$mysqlsettings["name"]["local"]="dbtest_chalet"; # database-name for testing purposes
$mysqlsettings["localhost"]="ss.postvak.net";# hostname for testing purposes
