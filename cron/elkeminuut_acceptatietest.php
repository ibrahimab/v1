<?php

#
#
# Dit script wordt op elke minuut gerund op de server web01.chalet.nl met het account chalet02 (acceptatie-testserver).
#
# /usr/bin/php /var/www/chalet.nl/html_test/cron/elkeminuut_acceptatietest.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$temptest=true;
} else {

}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	echo "<pre>";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/elkeminuut.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/var/www/chalet.nl/html_test/";
	// mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkeminuut_acceptatietest","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

$huidig_uur = date("H");


$bijkomendekosten = new bijkomendekosten;
$bijkomendekosten->pre_calculate_all_types(100);


?>