<?php


$mustlogin=true;
$vars["types_in_vars"]=true;
include("admin/vars.php");

if($_POST["statusfilled"]) {
	$db->query("UPDATE optieaanvraag SET status='".addslashes($_POST["status"])."' WHERE optieaanvraag_id='".addslashes($_GET["35k0"])."';");
	header("Location: ".$_SERVER["REQUEST_URI"]);
}

# Gegevens geselecteerde optieaanvraag uit database halen
if($_GET["35k0"]) {
	$db->query("SELECT type_id, aankomstdatum, aankomstdatum_exact, status, ingevuldvia, aantalpersonen FROM optieaanvraag WHERE optieaanvraag_id='".addslashes($_GET["35k0"])."';");
	if($db->next_record()) {
		$vars["temp_optieaanvraag"]["acc"]=accinfo($db->f("type_id"));
		$vars["temp_optieaanvraag"]["aankomstdatum"]=$db->f("aankomstdatum");
		$vars["temp_optieaanvraag"]["aankomstdatum_exact"]=$db->f("aankomstdatum_exact");
		$vars["temp_optieaanvraag"]["status"]=$db->f("status");
		$vars["temp_optieaanvraag"]["ingevuldvia"]=$db->f("ingevuldvia");
		$vars["temp_optieaanvraag"]["aantalpersonen"]=$db->f("aantalpersonen");
		$_GET["status"]=$db->f("status");
	}
} else {
	# Alle leveranciers in vars zetten
	$db->query("SELECT o.optieaanvraag_id, l.leverancier_id, l.naam AS leverancier FROM optieaanvraag o, leverancier l, type t, accommodatie a WHERE o.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND t.leverancier_id=l.leverancier_id;");
	while($db->next_record()) {
		$vars["kolom_leveranciers"][$db->f("optieaanvraag_id")]=$db->f("leverancier");
	}

	# Boekdatum in vars zetten
	if($_GET["status"]==6) {
		$db->query("SELECT o.optieaanvraag_id, UNIX_TIMESTAMP(b.bevestigdatum) AS bevestigdatum FROM boeking b, optieaanvraag o WHERE b.optieaanvraag_id=o.optieaanvraag_id;");
		while($db->next_record()) {
			$vars["kolom_boekdatum"][$db->f("optieaanvraag_id")]=$db->f("bevestigdatum");
		}
		if(!$vars["kolom_boekdatum"]) {
			$vars["kolom_boekdatum"][0]="";
		}
	}
}

if(!$_GET["status"]) {
	$_GET["status"]=1;
}

if($_GET["status"]) {
	# Ongebruikte garanties
	$cms->db[35]["where"]="status='".addslashes($_GET["status"])."'";
}

if($_GET["tid"]) {
	$cms->db[35]["where"].=" AND type_id='".addslashes($_GET["tid"])."'";
}

#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")

if($vars["kolom_leveranciers"]) {
	$cms->db_field(35,"select","leverancier","optieaanvraag_id",array("selection"=>$vars["kolom_leveranciers"]));
}

if($vars["kolom_boekdatum"]) {
	$cms->db_field(35,"select","boekdatum","optieaanvraag_id",array("selection"=>$vars["kolom_boekdatum"]));
}

$cms->db_field(35,"select","website","",array("selection"=>$vars["websites"]));
$cms->db_field(35,"select","type_id","",array("selection"=>$vars["alletypes"]));
$cms->db_field(35,"date","aankomstdatum");
$cms->db_field(35,"date","aankomstdatum_exact");
$cms->db_field(35,"date","einddatum_klant");
$cms->db_field(35,"date","aanvraagdatum_leverancier");
$cms->db_field(35,"date","einddatum_leverancier");
$cms->db_field(35,"integer","aantalpersonen");
$cms->db_field(35,"select","status","",array("selection"=>$vars["optieaanvragen_status"]));
$cms->db_field(35,"select","herkomst","",array("selection"=>$vars["optieaanvragen_herkomst"]));
$cms->db_field(35,"textarea","opmerkingen_intern");
$cms->db_field(35,"select","ingevuldvia","",array("selection"=>$vars["optieaanvragen_ingevuldvia"]));
$cms->db_field(35,"select","user_id","",array("othertable"=>"25","otherkeyfield"=>"user_id","otherfield"=>"voornaam"));
$cms->db_field(35,"date","invulmoment");
$cms->db_field(35,"text","voornaam");
$cms->db_field(35,"text","tussenvoegsel");
$cms->db_field(35,"text","achternaam");
$cms->db_field(35,"text","adres");
$cms->db_field(35,"text","postcode");
$cms->db_field(35,"text","plaats");
$cms->db_field(35,"text","land");
$cms->db_field(35,"text","telefoonnummer");
$cms->db_field(35,"text","mobielwerk");
$cms->db_field(35,"email","email");

#
# List
#
# Te tonen icons/links bij list
$cms->settings[35]["list"]["show_icon"]=true;
$cms->settings[35]["list"]["edit_icon"]=false;
$cms->settings[35]["list"]["delete_icon"]=true;
$cms->settings[35]["list"]["add_link"]=false;

# List list_field($counter,$id,$title="",$options="",$layout="")

