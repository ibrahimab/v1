<?php

session_start();
//$mustlogin=true;

$vars["verberg_linkerkolom"]=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$laat_titel_weg=true;
//ini_set("memory_limit","256M");
//include("excel/excelwriter.inc.php");
require_once("admin/phpexcel/PHPExcel.php");

include("admin/vars.php");

if(!$vars["lokale_testserver"]) {
	# Alleen toegankelijk voor lokaal gebruik
	exit;
}

include "content/opmaak.php";

?>