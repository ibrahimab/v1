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


$factuurdatum_kan_gewijzigd_worden = true;

if($factuurdatum_kan_gewijzigd_worden) {
	$form->field_date(1,"datum_nieuwefactuur","Datum aan te maken factuur","",array("time"=>time()),array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true));
} else {
	$form->field_htmlcol("","Datum aan te maken factuur",array("text"=>datum("D MAAND JJJJ")));
}

$form->field_yesno("factuuraanmaken","Maak nu een factuur aan");
$form->field_yesno("factuurmailen","Mail deze aangemaakte factuur naar de klant");

$form->field_yesno("voorwaardenmeesturen","Stuur voorwaarden mee (PDF's)","",array("selection"=>($gegevens["stap1"]["factuurdatum"]>0 ? false : true)));



if($gegevens["stap1"]["voucherstatus"]<>"0" and $gegevens["stap1"]["voucherstatus"]<>"5") {
	$form->field_select(0,"vouchersopnieuw","Vouchers moeten opnieuw worden aangemaakt","","",array("selection"=>array(1=>"ja",2=>"nee")));
	$vouchersopnieuw_vragen=true;
}

$form->field_htmlrow("","<hr><b>Goedkeuring</b>");
$form->field_yesno("ondertekenen","Goedkeuring benodigd: vraag om goedkeuring/ondertekening door de klant","",array("selection"=>($gegevens["stap1"]["vraag_ondertekening"]==0 ? false : true)));

#_field: (obl),id,title,db,prevalue,options,layout

