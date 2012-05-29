<?php

$mustlogin=true;

include("admin/vars.php");

$cms->settings[7]["list"]["show_icon"]=true;
$cms->settings[7]["list"]["edit_icon"]=true;
$cms->settings[7]["list"]["delete_icon"]=true;
$cms->settings[7]["list"]["add_link"]=true;

# actieve seizoenen laten
unset($inquery);
$db->query("SELECT seizoen_id FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".(time()-8640000)."';");
while($db->next_record()) {
	$inquery.=",".$db->f("seizoen_id");
}
if(!$inquery) $inquery=",0";

# Where-statement
$cms->db[7]["where"]="seizoen_id IN (".substr($inquery,1).")";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(7,"text","naam");
#$cms->db_field(7,"textarea","omschrijving");
$cms->db_field(7,"textarea","toelichting");
$cms->db_field(7,"textarea","toelichting_en");
$cms->db_field(7,"select","seizoen_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"UNIX_TIMESTAMP(eind)>'".(time()-8640000)."'"));
#$cms->db_field(7,"select","soort","",array("selection"=>$vars["vertrekdagtypes_soorten"]));
$cms->db_field(7,"textarea","afwijking");

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[7]=array("seizoen_id","naam");
$cms->list_field(7,"naam","Naam");
$cms->list_field(7,"seizoen_id","Seizoen");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(7,1,"seizoen_id","Seizoen");
$cms->edit_field(7,1,"naam","Interne naam");
#$cms->edit_field(7,0,"omschrijving","Omschrijving (intern)");
$cms->edit_field(7,1,"toelichting","Toelichting");
$cms->edit_field(7,1,"toelichting_en","Toelichting (Engels)");
#$cms->edit_field(7,1,"soort","Soort");
$cms->edit_field(7,0,"htmlrow","<hr><b>Afwijkdata</b><br><br><i><br>Voorbeelden:<br><br><b>2512 +1</b> = vertrekdag zaterdag 25 december verschuift naar zondag 26 december<br><b>0202 -1</b> = vertrekdag zaterdag 1 februari verschuift naar vrijdag 1 februari<br><br>Meerdere afwijkingen scheiden door enters</i>");
$cms->edit_field(7,1,"afwijking","Afwijkdata");


$cms->show_name[7]="vertrekdagtypes";
$cms->show_mainfield[7]="naam";
$cms->show_field(7,"naam","Naam");
$cms->show_field(7,"seizoen_id","Seizoen");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(7);
if($cms_form[7]->filled) {
	if($cms_form[7]->input["afwijking"]) {
		$regels=split("\r\n",$cms_form[7]->input["afwijking"]);
		while(list($key,$value)=each($regels)) {
			if($value) {
				if(ereg("^([0-9][0-9])([0-9][0-9]) [+-][0-9]$",$value,$regs)) {
					if(intval($regs[1])>31) $afwijking_error=true;
					if(intval($regs[2])>12) $afwijking_error=true;
				} else {
					$afwijking_error=true;
				}
			}
		}
	}
	if($afwijking_error) $cms_form[7]->error("afwijking","onjuiste afwijking");
	
	
	if($cms_form[7]->input["naam"] and (eregi("^a:",$cms_form[7]->input["naam"]) or eregi("^a ",$cms_form[7]->input["naam"]))) {
		$cms_form[7]->error("naam","'A' is gereserveerd voor 'Geen afwijking op seizoensniveau'. Begin bij 'B'");
	}
}

# Controle op delete-opdracht
if($_GET["delete"]==7 and $_GET["7k0"]) {
	$db->query("SELECT accommodatie_id FROM accommodatie_seizoen WHERE vertrekdagtype_id='".addslashes($_GET["7k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(7,"Er zijn nog accommodatie-seizoenen gekoppeld");
	}
}

# End declaration
$cms->end_declaration();
$layout->display_all($cms->page_title);

?>