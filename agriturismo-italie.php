<?php

$title["agriturismo-italie"]="Agriturismo Italië";

# alvast velden invullen bij zoekform indien zoek-en-boek links wordt gebruikt
$vars["zoekenboeklinks_hidden_form_fields"]=array("vf_kenm38"=>"1");

$laat_titel_weg=true;
$meta_description="Geniet van het authentiek en landelijk Italië op een agriturismo. Bekijk onze vakantiehuizen op agriturismi door heel Italië.";

include("admin/vars.php");

if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

$html_ipv_blokaccommodatie.="<div style=\"font-size:0.9em;margin-top:-17px;\">";
$html_ipv_blokaccommodatie.="<div style=\"margin-bottom:10px;margin-top:30px;\"><i>Agriturismi per regio:</i></div>";
$html_ipv_blokaccommodatie.="<div style=\"margin-bottom:5px;\"><a href=\"".$vars["path"]."zoek-en-boek.php?filled=1&fzt=&fsg=5-124&fap=0&fad=0&fdu=0&vf_kenm38=1\">".wt_he("Agriturismi in Toscane")."</a></div>";
$html_ipv_blokaccommodatie.="<div style=\"margin-bottom:5px;\"><a href=\"".$vars["path"]."zoek-en-boek.php?filled=1&fzt=&fsg=5-156&fap=0&fas=0&fad=0&fdu=0&vf_kenm38=1\">".wt_he("Agriturismi in Le Marche")."</a></div>";
$html_ipv_blokaccommodatie.="<div style=\"margin-bottom:5px;\"><a href=\"".$vars["path"]."zoek-en-boek.php?filled=1&fzt=&fsg=5-122&fap=0&fas=0&fad=0&fdu=0&vf_kenm38=1\">".wt_he("Agriturismi in Umbrië")."</a></div>";
$html_ipv_blokaccommodatie.="<div style=\"margin-bottom:5px;\"><a href=\"".$vars["path"]."zoek-en-boek.php?filled=1&fzt=&fsg=5-142&fap=0&fas=0&fad=0&fdu=0&vf_kenm38=1\">".wt_he("Agriturismi in Merengebied / Lombardije")."</a></div>";
$html_ipv_blokaccommodatie.="<div style=\"margin-bottom:5px;\"><a href=\"".$vars["path"]."zoek-en-boek.php?filled=1&fzt=&fsg=5-125&fap=0&fas=0&fad=0&fdu=0&vf_kenm38=1\">".wt_he("Agriturismi in Lazio")."</a></div>";
$html_ipv_blokaccommodatie.="<div style=\"margin-bottom:5px;\"><a href=\"".$vars["path"]."zoek-en-boek.php?filled=1&fzt=&fsg=5-130&fap=0&fas=0&fad=0&fdu=0&vf_kenm38=1\">".wt_he("Agriturismi in Ligurië")."</a></div>";
$html_ipv_blokaccommodatie.="</div>";
include "content/opmaak.php";

?>