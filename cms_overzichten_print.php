<?php

if($_COOKIE["levli"] and ($_GET["roominglist"] or $_GET["vertreklijst"])) {
	#
	# Ingelogde leverancier/eigenaar
	#
	$vars["leverancier_mustlogin"]=true;
} else {
	#
	# CMS
	#
	$mustlogin=true;
}
include("admin/vars.php");

if($_COOKIE["levli"] and ($_GET["roominglist"] or $_GET["vertreklijst"])) {
	#
	# Ingelogde leverancier/eigenaar
	#
	if($_GET["roominglist"]) {
		$_GET["t"]=1;
	}
	if($_GET["vertreklijst"]) {
		$_GET["t"]=4;
	}
	$_GET["lid"]=$login_lev->user_id;
} else {
	#
	# CMS
	#
	if(!$login->has_priv("25")) {
		header("Location: cms.php");
		exit;
	}
}

if($_GET["t"]==5 or $_GET["t"]==8 or $_GET["t"]==9 or $_GET["t"]==10 or $_GET["t"]==11 or $_GET["t"]==12) {
	# CSV-bestand
} else {
	# Word-bestand
	include("admin/class.msoffice.php");
	$ms=new ms_office;
	$ms->author="Chalet.nl";
	$ms->company="Chalet.nl";
	if($_GET["t"]==7) {
		$ms->margin="0cm 0cm 0cm 0cm;";
	} else {
		$ms->landscape=true;
	}

	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") $ms->test=true;
}

