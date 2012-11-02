<?php

set_time_limit(0);

$mustlogin=true;
$boeking_bepaalt_taal=true;

include("admin/vars.php");

$temp_gegevens=boekinginfo($_GET["bid"]);
$gegevens["stap1"]=$temp_gegevens["stap1"];
if($gegevens["stap1"]["boekingid"]) {
	$accinfo=accinfo($gegevens["stap1"]["typeid"],$gegevens["stap1"]["aankomstdatum"],$gegevens["stap1"]["aantalpersonen"]);

	if($accinfo["verzameltype"] and $gegevens["stap1"]["verzameltype_gekozentype_id"]) {
		# Verzameltype: andere gegevens laden
		$db->query("SELECT code, soortaccommodatie, naam, tnaam, optimaalaantalpersonen, maxaantalpersonen, receptie".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS receptie, telefoonnummer FROM view_accommodatie WHERE type_id='".addslashes($gegevens["stap1"]["verzameltype_gekozentype_id"])."';");
		if($db->next_record()) {
			$accinfo["code"]=$db->f("code");
			$accinfo["soortaccommodatie"]=$vars["soortaccommodatie"][$db->f("soortaccommodatie")];
			$accinfo["naam_ap"]=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? " - ".$db->f("maxaantalpersonen") : "")." ".txt("pers").")";
			if($db->f("receptie")) $accinfo["receptie"]=$db->f("receptie");
			if($db->f("telefoonnummer")) $accinfo["telefoonnummer"]=$db->f("telefoonnummer");
		}
	}

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

function voucher_form($voucher,$voucherteller) {
	global $form,$gegevens,$accinfo,$skipas_dubbele_voucher;

	$form->field_htmlrow("","<hr><b>Voucher ".htmlentities($voucher["soort_voucher"]));
	$form->field_text(1,"naam_voucher".$voucherteller,"Naam","",array("text"=>$voucher["naam_voucher"]));
	if($voucher["begindag"]) {
		$voucher["aanvangsdatum"]=mktime(0,0,0,date("m",$gegevens["stap1"]["aankomstdatum_exact"]),date("d",$gegevens["stap1"]["aankomstdatum_exact"])+$voucher["begindag"],date("Y",$gegevens["stap1"]["aankomstdatum_exact"]));
	} else {
		$voucher["aanvangsdatum"]=$gegevens["stap1"]["aankomstdatum_exact"];
	}
	if($voucher["einddag"]) {
		$voucher["einddatum"]=mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])+$voucher["einddag"],date("Y",$gegevens["stap1"]["vertrekdatum_exact"]));
	} else {
		$voucher["einddatum"]=mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"]),date("Y",$gegevens["stap1"]["vertrekdatum_exact"]));
	}
	$form->field_text(0,"aanvangsdatum".$voucherteller,"Aanvangsdatum","",array("text"=>date("d-m-Y",$voucher["aanvangsdatum"]).($voucher["tekstbegin_voucher"] ? " ".$voucher["tekstbegin_voucher"] : "")));
	$form->field_text(0,"einddatum".$voucherteller,"Einddatum","",array("text"=>($voucher["tooneinddatum_voucher"] ? date("d-m-Y",$voucher["einddatum"]).($voucher["teksteind_voucher"] ? " ".$voucher["teksteind_voucher"] : "") : "")));
	if($voucherteller==1 or $voucherteller==2) {
		# Accommodatievoucher
		$form->field_integer(1,"aantalpersonen".$voucherteller,"Aantal deelnemers","",array("text"=>$gegevens["stap1"]["aantalpersonen"]));
		$form->field_textarea(0,"opmerkingen_voucher".$voucherteller,txt("extra","voucher"),"",array("text"=>$gegevens["stap1"]["opmerkingen_voucher"]));
		$form->field_textarea(0,"voucherinfo".$voucherteller,txt("infoaccommodatie","voucher"),"",array("text"=>$accinfo["avoucherinfo"].($accinfo["avoucherinfo"] && $accinfo["tvoucherinfo"] ? "\n" : "").$accinfo["tvoucherinfo"]));
	} else {
		# Alle andere vouchers
		$form->field_text(0,"omschrijving_voucher".$voucherteller,"Omschrijving","",array("text"=>$voucher["omschrijving_voucher"]));
		for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
			if($gegevens["stap3"][$i]["voornaam"] and $gegevens["stap3"][$i]["achternaam"]) {
				$voucher_unsort[$i]=wt_naam($gegevens["stap3"][$i]["voornaam"],$gegevens["stap3"][$i]["tussenvoegsel"],$gegevens["stap3"][$i]["achternaam"]);
				$voucher_sort[$gegevens["stap3"][$i]["achternaam"].$gegevens["stap3"][$i]["voornaam"].$gegevens["stap3"][$i]["tussenvoegsel"].$i]=$i;
			} else {
				$voucher_unsort[$i]="-- persoon ".$i." --";
				$voucher_sort["zzzzzzzzzzzzzz".$i]=$i;
			}
		}
		ksort($voucher_sort);
		while(list($key,$value)=each($voucher_sort)) {
			if($skipas_dubbele_voucher[$value] and $voucher["skipas"]) {
				$voucher["alledeelnemers"][$value]=htmlentities($voucher_unsort[$value])." <b style=\"color:red\">(meervoudige skipas-afwijking)</b>";
			} else {
				$voucher["alledeelnemers"][$value]=htmlentities($voucher_unsort[$value]);
			}
		}
		$form->field_checkbox(0,"deelnemers".$voucherteller,"Personnes","",array("selection"=>$voucher["deelnemers"]),array("selection"=>$voucher["alledeelnemers"]),array("content_html"=>true,"one_per_line"=>true));
		$form->field_textarea(0,"aanvullend_voucher".$voucherteller,txt("letop","voucher"),"",array("text"=>$voucher["aanvullend_voucher"]));
	}
}


#$gegevens["stap1"]["website_specifiek"]["ttv"]

# Accommodatie
$voucherteller=1;
$voucher[$voucherteller]["soort_voucher"]="Accommodatie ".$accinfo["begincode"].$accinfo["type_id"]." ".$accinfo["accommodatie"]." ".$accinfo["cms_typenaam"].($accinfo["bestelnaam"] ? " (".$accinfo["bestelnaam"].")" : "");
$voucher[$voucherteller]["naam_voucher"]="Voucher ".txt("accommodatie","voucher");
#$voucher[$voucherteller]["aanvullend_voucher"]=$db->f("aanvullend_voucher");
#$voucher[$voucherteller]["tekstbegin_voucher"]=$db->f("tekstbegin_voucher");
#$voucher[$voucherteller]["teksteind_voucher"]=$db->f("teksteind_voucher");
$voucher[$voucherteller]["begindag"]=0;
$voucher[$voucherteller]["einddag"]=0;
$voucher[$voucherteller]["tooneinddatum_voucher"]=1;
$voucher[$voucherteller]["logo"]="pic/cms/voucherlogo_accommodatie/".$accinfo["leverancier_id"].".jpg";

