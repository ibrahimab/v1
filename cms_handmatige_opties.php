<?php

$mustlogin=true;

include("admin/vars.php");

if($_GET["edit"]==23 and $_GET["23k0"]) {
	$db->query("SELECT bijkomendekosten_id FROM extra_optie WHERE extra_optie_id='".addslashes($_GET["23k0"])."';");
	if($db->next_record() and $db->f("bijkomendekosten_id")) {
		$bijkomendekosten=true;
	}
}

$db->query("SELECT extra_optie_id, soort, bijkomendekosten_id, persoonnummer, deelnemers FROM extra_optie WHERE boeking_id='".addslashes($_GET["bid"])."';");
while($db->next_record()) {
	if($db->f("bijkomendekosten_id")) {
		$soort[$db->f("extra_optie_id")]="Bijkomende kosten";
		if($db->f("persoonnummer")=="pers" and !$db->f("deelnemers")) {
			$geen_deelnemers[$db->f("extra_optie_id")] = true;
		}
	} else {
		$soort[$db->f("extra_optie_id")]=ucfirst($db->f("soort"));
	}
}

if(!is_array($soort)) $soort[1]="-";

#
# Gegevens uit database halen
#
$temp_gegevens=boekinginfo($_GET["bid"]);

$gegevens["stap1"]=$temp_gegevens["stap1"];

# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
if($temp_gegevens["stap2"][2]) {
	$gegevens["stap2"]=$temp_gegevens["stap2"][2];
} elseif($temp_gegevens["stap2"][1]) {
	$gegevens["stap2"]=$temp_gegevens["stap2"][1];
}

# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
if($temp_gegevens["stap3"][2][2]) {
	$gegevens["stap3"]=$temp_gegevens["stap3"][2];
} elseif($temp_gegevens["stap3"][1][2]) {
	$gegevens["stap3"]=$temp_gegevens["stap3"][1];
}

# Controle op status Geselecteerde opties (2 heeft voorkeur boven 1)
if($temp_gegevens["stap4"][2]) {
	$gegevens["stap4"]=$temp_gegevens["stap4"][2];
	$gegevens["stap4"]["actieve_status"]=2;
	$gegevens["fin"]=$temp_gegevens["fin"][2];
} else {
	$gegevens["stap4"]=$temp_gegevens["stap4"][1];
	$gegevens["stap4"]["actieve_status"]=1;
	$gegevens["fin"]=$temp_gegevens["fin"][1];
}
$gegevens["stap5"]=$temp_gegevens["stap5"];

#$vars["personen"]["alg"]="algemene optie (niet aan personen gekoppeld)";
#$vars["personen"]["ap"]="alle personen";

$vars["keuze_persoonnummer"]["alg"]="algemene optie (niet aan personen gekoppeld)";
$vars["keuze_persoonnummer"]["pers"]="bepaalde personen";


for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
	$vars["personen"][$i]=($i==1 ? "Hoofdboeker" : "Persoon ".$i)." (".wt_naam($gegevens["stap3"][$i]["voornaam"],$gegevens["stap3"][$i]["tussenvoegsel"],$gegevens["stap3"][$i]["achternaam"]).")";
}