if($_GET["t"]==1 or $_GET["t"]==2) {
	# t=1 --> Roominglist totaal
	# t=2 --> Roominglist op datum (=aankomstlijst)

	if($mustlogin) {
		if($_GET["t"]==1) {
			cmslog_pagina_title("Overzichten - Roominglist");
		} else {
			cmslog_pagina_title("Overzichten - Aankomstlijst");		
		}
	}
	
	$colspan=9;
	
	# Leveranciersgegevens ophalen
	
	$db->query("SELECT roominglist_toonaantaldeelnemers, roominglist_toontelefoonnummer FROM leverancier WHERE leverancier_id='".addslashes($_GET["lid"])."';");
	if($db->next_record()) {
		if($db->f("roominglist_toonaantaldeelnemers")) {
			if($_GET["t"]==2) {
				$roominglist_toonaantaldeelnemers=true;
			}
		}
		if($db->f("roominglist_toontelefoonnummer")) {
			if($_GET["t"]==2) {
				$roominglist_toontelefoonnummer=true;
				$colspan++;
			}
		}
	}
	
	# Gewone boekingen
	if($_GET["t"]==2) {
		$where="b.aankomstdatum='".addslashes($_GET["date"])."' AND ";
	} else {
		$where="b.aankomstdatum_exact>='".time()."' AND ";
	}
#	$db->query("SELECT bp.voornaam, bp.tussenvoegsel, bp.achternaam, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." b.leverancier_id=l.leverancier_id AND l.leverancier_id='".addslashes($_GET["lid"])."' AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, b.aankomstdatum_exact, a.naam, t.naam;");
#	$db->query("SELECT bp.voornaam, bp.tussenvoegsel, bp.achternaam, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." (b.leverancier_id=l.leverancier_id OR a.beheerder_id=l.leverancier_id OR t.beheerder_id=l.leverancier_id) AND l.leverancier_id='".addslashes($_GET["lid"])."' AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, b.aankomstdatum_exact, a.naam, t.naam;");
#	$db->query("SELECT bp.voornaam, bp.tussenvoegsel, bp.achternaam, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." (b.leverancier_id=l.leverancier_id OR b.beheerder_id=l.leverancier_id) AND l.leverancier_id='".addslashes($_GET["lid"])."' AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, b.aankomstdatum_exact, a.naam, t.naam;");
#	$db->query("SELECT b.boeking_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.mobielwerk, bp.telefoonnummer, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, a.bestelnaam AS abestelnaam, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." (b.leverancier_id=l.leverancier_id OR b.beheerder_id=l.leverancier_id) AND l.leverancier_id='".addslashes($_GET["lid"])."' AND ((b.verzameltype_gekozentype_id IS NULL AND b.type_id=t.type_id) OR (b.verzameltype_gekozentype_id>0 AND b.verzameltype_gekozentype_id=t.type_id)) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, b.aankomstdatum_exact, a.naam, t.naam;");
	$db->query("SELECT b.boeking_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.mobielwerk, bp.telefoonnummer, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, a.bestelnaam AS abestelnaam, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." (b.leverancier_id=l.leverancier_id OR b.beheerder_id=l.leverancier_id) AND l.leverancier_id='".addslashes($_GET["lid"])."' AND ((b.verzameltype_gekozentype_id IS NULL AND b.type_id=t.type_id) OR (b.verzameltype_gekozentype_id>0 AND b.verzameltype_gekozentype_id=t.type_id)) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 AND UNIX_TIMESTAMP(b.besteldatum)>0 ORDER BY p.naam, b.aankomstdatum_exact, a.naam, t.naam;");



	while($db->next_record()) {
	
		$sortkey=$db->f("plaats")."_".$db->f("aankomstdatum_exact")."_".$db->f("accommodatie")."_".$db->f("type");
		if(!$leverancier) {
			$leverancier=$db->f("leverancier");
			$aankomstdatum=$db->f("aankomstdatum_exact");
		}
		$aankomstdata[$db->f("aankomstdatum_exact")]=true;
		$accnaam=$db->f("abestelnaam")." ".($db->f("type") ? $db->f("type")." " : "")."(".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." p)";
		$accnaam_kort=$db->f("abestelnaam")." ".($db->f("type") ? $db->f("type")." " : "");
		$accnaam_kort_aanvullend="";
		if($db->f("accommodatie")<>$db->f("abestelnaam")) {
			$accnaam_kort_aanvullend.=" <i>(our name: ".htmlentities($db->f("accommodatie").($db->f("type") ? " ".$db->f("type") : "")).")</i>";
		}
		$tempplaatsid[$sortkey]=$db->f("plaats_id");
		$regels[$sortkey].="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">".htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam")))."</td>";
		if($roominglist_toontelefoonnummer) {
			$regels[$sortkey].="<td valign=\"top\">";
			if($db->f("mobielwerk")) {
				$regels[$sortkey].=htmlentities($db->f("mobielwerk"));
			} elseif($db->f("telefoonnummer")) {
				$regels[$sortkey].=htmlentities($db->f("telefoonnummer"));
			} else {
				$regels[$sortkey].="&nbsp;";
			}
			$regels[$sortkey].="</td>";
		}
		$regels[$sortkey].="<td valign=\"top\" nowrap>".date("d-m-y",$db->f("aankomstdatum_exact"))."</td><td valign=\"top\" nowrap>".date("d-m-y",$db->f("vertrekdatum_exact"))."</td><td valign=\"top\">".htmlentities($db->f("plaats"))."</td><td valign=\"top\">".htmlentities($accnaam_kort).$accnaam_kort_aanvullend."</td><td valign=\"top\">".($db->f("code") ? htmlentities($db->f("code")) : "&nbsp;")."</td>";
		if($roominglist_toonaantaldeelnemers) {
			$db2->query("SELECT COUNT(boeking_id) AS aantal FROM boeking_persoon WHERE boeking_id='".$db->f("boeking_id")."';");
			if($db2->next_record()) {
				$aantal=intval($db2->f("aantal"));
			} else {
				$aantal="";
			}
			$regels[$sortkey].="<td valign=\"top\">".$aantal."</td>";
		} else {
			$regels[$sortkey].="<td valign=\"top\">".$db->f("maxaantalpersonen")."</td>";
		}
		$regels[$sortkey].="<td valign=\"top\">".($db->f("leverancierscode") ? htmlentities($db->f("leverancierscode")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("opmerkingen_voucher") ? nl2br(htmlentities($db->f("opmerkingen_voucher"))) : "&nbsp;")."</td></tr>";
	}
	
	# Garanties
	if($_GET["t"]==2) {
		$where="g.aankomstdatum='".addslashes($_GET["date"])."' AND ";
	} else {
		$where="g.aankomstdatum_exact>='".time()."' AND ";
	}
	$db->query("SELECT g.naam, g.aankomstdatum_exact, g.vertrekdatum_exact, g.factuurnummer, UNIX_TIMESTAMP(g.inkoopdatum) AS inkoopdatum, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM garantie g, type t, accommodatie a, plaats p, leverancier l WHERE ".$where." g.leverancier_id=l.leverancier_id AND l.leverancier_id='".addslashes($_GET["lid"])."' AND g.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND g.boeking_id=0;");
	while($db->next_record()) {
		$sortkey=$db->f("plaats")."_".$db->f("aankomstdatum_exact")."_".$db->f("accommodatie")."_".$db->f("type");
		if(!$leverancier) {
			$leverancier=$db->f("leverancier");
			$aankomstdatum=$db->f("aankomstdatum_exact");
		}
		$aankomstdata[$db->f("aankomstdatum_exact")]=true;
		$accnaam=$db->f("accommodatie")." ".($db->f("type") ? $db->f("type")." " : "")."(".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." p)";
		$accnaam_kort=$db->f("accommodatie")." ".($db->f("type") ? $db->f("type")." " : "");
		$tempplaatsid[$sortkey]=$db->f("plaats_id");
		$regels[$sortkey].="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">".htmlentities($db->f("naam"))."</td>";
		if($roominglist_toontelefoonnummer) {
			$regels[$sortkey].="<td valign=\"top\">&nbsp;</td>";
		}
		$regels[$sortkey].="<td valign=\"top\" nowrap>".date("d-m-y",$db->f("aankomstdatum_exact"))."</td><td valign=\"top\" nowrap>".date("d-m-y",$db->f("vertrekdatum_exact"))."</td><td valign=\"top\">".htmlentities($db->f("plaats"))."</td><td valign=\"top\">".htmlentities($accnaam_kort)."</td><td valign=\"top\">".($db->f("code") ? htmlentities($db->f("code")) : "&nbsp;")."</td>";
		if($roominglist_toonaantaldeelnemers) {
			$regels[$sortkey].="<td valign=\"top\">&nbsp;</td>";
		} else {
			$regels[$sortkey].="<td valign=\"top\">".$db->f("maxaantalpersonen")."</td>";
		}
		$regels[$sortkey].="<td valign=\"top\">".($db->f("factuurnummer") ? htmlentities($db->f("factuurnummer")) : ($db->f("inkoopdatum")>0 ? "OK ".date("d-m-Y",$db->f("inkoopdatum")) : "&nbsp;"))."</td><td valign=\"top\">".($db->f("opmerkingen_voucher") ? nl2br(htmlentities($db->f("opmerkingen_voucher"))) : "&nbsp;")."</td></tr>";
	}	
	
	if(is_array($regels)) {
		ksort($regels);
		while(list($key,$value)=each($regels)) {
			if($plaatsid_gehad and $tempplaatsid[$key]<>$plaatsid_gehad) {
				$html.="<tr style='mso-yfti-irow:1'><td colspan=\"".$colspan."\">&nbsp;</td></tr>";
			}
			$plaatsid_gehad=$tempplaatsid[$key];
			$html.=$value;
		}
	}
	if($_GET["t"]==1) {
		$ms->filename="roominglist_".strtolower(ereg_replace(" ","_",$leverancier));
	} else {
		$ms->filename="arrivals_".strtolower(ereg_replace(" ","_",$leverancier));
	}
	$ms->html.="<table border=\"1\" bordercolor=\"#000000\" cellpadding=\"5\" cellspacing=\"0\"><thead>";
	$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><td colspan=".$colspan.">";

	$ms->html.="<table width=100%><thead><tr><td><h3>";
	if($_GET["t"]==2) {
		$ms->html.="Arrivals ";
		# Datum in titel
		if(@count($aankomstdata)>1) {
			ksort($aankomstdata);
			while(list($key,$value)=each($aankomstdata)) {
				if($aankomstdatum_teller) $ms->html.=" + ";
				$aankomstdatum_teller++;
				$ms->html.=date("d-m-Y",$key);
			}
		} else {
			$ms->html.=date("d-m-Y",$aankomstdatum);
		}
	} else {
		$ms->html.="Roominglist ".date("d-m-Y");
	}
	
	$ms->html.=": Chalet.nl / Wintersportaccommodaties.nl / Zomerhuisje.nl</h3>Chalet.nl B.V. - Lindenhof 5 - 3442 GT Woerden - The Netherlands - Tel: +31 348 - 43 46 49 - Fax: +31 348 - 69 07 52 - info@chalet.nl</td><td align=right>";
	$ms->html.="<img width=92 height=79 src=\"http://www.chalet.nl/pic/factuur_logo_vakantiewoningen.png\"></td></tr></thead></table>";
	$ms->html.="</td></tr>";
	$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Clientsname</th>";
	
	if($roominglist_toontelefoonnummer) {
		$ms->html.="<th>Phone</th>";
	}
	$ms->html.="<th>Arrival</th><th>Departure</th><th>Resort</th><th>Accommodation</th><th>Type</th>";
	
	if($roominglist_toonaantaldeelnemers) {
		$ms->html.="<th>People</th>";
	} else {
		$ms->html.="<th>Cap.</th>";
	}
	$ms->html.="<th>Reserv.<br>Nr.</th><th>Extra<br>Options</th></tr></thead>";
	
	$ms->html.=$html;
	$ms->html.="</table>";
	$ms->html.="<br><i>".$leverancier." - printed ".date("d-m-y")."</i>";
} elseif($_GET["t"]==3) {

	cmslog_pagina_title("Overzichten - Telefoonlijst");

	$ms->filename="telefoonlijst_".date("d_m_Y",$_GET["date"]);
	# Overzicht telefoonnummers (voor intern gebruik)
	$ms->html.="<h3>Telefoonlijst ".$vars["aankomstdatum_weekend_alleseizoenen"][$_GET["date"]]."</h3>";
	
	# Accommodaties
#	$db->query("SELECT DISTINCT a.naam AS accommodatie, a.receptie, a.telefoonnummer, p.naam AS plaats, l.naam AS leverancier, l.faxnummer, l.noodnummer FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, a.naam;");
	$db->query("SELECT DISTINCT a.naam AS accommodatie, a.bestelnaam, a.receptie, a.telefoonnummer, p.naam AS plaats, l.naam AS leverancier, l.faxnummer, l.noodnummer FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND ((b.verzameltype_gekozentype_id IS NULL AND b.type_id=t.type_id) OR (b.verzameltype_gekozentype_id>0 AND b.verzameltype_gekozentype_id=t.type_id)) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY p.naam, a.naam;");


#	echo $db->lastquery;
	if($db->num_rows()) {
		echo $db->f("telefoonnummer");
		$ms->html.="<table border=\"1\" bordercolor=\"#000000\" cellpadding=\"5\" cellspacing=\"0\"><thead>";

		$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Plaats</th><th>Accommodatie</th><th>Contactpersoon</th><th>Telefoonnummer</th><th>Faxnummer</th><th>Noodnummer</th></tr></thead>";
		while($db->next_record()) {
			$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">".htmlentities($db->f("plaats"))."</td><td valign=\"top\">".htmlentities($db->f("accommodatie")).($db->f("accommodatie")<>$db->f("bestelnaam") ? " <i>(".htmlentities($db->f("bestelnaam")).")</i>" : "")."</td><td valign=\"top\">".($db->f("receptie") ? htmlentities($db->f("receptie")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("telefoonnummer") ? htmlentities($db->f("telefoonnummer")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("faxnummer") ? htmlentities($db->f("faxnummer")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("noodnummer") ? htmlentities($db->f("noodnummer")) : "&nbsp;")."</td></tr>";
		}
		$ms->html.="</table>";
	}

	# Skipassen
	$db->query("SELECT DISTINCT s.naam, s.contactpersoon, s.telefoonnummer, s.faxnummer, s.noodnummer FROM boeking b, type t, accommodatie a, plaats p, leverancier l, skipas s WHERE s.skipas_id=a.skipas_id AND b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY s.naam;");
#	echo $db->lastquery;
	if($db->num_rows()) {
		echo $db->f("telefoonnummer");
		$ms->html.="<br>&nbsp;<br><table border=\"1\" bordercolor=\"#000000\" cellpadding=\"5\" cellspacing=\"0\"><thead>";

		$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Skipas</th><th>Contactpersoon</th><th>Telefoonnummer</th><th>Faxnummer</th><th>Noodnummer</th></tr></thead>";
		while($db->next_record()) {
			$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">".htmlentities($db->f("naam").$db->f("optie_groep_id"))."</td><td valign=\"top\">".($db->f("contactpersoon") ? htmlentities($db->f("contactpersoon")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("telefoonnummer") ? htmlentities($db->f("telefoonnummer")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("faxnummer") ? htmlentities($db->f("faxnummer")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("noodnummer") ? htmlentities($db->f("noodnummer")) : "&nbsp;")."</td></tr>";
		}
		$ms->html.="</table>";
	}
		
	# Opties
	$db->query("SELECT DISTINCT og.optie_groep_id, og.naam, og.contactpersoon, og.telefoonnummer, og.faxnummer, og.noodnummer FROM optie_accommodatie oa, optie_groep og, boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE (og.contactpersoon<>'' OR og.telefoonnummer<>'' OR og.faxnummer<>'' OR og.noodnummer<>'') AND oa.optie_groep_id=og.optie_groep_id AND oa.accommodatie_id=a.accommodatie_id AND b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY og.naam;");
	if($db->num_rows()) {
		$ms->html.="<br>&nbsp;<br><table border=\"1\" bordercolor=\"#000000\" cellpadding=\"5\" cellspacing=\"0\"><thead>";
		$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Optie</th><th>Contactpersoon</th><th>Telefoonnummer</th><th>Faxnummer</th><th>Noodnummer</th></tr></thead>";
		while($db->next_record()) {
			$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">".htmlentities($db->f("naam"))."</td><td valign=\"top\">".($db->f("contactpersoon") ? htmlentities($db->f("contactpersoon")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("telefoonnummer") ? htmlentities($db->f("telefoonnummer")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("faxnummer") ? htmlentities($db->f("faxnummer")) : "&nbsp;")."</td><td valign=\"top\">".($db->f("noodnummer") ? htmlentities($db->f("noodnummer")) : "&nbsp;")."</td></tr>";
		}
		$ms->html.="</table>";
	}
} elseif($_GET["t"]==4) {

	if($_GET["vertreklijst"]) {
		#
		# Vertreklijst voor eigenaren (via eigenarenlogin)
		#

# nog niet in gebruik
exit;

		cmslog_pagina_title("Overzichten - Vertreklijst");
	
		$ms->filename="vertreklijst";
		
		# Reisbureau-gegevens ophalen
		$db->query("SELECT r.reisbureau_id, u.user_id AS reisbureau_user_id, r.naam, u.naam AS usernaam, u.email, r.reserveringskosten, r.adres, r.postcode, r.plaats, r.land, r.aanpassing_commissie FROM reisbureau r, reisbureau_user u, boeking b WHERE u.reisbureau_id=r.reisbureau_id AND b.reisbureau_user_id=u.user_id;");
		while($db->next_record()) {
			$vars["reisbureau_naam"][$db->f("reisbureau_user_id")]=$db->f("naam");
		}
		
		
		# Boekingen ophalen
	#	$db->query("SELECT SUBSTRING(b.boekingsnummer,11) AS boekingsnummer, b.reisbureau_user_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.telefoonnummer, bp.mobielwerk, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, b.opmerkingen_vertreklijst, b.aantalpersonen, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY SUBSTRING(b.boekingsnummer,11), p.naam, a.naam, t.naam;");
		$db->query("SELECT SUBSTRING(b.boekingsnummer,2,6) AS boekingsnummer1, SUBSTRING(b.boekingsnummer,11) AS boekingsnummer2, SUBSTRING(b.boekingsnummer,2,8) AS boekingsnummer, b.reisbureau_user_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.telefoonnummer, bp.mobielwerk, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, b.opmerkingen_vertreklijst, b.aantalpersonen, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, a.bestelnaam AS abestelnaam, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND ((b.verzameltype_gekozentype_id IS NULL AND b.type_id=t.type_id) OR (b.verzameltype_gekozentype_id>0 AND b.verzameltype_gekozentype_id=t.type_id)) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY SUBSTRING(b.boekingsnummer,11), SUBSTRING(b.boekingsnummer,2,8), p.naam, a.naam, t.naam;");
		if($db->num_rows()) {
			$ms->html.="<table border=\"1\" bordercolor=\"#000000\" cellpadding=\"5\" cellspacing=\"0\"><thead>";
			$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Volgnr.</th><th>Res.<br>nr.</th><th>Hoofdboeker</th><th>Telefoon<br>hoofdboeker</th><th>Aankomst</th><th>Plaats</th><th>Accommodatie</th><th>Type</th><th>Cap.</th><th>Pers.</th><th>Leverancier</th><th>Res.<br>code</th></tr></thead>";
			while($db->next_record()) {
				if($db->f("telefoonnummer") and $db->f("mobielwerk")) {
					$telefoon=$db->f("telefoonnummer")."<br>".$db->f("mobielwerk");
				} elseif($db->f("telefoonnummer")) {
					$telefoon=$db->f("telefoonnummer");
				} elseif($db->f("mobielwerk")) {
					$telefoon=$db->f("mobielwerk");
				} else {
					$telefoon="&nbsp;";
				}
				$accnaam=$db->f("accommodatie")." ".($db->f("type") ? $db->f("type")." " : "")."(".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." p)";
				if($db->f("accommodatie")<>$db->f("abestelnaam")) {
					$accnaam_toevoeging=" <i>(".htmlentities($db->f("abestelnaam").($db->f("type") ? " ".$db->f("type") : "")).")</i>";
				} else {
					$accnaam_toevoeging="";
				}
				$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">".($db->f("boekingsnummer2") ? $db->f("boekingsnummer2") : $db->f("boekingsnummer"))."</td><td valign=\"top\">".($db->f("boekingsnummer2") ? $db->f("boekingsnummer1") : $db->f("boekingsnummer"))."</td><td valign=\"top\">".htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"))).($db->f("opmerkingen_vertreklijst") ? " (".htmlentities(trim($db->f("opmerkingen_vertreklijst"))).")" : "").($vars["reisbureau_naam"][$db->f("reisbureau_user_id")] ? htmlentities(" (via ".$vars["reisbureau_naam"][$db->f("reisbureau_user_id")].")") : "")."</td><td valign=\"top\">".$telefoon."</td><td valign=\"top\" nowrap>".date("d-m-Y",$db->f("aankomstdatum_exact"))."</td><td valign=\"top\">".htmlentities($db->f("plaats"))."</td><td valign=\"top\">".htmlentities($accnaam).$accnaam_toevoeging."</td><td valign=\"top\">".($db->f("code") ? htmlentities($db->f("code")) : "&nbsp;")."</td><td valign=\"top\">".$db->f("maxaantalpersonen")."</td><td valign=\"top\">".$db->f("aantalpersonen")."</td><td valign=\"top\">".htmlentities($db->f("leverancier"))."</td><td valign=\"top\">".($db->f("leverancierscode") ? htmlentities($db->f("leverancierscode")) : "&nbsp;")."</td></tr>";
			}
			$ms->html.="</table>";
		}
	} else {
		#
		# Interne vertreklijst
		#
		cmslog_pagina_title("Overzichten - Vertreklijst");
	
		$ms->filename="vertreklijst";
		
		# Reisbureau-gegevens ophalen
		$db->query("SELECT r.reisbureau_id, u.user_id AS reisbureau_user_id, r.naam, u.naam AS usernaam, u.email, r.reserveringskosten, r.adres, r.postcode, r.plaats, r.land, r.aanpassing_commissie FROM reisbureau r, reisbureau_user u, boeking b WHERE u.reisbureau_id=r.reisbureau_id AND b.reisbureau_user_id=u.user_id;");
		while($db->next_record()) {
			$vars["reisbureau_naam"][$db->f("reisbureau_user_id")]=$db->f("naam");
		}
		
		
		# Boekingen ophalen
	#	$db->query("SELECT SUBSTRING(b.boekingsnummer,11) AS boekingsnummer, b.reisbureau_user_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.telefoonnummer, bp.mobielwerk, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, b.opmerkingen_vertreklijst, b.aantalpersonen, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY SUBSTRING(b.boekingsnummer,11), p.naam, a.naam, t.naam;");
		$db->query("SELECT SUBSTRING(b.boekingsnummer,2,6) AS boekingsnummer1, SUBSTRING(b.boekingsnummer,11) AS boekingsnummer2, SUBSTRING(b.boekingsnummer,2,8) AS boekingsnummer, b.reisbureau_user_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.telefoonnummer, bp.mobielwerk, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, b.opmerkingen_vertreklijst, b.aantalpersonen, p.plaats_id, p.naam AS plaats, a.naam AS accommodatie, a.bestelnaam AS abestelnaam, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND ((b.verzameltype_gekozentype_id IS NULL AND b.type_id=t.type_id) OR (b.verzameltype_gekozentype_id>0 AND b.verzameltype_gekozentype_id=t.type_id)) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY SUBSTRING(b.boekingsnummer,11), SUBSTRING(b.boekingsnummer,2,8), p.naam, a.naam, t.naam;");
		if($db->num_rows()) {
			$ms->html.="<table border=\"1\" bordercolor=\"#000000\" cellpadding=\"5\" cellspacing=\"0\"><thead>";
			$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Volgnr.</th><th>Res.<br>nr.</th><th>Hoofdboeker</th><th>Telefoon<br>hoofdboeker</th><th>Aankomst</th><th>Plaats</th><th>Accommodatie</th><th>Type</th><th>Cap.</th><th>Pers.</th><th>Leverancier</th><th>Res.<br>code</th></tr></thead>";
			while($db->next_record()) {
				if($db->f("telefoonnummer") and $db->f("mobielwerk")) {
					$telefoon=$db->f("telefoonnummer")."<br>".$db->f("mobielwerk");
				} elseif($db->f("telefoonnummer")) {
					$telefoon=$db->f("telefoonnummer");
				} elseif($db->f("mobielwerk")) {
					$telefoon=$db->f("mobielwerk");
				} else {
					$telefoon="&nbsp;";
				}
				$accnaam=$db->f("accommodatie")." ".($db->f("type") ? $db->f("type")." " : "")."(".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." p)";
				if($db->f("accommodatie")<>$db->f("abestelnaam")) {
					$accnaam_toevoeging=" <i>(".htmlentities($db->f("abestelnaam").($db->f("type") ? " ".$db->f("type") : "")).")</i>";
				} else {
					$accnaam_toevoeging="";
				}
				$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">".($db->f("boekingsnummer2") ? $db->f("boekingsnummer2") : $db->f("boekingsnummer"))."</td><td valign=\"top\">".($db->f("boekingsnummer2") ? $db->f("boekingsnummer1") : $db->f("boekingsnummer"))."</td><td valign=\"top\">".htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"))).($db->f("opmerkingen_vertreklijst") ? " (".htmlentities(trim($db->f("opmerkingen_vertreklijst"))).")" : "").($vars["reisbureau_naam"][$db->f("reisbureau_user_id")] ? htmlentities(" (via ".$vars["reisbureau_naam"][$db->f("reisbureau_user_id")].")") : "")."</td><td valign=\"top\">".$telefoon."</td><td valign=\"top\" nowrap>".date("d-m-Y",$db->f("aankomstdatum_exact"))."</td><td valign=\"top\">".htmlentities($db->f("plaats"))."</td><td valign=\"top\">".htmlentities($accnaam).$accnaam_toevoeging."</td><td valign=\"top\">".($db->f("code") ? htmlentities($db->f("code")) : "&nbsp;")."</td><td valign=\"top\">".$db->f("maxaantalpersonen")."</td><td valign=\"top\">".$db->f("aantalpersonen")."</td><td valign=\"top\">".htmlentities($db->f("leverancier"))."</td><td valign=\"top\">".($db->f("leverancierscode") ? htmlentities($db->f("leverancierscode")) : "&nbsp;")."</td></tr>";
			}
			$ms->html.="</table>";
		}
	}
} elseif($_GET["t"]==5 or $_GET["t"]==9) {
	# CSV-opties

	if($_GET["t"]==5) {
		cmslog_pagina_title("Overzichten - Bestellijst opties");
	} elseif($_GET["t"]==9) {
		cmslog_pagina_title("Overzichten - Interne lijst opties");	
	}

	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		echo "<pre>";
	} else {
		$db->query("SELECT naam FROM optieleverancier WHERE optieleverancier_id='".addslashes($_GET["olid"])."';");
		if($db->next_record()) {
			$optieleverancier=ereg_replace("[^A-Za-z0-9]","_",strtolower($db->f("naam")));
			$optieleverancier=ereg_replace("_{2,}","_",$optieleverancier);
		}
		if($_GET["t"]==5) {
			$filename="orderlist_".$optieleverancier."_".date("d_m_Y",$_GET["date"]).".csv";
		} else {
			if($_GET["date"]) {
				$filename="internelist_".$optieleverancier."_".date("d_m_Y",$_GET["date"]).".csv";
			} else {
				$filename="internelist_".$optieleverancier."_".date("d_m_Y",$_GET["bdate"])."_".date("d_m_Y",$_GET["edate"]).".csv";
			}
		}
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
	}

	if($_GET["date"]) {
		$periode="b.aankomstdatum='".addslashes($_GET["date"])."'";
	} else {
		$periode="b.aankomstdatum>='".addslashes($_GET["bdate"])."' AND b.aankomstdatum<='".addslashes($_GET["edate"])."'";
	}

	$delimiter=";";
	if($_GET["t"]==5) {
		# Bestellijst
		echo "Code TO".$delimiter."Num".$delimiter."Name".$delimiter."Arrival Date".$delimiter."Resort".$delimiter."Pack".$delimiter."Qt".$delimiter."Duration\n";
	} else {
		# Interne lijst
		echo "Num".$delimiter."Naam".$delimiter."Aankomst".$delimiter."Resort".$delimiter."Pack".$delimiter."Aantal".$delimiter."Duur".$delimiter."Bruto".$delimiter."Korting".$delimiter."Netto\n";
	}
	
	# Plaats-codes uit database halen
	$db->query("SELECT leverancierscode, plaats_id FROM plaats_optieleverancier WHERE optieleverancier_id='".addslashes($_GET["olid"])."';");
	while($db->next_record()) {
		$plaatscode[$db->f("plaats_id")]=$db->f("leverancierscode");
	}
	
	# Gewone opties
	if($_GET["t"]==5) {
		# Bestellijst
		$db->query("SELECT bp.voornaam, bp.tussenvoegsel, bp.achternaam, b.boekingsnummer, b.aankomstdatum_exact, oo.leverancierscode, oo.omschrijving_voucher, oo.naam AS optieonderdeel, og.duur, COUNT(*) AS aantal, p.naam AS plaats, p.plaats_id FROM optieleverancier ol, optie_accommodatie oa, optie_groep og, boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l, optie_onderdeel oo, boeking_optie bo WHERE ol.optieleverancier_id='".addslashes($_GET["olid"])."' AND og.optieleverancier_id=ol.optieleverancier_id AND bo.boeking_id=b.boeking_id AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oa.optie_groep_id=og.optie_groep_id AND oo.optie_groep_id=og.optie_groep_id AND oa.accommodatie_id=a.accommodatie_id AND ".$periode." AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 GROUP BY b.boeking_id, oo.optie_onderdeel_id ORDER BY b.boekingsnummer;");
	} else {
		# Interne lijst
		$db->query("SELECT bp.voornaam, bp.tussenvoegsel, bp.achternaam, b.boekingsnummer, b.aankomstdatum_exact, oo.leverancierscode, oo.omschrijving_voucher, oo.naam AS optieonderdeel, og.duur, COUNT(*) AS aantal, p.naam AS plaats, p.plaats_id, ot.verkoop, ot.inkoop, ot.korting FROM optie_tarief ot, optieleverancier ol, optie_accommodatie oa, optie_groep og, boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l, optie_onderdeel oo, boeking_optie bo WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND ot.seizoen_id=b.seizoen_id AND ot.week=b.aankomstdatum AND ol.optieleverancier_id='".addslashes($_GET["olid"])."' AND og.optieleverancier_id=ol.optieleverancier_id AND bo.boeking_id=b.boeking_id AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oa.optie_groep_id=og.optie_groep_id AND oo.optie_groep_id=og.optie_groep_id AND oa.accommodatie_id=a.accommodatie_id AND ".$periode." AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 GROUP BY b.boeking_id, oo.optie_onderdeel_id ORDER BY b.boekingsnummer;");
	}
	while($db->next_record()) {
		$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
		if($_GET["t"]==5) {
			if($plaatscode[$db->f("plaats_id")]) {
				$plaats=$plaatscode[$db->f("plaats_id")];
			} else {
				$plaats=$db->f("plaats").": NOG GEEN PLAATSCODE";
			}
			$levcode=$db->f("leverancierscode");
			if(!$levcode) $levcode="NOG GEEN BESTELCODE: ".$db->f("optieonderdeel");
		} else {
			$plaats=$db->f("plaats");
			$levcode=$db->f("omschrijving_voucher");
			if($db->f("korting")<>0) {
				$netto=$db->f("inkoop")*(1-$db->f("korting")/100);
			} else {
				$netto=$db->f("inkoop");
			}
		}
		if(ereg($delimiter,$naam)) $naam="'".$naam."'";
		if(ereg($delimiter,$plaats)) $plaats="'".$plaats."'";
		if(ereg($delimiter,$levcode)) $levcode="'".$levcode."'";
		if($_GET["t"]==5) {
			# Bestellijst
			echo "TO".$delimiter.$db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$plaats.$delimiter.$levcode.$delimiter.$db->f("aantal").$delimiter.$db->f("duur")."\n";
		} else {
			# Interne lijst
			echo $db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$plaats.$delimiter.$levcode.$delimiter.$db->f("aantal").$delimiter.$db->f("duur").$delimiter.number_format($db->f("inkoop"),2,",","").$delimiter.number_format($db->f("korting"),2,",","").$delimiter.number_format($netto,2,",","")."\n";
		}
	}
	
	# Handmatige opties
	$db->query("SELECT DISTINCT bp.voornaam, bp.tussenvoegsel, bp.achternaam, b.boekingsnummer, b.aankomstdatum_exact, e.leverancierscode, e.verberg_voor_klant, e.omschrijving_voucher, e.naam AS optieonderdeel, e.begindag, e.einddag, e.deelnemers, e.verkoop, e.inkoop, e.korting, p.naam AS plaats, p.plaats_id FROM optieleverancier ol, extra_optie e, boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE (e.voucher=1 OR e.verberg_voor_klant=1) AND e.deelnemers<>'' AND e.optieleverancier_id='".addslashes($_GET["olid"])."' AND e.boeking_id=b.boeking_id AND ".$periode." AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY b.boekingsnummer;");
	while($db->next_record()) {
		$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
		if($_GET["t"]==5) {
			if($plaatscode[$db->f("plaats_id")]) {
				$plaats=$plaatscode[$db->f("plaats_id")];
			} else {
				$plaats=$db->f("plaats").": NOG GEEN PLAATSCODE";
			}
			$levcode=$db->f("leverancierscode");
			if(!$levcode) $levcode="NOG GEEN BESTELCODE: ".$db->f("optieonderdeel");
		} else {
			$plaats=$db->f("plaats");
			if($db->f("verberg_voor_klant")) {
				$levcode=$db->f("optieonderdeel");
			} else {
				$levcode=$db->f("omschrijving_voucher");			
			}
			if($db->f("korting")<>0) {
				$netto=$db->f("inkoop")*(1-$db->f("korting")/100);
			} else {
				$netto=$db->f("inkoop");
			}
		}
		$aantal=@count(split(",",$db->f("deelnemers")));
		$duur=7-$db->f("begindag")+$db->f("einddag");
		if(ereg($delimiter,$naam)) $naam="'".$naam."'";
		if(ereg($delimiter,$plaats)) $plaats="'".$plaats."'";
		if(ereg($delimiter,$levcode)) $levcode="'".$levcode."'";
		if($_GET["t"]==5) {
			# Bestellijst
			echo "TO".$delimiter.$db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$plaats.$delimiter.$levcode.$delimiter.$aantal.$delimiter.$duur."\n";
		} else {
			# Interne lijst
			echo $db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$plaats.$delimiter.$levcode.$delimiter.$aantal.$delimiter.$duur.$delimiter.number_format($db->f("inkoop"),2,",","").$delimiter.number_format($db->f("korting"),2,",","").$delimiter.number_format($netto,2,",","")."\n";
		}
	}
#	exit;
} elseif($_GET["t"]==6) {

	cmslog_pagina_title("Overzichten - Skipas-bestellijst");

	$ms->landscape=false;
	# Skipas-bestellijsten
	$db->query("SELECT naam, omschrijving_voucher FROM skipas WHERE skipas_id='".addslashes($_GET["spid"])."';");
	if($db->next_record()) {
		$skipas["naam"]=$db->f("naam");
		$skipas["omschrijving_voucher"]=$db->f("omschrijving_voucher");
		$leverancier=ereg_replace("[^A-Za-z0-9]","_",strtolower($db->f("naam")));
		$leverancier=ereg_replace("_{2,}","_",$leverancier);
	}

	$ms->filename="orderlist_".$leverancier."_".date("d_m_Y",$_GET["date"]);
	$ms->html.="<table width=100% border=\"1\" cellpadding=\"0\" cellspacing=\"0\"><thead>";
	$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><td>";
	$ms->html.="<table width=100% border=0><tr><td><h3>";
#	$ms->html.="Orderlist ".htmlentities($skipas["naam"])." - ".date("d-m-Y",$_GET["date"]);
	$ms->html.="Chalet.nl / Wintersportaccommodaties.nl</h3>Chalet.nl B.V. - Lindenhof 5 - 3442 GT Woerden - The Netherlands<br>Tel: +31 348 - 43 46 49 - Fax: +31 348 - 69 07 52 - info@chalet.nl</td><td align=right>";
	$ms->html.="<img width=92 height=79 src=\"http://www.chalet.nl/pic/factuur_logo_vakantiewoningen.png\"></td></tr></table>";
	$ms->html.="</td></tr></thead>";
#	$ms->html.="<tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'><th>Clientsname</th><th>Arrival</th><th>Departure</th><th>Resort</th><th>Accommodation</th><th>Type</th><th>Cap.</th><th>Reserv.<br>Nr.</th><th>Extra<br>Options</th></tr></thead>";
	$ms->html.="<tr><td><table border=0 width=100%><tr><td>Dear reservation team of <b>".htmlentities($skipas["naam"])."</b>,<p>We want to make the following reservation(s) for the period from the <b>".date("d-m-Y",$_GET["date"])."</b></td></tr></table></td></tr>";

	# Gewone skipassen
	$db->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.aantalpersonen, b.seizoen_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode, s.begindag, st.bruto, st.korting, st.netto FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, land, leverancier l, skipas s, skipas_tarief st WHERE b.wederverkoop=0 AND st.skipas_id=s.skipas_id AND st.seizoen_id=b.seizoen_id AND st.week='".addslashes($_GET["date"])."' AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND a.skipas_id=s.skipas_id AND s.skipas_id='".addslashes($_GET["spid"])."' AND b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY b.boekingsnummer;");
	while($db->next_record()) {
		unset($afwijkteller,$afwijkende_skipas,$deelnemer,$deelnemers1,$deelnemers2,$deelnemersteller,$not_in);
		#
		# Kijken welke deelnemers een afwijkende skipas hebben
		#
		# Gewone opties
		$db2->query("SELECT bo.persoonnummer FROM boeking_optie bo, optie_groep og, optie_onderdeel oo WHERE og.skipas_id>0 AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".addslashes($db->f("boeking_id"))."';");
		while($db2->next_record()) {
			$afwijkende_skipas[$db2->f("persoonnummer")]=true;
			if($not_in) $not_in.=",".$db2->f("persoonnummer"); else $not_in=$db2->f("persoonnummer");
		}
		# Handmatige opties
		$db2->query("SELECT deelnemers FROM extra_optie WHERE boeking_id='".addslashes($db->f("boeking_id"))."' AND skipas_id>0;");
		while($db2->next_record()) {
			$tempdeelnemers=@split(",",$db2->f("deelnemers"));
			while(list($key,$value)=@each($tempdeelnemers)) {
				$afwijkende_skipas[$value]=true;
				if($not_in) $not_in.=",".$value; else $not_in=$value;
			}
		}
	
		if(@count($afwijkende_skipas)<$db->f("aantalpersonen")) {

			#
			# Gewone skipas
			#
			$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">";
			$ms->html.="<table border=0 width=100%>";
			
			# Deelnemers bepalen
			$db2->query("SELECT bp.persoonnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking_persoon bp WHERE bp.boeking_id=".$db->f("boeking_id").($not_in ? " AND bp.persoonnummer NOT IN (".$not_in.")" : "")." ORDER BY bp.achternaam, bp.voornaam, bp.tussenvoegsel;");
			$helft=ceil($db2->num_rows()/2);
			while($db2->next_record()) {
				$deelnemersteller++;
				$deelnemer="<br>".substr("0".$deelnemersteller,-2)." ";
				if($db2->f("achternaam")) {
					$deelnemer.=htmlentities(wt_naam($db2->f("voornaam"),$db2->f("tussenvoegsel"),$db2->f("achternaam")));
				} else {
					$deelnemer.=" --- ";
				}
				if($deelnemersteller<=$helft) {
					$deelnemers1.=$deelnemer;
				} else {
					$deelnemers2.=$deelnemer;
				}
			}
			$deelnemers1=substr($deelnemers1,4);
			$deelnemers2=substr($deelnemers2,4);
	
			$ms->html.="<tr><td colspan=2><hr></td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_reservering"][$db->f("begincode")])."</td><td>".$db->f("boekingsnummer")." - ".htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam")))."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_plaats"][$db->f("begincode")])."</td><td>".htmlentities($db->f("plaats"))."</td></tr>";
			$ms->html.="<tr><td colspan=2>&nbsp;</td></tr>";
			
			
			if($db->f("begindag")) {
				$aanvangsdatum=mktime(0,0,0,date("m",$db->f("aankomstdatum_exact")),date("d",$db->f("aankomstdatum_exact"))+$db->f("begindag"),date("Y",$db->f("aankomstdatum_exact")));
			} else {
				$aanvangsdatum=$db->f("aankomstdatum_exact");
			}
			
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_eerstedag"][$db->f("begincode")])."</td><td>".date("d-m-Y",$aanvangsdatum)."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_type"][$db->f("begincode")])."</td><td>".($db->f("aantalpersonen")-@count($afwijkende_skipas))."x ".htmlentities($skipas["omschrijving_voucher"])."</td></tr>";
				
			if($db->f("korting")<>0) {
				$price=number_format($db->f("bruto"),2,',','.');
				$price.=" -/- ".number_format($db->f("korting"),2,',','.')."%";
			} else {
				$price=number_format($db->f("netto"),2,',','.');
			}
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_prijs"][$db->f("begincode")])."</td><td>EURO&nbsp;".$price."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_deelnemers"][$db->f("begincode")])."</td><td><table width=100% cellspacing=0 cellpadding=0><tr><td width=50% valign=top>".$deelnemers1."</td><td>&nbsp;</td><td width=50% valign=top>".$deelnemers2."</td></tr></table></td></tr>";
			$ms->html.="</table></td></tr>";
		}

		# Afwijkende skipassen
		$db2->query("SELECT oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, COUNT(*) AS aantalpersonen FROM boeking_optie bo, optie_groep og, optie_onderdeel oo WHERE oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".$db->f("boeking_id")."' GROUP BY bo.optie_onderdeel_id;");
		while($db2->next_record()) {
			# Deelnemers bepalen
			unset($deelnemersteller,$deelnemers1,$deelnemers2,$deelnemer);
			$db3->query("SELECT bp.persoonnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking_persoon bp, boeking_optie bo WHERE bo.boeking_id=bp.boeking_id AND bo.optie_onderdeel_id='".$db2->f("optie_onderdeel_id")."' AND bo.persoonnummer=bp.persoonnummer AND bp.boeking_id=".$db->f("boeking_id").($not_in ? " AND bp.persoonnummer IN (".$not_in.")" : "")." ORDER BY bp.achternaam, bp.voornaam, bp.tussenvoegsel;");
			$helft=ceil($db3->num_rows()/2);
			while($db3->next_record()) {
				$deelnemersteller++;
				$deelnemer="<br>".substr("0".$deelnemersteller,-2)." ";
				if($db3->f("achternaam")) {
					$deelnemer.=htmlentities(wt_naam($db3->f("voornaam"),$db3->f("tussenvoegsel"),$db3->f("achternaam")));
				} else {
					$deelnemer.=" --- ";
				}
				if($deelnemersteller<=$helft) {
					$deelnemers1.=$deelnemer;
				} else {
					$deelnemers2.=$deelnemer;
				}
			}
			$deelnemers1=substr($deelnemers1,4);
			$deelnemers2=substr($deelnemers2,4);
		
		
			$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">";
			$ms->html.="<table border=0 width=100%>";

			$ms->html.="<tr><td colspan=2><hr></td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_reservering"][$db->f("begincode")])."</td><td>".$db->f("boekingsnummer")." - ".htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam")))."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_plaats"][$db->f("begincode")])."</td><td>".htmlentities($db->f("plaats"))."</td></tr>";
			$ms->html.="<tr><td colspan=2>&nbsp;</td></tr>";

			if($db2->f("begindag")) {
				$aanvangsdatum=mktime(0,0,0,date("m",$db->f("aankomstdatum_exact")),date("d",$db->f("aankomstdatum_exact"))+$db2->f("begindag"),date("Y",$db->f("aankomstdatum_exact")));
			} else {
				$aanvangsdatum=$db->f("aankomstdatum_exact");
			}

			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_eerstedag"][$db->f("begincode")])."</td><td>".date("d-m-Y",$aanvangsdatum)."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_type"][$db->f("begincode")])."</td><td>".$db2->f("aantalpersonen")."x ".($db2->f("omschrijving_voucher") ? htmlentities($db2->f("omschrijving_voucher")) : "GEEN VOUCHEROMSCHRIJVING INGEVOERD: ".htmlentities($db2->f("naam")))."</td></tr>";

			$db3->query("SELECT inkoop, korting FROM optie_tarief WHERE optie_onderdeel_id='".$db2->f("optie_onderdeel_id")."' AND seizoen_id='".$db->f("seizoen_id")."' AND week='".addslashes($_GET["date"])."';");
			if($db3->next_record()) {
				$price2="EURO&nbsp;".number_format($db3->f("inkoop"),2,',','.');
				if($db3->f("korting")>0) $price2.=" -/- ".number_format($db3->f("korting"),2,',','.')."%";
			} else {
				$price2="[NIET INGEVOERD]";
			}
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_prijs"][$db->f("begincode")])."</td><td>".$price2."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_deelnemers"][$db->f("begincode")])."</td><td><table width=100% cellspacing=0 cellpadding=0><tr><td width=50% valign=top>".$deelnemers1."</td><td>&nbsp;</td><td width=50% valign=top>".$deelnemers2."</td></tr></table></td></tr>";
			$ms->html.="</table></td></tr>";
		}
		
		# Skipassen bij handmatige opties
		$db2->query("SELECT e.extra_optie_id, e.deelnemers, e.naam, e.omschrijving_voucher, e.begindag, e.inkoop, e.korting FROM extra_optie e WHERE e.deelnemers<>'' AND e.voucher=1 AND e.skipas_id='".addslashes($_GET["spid"])."' AND e.boeking_id='".$db->f("boeking_id")."' ORDER BY naam;");
		while($db2->next_record()) {
			if($extra_optie) $extra_optie.=",".$db2->f("extra_optie_id"); else $extra_optie=$db2->f("extra_optie_id");

			# Deelnemers bepalen
			unset($deelnemersteller,$deelnemers1,$deelnemers2,$deelnemer);
			if($db2->f("deelnemers")) {
				$db3->query("SELECT bp.persoonnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking_persoon bp WHERE bp.persoonnummer IN (".$db2->f("deelnemers").") AND bp.boeking_id=".$db->f("boeking_id")." ORDER BY bp.achternaam, bp.voornaam, bp.tussenvoegsel;");
				$helft=ceil($db3->num_rows()/2);
				while($db3->next_record()) {
					$deelnemersteller++;
					$deelnemer="<br>".substr("0".$deelnemersteller,-2)." ";
					if($db3->f("achternaam")) {
						$deelnemer.=htmlentities(wt_naam($db3->f("voornaam"),$db3->f("tussenvoegsel"),$db3->f("achternaam")));
					} else {
						$deelnemer.=" --- ";
					}
					if($deelnemersteller<=$helft) {
						$deelnemers1.=$deelnemer;
					} else {
						$deelnemers2.=$deelnemer;
					}
				}
				$deelnemers1=substr($deelnemers1,4);
				$deelnemers2=substr($deelnemers2,4);
			}
		
			$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">";
			$ms->html.="<table border=0 width=100%>";

			$ms->html.="<tr><td colspan=2><hr></td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_reservering"][$db->f("begincode")])."</td><td>".$db->f("boekingsnummer")." - ".htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam")))."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_plaats"][$db->f("begincode")])."</td><td>".htmlentities($db->f("plaats"))."</td></tr>";
			$ms->html.="<tr><td colspan=2>&nbsp;</td></tr>";

			if($db2->f("begindag")) {
				$aanvangsdatum=mktime(0,0,0,date("m",$db->f("aankomstdatum_exact")),date("d",$db->f("aankomstdatum_exact"))+$db2->f("begindag"),date("Y",$db->f("aankomstdatum_exact")));
			} else {
				$aanvangsdatum=$db->f("aankomstdatum_exact");
			}

			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_eerstedag"][$db->f("begincode")])."</td><td>".date("d-m-Y",$aanvangsdatum)."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_type"][$db->f("begincode")])."</td><td>".$deelnemersteller."x ".($db2->f("omschrijving_voucher") ? htmlentities($db2->f("omschrijving_voucher")) : "GEEN VOUCHEROMSCHRIJVING INGEVOERD: ".htmlentities($db2->f("naam")))."</td></tr>";

			$price2="EURO&nbsp;".number_format($db2->f("inkoop"),2,',','.');
			if($db2->f("korting")>0) $price2.=" -/- ".number_format($db2->f("korting"),2,',','.')."%";

			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_prijs"][$db->f("begincode")])."</td><td>".$price2."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_deelnemers"][$db->f("begincode")])."</td><td><table width=100% cellspacing=0 cellpadding=0><tr><td width=50% valign=top>".$deelnemers1."</td><td>&nbsp;</td><td width=50% valign=top>".$deelnemers2."</td></tr></table></td></tr>";
			$ms->html.="</table></td></tr>";
		}
	}

	# Skipassen bij handmatige opties (bij een boeking met accommodatie met andere/geen skipas)
	$db->query("SELECT e.extra_optie_id, e.deelnemers, e.naam, e.omschrijving_voucher, e.begindag, e.inkoop, e.korting, b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.aantalpersonen, b.seizoen_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, land, leverancier l, skipas s, extra_optie e WHERE e.boeking_id=b.boeking_id AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND e.skipas_id=s.skipas_id AND s.skipas_id='".addslashes($_GET["spid"])."' AND b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND b.goedgekeurd=1 AND b.geannuleerd=0 ".($extra_optie ? " AND extra_optie_id NOT IN (".$extra_optie.")" : "")." ORDER BY b.boekingsnummer;");
	while($db->next_record()) {
		# Deelnemers bepalen
		unset($deelnemersteller,$deelnemers1,$deelnemers2,$deelnemer);
		if($db->f("deelnemers")) {
			$db3->query("SELECT bp.persoonnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking_persoon bp WHERE bp.persoonnummer IN (".$db->f("deelnemers").") AND bp.boeking_id=".$db->f("boeking_id")." ORDER BY bp.achternaam, bp.voornaam, bp.tussenvoegsel;");
			$helft=ceil($db3->num_rows()/2);
			while($db3->next_record()) {
				$deelnemersteller++;
				$deelnemer="<br>".substr("0".$deelnemersteller,-2)." ";
				if($db3->f("achternaam")) {
					$deelnemer.=htmlentities(wt_naam($db3->f("voornaam"),$db3->f("tussenvoegsel"),$db3->f("achternaam")));
				} else {
					$deelnemer.=" --- ";
				}
				if($deelnemersteller<=$helft) {
					$deelnemers1.=$deelnemer;
				} else {
					$deelnemers2.=$deelnemer;
				}
			}
			$deelnemers1=substr($deelnemers1,4);
			$deelnemers2=substr($deelnemers2,4);
		}
	
		$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">";
		$ms->html.="<table border=0 width=100%>";

		$ms->html.="<tr><td colspan=2><hr></td></tr>";
		$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_reservering"][$db->f("begincode")])."</td><td>".$db->f("boekingsnummer")." - ".htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam")))."</td></tr>";
		$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_plaats"][$db->f("begincode")])."</td><td>".htmlentities($db->f("plaats"))."</td></tr>";
		$ms->html.="<tr><td colspan=2>&nbsp;</td></tr>";

		if($db->f("begindag")) {
			$aanvangsdatum=mktime(0,0,0,date("m",$db->f("aankomstdatum_exact")),date("d",$db->f("aankomstdatum_exact"))+$db->f("begindag"),date("Y",$db->f("aankomstdatum_exact")));
		} else {
			$aanvangsdatum=$db->f("aankomstdatum_exact");
		}

		$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_eerstedag"][$db->f("begincode")])."</td><td>".date("d-m-Y",$aanvangsdatum)."</td></tr>";
		$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_type"][$db->f("begincode")])."</td><td>".$deelnemersteller."x ".($db->f("omschrijving_voucher") ? htmlentities($db->f("omschrijving_voucher")) : "GEEN VOUCHEROMSCHRIJVING INGEVOERD: ".htmlentities($db->f("naam")))."</td></tr>";

		$price2="EURO&nbsp;".number_format($db->f("inkoop"),2,',','.');
		if($db->f("korting")>0) $price2.=" -/- ".number_format($db->f("korting"),2,',','.')."%";
		$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_prijs"][$db->f("begincode")])."</td><td>".$price2."</td></tr>";
		$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_deelnemers"][$db->f("begincode")])."</td><td><table width=100% cellspacing=0 cellpadding=0><tr><td width=50% valign=top>".$deelnemers1."</td><td>&nbsp;</td><td width=50% valign=top>".$deelnemers2."</td></tr></table></td></tr>";
		$ms->html.="</table></td></tr>";
	}

	# Losse skipassen (bij accommodaties zonder skipas) (os.losse_skipas=1)
