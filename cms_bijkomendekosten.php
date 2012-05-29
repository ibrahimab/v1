<?php

$mustlogin=true;
include("admin/vars.php");

if(!$_GET["33k0"]) {
	# controle op ingevulde tarieven (per seizoen)
	$db->query("SELECT seizoen_id, naam FROM seizoen WHERE optietarieven_controleren_in_cms=1 ORDER BY begin, eind;");
	while($db->next_record()) {
		$sz_controle[$db->f("seizoen_id")]=$db->f("naam");
	}
	
	while(list($key,$value)=@each($sz_controle)) {
		$db->query("SELECT DISTINCT bijkomendekosten_id FROM bijkomendekosten_tarief WHERE seizoen_id='".$key."';");
		while($db->next_record()) {
			$sz_controle_array[$key][$db->f("bijkomendekosten_id")]="ingevuld";
		}
	}
}


#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(33,"text","internenaam");
$cms->db_field(33,"text","soort");
if($vars["cmstaal"]) $cms->db_field(33,"text","soort_".$vars["cmstaal"]);
$cms->db_field(33,"text","naam");
if($vars["cmstaal"]) $cms->db_field(33,"text","naam_".$vars["cmstaal"]);
$cms->db_field(33,"textarea","omschrijving");
if($vars["cmstaal"]) $cms->db_field(33,"textarea","omschrijving_".$vars["cmstaal"]);
$cms->db_field(33,"select","perboekingpersoon","",array("selection"=>$vars["bijkomendekosten_perboekingpersoon"]));
$cms->db_field(33,"select","gekoppeldaan","",array("selection"=>$vars["bijkomendekosten_gekoppeldaan"]));
$cms->db_field(33,"integer","min_leeftijd");
$cms->db_field(33,"integer","max_leeftijd");
$cms->db_field(33,"yesno","zonderleeftijd");
$cms->db_field(33,"yesno","hoort_bij_accommodatieinkoop");
$cms->db_field(33,"select","optiecategorie","",array("selection"=>$vars["optiecategorie"]));
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->db_field(33,"select","optietarieven_controleren_in_cms_".$key,"bijkomendekosten_id",array("selection"=>$sz_controle_array[$key]));
	}
}

#
# List
#
# Te tonen icons/links bij list
$cms->settings[33]["list"]["show_icon"]=true;
$cms->settings[33]["list"]["edit_icon"]=true;
$cms->settings[33]["list"]["delete_icon"]=true;
$cms->settings[33]["list"]["add_link"]=true;
$cms->settings[33]["show"]["goto_new_record"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[33]=array("gekoppeldaan","internenaam");
$cms->list_field(33,"internenaam","Naam");
$cms->list_field(33,"gekoppeldaan","Gekoppeld aan");
$cms->list_field(33,"optiecategorie","Categorie");
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->list_field(33,"optietarieven_controleren_in_cms_".$key,$value);
	}
}


# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[33]="Bijkomende kosten";
$cms->show_mainfield[33]="internenaam";
$cms->show_field(33,"internenaam","Naam");
$cms->show_field(33,"gekoppeldaan","Kan worden gekoppeld aan");
$cms->show_field(33,"perboekingpersoon","Soort");


