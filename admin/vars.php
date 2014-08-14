<?php

if(!$cron and !$css) {
	header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
}

// constants
define("WT_trackmailaddress","chaletmailbackup+track@gmail.com");
define("wt_mail_fromname","Chalet.nl");
define("wt_mail_from","info@chalet.nl");

// Netrom?
if($_SERVER["HTTP_HOST"]=="chalet-nl-dev.web.netromtest.ro") {
	define("netrom_testserver",true);
} else {
	define("netrom_testserver",false);
}

// is this the backup-server?
if($_SERVER["SERVER_ADDR"]=="149.210.172.200") {
	$vars["backup_server"] = true;
}

# diverse $vars
# zoekvolgorde mag maximaal 8 zijn
$vars["zoekvolgorde"]=array(1=>"Categorie 1 (hoogst)",2=>"Categorie 2 (hoger)",3=>"Categorie 3 (neutraal)",4=>"Categorie 4 (lager)",5=>"Categorie 5 (laagst)");
if($vars["backup_server"]) {
	// $vars["wt_htmlentities_utf8"] = true;
	$vars["wt_htmlentities_cp1252"] = true;
} else {
	$vars["wt_htmlentities_cp1252"]=true;
}
$vars["wt_mail_https_bcc"]=true;
$vars["salt"]="Ml3k39jj302kdpqQM";
$vars["wt_mysql_lost_nolog"]=true;


# ID, URL-array en Basehref bepalen
if($otherid) {
	$id=$otherid;
} else {
	$id=basename($id);
	if(!$id) {
		$id=ereg_replace("\.php$","",basename($_SERVER["PHP_SELF"]));
	}
	$id=basename($id);
}
if(ereg("(winter|zomer|summer)/",$_SERVER["REDIRECT_URL"])) {
	$url=ereg_replace("(winter|zomer|summer)/","",$_SERVER["REQUEST_URI"]);
	$url=explode("/",ereg_replace($_SERVER["SCRIPT_NAME"]."/","",$url));
} else {
	$url=explode("/",ereg_replace($_SERVER["SCRIPT_NAME"]."/","",$_SERVER["REQUEST_URI"]));
}
$vars["id"]=$id;
if(isset($url[(count($url)-1)]) and !$url[(count($url)-1)]) unset($url[(count($url)-1)]);

// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" and $_COOKIE["jeroen"]==1) {
	// $vars["bezoeker_is_jeroen"]=true;
	// $vars["wt_disable_error_handler"]=true;
// }

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or netrom_testserver) {
	$vars["lokale_testserver"]=true;

	if(netrom_testserver) {
		$vars["wt_disable_error_handler"]=true;
		$vars["lokale_testserver_mailadres"]="chalet@netrom.ro";
	}
}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$vars["webtastic"]=true;
}

$unixdir = dirname(dirname(__FILE__)) . "/";


// Mobile detect class
include_once($unixdir . "lib/mobile-detect/Mobile_Detect.php");
$detect = new Mobile_Detect();

if(isset($_COOKIE["siteVer"]) && ($_COOKIE["siteVer"] == "desktop")) {
	$isMobile = false;
} else {
	$isMobile = (($detect->isMobile() && !$detect->isTablet()) || (isset($_COOKIE["siteVer"]) && ($_COOKIE["siteVer"] == "mobile")));
}

if(isset($_GET["setmobile"])) {
	setcookie("siteVer","mobile",0,"/");
	$isMobile = true;
}

$onMobile = ($detect->isMobile() && !$detect->isTablet());

# Bestanden includen
if($isMobile) {
	if(!isset($allfuntions_version)) {
		require_once($unixdir."admin_mobile/allfunctions_mobile.php");
	}
	require($unixdir."admin_mobile/class.login_mobile.php");
	require($unixdir."admin_mobile/class.form_mobile.php");
} else {
	require_once($unixdir."admin/allfunctions.php");
	require($unixdir."admin/class.login.php");
	require($unixdir."admin/class.form.php");
}
require($unixdir."admin/class.cms2.php");
require($unixdir."admin/class.tablelist.php");
require($unixdir."admin/class.cms.layout.php");
require($unixdir."admin/vars_functions.php");


// is this the acceptation-testserver?
if(preg_match("@^test\.@",$_SERVER["HTTP_HOST"]) or preg_match("@/html_test/@",$_SERVER["SCRIPT_FILENAME"])) {
	$vars["acceptatie_testserver"]=true;

	if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["REMOTE_ADDR"]=="37.34.56.191") {
		// IP WebTastic
		$vars["lokale_testserver_mailadres"]="testform_ss@webtastic.nl";
		$vars["lokale_testserver_mailadres"]="jeroen_boschman@hotmail.com";
	} elseif($_SERVER["REMOTE_ADDR"]=="213.125.152.154") {
		// IP Chalet
		$vars["lokale_testserver_mailadres"]="bjorn@chalet.nl";
	} elseif($_SERVER["REMOTE_ADDR"]=="82.77.165.60" or $_SERVER["REMOTE_ADDR"]=="194.102.98.240") {
		$vars["lokale_testserver_mailadres"]="chalet@netrom.ro";
	} else {
		$vars["lokale_testserver_mailadres"]="testform_ss@webtastic.nl";
	}
}

#
# MySQL
#
require($unixdir."admin/vars_db.php");
if($vars["wt_htmlentities_utf8"]) {
	$mysqlsettings["charset"]="utf8";
}
require($unixdir."admin/class.mysql.php");


if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$db->query("SET CHARACTER SET 'latin1';");

	// log slow queries
	// $db->log_slow_queries="/tmp/slow_query.txt";
	// $db->log_slow_queries_time=.1;
}

if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	// // log slow queries
	// if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	// 	$db->log_slow_queries="/tmp/slow_query.txt";
	// } else {
	// 	$db->log_slow_queries="/var/www/chalet.nl/slow_query.txt";
	// }
	// $db->log_slow_queries_time=0;
}

// autoloaden van classes
function __autoload($classname) {
	global $vars, $unixdir;
	if (file_exists($unixdir."admin/siteclass/siteclass.".$classname . ".php")) {
		require_once $unixdir."admin/siteclass/siteclass.".$classname . ".php";
		return true;
	} else {
		if($classname!="MYPDF") {

			$debug=@debug_backtrace();

			if ( is_array( $debug ) ) {
				$filename=$debug[0]["file"];
				$linenumber=$debug[0]["line"];
			}
			trigger_error("_WT_FILENAME_".$filename."_WT_FILENAME__WT_LINENUMBER_".$linenumber."_WT_LINENUMBER_class ".$classname." kan niet worden geladen",E_USER_NOTICE);

		}
		return false;
	}
}
spl_autoload_register('__autoload');

#
# jquery/fancybox
#
$vars["jquery_fancybox"]=true;


#
# Tracker / advertenties
#
$vars["ads"]=array(1=>"Google AdWords algemeen",2=>"Google AdWords Oostenrijk",3=>"Google AdWords Frankrijk",4=>"Google AdWords Zwitserland",5=>"Google AdWords Snowboard",6=>"Google AdWords Aanbiedingen",7=>"Google AdWords Les Arcs",8=>"Google AdWords Banner 468x60",9=>"Tourploeg-banner",10=>"Tourploeg-maillink",11=>"Mailingmanager",12=>"Snowplaza",20=>"TradeTracker",30=>"Cleafs",31=>"Zoover",32=>"Vakantiereiswijzer",33=>"Sneeuwhoogte.nl");
$vars["ads_controle"]=array(9=>"TDF",12=>"KWX",31=>"KSQ",32=>"PWL"); # voeg toe aan URL (bijvoorbeeld): ?chad=KWX12
// $vars["ads_referermail"]=array(9=>"jeroen@webtastic.nl",12=>"j.fokke@snowplaza.nl");
$vars["ads_referermail"]=array(9=>"jeroen@webtastic.nl");
if(!$cron and !$cronmap and !$css and !$geen_tracker_cookie and !$_GET["nocache"] and !preg_match("/Googlebot/",$_SERVER["HTTP_USER_AGENT"])) {
	include($unixdir."admin/trackercookie.php");
}

#
# Plaats- en accommodatie-pagina zonder afsluitende "/" reloaden
#
// uitgezet 23-01-2013 : gaf eindeloze redirect
// if(($id=="plaats" or $id=="accommodatie" or $id=="skigebied" or $id=="land") and substr($_SERVER["PHP_SELF"],-1)<>"/") {
// 	header("Location: ".$_SERVER["PHP_SELF"]."/".($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : ""),true,301);
// 	exit;
// }


if($boeking_bepaalt_taal and $_GET["bid"]) {
	# Taal bepalen indien $boeking_bepaalt_taal
	$db->query("SELECT taal FROM boeking WHERE boeking_id='".addslashes($_GET["bid"])."';");
	if($db->next_record()) {
		$gegevens["stap1"]["taal"]=$db->f("taal");
		if($gegevens["stap1"]["taal"]<>"nl") $vars["ttv"]="_".$gegevens["stap1"]["taal"];
	}
}

# Testsite bepalen
if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$vars["testsite"]=file_get_contents("/home/webtastic/html/chalet/tmp/testsite.txt");
}

if(preg_match("/87\.250/",$_SERVER["HTTP_HOST"])) {
	# bezoek op alleen IP-adres doorsturen naar juiste hostname
	header("Location: https://www.chalet.nl/");
	exit;
}

#
# Websitetype en seizoentype bepalen
#
require($unixdir."admin/vars_websitetype.php");

# Cookiebalk tonen?
$vars["cookiemelding_tonen"]=true;

# Opvalmelding tonen
$vars["opvalmelding_tonen"]=false;

#
# Land-instellingen
#
if($vars["taal"]=="en") {
	setlocale(LC_TIME,"en_UK.ISO8859-1");
	setlocale(LC_MONETARY,"en_UK.ISO8859-1");
} else {
#	setlocale(LC_TIME,"nl_NL.ISO_8859-1");
	setlocale(LC_TIME,'nl_NL.ISO8859-1');
	setlocale(LC_MONETARY,"nl_NL.ISO8859-1");
}

require($unixdir."content/_teksten_intern.php");
require($unixdir."content/_teksten.php");

$vars["path"]=$path;
$vars["unixdir"]=$unixdir;

#
# Interne info (voor testsysteem)
#
if($vars["bezoeker_is_jeroen"] or $vars["testsite"]) {
	$interneinfo="Testaccommodaties:<ul>";
	if($vars["seizoentype"]==1) {
		$interneinfo.="<li><a href=\"".$vars["path"]."accommodatie/F240/\">Le Clos du Pr&eacute;</a></li>";
		$interneinfo.="<li><a href=\"".$vars["path"]."accommodatie/F251/\">Verzameltype</a></li>";
		$interneinfo.="<li><a href=\"".$vars["path"]."accommodatie/O1038/\">Bauernhaus</a></li>";
	}
	$interneinfo.="</ul>";
}

# Leveranciers waarbij het mogelijk is nieuwe accommodaties te importeren (levcode+naam)
# Maisons Vacances uitgezet op verzoek van Barteld (27-11-2012)
if($vars["lokale_testserver"] or $vars["acceptatie_testserver"]) {
	$vars["xmlnewimport_leveranciers"]=array(131=>"Posarelli Villas",421=>"Interhome",35=>"Direkt Holidays", 245=>"Alpin Rentals Kaprun");
} else {
	$vars["xmlnewimport_leveranciers"]=array(131=>"Posarelli Villas",421=>"Interhome",35=>"Direkt Holidays");
}


if($vars["websitetype"]==1 or $vars["websitetype"]==3 or $vars["websitetype"]==4 or $vars["websitetype"]==5 or $vars["websitetype"]==6 or $vars["websitetype"]==7 or $vars["websitetype"]==8 or $vars["websitetype"]==9) {
	# Alle sites behalve WSA.nl
	$menu=array("index","accommodaties","aanbiedingen","top10","anderesite","contact");
	if($vars["websitetype"]==7) {
		$vars["bordercolor"]=$activetabcolor;
	} else {
		$vars["bordercolor"]=$table;
	}
} else {
	# WSA.nl
	$vars["bordercolor"]="#BAC5D6";
	$menu["index"]="hoofdpagina";
	$menu["zoek-en-boek"]="Zoek en boek";
	$menu["aanbiedingen"]="aanbiedingen";
	if($vars["seizoentype"]==1) {
		$menu["weekendski"]="weekendski";
	} else {
		$menu["top10"]="top 10 per week";
	}
#	$menu["groepsreizen"]="groepsreizen";
	$menu["contact"]="contact";
}

