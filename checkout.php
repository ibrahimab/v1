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
$boekingObject["Totaal boeking bedrag"]=3021;
$boekingObject["Termijn"]=920;
$boekingObject["reserveringsnummer"]="C12105049";
$boekingObject["plaats"]="test plaats";
$boekingObject["Accommodatie"]="Chalet Almhaus Peter(8-10 pers.)";
$boekingObject["Deelnemers"]="8 personen";
$boekingObject["Verblijfsperiode"]="16 februari 2013 - 23 februari 2013";
$boekingObject["product"][0]="1x Accommodatie";
$boekingObject["product"]["prijs"][0]=2810;
$boekingObject["product"][1]="1x Energie en eindschoonmaakkosten (excl. keukenhoek) verplicht te voldoen";
$boekingObject["product"]["prijs"][1]=191;
$boekingObject["Reserveringskosten"]=true;

include "content/opmaak.php";
?>