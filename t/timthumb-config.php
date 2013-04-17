<?php


define('FILE_CACHE_TIME_BETWEEN_CLEANS',864000);
define('FILE_CACHE_MAX_FILE_AGE',864000);
define('ALLOW_EXTERNAL',false);

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	define ('FILE_CACHE_DIRECTORY', '/tmp/timthumb');
}

?>