#
# Wederverkoop-instellingen (inlogsysteem reisagenten)
#
if($vars["wederverkoop"]) {
	if($vars["website"]=="T" or $vars["website"]=="Y") {
		# Chalettour = noindex
		if($id<>"index" or $vars["website"]=="Y") {
			$robot_noindex=true;
		}
		$robot_nofollow=true;
	}

	if(!$mustlogin and !$css) {
		# Login-class voor reisbureaus (Chalettour)
		$login_rb = new Login;
		$login_rb->settings["logout_number"]=45;
		$login_rb->settings["adminmail"]="info@chalet.nl";
		$login_rb->settings["mail_wt"]=false;
		$login_rb->settings["db"]["tablename"]="reisbureau_user";
		$login_rb->settings["loginform_nobr"]=true;
		$login_rb->settings["extra_unsafe_cookie"]="rbli"; # ReisBureauLogIn
		if($vars["reisbureau_mustlogin"]) {
			$login_rb->settings["mustlogin"]=true;
		} else {
			$login_rb->settings["mustlogin"]=false;
		}
		if(!$vars["lokale_testserver"] and !$vars["acceptatie_testserver"] and !$vars["backup_server"] and !$vars["wwwtest"]) {
			$login_rb->settings["mustlogin_via_https"]=true;
		}

		$login_rb->settings["loginpage"]=$path."reisagent.php";
		$login_rb->settings["checkloginpage"]="reisagent.php";
		$login_rb->settings["tablecolor"]=$vars["bordercolor"];
		$login_rb->settings["name"]="reisbureau";
		$login_rb->settings["language"]=$vars["taal"];
		$login_rb->settings["message"]["login"]=txt("emailadres","reisbureau");
		$login_rb->settings["db"]["fieldusername"]="email";
		$login_rb->settings["message"]["wronglogin"]=nl2br(html("onjuistelogin","reisbureau",array("h_1"=>"<b style=\"color:red;\">","h_2"=>"</b>")));
		$login_rb->settings["sysop"]="<a href=\"mailto:".$vars["email"]."\">".$vars["websitenaam"]."</a>";
		if($vars["taal"]=="en") {
			$login_rb->settings["message"]["wronglogintemp"]="Account blocked for 1 hour";
		} else {
			$login_rb->settings["message"]["wronglogintemp"]="Account 1 uur lang geblokkeerd vanwege teveel onjuiste inlogpogingen";
		}
		$login_rb->settings["save_user_agent"]=true;
		$login_rb->settings["recheck_userdata"]=true;
		$login_rb->end_declaration();
		if($login_rb->logged_in) {
			$vars["chalettour_logged_in"]=true;
			$db->query("SELECT naam, beschikbaarheid_inzien, commissie_inzien, aanpassing_commissie, telefoonnummer FROM reisbureau WHERE reisbureau_id='".addslashes($login_rb->vars["reisbureau_id"])."' AND actief=1 AND websites LIKE '%".$vars["website"]."%';");
			if($db->next_record()) {
				$helemaalboven=wt_he($db->f("naam"))."&nbsp;&nbsp;<a href=\"".$vars["path"]."reisagent.php?logout=45\">".html("gebruikersnaamuitloggen","vars",array("v_gebruiker"=>wt_naam($login_rb->vars["voornaam"],$login_rb->vars["tussenvoegsel"],$login_rb->vars["achternaam"])))."</a>";
				if($id=="reisagent") {
					$helemaalboven.="&nbsp;&nbsp;".html("hoofdmenu_reisagent");
				} else {
					$helemaalboven.="&nbsp;&nbsp;<a href=\"".$path."reisagent.php\">".html("hoofdmenu_reisagent")."</a>";
				}

				$vars["chalettour_naam"]=$db->f("naam");
				$vars["chalettour_reisagentnaam"]=wt_naam($login_rb->vars["voornaam"],$login_rb->vars["tussenvoegsel"],$login_rb->vars["achternaam"]);
				$vars["chalettour_telefoonnummer"]=$db->f("telefoonnummer");

				$vars["wederverkoop_beschikbaarheid_inzien"]=$db->f("beschikbaarheid_inzien");

# Tijdelijk uitgezet (op verzoek van Bert). - 23 juni 2010 - weer aangezet in september 2010
#$vars["wederverkoop_beschikbaarheid_inzien"]=false;

				$vars["wederverkoop_commissie_inzien"]=$db->f("commissie_inzien");
				$vars["chalettour_aanpassing_commissie"]=$db->f("aanpassing_commissie");
			} else {
				$login_rb->logout();
				trigger_error("inlog reisagent (id ".$login_rb->user_id.") niet gelukt",E_USER_NOTICE);
#				wt_mail("jeroen@webtastic.nl","inlog reisbureau niet gelukt","inlog reisbureau (id ".$login_rb->vars["reisbureau_id"].") niet gelukt\n\n".$_SERVER["HTTP_HOST"]);
				header("Location: ".$path."reisagent.php?nietactief=1");
				exit;
			}
		}
	}
}


#
# Inlogsysteem leveranciers/eigenaren
#
if($vars["leverancier_mustlogin"]) {
	if(!$mustlogin and !$css) {
		# Login-class voor leveranciers
		$login_lev = new Login;
		$login_lev->settings["logout_number"]=145;
		$login_lev->settings["adminmail"]="info@chalet.nl";
		$login_lev->settings["mail_wt"]=false;
		$login_lev->settings["db"]["tablename"]="leverancier";
		$login_lev->settings["db"]["fielduserid"]="leverancier_id";
		$login_lev->settings["loginform_nobr"]=true;
		$login_lev->settings["extra_unsafe_cookie"]="levli"; # ReisBureauLogIn
		$login_lev->settings["mustlogin"]=true;
		$login_lev->settings["salt"]=$vars["salt"];

		if(!$vars["lokale_testserver"] and !$vars["acceptatie_testserver"] and !$vars["backup_server"] and !$vars["wwwtest"]) {
			$login_lev->settings["mustlogin_via_https"]=true;
		}

		$login_lev->settings["loginpage"]=$path."lev_login.php";
		$login_lev->settings["checkloginpage"]="lev_login.php";
		$login_lev->settings["tablecolor"]=$vars["bordercolor"];
		$login_lev->settings["name"]="leverancier";
		$login_lev->settings["language"]=$vars["taal"];
		$login_lev->settings["message"]["login"]=txt("emailadres","lev_login");
		$login_lev->settings["db"]["fieldusername"]="email_contract";
		$login_lev->settings["db"]["where"]="inlog_toegestaan=1";
		$login_lev->settings["message"]["wronglogin"]=nl2br(html("onjuistelogin","lev_login",array("h_1"=>"<b style=\"color:red;\">","h_2"=>"</b>")));
		$login_lev->settings["sysop"]="<a href=\"mailto:".$vars["email"]."\">".$vars["websitenaam"]."</a>";
		if($vars["taal"]=="en") {
			$login_lev->settings["message"]["wronglogintemp"]="Account blocked for 1 hour";
		} else {
			$login_lev->settings["message"]["wronglogintemp"]="Account 1 uur lang geblokkeerd vanwege teveel onjuiste inlogpogingen";
		}
		$login_lev->settings["save_user_agent"]=true;
		$login_lev->settings["recheck_userdata"]=true;
		$login_lev->end_declaration();

		if($login_lev->logged_in) {

			if($login_lev->vars["inlog_taal"]=="en") {
				# zorgen voor koptekst in de juiste taal
				$txta["nl"]["title_lev_login"]=$txta["en"]["title_lev_login"];
			}

		}
	}
}

# 213.125.152.154 = kantoor Ziggo 1
# 213.125.152.155 = kantoor Ziggo 2
# 213.125.152.156 = kantoor Ziggo 3
# 213.125.152.157 = kantoor Ziggo 4
# 213.125.152.158 = kantoor Ziggo 5
# 83.163.123.209  = Bert thuis
# 31.223.173.113  = WebTastic 1
# 37.34.56.191    = WebTastic 2
# 172.16.1.10     = t.b.v. testserver
# 172.16.1.35     = t.b.v. testserver (laptop)
# 127.0.0.1	      = t.b.v. testserver (Miguel)
# 62.195.99.8     = Selma thuis (i.v.m. beenbreuk - 6 juni 2013)
#

$vars["vertrouwde_ips"]=array("213.125.152.154","213.125.152.155","213.125.152.156","213.125.152.157","213.125.152.158","83.163.123.209","31.223.173.113","37.34.56.191","62.195.99.8","172.16.1.10","172.16.1.35","127.0.0.1");

