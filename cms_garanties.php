<?php

$mustlogin=true;
$vars["types_in_vars"]=true;
include("admin/vars.php");

if(!$_GET["status"] or $_GET["status"]>5) {
	$_GET["status"]=1;
}
if(!$login->has_priv("9") and !in_array($_GET["status"], array(1,5))) {
	header("Location: cms.php");
	exit;
}

# Gegevens geselecteerde type downloaden
if($_GET["34k0"]) {
	$db->query("SELECT naam, type_id, UNIX_TIMESTAMP(inkoopdatum) AS inkoopdatum, aankomstdatum, aankomstdatum_exact, leverancier_id, reserveringsnummer_extern, boeking_id FROM garantie WHERE garantie_id='".addslashes($_GET["34k0"])."';");
	if($db->next_record()) {
		$acc=accinfo($db->f("type_id"));
		$aankomstdatum=$db->f("aankomstdatum");
		$aankomstdatum_exact=$db->f("aankomstdatum_exact");
		$inkoopdatum=$db->f("inkoopdatum");
		$boekingid=$db->f("boeking_id");
		$leverancierid=$db->f("leverancier_id");
		$naam_garantie=$db->f("naam");
#		$reserveringsnummer_2=get_reserveringsnummer_2($db->f("leverancier_id"),$acc["aankomstdatum_unixtime"][$db->f("aankomstdatum")]);
#		$reserveringsnummer_extern=$db->f("reserveringsnummer_extern");
	}

	$db->query("SELECT leverancier_id, naam, bevestigmethode, opmerkingen_facturen, aanbetaling_dagen, eindbetaling_dagen_factuur FROM leverancier WHERE leverancier_id='".addslashes($leverancierid)."';");
	if($db->next_record()) {
		$temp["leverancier_id"]=$db->f("leverancier_id");
		$temp["leverancier_naam"]=$db->f("naam");
		$temp["leverancier_bevestigmethode"]=$db->f("bevestigmethode");
		$temp["leverancier_opmerkingen_facturen"]=$db->f("opmerkingen_facturen");
		$temp["leverancier_aanbetaling_dagen"]=$db->f("aanbetaling_dagen");
		$temp["leverancier_eindbetaling_dagen_factuur"]=$db->f("eindbetaling_dagen_factuur");
	}
}

# Chalettour-tarieven bepalen (om te tonen in overzicht)
$db->query("SELECT g.garantie_id, t.wederverkoop_verkoopprijs, c_verkoop_site FROM tarief t, garantie g WHERE g.type_id=t.type_id AND g.aankomstdatum=t.week;");
while($db->next_record()) {
	if($db->f("wederverkoop_verkoopprijs")>0) {
		$verkoopprijs[$db->f("garantie_id")]=$db->f("wederverkoop_verkoopprijs");
	} elseif($db->f("c_verkoop_site")>0) {
		$verkoopprijs[$db->f("garantie_id")]=$db->f("c_verkoop_site");
	} else {
		$verkoopprijs[$db->f("garantie_id")]="onbekend";
	}
}

// get `optie_opmerkingen_intern`
if ($_GET["status"]==5) {
	$db->query("SELECT garantie_id, optie_opmerkingen_intern FROM garantie WHERE boeking_id=0 AND aankomstdatum_exact>'".time()."' AND optie_klant=1 AND optie_opmerkingen_intern IS NOT NULL AND optie_opmerkingen_intern<>'';");
	while($db->next_record()) {
		$optie_opmerkingen_intern[$db->f("garantie_id")]="<a href=\"#\" onclick=\"return false;\" class=\"opm\"><span class=\"balloon_small2\">".nl2br(wt_he($db->f( "optie_opmerkingen_intern" )))."</span>opmerking</a>";
	}
}

# Boekingen vullen
$db->query("SELECT b.boeking_id, b.boekingsnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking b, boeking_persoon bp WHERE bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND b.boekingsnummer<>'' AND b.geannuleerd=0 ORDER BY b.boekingsnummer;");
while($db->next_record()) {
	$vars["alleboekingen"][$db->f("boeking_id")]=$db->f("boekingsnummer")." ".wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
}

