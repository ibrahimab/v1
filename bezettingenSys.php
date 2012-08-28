<?php

//$mustlogin=true;
$vars["verberg_linkerkolom"]=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$laat_titel_weg=true;
//include("excel/excelwriter.inc.php");
require_once("excel/Classes/PHPExcel.php");
include("admin/vars.php");

include "content/opmaak.php";

?>