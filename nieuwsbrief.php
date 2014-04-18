<?php

include("admin/vars.php");

if(!$vars["nieuwsbrief_aanbieden"]) {
	header("Location: ".$vars["path"],true,301);
	exit;
} else {
	include "content/opmaak.php";
}

?>