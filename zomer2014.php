<?php

//
//
//
// LET OP!!!! Na opzetten van deze pagina voor een nieuw seizoen: ook .htaccess aanpassen (zorgen voor 301 van oud naar nieuw): RewriteRule ^zomer2013\.php$ /zomer2014.php [L,R=301]
//
//
//

$title["zomer2014"]="Nu al te boeken voor 2014";
$vars["zoekform_italissima_nieuwe_tarieven"]=true;

$laat_titel_weg=true;
$vars["verberg_zoekenboeklinks"]=true;

#$laat_titel_weg=true;
include("admin/vars.php");


# Alleen voor Italissima
if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

#
# seizoen_id van het te tonen seizoen (ook aanpassen in siteclass.voorraad_gekoppeld)
#
$vars["themainfo"]["tarievenbekend_seizoen_id"]=23;

if($vars["themainfo"]["tarievenbekend_seizoen_id"]) {
	# $vars["aankomstdatum_weekend"] opnieuw vullen (met het specifieke seizoen voor dit thema)
	unset($vars["aankomstdatum_weekend"]);
	$db->query("SELECT UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind, seizoen_id FROM seizoen WHERE seizoen_id='".$vars["themainfo"]["tarievenbekend_seizoen_id"]."' ORDER BY begin, eind;");
	if($db->num_rows()) {
		$vars["aankomstdatum_weekend"][0]=$vars["geenvoorkeur"];
		while($db->next_record()) {
			# Aankomstdatum-array vullen
			$timeteller=$db->f("begin");
			while($timeteller<=$db->f("eind")) {
				# aankomstdatum_weekend vullen (alleen indien niet langer dan 8 dagen geleden)
				if($timeteller>=time()-691200) {
					$vars["aankomstdatum_weekend"][$timeteller]=txt("weekend","vars")." ".date("j",$timeteller)." ".datum("MAAND JJJJ",$timeteller,$vars["taal"]);
				}
				$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
			}
		}
		@ksort($vars["aankomstdatum_weekend"]);
	}

	# Totaal aantal accommodaties opvragen: kan getoond worden bij "tarievenbekend_seizoen_id"
	$db->query("SELECT DISTINCT COUNT(t.type_id) AS aantal FROM accommodatie a, plaats p, skigebied s, land l, leverancier lv, type t WHERE lv.leverancier_id=t.leverancier_id AND t.accommodatie_id=a.accommodatie_id AND l.land_id=p.land_id AND p.plaats_id=a.plaats_id AND p.skigebied_id=s.skigebied_id AND t.websites LIKE '%".addslashes($vars["website"])."%' AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.weekendski=0;");
	if($db->next_record()) {
		$totaal_tarievenbekend_seizoen_id=$db->f("aantal");
	}

	$vars["themainfo"]["naam"]=$title["zomer2014"];
	$vars["themainfo"]["toelichting"]="Onderstaande vakantiehuizen zijn nu al te boeken voor de zomer van 2014! Deze pagina zal de komende periode regelmatig worden uitgebreid met meer vakantiehuizen.";

	$vars["zoekform_thema"]=true;
	$id="zoek-en-boek";
	include("zoek-en-boek.php");
	exit;

}

include "content/opmaak.php";

?>