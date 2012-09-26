<?php

#
# In dit bestand staan alleen vars en functions die binnen het CMS nodig zijn
#

if($mustlogin) {
	#
	# CMS
	#
	ini_set("session.use_cookies",1);
	ini_set("session.use_only_cookies",1);
	ini_set("session.enable_trans_sid",1);
	ini_set("session.use_trans_sid",0);
	ini_set("session.cookie_httponly",1);

	session_start();

	if(!$cron and !$cronmap and !$css) {
		include_once "Text/Diff.php";
		include_once "Text/Diff/Renderer.php";
		include_once "Text/Diff/Renderer/inline.php";
	}

	# Taal bepalen
	if($_GET["cmstaal"]) {
		if($_GET["cmstaal"]=="nl") {
			$vars["cmstaal"]="";
		} else {
			$vars["cmstaal"]=$_GET["cmstaal"];
			if($vars["cmstaal"]) {
				$vars["ttv_cms"]="_".$vars["cmstaal"];
			}
		}
	} else {
		if($_SESSION["cmstaal"]=="nl") {
			$vars["cmstaal"]="";
		} else {
			$vars["cmstaal"]=$_SESSION["cmstaal"];
			if($vars["cmstaal"]) {
				$vars["ttv_cms"]="_".$vars["cmstaal"];
			}
		}
	}

	# wzt bepalen
	if(!$_GET["wzt"] and $_GET["edit"] and ($id=="cms_accommodaties" or $id=="cms_plaatsen" or $id=="cms_skigebieden")) {
		if($id=="cms_accommodaties" and ($_GET["edit"]==1 or $_GET["show"]==1) and $_GET["1k0"]) {
			$db->query("SELECT wzt FROM accommodatie WHERE accommodatie_id='".addslashes($_GET["1k0"])."';");
			if($db->next_record()) {
				$_GET["wzt"]=$db->f("wzt");
			}
		} elseif($id=="cms_plaatsen" and ($_GET["edit"]==4 or $_GET["show"]==4) and $_GET["4k0"]) {
			$db->query("SELECT wzt FROM plaats WHERE plaats_id='".addslashes($_GET["4k0"])."';");
			if($db->next_record()) {
				$_GET["wzt"]=$db->f("wzt");
			}
		} elseif($id=="cms_skigebieden" and ($_GET["edit"]==5 or $_GET["show"]==5) and $_GET["5k0"]) {
			$db->query("SELECT wzt FROM skigebied WHERE skigebied_id='".addslashes($_GET["5k0"])."';");
			if($db->next_record()) {
				$_GET["wzt"]=$db->f("wzt");
			}
		}
	}

	# Gegevens werknemers
	$db->query("SELECT user_id, voornaam, email FROM user ORDER BY user_id;");
	while($db->next_record()) {
		$werknemer[$db->f("user_id")]=$db->f("voornaam");
		$werknemer_mail[$db->f("user_id")]=$db->f("email");
	}
	$vars["allewerknemers"]=$werknemer;
	$vars["allewerknemers_mail"]=$werknemer_mail;

	# Login-class CMS
	#
	# priv:
	# 1 = Gebruikers
	# 2 = Verwijderen boeking
	# 3 = Financiële overzichten
	# 4 = Tarievenoptie van bestaande accommodaties aanpassen
	#
	#
	$login = new Login;
	$login->settings["logout_number"]=1;
	$login->settings["adminmail"]=$vars["email"];
	$login->settings["db"]["tablename"]="user";
	if($vars["mustlogin_cms_cron_false"]) {
		$login->settings["mustlogin"]=false;
	} else {
		$login->settings["mustlogin"]=true;
	}
	$login->settings["loginpage"]=$vars["path"]."cms.php";
	$login->settings["checkloginpage"]="cms.php";
	$login->settings["tablecolor"]="#0D3E88";
	$login->settings["name"]="chalet";
	$login->settings["language"]="nl";
	$login->settings["mailpassword_attempt"]=false;
	$login->settings["sysop"]="<a href=\"http://www.webtastic.nl/6.html\">WebTastic</a>";
	$login->settings["recheck_userdata"]=true;
	$login->settings["save_user_agent"]=true;
	$login->settings["salt"]=$vars["salt"];

	if($vars["website"]=="C" or $vars["website"]=="Z") {
		if(!$vars["lokale_testserver"] and $_SERVER["REMOTE_ADDR"]<>"80.101.166.235" and $_SERVER["REMOTE_ADDR"]<>"213.125.164.74" and $_SERVER["REMOTE_ADDR"]<>"82.173.186.80") {
			# Buiten kantoor
			$login->settings["mustlogin_via_https"]=true;
		}
	}

	if(!in_array($_SERVER["REMOTE_ADDR"],$vars["vertrouwde_ips"])) {
		$login->settings["settings"]["rememberpassword"]=false;
		$login->settings["settings"]["no_autocomplete"]=true;
		if($_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html" and $_SERVER["REMOTE_ADDR"]<>"82.173.186.80") {
			$login->settings["mail_after_login"]="bert@chalet.nl";
			$host=gethostbyaddr($_SERVER["REMOTE_ADDR"]);
			$login->settings["mailtext_after_login"]="Zojuist heeft [[voornaam]] vanaf een andere locatie ingelogd in het Chalet.nl-CMS.\n\n".$host.($_SERVER["REMOTE_ADDR"]<>$host ? " (".$_SERVER["REMOTE_ADDR"].")" : "");
			$login->settings["mailsubject_after_login"]="Inlog vanaf andere locatie door [[voornaam]] - ".$host;
		}
	}
	$login->end_declaration();

	# Titels (die afwijken van $menu)
#	$title["cms"]="Content Management System ".$vars["websitenaam"]." - Siteversie 2.8.5 (3.0 in ontwikkeling)";
	$title["cms"]="Content Management System ".$vars["websitenaam"];

	# Layout CMS
	$layout=new cms_layout;
	$layout->settings["system_name"]="CMS Chalet.nl";
#	$layout->settings["path"]=$vars["path"];
	$layout->settings["css_folder"]=$vars["path"]."css/";
	$layout->settings["scripts_folder"]=$vars["path"]."scripts/";
	$layout->settings["logo"]="pic/cms_logo_".$vars["cmstaal"].".gif";
#	echo 	$layout->settings["logo"];exit;
	$layout->settings["mainpage"]="cms";
	$layout->settings["loginname_field"]="voornaam";
	$layout->settings["css_cacheversion"]=@filemtime("css/cms_layout.css");
	$layout->settings["chromeframe"]=true;
	$layout->settings["javascript_cacheversion"]=@filemtime("scripts/cms_functions.js");
	$layout->settings["extra_cssfiles"][]=$vars["path"]."css/opmaak_websites_en_cms.css.phpcache?type=1&cmscss=1&cache=".@filemtime("css/opmaak_websites_en_cms.css.phpcache");
	$layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_laatste.css?cache=".@filemtime("css/cms_layout_laatste.css");
	if($_GET["hidecmsmenu"]) {
		$layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_hidemenu.css";
	}
	$layout->settings["extra_javascriptfiles"][]=$vars["path"]."scripts/cms.layout.js";
#	$layout->settings["extra_javascriptfiles"][]=$vars["path"]."scripts/jquery.js";
#	$layout->settings["extra_javascriptfiles"][]=$vars["path"]."scripts/jquery.tablescroll.js";

	if($login->logged_in and $id<>"cms" and $_SERVER["HTTP_HOST"]<>"www.chalet.nl" and !$vars["lokale_testserver"]) {
		$layout->settings["cms_via_verkeerde_site"]=true;
	}

	$layout->settings["jquery_google_api"]=true;
	$layout->settings["jqueryui_google_api"]=true;
	$layout->settings["jqueryui_google_api_theme"]="smoothness";

	if($id=="cms_financien_betalingen" or $id=="cms_overzichten_overig") {
		$layout->settings["render_as_ie7"]=true;
	}

	# Achtergrondkleur CMS bepalen
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		$layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_bgcolor.css.phpcache?bg=878481";
	} elseif($login->userlevel>=10) {
		$layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_bgcolor.css.phpcache?bg=ff1844";
	} elseif($_GET["wzt"]==2) {
		$layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_bgcolor.css.phpcache?bg=95ddec";
	}
	if($_GET["bid"]) {
		$db->query("SELECT website FROM boeking WHERE boeking_id='".addslashes($_GET["bid"])."';");
		if($db->next_record()) {
			if($vars["websites_wzt"][2][$db->f("website")] and $_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html" and $login->userlevel<10) {
				$layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_bgcolor.css.phpcache?bg=95ddec";
			}
		}
	}

	if($vars["cmstaal"] and $vars["cmstaal"]<>"nl") $layout->settings["extra_cssfiles"][]=$vars["path"]."css/cms_layout_anderetaal.css";
	if($login->logged_in) $layout->settings["logout_extra"]="<div style=\"margin-top:3px;\"><form method=\"get\" style=\"margin:0px;\" action=\"".$vars["path"]."cms.php\"><input type=\"hidden\" name=\"bc\" value=\"".htmlentities($_GET["bc"])."\"><input type=\"text\" name=\"cmssearch\">&nbsp;<input type=\"submit\" value=\" OK \"></form></div>";
	$layout->menu_item("cms","Hoofdpagina","",false);

	$layout->menu_item("cms_aanbiedingen","Aanbiedingen","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Winter - actief",array("wzt"=>"1","t"=>"1"),true);
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Winter - inactief",array("wzt"=>"1","t"=>"2"),true);
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Winter - archief",array("wzt"=>"1","t"=>"3"),true);
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Zomer - actief",array("wzt"=>"2","t"=>"1"),true);
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Zomer - inactief",array("wzt"=>"2","t"=>"2"),true);
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Zomer - archief",array("wzt"=>"2","t"=>"3"),true);
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Winterkorting - inactief",array("wzt"=>"1","t"=>"4"),true);
	$layout->submenu_item("cms_aanbiedingen","","cms_aanbiedingen","Zomerkorting - inactief",array("wzt"=>"2","t"=>"4"),true);

	$layout->menu_item("cms_accommodaties","Accommodaties",array("archief"=>"0"),true,false,array("slide"=>true));
	$layout->submenu_item("cms_accommodaties--archief__WTIS__0","","cms_accommodaties","Winter",array("wzt"=>"1","archief"=>"0"),true);
	$layout->submenu_item("cms_accommodaties--archief__WTIS__0","","cms_accommodaties","Zomer",array("wzt"=>"2","archief"=>"0"),true);
	$layout->submenu_item("cms_accommodaties--archief__WTIS__0","","cms_accommodaties","Nog nakijken: Winter",array("wzt"=>"1","archief"=>"0","controleren"=>1),true);
	$layout->submenu_item("cms_accommodaties--archief__WTIS__0","","cms_accommodaties","Nog nakijken: Zomer",array("wzt"=>"2","archief"=>"0","controleren"=>1),true);

	if($login->has_priv("24")) {
		$layout->menu_item("cms_mail_klanten_vorig_seizoen","Bestaande klanten","",true,false,array("slide"=>true));
		$layout->submenu_item("cms_mail_klanten_vorig_seizoen","","cms_mail_klanten_vorig_seizoen","Te mailen",array("status"=>"2"),true);
		$layout->submenu_item("cms_mail_klanten_vorig_seizoen","","cms_mail_klanten_vorig_seizoen","Nabellen",array("status"=>"3"),true);
		$layout->submenu_item("cms_mail_klanten_vorig_seizoen","","cms_mail_klanten_vorig_seizoen","Actief",array("status"=>"1"),true);
		$layout->submenu_item("cms_mail_klanten_vorig_seizoen","","cms_mail_klanten_vorig_seizoen","Afgehandeld",array("status"=>"4"),true);
	}

	$layout->menu_item("cms_bijkomendekosten","Bijkomende kosten","",true);

#	$layout->menu_item("cms_blokkenhoofdpagina","Blokken hoofdpagina","",true);
	$layout->menu_item("cms_blokkenhoofdpagina","Blokken hoofdpagina","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_blokkenhoofdpagina","","cms_blokkenhoofdpagina","Winter",array("wzt"=>"1"),true);
	$layout->submenu_item("cms_blokkenhoofdpagina","","cms_blokkenhoofdpagina","Zomerhuisje",array("wzt"=>"2"),true);
	$layout->submenu_item("cms_blokkenhoofdpagina","","cms_blokkenhoofdpagina","Italissima",array("wzt"=>"3"),true);

#	$layout->menu_item("cms_blokkenaccommodaties","Blokken links","",true);

	$layout->menu_item("cms_blokkenaccommodaties","Blokken links","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_blokkenaccommodaties","","cms_blokkenaccommodaties","Zomerhuisje",array("wst"=>"3"),true);
	$layout->submenu_item("cms_blokkenaccommodaties","","cms_blokkenaccommodaties","Italissima",array("wst"=>"7"),true);

	$layout->menu_item("cms_boekingen","Boekingen",array("archief"=>"0"),true,false,array("slide"=>true));

	$layout->submenu_item("cms_boekingen--archief__WTIS__0","","cms_boekingen","Aangevraagd",array("bt"=>"1","archief"=>"0"),true);
	$layout->submenu_item("cms_boekingen--archief__WTIS__0","","cms_boekingen","Bevestigd",array("bt"=>"2","archief"=>"0"),true);
	$layout->submenu_item("cms_boekingen--archief__WTIS__0","","cms_boekingen","Actueel",array("bt"=>"5","archief"=>"0"),true);
	$layout->submenu_item("cms_boekingen--archief__WTIS__0","","cms_boekingen","Recent onafgerond",array("bt"=>"3","archief"=>"0"),true);
	$layout->submenu_item("cms_boekingen--archief__WTIS__0","","cms_boekingen","Geannuleerd",array("bt"=>"6","archief"=>"0"),true);

	$layout->menu_item("cms_diversen","Diversen","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_diversen","","cms_diversen","Actielijst WebTastic",array("t"=>"1"),true);
	$layout->submenu_item("cms_diversen","","cms_diversen","Actielijst archief",array("t"=>"2"),true);
	$layout->submenu_item("cms_diversen","","cms_blog","Blog Italissima","",true);
	$layout->submenu_item("cms_diversen","","cms_diversen","Instellingen",array("t"=>"3"),true);
	$layout->submenu_item("cms_diversen","","cms_diversen","Vouchertermen",array("t"=>"5"),true);

	if(($login->has_priv(27) and $_SERVER["REMOTE_ADDR"]=="80.101.166.235") or $login->has_priv(28) or $login->has_priv(3)) {
		$layout->menu_item("cms_financien","Financiën","",true);
	}

	$layout->menu_item("cms_garanties","Garanties","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_garanties","","cms_garanties","Ongebruikte",array("status"=>"1"),true);
	if($login->has_priv("9")) {
		$layout->submenu_item("cms_garanties","","cms_garanties","Gebruikte",array("status"=>"2"),true);
		$layout->submenu_item("cms_garanties","","cms_garanties","Verlopen",array("status"=>"3"),true);
		$layout->submenu_item("cms_garanties","","cms_garanties","Overzicht",array("status"=>"4"),true);
	}

	if($login->has_priv("1")) {
		$layout->menu_item("cms_gebruikers","Gebruikers","",true,false,array("slide"=>true));
		$layout->submenu_item("cms_gebruikers","","cms_gebruikers","actief",array("t"=>"1"),true);
		$layout->submenu_item("cms_gebruikers","","cms_gebruikers","inactief",array("t"=>"2"),true);
	}
	$layout->menu_item("cms_hulp","Hulp","",true);

	$layout->menu_item("cms_kortingscodes","Kortingscodes","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_kortingscodes","","cms_kortingscodes","actief",array("t"=>"1"),true);
	$layout->submenu_item("cms_kortingscodes","","cms_kortingscodes","inactief",array("t"=>"2"),true);
	$layout->submenu_item("cms_kortingscodes","","cms_kortingscodes","archief",array("t"=>"3"),true);

	$layout->menu_item("cms_landen","Landen","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_landen","","cms_landen","Winter",array("wzt"=>"1"),true);
	$layout->submenu_item("cms_landen","","cms_landen","Zomer",array("wzt"=>"2"),true);

	$layout->menu_item("cms_leveranciers","Leveranciers","",true);

	$layout->menu_item("cms_leveranciers","Leveranciers","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_leveranciers","","cms_leveranciers","Direct",array("beheerder"=>"0"),true);
	$layout->submenu_item("cms_leveranciers","","cms_leveranciers","Beheerders",array("beheerder"=>"1"),true);