// backup-server only available for vertrouwde_ips
if($vars["backup_server"]) {
	if(!in_array($_SERVER["REMOTE_ADDR"], $vars["vertrouwde_ips"])) {
		$www_url = preg_replace("@www2@", "www", "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
		header("Location: ".$www_url);
		exit;
	}
}

// test of new server-serup only available for vertrouwde_ips
if($_SERVER["HTTP_HOST"]=="wwwtest.chalet.nl") {
	$vars["wwwtest"] = true;
	if(!in_array($_SERVER["REMOTE_ADDR"], $vars["vertrouwde_ips"])) {
		$www_url = preg_replace("@wwwtest@", "www", "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
		header("Location: ".$www_url);
		exit;
	}
}

# Geldigheidsduur intern FLC-cookie verlengen
if($_COOKIE["flc"]==substr(md5($_SERVER["REMOTE_ADDR"]."XhjL"),0,8) and $_GET["logout"]<>1) {

	if(!$vars["lokale_testserver"] and !$vars["acceptatie_testserver"] and !$vars["backup_server"] and !$vars["wwwtest"]) {
		ini_set("session.cookie_secure",1);
	}

	$voorkant_cms=true;
	// $vars["annverzekering_mogelijk"]=1;
	// $vars["reisverzekering_mogelijk"]=1;
	if(in_array($_SERVER["REMOTE_ADDR"],$vars["vertrouwde_ips"])) {
		$cookie_expire=mktime(3,0,0,date("m"),date("d"),date("Y")+1);
	} else {
		$cookie_expire=0;
	}
	setcookie("flc",substr(md5($_SERVER["REMOTE_ADDR"]."XhjL"),0,8),$cookie_expire,"/");
}




unset($menu);
if($vars["websitetype"]==7) {
	#
	# Italissima
	#
	$menu["index"]=txt("menutitle_index");
	$menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$menu["bestemmingen"]=txt("menutitle_bestemmingen");
	$menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	if($vars["taal"]=="nl") {
		$menu["blog"]=txt("menutitle_blog");
	}
	$menu["vraag-ons-advies"]=txt("menutitle_vraag-ons-advies");
	$menu["contact"]=txt("menutitle_contact");

	$submenu["inloggen"]=txt("submenutitle_inloggen");
	if($vars["nieuwsbrief_aanbieden"]) {
		$submenu["nieuwsbrief"]=txt("submenutitle_nieuwsbrief");
	}
	if($vars["website"]=="I") {
		$submenu["reisagent"]=txt("submenutitle_reisagent");
	}
	$submenu["wie-zijn-wij"]=txt("submenutitle_wiezijnwij");
	if($vars["website"]=="I") {
		$submenu["werkenbij"]=txt("submenutitle_werkenbij");
	}
	$submenu["favorieten"]=txt("submenutitle_favorieten");
	$submenu["verzekeringen"]=txt("submenutitle_verzekeringen");

	$mobile_menu["home"]=txt("menutitle_home");
	$mobile_menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$mobile_menu["bestemmingen"]=txt("menutitle_bestemmingen");
	$mobile_menu["bsys"]=txt("menutitle_bsys");
	$mobile_menu["favorieten"]=txt("submenutitle_favorieten");
	$mobile_menu["contact"]=txt("menutitle_contact");
	$mobile_menu["vraag-ons-advies"]=txt("menutitle_vraag-ons-advies");
	$mobile_menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	$mobile_menu["start-chat"]=txt("menutitle_start-chat");

} elseif($vars["seizoentype"]==2) {
	#
	# Zomerhuisje
	#
	$menu["index"]=txt("menutitle_index");
	$menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$menu["bestemmingen"]=txt("menutitle_bestemmingen");
	$menu["themas"]=txt("menutitle_themas");
	$menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	$menu["vraag-ons-advies"]=txt("menutitle_vraag-ons-advies");
	$menu["contact"]=txt("menutitle_contact");

	if($vars["wederverkoop"]) {
		if(!$vars["chalettour_logged_in"]) {
			$submenu["inloggen"]=txt("submenutitle_inloggen");
			$submenu["reisagent"]=txt("submenutitle_reisagent");
		}
	} else {
		$submenu["inloggen"]=txt("submenutitle_inloggen");
	}
	$submenu["wie-zijn-wij"]=txt("submenutitle_wiezijnwij");
	$submenu["favorieten"]=txt("submenutitle_favorieten");
	$submenu["algemenevoorwaarden"]=txt("submenutitle_algemenevoorwaarden");
	$submenu["verzekeringen"]=txt("submenutitle_verzekeringen");
	$submenu["chaletwinter"]=txt("submenutitle_chaletwinter");
} elseif($vars["websitetype"]==6) {
	#
	# Chalets in Vallandry
	#
	$menu["index"]=txt("menutitle_index");
	$menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$menu["chalets"]=txt("menutitle_chalets");
	$menu["omgeving"]=txt("menutitle_omgeving");
	$menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	$menu["kort-verblijf"]=txt("menutitle_kort-verblijf");
	$menu["contact"]=txt("menutitle_contact");

	$submenu["inloggen"]=txt("submenutitle_inloggen");
	$submenu["reisagent"]=txt("submenutitle_reisagenten");
	$submenu["huiseigenaren"]=txt("submenutitle_huiseigenaren");
	$submenu["wie-zijn-wij"]=txt("submenutitle_wiezijnwij");
	$submenu["algemenevoorwaarden"]=txt("submenutitle_algemenevoorwaarden");
	$submenu["verzekeringen"]=txt("submenutitle_verzekeringen");
} elseif($vars["websitetype"]==8) {
	#
	# SuperSki
	#
	$menu["index"]=txt("menutitle_index");
	$menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$menu["skigebieden"]=txt("menutitle_skigebieden");
	$menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	$menu["weekendski"]=txt("menutitle_weekendski");
	$menu["contact"]=txt("menutitle_contact");

	$submenu["inloggen"]=txt("submenutitle_inloggen");
	$submenu["wie-zijn-wij"]=txt("submenutitle_wiezijnwij");
	$submenu["favorieten"]=txt("submenutitle_favorieten");
	$submenu["verzekeringen"]=txt("submenutitle_verzekeringen");
} elseif($vars["websitetype"]==9) {
	#
	# Venturasol
	#
	$menu["index"]=txt("menutitle_index");
	$menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$menu["skigebieden"]=txt("menutitle_skigebieden");
	$menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	$menu["contact"]=txt("menutitle_contact");

	if($vars["wederverkoop"]) {
		if(!$vars["chalettour_logged_in"]) {
			$submenu["inloggen"]=txt("submenutitle_inloggen");
			$submenu["reisagent"]=txt("submenutitle_reisagent");
		}
	} else {
		$submenu["inloggen"]=txt("submenutitle_inloggen");
	}

	// $submenu["wie-zijn-wij"]=txt("submenutitle_wiezijnwij");
	$submenu["favorieten"]=txt("submenutitle_favorieten");
	$submenu["verzekeringen"]=txt("submenutitle_verzekeringen");
	$submenu["algemenevoorwaarden"]=txt("submenutitle_algemenevoorwaarden");
} else {
	#
	# Winter
	#
	$menu["index"]=txt("menutitle_index");
	$menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$menu["skigebieden"]=txt("menutitle_skigebieden");
	$menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	$menu["weekendski"]=txt("menutitle_weekendski");
	if($vars["websitetype"]<>1 and $vars["websitetype"]<>3 and $vars["websitetype"]<>7) {

	} else {
		$menu["vraag-ons-advies"]=txt("menutitle_vraag-ons-advies");
	}
	$menu["contact"]=txt("menutitle_contact");

    	$mobile_menu["home"]=txt("menutitle_home");
	$mobile_menu["zoek-en-boek"]=txt("menutitle_zoek-en-boek");
	$mobile_menu["bsys"]=txt("menutitle_bsys");
	$mobile_menu["favorieten"]=txt("submenutitle_favorieten");
	$mobile_menu["contact"]=txt("menutitle_contact");
	$mobile_menu["vraag-ons-advies"]=txt("menutitle_vraag-ons-advies");
	$mobile_menu["aanbiedingen"]=txt("menutitle_aanbiedingen");
	$mobile_menu["start-chat"]=txt("menutitle_start-chat");

	if($vars["wederverkoop"]) {
		if(!$vars["chalettour_logged_in"]) {
			$submenu["inloggen"]=txt("submenutitle_inloggen");
			$submenu["reisagent"]=txt("submenutitle_reisagent");
		}
	} else {
		$submenu["inloggen"]=txt("submenutitle_inloggen");
	}

	if($vars["nieuwsbrief_aanbieden"]) {
		$submenu["nieuwsbrief"]=txt("submenutitle_nieuwsbrief");
	}
	$submenu["favorieten"]=txt("submenutitle_favorieten");
	$submenu["wie-zijn-wij"]=txt("submenutitle_wiezijnwij");
	if($vars["website"]=="C") {
		$submenu["werkenbij"]=txt("submenutitle_werkenbij");
	}
	$submenu["verzekeringen"]=txt("submenutitle_verzekeringen");

	if($vars["website"]=="C" or $vars["website"]=="B") {
		$submenu["blog"]="blog";
	}

	$submenu["zomerhuisje"]=txt("submenutitle_zomerhuisje");
}

$submenu_url_zonder_punt_php=array("sitemap");

#
# Titles
#

$title["index"]=txt("title_index");
$title["skigebieden"]=txt("title_skigebieden");
$title["accommodaties"]=txt("title_zoekenboek");
$title["algemenevoorwaarden"]=txt("title_algemenevoorwaarden");
$title["werkwijze"]=txt("title_werkwijze");
$title["materiaalhuur"]=txt("title_materiaalhuur");
$title["aanbiedingen"]=txt("title_aanbiedingen").($_GET["d"] ? " - ".ucfirst(txt("aankomst")).": ".weekend_voluit($_GET["d"]) : "");
$title["contact"]=txt("title_contact");
#$title["groepsreizen"]=txt("title_groepsreizen");
$title["overigeinfo"]=txt("title_overigeinfo");
$title["top10"]=txt("title_top10").($_GET["d"] ? " - ".ucfirst(txt("aankomst")).": ".weekend_voluit($_GET["d"]) : "");
$title["nieuwsbrief"]=txt("title_nieuwsbrief");
$title["nieuwsbrief_uitschrijven"]=txt("title_uitschrijvennieuwsbrief");
if($_GET["o"]) {
	$title["beschikbaarheid"]=txt("title_optieaanvragen");
} else {
	$title["beschikbaarheid"]=txt("title_beschikbaarheid");
}
$title["boeken"]=txt("title_boeken");
$title["boeking_bevestigd"]=txt("title_boeking_bevestigd");
$title["bsys"]=txt("title_bsys");
$title["bsys_wijzigen"]=txt("title_bsys");
$title["bsys_selecteren"]=txt("title_selecteren");
$title["bsys_payments"]=txt("title_bsys");
$title["wachtwoord"]=txt("title_wachtwoord");
$title["inloggen"]=txt("title_inloggen");
$title["inloggen_geblokkeerd"]=txt("title_inloggen_geblokkeerd");
$title["verzekeringen"]=txt("title_verzekeringen");
if($vars["chalettour_logged_in"]) {
	$title["reisagent"]=txt("title_reisagent_ingelogd");
} else {
	$title["reisagent"]=txt("title_reisagent");
}
$title["reisagent_aanmelden"]=txt("title_reisagent_aanmelden");
$title["reisagent_nieuws"]=txt("title_reisagent_nieuws");

if($_GET["calculations"]==1) {
	$title["reisagent_overzicht"]=txt("title_reisagent_overzicht_calc");
} elseif($_GET["mijngeg"]==1) {
	$title["reisagent_overzicht"]=txt("title_reisagent_overzicht_mijngeg");
} elseif($_GET["fin"]==1) {
	$title["reisagent_overzicht"]=txt("title_reisagent_financieel");
} else {
	$title["reisagent_overzicht"]=txt("title_reisagent_overzicht");
}
$title["rebook"]=txt("title_rebook");
$title["veelgestelde-vragen"]=txt("title_veelgesteldevragen");
$title["themas"]=txt("title_themas");
$title["weekendski"]=txt("title_weekendski");
$title["404"]=txt("title_404");
$title["calc"]=txt("title_calc");
$title["saved"]=txt("title_saved");
$title["jubileum"]=txt("title_jubileum");
$title["persberichten"]=txt("title_persberichten");
$title["bestemmingen"]=txt("title_bestemmingen");
$title["enquete"]=txt("title_enquete");
$title["zoek-en-boek"]=txt("title_zoekenboek");
$title["sitemap"]=txt("title_sitemap");
$title["vraag-ons-advies"]=txt("title_vraagonsadvies");
// $title["ask-our-advice"]=txt("title_vraagonsadvies");

$title["xmlmanual"]="Manual XML export";

# TIJDELIJK
$title["reischeque"]="Welkom bij Zomerhuisje.nl!";
$title["smaak-van-italie"]="Zomerhuisje.nl op 'De smaak van Italië'";
if($vars["seizoentype"]==2) {
	$title["brochure"]="Zomerhuisje Magazine";
} else {
	$title["brochure"]="Wintersport Magazine";
}
$title["agentenactie"]="Chalet.nl Agentenactie";
$title["vroegboekkorting"]="Unieke aanbieding: 5% vroegboekkorting op je zomervakantie!";
$title["montegufoni"]="Lezersaanbieding Smaak van Italië";
$title["alpedhuzes"]="Alpe d'huZes 2013";
$title["privacy-statement"]=txt("title_privacy-statement");
$title["disclaimer"]=txt("title_disclaimer","",array("v_websitenaam"=>$vars["websitenaam"]));
$title["favorieten"]=txt("title_favorieten");

$title["lev_login"]=txt("title_lev_login");




# Nieuwe vormgeving/site
$title["vernieuwd"]="Voorproefje vernieuwde website Chalet.nl";
$title["wie-zijn-wij"]=txt("title_wiezijnwij");

# Vallandry
$title["chalets"]=txt("title_chalets");
$title["omgeving"]=txt("title_omgeving");
$title["kort-verblijf"]=txt("title_kort-verblijf");
$title["huiseigenaren"]=txt("title_huiseigenaren");

# Italissima
$title["blog"]=txt("title_blog");




# $vars - Variabelen declareren
$vars["actuele_vacature"]="multiple";

$vars["fotofabriek_code_na_enquete"]=false;

$vars["referentiekeuze"]=array(1=>txt("referentie_1","vars"),2=>txt("referentie_2","vars"),3=>txt("referentie_3","vars"),4=>txt("referentie_4","vars"),9=>txt("referentie_9","vars"),10=>txt("referentie_10","vars"),5=>txt("referentie_5","vars"),6=>txt("referentie_6","vars"),8=>txt("referentie_8","vars"),7=>txt("referentie_7","vars"));
$vars["referentiekeuze_mobile"]=array(1=>txt("referentie_1","vars"),11=>txt("referentie_11","vars"),12=>txt("referentie_12","vars"),13=>txt("referentie_13","vars"),4=>txt("referentie_4","vars"),3=>txt("referentie_3","vars"),5=>txt("referentie_5","vars"),6=>txt("referentie_6","vars"),8=>txt("referentie_8","vars"),7=>txt("referentie_7","vars"));

$vars["reserveringskosten"]=20;

$vars["boeken"]=array(1=>html("accommodatiegegevensopgeven","vars"),2=>html("gegevenshoofdboekeropgeven","vars"),3=>html("gegevensoverigepersonenopgeven","vars"),4=>html("optiesselecteren","vars"),5=>html("boekingbevestigen","vars"));
$vars["boeken_cms"]=array(1=>"Accommodatie, aantal personen of aankomstdatum wijzigen",2=>"Gegevens hoofdboeker wijzigen",3=>"Gegevens overige personen wijzigen",4=>"Opties wijzigen");
$vars["stappen_log"]=array(1=>"aantal personen en aankomstdatum ingevoerd",2=>"gegevens hoofdboeker ingevoerd",3=>"gegevens overige personen ingevoerd",4=>"opties geselecteerd",5=>"boeking bevestigd");
$vars["talen"]=array("nl"=>"Nederlands","en"=>"English","de"=>"Deutsch");
#$vars["begineinddagen"]=array(-7=>"-7 dagen",-6=>"-6 dagen",-5=>"-5 dagen",-4=>"-4 dagen",-3=>"-3 dagen",-2=>"-2 dagen",-1=>"-1 dag",0=>"geen aanpassing",1=>"+1 dag",2=>"+2 dagen",3=>"+3 dagen",4=>"+4 dagen",5=>"+5 dagen",6=>"+6 dagen",7=>"+7 dagen");
$vars["begineinddagen"]=array(-10=>"-10 dagen",-9=>"-9 dagen",-8=>"-8 dagen",-7=>"-7 dagen",-6=>"-6 dagen",-5=>"-5 dagen",-4=>"-4 dagen",-3=>"-3 dagen",-2=>"-2 dagen",-1=>"-1 dag",0=>"geen aanpassing",1=>"+1 dag",2=>"+2 dagen",3=>"+3 dagen",4=>"+4 dagen",5=>"+5 dagen",6=>"+6 dagen",7=>"+7 dagen",8=>"+8 dagen",9=>"+9 dagen",10=>"+10 dagen");
$vars["geenvoorkeur"]="-- ".txt("geenvoorkeur","vars")." --";
$vars["kwaliteit"]=array(1=>"*",2=>"* *",3=>"* * *",4=>"* * * *",5=>"* * * * *");
$vars["soortaccommodatie"]=array(1=>txt("chalet","vars"),2=>txt("appartement","vars"),3=>txt("hotel","vars"),4=>txt("chaletappartement","vars"),6=>txt("vakantiewoning","vars"),7=>txt("villa","vars"),8=>txt("kasteel","vars"),9=>txt("vakantiepark","vars"),10=>txt("agriturismo","vars"),11=>txt("domein","vars"),12=>txt("pension","vars"));
$vars["interhome_soortaccommodatie"] = array("h"=>1,"a"=>2,"d"=>6); // h=house; a=apartment; d=detached house
$vars["toonper"]=array(1=>"A : Prijs Arrangement (accommodatie + skipas)",2=>"B : Prijs Arrangement (pakket + toeslag onbezet bed)",3=>"C : Prijs Accommodatie");
$vars["toonper_beperktekeuze"]=array(1=>"A : Prijs Arrangement (accommodatie + skipas)",3=>"C : Prijs Accommodatie");
$vars["seizoentype_namen"]=array(1=>"winter",2=>"zomer");
$vars["sjabloon_velden"]=array("aflopen_allotment","korting_percentage","toeslag","korting_euro","vroegboekkorting_percentage","vroegboekkorting_euro","opslag_accommodatie","opslag_skipas","korting_arrangement_bed_percentage","toeslag_arrangement_euro","korting_arrangement_euro","toeslag_bed_euro","korting_bed_euro","vroegboekkorting_arrangement_percentage","vroegboekkorting_arrangement_euro","vroegboekkorting_bed_percentage","vroegboekkorting_bed_euro","opslag","c_korting_percentage","c_toeslag","c_korting_euro","c_vroegboekkorting_percentage","c_vroegboekkorting_euro","c_opslag_accommodatie","wederverkoop_opslag_euro","wederverkoop_opslag_percentage","wederverkoop_commissie_agent");
$vars["tarief_velden"]=array("beschikbaar", "blokkeren_wederverkoop", "bruto", "korting_percentage", "toeslag", "korting_euro", "vroegboekkorting_percentage", "vroegboekkorting_euro", "opslag_accommodatie", "opslag_skipas", "arrangementsprijs", "onbezet_bed", "korting_arrangement_bed_percentage", "toeslag_arrangement_euro", "korting_arrangement_euro", "toeslag_bed_euro", "korting_bed_euro", "vroegboekkorting_arrangement_percentage", "vroegboekkorting_arrangement_euro", "vroegboekkorting_bed_percentage", "vroegboekkorting_bed_euro", "opslag", "c_bruto", "c_korting_percentage", "c_toeslag", "c_korting_euro", "c_vroegboekkorting_percentage", "c_vroegboekkorting_euro", "c_opslag_accommodatie", "c_verkoop_afwijking", "c_verkoop_site", "voorraad_garantie", "voorraad_allotment", "voorraad_vervallen_allotment", "voorraad_optie_leverancier", "voorraad_xml", "voorraad_request", "voorraad_optie_klant", "voorraad_bijwerken", "wederverkoop_opslag_euro", "wederverkoop_opslag_percentage", "wederverkoop_commissie_agent", "wederverkoop_verkoopprijs", "aanbiedingskleur", "autoimportxmltarief", "blokkeerxml", "aflopen_allotment", "aanbieding_acc_percentage", "aanbieding_acc_euro", "aanbieding_skipas_percentage", "aanbieding_skipas_euro", "inkoopkorting_percentage", "inkoopkorting_euro", "verkoop_accommodatie", "toonexactekorting");

//Array with all tussenvoegsel available
$vars['availableTussenvoegsel']= array("de", "den", "ten", "van", "het", "der", "v/d", "v.d.", "vd");

#$vars["tarief_velden_nietopslaan"]=array("aanbieding_acc_percentage","aanbieding_acc_euro","aanbieding_skipas_percentage","aanbieding_skipas_euro");
$vars["tarief_datum_velden"]=array("vroegboekkorting_percentage_datum","vroegboekkorting_euro_datum","vroegboekkorting_arrangement_percentage_datum","vroegboekkorting_arrangement_euro_datum","vroegboekkorting_bed_percentage_datum","vroegboekkorting_bed_euro_datum","c_vroegboekkorting_euro_datum","c_vroegboekkorting_percentage_datum");
$vars["tarief_optie_velden"]=array("beschikbaar","verkoop","netto_ink","inkoop","korting","korting_euro","omzetbonus","wederverkoop_commissie_agent");
$vars["tarief_flex_velden"]=array("aankomstdag","minimum_aantal_nachten","bruto","korting_percentage","toeslag","korting_euro","opslag_accommodatie","verkoop_afwijking","verkoop_site","wederverkoop_commissie_agent","wederverkoop_verkoopprijs","beschikbaar","voorraad_garantie","voorraad_allotment","voorraad_vervallen_allotment","voorraad_optie_leverancier","voorraad_xml","voorraad_request","voorraad_optie_klant","voorraad_bijwerken");
$vars["tarief_flex_velden_omzetten"]=array("bruto"=>"c_bruto","korting_percentage"=>"c_korting_percentage","toeslag"=>"c_toeslag","korting_euro"=>"c_korting_euro","opslag_accommodatie"=>"c_opslag_accommodatie","verkoop_afwijking"=>"c_verkoop_afwijking","verkoop_site"=>"c_verkoop_site");
$vars["korting_tarief_velden"]=array("inkoopkorting_percentage","aanbieding_acc_percentage","aanbieding_skipas_percentage","inkoopkorting_euro","aanbieding_acc_euro","aanbieding_skipas_euro");
$vars["bijkomendekosten_velden"]=array("verkoop","netto_ink","inkoop","korting","korting_euro","omzetbonus");
$vars["sjabloon_skipas"]=array("bruto","netto_ink","korting","verkoopkorting","prijs","omzetbonus","wederverkoop_commissie_agent","netto");
$vars["aantalslaapkamers"]=array(0=>$vars["geenvoorkeur"],1=>txt("minimaal","accommodaties")." 1",2=>txt("minimaal","accommodaties")." 2",3=>txt("minimaal","accommodaties")." 3",4=>txt("minimaal","accommodaties")." 4",5=>txt("minimaal","accommodaties")." 5",6=>txt("minimaal","accommodaties")." 6",7=>txt("minimaal","accommodaties")." 7",8=>txt("minimaal","accommodaties")." 8",9=>txt("minimaal","accommodaties")." 9",10=>txt("minimaal","accommodaties")." 10");
#$vars["minpersonen"]=array("1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"14","16"=>"16","17"=>"16","18"=>"16","19"=>"18","20"=>"18");
#$vars["maxpersonen"]=array("1"=>"4","2"=>"6","3"=>"6","4"=>"8","5"=>"9","6"=>"10","7"=>"11","8"=>"12","9"=>"13","10"=>"14","11"=>"15","12"=>"16","13"=>"18","14"=>"19","15"=>"20","16"=>"25","17"=>"25","18"=>"28","19"=>"28","20"=>"50");
$vars["maxpersonen"]=array("1"=>"8","2"=>"12","3"=>"12","4"=>"16","5"=>"18","6"=>"20","7"=>"22","8"=>"24","9"=>"26","10"=>"28","11"=>"30","12"=>"32","13"=>"36","14"=>"38","15"=>"40","16"=>"50","17"=>"50","18"=>"50","19"=>"50","20"=>"50");
$vars["optimaalmaxpersonen"]=array("1"=>"4","2"=>"6","3"=>"6","4"=>"8","5"=>"9","6"=>"10","7"=>"11","8"=>"12","9"=>"13","10"=>"14","11"=>"15","12"=>"16","13"=>"18","14"=>"19","15"=>"20","16"=>"25","17"=>"25","18"=>"28","19"=>"28","20"=>"50");

$vars["minpersonen_boeking"]=array("1"=>"1","2"=>"1","3"=>"1","4"=>"2","5"=>"2","6"=>"3","7"=>"4","8"=>"4","9"=>"5","10"=>"6","11"=>"7","12"=>"7","13"=>"8","14"=>"8","15"=>"9","16"=>"10","17"=>"11","18"=>"12","19"=>"12","20"=>"13");
$vars["geslacht"]=array(1=>txt("man","vars"),2=>txt("vrouw","vars"));
$vars["verzendmethode_reisdocumenten"]=array(1=>txt("email","vars"),2=>txt("post","vars"));
$vars["verzendmethode_reisdocumenten_inclusief_nvt"]=array(1=>txt("email","vars"),2=>txt("post","vars"),3=>txt("nvt","vars"));

// blogcategorie Chalet.nl/be
$vars["blogcategorie"][1]=array(1=>"trends",2=>"bestemmingen",3=>"eten & drinken",4=>"skimateriaal & -kleding",5=>"wintersport-lifestyle",6=>"overig");

// blogcategorie Italissima
$vars["blogcategorie"][7]=array(1=>"eten & drinken",2=>"tradities & feestdagen",3=>"kunst & cultuur",4=>"mode & design",5=>"films",7=>"levensstijl",6=>"overig");


$vars["recaptcha_publickey"]="6LdyodYSAAAAAORWNxjHjtO7q76k38LP7eQpYzg9";
$vars["recaptcha_privatekey"]="6LdyodYSAAAAAIVrp5gAZBhBygsy1KObuX6XAM-W";

$vars["vertrekdagtypes_soorten"]=array(1=>"unieke afwijkdagen",3=>"zaterdag-zaterdag",2=>"zondag-zondag");

# Voucher-teksten
$vars["voucher_resnrlev"]=array("D"=>"Reservierung Nummer","E"=>"Reservation number","F"=>"Réservation accomm.","I"=>"Numero di prenotazione","N"=>"Res.nr. Leverancier","O"=>"Reservierung Nummer","Z"=>"Reservierung Nummer","CZ"=>"Reservierung Nummer","S"=>"Reserva Nº del proveedor");
$vars["voucher_bestemming"]=array("D"=>"Ort","E"=>"Destination","F"=>"Destination","I"=>"Città","N"=>"Bestemming","O"=>"Ort","Z"=>"Ort","CZ"=>"Ort","S"=>"Destino");
$vars["voucher_accommodatie"]=array("D"=>"Wohnung","E"=>"Accommodation","F"=>"Hébergement","I"=>"Proprietà","N"=>"Accommodatie","O"=>"Wohnung","Z"=>"Wohnung","CZ"=>"Wohnung","S"=>"Alojamiento");
$vars["voucher_accommodatiecode"]=array("D"=>"Code akkommodation","E"=>"Code accommodation","F"=>"Code logement","I"=>"Codice Proprietà","N"=>"Code accommodatie","O"=>"Code akkommodation","Z"=>"Code akkommodation","CZ"=>"Code akkommodation","S"=>"Código del alojamiento");
$vars["voucher_eerstedag"]=array("D"=>"Anfang","E"=>"First day","F"=>"Premier jour","I"=>"Arrivo","N"=>"Aanvangsdatum","O"=>"Anfang","Z"=>"Anfang","CZ"=>"Anfang","S"=>"Llegada");
$vars["voucher_laatstedag"]=array("D"=>"Ende","E"=>"Last day","F"=>"Fin jour","I"=>"Partenza","N"=>"Einddatum","O"=>"Ende","Z"=>"Ende","CZ"=>"Ende","S"=>"Salida");
$vars["voucher_aantal"]=array("D"=>"Nummer","E"=>"Number","F"=>"Nombre","I"=>"Importo","N"=>"Aantal","O"=>"Nummer","Z"=>"Nummer","CZ"=>"Nummer","S"=>"Número");
$vars["voucher_deelnemers"]=array("D"=>"Personen","E"=>"Persons","F"=>"Personnes","I"=>"Persone","N"=>"Deelnemers","O"=>"Personen","Z"=>"Personen","CZ"=>"Personen","S"=>"Participantes");
$vars["voucher_persoon"]=array("D"=>"Person","E"=>"person","F"=>"personne","I"=>"persone","N"=>"persoon","O"=>"Person","Z"=>"Person","CZ"=>"Person","S"=>"persona");
$vars["voucher_personen"]=array("D"=>"Personen","E"=>"persons","F"=>"personnes","I"=>"persone","N"=>"personen","O"=>"Personen","Z"=>"Personen","CZ"=>"Personen","S"=>"personas");
$vars["voucher_reservering"]=array("D"=>"Reservierung","E"=>"Reservation","F"=>"Réservation","I"=>"Prenotazione","N"=>"Reservering","O"=>"Reservierung","Z"=>"Reservierung","CZ"=>"Reservierung","S"=>"Reserva");
$vars["voucher_plaats"]=array("D"=>"Ort","E"=>"Resort","F"=>"Destination","I"=>"Paese","N"=>"Plaats","O"=>"Ort","Z"=>"Ort","CZ"=>"Ort","S"=>"Localidad");
$vars["voucher_omschrijving"]=array("D"=>"[OMSCHRIJVING]","E"=>"[OMSCHRIJVING]","F"=>"[OMSCHRIJVING]","I"=>"[OMSCHRIJVING]","N"=>"Omschrijving","O"=>"[OMSCHRIJVING]","Z"=>"[OMSCHRIJVING]","CZ"=>"[OMSCHRIJVING]","S"=>"Descripción");
$vars["voucher_type"]=array("D"=>"Typ","E"=>"Type","F"=>"Type","I"=>"Tipo","N"=>"Type","O"=>"Typ","Z"=>"Typ","CZ"=>"Typ","S"=>"Tipo");
$vars["voucher_prijs"]=array("D"=>"Preis","E"=>"Price","F"=>"Prix","I"=>"Prezzo","N"=>"Prijs","O"=>"Preis","Z"=>"Preis","CZ"=>"Preis","S"=>"Precio");
$vars["voucher_gratis"]=array("D"=>"Frei","E"=>"Free","F"=>"Gratuit","I"=>"Gratuito","N"=>"Gratis","O"=>"Frei","Z"=>"Frei","CZ"=>"Frei","S"=>"Gratis");

$vars["themakleurcode"]=array(1=>"#99ccff",2=>"#003366",3=>"#cbd328",4=>"#636f07",5=>"#cc0000",6=>"#990099",7=>"#ff9900",8=>"#5f227b",9=>"#00ccff");
$vars["themakleurcode_licht"]=array(1=>"#e0f0ff",2=>"#b2c1d1",3=>"#eff2be",4=>"#d0d4b4",5=>"#f5cccc",6=>"#ebcceb",7=>"#ffebcc",8=>"#dfd3e5",9=>"#ccf5ff");


#$vars["voucherstatus"]=array(0=>"nog niet geprint",1=>"geprint",2=>"verzonden per post",3=>"verzonden per mail");
$vars["voucherstatus"]=array(0=>"nog niet gecontroleerd",4=>"gecontroleerd + aangemaakt",5=>"opnieuw controleren",1=>"geprint",2=>"verzonden per post",11=>"geprint+verzonden per post",3=>"verzonden per mail",6=>"aanvullende vouchers nog niet gecontroleerd",7=>"aanvullende vouchers gecontroleerd + aangemaakt",8=>"aanvullende vouchers geprint",9=>"aanvullende vouchers verzonden per post",12=>"aanvullende vouchers geprint+verzonden per post",10=>"aanvullende vouchers verzonden per mail",13=>"OK; wordt niet verzonden");
$vars["voucherstatus_zonderwijzigingen"]=array(0=>"nog niet gecontroleerd",4=>"gecontroleerd + aangemaakt",5=>"opnieuw controleren",1=>"geprint",2=>"verzonden per post",11=>"geprint+verzonden per post",3=>"verzonden per mail",13=>"OK; wordt niet verzonden");
$vars["voucherstatus_nawijzigingen"]=array(6=>"aanvullende vouchers nog niet gecontroleerd",7=>"aanvullende vouchers gecontroleerd + aangemaakt",8=>"aanvullende vouchers geprint",9=>"aanvullende vouchers verzonden per post",12=>"aanvullende vouchers geprint+verzonden per post",10=>"aanvullende vouchers verzonden per mail",13=>"OK; wordt niet verzonden");
#$vars["voucherstatus"]=array_merge($vars["voucherstatus_zonderwijzigingen"],$vars["voucherstatus_nawijzigingen"]);
#echo wt_dump($vars["voucherstatus"]);

#$vars["voucher_"]=array("D"=>"","E"=>"","F"=>"","I"=>"","N"=>"","O"=>"","Z"=>"");
#$vars["voucher_"]=array("D"=>"","E"=>"","F"=>"","I"=>"","N"=>"","O"=>"","Z"=>"");


# Vertrekinfo
$vars["vertrekinfo_soortbeheer"]=array(1=>txt("receptie","vertrekinfo"),2=>txt("agentschap","vertrekinfo"),3=>txt("eigenaar","vertrekinfo"),4=>txt("contactpersoon","vertrekinfo"));
$vars["vertrekinfo_soortbeheer_sjabloontekst"]=array(1=>txt("dereceptie","vertrekinfo"),2=>txt("hetagentschap","vertrekinfo"),3=>txt("deeigenaar","vertrekinfo"),4=>txt("decontactpersoon","vertrekinfo"));
$vars["vertrekinfo_soortadres"]=array(1=>"adres accommodatie",2=>"sleuteladres");



# Welke taal in welk land?
$vars["landcodes"]=array("D"=>"de","E"=>"en","F"=>"fr","I"=>"","N"=>"nl","O"=>"de","Z"=>"de","CZ"=>"de");

$vars["bestelmailfax_taal"]=array("D"=>"Duits","E"=>"Engels","N"=>"Nederlands");

$vars["bestelmailfax_beste"]=array("D"=>"Sehr Geehrte(r)","E"=>"Dear","N"=>"Beste");
$vars["bestelmailfax_hierbijwillenwe"]=array("D"=>"Wir möchten gerne nachfolgende Fixreservierung machen","E"=>"We would like to make the next definitive reservation","N"=>"Hierbij willen wij onderstaande accommodatie definitief boeken");
$vars["bestelmailfax_dezeacchebbenwij"]=array("D"=>"Zur Ihrer Information: Wir haben es","E"=>"This accommodation is","N"=>"Deze accommodatie hebben wij");
$vars["bestelmailfax_soort"]["D"]=array(1=>"auf Anfrage",2=>"in Kontingent",3=>"in Garantie",4=>"in Option");
$vars["bestelmailfax_soort"]["E"]=array(1=>"on request",2=>"in allotment",3=>"in guarantee",4=>"in option");
$vars["bestelmailfax_soort"]["N"]=array(1=>"op aanvraag",2=>"in allotment",3=>"in garantie",4=>"in optie");
#$vars["mailfaxboeken_soort"]=array(1=>"request",2=>"allotment",3=>"guarantee",4=>"option");
#$vars["bestelmailfax_opaanvraag"]=array("D"=>"auf Anfrage","E"=>"on request","N"=>"op aanvraag");
#$vars["bestelmailfax_inallotment"]=array("D"=>"in Kontingent","E"=>"in allotment","N"=>"in allotment");
#$vars["bestelmailfax_ingarantie"]=array("D"=>"in Garantie","E"=>"in guarantee","N"=>"in garantie");
#$vars["bestelmailfax_inoptie"]=array("D"=>"in Option","E"=>"an option","N"=>"in optie");
$vars["bestelmailfax_tot"]=array("D"=>"bis","E"=>"until","N"=>"tot");
$vars["bestelmailfax_klantnaam"]=array("D"=>"Kunde Name","E"=>"Clients name","N"=>"Klantnaam");
$vars["bestelmailfax_aankomst"]=array("D"=>"Anreise","E"=>"Date of Arrival","N"=>"Aankomst");
$vars["bestelmailfax_verblijfsduur"]=array("D"=>"Aufentheltsdauern","E"=>"Staying time","N"=>"Verblijfsduur");
$vars["bestelmailfax_nachten"]=array("D"=>"Nachten","E"=>"nights","N"=>"nachten");
$vars["bestelmailfax_plaats"]=array("D"=>"Ort","E"=>"Resort","N"=>"Plaats");
$vars["bestelmailfax_accommodatie"]=array("D"=>"Akkommodation","E"=>"Accommodation","N"=>"Accommodatie");
$vars["bestelmailfax_type"]=array("D"=>"Type","E"=>"Type","N"=>"Type");
$vars["bestelmailfax_maxcapaciteit"]=array("D"=>"Max. Kapazität","E"=>"Max. capacity","N"=>"Max. capaciteit");
$vars["bestelmailfax_persoon"]=array("D"=>"Personen","E"=>"person","N"=>"persoon");
$vars["bestelmailfax_personen"]=array("D"=>"Personen","E"=>"persons","N"=>"personen");
$vars["bestelmailfax_prijs"]=array("D"=>"Mietpreis","E"=>"Price","N"=>"Prijs");
$vars["bestelmailfax_korting"]=array("D"=>"Kommission","E"=>"Commission","N"=>"Commissie");
#$vars["bestelmailfax_vroegboekkorting"]=array("D"=>"Spezial Rabatt","E"=>"Early Bird","N"=>"Vroegboekkorting");
$vars["bestelmailfax_vroegboekkorting"]=array("D"=>"Spezial Rabatt","E"=>"Special Reduction","N"=>"Extra korting");
$vars["bestelmailfax_extra"]=array("D"=>"Extra","E"=>"Extra","N"=>"Extra");
$vars["bestelmailfax_graagpermailoffaxbevestigen"]=array("D"=>"Wir bitten Sie uns umgehend eine Buchungsbestätigung zu schicken per Mail. Vielen dank im voraus.","E"=>"Please confirm this reservation as soon as possible by email.","N"=>"We vragen u deze reservering zo snel mogelijk per e-mail aan ons te bevestigen.");
$vars["bestelmailfax_metvriendelijkegroet"]=array("D"=>"Mit freundlichen Grüßen","E"=>"Thanks in advance","N"=>"Met vriendelijke groet");
$vars["bestelmailfax_pagina"]=array("D"=>"Seite","E"=>"Page","N"=>"Pagina");

$vars["bestelmailfax_reservering"]=array("D"=>"Reservierung","E"=>"Reservation","N"=>"Reservering");
$vars["bestelmailfax_klant"]=array("D"=>"Kunde","E"=>"client","N"=>"klant");
#$vars["bestelmailfax_"]=array("D"=>"","E"=>"","N"=>"");
#$vars["bestelmailfax_"]=array("D"=>"","E"=>"","N"=>"");

$vars["flex_max_aantalnachten"]=28;
$vars["roominglist_site_benaming"]=array(1=>"Chalet.nl",2=>"Chalet.nl / Zomerhuisje.nl",3=>"Italissima");


#
# Kenmerken Winter
#
$vars["kenmerken_type_1"]=array(1=>"catering mogelijk",2=>"aan de piste",3=>"sauna (privé)",4=>"zwembad (privé)",5=>"goed voor kids",6=>"grote groepen",7=>"prijsbewust",8=>"alleen het allerbeste (topselectie)",9=>"winter-wellness",10=>"open haard/houtkachel",11=>"huisdieren toegestaan",12=>"allergievrij",13=>"verhuur zondag t/m zondag",14=>"huisje voor 2",15=>"bijzonder",16=>"wasmachine",17=>"balkon",18=>"terras of balkon",19=>"huisdieren NIET toegestaan",20=>"internetverbinding",21=>"charmant chalet",22=>"internet via Wi-Fi",23=>"Jacuzzi/bubbelbad");
$vars["kenmerken_accommodatie_1"]=array(1=>"catering mogelijk",2=>"aan de piste",3=>"sauna (privé)",4=>"zwembad (privé)",5=>"goed voor kids",6=>"grote groepen",7=>"prijsbewust",8=>"alleen het allerbeste (topselectie)",9=>"winter-wellness",10=>"sauna",11=>"zwembad",12=>"open haard/houtkachel",13=>"huisdieren toegestaan",14=>"allergievrij",15=>"verhuur zondag t/m zondag",16=>"bijzonder",17=>"wasmachine",18=>"balkon",19=>"terras of balkon",20=>"huisdieren NIET toegestaan",21=>"internetverbinding",22=>"charmant chalet",23=>"internet via Wi-Fi",24=>"Jacuzzi/bubbelbad");
$vars["kenmerken_plaats_1"]=array(1=>"kindvriendelijk",2=>"prijsbewust",4=>"winter-wellness",5=>"meer dan alleen skiën",6=>"een 10 voor après-ski",7=>"altijd sneeuw",8=>"voor beginnende wintersporters",9=>"voor fanatieke wintersporters",10=>"snowboardfun",11=>"wandelen",12=>"langlaufen",13=>"charmant skidorp",14=>"super ski stations"); # 3 is nog leeg
$vars["kenmerken_skigebied_1"]=array(1=>"kindvriendelijk",2=>"prijsbewust",3=>"winter-wellness",4=>"meer dan alleen skiën",5=>"altijd sneeuw",6=>"voor beginnende wintersporters",7=>"voor fanatieke wintersporters",8=>"snowboardfun",9=>"wandelen",10=>"langlaufen");

#
# Kenmerken Zomer
#
$vars["kenmerken_type_2"]=array(
	1=>"huisje voor 2",
	2=>"los, vrijstaand huis",
	4=>"zwembad (privé)",
	5=>"goed voor kids",
	6=>"voor groepen",
	11=>"huisdieren toegestaan",
	14=>"niet vrijstaand/geschakeld/appartement",
	15=>"vrijstaand huis (met meerdere huizen)",
	16=>"bijzonder",
	17=>"tuin/terras (privé)",
	18=>"wasmachine",
	19=>"balkon (privé)",
	20=>"tuin/terras of balkon",
	22=>"internetverbinding",
	23=>"omheinde tuin",
	24=>"internet via Wi-Fi",
	25=>"verdieping: op begane grond",
	26=>"verdieping: niet op begane grond",
	27=>"airconditioning",
	28=>"barbecue (gemeenschappelijk)",
	29=>"barbecue (privé)",
	30=>"kinderbed",
	31=>"jacuzzi",
	32=>"kinderstoel",
	33=>"vaatwasser"
);
# # verwijderd: 3=>"sauna (privé)",
# # verwijderd: 7=>"prijsbewust",
# # verwijderd: 8=>"alleen het allerbeste (topselectie)",
# # verwijderd: 9=>"vleugje wellness",
# # verwijderd: 10=>"open haard/houtkachel",
# # verwijderd: 12=>"allergievrij",
# # verwijderd: 13=>"verhuur zondag t/m zondag",
# # verwijderd: 21=>"huisdieren NIET toegestaan",



$vars["kenmerken_accommodatie_2"]=array(
	1=>"actief in de bergen",
	2=>"golfbaan",
	4=>"zwembad (privé)",
	5=>"goed voor kids",
	6=>"voor groepen",
	8=>"alleen het allerbeste (topselectie)",
	9=>"wellness-faciliteiten",
	10=>"sauna",
	11=>"zwembad",
	13=>"huisdieren toegestaan",
	16=>"stekje aan het water",
	18=>"huisje voor 2",
	19=>"tennisbaan (bij accommodaties)",
	22=>"los, vrijstaand huis",
	23=>"niet vrijstaand/geschakeld/appartement",
	24=>"vakantiepark",
	25=>"te gast op de boerderij",
	26=>"speeltoestellen",
	27=>"vrijstaand huis (met meerdere huizen)",
	28=>"bijzonder",
	29=>"tennisbaan (in directe omgeving)",
	30=>"tuin/terras (privé)",
	31=>"paardrijden (in directe omgeving)",
	32=>"wasmachine",
	33=>"balkon (privé)",
	34=>"tuin/terras of balkon",
	36=>"internetverbinding",
	37=>"wijndomein",
	38=>"omheinde tuin",
	39=>"centrum op loopafstand (max. 1km)",
	40=>"restaurant op domein",
	41=>"agriturismo",
	42=>"internet via Wi-Fi",
	43=>"verdieping: op begane grond",
	44=>"verdieping: niet op begane grond",
	45=>"airconditioning",
	46=>"barbecue (gemeenschappelijk)",
	47=>"barbecue (privé)",
	48=>"kinderbed",
	49=>"jacuzzi",
	50=>"kinderstoel",
	51=>"vaatwasser",
	52=>"activiteiten (bijv. workshops)",
	53=>"agriturismo a) klein: 1 - 10 app.",
	54=>"agriturismo b) middelgroot: 11 - 20 app.",
	55=>"agriturismo c) groot: meer dan 20 app.",
	56=>"animatie",
	57=>"boerderijdieren",
	58=>"fietsen",
	59=>"kindvriendelijk zwembad",
	60=>"ontbijt mogelijk",
);
# # verwijderd: 3=>"sauna (privé)",
# # verwijderd: 7=>"prijsbewust",
# # verwijderd: 12=>"open haard/houtkachel",
# # verwijderd: 14=>"allergievrij",
# # verwijderd: 15=>"verhuur zondag t/m zondag",
# # verwijderd: 17=>"levensgenieters"
# # verwijderd: 20=>"wijnstreek",
# # verwijderd: 21=>"citytrip",
# # verwijderd: 35=>"huisdieren NIET toegestaan",


$vars["kenmerken_plaats_2"]=array(
	1=>"kindvriendelijk",
	5=>"actief in de bergen",
	6=>"golfbaan",
	7=>"stekje aan het water",
	9=>"wijnstreek",
	10=>"citytrip",
	11=>"grote stad in de omgeving",
	12=>"dichtbij zee",
	13=>"dichtbij een meer",
);
# # verwijderd: 8=>"levensgenieters"
# # verwijderd: 2=>"prijsbewust"
# # verwijderd: 4=>"vleugje wellness"


$vars["kenmerken_skigebied_2"]=array(1=>"kindvriendelijk",2=>"prijsbewust",3=>"wellness-faciliteiten",4=>"actief in de bergen",6=>"wijnstreek");
# # verwijderd: 5=>"levensgenieters"

# Zoekformulier-kenmerken winter
# 1 = aan de piste
$vars["zoekkenmerken_type_1"]=array(1=>2);
$vars["zoekkenmerken_accommodatie_1"]=array(1=>2);

asort($vars["kenmerken_type_1"]);
asort($vars["kenmerken_type_2"]);
asort($vars["kenmerken_accommodatie_1"]);
asort($vars["kenmerken_accommodatie_2"]);
asort($vars["kenmerken_plaats_1"]);
asort($vars["kenmerken_plaats_2"]);
asort($vars["kenmerken_skigebied_1"]);
asort($vars["kenmerken_skigebied_2"]);

// Docdata payment type
$vars["boeking_betaling_type"]=array(1=>"bank-overschrijving", 2=>"uitbetaling door Docdata", 3=>"verrekening Docdata-betaling/overschrijving", 4=>"Docdata-betaling (iDEAL)", 5=>"Docdata-betaling (creditcard)", 6=>"Docdata-uitbetaling (Mister Cash)", 7=>"betalingsverschil");

# Mailtjes bij optieaanvragen-systeem

# Klant ziet van optie af (leverancier mailen)
$vars["optiemail_leverancier_niet_subject"]=array(
	"D"=>"DUITS ONTBREEKT NOG - Cancellation option [AANKOMSTDATUM] [ACCOMMODATIE]",
	"E"=>"Cancellation option [AANKOMSTDATUM] [ACCOMMODATIE]",
	"N"=>"Annulering optie [AANKOMSTDATUM] [ACCOMMODATIE]"
);
$vars["optiemail_leverancier_niet_body"]=array(
	"D"=>"DUITS ONTBREEKT NOG - Dear [CONTACTPERSOON_LEVERANCIER],\n\nUnfortuantely, we have to cancel the option mentioned below:\n\nAccommodation: [ACCOMMODATIE]\nName guest: [NAAMKLANT]\nArrival date: [AANKOMSTDATUM]\nDuration: [AANTAL_NACHTEN] nachten\n\nKind regards,\n[NAAM_MEDEWERKER]\n\nChalet.nl\nWipmolenlaan 3\n3447 GJ WOERDEN\nKvK: 30209634\nTel: +31 (0)348 - 43 46 49\nFax: +31 (0)348 - 69 07 52\nEmail: [EMAIL_MEDEWERKER]",
	"E"=>"Dear [CONTACTPERSOON_LEVERANCIER],\n\nUnfortuantely, we have to cancel the option mentioned below:\n\nAccommodation: [ACCOMMODATIE]\nName guest: [NAAMKLANT]\nArrival date: [AANKOMSTDATUM]\nDuration: [AANTAL_NACHTEN] nachten\n\nKind regards,\n[NAAM_MEDEWERKER]\n\nChalet.nl\nWipmolenlaan 3\n3447 GJ WOERDEN\nKvK: 30209634\nTel: +31 (0)348 - 43 46 49\nFax: +31 (0)348 - 69 07 52\nEmail: [EMAIL_MEDEWERKER]",
	"N"=>"Beste [CONTACTPERSOON_LEVERANCIER],\n\nHelaas mag de onderstaande optie komen te vervallen:\n\nAccommodatie: [ACCOMMODATIE]\nNaam klant: [NAAMKLANT]\nAankomstdatum: [AANKOMSTDATUM]\nDuur: [AANTAL_NACHTEN] nachten\n\nMet vriendelijke groet,\n[NAAM_MEDEWERKER]\n\nChalet.nl\nWipmolenlaan 3\n3447 GJ WOERDEN\nKvK: 30209634\nTel: +31 (0)348 - 43 46 49\nFax: +31 (0)348 - 69 07 52\nEmail: [EMAIL_MEDEWERKER]"
);

$vars["optiemail_leverancier_doorgeven_subject"]=array(
	"D"=>"DUITS ONTBREEKT NOG - Option request [AANKOMSTDATUM] [ACCOMMODATIE]",
	"E"=>"Option request [AANKOMSTDATUM] [ACCOMMODATIE]",
	"N"=>"Optie-aanvraag [AANKOMSTDATUM] [ACCOMMODATIE]"
);

$vars["optiemail_leverancier_doorgeven_body"]=array(
	"D"=>"DUITS ONTBREEKT NOG - Dear [CONTACTPERSOON_LEVERANCIER],\n\nCan we please have an option on the accommodation noted below:\n\nAccommodation: [ACCOMMODATIE]\nName guest: [NAAMKLANT]\nArrival date: [AANKOMSTDATUM]\nDuration: [AANTAL_NACHTEN] nachten\n\nCan you please let me know if we can have this option and until when we can have this?\n\nThanks in advance for your early reply.\n\nKind regards,\n[NAAM_MEDEWERKER]\n\nChalet.nl\nWipmolenlaan 3\n3447 GJ WOERDEN\nKvK: 30209634\nTel: +31 (0)348 - 43 46 49\nFax: +31 (0)348 - 69 07 52\nEmail: [EMAIL_MEDEWERKER]",
	"E"=>"Dear [CONTACTPERSOON_LEVERANCIER],\n\nCan we please have an option on the accommodation noted below:\n\nAccommodation: [ACCOMMODATIE]\nName guest: [NAAMKLANT]\nArrival date: [AANKOMSTDATUM]\nDuration: [AANTAL_NACHTEN] nachten\n\nCan you please let me know if we can have this option and until when we can have this?\n\nThanks in advance for your early reply.\n\nKind regards,\n[NAAM_MEDEWERKER]\n\nChalet.nl\nWipmolenlaan 3\n3447 GJ WOERDEN\nKvK: 30209634\nTel: +31 (0)348 - 43 46 49\nFax: +31 (0)348 - 69 07 52\nEmail: [EMAIL_MEDEWERKER]",
	"N"=>"Beste [CONTACTPERSOON_LEVERANCIER],\n\nZou je ons een optie kunnen geven op onderstaande accommodatie:\n\nAccommodatie: [ACCOMMODATIE]\nNaam klant: [NAAMKLANT]\nAankomstdatum: [AANKOMSTDATUM]\nDuur: [AANTAL_NACHTEN] nachten\n\nZou je me deze optie kunnen bevestigen en vertellen tot wanneer deze genoteerd staat?\n\nAlvast bedankt voor je spoedige reactie.\n\nMet vriendelijke groet,\n[NAAM_MEDEWERKER]\n\nChalet.nl\nWipmolenlaan 3\n3447 GJ WOERDEN\nKvK: 30209634\nTel: +31 (0)348 - 43 46 49\nFax: +31 (0)348 - 69 07 52\nEmail: [EMAIL_MEDEWERKER]"
);


$vars["aankomst_plusmin"]=array(-6=>"-6 (zondag)",-5=>"-5 (maandag)",-4=>"-4 (dinsdag)",-3=>"-3 (woensdag)",-2=>"-2 (donderdag)",-1=>"-1 (vrijdag)",0=>"0 (zaterdag)",1=>"+1 (zondag)",2=>"+2 (maandag)",3=>"+3 (dinsdag)",4=>"+4 (woensdag)",5=>"+5 (donderdag)",6=>"+6 (vrijdag)");
$vars["vertrek_plusmin"]=array(-6=>"-6 (zondag)",-5=>"-5 (maandag)",-4=>"-4 (dinsdag)",-3=>"-3 (woensdag)",-2=>"-2 (donderdag)",-1=>"-1 (vrijdag)",0=>"0 (zaterdag)",1=>"+1 (zondag)",2=>"+2 (maandag)",3=>"+3 (dinsdag)",4=>"+4 (woensdag)",5=>"+5 (donderdag)",6=>"+6 (vrijdag)",7=>"+7 (zaterdag)",8=>"+8 (zondag)",9=>"+9 (maandag)",10=>"+10 (dinsdag)",11=>"+11 (woensdag)",12=>"+12 (donderdag)",13=>"+13 (vrijdag)",14=>"+14 (zaterdag)");


$vars["aanbieding_soort"]=array(1=>txt("gewoneaanbieding","vars"),2=>txt("lastminute","vars"));
$vars["aanbieding_soort_cms"]=array(1=>"Aanbieding met toelichting voor bezoeker",2=>"Aanbieding zonder toelichting (= gewoon korting)");
$vars["bedrag_soort"]=array(1=>"Korting in euro's",2=>"Kortingspercentage",3=>"Exact bedrag",4=>"Geen bedrag (handmatige verwerking)");

# vars reisbureaus
$vars["commissie_hooglaag"]=array(2=>"hoog",1=>"laag");


# Vars m.b.t. de boekhouding
$vars["landcodes_boekhouding_lang"]=array(1=>"België",2=>"Duitsland",3=>"Frankrijk",11=>"Italië",4=>"Luxemburg",5=>"Nederland",6=>"Oostenrijk",7=>"Spanje",8=>"Verenigd Koninkrijk",10=>"Verenigde Staten",9=>"Zwitserland",12=>" - Overig (binnen Europa)");
$vars["landcodes_boekhouding_kort"]=array(1=>"BE",2=>"DE",3=>"FR",4=>"LU",5=>"NL",6=>"AT",7=>"ES",8=>"GB",9=>"CH",10=>"US",11=>"IT",12=>"OV");
$vars["landcodes_boekhouding_btwcode"]=array(1=>"15",2=>"15",3=>"15",4=>"15",5=>"10",6=>"15",7=>"15",8=>"15",9=>"15",10=>"0",11=>"15",12=>"15");

#$vars["grootboekrekeningen"]=array(1=>"Garantie accommodaties",2=>"Accommodaties",3=>"Arrangement",4=>"Skipassen",5=>"Materiaalhuur",6=>"Vervoer",7=>"Overige",8=>"Annuleringsverzekering",9=>"Reisverzekering");
#$vars["grootboekrekeningen"]=array(2=>"Accommodaties",3=>"Arrangement",4=>"Skipassen",5=>"Materiaalhuur",6=>"Vervoer",7=>"Overige",8=>"Annuleringsverzekering",9=>"Reisverzekering");


$vars["annverz_soorten"]=array(1=>txt("annverz_standaard","boeken"),2=>txt("annverz_garantie","boeken"),3=>txt("annverz_allrisk","boeken"),4=>txt("annverz_garantie_waarneming","boeken"));
$vars["annverz_soorten_kort"]=array(1=>txt("annverz_standaard_kort","boeken"),2=>txt("annverz_garantie_kort","boeken"),3=>txt("annverz_allrisk_kort","boeken"),4=>txt("annverz_garantie_waarneming_kort","boeken"));

#$vars["optieaanvragen_status"]=array(1=>"ingevuld door klant",2=>"Ingevuld door Chalet.nl",3=>"verstuurd aan leverancier",4=>"goedgekeurd door leverancier",5=>"afgekeurd door leverancier",6=>"teruggekoppeld aan klant",7=>"vervallen",8=>"klant heeft accommodatie geboekt");
#$vars["optieaanvragen_status_menu"]=array(1=>"Ingevuld door klant",2=>"Ingevuld door Chalet.nl",3=>"Verstuurd aan leverancier",4=>"Goedgekeurd",5=>"Afgekeurd",6=>"Teruggekoppeld aan klant",7=>"Vervallen",8=>"Geboekt");

$vars["optieaanvragen_status"]=array(1=>"aangevraagd",2=>"aangevraagd bij leverancier",3=>"uitstaand",4=>"afgewezen",5=>"vervallen",6=>"geboekt");
$vars["optieaanvragen_status_menu"]=array(1=>"aangevraagd",2=>"aangevraagd leverancier",3=>"uitstaand",4=>"afgewezen",5=>"vervallen",6=>"geboekt");

$vars["optieaanvragen_herkomst"]=array(1=>"garantie",2=>"allotment",3=>"optie bij leverancier",4=>"request");
$vars["optieaanvragen_ingevuldvia"]=array(1=>"Optie-aanvraag-formulier door klant",2=>"Beschikbaarheidsaanvraag-formulier door klant",3=>"Medewerker Chalet.nl");

$vars["factuurnummer_prefix"]=array(2006=>"67",2007=>"78",2008=>"89",2009=>"90",2010=>"101",2011=>"112",2012=>"123",2013=>"134",2014=>"145",2015=>"156",2016=>"167",2017=>"178",2018=>"189",2019=>"190",2020=>"201");

# Aanbetaling-dagen
$vars["aanbetaling1_dagennaboeken"]=10;
$vars["totale_reissom_dagenvooraankomst"]=42;
$vars["jquery_url"]="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js";

#$vars["jquery_url"]="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js";

if(($id == "toonaccommodatie") && ($isMobile)){
        # oude jquery ui-versie nodig voor zomer-tarieventabel in IE9
        $vars["jqueryui_url"]="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js";
}else {
       $vars["jqueryui_url"]="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js";
}

if($boeking_wijzigen) {
	# Login-class klanten
	$login = new Login;
	$login->settings["logout_number"]=21;
	$login->settings["adminmail"]="info@chalet.nl";
	$login->settings["mail_wt"]=false;
	$login->settings["db"]["tablename"]="boekinguser";
	if($vars["websitetype"]==8) {
		# dunnere border bij SuperSki
		$login->settings["tableborderwidth"]=1;
	}
	if($vars["wederverkoop"]) {
		if($login_rb->logged_in) {
			$login->settings["mustlogin"]=false;
		} else {
			$login->settings["mustlogin"]=true;
		}
	} else {
		$login->settings["mustlogin"]=true;
	}
	if(!$vars["lokale_testserver"] and !$vars["acceptatie_testserver"] and !$vars["backup_server"] and !$vars["wwwtest"]) {
		$login->settings["mustlogin_via_https"]=true;
	}
	$login->settings["loginpage"]=$path.txt("menu_inloggen").".php";
	$login->settings["checkloginpage"]=txt("menu_inloggen").".php";
	$login->settings["tablecolor"]=$vars["bordercolor"];
	$login->settings["name"]="chaletboekingwijzigen";
	$login->settings["language"]=$vars["taal"];
	$login->settings["loginform_nobr"]=true;
	$login->settings["sysop"]="<a href=\"mailto:".$vars["email"]."\">".$vars["websitenaam"]."</a>";
	if($vars["taal"]=="en") {
		$login->settings["message"]["wronglogintemp"]="Account blocked: <a href=\"".$path."inloggen_geblokkeerd.php?blocktime=\">read more</a>";
	} else {
		$login->settings["message"]["wronglogintemp"]="Account geblokkeerd: <a href=\"".$path."inloggen_geblokkeerd.php?blocktime=\">meer informatie</a>";
	}

	if($vars["taal"]=="en") {
		$login->settings["message"]["login"]="Email address";
	} else {
		$login->settings["message"]["login"]="E-mailadres";
	}
	$login->settings["save_user_agent"]=true;
	$login->end_declaration();

	if($vars["huidige_user_uitloggen"] and $login->logged_in) {
		$login->logout();
	}

	# Om welke wederverkoop-boeking gaat het?
	if($vars["wederverkoop"] and $login_rb->logged_in and $_GET["bid"]) {

		if($login_rb->vars["inzicht_boekingen"]) {
			$reisbureau_user_id_inquery="SELECT user_id FROM reisbureau_user WHERE reisbureau_id=(SELECT reisbureau_id FROM reisbureau_user WHERE user_id='".addslashes($login_rb->user_id)."')";
		} else {
			$reisbureau_user_id_inquery=$login_rb->user_id;
		}
		$db->query("SELECT DISTINCT b.boeking_id, b.website FROM boeking b, boeking_persoon bp WHERE b.boeking_id='".addslashes($_GET["bid"])."' AND b.boeking_id=bp.boeking_id AND b.bevestigdatum IS NOT NULL AND b.tonen_in_mijn_boeking=1 AND bp.persoonnummer=1 AND b.reisbureau_user_id IN (".$reisbureau_user_id_inquery.") AND b.tonen_in_mijn_boeking=1;");
		if($db->next_record()) {
			if($boekingid_inquery) $boekingid_inquery.=",".$db->f("boeking_id"); else $boekingid_inquery=$db->f("boeking_id");
			if($db->f("website")==$vars["website"]) {
				$login->logged_in=true;
				$vars["chalettour_loggedin_overzichtboekingen"]=true;
			} else {
				if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
					$vars["websites_basehref"][$db->f("website")]=ereg_replace("https://www\.chalettour\.nl/","http://ss.postvak.net/chalet/",$vars["websites_basehref"][$db->f("website")]);
				}
				header("Location: ".$vars["websites_basehref"][$db->f("website")]."bsys.php?".$_SERVER["QUERY_STRING"]);
				exit;
			}
		} else {
			unset($_GET["bid"]);
			header("Location: ".$path);
			exit;
		}
	} else {
		if($login->logged_in) {
			$db->query("SELECT DISTINCT b.boeking_id FROM boeking b, boeking_persoon bp WHERE b.boeking_id=bp.boeking_id AND b.website='".$vars["website"]."' AND bp.email='".addslashes($login->username)."' AND b.bevestigdatum IS NOT NULL AND b.tonen_in_mijn_boeking=1;");
			if($db->num_rows()) {
				while($db->next_record()) {
					$wijzigen[$db->f("boeking_id")]=true;
					$temp_boekingid=$db->f("boeking_id");
					if($boekingid_inquery) $boekingid_inquery.=",".$db->f("boeking_id"); else $boekingid_inquery=$db->f("boeking_id");
					if($_GET["bid"]) {
						if($db->f("boeking_id")==$_GET["bid"]) $geldige_login=true;
					} else {
						$geldige_login=true;
					}
				}
				if($geldige_login) {
					if(@count($wijzigen)>1 and !$_GET["bid"] and $id<>"bsys_selecteren") {
						header("Location: ".$path."bsys_selecteren.php?back=".$_SERVER["REQUEST_URI"]);
						exit;
					} elseif(@count($wijzigen)==1) {
						$_GET["bid"]=$temp_boekingid;
					}
				} else {
					header("Location: ".$path);
					exit;
				}
			} else {
				unset($_GET["bid"]);
			}
		}
	}
} elseif($mustlogin or $cron) {
	require($unixdir."admin/vars_cms.php");
} else {
	# Login-class voor Chalet-medewerkers (andere pagina's dan CMS-pagina's)
	if($voorkant_cms) {
		$login = new Login;
		$login->settings["logout_number"]=1;
		$login->settings["adminmail"]=$vars["email"];
		$login->settings["db"]["tablename"]="user";
		if($_COOKIE["flc"]==substr(md5($_SERVER["REMOTE_ADDR"]."XhjL"),0,8) and $_GET["logout"]<>1 and !$css and $_SERVER["REMOTE_ADDR"]<>"31.223.173.113" and !$vars["lokale_testserver"]) {
			$login->settings["mustlogin"]=true;
		} else {
			$login->settings["mustlogin"]=false;
		}
		$login->settings["loginpage"]=$vars["path"]."cms.php";
		$login->settings["checkloginpage"]="cms.php";
		$login->settings["tablecolor"]="#0D3E88";
		$login->settings["name"]="chalet";
		$login->settings["language"]="nl";
		$login->settings["mailpassword_attempt"]=false;
		$login->settings["sysop"]="<a href=\"http://www.webtastic.nl/6.html\">WebTastic</a>";
		$login->settings["save_user_agent"]=true;
		$login->settings["salt"]=$vars["salt"];
		$login->settings["loginblocktime"]=600; # 10 minuten geblokkeerd

		$login->end_declaration();

		# zorgen dat cookie ook gevuld is indien er van buiten kantoor is ingelogd ($login->settings["settings"]["rememberpassword"] staat dan uit)
		if($login->user_id and !$_COOKIE["loginuser"]["chalet"]) {
			$_COOKIE["loginuser"]["chalet"]=$login->user_id;
		}

		if(!$helemaalboven and !$_GET["cmsuit"]) {
			if(defined("wt_server_name")) {
				$helemaalboven .= "server: ".wt_server_name." - ";
			}
			$helemaalboven .= "Intern ingelogd: ".wt_he($login->vars["voornaam"])." - <a href=\"".($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? $vars["path"] : "http://".($vars["acceptatie_testserver"] ? "test" : ($vars["backup_server"] ? "www2" : "www")).".chalet.nl/")."cms.php\" target=\"_blank\">cms</a> - <a href=\"".$vars["path"]."cms.php?logout=1\">uitloggen</a>";
		}

		if($vars["chalettour_logged_in"] and $login->logged_in and !$css) {
			$login_rb->logout();
			trigger_error("dubbele login (CMS en reisagent)",E_USER_NOTICE);
			echo "<div style=\"font-family:Verdana;font-size:0.8em;width:600px;border: 1px solid #000000;background-color: yellow;padding: 5px;margin-top: 10px;margin-bottom: 10px;\">Het is niet toegestaan tegelijk in het CMS als in het reisagentensysteem ingelogd te zijn.<br><br>Je bent nu uitgelogd uit het reisagenten-systeem.<br><br><a href=\"".wt_he($_SERVER["REQUEST_URI"])."\">Ga verder &gt;</a></div";
			exit;
		}
	}

	#
	# Voorkant website
	#
	# Aankomstdata-vars vullen (voor zoekformulier)
	if($vars["websitetype"]==6) {
		$temp_seizoentype="1,2";
	} else {
		$temp_seizoentype=$vars["seizoentype"];
	}
	$db->query("SELECT naam, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind, seizoen_id, tonen FROM seizoen WHERE type IN (".$temp_seizoentype.") AND tonen".($voorkant_cms ? ">=" : "=")."3 ORDER BY begin, eind;");
	if($db->num_rows()) {
		if($id=="accommodaties" or $id=="zoek-en-boek" or $id=="thema" or $id=="weekendski" or $id=="land" or $id=="chalets") $vars["aankomstdatum_weekend"][0]=$vars["geenvoorkeur"];
		while($db->next_record()) {
			# Aankomstdatum-array vullen
			$timeteller=$db->f("begin");
			while($timeteller<=$db->f("eind")) {
				$vars["aankomstdatum"][$timeteller]=datum("DAG D MAAND JJJJ",$timeteller,$vars["taal"]);
				$vars["aankomstdatum_kort"][$timeteller]=datum("D MAAND JJJJ",$timeteller,$vars["taal"]);

				# aankomstdatum_weekend vullen (alleen indien niet langer dan 8 dagen geleden)
				if($timeteller>=time()-691200) {
					$vars["aankomstdatum_weekend"][$timeteller]=txt("weekend","vars")." ".date("j",$timeteller)." ".datum("MAAND JJJJ",$timeteller,$vars["taal"]);
					$vars["aankomstdatum_weekend_afkorting"][$timeteller]=txt("weekend","vars")." ".date("j",$timeteller)." ".datum("MND JJJJ",$timeteller,$vars["taal"]);
				}

				$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
			}

			// kijken of seizoen intern anders gebruikt wordt dan extern
			if($db->f("tonen")==4 and $voorkant_cms) {
				$vars["seizoen_alleen_intern"].=" en ".$db->f("naam");
			}
		}
		@ksort($vars["aankomstdatum"]);
		@ksort($vars["aankomstdatum_kort"]);
		@ksort($vars["aankomstdatum_weekend"]);
	}
}

if(($boeking_wijzigen and $login->logged_in) or (ereg("^[0-9]+",$_COOKIE["CHALET"]["boeking"]["boekingid"]) and $id<>"boeken")) {
	unset($rechtsboven);
	if($boeking_wijzigen and $login->logged_in and !$vars["chalettour_loggedin_overzichtboekingen"]) {
		$rechtsboven.="<span class=\"x-small\">";
		if(@count($wijzigen)>1 and $id<>"bsys_selecteren") $rechtsboven.="<a href=\"bsys_selecteren.php\">".html("andereboeking","bsys")."</a> - ";
		$rechtsboven.="<a href=\"".$path.txt("menu_inloggen").".php?logout=21\">".html("gebruikersnaamuitloggen","vars",array("v_gebruiker"=>$login->username))."</a>";
		$rechtsboven.="</span>";
	} elseif(ereg("^([0-9]+)_([a-z0-9]{8})$",$_COOKIE["CHALET"]["boeking"]["boekingid"],$regs)) {
		if($regs[2]==boeking_veiligheid($regs[1])) {
			$db->query("SELECT type_id FROM boeking WHERE boeking_id='".addslashes($_COOKIE["CHALET"]["boeking"]["boekingid"])."' AND bevestigdatum IS NULL;");
			if($db->next_record()) {
				$verder=accinfo($db->f("type_id"));
				if(($id=="toonaccommodatie" and $typeid<>$db->f("type_id")) or ($id<>"boeken" and $id<>"toonaccommodatie")) {
					if($verder["tonen"] and !$voorkant_cms) {
						$rechtsboven.="<span class=\"x-small\"><a href=\"".$path."boeken.php?bfbid=".$regs[1]."\">".html("gaverdermetboeken","vars")." ";
						if(!$vars["wederverkoop"]) {
							$rechtsboven.=wt_he(ucfirst($verder["soortaccommodatie"])." ".$verder["accommodatie"]);
						}
						$rechtsboven.=" &gt;</a></span>";
					}
				}
			}
		}
	}
}

# Favorieten en Laatst bekeken accommodaties uit database halen
if($_COOKIE["sch"]) {
	$db->query("SELECT COUNT(b.type_id) AS aantal FROM bezoeker_favoriet b, view_accommodatie v WHERE b.bezoeker_id='".addslashes($_COOKIE["sch"])."' AND b.type_id=v.type_id AND v.websites LIKE '%".$vars["website"]."%' AND v.atonen=1 AND v.ttonen=1 AND v.archief=0;");
	if($db->next_record()) {
		$vars["bezoeker_aantal_favorieten"]=$db->f("aantal");
	}
	if(!$boeking_wijzigen) {
		$db->query("SELECT last_acc FROM bezoeker WHERE bezoeker_id='".addslashes($_COOKIE["sch"])."' and last_acc IS NOT NULL;");
		if($db->next_record()) {
			$vars["last_acc_bezoeker"]=$db->f("last_acc");
			$last_acc_bezoeker=@split(",",$db->f("last_acc"));
			@reset($last_acc_bezoeker);
			while(list($key,$value)=@each($last_acc_bezoeker)) {
				if($value>0) {
					if($last_acc_inquery) $last_acc_inquery.=",".addslashes($value); else $last_acc_inquery=addslashes($value);
				}
			}
			if($last_acc_inquery) {
				$db->query("SELECT begincode, type_id, accommodatie_id, naam, tnaam".$vars["ttv"]." AS tnaam, optimaalaantalpersonen, maxaantalpersonen, plaats, skigebied, land FROM view_accommodatie WHERE type_id IN (".$last_acc_inquery.") AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' ORDER BY FIND_IN_SET(type_id,'".$last_acc_inquery."') DESC;");
				while($db->next_record()) {
					$last_acc[$db->f("type_id")]["begincode"]=$db->f("begincode");
					$last_acc[$db->f("type_id")]["naam"]=$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." ".txt("pers").")";
					$last_acc[$db->f("type_id")]["plaats"]=$db->f("plaats");
					$last_acc[$db->f("type_id")]["skigebied"]=$db->f("skigebied");
					$last_acc[$db->f("type_id")]["land"]=$db->f("land");
					if(file_exists("pic/cms/types_specifiek/".$db->f("type_id").".jpg")) {
						$last_acc[$db->f("type_id")]["afbeelding"]="types_specifiek/".$db->f("type_id").".jpg";
					} elseif(file_exists("pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg")) {
						$last_acc[$db->f("type_id")]["afbeelding"]="accommodaties/".$db->f("accommodatie_id").".jpg";
					} else {
						$last_acc[$db->f("type_id")]["afbeelding"]="accommodaties/0.jpg";
					}
				}
			}
			if(is_array($last_acc)) {
				$last_acc_html.="<div id=\"laatstbekeken\" class=\"noprint\">";
				$last_acc_html.="<div id=\"laatstbekeken_acc_wrapper\">";
				$last_acc_html.="<div class=\"kop\">".html("laatstbekekenaccommodaties","index")."</div>";
				$last_acc_teller=0;
				while(list($key,$value)=each($last_acc)) {
					if($last_acc_teller) {
						$last_acc_html.="<div class=\"laatstbekeken_divider\">&nbsp;</div>";
					}
					$last_acc_html.="<div class=\"laatstbekeken_acc\" onclick=\"document.location.href='".$vars["path"].txt("menu_accommodatie")."/".$value["begincode"].$key."/';\">";
					$last_acc_html.="<div class=\"laatstbekeken_img_div\"><img src=\"".$vars["path"]."pic/cms/".$value["afbeelding"]."\" alt=\"\"></div>";
					$last_acc_html.="<div class=\"laatstbekeken_tekst\">";
					$last_acc_html.="<div>".wt_he($value["naam"])."</div>";
					$last_acc_html.="<div style=\"margin-top:7px;\">";
					if($vars["websitetype"]==7) {
						$last_acc_html.=wt_he($value["plaats"].", ".$value["skigebied"]);
					} else {
						$last_acc_html.=wt_he($value["plaats"].", ".$value["land"]);
					}
					$last_acc_html.="</div>";
					$last_acc_html.="</div>"; # afsluiten laatstbekeken_tekst
					$last_acc_html.="</div>"; # afsluiten laatstbekeken_acc
					$last_acc_teller++;
					if($last_acc_teller>=($id=="index" ? 3 : 4)) {
						break;
					}
				}
				$last_acc_html.="<div style=\"clear: both;\"></div>\n";
				$last_acc_html.="</div>"; # afsluiten laatstbekeken_acc_wrapper
				$last_acc_html.="<div id=\"laatstbekeken_volledigelijst\"><a href=\"".$vars["path"]."saved.php\">".html("laatstbekekenaccommodaties_volledigelijst","index")." &raquo;</a></div>";
				$last_acc_html.="</div>"; # afsluiten laatstbekeken

				$last_acc_html.="<div style=\"clear: both;\"></div>\n";
			}

			if($vars["websitetype"]==6) {
				# "laatst bekeken"-blok (nog) niet tonen op ChaletsInVallandry
				unset($last_acc_html);
			}

			#
			# SPECIAAL voor WSA : mag verwijderd worden zodra WSA van nieuwe vormgeving is voorzien (opmerking geplaatst: 02-09-2011)
			#
			if($last_acc_inquery and $vars["website"]=="W") {
				$db->query("SELECT t.type_id, a.accommodatie_id, a.naam, a.soortaccommodatie, t.naam AS tnaam, l.begincode, p.naam AS plaats FROM accommodatie a, type t, plaats p, land l WHERE t.type_id IN (".$last_acc_inquery.") AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND a.wzt='".addslashes($vars["seizoentype"])."' AND a.tonen=1 AND t.tonen=1;");
		#		echo $db->lastquery;
				while($db->next_record()) {
					$last_acc_array[$db->f("type_id")]="<a href=\"".$vars["basehref"].txt("menu_accommodatie")."/".$db->f("begincode").$db->f("type_id")."/\" title=\"".wt_he(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "").", ".$db->f("plaats"))."\"><img src=\"".$vars["basehref"]."pic/cms/";
					if(file_exists("pic/cms/types_specifiek_tn/".$db->f("type_id").".jpg")) {
						$last_acc_array[$db->f("type_id")].="types_specifiek_tn/".$db->f("type_id");
					} elseif(file_exists("pic/cms/accommodaties_tn/".$db->f("accommodatie_id").".jpg")) {
						$last_acc_array[$db->f("type_id")].="accommodaties_tn/".$db->f("accommodatie_id");
					} else {
						$last_acc_array[$db->f("type_id")].="accommodaties_tn/0";
					}
					$last_acc_array[$db->f("type_id")].=".jpg\" width=\"32\" height=\"24\" border=\"0\" class=\"pic_met_border\"></a>";
		#			$last_acc_teller++;
				}
				@krsort($last_acc_bezoeker);
				@reset($last_acc_bezoeker);
			#	unset($last_acc_teller);
				while(list($key,$value)=@each($last_acc_bezoeker)) {
					if($last_acc_teller<10 and $last_acc_array[$value]) {
						$last_acc_teller++;
						$last_acc_content=$last_acc_array[$value]."&nbsp;".$last_acc_content;
					}
				}
				if($last_acc_teller and $last_acc_content) {
					$last_acc_wsa="<span class=\"wtform_small\">".html(($last_acc_teller>7 ? "laatstbekeken_10" : "laatstbekeken")).":</span>&nbsp;<br>";
					$last_acc_wsa.=$last_acc_content;
				}
			}
		}
	}
}

