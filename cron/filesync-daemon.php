<?php


#
#
# Dit script wordt als daemon op beide webservers gedraaid met het account chalet01.
#
# /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/filesync.php test
#

/*

/etc/init/filesync.conf:


description "filesync"
author "WebTastic"

start on startup
stop on shutdown
respawn

exec sudo -u chalet01 php -f /var/www/chalet.nl/html/cron/filesync-daemon.php

*/


//

set_time_limit(0);
$unixdir = dirname(dirname(__FILE__)) . "/";

$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

touch("/tmp/last-filesync");


for($i=1; $i<=6; $i++) {

	$filesync = new filesync;
	$filesync->sync_files();



	sleep(10);
	// wt_mail("chaletmailbackup+systemlog@gmail.com","Chalet-upstart filesync","Upstart-script draait om ".date("r"));

}


?>