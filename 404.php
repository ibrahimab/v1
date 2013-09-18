<?php

$geen_tracker_cookie=true;

if(ereg("/pic/chalets/",$_SERVER["REQUEST_URI"])) {
	$fp=fopen("pic/chalet_404.jpg", "r");
	header("Content-type: image/jpg");
	fpassthru($fp);
	fclose($fp);
} else {


	if($_SERVER["HTTP_HOST"]=="www.venturasol.nl") {
		// redirects voor Venturasol (301's oude site naar nieuwe site)

		$redirect["http://www.venturasol.nl/skivakanties/home"]="http://www.venturasol.nl/";
		$redirect["http://www.venturasol.nl/skigebieden"]="http://www.venturasol.nl/skigebieden.php";
		$redirect["http://www.venturasol.nl/skivakanties/aanbiedingen"]="http://www.venturasol.nl/aanbiedingen.php";
		$redirect["http://www.venturasol.nl/skivakanties/zoek-en-boek"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/skivakanties/bestemming/les-trois-vallees"]="http://www.venturasol.nl/wintersport/skigebied/Les-Trois-Vallees/";
		$redirect["http://www.venturasol.nl/voorwaarden"]="http://www.venturasol.nl/algemenevoorwaarden.php";
		$redirect["http://www.venturasol.nl/appartementen"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/ski-chalet"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/skigebieden"]="http://www.venturasol.nl/skigebieden.php";
		$redirect["http://www.venturasol.nl/offerte-winter"]="http://www.venturasol.nl/contact.php";
		$redirect["http://www.venturasol.nl/wie"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/home"]="http://www.venturasol.nl/";
		$redirect["http://www.venturasol.nl/service-winter"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/zonvakanties/home"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/sitemap-winter"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/wie-zijn-wij"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/wiezijnwij"]="http://www.venturasol.nl/zoek-en-boek.php";
		$redirect["http://www.venturasol.nl/skivakanties/alle-accommodaties"]="http://www.venturasol.nl/zoek-en-boek.php";
		// $redirect[""]="";
		// $redirect[""]="";

		// accommodaties
		$redirect["http://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-8-kamer-chalet-7-slaapkamers-max-14-pers"]="http://www.venturasol.nl/wintersport/f8275/Chalet-Le-Hameau-des-Marmottes-8-kamer";
		$redirect["http://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees-600km-piste-les-menuires-appartementen/chalet-appartementen-le-hameau-des-marmottes"]="http://www.venturasol.nl/wintersport/f8345/Chalet-appartement-Le-Hameau-des-Marmottes-3-kamer";
		$redirect["http://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-6-kamer-chalet-5-slaapkamers-sauna-max-10-personen"]="http://www.venturasol.nl/wintersport/f8268/Chalet-Le-Hameau-des-Marmottes-6-kamer-met-sauna";
		$redirect["http://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-7-kamer-chalet-6-slaapkamers-max-12-pers"]="http://www.venturasol.nl/wintersport/f8274/Chalet-Le-Hameau-des-Marmottes-7-kamer";
		$redirect["http://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees/les-menuires-le-hameau-des-marmottes-8-kamer-chalet-7-slaapkamers-max-14-pers"]="http://www.venturasol.nl/wintersport/f8275/Chalet-Le-Hameau-des-Marmottes-8-kamer";
		$redirect["http://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees-600km-piste-les-menuires-appartementen/les-alpages-de-reberty-copy"]="http://www.venturasol.nl/wintersport/f8365/Chalet-appartement-Les-Alpages-de-Reberty-2-kamer-cabine-zondag-t-m-zondag";
		$redirect["http://www.venturasol.nl/skivakanties/accomodatie/les-trois-vallees-600km-piste-les-menuires-appartementen/les-alpages-de-reberty"]="http://www.venturasol.nl/wintersport/f8356/Chalet-appartement-Penthouse-Les-Alpages-de-Reberty-5-kamer-zondag-t-m-zondag";

		$current_url="http://www.venturasol.nl".$_SERVER["REQUEST_URI"];
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

	# mensen doorsturen naar zoekformulier (bijv. bij http://www.chalet.nl/zwitserland)
	if(eregi("^/([0-9a-zA-Z]+)$",$_SERVER["REQUEST_URI"],$regs404) and $_SERVER["HTTP_HOST"]!="www.venturasol.nl") {
		include("admin/vars.php");
		$_SERVER["REDIRECT_STATUS"]="404";
		wt_404();
		header("Location: ".$path.txt("menu_zoek-en-boek").".php?filled=1&fzt=".htmlentities(urlencode($regs404[1])),true,302); # geen 301-redirect!!
		exit;
	}

	include("admin/vars.php");

	wt_404(true);
	header("HTTP/1.0 404 Not Found");

	include "content/opmaak.php";
}

?>