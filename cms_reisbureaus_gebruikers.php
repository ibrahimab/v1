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

$cms->settings[28]["list"]["show_icon"]=false;
$cms->settings[28]["list"]["edit_icon"]=true;
$cms->settings[28]["list"]["delete_icon"]=true;

$cms->settings[28]["show"]["goto_new_record"]=false;


#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(28,"text","user");
$cms->db_field(28,"text","code");
$cms->db_field(28,"text","naam");
$cms->db_field(28,"text","voornaam");
$cms->db_field(28,"text","tussenvoegsel");
$cms->db_field(28,"text","achternaam");
$cms->db_field(28,"text","adres");
$cms->db_field(28,"text","postcode");
$cms->db_field(28,"text","plaats");
$cms->db_field(28,"text","land");
$cms->db_field(28,"text","telefoonnummer");
$cms->db_field(28,"text","mobiel");
$cms->db_field(28,"email","email");
$cms->db_field(28,"url","website");
$cms->db_field(28,"textarea","opmerkingen");
$cms->db_field(28,"date","lastlogin");
$cms->db_field(28,"password","password");
$cms->db_field(28,"yesno","hoofdgebruiker");
$cms->db_field(28,"yesno","inzicht_boekingen");
$cms->db_field(28,"yesno","inzicht_prijsberekeningen");
$cms->db_field(28,"yesno","bevestiging_naar_gebruiker");
$cms->db_field(28,"yesno","aanmaning_naar_gebruiker");
#$cms->db_field(28,"yesno","mailingmanager_gewonenieuwsbrief");
#$cms->db_field(28,"yesno","mailingmanager_agentennieuwsbrief");
$cms->db_field(28,"picture","logo","",array("savelocation"=>"pic/cms/reisagent_logo/","filetype"=>"jpg"));

$cms->db[28]["set"]="reisbureau_id='".addslashes($_GET["27k0"])."'";
$cms->db[28]["where"]="reisbureau_id='".addslashes($_GET["27k0"])."'";

#
# List
#
# Te tonen icons/links bij list
$cms->settings[28]["list"]["show_icon"]=false;
$cms->settings[28]["list"]["edit_icon"]=true;
$cms->settings[28]["list"]["delete_icon"]=true;
$cms->settings[28]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[28]=array("naam");
#$cms->list_field(28,"user","Gebruikersnaam");
$cms->list_field(28,"naam","Naam");
$cms->list_field(28,"lastlogin","Laatste login",array("date_format"=>"DAG D MAAND JJJJ, U:ZZ"));


