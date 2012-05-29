<?php

#
#
#http://ss.postvak.net/chalet/cms_pdfdownload.php?pdffile=pdf%2Ffacturen%2Ffactuur_22188_1.pdf

$mustlogin=true;

include("admin/vars.php");

if($login->userlevel>=1 and $_GET["pdffile"] and ereg("\.pdf$",$_GET["pdffile"])) {
	if(file_exists($_GET["pdffile"])) {
		# PDF
    		header("Content-Type: application/pdf");
		header("Content-Transfer-Encoding: binary");
		readfile($_GET["pdffile"]);
	}
}
cmslog_pagina_title("Boeking - Facturen - openen");

?>