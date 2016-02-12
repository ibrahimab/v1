<?php

//
// database-credentials
//

/*

db01.chalet.nl/87.250.157.200 (Echte IP voor database server 1)
db02.chalet.nl/87.250.157.201 (Echte IP voor database server 2)
87.250.157.202 (Virtuele IP voor CRUD)

*/


$mysqlsettings["name"]["remote"] = "db_chalet";
$mysqlsettings["user"]           = "chaletdb";
$mysqlsettings["password"]       = "kskL2K2kaQ";

if(defined("wt_server_name") and (wt_server_name=="backup" or wt_server_name=="dev")) {
	// backup-server / dev-server
	$mysqlsettings["host"]="localhost";

	if(wt_server_name=="dev") {
		$mysqlsettings["name"]["remote"]="dbtest_chalet";
	}

} else {
	// all other servers
	$mysqlsettings["host"]="87.250.157.202";# Hostname bij provider
}

if (true === $vars["acceptatie_testserver"] || true === $vars['legacy_server']) {
	$mysqlsettings["name"]["remote"]="dbtest_chalet"; # database-name for acceptance-server
}
$mysqlsettings["name"]["local"]="dbtest_chalet"; # database-name for testing purposes
$mysqlsettings["localhost"]="ss.postvak.net";# hostname for testing purposes
