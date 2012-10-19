<?php

# Te includen bestand bepalen
if($language_content) {
	if(file_exists("content/_meertalig/".$id."_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_".$vars["taal"].".html";
	}
} else {
	if(file_exists("content/".$id."_nieuw.html")) {
		$include="content/".$id."_nieuw.html";
	} elseif(file_exists("content/".$id.".html")) {
		$include="content/".$id.".html";
	}
}
if(!$include) {
	if($_SERVER["HTTP_REFERER"]) {
		trigger_error("geen include-bestand bekend",E_USER_NOTICE);
	}
	header("Location: ".$vars["path"],true,301);
	exit;
}
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\"\n      xmlns:og=\"http://ogp.me/ns#\"\n      xmlns:fb=\"https://www.facebook.com/2008/fbml\">\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9\" />\n";
echo "<title>";
if($grizzly_title) {
	echo $grizzly_title;
} else {
	if($id=="index") {
		echo htmlentities($vars["websitenaam"])." - ".htmlentities(txt("subtitel"));
		$vars["facebook_title"]=$vars["websitenaam"]." - ".txt("subtitel");
	} else {
		if($title[$id] and $id) {
			echo htmlentities($title[$id])." - ";
			$vars["facebook_title"]=$title[$id];
		}
		echo htmlentities($vars["websitenaam"]);
	}
}
echo "</title>";
if($vars["page_with_tabs"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/tabs.css.phpcache?cache=".@filemtime("css/tabs.css.phpcache")."?type=".$vars["websitetype"]."\" />\n";
}

if($vars["page_with_jqueryui"]) {
	# jQuery UI theme laden
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css?cache=".@filemtime("css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css")."\" />\n";
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&type=".$vars["websitetype"]."\" />\n";
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
echo "<link rel=\"shortcut icon\" href=\"".$vars["path"]."favicon.ico\" />\n";
if($vars["canonical"]) {
	echo "<link rel=\"canonical\" href=\"".htmlentities($vars["canonical"])."\" />\n";
} elseif($_SERVER["HTTPS"]=="on") {
	echo "<link rel=\"canonical\" href=\"http://".htmlentities($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."\" />\n";
}
# JQuery
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jquery_url"])."\" ></script>\n";
if($id=="index") {
	# Voor de sliders op de hoofdpagina is een oudere jquery-ui nodig
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.5.3/jquery-ui.min.js\" ></script>\n";
	} else {
#		echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/jquery-ui-1.5.3.min.js\" ></script>\n";
		echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.5.3/jquery-ui.min.js\" ></script>\n";
	}
} else {
	echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jqueryui_url"])."\" ></script>\n";
}
if($vars["googlemaps"]) {
	# Google Maps API
	echo "<script src=\"https://maps-api-ssl.google.com/maps/api/js?v=3&sensor=false\" type=\"text/javascript\"></script>\n";
}

# Chosen
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/jquery.chosen.min.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_chalet.js?cache=".@filemtime("scripts/functions_chalet.js")."\" ></script>\n";
if(file_exists("scripts/functions_".$id.".js")) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_".$id.".js?cache=".@filemtime("scripts/functions_".$id.".js")."\" ></script>\n";
}

# Fancybox
if($vars["jquery_fancybox"]) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.4.pack.js\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.4.css?c=1\" type=\"text/css\" media=\"screen\" />\n";
}

# live.js (live testen van CSS)
#if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
#	echo "<script type=\"text/javascript\" src=\"http://ss.postvak.net/_intern/extra/live.js\"></script>\n";
#}

# meta name robots
if(!$vars["canonical"] and ($_GET["back"] or $_GET["backtypeid"] or $_GET["filled"] or $_GET["page"] or $_GET["PHPSESSID"] or $id=="boeken")) {
	$robot_noindex=true;
}
if($robot_noindex or $robot_nofollow) {
	echo "<meta name=\"robots\" content=\"".($robot_noindex ? "no" : "")."index,".($robot_nofollow ? "no" : "")."follow\" />\n";
}
#echo "<meta name=\"keywords\" content=\"\" />\n";
#echo "<meta name=\"description\" content=\"".htmlentities($vars["websitenaam"])." - ".htmlentities(txt("subtitel"))."\" />";
echo "<meta name=\"description\" content=\"".wt_he(($meta_description ? $meta_description : ($title[$id]&&$id&&$id<>"index" ? $title[$id] : txt("description"))))."\" />\n";

# Facebook/Open Graph-gegevens in header opnemen
echo facebook_opengraph();

# Google+
echo "<link href=\"https://plus.google.com/102116407421806841336\" rel=\"publisher\" />\n";

# Google Analytics
echo googleanalytics();

echo "</head>\n";
echo "<body";
if($id<>"index") echo " onscroll=\"document.getElementById('terugnaarboven').style.visibility='visible'\"";
if($onload) echo " onload=\"".$onload."\"";
echo ">";
echo "<div id=\"wrapper\">";
echo "<div id=\"top\">";
echo "<div id=\"logo\">";
if($id<>"index") echo "<a href=\"".$vars["path"]."\">";
echo "<img src=\"".$vars["path"]."pic/logo_chalet";
#if($vars["websitetype"]==1 and $vars["taal"]=="nl" and $vars["websiteland"]=="nl") echo "_10jr";
if($vars["taal"]<>"nl") echo "_eu";
if($vars["websiteland"]=="be") echo "_be";
if($vars["websitetype"]==4 or $vars["websitetype"]==5) echo "_tour";
echo ".gif?c=2\" width=\"188\" height=\"140\" border=\"0\" alt=\"".htmlentities($vars["websitenaam"])."\">";
if($id<>"index") echo "</a>";
echo "</div>\n";

echo "<div id=\"menubalk_print\" class=\"onlyprint\">";
echo "<h2>".htmlentities($vars["websitenaam"])."</h2>";
echo "<b>".htmlentities(ereg_replace("http://([a-z0-9\.]*)/.*","\\1",$vars["basehref"]))."<p>".html("telefoonnummer")."</b>";


echo "</div>";

echo "<div id=\"menubalk\">";

echo "<div id=\"submenu\">";
echo "<table cellspacing=0 cellpadding=0><tr><td>";
while(list($key,$value)=each($submenu)) {
	$submenuteller++;
	if($value=="newsletter") {
		# Geen nieuwsbrief voor Engelstalige site
		continue;
	}
	if($value<>"-") {
		if($key=="zomerhuisje") {
			if($vars["website"]=="C" or $vars["website"]=="T") {
				echo "<span id=\"submenu_zomerhuisje\">";
				echo "<a href=\"http://www.zomerhuisje.nl/";
				if($vars["website"]=="T") echo "?fromsite=chalettour";
				echo "\" target=\"_blank\">";
				echo htmlentities($value);
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
			if(($vars["website"]<>"E" and $vars["website"]<>"B") or $submenuteller<(count($submenu)-1)) {
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
#		echo "<a href=\"http://www.chalet.nl/\"><img src=\"".$vars["path"]."pic/vlag_nl_klein.gif\" border=\"0\" width=\"17\" height=\"11\" style=\"padding-top:0px;\"></a>";
#	} else {
#		echo "<a href=\"http://www.chalet.eu/\"><img src=\"".$vars["path"]."pic/vlag_en_klein.gif\" border=\"0\" width=\"17\" height=\"11\" style=\"padding-top:0px;\"></a>";
#	}
#}
echo "</td></tr></table>";
echo "</div>\n";

echo "<div id=\"topfoto\">";
echo "<img src=\"".$vars["path"]."pic/topfoto_";
if($id=="index") {
	echo "1";
} else {
	echo "2";
}
echo ".jpg\" width=\"725\" height=\"106\">";
echo "</div>\n";
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
	echo "\">&nbsp;&nbsp;";
	if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
		echo "<span class=\"hoofdmenu_actief\">";
	}
	echo htmlentities($value);
	if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
		echo "</span>";
	}
	echo "&nbsp;&nbsp;</a>";
}
echo "</div>\n";
echo "<div id=\"kleinelogos\">";
if($vars["websiteland"]=="nl") {
	echo "<a href=\"".$vars["path"].txt("menu_algemenevoorwaarden").".php#sgr\"><img src=\"".$vars["path"]."pic/sgr_hoofdmenu.gif\" border=\"0\" width=\"30\" height=\"27\" alt=\"Stichting Garantiefonds Reisgelden\"></a>&nbsp;&nbsp;";
}
echo "</div>\n";
echo "</div>\n";
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
$rechtsboven=str_replace("<font size=\"1\">","<font>",$rechtsboven);
if($rechtsboven) {
	if($helemaalboven) echo "&nbsp;&nbsp;";
	echo $rechtsboven;
}
echo "</div>";

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

	echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";

	if(!$vars["wederverkoop"]) {
#		echo "<div id=\"blauwelijn_onderaan\"></div>\n";
#		echo "<div id=\"contactgegevens\">".htmlentities($vars["websitenaam"])."&nbsp;&nbsp;&nbsp;".html("telefoonnummer")."&nbsp;&nbsp;&nbsp;<a href=\"mailto:".htmlentities($vars["email"])."\">".htmlentities($vars["email"])."</a></div>";
	}

	echo "</div>\n";
} else {
	echo "<div id=\"bloklinks_blok\" class=\"noprint\">";

	echo "<div id=\"bloklinks\">";
	if($last_accNOT and $id<>"saved" and $id<>"boeken") {
		echo "<div id=\"laatstbekeken_favorieten_blok\">";
		echo "<div class=\"laatstbekeken_favorieten\">";
		echo "<a href=\"".$vars["path"]."saved.php\">".html("laatstbekeken","index")."</a>";
		echo "</div>"; # afsluiten laatstbekeken_favorieten
		echo "</div>"; # afsluiten laatstbekeken_favorieten_blok
		$vars["zoekenboek_overlay_doorschuiven"]+=30;
	}
	echo "<div class=\"bloklinks_blauwelijn\"></div>\n";

	if(!$vars["verberg_zoekenboeklinks"]) {
		echo "<div id=\"zoekenboek_leeg\">&nbsp;</div>";
		echo "<div class=\"bloklinks_blauwelijn\"></div>\n";
	}

	if(!$vars["verberg_directnaar"]) {
		echo "<div id=\"directnaar\" class=\"noprint\">";
		echo "<div id=\"directnaar_kop\"><span class=\"bloklinks_kop\">".html("directnaar","index")."</span></div>";
		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("frankrijk","index"))."/\">".html("frankrijk","index")."</a><br />";
		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("oostenrijk","index"))."/\">".html("oostenrijk","index")."</a><br />";
		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("zwitserland","index"))."/\">".html("zwitserland","index")."</a><br />";
		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("italie","index"))."/\">".html("italie","index")."</a><br />";
		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("duitsland","index"))."/\">".html("duitsland","index")."</a><br />";
