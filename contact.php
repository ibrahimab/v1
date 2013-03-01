<?php

include_once "admin/vars.php";

$vars["canonical"]=$vars["path"]."contact.php";

# Session starten voor TradeTracker
session_start();

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Contactformulier ".$vars["websites"][$vars["website"]];
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"][$vars["taal"]]=strtoupper(txt("verzenden","contact"));
$form->settings["language"]=$vars["taal"];

#_field: (obl),id,title,db,prevalue,options,layout

if($_GET["accid"]) {
	# Pagina bezocht vanaf een accommodatiepagina
	$db->query("SELECT l.begincode, a.soortaccommodatie, a.naam, t.naam".$vars["ttv"]." AS tnaam, t.optimaalaantalpersonen , t.maxaantalpersonen, p.naam AS plaats, s.naam AS skigebied FROM accommodatie a, plaats p, skigebied s, type t, land l WHERE p.land_id=l.land_id AND t.accommodatie_id=a.accommodatie_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.type_id='".addslashes($_GET["accid"])."';");
	if($db->next_record()) {
		$form->field_onlyinoutput("accommodatie",txt("accommodatie","contact"),"",array("text"=>$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." pers.)"));
		$form->field_onlyinoutput("plaats",txt("plaats","contact"),"",array("text"=>$db->f("plaats").", ".$db->f("skigebied")));
		$accomschrijving=$db->f("plaats")." / ".ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".txt("max")." ".$db->f("maxaantalpersonen")." ".txt("personen").")%0D%0AVoor omschrijving: ".$vars["basehref"]."accommodatie/".$db->f("begincode").$_GET["accid"]."/%0D%0A%0D%0A";
	}
}

$temp_naw=getnaw();

if($vars["websiteland"]=="nl") {
	$landinvullen="Nederland";
} elseif($vars["websiteland"]=="be") {
	$landinvullen="België";
}

if($vars["website"]=="T") {
	$form->field_text(0,"naamreisbureau",txt("naamreisbureau_chalettour","contact"));
}
$form->field_text(1,"voornaam",txt("voornaam","contact"),"",array("text"=>$temp_naw["voornaam"]));
$form->field_text(0,"tussenvoegsel",txt("tussenvoegsel","contact"),"",array("text"=>$temp_naw["tussenvoegsel"]));
$form->field_text(1,"achternaam",txt("achternaam","contact"),"",array("text"=>$temp_naw["achternaam"]));
$form->field_text(1,"adres",txt("adres","contact"),"",array("text"=>$temp_naw["adres"]));
$form->field_text(1,"postcode",txt("postcode","contact"),"",array("text"=>$temp_naw["postcode"]));
$form->field_text(1,"woonplaats",txt("woonplaats","contact"),"",array("text"=>$temp_naw["plaats"]));
$form->field_text(1,"land",txt("land","contact"),"",array("text"=>($temp_naw["land"] ? $temp_naw["land"] : $landinvullen)));
$form->field_text(1,"telefoonnummer",txt("telefoonnummer","contact"),"",array("text"=>$temp_naw["telefoonnummer"]));
$form->field_text(0,"mobielwerk",txt("mobielwerk","contact"),"",array("text"=>$temp_naw["mobielwerk"]));
$form->field_email(1,"email",txt("email","contact"),"",array("text"=>$temp_naw["email"]));
$form->field_textarea(0,"opmerkingen",txt("opmerkingen","contact"));
$form->field_yesno("teruggebeld",txt("teruggebeld","contact"));

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

$form->check_input();

if($form->filled) {
	# invoer filteren op spam
	if(strpos(" ".$form->input["voornaam"],"http://")) $form->error("voornaam",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["tussenvoegsel"],"http://")) $form->error("tussenvoegsel",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["achternaam"],"http://")) $form->error("achternaam",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["adres"],"http://")) $form->error("adres",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["postcode"],"http://")) $form->error("postcode",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["woonplaats"],"http://")) $form->error("woonplaats",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["land"],"http://")) $form->error("land",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["telefoonnummer"],"http://")) $form->error("telefoonnummer",txt("linkniettoegestaan","contact"));
	if(strpos(" ".$form->input["mobielwerk"],"http://")) $form->error("mobielwerk",txt("linkniettoegestaan","contact"));

	# opmerkingenveld filteren op <a href, [url=, [link=
	$filter_array=array("<a href=","[url=","[link=");
	while(list($key,$value)=each($filter_array)) {
		$pos=strpos(" ".$form->input["opmerkingen"],$value);
		if($pos) {
			$form->error("opmerkingen",txt("linkniettoegestaan","contact"));
			break;
		}
	}
}


if(eregi("^belgie$",$form->input["land"])) $form->input["land"]="België";

if($form->okay) {
	# "Reageren op"-link bovenaan mail aan Chalet plaatsen
	$body=vtanaam(ucfirst($form->input["voornaam"]),$form->input["tussenvoegsel"],ucfirst($form->input["achternaam"]))."%0D%0A".$form->input["adres"]."%0D%0A".$form->input["postcode"]." ".ucfirst($form->input["woonplaats"])."%0D%0A".($form->input["land"]<>"Nederland" ? ucfirst($form->input["land"])."%0D%0A" : "").$form->input["telefoonnummer"]."%0D%0A".$form->input["mobielwerk"]."%0D%0A%0D%0A".$accomschrijving.ereg_replace("\n","%0D%0A",$form->input["opmerkingen"]);
	$subject=ereg_replace("&","%26",txt("reactieopuwvraag","contact"));

	$body=ereg_replace("&","%26",$body);
	$body=ereg_replace("\"","%22",$body);
	$body=ereg_replace("'","%27",$body);
	$mailtop="Reageren op deze mail: <a href=\"mailto:".$form->input["email"].ereg_replace(" ","%20","?subject=".$subject."&body=".$body)."\">mail sturen</A>";
	$referer=getreferer($_COOKIE["sch"]);
	if($referer["opsomming"]) $form->outputtable_tr="<tr><td colspan=\"2\"><table><tr><td><b>Referentielink</b><br>".$referer["opsomming"]."</td></tr></table></td></tr>";
	if(ereg("@webtastic\.nl",$form->input["email"])) {
		$to="chalet_test@webtastic.nl";
	} else {
		$to="info@chalet.nl";
	}

	$form->mail($to,"","Ingevuld contactformulier","",$mailtop,$mailbottom,"info@chalet.nl","Website ".$vars["websites"][$vars["website"]]);
	nawcookie($form->input["voornaam"],$form->input["tussenvoegsel"],$form->input["achternaam"],$form->input["adres"],$form->input["postcode"],$form->input["woonplaats"],$form->input["land"],$form->input["telefoonnummer"],$form->input["mobielwerk"],$form->input["email"],"not",$form->input["nieuwsbrief"]);

	# Inschrijven nieuwsbrief
	if($form->input["nieuwsbrief"] and $form->input["nieuwsbrief"]<>"3") {
		$nieuwsbrief_waardes=array("email"=>$form->input["email"],"voornaam"=>$form->input["voornaam"],"tussenvoegsel"=>$form->input["tussenvoegsel"],"achternaam"=>$form->input["achternaam"],"per_wanneer"=>$form->input["nieuwsbrief"]);
		nieuwsbrief_inschrijven($vars["seizoentype"],$nieuwsbrief_waardes);
	}
}
$form->end_declaration();

include "content/opmaak.php";

?>