#	$layout->menu_item("cms_mailteksten","Mailteksten","",true);
	$layout->menu_item("cms_mailteksten","Mailteksten","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_mailteksten","","cms_mailteksten","Winter",array("wzt"=>"1"),true);
	$layout->submenu_item("cms_mailteksten","","cms_mailteksten","Zomer",array("wzt"=>"2"),true);

	$layout->menu_item("cms_optieaanvragen","Optie-aanvragen","",true,false,array("slide"=>true));
	while(list($key,$value)=each($vars["optieaanvragen_status_menu"])) {
		$layout->submenu_item("cms_optieaanvragen","","cms_optieaanvragen",$value,array("status"=>$key),true);
	}

	$layout->menu_item("cms_optieleveranciers","Optieleveranciers","",true);

	$layout->menu_item("cms_optie_soorten","Opties","",true);

#	$layout->menu_item("cms_overzichten","Overzichten","",true);

	$layout->menu_item("cms_overzichten","Overzichten","",true,false,array("slide"=>true));
	if($login->has_priv("22")) {
		$layout->submenu_item("cms_overzichten","","cms_overzichten_boekingen","Adressen bij boekingen",array("t"=>"5"),true);
	}
	if($login->has_priv("25")) {
		$layout->submenu_item("cms_overzichten","","cms_overzichten","Lijsten",array("t"=>"1"),true);
	}
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Ontbr. handtekeningen",array("t"=>"2"),true);
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Te vertalen teksten",array("t"=>"3"),true);
	$layout->submenu_item("cms_overzichten","","cms_meldingen","Indeling CMS-hoofdpagina",array("t"=>"4"),true);
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Vouchers",array("t"=>"5"),true);
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Na te kijken winteracc.",array("t"=>"6"),true);
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Na te kijken zomeracc.",array("t"=>"7"),true);
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Na te kijken beoordelingen",array("t"=>"11"),true);
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Nieuwsbrief-leden",array("t"=>"10"),true);
	$layout->submenu_item("cms_overzichten","","cms_overzichten_overig","Overig",array("t"=>"9"),true);

	$layout->menu_item("cms_plaatsen","Plaatsen","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_plaatsen","","cms_plaatsen","Winter",array("wzt"=>"1"),true);
	$layout->submenu_item("cms_plaatsen","","cms_plaatsen","Zomer",array("wzt"=>"2"),true);

	$layout->menu_item("cms_skigebieden","Regio's","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_skigebieden","","cms_skigebieden","Winter",array("wzt"=>"1"),true);
	$layout->submenu_item("cms_skigebieden","","cms_skigebieden","Zomer",array("wzt"=>"2"),true);

	if($login->has_priv("23")) {
#		$layout->menu_item("cms_reisbureaus","Reisbureaus","",true);
		$layout->menu_item("cms_reisbureaus","Reisbureaus","",true,false,array("slide"=>true));
		$layout->submenu_item("cms_reisbureaus","","cms_reisbureaus","Actief",array("t"=>"1"),true);
		$layout->submenu_item("cms_reisbureaus","","cms_reisbureaus","Inactief",array("t"=>"2"),true);
	}

	$layout->menu_item("cms_seizoenen","Seizoenen","",true);

	$layout->menu_item("cms_skipassen","Skipassen","",true);

#	$layout->menu_item("cms_themas","Thema's","",true);
	$layout->menu_item("cms_themas","Thema's","",true,false,array("slide"=>true));
	$layout->submenu_item("cms_themas","","cms_themas","Winter",array("wzt"=>"1"),true);
	$layout->submenu_item("cms_themas","","cms_themas","Zomer",array("wzt"=>"2"),true);

	$layout->menu_item("cms_top10s","Top 10 aanbiedingen","",true);
	$layout->menu_item("cms_vertrekdagtypes","Vertrekdagtypes","",true);

	if($login->has_priv("8")) {
		$layout->menu_item("cms_zoekstatistieken","Zoekstatistieken","",true,false,array("slide"=>true));
		$layout->submenu_item("cms_zoekstatistieken","","cms_zoekstatistieken","Winter",array("wzt"=>"1"),true);
		$layout->submenu_item("cms_zoekstatistieken","","cms_zoekstatistieken","Zomer",array("wzt"=>"2"),true);
	}


	$layout->menu_item("archief","Archief",array("archief"=>"1"),true,false,array("slide"=>true));
	$layout->submenu_item("archief--archief__WTIS__1","","cms_accommodaties","Winteraccommodaties",array("wzt"=>"1","archief"=>"1"),true);
	$layout->submenu_item("archief--archief__WTIS__1","","cms_accommodaties","Zomeraccommodaties",array("wzt"=>"2","archief"=>"1"),true);
	$layout->submenu_item("archief--archief__WTIS__1","","cms_boekingen","Boekingen",array("bt"=>"4","archief"=>"1"),true);
	$layout->submenu_item("archief--archief__WTIS__1","","cms_optie_soorten","Opties",array("verborgen"=>"1"),true);

	if($login->userlevel>=10) {
		$layout->menu_item("cms_testpaginas","Testpagina's (WT)","",true);
	}

	$layout->title=$title;
	$layout->end_declaration();

	# CMS
	$cms=new cms2;

	$cms->settings["show_upload_message"]=true;
	$cms->vars["users"]=$werknemer;

	# 1 = accommodatie
	if($_GET["wzt"]==2) {
		$cms->settings[1]["types"]="zomeraccommodaties";
		$cms->settings[1]["type_single"]="zomeraccommodatie";
	} else {
		$cms->settings[1]["types"]="winteraccommodaties";
		$cms->settings[1]["type_single"]="winteraccommodatie";
	}
	if($_GET["archief"]) {
		$cms->settings[1]["types"].=" - archief";
	}
	$cms->settings[1]["file"]="cms_accommodaties.php";
	$cms->settings[1]["log"]["active"]=true;
	$cms->db[1]["maintable"]="accommodatie";

	# 2 = type
	$cms->settings[2]["types"]="types";
	$cms->settings[2]["type_single"]="type";
	$cms->settings[2]["file"]="cms_types.php";
	$cms->settings[2]["log"]["active"]=true;
	$cms->db[2]["maintable"]="type";

	# 3 = leverancier
	$cms->settings[3]["types"]="leveranciers";
	$cms->settings[3]["type_single"]="leverancier";
	$cms->settings[3]["file"]="cms_leveranciers.php";
	$cms->settings[3]["log"]["active"]=true;
	$cms->db[3]["maintable"]="leverancier";

	# 4 = plaats
	if($_GET["wzt"]==2) {
		$cms->settings[4]["types"]="zomerplaatsen";
		$cms->settings[4]["type_single"]="zomerplaats";
	} else {
		$cms->settings[4]["types"]="winterplaatsen";
		$cms->settings[4]["type_single"]="winterplaats";
	}
	$cms->settings[4]["file"]="cms_plaatsen.php";
	$cms->settings[4]["log"]["active"]=true;
	$cms->db[4]["maintable"]="plaats";

	# 5 = skigebied
	if($_GET["wzt"]==2) {
		$cms->settings[5]["types"]="zomerregio's";
		$cms->settings[5]["type_single"]="zomerregio";
	} else {
		$cms->settings[5]["types"]="skigebieden";
		$cms->settings[5]["type_single"]="skigebied";
	}
	$cms->settings[5]["file"]="cms_skigebieden.php";
	$cms->settings[5]["log"]["active"]=true;
	$cms->db[5]["maintable"]="skigebied";

	# 6 = land
	$cms->settings[6]["types"]="landen";
	$cms->settings[6]["type_single"]="land";
	$cms->settings[6]["file"]="cms_landen.php";
	$cms->settings[6]["log"]["active"]=true;
	$cms->db[6]["maintable"]="land";

	# 7 = vertrekdagtype
	$cms->settings[7]["types"]="vertrekdagtypes";
	$cms->settings[7]["type_single"]="vertrekdagtype";
	$cms->settings[7]["file"]="cms_vertrekdagtypes.php";
	$cms->settings[7]["log"]["active"]=true;
	$cms->db[7]["maintable"]="vertrekdagtype";

	# 8 = beheerder (via leverancier)
	if($_GET["beheerder"]) {
		$cms->settings[8]["types"]="beheerders";
		$cms->settings[8]["type_single"]="beheerder";
	} else {
		$cms->settings[8]["types"]="leveranciers";
		$cms->settings[8]["type_single"]="leverancier";
	}
	$cms->settings[8]["file"]="cms_leveranciers.php";
	$cms->settings[8]["log"]["active"]=true;
	$cms->db[8]["maintable"]="leverancier";

	# 9 = seizoen
	$cms->settings[9]["types"]="seizoenen";
	$cms->settings[9]["type_single"]="seizoen";
	$cms->settings[9]["file"]="cms_seizoenen.php";
	$cms->settings[9]["log"]["active"]=true;
	$cms->db[9]["maintable"]="seizoen";

	# 10 = skipas
	$cms->settings[10]["types"]="skipassen";
	$cms->settings[10]["type_single"]="skipas";
	$cms->settings[10]["file"]="cms_skipassen.php";
	$cms->settings[10]["log"]["active"]=true;
	$cms->db[10]["maintable"]="skipas";

	# 11 = optie_soort
	$cms->settings[11]["types"]="optie-soorten";
	$cms->settings[11]["type_single"]="optie-soort";
	$cms->settings[11]["file"]="cms_optie_soorten.php";
	$cms->settings[11]["log"]["active"]=true;
	$cms->db[11]["maintable"]="optie_soort";

	# 12 = optie_groep
	$cms->settings[12]["types"]="optie-groepen";
	$cms->settings[12]["type_single"]="optie-groep";
	$cms->settings[12]["file"]="cms_optie_groepen.php";
	$cms->settings[12]["log"]["active"]=true;
	$cms->db[12]["maintable"]="optie_groep";

	# 13 = optie_onderdeel
	$cms->settings[13]["types"]="optie-onderdelen";
	$cms->settings[13]["type_single"]="optie-onderdeel";
	$cms->settings[13]["file"]="cms_optie_onderdelen.php";
	$cms->settings[13]["log"]["active"]=true;
	$cms->db[13]["maintable"]="optie_onderdeel";

	# 14 = aanbieding
	if($_GET["wzt"]==2) {
		if($_GET["t"]==1) {
			$cms->settings[14]["types"]="actieve zomeraanbiedingen";
			$cms->settings[14]["type_single"]="actieve zomeraanbieding";
		} elseif($_GET["t"]==2) {
			$cms->settings[14]["types"]="inactieve zomeraanbiedingen";
			$cms->settings[14]["type_single"]="inactieve zomeraanbieding";
		} elseif($_GET["t"]==3) {
			$cms->settings[14]["types"]="gearchiveerde zomeraanbiedingen";
			$cms->settings[14]["type_single"]="gearchiveerde zomeraanbieding";
		} elseif($_GET["t"]==4) {
			$cms->settings[14]["types"]="inactieve zomerkortingen";
			$cms->settings[14]["type_single"]="inactieve zomerkorting";
		}
	} else {
		if($_GET["t"]==1) {
			$cms->settings[14]["types"]="actieve winteraanbiedingen";
			$cms->settings[14]["type_single"]="actieve winteraanbieding";
		} elseif($_GET["t"]==2) {
			$cms->settings[14]["types"]="inactieve winteraanbiedingen";
			$cms->settings[14]["type_single"]="inactieve winteraanbieding";
		} elseif($_GET["t"]==3) {
			$cms->settings[14]["types"]="gearchiveerde winteraanbiedingen";
			$cms->settings[14]["type_single"]="gearchiveerde winteraanbieding";
		} elseif($_GET["t"]==4) {
			$cms->settings[14]["types"]="inactieve winterkortingen";
			$cms->settings[14]["type_single"]="inactieve winterkorting";
		}
	}
	$cms->settings[14]["file"]="cms_aanbiedingen.php";
	$cms->settings[14]["log"]["active"]=true;
	$cms->db[14]["maintable"]="aanbieding";

	# 15 = aanbieding_accommodatie
	$cms->settings[15]["types"]="accommodaties";
	$cms->settings[15]["type_single"]="accommodatie";
	$cms->settings[15]["file"]="cms_aanbiedingen.php";
	$cms->settings[15]["log"]["active"]=true;
	$cms->db[15]["maintable"]="aanbieding_accommodatie";

	# 16 = aanbieding_type
	$cms->settings[16]["types"]="types";
	$cms->settings[16]["type_single"]="type";
	$cms->settings[16]["file"]="cms_aanbiedingen.php";
	$cms->settings[16]["log"]["active"]=true;
	$cms->db[16]["maintable"]="aanbieding_type";

	# 17 = aanbieding_aankomstdatum
	$cms->settings[17]["types"]="aankomstdata";
	$cms->settings[17]["type_single"]="aankomstdatum";
	$cms->settings[17]["file"]="cms_aanbiedingen.php";
	$cms->settings[17]["log"]["active"]=true;
	$cms->db[17]["maintable"]="aanbieding_aankomstdatum";

	# 18 = top10
	$cms->settings[18]["types"]="top 10-seizoenen";
	$cms->settings[18]["type_single"]="top 10-seizoen";
	$cms->settings[18]["file"]="cms_top10s.php";
	$cms->settings[18]["log"]["active"]=true;
	$cms->db[18]["maintable"]="top10";

	# 19 = top10_week
	$cms->settings[19]["types"]="weken";
	$cms->settings[19]["type_single"]="week";
	$cms->settings[19]["file"]="cms_top10_week.php";
	$cms->settings[19]["log"]["active"]=true;
	$cms->db[19]["maintable"]="top10_week";

	# 20 = top10_week_type
	$cms->settings[20]["types"]="accommodaties";
	$cms->settings[20]["type_single"]="accommodatie";
	$cms->settings[20]["file"]="cms_top10_week_type.php";
	$cms->settings[20]["log"]["active"]=true;
	$cms->db[20]["maintable"]="top10_week_type";

	# 21 = boeking
	if($_GET["bt"]==1) {
		$cms->settings[21]["types"]="aanvragen";
		$cms->settings[21]["type_single"]="aanvraag";
	} elseif($_GET["bt"]==4) {
		$cms->settings[21]["types"]="afgelopen boekingen";
		$cms->settings[21]["type_single"]="afgelopen boeking";
	} else {
		$cms->settings[21]["types"]="boekingen";
		$cms->settings[21]["type_single"]="boeking";
	}
	$cms->settings[21]["file"]="cms_boekingen.php";
