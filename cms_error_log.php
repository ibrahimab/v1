<?php

//
// for internal use: log and show php-errors
//

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
		header( "Content-Type: text/plain" );

		if( file_exists($file)) {
			$log = file_get_contents( $file );
			echo $log;
		} else {
			echo "Log ".intval( $_GET["show"] )." not found.";
		}
	}
	exit;
}

echo "OK";
