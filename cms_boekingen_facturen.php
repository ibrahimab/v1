<?php

$mustlogin=true;
$boeking_bepaalt_taal=true;
include("admin/vars.php");

$temp_gegevens=boekinginfo($_GET["bid"]);
$gegevens["stap1"]=$temp_gegevens["stap1"];
if($gegevens["stap1"]["boekingid"]) {
	$accinfo=accinfo($gegevens["stap1"]["typeid"],$gegevens["stap1"]["aankomstdatum"],$gegevens["stap1"]["aantalpersonen"]);

	# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
	if($temp_gegevens["stap2"][2]) {
		$gegevens["stap2"]=$temp_gegevens["stap2"][2];
	} elseif($temp_gegevens["stap2"][1]) {
		$gegevens["stap2"]=$temp_gegevens["stap2"][1];
	}

	# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
	@reset($temp_gegevens["stap3"][2]);
	while(list($key,$value)=@each($temp_gegevens["stap3"][2])) {
		if(is_array($value)) {
			$gegevens["stap3"][$key]=$value;
		} elseif(is_array($temp_gegevens["stap3"][1][$key])) {
			$gegevens["stap3"][$key]=$temp_gegevens["stap3"][1][$key];
		}
	}

	@reset($temp_gegevens["stap3"][1]);
	while(list($key,$value)=@each($temp_gegevens["stap3"][1])) {
		if(is_array($value) and !is_array($gegevens["stap3"][$key])) $gegevens["stap3"][$key]=$value;
	}

	$gegevens["stap4"]=$temp_gegevens["stap4"][1];
	$gegevens["stap4"]["actieve_status"]=1;
	$gegevens["fin"]=$temp_gegevens["fin"][1];
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Naam";
$form->settings["layout"]["css"]=false;
$form->settings["db"]["table"]="boeking";
$form->settings["db"]["where"]="boeking_id='".addslashes($_GET["bid"])."'";
$form->settings["goto"]=$_GET["burl"];
$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";

#_field: (obl),id,title,db,prevalue,options,layout

$form->field_htmlrow("","<a href=\"#\" onclick=\"document.frm.target='_blank';document.frm.elements['alleen_tonen'].value=1;document.frm.submit();document.frm.target='';document.frm.elements['alleen_tonen'].value=0;\">Bekijk de te mailen factuur &raquo;</a>");
$form->field_hidden("alleen_tonen","0");

$form->field_date(1,"datum_nieuwefactuur","Datum aan te maken factuur","",array("time"=>time()),array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true));
$form->field_yesno("factuuraanmaken","Maak nu een factuur aan");
$form->field_yesno("factuurmailen","Mail deze aangemaakte factuur naar de klant");
if($gegevens["stap1"]["voucherstatus"]<>"0" and $gegevens["stap1"]["voucherstatus"]<>"5") {
	$form->field_select(0,"vouchersopnieuw","Vouchers moeten opnieuw worden aangemaakt","","",array("selection"=>array(1=>"ja",2=>"nee")));
	$vouchersopnieuw_vragen=true;
}

$factuurdatum_time=time();

$form->field_yesno("ondertekenen","Vraag om ondertekening door de klant","",array("selection"=>($gegevens["stap1"]["factuur_ondertekendatum"]>0||$gegevens["stap1"]["vraag_ondertekening"]==0 ? false : true)));

#_field: (obl),id,title,db,prevalue,options,layout

if($gegevens["stap1"]["factuurdatum"]) {
	$form->field_date(0,"factuur_ondertekendatum","Factuur ondertekend ontvangen","",array("time"=>$gegevens["stap1"]["factuur_ondertekendatum"]),array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true));
}
$form->field_htmlrow("","<hr><b>Mailinstellingen</b>");
$form->field_yesno("mailblokkeren_opties","Stuur deze klant geen mail met uitnodiging tot inloggen en opties bijboeken (8 weken voor vertrek)","",array("selection"=>$gegevens["stap1"]["mailblokkeren_opties"]));
$form->field_yesno("mailblokkeren_persoonsgegevens","Stuur deze klant geen mail met verzoek tot invullen persoonsgegevens (43 dagen voor vertrek)","",array("selection"=>$gegevens["stap1"]["mailblokkeren_persoonsgegevens"]));
if($gegevens["stap1"]["mailverstuurd_opties"]>0) {
	$form->field_htmlrow("","<b>Optiesbijboeken-mailtje verstuurd op ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["mailverstuurd_opties"])."</b>");
	$form->field_yesno("mailverstuurd_opties_wissen","Wis het verzendmoment van het optiesbijboeken-mailtje (zodat opnieuw sturen via het systeem mogelijk wordt)");
}
$form->field_yesno("mailblokkeren_ontvangenbetaling","Stuur deze klant geen ontvangstbevestigingen van betalingen","",array("selection"=>$gegevens["stap1"]["mailblokkeren_ontvangenbetaling"]));
$form->field_yesno("aanmaning_mailblokkeren","Stuur deze klant geen aanmaningen","",array("selection"=>$gegevens["stap1"]["aanmaning_mailblokkeren"]));
$form->field_yesno("mailblokkeren_klanten_vorig_seizoen","Stuur deze klant geen mailtje met uitnodiging m.b.t. volgend seizoen","",array("selection"=>$gegevens["stap1"]["mailblokkeren_klanten_vorig_seizoen"]));
$form->field_yesno("mailblokkeren_enquete","Stuur deze klant geen mailtje met enqu�te-verzoek (na terugkomst)","",array("selection"=>$gegevens["stap1"]["mailblokkeren_enquete"]));
$form->field_yesno("pdfplattegrond_nietnodig","Plattegrond-PDF is niet nodig bij de reisdocumenten","",array("selection"=>$gegevens["stap1"]["pdfplattegrond_nietnodig"]));
$form->field_htmlrow("","<hr>");

$form->field_textarea(0,"opmerkingen_intern","Opmerkingen (intern)","",array("text"=>$gegevens["stap1"]["opmerkingen_intern"]),array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));

$form->check_input();

if($form->input["factuurmailen"] and !$form->input["factuuraanmaken"]) {
	$form->error("factuurmailen","een factuur moet ook worden aangemaakt om deze te kunnen mailen");
}

