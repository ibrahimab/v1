<?php

if($_GET["d"]) $_GET["d"]=intval($_GET["d"]);
include("admin/vars.php");

# Top10 bestaat niet meer
header("Location: ".$path.txt("menu_aanbiedingen").".php",true,301);
exit;

?>