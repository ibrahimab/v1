<?php

if($_GET["logout"]) {
	# bij uitloggen: niet afgeronde boeking wissen
	unset($_SESSION["boeking"]);	
	unset($_COOKIE["CHALET"]["boeking"]["boekingid"]);
	setcookie("CHALET[boeking][boekingid]","_leeg_",time()+60);
	setcookie("CHALET[boeking][boekingid]","",time()-864000);
}

$vars["reisbureau_mustlogin"]=true;
include("admin/vars.php");

if(!$vars["wederverkoop"]) {
	header("Location: ".$path);
	exit;
}

$robot_noindex=true;
include "content/opmaak.php";

?>