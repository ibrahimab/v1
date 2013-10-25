<?php

$mustlogin=true;

include("admin/vars.php");

if($_GET["lid"]) {
	// $invullen["soort"]=4;
	$invullen["leverancier"]=$_GET["lid"];
	$db->query("SELECT voornaam, tussenvoegsel, achternaam, aankomstdatum, aankomstdatum_exact, aantalpersonen, type_id, UNIX_TIMESTAMP(einddatum_klant) AS einddatum_klant, website FROM optieaanvraag WHERE optieaanvraag_id='".addslashes($_GET["oaid"])."';");
	if($db->next_record()) {
		$invullen["looptaf"]=date("d/m/Y",$db->f("einddatum_klant"));
		$invullen["voornaam"]=$db->f("voornaam");
		$invullen["tussenvoegsel"]=$db->f("tussenvoegsel");
		$invullen["achternaam"]=$db->f("achternaam");
		$invullen["aankomstdatum"]=$db->f("aankomstdatum");
		$invullen["aankomstdatum_exact"]=$db->f("aankomstdatum_exact");
		$invullen["accinfo"]=accinfo($db->f("type_id"),$db->f("aankomstdatum"),$db->f("aantalpersonen"));
		$invullen["aantalnachten"]=round(($invullen["accinfo"]["vertrekdatum"]-$db->f("aankomstdatum_exact"))/86400);
		$invullen["seizoenid"]=$invullen["accinfo"]["seizoenid"];
		$invullen["website"]=$db->f("website");
	}
} else {
	$gegevens=get_boekinginfo($_GET["bid"]);

	$invullen["leverancier"]=$gegevens["stap1"]["leverancierid"];
	$invullen["looptaf"]="";
	$invullen["voornaam"]=$gegevens["stap2"]["voornaam"];
	$invullen["tussenvoegsel"]=$gegevens["stap2"]["tussenvoegsel"];
	$invullen["achternaam"]=$gegevens["stap2"]["achternaam"];
	$invullen["aankomstdatum"]=$gegevens["stap1"]["aankomstdatum"];
	$invullen["aankomstdatum_exact"]=$gegevens["stap1"]["aankomstdatum_exact"];
	$invullen["accinfo"]=$gegevens["stap1"]["accinfo"];
	$invullen["aantalnachten"]=$gegevens["stap1"]["aantalnachten"];
	$invullen["seizoenid"]=$gegevens["stap1"]["seizoenid"];
	$invullen["website"]=$gegevens["stap1"]["website"];

	# Kijken of het om een verzameltype gaat
	if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
		$invullen["accinfo"]=accinfo($gegevens["stap1"]["verzameltype_gekozentype_id"],$gegevens["stap1"]["aankomstdatum"],$gegevens["stap1"]["aantalpersonen"]);
	}
}

$db->query("SELECT naam, contactpersoon_reserveringen AS contactpersoon, faxnummer_reserveringen AS faxnummer, email_reserveringen AS email, adresregels, bestelfax_logo, bestelmailfax_taal, zwitersefranken FROM leverancier WHERE leverancier_id='".addslashes($invullen["leverancier"])."';");
if($db->next_record()) {
	$vars["temp_leverancier"]["naam"]=$db->f("naam");
	$vars["temp_leverancier"]["contactpersoon"]=$db->f("contactpersoon");
	$vars["temp_leverancier"]["faxnummer"]=$db->f("faxnummer");
	$vars["temp_leverancier"]["email"]=$db->f("email");
	$vars["temp_leverancier"]["adresregels"]=$db->f("adresregels");
	$vars["temp_leverancier"]["bestelfax_logo"]=$db->f("bestelfax_logo");
	$vars["temp_leverancier"]["zwitersefranken"]=$db->f("zwitersefranken");
	$bmftaal=$db->f("bestelmailfax_taal");
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Bestelmailfax";
$form->settings["layout"]["css"]=false;
if($_GET["t"]==1) {
	$form->settings["message"]["submitbutton"]["nl"]="BESTELLING MAILEN";
} else {
	$form->settings["message"]["submitbutton"]["nl"]="FAX PRINTEN";
	$form->settings["goto"]=$_GET["burl"];
	$form->settings["target"]="_blank";
}

#_field: (obl),id,title,db,prevalue,options,layout
if($gegevens["stap1"]["verzameltype"]) {
	if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
		$form->field_htmlrow("","<span class=\"error\">verzameltype-boeking - het gekozen onderliggende type zal worden besteld</span>");
	} else {
		$form->field_htmlrow("","<span class=\"error\">verzameltype-boeking - let op: er is nog geen onderliggend type geselecteerd</span>");
	}
}

