<?php


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
// echo htmlentities($output);

$robot_noindex=true;
include("admin/vars.php");
if($vars["lokale_testserver"]) {
	include "content/opmaak.php";
}

// $a = new PPPa;

?>