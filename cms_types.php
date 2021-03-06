<?php

if(!$_GET) {
	if($_SERVER["HTTP_REFERER"]) {
#		mail("jeroen@webtastic.nl","Lege cms_types.php Chalet",$_SERVER["REQUEST_URI"]."\n\nReferer: ".$_SERVER["HTTP_REFERER"]);
	}
	header("Location: /cms.php");
	exit;
}

$mustlogin=true;

include("admin/vars.php");

if($_GET["copy_type"] and $_GET["confirmed"]) {

	// copy accommodation

	$copydatabaserecord = new copydatabaserecord;
	$copydatabaserecord->copy_type($_GET["2k0"]);

	$_SESSION["wt_popupmsg"]="type correct gekopieerd";

	$url=ereg_replace("&copy_type=1","",$_SERVER["REQUEST_URI"]);
	$url=ereg_replace("&confirmed=1","",$url);
	$url=ereg_replace("&2k0=([0-9]+)","&2k0=".$copydatabaserecord->new_type_id,$url);
	header("Location: ".$url);
	exit;
}

if(!$_GET["1k0"] and $_GET["2k0"]) {
	$db->query("SELECT accommodatie_id FROM type WHERE type_id='".addslashes($_GET["2k0"])."';");
	if($db->next_record()) {
		$_GET["1k0"]=$db->f("accommodatie_id");
	}
}

