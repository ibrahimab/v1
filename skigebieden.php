<?php

include("admin/vars.php");

$breadcrumbs["last"]=ucfirst(txt("menu_skigebieden"));

if($vars["seizoentype"]==2) {
	header("Location: ".$vars["path"]."bestemmingen.php",true,301);
	exit;
}

$vars["page_with_tabs"]=true;
include "content/opmaak.php";

?>