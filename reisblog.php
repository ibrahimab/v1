<?php


include("admin/vars.php");

if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}


$vars["italissima_topfoto"]="pic/tijdelijk/reisblog/topfoto_reisblog.jpg";

#$meta_description="Op dit blog vind je artikelen die te maken hebben met Italië en het aanbod van Italissima, aanbieder van agriturismi en andere vakantiehuizen in Italië.";

# Opsomming rechts
$blog["blok_rechts"].="<div class=\"blog_titels_rechts\">";
$db->query("SELECT reisblog_id, titel_rechts FROM reisblog WHERE actief=1 AND plaatsingsdatum<NOW() ORDER BY titel_rechts_volgorde;");
while($db->next_record()) {
	$blog["blok_rechts"].="<div>";
	if($_GET["b"]==$db->f("reisblog_id")) {
		$blog["blok_rechts"].="<b>";
	} else {
		$blog["blok_rechts"].="<a href=\"".$vars["path"]."reisblog?b=".$db->f("reisblog_id")."\">";
	}
	$blog["blok_rechts"].=wt_he($db->f("titel_rechts"));
	if($_GET["b"]==$db->f("reisblog_id")) {
		$blog["blok_rechts"].="</b>";
	} else {
		$blog["blok_rechts"].="</a>";
	}
	$blog["blok_rechts"].="</div>";
}
$blog["blok_rechts"].="</div>"; # afsluiten blog_titels_rechts

$facebook_like="<div id=\"facebook_like\">Volg de complete reis via Facebook:<div style=\"margin-left:10px;width:150px;\" class=\"fb-like\" data-href=\"https://www.facebook.com/Italissima.nl\" data-send=\"false\" data-layout=\"button_count\" data-width=\"250\" data-show-faces=\"false\" data-font=\"arial\"></div></div>";

if($_GET["b"]) {
	$db->query("SELECT reisblog_id, titel, inleiding, inhoud, inhoud_html, UNIX_TIMESTAMP(plaatsingsdatum) AS plaatsingsdatum FROM reisblog WHERE 1=1 AND reisblog_id='".intval($_GET["b"])."' AND actief=1 AND plaatsingsdatum<NOW();");
	if($db->next_record()) {
		$blog["reisblog_id"]=$db->f("reisblog_id");
		$blog["titel"]=$db->f("titel");
		$blog["inleiding"]=$db->f("inleiding");
		$blog["inhoud"]=$db->f("inhoud");
		$blog["inhoud_html"]=$db->f("inhoud_html");
		$blog["plaatsingsdatum"]=$db->f("plaatsingsdatum");
		$blog["accommodatiecodes"]=$db->f("accommodatiecodes");

		if(preg_match("/(http:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9]+)[^[:blank:]]+)/",$blog["inhoud"],$regs)) {
			$blog["youtube"]=$regs[2];
			$blog["inhoud"]=str_replace($regs[1],"",$blog["inhoud"]);
		}

		# Foto's artikel
		$d = dir($vars["unixdir"]."pic/cms/reisblog");
		while (false !== ($entry = $d->read())) {
			if(preg_match("/^".intval($_GET["b"])."-([0-9]+)\.jpg$/",$entry,$regs)) {
				$afbeeldingen[intval($regs[1])]=$entry;
			}
		}
		$d->close();
		if(is_array($afbeeldingen)) {
			ksort($afbeeldingen);
		}

		# Foto's rechts
		$d = dir($vars["unixdir"]."pic/cms/reisblog_rechts");
		while (false !== ($entry = $d->read())) {
			if(preg_match("/^".intval($_GET["b"])."-([0-9]+)\.jpg$/",$entry)) {
				$blog["blok_rechts"].="<a href=\"".wt_he($vars["path"]."t/t.php?src=".urlencode("pic/cms/reisblog_rechts/".$entry)."&w=800")."\" class=\"fotopopup_border\" rel=\"group1\"><img src=\"".wt_he($vars["path"]."t/t.php?src=".urlencode("pic/cms/reisblog_rechts/".$entry)."&w=200")."\" width=\"198\"></a>";
			}
		}
		$d->close();

		$title["reisblog"]=$blog["titel"];
		$breadcrumbs["reisblog"]="Reisblog: Beleef Toscane";
		$breadcrumbs["last"]=$blog["titel"];
	} else {
		header("Location: ".$vars["path"]."blog.php");
		exit;
	}
} else {
	$title["reisblog"]="Reisblog: Beleef Toscane";
	$breadcrumbs["last"]="Reisblog: Beleef Toscane";
}




include "content/opmaak.php";

?>