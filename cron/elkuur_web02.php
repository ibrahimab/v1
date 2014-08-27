<?php

#
#
# Dit script wordt op elk heel uur gerund op de server web02.chalet.nl met het account chalet01.
#
# /usr/bin/php /var/www/chalet.nl/html/cron/elkuur.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

} else {

}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/elkuur_web02.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/var/www/chalet.nl/html/";
	// mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkuur web02","Cron is gestart om ".date("r"));
	// mail("jeroen@webtastic.nl","Chalet-cron elkuur web02","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

echo date("d-m-Y H:i")."u.<pre>\n\nChalet.nl elke uur web02\n\n\n";
flush();


for($i=1;$i<=1;$i++) {

	$mail=new wt_mail;
	$mail->fromname="Chalet.be";
	$mail->from="info@chalet.be";
	$mail->returnpath="info@chalet.be";

	# geen BCC-trackmail
	$mail->send_bcc=false;

	$mail->subject="Testmail vanaf web02 ".date("H:i");

	$mail->plaintext="";

	$mail->html_top="";
	$mail->html="<B>Hallo, dit is een test.</B><br/>".date("H:i");
	$mail->html_bottom="";

	$mail->toname="Jeroen Boschman";
	$mail->to="boschman@outlook.com";
	$mail->send();

	$mail->toname="Jeroen Boschman";
	$mail->to="jeroen_boschman@hotmail.com";
	$mail->send();

	$mail->toname="Jeroen Boschman";
	$mail->to="boschman@live.nl";
	$mail->send();

	$mail->toname="Robert Jansen";
	$mail->to="fastjansen@hotmail.com";
	$mail->send();

	sleep(10);
	$mail->toname="Jeroen Boschman";
	$mail->to="boschman@outlook.com";
	$mail->send();


	// sleep(90);

}


?>