#	$cms->settings[21]["log"]["active"]=true;
	$cms->db[21]["maintable"]="boeking";

	# 22 = boeking_persoon
	$cms->settings[22]["types"]="personen";
	$cms->settings[22]["type_single"]="persoon";
	$cms->settings[22]["file"]="cms_boekingen_personen.php";
#	$cms->settings[22]["log"]["active"]=true;
	$cms->db[22]["maintable"]="boeking_persoon";

	# 23 = extra_optie
	$cms->settings[23]["types"]="handmatige opties";
	$cms->settings[23]["type_single"]="handmatige optie";
	$cms->settings[23]["file"]="cms_handmatige_opties.php";
#	$cms->settings[23]["log"]["active"]=true;
	$cms->db[23]["maintable"]="extra_optie";

	# 24 = optieleverancier
	$cms->settings[24]["types"]="optieleveranciers";
	$cms->settings[24]["type_single"]="optieleverancier";
	$cms->settings[24]["file"]="cms_optieleveranciers.php";
	$cms->settings[24]["log"]["active"]=true;
	$cms->db[24]["maintable"]="optieleverancier";

	# 25 = gebruikers
	$cms->settings[25]["types"]="gebruikers";
	$cms->settings[25]["type_single"]="gebruiker";
	$cms->settings[25]["file"]="cms_gebruikers.php";
	$cms->settings[25]["log"]["active"]=true;
	$cms->db[25]["maintable"]="user";

	# 26 = betalingen
	$cms->settings[26]["types"]="betalingen";
	$cms->settings[26]["type_single"]="betaling";
	$cms->settings[26]["file"]="cms_boekingen_betalingen.php";
#	$cms->settings[26]["log"]["active"]=true;
	$cms->db[26]["maintable"]="boeking_betaling";

	# 27 = reisbureaus
	if($_GET["t"]==2) {
		$cms->settings[27]["types"]="inactieve reisbureaus";
	} else {
		$cms->settings[27]["types"]="reisbureaus";
	}
	$cms->settings[27]["type_single"]="reisbureau";
	$cms->settings[27]["file"]="cms_reisbureaus.php";
	$cms->settings[27]["log"]["active"]=true;
	$cms->db[27]["maintable"]="reisbureau";

	# 28 = reisbureaus_user
	$cms->settings[28]["types"]="reisagenten";
	$cms->settings[28]["type_single"]="reisagent";
	$cms->settings[28]["file"]="cms_reisbureaus_gebruikers.php";
	$cms->settings[28]["log"]["active"]=true;
	$cms->db[28]["maintable"]="reisbureau_user";

	# 29 = kortingscode
	if($_GET["t"]==2) {
		$cms->settings[29]["types"]="inactieve kortingscodes";
	} elseif($_GET["t"]==3) {
		$cms->settings[29]["types"]="gearchiveerde kortingscodes";
	} else {
		$cms->settings[29]["types"]="kortingscodes";
	}
	$cms->settings[29]["type_single"]="kortingscode";
	$cms->settings[29]["file"]="cms_kortingscodes.php";
	$cms->settings[29]["log"]["active"]=true;
	$cms->db[29]["maintable"]="kortingscode";

	# 30 = aanbieding_accommodatie
	$cms->settings[30]["types"]="accommodaties";
	$cms->settings[30]["type_single"]="accommodatie";
	$cms->settings[30]["file"]="cms_kortingscodes.php";
	$cms->settings[30]["log"]["active"]=true;
	$cms->db[30]["maintable"]="kortingscode_accommodatie";

	# 31 = aanbieding_type
	$cms->settings[31]["types"]="types";
	$cms->settings[31]["type_single"]="type";
	$cms->settings[31]["file"]="cms_kortingscodes.php";
	$cms->settings[31]["log"]["active"]=true;
	$cms->db[31]["maintable"]="kortingscode_type";

	# 32 = mailteksten "uitnodigen tot inloggen en opties bijboeken"

	if($_GET["wzt"]==2) {
		$cms->settings[32]["types"]="zomermailteksten";
		$cms->settings[32]["type_single"]="zomermailtekst";
	} else {
		$cms->settings[32]["types"]="wintermailteksten";
		$cms->settings[32]["type_single"]="wintermailtekst";
	}
	$cms->settings[32]["file"]="cms_mailteksten.php";
	$cms->settings[32]["log"]["active"]=true;
	$cms->db[32]["maintable"]="mailtekst";

	# 33 = bijkomendekosten
	$cms->settings[33]["types"]="bijkomende kosten";
	$cms->settings[33]["type_single"]="bijkomende kosten";
	$cms->settings[33]["file"]="cms_bijkomendekosten.php";
	$cms->settings[33]["log"]["active"]=true;
	$cms->db[33]["maintable"]="bijkomendekosten";

	# 34 = garanties
	if($_GET["status"]==2) {
		$cms->settings[34]["types"]="gebruikte garanties";
		$cms->settings[34]["type_single"]="gebruikte garantie";
	} elseif($_GET["status"]==3) {
		$cms->settings[34]["types"]="verlopen garanties";
		$cms->settings[34]["type_single"]="verlopen garantie";
	} elseif($_GET["status"]==4) {
		$cms->settings[34]["types"]="garanties";
		$cms->settings[34]["type_single"]="garantie";
	} elseif($_GET["status"]==1) {
		$cms->settings[34]["types"]="ongebruikte garanties";
		$cms->settings[34]["type_single"]="ongebruikte garantie";
	} else {
		$cms->settings[34]["types"]="garanties";
		$cms->settings[34]["type_single"]="garantie";
	}
	if($_GET["volgnr"]=="leeg") {
		$cms->settings[34]["types"].=" zonder volgnummer";
		$cms->settings[34]["type_single"].=" zonder volgnummer";
	}
	$cms->settings[34]["file"]="cms_garanties.php";
	$cms->settings[34]["log"]["active"]=true;
	$cms->db[34]["maintable"]="garantie";

	# 35 = optieaanvraag
	if($_GET["status"]) {
		$cms->settings[35]["types"]="optie-aanvragen - ".$vars["optieaanvragen_status"][$_GET["status"]];
		$cms->settings[35]["type_single"]="optie-aanvraag - ".$vars["optieaanvragen_status"][$_GET["status"]];
	} else {
		$cms->settings[35]["types"]="optie-aanvragen";
		$cms->settings[35]["type_single"]="optie-aanvraag";
	}
	$cms->settings[35]["file"]="cms_optieaanvragen.php";
	$cms->settings[35]["log"]["active"]=true;
	$cms->db[35]["maintable"]="optieaanvraag";

	# 36 = thema's
	$cms->settings[36]["types"]="thema's";
	$cms->settings[36]["type_single"]="thema";
	$cms->settings[36]["file"]="cms_themas.php";
	$cms->settings[36]["log"]["active"]=true;
	$cms->db[36]["maintable"]="thema";

	# 37 = blokken hoofdpagina
	$cms->settings[37]["types"]="blokken hoofdpagina";
	$cms->settings[37]["type_single"]="blok hoofdpagina";
	$cms->settings[37]["file"]="cms_blokkenhoofdpagina.php";
	$cms->settings[37]["log"]["active"]=true;
	$cms->db[37]["maintable"]="blokhoofdpagina";

	# 38 = blokken met accommodaties (links op de pagina's)
	$cms->settings[38]["types"]="blokken links";
	$cms->settings[38]["type_single"]="blok links";
	$cms->settings[38]["file"]="cms_blokkenaccommodaties.php";
	$cms->settings[38]["log"]["active"]=true;
	$cms->db[38]["maintable"]="blokaccommodatie";

	# 39 = acties WebTastic
	$cms->settings[39]["types"]="acties";
	$cms->settings[39]["type_single"]="actie";
	$cms->settings[39]["file"]="cms_diversen.php";
	$cms->settings[39]["log"]["active"]=true;
	$cms->db[39]["maintable"]="actie";

	# 40 = nummers (hangt onder accommodaties > types)
#	$cms->settings[40]["types"]="nummers";
#	$cms->settings[40]["type_single"]="nummer";
#	$cms->settings[40]["file"]="cms_nummers.php";
#	$cms->settings[40]["log"]["active"]=true;
#	$cms->db[40]["maintable"]="nummer";

	# 41 = aanbieding_leverancier
	$cms->settings[41]["types"]="leveranciers";
	$cms->settings[41]["type_single"]="leverancier";
	$cms->settings[41]["file"]="cms_aanbiedingen.php";
	$cms->settings[41]["log"]["active"]=true;
	$cms->db[41]["maintable"]="aanbieding_leverancier";

	# 42 = leverancier_sub
	$cms->settings[42]["types"]="sub-leveranciers";
	$cms->settings[42]["type_single"]="sub-leverancier";
	$cms->settings[42]["file"]="cms_leveranciers_sub.php";
	$cms->settings[42]["log"]["active"]=true;
	$cms->db[42]["maintable"]="leverancier_sub";

	# 43 = boeking_betaling_lev
	$cms->settings[43]["types"]="inkoopbetalingen";
	$cms->settings[43]["type_single"]="inkoopbetaling";
	$cms->settings[43]["file"]="cms_boekingen_betalingen_lev.php";
#	$cms->settings[43]["log"]["active"]=true;
	$cms->db[43]["maintable"]="boeking_betaling_lev";

	# 44 = blog
	$cms->settings[44]["types"]="blog-items";
	$cms->settings[44]["type_single"]="blog-item";
	$cms->settings[44]["file"]="cms_blog.php";
	$cms->settings[44]["log"]["active"]=true;
	$cms->db[44]["maintable"]="blog";

	# 45 = blog_plaats
	$cms->settings[45]["types"]="gekoppelde plaatsen";
	$cms->settings[45]["type_single"]="gekoppelde plaats";
	$cms->settings[45]["file"]="cms_blog.php";
	$cms->settings[45]["log"]["active"]=true;
	$cms->db[45]["maintable"]="blog_plaats";

	# 46 = blog_skigebied
	$cms->settings[46]["types"]="gekoppelde regio's";
	$cms->settings[46]["type_single"]="gekoppelde regio";
	$cms->settings[46]["file"]="cms_blog.php";
	$cms->settings[46]["log"]["active"]=true;
	$cms->db[46]["maintable"]="blog_skigebied";

	# 47 = blog_reactie
	$cms->settings[47]["types"]="blog-reacties";
	$cms->settings[47]["type_single"]="blog-reactie";
	$cms->settings[47]["file"]="cms_blog_reactie.php";
	$cms->settings[47]["log"]["active"]=true;
	$cms->db[47]["maintable"]="blog_reactie";

	# 48 = accommodatie_review
	$cms->settings[48]["types"]="accommodatie-reviews";
	$cms->settings[48]["type_single"]="accommodatie-review";
	$cms->settings[48]["file"]="cms_accommodatie_reviews.php";
	$cms->settings[48]["log"]["active"]=true;
	$cms->db[48]["maintable"]="accommodatie_review";

	# Aankomstdata vullen (voor CMS)
	$db->query("SELECT seizoen_id, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen ORDER BY begin, eind;");
	if($db->num_rows()) {
		if($id=="accommodaties") $vars["aankomstdatum_weekend"][0]=$vars["geenvoorkeur"];
		while($db->next_record()) {
			# Aankomstdatum-array vullen
			$timeteller=$db->f("begin");
			while($timeteller<=$db->f("eind")) {
				$vars["aankomstdatum"][$db->f("seizoen_id")][$timeteller]=datum("DAG D MAAND JJJJ",$timeteller);
				$vars["aankomstdatum_kort"][$db->f("seizoen_id")][$timeteller]=datum("D MAAND JJJJ",$timeteller);
				$vars["aankomstdatum_weekend"][$db->f("seizoen_id")][$timeteller]="Weekend ".date("j",$timeteller)." ".datum("MAAND JJJJ",$timeteller);
				$vars["aankomstdatum_weekend_alleseizoenen"][$timeteller]="weekend ".date("j",$timeteller)." ".datum("MAAND JJJJ",$timeteller);
				$timeteller=mktime(0,0,0,date("n",$timeteller),date("j",$timeteller)+7,date("Y",$timeteller));
			}
		}
	}
}

#
# cmslog_pagina opslaan
#
if($_SERVER["REQUEST_URI"] and is_object($login) and !$_POST and !$vars["cmslog_pagina_niet_opslaan"] and !$_GET["delete"] and !$_GET["delkid"]) {
	if($login->vars["cmslog_pagina"]) {
		if(is_object($layout)) {
			if($layout->pageid=="cms") {
				$cmslog_pagina_title="hoofdpagina CMS";
				if($_GET["cmssearch"]) {
					$cmslog_pagina_title="Zoekopdracht";
				}
			} else {
				if($layout->title[$layout->pageid]) {
					$cmslog_pagina_title=$layout->title[$layout->pageid];
				} else {
					$cmslog_pagina_title=$layout->menu_title[$layout->pageid];
				}
			}
		}
		$cmslog_pagina_userid=$login->user_id;
		$db->query("INSERT INTO cmslog_pagina SET url='".addslashes($_SERVER["REQUEST_URI"])."', title='".addslashes($cmslog_pagina_title)."', user_id='".addslashes($cmslog_pagina_userid)."', savedate=NOW();");
		if(is_object($layout)) {
			$layout->cmslog_pagina_id=$db->insert_id();
			$layout->cmslog_pagina_title=$cmslog_pagina_title;
		}
	}
}

function cmslog_pagina_title($title) {
	global $layout;
	$db=new DB_sql;
	if(is_object($layout) and $layout->cmslog_pagina_id) {
		$db->query("UPDATE cmslog_pagina SET title='".addslashes($title)."' WHERE cmslog_pagina_id='".addslashes($layout->cmslog_pagina_id)."';");
	}
}

