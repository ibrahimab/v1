<?php

#
# Testversie?
#
if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$testsysteem=true;
} else {
	# Voorkomen dat er multiple instances van het script runnen
	$PID=`ps aux|grep xml_import.php`;
	$PID=split("\n",$PID);
	while(list($key,$value)=each($PID)) {
	        if(preg_match("/php.*xml_import_flex\.php/",$value) and !preg_match("/\/bin\/sh/",$value)) {
	        	$pidteller++;
 			}
	}
	if($pidteller>1) {
#	        echo "xml_import.php draait al\n";
	        exit;
	}
	sleep(1);
}

set_time_limit(0);
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	$unzip="/usr/bin/unzip";
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		$tmpdir="/home/webtastic/html/chalet/tmp/";
	} else {
		$tmpdir="/tmp/";
	}
} else {
	$unixdir="/var/www/chalet.nl/html/";
	$tmpdir="/var/www/chalet.nl/html/tmp/";
	$unzip="/usr/bin/unzip";
}
$cron=true;
include($unixdir."admin/vars.php");
include($unixdir."admin/vars_xmlimport.php");





?>