<?php

//
// script to autopull Git commits
//

include_once("/var/www/chalet.nl/html_test/admin/allfunctions.php");

$checkfile = "/var/www/chalet.nl/html_test/tmp/git-autopull-acceptance-test.txt";

if($argv[1]=="cron") {

	if(file_exists($checkfile)) {
		unlink($checkfile);
		passthru("cd /var/www/chalet.nl/html_test/;git fetch origin;git reset --hard origin/acceptance-test");
	}

} else {

	if($_POST["payload"]) {

		$obj = json_decode($_POST["payload"], true);

		if(preg_match("@acceptance-test@",$obj["ref"])) {
			touch($checkfile);
			wt_mail("jeroen@webtastic.nl","POST guthub",wt_dump($obj,false));
		}

	}
	echo "OK";
}


?>