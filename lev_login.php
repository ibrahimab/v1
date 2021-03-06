<?php

$vars["leverancier_mustlogin"]=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$vars["verberg_linkerkolom"]=true;
$laat_titel_weg=true;

include("admin/vars.php");

$robot_noindex=true;

if($login_lev->logged_in) {


	if($_GET["roominglist"]) {
		if($login_lev->vars["inlog_toon_roominglists"]) {

			// roominglist tonen

			$roominglist = new roominglist;
			$roominglist->leverancier_id = intval($login_lev->user_id);

			// if($login_lev->vars["roominglist_garanties_doorgeven"]) {
			// 	$roominglist->garanties_doorgeven=$login_lev->vars["roominglist_garanties_doorgeven"];
			// } else {
			// 	$roominglist->garanties_doorgeven="0";
			// }

			$vars["create_list"]=$roominglist->create_list();

			$filename = $vars["unixdir"]."tmp/roominglist-".$login_lev->user_id."-".time().".doc";
			$roominglist->word_bestand(array("save_filename"=>$filename));

			header("Content-type: application/msword");
			header("Content-Disposition: attachment; filename=roominglist-".date("Y-m-d").".doc");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

			readfile($filename);

			unlink($filename);
			exit;

		}
		exit;
	}

	# Taal bepalen
	if($login_lev->vars["inlog_taal"]) {
		$org_taal=$vars["taal"];
		$vars["taal"]=$login_lev->vars["inlog_taal"];
		if($vars["taal"]<>"nl") $vars["ttv_lev_login"]="_".$vars["taal"];
	}

	# uitlog-link bovenaan tonen
	unset($helemaalboven);
	$helemaalboven=wt_he($login_lev->vars["naam"])."&nbsp;&nbsp;";
	if($_GET["t"]) {
		$helemaalboven.="<a href=\"".$vars["path"]."lev_login.php\">".html("hoofdmenu","lev_login")."</a>&nbsp;&nbsp;&nbsp;";
	}
	$helemaalboven.="<a href=\"".$vars["path"]."lev_login.php?logout=145\">".html("uitloggen","vars",array("v_gebruiker"=>$login_lev->vars["naam"]))."</a>";

} else {
	$vars["verberg_linkerkolom"]=false;
	if($voorkant_cms and $login->logged_in and $_POST["lev_login_cms"] and $_POST["leverancier_id"]) {
		setcookie("levli[leverancier]","1");
		$_SESSION["LOGIN"]["leverancier"]["logged_in"]=true;
		$_SESSION["LOGIN"]["leverancier"]["leverancier_id"]=intval($_POST["leverancier_id"]);
		$login_lev->end_declaration();
		header("Location: ".$_SERVER["REQUEST_URI"]);
		exit;
	}
}


if($_GET["bezoverzicht"] and $_GET["bezid"]) {
	// bezettingsoverzicht

	// kijken of ingelogde persoon toegang heeft tot deze lijst (+ externe naam opvragen uit db)
	$db->query("SELECT b.externenaam".$vars["ttv_lev_login"]." AS externenaam, b.bezettingsoverzicht_id FROM bezettingsoverzicht b, bezettingsoverzicht_leverancier bl WHERE bl.bezettingsoverzicht_id=b.bezettingsoverzicht_id AND b.bezettingsoverzicht_id='".addslashes($_GET["bezid"])."' AND bl.leverancier_id='".addslashes($login_lev->user_id)."' AND b.externenaam".$vars["ttv_lev_login"]."<>'';");
	if($db->next_record()) {
		$_GET["49k0"]=$db->f("bezettingsoverzicht_id");
		$vars["bezettingsoverzicht_leverancier"]=true;
		$vars["bezettingsoverzicht_externenaam"]=$db->f("externenaam");

		// pagina met overzicht includen
		include("content/cms_bezettingsoverzichten_gegevens.html");
		exit;
	}
} elseif($login_lev->logged_in and $_GET["t"]==1) {


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