# Controle op delete-opdracht
if($_GET["delete"]==28 and $_GET["28k0"]) {
	$db->query("SELECT boeking_id FROM boeking WHERE reisbureau_user_id='".addslashes($_GET["28k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(28,"Deze reisbureau-gebruiker is nog gekoppeld aan een boeking");
	}
}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(28)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
#$cms->edit_field(28,1,"naam","Naam");
$cms->edit_field(28,0,"code","Persoonlijke agentcode");
#$cms->edit_field(28,1,"naam","Naam");

$cms->edit_field(28,1,"voornaam","Voornaam");
$cms->edit_field(28,0,"tussenvoegsel","Tussenvoegsel");
$cms->edit_field(28,1,"achternaam","Achternaam");
$cms->edit_field(28,1,"email","E-mailadres (=gebruikersnaam)");
#$cms->edit_field(28,1,"user","Gebruikersnaam (niet meer in gebruik)");

if($_GET["add"]==28 and $vars["taal"]=="nl") {
	$cms->edit_field(28,0,"htmlrow","<input type=\"checkbox\" id=\"reisbureau_gebruiker_mailen\" name=\"reisbureau_gebruiker_mailen\"".($_POST["reisbureau_gebruiker_mailen"] ? " checked" : "")."><label for=\"reisbureau_gebruiker_mailen\">&nbsp;&nbsp;Stuur deze nieuwe gebruiker een welkomstmail (wachtwoord wordt automatisch aangemaakt)</label>");
}

$cms->edit_field(28,0,"password","Wachtwoord","",array("new_password"=>true,"strong_password"=>true));

$cms->edit_field(28,0,"htmlrow","<hr><b>Contactgegevens (indien afwijkend van bovenliggend reisbureau)</b>");
$cms->edit_field(28,0,"adres","Adres");
$cms->edit_field(28,0,"postcode","Postcode");
$cms->edit_field(28,0,"plaats","Plaats");
$cms->edit_field(28,0,"land","Land");
$cms->edit_field(28,0,"telefoonnummer","Telefoonnummer");
$cms->edit_field(28,0,"mobiel","Mobiel nummer");
$cms->edit_field(28,0,"website","Eigen website");
$cms->edit_field(28,0,"htmlrow","<hr><b>Diverse instellingen</b>");

$cms->edit_field(28,1,"hoofdgebruiker","Deze agent is een hoofdgebruiker (mag organisatie-gegevens wijzigen)");
$cms->edit_field(28,1,"inzicht_boekingen","Deze agent heeft inzicht in de boekingen van andere gebruikers van dit reisbureau",array("selection"=>1));
$cms->edit_field(28,1,"inzicht_prijsberekeningen","Deze agent heeft inzicht in de prijsberekeningen van andere gebruikers van dit reisbureau",array("selection"=>1));

$cms->edit_field(28,0,"htmlrow","<hr><b>Mailinstellingen</b><br><br><i>Indien deze vinkjes zowel op reisbureau-niveau als gebruiker-niveau uit staan, dan worden de mails verstuurd naar deze gebruiker.</i>");
$cms->edit_field(28,1,"bevestiging_naar_gebruiker","Bevestigingen naar deze agent sturen",array("selection"=>1));
$cms->edit_field(28,1,"aanmaning_naar_gebruiker","Aanmaningen en ontvangstbevestigingen naar deze agent sturen",array("selection"=>1));
$cms->edit_field(28,0,"htmlrow","<hr><b>Nieuwsbrieven</b>");

#$cms->edit_field(28,1,"mailingmanager_gewonenieuwsbrief","Lid van de Chalet.nl/Zomerhuisje.nl-nieuwsbrief",array("selection"=>1));
#$cms->edit_field(28,1,"mailingmanager_agentennieuwsbrief","Lid van de agenten-nieuwsbrief",array("selection"=>1));

$cms->edit_field(28,0,"htmlrow","<hr>");
$cms->edit_field(28,0,"logo","Logo reisagent");
$cms->edit_field(28,0,"htmlrow","<hr>");
$cms->edit_field(28,0,"opmerkingen","Interne opmerkingen");

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(28);
if($cms_form[28]->filled) {
#	if(!ereg("^[0-9a-z]+$",$cms_form[28]->input["user"])) {
#		$cms_form[28]->error("user","alleen kleine letters en cijfers toegestaan");
#	}
#	$db->query("SELECT user_id FROM reisbureau_user WHERE user='".addslashes($cms_form[28]->input["user"])."' AND user_id<>'".addslashes($_GET["28k0"])."';");
#	if($db->num_rows()) {
#		$cms_form[28]->error("user","gebruikersnaam bestaat al");
#	}

	$db->query("SELECT user_id FROM reisbureau_user WHERE email='".addslashes($cms_form[28]->input["email"])."' AND user_id<>'".addslashes($_GET["28k0"])."';");
	if($db->num_rows()) {
		$cms_form[28]->error("email","E-mailadres bestaat al");
	}
}

# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars;
	if($_GET["add"]==28 and $form->db_insert_id and $_POST["reisbureau_gebruiker_mailen"]) {
		#
		# Welkomstmail versturen
		#

		if($form->input["password"]) {
			# Wachtwoord ingevoerd
			$password=$form->input["password"];
		} else {
			# Wachtwoord aanmaken
			$password=wt_generate_password(6);
			$db->query("UPDATE reisbureau_user SET password='".addslashes(md5($password))."' WHERE user_id='".addslashes($form->db_insert_id)."';");
		}
		# Links naar sites
		$db->query("SELECT websites FROM reisbureau WHERE reisbureau_id='".addslashes($_GET["27k0"])."';");
		if($db->next_record()) {
			$websites_array=split(",",$db->f("websites"));
			while(list($key,$value)=each($websites_array)) {
				if($links_naar_sites) $links_naar_sites.="\n";
				$links_naar_sites.=$vars["websiteinfo"]["basehref"][$value]."reisagent.php";
			}
		}
		if(!$links_naar_sites) $links_naar_sites="http://www.chalettour.nl/reisagent.php";

		$mail=new wt_mail;
		$mail->fromname="Chalet.nl";
		$mail->from="info@chalet.nl";
		if($login->vars["email"]) {
			$mail->bcc=$login->vars["email"];
		}

		$mail->to=$form->input["email"];

		$mail->subject="Inloggegevens Chalettour.nl";

		$mail->plaintext=txt("welkomstmail","reisbureau",array("v_websites"=>$links_naar_sites,"v_email"=>$form->input["email"],"v_wachtwoord"=>$password,"v_voornaam"=>trim($form->input["voornaam"])));
		$mail->send();
	}
	if($_GET["edit"]==28) {
		$db->query("UPDATE reisbureau_user SET wrongcount=0, wrongtime=0 WHERE user_id='".addslashes($_GET["28k0"])."';");
	}
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>