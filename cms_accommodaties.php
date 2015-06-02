<?php

$mustlogin=true;
#$vars["acc_in_vars"]=true;

include("admin/vars.php");

if($_GET["1k0"]) {
	$db->query("SELECT a.toonper, a.plaats_id FROM accommodatie a WHERE a.accommodatie_id='".addslashes($_GET["1k0"])."';");
	if($db->next_record()) {
		$oud_toonper=$db->f("toonper");
		$plaats_id=$db->f("plaats_id");
	}

	# Kijken of er aan deze accommodatie al boekingen hangen
	$db->query("SELECT b.boeking_id FROM boeking b, accommodatie a, type t WHERE a.accommodatie_id='".addslashes($_GET["1k0"])."' AND b.stap_voltooid=5 AND t.accommodatie_id=a.accommodatie_id AND b.type_id=t.type_id;");
	if($db->next_record()) {
		$accommodatie_heeft_boekingen=true;
	}
} else {
	# Actuele boekingen voor in totaaloverzicht uit database halen
	$db->query("SELECT t.accommodatie_id, COUNT(b.boeking_id) AS aantal FROM boeking b, type t, seizoen s WHERE b.seizoen_id=s.seizoen_id AND b.type_id=t.type_id AND b.geannuleerd=0 AND b.stap_voltooid=5 AND b.goedgekeurd=1 AND s.tonen>1 GROUP BY t.accommodatie_id;");
	while($db->next_record()) {
		$aantal_actuele_boekingen[$db->f("accommodatie_id")]=$db->f("aantal");
	}
}

if($_GET["edit"]==1 and $_GET["1k0"] and $_POST["frm_filled"]) {

	$db->query("SELECT type_id FROM type WHERE accommodatie_id='".intval($_GET["1k0"])."';");
	if($db->num_rows()) {
		$voorraad_gekoppeld=new voorraad_gekoppeld;
		while($db->next_record()) {
			$voorraad_gekoppeld->vanaf_prijzen_berekenen($db->f("type_id"));
		}
		$voorraad_gekoppeld->koppeling_uitvoeren_na_einde_script();
	}
}

if($_GET["xmlvoorraadreset"]) {
	#
	# XML-voorraad van alle onderliggende tarieven op 0 zetten (en daarna beschikbaarheid indien nodig aanpassen)
	#
	if($_GET["confirmed"] and $_GET["1k0"]) {
		$db->query("SELECT seizoen_id FROM seizoen WHERE UNIX_TIMESTAMP(eind)>".(time()).";");
		while($db->next_record()) {
			$db2->query("SELECT DISTINCT type_id, week FROM tarief WHERE voorraad_xml<>0 AND seizoen_id='".addslashes($db->f("seizoen_id"))."' AND type_id IN (SELECT type_id FROM type WHERE accommodatie_id='".addslashes($_GET["1k0"])."') ORDER BY week;");
			while($db2->next_record()) {
				voorraad_bijwerken($db2->f("type_id"),$db2->f("week"),false,"","","","",0,"","",false,5);
#				echo $db2->f("type_id")." ".date("r",$db2->f("week"))."<br>";
			}
		}
	}
	$url=ereg_replace("&xmlvoorraadreset=1","",$_SERVER["REQUEST_URI"]);
	$url=ereg_replace("&confirmed=1","",$url);
	header("Location: ".$url);
	exit;
}

if($_GET["copy_accommodation"] and $_GET["confirmed"]) {

	// copy accommodation

	$copydatabaserecord = new copydatabaserecord;
	$copydatabaserecord->copy_accommodatie($_GET["1k0"]);

	$_SESSION["wt_popupmsg"]="accommodatie correct gekopieerd";

	$url=ereg_replace("&copy_accommodation=1","",$_SERVER["REQUEST_URI"]);
	$url=ereg_replace("&confirmed=1","",$url);
	$url=ereg_replace("&1k0=([0-9]+)","&1k0=".$copydatabaserecord->new_accommodatie_id,$url);
	header("Location: ".$url);
	exit;
}

# wzt opvragen indien niet meegegeven met query_string
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

# Kijken of er actieve boekingen aan deze accommodaties hangen
$db->query("SELECT DISTINCT og.optie_soort_id FROM boeking b, type t, accommodatie a, optie_groep og, optie_onderdeel oo, boeking_optie bo WHERE bo.boeking_id=b.boeking_id AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND t.accommodatie_id=a.accommodatie_id AND b.type_id=t.type_id AND a.accommodatie_id='".addslashes($_GET["1k0"])."' AND b.bevestigdatum>0 AND b.geannuleerd=0 AND b.vertrekdatum_exact>".time().";");
while($db->next_record()) {
	if(!$login->has_priv("6")) {
		# Niet aan te passen optie_soorten niet wissen
		if($inquery) $inquery.=",".$db->f("optie_soort_id"); else $inquery=$db->f("optie_soort_id");
	}
	$vars["temp"]["niet_wijzigen"][$db->f("optie_soort_id")]=true;
}

# Beschikbare opties opslaan
if($_POST["opties_filled"]) {
	$db->query("DELETE FROM optie_accommodatie WHERE accommodatie_id='".addslashes($_GET["1k0"])."'".($inquery ? " AND optie_soort_id NOT IN (".$inquery.")" : "").";");
	@reset($_POST["groep"]);
	while(list($key,$value)=@each($_POST["groep"])) {
		if($key>0 and $value>0) $db->query("INSERT INTO optie_accommodatie SET accommodatie_id='".addslashes($_GET["1k0"])."', optie_soort_id='".addslashes($key)."', optie_groep_id='".addslashes($value)."';");
	}
	header("Location: cms_accommodaties.php?".$_SERVER["QUERY_STRING"]);
	exit;
}

# Afwijkende vertrekdagtypes opslaan
if($_POST["vertrekdagtypes_filled"]) {
	@reset($_POST["vertrekdagtype"]);
	while(list($key,$value)=@each($_POST["vertrekdagtype"])) {
		if($key>0) {
			if($value<>"") {
				$db->query("INSERT INTO accommodatie_seizoen SET accommodatie_id='".addslashes($_GET["1k0"])."', seizoen_id='".addslashes($key)."', vertrekdagtype_id='".addslashes($value)."';");
				if($db->Errno==1062) {
					$db->query("UPDATE accommodatie_seizoen SET vertrekdagtype_id='".addslashes($value)."' WHERE accommodatie_id='".addslashes($_GET["1k0"])."' AND seizoen_id='".addslashes($key)."';");
				}
			}
		}
	}
	header("Location: cms_accommodaties.php?".$_SERVER["QUERY_STRING"]);
	exit;
}

# Gekoppelde accommodaties opslaan
if($_POST["acckoppelen_filled"] and $_POST["accid"]) {
	$db->query("INSERT INTO accommodatie_koppeling SET accommodatie1_id='".addslashes($_GET["1k0"])."', accommodatie2_id='".addslashes($_POST["accid"])."';");
	gekoppelde_acc_opslaan_voor_zoekfunctie();
	header("Location: cms_accommodaties.php?".$_SERVER["QUERY_STRING"]);
	exit;
}

