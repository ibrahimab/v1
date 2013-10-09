<?php

include("/var/www/chalet.nl/html_test/admin/allfunctions.php");

if($argv[1]=="cron") {

} elseif($_POST) {
	wt_mail("jeroen@webtastic.nl","POST guthub",wt_dump($_POST,false));
}










?>