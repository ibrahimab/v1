<?php

#
# Dit is een test-soapserver (nog niet in gebruik)
#


ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache


#
# Alle functions definieren
#
function saveDistribReservation2($upc){
	//in reality, this data would be coming from a database
	$items = array('12345'=>5,'19283'=>100,'23489'=>'234');
	return $items[$upc];
}

$server = new SoapServer("soap.wsdl");
$server->addFunction("saveDistribReservation2");
$server->handle();

?>