# Gekoppelde accommodaties wissen
if($_GET["acckoppelen_delete"] and $_GET["confirmed"]) {
	$db->query("DELETE FROM accommodatie_koppeling WHERE accommodatie1_id='".addslashes($_GET["acc1"])."' AND accommodatie2_id='".addslashes($_GET["acc2"])."';");
	$db->query("DELETE FROM accommodatie_koppeling WHERE accommodatie1_id='".addslashes($_GET["acc2"])."' AND accommodatie2_id='".addslashes($_GET["acc1"])."';");
	gekoppelde_acc_opslaan_voor_zoekfunctie();
	header("Location: cms_accommodaties.php?".wt_stripget($_GET,array("acckoppelen_delete","confirmed","acc1","acc2")));
	exit;
}

if($_GET["1k0"]) {
	# Seizoenen laden t.b.v. vertrekinfo_seizoengoedgekeurd
	$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE type='".addslashes($_GET["wzt"])."' AND UNIX_TIMESTAMP(eind)>'".(time()-(86400*60))."' ORDER BY type, begin, eind;");
	while($db->next_record()) {
		$vars["seizoengoedgekeurd"][$db->f("seizoen_id")]=$db->f("naam");
		$laatste_seizoen=$db->f("seizoen_id");
	}

	# Vertrekinfo-tracking
	$vertrekinfo_tracking_array=array("inclusief", "exclusief" ,"vertrekinfo_incheck_sjabloon_id", "vertrekinfo_soortbeheer", "vertrekinfo_soortbeheer_aanvulling", "vertrekinfo_telefoonnummer", "vertrekinfo_noodtelefoonnummer_accommodatie", "vertrekinfo_inchecktijd", "vertrekinfo_uiterlijkeinchecktijd", "vertrekinfo_uitchecktijd", "vertrekinfo_inclusief", "vertrekinfo_exclusief", "vertrekinfo_route", "vertrekinfo_soortadres", "vertrekinfo_adres", "vertrekinfo_plaatsnaam_beheer", "vertrekinfo_gps_lat", "vertrekinfo_gps_long");
	if($vars["cmstaal"]) {
		$vertrekinfo_tracking_array[]="vertrekinfo_inclusief_".$vars["cmstaal"];
		$vertrekinfo_tracking_array[]="vertrekinfo_exclusief_".$vars["cmstaal"];
		$vertrekinfo_tracking_array[]="vertrekinfo_route_".$vars["cmstaal"];
		$vertrekinfo_tracking_array[]="vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"];
	}
	$vertrekinfo_tracking=vertrekinfo_tracking("accommodatie",$vertrekinfo_tracking_array,$_GET["1k0"],$laatste_seizoen);
}

$cms->settings[1]["list"]["show_icon"]=true;
$cms->settings[1]["list"]["edit_icon"]=true;
$cms->settings[1]["list"]["delete_icon"]=true;

if(!$_GET["1where"]) {
	$cms->db[1]["where"]="wzt='".addslashes($_GET["wzt"])."'";
	if($_GET["controleren"] and !$_GET["1k0"]) {
		$db->query("SELECT DISTINCT accommodatie_id FROM type WHERE controleren=1");
		while($db->next_record()) {
			$inquery_controleren.=",".$db->f("accommodatie_id");
		}
		if($inquery_controleren) {
			$cms->db[1]["where"].=" AND (controleren=1 OR accommodatie_id IN (".substr($inquery_controleren,1)."))";
		} else {
			$cms->db[1]["where"].=" AND controleren=1";
		}
	}
}
$cms->db[1]["set"]="wzt='".addslashes($_GET["wzt"])."'";

if($_GET["edit"]<>1 and $_GET["show"]<>1 and !$_GET["1where"]) {
	if($_GET["archief"]) {
		$cms->db[1]["where"].=" AND archief=1";
		$cms->settings[1]["list"]["add_link"]=false;
	} elseif(isset($_GET["archief"])) {
		$cms->db[1]["where"].=" AND archief=0";
	}
	if($_GET["controleren"]) {
		$cms->settings[1]["list"]["add_link"]=false;
	}
}

$cms->settings[1]["show"]["goto_new_record"]=true;
$cms->settings[1]["edit"]["top_submit_button"]=true;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(1,"noedit","accommodatie_id");
$cms->db_field(1,"select","aantal_actuele_boekingen","accommodatie_id",array("selection"=>$aantal_actuele_boekingen));
$cms->db_field(1,"text","naam");
$cms->db_field(1,"text","internenaam");
$cms->db_field(1,"text","bestelnaam");
$cms->db_field(1,"text","korteomschrijving");
if($vars["cmstaal"]) $cms->db_field(1,"text","korteomschrijving_".$vars["cmstaal"]);
$cms->db_field(1,"text","altnaam");
$cms->db_field(1,"text","altnaam_zichtbaar");
$cms->db_field(1,"textarea","aantekeningen","",array("dontlog"=>true));
$cms->db_field(1,"yesno","controleren");
$cms->db_field(1,"yesno","tonen");
$cms->db_field(1,"yesno","request_translation");
$cms->db_field(1,"yesno","archief");
$cms->db_field(1,"yesno","tonenzoekformulier");
$cms->db_field(1,"text","leverancierscode");
$cms->db_field(1,"url","url_leverancier");

