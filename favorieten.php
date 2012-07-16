<?php

include("admin/vars.php");
//dat heb ik zo gedaan omdat de menu al wordt opgebouwd voordat de html pagina wordt opgeladen. wanneer deze is opgeladen gebeurt er niks meer 
//met de menu balk. tijdens het ophalen van deze php bestand wordt de vars bestand opgehaald en kan de menubalk nog worden overschreven.
//ik geprobeerd om deze gebeurtenis vanuit de html pagina te laten gebeuren maar zonder succes.
if($vars["websitetype"]==1){
	$klantfavs=array();
		$db->query("select type_id from bezoeker_favoriet where bezoeker_id='".$_COOKIE["sch"]."'");
		while($db->next_record()){
			array_push($klantfavs,$db->f("type_id"));
		}
	$submenu["favorieten"]=txt("submenutitle_favorieten")."(".count($klantfavs).")";
}
include "content/opmaak.php";

?>