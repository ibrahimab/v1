<?php

# test 4
# test
# TEST 8-10-2013

// ksort($a);

// echo "TT";
#include("admin/vars.php");

#verstuur_opmaakmail("I","boschman@gmail.com","Jeroen","Onderwerp testmail","Hallo, hier de body","");

#error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);

#if($_COOKIE["flc"]) {
	phpinfo();
#}

?>