if(!$_GET["wzt"]) {
	if($_GET["1k0"]) {
		$db->query("SELECT wzt FROM accommodatie WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
		if($db->next_record()) {
			$_GET["wzt"]=$db->f("wzt");
		}
	} else {
		$_GET["wzt"]=1;
	}
}

if($_GET["1k0"] and $_GET["add"]==2) {
	$db->query("SELECT leverancier_id, beheerder_id, websites FROM type WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
	if($db->next_record()) {
		$accommodatiegegevens["leverancier_id"]=$db->f("leverancier_id");
		$accommodatiegegevens["beheerder_id"]=$db->f("beheerder_id");
		$accommodatiegegevens["websites"]=$db->f("websites");
	}
}

if($_GET["2k0"]) {

	# korting wissen
	if($_GET["delkid"]) {
		$db->query("SELECT gekoppeld_code FROM korting WHERE korting_id='".addslashes($_GET["delkid"])."' AND gekoppeld_code>0;");
		if($db->next_record()) {
			$db2->query("SELECT type_id, korting_id FROM korting WHERE gekoppeld_code='".$db->f("gekoppeld_code")."';");
			while($db2->next_record()) {
				if($typeid_berekenen_inquery) $typeid_berekenen_inquery.=",".$db2->f("type_id"); else $typeid_berekenen_inquery=$db2->f("type_id");
				$db3->query("DELETE FROM korting_tarief WHERE korting_id='".addslashes($db2->f("korting_id"))."';");
				$db3->query("DELETE FROM korting WHERE korting_id='".addslashes($db2->f("korting_id"))."';");
			}
		} else {
			$db2->query("SELECT type_id FROM korting WHERE korting_id='".addslashes($_GET["delkid"])."';");
#			echo $db2->lastquery."<br>";
			if($db2->next_record()) {
				$typeid_berekenen_inquery=$db2->f("type_id");
			}
			$db3->query("DELETE FROM korting_tarief WHERE korting_id='".addslashes($_GET["delkid"])."';");
#			echo $db3->lastquery."<br>";
			$db3->query("DELETE FROM korting WHERE korting_id='".addslashes($_GET["delkid"])."';");
#			echo $db3->lastquery."<br>";
		}

		# Tarieven doorrekenen na wissen kortingen
		if($typeid_berekenen_inquery) {
#			echo $typeid_berekenen_inquery;
			include("cron/tarieven_berekenen.php");
		}
		header("Location: cms_types.php?".wt_stripget($_GET,array("delkid","confirmed")));
		exit;
	}

	$vars["temp_error_geen_tarieven"]=true; # bij geen tarieven: geen error tonen
	$vars["accinfo"]=accinfo($_GET["2k0"]);

	# Verzameltype-parents laden
	if($vars["accinfo"]["toonper"]==1) {
#		$extrawhere=" AND maxaantalpersonen='".$vars["accinfo"]["maxaantalpersonen"]."'";
	} else {
		$extrawhere="";
	}
	$db->query("SELECT begincode, type_id, naam, tnaam, plaats, optimaalaantalpersonen, maxaantalpersonen FROM view_accommodatie WHERE verzameltype=1 AND plaats_id='".$vars["accinfo"]["plaats_id"]."' AND toonper='".$vars["accinfo"]["toonper"]."'".$extrawhere.";");
	while($db->next_record()) {
		$vars["verzameltype_parent"][$db->f("type_id")]=$db->f("plaats")." - ".$db->f("begincode").$db->f("type_id")." ".$db->f("naam")." ".($db->f("tnaam") ? $db->f("tnaam")." " : "")."(".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." p)";
	}


	# Seizoenen laden t.b.v. vertrekinfo_seizoengoedgekeurd
	$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE type='".addslashes($_GET["wzt"])."' AND UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY type, begin, eind;");
	while($db->next_record()) {
		$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
		$laatste_seizoen=$db->f("seizoen_id");
	}

	# Vertrekinfo-tracking
	$vertrekinfo_tracking_array=array("inclusief", "exclusief" ,"vertrekinfo_incheck_sjabloon_id", "vertrekinfo_soortbeheer", "vertrekinfo_soortbeheer_aanvulling", "vertrekinfo_telefoonnummer", "vertrekinfo_inchecktijd", "vertrekinfo_uiterlijkeinchecktijd", "vertrekinfo_uitchecktijd", "vertrekinfo_inclusief", "vertrekinfo_exclusief", "vertrekinfo_route", "vertrekinfo_soortadres", "vertrekinfo_adres", "vertrekinfo_plaatsnaam_beheer", "vertrekinfo_gps_lat", "vertrekinfo_gps_long");
	if($vars["cmstaal"]) {
		$vertrekinfo_tracking_array[]="vertrekinfo_inclusief_".$vars["cmstaal"];
		$vertrekinfo_tracking_array[]="vertrekinfo_exclusief_".$vars["cmstaal"];
		$vertrekinfo_tracking_array[]="vertrekinfo_route_".$vars["cmstaal"];
		$vertrekinfo_tracking_array[]="vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"];
	}
	$vertrekinfo_tracking=vertrekinfo_tracking("type",$vertrekinfo_tracking_array,$_GET["2k0"],$laatste_seizoen);
}

$cms->settings[2]["list"]["show_icon"]=false;
$cms->settings[2]["list"]["edit_icon"]=true;
$cms->settings[2]["list"]["delete_icon"]=true;

$cms->settings[2]["show"]["goto_new_record"]=false;
$cms->settings[2]["edit"]["top_submit_button"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(2,"noedit","type_id");
$cms->db_field(2,"text","naam");
if($vars["cmstaal"]) $cms->db_field(2,"text","naam_".$vars["cmstaal"]);
#$cms->db_field(2,"text","bestelnaam");
$cms->db_field(2,"text","korteomschrijving");
if($vars["cmstaal"]) $cms->db_field(2,"text","korteomschrijving_".$vars["cmstaal"]);
$cms->db_field(2,"text","altnaam");
$cms->db_field(2,"text","altnaam_zichtbaar");
$cms->db_field(2,"integer","voorraad");

# inactieve sites uitzetten
while(list($key,$value)=each($vars["websites_inactief"])) {
	unset($vars["websites_wzt"][$_GET["wzt"]][$key]);
}
$cms->db_field(2,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(2,"select","leverancier_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));
$cms->db_field(2,"select","leverancier_sub_id","",array("othertable"=>"42","otherkeyfield"=>"leverancier_sub_id","otherfield"=>"naam"));
$cms->db_field(2,"select","beheerder_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=1"));
$cms->db_field(2,"select","eigenaar_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));
$cms->db_field(2,"yesno","verzameltype");
$cms->db_field(2,"yesno","apart_tonen_in_zoekresultaten");
$cms->db_field(2,"select","verzameltype_parent","",array("selection"=>$vars["verzameltype_parent"]));
$cms->db_field(2,"textarea","aantekeningen","",array("dontlog"=>true));
$cms->db_field(2,"text","code");
$cms->db_field(2,"text","leverancierscode");
$cms->db_field(2,"text","leverancierscode_negeertarief");
$cms->db_field(2,"yesno","xmltarievenimport");
$cms->db_field(2,"url","url_leverancier");
$cms->db_field(2,"yesno","tonen");
$cms->db_field(2,"yesno","tonenzoekformulier");
$cms->db_field(2,"yesno","request_translation");
$cms->db_field(2,"yesno","request_translation_en");
$cms->db_field(2,"yesno","request_translation_de");
$cms->db_field(2,"yesno","is_maintype_canonical");
$cms->db_field(2,"yesno","controleren");
$cms->db_field(2,"yesno","onderverdeeld_in_nummers");
#$cms->db_field(2,"yesno","shortlist");
$cms->db_field(2,"select","zoekvolgorde","",array("selection"=>$vars["zoekvolgorde"]));
$cms->db_field(2,"url","url_virtuele_rondgang");
#$cms->db_field(2,"checkbox","kenmerken","",array("selection"=>$vars["kenmerken_type_".$_GET["wzt"]]));
$cms->db_field(2,"multiradio","kenmerken","",array("selection"=>$vars["kenmerken_type_".$_GET["wzt"]],"multiselection"=>array(1=>"ja",2=>"nee",3=>"onbekend",4=>"niet relevant"),"multiselectionfields"=>array(1=>"kenmerken",2=>"kenmerken_nee",3=>"kenmerken_onbekend",4=>"kenmerken_irrelevant")));
$cms->db_field(2,"textarea","omschrijving");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","omschrijving_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","tip_specialist");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","tip_specialist_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","praktische_info");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","praktische_info_".$vars["cmstaal"]);
$cms->db_field(2,"select","kwaliteit","",array("selection"=>$vars["kwaliteit"]));
$cms->db_field(2,"integer","optimaalaantalpersonen");
$cms->db_field(2,"integer","maxaantalpersonen");
$cms->db_field(2,"integer","aangepaste_min_tonen");
$cms->db_field(2,"integer","oppervlakte");
$cms->db_field(2,"select","bijkomendekosten1_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(2,"select","bijkomendekosten2_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(2,"select","bijkomendekosten3_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(2,"select","bijkomendekosten4_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(2,"select","bijkomendekosten5_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(2,"select","bijkomendekosten6_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(2,"text","oppervlakteextra");
if($vars["cmstaal"]) $cms->db_field(2,"text","oppervlakteextra_".$vars["cmstaal"]);
$cms->db_field(2,"integer","slaapkamers");
$cms->db_field(2,"text","slaapkamersextra");
if($vars["cmstaal"]) $cms->db_field(2,"text","slaapkamersextra_".$vars["cmstaal"]);
$cms->db_field(2,"integer","badkamers");
$cms->db_field(2,"text","badkamersextra");
if($vars["cmstaal"]) $cms->db_field(2,"text","badkamersextra_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","indeling");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","indeling_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","inclusief");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","inclusief_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","exclusief");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","exclusief_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","voucherinfo");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","voucherinfo_".$vars["cmstaal"]);
$cms->db_field(2,"integer","oude_accommodatie_id");
$cms->db_field(2,"text","doorsturen_naar_type_id");
if($_GET["wzt"]==2 and $_GET["edit"]==2) {
	$db->query("SELECT p.land_id FROM plaats p, accommodatie a, type t WHERE a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($_GET["2k0"])."';");
	if($db->next_record()) {
		$landid=$db->f("land_id");
	} else {
		$landid=0;
	}
	unset($inquery);
	$db->query("SELECT zomerwinterkoppeling_accommodatie_id FROM type WHERE zomerwinterkoppeling_accommodatie_id<>0 AND zomerwinterkoppeling_accommodatie_id IS NOT NULL AND type_id<>'".addslashes($_GET["2k0"])."';");
	while($db->next_record()) {
		if($inquery) $inquery.=",".$db->f("zomerwinterkoppeling_accommodatie_id"); else $inquery=$db->f("zomerwinterkoppeling_accommodatie_id");
	}
	if(!$inquery) $inquery=0;
	$db->query("SELECT t.type_id, p.naam AS plaats, a.naam, t.naam AS tnaam, t.optimaalaantalpersonen, t.maxaantalpersonen, l.begincode FROM accommodatie a, type t, plaats p, land l WHERE t.type_id NOT IN(".$inquery.") AND t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND l.land_id='".addslashes($landid)."' AND a.wzt=1 ORDER BY p.naam, a.naam, t.naam, t.optimaalaantalpersonen, t.maxaantalpersonen;");
	while($db->next_record()) {
		$wzt_koppeling[$db->f("type_id")]=$db->f("plaats")." - ".substr($db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : ""),0,30)." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p - ".$db->f("begincode").$db->f("type_id").")";
	}
	$cms->db_field(2,"select","zomerwinterkoppeling_accommodatie_id","",array("selection"=>$wzt_koppeling));
	$cms->db_field(2,"yesno","geenzomerwinterkoppeling");
}

if($_GET["edit"]==2) {
	$db->query("SELECT v.begincode, v.type_id, v.naam, v.tnaam, v.optimaalaantalpersonen, v.maxaantalpersonen FROM view_accommodatie v WHERE v.wzt='".intval($_GET["wzt"])."' AND v.plaats_id=(SELECT plaats_id FROM view_accommodatie WHERE type_id='".intval($_GET["2k0"])."') AND type_id<>'".intval($_GET["2k0"])."' ORDER BY v.naam, v.tnaam, v.optimaalaantalpersonen, v.type_id;");
	while($db->next_record()) {
		$voorraad_gekoppeld_type_id[$db->f("type_id")]=$db->f("begincode").$db->f("type_id")." - ".trim($db->f("naam")." ".$db->f("tnaam"))." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p)";
	}
}
$cms->db_field(2,"select","voorraad_gekoppeld_type_id","",array("selection"=>$voorraad_gekoppeld_type_id));

$cms->db_field(2,"picture","picgroot","",array("savelocation"=>"pic/cms/types_specifiek/","filetype"=>"jpg"));
$cms->db_field(2,"picture","hoofdfoto_type","",array("savelocation"=>"pic/cms/hoofdfoto_type/","filetype"=>"jpg"));
$cms->db_field(2,"picture","picklein","",array("savelocation"=>"pic/cms/types_specifiek_tn/","filetype"=>"jpg"));
$cms->db_field(2,"picture","picaanvullend","",array("savelocation"=>"pic/cms/types/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(2,"picture","picaanvullend_breed","",array("savelocation"=>"pic/cms/types_breed/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(2,"text","homeaway_code");
$cms->db_field(2,"text","gps_lat");
$cms->db_field(2,"text","gps_long");

# Nieuw vertrekinfo-systeem
$cms->db_field(2,"checkbox","vertrekinfo_goedgekeurd_seizoen","",array("selection"=>$vars["seizoengoedgekeurd"]));
if($vars["cmstaal"]) $cms->db_field(2,"checkbox","vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(2,"text","vertrekinfo_goedgekeurd_datetime");
if($vars["cmstaal"]) $cms->db_field(2,"text","vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"]);
$cms->db_field(2,"select","vertrekinfo_incheck_sjabloon_id","",array("othertable"=>"54","otherkeyfield"=>"vertrekinfo_sjabloon_id","otherfield"=>"naam","otherwhere"=>"soort=1"));
$cms->db_field(2,"select","vertrekinfo_soortbeheer","",array("selection"=>$vars["vertrekinfo_soortbeheer"]));
$cms->db_field(2,"text","vertrekinfo_soortbeheer_aanvulling");
if($vars["cmstaal"]) $cms->db_field(2,"text","vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]);
$cms->db_field(2,"text","vertrekinfo_telefoonnummer");
$cms->db_field(2,"text","vertrekinfo_inchecktijd");
$cms->db_field(2,"text","vertrekinfo_uiterlijkeinchecktijd");
$cms->db_field(2,"text","vertrekinfo_uitchecktijd");
$cms->db_field(2,"textarea","vertrekinfo_inclusief");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","vertrekinfo_inclusief_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","vertrekinfo_exclusief");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","vertrekinfo_exclusief_".$vars["cmstaal"]);
$cms->db_field(2,"textarea","vertrekinfo_route");
if($vars["cmstaal"]) $cms->db_field(2,"textarea","vertrekinfo_route_".$vars["cmstaal"]);
$cms->db_field(2,"select","vertrekinfo_soortadres","",array("selection"=>$vars["vertrekinfo_soortadres"]));
$cms->db_field(2,"textarea","vertrekinfo_adres");
$cms->db_field(2,"text","vertrekinfo_plaatsnaam_beheer");
$cms->db_field(2,"text","vertrekinfo_gps_lat");
$cms->db_field(2,"text","vertrekinfo_gps_long");

# Video
$cms->db_field(2,"yesno","video");
$cms->db_field(2,"url","video_url");


$cms->db[2]["set"]="accommodatie_id='".addslashes($_GET["1k0"])."'";

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[2]=array("naam","naam");
$cms->list_field(2,"naam","Naam");

if($vars["cmstaal"]) {
	$db->query("SELECT toonper, omschrijving, omschrijving_".$vars["cmstaal"].", indeling, indeling_".$vars["cmstaal"].", inclusief, inclusief_".$vars["cmstaal"].", exclusief, exclusief_".$vars["cmstaal"].", tip_specialist, tip_specialist_".$vars["cmstaal"].", praktische_info, praktische_info_".$vars["cmstaal"].", voucherinfo, voucherinfo_".$vars["cmstaal"]." FROM accommodatie WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
} else {
	$db->query("SELECT toonper, omschrijving, indeling, inclusief, exclusief, tip_specialist, praktische_info, voucherinfo FROM accommodatie WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
}

if($db->next_record()) {
	$temp["toonper"]=$db->f("toonper");
	$temp["omschrijving"]=$db->f("omschrijving");
	$temp["omschrijving_".$vars["cmstaal"]]=$db->f("omschrijving_".$vars["cmstaal"]);
	$temp["indeling"]=$db->f("indeling");
	$temp["indeling_".$vars["cmstaal"]]=$db->f("indeling_".$vars["cmstaal"]);
	$temp["inclusief"]=$db->f("inclusief");
	$temp["inclusief_".$vars["cmstaal"]]=$db->f("inclusief_".$vars["cmstaal"]);
	$temp["exclusief"]=$db->f("exclusief");
	$temp["exclusief_".$vars["cmstaal"]]=$db->f("exclusief_".$vars["cmstaal"]);
	$temp["tip_specialist"]=$db->f("tip_specialist");
	$temp["tip_specialist_".$vars["cmstaal"]]=$db->f("tip_specialist_".$vars["cmstaal"]);
	$temp["praktische_info"]=$db->f("praktische_info");
	$temp["praktische_info_".$vars["cmstaal"]]=$db->f("praktische_info_".$vars["cmstaal"]);
	$temp["voucherinfo"]=$db->f("voucherinfo");
	$temp["voucherinfo_".$vars["cmstaal"]]=$db->f("voucherinfo_".$vars["cmstaal"]);
}

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(2,0,"controleren","Nog nakijken");
$cms->edit_field(2,0,"tonen","Tonen op de website",array("selection"=>true));
$cms->edit_field(2,0,"tonenzoekformulier","Tonen in de zoekresultaten",array("selection"=>true));
$cms->edit_field(2,0,"verzameltype","Dit is een verzameltype");
$cms->edit_field(2,0,"verzameltype_parent","Dit type valt onder het volgende verzameltype");
$cms->edit_field(2,0,"apart_tonen_in_zoekresultaten","Toon dit type bij de zoekresultaten als losse accommodatie (niet als onderdeel van de bovenliggende accommodatie)");
$cms->edit_field(2,0,"htmlrow","<hr>");
$cms->edit_field(2,0,"htmlrow","Opnemen in lijst<a href=\"".$vars["path"]."cms_overzichten_overig.php?t=3&wzt=".intval($_GET["wzt"])."&vertaalsysteem&request_translation=1\" target=\"_blank\" style=\"padding:5px;\">nieuw te vertalen accommodaties/types</a>",array("selection"=>false),"",array("title_html"=>true));
$cms->edit_field(2,0,"request_translation_en","EN",array("selection"=>false),"",array("title_html"=>true));
$cms->edit_field(2,0,"request_translation_de","DE",array("selection"=>false),"",array("title_html"=>true));
$cms->edit_field(2,0,"htmlrow","<hr>");
$cms->edit_field(2,0,"websites","Websites",array("selection"=>$accommodatiegegevens["websites"]),"",array("one_per_line"=>true));
if($_GET["edit"]==2) {
	$cms->edit_field(2,0,"htmlrow","<hr>");
}
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"naam","Naam NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"naam_".$vars["cmstaal"],"Naam ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,0,"naam","Naam op de website");
}
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"korteomschrijving","Korte omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"korteomschrijving_".$vars["cmstaal"],"Korte omschrijving ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,0,"korteomschrijving","Korte omschrijving","","",array("info"=>"Korte/krachtige omschrijving van de accommodatie in 1 zin voor op de accommodatiepagina (voor bezoekers en zoekmachines). Is ook in te voeren op accommodatieniveau (de hier ingevoerde type-tekst overschijft de accommodatie-tekst)."));
}
$cms->edit_field(2,0,"altnaam","Zoekwoorden (intern zoekformulier)");
$cms->edit_field(2,0,"altnaam_zichtbaar","Alternatieve spelling / trefwoorden (Google)");
$cms->edit_field(2,1,"leverancier_id","Leverancier",array("selection"=>$accommodatiegegevens["leverancier_id"]));
$cms->edit_field(2,0,"leverancier_sub_id","Sub-leverancier");
$cms->edit_field(2,0,"beheerder_id","Beheerder",array("selection"=>$accommodatiegegevens["beheerder_id"]));
$cms->edit_field(2,0,"eigenaar_id","Eigenaar");
$cms->edit_field(2,0,"aantekeningen","Aantekeningen (intern)","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
$cms->edit_field(2,0,"code","Code");
$cms->edit_field(2,0,"url_leverancier","Directe link bij leverancier");
$cms->edit_field(2,0,"is_maintype_canonical","Canonical-tag (voor Google)", "", "", array("info"=>"Om te voorkomen dat ��n accommodatie meerdere keren in Google terecht komt en dat de linkwaarde over meerdere types verdeeld wordt, kun je met behulp van deze checkbox ��n type van de accommodatie aanwijzen welke we in de zoekresultaten van Google willen tonen. Dit gebeurt met een canonical-tag die verwijst naar het hier gekozen hoofdtype."));
#$cms->edit_field(2,0,"onderverdeeld_in_nummers","Dit type is onderverdeeld in nummers");
$cms->edit_field(2,0,"htmlrow","<hr><b>XML-import (beschikbaarheid/tarieven)</b><p><i>In geval van meerdere codes: scheiden door komma</i>");
$cms->edit_field(2,0,"leverancierscode","Leverancierscode type");
$cms->edit_field(2,0,"leverancierscode_negeertarief","Leverancierscodes die niet moeten worden opgeteld bij de brutoprijs");
$cms->edit_field(2,0,"xmltarievenimport","Tarieven importeren via XML (indien beschikbaar)",array("selection"=>true));
if($_GET["wzt"]==2 and $_GET["edit"]==2) {
	$cms->edit_field(2,0,"htmlrow","<hr>");
	$cms->edit_field(2,0,"zomerwinterkoppeling_accommodatie_id","Zelfde accommodatie in winterprogramma");
	$cms->edit_field(2,0,"geenzomerwinterkoppeling","Deze accommodatie is niet beschikbaar in het winterprogramma");
}


// kijken of dit type voorraad-houder is voor andere types
$db->query("SELECT t.type_id, v.accommodatie_id, v.begincode, v.naam, v.tnaam, v.optimaalaantalpersonen, v.maxaantalpersonen FROM type t, view_accommodatie v WHERE v.type_id=t.type_id AND t.voorraad_gekoppeld_type_id='".intval($_GET["2k0"])."';");
if($db->num_rows()) {

	while($db->next_record()) {
		$gekoppelde_types_html.="<li><a href=\"".$vars["path"]."cms_types.php?show=2&wzt=".intval($_GET["wzt"])."&1k0=".$db->f("accommodatie_id")."&2k0=".$db->f("type_id")."\" target=\"_blank\">".$db->f("begincode").$db->f("type_id")." - ".trim($db->f("naam")." ".$db->f("tnaam"))." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p)</a></li>";
	}
	$cms->edit_field(2,0,"htmlrow","<hr><b>Voorraad overnemen van ander type</b><p><i>Dit type is voorraad-houder van:</i><ul>".$gekoppelde_types_html."</ul>");
} else {
	$cms->edit_field(2,0,"htmlrow","<hr><b>Voorraad overnemen van ander type</b><p><i>Vul hier een type in om de voorraad van dat type automatisch over te nemen. Er is in dat geval geen eigen voorraad meer van toepassing op dit type.</i>");
	$cms->edit_field(2,0,"voorraad_gekoppeld_type_id","Voorraad-houder");
}

$cms->edit_field(2,0,"htmlrow","<hr>");
$cms->edit_field(2,1,"optimaalaantalpersonen","Capaciteit");
$cms->edit_field(2,1,"maxaantalpersonen","Maximale capaciteit");
$cms->edit_field(2,0,"doorsturen_naar_type_id","Indien \"niet tonen\": doorsturen naar type");
$cms->edit_field(2,0,"bijkomendekosten1_id","Bijkomende kosten 1");
$cms->edit_field(2,0,"bijkomendekosten2_id","Bijkomende kosten 2");
$cms->edit_field(2,0,"bijkomendekosten3_id","Bijkomende kosten 3");
$cms->edit_field(2,0,"bijkomendekosten4_id","Bijkomende kosten 4");
$cms->edit_field(2,0,"bijkomendekosten5_id","Bijkomende kosten 5");
$cms->edit_field(2,0,"bijkomendekosten6_id","Bijkomende kosten 6");
$cms->edit_field(2,1,"zoekvolgorde","Zoekvolgorde",array("selection"=>3));
$cms->edit_field(2,0,"url_virtuele_rondgang","URL virtuele rondgang");
$cms->edit_field(2,0,"gps_lat","GPS latitude","","",array("info"=>"Vul de breedtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 52.086508"));
$cms->edit_field(2,0,"gps_long","GPS longitude","","",array("info"=>"Vul de lengtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 4.886513"));
$cms->edit_field(2,0,"homeaway_code","HomeAway-code","","",array("info"=>"Wordt gebruik om een XML-feed voor jullie partner HomeAway op te stellen. Types waarbij deze code is ingevuld worden vanzelf in de feed opgenomen."));
$cms->edit_field(2,0,"htmlrow","<hr>");
$cms->edit_field(2,1,"kenmerken","Kenmerken");
if($temp["toonper"]<>3) {
	$cms->edit_field(2,0,"htmlrow","<hr>");
	$cms->edit_field(2,0,"aangepaste_min_tonen","Aangepast min. aantal personen","","",array("title_html"=>true));
	$cms->edit_field(2,0,"htmlrow","<font size=1>Toelichting: wijzigt bij grote accommodaties het minimum aantal personen dat mensen kunnen selecteren bij het boeken van deze accommodatie.<p><b>Let op! Na het wijzigen van deze waarde moet voor alle actieve seizoenen de tarievenmodule worden geopend en opgeslagen (zodat voor het lagere aantal personen de tarieven worden berekend en opgeslagen).</b></font>");
}
$cms->edit_field(2,1,"htmlrow","<hr>");
if($vars["cmstaal"]) {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-omschrijving NL",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["omschrijving"]))."</span>"));
	$cms->edit_field(2,0,"omschrijving","Type-omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(2,1,"htmlcol","Accommodatie-omschrijving ".strtoupper($vars["cmstaal"]),array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["omschrijving_".$vars["cmstaal"]]))."</span>"));
	$cms->edit_field(2,0,"omschrijving_".$vars["cmstaal"],"Type-omschrijving ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-omschrijving",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["omschrijving"]))."</span>"));
	$cms->edit_field(2,0,"omschrijving","Type-omschrijving");
}
$cms->edit_field(2,1,"htmlrow","<hr>");
if($vars["cmstaal"]) {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-tip van de specialist NL",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["tip_specialist"]))."</span>"));
	$cms->edit_field(2,0,"tip_specialist","Type-tip van de specialist NL","",array("noedit"=>true));
	$cms->edit_field(2,1,"htmlcol","Accommodatie-tip van de specialist ".strtoupper($vars["cmstaal"]),array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["tip_specialist_".$vars["cmstaal"]]))."</span>"));
	$cms->edit_field(2,0,"tip_specialist_".$vars["cmstaal"],"Type-tip van de specialist ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-tip van de specialist",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["tip_specialist"]))."</span>"));
	$cms->edit_field(2,0,"tip_specialist","Type-tip van de specialist");
}
$cms->edit_field(2,1,"htmlrow","<hr>");
if($vars["cmstaal"]) {
	$cms->edit_field(2,1,"htmlcol","Accommodatie praktische info NL",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["praktische_info"]))."</span>"));
	$cms->edit_field(2,0,"praktische_info","Type praktische info NL","",array("noedit"=>true));
	$cms->edit_field(2,1,"htmlcol","Accommodatie praktische info ".strtoupper($vars["cmstaal"]),array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["praktische_info_".$vars["cmstaal"]]))."</span>"));
	$cms->edit_field(2,0,"praktische_info_".$vars["cmstaal"],"Type praktische info ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,1,"htmlcol","Accommodatie praktische info",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["praktische_info"]))."</span>"));
	$cms->edit_field(2,0,"praktische_info","Type praktische info");
}
$cms->edit_field(2,1,"htmlrow","<hr>");
$cms->edit_field(2,0,"kwaliteit","Kwaliteit");
$cms->edit_field(2,0,"oppervlakte","Oppervlakte in m�");

