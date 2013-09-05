<?php

include("admin/vars.php");

# Bij WSA en Italissima zijn geen thema's beschikbaar
if($vars["websitetype"]==7 or $vars["websitetype"]==8 or $vars["websitetype"]==9) {
	header("Location: ".$vars["path"]);
	exit;
}

include "content/opmaak.php";

?>