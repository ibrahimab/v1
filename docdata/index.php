<?php
session_start();

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('ROOT')) define('ROOT', dirname(__FILE__));
if(!defined('SITE_ROOT')) define('SITE_ROOT', dirname(dirname(__FILE__)));

# load the share database config file
if(file_exists(SITE_ROOT . DS . "admin" . DS . "vars.php")) {
	require_once( SITE_ROOT . DS . "admin" . DS . "vars.php" );

	if(isset($vars["acceptatie_testserver"]) && ($vars["acceptatie_testserver"] == true)) {

	} else {
		//Force using https on production environment
		if($_SERVER["HTTPS"]<>"on") {
			header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
			exit;
		}
	}

} else {
	die("Can not load database config file");
}

$dd_url = $_SERVER["REQUEST_URI"];
$pos = strpos($dd_url, "?");
if($pos) $dd_url = substr($dd_url, 0, $pos);

require_once (ROOT . DS . 'library' . DS . 'request.php');

$request = new Request();

require_once (ROOT . DS . 'library' . DS . 'bootstrap.php');
