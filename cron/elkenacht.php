<?php

#
#
# Dit script wordt op elke dag om 3.30u. gerund op de server web01.chalet.nl met het account chalet01.
#
# /usr/bin/php /var/www/chalet.nl/html/cron/elkenacht.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

} else {

}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/elkenacht.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/var/www/chalet.nl/html/";
#	mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkuur","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

echo date("d-m-Y H:i")."u.<pre>\n\nChalet.nl elke nacht\n\n\n";
flush();


# Opruimen filesync
$db->query("DELETE FROM filesync WHERE added<(NOW() - INTERVAL 1 DAY) AND sync_finish IS NOT NULL;");



?>