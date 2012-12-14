<?php

$mustlogin=true;

include("admin/vars.php");

if($_GET["11k0"]) {
	$db->query("SELECT voucher, algemeneoptie FROM optie_soort WHERE optie_soort_id='".addslashes($_GET["11k0"])."';");
	if($db->next_record()) {
		$temp["voucher"]=$db->f("voucher");
		$temp["algemeneoptie"]=$db->f("algemeneoptie");
	}
}

if($_GET["12k0"]) {
	# controle op ingevulde tarieven (per seizoen)
	$db->query("SELECT seizoen_id, naam FROM seizoen WHERE optietarieven_controleren_in_cms=1 ORDER BY begin, eind;");
	while($db->next_record()) {
		$sz_controle[$db->f("seizoen_id")]=$db->f("naam");
	}

	while(list($key,$value)=@each($sz_controle)) {
		$db->query("SELECT DISTINCT oo.optie_onderdeel_id FROM optie_onderdeel oo, optie_tarief ot WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id='".addslashes($_GET["12k0"])."' AND ot.seizoen_id='".$key."';");
		while($db->next_record()) {
			$sz_controle_array[$key][$db->f("optie_onderdeel_id")]="ingevuld";
		}
	}


	# Seizoenen laden t.b.v. vertrekinfo_seizoengoedgekeurd
	$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE type='1' AND UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY type, begin, eind;");
	while($db->next_record()) {
		$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
		$laatste_seizoen=$db->f("seizoen_id");
	}

	# Vertrekinfo-tracking
	$vertrekinfo_tracking=vertrekinfo_tracking("optie_groep",array("vertrekinfo_optiegroep"),$_GET["12k0"],$laatste_seizoen);
}

