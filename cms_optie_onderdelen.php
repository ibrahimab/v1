<?php

$mustlogin=true;

include("admin/vars.php");

# Tarieven kopiëren
if($_POST["optieonderdeel_kopieer"]==1) {
	if($_POST["seizoen"] and $_POST["optieonderdeel"]) {
		# Eerst oude tarieven wissen
		$db->query("DELETE FROM optie_tarief WHERE optie_onderdeel_id='".addslashes($_POST["optieonderdeel"])."' AND seizoen_id='".addslashes($_POST["seizoen"])."';");

		# Dan kopiëren
		$db->query("SELECT week, beschikbaar, verkoop, netto_ink, inkoop, korting, korting_euro, omzetbonus, wederverkoop_commissie_agent FROM optie_tarief WHERE optie_onderdeel_id='".addslashes($_GET["13k0"])."' AND seizoen_id='".addslashes($_POST["seizoen"])."';");
		while($db->next_record()) {
			$db2->query("INSERT INTO optie_tarief SET week='".addslashes($db->f("week"))."', beschikbaar='".addslashes($db->f("beschikbaar"))."', verkoop='".addslashes($db->f("verkoop"))."', netto_ink='".addslashes($db->f("netto_ink"))."', inkoop='".addslashes($db->f("inkoop"))."', korting='".addslashes($db->f("korting"))."', korting_euro='".addslashes($db->f("korting_euro"))."', omzetbonus='".addslashes($db->f("omzetbonus"))."', wederverkoop_commissie_agent='".addslashes($db->f("wederverkoop_commissie_agent"))."', optie_onderdeel_id='".addslashes($_POST["optieonderdeel"])."', seizoen_id='".addslashes($_POST["seizoen"])."';");
		}
		header("Location: ".$_SERVER["REQUEST_URI"]."&copy=1");
		exit;
	}
}

if($_GET["add"]==13 and $_GET["12k0"]) {
	$db->query("SELECT max(volgorde) AS volgorde FROM optie_onderdeel WHERE optie_groep_id='".addslashes($_GET["12k0"])."'");
	if($db->next_record()) {
		$volgende=$db->f("volgorde")+10;
	}
}

if($_GET["11k0"]) {
	$db->query("SELECT voucher, algemeneoptie FROM optie_soort WHERE optie_soort_id='".addslashes($_GET["11k0"])."';");
	if($db->next_record()) {
		$temp["voucher"]=$db->f("voucher");
		$temp["algemeneoptie"]=$db->f("algemeneoptie");
	}
}

if(!$volgende) $volgende=1;

$cms->settings[13]["list"]["show_icon"]=true;
$cms->settings[13]["list"]["edit_icon"]=true;
$cms->settings[13]["list"]["delete_icon"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(13,"text","naam");
if($vars["cmstaal"]) $cms->db_field(13,"text","naam_".$vars["cmstaal"]);
$cms->db_field(13,"yesno","actief");
$cms->db_field(13,"integer","volgorde");
$cms->db_field(13,"text","leverancierscode");
#$cms->db_field(13,"textarea","toelichting");
$cms->db_field(13,"integer","min_leeftijd");
$cms->db_field(13,"integer","max_leeftijd");
$cms->db_field(13,"integer","min_deelnemers");
$cms->db_field(13,"select","bijkomendekosten_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=3"));
#$cms->db_field(13,"yesno","te_selecteren");
$cms->db_field(13,"yesno","te_selecteren_door_klant");
$cms->db_field(13,"yesno","tonen_accpagina");
$cms->db_field(13,"yesno","voucher");
$cms->db_field(13,"text","omschrijving_voucher");
$cms->db_field(13,"text","tekstbegin_voucher");
$cms->db_field(13,"text","teksteind_voucher");
$cms->db_field(13,"select","begindag","",array("selection"=>$vars["begineinddagen"]));
$cms->db_field(13,"select","einddag","",array("selection"=>$vars["begineinddagen"]));
$cms->db_field(13,"yesno","geboortedatum_voucher");
$cms->db_field(13,"yesno","arrangement_zonder_skipas");
$cms->db_field(13,"yesno","hoort_bij_accommodatieinkoop");





