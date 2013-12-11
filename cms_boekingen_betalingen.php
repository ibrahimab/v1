<?php


$mustlogin=true;
$boeking_bepaalt_taal=true;
include("admin/vars.php");

if(!$login->has_priv("5") and ($_GET["edit"]==26 or $_GET["add"]==26 or $_GET["delete"]==26)) {
	header("Location: cms.php");
	exit;
}


$gegevens=get_boekinginfo($_GET["bid"]);


if($_POST["opmfilled"]==1) {
	if($_POST["opmerkingen"]<>$gegevens["stap1"]["opmerkingen_intern"]) {
		# Opslaan wanneer opmerkingen zijn gewijzigd
		$setquery=", opmerkingen_intern_gewijzigd=NOW()";
	}
	$db->query("UPDATE boeking SET opmerkingen_intern='".addslashes($_POST["opmerkingen"])."'".$setquery." WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
	cmslog_pagina_title("interne opmerkingen boeking gewijzigd");
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

if($_POST["overbetaling_filled"] and $_GET["bid"]) {
	# Overbetaling verwerken (overboeken naar andere boekingen)
	$db->query("SELECT boekingsnummer FROM boeking WHERE boeking_id='".addslashes($_GET["bid"])."';");
	if($db->next_record()) {
		$boekingsnummer=$db->f("boekingsnummer");
	}
	if(!$boekingsnummer) $boekingsnummer="boeking".$_GET["bid"];
	while(list($key,$value)=@each($_POST["overbetaling"])) {
		$value=floatval(preg_replace("/,/",".",$value));
		if($value<>0) {
			$db->query("INSERT INTO boeking_betaling SET boeking_id='".addslashes($key)."', bedrag='".addslashes($value)."', datum=NOW(), opmerkingen='Overgeboekt van ".addslashes($boekingsnummer)." i.v.m. overbetaling', toon_opmerkingen_in_overzicht=1;");
			$db->query("SELECT boekingsnummer FROM boeking WHERE boeking_id='".addslashes($key)."';");
			if($db->next_record()) {
				$db2->query("INSERT INTO boeking_betaling SET boeking_id='".addslashes($_GET["bid"])."', bedrag='".addslashes(0-$value)."', datum=NOW(), opmerkingen='Overgeboekt naar ".addslashes(($db->f("boekingsnummer") ? $db->f("boekingsnummer") : "boeking".$key))." i.v.m. overbetaling', toon_opmerkingen_in_overzicht=1;");
			}
			$overboektotaal+=$value;
		}
	}
	header("Location: ".$vars["path"]."cms_boekingen_betalingen.php?burl=".$_GET["burl"]."&bid=".$_GET["bid"]);
	exit;
#	echo wt_dump($_POST);
#	exit;
}

if(isset($_GET["goedgekeurde_betaling"]) and $_GET["confirmed"]) {
	$_GET["goedgekeurde_betaling"]=preg_replace("/,/",".",$_GET["goedgekeurde_betaling"]);
	if($_GET["bid"]) {
		$db->query("UPDATE boeking SET goedgekeurde_betaling='".addslashes($_GET["goedgekeurde_betaling"])."' WHERE boeking_id='".addslashes($_GET["bid"])."';");
#		echo $db->lq;
	}
	if($_GET["goedgekeurde_betaling"]>0) {
		chalet_log("betaling t.w.v. € ".number_format($_GET["goedgekeurde_betaling"],2,',','.')." goedgekeurd",false,true);
		cmslog_pagina_title("betaling goedgekeurd");
	} else {
		chalet_log("goedgekeurde betaling ingetrokken",false,true);
		cmslog_pagina_title("goedgekeurde betaling ingetrokken");
	}
	header("Location: ".$vars["path"]."cms_boekingen_betalingen.php?".wt_stripget($_GET,array("goedgekeurde_betaling","confirmed")));
	exit;
}

#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(26,"date","datum");
$cms->db_field(26,"currency","bedrag");
$cms->db_field(26,"textarea","opmerkingen");
$cms->db_field(26,"yesno","mailsturen","",array("notdb"=>true));
$cms->db_field(26,"yesno","goedgekeurde_betaling_wissen","",array("notdb"=>true));
$cms->db_field(26,"yesno","import");
$cms->db_field(26,"select","type","",array("selection"=>$vars["boeking_betaling_type"]));
$cms->db_field(26,"datetime","importdatetime");


# Where-statement
$cms->db[26]["where"]="boeking_id='".addslashes($_GET["bid"])."'";
$cms->db[26]["set"]="boeking_id='".addslashes($_GET["bid"])."'";


#
# List
#
# Te tonen icons/links bij list
$cms->settings[26]["list"]["show_icon"]=false;
if($login->has_priv("5")) {
	$cms->settings[26]["list"]["edit_icon"]=true;
	$cms->settings[26]["list"]["delete_icon"]=true;
	$cms->settings[26]["list"]["add_link"]=true;
} else {
	$cms->settings[26]["list"]["edit_icon"]=false;
	$cms->settings[26]["list"]["delete_icon"]=false;
	$cms->settings[26]["list"]["add_link"]=false;
}

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[26]=array("datum");
$cms->list_field(26,"datum","Datum",array("date_format"=>"DD-MM-JJJJ"));
$cms->list_field(26,"bedrag","Bedrag");
$cms->list_field(26,"type","Soort");
$cms->list_field(26,"import","Via Exact-import");
$cms->list_field(26,"importdatetime","Importmoment",array("date_format"=>"D MAAND JJJJ, UU:ZZ"));

# Controle op delete-opdracht
if($_GET["delete"]==26 and $_GET["26k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(26)) {
	chalet_log("betaling gewist",false,true);
}

#
# Edit
#
# Nieuw record meteen openen na toevoegen
$cms->settings[26]["show"]["goto_new_record"]=false;

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(26,1,"bedrag","","",array("negative"=>true));
$cms->edit_field(26,1,"datum","Betaaldatum",array("time"=>time()),array("startyear"=>2003,"endyear"=>date("Y")+1),array("calendar"=>true));
$cms->edit_field(26,0,"opmerkingen","Opmerkingen");
$cms->edit_field(26,1,"type","Soort betaling");
if($_GET["add"]==26) $cms->edit_field(26,0,"mailsturen","Mail sturen aan klant dat betaling is ontvangen");
if($_GET["add"]==26 and $gegevens["stap1"]["goedgekeurde_betaling"]>0) $cms->edit_field(26,0,"goedgekeurde_betaling_wissen","Eerder goedgekeurde betaling t.w.v. € ".number_format($gegevens["stap1"]["goedgekeurde_betaling"],2,',','.')." wissen",array("selection"=>true));

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(26);
if($cms_form[26]->filled) {

}

if($cms_form[26]->okay) {
	if($_GET["add"]==26 and $cms_form[26]->input["goedgekeurde_betaling_wissen"]) {
		# Eerder goedgekeurde betaling wissen
		$db->query("UPDATE boeking SET goedgekeurde_betaling=0 WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

		chalet_log("eerder goedgekeurde betaling op € 0,00 gezet",true,true);
	}

	chalet_log("betaling ".date("d-m-Y",$cms_form[26]->input["datum"]["unixtime"]).": € ".number_format($cms_form[26]->input["bedrag"],2,',','.'),false,true);
}

function form_before_goto($form) {
	if($_GET["add"]==26 and $form->input["mailsturen"]) {
		// Ontvangstbevestigen mailen aan klant
		$paymentmail = new paymentmail;
		$paymentmail->send_mail($_GET["bid"], $form->input["bedrag"], $form->input["datum"]["unixtime"]);
	}
}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>