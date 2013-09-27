<?php

$mustlogin=true;
$vars["types_in_vars"]=true;

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

	$vars["roominglist_object"]=$roominglist;

	$db->query("SELECT leverancier_id, naam, beheerder, contactpersoon_lijsten, email_lijsten, roominglist_goedgekeurd, roominglist_goedgekeurd_archief FROM leverancier WHERE leverancier_id='".intval($_GET["levid"])."';");
	if($db->next_record()) {

		# frm = formname (mag ook wat anders zijn)
		$form=new form2("frm");
		$form->settings["fullname"]="roominglistform";
		$form->settings["layout"]["css"]=false;
		$form->settings["db"]["table"]="leverancier";
		$form->settings["db"]["where"]="leverancier_id='".intval($_GET["levid"])."'";
		$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN / VERZENDEN";
		#$form->settings["target"]="_blank";

		$leveranciersnaam=$db->f("naam");

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

		$form->field_htmlrow("","<hr><b>Periode</b>","",array("tr_class"=>"roomingaankomst_verzenden"));
		$form->field_date(1,"van","Van","",array("time"=>time()),array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true,"tr_class"=>"roomingaankomst_verzenden"));
		$form->field_date(0,"tot","Tot en met","","",array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true,"tr_class"=>"roomingaankomst_verzenden"));

		$form->field_htmlrow("","<hr><b>Tekst in mailtje</b>","",array("tr_class"=>"roomingaankomst_verzenden"));
		$form->field_textarea(0,"mailbody","Tekst","",array("text"=>"See attached file."),"",array("tr_class"=>"roomingaankomst_verzenden"));

		$form->field_htmlrow("","<hr><b>Naamswijzigingen doorgeven</b><br/><br/><p><i>Legenda</i><br/><span class=\"soort_garantie_1\">garantie: ".$vars["soort_garantie"][1]."</span><br/><span class=\"soort_garantie_2\">garantie: ".$vars["soort_garantie"][2]."</span></p>","",array("tr_class"=>"roomingaankomst_verzenden"));
		if(is_array($roominglist->naamswijzigingen)) {
			$form->field_checkbox(0,"roominglist_naamswijzigingen_doorgeven","Naamswijziging opnemen in roominglist","","",array("selection"=>$roominglist->naamswijzigingen_html),array("one_per_line"=>true,"tr_class"=>"roomingaankomst_verzenden","content_html"=>true));
		} else {
			$form->field_htmlcol("","Opnemen in roominglist",array("html"=>"Er zijn geen naamswijzigingen."),"",array("tr_class"=>"roomingaankomst_verzenden"));
		}

		$form->field_htmlrow("","<hr><b>Op te nemen garanties</b><br/><br/><p><i>Legenda</i><br/><span class=\"soort_garantie_1\">garantie: ".$vars["soort_garantie"][1]."</span><br/><span class=\"soort_garantie_2\">garantie: ".$vars["soort_garantie"][2]."</span></p>","",array("tr_class"=>"roomingaankomst_verzenden"));
		$form->field_checkbox(0,"roominglist_garanties_doorgeven","Opnemen in roominglist","",array("selection"=>substr($roominglist->garanties_doorgeven,1)),array("selection"=>$roominglist->garanties_html),array("one_per_line"=>true,"tr_class"=>"roomingaankomst_verzenden","content_html"=>true));

		$form->field_htmlrow("","<hr><b>Goedkeuring door leverancier</b>");
		$form->field_text(0,"roominglist_goedgekeurd","Goedgekeurd op (+eventuele opmerking)",array("field"=>"roominglist_goedgekeurd"));

		$db->query("SELECT leverancier_naamswijziging_id, beschrijving FROM leverancier_naamswijziging WHERE leverancier_id='".intval($_GET["levid"])."' AND goedgekeurd IS NULL ORDER BY verzonden;");
		if($db->num_rows()) {
			while($db->next_record()) {
				$leverancier_naamswijziging_goedkeuren[$db->f("leverancier_naamswijziging_id")]=$db->f("beschrijving");
			}
			$form->field_checkbox(0,"leverancier_naamswijziging_goedkeuren","Naamswijziging goedgekeuren","","",array("selection"=>$leverancier_naamswijziging_goedkeuren),array("one_per_line"=>true,"content_html"=>true));
		}


		if($db->f("roominglist_goedgekeurd_archief")) {
			$form->field_htmlcol("","Eerdere goedkeuringen",array("html"=>"<div style=\"border:1px solid #003366;padding:5px;max-height:100px;overflow-y:scroll;\">".nl2br(wt_he($db->f("roominglist_goedgekeurd_archief"))."</div>")));
		}

		$form->field_htmlrow("","<hr><b>Interne opmerkingen</b>");
		$form->field_textarea(0,"roominglist_interne_opmerkingen","Opmerkingen",array("field"=>"roominglist_interne_opmerkingen"),"",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));

		$form->field_htmlrow("","<a href=\"#\" id=\"roominglist_bekijken\">Roominglist bekijken zonder verzenden &raquo;</a><input type=\"hidden\" name=\"roominglist_bekijken\" value=\"0\">");


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

			$roominglist->frm_filled = true;

			if($form->input["van"]["unixtime"]>0) {
				$roominglist->van=$form->input["van"]["unixtime"];
			}

			if($form->input["tot"]["unixtime"]>0) {
				$roominglist->tot=$form->input["tot"]["unixtime"];
			}

			if($form->input["roominglist_garanties_doorgeven"]) {
				$roominglist->garanties_doorgeven=$form->input["roominglist_garanties_doorgeven"];
			}

			if($form->input["roominglist_naamswijzigingen_doorgeven"]) {
				$roominglist->naamswijzigingen_doorgeven=$form->input["roominglist_naamswijzigingen_doorgeven"];
			}

			// if($form->input["roominglist_garanties_verbergen"]) {
			// 	$roominglist->verberg_garanties=$form->input["roominglist_garanties_verbergen"];
			// }

			// if($form->input["roominglist_naamswijzigingen_tegenhouden"]) {
			// 	$roominglist->verberg_naamswijzigingen=$form->input["roominglist_naamswijzigingen_tegenhouden"];
			// }

			if($_POST["roominglist_bekijken"]) {
				$roominglist->onbesteld_opvallend=true;
			}