#	$db2->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.aantalpersonen, b.seizoen_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode, oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, COUNT(*) AS aantalpersonen FROM boeking_optie bo, boeking b, boeking_persoon bp, optie_groep og, optie_onderdeel oo, optie_soort os, type t, accommodatie a, plaats p, land WHERE oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id=b.boeking_id AND b.aankomstdatum='".addslashes($_GET["date"])."' AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND og.optie_soort_id=os.optie_soort_id AND os.losse_skipas=1 GROUP BY bo.optie_onderdeel_id;");
	$db2->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.aantalpersonen, b.seizoen_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode, oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, COUNT(*) AS aantalpersonen FROM boeking_optie bo, boeking b, boeking_persoon bp, optie_groep og, optie_onderdeel oo, optie_soort os, type t, accommodatie a, plaats p, land WHERE oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id=b.boeking_id AND b.aankomstdatum='".addslashes($_GET["date"])."' AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND og.optie_soort_id=os.optie_soort_id AND os.losse_skipas=1 GROUP BY b.boeking_id;");
#echo $db2->lastquery;	
	while($db2->next_record()) {
		#
		# Kijken welke deelnemers een afwijkende skipas hebben
		#
		unset($afwijkende_skipas,$not_in);
		# Gewone opties
		$db3->query("SELECT bo.persoonnummer FROM boeking_optie bo, optie_groep og, optie_onderdeel oo, optie_soort os WHERE og.optie_soort_id=os.optie_soort_id AND og.skipas_id>0 AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND os.losse_skipas=0 AND bo.boeking_id='".addslashes($db2->f("boeking_id"))."';");
		while($db3->next_record()) {
			$afwijkende_skipas[$db3->f("persoonnummer")]=true;
			if($not_in) $not_in.=",".$db3->f("persoonnummer"); else $not_in=$db3->f("persoonnummer");
		}
		
		# Handmatige opties
		$db3->query("SELECT deelnemers FROM extra_optie WHERE boeking_id='".addslashes($db2->f("boeking_id"))."' AND skipas_id>0;");
		while($db3->next_record()) {
			$tempdeelnemers=@split(",",$db3->f("deelnemers"));
			while(list($key,$value)=@each($tempdeelnemers)) {
				$afwijkende_skipas[$value]=true;
				if($not_in) $not_in.=",".$value; else $not_in=$value;
			}
		}

		# Deelnemers basis-skipas bepalen
		unset($deelnemersteller,$deelnemers1,$deelnemers2,$deelnemer);
		$db3->query("SELECT bp.persoonnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking_persoon bp, boeking_optie bo WHERE bo.boeking_id=bp.boeking_id AND bo.optie_onderdeel_id='".$db2->f("optie_onderdeel_id")."' AND bo.persoonnummer=bp.persoonnummer AND bp.boeking_id=".$db2->f("boeking_id")." AND bp.persoonnummer=bo.persoonnummer".($not_in ? " AND bp.persoonnummer NOT IN (".$not_in.")" : "")." ORDER BY bp.achternaam, bp.voornaam, bp.tussenvoegsel;");
		if($db3->num_rows()) {
			$helft=ceil($db3->num_rows()/2);
			while($db3->next_record()) {
				$deelnemersteller++;
				$deelnemer="<br>".substr("0".$deelnemersteller,-2)." ";
				if($db3->f("achternaam")) {
					$deelnemer.=htmlentities(wt_naam($db3->f("voornaam"),$db3->f("tussenvoegsel"),$db3->f("achternaam")));
				} else {
					$deelnemer.=" --- ";
				}
				if($deelnemersteller<=$helft) {
					$deelnemers1.=$deelnemer;
				} else {
					$deelnemers2.=$deelnemer;
				}
			}
			$deelnemers1=substr($deelnemers1,4);
			$deelnemers2=substr($deelnemers2,4);
		
		
			$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">";
			$ms->html.="<table border=0 width=100%>";
	
			$ms->html.="<tr><td colspan=2><hr></td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_reservering"][$db2->f("begincode")])."</td><td>".$db2->f("boekingsnummer")." - ".htmlentities(wt_naam($db2->f("voornaam"),$db2->f("tussenvoegsel"),$db2->f("achternaam")))."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_plaats"][$db2->f("begincode")])."</td><td>".htmlentities($db2->f("plaats"))."</td></tr>";
			$ms->html.="<tr><td colspan=2>&nbsp;</td></tr>";
	
			if($db2->f("begindag")) {
				$aanvangsdatum=mktime(0,0,0,date("m",$db2->f("aankomstdatum_exact")),date("d",$db2->f("aankomstdatum_exact"))+$db2->f("begindag"),date("Y",$db2->f("aankomstdatum_exact")));
			} else {
				$aanvangsdatum=$db2->f("aankomstdatum_exact");
			}
	
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_eerstedag"][$db2->f("begincode")])."</td><td>".date("d-m-Y",$aanvangsdatum)."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_type"][$db2->f("begincode")])."</td><td>".$deelnemersteller."x ".($db2->f("omschrijving_voucher") ? htmlentities($db2->f("omschrijving_voucher")) : "GEEN VOUCHEROMSCHRIJVING INGEVOERD: ".htmlentities($db2->f("naam")))."</td></tr>";
	
			$db4->query("SELECT verkoop, inkoop, korting FROM optie_tarief WHERE optie_onderdeel_id='".$db2->f("optie_onderdeel_id")."' AND seizoen_id='".$db2->f("seizoen_id")."' AND week='".addslashes($_GET["date"])."';");
			if($db4->next_record()) {
				if($db4->f("korting")>0) {
					$price2="EURO&nbsp;".number_format($db4->f("verkoop"),2,',','.');
					$price2.=" -/- ".number_format($db4->f("korting"),2,',','.')."%";
				} else {
					$price2="EURO&nbsp;".number_format($db4->f("inkoop"),2,',','.');
				}
			} else {
				$price2="[NIET INGEVOERD]";
			}
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_prijs"][$db2->f("begincode")])."</td><td>".$price2."</td></tr>";
			$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_deelnemers"][$db2->f("begincode")])."</td><td><table width=100% cellspacing=0 cellpadding=0><tr><td width=50% valign=top>".$deelnemers1."</td><td>&nbsp;</td><td width=50% valign=top>".$deelnemers2."</td></tr></table></td></tr>";
			$ms->html.="</table></td></tr>";
		}
		
		# Afwijkende skipassen (os.losse_skipas=0)
		$db3->query("SELECT oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, COUNT(*) AS aantalpersonen FROM boeking_optie bo, optie_groep og, optie_onderdeel oo, optie_soort os WHERE og.optie_soort_id=os.optie_soort_id AND os.losse_skipas=0 AND oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".$db2->f("boeking_id")."' GROUP BY bo.optie_onderdeel_id;");
		while($db3->next_record()) {
			# Deelnemers bepalen
			unset($deelnemersteller,$deelnemers1,$deelnemers2,$deelnemer);
			$db4->query("SELECT bp.persoonnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking_persoon bp, boeking_optie bo WHERE bo.boeking_id=bp.boeking_id AND bo.optie_onderdeel_id='".$db3->f("optie_onderdeel_id")."' AND bo.persoonnummer=bp.persoonnummer AND bp.boeking_id='".$db2->f("boeking_id")."' ORDER BY bp.achternaam, bp.voornaam, bp.tussenvoegsel;");
			$helft=ceil($db4->num_rows()/2);
			while($db4->next_record()) {
				$deelnemersteller++;
				$deelnemer="<br>".substr("0".$deelnemersteller,-2)." ";
				if($db4->f("achternaam")) {
					$deelnemer.=htmlentities(wt_naam($db4->f("voornaam"),$db4->f("tussenvoegsel"),$db4->f("achternaam")));
				} else {
					$deelnemer.=" --- ";
				}
				if($deelnemersteller<=$helft) {
					$deelnemers1.=$deelnemer;
				} else {
					$deelnemers2.=$deelnemer;
				}
			}
			$deelnemers1=substr($deelnemers1,4);
			$deelnemers2=substr($deelnemers2,4);
		
			if($deelnemersteller) {
				$ms->html.="<tr style='mso-yfti-irow:1;page-break-inside:avoid'><td valign=\"top\">";
				$ms->html.="<table border=0 width=100%>";
	
				$ms->html.="<tr><td colspan=2><hr></td></tr>";
				$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_reservering"][$db2->f("begincode")])."</td><td>".$db2->f("boekingsnummer")." - ".htmlentities(wt_naam($db2->f("voornaam"),$db2->f("tussenvoegsel"),$db2->f("achternaam")))."</td></tr>";
				$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_plaats"][$db2->f("begincode")])."</td><td>".htmlentities($db2->f("plaats"))."</td></tr>";
				$ms->html.="<tr><td colspan=2>&nbsp;</td></tr>";
	
				if($db3->f("begindag")) {
					$aanvangsdatum=mktime(0,0,0,date("m",$db2->f("aankomstdatum_exact")),date("d",$db2->f("aankomstdatum_exact"))+$db3->f("begindag"),date("Y",$db2->f("aankomstdatum_exact")));
				} else {
					$aanvangsdatum=$db2->f("aankomstdatum_exact");
				}
	
				$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_eerstedag"][$db2->f("begincode")])."</td><td>".date("d-m-Y",$aanvangsdatum)."</td></tr>";
				$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_type"][$db2->f("begincode")])."</td><td>".$db3->f("aantalpersonen")."x ".($db3->f("omschrijving_voucher") ? htmlentities($db3->f("omschrijving_voucher")) : "GEEN VOUCHEROMSCHRIJVING INGEVOERD: ".htmlentities($db3->f("naam")))."</td></tr>";
	
				$db0->query("SELECT inkoop, korting FROM optie_tarief WHERE optie_onderdeel_id='".$db3->f("optie_onderdeel_id")."' AND seizoen_id='".$db2->f("seizoen_id")."' AND week='".addslashes($_GET["date"])."';");
				if($db0->next_record()) {
					$price2="EURO&nbsp;".number_format($db0->f("inkoop"),2,',','.');
					if($db0->f("korting")>0) $price2.=" -/- ".number_format($db0->f("korting"),2,',','.')."%";
				} else {
					$price2="[NIET INGEVOERD]";
				}
				$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_prijs"][$db2->f("begincode")])."</td><td>".$price2."</td></tr>";
				$ms->html.="<tr><td width=150 valign=top>".htmlentities($vars["voucher_deelnemers"][$db2->f("begincode")])."</td><td><table width=100% cellspacing=0 cellpadding=0><tr><td width=50% valign=top>".$deelnemers1."</td><td>&nbsp;</td><td width=50% valign=top>".$deelnemers2."</td></tr></table></td></tr>";
				$ms->html.="</table></td></tr>";
			}
		}
		
	}
	$ms->html.="</table>";
} elseif($_GET["t"]==7) {
	#
	# Etiketten met adressen
	#

	cmslog_pagina_title("Overzichten - Etiketten");

	$ms->filename="etiketten";
	$db->query("SELECT SUBSTRING(b.boekingsnummer,11) AS boekingsnummeroud, SUBSTR(b.boekingsnummer,2,8) AS boekingsnummer, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.adres, bp.postcode, bp.plaats, bp.land, b.aankomstdatum_exact, b.vertrekdatum_exact, b.leverancierscode, b.opmerkingen_voucher, b.aantalpersonen, b.reisbureau_user_id, p.plaats_id, a.naam AS accommodatie, t.naam AS type, t.optimaalaantalpersonen, t.maxaantalpersonen, t.code, l.naam AS leverancier FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, leverancier l WHERE b.aankomstdatum='".addslashes($_GET["date"])."' AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 AND b.verzendmethode_reisdocumenten<>1 ORDER BY SUBSTRING(b.boekingsnummer,11), SUBSTRING(b.boekingsnummer,2,8), p.naam, a.naam, t.naam;");
	if($db->num_rows()) {
		$ms->html="<table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0 style='border-collapse:collapse;mso-padding-top-alt:0cm;mso-padding-bottom-alt: 0cm'>";
		
		$height="93.5pt";
		
		if($_GET["col"]>1 or $_GET["row"]>1) {
			for($r=1;$r<=$_GET["row"];$r++) {
				$ms->html.="<tr style='mso-yfti-irow:".($r-1).";page-break-inside:avoid;height:".$height."'>";
				if($r==$_GET["row"]) {
					$col=$_GET["col"]-1;
				} else {
					$col=3;
				}
				for($c=1;$c<=$col;$c++) {
					$ms->html.="<td width=264 style='width:198.35pt;padding:0cm .75pt 0cm .75pt;height:".$height."'>&nbsp;</td>";
				}
				if($c==4) $ms->html.="</tr>\n";
			}
			if($_GET["col"]<4) {
				$col=$_GET["col"]-1;
			} else {
				unset($col);
			}
		}
		if(!$r) $r=1;
		while($db->next_record()) {
			if(!$col) {
				$ms->html.="<tr style='mso-yfti-irow:".($r-1).";page-break-inside:avoid;height:".$height."'>";
				$r++;
			}
			$col++;
			unset($naam,$adres,$postcode,$plaats,$land);
			# Reisbureau of gewoon persoon als adres?
			if($db->f("reisbureau_user_id")) {
				$db2->query("SELECT r.naam, r.adres, r.postcode, r.plaats, r.land FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id AND ru.user_id='".$db->f("reisbureau_user_id")."';");
				if($db2->next_record()) {
					$naam=$db2->f("naam");
					$adres=$db2->f("adres");
					$postcode=$db2->f("postcode");
					$plaats=$db2->f("plaats");
					$land=$db2->f("land");
				}
			} else {
				$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
				$adres=$db->f("adres");
				$postcode=$db->f("postcode");
				$plaats=$db->f("plaats");
				$land=$db->f("land");
			}
			$ms->html.="<td width=264 style='width:198.35pt;padding:0cm .75pt 0cm .75pt;height:".$height."'><div style=\"text-align:right;\"><font style=\"font-size:0.7em;\">".($db->f("boekingsnummeroud") ? $db->f("boekingsnummeroud") : $db->f("boekingsnummer"))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></div><p>".htmlentities($naam)."<br>".htmlentities($adres)."<br>".htmlentities($postcode." ".$plaats).(!eregi("nederland",$land) ? "<br>".htmlentities($land) : "")."</p></td>";
			if($col==3) {
				unset($col);
				$ms->html.="</tr>\n";
			}
		}
		$ms->html.="</table>";
	}
} elseif($_GET["t"]==8) {
	# CSV-skipassen (interne lijst)

	cmslog_pagina_title("Overzichten - Skipas interne lijst");

	$db->query("SELECT naam FROM skipas WHERE skipas_id='".addslashes($_GET["spid"])."';");
	if($db->next_record()) {
		$leverancier=ereg_replace("[^A-Za-z0-9]","_",strtolower($db->f("naam")));
		$leverancier=ereg_replace("_{2,}","_",$leverancier);
	}
	if($_GET["date"]) {
		$filename="internelist_".$leverancier."_".date("d_m_Y",$_GET["date"]).".csv";
	} else {
		$filename="internelist_".$leverancier."_".date("d_m_Y",$_GET["bdate"])."_".date("d_m_Y",$_GET["edate"]).".csv";
	}
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		echo "<pre>";
		echo "Filename: ".$filename."\n\n";
	} else {	
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
	}
	$delimiter=";";
	# Interne lijst kopregel
