<?php

$mustlogin=true;
$vars["types_in_vars"]=true;

include("admin/vars.php");

if(!$_GET["t"]) {
	$_GET["t"]=1;
}

if($_GET["reset"]) {
	$roominglist = new roominglist;
	$roominglist->vergelijk_lijsten();

	exit;
}

if($_GET["levid"]) {

	$roominglist = new roominglist;
	$roominglist->leverancier_id = intval($_GET["levid"]);

	if($_GET["t"]==2) {
		$roominglist->totaal = false;
		$roominglist->date = $_GET["date"];
	}

	$vars["create_list"]=$roominglist->create_list();

	$vars["roominglist_object"]=$roominglist;

	$db->query("SELECT leverancier_id, naam, beheerder, contactpersoon_lijsten, email_lijsten, email_lijsten_kopie, roominglist_goedgekeurd, roominglist_goedgekeurd_archief FROM leverancier WHERE leverancier_id='".intval($_GET["levid"])."';");
	if($db->next_record()) {

		// goedkeuring needed?
		if($_GET["t"]==2) {
			$db2->query("SELECT laatste_verzending, goedgekeurd, niet_verzenden FROM leverancier_aankomstlijst WHERE leverancier_id='".intval($_GET["levid"])."' AND aankomstdatum='".date("Y-m-d", $_GET["date"])."';");
			if($db2->next_record()) {
				$leverancier_aankomstlijst_laatste_verzending = $db2->f("laatste_verzending");
				$leverancier_aankomstlijst_goedgekeurd = $db2->f("goedgekeurd");
				$leverancier_aankomstlijst_niet_verzenden = $db2->f("niet_verzenden");
			}
		}

		# frm = formname (mag ook wat anders zijn)
		$form=new form2("frm");
		$form->settings["fullname"]="roominglistform";
		$form->settings["layout"]["css"]=false;
		$form->settings["form_css_class"]="no_submit_disable";
		if($_GET["t"]==1) {
			$form->settings["db"]["table"]="leverancier";
			$form->settings["db"]["where"]="leverancier_id='".intval($_GET["levid"])."'";
		}
		$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";

		#$form->settings["target"]="_blank";

		$leveranciersnaam=$db->f("naam");

		# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
		$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

		#_field: (obl),id,title,db,prevalue,options,layout

		if($_GET["t"]==2) {
			$form->field_htmlcol("","Aankomst",array("html"=>datum("DAG D MAAND JJJJ",$_GET["date"])));
		}

		$form->field_htmlcol("","Leverancier",array("html"=>"<a href=\"".$vars["path"]."cms_leveranciers.php?edit=8&beheerder=".intval($db->f("beheerder"))."&8k0=".$db->f("leverancier_id")."\" target=\"_blank\">".wt_he($db->f("naam"))."</a>"));
		if($_GET["t"]==2) {

			$form->field_htmlrow("","<hr><b>Aankomstlijst verzenden</b>");
			$form->field_yesno("niet_verzenden", "Verzenden van de laatste wijzigingen is niet nodig", "", array("selection"=>$leverancier_aankomstlijst_niet_verzenden));
			$form->field_yesno("versturen","Verstuur aankomstlijst per mail");

		} else {
			$form->field_date(0,"roominglist_volgende_controle","Herinner mij opnieuw vanaf",array("field"=>"roominglist_volgende_controle"),"",array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
			$form->field_htmlrow("","<hr><b>Roominglist verzenden</b>");
			$form->field_yesno("versturen","Verstuur roominglist per mail");
		}
		$form->field_email(0,"email","E-mailadres","",array("text"=>$db->f("email_lijsten")),"",array("tr_class"=>"roomingaankomst_verzenden"));
		// $form->field_email(0,"email","E-mailadres","",array("text"=>"danielle@chalet.nl"));
		if($_GET["t"]==2) {
			// aankomstlijst: use email_lijsten_kopie
			$form->field_email(0,"email_cc","Kopie sturen naar (e-mailadres)","",array("text"=>$db->f("email_lijsten_kopie")),"",array("tr_class"=>"roomingaankomst_verzenden"));
		} else {
			// roominglist: don't use email_lijsten_kopie
			$form->field_email(0,"email_cc","Kopie sturen naar (e-mailadres)","","","",array("tr_class"=>"roomingaankomst_verzenden"));
		}

		if($_GET["t"]==1) {
			$form->field_htmlrow("","<hr><b>Periode</b>","",array("tr_class"=>"roomingaankomst_verzenden"));
			$form->field_date(1,"van","Van","",array("time"=>time()),array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true,"tr_class"=>"roomingaankomst_verzenden"));
			$form->field_date(0,"tot","Tot en met","","",array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true,"tr_class"=>"roomingaankomst_verzenden"));
		}

		$form->field_htmlrow("","<hr><b>Tekst in mailtje</b>","",array("tr_class"=>"roomingaankomst_verzenden"));

		if($_GET["t"]==2) {
			// text aankomstlijst
			if($roominglist->bestelmailfax_taal=="N") {
				$mailtekst="Beste ".$db->f("contactpersoon_lijsten").",\n\nBijgaand sturen we een overzicht met onze aankomsten op ".date("d/m/Y",$_GET["date"]).". Hierbij het verzoek om deze reserveringen en extra opties te checken en ons per omgaande te bevestigen.\n\nAlvast heel hartelijk bedankt voor je snelle reactie.\n\nMet vriendelijke groet,";
			} elseif($roominglist->bestelmailfax_taal=="D") {
				$mailtekst="Sehr geehrte(r) ".$db->f("contactpersoon_lijsten").",\n\nAnbei unsere Anreiseliste für die Woche vom ".date("d/m/Y",$_GET["date"]).". Können Sie diese Reservierungen und zusätzliche Optionen bitte überprüfen und uns umgehend bestätigen.\n\nViele Dank in voraus für Ihre baldige Rückbestätigung.\n\nMit freundlichem Gruß,";
			} else {
				$mailtekst="Dear ".$db->f("contactpersoon_lijsten").",\n\nWe are pleased to send you attached a list with our arrivals on ".date("d/m/Y",$_GET["date"]).". Can you please check these reservations and the eventual extra options and send us confirmation by return.\n\nThanks in advance for your early reply.\n\nKind regards,";
			}
		} else {
			// text roominglist
			if($roominglist->bestelmailfax_taal=="N") {
				$mailtekst="Beste ".$db->f("contactpersoon_lijsten").",\n\nBijgaand sturen wij een actueel overzicht met al onze uitstaande reserveringen tot heden. Hierbij het verzoek om deze reserveringen en de eventuele opties te controleren en ons per omgaande te bevestigen.\n\nBij voorbaat heel hartelijk bedankt voor je snelle reactie.\n\nMet vriendelijke groet,";
			} elseif($roominglist->bestelmailfax_taal=="D") {
				$mailtekst="Sehr geehrte(r) ".$db->f("contactpersoon_lijsten").",\n\nAnbei schicken wir Ihnen einen Übersicht mit unseren ausstehenden Reservierungen bis heute. Können Sie diese Reservierungen und die zusätzliche Optionen bitte überprüfen und uns umgehend bestätigen. Wir danken Ihnen in voraus für Ihre baldige Reaktion.\n\nMit freundlichem Gruß,";
			} else {
				$mailtekst="Dear ".$db->f("contactpersoon_lijsten").",\n\nWe are pleased to send you attached an actual list with all our outstanding reservations till today.\n\nCan you please check these reservations and the eventual extra options and send us a confirmation by return.\n\nThanks in advance for your early reaction.\n\nKind regards,";
			}
		}
		$mailtekst.="\n\n".wt_naam($login->vars["voornaam"],$login->vars["tussenvoegsel"],$login->vars["achternaam"])."\n\nChalet.nl\nWipmolenlaan 3\n3447 GJ Woerden\nKvK: 30209634\nTel:  +31 (0) 348 43 46 49\nFax: +31 (0) 348 69 07 52\nEmail: ".$login->vars["email"]."\n";
		$form->field_textarea(0,"mailbody","Tekst","",array("text"=>$mailtekst),"",array("tr_class"=>"roomingaankomst_verzenden","rows"=>24));

		if($_GET["t"]==2) {
			if($leverancier_aankomstlijst_laatste_verzending) {
				$form->field_htmlrow("","<hr><b>Goedkeuring door leverancier</b>");
				$form->field_text(0,"roominglist_goedgekeurd","Goedgekeurd op (+eventuele opmerking)","",array("text"=>$leverancier_aankomstlijst_goedgekeurd));
			}

			if($roominglist->garanties_html) {
				$form->field_htmlrow("","<hr><b>Op te nemen garanties</b><br/><br/><p><i>Legenda</i><br/><span class=\"soort_garantie_1\">garantie: ".$vars["soort_garantie"][1]."</span><br/><span class=\"soort_garantie_2\">garantie: ".$vars["soort_garantie"][2]."</span></p>","",array("tr_class"=>"roomingaankomst_verzenden"));
				$form->field_checkbox(0,"roominglist_garanties_doorgeven","Opnemen in aankomstlijst","",array("selection"=>substr($roominglist->garanties_doorgeven,1)),array("selection"=>$roominglist->garanties_html),array("one_per_line"=>true,"tr_class"=>"roomingaankomst_verzenden","content_html"=>true));
			}

			$form->field_htmlrow("","<a href=\"#\" id=\"roominglist_bekijken\">Aankomstlijst bekijken zonder verzenden &raquo;</a><input type=\"hidden\" name=\"roominglist_bekijken\" value=\"0\"><input type=\"hidden\" name=\"t\" value=\"2\"><input type=\"hidden\" name=\"date\" value=\"".intval($_GET["date"])."\">");
		} else {
			$form->field_htmlrow("","<hr><b>Naamswijzigingen doorgeven</b><br/><br/><p><i>Legenda</i><br/><span class=\"soort_garantie_1\">garantie: ".$vars["soort_garantie"][1]."</span><br/><span class=\"soort_garantie_2\">garantie: ".$vars["soort_garantie"][2]."</span></p>","",array("tr_class"=>"roomingaankomst_verzenden"));
			if(is_array($roominglist->naamswijzigingen)) {
				$form->field_checkbox(0,"roominglist_naamswijzigingen_doorgeven","Naamswijziging opnemen","","",array("selection"=>$roominglist->naamswijzigingen_html),array("one_per_line"=>true,"tr_class"=>"roomingaankomst_verzenden","content_html"=>true));
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
				$form->field_checkbox(0,"leverancier_naamswijziging_goedkeuren","Naamswijziging is goedgekeurd","","",array("selection"=>$leverancier_naamswijziging_goedkeuren),array("one_per_line"=>true,"content_html"=>true));
			}


			if($db->f("roominglist_goedgekeurd_archief")) {
				$form->field_htmlcol("","Eerdere goedkeuringen",array("html"=>"<div style=\"border:1px solid #003366;padding:5px;max-height:100px;overflow-y:scroll;\">".nl2br(wt_he($db->f("roominglist_goedgekeurd_archief"))."</div>")));
			}

			$form->field_htmlrow("","<hr><b>Interne opmerkingen</b>");
			$form->field_textarea(0,"roominglist_interne_opmerkingen","Opmerkingen",array("field"=>"roominglist_interne_opmerkingen"),"",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));

			$form->field_htmlrow("","<a href=\"#\" id=\"roominglist_bekijken\">Roominglist bekijken zonder verzenden &raquo;</a><input type=\"hidden\" name=\"roominglist_bekijken\" value=\"0\"><input type=\"hidden\" name=\"t\" value=\"1\">");

		}



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

			$roominglist->garanties_doorgeven=$form->input["roominglist_garanties_doorgeven"];

			if($form->input["roominglist_naamswijzigingen_doorgeven"]) {
				$roominglist->naamswijzigingen_doorgeven=$form->input["roominglist_naamswijzigingen_doorgeven"];
			}

			if($_POST["roominglist_bekijken"]) {
				$roominglist->onbesteld_opvallend=true;
			}


			if($_POST["t"]==2) {
				// arrivals (not roominglist)
				$roominglist->totaal=false;
				$roominglist->date=$_POST["date"];
			}

			$vars["create_list"]=$roominglist->create_list();

			if($_POST["roominglist_bekijken"]) {

				// preview roominglist
				echo "<!DOCTYPE html>\n<html><head>\n";
				echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />";
				echo "<style>

				html {
					font-family: Arial, Helvetica, sans-serif;
					font-size: 0.8em;
				}

				.nog_niet_besteld {
					background-color: #8bb9ff;
				}

				</style></head><body>
				";

				if($roominglist->totaal) {
					echo "<h2>Roominglist</h2>";
				} else {
					echo "<h2>Aankomstlijst</h2>";
				}


				if($roominglist->regels) {

					echo "<p><span class=\"nog_niet_besteld\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = nog niet besteld (wordt niet meegezonden)</p>";
					if($roominglist->totaal) {
						echo "<p><span style=\"font-weight:bold;background-color:yellow;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = naamswijziging (geel + dikgedrukt, wordt w&eacute;l meegezonden)</p>";
					} elseif($roominglist->aankomstlijst_gele_wijziging) {
						echo "<p><span style=\"font-weight:bold;background-color:yellow;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> = wijziging t.o.v. laatste aankomstlijst of naamswijziging t.o.v. laatste roominglist (geel + dikgedrukt, wordt w&eacute;l meegezonden)</p>";
					}

					echo $vars["create_list"]["html"];
				} else {
					echo "<p>De aankomstlijst is leeg.</p>";
				}

				echo "</body></html>";
				exit;
			} else {
				if($_GET["t"]==1) {
					# Gegevens opslaan in de database
					$form->save_db();
				}

				// goedgekeurde naamswijzigingen opslaan
				if($roominglist->totaal) {
					if($form->input["leverancier_naamswijziging_goedkeuren"]) {
						foreach (preg_split("@,@",$form->input["leverancier_naamswijziging_goedkeuren"]) as $key => $value) {
							echo $key." ".$value."<br>";
							$db->query("UPDATE leverancier_naamswijziging SET goedgekeurd=NOW() WHERE leverancier_naamswijziging_id='".intval($value)."';");
						}
					}
				} else {
					// save goedgekeurd
					if($form->input["roominglist_goedgekeurd"]) {
						$db2->query("UPDATE leverancier_aankomstlijst SET goedgekeurd='".addslashes($form->input["roominglist_goedgekeurd"])."' WHERE leverancier_id='".intval($_GET["levid"])."' AND aankomstdatum='".date("Y-m-d", $_GET["date"])."';");
					}
					if($form->input["niet_verzenden"]) {
						$db2->query("UPDATE leverancier_aankomstlijst SET niet_verzenden=1 WHERE leverancier_id='".intval($_GET["levid"])."' AND aankomstdatum='".date("Y-m-d", $_GET["date"])."';");
					}
				}


				# Versturen
				if($form->input["versturen"] and !$_POST["roominglist_bekijken"]) {

					if($roominglist->totaal) {
						$filename="roominglist.doc";
					} else {
						$filename="arrivals_".date("Y_m_d",$_POST["date"]).".doc";
					}


					@unlink($vars["unixdir"]."tmp/".$filename);
					$roominglist->word_bestand(array("save_filename"=>$vars["unixdir"]."tmp/".$filename));

					if(file_exists($vars["unixdir"]."tmp/".$filename)) {

						$mail=new wt_mail;
						$mail->fromname="Chalet.nl: ".$login->vars["voornaam"];
						$mail->from=$login->vars["email"];
						$mail->to=$form->input["email"];
						if($roominglist->totaal) {
							$mail->subject="Roominglist";
						} else {
							$mail->subject="Arrivals ".date("d/m/Y",$_GET["date"]);
						}
						if($form->input["email"]=="danielle@chalet.nl") {
							$mail->subject.=" ".$leveranciersnaam;
						}

						if($roominglist->regels) {
							$mail->attachment($vars["unixdir"]."tmp/".$filename,"application/msword");
						}

						$mail->plaintext=$form->input["mailbody"];

						$mail->send();

						if($form->input["email_cc"]) {
							# kopie verzenden
							$mail->to=$form->input["email_cc"];
							$mail->send();
						}

						// kopie aan Danielle als archief
						if($form->input["email"]<>"danielle@chalet.nl") {
							$mail->fromname="Chalet.nl";
							$mail->to="danielle@chalet.nl";

							if($roominglist->totaal) {
								$mail->from="archief-roominglist@chalet.nl";
								$mail->subject="Roominglist ".$leveranciersnaam;;
							} else {
								$mail->from="archief-arrivals@chalet.nl";
								$mail->subject="Arrivals ".date("d/m/Y",$_GET["date"])." ".$leveranciersnaam;;
							}

							$mail->send();
						}


						if($roominglist->totaal) {
							//
							// roominglist
							//
							$roominglist_goedgekeurd_archief=trim($db->f("roominglist_goedgekeurd"));
							if($roominglist_goedgekeurd_archief) $roominglist_goedgekeurd_archief.="\n";
							$roominglist_goedgekeurd_archief.=trim($db->f("roominglist_goedgekeurd_archief"));

							$roominglist_inhoud_laatste_verzending=trim($roominglist->regels);

							if(is_array($roominglist->garanties_doorgeven_opslaan_array)) {
								// welke garanties moeten de volgende keer opnieuw aangevinkt zijn?
								foreach ($roominglist->garanties_doorgeven_opslaan_array as $key => $value) {
									$garanties_doorgeven_opslaan.=",".$key;
								}
								$garanties_doorgeven_opslaan=substr($garanties_doorgeven_opslaan,1);
							}

							$db2->query("UPDATE leverancier SET roominglist_aantal_wijzigingen=0, roominglist_goedgekeurd='', roominglist_goedgekeurd_archief='".addslashes($roominglist_goedgekeurd_archief)."', roominglist_inhoud_laatste_verzending='".addslashes($roominglist_inhoud_laatste_verzending)."', roominglist_laatste_verzending_datum=NOW(), roominglist_garanties_doorgeven='".addslashes($garanties_doorgeven_opslaan)."' WHERE leverancier_id='".intval( $_GET["levid"] )."';" );
						} else {
							//
							// aankomstlijst
							//

							$set="laatste_verzending=NOW(), goedgekeurd=NULL, aantal_wijzigingen=0, inhoud_laatste_verzending='".addslashes(trim($roominglist->regels))."', garanties_doorgeven='".addslashes($form->input["roominglist_garanties_doorgeven"])."'";
							$db2->query("INSERT INTO leverancier_aankomstlijst SET leverancier_id='".intval($_GET["levid"])."', aankomstdatum=FROM_UNIXTIME(".intval($_GET["date"])."), ".$set." ON DUPLICATE KEY UPDATE ".$set.";");
						}

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

					} else {
						trigger_error("tmp/".$filename." niet gevonden",E_USER_NOTICE);
					}
				}

				# terug naar overzicht
				header("Location: ".$vars["path"]."cms_roomingaankomst.php?t=".$_GET["t"].($_GET["date"] ? "&date=".$_GET["date"] : ""));
				exit;
			}
		}
		$form->end_declaration();
	}
}


$layout->display_all($cms->page_title);

?>