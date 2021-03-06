<?php

$vars["wt_htmlentities_cp1252"] = true;
require_once("admin/allfunctions.php");

$geen_tracker_cookie=true;

if(ereg("/pic/chalets/",$_SERVER["REQUEST_URI"])) {
	$fp=fopen("pic/chalet_404.jpg", "r");
	header("Content-type: image/jpg");
	fpassthru($fp);
	fclose($fp);
} else {


	if($_SERVER["HTTP_HOST"]=="www.venturasol.nl") {
		// redirects voor Venturasol (301's oude site naar nieuwe site)

		$redirect["https://www.venturasol.nl/skivakanties/home"]="https://www.venturasol.nl/";
		$redirect["https://www.venturasol.nl/skigebieden"]="https://www.venturasol.nl/skigebieden.php";
		$redirect["https://www.venturasol.nl/skivakanties/aanbiedingen"]="https://www.venturasol.nl/aanbiedingen.php";
		$redirect["https://www.venturasol.nl/skivakanties/zoek-en-boek"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/skivakanties/bestemming/les-trois-vallees"]="https://www.venturasol.nl/wintersport/skigebied/Les-Trois-Vallees/";
		$redirect["https://www.venturasol.nl/voorwaarden"]="https://www.venturasol.nl/algemenevoorwaarden.php";
		$redirect["https://www.venturasol.nl/appartementen"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/ski-chalet"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/skigebieden"]="https://www.venturasol.nl/skigebieden.php";
		$redirect["https://www.venturasol.nl/offerte-winter"]="https://www.venturasol.nl/contact.php";
		$redirect["https://www.venturasol.nl/wie"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/home"]="https://www.venturasol.nl/";
		$redirect["https://www.venturasol.nl/service-winter"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/zonvakanties/home"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/sitemap-winter"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/wie-zijn-wij"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/wiezijnwij"]="https://www.venturasol.nl/zoek-en-boek.php";
		$redirect["https://www.venturasol.nl/skivakanties/alle-accommodaties"]="https://www.venturasol.nl/zoek-en-boek.php";
		// $redirect[""]="";
		// $redirect[""]="";

		// accommodaties
		$redirect["https://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-8-kamer-chalet-7-slaapkamers-max-14-pers"]="https://www.venturasol.nl/wintersport/f8275/Chalet-Le-Hameau-des-Marmottes-8-kamer";
		$redirect["https://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees-600km-piste-les-menuires-appartementen/chalet-appartementen-le-hameau-des-marmottes"]="https://www.venturasol.nl/wintersport/f8345/Chalet-appartement-Le-Hameau-des-Marmottes-3-kamer";
		$redirect["https://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-6-kamer-chalet-5-slaapkamers-sauna-max-10-personen"]="https://www.venturasol.nl/wintersport/f8268/Chalet-Le-Hameau-des-Marmottes-6-kamer-met-sauna";
		$redirect["https://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-7-kamer-chalet-6-slaapkamers-max-12-pers"]="https://www.venturasol.nl/wintersport/f8274/Chalet-Le-Hameau-des-Marmottes-7-kamer";
		$redirect["https://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-8-kamer-chalet-7-slaapkamers-max-14-pers"]="https://www.venturasol.nl/wintersport/f8275/Chalet-Le-Hameau-des-Marmottes-8-kamer";
		$redirect["https://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees-600km-piste-les-menuires-appartementen/les-alpages-de-reberty-copy"]="https://www.venturasol.nl/wintersport/f8365/Chalet-appartement-Les-Alpages-de-Reberty-2-kamer-cabine-zondag-t-m-zondag";
		$redirect["https://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees-600km-piste-les-menuires-appartementen/les-alpages-de-reberty"]="https://www.venturasol.nl/wintersport/f8356/Chalet-appartement-Penthouse-Les-Alpages-de-Reberty-5-kamer-zondag-t-m-zondag";

		$current_url="https://www.venturasol.nl".$_SERVER["REQUEST_URI"];
		if($redirect[$current_url]) {
			header("Location: ".$redirect[$current_url],true,301);
			exit;
		} elseif(preg_match("@skivakanties/accomodatie@",$_SERVER["REQUEST_URI"]) or preg_match("@skivakanties/bestemming@",$_SERVER["REQUEST_URI"])) {
			header("Location: /zoek-en-boek.php",true,301);
			exit;
		}
	}



	if(eregi("ac[a-z]+/([A-Za-z0]+[0-9]+)",$_SERVER["REQUEST_URI"],$regs404)) {
		include("admin/vars.php");
		if(substr($regs404[1],0,1)=="0") {
			$regs404[1]=ereg_replace("^0","O",$regs404[1]);
		}
		header("Location: ".$path.txt("menu_accommodatie")."/".strtoupper($regs404[1])."/",true,301);
		exit;
	}

	# /f1815 doorsturen naar /accommodatie/F1815
	if(eregi("^/([a-z]{1,2}[0-9]+)/?$",$_SERVER["REQUEST_URI"],$regs404)) {
		include("admin/vars.php");
		header("Location: ".$path.txt("menu_accommodatie")."/".strtoupper($regs404[1])."/",true,301);
		exit;
	}

	# /1815 doorsturen naar /accommodatie/F1815
	if(eregi("^/([0-9]+)/?$",$_SERVER["REQUEST_URI"],$regs404)) {
		include("admin/vars.php");
		header("Location: ".$path.txt("menu_accommodatie")."/F".strtoupper($regs404[1])."/",true,301);
		exit;
	}

	# /filename doorsturen naar /filename.php
	if(eregi("^/([a-z0-9_-]+)$",$_SERVER["REQUEST_URI"],$regs404) and file_exists(strtolower($regs404[1].".php"))) {
		header("Location: /".$regs404[1].".php",true,301);
		exit;
	}

	if(ereg("/inlog",$_SERVER["REQUEST_URI"])) {
		header("Location: ".$path."inloggen.php",true,301);
		exit;
	}

	# mensen doorsturen naar zoekformulier (bijv. bij https://www.chalet.nl/zwitserland)
	if(eregi("^/([0-9a-zA-Z]+)$",$_SERVER["REQUEST_URI"],$regs404) and $_SERVER["HTTP_HOST"]!="www.venturasol.nl") {
		include("admin/vars.php");
		$_SERVER["REDIRECT_STATUS"]="404";
		wt_404();
		header("Location: ".$path.txt("menu_zoek-en-boek").".php?filled=1&fzt=".wt_he(urlencode($regs404[1])),true,302); # geen 301-redirect!!
		exit;
	}

	include("admin/vars.php");

	wt_404(true);
	header("HTTP/1.0 404 Not Found");

	include "content/opmaak.php";
}

?>