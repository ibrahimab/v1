<?php

// phpinfo();


// $array = array("blue", "red", "green", "blue", "1blue");
// $aantal_landen=count(array_keys($array, "blue"));

// echo $aantal_landen;

// exit;

// $url="https://secure.villeinitalia.com/protAgency/AvailableFile.jsp";
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_USERPWD, "italissima:italissima2144");
// curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// $output = curl_exec($ch);
// $info = curl_getinfo($ch);
// curl_close($ch);

// #var_dump($info);
// echo wt_he($output);

$robot_noindex=true;
$vars["verberg_linkerkolom"]=true;
include("admin/vars.php");
#if($vars["lokale_testserver"]) {
	include "content/opmaak.php";
#}

// $a = new PPPa;

?>