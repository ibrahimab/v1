<?php

// Set the P3P compact policy.
header('P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

require_once('../admin/vars_db.php');
require_once("../admin/class.mysql.php");
require_once("../admin/allfunctions.php");

# trackercookie opslaan
$_GET["chad"] = 20;
$vars["bezoek_altijd_opslaan"] = true;
include("../admin/trackercookie.php");

# Cookie plaatsen voor controle (networkontdubbeling)
@setcookie("tradetracker", time(), (time() + 3456000), "/");

// Set domain name on which the redirect-page runs, WITHOUT "www.".
$domainName = strtolower(preg_replace("@^[a-zA-Z0-9-]+\.([a-zA-Z0-9-]+\.[a-zA-Z0-9]+)$@", "\\1", $_SERVER["HTTP_HOST"]));

$use_new_tradetracker_code = [
	'chaletonline.de',
];

if (in_array($domainName, $use_new_tradetracker_code)) {

	// Set tracking group ID if provided by TradeTracker.
	$trackingGroupID = '';

	if (isset($_GET['tt'])) {

		$trackingParam = explode('_', $_GET['tt']);

		$campaignID = isset($trackingParam[0]) ? $trackingParam[0] : '';
		$materialID = isset($trackingParam[1]) ? $trackingParam[1] : '';
		$affiliateID = isset($trackingParam[2]) ? $trackingParam[2] : '';
		$reference = isset($trackingParam[3]) ? $trackingParam[3] : '';

		$redirectURL = isset($_GET['r']) ? $_GET['r'] : '';

		// Calculate MD5 checksum.
		$checkSum = md5('CHK_' . $campaignID . '::' . $materialID . '::' . $affiliateID . '::' . $reference);

		// Set tracking data.
		$trackingData = $materialID . '::' . $affiliateID . '::' . $reference . '::' . $checkSum . '::' . time();

		// Set regular tracking cookie.
		setcookie('TT2_' . $campaignID, $trackingData, time() + 31536000, '/', empty($domainName) ? null : '.' . $domainName);

		// Set session tracking cookie.
		setcookie('TTS_' . $campaignID, $trackingData, 0, '/', empty($domainName) ? null : '.' . $domainName);

		// Set tracking group cookie.
		if (!empty($trackingGroupID)) {
			setcookie('__tgdat' . $trackingGroupID, $trackingData . '_' . $campaignID, time() + 31536000, '/', empty($domainName) ? null : '.' . $domainName);
		}

		// Set track-back URL.
		$trackBackURL = 'https://tc.tradetracker.net/?c=' . $campaignID . '&m=' . $materialID . '&a=' . $affiliateID . '&r=' . urlencode($reference) . '&u=' . urlencode($redirectURL);

		// Redirect to TradeTracker.
		header('Location: ' . $trackBackURL);
	} else {
		header('Location: /');
	}

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
	wt_session_start();

	// Set session data
	$_SESSION[$cookieName] = $cookieValue;

	// Set track-back URL
	$trackBackURL = "http://tc.tradetracker.nl/v2/{$campaignID}/{$materialID}/{$affiliateID}/" . urlencode($reference) . "?r=" . urlencode($redirectURL);

	// Redirect to TradeTracker
	header("Location: {$trackBackURL}", true, 301);

} elseif($_SERVER["HTTP_HOST"]=="www.italissima.nl" || $_SERVER["HTTP_HOST"]=="www.italissima.be") {

	#
	# Italissima.nl/be
	#

	//! Tradetracker Redirect-Page.

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
		wt_session_start();

		// Set session data.
		$_SESSION[$cookieName] = $cookieValue;

		// Set track-back URL.
		$trackBackURL = 'https://tc.tradetracker.net/?c=' . $campaignID . '&m=' . $materialID . '&a=' . $affiliateID . '&r=' . urlencode($reference) . '&u=' . urlencode($redirectURL);

		// Redirect to TradeTracker.
		header('Location: ' . $trackBackURL, true, 301);
	}
} else {


	#
	# Chalet.nl Winter / Chalet.be Winter / Chalet.eu Winter
	#

	// configuration
	if($_SERVER["HTTP_HOST"]=="www.chalet.be") {
		$domainName = "chalet.be";
	} elseif($_SERVER["HTTP_HOST"]=="www.chalet.eu") {
		$domainName = "chalet.eu";
	} else {
		$domainName = "chalet.nl";
	}

	// create session
	wt_session_start();

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
