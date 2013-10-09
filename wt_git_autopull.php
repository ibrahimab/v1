<?php

include("/var/www/chalet.nl/html_test/admin/allfunctions.php");

if($argv[1]=="cron") {

} elseif($_POST["payload"]) {

	$obj = json_decode($_POST["payload"], true);

	wt_mail("jeroen@webtastic.nl","POST guthub",wt_dump($obj,false));
}










?>