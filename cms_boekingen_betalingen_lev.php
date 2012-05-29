<?php

$mustlogin=true;
$boeking_bepaalt_taal=true;
if($_GET["gar_id"]) {
	$vars["types_in_vars"]=true;
}
include("admin/vars.php");

if(!$login->has_priv("5")) {
	header("Location: cms.php");
	exit;
}

if($_GET["bid"]) {
	$gegevens=get_boekinginfo($_GET["bid"]);
} elseif($_GET["gar_id"]) {
	$db->query("SELECT g.leverancier_id, g.bruto, g.korting_percentage, g.netto, g.factuurnummer, g.reserveringsnummer_extern, g.inkoopaanbetaling_gewijzigd, g.aankomstdatum_exact, UNIX_TIMESTAMP(g.inkoopfactuurdatum) AS inkoopfactuurdatum, g.aankomstdatum_exact, g.naam AS garantie, v.type_id FROM garantie g, view_accommodatie v WHERE g.garantie_id='".addslashes($_GET["gar_id"])."' AND g.type_id=v.type_id;");
	if($db->next_record()) {

		$vars["temp_garantieinfo"]["naam"]=$db->f("garantie");
		$vars["temp_garantieinfo"]["accnaam"]=$vars["alletypes"][$db->f("type_id")];
	
#	echo wt_dump($vars["temp_garantieinfo"]);
	
		$gegevens["stap1"]["leverancierid"]=$db->f("leverancier_id");
		$gegevens["stap1"]["inkoopbruto"]=$db->f("bruto");
		$gegevens["stap1"]["inkoopcommissie"]=$db->f("korting_percentage");
	#	$gegevens["stap1"]["inkoopkorting"]
	#	$gegevens["stap1"]["inkoopkorting_percentage"]
	#	$gegevens["stap1"]["inkoopkorting_euro"]
		$gegevens["stap1"]["totaalfactuurbedrag"]=$db->f("netto");
		$gegevens["stap1"]["factuurnummer_leverancier"]=$db->f("factuurnummer");
		$gegevens["stap1"]["boekingsnummer"]=$db->f("reserveringsnummer_extern"); # ons factuurnummer
		$gegevens["stap1"]["inkoopaanbetaling_gewijzigd"]=$db->f("inkoopaanbetaling_gewijzigd");
		$gegevens["stap1"]["inkoopfactuurdatum"]=$db->f("inkoopfactuurdatum");
		$gegevens["stap1"]["aankomstdatum_exact"]=$db->f("aankomstdatum_exact");
	}		
}


