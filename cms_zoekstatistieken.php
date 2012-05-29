<?php

$mustlogin=true;
include("admin/vars.php");
if($_GET["wzt"]==2) {
	$cms->page_title="Zoekstatistieken zomer";
} else {
	$_GET["wzt"]=1;
	$cms->page_title="Zoekstatistieken winter";
}
if($login->has_priv("8")) {
	$layout->display_all($cms->page_title);
} else {
	header("Location: cms.php");
}

?>