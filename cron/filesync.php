<?php


#
#
# Dit script wordt op elke 10 seconden gerund op beide webservers met het account chalet01.
#
# /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/filesync.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$temptest=true;
} else {

}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	echo "<pre>";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/filesync.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/var/www/chalet.nl/html/";
#	mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron filesync","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");


for($i=1; $i<=20; $i++) {

	$filesync = new filesync;
	$filesync->sync_files();

	sleep(8);
}


?>