if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"oppervlakteextra","Toevoeging oppervlakte NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"oppervlakteextra_".$vars["cmstaal"],"Toevoeging oppervlakte ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,0,"oppervlakteextra","Toevoeging oppervlakte");
}

$cms->edit_field(2,0,"slaapkamers","Aantal slaapkamers");
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"slaapkamersextra","Toevoeging slaapkamers NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"slaapkamersextra_".$vars["cmstaal"],"Toevoeging slaapkamers ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,0,"slaapkamersextra","Toevoeging slaapkamers");
}

$cms->edit_field(2,0,"badkamers","Aantal badkamers");
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"badkamersextra","Toevoeging badkamers NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"badkamersextra_".$vars["cmstaal"],"Toevoeging badkamers ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,0,"badkamersextra","Toevoeging badkamers");
}
$cms->edit_field(2,1,"htmlrow","<hr>");

if($vars["cmstaal"]) {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-indeling NL",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["indeling"]))."</span>"));
	$cms->edit_field(2,0,"indeling","Type-indeling NL","",array("noedit"=>true));
	$cms->edit_field(2,1,"htmlcol","Accommodatie-indeling ".strtoupper($vars["cmstaal"]),array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["indeling_".$vars["cmstaal"]]))."</span>"));
	$cms->edit_field(2,0,"indeling_".$vars["cmstaal"],"Type-indeling ".strtoupper($vars["cmstaal"]),"","",array("rows"=>25));
} else {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-indeling",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["indeling"]))."</span>"));
	$cms->edit_field(2,0,"indeling","Type-indeling","","",array("rows"=>25));
}

