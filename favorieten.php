<?php

include("admin/vars.php");
$klantfavs=array();
$db->query("SELECT type_id FROM bezoeker_favoriet WHERE bezoeker_id='".addslashes($_COOKIE["sch"])."';");
while($db->next_record()){
	array_push($klantfavs,$db->f("type_id"));
}
$submenu["favorieten"]=txt("submenutitle_favorieten")."(".count($klantfavs).")";
if($vars["websitetype"]==1){
	$standardtext="Je hebt momenteel geen chalets en/of appartementen aan je favorieten toegevoegd.<BR><BR>Ga naar de pagina van een chalet of appartement en klik op <a href=\"#\">";
	$standardtext.="<img border=\"0\" src=\"".$vars["path"]."pic/icon_plus.png\">";
	$standardtext.=" Plaats in mijn favorieten";
	$standardtext.="</a> om een chalet of appartement aan je lijst met favorieten toe te voegen.";
	
	$doormailText="Ik heb een aantal leuke chalets en appartementen gevonden op ".$vars["websitenaam"]. " Dit moet je zien!";
}
elseif($vars["websitetype"]==3 or $vars["websitetype"]==7){
	$standardtext="Je hebt momenteel geen vakantiehuizen aan je favorieten toegevoegd.<BR><BR>Ga naar de pagina van een vakantiehuis en klik op <a href=\"#\">";
	$standardtext.="<img border=\"0\" src=\"".$vars["path"]."pic/icon_plus.png\">";
	$standardtext.=" Plaats in mijn favorieten";
	$standardtext.="</a> om een vakantiehuis aan je lijst met favorieten toe te voegen.";
	
	$doormailText="Ik heb een aantal leuke vakantiehuizen gevonden op  ".$vars["websitenaam"]. " Dit moet je zien!";
}
include "content/opmaak.php";

?>