<?php

$page_id = $id;
# Te includen bestand bepalen

//if($_SERVER["REMOTE_ADDR"] == '192.168.192.240') {
//	if($language_content) {
//		if(file_exists("content".$mobile."/_meertalig/".$id."_".$vars["taal"].".html")) {
//			$include="content".$mobile."/_meertalig/".$id."_".$vars["taal"].".html";
//		}
//	} else {
//		if(file_exists("content".$mobile."/".$id."_chalet.html")) {
//			$include="content".$mobile."/".$id."_chalet.html";
//		} elseif(file_exists("content".$mobile."/".$id."_nieuw.html")) {
//			$include="content".$mobile."/".$id."_nieuw.html";
//		} elseif(file_exists("content".$mobile."/".$id.".html")) {
//			$include="content".$mobile."/".$id.".html";
//		}
//	}
//} else {
	// Restrict access to certain pages
//	$allow = array("index", "zoek-en-boek", "contact", "toonaccommodatie", "vraag-ons-advies",
//	"aanbiedingen", "veelgestelde-vragen", "boeken", "boeking_bevestigd", "bsys_selecteren", "bsys",
//    "bsys_payments", "bsys_wijzigen", "favorieten");
//	if(in_array($id, $allow)) {
	if($language_content) {
		if(file_exists("content".$mobile."/_meertalig/".$id."_".$vars["taal"].".html")) {
			$include="content".$mobile."/_meertalig/".$id."_".$vars["taal"].".html";
		}
	} else {

		if(file_exists("content".$mobile."/".$id."_chalet.html")) {
			$include="content".$mobile."/".$id."_chalet.html";
		} elseif(file_exists("content".$mobile."/".$id."_nieuw.html")) {
			$include="content".$mobile."/".$id."_nieuw.html";
		} elseif(file_exists("content".$mobile."/".$id.".html")) {
			$include="content".$mobile."/".$id.".html";
		}
	}
//	}
//}
if(!$include) {
	// header("Location: ".$vars["path"],true,301);
	// exit;

	$vars["only_for_desktop_available"] = true;

	include("content/opmaak_chalet.php");
	exit;


	if($_SERVER["HTTP_REFERER"]) {
		if(!preg_match("@\.php/@",$_SERVER["REQUEST_URI"])) {
			trigger_error("_notice: geen include-bestand bekend",E_USER_NOTICE);
		}
	}
}

if($vars["website"]=="E") {
	# SPDY-header sturen (prefetchen diverse CSS-bestanden)
#	header('X-Associated-Content: "'.$vars["path"].'css/opmaak_alle_sites.css.phpcache?cache='.@filemtime("css/opmaak_alle_sites.css.phpcache").'&type='.$vars["websitetype"].'", "'.$vars["path"].'css/tabs.css.phpcache?cache='.@filemtime("css/tabs.css.phpcache").'&type='.$vars["websitetype"].'"');
}



echo "<!DOCTYPE html>\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\"\n prefix=\"fb: http://www.facebook.com/2008/fbml og: http://ogp.me/ns#\">\n";
echo "<head>\n";

echo $opmaak->header_begin();

echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1\" />";
echo "<!--[if IE]><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" /><![endif]-->\n";

echo "<title>";
if($grizzly_title) {
	echo $grizzly_title;
} else {
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
}
echo "</title>";

if($vars["page_with_tabs"]) {
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/tabs.css.phpcache?cache=".@filemtime("css/mobile/tabs.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
}

# Font Awesome-css

if(!$vars["page_with_tabs"]) {
# jQuery UI theme laden
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css?cache=".@filemtime("css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css")."\" />\n";
}

# jQuery Chosen css
#if($vars["jquery_chosen"]) {
echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."css/mobile/chosen.css\" type=\"text/css\" />\n";
#}

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
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/jquery.mmenu.all.css?cache=".@filemtime("css/mobile/jquery.mmenu.all.css")."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/mobile.css?cache=".@filemtime("css/mobile/mobile.css")."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/responsive.css?cache=".@filemtime("css/mobile/responsive.css")."\" />\n";

