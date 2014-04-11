<?php


$init_loginclass_voor_chaletmedewerkers=true;

if(!$mustlogin and !$boeking_wijzigen) {
	session_start();
	$vars["verberg_zoekenboeklinks"]=true;
	$vars["verberg_directnaar"]=true;

	include("admin/vars.php");

	if(($vars["website"]=="C" or $vars["website"]=="Z") and $_SERVER["HTTPS"]<>"on" and !$_POST and !$vars["lokale_testserver"] and !$vars["acceptatie_testserver"]) {
		# deze pagina altijd via https
#		if($_SERVER["REMOTE_ADDR"]=="82.173.186.80") {
			header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
			exit;
#		}
	}

	# Indien geen sessie maar wel een cookie: sessie vullen met cookie
	if(!$_SESSION["boeking"]["boekingid"] and ereg("^([0-9]+)_([a-z0-9]{8})$",$_COOKIE["CHALET"]["boeking"]["boekingid"],$regs)) {
		if($regs[2]==boeking_veiligheid($regs[1])) {
			$db->query("SELECT type_id FROM boeking WHERE boeking_id='".addslashes($regs[1])."';");
			if($db->next_record()) {
#				$_SESSION["boeking"]["boekingid"]=$regs[1];
				$_SESSION["boeking"]["boekingid"][$regs[1]]=true;
#				$_SESSION["boeking"]["typeid"]=$db->f("type_id");
			}
			$controleer_beschikbaarheid=true;
		}
	}
}

#
# Indien _GET leeg is (en dus alleen boeken.php is opgevraagd): terug naar hoofdpagina
#
if(!$_GET) {
	header("Location: ".$path);
	exit;
}

if($boeking_wijzigen) {
	# Bij wijzigen boeking: alleen toegang tot stap 1 tot en met 4
	if($_GET["stap"]>4) {
		header("Location: ".$path."bsys.php");
		exit;
	}
}

#unset($gegevens);
if($_GET["tid"]) {
	$gegevens["stap1"]["typeid"]=$_GET["tid"];
	$gegevens["stap1"]["aankomstdatum"]=$_GET["d"];
	$gegevens["stap1"]["aantalpersonen"]=$_GET["ap"];
	$accinfo=accinfo($_GET["tid"]);

	# Sessie wissen indien een andere accommodatie wordt geboekt
#	if($_SESSION["boeking"]["typeid"] and $_SESSION["boeking"]["typeid"]<>$_GET["tid"]) {
#		unset($_SESSION["boeking"]);
#	}
	# Cookie wissen indien een andere accommodatie wordt geboekt
	if($_COOKIE["CHALET"]["boeking"]["typeid"]<>$_GET["tid"]) {
		unset($_COOKIE["CHALET"]["boeking"]["boekingid"]);
		setcookie("CHALET[boeking][boekingid]","_leeg_",time()+60);
		setcookie("CHALET[boeking][boekingid]","",time()-864000);
	}
	$controleer_beschikbaarheid=true;
}

#
# Status bepalen
#
if($mustlogin) {
	$status=1;
	$persoonstatus=2;
} else {
	$status=2;
	$persoonstatus=2;
}

if(!$gegevens["stap1"]["boekingid"] and $_GET["bfbid"] and $_SESSION["boeking"]["boekingid"][$_GET["bfbid"]]) {
	$gegevens["stap1"]["boekingid"]=$_GET["bfbid"];
}

#	if(is_array($_SESSION["boeking"]["boekingid"])) {
#		if(count($_SESSION["boeking"]["boekingid"])==1) {
#			list($gegevens["stap1"]["boekingid"],$value)=$_SESSION["boeking"]["boekingid"];
#		} elseif($_GET["bfbid"]) {
#			$gegevens["stap1"]["boekingid"]=$_GET["bfbid"];
#		}
#		if(!$_SESSION["boeking"]["boekingid"][$gegevens["stap1"]["boekingid"]]) unset($gegevens["stap1"]["boekingid"]);
#	} else {
#		$gegevens["stap1"]["boekingid"]=$_SESSION["boeking"]["boekingid"];
#	}
#}

#echo wt_dump($gegevens);

if($gegevens["stap1"]["boekingid"]) {
	$temp_gegevens=boekinginfo($gegevens["stap1"]["boekingid"]);
	if($temp_gegevens["stap1"]["boekingid"]) {
		if($_GET["tid"] and $_GET["tid"]<>$temp_gegevens["stap1"]["typeid"]) {
#			unset($_SESSION["boeking"]);
#			$_GET["stap"]=1;
		} else {
			$gegevens["stap1"]=$temp_gegevens["stap1"];
			$accinfo=accinfo($gegevens["stap1"]["typeid"],$gegevens["stap1"]["aankomstdatum"],$gegevens["stap1"]["aantalpersonen"]);

			# Cookie plaatsen (zodat de einddatum steeds weer doorschuift)
			setcookie("CHALET[boeking][boekingid]",$gegevens["stap1"]["boekingid"]."_".boeking_veiligheid($gegevens["stap1"]["boekingid"]),time()+259200);

			if(!$_GET["stap"]) {
				$_GET["stap"]=$gegevens["stap1"]["stap_voltooid"]+1;
				if($_GET["stap"]>5) {
					unset($_SESSION["boeking"]);
					unset($_SESSION["boeking"]);
					unset($_COOKIE["CHALET"]["boeking"]["boekingid"]);
					setcookie("CHALET[boeking][boekingid]","_leeg_",time()+60);
					setcookie("CHALET[boeking][boekingid]","",time()-864000);
					header("Location: ".$_SERVER["REQUEST_URI"]);
					exit;
				}
			}

			$gegevens["stap_voltooid"][0]=true;
			for($i=1;$i<=$gegevens["stap1"]["stap_voltooid"];$i++) {
				$gegevens["stap_voltooid"][$i]=true;
			}

			# Kijken of de accommodatie op de ingevulde datum nog wel beschikbaar is
			if($controleer_beschikbaarheid) {
				$db->query("SELECT tr.beschikbaar FROM tarief tr, seizoen s WHERE tr.seizoen_id=s.seizoen_id AND s.seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tr.week='".addslashes($gegevens["stap1"]["aankomstdatum"])."' AND tr.type_id='".addslashes($accinfo["typeid"])."';");
				if(!$db->num_rows()) {
					$niet_beschikbaar=true;
					unset($_SESSION["boeking"]);
					unset($_COOKIE["CHALET"]["boeking"]["boekingid"]);
					setcookie("CHALET[boeking][boekingid]","_leeg_",time()+60);
					setcookie("CHALET[boeking][boekingid]","",time()-864000);
				}
			}
		}
	}

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
	if($temp_gegevens["stap4"][2]) {
		$gegevens["stap4"]=$temp_gegevens["stap4"][2];
		$gegevens["stap4"]["actieve_status"]=2;
		$gegevens["fin"]=$temp_gegevens["fin"][2];
	} else {
		$gegevens["stap4"]=$temp_gegevens["stap4"][1];
		$gegevens["stap4"]["actieve_status"]=1;
		$gegevens["fin"]=$temp_gegevens["fin"][1];
	}
	if($temp_gegevens["stap4"][1] and $temp_gegevens["stap4"][2]) $nog_niet_goedgekeurd[4]=true;
}

# flexibel
if($gegevens["stap1"]["flexibel"]) {
	$vars["reisverzekering_mogelijk"]=false;
}

if(!$gegevens["stap_voltooid"][2]) {

	if($vars["chalettour_logged_in"]) {
		$gegevens["stap2"]["email"]=$login_rb->vars["email"];
	} elseif(!$voorkant_cms or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		$temp_naw=getnaw();

		$gegevens["stap2"]["voornaam"]=$temp_naw["voornaam"];
		$gegevens["stap2"]["tussenvoegsel"]=$temp_naw["tussenvoegsel"];
		$gegevens["stap2"]["achternaam"]=$temp_naw["achternaam"];
		$gegevens["stap2"]["adres"]=$temp_naw["adres"];
		$gegevens["stap2"]["postcode"]=$temp_naw["postcode"];
		$gegevens["stap2"]["plaats"]=$temp_naw["plaats"];
		$gegevens["stap2"]["land"]=$temp_naw["land"];
		$gegevens["stap2"]["telefoonnummer"]=$temp_naw["telefoonnummer"];
		$gegevens["stap2"]["mobielwerk"]=$temp_naw["mobielwerk"];
		$gegevens["stap2"]["email"]=$temp_naw["email"];
		$gegevens["stap2"]["geboortedatum"]=$temp_naw["geboortedatum"];
		$gegevens["stap2"]["geslacht"]=$temp_naw["geslacht"];
	} elseif($voorkant_cms) {
#		$gegevens["stap2"]["email"]="aanvraagnr".$gegevens["stap1"]["boekingid"]."@chalet.nl";
	}

	# Gegevens overnemen van gekoppelde optieaanvraag
	if($gegevens["stap1"]["optieaanvraag_id"]) {
		$db->query("SELECT voornaam, tussenvoegsel, achternaam, adres, postcode, plaats, land, telefoonnummer, mobielwerk, email FROM optieaanvraag WHERE optieaanvraag_id='".addslashes($gegevens["stap1"]["optieaanvraag_id"])."';");
		if($db->next_record()) {
			$gegevens["stap2"]["voornaam"]=$db->f("voornaam");
			$gegevens["stap2"]["tussenvoegsel"]=$db->f("tussenvoegsel");
			$gegevens["stap2"]["achternaam"]=$db->f("achternaam");
			$gegevens["stap2"]["adres"]=$db->f("adres");
			$gegevens["stap2"]["postcode"]=$db->f("postcode");
			$gegevens["stap2"]["plaats"]=$db->f("plaats");
			$gegevens["stap2"]["land"]=$db->f("land");
			$gegevens["stap2"]["telefoonnummer"]=$db->f("telefoonnummer");
			$gegevens["stap2"]["mobielwerk"]=$db->f("mobielwerk");
			$gegevens["stap2"]["email"]=$db->f("email");
		}
	}
}

if(!$_GET["stap"]) $_GET["stap"]=1;

