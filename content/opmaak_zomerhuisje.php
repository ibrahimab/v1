<?php

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />\n";
echo "<title>";
if($grizzly_title) {
	echo $grizzly_title;
} else {
	if($id=="index") {
		echo htmlentities($vars["websitenaam"])." ".htmlentities(txt("sitetitel"));
		$vars["facebook_title"]=$vars["websitenaam"]." - ".txt("sitetitel");
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

# jQuery Chosen css
if($vars["jquery_chosen"]) {
	echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."css/chosen.css\" type=\"text/css\" />\n";
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_zomerhuisje.css?cache=".@filemtime("css/opmaak_zomerhuisje.css")."\" />\n";
if(file_exists("css/".$id."_zomerhuisje.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_zomerhuisje.css?cache=".@filemtime("css/".$id."_zomerhuisje.css")."\" />\n";
} elseif(file_exists("css/".$id.".css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id.".css?cache=".@filemtime("css/".$id.".css")."\" />\n";
}
if(file_exists("css/".$id."_zomerhuisje_extra.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/".$id."_zomerhuisje_extra.css?cache=".@filemtime("css/".$id."_zomerhuisje_extra.css")."\" />\n";
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

if(preg_match("/MSIE 6/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<!--[if lt IE 7]>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie6_zomerhuisje.css\" />\n<![endif]-->\n";
}
if(preg_match("/MSIE 7/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie7.css?cache=".@filemtime("css/ie7.css")."\" />\n";
}
if(preg_match("/MSIE 8/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/ie8.css?cache=".@filemtime("css/ie8.css")."\" />\n";
}

echo "<link rel=\"shortcut icon\" href=\"".$vars["path"]."favicon_zomerhuisje.ico\" />\n";

if($vars["canonical"]) {
	echo "<link rel=\"canonical\" href=\"".htmlentities($vars["canonical"])."\" />\n";
} elseif($_SERVER["HTTPS"]=="on") {
	echo "<link rel=\"canonical\" href=\"http://".htmlentities($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."\" />\n";
}

# jQuery
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jquery_url"])."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jqueryui_url"])."\" ></script>\n";

if($vars["jquery_maphilight"]) {
	# Google Maps API
	echo "<script src=\"".$vars["path"]."scripts/jquery.maphilight.min.js\" type=\"text/javascript\"></script>\n";
#	echo "<script src=\"".$vars["path"]."scripts/jquery.metadata.js\" type=\"text/javascript\"></script>\n";
}


if($vars["googlemaps"]) {
	# Google Maps API
	echo "<script src=\"https://maps-api-ssl.google.com/maps/api/js?v=3&sensor=false\" type=\"text/javascript\"></script>\n";
}

# jQuery Chosen javascript
if($vars["jquery_chosen"]) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/allfunctions.js?c=".@filemtime("scripts/allfunctions.js")."\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/jquery.chosen.js?c=".@filemtime("scripts/jquery.chosen.js")."\"></script>\n";
}

# Javascript-functions
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions.js?cache=".@filemtime("scripts/functions.js")."\" ></script>\n";
echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_zomerhuisje.js?cache=".@filemtime("scripts/functions_zomerhuisje.js")."\" ></script>\n";
if($grizzly_body and $_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html") {
	echo "<script type=\"text/javascript\" language=\"JavaScript\" src=\"https://www.zomerhuisje.nl/vakantie/zomerhuisjenl.js\"></script>\n";
}

if(file_exists("scripts/functions_".$id.".js")) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/functions_".$id.".js?cache=".@filemtime("scripts/functions_".$id.".js")."\" ></script>\n";
}
# Fancybox
if($vars["jquery_fancybox"]) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.4.pack.js\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."fancybox/jquery.fancybox-1.3.4.css?c=1\" type=\"text/css\" media=\"screen\" />\n";
}

# IE8-javascript
if(preg_match("/MSIE 8/",$_SERVER["HTTP_USER_AGENT"])) {
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/ie8.js?cache=".@filemtime("scripts/ie8.js")."\" ></script>\n";
}

if($vars["page_with_tabs"]) {
	# jQuery Address: t.b.v. correcte verwerking hashes in URL
	echo "<script type=\"text/javascript\" src=\"".$vars["path"]."scripts/jquery.address-1.5.min.js\"></script>\n";
}

# meta name robots
if(!$vars["canonical"] and ($_GET["back"] or $_GET["backtypeid"] or $_GET["filled"] or $_GET["page"] or $_GET["PHPSESSID"] or $id=="boeken")) {
	$robot_noindex=true;
}
if($robot_noindex or $robot_nofollow) {
	echo "<meta name=\"robots\" content=\"".($robot_noindex ? "no" : "")."index,".($robot_nofollow ? "no" : "")."follow\" />\n";
}

#echo "<meta name=\"keywords\" content=\"\" />\n";
echo "<meta name=\"description\" content=\"".wt_he(($meta_description ? $meta_description : ($title[$id]&&$id&&$id<>"index" ? $title[$id] : txt("description"))))."\" />\n";

# Facebook/Open Graph-gegevens in header opnemen
echo facebook_opengraph();

# Google+
echo "<link href=\"https://plus.google.com/113644542072220125279\" rel=\"publisher\" />\n";

# Google Analytics
echo googleanalytics();

echo "</head>\n";
echo "<body";
if($id<>"index") echo " onscroll=\"document.getElementById('terugnaarboven').style.visibility='visible'\"";
if($onload) echo " onload=\"".$onload."\"";
echo " id=\"body_".$id."\"";
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
		if($key=="chaletwinter") {
			echo "<span id=\"submenu_zomerhuisje\">";
			if($vars["websiteland"]=="be") {
				echo "<a href=\"http://www.chalet.be/\" target=\"_blank\">";
				echo htmlentities("Chalet.be wintersport");
			} else {
				if($_GET["fromsite"]=="chalettour" or $_COOKIE["fromsite"]=="chalettour") {
					echo "<a href=\"http://www.chalettour.nl/\" target=\"_blank\">Chalettour.nl wintersport";
				} else {
					echo "<a href=\"http://www.chalet.nl/\" target=\"_blank\">";
					echo htmlentities($value);
				}
			}
			echo "</a>";
			echo "</span>";
		} else {
			echo "<a href=\"".$vars["path"].txt("menu_".$key).(@in_array($key,$submenu_url_zonder_punt_php) ? "" : ".php")."\">";
			if($key=="favorieten") {
				 echo html("submenutitle_favorieten")." (<span id=\"favorietenaantal\">".intval($vars["bezoeker_aantal_favorieten"])."</span>)";
			} else {
				echo wt_he($value);
			}
			echo "</a>";
			echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
		}
	}
}
echo "</div>\n";

#if(@count($vars["topfoto"])<2) {
# Altijd deze topfoto's tonen (tijdelijk: later komen er pagina-specifieke topfoto's - 20-11-2012)
	unset($vars["topfoto"]);
	$vars["topfoto"][1]="pic/topfoto/topfoto-zomerhuisje-1.jpg";
	$vars["topfoto"][2]="pic/topfoto/topfoto-zomerhuisje-2.jpg";
#}

echo "<div id=\"topfoto\">";
while(list($key,$value)=each($vars["topfoto"])) {
	$topfototeller++;
	echo "<img src=\"".$vars["path"].$value."?c=".filemtime($value)."\" width=\"388\" height=\"140\" alt=\"\" class=\"noprint\">";
	if($topfototeller==1) {
		echo "<img src=\"".$vars["path"]."pic/leeg.gif\" width=\"194\" height=\"140\" alt=\"\" class=\"noprint\">";
	}
	if($topfototeller==2) {
		break;
	}
}
echo "<div style=\"position:absolute;top:0px;left:0px;\">";
echo "<img src=\"".$vars["path"]."pic/zomerhuisje_topbalk".($vars["websiteland"]=="be" ? "_eu" : "").".".(ereg("MSIE 6",$_SERVER["HTTP_USER_AGENT"]) ? "gif" : "png")."?c=1\" width=\"970\" height=\"179\" alt=\"".htmlentities($vars["websitenaam"])."\" border=\"0\">";
echo "</div>";

# Link naar hoofdpagina
if($id<>"index") {
	echo "<a href=\"".$vars["path"]."\" style=\"display:block;position:absolute;top:0px;left:371px;width:227px;height:180px;\" class=\"noprint\"></a>";
}
if($vars["websiteland"]=="nl") {
	echo "<div style=\"position:absolute;bottom:8px;right:4px;\" class=\"noprint\">";
	echo "<a href=\"".$vars["path"].txt("menu_algemenevoorwaarden").".php#sgr\"><img src=\"".$vars["path"]."pic/sgr_zomerhuisje.gif\" border=\"0\" width=\"35\" height=\"33\" style=\"margin-top:4px;\" alt=\"Stichting Garantiefonds Reisgelden\"></a>";
	echo "</div>";
}
echo "</div>";

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
echo "</div>\n";


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
echo "</div>"; # afsluiten meldingen

echo "<div style=\"clear: both;\"></div>\n";

echo "</div>"; # afsluiten balkbovencontent

# breadcrumbbalk
echo "<div id=\"breadcrumbbalk\" class=\"noprint\">";
echo "</div>"; # afsluiten breadcrumbbalk

# Content
echo "<div id=\"content\">";

# Te includen bestand bepalen
if($language_content) {
	if(file_exists("content/_meertalig/".$id."_zomerhuisje_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_zomerhuisje_".$vars["taal"].".html";
	} elseif(file_exists("content/_meertalig/".$id."_".$vars["taal"].".html")) {
		$include="content/_meertalig/".$id."_".$vars["taal"].".html";
	}
} else {
	if(file_exists("content/".$id."_zomerhuisje.html") and $id<>"aanbiedingen") {
		$include="content/".$id."_zomerhuisje.html";
	} elseif(file_exists("content/".$id."_nieuw.html")) {
		$include="content/".$id."_nieuw.html";
	} elseif(file_exists("content/".$id.".html")) {
		$include="content/".$id.".html";
	}
}
if(!$include) {
	if($_SERVER["HTTP_REFERER"]) {
		trigger_error("_notice: geen include-bestand bekend",E_USER_NOTICE);
	}
	header("Location: ".$vars["path"],true,301);
	exit;
}

if($vars["verberg_linkerkolom"]) {
	echo "<div id=\"contentvolledig\">";

	# Content includen
	echo "<div style=\"min-height:290px;\">";
	include($include);
	echo "</div>";

	if($last_acc_html and $id<>"saved" and (!$vars["verberg_directnaar"] or $id=="zoek-en-boek") and !$vars["verberg_lastacc"]) {
		echo $last_acc_html;
	}

	echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";

#	if(!$vars["wederverkoop"]) {
#		echo "<div id=\"contactgegevens\">".htmlentities($vars["websitenaam"])."&nbsp;&nbsp;&nbsp;".html("telefoonnummer")."&nbsp;&nbsp;&nbsp;<a href=\"mailto:".htmlentities($vars["email"])."\">".htmlentities($vars["email"])."</a></div>";
#	}

	echo "</div>\n";
} else {
	echo "<div id=\"bloklinks\" class=\"noprint\">";

	# telefoonblok
	echo "<div id=\"telefoonblok\" class=\"noprint".($id<>"contact" ? " telefoonblokhover" : "")."\"".($id<>"contact" ? " onclick=\"document.location.href='".$vars["path"].txt("menu_contact").".php';\"" : "").">";
	echo "<div id=\"telefoonblok_nummer\"><table cellspacing=\"0\" cellpadding=\"0\"><tr><td><img src=\"".$vars["path"]."pic/icon_telefoon_zomer.gif\"></td><td>".html("telefoonnummer_telefoonblok")."</td></tr></table></div>";
	echo "<div id=\"telefoonblok_open\">".html("openingstijden_telefoonblok")."</div>";
	echo "</div>"; # afsluiten telefoonblok

	if($vars["verberg_zoekenboeklinks"]) {
		echo "&nbsp;";
	} else {
		echo "<div id=\"zoekenboek\">";
		if($id=="aanbiedingen_zomerhuisje") {
			echo "<div id=\"zoekenboek_aanbiedingen\">";
			echo "<div id=\"zoekenboek_aanbiedingen_kop\">";
			echo html("meeraanbiedingen","index");
			echo "</div>";
			echo "<div style=\"padding:4px;\">";
		} else {
			echo "<div class=\"zoekenboek_kop\" style=\"margin-bottom:3px;\"><span class=\"bloklinksrechts_kop\">";
			echo html("zoekenboek","index");
			echo "</span></div>";
		}

		echo "<form method=\"get\" action=\"".$vars["path"].txt("menu_zoek-en-boek").".php\" name=\"zoeken\">";
		echo "<input type=\"hidden\" name=\"filled\" value=\"1\">";
		if($id=="aanbiedingen_zomerhuisje") {
			echo "<input type=\"hidden\" name=\"aab\" value=\"1\">";
			echo "<input type=\"hidden\" name=\"faab\" value=\"1\">";
		}
	#	echo "<div class=\"zoekenboek_tekst\" style=\"margin-bottom:3px;\">".html("skigebied","index")."</div>";

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
			if(preg_match("/^([0-9]+)-([0-9]+)/",$value,$regs)) {
				$landdid_currect=$regs[1];
				$skigebiedid_currect=$regs[2];
			}
			if(ereg("^([0-9]+)-0$",$value,$regs)) {
				if($optgroup_open) echo "</OPTGROUP>\n";
				echo "<OPTGROUP LABEL=\"".htmlentities($landnaam[$regs[1]])."\">\n";
				$optgroup_open=true;
			}
			echo "<option value=\"".$value."\"";
			if($vars["zoekenboeklinks_prefilled_form_fields"]["fsg"] and $vars["zoekenboeklinks_prefilled_form_fields"]["fsg"]==$value) {
				echo " selected";
			} elseif($skigebiedid and $skigebiedid==$skigebiedid_currect) {
				echo " selected";
			} elseif($landinfo["id"] and $landinfo["id"]==$landdid_currect and preg_match("/AAAAA/",$key)) {
				echo " selected";
			} elseif($land["id"] and $land["id"]==$landdid_currect and preg_match("/AAAAA/",$key)) {
				echo " selected";
			}
			echo ">";
			echo htmlentities(ereg_replace("^.*___","",$key))."</OPTION>";
		}
		if($optgroup_open) echo "</OPTGROUP>\n";

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

		echo "<input type=\"submit\" value=\" ".html("zoeken","index")."\">";

		echo "<div style=\"margin-top:4px;margin-bottom:0px;\"><a href=\"#\" id=\"uitgebreidzoeken\">".html("uitgebreidzoeken","index")."</a></div>";
		echo "</form>";

		if($id=="aanbiedingen_zomerhuisje") {
			echo "</div>";
			echo "</div>";
		}
		echo "</div>"; # afsluiten "zoekenboek"
	}

	#
	# Blok links met accommodatie
	#
	if($id=="index" or $id=="thema" or $id=="land" or $id=="themas" or $id=="bestemmingen" or $id=="toonskigebied" or $id=="toonplaats") {
		$blokaccommodatie=opvalblok();
	}

	if($html_ipv_blokaccommodatie) {
		echo "<div id=\"blokaccommodatie\"".($html_ipv_blokaccommodatie_bgcolor ? " style=\"background-color:".$html_ipv_blokaccommodatie_bgcolor."\"" : "").">";
		echo $html_ipv_blokaccommodatie;
		echo "</div>";
	} elseif($blokaccommodatie) {
		echo $blokaccommodatie;
	} else {
		echo "<div id=\"blokaccommodatie\">&nbsp;</div>";
	}

	if($id=="index") {
		# Opsomming "Waarom Zomerhuisje?"
		echo "<div id=\"hoofdpagina_waarom\" onclick=\"document.location.href='".txt("menu_wie-zijn-wij").".php';\">";
		echo "<div class=\"kop\">".html("waarom","index",array("v_websitenaam"=>$vars["websitenaam"]))."</div>";
		echo "<div><ul>";
		echo "<li>Bergvakantie-specialist</li>";
		echo "<li>12 jaar ervaring</li>";
		echo "<li>Uniek aanbod</li>";
		echo "<li>Persoonlijke service</li>";
		echo "<li>Lid SGR-Garantiefonds</li>";
		echo "</ul></div>";
		echo "</div>\n";

		# Tijdelijk aan de linkerkant van de homepage (vanwege Alpe d'HuZes-button)
		echo "<div id=\"hoofdpagina_sociallinks\">";
		echo "<div style=\"float:left;padding-left:10px;\">";
		echo "<div style=\"margin-bottom:3px;\">Volg ons:</div>";
		echo "<a href=\"https://www.facebook.com/Zomerhuisje.nl\" title=\"Volg Zomerhuisje.nl via Facebook\" target=\"_blank\"><img src=\"".$vars["path"]."pic/icon_facebook_off.png\" width=\"32\" height=\"32\" class=\"img-swap\"></a>";
		echo "<a href=\"https://twitter.com/@Zomerhuisje\" title=\"Volg Zomerhuisje.nl via Twitter\" target=\"_blank\"><img src=\"".$vars["path"]."pic/icon_twitter_off.png\" width=\"32\" height=\"32\" class=\"img-swap\"></a>";
		echo "<a href=\"https://plus.google.com/113644542072220125279?prsrc=3\" title=\"Volg Zomerhuisje.nl via Google+\" target=\"_blank\"><img src=\"".$vars["path"]."pic/icon_googleplus_off.png\" width=\"32\" height=\"32\" class=\"img-swap\"></a>";
		echo "</div>\n";
		echo "</div>\n";  # afsluiten hoofdpagina_sociallinks

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
		echo "<h2>Vakantie in de bergen</h2>";
		include($include);
	} else {

		if($id=="zoek-en-boek") {
			# zorgen voor hoge content i.v.m. "verfijn"-blok
			echo "<div style=\"min-height:840px;\">";
		} else {
			echo "<div style=\"min-height:290px;\">";
		}

		include($include);

		echo "</div>"; # afsluiten naamloze div

		# Laatst bekeken accommodaties
		if($last_acc_html and $id<>"saved" and (!$vars["verberg_directnaar"] or $id=="zoek-en-boek") and !$vars["verberg_lastacc"]) {
			echo $last_acc_html;
		}

		echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"visibility:hidden;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";
	}

	echo "</div>";
	echo "<div style=\"clear: both;\"></div>\n";
}

echo "<div id=\"colofon\" class=\"noprint\">".html("handelsnaam_zomerhuisje")."&nbsp;&nbsp;-&nbsp;&nbsp;<a href=\"mailto:".htmlentities($vars["email"])."\">".htmlentities($vars["email"])."</a>&nbsp;&nbsp;-&nbsp;&nbsp;tel:&nbsp;".html("telefoonnummer_alleen")."</div>\n";

echo "<div id=\"submenu\" style=\"text-align:center;\"><a href=\"".$vars["path"]."disclaimer.php\">Disclaimer</a> - <a href=\"".$vars["path"]."privacy-statement.php\">Privacy statement</a></div>";

echo "</div>\n"; # "content" afsluiten


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

if($lhtml) {
	# Afwijkende content linkerblok plaatsen
	echo "<div id=\"linkerbalk_overlay\" class=\"noprint\">";
	echo $lhtml;
	echo "</div>";
}

echo "</div>\n"; # "wrapper" afsluiten

# Ajaxloader in het midden van de pagina
echo "<div id=\"ajaxloader_page\"></div>";

# Balk met cookie-melding cookiebalk
if($vars["cookiemelding_tonen"] and $vars["websiteland"]=="nl" and (!$_COOKIE["cookiemelding_gelezen"] or $vars["lokale_testserver"])) {
	echo "<p>&nbsp;</p>";
	echo "<div id=\"cookie_bottombar\" class=\"noprint\"><div id=\"cookie_bottombar_wrapper\"><div id=\"cookie_bottombar_text\">".html("cookiemelding","vars",array("h_1"=>"<a href=\"".$vars["path"]."privacy-statement.php\">","h_2"=>"</a>"))."</div><div id=\"cookie_bottombar_close\">sluiten</div></div></div>";
}

# Balk met opvallende melding
if($vars["opvalmelding_tonen"] and (!$_COOKIE["opvalmelding_gelezen"] or $vars["lokale_testserver"])) {
	echo "<div id=\"opval_bottombar\" class=\"noprint\"><div id=\"opval_bottombar_wrapper\"><div id=\"opval_bottombar_text\">".nl2br(html("opvalmelding","vars",array("h_1"=>"<a href=\"mailto:".$vars["email"]."\">","h_2"=>"</a>","v_email"=>$vars["email"])))."</div><div id=\"opval_bottombar_close\">&nbsp;</div></div></div>";
}


if($voorkant_cms and !$_GET["cmsuit"] and $interneinfo) {
	echo "<div id=\"interneinfo_rechts\">";
	echo $interneinfo;
	echo "</div>"; # interneinfo_rechts
}

// fancybox-popup bij Zomerhuisje (melding over koerswijziging)
if($vars["trackercookie_terugkerende_bezoeker"] and !$_COOKIE["zh_kw"]) {
	# zh_kw = Zomerhuisje-koerswijziging
	echo "<a href=\"".$vars["path"]."popup_fancybox.php?popupfbid=zomerhuisje-koerswijziging\" id=\"zomerhuisje_popup\" style=\"display:none;\" rel=\"nofollow\">&nbsp;</a>";
}


echo "</body>";
echo "</html>";

?>