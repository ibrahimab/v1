<?php

include("admin/vars.php");


// // echo "TT3";
// phpinfo();

// exit;


$mail=new wt_mail;
$mail->toname="Jeroen";
$mail->to="boschman@gmail.com";
$mail->to="check-auth-jeroen=webtastic.nl@verifier.port25.com";
// $mail->to="jeroen_boschman@hotmail.com";
$mail->subject="Testmail ".date("H:i");

$mail->plaintext="Hallo";

$mail->html_top="";
$mail->html="<B>Hallo</B>";
$mail->html_bottom="";

$mail->test = false;

$mail->mail_proxy=true;

foreach ($vars["websiteinfo"]["email"] as $key => $value) {

	if(!$vars["websites_inactief"][$key]) {
		$mail->fromname=$vars["websiteinfo"]["websitenaam"][$key];
		$mail->from=$value;
		$mail->returnpath=$value;

		$mail->send();

		echo "verzonden aan: ".$value."<br/>\n";

		// exit;

	}


}


echo "OK";

exit;

// if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
// 	$filesync = new filesync;
// 	$filesync->sync_files();
// }

// wt_mail("check-auth-jeroen=webtastic.nl@verifier.port25.com","Testmail anti-spammaatregelen","Testmail anti-spammaatregelen", "info@chalet.nl", "Chalet.nl");
// wt_mail("sa-test@sendmail.net","Testmail anti-spammaatregelen","Testmail anti-spammaatregelen", "wouter@chalet.nl", "Chalet.nl");


// echo "Test 1";
// if(defined("wt_server_name")) {
// 	echo "NAME:".wt_server_name;
// }
phpinfo();
// http://wwwtest.chalet.nl/wtphp.php
exit;

// echo htmlspecialchars("Hé €", ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');




// $mailadres="bjorn@chalet.nl";
// $naam="Bjorn den Blanken";

// $mailadres="jeroen@webtastic.nl";
// $naam="André Test";

// $order_number="B10000002";

// echo "Testmail is naar Trustpilot verzonden.<br/><br/>Klantgegevens: [".$order_number."] ".$naam." - ".$mailadres;

// $mail=new wt_mail;

// $mail->from=$vars["email"];
// $mail->fromname=$vars["websitenaam"];

// $mail->to=$vars["trustpilot_code"];
// // $mail->to="jeroen@webtastic.nl";
// $mail->subject="Order number ".$order_number;

// $mail->html="<!--
// tp_lang: nl-NL
// tp_tld: nl
// -->
// Customer Email: ".wt_he($mailadres)."\n<br/>Customer Name: ".wt_he($naam)."<br/>\n";

// // $mail->html_top="";
// // $mail->html="<B>Hallo</B>";
// // $mail->html_bottom="";

// $mail->send();

// exit;


// $mail=new wt_mail;
// $mail->fromname="Testmailër";
// $mail->from="test@webtastic.nl";
// $mail->toname="Jeroën";
// $mail->to="jeroen@webtastic.nl";
// $mail->subject="Onderwerp hé ga je?";

// $mail->plaintext="Hallo";

// $mail->html_top="";
// $mail->html="<B>Hallo</B>";
// $mail->html_bottom="";

// $mail->send();

// exit;


if($_GET["errortest"]) {
	echo "Errortest has been sent...";
	sort($empty_test_array);
} else {
	// if($_COOKIE["flc"]) {
		phpinfo();
	// }
}

# test 12
# test
# TEST 15-11-2013

// ksort($a);

// echo "TT";
#include("admin/vars.php");

#verstuur_opmaakmail("I","boschman@gmail.com","Jeroen","Onderwerp testmail","Hallo, hier de body","");

#error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);



//http://chalet-dev.web.netromtest.ro/error_log.php?error=

// $a=file_get_contents("http://www.webtastic.nl/ipadres/direct.php");
// echo "IPADRES:".$a;


?>