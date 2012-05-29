<?php

$mustlogin=true;
include("admin/vars.php");

$temp_gegevens=boekinginfo($_GET["bid"]);
$gegevens["stap1"]=$temp_gegevens["stap1"];
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
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm"); 
$form->settings["fullname"]="Naam";
$form->settings["layout"]["css"]=false;
$form->settings["db"]["table"]="boeking";
$form->settings["db"]["where"]="boeking_id='".addslashes($_GET["bid"])."'";
$form->settings["goto"]=$_GET["burl"];
$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";

# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
$form->settings["layout"]["goto_aname"]=true;

#_field: (obl),id,title,db,prevalue,options,layout

$form->field_hidden("bewerkdatetime",$gegevens["stap1"]["bewerkdatetime"]);
$form->field_noedit("toonper","Tariefoptie","",array("html"=>$vars["toonper"][$gegevens["stap1"]["accinfo"]["toonper"]].($gegevens["stap1"]["wederverkoop"] ? " (via een wederverkoop-site)" : "")));
$form->field_noedit("verkoop",($gegevens["stap1"]["accinfo"]["toonper"]==3||$gegevens["stap1"]["wederverkoop"] ? "Accommodatieprijs" : "Arrangementsprijs"),"",array("html"=>"&euro;&nbsp;".number_format($gegevens["stap1"]["verkoop_ongewijzigd"],2,',','.')));


if($gegevens["stap1"]["accinfo"]["toonper"]<>3 and !$gegevens["stap1"]["wederverkoop"]) {
#	$form->field_htmlrow("","<b>Let op!</b> Dit bedrag wordt altijd gehanteerd, ook al wordt het aantal personen gewijzigd. Wil je een bedrag wijzigen dat zich aanpast aan het aantal personen, doe dat dan onderaan deze pagina bij <a href=\"#opgeslagentarieven\">Opgeslagen tarieven op moment van boeken</a>.");
	$form->field_htmlrow("","<i>Het wijzigen van de arrangementsprijs kan bij <a href=\"#opgeslagentarieven\">Opgeslagen tarieven op moment van boeken</a>.</i>");
} else {
	$form->field_currency(0,"verkoop_gewijzigd",($gegevens["stap1"]["accinfo"]["toonper"]==3||$gegevens["stap1"]["wederverkoop"] ? "Accommodatieprijs" : "Arrangementsprijs")." gewijzigd",array("field"=>"verkoop_gewijzigd"));
}

if($gegevens["stap1"]["reisbureau_user_id"]) {
	$form->field_float(0,"commissie","Commissiepercentage accommodatie",array("field"=>"commissie"));
}

$form->field_htmlrow("","<hr>");
$form->field_currency(0,"annuleringsverzekering_poliskosten","Annuleringsverzekering poliskosten",array("field"=>"annuleringsverzekering_poliskosten"));
$form->field_currency(0,"annuleringsverzekering_percentage_1","Percentage ".$vars["annverz_soorten"][1],array("field"=>"annuleringsverzekering_percentage_1"));
$form->field_currency(0,"annuleringsverzekering_percentage_2","Percentage ".$vars["annverz_soorten"][2],array("field"=>"annuleringsverzekering_percentage_2"));
$form->field_currency(0,"annuleringsverzekering_percentage_3","Percentage ".$vars["annverz_soorten"][3],array("field"=>"annuleringsverzekering_percentage_3"));
$form->field_currency(0,"annuleringsverzekering_percentage_4","Percentage ".$vars["annverz_soorten"][4],array("field"=>"annuleringsverzekering_percentage_4"));

$form->field_htmlrow("","<hr>");
$form->field_currency(0,"schadeverzekering_percentage","Percentage Schade Logies Verblijven",array("field"=>"schadeverzekering_percentage"));
$form->field_currency(0,"accprijs","Verzekerd bedrag Schade Logies Verblijven",array("field"=>"accprijs"));

