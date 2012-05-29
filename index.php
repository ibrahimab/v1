<?php

include("admin/vars.php");

# De smaak van italie - Zomerhuisje
#	if(date("Ymd")<="20100606" and $vars["website"]=="Z") {
#		$html_ipv_blokaccommodatie="<a href=\"".$vars["path"]."smaak-van-italie.php\"><img src=\"".$vars["path"]."pic/tijdelijk/italie_banner.gif\" border=\"0\" style=\"border:1px solid #000000;margin-top:20px;margin-left:30px;\"></a>";
#	}

# Zomerhuisje Magazine blok 
if(date("Ymd")<="20110131" and $vars["website"]=="Z") {
	$html_ipv_blokaccommodatie="<div style=\"position:relative;\" class=\"overlay_foto\" onclick=\"document.location.href='".$vars["path"]."brochure.php'\"><img src=\"".$vars["path"]."pic/zomerhuisjemagazine_groter.jpg\" width=\"160\" height=\"120\" border=\"0\"><div id=\"blokaccommodatie_overlay\"><div style=\"padding-top:7px;\"><b>Bekijk ons magazine!</b></div></div></div>";
	$html_ipv_blokaccommodatie_bgcolor="#5f227b";
}

if($vars["websitetype"]==6) {
	# Vallandry
	$onload="setTimeout('rotatephotos()',1000);";
}

include "content/opmaak.php";

?>