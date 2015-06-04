<?php


//
// XML-export HomeAway
//
//
// https://www.chalet.eu/xml/homeaway-new.php?feedtype=rates
// https://www.chalet.eu/xml/homeaway-new.php?feedtype=reservations
//
// https://www.italyhomes.eu/xml/homeaway-new.php?feedtype=rates
// https://www.italyhomes.eu/xml/homeaway-new.php?feedtype=reservations
//
//


set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");


$xmlHomeAway = new xmlHomeAway;

$xmlHomeAway->website = $vars["website"];
// $xmlHomeAway->type_ids = "253";


if($vars["seizoentype"]==1) {


	$xmlHomeAway->type_codes=array(

		"7906"=>"HA1138520",
		"6642"=>"HA1309554",
		"8172"=>"HA1309555",
		"5943"=>"HA1309556",
		"5941"=>"HA1309557",
		"5940"=>"HA1309558",
		"6545"=>"HA1309561",
		"250"=>"HA1309564",
		"252"=>"HA1309565",
		"8369"=>"HA1309566",
		"3"=>"HA1309567",
		"5"=>"HA1309568",
		"2"=>"HA1309569",
		"7"=>"HA1309570",
		"3564"=>"HA1672559",
		"8539"=>"HA1672558",
		"242"=>"HA1138508",
		"240"=>"HA1138509",
		"453"=>"HA1138510",
		"249"=>"HA1138511",
		"3167"=>"HA1138512",
		"247"=>"HA1138513",
		"248"=>"HA1138514",
		"4034"=>"HA1138515",
		"165"=>"HA1138516",
		"4"=>"HA1138517",
		"5409"=>"HA1138519",
		"6577"=>"HA1138521",
		"1038"=>"HA1138522",
		"4697"=>"HA1138523",
		"3179"=>"HA1138524",
		"5312"=>"HA1138525",
		"9004"=>"HA1530303",
		"3023"=>"HA1530304",
		"162"=>"HA1530305",
		"9030"=>"HA1594664",
		"267"=>"HA1594665",
		"6265"=>"HA1594666",

	);

	// 	$xmlHomeAway->type_codes=array(

	// 	                      "3023"=>"HA1530304",
	// );


} elseif($vars["seizoentype"]==2) {
	$xmlHomeAway->type_codes=array(
		"7160"=>"HA1309578",
		"7678"=>"HA1309582",
		"7484"=>"HA1309584",
		"7179"=>"HA1309585",
		"9103"=>"HA1672555",
		"8633"=>"HA1672538",
		"5076"=>"HA1672533",
		"3738"=>"HA1672530",
		"7477"=>"HA1672528",
		"9785"=>"HA1672527",
		"6156"=>"HA1672526",
		"8305"=>"HA1672554",
	);
}

$xmlHomeAway->feedtype = $_GET["feedtype"];
$xmlHomeAway->showXML();

