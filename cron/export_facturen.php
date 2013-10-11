<?php

$cronmap=true;

set_time_limit(0);

if(!$_GET["bedrijf"]) {
	$_GET["bedrijf"]="chalet";
}

$unixdir="../";
include($unixdir."admin/vars.php");

$exporteermoment=time();

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Periode";
$form->settings["layout"]["css"]=true;
$form->settings["type"]="get";

$form->settings["message"]["submitbutton"]["nl"]="CSV-BESTAND DOWNLOADEN";

#_field: (obl),id,title,db,prevalue,options,layout

$gisteren=mktime(0,0,0,date("m"),date("d")-1,date("Y"));
$begin_boekjaar=mktime(0,0,0,7,1,boekjaar($gisteren));

$begin_vorigemaand=mktime(0,0,0,date("m")-1,1,date("Y"));
$eind_vorigemaand=mktime(0,0,0,date("m"),0,date("Y"));

$form->field_date(1,"van","Van","",array("time"=>$begin_vorigemaand),array("startyear"=>2006,"endyear"=>date("Y")),array("calendar"=>true));
$form->field_date(1,"tot","Tot en met","",array("time"=>$eind_vorigemaand),array("startyear"=>2006,"endyear"=>date("Y")),array("calendar"=>true));

$form->check_input();

if($form->input["van"]["unixtime"]>$form->input["tot"]["unixtime"]) $form->error("tot","moet later zijn dan de eerste");
if($form->input["tot"]["unixtime"]>mktime(0,0,0,date("m"),date("d")-1,date("Y"))) $form->error("tot","exporteren kan maximaal tot gisteren");
if(boekjaar($form->input["van"]["unixtime"])<>boekjaar($form->input["van"]["unixtime"])) $form->error("tot","beide datums moeten in hetzelfde boekjaar zijn");

if($form->okay or ($_GET["nodate"] and $_GET["confirmed"])) {


	// boekingen bij juiste B.V. zoeken
	unset($andquery);
	if($_GET["bedrijf"]=="venturasol") {
		$andquery.=" AND b.valt_onder_bedrijf=2";
	} else {
		$andquery.=" AND b.valt_onder_bedrijf=1";
	}

	if($_GET["nodate"]) {
		$tempwhere="f.geexporteerd=0";
	} else {
		$tempwhere="UNIX_TIMESTAMP(f.datum)>='".addslashes($form->input["van"]["unixtime"])."' AND UNIX_TIMESTAMP(f.datum)<='".addslashes($form->input["tot"]["unixtime"])."'";
	}
	$db->query("SELECT b.boeking_id, b.debiteurnummer, b.boekingsnummer, b.landcode, b.aankomstdatum_exact, b.reisbureau_user_id, p.voornaam, p.tussenvoegsel, p.achternaam, f.factuur_id, UNIX_TIMESTAMP(f.datum) AS factuurdatum, fr.regelnummer, fr.bedrag, fr.grootboektype FROM boeking b, boeking_persoon p, factuur f, factuurregel fr WHERE f.boeking_id=b.boeking_id AND fr.factuur_id=f.factuur_id AND p.persoonnummer=1 AND p.boeking_id=b.boeking_id AND debiteurnummer>0 AND ".$tempwhere.$andquery." ORDER BY f.factuur_id, fr.regelnummer;");
#	echo $db->lastquery;
	while($db->next_record()) {

		if(date("m",$db->f("factuurdatum"))>=7) {
			$periode=date("m",$db->f("factuurdatum"))-6;
		} else {
			$periode=date("m",$db->f("factuurdatum"))+6;
		}
		$boekjaar=boekjaar($db->f("factuurdatum"));
		if($db->f("debiteurnummer")<1000) {
			$debiteurnummer="130".substr("0000".$db->f("debiteurnummer"),-4);
		} else {
			$debiteurnummer="130".$db->f("debiteurnummer");
		}


		if($db->f("regelnummer")==0) {
			$boekstuknummer=$db->f("factuur_id");
			$factuurnummer="";
			$betaalref=$db->f("boekingsnummer");
			$grootboek="";
			$btwcode="";
			$btwbedrag="";
		} else {
			$boekstuknummer="";

#			$factuurnummer=$db->f("factuur_id");
			if(strlen($db->f("boekingsnummer"))==16) {
				$factuurnummer=substr($db->f("boekingsnummer"),1,6);
			} else {
				$factuurnummer=substr($db->f("boekingsnummer"),1,8);
			}

			$betaalref="";

			# Grootboekrekening bepalen
			$website=ereg_replace("^([A-Z]).*$","\\1",$db->f("boekingsnummer"));
			if($boekjaar<boekjaar($db->f("aankomstdatum_exact"))) {
				$boekjaar_plusmin=1;
			} elseif($boekjaar>boekjaar($db->f("aankomstdatum_exact"))) {
				$boekjaar_plusmin=-1;
			} else {
				$boekjaar_plusmin=0;
			}
			if($db->f("grootboektype")==0) {
				$grootboek="";
			} elseif($db->f("grootboektype")==1) {
				# Alleen vanaf boekjaar 2010 wederverkoop-grootboekrekeningen toepassen
				if($db->f("reisbureau_user_id") and $boekjaar>=2010) {
					$wederverkoop="_wederverkoop";
				} else {
					$wederverkoop="";
				}
				$grootboek=$vars["grootboekrekeningnummers".$wederverkoop][$website][$boekjaar_plusmin];
			} elseif($db->f("grootboektype")==2) {
				$grootboek="1513";
			}

			# BTW-code en -bedrag
			$btwcode=$vars["landcodes_boekhouding_btwcode"][$db->f("landcode")];
			$btwbedrag=0;
		}
		$csv[]=$db->f("regelnummer").",V,70,".$periode.",".$boekjaar.",".$boekstuknummer.",".wt_csvconvert(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"))." ".$db->f("boekingsnummer")).",".date("dmy",$db->f("factuurdatum")).",".$grootboek.",".$debiteurnummer.",,".$factuurnummer.",".wt_csvconvert(number_format($db->f("bedrag"),2,',','')).",EUR,,,,,,,".$btwcode.",".$btwbedrag.",,".$betaalref.",,,,,,,,,,,,,,,,";

		# Opslaan dat de factuur is geexporteerd (indien geen periode is opgegeven)
		if($_GET["nodate"] and $_GET["confirmed"]) {
			$db2->query("UPDATE factuur SET geexporteerd=1, exporteermoment=FROM_UNIXTIME('".$exporteermoment."') WHERE factuur_id='".$db->f("factuur_id")."';");
		}
	}
}
$form->end_declaration();

# Content
if($form->okay or ($_GET["nodate"] and $_GET["confirmed"])) {
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2") {
		echo "<pre>";
	} else {
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=verkoopboekingen.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
	}
	while(list($key,$value)=@each($csv)) {
		echo $value."\n";
	}
} else { ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<title>CSV-Export verkoopboekingen</title>
	<link href="<?php echo $path; ?>css/cms_layout.css" rel="stylesheet" type="text/css" />
	</head><body>
	<?php
	echo "<div style=\"width:800px;padding:10px;background-color:".($_GET["bedrijf"]=="venturasol" ? "#fff093" : "#ffffff").";\">";
	echo "<h3>CSV-Export verkoopboekingen</h3>";
	echo "Door het invullen van een begin- en einddatum worden de verkoopboekingen ge&euml;xporeerd naar CSV.<p>";

	$form->display_all();

	echo "</div></body></html>";
}


?>