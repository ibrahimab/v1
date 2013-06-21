<?php

$mustlogin=true;
include("admin/vars.php");

if($_GET["reset"]) {
	$roominglist = new roominglist;
	$roominglist->vergelijk_lijsten();

	exit;
}

// $roominglist->leverancier_id = 48;
// $roominglist->word_bestand();
// exit;

if($_GET["levid"]) {

	$roominglist = new roominglist;
	$roominglist->leverancier_id = intval($_GET["levid"]);

	$vars["create_list"]=$roominglist->create_list();

	$db->query("SELECT leverancier_id, naam, beheerder, contactpersoon_lijsten, email_lijsten, roominglist_goedgekeurd, roominglist_goedgekeurd_archief FROM leverancier WHERE leverancier_id='".intval($_GET["levid"])."';");
	if($db->next_record()) {

		# frm = formname (mag ook wat anders zijn)
		$form=new form2("frm");
		$form->settings["fullname"]="Naam";
		$form->settings["layout"]["css"]=false;
		$form->settings["db"]["table"]="leverancier";
		$form->settings["db"]["where"]="leverancier_id='".intval($_GET["levid"])."'";
		$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
		#$form->settings["target"]="_blank";

		# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
		$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

		#_field: (obl),id,title,db,prevalue,options,layout

		$form->field_htmlcol("","Leverancier",array("html"=>"<a href=\"".$vars["path"]."cms_leveranciers.php?edit=8&beheerder=".intval($db->f("beheerder"))."&8k0=".$db->f("leverancier_id")."\" target=\"_blank\">".wt_he($db->f("naam"))."</a>"));
		$form->field_date(0,"roominglist_volgende_controle","Herinner mij opnieuw vanaf",array("field"=>"roominglist_volgende_controle"),"",array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
		$form->field_htmlrow("","<hr><b>Roominglist verzenden</b>");
		$form->field_yesno("versturen","Verstuur onderstaande roominglist naar dit mailadres:");
		// $form->field_email(0,"email","E-mailadres","",array("text"=>$db->f("email_lijsten")));
		$form->field_email(0,"email","E-mailadres","",array("text"=>"danielle@chalet.nl"));
		$form->field_email(0,"email_cc","Kopie sturen naar (e-mailadres)");

		$form->field_htmlrow("","<hr><b>Goedkeuring door leverancier</b>");
		$form->field_text(0,"roominglist_goedgekeurd","Goedgekeurd op (+eventuele opmerking)",array("field"=>"roominglist_goedgekeurd"));
		if($db->f("roominglist_goedgekeurd_archief")) {
			$form->field_htmlcol("","Eerdere goedkeuringen",array("html"=>"<div style=\"border:1px solid #003366;padding:5px;max-height:100px;overflow-y:scroll;\">".nl2br(wt_he($db->f("roominglist_goedgekeurd_archief"))."</div>")));
		}

		$form->field_htmlrow("","<hr><b>Interne opmerkingen</b>");
		$form->field_textarea(0,"roominglist_interne_opmerkingen","Opmerkingen",array("field"=>"roominglist_interne_opmerkingen"),"",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));


		$form->check_input();

		if($form->filled) {
			if($form->input["versturen"] and !$form->input["email"]) {
				$form->error("email","obl");
			}

			if($form->input["versturen"] and $form->input["roominglist_goedgekeurd"]) {
				$form->error("roominglist_goedgekeurd","een nieuw verzonden lijst kan niet direct zijn goedgekeurd");
			}

		}

		if($form->okay) {

			# Gegevens opslaan in de database
			$form->save_db();

			# Versturen
			if($form->input["versturen"]) {

				@unlink($vars["unixdir"]."tmp/roominglist.doc");
				$roominglist->word_bestand(array("save_filename"=>$vars["unixdir"]."tmp/roominglist.doc"));

				if(file_exists($vars["unixdir"]."tmp/roominglist.doc")) {

					$mail=new wt_mail;
					$mail->fromname="Chalet.nl";
					$mail->from="info@chalet.nl";
					$mail->to=$form->input["email"];
					$mail->subject="Roominglist";

					$mail->attachment($vars["unixdir"]."tmp/roominglist.doc","application/msword");

					$mail->plaintext="See attached file.";

					$mail->send();

					if($form->input["email_cc"]) {
						# kopie verzenden
						$mail->to=$form->input["email"];
						$mail->send();
					}


					$roominglist_goedgekeurd_archief=trim($db->f("roominglist_goedgekeurd"));
					if($roominglist_goedgekeurd_archief) $roominglist_goedgekeurd_archief.="\n";
					$roominglist_goedgekeurd_archief.=trim($db->f("roominglist_goedgekeurd_archief"));

#print_r($roominglist);
#exit;

					$roominglist_inhoud_laatste_verzending=trim($roominglist->regels);

					$db2->query("UPDATE leverancier SET roominglist_aantal_wijzigingen=0, roominglist_goedgekeurd='', roominglist_goedgekeurd_archief='".addslashes($roominglist_goedgekeurd_archief)."', roominglist_inhoud_laatste_verzending='".addslashes($roominglist_inhoud_laatste_verzending)."', roominglist_laatste_verzending_datum=NOW() WHERE leverancier_id='".intval( $_GET["levid"] )."';" );

#					echo wt_he($db2->lq);
#					exit;

					# Klantnamen opslaan
					if(is_array($roominglist->klantnamen_boekingen)) {
						foreach ($roominglist->klantnamen_boekingen as $key => $value) {
							$db2->query("UPDATE boeking SET aan_leverancier_doorgegeven_naam='".addslashes($value)."' WHERE boeking_id='".intval($key)."';");
						}
					}
					if(is_array($roominglist->klantnamen_garanties)) {
						foreach ($roominglist->klantnamen_garanties as $key => $value) {
							$db2->query("UPDATE garantie SET aan_leverancier_doorgegeven_naam='".addslashes($value)."' WHERE garantie_id='".intval($key)."';");
						}
					}
				} else {
					trigger_error("tmp/roominglist.doc niet gevonden",E_USER_NOTICE);
				}
			}

			# terug naar overzicht
			header("Location: ".$vars["path"]."cms_roomingaankomst.php");
			exit;
		}
		$form->end_declaration();
	}
}


$layout->display_all($cms->page_title);

?>