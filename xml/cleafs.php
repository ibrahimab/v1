<?php

#
#
# XML-export van alle accommodaties t.b.v. Cleafs
# XSD: http://www.cleafs.com/tracker/productsfeed.xsd
#
# ========================>>>>>>>>>>>>>>>> LET OP: bij deze file wordt een cache gebruikt! <<<<<<<<<<<<<<<<==================================
#
#
#

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");
$cachefile=$unixdir."cache/feed_cleafs_".$vars["website"].".xml";

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	header ("Content-Type: text/plain; charset=utf-8");
} elseif(@filemtime($cachefile)>=(time()-86400) and !$_GET["nocache"]) {
	header("Content-Type: text/xml; charset=utf-8");
	$content=file_get_contents($cachefile);
	echo $content;
	exit;
} else {
	header("Content-Type: text/xml; charset=utf-8");
	echo "<";
	echo "?xml version=\"1.0\" encoding=\"utf-8\"?";
	echo ">\n";
}

#echo "<productFeed xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"http://www.cleafs.com/tracker/productsfeed.xsd\">\n";
echo "<productFeed>\n";

$db->query("SELECT DISTINCT t.type_id, a.accommodatie_id, a.toonper, a.naam, a.kenmerken AS akenmerken, t.naam AS tnaam, a.zoekvolgorde AS azoekvolgorde, a.omschrijving, a.kwaliteit, a.gps_lat, a.gps_long, a.afstandwinkel, a.afstandwinkelextra, a.afstandrestaurant, a.afstandrestaurantextra, a.afstandpiste, a.afstandpisteextra, a.afstandskilift, a.afstandskiliftextra, a.afstandloipe, a.afstandloipeextra, a.afstandskibushalte, a.afstandskibushalteextra, a.afstandstrand, a.afstandstrandextra, a.afstandzwembad, a.afstandzwembadextra, a.afstandzwemwater, a.afstandzwemwaterextra, a.afstandgolfbaan, a.afstandgolfbaanextra, t.kwaliteit AS tkwaliteit, t.omschrijving AS tomschrijving, t.zoekvolgorde AS tzoekvolgorde, lv.zoekvolgorde AS lzoekvolgorde, t.optimaalaantalpersonen, t.maxaantalpersonen, a.soortaccommodatie, t.slaapkamers, t.badkamers, t.kenmerken AS tkenmerken, s.skigebied_id, s.naam AS skigebied, l.naam AS land, l.begincode, p.naam AS plaats FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".$vars["website"]."%' AND a.tonen=1 AND a.archief=0 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? " LIMIT 0,10" : "").";");
while($db->next_record()) {

	# Prijs bepalen
	unset($prijs);
	if($db->f("toonper")==3) {
		$db2->query("SELECT MIN(c_verkoop_site) AS prijs FROM tarief WHERE type_id='".$db->f("type_id")."' AND week>'".(time()+604800)."' AND c_verkoop_site>0 AND beschikbaar=1;");
		if($db2->next_record()) {
			$prijs=$db2->f("prijs");
		}
	} else {
		$db2->query("SELECT MIN(tp.prijs) AS prijs FROM tarief t, tarief_personen tp WHERE tp.type_id='".$db->f("type_id")."' AND tp.week>'".(time()+604800)."' AND tp.prijs>0 AND tp.personen='".$db->f("maxaantalpersonen")."' AND t.beschikbaar=1 AND t.type_id=tp.type_id AND t.week=tp.week AND t.seizoen_id=tp.seizoen_id;");
		if($db2->next_record()) {
			$prijs=$db2->f("prijs");
		}
	}

	# Aanbiedingen verwerken
	

	if($prijs) {
		echo "<product id=\"".$db->f("type_id")."\">\n";
	
		$aantalpersonen=$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? "persoon" : "personen");
		$accnaam=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." - ".$aantalpersonen;
		
		echo "<name><![CDATA[".wt_utf8encode($accnaam)."]]></name>\n";
		echo "<price><![CDATA[".$prijs."]]></price>\n";
	
		unset($description);
		if($db->f("omschrijving") or $db->f("tomschrijving")) {
			$description=$db->f("omschrijving");
			if($db->f("omschrijving") and $db->f("tomschrijving")) {
				$description.="\n\n";
			}
			$description.=$db->f("tomschrijving");
			echo "<description><![CDATA[".wt_utf8encode($description)."]]></description>\n";
		}
		
		$url=$vars["basehref"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/";
		echo "<productURL><![CDATA[".wt_utf8encode($url)."]]></productURL>\n";
		
		$imgurl="";
		if(file_exists("../pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
			$imgurl=$vars["basehref"]."pic/cms/types_specifiek/".$db->f("type_id").".jpg";
		} elseif(file_exists("../pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
			$imgurl=$vars["basehref"]."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
		}
		if($imgurl) {
			echo "<imageURL><![CDATA[".wt_utf8encode($imgurl)."]]></imageURL>\n";
		}
		echo "<additional>\n";
		if($db->f("kwaliteit") or $db->f("tkwaliteit")) {
			if($db->f("tkwaliteit")) {
				$kwaliteit=$db->f("tkwaliteit");
			} else {
				$kwaliteit=$db->f("kwaliteit");
			}
			echo "<sterren>".$kwaliteit."</sterren>\n";
		}
		
		echo "<soort><![CDATA[".wt_utf8encode(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")]))."]]></soort>\n";
		if($db->f("gps_lat") and $db->f("gps_long")) {
			echo "<gps_latitude>".wt_utf8encode($db->f("gps_lat"))."</gps_latitude>\n";
			echo "<gps_longitude>".wt_utf8encode($db->f("gps_long"))."</gps_longitude>\n";
		}
		
		echo "<land><![CDATA[".wt_utf8encode($db->f("land"))."]]></land>\n";
	#	echo "<categorie><![CDATA[".wt_utf8encode()."]]></categorie>\n";
		if($vars["seizoentype"]==1) {
			echo "<skigebied><![CDATA[".wt_utf8encode($db->f("skigebied"))."]]></skigebied>\n";
		} else {
			echo "<regio><![CDATA[".wt_utf8encode($db->f("skigebied"))."]]></regio>\n";
		}
		echo "<plaats><![CDATA[".wt_utf8encode($db->f("plaats"))."]]></plaats>\n";
	#	echo "<seizoen><![CDATA[".wt_utf8encode()."]]></seizoen>\n";
	#	echo "<vervoer><![CDATA[".wt_utf8encode()."]]></vervoer>\n";
	#	echo "<verzorging><![CDATA[".wt_utf8encode()."]]></verzorging>\n";
		echo "<aantalpersonen_van><![CDATA[".wt_utf8encode($db->f("optimaalaantalpersonen"))."]]></aantalpersonen_van>\n";
		echo "<aantalpersonen_tot><![CDATA[".wt_utf8encode($db->f("maxaantalpersonen"))."]]></aantalpersonen_tot>\n";
		if($db->f("toonper")==3) {
			echo "<prijs><![CDATA[".wt_utf8encode("vanaf ".number_format($prijs,2,",",".")." euro per accommodatie")."]]></prijs>\n";
		} else {
			echo "<prijs><![CDATA[".wt_utf8encode("vanaf ".number_format($prijs,2,",",".")." euro per persoon incl. skipas")."]]></prijs>\n";
		}
		echo "<aantalnachten><![CDATA[".wt_utf8encode("7")."]]></aantalnachten>\n";

		# Afstanden
		if($vars["seizoentype"]==1) {
			# winter
			$doorloop_afstanden=array(
				"afstandwinkel"=>"afstand_winkel",
				"afstandrestaurant"=>"afstand_restaurant",
				"afstandpiste"=>"afstand_piste",
				"afstandskilift"=>"afstand_skilift",
				"afstandloipe"=>"afstand_loipe",
				"afstandskibushalte"=>"afstand_skibushalte"
			);
		} elseif($vars["seizoentype"]==2) {
			# zomer
			$doorloop_afstanden=array(
				"afstandwinkel"=>"afstand_winkel",
				"afstandrestaurant"=>"afstand_restaurant",
				"afstandstrand"=>"afstand_strand",
				"afstandzwembad"=>"afstand_zwembad",
				"afstandzwemwater"=>"afstand_zwemwater",
				"afstandgolfbaan"=>"afstand_golfcourse"
			);
		}
		while(list($key,$value)=@each($doorloop_afstanden)) {
			if($db->f($key)) {
				echo "<".$value."><![CDATA[".wt_utf8encode(toonafstand($db->f($key),$db->f($key."extra"),txt("meter","toonaccommodatie")))."]]></".$value.">\n";
			}
		}
		
		# Aanvullende afbeeldingen
		$foto=imagearray(array("accommodaties_aanvullend","types","accommodaties_aanvullend_onderaan","accommodaties_aanvullend_breed","types_breed"),array($db->f("accommodatie_id"),$db->f("type_id"),$db->f("accommodatie_id"),$db->f("accommodatie_id"),$db->f("type_id")),"../");
		if(is_array($foto["pic"])) {
			$fototeller=0;
			while(list($key,$value)=each($foto["pic"])) {
				$fototeller++;
				echo "<extra_image_".$fototeller."><![CDATA[".wt_utf8encode($vars["basehref"]."pic/cms/".$value)."]]></extra_image_".$fototeller.">\n";
			}
		}
		if(is_array($foto["picbreed"])) {
			$fototeller=0;
			while(list($key,$value)=each($foto["picbreed"])) {
				$fototeller++;
				echo "<extra_image_wide_".$fototeller."><![CDATA[".wt_utf8encode($vars["basehref"]."pic/cms/".$value)."]]></extra_image_wide_".$fototeller.">\n";
			}
		}
		
		echo "</additional>\n";
		echo "</product>\n";
	}
}

echo "</productFeed>\n";

function wt_utf8encode($text) {
	$return=$text;
	$return=ereg_replace(chr(145),"'",$return); # ‘ omzetten naar '
	$return=ereg_replace(chr(147),"\"",$return); # “ omzetten naar "
	$return=ereg_replace(chr(148),"\"",$return); # ” omzetten naar "
	$return=utf8_encode($return);
	
	return $return;
}

?>