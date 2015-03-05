<?php

#
#
# Dit script wordt op elke dag om 5.00u. gerund op de server ss.postvak.net met het account webtastic.
#
# /usr/bin/php /home/webtastic/html/chalet/cron/elkenacht_testserver.php
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

} else {

}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/elkenacht_testserver.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/home/webtastic/html/chalet/";
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

// echo date("d-m-Y H:i")."u.<pre>\n\nChalet.nl elke nacht\n\n\n";
// flush();


$bijkomendekosten = new bijkomendekosten;
$bijkomendekosten->pre_calculate_all_types();



?>