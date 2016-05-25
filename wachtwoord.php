<?php

$my_booking = true;

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
	$db->query("SELECT user_id, wrongcount FROM boekinguser WHERE user='".addslashes($form->input["email"])."';");
	if($db->next_record()) {
		# Wachtwoord mailen
		if($db->f("wrongcount")>=50) {
			# Nieuw wachtwoord aanmaken
			$password = wt_generate_password(6,false);
			$db->query("UPDATE boekinguser SET password='".addslashes(wt_complex_password_hash($password,$vars["salt"]))."', uniqueid='', uniqueid_ip='', wrongcount=0 WHERE user='".addslashes($form->input["email"])."';");
		}

		$directlogin = new directlogin;
		$directlogin_link=$directlogin->maak_link($vars["website"],1,$db->f("user_id"));

		# Button
		$inlogbutton.="<p><table cellspacing=\"0\" cellpadding=\"0\"><tr><td align=\"center\" width=\"200\" height=\"30\" bgcolor=\"".$table."\" style=\"-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: ".$thfontcolor."; display: block;\"><a href=\"".wt_he($directlogin_link)."\" style=\"color: ".$thfontcolor."; font-size:11px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; text-decoration: none; line-height:30px; width:100%; display:inline-block\">".html("directinloggen","boeken")."</a></td></tr></table></p>";

		$body=nl2br(html("mailcontent","wachtwoord",array("v_wachtwoord"=>$password,"h_button"=>$inlogbutton,"h_1"=>"<strong>","h_2"=>"</strong>")));

		verstuur_opmaakmail($vars["website"],$form->input["email"],"",txt("mailsubject","wachtwoord",array("v_website"=>$vars["websitenaam"])),$body,array(""));

	} else {
		$form->error("email",txt("mailadresonbekend","wachtwoord"));
	}
}
$form->end_declaration();

include "content/opmaak.php";
