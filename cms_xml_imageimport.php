<?php

set_time_limit(0);

$mustlogin=true;
include("admin/vars.php");

# Type- en acc-code uit db halen
$db->query("SELECT DISTINCT a.accommodatie_id, a.leverancierscode FROM accommodatie a WHERE a.leverancier_id='".addslashes($_GET["lev"])."';");
while($db->next_record()) {
	$vars["acccode"][$db->f("leverancierscode")]["accid"]=$db->f("accommodatie_id");
}

$db->query("SELECT DISTINCT a.accommodatie_id, a.leverancierscode, t.type_id, t.leverancierscode AS tleverancierscode FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND t.leverancier_id='".addslashes($_GET["lev"])."';");
while($db->next_record()) {
	$vars["typecode"][$db->f("tleverancierscode")]["accid"]=$db->f("accommodatie_id");
	$vars["typecode"][$db->f("tleverancierscode")]["typeid"]=$db->f("type_id");
	$vars["typecode"][$db->f("tleverancierscode")]["acccode"]=$db->f("leverancierscode");
}

$layout->display_all($cms->page_title);

?>