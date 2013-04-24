<?php


include("admin/vars.php");

if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}


$vars["italissima_topfoto"]="pic/tijdelijk/reisblog/topfoto_reisblog.jpg";

$meta_description="Beleef de reis van Chantal en Gijs door Toscane! Wat Chantal en Gijs gaan doen dat bepalen onze Facebook-fans. Stem mee en maak kans op een weekverblijf in Toscane.";

# Opsomming rechts
$blog["blok_rechts"].="<div class=\"blog_titels_rechts\">";
$db->query("SELECT reisblog_id, titel_rechts FROM reisblog WHERE actief=1 AND plaatsingsdatum<NOW() ORDER BY titel_rechts_volgorde;");
$blog["blok_rechts"].="<ul>";
while($db->next_record()) {
	$blog["blok_rechts"].="<li>";
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
	$blog["blok_rechts"].="</li>";
}
$blog["blok_rechts"].="</ul>";
$blog["blok_rechts"].="</div>"; # afsluiten blog_titels_rechts

$facebook_like="<div class=\"clear\"></div><div id=\"facebook_like\">Like en stem mee via <a href=\"https://www.facebook.com/Italissima.nl\" target=\"_blank\">Facebook</a>:<div style=\"margin-left:10px;width:150px;\" class=\"fb-like\" data-href=\"https://www.facebook.com/Italissima.nl\" data-send=\"false\" data-layout=\"button_count\" data-width=\"250\" data-show-faces=\"false\" data-font=\"arial\"></div></div>";

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

		if(preg_match("/(https?:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9]+))/",$blog["inhoud"],$regs)) {
			$blog["youtube"]=trim($regs[2]);
			$blog["inhoud"]=str_replace($regs[1],"",$blog["inhoud"]);
		}

		# Foto's artikel
		$d = dir($vars["unixdir"]."pic/cms/reisblog");
		while (false !== ($entry = $d->read())) {
			if(preg_match("/^".intval($_GET["b"])."-([0-9]+)\.jpg$/",$entry,$regs)) {
				$afbeeldingen[intval($regs[1])]=$entry;
				$vars["facebook_opengraph_image"][]=$vars["basehref"]."pic/cms/reisblog/".$entry;
			}
		}
		$d->close();
		if(is_array($afbeeldingen)) {
			ksort($afbeeldingen);
		}

		# facebook opengraph aanvullende foto's
		$d = dir($vars["unixdir"]."pic/cms/reisblog_rechts");
		while (false !== ($entry = $d->read())) {
			if(preg_match("/^".intval($_GET["b"])."-([0-9]+)\.jpg$/",$entry,$regs)) {
				$vars["facebook_opengraph_image"][]=$vars["basehref"]."pic/cms/reisblog_rechts/".$entry;
			}
		}

		if(is_array($vars["facebook_opengraph_image"])) {
			asort($vars["facebook_opengraph_image"]);
		}


		$title["reisblog"]=$blog["titel"];
		$breadcrumbs["reisblog"]="Reisblog: Beleef Toscane";
		$breadcrumbs["last"]=$blog["titel"];
	} else {
		header("Location: ".$vars["path"]."reisblog");
		exit;
	}

} else {
	$title["reisblog"]="Reisblog: Beleef Toscane";
	if($_GET["voorwaarden"]) {
		$breadcrumbs["reisblog"]="Reisblog: Beleef Toscane";
		$breadcrumbs["last"]="Voorwaarden";
		$vars["facebook_opengraph_image"]=$vars["basehref"]."pic/tijdelijk/reisblog/reisblog_opengraph.jpg";
	} else {
		$breadcrumbs["last"]="Reisblog: Beleef Toscane";
		$vars["facebook_opengraph_image"]=$vars["basehref"]."pic/tijdelijk/reisblog/reisblog_opengraph.jpg";
	}
}

# Banner rechts
$blog["blok_rechts"].="<a href=\"https://www.facebook.com/Italissima.nl\" target=\"_blank\"><img src=\"".$vars["path"]."pic/tijdelijk/reisblog/facebookbanner.jpg\" width=\"200\" height=\"350\" border=\"0\"></a>";


include "content/opmaak.php";

?>