<?php


# http://php.net/manual/en/soapclient.soapclient.php

set_time_limit(0);
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	$unzip="/usr/bin/unzip";
	$tmpdir="/tmp/";
} elseif($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$unixdir="/home/webtastic/html/chalet/";
	$unzip="/usr/bin/unzip";
	$tmpdir="/home/webtastic/html/chalet/tmp/";
} else {
	if($_SERVER["PWD"]=="/var/www/chalet.nl") {
		$unixdir="/var/www/chalet.nl/html/";
		$tmpdir="/var/www/chalet.nl/html/tmp/";
		$unzip="/usr/bin/unzip";
	} else {
		$unixdir="/home/sites/chalet.nl/html/";
		$tmpdir="/home/sites/chalet.nl/html/tmp/";
		$unzip="/usr/local/bin/unzip";
	}
}

$cron=true;
include($unixdir."admin/vars.php");

echo date("r")."<pre>Chalet.nl XML Import WSDL\n\n\n";
flush();


#echo "<pre>";


$t=1;

if($t==1) {

	echo "<h1>Eurogroup</h1>";

#	'base_id'=>'eurogroup',
#	'username'=>'chaletnl',
#	'password'=>'partner',
#	'partnercode'=>'REBERE002',
#	'convention_id'=>'1894',
#	'allotment'=>'',
#	'etab_id'=>'52',
#	'room_type'=>'BBT',
#	'start_date'=>'2011-04-02',
#	'end_date'=>'2011-04-09'
#	);

	$client = new SoapClient("http://www.eto.madamevacances.resalys.com/rsl/wsdl_distrib",array('trace'=>1));
#	$client = new SoapClient("http://sslocal.postvak.net/chalet/xml/soap.wsdl",array('trace'=>1));

#	new SoapVar('<FilterBy Column="Id" FilterOperator="=" FilterValue="NUMBER" Table="Case"/>', XSD_ANYXML))

	$result=$client->getDistribProposals2("eurogroup","chaletnl","partner","REBERE002","1894","");
#	$result=$client->getDistribProposalsbyDate2("eurogroup","chaletnl","partner","REBERE002","1894","","52","BBT","2011-04-02","2011-04-09");
	print_r($result);


exit;
	$reservation=array(
		"external_id"=>"TEST",
		"etab_id"=>"TEST",
		"reservation_type"=>"gin",
		"firstname"=>"Mr",
		"lastname"=>"TEST",
		"stays"=>array(
			"stays"=>array(
				"nb_rooms"=>1,
				"room_type_code"=>"TEST",
				"start_date"=>"2010-01-16",
				"end_date"=>"2010-01-23"
			)
		)
	);

#	$aa=array("TEST","TEST","gin","Mr","TEST");
#echo wt_dump($reservation);



#	$result=$client->saveDistribReservation2("eurogroup","chaletnl","partner","REBERE002",$reservation);
#	$result=$client->saveDistribReservation2("eurogroup","chaletnl","partner","REBERE002","AA",array("BB","CC","DD","EE"));

#$args=array(
#"eurogroup","chaletnl","partner","REBERE002","AA","external_id"=>"BB"
#);

#$args["reservation"]=$reservation;

#	$result = $client->__soapCall("saveDistribReservation2",$args);


	print_r($result);


	# Alle prijzen+beschikbaarheid
#	$result=$client->getDistribProposals2("eurogroup","chaletnl","partner","REBERE002","1894","");
#	print_r($result);

#	print_r($result->distribProposal);
#	while(list($key,$value)=each($result->distribProposal)) {

#	$client = new SoapClient("http://www.eto.madamevacances.resalys.com/rsl/wsdl_distrib",array('trace'=>1));
#	$result=$client->getDistribProposals2("eurogroup","chaletnl","partner","REBERE002","1894","");
#	echo "<table cellspacing=\"0\" class=\"tbl\"><tr><th>Plaats</th><th>Accommodatie</th><th>Type</th><th>XML-code</th></tr>\n";
#	foreach($result->distribProposal as $value3) {
#		$key=utf8_decode($value3->etab_id)."_".utf8_decode($value3->room_type_code);
#		if(!$keygehad[$key]) {
#			$sortkey=utf8_decode($value3->station)."_".utf8_decode($value3->etab_name)."_".utf8_decode($value3->room_type_label);
#			$teller++;
#			$trregel[$sortkey."_".$teller]="<tr><td>".htmlentities(utf8_decode($value3->station))."</td><td>".htmlentities(utf8_decode($value3->etab_name))."</td><td>".htmlentities(utf8_decode($value3->room_type_label))."</td><td>".htmlentities($key)."</td></tr>\n";
#			$keygehad[$key]=true;
#		}
#	}
#	ksort($trregel);
#	while(list($key,$value)=each($trregel)) {
#		echo $value;
#	}
#	echo "</table>";

#	$result=$client->getAllEtabs("eurogroup","chaletnl","partner","nl");
#	print_r($result);

	echo "REQUEST:\n" . htmlentities($client->__getLastRequest(),ENT_QUOTES, 'UTF-8') . "\n";
	echo "RESPONSE:\n" . htmlentities($client->__getLastResponse(),ENT_QUOTES, 'UTF-8') . "\n";


} elseif($t==2) {

	echo "<h1>Ruralandalus</h1>";

	$client = new SoapClient("http://212.79.145.195/desarrolloixml/integracion.asmx?wsdl",array('trace'=>1));

	$params = array(
	"idEntidad_Cliente"=>"251028",
	"clave"=>"Ch@let0810"
	);

	$result=$client->CrearIdSesion($params);
	print_r($result);
	echo "REQUEST:\n" . htmlentities($client->__getLastRequest(),ENT_QUOTES, 'UTF-8') . "\n";
	echo "RESPONSE:\n" . htmlentities($client->__getLastResponse(),ENT_QUOTES, 'UTF-8') . "\n";
}

?>