<?php

header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');


# Bezoekers-statistieken opslaan
#$mysqlsettings["name"]["remote"]="chalet";	# Databasenaam bij provider
$mysqlsettings["name"]["local"]="dbtest_chalet";		# Optioneel: Databasenaam lokaal (alleen invullen indien anders dan database bij provider)
$mysqlsettings["host"]="localhost";# Hostname bij provider
$mysqlsettings["localhost"]="ss.postvak.net";# Hostname voor lokaal gebruik
#$mysqlsettings["user"]="chalet";		# Username bij provider
#$mysqlsettings["password"]="20012002";		# Password bij provider

$mysqlsettings["host"]="localhost";# Hostname bij provider
$mysqlsettings["name"]["remote"]="db_chalet";	# Databasenaam bij provider
$mysqlsettings["user"]="chaletdb";		# Username bij provider
$mysqlsettings["password"]="kskL2K2kaQ";		# Password bij provider


$mysqlsettings["halt_on_error"]="yes";		# Wat te doen bij MySQL-foutmelding bij provider ("yes" = foutmelding weergeven en stoppen, "no" = geen foutmelding en gewoon doorgaan, "mail" = geen foutmelding op het scherm maar wel een melding aan jeroen@webtastic.nl
require("../admin/class.mysql.php");
require("../admin/allfunctions.php");

# trackercookie opslaan
$_GET["chad"]=20;
$vars["bezoek_altijd_opslaan"]=true;
include("../admin/trackercookie.php");

# Cookie plaatsen voor controle (networkontdubbeling) Cleafs vs TradeTracker
@setcookie("tradetracker", time(), (time() + 3456000), "/");

