<?php

#
#
# XML-export van alle accommodaties t.b.v. Zoover
#
#
#

$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	header ("Content-Type: text/plain; charset=utf-8");
} else {
	header ("Content-Type: text/xml; charset=utf-8");
	echo "<";
	echo "?xml version=\"1.0\" encoding=\"utf-8\"?";
	echo ">\n";
}

if($_GET["campaign"]=="zooverbe") {
	$campaigncode="?utm_source=zoover&utm_medium=partnersite&utm_campaign=zoover.be";
} elseif($_GET["campaign"]=="vakantiereiswijzer") {
	$campaigncode="?utm_source=zoover&utm_medium=partnersite&utm_campaign=vakantiereiswijzer";
} else {
	$campaigncode="?utm_source=zoover&utm_medium=partnersite&utm_campaign=zoover";
}


#echo "<accommodations xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"http://www.cleafs.com/tracker/productsfeed.xsd\">\n";
echo "<accommodations>\n";

unset($andquery);

# Bij SuperSki: accommodaties die al op Chalet.nl staan niet in de feed opnemen
if($vars["website"]=="W") {
	$andquery.=" AND t.websites NOT LIKE '%C%'";
}

// Only use Zell am See (120), Kaprun (113), Les Menuires (1), Oz en Oisans (45), Alpe d’Huez (44), Vallandry (28) and Val Thorens (30)
if($_GET["specialfeed"]) {
	$andquery.=" AND p.plaats_id IN (120, 113, 1, 45, 44, 28, 30)";
} else {
	$andquery.=" AND p.plaats_id NOT IN (120, 113, 1, 45, 44, 28, 30)";
}

