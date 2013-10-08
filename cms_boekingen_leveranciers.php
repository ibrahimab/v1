<?php

$mustlogin=true;
include("admin/vars.php");

# Bijkomende kosten uitzetten/aanzetten
if(is_array($_POST["wis_leverancierfactuur_extraoptie"])) {
	reset($_POST["wis_leverancierfactuur_extraoptie"]);
	while(list($key,$value)=each($_POST["wis_leverancierfactuur_extraoptie"])) {
		if(preg_match("/^ooid_([0-9]+)$/",$key,$regs)) {
			# gewone opties
			if($value==1) {
				$db->query("UPDATE boeking_optie SET hoort_bij_accommodatieinkoop=0 WHERE optie_onderdeel_id='".addslashes($regs[1])."' AND boeking_id='".addslashes($_GET["bid"])."';");
				if($db->affected_rows()) $bijkomende_kosten_op_factuur_aangepast=true;
			} else {
				$db->query("UPDATE boeking_optie SET hoort_bij_accommodatieinkoop=1 WHERE optie_onderdeel_id='".addslashes($regs[1])."' AND boeking_id='".addslashes($_GET["bid"])."';");
				if($db->affected_rows()) $bijkomende_kosten_op_factuur_aangepast=true;
			}
		} else {
			# handmatige opties
			if($value==1) {
				$db->query("UPDATE extra_optie SET hoort_bij_accommodatieinkoop=0 WHERE extra_optie_id='".addslashes($key)."' AND boeking_id='".addslashes($_GET["bid"])."';");
				if($db->affected_rows()) $bijkomende_kosten_op_factuur_aangepast=true;
			} else {
				$db->query("UPDATE extra_optie SET hoort_bij_accommodatieinkoop=1 WHERE extra_optie_id='".addslashes($key)."' AND boeking_id='".addslashes($_GET["bid"])."';");
				if($db->affected_rows()) $bijkomende_kosten_op_factuur_aangepast=true;
			}
		}
	}
}
$temp_gegevens=boekinginfo($_GET["bid"]);
$gegevens["stap1"]=$temp_gegevens["stap1"];

if($bijkomende_kosten_op_factuur_aangepast) {
	chalet_log("bijkomende kosten van de leveranciersfactuur gewijzigd",true,true);
}

