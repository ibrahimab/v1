<?php

include("admin/vars.php");

$robot_noindex=true;

$form=new form2("frm");
$form->settings["fullname"]="wachtwoord";
$form->settings["layout"]["css"]=false;
$form->settings["language"]=$vars["taal"];

#_field: (obl),id,title,db,prevalue,options,layout

$form->field_email(1,"email","E-mailadres");

$form->check_input();

if($form->okay) {
	$db->query("SELECT user_id FROM reisbureau_user WHERE email='".addslashes($form->input["email"])."';");
	if($db->next_record()) {
		# Wachtwoord mailen

		# Nieuw wachtwoord aanmaken
		$password=wt_generate_password(6);
		$db2->query("UPDATE reisbureau_user SET password='".addslashes(wt_complex_password_hash($password,$vars["salt"]))."', uniqueid='', uniqueid_ip='', wrongcount=0, wrongtime=0 WHERE user_id='".addslashes($db->f("user_id"))."';");

		$mail=new wt_mail;
		$mail->fromname=$vars["websitenaam"];
		$mail->from=$vars["email"];
		$mail->to=$form->input["email"];
		$mail->subject=txt("nieuwwachtwoord","reisagent_wachtwoord");
		$mail->plaintext=txt("uwnieuwewachtwoordis","reisagent_wachtwoord")." ".$password."\n\n".txt("inloggenkanvia","reisagent_wachtwoord",array("v_url"=>$vars["basehref"]."reisagent.php")). " \n\n".txt("eenmaalingelogdkuntu","reisagent_wachtwoord")."\n\n".txt("metvriendelijkegroet","reisagent_wachtwoord")."\n".txt("medewerkers","reisagent_wachtwoord")." ".$vars["websitenaam"]."\n\n";
		$mail->send();
	} else {
		usleep(500000);
	}
}
$form->end_declaration();

include "content/opmaak.php";

?>