#	echo "Num".$delimiter."Naam".$delimiter."Aankomst".$delimiter."Resort".$delimiter."Pack".$delimiter."Aantal".$delimiter."Duur".$delimiter."Bruto".$delimiter."Korting".$delimiter."Netto".$delimiter."Skipas\n";
	echo "Num".$delimiter."Naam".$delimiter."Aankomst".$delimiter."Resort".$delimiter."Pack".$delimiter."Aantal".$delimiter."Duur".$delimiter."Bruto".$delimiter."Korting".$delimiter."Netto\n";
	
	if($_GET["date"]) {
		$periode="b.aankomstdatum='".addslashes($_GET["date"])."'";
	} else {
		$periode="b.aankomstdatum>='".addslashes($_GET["bdate"])."' AND b.aankomstdatum<='".addslashes($_GET["edate"])."'";
	}

	unset($afwijkteller,$afwijkende_skipas,$deelnemer,$deelnemers1,$deelnemers2,$deelnemersteller,$not_in);
	$db->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum, b.aankomstdatum_exact, b.vertrekdatum_exact, b.aantalpersonen, b.seizoen_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode, s.begindag, s.einddag, st.bruto, st.korting, st.netto, st.netto_ink, st.prijs, st.prijs, s.omschrijving_voucher, s.naam AS skipasnaam FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, land, leverancier l, skipas s, skipas_tarief st WHERE b.wederverkoop=0 AND st.skipas_id=s.skipas_id AND st.seizoen_id=b.seizoen_id AND st.week=b.aankomstdatum AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND a.skipas_id=s.skipas_id AND s.skipas_id='".addslashes($_GET["spid"])."' AND ".$periode." AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND b.goedgekeurd=1 AND b.geannuleerd=0 ORDER BY b.boekingsnummer;");
	while($db->next_record()) {
		unset($afwijkteller,$afwijkende_skipas,$deelnemer,$deelnemers1,$deelnemers2,$deelnemersteller,$not_in);
		
		$skipas=$db->f("prijs");
		
		#
		# Kijken hoeveel deelnemers een afwijkende skipas hebben
		#
		# Gewone opties
		$db2->query("SELECT bo.persoonnummer FROM boeking_optie bo, optie_groep og, optie_onderdeel oo WHERE og.skipas_id>0 AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".addslashes($db->f("boeking_id"))."';");
		while($db2->next_record()) {
			$afwijkende_skipas[$db2->f("persoonnummer")]=true;
		}
		# Handmatige opties
		$db2->query("SELECT deelnemers FROM extra_optie WHERE boeking_id='".addslashes($db->f("boeking_id"))."' AND skipas_id>0 AND verberg_voor_klant=0;");
		while($db2->next_record()) {
			$tempdeelnemers=@split(",",$db2->f("deelnemers"));
			while(list($key,$value)=@each($tempdeelnemers)) {
#				$afwijkteller++;
				$afwijkende_skipas[$value]=true;
			}
		}
		$afwijkteller=intval(@count($afwijkende_skipas));
		$duurreis=round(($db->f("vertrekdatum_exact")-$db->f("aankomstdatum_exact"))/86400)+1;
	
		# Gewone skipassen
		if($afwijkteller<$db->f("aantalpersonen")) {

			$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
			$resort=$db->f("plaats");
			$pack=$db->f("omschrijving_voucher");

			if(ereg($delimiter,$naam)) $naam="'".$naam."'";
			if(ereg($delimiter,$resort)) $resort="'".$resort."'";
			if(ereg($delimiter,$pack)) $pack="'".$pack."'";

			$bruto=$db->f("bruto");
			if($db->f("netto_ink")>0) {
				$korting=0;
			} else {
				$korting=$db->f("korting");
			}
			$netto=$db->f("netto_ink");

			# Duur bepalen
			$duur=intval($duurreis-$db->f("begindag")+$db->f("einddag"));
			
			echo $db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$resort.$delimiter.$pack.$delimiter.intval($db->f("aantalpersonen")-$afwijkteller).$delimiter.$duur.$delimiter.number_format($bruto,2,",","").$delimiter.number_format($korting,2,",","").$delimiter.number_format($netto,2,",","")."\n";
		}

		# Afwijkende skipassen
		$db2->query("SELECT oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, oo.begindag, oo.einddag, COUNT(*) AS aantalpersonen, ot.verkoop, ot.inkoop, ot.korting FROM boeking_optie bo, optie_groep og, optie_onderdeel oo, optie_tarief ot WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND ot.week='".$db->f("aankomstdatum")."' AND oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".$db->f("boeking_id")."' GROUP BY bo.optie_onderdeel_id;");
		while($db2->next_record()) {

			$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
			$resort=$db->f("plaats");
			$pack=$db2->f("omschrijving_voucher");

			if(ereg($delimiter,$naam)) $naam="'".$naam."'";
			if(ereg($delimiter,$resort)) $resort="'".$resort."'";
			if(ereg($delimiter,$pack)) $pack="'".$pack."'";

			if($db2->f("korting")<>0) {
				$netto=$db2->f("inkoop")*(1-$db2->f("korting")/100);
			} else {
				$netto=$db2->f("inkoop");
			}

			# Duur bepalen
			$duur=intval($duurreis-$db2->f("begindag")+$db2->f("einddag"));

			echo $db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$resort.$delimiter.$pack.$delimiter.$db2->f("aantalpersonen").$delimiter.$duur.$delimiter.number_format($db2->f("inkoop"),2,",","").$delimiter.number_format($db2->f("korting"),2,",","").$delimiter.number_format($netto,2,",","")."\n";
		}

		# Skipassen bij handmatige opties
		unset($aantalpersonen);
		$db2->query("SELECT e.extra_optie_id, e.deelnemers, e.naam, e.omschrijving_voucher, e.begindag, e.einddag, e.inkoop, e.korting FROM extra_optie e WHERE e.deelnemers<>'' AND e.skipas_id='".addslashes($_GET["spid"])."' AND e.boeking_id='".$db->f("boeking_id")."' ORDER BY naam;");
		while($db2->next_record()) {
			unset($aantalpersonen);
			if($extra_optie) $extra_optie.=",".$db2->f("extra_optie_id"); else $extra_optie=$db2->f("extra_optie_id");

			$tempdeelnemers=@split(",",$db2->f("deelnemers"));
			while(list($key,$value)=@each($tempdeelnemers)) {
				$aantalpersonen++;
			}
			$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
			$resort=$db->f("plaats");
			$pack=$db2->f("omschrijving_voucher");

			if(ereg($delimiter,$naam)) $naam="'".$naam."'";
			if(ereg($delimiter,$resort)) $resort="'".$resort."'";
			if(ereg($delimiter,$pack)) $pack="'".$pack."'";

			if($db2->f("korting")<>0) {
				$netto=$db2->f("inkoop")*(1-$db2->f("korting")/100);
			} else {
				$netto=$db2->f("inkoop");
			}

			$duur=intval($duurreis-$db2->f("begindag")+$db2->f("einddag"));

			echo $db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$resort.$delimiter.$pack.$delimiter.$aantalpersonen.$delimiter.$duur.$delimiter.number_format($db2->f("inkoop"),2,",","").$delimiter.number_format($db2->f("korting"),2,",","").$delimiter.number_format($netto,2,",","")."\n";
		}
	}

	# Skipassen bij handmatige opties (bij een boeking met accommodatie met andere/geen skipas)
	$db->query("SELECT e.extra_optie_id, e.deelnemers, e.naam, e.omschrijving_voucher, e.begindag, e.einddag, e.verkoop, e.inkoop, e.korting, b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.aantalpersonen, b.seizoen_id, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode, st.prijs FROM boeking b, boeking_persoon bp, type t, accommodatie a, plaats p, land, leverancier l, skipas s, skipas_tarief st, extra_optie e WHERE st.skipas_id=s.skipas_id AND st.seizoen_id=b.seizoen_id AND st.week=b.aankomstdatum AND e.boeking_id=b.boeking_id AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND e.skipas_id=s.skipas_id AND s.skipas_id='".addslashes($_GET["spid"])."' AND ".$periode." AND b.leverancier_id=l.leverancier_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND b.goedgekeurd=1 AND b.geannuleerd=0 ".($extra_optie ? " AND extra_optie_id NOT IN (".$extra_optie.")" : "")." ORDER BY b.boekingsnummer;");
	while($db->next_record()) {
		unset($aantalpersonen);

		$skipas=$db->f("prijs");

		$tempdeelnemers=@split(",",$db->f("deelnemers"));
		while(list($key,$value)=@each($tempdeelnemers)) {
			$aantalpersonen++;
		}

		$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
		$resort=$db->f("plaats");
		$pack=$db->f("omschrijving_voucher");

		if(ereg($delimiter,$naam)) $naam="'".$naam."'";
		if(ereg($delimiter,$resort)) $resort="'".$resort."'";
		if(ereg($delimiter,$pack)) $pack="'".$pack."'";

		if($db->f("korting")<>0) {
			$netto=$db->f("inkoop")*(1-$db->f("korting")/100);
		} else {
			$netto=$db->f("inkoop");
		}

		$duur=intval(8-$db->f("begindag")+$db->f("einddag"));
		echo $db->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db->f("aankomstdatum_exact")).$delimiter.$resort.$delimiter.$pack.$delimiter.$aantalpersonen.$delimiter.$duur.$delimiter.number_format($db->f("inkoop"),2,",","").$delimiter.number_format($db->f("korting"),2,",","").$delimiter.number_format($netto,2,",","")."\n";
	}
	
	# Losse skipassen (bij accommodaties zonder skipas)
