<?php

include("admin/vars.php");

# Bij WSA en Italissima zijn geen thema's beschikbaar
if($vars["website"]=="W" or $vars["websitetype"]==7) {
	header("Location: ".$vars["path"]);
	exit;
}

include "content/opmaak.php";

?>