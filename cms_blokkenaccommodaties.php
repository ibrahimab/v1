<?php


# altijd zomer
$_GET["wzt"]=2;

$mustlogin=true;
$vars["types_in_vars"]=true;
$vars["types_in_vars_wzt_splitsen"]=true;
if($_GET["wst"]==7) {
	# Italissima: alleen Italiaans accommodaties tonen
	$vars["types_in_vars_andquery"]=" AND p.land_id=5";
}
include("admin/vars.php");

# wst opvragen indien niet meegegeven met query_string
if(!$_GET["wst"]) {
	if($_GET["38k0"]) {
		$db->query("SELECT websitetype FROM blokaccommodatie WHERE blokaccommodatie_id='".addslashes($_GET["38k0"])."';");
		if($db->next_record()) {
			$_GET["wst"]=$db->f("websitetype");
		}
	} else {
		$_GET["wst"]=3;
	}
}

#
# Database-declaratie
#

$cms->db[38]["where"]="websitetype='".addslashes($_GET["wst"])."'";
$cms->db[38]["set"]="websitetype='".addslashes($_GET["wst"])."'";


# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(38,"yesno","tonen");
$cms->db_field(38,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(38,"text","internenaam");
$cms->db_field(38,"text","regel1");
$cms->db_field(38,"text","regel2");
$cms->db_field(38,"select","type_id","",array("selection"=>$vars["alletypes"][$_GET["wzt"]]));
$cms->db_field(38,"date","begindatum");
$cms->db_field(38,"date","einddatum");
$cms->db_field(38,"yesno","hoofdpagina");
$cms->db_field(38,"yesno","bestemmingen");
$cms->db_field(38,"yesno","themaoverzicht");
if($_GET["wst"]==7) {
	$cms->db_field(38,"select","skigebied_id","",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>"skigebied_id IN (SELECT DISTINCT skigebied_id FROM view_accommodatie WHERE land_id=5)"));
	$cms->db_field(38,"select","plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>"land_id=5"));
} else {
	$cms->db_field(38,"select","thema_id","",array("othertable"=>"36","otherkeyfield"=>"thema_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
	$cms->db_field(38,"select","land_id","",array("othertable"=>"6","otherkeyfield"=>"land_id","otherfield"=>"naam"));
	$cms->db_field(38,"select","skigebied_id","",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
	$cms->db_field(38,"select","plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
}

#
# List
#
# Te tonen icons/links bij list
$cms->settings[38]["list"]["show_icon"]=false;
$cms->settings[38]["list"]["edit_icon"]=true;
$cms->settings[38]["list"]["delete_icon"]=true;
$cms->settings[38]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[38]=array("begindatum","einddatum","type_id","regel1","regel2","internenaam");
$cms->list_field(38,"type_id","Accommodatie");
$cms->list_field(38,"regel1","Regel 1");
$cms->list_field(38,"regel2","Regel 2");
$cms->list_field(38,"internenaam","Intern");
$cms->list_field(38,"tonen","Tonen");
$cms->list_field(38,"begindatum","Begindatum",array("date_format"=>"DAG D MAAND JJJJ"));
$cms->list_field(38,"einddatum","Einddatum",array("date_format"=>"DAG D MAAND JJJJ"));

# Controle op delete-opdracht
if($_GET["delete"]==38 and $_GET["38k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(38)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(38,0,"tonen","Tonen op de website",array("selection"=>true));
#$cms->edit_field(38,0,"websites","Websites",array("selection"=>($_GET["wzt"]==1 ? "B,C,E,T,W" : "N,O,S,Z")),"",array("one_per_line"=>true));

$cms->edit_field(38,1,"type_id","Accommodatie");
$cms->edit_field(38,1,"regel1","Regel 1");
$cms->edit_field(38,0,"regel2","Regel 2");
$cms->edit_field(38,0,"internenaam","Interne omschrijving van dit blok");
$cms->edit_field(38,0,"begindatum","Begindatum","","",array("calendar"=>true));
$cms->edit_field(38,0,"einddatum","Einddatum","","",array("calendar"=>true));
$cms->edit_field(38,0,"htmlrow","<hr><b>Toon dit blok op de volgende pagina's</b>");
$cms->edit_field(38,0,"hoofdpagina","Hoofdpagina");
$cms->edit_field(38,0,"bestemmingen","Bestemmingen");
if($_GET["wst"]<>7) {
	$cms->edit_field(38,0,"themaoverzicht","Thema-overzicht");
	$cms->edit_field(38,0,"thema_id","Thema");
	$cms->edit_field(38,0,"land_id","Land");
}
$cms->edit_field(38,0,"skigebied_id","Regio");
$cms->edit_field(38,0,"plaats_id","Plaats");

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(38);
if($cms_form[38]->filled) {

}
# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>