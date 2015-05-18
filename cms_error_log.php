<?php


$file = "/var/www/chalet.nl/log/php_error.log";

if( $_GET["error"] ) {

	$error = trim($_GET["error"]);

	$error .= "\n";

	file_put_contents($file, $error, FILE_APPEND);

} elseif( $_GET["show"] ) {

	$mustlogin=true;
	include("admin/vars.php");

	if( $login->logged_in ) {
		$log = file_get_contents( $file );
		echo $log;
	}
	exit;
}

echo "OK";
