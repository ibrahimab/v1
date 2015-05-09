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
// mail("jeroen@webtastic.nl","Chalet-upstart git-deploy","Upstart-script draait om ".date("r"));

$unixdir = dirname(dirname(__FILE__)) . "/";


$checkfile = $unixdir."tmp/git-deploy.txt";

for($i=1; $i<=60; $i++) {

	if ( file_exists($checkfile)) {

		echo "git-deploy ".date("r")."\n\n";
		mail("jeroen@webtastic.nl","git-deploy","deploy now: ".date("r"));

		unlink($checkfile);

		passthru("cd /home/chaletnl/Website-Chalet.nl/;git fetch origin;git checkout production;git reset --hard origin/production");
		passthru("/usr/bin/sitecopy -r /home/chaletnl/.sitecopyrc -p /home/chaletnl/.sitecopy --update chalet_web01");
		passthru("/usr/bin/sitecopy -r /home/chaletnl/.sitecopyrc -p /home/chaletnl/.sitecopy --update chalet_web02");

		$auth = new OAuth2('WuSNBiog1IjRoijaag5BUaM04r3alQTrjgnlV8O4');
		$client = new Client($auth);
		$roomAPI = new RoomAPI($client);
		$msg = new Message();
		$msg->setMessage("Auto-notify: production branch has been deployed to web01.chalet.nl and web02.chalet.nl.");
		$msg->setNotify(true);
		$msg->setColor("green");
		// id 900265 = GitHub meldingen
		// id 1502695 = API-test

		$send = $roomAPI->sendRoomNotification(1502695, $msg);

	}
	sleep(1);
}

?>