# inactieve sites uitzetten
while(list($key,$value)=each($vars["websites_inactief"])) {
	unset($vars["websites_wzt"][$_GET["wzt"]][$key]);
}
$cms->db_field(1,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(1,"yesno","weekendski");
$cms->db_field(1,"select","leverancier_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));
$cms->db_field(1,"select","beheerder_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=1"));
$cms->db_field(1,"select","skipas_id","",array("othertable"=>"10","otherkeyfield"=>"skipas_id","otherfield"=>"naam"));
$cms->db_field(1,"select","plaats_id","",array("othertable"=>"4","otherkeyfield"=>"plaats_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
$cms->db_field(1,"select","soortaccommodatie","",array("selection"=>$vars["soortaccommodatie"]));
if($oud_toonper==2) {
	$cms->db_field(1,"select","toonper","",array("selection"=>$vars["toonper"]));
} else {
	$cms->db_field(1,"select","toonper","",array("selection"=>$vars["toonper_beperktekeuze"]));
}
$cms->db_field(1,"yesno","flexibel");
$cms->db_field(1,"select","bijkomendekosten1_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(1,"select","bijkomendekosten2_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(1,"select","bijkomendekosten3_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(1,"select","bijkomendekosten4_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(1,"select","bijkomendekosten5_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(1,"select","bijkomendekosten6_id","",array("othertable"=>"33","otherkeyfield"=>"bijkomendekosten_id","otherfield"=>"internenaam","otherwhere"=>"gekoppeldaan=1"));
$cms->db_field(1,"select","zoekvolgorde","",array("selection"=>$vars["zoekvolgorde"]));

$cms->db_field(1,"select","aankomst_plusmin","",array("selection"=>$vars["aankomst_plusmin"]));
$cms->db_field(1,"select","vertrek_plusmin","",array("selection"=>$vars["vertrek_plusmin"]));

$cms->db_field(1,"textarea","omschrijving");
#$cms->db_field(1,"checkbox","kenmerken","",array("selection"=>$vars["kenmerken_accommodatie_".$_GET["wzt"]]));
$cms->db_field(1,"multiradio","kenmerken","",array("selection"=>$vars["kenmerken_accommodatie_".$_GET["wzt"]],"multiselection"=>array(1=>"ja",2=>"nee",3=>"onbekend",4=>"niet relevant"),"multiselectionfields"=>array(1=>"kenmerken",2=>"kenmerken_nee",3=>"kenmerken_onbekend",4=>"kenmerken_irrelevant")));
#$cms->db_field(1,"yesno","kenmerken_gecontroleerd");
$cms->db_field(1,"date","kenmerken_gecontroleerd_datum");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","omschrijving_".$vars["cmstaal"]);
$cms->db_field(1,"select","kwaliteit","",array("selection"=>$vars["kwaliteit"]));
$cms->db_field(1,"textarea","indeling");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","indeling_".$vars["cmstaal"]);
$cms->db_field(1,"textarea","inclusief");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","inclusief_".$vars["cmstaal"]);
$cms->db_field(1,"textarea","exclusief");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","exclusief_".$vars["cmstaal"]);
$cms->db_field(1,"textarea","extraopties");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","extraopties_".$vars["cmstaal"]);
$cms->db_field(1,"text","receptie");
if($vars["cmstaal"]) $cms->db_field(1,"text","receptie_".$vars["cmstaal"]);
$cms->db_field(1,"textarea","telefoonnummer");
$cms->db_field(1,"textarea","reviews");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","reviews_".$vars["cmstaal"]);
$cms->db_field(1,"textarea","voucherinfo");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","voucherinfo_".$vars["cmstaal"]);
$cms->db_field(1,"integer","afstandwinkel");
$cms->db_field(1,"text","afstandwinkelextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandwinkelextra_".$vars["cmstaal"]);
$cms->db_field(1,"integer","afstandrestaurant");
$cms->db_field(1,"text","afstandrestaurantextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandrestaurantextra_".$vars["cmstaal"]);
$cms->db_field(1,"integer","afstandpiste");
$cms->db_field(1,"text","afstandpisteextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandpisteextra_".$vars["cmstaal"]);
$cms->db_field(1,"integer","afstandskilift");
$cms->db_field(1,"text","afstandskiliftextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandskiliftextra_".$vars["cmstaal"]);
$cms->db_field(1,"integer","afstandloipe");
$cms->db_field(1,"text","afstandloipeextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandloipeextra_".$vars["cmstaal"]);
$cms->db_field(1,"integer","afstandskibushalte");
$cms->db_field(1,"text","afstandskibushalteextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandskibushalteextra_".$vars["cmstaal"]);

$cms->db_field(1,"integer","afstandstrand");
$cms->db_field(1,"text","afstandstrandextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandstrandextra_".$vars["cmstaal"]);

$cms->db_field(1,"integer","afstandzwembad");
$cms->db_field(1,"text","afstandzwembadextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandzwembadextra_".$vars["cmstaal"]);

$cms->db_field(1,"integer","afstandzwemwater");
$cms->db_field(1,"text","afstandzwemwaterextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandzwemwaterextra_".$vars["cmstaal"]);

$cms->db_field(1,"integer","afstandgolfbaan");
$cms->db_field(1,"text","afstandgolfbaanextra");
if($vars["cmstaal"]) $cms->db_field(1,"text","afstandgolfbaanextra_".$vars["cmstaal"]);

$cms->db_field(1,"select","mailtekst_id","",array("othertable"=>"32","otherkeyfield"=>"mailtekst_id","otherfield"=>"naam","otherwhere"=>"wzt='".addslashes($_GET["wzt"])."'"));
$cms->db_field(1,"integer","optiedagen_klanten_vorig_seizoen","","",array("text"=>"10"));

$cms->db_field(1,"text","gps_lat");
$cms->db_field(1,"text","gps_long");

$cms->db_field(1,"mongodb_picture","picgroot","",array("savelocation"=>"pic/cms/accommodaties/","filetype"=>"jpg"));
$cms->db_field(1,"picture","hoofdfoto_accommodatie","",array("savelocation"=>"pic/cms/hoofdfoto_accommodatie/","filetype"=>"jpg"));
#$cms->db_field(1,"picture","picklein","",array("savelocation"=>"pic/cms/accommodaties_tn/","filetype"=>"jpg"));
$cms->db_field(1,"picture","picaanvullend","",array("savelocation"=>"pic/cms/accommodaties_aanvullend/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(1,"picture","picaanvullendonderaan","",array("savelocation"=>"pic/cms/accommodaties_aanvullend_onderaan/","filetype"=>"jpg","multiple"=>true));
$cms->db_field(1,"picture","picaanvullend_breed","",array("savelocation"=>"pic/cms/accommodaties_aanvullend_breed/","filetype"=>"jpg","multiple"=>true));

# vertrekinfo in NL en EN
$cms->db_field(1,"upload","route","",array("savelocation"=>"pdf/route_nl/","filetype"=>"pdf"));
$cms->db_field(1,"checkbox","vertrekinfo_seizoengoedgekeurd","",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(1,"upload","route_en","",array("savelocation"=>"pdf/route_en/","filetype"=>"pdf"));
$cms->db_field(1,"checkbox","vertrekinfo_seizoengoedgekeurd_en","",array("selection"=>$vars["seizoengoedgekeurd"]));

# Nieuw vertrekinfo-systeem
$cms->db_field(1,"upload","accommodatie_aanvullende_informatie","",array("savelocation"=>"pdf/accommodatie_aanvullende_informatie/","filetype"=>"pdf"));

$cms->db_field(1,"checkbox","vertrekinfo_goedgekeurd_seizoen","",array("selection"=>$vars["seizoengoedgekeurd"]));
if($vars["cmstaal"]) $cms->db_field(1,"checkbox","vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"",array("selection"=>$vars["seizoengoedgekeurd"]));
$cms->db_field(1,"text","vertrekinfo_goedgekeurd_datetime");
if($vars["cmstaal"]) $cms->db_field(1,"text","vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"]);
$cms->db_field(1,"select","vertrekinfo_incheck_sjabloon_id","",array("othertable"=>"54","otherkeyfield"=>"vertrekinfo_sjabloon_id","otherfield"=>"naam","otherwhere"=>"soort=1"));
$cms->db_field(1,"select","vertrekinfo_soortbeheer","",array("selection"=>$vars["vertrekinfo_soortbeheer"]));
$cms->db_field(1,"text","vertrekinfo_soortbeheer_aanvulling");
if($vars["cmstaal"]) $cms->db_field(1,"text","vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]);
$cms->db_field(1,"text","vertrekinfo_telefoonnummer");
$cms->db_field(1,"text","vertrekinfo_noodtelefoonnummer_accommodatie");
$cms->db_field(1,"text","vertrekinfo_inchecktijd");
$cms->db_field(1,"text","vertrekinfo_uiterlijkeinchecktijd");
$cms->db_field(1,"text","vertrekinfo_uitchecktijd");
$cms->db_field(1,"textarea","vertrekinfo_inclusief");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","vertrekinfo_inclusief_".$vars["cmstaal"]);
$cms->db_field(1,"textarea","vertrekinfo_exclusief");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","vertrekinfo_exclusief_".$vars["cmstaal"]);
$cms->db_field(1,"textarea","vertrekinfo_route");
if($vars["cmstaal"]) $cms->db_field(1,"textarea","vertrekinfo_route_".$vars["cmstaal"]);
$cms->db_field(1,"select","vertrekinfo_soortadres","",array("selection"=>$vars["vertrekinfo_soortadres"]));
$cms->db_field(1,"textarea","vertrekinfo_adres");
$cms->db_field(1,"text","vertrekinfo_plaatsnaam_beheer");
$cms->db_field(1,"text","vertrekinfo_gps_lat");
$cms->db_field(1,"text","vertrekinfo_gps_long");


# Video
$cms->db_field(1,"yesno","video");
$cms->db_field(1,"url","video_url");

#
#
# List list_field($counter,$id,$title="",$options="",$layout="")
#
#
#
$cms->list_sort[1]=array("leverancier_id","plaats_id","naam");
#$cms->list_field(1,"accommodatie_id","ID");
$cms->list_field(1,"leverancier_id","Leverancier");
$cms->list_field(1,"plaats_id","Plaats");
$cms->list_field(1,"internenaam","Interne naam");
if(!$_GET["archief"]) {
	$cms->list_field(1,"tonen","Tonen");
	$cms->list_field(1,"tonenzoekformulier","Zoektonen");
}

$cms->list_field(1,"websites","Sites");
$cms->list_field(1,"aantal_actuele_boekingen","Boekingen");


# Sommige velden zijn niet verplicht bij inactieve accommodaties
$actief_obl=1;
if($_POST["frm_filled"]) {
	 if($_POST["input"]["archief"]) $actief_obl=0;
	 if(!$_POST["input"]["tonen"]) $actief_obl=0;
}

#
#
# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
#
#
$cms->edit_field(1,0,"archief","Gearchiveerde accommodatie");
$cms->edit_field(1,0,"controleren","Nog nakijken");
$cms->edit_field(1,0,"tonen","Tonen op de website",array("selection"=>true));
$cms->edit_field(1,0,"tonenzoekformulier","Tonen in de zoekresultaten",array("selection"=>true));
$cms->edit_field(1,0,"request_translation","Opnemen in lijst <a href=\"".$vars["path"]."cms_overzichten_overig.php?t=3&wzt=".intval($_GET["wzt"])."&vertaalsysteem&request_translation=1\" target=\"_blank\">nieuw te vertalen accommodaties/types</a>",array("selection"=>false),"",array("title_html"=>true));
$cms->edit_field(1,0,"weekendski","Weekendski");
if($_GET["edit"]==1) {
	$cms->edit_field(1,0,"htmlrow","<hr><i><span style=\"color:red;\"><b>Let op!</b> Bij wijzigen &quot;websites&quot; worden alle onderliggende types aangepast.</span><br>Om dat te voorkomen kun je &quot;websites&quot; aanpassen op type-niveau.</i><br/><br/>Een ingevulde waarde hier wil zeggen dat bij minstens &eacute;&eacute;n onderliggend type deze website aangevinkt staat. Het wil niet zeggen dat bij &agrave;lle onderliggende types de website aangevinkt staat.");
}
$cms->edit_field(1,0,"websites","Websites",array("selection"=>($_GET["wzt"]==1 ? "B,C,T,W" : "N,O,Z")),"",array("one_per_line"=>true));
if($_GET["edit"]==1) {
	$cms->edit_field(1,0,"htmlrow","<hr>");
}
if($_GET["1k0"]) $cms->edit_field(1,1,"accommodatie_id","ID");
$cms->edit_field(1,1,"naam","Naam op de website","","",array("onchange"=>"if(document.forms['frm'].elements['input[internenaam]'].value=='') document.forms['frm'].elements['input[internenaam]'].value=document.forms['frm'].elements['input[naam]'].value;if(document.forms['frm'].elements['input[bestelnaam]'].value=='') document.forms['frm'].elements['input[bestelnaam]'].value=document.forms['frm'].elements['input[naam]'].value;","info"=>"accommodatienaam die de klant krijgt te zien"));
$cms->edit_field(1,1,"internenaam","Interne naam","","",array("info"=>"accommodatienaam voor intern gebruik"));
$cms->edit_field(1,1,"bestelnaam","Naam volgens leverancier","","",array("info"=>"accommodatienaam zoals de leverancier 'm gebruikt (wordt o.a. gebruikt bij bestellen)"));
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"korteomschrijving","Korte omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(1,$actief_obl,"korteomschrijving_".$vars["cmstaal"],"Korte omschrijving ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(1,($login->userlevel>=10 ? 0 : $actief_obl),"korteomschrijving","Korte omschrijving","","",array("info"=>"Korte/krachtige omschrijving van de accommodatie in 1 zin voor op de accommodatiepagina (voor bezoekers en zoekmachines). Is ook in te voeren op typeniveau (die overschijft dan deze invoer)."));
}
$cms->edit_field(1,0,"altnaam","Zoekwoorden (intern zoekformulier)");
$cms->edit_field(1,0,"altnaam_zichtbaar","Alternatieve spelling / trefwoorden (Google)");
$cms->edit_field(1,0,"aantekeningen","Aantekeningen (intern)","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
if($_GET["add"]==1) {
	$cms->edit_field(1,1,"leverancier_id","Leverancier");
	$cms->edit_field(1,0,"beheerder_id","Beheerder");
} else {
	$cms->edit_field(1,1,"leverancier_id","Leverancier","",array("noedit"=>true));
#	$cms->edit_field(1,0,"beheerder_id","Beheerder","",array("noedit"=>true));
}
$cms->edit_field(1,0,"leverancierscode","Leverancierscode accommodatie (voor XML)");
$cms->edit_field(1,0,"url_leverancier","Directe link bij leverancier");
if($_GET["wzt"]==1) {
	$cms->edit_field(1,0,"skipas_id","Skipas");
}
$cms->edit_field(1,1,"plaats_id","Plaats");
$cms->edit_field(1,0,"gps_lat","GPS latitude","","",array("info"=>"Vul de breedtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 52.086508"));
$cms->edit_field(1,0,"gps_long","GPS longitude","","",array("info"=>"Vul de lengtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 4.886513"));
$cms->edit_field(1,1,"soortaccommodatie","Soort accommodatie");
if($accommodatie_heeft_boekingen) {
	if($login->userlevel>=10) {
		$cms->edit_field(1,0,"htmlrow","<hr><b>LET OP:</b> <i>Alleen WebTastic - Er zijn <a href=\"cms_boekingen.php?boekingsearch=_".wt_he($_GET["1k0"])."\" target=\"_blank\">boekingen aan deze accommodatie gekoppeld</a>. - wijzigen alleen mogelijk door WebTastic. Boekingen worden gewist.</i>");
		$cms->edit_field(1,1,"toonper","Noteer tarieven per","",array("noedit"=>false));
		$cms->edit_field(1,0,"htmlrow","<hr>");
	} else {
		$cms->edit_field(1,0,"htmlrow","<hr><i>Er zijn <a href=\"cms_boekingen.php?boekingsearch=_".wt_he($_GET["1k0"])."\" target=\"_blank\">boekingen aan deze accommodatie gekoppeld</a>. Wijzigen van onderstaand veld is alleen mogelijk door WebTastic. Boekingen moeten in dat geval worden gewist.</i>");
		$cms->edit_field(1,1,"toonper","Noteer tarieven per","",array("noedit"=>true));
		$cms->edit_field(1,0,"htmlrow","<hr>");
	}
} else {
	if($_GET["add"]<>1) {
		$cms->edit_field(1,0,"htmlrow","<hr><b>LET OP:</b> <i>bij het wijzigen van dit veld worden alle ooit ingevoerde tarieven en kortingen gewist.</i>");
	}
	$cms->edit_field(1,1,"toonper","Noteer tarieven per");
	if($_GET["add"]<>1) {
		$cms->edit_field(1,0,"htmlrow","<hr>");
	}
}
if($_GET["wzt"]==2) {
	$cms->edit_field(1,0,"flexibel","Flexibele aankomst/vertrek mogelijk","","",array("info"=>"Zet hier alleen een vinkje als de leverancier via XML flexibele gegevens aanlevert (dat is zeer uitzonderlijk)."));
}
$cms->edit_field(1,0,"bijkomendekosten1_id","Bijkomende kosten 1");
$cms->edit_field(1,0,"bijkomendekosten2_id","Bijkomende kosten 2");
$cms->edit_field(1,0,"bijkomendekosten3_id","Bijkomende kosten 3");
$cms->edit_field(1,0,"bijkomendekosten4_id","Bijkomende kosten 4");
$cms->edit_field(1,0,"bijkomendekosten5_id","Bijkomende kosten 5");
$cms->edit_field(1,0,"bijkomendekosten6_id","Bijkomende kosten 6");
$cms->edit_field(1,1,"zoekvolgorde","Zoekvolgorde",array("selection"=>3));
$cms->edit_field(1,0,"kwaliteit","Kwaliteit");
$cms->edit_field(1,0,"htmlrow","<hr><b>Kenmerken</b>");
#$cms->edit_field(1,0,"kenmerken_gecontroleerd","Alle kenmerken zijn gecontroleerd");
$cms->edit_field(1,1,"kenmerken","Kenmerken");
$cms->edit_field(1,0,"kenmerken_gecontroleerd_datum","Alle kenmerken zijn gecontroleerd op","",array("startyear"=>date("Y")-2,"endyear"=>date("Y")+1),array("calendar"=>true,"info"=>"Na het invullen van deze datum zal er een jaar lang op de CMS-hoofdpagina geen melding worden gemaakt indien er nog onbekende kenmerken zijn."));
$cms->edit_field(1,0,"htmlrow","<a name=\"verblijfsduur\"></a><hr><b>Verblijfsduur</b>");
$cms->edit_field(1,0,"aankomst_plusmin","Aankomst (afwijking in dagen)");
$cms->edit_field(1,0,"vertrek_plusmin","Vertrek (afwijking in dagen)");
$cms->edit_field(1,0,"htmlrow","<hr><b>Teksten</b>");
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"omschrijving","Omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"omschrijving_".$vars["cmstaal"],"Omschrijving ".strtoupper($vars["cmstaal"]),"","",array("rows"=>25));
} else {
	$cms->edit_field(1,0,"omschrijving","Omschrijving","","",array("rows"=>25));
}
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"indeling","Indeling NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"indeling_".$vars["cmstaal"],"Indeling ".strtoupper($vars["cmstaal"]),"","",array("rows"=>25));
} else {
	$cms->edit_field(1,0,"indeling","Indeling","","",array("rows"=>25));
}
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"inclusief","Inclusief NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"inclusief_".$vars["cmstaal"],"Inclusief ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(1,0,"inclusief","Inclusief (verouderd)");
}
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"exclusief","Exclusief NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"exclusief_".$vars["cmstaal"],"Exclusief ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(1,0,"exclusief","Exclusief (verouderd)");
}
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"extraopties","Extra opties NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"extraopties_".$vars["cmstaal"],"Extra opties ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(1,0,"extraopties","Extra opties");
}
if($_GET["wzt"]==2) {
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"reviews","Reviews NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"reviews_".$vars["cmstaal"],"Reviews ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"reviews","Reviews","","",array("info"=>"Voer hier accommodatie-reviews van Posarelli in (voorlopig alleen Posarelli, later wellicht ook andere leveranciers)"));
	}
}

# Velden "receptie" en "telefoonnummer" zijn niet meer nodig: komen voortaan uit de vertrekinfo-gegevens (02-01-2013)
// if($vars["cmstaal"]) {
// 	$cms->edit_field(1,0,"receptie","Receptie/Sleutel NL","",array("noedit"=>true));
// 	$cms->edit_field(1,0,"receptie_".$vars["cmstaal"],"Receptie/Sleutel ".strtoupper($vars["cmstaal"]));
// } else {
// 	$cms->edit_field(1,0,"receptie","Receptie/Sleutel");
// }
// $cms->edit_field(1,0,"telefoonnummer","Telefoonnummer");


if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"voucherinfo","Vouchertekst","",array("noedit"=>true));
	$cms->edit_field(1,0,"voucherinfo_".$vars["cmstaal"],"Vouchertekst ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(1,0,"voucherinfo","Vouchertekst");
}

$cms->edit_field(1,0,"mailtekst_id","Mailtekst (8 weken voor vertrek)");
$cms->edit_field(1,1,"optiedagen_klanten_vorig_seizoen","Aantal optiedagen voor klant die het volgende seizoen de accommodatie opnieuw boekt");


$cms->edit_field(1,0,"htmlrow","<hr><b>Afstanden</b>");
$cms->edit_field(1,0,"afstandwinkel","Afstand tot winkel (in meters)");
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"afstandwinkelextra","Toevoeging afstand winkel NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"afstandwinkelextra_".$vars["cmstaal"],"Toevoeging afstand winkel ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(1,0,"afstandwinkelextra","Toevoeging afstand winkel");
}
$cms->edit_field(1,0,"afstandrestaurant","Afstand tot restaurant (in meters)");
$cms->edit_field(1,0,"afstandwinkel","Afstand tot winkel (in meters)");
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"afstandrestaurantextra","Toevoeging afstand restaurant NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"afstandrestaurantextra_".$vars["cmstaal"],"Toevoeging afstand restaurant ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(1,0,"afstandrestaurantextra","Toevoeging afstand restaurant");
}
if($_GET["wzt"]==1) {
	$cms->edit_field(1,0,"afstandpiste","Afstand tot piste (in meters)");
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandpisteextra","Toevoeging afstand piste NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandpisteextra_".$vars["cmstaal"],"Toevoeging afstand piste ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandpisteextra","Toevoeging afstand piste");
	}

	$cms->edit_field(1,0,"afstandskilift","Afstand tot skilift (in meters)");
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandskiliftextra","Toevoeging afstand skilift NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandskiliftextra_".$vars["cmstaal"],"Toevoeging afstand skilift ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandskiliftextra","Toevoeging afstand skilift");
	}

	$cms->edit_field(1,0,"afstandloipe","Afstand tot loipe (in meters)");

	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandloipeextra","Toevoeging afstand loipe NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandloipeextra_".$vars["cmstaal"],"Toevoeging afstand loipe ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandloipeextra","Toevoeging afstand loipe");
	}
	$cms->edit_field(1,0,"afstandskibushalte","Afstand tot skibushalte (in meters)");
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandskibushalteextra","Toevoeging afstand skibushalte NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandskibushalteextra_".$vars["cmstaal"],"Toevoeging afstand skibushalte ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandskibushalteextra","Toevoeging afstand skibushalte");
	}
} else {
	$cms->edit_field(1,0,"afstandstrand","Afstand tot strand (in meters)");
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandstrandextra","Toevoeging afstand strand NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandstrandextra_".$vars["cmstaal"],"Toevoeging afstand strand ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandstrandextra","Toevoeging afstand strand");
	}
	$cms->edit_field(1,0,"afstandzwembad","Afstand tot zwembad (in meters)");
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandzwembadextra","Toevoeging afstand zwembad NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandzwembadextra_".$vars["cmstaal"],"Toevoeging afstand zwembad ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandzwembadextra","Toevoeging afstand zwembad");
	}
	$cms->edit_field(1,0,"afstandzwemwater","Afstand tot zwemwater (in meters)");
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandzwemwaterextra","Toevoeging afstand zwemwater NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandzwemwaterextra_".$vars["cmstaal"],"Toevoeging afstand zwemwater ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandzwemwaterextra","Toevoeging afstand zwemwater");
	}
	$cms->edit_field(1,0,"afstandgolfbaan","Afstand tot golfbaan (in meters)");
	if($vars["cmstaal"]) {
		$cms->edit_field(1,0,"afstandgolfbaanextra","Toevoeging afstand golfbaan NL","",array("noedit"=>true));
		$cms->edit_field(1,0,"afstandgolfbaanextra_".$vars["cmstaal"],"Toevoeging afstand golfbaan ".strtoupper($vars["cmstaal"]));
	} else {
		$cms->edit_field(1,0,"afstandgolfbaanextra","Toevoeging afstand golfbaan");
	}
}

