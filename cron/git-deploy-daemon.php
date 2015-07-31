<?php



//
// Dit script wordt als daemon op backup.chalet.nl gedraaid met het account chaletnl.
//


/*

/etc/init/git-deploy.conf:


description "git-deploy"
author "WebTastic"

start on startup
stop on shutdown
respawn

exec sudo -u chaletnl php -f /home/chaletnl/Website-Chalet.nl/cron/git-deploy-daemon.php

*/


//

set_time_limit(0);
$unixdir = dirname(dirname(__FILE__)) . "/";

// Include "Composer" autoloader.
include $unixdir."vendor/autoload.php";

use GorkaLaucirica\HipchatAPIv2Client\Auth\OAuth2;
use GorkaLaucirica\HipchatAPIv2Client\Client;
use GorkaLaucirica\HipchatAPIv2Client\API\RoomAPI;
use GorkaLaucirica\HipchatAPIv2Client\Model\Message;

touch("/tmp/last-git-deploy");

$unixdir = dirname(dirname(__FILE__)) . "/";


$checkfile = $unixdir."tmp/git-deploy.txt";

for($i=1; $i<=60; $i++) {

	if ( file_exists($checkfile)) {

		echo "git-deploy ".date("r")."\n\n";

		unlink($checkfile);

		exec("cd /home/chaletnl/Website-Chalet.nl/;git fetch origin;git checkout production;git reset --hard origin/production", $git_output);

		exec("/usr/bin/sitecopy -r /home/chaletnl/.sitecopyrc -p /home/chaletnl/.sitecopy --list chalet_web01", $sitecopy_output1);

		exec("/usr/bin/sitecopy -r /home/chaletnl/.sitecopyrc -p /home/chaletnl/.sitecopy --update chalet_web01", $sitecopy_output2);
		exec("/usr/bin/sitecopy -r /home/chaletnl/.sitecopyrc -p /home/chaletnl/.sitecopy --update chalet_web02", $sitecopy_output3);


		$git_output = trim(implode("\n", $git_output));

		$sitecopy_output1 = trim(implode("\n", $sitecopy_output1));
		$sitecopy_output2 = trim(implode("\n", $sitecopy_output2));
		$sitecopy_output3 = trim(implode("\n", $sitecopy_output3));

		$git_commit = trim(preg_replace("@^.*HEAD is now at [a-z0-9]+ (.*)$@s", "\\1", $git_output));

		$log1 = preg_replace("@^.*These items have been changed since the last update:(.*)sitecopy: The remote.*$@s", "\\1", $sitecopy_output1);
		$log1 = trim($log1);

		$hipchat_msg = "Auto-notify: pushed production-commit(s) deployed to web01 and web02. Changed files: ".$log1;

		mail("chaletmailbackup+systemlog@gmail.com","git-deploy Chalet.nl","Deployed ".date("r")."\n\n\n".$hipchat_msg."\n\n\n".$git_output."\n\n\n".$sitecopy_output1."\n\n\n".$sitecopy_output2."\n\n\n".$sitecopy_output3);

		$auth = new OAuth2('XIqOmoqjbWaVeGl31aQf8ahExgeug8opaZ4w4Swv');
		$client = new Client($auth);
		$roomAPI = new RoomAPI($client);
		$msg = new Message();
		$msg->setMessage( $hipchat_msg );
		$msg->setNotify(true);
		$msg->setColor("green");

		// id 900265 = GitHub meldingen
		// id 1502695 = API-test
		$send = $roomAPI->sendRoomNotification(900265, $msg);

	}
	sleep(1);
}

?>