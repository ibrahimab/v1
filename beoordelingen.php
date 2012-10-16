<?php

include("admin/vars.php");

$db->query("SELECT AVG(be.vraag1_7) AS totaaloordeel FROM boeking_enquete be, view_accommodatie v WHERE be.type_id='".intval($_GET["typeid"])."' AND be.beoordeeld=1 AND v.type_id=be.type_id AND v.atonen=1 AND v.ttonen=1 AND v.archief=0 GROUP BY be.type_id;");

if($db->next_record()) {
	$totaaloordeel=$db->f("totaaloordeel");

	$accinfo=accinfo($_GET["typeid"]);


	$db->query("SELECT b.boeking_id, b.aankomstdatum_exact, be.websitetekst_gewijzigd, be.websitetekst_naam, be.vraag1_1, be.vraag1_2, be.vraag1_3, be.vraag1_4, be.vraag1_5, be.vraag1_6, be.vraag1_7 FROM boeking_enquete be, boeking b, type t WHERE b.type_id=t.type_id AND be.boeking_id=b.boeking_id AND t.type_id='".intval($_GET["typeid"])."' AND be.beoordeeld=1 ORDER BY b.aankomstdatum_exact DESC, b.boeking_id;");
	while($db->next_record()) {
		$aantalbeoordelingen=$db->num_rows();
		$enquete[$db->f("boeking_id")]["aankomstdatum_exact"]=$db->f("aankomstdatum_exact");
		$enquete[$db->f("boeking_id")]["websitetekst_gewijzigd"]=$db->f("websitetekst_gewijzigd");
		$enquete[$db->f("boeking_id")]["websitetekst_naam"]=$db->f("websitetekst_naam");
		for($i=1;$i<=7;$i++) {
			if($db->f("vraag1_".$i) and $db->f("vraag1_".$i)<=10) {
				$enquete[$db->f("boeking_id")][$i]=$db->f("vraag1_".$i);
			}
		}
	}

	echo "<style type=\"text/css\">\n"; ?>

	#beoordeling_wrapper {
		margin: 10px;
		width: 774px;
		margin-left: auto;
		margin-right: auto;
	}

	#beoordeling_header {
		margin-bottom: 20px;
	}

	beoordeling_header_left {
		float: left;
	}

	beoordeling_header_right {
		float: right;
	}

	#beoordeling_header img {
		float: left;
		margin-right: 10px;
		display: block;
	}

	#gemiddeldtotaaloordeel {
		font-size: 1.1em;
		margin-top: 10px;
		font-weight: bold;
	}

	#aantalbeoordelingen {
		font-size: 0.7em;
		font-weight: normal;
	}

	.beoordeling_enquete {
		margin-bottom: 20px;
		background-color: #d5e1f9;
		border: 1px solid #003366;
		padding: 10px;
	}

	.beoordeling_enquete_bovenbalk {
		background-color: #d5e1f9;
		margin-bottom: 5px;
	}

	.beoordeling_enquete_cijfers {
		float: left;
		width: 370px;
	}

	.beoordeling_enquete_toelichting {
		float: right;
		width: 370px;
		border: 1px solid #003366;
		background-color: #ffffff;
		min-height: 116px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		-o-box-sizing: border-box;
		box-sizing: border-box;
	}

	.beoordeling_enquete_toelichting_wrapper {
		padding: 7px;
	}

	.beoordeling_enquete_table {
		border: 1px solid #003366;
		width: 100%;
	}

	.beoordeling_enquete_table .regel1 {
		background-color: #ffffff;
	}

	.beoordeling_enquete_table .regel2 {
		background-color: #ebebeb;
	}

	.beoordeling_enquete_table td {
		padding-left: 3px;
		padding-right: 3px;
		line-height: 1.5em;
	}

	.clear {
		clear: both;
	}

	<?php

	echo "</style>";

	echo "<div id=\"beoordeling_wrapper\">";
	// echo "<h1>".html("titel","beoordelingen")."</h1>";

	echo "<div id=\"beoordeling_header\">";
	echo "<div id=\"beoordeling_header_left\">";
	echo "<img src=\"".imageurl(substr($accinfo["hoofdfoto"],8),170,127)."\" width=\"170\" height=\"127\">";
	echo "</div>"; // afsluiten #beoordeling_header_left

	echo "<div id=\"beoordeling_header_right\">";
	echo "<h1>".wt_he(ucfirst($accinfo["soortaccommodatie"])." ".$accinfo["accommodatie"])."</h1><h3 style=\"padding-bottom:5px;\">".wt_he($accinfo["type"])."</h3>";
	echo "<div>".wt_he($accinfo["plaats"]." - ".$accinfo["skigebied"])."</div>";

	echo "<div id=\"gemiddeldtotaaloordeel\">";

	echo html("gemiddeldtotaaloordeel","beoordelingen").": ".number_format($totaaloordeel,1,",",".");

	echo "&nbsp;&nbsp;<span id=\"aantalbeoordelingen\">(".intval($aantalbeoordelingen)." ".($aantalbeoordelingen==1 ? html("beoordeling","beoordelingen") : html("beoordelingen","beoordelingen")).")</span>";

	echo "</div>"; // afsluiten #gemiddeldtotaaloordeel
	echo "</div>"; // afsluiten #beoordeling_header_right

	echo "<div class=\"clear\"></div>";

	echo "</div>"; // afsluiten #beoordeling_header

	echo "<div class=\"clear\"></div>";

	if(is_array($enquete)) {
		while(list($key,$value)=each($enquete)) {

			echo "<div class=\"beoordeling_enquete\">";

			echo "<div class=\"beoordeling_enquete_bovenbalk\">";
			echo "<b>".html("totaaloordeel","beoordelingen").": ".$value[7]."</b>";
			echo " - ";
			echo html("aankomst","beoordelingen").": ".DATUM("MAAND JJJJ",$value["aankomstdatum_exact"],$vars["taal"]);

			echo " - ".html("ingevulddoor","beoordelingen").": ";
			if($value["websitetekst_naam"]) {
				echo wt_he($value["websitetekst_naam"]);
			} else {
				echo "<i>".html("anoniem","beoordelingen")."</i>";
			}
			echo "</div>"; // afsluiten .beoordeling_enquete_bovenbalk

			echo "<div class=\"beoordeling_enquete_cijfers\">";

			echo "<table cellspacing=\"0\" cellpadding=\"0\" class=\"beoordeling_enquete_table\">";
			for($i=1;$i<=6;$i++) {
				if($value[$i]>0) {
					echo "<tr class=\"".($i%2 ? "regel1" : "regel2")."\"><td>".html("vraag1_".$i,"enquete")."</td><td>&nbsp;</td><td align=\"right\">".intval($value[$i])."</td></tr>";
				}
			}
			echo "</table>";

			echo "</div>"; // afsluiten .beoordeling_enquete_cijfers

			if($value["websitetekst_gewijzigd"] and $vars["taal"]=="nl") {
				echo "<div class=\"beoordeling_enquete_toelichting\">";
				echo "<div class=\"beoordeling_enquete_toelichting_wrapper\">";
				echo "<i>".html("toelichting","beoordelingen").":</i><br>";
				echo nl2br(wt_he($value["websitetekst_gewijzigd"]));
				echo "</div>"; // afsluiten .beoordeling_enquete_toelichting_wrapper
				echo "</div>"; // afsluiten .beoordeling_enquete_toelichting
			}
			echo "<div class=\"clear\"></div>";
			echo "</div>"; // afsluiten .beoordeling_enquete
		}
	}
	echo "</div>"; // afsluiten #beoordeling_wrapper
	echo "<br>";
} else {
	echo "<p>not found</p>";
	trigger_error("geen beoordelingen gevonden",E_USER_NOTICE);
}

?>