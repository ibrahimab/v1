<?php

#echo "Vallandry";
#exit;



echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
echo "<head>";
echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9\" />\n";
echo "<title>";
if($id=="index") {
	echo htmlentities($vars["websitenaam"])." - ".htmlentities(txt("sitetitel"));
} else {
	if($title[$id] and $id) {
		echo htmlentities($title[$id])." - ";
	}
	echo htmlentities($vars["websitenaam"]);
}
echo "</title>";

if($vars["page_with_tabs"]) {
#	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/tabs.css.phpcache?cache=".@filemtime("css/tabs.css.phpcache")."\" />\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/tabs.css.phpcache?cache=".@filemtime("css/tabs.css.phpcache")."?type=".$vars["websitetype"]."\" />\n";	
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&type=".$vars["websitetype"]."\" />\n";

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_vallandry.css?cache=".@filemtime("css/opmaak_vallandry.css")."\" />\n";
if(file_exists("css/".$id."_vallandry.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_vallandry.css?cache=".@filemtime("css/".$id."_vallandry.css")."\" />\n";
} elseif(file_exists("css/".$id.".css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id.".css?cache=".@filemtime("css/".$id.".css")."\" />\n";
}
if(file_exists("css/".$id."_vallandry_extra.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_vallandry_extra.css?cache=".@filemtime("css/".$id."_vallandry_extra.css")."\" />\n";
}

if($voorkant_cms and !$_GET["cmsuit"]) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/voorkantcms.css?cache=".@filemtime("css/voorkantcms.css")."\" />\n";
}

if(is_array($vars["extracss"])) {
	while(list($key,$value)=each($vars["extracss"])) {
		if(file_exists("css/".$value)) {
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$value."?cache=".@filemtime("css/".$value)."\" />\n";
		}
	}
}

if(preg_match("/MSIE 7/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<!--[if lt IE 7]>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie6_vallandry.css\" />\n<![endif]-->\n";
}

if(preg_match("/MSIE 7/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7.css?cache=".@filemtime("css/ie7.css")."\" />\n";
}

echo "<link rel=\"shortcut icon\" href=\"".$vars["path"]."favicon_vallandry.ico\" />\n";

if($vars["canonical"]) {
	echo "<link rel=\"canonical\" href=\"".htmlentities($vars["canonical"])."\" />\n";
} elseif($_SERVER["HTTPS"]=="on") {
	echo "<link rel=\"canonical\" href=\"http://".htmlentities($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."\" />\n";
}

# JQuery
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jquery_url"])."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jqueryui_url"])."\" ></script>\n";

if($vars["googlemaps"]) {
	# Google Maps API
	echo "<script src=\"https://maps-api-ssl.google.com/maps/api/js?v=3&sensor=false\" type=\"text/javascript\"></script>\n";
}

# Chosen
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/jquery.chosen.min.js\"></script>\n";

echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_vallandry.js?cache=".@filemtime("scripts/functions_vallandry.js")."\" ></script>\n";

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

#echo "<meta name=\"keywords\" content=\"\" />\n";
#echo "<meta name=\"description\" content=\"".htmlentities(txt("subtitel"))."\" />";
echo "<meta name=\"description\" content=\"".wt_he(($meta_description ? $meta_description : ($title[$id]&&$id&&$id<>"index" ? $title[$id] : txt("subtitel"))))."\" />\n";

# Google Analytics
echo googleanalytics();

echo "</head>\n";
echo "<body";
if($id<>"index") echo " onscroll=\"document.getElementById('terugnaarboven').style.visibility='visible'\"";
if($onload) echo " onload=\"".$onload."\"";
echo ">";
echo "<div id=\"wrapper\">";

echo "<div id=\"top\">";

echo "<div id=\"menubalk_print\" class=\"onlyprint\">";
echo "<h2>".htmlentities($vars["websitenaam"])."</h2>";
echo "<b>".htmlentities(ereg_replace("http://([a-z0-9\.]*)/.*","\\1",$vars["basehref"]))."<p>".html("telefoonnummer")."</b>";
echo "</div>";

echo "<div id=\"submenu\">";
while(list($key,$value)=each($submenu)) {
	if($value<>"-") {
		echo "<a href=\"".$vars["path"].txt("menu_".$key).".php\">";
		echo htmlentities($value);
		echo "</a>";
		echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
	}
}
if($vars["taal"]=="en") {
	echo "<a href=\"http://www.chaletsinvallandry.nl/\"><img src=\"".$vars["path"]."pic/vlag_nl_klein.gif\" border=\"0\" width=\"17\" height=\"11\" style=\"padding-top:0px;\"></a>";
} else {
	echo "<a href=\"http://www.chaletsinvallandry.com/\"><img src=\"".$vars["path"]."pic/vlag_en_klein.gif\" border=\"0\" width=\"17\" height=\"11\" style=\"padding-top:0px;\"></a>";
}

echo "</div>\n";

echo "<div id=\"topfoto\">";
if($id<>"index") echo "<a href=\"".$vars["path"]."\">";
echo "<img src=\"".$vars["path"]."pic/vallandry_topbalk".($vars["taal"]=="en" ? "_en" : "").".jpg\" width=\"970\" height=\"161\" alt=\"\" border=\"0\">";
if($id<>"index") echo "</a>";
echo "</div>";

echo "<div id=\"hoofdmenu\" class=\"noprint\">";
while(list($key,$value)=each($menu)) {
	if($menuteller) {
		echo "<span class=\"hoofdmenuspacer\">|</span>";
	}
	$menuteller++;
	echo "<a href=\"".$vars["path"];
	if($key<>"index") {
		echo txt("menu_".$key).".php";
	}
	echo "\">&nbsp;&nbsp;";
	if($key==$id or ($id=="accommodaties" and $key=="zoek-en-boek")) {
		echo "<span class=\"hoofdmenu_actief\">";
	}
	echo htmlentities($value);
	if($key==$id or ($id=="accommodaties" and $key=="zoek-en-boek")) {
		echo "</span>";
	}
	echo "&nbsp;&nbsp;</a>";
}
echo "</div>\n";


echo "</div>\n";

# Balk boven content
echo "<div id=\"balkbovencontent\" class=\"noprint\">";

# Bekeken en bewaarde accommodaties
if($last_acc and $id<>"saved") {
	echo "<div id=\"bekekenbewaard\">";
	echo "<a href=\"".$vars["path"]."saved.php\">".html("laatstbekekenaccommodaties","index")."</a>";
	echo "</div>";
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

echo "</div>";

# Content
echo "<div id=\"content\">";

# Te includen bestand bepalen
if($language_content) {
	if(file_exists("content/_meertalig/".$id."_vallandry_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_vallandry_".$vars["taal"].".html";
	} elseif(file_exists("content/_meertalig/".$id."_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_".$vars["taal"].".html";
	}
} else {
	if(file_exists("content/".$id."_vallandry.html")) {
		$include="content/".$id."_vallandry.html";
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

if($vars["verberg_linkerkolom"]) {
	echo "<div id=\"contentvolledig\">";
	
	# Content includen
	include($include);
	echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";

#	if(!$vars["wederverkoop"]) {
#		echo "<div id=\"contactgegevens\">".htmlentities($vars["websitenaam"])."&nbsp;&nbsp;&nbsp;".html("telefoonnummer")."&nbsp;&nbsp;&nbsp;<a href=\"mailto:".htmlentities($vars["email"])."\">".htmlentities($vars["email"])."</a></div>";
#	}

	echo "</div>\n";
} else {
	echo "<div id=\"bloklinks\">";
	
	if($vars["verberg_zoekenboeklinks"]) {
		echo "&nbsp;";	
	} else {
		echo "<div id=\"zoekenboek\">";
		
		echo "<div class=\"zoekenboek_kop\" style=\"margin-bottom:15px;\"><span class=\"bloklinksrechts_kop\">".html("zoekenboek","index")."</span></div>";
	
		echo "<form method=\"get\" action=\"".$vars["path"].txt("menu_zoek-en-boek").".php\" name=\"zoeken\">";
		echo "<input type=\"hidden\" name=\"filled\" value=\"1\">";
	
		echo "<div class=\"zoekenboek_invulveld\">";
		echo "<input type=\"text\" name=\"fzt\" class=\"tekstzoeken\" value=\"-- ".html("trefwoord","index")." --\" onfocus=\"if(this.value=='-- ".html("trefwoord","index")." --') this.value='';\" onblur=\"if(this.value=='') this.value='-- ".html("trefwoord","index")." --';\">";
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

		# Verblijfsduur
#		$vars["verblijfsduur"]["-"]="-- ".txt("verblijfsduur","index")." --";
#		$vars["verblijfsduur"]["0"]=txt("geenvoorkeur","vars");
#		$vars["verblijfsduur"]["1"]="1 ".txt("week","vars");
#		$vars["verblijfsduur"]["2"]="2 ".txt("weken","vars");
#		$vars["verblijfsduur"]["3"]="3 ".txt("weken","vars");
#		$vars["verblijfsduur"]["4"]="4 ".txt("weken","vars");
#		
#		echo "<div class=\"zoekenboek_invulveld\">";
#		echo "<select name=\"fdu\" class=\"selectbox\">";
#		while(list($key,$value)=each($vars["verblijfsduur"])) {
#			echo "<option value=\"".($key=="-" ? "0" : $key)."\"";
#			if($key==="-") echo " selected";
#			echo ">".htmlentities($value)."</option>";
#		}
#		echo "</select>";
#		echo "</div>";
	
		echo "<input type=\"submit\" value=\" ".html("zoeken","index")."\">";
		
		echo "<div style=\"margin-top:10px;margin-bottom:0px;\"><a href=\"".$vars["path"].txt("menu_zoek-en-boek").".php\">".html("uitgebreidzoeken","index")."</a></div>";
		echo "</form>";

		if($vars["website"]=="V") {
			echo "<a href=\"".$vars["path"].txt("menu_algemenevoorwaarden").".php#sgr\"><img src=\"".$vars["path"]."pic/sgr_zomerhuisje.gif\" width=\"35\" height=\"33\" style=\"margin-top:20px;margin-bottom:100px;\" border=\"0\"></a>";
		} else {
			echo "<img src=\"".$vars["path"]."pic/leeg.gif\" width=\"35\" height=\"33\" style=\"margin-top:20px;margin-bottom:100px;\" border=\"0\">";
		}
		
		echo "</div>"; # afsluiten "zoekenboek"
	}
	
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
		include($include);
#		echo "<br>&nbsp;";
		echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	}
	
	echo "</div>";
	echo "<div style=\"clear: both;\"></div>\n";
}

echo "<div id=\"colofon\" class=\"noprint\">".html("chaletsinvallandry")."&nbsp;&nbsp;-&nbsp;&nbsp;<a href=\"mailto:".htmlentities($vars["email"])."\">".ereg_replace("invallandry","<i>in</i>vallandry",htmlentities($vars["email"]))."</a>&nbsp;&nbsp;-&nbsp;&nbsp;".html("telefoonnummer")."</div>\n";
echo "<div id=\"submenu\" class=\"noprint\" style=\"text-align:center;\"> <a href=\"".htmlentities($vars["websiteinfo"]["privacystatement"][$vars["website"]])."\">Privacy statement</a> - <a href=\"".htmlentities($vars["websiteinfo"]["disclaimer"][$vars["website"]])."\">Disclaimer</a></div>";

echo "</div>\n"; # "content" afsluiten

echo "</div>\n"; # "wrapper" afsluiten


if($voorkant_cms and !$_GET["cmsuit"] and $interneinfo) {
		echo "<div id=\"interneinfo_rechts\">";
		echo $interneinfo;
		echo "</div>"; # interneinfo_rechts
}

echo "</body>";
echo "</html>";

?>