if($_SERVER["HTTP_HOST"]=="www.wintersportaccommodaties.nl") {

	#
	# Wintersportaccommodaties.nl
	#

	//! Tradetracker Landingpage
	
	//===================================================================================================
	//											Configuration											/
	//===================================================================================================
	// Set domain name and secret key
	$domainName = "wintersportaccommodaties.nl";	//the domain name on which the landingpage runs, without www.
	$secretKey = "";		//the secret-key is provided by TradeTracker
	//===================================================================================================
	
	// V1 support
	if($_GET["campaignID"]) {
		//set parameters
		$campaignID = $_GET["campaignID"];
		$materialID = $_GET["materialID"];
		$affiliateID = $_GET["affiliateID"];
		$redirectURL = $_GET["redirectURL"];
		$reference = "";
	} else {
		// Set parameters
		list($campaignID, $materialID, $affiliateID, $reference) = explode('_', $_GET["tt"]);
		$redirectURL = $_GET["r"];
	}
	
	// Calculate MD5 checksum
	$checkSum = md5("CHK_{$campaignID}::{$materialID}::{$affiliateID}::{$reference}::{$secretKey}");
	
	// Set session/cookie arguments
	$cookieName = "TT2_{$campaignID}";
	$cookieValue = "{$materialID}::{$affiliateID}::{$reference}::{$checkSum}";
	
	# Cookie niet plaatsen bij werknemers Chalet
	if(!$_COOKIE["flc"]) {
		// Create tracking cookie
		@setcookie($cookieName, $cookieValue, (time() + 3456000), "/", ".{$domainName}");
	}
	
	// Create tracking session
	session_start();
	
	
	// Set session data
	$_SESSION[$cookieName] = $cookieValue;
	
	// Set track-back URL
	$trackBackURL = "http://tc.tradetracker.nl/v2/{$campaignID}/{$materialID}/{$affiliateID}/" . urlencode($reference) . "?r=" . urlencode($redirectURL);
	
	// Redirect to TradeTracker
	header("Location: {$trackBackURL}", true, 301);
} elseif($_SERVER["HTTP_HOST"]=="www.zomerhuisje.nl") {
	#
	# Zomerhuisje.nl
	#
	//! Tradetracker Landingpage
	
	//===================================================================================================
	//											Configuration											/
	//===================================================================================================
	// Set domain name and secret key
	$domainName = "zomerhuisje.nl";	//the domain name on which the landingpage runs, without www.
	$secretKey = "0420046291";		//the secret-key is provided by TradeTracker
	//===================================================================================================
	
	// V1 support
	if($_GET["campaignID"]) {
		//set parameters
		$campaignID = $_GET["campaignID"];
		$materialID = $_GET["materialID"];
		$affiliateID = $_GET["affiliateID"];
		$redirectURL = $_GET["redirectURL"];
		$reference = "";
	} else {
		// Set parameters
		list($campaignID, $materialID, $affiliateID, $reference) = explode('_', $_GET["tt"]);
		$redirectURL = $_GET["r"];
	}
	
	// Calculate MD5 checksum
	$checkSum = md5("CHK_{$campaignID}::{$materialID}::{$affiliateID}::{$reference}::{$secretKey}");
	
	// Set session/cookie arguments
	$cookieName = "TT2_{$campaignID}";
	$cookieValue = "{$materialID}::{$affiliateID}::{$reference}::{$checkSum}";
	
	# Cookie niet plaatsen bij werknemers Chalet
	if(!$_COOKIE["flc"]) {
		// Create tracking cookie
		@setcookie($cookieName, $cookieValue, (time() + 3456000), "/", ".{$domainName}");
	}
		
	// Create tracking session
	session_start();
	
	// Set session data
	$_SESSION[$cookieName] = $cookieValue;
	
	// Set track-back URL
	$trackBackURL = "http://tc.tradetracker.nl/v2/{$campaignID}/{$materialID}/{$affiliateID}/" . urlencode($reference) . "?r=" . urlencode($redirectURL);
	
	// Redirect to TradeTracker
	header("Location: {$trackBackURL}", true, 301);

} elseif($_SERVER["HTTP_HOST"]=="www.italissima.nl") {

	#
	# Italissima
	#

	//! Tradetracker Redirect-Page.

	// Set domain name on which the redirect-page runs, WITHOUT "www.".
	$domainName = 'italissima.nl';

	// Set the P3P compact policy.
	header('P3P: CP="ALL PUR DSP CUR ADMi DEVi CONi OUR COR IND"');

	// Define parameters.
	$canRedirect = true;

	// Set parameters.
	if (isset($_GET['campaignID']))
	{
		$campaignID = $_GET['campaignID'];
		$materialID = isset($_GET['materialID']) ? $_GET['materialID'] : '';
		$affiliateID = isset($_GET['affiliateID']) ? $_GET['affiliateID'] : '';
		$redirectURL = isset($_GET['redirectURL']) ? $_GET['redirectURL'] : '';
		$reference = '';
	}
	else if (isset($_GET['tt']))
	{
		$trackingData = explode('_', $_GET['tt']);

		$campaignID = isset($trackingData[0]) ? $trackingData[0] : '';
		$materialID = isset($trackingData[1]) ? $trackingData[1] : '';
		$affiliateID = isset($trackingData[2]) ? $trackingData[2] : '';
		$reference = isset($trackingData[3]) ? $trackingData[3] : '';

		$redirectURL = isset($_GET['r']) ? $_GET['r'] : '';
	}
	else
		$canRedirect = false;

	if ($canRedirect)
	{
		// Calculate MD5 checksum.
		$checkSum = md5('CHK_' . $campaignID . '::' . $materialID . '::' . $affiliateID . '::' . $reference);

		// Set session/cookie arguments.
		$cookieName = 'TT2_' . $campaignID;
		$cookieValue = $materialID . '::' . $affiliateID . '::' . $reference . '::' . $checkSum . '::' . time();

		// Create tracking cookie.
		setcookie($cookieName, $cookieValue, (time() + 31536000), '/', !empty($domainName) ? '.' . $domainName : null);

		// Create tracking session.
		session_start();

		// Set session data.
		$_SESSION[$cookieName] = $cookieValue;

		// Set track-back URL.
		$trackBackURL = 'http://tc.tradetracker.net/?c=' . $campaignID . '&m=' . $materialID . '&a=' . $affiliateID . '&r=' . urlencode($reference) . '&u=' . urlencode($redirectURL);

		// Redirect to TradeTracker.
		header('Location: ' . $trackBackURL, true, 301);
	}
} else {


	#
	# Chalet.nl Winter
	#

	// configuration
	$domainName = "chalet.nl";
	
	// create session
	session_start();
	
	// define arguments
	$campaignID = $_GET["campaignID"];
	$materialID = $_GET["materialID"];
	$affiliateID = $_GET["affiliateID"];
	$redirectURL = $_GET["redirectURL"];
	
	// set current timestamp
	$timeStamp = time();
	
	// calculate MD5 checksum
	$checkSum = md5("CHK_" . $campaignID . "::" . $materialID . "::" . $affiliateID . "::". $timeStamp);
	
	// set session/cookie arguments
	$cookieName = "TT2_" . $campaignID;
	$cookieValue = $materialID . "::" . $affiliateID . "::" . $timeStamp . "::" . $checkSum;
	$cookieExpire = time() + (60 * 60 * 24 * 40);
	$cookiePath = "/";
	$cookieDomain = "." . $domainName;
	
	// set session data
	$_SESSION[$cookieName] = $cookieValue;
	
	# Cookie niet plaatsen bij werknemers Chalet
	if(!$_COOKIE["flc"]) {
		// create the normal tracking cookie
		@setcookie($cookieName, $cookieValue, $cookieExpire, $cookiePath, $cookieDomain);
	}
		
	// Set trackBackURL
	$trackBackURL = "http://tc.tradetracker.nl/$campaignID/$materialID/$affiliateID";
	
	// If redirect URL is defined, add it to the trackBackURL
	if($redirectURL != "") $trackBackURL = $trackBackURL . "/?redirectURL=" . urlencode($redirectURL);
	
	// redirect to TradeTracker
	header("Location: $trackBackURL");
}

?>