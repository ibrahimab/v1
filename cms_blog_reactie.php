<?php

$mustlogin=true;

include("admin/vars.php");


$cms->settings[47]["list"]["show_icon"]=false;
$cms->settings[47]["list"]["edit_icon"]=true;
$cms->settings[47]["list"]["delete_icon"]=true;
$cms->settings[47]["list"]["add_link"]=true;
$cms->settings[47]["list"]["delete_checkbox"]=true;

#$cms->db[47]["where"]="blog_id='".addslashes($_GET["44k0"])."'";
#$cms->db[47]["set"]="blog_id='".addslashes($_GET["44k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(47,"yesno","actief");
$cms->db_field(47,"datetime","adddatetime");
$cms->db_field(47,"text","naam");
$cms->db_field(47,"email","email");
$cms->db_field(47,"textarea","inhoud");

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[47]=array("adddatetime");
$cms->list_field(47,"adddatetime","Datum/tijd");
$cms->list_field(47,"naam","Naam");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(47,1,"actief","Tonen op de website");
$cms->edit_field(47,1,"naam");
$cms->edit_field(47,0,"email","E-mail");
$cms->edit_field(47,1,"inhoud","Reactie");


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>