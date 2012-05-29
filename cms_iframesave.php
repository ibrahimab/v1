<?php

$mustlogin=true;
include("admin/vars.php");

if($_GET["t"]==1) {
	$onload="setTimeout('document.getElementById(\'opgeslagen\').style.display=\'none\'',1500);";
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>CMS Chalet.nl</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="robots" content="noindex,nofollow" />
<link href="css/cms_layout.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" language="JavaScript1.2" src="scripts/cms_functions.js?t=1"></script>
</head>
<?php
echo "<body style=\"background-color:#ffffff;\"".($onload ? " onload=\"".$onload."\"" : "").">";

if($_GET["t"]==1) {
	cmslog_pagina_title("Boeking - Voucherstatus");
	echo "<table><tr><td><b>Voucherstatus met 1 klik wijzigen en opslaan:</b></td><td>";
	echo "</td>";
	echo "<form id=\"frm\" method=\"post\" action=\"".htmlentities($_SERVER["REQUEST_URI"])."\">";
	echo "<td>";
	echo "<select name=\"voucherstatus\" onchange=\"document.getElementById('frm').submit();\" class=\"wtform_input\" style=\"width:500px;\">";
	if(isset($_POST["voucherstatus"])) {
		$voucherstatus=$_POST["voucherstatus"];
		if($_GET["bid"]) {
			# Opslaan in tabel boeking
			$db->query("UPDATE boeking SET voucherstatus='".addslashes($_POST["voucherstatus"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
			$saved=true;
			
			# Loggen
			$gegevens=get_boekinginfo($_GET["bid"]);
			chalet_log("voucherstatus: ".$vars["voucherstatus"][$_POST["voucherstatus"]],false,true);
		}
	} else {
		$voucherstatus=$_GET["voucherstatus"];
	}
	if($_GET["voucherstatus"]>=6) {
		$doorloop_array=$vars["voucherstatus_nawijzigingen"];
	} else {
		$doorloop_array=$vars["voucherstatus_zonderwijzigingen"];
	}
	while(list($key,$value)=each($doorloop_array)) {
		echo "<option value=\"".$key."\"".($voucherstatus==$key ? " selected" : "").">".htmlentities($value)."</option>\n";
	}
	echo "</select>";
	echo "</td><td>&nbsp;</td>";
	echo "</form>";
	echo "<td id=\"opgeslagen\">";
	if($saved) {
		echo "<i>opgeslagen</i>";
	} else {
		echo "&nbsp;";
	}
	echo "</td></tr></table>";

} else {


}

?></body></html>