# Informatie voor thuisblijvers
$voucherteller++;
$voucher[$voucherteller]["soort_voucher"]=txt("informatievoorthuisblijvers","voucher");
$voucher[$voucherteller]["naam_voucher"]=txt("informatievoorthuisblijvers","voucher");

# Kijken welke deelnemers een afwijkende skipas hebben
# Gewone opties
$db->query("SELECT bo.persoonnummer FROM boeking_optie bo, optie_groep og, optie_onderdeel oo WHERE (oo.wederverkoop_skipas_id=0 OR oo.wederverkoop_skipas_id IS NULL) AND og.skipas_id>0 AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
while($db->next_record()) {
	$afwijkende_skipas[$db->f("persoonnummer")]=true;
}
# Handmatige opties
$db->query("SELECT deelnemers FROM extra_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND skipas_id>0;");
while($db->next_record()) {
	$tempdeelnemers=@split(",",$db->f("deelnemers"));
	while(list($key,$value)=@each($tempdeelnemers)) {
		$afwijkende_skipas[$value]=true;
	}
}


# Skipas
$db->query("SELECT s.skipas_id, s.naam, s.naam_voucher".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam_voucher, s.omschrijving_voucher, s.aanvullend_voucher".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS aanvullend_voucher, s.tekstbegin_voucher, s.teksteind_voucher, s.begindag, s.einddag FROM skipas s WHERE s.skipas_id='".addslashes($accinfo["skipasid"])."';");
if($db->next_record()) {
	$voucherteller++;
	$voucher[$voucherteller]["soort_voucher"]="Skipas ".$db->f("naam");
	$voucher[$voucherteller]["naam_voucher"]=$db->f("naam_voucher");
	$voucher[$voucherteller]["omschrijving_voucher"]=$db->f("omschrijving_voucher");
	$voucher[$voucherteller]["aanvullend_voucher"]=$db->f("aanvullend_voucher");
	$voucher[$voucherteller]["tekstbegin_voucher"]=$db->f("tekstbegin_voucher");
	$voucher[$voucherteller]["teksteind_voucher"]=$db->f("teksteind_voucher");
	$voucher[$voucherteller]["tooneinddatum_voucher"]=1;
	$voucher[$voucherteller]["begindag"]=$db->f("begindag");
	$voucher[$voucherteller]["einddag"]=$db->f("einddag");
	$voucher[$voucherteller]["logo"]="pic/cms/voucherlogo_skipas/".$db->f("skipas_id").".jpg";

	# Deelnemers bepalen
	for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
		if(!$afwijkende_skipas[$i]) {
			if($voucher[$voucherteller]["deelnemers"]) $voucher[$voucherteller]["deelnemers"].=",".$i; else $voucher[$voucherteller]["deelnemers"]=$i;
		}
	}
}

