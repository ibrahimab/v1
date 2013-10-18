<?php

/** Check if environment is development and display errors **/

function setReporting() {
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('display_errors','On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
	}
}

/** Check for Magic Quotes and remove them **/

function stripSlashesDeep($value) {
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}

function removeMagicQuotes() {
	if ( get_magic_quotes_gpc() ) {
		$_GET    = stripSlashesDeep($_GET   );
		$_POST   = stripSlashesDeep($_POST  );
		$_COOKIE = stripSlashesDeep($_COOKIE);
	}
}

/** Check register globals and remove them **/

function unregisterGlobals() {
	if (ini_get('register_globals')) {
		$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		foreach ($array as $value) {
			foreach ($GLOBALS[$value] as $key => $var) {
				if ($var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}
}

/** Main Call Function **/

function callHook() {
	global $url;
	global $default;
	global $request;

	$queryString = array();

	if (!isset($url)) {
		$controller = $default['controller'];
		$action = $default['action'];
	} else {
		$url = routeURL($url);
		$urlArray = array();
		$urlArray = explode("/",$url);

		array_shift($urlArray); // remove empty value
		array_shift($urlArray); // remove docdata

		$controller = ((isset($urlArray[0]) && !empty($urlArray[0])) ? $urlArray[0] : 'index');
		array_shift($urlArray);
		if (isset($urlArray[0])) {
			$action = (!empty($urlArray[0]) ? $urlArray[0] : 'index');
			array_shift($urlArray);
		} else {
			$action = 'index'; // Default Action
		}
		$queryString = $urlArray;
	}

	$controllerName = ucfirst($controller).'Controller';

	$dispatch = new $controllerName($controller,$action);
	$action .= "Action";

	$request->setQueryString($queryString);

	if ((int)method_exists($controllerName, $action)) {
		call_user_func_array(array($dispatch,$action),$queryString);
	} else {
		/* Error Generation Code Here */
	}
}

/** Routing **/

function routeURL($url) {
	global $routing;

	foreach ( $routing as $pattern => $result ) {
		if ( preg_match( $pattern, $url ) ) {
			return preg_replace( $pattern, $result, $url );
		}
	}

	return ($url);
}

/** Autoload any classes that are required **/

function __autoload($className) {
	if (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.php')) {
		require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . $className . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . $className . '.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php');
	} else {
		/* Error Generation Code Here */
	}

	$file = ROOT . DS . 'library' . DS . 'lib-monolog' . DS . strtr($className, '\\', '/') . '.php';
	if (file_exists($file)) {
		require $file;
		return true;
	}

	if(strpos($className, "_") !== false) {
		loadDirectories($className);
	}
}

function loadDirectories($className) {
	$parts = explode("_", $className);
	$path = implode(DS, $parts);
	if (file_exists(ROOT . DS . $path . '.php') ) {
		require_once(ROOT . DS . $path . '.php');
	}
}

function __($text) {
	return $text;
}

include_once( SITE_ROOT . DS . "admin" . DS . "class.mysql.php" );

setReporting();
removeMagicQuotes();
unregisterGlobals();
callHook();