# JQuery
echo "<script type=\"text/javascript\" src=\"".wt_he($vars["jquery_url"])."\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".wt_he($vars["jqueryui_url"])."\"></script>\n";

echo $opmaak->header_end();

echo "</head>";

echo $opmaak->body_tag();
echo $opmaak->google_tag_manager();
echo "\n";

	echo "<div onclick='' class=\"header\">\n";
	echo "<a href=\"#menu-left\"  id=\"show\">&nbsp;</a>\n";
	echo "<div class=\"logo\">\n";
		echo "<a href=\"".$vars["path"]."\"><img src=\"".$vars["path"]."pic/logo_chalet";
#if($vars["websitetype"]==1 and $vars["taal"]=="nl" and $vars["websiteland"]=="nl") echo "_10jr";
if($vars["taal"]<>"nl") echo "_eu";
if($vars["websiteland"]=="be") echo "_be";
if($vars["websitetype"]==4 or $vars["websitetype"]==5) echo "_tour";
echo ".gif?c=2\" width=\"130\" height=\"97\" alt=\"".wt_he($vars["websitenaam"])."\" /></a>\n";
	echo "</div>\n"; #close .logo
	echo "<div class=\"motto\">".html("koptekst_mobile","index",array("h_b" => "<br />"))."</div>\n";
	echo "<div class=\"clear\"></div>\n";
	if($id == "index"){
		echo "<div class=\"topfoto\">&nbsp;</div>\n";
	} else {
		echo "<div style=\"height:20px;\">&nbsp;</div>\n";
	}
echo "</div>\n\n"; #close .header

echo "<div id=\"menu-left\" class=\"nav mm-menu\">";
echo "<ul>";
	while(list($key,$value)=each($mobile_menu)) {
		if($vars["active_menu_item"]) {
			$checkid=$vars["active_menu_item"];
		} else {
			$checkid=$id;
		}
		if($key == 'start-chat' && $vars['website'] == 'E') continue;


		if ($key == 'home') {
			echo "<li><a href=\"/\"";
		} else {
			echo "<li><a href=\"".$vars["path"];
		}


		if($key<>"index" && $key<>"start-chat" && $key<>"home") {
			echo txt("menu_".$key).".php";
		}
		if($key=="start-chat") {
			echo "\" onclick=\"LC_API.open_chat_window();return false;";
		}
		echo "\">";

		if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
			echo "<span class=\"hoofdmenu_actief\">";
		}
		if($key=="favorieten") {
			echo html("submenutitle_favorieten")." (<span id=\"favorietenaantal\">".intval($vars["bezoeker_aantal_favorieten"])."</span>)";
		} else {
			echo wt_he($value);
		}
		if($key==$checkid or ($checkid=="accommodaties" and $key=="zoek-en-boek")) {
			echo "</span>";
		}

		echo "</a></li>";
	}
echo "</ul>";
echo "</div>";


echo "<div onclick='' id=\"meldingen\">";
if($helemaalboven) echo $helemaalboven;
$rechtsboven=str_replace("<span class=\"x-small\">","<span>",$rechtsboven);

if($rechtsboven) {
	if($helemaalboven) echo "&nbsp;&nbsp;";
	echo $rechtsboven;
}
echo "</div><!-- END #meldingen -->";

echo "<div onclick='' class=\"wrapper\">";

	if($id<>"index" and $id<>"toonaccommodatie" and !$laat_titel_weg) {
		if($header[$id]) {
			echo "<h1>".wt_he($header[$id])."</h1>";
		} else {
			echo "<h1>".wt_he($title[$id])."</h1>";
		}
	}

	include($include);

echo "<div class=\"paymenticons\" id=\"kleinelogos\">";
if($vars["websiteland"]=="nl") {
	echo "<a href=\"".$vars["path"].txt("menu_algemenevoorwaarden").".php#sgr\" class=\"sgrlogo_hoofdmenu\"><img src=\"".$vars["path"]."pic/sgr_hoofdmenu.png\" height=\"27\" alt=\"Stichting Garantiefonds Reisgelden\" /></a>";
}

