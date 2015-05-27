<?php

//
// script to autopull Git commits
//

// webhook 1:
// to deploy various branches to test.chalet.nl
// url: http://test.chalet.nl/wt_git_autopull.php


// webhook 2:
// to deploy production branch to live-servers web01.chalet.nl and web02.chalet.nl
// url: http://www2.chalet.nl/wt_git_autopull.php?git-deploy=1


$unixdir = dirname(__FILE__) . "/";

include_once($unixdir."admin/allfunctions.php");

if( $_GET["git-deploy"]==1 ) {
	$checkfile = "/home/chaletnl/Website-Chalet.nl/tmp/git-deploy.txt";
} else {
	$checkfile = $unixdir."tmp/git-autopull-acceptance-test.txt";
}

$current_branch_file = $unixdir."tmp/git-current-branch.txt";

if($argv[1]=="cron") {

	if(file_exists($checkfile)) {

		$git_branch = trim(file_get_contents($checkfile));
		unlink($checkfile);

		if(!$git_branch) {
			$git_branch = "acceptance-test";
		}

		passthru("cd /var/www/chalet.nl/html_test/;git fetch --all;git checkout ".$git_branch.";git reset --hard origin/".$git_branch);

		file_put_contents($current_branch_file, $git_branch);
	}

} else {

	if($_POST["payload"]) {

		$obj = json_decode($_POST["payload"], true);

		if( $_GET["git-deploy"] ) {

			if(preg_match("@production$@",$obj["ref"])) {
				touch($checkfile);
			}

		} else {

			$current_branch = trim(file_get_contents($current_branch_file));

			if( !$current_branch ) {
				$current_branch = "acceptance-test";
			}

			if(preg_match("@".$current_branch."$@",$obj["ref"])) {
				file_put_contents($checkfile, $current_branch);
				chmod($checkfile, 0666);
			}
		}
	}
	echo "OK";
}

?>