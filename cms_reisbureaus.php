<?php

#
#
#

$mustlogin=true;

include("admin/vars.php");

if(!$login->has_priv("23")) {
	header("Location: cms.php");
	exit;
}

$vars["wederverkoop_sites"]=array("T"=>"Chalettour.nl","Z"=>"Zomerhuisje.nl","V"=>"ChaletsInVallandry.nl","Q"=>"ChaletsInVallandry.com","I"=>"Italissima","E"=>"Chalet.eu");

# Toegevoegde landen, leveranciers, accommodaties en types opslaan
if($_POST["toevoegen_filled"]) {
	if($_POST["landen"]) {
		$db->query("INSERT INTO reisbureau_xml_land SET land_id='".addslashes($_POST["landen"])."', wzt='".addslashes($_POST["wzt"])."', reisbureau_id='".addslashes($_GET["27k0"])."';");
		$goto="land";
	}
	if($_POST["leveranciers"]) {
		$db->query("INSERT INTO reisbureau_xml_leverancier SET leverancier_id='".addslashes($_POST["leveranciers"])."', wzt='".addslashes($_POST["wzt"])."', reisbureau_id='".addslashes($_GET["27k0"])."';");
		$goto="leverancier";
	}
	if($_POST["accommodaties"]) {
		$db->query("INSERT INTO reisbureau_xml_accommodatie SET accommodatie_id='".addslashes($_POST["accommodaties"])."', wzt='".addslashes($_POST["wzt"])."', reisbureau_id='".addslashes($_GET["27k0"])."';");
		$goto="accommodatie";
	}
	if($_POST["types"]) {
		$db->query("INSERT INTO reisbureau_xml_type SET type_id='".addslashes($_POST["types"])."', wzt='".addslashes($_POST["wzt"])."', reisbureau_id='".addslashes($_GET["27k0"])."';");
		$goto="type";
	}
	header("Location: ".$_SERVER["REQUEST_URI"]."#".$goto.$_POST["wzt"]);
	exit;
}

# landen, leveranciers, accommodaties en types wissen
if($_GET["deltable"] and $_GET["delrecord"]) {
	$db->query("DELETE FROM reisbureau_xml_".addslashes($_GET["deltable"])." WHERE ".addslashes($_GET["deltable"])."_id='".addslashes($_GET["delrecord"])."' AND reisbureau_id='".addslashes($_GET["27k0"])."' AND wzt='".addslashes($_GET["wzt"])."';");
#	echo $db->lastquery;
	header("Location: cms_reisbureaus.php?".wt_stripget($_GET,array("deltable","delrecord","wzt"))."#".$_GET["deltable"].$_GET["wzt"]);
	exit;
}


$cms->settings[27]["list"]["show_icon"]=true;
$cms->settings[27]["list"]["edit_icon"]=true;
$cms->settings[27]["list"]["delete_icon"]=true;

$cms->settings[27]["show"]["goto_new_record"]=true;