# Opties
$db->query("SELECT DISTINCT os.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS osnaam, oo.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS oonaam, oo.wederverkoop_skipas_id, og.naam AS ognaam, og.optie_groep_id, og.naam_voucher".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam_voucher, og.aanvullend_voucher".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS aanvullend_voucher, og.skipas_id, og.tooneinddatum_voucher, oo.omschrijving_voucher, oo.tekstbegin_voucher, oo.teksteind_voucher, oo.optie_onderdeel_id, oo.geboortedatum_voucher, oo.begindag, oo.einddag FROM optie_groep og, optie_onderdeel oo, optie_soort os, boeking_optie bo WHERE bo.optie_onderdeel_id=oo.optie_onderdeel_id AND bo.status=1 AND oo.optie_groep_id=og.optie_groep_id AND og.optie_soort_id=os.optie_soort_id AND os.voucher=1 AND oo.voucher=1 AND bo.boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' ORDER BY os.volgorde, oo.volgorde;");
while($db->next_record()) {
	$voucherteller++;
	$voucher[$voucherteller]["soort_voucher"]=$db->f("osnaam").": ".$db->f("oonaam")." - ".$db->f("ognaam");
	$voucher[$voucherteller]["naam_voucher"]=$db->f("naam_voucher");
	$voucher[$voucherteller]["omschrijving_voucher"]=$db->f("omschrijving_voucher");
	$voucher[$voucherteller]["aanvullend_voucher"]=$db->f("aanvullend_voucher");
	$voucher[$voucherteller]["tekstbegin_voucher"]=$db->f("tekstbegin_voucher");
	$voucher[$voucherteller]["teksteind_voucher"]=$db->f("teksteind_voucher");
	$voucher[$voucherteller]["geboortedatum_voucher"]=$db->f("geboortedatum_voucher");
	$voucher[$voucherteller]["tooneinddatum_voucher"]=$db->f("tooneinddatum_voucher");
	$voucher[$voucherteller]["begindag"]=$db->f("begindag");
	$voucher[$voucherteller]["einddag"]=$db->f("einddag");
	$voucher[$voucherteller]["logo"]="pic/cms/voucherlogo_optie/".$db->f("optie_groep_id").".jpg";
	if($db->f("skipas_id")) {
		if(!file_exists($voucher[$voucherteller]["logo"])) {
			$voucher[$voucherteller]["logo"]="pic/cms/voucherlogo_skipas/".$db->f("skipas_id").".jpg";
		}
		$db2->query("SELECT aanvullend_voucher".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS aanvullend_voucher FROM skipas WHERE skipas_id='".addslashes($db->f("skipas_id"))."';");
		if($db2->next_record()) {
			if($db2->f("aanvullend_voucher")) {
				if($voucher[$voucherteller]["aanvullend_voucher"]) $voucher[$voucherteller]["aanvullend_voucher"].="\n";
				$voucher[$voucherteller]["aanvullend_voucher"].=$db2->f("aanvullend_voucher");
			}
		}
	}

	# Deelnemers bepalen
	$db2->query("SELECT persoonnummer FROM boeking_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND optie_onderdeel_id='".$db->f("optie_onderdeel_id")."';");
	while($db2->next_record()) {
		if(!$db->f("wederverkoop_skipas_id") or !$afwijkende_skipas[$db2->f("persoonnummer")]) {
			if($voucher[$voucherteller]["deelnemers"]) $voucher[$voucherteller]["deelnemers"].=",".$db2->f("persoonnummer"); else $voucher[$voucherteller]["deelnemers"]=$db2->f("persoonnummer");
			if($db->f("skipas_id")) {
				$voucher[$voucherteller]["skipas"]=true;
				# Kijken of er dubbele skipas-vouchers zijn
				if($skipas_voucher[$db2->f("persoonnummer")]) {
					$skipas_dubbele_voucher[$db2->f("persoonnummer")]=true;
				} else {
					$skipas_voucher[$db2->f("persoonnummer")]=true;
				}
			}
		}
	}
}

# Handmatige opties
$db->query("SELECT persoonnummer, deelnemers, soort, naam, naam_voucher, omschrijving_voucher, aanvullend_voucher, tekstbegin_voucher, begindag, teksteind_voucher, geboortedatum_voucher, einddag, skipas_id FROM extra_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND voucher=1;");
while($db->next_record()) {
	$voucherteller++;
	$voucher[$voucherteller]["soort_voucher"]=$db->f("soort").": ".$db->f("naam");
	$voucher[$voucherteller]["naam_voucher"]=$db->f("naam_voucher");
	$voucher[$voucherteller]["omschrijving_voucher"]=$db->f("omschrijving_voucher");
	$voucher[$voucherteller]["aanvullend_voucher"]=$db->f("aanvullend_voucher");
	$voucher[$voucherteller]["tekstbegin_voucher"]=$db->f("tekstbegin_voucher");
	$voucher[$voucherteller]["teksteind_voucher"]=$db->f("teksteind_voucher");
	$voucher[$voucherteller]["geboortedatum_voucher"]=$db->f("geboortedatum_voucher");
	$voucher[$voucherteller]["tooneinddatum_voucher"]=1;
	$voucher[$voucherteller]["begindag"]=$db->f("begindag");
	$voucher[$voucherteller]["einddag"]=$db->f("einddag");
	if($db->f("skipas_id")) {
		$voucher[$voucherteller]["skipas"]=true;
		if(!file_exists($voucher[$voucherteller]["logo"])) {
			$voucher[$voucherteller]["logo"]="pic/cms/voucherlogo_skipas/".$db->f("skipas_id").".jpg";
		}
		$db2->query("SELECT aanvullend_voucher FROM skipas WHERE skipas_id='".addslashes($db->f("skipas_id"))."';");
		if($db2->next_record()) {
			if($db2->f("aanvullend_voucher")) {
				if($voucher[$voucherteller]["aanvullend_voucher"]) $voucher[$voucherteller]["aanvullend_voucher"].="\n";
				$voucher[$voucherteller]["aanvullend_voucher"].=$db2->f("aanvullend_voucher");
			}
		}
	}

	# Deelnemers bepalen
	if($db->f("persoonnummer")=="alg") {
		# Algemene optie
	} elseif($db->f("persoonnummer")=="pers") {
		$tempdeelnemers=@split(",",$db->f("deelnemers"));
		while(list($key,$value)=@each($tempdeelnemers)) {
			if($voucher[$voucherteller]["deelnemers"]) $voucher[$voucherteller]["deelnemers"].=",".$value; else $voucher[$voucherteller]["deelnemers"]=$value;
			if($db->f("skipas_id")) {
				# Kijken of er dubbele skipas-vouchers zijn
				if($skipas_voucher[$value]) {
					$skipas_dubbele_voucher[$value]=true;
				} else {
					$skipas_voucher[$value]=true;
				}
			}
		}
	}
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Naam";
$form->settings["layout"]["css"]=false;
#$form->settings["db"]["table"]="boeking";
#$form->settings["db"]["where"]="boeking_id='".addslashes($_GET["bid"])."'";
$form->settings["goto"]=$_GET["burl"];
$form->settings["message"]["submitbutton"]["nl"]="VOUCHERS AANMAKEN";
$form->settings["layout"]["top_submit_button"]=true;

#_field: (obl),id,title,db,prevalue,options,layout

#$form->field_htmlrow("","");


# Voucher-bestanden in $vars["temp_voucherfiles"] zetten
$d=dir("pdf/vouchers/");
while($entry=$d->read()) {
	if(ereg("^voucher_".basename($_GET["bid"])."_[0-9]+\.pdf$",$entry,$regs)) {
		$vars["temp_voucherfiles"][$entry]=filectime("pdf/vouchers/".$entry);
	}
}
@asort($vars["temp_voucherfiles"]);

#
# Tabel om PDF's te printen samenstellen
#
unset($vars["temp_pdfprinttable"]);
$vars["temp_pdfprinttable"].="<table style=\"border:1px solid #000000;width:600px;\">";
$vars["temp_pdfprinttable"].="<tr><td align=\"center\" style=\"\"><b>PDF's printen</b></td></tr>";
$vars["temp_pdfprinttable"].="<tr><td>&nbsp;</td></tr>";

# Voucher
$vars["temp_pdfprinttable"].="<tr><td>";
if(is_array($vars["temp_voucherfiles"])) {
	end($vars["temp_voucherfiles"]);
#	echo key($vars["temp_voucherfiles"]);
#	echo current($vars["temp_voucherfiles"]);
	$vars["temp_pdfprinttable"].="<table cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"middle\"><img src=\"pic/pdflogo.gif\" width=\"18\" height=\"18\"></td><td valign=\"middle\">&nbsp;";
	if(ereg("MSIE",$_SERVER["HTTP_USER_AGENT"])) {
		$vars["temp_pdfprinttable"].="<a href=\"cms_pdfdownload.php?pdffile=".urlencode("pdf/vouchers/".key($vars["temp_voucherfiles"]))."\" target=\"_blank\">";
	} else {
		$vars["temp_pdfprinttable"].="<a href=\"#\" onclick=\"window.open('cms_pdfdownload.php?pdffile=".urlencode("pdf/vouchers/".key($vars["temp_voucherfiles"]))."','_blank');return false;\">";
	}
	$vars["temp_pdfprinttable"].="Print de vouchers &raquo;</a> (laatste versie van ".date("d-m-Y",current($vars["temp_voucherfiles"])).")</td></tr></table>";
} else {
	$vars["temp_pdfprinttable"].="<table cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"middle\"><img src=\"pic/pdflogo.gif\" width=\"18\" height=\"18\"></td><td valign=\"middle\">&nbsp;<b>Let op! vouchers zijn nog niet aangemaakt. Gebruik onderstaand formulier.</td></tr></table>";
}
$vars["temp_pdfprinttable"].="</td></tr>";

# Voorbrief-pdf
#$pdffile_voorbrief="pdf/voorbrief_".$gegevens["stap1"]["taal"]."/".$gegevens["stap1"]["seizoenid"].".pdf";
$pdffile_voorbrief="pdf/voorbrief_".$gegevens["stap1"]["website"]."/".$gegevens["stap1"]["seizoenid"].".pdf";
if(file_exists($pdffile_voorbrief)) {
	$htmlrow="<a href=\"".htmlentities($pdffile_voorbrief)."\" target=\"_blank\">Print de bijbehorende voorbrief &raquo;</a>";
} else {
	$htmlrow="<b>Let op! voorbrief-PDF ontbreekt. Uploaden via <a href=\"cms_seizoenen.php?edit=9&9k0=".$gegevens["stap1"]["seizoenid"]."\" target=\"_blank\">seizoen</a>.</b>";
	unset($pdffile_voorbrief);
}
$vars["temp_pdfprinttable"].="<tr><td>";
$vars["temp_pdfprinttable"].="<table cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"middle\"><img src=\"pic/pdflogo.gif\" width=\"18\" height=\"18\"></td><td valign=\"middle\">&nbsp;".$htmlrow."</td></tr></table>";
$vars["temp_pdfprinttable"].="</td></tr>";

# Vertrekinfo+route-pdf
if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
	# onderliggend verzameltype
	$db->query("SELECT accommodatie_id FROM type WHERE type_id='".addslashes($gegevens["stap1"]["verzameltype_gekozentype_id"])."';");
	if($db->next_record()) {
		$verzameltype_gekozenacc_id=$db->f("accommodatie_id");
		$pdffile_route_gekozentypeid="pdf/route_".$gegevens["stap1"]["taal"]."/".$db->f("accommodatie_id").".pdf";
		if(file_exists($pdffile_route_gekozentypeid)) {
			$pdffile_route=$pdffile_route_gekozentypeid;
			$pdffile_route_gekozentypeid_aanwezig=true;
		}
	}
}
if(!$pdffile_route) {
	$pdffile_route="pdf/route_".$gegevens["stap1"]["taal"]."/".$gegevens["stap1"]["accinfo"]["accommodatieid"].".pdf";
}
if(file_exists($pdffile_route)) {
	if($gegevens["stap1"]["taal"]=="nl") {
		$check_array=split(",",$gegevens["stap1"]["accinfo"]["vertrekinfo_seizoengoedgekeurd"]);
	} else {
		$check_array=split(",",$gegevens["stap1"]["accinfo"]["vertrekinfo_seizoengoedgekeurd"."_".$gegevens["stap1"]["taal"]]);
	}
	if(@in_array($gegevens["stap1"]["seizoenid"],$check_array)) {
		$htmlrow="<a href=\"".htmlentities($pdffile_route)."\" target=\"_blank\">Print de bijbehorende vertrekinfo+route &raquo;</a>";
		if($pdffile_route_gekozentypeid_aanwezig) {
			$htmlrow.="&nbsp;&nbsp;&nbsp;<span style=\"font-size:0.8em;\">(van onderliggend verzameltype)</span>";
		} elseif($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
			$htmlrow.="&nbsp;&nbsp;&nbsp;<span style=\"font-size:0.8em;\">(van bovenliggend verzameltype)</span>";
		}
		$vars["temp_vertrekinfo_goedgekeurd"]=true;
	} else {
#		$htmlrow="<div style=\"background-color:#e587f6;width:700px;\"><a href=\"".htmlentities($pdffile_route)."\" target=\"_blank\">Print de bijbehorende vertrekinfo+route &raquo;</a> <b>Let op:</b> </b></div>";
		$htmlrow="<b>Let op! vertrekinfo+route-PDF is niet goedgekeurd voor het betreffende seizoen. Nu <a href=\"cms_accommodaties.php?edit=1&wzt=".$gegevens["stap1"]["accinfo"]["wzt"]."&1k0=".($verzameltype_gekozenacc_id ? $verzameltype_gekozenacc_id : $gegevens["stap1"]["accinfo"]["accommodatieid"])."#vertrekinfo\" target=\"_blank\">goedkeuren</a>.</b>";
		$vars["temp_vertrekinfo_goedgekeurd"]=false;
	}
} else {
	$htmlrow="<b>Let op! vertrekinfo+route-PDF ontbreekt. Uploaden via <a href=\"cms_accommodaties.php?edit=1&wzt=".$gegevens["stap1"]["accinfo"]["wzt"]."&1k0=".$gegevens["stap1"]["accinfo"]["accommodatieid"]."\" target=\"_blank\">accommodatie</a>.</b>";
	unset($pdffile_route);
}

$vars["temp_pdfprinttable"].="<tr><td>";
$vars["temp_pdfprinttable"].="<table cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"middle\"><img src=\"pic/pdflogo.gif\" width=\"18\" height=\"18\"></td><td valign=\"middle\">&nbsp;".$htmlrow."</td></tr></table>";
$vars["temp_pdfprinttable"].="</td></tr>";

# Plattegrond-pdf
unset($htmlrow);
$pdffile_plattegrond="pdf/plaats_plattegrond/".$gegevens["stap1"]["accinfo"]["plaats_id"].".pdf";
if(file_exists($pdffile_plattegrond)) {
	$htmlrow="<a href=\"".htmlentities($pdffile_plattegrond)."\" target=\"_blank\">Print de bijbehorende plattegrond &raquo;</a>";
} else {
	$db->query("SELECT pdfplattegrond_nietnodig FROM plaats WHERE plaats_id='".addslashes($gegevens["stap1"]["accinfo"]["plaats_id"])."';");
	if($gegevens["stap1"]["pdfplattegrond_nietnodig"] or ($db->next_record() and $db->f("pdfplattegrond_nietnodig")==1)) {
		$pdffile_plattegrond="okay";
		$htmlrow="Plattegrond-PDF is niet nodig bij de reisdocumenten";
	} else {
		$htmlrow="<b>Let op! plattegrond-PDF ontbreekt. Uploaden via <a href=\"cms_plaatsen.php?edit=4&wzt=".$gegevens["stap1"]["accinfo"]["wzt"]."&4k0=".$gegevens["stap1"]["accinfo"]["plaats_id"]."\" target=\"_blank\">plaats</a>.</b>";
		unset($pdffile_plattegrond);
	}
}
$vars["temp_pdfprinttable"].="<tr><td>";
$vars["temp_pdfprinttable"].="<table cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"middle\"><img src=\"pic/pdflogo.gif\" width=\"18\" height=\"18\"></td><td valign=\"middle\">&nbsp;".$htmlrow."</td></tr></table>";
$vars["temp_pdfprinttable"].="</td></tr>";
$vars["temp_pdfprinttable"].="</table><br><br>";

if($pdffile_voorbrief and $pdffile_route and $pdffile_plattegrond) {
	$vars["temp_pdffiles_aanwezig"]=true;
}

$form->field_htmlrow("","<b>Status wijzigen</b>");
if($gegevens["stap1"]["voucherstatus"]>=5) {
	$form->field_select(0,"na_aanmaken1","Wijzig de status na aanmaken naar","",array("selection"=>7),array("selection"=>$vars["voucherstatus_nawijzigingen"]),array("onchange"=>"document.frm.elements['input[na_aanmaken]'].value=document.frm.elements['input[na_aanmaken1]'].value;"));
#	$form->field_hidden("na_aanmaken_previous","7");
} else {
	$form->field_select(0,"na_aanmaken1","Wijzig de status na aanmaken naar","",array("selection"=>4),array("selection"=>$vars["voucherstatus_zonderwijzigingen"]),array("onchange"=>"document.frm.elements['input[na_aanmaken]'].value=document.frm.elements['input[na_aanmaken1]'].value;"));
#	$form->field_hidden("na_aanmaken_previous","4");
}

#$form->field_htmlrow("","<hr><a href=\"#\" onclick=\"document.frm.target='_blank';document.frm.elements['alleen_tonen'].value=1;document.frm.submit();document.frm.target='';document.frm.elements['alleen_tonen'].value=0;\">Bekijk de aan te maken vouchers &raquo;</a>&nbsp;&nbsp;(zonder ze op te slaan of te printen)");
$form->field_hidden("alleen_tonen","0");
if($vars["temp_pdffiles_aanwezig"]) {
	$form->field_yesno("vouchersmailen","Mail onderstaande voucher".($voucherteller>2 ? "s" : "")." naar de klant (incl. vertrekinformatie-PDF's)","","","",array("onclick"=>"if(document.frm.elements['input[vouchersmailen]'].checked) { document.frm.elements['na_aanmaken_previous'].value=document.frm.elements['input[na_aanmaken]'].value;document.frm.elements['input[na_aanmaken]'].value='".($gegevens["stap1"]["voucherstatus"]>=5 ? "10" : "3")."'; document.frm.elements['input[na_aanmaken1]'].value=document.frm.elements['input[na_aanmaken]'].value; } else { document.frm.elements['input[na_aanmaken]'].value=document.frm.elements['na_aanmaken_previous'].value; document.frm.elements['input[na_aanmaken1]'].value=document.frm.elements['input[na_aanmaken]'].value;};"));
}
#echo wt_dump($gegevens);

while(list($key,$value)=@each($voucher)) {
	if($key<>2) voucher_form($value,$key);
	$vouchers_aanwezig=true;
}

$form->field_hidden("voucherteller",$voucherteller);
$form->field_htmlrow("","<hr><b>Status wijzigen</b>");
if($gegevens["stap1"]["voucherstatus"]>=5) {
	$form->field_select(0,"na_aanmaken","Wijzig de status na aanmaken naar","",array("selection"=>7),array("selection"=>$vars["voucherstatus_nawijzigingen"]),array("onchange"=>"document.frm.elements['input[na_aanmaken1]'].value=document.frm.elements['input[na_aanmaken]'].value;"));
	$form->field_hidden("na_aanmaken_previous","7");
} else {
	$form->field_select(0,"na_aanmaken","Wijzig de status na aanmaken naar","",array("selection"=>4),array("selection"=>$vars["voucherstatus_zonderwijzigingen"]),array("onchange"=>"document.frm.elements['input[na_aanmaken1]'].value=document.frm.elements['input[na_aanmaken]'].value;"));
	$form->field_hidden("na_aanmaken_previous","4");
}

$form->check_input();
if($form->filled) {

}

if($form->okay) {

	require("admin/fpdf.php");

	class PDF extends FPDF {

		function _getfontpath() {
			return "pdf/fonts/";
		}
	}

	$pdf=new PDF();
	$pdf->gegevens=$gegevens;
	$pdf->AliasNbPages();
	$pdf->SetAutoPageBreak(false,0);

	for($i=1;$i<=$_POST["voucherteller"];$i++) {
		if($i==2) {
			$thuisblijvers=true;
			$i=1;
		}
		if(is_array($_POST["input"]["deelnemers".$i]) or $i==1) {
			$kolom=1;
			$deelnemersteller=0;
			unset($deelnemerskolom,$deelnemerskolom_teller,$deelnemernummer);
			if(is_array($_POST["input"]["deelnemers".$i])) {
				reset($_POST["input"]["deelnemers".$i]);
				while(list($key,$value)=each($_POST["input"]["deelnemers".$i])) {
					$deelnemernummer++;
					if($deelnemersteller>=10) {
						$kolom++;
						$deelnemersteller=0;
						if($kolom%2==1) {
							$deelnemernummer=1;
						}
					}
					$deelnemersteller++;
					$naam=substr("00".$deelnemernummer,-2)." ".wt_naam($gegevens["stap3"][$key]["voornaam"],$gegevens["stap3"][$key]["tussenvoegsel"],$gegevens["stap3"][$key]["achternaam"]);
					if($voucher[$i]["geboortedatum_voucher"] and isset($gegevens["stap3"][$key]["geboortedatum"])) $naam.=" (".wt_adodb_date("d-m-Y",$gegevens["stap3"][$key]["geboortedatum"]).")";
					if($deelnemerskolom[$kolom]) {
						$deelnemerskolom[$kolom].="\n".$naam;
					} else {
						$deelnemerskolom[$kolom].=$naam;
					}
					$deelnemerskolom_teller[$kolom]++;
				}
			} else {
				$deelnemerskolom[1]=true;
			}
			for($j=1;$j<=count($deelnemerskolom);$j=$j+2) {
				if($boven) {
					unset($boven);
					$y=153;
				} else {
					$boven=true;
					$pdf->AddPage();
					$y=0;
				}

				$pdf->SetY(6+$y);
#				if($gegevens["stap1"]["website_specifiek"]["websitetype"]==3 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==7 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
					# Zomerhuisje/Italissima/SuperSki - positie "VOUCHER" helemaal links
					$pdf->SetX(10);
					$pdf->SetFont('Arial','',20);
					$pdf->Cell(0,4,($thuisblijvers ? "" : "VOUCHER"),0,1);
#				} else {
#					$pdf->SetX(80);
#					$pdf->SetFont('Arial','',20);
#					$pdf->Cell(0,4,($thuisblijvers ? "" : "       VOUCHER"),0,1);
#				}
				$pdf->SetFont('Arial','',8);
#				if($gegevens["stap1"]["website_specifiek"]["websitetype"]==3 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==4 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==5 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==6 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==7 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
#					# Geen extra logo
#				} else {
#					# Extra logo WSA
#					$pdf->Image("pic/factuur_logo_wsa.png",10,3+$y,70);
#				}


				if($gegevens["stap1"]["website_specifiek"]["websitetype"]==4 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==5) {
					# Chalettour-logo
					$pdf->Image("pic/factuur_logo_chalettour.png",169.5,1+$y,32);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==6) {
					# ChaletsInVallandry-logo
					$pdf->Image("pic/factuur_logo_vallandry.png",150,5+$y,56);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==3) {
					# Zomerhuisje-logo
					$pdf->Image("pic/factuur_logo_zomerhuisje.png",150,5+$y,56);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==7) {
					# Italissima-logo
					$pdf->Image("pic/factuur_logo_italissima.png",150,5+$y,56);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
					# SuperSki-logo
					$pdf->Image("pic/factuur_logo_superski.png",150,5+$y,56);
				} else {
					# Chalet.nl-logo
					$pdf->Image("pic/factuur_logo.png",169.5,1+$y,32);
				}

#				$pdf->Ln(19);
#				$pdf->MultiCell(0,3,"".$pdf->gegevens["stap1"]["website_specifiek"]["langewebsitenaam"]."\nLindenhof 5\n3442 GT Woerden\n\nTel.: +31 348 43 46 49\nFax: +31 348 69 07 52\n\nE-mail: info@chalet.nl",0,"R");
#				$pdf->Ln(20);
				$pdf->SetFont('Arial','',10);

				$pdf->SetY(22+$y);

				$pdf->Cell(35,4,txt("refplushoofdboeker","voucher"),0,0,'L',0);
				$pdf->Cell(5,4,"  :",0,0,'L',0);
				$pdf->Cell(50,4,$gegevens["stap1"]["boekingsnummer"]." - ".wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]),0,0,'L',0);
				$pdf->Ln();

				$pdf->Cell(35,4,$vars["voucher_bestemming"][$accinfo["begincode"]],0,0,'L',0);
				$pdf->Cell(5,4,"  :",0,0,'L',0);
				$pdf->Cell(50,4,$accinfo["plaats"],0,0,'L',0);
				$pdf->Ln();
				$pdf->Cell(35,4,$vars["voucher_accommodatie"][$accinfo["begincode"]],0,0,'L',0);
				$pdf->Cell(5,4,"  :",0,0,'L',0);
				if($accinfo["bestelnaam"]) {
					$pdf->MultiCell(150,4,ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"]." (our name: ".$accinfo["bestelnaam"].")",0,'L',0);
				} else {
					$pdf->MultiCell(150,4,ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"],0,'L',0);
				}

				if($i==1 and !$thuisblijvers) {
					$pdf->Cell(35,4,"Type / Code",0,0,'L',0);
					$pdf->Cell(5,4,"  :",0,0,'L',0);
					$pdf->Cell(50,4,$accinfo["code"],0,0,'L',0);
					$pdf->Ln();
				}
				$pdf->Ln();

#				if(!$form->input["einddatum".$i]) {
#					$pdf->Ln();
#				}

				$pdf->Cell(35,4,($vars["landcodes"][$accinfo["begincode"]]<>$gegevens["stap1"]["taal"] ? txt("aanvang","voucher")." / " : "").$vars["voucher_eerstedag"][$accinfo["begincode"]],0,0,'L',0);
				$pdf->Cell(5,4,"  :",0,0,'L',0);
				$pdf->Cell(50,4,$form->input["aanvangsdatum".$i],0,0,'L',0);
				$pdf->Ln();

				if($form->input["einddatum".$i]) {
					$pdf->Cell(35,4,($vars["landcodes"][$accinfo["begincode"]]<>$gegevens["stap1"]["taal"] ? txt("laatstedag","voucher")." / " : "").$vars["voucher_laatstedag"][$accinfo["begincode"]],0,0,'L',0);
					$pdf->Cell(5,4,"  :",0,0,'L',0);
					$pdf->Cell(50,4,$form->input["einddatum".$i],0,0,'L',0);
					$pdf->Ln();
				}

				if($i==1) {
					$pdf->Ln();
					$pdf->Cell(35,4,txt("receptiesleutel","voucher"),0,0,'L',0);
					$pdf->Cell(5,4,"  :",0,0,'L',0);
					$pdf->Cell(50,4,$accinfo["receptie"],0,0,'L',0);
					$pdf->Ln();

					$pdf->Cell(35,4,txt("telefoonnummer","voucher"),0,0,'L',0);
					$pdf->Cell(5,4,"  :",0,0,'L',0);
					$pdf->MultiCell(0,4,$accinfo["telefoonnummer"],0,'L',0);
					$pdf->Ln();

					if(!$thuisblijvers) {
						$pdf->Cell(35,4,$vars["voucher_deelnemers"][$accinfo["begincode"]],0,0,'L',0);
						$pdf->Cell(5,4,"  :",0,0,'L',0);
						$pdf->Cell(50,4,$form->input["aantalpersonen".$i]." ".($form->input["aantalpersonen".$i]==1 ? $vars["voucher_persoon"][$accinfo["begincode"]] : $vars["voucher_personen"][$accinfo["begincode"]]),0,0,'L',0);
						$pdf->Ln();

						if($form->input["opmerkingen_voucher".$i]) {
							$pdf->Cell(35,4,txt("extra","voucher"),0,0,'L',0);
							$pdf->Cell(5,4,"  :",0,0,'L',0);
							$pdf->MultiCell(0,4,"".$form->input["opmerkingen_voucher".$i],0,"L");
							$pdf->Ln();
						}
						if($form->input["voucherinfo".$i]) {
							$pdf->Cell(35,4,txt("infoaccommodatie","voucher"),0,0,'L',0);
							$pdf->Cell(5,4,"  :",0,0,'L',0);
							$pdf->MultiCell(0,4,"".$form->input["voucherinfo".$i],0,"L");
							$pdf->Ln();
						}
					}
					$pdf->Ln();
					$pdf->MultiCell(0,4,txt("alleenvoornoodgevallen","voucher")."\n".txt("ofbuitenkantooruren","voucher"),0,"L");
				} else {
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(35,4,txt("omschrijving","voucher"),0,0,'L',0);
					$pdf->Cell(5,4,"  :",0,0,'L',0);
					$pdf->Cell(50,4,($deelnemerskolom_teller[$j]+$deelnemerskolom_teller[$j+1])." x ".$form->input["omschrijving_voucher".$i],0,0,'L',0);
					$pdf->Ln();
					$pdf->SetFont('Arial','',10);
					$pdf->Ln();

					$pdf->Cell(35,4,$vars["voucher_deelnemers"][$accinfo["begincode"]],0,0,'L',0);
					$pdf->Cell(5,4,"  :",0,0,'L',0);

					$posy=$pdf->GetY();
					$pdf->MultiCell(0,4,"".$deelnemerskolom[$j],0,"L");
					$posy2=$pdf->GetY();
					if($deelnemerskolom[$j+1]) {
						$pdf->SetY($posy);
						$pdf->SetX(123);
						$pdf->MultiCell(0,4,"".$deelnemerskolom[$j+1],0,"L");
						$pdf->SetY($posy2);
					}

					$pdf->Ln();

					if($form->input["aanvullend_voucher".$i]) {
						$pdf->SetFont('Arial','B',10);
						$pdf->Cell(35,4,txt("letop","voucher"),0,0,'L',0);
						$pdf->SetFont('Arial','',10);
						$pdf->Cell(5,4,"  :",0,0,'L',0);
						$pdf->MultiCell(0,4,$form->input["aanvullend_voucher".$i]);
						$pdf->Ln();
					}
				}

				$ondersteregel=123;

				$pdf->SetY($ondersteregel+$y);

				if(file_exists($voucher[$i]["logo"]) and !$thuisblijvers) {
					$pdf->Image($voucher[$i]["logo"],11.5,($ondersteregel-10)+$y,21);
				}
				$pdf->Ln(7.3);

				if($gegevens["stap1"]["website_specifiek"]["websitetype"]==3 or $gegevens["stap1"]["website_specifiek"]["websitetype"]==7) {
					# Zomerhuisje/Italissima - anders centreren
					$pdf->Cell(35,4,"",0,0,'L',0);
#					$pdf->Cell(5,4,"",0,0,'L',0);
					$pdf->Ln(0);
					$pdf->SetFont('Arial','B',18);
					$pdf->Cell(0,4,($thuisblijvers ? txt("informatievoorthuisblijvers","voucher") : $form->input["naam_voucher".$i]),0,0,'C',0);
				} else {
					$pdf->Cell(35,4,"",0,0,'L',0);
					$pdf->Cell(5,4,"",0,0,'L',0);
					$pdf->SetFont('Arial','B',18);
					$pdf->Cell(50,4,($thuisblijvers ? txt("informatievoorthuisblijvers","voucher") : $form->input["naam_voucher".$i]),0,0,'L',0);
				}
				$pdf->Ln(5);

				if($gegevens["stap1"]["website_specifiek"]["websitetype"]==3) {
					# Zomerhuisje - onderaan andere URL tonen
					$pdf->Cell(35,4,"",0,0,'L',0);
#					$pdf->Cell(0,4,"",0,0,'L',0);
					$pdf->Ln(0);
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(0,4,"www.zomerhuisje.nl",0,0,'C',0);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==7) {
					# Italissima - onderaan andere URL tonen
					$pdf->Cell(35,4,"",0,0,'L',0);
#					$pdf->Cell(0,4,"",0,0,'L',0);
					$pdf->Ln(0);
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(0,4,"www.italissima.nl",0,0,'C',0);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
					# SuperSki - onderaan andere URL tonen
					$pdf->Cell(35,4,"",0,0,'L',0);
#					$pdf->Cell(0,4,"",0,0,'L',0);
					$pdf->Ln(0);
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(0,4,"www.superski.nl",0,0,'C',0);
				} else {
					$pdf->Cell(35,4,"",0,0,'L',0);
					$pdf->Cell(5,4,"",0,0,'L',0);
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(0,4,"www.chalet.nl  -  www.chalet.eu",0,0,'L',0);
				}
				if($gegevens["stap1"]["website_specifiek"]["websitetype"]==3) {
					# Zomerhuisje mailadres
					$pdf->SetFont('Arial','B',6);
					$pdf->Ln(4);
					$pdf->Cell(0,4,"Chalet.nl B.V. - Lindenhof 5 - 3442 GT Woerden - The Netherlands - Tel.: +31 348 43 46 49 - Emergency: +31 616 45 73 34 - Fax: +31 348 69 07 52 - E-mail: info@zomerhuisje.nl",0,0,'C',0);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==7) {
					# Italissima-mailadres
					$pdf->SetFont('Arial','B',6);
					$pdf->Ln(4);
					$pdf->Cell(0,4,"Chalet.nl B.V. - Lindenhof 5 - 3442 GT Woerden - The Netherlands - Tel.: +31 348 43 46 49 - Emergency: +31 616 45 73 34 - Fax: +31 348 69 07 52 - E-mail: info@italissima.nl",0,0,'C',0);
				} elseif($gegevens["stap1"]["website_specifiek"]["websitetype"]==8) {
					# SuperSki-mailadres
					$pdf->SetFont('Arial','B',6);
					$pdf->Ln(4);
					$pdf->Cell(0,4,"SuperSki - Lindenhof 5 - 3442 GT Woerden - The Netherlands - Tel.: +31 348 43 46 49 - Emergency: +31 616 45 73 34 - Fax: +31 348 69 07 52 - E-mail: info@superski.nl",0,0,'C',0);
				} else {
					$pdf->SetFont('Arial','B',7);
					$pdf->Ln(4);
					$pdf->Cell(0,4,"Chalet.nl B.V. - Lindenhof 5 - 3442 GT Woerden - The Netherlands - Tel.: +31 348 43 46 49 - Emergency: +31 616 45 73 34 - Fax: +31 348 69 07 52 - E-mail: info@chalet.nl",0,0,'C',0);
				}

				$pdf->Ln();
			}
		}
		if($thuisblijvers) {
			$thuisblijvers=false;
			$i=2;
		}
	}

	if($_POST["alleen_tonen"]) {
		$pdf->Output();
		exit;
	} else {
		# Vouchers als tijdelijk bestand opslaan voor te verzenden mailtje
		$tempfile="tmp/voucher_".ereg_replace(" / ","_",$gegevens["stap1"]["boekingsnummer"]).".pdf";
		$tempfile_teller=1;
		while(file_exists($tempfile)) {
			$tempfile_teller++;
			$tempfile="tmp/voucher_".ereg_replace(" / ","_",$gegevens["stap1"]["boekingsnummer"])."_".$tempfile_teller.".pdf";
		}

		$pdf->Output($tempfile);

		# Vouchers opslaan voor vouchers-archief
		$archieffile="pdf/vouchers/voucher_".$gegevens["stap1"]["boekingid"]."_1.pdf";
		while(file_exists($archieffile)) {
			$archieffile_teller++;
			$archieffile="pdf/vouchers/voucher_".$gegevens["stap1"]["boekingid"]."_".$archieffile_teller.".pdf";
		}

		$pdf->Output($archieffile);
		chmod($archieffile,0666);


		if($form->input["vouchersmailen"]) {

			# Mail met vouchers versturen aan klant (html-mail!)
			$mail=new wt_mail;
			$mail->fromname=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
			$mail->from=$gegevens["stap1"]["website_specifiek"]["email"];
			$mail->to=$gegevens["stap2"]["email"];
			$mail->subject="[".$gegevens["stap1"]["boekingsnummer"]."] Voucher".($voucherteller>2 ? "s" : "");

			# Indien geboekt door reisbureau: andere kop boven mailtje
			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$mail->html=txt("mailtje_wederverkoop","voucher",array("v_reserveringsnummer"=>$gegevens["stap1"]["boekingsnummer"],"v_hoofdboeker"=>wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"])))."<br><br><br>";
			}

			$mail->html_top="<table width=600><tr><td>";
			$mail->html.=nl2br(txt("mailtje","voucher",array("v_voornaam"=>trim($gegevens["stap2"]["voornaam"]),"v_websitenaam"=>$gegevens["stap1"]["website_specifiek"]["websitenaam"],"v_langewebsitenaam"=>$gegevens["stap1"]["website_specifiek"]["langewebsitenaam"],"h_1"=>"<table cellspacing=0 cellpadding=0 style=\"border:1px solid #000000;padding:5px;background-color:#ffffb1;\"><tr><td>","h_2"=>"</td></tr></table>","h_3"=>"<a href=\"http://get.adobe.com/reader/\">http://get.adobe.com/reader/</a>")));
			$mail->html_bottom="</td></tr>";

			$mail->attachment($tempfile);

			if(file_exists($pdffile_voorbrief)) {
				$mail->attachment($pdffile_voorbrief,"","",txt("attachmentnaam_voorbrief_pdf","voucher"));
				$algemene_informatie=true;
			}

			if(file_exists($pdffile_route)) {
				if($gegevens["stap1"]["reisbureau_user_id"]) {
					# route alleen bij wederverkoop in voucher-mailtje (in andere gevallen in het mailtje "algemene informatie")
					$mail->attachment($pdffile_route,"","",txt("attachmentnaam_route_pdf","voucher"));
				}
				$algemene_informatie=true;
			}

			if(file_exists($pdffile_plattegrond)) {
				if($gegevens["stap1"]["reisbureau_user_id"]) {
					# plattegrond alleen bij wederverkoop in voucher-mailtje (in andere gevallen in het mailtje "algemene informatie")
					$mail->attachment($pdffile_plattegrond,"","",txt("attachmentnaam_plattegrond_pdf","voucher"));
				}
				$algemene_informatie=true;
			}

			if($algemene_informatie and !$gegevens["stap1"]["reisbureau_user_id"]) {
				$mail->subject.=" ".txt("mailtje_onderwerp_1van2","voucher");
			}
			$mail->send();
			if($algemene_informatie and !$gegevens["stap1"]["reisbureau_user_id"]) {
				# Mail met vertrekinformatie mailen (niet als het een wederverkoop-boeking betreft)

				# even wachten (zodat mailtje 2 later aankomt dan mailtje 1)
				sleep(2);

				unset($mail);
				$mail=new wt_mail;
				$mail->fromname=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
				$mail->from=$gegevens["stap1"]["website_specifiek"]["email"];
				$mail->to=$gegevens["stap2"]["email"];
				$mail->subject="[".$gegevens["stap1"]["boekingsnummer"]."] ".txt("mailtje_zonder_voucher_onderwerp","voucher");
				$mail->subject.=" ".txt("mailtje_onderwerp_2van2","voucher");


				$mail->html_top="<table width=600><tr><td>";
				$mail->html.=nl2br(txt("mailtje_zonder_voucher","voucher",array("v_voornaam"=>trim($gegevens["stap2"]["voornaam"]),"v_websitenaam"=>$gegevens["stap1"]["website_specifiek"]["websitenaam"],"h_1"=>"","h_2"=>"","h_3"=>"<a href=\"http://get.adobe.com/reader/\">http://get.adobe.com/reader/</a>")));
				$mail->html_bottom="</td></tr>";

				if(file_exists($pdffile_voorbrief)) {
					$mail->attachment($pdffile_voorbrief,"","",txt("attachmentnaam_voorbrief_pdf","voucher"));
				}

				if(file_exists($pdffile_route)) {
					$mail->attachment($pdffile_route,"","",txt("attachmentnaam_route_pdf","voucher"));
				}

				if(file_exists($pdffile_plattegrond)) {
					$mail->attachment($pdffile_plattegrond,"","",txt("attachmentnaam_plattegrond_pdf","voucher"));
				}
				$mail->send();
			}
			chalet_log("vouchers aangemaakt en gemaild aan ".$gegevens["stap2"]["email"],true,true);
		} else {
			chalet_log("vouchers aangemaakt",true,true);
		}
		unlink($tempfile);

		# voucherstatus wegschrijven
		if($form->input["na_aanmaken"]<>"") {
			$db->query("UPDATE boeking SET voucherstatus='".addslashes($form->input["na_aanmaken"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			if($form->input["na_aanmaken"]<>$gegevens["stap1"]["voucherstatus"]) {
				chalet_log("voucherstatus: ".$vars["voucherstatus"][$form->input["na_aanmaken"]],true,true);
			}
		}
	}
}
$form->end_declaration();

$layout->display_all($cms->page_title);

?>