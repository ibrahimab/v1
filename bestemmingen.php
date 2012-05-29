<?php


include("admin/vars.php");
if($vars["websitetype"]==7) {
	$vars["jquery_maphilight"]=true;
}

$vars["canonical"]=$vars["path"]."bestemmingen.php";
if($vars["seizoentype"]==2) {
	include "content/opmaak.php";
} else {
	header("Location: ".$vars["path"]."zoek-en-boek.php");
}

?>