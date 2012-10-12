<?php

#
#
# Dit script wordt op elke dag om 12.30u. gerund op de server srv01.chalet.nl met het account chalet01.
#
# /usr/local/bin/php --php-ini /home/sites/chalet.nl/php_cli.ini /home/sites/chalet.nl/html/cron/elkedag.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

} else {
	sleep(10);
}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/elkedag.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/home/sites/chalet.nl/html/";
#	mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkuur","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

echo date("d-m-Y H:i")."u.<pre>\n\nChalet.nl elke dag\n\n\n";
flush();


#
# Opties bijboeken-mailtjes (zomer) versturen (50 dagen voor vertrek)
#

$vars["vertrekdatum_over_50_dagen"]=mktime(0,0,0,date("m"),date("d")+50,date("Y"));

# Kijken welke reisbureau_user_ids geen mailtje moeten ontvangen
$db->query("SELECT user_id FROM reisbureau_user ru, reisbureau r WHERE ru.reisbureau_id=r.reisbureau_id AND r.mailblokkeren_opties=1;");
while($db->next_record()) {
	if($reisbureau_user_blokkeren) $reisbureau_user_blokkeren.=",".$db->f("user_id"); else $reisbureau_user_blokkeren=$db->f("user_id");
}

# Kijken welke zomerboekingen een mail moeten ontvangen
$db->query("SELECT b.boekingsnummer, b.mailblokkeren_opties, b.boeking_id, b.aankomstdatum, b.aankomstdatum_exact, b.website, b.mailtekst_opties, a.naam, a.mailtekst_id, t.type_id, t.naam AS tnaam, p.naam AS plaats, l.begincode FROM boeking b, accommodatie a, type t, plaats p, land l, skigebied s WHERE b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.skigebied_id=s.skigebied_id AND p.land_id=l.land_id AND b.factuurdatum IS NOT NULL AND b.geannuleerd=0 AND b.stap_voltooid=5 AND b.goedgekeurd=1 AND b.mailverstuurd_opties IS NULL AND b.aankomstdatum>='".mktime(0,0,0,date("m"),date("d")+1,date("Y"))."' AND b.aankomstdatum<='".$vars["vertrekdatum_over_50_dagen"]."' AND a.wzt=2 AND b.mailblokkeren_opties=0".($reisbureau_user_blokkeren ? " AND (b.reisbureau_user_id IS NULL OR b.reisbureau_user_id NOT IN (".$reisbureau_user_blokkeren."))" : "")." ORDER BY b.aankomstdatum_exact, s.naam, p.naam, b.boekingsnummer;");
#echo $db->lastquery."<br>a";
if($db->num_rows()) {
	echo "De volgende zomerboekingen hebben een opties-bijboeken-mailtje ontvangen:\n\n";
	while($db->next_record()) {

		# Tekst bepalen
		$mailtekst_opties=mailtekst_opties($db->f("boeking_id"));

		if(!$mailtekst_opties["mailverstuurd_opties"]) {

			# Mail versturen
			$mail=new wt_mail;
			$mail->fromname=$mailtekst_opties["fromname"];
			$mail->from=$mailtekst_opties["from"];
			$mail->to=$mailtekst_opties["to"];

			$mail->subject=$mailtekst_opties["subject"];
			$mail->plaintext=$mailtekst_opties["body"];

			$mail->send();

			# Loggen
			chalet_log("opties-bijboeken-mailtje verstuurd aan ".$mail->to,false,true);

			# Database opslaan
			$db2->query("UPDATE boeking SET mailverstuurd_opties=NOW() WHERE boeking_id='".addslashes($db->f("boeking_id"))."';");

			echo "- ".$db->f("boekingsnummer")." - ".date("d-m-Y",$db->f("aankomstdatum_exact"))."\n";
			flush();
		}
	}
	echo "\n\n";
}

?>