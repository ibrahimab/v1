<?php

//
// Dit script wordt op elke dag om 5.00u. gerund op de server ss.postvak.net met het account webtastic.
//
// /usr/bin/php /home/webtastic/html/chalet/cron/elkenacht_testserver.php
//

set_time_limit(0);
$unixdir = dirname(dirname(__FILE__)) . "/";

$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

$bijkomendekosten = new bijkomendekosten;
$bijkomendekosten->setRedis(new wt_redis);
$bijkomendekosten->pre_calculate_all_types(0, true);
