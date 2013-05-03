<?php

#
# Tarieven-module
#

if(!$skipastarieven_verwerken) {
	$mustlogin=true;

	include("admin/vars.php");
}

if($_POST["filled"]) {

	# toonper bepalen
	$toonper=$_POST["toonper"];

	# Datumtijd vaststellen
	$datetime=time();

	# "korting" opslaan in database
	$van=mktime(0,0,0,$_POST["input"]["van"]["month"],$_POST["input"]["van"]["day"],$_POST["input"]["van"]["year"]);
	$tot=mktime(0,0,0,$_POST["input"]["tot"]["month"],$_POST["input"]["tot"]["day"],$_POST["input"]["tot"]["year"]);
	$vandaag=mktime(0,0,0,date("m"),date("d"),date("Y"));
	$gekoppelde_types[$_GET["tid"]]=true;
	if($_POST["input"]["gekoppeld_code"]) {
		if($_GET["kid"]) {
			$gekoppeld_code=$_POST["input"]["gekoppeld_code"];
		} else {
			$db->query("SELECT MAX(gekoppeld_code) AS gekoppeld_code FROM korting;");
			if($db->next_record()) {
				$gekoppeld_code=$db->f("gekoppeld_code")+1;
			} else {
				$gekoppeld_code=1;
			}
		}
		$db->query("SELECT type_id FROM type WHERE accommodatie_id=(SELECT accommodatie_id FROM type WHERE type_id='".addslashes($_GET["tid"])."');");
#		echo $db->lastquery;
		while($db->next_record()) {
			$gekoppelde_types[$db->f("type_id")]=true;
		}
	}
	while(list($tid,$legevalue)=each($gekoppelde_types)) {
		unset($kid,$savequery);

		if($inquery) $inquery.=",".$tid; else $inquery=$tid;

		$setquery="actief='".addslashes($_POST["input"]["actief"])."', naam='".addslashes($_POST["input"]["naam"])."', interne_opmerkingen='".addslashes($_POST["input"]["interne_opmerkingen"])."', type_id='".addslashes($tid)."', gekoppeld_code='".addslashes($gekoppeld_code)."', seizoen_id='".addslashes($_GET["sid"])."', van=FROM_UNIXTIME('".$van."'), tot=FROM_UNIXTIME('".$tot."'), toonexactekorting='".addslashes($_POST["input"]["toonexactekorting"])."', toon_abpagina='".addslashes($_POST["input"]["toon_abpagina"])."', toon_als_aanbieding='".addslashes($_POST["input"]["toon_als_aanbieding"])."', aanbiedingskleur='".addslashes($_POST["input"]["aanbiedingskleur"])."', onlinenaam".$vars["ttv"]."='".addslashes($_POST["input"]["onlinenaam"])."', omschrijving".$vars["ttv"]."='".addslashes($_POST["input"]["omschrijving"])."', volgorde='".addslashes($_POST["input"]["volgorde"])."', editdatetime=NOW()";

		if($_GET["kid"]) {
			if($gekoppeld_code) {
				$db->query("SELECT korting_id FROM korting WHERE type_id='".addslashes($tid)."' AND gekoppeld_code='".addslashes($gekoppeld_code)."';");
#				echo $db->lastquery."<br>";
				if($db->next_record()) {
					$kid=$db->f("korting_id");
					$db->query("UPDATE korting SET ".$setquery." WHERE gekoppeld_code='".addslashes($gekoppeld_code)."' AND type_id='".addslashes($tid)."';");
				} else {
					$db->query("INSERT INTO korting SET ".$setquery.", adddatetime=NOW();");
					$kid=$db->insert_id();
				}
			} else {
				$db->query("UPDATE korting SET ".$setquery." WHERE korting_id='".addslashes($_GET["kid"])."';");
				$kid=$_GET["kid"];
			}
		} else {
			$db->query("INSERT INTO korting SET ".$setquery.", adddatetime=NOW();");
			$kid=$db->insert_id();
		}

		# inkoopkorting_overnemen opslaan bij type
		$db->query("UPDATE type SET inkoopkorting_overnemen='".addslashes($_POST["auto_overnemen"])."' WHERE type_id='".addslashes($tid)."';");

		# "korting_tarief" opslaan in database
		reset($vars["korting_tarief_velden"]);
		while(list($key,$value)=each($vars["korting_tarief_velden"])) {
			@reset($_POST[$value]);
			while(list($key2,$value2)=@each($_POST[$value])) {
				if(is_int($key2)) {
					if($value2<>0) {
						$savequery[$key2].=", ".$value."='".addslashes($value2)."'";
					}
				}
			}
		}

		if($kid) {
			# Eerst gegevens wissen
			$db->query("DELETE FROM korting_tarief WHERE korting_id='".addslashes($kid)."';");

			# Dan opslaan
			while(list($key,$value)=@each($savequery)) {
				# Opslaan in tabel korting_tarief
				$db->query("INSERT INTO korting_tarief SET korting_id='".addslashes($kid)."', week='".$key."', opgeslagen=NOW()".$value.";");
			}
		} else {
			trigger_error("var kid is leeg bij opslaan korting",E_USER_NOTICE);
		}
	}

	if($inquery) {
		#
		# Alle kortingen doorrekenen
		#

		unset($korting);

		# kortingen uit db halen
		unset($korting);
		$db->query("SELECT k.type_id, k.toonexactekorting, k.aanbiedingskleur, k.toon_abpagina, k.toon_als_aanbieding, kt.week, kt.inkoopkorting_percentage, kt.aanbieding_acc_percentage, kt.aanbieding_skipas_percentage, kt.inkoopkorting_euro, kt.aanbieding_acc_euro, kt.aanbieding_skipas_euro FROM korting k, korting_tarief kt WHERE k.actief=1 AND kt.korting_id=k.korting_id AND UNIX_TIMESTAMP(k.van)<='".$vandaag."' AND UNIX_TIMESTAMP(k.tot)>='".$vandaag."' AND k.type_id IN (".$inquery.");");
		while($db->next_record()) {
			$korting[$db->f("type_id")][$db->f("week")]["inkoopkorting_percentage"]+=$db->f("inkoopkorting_percentage");
			$korting[$db->f("type_id")][$db->f("week")]["inkoopkorting_euro"]+=$db->f("inkoopkorting_euro");
			$korting[$db->f("type_id")][$db->f("week")]["aanbieding_acc_percentage"]+=$db->f("aanbieding_acc_percentage");
			$korting[$db->f("type_id")][$db->f("week")]["aanbieding_acc_euro"]+=$db->f("aanbieding_acc_euro");
			$korting[$db->f("type_id")][$db->f("week")]["aanbieding_skipas_percentage"]+=$db->f("aanbieding_skipas_percentage");
			$korting[$db->f("type_id")][$db->f("week")]["aanbieding_skipas_euro"]+=$db->f("aanbieding_skipas_euro");
			if($db->f("toonexactekorting")) $korting[$db->f("type_id")][$db->f("week")]["toonexactekorting"]=$db->f("toonexactekorting");
			if($db->f("aanbiedingskleur")) $korting[$db->f("type_id")][$db->f("week")]["aanbiedingskleur"]=$db->f("aanbiedingskleur");
			if($db->f("toon_abpagina")) $korting[$db->f("type_id")][$db->f("week")]["toon_abpagina"]=$db->f("toon_abpagina");
			if($db->f("toon_als_aanbieding")) $korting[$db->f("type_id")][$db->f("week")]["toon_als_aanbieding"]=$db->f("toon_als_aanbieding");
		}

		# kortingen opslaan
		while(list($key,$value)=@each($korting)) {
			while(list($key2,$value2)=each($value)) {
#				$db->query("UPDATE tarief SET kortingenverwerkt=1, kortingactief=1, inkoopkorting_percentage='".addslashes($value2["inkoopkorting_percentage"])."', aanbieding_acc_percentage='".addslashes($value2["aanbieding_acc_percentage"])."', aanbieding_skipas_percentage='".addslashes($value2["aanbieding_skipas_percentage"])."', inkoopkorting_euro='".addslashes($value2["inkoopkorting_euro"])."', aanbieding_acc_euro='".addslashes($value2["aanbieding_acc_euro"])."', aanbieding_skipas_euro='".addslashes($value2["aanbieding_skipas_euro"])."', toonexactekorting='".addslashes($value2["toonexactekorting"])."'  WHERE type_id='".addslashes($key)."' AND week='".addslashes($key2)."';");
				$db->query("UPDATE tarief SET kortingenverwerkt=1, kortingactief=1, inkoopkorting_percentage='".addslashes($value2["inkoopkorting_percentage"])."', aanbieding_acc_percentage='".addslashes($value2["aanbieding_acc_percentage"])."', aanbieding_skipas_percentage='".addslashes($value2["aanbieding_skipas_percentage"])."', inkoopkorting_euro='".addslashes($value2["inkoopkorting_euro"])."', aanbieding_acc_euro='".addslashes($value2["aanbieding_acc_euro"])."', aanbieding_skipas_euro='".addslashes($value2["aanbieding_skipas_euro"])."', toonexactekorting='".addslashes($value2["toonexactekorting"])."', aanbiedingskleur_korting='".addslashes($value2["aanbiedingskleur"])."', korting_toon_abpagina='".addslashes($value2["toon_abpagina"])."', korting_toon_als_aanbieding='".addslashes($value2["toon_als_aanbieding"])."' WHERE type_id='".addslashes($key)."' AND week='".addslashes($key2)."';");

#				echo $db->lastquery."<br>";
			}
		}

		$db->query("UPDATE tarief SET inkoopkorting_percentage=0, aanbieding_acc_percentage=0, aanbieding_skipas_percentage=0, inkoopkorting_euro=0, aanbieding_acc_euro=0, aanbieding_skipas_euro=0, korting_toon_abpagina=0, korting_toon_als_aanbieding=0 WHERE kortingenverwerkt=0 AND type_id IN (".$inquery.");");

		# alle tarieven berekenen indien kortingactief=1
		$skipastarieven_verwerken=true;
		unset($_POST);
		reset($gekoppelde_types);
		while(list($tid,$legevalue)=each($gekoppelde_types)) {

#			echo "Bereken ".$tid." (seizoen ".$_GET["sid"].")<br>";

			unset($seizoen,$acc,$skipas);
			$_GET["tid"]=$tid;
			include("cms_tarieven.php");
			@reset($seizoen["weken"]);
			if($toonper==1) {
				# bij arrangementen: tarief_personen wissen
				$db4->query("DELETE FROM tarief_personen WHERE type_id='".$tid."' AND seizoen_id='".addslashes($_GET["sid"])."';");
			}
			while(list($key,$value)=@each($seizoen["weken"])) {
				if($toonper==1) {
					# toonper=1 (arrangementen): tarief per persoon opslaan
					while(list($key2,$value2)=@each($value["verkoop_site"])) {
						$db4->query("INSERT INTO tarief_personen SET type_id='".$tid."', seizoen_id='".addslashes($_GET["sid"])."', week='".$key."', personen='".addslashes($key2)."', prijs='".addslashes($value2)."', afwijking='".addslashes($value["verkoop_afwijking"][$key2])."';");
	#					echo $db4->lastquery."<hr>";
					}
					$db4->query("UPDATE tarief SET wederverkoop_verkoopprijs='".addslashes($value["wederverkoop_verkoopprijs"])."', verkoop_accommodatie='".addslashes($value["verkoop_accommodatie"])."' WHERE type_id='".$tid."' AND seizoen_id='".addslashes($_GET["sid"])."' AND week='".$key."';");
				} elseif($toonper==3) {
					# toonper=3: tarief opslaan
					$db4->query("UPDATE tarief SET wederverkoop_verkoopprijs='".addslashes($value["wederverkoop_verkoopprijs"])."', c_verkoop_site='".addslashes($value["c_verkoop_site"])."' WHERE type_id='".$tid."' AND seizoen_id='".addslashes($_GET["sid"])."' AND week='".$key."';");
				}
#				echo $db4->lastquery."<hr>";
			}
		}

		# kortingactief en kortingenverwerkt uitzetten
		$db->query("UPDATE tarief SET kortingactief=0 WHERE kortingenverwerkt=0 AND type_id IN (".$inquery.");");
		$db->query("UPDATE tarief SET kortingenverwerkt=0 WHERE type_id IN (".$inquery.");");


		# verzameltype berekenen
		reset($gekoppelde_types);
		while(list($tid,$legevalue)=each($gekoppelde_types)) {
			verzameltype_berekenen($_GET["sid"],$tid);
		}
	}

	# Volgorde van kortingen: zorgen voor stappen van 10
	$volgorde=0;
	$db->query("SELECT korting_id, gekoppeld_code FROM korting WHERE actief=1 AND xml_korting=0 AND volgorde IS NOT NULL AND volgorde>0 ORDER BY volgorde, adddatetime;");
	while($db->next_record()) {
		if($db->f("gekoppeld_code")>0) {
			if(!$gekoppeld_code_gehad[$db->f("gekoppeld_code")]) {
				$volgorde+=10;
				$db2->query("UPDATE korting SET volgorde='".intval($volgorde)."' WHERE gekoppeld_code='".$db->f("gekoppeld_code")."';");
				$gekoppeld_code_gehad[$db->f("gekoppeld_code")]=true;
			}
		} else {
			$volgorde+=10;
			$db2->query("UPDATE korting SET volgorde='".intval($volgorde)."' WHERE korting_id='".$db->f("korting_id")."';");
		}
	}

	if($vars["bezoeker_is_jeroen"]) {
#		exit;
	}


	if($_GET["from"]) {
		header("Location: ".$_GET["from"]);
	} else {
		header("Location: cms_types.php?show=2&2k0=".$_GET["tid"]);
	}
	exit;
} else {
	# Gegevens ophalen uit database

	$cms_kortingen_tarieven=true;

	# Seizoengegevens laden
	$db->query("SELECT naam, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
		$seizoen["naam"]=$db->f("naam");
		$seizoen["begin"]=$db->f("begin");
		$seizoen["eind"]=$db->f("eind");
	}

	# Accommodatiegegevens laden
	$db->query("SELECT a.accommodatie_id, a.wzt, a.naam AS anaam, a.toonper, t.leverancier_id, t.inkoopkorting_overnemen, a.skipas_id, a.aankomst_plusmin, a.vertrek_plusmin, t.websites, t.naam AS tnaam, t.leverancierscode, t.code, p.naam AS plaats, t.optimaalaantalpersonen, t.maxaantalpersonen, t.aangepaste_min_tonen, l.begincode, lev.naam AS leverancier, lev.opmerkingen_intern, lev.aflopen_allotment, a.aantekeningen, t.aantekeningen AS taantekeningen, t.onderverdeeld_in_nummers, t.verzameltype FROM accommodatie a, type t, plaats p, land l, leverancier lev WHERE t.leverancier_id=lev.leverancier_id AND l.land_id=p.land_id AND a.plaats_id=p.plaats_id AND a.accommodatie_id=t.accommodatie_id AND t.type_id='".addslashes($_GET["tid"])."';");
	if($db->next_record()) {
		$acc["accommodatie_id"]=$db->f("accommodatie_id");
		$acc["wzt"]=$db->f("wzt");
		$acc["naam"]=$db->f("anaam").($db->f("tnaam") ? " ".$db->f("tnaam") : "");
		$acc["plaats"]=$db->f("plaats");
		$acc["min"]=$db->f("optimaalaantalpersonen");
		$acc["max"]=$db->f("maxaantalpersonen");
		$acc["toonper"]=$db->f("toonper");
		$acc["code"]=$db->f("code");
		$acc["onderverdeeld_in_nummers"]=$db->f("onderverdeeld_in_nummers");
		$acc["land_begincode"]=$db->f("begincode");
		$acc["inkoopkorting_overnemen"]=$db->f("inkoopkorting_overnemen");
		if($db->f("optimaalaantalpersonen")>12) {
			if($db->f("aangepaste_min_tonen")) {
				$acc["min_tonen"]=$db->f("aangepaste_min_tonen");
			} else {
				$acc["min_tonen"]=floor($db->f("optimaalaantalpersonen")*.5);
			}
		} else {
			$acc["min_tonen"]=1;
		}
		$acc["leverancier_id"]=$db->f("leverancier_id");
		$acc["leverancier"]=$db->f("leverancier");
		$acc["skipas_id"]=$db->f("skipas_id");
		$acc["xml_leverancierscode"]=$db->f("leverancierscode");
		$acc["opmerkingen_intern"]=$db->f("opmerkingen_intern");
		$acc["aantekeningen"]=$db->f("aantekeningen");
		$acc["taantekeningen"]=$db->f("taantekeningen");
		$acc["aankomst_plusmin"]=$db->f("aankomst_plusmin");
		$acc["vertrek_plusmin"]=$db->f("vertrek_plusmin");
		$acc["aflopen_allotment"]=$db->f("aflopen_allotment");
		$acc["verzameltype"]=$db->f("verzameltype");
		$acc["websites"]=$db->f("websites");
		if(ereg("T",$acc["websites"]) or ereg("O",$acc["websites"]) or ereg("Z",$acc["websites"]) or ereg("E",$acc["websites"])) {
			$acc["wederverkoop"]=true;
		}
	}

	if($_GET["kid"]) {
		$db->query("SELECT actief, naam, interne_opmerkingen, gekoppeld_code, UNIX_TIMESTAMP(van) AS van, UNIX_TIMESTAMP(tot) AS tot, toonexactekorting, aanbiedingskleur, toon_abpagina, toon_als_aanbieding, onlinenaam".$vars["ttv"]." AS onlinenaam, omschrijving".$vars["ttv"]." AS omschrijving, volgorde, xml_korting FROM korting WHERE korting_id='".addslashes($_GET["kid"])."';");
		if($db->next_record()) {
			$korting["actief"]=$db->f("actief");
			$korting["naam"]=$db->f("naam");
			$korting["interne_opmerkingen"]=$db->f("interne_opmerkingen");
			$korting["van"]=$db->f("van");
			$korting["tot"]=$db->f("tot");
			$korting["gekoppeld_code"]=$db->f("gekoppeld_code");
			$korting["toonexactekorting"]=$db->f("toonexactekorting");
			$korting["aanbiedingskleur"]=$db->f("aanbiedingskleur");
			$korting["toon_abpagina"]=$db->f("toon_abpagina");
			$korting["toon_als_aanbieding"]=$db->f("toon_als_aanbieding");
			$korting["onlinenaam"]=$db->f("onlinenaam");
			$korting["omschrijving"]=$db->f("omschrijving");
			$korting["volgorde"]=$db->f("volgorde");
			$korting["xml_korting"]=$db->f("xml_korting");
		}
	}

	if($acc["toonper"]==1) {
		# Skipas-tarieven
		$db->query("SELECT week, bruto, netto_ink, korting, verkoopkorting, prijs, omzetbonus, netto FROM skipas_tarief WHERE skipas_id='".addslashes($acc["skipas_id"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		while($db->next_record()) {
			reset($vars["sjabloon_skipas"]);
			while(list($key,$value)=each($vars["sjabloon_skipas"])) {
				if($db->f($value)>0) $skipas["weken"][$db->f("week")][$value]=$db->f($value);
			}

			if($skipas["weken"][$db->f("week")]["bruto"]>0) {
				$skipas["weken"][$db->f("week")]["marge_op_verkoop"]=number_format(($skipas["weken"][$db->f("week")]["bruto"]-$skipas["weken"][$db->f("week")]["netto"])/$skipas["weken"][$db->f("week")]["bruto"]*100,2,".","");
			}
		}
	}


	# Kortingen uit tabel korting_tarief
	if($_GET["kid"]) {
		$db->query("SELECT week, inkoopkorting_percentage, aanbieding_acc_percentage, aanbieding_skipas_percentage, inkoopkorting_euro, aanbieding_acc_euro, aanbieding_skipas_euro FROM korting_tarief WHERE korting_id='".addslashes($_GET["kid"])."';");
		while($db->next_record()) {
			reset($vars["korting_tarief_velden"]);
			while(list($key,$value)=each($vars["korting_tarief_velden"])) {
				if($db->f($value)<>0) {
					$seizoen["weken"][$db->f("week")][$value]=$db->f($value);
				}
			}
		}
	}

	# Accommodatie tarieven
	$db->query("SELECT type_id FROM tarief WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
		# Tarieven uit tabel tarief
		$db->query("SELECT week, beschikbaar, blokkeren_wederverkoop, bruto, korting_percentage, toeslag, korting_euro, vroegboekkorting_percentage, vroegboekkorting_euro, opslag_accommodatie, opslag_skipas, afwijking_alle, arrangementsprijs, onbezet_bed, toeslag_arrangement_euro, korting_arrangement_euro, toeslag_bed_euro, korting_bed_euro, vroegboekkorting_arrangement_percentage, vroegboekkorting_arrangement_euro, vroegboekkorting_bed_percentage, vroegboekkorting_bed_euro, opslag, c_bruto, c_korting_percentage, c_toeslag, c_korting_euro, c_vroegboekkorting_percentage, c_vroegboekkorting_euro, c_opslag_accommodatie, c_verkoop_afwijking, c_verkoop_site, voorraad_garantie, voorraad_allotment, voorraad_vervallen_allotment, voorraad_optie_leverancier, voorraad_xml, voorraad_request, voorraad_optie_klant, voorraad_bijwerken, wederverkoop_opslag_euro, wederverkoop_opslag_percentage, wederverkoop_commissie_agent, aanbiedingskleur, autoimportxmltarief, blokkeerxml, aflopen_allotment FROM tarief WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		while($db->next_record()) {
			reset($vars["tarief_velden"]);
			while(list($key,$value)=each($vars["tarief_velden"])) {
				if($value=="aflopen_allotment") {
					if($db->f($value)<>"") $seizoen["weken"][$db->f("week")][$value]=$db->f($value);
				} else {
					if($db->f($value)<>0) $seizoen["weken"][$db->f("week")][$value]=$db->f($value);
				}

/*
				# Voorraad
				$seizoen["weken"][$db->f("week")]["voorraad_totaal"]=$db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")+$db->f("voorraad_request")-$db->f("voorraad_optie_klant");
				if($acc["aflopen_allotment"]<>"" or $aflopen_allotment[$db->f("week")]<>"" or $db->f("aflopen_allotment")<>"") {
					if($db->f("aflopen_allotment")<>"") {
						$temp_aflopen_allotment=$db->f("aflopen_allotment");
					} elseif($aflopen_allotment[$db->f("week")]<>"") {
						$temp_aflopen_allotment=$aflopen_allotment[$db->f("week")];
					} else {
						$temp_aflopen_allotment=$acc["aflopen_allotment"];
					}
					$seizoen["weken"][$db->f("week")]["voorraad_aflopen_allotment"]=mktime(0,0,0,date("m",$db->f("week")),date("d",$db->f("week"))-$temp_aflopen_allotment,date("Y",$db->f("week")));
					$seizoen["weken"][$db->f("week")]["voorraad_aflopen_allotment_dagen"]=$temp_aflopen_allotment;
				}
				if($db->f("voorraad_bijwerken")) {
					if(($db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")+$db->f("voorraad_request"))>0) {
						$seizoen["weken"][$db->f("week")]["beschikbaar"]=1;
					} else {
						$seizoen["weken"][$db->f("week")]["beschikbaar"]=0;
					}
				}

*/
			}

		}
		reset($vars["tarief_datum_velden"]);
		unset($datum_velden);
		while(list($key,$value)=each($vars["tarief_datum_velden"])) {
			$datum_velden.=", UNIX_TIMESTAMP(".$value.") AS ".$value;
		}

		if($acc["toonper"]==1) {
			# Tarieven uit tabel tarief_personen
			$db->query("SELECT week, personen, prijs, afwijking FROM tarief_personen WHERE type_id='".addslashes($_GET["tid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
			while($db->next_record()) {
				if($seizoen["weken"][$db->f("week")]["bruto"]>0) {
					if($db->f("prijs")>0) $seizoen["weken"][$db->f("week")]["verkoop_site"][$db->f("personen")]=$db->f("prijs");
					if($db->f("afwijking")<>"0.00") $seizoen["weken"][$db->f("week")]["verkoop_afwijking"][$db->f("personen")]=$db->f("afwijking");
				}
			}
		}

		# Tarieven doorrekenen
		$doorloop_array=$seizoen["weken"];
		while(list($key,$value)=@each($doorloop_array)) {
			$seizoen["weken"][$key]=bereken($acc["toonper"],$seizoen,$key,$acc,$skipas);
		}

#		echo wt_dump($seizoen["weken"]);

	} else {
		echo "Er zijn nog geen tarieven bekend. Kortingen invoeren kan pas na het invoeren van tarieven.";
		exit;
	}
	if(!$skipastarieven_verwerken) include("content/cms_kortingen_tarieven.html");
}

?>