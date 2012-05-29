<?php

# Vallandry
include("admin/vars.php");
if($vars["websitetype"]<>6) {
	header("Location: ".$vars["path"]);
	exit;
}
include "content/opmaak.php";

?>