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

	# Seizoenen laden t.b.v. vertrekinfo_seizoengoedgekeurd
	$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY begin, eind;");
	while($db->next_record()) {
		$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
		$laatste_seizoen=$db->f("seizoen_id");
	}

	# Vertrekinfo-tracking
	$vertrekinfo_tracking_array=array("vertrekinfo_incheck_sjabloon_id", "vertrekinfo_soortbeheer", "vertrekinfo_soortbeheer_aanvulling", "vertrekinfo_telefoonnummer", "vertrekinfo_inchecktijd", "vertrekinfo_uiterlijkeinchecktijd", "vertrekinfo_uitchecktijd", "vertrekinfo_route", "vertrekinfo_soortadres", "vertrekinfo_adres", "vertrekinfo_plaatsnaam_beheer", "vertrekinfo_gps_lat", "vertrekinfo_gps_long");
	if($vars["cmstaal"]) {
		$vertrekinfo_tracking_array[]="vertrekinfo_route_".$vars["cmstaal"];
		$vertrekinfo_tracking_array[]="vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"];
	}
	$vertrekinfo_tracking=vertrekinfo_tracking("leverancier",$vertrekinfo_tracking_array,$_GET["8k0"],$laatste_seizoen);
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

$cms->db_field(8,"text","contactpersoon_lijsten");
$cms->db_field(8,"email","email_lijsten");
$cms->db_field(8,"select","roominglist_versturen","",array("selection"=>array(1=>"ja",2=>"nee")));
$cms->db_field(8,"select","aankomstlijst_versturen","",array("selection"=>array(1=>"ja",2=>"nee")));

$cms->db_field(8,"text","noodnummer");
$cms->db_field(8,"textarea","adresregels");
$cms->db_field(8,"textarea","opmerkingen_intern");
$cms->db_field(8,"textarea","annuleringscondities");
$cms->db_field(8,"yesno","hoogseizoencontrole");
$cms->db_field(8,"yesno","roominglist_toontelefoonnummer");
$cms->db_field(8,"yesno","roominglist_toonaantaldeelnemers");
$cms->db_field(8,"select","roominglist_site_benaming","",array("selection"=>$vars["roominglist_site_benaming"]));
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

