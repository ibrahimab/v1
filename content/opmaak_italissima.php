<?php

# Te includen bestand bepalen
if($language_content) {
	if(file_exists("content/_meertalig/".$id."_italissima_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_italissima_".$vars["taal"].".html";
	} elseif(file_exists("content/_meertalig/".$id."_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_".$vars["taal"].".html";
	}
} else {
	if(file_exists("content/".$id."_italissima.html") and $id<>"aanbiedingen") {
		$include="content/".$id."_italissima.html";
	} elseif(file_exists("content/".$id."_nieuw.html")) {
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
echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9\" />\n";
echo "<title>";
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
echo "</title>";

if($vars["page_with_tabs"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/tabs.css.phpcache?cache=".@filemtime("css/tabs.css.phpcache")."?type=".$vars["websitetype"]."\" />\n";
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_italissima.css?cache=".@filemtime("css/opmaak_italissima.css")."\" />\n";
if(file_exists("css/".$id."_italissima.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_italissima.css?cache=".@filemtime("css/".$id."_italissima.css")."\" />\n";
} elseif(file_exists("css/".$id.".css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id.".css?cache=".@filemtime("css/".$id.".css")."\" />\n";
}
if(file_exists("css/".$id."_italissima_extra.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_italissima_extra.css?cache=".@filemtime("css/".$id."_italissima_extra.css")."\" />\n";
}
if($voorkant_cms and !$_GET["cmsuit"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/voorkantcms_italissima.css?cache=".@filemtime("css/voorkantcms_italissima.css")."\" />\n";
}

if(preg_match("/MSIE 6/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie6_italissima.css?cache=".@filemtime("css/ie6_italissima.css")."\" />\n";
}
if(preg_match("/MSIE 7/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7.css?cache=".@filemtime("css/ie7.css")."\" />\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7_italissima.css?cache=".@filemtime("css/ie7_italissima.css")."\" />\n";
}

echo "<link rel=\"shortcut icon\" href=\"".$vars["path"]."favicon_italissima.ico\" />\n";

if($vars["canonical"]) {
	echo "<link rel=\"canonical\" href=\"".htmlentities($vars["canonical"])."\" />\n";
} elseif($_SERVER["HTTPS"]=="on") {
	echo "<link rel=\"canonical\" href=\"http://".htmlentities($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."\" />\n";
}

# JQuery
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jquery_url"])."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jqueryui_url"])."\" ></script>\n";

#echo "<script type=\"text/javascript\" src=\"http://labs.juliendecaudin.com/barousel/js/jquery.thslide.js\"></script>\n";



if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	# jQuery inlog: http://prinzhorn.github.com/jquery-inlog/
	# $.inlog(true);
	# $.("").....;
	# $.inlog(false);
#	echo "<script type=\"text/javascript\" src=\"".htmlentities("http://ss.postvak.net/_intern/extra/jquery.inlog.js")."\" ></script>\n";
}

if($vars["jquery_maphilight"]) {
	# Google Maps API
	echo "<script src=\"".$vars["path"]."scripts/jquery.maphilight.min.js\" type=\"text/javascript\"></script>\n";
}

if($vars["googlemaps"]) {
	# Google Maps API
	echo "<script src=\"https://maps-api-ssl.google.com/maps/api/js?v=3&amp;sensor=false\" type=\"text/javascript\"></script>\n";
}

# Chosen
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/jquery.chosen.min.js\"></script>\n";

echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_italissima.js?cache=".@filemtime("scripts/functions_italissima.js")."\" ></script>\n";
if(file_exists("scripts/functions_".$id.".js")) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_".$id.".js?cache=".@filemtime("scripts/functions_".$id.".js")."\" ></script>\n";
}

# Fancybox
if($vars["jquery_fancybox"]) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.1.pack.js\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.1.css?c=1\" type=\"text/css\" media=\"screen\" />\n";
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
echo "<link href=\"https://plus.google.com/118061823772005667855\" rel=\"publisher\" />\n";

# Google Analytics
echo googleanalytics();

echo "</head>\n";
echo "<body";
if($id<>"index") echo " onscroll=\"document.getElementById('terugnaarboven').style.visibility='visible'\"";
if($onload) echo " onload=\"".$onload."\"";
echo ">";
echo "<div id=\"wrapper\">";

echo "<div id=\"top\">";

echo "<div id=\"submenu\">";
echo "<table cellspacing=0 cellpadding=0 id=\"submenu_table\"><tr><td>";
while(list($key,$value)=each($submenu)) {
	$submenuteller++;
	if($value<>"-") {
		echo "<a href=\"".$vars["path"].txt("menu_".$key).(@in_array($key,$submenu_url_zonder_punt_php) ? "" : ".php")."\">";
		echo htmlentities($value);
		echo "</a>";
		if($submenuteller<(count($submenu))) {
			echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
		}
	}
}
echo "</td><td>";
echo "</td></tr></table>";
echo "</div>\n"; # afsluiten submenu

echo "<div id=\"logo\">";
if($id<>"index") echo "<a href=\"".$vars["path"]."\">";
echo "<img src=\"".$vars["path"]."pic/logo_italissima.gif\" width=\"200\" height=\"160\" border=\"0\" alt=\"".htmlentities($vars["websitenaam"])."\">";
if($id<>"index") echo "</a>";
echo "</div>\n"; # afsluiten logo

echo "<div id=\"topfoto\" class=\"noprint\">";
echo "<img src=\"".$vars["path"];

if($vars["italissima_topfoto"]) {
	echo $vars["italissima_topfoto"];
} else {
	echo "pic/topfoto_italissima_1.jpg";
}
echo "\" width=\"760\" height=\"160\">";
echo "</div>\n"; # afsluiten topfoto

echo "<div style=\"clear: both;\"></div>\n";

# alleen voor print
echo "<div id=\"menubalk_print\" class=\"onlyprint\">";
echo "<h2>".htmlentities($vars["websitenaam"])."</h2>";
echo "<b>".htmlentities(ereg_replace("http://([a-z0-9\.]*)/.*","\\1",$vars["basehref"]))."<p>".html("telefoonnummer")."</p></b>";
echo "</div>"; # afsluiten menubalk_print

echo "<div id=\"menubalk\" class=\"noprint\">";

echo "<div id=\"hoofdmenu\" class=\"noprint\">";
while(list($key,$value)=each($menu)) {
	if($menuteller) {
		echo "<span class=\"hoofdmenuspacer\">|</span>";
	}
	$menuteller++;
	echo "<a href=\"".$vars["path"];
	if($key=="aanbiedingen") {
		echo txt("menu_".$key)."/";
	} elseif($key<>"index") {
		echo txt("menu_".$key).".php";
	}
	echo "\">&nbsp;&nbsp;";
	if($key==$id or ($id=="accommodaties" and $key=="zoek-en-boek") or ($id=="aanbiedingen_zomerhuisje" and $key=="aanbiedingen")) {
		echo "<span class=\"hoofdmenu_actief\">";
	}
	echo htmlentities($value);
	if($key==$id or ($id=="accommodaties" and $key=="zoek-en-boek") or ($id=="aanbiedingen_zomerhuisje" and $key=="aanbiedingen")) {
		echo "</span>";
	}
	echo "&nbsp;&nbsp;</a>";
}
echo "</div>\n"; # afsluiten hoofdmenu

echo "</div>"; # afsluiten menubalk


echo "<div style=\"clear: both;\"></div>\n";

echo "</div>\n"; # afsluiten top


# Balk boven content
echo "<div id=\"balkbovencontent\" class=\"noprint\">";
echo "<div id=\"sgr_logo\"><a href=\"".$vars["path"]."algemenevoorwaarden.php#sgr\"><img src=\"".$vars["path"]."pic/sgr_klein.gif\" width=\"25\" height=\"23\" border=\"0\" alt=\"Stichting Garantiefonds Reisgelden\"></a></div>";

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
	
	echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	echo "</div>\n";
} else {
	echo "<div id=\"bloklinks_blok\" class=\"noprint\">";

	echo "<div id=\"bloklinks\" class=\"noprint\">";

	if(!$vars["verberg_zoekenboeklinks"]) {
		echo "<div id=\"zoekenboek_leeg\">&nbsp;</div>";
#		echo "<div class=\"bloklinks_blauwelijn\"></div>\n";
	}

	if(!$vars["verberg_directnaar"]) {
		echo "<div id=\"directnaar\" class=\"noprint\">";
#		echo "<div id=\"directnaar_kop\"><span class=\"bloklinks_kop\">".html("directnaar","index")."</span></div>";
		
#		echo "Accommodatietip";
#		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("frankrijk","index"))."/\">".html("frankrijk","index")."</a><br />";
#		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("oostenrijk","index"))."/\">".html("oostenrijk","index")."</a><br />";
#		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("zwitserland","index"))."/\">".html("zwitserland","index")."</a><br />";
#		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".$vars["path"].txt("menu_land")."/".wt_convert2url(txt("italie","index"))."/\">".html("italie","index")."</a><br />";
#		echo "<span class=\"redtext\">&bull;</span>&nbsp;<a href=\"".txt("menu_land")."/".wt_convert2url(txt("frankrijk","index"))."/\">".html("overigelanden","index")."</a><br />";
		echo "</div>\n"; # afsluiten directnaar
	
		#
		# Blok links met accommodatie
		#
		unset($blokaccommodatie,$where);
		# where bepalen
		if($id=="index") {
			$where="b.hoofdpagina=1";
		} elseif($id=="bestemmingen") {
			$where="b.bestemmingen=1";
		}
		if($where) {
			$checkdate=mktime(0,0,0,date("m"),date("d"),date("Y"));
			$db->query("SELECT b.regel1, b.regel2, b.begindatum, b.einddatum, t.type_id, a.accommodatie_id, l.begincode FROM blokaccommodatie b, accommodatie a, type t, plaats p, land l WHERE b.websitetype=7 AND b.type_id=t.type_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND b.tonen=1 AND a.tonen=1 AND t.tonen=1 AND ".$where." ORDER BY b.begindatum, b.blokaccommodatie_id;");
			while($db->next_record()) {
				$binnendatum=true;
				if($db->f("begindatum") and $db->f("begindatum")>$checkdate) {
					$binnendatum=false;
				}
				
				if($db->f("einddatum") and $db->f("einddatum")<$checkdate) {
					$binnendatum=false;
				}
				if($binnendatum) {
					unset($blokaccommodatie_afbeeldingaanwezig);
					if(file_exists("pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
						$blokaccommodatie_teller++;
						$blokaccommodatie[$blokaccommodatie_teller]["afbeelding"]="pic/cms/types_specifiek/".$db->f("type_id").".jpg";
						$blokaccommodatie_afbeeldingaanwezig=true;
					} elseif(file_exists("pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
						$blokaccommodatie_teller++;
						$blokaccommodatie[$blokaccommodatie_teller]["afbeelding"]="pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
						$blokaccommodatie_afbeeldingaanwezig=true;
					} elseif($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
						$blokaccommodatie_teller++;
						$blokaccommodatie[$blokaccommodatie_teller]["afbeelding"]="pic/cms/accommodaties/1097.jpg";
						$blokaccommodatie_afbeeldingaanwezig=true;
					}
					if($blokaccommodatie_afbeeldingaanwezig) {
						$blokaccommodatie[$blokaccommodatie_teller]["regel1"]=$db->f("regel1");
						$blokaccommodatie[$blokaccommodatie_teller]["regel2"]=$db->f("regel2");
						$blokaccommodatie[$blokaccommodatie_teller]["url"]=$vars["path"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/";
					}
				}
			}
		}
		
		if($html_ipv_blokaccommodatie) {
			echo "<div id=\"blokaccommodatie\"".($html_ipv_blokaccommodatie_bgcolor ? " style=\"background-color:".$html_ipv_blokaccommodatie_bgcolor."\"" : "").">";
			echo $html_ipv_blokaccommodatie;
			echo "</div>";
		} elseif($blokaccommodatie) {
			$bgcolor="#ff9900";
			$random_blokaccommodatie=rand(1,$blokaccommodatie_teller);
			echo "<div id=\"blokaccommodatie\" style=\"background-color:".$bgcolor."\">";
			echo "<div id=\"blokaccommodatie_innerdiv\" style=\"position:relative;".(ereg("MSIE 6",$_SERVER["HTTP_USER_AGENT"]) ? "height:130px;" : "")."\" class=\"overlay_foto\" onclick=\"document.location.href='".htmlentities($blokaccommodatie[$random_blokaccommodatie]["url"])."'\">";
			echo "<img src=\"".$vars["path"].htmlentities($blokaccommodatie[$random_blokaccommodatie]["afbeelding"])."\" width=\"180\" border=\"0\">";
			echo "<div id=\"blokaccommodatie_overlay\">";
			echo "<div>".htmlentities(trim($blokaccommodatie[$random_blokaccommodatie]["regel1"]))."</div>";
			if($blokaccommodatie[$random_blokaccommodatie]["regel2"]) {
				echo "<div style=\"padding-top: 3px;\">".htmlentities(trim($blokaccommodatie[$random_blokaccommodatie]["regel2"]))."</div>";
			}
			echo "</div>"; # afsluiten blokaccommodatie_overlay
			echo "</div>"; # afsluiten blokaccommodatie_innerdiv
			
			echo "</div>";
		} else {
	#		echo "<div id=\"blokaccommodatie\" >&nbsp;</div>";
		}
	}
	
	if($id=="index") {
		# Opsomming "Waarom Italissima?"
		echo "<div id=\"hoofdpagina_waarom\" onclick=\"document.location.href='".txt("menu_wie-zijn-wij").".php';\">";
		echo "<div class=\"kop\">".html("waarom","index",array("v_websitenaam"=>$vars["websitenaam"]))."</div>";
		echo "<div><ul>";
		echo "<li>Uniek aanbod</li>";
		echo "<li>Persoonlijke service</li>";
		echo "<li>Kwaliteit</li>";
		echo "<li>Prijsbewust</li>";
		echo "<li>12 jaar ervaring</li>";
		echo "<li>Lid SGR-Garantiefonds</li>";
		echo "</ul></div>"; # afsluiten hoofdpagina_waarom
		echo "</div>\n"; # afsluiten naamloze div
	}

	echo "</div>\n"; # afsluiten bloklinks

	echo "</div>\n"; # afsluiten bloklinks_blok
	
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
		if($id=="zoek-en-boek") {
			# zorgen voor hoge content i.v.m. "verfijn"-blok
			echo "<div style=\"min-height:790px;\">";
		} else {
			echo "<div style=\"min-height:290px;\">";
		}
		include($include);
		echo "</div>"; # afsluiten naamloze div

		if($last_acc_html and $id<>"saved" and (!$vars["verberg_directnaar"] or $id=="zoek-en-boek") and !$vars["verberg_lastacc"]) {
			echo $last_acc_html;
		}
		echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	}
	echo "</div>\n"; # afsluiten contentrechts
}

#echo "</div>\n";

echo "</div>\n"; # afsluiten content


echo "<div style=\"clear: both;\"></div>\n";


# telefoonblok
if(!$vars["verberg_linkerkolom"] and (!$vars["verberg_linkerkolom"] or $id=="toonaccommodatie")) {
	echo "<div id=\"telefoonblok\" class=\"noprint".($id<>"contact" ? " telefoonblokhover" : "")."\"".($id<>"contact" ? " onclick=\"document.location.href='".$vars["path"].txt("menu_contact").".php';\"" : "").">";
	echo "<div id=\"telefoonblok_nummer\"><table cellspacing=\"0\" cellpadding=\"0\"><tr><td><img src=\"".$vars["path"]."pic/icon_telefoon_italissima.gif\"></td><td>".html("telefoonnummer_telefoonblok")."</td></tr></table></div>";
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
echo "<div id=\"colofon\" class=\"noprint\">Italissima is een handelsnaam van Chalet.nl B.V. - <a href=\"mailto:".htmlentities($vars["websiteinfo"]["email"][$vars["website"]])."\">".htmlentities($vars["websiteinfo"]["email"][$vars["website"]])."</a> - ".html("telefoonnummer_colofon")."</div>";
if($id=="index") {
	echo "<div id=\"ondercolofon\" class=\"noprint\"><a href=\"".$vars["path"]."agriturismo-italie\">agriturismi in Italië</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href=\"http://www.chalet.nl/land/Italie/\" target=\"_blank\">wintersport in Italië</a></div>";
}
echo "</div>"; # afsluiten colofon_wrapper

if(!$vars["verberg_linkerkolom"] and !$vars["verberg_zoekenboeklinks"]) {

	echo "<div id=\"zoekenboek_overlay\" class=\"noprint\">";
	
	if($id=="aanbiedingen_zomerhuisje") {
		echo "<div id=\"zoekenboek_aanbiedingen\">";
		echo "<div id=\"zoekenboek_aanbiedingen_kop\">";
		echo html("meeraanbiedingen","index");
		echo "</div>";
		echo "<div style=\"padding:4px;\">";
	} else {
		echo "<div class=\"zoekenboek_kop\"><span class=\"bloklinksrechts_kop\">";
		echo html("zoekenboek","index");
		echo "</span></div>";
	}

	echo "<form method=\"get\" action=\"".$vars["path"].txt("menu_zoek-en-boek").".php\" name=\"zoeken\">";
	echo "<input type=\"hidden\" name=\"filled\" value=\"1\">";
	if($id=="aanbiedingen_zomerhuisje") {
		echo "<input type=\"hidden\" name=\"aab\" value=\"1\">";
		echo "<input type=\"hidden\" name=\"faab\" value=\"1\">";
	}
	if(is_array($vars["zoekenboeklinks_hidden_form_fields"])) {
		while(list($key,$value)=each($vars["zoekenboeklinks_hidden_form_fields"])) {
			echo "<input type=\"hidden\" name=\"".wt_he($key)."\" value=\"".wt_he($value)."\">";
		}
	}

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<input type=\"text\" name=\"fzt\" class=\"tekstzoeken\" value=\"-- ".html("trefwoord","index")." --\" onfocus=\"if(this.value=='-- ".html("trefwoord","index")." --') this.value='';\" onblur=\"if(this.value=='') this.value='-- ".html("trefwoord","index")." --';\">";
	echo "</div>";

	# Skigebied-array vullen
	$vars["skigebied"]["AAAAA___-- ".txt("bestemming","index")." --"]=0;
	$vars["skigebied"]["AAAAB___".txt("geenvoorkeur","index")]=0;

	$db->query("SELECT DISTINCT s.skigebied_id, s.naam, s.kortenaam1, s.kortenaam2, s.kortenaam3, s.kortenaam4, l.naam".$vars["ttv"]." AS land, l.naam AS landnl, l.land_id, s.koppeling_1_1, s.koppeling_1_2, s.koppeling_1_3, s.koppeling_1_4, s.koppeling_1_5, s.koppeling_2_1, s.koppeling_2_2, s.koppeling_2_3, s.koppeling_2_4, s.koppeling_2_5, s.koppeling_3_1, s.koppeling_3_2, s.koppeling_3_3, s.koppeling_3_4, s.koppeling_3_5, s.koppeling_4_1, s.koppeling_4_2, s.koppeling_4_3, s.koppeling_4_4, s.koppeling_4_5, s.koppeling_5_1, s.koppeling_5_2, s.koppeling_5_3, s.koppeling_5_4, s.koppeling_5_5 FROM skigebied s, plaats p, land l, type t, accommodatie a WHERE t.accommodatie_id=a.accommodatie_id AND t.tonen=1 AND t.tonenzoekformulier=1 AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.websites LIKE '%".$vars["website"]."%' AND a.plaats_id=p.plaats_id AND l.land_id=p.land_id AND s.skigebied_id=p.skigebied_id ORDER BY l.naam".$vars["ttv"].", s.naam;");
	while($db->next_record()) {
		$landen[$db->f("land")]=true;
		if(!$landgehad[$db->f("land")]) {
#			$vars["skigebied"][$db->f("land_id")."-0"]="".txt("alleskigebiedenin","accommodaties")." ".$db->f("land")."";
			$vars["skigebied"][$db->f("land")."AAAAA___".txt("heelskigebieden","accommodaties")." ".$db->f("land")]=$db->f("land_id")."-0";
			
			$landnaam[$db->f("land_id")]=$db->f("land");
			$landgehad[$db->f("land")]=true;
		}
		if($db->f("kortenaam1")) {
			$vars["skigebied"][$db->f("land")."ZZZZZ___".$db->f("kortenaam1")]=$db->f("land_id")."-".$db->f("skigebied_id")."-1";
			if($db->f("kortenaam2")) {
				$vars["skigebied"][$db->f("land")."ZZZZZ___".$db->f("kortenaam2")]=$db->f("land_id")."-".$db->f("skigebied_id")."-2";
			}
			if($db->f("kortenaam3")) {
				$vars["skigebied"][$db->f("land")."ZZZZZ___".$db->f("kortenaam3")]=$db->f("land_id")."-".$db->f("skigebied_id")."-3";
			}
			if($db->f("kortenaam4")) {
				$vars["skigebied"][$db->f("land")."ZZZZZ___".$db->f("kortenaam4")]=$db->f("land_id")."-".$db->f("skigebied_id")."-4";
			}
		} else {
			$vars["skigebied"][$db->f("land")."ZZZZZ___".$db->f("naam")]=$db->f("land_id")."-".$db->f("skigebied_id");
		}
	}

	ksort($vars["skigebied"]);
#	echo "<div class=\"zoekenboek_tekst\" style=\"margin-bottom:3px;\">".html("skigebied","index")."</div>";
	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fsg\" class=\"selectbox\">";
	while(list($key,$value)=each($vars["skigebied"])) {
#		if(ereg("^([0-9]+)-0$",$value,$regs)) {
#			if($optgroup_open) echo "</OPTGROUP>\n";
#			echo "<OPTGROUP LABEL=\"".htmlentities($landnaam[$regs[1]])."\">\n";
#			$optgroup_open=true;
#		}
		if(preg_match("/^[0-9]+-([0-9]+)/",$value,$regs)) {
			$skigebiedid_currect=$regs[1];
		}
		echo "<option value=\"".$value."\"";
		if($vars["zoekenboeklinks_prefilled_form_fields"]["fsg"] and $vars["zoekenboeklinks_prefilled_form_fields"]["fsg"]==$value) {
			echo " selected";
		} elseif($skigebiedid and $skigebiedid==$skigebiedid_currect) {
			echo " selected";
		}
		echo ">";
		echo htmlentities(ereg_replace("^.*___","",$key))."</OPTION>";
	}
#	if($optgroup_open) echo "</OPTGROUP>\n";

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
	ksort($vars["aankomstdatum_weekend_afkorting"]);

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

	# Verblijfsduur
	$vars["verblijfsduur"]["-"]="-- ".txt("verblijfsduur","index")." --";
	$vars["verblijfsduur"]["0"]=txt("geenvoorkeur","vars");
	$vars["verblijfsduur"]["1"]="1 ".txt("week","vars");
	$vars["verblijfsduur"]["2"]="2 ".txt("weken","vars");
	$vars["verblijfsduur"]["3"]="3 ".txt("weken","vars");
	$vars["verblijfsduur"]["4"]="4 ".txt("weken","vars");
	
	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<select name=\"fdu\" class=\"selectbox\">";
	while(list($key,$value)=each($vars["verblijfsduur"])) {
		echo "<option value=\"".($key=="-" ? "0" : $key)."\"";
		if($key==="-") echo " selected";
		echo ">".htmlentities($value)."</option>";
	}
	echo "</select>";
	echo "</div>";

	echo "<div class=\"zoekenboek_invulveld\">";
	echo "<input type=\"submit\" value=\" ".html("zoeken","index")."\">";
	echo "</div>";
	
	echo "<div style=\"margin-top:7px;margin-bottom:0px;\"><a href=\"".$vars["path"].txt("menu_zoek-en-boek").".php".($id=="aanbiedingen_zomerhuisje" ? "?aab=1&faab=1&filled=1" : "")."\">".html("uitgebreidzoeken","index")."</a></div>";
	echo "</form>";

	echo "</div>";
	echo "</div>\n";
	
	if($id=="aanbiedingen_zomerhuisje") {
		echo "</div>";
		echo "</div>";
	}
	
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

echo "</body>";
echo "</html>";

?>