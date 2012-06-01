<?php
//hier onde de code
$insert = array(
	'user' => 'chaletnl',
	'pass' => 'aTL9!32',
	);

$insertStr = http_build_query($insert, '', '&'); 

$url = "http://www.alpinrentals.co.uk/api/get";

$ch = curl_init();
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $insertStr);
curl_setopt($ch, CURLOPT_POST, 1);
$results = curl_exec($ch);
curl_close($ch);

$xml = simplexml_load_string($results);
foreach($xml->House as $accomodatie){
	echo "<p>";
	echo "<b>Code:</b> ";
	echo $accomodatie->HouseCode;
	echo "<BR/>";
	echo "<b>Datum:</b> ";
	echo $accomodatie->Date;
	echo "<BR/>";
	echo "<b>Beschikbaarheid:</b> ";
	echo $accomodatie->Availability;
	echo "<BR/>";
	echo "<b>Prijs:</b> ";
	echo $accomodatie->Price;
	echo "<BR/>";
	echo "<b>Minimale aantal overnachtingen:</b> ";
	echo $accomodatie->MinNights;
}
?>