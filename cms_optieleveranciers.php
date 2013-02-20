<?php

$mustlogin=true;

include("admin/vars.php");

$cms->settings[24]["list"]["show_icon"]=true;
$cms->settings[24]["list"]["edit_icon"]=true;
$cms->settings[24]["list"]["delete_icon"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(24,"integer","optieleverancier_id");
$cms->db_field(24,"text","naam");
$cms->db_field(24,"yesno","plaatscode_van_toepassing");
$cms->db_field(24,"yesno","bestellijst_1_regel_per_persoon");


# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(24,"naam","Naam");
$cms->list_field(24,"optieleverancier_id","Intern volgnummer");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(24,1,"optieleverancier_id","Intern volgnummer","",array("noedit"=>true));
$cms->edit_field(24,1,"naam");
$cms->edit_field(24,1,"plaatscode_van_toepassing","Bij deze leverancier is een plaatscode van toepassing",array("selection"=>true));
$cms->edit_field(24,1,"bestellijst_1_regel_per_persoon","In bestellijst-CSV: 1 regel per deelnemer");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(24);
if($cms_form[24]->filled) {

}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[24]="leveranciergegevens";
$cms->show_mainfield[24]="naam";
$cms->show_field(24,"naam","Naam");
$cms->show_field(24,"optieleverancier_id","Intern volgnummer");

# Controle op delete-opdracht
if($_GET["delete"]==24 and $_GET["24k0"]) {
	$db->query("SELECT optie_groep_id FROM optie_groep WHERE optieleverancier_id='".addslashes($_GET["24k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(24,"Er zijn nog optie-groepen gekoppeld aan deze optieleverancier");
	}
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>