$db->query("SELECT DISTINCT t.type_id, a.accommodatie_id, a.toonper, a.naam, a.kenmerken AS akenmerken, t.naam AS tnaam, a.zoekvolgorde AS azoekvolgorde, a.omschrijving, a.kwaliteit, a.gps_lat, a.gps_long, t.kwaliteit AS tkwaliteit, t.omschrijving AS tomschrijving, t.zoekvolgorde AS tzoekvolgorde, lv.zoekvolgorde AS lzoekvolgorde, t.optimaalaantalpersonen, t.maxaantalpersonen, a.soortaccommodatie, t.slaapkamers, t.badkamers, t.kenmerken AS tkenmerken, s.skigebied_id, s.naam AS skigebied, l.naam AS land, l.begincode, p.naam AS plaats FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".$vars["website"]."%' AND a.tonen=1 AND a.archief=0 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0".$andquery." ORDER BY t.optimaalaantalpersonen".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2" ? " LIMIT 0,30" : "").";");
while($db->next_record()) {
	if(!$accommodatie_getoond[$db->f("accommodatie_id")] and !$accommodatienaam_plaats_getoond[$db->f("naam")."_".$db->f("plaats")]) {

		$accommodatie_getoond[$db->f("accommodatie_id")]=true;
		$accommodatienaam_plaats_getoond[$db->f("naam")."_".$db->f("plaats")]=true;



/*
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

*/


		echo "<accommodation>\n";
		echo "<continent>Europe</continent>\n";
		echo "<country><![CDATA[".wt_utf8encode($db->f("land"))."]]></country>\n";
		echo "<region><![CDATA[".wt_utf8encode($db->f("skigebied"))."]]></region>\n";
		echo "<place><![CDATA[".wt_utf8encode($db->f("plaats"))."]]></place>\n";

#			$aantalpersonen=$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? "persoon" : "personen");
#			$accnaam=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." - ".$aantalpersonen;

#			$accnaam=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "");
		$accnaam=$db->f("naam");

		echo "<accommodationname><![CDATA[".wt_utf8encode($accnaam)."]]></accommodationname>\n";
		echo "<accommodationtype><![CDATA[".wt_utf8encode(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")]))."]]></accommodationtype>\n";

#			echo "<price><![CDATA[".$prijs."]]></price>\n";

		unset($description);
		if($db->f("omschrijving") or $db->f("tomschrijving")) {
			$description=$db->f("omschrijving");
			if($db->f("omschrijving") and $db->f("tomschrijving")) {
				$description.="\n\n";
			}
			$description.=$db->f("tomschrijving");
			echo "<description><![CDATA[".wt_utf8encode($description)."]]></description>\n";
		}

		$url=$vars["basehref"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/".$campaigncode;

/*
		# Hoofd-afbeelding
		$imgurl="";
		if(file_exists("../pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
			$imgurl=$vars["basehref"]."pic/cms/types_specifiek/".$db->f("type_id").".jpg";
		} elseif(file_exists("../pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
			$imgurl=$vars["basehref"]."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
		}
		if($imgurl) {
#				echo "<MainImageURL><![CDATA[".wt_utf8encode($imgurl)."]]></MainImageURL>\n";
		}

		# Extra afbeeldingen
		$img_array=imagearray(array("accommodaties_aanvullend","types","accommodaties_aanvullend_onderaan","accommodaties_aanvullend_breed","types_breed"),array($db->f("accommodatie_id"),$db->f("type_id"),$db->f("accommodatie_id"),$db->f("accommodatie_id"),$db->f("type_id")),"../");
		while(list($key,$value)=@each($img_array)) {
			while(list($key2,$value2)=@each($value)) {
#					if($value2) echo "<ExtraImageURL><![CDATA[".wt_utf8encode($vars["basehref"]."pic/cms/".$value2)."]]></ExtraImageURL>\n";
			}
		}

*/

#			echo "<additional>\n";
#			if($db->f("kwaliteit") or $db->f("tkwaliteit")) {
#				if($db->f("tkwaliteit")) {
#					$kwaliteit=$db->f("tkwaliteit");
#				} else {
#					$kwaliteit=$db->f("kwaliteit");
#				}
#				echo "<sterren>".$kwaliteit."</sterren>\n";
#			}
	#	echo "<categorie><![CDATA[".wt_utf8encode()."]]></categorie>\n";
	#	echo "<seizoen><![CDATA[".wt_utf8encode()."]]></seizoen>\n";
	#	echo "<vervoer><![CDATA[".wt_utf8encode()."]]></vervoer>\n";
	#	echo "<verzorging><![CDATA[".wt_utf8encode()."]]></verzorging>\n";
#			echo "<aantalpersonen_van><![CDATA[".wt_utf8encode($db->f("optimaalaantalpersonen"))."]]></aantalpersonen_van>\n";
#			echo "<aantalpersonen_tot><![CDATA[".wt_utf8encode($db->f("maxaantalpersonen"))."]]></aantalpersonen_tot>\n";
#			if($db->f("toonper")==3) {
#				echo "<prijs><![CDATA[".wt_utf8encode("vanaf ".number_format($prijs,2,",",".")." euro per accommodatie")."]]></prijs>\n";
#			} else {
#				echo "<prijs><![CDATA[".wt_utf8encode("vanaf ".number_format($prijs,2,",",".")." euro per persoon incl. skipas")."]]></prijs>\n";
#			}
#			echo "<aantalnachten><![CDATA[".wt_utf8encode("7")."]]></aantalnachten>\n";
#			echo "</additional>\n";

		echo "<accodeeplink><![CDATA[".wt_utf8encode($url)."]]></accodeeplink>\n";
		echo "<placedeeplink><![CDATA[".wt_utf8encode($vars["basehref"].txt("canonical_accommodatiepagina")."/".txt("menu_plaats")."/".wt_convert2url_seo($db->f("plaats"))."/".$campaigncode)."]]></placedeeplink>\n";
		echo "<regiondeeplink><![CDATA[".wt_utf8encode($vars["basehref"].txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("skigebied"))."/".$campaigncode)."]]></regiondeeplink>\n";
		echo "<countrydeeplink><![CDATA[".wt_utf8encode($vars["basehref"].txt("menu_land")."/".wt_convert2url_seo($db->f("land"))."/".$campaigncode)."]]></countrydeeplink>\n";
		echo "<id>".$db->f("type_id")."</id>\n";
		$gps_lat=trim($db->f("gps_lat"));
		$gps_long=trim($db->f("gps_long"));

		if($gps_lat and $gps_long) {
			if(preg_match("/^[0-9\.\-]+$/",$gps_lat) and preg_match("/^[0-9\.\-]+$/",$gps_long)) {
				echo "<latitude>".$gps_lat."</latitude>\n";
				echo "<longitude>".$gps_long."</longitude>\n";
			}
		}

		echo "</accommodation>\n";
	}
}

echo "</accommodations>\n";


function wt_utf8encode($text) {
	$return=$text;
	$return=ereg_replace(chr(145),"'",$return); # ‘ omzetten naar '
	$return=ereg_replace(chr(146),"'",$return); # ’ omzetten naar '
	$return=ereg_replace(chr(147),"\"",$return); # “ omzetten naar "
	$return=ereg_replace(chr(148),"\"",$return); # ” omzetten naar "
	$return=utf8_encode($return);

	return $return;
}

?>