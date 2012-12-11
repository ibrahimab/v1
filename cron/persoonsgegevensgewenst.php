<?php

#
# Verzoek tot invullen persoonsgegevens + doorgeven verzendmethode reisdocumenten

#
# Wordt elke dag om 12:15 uur, 14:15 uur en 16:15 uur gerund via cron
#
# /usr/bin/php /var/www/chalet.nl/html/cron/persoonsgegevensgewenst.php
#


set_time_limit(0);
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
} else {
	$unixdir="/var/www/chalet.nl/html/";
#	mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron persoonsgegevens","Cron is gestart om ".date("r"));
}
$cron=true;
include($unixdir."admin/vars.php");

echo date("d-m-Y H:i")."u.<pre>Chalet.nl persoonsgegevensgewenst-mailtjes\n\n\n";
flush();

#
# Persoonsgegevens - Gewone mailtjes
#
$db->query("SELECT boeking_id, aankomstdatum FROM boeking WHERE UNIX_TIMESTAMP(invuldatum)<'".(time()-86400)."' AND aankomstdatum_exact>'".(time()+(86400*10))."' AND aankomstdatum<=(".mktime(0,0,0,date("m"),date("d"),date("Y"))."+(86400*(mailverstuurd_persoonsgegevens_dagenvoorvertrek))) AND mailverstuurd_persoonsgegevens IS NULL AND geannuleerd=0 AND stap_voltooid=5 AND goedgekeurd=1 AND mailblokkeren_persoonsgegevens=0 AND voucherstatus=0 ORDER BY aankomstdatum;");
while($db->next_record()) {

	$gegevens=get_boekinginfo($db->f("boeking_id"));

	$gewenst=persoonsgegevensgewenst($gegevens);
	if($gewenst) {
		$mailtekst_persoonsgegevens=mailtekst_persoonsgegevens($gegevens["stap1"]["boekingid"],$gewenst);

		$mail=new wt_mail;
		$mail->fromname=$mailtekst_persoonsgegevens["fromname"];
		$mail->from=$mailtekst_persoonsgegevens["from"];
		$mail->to=$mailtekst_persoonsgegevens["to"];

		$mail->subject=$mailtekst_persoonsgegevens["subject"];
		$mail->plaintext=$mailtekst_persoonsgegevens["body"];

		echo "<hr><a href=\"".$vars["path"]."cms_boekingen.php?show=21&bt=2&archief=0&21k0=".$gegevens["stap1"]["boekingid"]."\" target=\"_blank\">Boeking ".$gegevens["stap1"]["boekingsnummer"]."</a> - ".date("d-m-Y",$gegevens["stap1"]["aankomstdatum_exact"])."<p>";
		echo "<b>".$mailtekst_persoonsgegevens["subject"]."</b><p>".wordwrap($mailtekst_persoonsgegevens["body"]);

		if($mail->to) {
			$mail->send();
		}

		# Opslaan in database
		$db2->query("UPDATE boeking SET mailverstuurd_persoonsgegevens=NOW() WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

		# Loggen
		chalet_log("persoonsgegevensgewenst-mailtje verstuurd aan ".$mail->to,false,true);

#		echo "<br>";
	}
	flush();
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") break;
}

#
# Persoonsgegevens - Reminders
#
$db->query("SELECT boeking_id, aankomstdatum FROM boeking WHERE aankomstdatum_exact>'".(time()+(86400*10))."' AND UNIX_TIMESTAMP(mailverstuurd_persoonsgegevens)<='".mktime(0,0,0,date("m"),date("d")-6,date("Y"))."' AND mailverstuurd_persoonsgegevens_reminder IS NULL AND geannuleerd=0 AND stap_voltooid=5 AND goedgekeurd=1 AND mailblokkeren_persoonsgegevens=0 AND voucherstatus=0 ORDER BY aankomstdatum;");
if($db->num_rows()) echo "Reminder \n\n";
while($db->next_record()) {

	$gegevens=get_boekinginfo($db->f("boeking_id"));

	$gewenst=persoonsgegevensgewenst($gegevens);

	if($gewenst) {
		$mailtekst_persoonsgegevens=mailtekst_persoonsgegevens($gegevens["stap1"]["boekingid"],$gewenst,true);

		$mail=new wt_mail;
		$mail->fromname=$mailtekst_persoonsgegevens["fromname"];
		$mail->from=$mailtekst_persoonsgegevens["from"];
		$mail->to=$mailtekst_persoonsgegevens["to"];

		$mail->subject=$mailtekst_persoonsgegevens["subject"];
		$mail->plaintext=$mailtekst_persoonsgegevens["body"];
		echo "<hr><a href=\"".$vars["path"]."cms_boekingen.php?show=21&bt=2&archief=0&21k0=".$gegevens["stap1"]["boekingid"]."\" target=\"_blank\">Boeking ".$gegevens["stap1"]["boekingsnummer"]."</a> - ".date("d-m-Y",$gegevens["stap1"]["aankomstdatum_exact"])."<p>";
		echo "<b>".$mailtekst_persoonsgegevens["subject"]."</b><p>".wordwrap($mailtekst_persoonsgegevens["body"]);

		if($mail->to) {
			$mail->send();
		}

		# Opslaan in database
		$db2->query("UPDATE boeking SET mailverstuurd_persoonsgegevens_reminder=NOW() WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

		# Loggen
		chalet_log("persoonsgegevensgewenst-mailtje-reminder verstuurd aan ".$mail->to,false,true);

	}
#	echo "<br>";
	flush();

	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") break;

}


#
# Verzendmethode invullen
#
# tussen 30 en 29 dagen voor vertrek versturen
#
$db->query("SELECT boeking_id, aankomstdatum FROM boeking WHERE UNIX_TIMESTAMP(invuldatum)<'".(time()-86400)."' AND aankomstdatum_exact<'".(time()+(86400*30))."' AND aankomstdatum_exact>'".(time()+(86400*29))."' AND wijzigen_dagen<=26 AND verzendmethode_reisdocumenten=0 and mailverstuurd_verzendmethode_reisdocumenten IS NULL AND geannuleerd=0 AND stap_voltooid=5 AND goedgekeurd=1 AND voucherstatus=0 ORDER BY aankomstdatum, boeking_id;");
while($db->next_record()) {

	$gegevens=get_boekinginfo($db->f("boeking_id"));

	$mailtekst=mailtekst_verzendmethode_reisdocumenten($gegevens["stap1"]["boekingid"]);

	$mail=new wt_mail;
	$mail->fromname=$mailtekst["fromname"];
	$mail->from=$mailtekst["from"];
	$mail->to=$mailtekst["to"];

	$mail->subject=$mailtekst["subject"];
	$mail->plaintext=$mailtekst["body"];

	echo "<hr>Verzendmethode reisdocumenten: <a href=\"".$vars["path"]."cms_boekingen.php?show=21&bt=2&archief=0&21k0=".$gegevens["stap1"]["boekingid"]."\" target=\"_blank\">boeking ".$gegevens["stap1"]["boekingsnummer"]."</a> - ".date("d-m-Y",$gegevens["stap1"]["aankomstdatum_exact"])."<p>\n\n";
	echo "<b>".$mailtekst["subject"]."</b><p>".wordwrap($mailtekst["body"]);

	if($mail->to and $mail->plaintext) {
		$mail->send();
	}

	# Opslaan in database
	$db2->query("UPDATE boeking SET mailverstuurd_verzendmethode_reisdocumenten=NOW() WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

	# Loggen
	chalet_log("'verzendmethode reisdocumenten'-mailtje verstuurd aan ".$mail->to,false,true);

#	echo "<br>";
	flush();
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") break;
}

?>