<?php

/**
 * @author  Jeroen Boschman <jeroen@webtastic.nl>
 * @package Chalet
 *
 * @param string $classname
 *
 * @return bool
 */
function __autoload($classname) {

	$root = dirname(__DIR__);

	if (file_exists($root . '/admin/siteclass/siteclass.' . $classname . '.php')) {

		require_once $root . '/admin/siteclass/siteclass.' . $classname . '.php';
		return true;
	}

	if ($classname != 'MYPDF' && !preg_match('@Horde@', $classname) && substr($classname, 0, 3) != 'PHP') {

		$debug = @debug_backtrace();

		if (is_array($debug)) {

			$filename   = $debug[0]['file'];
			$linenumber = $debug[0]['line'];
		}

		trigger_error('_WT_FILENAME_' . $filename . '_WT_FILENAME__WT_LINENUMBER_' . $linenumber . '_WT_LINENUMBER_class ' . $classname . ' kan niet worden geladen', E_USER_NOTICE);
		return false;
	}
}

// first register composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// then register custom autoloader
spl_autoload_register('__autoload');