#$form->field_currency(0,"annuleringsverzekering_verzekerdbedrag","Verzekerd bedrag",array("field"=>"annuleringsverzekering_verzekerdbedrag"));
#$form->field_currency(0,"annuleringsverzekering_percentage","Annuleringsverzekering percentage",array("field"=>"annuleringsverzekering_percentage"));
#$form->field_noedit("optie_bedrag_binnen_annuleringsverzekering","Bedrag binnen annuleringsverzekering","",array("html"=>"&euro;&nbsp;".number_format($gegevens["fin"]["accommodatie_totaalprijs"]+$gegevens["stap4"]["optie_bedrag_binnen_annuleringsverzekering"],2,',','.')));
#$form->field_currency(0,"annuleringsverzekering_korting","Korting op bedrag annuleringsverzekering",array("field"=>"annuleringsverzekering_korting"),"",array("negative"=>true));
$form->field_htmlrow("","<hr>");
$form->field_currency(0,"verzekeringen_poliskosten","Verzekeringen poliskosten",array("field"=>"verzekeringen_poliskosten"));
$form->field_htmlrow("","<hr>");
$form->field_currency(0,"reisverzekering_poliskosten","Reisverzekering poliskosten",array("field"=>"reisverzekering_poliskosten"));
$form->field_htmlrow("","<hr>");
$form->field_currency(0,"reserveringskosten","Reserveringskosten",array("field"=>"reserveringskosten"));
#$form->field_htmlrow("","<hr>");
#$form->field_noedit("aanbetaling","Aanbetaling","",array("html"=>"&euro;&nbsp;".number_format($gegevens["fin"]["aanbetaling_ongewijzigd"],2,',','.')));
#$form->field_currency(0,"aanbetaling_gewijzigd","Aanbetaling gewijzigd",array("field"=>"aanbetaling_gewijzigd"));
$form->field_htmlrow("","<hr><b>Aanbetaling 1</b>");
$form->field_noedit("aanbetaling1","Bedrag","",array("html"=>"&euro;&nbsp;".number_format($gegevens["fin"]["aanbetaling_ongewijzigd"],2,',','.')));
$form->field_currency(0,"aanbetaling1_gewijzigd","Gewijzigd bedrag",array("field"=>"aanbetaling1_gewijzigd"));
$form->field_integer(1,"aanbetaling1_dagennaboeken","Aantal dagen na boeken",array("field"=>"aanbetaling1_dagennaboeken"));

$form->field_htmlrow("","<hr><b>Aanbetaling 2 (optioneel)</b>");
$form->field_currency(0,"aanbetaling2","Bedrag",array("field"=>"aanbetaling2"));
$form->field_date(0,"aanbetaling2_datum","Betaaldatum",array("field"=>"aanbetaling2_datum"),"","",array("calendar"=>true));

$form->field_htmlrow("","<hr><b>Eindbetaling</b>");
$form->field_integer(1,"totale_reissom_dagenvooraankomst","Aantal dagen voor aankomst",array("field"=>"totale_reissom_dagenvooraankomst"));

# Algemene opties
if(is_array($gegevens["stap4"]["algemene_optie_zelfgekozen"])) {
	$form->field_htmlrow("","<hr><b>Algemene opties</b>");
	while(list($key,$value)=each($gegevens["stap4"]["algemene_optie_zelfgekozen"])) {
		$form->field_currency(0,"optie_1_".$gegevens["stap4"]["algemene_optie"]["optie_onderdeel_id"]["alg".$key],$gegevens["stap4"]["algemene_optie"]["soort"]["alg".$key].": ".$gegevens["stap4"]["algemene_optie"]["naam"]["alg".$key],"",array("text"=>$gegevens["stap4"]["algemene_optie"]["verkoop"]["alg".$key]),array("negative"=>true));

		if($gegevens["stap1"]["reisbureau_user_id"]) {
			$form->field_float(0,"commissie_optie_".$i."_".$gegevens["stap4"]["algemene_optie"]["optie_onderdeel_id"]["alg".$key],"Commissiepercentage","",array("text"=>$gegevens["stap4"]["optie_onderdeelid_commissie_persoonnummer"][$key][$i]));
			$form->field_htmlrow("","<p>");
		}
	}
}