if($_GET["t"]==1) {
	# actief
	$cms->db[27]["where"]="actief=1";
} elseif($_GET["t"]==2) {
	# inactief
	$cms->db[27]["where"]="actief=0";
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(27,"yesno","actief");
$cms->db_field(27,"yesno","goedgekeurd");
$cms->db_field(27,"text","naam");
$cms->db_field(27,"text","adres");
$cms->db_field(27,"text","postcode");
$cms->db_field(27,"text","plaats");
$cms->db_field(27,"text","land");
$cms->db_field(27,"text","post_adres");
$cms->db_field(27,"text","post_postcode");
$cms->db_field(27,"text","post_plaats");
$cms->db_field(27,"text","post_land");
$cms->db_field(27,"text","telefoonnummer");
$cms->db_field(27,"text","noodnummer");
$cms->db_field(27,"url","website");
$cms->db_field(27,"checkbox","websites","",array("selection"=>$vars["wederverkoop_sites"]));
$cms->db_field(27,"email","email_overeenkomst");
$cms->db_field(27,"text","verantwoordelijke");
$cms->db_field(27,"text","anvrnummer");
$cms->db_field(27,"text","btwnummer");
$cms->db_field(27,"yesno","btw_over_commissie");
$cms->db_field(27,"text","kvknummer");
$cms->db_field(27,"email","email_facturen");
$cms->db_field(27,"email","email_marketing");
$cms->db_field(27,"float","aanpassing_commissie");
$cms->db_field(27,"yesno","reserveringskosten");
$cms->db_field(27,"yesno","geenaanbetaling");
$cms->db_field(27,"integer","aanbetaling1_dagennaboeken");
$cms->db_field(27,"integer","totale_reissom_dagenvooraankomst");
$cms->db_field(27,"textarea","opmerkingen");
$cms->db_field(27,"yesno","beschikbaarheid_inzien");
$cms->db_field(27,"yesno","commissie_inzien");
$cms->db_field(27,"yesno","bevestiging_naar_reisbureau");
$cms->db_field(27,"yesno","aanmaning_naar_reisbureau");
$cms->db_field(27,"yesno","xmlfeed");
$cms->db_field(27,"yesno","xmlfeed_winter");
$cms->db_field(27,"yesno","xmlfeed_zomer");
$cms->db_field(27,"text","xmlfeed_toegangscode");
$cms->db_field(27,"yesno","mailblokkeren_opties");
$cms->db_field(27,"select","verzendmethode_reisdocumenten","",array("selection"=>$vars["verzendmethode_reisdocumenten_inclusief_nvt"]));
$cms->db_field(27,"picture","logo","",array("savelocation"=>"pic/cms/reisbureau_logo/","filetype"=>"jpg"));


# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[27]=array("naam","plaats","goedgekeurd","websites");
$cms->list_field(27,"naam","Naam");
$cms->list_field(27,"plaats","Plaats");
$cms->list_field(27,"websites","Sites");
#$cms->list_field(27,"actief","Actief");
$cms->list_field(27,"goedgekeurd","Goedgekeurd");
$cms->list_field(27,"btw_over_commissie","BTW");


# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(27,1,"actief","Actief",array("selection"=>1));
$cms->edit_field(27,1,"goedgekeurd","Goedgekeurd");
$cms->edit_field(27,1,"naam","Naam organisatie");
$cms->edit_field(27,0,"verantwoordelijke"," Verantwoordelijke m.b.t. samenwerking");
$cms->edit_field(27,0,"email_overeenkomst","E-mailadres t.b.v. samenwerkingsovereenkomst");
$cms->edit_field(27,0,"anvrnummer","ANVR-nummer");
$cms->edit_field(27,0,"kvknummer","KvK-nummer");
$cms->edit_field(27,0,"btwnummer","BTW-nummer");
$cms->edit_field(27,0,"btw_over_commissie","BTW over commissie rekenen");
$cms->edit_field(27,0,"htmlrow","<hr><b>Bezoekadres</b>");
$cms->edit_field(27,1,"adres","Adres");
$cms->edit_field(27,1,"postcode","Postcode");
$cms->edit_field(27,1,"plaats","Plaats");
$cms->edit_field(27,1,"land","Land");
$cms->edit_field(27,0,"htmlrow","<div style=\"\">&darr;&nbsp;<a href=\"#\" onclick=\"fieldcopy('adres','post_adres');fieldcopy('postcode','post_postcode');fieldcopy('plaats','post_plaats');fieldcopy('land','post_land');return false;\">kopieer &quot;adres&quot; naar &quot;postadres&quot;</a>&nbsp;&darr;</div><hr><b>Postadres</b>");
$cms->edit_field(27,1,"post_adres","Adres");
$cms->edit_field(27,1,"post_postcode","Postcode");
$cms->edit_field(27,1,"post_plaats","Plaats");
$cms->edit_field(27,1,"post_land","Land");
$cms->edit_field(27,0,"htmlrow","<hr>");
$cms->edit_field(27,1,"telefoonnummer","Telefoonnummer algemeen");
$cms->edit_field(27,0,"noodnummer","Noodnummer");
$cms->edit_field(27,0,"website","Website");
$cms->edit_field(27,0,"email_facturen","E-mailadres facturen + aanmaningen");
$cms->edit_field(27,0,"email_marketing","E-mailadres marketing algemeen");
$cms->edit_field(27,0,"htmlrow","<hr>");
$cms->edit_field(27,0,"aanpassing_commissie","Aanpassing commissie (positief/negatief)","",array("noedit"=>($login->has_priv("7") ? false : true),"negative"=>true));
$cms->edit_field(27,1,"reserveringskosten","Reserveringskosten in rekening brengen",array("selection"=>1));
$cms->edit_field(27,1,"beschikbaarheid_inzien","Mag beschikbaarheid inzien",array("selection"=>1));
$cms->edit_field(27,1,"commissie_inzien","Mag commissie inzien",array("selection"=>1));
$cms->edit_field(27,1,"bevestiging_naar_reisbureau","Bevestigingen naar reisbureau sturen",array("selection"=>1));
$cms->edit_field(27,1,"aanmaning_naar_reisbureau","Aanmaningen en ontvangstbevestigingen naar reisbureau sturen",array("selection"=>1));
$cms->edit_field(27,0,"mailblokkeren_opties","Stuur dit reisbureau geen mail met uitnodiging tot inloggen en opties bijboeken");
$cms->edit_field(27,0,"verzendmethode_reisdocumenten","Standaard-verzendmethode reisdocumenten");
$cms->edit_field(27,1,"htmlrow","<hr><b>Websites waar reisbureau mag inloggen</b>");
$cms->edit_field(27,0,"websites","Websites",array("selection"=>"T,Z"),"",array("one_per_line"=>true));
$cms->edit_field(27,1,"htmlrow","<hr><b>Afwijkende betalingsvoorwaarden</b>");
$cms->edit_field(27,1,"htmlrow","<i>alleen invullen indien afwijkend</i>");
$cms->edit_field(27,1,"geenaanbetaling","Reisbureau hoeft geen aanbetalingen te doen");
$cms->edit_field(27,0,"aanbetaling1_dagennaboeken","Aanbetaling 1: dagen na boeken");
$cms->edit_field(27,0,"totale_reissom_dagenvooraankomst","Eindbetaling: dagen voor aankomst");
#$cms->db_field(27,"integer","aanbetaling1_dagennaboeken");
#$cms->db_field(27,"integer","totale_reissom_dagenvooraankomst");
#$cms->edit_field(27,1,"htmlrow","<hr><b>Diversen</b>");

$cms->edit_field(27,1,"htmlrow","<hr>");
$cms->edit_field(27,0,"logo","Logo reisbureau");

$cms->edit_field(27,1,"htmlrow","<hr>");
$cms->edit_field(27,0,"opmerkingen","Interne opmerkingen");
$cms->edit_field(27,1,"htmlrow","<hr><b>XML-feed</b>");
$cms->edit_field(27,0,"xmlfeed","Deze partner toegang bieden tot een XML-feed","","",array("onclick"=>"if(document.forms['frm'].elements['input[xmlfeed]'].checked&&document.forms['frm'].elements['input[xmlfeed_toegangscode]'].value=='') document.forms['frm'].elements['input[xmlfeed_toegangscode]'].value='".wt_create_id("reisbureau","xmlfeed_toegangscode",8)."';"));
$cms->edit_field(27,0,"xmlfeed_winter","winteraccommodaties opnemen in de XML-feed");
$cms->edit_field(27,0,"xmlfeed_zomer","zomeraccommodaties opnemen in de XML-feed");
$cms->edit_field(27,0,"xmlfeed_toegangscode","Toegangscode");

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[27]="reisbureaugegevens";
$cms->show_mainfield[1]="naam";
$cms->show_field(27,"naam");
$cms->show_field(27,"adres");
$cms->show_field(27,"postcode");
$cms->show_field(27,"plaats");
$cms->show_field(27,"land");
$cms->show_field(27,"telefoonnummer");
$cms->show_field(27,"noodnummer");
$cms->show_field(27,"opmerkingen");
$cms->show_field(27,"actief");

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(27);
if($cms_form[27]->filled) {
	if($cms_form[27]->input["geenaanbetaling"] and $cms_form[27]->input["aanbetaling1_dagennaboeken"]) {
		$cms_form[27]->error("aanbetaling1_dagennaboeken","invullen niet mogelijk bij \"geen aanbetalingen\"");
	}

	if($cms_form[27]->input["aanbetaling1_dagennaboeken"]=="0") {
		$cms_form[27]->error("aanbetaling1_dagennaboeken","0 is een onjuiste waarde");
	}

	if($cms_form[27]->input["bevestiging_naar_reisbureau"] and ! $cms_form[27]->input["email_facturen"]) {
		$cms_form[27]->error("email_facturen","verplicht bij \"Bevestigingen naar reisbureau sturen\"");
	}
	if($cms_form[27]->input["aanmaning_naar_reisbureau"] and ! $cms_form[27]->input["email_facturen"]) {
		$cms_form[27]->error("email_facturen","verplicht bij \"Aanmaningen naar reisbureau sturen\"");
	}
	if($cms_form[27]->input["xmlfeed"]) {
		if(!$cms_form[27]->input["xmlfeed_toegangscode"]) {
			$cms_form[27]->error("xmlfeed_toegangscode","de toegangscode is verplicht bij een XML-feed");
		}
		if(!$cms_form[27]->input["xmlfeed_winter"] and !$cms_form[27]->input["xmlfeed_zomer"]) {
			$cms_form[27]->error("xmlfeed","kies minstens 1 soort XML-seizoen");
		}
	}
	if($cms_form[27]->input["xmlfeed_toegangscode"]) {
		if(!ereg("^[a-z0-9]{8}$",$cms_form[27]->input["xmlfeed_toegangscode"])) {
			$cms_form[27]->error("xmlfeed_toegangscode","onjuiste code (8 letters en/of cijfers");
		} else {
			if($_GET["27k0"]) {
				$db->query("SELECT reisbureau_id, naam FROM reisbureau WHERE xmlfeed_toegangscode='".addslashes($cms_form[27]->input["xmlfeed_toegangscode"])."' AND reisbureau_id<>'".addslashes($_GET["27k0"])."';");
				if($db->next_record()) {
					$cms_form[27]->error("xmlfeed_toegangscode","code bestaat al bij ".$db->f("naam"));
				}
			} else {
				$db->query("SELECT reisbureau_id, naam FROM reisbureau WHERE xmlfeed_toegangscode='".addslashes($cms_form[27]->input["xmlfeed_toegangscode"])."';");
				if($db->next_record()) {
					$cms_form[27]->error("xmlfeed_toegangscode","code bestaat al bij ".$db->f("naam"));
				}
			}
		}
	}
}


# Controle op delete-opdracht
if($_GET["delete"]==27 and $_GET["27k0"]) {
	$db->query("SELECT user_id FROM reisbureau_user WHERE reisbureau_id='".addslashes($_GET["27k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(27,"Dit reisbureau bevat nog gekoppelde reisbureau-gebruikers");
	}
}

#
# DELETEn van andere tabellen
#
if($cms->set_delete_init(27)) {

}






#
# reisbureau_user
#


$cms->settings[27]["connect"][]=28;
$cms->settings[28]["parent"]=27;
$cms->db[28]["where"]="reisbureau_id='".addslashes($_GET["27k0"])."'";


#
#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(28,"text","naam");
$cms->db_field(28,"email","email");
$cms->db_field(28,"date","lastlogin");
$cms->db_field(28,"text","voornaam");
$cms->db_field(28,"text","tussenvoegsel");
$cms->db_field(28,"text","achternaam");

#
# List
#
# Te tonen icons/links bij list
$cms->settings[28]["list"]["show_icon"]=false;
$cms->settings[28]["list"]["edit_icon"]=true;
$cms->settings[28]["list"]["delete_icon"]=true;
$cms->settings[28]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[28]=array("achternaam","voornaam","email");
#$cms->list_field(28,"naam","Naam");
$cms->list_field(28,"voornaam","Voornaam");
$cms->list_field(28,"tussenvoegsel","Tussenvoegsel");
$cms->list_field(28,"achternaam","Achternaam");
$cms->list_field(28,"email","E-mail");
$cms->list_field(28,"lastlogin","Laatste login",array("date_format"=>"DAG D MAAND JJJJ, U:ZZ"));

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(28)) {

}


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>