if($gegevens["stap1"]["boekingid"]) {
	$accinfo=accinfo($gegevens["stap1"]["typeid"],$gegevens["stap1"]["aankomstdatum"],$gegevens["stap1"]["aantalpersonen"]);

	# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
	if($temp_gegevens["stap2"][2]) {
		$gegevens["stap2"]=$temp_gegevens["stap2"][2];
	} elseif($temp_gegevens["stap2"][1]) {
		$gegevens["stap2"]=$temp_gegevens["stap2"][1];
	}

	# Controle op status Persoonlijke gegevens (2 heeft voorkeur boven 1)
	@reset($temp_gegevens["stap3"][2]);
	while(list($key,$value)=@each($temp_gegevens["stap3"][2])) {
		if(is_array($value)) {
			$gegevens["stap3"][$key]=$value;
		} elseif(is_array($temp_gegevens["stap3"][1][$key])) {
			$gegevens["stap3"][$key]=$temp_gegevens["stap3"][1][$key];
		}
	}

	@reset($temp_gegevens["stap3"][1]);
	while(list($key,$value)=@each($temp_gegevens["stap3"][1])) {
		if(is_array($value) and !is_array($gegevens["stap3"][$key])) $gegevens["stap3"][$key]=$value;
	}

	# Controle op status Geselecteerde opties (2 heeft voorkeur boven 1)
#	if($temp_gegevens["stap4"][2]) {
#		$gegevens["stap4"]=$temp_gegevens["stap4"][2];
#	} else {
		$gegevens["stap4"]=$temp_gegevens["stap4"][1];
		$gegevens["stap4"]["actieve_status"]=1;
		$gegevens["fin"]=$temp_gegevens["fin"][1];
#	}

	$db->query("SELECT leverancier_id, naam, bevestigmethode, opmerkingen_facturen, aanbetaling_dagen, eindbetaling_dagen_factuur FROM leverancier WHERE leverancier_id='".addslashes($gegevens["stap1"]["leverancierid"])."';");
	if($db->next_record()) {
		$temp["leverancier_id"]=$db->f("leverancier_id");
		$temp["leverancier_naam"]=$db->f("naam");
		$temp["leverancier_bevestigmethode"]=$db->f("bevestigmethode");
		$temp["leverancier_opmerkingen_facturen"]=$db->f("opmerkingen_facturen");
		$temp["leverancier_aanbetaling_dagen"]=$db->f("aanbetaling_dagen");
		$temp["leverancier_eindbetaling_dagen_factuur"]=$db->f("eindbetaling_dagen_factuur");
	}

	$db->query("SELECT garantie_id FROM garantie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
	if($db->next_record()) {
		$temp["garantie"]=" <span style=\"color:green;\">(via garantie)</span>";
	}
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Naam";
$form->settings["layout"]["css"]=false;
$form->settings["db"]["table"]="boeking";
$form->settings["db"]["where"]="boeking_id='".addslashes($_GET["bid"])."'";
$form->settings["goto"]=$_GET["burl"];
$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
$form->settings["submitbutton"]["class"]="wtform_submitbutton inkoopgegevens_submit";
$form->settings["submitbutton"]["no_action"]="true";


#_field: (obl),id,title,db,prevalue,options,layout

$form->field_htmlrow("","<b>Bestelgegevens leverancier <i>".htmlentities($temp["leverancier_naam"]).($temp["garantie"] ? $temp["garantie"] : "")."</i></b>");
$form->field_select(1,"bestelstatus","Bestelstatus",array("field"=>"bestelstatus"),"",array("selection"=>$vars["bestelstatus"]),array("input_class"=>"wtform_input bestelstatus_besteldatum","td_cell_right_class"=>"wtform_cell_right bestelstatus_td"));
$form->field_yesno("bestelstatus_schriftelijk_later","Schriftelijke bevestiging volgt binnen enkele dagen",array("field"=>"bestelstatus_schriftelijk_later"),"","",array("info"=>"Aankruisen wanneer telefonisch of per mail akkoord is gegaan, maar leverancier doorgaans direct een schriftelijke bevestiging stuurt."));
$form->field_date(0,"besteldatum","Besteldatum",array("field"=>"besteldatum"),"",array("startyear"=>date("Y")-1,"endyear"=>date("Y")),array("calendar"=>true,"td_cell_right_class"=>"wtform_cell_right besteldatum_td"));

if($temp["leverancier_aanbetaling_dagen"]<>"" or $temp["leverancier_eindbetaling_dagen_factuur"]<>"") {
	$form->field_date(0,"inkoopfactuurdatum","Factuurdatum",array("field"=>"inkoopfactuurdatum"),"",array("startyear"=>date("Y")-1,"endyear"=>date("Y")),array("calendar"=>true));
}

if($gegevens["stap1"]["leverancierscode_oud"]) {
	$form->field_htmlcol("","Reserveringsnummer<br><span style=\"font-size:0.8em;\">(volgens oude methode)</span>",array("html"=>htmlentities($gegevens["stap1"]["leverancierscode_oud"])),"",array("tr_style"=>"color:grey;","title_html"=>true));
}

if($temp["leverancier_bevestigmethode"]) {
	$form->field_htmlcol("","Bevestigmethode",array("html"=>$temp["leverancier_naam"]." ".htmlentities($vars["bevestigmethode"][$temp["leverancier_bevestigmethode"]])),"",array("tr_style"=>"color:grey;font-style:italic;","title_html"=>true));
} else {
	$form->field_htmlcol("","Bevestigmethode",array("html"=>"<span style=\"background-color:yellow;\">Er is bij <a href=\"".$vars["path"]."cms_leveranciers.php?edit=8&beheerder=0&8k0=".$temp["leverancier_id"]."\" target=\"_blank\">".htmlentities($temp["leverancier_naam"])."</a> nog geen bevestigmethode aangegeven.</span>"),"",array("tr_style"=>"color:grey;","title_html"=>true));
}

if($temp["leverancier_bevestigmethode"]==1) {
	# bevestigmethode: stuurt direct een factuurnummer

	# indien oude gegevens in het systeem staan (bij wijzigen van bevestigmethode gedurende een seizoen):
	if($gegevens["stap1"]["leverancierscode"] and $gegevens["stap1"]["leverancierscode"]<>$gegevens["stap1"]["factuurnummer_leverancier"]) {
		$form->field_text(0,"leverancierscode","Reserveringsnummer (voorheen)",array("field"=>"leverancierscode"),"",array("info"=>"Alleen van toepassing omdat de bevestigmethode is gewijzigd."),array("info"=>"Alleen van toepassing omdat de bevestigmethode van deze leverancier is gewijzigd.","input_class"=>"wtform_input leverancierscode_keydown"));
	}
} elseif($temp["leverancier_bevestigmethode"]==2) {
	# bevestigmethode: bevestigt zonder reserveringsnummer
	$form->field_text(0,"leverancierscode","Bevestiging leverancier (zonder reserv.nr)",array("field"=>"leverancierscode"),"","",array("input_class"=>"wtform_input leverancierscode_keydown"));
} elseif($temp["leverancier_bevestigmethode"]==3) {
	# bevestigmethode: bevestigt met reserveringsnummer
	$form->field_text(0,"leverancierscode","Reserveringsnummer leverancier",array("field"=>"leverancierscode"),"","",array("input_class"=>"wtform_input leverancierscode_keydown","add_html_after_field"=>"<br><span style=\"font-size:0.8em;\">Moet reserveringsnummer zijn, dus g&eacute;&eacute;n OK of datum!</span>"));
} else {
	# Melden dat leverancier_bevestigmethode nog niet gevuld is
	$form->field_text(0,"leverancierscode","Reserveringsnummer leverancier",array("field"=>"leverancierscode"),"","",array("input_class"=>"wtform_input leverancierscode_keydown"));
}
# aparte factuurgegevens
$form->field_text(0,"factuurnummer_leverancier","Factuurnummer leverancier",array("field"=>"factuurnummer_leverancier"),"","",array("input_class"=>"wtform_input leverancierscode_keydown"));
if($temp["leverancier_opmerkingen_facturen"]) {
	$form->field_htmlcol(""," ",array("html"=>"<div style=\"font-size:0.8em;\">".nl2br(htmlentities($temp["leverancier_opmerkingen_facturen"]))."</div>"));
}


if(!$gegevens["stap1"]["besteldatum"] or $gegevens["stap1"]["besteldatum"]>(time()-86400*14)) {
	if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
		# inkoopprijs van gekozen onderliggende verzameltype
		$inkoop_actueel=inkoopprijs_bepalen($gegevens["stap1"]["verzameltype_gekozentype_id"],$gegevens["stap1"]["aankomstdatum"]);
	} else {
		# inkoopprijs
		$inkoop_actueel=inkoopprijs_bepalen($gegevens["stap1"]["typeid"],$gegevens["stap1"]["aankomstdatum"]);
	}
}

$form->field_htmlrow("","<hr><b>Inkoopgegevens</b>");
$form->field_currency(0,"inkoopbruto","Bruto-accommodatie €",array("field"=>"inkoopbruto"),"","",array("input_class"=>"wtform_input inkoopgegevens","add_html_after_field"=>inkoopgegevens_actueel("inkoopbruto",$inkoop_actueel["bruto"],$gegevens["stap1"]["inkoopbruto"])));
$form->field_float(0,"inkoopcommissie","Commissie %",array("field"=>"inkoopcommissie"),"","",array("input_class"=>"wtform_input inkoopgegevens","add_html_after_field"=>inkoopgegevens_actueel("inkoopcommissie",$inkoop_actueel["inkoopcommissie"],$gegevens["stap1"]["inkoopcommissie"])));
$form->field_htmlcol("","Inkoop -/- commissie €",array("html"=>wt_cur($gegevens["stap1"]["inkoopbruto"]*(1-$gegevens["stap1"]["inkoopcommissie"]/100),false)),"",array("tr_class"=>"inkoopgegevens_onopvallend","td_cell_right_class"=>"wtform_cell_right uitkomst_inkoopmincommissie"));
$form->field_currency(0,"inkoopkorting","Korting/Toeslag €",array("field"=>"inkoopkorting"),"",array("negative"=>true),array("input_class"=>"wtform_input inkoopgegevens","add_html_after_field"=>inkoopgegevens_actueel("inkoopkorting",$inkoop_actueel["inkoopkorting"],$gegevens["stap1"]["inkoopkorting"])));
$form->field_float(0,"inkoopkorting_percentage","Inkoopkorting accommodatie %",array("field"=>"inkoopkorting_percentage"),"",array("negative"=>true),array("input_class"=>"wtform_input inkoopgegevens","add_html_after_field"=>inkoopgegevens_actueel("inkoopkorting_percentage",$inkoop_actueel["inkoopkorting_percentage"],$gegevens["stap1"]["inkoopkorting_percentage"])));
$form->field_currency(0,"inkoopkorting_euro","Inkoopkorting accommodatie €",array("field"=>"inkoopkorting_euro"),"",array("negative"=>true),array("input_class"=>"wtform_input inkoopgegevens","add_html_after_field"=>inkoopgegevens_actueel("inkoopkorting_euro",$inkoop_actueel["inkoopkorting_euro"],$gegevens["stap1"]["inkoopkorting_euro"])));
$form->field_htmlrow("","&nbsp;<input type=\"hidden\" name=\"input[inkoopnetto]\" value=\"".htmlentities(wt_cur($gegevens["stap1"]["inkoopnetto"],false))."\"><input type=\"hidden\" name=\"input[totaalfactuurbedrag]\" value=\"\">","",array("tr_style"=>"display:none;"));

$temp_totaalbedrag=$gegevens["stap1"]["inkoopnetto"];
$temp_extraopties_totaal=0;

# verborgen opties laden
$db->query("SELECT extra_optie_id, persoonnummer, deelnemers, naam, inkoop, korting, hoort_bij_accommodatieinkoop FROM extra_optie WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."' AND verberg_voor_klant=1 ORDER BY soort, naam;");
while($db->next_record()) {
	if($db->f("hoort_bij_accommodatieinkoop")==1) {
		$use_key="hoort_bij_accommodatieinkoop";
	} else {
		$use_key="hoort_niet_bij_accommodatieinkoop";
	}
	if($db->f("persoonnummer")=="alg") {
		$gegevens["stap4"]["algemene_optie"][$use_key][$db->f("extra_optie_id")]=true;
		$gegevens["stap4"]["algemene_optie"]["inkoop"][$db->f("extra_optie_id")]=$db->f("inkoop");
		$gegevens["stap4"]["algemene_optie"]["korting"][$db->f("extra_optie_id")]=$db->f("korting");
		$gegevens["stap4"]["algemene_optie"]["naam"][$db->f("extra_optie_id")]=$db->f("naam");
	} elseif($db->f("persoonnummer")=="pers") {
		$gegevens["stap4"]["persoonsoptie_fin"][$use_key][$db->f("extra_optie_id")]=true;
		$aantal_deelnemers=intval(@count(@preg_split("/,/",$db->f("deelnemers"))));
		$gegevens["stap4"]["persoonsoptie_fin"]["aantal"][$db->f("extra_optie_id")]=$aantal_deelnemers;
		$gegevens["stap4"]["persoonsoptie_fin"]["inkoop"][$db->f("extra_optie_id")]=$db->f("inkoop")*$aantal_deelnemers;
		$gegevens["stap4"]["persoonsoptie_fin"]["korting"][$db->f("extra_optie_id")]=$db->f("korting");
		$gegevens["stap4"]["persoonsoptie_fin"]["naam"][$db->f("extra_optie_id")]=$db->f("naam");
	}
}

# gewone (niet-handmatige) opties laden waarbij hoort_bij_accommodatieinkoop aan staat
while(list($key,$value)=@each($gegevens["stap4"]["optie_hoort_bij_accommodatieinkoop"])) {
	$db->query("SELECT bo.hoort_bij_accommodatieinkoop, ot.inkoop, ot.korting FROM boeking_optie bo, optie_tarief ot WHERE ot.week='".$gegevens["stap1"]["aankomstdatum"]."' AND bo.boeking_id='".$gegevens["stap1"]["boekingid"]."' AND ot.optie_onderdeel_id=bo.optie_onderdeel_id AND ot.optie_onderdeel_id='".addslashes($key)."';");
	if($db->next_record()) {
		if($db->f("hoort_bij_accommodatieinkoop")==1) {
			$use_key1="hoort_bij_accommodatieinkoop";
		} else {
			$use_key1="hoort_niet_bij_accommodatieinkoop";
		}
		$use_key2="ooid_".$key;
		$gegevens["stap4"]["persoonsoptie_fin"][$use_key1][$use_key2]=true;
		$aantal_deelnemers=$gegevens["stap4"]["optie_hoort_bij_accommodatieinkoop_aantal"][$key];
		$gegevens["stap4"]["persoonsoptie_fin"]["aantal"][$use_key2]=$aantal_deelnemers;
		$gegevens["stap4"]["persoonsoptie_fin"]["inkoop"][$use_key2]=$db->f("inkoop")*$aantal_deelnemers;
		$gegevens["stap4"]["persoonsoptie_fin"]["korting"][$use_key2]=$db->f("korting");
		$gegevens["stap4"]["persoonsoptie_fin"]["naam"][$use_key2]=$gegevens["stap4"]["optie_onderdeelid_naam"][$key];
	}
}


if($gegevens["stap4"]["algemene_optie"]["hoort_bij_accommodatieinkoop"] or $gegevens["stap4"]["persoonsoptie_fin"]["hoort_bij_accommodatieinkoop"] or $gegevens["stap4"]["algemene_optie"]["hoort_niet_bij_accommodatieinkoop"] or $gegevens["stap4"]["persoonsoptie_fin"]["hoort_niet_bij_accommodatieinkoop"]) {
	$form->field_htmlcol("","Netto-accommodatie €",array("html"=>wt_cur($gegevens["stap1"]["inkoopnetto"],false)),"",array("tr_class"=>"inkoopgegevens_opvallend","td_cell_right_class"=>"wtform_cell_right uitkomst_inkoopnetto"));

	# extra opties: algemeen: actief
	while(list($key,$value)=@each($gegevens["stap4"]["algemene_optie"]["hoort_bij_accommodatieinkoop"])) {
		if($titel_bijkomend_getoond) {
			$titel_bijkomend="";
		} else {
			$titel_bijkomend="Bijkomende kosten op factuur";
			$titel_bijkomend_getoond=true;
		}
		$bedrag=$gegevens["stap4"]["algemene_optie"]["inkoop"][$key]*(1-$gegevens["stap4"]["algemene_optie"]["korting"][$key]/100);
		$temp_totaalbedrag+=$bedrag;
		$temp_extraopties_totaal+=$bedrag;
		$form->field_htmlcol("",$titel_bijkomend,array("html"=>"<div style=\"width:100px;float:left;\">".wt_cur($bedrag,false)."</div><div style=\"float:left;\">1 x ".htmlentities($gegevens["stap4"]["algemene_optie"]["naam"][$key])."</div><div style=\"float:right;\"><input type=\"hidden\" name=\"wis_leverancierfactuur_extraoptie[".$key."]\" value=\"0\"><a href=\"#\" onclick=\"inkoopgegevens_bijkomend_factuur_wissen(this,".floatval($bedrag).");return false;\" class=\"noactive\"><img src=\"".$vars["path"]."pic/class.cms_delete.gif\" border=\"0\" width=\"14\" title=\"Deze kosten aan- en uitzetten bij de leveranciersfactuur\"></a></div>"));
	}

	# extra opties: algemeen: inactief
	while(list($key,$value)=@each($gegevens["stap4"]["algemene_optie"]["hoort_niet_bij_accommodatieinkoop"])) {
		if($titel_bijkomend_getoond) {
			$titel_bijkomend="";
		} else {
			$titel_bijkomend="Bijkomende kosten op factuur";
			$titel_bijkomend_getoond=true;
		}
		$bedrag=$gegevens["stap4"]["algemene_optie"]["inkoop"][$key]*(1-$gegevens["stap4"]["algemene_optie"]["korting"][$key]/100);
		$form->field_htmlcol("",$titel_bijkomend,array("html"=>"<div style=\"width:100px;float:left;\">".wt_cur($bedrag,false)."</div><div style=\"float:left;\">1 x ".htmlentities($gegevens["stap4"]["algemene_optie"]["naam"][$key])."</div><div style=\"float:right;\"><input type=\"hidden\" name=\"wis_leverancierfactuur_extraoptie[".$key."]\" value=\"1\"><a href=\"#\" onclick=\"inkoopgegevens_bijkomend_factuur_wissen(this,".floatval($bedrag).");return false;\" class=\"noactive\"><img src=\"".$vars["path"]."pic/class.cms_delete.gif\" border=\"0\" width=\"14\" title=\"Deze kosten aan- en uitzetten bij de leveranciersfactuur\"></a></div>"),"",array("tr_class"=>"bijkomend_doorstrepen"));
	}

	# extra opties: per persoon: actief
	while(list($key,$value)=@each($gegevens["stap4"]["persoonsoptie_fin"]["hoort_bij_accommodatieinkoop"])) {
		if($titel_bijkomend_getoond) {
			$titel_bijkomend="";
		} else {
			$titel_bijkomend="Bijkomende kosten op factuur";
			$titel_bijkomend_getoond=true;
		}
		$bedrag=$gegevens["stap4"]["persoonsoptie_fin"]["inkoop"][$key]*(1-$gegevens["stap4"]["persoonsoptie_fin"]["korting"][$key]/100);
		$temp_totaalbedrag+=$bedrag;
		$temp_extraopties_totaal+=$bedrag;
		$form->field_htmlcol("",$titel_bijkomend,array("html"=>"<div style=\"width:100px;float:left;\">".wt_cur($bedrag,false)."</div><div style=\"float:left;\">".$gegevens["stap4"]["persoonsoptie_fin"]["aantal"][$key]." x ".htmlentities($gegevens["stap4"]["persoonsoptie_fin"]["naam"][$key])."</div><div style=\"float:right;\"><input type=\"hidden\" name=\"wis_leverancierfactuur_extraoptie[".$key."]\" value=\"0\"><a href=\"#\" onclick=\"inkoopgegevens_bijkomend_factuur_wissen(this,".floatval($bedrag).");return false;\" class=\"noactive\"><img src=\"".$vars["path"]."pic/class.cms_delete.gif\" border=\"0\" width=\"14\" title=\"Deze kosten aan- en uitzetten bij de leveranciersfactuur\"></a></div>"));
	}

	# extra opties: per persoon: inactief
	while(list($key,$value)=@each($gegevens["stap4"]["persoonsoptie_fin"]["hoort_niet_bij_accommodatieinkoop"])) {
		if($titel_bijkomend_getoond) {
			$titel_bijkomend="";
		} else {
			$titel_bijkomend="Bijkomende kosten op factuur";
			$titel_bijkomend_getoond=true;
		}
		$bedrag=$gegevens["stap4"]["persoonsoptie_fin"]["inkoop"][$key]*(1-$gegevens["stap4"]["persoonsoptie_fin"]["korting"][$key]/100);
		$form->field_htmlcol("",$titel_bijkomend,array("html"=>"<div style=\"width:100px;float:left;\">".wt_cur($bedrag,false)."</div><div style=\"float:left;\">".$gegevens["stap4"]["persoonsoptie_fin"]["aantal"][$key]." x ".htmlentities($gegevens["stap4"]["persoonsoptie_fin"]["naam"][$key])."</div><div style=\"float:right;\"><input type=\"hidden\" name=\"wis_leverancierfactuur_extraoptie[".$key."]\" value=\"1\"><a href=\"#\" onclick=\"inkoopgegevens_bijkomend_factuur_wissen(this,".floatval($bedrag).");return false;\" class=\"noactive\"><img src=\"".$vars["path"]."pic/class.cms_delete.gif\" border=\"0\" width=\"14\" title=\"Deze kosten aan- en uitzetten bij de leveranciersfactuur\"></a></div>"),"",array("tr_class"=>"bijkomend_doorstrepen"));
	}
} else {
	$form->field_htmlcol("","Netto-accommodatie €",array("html"=>wt_cur($gegevens["stap1"]["inkoopnetto"],false)),"",array("tr_class"=>"inkoopgegevens_opvallend","td_cell_right_class"=>"wtform_cell_right uitkomst_inkoopnetto","tr_style"=>"display:none;"));
}

$form->field_htmlrow("","&nbsp;<input type=\"hidden\" name=\"input[extraopties_totaal]\" value=\"".htmlentities(wt_cur($temp_extraopties_totaal,false))."\">","",array("tr_style"=>"display:none;"));
$form->field_htmlcol("","Totaalfactuurbedrag €",array("html"=>wt_cur($temp_totaalbedrag,false)),"",array("tr_class"=>"inkoopgegevens_opvallend","td_cell_right_class"=>"wtform_cell_right uitkomst_totaalfactuurbedrag"));
$form->field_yesno("inkoop_van_0_toegestaan","Inkoop van 0 is toegestaan",array("field"=>"inkoop_van_0_toegestaan"),"","",array("tr_class"=>"inkoop_van_0_toegestaan","tr_style"=>"display:none;"));

$form->field_currency(0,"totaal_volgens_ontvangen_factuur","Totaal volgens ontvangen factuur €",array("field"=>"totaal_volgens_ontvangen_factuur"),"",array("negative"=>true),array("input_class"=>"wtform_input inkoopgegevens","add_html_after_field"=>"<span id=\"opmerking_totaal_volgens_ontvangen_factuur\" style=\"font-weight:bold;\"></span>"));

// betalingsverschil is op verzoek van Bert op "niet tonen" gezet (zodat niemand daar iets kan invullen) - 30-08-2013
$form->field_currency(0,"betalingsverschil","Betalingsverschil €",array("field"=>"betalingsverschil"),"",array("negative"=>true),array("tr_class"=>"tr_inkoopgegevens_betalingsverschil","input_class"=>"wtform_input inkoopgegevens"));

$form->field_htmlcol("","Saldo factuurbedrag €",array("html"=>""),"",array("tr_class"=>"inkoopgegevens_onopvallend","td_cell_right_class"=>"wtform_cell_right uitkomst_betalingssaldo"));
$form->field_select(0,"factuurbedrag_gecontroleerd","Factuurbedrag akkoord",array("field"=>"factuurbedrag_gecontroleerd"),"",array("selection"=>$vars["factuurbedrag_gecontroleerd"]),array("tr_style"=>"display:none;"));
$form->field_textarea(0,"factuur_opmerkingen","Opmerkingen factuur",array("field"=>"factuur_opmerkingen"));


$db->query("SELECT boeking_id FROM boeking WHERE boeking_id='".addslashes($_GET["bid"])."' AND eenmaliggecontroleerd=0;");
if($db->next_record()) {
	$form->field_htmlrow("","<hr><b>Eenmalige controle</b>");
	$form->field_yesno("eenmaliggecontroleerd","Bovenstaande bedragen zijn gecontroleerd en correct bevonden",array("field"=>"eenmaliggecontroleerd"));
}

$form->check_input();

if($form->filled) {
	if($form->input["factuurbedrag_gecontroleerd"]==2 and !$form->input["factuur_opmerkingen"]) {
			$form->error("factuur_opmerkingen","verplicht indien het factuurbedrag afwijkt");
	}
	if($form->input["factuurbedrag_gecontroleerd"]) {
		$betalingssaldo=round($_POST["input"]["totaalfactuurbedrag"],2);
		$betalingssaldo=$betalingssaldo-round($form->input["totaal_volgens_ontvangen_factuur"],2);
		$betalingssaldo=$betalingssaldo+round($form->input["betalingsverschil"],2);
		$betalingssaldo=round($betalingssaldo,2);
		if($betalingssaldo<>0) {
			$form->error("factuurbedrag_gecontroleerd","saldo factuurbedrag moet 0 zijn");
		}
	}
	if($form->input["factuurbedrag_gecontroleerd"]==1 and $form->input["betalingsverschil"]) {
		$form->error("betalingsverschil","niet van toepassing bij 'ja, alles klopt'");
	}
	if($form->input["factuurbedrag_gecontroleerd"]==2 and !$form->input["betalingsverschil"]) {
		$form->error("betalingsverschil","verplicht bij 'ja, maar het bedrag wijkt af'");
	}
	if($form->input["totaal_volgens_ontvangen_factuur"]<>0 and !$form->input["factuurnummer_leverancier"]) {
		$form->error("factuurnummer_leverancier","verplicht bij invullen factuurbedrag");
	}
	if($form->input["factuurnummer_leverancier"] and !$form->input["totaal_volgens_ontvangen_factuur"]) {
		$form->error("totaal_volgens_ontvangen_factuur","verplicht bij invullen factuurnummer");
	}
	if($form->input["factuurnummer_leverancier"] and !$form->input["besteldatum"]["unixtime"]) {
		$form->error("besteldatum","verplicht bij invullen factuurnummer");
	}
	if($temp["leverancier_aanbetaling_dagen"]<>"" or $temp["leverancier_eindbetaling_dagen_factuur"]<>"") {
		if($form->input["factuurnummer_leverancier"] and !$form->input["inkoopfactuurdatum"]["unixtime"]) {
			$form->error("inkoopfactuurdatum","verplicht bij invullen factuurnummer");
		}
	}
}

if($form->okay) {
	$form->save_db();

	if($temp["leverancier_bevestigmethode"]==1) {
		# factuurnummer_leverancier opslaan in leverancierscode (bij bestelmethode 'stuurt direct een factuurnummer')
		$db->query("UPDATE boeking SET leverancierscode=factuurnummer_leverancier WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."';");
	}

	# inkoopnetto + totaalfactuurbedrag (via javascript berekend) opslaan
	$db->query("UPDATE boeking SET inkoopnetto='".addslashes(str_replace(",",".",$_POST["input"]["inkoopnetto"]))."', totaalfactuurbedrag='".addslashes($_POST["input"]["totaalfactuurbedrag"])."' WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."';");

	# bij bestelstatus = "bevestiging afwachten" of "bevestigd": aan_leverancier_doorgegeven_naam opslaan indien die nog leeg is
	if($form->input["bestelstatus"]>=2 and !$gegevens["stap1"]["aan_leverancier_doorgegeven_naam"]) {
		$db->query("UPDATE boeking SET aan_leverancier_doorgegeven_naam='".addslashes(wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]))."' WHERE boeking_id='".intval($gegevens["stap1"]["boekingid"])."';");
	}

	# totale_reissom_inkoop_actueel berekenen en opslaan
	$nieuwe_gegevens=get_boekinginfo($gegevens["stap1"]["boekingid"]);
	$nieuwe_reissom_tabel=reissom_tabel($nieuwe_gegevens,$nieuwe_gegevens["stap1"]["accinfo"],"",true);
	$db->query("UPDATE boeking SET totale_reissom_inkoop='".addslashes($nieuwe_reissom_tabel["bedragen"]["inkoop"])."', totale_reissom_inkoop_actueel='".addslashes($nieuwe_reissom_tabel["bedragen"]["inkoop"])."' WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."';");
	$db->query("DELETE FROM boeking_optieinkoop WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."';");
	while(list($key,$value)=@each($nieuwe_reissom_tabel["bedragen"]["optieinkoop"])) {
		if($key) {
			$db->query("INSERT INTO boeking_optieinkoop SET boeking_id='".$gegevens["stap1"]["boekingid"]."', optiecategorie='".addslashes($key)."', bedrag='".$value."';");
		} elseif(!$gegevens["stap1"]["geannuleerd"]) {
			trigger_error("lege optiecategorie bij boeking ".$gegevens["stap1"]["boekingsnummer"],E_USER_NOTICE);
		}
	}

	# wijzigingen loggen
	if($temp["leverancier_bevestigmethode"]==1) {

	} elseif($temp["leverancier_bevestigmethode"]==2) {
		if($form->input["leverancierscode"]<>$gegevens["stap1"]["leverancierscode"]) chalet_log("bevestiging leverancier (".$form->input["leverancierscode"].")",true,true);
	} elseif($temp["leverancier_bevestigmethode"]==3) {
		if($form->input["leverancierscode"]<>$gegevens["stap1"]["leverancierscode"]) chalet_log("reserveringsnummer leverancier (".$form->input["leverancierscode"].")",true,true);
	} else {
		if($form->input["leverancierscode"]<>$gegevens["stap1"]["leverancierscode"]) chalet_log("reserveringsnummer leverancier (".$form->input["leverancierscode"].")",true,true);
	}
	if($form->input["bestelstatus"]<>$gegevens["stap1"]["bestelstatus"]) chalet_log("bestelstatus (".$vars["bestelstatus"][$form->input["bestelstatus"]].")",true,true);
	if($form->input["besteldatum"]["unixtime"] and $form->input["besteldatum"]["unixtime"]<>$gegevens["stap1"]["besteldatum"]) chalet_log("besteldatum (".DATUM("DAG D MAAND JJJJ",$form->input["besteldatum"]["unixtime"]).")",true,true);
	if($temp["leverancier_aanbetaling_dagen"]<>"" or $temp["leverancier_eindbetaling_dagen_factuur"]<>"") {
		if($form->input["inkoopfactuurdatum"]["unixtime"] and $form->input["inkoopfactuurdatum"]["unixtime"]<>$gegevens["stap1"]["inkoopfactuurdatum"]) chalet_log("inkoopfactuurdatum (".DATUM("DAG D MAAND JJJJ",$form->input["inkoopfactuurdatum"]["unixtime"]).")",true,true);
	}
	if($form->input["bestelstatus_schriftelijk_later"] and !$gegevens["stap1"]["bestelstatus_schriftelijk_later"]) {
		chalet_log("\"Schriftelijke bevestiging volgt binnen enkele dagen\" aangezet",true,true);
		$db->query("UPDATE boeking SET bestelstatus_schriftelijk_later_aanvinkmoment=NOW() WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
	}
	if(!$form->input["bestelstatus_schriftelijk_later"] and $gegevens["stap1"]["bestelstatus_schriftelijk_later"]) {
		chalet_log("\"Schriftelijke bevestiging volgt binnen enkele dagen\" uitgezet",true,true);
		$db->query("UPDATE boeking SET bestelstatus_schriftelijk_later_aanvinkmoment=NULL WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
	}
	if($form->input["factuurnummer_leverancier"]<>$gegevens["stap1"]["factuurnummer_leverancier"]) chalet_log("factuurnummer leverancier (".$form->input["factuurnummer_leverancier"].")",true,true);
	if($form->input["factuur_opmerkingen"]<>$gegevens["stap1"]["factuur_opmerkingen"]) chalet_log("opmerkingen factuur",true,true);

	# tijdelijk
	if($form->input["eenmaliggecontroleerd"]) chalet_log("inkoopbedragen zijn gecontroleerd en correct bevonden",true,true);

	if($form->input["inkoopbruto"]<>$gegevens["stap1"]["inkoopbruto"]) chalet_log("inkoopprijs bruto (€ ".@number_format($form->input["inkoopbruto"],2,",",".").")",true,true);
	if($form->input["inkoopcommissie"]<>$gegevens["stap1"]["inkoopcommissie"]) chalet_log("inkoopcommissie  (".@number_format($form->input["inkoopcommissie"],2,",",".")."%)",true,true);
	if($form->input["inkooptoeslag"]<>$gegevens["stap1"]["inkooptoeslag"]) chalet_log("inkooptoeslag (€ ".@number_format($form->input["inkooptoeslag"],2,",",".").")",true,true);
	if($form->input["inkoopkorting"]<>$gegevens["stap1"]["inkoopkorting"]) chalet_log("inkoopkorting (€ ".@number_format($form->input["inkoopkorting"],2,",",".").")",true,true);
	if($form->input["inkoopkorting_percentage"]<>$gegevens["stap1"]["inkoopkorting_percentage"]) chalet_log("inkoopkorting accommodatie (".@number_format($form->input["inkoopkorting_percentage"],2,",",".")."%)",true,true);
	if($form->input["inkoopkorting_euro"]<>$gegevens["stap1"]["inkoopkorting_euro"]) chalet_log("inkoopkorting accommodatie (€ ".@number_format($form->input["inkoopkorting_euro"],2,",",".").")",true,true);
	if($_POST["input"]["inkoopnetto"]<>$gegevens["stap1"]["inkoopnetto"]) chalet_log("inkoopprijs netto (€ ".@number_format($_POST["input"]["inkoopnetto"],2,",",".").")",true,true);

	if(!$gegevens["stap1"]["factuurbedrag_gecontroleerd"]) $gegevens["stap1"]["factuurbedrag_gecontroleerd"]="";
	if($form->input["factuurbedrag_gecontroleerd"]<>$gegevens["stap1"]["factuurbedrag_gecontroleerd"]) chalet_log("inkoopfactuurbedrag gecontroleerd: ".$vars["factuurbedrag_gecontroleerd"][$form->input["factuurbedrag_gecontroleerd"]],true,true);

	if($form->input["totaal_volgens_ontvangen_factuur"]<>$gegevens["stap1"]["totaal_volgens_ontvangen_factuur"]) chalet_log("totaal volgens ontvangen factuur (€ ".@number_format($form->input["totaal_volgens_ontvangen_factuur"],2,",",".").")",true,true);
	if($form->input["betalingsverschil"]<>$gegevens["stap1"]["betalingsverschil"]) chalet_log("betalingsverschil (€ ".@number_format($form->input["betalingsverschil"],2,",",".").")",true,true);

}
$form->end_declaration();

$layout->display_all("Boeking - Inkoopgegevens accommodatie");

function inkoopgegevens_actueel($naam,$bedrag_actueel,$bedrag_field) {
	global $inkoop_actueel,$gegevens;
	if($inkoop_actueel) {
		$return="<span id=\"".$naam."_actueel\">nu in tarieventabel: <span id=\"".$naam."_actueel_getal\">".wt_cur($bedrag_actueel,false)."</span>";
		$return.="</span>";
		if($gegevens["stap1"]["verzameltype_gekozentype_id"]) {
			$return.="<span style=\"color:grey;\"> (gekozen type)</span>";
		}
	}
	return $return;
}

?>