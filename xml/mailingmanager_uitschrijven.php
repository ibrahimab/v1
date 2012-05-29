<?php

#
# Bij uitschrijven via Mailingmanager: vinkjes bij reisbureau_user uitzetten
#

if($_GET["t"] and ($_SERVER["REMOTE_ADDR"]=="172.16.1.10" or $_SERVER["REMOTE_ADDR"]=="82.173.186.80" or $_SERVER["REMOTE_ADDR"]=="87.250.136.101")) {
	$geen_tracker_cookie=true;
	$unixdir="../";
	include("../admin/vars.php");
	
#	wt_mail("jeroen@webtastic.nl","mailingmanager_uitschrijven",wt_dump($_SERVER,false)."\n\n".wt_dump($_GET,false));
	
#	exit;
	
	if($_GET["t"]==2) {
		# agenten-nieuwsbrief (t=2)
		$setquery="mailingmanager_agentennieuwsbrief=0";
	} elseif($_GET["t"]==1) {
		# gewone nieuwsbrief (t=1)
		$setquery="mailingmanager_gewonenieuwsbrief=0";
	}
	
	if($setquery and $_GET["externlidnummer"] and $_GET["email"]) {
		$db->query("SELECT user_id FROM reisbureau_user WHERE user_id='".addslashes($_GET["externlidnummer"])."' AND email='".addslashes($_GET["email"])."';");
		if($db->next_record()) {
			$db2->query("UPDATE reisbureau_user SET ".$setquery." WHERE user_id='".addslashes($db->f("user_id"))."';");
#			wt_mail("jeroen@webtastic.nl","mailingmanager_uitschrijven",$db2->lastquery);
		}
	}
	echo "OK";
} else {
	trigger_error("onjuist remote_addr",E_USER_NOTICE);
	echo "NOT OK";
}

?>