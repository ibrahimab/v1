<?php

$title["error_msg"]="Error message";
$vars["verberg_zoekenboeklinks"]=true;
$robot_noindex=true;
include("admin/vars.php");

if($_GET["t"]==1) {
	$hostname = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	if(preg_match("@google@", $hostname)) {
		header("Location: ".$vars["basehref"], true, 301);
		exit;
	}
}

include "content/opmaak.php";

?>