<?php

#
#
#

$mustlogin=true;

include("admin/vars.php");

# hoogseizoen-gegevens opslaan
if($_POST["hoogseizoen_filled"]) {
	reset($_POST["datum"]);
	while(list($key,$value)=each($_POST["datum"])) {
		$db->query("INSERT INTO seizoen_week SET seizoen_id='".addslashes($_GET["9k0"])."', week='".addslashes($key)."', hoogseizoen='".addslashes($value)."', adddatetime=NOW(), editdatetime=NOW();");
		if($db->Errno==1062) {
			$db->query("UPDATE seizoen_week SET hoogseizoen='".addslashes($value)."', editdatetime=NOW() WHERE seizoen_id='".addslashes($_GET["9k0"])."' AND week='".addslashes($key)."';");
		}
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

# Begin- en einddatum wijzigen nog toegestaan?
if($_GET["edit"]==9) {
	$db->query("SELECT seizoen_id FROM calculatiesjabloon WHERE seizoen_id='".addslashes($_GET["9k0"])."';");
	if($db->num_rows()) {
		$datum_niet_meer_wijzigen=true;
	}
	$db->query("SELECT seizoen_id FROM calculatiesjabloon_week WHERE seizoen_id='".addslashes($_GET["9k0"])."';");
	if($db->num_rows()) {
		$datum_niet_meer_wijzigen=true;
	}
	$db->query("SELECT seizoen_id FROM tarief WHERE seizoen_id='".addslashes($_GET["9k0"])."';");
	if($db->num_rows()) {
		$datum_niet_meer_wijzigen=true;
	}
}

#$datum_niet_meer_wijzigen=false;

$cms->settings[9]["list"]["show_icon"]=true;
$cms->settings[9]["list"]["edit_icon"]=true;
$cms->settings[9]["list"]["delete_icon"]=false;

$cms->settings[9]["show"]["goto_new_record"]=false;

if($_GET["show"]<>9 and $_GET["edit"]<>9 and $_GET["add"]<>9 and !$_GET["oud"]) {
	$cms->db[9]["where"]="UNIX_TIMESTAMP(eind)>'".(time()-864000)."'";
}
		
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(9,"text","naam");
if($vars["cmstaal"]) $cms->db_field(9,"text","naam_".$vars["cmstaal"]);
$cms->db_field(9,"select","tonen","",array("selection"=>$vars["seizoen_tonen"]));
$cms->db_field(9,"yesno","optietarieventonen");
$cms->db_field(9,"yesno","optietarieven_controleren_in_cms");
$cms->db_field(9,"yesno","verplichtekeuze_vertrekdagtype");
$cms->db_field(9,"date","begin");
$cms->db_field(9,"date","eind");
$cms->db_field(9,"select","type","",array("selection"=>$vars["seizoentype_namen"]));
$cms->db_field(9,"currency","verzekeringen_poliskosten");
$cms->db_field(9,"currency","verzekeringen_poliskosten_basis");
$cms->db_field(9,"float","verzekeringen_poliskosten_korting");
$cms->db_field(9,"currency","annuleringsverzekering_poliskosten");
$cms->db_field(9,"currency","annuleringsverzekering_poliskosten_basis");
$cms->db_field(9,"float","annuleringsverzekering_poliskosten_korting");
$cms->db_field(9,"currency","reisverzekering_poliskosten");
$cms->db_field(9,"currency","reisverzekering_poliskosten_basis");
$cms->db_field(9,"float","reisverzekering_poliskosten_korting");
$cms->db_field(9,"float","assurantiebelasting");


$cms->db_field(9,"float","annuleringsverzekering_percentage_1");
$cms->db_field(9,"float","annuleringsverzekering_percentage_1_basis");
$cms->db_field(9,"float","annuleringsverzekering_percentage_1_korting");
$cms->db_field(9,"float","annuleringsverzekering_percentage_1_berekend");

$cms->db_field(9,"float","annuleringsverzekering_percentage_2");
$cms->db_field(9,"float","annuleringsverzekering_percentage_2_basis");
$cms->db_field(9,"float","annuleringsverzekering_percentage_2_korting");
$cms->db_field(9,"float","annuleringsverzekering_percentage_2_berekend");

$cms->db_field(9,"float","annuleringsverzekering_percentage_3");
$cms->db_field(9,"float","annuleringsverzekering_percentage_3_basis");
$cms->db_field(9,"float","annuleringsverzekering_percentage_3_korting");
$cms->db_field(9,"float","annuleringsverzekering_percentage_3_berekend");

$cms->db_field(9,"float","annuleringsverzekering_percentage_4");
$cms->db_field(9,"float","annuleringsverzekering_percentage_4_basis");
$cms->db_field(9,"float","annuleringsverzekering_percentage_4_korting");
$cms->db_field(9,"float","annuleringsverzekering_percentage_4_berekend");

$cms->db_field(9,"float","schadeverzekering_percentage");
$cms->db_field(9,"float","schadeverzekering_percentage_basis");
$cms->db_field(9,"float","schadeverzekering_percentage_korting");
$cms->db_field(9,"float","schadeverzekering_percentage_berekend");
$cms->db_field(9,"float","zwitersefranken_kortingspercentage");


# voorbrieven voor verschillende sites
reset($vars["websites"]);
while(list($key,$value)=each($vars["websites"])) {
	if(!$vars["websites_inactief"][$key]) {
		$cms->db_field(9,"upload","voorbrief_".strtolower($key),"",array("savelocation"=>"pdf/voorbrief_".$key."/","filetype"=>"pdf"));
	}
}

#if($vars["cmstaal"]) {
#	$cms->db_field(9,"upload","voorbrief_".$vars["cmstaal"],"",array("savelocation"=>"pdf/voorbrief_".$vars["cmstaal"]."/","filetype"=>"pdf"));
#} else {
#	$cms->db_field(9,"upload","voorbrief","",array("savelocation"=>"pdf/voorbrief_nl/","filetype"=>"pdf"));
#}

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[9]=array("type","begin","eind");
$cms->list_field(9,"type","Type");
$cms->list_field(9,"naam","Omschrijving");
$cms->list_field(9,"begin","",array("date_format"=>"DD-MM-JJJJ"));
$cms->list_field(9,"eind","",array("date_format"=>"DD-MM-JJJJ"));

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(9,1,"tonen","Zichtbaarheid",array("selection"=>1));
$cms->edit_field(9,0,"optietarieventonen","Optietarieven van dit seizoen: tonen op de accommodatiepagina",array("selection"=>true));
$cms->edit_field(9,0,"optietarieven_controleren_in_cms","Optie/skipas-tarieven van dit seizoen: tonen in CMS of ze zijn gevuld",array("selection"=>false));
$cms->edit_field(9,0,"verplichtekeuze_vertrekdagtype","Bij dit seizoen is het verplicht per accommodatie een vertrekdagtype te selecteren");


if($vars["cmstaal"]) {
	$cms->edit_field(9,0,"naam","Omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(9,1,"naam_".$vars["cmstaal"],"Omschrijving ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(9,1,"naam","Omschrijving");
}
$cms->edit_field(9,1,"type","Type");
if($datum_niet_meer_wijzigen) {
	
} else {
	$cms->edit_field(9,0,"htmlrow","<hr><div style=\"padding:5px;width:400px;border:1px solid red;background-color:yellow;\"><b>Let op: kies onderstaande datums heel zorgvuldig!</b><br>Na het invoeren van calculatiesjablonen en tarieven<br>is wijzigen niet meer mogelijk.</div>");
	$cms->edit_field(9,1,"begin","Eerste aankomstdatum","","",array("calendar"=>true));
	$cms->edit_field(9,1,"eind","Laatste aankomstdatum","","",array("calendar"=>true));
}

# voorbrieven voor verschillende sites
reset($vars["websites"]);
while(list($key,$value)=each($vars["websites"])) {
	if(!$vars["websites_inactief"][$key]) {
		$cms->edit_field(9,0,"voorbrief_".strtolower($key),"Voorbrief ".$value." (".$key.")","",array("showfiletype"=>true));
	}
}

#if($vars["cmstaal"]) {
#	$cms->edit_field(9,0,"voorbrief_".$vars["cmstaal"],"Voorbrief ".strtoupper($vars["cmstaal"]),"",array("showfiletype"=>true));
#} else {
#	$cms->edit_field(9,0,"voorbrief","Voorbrief","",array("showfiletype"=>true));
#}
$cms->edit_field(9,0,"htmlrow","<hr><b>Verzekeringen</b>");
$cms->edit_field(9,1,"assurantiebelasting","Assurantiebelasting");

$cms->edit_field(9,0,"htmlrow","<hr><b>Poliskosten alle verzekeringen</b>");
$cms->edit_field(9,1,"verzekeringen_poliskosten_basis","Basis");
$cms->edit_field(9,1,"verzekeringen_poliskosten_korting","Korting");
$cms->edit_field(9,1,"verzekeringen_poliskosten","Basis + assurantiebelasting");

if($_GET["add"]==9 or $_GET["1k0"]<=18) {
	# losse poliskosten reisverzekering en annuleringsverzekering niet meer van toepassing 
	$cms->edit_field(9,0,"htmlrow","<hr><b>Poliskosten reisverzekering</b> (binnenkort niet meer van toepassing)");
	$cms->edit_field(9,1,"reisverzekering_poliskosten_basis","Basis");
	$cms->edit_field(9,1,"reisverzekering_poliskosten_korting","Korting");
	$cms->edit_field(9,1,"reisverzekering_poliskosten","Basis + assurantiebelasting");
	
	$cms->edit_field(9,0,"htmlrow","<hr><b>Poliskosten annuleringsverzekering</b> (binnenkort niet meer van toepassing)");
	$cms->edit_field(9,1,"annuleringsverzekering_poliskosten_basis","Basis");
	$cms->edit_field(9,1,"annuleringsverzekering_poliskosten_korting","Korting");
	$cms->edit_field(9,1,"annuleringsverzekering_poliskosten","Basis + assurantiebelasting");
}

# annuleringsverzekering_percentage_1
$cms->edit_field(9,0,"htmlrow","<hr><b>Variabele kosten % ".$vars["annverz_soorten"][1]."</b>");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_1_basis","Basis");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_1_korting","Korting");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_1_berekend","Basis + assurantiebelasting","",array("decimals"=>5));
$cms->edit_field(9,1,"annuleringsverzekering_percentage_1","Afgerond voor klant");

# annuleringsverzekering_percentage_2
$cms->edit_field(9,0,"htmlrow","<hr><b>Variabele kosten % ".$vars["annverz_soorten"][2]."</b>");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_2_basis","Basis");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_2_korting","Korting");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_2_berekend","Basis + assurantiebelasting","",array("decimals"=>5));
$cms->edit_field(9,1,"annuleringsverzekering_percentage_2","Afgerond voor klant");

# annuleringsverzekering_percentage_3
$cms->edit_field(9,0,"htmlrow","<hr><b>Variabele kosten % ".$vars["annverz_soorten"][3]."</b>");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_3_basis","Basis");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_3_korting","Korting");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_3_berekend","Basis + assurantiebelasting","",array("decimals"=>5));
$cms->edit_field(9,1,"annuleringsverzekering_percentage_3","Afgerond voor klant");