if($_POST["opmfilled"]==1) {
	# Opmerking opslaan
	$db->query("UPDATE boeking SET factuur_opmerkingen='".addslashes($_POST["opmerkingen"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
	cmslog_pagina_title("opmerkingen factuur");
	chalet_log("opmerkingen factuur",true,true);
	$_SESSION["wt_popupmsg"]="opmerkingen zijn opgeslagen";
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

if($_POST["inkoopaanbetaling_gewijzigd_filled"]) {
	if($_POST["inkoopaanbetaling_gewijzigd"]=="") {
		if($_GET["gar_id"]) {
			$db->query("UPDATE garantie SET inkoopaanbetaling_gewijzigd=NULL WHERE garantie_id='".addslashes($_GET["gar_id"])."';");		
		} else {
			$db->query("UPDATE boeking SET inkoopaanbetaling_gewijzigd=NULL WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			chalet_log("inkoopaanbetaling: handmatig bedrag gewist",true,true);
		}
	} else {
		if($_GET["gar_id"]) {
			$db->query("UPDATE garantie SET inkoopaanbetaling_gewijzigd='".addslashes($_POST["inkoopaanbetaling_gewijzigd"])."' WHERE garantie_id='".addslashes($_GET["gar_id"])."';");
		} else {
			$_POST["inkoopaanbetaling_gewijzigd"]=floatval(preg_replace("/,/",".",$_POST["inkoopaanbetaling_gewijzigd"]));
			$db->query("UPDATE boeking SET inkoopaanbetaling_gewijzigd='".addslashes($_POST["inkoopaanbetaling_gewijzigd"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			chalet_log("inkoopaanbetaling: € ".number_format($_POST["inkoopaanbetaling_gewijzigd"],2,",",".")."",true,true);
		}
	}
	$_SESSION["wt_popupmsg"]="aanbetalingsbedrag is opgeslagen";
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

# al_betaald berekenen en betalingsverschillen constateren
unset($vars["temp_al_betaald"]);
if($_GET["gar_id"]) {
	$db2->query("SELECT boeking_betaling_lev_id, bedrag, bedrag_goedgekeurd FROM boeking_betaling_lev WHERE garantie_id='".addslashes($_GET["gar_id"])."';");
} else {
	$db2->query("SELECT boeking_betaling_lev_id, bedrag, bedrag_goedgekeurd FROM boeking_betaling_lev WHERE boeking_id='".addslashes($_GET["bid"])."';");
}
while($db2->next_record()) {
	if($db2->f("bedrag_goedgekeurd")) {
		$vars["temp_al_betaald"]+=$db2->f("bedrag_goedgekeurd");
		if($db2->f("bedrag_goedgekeurd")<>$db2->f("bedrag")) {
			$vars["temp_boeking_betaling_lev_verschil"][$db2->f("boeking_betaling_lev_id")]="<span style=\"color:red;font-weight:bold;\">verschil tussen onderweg en betaald: € ".number_format($db2->f("bedrag")-$db2->f("bedrag_goedgekeurd"),2,",",".")."</span>";
		}	
	} else {
		$vars["temp_al_betaald"]+=$db2->f("bedrag");
	}
}

#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(43,"date","datum");
$cms->db_field(43,"currency","bedrag");
$cms->db_field(43,"currency","bedrag_goedgekeurd");
$cms->db_field(43,"textarea","opmerkingen");
$cms->db_field(43,"select","verschil","boeking_betaling_lev_id",array("selection"=>$vars["temp_boeking_betaling_lev_verschil"]));

# Where-statement
if($_GET["gar_id"]) {
	$cms->db[43]["where"]="garantie_id='".addslashes($_GET["gar_id"])."'";
	$cms->db[43]["set"]="garantie_id='".addslashes($_GET["gar_id"])."'";
} else {
	$cms->db[43]["where"]="boeking_id='".addslashes($_GET["bid"])."'";
	$cms->db[43]["set"]="boeking_id='".addslashes($_GET["bid"])."'";
}


#
# List
#
# Te tonen icons/links bij list
$cms->settings[43]["list"]["show_icon"]=false;
$cms->settings[43]["list"]["edit_icon"]=true;
$cms->settings[43]["list"]["delete_icon"]=true;
$cms->settings[43]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[43]=array("datum");
$cms->list_field(43,"datum","Datum",array("date_format"=>"DD-MM-JJJJ"));
$cms->list_field(43,"bedrag","Onderweg");
$cms->list_field(43,"bedrag_goedgekeurd","Betaald");
$cms->list_field(43,"verschil","Verschil","",array("html"=>true)); # toon als html

# Controle op delete-opdracht
if($_GET["delete"]==43 and $_GET["43k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(43)) {
	if($_GET["bid"]) {
		chalet_log("inkoopbetaling gewist",false,true);
	}
}

#
# Edit
#
# Nieuw record meteen openen na toevoegen
$cms->settings[43]["show"]["goto_new_record"]=false;

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(43,1,"bedrag","Bedrag onderweg","",array("negative"=>true));
$cms->edit_field(43,0,"bedrag_goedgekeurd","Bedrag betaald","",array("negative"=>true));
$cms->edit_field(43,1,"datum","Betaaldatum",array("time"=>time()),array("startyear"=>2003,"endyear"=>date("Y")+1),array("calendar"=>true));
$cms->edit_field(43,0,"opmerkingen","Opmerkingen");

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(43);
if($cms_form[43]->filled) {

}

if($cms_form[43]->okay) {
	$vorig_bedrag=0;
	$vorig_bedrag_goedgekeurd=0;
	if($_GET["43k0"]) {
		$db->query("SELECT bedrag, bedrag_goedgekeurd FROM boeking_betaling_lev WHERE boeking_betaling_lev_id='".addslashes($_GET["43k0"])."';");
		if($db->next_record()) {
			$vorig_bedrag=$db->f("bedrag");
			$vorig_bedrag_goedgekeurd=$db->f("bedrag_goedgekeurd");
		}
	}
	if($_GET["bid"]) {
		if($cms_form[43]->input["bedrag"]<>$vorig_bedrag) {
			chalet_log("inkoopbetaling ".date("d-m-Y",$cms_form[43]->input["datum"]["unixtime"])." onderweg: € ".@number_format($cms_form[43]->input["bedrag"],2,',','.'),false,true);
		}
		if($cms_form[43]->input["bedrag_goedgekeurd"]<>$vorig_bedrag_goedgekeurd) {
			chalet_log("inkoopbetaling goedgekeurd: € ".@number_format($cms_form[43]->input["bedrag_goedgekeurd"],2,',','.'),false,true);
		}
	}
}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>