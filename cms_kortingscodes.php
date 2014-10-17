<?php

#
#
#

$mustlogin=true;

include("admin/vars.php");

# Toegevoegde accommodaties opslaan
if($_POST["toevoegen_filled"]) {
	if($_POST["accommodaties"]) {
		$db->query("INSERT INTO kortingscode_accommodatie SET accommodatie_id='".addslashes($_POST["accommodaties"])."', kortingscode_id='".addslashes($_GET["29k0"])."';");
	}
	if($_POST["types"]) {
		$db->query("INSERT INTO kortingscode_type SET type_id='".addslashes($_POST["types"])."', kortingscode_id='".addslashes($_GET["29k0"])."';");
	}
	header("Location: cms_kortingscodes.php?".$_SERVER["QUERY_STRING"]);
	exit;
}


# status van eenmalige codes opslaan
if($_POST["status_filled"]) {
	@reset($_POST["status"]);
	while(list($key,$value)=@each($_POST["status"])) {
		$db->query("UPDATE kortingscode_eenmalig SET status='".addslashes($value)."', editdatetime=NOW() WHERE kortingscode_eenmalig_id='".addslashes($key)."';");
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}


# Aantal boekingen per code bepalen
$db->query("SELECT COUNT(boeking_id) AS aantal, kortingscode_id FROM boeking WHERE goedgekeurd=1 AND boekingsnummer<>'' AND kortingscode_id IS NOT NULL GROUP BY kortingscode_id;");
while($db->next_record()) {
	$aantal_boekingen[$db->f("kortingscode_id")]=$db->f("aantal");
}

$cms->settings[29]["list"]["show_icon"]=true;
$cms->settings[29]["list"]["edit_icon"]=true;
$cms->settings[29]["list"]["delete_icon"]=true;
if($_GET["t"]==2 or $_GET["t"]==3) {
	$cms->settings[29]["list"]["add_link"]=false;
}

$cms->settings[29]["show"]["goto_new_record"]=true;
$cms->settings[29]["show"]["goto_changed_record"]=true;
#$cms->settings[29]["edit"]["top_submit_button"]=true;

if($_GET["t"]==1) {
	$cms->db[29]["where"]="((UNIX_TIMESTAMP(einddatum)>='".mktime(0,0,0,date("m"),date("d"),date("Y"))."' OR einddatum IS NULL) AND archief=0)";
} elseif($_GET["t"]==2) {
	$cms->db[29]["where"]="(UNIX_TIMESTAMP(einddatum)<'".mktime(0,0,0,date("m"),date("d"),date("Y"))."' AND archief=0)";
} elseif($_GET["t"]==3) {
	$cms->db[29]["where"]="archief=1";
}

#$cms->db[29]["set"]="wzt='".addslashes($_GET["wzt"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(29,"select","aantal_boekingen","kortingscode_id",array("selection"=>$aantal_boekingen));
$cms->db_field(29,"checkbox","websites","",array("selection"=>$vars["websites"]));
$cms->db_field(29,"text","omschrijving");
$cms->db_field(29,"text","code");
$cms->db_field(29,"currency","korting_euro");
$cms->db_field(29,"float","korting_percentage");
$cms->db_field(29,"currency","korting_maximaal");
$cms->db_field(29,"date","einddatum");
$cms->db_field(29,"text","omschrijving");
$cms->db_field(29,"text","actietekst");
$cms->db_field(29,"yesno","archief");

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[29]=array("omschrijving","einddatum");
$cms->list_field(29,"omschrijving","Omschrijving");
$cms->list_field(29,"code","Code");
$cms->list_field(29,"einddatum","",array("date_format"=>"DD-MM-JJJJ"));
$cms->list_field(29,"aantal_boekingen","Boekingen");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(29,1,"archief","Archief");
$cms->edit_field(29,1,"websites","Geldig voor","","",array("one_per_line"=>true));
$cms->edit_field(29,1,"omschrijving","Omschrijving (intern)");
$cms->edit_field(29,1,"code","Code","","",array("info"=>"Vul hier de gewenste code in, of vul AUTOMATISCH in om het systeem meerdere (eenmalig te gebruiken) codes te laten genereren.\n\nVul ELKEBOEKING in om de korting op elke boeking van toepassing te laten zijn. De klant hoeft daarbij geen kortingscode in te voeren."));
$cms->edit_field(29,0,"einddatum","Geldig tot en met","","",array("calendar"=>true));
$cms->edit_field(29,0,"htmlrow","<hr><b>Korting</b>");
$cms->edit_field(29,0,"korting_euro","Korting in euro's");
$cms->edit_field(29,0,"korting_percentage","Kortingspercentage");
$cms->edit_field(29,0,"korting_maximaal","Maximale korting (bij kortingspercentage)");
$cms->edit_field(29,0,"htmlrow","<hr><b>Andere actie</b>");
$cms->edit_field(29,0,"actietekst","Tekst voor op factuur","","",array("info"=>"Te gebruiken in plaats van een korting, of te gebruiken in combinatie met een korting. In het laatste geval wordt de korting toegepast met deze tekst als omschrijving."));



# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(29);
if($cms_form[29]->filled) {
	# kiezen tussen korting_euro en korting_percentage
	if($cms_form[29]->input["korting_euro"] and $cms_form[29]->input["korting_percentage"]) {
		$cms_form[29]->error("korting_percentage","kies voor euro's of percentage (niet allebei)");
	}

	if($cms_form[29]->input["korting_euro"] and $cms_form[29]->input["korting_maximaal"]) {
		$cms_form[29]->error("korting_maximaal","alleen te gebruiken bij kortingspercentage");
	}

	if(!$cms_form[29]->input["korting_euro"] and !$cms_form[29]->input["korting_percentage"] and !$cms_form[29]->input["actietekst"]) {
		$cms_form[29]->error("korting_euro","voor een korting in");
	}

	if($cms_form[29]->input["korting_percentage"]>100) {
		$cms_form[29]->error("korting_percentage","maximaal 100%");
	}

	if($cms_form[29]->input["actietekst"] and ($cms_form[29]->input["korting_euro"] or $cms_form[29]->input["korting_percentage"] or $cms_form[29]->input["korting_maximaal"])) {
#		$cms_form[29]->error("actietekst","kies voor korting of andere actie (niet allebei)");
	}

	if($cms_form[29]->input["code"] and $cms_form[29]->input["code"]<>"AUTOMATISCH") {
		if(ereg("^[A-Z0-9]+$",$cms_form[29]->input["code"])) {
			if($_GET["29k0"]) {
				$db->query("SELECT kortingscode_id FROM kortingscode WHERE kortingscode_id<>'".addslashes($_GET["29k0"])."' AND code='".addslashes($cms_form[29]->input["code"])."';");
			} else {
				$db->query("SELECT kortingscode_id FROM kortingscode WHERE code='".addslashes($cms_form[29]->input["code"])."';");
			}
			if($db->num_rows()) {
				$cms_form[29]->error("code","bestaat al");
			}
			$db->query("SELECT kortingscode_eenmalig_id FROM kortingscode_eenmalig WHERE code='".addslashes($cms_form[29]->input["code"])."';");
			if($db->num_rows()) {
				$cms_form[29]->error("code","bestaat al");
			}
		} else {
			$cms_form[29]->error("code","gebruik alleen hoofdletters en cijfers");
		}
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[29]="kortingscode";
$cms->show_mainfield[29]="omschrijving";
$cms->show_field(29,"code","Code");
$cms->show_field(29,"websites","Geldig voor");
$cms->show_field(29,"korting_euro","Korting in euro's");
$cms->show_field(29,"korting_percentage","Kortingspercentage");
$cms->show_field(29,"actietekst","Actietekst");


# Controle op delete-opdracht
if($_GET["delete"]==29 and $_GET["29k0"]) {

}

#
# DELETEn van andere tabellen
#
if($cms->set_delete_init(29)) {

}

#
# Koppeling met kortingscode_accommodatie
#
$cms->settings[29]["connect"][]=30;
$cms->settings[30]["parent"]=29;

$cms->settings[30]["list"]["show_icon"]=false;
$cms->settings[30]["list"]["edit_icon"]=false;
$cms->settings[30]["list"]["delete_icon"]=true;
$cms->settings[30]["list"]["add_link"]=false;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->db[30]["where"]="kortingscode_id='".addslashes($_GET["29k0"])."'";

$db->query("SELECT a.wzt, a.accommodatie_id, a.naam, p.naam AS plaats FROM accommodatie a, kortingscode_accommodatie aa, plaats p WHERE a.plaats_id=p.plaats_id AND a.accommodatie_id=aa.accommodatie_id AND aa.kortingscode_id='".addslashes($_GET["29k0"])."' ORDER BY p.naam, a.naam;");
while($db->next_record()) {
	$vars["kortingscodes_accommodaties"][$db->f("accommodatie_id")]=($db->f("wzt")==1 ? "W" : "Z").": ".$db->f("plaats")." - ".$db->f("naam");
	if($vars["inquery_accommodatie"]) $vars["inquery_accommodatie"].=",".$db->f("accommodatie_id"); else $vars["inquery_accommodatie"]=$db->f("accommodatie_id");
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(30,"select","accommodatie_id","",array("selection"=>$vars["kortingscodes_accommodaties"]));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[30]=array("accommodatie_id");
$cms->list_field(30,"accommodatie_id","Naam");


#
# Koppeling met kortingscode_type
#
$cms->settings[29]["connect"][]=31;
$cms->settings[31]["parent"]=29;

$cms->settings[31]["list"]["show_icon"]=false;
$cms->settings[31]["list"]["edit_icon"]=false;
$cms->settings[31]["list"]["delete_icon"]=true;
$cms->settings[31]["list"]["add_link"]=false;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->db[31]["where"]="kortingscode_id='".addslashes($_GET["29k0"])."'";

#$db->query("SELECT a.accommodatie_id, a.naam AS anaam, t.naam AS tnaam, p.naam AS plaats FROM accommodatie a, type t, kortingscode_type at, plaats p WHERE a.plaats_id=p.plaats_id AND a.accommodatie_id=t.accommodatie_id AND at.type_id=t.type_id AND at.kortingscode_id='".addslashes($_GET["29k0"])."' ORDER BY p.naam, a.naam, t.naam;");
$db->query("SELECT a.wzt, a.naam AS accommodatie, a.soortaccommodatie, a.toonper, a.accommodatie_id, t.type_id, t.naam AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l, kortingscode_type at WHERE at.type_id=t.type_id AND at.kortingscode_id='".addslashes($_GET["29k0"])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id".($vars["inquery_type"] ? " AND t.type_id NOT IN (".$vars["inquery_type"].")" : "")." ORDER BY p.naam, a.naam, t.type_id, t.naam, t.maxaantalpersonen;");
while($db->next_record()) {
	$vars["kortingscodes_types"][$db->f("type_id")]=($db->f("wzt")==1 ? "W" : "Z").": ".$db->f("plaats")." - ".$db->f("accommodatie")." (".$db->f("begincode").$db->f("type_id").")";
	if($vars["inquery_type"]) $vars["inquery_type"].=",".$db->f("type_id"); else $vars["inquery_type"]=$db->f("type_id");
	if($vars["inquery_type_accommodatie"]) $vars["inquery_type_accommodatie"].=",".$db->f("accommodatie_id"); else $vars["inquery_type_accommodatie"]=$db->f("accommodatie_id");
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(31,"select","type_id","",array("selection"=>$vars["kortingscodes_types"]));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[31]=array("type_id");
$cms->list_field(31,"type_id","Naam");

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>