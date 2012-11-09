<?php

include("admin/vars.php");

# De smaak van italie - Zomerhuisje
#	if(date("Ymd")<="20100606" and $vars["website"]=="Z") {
#		$html_ipv_blokaccommodatie="<a href=\"".$vars["path"]."smaak-van-italie.php\"><img src=\"".$vars["path"]."pic/tijdelijk/italie_banner.gif\" border=\"0\" style=\"border:1px solid #000000;margin-top:20px;margin-left:30px;\"></a>";
#	}

if($vars["websitetype"]==6) {
	# Vallandry
	$onload="setTimeout('rotatephotos()',1000);";
}

include "content/opmaak.php";

?>