#

			$vars["create_list"]=$roominglist->create_list();

			if($_POST["roominglist_bekijken"]) {

				// preview roominglist
				echo "<!DOCTYPE html>\n<html>";

				echo "<style>

				html {
					font-family: Arial, Helvetica, sans-serif;
					font-size: 0.8em;
				}

				.nog_niet_besteld {
					background-color: #8bb9ff;
				}

				</style>
				";

				echo "<h2>Roominglist</h2>";

				echo "<p><span class=\"nog_niet_besteld\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = nog niet besteld (wordt niet meegezonden)</p>";

				// $vars["create_list"]=$vars["roominglist_object"]->create_list();

				echo $vars["create_list"]["html"];

				echo "</html>";
				exit;
			} else {
				# Gegevens opslaan in de database
				$form->save_db();

				// goedgekeurde naamswijzigingen opslaan
				if($form->input["leverancier_naamswijziging_goedkeuren"]) {
					foreach (preg_split("@,@",$form->input["leverancier_naamswijziging_goedkeuren"]) as $key => $value) {
						echo $key." ".$value."<br>";
						$db->query("UPDATE leverancier_naamswijziging SET goedgekeurd=NOW() WHERE leverancier_naamswijziging_id='".intval($value)."';");
					}
				}

				# Versturen
				if($form->input["versturen"] and !$_POST["roominglist_bekijken"]) {

					@unlink($vars["unixdir"]."tmp/roominglist.doc");
					$roominglist->word_bestand(array("save_filename"=>$vars["unixdir"]."tmp/roominglist.doc"));

					if(file_exists($vars["unixdir"]."tmp/roominglist.doc")) {

						$mail=new wt_mail;
						$mail->fromname="Chalet.nl";
						$mail->from="info@chalet.nl";
						$mail->to=$form->input["email"];
						if($form->input["email"]=="danielle@chalet.nl") {
							$mail->subject="Roominglist ".$leveranciersnaam;
						} else {
							$mail->subject="Roominglist";
						}

						$mail->attachment($vars["unixdir"]."tmp/roominglist.doc","application/msword");

						$mail->plaintext=$form->input["mailbody"];

						$mail->send();

						if($form->input["email_cc"]) {
							# kopie verzenden
							$mail->to=$form->input["email"];
							$mail->send();
						}

						// kopie aan Danielle als archief
						if($form->input["email"]<>"danielle@chalet.nl") {
							$mail->from="archief-roominglist@chalet.nl";
							$mail->to="danielle@chalet.nl";

							$mail->subject="Roominglist ".$leveranciersnaam;

							$mail->send();
						}



						$roominglist_goedgekeurd_archief=trim($db->f("roominglist_goedgekeurd"));
						if($roominglist_goedgekeurd_archief) $roominglist_goedgekeurd_archief.="\n";
						$roominglist_goedgekeurd_archief.=trim($db->f("roominglist_goedgekeurd_archief"));

						$roominglist_inhoud_laatste_verzending=trim($roominglist->regels);

						$db2->query("UPDATE leverancier SET roominglist_aantal_wijzigingen=0, roominglist_goedgekeurd='', roominglist_goedgekeurd_archief='".addslashes($roominglist_goedgekeurd_archief)."', roominglist_inhoud_laatste_verzending='".addslashes($roominglist_inhoud_laatste_verzending)."', roominglist_laatste_verzending_datum=NOW() WHERE leverancier_id='".intval( $_GET["levid"] )."';" );

						# Klantnamen opslaan
						if(is_array($roominglist->klantnamen_boekingen)) {
							foreach ($roominglist->klantnamen_boekingen as $key => $value) {
								$db2->query("UPDATE boeking SET aan_leverancier_doorgegeven_naam='".addslashes($value)."' WHERE boeking_id='".intval($key)."';");

								// naamswijziging opslaan (zodat deze kan worden goedgekeurd)
								$db2->query("INSERT INTO leverancier_naamswijziging SET leverancier_id='".intval($_GET["levid"])."', verzonden=NOW(), beschrijving='".addslashes($roominglist->naamswijzigingen_html[$key])."', boeking_id='".intval($key)."';");
							}
						}
						if(is_array($roominglist->klantnamen_garanties)) {
							foreach ($roominglist->klantnamen_garanties as $key => $value) {
								$db2->query("UPDATE garantie SET aan_leverancier_doorgegeven_naam='".addslashes($value)."' WHERE garantie_id='".intval($key)."';");

								// naamswijziging opslaan (zodat deze kan worden goedgekeurd)
								$db2->query("INSERT INTO leverancier_naamswijziging SET leverancier_id='".intval($_GET["levid"])."', verzonden=NOW(), beschrijving='".addslashes($roominglist->naamswijzigingen_html["g".$key])."', garantie_id='".intval($key)."';");

							}
						}

						if(!$form->input["roominglist_naamswijzigingen_tegenhouden"]) {
							// bij geen naamswijzigingen: veld leegmaken
							$db2->query("UPDATE leverancier SET roominglist_naamswijzigingen_tegenhouden='' WHERE leverancier_id='".intval( $_GET["levid"] )."';" );
						}

					} else {
						trigger_error("tmp/roominglist.doc niet gevonden",E_USER_NOTICE);
					}
				}

				# terug naar overzicht
				header("Location: ".$vars["path"]."cms_roomingaankomst.php");
				exit;
			}
		}
		$form->end_declaration();
	}
}


$layout->display_all($cms->page_title);

?>