if($_POST["kopieer"] and $_POST["from"] and $_POST["to"] and $_POST["from"]<>$_POST["to"]) {
	# Kopieerfunctie tarieven naar ander seizoen
	$db->query("SELECT UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id='".addslashes($_POST["from"])."';");
	if($db->next_record()) {
		$timeteller=$db->f("begin");
		unset($teller);
		while($timeteller<=$db->f("eind")) {
			$teller++;
			$weken_from[$teller]=$timeteller;
			$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
		}
		$db->query("SELECT UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id='".addslashes($_POST["to"])."';");
		if($db->next_record()) {
			$nieuwseizoen["begin"]=$db->f("begin");
			$nieuwseizoen["eind"]=$db->f("eind");
			$timeteller=$nieuwseizoen["begin"];
			unset($teller);
			while($timeteller<=$nieuwseizoen["eind"]) {
				$teller++;
				$weken_to[$weken_from[$teller]]=$timeteller;
				$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
			}

			# Alle optieonderdelen een-voor-een doorlopen
			$db->query("SELECT DISTINCT ot.optie_onderdeel_id FROM optie_onderdeel oo, optie_tarief ot WHERE ot.seizoen_id='".addslashes($_POST["from"])."' AND ot.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id='".addslashes($_GET["12k0"])."' ORDER BY ot.week;");
			while($db->next_record()) {
				$db2->query("SELECT ot.optie_onderdeel_id, ot.seizoen_id, ot.week, ot.beschikbaar, ot.verkoop, ot.inkoop, ot.korting, ot.omzetbonus FROM optie_tarief ot WHERE ot.seizoen_id='".addslashes($_POST["from"])."' AND ot.optie_onderdeel_id='".$db->f("optie_onderdeel_id")."' ORDER BY ot.week;");
				unset($tarieven,$anders,$vergelijk);
				$eersterecord=true;
				while($db2->next_record()) {
					$tarieven[$weken_to[$db2->f("week")]]["beschikbaar"]=$db2->f("beschikbaar");
					$tarieven[$weken_to[$db2->f("week")]]["verkoop"]=$db2->f("verkoop");
					$tarieven[$weken_to[$db2->f("week")]]["inkoop"]=$db2->f("inkoop");
					$tarieven[$weken_to[$db2->f("week")]]["korting"]=$db2->f("korting");
					$tarieven[$weken_to[$db2->f("week")]]["omzetbonus"]=$db2->f("omzetbonus");

					if($eersterecord) {
						unset($eersterecord);
						$vergelijk["verkoop"]=$db2->f("verkoop");
						$vergelijk["inkoop"]=$db2->f("inkoop");
						$vergelijk["korting"]=$db2->f("korting");
						$vergelijk["omzetbonus"]=$db2->f("omzetbonus");
					} else {
						if($vergelijk["verkoop"]<>$db2->f("verkoop")) $anders["verkoop"]=true;
						if($vergelijk["inkoop"]<>$db2->f("inkoop")) $anders["inkoop"]=true;
						if($vergelijk["korting"]<>$db2->f("korting")) $anders["korting"]=true;
						if($vergelijk["omzetbonus"]<>$db2->f("omzetbonus")) $anders["omzetbonus"]=true;
					}
				}
				if($anders["verkoop"] or $anders["inkoop"] or $anders["korting"] or $anders["omzetbonus"]) {
					while(list($key,$value)=each($tarieven)) {
						$setquery="optie_onderdeel_id='".$db->f("optie_onderdeel_id")."', week='".addslashes($key)."', seizoen_id='".addslashes($_POST["to"])."', beschikbaar='".addslashes($value["beschikbaar"])."', verkoop='".addslashes($value["verkoop"])."', inkoop='".addslashes($value["inkoop"])."', korting='".addslashes($value["korting"])."', omzetbonus='".addslashes($value["omzetbonus"])."'";
						$db3->query("SELECT optie_onderdeel_id FROM optie_tarief WHERE optie_onderdeel_id='".$db->f("optie_onderdeel_id")."' AND seizoen_id='".addslashes($_POST["to"])."' AND week='".addslashes($key)."';");
						if($db3->num_rows()) {
							$db3->query("UPDATE optie_tarief SET ".$setquery." WHERE optie_onderdeel_id='".$db->f("optie_onderdeel_id")."' AND seizoen_id='".addslashes($_POST["to"])."' AND week='".addslashes($key)."';");
						} else {
							$db3->query("INSERT INTO optie_tarief SET ".$setquery.";");
						}
					}
				} else {
					$timeteller=$nieuwseizoen["begin"];
					while($timeteller<=$nieuwseizoen["eind"]) {
						$setquery="optie_onderdeel_id='".$db->f("optie_onderdeel_id")."', week='".$timeteller."', seizoen_id='".addslashes($_POST["to"])."', beschikbaar='1', verkoop='".addslashes($vergelijk["verkoop"])."', inkoop='".addslashes($vergelijk["inkoop"])."', korting='".addslashes($vergelijk["korting"])."', omzetbonus='".addslashes($vergelijk["omzetbonus"])."'";
						$db3->query("SELECT optie_onderdeel_id FROM optie_tarief WHERE optie_onderdeel_id='".$db->f("optie_onderdeel_id")."' AND seizoen_id='".addslashes($_POST["to"])."' AND week='".addslashes($timeteller)."';");
						if($db3->num_rows()) {
							$db3->query("UPDATE optie_tarief SET ".$setquery." WHERE optie_onderdeel_id='".$db->f("optie_onderdeel_id")."' AND seizoen_id='".addslashes($_POST["to"])."' AND week='".addslashes($timeteller)."';");
						} else {
							$db3->query("INSERT INTO optie_tarief SET ".$setquery.";");
						}
						$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
					}
				}
			}
		}
	}
	header("Location: ".$_SERVER["REQUEST_URI"]."&tariefcopy=1");
	exit;
}