$cms->edit_field(2,1,"htmlrow","<hr>");
if($vars["cmstaal"]) {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-vouchertekst NL",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["voucherinfo"]))."</span>"));
	$cms->edit_field(2,0,"voucherinfo","Type-vouchertekst NL","",array("noedit"=>true));
	$cms->edit_field(2,1,"htmlcol","Accommodatie-vouchertekst ".strtoupper($vars["cmstaal"]),array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["voucherinfo_".$vars["cmstaal"]]))."</span>"));
	$cms->edit_field(2,0,"voucherinfo_".$vars["cmstaal"],"Type-vouchertekst ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(2,1,"htmlcol","Accommodatie-vouchertekst",array("html"=>"<span class=\"accinfo__in_typeform\">".nl2br(wt_he($temp["voucherinfo"]))."</span>"));
	$cms->edit_field(2,0,"voucherinfo","Type-vouchertekst");
}

/**
 * Nieuw afbeelding systeem
 */
if (isset($_GET['2k0'])) {

    ob_start();

    $uploaderData = ['id' => $_GET['2k0'], 'accommodationId' => $_GET['1k0'], 'collection' => 'types'];
    include $vars['unixdir'] . '/uploader/types.php';
    $types = ob_get_contents();

    ob_end_clean();

    $cms->edit_field(2, 0, 'htmlrow', "<hr><b id=\"images\">Nieuwe afbeeldingen</b>");
    $cms->edit_field(2, 0, 'htmlrow', $types);
}

