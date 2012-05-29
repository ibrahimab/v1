<?php

$mustlogin=true;

include("admin/vars.php");

if($_GET["18k1"] and !$_GET["week"]) {
	# Records die missen alsnog INSERTen
	
	
}

# Gewijzigde blokgegevens opslaan
if($_POST["blokgegevens_filled"]) {
#echo wt_dump($_POST);
#exit;
	reset($_POST["blokweken"]);
	while(list($key,$value)=each($_POST["blokweken"])) {
		$db->query("INSERT INTO top10_week SET bloknaam='".addslashes(trim($_POST["bloknaam"][$key]))."', blokvolgorde=".($_POST["blokvolgorde"][$key] ? "'".addslashes($_POST["blokvolgorde"][$key])."'" : "NULL").", site='".addslashes($_POST["site"])."', seizoen_id='".addslashes($_GET["18k1"])."', week='".addslashes($key)."';");
		if($db->Errno==1062) {
			$db->query("UPDATE top10_week SET bloknaam='".addslashes(trim($_POST["bloknaam"][$key]))."', blokvolgorde=".($_POST["blokvolgorde"][$key] ? "'".addslashes($_POST["blokvolgorde"][$key])."'" : "NULL")." WHERE site='".addslashes($_POST["site"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($key)."';");
		}
	}
	$_SESSION["wt_popupmsg"]="blokgegevens zijn opgeslagen";
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}
# Gewijzigde volgorde opslaan
if($_POST["volgordeveranderen"]) {
	while(list($key,$value)=each($_POST["type"])) {
		$db->query("UPDATE top10_week_type SET volgorde='".addslashes($value)."' WHERE site='".addslashes($_POST["site"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($_GET["week"])."' AND type_id='".addslashes($key)."';");
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

# Nieuwe accommodatie opslaan
if($_POST["acctoevoegen"]) {
	$db->query("SELECT max(volgorde) AS volgorde FROM top10_week_type WHERE site='".addslashes($_POST["site"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($_GET["week"])."';");
	if($db->next_record()) {
		$volgorde=$db->f("volgorde")+10;
	} else {
		$volgorde="10";
	}
	if($_POST["type"]) {
		$db->query("INSERT INTO top10_week_type SET volgorde='".$volgorde."', site='".addslashes($_POST["site"])."', seizoen_id='".addslashes($_GET["18k1"])."', week='".addslashes($_GET["week"])."', type_id='".addslashes($_POST["type"])."';");
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

# Accommodatie wissen
if($_GET["deletetype"] and $_GET["site"]) {
	$db->query("DELETE FROM top10_week_type WHERE site='".addslashes($_GET["site"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($_GET["week"])."' AND type_id='".addslashes($_GET["deletetype"])."';");
	header("Location: cms_top10s.php?".wt_stripget($_GET,array("deletetype","site")));
	exit;
}

# Kopieer gegevens naar andere website
if($_GET["kopieer"] and $_GET["from"]) {
	
	# Eerst oude wissen
	$db->query("DELETE FROM top10_week_type WHERE site='".addslashes($_GET["kopieer"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($_GET["week"])."';");

	$db->query("SELECT type_id, volgorde FROM top10_week_type WHERE site='".addslashes($_GET["from"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($_GET["week"])."';");
	while($db->next_record()) {
		$db2->query("INSERT INTO top10_week_type SET volgorde='".$db->f("volgorde")."', site='".addslashes($_GET["kopieer"])."', seizoen_id='".addslashes($_GET["18k1"])."', week='".addslashes($_GET["week"])."', type_id='".addslashes($db->f("type_id"))."';");
	}
	header("Location: cms_top10s.php?".wt_stripget($_GET,array("kopieer","from")));
	exit;
}

# Kopieer gegevens naar andere datum
if($_POST["copydate_fromsite"]) {
#echo date("r",$_POST["copy_date"]);
#exit;
	if($_GET["18k1"] and $_GET["week"]) {
		# Eerst oude wissen
		$db->query("DELETE FROM top10_week_type WHERE site='".addslashes($_POST["copydate_fromsite"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($_POST["copy_date"])."';");
	
		$db->query("SELECT type_id, volgorde FROM top10_week_type WHERE site='".addslashes($_POST["copydate_fromsite"])."' AND seizoen_id='".addslashes($_GET["18k1"])."' AND week='".addslashes($_GET["week"])."';");
		while($db->next_record()) {
			$db2->query("INSERT INTO top10_week_type SET volgorde='".$db->f("volgorde")."', site='".addslashes($_POST["copydate_fromsite"])."', seizoen_id='".addslashes($_GET["18k1"])."', week='".addslashes($_POST["copy_date"])."', type_id='".addslashes($db->f("type_id"))."';");
#			echo $db2->lastquery."<br>";
		}
	}
	header("Location: cms_top10s.php?week=".$_POST["copy_date"]."&".wt_stripget($_GET,array("kopieer","from","week")));
	exit;
}

$cms->settings[18]["list"]["show_icon"]=true;
$cms->settings[18]["list"]["edit_icon"]=false;
$cms->settings[18]["list"]["delete_icon"]=true;
$cms->settings[18]["show"]["goto_new_record"]=true;
$cms->settings[18]["show"]["edit_icon"]=false;

if($_GET["show"]<>18 and $_GET["edit"]<>18) {
	unset($inquery);
	$db->query("SELECT seizoen_id FROM seizoen WHERE UNIX_TIMESTAMP(eind)>'".time()."';");
	while($db->next_record()) {
		$inquery.=",".$db->f("seizoen_id");
	}
	if($inquery) {
		$cms->db[18]["where"]="site='1' AND seizoen_id IN (".substr($inquery,1).")";
	} else {
		$cms->db[18]["where"]="site='1'";
	}
}
$cms->db[18]["set"]="site='1'";

# Database db_field($counter,$type,$id,$field="",$options="")

$cms->db_field(18,"select","site","",array("selection"=>$vars["websitetype_namen"]));
if($_GET["add"]) {
	$cms->db_field(18,"select","seizoen_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"UNIX_TIMESTAMP(eind)>'".(time()+2592000)."'"));
} else {
	$cms->db_field(18,"select","seizoen_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam"));
}

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_field(18,"seizoen_id","Seizoen");

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
$cms->edit_field(18,1,"seizoen_id","Seizoen");

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(18);
if($cms_form[18]->filled) {

}

# Doen na toevoegen record
if($cms_form[18]->okay) {

	# Begin- en eind van seizoen uit db halen
	$db->query("SELECT seizoen_id, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE seizoen_id='".addslashes($cms_form[18]->input["seizoen_id"])."' ORDER BY begin, eind;");
	if($db->next_record()) {

		# Alle sites doorlopen
		reset($vars["websitetype_namen"]);
		while(list($key,$value)=each($vars["websitetype_namen"])) {
			if($db->f("type")==$vars["websites_wzt_siteid"][$key]) {
				$db2->query("INSERT INTO top10 SET site='".$key."', seizoen_id='".$db->f("seizoen_id")."';");
	
				# Alle aankomstdatums vullen
				$timeteller=$db->f("begin");
				while($timeteller<=$db->f("eind")) {
					$db2->query("INSERT INTO top10_week SET site='".addslashes($key)."', seizoen_id='".addslashes($db->f("seizoen_id"))."', week='".$timeteller."';");
					$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
				}
			}
		}
	}
}


# Controle op delete-opdracht
if($_GET["delete"]==18 and $_GET["18k0"] and $_GET["18k1"]) {
	$db->query("SELECT week FROM top10_week_type WHERE seizoen_id='".addslashes($_GET["18k1"])."';");
	if($db->next_record()) {
		$cms->delete_error(18,"Er zijn nog accommodaties gekoppeld");
	}
}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(18)) {
	$db->query("DELETE FROM top10 WHERE seizoen_id='".addslashes($_GET["18k1"])."';");
	$db->query("DELETE FROM top10_week WHERE seizoen_id='".addslashes($_GET["18k1"])."';");
	$db->query("DELETE FROM top10_week_type WHERE seizoen_id='".addslashes($_GET["18k1"])."';");
}


# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[18]="seizoen";
$cms->show_mainfield[18]="seizoen";
#$cms->show_field(14,"naam","Naam");
#$cms->show_field(18,"site","Site");
$cms->show_field(18,"seizoen_id","seizoen");

# End declaration
$cms->end_declaration();

# Gegevens seizoen uit db halen
$db->query("SELECT naam, seizoen_id, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind, type FROM seizoen WHERE seizoen_id='".addslashes($_GET["18k1"])."' ORDER BY begin, eind;");
if($db->next_record()) {
	$vars["tempseizoen"]["naam"]=$db->f("naam");
	$vars["tempseizoen"]["begin"]=$db->f("begin");
	$vars["tempseizoen"]["eind"]=$db->f("eind");
	$vars["tempseizoen"]["type"]=$db->f("type");
}

$layout->display_all("Top 10 - Aanbiedingen per week (t.b.v. uitgaande XML)".($vars["tempseizoen"]["naam"] ? " - ".$vars["tempseizoen"]["naam"] : "").($_GET["week"] ? " - ".DATUM("D MAAND JJJJ",$_GET["week"]) : ""));

?>