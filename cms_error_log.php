<?php


$file = "tmp/php_error.log";

if( $_GET["error"] ) {

	$error = trim($_GET["error"]);

	$error .= "\n";

	file_put_contents($file, $error, FILE_APPEND);

}

echo "OK";