$cms->db[13]["set"]="optie_groep_id='".addslashes($_GET["12k0"])."'";

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(13,"naam","Naam");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(13,0,"actief","Actief",array("selection"=>true));
if($vars["cmstaal"]) {
	$cms->edit_field(13,1,"naam","Naam NL","",array("noedit"=>true));
	$cms->edit_field(13,1,"naam_".$vars["cmstaal"],"Naam ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(13,1,"naam");
}

#arrangement_zonder_skipas


#$cms->edit_field(13,0,"toelichting");
$cms->edit_field(13,1,"volgorde","",array("text"=>$volgende));
$cms->edit_field(13,0,"leverancierscode","Bestellijstcode");
if(!$temp["algemeneoptie"]) {
	$cms->edit_field(13,0,"min_leeftijd","Minimale leeftijd (in jaren)");
	$cms->edit_field(13,0,"max_leeftijd","Maximale leeftijd (in jaren)");
	$cms->edit_field(13,0,"min_deelnemers","Minimaal aantal deelnemers (dat deze optie afneemt)");
	$cms->edit_field(13,0,"bijkomendekosten_id","Bijkomende kosten");
}
#$cms->edit_field(13,0,"te_selecteren","Kan worden geselecteerd bij boeken",array("selection"=>true));
$cms->edit_field(13,0,"te_selecteren_door_klant","Kan worden geselecteerd bij boeken door de klant",array("selection"=>true));
$cms->edit_field(13,0,"tonen_accpagina","Tonen op de accommodatiepagina (op het tabblad 'extra opties')",array("selection"=>true));

$cms->edit_field(13,0,"arrangement_zonder_skipas","Dit is een \"korting ivm arrangement zonder skipas\"");
$cms->edit_field(13,0,"hoort_bij_accommodatieinkoop","Deze kosten worden berekend op de factuur van de accommodatie-leverancier");
if($temp["voucher"]) {
	$cms->edit_field(13,0,"htmlrow","<hr><b>Voucher</b>");
	$cms->edit_field(13,0,"voucher","Komt op een voucher",array("selection"=>true));
	$cms->edit_field(13,0,"omschrijving_voucher","Omschrijving op de voucher");
	$cms->edit_field(13,0,"tekstbegin_voucher","Extra tekst eerste dag");
	$cms->edit_field(13,0,"teksteind_voucher","Extra tekst laatste dag");
	$cms->edit_field(13,0,"begindag","Datumaanpassing begin");
	$cms->edit_field(13,0,"einddag","Datumaanpassing eind");
	$cms->edit_field(13,0,"geboortedatum_voucher","Geboortedatum op voucher tonen");
}

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(13);
if($cms_form[13]->filled) {
	if($cms_form[13]->input["voucher"] and !$cms_form[13]->input["omschrijving_voucher"]) {
		$cms_form[13]->error("omschrijving_voucher","obl");
	}

	if(!$cms_form[13]->input["actief"]) {
		$db->query("SELECT DISTINCT SUBSTR(b.boekingsnummer,2), b.boeking_id, b.boekingsnummer, s.naam FROM boeking b, boeking_optie bo, view_optie v, seizoen s WHERE b.seizoen_id=s.seizoen_id AND s.eind>NOW() AND v.optie_onderdeel_id='".addslashes($_GET["13k0"])."' AND bo.optie_onderdeel_id=v.optie_onderdeel_id AND bo.boeking_id=b.boeking_id AND boekingsnummer<>'' ORDER BY 1;");
		if($db->num_rows()) {
			$melding="de-activeren niet mogelijk: er zijn nog actuele boekingen aan dit optie-onderdeel gekoppeld:<ul>";
			while($db->next_record()) {
				$melding.="<li><a href=\"cms_boekingen.php?show=21&21k0=".$db->f("boeking_id")."\" target=\"_blank\">".wt_he($db->f("boekingsnummer"))."</a> (".$db->f("naam").")</li>";
			}
			$melding.="</ul>";
			$cms_form[13]->error("actief",$melding);
		}
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[13]="optie-onderdeel-gegevens";
$cms->show_mainfield[13]="naam";
$cms->show_field(13,"naam","Naam");

function form_before_goto($form) {
	global $login,$vars;
	$db=new DB_sql;
	$db2=new DB_sql;

	if($_GET["12k0"]) {
		$volgorde=0;
		$db->query("SELECT optie_onderdeel_id FROM optie_onderdeel WHERE optie_groep_id='".addslashes($_GET["12k0"])."' ORDER BY volgorde;");
		while($db->next_record()) {
			$volgorde=$volgorde+10;
			$db2->query("UPDATE optie_onderdeel SET volgorde='".$volgorde."' WHERE optie_onderdeel_id='".$db->f("optie_onderdeel_id")."';");
		}
	}
}

# Controle op delete-opdracht
if($_GET["delete"]==13 and $_GET["13k0"]) {

}

# DELETEn van andere tabellen
if($cms->set_delete_init(13)) {
	$db->query("DELETE FROM optie_tarief WHERE optie_onderdeel_id='".addslashes($_GET["13k0"])."';");
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>