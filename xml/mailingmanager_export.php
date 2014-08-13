<?php

#
# Reisbureaus exporteren naar Mailingmanager
#

if($_GET["t"] and ($_SERVER["REMOTE_ADDR"]=="172.16.1.10" or $_SERVER["REMOTE_ADDR"]=="87.250.136.101")) {
	$geen_tracker_cookie=true;
	$unixdir="../";
	include("../admin/vars.php");

	if($_GET["t"]==2) {
		# agenten-nieuwsbrief (t=2)
		$wherequery=" AND ru.mailingmanager_agentennieuwsbrief=1";
	} else {
		# gewone nieuwsbrief (t=1)
		$wherequery=" AND ru.mailingmanager_gewonenieuwsbrief=1";
	}

	$db->query("SELECT ru.user_id, ru.email, ru.voornaam, ru.tussenvoegsel, ru.achternaam FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id AND r.actief=1".$wherequery.";");
#	echo $db->lastquery;
	if($db->num_rows()) {

		$xmlcontent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<userlist></userlist>\n";

		$xml = new SimpleXMLElement($xmlcontent);

		$info = $xml->addChild("info");
		$info->addChild("exportunixtime",time());
		$info->addChild("aantalleden",$db->num_rows());

		while($db->next_record()) {
			$user = $xml->addChild("user");
			$user->addChild("lidnummer",utf8_encode($db->f("user_id")));
			$user->addChild("actief","True");
			$user->addChild("voornaam",xml_text($db->f("voornaam"),false));
			$user->addChild("tussenvoegsel",xml_text($db->f("tussenvoegsel"),false));
			$user->addChild("achternaam",xml_text($db->f("achternaam"),false));
			if($db->f("geslacht")==1) {
				$geslacht="M";
			} elseif($db->f("geslacht")==2) {
				$geslacht="V";
			} else {
				$geslacht="";
			}
			$user->addChild("geslacht",$geslacht);
			$user->addChild("email1",utf8_encode($db->f("email")));
			$user->addChild("email2","");
		}

		header('Content-type: text/xml; charset=utf-8');
		echo $xml->asXML();
	}
} else {
	trigger_error("onjuist remote_addr",E_USER_NOTICE);
}

?>