if($gegevens["stap1"]["factuurdatum"]) {
	// if($gegevens["stap1"]["factuur_ondertekendatum"]) {
	// 	$add_html_after_field="<div style=\"margin-bottom:-20px;padding-top: 5px;color:#888888;\">(niet zelf invullen)</div>";
	// }
	// $form->field_date(0,"factuur_ondertekendatum","Factuur goedgekeurd door de klant (via website of ondertekening)","",array("time"=>$gegevens["stap1"]["factuur_ondertekendatum"]),array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true, "add_html_after_field"=>$add_html_after_field));
	$form->field_date(0,"factuur_ondertekendatum","Factuur goedgekeurd door de klant (via website of ondertekening)","",array("time"=>$gegevens["stap1"]["factuur_ondertekendatum"]),array("startyear"=>date("Y")-1,"endyear"=>date("Y")+1),array("calendar"=>true));
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
$form->field_yesno("mailblokkeren_enquete","Stuur deze klant geen mailtje met enquête-verzoek (na terugkomst)","",array("selection"=>$gegevens["stap1"]["mailblokkeren_enquete"]));
$form->field_yesno("pdfplattegrond_nietnodig","Plattegrond-PDF niet meesturen bij de reisdocumenten","",array("selection"=>$gegevens["stap1"]["pdfplattegrond_nietnodig"]));
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

	# Factuurdatum
	if($factuurdatum_kan_gewijzigd_worden) {
		$factuurdatum=$form->input["datum_nieuwefactuur"]["unixtime"];
	} else {
		$factuurdatum=mktime(0,0,0,date("m"),date("d"),date("Y"));
	}

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
				$setquery.=", factuurdatum=FROM_UNIXTIME('".addslashes($factuurdatum)."')";

				if(!$gegevens["stap1"]["factuurdatum_eerste_factuur"]) {
					$setquery.=", factuurdatum_eerste_factuur=FROM_UNIXTIME('".addslashes($factuurdatum)."')";
				}
			}
		}


		# Vraag om goedkeuring/ondertekening door de klant
		if($form->input["ondertekenen"]) {
			if(!$gegevens["stap1"]["vraag_ondertekening"]) chalet_log("\"vraag om goedkeuring/ondertekening door de klant\" opnieuw aangezet",true,true);
			$setquery.=", vraag_ondertekening=1";
		} else {
			if($gegevens["stap1"]["vraag_ondertekening"]) chalet_log("\"vraag om goedkeuring/ondertekening door de klant\" uitgezet",true,true);
			$setquery.=", vraag_ondertekening=0";
		}
		if(!$gegevens["stap1"]["aanbetaling1_vastgezet"]) {
			$setquery.=", aanbetaling1='".addslashes($gegevens["fin"]["aanbetaling_ongewijzigd"])."'";
		}

		$setquery.=", factuur_bedrag_wijkt_af=0, totale_reissom='".addslashes($gegevens["fin"]["totale_reissom"])."', mailblokkeren_opties='".addslashes($form->input["mailblokkeren_opties"])."', mailblokkeren_persoonsgegevens='".addslashes($form->input["mailblokkeren_persoonsgegevens"])."', aanmaning_mailblokkeren='".addslashes($form->input["aanmaning_mailblokkeren"])."', mailblokkeren_ontvangenbetaling='".addslashes($form->input["mailblokkeren_ontvangenbetaling"])."', mailblokkeren_klanten_vorig_seizoen='".addslashes($form->input["mailblokkeren_klanten_vorig_seizoen"])."', mailblokkeren_enquete='".addslashes($form->input["mailblokkeren_enquete"])."', pdfplattegrond_nietnodig='".addslashes($form->input["pdfplattegrond_nietnodig"])."'";

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
			chalet_log("Aangezet: Stuur deze klant geen mailtje met enquête-verzoek (na terugkomst)",true,true);
		}
		if(!$form->input["mailblokkeren_enquete"] and $gegevens["stap1"]["mailblokkeren_enquete"]) {
			chalet_log("Uitgezet: Stuur deze klant geen mailtje met enquête-verzoek (na terugkomst)",true,true);
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
		if(!$_POST["alleen_tonen"] and $form->input["factuuraanmaken"]) {
			$db->query("UPDATE boeking SET factuur_versturen=0, factuur_tewijzigen=0, factuurdatum=FROM_UNIXTIME('".addslashes($factuurdatum)."') WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
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
					if($this->gegevens["stap1"]["website_specifiek"]["websiteland"]=="en") {
						$this->Image('pic/factuur_logo_italyhomes.png',10,8,50);
					} else {
						$this->Image('pic/factuur_logo_italissima.png',10,8,50);
					}
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
					# SuperSki
					$this->Image('pic/factuur_logo_superski.png',10,10,70);
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==9) {
					if($this->gegevens["stap1"]["website"]=="Y") {
						# Venturasol Vacances
						$this->Image('pic/factuur_logo_venturasolvacances.png',10,10,70);
					} else {
						# Venturasol Wintersport
						$this->Image('pic/factuur_logo_venturasol.png',10,10,70);
					}
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
				$this->SetFont('Arial','',14);
				$this->MultiCell(0,4,"".txt("bevestigingfactuur","factuur"),0,"R");
				$this->Ln(3);
				$this->SetFont('Arial','',10);
				if($this->gegevens["stap1"]["website_specifiek"]["websitetype"]=="9" and $this->gegevens["stap1"]["website"]=="Y") {
					# Venturasol Vacances-gegevens
					$this->MultiCell(0,4,"".$this->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nWipmolenlaan 3\n3447 GJ Woerden\n\nTel.: 0541 - 53 27 98\nKvK nr. 08116755\n\nIBAN: NL77 ABNA 0436 6729 01\nBIC: ABNANL2A\nBTW NL-8121.27.377.B.01\n",0,"R");
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websiteland"]=="nl") {
					# Adres voor Nederlanders
					$this->MultiCell(0,4,"".$this->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nWipmolenlaan 3\n3447 GJ Woerden\n\nTel.: 0348 434649\nFax: 0348 690752\nKvK nr. 30209634\n\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nBTW NL-8169.23.462.B.01\nABN AMRO - Woerden",0,"R");
				} else {
					if($this->gegevens["stap1"]["taal"]=="en") {
						# Adres voor Engelstalige buitenlanders
						$this->MultiCell(0,4,"".$this->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nWipmolenlaan 3\n3447 GJ Woerden\nThe Netherlands\n\nTel.: +31 348 434649\nFax: +31 348 690752\nKvK nr. 30209634\n\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nBTW NL-8169.23.462.B.01\nABN AMRO - Woerden",0,"R");
					} else {
						# Adres voor Nederlandstalige buitenlanders
						$this->MultiCell(0,4,"".$this->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nWipmolenlaan 3\n3447 GJ Woerden\nNederland\n\nTel.: +31 348 434649\nFax: +31 348 690752\nKvK nr. 30209634\n\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nBTW NL-8169.23.462.B.01\nABN AMRO - Woerden",0,"R");
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
		$pdf->Cell(0,4,"Woerden, ".DATUM("D MAAND JJJJ",$factuurdatum,$gegevens["stap1"]["taal"]),0,1);
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
				$pdf->Cell(5,4,"€",0,0,'L',0);
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
				factuur_opties(1,$vars["annverz_soorten_kort"][$key]." (".ereg_replace("\.",",",$gegevens["stap1"]["annuleringsverzekering_percentage_".$key])."% ".txt("over","factuur")." € ".number_format($toon_annuleringsverzekering_bedragen,2,',','.').")",$gegevens["fin"]["annuleringsverzekering_variabel_".$key]);
			}

			if($gegevens["fin"]["annuleringsverzekering_poliskosten"]<>0) {
				# Poliskosten annuleringsverzekering
				factuur_opties(1,txt("poliskostenannuleringsverzekering","factuur"),$gegevens["fin"]["annuleringsverzekering_poliskosten"]);
			}
		}


		# Schadeverzekering
		if($gegevens["stap1"]["schadeverzekering"]) {
#			factuur_opties(1,txt("schadeverzekering","factuur"),$gegevens["fin"]["schadeverzekering_variabel"]);
			factuur_opties(1,txt("schadeverzekering","factuur")." (".ereg_replace("\.",",",$gegevens["stap1"]["schadeverzekering_percentage"])."% ".txt("over","factuur")." € ".number_format($gegevens["stap1"]["accprijs"],2,',','.').")",$gegevens["fin"]["schadeverzekering_variabel"]);
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

		if($gegevens["fin"]["commissie_accommodatie"]<>0 or $gegevens["fin"]["commissie_opties"]<>0) {
			factuur_opties("",txt("totaal_klant","factuur"),$gegevens["fin"]["totale_reissom_zonder_commissie_aftrek"],"plaintext");
			$pdf->Ln();

			if($pdf->GetY()>250) {
				$pdf->AddPage();
			}

			if($gegevens["fin"]["commissie_accommodatie"]<>0) {
				factuur_opties("",txt("commissie_accommodatie","factuur")." (".getal_met_juist_aantal_decimalen_weergeven($gegevens["stap1"]["commissie"])."%)",0-$gegevens["fin"]["commissie_accommodatie"],"plaintext",0,true);
			}

			if(@count($gegevens["stap4"]["opties_commissie_precentages"])==1) {
				reset($gegevens["stap4"]["opties_commissie_precentages"]);
				list($temp_key,$temp_value)=each($gegevens["stap4"]["opties_commissie_precentages"]);
				$perc.=number_format(floatval($temp_key),0,',','.')."%";
			} else {
				$perc=txt("commissie_diverse_percentages","factuur");
			}

			if($gegevens["fin"]["commissie_opties"]<>0) {
				factuur_opties("",txt("commissie_opties","factuur")." (".$perc.")",0-$gegevens["fin"]["commissie_opties"],"plaintext",0,true);
			}

			# BTW over commissie
			if($gegevens["fin"]["commissie_btw"]<>0) {
				factuur_opties("",txt("commissie_btw","factuur",array("v_btwpercentage"=>number_format($gegevens["stap1"]["btw_over_commissie_percentage"],0),"v_commissiezonderbtw"=>number_format($gegevens["fin"]["commissie_accommodatie"]+$gegevens["fin"]["commissie_opties"],2,",","."))),0-$gegevens["fin"]["commissie_btw"],"plaintext",0,true);
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

		//
		// betalingen
		//
		$booking_payment = new booking_payment($gegevens);
		$booking_payment->bereken_bedragen_opnieuw=true;
		$booking_payment->get_amounts();

		if($booking_payment->amount["reedsvoldaan"]<>0) {
			// reeds voldaan
			factuur_opties("",$booking_payment->text["reedsvoldaan"],$booking_payment->amount["reedsvoldaan"],"plaintext");
			$pdf->Ln(1);
		}

		if($booking_payment->amount["aanbetaling1"]<>0) {
			// aanbetaling 1
			factuur_opties("",$booking_payment->text["aanbetaling1"],$booking_payment->amount["aanbetaling1"],"plaintext");
			$pdf->Ln(1);
		}

		if($booking_payment->amount["aanbetaling2"]<>0) {
			// aanbetaling 2
			factuur_opties("",$booking_payment->text["aanbetaling2"],$booking_payment->amount["aanbetaling2"],"plaintext");
			$pdf->Ln(1);
		}

		if($booking_payment->amount["eindbetaling"]<>0) {
			// aanbetaling 2
			factuur_opties("",$booking_payment->text["eindbetaling"],abs($booking_payment->amount["eindbetaling"]),"plaintext");
			$pdf->Ln(1);
		}


		// if($gegevens["stap1"]["factuur_tekstvak2"] and $aanbetalen<>0) {
		// 	factuur_opties("",$gegevens["stap1"]["factuur_tekstvak2"],$aanbetalen,"plaintext");
		// 	$pdf->Ln(1);
		// }

		// # Aanbetaling 2
		// if($aanbetaling2_factuurtekst and $gegevens["stap1"]["aanbetaling2"]<>0) {
		// 	factuur_opties("",$aanbetaling2_factuurtekst,$gegevens["stap1"]["aanbetaling2"],"plaintext");
		// 	$pdf->Ln(1);
		// }

		// if($restbetalen<>0) factuur_opties("",$gegevens["stap1"]["factuur_tekstvak3"],abs($restbetalen),"plaintext");
		// $pdf->Ln(1);

		$pdf->Cell(190,4,txt("vermeldresnummer","factuur",array("v_resnummer"=>$gegevens["stap1"]["boekingsnummer"])));
		$pdf->Ln();

		if($pdf->GetY()>230) {
			$pdf->AddPage();
		}
		$pdf->Ln();
		$pdf->MultiCell(0,4,$booking_payment->text["afsluiting"]);
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
					$creditfactuur_datum=$factuurdatum;
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
			$prefix=$vars["factuurnummer_prefix"][boekjaar($factuurdatum)];
			$db->query("SELECT MAX(factuur_id) AS factuur_id FROM factuur WHERE SUBSTRING(factuur_id,1,".strlen($prefix).")='".$prefix."';");
			if($db->next_record()) {
				$factuurid=$db->f("factuur_id")+1;
			}
			if($factuurid==1) {
				$factuurid=intval($prefix."00001");
			}
			$db->query("INSERT INTO factuur SET factuur_id='".$factuurid."', filename='".addslashes($archieffile_db)."', datum=FROM_UNIXTIME(".addslashes($factuurdatum)."), volgorde_datetime=NOW(), boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			if($db->insert_id()) {
				$db->query("INSERT INTO factuurregel SET factuur_id='".addslashes($factuurid)."', regelnummer='0', bedrag='".addslashes($gegevens["fin"]["totale_reissom"])."', omschrijving='".addslashes("hele boekstuk")."', grootboektype=0;");
				$db->query("INSERT INTO factuurregel SET factuur_id='".addslashes($factuurid)."', regelnummer='1', bedrag='".addslashes($gegevens["fin"]["totale_reissom"])."', omschrijving='".addslashes("totaal")."', grootboektype=1;");
				$db->query("INSERT INTO factuurregel SET factuur_id='".addslashes($factuurid)."', regelnummer='2', bedrag='0', omschrijving='".addslashes("btw")."', grootboektype=2;");

			}

			$pdf->Output($archieffile);

			chmod($archieffile,0666);
			filesync::add_to_filesync_table($archieffile);

			# Mail versturen aan klant

			// $mail=new wt_mail;
			// $mail->fromname=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
			// $mail->from=$gegevens["stap1"]["website_specifiek"]["email"];
			// $mail->subject="[".$gegevens["stap1"]["boekingsnummer"]."] ".txt("boekingsbevestiging","factuur");

			// unset($mail->plaintext);

			// # Indien geboekt door reisbureau: andere kop boven mailtje
			// if($gegevens["stap1"]["reisbureau_user_id"]) {
			// 	$mail->plaintext.=txt("wederverkoop_reserveringsnummer","factuur").": ".$gegevens["stap1"]["boekingsnummer"]."\n";
			// 	$mail->plaintext.=txt("wederverkoop_hoofdboeker","factuur").": ".wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"])."\n\n\n";
			// }

			// if($form->input["factuur_ondertekendatum"]["unixtime"]>0) {
			// 	$mail->plaintext.=txt("beste","factuur")." ".($gegevens["stap1"]["reisbureau_user_id"] ? $gegevens["stap1"]["reisbureau_uservoornaam"] : ucfirst($gegevens["stap2"]["voornaam"])).",\n\n".txt("attachmentgecorbevest","factuur")."\n\n";
			// } else {
			// 	$mail->plaintext.=txt("beste","factuur")." ".($gegevens["stap1"]["reisbureau_user_id"] ? $gegevens["stap1"]["reisbureau_uservoornaam"] : ucfirst($gegevens["stap2"]["voornaam"])).",\n\n".txt("bedanktvoorjeboeking","factuur")." ".txt("attachmentbevest","factuur")."\n\n";
			// }

			// if($form->input["ondertekenen"]) {
			// 	$mail->plaintext.=txt("tercontroleopfouten","factuur")." ";
			// }
			// $mail->plaintext.=txt("bijonjuistheden","factuur")."\n\n".txt("tot6wekeninloggen","factuur");

			// if(!$gegevens["stap1"]["annuleringsverzekering"]) {
			// 	$mail->plaintext.="\n\n".txt("weadviserenannuleringsverzekering","factuur")." ".$gegevens["stap1"]["website_specifiek"]["basehref"].txt("menu_verzekeringen").".php#annuleringsverzekering";
			// }
			// $mail->plaintext.="\n\n".txt("pdfdownloaden","factuur");
			// $mail->plaintext.="\n\n".txt("mailmetvriendelijkegroet","factuur")."\n".txt("mailmedewerkerswebsitenaam","factuur",array("v_websitenaam"=>$gegevens["stap1"]["website_specifiek"]["websitenaam"]));

			// if(!$form->input["factuurmailen"]) {
			// 	$mail->plaintext="Onderstaand mailtje is NIET aan de klant gestuurd.\n\n".$mail->plaintext;
			// }

			// $mail->attachment($tempfile);

			#
			# Opmaakmail samenstellen
			#
			$subject="[".$gegevens["stap1"]["boekingsnummer"]."] ".txt("boekingsbevestiging","factuur");
			$settings["attachment"][$tempfile]=basename($tempfile);

			$settings["no_header_image"]=true;

			$html="";

			# Melding: niet aan klant gestuurd?
			if(!$form->input["factuurmailen"]) {
				$html.="<p><b>Onderstaand mailtje is NIET aan de klant gestuurd.</b></p>";
			}

			# Indien geboekt door reisbureau: andere kop boven mailtje
			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$html.="<p><b>".html("wederverkoop_reserveringsnummer","factuur").":</b> ".wt_he($gegevens["stap1"]["boekingsnummer"])."<br/>";
				$html.="<b>".html("wederverkoop_hoofdboeker","factuur").":</b> ".wt_he(wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]))."</p>";
			}

			if($form->input["factuur_ondertekendatum"]["unixtime"]>0) {
				$html.="<p>".html("beste","factuur")." ".wt_he(($gegevens["stap1"]["reisbureau_user_id"] ? $gegevens["stap1"]["reisbureau_uservoornaam"] : ucfirst($gegevens["stap2"]["voornaam"]))).",</p><p>".html("attachmentgecorbevest","factuur")."</p>";
			} else {
				$html.="<p>".html("beste","factuur")." ".wt_he(($gegevens["stap1"]["reisbureau_user_id"] ? $gegevens["stap1"]["reisbureau_uservoornaam"] : ucfirst($gegevens["stap2"]["voornaam"]))).",</p><p>".html("bedanktvoorjeboeking","factuur")." ".html("attachmentbevest","factuur")."</p>";
			}

			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$directlogin_link=$vars["websiteinfo"]["basehref"][$gegevens["stap1"]["website"]]."bsys.php?menu=3&reisbureaulogin=1&bid=".$gegevens["stap1"]["boekingid"];
			} else {
				$db0->query("SELECT user_id, password, password_uc FROM boekinguser WHERE user='".addslashes($gegevens["stap2"]["email"])."';");
				if($db0->next_record() and $db0->f("password_uc")) {
					$directlogin = new directlogin;
					$directlogin->boeking_id=$gegevens["stap1"]["boekingid"];
					$directlogin_link=$directlogin->maak_link($gegevens["stap1"]["website"],3,$db0->f("user_id"),md5($db0->f("password_uc")));
				}
			}

			$html.="<p>";
			if($form->input["ondertekenen"]) {
				$html.=html("tercontroleopfouten","factuur")."<ul>";
				$html.="<li><b>".html("onlinebevestigen","factuur")."</b><br>".nl2br(html("onlinebevestigen_uitleg","factuur",array("h_1"=>"<a href=\"".wt_he($directlogin_link)."\">","h_2"=>"&nbsp;&raquo;</a>")))."</li><br/>";
				$html.="<li><b>".html("permailfaxpost","factuur")."</b><br>".html("permailfaxpost_uitleg","factuur")."</li>";
				$html.="</ul>";
			} else {
				$html.=html("bijonjuistheden","factuur")."</p>";
			}

			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$directlogin_link=$vars["websiteinfo"]["basehref"][$gegevens["stap1"]["website"]]."reisagent.php";
			} else {
				$directlogin_link=$directlogin->maak_link($gegevens["stap1"]["website"],1,$db0->f("user_id"),md5($db0->f("password_uc")));
			}
			$html.="<p>".html("tot6wekeninloggen","factuur",array("h_1"=>"<a href=\"".wt_he($directlogin_link)."\">","h_2"=>"</a>","h_3"=>"<i>","h_4"=>"</i>","v_wachtwoord"=>$db0->f("password_uc"),"v_mailadres"=>$gegevens["stap2"]["email"]))."</p>";

			if(!$gegevens["stap1"]["annuleringsverzekering"]) {
				$link=$gegevens["stap1"]["website_specifiek"]["basehref"].html("menu_verzekeringen").".php#annuleringsverzekering";
				$html.="<p>".html("weadviserenannuleringsverzekering","factuur",array("h_1"=>"<a href=\"".wt_he($link)."\">","h_2"=>"</a>"))."</p>";
			}
			$html.="<p>".html("pdfdownloaden","factuur",array("h_1"=>"<a href=\"http://get.adobe.com/reader/\">","h_2"=>"</a>"))."</p>";
			$html.="[ondertekening]";


			//
			// wel/niet meesturen algemene voorwaarden en verzekeringsvoorwaarden
			//
			if($form->input["voorwaardenmeesturen"]) {

				if($gegevens["stap1"]["website"]=="Y") {
					$pdffile="pdf/".txt("pdf_algemene_voorwaarden")."_venturasolvacances.pdf";
				} elseif($gegevens["stap1"]["website"]=="X") {
					$pdffile="pdf/".txt("pdf_algemene_voorwaarden")."_venturasol.pdf";
				} else {
					$pdffile="pdf/".txt("pdf_algemene_voorwaarden").".pdf";
				}
				if(file_exists($pdffile)) {
					// $mail->attachment("pdf/".txt("pdf_algemene_voorwaarden").".pdf");
					$settings["attachment"][$pdffile]=txt("pdf_algemene_voorwaarden").".pdf";
				} else {
					trigger_error("bestand ".$pdffile." niet beschikbaar",E_USER_NOTICE);
				}
				if($gegevens["stap1"]["website_specifiek"]["verzekering_mogelijk"] or $gegevens["stap1"]["annverz_aantalpersonen"]) {
					if(file_exists("pdf/".txt("pdf_voorwaarden_europeesche_annverz").".pdf")) {
						// $mail->attachment("pdf/".txt("pdf_voorwaarden_europeesche_annverz").".pdf");
						$settings["attachment"]["pdf/".txt("pdf_voorwaarden_europeesche_annverz").".pdf"]=txt("pdf_voorwaarden_europeesche_annverz").".pdf";
					} else {
						trigger_error("bestand "."pdf/".txt("pdf_voorwaarden_europeesche_annverz").".pdf niet beschikbaar",E_USER_NOTICE);
					}
				}
			}

			if($form->input["factuurmailen"] and $gegevens["stap1"]["reisbureau_bevestiging_email_1"]) {
				# reisbureau
				if($gegevens["stap1"]["reisbureau_bevestiging_email_2"]) {
					// $mail->to=$gegevens["stap1"]["reisbureau_bevestiging_email_1"];
					// $mail->send();
					// $mail->to=$gegevens["stap1"]["reisbureau_bevestiging_email_2"];
					// $mail->send();
					verstuur_opmaakmail($gegevens["stap1"]["website"],$gegevens["stap1"]["reisbureau_bevestiging_email_1"],"",$subject,$html,$settings);
					verstuur_opmaakmail($gegevens["stap1"]["website"],$gegevens["stap1"]["reisbureau_bevestiging_email_2"],"",$subject,$html,$settings);
					chalet_log("factuur gemaild aan ".$gegevens["stap1"]["reisbureau_bevestiging_email_1"]." en ".$gegevens["stap1"]["reisbureau_bevestiging_email_2"],true,true);
				} else {
					// $mail->to=$gegevens["stap1"]["reisbureau_bevestiging_email_1"];
					// $mail->send();
					verstuur_opmaakmail($gegevens["stap1"]["website"],$gegevens["stap1"]["reisbureau_bevestiging_email_1"],"",$subject,$html,$settings);
					chalet_log("factuur gemaild aan ".$gegevens["stap1"]["reisbureau_bevestiging_email_1"],true,true);
				}
			} else {
				# gewone klant
				if($form->input["factuurmailen"]) {
					// $mail->to=$gegevens["stap2"]["email"];
					$to=$gegevens["stap2"]["email"];
					chalet_log("factuur gemaild aan ".$gegevens["stap2"]["email"],true,true);
				} else {
					if($login->vars["email"]) {
						// $mail->to=$login->vars["email"];
						$to=$login->vars["email"];
					} else {
						// $mail->to="info@chalet.nl";
						$to="info@chalet.nl";
					}
					chalet_log("factuur aangemaakt",true,true);
				}
				verstuur_opmaakmail($gegevens["stap1"]["website"],$to,"",$subject,$html,$settings);
				// $mail->send();
			}

			unlink($tempfile);
		}
	}
}
$form->end_declaration();

$layout->display_all($cms->page_title);

?>