#	$db2->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.aantalpersonen, b.seizoen_id, b.aankomstdatum_exact, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode, oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, oo.einddag, COUNT(*) AS aantalpersonen, ot.verkoop, ot.inkoop, ot.korting FROM optie_tarief ot, boeking_optie bo, boeking b, boeking_persoon bp, optie_groep og, optie_onderdeel oo, optie_soort os, type t, accommodatie a, plaats p, land WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND ot.week=b.aankomstdatum AND oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id=b.boeking_id AND b.aankomstdatum='".addslashes($_GET["date"])."' AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND og.optie_soort_id=os.optie_soort_id AND os.losse_skipas=1 GROUP BY bo.optie_onderdeel_id;");
	$db2->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.aantalpersonen, b.seizoen_id, b.aankomstdatum_exact, bp.voornaam, bp.tussenvoegsel, bp.achternaam, p.naam AS plaats, p.plaats_id, land.begincode, oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, oo.einddag, COUNT(*) AS aantalpersonen, ot.verkoop, ot.inkoop, ot.korting FROM optie_tarief ot, boeking_optie bo, boeking b, boeking_persoon bp, optie_groep og, optie_onderdeel oo, optie_soort os, type t, accommodatie a, plaats p, land WHERE ot.optie_onderdeel_id=oo.optie_onderdeel_id AND ot.week=b.aankomstdatum AND oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id=b.boeking_id AND b.aankomstdatum='".addslashes($_GET["date"])."' AND p.land_id=land.land_id AND bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND og.optie_soort_id=os.optie_soort_id AND os.losse_skipas=1 GROUP BY b.boeking_id;");
	while($db2->next_record()) {

		#
		# Kijken welke deelnemers een afwijkende skipas hebben
		#
		unset($afwijkende_skipas,$not_in);
		# Gewone opties
		$db3->query("SELECT bo.persoonnummer FROM boeking_optie bo, optie_groep og, optie_onderdeel oo, optie_soort os WHERE og.optie_soort_id=os.optie_soort_id AND og.skipas_id>0 AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND os.losse_skipas=0 AND bo.boeking_id='".addslashes($db2->f("boeking_id"))."';");
		while($db3->next_record()) {
			$afwijkende_skipas[$db3->f("persoonnummer")]=true;
			if($not_in) $not_in.=",".$db3->f("persoonnummer"); else $not_in=$db3->f("persoonnummer");
		}
		
		# Handmatige opties
		$db3->query("SELECT deelnemers FROM extra_optie WHERE boeking_id='".addslashes($db2->f("boeking_id"))."' AND skipas_id>0;");
		while($db3->next_record()) {
			$tempdeelnemers=@split(",",$db3->f("deelnemers"));
			while(list($key,$value)=@each($tempdeelnemers)) {
				$afwijkende_skipas[$value]=true;
				if($not_in) $not_in.=",".$value; else $not_in=$value;
			}
		}

		# Aantal personen bepalen
		$db3->query("SELECT persoonnummer FROM boeking_optie WHERE boeking_id='".$db2->f("boeking_id")."' AND optie_onderdeel_id='".$db2->f("optie_onderdeel_id")."'".($not_in ? " AND persoonnummer NOT IN (".$not_in.")" : "").";");
		$aantalpersonen=$db3->num_rows();

		if($aantalpersonen) {
			$naam=wt_naam($db2->f("voornaam"),$db2->f("tussenvoegsel"),$db2->f("achternaam"));
			$resort=$db2->f("plaats");
			$pack=$db2->f("omschrijving_voucher");
	
			if(ereg($delimiter,$naam)) $naam="'".$naam."'";
			if(ereg($delimiter,$resort)) $resort="'".$resort."'";
			if(ereg($delimiter,$pack)) $pack="'".$pack."'";
	
			if($db2->f("korting")<>0) {
				$netto=$db2->f("inkoop")*(1-$db2->f("korting")/100);
			} else {
				$netto=$db2->f("inkoop");
			}
	
			# Duur bepalen
			$duur=intval($duurreis-$db2->f("begindag")+$db2->f("einddag"));
	
			echo $db2->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db2->f("aankomstdatum_exact")).$delimiter.$resort.$delimiter.$pack.$delimiter.$aantalpersonen.$delimiter.$duur.$delimiter.number_format($db2->f("inkoop"),2,",","").$delimiter.number_format($db2->f("korting"),2,",","").$delimiter.number_format($netto,2,",","")."\n";
		}
	
		# Afwijkende skipassen
		$db3->query("SELECT oo.optie_onderdeel_id, oo.naam, oo.omschrijving_voucher, oo.begindag, oo.einddag, COUNT(*) AS aantalpersonen FROM boeking_optie bo, optie_groep og, optie_onderdeel oo, optie_soort os WHERE og.optie_soort_id=os.optie_soort_id AND oo.voucher=1 AND og.skipas_id='".addslashes($_GET["spid"])."' AND bo.status=1 AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND bo.boeking_id='".$db2->f("boeking_id")."' AND os.losse_skipas=0 GROUP BY bo.optie_onderdeel_id;");
		while($db3->next_record()) {

			$naam=wt_naam($db2->f("voornaam"),$db2->f("tussenvoegsel"),$db2->f("achternaam"));
			$resort=$db2->f("plaats");
			$pack=$db3->f("omschrijving_voucher");
	
			if(ereg($delimiter,$naam)) $naam="'".$naam."'";
			if(ereg($delimiter,$resort)) $resort="'".$resort."'";
			if(ereg($delimiter,$pack)) $pack="'".$pack."'";

			$db0->query("SELECT inkoop, korting FROM optie_tarief WHERE optie_onderdeel_id='".$db3->f("optie_onderdeel_id")."' AND seizoen_id='".$db2->f("seizoen_id")."' AND week='".addslashes($_GET["date"])."';");
			if($db0->next_record()) {
				$netto=$db0->f("inkoop")*(1-$db0->f("korting")/100);
			} else {
				$netto=$db2->f("inkoop");
			}
	
			# Duur bepalen
			$duur=intval($duurreis-$db3->f("begindag")+$db2->f("einddag"));
	
			echo $db2->f("boekingsnummer").$delimiter.$naam.$delimiter.date("d-m-Y",$db2->f("aankomstdatum_exact")).$delimiter.$resort.$delimiter.$pack.$delimiter.$db3->f("aantalpersonen").$delimiter.$duur.$delimiter.number_format($db0->f("inkoop"),2,",","").$delimiter.number_format($db0->f("korting"),2,",","").$delimiter.number_format($netto,2,",","")."\n";
		}
		
	}
	
} elseif($_GET["t"]==10) {
	#
	# Overzicht annuleringsverzekering
	#

	cmslog_pagina_title("Overzichten - annuleringsverzekering");
	
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		echo "<pre>";
	} else {
		$filename="annverzekeringen_".date("d_m_Y").".csv";
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
	}
	$delimiter=";";
	$decimalen=5;
	
	echo "Site".$delimiter."Resnr1".$delimiter."Resnr2".$delimiter."Klantnaam".$delimiter."Aankomstdatum".$delimiter."Bedrag".$delimiter."Percentage".$delimiter."Bruto".$delimiter."Excl".$delimiter."Ann. IK".$delimiter."PK Ann".$delimiter."Ass. Ann".$delimiter."Ass. PK".$delimiter."Ass. Tot".$delimiter."Provisie".$delimiter."Totaal\n";

	$db->query("SELECT DISTINCT b.boeking_id, b.boekingsnummer, b.seizoen_id, b.aankomstdatum_exact, b.verzekeringen_poliskosten, bp.annverz FROM boeking b, boeking_persoon bp, seizoen s WHERE b.goedgekeurd=1 AND b.geannuleerd=0 AND b.seizoen_id=s.seizoen_id AND s.annuleringsverzekering_percentage_1_basis>0 AND bp.boeking_id=b.boeking_id AND bp.annverz>0 ORDER BY b.aankomstdatum_exact, bp.boeking_id, bp.annverz;");
	while($db->next_record()) {
		unset($naam,$verzekerdbedrag,$percentage,$percentage_korting,$poliskosten,$poliskosten_basis,$bruto,$excl,$annik,$assurantiebelasting,$boekingsnummer1,$boekingsnummer2,$boekingsnummer3);
		if($boeking_onthouden[$db->f("boeking_id")]) {
			$poliskosten_opnemen=false;
		} else {
			$poliskosten_opnemen=true;
			$boeking_onthouden[$db->f("boeking_id")]=true;
		}
		$db2->query("SELECT bp.annverz, bp.annverz_verzekerdbedrag, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.persoonnummer FROM boeking_persoon bp WHERE bp.boeking_id='".$db->f("boeking_id")."';");
		while($db2->next_record()) {
			if($db2->f("persoonnummer")==1) {
				$naam=wt_naam($db2->f("voornaam"),$db2->f("tussenvoegsel"),$db2->f("achternaam"));
			}
			$verzekerdbedrag+=$db2->f("annverz_verzekerdbedrag");
		}
		$db2->query("SELECT annuleringsverzekering_percentage_".$db->f("annverz")."_berekend AS annuleringsverzekering_percentage_berekend, annuleringsverzekering_percentage_".$db->f("annverz")."_korting AS annuleringsverzekering_percentage_korting, annuleringsverzekering_poliskosten, annuleringsverzekering_poliskosten_basis, annuleringsverzekering_poliskosten_korting, verzekeringen_poliskosten, verzekeringen_poliskosten_basis, verzekeringen_poliskosten_korting, assurantiebelasting FROM seizoen WHERE seizoen_id='".$db->f("seizoen_id")."';");
		if($db2->next_record()) {
			$percentage=$db2->f("annuleringsverzekering_percentage_berekend");
			$percentage_korting=$db2->f("annuleringsverzekering_percentage_korting");
			if($db->f("boeking_id")>=36649) {
				$poliskosten=$db2->f("verzekeringen_poliskosten");
				$poliskosten_basis=$db2->f("verzekeringen_poliskosten_basis");
				$poliskosten_korting=$db2->f("verzekeringen_poliskosten_korting");
			} else {
				$poliskosten=$db2->f("annuleringsverzekering_poliskosten");
				$poliskosten_basis=$db2->f("annuleringsverzekering_poliskosten_basis");
				$poliskosten_korting=$db2->f("annuleringsverzekering_poliskosten_korting");
			}
			$assurantiebelasting=$db2->f("assurantiebelasting");
		}
		if(strlen($db->f("boekingsnummer"))==16) {
			$boekingsnummer1=substr($db->f("boekingsnummer"),0,1);
			$boekingsnummer2=substr($db->f("boekingsnummer"),1,6);
			$boekingsnummer3=substr($db->f("boekingsnummer"),10,6);
		} else {
			$boekingsnummer1=substr($db->f("boekingsnummer"),0,1);
			$boekingsnummer2=substr($db->f("boekingsnummer"),1,8);
		}
		$verzekerdbedrag=round($verzekerdbedrag,$decimalen);
		$percentage=round($percentage,$decimalen);
		$bruto=round($verzekerdbedrag*($percentage/100),$decimalen);
		$excl=round($bruto/(1+($assurantiebelasting/100)),$decimalen);
		$annik=round($excl*(1-($percentage_korting/100)),$decimalen);
		if($poliskosten_opnemen) {
			$pkann=round($poliskosten_basis,$decimalen);
		} else {
			$pkann=0;
		}
		$assann=round($bruto-$excl,$decimalen);
		
		if($poliskosten_opnemen) {
			$asspk=round($poliskosten-$poliskosten_basis,$decimalen);
		} else {
			$asspk=0;
		}
		$asstot=round($assann+$asspk,$decimalen);
		$provisie=round(($excl*($percentage_korting/100))+($pkann*($poliskosten_korting/100)),$decimalen);
		$totaal=round($annik+$asstot,$decimalen);
		
		# Waardes in juiste formaat
		$verzekerdbedrag=number_format($verzekerdbedrag,$decimalen,",","");
		$percentage=number_format($percentage,$decimalen,",","");
		$bruto=number_format($bruto,$decimalen,",","");
		$excl=number_format($excl,$decimalen,",","");
		$annik=number_format($annik,$decimalen,",","");
		$pkann=number_format($pkann,$decimalen,",","");
		$assann=number_format($assann,$decimalen,",","");
		$asspk=number_format($asspk,$decimalen,",","");
		$asstot=number_format($asstot,$decimalen,",","");
		$provisie=number_format($provisie,$decimalen,",","");
		$totaal=number_format($totaal,$decimalen,",","");

		echo $boekingsnummer1.$delimiter.$boekingsnummer2.$delimiter.$boekingsnummer3.$delimiter.wt_csvconvert($naam).$delimiter.date("Y-m-d",$db->f("aankomstdatum_exact")).$delimiter.$verzekerdbedrag.$delimiter.$percentage.$delimiter.$bruto.$delimiter.$excl.$delimiter.$annik.$delimiter.$pkann.$delimiter.$assann.$delimiter.$asspk.$delimiter.$asstot.$delimiter.$provisie.$delimiter.$totaal."\n";
	}
} elseif($_GET["t"]==11) {
	#
	# Overzicht reisverzekering
	#

	cmslog_pagina_title("Overzichten - reisverzekering");

	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		echo "<pre>";
	} else {
		$filename="reisverzekeringen_".date("d_m_Y").".csv";
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
	}
	$delimiter=";";
	$decimalen=2;
	
	echo "Site".$delimiter."Resnr1".$delimiter."Resnr2".$delimiter."Klantnaam".$delimiter."Aankomstdatum".$delimiter."Verkoopprijs".$delimiter."Bruto inkoop".$delimiter."Korting".$delimiter."Ink. premie".$delimiter."PK Reis".$delimiter."Ass. PK".$delimiter."Ass. Tot".$delimiter."Provisie".$delimiter."Totaal\n";
	$db->query("SELECT DISTINCT b.boeking_id, b.boekingsnummer, oo.optie_onderdeel_id, b.aankomstdatum_exact, b.seizoen_id, b.aankomstdatum FROM boeking b, boeking_optie bo, optie_onderdeel oo, optie_groep og, optie_soort os, seizoen s WHERE b.seizoen_id=s.seizoen_id AND s.reisverzekering_poliskosten_basis>0 AND b.goedgekeurd=1 AND b.geannuleerd=0 AND bo.boeking_id=b.boeking_id AND bo.optie_onderdeel_id=oo.optie_onderdeel_id AND oo.optie_groep_id=og.optie_groep_id AND og.optie_soort_id=os.optie_soort_id AND os.reisverzekering=1 ORDER BY b.aankomstdatum_exact, oo.optie_onderdeel_id;");
	while($db->next_record()) {
		unset($naam,$verkoopprijs,$brutoinkoop,$korting,$poliskosten,$poliskosten_basis,$inkpremie,$pkreis,$asspk,$totaal,$boekingsnummer1,$boekingsnummer2,$boekingsnummer3);

		if($boeking_onthouden[$db->f("boeking_id")]) {
			$poliskosten_opnemen=false;
		} else {
			$poliskosten_opnemen=true;
			$boeking_onthouden[$db->f("boeking_id")]=true;
		}

		if(strlen($db->f("boekingsnummer"))==16) {
			$boekingsnummer1=substr($db->f("boekingsnummer"),0,1);
			$boekingsnummer2=substr($db->f("boekingsnummer"),1,6);
			$boekingsnummer3=substr($db->f("boekingsnummer"),10,6);
		} else {
			$boekingsnummer1=substr($db->f("boekingsnummer"),0,1);
			$boekingsnummer2=substr($db->f("boekingsnummer"),1,8);
		}

		$db2->query("SELECT bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM boeking_persoon bp WHERE bp.boeking_id='".$db->f("boeking_id")."' AND persoonnummer=1;");
		if($db2->next_record()) {
			$naam=wt_naam($db2->f("voornaam"),$db2->f("tussenvoegsel"),$db2->f("achternaam"));
		}

		$db2->query("SELECT bo.verkoop, ot.inkoop, ot.korting FROM boeking_optie bo, optie_tarief ot WHERE ot.optie_onderdeel_id=bo.optie_onderdeel_id AND ot.seizoen_id='".$db->f("seizoen_id")."' AND ot.week='".$db->f("aankomstdatum")."' AND bo.optie_onderdeel_id='".$db->f("optie_onderdeel_id")."' AND bo.boeking_id='".$db->f("boeking_id")."' AND bo.status=1;");
		while($db2->next_record()) {
			$verkoopprijs+=$db2->f("verkoop");
			$brutoinkoop+=$db2->f("inkoop");
			$korting=$db2->f("korting");
		}

		$db2->query("SELECT s.reisverzekering_poliskosten, s.reisverzekering_poliskosten_basis, s.verzekeringen_poliskosten, verzekeringen_poliskosten_basis, s.assurantiebelasting FROM boeking b, seizoen s WHERE b.seizoen_id=s.seizoen_id AND b.boeking_id='".$db->f("boeking_id")."';");
		if($db2->next_record()) {
			if($db->f("boeking_id")>=36649) {
				$poliskosten=$db2->f("verzekeringen_poliskosten");
				$poliskosten_basis=$db2->f("verzekeringen_poliskosten_basis");
			} else {
				$poliskosten=$db2->f("reisverzekering_poliskosten");
				$poliskosten_basis=$db2->f("reisverzekering_poliskosten_basis");
			}
		}

		$inkpremie=round($brutoinkoop*(1-($korting/100)),$decimalen);
		if($poliskosten_opnemen) {
			$pkreis=round($poliskosten_basis,$decimalen);
		} else {
			$pkreis=0;
		}
		$asspk=round($poliskosten-$poliskosten_basis,$decimalen);
		$asstot=round($asspk,$decimalen);
		$provisie=round(($brutoinkoop*($korting/100))+$pkreis,$decimalen);
		$totaal=round($inkpremie+$asstot,$decimalen);
		
		$verkoopprijs=number_format($verkoopprijs,$decimalen,",","");
		$brutoinkoop=number_format($brutoinkoop,$decimalen,",","");
		$korting=number_format($korting,$decimalen,",","");
		$inkpremie=number_format($inkpremie,$decimalen,",","");
		$pkreis=number_format($pkreis,$decimalen,",","");
		$asspk=number_format($asspk,$decimalen,",","");
		$asstot=number_format($asstot,$decimalen,",","");
		$provisie=number_format($provisie,$decimalen,",","");
		$totaal=number_format($totaal,$decimalen,",","");

		echo $boekingsnummer1.$delimiter.$boekingsnummer2.$delimiter.$boekingsnummer3.$delimiter.wt_csvconvert($naam).$delimiter.date("Y-m-d",$db->f("aankomstdatum_exact")).$delimiter.$verkoopprijs.$delimiter.$brutoinkoop.$delimiter.$korting.$delimiter.$inkpremie.$delimiter.$pkreis.$delimiter.$asspk.$delimiter.$asstot.$delimiter.$provisie.$delimiter.$totaal."\n";
	}
} elseif($_GET["t"]==12) {
	#
	# Overzicht schadeverzekering
	#

	cmslog_pagina_title("Overzichten - schadeverzekering");
	
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		echo "<pre>";
	} else {
		$filename="schadeverzekeringen_".date("d_m_Y").".csv";
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
	}
	$delimiter=";";
	$decimalen=5;
	
	echo "Site".$delimiter."Resnr1".$delimiter."Resnr2".$delimiter."Klantnaam".$delimiter."Aankomstdatum".$delimiter."Bedrag".$delimiter."Percentage".$delimiter."Bruto".$delimiter."Excl".$delimiter."Inkoop".$delimiter."PK".$delimiter."Ass.Verz".$delimiter."Ass.PK".$delimiter."Ass.Tot".$delimiter."Provisie".$delimiter."Totaal\n";

	$db->query("SELECT DISTINCT b.boeking_id, b.boekingsnummer, b.seizoen_id, b.aankomstdatum_exact, b.verzekeringen_poliskosten, b.accprijs, bp.voornaam, bp.tussenvoegsel, bp.achternaam, bp.persoonnummer, s.verzekeringen_poliskosten, s.verzekeringen_poliskosten_basis, s.verzekeringen_poliskosten_korting, s.schadeverzekering_percentage_korting, s.assurantiebelasting, s.schadeverzekering_percentage_berekend FROM boeking b, boeking_persoon bp, seizoen s WHERE bp.boeking_id=b.boeking_id AND bp.persoonnummer=1 AND b.schadeverzekering=1 AND b.goedgekeurd=1 AND b.geannuleerd=0 AND b.seizoen_id=s.seizoen_id ORDER BY b.aankomstdatum_exact, bp.achternaam;");
	while($db->next_record()) {
		unset($naam,$verzekerdbedrag,$percentage,$percentage_korting,$poliskosten,$poliskosten_basis,$poliskosten_korting,$bruto,$excl,$annik,$assurantiebelasting,$boekingsnummer1,$boekingsnummer2,$boekingsnummer3);
		$naam=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
		$verzekerdbedrag=$db->f("accprijs");
		$percentage=$db->f("schadeverzekering_percentage_berekend");
		$percentage_korting=$db->f("schadeverzekering_percentage_korting");
		$assurantiebelasting=$db->f("assurantiebelasting");
		
		if(strlen($db->f("boekingsnummer"))==16) {
			$boekingsnummer1=substr($db->f("boekingsnummer"),0,1);
			$boekingsnummer2=substr($db->f("boekingsnummer"),1,6);
			$boekingsnummer3=substr($db->f("boekingsnummer"),10,6);
		} else {
			$boekingsnummer1=substr($db->f("boekingsnummer"),0,1);
			$boekingsnummer2=substr($db->f("boekingsnummer"),1,8);
		}
		$verzekerdbedrag=round($verzekerdbedrag,$decimalen);
		
		$poliskosten=$db->f("verzekeringen_poliskosten");
		$poliskosten_basis=$db->f("verzekeringen_poliskosten_basis");
		$poliskosten_korting=$db->f("verzekeringen_poliskosten_korting");
		
		$percentage=round($percentage,$decimalen);
		$bruto=round($verzekerdbedrag*($percentage/100),$decimalen);
		$excl=round($bruto/(1+($assurantiebelasting/100)),$decimalen);
		$annik=round($excl*(1-($percentage_korting/100)),$decimalen);
		$pkann=round($poliskosten_basis,$decimalen);
		$assann=round($bruto-$excl,$decimalen);

		$asspk=round($poliskosten-$poliskosten_basis,$decimalen);
		$asstot=round($assann+$asspk,$decimalen);
		$provisie=round(($excl*($percentage_korting/100))+($pkann*($poliskosten_korting/100)),$decimalen);
		$totaal=round($annik+$asstot,$decimalen);
		
		# Waardes in juiste formaat
		$verzekerdbedrag=number_format($verzekerdbedrag,$decimalen,",","");
		$percentage=number_format($percentage,$decimalen,",","");
		$bruto=number_format($bruto,$decimalen,",","");
		$excl=number_format($excl,$decimalen,",","");
		$annik=number_format($annik,$decimalen,",","");
		$pkann=number_format($pkann,$decimalen,",","");
		$assann=number_format($assann,$decimalen,",","");
		$asspk=number_format($asspk,$decimalen,",","");
		$asstot=number_format($asstot,$decimalen,",","");
		$provisie=number_format($provisie,$decimalen,",","");
		$totaal=number_format($totaal,$decimalen,",","");

		echo $boekingsnummer1.$delimiter.$boekingsnummer2.$delimiter.$boekingsnummer3.$delimiter.wt_csvconvert($naam).$delimiter.date("Y-m-d",$db->f("aankomstdatum_exact")).$delimiter.$verzekerdbedrag.$delimiter.$percentage.$delimiter.$bruto.$delimiter.$excl.$delimiter.$annik.$delimiter.$pkann.$delimiter.$assann.$delimiter.$asspk.$delimiter.$asstot.$delimiter.$provisie.$delimiter.$totaal."\n";
	}
}

