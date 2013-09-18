<?php

$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_directnaar"]=true;
$vars["verberg_linkerkolom"]=true;
include("admin/vars.php");

#echo substr(sha1("19636"."kkSLlejkd"),0,8);
#exit;

if($_GET["bid"] and $_GET["ch"]==substr(sha1($_GET["bid"]."kkSLlejkd"),0,8)) {
	$gegevens=get_boekinginfo($_GET["bid"]);
	if($gegevens["stap1"]["boekingid"] and $gegevens["stap1"]["boekingid"]==$_GET["bid"]) {

		if($_POST["enqfilled"]) {

#			echo wt_dump($gegevens);

			# Gegevens opslaan
			unset($setquery);

			$mailhtml.="De volgende gegevens zijn zojuist ingevuld:<p>";

			$mailhtml.="<table style=\"width:700px;\">";
			$mailhtml.="<tr><td width=\"200\" valign=\"top\">Boeking</td><td>&nbsp;</td><td><a href=\"http://www.chalet.nl/cms_boekingen.php?show=21&archief=0&21k0=".$gegevens["stap1"]["boekingid"]."\">".htmlentities($gegevens["stap1"]["boekingsnummer"])."</a></td></tr>";
			$mailhtml.="<tr><td width=\"200\" valign=\"top\">Plaats</td><td>&nbsp;</td><td>".htmlentities($gegevens["stap1"]["accinfo"]["plaats"].", ".$gegevens["stap1"]["accinfo"]["land"])."</td></tr>";
			$mailhtml.="<tr><td width=\"200\" valign=\"top\">Accommodatie</td><td>&nbsp;</td><td><a href=\"".htmlentities($gegevens["stap1"]["accinfo"]["url"])."\">".htmlentities($gegevens["stap1"]["accinfo"]["begincode"].$gegevens["stap1"]["accinfo"]["type_id"]." ".ucfirst($gegevens["stap1"]["accinfo"]["soortaccommodatie"])." ".$gegevens["stap1"]["accinfo"]["naam_ap"])."</a></td></tr>";
			$mailhtml.="<tr><td width=\"200\" valign=\"top\">Periode</td><td>&nbsp;</td><td>".htmlentities(DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"]))." - ".htmlentities(DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum_exact"]))."</td></tr>";
			$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";

			# Vraag 1
			$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("vraag1","enquete")."</i></b></td></tr>";
			for($i=1;$i<=6;$i++) {
				if($_POST["vraag1_".$i]) {
					$setquery.=", vraag1_".$i."='".addslashes($_POST["vraag1_".$i])."'";
					$mailhtml.="<tr><td width=\"200\">".html("vraag1_".$i,"enquete")."</td><td>&nbsp;</td><td>".($_POST["vraag1_".$i]==11 ? "n.v.t." : htmlentities($_POST["vraag1_".$i]))."</td></tr>";
				}
			}
			if($_POST["toelichting1"]) {
				$setquery.=", vraag1_toelichting='".addslashes($_POST["toelichting1"])."'";
				$mailhtml.="<tr><td width=\"200\" valign=\"top\">".html("toelichtingbij","enquete")."</td><td>&nbsp;</td><td>".nl2br(htmlentities($_POST["toelichting1"]))."</td></tr>";
			}

			# Vraag 2
			$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
			$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("totaaloordeel","enquete")."</i></b></td></tr>";
			$mailhtml.="<tr><td width=\"200\">".html("vraag1_7","enquete")."</td><td>&nbsp;</td><td>".htmlentities($_POST["vraag1_7"])."</td></tr>";
			if($_POST["vraag1_7"]) {
				$setquery.=", vraag1_7='".addslashes($_POST["vraag1_7"])."'";
			}
			$mailhtml.="<tr><td width=\"200\">Naam voor op website</td><td>&nbsp;</td><td>".($_POST["websitetekst_naam"] ? htmlentities($_POST["websitetekst_naam"]) : "<i>anoniem</i>")."</td></tr>";
			if($_POST["websitetekst_naam"]) {
				$setquery.=", websitetekst_naam='".addslashes($_POST["websitetekst_naam"])."'";
			}
			$mailhtml.="<tr><td colspan=\"3\">".nl2br(htmlentities($_POST["websitetekst"]))."</td></tr>";
			if($_POST["websitetekst"]) {
				$setquery.=", websitetekst='".addslashes($_POST["websitetekst"])."', websitetekst_gewijzigd='".addslashes($_POST["websitetekst"])."'";
			}

			# Vraag 3
			$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
			$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("vraag2","enquete")."</i></b></td></tr>";
			for($i=1;$i<=7;$i++) {
				if($_POST["vraag2_".$i]) {
					$setquery.=", vraag2_".$i."='".addslashes($_POST["vraag2_".$i])."'";
					$mailhtml.="<tr><td colspan=\"3\">".html("vraag2_".$i,"enquete");
					if($_POST["andersnamelijk2"] and $i==7) {
						$mailhtml.=": ".htmlentities($_POST["andersnamelijk2"]);
					}
					$mailhtml.="</td></tr>";
				}
			}
			if($_POST["andersnamelijk2"]) {
				$setquery.=", vraag2_anders='".addslashes($_POST["andersnamelijk2"])."'";
			}

			# Vraag 3
			$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
			$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("vraag3","enquete",array("v_websitenaam"=>$vars["websitenaam"]))."</i></b></td></tr>";
			for($i=1;$i<=9;$i++) {
				if($_POST["vraag3_".$i]) {
					$setquery.=", vraag3_".$i."='".addslashes($_POST["vraag3_".$i])."'";
					$mailhtml.="<tr><td width=\"200\">".html("vraag3_".$i,"enquete",array("v_websitenaam"=>$vars["websitenaam"]))."</td><td>&nbsp;</td><td>".($_POST["vraag3_".$i]==11 ? "n.v.t." : htmlentities($_POST["vraag3_".$i]))."</td></tr>";
				}
			}
			if($_POST["toelichting3"]) {
				$setquery.=", vraag3_toelichting='".addslashes($_POST["toelichting3"])."'";
				$mailhtml.="<tr><td width=\"200\" valign=\"top\">".html("toelichtingbij","enquete")."</td><td>&nbsp;</td><td>".nl2br(htmlentities($_POST["toelichting3"]))."</td></tr>";
			}

			# Vraag 4
			$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
			$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("vraag4","enquete")."</i></b></td></tr>";

			if($_POST["vraag4"]) {
				$setquery.=", vraag4='".addslashes($_POST["vraag4"])."'";
				$mailhtml.="<tr><td colspan=\"3\">".html("vraag4_".$_POST["vraag4"],"enquete")."</td></tr>";
#				if($_POST["vraag4"]==2 or $_POST["vraag4"]==3) {
				if($_POST["vraag4"]==3) {
					$db->query("UPDATE boeking SET mailblokkeren_klanten_vorig_seizoen=1 WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				}
			}

			# Vraag 5
#			$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
#			$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("vraag5","enquete")."</i></b></td></tr>";
#			if($_POST["vraag5"]) {
#				$setquery.=", vraag5='".addslashes($_POST["vraag5"])."'";
#				$mailhtml.="<tr><td colspan=\"3\">".html("vraag5_".$_POST["vraag5"],"enquete");
#				if($_POST["vraag5"]==1 and $_POST["toelichting5"]) {
#					$mailhtml.=": ".nl2br(htmlentities($_POST["toelichting5"]));
#				}
#				$mailhtml.="</td></tr>";
#			}
#			if($_POST["toelichting5"]) {
#				$setquery.=", vraag5_toelichting='".addslashes($_POST["toelichting5"])."'";
#			}

			# Vraag 6
			if($vars["taal"]=="nl") {
				$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
				$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("vraag6","enquete",array("v_websitenaam"=>$vars["websitenaam"]))."</i></b></td></tr>";
			}
			if($_POST["vraag6"]) {
				$setquery.=", vraag6='".addslashes($_POST["vraag6"])."'";
				$mailhtml.="<tr><td colspan=\"3\">".html("vraag6_".$_POST["vraag6"],"enquete")."</td></tr>";

				if($_POST["vraag6"]==1) {
					# Inschrijven nieuwsbrief (Chalet.nl of Zomerhuisje.nl)
					$nieuwsbrief_waardes=array("email"=>$gegevens["stap2"]["email"],"voornaam"=>$gegevens["stap2"]["voornaam"],"tussenvoegsel"=>$gegevens["stap2"]["tussenvoegsel"],"achternaam"=>$gegevens["stap2"]["achternaam"]);
					nieuwsbrief_inschrijven($vars["seizoentype"],$nieuwsbrief_waardes);
				}
			}

			# Vraag 7
#			if($vars["taal"]=="nl" and $vars["seizoentype"]<>2) {
#				$mailhtml.="<tr><td colspan=\"3\">&nbsp;</td></tr>";
#				$mailhtml.="<tr><td colspan=\"3\"><b><i>".html("vraag7","enquete",array("h_1"=>"","h_2"=>""))."</i></b></td></tr>";
#			}
#			if($_POST["vraag7"]) {
#				$setquery.=", vraag7='".addslashes($_POST["vraag7"])."'";
#				$mailhtml.="<tr><td colspan=\"3\">".html("vraag7_".$_POST["vraag7"],"enquete")."</td></tr>";
#
#				if($_POST["vraag7"]==1) {
#					# Inschrijven nieuwsbrief Zomerhuisje.nl
#					$mm_waardes=array("voornaam"=>$gegevens["stap2"]["voornaam"],"tussenvoegsel"=>$gegevens["stap2"]["tussenvoegsel"],"achternaam"=>$gegevens["stap2"]["achternaam"]);
#					mm_newmember($gegevens["stap2"]["email"],"uv8lyday",$mm_waardes);
#				}
#			}

			# Overige toelichting
			if($_POST["overigetoelichting"]) {
#				$setquery.=", overig='".addslashes($_POST["overigetoelichting"])."'";
#				$mailhtml.="<tr><td valign=\"top\">".html("overigetoelichting","enquete")."</td><td>&nbsp;</td><td>".nl2br(htmlentities($_POST["overigetoelichting"]))."</td></tr>";
			}

			# Mail met kortingscode sturen
			if($vars["taal"]=="nl" and $vars["fotofabriek_code_na_enquete"]) {

				$db->query("SELECT boeking_id FROM boeking_enquete WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
				if(!$db->next_record()) {

					# Kortingscode uit database halen
					$db2->query("SELECT enquete_kortingscode_id, code FROM enquete_kortingscode WHERE verzonden IS NULL ORDER BY enquete_kortingscode_id LIMIT 0,1");
					if($db2->next_record()) {
						$kortingscode=$db2->f("code");

						# Opslaan dat deze kortingscode verzonden is
						$db3->query("UPDATE enquete_kortingscode SET verzonden=NOW() WHERE enquete_kortingscode_id='".$db2->f("enquete_kortingscode_id")."';");
					}

					# Kijken of er nog genoeg kortingscodes beschikbaar zijn
					$db2->query("SELECT COUNT(enquete_kortingscode_id) AS aantal FROM enquete_kortingscode WHERE verzonden IS NULL;");
					if($db2->next_record()) {
						$enquete_kortingscode_aantal=$db2->f("aantal");
					}
					if($enquete_kortingscode_aantal<50) {
						# Zo niet: Bjorn mailen
						wt_mail("bjorn@chalet.nl","Nog maar ".$enquete_kortingscode_aantal." enquête-kortingscodes","Er zijn nog maar ".$enquete_kortingscode_aantal." enquête-kortingscodes beschikbaar.\n\nVoeg z.s.m. nieuwe codes toe via http://www.chalet.nl/cms_diversen.php?t=3\n\n");
					}

					$mailbody="Als dank voor het invullen van de enquête van ".$vars["websitenaam"]." ontvang je hierbij je kortingscode. Met deze kortingscode kun je op [link=http://www.fotofabriek.nl/chalet-actie/]fotofabriek.nl[/link] met 25% korting een foto-canvas bestellen!

Je kortingscode is: [b]".trim($kortingscode)."[/b]

Deze is geldig t/m 1 mei 2014 en kan eenmalig worden gebruikt. Bekijk de [link=http://www.fotofabriek.nl/chalet-actie/]speciale actiepagina[/link] voor verdere details en de voorwaarden.

Wij wensen je hiermee heel veel plezier en hopen je binnenkort opnieuw van dienst te mogen zijn.


Met vriendelijke groet,

[ondertekening]";

					if($kortingscode) {
						# Mail versturen (met opmaak)
						verstuur_opmaakmail($gegevens["stap1"]["website"],$gegevens["stap2"]["email"],wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]),"kortingscode fotofabriek.nl",$mailbody,array("convert_to_html"=>true));
					}
				}
			}


			$db->query("INSERT INTO boeking_enquete SET boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."', type_id='".addslashes($gegevens["stap1"]["typeid"])."', aankomstdatum_exact=FROM_UNIXTIME('".addslashes($gegevens["stap1"]["aankomstdatum_exact"])."'), vertrekdatum_exact=FROM_UNIXTIME('".addslashes($gegevens["stap1"]["vertrekdatum_exact"])."'), invulmoment=NOW()".$setquery.";");

			$mail=new wt_mail;
			$mail->fromname="Website ".$vars["websitenaam"];
			$mail->from="info@chalet.nl";
			$mail->to="info@chalet.nl";

#$mail->to="jeroen@webtastic.nl";

			$mail->subject="[".$gegevens["stap1"]["boekingsnummer"]."] Ingevulde enquête";

			$mail->plaintext="";

#			$mail->html_top="";
			$mail->html=$mailhtml;
#			$mail->html_bottom="";

			$mail->send();

			chalet_log("enquête ingevuld",true,true);

			header("Location: ".$_SERVER["REQUEST_URI"]."&enqfilled=1");
			exit;
		}

	} else {
		$onjuisteurl=true;
	}
} else {
	$onjuisteurl=true;
}

include "content/opmaak.php";

?>