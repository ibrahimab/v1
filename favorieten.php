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
	$vars["balkkleur"]="#003366";
	$vars["backcolor"]="#d5e1f9";
	$vars["textColor"]="#003366";
	$vars["korteOmschrijvinkleur"]="#d40139";
	$knopLeesmeer="<img border=\"0\" src=\"".$vars["basehref"]."pic/leesMeerButton.png\" width=\"82\" height=\"23\"></img></a>";
	
	$doormailText="Ik heb een aantal leuke chalets en appartementen gevonden op ".$vars["websitenaam"]. " Dit moet je zien!";
}
elseif($vars["websitetype"]==3 or $vars["websitetype"]==7){
	$standardtext="Je hebt momenteel geen vakantiehuizen aan je favorieten toegevoegd.<BR><BR>Ga naar de pagina van een vakantiehuis en klik op <a href=\"#\">";
	$standardtext.="<img border=\"0\" src=\"".$vars["path"]."pic/icon_plus.png\">";
	$standardtext.=" Plaats in mijn favorieten";
	$standardtext.="</a> om een vakantiehuis aan je lijst met favorieten toe te voegen.";
	
	
	$doormailText="Ik heb een aantal leuke vakantiehuizen gevonden op  ".$vars["websitenaam"]. " Dit moet je zien!";
	if($vars["websitetype"]==3){
		$vars["balkkleur"]="#5f227b";
		$vars["backcolor"]="#eaeda9";
		$vars["textColor"]="#5f227b";
		$vars["korteOmschrijvinkleur"]="#5f227b";
		$knopLeesmeer="<img border=\"0\" src=\"".$vars["basehref"]."pic/leesmeerZom.jpg\" width=\"82\" height=\"23\"></img></a>";
		
	}
	elseif($vars["websitetype"]==7){
		$vars["balkkleur"]="#661700";
		$vars["backcolor"]="#FFFFFF";
		$vars["textColor"]="#661700";
		$vars["korteOmschrijvinkleur"]="#661700";
		$knopLeesmeer="<img border=\"0\" src=\"".$vars["basehref"]."pic/leesmeerIta.jpg\" width=\"82\" height=\"23\"></img></a>";
	}
}
include "content/opmaak.php";

?>