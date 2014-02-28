<?php


# altijd zomer
$_GET["wzt"]=2;

$mustlogin=true;
$vars["types_in_vars"]=true;
$vars["types_in_vars_wzt_splitsen"]=true;

if($_GET["websites"]=="I,K") {
	$where_website="(websites LIKE '%I%' OR	websites LIKE '%K%')";
	$where_website_acc="(a.websites LIKE '%I%' OR a.websites LIKE '%K%')";
} else {
	$where_website="websites LIKE '%".addslashes($_GET["websites"])."%'";
	$where_website_acc="a.websites LIKE '%".addslashes($_GET["websites"])."%'";
}


if($_GET["websites"]=="I,K" or $_GET["websites"]=="H") {
	# Italissima: alleen Italiaans accommodaties tonen
	$vars["types_in_vars_andquery"]=" AND ".$where_website_acc;
}
include("admin/vars.php");


#
# Database-declaratie
#

$cms->db[38]["where"]="websites='".addslashes($_GET["websites"])."'";
$cms->db[38]["set"]="website='".addslashes($_GET["websites"])."'";


# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(38,"yesno","tonen");
$cms->db_field(38,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(38,"text","internenaam");
$cms->db_field(38,"integer","volgorde");
$cms->db_field(38,"text","regel1");
$cms->db_field(38,"text","regel2");
$cms->db_field(38,"text","regel3");
$cms->db_field(38,"select","type_id","",array("selection"=>$vars["alletypes"][$_GET["wzt"]]));
$cms->db_field(38,"date","begindatum");
$cms->db_field(38,"date","einddatum");
$cms->db_field(38,"yesno","hoofdpagina");
$cms->db_field(38,"yesno","bestemmingen");
$cms->db_field(38,"yesno","themaoverzicht");
$cms->db_field(38,"yesno","aanbiedingenpagina");
if($_GET["websites"]=="Z") {
	$cms->db_field(38,"select","thema_id","",array("othertable"=>"36","otherkeyfield"=>"thema_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
	$cms->db_field(38,"select","land_id","",array("othertable"=>"6","otherkeyfield"=>"land_id","otherfield"=>"naam"));
	$cms->db_field(38,"select","skigebied_id","",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
	$cms->db_field(38,"select","plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));

} else {
	$cms->db_field(38,"select","skigebied_id","",array("othertable"=>"5","otherkeyfield"=>"skigebied_id","otherfield"=>"naam","otherwhere"=>"skigebied_id IN (SELECT DISTINCT skigebied_id FROM view_accommodatie WHERE ".$where_website.")"));
	$cms->db_field(38,"select","plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>$where_website));
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
$cms->list_sort[38]=array("volgorde","begindatum","einddatum","type_id","regel1","regel2","internenaam");
$cms->list_field(38,"internenaam","Interne naam");
$cms->list_field(38,"type_id","Accommodatie");
#$cms->list_field(38,"regel1","Regel 1");
#$cms->list_field(38,"regel2","Regel 2");
$cms->list_field(38,"tonen","Tonen");
$cms->list_field(38,"volgorde","Volgorde");
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

$cms->edit_field(38,0,"internenaam","Interne omschrijving van dit blok");
$cms->edit_field(38,0,"htmlrow","<hr>");

$cms->edit_field(38,1,"type_id","Accommodatie");
$cms->edit_field(38,1,"regel1","Regel 1","","",array("info"=>"Vul hier de regio in waar de accommodatie gelegen is."));
$cms->edit_field(38,1,"regel2","Regel 2","","",array("info"=>"Vul hier een pakkende zin ('trigger') in."));
$cms->edit_field(38,1,"regel3","Regel 3","","",array("info"=>"Vul hier de korting in die de klant krijgt (bijv. -15% op weekverblijf)"));
$cms->edit_field(38,0,"htmlrow","<hr><i>Wanneer moet dit blok getoond worden? (velden zijn niet verplicht)</i>");
$cms->edit_field(38,0,"begindatum","Toon vanaf","","",array("calendar"=>true));
$cms->edit_field(38,0,"einddatum","Tot en met","","",array("calendar"=>true));
$cms->edit_field(38,0,"volgorde","Volgorde","","",array("info"=>"Deze volgorde wordt alleen gebruikt bij de aanbiedingenpagina. Bij alle andere pagina's worden de blokken willekeurig getoond."));
$cms->edit_field(38,0,"htmlrow","<hr><b>Toon dit blok op de volgende pagina's</b>");
$cms->edit_field(38,0,"hoofdpagina","Hoofdpagina");
$cms->edit_field(38,0,"bestemmingen","Bestemmingen");
$cms->edit_field(38,0,"aanbiedingenpagina","Aanbiedingenpagina");
if($_GET["websites"]=="Z") {
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



# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars;

	$volgorde=0;
	$db->query("SELECT blokaccommodatie_id FROM blokaccommodatie WHERE websites='".addslashes($_GET["websites"])."' ORDER BY volgorde;");
	while($db->next_record()) {
		$volgorde=$volgorde+10;
		$db2->query("UPDATE blokaccommodatie SET volgorde='".$volgorde."' WHERE blokaccommodatie_id='".$db->f("blokaccommodatie_id")."';");
	}
}


# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>