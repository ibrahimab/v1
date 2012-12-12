<?php

$mustlogin=true;

include("admin/vars.php");

if(!$_GET["wzt"]) {
	$_GET["wzt"]=1;
}

# Seizoenen laden t.b.v. vertrekinfo_seizoengoedgekeurd
$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE type='".addslashes($_GET["wzt"])."' AND UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY type, begin, eind;");
while($db->next_record()) {
	$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
	$laatste_seizoen=$db->f("seizoen_id");
}

# Vertrekinfo-tracking
if($_GET["wzt"]==2) {
	$vertrekinfo_tracking=vertrekinfo_tracking("land",array("zomervertrekinfo_landroute"),$_GET["6k0"],$laatste_seizoen);
} else {
	$vertrekinfo_tracking=vertrekinfo_tracking("land",array("vertrekinfo_landroute"),$_GET["6k0"],$laatste_seizoen);
}


$cms->settings[6]["list"]["show_icon"]=false;
$cms->settings[6]["list"]["edit_icon"]=true;
$cms->settings[6]["list"]["delete_icon"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(6,"text","naam");
if($vars["cmstaal"]) $cms->db_field(6,"text","naam_".$vars["cmstaal"]);
$cms->db_field(6,"text","altnaam");
if($_GET["add"]==6) {
	$cms->db_field(6,"text","begincode");
} else {
	$cms->db_field(6,"noedit","begincode");
}
$cms->db_field(6,"yesno","tonen");
$cms->db_field(6,"yesno","zomertonen");
$cms->db_field(6,"text","titel");
if($vars["cmstaal"]) $cms->db_field(6,"text","titel_".$vars["cmstaal"]);
$cms->db_field(6,"text","descriptiontag");
if($vars["cmstaal"]) $cms->db_field(6,"text","descriptiontag_".$vars["cmstaal"]);
$cms->db_field(6,"text","zomerdescriptiontag");
if($vars["cmstaal"]) $cms->db_field(6,"text","zomerdescriptiontag_".$vars["cmstaal"]);
$cms->db_field(6,"textarea","omschrijving");
if($vars["cmstaal"]) $cms->db_field(6,"textarea","omschrijving_".$vars["cmstaal"]);
$cms->db_field(6,"textarea","omschrijving_openklap");
if($vars["cmstaal"]) $cms->db_field(6,"textarea","omschrijving_openklap_".$vars["cmstaal"]);
$cms->db_field(6,"textarea","zomeromschrijving");
if($vars["cmstaal"]) $cms->db_field(6,"textarea","zomeromschrijving_".$vars["cmstaal"]);
$cms->db_field(6,"textarea","zomeromschrijving_openklap");
$cms->db_field(6,"textarea","praktischeinfo");
if($vars["cmstaal"]) $cms->db_field(6,"textarea","praktischeinfo_".$vars["cmstaal"]);
$cms->db_field(6,"select","kleurcode","",array("selection"=>$vars["themakleurencombinatie"]));
$cms->db_field(6,"text","accommodatiecodes");
$cms->db_field(6,"picture","afbeelding","",array("savelocation"=>"pic/cms/landen/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(6,"picture","zomerafbeelding","",array("savelocation"=>"pic/cms/zomerlanden/","filetype"=>"jpg"));
$cms->db_field(6,"picture","zomerafbeelding_top","",array("savelocation"=>"pic/cms/zomerlanden_top/","filetype"=>"jpg","multiple"=>true));
if($_GET["wzt"]==2) {
	# Zomer-vertrekinfo
	$cms->db_field(6,"checkbox","vertrekinfo_goedgekeurd_seizoen","zomervertrekinfo_goedgekeurd_seizoen",array("selection"=>$vars["seizoengoedgekeurd"]));
	$cms->db_field(6,"text","vertrekinfo_goedgekeurd_datetime","zomervertrekinfo_goedgekeurd_datetime");
	$cms->db_field(6,"textarea","vertrekinfo_landroute","zomervertrekinfo_landroute");
} else {
	# Winter-vertrekinfo
	$cms->db_field(6,"checkbox","vertrekinfo_goedgekeurd_seizoen","",array("selection"=>$vars["seizoengoedgekeurd"]));
	$cms->db_field(6,"text","vertrekinfo_goedgekeurd_datetime");
	$cms->db_field(6,"textarea","vertrekinfo_landroute");
}


# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(6,"naam","Naam");
$cms->list_field(6,"begincode","Landcode");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
if($_GET["wzt"]==2) {
	$cms->edit_field(6,0,"zomertonen","Tonen in overzicht \"andere landen\"",array("selection"=>true));
}
if($vars["cmstaal"]) {
	$cms->edit_field(6,0,"naam","Naam NL","",array("noedit"=>true));
	$cms->edit_field(6,1,"naam_".$vars["cmstaal"],"Naam ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(6,1,"naam");
}

$cms->edit_field(6,0,"altnaam","Alternatieve spelling");
$cms->edit_field(6,1,"begincode","Begincode voor accommodaties");
$cms->edit_field(6,0,"htmlrow","<hr><b>Google</b><br><br><i>Gebruik geen sitenamen in Google-teksten (dit wordt automatisch door het systeem gedaan).</i>");
if($_GET["wzt"]==2) $temp_zomer="zomer"; else $temp_zomer="";
if($vars["cmstaal"]) {
	$cms->edit_field(6,0,$temp_zomer."descriptiontag","Description NL","",array("noedit"=>true));
	$cms->edit_field(6,0,$temp_zomer."descriptiontag_".$vars["cmstaal"],"Description ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(6,0,$temp_zomer."descriptiontag","Description (ongeveer 100 - 159 karakters)");
}

$cms->edit_field(6,0,"htmlrow","<hr><b>Landenpagina</b>");
if($_GET["wzt"]==1) {
	if($vars["cmstaal"]) {
		$cms->edit_field(6,0,"titel","Koptekst NL","",array("noedit"=>true));
		$cms->edit_field(6,0,"titel_".$vars["cmstaal"],"Koptekst ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(6,0,"titel");
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(6,0,"omschrijving","Omschrijving NL","",array("noedit"=>true));
		$cms->edit_field(6,0,"omschrijving_".$vars["cmstaal"],"Omschrijving ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(6,0,"omschrijving");
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(6,0,"omschrijving_openklap","Aanvullende omschrijving NL","",array("noedit"=>true));
		$cms->edit_field(6,0,"omschrijving_openklap_".$vars["cmstaal"],"Aanvullende omschrijving ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(6,0,"omschrijving_openklap","Aanvullende omschrijving (zichtbaar na openklappen)");
	}
	if($vars["cmstaal"]) {
#		$cms->edit_field(6,0,"praktischeinfo","Praktische informate NL","",array("noedit"=>true));
#		$cms->edit_field(6,0,"praktischeinfo_".$vars["cmstaal"],"Praktische informate ".strtoupper($vars["cmstaal"]));
	} else {
#		$cms->edit_field(6,0,"praktischeinfo","Praktische info (opsomming, 1 per regel)");
	}
	$cms->edit_field(6,0,"afbeelding","Afbeeldingen (4 stuks)","",array("img_minwidth"=>"200","img_minheight"=>"150","img_maxwidth"=>"600","img_maxheight"=>"450","img_ratio_width"=>"4","img_ratio_height"=>"3","number_of_uploadbuttons"=>4));
#	$cms->edit_field(6,0,"afbeelding","Landkaart","",array("img_minwidth"=>"200","img_minheight"=>"150","img_maxwidth"=>"600","img_maxheight"=>"450","img_ratio_width"=>"4","img_ratio_height"=>"3","number_of_uploadbuttons"=>4));
} else {
	if($vars["cmstaal"]) {
		$cms->edit_field(6,0,"zomeromschrijving","Toelichting NL","",array("noedit"=>true));
		$cms->edit_field(6,0,"zomeromschrijving_".$vars["cmstaal"],"Toelichting ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(6,1,"zomeromschrijving","Toelichting");
	}
	$cms->edit_field(6,0,"zomeromschrijving_openklap","Aanvullende toelichting (zichtbaar na openklappen)");

	# Kleurcodes verwerken
	while(list($key,$value)=each($vars["themakleurcode"])) {
		$kleurcodehtml.="<span style=\"background-color:".$value.";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span style=\"background-color:".$vars["themakleurcode_licht"][$key].";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;".htmlentities($vars["themakleurencombinatie"][$key])."<p>\n";
	}

	$cms->edit_field(6,0,"htmlrow","<hr><b>Te tonen accommodaties</b> (3 accommodatiecodes, gescheiden door komma's)");
	$cms->edit_field(6,1,"accommodatiecodes","Accommodatiecodes");
	$cms->edit_field(6,0,"htmlrow","<hr><b>Kleuren</b><p>".$kleurcodehtml);
	$cms->edit_field(6,1,"kleurcode","Kleurcode");
	$cms->edit_field(6,0,"htmlrow","<hr>");
	$cms->edit_field(6,1,"zomerafbeelding","Afbeelding","",array("img_width"=>"240","img_height"=>"180"));
	$cms->edit_field(6,0,"zomerafbeelding_top","Afbeeldingen bovenaan (4 stuks)","",array("img_width"=>"194","img_height"=>"120","number_of_uploadbuttons"=>4));
}

$cms->edit_field(6,0,"htmlrow","<hr><br><b>Nieuw vertrekinfo-systeem (nog niet in gebruik, maar gegevens invoeren is al mogelijk)</b>");
$cms->edit_field(6,0,"htmlrow","<br><i>Alinea 'Enkele aanwijzingen' (hoort bij routebeschrijving)</i>");
$cms->edit_field(6,0,"vertrekinfo_landroute","Tekst");
if($_GET["wzt"]==2) {
	# Zomer
	if($vertrekinfo_tracking["zomervertrekinfo_landroute"]) {
		$cms->edit_field(6,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["zomervertrekinfo_landroute"]))."</div>"));
	}
} else {
	if($vertrekinfo_tracking["vertrekinfo_landroute"]) {
		$cms->edit_field(6,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_landroute"]))."</div>"));
	}
}
$cms->edit_field(6,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo</b>");
$cms->edit_field(6,0,"vertrekinfo_goedgekeurd_seizoen","Vertrekinfo is goedgekeurd voor seizoen","","",array("one_per_line"=>true));
$cms->edit_field(6,0,"vertrekinfo_goedgekeurd_datetime","Laatste goedkeuring","","",array("one_per_line"=>true));

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(6);
if($cms_form[6]->filled) {
	if($cms_form[6]->input["begincode"]) {
		$db->query("SELECT naam FROM land WHERE begincode='".addslashes($cms_form[6]->input["begincode"])."' AND land_id<>'".addslashes($_GET["6k0"])."';");
		if($db->next_record()) {
			$cms_form[6]->error("begincode",htmlentities($db->f("naam"))." heeft deze code al");
		}
	}
	if(!ereg("^[A-Z]+$",$cms_form[6]->input["begincode"])) {
		$cms_form[6]->error("begincode","alleen hoofdletters toegestaan");
	}
	if(strlen($cms_form[6]->input["begincode"])>2) {
		$cms_form[6]->error("begincode","maximaal 2 letters mogelijk");
	}
	if($_GET["wzt"]==2 and $cms_form[6]->input["accommodatiecodes"]) {
		if(!ereg("^[A-Z]{1,2}[0-9]+,[A-Z]{1,2}[0-9]+,[A-Z]{1,2}[0-9]+$",$cms_form[6]->input["accommodatiecodes"])) {
			$cms_form[6]->error("accommodatiecodes","gebruik deze vorm: F100,F101,F102");
		}
	}
}

# Controle op delete-opdracht
if($_GET["delete"]==6 and $_GET["6k0"]) {
	$db->query("SELECT land_id FROM plaats WHERE land_id='".addslashes($_GET["6k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(6,"Er zijn nog <a href=\"cms_plaatsen.php?4where=".urlencode("land_id=".$_GET["6k0"])."\">plaatsen</a> gekoppeld");
	}
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>