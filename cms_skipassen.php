<?php

$mustlogin=true;

include("admin/vars.php");


if(!$_GET["10k0"]) {
	# controle op ingevulde tarieven (per seizoen)
	$db->query("SELECT seizoen_id, naam FROM seizoen WHERE optietarieven_controleren_in_cms=1 AND type=1 ORDER BY begin, eind;");
	while($db->next_record()) {
		$sz_controle[$db->f("seizoen_id")]=$db->f("naam");
	}

	while(list($key,$value)=@each($sz_controle)) {
		$db->query("SELECT DISTINCT skipas_id FROM skipas_tarief WHERE seizoen_id='".$key."';");
		while($db->next_record()) {
			$sz_controle_array[$key][$db->f("skipas_id")]="ingevuld";
		}
	}
}

if($_GET["10k0"]) {
	# Seizoenen laden t.b.v. vertrekinfo_seizoengoedgekeurd
	$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE type='1' AND UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY type, begin, eind;");
	while($db->next_record()) {
		$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
		$laatste_seizoen=$db->f("seizoen_id");
	}

	# Vertrekinfo-tracking
	$vertrekinfo_tracking=vertrekinfo_tracking("skipas",array("vertrekinfo_skipas"),$_GET["10k0"],$laatste_seizoen);
}

#echo wt_dump($vertrekinfo_tracking);

$cms->settings[10]["list"]["show_icon"]=true;
$cms->settings[10]["list"]["edit_icon"]=true;
$cms->settings[10]["list"]["delete_icon"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(10,"text","naam");
$cms->db_field(10,"text","leverancierscode");
$cms->db_field(10,"select","optieleverancier_id","",array("othertable"=>"24","otherkeyfield"=>"optieleverancier_id","otherfield"=>"naam"));
$cms->db_field(10,"text","naam_voorkant");
$cms->db_field(10,"textarea","website_omschrijving");
if($vars["cmstaal"]) $cms->db_field(10,"textarea","website_omschrijving_".$vars["cmstaal"]);
$cms->db_field(10,"text","wederverkoop_naam");
if($vars["cmstaal"]) $cms->db_field(10,"text","wederverkoop_naam_".$vars["cmstaal"]);
#$cms->db_field(10,"float","wederverkoop_commissie_agent");
$cms->db_field(10,"text","naam_voucher");
if($vars["cmstaal"]) $cms->db_field(10,"text","naam_voucher_".$vars["cmstaal"]);
$cms->db_field(10,"text","omschrijving_voucher");
$cms->db_field(10,"textarea","aanvullend_voucher");
if($vars["cmstaal"]) $cms->db_field(10,"textarea","aanvullend_voucher_".$vars["cmstaal"]);
$cms->db_field(10,"text","tekstbegin_voucher");
$cms->db_field(10,"text","teksteind_voucher");
$cms->db_field(10,"integer","aantaldagen");
$cms->db_field(10,"select","begindag","",array("selection"=>$vars["begineinddagen"]));
$cms->db_field(10,"select","einddag","",array("selection"=>$vars["begineinddagen"]));
$cms->db_field(10,"text","contactpersoon");
$cms->db_field(10,"text","telefoonnummer");
$cms->db_field(10,"text","faxnummer");
$cms->db_field(10,"text","noodnummer");
$cms->db_field(10,"picture","voucherlogo","",array("savelocation"=>"pic/cms/voucherlogo_skipas/","filetype"=>"jpg"));
$cms->db_field(10,"select","bijkomendekosten_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=2"));
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->db_field(10,"select","optietarieven_controleren_in_cms_".$key,"skipas_id",array("selection"=>$sz_controle_array[$key]));
	}
}