if($_GET["t"]==1) {
	$form->field_htmlcol("aan","Mailadres",array("text"=>$vars["temp_leverancier"]["email"]));
}
$form->field_text(1,"contactpersoon",$vars["bestelmailfax_beste"][$bmftaal],"",array("text"=>$vars["temp_leverancier"]["contactpersoon"])); # (opslaan in databaseveld "test")

// eerder afgeboekte voorraad bepalen

// 1 = op aanvraag
// 2 = in allotment
// 3 = in garantie
// 4 = in optie
unset($temp_teller);
$temp_log=$gegevens["stap1"]["log"];
while(preg_match("@1 afboeken@",$temp_log)) {
	if(preg_match("@(voorraad ([a-z0-9 ]+) 1 afboeken)@",$temp_log,$regs)) {
		$afgeboekte_voorraad=1;
		if($regs[2]=="request") {
			$afgeboekte_voorraad=1;
		} elseif($regs[2]=="allotment") {
			$afgeboekte_voorraad=2;
		} elseif($regs[2]=="garantie") {
			$afgeboekte_voorraad=3;
		} elseif($regs[2]=="optie leverancier") {
			$afgeboekte_voorraad=4;
		}
	}
	$temp_log=str_replace($regs[1], "", $temp_log);

	$temp_teller++;
	if($temp_teller>100) {
		trigger_error("vastlopende while-loop",E_USER_NOTICE);
		echo "FOUTMELDING: X881<br/>".$temp_log;
		exit;
	}
}

// actuele voorraad bepalen
$db->query("SELECT voorraad_garantie, voorraad_allotment, voorraad_optie_leverancier, voorraad_request FROM tarief WHERE type_id='".intval($invullen["accinfo"]["type_id"])."' AND week='".$gegevens["stap1"]["aankomstdatum"]."';");
if($db->next_record()) {
	$huidige_voorraad[1]=$db->f("voorraad_request");
	$huidige_voorraad[2]=$db->f("voorraad_allotment");
	$huidige_voorraad[3]=$db->f("voorraad_garantie");
	$huidige_voorraad[4]=$db->f("voorraad_optie_leverancier");
}

// voorraad vermelden bij keuzes "Deze accommodatie hebben wij..."
foreach ($vars["bestelmailfax_soort"][$bmftaal] as $key => $value) {
	$deze_accommodatie_hebben_wij[$key]=$value;

	if($afgeboekte_voorraad==$key) {
		$deze_accommodatie_hebben_wij[$key].=" - (eerder gekozen)";
	} else {
		$deze_accommodatie_hebben_wij[$key].=" - (actuele voorraad: ".$huidige_voorraad[$key].")";
	}
}




