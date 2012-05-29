<?php

$geen_tracker_cookie=true;

if(ereg("/pic/chalets/",$_SERVER["REQUEST_URI"])) {
	$fp=fopen("pic/chalet_404.jpg", "r");
	header("Content-type: image/jpg");
	fpassthru($fp);
	fclose($fp);
} else {
	if(eregi("ac[a-z]+/([A-Za-z0]+[0-9]+)",$_SERVER["REQUEST_URI"],$regs404)) {
		include("admin/vars.php");
		if(substr($regs404[1],0,1)=="0") {
			$regs404[1]=ereg_replace("^0","O",$regs404[1]);
		}
		header("Location: ".$path.txt("menu_accommodatie")."/".strtoupper($regs404[1])."/",true,301);
		exit;
	}	

	# /f1815 doorsturen naar /accommodatie/F1815
	if(eregi("^/([a-z]{1,2}[0-9]+)/?$",$_SERVER["REQUEST_URI"],$regs404)) {
		include("admin/vars.php");
		header("Location: ".$path.txt("menu_accommodatie")."/".strtoupper($regs404[1])."/",true,301);
		exit;
	}

	# /1815 doorsturen naar /accommodatie/F1815
	if(eregi("^/([0-9]+)/?$",$_SERVER["REQUEST_URI"],$regs404)) {
		include("admin/vars.php");
		header("Location: ".$path.txt("menu_accommodatie")."/F".strtoupper($regs404[1])."/",true,301);
		exit;
	}

	# /filename doorsturen naar /filename.php
	if(eregi("^/([a-z0-9_-]+)$",$_SERVER["REQUEST_URI"],$regs404) and file_exists(strtolower($regs404[1].".php"))) {
		header("Location: /".$regs404[1].".php",true,301);
		exit;
	}

	if(ereg("/inlog",$_SERVER["REQUEST_URI"])) {
		header("Location: ".$path."inloggen.php",true,301);
		exit;
	}

	# mensen doorsturen naar zoekformulier (bijv. bij http://www.chalet.nl/zwitserland)
	if(eregi("^/([0-9a-zA-Z]+)$",$_SERVER["REQUEST_URI"],$regs404)) {
		include("admin/vars.php");
		$_SERVER["REDIRECT_STATUS"]="404";
		wt_404();
		header("Location: ".$path.txt("menu_zoek-en-boek").".php?filled=1&fzt=".htmlentities(urlencode($regs404[1])),true,302); # geen 301-redirect!!
		exit;
	}

	include("admin/vars.php");

	wt_404(true);
	header("HTTP/1.0 404 Not Found");
	
	include "content/opmaak.php";
}

?>