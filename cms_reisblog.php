<?php

$mustlogin=true;

include("admin/vars.php");

$cms->settings[55]["list"]["show_icon"]=false;
$cms->settings[55]["list"]["edit_icon"]=true;
$cms->settings[55]["list"]["delete_icon"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(55,"yesno","actief");
$cms->db_field(55,"text","titel");
$cms->db_field(55,"text","titel_rechts");
$cms->db_field(55,"integer","titel_rechts_volgorde");
$cms->db_field(55,"textarea","inleiding");
$cms->db_field(55,"textarea","inhoud");
$cms->db_field(55,"textarea","inhoud_html");
$cms->db_field(55,"date","plaatsingsdatum");
$cms->db_field(55,"picture","afbeelding_tn","",array("savelocation"=>"pic/cms/reisblog_tn/","filetype"=>"jpg","multiple"=>false));
$cms->db_field(55,"picture","afbeelding","",array("savelocation"=>"pic/cms/reisblog/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(55,"picture","afbeelding_rechts","",array("savelocation"=>"pic/cms/reisblog_rechts/","filetype"=>"jpg","multiple"=>true));



# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[55]=array("titel_rechts_volgorde");
#$cms->list_sort_desc[55]=true;
$cms->list_field(55,"titel_rechts_volgorde","Volgorde rechts");
$cms->list_field(55,"titel_rechts","Titel rechts");
$cms->list_field(55,"actief","Tonen");
$cms->list_field(55,"plaatsingsdatum","Tonen vanaf",array("date_format"=>"D MAAND JJJJ"));

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(55,0,"actief","Tonen op de website",array("selection"=>true));
$cms->edit_field(55,1,"plaatsingsdatum","Tonen vanaf",array("time"=>time()),"",array("calendar"=>true));
$cms->edit_field(55,1,"titel");
$cms->edit_field(55,1,"titel_rechts","Titel rechts");
$cms->edit_field(55,1,"titel_rechts_volgorde","Titel rechts volgorde");
$cms->edit_field(55,1,"inleiding");
$cms->edit_field(55,1,"inhoud","Inhoud","","",array("rows"=>60,"info"=>$vars["wysiwyg_info"]));
if($login->userlevel>=10) {
	$cms->edit_field(55,0,"inhoud_html","Inhoud html","","",array("rows"=>30));
}

$cms->edit_field(55,0,"htmlrow","<hr><b>Afbeeldingen</b>");

$cms->edit_field(55,1,"afbeelding_tn","Thumbnail (verhouding 1,5 : 1)","",array("autoresize"=>true,"autoresize_cut"=>true,"img_width"=>"148","img_maxheight"=>"99"));
$cms->edit_field(55,0,"afbeelding","Afbeeldingen voor in artikel","",array("autoresize"=>false,"number_of_uploadbuttons"=>6));
$cms->edit_field(55,0,"afbeelding_rechts","Afbeeldingen rechts","",array("autoresize"=>false,"number_of_uploadbuttons"=>6));



# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(55);
if($cms_form[55]->filled) {

}

# Controle op delete-opdracht
if($_GET["delete"]==55 and $_GET["55k0"]) {

}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>