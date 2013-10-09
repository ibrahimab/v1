<?php

//
// script to autopull Git commits
//

include("/var/www/chalet.nl/html_test/admin/allfunctions.php");

$checkfile = "/var/www/chalet.nl/html_test/tmp/git-autopull-acceptance-test.txt";

if($argv[1]=="cron") {

	if($argv[2]=="sleep") {
		sleep(30);
	}

	if(file_exists($checkfile)) {
		unlink($checkfile);
		system("cd /var/www/chalet.nl/html_test/;git fetch origin;git reset --hard origin/acceptance-test");
	}

} elseif($_POST["payload"]) {

	$obj = json_decode($_POST["payload"], true);

	if(preg_match("@acceptance-test@",$obj["ref"])) {
		touch($checkfile);
	}

	wt_mail("jeroen@webtastic.nl","POST guthub",wt_dump($obj,false));
} else {
	wt_mail("jeroen@webtastic.nl","lege POST guthub",wt_dump($_POST,false));
}

echo "OK";

?>