# Alle types in $vars zetten / vullen
if($vars["types_in_vars"]) {
	$db->query("SELECT DISTINCT a.".($mustlogin ? "interne" : "")."naam AS naam, a.archief, a.wzt, p.naam AS plaats, l.begincode, a.accommodatie_id, t.websites, t.type_id, t.optimaalaantalpersonen, t.maxaantalpersonen, t.verzameltype FROM accommodatie a, type t, plaats p, land l, skigebied s WHERE t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.skigebied_id=s.skigebied_id AND p.land_id=l.land_id".$vars["types_in_vars_andquery"]." ORDER BY p.naam, a.naam, t.optimaalaantalpersonen, a.accommodatie_id, s.naam, p.naam;");
	while($db->next_record()) {
		if($vars["types_in_vars_wzt_splitsen"]) {
			$vars["alletypes"][$db->f("wzt")][$db->f("type_id")]=$db->f("plaats")." - ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." pers. - ".$db->f("begincode").$db->f("type_id").")".($db->f("verzameltype") ? " (V)" : "");
			if($db->f("archief")==0) {
				$vars["alletypes_zonderarchief"][$db->f("wzt")][$db->f("type_id")]=$vars["alletypes"][$db->f("type_id")];
			}
		} else {
			$vars["alletypes"][$db->f("type_id")]=$db->f("plaats")." - ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." pers. - ".$db->f("begincode").$db->f("type_id").")".($db->f("verzameltype") ? " (V)" : "");

			if($db->f("wzt")==2) {
				if(preg_match("/I/",$db->f("websites")) and !preg_match("/Z/",$db->f("websites"))) {
					# Link naar Italissima.nl
					$externelink=$vars["websites_basehref"]["I"];
				} else {
					# Link naar Zomerhuisje.nl
					$externelink=$vars["websites_basehref"]["Z"];
				}
			} else {
				if(preg_match("/W/",$db->f("websites")) and !preg_match("/C/",$db->f("websites"))) {
					# Link naar SuperSki.nl
					$externelink=$vars["websites_basehref"]["W"];
				} else {
					# Link naar Chalet.nl
					$externelink=$vars["websites_basehref"]["C"];
				}
			}
			if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
				$externelink="http://".$_SERVER["HTTP_HOST"]."/chalet/";
			}
			$externelink.="accommodatie/".$db->f("begincode").$db->f("type_id")."/";
			$vars["alletypes_externelink"][$db->f("type_id")]="<a href=\"".$externelink."\" target=\"_blank\">".wt_he($db->f("plaats")." - ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("optimaalaantalpersonen")<>$db->f("maxaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")." pers. - ".$db->f("begincode").$db->f("type_id").")")."</a>";
			if($db->f("archief")==0) {
				$vars["alletypes_zonderarchief"][$db->f("type_id")]=$vars["alletypes"][$db->f("type_id")];
			}
		}
	}
}