# Controle op delete-opdracht
if($_GET["delete"]==33 and $_GET["33k0"]) {
	# Kijken of deze bijkomendekosten ergens worden gebruikt
	$db->query("SELECT accommodatie_id FROM accommodatie WHERE bijkomendekosten1_id='".addslashes($_GET["33k0"])."' OR bijkomendekosten2_id='".addslashes($_GET["33k0"])."'OR bijkomendekosten3_id='".addslashes($_GET["33k0"])."'OR bijkomendekosten4_id='".addslashes($_GET["33k0"])."'OR bijkomendekosten5_id='".addslashes($_GET["33k0"])."' OR bijkomendekosten6_id='".addslashes($_GET["33k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(33,"Deze bijkomende kosten zijn nog gekoppeld aan accommodaties");
	}
	$db->query("SELECT type_id FROM type WHERE bijkomendekosten1_id='".addslashes($_GET["33k0"])."' OR bijkomendekosten2_id='".addslashes($_GET["33k0"])."'OR bijkomendekosten3_id='".addslashes($_GET["33k0"])."'OR bijkomendekosten4_id='".addslashes($_GET["33k0"])."'OR bijkomendekosten5_id='".addslashes($_GET["33k0"])."' OR bijkomendekosten6_id='".addslashes($_GET["33k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(33,"Deze bijkomende kosten zijn nog gekoppeld aan accommodatie-types");
	}
	$db->query("SELECT skipas_id FROM skipas WHERE bijkomendekosten_id='".addslashes($_GET["33k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(33,"Deze bijkomende kosten zijn nog gekoppeld aan skipassen");
	}
	$db->query("SELECT optie_onderdeel_id FROM optie_onderdeel WHERE bijkomendekosten_id='".addslashes($_GET["33k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(33,"Deze bijkomende kosten zijn nog gekoppeld aan opties");
	}
}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(33)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(33,1,"internenaam","Naam (voor intern gebruik)");
$cms->edit_field(33,1,"gekoppeldaan","Kan worden gekoppeld aan");
$cms->edit_field(33,1,"perboekingpersoon","Type");

if($vars["cmstaal"]) {
#	$cms->edit_field(33,1,"soort","Soort (voor klant) NL","",array("noedit"=>true));
#	$cms->edit_field(33,1,"soort_".$vars["cmstaal"],"Soort (voor klant) ".strtoupper($vars["cmstaal"]));

	$cms->edit_field(33,1,"naam","Omschrijving (voor klant) NL","",array("noedit"=>true));
	$cms->edit_field(33,1,"naam_".$vars["cmstaal"],"Omschrijving (voor klant) ".strtoupper($vars["cmstaal"]));

	$cms->edit_field(33,0,"omschrijving","Toelichting (voor klant, na doorklikken) NL","",array("noedit"=>true));
	$cms->edit_field(33,0,"omschrijving_".$vars["cmstaal"],"Toelichting (voor klant, na doorklikken) ".strtoupper($vars["cmstaal"]));
} else {
#	$cms->edit_field(33,1,"soort","Soort (voor klant)");
	$cms->edit_field(33,1,"naam","Omschrijving (voor klant)");
	$cms->edit_field(33,0,"omschrijving","Toelichting (accommodatiepagina, na doorklikken)");
}
$cms->edit_field(33,0,"hoort_bij_accommodatieinkoop","Deze kosten worden berekend op de factuur van de accommodatie-leverancier");
$cms->edit_field(33,1,"optiecategorie","Optie-categorie");



$cms->edit_field(33,0,"htmlrow","<hr><b>Leeftijdscontrole</b>");
$cms->edit_field(33,0,"min_leeftijd","Minimale leeftijd (in jaren)");
$cms->edit_field(33,0,"max_leeftijd","Maximale leeftijd (in jaren)");
$cms->edit_field(33,0,"zonderleeftijd","Kosten toevoegen aan persoon indien geboortedatum niet bekend is");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(33);
if($cms_form[33]->filled) {
	if($cms_form[33]->input["min_leeftijd"] and $cms_form[33]->input["perboekingpersoon"]==1) {
		$cms_form[33]->error("min_leeftijd","alleen mogelijk bij &quot;per persoon&quot;");
	}
	if($cms_form[33]->input["max_leeftijd"] and $cms_form[33]->input["perboekingpersoon"]==1) {
		$cms_form[33]->error("max_leeftijd","alleen mogelijk bij &quot;per persoon&quot;");
	}
	if($cms_form[33]->input["gekoppeldaan"]==3 and $cms_form[33]->input["perboekingpersoon"]==1) {
		$cms_form[33]->error("perboekingpersoon","bij opties alleen per persoon mogelijk");
	}
	if($cms_form[33]->input["zonderleeftijd"] and !$cms_form[33]->input["min_leeftijd"] and !$cms_form[33]->input["max_leeftijd"]) {
		$cms_form[33]->error("zonderleeftijd","Kosten zonder geboortedatum: alleen mogelijk bij gebruik minimale of maximale leeftijd");
	}
	if($cms_form[33]->input["gekoppeldaan"]==3 and $cms_form[33]->input["min_leeftijd"]) {
		$cms_form[33]->error("min_leeftijd","de minimale leeftijd kun je opgeven bij de gekoppelde optie");
	}
	if($cms_form[33]->input["gekoppeldaan"]==3 and $cms_form[33]->input["max_leeftijd"]) {
		$cms_form[33]->error("max_leeftijd","de maximale leeftijd kun je opgeven bij de gekoppelde optie");
	}
	if($cms_form[33]->input["hoort_bij_accommodatieinkoop"] and $cms_form[33]->input["optiecategorie"]>2) {
		$cms_form[33]->error("optiecategorie","niet van toepassing op de factuur van de accommodatie-leverancier");
	}
}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>