if($_GET["t"]==5 or $_GET["t"]==8 or $_GET["t"]==9 or $_GET["t"]==10 or $_GET["t"]==11 or $_GET["t"]==12) {
	# CSV-bestand
} else {
	if($_GET["t"]==7) {
		$ms->css="	td {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10pt;
		}
		p {
			padding: 0px;
			margin: 0px;
			margin-left: 35px;
		}
		";
	} else {
		# Word- bestand
		$ms->css="	body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 0.8em;
			line-height: 110%;
		}
		
		table {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 1em;
			margin: 0px;
			padding: 1px;
		}
		
		.betreft {
			width: 300px;
		}
		
		.rekentabel {
			width: 100%;
		}
		
		.kunsttabel {
			width: 500px;
		}
		
		.kunsttabel .links {
			width: 10%;
		}	
		
		.kunsttabel .rechts {
			width: 90%;
			padding-left:7px;
		}	
		
		.tekst {
			padding: 1px;	
		}
		
		td {
			padding: 1px;	
		}
		
		.koptekst {
			font-size: 1.3em;
			font-weight: bold;
		}
		
		hr {
			height: 2px;
			color: #000000;
			background-color: #000000;
		}
		";
	}
	
	if($_GET["test"]) {
		exit;
	}

	#$ms->margin="2cm 2cm 5cm 2cm;";
	$ms->create_word_document();
}

# http://www.chalet.nl/cms_overzichten_print.php?t=2&lid=159&date=1324681200

?>