# $vars specifiek voor het CMS
$vars["zaterdag_over_6_weken"]=mktime(0,0,0,date("m"),date("d")+(6-date("w"))+(6*7),date("Y"));
$vars["zaterdag_over_8_weken"]=mktime(0,0,0,date("m"),date("d")+(6-date("w"))+(8*7),date("Y"));
$vars["bijkomendekosten_perboekingpersoon"]=array(1=>"per boeking",2=>"per persoon");
$vars["bijkomendekosten_gekoppeldaan"]=array(1=>"accommodaties/types",2=>"skipassen",3=>"opties");
$vars["status_bestaandeklanten"]=array(0=>"mail sturen",1=>"mail verstuurd",2=>"nabellen",3=>"opnieuw mailen",4=>"optie bij ons aangevraagd",5=>"geboekt bij ons",6=>"elders geboekt",7=>"geen belangstelling");
$vars["themakleurencombinatie"]=array(1=>"kleurencombinatie 1",2=>"kleurencombinatie 2",3=>"kleurencombinatie 3",4=>"kleurencombinatie 4",5=>"kleurencombinatie 5",6=>"kleurencombinatie 6",7=>"kleurencombinatie 7",8=>"kleurencombinatie 8",9=>"kleurencombinatie 9");
$vars["standaardbestelmanier"]=array(1=>"via e-mail",2=>"via fax",3=>"via XML",4=>"via inlog op website");
$vars["bevestigmethode"]=array(1=>"stuurt direct een factuur",2=>"bevestigt zonder reserveringsnummer",3=>"bevestigt met reserveringsnummer");
#$vars["nummers_voorraad_velden"]=array("voorraad_garantie","voorraad_allotment","voorraad_vervallen_allotment","voorraad_optie_leverancier","voorraad_xml","voorraad_request","voorraad_optie_klant","voorraad_bijwerken");
# zonder XML:
$vars["nummers_voorraad_velden"]=array("voorraad_garantie","voorraad_allotment","voorraad_vervallen_allotment","voorraad_optie_leverancier","voorraad_request","voorraad_optie_klant","voorraad_bijwerken");
$vars["bestelstatus"]=array(1=>"nog niet besteld",2=>"bevestiging afwachten",3=>"bevestigd");
$vars["factuurbedrag_gecontroleerd"]=array(1=>"ja, alles klopt",2=>"nee, bedrag komt niet overeen");
$vars["optiecategorie"]=array(1=>"n.v.t.",2=>"bijkomende kosten verblijf",3=>"skipassen",4=>"huurmateriaal",5=>"skilessen",6=>"catering/maaltijden",7=>"vervoer",8=>"verzekeringen",9=>"aanbiedingskortingen + klachtafhandeling",20=>"overig");
$vars["inkoopbetaling_status"]=array(1=>"onderweg",2=>"ingeboekt");
$vars["wysiwyg_info"]="gebruik voor speciale opmaak:\n\nbold: [b]tekst[/b]\n\nitalics: [i]tekst[/i]\n\ninterne link: [links=/accommodatie/I4529/]tekst[/link]\n\nexterne link: [link=http://www.test.com/]tekst[/link]\n\n";
$vars["accommodatie_review_bron"]=array(1=>"door eigen klant ingevulde enquête",2=>"Posarelli");
$vars["enquetestatus"]=array(0=>"nog controleren",2=>"nog controleren door Bert/Barteld",1=>"goedgekeurd",3=>"afgekeurd");


# Soorten hoofdpagina-meldingen / rollen
$vars["cms_hoofdpagina_soorten"]=array(
	11=>"boekingen",
	12=>"tarieven",
	13=>"vertalingen",
	14=>"accommodatiegegevens",
	15=>"te verzenden mailtjes",
	16=>"betalingen",
	17=>"aanbiedingen",
	18=>"(gereserveerd 3)",
	19=>"(gereserveerd 4)",
	20=>"(gereserveerd 5)"
);
asort($vars["cms_hoofdpagina_soorten"]);

# Privileges
$vars["priv"]=array(
	1=>"Gebruikers beheren",
	2=>"Verwijderen boeking",
	3=>"Financiële gegevens beheren (export verkoopboekingen+debiteuren, xml-import betalingen)",
	5=>"Inkomende betalingen bewerken + uitgaande betalingen inzien en beheren",
	6=>"Opties wijzigen bij accommodaties met actieve boekingen",
	7=>"Commissie per reisbureau aanpassen",
	8=>"Zoekstatistieken inzien",
	9=>"Garanties beheren",
	10=>"WebTastic-acties: alles inzien",
	21=>"Onderdelen CMS-hoofdpagina beheren",
	22=>"Adressen bij boekingen inzien",
	23=>"Reisbureaus beheren",
	24=>"Bestaande klanten inzien",
	25=>"Lijsten bij \"Overzichten > Lijsten\" inzien",
	26=>"WebTastic-acties: prioriteit wijzigen",
	27=>"Totaaloverzicht financiën kunnen opvragen (alleen binnen kantoor)",
	28=>"Totaaloverzicht financiën kunnen opvragen (buiten kantoor)",
); # LET OP! Bij doortellen rekening houden met $vars["cms_hoofdpagina_soorten"]

while(list($key,$value)=each($vars["cms_hoofdpagina_soorten"])) {
	if(!preg_match("/\(gereserveerd/",$value)) {
		$vars["priv"][$key]="CMS-hoofdpagina: ".$value;
	}
}
asort($vars["priv"]);


# Actielijst
$vars["actielijst_soort"]=array(1=>"concrete opdracht",2=>" foutmelding",3=>"wens/idee",);

#$vars["actielijst_status"]=array(1=>"Chalet.nl moet er nog een beslissing over nemen",13=>"Nog te bespreken",2=>"Ingediend bij WebTastic",3=>"WebTastic moet nog aanvullend onderzoek doen",4=>"WebTastic voert de volgende werkzaamheden uit op volgorde van prioriteit",5=>"WebTastic is ermee bezig",6=>"WebTastic wacht op antwoord",7=>"WebTastic wacht op antwoord van derden",8=>"WebTastic wacht op afronding van een ander onderdeel",9=>"Gedeeltelijk opgeleverd; goedkeuring gewenst",10=>"Opgeleverd; goedkeuring gewenst",11=>"Afgerond",12=>"Vervallen"); # volgorde cijfers niet 1, 2, 3 etc
$vars["actielijst_status"]=array(2=>"Volgens prioriteit",1=>"WebTastic is nu bezig met",7=>"WebTastic wacht op een reactie",3=>"In de wacht",4=>"Toekomstwens / nog te bespreken",6=>"Vervallen",5=>"Afgerond"); # volgorde key is niet 1, 2, 3 etc

function mailtekst_opties($boekingid) {
	global $vars,$txt,$txta,$gegevens;
	$db=new DB_sql;
	if($boekingid) {
		$gegevens=get_boekinginfo($boekingid);
		$taal=$gegevens["stap1"]["taal"];

		$return["subject"]="[".$gegevens["stap1"]["boekingsnummer"]."] ".$txt[$taal]["vars"]["mailopties_wzt".$gegevens["stap1"]["accinfo"]["wzt"]."_vakantie"]." ".$gegevens["stap1"]["accinfo"]["plaats"];
		$return["from"]=$gegevens["stap1"]["website_specifiek"]["email"];
		$return["fromname"]=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
		$return["boekingsnummer"]=$gegevens["stap1"]["boekingsnummer"];
		$return["plaats"]=$gegevens["stap1"]["accinfo"]["plaats"];
		$return["to"]=$gegevens["stap2"]["email"];
		$return["mailverstuurd_opties"]=$gegevens["stap1"]["mailverstuurd_opties"];

		if($gegevens["stap1"]["mailtekst_opties"]) {
			$return["body"].=$gegevens["stap1"]["mailtekst_opties"];
			$return["bewerkt"]=true;
		} else {
			$return["body"]=$txt[$taal]["vars"]["mailopties_wzt".$gegevens["stap1"]["accinfo"]["wzt"]."_1"]."\n\n";

			# Kijken of er voor deze accommodatie een specifieke tekst toegevoegd moet worden
			if($gegevens["stap1"]["accinfo"]["mailtekst_id"]) {
				if($taal<>"nl") {
					$ttv="_".$taal;
				}
				$db->query("SELECT tekst".$ttv." AS tekst FROM mailtekst WHERE mailtekst_id='".$gegevens["stap1"]["accinfo"]["mailtekst_id"]."';");
				if($db->next_record()) {
					$return["body"].=trim($db->f("tekst"))."\n\n";
				}
			}

			$return["body"].=$txt[$taal]["vars"]["mailopties_wzt".$gegevens["stap1"]["accinfo"]["wzt"]."_2"];


			# Wachtwoord invullen
			$db->query("SELECT password_uc FROM boekinguser WHERE user='".addslashes($gegevens["stap2"]["email"])."';");
			if($db->next_record()) {
				$return["body"]=ereg_replace("\[WACHTWOORD\]",$db->f("password_uc"),$return["body"]);
			} else {
				$return["body"]=ereg_replace("\(\[WACHTWOORD\]\) ","",$return["body"]);
				$return["body"]=ereg_replace("\[WACHTWOORD\]","",$return["body"]);
			}

			# Link naar verzendmethode_reisdocumenten invullen
			$url=$vars["websites_basehref"][$gegevens["stap1"]["website"]]."verzendmethode.php?bid=".$boekingid."&c=".substr(sha1("ldlklKDKLk".$boekingid."JJJdkkk4uah!"),0,8);
			$return["body"]=ereg_replace("\[VERZENDMETHODE_REISDOCUMENTEN_URL\]",$url,$return["body"]);

			# Gegevens overzetten
			$return["body"]=ereg_replace("\[NAAM\]",trim($gegevens["stap2"]["voornaam"]),$return["body"]);
			$return["body"]=ereg_replace("\[PLAATS\]",$gegevens["stap1"]["accinfo"]["plaats"],$return["body"]);
			$return["body"]=ereg_replace("\[DATUM\]",DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$taal),$return["body"]);
			$return["body"]=ereg_replace("\[LINK\]",$vars["websites_basehref"][$gegevens["stap1"]["website"]].$txta[$taal]["menu_inloggen"].".php",$return["body"]);
			$return["body"]=ereg_replace("\[VERZEKERINGLINK\]",$vars["websites_basehref"][$gegevens["stap1"]["website"]].$txta[$taal]["menu_verzekeringen"].".php",$return["body"]);
			$return["body"]=ereg_replace("\[WEBSITE\]",$gegevens["stap1"]["website_specifiek"]["websitenaam"],$return["body"]);
		}


		return $return;
	} else {
		return false;
	}
}

