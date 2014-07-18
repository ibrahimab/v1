<?php


# zoeken naar "algemeneoptie" en aanzetten!!

$mustlogin=true;

include("admin/vars.php");

if($_GET["11k0"]) {
	$db->query("SELECT algemeneoptie FROM optie_soort WHERE optie_soort_id='".addslashes($_GET["11k0"])."';");
	if($db->next_record() and $db->f("algemeneoptie")) {
		$algemeneoptie=true;
		$koptekst="Algemene optie (per boeking)";
	} else {
		$koptekst="Optie per persoon";
	}
}

if($_GET["11k0"]) {
	# controle op ingevulde tarieven (per seizoen)
	$db->query("SELECT seizoen_id, naam FROM seizoen WHERE optietarieven_controleren_in_cms=1 ORDER BY begin, eind;");
	while($db->next_record()) {
		$sz_controle[$db->f("seizoen_id")]=$db->f("naam");
	}

	while(list($key,$value)=@each($sz_controle)) {
		$db->query("SELECT DISTINCT oo.optie_groep_id, oo.optie_onderdeel_id FROM optie_onderdeel oo, optie_tarief ot WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND ot.seizoen_id='".$key."';");
		while($db->next_record()) {
			$sz_controle_optie_onderdeel[$key][$db->f("optie_groep_id")]++;
		}
		$db->query("SELECT optie_groep_id, COUNT(optie_onderdeel_id) AS aantal FROM optie_onderdeel GROUP BY optie_groep_id;");
		while($db->next_record()) {
			if($db->f("aantal")==$sz_controle_optie_onderdeel[$key][$db->f("optie_groep_id")]) {
				$sz_controle_array[$key][$db->f("optie_groep_id")]="compleet";
			}
		}
	}
}

