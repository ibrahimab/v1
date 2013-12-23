<?php

$mustlogin=true;

include("admin/vars.php");

$cms->settings[56]["list"]["show_icon"]=false;
$cms->settings[56]["list"]["edit_icon"]=true;
$cms->settings[56]["list"]["delete_icon"]=true;

// nieuwe waarde volgorde bepalen
$db->query("SELECT MAX(volgorde) AS volgorde FROM faq WHERE 1=1 ORDER BY volgorde;");
if($db->next_record()) {
	$volgorde = $db->f("volgorde");
}
$volgorde=$volgorde+10;

// ksort($vars["websites_actief"]);

$te_tonen_websites = array("C"=>"Chalet.nl", "B"=>"Chalet.be", "T"=>"Chalettour.nl","V"=>"Chalets in Vallandry (.nl)","X"=>"Venturasol","Y"=>"Venturasol-partner");


# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(56,"checkbox","websites","",array("selection"=>$te_tonen_websites));
$cms->db_field(56,"integer","volgorde");
$cms->db_field(56,"text","interne_naam");
$cms->db_field(56,"textarea","vraag");
$cms->db_field(56,"textarea","antwoord");



# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[56]=array("volgorde");
$cms->list_field(56,"volgorde","Volgorde");
$cms->list_field(56,"interne_naam","Interne omschrijving");
// $cms->list_field(56,"vraag","Vraag");
$cms->list_field(56,"websites","Websites");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(56,0,"websites","Toon deze vraag op", "", "", array("one_per_line"=>true));
$cms->edit_field(56,1,"volgorde","Volgorde", array("text"=>$volgorde));
$cms->edit_field(56,1,"interne_naam", "Korte interne omschrijving");
$cms->edit_field(56,1,"vraag","Vraag");
$cms->edit_field(56,1,"antwoord","Antwoord");



# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(56);
if($cms_form[56]->filled) {

}

# Controle op delete-opdracht
if($_GET["delete"]==56 and $_GET["56k0"]) {

}

# functie na opslaan form
function form_before_goto($form) {
	$db = new DB_sql;
	$db2 =new DB_sql;
	global $login, $vars;

	$volgorde=0;
	$db->query("SELECT faq_id FROM faq WHERE 1=1 ORDER BY volgorde;");
	while($db->next_record()) {
		$volgorde=$volgorde+10;
		$db2->query("UPDATE faq SET volgorde='".$volgorde."' WHERE faq_id='".$db->f("faq_id")."';");
	}
}


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>