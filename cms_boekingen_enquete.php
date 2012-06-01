<?php

$mustlogin=true;
include("admin/vars.php");

$gegevens=get_boekinginfo($_GET["bid"]);

$layout->display_all($cms->page_title);

?>