# annuleringsverzekering_percentage_4
$cms->edit_field(9,0,"htmlrow","<hr><b>Variabele kosten % ".$vars["annverz_soorten"][4]."</b>");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_4_basis","Basis");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_4_korting","Korting");
$cms->edit_field(9,1,"annuleringsverzekering_percentage_4_berekend","Basis + assurantiebelasting","",array("decimals"=>5));
$cms->edit_field(9,1,"annuleringsverzekering_percentage_4","Afgerond voor klant");

# schadeverzekering
$cms->edit_field(9,0,"htmlrow","<hr><b>Variabele kosten % Schade Logies Verblijven</b>");
$cms->edit_field(9,1,"schadeverzekering_percentage_basis","Basis");
$cms->edit_field(9,1,"schadeverzekering_percentage_korting","Korting");
$cms->edit_field(9,1,"schadeverzekering_percentage_berekend","Basis + assurantiebelasting","",array("decimals"=>5));
$cms->edit_field(9,1,"schadeverzekering_percentage","Afgerond voor klant");

$cms->edit_field(9,0,"htmlrow","<hr><b>Leveranciers die met Zwitserse Franken werken</b><br><br><i>Indien de wisselkoers van de CHF bijvoorbeeld 0,85 euro bedraagt, vul je als korting 15% in.</i>");
$cms->edit_field(9,1,"zwitersefranken_kortingspercentage","Kortingspercentage bij Zwitserse Franken");