if($_GET["status"]==1) {
	$cms->list_sort[35]=array("invulmoment","wt_naam","type_id");
	$cms->list_field(35,"invulmoment","Invuldatum",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"leverancier","Leverancier");
	$cms->list_field(35,"type_id","Accommodatie");
	$cms->list_field(35,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"wt_naam","Naam klant");
} elseif($_GET["status"]==2) {
	$cms->list_sort[35]=array("aanvraagdatum_leverancier","wt_naam","type_id");
	$cms->list_field(35,"aanvraagdatum_leverancier","Aanvraagdatum",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"leverancier","Leverancier");
	$cms->list_field(35,"type_id","Accommodatie");
	$cms->list_field(35,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"wt_naam","Naam klant");
} elseif($_GET["status"]==3) {
	$cms->list_sort[35]=array("einddatum_klant","wt_naam","type_id");
	$cms->list_field(35,"einddatum_klant","Einddatum klant",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"leverancier","Leverancier");
	$cms->list_field(35,"type_id","Accommodatie");
	$cms->list_field(35,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"wt_naam","Naam klant");
	$cms->list_field(35,"einddatum_leverancier","Einddatum lev",array("date_format"=>"DD-MM-JJJJ"));
} elseif($_GET["status"]==4) {
	$cms->list_sort[35]=array("aanvraagdatum_leverancier","wt_naam","type_id");
	$cms->list_field(35,"aanvraagdatum_leverancier","Aanvraagdatum",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"leverancier","Leverancier");
	$cms->list_field(35,"type_id","Accommodatie");
	$cms->list_field(35,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"wt_naam","Naam klant");
} elseif($_GET["status"]==5) {
	$cms->list_sort[35]=array("einddatum_klant","wt_naam","type_id");
	$cms->list_field(35,"einddatum_klant","Einddatum klant",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"leverancier","Leverancier");
	$cms->list_field(35,"type_id","Accommodatie");
	$cms->list_field(35,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"wt_naam","Naam klant");
	$cms->list_field(35,"einddatum_leverancier","Einddatum lev",array("date_format"=>"DD-MM-JJJJ"));
} elseif($_GET["status"]==6) {
	$cms->list_sort[35]=array("boekdatum","aankomstdatum_exact","wt_naam","type_id");
	$cms->list_field(35,"boekdatum","Boekdatum",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"leverancier","Leverancier");
	$cms->list_field(35,"type_id","Accommodatie");
	$cms->list_field(35,"aankomstdatum_exact","Aankomst",array("date_format"=>"DD-MM-JJJJ"));
	$cms->list_field(35,"wt_naam","Naam klant");
	$cms->list_field(35,"einddatum_leverancier","Einddatum lev",array("date_format"=>"DD-MM-JJJJ"));
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[35]="optie-aanvraag";
$cms->show_mainfield[35]="wt_naam";
$cms->show_field(35,"wt_naam","Naam");
$cms->show_field(35,"aantalpersonen","Aantal personen");
$cms->show_field(35,"aankomstdatum_exact","Aankomstdatum",array("date_format"=>"DAG D MAAND JJJJ"));
if($_GET["status"]==4 or $_GET["status"]==6) {
	$cms->show_field(35,"herkomst","Herkomst");
}
$cms->show_field(35,"invulmoment","Ingevuld",array("date_format"=>"D MAAND JJJJ, UU:ZZ"));
$cms->show_field(35,"einddatum_klant","Einddatum klant",array("date_format"=>"DAG D MAAND JJJJ"));
$cms->show_field(35,"aanvraagdatum_leverancier","Aangevraagd",array("date_format"=>"DAG D MAAND JJJJ"));
$cms->show_field(35,"einddatum_leverancier","Einddatum lev.",array("date_format"=>"DAG D MAAND JJJJ"));
$cms->show_field(35,"website","Website");
$cms->show_field(35,"ingevuldvia","Via");
if($vars["temp_optieaanvraag"]["ingevuldvia"]==3) {
	$cms->show_field(35,"user_id","Ingevuld door");
}

# Controle op delete-opdracht
if($_GET["delete"]==35 and $_GET["35k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(35)) {
#	if(($_GET["status"]==1 or $_GET["status"]==3) and $delete_typeid and $delete_aankomstdatum) {
#		voorraad_bijwerken($delete_typeid,$delete_aankomstdatum,true,-1,0,0,0,0,0,0);
#	}
}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
if($_GET["35k0"]) {
	$cms->edit_field(35,1,"htmlcol","Accommodatie",array("html"=>wt_he($vars["alletypes"][$vars["temp_optieaanvraag"]["acc"]["type_id"]])));
	$cms->edit_field(35,1,"htmlcol","Aankomstdatum",array("html"=>wt_he(DATUM("DAG D MAAND JJJJ",$vars["temp_optieaanvraag"]["aankomstdatum_exact"]))));
}
$cms->edit_field(35,1,"status","Status");
$cms->edit_field(35,1,"einddatum_klant","Einddatum klant","","",array("calendar"=>true));
$cms->edit_field(35,0,"herkomst","Herkomst");
$cms->edit_field(35,0,"aanvraagdatum_leverancier","Aanvraagdatum leverancier","","",array("calendar"=>true));
$cms->edit_field(35,0,"einddatum_leverancier","Einddatum leverancier","","",array("calendar"=>true));
$cms->edit_field(35,0,"opmerkingen_intern","Opmerkingen (intern)","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
$cms->edit_field(35,0,"htmlrow","<hr><b>Gegevens klant</b>");
$cms->edit_field(35,0,"voornaam");
$cms->edit_field(35,0,"tussenvoegsel");
$cms->edit_field(35,0,"achternaam");
$cms->edit_field(35,0,"adres");
$cms->edit_field(35,0,"postcode");
$cms->edit_field(35,0,"plaats");
$cms->edit_field(35,0,"land");
$cms->edit_field(35,0,"telefoonnummer");
$cms->edit_field(35,0,"mobielwerk");
$cms->edit_field(35,0,"email");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(35);
if($cms_form[35]->filled) {

}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>