$form->field_select(1,"soort",$vars["bestelmailfax_dezeacchebbenwij"][$bmftaal],"",array("selection"=>$afgeboekte_voorraad),array("selection"=>$deze_accommodatie_hebben_wij));
$form->field_text(0,"geldig",$vars["bestelmailfax_tot"][$bmftaal],"",array("text"=>$invullen["looptaf"]));
$form->field_htmlrow("","<hr>");
$form->field_text(1,"clientsname",$vars["bestelmailfax_klantnaam"][$bmftaal],"",array("text"=>wt_naam($invullen["voornaam"],$invullen["tussenvoegsel"],$invullen["achternaam"])));
$form->field_text(1,"dateofarrival",$vars["bestelmailfax_aankomst"][$bmftaal],"",array("text"=>date("d/m/Y",$invullen["aankomstdatum_exact"])));
$form->field_text(1,"stayingtime",$vars["bestelmailfax_verblijfsduur"][$bmftaal],"",array("text"=>$invullen["aantalnachten"]." ".$vars["bestelmailfax_nachten"][$bmftaal]));
$form->field_text(1,"resort",$vars["bestelmailfax_plaats"][$bmftaal],"",array("text"=>$invullen["accinfo"]["plaats"]));
if($invullen["accinfo"]["accommodatie"]<>$invullen["accinfo"]["abestelnaam"]) {
	$acc_naam=$invullen["accinfo"]["abestelnaam"]." (our name: ".$invullen["accinfo"]["accommodatie"].")";
} else {
	$acc_naam=$invullen["accinfo"]["accommodatie"];
}
$form->field_text(1,"accommodation",$vars["bestelmailfax_accommodatie"][$bmftaal],"",array("text"=>$acc_naam));
$form->field_text(0,"type",$vars["bestelmailfax_type"][$bmftaal],"",array("text"=>$invullen["accinfo"]["code"]));
$form->field_text(1,"maxcapacity",$vars["bestelmailfax_maxcapaciteit"][$bmftaal],"",array("text"=>$invullen["accinfo"]["maxaantalpersonen"]." ".($invullen["accinfo"]["maxaantalpersonen"]==1 ? $vars["bestelmailfax_persoon"][$bmftaal] : $vars["bestelmailfax_personen"][$bmftaal])));
$db->query("SELECT t.bruto, t.korting_percentage, t.inkoopkorting_percentage, t.inkoopkorting_euro, t.korting_euro, t.c_bruto, t.c_korting_percentage, t.c_korting_euro, t.arrangementsprijs, t.korting_arrangement_bed_percentage, t.vroegboekkorting_percentage, t.vroegboekkorting_euro, t.vroegboekkorting_arrangement_percentage, t.vroegboekkorting_arrangement_euro, t.c_vroegboekkorting_percentage, t.c_vroegboekkorting_euro, UNIX_TIMESTAMP(ts.vroegboekkorting_percentage_datum) AS vroegboekkorting_percentage_datum, UNIX_TIMESTAMP(ts.vroegboekkorting_euro_datum) AS vroegboekkorting_euro_datum, UNIX_TIMESTAMP(ts.vroegboekkorting_arrangement_percentage_datum) AS vroegboekkorting_arrangement_percentage_datum, UNIX_TIMESTAMP(ts.vroegboekkorting_arrangement_euro_datum) AS vroegboekkorting_arrangement_euro_datum, UNIX_TIMESTAMP(ts.c_vroegboekkorting_euro_datum) AS c_vroegboekkorting_euro_datum, UNIX_TIMESTAMP(ts.c_vroegboekkorting_percentage_datum) AS c_vroegboekkorting_percentage_datum FROM tarief t, type_seizoen ts WHERE t.type_id='".$invullen["accinfo"]["typeid"]."' AND t.week='".$invullen["aankomstdatum"]."' AND t.seizoen_id='".$invullen["seizoenid"]."' AND ts.seizoen_id=t.seizoen_id AND ts.type_id=t.type_id;");
if($db->next_record()) {
	if($vars["temp_leverancier"]["zwitersefranken"]==1) {
		$valuta="CHF";
	} else {
		$valuta="EURO";
	}
	if($invullen["accinfo"]["toonper"]==1) {
		# Tarievenoptie A Prijs Arrangement (accommodatie + skipas)
		$price=$db->f("bruto");
		if($db->f("korting_euro")>0) {
			$commission=$valuta." ".number_format($db->f("korting_euro"),2,',','.');
		} elseif($db->f("korting_percentage")>0) {
			$commission=$db->f("korting_percentage");
			$commission=ereg_replace("\.00$","",$commission)."%";
		}
		if($db->f("vroegboekkorting_percentage")>0 and (!$db->f("vroegboekkorting_percentage_datum") or $db->f("vroegboekkorting_percentage_datum")>=time())) {
			$vroegboekkorting=$db->f("vroegboekkorting_percentage")."%";
			$vroegboekkorting=ereg_replace("\.00","",$vroegboekkorting);
		}
		if($db->f("vroegboekkorting_euro")>0 and (!$db->f("vroegboekkorting_euro_datum") or $db->f("vroegboekkorting_euro_datum")>=time())) {
			$vroegboekkorting=$valuta." ".$db->f("vroegboekkorting_euro");
		}
		if($db->f("inkoopkorting_percentage")>0) {
			$vroegboekkorting=$db->f("inkoopkorting_percentage")."%";
			$vroegboekkorting=ereg_replace("\.00","",$vroegboekkorting);
		}
		if($db->f("inkoopkorting_euro")>0) {
			$vroegboekkorting=$valuta." ".$db->f("inkoopkorting_euro");
		}
	} elseif($invullen["accinfo"]["toonper"]==2) {
		# Tarievenoptie B : Prijs Arrangement (pakket + toeslag onbezet bed)
		$price=$db->f("arrangementsprijs");
#		$commission=$db->f("korting_arrangement_bed_percentage");
	} elseif($invullen["accinfo"]["toonper"]==3) {
		# Tarievenoptie C : Prijs Accommodatie
		$price=$db->f("c_bruto");

		if($db->f("c_korting_euro")>0) {
			$commission=$valuta." ".number_format($db->f("c_korting_euro"),2,',','.');
		} elseif($db->f("c_korting_percentage")>0) {
			$commission=$db->f("c_korting_percentage");
			$commission=ereg_replace("\.00$","",$commission)."%";
		}

		if($db->f("c_vroegboekkorting_percentage")>0 and (!$db->f("c_vroegboekkorting_percentage_datum") or $db->f("c_vroegboekkorting_percentage_datum")>=time())) {
			$vroegboekkorting=$db->f("c_vroegboekkorting_percentage")."%";
			$vroegboekkorting=ereg_replace("\.00","",$vroegboekkorting);
		}
		if($db->f("c_vroegboekkorting_euro")>0 and (!$db->f("c_vroegboekkorting_euro_datum") or $db->f("c_vroegboekkorting_euro_datum")>=time())) {
			$vroegboekkorting=$valuta." ".number_format($db->f("c_vroegboekkorting_euro"),2,',','.');
		}

		if($db->f("inkoopkorting_percentage")>0) {
			$vroegboekkorting=$db->f("inkoopkorting_percentage")."%";
			$vroegboekkorting=ereg_replace("\.00","",$vroegboekkorting);
		}
		if($db->f("inkoopkorting_euro")>0) {
			$vroegboekkorting=$valuta." ".$db->f("inkoopkorting_euro");
		}
	}
}
$form->field_text(1,"price",$vars["bestelmailfax_prijs"][$bmftaal],"",array("text"=>$valuta." ".number_format($price,2,',','.')));
$form->field_text(1,"commission",$vars["bestelmailfax_korting"][$bmftaal],"",array("text"=>$commission));
if($vars["temp_leverancier"]["zwitersefranken"]==1) {
	$form->field_htmlrow("","<br><span style=\"color:red;font-weight:bold;\">Let op: omrekenkoers van CHF naar EURO op &quot;0&quot; zetten!</span>");
}

