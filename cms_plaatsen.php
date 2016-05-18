<?php

$mustlogin=true;

include("admin/vars.php");

if(!$_GET["wzt"]) {
	if($_GET["4k0"]) {
		$db->query("SELECT wzt FROM plaats WHERE plaats_id='".addslashes($_GET["4k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}

if($_GET["4k0"]) {
	# Seizoenen laden t.b.v. vertrekinfo_seizoengoedgekeurd
	$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE type='".addslashes($_GET["wzt"])."' AND UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY type, begin, eind;");
	while($db->next_record()) {
		$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
		$laatste_seizoen=$db->f("seizoen_id");
	}

	# toon_op_homepage active on ski-area/region?
	$db->query("SELECT p.plaats_id FROM skigebied s INNER JOIN plaats p USING(skigebied_id) WHERE p.plaats_id='".intval($_GET["4k0"])."' AND s.toon_op_homepage=1;");
	if($db->next_record()) {
		$skigebied_toon_op_homepage = true;
	}
	# Vertrekinfo-tracking
	$vertrekinfo_tracking_array=array("vertrekinfo_plaatsroute");
	if($vars["cmstaal"]) {
		$vertrekinfo_tracking_array[]="vertrekinfo_plaatsroute_".$vars["cmstaal"];
	}
	$vertrekinfo_tracking=vertrekinfo_tracking("plaats",$vertrekinfo_tracking_array,$_GET["4k0"],$laatste_seizoen);
}
if($_GET["add"]<>4 and $_GET["edit"]<>4) {
	// determine number of toon_op_homepage-villages
	$db->query("SELECT p.plaats_id FROM plaats p INNER JOIN skigebied s USING(skigebied_id) WHERE s.toon_op_homepage=1 AND p.toon_op_homepage=1;");
	while($db->next_record()) {
		$toon_op_homepage[$db->f("plaats_id")] = "ja";
	}
}


# Toegevoegde accommodaties opslaan
if($_POST["leverancierscode_filled"]) {
	$db->query("DELETE FROM plaats_optieleverancier WHERE plaats_id='".addslashes($_GET["4k0"])."';");
	while(list($key,$value)=@each($_POST["lev"])) {
		$db->query("INSERT INTO plaats_optieleverancier SET leverancierscode='".addslashes($value)."', vertrekinfo_optiegroep_variabele='".addslashes($_POST["vertrekinfo_var"][$key])."', optieleverancier_id='".addslashes($key)."', plaats_id='".addslashes($_GET["4k0"])."';");
	}
	$_SESSION["wt_popupmsg"]="gegevens zijn opgeslagen";
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

# Aantal accommodaties en capaciteit per plaats bepalen
$db->query("SELECT plaats_id, count(type_id) aantal, MIN(optimaalaantalpersonen) AS minpersonen, MAX(maxaantalpersonen) AS maxpersonen FROM view_accommodatie WHERE archief=0 AND atonen=1 AND ttonen=1 GROUP BY plaats_id;");
while($db->next_record()) {
	$aantalacc[$db->f("plaats_id")]=$db->f("aantal");
	$aantalpers[$db->f("plaats_id")]=$db->f("minpersonen")."-".$db->f("maxpersonen")." p.";
}

if(!$_GET["edit"] and !$_GET["add"]) {
	# Link naar plaatsen bepalen
	$db->query("SELECT plaats_id, naam FROM plaats WHERE wzt='".addslashes($_GET["wzt"])."';");
	while($db->next_record()) {
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$url="/chalet/";
		} elseif($_GET["wzt"]==2) {
			$url="https://www.zomerhuisje.nl/";
		} else {
			$url="https://www.chalet.nl/";
		}
		$url=$url."plaats/".wt_convert2url_seo($db->f("naam"))."/";
		$plaatslink[$db->f("plaats_id")]="<a href=\"".wt_he($url)."\" target=\"_blank\">".wt_he($url)."</a>";
	}
}

if(!$plaatslink) $plaatslink=array("1"=>"leeg");

$cms->settings[4]["list"]["show_icon"]=true;
$cms->settings[4]["list"]["edit_icon"]=true;
$cms->settings[4]["list"]["delete_icon"]=true;

$cms->db[4]["where"]="wzt='".addslashes($_GET["wzt"])."'";
$cms->db[4]["set"]="wzt='".addslashes($_GET["wzt"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(4,"text","naam");
if($vars["cmstaal"]) $cms->db_field(4,"text","naam_".$vars["cmstaal"]);

$cms->db_field(4,"text","seonaam");
if($vars["cmstaal"]) $cms->db_field(4,"text","seonaam_".$vars["cmstaal"]);


# inactieve sites uitzetten
while(list($key,$value)=each($vars["websites_inactief"])) {
	unset($vars["websites_wzt"][$_GET["wzt"]][$key]);
}
$cms->db_field(4,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(4,"select","toon_op_homepage_list","plaats_id",array("selection"=>$toon_op_homepage));
$cms->db_field(4,"text","altnaam");
$cms->db_field(4,"text","altnaam_zichtbaar");
$cms->db_field(4,"select","skigebied_id","",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
$cms->db_field(4,"multiradio","kenmerken","",array("selection"=>$vars["kenmerken_plaats_".$_GET["wzt"]],"multiselection"=>array(1=>"ja",2=>"nee",3=>"onbekend",4=>"niet relevant"),"multiselectionfields"=>array(1=>"kenmerken",2=>"kenmerken_nee",3=>"kenmerken_onbekend",4=>"kenmerken_irrelevant")));
$cms->db_field(4,"yesno","toon_op_homepage");
$cms->db_field(4,"select","aantalacc","plaats_id",array("selection"=>$aantalacc));
$cms->db_field(4,"select","aantalpers","plaats_id",array("selection"=>$aantalpers));
$cms->db_field(4,"select","plaatslink","plaats_id",array("selection"=>$plaatslink));
$cms->db_field(4,"textarea","omschrijving");
$cms->db_field(4,"text","korteomschrijving");
$korteomschrijving_info = array("info"=>"Vul een korte/krachtige omschrijving van de plaats in 1 zin voor op de plaatspagina (voor bezoekers en zoekmachines).");
if($vars["cmstaal"]) $cms->db_field(4,"text","korteomschrijving_".$vars["cmstaal"]);
if($vars["cmstaal"]) $cms->db_field(4,"textarea","omschrijving_".$vars["cmstaal"]);
$cms->db_field(4,"text","descriptiontag");
if($vars["cmstaal"]) $cms->db_field(4,"text","descriptiontag_".$vars["cmstaal"]);
$cms->db_field(4,"textarea","wandelen");
if($vars["cmstaal"]) $cms->db_field(4,"textarea","wandelen_".$vars["cmstaal"]);
$cms->db_field(4,"textarea","fietsen");
if($vars["cmstaal"]) $cms->db_field(4,"textarea","fietsen_".$vars["cmstaal"]);
$cms->db_field(4,"textarea","sportief");
if($vars["cmstaal"]) $cms->db_field(4,"textarea","sportief_".$vars["cmstaal"]);
$cms->db_field(4,"textarea","bezienswaardigheden");
if($vars["cmstaal"]) $cms->db_field(4,"textarea","bezienswaardigheden_".$vars["cmstaal"]);
$cms->db_field(4,"textarea","cultuur");
if($vars["cmstaal"]) $cms->db_field(4,"textarea","cultuur_".$vars["cmstaal"]);
$cms->db_field(4,"textarea","xmlnaam");
$cms->db_field(4,"select","land_id","",array("othertable"=>"6","otherkeyfield"=>"land_id","otherfield"=>"naam"));
$cms->db_field(4,"select","hoortbij_plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>($_GET["4k0"] ? "plaats_id<>'".addslashes($_GET["4k0"])."' AND " : "")."wzt='".addslashes($_GET["wzt"])."'"));
$cms->db_field(4,"integer","hoogte");
$cms->db_field(4,"text","luchthaven");
$cms->db_field(4,"integer","afstandtotluchthaven");
$cms->db_field(4,"integer","afstandtotutrecht");
$cms->db_field(4,"integer","reistijd");
$cms->db_field(4,"text","treinstation");
$cms->db_field(4,"integer","afstandtottreinstation");
$cms->db_field(4,"textarea","vervoeromschrijving");
if($vars["cmstaal"]) $cms->db_field(4,"textarea","vervoeromschrijving_".$vars["cmstaal"]);
$cms->db_field(4,"text","bekendestad");
if($vars["cmstaal"]) $cms->db_field(4,"text","bekendestad_".$vars["cmstaal"]);
$cms->db_field(4,"integer","afstandbekendestad");
$cms->db_field(4,"text","afstandbekendestadextra");
if($vars["cmstaal"]) $cms->db_field(4,"text","afstandbekendestadextra_".$vars["cmstaal"]);
$cms->db_field(4,"url","toeristenbureau");
$cms->db_field(4,"url","weerbericht");
$cms->db_field(4,"url","webcam");
$cms->db_field(4,"url","kaart");
$cms->db_field(4,"url","plattegrond");
$cms->db_field(4,"text","gps_lat");
$cms->db_field(4,"text","gps_long");
$cms->db_field(4,"text","foto_onderschrift");
$cms->db_field(4,"picture","afbeelding","",array("savelocation"=>"pic/cms/plaatsen/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(4,"picture","afbeelding_breed","",array("savelocation"=>"pic/cms/plaatsen_breed/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(4,"picture","landkaart","",array("savelocation"=>"pic/cms/plaatsen_landkaarten/","filetype"=>"gif"));
$cms->db_field(4,"upload","pdfplattegrond","",array("savelocation"=>"pdf/plaats_plattegrond/","filetype"=>"pdf"));
$cms->db_field(4,"yesno","pdfplattegrond_nietnodig");

# Nieuw vertrekinfo-systeem
$cms->db_field(4,"checkbox","vertrekinfo_goedgekeurd_seizoen","",array("selection"=>$vars["seizoengoedgekeurd"]));
if($vars["cmstaal"]) $cms->db_field(4,"checkbox","vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(4,"text","vertrekinfo_goedgekeurd_datetime");
if($vars["cmstaal"]) $cms->db_field(4,"text","vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"]);
$cms->db_field(4,"textarea","vertrekinfo_plaatsroute");
if($vars["cmstaal"]) $cms->db_field(4,"textarea","vertrekinfo_plaatsroute_".$vars["cmstaal"]);

# Video
$cms->db_field(4,"yesno","video");
$cms->db_field(4,"url","video_url");



# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(4,"naam","Naam");
if($_GET["wzt"]==2) {
	$cms->list_field(4,"skigebied_id","Regio");
} else {
	$cms->list_field(4,"skigebied_id","Skigebied");
}
$cms->list_field(4,"land_id","Land");
$cms->list_field(4,"aantalacc","Aantal acc");
$cms->list_field(4,"aantalpers","Capaciteit");
$cms->list_field(4,"toon_op_homepage_list","Op homepage");
$cms->list_field(4,"plaatslink","Link","",array("html"=>true));
$cms->list_field(4,"websites","Sites");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(4,0,"websites","Toon in totaaloverzicht op",array("selection"=>($_GET["wzt"]==1 ? "B,C,T,W" : "N,O,Z")),"",array("one_per_line"=>true));
if($skigebied_toon_op_homepage) {
	if($_GET["wzt"]==1) {
		$cms->edit_field(4,0,"toon_op_homepage","Toon deze plaats op de homepage bij het betreffende skigebied (nog niet in gebruik)");
	} else {
		$cms->edit_field(4,0,"toon_op_homepage","Toon deze plaats op de homepage bij de betreffende regio (nog niet in gebruik)");
	}
}
if($vars["cmstaal"]) {
	$cms->edit_field(4,1,"naam", "Naam NL","",array("noedit"=>true));
	$cms->edit_field(4,1,"naam_".$vars["cmstaal"], "Naam ".strtoupper($vars["cmstaal"]), "", [], ['data_field' => ['role' => 'generate-seoname', 'seo-field' => 'input[name="input[seonaam_' . $vars['cmstaal'] . ']"]']]);
} else {
	$cms->edit_field(4,1,"naam", "", [], ['data_field' => ['role' => 'generate-seo', 'seo-field' => 'input[name="input[seonaam]"]']]);
}

$seoinfo = 'Deze naam zal gebruikt worden als naam van deze plaats in de url balk. Bjorn is verantwoordelijk voor dit veld.';

if($vars["cmstaal"]) {
	$cms->edit_field(4,0,"seonaam","Seonaam NL","",array("noedit"=>true));
	$cms->edit_field(4,1,"seonaam_".$vars["cmstaal"],"Seonaam ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(4,1,"seonaam");
}


$cms->edit_field(4,0,"altnaam","Zoekwoorden (zoekformulier)");
$cms->edit_field(4,0,"altnaam_zichtbaar","Alternatieve spelling (zoekformulier)");
$cms->edit_field(4,0,"htmlrow","<hr><b>Google</b><br><br><i>Gebruik geen sitenamen in Google-teksten (dit wordt automatisch door het systeem gedaan).</i>");
if($vars["cmstaal"]) {
	$cms->edit_field(4,0,"descriptiontag","Description NL","",array("noedit"=>true));
	$cms->edit_field(4,0,"descriptiontag_".$vars["cmstaal"],"Description ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(4,0,"descriptiontag","Description (ongeveer 100 - 159 karakters)");
}
$cms->edit_field(4,0,"htmlrow","<hr>");
$cms->edit_field(4,1,"land_id","Land");
$cms->edit_field(4,1,"skigebied_id",($_GET["wzt"]==1 ? "Skigebied" : "Regio"));
$cms->edit_field(4,0,"hoortbij_plaats_id","Hoort bij");
$cms->edit_field(4,0,"gps_lat","GPS latitude","","",array("info"=>"Vul de breedtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 52.086508"));
$cms->edit_field(4,0,"gps_long","GPS longitude","","",array("info"=>"Vul de lengtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 4.886513"));
if($_GET["wzt"]==1) {
	$cms->edit_field(4,1,"hoogte","Hoogte (in meters)");
}
$cms->edit_field(4,1,"afstandtotutrecht","Afstand tot Utrecht (in k.m.)");
#$cms->edit_field(4,1,"reistijd","Reistijd vanaf Utrecht (in hele uren)");
$cms->edit_field(4,0,"htmlrow","<hr><b>XML-namen</b><br>Geef de namen die in de XML-export gebruikt worden voor deze plaats (gescheiden door komma's)");
$cms->edit_field(4,0,"xmlnaam","XML-namen");
if($_GET["wzt"]==2) $cms->edit_field(4,0,"htmlrow","<hr><b>Omschrijvingen</b>");

if($_GET["wzt"]==1) {

	if($vars["cmstaal"]) {

		$cms->edit_field(4,0,"korteomschrijving","Korte omschrijving NL", "", "",$korteomschrijving_info);
		$cms->edit_field(4,0,"korteomschrijving_".$vars["cmstaal"],"Korte omschrijving ".strtoupper($vars["cmstaal"]),"", "",$korteomschrijving_info);

		$cms->edit_field(4,0,"omschrijving","Omschrijving NL");
		$cms->edit_field(4,1,"omschrijving_".$vars["cmstaal"],"Omschrijving ".strtoupper($vars["cmstaal"]));

	} else {

		$cms->edit_field(4,0,"korteomschrijving","Korte omschrijving", "", "",$korteomschrijving_info);
		$cms->edit_field(4,1,"omschrijving","Omschrijving","","",array("rows"=>20,"info"=>$vars["wysiwyg_info"]));
	}

} else {

	if($vars["cmstaal"]) {

		$cms->edit_field(4,0,"korteomschrijving","Korte omschrijving NL", "", "",$korteomschrijving_info);
		$cms->edit_field(4,1,"korteomschrijving_".$vars["cmstaal"],"Korte omschrijving ".strtoupper($vars["cmstaal"]));

		$cms->edit_field(4,0,"omschrijving","Algemene omschrijving NL","",array("noedit"=>true));
		$cms->edit_field(4,1,"omschrijving_".$vars["cmstaal"],"Algemene omschrijving ".strtoupper($vars["cmstaal"]));

	} else {

		$cms->edit_field(4,0,"korteomschrijving","Korte omschrijving","","",$korteomschrijving_info);
		$cms->edit_field(4,1,"omschrijving","Algemene omschrijving","","",array("rows"=>20,"info"=>$vars["wysiwyg_info"]));
	}

	if($vars["cmstaal"]) {
		$cms->edit_field(4,0,"wandelen","Wandelen NL","",array("noedit"=>true));
		$cms->edit_field(4,0,"wandelen_".$vars["cmstaal"],"Wandelen ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(4,0,"wandelen","Wandelen");
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(4,0,"fietsen","Fietsen NL","",array("noedit"=>true));
		$cms->edit_field(4,0,"fietsen_".$vars["cmstaal"],"Fietsen ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(4,0,"fietsen","Fietsen");
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(4,0,"sportief","Sportief NL","",array("noedit"=>true));
		$cms->edit_field(4,0,"sportief_".$vars["cmstaal"],"Sportief ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(4,0,"sportief","Sportief");
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(4,0,"bezienswaardigheden","Bezienswaardigheden NL","",array("noedit"=>true));
		$cms->edit_field(4,0,"bezienswaardigheden_".$vars["cmstaal"],"Bezienswaardigheden ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(4,0,"bezienswaardigheden","Bezienswaardigheden");
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(4,0,"cultuur","Cultuur/historisch NL","",array("noedit"=>true));
		$cms->edit_field(4,0,"cultuur_".$vars["cmstaal"],"Cultuur/historisch ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(4,0,"cultuur","Cultuur/historisch");
	}
}
$cms->edit_field(4,0,"kenmerken","Kenmerken");
$cms->edit_field(4,0,"htmlrow","<hr><b>Vervoer</b>");
$cms->edit_field(4,0,"luchthaven","Dichtsbijzijnde luchthaven");
$cms->edit_field(4,0,"afstandtotluchthaven","Afstand tot luchthaven (in k.m.)");
if($_GET["wzt"]==1) {
	$cms->edit_field(4,0,"treinstation","Dichtsbijzijnde treinstation");
	$cms->edit_field(4,0,"afstandtottreinstation","Afstand tot treinstation (in k.m.)");
}
if($vars["cmstaal"]) {
	$cms->edit_field(4,0,"vervoeromschrijving","Aanvulling vervoer NL","",array("noedit"=>true));
	$cms->edit_field(4,0,"vervoeromschrijving_".$vars["cmstaal"],"Aanvulling vervoer ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(4,0,"vervoeromschrijving","Aanvulling vervoer");
}

// Bekende stad + afstand (known city + distance)
$cms->edit_field(4,0,"htmlrow","<hr><b>Bekende stad</b>");
if($vars["cmstaal"]) {
	$cms->edit_field(4,0,"bekendestad","Dichtsbijzijnde bekende stad NL","",array("noedit"=>true));
	$cms->edit_field(4,0,"bekendestad_".$vars["cmstaal"],"Dichtsbijzijnde bekende stad ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(4,0,"bekendestad","Dichtsbijzijnde bekende stad");
}
$cms->edit_field(4,0,"afstandbekendestad","Afstand tot die bekende stad (in k.m.)");
if($vars["cmstaal"]) {
	$cms->edit_field(4,0,"afstandbekendestadextra","Toevoeging afstand tot die bekende stad NL","",array("noedit"=>true));
	$cms->edit_field(4,0,"afstandbekendestadextra_".$vars["cmstaal"],"Toevoeging afstand tot die bekende stad ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(4,0,"afstandbekendestadextra","Toevoeging afstand tot die bekende stad");
}

$cms->edit_field(4,0,"htmlrow","<hr><b>Links</b>");
$cms->edit_field(4,0,"toeristenbureau","Toeristenbureau URL");
$cms->edit_field(4,0,"weerbericht","Weerbericht URL");
if($_GET["wzt"]==1) {
	$cms->edit_field(4,0,"webcam","Webcam URL");
	$cms->edit_field(4,0,"kaart","Plan de piste URL");
}
$cms->edit_field(4,0,"plattegrond","Plattegrond URL");
$cms->edit_field(4,0,"htmlrow","<hr><b>Afbeeldingen/landkaart/plattegrond</b>");
$cms->edit_field(4,0,"afbeelding","Afbeelding(en)","",array("autoresize"=>false,"img_minwidth"=>"200","img_minheight"=>"150","img_ratio_width"=>"4","img_ratio_height"=>"3","number_of_uploadbuttons"=>8));
$cms->edit_field(4,0,"foto_onderschrift","Onderschrift bij afbeeldingen");
#$cms->edit_field(4,0,"afbeelding_breed","Brede afbeelding(en)","",array("img_width"=>"400","img_height"=>"150","number_of_uploadbuttons"=>2));
$cms->edit_field(4,0,"afbeelding_breed","Brede afbeelding(en)","",array("autoresize"=>true,"img_width"=>"400","img_height"=>"150","img_ratio_width"=>"8","img_ratio_height"=>"3","number_of_uploadbuttons"=>2));
$cms->edit_field(4,0,"landkaart","Landkaart","",array("img_minheight"=>"150","img_maxwidth"=>"600","img_maxheight"=>"600","showfiletype"=>true));
$cms->edit_field(4,0,"pdfplattegrond","Plattegrond-PDF","",array("showfiletype"=>true));
$cms->edit_field(4,0,"pdfplattegrond_nietnodig","Plattegrond-PDF is niet nodig bij de reisdocumenten");

$cms->edit_field(4,0,"htmlrow","<hr><b>Video</b>");
$cms->edit_field(4,0,"video_url","URL van Vimeo");
$cms->edit_field(4,0,"video","Toon deze video op de plaatspagina");

$cms->edit_field(4,0,"htmlrow","<a name=\"vertrekinfo\"></a><hr><br><b>Vertrekinfo-systeem</b>");
$cms->edit_field(4,0,"htmlrow","<br><i>Alinea 'Route naar [plaatsnaam]'</i>");
if($vars["cmstaal"]) {
	$cms->edit_field(4,0,"vertrekinfo_plaatsroute","Tekst NL","",array("noedit"=>true));
	$cms->edit_field(4,0,"vertrekinfo_plaatsroute_".$vars["cmstaal"],"Tekst ".strtoupper($vars["cmstaal"]));
	if($vertrekinfo_tracking["vertrekinfo_plaatsroute_".$vars["cmstaal"]]) {
		$cms->edit_field(4,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_plaatsroute_".$vars["cmstaal"]]))."</div>"));
	}
} else {
	$cms->edit_field(4,0,"vertrekinfo_plaatsroute","Tekst","","",array("info"=>"Routebeschrijving naar de betreffende plaats, met alleen het laatste gedeelte van de route (aangezien niet duidelijk is wat het vertrekpunt van de betreffende klant is; dat kan zelfs België zijn)."));
	if($vertrekinfo_tracking["vertrekinfo_plaatsroute"]) {
		$cms->edit_field(4,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_plaatsroute"]))."</div>"));
	}
}
if($vars["cmstaal"]) {
	$cms->edit_field(4,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo ".strtoupper($vars["cmstaal"])."</b>");
	$cms->edit_field(4,0,"vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"Vertrekinfo is goedgekeurd voor seizoen ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
	$cms->edit_field(4,0,"vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"],"Laatste goedkeuring ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
} else {
	$cms->edit_field(4,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo</b>");
	$cms->edit_field(4,0,"vertrekinfo_goedgekeurd_seizoen","Vertrekinfo is goedgekeurd voor seizoen","","",array("one_per_line"=>true));
	$cms->edit_field(4,0,"vertrekinfo_goedgekeurd_datetime","Laatste goedkeuring","","",array("one_per_line"=>true));
}

# Show
$cms->show_name[4]="plaatsgegevens";
$cms->show_mainfield[4]="naam";
$cms->show_field(4,"naam","Naam");
$cms->show_field(4,"pdfplattegrond","Plattegrond");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(4);
if($cms_form[4]->filled) {

	# Controle of juiste taal wel actief is
	if(!$vars["cmstaal"]) {
		while(list($key,$value)=each($_POST["input"])) {
			if(ereg("^omschrijving_",$key)) {
				$cms_form[4]->error("taalprobleem","De CMS-taal is gewijzigd tijdens het bewerken. Opslaan is niet mogelijk. Ga terug naar het CMS-hoofdmenu en kies de gewenste taal",false,true);
			}
		}
	}

	# Controle uniekheid seonaam
	$db->query("SELECT COUNT(1) AS count FROM plaats
				WHERE  seonaam" . $vars['ttv'] . " != '" . $cms_form[4]->input['seonaam' . $vars['ttv']] . "'
				AND    plaats_id <> " . intval($_GET['4k0']));

	if ($count = $db->next_record()) {

		if ($db->f('count') > 0) {
			$cms_form[4]->error('seonaam' . $vars['ttv'], 'Seonaam bestaat al in de database, kies een andere seonaam', false, true);
		}
	}

	# Controle op gps_lat
	if($cms_form[4]->input["gps_lat"]) {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[4]->input["gps_lat"])) {
			if(floatval($cms_form[4]->input["gps_lat"])<33.797408767572485 or floatval($cms_form[4]->input["gps_lat"])>71.01695975726373) {
				$cms_form[4]->error("gps_lat","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[4]->error("gps_lat","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}

	# Controle op gps_long
	if($cms_form[4]->input["gps_long"]) {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[4]->input["gps_long"])) {
			if(floatval($cms_form[4]->input["gps_long"])<-9.393310546875 or floatval($cms_form[4]->input["gps_long"])>27.7734375) {
				$cms_form[4]->error("gps_long","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[4]->error("gps_long","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}

	# Controle op aanwezige video_url
	if($cms_form[4]->input["video"] and !$cms_form[4]->input["video_url"]) {
		$cms_form[4]->error("video_url","obl");
	}

	# Controle op Vimeo-link
	if($cms_form[4]->input["video_url"] and !preg_match("/^https:\/\/player\.vimeo\.com\/video\/[0-9]+$/",$cms_form[4]->input["video_url"])) {
		$cms_form[4]->error("video_url","onjuist formaat. Voorbeeld: https://player.vimeo.com/video/44377043");
	}

	// Check known city (bekende stad)
	if($cms_form[4]->input["bekendestad".$vars["ttv"]] and !$cms_form[4]->input["afstandbekendestad"]) {
		$cms_form[4]->error("afstandbekendestad","obl");
	}
	if(!$cms_form[4]->input["bekendestad".$vars["ttv"]] and $cms_form[4]->input["afstandbekendestad"]) {
		$cms_form[4]->error("bekendestad".$vars["ttv"],"obl");
	}
	if($cms_form[4]->input["afstandbekendestadextra".$vars["ttv"]] and !$cms_form[4]->input["afstandbekendestad"]) {
		$cms_form[4]->error("afstandbekendestad","obl");
	}
}

# Na opslaan form de volgende actie uitvoeren
if($cms_form[4]->okay) {

}

function form_before_goto($form) {
	global $db0,$login;
	if($form->settings["fullname"]=="cms_4") {
		if($form->upload_okay["pdfplattegrond"]) {
			if($form->db_insert_id) {
				$plaatsid=$form->db_insert_id;
			} elseif($_GET["4k0"]) {
				$plaatsid=$_GET["4k0"];
			}
			if($plaatsid) {
				$db0->query("UPDATE plaats SET pdfupload_user='".addslashes($login->user_id)."', pdfupload_datum=NOW() WHERE plaats_id='".addslashes($plaatsid)."';");
			}
		}
	}
}

# Controle op delete-opdracht
if($_GET["delete"]==4 and $_GET["4k0"]) {
	$db->query("SELECT accommodatie_id FROM accommodatie WHERE plaats_id='".addslashes($_GET["4k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(4,"Er zijn nog <a href=\"cms_accommodaties.php?wzt=".intval($_GET["wzt"])."&1where=".urlencode("plaats_id=".$_GET["4k0"])."\">accommodaties</a> gekoppeld");
	}
}


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>