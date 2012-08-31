<?php

#
# JSON-communicatie met de database voor het Chalet.nl-CMS
#


set_time_limit(30);
ignore_user_abort(false);

$mustlogin=false;
$geen_tracker_cookie=true;

include("admin/vars.php");


#mail("jeroen@webtastic.nl","rpc_json.php",$_SERVER["REQUEST_URI"]);

if($_GET["test"]) {

} else {
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=UTF-8');
}

if($_GET["t"]==1) {

	#
	# Op Google Maps accommodaties plaatsen
	#
	$db->query("SELECT accommodatie_id, tnaam, COUNT(type_id) AS aantal FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' GROUP BY accommodatie_id;");
	while($db->next_record()) {
		if($db->f("aantal")==1) {
			$typenaam[$db->f("accommodatie_id")]=$db->f("tnaam");
		}
		$typenaam_alle_acc[$db->f("accommodatie_id")]=$db->f("tnaam");
	}

#	$db->query("SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen FROM view_accommodatie WHERE gps_lat<='".addslashes($_GET["lat1"])."' AND gps_lat>='".addslashes($_GET["lat2"])."' AND gps_long<='".addslashes($_GET["long1"])."' AND gps_long>='".addslashes($_GET["long2"])."' AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' AND accommodatie_id<>'".addslashes($_GET["accid"])."' GROUP BY accommodatie_id ORDER BY accommodatie_id;");
#	$db->query("SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen FROM view_accommodatie WHERE gps_lat<=".addslashes($_GET["lat1"])." AND gps_lat>=".addslashes($_GET["lat2"])." AND gps_long<=".addslashes($_GET["long1"])." AND gps_long>=".addslashes($_GET["long2"])." AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' AND accommodatie_id<>'".addslashes($_GET["accid"])."' GROUP BY accommodatie_id ORDER BY accommodatie_id;");
#	$db->query("SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, tgps_lat, tgps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen FROM view_accommodatie WHERE ((gps_lat IS NOT NULL AND gps_long IS NOT NULL) OR ((tgps_lat IS NOT NULL AND tgps_long IS NOT NULL))) AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' GROUP BY accommodatie_id ORDER BY accommodatie_id;");

	$query["types"]="SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, tgps_lat, tgps_long, naam, plaats, skigebied, land, begincode, optimaalaantalpersonen, maxaantalpersonen FROM view_accommodatie WHERE tgps_lat IS NOT NULL AND tgps_long IS NOT NULL AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' ORDER BY accommodatie_id;";
	$query["accommodaties"]="SELECT accommodatie_id, soortaccommodatie, type_id, gps_lat, gps_long, tgps_lat, tgps_long, naam, plaats, skigebied, land, begincode, MIN(optimaalaantalpersonen) AS optimaalaantalpersonen, MAX(maxaantalpersonen) AS maxaantalpersonen FROM view_accommodatie WHERE gps_lat IS NOT NULL AND gps_long IS NOT NULL AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' GROUP BY accommodatie_id ORDER BY accommodatie_id, type_id;";
	
	while(list($key,$value)=each($query)) {
		$db->query($value);
#		wt_jabber("boschman@gmail.com",$db->lastquery);
		$return["ok"]=true;
		if($db->num_rows()) {
			while($db->next_record()) {
				if($key=="types") {
					$gps_lat=$db->f("tgps_lat");
					$gps_long=$db->f("tgps_long");
				} else {
					$gps_lat=$db->f("gps_lat");
					$gps_long=$db->f("gps_long");
				}
				if(!$algehad[$gps_lat."_".$gps_long]) {
					$return["aantal"]++;
					if($key=="types") {
						# gps-gegevens op type-niveau
						if($db->f("type_id")<>$_GET["typeid"]) {
							$return["acc"][$db->f("type_id")][1]=$gps_lat;
							$return["acc"][$db->f("type_id")][2]=$gps_long;
							$return["acc"][$db->f("type_id")]["naam"]=utf8_encode(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam")." ".$typenaam_alle_acc[$db->f("accommodatie_id")]);
							$return["acc"][$db->f("type_id")]["naamhtml"]=wt_he(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam")." ".$typenaam_alle_acc[$db->f("accommodatie_id")]);
							if(file_exists("pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
								$return["acc"][$db->f("type_id")]["afbeelding"]=imageurl("types_specifiek/".$db->f("type_id").".jpg",170,127);
							} elseif(file_exists("pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
								$return["acc"][$db->f("type_id")]["afbeelding"]=imageurl("accommodaties/".$db->f("accommodatie_id").".jpg",170,127);
							}
							$return["acc"][$db->f("type_id")]["plaatsland"]=utf8_encode(wt_he($db->f("plaats").", ".$db->f("land")));
							$return["acc"][$db->f("type_id")]["aantalpersonen"]=utf8_encode($db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? " - ".$db->f("maxaantalpersonen") : "")." ".html("personen"));
							$return["acc"][$db->f("type_id")]["url"]=utf8_encode($vars["path"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/");
						}
					} else {
						# gps-gegevens op accommodatie-niveau
						if($db->f("accommodatie_id")<>$_GET["accid"]) {
							$return["acc"][$db->f("type_id")][1]=$gps_lat;
							$return["acc"][$db->f("type_id")][2]=$gps_long;
							$return["acc"][$db->f("type_id")]["naam"]=utf8_encode(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($typenaam[$db->f("accommodatie_id")] ? " ".$typenaam[$db->f("accommodatie_id")] : ""));
							$return["acc"][$db->f("type_id")]["naamhtml"]=wt_he(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($typenaam[$db->f("accommodatie_id")] ? " ".$typenaam[$db->f("accommodatie_id")] : ""));
							if(file_exists("pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
								$return["acc"][$db->f("type_id")]["afbeelding"]=imageurl("accommodaties/".$db->f("accommodatie_id").".jpg",170,127);
							}
							$return["acc"][$db->f("type_id")]["plaatsland"]=utf8_encode(wt_he($db->f("plaats").", ".$db->f("land")));
							$return["acc"][$db->f("type_id")]["aantalpersonen"]=utf8_encode($db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? " - ".$db->f("maxaantalpersonen") : "")." ".html("personen"));
							$return["acc"][$db->f("type_id")]["url"]=utf8_encode($vars["path"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/");
						}
					}

					$algehad[$gps_lat."_".$gps_long]=true;
				}
			}
		}
	}
} elseif($_GET["t"]==2) {
	#
	# Op Google Maps plaatsen plaatsen (Italissima)
	#
	
	# Aantal
#	$db->query("SELECT plaats_id, skigebied_id, naam, gps_lat, gps_long FROM plaats WHERE gps_lat IS NOT NULL AND gps_long IS NOT NULL AND wzt=2 AND websites LIKE '%".$vars["website"]."%' ORDER BY plaats_id;");

	unset($andquery);
	if(preg_match("/MSIE [67]/",$_SERVER["HTTP_USER_AGENT"])) {
		# Bij MSIE 6 en 7: alleen plaatsen uit de actieve regio tonen (vanwege de Javascript-traagheid van die browsers)
		$andquery.=" AND p.skigebied_id='".intval($_GET["skigebiedid"])."'";
	}


	$db->query("SELECT count(t.type_id) AS aantal, p.plaats_id, p.skigebied_id, p.naam, p.gps_lat, p.gps_long, s.naam AS skigebied FROM plaats p, land l, type t, accommodatie a, skigebied s WHERE p.skigebied_id=s.skigebied_id AND a.weekendski=0 AND t.accommodatie_id=a.accommodatie_id AND t.tonen=1 AND a.tonen=1 AND t.websites LIKE '%".$vars["website"]."%' AND a.plaats_id=p.plaats_id AND l.land_id=p.land_id".$andquery." GROUP BY p.plaats_id;");
#	wt_jabber("boschman@gmail.com",$db->lastquery);
	if($db->num_rows()) {
		$return["ok"]=true;
		while($db->next_record()) {
			$return["plaats"][$db->f("plaats_id")][1]=$db->f("gps_lat");
			$return["plaats"][$db->f("plaats_id")][2]=$db->f("gps_long");
			$return["plaats"][$db->f("plaats_id")]["naam"]=utf8_encode($db->f("naam"));
			$return["plaats"][$db->f("plaats_id")]["naamhtml"]=wt_he($db->f("naam"));
			if($db->f("skigebied_id")==$_GET["skigebiedid"]) {
				$return["plaats"][$db->f("plaats_id")]["binnengebied"]=1;
			} else {
				$return["plaats"][$db->f("plaats_id")]["binnengebied"]=0;
			}
			$return["plaats"][$db->f("plaats_id")]["aantalacc"]="<a href=\"".$vars["path"]."plaats/".wt_convert2url($db->f("naam"))."/\">".$db->f("aantal")." ".($db->f("aantal")==1 ? "vakantiehuis" : "vakantiehuizen")."</a>";
			$return["plaats"][$db->f("plaats_id")]["skigebied"]=wt_he($db->f("skigebied"));
			$return["aantal"]++;
		}
	}
} elseif($_GET["t"]==3) {
	#
	# Autocomplete zoekformulier
	#
	require($unixdir."admin/class.search.php");

	$search=new search;
	$search->settings["delete_ignorewords"]=false;
	$search->settings["only_whole_words"]=false;
	$search->wordsplit($_GET["q"]);
	
	$andquery1=$search->regexpquery(array("naam"));
	$andquery2=$search->regexpquery(array("plaats","plaats_altnaam"));
	$andquery3=$search->regexpquery(array("skigebied","skigebied_altnaam"));
	$andquery4=$search->regexpquery(array("woord"));
	
	if($andquery1) {
		$query[1]="SELECT naam AS result FROM view_accommodatie WHERE (".$andquery1.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%';";
		$query[2]="SELECT plaats AS result FROM view_accommodatie WHERE (".$andquery2.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%';";
		$query[3]="SELECT skigebied AS result FROM view_accommodatie WHERE (".$andquery3.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%';";
		$query[4]="SELECT woord AS result FROM woord_autocomplete WHERE wzt='".$vars["seizoentype"]."' AND taal='".$vars["taal"]."' AND (".$andquery4.");";
		
		while(list($key,$value)=each($query)) {
			$db->query($value);
			while($db->next_record()) {
				if($db->f("result")) $results[$db->f("result")]=true;
			}
		}
	}
	
	if(is_array($woorden_autocomplete)) {
		while(list($key,$value)=each($woorden_autocomplete)) {
		
		}
	}

	$return["ok"]=true;	
	if(is_array($results)) {

		setlocale(LC_COLLATE,"nl_NL.ISO8859-1");
		ksort($results,SORT_LOCALE_STRING);
		setlocale(LC_COLLATE,"C");
		
		while(list($key,$value)=each($results)) {
			if($key and $return["totalResultsCount"]<=10) {
				$return["totalResultsCount"]++;
				$return["results"][$return["totalResultsCount"]]["name"]=utf8_encode($key);
			}
		}
	} else {
		$return["results"][1]["name"]="";
	}
} elseif($_GET["t"]==4) {
	#
	# Favorietenfunctie
	#
	if(isset($_GET['klantID'])&& isset($_GET['accommodatie'])) {
		if($_GET['action']=="insert") {
			$klantID=$_GET['klantID'];
			$accommodatieID=$_GET['accommodatie'];
			$db->query("INSERT INTO bezoeker_favoriet(bezoeker_id, type_id, adddatetime)VALUES('".addslashes($klantID)."','".addslashes($accommodatieID)."',NOW());");
			$return1=$db->query("SELECT b.type_id, b.bezoeker_id, t.websites, t.type_id FROM  bezoeker_favoriet b, type t WHERE b.bezoeker_id='".addslashes($_COOKIE["sch"])."' AND b.type_id=t.type_id AND t.websites LIKE '%".$vars["website"]."%';");
			$return["aantal"]=count($return1);
			//return count($return);
		}
		elseif($_GET['action']=="delete"){
			$klantID=$_GET['klantID'];
			$accommodatieID=$_GET['accommodatie'];
			$db->query("DELETE FROM bezoeker_favoriet WHERE bezoeker_id='".addslashes($klantID)."' AND type_id='".addslashes($accommodatieID)."';");
			$return1=$db->query("SELECT b.type_id, b.bezoeker_id, t.websites, t.type_id FROM  bezoeker_favoriet b, type t WHERE b.bezoeker_id='".addslashes($_COOKIE["sch"])."' AND b.type_id=t.type_id AND t.websites LIKE '%".$vars["website"]."%';");
			$return["aantal"]=count($return1);
			//return count($return);
		}
	
	}
	
	if(!$return["aantal"]) $return["aantal"]=0;

	# Communicatie met dit script via jQuery


#	$.getJSON("rpc_json.php", {
#		"t": 4,
#		"klantID": "waarde",
#		"accommodatie": "waarde"
#	}, function(data) {
#		if(data.ok) {
#			
#		}
#	});
	
	


	$return["ok"]=true;
#	$return["waarde_die_je_wilt_terugsturen"]="waarde";
}

echo json_encode($return);

?>