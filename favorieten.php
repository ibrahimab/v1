<?php

include("admin/vars.php");
if($vars["websitetype"]==1){
	$klantfavs=array();
		$db->query("SELECT type_id FROM bezoeker_favoriet WHERE bezoeker_id='".addslashes($_COOKIE["sch"])."';");
		while($db->next_record()){
			array_push($klantfavs,$db->f("type_id"));
		}
	$submenu["favorieten"]=txt("submenutitle_favorieten")."(".count($klantfavs).")";
}
include "content/opmaak.php";

?>