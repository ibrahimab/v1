<?php

$robot_noindex=true;
$vars["verberg_lastacc"]=true;
include("admin/vars.php");

# Totale reissom opvragen voor TradeTracker, Cleafs en Google Analytics
if($_GET["aanvraagnr"]) {
	$_GET["aanvraagnr"]=intval($_GET["aanvraagnr"]);
	$gegevens=get_boekinginfo($_GET["aanvraagnr"]);
	if($gegevens["fin"]["totale_reissom"]>0) {
		$totalereissom=round($gegevens["fin"]["totale_reissom"],2);
	}
	$share_url=$gegevens["stap1"]["accinfo"]["url_seo"];
} else {
	$share_url=$vars["basehref"];
}
if($totalereissom>0) {
	$vars["googleanalytics_extra"]="_gaq.push(['_addTrans',
    '".htmlentities($_GET["aanvraagnr"])."',           // order ID - required
    '',  // affiliation or store name
    '".htmlentities($totalereissom)."',          // total - required
    '',           // tax
    '',              // shipping
    '',       // city
    '',     // state or province
    ''             // country
  ]);
  _gaq.push(['_addItem',
    '".htmlentities($_GET["aanvraagnr"])."',           // order ID - required
    '1',           // SKU/code - required
    '".htmlentities($gegevens["stap1"]["accinfo"]["begincode"].$gegevens["stap1"]["accinfo"]["typeid"])."',        // product name
    '',   // category or variation
    '".htmlentities($totalereissom)."',          // unit price - required
    '1'               // quantity - required
  ]);
  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers";

}


// Mobile website only
if($isMobile) {

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm");
	$form->settings["fullname"]="Boeking";
	$form->settings["layout"]["css"]=false;
	$form->settings["layout"]["goto_aname"]="kop";
	$form->settings["language"]=$vars["taal"];

	if(!$gegevens["stap1"]["reisbureau_user_id"]) {
		$form->field_checkbox(0,"referentiekeuze",txt("referentiekeuze","boeken",array("v_websitenaam"=>$vars["websitenaam"])),"","",array("selection"=>$vars["referentiekeuze_mobile"]),array("one_per_line"=>true));
	}

	if($vars["nieuwsbrief_aanbieden"]) {
		if($vars["nieuwsbrief_tijdelijk_kunnen_afmelden"]) {
			# Nieuwsbrief: kiezen tussen direct/einde van het seizoen/nee
			$form->field_radio(0,"nieuwsbrief","<div style=\"height:7px;\"></div>Wil je de ".$vars["websitenaam"]."-nieuwsbrief ontvangen?","",array("selection"=>3),array("selection"=>array(1=>"Ja, per direct",2=>"Ja, tegen het einde van dit winterseizoen, met nieuws over het volgende winterseizoen",3=>"Nee, ik wil geen nieuwsbrief ontvangen")),array("one_per_line"=>true,"newline"=>true,"tr_class"=>"nieuwsbrief_per_wanneer","title_html"=>true));
		} else {
			# Nieuwsbrief: kiezen tussen ja/nee
			$nieuwsbrief_vraag=txt("nieuwsbriefvraag","contact",array("v_websitenaam"=>$vars["websitenaam"]));
			$form->field_yesno("nieuwsbrief",$nieuwsbrief_vraag,"",array("selection"=>false));
		}
	}

	$form->check_input();

	if($form->okay) {
		if($form->input["referentiekeuze"]) {
			$db->query("UPDATE boeking SET referentiekeuze='".addslashes($form->input["referentiekeuze"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		}

		# Inschrijven nieuwsbrief
		if($form->input["nieuwsbrief"] and $form->input["nieuwsbrief"]<>"3") {
			$nieuwsbrief_waardes=array("email"=>$gegevens["stap2"]["email"],"voornaam"=>$gegevens["stap2"]["voornaam"],"tussenvoegsel"=>$gegevens["stap2"]["tussenvoegsel"],"achternaam"=>$gegevens["stap2"]["achternaam"],"per_wanneer"=>$form->input["nieuwsbrief"]);
			var_dump($nieuwsbrief_waardes);
			nieuwsbrief_inschrijven($vars["seizoentype"],$nieuwsbrief_waardes);
		}

		setcookie("naw[nieuwsbrief]",($form->input["nieuwsbrief"] ? "ja" : "nee"),time()+12960000);
	}



}

include "content/opmaak.php";

?>