$cms->edit_field(1,0,"htmlrow","<hr><b id=\"images\">Afbeeldingen</b><br><i>Afbeeldingen kunnen in groot formaat worden ge&uuml;pload; het systeem zet ze om naar de juiste afmetingen. De verhouding moet wel altijd 4:3 zijn.</i>");

$cms->edit_field(1, 0, 'mongo_pictures', 'Afbeeldingen', '', array('collection' => 'accommodations', 'file_id' => $_GET['1k0'], 'destination' => dirname(__FILE__) . '/pic/cms/accommodations', 'multiple' => true, 'default_kind' => 'accommodaties_aanvullend', 'kinds' => ['hoofdfoto_accommodatie' => 'Hoofdafbeelding', 'accommodaties' => 'Hoofdafbeelding (overig)', 'accommodaties_aanvullend' => 'Afbeelding (4:3)', 'accommodaties_aanvullend_breed' => 'Afbeelding onderaan (8:3)', 'accommodaties_aanvullend_onderaan' => 'Afbeelding onderaan (4:3)'], 'showfiletype' => true, 'must_be_filetype' => 'jpg', 'img_minwidth' => 240, 'img_minheight' => 180, 'img_ratio_width' => 4, 'img_ratio_height' => 3));

$cms->edit_field(1,0,"htmlrow","<hr><b>Video</b>");
$cms->edit_field(1,0,"video_url","URL van Vimeo");
$cms->edit_field(1,0,"video","Toon deze video op de accommodatiepagina");


