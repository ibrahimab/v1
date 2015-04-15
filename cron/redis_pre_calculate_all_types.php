<?php

#
#
# This script is run after a database-import on the acceptance-server (on web01.chalet.nl)
#
# /usr/bin/php /var/www/chalet.nl/html_test/cron/redis_pre_calculate_all_types
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$temptest=true;
} else {

}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	echo "<pre>";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/redis_pre_calculate_all_types.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/var/www/chalet.nl/html_test/";
	// mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkeminuut_acceptatietest","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

$vars["tmp_info_tonen"] = true;

$bijkomendekosten = new bijkomendekosten;
$bijkomendekosten->pre_calculate_all_types(0, true);

?>