$cms->settings[23]["list"]["show_icon"]=false;
$cms->settings[23]["list"]["edit_icon"]=true;
$cms->settings[23]["list"]["delete_icon"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db[23]["where"]="boeking_id='".addslashes($_GET["bid"])."'";
$cms->db[23]["set"]="boeking_id='".addslashes($_GET["bid"])."'";

if(!$_GET["edit"] and !$_GET["add"]) {
	// hide bijkomendekosten "pers" without "deelnemers" in list
	$cms->db[23]["where"] .= " AND ( (bijkomendekosten_id IS NULL) OR (persoonnummer<>'pers') OR (persoonnummer='pers' AND deelnemers<>'') )";
}

$cms->db_field(23,"select","extra_optie_id","",array("selection"=>$soort));
$cms->db_field(23,"text","soort");
$cms->db_field(23,"text","naam");
$cms->db_field(23,"select","persoonnummer","",array("selection"=>$vars["keuze_persoonnummer"]));
$cms->db_field(23,"checkbox","deelnemers","",array("selection"=>$vars["personen"]));
$cms->db_field(23,"integer","alg_aantal");
$cms->db_field(23,"currency","verkoop");
$cms->db_field(23,"currency","inkoop");
$cms->db_field(23,"currency","korting");
$cms->db_field(23,"float","commissie");
#$cms->db_field(23,"yesno","toonnul");
$cms->db_field(23,"yesno","persoonsgegevensgewenst");
$cms->db_field(23,"yesno","verberg_voor_klant");
$cms->db_field(23,"yesno","voucher");
$cms->db_field(23,"text","naam_voucher");
$cms->db_field(23,"text","omschrijving_voucher");
$cms->db_field(23,"textarea","aanvullend_voucher");
$cms->db_field(23,"text","tekstbegin_voucher");
$cms->db_field(23,"text","teksteind_voucher");
$cms->db_field(23,"select","begindag","",array("selection"=>$vars["begineinddagen"]));
$cms->db_field(23,"select","einddag","",array("selection"=>$vars["begineinddagen"]));
$cms->db_field(23,"yesno","geboortedatum_voucher");
$cms->db_field(23,"select","skipas_id","",array("othertable"=>"10","otherkeyfield"=>"skipas_id","otherfield"=>"naam"));
$cms->db_field(23,"select","optieleverancier_id","",array("othertable"=>"24","otherkeyfield"=>"optieleverancier_id","otherfield"=>"naam"));
$cms->db_field(23,"text","leverancierscode");
$cms->db_field(23,"yesno","hoort_bij_accommodatieinkoop");
$cms->db_field(23,"select","optiecategorie","",array("selection"=>$vars["optiecategorie"]));


# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[23]=array("persoonnummer","naam");
$cms->list_field(23,"persoonnummer","Gekoppeld aan");
$cms->list_field(23,"extra_optie_id","Soort");
$cms->list_field(23,"naam","Omschrijving");
$cms->list_field(23,"verkoop","Verkoop");
$cms->list_field(23,"optiecategorie","Categorie");

if($_POST["frm_filled"] and $_POST["input"]["voucher"]) {
	$voucher_obl=1;
}

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")

if($bijkomendekosten) {
	$cms->edit_field(23,1,"htmlrow","<b>Bijkomende kosten");
} else {
	$cms->edit_field(23,1,"htmlrow","<b>Handmatige optie");
	$cms->edit_field(23,1,"verberg_voor_klant","Verberg deze handmatige optie voor de klant","","",array("info"=>"Verbergen kun je gebruiken om de inkoopgegevens van een optie aan te passen (correctie inkoop optie)."));
}
if(!$bijkomendekosten) $cms->edit_field(23,1,"soort","Optie-soort");
$cms->edit_field(23,1,"naam","Omschrijving");
if($geen_deelnemers[$_GET["23k0"]]) {
	$cms->edit_field(23,1,"htmlcol","Gekoppeld aan", array("html"=>"-aan geen enkele deelnemer gekoppeld-"));
} else {
	$cms->edit_field(23,1,"persoonnummer","Gekoppeld aan","",array("noedit"=>$bijkomendekosten));
}
if(!$bijkomendekosten) $cms->edit_field(23,0,"deelnemers","Deelnemers","","",array("one_per_line"=>true));
$cms->edit_field(23,0,"alg_aantal","Aantal keer (alleen voor algemene optie)");
$cms->edit_field(23,1,"verkoop","Verkoopprijs","",array("negative"=>true));
$cms->edit_field(23,1,"inkoop","Inkoopprijs","",array("negative"=>true));
$cms->edit_field(23,0,"korting","Kortingspercentage");
if(!$bijkomendekosten) {
	if($gegevens["stap1"]["reisbureau_user_id"]) {
		$cms->edit_field(23,0,"commissie","Commissiepercentage");
	}
	$cms->edit_field(23,0,"skipas_id","Gekoppeld aan skipasleverancier");
	$cms->edit_field(23,0,"optieleverancier_id","Gekoppeld aan optieleverancier");
	$cms->edit_field(23,0,"leverancierscode","Bestellijstcode");
	$cms->edit_field(23,0,"persoonsgegevensgewenst","Persoonsgegevens gewenst bij deze optie");
	$cms->edit_field(23,0,"htmlrow","<hr><b>Voucher</b>");
	$cms->edit_field(23,0,"voucher","Komt op een voucher",array("selection"=>false));
	$cms->edit_field(23,$voucher_obl,"naam_voucher","Naam van de voucher");
	$cms->edit_field(23,$voucher_obl,"omschrijving_voucher","Omschrijving op de voucher");
	$cms->edit_field(23,0,"aanvullend_voucher","Let-op-tekst op de voucher");
	$cms->edit_field(23,0,"tekstbegin_voucher","Extra tekst eerste dag");
	$cms->edit_field(23,0,"teksteind_voucher","Extra tekst laatste dag");
	$cms->edit_field(23,0,"begindag","Datumaanpassing begin");
	$cms->edit_field(23,0,"einddag","Datumaanpassing eind");
	$cms->edit_field(23,0,"geboortedatum_voucher","Geboortedatum op voucher tonen");
}

$cms->edit_field(23,0,"htmlrow","<hr><b>Leveranciersfactuur</b>");
$cms->edit_field(23,0,"hoort_bij_accommodatieinkoop","Deze kosten worden berekend op de factuur van de accommodatie-leverancier");
$cms->edit_field(23,1,"optiecategorie","Optie-categorie");


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(23);
if($cms_form[23]->filled) {

	if($cms_form[23]->input["persoonnummer"]=="alg" and $cms_form[23]->input["alg_aantal"]=="") {
		$cms_form[23]->error("alg_aantal","verplicht bij een algemene optie");
	} elseif($cms_form[23]->input["persoonnummer"]=="alg" and $cms_form[23]->input["alg_aantal"]=="0") {
		$cms_form[23]->error("alg_aantal","minimaal 1");
	}
	if($cms_form[23]->input["persoonnummer"]<>"alg" and $cms_form[23]->input["alg_aantal"]<>"") {
		$cms_form[23]->error("alg_aantal","alleen bij een algemene optie invullen");
	}

	if($cms_form[23]->input["persoonnummer"]=="alg" and $cms_form[23]->input["voucher"]) {
		$cms_form[23]->error("voucher","Bij een algemene optie kan geen voucher worden aangemaakt");
	}
	if($cms_form[23]->input["persoonnummer"]=="alg" and $cms_form[23]->input["deelnemers"]) {
		$cms_form[23]->error("voucher","Aan een algemene optie kunnen geen personen worden gekoppeld");
	}
	if($cms_form[23]->input["persoonnummer"]=="alg" and $cms_form[23]->input["skipas_id"]) {
		$cms_form[23]->error("voucher","Aan een algemene optie kan geen skipasleverancier worden gekoppeld");
	}
	if($cms_form[23]->input["persoonnummer"]=="alg" and $cms_form[23]->input["optieleverancier_id"]) {
		$cms_form[23]->error("voucher","Aan een algemene optie kan geen optieleverancier worden gekoppeld");
	}
	if($cms_form[23]->input["skipas_id"] and $cms_form[23]->input["optieleverancier_id"]) {
		$cms_form[23]->error("optieleverancier_id","Kies voor een skipasleverancier of een optieleverancier (maar niet allebei)");
	}
	if($cms_form[23]->input["leverancierscode"] and !$cms_form[23]->input["optieleverancier_id"]) {
		$cms_form[23]->error("leverancierscode","Alleen nodig indien gekoppeld aan een optieleverancier");
	}
	if($cms_form[23]->input["skipas_id"] and $cms_form[23]->input["hoort_bij_accommodatieinkoop"]) {
		$cms_form[23]->error("skipas_id","niet mogelijk bij 'Deze kosten worden berekend op de factuur van de accommodatie-leverancier'");
	}
	if($cms_form[23]->input["optieleverancier_id"] and $cms_form[23]->input["hoort_bij_accommodatieinkoop"]) {
		$cms_form[23]->error("optieleverancier_id","niet mogelijk bij 'Deze kosten worden berekend op de factuur van de accommodatie-leverancier'");
	}
	if($cms_form[23]->input["hoort_bij_accommodatieinkoop"] and $cms_form[23]->input["optiecategorie"]>2) {
		$cms_form[23]->error("optiecategorie","niet van toepassing op de factuur van de accommodatie-leverancier");
	}
	if($cms_form[23]->input["optiecategorie"]==1) {
		# optiecategorie: "n.v.t.": mag niet als er bedragen zijn ingevuld
		if($cms_form[23]->input["verkoop"]<>0 or $cms_form[23]->input["inkoop"]<>0) {
			$cms_form[23]->error("optiecategorie","'n.v.t.' niet mogelijk als er verkoop- en/of inkoopbedragen zijn ingevuld");
		}
	}
	if($cms_form[23]->input["verberg_voor_klant"] and $cms_form[23]->input["verkoop"]<>0) {
		$cms_form[23]->error("verkoop","bij verbergen voor klant niet van toepassing");
	}
	if($cms_form[23]->input["verberg_voor_klant"] and $cms_form[23]->input["voucher"]) {
		$cms_form[23]->error("voucher","bij verbergen voor klant is een voucher niet van toepassing");
	}
	if($cms_form[23]->input["verberg_voor_klant"] and $cms_form[23]->input["persoonsgegevensgewenst"]) {
		$cms_form[23]->error("persoonsgegevensgewenst","bij verbergen voor klant zijn persoonsgegevens niet van toepassing");
	}
#	if($cms_form[23]->input["verberg_voor_klant"] and $cms_form[23]->input["persoonnummer"]=="alg") {
#		$cms_form[23]->error("persoonnummer","bij verbergen voor klant zijn algemene opties niet mogelijk");
#	}
	if($cms_form[23]->input["verberg_voor_klant"] and $cms_form[23]->input["skipas_id"]) {
		$cms_form[23]->error("skipas_id","bij verbergen voor klant zijn skipassen niet mogelijk");
	}

	# Zorgen dat voucher-gegevens alleen ingevuld kunnen worden indien voucher-vinkje aan staat
	if(!$cms_form[23]->input["voucher"]) {
		if($cms_form[23]->input["naam_voucher"]) $cms_form[23]->error("naam_voucher","alleen invullen indien 'Komt op een voucher' aan staat");
		if($cms_form[23]->input["omschrijving_voucher"]) $cms_form[23]->error("omschrijving_voucher","alleen invullen indien 'Komt op een voucher' aan staat");
		if($cms_form[23]->input["aanvullend_voucher"]) $cms_form[23]->error("aanvullend_voucher","alleen invullen indien 'Komt op een voucher' aan staat");
	}
}

if($cms_form[23]->okay) {
	# Loggen
	chalet_log(($cms_form[23]->input["soort"] ? "handmatige optie \"".$cms_form[23]->input["soort"].": " : "Bijkomende kosten aangepast: \"").$cms_form[23]->input["naam"]."\"",($_GET["add"]==23 ? true : false),true);
}

# Controle op delete-opdracht
if($_GET["delete"]==23 and $_GET["23k0"]) {

}

if($cms->set_delete_init(23)) {
	chalet_log("handmatige optie verwijderd",true,true);
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>