$cms->db[34]["where"]="1=1";

if($_GET["status"]==1) {
	# Ongebruikte garanties
	if(!$_GET["34k0"] or $_GET["delete"]==34) {
		$cms->db[34]["where"].=" AND boeking_id=0 AND aankomstdatum_exact>'".time()."'";
	}
} elseif($_GET["status"]==2) {
	# Gebruikte garanties
	$cms->db[34]["where"].=" AND boeking_id>0";
} elseif($_GET["status"]==3) {
	# Verlopen garanties
	$cms->db[34]["where"].=" AND boeking_id=0 AND aankomstdatum_exact<='".time()."'";
} elseif($_GET["status"]==5) {
	# Ongebruikte garanties (in optie)
	$cms->db[34]["where"].=" AND boeking_id=0 AND aankomstdatum_exact>'".time()."' AND optie_klant=1";
}

if($_GET["tid"]) {
	$cms->db[34]["where"].=" AND type_id='".addslashes($_GET["tid"])."'";
}

if($_GET["volgnr"]=="leeg") {
	$cms->db[34]["where"].=" AND (reserveringsnummer_extern IS NULL OR reserveringsnummer_extern='')";
}

#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(34,"select","type_id","",array("selection"=>$vars["alletypes_externelink"]));
$cms->db_field(34,"date","aankomstdatum_exact");
$cms->db_field(34,"text","naam");
$cms->db_field(34,"date","inkoopdatum");
if($temp["leverancier_aanbetaling_dagen"]<>"" or $temp["leverancier_eindbetaling_dagen_factuur"]<>"") {
	$cms->db_field(34,"date","inkoopfactuurdatum");
}
$cms->db_field(34,"date","verkoopdatum");
#$cms->db_field(34,"text","reserveringsnummer_intern");
$cms->db_field(34,"text","reserveringsnummer_extern");
$cms->db_field(34,"yesno","confirmed");
$cms->db_field(34,"text","factuurnummer");
$cms->db_field(34,"text","leverancierscode");
$cms->db_field(34,"currency","bruto");
$cms->db_field(34,"currency","mag_voor"); //database field to be created
$cms->db_field(34,"text","opmerkingen_overzicht");  //database field to be created
$cms->db_field(34,"float","korting_percentage");
$cms->db_field(34,"currency","korting_euro");
$cms->db_field(34,"float","inkoopkorting_percentage");
$cms->db_field(34,"currency","inkoopkorting_euro");
$cms->db_field(34,"currency","netto");
$cms->db_field(34,"select","boeking_id","",array("selection"=>$vars["alleboekingen"]));
$cms->db_field(34,"select","leverancier_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));
$cms->db_field(34,"textarea","opmerkingen");
$cms->db_field(34,"select","garantie_id","",array("selection"=>$verkoopprijs));
$cms->db_field(34,"select","soort_garantie","",array("selection"=>$vars["soort_garantie_uitgebreid"]));
$cms->db_field(34,"text","aan_leverancier_doorgegeven_naam");
$cms->db_field(34,"yesno","optie_klant");
$cms->db_field(34,"text","optie_klantnaam");
$cms->db_field(34,"datetime","optie_einddatum");
$cms->db_field(34,"textarea","optie_opmerkingen_intern");
if ($_GET["status"]==5) {
	$cms->db_field(34,"select","optie_opmerkingen_intern_popup","garantie_id",array("selection"=>$optie_opmerkingen_intern));
}


#
# List
#
# Te tonen icons/links bij list
$cms->settings[34]["list"]["currencyfields_hide_euro_sign"]=true;
$cms->settings[34]["list"]["show_icon"]=false;
if($login->has_priv("9")) {
	$cms->settings[34]["list"]["edit_icon"]=true;
	$cms->settings[34]["list"]["delete_icon"]=true;
} else {
	$cms->settings[34]["list"]["edit_icon"]=false;
	$cms->settings[34]["list"]["delete_icon"]=false;
}
if (in_array($_GET["status"], array(1,5))) {
	// status 1 & 5: anybody can edit
	$cms->settings[34]["list"]["edit_icon"] = true;
}
if ($_GET["status"]==5) {
	$cms->settings[34]["list"]["delete_icon"] = false;
}
$cms->settings[34]["list"]["add_link"]=false;

# List list_field($counter,$id,$title="",$options="",$layout="")
if($_GET["status"]==1) {
	$cms->list_sort[34]=array("aankomstdatum_exact","type_id","reserveringsnummer_extern","naam");
} elseif($_GET["status"]==5) {
	$cms->list_sort[34]=array("optie_einddatum","aankomstdatum_exact","type_id","reserveringsnummer_extern");
} else {
	$cms->list_sort[34]=array("type_id","aankomstdatum_exact","reserveringsnummer_extern","naam");
}
$cms->list_field(34,"type_id","Accommodatie","",array("html"=>true, "td_class"=>"td_type_id"));
$cms->list_field(34,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJJJ"));
if($_GET["status"]==1) {
	$cms->list_field(34,"reserveringsnummer_extern","Volgnr");
	$cms->list_field(34,"optie_klant","Optie","",array("td_class"=>"td_optie"));
} elseif($_GET["status"]==2) {
	$cms->list_field(34,"boeking_id","Boeking");
} elseif($_GET["status"]==5) {
	$cms->list_field(34,"optie_klantnaam","Klantnaam");
	$cms->list_field(34,"optie_einddatum","Optie-einddatum",array("date_format"=>"DD-MM-JJJJ UU:ZZ", "sort_empty_below"=>true));
	$cms->list_field(34,"optie_opmerkingen_intern_popup","Intern","",array("html"=>true));

} else {
	$cms->list_field(34,"reserveringsnummer_extern","Volgnr");
}

if($_GET["status"]!=5) {
	$cms->list_field(34,"naam","Naam", "", array("td_class"=>"td_naam"));
}
$cms->list_field(34,"netto","Netto");
if($_GET["status"]<>5) {
	$cms->list_field(34,"bruto","Bruto");
	$cms->list_field(34,"garantie_id","Verkoop",array("force_field_type"=>"currency"));
	$cms->list_field(34,"mag_voor","Mag voor",array("force_field_type"=>"currency"));
	$cms->list_field(34,"opmerkingen_overzicht","Opmerking", "", array("td_class"=>"td_opmerkingen_overzicht"));
}

# Controle op delete-opdracht
if($_GET["delete"]==34 and $_GET["34k0"]) {
	if($_GET["status"]==1 or $_GET["status"]==3) {
		$db->query("SELECT type_id, aankomstdatum FROM garantie WHERE garantie_id='".addslashes($_GET["34k0"])."' AND boeking_id=0;");
		if($db->next_record()) {
			$delete_typeid=$db->f("type_id");
			$delete_aankomstdatum=$db->f("aankomstdatum");
		} else {
			$cms->delete_error(34,"Deze garantie kan niet worden gewist");
			trigger_error("garantie kan niet worden gewist",E_USER_NOTICE);
		}
	}

	# Controleren of de garantie al is betaald aan de leverancier. Zo ja: wissen niet mogelijk
	$db->query("SELECT garantie_id FROM boeking_betaling_lev WHERE garantie_id='".addslashes($_GET["34k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(34,"Wissen van deze garantie is niet mogelijk: er zijn leveranciers-betalingen aan gekoppeld");
	}
}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(34)) {
	if(($_GET["status"]==1 or $_GET["status"]==3) and $delete_typeid and $delete_aankomstdatum) {
		voorraad_bijwerken($delete_typeid,$delete_aankomstdatum,true,-1,0,0,0,0,0,0,false,7);
	}
}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
if($_GET["34k0"]) {
	# URL bepalen
	$db->query("SELECT a.wzt FROM accommodatie a, type t, garantie g WHERE t.accommodatie_id=a.accommodatie_id AND g.type_id=t.type_id AND g.garantie_id='".addslashes($_GET["34k0"])."';");
	if($db->next_record()) {
		if($db->f("wzt")==2) {
			$url=$vars["websites_basehref"]["Z"];
		} else {
			$url=$vars["websites_basehref"]["C"];
		}
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
			$url="http://".$_SERVER["HTTP_HOST"]."/chalet/";
		}
	}

	$cms->edit_field(34,1,"htmlcol","Accommodatie",array("html"=>"<a href=\"".$url."accommodatie/".$acc["begincode"].$acc["type_id"]."/\" target=\"_blank\">".wt_he($vars["alletypes"][$acc["type_id"]])."</a>"));
	$cms->edit_field(34,1,"htmlcol","Aankomstdatum",array("html"=>wt_he(DATUM("DAG D MAAND JJJJ",$aankomstdatum_exact))));
}
$cms->edit_field(34,1,"naam","Naam");
$cms->edit_field(34,1,"leverancier_id","Leverancier");
if($_GET["status"]==1) {
	$cms->edit_field(34,0,"reserveringsnummer_extern","Leveranciers-volgnr&nbsp;<span style=\"font-size:0.8em;\"><a href=\"javascript:auto_garantienr(".$aankomstdatum_exact.");\">automatisch</a></span>","","",array("title_html"=>true));
} else {
	$cms->edit_field(34,0,"reserveringsnummer_extern","Leveranciers-volgnr","",array("noedit"=>true),array("title_html"=>true));
}
$cms->edit_field(34,1,"soort_garantie","Soort garantie");




#$cms->edit_field(34,0,"reserveringsnummer_intern","Reserveringsnummer intern");

$cms->edit_field(34,0,"htmlrow","<hr><b>Inkoopgegevens</b>");
$cms->edit_field(34,0,"bruto","Bruto-accommodatie €","","",array("input_class"=>"wtform_input garantie_inkoopgegevens"));
$cms->edit_field(34,0,"korting_percentage","Commissie %","","",array("input_class"=>"wtform_input garantie_inkoopgegevens"));
$cms->edit_field(34,0,"htmlcol","Inkoop -/- commissie €",array("html"=>""),"",array("tr_class"=>"inkoopgegevens_onopvallend","td_cell_right_class"=>"wtform_cell_right uitkomst_garantie_inkoopmincommissie"));
$cms->edit_field(34,0,"korting_euro","Korting/Toeslag €","",array("negative"=>true),array("input_class"=>"wtform_input garantie_inkoopgegevens"));
$cms->edit_field(34,0,"inkoopkorting_percentage","Inkoopkorting accommodatie %","","",array("input_class"=>"wtform_input garantie_inkoopgegevens"));
$cms->edit_field(34,0,"inkoopkorting_euro","Inkoopkorting accommodatie €","","",array("input_class"=>"wtform_input garantie_inkoopgegevens"));
$cms->edit_field(34,0,"netto","Netto-accommodatie €","","",array("tr_style"=>"display:none;"));
$cms->edit_field(34,0,"htmlcol","Netto-accommodatie €",array("html"=>""),"",array("tr_class"=>"inkoopgegevens_opvallend","td_cell_right_class"=>"wtform_cell_right uitkomst_garantie_inkoopnetto"));
$cms->edit_field(34,0,"htmlrow","<hr>");
$cms->edit_field(34,0,"inkoopdatum","Inkoopdatum","",array("startyear"=>2009,"endyear"=>date("Y")+2),array("calendar"=>true));
if($temp["leverancier_aanbetaling_dagen"]<>"" or $temp["leverancier_eindbetaling_dagen_factuur"]<>"") {
	$cms->edit_field(34,0,"inkoopfactuurdatum","Factuurdatum","",array("startyear"=>2009,"endyear"=>date("Y")+2),array("calendar"=>true));
}

if($temp["leverancier_bevestigmethode"]) {
	$cms->edit_field(34,0,"htmlcol","Bevestigmethode",array("html"=>$temp["leverancier_naam"]." ".wt_he($vars["bevestigmethode"][$temp["leverancier_bevestigmethode"]])),"",array("tr_style"=>"color:grey;font-style:italic;","title_html"=>true));
} else {
	$cms->edit_field(34,0,"htmlcol","Bevestigmethode",array("html"=>"<span style=\"background-color:yellow;\">Er is bij <a href=\"".$vars["path"]."cms_leveranciers.php?edit=8&beheerder=0&8k0=".$temp["leverancier_id"]."\" target=\"_blank\">".wt_he($temp["leverancier_naam"])."</a> nog geen bevestigmethode aangegeven.</span>"),"",array("tr_style"=>"color:grey;","title_html"=>true));
}

if($temp["leverancier_bevestigmethode"]==1) {
	# bevestigmethode: stuurt direct een factuurnummer

} elseif($temp["leverancier_bevestigmethode"]==2) {
	# bevestigmethode: bevestigt zonder reserveringsnummer
	// $form->field_text(0,"leverancierscode","Bevestiging leverancier (zonder reserv.nr)",array("field"=>"leverancierscode"),"","",array("input_class"=>"wtform_input garantie_leverancierscode_keydown"));
	$cms->edit_field(34,0,"leverancierscode","Bevestiging leverancier (zonder reserv.nr)", "", "", array("input_class"=>"wtform_input garantie_leverancierscode_keydown","add_html_after_field"=>"<br><span style=\"font-size:0.8em;\">Moet reserveringsnummer zijn, dus g&eacute;&eacute;n OK of datum!</span>"));
} elseif($temp["leverancier_bevestigmethode"]==3) {
	# bevestigmethode: bevestigt met reserveringsnummer
	// $form->field_text(0,"leverancierscode","Reserveringsnummer leverancier",array("field"=>"leverancierscode"),"","",array("input_class"=>"wtform_input garantie_leverancierscode_keydown","add_html_after_field"=>"<br><span style=\"font-size:0.8em;\">Moet reserveringsnummer zijn, dus g&eacute;&eacute;n OK of datum!</span>"));
	$cms->edit_field(34,0,"leverancierscode","Reserveringsnummer leverancier", "", "", array("input_class"=>"wtform_input garantie_leverancierscode_keydown","add_html_after_field"=>"<br><span style=\"font-size:0.8em;\">Moet reserveringsnummer zijn, dus g&eacute;&eacute;n OK of datum!</span>"));
} else {
	$cms->edit_field(34,0,"leverancierscode","Reserveringsnummer leverancier");
}
// $cms->edit_field(34,0,"htmlrow","<i>Vul confirmed &oacute;f factuurnummer in (niet allebei)</i>");
// $cms->edit_field(34,1,"confirmed","Confirmed","","",array("onclick"=>"if(this.checked==true&&document.frm.elements['input[inkoopdatum][day]'].value=='') setdate('inkoopdatum','');"));
$cms->edit_field(34,0,"factuurnummer","Factuurnummer", "", "", array("input_class"=>"wtform_input garantie_leverancierscode_keydown"));
$cms->edit_field(34,0,"verkoopdatum","Verkoopdatum","",array("startyear"=>2009,"endyear"=>date("Y")+2),array("calendar"=>true));
$cms->edit_field(34,0,"boeking_id","Gekoppelde boeking");
#$cms->edit_field(34,0,"opmerkingen","Opmerkingen");
$cms->edit_field(34,0,"opmerkingen","Opmerkingen (intern)","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));

if($_GET["status"]==1) {
	$cms->edit_field(34,0,"htmlrow","<hr><b>T.b.v. roominglist</b>");
	$cms->edit_field(34,0,"aan_leverancier_doorgegeven_naam","Aan leverancier doorgegeven naam");
}

$cms->edit_field(34,0,"htmlrow","<hr><b>Commerciële/lastminute-informatie </b>");
$cms->edit_field(34,0,"mag_voor","Mag voor €","","",array("input_class"=>"wtform_input garantie_inkoopgegevens"));
$cms->edit_field(34,0,"opmerkingen_overzicht","Opmerking overzicht","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
$cms->edit_field(34,0,"htmlrow","<hr><b>In optie voor klant</b>");
$cms->edit_field(34,0,"optie_klant","Deze garantie staat in optie voor een klant");
$cms->edit_field(34,0,"optie_klantnaam","Klantnaam");
$cms->edit_field(34,0,"optie_einddatum","Einddatum", "", array("min_jump"=>5), array("add_html_after_field"=>"&nbsp;&nbsp;<a href=\"#\" onclick=\"$(&quot;select[name^='input[optie_einddatum]'&quot;).val(&quot;&quot;);return false;\" style=\"font-size:1.3em;text-decoration:none;\">&times;</a>","calendar"=>true));
$cms->edit_field(34,0,"optie_opmerkingen_intern","Interne opmerkingen bij de optie", "", array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(34);
if($cms_form[34]->filled) {
	if($cms_form[34]->input["reserveringsnummer_extern"]) {
		# Controle op indeling
		if(!ereg("^[0-9]{6,7}$",$cms_form[34]->input["reserveringsnummer_extern"])) {
			$cms_form[34]->error("","Leveranciers-volgnr: onjuiste indeling",false,true);
		}
		# Controle of nummer al bestaat bij tabel garantie
		$db->query("SELECT garantie_id FROM garantie WHERE reserveringsnummer_extern='".addslashes($cms_form[34]->input["reserveringsnummer_extern"])."' AND garantie_id<>'".addslashes($_GET["34k0"])."';");
		if($db->num_rows()) {
			$cms_form[34]->error("","Leveranciers-volgnr: nummer bestaat al bij andere garantie",false,true);
		}
		# Controle of nummer al bestaat bij tabel boeking
		$db->query("SELECT boeking_id FROM boeking WHERE SUBSTRING(boekingsnummer,11,6)='".addslashes($cms_form[34]->input["reserveringsnummer_extern"])."'".($boekingid ? " AND boeking_id<>'".$boekingid."'" : "").";");
		if($db->num_rows()) {
			$cms_form[34]->error("","Leveranciers-volgnr: nummer bestaat al bij een boeking",false,true);
		}
	}
}

function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login, $vars, $boekingid, $naam_garantie;

	$db->query("SELECT type_id, aankomstdatum FROM garantie WHERE garantie_id='".addslashes($_GET["34k0"])."';");
	if($db->next_record()) {
		$typeid=$db->f("type_id");
		$aankomstdatum=$db->f("aankomstdatum");
	}

	if(!$boekingid and $form->input["boeking_id"]) {
		# garantie: -1
		voorraad_bijwerken($typeid,$aankomstdatum,true,-1,0,0,0,0,0,0,false,9);

		# inkoopbetalingen koppelen aan boeking
		$db2->query("UPDATE boeking_betaling_lev SET boeking_id='".addslashes($form->input["boeking_id"])."' WHERE garantie_id='".addslashes($_GET["34k0"])."';");

	}
	if($boekingid and !$form->input["boeking_id"]) {
		# garantie: +1
		voorraad_bijwerken($typeid,$aankomstdatum,true,1,0,0,0,0,0,0,false,9);

		# inkoopbetalingen loskoppelen van boeking
		$db2->query("UPDATE boeking_betaling_lev SET boeking_id='0' WHERE garantie_id='".intval($_GET["34k0"])."';");
	}

	# eerste invoer garantie-naam opslaan in aan_leverancier_doorgegeven_naam
	$db2->query("UPDATE garantie SET aan_leverancier_doorgegeven_naam=naam WHERE garantie_id='".intval($_GET["34k0"])."' AND aan_leverancier_doorgegeven_naam='';");
}


# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);
