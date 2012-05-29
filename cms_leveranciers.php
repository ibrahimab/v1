<?php

$mustlogin=true;

include("admin/vars.php");

# "beheerder" opvragen indien niet meegegeven met query_string
if(!$_GET["beheerder"]) {
	if($_GET["8k0"]) {
		$db->query("SELECT beheerder FROM leverancier WHERE leverancier_id='".addslashes($_GET["8k0"])."';");
		if($db->next_record()) {
			$_GET["beheerder"]=$db->f("beheerder");
		}
	} else {
		$_GET["beheerder"]=0;
	}
}
if($_GET["beheerder"]) {
	$cms->settings[8]["list"]["show_icon"]=false;
} else {
	$cms->settings[8]["list"]["show_icon"]=true;
}
$cms->settings[8]["list"]["edit_icon"]=true;
$cms->settings[8]["list"]["delete_icon"]=true;

$cms->db[8]["where"]="beheerder='".addslashes($_GET["beheerder"])."'";
$cms->db[8]["set"]="beheerder='".addslashes($_GET["beheerder"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(8,"integer","leverancier_id");
$cms->db_field(8,"text","naam");
$cms->db_field(8,"select","zoekvolgorde","",array("selection"=>$vars["zoekvolgorde"]));
$cms->db_field(8,"url","url");

$cms->db_field(8,"text","contactpersoon");
$cms->db_field(8,"text","faxnummer");
$cms->db_field(8,"email","email");

$cms->db_field(8,"text","contactpersoon_contract");
$cms->db_field(8,"email","email_contract");
$cms->db_field(8,"text","telefoonnummer_contract");
$cms->db_field(8,"text","faxnummer_contract");

$cms->db_field(8,"text","contactpersoon_reserveringen");
$cms->db_field(8,"email","email_reserveringen");
$cms->db_field(8,"text","telefoonnummer_reserveringen");
$cms->db_field(8,"text","faxnummer_reserveringen");

$cms->db_field(8,"text","noodnummer");
$cms->db_field(8,"textarea","adresregels");
$cms->db_field(8,"textarea","opmerkingen_intern");
$cms->db_field(8,"textarea","annuleringscondities");
$cms->db_field(8,"yesno","hoogseizoencontrole");
$cms->db_field(8,"yesno","roominglist_toontelefoonnummer");
$cms->db_field(8,"yesno","roominglist_toonaantaldeelnemers");
$cms->db_field(8,"select","xml_type","",array("selection"=>$vars["xml_type"]));
$cms->db_field(8,"select","bestelmailfax_taal","",array("selection"=>$vars["bestelmailfax_taal"]));
$cms->db_field(8,"yesno","zwitersefranken");
$cms->db_field(8,"integer","aflopen_allotment");
$cms->db_field(8,"yesno","bestelfax_logo");

#$cms->db_field(8,"url","xml_url");
$cms->db_field(8,"picture","voucherlogo","",array("savelocation"=>"pic/cms/voucherlogo_accommodatie/","filetype"=>"jpg"));
$cms->db_field(8,"date","gegevensgecontroleerd");
$cms->db_field(8,"select","standaardbestelmanier","",array("selection"=>$vars["standaardbestelmanier"]));
$cms->db_field(8,"select","bevestigmethode","",array("selection"=>$vars["bevestigmethode"]));
$cms->db_field(8,"textarea","opmerkingen_facturen");
$cms->db_field(8,"integer","aanbetaling_dagen");
$cms->db_field(8,"float","aanbetaling_percentage");
$cms->db_field(8,"currency","aanbetaling_euro");
$cms->db_field(8,"yesno","aanbetaling_incl_toeslag");
$cms->db_field(8,"integer","eindbetaling_dagen_aankomst");
$cms->db_field(8,"integer","eindbetaling_dagen_factuur");
$cms->db_field(8,"textarea","opmerkingen_betalingstermijn");

$cms->db_field(8,"yesno","inlog_toegestaan");
$cms->db_field(8,"radio","inlog_taal","",array("selection"=>array("nl"=>"Nederlands","en"=>"Engels")));
$cms->db_field(8,"select","inlog_wintersite","",array("selection"=>$vars["websites_wzt_actief"][1]),array("one_per_line"));
$cms->db_field(8,"select","inlog_zomersite","",array("selection"=>$vars["websites_wzt_actief"][2]),array("one_per_line"));
$cms->db_field(8,"yesno","inlog_inzichtinkoop");
$cms->db_field(8,"yesno","inlog_inzichtbetalingen");
$cms->db_field(8,"yesno","inlog_toon_derden");
$cms->db_field(8,"password","password");
$cms->db_field(8,"textarea","inlog_afspraken");
$cms->db_field(8,"textarea","inlog_interne_opmerkingen");



# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(8,"naam","Naam");
$cms->list_field(8,"leverancier_id","Intern volgnummer");
$cms->list_field(8,"gegevensgecontroleerd","Gecontroleerd",array("date_format"=>"D MAAND JJJJ"));
#$cms->list_field(1,"volgende_levering","Volgende levering",array("date_format"=>"D MAAND JJJJ"));

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(8,1,"leverancier_id","Intern volgnummer","",array("noedit"=>true));
$cms->edit_field(8,1,"naam");
if(!$_GET["beheerder"]) {
	$cms->edit_field(8,1,"zoekvolgorde","Zoekvolgorde",array("selection"=>3));
}
$cms->edit_field(8,0,"url","Website");
#$cms->edit_field(8,0,"htmlrow","<hr><b>Oude gegevens</b>");
#$cms->edit_field(8,0,"contactpersoon","Contactpersoon");
#$cms->edit_field(8,0,"email","E-mailadres");
#$cms->edit_field(8,0,"faxnummer");
$cms->edit_field(8,0,"htmlrow","<hr><b><i>Contract</i></b>");
$cms->edit_field(8,0,"contactpersoon_contract","Contactpersoon");
$cms->edit_field(8,0,"email_contract","E-mailadres");
$cms->edit_field(8,0,"telefoonnummer_contract","Telefoonnummer");
$cms->edit_field(8,0,"faxnummer_contract","Faxnummer");
$cms->edit_field(8,0,"htmlrow","<div style=\"\">&darr;&nbsp;<a href=\"#\" onclick=\"fieldcopy('contactpersoon_contract','contactpersoon_reserveringen');fieldcopy('email_contract','email_reserveringen');fieldcopy('telefoonnummer_contract','telefoonnummer_reserveringen');fieldcopy('faxnummer_contract','faxnummer_reserveringen');return false;\">kopieer &quot;contract&quot; naar &quot;reserveringen&quot;</a>&nbsp;&darr;</div><hr><b><i>Reserveringen</i></b>");
$cms->edit_field(8,0,"contactpersoon_reserveringen","Contactpersoon");
$cms->edit_field(8,0,"email_reserveringen","E-mailadres");
$cms->edit_field(8,0,"telefoonnummer_reserveringen","Telefoonnummer");
$cms->edit_field(8,0,"faxnummer_reserveringen","Faxnummer");

$cms->edit_field(8,0,"htmlrow","<hr><b><i>Diversen</i></b>");
$cms->edit_field(8,0,"noodnummer");
$cms->edit_field(8,0,"adresregels","Naam + adresgegevens");
if($_GET["beheerder"]) {
	$cms->edit_field(8,1,"bestelmailfax_taal","Taal");
	$cms->edit_field(8,0,"roominglist_toontelefoonnummer","Toon klant-telefoonnummer op roominglist",array("selection"=>false));
	$cms->edit_field(8,0,"roominglist_toonaantaldeelnemers","Toon aantal deelnemers op roominglist",array("selection"=>true));
} else {
	$cms->edit_field(8,0,"htmlrow","<hr>");
	$cms->edit_field(8,0,"roominglist_toontelefoonnummer","Toon klant-telefoonnummer op roominglist",array("selection"=>false));
	$cms->edit_field(8,0,"roominglist_toonaantaldeelnemers","Toon aantal deelnemers op roominglist",array("selection"=>true));
	$cms->edit_field(8,0,"hoogseizoencontrole","Controle uitvoeren op hoogseizoen-tarieven",array("selection"=>true));
	$cms->edit_field(8,1,"zwitersefranken","Deze leverancier gebruikt Zwitserse Franken");
	$cms->edit_field(8,0,"xml_type","Type XML-importeerfunctie");
	#$cms->edit_field(8,0,"xml_url","URL XML-importeerfunctie");
	$cms->edit_field(8,0,"aflopen_allotment","Allotment loopt af (dagen voor aankomst)");
	$cms->edit_field(8,0,"standaardbestelmanier","Standaard-bestelmanier");
	
	$cms->edit_field(8,0,"voucherlogo","Voucherlogo","",array("img_width"=>"600","img_height"=>"600"));
#	$cms->edit_field(8,0,"bestelfax_logo","Bij boekingen via WSA: toon WSA-logo i.p.v. Chalet.nl-logo op bestelfax");
	$cms->edit_field(8,1,"bestelmailfax_taal","Taal bestelmail/fax");

}
$cms->edit_field(8,0,"opmerkingen_intern","Opmerkingen (intern)","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
if(!$_GET["beheerder"]) {
	$cms->edit_field(8,0,"htmlrow","<hr><b><i>Betalingstermijn</i></b>");
	$cms->edit_field(8,0,"htmlrow","<br><i>Aanbetaling</i>");
	$cms->edit_field(8,0,"aanbetaling_dagen","Aantal dagen na ontvangst factuur");
	$cms->edit_field(8,0,"aanbetaling_incl_toeslag","Aanbetaling gaat over bedrag incl. toeslag");
	$cms->edit_field(8,0,"aanbetaling_percentage","Aanbetaling in %");
	$cms->edit_field(8,0,"htmlrow","<i>of</i>");
	$cms->edit_field(8,0,"aanbetaling_euro","Aanbetaling in €");
	$cms->edit_field(8,0,"htmlrow","<br><i>Eindbetaling</i>");
	$cms->edit_field(8,0,"eindbetaling_dagen_aankomst","Aantal dagen vóór (-) of na (+) aankomst van de klant","",array("negative"=>true));
	$cms->edit_field(8,0,"htmlrow","<i>of</i>");
	$cms->edit_field(8,0,"eindbetaling_dagen_factuur","Aantal dagen na ontvangst factuur");
	$cms->edit_field(8,0,"htmlrow","&nbsp;");
	$cms->edit_field(8,0,"opmerkingen_betalingstermijn","Opmerkingen (intern) betalingstermijn","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
	$cms->edit_field(8,0,"htmlrow","<hr>");
	$cms->edit_field(8,0,"annuleringscondities","Annuleringscondities");

	$cms->edit_field(8,0,"htmlrow","<hr><b><i>Facturen</i></b>");
	$cms->edit_field(8,1,"bevestigmethode","Hoe bevestigt deze leverancier een bestelling?");
#	$cms->edit_field(8,0,"opmerkingen_facturen","Opmerkingen m.b.t. facturen","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
	$cms->edit_field(8,0,"opmerkingen_facturen","Opmerkingen m.b.t. facturen");

}

$cms->edit_field(8,0,"htmlrow","<hr><br><b><i>Inlogsysteem - eigenarenlogin</i></b><br><br>Inloggen kan via: <a href=\"https://www.chalet.nl/ownerlogin\" target=\"_blank\">https://www.chalet.nl/ownerlogin</a><br>&nbsp;");
$cms->edit_field(8,0,"inlog_toegestaan","Deze ".$cms->settings[8]["type_single"]." mag inloggen (met contract-mailadres)");
$cms->edit_field(8,0,"inlog_taal","Taal inlogsysteem");
$cms->edit_field(8,0,"inlog_wintersite","Website winter");
$cms->edit_field(8,0,"inlog_zomersite","Website zomer");
$cms->edit_field(8,0,"inlog_inzichtinkoop","Mag inzicht hebben in bruto-/nettoprijs");
$cms->edit_field(8,0,"inlog_inzichtbetalingen","Mag inzicht hebben in betalingen aan de leverancier");
$cms->edit_field(8,0,"inlog_toon_derden","Toon 'Boekingen via derden'","","",array("info"=>"Boekingen via derden is NIET hetzelfde als boekingen via partners!\n\nEen voorbeeld van boekingen via derden zijn de boekingen van PVR in Vallandry op de chalets welke zij beheren, en waarvan de betalingen ook via hun gaan. Wanneer de betaling via ons gaat is het gewoon een partnerboeking welke via \"reisbureaus\" ingeboekt wordt."));
$cms->edit_field(8,0,"inlog_afspraken","Afspraken (te zien na inloggen)");
$cms->edit_field(8,0,"inlog_interne_opmerkingen","Interne opmerkingen m.b.t inloggen","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
$cms->edit_field(8,0,"password","Wachtwoord","",array("new_password"=>true,"strong_password"=>true,"salt"=>$vars["salt"]));
$cms->edit_field(8,0,"htmlrow","Wachtwoord-suggestie: ".wt_generate_password(6));



$cms->edit_field(8,0,"htmlrow","<hr>");
$cms->edit_field(8,0,"gegevensgecontroleerd","Alle bovenstaande gegevens zijn gecontroleerd en compleet bevonden op","","",array("calendar"=>true));


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(8);
if($cms_form[8]->filled) {
	if($cms_form[8]->input["standaardbestelmanier"]==4 and !$cms_form[8]->input["url"]) {
		$cms_form[8]->error("url","verplicht bij standaard-bestelmanier &quot;via inlog op website&quot;");
	}

	if(!$_GET["beheerder"]) {

		if($cms_form[8]->input["aanbetaling_dagen"]<>"" and !$cms_form[8]->input["aanbetaling_percentage"] and !$cms_form[8]->input["aanbetaling_euro"]) {
			$cms_form[8]->error("aanbetaling_dagen","vul ook % of € in");
		}
		if($cms_form[8]->input["aanbetaling_dagen"]=="" and ($cms_form[8]->input["aanbetaling_percentage"] or $cms_form[8]->input["aanbetaling_euro"])) {
			$cms_form[8]->error("aanbetaling_dagen","vul bij de aanbetaling ook het aantal dagen in");
		}
		if($cms_form[8]->input["aanbetaling_percentage"] and $cms_form[8]->input["aanbetaling_euro"]) {
			$cms_form[8]->error("aanbetaling_euro","kies voor % óf €");
		}
		if($cms_form[8]->input["eindbetaling_dagen_aankomst"]<>"" and $cms_form[8]->input["eindbetaling_dagen_factuur"]<>"") {
			$cms_form[8]->error("eindbetaling_dagen_factuur","kies voor dagen na aankomst óf dagen na ontvangst factuur");
		}
		if($cms_form[8]->input["eindbetaling_dagen_aankomst"]=="" and $cms_form[8]->input["eindbetaling_dagen_factuur"]=="") {
			$cms_form[8]->error("eindbetaling_dagen_aankomst","vul het aantal dagen van de eindbetaling in");
		}
	}
	
	if($cms_form[8]->input["inlog_toegestaan"]) {
#		if(!$cms_form[8]->input["password"]) {
#			$cms_form[8]->error("password","verplicht bij inloggen");
#		}
		if(!$cms_form[8]->input["email_contract"]) {
			$cms_form[8]->error("email_contract","verplicht bij inloggen");
		}
		if(!$cms_form[8]->input["inlog_taal"]) {
			$cms_form[8]->error("inlog_taal","verplicht bij inloggen");
		}
		if(!$cms_form[8]->input["inlog_wintersite"]) {
			$cms_form[8]->error("inlog_wintersite","verplicht bij inloggen");
		}
		if(!$cms_form[8]->input["inlog_zomersite"]) {
			$cms_form[8]->error("inlog_zomersite","verplicht bij inloggen");
		}
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[8]="leveranciergegevens";
$cms->show_mainfield[8]="naam";
$cms->show_field(8,"naam","Naam");
$cms->show_field(8,"leverancier_id","Intern volgnummer");

# Controle op delete-opdracht
if($_GET["delete"]==8 and $_GET["8k0"]) {
	$db->query("SELECT type_id FROM type WHERE leverancier_id='".addslashes($_GET["8k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(8,"Er zijn nog <a href=\"cms_types.php?2where=".urlencode("leverancier_id=".$_GET["8k0"])."\">types</a> gekoppeld");
	}
}











#
#
# Subleveranciers
#
#
$cms->settings[8]["connect"][]=42;
$cms->settings[42]["parent"]=8;

$cms->settings[42]["list"]["show_icon"]=false;
$cms->settings[42]["list"]["edit_icon"]=true;
$cms->settings[42]["list"]["delete_icon"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->settings[42]["prevalue"]["leverancier_id"]=$_GET["8k0"];
$cms->db[42]["where"]="leverancier_id='".addslashes($_GET["8k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(42,"text","naam");
$cms->db_field(42,"select","leverancier_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[42]=array("naam");
$cms->list_field(42,"naam","Naam");

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>