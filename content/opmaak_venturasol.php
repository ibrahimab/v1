<?php
$page_id = $id;
# Te includen bestand bepalen
if($language_content) {
	if(file_exists("content/_meertalig/".$id."_venturasol_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_venturasol_".$vars["taal"].".html";
	} elseif(file_exists("content/_meertalig/".$id."_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_".$vars["taal"].".html";
	}
} else {
	if(file_exists("content/".$id."_venturasol.html") and $id<>"aanbiedingen") {
		$include="content/".$id."_venturasol.html";
	} elseif(file_exists("content/".$id."_nieuw.html")) {
		$include="content/".$id."_nieuw.html";
	} elseif(file_exists("content/".$id.".html")) {
		$include="content/".$id.".html";
	}
}
if(!$include) {
	if($_SERVER["HTTP_REFERER"]) {
		if(!preg_match("@\.php/@",$_SERVER["REQUEST_URI"])) {
			trigger_error("_notice: geen include-bestand bekend",E_USER_NOTICE);
		}
	}
	header("Location: ".$vars["path"],true,301);
	exit;
}

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";

echo $opmaak->header_begin();

echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<!--[if IE]><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" /><![endif]-->\n";
echo "<title>";
if($id=="index") {
	echo wt_he($vars["websitenaam"])." - ".wt_he(txt("subtitel"));
	$vars["facebook_title"]=$vars["websitenaam"]." - ".txt("subtitel");
} else {
	if($title[$id] and $id) {
		echo wt_he($title[$id])." - ";
		$vars["facebook_title"]=$title[$id];
	}
	echo wt_he($vars["websitenaam"]);
}
echo "</title>";

if($vars["page_with_tabs"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/tabs.css.phpcache?cache=".@filemtime("css/tabs.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
}

// Link to CSS files
echo $opmaak->link_rel_css();

if(!$vars["page_with_tabs"]) {
	# jQuery UI theme laden
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css?cache=".@filemtime("css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css")."\" />\n";
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_venturasol.css?cache=".@filemtime("css/opmaak_venturasol.css")."\" />\n";
if(file_exists("css/".$id."_venturasol.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_venturasol.css?cache=".@filemtime("css/".$id."_venturasol.css")."\" />\n";
} elseif(file_exists("css/".$id.".css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id.".css?cache=".@filemtime("css/".$id.".css")."\" />\n";
}
if(file_exists("css/".$id."_venturasol_extra.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_venturasol_extra.css?cache=".@filemtime("css/".$id."_venturasol_extra.css")."\" />\n";
}
if($voorkant_cms and !$_GET["cmsuit"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/voorkantcms_venturasol.css?cache=".@filemtime("css/voorkantcms_venturasol.css")."\" />\n";
}

if(preg_match("/MSIE 6/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie6_venturasol.css?cache=".@filemtime("css/ie6_venturasol.css")."\" />\n";
}
if(preg_match("/MSIE 7/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7.css?cache=".@filemtime("css/ie7.css")."\" />\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7_venturasol.css?cache=".@filemtime("css/ie7_venturasol.css")."\" />\n";
}
if(preg_match("/MSIE 8/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie8.css?cache=".@filemtime("css/ie8.css")."\" />\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie8_venturasol.css?cache=".@filemtime("css/ie8_venturasol.css")."\" />\n";
}
# Fancybox
if($vars["jquery_fancybox"]) {
	echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.4.css?c=1\" type=\"text/css\" media=\"screen\" />\n";
}

echo "<script>";
// Hides the tabs + zoekblok during initialization
echo 'document.write(\'<style type="text/css">	#tabs, #zoekenboek_overlay { visibility: hidden; } #body_zoek-en-boek #zoekblok, #body_zoek-en-boek #verfijn { visibility: hidden; } </style>\');';
echo "</script>";

# JQuery
echo "<script type=\"text/javascript\" src=\"".wt_he($vars["jquery_url"])."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".wt_he($vars["jqueryui_url"])."\" ></script>\n";

echo "<link rel=\"shortcut icon\" href=\"".$vars["path"]."favicon_venturasol.ico\" />\n";

if($vars["canonical"]) {
	echo "<link rel=\"canonical\" href=\"".wt_he($vars["canonical"])."\" />\n";
}


# meta name robots
if(!$vars["canonical"] and ($_GET["back"] or $_GET["backtypeid"] or $_GET["filled"] or $_GET["page"] or $_GET["PHPSESSID"] or $id=="boeken")) {
	$robot_noindex=true;
}
if($robot_noindex or $robot_nofollow) {
	echo "<meta name=\"robots\" content=\"".($robot_noindex ? "no" : "")."index,".($robot_nofollow ? "no" : "")."follow\" />\n";
}
echo "<meta name=\"description\" content=\"".wt_he(($meta_description ? $meta_description : ($title[$id]&&$id&&$id<>"index" ? $title[$id] : txt("description"))))."\" />\n";

# Facebook/Open Graph-gegevens in header opnemen
echo facebook_opengraph();

# Google+
// echo "<link href=\"https://plus.google.com/118061823772005667855\" rel=\"publisher\" />\n";

echo $opmaak->header_end();

echo "</head>\n";

echo $opmaak->body_tag();

echo "<div id=\"wrapper\">";

echo "<div id=\"top\">";

echo "<div id=\"submenu\">";
echo "<table id=\"submenu_table\" class=\"table\"><tr><td>";
while(list($key,$value)=each($submenu)) {
	$submenuteller++;
	if($value<>"-") {
		echo "<a href=\"".$vars["path"].txt("menu_".$key).(@in_array($key,$submenu_url_zonder_punt_php) ? "" : ".php")."\">";
		if($key=="favorieten") {
			 echo html("submenutitle_favorieten")." (<span id=\"favorietenaantal\">".intval($vars["bezoeker_aantal_favorieten"])."</span>)";
		} else {
			echo wt_he($value);
		}
		echo "</a>";
		if($submenuteller<(count($submenu))) {
			echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
		}
	}
}
echo "</td><td>";
echo "</td></tr></table>";
echo "</div>\n"; # afsluiten submenu

echo "<div id=\"topfoto\" class=\"noprint\" style=\"background-image:url('".$vars["path"].($vars["venturasol_topfoto"] ? $vars["venturasol_topfoto"] : "pic/topfoto_venturasol_1.jpg")."');\">";
echo "<a href=\"".$vars["path"]."\" id=\"logo\" class=\"".($vars["website"]=="Y" ? "logo-vacances" : "")."\">&nbsp;</a>";
echo "<div id=\"tagline\" class=\"noprint\"><h1><b>Chalets en<br/>appartementen</b><br/>in Frankrijk</h1></div>";
echo "<div style=\"clear: both;\"></div>\n";
echo "<div class=\"paymenticons\" id=\"kleinelogos\">";

if (in_array($vars['website'], $vars['anvr']) && in_array($vars['website'], $vars['sgr_c'])) {
	
	// anvr + sgr + calamiteitenfonds logo
	echo "<a href=\"".$vars["path"]."voorwaarden.php\"><img src=\"".$vars["path"]."pic/anvr_sgr_calamiteitenfonds_hoofdmenu.png\" height=\"27\" style=\"border:0;\" alt=\"Stichting Garantiefonds Reisgelden\" /></a>";
	
} elseif (in_array($vars['website'], $vars['anvr']) && !in_array($vars['website'], $vars['sgr_c'])) {
	
	// anvr logo
	echo "<a href=\"".$vars["path"]."voorwaarden.php\"><img src=\"".$vars["path"]."pic/anvr_hoofdmenu.png\" height=\"27\" style=\"border:0;\" alt=\"ANVR\" /></a>";
}

# Docdata payment logos
if($vars["docdata_payments"]) {
	if(count($vars["docdata_payments"]) > 0) {
		foreach($vars["docdata_payments"] as $key => $value) {
			echo "<span class=\"". $value["by"] ."\" title=\"". $value["title"] ."\"></span>";
		}
	}
}
echo "</div>";
echo "<div style=\"clear: both;\"></div>\n";

echo "</div>\n"; # afsluiten topfoto
echo "<div style=\"clear: both;\"></div>\n";


# alleen voor print
echo "<div id=\"menubalk_print\" class=\"onlyprint\">";
echo "<div id=\"menubalk_print_info\">";
echo "<h2>".wt_he($vars["websitenaam"])."</h2>";
echo "<b>".wt_he(ereg_replace("http://([a-z0-9\.]*)/.*","\\1",$vars["basehref"]))."</b><p><b>".html("telefoonnummer")."</b></p>";
echo "</div>"; # afsluiten #menubalk_print_info
echo "<div style=\"clear: both;\"></div>\n";
echo "</div>"; # afsluiten #menubalk_print

echo "<div id=\"menubalk\" class=\"noprint\">";

echo "<div id=\"hoofdmenu\" class=\"noprint\">";
while(list($key,$value)=each($menu)) {
	if($vars["active_menu_item"]) {
		$checkid=$vars["active_menu_item"];
	} else {
		$checkid=$id;
	}
	if($menuteller) {
		echo "<span class=\"hoofdmenuspacer\">|</span>";
	}
	$menuteller++;
	echo "<a href=\"".$vars["path"];
	if($key<>"index") {
		echo txt("menu_".$key).".php";
	}
	echo "\">&nbsp;&nbsp;";
	if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
		echo "<span class=\"hoofdmenu_actief\">";
	}
	echo wt_he($value);
	if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
		echo "</span>";
	}
	echo "&nbsp;&nbsp;</a>";
}
echo "</div>\n"; # afsluiten hoofdmenu

echo "</div>"; # afsluiten menubalk


echo "<div style=\"clear: both;\" class=\"noprint\"></div>\n";

echo "</div>\n"; # afsluiten top


# Balk boven content
echo "<div id=\"balkbovencontent\" class=\"noprint\">";

echo "<div id=\"meldingen\">";
if($helemaalboven) echo $helemaalboven;
$rechtsboven=str_replace("<font size=\"1\">","<font>",$rechtsboven);
if($rechtsboven) {
	if($helemaalboven) echo "&nbsp;&nbsp;";
	echo $rechtsboven;
}
echo "</div>"; # afsluiten meldingen


echo "<div style=\"clear: both;\"></div>\n";

echo "</div>"; # afsluiten balkbovencontent

# breadcrumbbalk
echo "<div id=\"breadcrumbbalk\" class=\"noprint\">";
echo "</div>"; # afsluiten breadcrumbbalk


# Content
echo "<div id=\"content\">";

if($vars["verberg_linkerkolom"]) {
	echo "<div id=\"contentvolledig\">";

	# Content includen
	include($include);

	//echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	echo "</div>\n";
} else {
	echo "<div id=\"bloklinks_blok\" class=\"noprint\">";

	echo "<div id=\"bloklinks\" class=\"noprint\">";

	if(!$vars["verberg_zoekenboeklinks"]) {
		echo "<div id=\"zoekenboek_leeg\">&nbsp;</div>";
	}

	if($id=="index") {
		// second-homes-banner
		if($vars["website"]=="X") {
			// echo "<a href=\"http://www.venturasol-vastgoed.nl/\" target=\"_blank\" id=\"hoofdpagina_banner_secondhomes\" class=\"analytics_track_external_click\"></a>";
		} elseif($vars["website"]=="Y") {
			echo "<a href=\"https://www.venturasol.nl/\" target=\"_blank\" id=\"hoofdpagina_banner_particulieren\" class=\"analytics_track_external_click\"></a>";
		}
	}

	echo "</div>\n"; # afsluiten bloklinks

	echo "</div>\n"; # afsluiten bloklinks_blok

	echo "<div id=\"contentrechts\">";

	if($id<>"index" and $id<>"toonaccommodatie" and !$laat_titel_weg) {
		if($header[$id]) {
			echo "<h1>".wt_he($header[$id])."</h1>";
		} else {
			echo "<h1>".wt_he($title[$id])."</h1>";
		}
	}

	# Content includen
	if($id=="index") {
		include($include);
	} else {
		if($id=="zoek-en-boek") {
			# zorgen voor hoge content i.v.m. "verfijn"-blok
			echo "<div style=\"min-height:890px;\">";
		} else {
			echo "<div style=\"min-height:290px;\">";
		}
		include($include);
		echo "</div>"; # afsluiten naamloze div

		if($last_acc_html and $id<>"saved" and (!$vars["verberg_directnaar"] or $id=="zoek-en-boek") and !$vars["verberg_lastacc"]) {
			echo $last_acc_html;
		}
		//echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	}
	echo "</div>\n"; # afsluiten contentrechts
}

#echo "</div>\n";

echo "</div>\n"; # afsluiten content


echo "<div style=\"clear: both;\"></div>\n";


# telefoonblok
if(!$vars["verberg_linkerkolom"] and (!$vars["verberg_linkerkolom"] or $id=="toonaccommodatie")) {
	echo "<div id=\"telefoonblok\" class=\"noprint".($id<>"contact" ? " telefoonblokhover" : "")."\"".($id<>"contact" ? " onclick=\"document.location.href='".$vars["path"].txt("menu_contact").".php';\"" : "").">";
	echo "<div id=\"telefoonblok_nummer\"><table class=\"table\"><tr><td><img src=\"".$vars["path"]."pic/icon_telefoon_venturasol.gif\" alt=\"Call us\"></td><td>".html("telefoonnummer_telefoonblok")."</td></tr></table></div>";
	echo "<div id=\"telefoonblok_open\">".html("openingstijden_telefoonblok")."</div>";
	echo "</div>"; # afsluiten telefoonblok
}

# breadcrumbs
if($id<>"index" and !$vars["leverancier_mustlogin"] and !$vars["verberg_breadcrumbs"]) {
	echo "<div id=\"breadcrumb_wrapper\" class=\"noprint\">";
	echo "<div id=\"breadcrumb_overlay\" class=\"noprint\">";
	echo "<a href=\"".$vars["path"]."\">".wt_he(ucfirst(txt("menutitle_index")))."</a>";
	if(!is_array($breadcrumbs)) {
		$breadcrumbs["last"]=$title[$id];
	}
	while(list($key,$value)=each($breadcrumbs)) {
		echo "&nbsp;&nbsp;&gt;&nbsp;&nbsp;";
		if($key<>"last") echo "<a href=\"".wt_he($vars["path"].$key)."\">";
		echo wt_he($value);
		if($key<>"last") echo "</a>";
	}
	echo "</div>"; # afsluiten breadcrumb_overlay
	echo "</div>"; # afsluiten breadcrumb_wrapper
}
echo "<div id=\"colofon_wrapper\" class=\"noprint\">";
echo "<div id=\"colofon\" class=\"noprint\">".wt_he($vars["websitenaam"])." - <a href=\"mailto:".wt_he($vars["email"])."\">".wt_he($vars["email"])."</a> - ".html("telefoonnummer_colofon"). "</div>";



// # footer met links
// echo"<div id=\"footerWrap\">";
// if($id=="index") {
// 	$rel_nofollow=false;
// } else {
// 	$rel_nofollow=true;
// }
// echo "<div class=\"divSepIND\">";
// echo "<br><b>&copy; SuperSki</b><br><br>";
// echo "<li><a href=\"".$vars["path"]."algemenevoorwaarden.php\">Algemene voorwaarden</a></li><li><a href=\"".$vars["path"]."disclaimer.php\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\">Sitemap</a></li>";
// echo "</div>";
// echo "<div class=\"wrap\">";
// echo "<div class=\"divContentIND\">";
// echo "<br><b>Onze bestemmingen</b><br><br>";
// echo "<li><a href=\"".$vars["path"]."land/Frankrijk/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Chalets in Frankrijk</a></li>";
// echo "<li><a href=\"".$vars["path"]."land/Oostenrijk/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Chalets in Oostenrijk</a></li>";
// echo "<li><a href=\"".$vars["path"]."land/Zwitserland/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Chalets in Zwitserland</a></li>";
// echo "<li><a href=\"".$vars["path"]."land/Italie/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Chalets in Itali&euml;</a></li>";
// echo "<li><a href=\"".$vars["path"]."land/Duitsland/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Chalets in Duitsland</a></li>";
// echo "</div>";
// echo "<div class=\"divContentIND\">";
// echo "<br><b>Populaire skigebieden</b><br><br>";
// echo "<li><a href=\"".$vars["path"]."skigebied/Les_Trois_Vallees/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Les Trois Vall&eacute;es</a></li>";
// echo "<li><a href=\"".$vars["path"]."skigebied/Paradiski_-_Les_Arcs/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Les Arcs / Paradiski</a></li>";
// echo "<li><a href=\"".$vars["path"]."skigebied/Zillertal/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Zillertal</a></li>";
// echo "<li><a href=\"".$vars["path"]."skigebied/Zell_am_See_Kaprun/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Zell am See / Kaprun</a></li>";
// echo "<li><a href=\"".$vars["path"]."skigebied/Les_Deux_Alpes/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Les Deux Alpes</a></li>";
// echo "</div>";
// echo "<div class=\"divContentIND\">";
// echo "<br><b>Populaire skidorpen</b><br><br>";
// echo "<li><a href=\"".$vars["path"]."plaats/Konigsleiten/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in K&ouml;nigsleiten</li></a>";
// echo "<li><a href=\"".$vars["path"]."plaats/Zell_am_See/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Zell am See</a></li>";
// echo "<li><a href=\"".$vars["path"]."plaats/Val_Thorens/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Val Thorens</a></li>";
// echo "<li><a href=\"".$vars["path"]."plaats/Chatel/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Ch&acirc;tel</a></li>";
// echo "<li><a href=\"".$vars["path"]."plaats/Les_Menuires/\"".($rel_nofollow ? " rel=\"nofollow\"" : "").">Wintersport in Les Menuires</a></li>";
// echo "</div>";
// echo "</div>";
// echo "</div>";

echo "</div>"; # afsluiten colofon_wrapper

if(!$vars["verberg_linkerkolom"] and !$vars["verberg_zoekenboeklinks"]) {

	echo "<div id=\"zoekenboek_overlay\" class=\"noprint\">";

	echo "<div class=\"zoekenboek_kop\"><span class=\"bloklinksrechts_kop\">";
	echo html("snelzoeken","index");
	echo "</span></div>";

	echo "<form method=\"get\" action=\"".$vars["path"].txt("menu_zoek-en-boek").".php\" name=\"zoeken\" id=\"form_zoekenboeklinks\">";
	echo "<input type=\"hidden\" name=\"filled\" value=\"1\">";
	echo "<input type=\"hidden\" name=\"referer\" value=\"2\">"; # t.b.v. statistieken
	echo "<input type=\"hidden\" name=\"selb\" value=\"0\">"; # doorgeven of mensen klikken op "selecteer bestemming"

	if(is_array($vars["zoekenboeklinks_hidden_form_fields"])) {
		while(list($key,$value)=each($vars["zoekenboeklinks_hidden_form_fields"])) {
			echo "<input type=\"hidden\" name=\"".wt_he($key)."\" value=\"".wt_he($value)."\">";
		}
	}

	# Selecteer een bestemming
	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<a href=\"#\" class=\"zoekenboek_invulveld_bestemming\">".html("selecteerbestemming","index")."<span style=\"font-size:0.7em;\">&nbsp;</span>&raquo;</a>";
	echo "</div>";

	// # Skigebied-array vullen
	// $vars["skigebied"]["00AAAAA___-- ".txt("skigebied","index")." --"]=0;
	// $vars["skigebied"]["00AAAAB___".txt("geenvoorkeur","index")]=0;

	// $db->query("SELECT DISTINCT s.skigebied_id, s.naam, s.kortenaam1, s.kortenaam2, s.kortenaam3, s.kortenaam4, l.naam".$vars["ttv"]." AS land, l.naam AS landnl, l.land_id, s.koppeling_1_1, s.koppeling_1_2, s.koppeling_1_3, s.koppeling_1_4, s.koppeling_1_5, s.koppeling_2_1, s.koppeling_2_2, s.koppeling_2_3, s.koppeling_2_4, s.koppeling_2_5, s.koppeling_3_1, s.koppeling_3_2, s.koppeling_3_3, s.koppeling_3_4, s.koppeling_3_5, s.koppeling_4_1, s.koppeling_4_2, s.koppeling_4_3, s.koppeling_4_4, s.koppeling_4_5, s.koppeling_5_1, s.koppeling_5_2, s.koppeling_5_3, s.koppeling_5_4, s.koppeling_5_5 FROM skigebied s, plaats p, land l, type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.websites LIKE '%".$vars["website"]."%' AND a.plaats_id=p.plaats_id AND l.land_id=p.land_id AND s.skigebied_id=p.skigebied_id ORDER BY l.naam".$vars["ttv"].", s.naam;");
	// while($db->next_record()) {
	// 	$landen[$db->f("land")]=true;

	// 	if($landen_sort[$db->f("land_id")]) {
	// 		$sorteer=$landen_sort[$db->f("land_id")];
	// 	} else {
	// 		$sorteer=$db->f("land");
	// 	}

	// 	if(!$landgehad[$db->f("land")]) {
	// 		$vars["skigebied"][$sorteer."AAAAA___".txt("heelskigebieden","accommodaties")." ".$db->f("land")]=$db->f("land_id")."-0";

	// 		$landnaam[$db->f("land_id")]=$db->f("land");
	// 		$landgehad[$db->f("land")]=true;
	// 	}
	// 	if($db->f("kortenaam1")) {
	// 		$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam1")]=$db->f("land_id")."-".$db->f("skigebied_id")."-1";
	// 		if($db->f("kortenaam2")) {
	// 			$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam2")]=$db->f("land_id")."-".$db->f("skigebied_id")."-2";
	// 		}
	// 		if($db->f("kortenaam3")) {
	// 			$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam3")]=$db->f("land_id")."-".$db->f("skigebied_id")."-3";
	// 		}
	// 		if($db->f("kortenaam4")) {
	// 			$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam4")]=$db->f("land_id")."-".$db->f("skigebied_id")."-4";
	// 		}
	// 	} else {
	// 		$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("naam")]=$db->f("land_id")."-".$db->f("skigebied_id");
	// 	}
	// }
	// setlocale(LC_COLLATE,"nl_NL.ISO8859-1");
	// ksort($vars["skigebied"],SORT_LOCALE_STRING);
	// setlocale(LC_COLLATE,"C");

	// echo "<div class=\"zoekenboek_invulveld\">";
	// echo "<select name=\"fsg\" class=\"selectbox\">";
	// while(list($key,$value)=each($vars["skigebied"])) {
	// 	if(ereg("^([0-9]+)-0$",$value,$regs)) {
	// 		if($optgroup_open) echo "</optgroup>\n";
	// 		echo "<optgroup label=\"".wt_he($landnaam[$regs[1]])."\">\n";
	// 		$optgroup_open=true;
	// 	}
	// 	if(preg_match("/^([0-9]+)-([0-9]+)/",$value,$regs)) {
	// 		$landdid_currect=$regs[1];
	// 		$skigebiedid_currect=$regs[2];
	// 	}
	// 	echo "<option value=\"".$value."\"";
	// 	if(($_GET["filled"] and $_GET["fsg"]==$value) or ($skigebiedid and $skigebiedid==$skigebiedid_currect) or ($landinfo["id"] and $landinfo["id"]==$landdid_currect and preg_match("/AAAAA/",$key))) {
	// 		if(!$skigebied_selected) {
	// 			$skigebied_selected=true;
	// 			echo " selected";
	// 			if($value) $save_query["regio"]=$vars["skigebied_nl"][$value];
	// 		}
	// 	}
	// 	echo ">";
	// 	echo wt_he(ereg_replace("^.*___","",$key))."</option>";
	// }
	// if($optgroup_open) echo "</optgroup>\n";

	// echo "</select>";
	// echo "</div>";


	# aantalpersonen-array vullen
	// $vars["aantalpersonen"]["-"]="-- ".txt("aantalpersonen","index")." --";
	$vars["aantalpersonen"][0]=txt("geenvoorkeur","index");
	for($i=1;$i<=40;$i++) {
		$vars["aantalpersonen"][$i]=$i;
	}

#	echo "<div class=\"zoekenboek_tekst\" style=\"margin-top:10px;margin-bottom:3px;\">".html("aantalpersonen","index")."</div>";
	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fap\" class=\"selectbox\" data-placeholder=\"".html("aantalpersonen","index")."\">";
	echo "<option value=\"\"> </option>";
	while(list($key,$value)=each($vars["aantalpersonen"])) {
		echo "<option value=\"".($key=="-" ? "0" : $key)."\"";
		if($key==="-") echo " selected";
		echo ">".wt_he($value)."</option>";
	}
	echo "</select>";
	echo "</div>";
#	echo "<div class=\"zoekenboek_tekst\" style=\"margin-top:10px;margin-bottom:3px;\">".html("aankomstdatum","index")."</div>";

	# Aankomstdatum vullen
#	$vars["aankomstdatum_weekend_afkorting"]["-"]="-- ".txt("aankomstdatum","index")." --";
#	$vars["aankomstdatum_weekend_afkorting"][0]=txt("geenvoorkeur","index");
	if(is_array($vars["aankomstdatum_weekend_afkorting"])) {
		ksort($vars["aankomstdatum_weekend_afkorting"]);
	}

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fad\" class=\"selectbox\" data-placeholder=\"".html("aankomstdatum","index")."\">";
	echo "<option value=\"\"> </option>";
	if(is_array($vars["aankomstdatum_weekend_afkorting"])) {
		while(list($key,$value)=each($vars["aankomstdatum_weekend_afkorting"])) {
			# Weken die al voorbij zijn niet tonen (2 dagen na aankomstdatum niet meer tonen)
			if(mktime(0,0,0,date("m"),date("d")-2,date("Y"))<$key or !$key or $key==="-") {
				echo "<option value=\"".($key=="-" ? "0" : $key)."\"";
				if($key==="-") echo " selected";
				echo ">".$value."</option>\n";
			}
		}
	}
	echo "</select>";
	echo "</div>";

	# Zoeken op tekst
	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<input type=\"text\" name=\"fzt\" class=\"tekstzoeken\" value=\"".html("zoekoptekst","index")."\" onfocus=\"if(this.value=='".html("zoekoptekst","index")."') this.value='';\" onblur=\"if(this.value=='') this.value='".html("zoekoptekst","index")."';\">";
	echo "</div>";

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<input type=\"submit\" value=\" ".html("zoeken","index")."\">";
	echo "</div>";

	echo "<div style=\"margin-top:7px;margin-bottom:0px;\"><a href=\"#\" id=\"uitgebreidzoeken\">".html("uitgebreidzoeken","index")."</a></div>";
	echo "</form>";

	echo "</div>\n";

} elseif($lhtml) {

	# Afwijkende content linkerblok plaatsen
	echo "<div id=\"zoekenboek_overlay\" class=\"noprint\">";
	echo $lhtml;
	echo "</div>";
}

if($voorkant_cms and !$_GET["cmsuit"] and $interneinfo) {
	echo "<div id=\"interneinfo_rechts\" class=\"noprint\">";
	echo $interneinfo;
	echo "</div>"; # interneinfo_rechts
}

# Zorgen dat zoekenboek_overlay naar beneden schuift i.v.m. "laatst bekeken"-button
if($vars["zoekenboek_overlay_doorschuiven"]) {
	echo "<style type=\"text/css\"><!--\n#zoekenboek_overlay {\ntop:".(264+$vars["zoekenboek_overlay_doorschuiven"])."px;\n}\n--></style>\n";
}

echo "\n</div><!-- END #wrapper -->\n";

# Ajaxloader in het midden van de pagina
echo "<div id=\"ajaxloader_page\"><img src=\"".$vars["path"]."pic/ajax-loader-large2.gif\" alt=\"loading...\"></div>";

# Balk met cookie-melding cookiebalk
if($opmaak->toon_cookiebalk()) {
	echo "<p>&nbsp;</p>";
	echo "<div class=\"clear\"></div>";
	echo "<div id=\"cookie_bottombar\" class=\"noprint\"><div id=\"cookie_bottombar_wrapper\"><div id=\"cookie_bottombar_text\">".html("cookiemelding","vars",array("h_1"=>"<a href=\"".$vars["path"]."privacy-statement.php\">","h_2"=>"</a>"))."</div><div id=\"cookie_bottombar_close\">sluiten</div></div></div>";
}

# Balk met opvallende melding
if($vars["opvalmelding_tonen"] and (!$_COOKIE["opvalmelding_gelezen"] or $vars["lokale_testserver"])) {
	echo "<div id=\"opval_bottombar\" class=\"noprint\"><div id=\"opval_bottombar_wrapper\"><div id=\"opval_bottombar_text\">".nl2br(html("opvalmelding","vars",array("h_1"=>"<a href=\"mailto:".$vars["email"]."\">","h_2"=>"</a>","v_email"=>$vars["email"])))."</div><div id=\"opval_bottombar_close\">&nbsp;</div></div></div>";
}

// show html that has to be at the bottom of the page
echo $opmaak->html_at_the_bottom;

######################### Load javascript files

if($vars["jquery_maphilight"]) {
	# Google Maps API
	echo "<script src=\"".$vars["path"]."scripts/jquery.maphilight.min.js\" type=\"text/javascript\"></script>\n";
}

if($vars["googlemaps"]) {
	# Google Maps API
	echo "<script src=\"https://maps-api-ssl.google.com/maps/api/js?v=3&amp;sensor=false\" type=\"text/javascript\"></script>\n";
}

# Google Analytics
echo googleanalytics();

// fill $lazyLoadJs
$lazyLoadJs = $opmaak->lazyLoadJs();


if($vars["page_with_tabs"]) {
	# jQuery Address: t.b.v. correcte verwerking hashes in URL
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.address-1.5.min.js'";
}

# Fancybox
if($vars["jquery_fancybox"]) {
	$lazyLoadJs[] = "'".$vars["path"]."fancybox/jquery.fancybox-1.3.4.pack.js'";
}

if($vars["jquery_scrollup"]) {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.scrollup.js'";
}

# Javascript-functions
$lazyLoadJs[] = "'".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."'";
$lazyLoadJs[] = "'".$vars["path"]."scripts/functions_venturasol.js?cache=".@filemtime("scripts/functions_venturasol.js")."'";
if(file_exists("scripts/functions_".$page_id.".js")) {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/functions_".$page_id.".js?cache=".@filemtime("scripts/functions_".$page_id.".js")."'";
}

# IE8-javascript
if(preg_match("/MSIE 8/",$_SERVER["HTTP_USER_AGENT"])) {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/ie8.js?cache=".@filemtime("scripts/ie8.js")."'";
}
?>

<script type="text/javascript">

	var deferredJSFiles = [<?php echo implode(",", $lazyLoadJs); ?>];

	function downloadJSAtOnload() {
		if (!deferredJSFiles.length)
			return;
		var deferredJSFile = deferredJSFiles.shift();
		var element = document.createElement('script');
		element.src = deferredJSFile;
		element.async = true;
		element.onload = element.onreadystatechange = function() {
			if (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')
				downloadJSAtOnload();
		};
		document.body.appendChild(element);
	}

	if (window.addEventListener) {
		window.addEventListener('load', downloadJSAtOnload, false);
	} else if (window.attachEvent) {
		window.attachEvent('onload', downloadJSAtOnload);
	} else {
		window.load = downloadJSAtOnload;
	}

</script>

<?php

echo $opmaak->body_end();

echo "</body>";
echo "</html>";

?>