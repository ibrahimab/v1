<?php


//
// XML-export van alle accommodaties t.b.v. TradeTracker
//
// ========================>>>>>>>>>>>>>>>> LET OP: bij deze file wordt een cache gebruikt! <<<<<<<<<<<<<<<<==================================
//
// Elke dag om 04:00 uur en 18:00 uur wordt de nieuwe cache aangemaakt (via cron/elkuur.php)
//
// handmatig starten aanmaken cache: /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/elkuur.php xmlopnieuw
//

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");


$xmlTradetracker = new xmlTradetracker;

if($_GET["aanbiedingen"]) {
	$xmlTradetracker->aanbieding = true;
}

$xmlTradetracker->website = $vars["website"];

$xmlTradetracker->limit = 50;

if($_GET["save_cache"]) {
	$xmlTradetracker->save_cache = true;
}

$xmlTradetracker->showXML();
