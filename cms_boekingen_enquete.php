<?php

$mustlogin=true;
include("admin/vars.php");


if($_GET["bid"]) {
	$gegevens=get_boekinginfo($_GET["bid"]);
}

if($_GET["controleren"]) {
	if($_GET["bid"]) {
		$where_condition = "boeking_id='".intval($_GET["bid"])."'";
	} elseif($_GET["hash"]){
		$where_condition = "hash='".addslashes($_GET["hash"])."' AND source_leverancier_id='".addslashes($_GET["lev"])."'";
	}

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm");
	$form->settings["fullname"]="beoordelingen";
	$form->settings["layout"]["css"]=false;
	$form->settings["db"]["table"]="boeking_enquete";
	$form->settings["db"]["where"]=$where_condition;
	$form->settings["layout"]["stars"]=false;

	$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
	#$form->settings["target"]="_blank";

	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen

	#_field: (obl),id,title,db,prevalue,options,layout

#	$form->field_htmlrow("","<b>Bekijk onderstaande enqu&ecirc;te en vul de status in.</b>");
	$form->field_select(1,"beoordeeld","Status van onderstaande enquête",array("field"=>"beoordeeld"),"",array("selection"=>$vars["enquetestatus"],"allow_0"=>true));
	$db->query("SELECT vraag1_7, websitetekst, tekst_language, websitetekst_gewijzigd, websitetekst_gewijzigd_de, websitetekst_gewijzigd_nl, websitetekst_gewijzigd_en  FROM boeking_enquete WHERE ".$where_condition.";");

	require_once('cms/language_data.php');

	if($db->next_record() and $db->f("websitetekst")<>"") {
		$form->field_select(0,
			"tekst_language",
			html("comment_language", "beoordelingen"),
			array("field"=>"tekst_language"),
			"",
			array("selection" => $language_options),
			array("input_class"=>"wtform_input review_language_selector")
		);
		$form->field_textarea(0,
			"websitetekst_gewijzigd",
			"Totaaloordeel",
			array("field"=>"websitetekst_gewijzigd"),
			"",
			array("newline"=>true),
			array("add_html_after_field" => $button_all, "input_class"=>"wtform_input wtform_textarea review_original_comment")
		);
		$form->field_textarea(0,
			"websitetekst_gewijzigd_nl",
			"Totaaloordeel nl",
			array("field"=>"websitetekst_gewijzigd_nl"),
			"",
			array("newline"=>true),
			array("add_html_after_title" => $nl_flag, "add_html_after_field" => $button_nl)
		);
		$form->field_textarea(0,
			"websitetekst_gewijzigd_en",
			"Totaaloordeel en",
			array("field"=>"websitetekst_gewijzigd_en"),
			"",
			array("newline"=>true),
			array("add_html_after_title" => $en_flag, "add_html_after_field" => $button_en)
		);
		$form->field_textarea(0,
			"websitetekst_gewijzigd_de",
			"Totaaloordeel de",
			array("field"=>"websitetekst_gewijzigd_de"),
			"",
			array("newline"=>true),
			array("add_html_after_title" => $de_flag, "add_html_after_field" => $button_de)
		);
	} else {
		$form->field_htmlcol("","Tekst totaaloordeel",array("html"=>"<i>niet ingevuld</i>"));
	}
	$form->field_text(0,"websitetekst_naam","Op website te tonen naam (leeg=anoniem)",array("field"=>"websitetekst_naam"),"",array("newline"=>true));
	if(!$db->f("vraag1_7")) {
		$form->field_htmlrow("","<span style=\"color:red;\">Omdat het 'Totaaloordeel accommodatie' ontbreekt zal deze beoordeling niet worden getoond op de accommodatiepagina.</span>");
	}

	$form->check_input();

	if($form->filled) {
		if(preg_match("/@/", $form->input["websitetekst_naam"])) {
			$form->error("websitetekst_naam", "Mailadres niet toegestaan");
		}
	}

	if($form->okay) {

		# Wijzigingen loggen bij boeking
		$db->query("SELECT beoordeeld, websitetekst_gewijzigd FROM boeking_enquete WHERE ".$where_condition.";");
		if($db->next_record()) {
			if($db->f("beoordeeld")<>$form->input["beoordeeld"]) {
				chalet_log("status enquête gewijzigd naar: ".$vars["enquetestatus"][$form->input["beoordeeld"]],false,true);
			}
			if($db->f("websitetekst_gewijzigd")<>$form->input["websitetekst_gewijzigd"]) {
				chalet_log("enquête: tekst totaaloordeel gewijzigd",false,true);
			}
		}

		# Gegevens opslaan in de database
		$form->save_db();

		if($_GET["from"]) {
			header("Location: ".$_GET["from"]);
			exit;
		} else {
			header("Location: ".$vars["path"]."cms_overzichten_overig.php?t=11");
			exit;
		}
	}
	$form->end_declaration();
}

$layout->display_all($cms->page_title);
