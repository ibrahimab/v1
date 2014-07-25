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
$form->settings["message"]["submitbutton"]["nl"]="BESTELLING MAILEN";

#_field: (obl),id,title,db,prevalue,options,layout
if($gegevens["stap1"]["verzameltype"]) {
	if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
		$form->field_htmlrow("","<span class=\"error\">verzameltype-boeking - het gekozen onderliggende type zal worden besteld</span>");
	} else {
		$form->field_htmlrow("","<span class=\"error\">verzameltype-boeking - let op: er is nog geen onderliggend type geselecteerd</span>");
	}
}

$form->field_htmlcol("aan","Mailadres",array("text"=>$vars["temp_leverancier"]["email"]));
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

if($gegevens["stap1"]["flexibel"] or $gegevens["stap1"]["verblijfsduur"]>1) {
	$form->field_htmlrow("","<hr><b>Let op: afwijkende verblijfsduur. Onderstaande velden zelf opzoeken en invullen.</b>");
} else {

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
}
$form->field_text(1,"price",$vars["bestelmailfax_prijs"][$bmftaal],"",array("text"=>($price>0 ? $valuta." ".number_format($price,2,',','.') : "")));
$form->field_text(1,"commission",$vars["bestelmailfax_korting"][$bmftaal],"",array("text"=>$commission));
if($vars["temp_leverancier"]["zwitersefranken"]==1) {
	$form->field_htmlrow("","<br><span style=\"color:red;font-weight:bold;\">Let op: omrekenkoers van CHF naar EURO op &quot;0&quot; zetten!</span>");
}

$form->field_text(0,"vroegboekkorting",$vars["bestelmailfax_vroegboekkorting"][$bmftaal],"",array("text"=>$vroegboekkorting));
if($gegevens["stap1"]["flexibel"] or $gegevens["stap1"]["verblijfsduur"]>1) {
	$form->field_htmlrow("","<hr>");
}

$form->field_textarea(0,"extra",$vars["bestelmailfax_extra"][$bmftaal],"",array("text"=>$gegevens["stap1"]["opmerkingen_voucher"]));
#$form->field_text(1,"ondertekennaam",$vars["bestelmailfax_metvriendelijkegroet"][$bmftaal],"",array("text"=>wt_naam($login->vars["voornaam"],$login->vars["tussenvoegsel"],$login->vars["achternaam"])));
$form->field_text(1,"ondertekennaam",$vars["bestelmailfax_metvriendelijkegroet"][$bmftaal],"",array("text"=>$login->vars["voornaam"]));
$form->field_select(1,"afzendmailadres","Versturen vanaf","",array("selection"=>($_GET["info"] ? 2 : 1)),array("selection"=>array(1=>$login->vars["email"],2=>"info@chalet.nl")));


$form->check_input();

if($form->okay) {

	# Klantnaam opslaan
	$db->query("UPDATE boeking SET aan_leverancier_doorgegeven_naam='".addslashes($form->input["clientsname"])."' WHERE boeking_id='".intval($_GET["bid"])."';");

	unset($html);
	$html.="<html><body style=\"font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 0.8em;\">";
	$html.="<p>".nl2br(wt_he($vars["temp_leverancier"]["adresregels"]))."<p>";
	$html.=wt_he($vars["bestelmailfax_beste"][$bmftaal])." ".wt_he($form->input["contactpersoon"]).",<p>";
	$html.=wt_he($vars["bestelmailfax_hierbijwillenwe"][$bmftaal]);
	if($_GET["reserveringsnummer"]) {
		$html.=": ".wt_he($vars["bestelmailfax_soort"][$bmftaal][4]." ".$_GET["reserveringsnummer"]);
	} else {
		$html.=".";
	}
	$html.="<p>";
	$html.=wt_he($vars["bestelmailfax_dezeacchebbenwij"][$bmftaal])." ".wt_he($vars["bestelmailfax_soort"][$bmftaal][$form->input["soort"]]);
	if($form->input["geldig"]) $html.=" ".wt_he($vars["bestelmailfax_tot"][$bmftaal])." ".wt_he($form->input["geldig"]);
	$html.=":<p>";
	$html.="<table cellspacing=\"0\" cellpadding=\"0\" style=\"font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 1.0em;\">";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_klantnaam"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["clientsname"])."</td></tr>";
	$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_aankomst"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["dateofarrival"])."</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_verblijfsduur"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["stayingtime"])."</td></tr>";
	$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_plaats"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["resort"])."</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_accommodatie"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["accommodation"])."</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_type"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["type"])."</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_maxcapaciteit"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["maxcapacity"])."</td></tr>";
	$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_prijs"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["price"])."</td></tr>";
	$html.="<tr><td>".wt_he($vars["bestelmailfax_korting"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["commission"])."</td></tr>";
	if($form->input["vroegboekkorting"]) $html.="<tr><td>".wt_he($vars["bestelmailfax_vroegboekkorting"][$bmftaal])."</td><td>&nbsp;&nbsp;&nbsp;:&nbsp;</td><td>".wt_he($form->input["vroegboekkorting"])."</td></tr>";

	if($form->input["extra"]) {
		$html.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
		$html.="<tr><td valign=\"top\">".wt_he($vars["bestelmailfax_extra"][$bmftaal])."</td><td valign=\"top\">&nbsp;&nbsp;&nbsp;:&nbsp;</td><td valign=\"top\">".nl2br(wt_he($form->input["extra"]))."</td></tr>";
	}
	$html.="</table><p>";
	$html.=wt_he($vars["bestelmailfax_graagpermailoffaxbevestigen"][$bmftaal])."<p>";
	$html.=wt_he($vars["bestelmailfax_metvriendelijkegroet"][$bmftaal]).",<br><br>";
	$html.=wt_he($form->input["ondertekennaam"])."<p>";
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
		$db->query("UPDATE boeking SET bestelstatus=2, besteldatum=NOW(), besteluser_id='".intval($login->user_id)."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

		# Loggen bij boeking
		chalet_log("bestelmail ".$vars["bestelmailfax_soort"]["N"][$form->input["soort"]]." verzonden naar ".$vars["temp_leverancier"]["email"],false,true);
		chalet_log("besteldatum op ".date("d-m-Y")." gezet, bestelstatus veranderd in 'bevestiging afwachten'",false,true);
	}
}

$form->end_declaration();

$layout->display_all("Bestellen via e-mail");

?>