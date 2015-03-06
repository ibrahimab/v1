<?php

$mustlogin=true;
include("admin/vars.php");

# wzt opvragen indien niet meegegeven met query_string
if(!$_GET["wzt"]) {
	if($_GET["59k0"]) {
		$db->query("SELECT wzt FROM homepageblok WHERE homepageblok_id='".intval($_GET["59k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}


#
# Database-declaratie
#

# Winter + Zomerhuisje
$cms->db[59]["where"]="wzt='".intval($_GET["wzt"])."'";
$cms->db[59]["set"]="wzt='".intval($_GET["wzt"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(59,"yesno","tonen");

if($_GET["wzt"]==1) {
	unset($vars["websites_wzt"][1]["W"]);
} elseif($_GET["wzt"]==2) {
	unset($vars["websites_wzt"][2]["N"]);
	unset($vars["websites_wzt"][2]["V"]);
	unset($vars["websites_wzt"][2]["S"]);
	unset($vars["websites_wzt"][2]["O"]);
	unset($vars["websites_wzt"][2]["Q"]);
}
$cms->db_field(59,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(59,"radio","positie","",array("selection"=>array(1=>"links (groot)", 2=>"rechts (kleiner)")));

$cms->db_field(59,"text","link");
$cms->db_field(59,"text","titel");
$cms->db_field(59,"text","titel_en");
$cms->db_field(59,"text","titel_de");
$cms->db_field(59,"text","button");
$cms->db_field(59,"text","button_en");
$cms->db_field(59,"text","button_de");
$cms->db_field(59,"integer","volgorde");
$cms->db_field(59,"date","begindatum");
$cms->db_field(59,"date","einddatum");
$cms->db_field(59,"picture","picgroot","",array("savelocation"=>"pic/cms/homepageblokken/","filetype"=>"jpg"));

#
# List
#
# Te tonen icons/links bij list
$cms->settings[59]["list"]["show_icon"]=false;
$cms->settings[59]["list"]["edit_icon"]=true;
$cms->settings[59]["list"]["delete_icon"]=true;
$cms->settings[59]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[59]=array("volgorde","begindatum","titel");
$cms->list_field(59,"positie","Positie");
$cms->list_field(59,"titel","Titel");
$cms->list_field(59,"tonen","Tonen");
$cms->list_field(59,"websites","Websites");
$cms->list_field(59,"volgorde","Volgorde");
$cms->list_field(59,"begindatum","Begindatum",array("date_format"=>"DAG D MAAND JJJJ"));
$cms->list_field(59,"einddatum","Einddatum",array("date_format"=>"DAG D MAAND JJJJ"));

# Controle op delete-opdracht
if($_GET["delete"]==59 and $_GET["59k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(59)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(59,0,"tonen","Tonen op de website",array("selection"=>true));
$cms->edit_field(59,0,"websites","Websites",array("selection"=>""),"",array("one_per_line"=>true));

$cms->edit_field(59,1,"positie","Positie van de afbeelding", "", array("noedit"=>($_GET["edit"] ? true : false)), array("one_per_line"=>true));
$cms->edit_field(59,0,"htmlrow","<br /><i>Bij de titels kun je een afwijkend lettertype gebruiken: [font]tekst met ander lettertype[/font]</i>");

$cms->edit_field(59,0,"titel","Titel");
$cms->edit_field(59,0,"titel_en","Titel (Engels)");
$cms->edit_field(59,0,"titel_de","Titel (Duits)");
$cms->edit_field(59,0,"button","Button");
$cms->edit_field(59,0,"button_en","Button (Engels)");
$cms->edit_field(59,0,"button_de","Button (Duits)");

$advies = array("add_html_after_field"=>"<div style=\"margin-top:4px;margin-bottom:10px;font-size:0.9em;color:blue;\">Tip: test of de URL correct werkt op alle hierboven aangevinkte websites.</div>");

if($_GET["wzt"]==1) {
	$cms->edit_field(59,1,"link","Link (zonder 'https://www.chalet.nl')", "", "", $advies);
} elseif($_GET["wzt"]==2) {
	$cms->edit_field(59,1,"link","Link (zonder 'https://www.italissima.nl')", "", "", $advies);
}
$cms->edit_field(59,1,"volgorde","Volgorde");
$cms->edit_field(59,0,"begindatum","Begindatum","","",array("calendar"=>true));
$cms->edit_field(59,0,"einddatum","Einddatum","","",array("calendar"=>true));
$cms->edit_field(59,0,"htmlrow","<hr><b>Afbeelding</b><br /><br /><i>Links (groot) = 562 x 428 pixels<br />Rechts (kleiner) = 562 x 196 pixels</i>");
if($_POST["input"]["positie"]==1) {
	# large left
	$cms->edit_field(59,1,"picgroot","Afbeelding","",array("img_width"=>"562","img_height"=>"428"));
} elseif($_POST["input"]["positie"]==2) {
	# small right
	$cms->edit_field(59,1,"picgroot","Afbeelding","",array("img_width"=>"562","img_height"=>"196"));
} else {
	$cms->edit_field(59,1,"picgroot","Afbeelding","",array("img_width"=>"562","img_height"=>"196", "showfiletype"=>false), array("hide_imginfo"=>true));
}

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(59);
if($cms_form[59]->filled) {
	if($cms_form[59]->input["link"]) {
		if(ereg("http",$cms_form[59]->input["link"])) {
			$cms_form[59]->error("link","laat 'http' weg");
		}
		if(substr($cms_form[59]->input["link"],0,1)<>"/") {
			$cms_form[59]->error("link","moet beginnen met een slash (/)");
		}
	}

	# Verplichte Engelse velden bij Chalet.eu / Italyhomes.eu aangevinkt:
	if(preg_match("/E/",$cms_form[59]->input["websites"]) or preg_match("/H/",$cms_form[59]->input["websites"])) {
		if($cms_form[59]->input["titel"] and !$cms_form[59]->input["titel_en"]) $cms_form[59]->error("titel_en","obl");
		if($cms_form[59]->input["button"] and !$cms_form[59]->input["button_en"]) $cms_form[59]->error("button_en","obl");
	}

	# Verplichte Duitse velden bij Chaletonline.de aangevinkt:
	if(preg_match("/D/",$cms_form[59]->input["websites"])) {
		if($cms_form[59]->input["titel"] and !$cms_form[59]->input["titel_de"]) $cms_form[59]->error("titel_de","obl");
		if($cms_form[59]->input["button"] and !$cms_form[59]->input["button_de"]) $cms_form[59]->error("button_de","obl");
	}

}


# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars,$cms;

	$volgorde=0;
	$db->query("SELECT homepageblok_id FROM homepageblok WHERE ".$cms->db[59]["where"]." ORDER BY volgorde;");
	while($db->next_record()) {
		$volgorde=$volgorde+10;
		$db2->query("UPDATE homepageblok SET volgorde='".$volgorde."' WHERE homepageblok_id='".$db->f("homepageblok_id")."';");
	}
}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>