if($vars["docdata_payments"]) {
	if(count($vars["docdata_payments"]) > 0) {
		foreach($vars["docdata_payments"] as $key => $value) {
			echo "<span class=\"". $value["by"] ."\" title=\"". $value["title"] ."\"></span>";
		}
	}
}
echo "</div><!-- END #kleinelogos -->\n";

//echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"display:none;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";

echo "</div><!-- END #wrapper -->\n";

echo "<div id=\"ajaxloader_page\"><img src=\"".$vars["path"]."pic/ajax-loader-large2.gif\" alt=\"loading...\"></div>";


echo "<div class=\"footer\">
	<p id=\"colofon\">";
if($vars["website"]=="C") {
	echo wt_he($vars["websiteinfo"]["langewebsitenaam"][$vars["website"]]);
} else {
	echo wt_he($vars["websiteinfo"]["websitenaam"][$vars["website"]]);
}
echo " - <a href=\"mailto:".wt_he($vars["websiteinfo"]["email"][$vars["website"]])."\">".wt_he($vars["websiteinfo"]["email"][$vars["website"]])."</a> - ".html("telefoonnummer_colofon");
echo "</p>";
echo "</div>\n";

echo "<p id=\"fullwebsite\"><a href=\"/\" onclick=\"return switch_website('desktop');\">".txt("naar_desktopversie","mobile")."</a></p>";


# Balk met cookie-melding cookiebalk
if($opmaak->toon_cookiebalk()) {
	echo "<div id=\"cookie_bottombar\" class=\"noprint\"><div id=\"cookie_bottombar_wrapper\"><div id=\"cookie_bottombar_text\">".html("cookiemelding","vars",array("h_1"=>"<a href=\"".$vars["path"]."privacy-statement.php\">","h_2"=>"</a>"))."</div><div id=\"cookie_bottombar_close\">sluiten</div></div></div>";
}

if(!$onMobile){
	echo "<div id=\"notification_bottombar\" class=\"noprint\"><div id=\"notification_bottombar_wrapper\"><div id=\"notification_bottombar_text\">".html("mobilenotification","vars",array("h_1"=>"<a onclick=\"return switch_website('desktop');\" href=\"".$vars["path"]."\">","h_2"=>"</a>"))."</div><div id=\"notification_bottombar_close\">sluiten</div></div></div>";
}


######################### Load javascript files

if($vars["googlemaps"]) {
# Google Maps API
echo "<script src=\"https://maps-api-ssl.google.com/maps/api/js?v=3&amp;sensor=false\" type=\"text/javascript\"></script>\n";
}

# Google Analytics
echo googleanalytics();

if($vars["website"]=="E") {
# jQuery ms-Dropdown (https://github.com/marghoobsuleman/ms-Dropdown)
$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.dd.min.js'";
}

# jQuery Chosen javascript
#if($vars["jquery_chosen"]) {
$lazyLoadJs[] = "'".$vars["path"]."scripts/allfunctions.js?c=".@filemtime("scripts/allfunctions.js")."'";
$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/jquery.chosen.js?c=".@filemtime("scripts/mobile/jquery.chosen.js")."'";

#}

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
$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/functions.js?cache=".@filemtime("scripts/mobile/functions.js")."'";
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

$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/jquery.mmenu.js?c=".@filemtime("scripts/mobile/jquery.mmenu.js")."'";
$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/ios-orientationchange-fix.js?c=".@filemtime("scripts/mobile/ios-orientationchange-fix.js")."'";
$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/jquery_002.js?c=".@filemtime("scripts/mobile/jquery_002.js")."'";
$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/mobile_functions.js?cache=".@filemtime("scripts/mobile/mobile_functions.js")."'";
?>

<script type="text/javascript">
	var isMobile = true;
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