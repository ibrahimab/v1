<?php

include("admin/vars.php");

$vars["topfoto"][1]="pic/tijdelijk/topfoto_italie1.jpg";
$vars["topfoto"][2]="pic/tijdelijk/topfoto_italie2.jpg";
$vars["topfoto"][3]="pic/tijdelijk/topfoto_italie3.jpg";
$vars["topfoto"][4]="pic/tijdelijk/topfoto_italie4.jpg";

if($vars["website"]=="Z") {
	include "content/opmaak.php";
} else {
	header("Location: /");
}

?>