if($mustlogin or $boeking_wijzigen or ($accinfo["tonen"] and !$niet_beschikbaar)) {

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm");
	$form->settings["fullname"]="Boeking";
	$form->settings["layout"]["css"]=false;
	$form->settings["layout"]["goto_aname"]="kop";
	$form->settings["language"]=$vars["taal"];

	if(($mustlogin or $voorkant_cms) and $_GET["stap"]==4) {
		# Submit-button bovenaan weergeven
		$form->settings["layout"]["top_submit_button"]=true;
	}

	if($mustlogin and $nog_niet_goedgekeurd[$_GET["stap"]]) $form->field_htmlrow("","<b>Let op: onderstaande gegevens bevatten wijzigingen die nog niet zijn goedgekeurd</b>");

	if($_GET["burl"]) {
		$form->settings["goto"]=$_GET["burl"];
	} else {
		$form->settings["goto"]=txt("menu_boeken").".php?bfbid=".$gegevens["stap1"]["boekingid"]."&stap=".($_GET["r"]>$_GET["stap"] ? $_GET["r"] : ($_GET["stap"]+1));
#		$form->settings["goto"]=txt("menu_boeken").".php?A=".""."&bfbid=".$gegevens["stap1"]["boekingid"]."&stap=".($_GET["r"]>$_GET["stap"] ? $_GET["r"] : ($_GET["stap"]+1));
	}

	if($_GET["stap"]==5) {
		$form->settings["message"]["submitbutton"][$vars["taal"]]=strtoupper(txt("boekingbevestigen","boeken"));
	} elseif($_GET["burl"]) {
		$form->settings["message"]["submitbutton"][$vars["taal"]]=strtoupper(txt("opslaan","boeken"));
	} else {
		if($_GET["r"] and $_GET["r"]>$_GET["stap"]) {
			$form->settings["message"]["submitbutton"][$vars["taal"]]=strtoupper(txt("opslaan","boeken"))." ".txt("enterugnaarstap","boeken")." ".$_GET["r"];
		} else {
			$form->settings["message"]["submitbutton"][$vars["taal"]]=strtoupper(txt("opslaan","boeken"))." ".txt("ennaarstap","boeken")." ".($_GET["stap"]+1);
		}
	}

	# #_field: (obl),id,title,db,prevalue,options,layout

	if($mustlogin) $form->field_hidden("bewerkdatetime",$gegevens["stap1"]["bewerkdatetime"]);

	if($_GET["stap"]==1) {
		if($mustlogin) {

			if($gegevens["stap1"]["geannuleerd"]) {
				$form->field_htmlrow("","<B style=\"color:red\">=== Let op! Deze boeking is geannuleerd ===</B>");
				$form->field_yesno("geannuleerd","boeking is geannuleerd (<i>vinkje uitzetten om te de-annuleren; heeft geen gevolgen voor de boekhouding</i>)","",array("selection"=>$gegevens["stap1"]["geannuleerd"]),"",array("title_html"=>true));
			}

			if($gegevens["stap1"]["boekingsnummer"] or ($_POST["frm_filled"] and $_POST["input"]["goedgekeurd"])) {
				$layout->bodyonload="toggle_display('tr_reserveringsnummer',1);setHgt();";
			}
			if($gegevens["stap1"]["bevestigdatum"]) {
				$form->field_yesno("goedgekeurd","deze accommodatie is bevestigd","",array("selection"=>$gegevens["stap1"]["goedgekeurd"]),"",array("onclick"=>"toggle_display('tr_reserveringsnummer',this.checked);"));
			}

			# Leveranciers laden
			$db->query("SELECT leverancier_id, naam, beheerder FROM leverancier ORDER BY naam;");
			while($db->next_record()) {
				if($db->f("beheerder")==1) {
					$alle_beheerders[$db->f("leverancier_id")]=$db->f("naam")." (".substr("000".$db->f("leverancier_id"),-3).")";
				} else {
					$alle_leveranciers[$db->f("leverancier_id")]=$db->f("naam")." (".substr("000".$db->f("leverancier_id"),-3).")";
				}
			}

			if($gegevens["stap1"]["boekingsnummer"]) {
				if(strlen($gegevens["stap1"]["boekingsnummer"])>9) {
					$reserveringsnummer_1=substr($gegevens["stap1"]["boekingsnummer"],0,7);
					$reserveringsnummer_2=substr($gegevens["stap1"]["boekingsnummer"],-6);
				} else {
					$reserveringsnummer=$gegevens["stap1"]["boekingsnummer"];
				}
			} else {
				#
				# Boekingsnummer vaststellen /bepalen
				#

				if(date("YmdH")<"2011093015") {

					# OUDE METHODE

					# Deel 1 (1e deel)
					$reserveringsnummer_1=$gegevens["stap1"]["website"].substr(date("Y"),3,1).date("m");
					$db->query("SELECT SUBSTRING(boekingsnummer,5,3) AS boekingsnummer FROM boeking WHERE SUBSTRING(boekingsnummer,2,3)='".addslashes(substr($reserveringsnummer_1,1))."' ORDER BY SUBSTRING(boekingsnummer,5,3) DESC LIMIT 0,1;");
					if($db->next_record()) {
						$reserveringsnummer_1.=substr("00".($db->f("boekingsnummer")+1),-3);
					} else {
						$reserveringsnummer_1.="001";
					}

					# Leverancierid
					if($gegevens["stap1"]["leverancierid"]) {
						$leverancierid=$gegevens["stap1"]["leverancierid"];
					} else {
						$db->query("SELECT l.leverancier_id FROM leverancier l, accommodatie a, type t WHERE t.leverancier_id=l.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($gegevens["stap1"]["typeid"])."';");
						$db->next_record();
						$leverancierid=$db->f("leverancier_id");
					}

					# Deel 2 (2e deel)
					$reserveringsnummer_2=get_reserveringsnummer_2($leverancierid,$gegevens["stap1"]["aankomstdatum_exact"]);
				} else {
					# Nieuwe methode
					if($gegevens["stap1"]["website"]=="X" or $gegevens["stap1"]["website"]=="Y") {
						// Laatste nummer Venturasol-boekingen opvragen
						$db->query("SELECT SUBSTR(boekingsnummer,6,4) AS volgnummer FROM boeking WHERE SUBSTR(boekingsnummer,2,4)='".date("ym")."' AND CHAR_LENGTH(boekingsnummer)=9 AND (website='X' OR website='Y') ORDER BY 1 DESC LIMIT 0,1;");
					} else {
						// Laatste nummer boekingen overige sites opvragen
						$db->query("SELECT SUBSTR(boekingsnummer,6,4) AS volgnummer FROM boeking WHERE SUBSTR(boekingsnummer,2,4)='".date("ym")."' AND CHAR_LENGTH(boekingsnummer)=9 AND website!='X' AND website!='Y' ORDER BY 1 DESC LIMIT 0,1;");
					}
#					echo $db->lq;
					if($db->next_record()) {
						$reserveringsnummer=$gegevens["stap1"]["website"].date("ym").strval((intval($db->f("volgnummer"))+1));
					} else {
						if($gegevens["stap1"]["website"]=="X" or $gegevens["stap1"]["website"]=="Y") {
							// boekingen van Venturasol beginnen na jaar/maand te tellen bij 3001
							$reserveringsnummer=$gegevens["stap1"]["website"].date("ym")."3001";
						} else {
							// boekingen van overige sites beginnen na jaar/maand te tellen bij 5001
							$reserveringsnummer=$gegevens["stap1"]["website"].date("ym")."5001";
						}
					}
				}
			}
			if($reserveringsnummer_1) {
				$form->field_text(($gegevens["stap1"]["boekingsnummer"] || $_POST["input"]["goedgekeurd"] ? 1 : 0),"reserveringsnummer_1","Reserveringsnummer 1e deel","",array("text"=>$reserveringsnummer_1),"",array("tr_class"=>"tr_reserveringsnummer"));
				$form->field_text(($gegevens["stap1"]["boekingsnummer"] || $_POST["input"]["goedgekeurd"] ? 1 : 0),"reserveringsnummer_2","Reserveringsnummer 2e deel","",array("text"=>$reserveringsnummer_2),"",array("tr_class"=>"tr_reserveringsnummer"));
			} else {
				if($gegevens["stap1"]["boekingsnummer"]) {
					# hidden input[reserveringsnummer_2] om de oude javascript-functies nog te kunnen laten draaien
					$form->field_htmlrow("","<input type=\"hidden\" name=\"input[reserveringsnummer_2]\" value=\"\">");
				} else {
					# melding + hidden input[reserveringsnummer_2] om de oude javascript-functies nog te kunnen laten draaien
#					$form->field_htmlrow("","<div style=\"padding:5px;background-color:yellow;border:1px solid #000000;\">Let op: vanaf 1 oktober 2011 is de structuur van het reserveringsnummer gewijzigd in: [WEBSITE-LETTER][JJ][MM][VOLGNUMMER]</div><input type=\"hidden\" name=\"input[reserveringsnummer_2]\" value=\"\">","",array("tr_class"=>"tr_reserveringsnummer"));
					$form->field_htmlrow("","<input type=\"hidden\" name=\"input[reserveringsnummer_2]\" value=\"\">","",array("tr_class"=>"tr_reserveringsnummer"));
				}
				$form->field_text(($gegevens["stap1"]["boekingsnummer"] || $_POST["input"]["goedgekeurd"] ? 1 : 0),"reserveringsnummer","Reserveringsnummer","",array("text"=>$reserveringsnummer),"",array("tr_class"=>"tr_reserveringsnummer"));
			}
			if(!$gegevens["stap1"]["goedgekeurd"]) {
				if($accinfo["verzameltype"]) {
					#
					# Verzameltype
					#

					$voorraad_afboeken_keuzes["niet_bijwerken"]="voorraad niet bijwerken";

					# Uit db halen welke types gekoppeld zijn
					unset($inquery);
					$db->query("SELECT type_id, begincode, leverancier_id, beheerder_id, eigenaar_id, tnaam FROM view_accommodatie WHERE verzameltype_parent='".addslashes(($_POST["input"]["typeid"] ? $_POST["input"]["typeid"] : $gegevens["stap1"]["typeid"]))."';");
#					echo $db->lastquery;
					while($db->next_record()) {
						if($inquery) $inquery.=",".$db->f("type_id"); else $inquery=$db->f("type_id");
						$verzameltypes_gekoppeld[$db->f("type_id")]=$db->f("begincode").$db->f("type_id").($db->f("tnaam") ? " - ".$db->f("tnaam") : "");

						# Leverancier aanpassen
						$voorraad_afboeken_onchange_first.="if(this.value=='voorraad_garantie_".$db->f("type_id")."'||this.value=='voorraad_allotment_".$db->f("type_id")."'||this.value=='voorraad_vervallen_allotment_".$db->f("type_id")."'||this.value=='voorraad_optie_leverancier_".$db->f("type_id")."'||this.value=='voorraad_request_".$db->f("type_id")."') { document.frm.elements['input[leverancierid]'].value='".$db->f("leverancier_id")."'; } ";

						# Beheerder en eigenaar aanpassen
						$voorraad_afboeken_onchange_first.="if(this.value.indexOf('oorraad_garantie_".$db->f("type_id")."')>0||this.value=='voorraad_garantie_".$db->f("type_id")."'||this.value=='voorraad_allotment_".$db->f("type_id")."'||this.value=='voorraad_vervallen_allotment_".$db->f("type_id")."'||this.value=='voorraad_optie_leverancier_".$db->f("type_id")."'||this.value=='voorraad_request_".$db->f("type_id")."') { document.frm.elements['input[beheerderid]'].value='".$db->f("beheerder_id")."';document.frm.elements['input[eigenaarid]'].value='".$db->f("eigenaar_id")."'; } ";

						# Reserveringsnummer 2e deel berekenen
						if($reserveringsnummer_1) {
							$voorraad_afboeken_onchange_last.="if(this.value=='voorraad_garantie_".$db->f("type_id")."'||this.value=='voorraad_allotment_".$db->f("type_id")."'||this.value=='voorraad_vervallen_allotment_".$db->f("type_id")."'||this.value=='voorraad_optie_leverancier_".$db->f("type_id")."'||this.value=='voorraad_request_".$db->f("type_id")."') { auto_resnr_bij_boeken(".$gegevens["stap1"]["aankomstdatum_exact"].");} ";
						}
					}
					if($inquery) {
						$db->query("SELECT type_id, voorraad_garantie, voorraad_allotment, voorraad_vervallen_allotment, voorraad_optie_leverancier, voorraad_xml, voorraad_request, voorraad_optie_klant, type_id, week, bruto, korting_percentage, toeslag, korting_euro, vroegboekkorting_percentage, vroegboekkorting_euro, c_bruto, c_korting_percentage, c_toeslag, c_korting_euro, c_vroegboekkorting_percentage, c_vroegboekkorting_euro FROM tarief WHERE type_id IN (".$inquery.") AND week='".addslashes(($_POST["input"]["aankomstdatum"] ? $_POST["input"]["aankomstdatum"] : $gegevens["stap1"]["aankomstdatum"]))."';");
						while($db->next_record()) {

							# Netto-prijs berekenen
							$week=($_POST["input"]["aankomstdatum"] ? $_POST["input"]["aankomstdatum"] : $gegevens["stap1"]["aankomstdatum"]);

							unset($seizoen);
							$seizoen["weken"][$week]["bruto"]=$db->f("bruto");
							$seizoen["weken"][$week]["korting_percentage"]=$db->f("korting_percentage");
							$seizoen["weken"][$week]["toeslag"]=$db->f("toeslag");
							$seizoen["weken"][$week]["korting_euro"]=$db->f("korting_euro");
							$seizoen["weken"][$week]["vroegboekkorting_percentage"]=$db->f("vroegboekkorting_percentage");
							$seizoen["weken"][$week]["vroegboekkorting_euro"]=$db->f("vroegboekkorting_euro");
							$seizoen["weken"][$week]["c_bruto"]=$db->f("c_bruto");
							$seizoen["weken"][$week]["c_korting_percentage"]=$db->f("c_korting_percentage");
							$seizoen["weken"][$week]["c_toeslag"]=$db->f("c_toeslag");
							$seizoen["weken"][$week]["c_korting_euro"]=$db->f("c_korting_euro");
							$seizoen["weken"][$week]["c_vroegboekkorting_percentage"]=$db->f("c_vroegboekkorting_percentage");
							$seizoen["weken"][$week]["c_vroegboekkorting_euro"]=$db->f("c_vroegboekkorting_euro");

							$prijs=bereken($accinfo["toonper"],$seizoen,$week,$accinfo,array(""));
							if($accinfo["toonper"]==1) {
								$netto=": € ".number_format($prijs["netto"],2,",",".");
							} else {
								$netto=": € ".number_format($prijs["c_netto"],2,",",".");
							}

							# Garanties uit db halen
							$db2->query("SELECT garantie_id, reserveringsnummer_extern, naam, netto, leverancier_id FROM garantie WHERE type_id='".addslashes($db->f("type_id"))."' AND aankomstdatum='".addslashes($db->f("week"))."' AND boeking_id=0 AND aankomstdatum>'".(time()-86400)."' ORDER BY inkoopdatum;");
							if($db2->num_rows()) {
								while($db2->next_record()) {
									$voorraad_afboeken_keuzes["voorraad_garantie_".$db->f("type_id")."_".$db2->f("garantie_id")]=$verzameltypes_gekoppeld[$db->f("type_id")]." - garantie: ".($db2->f("reserveringsnummer_extern") ? $db2->f("reserveringsnummer_extern") : "onbekend volgnummer")." - ".$db2->f("naam").": € ".number_format($db2->f("netto"),2,",",".");
									$voorraad_afboeken_onchange_temp="if(document.frm.elements['input[voorraad_afboeken]'].value=='voorraad_garantie_".$db->f("type_id")."_".$db2->f("garantie_id")."') { document.frm.elements['input[reserveringsnummer_2]'].value='".$db2->f("reserveringsnummer_extern")."'; }";
									if($voorraad_afboeken_onchange) {
										$voorraad_afboeken_onchange.=" else ".$voorraad_afboeken_onchange_temp;
									} else {
										$voorraad_afboeken_onchange=$voorraad_afboeken_onchange_temp;
									}
									$voorraad_afboeken_onchange_first.="if(this.value=='voorraad_garantie_".$db->f("type_id")."_".$db2->f("garantie_id")."') { document.frm.elements['input[leverancierid]'].value='".$db2->f("leverancier_id")."'; } ";
								}
#								$voorraad_afboeken_onchange.=" else document.frm.elements['input[reserveringsnummer_2]'].value='".$reserveringsnummer_2."';";
							} elseif($db->f("voorraad_garantie")>0) {
								$voorraad_afboeken_keuzes["voorraad_garantie_".$db->f("type_id")]=$verzameltypes_gekoppeld[$db->f("type_id")]." - in garantie (".$db->f("voorraad_garantie")." beschikbaar) ".$netto;
							}
							if($db->f("voorraad_allotment")>0) {
								$voorraad_afboeken_keuzes["voorraad_allotment_".$db->f("type_id")]=$verzameltypes_gekoppeld[$db->f("type_id")]." - in allotment (".$db->f("voorraad_allotment")." beschikbaar) ".$netto;
							}
							if($db->f("voorraad_vervallen_allotment")>0) {
								$voorraad_afboeken_keuzes["voorraad_vervallen_allotment_".$db->f("type_id")]=$verzameltypes_gekoppeld[$db->f("type_id")]." - vervallen allotment (".$db->f("voorraad_vervallen_allotment")." beschikbaar) ".$netto;
							}
							if($db->f("voorraad_optie_leverancier")>0) {
								$voorraad_afboeken_keuzes["voorraad_optie_leverancier_".$db->f("type_id")]=$verzameltypes_gekoppeld[$db->f("type_id")]." - in optie bij leverancier (".$db->f("voorraad_optie_leverancier")." beschikbaar) ".$netto;
							}
							if($db->f("voorraad_request")>0) {
								$voorraad_afboeken_keuzes["voorraad_request_".$db->f("type_id")]=$verzameltypes_gekoppeld[$db->f("type_id")]." - op request beschikbaar (".$db->f("voorraad_request")." beschikbaar) ".$netto;
							}
						}
					}

					if($gegevens["stap1"]["flexibel"]) {
						# bij flexibel: voorraad afboeken niet van toepassing
						unset($voorraad_afboeken_keuzes);
						$voorraad_afboeken_keuzes["niet_bijwerken"]="voorraad niet bijwerken";
					}
					$form->field_select(0,"voorraad_afboeken","Voorraad afboeken*","","",array("selection"=>$voorraad_afboeken_keuzes),array("tr_class"=>"tr_reserveringsnummer","onchange"=>$voorraad_afboeken_onchange_first.$voorraad_afboeken_onchange.$voorraad_afboeken_onchange_last));
				} else {
					#
					# Gewoon type (geen verzameltype)
					#
					$db->query("SELECT voorraad_garantie, voorraad_allotment, voorraad_vervallen_allotment, voorraad_optie_leverancier, voorraad_xml, voorraad_request, voorraad_optie_klant, type_id, week FROM tarief WHERE type_id='".addslashes(($_POST["input"]["typeid"] ? $_POST["input"]["typeid"] : $gegevens["stap1"]["typeid"]))."' AND week='".addslashes(($_POST["input"]["aankomstdatum"] ? $_POST["input"]["aankomstdatum"] : $gegevens["stap1"]["aankomstdatum"]))."';");
					if($db->next_record()) {
						$voorraad_afboeken_keuzes["niet_bijwerken"]="voorraad niet bijwerken";

						# Garanties uit db halen
						$db2->query("SELECT garantie_id, reserveringsnummer_extern, naam FROM garantie WHERE type_id='".addslashes($db->f("type_id"))."' AND aankomstdatum='".addslashes($db->f("week"))."' AND boeking_id=0 AND aankomstdatum>'".(time()-86400)."' ORDER BY inkoopdatum;");
						if($db2->num_rows()) {
							while($db2->next_record()) {
								$voorraad_afboeken_keuzes["voorraad_garantie_".$db2->f("garantie_id")]="garantie: ".($db2->f("reserveringsnummer_extern") ? $db2->f("reserveringsnummer_extern") : "onbekend volgnummer")." - ".$db2->f("naam");
								$voorraad_afboeken_onchange_temp="if(document.frm.elements['input[voorraad_afboeken]'].value=='voorraad_garantie_".$db2->f("garantie_id")."') { document.frm.elements['input[reserveringsnummer_2]'].value='".$db2->f("reserveringsnummer_extern")."'; }";
								if($voorraad_afboeken_onchange) {
									$voorraad_afboeken_onchange.=" else ".$voorraad_afboeken_onchange_temp;
								} else {
									$voorraad_afboeken_onchange=$voorraad_afboeken_onchange_temp;
								}
							}
#							$voorraad_afboeken_onchange.=" else document.frm.elements['input[reserveringsnummer_2]'].value='".$reserveringsnummer_2."';";
						} elseif($db->f("voorraad_garantie")>0) {
							$voorraad_afboeken_keuzes["voorraad_garantie"]="In garantie (".$db->f("voorraad_garantie")." beschikbaar)";
						}
						if($db->f("voorraad_allotment")>0) {
							$voorraad_afboeken_keuzes["voorraad_allotment"]="In allotment (".$db->f("voorraad_allotment")." beschikbaar)";
						}
						if($db->f("voorraad_vervallen_allotment")>0) {
							$voorraad_afboeken_keuzes["voorraad_vervallen_allotment"]="Vervallen allotment (".$db->f("voorraad_vervallen_allotment")." beschikbaar)";
						}
						if($db->f("voorraad_optie_leverancier")>0) {
							$voorraad_afboeken_keuzes["voorraad_optie_leverancier"]="In optie bij leverancier (".$db->f("voorraad_optie_leverancier")." beschikbaar)";
						}
						if($db->f("voorraad_request")>0) {
							$voorraad_afboeken_keuzes["voorraad_request"]="Op request beschikbaar (".$db->f("voorraad_request")." beschikbaar)";
						}

						if($gegevens["stap1"]["flexibel"]) {
							# bij flexibel: voorraad afboeken niet van toepassing
							unset($voorraad_afboeken_keuzes);
							$voorraad_afboeken_keuzes["niet_bijwerken"]="voorraad niet bijwerken";
						}

						if($accinfo["voorraad_gekoppeld_type_id"]) {
							# bij voorraad_gekoppeld_type_id: voorraad afboeken niet van toepassing
							unset($voorraad_afboeken_keuzes);
							$voorraad_afboeken_keuzes["niet_bijwerken"]="voorraad niet bijwerken (".$accinfo["begincode"].$accinfo["type_id"]." heeft ".$accinfo["begincode"].$accinfo["voorraad_gekoppeld_type_id"]." als voorraad-houder)";
						}

						$form->field_select(0,"voorraad_afboeken","Voorraad afboeken*","","",array("selection"=>$voorraad_afboeken_keuzes),array("tr_class"=>"tr_reserveringsnummer","onchange"=>$voorraad_afboeken_onchange));

						if($db->f("voorraad_optie_klant")>0) {
							$form->field_yesno("voorraad_optie_klant","Optie aan klant wegstrepen","","","",array("tr_class"=>"tr_reserveringsnummer"));
						}
					}
				}
			}
			$form->field_select(1,"leverancierid","Leverancier","",array("selection"=>$gegevens["stap1"]["leverancierid"]),array("selection"=>$alle_leveranciers));
			$form->field_select(0,"beheerderid","Beheerder","",array("selection"=>$gegevens["stap1"]["beheerderid"]),array("selection"=>$alle_beheerders));
			$form->field_select(0,"eigenaarid","Eigenaar","",array("selection"=>$gegevens["stap1"]["eigenaarid"]),array("selection"=>$alle_leveranciers));

			if($gegevens["stap1"]["bestelstatus"]==2 or $gegevens["stap1"]["bestelstatus"]==3) {
				$form->field_yesno("bestelstatus_wissen","Bestelstatus en besteldatum wissen","","","",array("tr_style"=>($_POST["input"]["bestelstatus_wissen"] ? "" : "display:none;")."background-color:#d5e1f9;","tr_id"=>"bestelstatus_wissen_tr"));
			}

			$form->field_select(1,"typeid","Accommodatie","",array("selection"=>$gegevens["stap1"]["typeid"]),array("selection"=>$vars["alle_types"]));

			if($gegevens["stap1"]["verzameltype"]) {
				if($gegevens["stap1"]["goedgekeurd"]) {
					$db->query("SELECT type_id, begincode, naam, tnaam, optimaalaantalpersonen, maxaantalpersonen FROM view_accommodatie WHERE verzameltype_parent='".addslashes($gegevens["stap1"]["typeid"])."' OR type_id='".addslashes($gegevens["stap1"]["verzameltype_gekozentype_id"])."' ORDER BY naam, tnaam, optimaalaantalpersonen, maxaantalpersonen, type_id;");
					while($db->next_record()) {
						$verzameltype_gekozentype_keuzes[$db->f("type_id")]=$db->f("begincode").$db->f("type_id")." ".$db->f("naam")." ".($db->f("tnaam") ? $db->f("tnaam")." " : "")."(".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p)";
					}
					$form->field_select(0,"verzameltype_gekozentype_id","Gekozen onderliggend type","",array("selection"=>$gegevens["stap1"]["verzameltype_gekozentype_id"]),array("selection"=>$verzameltype_gekozentype_keuzes));
				} else {
					$form->field_htmlcol("","Gekozen onderliggend type",array("text"=>"selecteer via \"Voorraad afboeken\""));
				}
			}

			$form->field_select(1,"aantalpersonen","Aantal personen","",array("selection"=>$gegevens["stap1"]["aantalpersonen"]),array("selection"=>$vars["alle_aantalpersonen_array"]));
			$form->field_select(1,"aankomstdatum","Aankomstdatum (tarieven)","",array("selection"=>$gegevens["stap1"]["aankomstdatum"]),array("selection"=>$vars["alle_aankomstdatum_weekend"]),array("tr_class"=>"tr_aankomstdatum","onchange"=>"toggle_display('tr_datum_exact',0);"));

			$form->field_date(1,"aankomstdatum_exact","Exacte aankomstdatum","",array("time"=>$gegevens["stap1"]["aankomstdatum_exact"]),"",array("calendar"=>true,"tr_class"=>"tr_datum_exact","onchange"=>"toggle_display('tr_aankomstdatum',0);"));
			$form->field_date(1,"vertrekdatum_exact","Exacte vertrekdatum","",array("time"=>$gegevens["stap1"]["vertrekdatum_exact"]),"",array("calendar"=>true,"tr_class"=>"tr_datum_exact","onchange"=>"toggle_display('tr_aankomstdatum',0);"));

			if($gegevens["stap1"]["voorraad_afboeken"]) {
				$form->field_select(1,"voorraad_afboeken_change","Bij bevestigen is voorraad afgeboekt van","",array("selection"=>$gegevens["stap1"]["voorraad_afboeken"]),array("selection"=>$vars["voorraad_afboeken"]));
			}

			$form->field_textarea(0,"opmerkingen_intern","Opmerkingen (intern)","",array("text"=>$gegevens["stap1"]["opmerkingen_intern"]),array("onfocus"=>"naamdatum_toevoegen(this,'".date("d/m/Y")." (".$login->vars["voornaam"]."):')"));
			$form->field_text(0,"opmerkingen_vertreklijst","Opmerkingen (vertreklijst)","",array("text"=>$gegevens["stap1"]["opmerkingen_vertreklijst"]));
			if(!$gegevens["stap1"]["geannuleerd"]) {
				$form->field_yesno("geannuleerd","deze boeking moet worden geannuleerd (boekhouding wordt <b>niet</b> gecrediteerd)","",array("selection"=>$gegevens["stap1"]["geannuleerd"]),"",array("title_html"=>true));
			}
			$form->field_yesno("tonen_in_mijn_boeking","deze boeking is voor de klant zichtbaar in \"Mijn boeking\"","",array("selection"=>$gegevens["stap1"]["tonen_in_mijn_boeking"]),"",array("title_html"=>true));
			if(!$gegevens["stap1"]["boekingsnummer"]) {
				$form->field_yesno("vervallen_aanvraag","dit is een vervallen aanvraag","",array("selection"=>$gegevens["stap1"]["vervallen_aanvraag"]));
			}

			$form->field_htmlrow("","<hr><b>Gegevens m.b.t. boekhouding</b>");
			if($gegevens["stap1"]["landcode"]) {
				$landcode=$gegevens["stap1"]["landcode"];
			} else {
				if($gegevens["stap1"]["reisbureau_user_id"]) {
					$form->field_htmlcol("","Land reisbureau",array("html"=>htmlentities($gegevens["stap1"]["reisbureau_land"])));
					$temp_land=$gegevens["stap1"]["reisbureau_land"];
				} else {
					$form->field_htmlcol("","Door hoofdboeker ingevuld land",array("html"=>htmlentities($gegevens["stap2"]["land"])));
					$temp_land=$gegevens["stap2"]["land"];
				}

				if(strtolower($temp_land)=="nederland") {
					$landcode=5;
				} elseif(strtolower($temp_land)=="belgië") {
					$landcode=1;
				} elseif(strtolower($temp_land)=="belgie") {
					$landcode=1;
				} elseif(strtolower($temp_land)=="belgiË") {
					$landcode=1;
				} elseif(strtolower($temp_land)=="belgium") {
					$landcode=1;
				} elseif(strtolower($temp_land)=="belgique") {
					$landcode=1;
				} elseif(strtolower($temp_land)=="duitsland") {
					$landcode=2;
				} elseif(strtolower($temp_land)=="deutschland") {
					$landcode=2;
				} elseif(strtolower($temp_land)=="luxemburg") {
					$landcode=4;
				} elseif(strtolower($temp_land)=="luxembourg") {
					$landcode=4;
				} elseif(strtolower($temp_land)=="la france") {
					$landcode=3;
				} elseif(strtolower($temp_land)=="frankrijk") {
					$landcode=3;
				} elseif(strtolower($temp_land)=="united kingdom") {
					$landcode=8;
				} elseif(strtolower($temp_land)=="switzerland") {
					$landcode=9;
				} else {
					$landcode=0;
				}
			}
			$form->field_select(1,"landcode","Koppelen aan landcode","",array("selection"=>$landcode),array("selection"=>$vars["landcodes_boekhouding_lang"]));

			if($gegevens["stap1"]["reisbureau_user_id"]) {
#				$temp_reisbureau=$gegevens["stap1"]["reisbureau_naam"]."\n".$gegevens["stap1"]["reisbureau_adres"]."\n".$gegevens["stap1"]["reisbureau_postcode"]." ".$gegevens["stap1"]["reisbureau_plaats"]."\n".$gegevens["stap1"]["reisbureau_land"];
#				$form->field_htmlcol("","Factuurgegevens reisbureau",array("html"=>nl2br(htmlentities($temp_reisbureau))));

				# Reisbureau wijzigen
				$db->query("SELECT r.naam, ru.voornaam, ru.tussenvoegsel, ru.achternaam, ru.user_id FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id ORDER BY r.naam, ru.voornaam, ru.achternaam;");
				while($db->next_record()) {
					$temp_reisbureauuser[$db->f("user_id")]=$db->f("naam")." - ".wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
				}
				$form->field_select(1,"reisbureauuserid","Reisbureau/gebruiker","",array("selection"=>$gegevens["stap1"]["reisbureau_user_id"]),array("selection"=>$temp_reisbureauuser));
				$form->field_yesno("btw_over_commissie","BTW over commissie rekenen","",array("selection"=>$gegevens["stap1"]["btw_over_commissie"]));

			} else {
				$naw_hoofdboeker=wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"])." (".($gegevens["stap2"]["geboortedatum"]=="" ? "geb.datum onbekend" : wt_adodb_date("d-m-Y",$gegevens["stap2"]["geboortedatum"])).")\n".$gegevens["stap2"]["adres"]."\n".$gegevens["stap2"]["postcode"]." ".$gegevens["stap2"]["plaats"]."\n".$gegevens["stap2"]["land"]."\nTel: ".$gegevens["stap2"]["telefoonnummer"]."\nE-mail: ".$gegevens["stap2"]["email"];
				$form->field_htmlcol("","NAW-gegevens hoofdboeker",array("html"=>nl2br(htmlentities($naw_hoofdboeker))));

				# Debiteur bepalen
				$zelfde_debiteur=0;
				$db->query("SELECT b.debiteurnummer, p.voornaam, p.tussenvoegsel, p.achternaam, p.email, p.geboortedatum FROM boeking b, boeking_persoon p WHERE p.persoonnummer=1 AND p.boeking_id=b.boeking_id AND b.debiteurnummer>0 ORDER BY b.boeking_id;");
				while($db->next_record()) {
					if($gegevens["stap1"]["boekingsnummer"]) {
						if($db->f("debiteurnummer")==$gegevens["stap1"]["debiteurnummer"]) {
							$zelfde_debiteur=$db->f("debiteurnummer");
						}
					} else {
						if($db->f("email") and $db->f("email")==$gegevens["stap2"]["email"]) {
							$zelfde_debiteur=$db->f("debiteurnummer");
						}
					}
					$naw_alleboekingen[$db->f("debiteurnummer")]=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"),true)." (".($db->f("geboortedatum")=="" ? "geb.datum onbekend" : wt_adodb_date("d-m-Y",$db->f("geboortedatum"))).")   ";
				}
				if($gegevens["stap1"]["debiteurnummer"]) $zelfde_debiteur=$gegevens["stap1"]["debiteurnummer"];
				asort($naw_alleboekingen);
				if($gegevens["stap1"]["boekingsnummer"]) {
					$tekst_debiteur="Debiteur";
				} else {
					$tekst_debiteur="Zelfde debiteur als";
				}

				$form->field_select(0,"debiteurnummer",$tekst_debiteur,"",array("selection"=>$zelfde_debiteur),array("selection"=>$naw_alleboekingen));

				if($gegevens["stap1"]["wederverkoop"]) {
					# Reisbureau koppelen
					$form->field_htmlrow("","<b>&nbsp;&nbsp;of</b>");

					$db->query("SELECT r.naam, ru.voornaam, ru.tussenvoegsel, ru.achternaam, ru.user_id FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id ORDER BY r.naam, ru.voornaam, ru.achternaam;");
					while($db->next_record()) {
#						$temp_reisbureauuser[$db->f("user_id")]=$db->f("naam")." - ".$db->f("runaam");
						$temp_reisbureauuser[$db->f("user_id")]=$db->f("naam")." - ".wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));

					}
					$form->field_select(0,"reisbureauuserid","Reisbureau/gebruiker","",array("selection"=>$gegevens["stap1"]["reisbureau_user_id"]),array("selection"=>$temp_reisbureauuser));
				}
			}
		} else {
			if($boeking_wijzigen) {
				$form->field_htmlrow("","<b>".htmlentities(ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"])."</b>");
			} else {
				if($mustlogin) {
					$form->field_noedit("accnaam",txt("accommodatie","boeken"),"",array("text"=>"<a href=\"".$accinfo["url"]."\" target=\"_blank\">".htmlentities($accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"])),"",array("html"=>true));
				} else {
					$form->field_noedit("accnaam",txt("accommodatie","boeken"),"",array("text"=>$accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"]));
				}
				$form->field_noedit("accplaats",txt("plaats","boeken"),"",array("text"=>$accinfo["plaats"].", ".$accinfo["land"]));
			}
			if(!$boeking_wijzigen or $gegevens["stap1"]["wijzigen_toegestaan"]) {

				if($boeking_wijzigen and $gegevens["stap1"]["verkoop_gewijzigd"]) {

				} else {
					unset($temp_min_aantalpersonen,$temp_max_aantalpersonen);
					if($accinfo["toonper"]<>3 and !$vars["wederverkoop"]) {
						# Minimum aantal te boeken personen bepalen (op basis van opgeslagen tarieven in tabel tarief_persoon)
						unset($inquery_aankomstdata);
						@reset($accinfo["aankomstdatum_beschikbaar"]);
						while(list($key,$value)=@each($accinfo["aankomstdatum_beschikbaar"])) {
							if($key>time() or $voorkant_cms) {
								if($inquery_aankomstdata) $inquery_aankomstdata.=",".$key; else $inquery_aankomstdata=$key;
							}
						}
						if($inquery_aankomstdata) {
							$db->query("SELECT DISTINCT personen FROM tarief_personen WHERE type_id='".addslashes($accinfo["typeid"])."' AND week IN (".$inquery_aankomstdata.") ORDER BY personen;");
							while($db->next_record()) {
								if(!$temp_min_aantalpersonen) $temp_min_aantalpersonen=$db->f("personen");
								$temp_max_aantalpersonen=$db->f("personen");
							}
							if($temp_max_aantalpersonen>$accinfo["maxaantalpersonen"]) $temp_max_aantalpersonen=$accinfo["maxaantalpersonen"];
							for($i=$temp_min_aantalpersonen;$i<=$temp_max_aantalpersonen;$i++) {
								$temp_aantalpersonen_array[$i]=$i." ".($i==1 ? txt("persoon") : txt("personen"));
							}
						}
					}
					if(!is_array($temp_aantalpersonen_array)) {
						$temp_aantalpersonen_array=$accinfo["aantalpersonen_array"];
					}
					$form->field_select(1,"aantalpersonen",txt("aantalpersonen","boeken"),"",array("selection"=>$gegevens["stap1"]["aantalpersonen"]),array("selection"=>$temp_aantalpersonen_array));
				}
			}

			if(!$boeking_wijzigen) {
				# Aankomstdata-array bepalen (geen data uit het verleden)
				@reset($accinfo["aankomstdatum_beschikbaar"]);
				unset($temp_aankomstdata);
				while(list($key,$value)=@each($accinfo["aankomstdatum_beschikbaar"])) {
					if($key>time() or $voorkant_cms) {
						$temp_aankomstdata[$key]=$value;
					}
				}
#				if((($_GET["flad"] and $_GET["fldu"]) or $gegevens["stap1"]["flexibel"]) and $accinfo["flexibel"]) {
				if($accinfo["wzt"]==2) {
					# flexibel
					if(!$gegevens["stap1"]["aankomstdatum_exact"]) {
						if($_GET["flad"]) {
							$gegevens["stap1"]["aankomstdatum_exact"]=$_GET["flad"];
						} elseif($_GET["d"]) {
							$gegevens["stap1"]["aankomstdatum_exact"]=$_GET["d"];
						}
					}
					if($accinfo["flexibel"]) {
						$form->field_date(1,"aankomstdatum_flex",txt("aankomstdatum","boeken"),"",array("time"=>$gegevens["stap1"]["aankomstdatum_exact"]),array("startyear"=>date("Y"),"endyear"=>date("Y")+1),array("calendar"=>true));
					} else {
						$form->field_select(1,"aankomstdatum",txt("aankomstdatum","boeken"),"",array("selection"=>$gegevens["stap1"]["aankomstdatum"]),array("selection"=>$temp_aankomstdata));
					}

					# Verblijfsduur
					unset($vars["verblijfsduur"]);
					$vars["verblijfsduur"]["1"]="1 ".txt("week","vars");
					$vars["verblijfsduur"]["2"]="2 ".txt("weken","vars");
					$vars["verblijfsduur"]["3"]="3 ".txt("weken","vars");
					$vars["verblijfsduur"]["4"]="4 ".txt("weken","vars");
					if($accinfo["flexibel"]) {
						// $vars["verblijfsduur"]["1n"]="1 ".txt("nacht","vars");
						for($i=3;$i<=$vars["flex_max_aantalnachten"];$i++) {
							$vars["verblijfsduur"][$i."n"]=$i." ".txt("nachten","vars");
						}
					}
					if($gegevens["stap1"]["verblijfsduur"]) {
						$temp_verblijfsduur=$gegevens["stap1"]["verblijfsduur"];
					} elseif($_GET["fldu"]) {
						$temp_verblijfsduur=$_GET["fldu"];
					} elseif($_GET["fdu"]>0 and !$_GET["fldu"]) {
						$temp_verblijfsduur=$_GET["fdu"];
					} else {
						$temp_verblijfsduur=1;
					}
					$form->field_select(1,"verblijfsduur",txt("verblijfsduur","boeken"),"",array("selection"=>$temp_verblijfsduur),array("selection"=>$vars["verblijfsduur"],"optgroup"=>array("1"=>txt("aantalweken"),"3n"=>txt("aantalnachten"))));
				} else {
					$form->field_select(1,"aankomstdatum",txt("aankomstdatum","boeken"),"",array("selection"=>$gegevens["stap1"]["aankomstdatum"]),array("selection"=>$temp_aankomstdata));
				}

				# Kortingscode
				if(!$gegevens["stap_voltooid"][5] and $_SESSION["boeking"]["kortingscode_foutief"]<5) {
					$form->field_text(0,"kortingscode",html("kortingscodeoptioneel","boeken",array("h_1"=>"<span style=\"font-size:0.8em;\">","h_2"=>"</span>")),"",array("text"=>($gegevens["stap_voltooid"][1] ? $_SESSION["boeking"]["kortingscode"] : "")),"",array("title_html"=>true));
				}

				if($voorkant_cms and $vars["wederverkoop"]) {

					# Reisbureau wijzigen
					unset($temp_reisbureauuser);
					$db->query("SELECT r.naam, ru.voornaam, ru.tussenvoegsel, ru.achternaam, ru.user_id FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id ORDER BY r.naam, ru.voornaam, ru.achternaam;");
					while($db->next_record()) {
#						$temp_reisbureauuser[$db->f("user_id")]=$db->f("naam")." - ".$db->f("runaam");
						$temp_reisbureauuser[$db->f("user_id")]=$db->f("naam")." - ".wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
					}
					$form->field_htmlrow("","<span class=\"intern\">Voor Chalet.nl-medewerkers is het mogelijk deze boeking op naam van een reisbureau te zetten:</span>");
					$form->field_select(0,"reisbureauuserid","Reisbureau/gebruiker","",array("selection"=>$gegevens["stap1"]["reisbureau_user_id"]),array("selection"=>$temp_reisbureauuser));
				}

			}
		}

		#
		# Kijken of er opties zijn ingevoerd die niet beschikbaar zijn bij aangepaste acc/aangepaste aankomstdatum. Zo ja: foutmelding
		#
		if($gegevens["stap_voltooid"][1] and $_POST["frm_filled"] and $_POST["input"]["aankomstdatum"]) {
			# Nieuwe opties laden
			if($_POST["input"]["typeid"]) {
				$nieuw_typeid=$_POST["input"]["typeid"];
			} else {
				$nieuw_typeid=$accinfo["typeid"];
			}
			unset($temp_inquery,$inquery);
			# Nieuwe opties in inquery zetten
			$db->query("SELECT DISTINCT oo.optie_onderdeel_id, bo.status FROM optie_onderdeel oo, optie_groep og, optie_soort os, boeking_optie bo, optie_accommodatie oa, optie_tarief ot, type t WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND ot.seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND ot.week='".addslashes($_POST["input"]["aankomstdatum"])."' AND ot.beschikbaar=1 AND oa.optie_groep_id=og.optie_groep_id AND og.optie_soort_id=os.optie_soort_id AND bo.boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oa.optie_soort_id=os.optie_soort_id AND oo.optie_groep_id=og.optie_groep_id AND oa.optie_groep_id=og.optie_groep_id AND oa.accommodatie_id=t.accommodatie_id AND t.type_id='".addslashes($nieuw_typeid)."';");
			while($db->next_record()) {
				if($temp_inquery[$db->f("status")]) $temp_inquery[$db->f("status")].=",".$db->f("optie_onderdeel_id"); else $temp_inquery[$db->f("status")]=$db->f("optie_onderdeel_id");
			}

			# Controle op status Geselecteerde opties (2 heeft voorkeur boven 1)
			if($temp_inquery[2]) {
				$inquery=$temp_inquery[2];
			} elseif($temp_inquery[1]) {
				$inquery=$temp_inquery[1];
			}
			if(!$inquery) $inquery=0;

			# Oude opties laden (uitgezonderd de nieuwe opties)
			$db->query("SELECT DISTINCT oo.optie_onderdeel_id, os.naam_enkelvoud".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam_enkelvoud, oo.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam, bo.status FROM optie_onderdeel oo, optie_groep og, optie_soort os, boeking_optie bo, optie_accommodatie oa, optie_tarief ot, type t WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND ot.seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND ot.week='".addslashes($gegevens["stap1"]["aankomstdatum"])."' AND ot.beschikbaar=1 AND oa.optie_groep_id=og.optie_groep_id AND og.optie_soort_id=os.optie_soort_id AND bo.boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oa.optie_soort_id=os.optie_soort_id AND oo.optie_groep_id=og.optie_groep_id AND oa.optie_groep_id=og.optie_groep_id AND oa.accommodatie_id=t.accommodatie_id AND t.type_id='".addslashes($gegevens["stap1"]["typeid"])."' AND oo.optie_onderdeel_id NOT IN (".$inquery.") ORDER BY os.volgorde, oo.naam;");
			unset($temp_opties_nietbeschikbaar,$opties_nietbeschikbaar);
			while($db->next_record()) {
				$temp_opties_nietbeschikbaar[$db->f("status")].="<li>".htmlentities($db->f("naam_enkelvoud").": ".$db->f("naam"))."</li>";
				if($temp_opties_nietbeschikbaar_id[$db->f("status")]) $temp_opties_nietbeschikbaar_id[$db->f("status")].=",".$db->f("optie_onderdeel_id"); else $temp_opties_nietbeschikbaar_id[$db->f("status")]=$db->f("optie_onderdeel_id");
				$temp_opties_nietbeschikbaar_idkey[$db->f("status")][$db->f("optie_onderdeel_id")]=true;
			}

			# Kijken naar opties met min_deelnemers
			if($_POST["input"]["aantalpersonen"] and $_POST["input"]["aantalpersonen"]<$gegevens["stap1"]["aantalpersonen"]) {
				$db->query("SELECT DISTINCT oo.optie_onderdeel_id, os.naam_enkelvoud".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam_enkelvoud, bo.status, oo.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam, oo.min_deelnemers FROM optie_onderdeel oo, boeking_optie bo, optie_soort os, optie_groep og WHERE og.optie_soort_id=os.optie_soort_id AND oo.optie_groep_id=og.optie_groep_id AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.min_deelnemers>'".addslashes($_POST["input"]["aantalpersonen"])."' AND bo.boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				while($db->next_record()) {
					if(!$temp_opties_nietbeschikbaar_idkey[$db->f("status")][$db->f("optie_onderdeel_id")]) {
						$temp_opties_nietbeschikbaar[$db->f("status")].="<li>".htmlentities($db->f("naam_enkelvoud").": ".$db->f("naam"))."</li>";
						if($temp_opties_nietbeschikbaar_id[$db->f("status")]) $temp_opties_nietbeschikbaar_id[$db->f("status")].=",".$db->f("optie_onderdeel_id"); else $temp_opties_nietbeschikbaar_id[$db->f("status")]=$db->f("optie_onderdeel_id");
					}
				}
			}
			# Controle op status Geselecteerde opties (2 heeft voorkeur boven 1)
			if($temp_opties_nietbeschikbaar[2]) {
				$opties_nietbeschikbaar=$temp_opties_nietbeschikbaar[2];
				$opties_nietbeschikbaar_id=$temp_opties_nietbeschikbaar_id[2];
			} elseif($temp_inquery[1]) {
				$opties_nietbeschikbaar=$temp_opties_nietbeschikbaar[1];
				$opties_nietbeschikbaar_id=$temp_opties_nietbeschikbaar_id[1];
			}

			if($opties_nietbeschikbaar) $form->field_yesno("wisopties_nietbeschikbaar",txt("wisopties_nietbeschikbaar","boeken"));
		}
	} elseif($_GET["stap"]==2) {
		if(!$mustlogin and !$boeking_wijzigen) {
			if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
				$form->field_htmlrow("","ALLEEN VOOR WEBTASTIC: <a href=\"javascript:testgegevens();\">Testgegevens invullen</a>");
			}
			$form->field_noedit("accnaam",txt("accommodatie","boeken"),"",array("text"=>$accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"]));
		} else {
			$form->field_htmlrow("","<b>".html("gegevenshoofdboeker","boeken")."</b>");
		}
		if(is_array($gegevens["stap4"][1]["optiesoort_perpersoon"]) and ($mustlogin or $boeking_wijzigen)) {
			while(list($key4,$value4)=each($gegevens["stap4"][1]["optiesoort_perpersoon"])) {
				$form->field_htmlrow("","<i>".htmlentities(($value4 ? $value4.": " : "").$gegevens["stap4"][1]["optieonderdeel_perpersoon"][$key4])."</i>");
			}
		}

		$form->field_text(1,"voornaam",txt("voornaam","boeken"),"",array("text"=>$gegevens["stap2"]["voornaam"]));
		if($boeking_wijzigen) {
			$form->field_noedit("tussenvoegsel",txt("tussenvoegsel","boeken"),"",array("text"=>$gegevens["stap2"]["tussenvoegsel"]));
			$form->field_noedit("achternaam",txt("achternaam","boeken"),"",array("text"=>$gegevens["stap2"]["achternaam"]));
			$form->field_htmlrow("","<span style=\"font-size:0.8em;font-style:italic;\">&nbsp;&nbsp;".html("voorhetwijzigenvanachternaam","boeken")."</span>");
		} else {
			$form->field_text(0,"tussenvoegsel",txt("tussenvoegsel","boeken"),"",array("text"=>$gegevens["stap2"]["tussenvoegsel"]));
			$form->field_text(1,"achternaam",txt("achternaam","boeken"),"",array("text"=>$gegevens["stap2"]["achternaam"]));
		}
		if(!$gegevens["stap1"]["reisbureau_user_id"]) {
			$form->field_text(1,"adres",txt("adres","boeken"),"",array("text"=>$gegevens["stap2"]["adres"]));
			$form->field_text(1,"postcode",txt("postcode","boeken"),"",array("text"=>$gegevens["stap2"]["postcode"]),array("maxlength"=>10));
		}
		$form->field_text(1,"plaats",txt("woonplaats","boeken"),"",array("text"=>$gegevens["stap2"]["plaats"]));

		if($vars["websiteland"]=="be") {
			$default_land="België";
		} elseif($vars["websiteland"]=="nl") {
			$default_land="Nederland";
		}
		$form->field_text(1,"land",txt("land","boeken"),"",array("text"=>($gegevens["stap2"]["land"] ? $gegevens["stap2"]["land"] : $default_land)));

		if($gegevens["stap1"]["reisbureau_user_id"]) {
			$form->field_text(0,"telefoonnummer",txt("telefoonnummer","boeken")."<br><span class=\"kleinfont\">(".txt("telefoonnummer_toelichtingwederverkoop","boeken").")</span>","",array("text"=>$gegevens["stap2"]["telefoonnummer"]),"",array("title_html"=>true));
#			$form->field_date(0,"geboortedatum",txt("geboortedatum","boeken")."<br><span class=\"kleinfont\">(".txt("geboortedatum_verplichtbij","boeken").")</span>","",array("time"=>$gegevens["stap2"]["geboortedatum"]),array("startyear"=>date("Y"),"endyear"=>1900),array("title_html"=>true));
			$form->field_date(($voorkant_cms ? 0 : 1),"geboortedatum",txt("geboortedatum","boeken"),"",array("time"=>$gegevens["stap2"]["geboortedatum"]),array("startyear"=>date("Y"),"endyear"=>1900),array("title_html"=>true));
		} else {
			$form->field_text(1,"telefoonnummer",txt("telefoonnummer","boeken"),"",array("text"=>$gegevens["stap2"]["telefoonnummer"]));
			$form->field_text(0,"mobielwerk",txt("mobielwerk","boeken"),"",array("text"=>$gegevens["stap2"]["mobielwerk"]));
			$form->field_email(1,"email",txt("email","boeken"),"",array("text"=>$gegevens["stap2"]["email"]));
			$form->field_date(($voorkant_cms ? 0 : 1),"geboortedatum",txt("geboortedatum","boeken"),"",array("time"=>$gegevens["stap2"]["geboortedatum"]),array("startyear"=>date("Y"),"endyear"=>1900));
		}
		if($gegevens["stap1"]["reisbureau_user_id"]) {
			$form->field_radio(1,"geslacht",txt("geslacht","boeken"),"",array("selection"=>$gegevens["stap2"]["geslacht"]),array("selection"=>$vars["geslacht"]));
		} else {
			$form->field_radio(1,"geslacht",txt("geslacht","boeken"),"",array("selection"=>$gegevens["stap2"]["geslacht"]),array("selection"=>$vars["geslacht"]));
		}

		if($boeking_wijzigen or $mustlogin) {
			# verzendmethode reisdocumenten doorgeven
			if($mustlogin) {
				$form->field_select(0,"verzendmethode_reisdocumenten",txt("verzendmethode_reisdocumenten","boeken"),"",array("selection"=>$gegevens["stap1"]["verzendmethode_reisdocumenten"]),array("selection"=>$vars["verzendmethode_reisdocumenten_inclusief_nvt"],"empty_is_0"=>true));
			} elseif($boeking_wijzigen) {
				$uiterlijke_datum=mktime(0,0,0,date("m",$gegevens["stap1"]["aankomstdatum_exact"]),date("d",$gegevens["stap1"]["aankomstdatum_exact"])-intval($gegevens["stap1"]["wijzigen_dagen"]),date("Y",$gegevens["stap1"]["aankomstdatum_exact"]));
				if(time()<$uiterlijke_datum) {
					if($gegevens["stap1"]["verzendmethode_reisdocumenten"]==3) {
						# verzendmethode staat op "n.v.t.": wijzigen niet mogelijk
					} else {
						# Wijzigen wel mogelijk (2 keuzes: post+mail)
						$form->field_select(1,"verzendmethode_reisdocumenten",txt("verzendmethode_reisdocumenten","boeken"),"",array("selection"=>$gegevens["stap1"]["verzendmethode_reisdocumenten"]),array("selection"=>$vars["verzendmethode_reisdocumenten"],"empty_is_0"=>true));
					}
				}
			}
		}

		if($mustlogin) {
			$form->field_htmlrow("","<hr><b>Inloggegevens</b>");

			$db0->query("SELECT user_id, password, password_uc FROM boekinguser WHERE user='".addslashes($gegevens["stap2"]["email"])."';");
			if($db0->next_record() and $db0->f("password_uc")) {

				$directlogin = new directlogin;
				$directlogin->boeking_id=$gegevens["stap1"]["boekingid"];
				$directlogin_link=$directlogin->maak_link($gegevens["stap1"]["website"],1,$db0->f("user_id"),md5($db0->f("password_uc")));

				$form->field_htmlcol("","Huidig wachtwoord",array("html"=>wt_he($db0->f("password_uc"))));
				if($vars["acceptatie_testserver"]) {
					$form->field_htmlcol("","URL directe inlog",array("html"=>"<a href=\"".wt_he($directlogin_link)."\" target=\"_blank\">".wt_he($directlogin_link)."</a>"));
				} else {
					$form->field_htmlcol("","URL directe inlog",array("html"=>wt_he($directlogin_link)));
				}
				if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {

					$directlogin_link=$directlogin->maak_link($gegevens["stap1"]["website"],1,$db0->f("user_id"),md5($db0->f("password_uc")));

					$directlogin_link=str_replace($vars["websiteinfo"]["basehref"][$gegevens["stap1"]["website"]],"http://".$_SERVER["HTTP_HOST"]."/chalet/",$directlogin_link);

					$directlogin_link_l="http://".$_SERVER["HTTP_HOST"]."/chalet/cms.php?gotourl=".urlencode($directlogin_link)."&testsite=".$gegevens["stap1"]["website"];

					$form->field_htmlcol("","URL directe inlog lokaal",array("html"=>"<a href=\"".wt_he($directlogin_link_l)."\" target=\"_blank\">".wt_he($directlogin_link)."</a>"));
				}
			}

			$form->field_password(0,"wachtwoord",txt("nieuwwachtwoord","boeken"));
			$form->field_password(0,"wachtwoord_herhaal",txt("herhaalnieuwwachtwoord","boeken"));


			$form->field_htmlrow("","<hr><b>T.b.v. roominglist</b>");
			$form->field_text(0,"aan_leverancier_doorgegeven_naam","Aan leverancier doorgegeven naam","",array("text"=>$gegevens["stap1"]["aan_leverancier_doorgegeven_naam"]));

		}

		# Kijken of een aangepaste geboortedatum overeenkomt met gekozen opties
		if($gegevens["stap_voltooid"][2] and $_POST["frm_filled"] and $_POST["input"]["geboortedatum"]["day"] and $_POST["input"]["geboortedatum"]["month"] and $_POST["input"]["geboortedatum"]["year"]) {
			$geboortedatum=adodb_mktime(0,0,0,$_POST["input"]["geboortedatum"]["month"],$_POST["input"]["geboortedatum"]["day"],$_POST["input"]["geboortedatum"]["year"]);
			if($geboortedatum<>$gegevens["stap2"]["geboortedatum"]) {
				$leeftijd=wt_leeftijd($geboortedatum,mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
				@reset($gegevens["stap4"]["optie_onderdeelid"][1]);
				while(list($key,$value)=@each($gegevens["stap4"]["optie_onderdeelid"][1])) {
					if(($gegevens["stap4"]["optie_onderdeelid_minleeftijd"][$key] and $leeftijd<$gegevens["stap4"]["optie_onderdeelid_minleeftijd"][$key]) or ($gegevens["stap4"]["optie_onderdeelid_maxleeftijd"][$key] and $leeftijd>$gegevens["stap4"]["optie_onderdeelid_maxleeftijd"][$key])) {
						$opties_nietbeschikbaar_geboortedatum.="<li>".htmlentities($gegevens["stap4"]["optie_onderdeelid_naam"][$key])."</li>";
						if($opties_nietbeschikbaar_geboortedatum_id) $opties_nietbeschikbaar_geboortedatum_id.=",".$key; else $opties_nietbeschikbaar_geboortedatum_id=$key;
					}
				}
			}
		}
		if($opties_nietbeschikbaar_geboortedatum) $form->field_yesno("wisopties_nietbeschikbaar",txt("wisnietbeschikbareopties","boeken"));

	} elseif($_GET["stap"]==3) {
		if($gegevens["stap1"]["aantalpersonen"]>1) {
			if(!$mustlogin and !$boeking_wijzigen) {
				$form->field_noedit("accnaam",txt("accommodatie","boeken"),"",array("text"=>htmlentities($accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"])),"",array("html"=>true));
				$form->field_htmlrow("","<b>".html("directdoor","boeken",array("l1"=>"javascript:document.frm.submit();"))."</b><hr>");
			}
			if($boeking_wijzigen or $mustlogin) {
				$vanafpersoon=1;
			} else {
				$vanafpersoon=2;
			}
			for($i=$vanafpersoon;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
				$form->field_htmlrow("","<a name=\"persoon".$i."\"></a><b>".txt("gegevenspersoon","boeken")." ".$i.($i==1 ? " (".html("hoofdboeker","boeken").")" : "")."</b>");
				if(is_array($gegevens["stap4"][$i]["optiesoort_perpersoon"]) and ($mustlogin or $boeking_wijzigen)) {
					while(list($key4,$value4)=each($gegevens["stap4"][$i]["optiesoort_perpersoon"])) {
						$form->field_htmlrow("","<i>".htmlentities(($value4 ? $value4.": " : "").$gegevens["stap4"][$i]["optieonderdeel_perpersoon"][$key4])."</i>");
					}
				}
				if($i==1) {
					$form->field_noedit("voornaam",txt("voornaam","boeken"),"",array("text"=>$gegevens["stap2"]["voornaam"]));
					$form->field_noedit("tussenvoegsel",txt("tussenvoegsel","boeken"),"",array("text"=>$gegevens["stap2"]["tussenvoegsel"]));
					$form->field_noedit("achternaam",txt("achternaam","boeken"),"",array("text"=>$gegevens["stap2"]["achternaam"]));
					$form->field_noedit("plaats",txt("woonplaats","boeken"),"",array("text"=>$gegevens["stap2"]["plaats"]));
					$form->field_noedit("land",txt("land","boeken"),"",array("text"=>$gegevens["stap2"]["land"]));
					$form->field_noedit("geboortedatum",txt("geboortedatum","boeken"),"",array("text"=>($gegevens["stap2"]["geboortedatum"] ? datum("D MAAND JJJJ",$gegevens["stap2"]["geboortedatum"]) : "-")));
					$form->field_noedit("geslacht",txt("geslacht","boeken"),"",array("text"=>$vars["geslacht"][$gegevens["stap2"]["geslacht"]]));
					$form->field_htmlrow("","<span style=\"font-style:italic;\"><a href=\"".htmlentities(str_replace("stap=3","stap=2",$_SERVER["REQUEST_URI"]))."\">".html("gegevenshoofdboekerwijzigen","boeken")." &raquo;</span>");
					$form->field_htmlrow("","<hr>");
				} else {
					$form->field_text(0,"voornaam".$i,txt("voornaam","boeken"),"",array("text"=>$gegevens["stap3"][$i]["voornaam"]));
					$form->field_text(0,"tussenvoegsel".$i,txt("tussenvoegsel","boeken"),"",array("text"=>$gegevens["stap3"][$i]["tussenvoegsel"]));
					$form->field_text(0,"achternaam".$i,txt("achternaam","boeken"),"",array("text"=>$gegevens["stap3"][$i]["achternaam"]));
					$form->field_text(0,"plaats".$i,txt("woonplaats","boeken"),"",array("text"=>$gegevens["stap3"][$i]["plaats"]));
					if(!$gegevens["stap3"][$i]["land"]) {
						$gegevens["stap3"][$i]["land"]=$gegevens["stap2"]["land"];
					}
					$form->field_text(0,"land".$i,txt("land","boeken"),"",array("text"=>$gegevens["stap3"][$i]["land"]));
					if($gegevens["stap1"]["reisbureau_user_id"]) {
						$form->field_date(0,"geboortedatum".$i,txt("geboortedatum","boeken")."<br><span class=\"kleinfont\">(".txt("geboortedatum_verplichtbij","boeken").")</span>","",array("time"=>$gegevens["stap3"][$i]["geboortedatum"]),array("startyear"=>date("Y"),"endyear"=>1900),array("title_html"=>true));
					} else {
						$form->field_date(0,"geboortedatum".$i,txt("geboortedatum","boeken"),"",array("time"=>$gegevens["stap3"][$i]["geboortedatum"]),array("startyear"=>date("Y"),"endyear"=>1900));
					}
					$form->field_radio(0,"geslacht".$i,txt("geslacht","boeken"),"",array("selection"=>$gegevens["stap3"][$i]["geslacht"]),array("selection"=>$vars["geslacht"]));
					if($i<$gegevens["stap1"]["aantalpersonen"]) $form->field_htmlrow("","<hr>");

					# Kijken of een aangepaste geboortedatum overeenkomt met gekozen opties
					if($gegevens["stap_voltooid"][3] and $_POST["frm_filled"] and $_POST["input"]["geboortedatum".$i]["day"] and $_POST["input"]["geboortedatum".$i]["month"] and $_POST["input"]["geboortedatum".$i]["year"]) {
						$geboortedatum=adodb_mktime(0,0,0,$_POST["input"]["geboortedatum".$i]["month"],$_POST["input"]["geboortedatum".$i]["day"],$_POST["input"]["geboortedatum".$i]["year"]);
						if($geboortedatum<>$gegevens["stap3"][$i]["geboortedatum"]) {
							$leeftijd=wt_leeftijd($geboortedatum,mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
							@reset($gegevens["stap4"]["optie_onderdeelid"][$i]);
							while(list($key,$value)=@each($gegevens["stap4"]["optie_onderdeelid"][$i])) {
								if(($gegevens["stap4"]["optie_onderdeelid_minleeftijd"][$key] and $leeftijd<$gegevens["stap4"]["optie_onderdeelid_minleeftijd"][$key]) or ($gegevens["stap4"]["optie_onderdeelid_maxleeftijd"][$key] and $leeftijd>$gegevens["stap4"]["optie_onderdeelid_maxleeftijd"][$key])) {
									$opties_nietbeschikbaar_geboortedatum.="<li>".txt("persoon","boeken")." ".$i." ".htmlentities($gegevens["stap4"]["optie_onderdeelid_naam"][$key])."</li>";
									if($opties_nietbeschikbaar_geboortedatum_id[$i]) $opties_nietbeschikbaar_geboortedatum_id[$i].=",".$key; else $opties_nietbeschikbaar_geboortedatum_id[$i]=$key;
								}
							}
						}
					}
				}
			}
			if($opties_nietbeschikbaar_geboortedatum) {
				$form->field_htmlrow("","<hr>");
				$form->field_yesno("wisopties_nietbeschikbaar",txt("wisnietbeschikbareopties","boeken"));
			}
		} else {
			$form->field_htmlrow("","<b>".html("aangezien1persoon","boeken",array("l1"=>"javascript:document.frm.submit();"))."</b><hr>");
		}
	} elseif($_GET["stap"]==4) {

		$dagen_na_bevestigdatum=round(mktime(0,0,0,date("m"),date("d"),date("Y"))-mktime(0,0,0,date("m",$gegevens["stap1"]["bevestigdatum"]),date("d",$gegevens["stap1"]["bevestigdatum"]),date("Y",$gegevens["stap1"]["bevestigdatum"]))/(60*60*24));

		# Optieformulier
		$db->query("SELECT s.optie_soort_id, s.algemeneoptie, s.naam_enkelvoud".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam_enkelvoud, s.omschrijving".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS omschrijving, s.annuleringsverzekering, s.reisverzekering, s.gekoppeld_id FROM optie_soort s, optie_accommodatie a WHERE ".($gegevens["stap1"]["website_specifiek"]["wederverkoop"] ? "s.beschikbaar_wederverkoop=1 AND " : "s.beschikbaar_directeklanten=1 AND ")."s.naam_enkelvoud".$gegevens["stap1"]["website_specifiek"]["ttv"]."<>'' AND a.accommodatie_id='".addslashes($accinfo["accommodatieid"])."' AND a.optie_soort_id=s.optie_soort_id ORDER BY s.volgorde, s.naam;");
		if($db->num_rows()) {
			unset($optie_soort,$optie_onderdeel,$optie_soort_algemeen);
			while($db->next_record()) {
				if(!$db->f("reisverzekering") or $vars["reisverzekering_mogelijk"] or $mustlogin) {
					if($db->f("algemeneoptie")) {
						$optie_soort_algemeen["naam_enkelvoud"][$db->f("optie_soort_id")]=ucfirst($db->f("naam_enkelvoud"));
						$optie_soort_algemeen[$db->f("optie_soort_id")]["annuleringsverzekering"]=$db->f("annuleringsverzekering");
						$optie_soort_algemeen[$db->f("optie_soort_id")]["reisverzekering"]=$db->f("reisverzekering");
					} else {
						$optie_soort["naam_enkelvoud"][$db->f("optie_soort_id")]=ucfirst($db->f("naam_enkelvoud"));
						$optie_soort["meerinformatie"][$db->f("optie_soort_id")]=$db->f("omschrijving");
						$optie_soort[$db->f("optie_soort_id")]["annuleringsverzekering"]=$db->f("annuleringsverzekering");
						$optie_soort[$db->f("optie_soort_id")]["reisverzekering"]=$db->f("reisverzekering");
					}

					$db2->query("SELECT o.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS naam, o.optie_onderdeel_id, o.min_leeftijd, o.max_leeftijd, o.min_deelnemers, o.actief, o.te_selecteren_door_klant, g.optie_groep_id, g.omschrijving".$gegevens["stap1"]["website_specifiek"]["ttv"]." AS omschrijving, t.verkoop, t.wederverkoop_commissie_agent FROM optie_onderdeel o, optie_groep g, optie_tarief t, optie_soort s, optie_accommodatie a, seizoen sz WHERE o.naam".$gegevens["stap1"]["website_specifiek"]["ttv"]."<>'' AND o.te_selecteren=1 AND o.actief=1".($mustlogin||$voorkant_cms||$boeking_wijzigen ? "" : " AND o.te_selecteren_door_klant=1")." AND sz.seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND a.accommodatie_id='".addslashes($accinfo["accommodatieid"])."' AND a.optie_soort_id=s.optie_soort_id AND a.optie_groep_id=g.optie_groep_id AND t.optie_onderdeel_id=o.optie_onderdeel_id AND t.seizoen_id=sz.seizoen_id AND t.week='".addslashes($gegevens["stap1"]["aankomstdatum"])."' AND t.beschikbaar=1 AND g.optie_soort_id='".$db->f("optie_soort_id")."' AND g.optie_soort_id=s.optie_soort_id AND g.optie_groep_id=o.optie_groep_id ORDER BY o.volgorde, o.naam;");
					while($db2->next_record()) {
						if($db2->f("min_leeftijd") or $db2->f("max_leeftijd")) {
							$optie_soort["leeftijdsgebonden"][$db->f("optie_soort_id")]=true;
						}
						$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["naam"]=$db2->f("naam");

						# Inactieve opties voorzien van tekst "INACTIEF" (alleen voor Chalet-medewerkers)
#						if(($mustlogin or $voorkant_cms) and $db2->f("actief")==0) {
#							$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["naam"]="INACTIEF: ".$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["naam"];
#						}

						$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["verkoop"]=$db2->f("verkoop");
						$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["commissie"]=$db2->f("wederverkoop_commissie_agent");
						$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["min_leeftijd"]=$db2->f("min_leeftijd");
						$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["max_leeftijd"]=$db2->f("max_leeftijd");
						$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["min_deelnemers"]=$db2->f("min_deelnemers");

						# te_selecteren_door_klant verwerken
						if(!$db2->f("te_selecteren_door_klant")) {
							if(($mustlogin or $voorkant_cms)) {
								$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["naam"].=" (alleen door Chalet-medewerker te boeken)";
							} elseif($boeking_wijzigen) {
								$optie_onderdeel[$db->f("optie_soort_id")][$db2->f("optie_onderdeel_id")]["verbergen"]=true;
							}
						}

						if($db2->f("omschrijving")) {
							$optie_soort["meerinformatie"][$db->f("optie_soort_id")].=$db2->f("omschrijving");
						}
						$optie_soort["optiegroepid"][$db->f("optie_soort_id")]=$db2->f("optie_groep_id");
					}
				}
			}
		}
		if($mustlogin) {
			$form->field_integer(1,"wijzigen_dagen","Aantal wijzigdagen voor vertrek","",array("text"=>$gegevens["stap1"]["wijzigen_dagen"]));
			$form->field_integer(1,"mailverstuurd_persoonsgegevens_dagenvoorvertrek","Mailtje \"persoonsgegevens gewenst\" (dagen voor vertrek)","",array("text"=>$gegevens["stap1"]["mailverstuurd_persoonsgegevens_dagenvoorvertrek"]));
#			$form->field_textarea(0,"opmerkingen_voucher","Extra op bevestiging/ roominglist/ accommodatievoucher","",array("text"=>$gegevens["stap1"]["opmerkingen_voucher"]));
			$form->field_textarea(0,"opmerkingen_klant","Opmerkingen voor klant<br><span style=\"font-size:0.75em;\">(bevestiging)</span>","",array("text"=>$gegevens["stap1"]["opmerkingen_klant"]),"",array("title_html"=>true));
			$form->field_textarea(0,"opmerkingen_voucher","Opmerkingen voor leverancier<br><span style=\"font-size:0.75em;\">(bestelmail, roominglist, aankomstlijst en accommodatievoucher)</span>","",array("text"=>$gegevens["stap1"]["opmerkingen_voucher"]),"",array("title_html"=>true));
		} elseif(!$boeking_wijzigen) {
			$form->field_noedit("accnaam",txt("accommodatie","boeken"),"",array("text"=>htmlentities($accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"])),"",array("html"=>true));
		}

		if(is_array($optie_soort_algemeen["naam_enkelvoud"])) {
			# Algemene opties selecteren
			$form->field_htmlrow("","<b>".txt("algemeneopties","boeken")."</b>");
			$koptekst_algemeneopties_getoond=true;
			reset($optie_soort_algemeen["naam_enkelvoud"]);
			while(list($key,$value)=each($optie_soort_algemeen["naam_enkelvoud"])) {
				unset($optie_keuzes);
				@reset($optie_onderdeel[$key]);
				while(list($key2,$value2)=@each($optie_onderdeel[$key])) {
					if($gegevens["stap4"]["algemene_optie"]["verkoop_op_onderdeel_id"][$key2]) {
						$verkoop=$gegevens["stap4"]["algemene_optie"]["verkoop_op_onderdeel_id"][$key2];
					} else {
						$verkoop=$value2["verkoop"];
					}
					$optie_keuzes[$key2]=$value2["naam"].": ".($verkoop<0 ? txt("korting","boeken")." " : "")."€ ".number_format(abs($verkoop),2,',','.');
				}
				if(is_array($optie_keuzes)) {
					$gekozen_algemene_optie=$gegevens["stap4"]["algemene_optie"]["optie_onderdeel_id"]["alg".$key];
					if($mustlogin or !$gegevens["stap_voltooid"][5] or $gegevens["stap1"]["wijzigen_toegestaan"] or $dagen_na_bevestigdatum<=3) {
						$form->field_select(0,"optie_".$key."_1",$value,"",array("selection"=>$gekozen_algemene_optie),array("selection"=>$optie_keuzes));
					} else {
						$form->input["optie_".$key."_1"]=$gekozen_algemene_optie;
						$form->field_htmlcol("optie_".$key."_1",$value,array("html"=>htmlentities($optie_keuzes[$gekozen_algemene_optie])));
					}
				}
			}
		}

		if(is_array($gegevens["stap4"]["algemene_optie"]["soort"])) {
			# Handmatige algemene opties tonen
			while(list($key,$value)=each($gegevens["stap4"]["algemene_optie"]["soort"])) {
				if(!$gegevens["stap4"]["algemene_optie"]["kortingscode"][$key] and !$gegevens["stap4"]["algemene_optie"]["bijkomendekosten_id"][$key] and !$gegevens["stap4"]["algemene_optie"]["bewerkbaar"][$key]) {
					if(!$koptekst_algemeneopties_getoond) {
						$form->field_htmlrow("","<b>".txt("algemeneopties","boeken")."</b>");
						$koptekst_algemeneopties_getoond=true;
					}
					$form->field_noedit("extra_algemene_optie".$key,ucfirst($value),"",array("html"=>htmlentities($gegevens["stap4"]["algemene_optie"]["naam"][$key]).": &euro;&nbsp;".$gegevens["stap4"]["algemene_optie"]["verkoop"][$key]));
				}
			}
		} else {
			if($vars["annverzekering_mogelijk"] and !$mustlogin and !$boeking_wijzigen) {
				if(!$koptekst_algemeneopties_getoond) {
					$form->field_htmlrow("","<b>".txt("algemeneoptie","boeken")."</b>");
					$koptekst_algemeneopties_getoond=true;
				}
			}
		}

		#
		# Verzekering Schade Logisch verblijven
		#

		if($gegevens["stap_voltooid"][4]) {
			if($gegevens["stap1"]["schadeverzekering"]) {
				$schadeverzekering_checkbox=true;
			} else {
				$schadeverzekering_checkbox=false;
			}
		} else {
			if($vars["seizoentype"]==2) {
				$schadeverzekering_checkbox=true;
			} else {
				$schadeverzekering_checkbox=false;
			}
		}
		if(!$gegevens["stap_voltooid"][4] and $gegevens["stap1"]["reisbureau_user_id"]) {
			# Vinkje standaard uitzetten bij boeken door reisagent
			unset($schadeverzekering_checkbox);
		}
		if($vars["websiteinfo"]["schadeverzekering_mogelijk"][$gegevens["stap1"]["website"]]) {
			if($mustlogin or !$boeking_wijzigen or !$gegevens["stap1"]["schadeverzekering"] or $gegevens["stap1"]["wijzigen_toegestaan"]) {
				$schadeverzekering_checkbox_getoond=true;
				$form->field_yesno("schadeverzekering","<b>".html("ikwileenschadeverzekering","boeken")."</b>","",array("selection"=>$schadeverzekering_checkbox),"",array("title_html"=>true));
				if(!$mustlogin) {
					$form->field_htmlrow("schadeverzekering_toelichting","<span class=\"x-small\"><a href=\"javascript:popwindow(650,0,'popup.php?id=schadeverzekering')\">".html("toelichtingschadeverzekering","boeken")."</a></span><div style=\"height:15px;\"></div>");
				}
			}
		}

		if($vars["annverzekering_mogelijk"] and !$mustlogin and !$boeking_wijzigen) {
			# Annuleringsverzekering-checkbox (bovenaan optie-pagina)
			unset($ann_verz_checkbox_counter,$ann_verz_checkbox);
			if($gegevens["stap_voltooid"][4]) {
				for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
					if($gegevens["stap3"][$i]["annverz"]<>0) $ann_verz_checkbox_counter++;
				}
				if($ann_verz_checkbox_counter==$gegevens["stap1"]["aantalpersonen"]) $ann_verz_checkbox=true;
			} else {
				// annuleringsverzekering: disabled by default (02-04-2014)
				$ann_verz_checkbox = false;
			}

			if($gegevens["stap1"]["reisbureau_user_id"] or $voorkant_cms) {
				# ann_verz standaard uitzetten voor reisagenten en CMS-gebruikers
				unset($ann_verz_checkbox);
			}
			$form->field_yesno("annuleringsverzekering","<b>".html("ikwileenannuleringsverzekering","boeken")."</b>","",array("selection"=>$ann_verz_checkbox),"",array("title_html"=>true,"onclick"=>"annverz(".$gegevens["stap1"]["aantalpersonen"].",this.checked)"));
			if(!$mustlogin) {
#				$form->field_htmlrow("ann_toelichting",html("wanneermaareendeelvandegroep","boeken")."<p><span class=\"x-small\"><a href=\"javascript:popwindow(650,0,'popup.php?id=annuleringsverzekering')\">".html("overigemogelijkhedenannuleringsverzekering","boeken")."</a></span>");
				$form->field_htmlrow("ann_toelichting","<span class=\"x-small\"><a href=\"javascript:popwindow(650,0,'popup.php?id=annuleringsverzekering')\">".html("overigemogelijkhedenannuleringsverzekering","boeken")."</a></span>");
			}
		}

		if(is_array($optie_soort["naam_enkelvoud"]) or $vars["annverzekering_mogelijk"]) {
			for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
				if(isset($gegevens["stap3"][$i]["geboortedatum"])) {
					$leeftijd=wt_leeftijd($gegevens["stap3"][$i]["geboortedatum"],mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
				} else {
					$gegevens["stap3"][$i]["fictieve_geboortedatum"]=mktime(0,0,0,1,1,(date("Y",$accinfo["aankomstdatum_unixtime"][$gegevens["stap1"]["aankomstdatum"]])-20));
					$leeftijd=wt_leeftijd($gegevens["stap3"][$i]["fictieve_geboortedatum"],mktime(0,0,0,date("m",$gegevens["stap1"]["vertrekdatum_exact"]),date("d",$gegevens["stap1"]["vertrekdatum_exact"])-1,date("Y",$gegevens["stap1"]["vertrekdatum_exact"])));
				}
				$form->field_htmlrow("",($koptekst_algemeneopties_getoond ? "<hr>" : "")."<b>".txt("opties","boeken")." ".wt_naam($gegevens["stap3"][$i]["voornaam"],$gegevens["stap3"][$i]["tussenvoegsel"],$gegevens["stap3"][$i]["achternaam"])." (".($i==1 ? txt("hoofdboeker","boeken") : txt("persoon2","boeken")." ".$i).")</b>");
				if(isset($gegevens["stap3"][$i]["geboortedatum"])) {
					$form->field_noedit("leeftijd".$i,txt("leeftijd","boeken"),"",array("text"=>txt("jaaropvoorlaatstedag","boeken",array("v_leeftijd"=>$leeftijd))));
				} elseif($mustlogin or $voorkant_cms) {
					$form->field_htmlrow("","<span class=\"intern\">Leeftijdsgebonden opties invoeren zonder geboortedatum is alleen mogelijk voor Chalet.nl-medewerkers</span>");
					$form->field_noedit("leeftijd".$i,"<span class=\"intern\">Leeftijd</span>","",array("html"=>"<span class=\"intern\">Nog niet ingevoerd</span>"),"",array("title_html"=>true));
					$geen_leeftijdscontrole=true;
				} else {
					$form->field_htmlrow("","<span class=\"opvallendeopmerking\">".html("hetopgevenvanleeftijdsgebondenopties","boeken")."</span>");
				}
				# Optie_soort doorlopen
				unset($optie_select_getoond);
				@reset($optie_soort["naam_enkelvoud"]);
				while(list($key,$value)=@each($optie_soort["naam_enkelvoud"])) {
					unset($optie_keuzes);
					@reset($optie_onderdeel[$key]);
					while(list($key2,$value2)=@each($optie_onderdeel[$key])) {

						// if reisverzekering (travel-insurance): only when land==nederland or reisverzekering is already booked
						if(!$optie_soort[$key]["reisverzekering"] or strtolower($gegevens["stap3"][$i]["land"])=="nederland" or $gegevens["stap4"]["opties"][$i][$key]) {

							if($geen_leeftijdscontrole or isset($gegevens["stap3"][$i]["geboortedatum"]) or !$optie_soort["leeftijdsgebonden"][$key]) {
								# Optie-onderdelen: leeftijdscontrole en minimaal aantal deelnemers
								if($geen_leeftijdscontrole or !$value2["min_leeftijd"] or ($value2["min_leeftijd"] and $leeftijd>=$value2["min_leeftijd"])) {
									if($geen_leeftijdscontrole or !$value2["max_leeftijd"] or ($value2["max_leeftijd"] and $leeftijd<=$value2["max_leeftijd"])) {
										if(!$value2["min_deelnemers"] or ($value2["min_deelnemers"] and $gegevens["stap1"]["aantalpersonen"]>=$value2["min_deelnemers"])) {
											if($gegevens["stap4"]["opties"][$i][$key] and $gegevens["stap4"]["opties"][$i][$key]==$key2) {
												$verkoop=$gegevens["stap4"]["optie_onderdeelid_verkoop_persoonnummer"][$key2][$i];
											} else {
												if(is_array($gegevens["stap4"]["optie_onderdeelid_verkoop_persoonnummer"][$key2])) {
													$verkoop=max($gegevens["stap4"]["optie_onderdeelid_verkoop_persoonnummer"][$key2]);
												} else {
													$verkoop=$value2["verkoop"];
												}
											}
											if($gegevens["stap4"]["opties"][$i][$key] and $optie_onderdeel[$key][$gegevens["stap4"]["opties"][$i][$key]]["verbergen"]) {
												$optie_keuzes[$key2]=$value2["naam"].": ".($verkoop<0 ? txt("korting","boeken")." " : "")."€ ".number_format(abs($verkoop),2,',','.');
											} elseif(!$optie_onderdeel[$key][$key2]["verbergen"]) {
												$optie_keuzes[$key2]=$value2["naam"].": ".($verkoop<0 ? txt("korting","boeken")." " : "")."€ ".number_format(abs($verkoop),2,',','.');
											}
										}
									}
								}
							}
						}
					}
					if(is_array($optie_keuzes)) {
						if($mustlogin or !$gegevens["stap_voltooid"][5] or $gegevens["stap1"]["wijzigen_toegestaan"] or $dagen_na_bevestigdatum<=3) {
							if($optie_soort["meerinformatie"][$key] and !$mustlogin) {
								$veldnaam=htmlentities($value)."<span class=\"x-small\"><br><span class=\"noprint\">(<a href=\"javascript:popwindow(500,0,'".$vars["path"]."popup.php?id=opties&amp;gid=".$optie_soort["optiegroepid"][$key]."');\">".html("meerinformatie","toonaccommodatie")."</a>)</span></span>";
							} else {
								$veldnaam=$value;
							}
							if($gegevens["stap4"]["opties"][$i][$key] and $optie_onderdeel[$key][$gegevens["stap4"]["opties"][$i][$key]]["verbergen"]) {
								# bewerken van optie niet toegestaan (want: alleen boekbaar door Chalet-medewerkers)
								$form->field_noedit("optie_".$key."_".$i,$veldnaam,"",array("selection"=>$gegevens["stap4"]["opties"][$i][$key],"text"=>$gegevens["stap4"]["opties"][$i][$key]),array("selection"=>$optie_keuzes),array("title_html"=>true,"add_html_after_field"=>"<br><div style=\"margin-top:2px;font-size:0.8em;font-style:italic;\">".html("optie_niet_bewerken","boeken")."</div>"));
							} else {
								$form->field_select(0,"optie_".$key."_".$i,$veldnaam,"",array("selection"=>$gegevens["stap4"]["opties"][$i][$key]),array("selection"=>$optie_keuzes),array("title_html"=>true));
							}
						} else {
							$form->input["optie_".$key."_".$i]=$gegevens["stap4"]["opties"][$i][$key];
							$form->field_htmlcol("optie_".$key."_".$i,$value,array("html"=>htmlentities($optie_keuzes[$gegevens["stap4"]["opties"][$i][$key]])));
						}
						$optie_select_getoond=true;
					}
				}

				# Annuleringsverzekering
				if($vars["annverzekering_mogelijk"] and (!$boeking_wijzigen or $gegevens["stap1"]["annuleringsverzekering_wijzigen_toegestaan"])) {

					$optie_select_getoond=true;

					# Aan of uit
					if($gegevens["stap_voltooid"][4]) {
						$annverz_keuze=$gegevens["stap3"][$i]["annverz"];
					} else {
						if($gegevens["stap1"]["reisbureau_user_id"] or $voorkant_cms) {
							$annverz_keuze=0;
						} else {
							# standaardkeuze: Europeesche Garantie
							$annverz_keuze=0;
						}
					}
					unset($annverz_array);


					if(!$mustlogin and !$boeking_wijzigen) {

						# Europeesche Garantie: is niet meer beschikbaar
						unset($vars["annverz_soorten"][2]);
					}

					# Europeesche Garantie waarneming: is niet meer beschikbaar
					unset($vars["annverz_soorten"][4]);

					reset($vars["annverz_soorten"]);
					while(list($key,$value)=each($vars["annverz_soorten"])) {
						$annverz_array[$key]=$value.": ".txt("annverz_reissomplusperboeking","boeken",array("v_percentage"=>number_format(($gegevens["stap1"]["annuleringsverzekering_percentage_".$key]>0 ? $gegevens["stap1"]["annuleringsverzekering_percentage_".$key] : $accinfo["annuleringsverzekering_percentage_".$key]),2,',','.'),"v_poliskosten"=>number_format($gegevens["stap1"]["verzekeringen_poliskosten"],2,',','.')));
					}
					if($mustlogin) {
						$veldnaam=html("annuleringsverzekering","boeken");
					} else {
						$veldnaam=html("annuleringsverzekering","boeken")."<span class=\"x-small\"><br><span class=\"noprint\">(<a href=\"javascript:popwindow(650,0,'".$vars["path"]."popup.php?id=annuleringsverzekering');\">".html("meerinformatie","toonaccommodatie")."</a>)</span></span>";
					}
					$form->field_select(0,"annverz_".$i,$veldnaam,"",array("selection"=>$annverz_keuze),array("selection"=>$annverz_array,"empty_is_0"=>true),array("title_html"=>true));
				}

				if(!$optie_select_getoond) {
					$form->field_htmlrow("","- ".txt("geenoptiesbeschikbaar","boeken")." -");
				}

				# Extra (handmatige) opties tonen (niet te bewerken)
				@reset($gegevens["stap4"][$i]["extra_opties"]["soort"]);
				while(list($key,$value)=@each($gegevens["stap4"][$i]["extra_opties"]["soort"])) {
					if(!$gegevens["stap4"][$i]["extra_opties"]["bijkomendekosten_id"][$key]) {
						$form->field_noedit("extra_optie".$i.$key,ucfirst($value),"",array("html"=>htmlentities($gegevens["stap4"][$i]["extra_opties"]["naam"][$key]).": &euro;&nbsp;".$gegevens["stap4"][$i]["extra_opties"]["verkoop"][$key]));
					}
				}
			}
		}
		$form->field_htmlrow("","<hr>");
		if($boeking_wijzigen) {
			$form->field_textarea(0,"opmerkingen_opties",txt("evtvragenopmerkingenopties","boeken"));
		} else {
			$form->field_textarea(0,"opmerkingen_opties",txt("evtvragenopmerkingenopties","boeken"),"",array("text"=>$gegevens["stap1"]["opmerkingen_opties"]));
		}

	} elseif($_GET["stap"]==5) {
		#
		# Stap 5: bevestigen boeking
		#
		if($gegevens["stap1"]["reisbureau_user_id"]) {
			$form->field_noedit("reisbureau",txt("reisbureau","boeken"),"",array("text"=>$gegevens["stap1"]["reisbureau_naam"]." - ".$gegevens["stap1"]["reisbureau_usernaam"]));
		}

		$form->field_noedit("accnaam",txt("accommodatie","boeken"),"",array("text"=>$accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"]));
		$form->field_noedit("accplaats",txt("plaats","boeken"),"",array("text"=>$accinfo["plaats"].", ".$accinfo["land"]));
		$form->field_noedit("aantalpersonen",txt("aantalpersonen","boeken"),"",array("text"=>$gegevens["stap1"]["aantalpersonen"]));
		if($gegevens["stap1"]["flexibel"] or $gegevens["stap1"]["verblijfsduur"]>1) {
			$form->field_noedit("verblijfsperiode",txt("verblijfsperiode","boeken"),"",array("text"=>DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$vars["taal"])." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum_exact"],$vars["taal"])));
		} else {
			$form->field_noedit("verblijfsperiode",txt("verblijfsperiode","boeken"),"",array("text"=>$accinfo["aankomstdatum"][$gegevens["stap1"]["aankomstdatum"]]." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum"],$vars["taal"])));
		}

		# Controle of de accommodatieprijs wel klopt
		if($gegevens["fin"]["accommodatie_verkoop"]>0) {

		} else {
			trigger_error("accommodatieprijs=0 bij stap 5 boekinsformulier",E_USER_NOTICE);
		}

		$reissomtabel=reissom_tabel($gegevens,$accinfo,array("tonen_verbergen"=>true));
		$form->field_htmlrow("","<hr><b>".txt("samenstellingreissom","boeken")."</b><p><table class=\"table\" style=\"width:660px;\">".$reissomtabel."</table><hr>");
		$reissomtabel=reissom_tabel($gegevens,$accinfo);

		if($gegevens["stap1"]["opmerkingen_opties"]) {
			$form->field_noedit("opmerkingen_opties",txt("vragenopmerkingenoveropties","boeken"),"",array("html"=>nl2br($gegevens["stap1"]["opmerkingen_opties"])),"",array("title_html"=>true));
			$form->field_textarea(0,"opmerkingen_boeker",txt("overigevragenopmerkingen","boeken")."<br><span style=\"font-size:0.8em\">(".txt("overigevragenopmerkingen_extra","boeken").")</span>","","","",array("title_html"=>true));
		} else {
			$form->field_textarea(0,"opmerkingen_boeker",txt("eventuelevragenopmerkingen","boeken")."<br><span style=\"font-size:0.8em\">(".txt("overigevragenopmerkingen_extra","boeken").")</span>","","","",array("title_html"=>true));
		}

		if($gegevens["stap1"]["reisbureau_verzendmethode_reisdocumenten"] and $gegevens["stap1"]["stap_voltooid"]<5) {
			$reisbureau_verzendmethode_reisdocumenten=$gegevens["stap1"]["reisbureau_verzendmethode_reisdocumenten"];
		} else {
			$reisbureau_verzendmethode_reisdocumenten=$gegevens["stap1"]["verzendmethode_reisdocumenten"];
		}
		if($mustlogin or $voorkant_cms) {
			# Intern: ook keuze "n.v.t" laten zien bij verzendmethode_reisdocumenten
			$form->field_select(($voorkant_cms ? 0 : 1),"verzendmethode_reisdocumenten",txt("verzendmethode_reisdocumenten","boeken"),"",array("selection"=>$reisbureau_verzendmethode_reisdocumenten),array("selection"=>$vars["verzendmethode_reisdocumenten_inclusief_nvt"],"empty_is_0"=>true),array("add_html_after_field"=>"<br><div style=\"margin-top:5px;font-size:0.8em;\">".html("verzendmethode_reisdocumenten_aangepast","boeken")."</div>"));
		} else {
			if($reisbureau_verzendmethode_reisdocumenten==3) {
				$form->field_select(1,"verzendmethode_reisdocumenten",txt("verzendmethode_reisdocumenten","boeken"),"",array("selection"=>$reisbureau_verzendmethode_reisdocumenten),array("selection"=>$vars["verzendmethode_reisdocumenten_inclusief_nvt"],"empty_is_0"=>true),array("add_html_after_field"=>"<br><div style=\"margin-top:5px;font-size:0.8em;\">".html("verzendmethode_reisdocumenten_aangepast","boeken")."</div>"));
			} else {
				$form->field_select(1,"verzendmethode_reisdocumenten",txt("verzendmethode_reisdocumenten","boeken"),"",array("selection"=>$reisbureau_verzendmethode_reisdocumenten),array("selection"=>$vars["verzendmethode_reisdocumenten"],"empty_is_0"=>true),array("add_html_after_field"=>"<br><div style=\"margin-top:5px;font-size:0.8em;\">".html("verzendmethode_reisdocumenten_aangepast","boeken")."</div>"));
			}
		}

		if(!$gegevens["stap1"]["reisbureau_user_id"]) {
			$form->field_checkbox(0,"referentiekeuze",txt("referentiekeuze","boeken",array("v_websitenaam"=>$vars["websitenaam"])),"","",array("selection"=>$vars["referentiekeuze"]),array("one_per_line"=>true));
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

		// if($vars["taal"]=="nl" and !$gegevens["stap1"]["reisbureau_user_id"] and $vars["websitetype"]<>3 and $vars["websitetype"]<>7 and $vars["websitetype"]<>8) {
		// 	if($vars["seizoentype"]==1 and $vars["wederverkoop"]) {
		// 		# Bij winter-wederverkoop: geen nieuwsbrief aanbieden
		// 	} else {
		// 		$form->field_yesno("nieuwsbrief",txt("ikwilgraaglidworden","boeken",array("v_websitenaam"=>$vars["websitenaam"])),"",array("selection"=>($temp_naw["nieuwsbrief"]||$voorkant_cms ? 0 : 1)));
		// 	}
		// }

		$form->field_yesno("akkoord",html("jaikwildezeboekingplaatsen","boeken",array("h_1"=>"</label>","h_2"=>"<label for=\"yesnoakkoord\">","l1"=>"javascript:popwindow(600,0,'popup.php?id=algemenevoorwaarden');","v_websitenaam"=>$vars["websitenaam"])),"",array("selection"=>$voorkant_cms),"",array("title_html"=>true));

	} elseif($_GET["stap"]==6) {
		# Gegevens wissen
		unset($_SESSION["boeking"]);
		unset($_COOKIE["CHALET"]["boeking"]["boekingid"]);
		setcookie("CHALET[boeking][boekingid]","_leeg_",time()+60);
		setcookie("CHALET[boeking][boekingid]","",time()-864000);
		header("Location: ".$path.txt("menu_boeking_bevestigd").".php?aanvraagnr=".$gegevens["stap1"]["boekingid"]);
		exit;
	}

	$form->check_input();

	if($form->filled) {
		if($mustlogin) {
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
		}

		if($_GET["stap"]==1) {
			if($mustlogin) {

				# Oude methode boekingsnummer
				if($form->input["reserveringsnummer_1"]) {
					$db->query("SELECT boeking_id FROM boeking WHERE SUBSTRING(boekingsnummer,2,6)='".addslashes(substr($form->input["reserveringsnummer_1"],1))."' AND boeking_id<>'".addslashes($gegevens["stap1"]["boekingid"])."';");
					if($db->num_rows()) {
						$form->error("reserveringsnummer_1","bestaat al");
					} elseif(!ereg("^".$gegevens["stap1"]["website"]."[0-9]{6}$",$form->input["reserveringsnummer_1"])) {
						$form->error("reserveringsnummer_1","onjuiste indeling");
					}
				}
				if($form->input["reserveringsnummer_2"]) {
					$db->query("SELECT boeking_id FROM boeking WHERE SUBSTRING(boekingsnummer,11,6)='".addslashes($form->input["reserveringsnummer_2"])."' AND boeking_id<>'".addslashes($gegevens["stap1"]["boekingid"])."';");
					if($db->num_rows()) {
						$form->error("reserveringsnummer_2","bestaat al bij een andere boeking");
					} elseif(!ereg("^[0-9]{6}$",$form->input["reserveringsnummer_2"])) {
						$form->error("reserveringsnummer_2","onjuiste indeling");
					}
					if(ereg("^voorraad_garantie_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {
						# Garantie
						$temp_andquery=" AND garantie_id<>'".addslashes($regs[1])."'";
					} elseif(ereg("^voorraad_garantie_([0-9]+)_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {
						# Garantie verzameltype
						$temp_andquery=" AND garantie_id<>'".addslashes($regs[2])."'";
					} else {
						$temp_andquery="";
					}
					# Controleren of het ingevoerde reserveringsnummer_2 wordt gebruikt bij een garantie
					$db->query("SELECT garantie_id FROM garantie WHERE reserveringsnummer_extern='".addslashes($form->input["reserveringsnummer_2"])."' AND boeking_id<>'".addslashes($gegevens["stap1"]["boekingid"])."'".$temp_andquery.";");
					if($db->num_rows()) {
						$form->error("reserveringsnummer_2","bestaat al bij een garantie");
					}

				}

				# Controleren of gekozen garantie wel een volgnummer heeft
				if($form->input["voorraad_afboeken"] and preg_match("/garantie: onbekend volgnummer/",$form->fields["options"]["voorraad_afboeken"]["selection"][$form->input["voorraad_afboeken"]])) {
					$form->error("voorraad_afboeken","garanties zonder volgnummer kunnen niet worden gebruikt. Voorzie de garantie van een leveranciers-volgnummer en herlaad deze pagina");
				}

				# Nieuwe methode boekingsnummer
				if($form->input["reserveringsnummer"] and $form->input["goedgekeurd"]) {
					$db->query("SELECT boeking_id FROM boeking WHERE SUBSTRING(boekingsnummer,2,8)='".addslashes(substr($form->input["reserveringsnummer"],1))."' AND boeking_id<>'".addslashes($gegevens["stap1"]["boekingid"])."';");
					if($db->num_rows()) {
						$form->error("reserveringsnummer","bestaat al");
					} elseif(!ereg("^".$gegevens["stap1"]["website"]."[0-9]{8}$",$form->input["reserveringsnummer"])) {
						$form->error("reserveringsnummer","onjuiste indeling");
					}
				}

				 # Afboeken voorraad controleren: verplicht veld
				if($form->input["goedgekeurd"] and $voorraad_afboeken_keuzes and !$form->input["voorraad_afboeken"]) {
					$form->error("voorraad_afboeken","obl");
				}

				if($form->input["voorraad_afboeken"] and $form->input["voorraad_afboeken"]<>"niet_bijwerken") {
					$voorraad_typeid=$form->input["typeid"];
					if(ereg("^voorraad_garantie_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {
						# Kijken of garantie kan worden afgeboekt
						$db->query("SELECT garantie_id, naam, reserveringsnummer_extern, aan_leverancier_doorgegeven_naam, UNIX_TIMESTAMP(inkoopdatum) AS inkoopdatum, confirmed, factuurnummer, leverancierscode, bruto, netto, korting_percentage, korting_euro, inkoopkorting_percentage, inkoopkorting_euro, inkoopaanbetaling_gewijzigd, UNIX_TIMESTAMP(inkoopfactuurdatum) AS inkoopfactuurdatum FROM garantie WHERE garantie_id='".addslashes($regs[1])."' AND boeking_id=0 AND type_id='".addslashes($form->input["typeid"])."' AND aankomstdatum='".addslashes($form->input["aankomstdatum"])."';");
						if($db->next_record()) {
							$log_garantie_tekst="boeking gekoppeld aan garantie: ".$db->f("naam").($db->f("reserveringsnummer_extern") ? " (".$db->f("reserveringsnummer_extern").")" : "");

							$temp_garantie_overnemen = true;

							$temp_garantie_factuurnummer=$db->f("factuurnummer");
							$temp_garantie_leverancierscode=$db->f("leverancierscode");

							$temp_garantie_inkoopdatum=$db->f("inkoopdatum");
							$temp_garantie_bruto=$db->f("bruto");
							$temp_garantie_netto=$db->f("netto");
							$temp_garantie_korting_percentage=$db->f("korting_percentage");
							$temp_garantie_korting_euro=$db->f("korting_euro");
							$temp_garantie_inkoopkorting_euro=$db->f("inkoopkorting_euro");
							$temp_garantie_inkoopkorting_percentage=$db->f("inkoopkorting_percentage");
							$temp_garantie_inkoopaanbetaling_gewijzigd=$db->f("inkoopaanbetaling_gewijzigd");
							$temp_garantie_aan_leverancier_doorgegeven_naam=$db->f("aan_leverancier_doorgegeven_naam");
							$temp_garantie_inkoopfactuurdatum=$db->f("inkoopfactuurdatum");
					 	} else {
							$form->error("voorraad_afboeken","deze garantie is niet (meer) beschikbaar");
						}
						$voorraad_veldnaam="voorraad_garantie";
					} elseif(ereg("^voorraad_garantie_([0-9]+)_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {
						# Garantie - in combinatie met verzameltype

						$voorraad_typeid=$regs[1];

						# Kijken of garantie kan worden afgeboekt
						$db->query("SELECT garantie_id, naam, reserveringsnummer_extern, aan_leverancier_doorgegeven_naam, UNIX_TIMESTAMP(inkoopdatum) AS inkoopdatum, confirmed, factuurnummer, leverancierscode, bruto, netto, korting_percentage, korting_euro, inkoopkorting_percentage, inkoopkorting_euro, inkoopaanbetaling_gewijzigd, UNIX_TIMESTAMP(inkoopfactuurdatum) AS inkoopfactuurdatum FROM garantie WHERE garantie_id='".addslashes($regs[2])."' AND boeking_id=0 AND type_id='".addslashes($voorraad_typeid)."' AND aankomstdatum='".addslashes($form->input["aankomstdatum"])."';");
						if($db->next_record()) {
							$log_garantie_tekst="boeking gekoppeld aan garantie: ".$db->f("naam").($db->f("reserveringsnummer_extern") ? " (".$db->f("reserveringsnummer_extern").")" : "");

							$temp_garantie_overnemen = true;

							$temp_garantie_factuurnummer=$db->f("factuurnummer");
							$temp_garantie_leverancierscode=$db->f("leverancierscode");

							$temp_garantie_inkoopdatum=$db->f("inkoopdatum");
							$temp_garantie_bruto=$db->f("bruto");
							$temp_garantie_netto=$db->f("netto");
							$temp_garantie_korting_percentage=$db->f("korting_percentage");
							$temp_garantie_korting_euro=$db->f("korting_euro");
							$temp_garantie_inkoopkorting_euro=$db->f("inkoopkorting_euro");
							$temp_garantie_inkoopkorting_percentage=$db->f("inkoopkorting_percentage");
							$temp_garantie_inkoopaanbetaling_gewijzigd=$db->f("inkoopaanbetaling_gewijzigd");
							$temp_garantie_aan_leverancier_doorgegeven_naam=$db->f("aan_leverancier_doorgegeven_naam");
							$temp_garantie_inkoopfactuurdatum=$db->f("inkoopfactuurdatum");
					 	} else {
							$form->error("voorraad_afboeken","deze garantie is niet (meer) beschikbaar");
						}
						$voorraad_veldnaam="voorraad_garantie";
					} elseif(ereg("^(voorraad_.*)_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {
						# Voorraad verzameltype
						$voorraad_typeid=$regs[2];
						$voorraad_veldnaam=$regs[1];
					} else {
						# Gewone voorraad
						$voorraad_veldnaam=$form->input["voorraad_afboeken"];
					}
					$db->query("SELECT ".addslashes($voorraad_veldnaam)." AS voorraad_afboeken FROM tarief WHERE type_id='".addslashes($voorraad_typeid)."' AND week='".addslashes($form->input["aankomstdatum"])."';");
					if(!$db->next_record() or $db->f("voorraad_afboeken")<1) {
						$form->error("voorraad_afboeken","niet (meer) beschikbaar bij deze accommodatie/aankomstdatum");
					}
				}

				# Kijken of er niet zowel een debiteur als een reisbureau wordt ingevoerd
				if($gegevens["stap1"]["wederverkoop"] and $form->input["debiteurnummer"]>0 and $form->input["reisbureauuserid"]>0) {
					$form->error("reisbureauuserid","niet mogelijk in combinatie met &quot;Zelfde debiteur als&quot; (kies &eacute;&eacute;n van beide)");
				}

				# Controleren of aankomstdatum niet tegelijk wordt gewijzigd met aankomstdatum_exact en vertrekdatum_exact
				if($form->input["aankomstdatum"]<>$gegevens["stap1"]["aankomstdatum"]) {
					if($form->input["aankomstdatum_exact"]["unixtime"]<>$gegevens["stap1"]["aankomstdatum_exact"]) {
						$form->error("aankomstdatum_exact","tegelijk wijzigen met aankomstdatum (tarieven) niet mogelijk (verander terug in ".date("d-m-Y",$gegevens["stap1"]["aankomstdatum_exact"]).")");
					}
					if($form->input["vertrekdatum_exact"]["unixtime"]<>$gegevens["stap1"]["vertrekdatum_exact"]) {
						$form->error("vertrekdatum_exact","tegelijk wijzigen met aankomstdatum (tarieven) niet mogelijk (verander terug in ".date("d-m-Y",$gegevens["stap1"]["vertrekdatum_exact"]).")");
					}
				}

				# Kijken of aantalpersonen overeenkomt met accommodatie
				$db->query("SELECT t.maxaantalpersonen, toonper FROM type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($form->input["typeid"])."';");
				if($db->next_record()) {
					if($form->input["aantalpersonen"]>$db->f("maxaantalpersonen")) {
						$form->error("aantalpersonen","het maximaal aantal personen bij deze accommodatie is ".$db->f("maxaantalpersonen"));
						$error_aantalpersonen=true;
					}
					if($gegevens["stap1"]["wederverkoop"]) {
						$db->query("SELECT type_id FROM tarief WHERE wederverkoop_verkoopprijs>0 AND type_id='".addslashes($form->input["typeid"])."' AND seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND week='".addslashes($form->input["aankomstdatum"])."';");
						if(!$db->next_record()) {
							$form->error("aankomstdatum","geen wederverkoop-tarieven in het systeem voor deze datum");
						}
					} elseif($db->f("toonper")==3) {
						$db->query("SELECT type_id FROM tarief WHERE c_verkoop_site>0 AND type_id='".addslashes($form->input["typeid"])."' AND seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND week='".addslashes($form->input["aankomstdatum"])."';");
						if(!$db->next_record()) {
							$form->error("aankomstdatum","geen tarieven in het systeem voor deze datum");
						}
					} else {
						$db->query("SELECT type_id FROM tarief WHERE (bruto>0 OR arrangementsprijs>0) AND type_id='".addslashes($form->input["typeid"])."' AND seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND week='".addslashes($form->input["aankomstdatum"])."';");
						if(!$db->next_record()) {
							$form->error("aankomstdatum","geen tarieven in het systeem voor deze datum");
						}
						if(!$error_aantalpersonen) {
							if($form->input["aankomstdatum"]==$gegevens["stap1"]["aankomstdatum"] and $form->input["typeid"]==$gegevens["stap1"]["typeid"]) {
								$db->query("SELECT verkoop FROM boeking_tarief WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND aantalpersonen='".addslashes($form->input["aantalpersonen"])."';");
								if(!$db->next_record()) {
									$form->error("aantalpersonen","geen tarieven bij deze boeking vastgelegd voor ".$form->input["aantalpersonen"]." ".($form->input["aantalpersonen"]==1 ? "persoon" : "personen").". Ga naar &quot;Bedragen&quot; om tarieven toe te voegen");
								}
							} else {
								$db->query("SELECT type_id FROM tarief_personen WHERE prijs>0 AND type_id='".addslashes($form->input["typeid"])."' AND seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND week='".addslashes($form->input["aankomstdatum"])."' AND personen='".addslashes($form->input["aantalpersonen"])."';");
								if(!$db->next_record()) {
									$form->error("aankomstdatum","geen tarieven in het systeem voor ".$form->input["aantalpersonen"]." ".($form->input["aantalpersonen"]==1 ? "persoon" : "personen")." op deze datum");
								}
							}
						}
					}
				}

				# Kijken of er bij het gekozen onderliggende type tarieven zijn ingevoerd
				if($form->input["verzameltype_gekozentype_id"]) {
					$db->query("SELECT type_id FROM tarief WHERE (bruto>0 OR c_bruto>0) AND type_id='".addslashes($form->input["verzameltype_gekozentype_id"])."' AND seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND week='".addslashes($form->input["aankomstdatum"])."';");
					if(!$db->next_record()) {
						$form->error("verzameltype_gekozentype_id","geen tarieven in het systeem voor dit onderliggende type op deze datum");
					}
				}

				// check for "dit is een vervallen aanvraag"
				if($form->input["vervallen_aanvraag"]) {
					if($form->input["goedgekeurd"]) {
						$form->error("vervallen_aanvraag","Zet \"dit is een vervallen aanvraag\" uit om de boeking te bevestigen");
					}
					if($form->input["tonen_in_mijn_boeking"]) {
						$form->error("tonen_in_mijn_boeking","Een vervallen aanvraag kan niet zichtbaar zijn in \"Mijn boeking\"");
					}
				}

			} else {
				#
				# Controle op aanwezigheid tarieven voor gekozen datum (voor klanten)
				#
#				if((($_GET["flad"] and $_GET["fldu"]) or $gegevens["stap1"]["flexibel"]) and $accinfo["flexibel"] and !$boeking_wijzigen) {
				if($accinfo["flexibel"] and flex_is_dit_flexibel($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"])) {
					$gegevens["stap1"]["flexibel"]=true;
				}

				if($accinfo["wzt"]==2 and !$boeking_wijzigen) {
					# flexibel - controle op tarief/beschikbaarheid
					if($accinfo["flexibel"]) {
						$flextarief=bereken_flex_tarief($gegevens["stap1"]["typeid"],$form->input["aankomstdatum_flex"]["unixtime"],0,flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]));
					} else {
						$flextarief=bereken_flex_tarief($gegevens["stap1"]["typeid"],$form->input["aankomstdatum"],0,flex_bereken_vertrekdatum($form->input["aankomstdatum"],$form->input["verblijfsduur"]));
					}
#					echo date("r",$form->input["aankomstdatum_flex"]["unixtime"])." ".$form->input["verblijfsduur"]." ".date("r",flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]));
					if($flextarief["tarief"]>0) {

						if($accinfo["flexibel"] and $form->input["aankomstdatum_flex"]["unixtime"]<time()) {
							# Datum ligt in het verleden
							$form->error("aankomstdatum_flex",txt("gekozenperiodenietbeschikbaar","boeken"));
						}

					} else {
						if($accinfo["flexibel"]) {
							$form->error("aankomstdatum_flex",txt("gekozenperiodenietbeschikbaar","boeken"));
						} else {
							$form->error("aankomstdatum",txt("gekozenperiodenietbeschikbaar","boeken"));
						}
						$form->error("verblijfsduur",txt("gekozenperiodenietbeschikbaar","boeken"));
					}
				} else {
					# gewoon
					unset($temp_aankomstdatum);
					if(isset($form->input["aankomstdatum"])) {
						$temp_aankomstdatum=$form->input["aankomstdatum"];
					} elseif($gegevens["stap1"]["aankomstdatum"]) {
						$temp_aankomstdatum=$gegevens["stap1"]["aankomstdatum"];
					} else {
						trigger_error("geen aankomstdatum bekend bij boeking stap1",E_USER_NOTICE);
					}
				}

				# Kortingscode
				if($form->input["kortingscode"]) {
					$_SESSION["boeking"]["kortingscode"]=strtoupper($form->input["kortingscode"]);
					$db->query("SELECT kortingscode_eenmalig_id, kortingscode_id, status FROM kortingscode_eenmalig WHERE code='".addslashes($form->input["kortingscode"])."';");
					if($db->next_record()) {
						if($db->f("status")<>2) {
							$temp_kortingscode["save_status_eenmalig"]=$db->f("kortingscode_eenmalig_id");
							# nieuwe eenmalige kortingscode: query uitvoeren op basis van kortingscode_id
							$db->query("SELECT kortingscode_id, korting_euro, korting_percentage, korting_maximaal, actietekst, UNIX_TIMESTAMP(einddatum) AS einddatum, archief FROM kortingscode WHERE kortingscode_id='".addslashes($db->f("kortingscode_id"))."' AND websites LIKE '%".$vars["website"]."%';");
						} else {
							$form->error("kortingscode",txt("kortingscodenietmeergeldig","boeken"));
							$_SESSION["boeking"]["kortingscode_foutief"]++;
						}
					} else {
						$db->query("SELECT kortingscode_id, korting_euro, korting_percentage, korting_maximaal, actietekst, UNIX_TIMESTAMP(einddatum) AS einddatum, archief FROM kortingscode WHERE code='".addslashes($form->input["kortingscode"])."' AND websites LIKE '%".$vars["website"]."%';");
					}
					if(!$form->error["kortingscode"]) {
						if($db->next_record()) {
							if($db->f("archief")==1 or ($db->f("einddatum")>0 and $db->f("einddatum")<mktime(0,0,0,date("m"),date("d"),date("Y")))) {
								$form->error("kortingscode",txt("kortingscodenietmeergeldig","boeken"));
							} else {
								$db2->query("SELECT accommodatie_id FROM kortingscode_accommodatie WHERE kortingscode_id='".$db->f("kortingscode_id")."';");
								while($db2->next_record()) {
									if($temp_kortingscode["accinquery"]) $temp_kortingscode["accinquery"].=",".$db2->f("accommodatie_id"); else $temp_kortingscode["accinquery"]=$db2->f("accommodatie_id");
								}
								$db2->query("SELECT type_id FROM kortingscode_type WHERE kortingscode_id='".$db->f("kortingscode_id")."';");
								while($db2->next_record()) {
									if($temp_kortingscode["typeinquery"]) $temp_kortingscode["typeinquery"].=",".$db2->f("type_id"); else $temp_kortingscode["typeinquery"]=$db2->f("type_id");
								}
								if(!$temp_kortingscode["accinquery"] and !$temp_kortingscode["typeinquery"]) {
									$temp_kortingscode["save_kortingscode"]=true;
								} else {
									if($temp_kortingscode["accinquery"]) {
										$db2->query("SELECT t.type_id FROM type t, accommodatie a WHERE t.type_id='".addslashes($gegevens["stap1"]["typeid"])."' AND t.accommodatie_id=a.accommodatie_id AND a.accommodatie_id IN (".$temp_kortingscode["accinquery"].");");
										if($db2->next_record()) {
											$temp_kortingscode["save_kortingscode"]=true;
										}
									}
									if($temp_kortingscode["typeinquery"]) {
										$db2->query("SELECT t.type_id FROM type t, accommodatie a WHERE t.type_id='".addslashes($gegevens["stap1"]["typeid"])."' AND t.accommodatie_id=a.accommodatie_id AND t.type_id IN (".$temp_kortingscode["typeinquery"].");");
										if($db2->next_record()) {
											$temp_kortingscode["save_kortingscode"]=true;
										}
									}
								}
								if(!$temp_kortingscode["save_kortingscode"]) {
									$form->error("kortingscode",txt("kortingscodeandereaccommodatie","boeken"));
									$_SESSION["boeking"]["kortingscode_foutief"]++;
								}
							}
							if($temp_kortingscode["save_kortingscode"]) {
								$temp_kortingscode["save_kortingscode_id"]=$db->f("kortingscode_id");
								$temp_kortingscode["save_korting_euro"]=$db->f("korting_euro");
								$temp_kortingscode["save_korting_percentage"]=$db->f("korting_percentage");
								$temp_kortingscode["save_korting_maximaal"]=$db->f("korting_maximaal");
								$temp_kortingscode["save_actietekst"]=$db->f("actietekst");
							}
						} else {
							$form->error("kortingscode",txt("kortingscodeonbekend","boeken"));
							$_SESSION["boeking"]["kortingscode_foutief"]++;
						}
					}
				}

				if($accinfo["toonper"]==3 or $vars["wederverkoop"] or $gegevens["stap1"]["wederverkoop"]) {
					if($temp_aankomstdatum) {
						if($vars["wederverkoop"] or $gegevens["stap1"]["wederverkoop"]) {
							$db->query("SELECT type_id FROM tarief WHERE wederverkoop_verkoopprijs>0 AND type_id='".addslashes($gegevens["stap1"]["typeid"])."' AND week='".addslashes($temp_aankomstdatum)."';");
							if(!$db->next_record()) {
								trigger_error("geen wederverkooptarieven in het systeem voor deze datum");
								$form->error("aankomstdatum",txt("geentarieveninhetsysteem","boeken"));
							}
						} else {
							$db->query("SELECT type_id FROM tarief WHERE c_verkoop_site>0 AND type_id='".addslashes($gegevens["stap1"]["typeid"])."' AND week='".addslashes($temp_aankomstdatum)."';");
							if(!$db->next_record()) {
								trigger_error("geen tarieven in het systeem voor deze datum");
								$form->error("aankomstdatum",txt("geentarieveninhetsysteem","boeken"));
							}
						}
					}
				} else {
					unset($temp_aantalpersonen);
					if(isset($form->input["aantalpersonen"])) {
						$temp_aantalpersonen=$form->input["aantalpersonen"];
					} elseif($gegevens["stap1"]["aankomstdatum"]) {
						$temp_aantalpersonen=$gegevens["stap1"]["aantalpersonen"];
					} else {
						trigger_error("geen aantalpersonen bekend bij boeking stap1",E_USER_NOTICE);
					}

					if($temp_aankomstdatum and $temp_aantalpersonen) {
						$db->query("SELECT type_id FROM tarief_personen WHERE prijs>0 AND type_id='".addslashes($gegevens["stap1"]["typeid"])."' AND week='".addslashes($temp_aankomstdatum)."' AND personen='".addslashes($temp_aantalpersonen)."';");
						if(!$db->next_record()) {
							trigger_error("geen tarieven in het systeem voor ".$temp_aantalpersonen." ".($temp_aantalpersonen==1 ? "persoon" : "personen")." op deze datum");
							$form->error("aantalpersonen",txt("geentarieveninhetsysteem","boeken"));
						}
					}
				}
			}

			if($gegevens["stap_voltooid"][1] and !$form->input["wisopties_nietbeschikbaar"]) {
				# Form-foutmelding
				if($opties_nietbeschikbaar) $form->error("opties_nietbeschikbaar",html("devolgendeoptiesnietmeerbeschikbaar","boeken")."<ul>".$opties_nietbeschikbaar."</ul>".html("zetvinkjebij","boeken"),"",true);
			}

		} elseif($_GET["stap"]==2) {
			if(isset($form->input["geboortedatum"]["unixtime"]) and $form->input["geboortedatum"]["unixtime"]>time()) $form->error("geboortedatum",html("geendatumtoekomst","boeken"));

			# Klopt gewijzigde geboortedatum met geselecteerde opties
			if($gegevens["stap_voltooid"][2] and !$form->input["wisopties_nietbeschikbaar"] and $opties_nietbeschikbaar_geboortedatum) {
				# Form-foutmelding
				$form->error("opties_nietbeschikbaar",html("optiesnietbeschikbaargeboortedatum","boeken")."<ul>".$opties_nietbeschikbaar_geboortedatum."</ul>".html("zetvinkjebij","boeken"),"",true);
			}

			if($form->input["wachtwoord"]) {
				if(ereg(" ",$form->input["wachtwoord"]) or strlen($form->input["wachtwoord"])<6) {
					$form->error("wachtwoord",html("gebruik6tekens","boeken"));
				}
				if($form->input["wachtwoord"]<>$form->input["wachtwoord_herhaal"]) {
					$form->error("wachtwoord_herhaal",html("tweekeerhetzelfdeww","boeken"));
				}
			}

			// check for address
			if($form->input["adres"] and (strlen(trim($form->input["adres"]))<3 or !preg_match("@[a-zA-Z]@",$form->input["adres"]))) {
				$form->error("adres",html("volledigadres", "boeken"));
			}

		} elseif($_GET["stap"]==3) {
			for($i=2;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
				if(isset($form->input["geboortedatum".$i]["unixtime"]) and $form->input["geboortedatum".$i]["unixtime"]>time()) $form->error("geboortedatum".$i,html("geendatumtoekomst","boeken"),"","",html("geboortedatumpersoon","boeken")." ".$i);

				if($form->input["voornaam".$i] or $form->input["tussenvoegsel".$i] or $form->input["achternaam".$i]) {
					if(!$form->input["voornaam".$i]) $form->error("voornaam".$i,html("vulzowelvooralsachternaamin","boeken"),"","",html("voornaampersoon","boeken")." ".$i);
					if(!$form->input["achternaam".$i]) $form->error("achternaam".$i,html("vulzowelvooralsachternaamin","boeken"),"","",html("achternaampersoon","boeken")." ".$i);
					if(!$form->input["geslacht".$i]) $form->error("geslacht".$i,html("ontbreekt","boeken"),"","",html("geslachtpersoon","boeken")." ".$i);
				}
			}
			# Klopt gewijzigde geboortedatum met geselecteerde opties
			if($gegevens["stap_voltooid"][3] and !$form->input["wisopties_nietbeschikbaar"] and $opties_nietbeschikbaar_geboortedatum) {
				# Form-foutmelding
				$form->error("opties_nietbeschikbaar",html("optiesnietbeschikbaargeboortedatum","boeken")."<ul>".$opties_nietbeschikbaar_geboortedatum."</ul>".html("zetvinkjebij","boeken"),"",true);
			}
		} elseif($_GET["stap"]==4) {
			# min_deelnemers controleren
			@reset($form->input);
			while(list($key,$value)=@each($form->input)) {
				if(ereg("^optie_([0-9]+)_([0-9]+)$",$key,$regs)) {
					if($optie_onderdeel[$regs[1]][$value]["min_deelnemers"]) {
						$optie_min_deelnemers["optie_id"][$value]=$optie_onderdeel[$regs[1]][$value]["min_deelnemers"];
						$optie_min_deelnemers["teller"][$value]++;
						$optie_min_deelnemers["naam"][$value]=$optie_onderdeel[$regs[1]][$value]["naam"];
					}
				}
			}
			while(list($key,$value)=@each($optie_min_deelnemers["optie_id"])) {
				if($value and $value>$optie_min_deelnemers["teller"][$key]) {
					$form->error("opties_min_deelnemers".$key,htmlentities($optie_min_deelnemers["naam"][$key]).": ".html("alleenmogelijkbijminimaaldeelnemers","boeken",array("v_aantal"=>$value)),"",true);
				}
			}
		} elseif($_GET["stap"]==5) {
			if(!$form->input["akkoord"]) $form->error("akkoord",html("plaatsonderaanvinkje","boeken"));
		}
	}

	if($form->okay) {
		#
		# Gegevens verwerken (opslaan in database)
		#
		if($_GET["stap"]==1) {
			$setquery="aantalpersonen='".addslashes($form->input["aantalpersonen"])."'";
			if($boeking_wijzigen and $gegevens["stap1"]["gezien"] and $form->input["aantalpersonen"] and $form->input["aantalpersonen"]<>$gegevens["stap1"]["aantalpersonen"]) {
				if(ereg("AANTAL_PERSONEN_VAN_".$form->input["aantalpersonen"]."_",$gegevens["stap1"]["gewijzigd"])) {
					$gegevens["stap1"]["gewijzigd"]=ereg_replace("AANTAL_PERSONEN_VAN_[0-9]+_","",$gegevens["stap1"]["gewijzigd"]);
					$setquery.=", gewijzigd='".addslashes(trim($gegevens["stap1"]["gewijzigd"]))."_'";
				} elseif(!ereg("AANTAL_PERSONEN_VAN",$gegevens["stap1"]["gewijzigd"])) {
					$setquery.=", gewijzigd='".addslashes(trim($gegevens["stap1"]["gewijzigd"]."\nAANTAL_PERSONEN_VAN_".$gegevens["stap1"]["aantalpersonen"]))."_'";
				}
			}

			if($accinfo["verzameltype"]) {
				# verzameltype opslaan
				$setquery.=", verzameltype='".addslashes($accinfo["verzameltype"])."'";
			}

			# reisbureau_user_id opslaan
			if(($voorkant_cms or $mustlogin) and isset($form->input["reisbureauuserid"])) {
				if($form->input["reisbureauuserid"]>0) {
					$setquery.=", reisbureau_user_id='".addslashes($form->input["reisbureauuserid"])."'";
					$reisbureauuserid=$form->input["reisbureauuserid"];
				} else {
					$setquery.=", reisbureau_user_id=NULL";
				}
			}

			# bezoeker_id opslaan
			if(strlen($_COOKIE["sch"])==32 and !$voorkant_cms and !$boeking_wijzigen) {
				$setquery.=", bezoeker_id='".addslashes($_COOKIE["sch"])."'";
			}

			# Kortingscode opslaan
			if($temp_kortingscode["save_kortingscode_id"]) {
				$setquery.=", kortingscode_id='".addslashes($temp_kortingscode["save_kortingscode_id"])."'";
			}

			# Bestelstatus en besteldatum wissen
			if($form->input["bestelstatus_wissen"]) {
				$setquery.=", bestelstatus=1, besteldatum=NULL";
			}

			if($gegevens["stap_voltooid"][1]) {
				if($mustlogin) {
					if($form->input["opmerkingen_intern"]<>$gegevens["stap1"]["opmerkingen_intern"]) {
						# Opslaan wanneer opmerkingen zijn gewijzigd
						$setquery.=", opmerkingen_intern_gewijzigd=NOW()";
					}
					if($form->input["opmerkingen_vertreklijst"] or $gegevens["stap1"]["opmerkingen_vertreklijst"]) {
						$setquery.=", opmerkingen_vertreklijst='".addslashes($form->input["opmerkingen_vertreklijst"])."'";
					}

#					$setquery.=", leverancier_id='".addslashes($form->input["leverancierid"])."', landcode='".addslashes($form->input["landcode"])."', opmerkingen_intern='".addslashes(trim($form->input["opmerkingen_intern"]))."', goedgekeurd='".addslashes($form->input["goedgekeurd"])."', geannuleerd='".addslashes(($gegevens["stap1"]["geannuleerd"] ? "1" : $form->input["geannuleerd"]))."'";
					$setquery.=", leverancier_id='".addslashes($form->input["leverancierid"])."', landcode='".addslashes($form->input["landcode"])."', opmerkingen_intern='".addslashes(trim($form->input["opmerkingen_intern"]))."', goedgekeurd='".addslashes($form->input["goedgekeurd"])."', geannuleerd='".addslashes(($form->input["geannuleerd"] ? "1" : "0"))."', btw_over_commissie='".addslashes($form->input["btw_over_commissie"])."', tonen_in_mijn_boeking='".addslashes($form->input["tonen_in_mijn_boeking"])."', vervallen_aanvraag='".intval($form->input["vervallen_aanvraag"])."'";

					# Eventueel beheerderid opslaan
					if($form->input["beheerderid"]) {
						$setquery.=", beheerder_id='".addslashes($form->input["beheerderid"])."'";
					} else {
						$setquery.=", beheerder_id=NULL";
					}

					# Eventueel eigenaarid opslaan
					if($form->input["eigenaarid"]) {
						$setquery.=", eigenaar_id='".addslashes($form->input["eigenaarid"])."'";
					} else {
						$setquery.=", eigenaar_id=NULL";
					}

					# Eventueel verzameltype_gekozentype_id opslaan
					if($form->input["verzameltype_gekozentype_id"]) {
						$setquery.=", verzameltype_gekozentype_id='".addslashes($form->input["verzameltype_gekozentype_id"])."'";
					} else {
						$setquery.=", verzameltype_gekozentype_id=NULL";
					}

					# reisbureau_user_id opslaan
					if($form->input["reisbureauuserid"]>0) {
						$setquery.=", reisbureau_user_id='".addslashes($form->input["reisbureauuserid"])."'";

						# Debiteurnummer (reisbureau) opslaan
						$db->query("SELECT DISTINCT debiteurnummer FROM boeking WHERE debiteurnummer<>0 AND reisbureau_user_id IN (SELECT user_id FROM reisbureau_user WHERE reisbureau_id=(SELECT reisbureau_id FROM reisbureau_user WHERE user_id='".addslashes($form->input["reisbureauuserid"])."'));");
						if($db->num_rows()>1) {
							trigger_error($db->num_rows()." debiteurnummers bij query \"".$db->lastquery."\"",E_USER_NOTICE);
						}
						if($db->next_record()) {
							$debiteurnummer=$db->f("debiteurnummer");
						} else {
							# Nieuw nummer automatisch aanmaken
							$db->query("SELECT MAX(debiteurnummer) AS debiteurnummer FROM boeking;");
							if($db->next_record()) {
								$debiteurnummer=$db->f("debiteurnummer")+1;
							} else {
								$debiteurnummer=1;
							}
						}
					} else {

						# Debiteurnummer (gewone boeker) opslaan
						if($form->input["debiteurnummer"]>0) {
							# Via formulier
							$debiteurnummer=$form->input["debiteurnummer"];
						} elseif($form->input["goedgekeurd"] and !$gegevens["stap1"]["goedgekeurd"]) {
							# Nieuw nummer automatisch aanmaken
							$db->query("SELECT MAX(debiteurnummer) AS debiteurnummer FROM boeking;");
							if($db->next_record()) {
								$debiteurnummer=$db->f("debiteurnummer")+1;
							} else {
								$debiteurnummer=1;
							}
						}
					}

					if($debiteurnummer>0) $setquery.=", debiteurnummer='".addslashes($debiteurnummer)."'";

					$nieuw_accinfo=accinfo($form->input["typeid"],$form->input["aankomstdatum"],$form->input["aantalpersonen"]);
					if($form->input["typeid"] and $form->input["typeid"]<>$accinfo["typeid"]) {
						# Gewijzigde accommodatie-gegevens opslaan
						$setquery.=", type_id='".addslashes($form->input["typeid"])."', verzameltype='".addslashes($nieuw_accinfo["verzameltype"])."', ".($nieuw_accinfo["verzameltype"] ? "" : "verzameltype_gekozentype_id=NULL, ").($gegevens["stap1"]["reisbureau_user_id"] ? "commissie='".addslashes(($nieuw_accinfo["commissie"]+$gegevens["stap1"]["reisbureau_aanpassing_commissie"]))."', " : "")." toonper='".addslashes($nieuw_accinfo["toonper"])."', naam_accommodatie='".addslashes($nieuw_accinfo["begincode"].$nieuw_accinfo["typeid"]." - ".$nieuw_accinfo["plaats"]." - ".ucfirst($nieuw_accinfo["soortaccommodatie"])." ".$nieuw_accinfo["naam"])."'";
						$nieuw_tarief=$nieuw_accinfo["tarief"];

						# nieuwe accprijs opslaan
						if($nieuw_accinfo["accprijs"]) {
							$setquery.=", accprijs='".addslashes($nieuw_accinfo["accprijs"])."'";
						}
						$tariefswijziging=true;
						$tariefswijziging_inkoop=true;
					}
					if($form->input["aankomstdatum"] and $form->input["aankomstdatum"]<>$gegevens["stap1"]["aankomstdatum"]) {
						# Nieuwe aankomstdatum opslaan
						$setquery.=", aankomstdatum='".addslashes($form->input["aankomstdatum"])."', aankomstdatum_exact='".addslashes($nieuw_accinfo["aankomstdatum_unixtime"][$form->input["aankomstdatum"]])."', vertrekdatum_exact='".addslashes($nieuw_accinfo["vertrekdatum"])."'";
						$nieuw_tarief=$nieuw_accinfo["tarief"];

						# nieuwe accprijs opslaan
						if($nieuw_accinfo["accprijs"]) {
							$setquery.=", accprijs='".addslashes($nieuw_accinfo["accprijs"])."'";
						}
						$tariefswijziging=true;
						$tariefswijziging_inkoop=true;
					}

					# Afwijkende vertrek/aankomstdata opslaan
					if($form->input["aankomstdatum_exact"]["unixtime"]<>$gegevens["stap1"]["aankomstdatum_exact"]) {
						$setquery.=", aankomstdatum_exact='".addslashes($form->input["aankomstdatum_exact"]["unixtime"])."'";
					}
					if($form->input["vertrekdatum_exact"]["unixtime"]<>$gegevens["stap1"]["vertrekdatum_exact"]) {
						$setquery.=", vertrekdatum_exact='".addslashes($form->input["vertrekdatum_exact"]["unixtime"])."'";
					}

					# Boekingsnummer opslaan
					if($form->input["goedgekeurd"]) {
						if($form->input["reserveringsnummer_1"] and $form->input["reserveringsnummer_2"]) {
							$setquery.=", boekingsnummer='".addslashes($form->input["reserveringsnummer_1"]." / ".$form->input["reserveringsnummer_2"])."'";
						} else {
							$setquery.=", boekingsnummer='".addslashes($form->input["reserveringsnummer"])."'";
						}
					} else {
						$setquery.=", boekingsnummer=''";
					}

					# Optie aan klant wegstrepen
					if($form->input["voorraad_optie_klant"]) {
						$db->query("UPDATE tarief SET voorraad_optie_klant=voorraad_optie_klant-1 WHERE type_id='".addslashes($form->input["typeid"])."' AND seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND week='".addslashes($form->input["aankomstdatum"])."';");
					}

					# Voorraad bijwerken
					if($form->input["voorraad_afboeken"]) {
						unset($voorraad_veldnaam,$bijwerken_garantie,$bijwerken_allotment,$bijwerken_optie_leverancier,$bijwerken_request,$verzameltype_gekozentype_id);

						$voorraad_typeid=$form->input["typeid"];

						if(ereg("^voorraad_garantie_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {
							# Garantie opslaan in tabel garantie
							$db->query("UPDATE garantie SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', verkoopdatum=NOW() WHERE garantie_id='".addslashes($regs[1])."';");
							$voorraad_veldnaam="voorraad_garantie";
							$log_garantie=true;

							# garantie-inkoopbetalingen overzetten naar boekings-inkoopbetalingen
							$db->query("UPDATE boeking_betaling_lev SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' WHERE garantie_id='".addslashes($regs[1])."';");

						} elseif(ereg("^voorraad_garantie_([0-9]+)_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {
							# Garantie - in combinatie met verzameltype
							$voorraad_typeid=$regs[1];

							# Garantie opslaan in tabel garantie
							$db->query("UPDATE garantie SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', verkoopdatum=NOW() WHERE garantie_id='".addslashes($regs[2])."';");
							$voorraad_veldnaam="voorraad_garantie";
							$log_garantie=true;

							$verzameltype_gekozentype_id=$regs[1];

							# garantie-inkoopbetalingen overzetten naar boekings-inkoopbetalingen
							$db->query("UPDATE boeking_betaling_lev SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' WHERE garantie_id='".addslashes($regs[1])."';");

						} elseif(ereg("^(voorraad_.*)_([0-9]+)$",$form->input["voorraad_afboeken"],$regs)) {

							# Voorraad verzameltype
							$voorraad_typeid=$regs[2];
							$voorraad_veldnaam=$regs[1];

							$verzameltype_gekozentype_id=$regs[2];
						} else {
							$voorraad_veldnaam=$form->input["voorraad_afboeken"];
						}

						if($voorraad_veldnaam=="voorraad_garantie") {
							$bijwerken_garantie=-1;
							$log_afboeken=true;
							$setquery.=", voorraad_afboeken=2";
						} elseif($voorraad_veldnaam=="voorraad_allotment") {
							$bijwerken_allotment=-1;
							$log_afboeken=true;
							$setquery.=", voorraad_afboeken=3";
						} elseif($voorraad_veldnaam=="voorraad_vervallen_allotment") {
							$bijwerken_vervallen_allotment=-1;
							$log_afboeken=true;
							$setquery.=", voorraad_afboeken=4";
						} elseif($voorraad_veldnaam=="voorraad_request") {
							$bijwerken_request=-1;
							$log_afboeken=true;
							$setquery.=", voorraad_afboeken=5";
						} elseif($voorraad_veldnaam=="voorraad_optie_leverancier") {
							$bijwerken_optie_leverancier=-1;
							$log_afboeken=true;
							$setquery.=", voorraad_afboeken=6";
						} elseif($voorraad_veldnaam=="niet_bijwerken") {
							$setquery.=", voorraad_afboeken=1";
						}
						voorraad_bijwerken($voorraad_typeid,$form->input["aankomstdatum"],true,$bijwerken_garantie,$bijwerken_allotment,$bijwerken_vervallen_allotment,$bijwerken_optie_leverancier,0,$bijwerken_request,0,true,6);

						if($temp_garantie_overnemen) {
							# Leveranciers-reserveringsnummer, bruto, netto, inkoopdatum uit garantie koppelen aan boeking
							$setquery.=", leverancierscode='".addslashes($temp_garantie_leverancierscode)."', factuurnummer_leverancier='".addslashes($temp_garantie_factuurnummer)."', bestelstatus=3, inkoopnetto='".addslashes($temp_garantie_netto)."', inkoopbruto='".addslashes($temp_garantie_bruto)."'";
							if($temp_garantie_inkoopdatum>0) {
								# inkoopdatum garantie opslaan in besteldatum boeking
								$setquery.=", besteldatum=FROM_UNIXTIME('".$temp_garantie_inkoopdatum."')";
							} else {
								# nog geen inkoopdatum bij de garantie
								$setquery.=", besteldatum=NULL";
							}
							if($temp_garantie_inkoopaanbetaling_gewijzigd<>"") {
								$setquery.=", inkoopaanbetaling_gewijzigd='".addslashes($temp_garantie_inkoopaanbetaling_gewijzigd)."'";
							}
							if($temp_garantie_inkoopfactuurdatum) {
								$setquery.=", inkoopfactuurdatum='".addslashes($temp_garantie_inkoopfactuurdatum)."'";
							}
							if($temp_garantie_aan_leverancier_doorgegeven_naam) {
								$setquery.=", aan_leverancier_doorgegeven_naam='".addslashes($temp_garantie_aan_leverancier_doorgegeven_naam)."'";
							}

							$log_inkoopprijzen="bruto-inkoop (€ ".number_format($temp_garantie_bruto,2,",",".")."), netto-inkoop (€ ".number_format($temp_garantie_netto,2,",",".").")";

							# commissie
							if($temp_garantie_korting_percentage<>0) {
								$log_inkoopprijzen.=", commissie (".number_format($temp_garantie_korting_percentage,2,",",".")."%)";
								$setquery.=", inkoopcommissie='".addslashes($temp_garantie_korting_percentage)."'";
							} else {
								$setquery.=", inkoopcommissie=NULL";
							}

							# korting/toeslag
							if($temp_garantie_korting_euro<>0) {
								$log_inkoopprijzen.=", korting/toeslag (€ ".number_format($temp_garantie_korting_euro,2,",",".").")";
								$setquery.=", inkoopkorting='".addslashes($temp_garantie_korting_euro)."'";
							} else {
								$setquery.=", inkoopkorting=NULL";
							}

							# inkoopkorting accommodatie percentage
							if($temp_garantie_inkoopkorting_percentage<>0) {
								$log_inkoopprijzen.=", inkoopkorting accommodatie (".number_format($temp_garantie_inkoopkorting_percentage,2,",",".")."%)";
								$setquery.=", inkoopkorting_percentage='".addslashes($temp_garantie_inkoopkorting_percentage)."'";
							} else {
								$setquery.=", inkoopkorting_percentage=NULL";
							}

							# inkoopkorting accommodatie euro
							if($temp_garantie_inkoopkorting_euro<>0) {
								$log_inkoopprijzen.=", inkoopkorting accommodatie (€ ".number_format($temp_garantie_inkoopkorting_euro,2,",",".").")";
								$setquery.=", inkoopkorting_euro='".addslashes($temp_garantie_inkoopkorting_euro)."'";
							} else {
								$setquery.=", inkoopkorting_euro=NULL";
							}

							$log_inkoopprijzen.=" overgenomen van garantie";
						}

						# verzameltype_gekozentype_id opslaan
						if($verzameltype_gekozentype_id) {
							$setquery.=", verzameltype_gekozentype_id='".addslashes($verzameltype_gekozentype_id)."'";
						}
					}

					if($form->input["voorraad_afboeken_change"]) {
						// manually changed "Bij bevestigen is voorraad afgeboekt van"
						$setquery.=", voorraad_afboeken='".intval($form->input["voorraad_afboeken_change"])."'";

						if($form->input["voorraad_afboeken_change"]<>$gegevens["stap1"]["voorraad_afboeken"]) {
							chalet_log("handmatig aangepast \"Bij bevestigen is voorraad afgeboekt van\": ".$vars["voorraad_afboeken"][$form->input["voorraad_afboeken_change"]]);
						}
					}

					#
					# Boeking annuleren
					#
					if($form->input["geannuleerd"] and !$gegevens["stap1"]["geannuleerd"]) {
						#
						# Creditfactuur
						#
						$db->query("SELECT factuur_id FROM factuur WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND credit=0 ORDER BY datum DESC, volgorde_datetime DESC, factuur_id DESC LIMIT 0,1");
						if($db->next_record()) {
							$oude_factuurid=$db->f("factuur_id");
							# Nieuw factuurnummer aanmaken (functie is niet actief!)
							$creditfactuur_datum=mktime(0,0,0,date("m"),date("d"),date("Y"));
							$prefix=$vars["factuurnummer_prefix"][boekjaar($creditfactuur_datum)];
							$db->query("SELECT MAX(factuur_id) AS factuur_id FROM factuur WHERE SUBSTRING(factuur_id,1,2)='".$prefix."';");
							if($db->next_record()) {
								$creditfactuur_factuurid=$db->f("factuur_id")+1;
							}
							if($creditfactuur_factuurid==1) {
								$creditfactuur_factuurid=intval($prefix."00001");
							}
							$db->query("SELECT regelnummer, omschrijving, bedrag, grootboektype FROM factuurregel WHERE factuur_id='".$oude_factuurid."';");
							while($db->next_record()) {
								$creditfactuur_bedrag=0-$db->f("bedrag");
								$creditfactuur_omschrijving="credit: ".$db->f("omschrijving");
							}
						}

						# Opslaan dat er een gecorrigeerde factuur moet worden aangemaakt
						$db->query("UPDATE boeking SET factuur_versturen=1 WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

					}
					if($tariefswijziging_inkoop and !$log_inkoopprijzen) {
						# Inkoopgegevens opnieuw opslaan
						$inkoop=inkoopprijs_bepalen($form->input["typeid"],$form->input["aankomstdatum"]);
						if($inkoop["bruto"]>0 and $inkoop["netto"]>0) {
							$setquery.=", inkoopnetto='".addslashes($inkoop["netto"])."', inkoopbruto='".addslashes($inkoop["bruto"])."'";

							$log_inkoopprijzen="bruto-inkoop (€ ".number_format($inkoop["bruto"],2,",",".")."), netto-inkoop (€ ".number_format($inkoop["netto"],2,",",".").")";

							if($inkoop["inkoopcommissie"]<>0) {
								$setquery.=", inkoopcommissie='".addslashes($inkoop["inkoopcommissie"])."'";
								$log_inkoopprijzen.=", commissie (".number_format($inkoop["inkoopcommissie"],2,",",".")."%)";
							} else {
								$setquery.=", inkoopcommissie=NULL";
							}
							if($inkoop["inkooptoeslag"]<>0) {
								$setquery.=", inkooptoeslag='".addslashes($inkoop["inkooptoeslag"])."'";
								$log_inkoopprijzen.=", toeslag (€ ".number_format($inkoop["inkooptoeslag"],2,",",".").")";
							} else {
								$setquery.=", inkooptoeslag=NULL";
							}
							if($inkoop["inkoopkorting"]<>0) {
								$setquery.=", inkoopkorting='".addslashes($inkoop["inkoopkorting"])."'";
								$log_inkoopprijzen.=", korting (€ ".number_format($inkoop["inkoopkorting"],2,",",".").")";
							} else {
								$setquery.=", inkoopkorting=NULL";
							}
							if($inkoop["inkoopkorting_percentage"]<>0) {
								$setquery.=", inkoopkorting_percentage='".addslashes($inkoop["inkoopkorting_percentage"])."'";
								$log_inkoopprijzen.=", korting (".number_format($inkoop["inkoopkorting_percentage"],2,",",".")."%)";
							} else {
								$setquery.=", inkoopkorting_percentage=NULL";
							}
							if($inkoop["inkoopkorting_euro"]<>0) {
								$setquery.=", inkoopkorting_euro='".addslashes($inkoop["inkoopkorting_euro"])."'";
								$log_inkoopprijzen.=", korting (€ ".number_format($inkoop["inkoopkorting_euro"],2,",",".").")";
							} else {
								$setquery.=", inkoopkorting_euro=NULL";
							}
							$log_inkoopprijzen.=" overgenomen van tarieventabel";
						}
					}
				} elseif(!$boeking_wijzigen) {
					# wijzigen boeking door klant via boekingsformulier (dus NIET via 'Mijn boeking')

					if($accinfo["wzt"]==2) {
						# flexibel
						if($accinfo["flexibel"]) {
							$form->input["aankomstdatum"]=dichtstbijzijnde_zaterdag($form->input["aankomstdatum_flex"]["unixtime"]);
							$setquery.=", aankomstdatum='".addslashes($form->input["aankomstdatum"])."', aankomstdatum_exact='".addslashes($form->input["aankomstdatum_flex"]["unixtime"])."', vertrekdatum_exact='".addslashes(flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]))."', verblijfsduur='".addslashes($form->input["verblijfsduur"])."'";
							if(flex_is_dit_flexibel($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"])) {
								$setquery.=", flexibel=1";
							} else {
								$setquery.=", flexibel=0";
								$gegevens["stap1"]["flexibel"]=false;
							}
						} else {
							$setquery.=", flexibel=0, aankomstdatum='".addslashes($form->input["aankomstdatum"])."', aankomstdatum_exact='".addslashes($form->input["aankomstdatum"])."', vertrekdatum_exact='".addslashes(flex_bereken_vertrekdatum($form->input["aankomstdatum"],$form->input["verblijfsduur"]))."', verblijfsduur='".addslashes($form->input["verblijfsduur"])."'";
						}
						$tariefswijziging=true;
					} else {
						# gewoon
						$nieuw_accinfo=accinfo($accinfo["typeid"],$form->input["aankomstdatum"],$form->input["aantalpersonen"]);
						if($form->input["aankomstdatum"]<>$gegevens["stap1"]["aankomstdatum"]) {
							$setquery.=", aankomstdatum='".addslashes($form->input["aankomstdatum"])."', aankomstdatum_exact='".addslashes($nieuw_accinfo["aankomstdatum_unixtime"][$form->input["aankomstdatum"]])."', vertrekdatum_exact='".addslashes($nieuw_accinfo["vertrekdatum"])."'";
						}
					}
				}

				# Gegevens uit boeking_tarief halen (indien toonper<>3 en aantal personen is gewijzigd)
				if(!$nieuw_tarief and $accinfo["toonper"]<>3 and !$gegevens["stap1"]["wederverkoop"] and $form->input["aantalpersonen"]<>$gegevens["stap1"]["aantalpersonen"]) {
					$db->query("SELECT verkoop FROM boeking_tarief WHERE aantalpersonen='".addslashes($form->input["aantalpersonen"])."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."'");
					if($db->next_record()) {
						$nieuw_tarief=$db->f("verkoop");
					}
				}

				if($nieuw_tarief>0 and ($boeking_wijzigen or $mustlogin)) {
					$setquery.=", verkoop='".addslashes($nieuw_tarief)."'";
				}



				$db->query("UPDATE boeking SET ".$setquery." WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

				# Bij wijzigen aantal personen: gegevens en opties wissen van personen die zijn afgevallen
				if($form->input["aantalpersonen"]<$gegevens["stap1"]["aantalpersonen"]) {

					# Persoonlijke gegevens
					$db->query("DELETE FROM boeking_persoon WHERE persoonnummer>'".$form->input["aantalpersonen"]."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

					# Gewone opties
					$db->query("DELETE FROM boeking_optie WHERE persoonnummer>'".$form->input["aantalpersonen"]."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

					# Handmatige opties
					$db->query("SELECT extra_optie_id, deelnemers FROM extra_optie WHERE deelnemers<>'' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
					while($db->next_record()) {
						unset($temp_deelnemers_oud,$temp_deelnemers_nieuw);
						$temp_deelnemers_oud=@split(",",$db->f("deelnemers"));
						for($i=1;$i<=$form->input["aantalpersonen"];$i++) {
							if(@in_array($i,$temp_deelnemers_oud)) {
								if($temp_deelnemers_nieuw) $temp_deelnemers_nieuw.=",".$i; else $temp_deelnemers_nieuw=$i;
							}
						}
						$db2->query("UPDATE extra_optie SET deelnemers='".addslashes($temp_deelnemers_nieuw)."' WHERE extra_optie_id='".$db->f("extra_optie_id")."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
					}
					$tariefswijziging=true; # annverz_verzekerdbedrag opnieuw berekenen
				}

				# Bij wijzigen aantal personen: boeking_persoon aanvullen
				if($form->input["aantalpersonen"]>$gegevens["stap1"]["aantalpersonen"]) {
					$tariefswijziging=true; # annverz_verzekerdbedrag opnieuw berekenen
					for($i=$gegevens["stap1"]["aantalpersonen"];$i<=$form->input["aantalpersonen"];$i++) {
						$db->query("INSERT INTO boeking_persoon SET persoonnummer='".$i."', status=2, boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
					}
				}

				# Niet meer beschikbare opties wissen
				if($opties_nietbeschikbaar_id) {
					$db->query("DELETE FROM boeking_optie WHERE optie_onderdeel_id IN (".$opties_nietbeschikbaar_id.") AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				}

				# Nog gehandhaafde opties van nieuw tarief voorzien, indien accommodatie of aankomstdatum wordt gewijzigd
				if($form->input["aankomstdatum"]<>$gegevens["stap1"]["aankomstdatum"] or $form->input["typeid"]<>$accinfo["typeid"]) {
					$db->query("SELECT ot.verkoop, ot.wederverkoop_commissie_agent, bo.optie_onderdeel_id FROM optie_tarief ot, boeking_optie bo WHERE bo.optie_onderdeel_id=ot.optie_onderdeel_id AND bo.boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND ot.seizoen_id='".addslashes($gegevens["stap1"]["seizoenid"])."' AND ot.week='".addslashes($form->input["aankomstdatum"])."';");
					while($db->next_record()) {
						$db2->query("UPDATE boeking_optie SET verkoop='".$db->f("verkoop")."'".($gegevens["stap1"]["reisbureau_user_id"] ? ", commissie='".addslashes($db->f("wederverkoop_commissie_agent"))."' " : "")." WHERE optie_onderdeel_id='".addslashes($db->f("optie_onderdeel_id"))."' AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
					}
				}

				if($tariefswijziging) {
					# Gewijzigd annverz_verzekerdbedrag opslaan
					$nieuw_gegevens=get_boekinginfo($gegevens["stap1"]["boekingid"]);

#					$db->query("SELECT persoonnummer, status FROM boeking_persoon WHERE annverz_verzekerdbedrag>0 AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
					$db->query("SELECT persoonnummer, status FROM boeking_persoon WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
					while($db->next_record()) {
						$db2->query("UPDATE boeking_persoon SET annverz_verzekerdbedrag=".($nieuw_gegevens["stap4"][$db->f("persoonnummer")]["annverz_verzekerdbedrag_actueel"]>0 ? "'".addslashes($nieuw_gegevens["stap4"][$db->f("persoonnummer")]["annverz_verzekerdbedrag_actueel"])."'" : "NULL")." WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer='".addslashes($db->f("persoonnummer"))."' AND status='".addslashes($db->f("status"))."';");
					}
				}

			} else {

				#
				# Nieuwe boeking opslaan
				#

				# nagaan of het een flexibele boeking is
				if($accinfo["wzt"]==2) {


					if($accinfo["flexibel"]) {
						if(flex_is_dit_flexibel($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"])) {
							$gegevens["stap1"]["flexibel"]=true;
						}
						$form->input["aankomstdatum"]=dichtstbijzijnde_zaterdag($form->input["aankomstdatum_flex"]["unixtime"]);
						$setquery.=", aankomstdatum_exact='".addslashes($form->input["aankomstdatum_flex"]["unixtime"])."', vertrekdatum_exact='".addslashes(flex_bereken_vertrekdatum($form->input["aankomstdatum_flex"]["unixtime"],$form->input["verblijfsduur"]))."', verblijfsduur='".addslashes($form->input["verblijfsduur"])."'";
					} else {
						$nieuw_accinfo=accinfo($_GET["tid"],$form->input["aankomstdatum"],$form->input["aantalpersonen"]);
						$setquery.=", aankomstdatum_exact='".addslashes($nieuw_accinfo["aankomstdatum_unixtime"][$form->input["aankomstdatum"]])."', vertrekdatum_exact='".addslashes(flex_bereken_vertrekdatum($nieuw_accinfo["aankomstdatum_unixtime"][$form->input["aankomstdatum"]],$form->input["verblijfsduur"]))."', verblijfsduur='".addslashes($form->input["verblijfsduur"])."'";
					}
				}

				if($gegevens["stap1"]["flexibel"]) {
					# flexibel
					$setquery.=", flexibel=1";
				}

				$db->query("SELECT seizoen_id FROM tarief WHERE type_id='".addslashes($_GET["tid"])."' AND week='".addslashes($form->input["aankomstdatum"])."';");
				if($db->next_record()) {
					$seizoenid=$db->f("seizoen_id");
				}
				$nieuw_accinfo=accinfo($_GET["tid"],$form->input["aankomstdatum"],$form->input["aantalpersonen"]);

				$reserveringskosten=$vars["reserveringskosten"];
				if($vars["wederverkoop"]) {
					# reisbureau_user_id opslaan indien er is ingelogd door een reisbureau
					if($login_rb->logged_in or $reisbureauuserid) {
						if($login_rb->logged_in and !$reisbureauuserid) {
							$setquery.=", reisbureau_user_id='".addslashes($login_rb->user_id)."'";
							$reisbureauuserid=$login_rb->user_id;
						}

						# Kijken of er reserveringskosten in rekening moeten worden gebracht
						$db->query("SELECT r.reserveringskosten, r.aanbetaling1_dagennaboeken, r.totale_reissom_dagenvooraankomst, r.geenaanbetaling, r.btw_over_commissie FROM reisbureau r, reisbureau_user u WHERE u.reisbureau_id=r.reisbureau_id AND u.user_id='".addslashes($reisbureauuserid)."';");
						if($db->next_record()) {
							if(!$db->f("reserveringskosten")) $reserveringskosten=0;

							# Aanbetaling-dagen bepalen
							if($db->f("aanbetaling1_dagennaboeken") or $db->f("aanbetaling1_dagennaboeken")=="0") $temp_aanbetaling1_dagennaboeken=$db->f("aanbetaling1_dagennaboeken");
							if($db->f("totale_reissom_dagenvooraankomst") or $db->f("totale_reissom_dagenvooraankomst")=="0") $temp_totale_reissom_dagenvooraankomst=$db->f("totale_reissom_dagenvooraankomst");

							if($db->f("geenaanbetaling")) {
								# Geen aanbetaling bij dit reisbureau
								$geenaanbetaling=true;
							}

							# Bepalen of dit reisbureau BTW over commissie betaalt
							$setquery.=", btw_over_commissie='".addslashes($db->f("btw_over_commissie"))."'";
						}

						# BTW-percentage commissie opslaan
						$db->query("SELECT percentage FROM btwpercentage_commissie WHERE vanaf<=NOW() ORDER BY vanaf DESC LIMIT 0,1;");
						if($db->next_record()) {
							$setquery.=", btw_over_commissie_percentage='".addslashes($db->f("percentage"))."'";
						}
					}
				}

				# Aanbetaling-dagen bepalen
				if(!$temp_aanbetaling1_dagennaboeken) $temp_aanbetaling1_dagennaboeken=$vars["aanbetaling1_dagennaboeken"];
				if(!$temp_totale_reissom_dagenvooraankomst) $temp_totale_reissom_dagenvooraankomst=$vars["totale_reissom_dagenvooraankomst"];

				# Koppeling met bestaande optieaanvraag?
				if($_GET["oaid"]) {
					$setquery.=", optieaanvraag_id='".addslashes($_GET["oaid"])."'";
				}

				$setquery.=", reserveringskosten='".addslashes($reserveringskosten)."', taal='".addslashes($vars["taal"])."', website='".addslashes($vars["website"])."', aankomstdatum='".addslashes($form->input["aankomstdatum"])."'";
				if($accinfo["wzt"]<>2) {
					$setquery.=", aankomstdatum_exact='".addslashes($nieuw_accinfo["aankomstdatum_unixtime"][$form->input["aankomstdatum"]])."', vertrekdatum_exact='".addslashes($nieuw_accinfo["vertrekdatum"])."'";
				}

				# geen losse poliskosten meer per verzekering
#				$setquery.=", annuleringsverzekering_poliskosten='".addslashes($nieuw_accinfo["annuleringsverzekering_poliskosten"])."'";
				$setquery.=", annuleringsverzekering_percentage_1='".addslashes($nieuw_accinfo["annuleringsverzekering_percentage_1"])."', annuleringsverzekering_percentage_2='".addslashes($nieuw_accinfo["annuleringsverzekering_percentage_2"])."', annuleringsverzekering_percentage_3='".addslashes($nieuw_accinfo["annuleringsverzekering_percentage_3"])."', annuleringsverzekering_percentage_4='".addslashes($nieuw_accinfo["annuleringsverzekering_percentage_4"])."', schadeverzekering_percentage='".addslashes($nieuw_accinfo["schadeverzekering_percentage"])."'";

				# geen losse poliskosten meer per verzekering
#				$setquery.=", reisverzekering_poliskosten='".addslashes($nieuw_accinfo["reisverzekering_poliskosten"])."'";

				$setquery.=", verzekeringen_poliskosten='".addslashes($nieuw_accinfo["verzekeringen_poliskosten"])."', seizoen_id='".addslashes($seizoenid)."', toonper='".addslashes($accinfo["toonper"])."', wederverkoop='".($vars["wederverkoop"] ? "1" : "0")."', naam_accommodatie='".addslashes($accinfo["begincode"].$accinfo["typeid"]." - ".$accinfo["plaats"]." - ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"])."', invuldatum=NOW(), stap_voltooid=1, type_id='".addslashes($_GET["tid"])."', leverancier_id='".addslashes($accinfo["leverancierid"])."', aanbetaling1_dagennaboeken='".addslashes($temp_aanbetaling1_dagennaboeken)."', totale_reissom_dagenvooraankomst='".addslashes($temp_totale_reissom_dagenvooraankomst)."'";

				# Eventueel beheerder_id opslaan
				if($accinfo["beheerderid"]) {
					$setquery.=", beheerder_id='".addslashes($accinfo["beheerderid"])."'";
				}

				if($accinfo["eigenaarid"]) {
					$setquery.=", eigenaar_id='".addslashes($accinfo["eigenaarid"])."'";
				}

				if($geenaanbetaling) {
					$setquery.=", aanbetaling1_gewijzigd=0";
				}

				$setquery.=", valt_onder_bedrijf='".intval($vars["valt_onder_bedrijf"])."'";

				$db->query("INSERT INTO boeking SET ".$setquery.";");
// echo $db->lastquery;
// exit;
				if($db->insert_id()) {
					$_SESSION["boeking"]["boekingid"][$db->insert_id()]=true;
					$gegevens["stap1"]["boekingid"]=$db->insert_id();

					$form->settings["goto"]=str_replace("&bfbid=&","&bfbid=".$db->insert_id()."&",$form->settings["goto"]);
					$form->settings["goto"]=str_replace("?bfbid=&","?bfbid=".$db->insert_id()."&",$form->settings["goto"]);
				}
			}
			#
			# Actuele tarieven opslaan in boeking_tarief (alleen voor tarievenoptie A en B (toonper=1 of 2))
			# alleen bij mustlogin en gewijzigde accommodatie of aankomstdatum
			#
			if($nieuw_accinfo["toonper"]) {
				$temp_toonper=$nieuw_accinfo["toonper"];
			} else {
				$temp_toonper=$accinfo["toonper"];
			}
			if($temp_toonper<>3 and !$gegevens["stap1"]["wederverkoop"] and $mustlogin and ($form->input["typeid"]<>$gegevens["stap1"]["typeid"] or $form->input["aankomstdatum"]<>$gegevens["stap1"]["aankomstdatum"])) {
				$db->query("DELETE FROM boeking_tarief WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."'");
				if($mustlogin) {
					$temp_typeid=$form->input["typeid"];
				} else {
					$temp_typeid=$gegevens["stap1"]["typeid"];
				}
				$db->query("SELECT tp.prijs, tp.personen, s.seizoen_id FROM tarief_personen tp, seizoen s WHERE tp.type_id='".addslashes($temp_typeid)."' AND tp.seizoen_id=s.seizoen_id AND s.type='".$vars["seizoentype"]."' AND tp.week='".addslashes($form->input["aankomstdatum"])."';");
				while($db->next_record()) {
					$tarief=$db->f("prijs");
					$aanbieding=aanbiedinginfo($temp_typeid,$db->f("seizoen_id"),$form->input["aankomstdatum"]);
					if($aanbieding["typeid_sort"][$temp_typeid]["bedrag"][$form->input["aankomstdatum"]]) {
						$tarief=verwerk_aanbieding($tarief,$aanbieding["typeid_sort"][$temp_typeid],$form->input["aankomstdatum"]);
					}
					$db2->query("INSERT INTO boeking_tarief SET verkoop='".addslashes($tarief)."', aantalpersonen='".$db->f("personen")."', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."'");
				}
			}

			# Korting op basis van kortingscode opslaan
			if($temp_kortingscode["save_kortingscode_id"]) {

				# Oude opgeslagen korting wissen
				$db2->query("DELETE FROM extra_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND kortingscode=1;");

				# Korting bepalen
				if($temp_kortingscode["save_korting_euro"]) {
					$temp_kortingscode["save_korting_bedrag"]=$temp_kortingscode["save_korting_euro"];
				} elseif($temp_kortingscode["save_korting_percentage"]) {
					$temp_gegevens2=get_boekinginfo($gegevens["stap1"]["boekingid"]);
					$temp_kortingscode["save_korting_bedrag"]=$temp_gegevens2["fin"]["accommodatie_totaalprijs"]*($temp_kortingscode["save_korting_percentage"]/100);
					if($temp_kortingscode["save_korting_maximaal"] and $temp_kortingscode["save_korting_bedrag"]>$temp_kortingscode["save_korting_maximaal"]) {
						$temp_kortingscode["save_korting_bedrag"]=$temp_kortingscode["save_korting_maximaal"];
					}
				}

				if($temp_kortingscode["save_korting_bedrag"]) {
					# Bedrag negatief maken
					$temp_kortingscode["save_korting_bedrag"]=0-$temp_kortingscode["save_korting_bedrag"];

					if($temp_kortingscode["save_actietekst"]) {
						$save_actietekst=$temp_kortingscode["save_actietekst"];
					} else {
						$save_actietekst=txt("kortingscode_extraoptienaam","boeken")." ".$_SESSION["boeking"]["kortingscode"];
					}

					# Korting opslaan
					$db2->query("INSERT INTO extra_optie SET soort='".addslashes(txt("kortingscode_extraoptiesoort","boeken"))."', naam='".addslashes($save_actietekst)."', persoonnummer='alg', verkoop='".addslashes($temp_kortingscode["save_korting_bedrag"])."', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', kortingscode=1;");
				} elseif($temp_kortingscode["save_actietekst"]) {
					# Actietekst opslaan
					$db2->query("INSERT INTO extra_optie SET soort='".addslashes(txt("kortingscode_extraoptiesoort_actie","boeken"))."', naam='".addslashes($temp_kortingscode["save_actietekst"])."', persoonnummer='alg', verkoop='0.00', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', kortingscode=1;");
				}

				# eenmalige kortingscode: status wijzigen
				if($temp_kortingscode["save_status_eenmalig"]) {
					$db2->query("UPDATE kortingscode_eenmalig SET status='2', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' WHERE kortingscode_eenmalig_id='".addslashes($temp_kortingscode["save_status_eenmalig"])."';");
				}
			}

			# Bijkomende kosten bepalen
			bereken_bijkomendekosten($gegevens["stap1"]["boekingid"]);

		} elseif($_GET["stap"]==2) {
			# Cookie plaatsen
			nawcookie($form->input["voornaam"],$form->input["tussenvoegsel"],$form->input["achternaam"],$form->input["adres"],$form->input["postcode"],$form->input["plaats"],$form->input["land"],$form->input["telefoonnummer"],$form->input["mobielwerk"],$form->input["email"],$form->input["geboortedatum"]["unixtime"],$form->input["nieuwsbrief"],$form->input["geslacht"]);


			# aan_leverancier_doorgegeven_naam opslaan
			if(isset($form->input["aan_leverancier_doorgegeven_naam"])) {
				$db->query("UPDATE boeking SET aan_leverancier_doorgegeven_naam='".addslashes($form->input["aan_leverancier_doorgegeven_naam"])."' WHERE boeking_id='".intval($gegevens["stap1"]["boekingid"])."';");
			}

			if($mustlogin) {
				# kijken of aan_leverancier_doorgegeven_naam is gewijzigd. Zo ja: loggen
				if($gegevens["stap1"]["aan_leverancier_doorgegeven_naam"]<>$form->input["aan_leverancier_doorgegeven_naam"]) {
					chalet_log("aan leverancier doorgegeven naam gewijzigd in \"".$form->input["aan_leverancier_doorgegeven_naam"]."\"");
				}
			}


			# Opties die niet meer gelden na wijzigen geboortedatum wissen
			if($opties_nietbeschikbaar_geboortedatum_id and $form->input["wisopties_nietbeschikbaar"]) {
				$db->query("DELETE FROM boeking_optie WHERE optie_onderdeel_id IN (".addslashes($opties_nietbeschikbaar_geboortedatum_id).") AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer=1;");
				$db->query("UPDATE boeking SET gewijzigd='".addslashes(trim($gegevens["stap1"]["gewijzigd"]."\nOPTIES_NA_GEBOORTEDATUM"))."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				chalet_log("optie(s) gewist i.v.m. wijzigen geboortedatum hoofdboeker");
			}

			# Gegevens opslaan in boeking_persoon (met "persoonnummer=1")
			$setquery="voornaam='".addslashes($form->input["voornaam"])."', tussenvoegsel='".addslashes($form->input["tussenvoegsel"])."', achternaam='".addslashes($form->input["achternaam"])."', adres='".addslashes($form->input["adres"])."', postcode='".addslashes($form->input["postcode"])."', plaats='".addslashes($form->input["plaats"])."', land='".addslashes($form->input["land"])."', telefoonnummer='".addslashes($form->input["telefoonnummer"])."', mobielwerk='".addslashes($form->input["mobielwerk"])."', email='".addslashes($form->input["email"])."', geboortedatum=".($form->input["geboortedatum"]["year"] ? "'".addslashes($form->input["geboortedatum"]["unixtime"])."'" : "NULL").", geslacht='".addslashes($form->input["geslacht"])."'";
			if($boeking_wijzigen or $mustlogin) {
				if($form->input["verzendmethode_reisdocumenten"]) {
					# verzendmethode_reisdocumenten opslaan
					$db->query("UPDATE boeking SET verzendmethode_reisdocumenten='".addslashes($form->input["verzendmethode_reisdocumenten"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				}
			}
			if($gegevens["stap_voltooid"][2]) {
				# E-mailadres wijzigen in tabel boekinguser
				if($mustlogin or $boeking_wijzigen) {
					if($form->input["email"] and $form->input["email"]<>$gegevens["stap2"]["email"]) {
						# Zijn er andere boekingen met dit mailadres?
						$db->query("SELECT DISTINCT b.boeking_id FROM boeking b, boeking_persoon bp WHERE bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND bp.email='".addslashes($gegevens["stap2"]["email"])."';");
						if($db->num_rows()>1) {
							$db->query("SELECT password, password_uc FROM boekinguser WHERE user='".addslashes($gegevens["stap2"]["email"])."';");
							if($db->next_record()) {
								$db->query("SELECT user_id FROM boekinguser WHERE user='".addslashes($form->input["email"])."';");
								if(!$db->num_rows()) {
									$db->query("SELECT password, password_uc FROM boekinguser WHERE user='".addslashes($gegevens["stap2"]["email"])."';");
									if($db->next_record()) {
										$password=$db->f("password");
										$db->query("INSERT INTO boekinguser SET password='".addslashes($password)."', password_uc='".addslashes($db->f("password_uc"))."', user='".addslashes($form->input["email"])."';");
									}
								}
							}
						} else {
							$db->query("SELECT user_id FROM boekinguser WHERE user='".addslashes($form->input["email"])."';");
							if(!$db->num_rows()) {
								$db->query("UPDATE boekinguser SET user='".addslashes($form->input["email"])."' WHERE user='".addslashes($gegevens["stap2"]["email"])."';");
							}
						}
					}
					if($form->input["wachtwoord"]) {
						if($mustlogin) {
							if($form->input["email"]) {
								$mailadres=$form->input["email"];
							} else {
								$mailadres=$gegevens["stap2"]["email"];
							}
							$db->query("UPDATE boekinguser SET password='".addslashes(md5($form->input["wachtwoord"]))."', password_uc='".addslashes($form->input["wachtwoord"])."' WHERE user='".addslashes($mailadres)."';");
						} else {
							$db->query("UPDATE boekinguser SET password='".addslashes(md5($form->input["wachtwoord"]))."', password_uc='".addslashes($form->input["wachtwoord"])."' WHERE user_id='".addslashes($login->user_id)."';");
						}
					}
				}
				$db->query("UPDATE boeking_persoon SET status='".addslashes($persoonstatus)."', ".$setquery." WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer=1;");
			} else {
				$setquery.=", status='".$persoonstatus."', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', persoonnummer=1";
				$db->query("INSERT INTO boeking_persoon SET ".$setquery.";");
			}

			# Bijkomende kosten bepalen
			bereken_bijkomendekosten($gegevens["stap1"]["boekingid"]);

		} elseif($_GET["stap"]==3) {

			# Opties die niet meer gelden na wijzigen geboortedatum wissen
			if(is_array($opties_nietbeschikbaar_geboortedatum_id) and $form->input["wisopties_nietbeschikbaar"]) {
				while(list($key,$value)=each($opties_nietbeschikbaar_geboortedatum_id)) {
					$db->query("DELETE FROM boeking_optie WHERE optie_onderdeel_id IN (".addslashes($value).") AND boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer='".addslashes($key)."';");
					$db->query("UPDATE boeking SET gewijzigd='".addslashes(trim($gegevens["stap1"]["gewijzigd"]."\nOPTIES_NA_GEBOORTEDATUM"))."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
					chalet_log("optie(s) gewist i.v.m. wijzigen geboortedatum(s)");
				}
			}

			# Eerst alle oude gegevens (status=2) wissen
			$db->query("DELETE FROM boeking_persoon WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer>1 AND status=2;");
			if($persoonstatus==1) {
				$db->query("DELETE FROM boeking_persoon WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer>1 AND status=1;");
			}

			# boeking_persoon opslaan
			for($i=2;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
				# kijken of er zaken zijn gewijzigd
				if(($gegevens["stap3"][$i]["voornaam"] and $form->input["voornaam".$i]<>$gegevens["stap3"][$i]["voornaam"]) or ($gegevens["stap3"][$i]["tussenvoegsel"] and $form->input["tussenvoegsel".$i]<>$gegevens["stap3"][$i]["tussenvoegsel"]) or ($gegevens["stap3"][$i]["achternaam"] and $form->input["achternaam".$i]<>$gegevens["stap3"][$i]["achternaam"]) or ($gegevens["stap3"][$i]["plaats"] and $form->input["plaats".$i]<>$gegevens["stap3"][$i]["plaats"]) or ($gegevens["stap3"][$i]["geslacht"] and $form->input["geslacht".$i]<>$gegevens["stap3"][$i]["geslacht"]) or (isset($gegevens["stap3"][$i]["geboortedatum"]) and $form->input["geboortedatum".$i]["unixtime"]<>$gegevens["stap3"][$i]["geboortedatum"])) {
					$tempstatus=$persoonstatus;
				} else {
					$tempstatus=$persoonstatus;
				}
				if($gegevens["stap3"][$i]["annverz_voorheen"]) {
					$annverz_voorheen="'".addslashes($gegevens["stap3"][$i]["annverz_voorheen"])."'";
				} else {
					$annverz_voorheen="NULL";
				}
				if($gegevens["stap3"][$i]["annverz_verzekerdbedrag"]) {
					$annverz_verzekerdbedrag="'".addslashes($gegevens["stap3"][$i]["annverz_verzekerdbedrag"])."'";
				} else {
					$annverz_verzekerdbedrag="NULL";
				}
				$db->query("INSERT INTO boeking_persoon SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', persoonnummer='".addslashes($i)."', status='".addslashes($tempstatus)."', voornaam='".addslashes($form->input["voornaam".$i])."', tussenvoegsel='".addslashes($form->input["tussenvoegsel".$i])."', achternaam='".addslashes($form->input["achternaam".$i])."', plaats='".addslashes($form->input["plaats".$i])."', land='".addslashes($form->input["land".$i])."', geboortedatum=".($form->input["geboortedatum".$i]["year"] ? "'".addslashes($form->input["geboortedatum".$i]["unixtime"])."'" : "NULL").", geslacht='".addslashes($form->input["geslacht".$i])."', annverz='".addslashes($gegevens["stap3"][$i]["annverz"])."', annverz_voorheen=".$annverz_voorheen.", annverz_verzekerdbedrag=".$annverz_verzekerdbedrag.";");
			}

			# Bijkomende kosten bepalen
			bereken_bijkomendekosten($gegevens["stap1"]["boekingid"]);

		} elseif($_GET["stap"]==4) {

			# opmerkingen_opties en wijzigingen opslaan
			if($boeking_wijzigen and $gegevens["stap1"]["gezien"]) {
				if($form->input["opmerkingen_opties"]) {
					$gegevens["stap1"]["gewijzigd"].="\nOPM_OPTIES";
				}
				if(!$form->input["schadeverzekering"]) $form->input["schadeverzekering"]=0;
				if($schadeverzekering_checkbox_getoond and $form->input["schadeverzekering"]<>$gegevens["stap1"]["schadeverzekering"]) {
					$gegevens["stap1"]["gewijzigd"].="\nSCHADEVERZEKERING";
				}
			}


			if($form->input["opmerkingen_opties"]) {
				if($boeking_wijzigen) {
					if($gegevens["stap1"]["opmerkingen_opties"]) {
						$gegevens["stap1"]["opmerkingen_opties"].="\n\n";
					}
					$gegevens["stap1"]["opmerkingen_opties"].="Aanvulling ".date("d-m-Y, H:i")." uur:\n";
					$gegevens["stap1"]["opmerkingen_opties"].=$form->input["opmerkingen_opties"];
				} else {
					$gegevens["stap1"]["opmerkingen_opties"]=$form->input["opmerkingen_opties"];
				}
			}
			$gegevens["stap1"]["gewijzigd"]=trim($gegevens["stap1"]["gewijzigd"]);
			if($mustlogin) {
				# Kijken of mailverstuurd_persoonsgegevens_dagenvoorvertrek is gewijzigd. Zo ja: mailverstuurd_persoonsgegevens op NULL zetten (zodat mailtje opnieuw verstuurd wordt)
				if($form->input["mailverstuurd_persoonsgegevens_dagenvoorvertrek"] and $gegevens["stap1"]["mailverstuurd_persoonsgegevens_dagenvoorvertrek"]<>$form->input["mailverstuurd_persoonsgegevens_dagenvoorvertrek"]) {
					$db->query("UPDATE boeking SET mailverstuurd_persoonsgegevens=NULL WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				}
			}
			$db->query("UPDATE boeking SET ".($mustlogin ? "wijzigen_dagen='".addslashes($form->input["wijzigen_dagen"])."', mailverstuurd_persoonsgegevens_dagenvoorvertrek='".addslashes($form->input["mailverstuurd_persoonsgegevens_dagenvoorvertrek"])."', opmerkingen_voucher='".addslashes($form->input["opmerkingen_voucher"])."', opmerkingen_klant='".addslashes($form->input["opmerkingen_klant"])."', " : "")."opmerkingen_opties='".addslashes($gegevens["stap1"]["opmerkingen_opties"])."', ".($schadeverzekering_checkbox_getoond ? "schadeverzekering='".addslashes(($form->input["schadeverzekering"] ? "1" : "0"))."'," : "")." gewijzigd='".addslashes(trim($gegevens["stap1"]["gewijzigd"]))."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

			# boeking_optie wissen (status=2)
			$db->query("DELETE FROM boeking_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND status=2;");
			if($status==1) {
				$db->query("DELETE FROM boeking_optie WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND status=1;");
			}

			# per persoonnummer bekijken of er opties gewijzigd zijn
			unset($tempstatus);
			@reset($form->input);
			while(list($key,$value)=@each($form->input)) {
				# reg1 = optie_soort_id
				# reg2 = persoonnummer
				if(ereg("^optie_([0-9]+)_([0-9]+)$",$key,$regs)) {
					if(($gegevens["stap4"]["opties"][$regs[2]][$regs[1]] and !$value) or (!$gegevens["stap4"]["opties"][$regs[2]][$regs[1]] and $value>0)) {
						$opties_gewijzigd[$regs[2]]=true;
					}
				}
			}

			# Indien er geen opties zijn gewijzigd, mag alles met status=1 worden opgeslagen
#			if($status==2 and !isset($opties_gewijzigd)) {
#				$temp_status=1;
#			} else {
#				$temp_status=$status;
#			}

			$temp_status=$status;

			# boeking_optie opslaan
			@reset($form->input);
			$reisverzekering_poliskosten=0;
			while(list($key,$value)=@each($form->input)) {
				# reg1 = optie_soort_id
				# reg2 = persoonnummer
				if(ereg("^optie_([0-9]+)_([0-9]+)$",$key,$regs)) {
					if($value>0) {
						if($gegevens["stap4"]["opties"][$regs[2]][$regs[1]] and $gegevens["stap4"]["opties"][$regs[2]][$regs[1]]==$value) {
							$verkoop=$gegevens["stap4"]["optie_onderdeelid_verkoop_persoonnummer"][$value][$regs[2]];
						} else {
							if(is_array($gegevens["stap4"]["optie_onderdeelid_verkoop_persoonnummer"][$value])) {
								$verkoop=max($gegevens["stap4"]["optie_onderdeelid_verkoop_persoonnummer"][$value]);
							} else {
								$verkoop=$optie_onderdeel[$regs[1]][$value]["verkoop"];
							}
						}
						$db->query("INSERT INTO boeking_optie SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', optie_onderdeel_id='".addslashes($value)."', persoonnummer='".addslashes($regs[2])."', status='".addslashes($temp_status)."', verkoop='".addslashes($verkoop)."'".($gegevens["stap1"]["reisbureau_user_id"] ? ", commissie='".addslashes($optie_onderdeel[$regs[1]][$value]["commissie"])."' " : "").";");
					}
				} elseif(ereg("^annverz_([0-9]+)$",$key,$regs)) {
					if($vars["annverzekering_mogelijk"] and (!$boeking_wijzigen or $gegevens["stap1"]["annuleringsverzekering_wijzigen_toegestaan"])) {

						# Annuleringsverzekering opslaan
						if(isset($gegevens["stap3"][$regs[1]]["voornaam"])) {
							$db->query("UPDATE boeking_persoon SET annverz='".addslashes($value)."'".($boeking_wijzigen && $gegevens["stap3"][$regs[1]]["annverz"]<>"" && $gegevens["stap3"][$regs[1]]["annverz"]<>$value ? ", annverz_voorheen='".addslashes($gegevens["stap3"][$regs[1]]["annverz"])."'" : "")." WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer='".addslashes($regs[1])."';");
						} else {
							$db->query("INSERT INTO boeking_persoon SET annverz='".addslashes($value)."', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', persoonnummer='".addslashes($regs[1])."', status=2;");
						}

						$temp_gegevens2=get_boekinginfo($gegevens["stap1"]["boekingid"]);

						# Verzekerd bedrag opslaan
						if(($gegevens["stap1"]["annuleringsverzekering_wijzigen_toegestaan"] and $value) or ((!$mustlogin and !$boeking_wijzigen) or ($value and !$gegevens["stap3"][$regs[1]]["annverz"] and (!$temp_gegevens2["stap4"][$regs[1]]["annverz_verzekerdbedrag"] or $temp_gegevens2["stap4"][$regs[1]]["annverz_verzekerdbedrag"]==0)))) {
							$db->query("UPDATE boeking_persoon SET annverz_verzekerdbedrag='".addslashes($temp_gegevens2["stap4"][$regs[1]]["annverz_verzekerdbedrag_actueel"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer='".addslashes($regs[1])."';");
						} elseif(!$value) {
							$db->query("UPDATE boeking_persoon SET annverz_verzekerdbedrag=NULL WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' AND persoonnummer='".addslashes($regs[1])."';");
						}
					}
				}
			}

			# Bijkomende kosten bepalen
			bereken_bijkomendekosten($gegevens["stap1"]["boekingid"]);

		} elseif($_GET["stap"]==5) {
			#
			# Boeking is bevestigd
			#

			# Tarieven opslaan in boeking_tarief
			if($accinfo["toonper"]<>3 and !$gegevens["stap1"]["wederverkoop"]) {
				$db->query("SELECT tp.prijs, tp.personen, s.seizoen_id FROM tarief_personen tp, seizoen s WHERE tp.type_id='".addslashes($gegevens["stap1"]["typeid"])."' AND tp.seizoen_id=s.seizoen_id AND s.type='".$vars["seizoentype"]."' AND tp.week='".addslashes($gegevens["stap1"]["aankomstdatum"])."';");
				while($db->next_record()) {
					$tarief=$db->f("prijs");
					$aanbieding=aanbiedinginfo($gegevens["stap1"]["typeid"],$db->f("seizoen_id"),$gegevens["stap1"]["aankomstdatum"]);
					if($aanbieding["typeid_sort"][$gegevens["stap1"]["typeid"]]["bedrag"][$gegevens["stap1"]["aankomstdatum"]]) {
						$tarief=verwerk_aanbieding($tarief,$aanbieding["typeid_sort"][$gegevens["stap1"]["typeid"]],$gegevens["stap1"]["aankomstdatum"]);
					}
					$db2->query("INSERT INTO boeking_tarief SET verkoop='".addslashes($tarief)."', aantalpersonen='".$db->f("personen")."', boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."'");
				}
			}

			# Inkoopgegevens opslaan
			$inkoop=inkoopprijs_bepalen($gegevens["stap1"]["typeid"],$gegevens["stap1"]["aankomstdatum"]);
			if($inkoop["bruto"]>0 and $inkoop["netto"]>0) {
				$setquery_inkoop.=", inkoopnetto='".addslashes($inkoop["netto"])."', inkoopbruto='".addslashes($inkoop["bruto"])."'";

				$log_inkoopprijzen="bruto-inkoop (€ ".number_format($inkoop["bruto"],2,",",".")."), netto-inkoop (€ ".number_format($inkoop["netto"],2,",",".").")";

				if($inkoop["inkoopcommissie"]<>0) {
					$setquery_inkoop.=", inkoopcommissie='".addslashes($inkoop["inkoopcommissie"])."'";
					$log_inkoopprijzen.=", commissie (".number_format($inkoop["inkoopcommissie"],2,",",".")."%)";
				}
				if($inkoop["inkooptoeslag"]<>0) {
					$setquery_inkoop.=", inkooptoeslag='".addslashes($inkoop["inkooptoeslag"])."'";
					$log_inkoopprijzen.=", toeslag (€ ".number_format($inkoop["inkooptoeslag"],2,",",".").")";
				}
				if($inkoop["inkoopkorting"]<>0) {
					$setquery_inkoop.=", inkoopkorting='".addslashes($inkoop["inkoopkorting"])."'";
					$log_inkoopprijzen.=", korting (€ ".number_format($inkoop["inkoopkorting"],2,",",".").")";
				}
				if($inkoop["inkoopkorting_percentage"]<>0) {
					$setquery_inkoop.=", inkoopkorting_percentage='".addslashes($inkoop["inkoopkorting_percentage"])."'";
					$log_inkoopprijzen.=", korting (".number_format($inkoop["inkoopkorting_percentage"],2,",",".")."%)";
				}
				if($inkoop["inkoopkorting_euro"]<>0) {
					$setquery_inkoop.=", inkoopkorting_euro='".addslashes($inkoop["inkoopkorting_euro"])."'";
					$log_inkoopprijzen.=", korting (€ ".number_format($inkoop["inkoopkorting_euro"],2,",",".").")";
				}
				$log_inkoopprijzen.=" overgenomen van tarieventabel";
			}

			# Commissie voor reisbureau's bepalen
			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$gegevens["stap1"]["commissie"]=$gegevens["stap1"]["accinfo"]["commissie"]+$gegevens["stap1"]["reisbureau_aanpassing_commissie"];
			}

			# Gegevens opslaan
			$db->query("UPDATE boeking SET verkoop='".addslashes($gegevens["stap1"]["verkoop"])."', accprijs='".addslashes($gegevens["stap1"]["accprijs"])."', ".($gegevens["stap1"]["reisbureau_user_id"] ? "commissie='".addslashes($gegevens["stap1"]["commissie"])."', " : "")."opmerkingen_boeker='".addslashes($form->input["opmerkingen_boeker"])."', ".($form->input["referentiekeuze"] ? "referentiekeuze='".addslashes($form->input["referentiekeuze"])."', " : "").($form->input["verzendmethode_reisdocumenten"] ? "verzendmethode_reisdocumenten='".addslashes($form->input["verzendmethode_reisdocumenten"])."', " : "")."bevestigdatum=NOW()".$setquery_inkoop." WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");

			# E-mailadres en wachtwoord opslaan in boekinguser
			$db->query("SELECT user_id, password_uc FROM boekinguser WHERE user='".addslashes($gegevens["stap2"]["email"])."';");
			if($db->next_record()) {
				$directlogin_user_id=$db->f("user_id");
				if($db->f("password_uc")) {
					$directlogin_wachtwoord=$db->f("password_uc");
					chalet_log("gebruikersnaam ".$gegevens["stap2"]["email"]." en wachtwoord (".$db->f("password_uc").") bestaan al");
				} else {
					$directlogin_wachtwoord=wt_generate_password(6,false);
					$db->query("UPDATE boekinguser SET password='".addslashes(md5($directlogin_wachtwoord))."', password_uc='".addslashes($directlogin_wachtwoord)."' WHERE user_id='".addslashes($db->f("user_id"))."';");
					chalet_log("gebruikersnaam ".$gegevens["stap2"]["email"]." bestaat al. Nieuw wachtwoord (".$directlogin_wachtwoord.") aangemaakt");
				}
			} else {
				if(!$gegevens["stap1"]["reisbureau_user_id"]) {
					$directlogin_wachtwoord=wt_generate_password(6,false);
					$db->query("INSERT INTO boekinguser SET user='".addslashes($gegevens["stap2"]["email"])."', password='".addslashes(md5($directlogin_wachtwoord))."', password_uc='".addslashes($directlogin_wachtwoord)."';");
					$directlogin_user_id=$db->insert_id();
					chalet_log("gebruikersnaam ".$gegevens["stap2"]["email"]." en wachtwoord (".$directlogin_wachtwoord.") aangemaakt");
				}
			}

			$directlogin = new directlogin;
			$directlogin_link=$directlogin->maak_link($vars["website"],1,$directlogin_user_id,md5($directlogin_wachtwoord));

			$inlogtext="";
			$inlogtext.=html("viaadreskuntuinloggen","boeken",array("h_1"=>"<a href=\"".wt_he($directlogin_link)."\">","h_2"=>"</a>"));

			# Button
#			$inlogtext.="<p><center><table class=\"table\"><tr><td width=\"200\" height=\"30\" bgcolor=\"".$table."\" style=\"-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; color: ".$thfontcolor."; display: block; text-align: center;\"><a href=\"".wt_he($directlogin_link)."\" style=\"color: ".$thfontcolor."; font-size:11px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; text-decoration: none; line-height:30px; width:100%; display:inline-block\">".html("directinloggen","boeken")."</a></td></tr></table></center></p>";

			if($directlogin_wachtwoord) {
				$inlogtext.=" ".html("gebruikdaarbijhetvolgendewachtwoord","boeken")." <strong>".htmlentities($directlogin_wachtwoord)."</strong>";
			}


			# Status optieaanvraag wijzigen indien gekoppeld aan optieaanvraag
			if($gegevens["stap1"]["optieaanvraag_id"]) {
				$db->query("UPDATE optieaanvraag SET status='6' WHERE optieaanvraag_id='".addslashes($gegevens["stap1"]["optieaanvraag_id"])."';");
			}

			# Inschrijven nieuwsbrief
			if($form->input["nieuwsbrief"] and $form->input["nieuwsbrief"]<>"3") {
				$nieuwsbrief_waardes=array("email"=>$gegevens["stap2"]["email"],"voornaam"=>$gegevens["stap2"]["voornaam"],"tussenvoegsel"=>$gegevens["stap2"]["tussenvoegsel"],"achternaam"=>$gegevens["stap2"]["achternaam"],"per_wanneer"=>$form->input["nieuwsbrief"]);
				nieuwsbrief_inschrijven($vars["seizoentype"],$nieuwsbrief_waardes);
			}

			setcookie("naw[nieuwsbrief]",($form->input["nieuwsbrief"] ? "ja" : "nee"),time()+12960000);


			# Tabellen met boekingsinformatie opstellen
			$tabellen="";
			$tabellen.="<table cellspacing=\"0\" cellpadding=\"3\" style=\"background-color: #FFFFFF;width: 630px;font-family: ".$font.";font-size: 1.0em;border:solid ".$table." 1px;\">";
			$tabellen.="<tr><td colspan=\"2\" style=\"font-weight: bold;background-color: ".$table.";color:".$thfontcolor.";border:solid ".$table." 1px\">".html("algemenegegevens","boeken")."</td></tr>";
			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("reisbureau","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap1"]["reisbureau_naam"]." - ".$gegevens["stap1"]["reisbureau_usernaam"]."</td></tr>";
			}

			$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("aanvraagnummer","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap1"]["boekingid"]."</td></tr>";
			$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("accommodatie","boeken")."</td><td style=\"border:solid ".$table." 1px\"><a href=\"".$accinfo["url"]."\">".ucfirst($accinfo["soortaccommodatie"])." ".htmlentities($accinfo["naam_ap"])."</a></td></tr>";
			$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("plaats","boeken")."</td><td style=\"border:solid ".$table." 1px\"><a href=\"".$accinfo["plaats_url"]."\">".htmlentities($accinfo["plaats"].", ".$accinfo["land"])."</a></td></tr>";
			$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("aantalpersonen","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($gegevens["stap1"]["aantalpersonen"])."</td></tr>";
			if($gegevens["stap1"]["flexibel"] or $gegevens["stap1"]["verblijfsduur"]>1) {
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("verblijfsperiode","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities(DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$vars["taal"])." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum_exact"],$vars["taal"]))."</td></tr>";
			} else {
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("verblijfsperiode","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($accinfo["aankomstdatum"][$gegevens["stap1"]["aankomstdatum"]]." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum"],$vars["taal"]))."</td></tr>";
			}
			if($gegevens["stap1"]["opmerkingen_opties"]) $tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px;vertical-align:top;\">".html("vragenofopmerkingenopties","boeken")."</td><td style=\"border:solid ".$table." 1px\">".nl2br(htmlentities($gegevens["stap1"]["opmerkingen_opties"]))."</td></tr>";
			if($form->input["opmerkingen_boeker"]) $tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px;vertical-align:top;\">".html("vragenofopmerkingen","boeken")."</td><td style=\"border:solid ".$table." 1px\">".nl2br(htmlentities($form->input["opmerkingen_boeker"]))."</td></tr>";

			$tabellen.="</table><p>";

			# Samenstelling reissom
			$tabellen.="<table cellspacing=\"0\" cellpadding=\"3\" style=\"background-color: #FFFFFF;width: 630px;font-family: ".$font.";font-size: 1.0em;border:solid ".$table." 2px;\">";
			$tabellen.="<tr><td colspan=\"8\" style=\"font-weight: bold;background-color: ".$table.";color:".$thfontcolor.";border:solid ".$table." 1px\">".html("samenstellingreissom","boeken")."</td></tr>";
			$tabellen.=$reissomtabel;
			$tabellen.="</table><p>";

			if($gegevens["stap1"]["reisbureau_user_id"]) {
				# Factuurgegevens (alleen tonen als gekoppeld aan reisbureau-user)
				$tabellen.="<table cellspacing=\"0\" cellpadding=\"3\" style=\"background-color: #FFFFFF;width: 630px;font-family: ".$font.";font-size: 1.0em;border:solid ".$table." 2px;\">";
				$tabellen.="<tr><td colspan=\"8\" style=\"font-weight: bold;background-color: ".$table.";color:".$thfontcolor.";border:solid ".$table." 1px\">".html("factuurgegevens","boeken")."</td></tr>";
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("reisbureau","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($gegevens["stap1"]["reisbureau_naam"])."&nbsp;</td></tr>";
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("adres","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($gegevens["stap1"]["reisbureau_adres"])."&nbsp;</td></tr>";
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("postcode","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($gegevens["stap1"]["reisbureau_postcode"])."&nbsp;</td></tr>";
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("plaats","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($gegevens["stap1"]["reisbureau_plaats"])."&nbsp;</td></tr>";
				$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("land","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities($gegevens["stap1"]["reisbureau_land"])."&nbsp;</td></tr>";
				$tabellen.="</table><p>";
			}

			for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
				if($gegevens["stap3"][$i]["voornaam"] or $gegevens["stap3"][$i]["tussenvoegsel"] or $gegevens["stap3"][$i]["achternaam"] or $gegevens["stap3"][$i]["plaats"] or $gegevens["stap3"][$i]["geslacht"] or $gegevens["stap3"][$i]["geboortedatum"] or is_array($gegevens["stap4"][$i]["opties_perpersoon"]) or $gegevens["stap3"][$i]["annverz"]) {
					$tabellen.="<table cellspacing=\"0\" cellpadding=\"3\" style=\"background-color: #FFFFFF;width: 630px;font-family: ".$font.";font-size: 1.0em;border:solid ".$table." 1px;\">";
					$tabellen.="<tr><td colspan=\"2\" style=\"font-weight: bold;background-color: ".$table.";color:".$thfontcolor.";border:solid ".$table." 1px\">".($i==1 ? html("gegevenshoofdboeker","boeken") : html("gegevenspersoon","boeken")." ".$i)."</td></tr>";
					$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("naam","boeken")."</td><td style=\"border:solid ".$table." 1px\">".htmlentities(wt_naam($gegevens["stap3"][$i]["voornaam"],$gegevens["stap3"][$i]["tussenvoegsel"],$gegevens["stap3"][$i]["achternaam"]))."&nbsp;</td></tr>";
					$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("geslacht","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$vars["geslacht"][$gegevens["stap3"][$i]["geslacht"]]."&nbsp;</td></tr>";
					$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("geboortedatum","boeken")."</td><td style=\"border:solid ".$table." 1px\">".(isset($gegevens["stap3"][$i]["geboortedatum"]) ? DATUM("D MAAND JJJJ",$gegevens["stap3"][$i]["geboortedatum"],$vars["taal"]) : "&nbsp;")."</td></tr>";
					if($i==1) {
						if(!$gegevens["stap1"]["reisbureau_user_id"]) {
							$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("adres","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap2"]["adres"]."</td></tr>";
							$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("postcode","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap2"]["postcode"]."</td></tr>";
						}
						$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("woonplaats","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap2"]["plaats"]."</td></tr>";
						$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("land","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap2"]["land"]."</td></tr>";
						$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("telefoonnummer","boeken")."</td><td style=\"border:solid ".$table." 1px\">".($gegevens["stap2"]["telefoonnummer"] ? $gegevens["stap2"]["telefoonnummer"] : "&nbsp;")."</td></tr>";
						if($gegevens["stap2"]["mobielwerk"]) $tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("mobielwerk","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap2"]["mobielwerk"]."</td></tr>";
						if(!$gegevens["stap1"]["reisbureau_user_id"]) {
							$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("email","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap2"]["email"]."</td></tr>";
						}
					} else {
						$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px\">".html("woonplaats","boeken")."</td><td style=\"border:solid ".$table." 1px\">".$gegevens["stap3"][$i]["plaats"]."&nbsp;</td></tr>";
					}
					if(is_array($gegevens["stap4"][$i]["opties_perpersoon"]) or $gegevens["stap3"][$i]["annverz"]) {
						$tabellen.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px;vertical-align:top;\">".html("opties","boeken")."</td><td style=\"border:solid ".$table." 1px\">";
						@reset($gegevens["stap4"][$i]["opties_perpersoon"]);
						$tabellen.="<table class=\"table\" style=\"font-family: ".$font.";font-size: 1.0em;width:450px;border:0;\">";
						while(list($key,$value)=@each($gegevens["stap4"][$i]["opties_perpersoon"])) {
							$tabellen.="<tr><td style=\"vertical-align:top;\">".htmlentities($value)."&nbsp;</td><td style=\"vertical-align:top;\">";
							$tabellen.="&euro;&nbsp;</td><td style=\"vertical-align:top;text-align:right;\">".number_format($gegevens["stap4"][$i]["optieonderdeel_verkoop"][$key],2,',','.');
							$tabellen.="</td></tr>";
						}
						# Annuleringsverzekering
						if($gegevens["stap3"][$i]["annverz"]) {
							$tabellen.="<tr><td colspan=\"3\" style=\"vertical-align:top;\">".htmlentities($vars["annverz_soorten_kort"][$gegevens["stap3"][$i]["annverz"]])."&nbsp;</td>";
#							$tabellen.="<td style=\"vertical-align:top;\">";
#							$tabellen.="&euro;&nbsp;</td><td style=\"vertical-align:top;text-align:right;\">".number_format($gegevens["stap4"][$i]["annverz_persoon"][$key],2,',','.');
#							$tabellen.="&euro;&nbsp;</td><td style=\"vertical-align:top;text-align:right;\">".number_format($gegevens["stap4"][$i]["annverz_persoon"],2,',','.');
#							$tabellen.="</td>";
							$tabellen.="</tr>";
						}
						$tabellen.="</table></td></tr>";
					}

					$tabellen.="</table><p>";
				}
			}

			# Referer-gegevens opvragem
			$referer=getreferer($_COOKIE["sch"]);

			# Mail sturen aan referer?
			if(is_array($referer["lijst"])) {
				reset($referer["lijst"]);
				while(list($key,$value)=each($referer["lijst"])) {
					if($value["ad"] and $vars["ads_referermail"][$value["ad"]]) {

						# Maximaal 30 dagen oud
						if($value["datumtijd"]>(time()-(30*86400))) {
							$mail=new wt_mail;
							$mail->subject="Boeking via referentielink ".$vars["ads"][$value["ad"]];
							$mail->bcc="info@chalet.nl";
							$mail->from=$vars["email"];
							$mail->fromname=$vars["websitenaam"];

							if(ereg("@webtastic\.nl",$gegevens["stap2"]["email"])) {
								$mail->to="chalet_test@webtastic.nl";
							} else {
								$mail->to=$vars["ads_referermail"][$value["ad"]];
							}
							$mail->plaintext="Geboekt via ".$vars["websitenaam"]." na referentielink van ".$vars["ads"][$value["ad"]].":\n\n".$accinfo["begincode"].$accinfo["type_id"]." ".ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["naam_ap"]."\n\n";
							$mail->send();
							break;
						}
					}
				}
			}

			# Mail aan info@chalet.nl
			unset($html);
			$mail=new wt_mail;
			$mail->subject="Boeking (".$gegevens["stap1"]["boekingid"].") ".date("d/m/Y",$gegevens["stap1"]["aankomstdatum_exact"])." ".$gegevens["stap1"]["accinfo"]["plaats"]."  / ".ucfirst($gegevens["stap1"]["accinfo"]["soortaccommodatie"])." ".$gegevens["stap1"]["accinfo"]["naam_ap"];
			$mail->from="info@chalet.nl";
			$mail->fromname="Website ".$vars["websites"][$vars["website"]];

			if(ereg("@webtastic\.nl",$gegevens["stap2"]["email"])) {
				$mail->to="chalet_test@webtastic.nl";
			} else {
				$mail->to="info@chalet.nl";
			}

			$html="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\"/><style type=\"text/css\"><!--\na:visited:hover,a:hover {\ncolor:".$hover.";\n}\n--></style>\n</head>\n<body style=\"background-color: #F3F3F3;font-family: ".$font."font-size: 0.8em;\">\n";
			$html.="<div style=\"width:630px\">";
			$html.="&nbsp;<br>De volgende gegevens zijn zojuist via de website ingevoerd:<p>".$tabellen;

			if($referer["opsomming"] or $form->input["referentiekeuze"]) {
				$html.="<p><table cellspacing=\"0\" cellpadding=\"3\" style=\"background-color: #FFFFFF;width: 630px;font-family: ".$font.";font-size: 1.0em;border:solid ".$table." 1px;\">";
				$html.="<tr><td colspan=\"2\" style=\"font-weight: bold;background-color: ".$table.";color:".$thfontcolor.";border:solid ".$table." 1px\">Referentie</td></tr>";
				if($referer["opsomming"]) {
					$html.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px;vertical-align:top;\">Referentielink</td><td style=\"border:solid ".$table." 1px\">".$referer["opsomming"]."</td></tr>";
				}
				if($form->input["referentiekeuze"]) {
					$html.="<tr><td style=\"width:165px;font-weight: bold;border:solid ".$table." 1px;vertical-align:top;\">Referentiekeuze</td><td style=\"border:solid ".$table." 1px\">";
					$referentiekeuze_array=split(",",$form->input["referentiekeuze"]);
					while(list($key,$value)=each($referentiekeuze_array)) {
#						$html.=htmlentities($vars["referentiekeuze"][$form->input["referentiekeuze"]])."<br>";
						$html.=htmlentities($vars["referentiekeuze"][$value])."<br>";
					}
					$html.="</td></tr>";
				}
				$html.="</td></tr></table>";
			}

			$html.="</div></body></html>";

			$mail->html=$html;
			if(!$voorkant_cms) {
				$mail->send();
			}

			# Mail aan klant
			unset($html);
			if($voorkant_cms) {
				if($login->vars["email"]) {
					$to=$login->vars["email"];
				} else {
					$to="info@chalet.nl";
				}
			} else {
				$to=$gegevens["stap2"]["email"];
			}

#			$html="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\"/><style type=\"text/css\"><!--\na:visited:hover,a:hover {\ncolor:".$hover.";\n}\n--></style>\n</head>\n<body style=\"background-color: #F3F3F3;font-family: ".$font.";font-size: 0.8em;\">\n";
#			$html.="<div style=\"width:630px\">";
			$html.=html("beste","boeken")." ";
			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$html.=htmlentities($login_rb->vars["voornaam"]);
			} else {
				$html.=htmlentities(wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]));
			}
			$html.=",<p>".html("wijhebbenuwboekingsaanvraag","boeken")."</p>";
			$html.="<p>".html("nageblekenbeschikbaarheidsturenwiju","boeken")."</p>";

			if(!$gegevens["stap1"]["reisbureau_user_id"]) {
				$html.="<p>".$inlogtext."</p>";
			}
			$html.="<br>".$tabellen."<br/>";
			$html.="<p>".html("metvriendelijkegroet","boeken")."<br>".html("medewerkerssitenaam","boeken",array("v_websitenaam"=>$vars["websitenaam"]))."<P>".$vars["langewebsitenaam"]."<br>Wipmolenlaan 3<br>3447 GJ Woerden<br>".($vars["websiteland"]<>"nl" ? html("nederland","contact")."<br>" : "").html("telefoonnummer_chalet","contact")."<br>".html("fax_chalet","contact")."<br>".html("email_kort","contact").": <a href=\"mailto:".$vars["email"]."\">".$vars["email"]."</a></p><br>&nbsp;";

#			$html.="</div></body></html>";

			// $mail->html=$html;
			// $mail->send();

			// opmaakmail sturen
			verstuur_opmaakmail($vars["website"],$to,"",html("boeking","boeken")." ".$vars["websitenaam"],$html,array(""));


if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	// exit;
}

		}

		# Log bepalen
		if($_GET["stap"]==1) {
			if($form->input["typeid"] and $gegevens["stap1"]["typeid"]<>$form->input["typeid"]) chalet_log("andere accommodatie: van \"".$accinfo["begincode"].$accinfo["type_id"]." ".$accinfo["naam_ap"]."\" naar \"".$nieuw_accinfo["begincode"].$nieuw_accinfo["type_id"]." ".$nieuw_accinfo["naam_ap"]."\"");
			if($form->input["goedgekeurd"] and !$gegevens["stap1"]["goedgekeurd"]) {
				chalet_log("accommodatie is definitief geboekt".($form->input["reserveringsnummer"] ? ": ".$form->input["reserveringsnummer"] : ""));
			}
			if($mustlogin and !$form->input["goedgekeurd"] and $gegevens["stap1"]["goedgekeurd"]) chalet_log("accommodatie weer teruggezet naar \"aangevraagd\"");
			if($form->input["geannuleerd"] and !$gegevens["stap1"]["geannuleerd"]) chalet_log("boeking is geannuleerd");
			if($mustlogin and !$form->input["geannuleerd"] and $gegevens["stap1"]["geannuleerd"]) chalet_log("boeking van geannuleerd naar weer actief");
			if($form->input["aantalpersonen"] and $gegevens["stap1"]["aantalpersonen"]<>$form->input["aantalpersonen"]) chalet_log("aantal personen (".$form->input["aantalpersonen"].")");
			if($form->input["aankomstdatum"] and $gegevens["stap1"]["aankomstdatum"]<>$form->input["aankomstdatum"]) chalet_log("aankomstdatum (".$accinfo["aankomstdatum"][$form->input["aankomstdatum"]].")");
			if($mustlogin and $form->input["opmerkingen_intern"]<>$gegevens["stap1"]["opmerkingen_intern"]) chalet_log("opmerkingen intern",true,true);
			if($form->input["opmerkingen_vertreklijst"]<>$gegevens["stap1"]["opmerkingen_vertreklijst"]) chalet_log("opmerkingen vertreklijst",true,true);
			if($form->input["aankomstdatum_exact"]["unixtime"] and $form->input["aankomstdatum_exact"]["unixtime"]<>$gegevens["stap1"]["aankomstdatum_exact"]) chalet_log("exacte aankomstdatum (van ".date("d-m-Y",$gegevens["stap1"]["aankomstdatum_exact"])." naar ".date("d-m-Y",$form->input["aankomstdatum_exact"]["unixtime"]).")",true,true);
			if($form->input["vertrekdatum_exact"]["unixtime"] and $form->input["vertrekdatum_exact"]["unixtime"]<>$gegevens["stap1"]["vertrekdatum_exact"]) chalet_log("exacte vertrekdatum (van ".date("d-m-Y",$gegevens["stap1"]["vertrekdatum_exact"])." naar ".date("d-m-Y",$form->input["vertrekdatum_exact"]["unixtime"]).")",true,true);
			if($form->input["kortingscode"]) chalet_log("kortingscode: ".strtoupper($form->input["kortingscode"]));

			if($mustlogin and intval($form->input["tonen_in_mijn_boeking"])<>intval($gegevens["stap1"]["tonen_in_mijn_boeking"])) {
				if($form->input["tonen_in_mijn_boeking"]) chalet_log("aangezet: deze boeking is voor de klant zichtbaar in \"Mijn boeking\"");
				if(!$form->input["tonen_in_mijn_boeking"]) chalet_log("uitgezet: deze boeking is voor de klant zichtbaar in \"Mijn boeking\"");
			}
			if($mustlogin and intval($form->input["vervallen_aanvraag"])<>intval($gegevens["stap1"]["vervallen_aanvraag"])) {
				if($form->input["vervallen_aanvraag"]) chalet_log("aangezet: dit is een vervallen aanvraag");
				if(!$form->input["vervallen_aanvraag"]) chalet_log("uitgezet: dit is een vervallen aanvraag");
			}


			if($mustlogin) {
				if($form->input["beheerderid"]<>$gegevens["stap1"]["beheerderid"]) chalet_log("beheerder (van ".($gegevens["stap1"]["beheerderid"] ? $alle_beheerders[$gegevens["stap1"]["beheerderid"]] : "-leeg-")." naar ".($form->input["beheerderid"] ? $alle_beheerders[$form->input["beheerderid"]] : "-leeg-").")",true,true);
				if($form->input["eigenaarid"]<>$gegevens["stap1"]["eigenaarid"]) chalet_log("eigenaar (van ".($gegevens["stap1"]["eigenaarid"] ? $alle_leveranciers[$gegevens["stap1"]["eigenaarid"]] : "-leeg-")." naar ".($form->input["eigenaarid"] ? $alle_leveranciers[$form->input["eigenaarid"]] : "-leeg-").")",true,true);

				if($form->input["bestelstatus_wissen"]) chalet_log("besteldatum gewist en bestelstatus op 'nog niet besteld' gezet",true,true);
			}

			if(isset($form->input["verzameltype_gekozentype_id"]) and $form->input["verzameltype_gekozentype_id"]<>$gegevens["stap1"]["verzameltype_gekozentype_id"]) chalet_log("gekozen onderliggend type (van ".($gegevens["stap1"]["verzameltype_gekozentype_id"] ? $verzameltype_gekozentype_keuzes[$gegevens["stap1"]["verzameltype_gekozentype_id"]] : "-leeg-")." naar ".($form->input["verzameltype_gekozentype_id"] ? $verzameltype_gekozentype_keuzes[$form->input["verzameltype_gekozentype_id"]] : "-leeg-").")",true,true);

			if($form->input["reisbureauuserid"]<>$gegevens["stap1"]["reisbureau_user_id"]) chalet_log("reisbureau (".$temp_reisbureauuser[$form->input["reisbureauuserid"]].")",true,true);

			if($form->input["reisbureauuserid"]) {
				if(intval($gegevens["stap1"]["btw_over_commissie"])<>intval($form->input["btw_over_commissie"])) {
					if($form->input["btw_over_commissie"]) {
						chalet_log("aangezet: BTW over commissie rekenen");
					} else {
						chalet_log("uitgezet: BTW over commissie rekenen");
					}
				}
			}

			# Garantie-gegevens loggen
			if($log_garantie) chalet_log($log_garantie_tekst);

			# Inkoopprijs-gegevens loggen
			if($log_inkoopprijzen) chalet_log($log_inkoopprijzen);

			if($verzameltype_gekozentype_id) {
				chalet_log("gekozen onderliggend type: ".$verzameltypes_gekoppeld[$verzameltype_gekozentype_id]);
			}
			if($log_afboeken) {
				if($verzameltype_gekozentype_id) {
					chalet_log($verzameltypes_gekoppeld[$verzameltype_gekozentype_id]." ".ereg_replace("_"," ",$voorraad_veldnaam)." 1 afboeken");
				} else {
					chalet_log(ereg_replace("_"," ",$voorraad_veldnaam)." 1 afboeken");
				}
			} elseif($form->input["voorraad_afboeken"]=="niet_bijwerken") {
				chalet_log("voorraad afboeken: voorraad niet bijwerken");
			}
		} elseif($_GET["stap"]==2) {
			if($gegevens["stap2"]["voornaam"]<>$form->input["voornaam"] or $gegevens["stap2"]["tussenvoegsel"]<>$form->input["tussenvoegsel"] or $gegevens["stap2"]["achternaam"]<>$form->input["achternaam"] or $gegevens["stap2"]["adres"]<>$form->input["adres"] or $gegevens["stap2"]["postcode"]<>$form->input["postcode"] or $gegevens["stap2"]["plaats"]<>$form->input["plaats"] or $gegevens["stap2"]["land"]<>$form->input["land"] or $gegevens["stap2"]["telefoonnummer"]<>$form->input["telefoonnummer"] or $gegevens["stap2"]["mobielwerk"]<>$form->input["mobielwerk"]) {
				chalet_log("naw-gegevens hoofdboeker");
			}
			if(($boeking_wijzigen or $mustlogin) and $form->input["verzendmethode_reisdocumenten"] and $form->input["verzendmethode_reisdocumenten"]<>$gegevens["stap1"]["verzendmethode_reisdocumenten"]) {
				chalet_log("verzendmethode reisdocumenten: ".$vars["verzendmethode_reisdocumenten_inclusief_nvt"][$form->input["verzendmethode_reisdocumenten"]]);
			}
			if($form->input["email"] and $gegevens["stap2"]["email"]<>$form->input["email"]) {
				if($gegevens["stap2"]["email"]) {
					chalet_log("e-mailadres hoofdboeker (van ".$gegevens["stap2"]["email"]." naar ".$form->input["email"].")");
				} else {
					chalet_log("e-mailadres hoofdboeker (".$form->input["email"].")");
				}
			}
			if($form->input["wachtwoord"]) {
				chalet_log("nieuw wachtwoord hoofdboeker (".$form->input["wachtwoord"].")");
			}
			if($gegevens["stap2"]["geslacht"]<>$form->input["geslacht"]) {
				chalet_log("geslacht hoofdboeker");
			}
			if($gegevens["stap2"]["geboortedatum"]<>$form->input["geboortedatum"]["unixtime"]) {
				chalet_log("geboortedatum hoofdboeker");
			}
		} elseif($_GET["stap"]==3) {
			for($i=2;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
				if($gegevens["stap3"][$i]["voornaam"]<>$form->input["voornaam".$i] or $gegevens["stap3"][$i]["tussenvoegsel"]<>$form->input["tussenvoegsel".$i] or $gegevens["stap3"][$i]["achternaam"]<>$form->input["achternaam".$i] or $gegevens["stap3"][$i]["plaats"]<>$form->input["plaats".$i]) {
					chalet_log("naw-gegevens persoon ".$i);
				}
				if($gegevens["stap3"][$i]["geslacht"]<>$form->input["geslacht".$i]) {
					chalet_log("geslacht persoon ".$i);
				}
				if($gegevens["stap3"][$i]["geboortedatum"]<>$form->input["geboortedatum".$i]["unixtime"] and isset($form->input["geboortedatum".$i]["unixtime"])) {
					chalet_log("geboortedatum persoon ".$i);
				}
			}
		} elseif($_GET["stap"]==4) {
			if($mustlogin and $form->input["wijzigen_dagen"]<>$gegevens["stap1"]["wijzigen_dagen"]) {
				chalet_log("aantal wijzigdagen (".$form->input["wijzigen_dagen"].")",true,true);
			}

			if($mustlogin and $form->input["opmerkingen_klant"]<>$gegevens["stap1"]["opmerkingen_klant"]) {
				chalet_log("optietekst voor klant",true,true);
			}

			if($mustlogin and $form->input["opmerkingen_voucher"]<>$gegevens["stap1"]["opmerkingen_voucher"]) {
				chalet_log("optietekst accommodatievoucher",true,true);
			}

			# schadeverzekering loggen
			if(!$form->input["schadeverzekering"]) $form->input["schadeverzekering"]=0;
			if($schadeverzekering_checkbox_getoond and $form->input["schadeverzekering"]<>$gegevens["stap1"]["schadeverzekering"]) {
				if($form->input["schadeverzekering"]) {
					chalet_log("schadeverzekering aan",true,true);
				} else {
					chalet_log("schadeverzekering uit",true,true);
				}
			}

			# Annuleringsverzekering loggen
			if($vars["annverzekering_mogelijk"] and (!$boeking_wijzigen or $gegevens["stap1"]["annuleringsverzekering_wijzigen_toegestaan"])) {
				for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
					if($form->input["annverz_".$i]<>$gegevens["stap3"][$i]["annverz"]) {
						if($form->input["annverz_".$i]) {
							chalet_log("persoon ".$i.": annuleringsverzekering ".strtolower($vars["annverz_soorten"][$form->input["annverz_".$i]]));
						} else {
							chalet_log("persoon ".$i.": geen annuleringsverzekering");
						}
					}
				}
			}
			# Algemene opties
			@reset($optie_soort_algemeen["naam_enkelvoud"]);
			while(list($key,$value)=@each($optie_soort_algemeen["naam_enkelvoud"])) {
				if($gegevens["stap4"]["algemene_optie"]["optie_onderdeel_id"]["alg".$key]<>$form->input["optie_".$key."_1"]) {
					chalet_log("optie ".strtolower($value).": ".$optie_onderdeel[$key][$form->input["optie_".$key."_1"]]["naam"],true);
				}
			}

			# Gewone opties
			for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
				@reset($optie_soort["naam_enkelvoud"]);
				while(list($key,$value)=@each($optie_soort["naam_enkelvoud"])) {
					if($gegevens["stap4"]["opties"][$i][$key]<>$form->input["optie_".$key."_".$i]) {
						chalet_log("optie ".strtolower($value)." persoon ".$i.": ".$optie_onderdeel[$key][$form->input["optie_".$key."_".$i]]["naam"],$gegevens["stap4"]["opties"][$i][$key]);
					}
				}
			}
		} elseif($_GET["stap"]==5) {
			if($gegevens["stap1"]["opmerkingen_boeker"]<>$form->input["opmerkingen_boeker"]) {
				chalet_log("algemene opmerkingen");
			}
			if($form->input["verzendmethode_reisdocumenten"]) {
				chalet_log("verzendmethode reisdocumenten: ".$vars["verzendmethode_reisdocumenten_inclusief_nvt"][$form->input["verzendmethode_reisdocumenten"]]);
			}
			chalet_log("boeking bevestigd (bevestiging gemaild aan ".$mail->to.")");

			# Inkoopprijs-gegevens loggen
			if($log_inkoopprijzen) chalet_log($log_inkoopprijzen);
		}

		# Vastleggen dat de stap is afgerond
		$db->query("UPDATE boeking SET ".($gegevens["stap_voltooid"][$_GET["stap"]] ? "" : "stap_voltooid='".addslashes($_GET["stap"])."', ")."log='".addslashes($gegevens["stap1"]["log"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
	}
	$form->end_declaration();
}

if(!$mustlogin and !$accinfo["tonen"]) {
	# Accommodatie is niet meer beschikbaar: wis cookie en session
	#mail("jeroen@webtastic.nl","Onbekende boeking debugtype B1 Chalet.nl","\nSession-boekingid: ".wt_dump($_SESSION["boeking"]["boekingid"],false)."\nCookie-boekingid: ".$_COOKIE["CHALET"]["boeking"]["boekingid"]."\nGegevens:\n".wt_dump($gegevens["stap1"],false)."\n\nAccinfo:".wt_dump($accinfo,false)."\nCookie: ".wt_dump($_COOKIE,false)."\nSession: ".wt_dump($_SESSION,false)."\nVar niet_beschikbaar: ".$niet_beschikbaar."\nVar accinfo[tonen]: ".$accinfo["tonen"]."\n".wt_dump($_SERVER,false)."\n");

	unset($_SESSION["boeking"]);
	unset($_COOKIE["CHALET"]["boeking"]["boekingid"]);
	setcookie("CHALET[boeking][boekingid]","_leeg_",time()+60);
	setcookie("CHALET[boeking][boekingid]","",time()-864000);
}

if($mustlogin) {
	$layout->display_all($cms->page_title);
} else {
	include "content/opmaak.php";
}

if($voorkant_cms and $_GET["stap"]==4 and $_POST) {
#	wt_mail("chaletmailbackup+systemlog@gmail.com","Chalet.nl memory usage",round(memory_get_peak_usage()/1024/1024)." MiB\n\n".$_SERVER["REQUEST_URI"]);
}

?>