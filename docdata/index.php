<?php
session_start();

//Force using https
if($_SERVER["HTTPS"]<>"on") {
//	# deze pagina altijd via https
	header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
	exit;
}

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('ROOT')) define('ROOT', dirname(__FILE__));
if(!defined('SITE_ROOT')) define('SITE_ROOT', dirname(dirname(__FILE__)));

$url = $_SERVER["REQUEST_URI"];
$pos = strpos($url, "?");
if($pos) $url = substr($url, 0, $pos);

require_once (ROOT . DS . 'library' . DS . 'request.php');

$request = new Request();

require_once (ROOT . DS . 'library' . DS . 'bootstrap.php');
