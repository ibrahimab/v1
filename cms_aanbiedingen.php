<?php

#
# Aanbiedingen-CMS
#


$mustlogin=true;

include("admin/vars.php");

if($_POST["csvimport"] and $_FILES["csv"]["tmp_name"] and $_GET["14k0"]) {
	$regels=file($_FILES["csv"]["tmp_name"]);
	if(is_array($regels)) {
		while(list($key,$value)=each($regels)) {
			$onderdeel=split(";",$value);
			$onderdeel[0]=trim($onderdeel[0]);
			$onderdeel[1]=trim($onderdeel[1]);
			if($onderdeel[0] and preg_match("/^([A-Z]{1,2})([0-9]+)$/",$onderdeel[0],$regs)) {
				$db->query("SELECT type_id, accommodatie_id FROM view_accommodatie WHERE begincode='".$regs[1]."' AND type_id='".$regs[2]."';");
				if($db->next_record()) {
					if($onderdeel[1]=="ja") {
						$db2->query("SELECT type_id FROM type WHERE accommodatie_id='".$db->f("accommodatie_id")."' AND tonen=1;");
						while($db2->next_record()) {
							$db3->query("INSERT INTO aanbieding_type SET type_id='".addslashes($db2->f("type_id"))."', aanbieding_id='".addslashes($_GET["14k0"])."';");
#							echo $db3->lastquery."<br>\n";
						}
					} else {
						$db2->query("INSERT INTO aanbieding_type SET type_id='".addslashes($db->f("type_id"))."', aanbieding_id='".addslashes($_GET["14k0"])."';");
#						echo $db2->lastquery."<br>";
					}
				}
			}
		}
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

# Toegevoegde accommodaties opslaan
if($_POST["toevoegen_filled"]) {
	if($_POST["accommodaties"]) {
		$db->query("INSERT INTO aanbieding_accommodatie SET accommodatie_id='".addslashes($_POST["accommodaties"])."', aanbieding_id='".addslashes($_GET["14k0"])."';");
	}
	if($_POST["types"]) {
		$db->query("INSERT INTO aanbieding_type SET type_id='".addslashes($_POST["types"])."', aanbieding_id='".addslashes($_GET["14k0"])."';");
	}
#	if($_POST["leveranciers"]) {
#		$db->query("INSERT INTO aanbieding_leverancier SET leverancier_id='".addslashes($_POST["leveranciers"])."', aanbieding_id='".addslashes($_GET["14k0"])."';");
#	}
	if($_POST["leveranciers_types"]) {
		$db->query("SELECT DISTINCT t.type_id FROM accommodatie a, type t WHERE a.wzt='".addslashes($_GET["wzt"])."' AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND t.tonen=1 AND a.archief=0 AND t.leverancier_id='".addslashes($_POST["leveranciers_types"])."';");
		while($db->next_record()) {
			$db2->query("INSERT INTO aanbieding_type SET type_id='".addslashes($db->f("type_id"))."', aanbieding_id='".addslashes($_GET["14k0"])."';");
		}
	}

	if($_POST["landen"]) {
#		$db->query("SELECT DISTINCT lev.leverancier_id FROM leverancier lev, accommodatie a, type t, plaats p, land l WHERE a.wzt='".addslashes($_GET["wzt"])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND t.leverancier_id=lev.leverancier_id AND lev.beheerder=0 AND a.tonen=1 AND t.tonen=1 AND a.archief=0 AND l.land_id='".addslashes($_POST["landen"])."';");
		$db->query("SELECT DISTINCT t.type_id FROM leverancier lev, accommodatie a, type t, plaats p, land l WHERE a.wzt='".addslashes($_GET["wzt"])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND t.leverancier_id=lev.leverancier_id AND lev.beheerder=0 AND a.tonen=1 AND t.tonen=1 AND a.archief=0 AND l.land_id='".addslashes($_POST["landen"])."';");
		while($db->next_record()) {
#			$db2->query("INSERT INTO aanbieding_leverancier SET leverancier_id='".addslashes($db->f("leverancier_id"))."', aanbieding_id='".addslashes($_GET["14k0"])."';");
			$db2->query("INSERT INTO aanbieding_type SET type_id='".addslashes($db->f("type_id"))."', aanbieding_id='".addslashes($_GET["14k0"])."';");
		}
	}
	header("Location: cms_aanbiedingen.php?".$_SERVER["QUERY_STRING"]);
	exit;
}

# Aankomstdata opslaan
#echo wt_dump($_POST);
if($_POST["aankomstdata_filled"]) {
	$db->query("DELETE FROM aanbieding_aankomstdatum WHERE aanbieding_id='".addslashes($_GET["14k0"])."';");
	if(is_array($_POST["aankomstdatum"])) {
		reset($_POST["aankomstdatum"]);
		while(list($key,$value)=each($_POST["aankomstdatum"])) {
			$db->query("INSERT INTO aanbieding_aankomstdatum SET week='".addslashes($key)."', aanbieding_id='".addslashes($_GET["14k0"])."';");
#			echo $db->lastquery;
		}
	}
	header("Location: cms_aanbiedingen.php?".$_SERVER["QUERY_STRING"]);
	exit;
}

# Aankomstdata uit database halen
$db->query("SELECT week FROM aanbieding_aankomstdatum WHERE aanbieding_id='".addslashes($_GET["14k0"])."' ORDER BY week;");
while($db->next_record()) {
	$vars["aanbiedingen_aankomstdata"][$db->f("week")]="Weekend ".date("j",$db->f("week"))." ".datum("MAAND JJJJ",$db->f("week"));
}


$cms->settings[14]["list"]["show_icon"]=true;
$cms->settings[14]["list"]["edit_icon"]=true;
$cms->settings[14]["list"]["delete_icon"]=true;

$cms->settings[14]["show"]["goto_new_record"]=true;
#$cms->settings[14]["show"]["goto_changed_record"]=true;
#$cms->settings[14]["edit"]["top_submit_button"]=true;

if($_GET["t"]==2) {
	$cms->db[14]["where"]="wzt='".addslashes($_GET["wzt"])."' AND tonen=0 AND archief=0";
} elseif($_GET["t"]==3) {
	$cms->db[14]["where"]="wzt='".addslashes($_GET["wzt"])."' AND archief=1";
} elseif($_GET["t"]==1) {
	$cms->db[14]["where"]="wzt='".addslashes($_GET["wzt"])."' AND tonen=1";
} else {
	$cms->db[14]["where"]="wzt='".addslashes($_GET["wzt"])."'";
}
$cms->db[14]["set"]="wzt='".addslashes($_GET["wzt"])."'";

# Zomer- of winter-sites in $temp_vars opslaan
while(list($key,$value)=each($vars["websitetype_namen"])) {
	if($vars["websitetype_namen_wzt"][$key]==$_GET["wzt"] or $key==6) {
		$temp_vars["websitetype_namen"][$key]=$value;
		if($standaard_aan) $standaard_aan.=",".$key; else $standaard_aan=$key;
	}
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(14,"select","soort","",array("selection"=>$vars["aanbieding_soort_cms"]));
$cms->db_field(14,"checkbox","toon_abpagina","",array("selection"=>$temp_vars["websitetype_namen"]));
#$cms->db_field(14,"select","seizoen_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam");
$cms->db_field(14,"select","seizoen1_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"type='".addslashes($_GET["wzt"])."'"));
$cms->db_field(14,"select","seizoen2_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"type='".addslashes($_GET["wzt"])."'"));
$cms->db_field(14,"select","seizoen3_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"type='".addslashes($_GET["wzt"])."'"));
$cms->db_field(14,"select","seizoen4_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"type='".addslashes($_GET["wzt"])."'"));
$cms->db_field(14,"text","naam");
$cms->db_field(14,"text","onlinenaam");
if($vars["cmstaal"]) $cms->db_field(14,"text","onlinenaam_".$vars["cmstaal"]);
$cms->db_field(14,"textarea","omschrijving");
if($vars["cmstaal"]) $cms->db_field(14,"textarea","omschrijving_".$vars["cmstaal"]);
$cms->db_field(14,"yesno","tonen");
$cms->db_field(14,"yesno","archief");
#$cms->db_field(14,"yesno","toon_abpagina");
$cms->db_field(14,"yesno","toon_als_aanbieding");
$cms->db_field(14,"select","bedrag_soort","",array("selection"=>$vars["bedrag_soort"]));
$cms->db_field(14,"yesno","toonkorting");
$cms->db_field(14,"integer","bedrag");
$cms->db_field(14,"date","begindatum");
$cms->db_field(14,"date","einddatum");
$cms->db_field(14,"textarea","opmerkingen_intern");

if($_GET["wzt"]==2) {
	$cms->db_field(14,"yesno","opval_tonen");
	$cms->db_field(14,"date","opval_begindatum");
	$cms->db_field(14,"date","opval_einddatum");
	$cms->db_field(14,"text","opval_regel1");
	$cms->db_field(14,"text","opval_regel2");
	$cms->db_field(14,"text","opval_regel3");
	$cms->db_field(14,"radio","opval_land","",array("othertable"=>"6","otherkeyfield"=>"land_id","otherfield"=>"naam","otherwhere"=>"zomertonen=1"));
	$cms->db_field(14,"integer","opval_volgorde");
	$cms->db_field(14,"integer","volgorde1_abpagina");
	$cms->db_field(14,"text","opval_linknaar");
	$cms->db_field(14,"picture","opval_afbeelding","",array("savelocation"=>"pic/cms/aanbieding_opval/","filetype"=>"jpg","multiple"=>false));
}

# List list_field($counter,$id,$title="",$options="",$layout="")
if($_GET["wzt"]==2) {
	$cms->list_sort[14]=array("begindatum","einddatum","volgorde1_abpagina");
} else {
	$cms->list_sort[14]=array("begindatum","einddatum");
}
$cms->list_field(14,"naam","Naam");
$cms->list_field(14,"bedrag_soort","Soort");
$cms->list_field(14,"bedrag","Bedrag");


#$cms->list_field(14,"begindatum","Begindatum");
#$cms->list_field(14,"einddatum","Einddatum");
$cms->list_field(14,"begindatum","",array("date_format"=>"DD-MM-JJJJ"));
$cms->list_field(14,"einddatum","",array("date_format"=>"DD-MM-JJJJ"));
if($_GET["wzt"]==2 and $_GET["t"]==1) {
	$cms->list_field(14,"volgorde1_abpagina","Volgorde");
	$cms->list_field(14,"opval_volgorde","Opval-volgorde");
}


# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(14,0,"tonen","Actief",array("selection"=>true));
$cms->edit_field(14,0,"archief","Archief",array("selection"=>false));
$cms->edit_field(14,0,"htmlrow","<hr><i>Alle aanbiedingen worden op alle sites getoond op de accommodatiepagina's. Wel is per site aan te geven of een aanbieding op de overzichtspagina \"Aanbiedingen\" moet worden getoond.</i>");
$cms->edit_field(14,0,"toon_abpagina","Tonen in overzicht op aanbiedingenpagina",array("selection"=>$standaard_aan),"",array("one_per_line"=>true));
if($_GET["wzt"]==2) {
	$cms->edit_field(14,0,"volgorde1_abpagina","Volgorde op aanbiedingenpagina");
}
$cms->edit_field(14,0,"htmlrow","<hr>");
$cms->edit_field(14,1,"soort","Soort",array("selection"=>1));

$db->query("SELECT seizoen_id FROM seizoen WHERE UNIX_TIMESTAMP(eind)>=".time()." AND type='".addslashes($_GET["wzt"])."' ORDER BY begin LIMIT 0,1;");
if($db->next_record()) {
	$huidig_seizoen=$db->f("seizoen_id");
}

#$cms->edit_field(14,1,"seizoen_id","Seizoen",array("selection"=>$huidig_seizoen));
$cms->edit_field(14,1,"seizoen1_id","Seizoen 1",array("selection"=>$huidig_seizoen));
$cms->edit_field(14,0,"seizoen2_id","Seizoen 2");
$cms->edit_field(14,0,"seizoen3_id","Seizoen 3");
$cms->edit_field(14,0,"seizoen4_id","Seizoen 4");
$cms->edit_field(14,1,"naam","Interne naam");
if($vars["cmstaal"]) {
	$cms->edit_field(14,0,"onlinenaam","Naam voor bezoekers NL","",array("noedit"=>true));
	$cms->edit_field(14,0,"onlinenaam_".$vars["cmstaal"],"Naam voor bezoekers ".strtoupper($vars["cmstaal"]));

	$cms->edit_field(14,0,"omschrijving","Omschrijving NL","",array("noedit"=>true));
	$cms->edit_field(14,0,"omschrijving_".$vars["cmstaal"],"Omschrijving ".strtoupper($vars["cmstaal"]));
} else {
	$cms->edit_field(14,0,"onlinenaam","Naam voor bezoekers");
	$cms->edit_field(14,0,"omschrijving","Omschrijving");
}

$cms->edit_field(14,1,"bedrag_soort","Soort bedrag",array("selection"=>4));
$cms->edit_field(14,0,"toon_als_aanbieding","Toon als aanbieding in de tarieventabel",array("selection"=>true));
$cms->edit_field(14,1,"toonkorting","Toon kortingsbedrag/percentage",array("selection"=>true));
$cms->edit_field(14,0,"bedrag","Bedrag");
$cms->edit_field(14,1,"begindatum","Tonen vanaf",array("time"=>time()),"",array("calendar"=>true));
$cms->edit_field(14,0,"einddatum","Tonen tot en met","","",array("calendar"=>true));
$cms->edit_field(14,0,"htmlrow","<hr>");
$cms->edit_field(14,0,"opmerkingen_intern","Opmerkingen (intern)","",array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
if($_GET["wzt"]==2) {

	if($_POST["input"]["opval_tonen"]) {
		$opval_obl=1;
	} else {
		$opval_obl=0;
	}
	$cms->edit_field(14,0,"htmlrow","<hr><b>Deze aanbieding opvallend tonen op de aanbiedingenpagina (aan de rechterkant)</b><br><br><span style=\"color:red;\">Belangrijk: controleer of de teksten binnen het blok passen!</span>");
	$cms->edit_field(14,0,"opval_tonen","Toon deze aanbieding opvallend");
	$cms->edit_field(14,$opval_obl,"opval_regel1","Regel 1","","",array("info"=>"Vul hier de regio in waar de accommodatie gelegen is."));
	$cms->edit_field(14,$opval_obl,"opval_regel2","Regel 2","","",array("info"=>"Vul hier een pakkende zin, 'trigger', in."));
	$cms->edit_field(14,$opval_obl,"opval_regel3","Regel 3","","",array("info"=>"Vul hier de korting in die de klant krijgt (vb -30% bij 2 weeks verblijf)"));
#	$cms->edit_field(14,$opval_obl,"opval_begindatum","Opvallend tonen vanaf","","",array("calendar"=>true));
#	$cms->edit_field(14,0,"opval_einddatum","Opvallend tonen tot en met","","",array("calendar"=>true));
	$cms->edit_field(14,$opval_obl,"opval_land","Toon alleen bij het volgende land","","",array("one_per_line"=>true));
	$cms->edit_field(14,$opval_obl,"opval_volgorde","Volgorde");
	$cms->edit_field(14,$opval_obl,"opval_afbeelding","Afbeelding","",array("autoresize"=>true,"img_width"=>"200","img_height"=>"133","img_ratio_width"=>"3","img_ratio_height"=>"2"));
	$cms->edit_field(14,0,"htmlrow","<br><i>Link: geef een URL op. Bij leeglaten: link gaat naar overzicht van de aanbieding.</i>");
	$cms->edit_field(14,0,"opval_linknaar","Link (zonder 'http://www.zomerhuisje.nl')");
}


# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(14);
if($cms_form[14]->filled) {

	# Voortaan: alleen tekstaanbiedingen toestaan (29-01-2013)
	if($cms_form[14]->input["soort"]<>1) {
		$cms_form[14]->error("soort","tegenwoordig zijn alleen aanbiedingen met toelichting (zonder bedrag) mogelijk");
	}
	if($cms_form[14]->input["bedrag_soort"]<>4) {
		$cms_form[14]->error("bedrag_soort","tegenwoordig zijn alleen aanbiedingen met toelichting (zonder bedrag) mogelijk");
	}

	# Geen omschrijving bij lastminute toegestaan
	if($cms_form[14]->input["soort"]==2 and $cms_form[14]->input["omschrijving"]) {
		$cms_form[14]->error("omschrijving","niet van toepassing bij 'Aanbieding zonder toelichting'");
	}
	if($cms_form[14]->input["soort"]==2 and $cms_form[14]->input["onlinenaam"]) {
		$cms_form[14]->error("onlinenaam","niet van toepassing bij 'Aanbieding zonder toelichting'");
	}
	if($cms_form[14]->input["omschrijving"] and !$cms_form[14]->input["onlinenaam"]) {
		$cms_form[14]->error("onlinenaam","verplicht indien er een omschrijving is ingevoerd");
	}

	if($cms_form[14]->input["archief"] and $cms_form[14]->input["tonen"]) {
		$cms_form[14]->error("archief","archiveren kan niet als de aanbieding actief is");
	}

	if($cms_form[14]->input["bedrag_soort"]==4) {
		if($cms_form[14]->input["toon_abpagina"] and $_GET["wzt"]<>2) $cms_form[14]->error("toon_abpagina","niet toegestaan bij \"geen bedrag\"");
		if($cms_form[14]->input["bedrag"]) $cms_form[14]->error("bedrag","niet toegestaan bij \"geen bedrag\"");
		if(!$cms_form[14]->input["omschrijving"]) $cms_form[14]->error("omschrijving","verplicht bij \"geen bedrag\"");
	} else {
		if(!$cms_form[14]->input["bedrag"]) $cms_form[14]->error("bedrag","verplicht bij dit soort bedrag");
	}

	if($cms_form[14]->input["bedrag_soort"]==3) {
		$db->query("SELECT a.accommodatie_id FROM accommodatie a, aanbieding_accommodatie aa WHERE aa.aanbieding_id='".addslashes($_GET["14k0"])."' AND aa.accommodatie_id=a.accommodatie_id AND a.toonper<>3;");
		if($db->next_record()) {
			$verkeerde_optie_gekoppeld=true;
		}
		$db->query("SELECT a.accommodatie_id FROM accommodatie a, type t, aanbieding_type at WHERE at.aanbieding_id='".addslashes($_GET["14k0"])."' AND at.type_id=t.type_id AND a.accommodatie_id=t.accommodatie_id AND a.toonper<>3;");
		if($db->next_record()) {
			$verkeerde_optie_gekoppeld=true;
		}
		if($verkeerde_optie_gekoppeld) $cms_form[14]->error("bedrag_soort","er zijn nog optie A/B-accommodaties aan deze aanbieding gekoppeld");
	}

	if($cms_form[14]->input["opval_linknaar"]) {
		if(ereg("http",$cms_form[14]->input["opval_linknaar"])) {
			$cms_form[14]->error("opval_linknaar","laat 'http' weg");
		}
		if(substr($cms_form[14]->input["opval_linknaar"],0,1)<>"/") {
			$cms_form[14]->error("opval_linknaar","moet beginnen met een slash (/)");
		}
	}
}

# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	$db2=new DB_sql;
	global $login,$vars;

	if($_GET["wzt"]==2) {
		$volgorde=0;
		$db->query("SELECT aanbieding_id FROM aanbieding WHERE wzt='".addslashes($_GET["wzt"])."' AND opval_volgorde>0 ORDER BY opval_volgorde;");
		while($db->next_record()) {
			$volgorde=$volgorde+10;
			$db2->query("UPDATE aanbieding SET opval_volgorde='".$volgorde."' WHERE aanbieding_id='".$db->f("aanbieding_id")."';");
		}

		$volgorde=0;
		$db->query("SELECT aanbieding_id, volgorde1_abpagina FROM aanbieding WHERE tonen=1 AND wzt='".addslashes($_GET["wzt"])."' ORDER BY volgorde1_abpagina;");
		while($db->next_record()) {
			if($db->f("volgorde1_abpagina")=="" or $db->f("volgorde1_abpagina")==2000) {
				$db2->query("UPDATE aanbieding SET volgorde1_abpagina='2000' WHERE aanbieding_id='".$db->f("aanbieding_id")."';");
			} else {
				$volgorde=$volgorde+10;
				$db2->query("UPDATE aanbieding SET volgorde1_abpagina='".$volgorde."' WHERE aanbieding_id='".$db->f("aanbieding_id")."';");
			}
		}
		$db->query("UPDATE aanbieding SET volgorde1_abpagina=NULL WHERE wzt='".addslashes($_GET["wzt"])."' AND tonen<>1;");
	}
}

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[14]="aanbieding";
$cms->show_mainfield[14]="naam";
#$cms->show_field(14,"naam","Naam");
$cms->show_field(14,"soort","Soort");
$cms->show_field(14,"bedrag_soort","Soort bedrag");
$cms->show_field(14,"omschrijving","Omschrijving");


# Controle op delete-opdracht
if($_GET["delete"]==14 and $_GET["14k0"]) {

}

#
# DELETEn van andere tabellen
#
if($cms->set_delete_init(14)) {
	$db->query("DELETE FROM aanbieding_aankomstdatum WHERE aanbieding_id='".addslashes($_GET["14k0"])."';");
	$db->query("DELETE FROM aanbieding_accommodatie WHERE aanbieding_id='".addslashes($_GET["14k0"])."';");
	$db->query("DELETE FROM aanbieding_leverancier WHERE aanbieding_id='".addslashes($_GET["14k0"])."';");
	$db->query("DELETE FROM aanbieding_type WHERE aanbieding_id='".addslashes($_GET["14k0"])."';");
}

#
# Koppeling met aanbieding_accommodatie
#
$cms->settings[14]["connect"][]=15;
$cms->settings[15]["parent"]=14;

$cms->settings[15]["list"]["show_icon"]=false;
$cms->settings[15]["list"]["edit_icon"]=false;
$cms->settings[15]["list"]["delete_icon"]=true;
$cms->settings[15]["list"]["add_link"]=false;
$cms->settings[15]["list"]["delete_checkbox"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->db[15]["where"]="aanbieding_id='".addslashes($_GET["14k0"])."'";

$db->query("SELECT a.accommodatie_id, a.internenaam AS naam, p.naam AS plaats FROM accommodatie a, aanbieding_accommodatie aa, plaats p WHERE a.plaats_id=p.plaats_id AND a.accommodatie_id=aa.accommodatie_id AND aa.aanbieding_id='".addslashes($_GET["14k0"])."' ORDER BY p.naam, a.naam;");
while($db->next_record()) {
	$vars["aanbiedingen_accommodaties"][$db->f("accommodatie_id")]=$db->f("plaats")." - ".$db->f("naam").($login->userlevel>=10 ? " (".$db->f("accommodatie_id").")" : "");
	if($vars["inquery_accommodatie"]) $vars["inquery_accommodatie"].=",".$db->f("accommodatie_id"); else $vars["inquery_accommodatie"]=$db->f("accommodatie_id");
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(15,"select","accommodatie_id","",array("selection"=>$vars["aanbiedingen_accommodaties"]));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[15]=array("accommodatie_id");
$cms->list_field(15,"accommodatie_id","Naam");


#
# Koppeling met aanbieding_type
#
$cms->settings[14]["connect"][]=16;
$cms->settings[16]["parent"]=14;

$cms->settings[16]["list"]["show_icon"]=false;
$cms->settings[16]["list"]["edit_icon"]=false;
$cms->settings[16]["list"]["delete_icon"]=true;
$cms->settings[16]["list"]["add_link"]=false;
$cms->settings[16]["list"]["delete_checkbox"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->db[16]["where"]="aanbieding_id='".addslashes($_GET["14k0"])."'";

#$db->query("SELECT a.accommodatie_id, a.naam AS anaam, t.naam AS tnaam, p.naam AS plaats FROM accommodatie a, type t, aanbieding_type at, plaats p WHERE a.plaats_id=p.plaats_id AND a.accommodatie_id=t.accommodatie_id AND at.type_id=t.type_id AND at.aanbieding_id='".addslashes($_GET["14k0"])."' ORDER BY p.naam, a.naam, t.naam;");
$db->query("SELECT a.naam AS accommodatie, a.soortaccommodatie, a.toonper, a.accommodatie_id, t.type_id, t.naam AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l, aanbieding_type at WHERE at.type_id=t.type_id AND at.aanbieding_id='".addslashes($_GET["14k0"])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id".($vars["inquery_type"] ? " AND t.type_id NOT IN (".$vars["inquery_type"].")" : "")." ORDER BY p.naam, a.naam, t.type_id, t.naam, t.maxaantalpersonen;");
while($db->next_record()) {
#	$vars["aanbiedingen_types"][$db->f("type_id")]=$db->f("plaats")." - ".$db->f("accommodatie")." (".$db->f("begincode").$db->f("type_id").")";
	$vars["aanbiedingen_types"][$db->f("type_id")]=$db->f("plaats")." - ".$db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : "")." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p - ".$db->f("begincode").$db->f("type_id").")";
	if($vars["inquery_type"]) $vars["inquery_type"].=",".$db->f("type_id"); else $vars["inquery_type"]=$db->f("type_id");
	if($vars["inquery_type_accommodatie"]) $vars["inquery_type_accommodatie"].=",".$db->f("accommodatie_id"); else $vars["inquery_type_accommodatie"]=$db->f("accommodatie_id");
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(16,"select","type_id","",array("selection"=>$vars["aanbiedingen_types"]));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[16]=array("type_id");
$cms->list_field(16,"type_id","Naam");




/*

# 21 juni 2011: leverancier-koppeling uitgezet (maakte database-query's bij opvragen aanbiedingen veel te traag)

#
# Koppeling met aanbieding_leverancier
#
$cms->settings[14]["connect"][]=41;
$cms->settings[41]["parent"]=14;

$cms->settings[41]["list"]["show_icon"]=false;
$cms->settings[41]["list"]["edit_icon"]=false;
$cms->settings[41]["list"]["delete_icon"]=true;
$cms->settings[41]["list"]["add_link"]=false;
$cms->settings[41]["list"]["delete_checkbox"]=true;

# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
$cms->db[41]["where"]="aanbieding_id='".addslashes($_GET["14k0"])."'";

#$db->query("SELECT DISTINCT lev.leverancier_id, lev.naam, l.naam AS land FROM leverancier lev, accommodatie a, type t, plaats p, land l WHERE a.wzt='".addslashes($_GET["wzt"])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND t.leverancier_id=lev.leverancier_id AND lev.beheerder=0 ORDER BY lev.naam, l.naam;");
$db->query("SELECT DISTINCT lev.leverancier_id, lev.naam, l.naam AS land FROM leverancier lev, accommodatie a, type t, plaats p, land l WHERE t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND t.leverancier_id=lev.leverancier_id AND lev.beheerder=0 ORDER BY lev.naam, l.naam;");
while($db->next_record()) {
	if($vars["aanbiedingen_leveranciers"][$db->f("leverancier_id")]) {
		$vars["aanbiedingen_leveranciers"][$db->f("leverancier_id")]=substr($vars["aanbiedingen_leveranciers"][$db->f("leverancier_id")],0,-1).", ".$db->f("land").")";
	} else {
		$vars["aanbiedingen_leveranciers"][$db->f("leverancier_id")]=$db->f("naam")." (actief in ".$db->f("land").")";
	}
}

# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(41,"select","leverancier_id","",array("selection"=>$vars["aanbiedingen_leveranciers"]));

# Listing list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[41]=array("leverancier_id");
$cms->list_field(41,"leverancier_id","Naam");

*/

# End declaration
$cms->end_declaration();

$layout->display_all($cms->page_title);

?>