$cms->edit_field(2,0,"htmlrow","<hr><b>Video</b>");
$cms->edit_field(2,0,"video_url","URL van Vimeo");
$cms->edit_field(2,0,"video","Toon deze video op de accommodatiepagina");


# Nieuw vertrekinfo-systeem
$cms->edit_field(2,0,"htmlrow","<a name=\"vertrekinfo\"></a><hr><br><b>Vertrekinfo-systeem</b>");
$cms->edit_field(2,0,"htmlrow","<br><i>Alinea 'Inchecken'</i>");
$cms->edit_field(2,0,"vertrekinfo_incheck_sjabloon_id","Sjabloon inchecken");
if($vertrekinfo_tracking["vertrekinfo_incheck_sjabloon_id"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_incheck_sjabloon_id"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_soortbeheer","Type beheer");
if($vertrekinfo_tracking["vertrekinfo_soortbeheer"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer"]))."</div>"));
}
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"vertrekinfo_soortbeheer_aanvulling","Aanvulling bij type beheer NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"],"Aanvulling bij type beheer ".strtoupper($vars["cmstaal"]));
	if($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]]))."</div>"));
	}
} else {
	$cms->edit_field(2,0,"vertrekinfo_soortbeheer_aanvulling","Aanvulling bij type beheer","","",array("info"=>"Bijvoorbeeld de naam van een contactpersoon: 'Carine'"));
	if($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling"]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling"]))."</div>"));
	}
}
$cms->edit_field(2,0,"vertrekinfo_telefoonnummer","Telefoonnummer beheer","","",array("info"=>"Bijvoorbeeld: '0039 0437 72 38 05'"));
if($vertrekinfo_tracking["vertrekinfo_telefoonnummer"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_telefoonnummer"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_inchecktijd","Inchecktijd","","",array("info"=>"Bijvoorbeeld: '17:00'"));
if($vertrekinfo_tracking["vertrekinfo_inchecktijd"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_inchecktijd"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_uiterlijkeinchecktijd","Uiterlijke inchecktijd","","",array("info"=>"Bijvoorbeeld: '19:00'"));
if($vertrekinfo_tracking["vertrekinfo_uiterlijkeinchecktijd"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_uiterlijkeinchecktijd"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_uitchecktijd","Uitchecktijd","","",array("info"=>"Bijvoorbeeld: '09:00'"));
$cms->edit_field(2,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Inclusief'</i>");
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"vertrekinfo_inclusief","Afwijkende inclusief-tekst NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"vertrekinfo_inclusief_".$vars["cmstaal"],"Afwijkende inclusief-tekst ".strtoupper($vars["cmstaal"]),"","",array("info"=>"Indien de tekst niet afwijkt van de website-tekst, dan hier niks invullen."));
	if($vertrekinfo_tracking["vertrekinfo_inclusief_".$vars["cmstaal"]]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_inclusief_".$vars["cmstaal"]]))."</div>"));
	}
} else {
	$cms->edit_field(2,0,"htmlcol","Inclusief-tekst website",array("html"=>"<div id=\"vertrekinfo_inclusief_website\" class=\"vertrekinfo_prevalue\"></div>"));
	if($vertrekinfo_tracking["inclusief"]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["inclusief"]))."</div>"));
	}
	$cms->edit_field(2,0,"vertrekinfo_inclusief","Afwijkende inclusief-tekst","","",array("info"=>"Indien de tekst niet afwijkt van de website-tekst, dan hier niks invullen."));
	if($vertrekinfo_tracking["vertrekinfo_inclusief"]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_inclusief"]))."</div>"));
	}
}
$cms->edit_field(2,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Exclusief'</i>");
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"vertrekinfo_exclusief","Afwijkende exclusief-tekst NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"vertrekinfo_exclusief_".$vars["cmstaal"],"Afwijkende exclusief-tekst ".strtoupper($vars["cmstaal"]),"","",array("info"=>"Indien de tekst niet afwijkt van de website-tekst, dan hier niks invullen."));
	if($vertrekinfo_tracking["vertrekinfo_exclusief_".$vars["cmstaal"]]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_exclusief_".$vars["cmstaal"]]))."</div>"));
	}
} else {
	$cms->edit_field(2,0,"htmlcol","Exclusief-tekst website",array("html"=>"<div id=\"vertrekinfo_exclusief_website\" class=\"vertrekinfo_prevalue\"></div>"));
	if($vertrekinfo_tracking["exclusief"]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["exclusief"]))."</div>"));
	}
	$cms->edit_field(2,0,"vertrekinfo_exclusief","Afwijkende exclusief-tekst","","",array("info"=>"Indien de tekst niet afwijkt van de website-tekst, dan hier niks invullen."));
}
$cms->edit_field(2,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Routebeschrijving naar de receptie of accommodatie' (wordt toegevoegd aan de routebeschrijving naar de betreffende plaats)</i>");
$db0->query("SELECT p.vertrekinfo_plaatsroute".($vars["cmstaal"] ? "_en" : "")." AS vertrekinfo_plaatsroute FROM plaats p, accommodatie a, type t WHERE a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND t.type_id='".intval($_GET["2k0"])."';");
if($db0->next_record()) {
	$cms->edit_field(2,0,"htmlcol","Routebeschrijving plaats",array("html"=>nl2br(wt_he($db0->f("vertrekinfo_plaatsroute")))));
}
if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"vertrekinfo_route","Routebeschrijving NL","",array("noedit"=>true));
	$cms->edit_field(2,0,"vertrekinfo_route_".$vars["cmstaal"],"Routebeschrijving ".strtoupper($vars["cmstaal"]));
	if($vertrekinfo_tracking["vertrekinfo_route_".$vars["cmstaal"]]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_route_".$vars["cmstaal"]]))."</div>"));
	}
} else {
	$cms->edit_field(2,0,"vertrekinfo_route","Routebeschrijving");
	if($vertrekinfo_tracking["vertrekinfo_route"]) {
		$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_route"]))."</div>"));
	}
}
$cms->edit_field(2,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Adres'</i>");
$cms->edit_field(2,0,"vertrekinfo_soortadres","Type adres");
if($vertrekinfo_tracking["vertrekinfo_soortadres"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortadres"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_adres","Adres");
if($vertrekinfo_tracking["vertrekinfo_adres"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_adres"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_plaatsnaam_beheer","Afwijkende plaatsnaam beheer","","",array("info"=>"Alleen invullen indien het beheer zich in een andere plaats dan de accommodatie bevindt."));
if($vertrekinfo_tracking["vertrekinfo_plaatsnaam_beheer"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_plaatsnaam_beheer"]))."</div>"));
}
$cms->edit_field(2,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'GPS-co&ouml;rdinaten'</i>");
if($vertrekinfo_tracking["vertrekinfo_gps_lat"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_gps_lat"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_gps_lat","GPS latitude beheer","","",array("info"=>"Alleen invullen indien deze afwijkt van de accommodatie-GPS-co�rdinaten. Vul de breedtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 52.086508"));
if($vertrekinfo_tracking["vertrekinfo_gps_long"]) {
	$cms->edit_field(2,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_gps_long"]))."</div>"));
}
$cms->edit_field(2,0,"vertrekinfo_gps_long","GPS longitude beheer","","",array("info"=>"Alleen invullen indien deze afwijkt van de accommodatie-GPS-co�rdinaten. Vul de lengtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 4.886513"));

if($vars["cmstaal"]) {
	$cms->edit_field(2,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo ".strtoupper($vars["cmstaal"])."</b>");
	$cms->edit_field(2,0,"vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"Vertrekinfo is goedgekeurd voor seizoen ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
	$cms->edit_field(2,0,"vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"],"Laatste goedkeuring ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
} else {
	$cms->edit_field(2,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo</b>");
	$cms->edit_field(2,0,"vertrekinfo_goedgekeurd_seizoen","Vertrekinfo is goedgekeurd voor seizoen","","",array("one_per_line"=>true));
	$cms->edit_field(2,0,"vertrekinfo_goedgekeurd_datetime","Laatste goedkeuring","","",array("one_per_line"=>true));
}

# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $cms,$vars;
	if($form->okay and $_GET["1k0"]) {
		if($form->fields["previous"]["leverancier_id"]["text"]<>$form->input["leverancier_id"]) {
			# bij wijziging leverancier: leverancier op accommodatie omzetten naar meestvoorkomende type-leverancier
			$db->query("SELECT leverancier_id, count(type_id) AS aantal FROM type WHERE accommodatie_id='".addslashes($_GET["1k0"])."' GROUP BY leverancier_id ORDER BY 2 DESC;");
			if($db->next_record()) {
				$nieuw_lev=$db->f("leverancier_id");
				$aantal=$db->f("aantal");
				if($db->next_record()) {
					# bij gelijk aantal: leverancier niet wijzigen
					if($db->f("aantal")==$aantal) {
						unset($nieuw_lev);
					}
				}
				if($nieuw_lev) {
					$db2->query("UPDATE accommodatie SET leverancier_id='".addslashes($nieuw_lev)."' WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
				}
			}

			# alle websites (type) opslaan in accommodatie
			$db->query("SELECT websites FROM type WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
			while($db->next_record()) {
				$allewebsites.=",".$db->f("websites");
			}
			$allewebsites_array=split(",",$allewebsites);
#			echo wt_dump($allewebsites_array);
			@reset($vars["websites_wzt"][$_GET["wzt"]]);
			while(list($key,$value)=@each($vars["websites_wzt"][$_GET["wzt"]])) {
				if(in_array($key,$allewebsites_array)) {
					if($allewebsites_acc) $allewebsites_acc.=",".$key; else $allewebsites_acc=$key;
				}
			}
			if($allewebsites_acc) {
				$db2->query("UPDATE accommodatie SET websites='".addslashes($allewebsites_acc)."' WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
#				echo $db2->lastquery."<br>";
			}
		}
	}

	if(($_GET["edit"]==2 or $_GET["add"]==2) and $_POST["frm_filled"]) {

		$voorraad_gekoppeld=new voorraad_gekoppeld;
		if($form->input["voorraad_gekoppeld_type_id"]) {
			// gekoppelde voorraad bijwerken
			$voorraad_gekoppeld->vanaf_prijzen_berekenen($form->input["voorraad_gekoppeld_type_id"]);
		}
		if($_GET["2k0"]) {
			$voorraad_gekoppeld->vanaf_prijzen_berekenen($_GET["2k0"]);
		}
		$voorraad_gekoppeld->koppeling_uitvoeren_na_einde_script();
	}

	# afbeeldingen verplaatsen en omzetten
	if(is_array($form->upload_filename)) {
		while(list($key,$value)=each($form->upload_filename)) {

			if(preg_match("/pic\/cms\/types_specifiek\//",$key)) {
				# hoofdfoto

				# thumbnail aanmaken
				wt_create_thumbnail("pic/cms/types_specifiek/".basename($key),"pic/cms/types_specifiek_tn/".basename($key),60,45);
				chmod("pic/cms/types_specifiek_tn/".basename($key),0666);
				filesync::add_to_filesync_table("pic/cms/types_specifiek_tn/".basename($key));

				# afbeelding naar juiste maat omzetten
				wt_create_thumbnail("pic/cms/types_specifiek/".basename($key),"pic/cms/types_specifiek/".basename($key),240,180);
				chmod("pic/cms/types_specifiek/".basename($key),0666);
				filesync::add_to_filesync_table("pic/cms/types_specifiek/".basename($key));
			}
		}
	}

	//  new type: bijkomendekosten
	if($form->db_insert_id) {

		// copy bijkomendekosten from accommodation
		$db->query("SELECT * FROM bk_accommodatie WHERE accommodatie_id='".intval($_GET["1k0"])."';");
		while($db->next_record()) {
			unset($setquery);
			foreach ($db->Record as $key => $value) {
				if($key<>"accommodatie_id" and !is_int($key)) {
					if($value=="") {
						$setquery .= ", ".$key."=NULL";
					} else {
						$setquery .= ", ".$key."='".addslashes($value)."'";
					}
				}
			}
			if($setquery) {
				$db2->query("INSERT INTO bk_type SET type_id='".intval($form->db_insert_id)."'".$setquery.";");
			}
		}

		// calculate bijkomendekosten
		$bijkomendekosten = new bijkomendekosten;
		$bijkomendekosten->setRedis(new wt_redis);
		$bijkomendekosten->pre_calculate_type($form->db_insert_id);
	}

	// change canonical-tag
	if ($form->okay && isset($_GET['1k0'])) {

		$db->query("SELECT is_maintype_canonical, accommodatie_id, type_id FROM type WHERE accommodatie_id = '" . intval($_GET["1k0"]) . "';");

		if ($form->input['is_maintype_canonical'] === '1') {
			$db->query("UPDATE type SET is_maintype_canonical = 0 WHERE accommodatie_id = " . intval($_GET['1k0']). " AND type_id != " . intval($_GET['2k0']) . ";");
		}
	}
}

function form_after_imagedelete($form) {
	# afbeeldingen wissen
	if(is_array($form->deleted_images)) {
		while(list($key,$value)=each($form->deleted_images)) {
			if(preg_match("/pic\/cms\/types_specifiek\//",$key)) {
				# hoofdfoto: thumbnail wissen
				delete_file("pic/cms/types_specifiek_tn/".basename($key));
			}
		}
	}
}

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(2);
if($cms_form[2]->filled) {

	#Controle of website engels of duitse is
		$websites = explode(",", $cms_form[2]->input["websites"]);

		foreach($websites as $website) {

			if (($vars["websiteinfo"]["taal"][$website] == "en" && $cms_form[2]->input["request_translation_en"]) || ($vars["websiteinfo"]["taal"][$website] == "de" && $cms_form[2]->input["request_translation_de"])) {
				$cms_form[2]->error("request_translation_en", "'Nieuw te vertalen' niet combineren met een corresponderende aangevinkte website");
			}
		}
	if($cms_form[2]->input["leverancierscode"] and !ereg("^[0-9,]+$",$cms_form[2]->input["leverancierscode"])) {
#		$cms_form[2]->error("leverancierscode","gebruik alleen cijfers (meerdere codes scheiden door komma's)");
	}

	if($cms_form[2]->input["leverancierscode_negeertarief"]) {
		if($cms_form[2]->input["leverancierscode"]) {
			$leverancierscode=split(",",$cms_form[2]->input["leverancierscode"]);
			$leverancierscode_negeertarief=split(",",$cms_form[2]->input["leverancierscode_negeertarief"]);
			while(list($key,$value)=each($leverancierscode_negeertarief)) {
				if(!in_array($value,$leverancierscode)) {
					$cms_form[2]->error("leverancierscode_negeertarief","onbekende leverancierscode");
				}
			}
		} else {
			$cms_form[2]->error("leverancierscode_negeertarief","onbekende leverancierscode");
		}
	}

	if($cms_form[2]->input["optimaalaantalpersonen"] and $cms_form[2]->input["aangepaste_min_tonen"]) {
		$mintonen=floor($cms_form[2]->input["optimaalaantalpersonen"]*.5);
		if($cms_form[2]->input["aangepaste_min_tonen"]>=$mintonen) {
			$cms_form[2]->error("aangepaste_min_tonen","een waarde van ".$mintonen." of hoger is niet nodig (dit is standaard al mogelijk)");
		}
	}
	# Controle op zomerwinterkoppeling_accommodatie_id vs geenzomerwinterkoppeling
	if($cms_form[2]->input["zomerwinterkoppeling_accommodatie_id"] and $cms_form[2]->input["geenzomerwinterkoppeling"]) {
		$cms_form[2]->error("zomerwinterkoppeling_accommodatie_id","niet invullen bij &quot;Deze accommodatie is niet beschikbaar in het winterprogramma&quot;");
	}

	// indien verzameltype: voorraad_gekoppeld_type_id niet toestaan
	if($cms_form[2]->input["verzameltype"] and $cms_form[2]->input["voorraad_gekoppeld_type_id"]) {
		$cms_form[2]->error("voorraad_gekoppeld_type_id","niet mogelijk bij verzameltype (voorraad is via het verzameltype al gekoppeld)");
	}

	# Controle of juiste taal wel actief is
	if(!$vars["cmstaal"] and $_POST["input"]) {
		while(list($key,$value)=each($_POST["input"])) {
			if(ereg("^naam_",$key)) {
				$cms_form[2]->error("taalprobleem","De CMS-taal is gewijzigd tijdens het bewerken. Opslaan is niet mogelijk. Ga terug naar het CMS-hoofdmenu en kies de gewenste taal",false,true);
			}
		}
	}
	if($cms_form[2]->input["doorsturen_naar_type_id"] and !ereg("^[A-Z][0-9]+$",$cms_form[2]->input["doorsturen_naar_type_id"])) {
		$cms_form[2]->error("doorsturen_naar_type_id","geen juiste accommodatie-code");
	}

	# Controle op verzameltype
	if($cms_form[2]->input["verzameltype"]) {
		if($cms_form[2]->input["leverancierscode"]) {
		 	$cms_form[2]->error("leverancierscode","een verzameltype kan geen XML-koppeling hebben");
		}
		if($cms_form[2]->input["verzameltype_parent"]) {
		 	$cms_form[2]->error("verzameltype_parent","dit type is zelf al een verzameltype");
		}
	}

	# Controle op subleverancier
	if($cms_form[2]->input["leverancier_sub_id"] and $cms_form[2]->input["leverancier_id"]) {
		$db->query("SELECT leverancier_sub_id FROM leverancier_sub WHERE leverancier_sub_id='".addslashes($cms_form[2]->input["leverancier_sub_id"])."' AND leverancier_id='".addslashes($cms_form[2]->input["leverancier_id"])."';");
		if(!$db->num_rows()) {
		 	$cms_form[2]->error("leverancier_sub_id","valt niet onder de gekozen leverancier");
		}
	}

	# Controle op gps_lat
	if($cms_form[2]->input["gps_lat"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[2]->input["gps_lat"])) {
			if(floatval($cms_form[2]->input["gps_lat"])<33.797408767572485 or floatval($cms_form[2]->input["gps_lat"])>71.01695975726373) {
				$cms_form[2]->error("gps_lat","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[2]->error("gps_lat","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}

	# Controle op gps_long
	if($cms_form[2]->input["gps_long"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[2]->input["gps_long"])) {
			if(floatval($cms_form[2]->input["gps_long"])<-9.393310546875 or floatval($cms_form[2]->input["gps_long"])>27.7734375) {
				$cms_form[2]->error("gps_long","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[2]->error("gps_long","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}
	if($cms_form[2]->input["gps_long"]<>"" and !$cms_form[2]->input["gps_lat"]) $cms_form[2]->error("gps_lat","vul zowel latitude als longitude in");
	if($cms_form[2]->input["gps_lat"]<>"" and !$cms_form[2]->input["gps_long"]) $cms_form[2]->error("gps_long","vul zowel latitude als longitude in");

	# Controle op vertrekinfo_gps_lat
	if($cms_form[2]->input["vertrekinfo_gps_lat"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[2]->input["vertrekinfo_gps_lat"])) {
			if(floatval($cms_form[2]->input["vertrekinfo_gps_lat"])<33.797408767572485 or floatval($cms_form[2]->input["vertrekinfo_gps_lat"])>71.01695975726373) {
				$cms_form[2]->error("vertrekinfo_gps_lat","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[2]->error("vertrekinfo_gps_lat","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}

	# Controle op vertrekinfo_gps_long
	if($cms_form[2]->input["vertrekinfo_gps_long"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[2]->input["vertrekinfo_gps_long"])) {
			if(floatval($cms_form[2]->input["vertrekinfo_gps_long"])<-9.393310546875 or floatval($cms_form[2]->input["vertrekinfo_gps_long"])>27.7734375) {
				$cms_form[2]->error("vertrekinfo_gps_long","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[2]->error("vertrekinfo_gps_long","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}
	if($cms_form[2]->input["vertrekinfo_gps_long"]<>"" and !$cms_form[2]->input["vertrekinfo_gps_lat"]) $cms_form[2]->error("vertrekinfo_gps_lat","vul zowel latitude als longitude in");
	if($cms_form[2]->input["vertrekinfo_gps_lat"]<>"" and !$cms_form[2]->input["vertrekinfo_gps_long"]) $cms_form[2]->error("gps_long","vul zowel latitude als longitude in");

	# Controle op vertrekinfo_soortadres bij invullen vertrekinfo_adres
	if($cms_form[2]->input["vertrekinfo_adres"] and !$cms_form[2]->input["vertrekinfo_soortadres"]) {
		$cms_form[2]->error("vertrekinfo_soortadres","obl");
	}

	# Controle op aanwezige video_url
	if($cms_form[2]->input["video"] and !$cms_form[2]->input["video_url"]) {
		$cms_form[2]->error("video_url","obl");
	}

	# Controle op Vimeo-link
	if($cms_form[2]->input["video_url"] and !preg_match("/^https:\/\/player\.vimeo\.com\/video\/[0-9]+$/",$cms_form[2]->input["video_url"])) {
		$cms_form[2]->error("video_url","onjuist formaat. Voorbeeld: https://player.vimeo.com/video/44377043");
	}

	// Check homeaway-code
	if($cms_form[2]->input["homeaway_code"] and !preg_match("@^HA[0-9]+$@",$cms_form[2]->input["homeaway_code"])) {
		$cms_form[2]->error("homeaway_code","onjuist formaat. Voorbeeld: HA1672528");
	}
}

if($_GET["wzt"]==2 and $_GET["edit"]==2 and $cms_form[2]->okay) {
	$db->query("UPDATE type SET zomerwinterkoppeling_accommodatie_id=NULL WHERE zomerwinterkoppeling_accommodatie_id='".addslashes($_GET["2k0"])."';");
	if($cms_form[2]->input["zomerwinterkoppeling_accommodatie_id"]) {
		$db->query("UPDATE type SET zomerwinterkoppeling_accommodatie_id='".addslashes($_GET["2k0"])."' WHERE type_id='".addslashes($cms_form[2]->input["zomerwinterkoppeling_accommodatie_id"])."';");
	}
}

# Controle op delete-opdracht
if($_GET["delete"]==2 and $_GET["2k0"]) {
	$db->query("SELECT type_id FROM boeking WHERE type_id='".addslashes($_GET["2k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(2,"Dit type bevat nog gekoppelde boekingen");
	}
}

#
# DELETEn van andere tabellen
#
if($cms->set_delete_init(2)) {
	$db->query("DELETE FROM tarief WHERE type_id='".addslashes($_GET["2k0"])."';");
	$db->query("DELETE FROM tarief_personen WHERE type_id='".addslashes($_GET["2k0"])."';");
	$db->query("DELETE FROM nummer WHERE type_id='".addslashes($_GET["2k0"])."';");
}

if($_GET["show"]==2) {
	$db->query("SELECT a.naam, t.naam AS tnaam, p.naam AS plaats, l.begincode FROM land l, plaats p, accommodatie a, type t WHERE t.type_id='".addslashes($_GET["2k0"])."' AND a.accommodatie_id=t.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id;");
	if($db->next_record()) {
		$naam=$db->f("naam").", ".$db->f("plaats");
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_header[2]=$naam;
$cms->show_name[2]="typegegevens";
$cms->show_mainfield[2]="naam";
$cms->show_field(2,"naam","Naam type");
$cms->show_field(2,"type_id","ID");
$cms->show_field(2,"code");
$cms->show_field(2,"optimaalaantalpersonen","Min");
$cms->show_field(2,"maxaantalpersonen","Max");
$cms->show_field(2,"aantekeningen");
$cms->show_field(2,"picgroot","Afbeelding");


# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);
