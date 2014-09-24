<?php

// if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	set_time_limit(0);
	include("admin/vars.php");

	echo time()."<br/>";
	// update bijkomende kosten Redis
	$bijkomendekosten = new bijkomendekosten;
	$bijkomendekosten->pre_calculate_all_types(100);
	echo time()."<br/>";


	exit;

// }

set_time_limit(0);


phpinfo();
exit;

// echo wt_redis_host;
// exit;
// 87.250.157.203

// $vars["tmp_info_tonen"] = true;





// exit;



// $bijkomendekosten->pre_calculate_all_types();

wt_debugtimer("start", 5);
echo $bijkomendekosten->get_type_from_cache(6755, 24, 5);
wt_debugtimer("eind", 5);


// $db->query("SELECT * FROM bk_accommodatie WHERE accommodatie_id='2977';");
// $db->next_record();
// var_dump($db);



exit;


$wt_redis = new wt_redis;

$a["test"][1]="test1";
$a["test"][2]="test2";
$a["test2"][1]="test2-1";
$a["test2"][2]="test2-2";

// $wt_redis->store_array("testarray", "a", $a);


$b = $wt_redis->get_array("testarray", "a");
echo wt_dump($a);
echo wt_dump($b);


// echo wt_dump(get_defined_constants());


$redis = new Redis() or die("Cannot load Redis module.");

// if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$redis->connect('87.250.157.203', 6379);
// }

// $redis->connect('localhost');
// $redis->set('random', rand(5000,6000));
echo $redis->get('random');


// $redis = new Redis();
// // $redis->connect('87.250.157.203', 6379);
// $redis->connect('127.0.0.1', 6379);

// phpinfo();

echo "OK";



exit;
// each client should remember their session id for EXACTLY 1 hour
ini_set('session.gc_maxlifetime', 10800);
session_set_cookie_params(10800);

phpinfo();
exit;









// // echo "TT3";
phpinfo();

exit;


$mail=new wt_mail;
$mail->toname="Jeroen";
$mail->to="boschman@gmail.com";
$mail->to="check-auth-jeroen=webtastic.nl@verifier.port25.com";
$mail->to="jeroen_boschman@hotmail.com";
// $mail->to="boschman@gmail.com";
$mail->subject="Testmail ".date("H:i");

$mail->plaintext="Hallo";

$mail->html_top="";
$mail->html="<B>Hallo</B>";
$mail->html_bottom="";

// $mail->test = false;


foreach ($vars["websiteinfo"]["email"] as $key => $value) {

	if(!$vars["websites_inactief"][$key]) {
		$mail->fromname=$vars["websiteinfo"]["websitenaam"][$key];
		$mail->from=$value;
		$mail->returnpath=$value;

		$mail->send();

		echo "verzonden van: ".$value." (aan ".$mail->to.")<br/>\n";

		exit;

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