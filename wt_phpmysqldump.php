<?php

if(!isset($_SERVER["PHP_AUTH_USER"])) {
	header("WWW-Authenticate: Basic realm=\"You must Log In!\"");
	header("HTTP/1.0 401 Unauthorized");
	exit;
} elseif($_SERVER["PHP_AUTH_USER"]=="wtmysql" and $_SERVER["PHP_AUTH_PW"]=="k333232fdj2383289shaskjl3kl33i0934jjh84" and ($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="87.250.157.198" or $_SERVER["REMOTE_ADDR"]=="87.250.157.199" or $_SERVER["REMOTE_ADDR"]=="37.34.56.191" or $_SERVER["REMOTE_ADDR"]=="149.210.172.200")) {
	# okay!
} else {
	echo "Error!\npw: ".$_SERVER["PHP_AUTH_PW"]."\nREMOTE_ADDR:".$_SERVER["REMOTE_ADDR"]."\n";
	exit;
}


ini_set('zlib.output_compression', 'On');
ini_set('zlib.output_compression_level', '9');


$host="87.250.157.202";
$base="db_chalet";
$login="chaletdb";
$password="kskL2K2kaQ";

require("admin/class.phpmysqldump.php");

$sav = new phpmysqldump( $host, $login, $password, $base, "en", $link);

if($_SERVER["REMOTE_ADDR"]=="87.250.137.107") {
	$sav->max_aantal_inserts_tegelijk=100;
}

$sav->unbuffered_query=true;

if($_GET["ignorefast"]) {
	$sav->where["tarief"]="week>'".(time()-31536000)."'";
	$sav->where["tarief_personen"]="week>'".(time()-31536000)."'";
	$sav->where["cmslog"]="UNIX_TIMESTAMP(savedate)>'".(time()-86400)."'";
	$sav->where["cmslog_pagina"]="UNIX_TIMESTAMP(savedate)>'".(time()-86400)."'";
	$sav->where["beschikbaar_archief"]="UNIX_TIMESTAMP(datumtijd)>'".(time()-86400)."'";
	$sav->where["zoekstatistiek"]="UNIX_TIMESTAMP(datumtijd)>'".(time()-86400)."'";
	$sav->where["zoekstatistiek_top"]="maand>'".(date("Ym",mktime(0,0,0,date("m")-1,date("d"),date("Y"))))."'";
	$sav->where["bezoeker"]="UNIX_TIMESTAMP(gewijzigd)>'".(time()-86400)."'";
}

if(!$_GET["cron"]) {
#		$sav->where["zoekstatistiek"]="UNIX_TIMESTAMP(datumtijd)>'".(time()-2592000)."'";
#		$sav->where["zoekstatistiek_top"]="maand>'".(date("Ym",mktime(0,0,0,date("m")-1,date("d"),date("Y"))))."'";
#		$sav->where["cmslog"]="UNIX_TIMESTAMP(savedate)>'".(time()-864000)."'";
#		$sav->where["cmslog_pagina"]="UNIX_TIMESTAMP(savedate)>'".(time()-864000)."'";
}

if($_GET["cron"]) {
	if(date("H")>=2) {
		# niet alles downloaden
			$sav->where["tarief"]="week>'".(time()-63072000)."'";
			$sav->where["tarief_personen"]="week>'".(time()-63072000)."'";
#			$sav->where["zoekstatistiek"]="UNIX_TIMESTAMP(datumtijd)>'".(time()-2592000)."'";
			$sav->where["cmslog"]="UNIX_TIMESTAMP(savedate)>'".(time()-864000)."'";
			$sav->where["cmslog_pagina"]="UNIX_TIMESTAMP(savedate)>'".(time()-864000)."'";
			$sav->where["bezoeker"]="UNIX_TIMESTAMP(gewijzigd)>'".(time()-864000)."'";
	} else {
		# 's nachts de volledige database downloaden

	}
}

$sav->nettoyage();
$sav->fly=true;
$sav->compress_ok=false;
$sav->backup();

?>