# Nieuw vertrekinfo-systeem
$cms->db_field(8,"checkbox","vertrekinfo_goedgekeurd_seizoen","",array("selection"=>$vars["seizoengoedgekeurd"]));
if($vars["cmstaal"]) $cms->db_field(8,"checkbox","vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(8,"text","vertrekinfo_goedgekeurd_datetime");
if($vars["cmstaal"]) $cms->db_field(8,"text","vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"]);
$cms->db_field(8,"select","vertrekinfo_incheck_sjabloon_id","",array("othertable"=>"54","otherkeyfield"=>"vertrekinfo_sjabloon_id","otherfield"=>"naam","otherwhere"=>"soort=1"));
$cms->db_field(8,"select","vertrekinfo_soortbeheer","",array("selection"=>$vars["vertrekinfo_soortbeheer"]));
$cms->db_field(8,"text","vertrekinfo_soortbeheer_aanvulling");
if($vars["cmstaal"]) $cms->db_field(8,"text","vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]);
$cms->db_field(8,"text","vertrekinfo_telefoonnummer");
$cms->db_field(8,"text","vertrekinfo_inchecktijd");
$cms->db_field(8,"text","vertrekinfo_uiterlijkeinchecktijd");
$cms->db_field(8,"text","vertrekinfo_uitchecktijd");
$cms->db_field(8,"textarea","vertrekinfo_inclusief");
if($vars["cmstaal"]) $cms->db_field(8,"textarea","vertrekinfo_inclusief_".$vars["cmstaal"]);
$cms->db_field(8,"textarea","vertrekinfo_exclusief");
if($vars["cmstaal"]) $cms->db_field(8,"textarea","vertrekinfo_exclusief_".$vars["cmstaal"]);
$cms->db_field(8,"textarea","vertrekinfo_route");
if($vars["cmstaal"]) $cms->db_field(8,"textarea","vertrekinfo_route_".$vars["cmstaal"]);
$cms->db_field(8,"select","vertrekinfo_soortadres","",array("selection"=>$vars["vertrekinfo_soortadres"]));
$cms->db_field(8,"textarea","vertrekinfo_adres");
$cms->db_field(8,"text","vertrekinfo_plaatsnaam_beheer");
$cms->db_field(8,"text","vertrekinfo_gps_lat");
$cms->db_field(8,"text","vertrekinfo_gps_long");


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
$cms->edit_field(8,0,"htmlrow","<div style=\"\">&darr;&nbsp;<a href=\"#\" onclick=\"fieldcopy('contactpersoon_reserveringen','contactpersoon_lijsten');fieldcopy('email_reserveringen','email_lijsten');return false;\">kopieer &quot;reserveringen&quot; naar &quot;aankomstlijst/roominglist&quot;</a>&nbsp;&darr;</div>");

$cms->edit_field(8,0,"htmlrow","<hr><b><i>Diversen</i></b>");
$cms->edit_field(8,0,"noodnummer");
$cms->edit_field(8,0,"adresregels","Naam + adresgegevens");
if($_GET["beheerder"]) {
	$cms->edit_field(8,1,"bestelmailfax_taal","Taal");
	$cms->edit_field(8,0,"htmlrow","<hr><b><i>Aankomstlijst/roominglist</i></b>");

	$cms->edit_field(8,0,"contactpersoon_lijsten","Contactpersoon");
	$cms->edit_field(8,0,"email_lijsten","E-mailadres");

	$cms->edit_field(8,1,"aankomstlijst_versturen","Wil aankomstlijsten ontvangen");
	$cms->edit_field(8,1,"roominglist_versturen","Wil roominglists ontvangen");

	$cms->edit_field(8,0,"roominglist_toontelefoonnummer","Toon klant-telefoonnummer op aankomstlijst",array("selection"=>false));
	$cms->edit_field(8,0,"roominglist_toonaantaldeelnemers","Toon aantal deelnemers op aankomstlijst",array("selection"=>true));
	$cms->edit_field(8,1,"roominglist_site_benaming","Site-benaming op aankomstlijst","","",array("info"=>"Welke sites moeten er boven de roominglist genoemd worden?"));
	$cms->edit_field(8,0,"htmlrow","<hr>");
} else {
	$cms->edit_field(8,0,"htmlrow","<hr><b><i>Aankomstlijst/roominglist</i></b>");
	$cms->edit_field(8,0,"contactpersoon_lijsten","Contactpersoon");
	$cms->edit_field(8,0,"email_lijsten","E-mailadres");

	$cms->edit_field(8,1,"aankomstlijst_versturen","Wil aankomstlijsten ontvangen","","",array("info"=>"Mag alleen uitgezet worden als de lijst naar de beheerder gaat"));
	$cms->edit_field(8,1,"roominglist_versturen","Wil roominglists ontvangen","","",array("info"=>"Mag alleen uitgezet worden als de lijst naar de beheerder gaat"));

	$cms->edit_field(8,0,"roominglist_toontelefoonnummer","Toon klant-telefoonnummer op aankomstlijst",array("selection"=>false));
	$cms->edit_field(8,0,"roominglist_toonaantaldeelnemers","Toon aantal deelnemers op aankomstlijst",array("selection"=>true));
	$cms->edit_field(8,1,"roominglist_site_benaming","Site-benaming op aankomstlijst","","",array("info"=>"Welke sites moeten er boven de roominglist genoemd worden?"));
	$cms->edit_field(8,0,"htmlrow","<hr>");
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

$cms->edit_field(8,0,"htmlrow","<hr><br><b><i>Inlogsysteem - eigenarenlogin</i></b><br><br>Inloggen kan via:<ul><li><a href=\"https://www.chalet.nl/ownerlogin\" target=\"_blank\">https://www.chalet.nl/ownerlogin</a></li><li><a href=\"http://www.venturasol.nl/ownerlogin\" target=\"_blank\">http://www.venturasol.nl/ownerlogin</a></li></ul>");
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

# Nieuw vertrekinfo-systeem
if(!$_GET["beheerder"]) {
	$cms->edit_field(8,0,"htmlrow","<a name=\"vertrekinfo\"></a><hr><br><b>Vertrekinfo-systeem</b>");
	$cms->edit_field(8,0,"htmlrow","<br><i>Alinea 'Inchecken'</i>");
	$cms->edit_field(8,0,"vertrekinfo_incheck_sjabloon_id","Sjabloon inchecken");
	if($vertrekinfo_tracking["vertrekinfo_incheck_sjabloon_id"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_incheck_sjabloon_id"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_soortbeheer","Type beheer");
	if($vertrekinfo_tracking["vertrekinfo_soortbeheer"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer"]))."</div>"));
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(8,0,"vertrekinfo_soortbeheer_aanvulling","Aanvulling bij type beheer NL","",array("noedit"=>true));
		$cms->edit_field(8,0,"vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"],"Aanvulling bij type beheer ".strtoupper($vars["cmstaal"]));
		if($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]]) {
			$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]]))."</div>"));
		}
	} else {
		$cms->edit_field(8,0,"vertrekinfo_soortbeheer_aanvulling","Aanvulling bij type beheer","","",array("info"=>"Bijvoorbeeld de naam van een contactpersoon: 'Carine'"));
		if($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling"]) {
			$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling"]))."</div>"));
		}
	}
	$cms->edit_field(8,0,"vertrekinfo_telefoonnummer","Telefoonnummer beheer","","",array("info"=>"Bijvoorbeeld: '0039 0437 72 38 05'"));
	if($vertrekinfo_tracking["vertrekinfo_telefoonnummer"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_telefoonnummer"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_inchecktijd","Inchecktijd","","",array("info"=>"Bijvoorbeeld: '17:00'"));
	if($vertrekinfo_tracking["vertrekinfo_inchecktijd"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_inchecktijd"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_uiterlijkeinchecktijd","Uiterlijke inchecktijd","","",array("info"=>"Bijvoorbeeld: '19:00'"));
	if($vertrekinfo_tracking["vertrekinfo_uiterlijkeinchecktijd"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_uiterlijkeinchecktijd"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_uitchecktijd","Uitchecktijd","","",array("info"=>"Bijvoorbeeld: '09:00'"));
	// $cms->edit_field(8,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Inclusief'</i>");
	// $cms->edit_field(8,0,"htmlcol","Inclusief-tekst website",array("html"=>"<div id=\"vertrekinfo_inclusief_website\" class=\"vertrekinfo_prevalue\"></div>"));
	// if($vertrekinfo_tracking["inclusief"]) {
	// 	$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["inclusief"]))."</div>"));
	// }
	// $cms->edit_field(8,0,"vertrekinfo_inclusief","Afwijkende inclusief-tekst","","",array("info"=>"Indien de tekst niet afwijkt van de website-tekst, dan hier niks invullen."));
	// if($vertrekinfo_tracking["vertrekinfo_inclusief"]) {
	// 	$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_inclusief"]))."</div>"));
	// }
	// $cms->edit_field(8,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Exclusief'</i>");
	// $cms->edit_field(8,0,"htmlcol","Exclusief-tekst website",array("html"=>"<div id=\"vertrekinfo_exclusief_website\" class=\"vertrekinfo_prevalue\"></div>"));
	// if($vertrekinfo_tracking["exclusief"]) {
	// 	$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["exclusief"]))."</div>"));
	// }
	// $cms->edit_field(8,0,"vertrekinfo_exclusief","Afwijkende exclusief-tekst","","",array("info"=>"Indien de tekst niet afwijkt van de website-tekst, dan hier niks invullen."));
	$cms->edit_field(8,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Routebeschrijving naar de receptie of accommodatie' (wordt toegevoegd aan de routebeschrijving naar de betreffende plaats)</i>");
	if($vars["cmstaal"]) {
		$cms->edit_field(8,0,"vertrekinfo_route","Routebeschrijving NL","",array("noedit"=>true));
		$cms->edit_field(8,0,"vertrekinfo_route_".$vars["cmstaal"],"Routebeschrijving ".strtoupper($vars["cmstaal"]));
		if($vertrekinfo_tracking["vertrekinfo_route_".$vars["cmstaal"]]) {
			$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_route_".$vars["cmstaal"]]))."</div>"));
		}
	} else {
		$cms->edit_field(8,0,"vertrekinfo_route","Routebeschrijving");
		if($vertrekinfo_tracking["vertrekinfo_route"]) {
			$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_route"]))."</div>"));
		}
	}
	$cms->edit_field(8,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Adres'</i>");
	$cms->edit_field(8,0,"vertrekinfo_soortadres","Type adres");
	if($vertrekinfo_tracking["vertrekinfo_soortadres"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortadres"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_adres","Adres");
	if($vertrekinfo_tracking["vertrekinfo_adres"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_adres"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_plaatsnaam_beheer","Afwijkende plaatsnaam beheer","","",array("info"=>"Alleen invullen indien het beheer zich in een andere plaats dan de accommodatie bevindt."));
	if($vertrekinfo_tracking["vertrekinfo_plaatsnaam_beheer"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_plaatsnaam_beheer"]))."</div>"));
	}
	$cms->edit_field(8,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'GPS-co&ouml;rdinaten'</i>");
	if($vertrekinfo_tracking["vertrekinfo_gps_lat"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_gps_lat"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_gps_lat","GPS latitude beheer","","",array("info"=>"Alleen invullen indien deze afwijkt van de accommodatie-GPS-coördinaten. Vul de breedtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 52.086508"));
	if($vertrekinfo_tracking["vertrekinfo_gps_long"]) {
		$cms->edit_field(8,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_gps_long"]))."</div>"));
	}
	$cms->edit_field(8,0,"vertrekinfo_gps_long","GPS longitude beheer","","",array("info"=>"Alleen invullen indien deze afwijkt van de accommodatie-GPS-coördinaten. Vul de lengtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 4.886513"));

	if($vars["cmstaal"]) {
		$cms->edit_field(8,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo ".strtoupper($vars["cmstaal"])."</b>");
		$cms->edit_field(8,0,"vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"Vertrekinfo is goedgekeurd voor seizoen ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
		$cms->edit_field(8,0,"vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"],"Laatste goedkeuring ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
	} else {
		$cms->edit_field(8,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo</b>");
		$cms->edit_field(8,0,"vertrekinfo_goedgekeurd_seizoen","Vertrekinfo is goedgekeurd voor seizoen","","",array("one_per_line"=>true));
		$cms->edit_field(8,0,"vertrekinfo_goedgekeurd_datetime","Laatste goedkeuring","","",array("one_per_line"=>true));
	}
}

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