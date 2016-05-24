<?php

$page_id = $id;
# Te includen bestand bepalen
if($language_content) {
	if(file_exists("content/_meertalig/".$id."_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_".$vars["taal"].".html";
	}
} else {
	if(file_exists("content/".$id."_chalet.html")) {
		$include="content/".$id."_chalet.html";
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


if($vars["website"]=="E") {
	# SPDY-header sturen (prefetchen diverse CSS-bestanden)
#	header('X-Associated-Content: "'.$vars["path"].'css/opmaak_alle_sites.css.phpcache?cache='.@filemtime("css/opmaak_alle_sites.css.phpcache").'&type='.$vars["websitetype"].'", "'.$vars["path"].'css/tabs.css.phpcache?cache='.@filemtime("css/tabs.css.phpcache").'&type='.$vars["websitetype"].'"');
}

echo "<!DOCTYPE html>\n";
echo "<html lang=\"".wt_he($vars["taal"])."\">\n";
echo "<head>\n";

echo $opmaak->header_begin();

echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<!--[if IE]><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" /><![endif]-->\n";
echo "<title>";
if($grizzly_title) {
	echo $grizzly_title;
} else {
	if($id=="index") {
		echo wt_he($vars["websitenaam"])." ".wt_he(txt("subtitel"));
		$vars["facebook_title"]=$vars["websitenaam"]." - ".txt("subtitel");
	} else {
		if($title[$id] and $id) {
			echo wt_he($title[$id])." - ";
			$vars["facebook_title"]=$title[$id];
		}
		echo wt_he($vars["websitenaam"]);
	}
}
echo "</title>";
if($vars["page_with_tabs"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/tabs.css.phpcache?cache=".@filemtime("css/tabs.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
}

if($onMobile){
    ?><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /><?php
}

// Link to CSS files
echo $opmaak->link_rel_css();

if(!$vars["page_with_tabs"]) {
	# jQuery UI theme laden
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css?cache=".@filemtime("css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css")."\" />\n";
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_chalet.css?cache=".@filemtime("css/opmaak_chalet.css")."\" />\n";
if(file_exists("css/".$id."_chalet.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_chalet.css?cache=".@filemtime("css/".$id."_chalet.css")."\" />\n";
} elseif(file_exists("css/".$id.".css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id.".css?cache=".@filemtime("css/".$id.".css")."\" />\n";
}
if(file_exists("css/".$id."_chalet_extra.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_chalet_extra.css?cache=".@filemtime("css/".$id."_chalet_extra.css")."\" />\n";
}
if($vars["website"]=="E") {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_chalet_eu.css?cache=".@filemtime("css/opmaak_chalet_eu.css")."\" />\n";
}
if($vars["website"]=="D") {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_chaletonline_de.css?cache=".@filemtime("css/opmaak_chaletonline_de.css")."\" />\n";
}
if($voorkant_cms and !$_GET["cmsuit"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/voorkantcms.css?cache=".@filemtime("css/voorkantcms.css")."\" />\n";
}
if(preg_match("/MSIE 6/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie6_chalet.css?cache=".@filemtime("css/ie6_chalet.css")."\" />\n";
}
if(preg_match("/MSIE 7/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7.css?cache=".@filemtime("css/ie7.css")."\" />\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7_chalet.css?cache=".@filemtime("css/ie7_chalet.css")."\" />\n";
}
if(preg_match("/MSIE 8/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie8.css?cache=".@filemtime("css/ie8.css")."\" />\n";
}
# Zorgen dat zoekenboek_overlay naar beneden schuift i.v.m. "laatst bekeken"-button
if($vars["zoekenboek_overlay_doorschuiven"]<>0) {
	echo "<style type=\"text/css\"><!--\n#zoekenboek_overlay {\ntop:".(264+$vars["zoekenboek_overlay_doorschuiven"])."px;\n}\n--></style>\n";
}
# Fancybox
if($vars["jquery_fancybox"]) {
	echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.4.css?c=1\" type=\"text/css\" media=\"screen\" />\n";
}

# My booking new website
if ($my_booking && ($_SERVER["USE_SYMFONY_ENV"] || $vars["lokale_testserver"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mybooking-new-website.css?cache=".@filemtime("css/mybooking-new-website.css")."\" />\n";
}

if($vars["livechat_code"] and preg_match("@^([0-9])-(.*)$@",$vars["livechat_code"],$regs)) {
	#
	# Chatsysteem
	#
echo "<style type=\"text/css\">
#bloklinks .bloklinks_blauwelijn {
	margin-top: 25px;
}
#zoekenboek_leeg {
	margin-bottom: -25px;
}
#zoekenboek_overlay {
	margin-top: 25px;
}
#telefoonblok {
	margin-top: -18px;
}
#toonaccommodatie_foto {
	margin-top: 22px;
	margin-bottom: -16px;
}
</style>\n";
}

// language specific css
if(file_exists("css/language-specific-".$vars["taal"].".css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/language-specific-".$vars["taal"].".css?cache=".@filemtime("css/language-specific-".$vars["taal"].".css")."\" />\n";
}

echo "<script>";
// Hides the tabs + zoekblok during initialization
echo 'document.write(\'<style type="text/css">	#tabs, #zoekenboek_overlay, .hide_during_pageload { visibility: hidden; } #body_zoek-en-boek #zoekblok, #body_zoek-en-boek #verfijn { visibility: hidden; } </style>\');';
echo "</script>";

# JQuery
echo "<script type=\"text/javascript\" src=\"".wt_he($vars["jquery_url"])."\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".wt_he($vars["jqueryui_url"])."\"></script>\n";

echo "<link rel=\"shortcut icon\" href=\"".$vars["path"]."favicon.ico\" />\n";
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
if($vars["website"]=="C") {
	echo "<link href=\"https://plus.google.com/+ChaletNlWintersport\" rel=\"publisher\" />\n";
}

// hreflang (MAR-66)
if($vars["website"]=="C") {
	echo "<link href=\"https://www.chalet.nl".wt_he($_SERVER["REQUEST_URI"])."\" hreflang=\"nl-NL\" rel=\"alternate\" />\n";
	echo "<link href=\"https://www.chalet.be".wt_he($_SERVER["REQUEST_URI"])."\" hreflang=\"nl-BE\" rel=\"alternate\" />\n";
}
if($vars["website"]=="B") {
	echo "<link href=\"https://www.chalet.be".wt_he($_SERVER["REQUEST_URI"])."\" hreflang=\"nl-BE\" rel=\"alternate\" />\n";
	echo "<link href=\"https://www.chalet.nl".wt_he($_SERVER["REQUEST_URI"])."\" hreflang=\"nl-NL\" rel=\"alternate\" />\n";
}

echo $opmaak->header_end();

echo "</head>\n";
echo $opmaak->body_tag();
echo $opmaak->google_tag_manager();
echo "<div id=\"wrapper\">";
echo "<div id=\"top\">";
echo "<div id=\"logo\">";
if($id<>"index") echo "<a href=\"".$vars["path"]."\">";
echo "<img src=\"".$vars["path"]."pic/logo_chalet";
#if($vars["websitetype"]==1 and $vars["taal"]=="nl" and $vars["websiteland"]=="nl") echo "_10jr";
if($vars["taal"]=="en") echo "_eu";
if($vars["websiteland"]=="be") echo "_be";
if($vars["websiteland"]=="de") echo "_de";
if($vars["websitetype"]==4 or $vars["websitetype"]==5) echo "_tour";
echo ".gif?c=2\" width=\"188\" height=\"140\" style=\"border:0;\" alt=\"".wt_he($vars["websitenaam"])."\">";
if($id<>"index") echo "</a>";
echo "</div>\n";

echo "<div id=\"menubalk_print\" class=\"onlyprint\">";
echo "<h2>".wt_he($vars["websitenaam"])."</h2>";
echo "<b>".wt_he(ereg_replace("http://([a-z0-9\.]*)/.*","\\1",$vars["basehref"]))."</b><p><b>".html("telefoonnummer")."</b></p>";


echo "</div>";

echo "<div id=\"menubalk\">";

echo "<div id=\"submenu\">";
echo "<table class=\"table\"><tr><td>";
while(list($key,$value)=each($submenu)) {
	$submenuteller++;
	if($value<>"-") {
		if($key=="zomerhuisje") {
			if($vars["website"]=="C" or $vars["website"]=="T") {
				echo "<span id=\"submenu_zomerhuisje\">";
				echo "<a href=\"https://www.zomerhuisje.nl/";
				if($vars["website"]=="T") echo "?fromsite=chalettour";
				echo "\" target=\"_blank\">";
				echo wt_he($value);
				echo "</a>";
				echo "</span>";
				if($vars["website"]<>"T") {
#					echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
				}
			}
		} else {
			echo "<a href=\"".$vars["path"].txt("menu_".$key).(@in_array($key,$submenu_url_zonder_punt_php) ? "" : ".php")."\">";
			if($key=="favorieten") {
				 echo html("submenutitle_favorieten")." (<span id=\"favorietenaantal\">".intval($vars["bezoeker_aantal_favorieten"])."</span>)";
			} else {
				echo wt_he($value);
			}
			echo "</a>";
			if(($vars["website"]<>"D" and $vars["website"]<>"E" and $vars["website"]<>"B") or $submenuteller<(count($submenu)-1)) {
				echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
			}
		}
	}
}
echo "</td><td>";

#
# vlaggetjes (tijdelijk?) uigezet op verzoek van Bert - 28 juli 2010
#
#if(!$vars["wederverkoop"]) {
#	if($vars["taal"]=="en") {
#		echo "<a href=\"https://www.chalet.nl/\"><img src=\"".$vars["path"]."pic/vlag_nl_klein.gif\" border=\"0\" width=\"17\" height=\"11\" style=\"padding-top:0px;\"></a>";
#	} else {
#		echo "<a href=\"https://www.chalet.eu/\"><img src=\"".$vars["path"]."pic/vlag_en_klein.gif\" border=\"0\" width=\"17\" height=\"11\" style=\"padding-top:0px;\"></a>";
#	}
#}
echo "</td></tr></table>";
echo "</div>\n";

// Zoover-awards
if (in_array($vars['website'], $vars['zoover'])) {

	echo '<a href="' . $vars['path'] . 'zoover-awards-2016" class="zoover_awards">' .
         '<img src="' . $vars['path'] . 'pic/tijdelijk/zoover/chalet/zoover-winner-2016.png" /></a>';
}

echo "<div id=\"topfoto\">";
echo "<img src=\"".$vars["path"]."pic/topfoto_";
if($id=="index") {
	echo "1";
} else {
	echo "2";
}
echo ".jpg\" width=\"725\" height=\"106\" alt=\"topfoto\">";
echo "</div><!--END #topfoto-->\n";
echo "<div id=\"lijn\">&nbsp;</div>\n";
echo "<div id=\"hoofdmenubalk\">";
echo "<div id=\"hoofdmenu\">";
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
	echo "\">";
#	echo "&nbsp;&nbsp;";
	if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
		echo "<span class=\"hoofdmenu_actief\">";
	}
	echo wt_he($value);
	if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
		echo "</span>";
	}
#	echo "&nbsp;&nbsp;";
	echo "</a>";
}
echo "</div><!--END #hoofdmenu-->\n";

// if($vars["website"]=="C" and $id<>"werkenbij") {
// 	echo "<div class=\"wij-zoeken-collegas\">";
// 	echo "<a href=\"".$vars["path"]."werkenbij#koks\">Wij zoeken chaletstaf &raquo;</a>";
// 	echo "</div>\n"; // close .wij-zoeken-collegas
// }


echo "<div class=\"paymenticons\" id=\"kleinelogos\">";

if (in_array($vars['website'], $vars['anvr']) && in_array($vars['website'], $vars['sgr_c'])) {
	echo "<a href=\"".$vars["path"].txt("menu_voorwaarden").".php\" class=\"sgrlogo_hoofdmenu\"><img src=\"".$vars["path"]."pic/anvr_sgr_calamiteitenfonds_hoofdmenu.png\" height=\"27\" alt=\"Stichting Garantiefonds Reisgelden\" /></a>";
}

if (in_array($vars['website'], $vars['anvr']) && !in_array($vars['website'], $vars['sgr_c'])) {
	echo "<a href=\"".$vars["path"].txt("menu_voorwaarden").".php\" class=\"sgrlogo_hoofdmenu\"><img src=\"".$vars["path"]."pic/anvr_hoofdmenu.png\" height=\"27\" alt=\"ANVR\" /></a>";
}

if (in_array($vars['website'], $vars['sgr'])) {
	echo "<a href=\"".$vars["path"].txt("menu_voorwaarden").".php#sgr\" class=\"sgrlogo_hoofdmenu\"><img src=\"".$vars["path"]."pic/sgr_hoofdmenu.png\" height=\"27\" alt=\"SGR\" /></a>";
}

if($vars["docdata_payments"]) {
	if(count($vars["docdata_payments"]) > 0) {
		foreach($vars["docdata_payments"] as $key => $value) {
			if (!isset($vars['docdata_payments_hide_logos']) || !in_array($key, $vars['docdata_payments_hide_logos'])) {
				echo "<span class=\"". $value["by"] ."\" title=\"". $value["title"] ."\"></span>";
			}
		}
	}
}

if ($vars['website'] === 'D') {
	echo "<a href=\"".$vars["path"].txt("menu_algemenevoorwaarden").".php#a7.9\" class=\"reisegarantlogo_hoofdmenu\"><img src=\"".$vars["path"]."pic/reise_garant_hoofdmenu.png\" height=\"27\" alt=\"Reisegarant\" /></a>";
}

echo "</div><!-- END #kleinelogos -->\n";
echo "</div><!--END #hoofdmenubalk-->\n";
echo "</div>\n";
echo "<div style=\"clear: both;\"></div>\n";
echo "</div>\n";
# Balk boven content
echo "<div id=\"balkbovencontent\" class=\"noprint\">";
# Bekeken en bewaarde accommodaties
if($last_acc and $id<>"saved") {
#	echo "<div id=\"bekekenbewaard\">";
#	echo "<a href=\"".$vars["path"]."saved.php\">".html("laatstbekekenaccommodaties","index")."</a>";
#	echo "</div>";
}
echo "<div id=\"meldingen\">";
if($helemaalboven) echo $helemaalboven;
$rechtsboven=str_replace("<span class=\"x-small\">","<span>",$rechtsboven);
if($rechtsboven) {
	if($helemaalboven) echo "&nbsp;&nbsp;";
	echo $rechtsboven;
}
echo "</div><!-- END #meldingen -->";

echo "<div style=\"clear: both;\"></div>\n";

echo "</div>"; # afsluiten balkbovencontent

# breadcrumbbalk
echo "<div id=\"breadcrumbbalk\" class=\"noprint\">";
echo "</div>"; # afsluiten breadcrumbbalk


# Content
echo "<div id=\"content\">";

if($vars["verberg_linkerkolom"]) {
	echo "<div id=\"contentvolledig\">";

#	echo "<div style=\"height:40px\"></div>";

#	echo "<div id=\"blauwelijn_links\">&nbsp;</div>\n";
#	echo "<div class=\"koptekst_rechts\"></div>\n";
#	echo "<div style=\"clear: both;\"></div>\n";

	# Content includen
	include($include);
#	echo "<br>&nbsp;";

#	if($id=="toonaccommodatie" and $last_acc_html) {
#		echo $last_acc_html;
#		echo "<div style=\"clear: both;\"></div>\n";
#	}

	// turned off 'terugnaarboven' in favor of dynamic 'terugnaarboven' link, JIRA-IB-5
	//echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";

	if(!$vars["wederverkoop"]) {
#		echo "<div id=\"blauwelijn_onderaan\"></div>\n";
#		echo "<div id=\"contactgegevens\">".wt_he($vars["websitenaam"])."&nbsp;&nbsp;&nbsp;".html("telefoonnummer")."&nbsp;&nbsp;&nbsp;<a href=\"mailto:".wt_he($vars["email"])."\">".wt_he($vars["email"])."</a></div>";
	}

	echo "</div><!-- END #contentvolledig -->\n";
} else {
	echo "<div id=\"bloklinks_blok\" class=\"noprint\">";

	echo "<div id=\"bloklinks\">";
	echo "<div class=\"bloklinks_blauwelijn\"></div>\n";

	if(!$vars["verberg_zoekenboeklinks"]) {
		echo "<div id=\"zoekenboek_leeg\">&nbsp;</div>";
		echo "<div class=\"bloklinks_blauwelijn\"></div>\n";
	}

	if($vars["verberg_directnaar"] and $vars["in_plaats_van_directnaar"]) {
		echo $vars["in_plaats_van_directnaar"];
	} elseif(!$vars["verberg_directnaar"]) {
		echo "<div id=\"directnaar\" class=\"noprint\">";
		echo "<div id=\"directnaar_kop\"><span class=\"bloklinks_kop\">".html("directnaar","index")."</span></div>";
		echo "<a href=\"".$vars["path"].txt('canonical_accommodatiepagina') . '/'  . txt("menu_land")."/".wt_convert2url_seo(txt("frankrijk","index"))."/\"><span class=\"flag france\"></span>".html("frankrijk_directnaar","index")."</a>";
		echo "<a href=\"".$vars["path"].txt('canonical_accommodatiepagina') . '/'  .txt("menu_land")."/".wt_convert2url_seo(txt("oostenrijk","index"))."/\"><span class=\"flag austria\"></span>".html("oostenrijk_directnaar","index")."</a>";
		echo "<a href=\"".$vars["path"].txt('canonical_accommodatiepagina') . '/'  . txt("menu_land")."/".wt_convert2url_seo(txt("zwitserland","index"))."/\"><span class=\"flag switzerland\"></span>".html("zwitserland_directnaar","index")."</a>";
		echo "<a href=\"".$vars["path"].txt('canonical_accommodatiepagina') . '/'  . txt("menu_land")."/".wt_convert2url_seo(txt("italie","index"))."/\"><span class=\"flag italy\"></span>".html("italie_directnaar","index")."</a>";
		// echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url_seo(txt("duitsland","index"))."/\">".html("duitsland_directnaar","index")."</a><br />";
#		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".txt("menu_land")."/".wt_convert2url_seo(txt("frankrijk","index"))."/\">".html("overigelanden","index")."</a><br />";

		echo "</div>\n";
		if($id=="index") {
			# Opsomming "Waarom Chalet.nl?"
			echo "<div id=\"hoofdpagina_waaromchalet\" onclick=\"document.location.href='".txt("menu_wie-zijn-wij").".php';\">";
			echo "<div class=\"kop\">".html("waarom","index",array("v_websitenaam"=>$vars["websitenaam"]))."</div>";
			echo "<div><ul>";
			for($i=1;$i<=6;$i++) {
				if(html("waarom".$i,"index")<>"-") {
					echo "<li>".html("waarom".$i,"index", ['v_jaar' => calculateDiffYear($vars['oprichting_chalet'])])."</li>";
				}
			}
			echo "</ul></div>";
			echo "</div>\n";
		}

		// if($vars["website"]=="C" and $id<>"alpedhuzes" and $id=="index") {
		// 	// Alpe d'HuZes
		// 	echo "<div id=\"alpedhuzes\">";
		// 	echo "<a href=\"".$vars["path"]."alpe-d-huzes\"><img src=\"".$vars["path"]."pic/tijdelijk/ad6-2015/alpedhuzes.gif\" width=\"168\" height=\"83\" border=\"0\"></a>";
		// 	echo "</div>";
		// }

	}

	echo "</div>\n";

	echo "</div>\n";

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
			echo "<div style=\"min-height:1000px;\">";
		} else {
			echo "<div style=\"min-height:290px;\">";
		}
		include($include);
		echo "</div>";

		if($last_acc_html and $id<>"saved" and (!$vars["verberg_directnaar"] or $id=="zoek-en-boek") and !$vars["verberg_lastacc"]) {
			echo $last_acc_html;
		}

		//echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	}
	if(!$vars["wederverkoop"] and $id<>"index") {
#		echo "<div id=\"blauwelijn_onderaan\"></div>\n";
#		echo "<div id=\"contactgegevens\">".wt_he($vars["websitenaam"])."&nbsp;&nbsp;&nbsp;".html("telefoonnummer")."&nbsp;&nbsp;&nbsp;<a href=\"mailto:".wt_he($vars["email"])."\">".wt_he($vars["email"])."</a></div>";
	}
	echo "</div><!-- END #contentrechts -->\n";
}

echo "</div><!-- END #content -->\n";
echo "<div style=\"clear: both;\"></div>\n";



echo "<div style=\"clear: both;\"></div>\n";


# telefoonblok
if(!$vars["verberg_linkerkolom"] and $vars["website"]<>"T" and (!$vars["verberg_linkerkolom"] or $id=="toonaccommodatie")) {
	echo "<div id=\"telefoonblok\" class=\"noprint".($id<>"contact" ? " telefoonblokhover" : "")."\"".($id<>"contact" ? " onclick=\"document.location.href='".$vars["path"].txt("menu_contact").".php';\"" : "").">";
	echo "<div id=\"telefoonblok_nummer\"><table class=\"table\"><tr><td><img src=\"".$vars["path"]."pic/icon_telefoon_winter.gif\" alt=\"Call us\"></td><td>".html("telefoonnummer_telefoonblok")."</td></tr></table></div>";
	echo "<div id=\"telefoonblok_open\">".html("openingstijden_telefoonblok")."</div>";
	echo "</div>"; # afsluiten telefoonblok

	if($vars["livechat_code"] and preg_match("@^([0-9])-(.*)$@",$vars["livechat_code"],$regs)) {
		# chat-blok
		echo "<div id=\"chatblok\" class=\"noprint\">";
		if($vars["lokale_testserver"]) {
			echo "<div data-id=\"".wt_he($regs[2])."\" class=\"livechat_button\"><a href=\"http://www.livechatinc.com/\">live chat software</a></div>";
		} else {
			echo "<div data-id=\"".wt_he($regs[2])."\" class=\"livechat_button\"><a href=\"http://www.livechatinc.com/\">live chat software</a></div>";
		}
		echo "</div>"; # afsluiten chatblok
	}
}


# breadcrumbs
if($id<>"index" and !$vars["leverancier_mustlogin"] and !$vars["verberg_breadcrumbs"]) {
	echo "<div id=\"breadcrumb_wrapper\" class=\"noprint\">";
	echo "<div id=\"breadcrumb_overlay\" class=\"noprint\">";
	echo "<div class=\"breadcrumb_vocabulary\" itemscope itemtype=\"http://data-vocabulary.org/Breadcrumb\">";
	echo "<a href=\"".$vars["basehref"]."\" itemprop=\"url\">".wt_he(ucfirst(txt("menutitle_index")))."</a>";
	echo "</div>";

	if(!is_array($breadcrumbs)) {
		$breadcrumbs["last"]=$title[$id];
	}
	while(list($key,$value)=each($breadcrumbs)) {
		echo "<div class=\"breadcrumb_vocabulary\" itemscope itemtype=\"http://data-vocabulary.org/Breadcrumb\">";
		echo "&nbsp;&nbsp;&gt;&nbsp;&nbsp;";
		if($key<>"last") echo "<a href=\"".wt_he($vars["path"].$key)."\" itemprop=\"url\">";
		echo "<span itemprop=\"title\">" . wt_he($value) . "</span>";
		if($key<>"last") echo "</a>";
		echo "</div>";
	}
	echo "</div>"; # afsluiten breadcrumb_overlay
	echo "</div>"; # afsluiten breadcrumb_wrapper
}
echo "<div id=\"colofon_wrapper\" class=\"noprint\">";
echo "<div id=\"colofon\" class=\"noprint\">";
if($vars["website"]=="C") {
	echo wt_he($vars["websiteinfo"]["langewebsitenaam"][$vars["website"]]);
} else {
	echo wt_he($vars["websiteinfo"]["websitenaam"][$vars["website"]]);
}
echo " - <a href=\"mailto:".wt_he($vars["websiteinfo"]["email"][$vars["website"]])."\">".wt_he($vars["websiteinfo"]["email"][$vars["website"]])."</a> - ".html("telefoonnummer_colofon");
echo "</div><!-- END #colofon -->\n";
echo "</div><!-- END #colofon_wrapper -->\n";

$voorwaarden_footer = in_array($vars['website'], $vars['anvr']) ?
                      '<li><a href="' . $vars['path'] . 'voorwaarden.php" rel="nofollow">Voorwaarden</a></li>' :
                      '<li><a href="' . $vars['path'] . 'algemenevoorwaarden.php" rel="nofollow">Algemene voorwaarden</a></li>';

if($vars["website"]=="C" or $vars["website"]=="B" or $vars["website"]=="T") {
	if($id!="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"disclaimerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>&copy; ".wt_he($vars["websitenaam"])."</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."veelgestelde-vragen\">Veelgestelde vragen</a></li>";
		echo $voorwaarden_footer;
		echo "<li><a href=\"".$vars["path"]."disclaimer.php\" rel=\"nofollow\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\" rel=\"nofollow\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\" rel=\"nofollow\">Sitemap</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Onze bestemmingen</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Frankrijk/\" rel=\"nofollow\">Wintersport in Frankrijk</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Oostenrijk/\" rel=\"nofollow\">Wintersport in Oostenrijk</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Zwitserland/\" rel=\"nofollow\">Wintersport in Zwitserland</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Italie/\" rel=\"nofollow\">Wintersport in Itali&euml;</a></li>";
		// echo "<li><a href=\"".$vars["path"]."wintersport/land/Duitsland/\" rel=\"nofollow\">Wintersport in Duitsland</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skigebieden</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Les_Trois_Vallees/\" rel=\"nofollow\">Wintersport in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Paradiski_-_Les_Arcs/\" rel=\"nofollow\">Wintersport in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Zillertal/\" rel=\"nofollow\">Wintersport in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Zell_am_See_Kaprun/\" rel=\"nofollow\">Wintersport in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Les_Deux_Alpes/\" rel=\"nofollow\">Wintersport in Les Deux Alpes</a></li>";
		echo "</ul>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skidorpen</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Konigsleiten/\" rel=\"nofollow\">Wintersport in K&ouml;nigsleiten</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Zell_am_See/\" rel=\"nofollow\">Wintersport in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Val_Thorens/\" rel=\"nofollow\">Wintersport in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Chatel/\" rel=\"nofollow\">Wintersport in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Les_Menuires/\" rel=\"nofollow\">Wintersport in Les Menuires</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	} elseif($id=="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>&copy; ".wt_he($vars["websitenaam"])."</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."veelgestelde-vragen\">Veelgestelde vragen</a></li>";
		echo $voorwaarden_footer;
		echo "<li><a href=\"".$vars["path"]."disclaimer.php\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\">Privacy-statement</a></li><li><a href=\"".$vars["path"]."sitemap\">Sitemap</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Onze bestemmingen</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Frankrijk/\">Wintersport in Frankrijk</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Oostenrijk/\">Wintersport in Oostenrijk</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Zwitserland/\">Wintersport in Zwitserland</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/land/Italie/\">Wintersport in Itali&euml;</a></li>";
		// echo "<li><a href=\"".$vars["path"]."wintersport/land/Duitsland/\">Wintersport in Duitsland</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skigebieden</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Les_Trois_Vallees/\">Wintersport in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Paradiski_-_Les_Arcs/\">Wintersport in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Zillertal/\">Wintersport in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Zell_am_See_Kaprun/\">Wintersport in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/skigebied/Les_Deux_Alpes/\">Wintersport in Les Deux Alpes</a></li>";
		echo "</ul>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skidorpen</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Konigsleiten/\">Wintersport in K&ouml;nigsleiten</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Zell_am_See/\">Wintersport in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Val_Thorens/\">Wintersport in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Chatel/\">Wintersport in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/plaats/Les_Menuires/\">Wintersport in Les Menuires</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}

} elseif($vars["website"]=="E") {
	if($id!="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"disclaimerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>&copy; ".wt_he($vars["websitenaam"])."</b><br><br>";
		echo "<ul><li><a href=\"".$vars["path"]."faq\" rel=\"nofollow\">FAQ</a></li><li><a href=\"".$vars["path"]."conditions.php\" rel=\"nofollow\">Conditions</a></li><li><a href=\"".$vars["path"]."disclaimer.php\" rel=\"nofollow\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\" rel=\"nofollow\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\" rel=\"nofollow\">Sitemap</a></li></ul>";
		echo "</div>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Our destinations</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/country/France/\" rel=\"nofollow\">Chalets in France</a></li>";
		echo"<li><a href=\"".$vars["path"]."winter-sports/country/Austria/\" rel=\"nofollow\">Chalets in Austria</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/country/Switzerland/\" rel=\"nofollow\">Chalets in Switzerland</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/country/Italy/\" rel=\"nofollow\">Chalets in Italy</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski regions</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Les_Trois_Vallees/\" rel=\"nofollow\">Chalets in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."wintersport/region/Paradiski_-_Les_Arcs/\" rel=\"nofollow\">Chalets in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Zillertal/\" rel=\"nofollow\">Chalets in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Zell_am_See_Kaprun/\" rel=\"nofollow\">Chalets in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Les_Deux_Alpes/\" rel=\"nofollow\">Chalets in Les Deux Alpes</a></li>";
		echo "</ul>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski villages</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Konigsleiten/\" rel=\"nofollow\">Chalets in K&ouml;nigsleiten</li></a>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Zell_am_See/\" rel=\"nofollow\">Chalets in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Val_Thorens/\" rel=\"nofollow\">Chalets in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Chatel/\" rel=\"nofollow\">Chalets in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Les_Menuires/\" rel=\"nofollow\">Chalets in Les Menuires</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	} elseif($id=="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>&copy; ".wt_he($vars["websitenaam"])."</b><br><br>";
		echo "<ul><li><a href=\"".$vars["path"]."faq\" rel=\"nofollow\">FAQ</a></li><li><a href=\"".$vars["path"]."conditions.php\">Conditions</a></li><li><a href=\"".$vars["path"]."disclaimer.php\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\">Sitemap</a></li></ul>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Our destinations</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/country/France/\">Chalets in France</a></li>";
		echo"<li><a href=\"".$vars["path"]."winter-sports/country/Austria/\">Chalets in Austria</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/country/Switzerland/\">Chalets in Switzerland</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/country/Italy/\">Chalets in Italy</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski regions</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Les_Trois_Vallees/\">Chalets in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Paradiski_-_Les_Arcs/\">Chalets in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Zillertal/\">Chalets in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Zell_am_See_Kaprun/\">Chalets in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/region/Les_Deux_Alpes/\">Chalets in Les Deux Alpes</a></li>";
		echo "</ul>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski villages</b><br><br>";
		echo "<ul>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Konigsleiten/\">Chalets in K&ouml;nigsleiten</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Zell_am_See/\">Chalets in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Val_Thorens/\">Chalets in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Chatel/\">Chalets in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."winter-sports/resort/Les_Menuires/\">Chalets in Les Menuires</a></li>";
		echo "</ul>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}
} else {
	echo "<div id=\"ondercolofon\" class=\"noprint\"><a href=\"".$vars["path"]."AGB.php\">AGB</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href=\"".$vars["path"]."Datenschutz.php\">Datenschutz</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href=\"".$vars["path"]."Impressum\">Impressum</a></div>";
}

if(!$vars["verberg_linkerkolom"] and !$vars["verberg_zoekenboeklinks"]) {
	echo "<div id=\"zoekenboek_overlay\" class=\"noprint\">";

	#
	# Formulier "snelzoeken"
	#
	echo "<div class=\"bloklinks_kop\" style=\"margin-bottom:10px;\">".html("snelzoeken","index")."</div>";

	echo "<form method=\"get\" action=\"".$vars["path"].txt("menu_zoek-en-boek").".php\" name=\"zoeken\" id=\"form_zoekenboeklinks\">";
	echo "<input type=\"hidden\" name=\"filled\" value=\"1\">";
	echo "<input type=\"hidden\" name=\"referer\" value=\"2\">"; # t.b.v. statistieken
	echo "<input type=\"hidden\" name=\"selb\" value=\"0\">"; # doorgeven of mensen klikken op "selecteer bestemming"

	# Selecteer een bestemming
	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<a href=\"#\" class=\"zoekenboek_invulveld_bestemming\">".html("selecteerbestemming","index")."<span style=\"font-size:0.7em;\">&nbsp;</span>&raquo;</a>";
	echo "</div>";

	# Aankomstdatum vullen
#	$vars["aankomstdatum_weekend_afkorting"]["-"]=txt("aankomstdatum","index");
	$vars["aankomstdatum_weekend_afkorting"][0]=txt("geenvoorkeur","index");
	ksort($vars["aankomstdatum_weekend_afkorting"],SORT_STRING);

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fad\" class=\"selectbox\" data-placeholder=\"".html("aankomstdatum","index")."\">";
	echo "<option value=\"\"> </option>";
	while(list($key,$value)=each($vars["aankomstdatum_weekend_afkorting"])) {
		# Weken die al voorbij zijn niet tonen (2 dagen na aankomstdatum niet meer tonen)
		if(mktime(0,0,0,date("m"),date("d")-2,date("Y"))<$key or !$key or $key==="-") {
			echo "<option value=\"".($key=="-" ? "0" : $key)."\"";
			if($key==="-") echo " selected";
			echo ">".wt_he($value)."</option>\n";
		}
	}
	echo "</select>";
	echo "</div>";

	# aantalpersonen-array vullen
#	$vars["aantalpersonen"]["-"]=txt("aantalpersonen","index");
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

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<input type=\"text\" name=\"fzt\" class=\"tekstzoeken\" value=\"".html("zoekoptekst","index")."\" onfocus=\"if(this.value=='".html("zoekoptekst","index")."') this.value='';\" onblur=\"if(this.value=='') this.value='".html("zoekoptekst","index")."';\">";
	echo "</div>";

#	echo "<div style=\"margin-top:0px;\">&nbsp;</div>";

	echo "<input type=\"submit\" value=\" ".html("zoeken","index")."\">";

	echo "<div style=\"margin-top:6px;margin-bottom:4px;\"><a href=\"#\" id=\"uitgebreidzoeken\">".html("uitgebreidzoeken","index")." &raquo;</a></div>";
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

echo "\n</div><!-- END #wrapper -->\n";

# Ajaxloader in het midden van de pagina
echo "<div id=\"ajaxloader_page\"><img src=\"".$vars["path"]."pic/ajax-loader-large2.gif\" alt=\"loading...\"></div>";

# Balk met cookie-melding cookiebalk
if($opmaak->toon_cookiebalk()) {
	echo "<div id=\"cookie_bottombar\" class=\"noprint\"><div id=\"cookie_bottombar_wrapper\"><div id=\"cookie_bottombar_text\">".html("cookiemelding","vars",array("h_1"=>"<a href=\"".$vars["path"]."privacy-statement.php\">","h_2"=>"</a>"))."</div><div id=\"cookie_bottombar_close\">sluiten</div></div></div>";
}

# Load change desktop to mobile notification

if($onMobile and !$_COOKIE['desktopnotification'] and !$vars["only_for_desktop_available"]){
	echo "<div id=\"notification_bottombar\" class=\"noprint\"><div id=\"notification_bottombar_wrapper\"><div id=\"notification_bottombar_text\">".html("desktopnotification","vars",array("h_1"=>"<a href=\"".$vars["path"]."?setmobile\">","h_2"=>"</a>"))."</div><div id=\"notification_bottombar_close\">sluiten</div></div></div>";
}

# Balk met opvallende melding
if($vars["opvalmelding_tonen"] and (!$_COOKIE["opvalmelding_gelezen"] or $vars["lokale_testserver"])) {
	echo "<div id=\"opval_bottombar\" class=\"noprint\"><div id=\"opval_bottombar_wrapper\"><div id=\"opval_bottombar_text\">".nl2br(html("opvalmelding","vars",array("h_1"=>"<a href=\"mailto:".$vars["email"]."\">","h_2"=>"</a>","v_email"=>$vars["email"])))."</div><div id=\"opval_bottombar_close\">&nbsp;</div></div></div>";
}

// show html that has to be at the bottom of the page
echo $opmaak->html_at_the_bottom;

######################### Load javascript files

if($vars["googlemaps"]) {
	# Google Maps API
	echo "<script src=\"https://maps-api-ssl.google.com/maps/api/js?v=3&amp;sensor=false\" type=\"text/javascript\"></script>\n";
}

// echo "<style type=\"text/css\">#scrollUp {
//     bottom: 20px;
//     right: 20px;
//     padding: 10px 20px;
//     background-color: #555;
//     color: #fff;
// }</style>";

# Google Analytics
echo googleanalytics();

$lazyLoadJs = $opmaak->lazyLoadJs();

if($vars["website"]=="E") {
	# jQuery ms-Dropdown (https://github.com/marghoobsuleman/ms-Dropdown)
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.dd.min.js'";
}

if($vars["page_with_tabs"]) {
	# jQuery Address: t.b.v. correcte verwerking hashes in URL
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.address-1.5.min.js'";
}
# Lazy load Fancybox
if($vars["jquery_fancybox"]) {
	$lazyLoadJs[] = "'".$vars["path"]."fancybox/jquery.fancybox-1.3.4.pack.js'";
}
if($vars["jquery_scrollup"]) {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.scrollup.js'";
}

# Javascript-functions
$lazyLoadJs[] = "'".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."'";
$lazyLoadJs[] = "'".$vars["path"]."scripts/functions_chalet.js?cache=".@filemtime("scripts/functions_chalet.js")."'";

if(file_exists("scripts/functions_".$page_id.".js")) {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/functions_".$page_id.".js?cache=".@filemtime("scripts/functions_".$page_id.".js")."'";
}

# IE8-javascript
if(preg_match("/MSIE 8/",$_SERVER["HTTP_USER_AGENT"])) {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/ie8.js?cache=".@filemtime("scripts/ie8.js")."'";
}

if($vars["livechat_code"] and preg_match("@^([0-9])-(.*)$@",$vars["livechat_code"],$regs)) {
	#
	# Chatsysteem
	#
	$http = 'http://';
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		$http = 'https://';
	}
	$lazyLoadJs[] = "'".$http."cdn.livechatinc.com/tracking.js'";
}
?>

<script type="text/javascript">

	<?php if($vars["livechat_code"] and preg_match("@^([0-9])-(.*)$@",$vars["livechat_code"],$regs)) { ?>

		var __lc = {};
		__lc.license = 2618611;
		__lc.group = <?php echo $regs[1]; ?>;

	<?php } ?>

	var deferredJSFiles = [<?php echo implode(",", $lazyLoadJs); ?>];

	function downloadJSAtOnload() {
		if (!deferredJSFiles.length)
			return;
		var deferredJSFile = deferredJSFiles.shift();
		var element = document.createElement('script');
		element.src = deferredJSFile;
		element.async = true;
		element.onload = element.onreadystatechange = function() {
			if (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete') {
				downloadJSAtOnload();
			}
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
