<?php
#error_reporting(E_ALL);

$page_id = $id;
# Te includen bestand bepalen
if($language_content) {
	if(file_exists("content".$mobile."/_meertalig/".$id."_italissima_".$vars["taal"].".html")) {
		$include="content".$mobile."/_meertalig/".$id."_italissima_".$vars["taal"].".html";
	} elseif(file_exists("content".$mobile."/_meertalig/".$id."_".$vars["taal"].".html")) {
		$include="content".$mobile."/_meertalig/".$id."_".$vars["taal"].".html";
	}
} else {
	if(file_exists("content".$mobile."/".$id."_italissima.html") and $id<>"aanbiedingen") {
		$include="content".$mobile."/".$id."_italissima.html";
	} elseif(file_exists("content".$mobile."/".$id."_nieuw.html")) {
		$include="content".$mobile."/".$id."_nieuw.html";
	} elseif(file_exists("content".$mobile."/".$id.".html")) {
		$include="content".$mobile."/".$id.".html";
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
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\"\n prefix=\"fb: http://www.facebook.com/2008/fbml og: http://ogp.me/ns#\">\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1\" />";
echo "<!--[if IE]><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" /><![endif]-->\n";
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
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/tabs.css.phpcache?cache=".@filemtime("css/mobile/tabs.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
}

# Font Awesome-css
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/font-awesome.min.css?c=1\" />\n";

if(!$vars["page_with_tabs"]) {
	# jQuery UI theme laden
	//echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css?cache=".@filemtime("css/jqueryui-theme/custom-theme/jquery-ui-1.8.22.custom.css")."\" />\n";
    echo " <link rel='stylesheet' href='http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css'>";
}else{
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/jqueryui-theme/custom-theme/jquery-ui-1.10.4.custom.css?cache=".@filemtime("css/mobile/jqueryui-theme/custom-theme/jquery-ui-1.10.4.custom.css")."\" />\n";
}

# jQuery Chosen css
#if($vars["jquery_chosen"]) {
echo "<link rel=\"stylesheet\" href=\"".$vars["path"]."css/mobile/chosen.css\" type=\"text/css\" />\n";
#}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_alle_sites.css.phpcache?cache=".@filemtime("css/opmaak_alle_sites.css.phpcache")."&amp;type=".$vars["websitetype"]."\" />\n";

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/opmaak_italissima.css?cache=".@filemtime("css/opmaak_italissima.css")."\" />\n";
if($id == "bestemmingen") {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/jqvmap.css?cache=".@filemtime("css/jqvmap.css")."\" />\n";
}
if(file_exists("css/mobile/".$id."_italissima.css")) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/".$id."_italissima.css?cache=".@filemtime("css/".$id."_italissima.css")."\" />\n";
}
elseif(file_exists("css/".$id."_italissima.css")) {
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

echo "<script>";
// Hides the tabs + zoekblok during initialization
echo 'document.write(\'<style type="text/css">	#tabs { visibility: hidden; } #body_zoek-en-boek #zoekblok, #body_zoek-en-boek #verfijn { visibility: hidden; } </style>\');';
echo "</script>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/jquery.mmenu.all.css?cache=".@filemtime("css/mobile/jquery.mmenu.all.css")."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/mobile_italissima.css?cache=".@filemtime("css/mobile/mobile_italissima.css")."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/mobile/responsive.css?cache=".@filemtime("css/mobile/responsive.css")."\" />\n";

# JQuery
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jquery_url"])."\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".htmlentities($vars["jqueryui_url"])."\"></script>\n";

echo "<link rel=\"shortcut icon\" href=\"".$vars["path"]."favicon_italissima.ico\" />\n";

if($vars["canonical"]) {
	echo "<link rel=\"canonical\" href=\"".htmlentities($vars["canonical"])."\" />\n";
} elseif($_SERVER["HTTPS"]=="on") {
	echo "<link rel=\"canonical\" href=\"http://".htmlentities($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."\" />\n";
}

#echo "<script type=\"text/javascript\" src=\"http://labs.juliendecaudin.com/barousel/js/jquery.thslide.js\"></script>\n";



if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	# jQuery inlog: http://prinzhorn.github.com/jquery-inlog/
	# $.inlog(true);
	# $.("").....;
	# $.inlog(false);
#	echo "<script type=\"text/javascript\" src=\"".htmlentities("http://ss.postvak.net/_intern/extra/jquery.inlog.js")."\" ></script>\n";
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

# admin voor facebook (Bjorn: 100001459420163, Jeroen WebTastic: 100002390923231)
#echo "<meta property=\"fb:admins\" content=\"100001459420163,100002390923231\"/>\n";

# Facebook app_id (om reisblog-comments te kunnen moderaten)
echo "<meta property=\"fb:app_id\" content=\"317521581710031\"/>\n";

# Google+
echo "<link href=\"https://plus.google.com/118061823772005667855\" rel=\"publisher\" />\n";


echo "</head>\n";

echo $opmaak->body_tag();
echo "\n";

echo "<div onclick='' class=\"header\">\n";
	echo "<a href=\"#menu-left\"  id=\"show\">&nbsp;</a>\n";



echo "<div class=\"logo\">";
if($id<>"index") echo "<a href=\"".$vars["path"]."\">";
echo "<img src=\"".$vars["path"]."pic/logo_italissima.gif\"  style=\"border:0;\" alt=\"".htmlentities($vars["websitenaam"])."\" /></a>";
echo "</div>\n"; #close .logo
echo "<div class=\"motto\">".html("koptekst_italissima_mobile","index",array("h_b" => "<br />"))."</div>\n";
echo "<div class=\"clear\"></div>\n";

echo "<div class=\"italissimatopfoto\">&nbsp;</div>\n";

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
			echo htmlentities($value);
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
$rechtsboven=str_replace("<font size=\"1\">","<font>",$rechtsboven);
if($rechtsboven) {
	if($helemaalboven) echo "&nbsp;&nbsp;";
	echo $rechtsboven;
}
echo "</div>"; # afsluiten meldingen


echo "<div style=\"clear: both;\"></div>\n";

echo "</div>"; # afsluiten balkbovencontent




# Content
echo "<div onclick='' class=\"wrapper\">";

	if($id<>"index" and $id<>"toonaccommodatie" and !$laat_titel_weg) {
		if($header[$id]) {
			echo "<h1>".htmlentities($header[$id])."</h1>";
		} else {
			echo "<h1>".htmlentities($title[$id])."</h1>";
		}
	}

	# Content includen
	
    include($include);

#echo "</div>\n";


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

echo "<div id=\"terugnaarboven\" class=\"noprint\" style=\"display:none;\"><a href=\"#top\">".html("terugnaarboven")."</a></div>";


echo "</div>\n"; # afsluiten content


echo "<div style=\"clear: both;\"></div>\n";



echo "<div class=\"footer\" class=\"noprint\">";
echo "<p id=\"colofon\" class=\"noprint\">".html("handelsnaam")." - <a href=\"mailto:".htmlentities($vars["websiteinfo"]["email"][$vars["website"]])."\">".htmlentities($vars["websiteinfo"]["email"][$vars["website"]])."</a> - ".html("telefoonnummer_colofon"). "</p>";


if($voorkant_cms and !$_GET["cmsuit"] and $interneinfo) {
	echo "<div id=\"interneinfo_rechts\" class=\"noprint\">";
	echo $interneinfo;
	echo "</div>"; # interneinfo_rechts
}
echo "\n</div><!-- END #wrapper -->\n";

# Zorgen dat zoekenboek_overlay naar beneden schuift i.v.m. "laatst bekeken"-button
if($vars["zoekenboek_overlay_doorschuiven"]) {
	echo "<style type=\"text/css\"><!--\n#zoekenboek_overlay {\ntop:".(264+$vars["zoekenboek_overlay_doorschuiven"])."px;\n}\n--></style>\n";
}

# Ajaxloader in het midden van de pagina
echo "<div id=\"ajaxloader_page\"><img src=\"".$vars["path"]."pic/ajax-loader-large2.gif\" alt=\"loading...\" /></div>";

echo "<p id=\"fullwebsite\"><a href=\"/\" onclick=\"return switch_website('desktop');\">".txt("naar_desktopversie","mobile")."</a></p>";

# Balk met cookie-melding cookiebalk
if($opmaak->toon_cookiebalk()) {
	echo "<div class=\"clear\"></div>";
	echo "<div id=\"cookie_bottombar\" class=\"noprint\"><div id=\"cookie_bottombar_wrapper\"><div id=\"cookie_bottombar_text\">".html("cookiemelding","vars",array("h_1"=>"<a href=\"".$vars["path"]."privacy-statement.php\">","h_2"=>"</a>"))."</div><div id=\"cookie_bottombar_close\">sluiten</div></div></div>";
}

if(!$onMobile){
	echo "<div id=\"notification_bottombar\" class=\"noprint\"><div id=\"notification_bottombar_wrapper\"><div id=\"notification_bottombar_text\">".html("mobilenotification","vars",array("h_1"=>"<a onclick=\"return switch_website('desktop');\" href=\"".$vars["path"]."\">","h_2"=>"</a>"))."</div><div id=\"notification_bottombar_close\">sluiten</div></div></div>";    
}

# Balk met opvallende melding
if($vars["opvalmelding_tonen"] and (!$_COOKIE["opvalmelding_gelezen"] or $vars["lokale_testserver"])) {
	echo "<div id=\"opval_bottombar\" class=\"noprint\"><div id=\"opval_bottombar_wrapper\"><div id=\"opval_bottombar_text\">".nl2br(html("opvalmelding","vars",array("h_1"=>"<a href=\"mailto:".$vars["email"]."\">","h_2"=>"</a>","v_email"=>$vars["email"])))."</div><div id=\"opval_bottombar_close\">&nbsp;</div></div></div>";
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

if($vars["page_with_tabs"]) {
	# jQuery Address: t.b.v. correcte verwerking hashes in URL
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.address-1.5.min.js'";
}

$lazyLoadJs[] = "'".$vars["path"]."scripts/allfunctions.js?c=".@filemtime("scripts/allfunctions.js")."'";

# jQuery Chosen javascript
if($vars["jquery_chosen"]) {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/jquery.chosen.js?c=".@filemtime("scripts/mobile/jquery.chosen.js")."'";
}

if($vars["jquery_maphilight"]) {
	# Google Maps API
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.maphilight.min.js'";
}

if($page_id=="zoek-en-boek") {
	# jQuery noUiSlider
	$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/jquery.nouislider.min.js'";
}

if($page_id == "bestemmingen") {
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery.vmap.js?cache=".@filemtime("scripts/jquery.vmap.js")."'";
	$lazyLoadJs[] = "'".$vars["path"]."scripts/jquery-jvectormap-it-mill-en.js?cache=".@filemtime("scripts/jquery-jvectormap-it-mill-en.js")."'";
}

# Lazy load Fancybox
if($vars["jquery_fancybox"]) {
	$lazyLoadJs[] = "'".$vars["path"]."fancybox/jquery.fancybox-1.3.4.pack.js'";
}

# Javascript-functions
$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/functions.js?cache=".@filemtime("scripts/mobile/functions.js")."'";
$lazyLoadJs[] = "'".$vars["path"]."scripts/mobile/functions_italissima.js?cache=".@filemtime("scripts/mobile/functions_italissima.js")."'";

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

echo "</body>";
echo "</html>";

?>

