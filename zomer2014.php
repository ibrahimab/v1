<?php

$title["zomer2014"]="Tarieven zomer 2014 al bekend";


#$laat_titel_weg=true;
include("admin/vars.php");


# Alleen voor Italissima
if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

#
# seizoen_id van het te tonen seizoen
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
}

include "content/opmaak.php";

?>