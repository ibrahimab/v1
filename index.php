<?php

include("admin/vars.php");

if($vars["websitetype"]==6) {
	# Vallandry
	$onload="setTimeout('rotatephotos()',1000);";
}

include "content/opmaak.php";

?>