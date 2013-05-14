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
	$db->query("SELECT user_id, password_uc, wrongcount FROM boekinguser WHERE user='".addslashes($form->input["email"])."';");
	if($db->next_record()) {
		# Wachtwoord mailen
		if($db->f("password_uc") and $db->f("wrongcount")<50) {
			# Ongecodeerd wachtwoord gebruiken
			$password=$db->f("password_uc");
		} else {
			# Nieuw wachtwoord aanmaken
			$password=wt_generate_password(6,false);
			$db->query("UPDATE boekinguser SET password='".addslashes(md5($password))."', password_uc='".addslashes($password)."', uniqueid='', wrongcount=0 WHERE user='".addslashes($form->input["email"])."';");
		}
		$mail=new wt_mail;
		$mail->fromname=$vars["websitenaam"];
		$mail->from=$vars["email"];
		$mail->to=$form->input["email"];
		$mail->subject=txt("nieuwwachtwoord","wachtwoord");
		$mail->plaintext=txt("uwnieuwewachtwoordis","wachtwoord")." ".$password."\n\n".txt("inloggenkanvia","wachtwoord",array("v_url"=>$vars["basehref"].txt("menu_inloggen").".php")). " \n\n".txt("eenmaalingelogdkuntu","wachtwoord")."\n\n".txt("metvriendelijkegroet","wachtwoord")."\n".txt("medewerkers","wachtwoord")." ".$vars["websitenaam"]."\n\n";
		$mail->send();
	} else {
		usleep(500000);
	}
}
$form->end_declaration();

include "content/opmaak.php";

?>
