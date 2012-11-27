<?php

$mustlogin=true;

include("admin/vars.php");

if(!$login->has_priv("29")) {
	header("Location: cms.php");
	exit;
}

# Gekoppelde plaatsen laden
$db->query("SELECT ep.evenement_id, p.naam FROM plaats p, evenement_plaats ep WHERE ep.plaats_id=p.plaats_id ORDER BY ep.evenement_id, p.naam;");
while($db->next_record()) {
	if($gekoppelde_plaatsen[$db->f("evenement_id")]) $gekoppelde_plaatsen[$db->f("evenement_id")].=", ".$db->f("naam"); else $gekoppelde_plaatsen[$db->f("evenement_id")]=$db->f("naam");
}

$cms->settings[52]["list"]["show_icon"]=true;
$cms->settings[52]["list"]["edit_icon"]=false;
$cms->settings[52]["list"]["delete_icon"]=true;

# Nieuw record meteen openen na toevoegen
$cms->settings[52]["show"]["goto_new_record"]=true;

#$cms->db[52]["where"]="wzt='".addslashes($_GET["wzt"])."'";
#$cms->db[52]["set"]="wzt='".addslashes($_GET["wzt"])."'";

# Alleen zomersites
$temp_websites=$vars["websites_wzt_actief"][2];

# Geen ChaletsinVallandry
unset($temp_websites["V"]);
unset($temp_websites["Q"]);

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(52,"yesno","actief");
$cms->db_field(52,"checkbox","websites","",array("selection"=>$temp_websites));
$cms->db_field(52,"text","naam");
$cms->db_field(52,"textarea","omschrijving");
$cms->db_field(52,"date","begindatum");
$cms->db_field(52,"text","datum_in_tekst");
$cms->db_field(52,"picture","afbeelding","",array("savelocation"=>"pic/cms/evenement/","filetype"=>"jpg","multiple"=>false));
$cms->db_field(52,"select","gekoppelde_plaatsen","evenement_id",array("selection"=>$gekoppelde_plaatsen));


# List list_field($counter,$id,$title="",$options="",$layout="")

$cms->list_sort[52]=array("begindatum","naam");
$cms->list_sort_desc[52]=false;
$cms->list_field(52,"naam","Naam");
$cms->list_field(52,"gekoppelde_plaatsen","Plaats");
$cms->list_field(52,"begindatum","Begindatum",array("date_format"=>"D MAAND JJJJ"));
$cms->list_field(52,"websites","Websites");



# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(52,0,"actief","Tonen op de website",array("selection"=>true));
$cms->edit_field(52,1,"websites","Websites","","",array("one_per_line"=>true));
$cms->edit_field(52,1,"begindatum","Datum","","",array("calendar"=>true,"info"=>"Dit veld is bedoeld om de evenementen in chronologische volgorde te kunnen tonen. De waarde die je hier invult wordt niet getoond."));
$cms->edit_field(52,1,"datum_in_tekst","Datum (in tekst)");
$cms->edit_field(52,1,"naam","Naam");
$cms->edit_field(52,1,"omschrijving");
$cms->edit_field(52,0,"afbeelding","Afbeelding","",array("autoresize"=>true,"img_maxwidth"=>"300"));





# Show
$cms->show_name[52]="evenement";
$cms->show_mainfield[52]="naam";
$cms->show_field(52,"naam","Naam");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(52);
if($cms_form[52]->filled) {

}

# Na opslaan form de volgende actie uitvoeren
function form_before_goto($form) {
	global $db0,$login;

}

# Controle op delete-opdracht
if($_GET["delete"]==52 and $_GET["52k0"]) {

}


#
# Koppeling met evenement_plaats
#
$cms->settings[52]["connect"][]=53;
$cms->settings[53]["parent"]=52;

$cms->settings[53]["list"]["show_icon"]=false;
$cms->settings[53]["list"]["edit_icon"]=false;
$cms->settings[53]["list"]["delete_icon"]=true;
$cms->settings[53]["list"]["add_link"]=true;
$cms->settings[53]["list"]["delete_checkbox"]=true;

$cms->db[53]["where"]="evenement_id='".addslashes($_GET["52k0"])."'";
$cms->db[53]["set"]="evenement_id='".addslashes($_GET["52k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$db->query("SELECT DISTINCT plaats_id, plaats, land FROM view_accommodatie WHERE wzt=2 AND atonen=1 AND ttonen=1 AND archief=0 ORDER BY land, plaats;");
while($db->next_record()) {
	$plaatsen[$db->f("plaats_id")]=$db->f("land").", ".$db->f("plaats");
}

$cms->db_field(53,"select","plaats_id","",array("selection"=>$plaatsen));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[53]=array("plaats_id");
$cms->list_field(53,"plaats_id","Naam");



# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>