#$cms->edit_field(9,1,"annuleringsverzekering_percentage_2","Variabele kosten % ".$vars["annverz_soorten"][2]);
#$cms->edit_field(9,1,"annuleringsverzekering_percentage_3","Variabele kosten % ".$vars["annverz_soorten"][3]);
#$cms->edit_field(9,1,"annuleringsverzekering_percentage_4","Variabele kosten % ".$vars["annverz_soorten"][4]);

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(9);
if($cms_form[9]->filled) {
	if($_GET["add"]==9) {
		if($cms_form[9]->input["begin"]["unixtime"]) {
			$db->query("SELECT naam FROM seizoen WHERE seizoen_id<>'".addslashes($_GET["9k0"])."' AND type='".addslashes($cms_form[9]->input["type"])."' AND UNIX_TIMESTAMP(begin)<=".addslashes($cms_form[9]->input["begin"]["unixtime"])." AND UNIX_TIMESTAMP(eind)>=".addslashes($cms_form[9]->input["begin"]["unixtime"]).";");
			if($db->next_record()) {
				$cms_form[9]->error("begin","overlap met seizoen &quot;".$db->f("naam")."&quot;");
			}
		}
		if($cms_form[9]->input["eind"]["unixtime"]) {
			$db->query("SELECT naam FROM seizoen WHERE seizoen_id<>'".addslashes($_GET["9k0"])."' AND type='".addslashes($cms_form[9]->input["type"])."' AND UNIX_TIMESTAMP(begin)<=".addslashes($cms_form[9]->input["eind"]["unixtime"])." AND UNIX_TIMESTAMP(eind)>=".addslashes($cms_form[9]->input["eind"]["unixtime"]).";");
			if($db->next_record()) {
				$cms_form[9]->error("eind","overlap met seizoen &quot;".$db->f("naam")."&quot;");
			}
		}
		if($cms_form[9]->input["begin"]["unixtime"] and date("w",$cms_form[9]->input["begin"]["unixtime"])<>6) $cms_form[9]->error("begin","moet een zaterdag zijn");
		if($cms_form[9]->input["eind"]["unixtime"] and date("w",$cms_form[9]->input["eind"]["unixtime"])<>6) $cms_form[9]->error("eind","moet een zaterdag zijn");
		if($cms_form[9]->input["begin"]["unixtime"] and $cms_form[9]->input["eind"]["unixtime"] and $cms_form[9]->input["eind"]["unixtime"]<$cms_form[9]->input["begin"]["unixtime"]) $cms_form[9]->error("eind","moet later zijn dan de eerste aankomstdatum");
	}
}


#
# DELETEn van andere tabellen
#
if($cms->set_delete_init(9)) {

}


# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[9]="seizoen";
$cms->show_mainfield[9]="naam";
$cms->show_field(9,"naam");

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>