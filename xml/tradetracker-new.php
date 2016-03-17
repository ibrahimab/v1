<?php
use Chalet\Frontend\Images\Images;
use Chalet\Frontend\Images\TypeRepository;
use Chalet\Frontend\Images\AccommodationRepository;

//
// XML-export van alle accommodaties t.b.v. TradeTracker
//
// ========================>>>>>>>>>>>>>>>> LET OP: bij deze file wordt een cache gebruikt! <<<<<<<<<<<<<<<<==================================
//
// Elke dag om 04:00 uur en 18:00 uur wordt de nieuwe cache aangemaakt (via cron/elkuur.php)
//
// handmatig starten aanmaken cache: /usr/bin/php /var/www/chalet.nl/html/cron/elkuur.php xmlopnieuw
//

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");


$imagesFetcher = new Images(new TypeRepository($vars['mongodb']['wrapper']), new AccommodationRepository($vars['mongodb']['wrapper']));
$imagesFetcher->setDefaultImage('accommodaties', '0.jpg');

$xmlTradetracker = new xmlTradetracker;
$xmlTradetracker->setImageFetcher($imagesFetcher);

if($_GET["aanbiedingen"]) {
	$xmlTradetracker->aanbieding = true;
}

$xmlTradetracker->website = $vars["website"];

if($_GET["save_cache"]) {
	$xmlTradetracker->save_cache = true;
}

$xmlTradetracker->showXML();
