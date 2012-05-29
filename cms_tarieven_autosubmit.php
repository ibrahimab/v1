<?php

if($_GET["check"] and $_GET["confirmed"]) {
	$mustlogin=true;
	$vars["cmslog_pagina_niet_opslaan"]=true;
	include("admin/vars.php");
	touch("tmp/autobijwerken.txt");
	unset($query);

	echo "<html>";
	echo "<head><title>Autosave tarieven</title>";
	echo "<style>html {\nfont-family: Verdana, Arial, Helvetica, sans-serif;\nfont-size: 0.8em;\n}</style>";
	echo "</head>";
	if($_GET["t"]==1) {
		#
		# Onjuiste wederverkoop-tarieven
		#
		$db->query("SELECT DISTINCT ta.type_id, ta.seizoen_id FROM tarief ta WHERE ta.wederverkoop_verkoopprijs>0 AND (ta.opgeslagen IS NULL OR UNIX_TIMESTAMP(ta.opgeslagen)<1217848726);");
		while($db->next_record()) {
			$tedoorlopen_type[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("type_id");
			$tedoorlopen_seizoen[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("seizoen_id");
		}
	} elseif($_GET["t"]==2) {
		#
		# XML-tarievenimport
		#
		$db->query("SELECT DISTINCT x.type_id, x.seizoen_id FROM xml_tarievenimport x, tarief t WHERE x.type_id>0 AND x.seizoen_id>0 AND x.type_id=t.type_id AND x.week=t.week AND x.seizoen_id=t.seizoen_id AND t.autoimportxmltarief=1;");
		while($db->next_record()) {
			$tedoorlopen_type[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("type_id");
			$tedoorlopen_seizoen[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("seizoen_id");
		}
		$db->query("SELECT DISTINCT x.type_id, x.seizoen_id FROM xml_tarievenimport x WHERE x.type_id>0 AND x.seizoen_id>0;");
		while($db->next_record()) {
			$db2->query("SELECT type_id FROM tarief WHERE type_id='".$db->f("type_id")."' AND seizoen_id='".$db->f("seizoen_id")."';");
			if(!$db2->num_rows()) {
				$tedoorlopen_type[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("type_id");
				$tedoorlopen_seizoen[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("seizoen_id");
			}
		}
	} elseif($_GET["t"]==10) {
		#
		# XML-tarievenimport flexibele tarieven
		#
		$db->query("SELECT DISTINCT x.type_id, x.seizoen_id FROM xml_tarievenimport_flex x WHERE x.type_id>0 AND x.seizoen_id>0;");
		while($db->next_record()) {
			$tedoorlopen_type[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("type_id");
			$tedoorlopen_seizoen[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("seizoen_id");
		}
	} elseif($_GET["t"]==99) {
		# Herstel-systeem bij niet juiste database
		
		# Zomerhuisje tarieven (waarbij wederverkoop nog niet geregeld is)		
#		$db->query("SELECT DISTINCT t.type_id, ta.seizoen_id FROM type t, accommodatie a, tarief ta WHERE t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND a.wzt=2 AND ta.c_verkoop_site>0 AND ta.wederverkoop_verkoopprijs=0 AND ta.seizoen_id=15 ORDER BY ta.type_id;");
#		while($db->next_record()) {
#			$tedoorlopen_type[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("type_id");
#			$tedoorlopen_seizoen[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("seizoen_id");
#		}


		# Verschil tussen normaal tarief en wederverkoop-tarief
#		$db->query("SELECT DISTINCT ta.type_id, ta.seizoen_id FROM tarief ta, type t, accommodatie a WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND ta.c_bruto>0 AND ta.c_verkoop_site>0 AND ta.wederverkoop_verkoopprijs>0 AND (ta.wederverkoop_verkoopprijs<ta.c_verkoop_site);");
		$db->query("SELECT DISTINCT ta.type_id, ta.seizoen_id FROM tarief ta, type t, accommodatie a WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND ta.c_bruto>0 AND ta.c_verkoop_site>0 AND ta.wederverkoop_verkoopprijs>0 AND (ta.wederverkoop_verkoopprijs<(ta.c_verkoop_site-10));");
#		echo $db->lastquery;
		while($db->next_record()) {
			$tedoorlopen_type[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("type_id");
			$tedoorlopen_seizoen[$db->f("type_id")."_".$db->f("seizoen_id")]=$db->f("seizoen_id");
		}
	}
	if($tedoorlopen_type) {
		echo "<p><b>DIT SYSTEEM OPENT AUTOMATISCH ONDERSTAANDE TYPES (steeds na 2 sec.)<br>Sluit de browser om deze functie uit te schakelen.</b><p>Bij de volgende ".count($tedoorlopen_type)." accommodaties worden de tarieven automatisch bijgewerkt:<ul>";
		while(list($key,$value)=each($tedoorlopen_type)) {
			if(!$teller) {
				$_SERVER["REQUEST_URI"]=str_replace("first=1","first=0",$_SERVER["REQUEST_URI"]);
				if($_GET["t"]==10) {
					$url="cms_tarieven_flex.php?autosave=1&from=".urlencode($_SERVER["REQUEST_URI"])."&sid=".$tedoorlopen_seizoen[$key]."&tid=".$value;
				} else {
					$url="cms_tarieven.php?autosave=1&from=".urlencode($_SERVER["REQUEST_URI"])."&sid=".$tedoorlopen_seizoen[$key]."&tid=".$value;
				}
				echo "<body onload=\"";
				if($_GET["first"]==1) echo "window.opener.location.reload();";
				echo "setTimeout('document.location=\'".$url."\'',".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? "100" : "2000").");";
				echo "\">";
			}
			$teller++;
			echo "<li>".$value." (seizoen ".ereg_replace("^.*_","",$key).")</li>";
		}
		echo "</ul>";
	} else {
		echo "<body>Alle tarieven zijn opgeslagen.";
		echo "<p><a href=\"#\" onclick=\"window.close();\">Venster sluiten</a><p>";
	}
	echo "</body></html>";
}

?>