$cms->settings[11]["list"]["show_icon"]=true;
$cms->settings[11]["list"]["edit_icon"]=true;
$cms->settings[11]["list"]["delete_icon"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(11,"text","internenaam");
$cms->db_field(11,"text","naam");
if($vars["cmstaal"]) $cms->db_field(11,"text","naam_".$vars["cmstaal"]);
$cms->db_field(11,"text","naam_enkelvoud");
if($vars["cmstaal"]) $cms->db_field(11,"text","naam_enkelvoud_".$vars["cmstaal"]);
$cms->db_field(11,"textarea","omschrijving");
if($vars["cmstaal"]) $cms->db_field(11,"textarea","omschrijving_".$vars["cmstaal"]);
#$cms->db_field(11,"select","gekoppeld_id","",array("othertable"=>"11","otherkeyfield"=>"optie_soort_id","otherfield"=>"naam","otherwhere"=>"optie_soort_id<>'".addslashes($_GET["11k0"])."'"));
$cms->db_field(11,"select","optiecategorie","",array("selection"=>$vars["optiecategorie"]));
$cms->db_field(11,"integer","volgorde");
$cms->db_field(11,"yesno","annuleringsverzekering");
$cms->db_field(11,"yesno","reisverzekering");
$cms->db_field(11,"yesno","voucher");
$cms->db_field(11,"yesno","winter");
$cms->db_field(11,"yesno","zomer");
$cms->db_field(11,"yesno","beschikbaar_directeklanten");
$cms->db_field(11,"yesno","beschikbaar_wederverkoop");
$cms->db_field(11,"yesno","algemeneoptie");
$cms->db_field(11,"text","tekst_beschikbaarheid");
$cms->db_field(11,"yesno","persoonsgegevensgewenst");
$cms->db_field(11,"yesno","losse_skipas");
$cms->db_field(11,"yesno","verbergen_in_cms");

# in list "verbergen_in_cms" verbergen
if(!$_GET["edit"] and !$_GET["show"] and !$_GET["delete"] and !$_GET["add"]) {
	if($_GET["verborgen"]) {
		$cms->db[11]["where"]="verbergen_in_cms=1";
	} else {
		$cms->db[11]["where"]="verbergen_in_cms=0";
	}
}

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[11]=array("volgorde","naam");
$cms->list_field(11,"internenaam","Interne naam");
$cms->list_field(11,"beschikbaar_directeklanten","Directe klanten");
$cms->list_field(11,"beschikbaar_wederverkoop","Wederverkoop");
$cms->list_field(11,"algemeneoptie","Algemeen");
$cms->list_field(11,"volgorde","Volgorde");
$cms->list_field(11,"optiecategorie","Categorie");


if($koptekst) {
	$cms->edit_field(11,0,"htmlrow","<b>".wt_he($koptekst)."</b>");
}
# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(11,1,"internenaam","Interne naam");
if($vars["cmstaal"]) {
	$cms->edit_field(11,0,"naam","Naam NL","",array("noedit"=>true));
	$cms->edit_field(11,0,"naam_".$vars["cmstaal"],"Naam ".strtoupper($vars["cmstaal"]));
	$cms->edit_field(11,0,"naam_enkelvoud","Naam enkelvoud NL","",array("noedit"=>true));
	$cms->edit_field(11,0,"naam_enkelvoud_".$vars["cmstaal"],"Naam enkelvoud ".strtoupper($vars["cmstaal"]));
	$cms->edit_field(11,0,"omschrijving","Omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(11,0,"omschrijving_".$vars["cmstaal"],"Omschrijving ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(11,1,"naam","Naam (klanten)");
	$cms->edit_field(11,1,"naam_enkelvoud","Naam enkelvoud (klanten)");
	$cms->edit_field(11,0,"omschrijving","Omschrijving (klanten)");
}

$cms->edit_field(11,1,"optiecategorie","Optie-categorie");
$cms->edit_field(11,1,"volgorde");
$cms->edit_field(11,0,"tekst_beschikbaarheid","Tekst beschikbaarheidsformulier");


# Algemene optie
if($_GET["add"]=="11") {
	$cms->edit_field(11,0,"algemeneoptie","Algemene optie (niet aan personen, maar aan de hele boeking gekoppeld)");
}

$cms->edit_field(11,0,"annuleringsverzekering","Valt binnen annuleringsverzekering",array("selection"=>true));
$cms->edit_field(11,0,"reisverzekering","Is een reisverzekering");
$cms->edit_field(11,0,"voucher","Komt op een voucher",array("selection"=>true));
$cms->edit_field(11,0,"winter","Beschikbaar voor winteraccommodaties");
$cms->edit_field(11,0,"zomer","Beschikbaar voor zomeraccommodaties");
$cms->edit_field(11,0,"beschikbaar_directeklanten","Beschikbaar voor directe klanten (= niet-wederverkoop)",array("selection"=>true));
$cms->edit_field(11,0,"beschikbaar_wederverkoop","Beschikbaar voor wederverkoop");
$cms->edit_field(11,0,"persoonsgegevensgewenst","Persoonsgegevens gewenst (klanten ontvangen automatisch een mailtje als ze niet op tijd hun gegevens invullen)");
$cms->edit_field(11,0,"losse_skipas","Deze optie-soort omvat losse skipassen (alleen voor accommodaties zonder skipas!)");
$cms->edit_field(11,0,"verbergen_in_cms","Deze optie-soort verbergen in het CMS (kan alleen als er geen actuele boekingen aan deze optie-soort gekoppeld zijn)");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(11);
if($cms_form[11]->filled) {
	if($cms_form[11]->input["reisverzekering"]) {
		$db->query("SELECT naam FROM optie_soort WHERE reisverzekering=1 AND optie_soort_id<>'".addslashes($_GET["11k0"])."';");
		if($db->next_record()) {
			$cms_form[11]->error("reisverzekering","er is al een reisverzekering-optie-soort aanwezig (&quot;".wt_he($db->f("naam"))."&quot; genaamd)");
		}
	}
	if($cms_form[11]->input["voucher"] and ($cms_form[11]->input["algemeneoptie"] or $algemeneoptie)) {
		$cms_form[11]->error("voucher","Bij een algemene optie kan geen voucher worden aangemaakt");
	}
	if($cms_form[11]->input["verbergen_in_cms"]) {
		$db->query("SELECT DISTINCT SUBSTR(b.boekingsnummer,2), b.boeking_id, b.boekingsnummer, s.naam FROM boeking b, boeking_optie bo, view_optie v, seizoen s WHERE b.seizoen_id=s.seizoen_id AND s.eind>NOW() AND v.optie_soort_id='".addslashes($_GET["11k0"])."' AND bo.optie_onderdeel_id=v.optie_onderdeel_id AND bo.boeking_id=b.boeking_id AND boekingsnummer<>'' ORDER BY 1;");
		if($db->num_rows()) {
			$melding="verbegen in het CMS niet mogelijk: er zijn nog actuele boekingen aan dit optie-onderdeel gekoppeld:<ul>";
			while($db->next_record()) {
				$melding.="<li><a href=\"cms_boekingen.php?show=21&21k0=".$db->f("boeking_id")."\" target=\"_blank\">".wt_he($db->f("boekingsnummer"))."</a> (".$db->f("naam").")</li>";
			}
			$melding.="</ul>";
			$cms_form[11]->error("verbergen_in_cms",$melding);
		}
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[11]="optiesoort-gegevens";
$cms->show_mainfield[11]="naam";
$cms->show_field(11,"naam","Naam");
$cms->show_field(11,"algemeneoptie","Algemene optie");

# Controle op delete-opdracht
if($_GET["delete"]==11 and $_GET["11k0"]) {
	$db->query("SELECT optie_groep_id FROM optie_groep WHERE optie_soort_id='".addslashes($_GET["11k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(11,"Er zijn nog <a href=\"cms_optie_groepen.php?12where=".urlencode("optie_soort_id=".$_GET["11k0"])."\">optie-groepen</a> gekoppeld");
	}
}


#
#
# optie_groep
#
#
$cms->settings[11]["connect"][]=12;
$cms->settings[12]["parent"]=11;

$cms->settings[12]["list"]["show_icon"]=true;
$cms->settings[12]["list"]["edit_icon"]=true;
$cms->settings[12]["list"]["delete_icon"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->settings[12]["prevalue"]["optie_soort_id"]=$_GET["11k0"];
$cms->db[12]["where"]="optie_soort_id='".addslashes($_GET["11k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(12,"text","naam");
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->db_field(12,"select","optietarieven_controleren_in_cms_".$key,"optie_groep_id",array("selection"=>$sz_controle_array[$key]));
	}
}

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[12]=array("naam");
$cms->list_field(12,"naam","Naam");
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->list_field(12,"optietarieven_controleren_in_cms_".$key,$value);
	}
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>