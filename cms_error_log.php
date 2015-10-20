<?php

//
// for internal use: log and show php-errors
//

include_once "vendor/autoload.php";

use GorkaLaucirica\HipchatAPIv2Client\Auth\OAuth2;
use GorkaLaucirica\HipchatAPIv2Client\Client;
use GorkaLaucirica\HipchatAPIv2Client\API\RoomAPI;
use GorkaLaucirica\HipchatAPIv2Client\Model\Message;

$file = "/var/www/chalet.nl/log/php_error.log";

if( $_GET["error"] ) {
	//
	// log errors
	//
	// WebTastic-server sends errors to http://www2.chalet.nl/cms_error_log.php?error=...
	//
	$error = trim( $_GET["error"] );

	$error .= "\n";

	file_put_contents($file, $error, FILE_APPEND);

	$checkfile = "/var/www/chalet.nl/log/hipchat-sent";

	// send to HipChat
	if ( !file_exists($checkfile) or filemtime($checkfile)<(time()-60) ) {

		// only non-403 errors
		if (!preg_match("@- 403 -@", $error)) {

			touch( $checkfile );

			$hipchat_msg = "New PHP-error. See <a href=\"http://www2.chalet.nl/cms_error_log.php?show=0\">http://www2.chalet.nl/cms_error_log.php?show=0</a> for details.";

			$auth = new OAuth2('eQJ1W6Cif4636SlZPRdG2AOGaniTGG8J5j5bRg5Y');
			$client = new Client($auth);
			$roomAPI = new RoomAPI($client);
			$msg = new Message();
			$msg->setMessage( $hipchat_msg );
			$msg->setNotify(true);
			$msg->setColor("red");
			$msg->setFrom("Error-tracker");


			// id 900265 = GitHub meldingen
			// id 1502695 = API-test
			// id 1274406 = PHP error-report
			$send = $roomAPI->sendRoomNotification(1274406, $msg);
		}
	}


} elseif( isset( $_GET["show"] ) ) {
	//
	// show errors
	//
	// use:
	//		http://www2.chalet.nl/cms_error_log.php?show=0 (current errorlog)
	//		http://www2.chalet.nl/cms_error_log.php?show=1 (errorlog from last week)
	//		http://www2.chalet.nl/cms_error_log.php?show=1 (errorlog from 2 weeks ago)
	//			etc... (log retention: 12 weeks)
	//
	$mustlogin=true;
	include( "admin/vars.php" );

	if( $_GET["show"]>0 ) {
		$file .= ".".intval( $_GET["show"] );
	}

	if( $login->logged_in ) {

		echo "<!DOCTYPE html>\n<html>\n<head>\n<title>Chalet.nl PHP-errorlog ".intval( $_GET["show"] )."</title>\n";
		echo "<meta name=\"robots\" content=\"noindex,nofollow\" />\n";

		echo "</head><body>";

		echo "<pre>";

		if( file_exists($file)) {
			$log = file_get_contents( $file );
			echo $log;
		} else {
			echo "Log ".intval( $_GET["show"] )." not found.";
		}
		echo "</pre>";
		echo "</body></html>";

	}
	exit;
}

echo "OK";
