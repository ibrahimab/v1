<?php

//
// script to autopull Git commits
//

// webhook: http://test.chalet.nl/wt_git_autopull.php

include_once("/var/www/chalet.nl/html_test/admin/allfunctions.php");

$checkfile = "/var/www/chalet.nl/html_test/tmp/git-autopull-acceptance-test.txt";

$current_branch_file = "/var/www/chalet.nl/html_test/tmp/git-current-branch.txt";

if($argv[1]=="cron") {

	if(file_exists($checkfile)) {

		$git_branch = trim(file_get_contents($checkfile));
		unlink($checkfile);

		if(!$git_branch) {
			$git_branch = "acceptance-test";
		}

		// passthru("cd /var/www/chalet.nl/html_test/;git checkout -b ".$git_branch);
		passthru("cd /var/www/chalet.nl/html_test/;git fetch origin;git checkout ".$git_branch.";git reset --hard origin/".$git_branch);

		file_put_contents($current_branch_file, $git_branch);
	}

} else {

	if($_POST["payload"]) {

		$obj = json_decode($_POST["payload"], true);

		$current_branch = trim(file_get_contents($current_branch_file));

		if( !$current_branch ) {
			$current_branch = "acceptance-test";
		}

		// wt_mail("jeroen@webtastic.nl","POST guthub",wt_dump($obj,false)."\n\nCurrent:".$current_branch);

		if(preg_match("@".$current_branch."$@",$obj["ref"])) {
			file_put_contents($checkfile, $current_branch);
		}
	}
	echo "OK";
}

?>