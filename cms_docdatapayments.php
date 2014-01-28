<?php

$mustlogin=true;

if(!$_GET["soort"]) {
	$_GET["soort"]="te_ontvangen";
}

include("admin/vars.php");

if($_GET["soort"]=="te_ontvangen") {
	$title["cms_docdatapayments"]="Docdata-betalingen - nog te ontvangen";
} else {
	$title["cms_docdatapayments"]="Docdata-betalingen - reeds ontvangen";
}

if(!$login->has_priv("3")) {
	header("Location: cms.php");
	exit;
}

$layout->display_all($title["cms_docdatapayments"]);

?>