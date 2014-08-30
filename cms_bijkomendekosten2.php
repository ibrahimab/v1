<?php

$mustlogin=true;
include("admin/vars.php");


// nieuwe waarde volgorde bepalen
$db->query("SELECT MAX(volgorde) AS volgorde FROM bk_soort WHERE 1=1;");
if($db->next_record()) {
	$volgorde = $db->f("volgorde");
}
$volgorde=$volgorde+10;


#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(57,"integer","volgorde");
$cms->db_field(57,"text","naam");
if($vars["cmstaal"]) $cms->db_field(57,"text","naam_".$vars["cmstaal"]);
$cms->db_field(57,"text","vouchernaam");
if($vars["cmstaal"]) $cms->db_field(57,"text","vouchernaam_".$vars["cmstaal"]);
$cms->db_field(57,"textarea","toelichting");
if($vars["cmstaal"]) $cms->db_field(57,"textarea","toelichting_".$vars["cmstaal"]);
$cms->db_field(57,"yesno","altijd_invullen");
$cms->db_field(57,"yesno","altijd_diversen");
$cms->db_field(57,"yesno","prijs_per_nacht");
$cms->db_field(57,"yesno","borg");
$cms->db_field(57,"checkbox","eenheden","",array("selection"=>$vars["bk_eenheid"]));
$cms->db_field(57,"yesno","hoort_bij_accommodatieinkoop");
$cms->db_field(57,"select","optiecategorie","",array("selection"=>$vars["optiecategorie"]));

#
# List
#
# Te tonen icons/links bij list
$cms->settings[57]["list"]["show_icon"]=true;
$cms->settings[57]["list"]["edit_icon"]=true;
$cms->settings[57]["list"]["delete_icon"]=true;
$cms->settings[57]["list"]["add_link"]=true;
$cms->settings[57]["show"]["goto_new_record"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[57]=array("volgorde","naam");
$cms->list_field(57,"naam","Naam");
$cms->list_field(57,"volgorde","Volgorde");


# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[57]="Bijkomende kosten";
$cms->show_mainfield[57]="naam";
$cms->show_field(57,"naam","Naam");


# Controle op delete-opdracht
if($_GET["delete"]==57 and $_GET["57k0"]) {


}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(57)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(57,1,"volgorde","Volgorde in CMS", array("text"=>$volgorde));

if($vars["cmstaal"]) {

	// $cms->edit_field(57,1,"naam","Omschrijving (voor klant) NL","",array("noedit"=>true));
	// $cms->edit_field(57,1,"naam_".$vars["cmstaal"],"Omschrijving (voor klant) ".strtoupper($vars["cmstaal"]));

	// $cms->edit_field(57,0,"omschrijving","Toelichting (voor klant, na doorklikken) NL","",array("noedit"=>true));
	// $cms->edit_field(57,0,"omschrijving_".$vars["cmstaal"],"Toelichting (voor klant, na doorklikken) ".strtoupper($vars["cmstaal"]));
} else {

	$cms->edit_field(57,1,"naam","Naam","",array("data_field"=>array("copy_field_to"=>"vouchernaam")),array("input_class"=>"wtform_input copy_field"));
	$cms->edit_field(57,1,"vouchernaam","Naam op voucher");
	$cms->edit_field(57,0,"toelichting","Toelichting (accommodatiepagina, na doorklikken)");
}

$cms->edit_field(57,1,"altijd_invullen","Deze kosten moeten bij iedere accommodatie worden ingevuld");
$cms->edit_field(57,1,"altijd_diversen","Deze kosten vallen altijd onder het kopje \"Diversen\"");
$cms->edit_field(57,1,"prijs_per_nacht","Het ingevoerde tarief is een prijs per nacht (systeem rekent dit automatisch om naar weekprijs)");
$cms->edit_field(57,1,"borg","Dit is een borg");
$cms->edit_field(57,0,"eenheden","Te selecteren eenheden", "", "", array("one_per_line"=>true));


$cms->edit_field(57,0,"hoort_bij_accommodatieinkoop","Deze kosten worden berekend op de factuur van de accommodatie-leverancier");
$cms->edit_field(57,1,"optiecategorie","Optie-categorie");




# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(57);
if($cms_form[57]->filled) {
	if($cms_form[57]->input["hoort_bij_accommodatieinkoop"] and $cms_form[57]->input["optiecategorie"]>2) {
		$cms_form[57]->error("optiecategorie","niet van toepassing op de factuur van de accommodatie-leverancier");
	}
}

# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars;

	$volgorde=0;
	$db->query("SELECT bk_soort_id FROM bk_soort WHERE 1=1 ORDER BY volgorde;");
	while($db->next_record()) {
		$volgorde=$volgorde+10;
		$db2->query("UPDATE bk_soort SET volgorde='".$volgorde."' WHERE bk_soort_id='".$db->f("bk_soort_id")."';");
	}
}


# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>