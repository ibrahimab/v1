<?php

$mustlogin=true;
include("admin/vars.php");

if(!$login->has_priv("25")) {
	header("Location: cms.php");
	exit;
}

$layout->display_all($cms->page_title);

?>