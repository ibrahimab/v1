<?php

#
# Informatie voor reisagenten (inloggen is niet nodig!)
#
#$vars["reisbureau_mustlogin"]=true;
$robot_noindex=true;
include("admin/vars.php");

if($vars["wederverkoop"]) {
	include "content/opmaak.php";
} else {
	header("Location: ".$vars["path"]);
}

?>