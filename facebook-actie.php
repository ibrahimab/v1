<?php

$laat_titel_weg=true;
include("admin/vars.php");

$title["facebook-actie"]="Facebook-actie SuperSki";

if($vars["website"]=="W") {
	include "content/opmaak.php";
} else {
	header("Location: /");
}

?>