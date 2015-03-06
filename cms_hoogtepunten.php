<?php

$mustlogin=true;
$vars["types_in_vars"]=true;
$vars["types_in_vars_wzt_splitsen"]=true;
$vars["types_in_vars_andquery"] = " AND a.tonen=1 AND t.tonen=1";
include("admin/vars.php");

# wzt opvragen indien niet meegegeven met query_string
if(!$_GET["wzt"]) {
	if($_GET["58k0"]) {
		$db->query("SELECT wzt FROM hoogtepunt WHERE hoogtepunt_id='".intval($_GET["58k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}


if($_POST["add_type_id"]) {

	$type_id = $_POST["type_id"];
	$type_id = preg_replace("@[^0-9]@", "", $type_id);

	$db->query("SELECT type_id FROM view_accommodatie WHERE wzt='".intval($_GET["wzt"])."' AND type_id='".intval($type_id)."' AND atonen=1 AND ttonen=1;");
	if($db->num_rows()) {

		$db->query("INSERT INTO hoogtepunt SET type_id='".intval($type_id)."', wzt='".intval($_GET["wzt"])."', tonen=1");
		if($db->insert_id()) {
			header("Location: ".$vars["path"]."cms_hoogtepunten.php?edit=58&wzt=".intval($_GET["wzt"])."&58k0=".intval($db->insert_id()));
			exit;
		}

	} else {
		header("Location: ".$_SERVER["REQUEST_URI"]);
		exit;
	}
}

$db->query("SELECT hoogtepunt_id, volgorde FROM hoogtepunt;");
while($db->next_record()) {
	$volgorde_field[$db->f("hoogtepunt_id")] = ($db->f("volgorde") ? $db->f("volgorde") : "willekeurig");
}

#
# Database-declaratie
#

$cms->db[58]["where"]="wzt='".intval($_GET["wzt"])."'";
$cms->db[58]["set"]="wzt='".intval($_GET["wzt"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(58,"yesno","tonen");

if($_GET["wzt"]==1) {
	unset($vars["websites_wzt"][1]["W"]);
} elseif($_GET["wzt"]==2) {
	unset($vars["websites_wzt"][2]["N"]);
	unset($vars["websites_wzt"][2]["V"]);
	unset($vars["websites_wzt"][2]["S"]);
	unset($vars["websites_wzt"][2]["O"]);
	unset($vars["websites_wzt"][2]["Q"]);
}

$cms->db_field(58,"select","type_id","",array("selection"=>$vars["alletypes_zonderarchief"][$_GET["wzt"]]));
$cms->db_field(58,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(58,"select","volgorde_order","hoogtepunt_id",array("selection"=>$volgorde_field));
$cms->db_field(58,"integer","volgorde");
$cms->db_field(58,"date","begindatum");
$cms->db_field(58,"date","einddatum");

#
# List
#
# Te tonen icons/links bij list
$cms->settings[58]["list"]["show_icon"]=false;
$cms->settings[58]["list"]["edit_icon"]=true;
$cms->settings[58]["list"]["delete_icon"]=true;
$cms->settings[58]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[58]=array("volgorde_order","begindatum");
$cms->list_field(58,"type_id","Type");
$cms->list_field(58,"tonen","Tonen");
$cms->list_field(58,"websites","Websites");

$cms->list_field(58,"volgorde_order","Volgorde");
$cms->list_field(58,"begindatum","Begindatum",array("date_format"=>"DAG D MAAND JJJJ"));
$cms->list_field(58,"einddatum","Einddatum",array("date_format"=>"DAG D MAAND JJJJ"));

# Controle op delete-opdracht
if($_GET["delete"]==58 and $_GET["58k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(58)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(58,0,"tonen","Tonen op de website",array("selection"=>true));

$cms->edit_field(58,0,"websites","Websites",array(""),"",array("one_per_line"=>true));


$advies = array("add_html_after_field"=>"<div style=\"margin-top:4px;margin-bottom:10px;font-size:0.9em;color:blue;\">Tip: test of de URL correct werkt op alle hierboven aangevinkte websites.</div>");

$cms->edit_field(58,1,"type_id","Type");
$cms->edit_field(58,0,"volgorde","Volgorde");
$cms->edit_field(58,0,"begindatum","Begindatum","","",array("calendar"=>true));
$cms->edit_field(58,0,"einddatum","Einddatum","","",array("calendar"=>true));

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(58);
if($cms_form[58]->filled) {

	// check for websites
	if($cms_form[58]->input["type_id"] and $cms_form[58]->input["websites"]) {
		$websites = preg_split("@,@", $cms_form[58]->input["websites"]);
		foreach ($websites as $key => $value) {
			$db->query("SELECT type_id FROM type WHERE type_id='".intval($cms_form[58]->input["type_id"])."' AND websites LIKE '%".$value."%';");
			if(!$db->num_rows()) {
				$error_websites .= ", ".$vars["websites_wzt"][$_GET["wzt"]][$value];
			}
		}

		if($error_websites) {
			$cms_form[58]->error("websites","type niet aanwezig op: ".substr($error_websites, 2));
		}
	}

}


# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars,$cms;

	$volgorde=0;
	$db->query("SELECT hoogtepunt_id FROM hoogtepunt WHERE volgorde IS NOT NULL AND ".$cms->db[58]["where"]." ORDER BY volgorde;");
	while($db->next_record()) {
		$volgorde=$volgorde+10;
		$db2->query("UPDATE hoogtepunt SET volgorde='".$volgorde."' WHERE hoogtepunt_id='".$db->f("hoogtepunt_id")."';");
	}
}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>