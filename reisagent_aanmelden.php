<?php

include("admin/vars.php");


if(!$vars["wederverkoop"]) {
	header("Location: ".$path);
	exit;
}

$robot_noindex=true;

if($login_rb->logged_in and !$_GET["newagent"]) {
	header("Location: ".$vars["path"]."reisbureau.php");
	exit;
}

if($_GET["newagent"] and (!$login_rb->logged_in or !$login_rb->vars["hoofdgebruiker"])) {
	header("Location: ".$vars["path"]."reisbureau.php");
	exit;
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm"); 
$form->settings["fullname"]="reisbureauaanmelden";
$form->settings["layout"]["css"]=false;
$form->settings["db"]["table"]="reisbureau_user";
#$form->settings["db"]["where"]="user_id='".addslashes($login_rb->user_id)."'";

$form->settings["message"]["submitbutton"]["nl"]="AANMELDEN";
#$form->settings["target"]="_blank";
 
# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen



if(!$_GET["newagent"]) {
	#
	# reisbureau
	#
	$form->field_htmlrow("","<b>".html("organisatiegegevens","reisbureau_overzicht")."</b>");
	$form->field_text(1,"reisbureau_naam",txt("naamorganisatie","reisbureau_overzicht"));
	$form->field_text(0,"reisbureau_verantwoordelijke",txt("verantwoordelijke","reisbureau_overzicht"));
	$form->field_email(0,"reisbureau_email_overeenkomst",txt("emailovereenkomst","reisbureau_overzicht"));
	$form->field_text(0,"reisbureau_anvrnummer",txt("anvrnummer","reisbureau_overzicht"));
	$form->field_text(0,"reisbureau_kvknummer",txt("kvknummer","reisbureau_overzicht"));
	$form->field_text(0,"reisbureau_btwnummer",txt("btwnummer","reisbureau_overzicht"));
	$form->field_htmlrow("","<hr><i>".html("adreskantoor","reisbureau_overzicht")."</i>");
	$form->field_text(1,"reisbureau_adres",txt("adres","reisbureau_overzicht"));
	$form->field_text(1,"reisbureau_postcode",txt("postcode","reisbureau_overzicht"));
	$form->field_text(1,"reisbureau_plaats",txt("plaats","reisbureau_overzicht"));
	if($vars["taal"]=="nl") {
		$pre_land="Nederland";
	}
	$form->field_text(1,"reisbureau_land",txt("land","reisbureau_overzicht"),"",array("text"=>$pre_land));
	$form->field_htmlrow("","<div style=\"\">&darr;&nbsp;<a href=\"#\" onclick=\"fieldcopy('reisbureau_adres','reisbureau_post_adres');fieldcopy('reisbureau_postcode','reisbureau_post_postcode');fieldcopy('reisbureau_plaats','reisbureau_post_plaats');fieldcopy('reisbureau_land','reisbureau_post_land');return false;\">kopieer &quot;bezoekadres&quot; naar &quot;postadres&quot;</a>&nbsp;&darr;</div>");
	
	$form->field_htmlrow("","<i>".html("postadreskantoor","reisbureau_overzicht")."</i>");
	$form->field_text(1,"reisbureau_post_adres",txt("adres","reisbureau_overzicht"));
	$form->field_text(1,"reisbureau_post_postcode",txt("postcode","reisbureau_overzicht"));
	$form->field_text(1,"reisbureau_post_plaats",txt("plaats","reisbureau_overzicht"));
	$form->field_text(1,"reisbureau_post_land",txt("land","reisbureau_overzicht"),"",array("text"=>$pre_land));
	$form->field_htmlrow("","&nbsp;");
	
	$form->field_text(1,"reisbureau_telefoonnummer",txt("telefoonnummer","reisbureau_overzicht"));
	$form->field_text(0,"reisbureau_noodnummer",txt("noodnummer","reisbureau_overzicht"));
	$form->field_url(0,"reisbureau_website",txt("website","reisbureau_overzicht"));
	$form->field_email(1,"reisbureau_email_facturen",txt("email_facturen","reisbureau_overzicht"));
	$form->field_email(1,"reisbureau_email_marketing",txt("email_marketing","reisbureau_overzicht"));
	$form->field_htmlrow("","<hr><b>".html("persoonlijkegegevens","reisbureau_overzicht")."</b><br>");
}

#
# reisagent
#
$form->field_text(0,"code",txt("persoonlijkeagentcode","reisbureau_overzicht"),array("field"=>"code"));
$form->field_text(0,"voornaam",txt("voornaam","reisbureau_overzicht"),array("field"=>"voornaam"));
$form->field_text(0,"tussenvoegsel",txt("tussenvoegsel","reisbureau_overzicht"),array("field"=>"tussenvoegsel"));
$form->field_text(1,"achternaam",txt("achternaam","reisbureau_overzicht"),array("field"=>"achternaam"));
$form->field_email(1,"email",txt("email","reisbureau_overzicht"),array("field"=>"email"));

$form->field_password(1,"password",txt("gewenstwachtwoord","reisagent_aanmelden"),array("field"=>"password"),"",array("new_password"=>true,"strong_password"=>true));
$form->field_password(1,"password2",txt("herhaalwachtwoord","reisbureau_overzicht"));
if($_GET["newagent"]) {
	$form->field_htmlrow("","<hr><b>".html("contactgegevensagent","reisagent_aanmelden").":</b>");
} else {
	$form->field_htmlrow("","<hr><b>".html("mijncontactgegevens","reisbureau_overzicht").":</b>");
}

$form->field_text(0,"adres",txt("adres","reisbureau_overzicht"),array("field"=>"adres"));
$form->field_text(0,"postcode",txt("postcode","reisbureau_overzicht"),array("field"=>"postcode"));
$form->field_text(0,"plaats",txt("plaats","reisbureau_overzicht"),array("field"=>"plaats"));
$form->field_text(0,"land",txt("land","reisbureau_overzicht"),array("field"=>"land"));
$form->field_text(0,"telefoonnummer",txt("telefoonnummer","reisbureau_overzicht"),array("field"=>"telefoonnummer"));
$form->field_text(0,"mobiel",txt("mobiel","reisbureau_overzicht"),array("field"=>"mobiel"));
$form->field_url(0,"website",txt("eigenwebsite","reisbureau_overzicht"),array("field"=>"website"));
$form->field_htmlrow("","<hr>");

#$form->field_yesno("mailingmanager_gewonenieuwsbrief",txt("lidchaletzomerhuisjenieuwsbrief","reisbureau_overzicht"),array("field"=>"mailingmanager_gewonenieuwsbrief"));
#$form->field_yesno("mailingmanager_agentennieuwsbrief",txt("lidagentennieuwsbrief","reisbureau_overzicht"),array("field"=>"mailingmanager_agentennieuwsbrief"));
#$form->field_htmlrow("","<hr>");

if(!$_GET["newagent"]) {
#	$form->field_yesno("akkoord","Ik ga akkoord met de </label><a href=\"javascript:popwindow(600,0,'popup.php?id=agentenvoorwaarden');\">voorwaarden</a>.","","","",array("title_html"=>true));
}
$form->check_input();

if($form->filled) {
	if($form->input["password"] and $form->input["password"]<>$form->input["password2"]) $form->error("password2",html("voer2xwachtwoord","reisbureau_overzicht"));
	if(!$_GET["newagent"]) {
#		if(!$form->input["akkoord"]) $form->error("akkoord","Je dient akkoord te gaan met onze voorwaarden");
	}

	if(!$form->input["voornaam"]) {
		$form->input["voornaam"]="Collega";
	}
	
	if($form->input["email"]) {
		$db->query("SELECT user_id FROM reisbureau_user WHERE email='".addslashes($form->input["email"])."';");
		if($db->next_record()) {
			$form->error("email","het mailadres <i>".htmlentities($form->input["email"])."</i> heeft al een account");
		}
	}
}

if($form->okay) {
	if($_GET["newagent"]) {	
		# Gegevens opslaan in de database
		$form->settings["db"]["set"]="reisbureau_id='".addslashes($login_rb->vars["reisbureau_id"])."', hoofdgebruiker=0, mailingmanager_gewonenieuwsbrief=1, mailingmanager_agentennieuwsbrief=1";
		$form->save_db();
	} else {
	
		# Gegevens opslaan in tabel reisbureau
		$db->query("INSERT INTO reisbureau SET actief=0, websites='T,Z', reserveringskosten=1, beschikbaarheid_inzien=1, commissie_inzien=1, bevestiging_naar_reisbureau=1, aanmaning_naar_reisbureau=1, naam='".addslashes($form->input["reisbureau_naam"])."', verantwoordelijke='".addslashes($form->input["reisbureau_verantwoordelijke"])."', email_overeenkomst='".addslashes($form->input["reisbureau_email_overeenkomst"])."', anvrnummer='".addslashes($form->input["reisbureau_anvrnummer"])."', kvknummer='".addslashes($form->input["reisbureau_kvknummer"])."', btwnummer='".addslashes($form->input["reisbureau_btwnummer"])."', adres='".addslashes($form->input["reisbureau_adres"])."', postcode='".addslashes($form->input["reisbureau_postcode"])."', plaats='".addslashes($form->input["reisbureau_plaats"])."', land='".addslashes($form->input["reisbureau_land"])."', post_adres='".addslashes($form->input["reisbureau_post_adres"])."', post_postcode='".addslashes($form->input["reisbureau_post_postcode"])."', post_plaats='".addslashes($form->input["reisbureau_post_plaats"])."', post_land='".addslashes($form->input["reisbureau_post_land"])."', telefoonnummer='".addslashes($form->input["reisbureau_telefoonnummer"])."', noodnummer='".addslashes($form->input["reisbureau_noodnummer"])."', website='".addslashes($form->input["reisbureau_website"])."', email_facturen='".addslashes($form->input["reisbureau_email_facturen"])."', email_marketing='".addslashes($form->input["reisbureau_email_marketing"])."', adddatetime=NOW(), editdatetime=NOW();");
	#	echo $db->lastquery;
		$nieuwe_id=$db->insert_id();
		if($nieuwe_id) {
			# Gegevens opslaan in de database
			$form->settings["db"]["set"]="reisbureau_id='".addslashes($db->insert_id())."', hoofdgebruiker=1, mailingmanager_gewonenieuwsbrief=1, mailingmanager_agentennieuwsbrief=1";
			$form->save_db();
		}
		
		$mail=new wt_mail;
		$mail->fromname="Website ".$vars["websites"][$vars["website"]];
		$mail->from="info@chalet.nl";
		$mail->to="info@chalet.nl";
		$mail->subject="Aanmelding reisbureau";
		$mail->plaintext="Er heeft zich een nieuw reisbureau aangemeld. Bezoek de volgende pagina om de gegevens goed te keuren en het reisbureau te activeren:\n\nhttp://www.chalet.nl/cms_reisbureaus.php?show=27&27k0=".$nieuwe_id."\n\n";
		$mail->send();
	}
}
$form->end_declaration();

include "content/opmaak.php";

?>