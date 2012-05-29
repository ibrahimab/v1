<?php

$vars["leverancier_mustlogin"]=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$vars["verberg_linkerkolom"]=true;

include("admin/vars.php");

$robot_noindex=true;

if($login_lev->logged_in) {
	
	# Taal bepalen
	if($login_lev->vars["inlog_taal"]) {
		$org_taal=$vars["taal"];
		$vars["taal"]=$login_lev->vars["inlog_taal"];
		if($vars["taal"]<>"nl") $vars["ttv_lev_login"]="_".$vars["taal"];
	}
}

if($login_lev->logged_in and $_GET["t"]==1) {


	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm"); 
	$form->settings["language"]=$login_lev->vars["inlog_taal"];
	$form->settings["fullname"]="lev_login_instellingen";
	$form->settings["layout"]["css"]=false;
	$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
	$form->settings["message"]["submitbutton"]["en"]="SAVE";
	#$form->settings["target"]="_blank";
	
	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen
	
	#_field: (obl),id,title,db,prevalue,options,layout
	$db->query("SELECT adresregels, contactpersoon_contract, email_contract, telefoonnummer_contract, noodnummer FROM leverancier WHERE leverancier_id='".addslashes(addslashes($login_lev->user_id))."';");
	if($db->next_record()) {
		if($db->f("adresregels")) {
			$form->field_htmlcol("",txt("naamenadres","lev_login"),array("html"=>nl2br(wt_he($db->f("adresregels")))));
		} else {
			$form->field_htmlcol("",txt("naam","lev_login"),array("html"=>wt_he($db->f("contactpersoon_contract"))));
		}
		$form->field_htmlcol("",txt("emailadres","lev_login"),array("html"=>wt_he($db->f("email_contract"))));
		$form->field_htmlcol("",txt("telefoonnummer","lev_login"),array("html"=>wt_he($db->f("telefoonnummer_contract"))));
		$form->field_htmlcol("",txt("noodnummer","lev_login"),array("html"=>wt_he($db->f("noodnummer"))));
		
		$form->field_htmlrow("","<br><i>".html("wijzigingendoorgeven","lev_login",array("h_1"=>"<a href=\"mailto:".$vars["email"]."\">".$vars["email"]."</a>"))."</i><br>&nbsp;");
		
	}
	$form->field_password(0,"password",txt("nieuwwachtwoord","lev_login"),"","",array("new_password"=>true,"strong_password"=>true));
	$form->field_password(0,"password2",txt("herhaalwachtwoord","lev_login"));
	
	$form->check_input();
	
	if($form->filled) {
		if($form->input["password"] and $form->input["password"]<>$form->input["password2"]) {
			$form->error("password2",txt("nietgelijk","lev_login"));
		}
	}
	
	if($form->okay) {
		if($form->input["password"]) {
			$password_hash=wt_complex_password_hash($form->input["password"],$vars["salt"]);
			$db->query("UPDATE leverancier SET ".($password_hash ? "password='".addslashes($password_hash)."'" : "")." WHERE leverancier_id='".addslashes($login_lev->user_id)."';");
		}
	}
	$form->end_declaration();
}

include "content/opmaak.php";

?>