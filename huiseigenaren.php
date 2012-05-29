<?php

# Vallandry
include("admin/vars.php");
if($vars["websitetype"]<>6) {
	header("Location: ".$vars["path"]);
	exit;
}
$robot_noindex=true;
include "content/opmaak.php";

?>