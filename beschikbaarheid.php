<?php

include("admin/vars.php");

# Session starten voor TradeTracker
session_start();

# Kijken of het een rebook betreft (boeker van vorig jaar)
if($_COOKIE["rebook"] and ereg("^([0-9]+)_(.+)$",$_COOKIE["rebook"],$regs)) {
	if($regs[2]==substr(sha1($regs[1]."_WT_488439fk3"),0,8)) {
		$vars["rebook"]=true;
		$db->query("SELECT boekingsnummer FROM boeking WHERE boeking_id='".addslashes($regs[1])."';");
		if($db->next_record()) {
			$vars["oud_boekingsnummer"]=$db->f("boekingsnummer");
			$vars["oud_boekingid"]=$regs[1];
		}
	}
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"]["nl"]=strtoupper(txt("beschikbaarheidcontroleren".($_GET["o"] ? "_optie" : ""),"beschikbaarheid"));
$form->settings["language"]=$vars["taal"];
if($_GET["o"]) {
	$form->settings["fullname"]="Optie-aanvraag";
	if($voorkant_cms) {
		$obl=0;
		$werknemer_optieaanvraag=true;
		$form->settings["message"]["submitbutton"]["nl"]="OPTIE TOEVOEGEN AAN OPTIE-AANVRAGENSYSTEEM";
	} else {
		$obl=1;
	}
} else {
	$form->settings["fullname"]="Beschikbaarheidaanvraag";
	$obl=1;
}

# _field: (obl),id,title,db,prevalue,options,layout

$accinfo=accinfo($_GET["tid"]);
if($werknemer_optieaanvraag) {
	$form->field_htmlrow("","<span class=\"intern\">Optieaanvraag toevoegen</span>");
}
$form->field_noedit("accnaam",txt("accommodatie","beschikbaarheid"),"",array("text"=>ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam"]." (".$accinfo["aantalpersonen"].")"));
$form->field_noedit("accplaats",txt("plaats","beschikbaarheid"),"",array("text"=>$accinfo["plaats"].", ".$accinfo["land"]));
$form->field_select(1,"aantalpersonen",txt("aantalpersonen","beschikbaarheid"),"",array("selection"=>$_GET["ap"]),array("selection"=>$accinfo["aantalpersonen_array"]));
if($accinfo["wzt"]==2) {
	# flexibel

	# aankomstdatum
	if($_GET["flad"]) {
		$temp_aankomstdatum_exact=$_GET["flad"];
	} elseif($_GET["d"]) {
		$temp_aankomstdatum_exact=$_GET["d"];
	}
	if($accinfo["flexibel"]) {
		$form->field_date(1,"aankomstdatum_flex",txt("aankomstdatum","beschikbaarheid"),"",array("time"=>$temp_aankomstdatum_exact),array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
	} else {
		@reset($accinfo["aankomstdatum_beschikbaar"]);
		unset($temp_aankomstdata);
		while(list($key,$value)=@each($accinfo["aankomstdatum_beschikbaar"])) {
			if($key>time()) {
				$temp_aankomstdata[$key]=$value;
			}
		}
		$form->field_select(1,"aankomstdatum",txt("aankomstdatum","beschikbaarheid"),"",array("selection"=>$_GET["d"]),array("selection"=>$temp_aankomstdata));
	}

	# verblijfsduur
	unset($vars["verblijfsduur"]);
	$vars["verblijfsduur"]["1"]="1 ".txt("week","vars");
	$vars["verblijfsduur"]["2"]="2 ".txt("weken","vars");
	$vars["verblijfsduur"]["3"]="3 ".txt("weken","vars");
	$vars["verblijfsduur"]["4"]="4 ".txt("weken","vars");

	if($accinfo["flexibel"]) {
		// $vars["verblijfsduur"]["1n"]="1 ".txt("nacht","vars");
		for($i=3;$i<=$vars["flex_max_aantalnachten"];$i++) {
			$vars["verblijfsduur"][$i."n"]=$i." ".txt("nachten","vars");
		}
	}
	if($_GET["fldu"]) {
		$temp_verblijfsduur=$_GET["fldu"];
	} elseif($_GET["fdu"]>0 and !$_GET["fldu"]) {
		$temp_verblijfsduur=$_GET["fdu"];
	} else {
		$temp_verblijfsduur=1;
	}
	$form->field_select(1,"verblijfsduur",txt("verblijfsduur","beschikbaarheid"),"",array("selection"=>$temp_verblijfsduur),array("selection"=>$vars["verblijfsduur"],"optgroup"=>array("1"=>txt("aantalweken"),"3n"=>txt("aantalnachten"))));
} else {
	@reset($accinfo["aankomstdatum_beschikbaar"]);
	unset($temp_aankomstdata);
	while(list($key,$value)=@each($accinfo["aankomstdatum_beschikbaar"])) {
		if($key>time()) {
			$temp_aankomstdata[$key]=$value;
		}
	}
	$form->field_select(1,"aankomstdatum",txt("aankomstdatum","beschikbaarheid"),"",array("selection"=>$_GET["d"]),array("selection"=>$temp_aankomstdata));
}

if(!$werknemer_optieaanvraag) {
	# Opties (bus, catering e.d.) tonen
	$db->query("SELECT os.optie_soort_id, os.tekst_beschikbaarheid".$vars["ttv"]." AS tekst_beschikbaarheid FROM optie_soort os, optie_accommodatie oa, optie_groep og WHERE oa.accommodatie_id='".addslashes($accinfo["accommodatie_id"])."' AND og.optie_groep_id=oa.optie_groep_id AND os.optie_soort_id=oa.optie_soort_id AND os.tekst_beschikbaarheid".$vars["ttv"]."<>'' ORDER BY os.volgorde, os.tekst_beschikbaarheid".$vars["ttv"].";");
	while($db->next_record()) {
		$form->field_select(1,"inclusief_optie_".$db->f("optie_soort_id"),"Inclusief ".$db->f("tekst_beschikbaarheid"),"","",array("selection"=>array(1=>"ja",2=>"nee")));
		$inclusief_optie_array["inclusief_optie_".$db->f("optie_soort_id")]=$db->f("tekst_beschikbaarheid");
	}
}

$temp_naw=getnaw();
if($vars["wederverkoop"] and $vars["chalettour_logged_in"]) {
	$form->field_htmlrow("","<hr><b>".html("gegevensklant","beschikbaarheid")."</b>");
	$form->field_text($obl,"voornaam",txt("voornaam","beschikbaarheid"),"",array("text"=>$temp_naw["voornaam"]));
	$form->field_text(0,"tussenvoegsel",txt("tussenvoegsel","beschikbaarheid"),"",array("text"=>$temp_naw["tussenvoegsel"]));
	$form->field_text($obl,"achternaam",txt("achternaam","beschikbaarheid"),"",array("text"=>$temp_naw["achternaam"]));
	$form->field_text($obl,"plaats",txt("woonplaats","beschikbaarheid"),"",array("text"=>$temp_naw["plaats"]));
	$form->field_text(0,"telefoonnummer",txt("telefoonnummer","beschikbaarheid")."<br><span class=\"kleinfont\">(".txt("telefoonnummer_toelichtingwederverkoop","beschikbaarheid").")</span>","",array("text"=>$temp_naw["telefoonnummer"]),"",array("title_html"=>true));
	$form->field_htmlrow("","<hr>");
	$wederverkoop_aanvraag=true;
} else {
	$form->field_text($obl,"voornaam",txt("voornaam","beschikbaarheid"),"",array("text"=>$temp_naw["voornaam"]));
	$form->field_text(0,"tussenvoegsel",txt("tussenvoegsel","beschikbaarheid"),"",array("text"=>$temp_naw["tussenvoegsel"]));
	$form->field_text($obl,"achternaam",txt("achternaam","beschikbaarheid"),"",array("text"=>$temp_naw["achternaam"]));
	$form->field_text($obl,"adres",txt("adres","beschikbaarheid"),"",array("text"=>$temp_naw["adres"]));
	$form->field_text($obl,"postcode",txt("postcode","beschikbaarheid"),"",array("text"=>$temp_naw["postcode"]));
	$form->field_text($obl,"plaats",txt("woonplaats","beschikbaarheid"),"",array("text"=>$temp_naw["plaats"]));
	$form->field_text($obl,"land",txt("land","beschikbaarheid"),"",array("text"=>($temp_naw["land"] ? $temp_naw["land"] : ($vars["taal"]=="nl" ? "Nederland" : ""))));
	$form->field_text($obl,"telefoonnummer",txt("telefoonnummer","beschikbaarheid"),"",array("text"=>$temp_naw["telefoonnummer"]));
	$form->field_text(0,"mobielwerk",txt("mobielwerk","beschikbaarheid"),"",array("text"=>$temp_naw["mobielwerk"]));
	$form->field_email($obl,"email",txt("email","beschikbaarheid"),"",array("text"=>$temp_naw["email"]));
}
if(!$_GET["o"]) {
	$form->field_yesno("optie",html("ikwiloptie","beschikbaarheid")."<br>".html("max1pergroep","beschikbaarheid"),"",array("selection"=>$_GET["o"]),"",array("title_html"=>true));
}
if($werknemer_optieaanvraag) {
	$form->field_htmlrow("","<hr>");
	$form->field_date(1,"einddatum_klant","<span class=\"intern\">Einddatum klant</span>","","","",array("title_html"=>true,"calendar"=>true));
	$form->field_textarea(0,"opmerkingen_intern","<span class=\"intern\">Interne opmerkingen</span>","","","",array("title_html"=>true,"newline"=>false));
} else {
	$form->field_textarea(0,"wensenbezet",txt("wensenindienbezet","beschikbaarheid"),"","","",array("newline"=>false));
	$form->field_textarea(0,"vraag",txt("vragenopmerkingen","beschikbaarheid"),"","","",array("newline"=>false));

	if($vars["nieuwsbrief_aanbieden"]) {
		if($vars["nieuwsbrief_tijdelijk_kunnen_afmelden"]) {
			# Nieuwsbrief: kiezen tussen direct/einde van het seizoen/nee
			$form->field_radio(0,"nieuwsbrief","<div style=\"height:7px;\"></div>Wil je de ".$vars["websitenaam"]."-nieuwsbrief ontvangen?","",array("selection"=>3),array("selection"=>array(1=>"Ja, per direct",2=>"Ja, tegen het einde van dit winterseizoen, met nieuws over het volgende winterseizoen",3=>"Nee, ik wil geen nieuwsbrief ontvangen")),array("one_per_line"=>true,"newline"=>true,"tr_class"=>"nieuwsbrief_per_wanneer","title_html"=>true));
		} else {
			# Nieuwsbrief: kiezen tussen ja/nee
			$nieuwsbrief_vraag=txt("nieuwsbriefvraag","contact",array("v_websitenaam"=>$vars["websitenaam"]));
			$form->field_yesno("nieuwsbrief",$nieuwsbrief_vraag,"",array("selection"=>false));
		}
	}
}

$form->check_input();

if($form->filled) {
	if($accinfo["flexibel"] or $form->input["verblijfsduur"]>1) {
		# flexibel - controle op tarief/beschikbaarheid
		if($accinfo["flexibel"]) {
			$flextarief=bereken_flex_tarief($_GET["tid"],$form->input["aankomstdatum_flex"]["unixtime"],0,flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]));
		} else {
			$flextarief=bereken_flex_tarief($_GET["tid"],$form->input["aankomstdatum"],0,flex_bereken_vertrekdatum($form->input["aankomstdatum"],$form->input["verblijfsduur"]));
		}
		if($flextarief["tarief"]>0) {

		} else {
			$form->error("aankomstdatum_flex",txt("gekozenperiodenietbeschikbaar","beschikbaarheid"));
			$form->error("verblijfsduur",txt("gekozenperiodenietbeschikbaar","beschikbaarheid"));
		}
	}
}

if($form->okay) {

	if($wederverkoop_aanvraag) {
		$form->input["email"]=$login_rb->vars["email"];
	}

	if($_GET["o"] or $form->input["optie"]) {

		# Einddatum_klant bepalen
		if($werknemer_optieaanvraag and $form->input["einddatum_klant"]["unixtime"]) {
			$einddatum_klant=$form->input["einddatum_klant"]["unixtime"];
		} else {
			if($vars["rebook"] and $accinfo["optiedagen_klanten_vorig_seizoen"]) {
				$geldigheidoptie=$accinfo["optiedagen_klanten_vorig_seizoen"];
			} else {
				$geldigheidoptie=3;
			}
#			$einddatum_klant=mktime(0,0,0,date("m"),date("d")+$geldigheidoptie,date("Y"));
		}

		# Status, ingevuldvia en userid bepalen
		if($werknemer_optieaanvraag) {
			# Interne aanvraag
			$status=2;
			$userid=$login->user_id;
			$ingevuldvia=3;
		} else {
			# Aanvraag door klant
			$status=1;
			if($_GET["o"]) {
				$ingevuldvia=1;
			} else {
				$ingevuldvia=2;
			}
			$userid="";
		}


		// uitgezet op 25-3-2014 (optiesysteem niet meer in gebruik)
		# Optieaanvraag opslaan
		// $setquery="website='".addslashes($vars["website"])."', type_id='".addslashes($accinfo["type_id"])."', aantalpersonen='".addslashes($form->input["aantalpersonen"])."'";
		// if($accinfo["flexibel"]) {
		// 	$form->input["aankomstdatum"]=dichtstbijzijnde_zaterdag($form->input["aankomstdatum_flex"]["unixtime"]);
		// 	$setquery.=", aankomstdatum='".addslashes($form->input["aankomstdatum"])."', aankomstdatum_exact='".addslashes($form->input["aankomstdatum_flex"]["unixtime"])."', vertrekdatum_exact='".flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"])."'";
		// } else {
		// 	$setquery.=", aankomstdatum='".addslashes($form->input["aankomstdatum"])."', aankomstdatum_exact='".addslashes($accinfo["aankomstdatum_unixtime"][$form->input["aankomstdatum"]])."'";
		// }
		// $setquery.=", status='".addslashes($status)."', opmerkingen_intern='".addslashes($form->input["opmerkingen_intern"])."', ingevuldvia='".addslashes($ingevuldvia)."', user_id='".addslashes($userid)."', invulmoment=NOW(), voornaam='".addslashes($form->input["voornaam"])."', tussenvoegsel='".addslashes($form->input["tussenvoegsel"])."', achternaam='".addslashes($form->input["achternaam"])."', adres='".addslashes($form->input["adres"])."', postcode='".addslashes($form->input["postcode"])."', plaats='".addslashes($form->input["plaats"])."', land='".addslashes($form->input["land"])."', telefoonnummer='".addslashes($form->input["telefoonnummer"])."', mobielwerk='".addslashes($form->input["mobielwerk"])."', email='".addslashes($form->input["email"])."'";
		// if($einddatum_klant>0) {
		// 	 $setquery.=", einddatum_klant=FROM_UNIXTIME('".addslashes($einddatum_klant)."')";
		// }
		// $db->query("INSERT INTO optieaanvraag SET ".$setquery.";");

		if($werknemer_optieaanvraag and $db->insert_id()) {
			header("Location: http://".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? "ss.postvak.net/chalet" : "www.chalet.nl")."/cms_optieaanvragen.php?show=35&status=2&35k0=".$db->insert_id());
			exit;
		}
	}

	if($werknemer_optieaanvraag) {

	} else {

		#
		# Optieformulier of beschikbaarheidsformulier
		#

		# Cookie plaatsen
		if(!$wederverkoop_aanvraag) {
			nawcookie($form->input["voornaam"],$form->input["tussenvoegsel"],$form->input["achternaam"],$form->input["adres"],$form->input["postcode"],$form->input["plaats"],$form->input["land"],$form->input["telefoonnummer"],$form->input["mobielwerk"],$form->input["email"],"not",$form->input["nieuwsbrief"]);
		}

		# Inschrijven nieuwsbrief
		if($form->input["nieuwsbrief"] and $form->input["nieuwsbrief"]<>"3") {
			$nieuwsbrief_waardes=array("email"=>$form->input["email"],"voornaam"=>$form->input["voornaam"],"tussenvoegsel"=>$form->input["tussenvoegsel"],"achternaam"=>$form->input["achternaam"],"per_wanneer"=>$form->input["nieuwsbrief"]);
			nieuwsbrief_inschrijven($vars["seizoentype"],$nieuwsbrief_waardes);
		}

		# "Inclusief optie"-invoer verwerken
		unset($inclusief_optie_html);
		reset($form->input);
		while(list($key,$value)=each($form->input)) {
			if(ereg("^inclusief_optie_[0-9]+$",$key)) {
				if($value==1) {
					$inclusief_optie_html.="<tr><td class=\"wtform_cell_left\">".htmlentities($form->fields["title"][$key])."</td><td class=\"wtform_cell_right\">ja</td></tr>";
					$omschrijving_inclusief.=" + ".$inclusief_optie_array[$key];
				}
			}
		}

		# verblijfsduur + aankomstdatum bepalen
		if($accinfo["flexibel"]) {
			$verblijfsduur=date("d/m/Y",$form->input["aankomstdatum_flex"]["unixtime"])."-".date("d/m/Y",flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]));
			$aankomstdatum=date("d/m/Y",$form->input["aankomstdatum_flex"]["unixtime"]);
		} else {
			$verblijfsduur=$accinfo["aankomstdatum_dmj"][$form->input["aankomstdatum"]];
			$aankomstdatum=$accinfo["aankomstdatum_dmj"][$form->input["aankomstdatum"]];
		}


		# Mail aan info@chalet.nl
		$mail=new wt_mail;
		if($_GET["o"] or $form->input["optie"]) {
			$mail->subject="Nieuwe optie-aanvraag";
		} else {
			$mail->subject="Beschikbaarheidaanvraag";
		}
		# onderwerp voorzien van extra informatie (aankomstdatum, plaats, accommodatie)
		$mail->subject.=" ".$aankomstdatum." ".$accinfo["plaats"]." / ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam"]." (max. ".$accinfo["maxaantalpersonen"]." ".txt("personen").")";

		if($wederverkoop_aanvraag) $mail->subject.=" (via ".$vars["chalettour_naam"].")";
		$mail->from="info@chalet.nl";
		$mail->fromname="Website ".$vars["websites"][$vars["website"]];

		if(ereg("@webtastic\.nl",$form->input["email"])) {
			$mail->to="chalet_test@webtastic.nl";
		} else {
			$mail->to="info@chalet.nl";
		}

		$omschrijving=$verblijfsduur;

		$omschrijving.=" ".$accinfo["plaats"]." / ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam"]." (max. ".$accinfo["maxaantalpersonen"]." ".txt("personen").") ".txt("met")." ".$form->input["aantalpersonen"]." ".txt("personen");
		$omschrijving.=$omschrijving_inclusief;

		$subject=txt(($_GET["o"]||$form->input["optie"] ? "optieaanvraag" : "beschikbaarheidaanvraag"),"beschikbaarheid");
		if(eregi("^belgie$",$form->input["land"])) $form->input["land"]="België";
		$subject.=" ".$omschrijving;
		if($form->input["verblijfsduur"]) {
			$verblijfsduur_tekst = txt("verblijfsduur","beschikbaarheid").": ".$vars["verblijfsduur"][$form->input["verblijfsduur"]]."%0D%0A%0D%0A";
		}
		if($wederverkoop_aanvraag) {
			$body=$vars["chalettour_naam"]."%0D%0A".$vars["chalettour_reisagentnaam"]."%0D%0A";
			if($login_rb->vars["telefoonnummer"]) {
				$body.=$login_rb->vars["telefoonnummer"]."%0D%0A";
			} elseif($vars["chalettour_telefoonnummer"]) {
				$body.=$vars["chalettour_telefoonnummer"]."%0D%0A";
			}
			if($login_rb->vars["mobiel"]) $body.=$login_rb->vars["mobiel"]."%0D%0A";
			$body.="%0D%0A";
			$body.=wt_naam(ucfirst($form->input["voornaam"]),$form->input["tussenvoegsel"],ucfirst($form->input["achternaam"]))."%0D%0A".ucfirst($form->input["plaats"])."%0D%0A".$form->input["telefoonnummer"]."%0D%0A%0D%0A".$verblijfsduur_tekst.$omschrijving."%0D%0A".txt("vooromschrijving","beschikbaarheid").": ".$accinfo["url"]."%0D%0A%0D%0A".ereg_replace("\n","%0D%0A",ereg_replace("\"","%22",$form->input["wensenbezet"]))."%0D%0A%0D%0A".ereg_replace("\n","%0D%0A",ereg_replace("\"","%22",$form->input["vraag"]));
		} else {
			$body=wt_naam(ucfirst($form->input["voornaam"]),$form->input["tussenvoegsel"],ucfirst($form->input["achternaam"]))."%0D%0A".$form->input["adres"]."%0D%0A".$form->input["postcode"]." ".ucfirst($form->input["plaats"])."%0D%0A".($form->input["land"]<>"Nederland" ? ucfirst($form->input["land"])."%0D%0A" : "").$form->input["telefoonnummer"]."%0D%0A".$form->input["mobielwerk"]."%0D%0A%0D%0A".$verblijfsduur_tekst.$omschrijving."%0D%0A".txt("vooromschrijving","beschikbaarheid").": ".$accinfo["url"]."%0D%0A%0D%0A".ereg_replace("\n","%0D%0A",ereg_replace("\"","%22",$form->input["wensenbezet"]))."%0D%0A%0D%0A".ereg_replace("\n","%0D%0A",ereg_replace("\"","%22",$form->input["vraag"]));
		}
		$subject=ereg_replace("&"," ",$subject);
		$subject=ereg_replace("   "," ",$subject);
		$body=ereg_replace("&"," ",$body);
		$body=ereg_replace("   "," ",$body);
		$html="<html><head>".$form->mail_css()."</head>\n<body>\n";
		$html.="<div style=\"width:660px\">";
		$html.="Reageren op dit verzoek: <a href=\"mailto:".$form->input["email"].ereg_replace(" ","%20","?subject=".$subject."&body=".$body)."\">mail sturen</a><p>";

		if($vars["rebook"]) {
			$html.="<b>Optie-aanvraag n.a.v. mail-uitnodiging (boeking vorig seizoen)</b><br>";
			if($accinfo["optiedagen_klanten_vorig_seizoen"]>3) {
				$html.="<b>Let op: langere optie-geldigheid! (".$accinfo["optiedagen_klanten_vorig_seizoen"]." dagen)</b><p>";
			}
		}

		$html.="<table class=\"wtform_table\" cellspacing=\"0\">";
		$html.="<tr><td class=\"wtform_cell_left\">Formulier</td><td class=\"wtform_cell_right\">".($_GET["o"] ? "Optie-aanvraag" : "Beschikbaarheid controleren")."</td></tr>";
		$html.="<tr><td class=\"wtform_cell_left\">Ingevuld op</td><td class=\"wtform_cell_right\">".DATUM("DAG D MAAND JJJJ")." ".date("H:i")."u.</td></tr>";

		if($vars["rebook"] and $vars["oud_boekingsnummer"]) {
			$html.="<tr><td class=\"wtform_cell_left\">Boekingsnummer vorig seizoen"."</td><td class=\"wtform_cell_right\"><a href=\"http://www.chalet.nl/cms_boekingen.php?show=21&21k0=".$vars["oud_boekingid"]."\">".$vars["oud_boekingsnummer"]."</a></td></tr>";

			# Opslaan bij oude boeking
			$db2->query("UPDATE boeking SET status_klanten_vorig_seizoen=4 WHERE status_klanten_vorig_seizoen<4 AND boeking_id='".addslashes($vars["oud_boekingid"])."';");
		}

		if($wederverkoop_aanvraag) {
			$html.="<tr><td class=\"wtform_cell_left\">Via reisagent</td><td class=\"wtform_cell_right\">".htmlentities($vars["chalettour_reisagentnaam"])." / <a href=\"http://www.chalet.nl/cms_reisbureaus.php?show=27&27k0=".$login_rb->vars["reisbureau_id"]."\">".htmlentities($vars["chalettour_naam"])."</a></td></tr>";
		}
		$html.="<tr><td class=\"wtform_cell_left\">Accommodatie</td><td class=\"wtform_cell_right\"><a href=\"".$accinfo["url"]."\">".$accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".htmlentities($accinfo["naam"])."</a> - ".$accinfo["aantalpersonen"]."</td></tr>";
		$html.="<tr><td class=\"wtform_cell_left\">Plaats</td><td class=\"wtform_cell_right\">".htmlentities($accinfo["plaats"].", ".$accinfo["land"])."</td></tr>";
		$html.="<tr><td class=\"wtform_cell_left\">Aantal personen</td><td class=\"wtform_cell_right\">".htmlentities($form->input["aantalpersonen"])."</td></tr>";
		if($accinfo["wzt"]==2) {
			if($accinfo["flexibel"]) {
				$html.="<tr><td class=\"wtform_cell_left\">Aankomstdatum</td><td class=\"wtform_cell_right\">".DATUM("DAG D MAAND JJJJ",$form->input["aankomstdatum_flex"]["unixtime"])."</td></tr>";
			} else {
				$html.="<tr><td class=\"wtform_cell_left\">Aankomstdatum</td><td class=\"wtform_cell_right\">".DATUM("DAG D MAAND JJJJ",$form->input["aankomstdatum"])."</td></tr>";
			}
			$html.="<tr><td class=\"wtform_cell_left\">Verblijfsduur</td><td class=\"wtform_cell_right\">".htmlentities($vars["verblijfsduur"][$form->input["verblijfsduur"]])."</td></tr>";
		} else {
			$html.="<tr><td class=\"wtform_cell_left\">Aankomstdatum</td><td class=\"wtform_cell_right\">".htmlentities($accinfo["aankomstdatum"][$form->input["aankomstdatum"]])."</td></tr>";
		}
		$html.=$inclusief_optie_html;

		$html.="<tr><td class=\"wtform_cell_left\">Voornaam</td><td class=\"wtform_cell_right\">".htmlentities($form->input["voornaam"])."</td></tr>";
		$html.="<tr><td class=\"wtform_cell_left\">Tussenvoegsel</td><td class=\"wtform_cell_right\">".htmlentities($form->input["tussenvoegsel"])."</td></tr>";
		$html.="<tr><td class=\"wtform_cell_left\">Achternaam</td><td class=\"wtform_cell_right\">".htmlentities($form->input["achternaam"])."</td></tr>";
		if($form->input["adres"]) $html.="<tr><td class=\"wtform_cell_left\">Adres</td><td class=\"wtform_cell_right\">".htmlentities($form->input["adres"])."</td></tr>";
		if($form->input["postcode"]) $html.="<tr><td class=\"wtform_cell_left\">Postcode</td><td class=\"wtform_cell_right\">".htmlentities($form->input["postcode"])."</td></tr>";
		$html.="<tr><td class=\"wtform_cell_left\">Plaats</td><td class=\"wtform_cell_right\">".htmlentities($form->input["plaats"])."</td></tr>";
		if($form->input["land"]) $html.="<tr><td class=\"wtform_cell_left\">Land</td><td class=\"wtform_cell_right\">".htmlentities($form->input["land"])."</td></tr>";
		if($form->input["telefoonnummer"]) $html.="<tr><td class=\"wtform_cell_left\">Telefoonnummer</td><td class=\"wtform_cell_right\">".htmlentities($form->input["telefoonnummer"])."</td></tr>";
		if($form->input["mobielwerk"]) $html.="<tr><td class=\"wtform_cell_left\">Mobiel of werktelefoonnummer</td><td class=\"wtform_cell_right\">".htmlentities($form->input["mobielwerk"])."</td></tr>";
		if($form->input["email"]) $html.="<tr><td class=\"wtform_cell_left\">E-mailadres</td><td class=\"wtform_cell_right\"><a href=\"mailto:".$form->input["email"]."\">".htmlentities($form->input["email"])."</a></td></tr>";
		if($vars["rebook"]) {
			$html.="<tr><td class=\"wtform_cell_left\">Optie van ".$accinfo["optiedagen_klanten_vorig_seizoen"]." dagen</td><td class=\"wtform_cell_right\">ja</td></tr>";
		} else {
			if($form->input["optie"] or $_GET["o"]) $html.="<tr><td class=\"wtform_cell_left\">Optie van 3 dagen</td><td class=\"wtform_cell_right\">ja</td></tr>";
		}
		if($form->input["wensenbezet"]) $html.="<tr><td class=\"wtform_cell_left\">Wensen indien bezet</td><td class=\"wtform_cell_right\">".nl2br(htmlentities($form->input["wensenbezet"]))."</td></tr>";
		if($form->input["vraag"]) $html.="<tr><td class=\"wtform_cell_left\">Vragen en opmerkingen</td><td class=\"wtform_cell_right\">".nl2br(htmlentities($form->input["vraag"]))."</td></tr>";

		$referer=getreferer($_COOKIE["sch"]);
		if($referer["opsomming"]) $html.="<tr><td class=\"wtform_cell_left\">Referentielink</td><td class=\"wtform_cell_right\">".$referer["opsomming"]."</td></tr>";


		$html.="</table>";
		$html.="</div></body></html>";

		$mail->html=$html;
		$mail->send();

		unset($html);

		# Mail aan klant
		$mail=new wt_mail;
		$mail->subject=txt(($_GET["o"]||$form->input["optie"] ? "optieaanvraag" : "beschikbaarheidaanvraag"),"beschikbaarheid")." ".$vars["websitenaam"];
		$mail->from=$vars["email"];
		$mail->fromname=$vars["websitenaam"];
	#	$mail->returnpath=$vars["email"];

		$naam=wt_naam($form->input["voornaam"],$form->input["tussenvoegsel"],$form->input["achternaam"]);

		$html="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\"/><style type=\"text/css\"><!--\na:visited:hover,a:hover {\ncolor:".$hover.";\n}\n--></style>\n</head>\n<body style=\"background-color: #F3F3F3;font-family: ".$font.";font-size: 0.8em;\">\n";
		$html.="<div style=\"width:660px\">".html("beste","beschikbaarheid")." ".htmlentities($naam).",<P>".html("ingoedeordeontvangen".($_GET["o"] ? "_optie" : ""),"beschikbaarheid")."<br><br>";
		$html.="<table style=\"background-color: #FFFFFF;width: 660px;font-family: ".$font.";font-size: 1.0em;border:solid ".$table." 1px;\" cellspacing=\"0\" cellpadding=\"3\">";
		$html.="<tr><td style=\"font-weight: bold;width:145px;border:solid ".$table." 1px\">".html("accommodatie","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\"><a href=\"".$accinfo["url"]."\">".ucfirst($accinfo["soortaccommodatie"])." ".htmlentities($accinfo["naam"])."</a></td></tr>";
		$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("plaats","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\"><a href=\"".$accinfo["plaats_url"]."\">".htmlentities($accinfo["plaats"].", ".$accinfo["land"])."</a></td></tr>";
		$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("aantalpersonen","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($form->input["aantalpersonen"])."</td></tr>";
		if($accinfo["wzt"]==2) {
			if($accinfo["flexibel"]) {
				$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("aankomstdatum","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities(DATUM("DAG D MAAND JJJJ",$form->input["aankomstdatum_flex"]["unixtime"],$vars["taal"]))."</td></tr>";
			} else {
				$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("aankomstdatum","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities(DATUM("DAG D MAAND JJJJ",$form->input["aankomstdatum"],$vars["taal"]))."</td></tr>";
			}
			$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("verblijfsduur","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($vars["verblijfsduur"][$form->input["verblijfsduur"]])."</td></tr>";
		} else {
			$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("aankomstdatum","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($accinfo["aankomstdatum"][$form->input["aankomstdatum"]])."</td></tr>";
		}

		reset($form->input);
		while(list($key,$value)=each($form->input)) {
			if(ereg("^inclusief_optie_[0-9]+$",$key)) {
				if($value==1) $html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".htmlentities($form->fields["title"][$key])."</td><td style=\"border:solid ".$table." 1px\">".html("ja")."</td></tr>";
			}
		}

		if($form->input["optie"] or $_GET["o"]) $html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("optievan3dagen","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".html("ja")."</td></tr>";
		$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("naam","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($naam)."</td></tr>";
		if($form->input["adres"]) $html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("adres","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($form->input["adres"])."</td></tr>";
		if($form->input["postcode"]) $html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("postcode","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($form->input["postcode"])."</td></tr>";
		$html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("woonplaats","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($form->input["plaats"])."</td></tr>";
		if($form->input["land"]) $html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("land","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($form->input["land"])."</td></tr>";
		if($form->input["telefoonnummer"]) $html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("telefoon","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($form->input["telefoonnummer"])."</td></tr>";
		if($form->input["mobielwerk"]) $html.="<tr><td style=\"font-weight: bold;border:solid ".$table." 1px\">".html("mobielwerk_kort","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($form->input["mobielwerk"])."</td></tr>";
		if($form->input["wensenbezet"]) $html.="<tr><td valign=\"top\" style=\"font-weight: bold;border:solid ".$table." 1px\">".html("wensenindienbezet","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".nl2br(htmlentities($form->input["wensenbezet"]))."</td></tr>";
		if($form->input["vraag"]) $html.="<tr><td valign=\"top\" style=\"font-weight: bold;border:solid ".$table." 1px\">".html("vragenopmerkingen","beschikbaarheid")."</td><td style=\"border:solid ".$table." 1px\">".nl2br(htmlentities($form->input["vraag"]))."</td></tr>";
		$html.="</table>";

		$html.="<br><br>".html("vooreengrootdeel","beschikbaarheid")."<p>".html("uontvangtzospoedigmogelijk","beschikbaarheid")." ";
		if($form->input["optie"] or $_GET["o"]) $html.=html("hierinvermeldenwijook","beschikbaarheid");
		$html.="<P>".html("bezoekonzewebsite","beschikbaarheid",array("v_websitenaam"=>$vars["websitenaam"],"l1"=>"http_werkwijze"));
		$html.="<P>".html("metvriendelijkegroet","beschikbaarheid").",<BR>".html("medewerkerssitenaam","beschikbaarheid",array("v_websitenaam"=>$vars["websitenaam"]))."<P>".$vars["langewebsitenaam"]."<br>Wipmolenlaan 3<br>3447 GJ Woerden<br>";
		if($vars["websiteland"]<>"nl") $html.=html("nederland","contact")."<br>";
		$html.=html("telefoonnummer_chalet","contact")."<br>".html("fax_chalet","contact")."<br>".html("email","contact").": <A HREF=\"mailto:".$vars["email"]."\">".$vars["email"]."</A><P>";
		$html.="</div></body></html>";

		$mail->to=$form->input["email"];

		$mail->html=$html;
		$mail->send();
	}
}

$form->end_declaration();

include "content/opmaak.php";

?>