$form->field_text(0,"vroegboekkorting",$vars["bestelmailfax_vroegboekkorting"][$bmftaal],"",array("text"=>$vroegboekkorting));
$form->field_textarea(0,"extra",$vars["bestelmailfax_extra"][$bmftaal],"",array("text"=>$gegevens["stap1"]["opmerkingen_voucher"]));
#$form->field_text(1,"ondertekennaam",$vars["bestelmailfax_metvriendelijkegroet"][$bmftaal],"",array("text"=>wt_naam($login->vars["voornaam"],$login->vars["tussenvoegsel"],$login->vars["achternaam"])));
$form->field_text(1,"ondertekennaam",$vars["bestelmailfax_metvriendelijkegroet"][$bmftaal],"",array("text"=>$login->vars["voornaam"]));
if($_GET["t"]==1) {
	$form->field_select(1,"afzendmailadres","Versturen vanaf","",array("selection"=>($_GET["info"] ? 2 : 1)),array("selection"=>array(1=>$login->vars["email"],2=>"info@chalet.nl")));
}

$form->check_input();

if($form->okay) {
	if($_GET["t"]==1) {

		# Klantnaam opslaan
		$db->query("UPDATE boeking SET aan_leverancier_doorgegeven_naam='".addslashes($form->input["clientsname"])."' WHERE boeking_id='".intval($_GET["bid"])."';");

		unset($html);
		$html.="<html><body style=\"font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 0.8em;\">";
		$html.="<p>".nl2br(htmlentities($vars["temp_leverancier"]["adresregels"]))."<p>";
		$html.=htmlentities($vars["bestelmailfax_beste"][$bmftaal])." ".htmlentities($form->input["contactpersoon"]).",<p>";
		$html.=htmlentities($vars["bestelmailfax_hierbijwillenwe"][$bmftaal]);
		if($_GET["reserveringsnummer"]) {
			$html.=": ".wt_he($vars["bestelmailfax_soort"][$bmftaal][4]." ".$_GET["reserveringsnummer"]);
		} else {
			$html.=".";
		}
		$html.="<p>";
		$html.=htmlentities($vars["bestelmailfax_dezeacchebbenwij"][$bmftaal])." ".htmlentities($vars["bestelmailfax_soort"][$bmftaal][$form->input["soort"]]);
		if($form->input["geldig"]) $html.=" ".htmlentities($vars["bestelmailfax_tot"][$bmftaal])." ".htmlentities($form->input["geldig"]);
		$html.=":<p>";
		$html.="<table cellspacing=\"0\" cellpadding=\"0\" style=\"font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 1.0em;\">";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_klantnaam"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["clientsname"])."</td></tr>";
		$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_aankomst"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["dateofarrival"])."</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_verblijfsduur"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["stayingtime"])."</td></tr>";
		$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_plaats"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["resort"])."</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_accommodatie"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["accommodation"])."</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_type"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["type"])."</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_maxcapaciteit"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["maxcapacity"])."</td></tr>";
		$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_prijs"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["price"])."</td></tr>";
		$html.="<tr><td>".htmlentities($vars["bestelmailfax_korting"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["commission"])."</td></tr>";
		if($form->input["vroegboekkorting"]) $html.="<tr><td>".htmlentities($vars["bestelmailfax_vroegboekkorting"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".htmlentities($form->input["vroegboekkorting"])."</td></tr>";

		if($form->input["extra"]) {
			$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
			$html.="<tr><td valign=\"top\">".htmlentities($vars["bestelmailfax_extra"][$bmftaal])."</td><td valign=\"top\">&nbsp;&nbsp;&nbsp;:&nbsp;</td><td valign=\"top\">".nl2br(htmlentities($form->input["extra"]))."</td></tr>";
		}
		$html.="</table><p>";
		$html.=htmlentities($vars["bestelmailfax_graagpermailoffaxbevestigen"][$bmftaal])."<p>";
		$html.=htmlentities($vars["bestelmailfax_metvriendelijkegroet"][$bmftaal]).",<br><br>";
		$html.=htmlentities($form->input["ondertekennaam"])."<p>";
		if($gegevens["stap1"]["accinfo"]["wzt"]==2) {
			$temp_websitenaam="Chalet.nl / Zomerhuisje.nl";
		} else {
			$temp_websitenaam="Chalet.nl";
		}
		$html.=$temp_websitenaam."<br>Wipmolenlaan 3<br>3447 GJ Woerden<br>KvK: 30209634<br>Tel: +31 (0)348 - 43 46 49<br>Fax: +31 (0)348 - 69 07 52<br>E-mail: <a href=\"mailto:info@chalet.nl\">info@chalet.nl</a><p>";
		$html.="</body></html>";
		$mail=new wt_mail;
		$mail->fromname=$temp_websitenaam;
		if($form->input["afzendmailadres"]==1) {
			$mail->from=$login->vars["email"];
		} else {
			$mail->from="info@chalet.nl";
		}
		$mail->subject=$temp_websitenaam." ".$vars["bestelmailfax_klant"][$bmftaal]." ".$form->input["clientsname"]." ".$form->input["dateofarrival"]." ".$form->input["resort"]." / ".$form->input["accommodation"].($form->input["type"] ? " ".$form->input["type"] : "");
		$mail->html=$html;

		# mailtje naar leverancier sturen
		$mail->to=$vars["temp_leverancier"]["email"];
		$mail->send();

		# mailtje ook naar chalet sturen
		if($form->input["afzendmailadres"]==1) {
			$mail->to=$login->vars["email"];
		} else {
			$mail->to="info@chalet.nl";
		}
		$mail->send();

		if($_GET["oaid"]) {
			# Status optieaanvraag veranderen in "geboekt"
			$db->query("UPDATE optieaanvraag SET status=8 WHERE optieaanvraag_id='".addslashes($_GET["oaid"])."';");
		} else {

			# bestelstatus op 'bevestiging afwachten' zetten en besteldatum op vandaag zetten
			$db->query("UPDATE boeking SET bestelstatus=2, besteldatum=NOW() WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

			# Loggen bij boeking
			chalet_log("bestelmail ".$vars["bestelmailfax_soort"]["N"][$form->input["soort"]]." verzonden naar ".$vars["temp_leverancier"]["email"],false,true);
			chalet_log("besteldatum op ".date("d-m-Y")." gezet, bestelstatus veranderd in 'bevestiging afwachten'",false,true);
		}
	} else {

		if($gegevens["stap1"]["accinfo"]["wzt"]==2) {
			$vars["temp_websitenaam"]="Chalet.nl / Zomerhuisje.nl";
		} else {
			$vars["temp_websitenaam"]="Chalet.nl";
		}

		# bestelstatus op 'bevestiging afwachten' zetten en besteldatum op vandaag zetten
		$db->query("UPDATE boeking SET bestelstatus=2, besteldatum=NOW() WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

		# Loggen bij boeking
		chalet_log("bestelfax ".$vars["bestelmailfax_soort"]["N"][$form->input["soort"]]." aangemaakt",false,true);
		chalet_log("besteldatum op ".date("d-m-Y")." gezet, bestelstatus veranderd in 'bevestiging afwachten'",false,true);

		require("admin/fpdf.php");

		class PDF extends FPDF {

			function _getfontpath() {
				return "pdf/fonts/";
			}

			function Header() {
				if($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==3) {
					$this->Image('pic/factuur_logo_vakantiewoningen.png',10,8,50);
				} elseif($this->gegevens["stap1"]["website_specifiek"]["websitetype"]==2) {
					if($this->vars["temp_leverancier"]["bestelfax_logo"]) {
						$this->Image('pic/factuur_logo_wsa.png',10,10,100);
					} else {
						$this->Image('pic/factuur_logo.png',10,8,50);
					}
				} else {
					$this->Image('pic/factuur_logo.png',10,8,50);
				}
				$this->SetFont('Arial','',10);
				$this->MultiCell(0,4,"".$this->vars["temp_websitenaam"]."\nWipmolenlaan 3\n3447 GJ Woerden\n\nTel.: +31 348 434649\nFax: +31 348 690752\nKvK nr. 30209634\n\nBankrek. 84.93.06.671\nBTW NL-8169.23.462.B.01\n\nIBAN: NL21 ABNA 0849 3066 71\nBIC: ABNANL2A\nABN AMRO - Woerden",0,"R");
				$this->Ln(20);
			}

			function Footer() {
				$this->SetY(-20);
				$this->SetFont('Arial','I',8);
				$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
			}
		}

		$pdf=new PDF();
		$pdf->gegevens=$gegevens;
		$pdf->vars=$vars;

		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetY(60);
		$pdf->MultiCell(150,4,$vars["temp_leverancier"]["adresregels"],0,'L',0);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell(0,4,"Woerden, ".DATUM("D")." ".date("F Y"),0,1);
		$pdf->Ln();
		$pdf->Cell(0,4,"fax: ".$vars["temp_leverancier"]["faxnummer"],0,1);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell(0,4,$vars["bestelmailfax_beste"][$bmftaal]." ".$form->input["contactpersoon"].",",0,1);
		$pdf->Ln();
		$pdf->Cell(0,4,$vars["bestelmailfax_hierbijwillenwe"][$bmftaal].".",0,1);
		$pdf->Ln();
		$pdf->Cell(0,4,$vars["bestelmailfax_dezeacchebbenwij"][$bmftaal]." ".$vars["bestelmailfax_soort"][$bmftaal][$form->input["soort"]].($form->input["geldig"] ? " ".$vars["bestelmailfax_tot"][$bmftaal]." ".$form->input["geldig"] : "").":",0,1);
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_klantnaam"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["clientsname"],0,0,'L',0);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_aankomst"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["dateofarrival"],0,0,'L',0);
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_verblijfsduur"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["stayingtime"],0,0,'L',0);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_plaats"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["resort"],0,0,'L',0);
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_accommodatie"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["accommodation"],0,0,'L',0);
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_type"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["type"],0,0,'L',0);
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_maxcapaciteit"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["maxcapacity"],0,0,'L',0);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_prijs"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["price"],0,0,'L',0);
		$pdf->Ln();

		$pdf->Cell(35,4,$vars["bestelmailfax_korting"][$bmftaal],0,0,'L',0);
		$pdf->Cell(5,4,":",0,0,'L',0);
		$pdf->Cell(50,4,$form->input["commission"],0,0,'L',0);
		$pdf->Ln();

		if($form->input["vroegboekkorting"]) {
			$pdf->Cell(35,4,$vars["bestelmailfax_vroegboekkorting"][$bmftaal],0,0,'L',0);
			$pdf->Cell(5,4,":",0,0,'L',0);
			$pdf->Cell(50,4,$form->input["vroegboekkorting"],0,0,'L',0);
			$pdf->Ln();
		}

		$pdf->Ln();

		if($form->input["extra"]) {
			$pdf->Cell(35,4,$vars["bestelmailfax_extra"][$bmftaal],0,0,'L',0);
			$pdf->Cell(5,4,":",0,0,'L',0);
			$pdf->MultiCell(150,4,$form->input["extra"],0,'L',0);
			$pdf->Ln();
			$pdf->Ln();
		}

		$pdf->Cell(0,4,$vars["bestelmailfax_graagpermailoffaxbevestigen"][$bmftaal],0,1);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->Cell(0,4,$vars["bestelmailfax_metvriendelijkegroet"][$bmftaal].",",0,1);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell(0,4,$form->input["ondertekennaam"],0,1);

		$pdf->Output();
		exit;
	}
}

$form->end_declaration();

$layout->display_all("Bestellen via e-mail/fax");

?>