<?php

$laat_titel_weg=true;
include("admin/vars.php");

if($vars["website"]=="C" or $vars["website"]=="T") {
	include "content/opmaak.php";
} else {
	header("Location: /");
	exit;
}

?>