#		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".txt("menu_land")."/".wt_convert2url(txt("frankrijk","index"))."/\">".html("overigelanden","index")."</a><br />";
		echo "</div>\n";
		if($id=="index") {
			# Opsomming "Waarom Chalet.nl?"
			echo "<div id=\"hoofdpagina_waaromchalet\" onclick=\"document.location.href='".txt("menu_wie-zijn-wij").".php';\">";
			echo "<div class=\"kop\">".html("waarom","index",array("v_websitenaam"=>$vars["websitenaam"]))."</div>";
			echo "<div><ul>";
			for($i=1;$i<=6;$i++) {
				if(html("waarom".$i,"index")<>"-") {
					echo "<li>".html("waarom".$i,"index")."</li>";
				}
			}
			echo "</ul></div>";
			echo "</div>\n";
		}
		# Nieuwsbrief
		if($vars["website"]=="C") {
			echo "<div id=\"hoofdpagina_nieuwsbrief\" class=\"noprint\">";
			echo "<div class=\"kop\">Nieuwsbrief</div>";
			echo "<div>Ontvang aanbiedingen, nieuws en reistips.</div>";
			if(($vars["website"]=="C" or $vars["website"]=="Z") and $_SERVER["HTTPS"]<>"on" and !$vars["lokale_testserver"]) {
				$nieuwsbrief_url=preg_replace("/^http:/","https:",$vars["basehref"])."nieuwsbrief.php";
			} else {
				$nieuwsbrief_url=$vars["path.php"]."nieuwsbrief.php";
			}
			echo "<form method=\"post\" action=\"".htmlentities($nieuwsbrief_url)."\">";
			echo "<div style=\"margin-top:5px;\"><input type=\"text\" name=\"mail\" value=\"e-mailadres\" onfocus=\"if(this.value=='e-mailadres') this.value='';\" onblur=\"if(this.value=='') this.value='e-mailadres';\"></div>";
			echo "<div style=\"margin-top:5px;margin-bottom:5px;\"><input type=\"submit\" value=\" inschrijven \"></div>";
			echo "</form>";
			echo "</div>\n"; # afsluiten hoofdpagina_nieuwsbrief

			echo "<div style=\"clear:both;\"></div>";

			//if(($vars["website"]=="C" or $vars["website"]=="T") and $id<>"alpedhuzes") {
			//			# Alpe d'HuZes
			//			echo "<div id=\"alpedhuzes\">";
			//			echo "<a href=\"".$vars["path"]."alpedhuzes.php\"><img src=\"".$vars["path"]."pic/tijdelijk/alpedhuzes".($vars["website"]=="T" ? "_chalettour" : "").".gif\" width=\"168\" height=\"83\" border=\"0\"></a>";
			//			echo "</div>";
			//		}
		}
	}

	echo "</div>\n";

	echo "</div>\n";

	echo "<div id=\"contentrechts\">";

	if($id<>"index" and $id<>"toonaccommodatie" and !$laat_titel_weg) {
		if($header[$id]) {
			echo "<h1>".htmlentities($header[$id])."</h1>";
		} else {
			echo "<h1>".htmlentities($title[$id])."</h1>";
		}
	}

	# Content includen
	if($id=="index") {
		include($include);
	} else {
		echo "<div style=\"min-height:290px;\">";
		include($include);
		echo "</div>";

		if($last_acc_html and $id<>"saved" and (!$vars["verberg_directnaar"] or $id=="zoek-en-boek") and !$vars["verberg_lastacc"]) {
			echo $last_acc_html;
		}

		echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	}
	if(!$vars["wederverkoop"] and $id<>"index") {
#		echo "<div id=\"blauwelijn_onderaan\"></div>\n";
#		echo "<div id=\"contactgegevens\">".htmlentities($vars["websitenaam"])."&nbsp;&nbsp;&nbsp;".html("telefoonnummer")."&nbsp;&nbsp;&nbsp;<a href=\"mailto:".htmlentities($vars["email"])."\">".htmlentities($vars["email"])."</a></div>";
	}
}

