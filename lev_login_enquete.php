<?php

$vars["leverancier_mustlogin"]=true;
$vars["verberg_lastacc"]=true;
$vars["verberg_zoekenboeklinks"]=true;
$vars["verberg_directnaar"]=true;
$vars["verberg_linkerkolom"]=true;

include("admin/vars.php");

$robot_noindex=true;

if($login_lev->logged_in) {
	
	# Taal bepalen
	if($login_lev->vars["inlog_taal"]) {
		$org_taal=$vars["taal"];
		$vars["taal"]=$login_lev->vars["inlog_taal"];
		if($vars["taal"]<>"nl") $vars["ttv_lev_login"]="_".$vars["taal"];
	}
}

echo "<!DOCTYPE html>\n<html>\n<head><title></title>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_chalet.css?cache=".@filemtime("css/opmaak_chalet.css")."\" />\n";
echo "</head>";

echo "<body>";

echo "<div style=\"margin:10px;\">";

if($login_lev->logged_in and $_GET["bid"]) {
	$db->query("SELECT b.boeking_id FROM boeking b, type t WHERE b.boeking_id='".addslashes($_GET["bid"])."' AND b.type_id=t.type_id AND (t.leverancier_id='".addslashes($login_lev->user_id)."' OR t.beheerder_id='".addslashes($login_lev->user_id)."' OR t.eigenaar_id='".addslashes($login_lev->user_id)."') AND b.boekingsnummer<>'' AND b.goedgekeurd=1 AND b.geannuleerd=0 AND b.seizoen_id>=17;");
	if($db->next_record()) {
		$vars["lev_login_enquete"]=true;
		$gegevens=get_boekinginfo($_GET["bid"]);
		include("content/cms_boekingen_enquete.html");
	}
}

echo "</div>";

echo "</body>";
echo "</html>";

?>