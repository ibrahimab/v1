<?php

include("admin/vars.php");
$klantfavs=array();
$db->query("SELECT b.type_id, b.bezoeker_id, t.websites, t.type_id FROM  bezoeker_favoriet b, type t WHERE b.bezoeker_id='".addslashes($_COOKIE["sch"])."' AND b.type_id=t.type_id AND t.websites LIKE '%".$vars["website"]."%';");
while($db->next_record()){
	array_push($klantfavs,$db->f("type_id"));
}
$submenu["favorieten"]=txt("submenutitle_favorieten")."(".count($klantfavs).")";
echo "<script language=\"javascript\">";
echo "sessionStorage.counter=".count($klantfavs);
echo "</script>";
if($vars["websitetype"]==1){
	$standardtext="Je hebt momenteel geen chalets en/of appartementen aan je favorieten toegevoegd.<BR><BR>Ga naar de pagina van een chalet of appartement en klik op <a href=\"#\">";
	$standardtext.="<img border=\"0\" src=\"".$vars["path"]."pic/icon_plus.png\">";
	$standardtext.=" Plaats in mijn favorieten";
	$standardtext.="</a> om een chalet of appartement aan je lijst met favorieten toe te voegen.";
	$vars["balkkleur"]="#003366";
	$vars["backcolor"]="#d5e1f9";
	$vars["textColor"]="#003366";
	$popup=$vars["basehref"]."pic/popBack.png";
	$vars["korteOmschrijvinkleur"]="#d40139";
	$onderwerpText="Mijn favoriete chalets en appartementen";
	$knopLeesmeer="<img border=\"0\" src=\"".$vars["basehref"]."pic/leesMeerButton.png\" width=\"82\" height=\"23\"></img></a>";
	$vars["mail_topfoto"]=$vars["basehref"]."pic/topfoto/headerChalet.png";
	$doormailText="Ik heb een aantal leuke chalets en appartementen gevonden op ".$vars["websitenaam"]. " Dit moet je zien!";
}
elseif($vars["websitetype"]==3 or $vars["websitetype"]==7){
	$standardtext="Je hebt momenteel geen vakantiehuizen aan je favorieten toegevoegd.<BR><BR>Ga naar de pagina van een vakantiehuis en klik op <a href=\"#\">";
	$standardtext.="<img border=\"0\" src=\"".$vars["path"]."pic/icon_plus.png\">";
	$standardtext.=" Plaats in mijn favorieten";
	$standardtext.="</a> om een vakantiehuis aan je lijst met favorieten toe te voegen.";
	$onderwerpText="Mijn favoriete vakantiehuizen";
	$doormailText="Ik heb een aantal leuke vakantiehuizen gevonden op  ".$vars["websitenaam"]. " Dit moet je zien!";
	if($vars["websitetype"]==3){
		$vars["balkkleur"]="#5f227b";
		$vars["backcolor"]="#eaeda9";
		$vars["textColor"]="#5f227b";
		$vars["korteOmschrijvinkleur"]="#5f227b";
		$knopLeesmeer="<img border=\"0\" src=\"".$vars["basehref"]."pic/leesmeerZom.jpg\" width=\"78\" height=\"20\"></img></a>";
		$vars["mail_topfoto"]=$vars["basehref"]."pic/topfoto/headerZomerhuisje.png";
		$popup=$vars["basehref"]."pic/popBackZomer.png";
		
	}
	elseif($vars["websitetype"]==7){
		$vars["balkkleur"]="#661700";
		$vars["backcolor"]="#FFFFFF";
		$vars["textColor"]="#661700";
		$vars["korteOmschrijvinkleur"]="#661700";
		$vars["mail_topfoto"]=$vars["basehref"]."pic/topfoto/headerItalissima.png";
		$knopLeesmeer="<img border=\"0\" src=\"".$vars["basehref"]."pic/leesmeerIta.jpg\" width=\"82\" height=\"23\"></img></a>";
		$popup=$vars["basehref"]."pic/popBackItal.png";
	}
}
include "content/opmaak.php";

?>