# Alle accommodaties in $vars zetten
if($vars["acc_in_vars"]) {
	$db->query("SELECT DISTINCT a.naam, a.archief, p.naam AS plaats, a.accommodatie_id, a.wzt FROM accommodatie a, plaats p WHERE a.plaats_id=p.plaats_id ORDER BY p.naam, a.naam, a.accommodatie_id;");
	while($db->next_record()) {
		$vars["alleacc"][$db->f("accommodatie_id")]=$db->f("plaats")." - ".$db->f("naam");
		$vars["alleacc_seizoen"][$db->f("wzt")][$db->f("accommodatie_id")]=$db->f("plaats")." - ".$db->f("naam");
		if($db->f("archief")==0) {
			$vars["alleacc_zonderarchief"][$db->f("accommodatie_id")]=$db->f("plaats")." - ".$db->f("naam");
			$vars["alleacc_seizoen_zonderarchief"][$db->f("wzt")][$db->f("accommodatie_id")]=$db->f("plaats")." - ".$db->f("naam");
		}
	}
}

# Opslaan dat de klant in het systeem bezig is
if($boeking_wijzigen and ($_GET["bid"] or $_GET["bid"] or $gegevens["stap1"]["boekingid"])) {
	if($_GET["bid"]) {
		$boekingid=$_GET["bid"];
	} else {
		$boekingid=$gegevens["stap1"]["boekingid"];
	}
	$db->query("UPDATE boeking SET zit_in_beheersysteem=NOW() WHERE boeking_id='".addslashes($boekingid)."';");
}

if($_GET["fromsite"]) {
	# Opslaan in cookie dat iemand heeft doorgeklikt van Zomerhuisje naar Chalettour (zodat correct kan worden teruggelinkt)
	setcookie("fromsite",$_GET["fromsite"],0,"/");
}


?>