<?php


//
// XML-export HomeAway
//
//
// https://www.chalet.eu/xml/homeaway.php?feedtype=rates
// https://www.chalet.eu/xml/homeaway.php?feedtype=reservations
//
// https://www.italyhomes.eu/xml/homeaway.php?feedtype=rates
// https://www.italyhomes.eu/xml/homeaway.php?feedtype=reservations
//
//


set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");


$xmlHomeAway = new xmlHomeAway;

$xmlHomeAway->website = $vars["website"];
// $xmlHomeAway->type_ids = "253";



$db->query("SELECT t.type_id, t.homeaway_code FROM type t INNER JOIN accommodatie a USING (accommodatie_id) WHERE t.websites LIKE '%".$vars["website"]."%' AND a.wzt='".intval($vars["seizoentype"])."' AND homeaway_code IS NOT NULL;");
while( $db->next_record() ) {
	$xmlHomeAway->type_codes[$db->f( "type_id" )] = $db->f( "homeaway_code" );
}


$xmlHomeAway->feedtype = $_GET["feedtype"];
$xmlHomeAway->showXML();

