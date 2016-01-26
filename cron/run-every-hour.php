<?php

use Chalet\Monitor\MissingBookingDataChecker;

//
// This scripts is the controller for actions that have to be run every hour.
//
// This script is run at HH:00 via a cronjob on server web01.chalet.nl with
// the account chalet01.
//
// cronjob:
//		0 * * * * /usr/bin/php /var/www/chalet.nl/html/cron/run-every-hour.php
//
// Run this script manually on web01:
// 		/usr/bin/php /var/www/chalet.nl/html/cron/run-every-hour.php [name_test_system]
//
// Local test:
// 		php cron/run-every-hour.php [name_test_system]
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

// Check for missing booking data
if (!$test_system || $test_system=='missing_booking_data') {

	$missingBookingDataChecker = new MissingBookingDataChecker($db);
	$missingBookingDataChecker->CheckForBookingPersonMissing();

}
