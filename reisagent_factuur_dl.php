<?php

#
# Factuur downloaden door reisbureau
#
$vars["reisbureau_mustlogin"]=true;
include("admin/vars.php");

if($login_rb->vars["inzicht_boekingen"]) {
	$reisbureau_user_id_inquery="SELECT user_id FROM reisbureau_user WHERE reisbureau_id=(SELECT reisbureau_id FROM reisbureau_user WHERE user_id='".addslashes($login_rb->user_id)."')";
} else {
	$reisbureau_user_id_inquery=$login_rb->user_id;
}
$db->query("SELECT f.filename, b.boeking_id FROM boeking b, factuur f WHERE f.factuur_id='".addslashes($_GET["f"])."' AND f.boeking_id=b.boeking_id AND b.reisbureau_user_id IN (".$reisbureau_user_id_inquery.")".$andq.";");
if($db->next_record()) {
	if(file_exists($vars["unixdir"]."pdf/facturen/".$db->f("filename"))) {
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$db->f("filename").'"');
		readfile($vars["unixdir"]."pdf/facturen/".$db->f("filename"));
	} else {
		trigger_error("factuur ".$db->f("filename")." niet gevonden",E_USER_NOTICE);
	}
} else {
	trigger_error("geen toegang tot factuur ".$_GET["f"],E_USER_NOTICE);
}

?>