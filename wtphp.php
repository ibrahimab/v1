<?php

include("admin/vars.php");

if($_GET["errortest"]) {
	echo "Errortest has been sent...";
	sort($empty_test_array);
} else {
	if($_COOKIE["flc"]) {
		phpinfo();
	}
}

# test 11
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