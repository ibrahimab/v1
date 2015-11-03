<?php

//
// pick up unfinished booking
//
// redirect client to booking form or option-request form
//

include("admin/vars.php");

wt_session_start();

// Google Analytics querystring
$ga_querystring = "&utm_source=Mail_onafgeronde_boekingen&utm_medium=Mail_onafgeronde_boekingen&utm_campaign=Mail_onafgeronde_boekingen";

// check if the url is valid
if ($_GET["boeking_id"] and substr(sha1(boeking_veiligheid($_GET["boeking_id"].$vars["salt"])), 0, 10)==$_GET["check"]) {

	// get booking data
	$gegevens = get_boekinginfo($_GET["boeking_id"]);

	if ($_GET["type"]=="b") {

		//
		// booking
		//

		unset($_SESSION["boeking"]["boekingid"]);

		// set cookie
		setcookie("CHALET[boeking][boekingid]",$_GET["boeking_id"]."_".boeking_veiligheid($_GET["boeking_id"]),time()+259200, "/");

		// redirect to booking form
		header("Location: ".$gegevens["stap1"]["website_specifiek"]["basehref"]."boeken.php?bfbid=".intval($_GET["boeking_id"]).$ga_querystring);

	} elseif ($_GET["type"]=="o") {

		//
		// option
		//

		// set trackercookie
		if ($gegevens["stap1"]["bezoekerid"]) {
			setcookie("sch", $gegevens["stap1"]["bezoekerid"], time()+(86400*365*10), "/");
		}

		// redirect to option-request form
		header("Location: ".$gegevens["stap1"]["website_specifiek"]["basehref"].txt("menu_beschikbaarheid").".php?tid=".$gegevens["stap1"]["typeid"]."&d=".$gegevens["stap1"]["aankomstdatum"]."&ap=".$gegevens["stap1"]["aantalpersonen"]."&o=1".$ga_querystring);
	}
}