function mailtekst_persoonsgegevens($boekingid,$gewenst,$reminder=false) {
	global $vars,$txt,$txta,$gegevens;
	if($boekingid) {
		$taal=$gegevens["stap1"]["taal"];
		$return["subject"]="[".$gegevens["stap1"]["boekingsnummer"]."] ".$txt[$taal]["vars"]["mailpersoonsgegevens_subject_wzt".$gegevens["stap1"]["accinfo"]["wzt"]]." ".$gegevens["stap1"]["accinfo"]["plaats"];
		$return["from"]=$gegevens["stap1"]["website_specifiek"]["email"];
		$return["fromname"]=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
		$return["boekingsnummer"]=$gegevens["stap1"]["boekingsnummer"];
		$return["plaats"]=$gegevens["stap1"]["accinfo"]["plaats"];
		$return["to"]=$gegevens["stap2"]["email"];

#		echo "mailpersoonsgegevens_".($reminder ? "reminder_" : "")."wzt".$gegevens["stap1"]["accinfo"]["wzt"];

		$return["body"]=$txt[$taal]["vars"]["mailpersoonsgegevens_".($reminder ? "reminder_" : "")."wzt".$gegevens["stap1"]["accinfo"]["wzt"]];

		if($return["body"]) {
			@ksort($gewenst["tekst"]);
			while(list($key,$value)=@each($gewenst["tekst"])) {
				$gewenst_tekst.=$value."\n";
			}
			$return["body"]=ereg_replace("\[GEGEVENS\]",trim($gewenst_tekst),$return["body"]);

			# Gegevens overzetten
			$return["body"]=ereg_replace("\[NAAM\]",$gegevens["stap2"]["voornaam"],$return["body"]);
			$return["body"]=ereg_replace("\[PLAATS\]",$gegevens["stap1"]["accinfo"]["plaats"],$return["body"]);
			$return["body"]=ereg_replace("\[DATUM\]",DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$taal),$return["body"]);
			$return["body"]=ereg_replace("\[LINK\]",$vars["websites_basehref"][$gegevens["stap1"]["website"]].$txta[$taal]["menu_inloggen"].".php",$return["body"]);
			$return["body"]=ereg_replace("\[WEBSITE\]",$gegevens["stap1"]["website_specifiek"]["websitenaam"],$return["body"]);
			return $return;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function mailtekst_klanten_vorig_seizoen($boekingid) {
	global $db,$vars,$txt,$txta,$gegevens,$login;
	if($boekingid) {
		$gegevens=get_boekinginfo($boekingid);
		$taal=$gegevens["stap1"]["taal"];

		$db->query("SELECT s.naam FROM seizoen s, diverse_instellingen d WHERE ".($gegevens["stap1"]["accinfo"]["wzt"]==2 ? "zomer" : "winter")."_huidig_seizoen_id=seizoen_id AND d.diverse_instellingen_id=1;");
		if($db->next_record()) {
			$seizoennaam=$db->f("naam");
		}

		$return["subject"]=$txt[$taal]["vars"]["mail_klanten_vorig_seizoen_subject_".$gegevens["stap1"]["accinfo"]["wzt"]];

		$return["from"]=$gegevens["stap1"]["website_specifiek"]["email"];
		$return["fromname"]=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
		$return["boekingsnummer"]=$gegevens["stap1"]["boekingsnummer"];
#		$return["plaats"]=$gegevens["stap1"]["accinfo"]["plaats"];
		$return["to"]=$gegevens["stap2"]["email"];
		$return["mailverstuurd_klanten_vorig_seizoen"]=$gegevens["stap1"]["mailverstuurd_klanten_vorig_seizoen"];
		$return["status_klanten_vorig_seizoen"]=$gegevens["stap1"]["status_klanten_vorig_seizoen"];

		$return["subject"]=ereg_replace("\[SEIZOEN\]",$seizoennaam,$return["subject"]);

		if($gegevens["stap1"]["mailtekst_klanten_vorig_seizoen"]) {
			$return["body"].=$gegevens["stap1"]["mailtekst_klanten_vorig_seizoen"];
			$return["bewerkt"]=true;
		} else {
			if($gegevens["stap1"]["accinfo"]["wzt"]==1 and $gegevens["stap1"]["website"]=="W" or $gegevens["stap1"]["website"]=="Q" or $gegevens["stap1"]["website"]=="V") {
				# tekst zonder link naar thema gebruiken
				$return["body"]=$txt[$taal]["vars"]["mail_klanten_vorig_seizoen_1_zonderthema"];
			} else {
				$return["body"]=$txt[$taal]["vars"]["mail_klanten_vorig_seizoen_".$gegevens["stap1"]["accinfo"]["wzt"]];
			}

			# Gegevens overzetten
			$return["body"]=ereg_replace("\[NAAM\]",$gegevens["stap2"]["voornaam"],$return["body"]);
			$return["body"]=ereg_replace("\[OPTIEDAGEN\]",$gegevens["stap1"]["accinfo"]["optiedagen_klanten_vorig_seizoen"],$return["body"]);
#			$return["body"]=ereg_replace("\[DATUM\]",DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$taal),$return["body"]);
			$return["body"]=ereg_replace("\[EERDERE_BOEKING\]",DATUM("D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$taal)." - ".$gegevens["stap1"]["accinfo"]["plaats"]." / ".ucfirst($gegevens["stap1"]["accinfo"]["soortaccommodatie"])." ".$gegevens["stap1"]["accinfo"]["naam_ap"],$return["body"]);
			$return["body"]=ereg_replace("\[ACCOMMODATIELINK\]",($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? "http://ss.postvak.net/chalet/" : $vars["websites_basehref"][$gegevens["stap1"]["website"]]).$gegevens["stap1"]["accinfo"]["url_zonderpad"],$return["body"]);

			$return["body"]=ereg_replace("\[LINK\]",($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" ? "http://ss.postvak.net/chalet/" : $vars["websites_basehref"][$gegevens["stap1"]["website"]])."rebook.php?bid=".$boekingid."&c=".substr(sha1($boekingid."_WT_488439fk3"),0,8),$return["body"]);
			$return["body"]=ereg_replace("\[WEBSITE\]",$gegevens["stap1"]["website_specifiek"]["websitenaam"],$return["body"]);
#			$return["body"]=ereg_replace("\[NAAM_MEDEWERKER\]",wt_naam($login->vars["voornaam"],$login->vars["tussenvoegsel"],$login->vars["achternaam"]),$return["body"]);
			$return["body"]=ereg_replace("\[NAAM_MEDEWERKER\]",$login->vars["voornaam"],$return["body"]);
			$return["body"]=ereg_replace("\[EMAIL\]",$gegevens["stap1"]["website_specifiek"]["email"],$return["body"]);
			$return["body"]=ereg_replace("\[BASEHREF\]",$gegevens["stap1"]["website_specifiek"]["basehref"],$return["body"]);
		}
		return $return;
	} else {
		return false;
	}
}

function mailtekst_verzendmethode_reisdocumenten($boekingid) {
	global $vars,$txt,$txta,$gegevens;
	if($boekingid) {
		$taal=$gegevens["stap1"]["taal"];
		$return["subject"]="[".$gegevens["stap1"]["boekingsnummer"]."] ".$txt[$taal]["vars"]["mailverzendmethode_reisdocumenten_subject_wzt".$gegevens["stap1"]["accinfo"]["wzt"]];
		$return["from"]=$gegevens["stap1"]["website_specifiek"]["email"];
		$return["fromname"]=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
		$return["boekingsnummer"]=$gegevens["stap1"]["boekingsnummer"];
		$return["plaats"]=$gegevens["stap1"]["accinfo"]["plaats"];
		$return["to"]=$gegevens["stap2"]["email"];

		$return["body"]=$txt[$taal]["vars"]["mailverzendmethode_reisdocumenten_wzt".$gegevens["stap1"]["accinfo"]["wzt"]];

		if($return["body"] and strlen($return["body"])>10) {

			# Gegevens overzetten
			$return["body"]=ereg_replace("\[NAAM\]",trim($gegevens["stap2"]["voornaam"]),$return["body"]);
			$return["body"]=ereg_replace("\[PLAATS\]",$gegevens["stap1"]["accinfo"]["plaats"],$return["body"]);
			$return["body"]=ereg_replace("\[DATUM\]",DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$taal),$return["body"]);
			$return["body"]=ereg_replace("\[LINK\]",$vars["websites_basehref"][$gegevens["stap1"]["website"]].$txta[$taal]["menu_inloggen"].".php",$return["body"]);
			$return["body"]=ereg_replace("\[WEBSITE\]",$gegevens["stap1"]["website_specifiek"]["websitenaam"],$return["body"]);

			# Link naar verzendmethode_reisdocumenten invullen
			$url=$vars["websites_basehref"][$gegevens["stap1"]["website"]]."verzendmethode.php?bid=".$boekingid."&c=".substr(sha1("ldlklKDKLk".$boekingid."JJJdkkk4uah!"),0,8);
			$return["body"]=ereg_replace("\[VERZENDMETHODE_REISDOCUMENTEN_URL\]",$url,$return["body"]);

			# uiterlijke datum dat de voorkeur mag worden doorgegeven invullen
			$uiterlijke_datum=mktime(0,0,0,date("m",$gegevens["stap1"]["aankomstdatum_exact"]),date("d",$gegevens["stap1"]["aankomstdatum_exact"])-intval($gegevens["stap1"]["wijzigen_dagen"]),date("Y",$gegevens["stap1"]["aankomstdatum_exact"]));
			$return["body"]=ereg_replace("\[UITERLIJKE_DATUM\]",DATUM("DAG D MAAND JJJJ",$uiterlijke_datum,$taal),$return["body"]);

			return $return;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function persoonsgegevensgewenst($gegevens) {
	# Checken of er nog persoonsgegevens moeten worden ingevuld
	global $vars,$txt,$txta;
	$taal=$gegevens["stap1"]["taal"];

	for($i=1;$i<=$gegevens["stap1"]["aantalpersonen"];$i++) {
		# Arrangement incl. skipas
		if($gegevens["stap1"]["accinfo"]["toonper"]==1 and !$gegevens["stap1"]["wederverkoop"]) {
			if(!$gegevens["stap3"][$i]["voornaam"] or !$gegevens["stap3"][$i]["achternaam"]) {
				if(!$gegevens["stap4"]["arrangement_zonder_skipas"][$i]) {
					$mail_versturen=true;
					if($i==1) {
						$return["tekst"][$i]=$txt[$taal]["bsys"]["gewenst_hoofdboeker"];
					} else {
						$return["tekst"][$i]=$txt[$taal]["bsys"]["gewenst_persoon"]." ".$i;
					}
					$return["tekst"][$i].=": ".$txt[$taal]["bsys"]["gewenst_naam"];
				}
			}
		}

		# Opties
		if($gegevens["stap4"]["persoonsgegevensgewenst"][$i]) {
			if(!$gegevens["stap3"][$i]["voornaam"] or !$gegevens["stap3"][$i]["achternaam"] or !$gegevens["stap3"]["geboortedatum_ingevuld"][$i]) {
				if(!$gegevens["stap4"]["arrangement_zonder_skipas"][$i]) {
					$mail_versturen=true;
					if($i==1) {
						$return["tekst"][$i]=$txt[$taal]["bsys"]["gewenst_hoofdboeker"];
					} else {
						$return["tekst"][$i]=$txt[$taal]["bsys"]["gewenst_persoon"]." ".$i;
					}
					$return["tekst"][$i].=": ".$txt[$taal]["bsys"]["gewenst_naamgeboortedatum"];
				}
			}
		}

		# Annuleringsverzekering
		if($gegevens["stap3"][$i]["annverz"]) {
			if(!$gegevens["stap3"][$i]["voornaam"] or !$gegevens["stap3"][$i]["achternaam"] or !$gegevens["stap3"]["geboortedatum_ingevuld"][$i] or !$gegevens["stap3"][$i]["geslacht"]) {
				$mail_versturen=true;
				if($i==1) {
					$return["tekst"][$i]=$txt[$taal]["bsys"]["gewenst_hoofdboeker"];
				} else {
					$return["tekst"][$i]=$txt[$taal]["bsys"]["gewenst_persoon"]." ".$i;
				}
				$return["tekst"][$i].=": ".$txt[$taal]["bsys"]["gewenst_naamplaats"];
			}
		}
	}
	if($mail_versturen) {
		return $return;
	} else {
		return false;
	}
}

function mailtekst_aanmaning($boekingid,$soortbetaling,$bedrag,$voldaan) {
	global $db,$vars,$txt,$txta,$gegevens;
	if($boekingid) {
		$gegevens=get_boekinginfo($boekingid);
		$taal=$gegevens["stap1"]["taal"];
		$return["subject"]="[".$gegevens["stap1"]["boekingsnummer"]."] ".$txt[$taal]["vars"]["mailaanmaning_subject_".$soortbetaling]." ".$gegevens["stap1"]["accinfo"]["plaats"];
		$return["from"]=$gegevens["stap1"]["website_specifiek"]["email"];
		$return["fromname"]=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
		$return["boekingsnummer"]=$gegevens["stap1"]["boekingsnummer"];
		$return["plaats"]=$gegevens["stap1"]["accinfo"]["plaats"];

		if($gegevens["stap1"]["reisbureau_aanmaning_email_1"]) {
			# reisbureau
			if($gegevens["stap1"]["reisbureau_aanmaning_email_2"]) {
				$return["to_1"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"];
				$return["to_2"]=$gegevens["stap1"]["reisbureau_aanmaning_email_2"];
				$return["to_log"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"]." en ".$gegevens["stap1"]["reisbureau_aanmaning_email_2"];
			} else {
				$return["to_1"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"];
				$return["to_log"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"];
			}
		} else {
			$return["to"]=$gegevens["stap2"]["email"];
			$return["to_log"]=$gegevens["stap2"]["email"];
		}

#		$return["mailverstuurd_opties"]=$gegevens["stap1"]["mailverstuurd_opties"];
		$return["soortvakantie"]=$txt[$taal]["vars"]["mailaanmaning_soortvakantie_wzt".$gegevens["stap1"]["accinfo"]["wzt"]];
		$return["soortbetaling"]=$txt[$taal]["vars"]["mailaanmaning_soortbetaling_".$soortbetaling];
		$return["bedrag"]=number_format($bedrag,2,",",".");

		if($gegevens["stap1"]["aanmaning_tekst"]) {
			$return["body"].=$gegevens["stap1"]["aanmaning_tekst"];
			$return["bewerkt"]=true;
		} else {
			$return["body"]=$txt[$taal]["vars"]["mailaanmaning"]."\n\n";

			# Gegevens overzetten
			$return["body"]=ereg_replace("\[NAAM\]",$gegevens["stap2"]["voornaam"],$return["body"]);
			$return["body"]=ereg_replace("\[PLAATS\]",$gegevens["stap1"]["accinfo"]["plaats"],$return["body"]);
			$return["body"]=ereg_replace("\[DATUM\]",DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$taal),$return["body"]);
#			$return["body"]=ereg_replace("\[LINK\]",$vars["websites_basehref"][$gegevens["stap1"]["website"]].$txta[$taal]["menu_inloggen"].".php",$return["body"]);
			$return["body"]=ereg_replace("\[WEBSITE\]",$gegevens["stap1"]["website_specifiek"]["websitenaam"],$return["body"]);
			$return["body"]=ereg_replace("\[SOORTVAKANTIE\]",$return["soortvakantie"],$return["body"]);
			$return["body"]=ereg_replace("\[SOORTBETALING\]",$return["soortbetaling"],$return["body"]);
			$return["body"]=ereg_replace("\[BEDRAG\]",$return["bedrag"],$return["body"]);

			if($gegevens["stap1"]["reisbureau_user_id"]) {
				$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$gegevens["stap1"]["boekingsnummer"]." (".wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]).")",$return["body"]);
			} else {
				$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$gegevens["stap1"]["boekingsnummer"],$return["body"]);
			}

			$return["body"]=ereg_replace("\[BETALINGSINFO\]",betalingsinfo($gegevens,$voldaan),$return["body"]);
		}
		$return["subject"]=ereg_replace("\[SOORTVAKANTIE\]",$return["soortvakantie"],$return["subject"]);

		return $return;
	} else {
		return false;
	}
}

function mailtekst_ontvangenbetaling($boekingid,$bedrag,$datum) {
	global $db,$vars,$txt,$txta,$gegevens;
	if($boekingid) {
		$gegevens=get_boekinginfo($boekingid);
		$taal=$gegevens["stap1"]["taal"];

		$totaal=round($gegevens["stap1"]["totale_reissom"],2);
		$db->query("SELECT sum(bedrag) AS bedrag FROM boeking_betaling WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		if($db->next_record()) {
			$voldaan=$db->f("bedrag");
		}
		$voldaan=round($voldaan,2);
		$openstaand=$totaal-$voldaan;
		$openstaand=round($openstaand,2);

		$return["subject"]="[".$gegevens["stap1"]["boekingsnummer"]."] ".$txt[$taal]["vars"]["mailbetaling_subject"]." ".$gegevens["stap1"]["accinfo"]["plaats"];
		$return["from"]=$gegevens["stap1"]["website_specifiek"]["email"];
		$return["fromname"]=$gegevens["stap1"]["website_specifiek"]["websitenaam"];
		$return["boekingsnummer"]=$gegevens["stap1"]["boekingsnummer"];
#		$return["plaats"]=$gegevens["stap1"]["accinfo"]["plaats"];
#		$return["to"]=$gegevens["stap2"]["email"];
		if($gegevens["stap1"]["reisbureau_aanmaning_email_1"]) {
			# reisbureau
			if($gegevens["stap1"]["reisbureau_aanmaning_email_2"]) {
				$return["to_1"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"];
				$return["to_2"]=$gegevens["stap1"]["reisbureau_aanmaning_email_2"];
				$return["to_log"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"]." en ".$gegevens["stap1"]["reisbureau_aanmaning_email_2"];
			} else {
				$return["to_1"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"];
				$return["to_log"]=$gegevens["stap1"]["reisbureau_aanmaning_email_1"];
			}
		} else {
			$return["to"]=$gegevens["stap2"]["email"];
			$return["to_log"]=$gegevens["stap2"]["email"];
		}

#		$return["mailverstuurd_opties"]=$gegevens["stap1"]["mailverstuurd_opties"];
		$return["soortvakantie"]=$txt[$taal]["vars"]["mailaanmaning_soortvakantie_wzt".$gegevens["stap1"]["accinfo"]["wzt"]];
#		$return["bedrag"]=number_format($bedrag,2,",",".");

		#
		# Te betalen moet hoger dan 1 euro zijn (vanwege afrondingsverschillen) - 11 oktober 2011
		#
		if($openstaand>1) {
			$return["body"]=$txt[$taal]["vars"]["mailbetaling"]."\n\n";
		} else {
			$return["body"]=$txt[$taal]["vars"]["mailbetaling_voldaan"]."\n\n";
		}

		# Gegevens overzetten
		$return["body"]=ereg_replace("\[NAAM\]",$gegevens["stap2"]["voornaam"],$return["body"]);
#		$return["body"]=ereg_replace("\[PLAATS\]",$gegevens["stap1"]["accinfo"]["plaats"],$return["body"]);
		$return["body"]=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$datum,$taal),$return["body"]);
#		$return["body"]=ereg_replace("\[LINK\]",$vars["websites_basehref"][$gegevens["stap1"]["website"]].$txta[$taal]["menu_inloggen"].".php",$return["body"]);
		$return["body"]=ereg_replace("\[WEBSITE\]",$gegevens["stap1"]["website_specifiek"]["websitenaam"],$return["body"]);
		$return["body"]=ereg_replace("\[SOORTVAKANTIE\]",$return["soortvakantie"],$return["body"]);
#		$return["body"]=ereg_replace("\[SOORTBETALING\]",$return["soortbetaling"],$return["body"]);
		$return["body"]=ereg_replace("\[BEDRAG\]",number_format($bedrag,2,",","."),$return["body"]);

		if($gegevens["stap1"]["reisbureau_user_id"]) {
			$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$gegevens["stap1"]["boekingsnummer"]." (".wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]).")",$return["body"]);
		} else {
			$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$gegevens["stap1"]["boekingsnummer"],$return["body"]);
#			$return["body"]=ereg_replace("\[RESERVERINGSNUMMER\]",$gegevens["stap1"]["boekingsnummer"]." (".wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]).")",$return["body"]);
		}

		$return["body"]=ereg_replace("\[BETALINGSINFO\]",betalingsinfo($gegevens,$voldaan),$return["body"]);
		$return["subject"]=ereg_replace("\[SOORTVAKANTIE\]",$return["soortvakantie"],$return["subject"]);

		return $return;
	} else {
		return false;
	}
}

function betalingsinfo($gegevens,$voldaan) {
	global $db,$vars,$txt,$txta;

	$taal=$gegevens["stap1"]["taal"];

	$totaal=$gegevens["stap1"]["totale_reissom"];
	$openstaand=$totaal-$voldaan;
	$openstaand=$openstaand;

	# Totale reissom (voor test)
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		$return.="€ ".number_format($totaal,2,',','.')." Totale reissom\n";
	}

	# Voldaan
	if($voldaan>0) {
		$return.=ereg_replace("\[BEDRAG\]",number_format($voldaan,2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_ontvangenbedrag"])."\n";
	}

	# Aanbetaling 1
	if($gegevens["fin"]["aanbetaling"] and $voldaan<$gegevens["fin"]["aanbetaling"]) {
		$tevoldoen=$gegevens["fin"]["aanbetaling"]-$voldaan;
		$tevoldoen=round($tevoldoen,2);
		if($tevoldoen>0) {
			$return.=ereg_replace("\[BEDRAG\]",number_format($tevoldoen,2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_nogteontvangenaanbetaling"])."\n";

			$aanbetaling1_datum=mktime(0,0,0,date("m",$gegevens["stap1"]["bevestigdatum"]),date("d",$gegevens["stap1"]["bevestigdatum"])+$gegevens["stap1"]["aanbetaling1_dagennaboeken"],date("Y",$gegevens["stap1"]["bevestigdatum"]));

			if($aanbetaling1_datum<$gegevens["stap1"]["bevestigdatum"]) {
				$aanbetaling1_datum=$gegevens["stap1"]["bevestigdatum"];
			}

			$return=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$aanbetaling1_datum,$taal),$return);
			$getoond["aanbetaling1"]=true;
		}
	}

	# Aanbetaling 2
	if($gegevens["stap1"]["aanbetaling2"] and $voldaan<($gegevens["fin"]["aanbetaling"]+$gegevens["stap1"]["aanbetaling2"])) {
		if($voldaan>$gegevens["fin"]["aanbetaling"]) {
			$meer_dan_aanbetaling1=$voldaan-$gegevens["fin"]["aanbetaling"];
			$tevoldoen=$gegevens["stap1"]["aanbetaling2"]-$meer_dan_aanbetaling1;
		} else {
			$tevoldoen=$gegevens["stap1"]["aanbetaling2"];
		}
		$tevoldoen=round($tevoldoen,2);
		if($tevoldoen>0) {
			$return.=ereg_replace("\[BEDRAG\]",number_format($tevoldoen,2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_nogteontvangenaanbetaling"])."\n";
			$return=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$gegevens["stap1"]["aanbetaling2_datum"],$taal),$return);
			$getoond["aanbetaling2"]=true;
		}
	}

	# Eindbetaling
	$eindbetaling=$totaal-$gegevens["fin"]["aanbetaling"]-$gegevens["stap1"]["aanbetaling2"];
	if($eindbetaling>$openstaand) $eindbetaling=$openstaand;
	$eindbetaling=round($eindbetaling,2);

	#
	# Te betalen bedrag moet hoger dan 0.01 zijn (vanwege afrondingsverschillen) - 25 november 2010
	#
	if($eindbetaling>0.01) {
		$return.=ereg_replace("\[BEDRAG\]",number_format($eindbetaling,2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_nogteontvangeneindbetaling"])."\n";

		$eindbetaling_datum=mktime(0,0,0,date("m",$gegevens["stap1"]["aankomstdatum_exact"]),date("d",$gegevens["stap1"]["aankomstdatum_exact"])-$gegevens["stap1"]["totale_reissom_dagenvooraankomst"],date("Y",$gegevens["stap1"]["aankomstdatum_exact"]));

		# Nooit eerder dan boekingsmoment
		if($eindbetaling_datum<$gegevens["stap1"]["bevestigdatum"]) {
			$eindbetaling_datum=$gegevens["stap1"]["bevestigdatum"];
		}

		# Nooit eerder dan aanbetalingsdatum
		if($aanbetaling1_datum>0 and $eindbetaling_datum<$aanbetaling1_datum) {
			$eindbetaling_datum=$aanbetaling1_datum;
		}

		$return=ereg_replace("\[DATUM\]",DATUM("D MAAND JJJJ",$eindbetaling_datum,$taal),$return);
		$getoond["eindbetaling"]=true;
	}

	# Totaal nog te ontvangen
	#
	# Te betalen bedrag moet hoger dan 0.01 zijn (vanwege afrondingsverschillen) - 25 november 2010
	#
	if(@count($getoond)>1 and $openstaand>0.01) {
		$return.=ereg_replace("\[BEDRAG\]",number_format($openstaand,2,',','.'),$txt[$taal]["vars"]["mailbetalingsinfo_totaalnogteontvangen"])."\n";
		$getoond["totaal"]=true;
	}

	return $return;
}

function boekingkoptekst($gegevens,$voucherstatus=true) {
	global $vars;
	# Boekingsinfo bovenaan tonen in CMS
	if($gegevens) {
		$db=new DB_sql;
		$db->query("SELECT leverancier_id, naam FROM leverancier WHERE leverancier_id='".addslashes($gegevens["stap1"]["leverancierid"])."';");
		if($db->next_record()) {
			$temp["leverancier_naam"]=$db->f("naam");
		}
		$return.="<table cellspacing=\"0\" style=\"width:100%;font-weight:bold;background-color:#0d3e88;color:#ffffff;border: solid #0d3e88 1px;padding: 2px;\" border=\"0\">";
		$return.="<tr><td style=\"width:700px;\">";
		if($gegevens["stap1"]["goedgekeurd"]) {
			$return.="Boeking ".($gegevens["stap1"]["boekingsnummer"] ? $gegevens["stap1"]["boekingsnummer"] : "");
		} else {
			$return.="Aanvraagnummer ".$gegevens["stap1"]["boekingid"];
		}
		if($gegevens["stap2"]["achternaam"]) $return.=" - ".htmlentities(wt_naam($gegevens["stap2"]["voornaam"],$gegevens["stap2"]["tussenvoegsel"],$gegevens["stap2"]["achternaam"]));
		if($gegevens["stap1"]["reisbureau_naam"]) $return.=" (via ".htmlentities($gegevens["stap1"]["reisbureau_naam"]).")";

		# Laatste wijziging
		$db->query("SELECT UNIX_TIMESTAMP(datum) AS datum FROM factuur WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."' ORDER BY datum DESC LIMIT 0,1;");
		if($db->next_record()) {
			$laatstefactuur=date("d-m-Y",$db->f("datum"));
		}
		if(!$laatstefactuur) $laatstefactuur="-";

		$return.="</td><td align=\"right\">Laatste factuur</td><td>&nbsp;&nbsp;</td><td>".htmlentities($laatstefactuur)."</td></tr>";
		$return.="<tr><td style=\"width:700px;\">";
		$return.=htmlentities($gegevens["stap1"]["accinfo"]["begincode"].$gegevens["stap1"]["accinfo"]["type_id"]." - ".ucfirst($gegevens["stap1"]["accinfo"]["soortaccommodatie"])." ".$gegevens["stap1"]["accinfo"]["naam"]." (".$gegevens["stap1"]["accinfo"]["optimaalaantalpersonen"].($gegevens["stap1"]["accinfo"]["optimaalaantalpersonen"]<>$gegevens["stap1"]["accinfo"]["maxaantalpersonen"] ? "-".$gegevens["stap1"]["accinfo"]["maxaantalpersonen"] : "")."p.)");
#		if($temp["leverancier_naam"]) $return.=" - ".htmlentities($temp["leverancier_naam"]);
		$return.="</td><td align=\"right\" nowrap>Mail optiesbijboeken</td><td>&nbsp;&nbsp;</td><td>".htmlentities(($gegevens["stap1"]["mailverstuurd_opties"] ? date("d-m-Y",$gegevens["stap1"]["mailverstuurd_opties"]) : "-"))."</td></tr>";
		$return.="<tr><td style=\"width:700px;\">";
#		$return.=DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$vars["taal"])." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum_exact"],$vars["taal"]);
		if($temp["leverancier_naam"]) $return.="Leverancier: ".htmlentities($temp["leverancier_naam"]); else $return.="&nbsp;";
		$return.="</td>";

		# Openstaand bedrag
#		$nog_te_betalen=$gegevens["fin"]["totale_reissom"];
		$nog_te_betalen=round($gegevens["stap1"]["totale_reissom"],2);
		$db->query("SELECT bedrag, UNIX_TIMESTAMP(datum) AS datum FROM boeking_betaling WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		while($db->next_record()) {
			$nog_te_betalen=round($nog_te_betalen-$db->f("bedrag"),2);
		}
		$vars["temp_openstaand"]=$nog_te_betalen;
		$vars["temp_openstaand_minus_goedgekeurd"]=round($nog_te_betalen-$gegevens["stap1"]["goedgekeurde_betaling"],2);

		$return.="<td align=\"right\" nowrap>&nbsp;Openstaand bedrag</td><td>&nbsp;&nbsp;</td><td>&euro; ".number_format($nog_te_betalen,2,',','.')."</td></tr>";
#		if($gegevens["stap1"]["voucherstatus"] and $voucherstatus) $return.="<tr><td>&nbsp;</td><td align=\"right\">Voucherstatus</td><td>&nbsp;&nbsp;</td><td nowrap>".$vars["voucherstatus"][$gegevens["stap1"]["voucherstatus"]]."</td></tr>";
		$return.="<tr><td>";
		$return.=DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["aankomstdatum_exact"],$vars["taal"])." - ".DATUM("DAG D MAAND JJJJ",$gegevens["stap1"]["vertrekdatum_exact"],$vars["taal"]);
		$return.="</td><td align=\"right\">Voucherstatus</td><td>&nbsp;&nbsp;</td><td nowrap>".$vars["voucherstatus"][$gegevens["stap1"]["voucherstatus"]]."</td></tr>";

		$return.="</table>";

		# balk met inkoop en marge
		$return.="<table cellspacing=\"0\" class=\"margebalk\" border=\"0\">";
		$reissom_tabel=reissom_tabel($gegevens,$gegevens["stap1"]["accinfo"],"",true);

#echo wt_dump($reissom_tabel["bedragen"]["optieinkoop"]);

		$factuur_bedrag_wijkt_af=0;
		$verschil=0;
		if($gegevens["stap1"]["geannuleerd"]) {
			$return.="<tr style=\"background-color:red;text-align:center;\"><td colspan=\"15\">deze boeking is geannuleerd</td></tr>";
		}

		if($gegevens["stap1"]["totale_reissom"]>0 and $gegevens["fin"]["totale_reissom"]>0) {
			$verschil=round($gegevens["stap1"]["totale_reissom"]-$gegevens["fin"]["totale_reissom"],2);
		}
		if($verschil<>0 and $gegevens["stap1"]["totale_reissom_inkoop"]) {
			#
			# Verschil
			#

			# marge oude bedragen bepalen
			$marge_euro_oud=$gegevens["stap1"]["totale_reissom"]-$gegevens["stap1"]["totale_reissom_inkoop"];
			if($gegevens["stap1"]["totale_reissom"]) {
				$marge_percentage_oud=($gegevens["stap1"]["totale_reissom"]-$gegevens["stap1"]["totale_reissom_inkoop"])/$gegevens["stap1"]["totale_reissom"]*100;
			} elseif($reissom_tabel["bedragen"]["inkoop"]) {
				$marge_percentage_oud=-100;
			} else {
				$marge_percentage_oud=0;
			}

			if($marge_euro_oud<0) {
				$margebgcolor="red";
			} else {
				$margebgcolor="";
			}

			$return.="<tr>";
			$return.="<td style=\"text-align:left;width:70px;\">Huidig</td><td style=\"text-align:left;width:50px;\">Verkoop:</td><td style=\"text-align:left;width:1px;\">&euro;</td><td colspan=\"2\" style=\"text-align:right;width:1px;\">".number_format($gegevens["stap1"]["totale_reissom"],2,",",".")."</td><td style=\"width:200px;\">&nbsp;</td>";
			$return.="<td style=\"text-align:left;\">Inkoop:</td><td style=\"text-align:left;width:1px;\">&euro;</td><td colspan=\"2\" style=\"text-align:right;width:1px;\">".number_format($gegevens["stap1"]["totale_reissom_inkoop"],2,",",".")."</td><td style=\"width:200px;\">&nbsp;</td>";
			$return.="<td style=\"text-align:right;background-color:".$margebgcolor."\">Marge:</td><td style=\"text-align:left;width:1px;background-color:".$margebgcolor."\">&euro;</td><td style=\"text-align:right;width:1px;background-color:".$margebgcolor."\">".number_format($marge_euro_oud,2,",",".")."</td><td style=\"text-align:right;background-color:".$margebgcolor."\">(".number_format($marge_percentage_oud,2,",",".")."%)</td>";
			$return.="</tr>";

			if($marge_euro_oud<$reissom_tabel["bedragen"]["marge_euro"]) {
				# nieuwe marge is hoger
				$bgcolor="#c2d69a";
			} elseif($marge_euro_oud>$reissom_tabel["bedragen"]["marge_euro"]) {
				# nieuwe marge is lager
				$bgcolor="#ff9e69";
			} else {
				# marge is gelijk gebleven
				$bgcolor="#cccccc";
			}

			if(($reissom_tabel["bedragen"]["verkoop"]-$gegevens["stap1"]["totale_reissom"])<>0) {
				$marge_percentage_verschil=($reissom_tabel["bedragen"]["marge_euro"]-$marge_euro_oud)/($reissom_tabel["bedragen"]["verkoop"]-$gegevens["stap1"]["totale_reissom"])*100;
			} elseif(($reissom_tabel["bedragen"]["marge_euro"]-$marge_euro_oud)<>0) {
				$marge_percentage_verschil=-100;
			} else {
				$marge_percentage_verschil=0;
			}

			$return.="<tr style=\"background-color:".$bgcolor.";\">";
			$return.="<td style=\"text-align:left;width:70px;\">Mutaties</td><td style=\"text-align:left;width:50px;\">&nbsp;</td><td style=\"text-align:left;width:1px;\">&euro;</td><td colspan=\"2\" style=\"text-align:right;width:1px;\">".number_format($reissom_tabel["bedragen"]["verkoop"]-$gegevens["stap1"]["totale_reissom"],2,",",".")."</td><td style=\"width:200px;\">&nbsp;</td>";
			$return.="<td style=\"text-align:left;\">&nbsp;</td><td style=\"text-align:left;width:1px;\">&euro;</td><td colspan=\"2\" style=\"text-align:right;width:1px;\">".number_format($reissom_tabel["bedragen"]["inkoop"]-$gegevens["stap1"]["totale_reissom_inkoop"],2,",",".")."</td><td style=\"width:200px;\">&nbsp;</td>";
			$return.="<td style=\"text-align:right;\"&nbsp;</td><td style=\"text-align:left;width:1px;\">&euro;</td><td style=\"text-align:right;width:1px;\">".number_format($reissom_tabel["bedragen"]["marge_euro"]-$marge_euro_oud,2,",",".")."</td><td style=\"text-align:right;\">(".number_format($marge_percentage_verschil,2,",",".")."%)</td>";
			$return.="</tr>";

			if($reissom_tabel["bedragen"]["marge_euro"]<0) {
				$margebgcolor="red";
			} else {
				$margebgcolor="";
			}

			$return.="<tr style=\"background-color:".$bgcolor."\">";
			$return.="<td style=\"text-align:left;width:70px;\">Nieuw</td><td style=\"text-align:left;width:50px;\">&nbsp;</td><td style=\"text-align:left;width:1px;\">&euro;</td><td colspan=\"2\" style=\"text-align:right;width:1px;\">".number_format($reissom_tabel["bedragen"]["verkoop"],2,",",".")."</td><td style=\"width:200px;\">&nbsp;</td>";
			$return.="<td style=\"text-align:left;\">&nbsp;</td><td style=\"text-align:left;width:1px;\">&euro;</td><td colspan=\"2\" style=\"text-align:right;width:1px;\">".number_format($reissom_tabel["bedragen"]["inkoop"],2,",",".")."</td><td style=\"width:200px;\">&nbsp;</td>";
			$return.="<td style=\"text-align:right;background-color:".$margebgcolor."\">&nbsp;</td><td style=\"text-align:left;width:1px;background-color:".$margebgcolor."\">&euro;</td><td style=\"text-align:right;width:1px;background-color:".$margebgcolor."\">".number_format($reissom_tabel["bedragen"]["marge_euro"],2,",",".")."</td><td style=\"text-align:right;background-color:".$margebgcolor."\">(".number_format($reissom_tabel["bedragen"]["marge_percentage"],2,",",".")."%)</td>";
			$return.="</tr>";

			$verschil_getoond=true;
		} else {
			$return.="<tr>";
			$return.="<td style=\"width:33%;text-align:left;\">Verkoop:&nbsp;&euro;&nbsp;".number_format($reissom_tabel["bedragen"]["verkoop"],2,",",".")."</td>";
			$return.="<td style=\"width:33%;text-align:center;\">Inkoop:&nbsp;&euro;&nbsp;".number_format($reissom_tabel["bedragen"]["inkoop"],2,",",".")."</td>";
			$return.="<td style=\"width:33%;text-align:right;".($reissom_tabel["bedragen"]["marge_euro"]<0 ? "background-color:red;" : "")."\">Marge:&nbsp;&euro;&nbsp;".number_format($reissom_tabel["bedragen"]["marge_euro"],2,",",".")." (".number_format($reissom_tabel["bedragen"]["marge_percentage"],2,",",".")."%)</td>";
			$return.="</tr>";

			if($verschil>0.01) {
				$return.="<tr style=\"color:#000000;background-color:#ffa9a9;text-align:center;\"><td colspan=\"3\">Let op: het gefactureerde bedrag (&euro;&nbsp;".number_format($gegevens["stap1"]["totale_reissom"],2,",",".").") is &euro;&nbsp;".number_format(abs($verschil),2,",",".")." hoger dan de totale reissom (&euro;&nbsp;".number_format($gegevens["fin"]["totale_reissom"],2,",",".").")</td></tr>";
			} elseif($verschil<-0.01) {
				$return.="<tr style=\"color:#000000;background-color:#ffa9a9;text-align:center;\"><td colspan=\"3\">Let op: het gefactureerde bedrag (&euro;&nbsp;".number_format($gegevens["stap1"]["totale_reissom"],2,",",".").") is &euro;&nbsp;".number_format(abs($verschil),2,",",".")." lager dan de totale reissom (&euro;&nbsp;".number_format($gegevens["fin"]["totale_reissom"],2,",",".").")</td></tr>";
			}
		}
		if(!$gegevens["stap1"]["eenmaliggecontroleerd"] and $_GET["cmscontrole"]==1) {
			$return.="<tr style=\"color:#000000;background-color:#cccccc;text-align:center;font-size:0.8em;\"><td colspan=\"3\">De inkoopgegevens van deze boeking zijn nog niet gecontroleerd. <a href=\"".$vars["path"]."cms.php?eenmaliggecontroleerd=".$gegevens["stap1"]["boekingid"]."#eenmaliggecontroleerd\" onclick=\"return confirm('zeker weten?');\">Bovenstaande bedragen goedkeuren &raquo;</a></td></tr>";
		}

		if(!$verschil_getoond and strval($gegevens["stap1"]["totale_reissom_inkoop"])!=strval($gegevens["stap1"]["totale_reissom_inkoop_actueel"])) {
			$return.="<tr style=\"color:#000000;background-color:#cccccc;text-align:center;\"><td colspan=\"3\">Nieuwe totale reissom inkoop: &euro;&nbsp;".number_format($gegevens["stap1"]["totale_reissom_inkoop_actueel"],2,",",".");
			if($gegevens["stap1"]["totale_reissom_inkoop"]<>0) $return.=" (laatste goedkeuring: &euro;&nbsp;".number_format($gegevens["stap1"]["totale_reissom_inkoop"],2,",",".").")";
			$return.="<br><br><a href=\"".$path."cms_boekingen_leveranciers.php?burl=".urlencode($vars["path"]."cms_boekingen.php?show=21&21k0=".$gegevens["stap1"]["boekingid"])."&bid=".$gegevens["stap1"]["boekingid"]."\">inkoopgegevens controleren en opslaan &raquo;</a></td></tr>";
		}
		$return.="</table>";

		if($verschil>0.01) {
			$factuur_bedrag_wijkt_af=1;
		} elseif($verschil<-0.01) {
			$factuur_bedrag_wijkt_af=1;
		}

		if($gegevens["stap1"]["aankomstdatum_exact"]>time() and (($gegevens["stap1"]["factuur_bedrag_wijkt_af"] and !$factuur_bedrag_wijkt_af) or (!$gegevens["stap1"]["factuur_bedrag_wijkt_af"] and $factuur_bedrag_wijkt_af))) {
			$db->query("UPDATE boeking SET factuur_bedrag_wijkt_af='".$factuur_bedrag_wijkt_af."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		}

		if($gegevens["stap1"]["totale_reissom_inkoop_actueel"]<>"" and !$verschil_getoond) {
			# totale_reissom_inkoop_actueel opslaan
			$db->query("UPDATE boeking SET totale_reissom_inkoop_actueel='".addslashes($reissom_tabel["bedragen"]["inkoop"])."' WHERE boeking_id='".addslashes($gegevens["stap1"]["boekingid"])."';");
		}

		return $return;
	}
}

function boekingoverzicht($wherequery) {
	global $vars,$tl_teller;
	$tl_teller++;
	$db=new DB_sql;

	# Reisbureau-namen in vars opnemen
	if(!$vars["reisbureau_namen"]) {
		$db->query("SELECT ru.user_id, r.naam FROM reisbureau r, reisbureau_user ru WHERE ru.reisbureau_id=r.reisbureau_id;");
		while($db->next_record()) {
			$vars["reisbureau_namen"][$db->f("user_id")]=$db->f("naam");
		}
	}
	# Boekingen opvragen
	$db->query("SELECT b.boeking_id, b.boekingsnummer, b.aankomstdatum_exact, b.vertrekdatum_exact, b.seizoen_id, b.reisbureau_user_id, b.toonper, b.wederverkoop, s.naam AS seizoen, t.type_id, a.accommodatie_id, a.soortaccommodatie, a.naam, a.wzt, t.naam AS tnaam, t.leverancierscode, t.optimaalaantalpersonen, t.maxaantalpersonen, p.naam AS plaats, l.begincode, bp.voornaam, bp.tussenvoegsel, bp.achternaam FROM accommodatie a, type t, plaats p, land l, boeking b, boeking_persoon bp, seizoen s WHERE bp.persoonnummer=1 AND bp.boeking_id=b.boeking_id AND b.seizoen_id=s.seizoen_id AND b.type_id=t.type_id AND b.geannuleerd=0 AND b.boekingsnummer<>'' AND p.land_id=l.land_id AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND ".$wherequery.";");

	if($db->num_rows()) {
		$tl=new tablelist;
		$tl->settings["systemid"]=$tl_teller;
		$tl->settings["arrowcolor"]="white";
		$tl->settings["max_results_per_page"]=3000;
		$tl->settings["path"]=$vars["path"];
		$tl->settings["resultpages_top"]=true;
		$tl->settings["th_id"]="col_";
		$tl->settings["td_class"]="col_";   # elke cel een class: "deze_voorloper+naam"

		$tl->sort=array("aankomst","accommodatie","hoofdboeker");
		$tl->sort_desc=true;

		$tl->field_show("cms_boekingen.php?show=21&21k0=[ID]","Boeking bekijken");
		$tl->field_text("aankomst","Aankomst");
		$tl->field_text("boekingsnummer","Boekingsnr");
		$tl->field_text("accommodatie","Accommodatie");
		$tl->field_text("hoofdboeker","Hoofdboeker");
		$tl->field_text("acc_of_arrangement","Ac/Ar/Co");

		while($db->next_record()) {
			# add_record($id,$key,$value,$sortvalue="",$datetime=false,$options="")
			$tl->add_record("aankomst",$db->f("boeking_id"),date("d-m-Y",$db->f("aankomstdatum_exact")),$db->f("aankomstdatum_exact"),true);
			$tl->add_record("boekingsnummer",$db->f("boeking_id"),$db->f("boekingsnummer"));
			$tl->add_record("accommodatie",$db->f("boeking_id"),$db->f("plaats")." - ".ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam").($db->f("tnaam") ? " ".$db->f("tnaam") : "")." (".$db->f("optimaalaantalpersonen").($db->f("maxaantalpersonen")>$db->f("optimaalaantalpersonen") ? "-".$db->f("maxaantalpersonen") : "")."p - ".$db->f("begincode").$db->f("type_id").")");
			$hoofdboeker=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
			if($db->f("reisbureau_user_id")) {
				$hoofdboeker.=" (via ".$vars["reisbureau_namen"][$db->f("reisbureau_user_id")].")";
				$acc_of_arrangement="Acc + Com";
			} else {
				if($db->f("toonper")==3 or $db->f("wederverkoop")) {
					$acc_of_arrangement="Acc";
				} else {
					$acc_of_arrangement="Arr";
				}
			}
			$tl->add_record("hoofdboeker",$db->f("boeking_id"),$hoofdboeker);
			$tl->add_record("acc_of_arrangement",$db->f("boeking_id"),$acc_of_arrangement);
		}
		$return=$tl->table("tbl",1);
		return $return;
	} else {
#		echo "<i>geen boekingen aanwezig</i>";
		return false;
	}
}

function get_reserveringsnummer_2($leverancierid,$aankomstdatum_exact) {

	#
	# Boekingsnummer 2e deel vaststellen /bepalen (nu nog in gebruik voor het garantiesysteem)
	#

	global $vars;
	$db=new DB_sql;

	$reserveringsnummer_2=substr("00".$leverancierid,-3);

	# Hoogste nummer uit tabel boeking halen
	$db->query("SELECT SUBSTRING(boekingsnummer,14,3) AS boekingsnummer, aankomstdatum_exact FROM boeking WHERE CHAR_LENGTH(boekingsnummer)=16 AND SUBSTRING(boekingsnummer,11,3)='".addslashes($reserveringsnummer_2)."' AND aankomstdatum_exact<='".boekjaar_unixtime($aankomstdatum_exact)."' ORDER BY SUBSTRING(boekingsnummer,14,3) DESC LIMIT 0,1;");
	if($db->next_record()) {
		$boekingsnummer=$db->f("boekingsnummer");
	}
	# Hoogste nummer uit garantie boeking halen
	$db->query("SELECT SUBSTRING(reserveringsnummer_extern,4,3) AS reserveringsnummer_extern FROM garantie WHERE SUBSTRING(reserveringsnummer_extern,1,3)='".addslashes($reserveringsnummer_2)."' AND aankomstdatum_exact<='".boekjaar_unixtime($aankomstdatum_exact)."' ORDER BY SUBSTRING(reserveringsnummer_extern,4,3) DESC LIMIT 0,1;");
	if($db->next_record()) {
		if($db->f("reserveringsnummer_extern")>$boekingsnummer) {
			$boekingsnummer=$db->f("reserveringsnummer_extern");
		}
	}

	if($boekingsnummer) {
		$boekjaarcijfer=substr($boekingsnummer,0,1);
		if(boekjaar($aankomstdatum_exact)==2008) {
			if($boekjaarcijfer<3) {
				$reserveringsnummer_2.="301";
			} else {
				$reserveringsnummer_2.=substr("000".($boekingsnummer+1),-3);

			}
		} elseif(boekjaar($aankomstdatum_exact)==2009) {
			if($boekjaarcijfer<5) {
				$reserveringsnummer_2.="501";
			} else {
				$reserveringsnummer_2.=substr("000".($boekingsnummer+1),-3);
			}
		} elseif(boekjaar($aankomstdatum_exact)==2010) {
			if($boekjaarcijfer<7) {
				$reserveringsnummer_2.="701";
			} else {
				$reserveringsnummer_2.=substr("000".($boekingsnummer+1),-3);
			}
		} elseif(boekjaar($aankomstdatum_exact)==2011) {
			if($boekjaarcijfer<9) {
				$reserveringsnummer_2.="901";
			} else {
				$reserveringsnummer_2.=substr("000".($boekingsnummer+1),-3);
			}
		} else {
			$reserveringsnummer_2.=substr("000".($boekingsnummer+1),-3);
		}
	} else {
		if(boekjaar($aankomstdatum_exact)==2008) {
			$boekjaarcijfer="3";
		} elseif(boekjaar($aankomstdatum_exact)==2009) {
			$boekjaarcijfer="5";
		} elseif(boekjaar($aankomstdatum_exact)==2010) {
			$boekjaarcijfer="7";
		} elseif(boekjaar($aankomstdatum_exact)==2011) {
			$boekjaarcijfer="9";
		} else {
			$boekjaarcijfer="0";
		}
		$reserveringsnummer_2.=$boekjaarcijfer."01";
	}

	return $reserveringsnummer_2;
}

function inkoopgegevens_berekenen_en_opslaan($gegevens) {

	#
	# Inkoopgegevens berekenen en vervolgens opslaan in de tabel boeking. Het gaat om de volgende velden:
	#
	# - totale_reissom_inkoop
	# - totale_reissom_inkoop_actueel
	# - inkoopnetto
	# - totaalfactuurbedrag
	# - inkoop van alle opties

	global $vars;
	$db=new DB_sql;
	$reissom_tabel=reissom_tabel($gegevens,$gegevens["stap1"]["accinfo"],"",true);


	# bekomende kosten accommodatieleverancier berekenen

	# verborgen opties laden
	$db->query("SELECT extra_optie_id, persoonnummer, deelnemers, naam, inkoop, korting, hoort_bij_accommodatieinkoop FROM extra_optie WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."' AND verberg_voor_klant=1 AND hoort_bij_accommodatieinkoop=1 ORDER BY soort, naam;");
	while($db->next_record()) {
		$use_key="hoort_bij_accommodatieinkoop";
		if($db->f("persoonnummer")=="alg") {
			$gegevens["stap4"]["algemene_optie"][$use_key][$db->f("extra_optie_id")]=true;
			$gegevens["stap4"]["algemene_optie"]["inkoop"][$db->f("extra_optie_id")]=$db->f("inkoop");
			$gegevens["stap4"]["algemene_optie"]["korting"][$db->f("extra_optie_id")]=$db->f("korting");
			$gegevens["stap4"]["algemene_optie"]["naam"][$db->f("extra_optie_id")]=$db->f("naam");
		} elseif($db->f("persoonnummer")=="pers") {
			$gegevens["stap4"]["persoonsoptie_fin"][$use_key][$db->f("extra_optie_id")]=true;
			$aantal_deelnemers=intval(@count(@preg_split("/,/",$db->f("deelnemers"))));
			$gegevens["stap4"]["persoonsoptie_fin"]["aantal"][$db->f("extra_optie_id")]=$aantal_deelnemers;
			$gegevens["stap4"]["persoonsoptie_fin"]["inkoop"][$db->f("extra_optie_id")]=$db->f("inkoop")*$aantal_deelnemers;
			$gegevens["stap4"]["persoonsoptie_fin"]["korting"][$db->f("extra_optie_id")]=$db->f("korting");
			$gegevens["stap4"]["persoonsoptie_fin"]["naam"][$db->f("extra_optie_id")]=$db->f("naam");
		}
	}

	# gewone (niet-handmatige) opties laden waarbij hoort_bij_accommodatieinkoop aan staat
	while(list($key,$value)=@each($gegevens["stap4"]["optie_hoort_bij_accommodatieinkoop"])) {
		$db->query("SELECT bo.hoort_bij_accommodatieinkoop, ot.inkoop, ot.korting FROM boeking_optie bo, optie_tarief ot WHERE ot.week='".$gegevens["stap1"]["aankomstdatum"]."' AND bo.boeking_id='".$gegevens["stap1"]["boekingid"]."' AND ot.optie_onderdeel_id=bo.optie_onderdeel_id AND ot.optie_onderdeel_id='".addslashes($key)."' AND bo.hoort_bij_accommodatieinkoop=1;");
		if($db->next_record()) {
			$use_key1="hoort_bij_accommodatieinkoop";
			$use_key2="ooid_".$key;
			$gegevens["stap4"]["persoonsoptie_fin"][$use_key1][$use_key2]=true;
			$aantal_deelnemers=$gegevens["stap4"]["optie_hoort_bij_accommodatieinkoop_aantal"][$key];
			$gegevens["stap4"]["persoonsoptie_fin"]["aantal"][$use_key2]=$aantal_deelnemers;
			$gegevens["stap4"]["persoonsoptie_fin"]["inkoop"][$use_key2]=$db->f("inkoop")*$aantal_deelnemers;
			$gegevens["stap4"]["persoonsoptie_fin"]["korting"][$use_key2]=$db->f("korting");
			$gegevens["stap4"]["persoonsoptie_fin"]["naam"][$use_key2]=$gegevens["stap4"]["optie_onderdeelid_naam"][$key];
		}
	}
	if($gegevens["stap4"]["algemene_optie"]["hoort_bij_accommodatieinkoop"] or $gegevens["stap4"]["persoonsoptie_fin"]["hoort_bij_accommodatieinkoop"] or $gegevens["stap4"]["algemene_optie"]["hoort_niet_bij_accommodatieinkoop"] or $gegevens["stap4"]["persoonsoptie_fin"]["hoort_niet_bij_accommodatieinkoop"]) {
		# extra opties: algemeen: actief
		while(list($key,$value)=@each($gegevens["stap4"]["algemene_optie"]["hoort_bij_accommodatieinkoop"])) {
			$bedrag=$gegevens["stap4"]["algemene_optie"]["inkoop"][$key]*(1-$gegevens["stap4"]["algemene_optie"]["korting"][$key]/100);
			$temp_extraopties_totaal+=$bedrag;
		}
		# extra opties: per persoon: actief
		while(list($key,$value)=@each($gegevens["stap4"]["persoonsoptie_fin"]["hoort_bij_accommodatieinkoop"])) {
			$bedrag=$gegevens["stap4"]["persoonsoptie_fin"]["inkoop"][$key]*(1-$gegevens["stap4"]["persoonsoptie_fin"]["korting"][$key]/100);
			$temp_extraopties_totaal+=$bedrag;
		}
	}


	// inkoopmincommissie berekenen

	$fin["inkoopmincommissie"]=bedrag_min_korting($gegevens["stap1"]["inkoopbruto"],$gegevens["stap1"]["inkoopcommissie"]);

	// inkoopnetto berekenen
	$fin["inkoopnetto"]=$fin["inkoopmincommissie"];
	$fin["inkoopnetto"]=$fin["inkoopnetto"]-$gegevens["stap1"]["inkoopkorting"];
	$fin["inkoopnetto"]=bedrag_min_korting($fin["inkoopnetto"],$gegevens["stap1"]["inkoopkorting_percentage"]);
	$fin["inkoopnetto"]=$fin["inkoopnetto"]-$gegevens["stap1"]["inkoopkorting_euro"];
	$fin["inkoopnetto"]=round($fin["inkoopnetto"],2);

	// totaalfactuurbedrag berekenen
	$fin["totaalfactuurbedrag"]=$fin["inkoopnetto"];
	$fin["totaalfactuurbedrag"]=$fin["totaalfactuurbedrag"]+$temp_extraopties_totaal;

	$db->query("UPDATE boeking SET totale_reissom_inkoop='".addslashes($reissom_tabel["bedragen"]["inkoop"])."', totale_reissom_inkoop_actueel='".addslashes($reissom_tabel["bedragen"]["inkoop"])."', inkoopnetto='".addslashes($fin["inkoopnetto"])."', totaalfactuurbedrag='".addslashes($fin["totaalfactuurbedrag"])."' WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."';");

	# inkoop van opties
	$db->query("DELETE FROM boeking_optieinkoop WHERE boeking_id='".$gegevens["stap1"]["boekingid"]."';");
	while(list($key,$value)=@each($reissom_tabel["bedragen"]["optieinkoop"])) {
		if($key) {
			$db->query("INSERT INTO boeking_optieinkoop SET boeking_id='".$gegevens["stap1"]["boekingid"]."', optiecategorie='".addslashes($key)."', bedrag='".$value."';");
		} elseif(!$gegevens["stap1"]["geannuleerd"]) {
			trigger_error("lege optiecategorie bij boeking ".$gegevens["stap1"]["boekingsnummer"],E_USER_NOTICE);
		}
	}
}

function bedrag_min_korting($bedrag,$kortingspercentage) {
	$bedrag=$bedrag*(100-$kortingspercentage);
	$bedrag=$bedrag/100;
	$bedrag=round($bedrag,2);
	return $bedrag;
}

function object2array($object,$utf8decode=false) {
	$return = NULL;
	if(is_array($object)) {
		foreach($object as $key => $value) $return[$key] = object2array($value,$utf8decode);
	} else {
		if(is_object($object)) {
			$var = get_object_vars($object);
		}

		if($var) {
			foreach($var as $key => $value)	$return[$key] = object2array($value,$utf8decode);
		} else {
			if($utf8decode) {
				return strval(utf8_decode($object)); // strval and everything is fine
			} else {
				return strval($object); // strval and everything is fine
			}
		}
	}
	return $return;
}

function xml_structure_convert($tempxml) {

	#
	# XML waar de veldnamen in de XML zelf zitten (via table_structure) omzetten naar array
	#

	$xml=object2array($tempxml);

	while(list($key,$value)=each($xml["database"]["table_structure"]["field"])) {
		$field[$key]=$value["@attributes"]["Field"];
	}

	while(list($key,$value)=@each($xml["database"]["table_data"]["row"])) {
		$teller++;
		while(list($key2,$value2)=each($value["field"])) {
			if(!is_array($value2)) {
				$data[$teller][$field[$key2]]=trim(iconv("UTF-8", "CP1252",$value2));
			}
		}
	}

	$return=$data;
	return $return;
}

function wt_diff($old, $new) {
	$tekst1=split("\n",trim($old));
	$tekst2=split("\n",trim($new));
	$diff = &new Text_Diff($tekst1,$tekst2);
	$renderer = &new Text_Diff_Renderer_inline();
	$return=$renderer->render($diff);
	return $return;
}

function verzameltype_berekenen($seizoenid,$typeid) {
	#
	# Verzameltype berekenen
	#

	global $vars;
	$db=new DB_sql;
	$db2=new DB_sql;
	$db3=new DB_sql;

	$db->query("SELECT t.verzameltype_parent, a.toonper FROM accommodatie a, type t WHERE t.accommodatie_id=a.accommodatie_id AND t.type_id='".addslashes($typeid)."' AND t.verzameltype_parent>0;");
	if($db->next_record()) {
		$verzameltype_parent=$db->f("verzameltype_parent");
		$toonper=$db->f("toonper");

		# Oude gegevens wissen
		$db->query("DELETE FROM tarief WHERE type_id='".addslashes($verzameltype_parent)."' AND seizoen_id='".addslashes($seizoenid)."';");
		if($toonper==1) {
			$db->query("DELETE FROM tarief_personen WHERE type_id='".addslashes($verzameltype_parent)."' AND seizoen_id='".addslashes($seizoenid)."';");
		}
		$db->query("SELECT type_id FROM type WHERE verzameltype_parent='".addslashes($verzameltype_parent)."';");
		while($db->next_record()) {
			if($verzameltype_inquery) $verzameltype_inquery.=",".$db->f("type_id"); else $verzameltype_inquery=$db->f("type_id");
		}

		# Voorraad + beschikbaar opslaan
		if($toonper==1) {
			# toonper==1: arrangement
			$main_price_field="verkoop_accommodatie";
		} else {
			# toonper==3: accommodatie
			$main_price_field="c_verkoop_site";
		}

		$nietovernemen_tarief=array("type_id","seizoen_id","week","beschikbaar","voorraad_garantie","voorraad_allotment","voorraad_vervallen_allotment","voorraad_optie_leverancier","voorraad_xml","voorraad_request","voorraad_optie_klant","opgeslagen");

		# bepalen welke velden allemaal over te nemen
		$db->query("SHOW COLUMNS FROM tarief;");
		while($db->next_record()) {
			if(!in_array($db->f("Field"),$nietovernemen_tarief)) {
				$tarieven_doorlopen[$db->f("Field")]=true;
			}
		}
		unset($opslaan,$opslaan_reserve,$weekgehad,$weekgehad_reserve,$tarief_personen,$laatsteweek);
		$db->query("SELECT * FROM tarief WHERE seizoen_id='".addslashes($seizoenid)."' AND type_id IN (".$verzameltype_inquery.") ORDER BY week, ".$main_price_field.";");
		while($db->next_record()) {
			if($db->f("beschikbaar") and !$opslaan[$db->f("week")]["beschikbaar"]) {
				$opslaan[$db->f("week")]["beschikbaar"]=1;
			}
			$opslaan[$db->f("week")]["voorraad_garantie"]+=$db->f("voorraad_garantie");
			$opslaan[$db->f("week")]["voorraad_allotment"]+=$db->f("voorraad_allotment");
			$opslaan[$db->f("week")]["voorraad_vervallen_allotment"]+=$db->f("voorraad_vervallen_allotment");
			$opslaan[$db->f("week")]["voorraad_optie_leverancier"]+=$db->f("voorraad_optie_leverancier");
			$opslaan[$db->f("week")]["voorraad_xml"]+=$db->f("voorraad_xml");
			$opslaan[$db->f("week")]["voorraad_request"]+=$db->f("voorraad_request");
			$opslaan[$db->f("week")]["voorraad_optie_klant"]+=$db->f("voorraad_optie_klant");


			if(!$weekgehad_reserve[$db->f("week")] and $laatsteweek) {
				if(!$weekgehad[$laatsteweek]) {
					# Voor $laatsteweek is geen enkel tarief opgeslagen
					if($opslaan_reserve[$laatsteweek]) {
						$weekgehad[$laatsteweek]=true;
						$opslaan[$laatsteweek]=$opslaan_reserve[$laatsteweek];
	#				echo "ja ".date("d/m/Y",$laatsteweek)."<br>";
						if($toonper==1) {
							$tarief_personen[$laatsteweek]=$tarief_personen_reserve[$laatsteweek];
						}
					}
				}
			}

			# Goedkoopste week bepalen indien alles bezet is (want dan moet er toch een tarief worden opgeslagen)
			if(!$weekgehad_reserve[$db->f("week")] and $db->f($main_price_field)>0) {
				reset($tarieven_doorlopen);
				while(list($key,$value)=each($tarieven_doorlopen)) {
					if($db->f($key)) {
						$opslaan_reserve[$db->f("week")][$key]="'".addslashes($db->f($key))."'";
					} elseif($db->f($key)=="0") {
						$opslaan_reserve[$db->f("week")][$key]="'".addslashes($db->f($key))."'";
					}
				}

				# tarief_personen
				if($toonper==1) {
					$tarief_personen_reserve[$db->f("week")]=$db->f("type_id");
				}

				$weekgehad_reserve[$db->f("week")]=true;
			}

			if(!$weekgehad[$db->f("week")] and $db->f("beschikbaar") and $db->f($main_price_field)>0) {
				reset($tarieven_doorlopen);
				while(list($key,$value)=each($tarieven_doorlopen)) {
					if($db->f($key)) {
						$opslaan[$db->f("week")][$key]="'".addslashes($db->f($key))."'";
					} elseif($db->f($key)=="0") {
						$opslaan[$db->f("week")][$key]="'".addslashes($db->f($key))."'";
					}
				}

				# tarief_personen
				if($toonper==1) {
					$tarief_personen[$db->f("week")]=$db->f("type_id");
				}
				$weekgehad[$db->f("week")]=true;
			}
			$laatsteweek=$db->f("week");
		}

		if($laatsteweek) {
			if(!$weekgehad[$laatsteweek]) {
				# Voor $laatsteweek is geen enkel tarief opgeslagen
				if($opslaan_reserve[$laatsteweek]) {
					$weekgehad[$laatsteweek]=true;
					$opslaan[$laatsteweek]=$opslaan_reserve[$laatsteweek];
	#				echo "ja ".date("d/m/Y",$laatsteweek)."<br>";
					if($toonper==1) {
						$tarief_personen[$laatsteweek]=$tarief_personen_reserve[$laatsteweek];
					}
				}
			}
		}

		while(list($key,$value)=@each($weekgehad)) {
			unset($setquery);
#			echo date("d/m/y",$key)."<br>";
			while(list($key2,$value2)=each($opslaan[$key])) {
				$setquery.=", ".$key2."=".$value2;
			}
			$db2->query("INSERT INTO tarief SET type_id='".$verzameltype_parent."', seizoen_id='".addslashes($seizoenid)."', week='".$key."', opgeslagen=NOW()".$setquery.";");
#			echo $db2->lastquery."<br>";

			if($tarief_personen[$key]) {
				$db2->query("SELECT personen, prijs, afwijking FROM tarief_personen WHERE type_id='".$tarief_personen[$key]."' AND week='".$key."' AND seizoen_id='".addslashes($seizoenid)."';");
#				echo $db2->lastquery."<br>";
				while($db2->next_record()) {
					$db3->query("INSERT INTO tarief_personen SET personen='".addslashes($db2->f("personen"))."', prijs='".addslashes($db2->f("prijs"))."', afwijking='".addslashes($db2->f("afwijking"))."', type_id='".$verzameltype_parent."', seizoen_id='".addslashes($seizoenid)."', week='".$key."';");
#					echo $db3->lastquery."<br>";
				}
			}
		}
	}
}


?>