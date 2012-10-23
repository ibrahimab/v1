<?php

include("admin/vars.php");

echo "<div id=\"fancybox_popup_wrapper\">";

$includefile=$unixdir."content/popup-fancybox-".basename($_GET["popupfbid"]).".html";
if(file_exists($includefile)) {
	include $includefile;
}

echo "</div>"; # afsluiten #fancybox_popup_wrapper

?>