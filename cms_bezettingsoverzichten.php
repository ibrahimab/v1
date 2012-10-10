<?php

$mustlogin=true;
$vars["types_in_vars"]=true;
include("admin/vars.php");

if($_GET["popup"]) {
	include("content/cms_bezettingsoverzichten_gegevens.html");
} else {

	# Toegevoegde types opslaan
	if($_POST["toevoegen_filled"]) {
		# accommodaties
		if($_POST["accommodatie_types"]) {
			$db->query("SELECT DISTINCT type_id FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND archief=0 AND accommodatie_id='".addslashes($_POST["accommodatie_types"])."';");
			while($db->next_record()) {
				$db2->query("INSERT INTO bezettingsoverzicht_type SET type_id='".addslashes($db->f("type_id"))."', bezettingsoverzicht_id='".addslashes($_GET["49k0"])."', adddatetime=NOW(), editdatetime=NOW();");
			}
		}

		# types
		if($_POST["types"]) {
			$db->query("INSERT INTO bezettingsoverzicht_type SET type_id='".addslashes($_POST["types"])."', bezettingsoverzicht_id='".addslashes($_GET["49k0"])."', adddatetime=NOW(), editdatetime=NOW();");
		}

		# leveranciers
		if($_POST["leverancier_types"]) {
			$db->query("SELECT DISTINCT t.type_id FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND t.tonen=1 AND a.archief=0 AND t.leverancier_id='".addslashes($_POST["leverancier_types"])."';");
			while($db->next_record()) {
				$db2->query("INSERT INTO bezettingsoverzicht_type SET type_id='".addslashes($db->f("type_id"))."', bezettingsoverzicht_id='".addslashes($_GET["49k0"])."', adddatetime=NOW(), editdatetime=NOW();");
			}
		}

		# plaatsen
		if($_POST["plaats_types"]) {
			$db->query("SELECT DISTINCT type_id FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND archief=0 AND plaats_id='".addslashes($_POST["plaats_types"])."';");
			while($db->next_record()) {
				$db2->query("INSERT INTO bezettingsoverzicht_type SET type_id='".addslashes($db->f("type_id"))."', bezettingsoverzicht_id='".addslashes($_GET["49k0"])."', adddatetime=NOW(), editdatetime=NOW();");
			}
		}

		# skigebieden
		if($_POST["skigebied_types"]) {
			$db->query("SELECT DISTINCT type_id FROM view_accommodatie WHERE atonen=1 AND ttonen=1 AND archief=0 AND skigebied_id='".addslashes($_POST["skigebied_types"])."';");
			while($db->next_record()) {
				$db2->query("INSERT INTO bezettingsoverzicht_type SET type_id='".addslashes($db->f("type_id"))."', bezettingsoverzicht_id='".addslashes($_GET["49k0"])."', adddatetime=NOW(), editdatetime=NOW();");
			}
		}

		# herladen
		header("Location: ".$_SERVER["REQUEST_URI"]);
		exit;
	}


	#
	# Database-declaratie
	#
	# Database db_field($counter,$type,$id,$field="",$options="")
	$cms->db_field(49,"text","naam");
	$cms->db_field(49,"select","seizoen_id","",array("othertable"=>"9","otherkeyfield"=>"seizoen_id","otherfield"=>"naam","otherwhere"=>"seizoen_id>=19"));
	$cms->db_field(49,"date","begindatum");
	$cms->db_field(49,"date","einddatum");

	#
	# List
	#
	# Te tonen icons/links bij list
	$cms->settings[49]["list"]["show_icon"]=true;
	$cms->settings[49]["list"]["edit_icon"]=true;
	$cms->settings[49]["list"]["delete_icon"]=false;
	$cms->settings[49]["list"]["add_link"]=true;
	$cms->settings[49]["show"]["goto_new_record"]=true;

	# List list_field($counter,$id,$title="",$options="",$layout="")
	$cms->list_sort[49]=array("naam");
	$cms->list_field(49,"naam","Naam");
	$cms->list_field(49,"begindatum","Begindatum",array("date_format"=>"DAG D MAAND JJJJ"));
	$cms->list_field(49,"einddatum","Einddatum",array("date_format"=>"DAG D MAAND JJJJ"));

	# Show show_field($counter,$id,$title="",$options="",$layout=""))
	$cms->show_name[49]="Bezettingsoverzicht";
	$cms->show_mainfield[49]="naam";
	$cms->show_field(49,"naam");
	$cms->show_field(49,"naam");
	$cms->show_field(49,"begindatum","Begindatum",array("date_format"=>"D MAAND JJJJ"));
	$cms->show_field(49,"einddatum","Einddatum",array("date_format"=>"D MAAND JJJJ"));


	# Controle op delete-opdracht
	if($_GET["delete"]==49 and $_GET["49k0"]) {

	}

	# Bij wissen record: DELETEn van andere tabellen
	if($cms->set_delete_init(49)) {
		$db->query("DELETE FROM bezettingsoverzicht_type WHERE bezettingsoverzicht_id='".addslashes($_GET["49k0"])."';");
		$db->query("DELETE FROM bezettingsoverzicht_leverancier WHERE bezettingsoverzicht_id='".addslashes($_GET["49k0"])."';");
	}

	# Seizoenen-var vullen waarmee begin- en einddatum automatisch wordt gevuld
	$db->query("SELECT DISTINCT s.seizoen_id, s.naam AS seizoen, UNIX_TIMESTAMP(s.begin) AS begin, UNIX_TIMESTAMP(s.eind) AS eind FROM garantie g, tarief t, seizoen s WHERE t.seizoen_id=s.seizoen_id AND g.type_id=t.type_id AND g.aankomstdatum=t.week ORDER BY s.begin, s.eind;");
	while($db->next_record()) {
		$vars["seizoenen_garanties"][$db->f("seizoen_id")]=$db->f("seizoen");

		# javascript garanties_seizoen_naar_datum
		$javascript_seizoenids.=",".$db->f("seizoen_id");
		$javascript_begindatums.=",".date("Ymd",$db->f("begin"));
		$javascript_einddatums.=",".date("Ymd",$db->f("eind"));
	}
	$javascript_seizoenids=substr($javascript_seizoenids,1);
	$javascript_begindatums=substr($javascript_begindatums,1);
	$javascript_einddatums=substr($javascript_einddatums,1);

	#
	# Edit
	#


	# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
	#$cms->edit_field(49,1,"naam","Naam");
	$cms->edit_field(49,1,"naam","Naam");
	$cms->edit_field(49,1,"seizoen_id","Seizoen","","",array("onchange"=>"seizoen_naar_datum(this,'".$javascript_seizoenids."','".$javascript_begindatums."','".$javascript_einddatums."','begindatum','einddatum');"));
	$cms->edit_field(49,1,"begindatum","Begindatum");
	$cms->edit_field(49,1,"einddatum","Einddatum");

	# Controle op ingevoerde formuliergegevens
	$cms->set_edit_form_init(49);
	if($cms_form[49]->filled) {

	}

	# functie na opslaan form
	function form_before_goto($form) {

	}



	#
	# Koppeling met bezettingsoverzicht_type
	#
	$cms->settings[49]["connect"][]=50;
	$cms->settings[50]["parent"]=49;

	$cms->settings[50]["list"]["show_icon"]=false;
	$cms->settings[50]["list"]["edit_icon"]=false;
	$cms->settings[50]["list"]["delete_icon"]=true;
	$cms->settings[50]["list"]["add_link"]=false;
	$cms->settings[50]["list"]["delete_checkbox"]=true;

	# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
	$cms->db[50]["where"]="bezettingsoverzicht_id='".addslashes($_GET["49k0"])."'";

	$db->query("SELECT a.naam AS accommodatie, a.soortaccommodatie, a.toonper, a.accommodatie_id, a.wzt, t.type_id, t.naam AS type, t.code, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, l.naam AS land, l.begincode FROM type t, accommodatie a, plaats p, land l, bezettingsoverzicht_type bo WHERE bo.type_id=t.type_id AND bo.bezettingsoverzicht_id='".addslashes($_GET["49k0"])."' AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id ORDER BY p.naam, a.naam, t.type_id, t.naam, t.maxaantalpersonen;");
	while($db->next_record()) {
		$vars["bezettingsoverzicht_types"][$db->f("type_id")]=($db->f("wzt")==1 ? "winter" : "zomer")." - ".$db->f("plaats")." - ".$db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : "")." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p - ".$db->f("begincode").$db->f("type_id").")";
		if($vars["inquery_type"]) $vars["inquery_type"].=",".$db->f("type_id"); else $vars["inquery_type"]=$db->f("type_id");
	}

	# Database db_field($counter,$type,$id,$field="",$options="")
	$cms->db_field(50,"select","type_id","",array("selection"=>$vars["bezettingsoverzicht_types"]));

	# Listing list_field($counter,$id,$title="",$options="",$layout="")
	$cms->list_sort[50]=array("type_id");
	$cms->list_field(50,"type_id","Naam");




	#
	# Koppeling met bezettingsoverzicht_leverancier
	#
	$cms->settings[49]["connect"][]=51;
	$cms->settings[51]["parent"]=49;

	$cms->settings[51]["list"]["hide"]=true;

	$cms->settings[51]["list"]["show_icon"]=false;
	$cms->settings[51]["list"]["edit_icon"]=false;
	$cms->settings[51]["list"]["delete_icon"]=true;
	$cms->settings[51]["list"]["add_link"]=true;
	$cms->settings[51]["list"]["delete_checkbox"]=true;

	# gebruikte naam na "prevalue" is niet de naam van het database-veld, maar van de id in het systeem!!
	$cms->db[51]["where"]="bezettingsoverzicht_id='".addslashes($_GET["49k0"])."'";
	$cms->db[51]["set"]="bezettingsoverzicht_id='".addslashes($_GET["49k0"])."'";

	# Database db_field($counter,$type,$id,$field="",$options="")
	$cms->db_field(51,"select","leverancier_id","",array("othertable"=>"3","otherkeyfield"=>"leverancier_id","otherfield"=>"naam","otherwhere"=>"beheerder=0"));

	# Listing list_field($counter,$id,$title="",$options="",$layout="")
	$cms->list_sort[51]=array("leverancier_id");
	$cms->list_field(51,"leverancier_id","Naam");


	# End declaration
	$cms->end_declaration();

	# Vormgeving weergeven
	$layout->display_all($cms->page_title);
}

?>