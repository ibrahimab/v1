<?php

$mustlogin=true;
include("admin/vars.php");

# wzt opvragen indien niet meegegeven met query_string
if(!$_GET["wzt"]) {
	if($_GET["37k0"]) {
		$db->query("SELECT wzt, italissima FROM blokhoofdpagina WHERE blokhoofdpagina_id='".addslashes($_GET["37k0"])."';");
		if($db->next_record()) {
			if($db->f("italissima")) {
				$_GET["wzt"]=3;
			} else {
				$_GET["wzt"]=$db->f("wzt");
			}
		}
	} else {
		$_GET["wzt"]=1;
	}
}


#
# Database-declaratie
#

if($_GET["wzt"]==3) {
	# Italissima
	$cms->db[37]["where"]="wzt='2' AND italissima=1";
	$cms->db[37]["set"]="wzt='2', italissima=1";
} else {
	# Winter + Zomerhuisje
	$cms->db[37]["where"]="wzt='".addslashes($_GET["wzt"])."'";
	$cms->db[37]["set"]="wzt='".addslashes($_GET["wzt"])."'";
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(37,"yesno","tonen");
$cms->db_field(37,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(37,"text","link");
$cms->db_field(37,"text","titel");
$cms->db_field(37,"text","titel_en");
$cms->db_field(37,"text","omschrijving");
$cms->db_field(37,"text","omschrijving_en");
$cms->db_field(37,"integer","volgorde");
$cms->db_field(37,"date","begindatum");
$cms->db_field(37,"date","einddatum");
$cms->db_field(37,"picture","picgroot","",array("savelocation"=>"pic/cms/blokkenhoofdpagina/","filetype"=>"jpg"));
#$cms->db_field(37,"picture","picklein","",array("savelocation"=>"pic/cms/blokkenhoofdpagina_tn/","filetype"=>"jpg"));

#
# List
#
# Te tonen icons/links bij list
$cms->settings[37]["list"]["show_icon"]=false;
$cms->settings[37]["list"]["edit_icon"]=true;
$cms->settings[37]["list"]["delete_icon"]=true;
$cms->settings[37]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[37]=array("volgorde","begindatum","titel");
$cms->list_field(37,"titel","Koptekst");
$cms->list_field(37,"tonen","Tonen");
$cms->list_field(37,"volgorde","Volgorde");
$cms->list_field(37,"begindatum","Begindatum",array("date_format"=>"DAG D MAAND JJJJ"));
$cms->list_field(37,"einddatum","Einddatum",array("date_format"=>"DAG D MAAND JJJJ"));

# Controle op delete-opdracht
if($_GET["delete"]==37 and $_GET["37k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(37)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(37,0,"tonen","Tonen op de website",array("selection"=>true));
#$cms->edit_field(37,0,"websites","Websites",array("selection"=>($_GET["wzt"]==1 ? "B,C,E,T,W" : "N,O,S,Z")),"",array("one_per_line"=>true));

if($_GET["wzt"]==1) {
	$cms->edit_field(37,1,"titel","Koptekst");
	$cms->edit_field(37,1,"titel_en","Koptekst (Engels)");
	$cms->edit_field(37,1,"omschrijving","Omschrijving");
	$cms->edit_field(37,1,"omschrijving_en","Omschrijving (Engels)");
} elseif($_GET["wzt"]==3) {
	$cms->edit_field(37,1,"titel","Titel");
	$cms->edit_field(37,1,"omschrijving","Omschrijving");
} else {
	$cms->edit_field(37,1,"titel","Titel");
#	$cms->edit_field(37,1,"titel_en","Titel (Engels)");
}

if($_GET["wzt"]==1) {
	$cms->edit_field(37,1,"link","Link (zonder 'http://www.chalet.nl')");
} elseif($_GET["wzt"]==3) {
	$cms->edit_field(37,1,"link","Link (zonder 'http://www.italissima.nl')");
} else {
	$cms->edit_field(37,1,"link","Link (zonder 'http://www.zomerhuisje.nl')");
}
$cms->edit_field(37,1,"volgorde","Volgorde");
$cms->edit_field(37,0,"begindatum","Begindatum","","",array("calendar"=>true));
$cms->edit_field(37,0,"einddatum","Einddatum","","",array("calendar"=>true));
$cms->edit_field(37,0,"htmlrow","<hr><b>Afbeeldingen</b>");
if($_GET["wzt"]==1) {
	$cms->edit_field(37,1,"picgroot","Afbeelding","",array("img_width"=>"500","img_height"=>"278"));
#	$cms->edit_field(37,1,"picklein","Kleine afbeelding","",array("img_width"=>"75","img_height"=>"56"));
} elseif($_GET["wzt"]==3) {
	$cms->edit_field(37,1,"picgroot","Afbeelding","",array("img_width"=>"360","img_height"=>"270"));
} else {
	$cms->edit_field(37,1,"picgroot","Afbeelding","",array("img_width"=>"362","img_height"=>"300"));
}

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(37);
if($cms_form[37]->filled) {
	if($cms_form[37]->input["link"]) {
		if(ereg("http",$cms_form[37]->input["link"])) {
			$cms_form[37]->error("link","laat 'http' weg");
		}
		if(substr($cms_form[37]->input["link"],0,1)<>"/") {
			$cms_form[37]->error("link","moet beginnen met een slash (/)");
		}
	}
}


# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars,$cms;
	
	$volgorde=0;
	$db->query("SELECT blokhoofdpagina_id FROM blokhoofdpagina WHERE ".$cms->db[37]["where"]." ORDER BY volgorde;");
	while($db->next_record()) {
		$volgorde=$volgorde+10;
		$db2->query("UPDATE blokhoofdpagina SET volgorde='".$volgorde."' WHERE blokhoofdpagina_id='".$db->f("blokhoofdpagina_id")."';");
	}
}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>