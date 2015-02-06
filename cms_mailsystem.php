<?php

$mustlogin=true;

include("admin/vars.php");

$form=new form2("frm");
$form->settings["fullname"]="mailsystem";
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"]["nl"]="MAIL VERSTUREN";


# Bepalen wat de in te vullen waardes zijn


if($_GET["t"] and $_GET["t"]<=4) {
	#
	# Optieaanvragen-systeem
	#

	# Gegevens klant
	$db->query("SELECT voornaam, tussenvoegsel, achternaam, email, aankomstdatum, aankomstdatum_exact, aantalpersonen, type_id, UNIX_TIMESTAMP(einddatum_klant) AS einddatum_klant, website FROM optieaanvraag WHERE optieaanvraag_id='".addslashes($_GET["oaid"])."';");
	if($db->next_record()) {
		if($db->f("einddatum_klant")) {
			$optiegeg["looptaf"]=date("d/m/Y",$db->f("einddatum_klant"));
		}
		$optiegeg["voornaam"]=$db->f("voornaam");
		$optiegeg["tussenvoegsel"]=$db->f("tussenvoegsel");
		$optiegeg["achternaam"]=$db->f("achternaam");
		$optiegeg["aankomstdatum"]=$db->f("aankomstdatum");
		$optiegeg["aankomstdatum_exact"]=$db->f("aankomstdatum_exact");
		$optiegeg["aantalpersonen"]=$db->f("aantalpersonen");
		$optiegeg["website"]=$db->f("website");
		$optiegeg["accinfo"]=accinfo($db->f("type_id"),$db->f("aankomstdatum"),$db->f("aantalpersonen"));
		$optiegeg["aantalnachten"]=round(($optiegeg["accinfo"]["vertrekdatum"]-$db->f("aankomstdatum_exact"))/86400);
		$optiegeg["seizoenid"]=$optiegeg["accinfo"]["seizoenid"];
		$optiegeg["email"]=$db->f("email");

		$klanttaal=$vars["websiteinfo"]["taal"][$db->f("website")];
	}

	# Gegevens leverancier
	$db->query("SELECT leverancier_id, naam, contactpersoon_reserveringen AS contactpersoon, faxnummer_reserveringen AS faxnummer, email_reserveringen AS email, adresregels, bestelfax_logo, bestelmailfax_taal FROM leverancier WHERE leverancier_id='".addslashes($_GET["lid"])."';");
	if($db->next_record()) {
		$optiegeg["leverancier"]=$db->f("leverancier_id");
		$optiegeg["leverancier_naam"]=$db->f("naam");
		$optiegeg["leverancier_contactpersoon"]=$db->f("contactpersoon");
		$optiegeg["leverancier_faxnummer"]=$db->f("faxnummer");
		$optiegeg["leverancier_email"]=$db->f("email");
		$optiegeg["leverancier_adresregels"]=$db->f("adresregels");
		$optiegeg["leverancier_bestelfax_logo"]=$db->f("bestelfax_logo");
		$bmftaal=$db->f("bestelmailfax_taal");
	}

	if($_GET["t"]==1) {
		# Optie doorgeven aan leverancier
		$inmail["aan"]=$optiegeg["leverancier_email"];
		$inmail["subject"]=$vars["optiemail_leverancier_doorgeven_subject"][$bmftaal];
		$inmail["body"]=$vars["optiemail_leverancier_doorgeven_body"][$bmftaal];
		$aan_leverancier=true;
	} elseif($_GET["t"]==2) {
		# Leverancier heeft optie goedgekeurd (klant mailen)
		$inmail["aan"]=$optiegeg["email"];
		$inmail["subject"]=$txt[$klanttaal]["optieaanvraag"]["mail_goed_subject"];
		$inmail["body"]=$txt[$klanttaal]["optieaanvraag"]["mail_goed_body"];
		$aan_klant=true;
	} elseif($_GET["t"]==3) {
		# Leverancier heeft optie afgekeurd (klant mailen)
		$inmail["aan"]=$optiegeg["email"];
		$inmail["subject"]=$txt[$klanttaal]["optieaanvraag"]["mail_af_subject"];
		$inmail["body"]=$txt[$klanttaal]["optieaanvraag"]["mail_af_body"];
		$aan_klant=true;
	} elseif($_GET["t"]==4) {
		# Klant ziet van optie af (leverancier mailen)
		$inmail["aan"]=$optiegeg["leverancier_email"];
		$inmail["subject"]=$vars["optiemail_leverancier_niet_subject"][$bmftaal];
		$inmail["body"]=$vars["optiemail_leverancier_niet_body"][$bmftaal];
		$aan_leverancier=true;
	}
	if($optiegeg["voornaam"]) {
		$inmail["body"]=ereg_replace("\[VOORNAAMKLANT\]",$optiegeg["voornaam"],$inmail["body"]);
	}

	$inmail["body"]=ereg_replace("\[ACHTERNAAMKLANT\]",wt_naam("", $optiegeg["tussenvoegsel"], $optiegeg["achternaam"]),$inmail["body"]);

	$inmail["body"]=ereg_replace("\[NAAMKLANT\]",wt_naam($optiegeg["voornaam"],$optiegeg["tussenvoegsel"],$optiegeg["achternaam"]),$inmail["body"]);
#	$inmail["body"]=ereg_replace("\[NAAM_MEDEWERKER\]",wt_naam($login->vars["voornaam"],$login->vars["tussenvoegsel"],$login->vars["achternaam"]),$inmail["body"]);
	$inmail["body"]=ereg_replace("\[NAAM_MEDEWERKER\]",$login->vars["voornaam"],$inmail["body"]);
	$inmail["body"]=ereg_replace("\[EMAIL_MEDEWERKER\]",$login->vars["email"],$inmail["body"]);
	$inmail["body"]=ereg_replace("\[WEBSITE\]",$vars["websiteinfo"]["websitenaam"][$optiegeg["website"]],$inmail["body"]);
	$inmail["body"]=ereg_replace("\[WEBSITE_URL\]",$vars["websiteinfo"]["basehref"][$optiegeg["website"]],$inmail["body"]);
	$inmail["body"]=ereg_replace("\[ACCOMMODATIE_URL\]",$vars["websiteinfo"]["basehref"][$optiegeg["website"]].$optiegeg["accinfo"]["url_zonderpad"],$inmail["body"]);
	if($optiegeg["looptaf"]) {
		$inmail["body"]=ereg_replace("\[LOOPT_AF\]",$optiegeg["looptaf"],$inmail["body"]);
	}
	$inmail["body"]=ereg_replace("\[AANTAL_NACHTEN\]",strval($optiegeg["aantalnachten"]),$inmail["body"]);
	$inmail["body"]=ereg_replace("\[CONTACTPERSOON_LEVERANCIER\]",$optiegeg["leverancier_contactpersoon"],$inmail["body"]);
	$inmail["body"]=ereg_replace("\[AANKOMSTDATUM\]",date("d/m/Y",$optiegeg["aankomstdatum_exact"]),$inmail["body"]);
	$inmail["body"]=ereg_replace("\[ACCOMMODATIE\]",$optiegeg["accinfo"]["plaats"]." - ".ucfirst($optiegeg["accinfo"]["soortaccommodatie"])." ".$optiegeg["accinfo"]["naam_ap"],$inmail["body"]);
	$inmail["body"]=ereg_replace("\[AANTALPERSONEN\]",$optiegeg["aantalpersonen"],$inmail["body"]);

	if(ereg("\[BEDRAG\]",$inmail["body"])) {
		# Bedrag bepalen
		$bedrag=$optiegeg["accinfo"]["tarief"]." ";
		$vars["taal"]=$klanttaal;
		if($optiegeg["accinfo"]["toonper"]==3) {
			$bedrag.=txt("peraccommodatie","tarieventabel");
		} else {
			$bedrag.=txt("perpersooninclskipas","tarieventabel");
		}

		$inmail["body"]=ereg_replace("\[BEDRAG\]",$bedrag,$inmail["body"]);
	}



	# Onderwerp
	$inmail["subject"]=ereg_replace("\[ACCOMMODATIE\]",$optiegeg["accinfo"]["plaats"]." - ".ucfirst($optiegeg["accinfo"]["soortaccommodatie"])." ".$optiegeg["accinfo"]["naam_ap"],$inmail["subject"]);
	$inmail["subject"]=ereg_replace("\[AANKOMSTDATUM\]",date("d/m/Y",$optiegeg["aankomstdatum_exact"]),$inmail["subject"]);



	if($aan_klant) {

		$inmail["van"]=$vars["websiteinfo"]["email"][$optiegeg["website"]];
		$inmail["vannaam"]=$vars["websiteinfo"]["websitenaam"][$optiegeg["website"]];

		$optiegegevens=$optiegeg["accinfo"]["plaats"]." - ".$optiegeg["accinfo"]["naam_ap"];
		$optiegegevens.="\n".datum("DAG D MAAND JJJJ",$optiegeg["aankomstdatum_exact"],$klanttaal);
	} else {

		$inmail["van"]=$login->vars["email"];
		$inmail["vannaam"]=$vars["websiteinfo"]["websitenaam"]["C"];

		$optiegegevens=$optiegeg["accinfo"]["plaats"]." - ".$optiegeg["accinfo"]["naam_ap"];
		$optiegegevens.=" - ".date("d/m/Y",$optiegeg["aankomstdatum_exact"]);
	}
	$inmail["vannaammail"]=$inmail["vannaam"]." (".$inmail["van"].")";
	$inmail["body"]=ereg_replace("\[OPTIEGEGEVENS\]",$optiegegevens,$inmail["body"]);
}