# Nieuw vertrekinfo-systeem
$cms->db_field(10,"checkbox","vertrekinfo_goedgekeurd_seizoen","",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(10,"text","vertrekinfo_goedgekeurd_datetime");
$cms->db_field(10,"textarea","vertrekinfo_skipas");


# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(10,"naam","Naam");
if($sz_controle) {
	reset($sz_controle);
	while(list($key,$value)=each($sz_controle)) {
		$cms->list_field(10,"optietarieven_controleren_in_cms_".$key,$value);
	}
}

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(10,1,"naam");
$cms->edit_field(10,1,"naam_voorkant","Naam voor de klant");
$cms->edit_field(10,0,"optieleverancier_id","Leverancier (i.v.m. losse skipassen)");
#$cms->edit_field(10,0,"leverancierscode","Bestellijstcode");
$cms->edit_field(10,0,"contactpersoon","Contactpersoon");
$cms->edit_field(10,0,"telefoonnummer","Telefoonnummer");
$cms->edit_field(10,0,"faxnummer","Faxnummer");
$cms->edit_field(10,0,"noodnummer","Noodnummer");
$cms->edit_field(10,0,"bijkomendekosten_id","Bijkomende kosten");
$cms->edit_field(10,0,"htmlrow","<hr><b>Website</b>");

if($vars["cmstaal"]) {
	$cms->edit_field(10,0,"website_omschrijving","Inclusieftekst accommodatiepagina","",array("noedit"=>true));
	$cms->edit_field(10,1,"website_omschrijving_".$vars["cmstaal"],"Inclusieftekst accommodatiepagina ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(10,1,"website_omschrijving","Inclusieftekst accommodatiepagina");
}
$cms->edit_field(10,1,"aantaldagen","Aantal dagen standaard-skipas",array("text"=>6));

$cms->edit_field(10,0,"htmlrow","<hr><b>Wederverkoop</b>");
if($vars["cmstaal"]) {
	$cms->edit_field(10,0,"wederverkoop_naam","Naam voor de klant bij wederverkoop","",array("noedit"=>true));
	$cms->edit_field(10,0,"wederverkoop_naam_".$vars["cmstaal"],"Naam voor de klant bij wederverkoop ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(10,0,"wederverkoop_naam","Naam voor de klant bij wederverkoop");
}
#$cms->edit_field(10,0,"wederverkoop_commissie_agent","Wederverkoop-commissie %");
$cms->edit_field(10,0,"htmlrow","<hr><b>Voucher</b>");
if($vars["cmstaal"]) {
	$cms->edit_field(10,1,"naam_voucher","Naam van de voucher NL","",array("noedit"=>true));
	$cms->edit_field(10,1,"naam_voucher_".$vars["cmstaal"],"Naam van de voucher ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(10,1,"naam_voucher","Naam van de voucher");
}
$cms->edit_field(10,1,"omschrijving_voucher","Omschrijving op de voucher");
if($vars["cmstaal"]) {
	$cms->edit_field(10,0,"aanvullend_voucher","Let-op-tekst op de voucher NL","",array("noedit"=>true));
	$cms->edit_field(10,0,"aanvullend_voucher_".$vars["cmstaal"],"Let-op-tekst op de voucher ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(10,0,"aanvullend_voucher","Let-op-tekst op de voucher");
}
$cms->edit_field(10,0,"tekstbegin_voucher","Extra tekst eerste dag");
$cms->edit_field(10,0,"teksteind_voucher","Extra tekst laatste dag");
$cms->edit_field(10,0,"begindag","Datumaanpassing begin");
$cms->edit_field(10,0,"einddag","Datumaanpassing eind");
$cms->edit_field(10,0,"voucherlogo","Voucherlogo","",array("img_width"=>"600","img_height"=>"600"));
$cms->edit_field(10,0,"htmlrow","<hr><br><b>Nieuw vertrekinfo-systeem (nog niet in gebruik, maar gegevens invoeren is al mogelijk)</b>");
$cms->edit_field(10,0,"htmlrow","<br><i>Alinea 'Skipas'</i>");
$cms->edit_field(10,0,"vertrekinfo_skipas","Tekst");
if($vertrekinfo_tracking["vertrekinfo_skipas"]) {
	$cms->edit_field(10,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_skipas"]))."</div>"));
}
$cms->edit_field(10,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo</b>");
$cms->edit_field(10,0,"vertrekinfo_goedgekeurd_seizoen","Vertrekinfo is goedgekeurd voor seizoen","","",array("one_per_line"=>true));
$cms->edit_field(10,0,"vertrekinfo_goedgekeurd_datetime","Laatste goedkeuring","","",array("one_per_line"=>true));

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(10);
if($cms_form[10]->filled) {
	# Controle of juiste taal wel actief is
	if(!$vars["cmstaal"]) {
		while(list($key,$value)=each($_POST["input"])) {
			if(ereg("^website_omschrijving_",$key)) {
				$cms_form[10]->error("taalprobleem","De CMS-taal is gewijzigd tijdens het bewerken. Opslaan is niet mogelijk. Ga terug naar het CMS-hoofdmenu en kies de gewenste taal",false,true);
			}
		}
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[10]="skipasgegevens";
$cms->show_mainfield[10]="naam";
$cms->show_field(10,"naam","Naam");

# Controle op delete-opdracht
if($_GET["delete"]==10 and $_GET["10k0"]) {
	$db->query("SELECT accommodatie_id FROM accommodatie WHERE skipas_id='".addslashes($_GET["10k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(10,"Er zijn nog <a href=\"cms_accommodaties.php?wzt=1&1where=".urlencode("skipas_id=".$_GET["10k0"])."\">accommodaties</a> gekoppeld");
	}
}

#function form_before_goto($form) {
#	#
#	# Kijken of wederverkoop_commissie_agent gewijzigd is
#	#
#	$db=new DB_sql;
#	$nieuw_percentage=$form->checked_input["wederverkoop_commissie_agent"];
#
#	if($_GET["10k0"] and $nieuw_percentage and $nieuw_percentage<>$form->fields["previous"]["wederverkoop_commissie_agent"]["text"]) {
#		# Wederverkoop-percentage skipasuitbreidingen wijzigen
#		$db->query("UPDATE optie_tarief SET wederverkoop_commissie_agent='".addslashes($nieuw_percentage)."' WHERE week>'".(time())."' AND optie_onderdeel_id IN (SELECT DISTINCT optie_onderdeel_id FROM view_optie WHERE skipas_id='".addslashes($_GET["10k0"])."');");
#
#		# Wederverkoop-percentage wederverkoop-skipassen wijzigen
#		$db->query("UPDATE optie_tarief SET wederverkoop_commissie_agent='".addslashes($nieuw_percentage)."' WHERE week>'".(time())."' AND wederverkoop_skipas_id='".addslashes($_GET["10k0"])."';");
#	}
#}

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>