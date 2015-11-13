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

$rebuild_branch_list_file = $unixdir."tmp/git-rebuild-branch-list.txt";
$branch_list_file = $unixdir."tmp/git-branch-list.txt";

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

	if(file_exists($rebuild_branch_list_file)) {
		//
		// rebuild the branch list
		//

		// git command to fetch all changes
		exec("cd /var/www/chalet.nl/html_test/;git fetch --all");

		// git command to clean-up outdated references
		exec("cd /var/www/chalet.nl/html_test/;git remote prune origin");

		// git command to list all branches
		exec("cd /var/www/chalet.nl/html_test/;git branch -r | awk -F ' +' '! /\(no branch\)/ {print $2}'", $output);

		// convert and clean-up output
		$git_branches = implode("\n", $output);
		$git_branches = preg_replace("@origin/@", "", $git_branches);

		// save contents to tmp/git-branch-list.txt
		file_put_contents($branch_list_file, $git_branches);

		// delete rebuild-file
		@unlink($rebuild_branch_list_file);

	}

} else {

	if($_POST["payload"]) {

		$obj = json_decode($_POST["payload"], true);

		if( $_GET["git-deploy"] ) {

			if(preg_match("@production$@",$obj["ref"])) {
				touch($checkfile);
			}

		} else {

			touch($rebuild_branch_list_file);

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