if($form->filled) {
	if($vouchersopnieuw_vragen) {
		if($form->input["factuuraanmaken"] and !$form->input["vouchersopnieuw"]) {
			$form->error("vouchersopnieuw","verplicht invullen bij het aanmaken van een factuur");
		}
	}
}

if($form->okay) {

	# Voucherstatus
	if($form->input["vouchersopnieuw"]==1) {
		$db->query("UPDATE boeking SET voucherstatus='5' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
	}

	# Betalingen
	unset($reedsvoldaan);
	$db->query("SELECT bedrag, UNIX_TIMESTAMP(datum) AS datum FROM boeking_betaling WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' ORDER BY datum;");
	while($db->next_record()) {
		$reedsvoldaan=round($reedsvoldaan+$db->f("bedrag"),2);
	}
	if(!$_POST["alleen_tonen"]) {
		if($gegevens["stap1"]["factuurdatum"]) {
			$setquery="";
			if($form->input["factuur_ondertekendatum"]["unixtime"]<>$gegevens["stap1"]["factuur_ondertekendatum"]) chalet_log("factuur ondertekend",true,true);
			if($form->input["factuur_ondertekendatum"]["unixtime"]>0) $setquery.=", factuur_ondertekendatum=FROM_UNIXTIME('".addslashes($form->input["factuur_ondertekendatum"]["unixtime"])."')";
		} else {
			if($form->input["factuuraanmaken"]) {
				# Bij aanmaken factuur: factuurdatum opslaan
				$setquery.=", factuurdatum=FROM_UNIXTIME('".addslashes($form->input["datum_nieuwefactuur"]["unixtime"])."')";
			}
		}

		# Vraag om ondertekening door de klant
		if($form->input["ondertekenen"]) {
			if(!$gegevens["stap1"]["vraag_ondertekening"]) chalet_log("\"vraag om ondertekening door de klant\" opnieuw aangezet",true,true);
			$setquery.=", vraag_ondertekening=1";
		} else {
			if($form->input["factuur_ondertekendatum"]["unixtime"]>0) {

			} else {
				if($gegevens["stap1"]["vraag_ondertekening"]) chalet_log("\"vraag om ondertekening door de klant\" uitgezet",true,true);
				$setquery.=", vraag_ondertekening=0";
			}
		}

		$setquery.=", factuur_bedrag_wijkt_af=0, aanbetaling1='".addslashes($gegevens["fin"]["aanbetaling_ongewijzigd"])."', totale_reissom='".addslashes($gegevens["fin"]["totale_reissom"])."', mailblokkeren_opties='".addslashes($form->input["mailblokkeren_opties"])."', mailblokkeren_persoonsgegevens='".addslashes($form->input["mailblokkeren_persoonsgegevens"])."', aanmaning_mailblokkeren='".addslashes($form->input["aanmaning_mailblokkeren"])."', mailblokkeren_ontvangenbetaling='".addslashes($form->input["mailblokkeren_ontvangenbetaling"])."', mailblokkeren_klanten_vorig_seizoen='".addslashes($form->input["mailblokkeren_klanten_vorig_seizoen"])."', mailblokkeren_enquete='".addslashes($form->input["mailblokkeren_enquete"])."', pdfplattegrond_nietnodig='".addslashes($form->input["pdfplattegrond_nietnodig"])."'";

		# inkoop bepalen en opslaan in totale_reissom_inkoop
#		$reissom_tabel=reissom_tabel($gegevens,$gegevens["stap1"]["accinfo"],"",true);
#		$setquery.=", totale_reissom_inkoop='".addslashes($reissom_tabel["bedragen"]["inkoop"])."'";

		# inkoopgegevens bepalen en opslaan
		inkoopgegevens_berekenen_en_opslaan($gegevens);

		if($form->input["mailverstuurd_opties_wissen"]) {
			$setquery.=", mailverstuurd_opties=NULL";
			chalet_log("verzendmoment optiesbijboeken-mailtje gewist",true,true);
		}
		if($form->input["opmerkingen_intern"]<>$gegevens["stap1"]["opmerkingen_intern"]) {
			# Opslaan wanneer opmerkingen zijn gewijzigd
			$setquery.=", opmerkingen_intern_gewijzigd=NOW()";
		}
		$db->query("UPDATE boeking SET opmerkingen_intern='".addslashes($form->input["opmerkingen_intern"])."'".$setquery." WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

		# Wijzigingen mailinstellingen loggen
		if($form->input["mailblokkeren_opties"] and !$gegevens["stap1"]["mailblokkeren_opties"]) {
			chalet_log("Aangezet: Stuur deze klant geen mail met uitnodiging tot inloggen en opties bijboeken",true,true);
		}
		if(!$form->input["mailblokkeren_opties"] and $gegevens["stap1"]["mailblokkeren_opties"]) {
			chalet_log("Uitgezet: Stuur deze klant geen mail met uitnodiging tot inloggen en opties bijboeken",true,true);
		}

		if($form->input["mailblokkeren_persoonsgegevens"] and !$gegevens["stap1"]["mailblokkeren_persoonsgegevens"]) {
			chalet_log("Aangezet: Stuur deze klant geen mail met verzoek tot invullen persoonsgegevens",true,true);
		}
		if(!$form->input["mailblokkeren_persoonsgegevens"] and $gegevens["stap1"]["mailblokkeren_persoonsgegevens"]) {
			chalet_log("Uitgezet: Stuur deze klant geen mail met verzoek tot invullen persoonsgegevens",true,true);
		}

		if($form->input["mailblokkeren_ontvangenbetaling"] and !$gegevens["stap1"]["mailblokkeren_ontvangenbetaling"]) {
			chalet_log("Aangezet: Stuur deze klant geen ontvangstbevestigingen van betalingen",true,true);
		}
		if(!$form->input["mailblokkeren_ontvangenbetaling"] and $gegevens["stap1"]["mailblokkeren_ontvangenbetaling"]) {
			chalet_log("Uitgezet: Stuur deze klant geen ontvangstbevestigingen van betalingen",true,true);
		}

		if($form->input["aanmaning_mailblokkeren"] and !$gegevens["stap1"]["aanmaning_mailblokkeren"]) {
			chalet_log("Aangezet: Stuur deze klant geen aanmaningen",true,true);
		}
		if(!$form->input["aanmaning_mailblokkeren"] and $gegevens["stap1"]["aanmaning_mailblokkeren"]) {
			chalet_log("Uitgezet: Stuur deze klant geen aanmaningen",true,true);
		}

		if($form->input["mailblokkeren_klanten_vorig_seizoen"] and !$gegevens["stap1"]["mailblokkeren_klanten_vorig_seizoen"]) {
			chalet_log("Aangezet: Stuur deze klant geen mailtje met uitnodiging m.b.t. volgend seizoen",true,true);
		}
		if(!$form->input["mailblokkeren_klanten_vorig_seizoen"] and $gegevens["stap1"]["mailblokkeren_klanten_vorig_seizoen"]) {
			chalet_log("Uitgezet: Stuur deze klant geen mailtje met uitnodiging m.b.t. volgend seizoen",true,true);
		}

		if($form->input["mailblokkeren_enquete"] and !$gegevens["stap1"]["mailblokkeren_enquete"]) {
			chalet_log("Aangezet: Stuur deze klant geen mailtje met enqu�te-verzoek (na terugkomst)",true,true);
		}
		if(!$form->input["mailblokkeren_enquete"] and $gegevens["stap1"]["mailblokkeren_enquete"]) {
			chalet_log("Uitgezet: Stuur deze klant geen mailtje met enqu�te-verzoek (na terugkomst)",true,true);
		}

		if($form->input["pdfplattegrond_nietnodig"] and !$gegevens["stap1"]["pdfplattegrond_nietnodig"]) {
			chalet_log("Aangezet: Plattegrond-PDF is niet nodig bij de reisdocumenten",true,true);
		}
		if(!$form->input["pdfplattegrond_nietnodig"] and $gegevens["stap1"]["pdfplattegrond_nietnodig"]) {
			chalet_log("Uitgezet: Plattegrond-PDF is niet nodig bij de reisdocumenten",true,true);
		}
	}

	if($form->input["factuuraanmaken"] or $_POST["alleen_tonen"]) {

		# Tekstvak 1
		if($form->input["factuur_ondertekendatum"]["unixtime"]) {
			$gegevens["stap1"]["factuur_tekstvak1"]=txt("hierbijontvangjedegecorrigeerdebevestiging","factuur");
		} else {
			$gegevens["stap1"]["factuur_tekstvak1"]=txt("hierbijontvangjedebevestiging","factuur");
		}

		# Tekstvak 2 en 3
		$datum_weken_voorvertrek=date("d/m/Y",mktime(0,0,0,date("m",$gegevens["stap1"]["aankomstdatum_exact"]),date("d",$gegevens["stap1"]["aankomstdatum_exact"])-$gegevens["stap1"]["totale_reissom_dagenvooraankomst"],date("Y",$gegevens["stap1"]["aankomstdatum_exact"])));

		unset($aanbetaling_aantalweken,$aanbetaling_aantaldagen);
		if($gegevens["stap1"]["totale_reissom_dagenvooraankomst"]%7==0 and $gegevens["stap1"]["totale_reissom_dagenvooraankomst"]<>7) {
			$aanbetaling_aantalweken=round($gegevens["stap1"]["totale_reissom_dagenvooraankomst"]/7);
		} else {
			$aanbetaling_aantaldagen=$gegevens["stap1"]["totale_reissom_dagenvooraankomst"];
		}
		if($gegevens["stap1"]["dagen_voor_vertrek"]>($gegevens["stap1"]["totale_reissom_dagenvooraankomst"]+$gegevens["stap1"]["aanbetaling1_dagennaboeken"])) {
#			if($gegevens["stap1"]["aanbetaling1_dagennaboeken"]>0) {
				$gegevens["stap1"]["factuur_tekstvak2"]=txt("binnenXdagentevoldoen","factuur",array("v_dagen"=>$gegevens["stap1"]["aanbetaling1_dagennaboeken"]));
				$aanbetalen=$gegevens["fin"]["aanbetaling"];
				$restbetalen=$gegevens["fin"]["totale_reissom"]-$gegevens["fin"]["aanbetaling"];
#			} else {
#				$aanbetalen=0;
#				$restbetalen=$gegevens["fin"]["totale_reissom"];
#			}
			if($aanbetaling_aantalweken) {
				$gegevens["stap1"]["factuur_tekstvak3"]=txt("uiterlijkXwekenvoorvertrek","factuur",array("v_weken"=>$aanbetaling_aantalweken,"v_datum"=>$datum_weken_voorvertrek));
			} else {
				$gegevens["stap1"]["factuur_tekstvak3"]=txt("uiterlijkXdagenvoorvertrek","factuur",array("v_dagen"=>$aanbetaling_aantaldagen,"v_datum"=>$datum_weken_voorvertrek));
			}

			# Aanbetaling 2
			if($gegevens["stap1"]["aanbetaling2"] and $gegevens["stap1"]["aanbetaling2_datum"]) {
				if($gegevens["stap1"]["aanbetaling2_datum"]>time()) {
					$aanbetaling2_factuurtekst=txt("uiterlijkdatumtebetalen","factuur",array("v_datum"=>date("d/m/Y",$gegevens["stap1"]["aanbetaling2_datum"])));
				}
				$aanbetaling2=$gegevens["stap1"]["aanbetaling2"];
			}
		} elseif($gegevens["stap1"]["dagen_voor_vertrek"]>$gegevens["stap1"]["totale_reissom_dagenvooraankomst"]) {
			if($aanbetaling_aantalweken) {
				$gegevens["stap1"]["factuur_tekstvak3"]=txt("uiterlijkXwekenvoorvertrek","factuur",array("v_weken"=>$aanbetaling_aantalweken,"v_datum"=>$datum_weken_voorvertrek));
			} else {
				$gegevens["stap1"]["factuur_tekstvak3"]=txt("uiterlijkXdagenvoorvertrek","factuur",array("v_dagen"=>$aanbetaling_aantaldagen,"v_datum"=>$datum_weken_voorvertrek));
			}
			$restbetalen=$gegevens["fin"]["totale_reissom"];
		} elseif($gegevens["stap1"]["dagen_voor_vertrek"]>28) {
			$gegevens["stap1"]["factuur_tekstvak3"]=txt("binnen5dagentevoldoen","factuur");
			$restbetalen=$gegevens["fin"]["totale_reissom"];
		} elseif($gegevens["stap1"]["dagen_voor_vertrek"]>14) {
			$gegevens["stap1"]["factuur_tekstvak3"]=txt("perdirecttevoldoen","factuur");
			$restbetalen=$gegevens["fin"]["totale_reissom"];
		} else {
			$gegevens["stap1"]["factuur_tekstvak3"]=txt("metspoedopdrachttevoldoen","factuur");
			$restbetalen=$gegevens["fin"]["totale_reissom"];
		}
		if($gegevens["stap1"]["factuurdatum"] and $reedsvoldaan<>0) {
			$aanbetalen=$reedsvoldaan;
		}

		if($reedsvoldaan>0) {
			$gegevens["stap1"]["factuur_tekstvak2"]=txt("reedsvoldaan","factuur");
		}

		$restbetalen=$gegevens["fin"]["totale_reissom"]-$aanbetalen-$aanbetaling2;
		if($restbetalen<0) {
			$gegevens["stap1"]["factuur_tekstvak3"]=txt("terugteontvangen","factuur");
		}

		if($gegevens["stap1"]["factuurdatum"]) {
			if($gegevens["stap1"]["dagen_voor_vertrek"]>10) {
				$gegevens["stap1"]["factuur_tekstvak4"]=txt("bedanktgecorboeking10dagen","factuur");
			} else {
				$gegevens["stap1"]["factuur_tekstvak4"]=txt("bedanktgecorboekingreispapieren","factuur");
			}
		} else {
			$gegevens["stap1"]["factuur_tekstvak4"]=txt("tercontrolebinnen24uur","factuur")."\n\n".txt("tot6wekenvoorvertrek","factuur");
		}
		if(!$_POST["alleen_tonen"] and $form->input["factuuraanmaken"]) {
			$db->query("UPDATE boeking SET factuur_versturen=0, factuur_tewijzigen=0, factuurdatum=FROM_UNIXTIME('".addslashes($form->input["datum_nieuwefactuur"]["unixtime"])."') WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		}

		require("admin/fpdf.php");

		class PDF extends FPDF {

			function _getfontpath() {
				return "pdf/fonts/";
			}

			function Header() {

				# Logo linksboven
				if($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==3) {
					# Zomerhuisje
					if($this->gegevens["stap1"]["website_specifiek"]["websiteland"]=="be") {
						# .be
						$this->Image('pic/factuur_logo_zomerhuisje.png',10,8,50);
					} elseif($this->gegevens["stap1"]["website_specifiek"]["websiteland"]=="en") {
						# .eu
						$this->Image('pic/factuur_logo_eu.png',10,8,50);
					} else {
						# .nl
						$this->Image('pic/factuur_logo_zomerhuisje.png',10,10.5,70);
					}
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==6) {
					# Vallandry
					$this->Image('pic/factuur_logo_vallandry.png',10,10.5,70);
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==2) {
					# WSA.nl
					$this->Image('pic/factuur_logo_wsa.png',10,10,100);
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==4 or $this->gegevens["stap1"]["website_specifiek"]["websitetype"]==5) {
					# Chalettour
					$this->Image('pic/factuur_logo_chalettour.png',10,8,50);
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==7) {
					$this->Image('pic/factuur_logo_italissima.png',10,8,50);
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
					# SuperSki
					$this->Image('pic/factuur_logo_superski.png',10,10,70);
				} else {
					# Chalet Winter
					if($this->gegevens["stap1"]["website_specifiek"]["websiteland"]=="be") {
						# .be
						$this->Image('pic/factuur_logo_be.png',10,8,50);
					} elseif($this->gegevens["stap1"]["website_specifiek"]["websiteland"]=="en") {
						# .eu
						$this->Image('pic/factuur_logo_eu.png',10,8,50);
					} else {
						# .nl
						$this->Image('pic/factuur_logo.png',10,8,50);
					}
				}
				$this->SetFont('Arial','',10);
				if($this->gegevens["stap1"]["website_specifiek"]["websiteland"]=="nl") {
					# Adres voor Nederlanders
					$this->MultiCell(0,4,"".$this->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nLindenhof 5\n3442 GT Woerden\n\nTel.: 0348 434649\nFax: 0348 690752\nKvK nr. 30209634\n\nBankrek. 84.93.06.671\nBTW NL-8169.23.462.B.01\n\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nABN AMRO - Woerden",0,"R");
				} else {
					if($this->gegevens["stap1"]["taal"]=="en") {
						# Adres voor Engelstalige buitenlanders
						$this->MultiCell(0,4,"".$this->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nLindenhof 5\n3442 GT Woerden\nThe Netherlands\n\nTel.: +31 348 434649\nFax: +31 348 690752\nKvK nr. 30209634\n\nBank account 84.93.06.671\nBTW NL-8169.23.462.B.01\n\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nABN AMRO - Woerden",0,"R");
					} else {
						# Adres voor Nederlandstalige buitenlanders
						$this->MultiCell(0,4,"".$this->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nLindenhof 5\n3442 GT Woerden\nNederland\n\nTel.: +31 348 434649\nFax: +31 348 690752\nKvK nr. 30209634\n\nBankrek. 84.93.06.671\nBTW NL-8169.23.462.B.01\n\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nABN AMRO - Woerden",0,"R");
					}

				}
				$this->Ln(20);
			}

			function Footer() {
				$this->SetY(-20);
				$this->SetFont('Arial','I',8);
#				$this->Cell(0,10,txt("pagina","factuur")." ".$this->PageNo().'/{nb}',0,0,'C');
				$this->Cell(0,10,txt("pagina","factuur")." ".$this->PageNo()."/{nb}  -  ".txt("reserveringsnummer_afgekort","factuur")." ".$this->gegevens["stap1"]["boekingsnummer"],0,0,'C');
			}
		}

		$pdf=new PDF();
		$pdf->gegevens=$gegevens;
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetY(60);
		if($gegevens["stap1"]["reisbureau_user_id"]) {
			# NAW reisbureau
			$pdf->Cell(0,4,$gegevens["stap1"]["reisbureau_naam"],0,1);
			$pdf->Cell(0,4,$gegevens["stap1"]["reisbureau_adres"],0,1);
			$pdf->Cell(0,4,$gegevens["stap1"]["reisbureau_postcode"]." ".$gegevens["stap1"]["reisbureau_plaats"].($gegevens["stap1"]["reisbureau_land"]<>"Nederland" ? " / ".$gegevens["stap1"]["reisbureau_land"] : ""),0,1);
			if($gegevens["stap1"]["reisbureau_btwnummer"]){
				$pdf->Ln();
				$pdf->Ln();
				$pdf->Cell(0,4,html("btwnummer","factuur").$gegevens["stap1"]["reisbureau_btwnummer"],0,1);
			}

		} else {
			# NAW hoofdboeker
			$pdf->Cell(0,4,wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]),0,1);
			$pdf->Cell(0,4,$gegevens["stap2"]["adres"],0,1);
			$pdf->Cell(0,4,$gegevens["stap2"]["postcode"]." ".$gegevens["stap2"]["plaats"].($gegevens["stap2"]["land"]<>"Nederland" ? " / ".$gegevens["stap2"]["land"] : ""),0,1);
		}
		$pdf->Ln();
		$pdf->Cell(0,4,"Woerden, ".DATUM("D MAAND JJJJ",$form->input["datum_nieuwefactuur"]["unixtime"],$gegevens["stap1"]["taal"]),0,1);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell(0,4,txt("beste","factuur")." ".($gegevens["stap1"]["reisbureau_user_id"] ? $gegevens["stap1"]["reisbureau_uservoornaam"] : ucfirst($gegevens["stap2"]["voornaam"])).",",0,1);
		$pdf->Ln();
		$pdf->MultiCell(0,4,$gegevens["stap1"]["factuur_tekstvak1"]);
		$pdf->Ln();

		if($gegevens["stap1"]["reisbureau_user_id"]) {
			$pdf->Cell(35,4,txt("reisagentfiliaal","factuur"),0,0,'L',0);
			$pdf->Cell(5,4,":",0,0,'L',0);
			$pdf->Cell(50,4,$gegevens["stap1"]["reisbureau_usernaam"].($gegevens["stap1"]["reisbureau_usercode"] ? " (".$gegevens["stap1"]["reisbureau_usercode"].")" : ""),0,0,'L',0);
			$pdf->Ln();
		}

		$pdf->Cell(35,4,txt("reserveringsnummer","factuur"),0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$gegevens["stap1"]["boekingsnummer"],0,0,'L',0);
		$pdf->Ln();

		if($gegevens["stap1"]["reisbureau_user_id"]) {
			$pdf->Cell(35,4,txt("hoofdboeker","factuur"),0,0,'L',0);
			$pdf->Cell(5,4,":",0,0,'L',0);
			$pdf->Cell(50,4,wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]),0,0,'L',0);
			$pdf->Ln();
		}

		$pdf->Cell(35,4,txt("plaats","factuur"),0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$accinfo["plaats"],0,0,'L',0);
		$pdf->Ln();
		$pdf->Cell(35,4,txt("accommodatie","factuur"),0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->MultiCell(150,4,ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"],0,'L',0);

		$pdf->Cell(35,4,txt("deelnemers","factuur"),0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$gegevens["stap1"]["aantalpersonen"]." ".($gegevens["stap1"]["aantalpersonen"]==1 ? txt("persoon") : txt("personen")),0,0,'L',0);
		$pdf->Ln();

		$pdf->Cell(35,4,txt("verblijfsperiode","factuur"),0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,DATUM("D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$gegevens["stap1"]["taal"])." - ".DATUM("D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum_exact"],$gegevens["stap1"]["taal"]),0,0,'L',0);
		$pdf->Ln();

		if($gegevens["stap1"]["opmerkingen_klant"]) {
			$pdf->Cell(35,4,txt("extra","factuur"),0,0,'L',0);
			$pdf->Cell(5,4,":",0,0,'L',0);
			$pdf->MultiCell(0,4,$gegevens["stap1"]["opmerkingen_klant"]);
			$pdf->Ln();
		}

		$pdf->Ln();
		$pdf->SetFont("","U");
		$pdf->Cell(30,4,txt("jehebthetvolgendegeboekt","factuur").":");
		$pdf->SetFont("","");
		$pdf->Ln();
		$pdf->Ln();

		function factuur_opties($aantal,$naam,$bedrag,$type="optie",$toonnul=0,$no_bold=false) {
			global $pdf;
			# Breedte = 190
			if($type=="optie") {
				$pdf->Cell(5,4,$aantal,0,0,'L',0);
				$pdf->Cell(5,4,"x",0,0,'C',0);
				$pdf->Cell(157,4,$naam,0,0,'L',0);
			} else {
				if(!$no_bold) {
					$pdf->SetFont("","B");
				}
				$pdf->Cell(167,4,$naam,0,0,'L',0);
			}
			if($type=="optellen") {
				$pdf->Cell(23,4,"------------------",0,0,'R',0);
			} else {
				$pdf->Cell(5,4,"�",0,0,'L',0);
				$pdf->Cell(18,4,number_format($bedrag,2,',','.'),0,0,'R',0);
			}
			$pdf->SetFont("","");
			$pdf->Ln();
		}

		# Tarief accommodatie/arrangement
		if($accinfo["toonper"]==3 or $gegevens["stap1"]["wederverkoop"]) {
			factuur_opties(1,txt("accommodatie","factuur"),$gegevens["fin"]["accommodatie_totaalprijs"]);
		} else {
			factuur_opties($gegevens["stap1"]["aantalpersonen"],txt("accommodatieplusskipasnaam","factuur",array("v_skipasnaam"=>$accinfo["skipas_naam"],"v_skipasaantaldagen"=>$accinfo["skipas_aantaldagen"])),$gegevens["fin"]["accommodatie_totaalprijs"]);
		}

		# Algemene opties
		@reset($gegevens["stap4"]["algemene_optie"]["soort"]);
		while(list($key,$value)=@each($gegevens["stap4"]["algemene_optie"]["soort"])) {
			factuur_opties(1,($value ? ucfirst($value).": " : "").$gegevens["stap4"]["algemene_optie"]["naam"][$key],$gegevens["stap4"]["algemene_optie"]["verkoop"][$key],"optie",$gegevens["stap4"]["algemene_optie"]["toonnul"][$key]);
		}

		# Opties per persoon
		@reset($gegevens["stap4"]["optie_onderdeelid_teller"]);
		while(list($key,$value)=@each($gegevens["stap4"]["optie_onderdeelid_teller"])) {
			$bedrag=$gegevens["stap4"]["optie_onderdeelid_verkoop_key_verkoop"][$key];
			$key=$gegevens["stap4"]["optie_onderdeelid_verkoop_key"][$key];

			if($gegevens["stap4"]["optie_onderdeelid_reisverzekering"][$key]) {
				$reisverzekeringen["aantal"][$key.$bedrag]=$value;
				$reisverzekeringen["bedrag"][$key.$bedrag]=$bedrag;
				$reisverzekeringen["naam"][$key.$bedrag]=$gegevens["stap4"]["optie_onderdeelid_naam"][$key];
			} else {
				factuur_opties($value,ucfirst($gegevens["stap4"]["optie_onderdeelid_naam"][$key]),$bedrag*$value,"optie",$gegevens["stap4"]["optie_onderdeelid_toonnul"][$key]);
			}
		}
		if($gegevens["fin"]["totale_reissom"]<>$gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"]-$gegevens["fin"]["commissie_totaal"]) {
			# Subtotaal
			factuur_opties("","","","optellen");
			factuur_opties("",txt("subtotaal","factuur"),$gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"]+$gegevens["stap4"]["optie_bedrag_buiten_annuleringsverzekering"],"plaintext");

			$pdf->Cell(190,4,"");
			$pdf->Ln();
		}

		# Reisverzekering
		if($gegevens["stap4"]["reisverzekering"]) {
			while(list($key2,$value2)=@each($reisverzekeringen["naam"])) {
				# Gewone kosten reisverzekering
				factuur_opties($reisverzekeringen["aantal"][$key2],ucfirst($value2),$reisverzekeringen["bedrag"][$key2]*$reisverzekeringen["aantal"][$key2]);
			}
			if($gegevens["fin"]["reisverzekering_poliskosten"]<>0) {
				# Poliskosten reisverzekering
				factuur_opties(1,txt("poliskostenreisverzekering","factuur"),$gegevens["fin"]["reisverzekering_poliskosten"]);
			}
		}

		# Tarief annuleringsverzekering
		if($gegevens["stap1"]["annverz_aantalpersonen"]) {

			ksort($gegevens["stap4"]["annuleringsverzekering_soorten"]);
			while(list($key,$value)=each($gegevens["stap4"]["annuleringsverzekering_soorten"])) {
				# Percentage annuleringsverzekering
				if(abs(floatval($gegevens["stap4"]["annuleringsverzekering_bedragen"][$key]-$gegevens["fin"]["accommodatie_totaalprijs"]-$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"]))<=0.03) {
					$toon_annuleringsverzekering_bedragen=$gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"];
				} else {
					$toon_annuleringsverzekering_bedragen=$gegevens["stap4"]["annuleringsverzekering_bedragen"][$key];
				}
				factuur_opties(1,$vars["annverz_soorten_kort"][$key]." (".ereg_replace("\.",",",$gegevens["stap1"]["annuleringsverzekering_percentage_".$key])."% ".txt("over","factuur")." � ".number_format($toon_annuleringsverzekering_bedragen,2,',','.').")",$gegevens["fin"]["annuleringsverzekering_variabel_".$key]);
			}

			if($gegevens["fin"]["annuleringsverzekering_poliskosten"]<>0) {
				# Poliskosten annuleringsverzekering
				factuur_opties(1,txt("poliskostenannuleringsverzekering","factuur"),$gegevens["fin"]["annuleringsverzekering_poliskosten"]);
			}
		}


		# Schadeverzekering
		if($gegevens["stap1"]["schadeverzekering"]) {
#			factuur_opties(1,txt("schadeverzekering","factuur"),$gegevens["fin"]["schadeverzekering_variabel"]);
			factuur_opties(1,txt("schadeverzekering","factuur")." (".ereg_replace("\.",",",$gegevens["stap1"]["schadeverzekering_percentage"])."% ".txt("over","factuur")." � ".number_format($gegevens["stap1"]["accprijs"],2,',','.').")",$gegevens["fin"]["schadeverzekering_variabel"]);
		}

		if($gegevens["fin"]["verzekeringen_poliskosten"]<>0) {
			# Poliskosten alle verzekeringen samen
			factuur_opties(1,txt("poliskostenverzekeringen","factuur"),$gegevens["fin"]["verzekeringen_poliskosten"]);
		}

		if($gegevens["fin"]["reserveringskosten"]>0) {
			# Reserveringskosten
			factuur_opties(1,txt("reserveringskosten","factuur"),$gegevens["fin"]["reserveringskosten"]);
		}

		factuur_opties("","","","optellen");

		if($gegevens["fin"]["commissie_accommodatie"]>0 or $gegevens["fin"]["commissie_opties"]>0) {
			factuur_opties("",txt("totaal_klant","factuur"),$gegevens["fin"]["totale_reissom_zonder_commissie_aftrek"],"plaintext");
			$pdf->Ln();

			if($pdf->GetY()>250) {
				$pdf->AddPage();
			}

			if($gegevens["fin"]["commissie_accommodatie"]>0) {
				factuur_opties("",txt("commissie_accommodatie","factuur")." (".round($gegevens["stap1"]["commissie"],0)."%)",0-$gegevens["fin"]["commissie_accommodatie"],"plaintext",0,true);
			}

			if(@count($gegevens["stap4"]["opties_commissie_precentages"])==1) {
				reset($gegevens["stap4"]["opties_commissie_precentages"]);
				list($temp_key,$temp_value)=each($gegevens["stap4"]["opties_commissie_precentages"]);
				$perc.=number_format(floatval($temp_key),0,',','.')."%";
			} else {
				$perc=txt("commissie_diverse_percentages","factuur");
			}

			if($gegevens["fin"]["commissie_opties"]>0) {
				factuur_opties("",txt("commissie_opties","factuur")." (".$perc.")",0-$gegevens["fin"]["commissie_opties"],"plaintext",0,true);
			}
			factuur_opties("","","","optellen");
			factuur_opties("",txt("eindtotaal","factuur"),$gegevens["fin"]["totale_reissom"],"plaintext");
		} else {
			factuur_opties("",txt("eindtotaal","factuur"),$gegevens["fin"]["totale_reissom"],"plaintext");
			if($pdf->GetY()>250) {
				$pdf->AddPage();
			}
		}

		$pdf->Ln();
		$pdf->Cell(190,4,"");
		$pdf->Ln();




		if($gegevens["stap1"]["factuur_tekstvak2"] and $aanbetalen<>0) {
			factuur_opties("",$gegevens["stap1"]["factuur_tekstvak2"],$aanbetalen,"plaintext");
			$pdf->Ln(1);
		}

		# Aanbetaling 2
		if($aanbetaling2_factuurtekst and $gegevens["stap1"]["aanbetaling2"]<>0) {
			factuur_opties("",$aanbetaling2_factuurtekst,$gegevens["stap1"]["aanbetaling2"],"plaintext");
			$pdf->Ln(1);
		}

		if($restbetalen<>0) factuur_opties("",$gegevens["stap1"]["factuur_tekstvak3"],abs($restbetalen),"plaintext");
		$pdf->Ln(1);
		$pdf->Cell(190,4,txt("vermeldresnummer","factuur",array("v_resnummer"=>$gegevens["stap1"]["boekingsnummer"])));
		$pdf->Ln();

		if($pdf->GetY()>230) {
			$pdf->AddPage();
		}
		$pdf->Ln();
		$pdf->MultiCell(0,4,$gegevens["stap1"]["factuur_tekstvak4"]);
		$pdf->Ln();

		$pdf->Ln();
		if($gegevens["stap1"]["website_specifiek"]["websiteland"]=="nl") {
			# Wel een SGR-tekst: pagina is daardoor langer
			if($pdf->GetY()>225) {
				$pdf->AddPage();
			}
		} else {
			# Geen SGR-tekst
			if($pdf->GetY()>240) {
				$pdf->AddPage();
			}
		}

		$y=$pdf->GetY();
		$pdf->SetY($y);
		$pdf->MultiCell(125,4,txt("metvriendelijkegroet","factuur")."\n\n".txt("medewerkerswebsitenaam","factuur",array("v_websitenaam"=>$gegevens["stap1"]["website_specifiek"]["websitenaam"])));

		if($form->input["ondertekenen"]) {
			$pdf->SetY($y);
			$pdf->SetX(125);
			$pdf->MultiCell(65,4,txt("voorakkoordnaamhandtekening","factuur")."\n\n\n\n--------------------------------------------------");
		}
		if($pdf->GetY()>228) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln(20);
		}

		$pdf->SetFont('Arial','I',7);
		$pdf->MultiCell(190,4,txt("opdezeovereenkomstalgvoorwaarden","factuur"));

		if($gegevens["stap1"]["website_specifiek"]["websiteland"]=="nl") {
			# SGR-tekst
			$pdf->Ln();

			$img_y=$pdf->GetY();

			$pdf->Image('pic/factuur_sgr.png',11,$img_y+2,7);
			$pdf->Cell(10,4,"",0,0,'L',0);

			$pdf->MultiCell(179,4,txt("sgr","factuur"));
		}

		$pdf->Ln();

		if($_POST["alleen_tonen"]) {
			$pdf->Output();
			exit;
		} else {
			# Factuur als tijdelijk bestand opslaan voor te verzenden mailtje
			$tempfile="tmp/".txt("pdf_factuur")."_".ereg_replace(" / ","_",$gegevens["stap1"]["boekingsnummer"]).".pdf";
			$tempfile_teller=1;
			while(file_exists($tempfile)) {
				$tempfile_teller++;
				$tempfile="tmp/".txt("pdf_factuur")."_".ereg_replace(" / ","_",$gegevens["stap1"]["boekingsnummer"])."_".$tempfile_teller.".pdf";
			}

			$pdf->Output($tempfile);

			# Factuur opslaan voor factuur-archief
			$archieffile="pdf/facturen/factuur_".$gegevens["stap1"]["boekingid"]."_1.pdf";
			while(file_exists($archieffile)) {
				$archieffile_teller++;
				$archieffile="pdf/facturen/factuur_".$gegevens["stap1"]["boekingid"]."_".$archieffile_teller.".pdf";
			}

			$archieffile_db=ereg_replace("pdf/facturen/","",$archieffile);

			if($gegevens["stap1"]["factuurdatum"]) {
				#
				# Creditfactuur
				#
				unset($oude_factuurid);
				$db->query("SELECT factuur_id FROM factuur WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND credit=0 ORDER BY datum DESC, volgorde_datetime DESC, factuur_id DESC LIMIT 0,1");
				if($db->next_record()) {
					$oude_factuurid=$db->f("factuur_id");

					# Nieuw factuurnummer aanmaken
					$creditfactuur_datum=$form->input["datum_nieuwefactuur"]["unixtime"];
					$prefix=$vars["factuurnummer_prefix"][boekjaar($creditfactuur_datum)];
					$db->query("SELECT MAX(factuur_id) AS factuur_id FROM factuur WHERE SUBSTRING(factuur_id,1,".strlen($prefix).")='".$prefix."';");
					if($db->next_record()) {
						$creditfactuur_factuurid=$db->f("factuur_id")+1;
					}
					if($creditfactuur_factuurid==1) {
						$creditfactuur_factuurid=intval($prefix."00001");
					}
					$db->query("INSERT INTO factuur SET factuur_id='".$creditfactuur_factuurid."', filename='', datum=FROM_UNIXTIME(".addslashes($creditfactuur_datum)."), volgorde_datetime=NOW(), boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', credit=1;");
					$db->query("SELECT regelnummer, omschrijving, bedrag, grootboektype FROM factuurregel WHERE factuur_id='".$oude_factuurid."';");
					while($db->next_record()) {
						$creditfactuur_bedrag=0-$db->f("bedrag");
						$creditfactuur_omschrijving="credit: ".$db->f("omschrijving");
						$db2->query("INSERT INTO factuurregel SET factuur_id='".$creditfactuur_factuurid."', regelnummer='".$db->f("regelnummer")."', bedrag='".addslashes($creditfactuur_bedrag)."', omschrijving='".addslashes($creditfactuur_omschrijving)."', grootboektype='".$db->f("grootboektype")."';");
					}
				}
			}

			#
			# Factuur opslaan in database
			#
			$prefix=$vars["factuurnummer_prefix"][boekjaar($form->input["datum_nieuwefactuur"]["unixtime"])];
			$db->query("SELECT MAX(factuur_id) AS factuur_id FROM factuur WHERE SUBSTRING(factuur_id,1,".strlen($prefix).")='".$prefix."';");
			if($db->next_record()) {
				$factuurid=$db->f("factuur_id")+1;
			}
			if($factuurid==1) {
				$factuurid=intval($prefix."00001");
			}
			$db->query("INSERT INTO factuur SET factuur_id='".$factuurid."', filename='".addslashes($archieffile_db)."', datum=FROM_UNIXTIME(".addslashes($form->input["datum_nieuwefactuur"]["unixtime"])."), volgorde_datetime=NOW(), boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			if($db->insert_id()) {
				$db->query("INSERT INTO factuurregel SET factuur_id='".addslashes($factuurid)."', regelnummer='0', bedrag='".addslashes($gegevens["fin"]["totale_reissom"])."', omschrijving='".addslashes("hele boekstuk")."', grootboektype=0;");
				$db->query("INSERT INTO factuurregel SET factuur_id='".addslashes($factuurid)."', regelnummer='1', bedrag='".addslashes($gegevens["fin"]["totale_reissom"])."', omschrijving='".addslashes("totaal")."', grootboektype=1;");
				$db->query("INSERT INTO factuurregel SET factuur_id='".addslashes($factuurid)."', regelnummer='2', bedrag='0', omschrijving='".addslashes("btw")."', grootboektype=2;");

			}

			$pdf->Output($archieffile);

			chmod($archieffile,0666);

			# Mail versturen aan klant
			$mail=new wt_mail;
			$mail->fromname=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
			$mail->from=$gegevens["stap1"]["website_specifiek"]["email"];
#			$mail->returnpath=$gegevens["stap1"]["website_specifiek"]["email"];
			$mail->subject="[".$gegevens["stap1"]["boekingsnummer"]."] ".txt("boekingsbevestiging","factuur");

			unset($mail->plaintext);

			# Indien geboekt door reisbureau: andere kop boven mailtje
			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$mail->plaintext.=txt("wederverkoop_reserveringsnummer","factuur").": ".$gegevens["stap1"]["boekingsnummer"]."\n";
				$mail->plaintext.=txt("wederverkoop_hoofdboeker","factuur").": ".wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"])."\n\n\n";
			}

			if($form->input["factuur_ondertekendatum"]["unixtime"]>0) {
				$mail->plaintext.=txt("beste","factuur")." ".($gegevens["stap1"]["reisbureau_user_id"] ? $gegevens["stap1"]["reisbureau_uservoornaam"] : ucfirst($gegevens["stap2"]["voornaam"])).",\n\n".txt("attachmentgecorbevest","factuur")."\n\n";
			} else {
				$mail->plaintext.=txt("beste","factuur")." ".($gegevens["stap1"]["reisbureau_user_id"] ? $gegevens["stap1"]["reisbureau_uservoornaam"] : ucfirst($gegevens["stap2"]["voornaam"])).",\n\n".txt("bedanktvoorjeboeking","factuur")." ".txt("attachmentbevest","factuur")."\n\n";
			}

			if($form->input["ondertekenen"]) {
				$mail->plaintext.=txt("tercontroleopfouten","factuur")." ";
			}
			$mail->plaintext.=txt("bijonjuistheden","factuur")."\n\n".txt("tot6wekeninloggen","factuur");

			if(!$gegevens["stap1"]["annuleringsverzekering"]) {
				$mail->plaintext.="\n\n".txt("weadviserenannuleringsverzekering","factuur")." ".$gegevens["stap1"]["website_specifiek"]["basehref"].txt("menu_verzekeringen").".php#annuleringsverzekering";
			}
			$mail->plaintext.="\n\n".txt("pdfdownloaden","factuur");
			$mail->plaintext.="\n\n".txt("mailmetvriendelijkegroet","factuur")."\n".txt("mailmedewerkerswebsitenaam","factuur",array("v_websitenaam"=>$gegevens["stap1"]["website_specifiek"]["websitenaam"]));

			if(!$form->input["factuurmailen"]) {
				$mail->plaintext="Onderstaand mailtje is NIET aan de klant gestuurd.\n\n".$mail->plaintext;
			}

			$mail->attachment($tempfile);

			if(!$gegevens["stap1"]["factuurdatum"]) {
				if(file_exists("pdf/".txt("pdf_algemene_voorwaarden").".pdf")) $mail->attachment("pdf/".txt("pdf_algemene_voorwaarden").".pdf");
				if($gegevens["stap1"]["website_specifiek"]["verzekering_mogelijk"] or $gegevens["stap1"]["annverz_aantalpersonen"]) {
					if(file_exists("pdf/".txt("pdf_voorwaarden_europeesche_annverz").".pdf")) $mail->attachment("pdf/".txt("pdf_voorwaarden_europeesche_annverz").".pdf");
				}
			}

			if($form->input["factuurmailen"] and $gegevens["stap1"]["reisbureau_bevestiging_email_1"]) {
				# reisbureau
				if($gegevens["stap1"]["reisbureau_bevestiging_email_2"]) {
					$mail->to=$gegevens["stap1"]["reisbureau_bevestiging_email_1"];
					$mail->send();
					$mail->to=$gegevens["stap1"]["reisbureau_bevestiging_email_2"];
					$mail->send();
					chalet_log("factuur gemaild aan ".$gegevens["stap1"]["reisbureau_bevestiging_email_1"]." en ".$gegevens["stap1"]["reisbureau_bevestiging_email_2"],true,true);
				} else {
					$mail->to=$gegevens["stap1"]["reisbureau_bevestiging_email_1"];
					$mail->send();
					chalet_log("factuur gemaild aan ".$gegevens["stap1"]["reisbureau_bevestiging_email_1"],true,true);
				}
			} else {
				# gewone klant
				if($form->input["factuurmailen"]) {
					$mail->to=$gegevens["stap2"]["email"];
					chalet_log("factuur gemaild aan ".$gegevens["stap2"]["email"],true,true);
				} else {
					if($login->vars["email"]) {
						$mail->to=$login->vars["email"];
					} else {
						$mail->to="info@chalet.nl";
					}
					chalet_log("factuur aangemaakt",true,true);
				}
				$mail->send();
			}

			unlink($tempfile);
		}
	}
}
$form->end_declaration();

$layout->display_all($cms->page_title);

?>