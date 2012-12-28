<?php

$laat_titel_weg=true;
include("admin/vars.php");


if($vars["website"]=="W") {
	$title["facebook-actie"]="Facebook-actie SuperSki";
	include "content/opmaak.php";
} elseif($vars["website"]=="I") {
	$title["facebook-actie"]="Facebook-actie Italissima";
	include "content/opmaak.php";
} else {
	header("Location: /");
}

?>