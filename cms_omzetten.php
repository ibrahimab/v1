<?php

include("admin/vars.php");

$db->query("SELECT naam, plaats_id, kaart FROM plaats WHERE wzt=2 AND kaart<>'';");
while($db->next_record()) {
	$db2->query("UPDATE plaats SET plattegrond='".addslashes($db->f("kaart"))."', kaart='' WHERE plaats_id='".$db->f("plaats_id")."';");
	echo $db->f("naam").": ".$db2->lastquery."<br>";
}


?>