echo "</div>\n";

echo "</div>\n";


echo "<div style=\"clear: both;\"></div>\n";


# telefoonblok
if(!$vars["verberg_linkerkolom"] and $vars["website"]<>"T" and (!$vars["verberg_linkerkolom"] or $id=="toonaccommodatie")) {
	echo "<div id=\"telefoonblok\" class=\"noprint".($id<>"contact" ? " telefoonblokhover" : "")."\"".($id<>"contact" ? " onclick=\"document.location.href='".$vars["path"].txt("menu_contact").".php';\"" : "").">";
	echo "<div id=\"telefoonblok_nummer\"><table cellspacing=\"0\" cellpadding=\"0\"><tr><td><img src=\"".$vars["path"]."pic/icon_telefoon_winter.gif\"></td><td>".html("telefoonnummer_telefoonblok")."</td></tr></table></div>";
	echo "<div id=\"telefoonblok_open\">".html("openingstijden_telefoonblok")."</div>";
	echo "</div>"; # afsluiten telefoonblok
}

# breadcrumbs
if($id<>"index" and !$vars["leverancier_mustlogin"] and !$vars["verberg_breadcrumbs"]) {
	echo "<div id=\"breadcrumb_wrapper\" class=\"noprint\">";
	echo "<div id=\"breadcrumb_overlay\" class=\"noprint\">";
	echo "<a href=\"".$vars["path"]."\">".htmlentities(ucfirst(txt("menutitle_index")))."</a>";
	if(!is_array($breadcrumbs)) {
		$breadcrumbs["last"]=$title[$id];
	}
	while(list($key,$value)=each($breadcrumbs)) {
		echo "&nbsp;&nbsp;&gt;&nbsp;&nbsp;";
		if($key<>"last") echo "<a href=\"".htmlentities($vars["path"].$key)."\">";
		echo htmlentities($value);
		if($key<>"last") echo "</a>";
	}
	echo "</div>"; # afsluiten breadcrumb_overlay
	echo "</div>"; # afsluiten breadcrumb_wrapper
}
echo "<div id=\"colofon_wrapper\" class=\"noprint\">";
echo "<div id=\"colofon\" class=\"noprint\">".htmlentities($vars["websiteinfo"]["langewebsitenaam"][$vars["website"]])." - <a href=\"mailto:".htmlentities($vars["websiteinfo"]["email"][$vars["website"]])."\">".htmlentities($vars["websiteinfo"]["email"][$vars["website"]])."</a> - ".html("telefoonnummer_colofon")."</div>";