# Gewone opties
$form->field_htmlrow("","<hr>");
for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
	unset($optie_geselecteerd);
	$form->field_htmlrow("","<strong>Opties ".($i==1 ? "hoofdboeker" : "persoon ".$i)." (".($gegevens["stap3"][$i]["voornaam"]||$gegevens["stap3"][$i]["achternaam"] ? htmlentities(wt_naam($gegevens["stap3"][$i]["voornaam"],$gegevens["stap3"][$i]["tussenvoegsel"],$gegevens["stap3"][$i]["achternaam"])) : "<i>naam niet ingevoerd</i>").")</strong>");
	if(is_array($gegevens["stap4"]["opties"][$i])) {
		$optie_geselecteerd=true;
		while(list($key,$value)=each($gegevens["stap4"]["opties"][$i])) {
			$form->field_currency(0,"optie_".$i."_".$value,$gegevens["stap4"]["optie_onderdeelid_naam"][$value],"",array("text"=>$gegevens["stap4"]["optie_onderdeelid_verkoop_persoonnummer"][$value][$i]),array("negative"=>true));

			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$form->field_float(0,"commissie_optie_".$i."_".$value,"Commissiepercentage","",array("text"=>$gegevens["stap4"]["optie_onderdeelid_commissie_persoonnummer"][$value][$i]));
				$form->field_htmlrow("","<p>");
			}
		}
	}
	if($gegevens["stap3"][$i]["annverz"]) {
		$form->field_currency(0,"annverz_verzekerdbedrag_".$i,"Verzekerd bedrag annuleringsverzekering","",array("text"=>$gegevens["stap3"][$i]["annverz_verzekerdbedrag"]));
		$optie_geselecteerd=true;
	}
	if(!$optie_geselecteerd) {
		$form->field_htmlrow("","<i>deze persoon heeft geen opties geselecteerd</i>");
	}
	#$form->field_currency(0,"annuleringsverzekering_verzekerdbedrag","Verzekerd bedrag",array("field"=>"annuleringsverzekering_verzekerdbedrag"));

	
	$form->field_htmlrow("","<hr>");
}

