<?php

use Chalet\Import\ExchangeRates\Importer as ExchangeRatesImporter;

//
// This scripts is the controller for actions that have to be run once a day.
//
// This script is run at HH:01 via a cronjob on server web01.chalet.nl with
// the account chalet01.
//
// cronjob:
//      1 * * * * /usr/bin/php /var/www/chalet.nl/html/cron/run-every-day.php
//
// Run this script manually on web01:
//      /usr/bin/php /var/www/chalet.nl/html/cron/run-every-day.php [name_test_system]
//
// Local test:
//      php cron/run-every-day.php [name_test_system]
//

set_time_limit(0);

$cron                 = true;
$geen_tracker_cookie  = true;
$boeking_bepaalt_taal = true;

$unixdir = dirname(dirname(__FILE__)) . "/";

require_once($unixdir . 'admin/vars.php');

$current_hour = date("H");

// get system to test from command line argument
if ($argv[1]) {
    $test_system = $argv[1];
} else {
    $test_system = false;
}

//
// Download exchange rates
//
// time: 10:01
//
if ((!$test_system && $current_hour == 10) || $test_system == 'exchange-rates') {

    $exchangeRatesImporter = new ExchangeRatesImporter($db);
    $exchangeRatesImporter->all();

}
