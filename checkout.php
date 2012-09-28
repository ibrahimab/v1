<?php
session_start();
//$mustlogin=true;

//$vars["verberg_linkerkolom"]=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$laat_titel_weg=true;
include("admin/vars.php");
//boekinggegeven object moet op een of andere manier worden doorgegeven vanuit de boekingen systeem. hierover overleg plegen met Jeroen.
//voorlopig eerst een demo object.
$boekingObject=array();
$boekingObject["klantNaam"]="Miguel Moukimou";
$boekingObject["Totaal boeking bedrag"]=3000;
$boekingObject["Termijn"]=1000;
include "content/opmaak.php";
?>