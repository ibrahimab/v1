<?php

$mustlogin=true;

if(!$_GET["t"]) {
	if(strpos($_SERVER["HTTP_REFERER"],"cms_diversen.php?t=2")) {
		$_GET["t"]=2;
	} else {
		$_GET["t"]=1;
	}
}

include("admin/vars.php");


if($_GET["t"]==1 or $_GET["t"]==2) {
	#
	# Actielijst-systeem
	#


	#
	# Database-declaratie
	#
	# Database db_field($counter,$type,$id,$field="",$options="")
	$cms->db_field(39,"integer","actie_id");
	$cms->db_field(39,"yesno","alleenwebtastic");
	$cms->db_field(39,"text","naam");
	$cms->db_field(39,"textarea","omschrijving");
	$cms->db_field(39,"select","user_id","",array("othertable"=>"25","otherkeyfield"=>"user_id","otherfield"=>"voornaam","otherwhere"=>"userlevel=1"));
	$cms->db_field(39,"checkbox","betrokkenen","",array("othertable"=>"25","otherkeyfield"=>"user_id","otherfield"=>"voornaam","otherwhere"=>"userlevel=1"));
	$cms->db_field(39,"date","invoermoment");
	$cms->db_field(39,"select","soort","",array("selection"=>$vars["actielijst_soort"]));
	$cms->db_field(39,"integer","prioriteit");
	$cms->db_field(39,"date","einddatum");
	$cms->db_field(39,"integer","geschattetijd_min");
	$cms->db_field(39,"integer","geschattetijd_max");
	$cms->db_field(39,"text","geschattetijd");
	$cms->db_field(39,"select","status","",array("selection"=>$vars["actielijst_status"]));
#	$cms->db_field(39,"textarea","zie_ook");
	$cms->db_field(39,"textarea","opmerkingen");
	$cms->db_field(39,"textarea","interne_notities");

	if(!$login->has_priv(10) and $login->userlevel<10) {
		# Where-statement
		$cms->db[39]["where"]="betrokkenen REGEXP '[[:<:]]".addslashes($login->user_id)."[[:>:]]'";
	}
	if($login->userlevel<10) {
		if($cms->db[39]["where"]) {
			$cms->db[39]["where"].=" AND alleenwebtastic=0";
		} else {
			$cms->db[39]["where"]="alleenwebtastic=0";
		}
	}

	# Set-statement
	$cms->db[39]["set"]="invoermoment=NOW(), ingevoerd_door='".addslashes($login->user_id)."'";

	#
	# List
	#
	# Te tonen icons/links bij list
	$cms->settings[39]["list"]["show_icon"]=false;
	$cms->settings[39]["list"]["edit_icon"]=true;
	$cms->settings[39]["list"]["delete_icon"]=true;
	$cms->settings[39]["list"]["add_link"]=true;

	# List list_field($counter,$id,$title="",$options="",$layout="")
	$cms->list_sort[39]=array("prioriteit","naam");
	$cms->list_field(39,"naam","Naam");
	$cms->list_field(39,"prioriteit","Prioriteit");

	# Controle op delete-opdracht
	if($_GET["delete"]==39 and $_GET["39k0"]) {

	}

	# Bij wissen record: DELETEn van andere tabellen
	if($cms->set_delete_init(39)) {

	}


	#
	# Edit
	#
	# Nieuw record meteen openen na toevoegen
	$cms->settings[39]["show"]["goto_new_record"]=false;

	# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
	$obl_personen=1;
	if($login->userlevel>=10) {
		$cms->edit_field(39,1,"alleenwebtastic","Alleen zichtbaar voor WebTastic");
		$obl_personen=0;
	}
	$cms->edit_field(39,1,"naam","Naam actie");
	if($login->userlevel>=10 or $_GET["add"]==39 or $login->has_priv(26)) {
		$cms->edit_field(39,1,"status","Status");
	} else {
		$cms->edit_field(39,1,"status","Status","",array("noedit"=>true));
	}
	if($login->has_priv(26)) {
		$cms->edit_field(39,0,"prioriteit","Prioriteit");
	} else {
		$cms->edit_field(39,1,"prioriteit","Prioriteit","",array("noedit"=>true));
	}
	$cms->edit_field(39,0,"omschrijving","Omschrijving","","",array("rows"=>25));
	$cms->edit_field(39,$obl_personen,"user_id","Verantwoordelijke bij Chalet.nl",array("selection"=>$login->user_id),"",array("onchange"=>"document.forms['frm'].elements['input[betrokkenen]['+this.value+']'].checked=true;"));
	$cms->edit_field(39,$obl_personen,"betrokkenen","Betrokkenen",array("selection"=>$login->user_id),"",array("one_per_line"=>true));
	$cms->edit_field(39,0,"einddatum","Streefdatum oplevering (optioneel)","","",array("calendar"=>true));
	if($login->userlevel>=10) {
		$cms->edit_field(39,0,"geschattetijd_min","Geschatte ontwikkeltijd in uren (min)");
		$cms->edit_field(39,0,"geschattetijd_max","Geschatte ontwikkeltijd in uren (max)");
		$cms->edit_field(39,0,"geschattetijd","Geschatte ontwikkeltijd opmerking");
	} elseif(!$_GET["add"]==39) {
		$cms->edit_field(39,0,"geschattetijd_min","Geschatte ontwikkeltijd in uren (min)","",array("noedit"=>true));
		$cms->edit_field(39,0,"geschattetijd_max","Geschatte ontwikkeltijd in uren (max)","",array("noedit"=>true));
		$cms->edit_field(39,0,"geschattetijd","Geschatte ontwikkeltijd opmerking","",array("noedit"=>true));
	}
	$cms->edit_field(39,0,"opmerkingen","Opmerkingen","","",array("rows"=>15));
	if($login->userlevel>=10) {
		$cms->edit_field(39,0,"interne_notities","Interne notities (alleen WebTastic)","","",array("rows"=>25));
	}

	if($login->userlevel>=10 and $_GET["add"]==39) {
		$cms->edit_field(39,0,"htmlrow","<input type=\"checkbox\" name=\"actiemailen\" value=\"1\" id=\"actiemailen\"><label for=\"actiemailen\">&nbsp;Stuur een mail aan de verantwoordelijke na toevoegen van deze actie.</label>");
	}

	# Toon ook bovenaan een submit-button
	if($_GET["edit"]==39) {
		$cms->settings[39]["edit"]["top_submit_button"]=true;
	}

	# Controle op ingevoerde formuliergegevens
	$cms->set_edit_form_init(39);
	if($cms_form[39]->filled) {
		if($login->userlevel>=10 and !$cms_form[39]->input["alleenwebtastic"]) {
			if(!$cms_form[39]->input["user_id"]) {
				$cms_form[39]->error("user_id","obl");
			}
			if(!$cms_form[39]->input["betrokkenen"]) {
				$cms_form[39]->error("betrokkenen","obl");
			}
		}
	}

	function form_before_goto($form) {
		global $login,$vars;
		$db=new DB_sql;
		$db2=new DB_sql;

		if($_GET["add"]==39 and $form->db_insert_id and $login->userlevel<10) {
			wt_mail("jeroen@webtastic.nl","Nieuwe actie ingevoerd: ".$form->input["naam"],"Bij de Chalet.nl-actielijst is een nieuwe actie ingevoerd door ".$vars["allewerknemers"][$login->user_id].".\n\nZie: https://www.chalet.nl/cms_diversen.php?edit=39&t=1&39k0=".$form->db_insert_id,$login->vars["email"],wt_naam($login->vars["voornaam"],$login->vars["tussenvoegsel"],$login->vars["achternaam"]));
		}
		if($_GET["edit"]==39 and $login->userlevel<10) {
#			wt_mail("jeroen@webtastic.nl","Actie gewijzigd door Chalet.nl","Bij de Chalet.nl-actielijst is een actie gewijzigd door ".$vars["allewerknemers"][$login->user_id].".\n\nZie: https://www.chalet.nl/cms_diversen.php?edit=39&t=1&39k0=".$_GET["39k0"]);
		}

		if($_GET["add"]==39 and $form->db_insert_id and $login->userlevel>=10 and $_POST["actiemailen"]==1) {
			wt_mail($vars["allewerknemers_mail"][$form->input["user_id"]],"Nieuwe actie ingevoerd door WebTastic","Aan de WebTastic-actielijst is een nieuwe actie toegevoegd door Jeroen.\n\nZie: https://www.chalet.nl/cms_diversen.php?edit=39&t=1&39k0=".$form->db_insert_id,"jeroen@webtastic.nl","WebTastic");
		}

		$prio=0;
		$db->query("SELECT actie_id, prioriteit FROM actie WHERE status IN (1,2,7) AND prioriteit>0 ORDER BY prioriteit, einddatum;");
		while($db->next_record()) {
			$prio=$prio+10;
			$db2->query("UPDATE actie SET prioriteit='".$prio."' WHERE actie_id='".$db->f("actie_id")."';");
		}
		$db->query("UPDATE actie SET prioriteit=NULL WHERE (status NOT IN (1,2,7) OR prioriteit=0);");
	}

	#
	# Show
	#


	# Show show_field($counter,$id,$title="",$options="",$layout=""))

	# End declaration
	$cms->end_declaration();

} elseif($_GET["t"]==3) {
	#
	# Diverse instellingen
	#

	# Seizoenen-array vullen
	$db->query("SELECT seizoen_id, naam, type FROM seizoen ORDER BY begin, eind, naam;");
	while($db->next_record()) {
		$vars["seizoenen"][$db->f("type")][$db->f("seizoen_id")]=$db->f("naam");
	}

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm");
	$form->settings["fullname"]="diverseinstellingen";
	$form->settings["layout"]["css"]=false;
	$form->settings["db"]["table"]="diverse_instellingen";
	$form->settings["db"]["where"]="diverse_instellingen_id=1";
	$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
	#$form->settings["target"]="_blank";

	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	if($_GET["back"]) {
		$form->settings["goto"]=$_GET["back"];
	} else {
		$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen
	}

	#_field: (obl),id,title,db,prevalue,options,layout

	$form->field_htmlrow("","<b>Zoekformulier</b>");
	#$form->field_text(1,"test","test",array("field"=>"test")); # (opslaan in databaseveld "test")
	$form->field_yesno("zoekformulier_weinig_tarieven_1","Toon bij de winter de tekst: \"omdat een groot deel van de accommodaties nog niet geprijsd is kan het verstandig zijn om zonder aankomstdatum te zoeken\"",array("field"=>"zoekformulier_weinig_tarieven_1"));
	$form->field_yesno("zoekformulier_weinig_tarieven_2","Toon bij de zomer de tekst: \"omdat een groot deel van de accommodaties nog niet geprijsd is kan het verstandig zijn om zonder aankomstdatum te zoeken\"",array("field"=>"zoekformulier_weinig_tarieven_2"));
	$form->field_htmlrow("","<hr><b>Mailtjes aan klanten vorig winterseizoen</b>");
	$form->field_select(1,"winter_vorig_seizoen_id","Vorig winterseizoen",array("field"=>"winter_vorig_seizoen_id"),"",array("selection"=>$vars["seizoenen"][1]));
	$form->field_select(1,"winter_huidig_seizoen_id","Nieuw winterseizoen",array("field"=>"winter_huidig_seizoen_id"),"",array("selection"=>$vars["seizoenen"][1]));
	$form->field_htmlrow("","<hr><b>Mailtjes aan klanten vorig zomerseizoen</b>");
	$form->field_select(1,"zomer_vorig_seizoen_id","Vorig zomerseizoen",array("field"=>"zomer_vorig_seizoen_id"),"",array("selection"=>$vars["seizoenen"][2]));
	$form->field_select(1,"zomer_huidig_seizoen_id","Nieuw zomerseizoen",array("field"=>"zomer_huidig_seizoen_id"),"",array("selection"=>$vars["seizoenen"][2]));
	$form->field_htmlrow("","<hr><b>Doorklik naar tarievenmodule bovenaan tab &quot;prijsinformatie&quot; op accommodatiepagina's</b>");
	$form->field_select(0,"winter_toon_tarievenlink_seizoen_id","Winter",array("field"=>"winter_toon_tarievenlink_seizoen_id"),"",array("selection"=>$vars["seizoenen"][1]));
	$form->field_select(0,"zomer_toon_tarievenlink_seizoen_id","Zomer",array("field"=>"zomer_toon_tarievenlink_seizoen_id"),"",array("selection"=>$vars["seizoenen"][2]));

	$form->field_htmlrow("","<hr><b>XML-import handmatig starten</b><br><br><i>Na opslaan wordt de import binnen 1 minuut gestart (tenzij er op dit moment al een XML-import draait; dan start de import zodra die andere import is afgerond).</i>");
	$form->field_select(0,"handmatige_xmlimport_id","Leverancier",array("field"=>"handmatige_xmlimport_id"),"",array("selection"=>$vars["xml_type"]));

	$form->field_htmlrow("","<hr><b>Autocomplete-woorden zoekfunctie (1 woord per regel)</b>");
	$form->field_textarea(0,"woorden_autocomplete_winter","Woordenlijst winter (NL)",array("field"=>"woorden_autocomplete_winter"));
	$form->field_textarea(0,"woorden_autocomplete_winter_en","Woordenlijst winter (EN)",array("field"=>"woorden_autocomplete_winter_en"));
	$form->field_textarea(0,"woorden_autocomplete_zomer","Woordenlijst zomer (NL)",array("field"=>"woorden_autocomplete_zomer"));

	$form->field_htmlrow("","<hr><b>Accommodaties met een hogere vergoeding voor TradeTracker-affiliates (1 accommodatie-code per regel)</b><br/><i>Deze functionaliteit is nog niet actief maar invullen van de codes kan al wel.</i>");
	$form->field_textarea(0,"tradetracker_higher_payout","Accommodatie-codes",array("field"=>"tradetracker_higher_payout"),"","",array("style"=>"height:400px"));

	if( $vars["acceptatie_testserver"] or $vars["lokale_testserver"]) {
		$form->field_htmlrow("","<hr><div id=\"git\"></div><b>Git-branch op test.chalet.nl</b>");

		$gitfile = dirname(__FILE__)."/.git/HEAD";
		if( file_exists($gitfile) ) {
			$git_current_branch = trim(basename(file_get_contents($gitfile)));
		}

		// git-branches
		$git_branches = file($vars["unixdir"]."tmp/git-branch-list.txt");
		foreach ($git_branches as $key => $value) {

			if (preg_match("@^[a-z]{3}[0-9]+$@", $value)) {
				$vars["git_branches"][$value] = strtoupper($value);
			} else {
				$vars["git_branches"][$value] = $value;
			}
		}

		ksort($vars["git_branches"]);

		$form->field_htmlcol("","Huidige branch", array("html"=>wt_he(($vars["git_branches"][$git_current_branch] ? $vars["git_branches"][$git_current_branch] : $git_current_branch))));

		$checkfile = "/var/www/chalet.nl/html_test/tmp/git-autopull-acceptance-test.txt";

		if( file_exists($checkfile) ) {
			$git_selected_branch = trim(file_get_contents($checkfile));
			$form->field_htmlcol("","Wordt nu gepulld", array("html"=>wt_he(($vars["git_branches"][$git_selected_branch] ? $vars["git_branches"][$git_selected_branch] : $git_selected_branch))));
		} else {
			$form->field_select(0,"git_change_to_branch","Switch naar branch","","",array("selection"=>$vars["git_branches"]));
		}
	}



	# aantal beschikbare kortingscodes bepalen
	$db->query("SELECT COUNT(enquete_kortingscode_id) AS aantal FROM enquete_kortingscode WHERE verzonden IS NULL;");
	if($db->next_record()) {
		$enquete_kortingscode_aantal=$db->f("aantal");
	}

	if($vars["fotofabriek_code_na_enquete"]) {
		$form->field_htmlrow("","<hr><b>Kortingscodes fotofabriek.nl toevoegen</b><br/><br/><i>Een klant ontvangt een code na het invullen van de enquête.</i><br/><br/><b>Aantal nog beschikbare codes: ".intval($enquete_kortingscode_aantal)."</b>");
		$form->field_textarea(0,"enquete_kortingscode","Nieuwe codes (1 per regel)");
	}

	#$form->field_htmlrow("","<hr><b>Nieuwe vormgeving</b>");
	#$form->field_yesno("nieuwevormgeving","Toon op deze computer \"".$login->username."\" Chalet.nl/winter in de nieuwe vormgeving","",array("selection"=>$_COOKIE["nieuwevormgeving_fixed"]));

	$form->check_input();

	if($form->filled) {
		if($form->input["winter_vorig_seizoen_id"] and $form->input["winter_vorig_seizoen_id"]==$form->input["winter_huidig_seizoen_id"]) {
			$form->error("winter_huidig_seizoen_id","zelfde als vorig winterseizoen");
		}
		if($form->input["zomer_vorig_seizoen_id"] and $form->input["zomer_vorig_seizoen_id"]==$form->input["zomer_huidig_seizoen_id"]) {
			$form->error("zomer_huidig_seizoen_id","zelfde als vorig zomerseizoen");
		}

		if($form->input["tradetracker_higher_payout"]) {
			$tradetracker_higher_payout = preg_split("@\n@", $form->input["tradetracker_higher_payout"]);
			if(is_array($tradetracker_higher_payout)) {
				foreach ($tradetracker_higher_payout as $key => $value) {
					$value = trim($value);
					if($value and !preg_match("@^[A-Z][0-9]+$@", $value)) {
						$tradetracker_higher_payout_error .= ", ".$value;
					}
				}
			}
			if($tradetracker_higher_payout_error) {
				$form->error("tradetracker_higher_payout", "de volgende codes zijn onjuist: ".substr($tradetracker_higher_payout_error,2));
			}
		}
	}

	if($form->okay) {
		$form->save_db();

		// git change to branch
		if( $form->input["git_change_to_branch"] ) {
			$checkfile = "/var/www/chalet.nl/html_test/tmp/git-autopull-acceptance-test.txt";
			file_put_contents($checkfile, trim($form->input["git_change_to_branch"]));
			chmod($checkfile, 0666);
		}

		# Woordenlijst autocomplete
		$db->query("SELECT woorden_autocomplete_winter, woorden_autocomplete_winter_en, woorden_autocomplete_zomer FROM diverse_instellingen WHERE diverse_instellingen_id=1;");
		if($db->next_record()) {
			$woorden_autocomplete[1]=preg_split("/\n/",$db->f("woorden_autocomplete_winter"));
			$woorden_autocomplete[2]=preg_split("/\n/",$db->f("woorden_autocomplete_winter_en"));
			$woorden_autocomplete[3]=preg_split("/\n/",$db->f("woorden_autocomplete_zomer"));
		}
		$db->query("DELETE FROM woord_autocomplete;");
		while(list($key,$value)=each($woorden_autocomplete)) {
			while(list($key2,$value2)=each($value)) {
				unset($wzt,$taal);
				if($key==1) {
					$wzt=1;
					$taal="nl";
				} elseif($key==2) {
					$wzt=1;
					$taal="en";
				} elseif($key==3) {
					$wzt=2;
					$taal="nl";
				}
				if($wzt and $taal) {
					$db->query("INSERT INTO woord_autocomplete SET woord='".addslashes($value2)."', wzt='".$wzt."', taal='".$taal."', adddatetime=NOW(), editdatetime=NOW();");
				}
			}
		}

		# Nieuwe kortingscodes opslaan in database
		if($form->input["enquete_kortingscode"]) {
			$enquete_kortingscode=explode("\n",$form->input["enquete_kortingscode"]);
			while(list($key,$value)=each($enquete_kortingscode)) {
				$value=trim($value);
				if(strlen($value)>0) {
					$db->query("INSERT INTO enquete_kortingscode SET code='".addslashes($value)."', adddatetime=NOW(), editdatetime=NOW();");
				}
			}
		}


	#	if($form->input["nieuwevormgeving"]) {
	#		setcookie("nieuwevormgeving_fixed",1,mktime(3,0,0,date("m"),date("d"),date("Y")+1),"/");
	#	} else {
	#		setcookie("nieuwevormgeving_fixed",0,mktime(3,0,0,date("m"),date("d"),date("Y")-1),"/");
	#	}
	}
	$form->end_declaration();
}

$layout->display_all("");

?>