<?php

$mustlogin=true;
include("admin/vars.php");

# wzt opvragen indien niet meegegeven met query_string
if(!$_GET["wzt"]) {
	if($_GET["36k0"]) {
		$db->query("SELECT wzt FROM thema WHERE thema_id='".addslashes($_GET["36k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}

#$vars["positiehoofdpagina"]=array(0=>"niet tonen op de hoofdpagina",1=>"bovenaan",2=>"midden",3=>"onderaan");
#if($_GET["36k0"]) {
#	$db->query("SELECT positiehoofdpagina FROM thema WHERE positiehoofdpagina>0 AND thema_id<>'".addslashes($_GET["36k0"])."';");
#	while($db->next_record()) {
#		$vars["positiehoofdpagina"][$db->f("positiehoofdpagina")].=" (al in gebruik)";
#		$thema_in_gebruik[$db->f("positiehoofdpagina")]=true;
#	}
#}

$cms->settings[36]["list"]["show_icon"]=false;
$cms->settings[36]["list"]["edit_icon"]=true;
$cms->settings[36]["list"]["delete_icon"]=true;

#
# Database-declaratie
#

$cms->db[36]["where"]="wzt='".addslashes($_GET["wzt"])."'";
$cms->db[36]["set"]="wzt='".addslashes($_GET["wzt"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(36,"yesno","actief");
if($_GET["wzt"]==1) {
#	$cms->db_field(36,"select","positiehoofdpagina","",array("selection"=>$vars["positiehoofdpagina"]));
	$cms->db_field(36,"integer","positiehoofdpagina");
} else {
	$cms->db_field(36,"integer","positiehoofdpagina");
	$cms->db_field(36,"select","kleurcode","",array("selection"=>$vars["themakleurencombinatie"]));
}
$cms->db_field(36,"text","naam");
if($vars["cmstaal"]) $cms->db_field(36,"text","naam_".$vars["cmstaal"]);
$cms->db_field(36,"text","titelhoofdpagina");
if($vars["cmstaal"]) $cms->db_field(36,"text","titelhoofdpagina_".$vars["cmstaal"]);
$cms->db_field(36,"text","titletag");
if($vars["cmstaal"]) $cms->db_field(36,"text","titletag_".$vars["cmstaal"]);
$cms->db_field(36,"text","descriptiontag");
if($vars["cmstaal"]) $cms->db_field(36,"text","descriptiontag_".$vars["cmstaal"]);
$cms->db_field(36,"textarea","toelichting");
if($vars["cmstaal"]) $cms->db_field(36,"textarea","toelichting_".$vars["cmstaal"]);
$cms->db_field(36,"textarea","uitgebreid");
if($vars["cmstaal"]) $cms->db_field(36,"textarea","uitgebreid_".$vars["cmstaal"]);
$cms->db_field(36,"text","url");
if($vars["cmstaal"]) $cms->db_field(36,"text","url_".$vars["cmstaal"]);
$cms->db_field(36,"text","externeurl");
if($vars["cmstaal"]) $cms->db_field(36,"text","externeurl_".$vars["cmstaal"]);
$cms->db_field(36,"select","typekenmerk","",array("selection"=>$vars["kenmerken_type_".$_GET["wzt"]]));
$cms->db_field(36,"select","accommodatiekenmerk","",array("selection"=>$vars["kenmerken_accommodatie_".$_GET["wzt"]]));
$cms->db_field(36,"select","plaatskenmerk","",array("selection"=>$vars["kenmerken_plaats_".$_GET["wzt"]]));
$cms->db_field(36,"select","skigebiedkenmerk","",array("selection"=>$vars["kenmerken_skigebied_".$_GET["wzt"]]));
$cms->db_field(36,"text","zoekterm");
$cms->db_field(36,"select","tarievenbekend_seizoen_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"type=".$_GET["wzt"]));
$cms->db_field(36,"text","accommodatiecodes");
$cms->db_field(36,"text","uitgebreidzoeken_url");
$cms->db_field(36,"picture","kleineafbeelding","",array("savelocation"=>"pic/cms/themas_hoofdpagina/","filetype"=>"jpg"));
$cms->db_field(36,"picture","afbeelding","",array("savelocation"=>"pic/cms/themas/","filetype"=>"jpg","multiple"=>true));

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(36,"naam","Naam");
if($_GET["wzt"]==2) {
	$cms->list_sort[36]=array("positiehoofdpagina","naam");
}
if($_GET["wzt"]==1) {
	$cms->list_field(36,"positiehoofdpagina","Positie hoofdpagina");
} else {
	$cms->list_field(36,"positiehoofdpagina","Volgorde");
}
$cms->list_field(36,"actief","Actief");

# Nieuw record meteen openen na toevoegen
#$cms->settings[36]["show"]["goto_new_record"]=true;

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(36,0,"actief","Actief",array("selection"=>true));
if($vars["cmstaal"]) {
	$cms->edit_field(36,0,"naam","Naam NL","",array("noedit"=>true));
	$cms->edit_field(36,0,"naam_".$vars["cmstaal"],"Naam ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(36,1,"naam","Naam");
}
if($vars["cmstaal"]) {
	$cms->edit_field(36,0,"url","Titel in link NL","",array("noedit"=>true));
	$cms->edit_field(36,1,"url_".$vars["cmstaal"],"Titel in link ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(36,1,"url","Titel in link<br>(https://www.chalet.nl/thema/titel-in-link)","","",array("title_html"=>true));
}


if($_GET["wzt"]==1) {
	# winter-thema's
	$cms->edit_field(36,1,"kleineafbeelding","Afbeelding","",array("img_width"=>"170","img_height"=>"128"));
} else {
	# zomer-thema's
	$cms->edit_field(36,1,"kleineafbeelding","Afbeelding","",array("img_width"=>"240","img_height"=>"180"));
}

$cms->edit_field(36,0,"htmlrow","<hr><b>Google</b><br><br><i>Gebruik geen sitenamen in Google-teksten (dit wordt automatisch door het systeem gedaan).</i>");
if($vars["cmstaal"]) {
	$cms->edit_field(36,0,"titletag","Title (t.b.v. Google) NL","",array("noedit"=>true));
	$cms->edit_field(36,1,"titletag_".$vars["cmstaal"],"Title (t.b.v. Google) ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(36,1,"titletag","Title (t.b.v. Google)");
}
if($vars["cmstaal"]) {
	$cms->edit_field(36,0,"descriptiontag","Description NL","",array("noedit"=>true));
	$cms->edit_field(36,0,"descriptiontag_".$vars["cmstaal"],"Description ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(36,0,"descriptiontag","Description (ongeveer 100 - 159 karakters)");
}

if($_GET["wzt"]==1) {
	$cms->edit_field(36,0,"htmlrow","<hr><b>Hoofdpagina</b>");
	if($vars["cmstaal"]) {
		$cms->edit_field(36,0,"titelhoofdpagina","Tekst op de hoofdpagina NL","",array("noedit"=>true));
		$cms->edit_field(36,0,"titelhoofdpagina_".$vars["cmstaal"],"Tekst op de hoofdpagina ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(36,0,"titelhoofdpagina","Tekst op de hoofdpagina");
	}
#	$cms->edit_field(36,1,"positiehoofdpagina","Positie op de hoofdpagina","",array("allow_0"=>true,"no_empty_first_selection"=>true));
	$cms->edit_field(36,0,"positiehoofdpagina","Positie op de hoofdpagina (leeg=niet tonen)");
} else {

}


$cms->edit_field(36,0,"htmlrow","<hr><b>Vul in: toelichting+uitgebreide informatie+afbeeldingen+kenmerken &oacute;f link</b>");
if($vars["cmstaal"]) {
	$cms->edit_field(36,0,"toelichting","Toelichting NL","",array("noedit"=>true));
	$cms->edit_field(36,0,"toelichting_".$vars["cmstaal"],"Toelichting ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(36,0,"toelichting","Toelichting");
}
if($vars["cmstaal"]) {
	$cms->edit_field(36,0,"uitgebreid","Uitgebreide informatie NL","",array("noedit"=>true));
	$cms->edit_field(36,0,"uitgebreid_".$vars["cmstaal"],"Uitgebreide informatie ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(36,0,"uitgebreid","Uitgebreide informatie");
}
$cms->edit_field(36,0,"htmlrow","<hr><b>Gekoppelde kenmerken</b>");
$cms->edit_field(36,0,"typekenmerk","Type-kenmerk");
$cms->edit_field(36,0,"accommodatiekenmerk","Accommodatie-kenmerk");
$cms->edit_field(36,0,"plaatskenmerk","Plaats-kenmerk");
$cms->edit_field(36,0,"skigebiedkenmerk","Regio-kenmerk");
$cms->edit_field(36,0,"zoekterm","Zoekterm (binnen 'Zoekwoorden (intern zoekformulier)')");
#if($_GET["wzt"]==1) {
	$cms->edit_field(36,0,"tarievenbekend_seizoen_id","Tarieven van seizoen al bekend (minstens 4 weken)");
#}
$cms->edit_field(36,0,"htmlrow","<hr><b>Link naar 'uitgebreid zoeken'</b>");
$cms->edit_field(36,0,"uitgebreidzoeken_url","Deel van de URL ('vf_kenm00=00')");

if($_GET["wzt"]==2) {
	$cms->edit_field(36,0,"htmlrow","<hr><b>Volgorde");
	$cms->edit_field(36,0,"positiehoofdpagina","Nummer");
	$cms->edit_field(36,0,"htmlrow","<hr><b>Te tonen accommodaties</b> (3 accommodatiecodes, gescheiden door komma's)");
	$cms->edit_field(36,1,"accommodatiecodes","Accommodatiecodes");

	# Kleurcodes verwerken
	while(list($key,$value)=each($vars["themakleurcode"])) {
		$kleurcodehtml.="<span style=\"background-color:".$value.";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span style=\"background-color:".$vars["themakleurcode_licht"][$key].";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;".htmlentities($vars["themakleurencombinatie"][$key])."<p>\n";
	}

	$cms->edit_field(36,0,"htmlrow","<hr><b>Kleuren</b><p>".$kleurcodehtml);
	$cms->edit_field(36,1,"kleurcode","Kleurcode");
}

$cms->edit_field(36,0,"htmlrow","<hr>");
if($_GET["wzt"]==1) {
	$cms->edit_field(36,0,"afbeelding","Afbeeldingen (4 stuks)","",array("autoresize"=>true,"img_minwidth"=>"200","img_minheight"=>"150","img_maxwidth"=>"600","img_maxheight"=>"450","img_ratio_width"=>"4","img_ratio_height"=>"3","number_of_uploadbuttons"=>4));
} else {
	$cms->edit_field(36,0,"afbeelding","Afbeeldingen bovenaan (4 stuks)","",array("img_width"=>"194","img_height"=>"120","number_of_uploadbuttons"=>4));
}

if($vars["cmstaal"]) {
	$cms->edit_field(36,0,"externeurl","Link NL","",array("noedit"=>true));
	$cms->edit_field(36,0,"externeurl_".$vars["cmstaal"],"Link ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(36,0,"externeurl","Link");
}

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(36);
if($cms_form[36]->filled) {
	if($_GET["wzt"]==1 and $cms_form[36]->input["positiehoofdpagina"]>0) {
	 	if(!$cms_form[36]->input["actief"]) {
			$cms_form[36]->error("positiehoofdpagina","kan alleen als het thema actief is");
		}
		if(!$cms_form[36]->input["titelhoofdpagina"]) {
			$cms_form[36]->error("titelhoofdpagina","obl");
		}
#		if($thema_in_gebruik[$cms_form[36]->input["positiehoofdpagina"]]) {
#			$cms_form[36]->error("positiehoofdpagina","al in gebruik");
#		}
	}
	if($_GET["wzt"]==2 and $cms_form[36]->input["accommodatiecodes"]) {
		if(!ereg("^[A-Z]{1,2}[0-9]+,[A-Z]{1,2}[0-9]+,[A-Z]{1,2}[0-9]+$",$cms_form[36]->input["accommodatiecodes"])) {
			$cms_form[36]->error("accommodatiecodes","gebruik deze vorm: F100,F101,F102");
		}
	}
	if($cms_form[36]->input["uitgebreidzoeken_url"]) {
		if(!preg_match("/^vf_[a-z]+[0-9]+=[0-9]+$/",$cms_form[36]->input["uitgebreidzoeken_url"])) {
			$cms_form[36]->error("uitgebreidzoeken_url","gebruik deze vorm: vf_kenm00=00");
		}
	}

	if($cms_form[36]->input["url"]) {
		if(!ereg("^[a-z0-9-]+$",$cms_form[36]->input["url"])) {
			$cms_form[36]->error("url","gebruik alleen kleine letters, cijfers en '-'");
		} else {
			$db->query("SELECT thema_id FROM thema WHERE wzt='".addslashes($_GET["wzt"])."' AND url='".addslashes($cms_form[36]->input["url"])."'".($_GET["36k0"] ? " AND thema_id<>'".addslashes($_GET["36k0"])."'" : "").";");
			if($db->num_rows()) {
				$cms_form[36]->error("url","al in gebruik");
			}
		}
	}
	if($cms_form[36]->input["url_".$vars["cmstaal"]]) {
		if(!ereg("^[a-z0-9-]+$",$cms_form[36]->input["url_".$vars["cmstaal"]])) {
			$cms_form[36]->error("url_".$vars["cmstaal"],"gebruik alleen kleine letters, cijfers en '-'");
		} else {
			$db->query("SELECT thema_id FROM thema WHERE wzt='".addslashes($_GET["wzt"])."' AND url='".addslashes($cms_form[36]->input["url_".$vars["cmstaal"]])."'".($_GET["36k0"] ? " AND thema_id<>'".addslashes($_GET["36k0"])."'" : "").";");
			if($db->num_rows()) {
				$cms_form[36]->error("url_".$vars["cmstaal"],"al in gebruik");
			}
		}
	}
	if($cms_form[36]->input["toelichting"]) {
		if($cms_form[36]->input["externeurl"]) {
			$cms_form[36]->error("toelichting","kies toelichting &oacute;f link");
		}
		if(!$cms_form[36]->input["url"]) {
			$cms_form[36]->error("url","verplicht bij dit thema");
		}
	}
	if($cms_form[36]->input["zoekterm"] and ereg(" ",trim($cms_form[36]->input["zoekterm"]))) {
		$cms_form[36]->error("zoekterm","maximaal &eacute;&eacute;n woord mogelijk");
	}
}


function form_before_goto($form) {
	global $login,$vars;
	$db=new DB_sql;
	$db2=new DB_sql;

	if($_GET["wzt"]==1) {
		$positiehoofdpagina=0;
		$db->query("SELECT thema_id, positiehoofdpagina FROM thema WHERE wzt=1 ORDER BY positiehoofdpagina;");
		while($db->next_record()) {
			if($db->f("positiehoofdpagina")>0) {
				$positiehoofdpagina=$positiehoofdpagina+10;
				$db2->query("UPDATE thema SET positiehoofdpagina='".$positiehoofdpagina."' WHERE thema_id='".$db->f("thema_id")."';");
			} else {
				$db2->query("UPDATE thema SET positiehoofdpagina=NULL WHERE thema_id='".$db->f("thema_id")."';");
			}
#			echo $db2->lastquery."<br>";
		}
#		exit;
	}
}


# Controle op delete-opdracht
if($_GET["delete"]==36 and $_GET["36k0"]) {

}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>