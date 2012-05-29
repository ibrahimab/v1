<?php

$mustlogin=true;

include("admin/vars.php");

#
#
# Subleveranciers
#
#
$cms->settings[8]["connect"][]=42;
$cms->settings[42]["parent"]=8;

$cms->settings[42]["list"]["show_icon"]=true;
$cms->settings[42]["list"]["edit_icon"]=true;
$cms->settings[42]["list"]["delete_icon"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->settings[42]["prevalue"]["leverancier_id"]=$_GET["8k0"];
$cms->db[42]["where"]="leverancier_id='".addslashes($_GET["1k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(42,"text","naam");
$cms->db_field(42,"select","leverancier_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[42]=array("naam");
$cms->list_field(42,"naam","Naam");

$cms->edit_field(42,1,"naam","Naam");
$cms->edit_field(42,1,"leverancier_id","Bovenliggende leverancier");

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(42);
if($cms_form[42]->filled) {

}

# Controle op delete-opdracht
if($_GET["delete"]==42 and $_GET["42k0"]) {
	$db->query("SELECT type_id FROM type WHERE leverancier_sub_id='".addslashes($_GET["42k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(42,"Er zijn nog <a href=\"cms_types.php?2where=".urlencode("leverancier_sub_id=".$_GET["42k0"])."\">types</a> gekoppeld");
	}
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);


?>