# Nieuw vertrekinfo-systeem
$cms->edit_field(1,0,"htmlrow","<a name=\"vertrekinfo\"></a><hr><br><b>Vertrekinfo-systeem</b>");
$cms->edit_field(1,0,"accommodatie_aanvullende_informatie","PDF met voor deze accommodatie specifieke informatie","",array("showfiletype"=>true));
$cms->edit_field(1,0,"htmlrow","<br><i>Alinea 'Inchecken'</i>");
$cms->edit_field(1,0,"vertrekinfo_incheck_sjabloon_id","Sjabloon inchecken");
if($vertrekinfo_tracking["vertrekinfo_incheck_sjabloon_id"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_incheck_sjabloon_id"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_soortbeheer","Type beheer");
if($vertrekinfo_tracking["vertrekinfo_soortbeheer"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer"]))."</div>"));
}

if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"vertrekinfo_soortbeheer_aanvulling","Aanvulling bij type beheer NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"],"Aanvulling bij type beheer ".strtoupper($vars["cmstaal"]));
	if($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]]) {
		$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling_".$vars["cmstaal"]]))."</div>"));
	}
} else {
	$cms->edit_field(1,0,"vertrekinfo_soortbeheer_aanvulling","Aanvulling bij type beheer","","",array("info"=>"Bijvoorbeeld de naam van een contactpersoon: 'Carine'"));
	if($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling"]) {
		$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortbeheer_aanvulling"]))."</div>"));
	}
}
$cms->edit_field(1,0,"vertrekinfo_telefoonnummer","Telefoonnummer beheer","","",array("info"=>"Bijvoorbeeld: '0039 0437 72 38 05'"));
if($vertrekinfo_tracking["vertrekinfo_telefoonnummer"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_telefoonnummer"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_noodtelefoonnummer_accommodatie","Noodtelefoonnummer accommodatie","","",array("info"=>"Bijvoorbeeld: '0039 437 72 38 05'\nLet op: Dit wordt niet naar de klant verstuurd."));
if($vertrekinfo_tracking["vertrekinfo_noodtelefoonnummer_accommodatie"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_noodtelefoonnummer_accommodatie"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_inchecktijd","Inchecktijd","","",array("info"=>"Bijvoorbeeld: '17:00'"));
if($vertrekinfo_tracking["vertrekinfo_inchecktijd"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_inchecktijd"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_uiterlijkeinchecktijd","Uiterlijke inchecktijd","","",array("info"=>"Bijvoorbeeld: '19:00'"));
if($vertrekinfo_tracking["vertrekinfo_uiterlijkeinchecktijd"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_uiterlijkeinchecktijd"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_uitchecktijd","Uitchecktijd","","",array("info"=>"Bijvoorbeeld: '09:00'"));


$cms->edit_field(1,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Routebeschrijving naar de receptie of accommodatie' (wordt toegevoegd aan de routebeschrijving naar de betreffende plaats)</i>");
$db0->query("SELECT vertrekinfo_plaatsroute".($vars["cmstaal"] ? "_en" : "")." AS vertrekinfo_plaatsroute FROM plaats WHERE plaats_id='".intval($plaats_id)."';");
if($db0->next_record()) {
	$cms->edit_field(1,0,"htmlcol","Routebeschrijving plaats",array("html"=>nl2br(wt_he($db0->f("vertrekinfo_plaatsroute")))));
}
if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"vertrekinfo_route","Routebeschrijving NL","",array("noedit"=>true));
	$cms->edit_field(1,0,"vertrekinfo_route_".$vars["cmstaal"],"Routebeschrijving ".strtoupper($vars["cmstaal"]));
	if($vertrekinfo_tracking["vertrekinfo_route_".$vars["cmstaal"]]) {
		$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_route_".$vars["cmstaal"]]))."</div>"));
	}
} else {
	$cms->edit_field(1,0,"vertrekinfo_route","Routebeschrijving");
	if($vertrekinfo_tracking["vertrekinfo_route"]) {
		$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_route"]))."</div>"));
	}
}
$cms->edit_field(1,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'Adres'</i>");
$cms->edit_field(1,0,"vertrekinfo_soortadres","Type adres");
if($vertrekinfo_tracking["vertrekinfo_soortadres"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_soortadres"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_adres","Adres");
if($vertrekinfo_tracking["vertrekinfo_adres"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_adres"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_plaatsnaam_beheer","Afwijkende plaatsnaam beheer","","",array("info"=>"Alleen invullen indien het beheer zich in een andere plaats dan de accommodatie bevindt."));
if($vertrekinfo_tracking["vertrekinfo_plaatsnaam_beheer"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_plaatsnaam_beheer"]))."</div>"));
}
$cms->edit_field(1,0,"htmlrow","<br><hr class=\"greyhr\"><br><i>Alinea 'GPS-co&ouml;rdinaten'</i>");
if($vertrekinfo_tracking["vertrekinfo_gps_lat"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_gps_lat"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_gps_lat","GPS latitude beheer","","",array("info"=>"Alleen invullen indien deze afwijkt van de accommodatie-GPS-coördinaten. Vul de breedtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 52.086508"));
if($vertrekinfo_tracking["vertrekinfo_gps_long"]) {
	$cms->edit_field(1,0,"htmlcol","Bij laatste goedkeuring",array("html"=>"<div class=\"vertrekinfo_tracking_voorheen\">".nl2br(wt_he($vertrekinfo_tracking["vertrekinfo_gps_long"]))."</div>"));
}
$cms->edit_field(1,0,"vertrekinfo_gps_long","GPS longitude beheer","","",array("info"=>"Alleen invullen indien deze afwijkt van de accommodatie-GPS-coördinaten. Vul de lengtegraad in. Gebruik alleen cijfers en een punt, bijvoorbeeld: 4.886513"));

if($vars["cmstaal"]) {
	$cms->edit_field(1,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo ".strtoupper($vars["cmstaal"])."</b>");
	$cms->edit_field(1,0,"vertrekinfo_goedgekeurd_seizoen_".$vars["cmstaal"],"Vertrekinfo is goedgekeurd voor seizoen ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
	$cms->edit_field(1,0,"vertrekinfo_goedgekeurd_datetime_".$vars["cmstaal"],"Laatste goedkeuring ".strtoupper($vars["cmstaal"]),"","",array("one_per_line"=>true));
} else {
	$cms->edit_field(1,0,"htmlrow","<br><hr class=\"greyhr\"><br><b>Goedkeuring bovenstaande vertrekinfo</b>");
	$cms->edit_field(1,0,"vertrekinfo_goedgekeurd_seizoen","Vertrekinfo is goedgekeurd voor seizoen","","",array("one_per_line"=>true));
	$cms->edit_field(1,0,"vertrekinfo_goedgekeurd_datetime","Laatste goedkeuring","","",array("one_per_line"=>true));
}
# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(1);
if($cms_form[1]->filled) {
	if($cms_form[1]->input["aankomst_plusmin"]>0 and $cms_form[1]->input["vertrek_plusmin"]<0) {
		if($cms_form[1]->input["aankomst_plusmin"]+abs($cms_form[1]->input["vertrek_plusmin"])>6) $cms_form[1]->error("vertrek_plusmin","overlap met aankomst");
	}

	# Controle op archief-functie
	if($cms_form[1]->input["archief"]) {
		if($cms_form[1]->input["tonen"]) {
			$cms_form[1]->error("tonen","tonen moet inactief zijn bij een gearchiveerde accommodatie");
		}
	}

	# Controle of juiste taal wel actief is
	if(!$vars["cmstaal"]) {
		while(list($key,$value)=@each($_POST["input"])) {
			if(ereg("^omschrijving_",$key)) {
				$cms_form[1]->error("taalprobleem","De CMS-taal is gewijzigd tijdens het bewerken. Opslaan is niet mogelijk. Ga terug naar het CMS-hoofdmenu en kies de gewenste taal",false,true);
			}
		}
	}

	# Controle op tarievenoptie B (toonper 2)
	if($cms_form[1]->input["toonper"]<>$oud_toonper and $cms_form[1]->input["toonper"]==2) {
		$cms_form[1]->error("toonper","tarievenoptie B is niet meer in gebruik");
	}

	# Controle op wijzigen toonper
	if($accommodatie_heeft_boekingen and $_POST["input"]["toonper"] and $_POST["input"]["toonper"]<>$oud_toonper) {
		if($login->userlevel>=10) {

		} else {
			$cms_form[1]->error("toonper","wijzigen kan niet; er zijn boekingen aan deze accommodatie gekoppeld");
		}
	}

	# Controle op gps_lat
	if($cms_form[1]->input["gps_lat"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[1]->input["gps_lat"])) {
			if(floatval($cms_form[1]->input["gps_lat"])<33.797408767572485 or floatval($cms_form[1]->input["gps_lat"])>71.01695975726373) {
				$cms_form[1]->error("gps_lat","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[1]->error("gps_lat","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}

	# Controle op gps_long
	if($cms_form[1]->input["gps_long"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[1]->input["gps_long"])) {
			if(floatval($cms_form[1]->input["gps_long"])<-9.393310546875 or floatval($cms_form[1]->input["gps_long"])>27.7734375) {
				$cms_form[1]->error("gps_long","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[1]->error("gps_long","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}
	if($cms_form[1]->input["gps_long"]<>"" and !$cms_form[1]->input["gps_lat"]) $cms_form[1]->error("gps_lat","vul zowel latitude als longitude in");
	if($cms_form[1]->input["gps_lat"]<>"" and !$cms_form[1]->input["gps_long"]) $cms_form[1]->error("gps_long","vul zowel latitude als longitude in");

	# Controle op vertrekinfo_gps_lat
	if($cms_form[1]->input["vertrekinfo_gps_lat"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[1]->input["vertrekinfo_gps_lat"])) {
			if(floatval($cms_form[1]->input["vertrekinfo_gps_lat"])<33.797408767572485 or floatval($cms_form[1]->input["vertrekinfo_gps_lat"])>71.01695975726373) {
				$cms_form[1]->error("vertrekinfo_gps_lat","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[1]->error("vertrekinfo_gps_lat","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}

	# Controle op vertrekinfo_gps_long
	if($cms_form[1]->input["vertrekinfo_gps_long"]<>"") {
		if(preg_match("/^-?[0-9]+\.[0-9]+$/",$cms_form[1]->input["vertrekinfo_gps_long"])) {
			if(floatval($cms_form[1]->input["vertrekinfo_gps_long"])<-9.393310546875 or floatval($cms_form[1]->input["vertrekinfo_gps_long"])>27.7734375) {
				$cms_form[1]->error("vertrekinfo_gps_long","opgegeven waarde ligt buiten Europa");
			}
		} else {
			$cms_form[1]->error("vertrekinfo_gps_long","gebruik alleen cijfers, &eacute;&eacute;n punt en eventueel een minteken");
		}
	}
	if($cms_form[1]->input["vertrekinfo_gps_long"]<>"" and !$cms_form[1]->input["vertrekinfo_gps_lat"]) $cms_form[1]->error("vertrekinfo_gps_lat","vul zowel latitude als longitude in");
	if($cms_form[1]->input["vertrekinfo_gps_lat"]<>"" and !$cms_form[1]->input["vertrekinfo_gps_long"]) $cms_form[1]->error("gps_long","vul zowel latitude als longitude in");

	# Controle op vertrekinfo_soortadres bij invullen vertrekinfo_adres
	if($cms_form[1]->input["vertrekinfo_adres"] and !$cms_form[1]->input["vertrekinfo_soortadres"]) {
		$cms_form[1]->error("vertrekinfo_soortadres","obl");
	}

	# Bij wijzigen van "toonper" alle tarieven wissen
	if($cms_form[1]->input["toonper"]<>$oud_toonper and $oud_toonper and $cms_form[1]->input["toonper"] and (!$accommodatie_heeft_boekingen or $login->userlevel>=10) and !$cms_form[1]->error and $_GET["1k0"] and $_GET["edit"]==1) {
		$db->query("SELECT type_id FROM type WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
		while($db->next_record()) {
			# tabel tarief wissen
			$db2->query("DELETE FROM tarief WHERE type_id='".$db->f("type_id")."';");

			# tabel tarief_personen
			$db2->query("DELETE FROM tarief_personen WHERE type_id='".$db->f("type_id")."';");

			# kortingen wissen
			$db2->query("DELETE FROM korting WHERE type_id='".$db->f("type_id")."';");

			# boekingen wissen
			$db2->query("DELETE FROM boeking WHERE type_id='".$db->f("type_id")."';");
		}
	}

	# Controle op aanwezige video_url
	if($cms_form[1]->input["video"] and !$cms_form[1]->input["video_url"]) {
		$cms_form[1]->error("video_url","obl");
	}

	# Controle op Vimeo-link
	if($cms_form[1]->input["video_url"] and !preg_match("/^https:\/\/player\.vimeo\.com\/video\/[0-9]+$/",$cms_form[1]->input["video_url"])) {
		$cms_form[1]->error("video_url","onjuist formaat. Voorbeeld: https://player.vimeo.com/video/44377043");
	}

}

# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars;

	# datum pdf-upload vastleggen
	if($form->settings["fullname"]=="cms_1") {
		if($form->upload_okay["route"]) {
			if($form->db_insert_id) {
				$accid=$form->db_insert_id;
			} elseif($_GET["1k0"]) {
				$accid=$_GET["1k0"];
			}
			if($accid) {
				if($vars["cmstaal"]) $ext="_".$vars["cmstaal"];
				$db->query("UPDATE accommodatie SET pdfupload_user".$ext."='".addslashes($login->user_id)."', pdfupload_datum".$ext."=NOW() WHERE accommodatie_id='".addslashes($accid)."';");
			}
		}
	}

	if($_GET["1k0"]) {

		# wijziging in websites: op alle onderliggende types doorvoeren

		# oud bepalen
		$oud=split(",",$form->fields["previous"]["websites"]["selection"]);
		reset($vars["websites_wzt"][$_GET["wzt"]]);
		while(list($key,$value)=each($vars["websites_wzt"][$_GET["wzt"]])) {
			if(in_array($key,$oud)) {
				if($oud_goedevolgorde) $oud_goedevolgorde.=",".$key; else $oud_goedevolgorde=$key;
			}
		}

		# nieuw bepalen
		$nieuw=split(",",$form->input["websites"]);
		reset($vars["websites_wzt"][$_GET["wzt"]]);
		while(list($key,$value)=each($vars["websites_wzt"][$_GET["wzt"]])) {
			if(in_array($key,$nieuw)) {
				if($nieuw_goedevolgorde) $nieuw_goedevolgorde.=",".$key; else $nieuw_goedevolgorde=$key;
			}
		}

		if($oud_goedevolgorde<>$nieuw_goedevolgorde) {
			$db->query("UPDATE type SET websites='".addslashes($nieuw_goedevolgorde)."' WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
		}
	}

	# afbeeldingen verplaatsen en omzetten
	if(is_array($form->upload_filename)) {
		while(list($key,$value)=each($form->upload_filename)) {

			if(preg_match("/pic\/cms\/accommodaties\//",$key)) {
				# hoofdfoto

				# thumbnail aanmaken
				wt_create_thumbnail("pic/cms/accommodaties/".basename($key),"pic/cms/accommodaties_tn/".basename($key),60,45);
				chmod("pic/cms/accommodaties_tn/".basename($key),0666);
				filesync::add_to_filesync_table("pic/cms/accommodaties_tn/".basename($key));

				# afbeelding naar juiste maat omzetten
				wt_create_thumbnail("pic/cms/accommodaties/".basename($key),"pic/cms/accommodaties/".basename($key),240,180);
				chmod("pic/cms/accommodaties/".basename($key),0666);
				filesync::add_to_filesync_table("pic/cms/accommodaties/".basename($key));
			}
		}
	}
}

function form_after_imagedelete($form) {
	# afbeeldingen wissen
	if(is_array($form->deleted_images)) {
		while(list($key,$value)=each($form->deleted_images)) {
			if(preg_match("/pic\/cms\/accommodaties\//",$key)) {
				# hoofdfoto: thumbnail wissen
				delete_file("pic/cms/accommodaties_tn/".basename($key));
			}
		}
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[1]="accommodatiegegevens";
$cms->show_mainfield[1]="naam";
#$cms->show_field(1,"naam");
#$cms->show_field(1,"picklein","","",array("colspan2"=>true,"align"=>"center"));
#$cms->show_field(1,"accommodatie_id","ID");
$cms->show_field(1,"leverancier_id","Leverancier");
$cms->show_field(1,"plaats_id","Plaats");
$cms->show_field(1,"naam","Naam");
$cms->show_field(1,"skipas_id","Skipas");
$cms->show_field(1,"tonen");
$cms->show_field(1,"toonper","Tarieventype");
$cms->show_field(1,"aantekeningen");
$cms->show_field(1,"picgroot","Afbeelding");
#$cms->show_field(1,"route","Route");
#$cms->show_field(1,"picklein","Af","",array("colspan2"=>true));


# Controle op delete-opdracht
if($_GET["delete"]==1 and $_GET["1k0"]) {
	$db->query("SELECT type_id FROM type WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
	if($db->next_record()) {
		$cms->delete_error(1,"Deze accommodatie bevat nog gekoppelde types");
	}
}


#
# DELETEn van andere tabellen
#
if($cms->set_delete_init(1)) {

}

#
#
# Types
#
#
$cms->settings[1]["connect"][]=2;
$cms->settings[2]["parent"]=1;

$cms->settings[2]["list"]["show_icon"]=true;
$cms->settings[2]["list"]["edit_icon"]=true;
$cms->settings[2]["list"]["delete_icon"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->settings[2]["prevalue"]["accommodatie_id"]=$_GET["1k0"];
$cms->db[2]["where"]="accommodatie_id='".addslashes($_GET["1k0"])."'";

# Verzameltypes
$db->query("SELECT type_id, begincode, verzameltype, verzameltype_parent FROM view_accommodatie WHERE verzameltype=1 OR verzameltype_parent>0 ORDER BY verzameltype DESC;");
while($db->next_record()) {
	if($db->f("verzameltype")) {
		$vars["verzameltypes"][$db->f("type_id")]=$db->f("begincode").$db->f("type_id")."  verzamel";
		$verzameltype_naam[$db->f("type_id")]=$db->f("begincode").$db->f("type_id");
	} else {
		$vars["verzameltypes"][$db->f("type_id")]=$verzameltype_naam[$db->f("verzameltype_parent")]." gekoppeld";
	}
}

#echo wt_dump($vars["verzameltypes"]);
#exit;

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(2,"noedit","type_id");
$cms->db_field(2,"select","verzameltypekoppeling","type_id",array("selection"=>$vars["verzameltypes"]));
$cms->db_field(2,"text","code");
$cms->db_field(2,"text","naam");
$cms->db_field(2,"integer","optimaalaantalpersonen");
$cms->db_field(2,"integer","maxaantalpersonen");
$cms->db_field(2,"yesno","tonen");
$cms->db_field(2,"select","leverancier_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));
$cms->db_field(2,"checkbox","websites","",array("selection"=>$vars["websites_wzt"][$_GET["wzt"]]));
$cms->db_field(2,"yesno","verzameltype");
$cms->db_field(2,"yesno","controleren");

#$cms->db_field(2,"yesno","shortlist");



# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[2]=array("verzameltypekoppeling","optimaalaantalpersonen","maxaantalpersonen","naam");
#$cms->list_sort_desc[2]=true;
$cms->list_field(2,"verzameltypekoppeling","Verzamel");
$cms->list_field(2,"leverancier_id","Leverancier");
$cms->list_field(2,"type_id","ID");
$cms->list_field(2,"naam","Naam");
$cms->list_field(2,"code","Code");
$cms->list_field(2,"optimaalaantalpersonen","Min");
$cms->list_field(2,"maxaantalpersonen","Max");
$cms->list_field(2,"tonen","Tonen");
$cms->list_field(2,"websites","Sites");
if($_GET["controleren"]) {
	$cms->list_field(2,"controleren","Nakijken");
}
#$cms->list_field(2,"shortlist","Shortlist");



# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>
