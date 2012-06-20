<?php

#
#
# XML-export van de Top10's (t.b.v. Snowplaza)
#
# ?chad=KWX12 wordt overgenomen (tracker voor Snowplaza)
#
# voorbeeld-url: http://www.chalet.nl/xml/top10.php?chad=KWX12 (is voor Snowplaza)
#

set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");
#$cachefile=$unixdir."cache/feed_tradetracker_".$vars["website"].".xml";

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	header("Content-Type: text/plain; charset=utf-8");
} else {
	header("Content-Type: text/xml; charset=utf-8");
	echo "<";
	echo "?xml version=\"1.0\" encoding=\"utf-8\"?";
	echo ">\n";
}
echo "<trips>\n";


$top10website=1;
$db->query("SELECT s.seizoen_id, t.type_id, tw.week, tw.blokvolgorde, tw.bloknaam, ac.naam, ac.soortaccommodatie, ac.toonper, ac.accommodatie_id, ac.omschrijving, ac.aankomst_plusmin, ac.vertrek_plusmin, t.omschrijving AS tomschrijving, t.naam".$vars["ttv"]." AS tnaam, t.slaapkamers, t.badkamers, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, sg.naam AS skigebied, l.naam".$vars["ttv"]." AS land, l.begincode, s.seizoen_id FROM accommodatie ac, type t, plaats p, land l, skigebied sg, seizoen s, tarief tr, top10_week tw, top10_week_type twt WHERE (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tr.beschikbaar=1 AND tr.type_id=t.type_id AND tr.seizoen_id=tw.seizoen_id AND tr.seizoen_id=twt.seizoen_id AND tr.week=tw.week AND tr.week=twt.week AND ac.plaats_id=p.plaats_id AND t.websites LIKE '%".$vars["website"]."%' AND p.land_id=l.land_id AND p.skigebied_id=sg.skigebied_id AND t.accommodatie_id=ac.accommodatie_id AND s.type=".$vars["seizoentype"]." AND tw.site=".$top10website." AND twt.site=tw.site AND s.tonen>=2 AND s.seizoen_id=tw.seizoen_id AND s.seizoen_id=twt.seizoen_id AND t.type_id=twt.type_id AND ac.tonen=1 AND t.websites LIKE '%".$vars["website"]."%' AND t.tonen=1 AND tw.week>'".time()."' AND tw.blokvolgorde>0 ORDER BY tw.blokvolgorde, twt.volgorde, ac.naam;");
if($db->num_rows()) {

	while($db->next_record()) {
		$alletypes.=",".$db->f("type_id");
	}
	$alletypes=substr($alletypes,1);
	$db->seek();

	# Afwijkende vertrekdagen?
	if($alletypes) {
		$typeid_inquery_vertrekdag=$alletypes;
	} else {
		$typeid_inquery_vertrekdag="ALL";
	}
	include("../content/vertrekdagaanpassing.html");

	while($db->next_record()) {

		unset($aanbieding);
	
		$aanbieding[$db->f("seizoen_id")]=aanbiedinginfo($db->f("type_id"),$db->f("seizoen_id"),$db->f("week"));

		# Prijs bepalen
		unset($prijs);
		if($db->f("toonper")==3) {
			$db2->query("SELECT MIN(c_verkoop_site) AS prijs FROM tarief WHERE type_id='".$db->f("type_id")."' AND week='".$db->f("week")."' AND c_verkoop_site>0 AND beschikbaar=1;");
			if($db2->next_record()) {
				$prijs=$db2->f("prijs");
			}
		} else {
			$db2->query("SELECT MIN(tp.prijs) AS prijs FROM tarief t, tarief_personen tp WHERE tp.type_id='".$db->f("type_id")."' AND tp.week='".$db->f("week")."' AND tp.prijs>0 AND tp.personen='".$db->f("maxaantalpersonen")."' AND t.beschikbaar=1 AND t.type_id=tp.type_id AND t.week=tp.week AND t.seizoen_id=tp.seizoen_id;");
			if($db2->next_record()) {
				$prijs=$db2->f("prijs");
			}
		}
		
		if($prijs) {

			# aanbiedingen verwerken
			$prijs=verwerk_aanbieding($prijs,$aanbieding[$db->f("seizoen_id")]["typeid_sort"][$db->f("type_id")],$db->f("week"));
			
			$aantal_acc_per_blok[$db->f("seizoen_id")][$db->f("week")]++;

			echo "<trip>\n";
			echo "<bloknummer>".xml_text($db->f("blokvolgorde"))."</bloknummer>\n";
			echo "<bloknaam>".xml_text($db->f("bloknaam"))."</bloknaam>\n";
			echo "<continent>Europa</continent>\n";
			echo "<country>".xml_text($db->f("land"))."</country>\n";
			echo "<region>".xml_text($db->f("skigebied"))."</region>\n";
			echo "<city>".xml_text($db->f("plaats"))."</city>\n";
		
			$aantalpersonen=$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." ".($db->f("maxaantalpersonen")==1 ? "persoon" : "personen");
			$accnaam=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." - ".$aantalpersonen;
			
			echo "<name>".xml_text($accnaam)."</name>\n";
			echo "<stars></stars>\n";
		
			unset($description);
			if($db->f("omschrijving") or $db->f("tomschrijving")) {
				$description=$db->f("omschrijving");
				if($db->f("omschrijving") and $db->f("tomschrijving")) {
					$description.="\n\n";
				}
				$description.=$db->f("tomschrijving");
				echo "<description>".xml_text($description)."</description>\n";
			} else {
				echo "<description></description>\n";
			}
					
			$url=$vars["basehref"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/";
			if($_GET["chad"]) {
				$url.="?chad=".$_GET["chad"];
				
				if($_GET["chad"]=="KWX12") {
					# Snowplaza: utm-codes toevoegen t.b.v. Google Analytics
					$url.="&utm_source=Snowplaza&utm_medium=Snowplaza&utm_campaign=Aanbiedingen";
				}
			}
			echo "<deeplink>".xml_text($url)."</deeplink>\n";
			
			$imgurl="";
			if(file_exists("../pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
				$imgurl=$vars["basehref"]."pic/cms/types_specifiek/".$db->f("type_id").".jpg";
			} elseif(file_exists("../pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
				$imgurl=$vars["basehref"]."pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
			}
			if($imgurl) {
				echo "<image_url>".xml_text($imgurl)."</image_url>\n";
			}
			echo "<room>\n";
			
			echo "<price>".number_format($prijs,2,".","")."</price>\n";
			if($db->f("toonper")==3) {
				echo "<price_info>per accommodatie</price_info>\n";
			} else {
				echo "<price_info>per persoon inclusief skipas</price_info>\n";
			}
			# Exacte aankomstdatum
			$week=$db->f("week");
			if($vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)] or $db->f("aankomst_plusmin")) {
				$aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)]+$db->f("aankomst_plusmin"),date("Y",$week));
				$exacte_unixtime=$aangepaste_unixtime;
			} else {
				$exacte_unixtime=$week;
			}

			# Afwijkende vertrekdagen verwerken
			unset($aantalnachten_afwijking);
			$aantalnachten_afwijking=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)];
			$volgendeweek=mktime(0,0,0,date("n",$week),date("j",$week)+7,date("Y",$week));
			$aantalnachten_afwijking-=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$volgendeweek)];
			# Afwijkende verblijfsduur
			$aantalnachten_afwijking=$aantalnachten_afwijking+$db->f("aankomst_plusmin")-$db->f("vertrek_plusmin");
			$nachten=7-$aantalnachten_afwijking;
	
			echo "<date_departure>".date("Ymd",$exacte_unixtime)."</date_departure>\n";
			echo "<days>".($nachten+1)."</days>\n";
			if($db->f("toonper")==3) {
				echo "<inclusions>Logies</inclusions>\n";
			} else {
				echo "<inclusions>Logies + 6-daagse skipas</inclusions>\n";
			}
			echo "<transport>eigen vervoer</transport>\n";
			echo "<airport_departure></airport_departure>\n";
			echo "<airport_arrival></airport_arrival>\n";
			echo "</room>\n";
			echo "</trip>\n";
	
	/*		
			echo "<additional>\n";
			if($db->f("kwaliteit") or $db->f("tkwaliteit")) {
				if($db->f("tkwaliteit")) {
					$kwaliteit=$db->f("tkwaliteit");
				} else {
					$kwaliteit=$db->f("kwaliteit");
				}
				echo "<field name=\"stars\" value=\"".xml_text($kwaliteit,false)."\" />\n";
			}
	
			echo "<field name=\"country\" value=\"".xml_text($db->f("land"),false)."\" />\n";
			if($vars["seizoentype"]==1) {
				echo "<field name=\"ski_area\" value=\"".xml_text($db->f("skigebied"),false)."\" />\n";
				echo "<field name=\"resort\" value=\"".xml_text($db->f("plaats"),false)."\" />\n";
			} else {
				echo "<field name=\"region\" value=\"".xml_text($db->f("skigebied"),false)."\" />\n";
				echo "<field name=\"city\" value=\"".xml_text($db->f("plaats"),false)."\" />\n";
			}
			echo "<field name=\"persons_from\" value=\"".xml_text($db->f("optimaalaantalpersonen"),false)."\" />\n";
			echo "<field name=\"persons_to\" value=\"".xml_text($db->f("maxaantalpersonen"),false)."\" />\n";
			if($db->f("toonper")==3) {
				echo "<field name=\"price_description\" value=\"".xml_text("vanaf ".number_format($prijs,2,",",".")." euro per accommodatie",false)."\" />\n";
			} else {
				echo "<field name=\"price_description\" value=\"".xml_text("vanaf ".number_format($prijs,2,",",".")." euro per persoon incl. skipas",false)."\" />\n";
			}
			echo "<field name=\"number_of_nights\" value=\"".xml_text("7",false)."\" />\n";
			
			# Aanvullende afbeeldingen
			$foto=imagearray(array("accommodaties_aanvullend","types","accommodaties_aanvullend_onderaan","accommodaties_aanvullend_breed","types_breed"),array($db->f("accommodatie_id"),$db->f("type_id"),$db->f("accommodatie_id"),$db->f("accommodatie_id"),$db->f("type_id")),"../");
			if(is_array($foto["pic"])) {
				$fototeller=0;
				while(list($key,$value)=each($foto["pic"])) {
					$fototeller++;
					echo "<field name=\"extra_image_".$fototeller."\" value=\"".xml_text($vars["basehref"]."pic/cms/".$value,false)."\" />\n";
				}
			}
			if(is_array($foto["picbreed"])) {
				$fototeller=0;
				while(list($key,$value)=each($foto["picbreed"])) {
					$fototeller++;
					echo "<field name=\"extra_image_wide_".$fototeller."\" value=\"".xml_text($vars["basehref"]."pic/cms/".$value,false)."\" />\n";
				}
			}
		
			echo "</additional>\n";
			echo "<categories>\n";
			$categorie=ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")]);
			echo "<category name=\"".xml_text($categorie.(eregi("appartement",$categorie)||eregi("vakantiewoning",$categorie) ? "en" : "s"))."\" />\n";
			echo "</categories>\n";
			echo "</product>\n";
	*/		
			
		}
	}
}

echo "</trips>\n";

if(is_array($aantal_acc_per_blok)) {
	while(list($key,$value)=each($aantal_acc_per_blok)) {
		while(list($key2,$value2)=each($value)) {
			$db->query("UPDATE top10_week SET minderdan10='".($value2<10 ? "1" : "0")."' WHERE site='".$top10website."' AND seizoen_id='".$key."' AND week='".$key2."';");
		}
	}
}

?>