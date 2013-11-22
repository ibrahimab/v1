<?php

include("admin/vars.php");

// $mailadres="bjorn@chalet.nl";
// $naam="Bjorn den Blanken";

// $mailadres="jeroen@webtastic.nl";
// $naam="Jeroen Boschman";

// $order_number="C12115414";

// echo "Testmail is naar Trustpilot verzonden.<br/><br/>Klantgegevens: [".$order_number."] ".$naam." - ".$mailadres;

// $mail=new wt_mail;
// $mail->fromname="Italissima.nl";
// $mail->from="info@italissima.nl";

// // $mail->fromname="Chalet.nl";
// // $mail->from="info@chalet.nl";

// $mail->to="b69417c8@trustpilotservice.com";
// // $mail->to="jeroen@webtastic.nl";
// $mail->subject="Order number ".$order_number;

// $mail->plaintext="Customer Email: ".$mailadres."\n\nCustomer Name: ".$naam."\n\n";

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
	if($_COOKIE["flc"]) {
		phpinfo();
	}
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