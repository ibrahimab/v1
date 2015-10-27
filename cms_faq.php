<?php

$mustlogin=true;

include("admin/vars.php");

# wzt opvragen indien niet meegegeven met query_string
if(!$_GET["wzt"]) {
	if($_GET["56k0"]) {
		$db->query("SELECT wzt FROM faq WHERE faq_id='".addslashes($_GET["56k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}

$cms->settings[56]["list"]["show_icon"]=false;
$cms->settings[56]["list"]["edit_icon"]=true;
$cms->settings[56]["list"]["delete_icon"]=true;

// nieuwe waarde volgorde bepalen
$db->query("SELECT MAX(volgorde) AS volgorde FROM faq WHERE wzt='".intval($_GET["wzt"])."' ORDER BY volgorde;");
if($db->next_record()) {
	$volgorde = $db->f("volgorde");
}
$volgorde=$volgorde+10;

// ksort($vars["websites_actief"]);

if($_GET["wzt"]==2) {

	#zomer websites
	$te_tonen_websites = array("I"=>"Italissima.nl", "K"=>"Italissima.be", "Z"=>"Zomerhuisje.nl", "H"=>"Italyhomes.eu (Engelstalig)");
} else {

	# winter websites
	$te_tonen_websites = array("C"=>"Chalet.nl", "E"=>"Chalet.eu (Engelstalig)", "B"=>"Chalet.be", "D"=>"Chaletonline.de (Duitstalig) ", "T"=>"Chalettour.nl", "V"=>"Chalets in Vallandry (.nl)","X"=>"Venturasol Wintersport","Y"=>"Venturasol Vacances", "Q"=>"Chalets in Vallandry (.com)");
}

# Database db_field($counter,$type,$id,$field="",$options="")

$cms->db[56]["where"]="wzt='".intval($_GET["wzt"])."'";
$cms->db[56]["set"]="wzt='".intval($_GET["wzt"])."'";

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
	$db->query("SELECT faq_id FROM faq WHERE wzt='".intval($_GET["wzt"])."' ORDER BY volgorde;");
	while($db->next_record()) {
		$volgorde=$volgorde+10;
		$db2->query("UPDATE faq SET volgorde='".$volgorde."' WHERE faq_id='".$db->f("faq_id")."';");
	}
}


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>
