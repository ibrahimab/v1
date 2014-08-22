<?php

$vars["reisbureau_mustlogin"]=true;
$vars["verberg_breadcrumbs"]=true;
include("admin/vars.php");

if($vars["wederverkoop"]) {
	if(!$login_rb->logged_in) {
		header("Location: ".$path."reisagent.php");
		exit;
	}
	if($_GET["mijngeg"]) {

		# Gegevens reisbureau uit database halen
		$db->query("SELECT * FROM reisbureau WHERE reisbureau_id='".addslashes($login_rb->vars["reisbureau_id"])."';");
		if($db->next_record()) {
			$reisbureau["naam"]=$db->f("naam");
			$reisbureau["adres"]=$db->f("adres");
			$reisbureau["postcode"]=$db->f("postcode");
			$reisbureau["plaats"]=$db->f("plaats");
			$reisbureau["land"]=$db->f("land");
			$reisbureau["post_adres"]=$db->f("post_adres");
			$reisbureau["post_postcode"]=$db->f("post_postcode");
			$reisbureau["post_plaats"]=$db->f("post_plaats");
			$reisbureau["post_land"]=$db->f("post_land");
			$reisbureau["telefoonnummer"]=$db->f("telefoonnummer");
			$reisbureau["noodnummer"]=$db->f("noodnummer");
			$reisbureau["website"]=$db->f("website");
			$reisbureau["anvrnummer"]=$db->f("anvrnummer");
			$reisbureau["verantwoordelijke"]=$db->f("verantwoordelijke");
			$reisbureau["email_overeenkomst"]=$db->f("email_overeenkomst");
			$reisbureau["kvknummer"]=$db->f("kvknummer");
			$reisbureau["btwnummer"]=$db->f("btwnummer");
			$reisbureau["email_facturen"]=$db->f("email_facturen");
			$reisbureau["email_marketing"]=$db->f("email_marketing");

			$htmlcontact=wt_he($reisbureau["adres"])."<br>".wt_he($reisbureau["postcode"]." ".$reisbureau["plaats"])."<br>".wt_he($reisbureau["land"])."<br>".wt_he($reisbureau["telefoonnummer"]);
			if($reisbureau["website"]) $htmlcontact.="<br><a href=\"".wt_he($reisbureau["website"])."\" target=\"_blank\">".wt_he($reisbureau["website"])."</a>";
			if($reisbureau["post_adres"]<>$reisbureau["adres"] or $reisbureau["post_plaats"]<>$reisbureau["plaats"]) {
				$htmlcontact.="<br><br><b>".html("postadres","reisbureau_overzicht").":</b><br>";
				$htmlcontact.=wt_he($reisbureau["post_adres"])."<br>".wt_he($reisbureau["post_postcode"]." ".$reisbureau["post_plaats"])."<br>".wt_he($reisbureau["post_land"]);

			}
		}


		# frm = formname (mag ook wat anders zijn)
		$form=new form2("frm");
		$form->settings["language"]=$vars["taal"];
		$form->settings["fullname"]="Naam";
		$form->settings["layout"]["css"]=false;
		$form->settings["db"]["table"]="reisbureau_user";
		$form->settings["db"]["where"]="user_id='".addslashes($login_rb->user_id)."'";

		$form->settings["add_to_filesync_table"] = true;

		$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
		#$form->settings["target"]="_blank";

		# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
		$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

		#_field: (obl),id,title,db,prevalue,options,layout
		if($login_rb->vars["hoofdgebruiker"]) {

			$form->field_htmlrow("","<b>".html("organisatiegegevens","reisbureau_overzicht")."</b>");
			$form->field_htmlcol("",txt("naamorganisatie","reisbureau_overzicht"),array("html"=>wt_he($reisbureau["naam"])));
			$form->field_text(($reisbureau["verantwoordelijke"] ? 1 : 0),"reisbureau_verantwoordelijke",txt("verantwoordelijke","reisbureau_overzicht"),"",array("text"=>$reisbureau["verantwoordelijke"]));
			$form->field_email(($reisbureau["email_overeenkomst"] ? 1 : 0),"reisbureau_email_overeenkomst",txt("emailovereenkomst","reisbureau_overzicht"),"",array("text"=>$reisbureau["email_overeenkomst"]));
			$form->field_text(($reisbureau["anvrnummer"] ? 1 : 0),"reisbureau_anvrnummer",txt("anvrnummer","reisbureau_overzicht"),"",array("text"=>$reisbureau["anvrnummer"]));
			$form->field_text(($reisbureau["kvknummer"] ? 1 : 0),"reisbureau_kvknummer",txt("kvknummer","reisbureau_overzicht"),"",array("text"=>$reisbureau["kvknummer"]));
			$form->field_text(($reisbureau["btwnummer"] ? 1 : 0),"reisbureau_btwnummer",txt("btwnummer","reisbureau_overzicht"),"",array("text"=>$reisbureau["btwnummer"]));
			$form->field_htmlrow("","<i>".html("adreskantoor","reisbureau_overzicht")."</i>");
			$form->field_text(1,"reisbureau_adres",txt("adres","reisbureau_overzicht"),"",array("text"=>$reisbureau["adres"]));
			$form->field_text(1,"reisbureau_postcode",txt("postcode","reisbureau_overzicht"),"",array("text"=>$reisbureau["postcode"]));
			$form->field_text(1,"reisbureau_plaats",txt("plaats","reisbureau_overzicht"),"",array("text"=>$reisbureau["plaats"]));
			$form->field_text(1,"reisbureau_land",txt("land","reisbureau_overzicht"),"",array("text"=>$reisbureau["land"]));

			$form->field_htmlrow("","<i>".html("postadreskantoor","reisbureau_overzicht")."</i>");
			$form->field_text(1,"reisbureau_post_adres",txt("adres","reisbureau_overzicht"),"",array("text"=>$reisbureau["post_adres"]));
			$form->field_text(1,"reisbureau_post_postcode",txt("postcode","reisbureau_overzicht"),"",array("text"=>$reisbureau["post_postcode"]));
			$form->field_text(1,"reisbureau_post_plaats",txt("plaats","reisbureau_overzicht"),"",array("text"=>$reisbureau["post_plaats"]));
			$form->field_text(1,"reisbureau_post_land",txt("land","reisbureau_overzicht"),"",array("text"=>$reisbureau["post_land"]));
			$form->field_htmlrow("","&nbsp;");

			$form->field_text(1,"reisbureau_telefoonnummer",txt("telefoonnummer","reisbureau_overzicht"),"",array("text"=>$reisbureau["telefoonnummer"]));
			$form->field_text(0,"reisbureau_noodnummer",txt("noodnummer","reisbureau_overzicht"),"",array("text"=>$reisbureau["noodnummer"]));
			$form->field_url(0,"reisbureau_website",txt("website","reisbureau_overzicht"),"",array("text"=>$reisbureau["website"]));
			$form->field_email(0,"reisbureau_email_facturen",txt("email_facturen","reisbureau_overzicht"),"",array("text"=>$reisbureau["email_facturen"]));
			$form->field_email(0,"reisbureau_email_marketing",txt("email_marketing","reisbureau_overzicht"),"",array("text"=>$reisbureau["email_marketing"]));
			$form->field_htmlrow("","<hr><b>".html("persoonlijkegegevens","reisbureau_overzicht")."</b><br>");
		}

		$form->field_text(0,"code",txt("persoonlijkeagentcode","reisbureau_overzicht"),array("field"=>"code"));
		$form->field_text(1,"voornaam",txt("voornaam","reisbureau_overzicht"),array("field"=>"voornaam"));
		$form->field_text(0,"tussenvoegsel",txt("tussenvoegsel","reisbureau_overzicht"),array("field"=>"tussenvoegsel"));
		$form->field_text(1,"achternaam",txt("achternaam","reisbureau_overzicht"),array("field"=>"achternaam"));
		$form->field_email(1,"email",txt("email","reisbureau_overzicht"),array("field"=>"email"));

		$form->field_password(0,"password",txt("nieuwwachtwoord","reisbureau_overzicht"),array("field"=>"password"),"",array("new_password"=>true,"strong_password"=>true));
		$form->field_password(0,"password2",txt("herhaalwachtwoord","reisbureau_overzicht"));
		$form->field_upload(0,"logo",txt("logo","reisbureau_overzicht"),"","",array("move_file_to"=>"pic/cms/reisagent_logo/","must_be_filetype"=>"jpg","showfiletype"=>true,"rename_file_to"=>$login_rb->user_id),"");
		if(!$login_rb->vars["hoofdgebruiker"]) {
			$form->field_htmlrow("","<hr><b>".html("contactgegevenskantoor","reisbureau_overzicht").":</b><br>".$htmlcontact);
		}
		$form->field_htmlrow("","<b>".html("mijncontactgegevens","reisbureau_overzicht").":</b>");

		$form->field_text(0,"adres",txt("adres","reisbureau_overzicht"),array("field"=>"adres"));
		$form->field_text(0,"postcode",txt("postcode","reisbureau_overzicht"),array("field"=>"postcode"));
		$form->field_text(0,"plaats",txt("plaats","reisbureau_overzicht"),array("field"=>"plaats"));
		$form->field_text(0,"land",txt("land","reisbureau_overzicht"),array("field"=>"land"));
		$form->field_text(0,"telefoonnummer",txt("telefoonnummer","reisbureau_overzicht"),array("field"=>"telefoonnummer"));
		$form->field_text(0,"mobiel",txt("mobiel","reisbureau_overzicht"),array("field"=>"mobiel"));
		$form->field_url(0,"website",txt("eigenwebsite","reisbureau_overzicht"),array("field"=>"website"));

#		if($vars["taal"]=="nl") {
			# tijdelijk uitgezet vanwege overgang naar Blinker (29-09-2011)
#			$form->field_htmlrow("","<hr>");
#			$form->field_yesno("mailingmanager_gewonenieuwsbrief",txt("lidchaletzomerhuisjenieuwsbrief","reisbureau_overzicht"),array("field"=>"mailingmanager_gewonenieuwsbrief"));
#			$form->field_yesno("mailingmanager_agentennieuwsbrief",txt("lidagentennieuwsbrief","reisbureau_overzicht"),array("field"=>"mailingmanager_agentennieuwsbrief"));
#		}

		$form->check_input();

		if($form->filled) {
			if($form->input["password"] and $form->input["password"]<>$form->input["password2"]) $form->error("password2",html("voer2xwachtwoord","reisbureau_overzicht"));
		}

		if($form->okay) {

			#
			# Gegevens loggen
			#


			# Gegevens opslaan in de database
			$form->save_db();

			# Gegevens opslaan in tabel reisbureau
			if($login_rb->vars["hoofdgebruiker"]) {
				$db->query("UPDATE reisbureau SET verantwoordelijke='".addslashes($form->input["reisbureau_verantwoordelijke"])."', email_overeenkomst='".addslashes($form->input["reisbureau_email_overeenkomst"])."', anvrnummer='".addslashes($form->input["reisbureau_anvrnummer"])."', kvknummer='".addslashes($form->input["reisbureau_kvknummer"])."', btwnummer='".addslashes($form->input["reisbureau_btwnummer"])."', adres='".addslashes($form->input["reisbureau_adres"])."', postcode='".addslashes($form->input["reisbureau_postcode"])."', plaats='".addslashes($form->input["reisbureau_plaats"])."', land='".addslashes($form->input["reisbureau_land"])."', post_adres='".addslashes($form->input["reisbureau_post_adres"])."', post_postcode='".addslashes($form->input["reisbureau_post_postcode"])."', post_plaats='".addslashes($form->input["reisbureau_post_plaats"])."', post_land='".addslashes($form->input["reisbureau_post_land"])."', telefoonnummer='".addslashes($form->input["reisbureau_telefoonnummer"])."', noodnummer='".addslashes($form->input["reisbureau_noodnummer"])."', website='".addslashes($form->input["reisbureau_website"])."', email_facturen='".addslashes($form->input["reisbureau_email_facturen"])."', email_marketing='".addslashes($form->input["reisbureau_email_marketing"])."' WHERE reisbureau_id='".addslashes($login_rb->vars["reisbureau_id"])."';");
			}
		}
		$form->end_declaration();
	}
} else {
	header("Location: ".$path);
	exit;
}

include "content/opmaak.php";

?>