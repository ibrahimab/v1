<?php

$mustlogin=true;

include("admin/vars.php");

if($_GET["add"]==5 or $_GET["edit"]==5) {
	# javascript-functie voor het toevoegen van gekoppelde regio's
	$layout->bodyonload="skigebied_dubbel('onload')";
}

if(!$_GET["wzt"]) {
	if($_GET["5k0"]) {
		$db->query("SELECT wzt FROM skigebied WHERE plaats_id='".addslashes($_GET["5k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}

if($_GET["add"]<>5 and $_GET["edit"]<>5) {
	// determine number of toon_op_homepage-villages
	$db->query("SELECT COUNT(p.plaats_id) AS aantal, s.skigebied_id FROM plaats p INNER JOIN skigebied s USING(skigebied_id) WHERE s.toon_op_homepage=1 AND p.toon_op_homepage=1 GROUP BY p.skigebied_id;");
	while($db->next_record()) {
		$toon_op_homepage_aantal[$db->f("skigebied_id")] = $db->f("aantal");
		if($db->f("aantal")>=2) {
			$toon_op_homepage[$db->f("skigebied_id")] = "ja";
		}
	}
}

# Skigebieden-array vullen voor koppelingen
$db->query("SELECT DISTINCT s.skigebied_id, s.naam, l.naam AS land FROM skigebied s, plaats p, land l WHERE s.wzt='".addslashes($_GET["wzt"])."' AND p.skigebied_id=s.skigebied_id AND p.land_id=l.land_id ORDER BY l.naam, s.naam;");
while($db->next_record()) {
	if($_GET["5k0"]<>$db->f("skigebied_id")) {
		if($vars["skigebieden"][$db->f("skigebied_id")]) {
			$vars["skigebieden"][$db->f("skigebied_id")]="(meerdere landen) - ".$db->f("naam");
		} else {
			$vars["skigebieden"][$db->f("skigebied_id")]=$db->f("land")." - ".$db->f("naam");
		}
		if($skigebied_inquery) $skigebied_inquery.=",".$db->f("skigebied_id"); else $skigebied_inquery=$db->f("skigebied_id");
	}
}

if(!$skigebied_inquery) $skigebied_inquery="0";
$db->query("SELECT skigebied_id, naam FROM skigebied WHERE wzt='".addslashes($_GET["wzt"])."' AND skigebied_id NOT IN (".$skigebied_inquery.");");
while($db->next_record()) {
	if($_GET["5k0"]<>$db->f("skigebied_id")) {
		$vars["skigebieden"][$db->f("skigebied_id")]="(land onbekend) - ".$db->f("naam");
	}
}
asort($vars["skigebieden"]);

if($_GET["5k0"]) {
	# is dit een Italiaanse regio?
	$db->query("SELECT skigebied_id FROM skigebied WHERE skigebied_id='".addslashes($_GET["5k0"])."' AND websites LIKE '%I%';");
	if($db->next_record()) {
		$italissima=true;
	}

	# is dit een Zomerhuisje-regio?
	$db->query("SELECT skigebied_id FROM skigebied WHERE skigebied_id='".addslashes($_GET["5k0"])."' AND websites LIKE '%Z%';");
	if($db->next_record()) {
		$zomerhuisje=true;
	}
}



$cms->settings[5]["list"]["show_icon"]=true;
$cms->settings[5]["list"]["edit_icon"]=true;
$cms->settings[5]["list"]["delete_icon"]=true;

$cms->db[5]["where"]="wzt='".addslashes($_GET["wzt"])."'";
$cms->db[5]["set"]="wzt='".addslashes($_GET["wzt"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(5,"text","naam");
if($vars["cmstaal"]) $cms->db_field(5,"text","naam_".$vars["cmstaal"]);

# inactieve sites uitzetten
while(list($key,$value)=each($vars["websites_inactief"])) {
	unset($vars["websites_wzt"][$_GET["wzt"]][$key]);
}
$cms->db_field(5,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(5,"select","toon_op_homepage_list","skigebied_id",array("selection"=>$toon_op_homepage));
$cms->db_field(5,"select","toon_op_homepage_aantal_plaatsen","skigebied_id",array("selection"=>$toon_op_homepage_aantal));
$cms->db_field(5,"text","altnaam");
$cms->db_field(5,"text","altnaam_zichtbaar");
$cms->db_field(5,"text","kortenaam1");
$cms->db_field(5,"text","kortenaam2");
$cms->db_field(5,"text","kortenaam3");
$cms->db_field(5,"text","kortenaam4");
$cms->db_field(5,"textarea","xmlnaam");
#$cms->db_field(5,"checkbox","kenmerken","",array("selection"=>$vars["kenmerken_skigebied_".$_GET["wzt"]]));
$cms->db_field(5,"multiradio","kenmerken","",array("selection"=>$vars["kenmerken_skigebied_".$_GET["wzt"]],"multiselection"=>array(1=>"ja",2=>"nee",3=>"onbekend",4=>"niet relevant"),"multiselectionfields"=>array(1=>"kenmerken",2=>"kenmerken_nee",3=>"kenmerken_onbekend",4=>"kenmerken_irrelevant")));
$cms->db_field(5,"yesno","toon_op_homepage");
$cms->db_field(5,"text","korteomschrijving");
$korteomschrijving_info = array("info"=>"Vul een korte/krachtige omschrijving van de skigebied in 1 zin voor op de skigebied pagina (voor bezoekers en zoekmachines).");
if($vars["cmstaal"]) $cms->db_field(5,"text","korteomschrijving_".$vars["cmstaal"]);
$cms->db_field(5,"textarea","omschrijving");
if($vars["cmstaal"]) $cms->db_field(5,"textarea","omschrijving_".$vars["cmstaal"]);
if($italissima) {
	$vars["omschrijving_afbreken_na"]=array(1=>"1 alinea",2=>"2 alinea's",3=>"3 alinea's",4=>"4 alinea's",5=>"5 alinea's",6=>"6 alinea's");
	$cms->db_field(5,"select","omschrijving_afbreken_na","",array("selection"=>$vars["omschrijving_afbreken_na"]));
}
$cms->db_field(5,"text","descriptiontag");
if($vars["cmstaal"]) $cms->db_field(5,"text","descriptiontag_".$vars["cmstaal"]);
$cms->db_field(5,"integer","minhoogte");
$cms->db_field(5,"integer","maxhoogte");
$cms->db_field(5,"select","skiwaardering","",array("selection"=>$vars["kwaliteit"]));
$cms->db_field(5,"select","snowboardwaardering","",array("selection"=>$vars["kwaliteit"]));
$cms->db_field(5,"integer","aantalliften");
$cms->db_field(5,"integer","aantalstoeltjesliften");
$cms->db_field(5,"integer","aantalsleepliften");
$cms->db_field(5,"integer","aantalcabineliften");
$cms->db_field(5,"integer","kilometerpiste");
$cms->db_field(5,"integer","aantalgroenepistes");
$cms->db_field(5,"integer","kmgroenepistes");
$cms->db_field(5,"integer","aantalblauwepistes");
$cms->db_field(5,"integer","kmblauwepistes");
$cms->db_field(5,"integer","aantalrodepistes");
$cms->db_field(5,"integer","kmrodepistes");
$cms->db_field(5,"integer","aantalzwartepistes");
$cms->db_field(5,"integer","kmzwartepistes");
$cms->db_field(5,"integer","aantalloipes");
$cms->db_field(5,"integer","kmloipes");
$cms->db_field(5,"url","weerbericht");
$cms->db_field(5,"url","webcam");
$cms->db_field(5,"url","kaart");
$cms->db_field(5,"textarea","omschrijvingskipasbasis");
if($vars["cmstaal"]) $cms->db_field(5,"textarea","omschrijvingskipasbasis_".$vars["cmstaal"]);
$cms->db_field(5,"textarea","omschrijvingskipasuitgebreid");
if($vars["cmstaal"]) $cms->db_field(5,"textarea","omschrijvingskipasuitgebreid_".$vars["cmstaal"]);
$cms->db_field(5,"picture","afbeelding","",array("savelocation"=>"pic/cms/skigebieden/","filetype"=>"jpg","multiple"=>true));
// $cms->db_field(5,"picture","afbeelding_breed","",array("savelocation"=>"pic/cms/skigebieden_breed/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(5,"picture","landkaart","",array("savelocation"=>"pic/cms/skigebieden_landkaarten/","filetype"=>"gif"));
$cms->db_field(5,"picture","pistekaart","",array("savelocation"=>"pic/cms/skigebieden_pistekaarten/","filetype"=>"jpg"));
for($i=1;$i<=5;$i++) {
	for($j=1;$j<=5;$j++) {
#		$cms->db_field(5,"select","koppeling_".$i."_".$j,"",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>($_GET["5k0"] ? "skigebied_id<>'".addslashes($_GET["5k0"])."' AND " : "")."wzt='".addslashes($_GET["wzt"])."'"));
		$cms->db_field(5,"select","koppeling_".$i."_".$j,"",array("selection"=>$vars["skigebieden"]));
	}
}
if($italissima) {
	$cms->db_field(5,"select","kleurcode","",array("selection"=>$vars["themakleurencombinatie"]));
	$cms->db_field(5,"picture","afbeelding_italissima","",array("savelocation"=>"pic/cms/skigebieden_hoofdfoto/","filetype"=>"jpg"));
	$cms->db_field(5,"picture","topafbeelding_italissima","",array("savelocation"=>"pic/cms/skigebieden_topfoto/","filetype"=>"jpg"));
	$cms->db_field(5,"text","accommodatiecodes");
	$cms->db_field(5,"integer","googlemaps_zoomlevel");
}

if($zomerhuisje) {
	$cms->db_field(5,"picture","bestemmingen_zomerhuisje","",array("savelocation"=>"pic/cms/bestemmingen_zomerhuisje/","filetype"=>"jpg"));
}


# Video
$cms->db_field(5,"yesno","video");
$cms->db_field(5,"url","video_url");

# foto-onderschrift
$cms->db_field(5,"text","foto_onderschrift");


# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(5,"naam","Naam");
$cms->list_field(5,"websites","Sites");
$cms->list_field(5,"toon_op_homepage_list","Op homepage");
$cms->list_field(5,"toon_op_homepage_aantal_plaatsen","Aantal plaatsen op homepage");


# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(5,0,"websites","Toon in totaaloverzicht op",array("selection"=>($_GET["wzt"]==1 ? "B,C,T,W" : "N,O,Z")),"",array("one_per_line"=>true));
if($_GET["wzt"]==1) {
	$cms->edit_field(5,0,"toon_op_homepage","Toon dit skigebied op de homepage van bovenstaande sites (nog niet in gebruik)");
} else {
	$cms->edit_field(5,0,"toon_op_homepage","Toon deze regio op de homepage van bovenstaande sites (nog niet in gebruik)");
}

if($vars["cmstaal"]) {
	$cms->edit_field(5,1,"naam", "Naam NL","",array("noedit"=>true));
	$cms->edit_field(5,1,"naam_".$vars["cmstaal"], "Naam ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(5,1,"naam");
}
$cms->edit_field(5,0,"altnaam","Zoekwoorden (zoekformulier)");
$cms->edit_field(5,0,"altnaam_zichtbaar","Alternatieve spelling (zoekformulier)");
if($vars["cmstaal"]) {

	$cms->edit_field(5,0,"korteomschrijving","Korte omschrijving NL","","",$korteomschrijving_info);
	$cms->edit_field(5,0,"korteomschrijving_".$vars["cmstaal"],"Korte omschrijving ".strtoupper($vars["cmstaal"]),"","",$korteomschrijving_info);

	$cms->edit_field(5,1,"omschrijving","Omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(5,1,"omschrijving_".$vars["cmstaal"],"Omschrijving ".strtoupper($vars["cmstaal"]));

} else {

	$cms->edit_field(5,0,"korteomschrijving","Korte omschrijving","","",$korteomschrijving_info);
	$cms->edit_field(5,1,"omschrijving","Omschrijving","","",array("rows"=>20,"info"=>$vars["wysiwyg_info"]));
}

$cms->edit_field(5,0,"kenmerken","Kenmerken");
$cms->edit_field(5,0,"htmlrow","<hr><b>Google</b><br><br><i>Gebruik geen sitenamen in Google-teksten (dit wordt automatisch door het systeem gedaan).</i>");
if($vars["cmstaal"]) {
	$cms->edit_field(5,0,"descriptiontag","Description NL","",array("noedit"=>true));
	$cms->edit_field(5,0,"descriptiontag_".$vars["cmstaal"],"Description ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(5,0,"descriptiontag","Description (ongeveer 100 - 159 karakters)");
}

$cms->edit_field(5,0,"htmlrow","<hr><b>Korte namen (voor het zoek-en-boekformulier aan de linkerkant)</b>");
$cms->edit_field(5,0,"kortenaam1","Korte naam 1");
$cms->edit_field(5,0,"kortenaam2","Korte naam 2");
$cms->edit_field(5,0,"kortenaam3","Korte naam 3");
$cms->edit_field(5,0,"kortenaam4","Korte naam 4");
$cms->edit_field(5,0,"htmlrow","<hr><b>XML-namen</b><br>Geef de namen die in de XML-export gebruikt worden voor deze regio (gescheiden door komma's)");
$cms->edit_field(5,0,"xmlnaam","XML-namen");
if($_GET["wzt"]==1) {
	$cms->edit_field(5,0,"htmlrow","<hr>");
	$cms->edit_field(5,0,"minhoogte","Minimumhoogte (in meters)");
	$cms->edit_field(5,0,"maxhoogte","Maximumhoogte (in meters)");
	$cms->edit_field(5,0,"skiwaardering","Waardering skiën");
	$cms->edit_field(5,0,"snowboardwaardering","Waardering snowboarden");
	$cms->edit_field(5,0,"htmlrow","<hr><b>Liften, pistes, loipes</b>");
	$cms->edit_field(5,0,"aantalliften","Aantal liften");
	$cms->edit_field(5,0,"aantalstoeltjesliften","Aantal stoeltjesliften");
	$cms->edit_field(5,0,"aantalsleepliften","Aantal sleepliften");
	$cms->edit_field(5,0,"aantalcabineliften","Aantal cabineliften");
	$cms->edit_field(5,1,"kilometerpiste","Aantal kilometer pistes");
	$cms->edit_field(5,0,"aantalgroenepistes","Aantal groene pistes");
	$cms->edit_field(5,0,"kmgroenepistes","Aantal kilometer groene pistes");
	$cms->edit_field(5,0,"aantalblauwepistes","Aantal blauwe pistes");
	$cms->edit_field(5,0,"kmblauwepistes","Aantal kilometer blauwe pistes");
	$cms->edit_field(5,0,"aantalrodepistes","Aantal rode pistes");
	$cms->edit_field(5,0,"kmrodepistes","Aantal kilometer rode pistes");
	$cms->edit_field(5,0,"aantalzwartepistes","Aantal zwarte pistes");
	$cms->edit_field(5,0,"kmzwartepistes","Aantal kilometer zwarte pistes");
	$cms->edit_field(5,0,"aantalloipes","Aantal loipes");
	$cms->edit_field(5,0,"kmloipes","Aantal kilometer loipes");
	$cms->edit_field(5,0,"htmlrow","<hr><b>Links</b>");
	$cms->edit_field(5,0,"weerbericht","Weerbericht URL");
	$cms->edit_field(5,0,"webcam","Webcam URL");
	$cms->edit_field(5,0,"kaart","Plan de piste URL");
	$cms->edit_field(5,0,"htmlrow","<hr><b>Skipassen</b>");
	if($vars["cmstaal"]) {
		$cms->edit_field(5,0,"omschrijvingskipasbasis","Omschrijving basis-skipas NL","",array("noedit"=>true));
		$cms->edit_field(5,0,"omschrijvingskipasbasis_".$vars["cmstaal"],"Omschrijving basis-skipas ".strtoupper($vars["cmstaal"]));
		$cms->edit_field(5,0,"omschrijvingskipasuitgebreid","Omschrijving uitgebreide skipas NL","",array("noedit"=>true));
		$cms->edit_field(5,0,"omschrijvingskipasuitgebreid_".$vars["cmstaal"],"Omschrijving uitgebreide skipas ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(5,0,"omschrijvingskipasbasis","Omschrijving basis-skipas");
		$cms->edit_field(5,0,"omschrijvingskipasuitgebreid","Omschrijving uitgebreide skipas");
	}
}
$cms->edit_field(5,0,"htmlrow","<hr><b>Afbeeldingen/landkaart</b>");
$cms->edit_field(5,0,"afbeelding","Afbeelding(en)","",array("autoresize"=>false,"img_minwidth"=>"200","img_minheight"=>"150","img_ratio_width"=>"4","img_ratio_height"=>"3","number_of_uploadbuttons"=>8));
$cms->edit_field(5,0,"foto_onderschrift","Onderschrift bij afbeeldingen");
// $cms->edit_field(5,0,"afbeelding_breed","Brede afbeelding(en)","",array("autoresize"=>true,"img_width"=>"400","img_height"=>"150","img_ratio_width"=>"8","img_ratio_height"=>"3","number_of_uploadbuttons"=>2));
$cms->edit_field(5,0,"pistekaart","Pistekaart skigebied","",array("img_minheight"=>"150","img_maxwidth"=>"4000","img_maxheight"=>"2200","showfiletype"=>true));
$cms->edit_field(5,0,"landkaart","Landkaart","",array("img_minheight"=>"150","img_maxwidth"=>"600","img_maxheight"=>"600","showfiletype"=>true));


$cms->edit_field(5,0,"htmlrow","<hr><b>Video</b>");
$cms->edit_field(5,0,"video_url","URL van Vimeo");
$cms->edit_field(5,0,"video","Toon deze video op de regiopagina");

$cms->edit_field(5,0,"htmlrow","<hr><b>Gekoppelde regio's</b>");
$cms->edit_field(5,0,"htmlrow","De gekoppelde regio's worden gebruikt bij het zoekformulier. Als een zoekopdracht weinig resultaten oplevert, wordt er ook gezocht in de gekoppelde regio's. Eerst wordt klasse 1 doorzocht. Zijn er dan nog onvoldoende resultaten, dan volgt klasse 2, enzovoort.");
for($i=1;$i<=5;$i++) {
	$cms->edit_field(5,0,"htmlrow","<i>Klasse ".$i."</i>");
	for($j=1;$j<=5;$j++) {
#		$cms->edit_field(5,0,"koppeling_".$i."_".$j,"Gekoppelde regio ".$j);
#		$cms->edit_field(5,0,"koppeling_".$i."_".$j,"Gekoppelde regio ".$j,"","",array("onchange"=>"checkskigebiedkoppeling('"."koppeling_".$i."_".$j."')"));
		$cms->edit_field(5,0,"koppeling_".$i."_".$j,"Gekoppelde regio ".$j,"","",array("onchange"=>"skigebied_dubbel('"."koppeling_".$i."_".$j."')"));
	}
}
if($italissima) {
	$cms->edit_field(5,0,"htmlrow","<hr><b>Italissima-specifieke gegevens</b>");
	$cms->edit_field(5,0,"omschrijving_afbreken_na","Omschrijving afbreken na","",array("allow_0"=>true));


	# Kleurcodes verwerken
	while(list($key,$value)=each($vars["themakleurcode"])) {
		$kleurcodehtml.="<span style=\"background-color:".$value.";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span style=\"background-color:".$vars["themakleurcode_licht"][$key].";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;".wt_he($vars["themakleurencombinatie"][$key])."<p>\n";
	}
	$cms->edit_field(5,0,"htmlrow","<i>Kleuren</i><p>".$kleurcodehtml);
	$cms->edit_field(5,1,"kleurcode","Kleurcode");
	$cms->edit_field(5,0,"accommodatiecodes","Accommodatiecodes (gescheiden door komma's)");
	$cms->edit_field(5,0,"googlemaps_zoomlevel","Google Maps Zoomlevel (0 = hele wereld, 15 = heel dichtbij)");

	$cms->edit_field(5,0,"afbeelding_italissima","Hoofdafbeelding","",array("autoresize"=>true,"img_width"=>"250","img_height"=>"188","img_ratio_width"=>"4","img_ratio_height"=>"3"));
	$cms->edit_field(5,0,"topafbeelding_italissima","Topafbeelding (helemaal bovenaan)","",array("autoresize"=>false,"img_width"=>"760","img_height"=>"160"));
}

if($zomerhuisje) {
	$cms->edit_field(5,0,"htmlrow","<hr><b>Zomerhuisje-specifieke gegevens</b>");
	$cms->edit_field(5,0,"bestemmingen_zomerhuisje","Bestemmingenpagina Zomerhuisje","",array("autoresize"=>false,"img_width"=>"170","img_height"=>"140"));
}


# Show show_field($counter,$id,$title="",$options="",$layout=""))
if($_GET["wzt"]==1) {
	$cms->show_name[5]="skigebied-gegevens";
} else {
	$cms->show_name[5]="regio-gegevens";
}
$cms->show_mainfield[5]="naam";
$cms->show_field(5,"naam","Naam");



# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(5);
if($cms_form[5]->filled) {
	# Controle of juiste taal wel actief is
	if(!$vars["cmstaal"]) {
		while(list($key,$value)=each($_POST["input"])) {
			if(ereg("^omschrijving_en",$key)) {
				$cms_form[5]->error("taalprobleem","De CMS-taal is gewijzigd tijdens het bewerken. Opslaan is niet mogelijk. Ga terug naar het CMS-hoofdmenu en kies de gewenste taal",false,true);
			}
		}
	}
	if($cms_form[5]->input["googlemaps_zoomlevel"] and $cms_form[5]->input["googlemaps_zoomlevel"]>15) {
		$cms_form[5]->error("googlemaps_zoomlevel","Kies een waarde van 0 t/m 15");
	}

	# Controle op aanwezige video_url
	if($cms_form[5]->input["video"] and !$cms_form[5]->input["video_url"]) {
		$cms_form[5]->error("video_url","obl");
	}

	# Controle op Vimeo-link
	if($cms_form[5]->input["video_url"] and !preg_match("/^https:\/\/player\.vimeo\.com\/video\/[0-9]+$/",$cms_form[5]->input["video_url"])) {
		$cms_form[5]->error("video_url","onjuist formaat. Voorbeeld: https://player.vimeo.com/video/44377043");
	}

}

# Controle op delete-opdracht
if($_GET["delete"]==5 and $_GET["5k0"]) {
	$db->query("SELECT skigebied_id FROM plaats WHERE skigebied_id='".addslashes($_GET["5k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(5,"Er zijn nog <a href=\"cms_plaatsen.php?wzt=".intval($_GET["wzt"])."&4where=".urlencode("skigebied_id=".$_GET["5k0"])."\">plaatsen</a> gekoppeld");
	}
}


function form_before_goto($form) {
	$db = new DB_sql;

	// set English name if empty
	$db->query("UPDATE skigebied SET naam_en=naam WHERE naam_en='';");
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>