$form->field_htmlcol("","Van",array("text"=>$inmail["vannaammail"]));
$form->field_text(1,"aan","Aan","",array("text"=>$inmail["aan"]));
$form->field_text(1,"subject","Onderwerp","",array("text"=>$inmail["subject"]));

$form->field_textarea(1,"body","Te mailen tekst","",array("text"=>$inmail["body"]),"",array("newline"=>true,"title_html"=>true,"style"=>"width:660px;","rows"=>20,"cols"=>80));

$form->check_input();

if($form->okay) {
	if($inmail["van"]) {
		# Mail sturen
		$mail=new wt_mail;
		$mail->fromname=$inmail["vannaam"];
		$mail->from=$inmail["van"];
		$mail->to=$inmail["aan"];
		$mail->subject=$inmail["subject"];
		$mail->bcc=$login->vars["email"];

		$mail->plaintext=$inmail["body"];

		$mail->send();

		# Vedere gevolgen verwerken
		if($_GET["t"]==1) {
			# Status veranderen in "aangevraagd leverancier"
		 	$db->query("UPDATE optieaanvraag SET status='2', aanvraagdatum_leverancier=NOW() WHERE optieaanvraag_id='".addslashes($_GET["oaid"])."';");
		} elseif($_GET["t"]==2) {
			# Status veranderen in "uitstaand"
		 	$db->query("UPDATE optieaanvraag SET status='3' WHERE optieaanvraag_id='".addslashes($_GET["oaid"])."';");
		} elseif($_GET["t"]==3) {
			# Status veranderen in "afgewezen"
		 	$db->query("UPDATE optieaanvraag SET status='4' WHERE optieaanvraag_id='".addslashes($_GET["oaid"])."';");
		} elseif($_GET["t"]==4) {
			# Status veranderen in "vervallen"
		 	$db->query("UPDATE optieaanvraag SET status='5' WHERE optieaanvraag_id='".addslashes($_GET["oaid"])."';");
		}

		if($_GET["burl"]) {
			header("Location: ".$_GET["burl"]."&sentmail=".urlencode($mail->to));
			exit;
		}
	}
}
$form->end_declaration();

$layout->display_all($cms->page_title);

?>