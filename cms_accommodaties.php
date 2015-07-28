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
