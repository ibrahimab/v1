<?php

//
//
// functions t.b.v. XML-import (o.a. van specifieke leveranciers)
//
//



function oud_xml_tempsave( $xml_type, $xmlcode, $dag, $var, $waarde ) {
	global $db0;
	$db0->query( "INSERT INTO xml_import_flex_temp SET xml_type='".addslashes( $xml_type )."', xmlcode='".addslashes( $xmlcode )."', dag='".addslashes( $dag )."', var='".addslashes( $var )."', waarde='".addslashes( $waarde )."';" );
	if ( $db0->Errno>0 ) {
		return false;
	} else {
		return true;
	}
}

function xml_tempsave( $xml_type, $xmlcode, $dag, $var, $waarde ) {

	global $db0, $xml_tempsave_teller, $xml_tempsave_query, $xml_tempsave_teller_overall;

	if($xml_type=="save_last_values") {
		if($xml_tempsave_query) {
			$db0->query("INSERT INTO xml_import_flex_temp (xml_type, xmlcode, dag, var, waarde) VALUES ".substr($xml_tempsave_query,1).";");

			$xml_tempsave_teller = 0;
			$xml_tempsave_query = "";

			if ( $db0->Errno>0 ) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}

	} else {

		$xml_tempsave_teller++;
		$xml_tempsave_query .= ", ('".addslashes( $xml_type )."', '".addslashes( $xmlcode )."', '".addslashes( $dag )."', '".addslashes( $var )."', '".addslashes( $waarde )."')";

		if($xml_tempsave_teller>=5000) {

			$db0->query("INSERT INTO xml_import_flex_temp (xml_type, xmlcode, dag, var, waarde) VALUES ".substr($xml_tempsave_query,1).";");

			$xml_tempsave_teller = 0;
			$xml_tempsave_query = "";

			if ( $db0->Errno>0 ) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
}



//
// Leverancier: Marche Holiday
//

//$marche_server="oorl.iwlab.com";
//$marche_script="/holiday/bin/mh/APIserver.php";

$marche_server="api.marcheholiday.it";
$marche_script="/APIserver.php";

function marche_RPC_get_season( $date, $idls ) {
	$l=marche_get_info( "get_season", array( "date", "idls" ), array( $date, $idls ) );
	return $l;
}

function marche_RPC_get_costi_sta_set( $idls, $date, $dateb, $ids ) {
	$l=marche_get_info( "get_costi_sta_set", array( "idls", "date", "dateb", "ids" ), array( $idls, $date, $dateb, $ids ) );
	return $l;
}

function marche_RPC_get_house_calendar( $RPC_login, $RPC_pass, $idhouse, $da_data, $numweeks ) { $l=marche_get_info( "get_house_calendar", array( "RPC_login", "RPC_pass", "idhouse", "da_data", "numweeks" ), array( $RPC_login, $RPC_pass, $idhouse, $da_data, $numweeks ) );
	return $l;
};

function marche_RPC_get_house_availability( $RPC_login, $RPC_pass, $idhouse, $da_data, $numweeks ) { $l=marche_get_info( "get_house_availability", array( "RPC_login", "RPC_pass", "idhouse", "da_data", "numweeks" ), array( $RPC_login, $RPC_pass, $idhouse, $da_data, $numweeks ) );
	return $l;
};

function marche_get_info( $function, $pars, $vals ) {
	global $marche_server, $marche_script;
	if ( $pars[0]=="RPC_login" ) {
		$opts = stream_context_create(array(
			'http' => array(
				'timeout' => 10
				)
			)
		);
		$challenge=@file_get_contents( "http://".$marche_server.$marche_script."?challenge=1" , false, $opts);
		if ( $challenge =="" ) return "";
		$chap=md5( $challenge.$vals[1] );
	}
	//Qui genero l'xml da inviare al server
	$start=0; $xm="<REQUEST>\n  <FUNCTION>".$function."</FUNCTION>\n";
	if ( $chap ) { $xm.="  <AUTH>\n    <CHAP>$chap</CHAP>\n    <USER>".$vals[0]."</USER>\n  </AUTH>\n";
		$start=2;
	};
	$xm.="  <FIELDS>\n";
	for ( $i=$start; $pars[$i]!=""; $i++ ) $xm.="    <".$pars[$i].">".$vals[$i]."</".$pars[$i].">\n";
	$xm.="  </FIELDS>\n";
	$xm.="</REQUEST>\n";
	$req= "POST $marche_script HTTP/1.0\r\nHost: ".$marche_server."\r\nContent-Type: text/xml; charset=utf-8\r\nContent-Length: ".strlen( $xm )."\r\n\r\n".$xm;

	global $use_debug;
	//$use_debug=1;
	if ( $use_debug >0 ) echo "Request:<br><textarea rows=18 cols=80>$req</textarea><br>";
	$fs=@fsockopen( $marche_server, 80 );
	if ( $fs ) { fputs( $fs, $req );
		while ( $l=fgets( $fs ) ) $reply.=$l;
	};
	if ( $use_debug >0 ) echo "Reply:<br><textarea rows=18 cols=80>$reply</textarea><br>";

	//.. Qui devo togliere l'header e restituire l'xml.
	//magari gia' sotto forma di array di array. Fico.
	$rp=explode( "\n", $reply );
	$a=array();
	$res=array(); $i=-1;
	$perf="";
	foreach ( $rp as $line ) { if ( strstr( $line, "<PERFORMED" )!="" ) $perf="OK";
		if ( substr( $line, 0, 12 ) == "  <DATA_ROW>" ) { $a=array(); $i++;
		};
		if ( substr( $line, 0, 13 ) == "  </DATA_ROW>" ) $res[$i]=$a;
		if ( substr( $line, 0, 5 ) == "    <" ) { $name = substr( $line, 5, strpos( $line, ">" )-5 );
			$val=substr( $line, strlen( $name )+6 );
			$val=substr( $val, 0, strlen( $val )-( strlen( $name )+3 ) );
			$a[$name]=$val;
		};
	};
	if ( $perf!="" ) return "Ok";
	if ( $i<0 ) { return ""; } else return $res;
}


function marche_tariefopvragen( $idls, $date, $persone ) {
	$rs=array( 0, 0, 0, 0, 0, 0 );
	//Calcolo la seconda data..
	$ts=strtotime( $date ) + 3600*24*6 + 3600*2;
	$dateb=date( "Y-m-d", $ts );

	//Vedo se la data cade in una stagione.. mi puo' servire
	//$ids=fastquery("select dz_nome from stagioni where da_data <='$date' and a_data >='$dateb'");
	$a=marche_RPC_get_season( $date, $idls );
	//$a=RPC_get_season_between($date, $dateb, $idls);
	if ( is_array( $a ) ) {$ll=$a[0]; $ids=$ll['dz_nome']; };
	if ( $ids<1 ) $ids=0;

	//Pesco dal listino tutti i costi applicabili
	$a=marche_RPC_get_costi_sta_set( $idls, $date, $dateb, $ids );

	//    echo wt_dump($a);
	//    echo $idls." - ".$date." - ".$dateb." ".$ids."<br>";

	//$l="select costo, guadagno, guadagnoriv, costogio, guadagnogio, guadagnorivgio,
	//  obbligatorio, apersona from listini where idls='$idls'
	//  and (da_data='' or da_data<='$date') and ( a_data='' or  a_data>='$dateb')
	//  and (idstagione=0 or idstagione='$ids');";
	//$res=mysql_query($l);
	if ( is_array( $a ) ) { //Singolo costo, l'applicazione dipdende dai flags
		foreach ( $a as $l ) { $cost=$l['costo'];       if ( $l['apersona']>0 ) $cost=$cost * $persone;
			$gain=$l['guadagno'];    if ( $l['apersona']>0 ) $gain=$gain * $persone;
			$griv=$l['guadagnoriv']; if ( $l['apersona']>0 ) $griv=$griv * $persone;
			if ( $l['obbligatorio']>0 ) { $rs[0]=$rs[0]+$cost;
				$rs[2]=$rs[2]+$cost+$gain;
				$rs[4]=$rs[4]+$cost+$griv;
			};
			$rs[1]=$rs[1]+$cost;
			$rs[3]=$rs[3]+$cost+$gain;
			$rs[5]=$rs[5]+$cost+$griv;
		};
	};
	$ra[]=$rs[2]; $ra[]=$rs[3]; $ra[]=$rs[4]; $ra[]=$rs[5]; return $ra;
}



?>