$db->query("SELECT aantalpersonen, verkoop FROM boeking_tarief WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
if($db->num_rows()) {
	$form->field_htmlrow("","<a name=\"opgeslagentarieven\"></a><b>Opgeslagen tarieven op moment van boeken</b><br>Wordt gebruikt bij het wijzigen van het aantal personen.");
	while($db->next_record()) {
#		if(!$temp_minaantal) $temp_minaantal=$db->f("aantalpersonen");
		$temp_maxaantal=$db->f("aantalpersonen");
		$temp_getoond[$db->f("aantalpersonen")]=true;
		$form->field_currency(1,"boekingtarief_".$db->f("aantalpersonen"),($db->f("aantalpersonen")==$gegevens["stap1"]["aantalpersonen"] ? "<b>" : "")."Tarief bij ".$db->f("aantalpersonen")." ".($db->f("aantalpersonen")==1 ? "persoon" : "personen").($db->f("aantalpersonen")==$gegevens["stap1"]["aantalpersonen"] ? "<b>" : ""),"",array("text"=>$db->f("verkoop")),"",array("title_html"=>true));
		$temp_boekingtarief[$db->f("aantalpersonen")]=$db->f("verkoop");
	}
	$form->field_htmlrow("","<hr>");
	if($temp_maxaantal>0) {
		for($i=1;$i<$temp_maxaantal;$i++) {
			if(!$temp_getoond[$i]) $temp_toevoegen=true;
		}
	}

	if($temp_toevoegen) {
		$form->field_htmlrow("","<b>Opgeslagen tarieven toevoegen</b><br>Wordt gebruikt bij het wijzigen van het aantal personen.");
		for($i=1;$i<$temp_maxaantal;$i++) {
			if(!$temp_getoond[$i]) {
				$form->field_currency(0,"nieuwboekingtarief_".$i,"Tarief bij ".$i." ".($i==1 ? "persoon" : "personen"));
			}
		}
		$form->field_htmlrow("","<hr>");
	}
}

$form->check_input();

if($form->filled) {
	unset($lasteditortxt,$bewerkdatetime_verschilt);
	if($_POST["bewerkdatetime"] and $_POST["bewerkdatetime"]<>$gegevens["stap1"]["bewerkdatetime"]) {
		if($gegevens["stap1"]["lasteditor"]==0) {
			$lasteditortxt=" door de klant";
		} else {
			$db->query("SELECT voornaam FROM user WHERE user_id='".addslashes($gegevens["stap1"]["lasteditor"])."';");
			if($db->next_record()) {
				$lasteditortxt=" door ".htmlentities($db->f("voornaam"));
			}
		}
		$bewerkdatetime_verschilt="Deze boeking is na het openen van dit formulier nog gewijzigd".$lasteditortxt.". Opslaan van de gegevens is nu niet mogelijk. <a href=\"".$_SERVER["REQUEST_URI"]."\">Herlaad dit formulier<a/> of klik nogmaals op OPSLAAN om de gegevens toch op te slaan";
		$form->error("bewerkdatetime",$bewerkdatetime_verschilt,false,true);
	}
	if($form->input["aanbetaling2"]<>0 and !$form->input["aanbetaling2_datum"]["unixtime"]) {
		$form->error("aanbetaling2_datum","verplicht bij gebruik aanbetaling 2");
	}
	if(!$form->input["aanbetaling2"] and $form->input["aanbetaling2_datum"]["unixtime"]>0) {
		$form->error("aanbetaling2","verplicht bij gebruik aanbetaling 2");
	}
}

if($form->okay) {
	$form->save_db();
	@reset($form->input);
	while(list($key,$value)=@each($form->input)) {
		if(ereg("^optie_([0-9]+)_([0-9]+)$",$key,$regs)) {
			$value=ereg_replace(",",".",$value);
			$db->query("UPDATE boeking_optie SET verkoop='".addslashes($value)."' WHERE persoonnummer='".addslashes($regs[1])."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND optie_onderdeel_id='".addslashes($regs[2])."';");
			if($gegevens["stap4"]["verkoop_optie_onderdeelid"][$regs[2]]=="0.00") $gegevens["stap4"]["verkoop_optie_onderdeelid"][$regs[2]]="";
			if($value<>$gegevens["stap4"]["verkoop_optie_onderdeelid"][$regs[2]]) {
#echo $regs[2];
#echo $gegevens["stap4"]["algemene_optie"]["naam_op_onderdeel_id"][$regs[2]];
#exit;			
				if($gegevens["stap4"]["algemene_optie"]["naam_op_onderdeel_id"][$regs[2]]) {
					chalet_log("optie-tarief ".$gegevens["stap4"]["algemene_optie"]["naam_op_onderdeel_id"][$regs[2]]." (€ ".@number_format($value,2,',','.').")",true,true);
				} else {
					chalet_log("optie-tarief ".$gegevens["stap4"]["optie_onderdeelid_naam"][$regs[2]]." (€ ".@number_format($value,2,',','.').")",true,true);				
				}
			}
		} elseif(ereg("^commissie_optie_([0-9]+)_([0-9]+)$",$key,$regs)) {
			$value=ereg_replace(",",".",$value);
			$db->query("UPDATE boeking_optie SET commissie='".addslashes($value)."' WHERE persoonnummer='".addslashes($regs[1])."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND optie_onderdeel_id='".addslashes($regs[2])."';");
			if($gegevens["stap4"]["optie_onderdeelid_commissie_persoonnummer"][$regs[2]][$regs[1]]=="0.00") $gegevens["stap4"]["optie_onderdeelid_commissie_persoonnummer"][$regs[2]][$regs[1]]="";
			
#			$return["stap4"][$db->f("status")]["optie_onderdeelid_commissie_persoonnummer"][$db->f("optie_onderdeel_id")][$db->f("persoonnummer")]
			
			if($value<>$gegevens["stap4"]["optie_onderdeelid_commissie_persoonnummer"][$regs[2]][$regs[1]]) {
				chalet_log("optie-commissie ".$gegevens["stap4"]["optie_onderdeelid_naam"][$regs[2]]." (".number_format($value,2,',','.')."%)",true,true);
			}
		} elseif(ereg("^annverz_verzekerdbedrag_([0-9]+)$",$key,$regs)) {
			$value=ereg_replace(",",".",$value);
			$db->query("UPDATE boeking_persoon SET annverz_verzekerdbedrag='".addslashes($value)."' WHERE persoonnummer='".addslashes($regs[1])."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			if($value<>$gegevens["stap3"][$regs[1]]["annverz_verzekerdbedrag"]) {
				chalet_log("annuleringsverzekering verzekerd bedrag persoon ".$regs[1]." (€ ".number_format($value,2,',','.').")",true,true);
			}
		} elseif(ereg("^boekingtarief_([0-9]+)$",$key,$regs)) {
			$value=ereg_replace(",",".",$value);
			$db->query("UPDATE boeking_tarief SET verkoop='".addslashes($value)."' WHERE aantalpersonen='".addslashes($regs[1])."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
			if($value<>$temp_boekingtarief[$regs[1]]) {
				chalet_log("opgeslagen tarief bij ".$regs[1]." ".($regs[1]==1 ? "persoon" : "personen")." gewijzigd (van € ".number_format($temp_boekingtarief[$regs[1]],2,',','.')." naar € ".number_format($value,2,',','.').")",true,true);
				if($regs[1]==$gegevens["stap1"]["aantalpersonen"]) {
					$db->query("UPDATE boeking SET verkoop='".addslashes($value)."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				}
			}
		} elseif(ereg("^nieuwboekingtarief_([0-9]+)$",$key,$regs)) {
			if($value>0) {
				$value=ereg_replace(",",".",$value);
				$db->query("INSERT INTO boeking_tarief SET verkoop='".addslashes($value)."', aantalpersonen='".addslashes($regs[1])."', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				chalet_log("toegevoegd: opgeslagen tarief bij ".$regs[1]." ".($regs[1]==1 ? "persoon" : "personen")." (€ ".number_format($value,2,',','.').")",true,true);
			}
		}
	}
	if($gegevens["stap1"]["reserveringskosten"]=="0.00") $gegevens["stap1"]["reserveringskosten"]="";
	if($form->input["reserveringskosten"]=="0.00") $form->input["reserveringskosten"]="";
	if($gegevens["stap1"]["verkoop_gewijzigd"]=="0.00") $gegevens["stap1"]["verkoop_gewijzigd"]="";
	if($gegevens["stap1"]["annuleringsverzekering_percentage_1"]=="0.00") $gegevens["stap1"]["annuleringsverzekering_percentage_1"]="";
	if($gegevens["stap1"]["annuleringsverzekering_percentage_2"]=="0.00") $gegevens["stap1"]["annuleringsverzekering_percentage_2"]="";
	if($gegevens["stap1"]["annuleringsverzekering_percentage_3"]=="0.00") $gegevens["stap1"]["annuleringsverzekering_percentage_3"]="";
	if($gegevens["stap1"]["annuleringsverzekering_percentage_4"]=="0.00") $gegevens["stap1"]["annuleringsverzekering_percentage_4"]="";
#	if($gegevens["stap1"]["annuleringsverzekering_percentage"]=="0.00") $gegevens["stap1"]["annuleringsverzekering_percentage"]="";
	if($gegevens["stap1"]["annuleringsverzekering_poliskosten"]=="0.00") $gegevens["stap1"]["annuleringsverzekering_poliskosten"]="";
	if($gegevens["stap1"]["reisverzekering_poliskosten"]=="0.00") $gegevens["stap1"]["reisverzekering_poliskosten"]="";
	if($gegevens["stap1"]["verzekeringen_poliskosten"]=="0.00") $gegevens["stap1"]["verzekeringen_poliskosten"]="";


	if($gegevens["stap1"]["reisbureau_user_id"]) {
		if($form->input["commissie"]<>$gegevens["stap1"]["commissie"]) {
			chalet_log("commissiepercentage accommodatie gewijzigd van ".$gegevens["stap1"]["commissie"]."% naar ".$form->input["commissie"]."%",true,true);
		}
	}

	if($form->input["aanbetaling1_gewijzigd"]<>$gegevens["stap1"]["aanbetaling1_gewijzigd"]) chalet_log("aanbetaling 1 - gewijzigd bedrag (".@number_format($form->input["aanbetaling1_gewijzigd"],2,',','.').")",true,true);
	if($form->input["aanbetaling1_dagennaboeken"]<>$gegevens["stap1"]["aanbetaling1_dagennaboeken"]) chalet_log("aanbetaling 1 - aantal dagen na boeken (".$form->input["aanbetaling1_dagennaboeken"].")",true,true);
	if($form->input["aanbetaling2"]<>$gegevens["stap1"]["aanbetaling2"]) chalet_log("aanbetaling 2 - bedrag (".@number_format($form->input["aanbetaling2"],2,',','.').")",true,true);
	if($form->input["aanbetaling2_datum"]["unixtime"]<>$gegevens["stap1"]["aanbetaling2_datum"]) chalet_log("aanbetaling 2 - betaaldatum (".date("d-m-Y",$form->input["aanbetaling2_datum"]["unixtime"]).")",true,true);
	if(isset($form->input["verkoop_gewijzigd"]) and $form->input["verkoop_gewijzigd"]<>$gegevens["stap1"]["verkoop_gewijzigd"]) chalet_log(($gegevens["stap1"]["accinfo"]["toonper"]==3||$gegevens["stap1"]["wederverkoop"] ? "accommodatieprijs" : "arrangementsprijs")." (€ ".ereg_replace("\.",",",@number_format($form->input["verkoop_gewijzigd"],2,',','.')).")",true,true);
#	if($form->input["annuleringsverzekering_percentage"]<>$gegevens["stap1"]["annuleringsverzekering_percentage"]) chalet_log("annuleringsverzekering percentage (".ereg_replace("\.",",",$form->input["annuleringsverzekering_percentage"]).")",true,true);
	if($form->input["annuleringsverzekering_percentage_1"]<>$gegevens["stap1"]["annuleringsverzekering_percentage_1"]) chalet_log("percentage annuleringsverzekering ".strtolower($vars["annverz_soorten"][1])." (".ereg_replace("\.",",",$form->input["annuleringsverzekering_percentage_1"]).")",true,true);
	if($form->input["annuleringsverzekering_percentage_2"]<>$gegevens["stap1"]["annuleringsverzekering_percentage_2"]) chalet_log("percentage annuleringsverzekering ".strtolower($vars["annverz_soorten"][2])." (".ereg_replace("\.",",",$form->input["annuleringsverzekering_percentage_2"]).")",true,true);
	if($form->input["annuleringsverzekering_percentage_3"]<>$gegevens["stap1"]["annuleringsverzekering_percentage_3"]) chalet_log("percentage annuleringsverzekering ".strtolower($vars["annverz_soorten"][3])." (".ereg_replace("\.",",",$form->input["annuleringsverzekering_percentage_3"]).")",true,true);
	if($form->input["annuleringsverzekering_percentage_4"]<>$gegevens["stap1"]["annuleringsverzekering_percentage_4"]) chalet_log("percentage annuleringsverzekering ".strtolower($vars["annverz_soorten"][4])." (".ereg_replace("\.",",",$form->input["annuleringsverzekering_percentage_4"]).")",true,true);
#	if($form->input["annuleringsverzekering_verzekerdbedrag"]<>$gegevens["stap1"]["annuleringsverzekering_verzekerdbedrag"]) chalet_log("annuleringsverzekering verzekerd bedrag (".ereg_replace("\.",",",$form->input["annuleringsverzekering_verzekerdbedrag"]).")",true,true);
	if($gegevens["stap1"]["annuleringsverzekering_poliskosten"] and $form->input["annuleringsverzekering_poliskosten"]<>$gegevens["stap1"]["annuleringsverzekering_poliskosten"]) chalet_log("annuleringsverzekering poliskosten (".ereg_replace("\.",",",$form->input["annuleringsverzekering_poliskosten"]).")",true,true);
	if($gegevens["stap1"]["reisverzekering_poliskosten"] and $form->input["reisverzekering_poliskosten"]<>$gegevens["stap1"]["reisverzekering_poliskosten"]) chalet_log("reisverzekering poliskosten (".ereg_replace("\.",",",$form->input["reisverzekering_poliskosten"]).")",true,true);
	if($form->input["verzekeringen_poliskosten"]<>$gegevens["stap1"]["verzekeringen_poliskosten"]) chalet_log("verzekeringen poliskosten (".ereg_replace("\.",",",$form->input["verzekeringen_poliskosten"]).")",true,true);
	if($form->input["reserveringskosten"]<>$gegevens["stap1"]["reserveringskosten"]) chalet_log("reserveringskosten (".ereg_replace("\.",",",$form->input["reserveringskosten"]).")",true,true);
 
	if($form->input["schadeverzekering_percentage"]<>$gegevens["stap1"]["schadeverzekering_percentage"]) chalet_log("percentage schade logies verblijven (".ereg_replace("\.",",",$form->input["schadeverzekering_percentage"]).")",true,true);
	if($form->input["accprijs"]<>$gegevens["stap1"]["accprijs"]) chalet_log("verzekerd bedrag schade logies verblijven (".ereg_replace("\.",",",$form->input["accprijs"]).")",true,true);

}
$form->end_declaration();

$layout->display_all("Boeking - Bedragen");

?>