<?php

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
	if ( $pars[0]=="RPC_login" ) { $challenge=@file_get_contents( "http://".$marche_server.$marche_script."?challenge=1" );
		if ( $challenge =="" ) return "";
		$chap=md5( $challenge.$vals[1] );
	};
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



#
# marche_RPC_get_house_calendar
#
echo "<b>Test with marche_RPC_get_house_calendar</b>:";

$result = marche_RPC_get_house_calendar("chaletmh","chalet11",176,"2013-01-05",54);
echo "<pre>";
echo var_dump($result);
echo "</pre>";

echo "<hr>";

#
# marche_RPC_get_house_availability
#

echo "<b>Test with marche_RPC_get_house_availability</b>:";

$result = marche_RPC_get_house_availability("chaletmh","chalet11",176,"2013-01-05",54);
echo "<pre>";
echo var_dump($result);
echo "</pre>";


?>