if($vars["website"]=="C" or $vars["website"]=="B" or $vars["website"]=="T") {
	if($id!="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"disclaimerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>".wt_he($vars["websitenaam"])." &copy; 2012</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."algemenevoorwaarden.php\" rel=\"nofollow\">Algemene voorwaarden</a></li><li><a href=\"".$vars["path"]."disclaimer.php\" rel=\"nofollow\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\" rel=\"nofollow\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\" rel=\"nofollow\">Sitemap</a></li>";
		echo "</div>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Onze bestemmingen</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."land/Frankrijk/\" rel=\"nofollow\">Chalets in Frankrijk</a></li>";
		echo"<li><a href=\"".$vars["path"]."land/Oostenrijk/\" rel=\"nofollow\">Chalets in Oostenrijk</a></li>";
		echo "<li><a href=\"".$vars["path"]."land/Zwitserland/\" rel=\"nofollow\">Chalets in Zwitserland</a></li>";
		echo "<li><a href=\"".$vars["path"]."land/Italie/\" rel=\"nofollow\">Chalets in Itali&euml;</a></li>";
		echo "<li><a href=\"".$vars["path"]."land/Duitsland/\" rel=\"nofollow\">Chalets in Duitsland</a></li>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skigebieden</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Les_Trois_Vallees/\" rel=\"nofollow\">Wintersport in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Paradiski_-_Les_Arcs/\" rel=\"nofollow\">Wintersport in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Zillertal/\" rel=\"nofollow\">Wintersport in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Zell_am_See_Kaprun/\" rel=\"nofollow\">Wintersport in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Les_Deux_Alpes/\" rel=\"nofollow\">Wintersport in Les Deux Alpes</a></li>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skidorpen</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."plaats/Konigsleiten/\" rel=\"nofollow\">Wintersport in K&ouml;nigsleiten</li></a>";
		echo "<li><a href=\"".$vars["path"]."plaats/Zell_am_See/\" rel=\"nofollow\">Wintersport in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."plaats/Val_Thorens/\" rel=\"nofollow\">Wintersport in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."plaats/Chatel/\" rel=\"nofollow\">Wintersport in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."plaats/Les_Menuires/\" rel=\"nofollow\">Wintersport in Les Menuires</a></li>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	} elseif($id=="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>".wt_he($vars["websitenaam"])." &copy; 2012</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."algemenevoorwaarden.php\">Algemene voorwaarden</a></li><li><a href=\"".$vars["path"]."disclaimer.php\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\">Sitemap</a></li>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Onze bestemmingen</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."land/Frankrijk/\">Chalets in Frankrijk</a></li>";
		echo"<li><a href=\"".$vars["path"]."land/Oostenrijk/\">Chalets in Oostenrijk</a></li>";
		echo "<li><a href=\"".$vars["path"]."land/Zwitserland/\">Chalets in Zwitserland</a></li>";
		echo "<li><a href=\"".$vars["path"]."land/Italie/\">Chalets in Itali&euml;</a></li>";
		echo "<li><a href=\"".$vars["path"]."land/Duitsland/\">Chalets in Duitsland</a></li>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skigebieden</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Les_Trois_Vallees/\">Wintersport in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Paradiski_-_Les_Arcs/\">Wintersport in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Zillertal/\">Wintersport in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Zell_am_See_Kaprun/\">Wintersport in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."skigebied/Les_Deux_Alpes/\">Wintersport in Les Deux Alpes</a></li>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Populaire skidorpen</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."plaats/Konigsleiten/\">Wintersport in K&ouml;nigsleiten</a></li>";
		echo "<li><a href=\"".$vars["path"]."plaats/Zell_am_See/\">Wintersport in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."plaats/Val_Thorens/\">Wintersport in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."plaats/Chatel/\">Wintersport in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."plaats/Les_Menuires/\">Wintersport in Les Menuires</a></li>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}

} elseif($vars["website"]=="E") {
	if($id!="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"disclaimerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>".wt_he($vars["websitenaam"])." &copy; 2012</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."conditions.php\" rel=\"nofollow\">Conditions</a></li><li><a href=\"".$vars["path"]."disclaimer.php\" rel=\"nofollow\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\" rel=\"nofollow\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\" rel=\"nofollow\">Sitemap</a></li>";
		echo "</div>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Our destinations</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."country/France/\" rel=\"nofollow\">Chalets in France</a></li>";
		echo"<li><a href=\"".$vars["path"]."country/Austria/\" rel=\"nofollow\">Chalets in Austria</a></li>";
		echo "<li><a href=\"".$vars["path"]."country/Switzerland/\" rel=\"nofollow\">Chalets in Switzerland</a></li>";
		echo "<li><a href=\"".$vars["path"]."country/Italy/\" rel=\"nofollow\">Chalets in Italy</a></li>";
		echo "<li><a href=\"".$vars["path"]."country/Germany/\" rel=\"nofollow\">Chalets in Germany</a></li>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski regions</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."region/Les_Trois_Vallees/\" rel=\"nofollow\">Chalets in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Paradiski_-_Les_Arcs/\" rel=\"nofollow\">Chalets in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Zillertal/\" rel=\"nofollow\">Chalets in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Zell_am_See_Kaprun/\" rel=\"nofollow\">Chalets in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Les_Deux_Alpes/\" rel=\"nofollow\">Chalets in Les Deux Alpes</a></li>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski villages</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."resort/Konigsleiten/\" rel=\"nofollow\">Chalets in K&ouml;nigsleiten</li></a>";
		echo "<li><a href=\"".$vars["path"]."resort/Zell_am_See/\" rel=\"nofollow\">Chalets in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."resort/Val_Thorens/\" rel=\"nofollow\">Chalets in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."resort/Chatel/\" rel=\"nofollow\">Chalets in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."resort/Les_Menuires/\" rel=\"nofollow\">Chalets in Les Menuires</a></li>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	} elseif($id=="index") {
		echo"<div id=\"footerWrap\">";
		echo "<div class=\"divSepIND\">";
		echo "<br><b>".wt_he($vars["websitenaam"])." &copy; 2012</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."conditions.php\">Conditions</a></li><li><a href=\"".$vars["path"]."disclaimer.php\">Disclaimer</a></li><li><a href=\"".$vars["path"]."privacy-statement.php\">Privacy statement</a></li><li><a href=\"".$vars["path"]."sitemap\">Sitemap</a></li>";
		echo "</div>";
		echo "<div class=\"wrap\">";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Our destinations</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."country/France/\">Chalets in France</a></li>";
		echo"<li><a href=\"".$vars["path"]."country/Austria/\">Chalets in Austria</a></li>";
		echo "<li><a href=\"".$vars["path"]."country/Switzerland/\">Chalets in Switzerland</a></li>";
		echo "<li><a href=\"".$vars["path"]."country/Italy/\">Chalets in Italy</a></li>";
		echo "<li><a href=\"".$vars["path"]."country/Germany/\">Chalets in Germany</a></li>";
		echo "</div>";
		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski regions</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."region/Les_Trois_Vallees/\">Chalets in Les Trois Vall&eacute;es</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Paradiski_-_Les_Arcs/\">Chalets in Les Arcs / Paradiski</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Zillertal/\">Chalets in Zillertal</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Zell_am_See_Kaprun/\">Chalets in Zell am See / Kaprun</a></li>";
		echo "<li><a href=\"".$vars["path"]."region/Les_Deux_Alpes/\">Chalets in Les Deux Alpes</a></li>";
		echo "</div>";

		echo "<div class=\"divContentIND\">";
		echo "<br><b>Popular ski villages</b><br><br>";
		echo "<li><a href=\"".$vars["path"]."resort/Konigsleiten/\">Chalets in K&ouml;nigsleiten</a></li>";
		echo "<li><a href=\"".$vars["path"]."resort/Zell_am_See/\">Chalets in Zell am See</a></li>";
		echo "<li><a href=\"".$vars["path"]."resort/Val_Thorens/\">Chalets in Val Thorens</a></li>";
		echo "<li><a href=\"".$vars["path"]."resort/Chatel/\">Chalets in Ch&acirc;tel</a></li>";
		echo "<li><a href=\"".$vars["path"]."resort/Les_Menuires/\">Chalets in Les Menuires</a></li>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}
} else {
	echo "<div id=\"ondercolofon\" class=\"noprint\"><a href=\"".$vars["path"]."disclaimer.php\">Disclaimer</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href=\"".$vars["path"]."privacy-statement.php\">Privacy statement</a></div>";
}
echo "</div>";

if(!$vars["verberg_linkerkolom"] and !$vars["verberg_zoekenboeklinks"]) {
	echo "<div id=\"zoekenboek_overlay\" class=\"noprint\">";
	echo "<div class=\"bloklinks_kop\" style=\"margin-bottom:5px;\">".html("zoekenboek","index")."</div>";

	echo "<form method=\"get\" action=\"".$vars["path"].txt("menu_zoek-en-boek").".php\" name=\"zoeken\">";
	echo "<input type=\"hidden\" name=\"filled\" value=\"1\">";

#	echo "<div class=\"zoekenboek_tekst\" style=\"margin-bottom:3px;\">".html("skigebied","index")."</div>";

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<input type=\"text\" name=\"fzt\" class=\"tekstzoeken\" value=\"-- ".html("trefwoord","index")." --\" onfocus=\"if(this.value=='-- ".html("trefwoord","index")." --') this.value='';\" onblur=\"if(this.value=='') this.value='-- ".html("trefwoord","index")." --';\">";
	echo "</div>";

	if($vars["seizoentype"]==1) {
		# landen in de winter niet op alfabet sorteren
		$landen_sort[1]="01";
		$landen_sort[2]="02";
		$landen_sort[3]="03";
		$landen_sort[5]="04";
		$landen_sort[4]="05";
		$landen_sort[7]="06";
		$landen_sort[6]="07";
	}

	# Skigebied-array vullen
	$vars["skigebied"]["00AAAAA___-- ".txt("skigebied","index")." --"]=0;
	$vars["skigebied"]["00AAAAB___".txt("geenvoorkeur","index")]=0;

	$db->query("SELECT DISTINCT s.skigebied_id, s.naam, s.kortenaam1, s.kortenaam2, s.kortenaam3, s.kortenaam4, l.naam".$vars["ttv"]." AS land, l.naam AS landnl, l.land_id, s.koppeling_1_1, s.koppeling_1_2, s.koppeling_1_3, s.koppeling_1_4, s.koppeling_1_5, s.koppeling_2_1, s.koppeling_2_2, s.koppeling_2_3, s.koppeling_2_4, s.koppeling_2_5, s.koppeling_3_1, s.koppeling_3_2, s.koppeling_3_3, s.koppeling_3_4, s.koppeling_3_5, s.koppeling_4_1, s.koppeling_4_2, s.koppeling_4_3, s.koppeling_4_4, s.koppeling_4_5, s.koppeling_5_1, s.koppeling_5_2, s.koppeling_5_3, s.koppeling_5_4, s.koppeling_5_5 FROM skigebied s, plaats p, land l, type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.websites LIKE '%".$vars["website"]."%' AND a.plaats_id=p.plaats_id AND l.land_id=p.land_id AND s.skigebied_id=p.skigebied_id ORDER BY l.naam".$vars["ttv"].", s.naam;");
	while($db->next_record()) {
		$landen[$db->f("land")]=true;

		if($landen_sort[$db->f("land_id")]) {
			$sorteer=$landen_sort[$db->f("land_id")];
		} else {
			$sorteer=$db->f("land");
		}

		if(!$landgehad[$db->f("land")]) {
			$vars["skigebied"][$sorteer."AAAAA___".txt("heelskigebieden","accommodaties")." ".$db->f("land")]=$db->f("land_id")."-0";

			$landnaam[$db->f("land_id")]=$db->f("land");
			$landgehad[$db->f("land")]=true;
		}
		if($db->f("kortenaam1")) {
			$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam1")]=$db->f("land_id")."-".$db->f("skigebied_id")."-1";
			if($db->f("kortenaam2")) {
				$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam2")]=$db->f("land_id")."-".$db->f("skigebied_id")."-2";
			}
			if($db->f("kortenaam3")) {
				$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam3")]=$db->f("land_id")."-".$db->f("skigebied_id")."-3";
			}
			if($db->f("kortenaam4")) {
				$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("kortenaam4")]=$db->f("land_id")."-".$db->f("skigebied_id")."-4";
			}
		} else {
			$vars["skigebied"][$sorteer."ZZZZZ___".$db->f("naam")]=$db->f("land_id")."-".$db->f("skigebied_id");
		}
	}
	setlocale(LC_COLLATE,"nl_NL.ISO8859-1");
	ksort($vars["skigebied"],SORT_LOCALE_STRING);
	setlocale(LC_COLLATE,"C");

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fsg\" class=\"selectbox\">";
	while(list($key,$value)=each($vars["skigebied"])) {
		if(ereg("^([0-9]+)-0$",$value,$regs)) {
			if($optgroup_open) echo "</optgroup>\n";
			echo "<optgroup label=\"".htmlentities($landnaam[$regs[1]])."\">\n";
			$optgroup_open=true;
		}
		if(preg_match("/^([0-9]+)-([0-9]+)/",$value,$regs)) {
			$landdid_currect=$regs[1];
			$skigebiedid_currect=$regs[2];
		}
		echo "<option value=\"".$value."\"";
		if(($_GET["filled"] and $_GET["fsg"]==$value) or ($skigebiedid and $skigebiedid==$skigebiedid_currect) or ($landinfo["id"] and $landinfo["id"]==$landdid_currect and preg_match("/AAAAA/",$key))) {
			if(!$skigebied_selected) {
				$skigebied_selected=true;
				echo " selected";
				if($value) $save_query["regio"]=$vars["skigebied_nl"][$value];
			}
		}
		echo ">";
		echo htmlentities(ereg_replace("^.*___","",$key))."</option>";
	}
	if($optgroup_open) echo "</optgroup>\n";

	echo "</select>";
	echo "</div>";

	# aantalpersonen-array vullen
	$vars["aantalpersonen"]["-"]="-- ".txt("aantalpersonen","index")." --";
	$vars["aantalpersonen"][0]=txt("geenvoorkeur","index");
	for($i=1;$i<=40;$i++) {
		$vars["aantalpersonen"][$i]=$i;
	}

#	echo "<div class=\"zoekenboek_tekst\" style=\"margin-top:10px;margin-bottom:3px;\">".html("aantalpersonen","index")."</div>";
	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fap\" class=\"selectbox\">";
	while(list($key,$value)=each($vars["aantalpersonen"])) {
		echo "<option value=\"".($key=="-" ? "0" : $key)."\"";
		if($key==="-") echo " selected";
		echo ">".htmlentities($value)."</option>";
	}
	echo "</select>";
	echo "</div>";
#	echo "<div class=\"zoekenboek_tekst\" style=\"margin-top:10px;margin-bottom:3px;\">".html("aankomstdatum","index")."</div>";

	# Aankomstdatum vullen
	$vars["aankomstdatum_weekend_afkorting"]["-"]="-- ".txt("aankomstdatum","index")." --";
	$vars["aankomstdatum_weekend_afkorting"][0]=txt("geenvoorkeur","index");
	ksort($vars["aankomstdatum_weekend_afkorting"],SORT_STRING);

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fad\" class=\"selectbox\">";
	while(list($key,$value)=each($vars["aankomstdatum_weekend_afkorting"])) {
		# Weken die al voorbij zijn niet tonen (2 dagen na aankomstdatum niet meer tonen)
		if(mktime(0,0,0,date("m"),date("d")-2,date("Y"))<$key or !$key or $key==="-") {
			echo "<option value=\"".($key=="-" ? "0" : $key)."\"";
			if($key==="-") echo " selected";
			echo ">".$value."</option>\n";
		}
	}
	echo "</select>";
	echo "</div>";

#	echo "<div style=\"margin-top:0px;\">&nbsp;</div>";

	echo "<input type=\"submit\" value=\" ".html("zoeken","index")."\">";

	echo "<div style=\"margin-top:4px;margin-bottom:4px;\"><a href=\"#\" id=\"uitgebreidzoeken\">".html("uitgebreidzoeken","index")."</a></div>";
	echo "</form>";
	echo "</div>";
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

# Balk met cookie-melding
if($vars["cookiemelding_tonen"] and $vars["websiteland"]=="nl" and (!$_COOKIE["cookiemelding_gelezen"] or $vars["lokale_testserver"])) {
	echo "<div id=\"cookie_bottombar\" class=\"noprint\"><div id=\"cookie_bottombar_wrapper\"><div id=\"cookie_bottombar_text\">".html("cookiemelding","vars",array("h_1"=>"<a href=\"".$vars["path"]."privacy-statement.php\">","h_2"=>"</a>"))."</div><div id=\"cookie_bottombar_close\">sluiten</div></div></div>";
}

# Zorgen dat zoekenboek_overlay naar beneden schuift i.v.m. "laatst bekeken"-button
if($vars["zoekenboek_overlay_doorschuiven"]) {
	echo "<style type=\"text/css\"><!--\n#zoekenboek_overlay {\ntop:".(264+$vars["zoekenboek_overlay_doorschuiven"])."px;\n}\n--></style>\n";
}
echo "</body>";
echo "</html>";

?>