if($_POST["kopieergroep"] and $_POST["naam"] and $_POST["leverancier"] and $_GET["12k0"]) {
	#
	# Kopieerfunctie optiegroepen
	#

	# Wat zijn actieve seizoenen?
	$db->query("SELECT seizoen_id FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".time()."';");
	unset($actiefseizoen);
	while($db->next_record()) {
		if($actiefseizoen) $actiefseizoen.=",".$db->f("seizoen_id"); else $actiefseizoen=$db->f("seizoen_id");
	}

	# tabel optie_groep
	$db->query("SELECT optie_soort_id, naam_voucher, naam_voucher_de, naam_voucher_en, naam_voucher_fr, duur, omschrijving, omschrijving_de, omschrijving_en, omschrijving_fr, aanvullend_voucher, aanvullend_voucher_de, aanvullend_voucher_en, aanvullend_voucher_fr, tooneinddatum_voucher, contactpersoon, telefoonnummer, faxnummer, noodnummer FROM optie_groep WHERE optie_groep_id='".addslashes($_GET["12k0"])."';");
	if($db->next_record()) {
		$optie_groep_id=addslashes($_GET["12k0"]);
		unset($setquery);
		while(list($key,$value)=each($db->Record)) {
			if(($value or $value=="0") and !ereg("^[0-9]+$",$key)) {
				$setquery.=", ".$key."='".addslashes($value)."'";
			}
		}
		$db2->query("INSERT INTO optie_groep SET naam='".addslashes($_POST["naam"])."', optieleverancier_id='".addslashes($_POST["leverancier"])."'".$setquery.";");
		$newoptie_groep_id=$db2->insert_id();

		# Vouchter-logo
		if(file_exists("pic/cms/voucherlogo_optie/".$optie_groep_id.".jpg")) {
			copy("pic/cms/voucherlogo_optie/".$optie_groep_id.".jpg","pic/cms/voucherlogo_optie/".$newoptie_groep_id.".jpg");
		}

		# tabel optie_onderdeel
		$db->query("SELECT optie_onderdeel_id, naam, naam_de, naam_en, naam_fr, voucher, omschrijving_voucher, tekstbegin_voucher, teksteind_voucher, begindag, geboortedatum_voucher, einddag, volgorde, toelichting, min_leeftijd, max_leeftijd, min_deelnemers, leverancierscode, te_selecteren FROM optie_onderdeel WHERE optie_groep_id=".$optie_groep_id.";");
		while($db->next_record()) {
			unset($setquery);
			$optie_onderdeel_id=$db->f("optie_onderdeel_id");
			while(list($key,$value)=each($db->Record)) {
				if(($value or $value=="0") and !ereg("^[0-9]+$",$key) and $key<>"optie_onderdeel_id") {
					$setquery.=", ".$key."='".addslashes($value)."'";
				}
			}
			$db2->query("INSERT INTO optie_onderdeel SET optie_groep_id='".addslashes($newoptie_groep_id)."'".$setquery.";");
			$newoptie_onderdeel_id=$db2->insert_id();

			# tabel optie_tarief
			$db2->query("SELECT seizoen_id, week, beschikbaar, verkoop, inkoop, korting, omzetbonus FROM optie_tarief WHERE optie_onderdeel_id='".$optie_onderdeel_id."'".($actiefseizoen ? " AND seizoen_id IN (".$actiefseizoen.")" : "").";");
			while($db2->next_record()) {
				$db3->query("INSERT INTO optie_tarief SET optie_onderdeel_id='".addslashes($newoptie_onderdeel_id)."', seizoen_id='".addslashes($db2->f("seizoen_id"))."', week='".addslashes($db2->f("week"))."', beschikbaar='".addslashes($db2->f("beschikbaar"))."', verkoop='".addslashes($db2->f("verkoop"))."', inkoop='".addslashes($db2->f("inkoop"))."', korting='".addslashes($db2->f("korting"))."', omzetbonus='".addslashes($db2->f("omzetbonus"))."';");
			}
		}
		header("Location: cms_optie_groepen.php?show=12&bc=".$_GET["bc"]."&11k0=".$_GET["11k0"]."&12k0=".$newoptie_groep_id);
		exit;
	}
}
$cms->settings[12]["list"]["show_icon"]=true;
$cms->settings[12]["list"]["edit_icon"]=true;
$cms->settings[12]["list"]["delete_icon"]=true;

