<?php

#
#
# This script is run after a database-import on the acceptance-server (on web01.chalet.nl)
#
# /usr/bin/php /var/www/chalet.nl/html_test/cron/redis_pre_calculate_all_types.php
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$temptest=true;
} else {

}
if($_SERVER["HTTP_HOST"]) {
	echo "<pre>";
}

$unixdir = dirname(dirname(__FILE__)) . "/";

$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

if ($vars["acceptatie_testserver"]) {
	echo "Acceptatie-testserver\n\n";
	sleep(1);
}

$vars["tmp_info_tonen"] = true;

$bijkomendekosten = new bijkomendekosten;
$bijkomendekosten->setRedis(new wt_redis);
$bijkomendekosten->pre_calculate_all_types(0, true);
$bijkomendekosten->store_complete_cache(1);
$bijkomendekosten->store_complete_cache(2);

// calculate vanafprijs
$vanafprijs = new vanafprijs;
$vanafprijs->set_all_types_to_calculate();
$vanafprijs->calculate_all_open_types();
