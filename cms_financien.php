<?php

set_time_limit(0);

if($_GET["csv"] and $_SERVER["REMOTE_ADDR"]=="87.250.137.107") {
	$vars["mustlogin_cms_cron_false"]=true;
#	mail("jeroen@webtastic.nl","cms_financien.php",$_SERVER["REMOTE_ADDR"]);
}

$mustlogin=true;


include("admin/vars.php");

if($_GET["marges"]) {

	if($_GET["wiszoek"]) {
		$db->query("UPDATE user SET fintotaaloverzicht_kolommen='' WHERE user_id='".addslashes($login->user_id)."';");
		header("Location: ".$vars["path"]."cms_financien.php?marges=1");
		exit;
	}

	$vars["totaaloverzicht_kolommen"]=array(
		"aantal_ingekocht"=>"Aantal ingekochte accommodaties",
		"capaciteit"=>"Capaciteit van de ingekochte accommodaties",
		"bruto_acc"=>"Prijs bruto-accommodatie",
		"deelnemers"=>"Werkelijk aantal deelnemers",
		"omzet"=>"Omzet inclusief bijgeboekte opties",
		"inkoopwaarde_acc"=>"Netto-inkoopwaarde accommodatie",
		"opties_bijkomende_kosten_verblijf"=>"Opties: netto-inkoopwaarde bijkomende kosten verblijf",
		"opties_skipassen"=>"Opties: netto-inkoopwaarde skipassen",
		"opties_huurmateriaal"=>"Opties: netto-inkoopwaarde huurmateriaal",
		"opties_skilessen"=>"Opties: netto-inkoopwaarde skilessen",
		"opties_catering"=>"Opties: netto-inkoopwaarde catering/maaltijden",
		"opties_vervoer"=>"Opties: netto-inkoopwaarde vervoer",
		"opties_verzekeringen"=>"Opties: netto-inkoopwaarde verzekeringen",
		"opties_kortingen"=>"Opties: netto-inkoopwaarde aanbiedingskortingen + klachtafhandeling",
		"opties_overig"=>"Opties: netto-inkoopwaarde overig",
		"inkooptotaal"=>"Inkoop totaal",
		"marge_euro"=>"Marge in euro's",
		"marge_percentage"=>"Marge procentueel",
		"betaald"=>"Betaald",
		"boeking_leverancier"=>"Details boeking: leverancier",
		"boeking_plaats"=>"Details boeking: plaats",
		"boeking_accommodatie"=>"Details boeking: accommodatie",
		"boeking_nachten"=>"Details boeking: aantal nachten",
		"boeking_aankomstdatum"=>"Details boeking: aankomstdatum",
		"boeking_resnr"=>"Details boeking: reserveringsnummer",
		"boeking_klantnaam"=>"Details boeking: klantnaam",
	);

	if($login->vars["fintotaaloverzicht_kolommen"]) {
		$vars["totaaloverzicht_kolommen_active"].=",".$login->vars["fintotaaloverzicht_kolommen"];
	} else {
		while(list($key,$value)=each($vars["totaaloverzicht_kolommen"])) {
			if(!preg_match("/^boeking_/",$key)) {
				$vars["totaaloverzicht_kolommen_active"].=",".$key;
			}
		}
	}

	# leveranciers laden
	$leveranciers[0]="-- Alle leveranciers --";
	$db->query("SELECT leverancier_id, naam FROM leverancier WHERE beheerder=0 ORDER BY naam;");
	while($db->next_record()) {
		$leveranciers[$db->f("leverancier_id")]=$db->f("naam");
	}

	# seizoenen laden
	$db->query("SELECT seizoen_id, naam, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id>=17 ORDER BY begin, eind;");
	while($db->next_record()) {
		$vars["temp_seizoenen"][$db->f("seizoen_id")]=$db->f("naam");
		if($db->f("eind")>time()-(30*86400)) {
			$vars["temp_seizoenen_active"].=",".$db->f("seizoen_id");
		}
	}

	# landen laden
	$vars["temp_landen"][0]="-- Alle landen --";
	$db->query("SELECT land_id, naam FROM land WHERE 1=1 ORDER BY naam;");
	while($db->next_record()) {
		$vars["temp_landen"][$db->f("land_id")]=$db->f("naam");
	}

	# regio's laden
	$vars["temp_regios"][0]="-- Alle regio's --";
	$db->query("SELECT wzt, skigebied_id, naam FROM skigebied WHERE 1=1 ORDER BY wzt, naam;");
	while($db->next_record()) {
		$vars["temp_regios"][$db->f("skigebied_id")]=$db->f("naam");

		if($db->f("wzt")==1 and !$regio_winter_gehad) {
			$vars["temp_optgroup"][$db->f("skigebied_id")]="Winter";
			$regio_winter_gehad=true;
		}
		if($db->f("wzt")==2 and !$regio_zomer_gehad) {
			$vars["temp_optgroup"][$db->f("skigebied_id")]="Zomer";
			$regio_zomer_gehad=true;
		}


	}

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm");
	$form->settings["fullname"]="Periode";
	$form->settings["layout"]["css"]=true;
	$form->settings["type"]="get";

	$form->settings["message"]["submitbutton"]["nl"]="OK";

	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["layout"]["goto_aname"]=true;

	#_field: (obl),id,title,db,prevalue,options,layout

#	$gisteren=mktime(0,0,0,date("m"),date("d")-1,date("Y"));
#	$begin_boekjaar=mktime(0,0,0,7,1,boekjaar($gisteren));
#	$begin_vorigemaand=mktime(0,0,0,date("m")-1,1,date("Y"));
#	$eind_vorigemaand=mktime(0,0,0,date("m"),0,date("Y"));
#	$form->field_date(1,"van","Van","",array("time"=>$begin_vorigemaand),array("startyear"=>2006,"endyear"=>date("Y")),array("calendar"=>true));
#	$form->field_date(1,"tot","Tot en met","",array("time"=>$eind_vorigemaand),array("startyear"=>2006,"endyear"=>date("Y")),array("calendar"=>true));

	$form->field_htmlrow("","<i>Selectiecriteria</i>");
	$form->field_yesno("losseboekingen","Toon alle boekingen/garanties apart");
	$form->field_yesno("onverkochtegaranties","Inclusief onverkochte garanties","",array("selection"=>true));
	$form->field_yesno("totaalperleverancier","Toon totalen per leverancier");
	$form->field_yesno("alleenverkochtegaranties","Neem alleen verkochte garanties mee in het overzicht");
	$form->field_yesno("testsysteem","Toon de testversie van het overzicht");
	$form->field_checkbox(1,"soortboekingen","Soort boekingen","",array("selection"=>"1,2"),array("selection"=>array(1=>"directe boekingen en ongebruikte garanties",2=>"boekingen via reisbureaus")),array("one_per_line"=>true));

	$form->field_htmlrow("","<hr>");
	$form->field_select(0,"leverancier_id","Leverancier","","",array("selection"=>$leveranciers,"no_empty_first_selection"=>true));
	$form->field_select(0,"land_id","Land","","",array("selection"=>$vars["temp_landen"],"no_empty_first_selection"=>true));
	$form->field_select(0,"skigebied_id","Skigebied/regio","","",array("selection"=>$vars["temp_regios"],"optgroup"=>$vars["temp_optgroup"],"no_empty_first_selection"=>true));
	$form->field_text(0,"plaats","Plaats","","","",array("info"=>"Voer de gewenste plaats(en) in. Er wordt gezocht op delen van de naam, dus zoeken naar 'Les Menuires' toont ook 'Lavassaix (bij Les Menuires)' als resultaat. Meerdere plaatsen kun je scheiden door een komma."));
	$form->field_checkbox(0,"websites","Websites","","",array("selection"=>$vars["websites_actief"]),array("one_per_line"=>true));
	$form->field_checkbox(1,"seizoenen","Te tonen seizoenen","",array("selection"=>substr($vars["temp_seizoenen_active"],1)),array("selection"=>$vars["temp_seizoenen"]),array("one_per_line"=>true));
	$form->field_checkbox(1,"kolommen","Te tonen kolommen","",array("selection"=>substr($vars["totaaloverzicht_kolommen_active"],1)),array("selection"=>$vars["totaaloverzicht_kolommen"]),array("one_per_line"=>true));
	$form->field_htmlcol("","",array("html"=>"<a href=\"".$_SERVER["REQUEST_URI"]."&wiszoek=1\">zoekformulier wissen</a>"));

	$form->check_input();

	$form->end_declaration();
}

$layout->display_all($cms->page_title);


?>