$cms->db[12]["set"]="optie_soort_id='".addslashes($_GET["11k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(12,"text","naam");
$cms->db_field(12,"select","optieleverancier_id","",array("othertable"=>"24","otherkeyfield"=>"optieleverancier_id","otherfield"=>"naam"));
$cms->db_field(12,"integer","duur");
$cms->db_field(12,"textarea","omschrijving");
if($vars["cmstaal"]) $cms->db_field(12,"textarea","omschrijving_".$vars["cmstaal"]);
$cms->db_field(12,"text","contactpersoon");
$cms->db_field(12,"text","telefoonnummer");
$cms->db_field(12,"text","faxnummer");
$cms->db_field(12,"text","noodnummer");
$cms->db_field(12,"select","skipas_id","",array("othertable"=>"10","otherkeyfield"=>"skipas_id","otherfield"=>"naam"));
$cms->db_field(12,"text","naam_voucher");
if($vars["cmstaal"]) $cms->db_field(12,"text","naam_voucher_".$vars["cmstaal"]);
$cms->db_field(12,"textarea","aanvullend_voucher");
if($vars["cmstaal"]) $cms->db_field(12,"textarea","aanvullend_voucher_".$vars["cmstaal"]);
$cms->db_field(12,"yesno","tooneinddatum_voucher");
$cms->db_field(12,"picture","voucherlogo","",array("savelocation"=>"pic/cms/voucherlogo_optie/","filetype"=>"jpg"));

# Vertrekinfo-systeem
$cms->db_field(12,"checkbox","vertrekinfo_goedgekeurd_seizoen","",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(12,"text","vertrekinfo_goedgekeurd_datetime");
$cms->db_field(12,"textarea","vertrekinfo_optiegroep");

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(12,"naam","Naam");

if($vars["cmstaal"]) {
	$db->query("SELECT omschrijving, omschrijving_".$vars["cmstaal"].", voucher FROM optie_soort WHERE optie_soort_id='".addslashes($_GET["11k0"])."';");
} else {
	$db->query("SELECT omschrijving, voucher FROM optie_soort WHERE optie_soort_id='".addslashes($_GET["11k0"])."';");
}
if($db->next_record()) {
	$temp["soort_omschrijving"]=$db->f("omschrijving");
	$temp["soort_omschrijving_".$vars["cmstaal"]]=$db->f("omschrijving_".$vars["cmstaal"]);
	$temp["voucher"]=$db->f("voucher");
}

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(12,1,"naam");
$cms->edit_field(12,0,"optieleverancier_id","Leverancier");
if($vars["cmstaal"]) {
	$cms->edit_field(12,1,"htmlcol","Soort-omschrijving NL",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(htmlentities($temp["soort_omschrijving"]))."</span>"));
	$cms->edit_field(12,0,"omschrijving","Groep-omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(12,1,"htmlcol","Soort-omschrijving ".strtoupper($vars["cmstaal"]),array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(htmlentities($temp["soort_omschrijving_".$vars["cmstaal"]]))."</span>"));
	$cms->edit_field(12,0,"omschrijving_".$vars["cmstaal"],"Groep-omschrijving ".strtoupper($vars["cmstaal"]));
} else {
#	$cms->edit_field(2,1,"htmlcol","Accommodatie-omschrijving",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(htmlentities($temp["omschrijving"]))."</span>"));
#	$cms->edit_field(2,0,"omschrijving","Type-omschrijving");
	$cms->edit_field(12,1,"htmlcol","Soort-omschrijving",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(htmlentities($temp["soort_omschrijving"]))."</span>"));
	$cms->edit_field(12,0,"omschrijving","Groep-omschrijving");
}


$cms->edit_field(12,0,"contactpersoon","Contactpersoon");
$cms->edit_field(12,0,"telefoonnummer","Telefoonnummer");
$cms->edit_field(12,0,"faxnummer","Faxnummer");
$cms->edit_field(12,0,"noodnummer","Noodnummer");
if(!$temp["algemeneoptie"]) {
	$cms->edit_field(12,0,"skipas_id","Gekoppelde skipas");
}
$cms->edit_field(12,0,"duur","Aantal dagen");
if($temp["voucher"]) {
	$cms->edit_field(12,0,"htmlrow","<hr><b>Voucher</b>");
	if($vars["cmstaal"]) {
		$cms->edit_field(12,0,"naam_voucher","Naam van de voucher NL","",array("noedit"=>true));
		$cms->edit_field(12,0,"naam_voucher_".$vars["cmstaal"],"Naam van de voucher ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(12,1,"naam_voucher","Naam van de voucher");
	}
	if($vars["cmstaal"]) {
		$cms->edit_field(12,0,"aanvullend_voucher","Let-op-tekst op de voucher NL","",array("noedit"=>true));
		$cms->edit_field(12,0,"aanvullend_voucher_".$vars["cmstaal"],"Let-op-tekst op de voucher ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(12,0,"aanvullend_voucher","Let-op-tekst op de voucher");
	}
	$cms->edit_field(12,0,"tooneinddatum_voucher","Toon een einddatum",array("selection"=>true));
	$cms->edit_field(12,0,"voucherlogo","Voucherlogo","",array("img_width"=>"600","img_height"=>"600"));
}
$cms->edit_field(12,0,"htmlrow","<hr><br><b>Nieuw vertrekinfo-systeem (nog niet in gebruik, maar gegevens invoeren is al mogelijk)</b>");
$cms->edit_field(12,0,"htmlrow","<br><i>Alinea 'naam optie-soort'</i>");
$cms->edit_field(12,0,"htmlcol","Beschikbare variabelen",array("html"=>"<table style=\"margin-top:15px;margin-bottom:15px;width:675px;\" class=\"tbl\" cellspacing=\"0\"><tr style=\"font-weight:bold;\"><th>variabele</th><th>omschrijving</th><th>voorbeeldwaarde</th></tr>
                 <tr><td>[optieleverancier-plaats]</td><td>per plaats/optieleverancier specifieke waarde</td><td>Alpe d'Huez - <a href=\"".$vars["path"]."cms_plaatsen.php?show=4&wzt=1&4k0=44\" target=\"_blank\">invulvoorbeeld</a></tr>
                 </table>"));

$cms->edit_field(12,0,"vertrekinfo_optiegroep","Tekst");
if($vertrekinfo_tracking["vertrekinfo_optiegroep"]) {
	$cms->edit_field(12,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_optiegroep"]))."</div>"));
}
$cms->edit_field(12,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo</b>");
$cms->edit_field(12,0,"vertrekinfo_goedgekeurd_seizoen","Vertrekinfo is goedgekeurd voor seizoen","","",array("one_per_line"=>true));
$cms->edit_field(12,0,"vertrekinfo_goedgekeurd_datetime","Laatste goedkeuring","","",array("one_per_line"=>true));

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(12);
if($cms_form[12]->filled) {
	if(!$temp["algemeneoptie"] and $cms_form[12]->input["optieleverancier_id"] and $cms_form[12]->input["skipas_id"]) {
		$cms_form[12]->error("skipas_id","vul leverancier óf gekoppelde skipas in (niet allebei tegelijk)");
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[12]="optiegroep-gegevens";
$cms->show_mainfield[12]="naam";
$cms->show_field(12,"naam","Naam");

# Controle op delete-opdracht
if($_GET["delete"]==12 and $_GET["12k0"]) {
	$db->query("SELECT optie_onderdeel_id FROM optie_onderdeel WHERE optie_groep_id='".addslashes($_GET["12k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(12,"Er zijn nog <a href=\"cms_optie_onderdelen.php?13where=".urlencode("optie_groep_id=".$_GET["12k0"])."\">optie-onderdelen</a> gekoppeld");
	}
}


#
#
# optie_onderdeel
#
#
$cms->settings[12]["connect"][]=13;
$cms->settings[13]["parent"]=12;

$cms->settings[13]["list"]["show_icon"]=true;
$cms->settings[13]["list"]["edit_icon"]=true;
$cms->settings[13]["list"]["delete_icon"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->settings[13]["prevalue"]["optie_groep_id"]=$_GET["12k0"];
$cms->db[13]["where"]="optie_groep_id='".addslashes($_GET["12k0"])."'";

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(13,"text","naam");
$cms->db_field(13,"integer","volgorde");
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->db_field(13,"select","optietarieven_controleren_in_cms_".$key,"optie_onderdeel_id",array("selection"=>$sz_controle_array[$key]));
	}
}

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[13]=array("volgorde","naam");
$cms->list_field(13,"naam","Naam");
$cms->list_field(13,"volgorde","Volgorde");
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->list_field(13,"optietarieven_controleren_in_cms_".$key,$value);
	}
}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>