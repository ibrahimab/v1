<?php
/** Configuration Variables **/

# the below credentials should be filled from the share database config file (admin/vars_db.php)
define('DB_NAME', $mysqlsettings["name"]["remote"]);
define('DB_USER', $mysqlsettings["user"]);
define('DB_PASSWORD', $mysqlsettings["password"]);
define('DB_HOST', $mysqlsettings["host"]);

// Check if acceptation server
if(isset($vars["acceptatie_testserver"]) && ($vars["acceptatie_testserver"] == true)) {

	// Errors display: true | false
	define ('DEVELOPMENT_ENVIRONMENT',true);

	// Payment module mode: test | production
	define('MODULE_MODE', 'test');

	// Chalet Server
	if($_SERVER["HTTP_HOST"]=="test.chalet.nl") {
		$site_url = "http". ($_SERVER["HTTPS"]=="on" ? "s" : "") ."://test.chalet.nl/";
	}elseif($_SERVER["HTTP_HOST"]=="test.chalet.eu") {
		$site_url = "http". ($_SERVER["HTTPS"]=="on" ? "s" : "") ."://test.chalet.eu/";
	}elseif($_SERVER["HTTP_HOST"]=="test.chalet.be") {
		$site_url = "http". ($_SERVER["HTTPS"]=="on" ? "s" : "") ."://test.chalet.be/";
	}

} else {

	// Live server
	define ('DEVELOPMENT_ENVIRONMENT',false);

	// Payment module mode: test | production
	define('MODULE_MODE', 'production');

	// Chalet Server
	if($_SERVER["HTTP_HOST"]=="www.chalet.nl") {
		$site_url = "http". ($_SERVER["HTTPS"]=="on" ? "s" : "") ."://www.chalet.nl/";
	}elseif($_SERVER["HTTP_HOST"]=="www.chalet.eu") {
		$site_url = "http". ($_SERVER["HTTPS"]=="on" ? "s" : "") ."://www.chalet.eu/";
	}elseif($_SERVER["HTTP_HOST"]=="www.chalet.be") {
		$site_url = "http". ($_SERVER["HTTPS"]=="on" ? "s" : "") ."://www.chalet.be/";
	}
}

define ('SITE_URL', $site_url);

// Docdata account
define ("TEST_MERCHANT_NAME", "chalet_nl");
define ("TEST_MERCHANT_PASSWORD", "7rU5ehew");
define ('PRODUCTION_MERCHANT_NAME', "chalet_nl");
define ('PRODUCTION_MERCHANT_PASSWORD', "ZAphAm6f");

// Docdata Payment URLs
define('TEST_WSDL', 'https://test.tripledeal.com/ps/services/paymentservice/1_0?wsdl');
define('PRODUCTION_WSDL', 'https://secure.docdatapayments.com/ps/services/paymentservice/1_0?wsdl');
define('TEST_WEBMENU', 'https://test.tripledeal.com/ps/menu?command=show_payment_cluster');
define('PRODUCTION_WEBMENU', 'https://secure.docdatapayments.com/ps/menu?command=show_payment_cluster');

class Config {

	private $types_WebmenuTypes = array();
	private $types_ModuleModes = array();
	private $types_ConfidenceLevels = array();

	public $general = array();
	private $merchant_account = array();
	private $payment_preferences = array();
	private $custom_statuses = array();
	private $payment_profiles = array();
	private $fallback_statuses = array();
	public $connection = array();
	public $payment = array();
	public $locking = array();
    public $pictures_path = array();

	function __construct() {
                
		$this->types_WebmenuTypes = array(
			// 1 to enable the menu (this value is used for the active state)
			array('value' => '1', 'label' => __('Show only the Docdata Webmenu option')),
			// 0 to disable the webmenu / attempt to go directly to the payment method
			array('value' => '0', 'label' => __('Go directly to a selected payment method in the Docdata Webmenu')),
		);

		$this->types_ModuleModes = array(
			array('value' => 'test', 'label' => __('Test Mode')),
			array('value' => 'production', 'label' => __('Production Mode')),
		);

		$this->types_ConfidenceLevels = array(
			array('value' => 'authorization', 'label' => __('Authorization')),
			array('value' => 'capture', 'label' => __('Capture')),
		);

		$this->general = array(
			"version" 				=> 1.0,
			"active"				=> 1,
			"module_mode"	 		=> MODULE_MODE, //All values: $this->types_ModuleModes
			"webmenu_active" 		=> 1, //All values: $this->types_WebmenuTypes
			"webmenu_css_id" 		=> array(
				"C" => "1", // Chalet.nl
				"E" => "2", // Chalet.eu
				"B" => "3", // Chalet.be
				"I" => "4", // Itallisima.nl
				"K" => "5", // Itallisima.be
				"Z" => "6", // zomerhuisje.nl
				"X" => "7", // venturasol.nl
				"V" => "8", // chaletsinvallandry.nl
				"Q" => "9", // chaletsinvallandry.com

			),
			"docdata_payment_title" => 'Docdata',
			"confidence_level" 		=> 'authorization', //All values: $this->types_ConfidenceLevels
			"test_wsdl"				=> TEST_WSDL,
			"production_wsdl"		=> PRODUCTION_WSDL,
			"test_webmenu"			=> TEST_WEBMENU,
			"production_webmenu"	=> PRODUCTION_WEBMENU
		);

		$this->merchant_account = array(
			"production_username" => PRODUCTION_MERCHANT_NAME, // will be provided by docdata after acceptance test
			"production_password" => PRODUCTION_MERCHANT_PASSWORD, // will be provided by docdata after acceptance test
			"test_username" => TEST_MERCHANT_NAME,
			"test_password" => TEST_MERCHANT_PASSWORD,
		);

		$this->payment_preferences = array(
			"profile" => "standard",
			"number_of_days_to_pay" => 6,
			"exhortation_period1_number_days" => 1,
			"exhortation_period1_profile" => "standard",
			"exhortation_period2_number_days" => 10,
			"exhortation_period2_profile" => "standard",
		);

		$this->custom_statuses = array(
			"new" 				=> "pending",
			"pending_payment" 	=> "pending_payment",
			"pending_refund" 	=> "pending_payment",
			"paid" 				=> "paid",
			"refunded" 			=> "payment_review",
			"charged_back" 		=> "payment_review",
			"canceled" 			=> "canceled",
			"on_hold" 			=> "holded"
		);

		$this->fallback_statuses = array(
			"new" 				=> "pending",
			"pending_payment" 	=> "pending_payment",
			"pending_refund" 	=> "pending_payment",
			"paid" 				=> "paid",
			"refunded" 			=> "payment_review",
			"charged_back" 		=> "payment_review",
			"canceled" 			=> "canceled",
			"on_hold" 			=> "holded"
		);

		$this->connection = array(
			"soap_encryption" => 0
		);

		$this->payment_profiles = array(
			"C" => "chalet.nl",
			"E" => "chalet.eu",
			"B" => "chalet.be",
			"I" => "italissima.nl",
			"K" => "italissima.be",
			"Z" => "zomerhuisje.nl",
			"X" => "venturasol.nl",
			"V" => "chaletsinvallandry.nl",
			"Q" => "chaletsinvallandry.com",
		);

		$this->payment = array(
			"docdata_payments" => array(
				"title" => "Docdata",
				"active" => 1,
				"model" => "model/method_docdata",
				"regions" => "INT",
				"group" => "docdata",
				"command" => ""
			),
			"docdata_mc" => array(
				"title" => "Mastercard",
				"active" => 1,
				"model" => "model/method_mastercard",
				"regions" => "INT",
				"group" => "docdata",
				"command" => "MASTERCARD",
				"type"	=> 5 //Docdata-betaling (creditcard), defined in admin/vars_cms.php
			),
			"docdata_mae" => array(
				"title" => "Maestro",
				"active" => 1,
				"model" => "model/method_maestro",
				"regions" => "INT",
				"group" => "docdata",
				"command" => "MAESTRO",
				"type"	=> 5 //Docdata-betaling (creditcard), defined in admin/vars_cms.php
			),
			"docdata_vi" => array(
				"title" => "Visa",
				"active" => 1,
				"model" => "model/method_visa",
				"regions" => "INT",
				"group" => "docdata",
				"command" => "VISA",
				"type"	=> 5 //Docdata-betaling (creditcard), defined in admin/vars_cms.php
			),
			"docdata_mrc" => array(
				"title" => "MrCash",
				"active" => 1,
				"model" => "model/method_mrcash",
				"regions" => "BE",
				"group" => "docdata",
				"command" => "MISTERCASH",
				"type"	=> 6 //Docdata-uitbetaling (Mister Cash), defined in admin/vars_cms.php
			),
			"docdata_idl" => array(
				"title" => "iDEAL",
				"active" => 1,
				"model" => "model/method_ideal",
				"regions" => "NL",
				"group" => "docdata",
				"command" => "IDEAL",
				"type"	=> 4, //Docdata-betaling (iDEAL), defined in admin/vars_cms.php
				"issuers" => array (
					"rbb" => array (
						"id" => "0021",
						"name" => "Rabobank"
					),
					"abn" => array (
						"id" => "0031",
						"name" => "ABN Amro Bank"
					),
					"fries" => array (
						"id" => "0091",
						"name" => "Friesland Bank"
					),
					"vlb" => array (
						"id" => "0161",
						"name" => "van Lanschot Bankiers"
					),
					"trio" => array (
						"id" => "0511",
						"name" => "Triodos Bank"
					),
					"ing" => array (
						"id" => "0721",
						"name" => "ING Bank"
					),
					"sns" => array (
						"id" => "0751",
						"name" => "SNS Bank"
					),
					"asn" => array (
						"id" => "0761",
						"name" => "ASN Bank"
					),
					"snsr" => array (
						"id" => "0771",
						"name" => "SNS Regio Bank"
					),
					"knab" => array (
						"id" => "KNAB",
						"name" => "KNAB Bank"
					),
				)
			)
		);

		$this->locking = array(
			"lock_timeout_sec" => 60, // in seconds
			"try_wait_sec" => 0.2, //in seconds can be < 1
			"steal_loc_sec" => 3600, // in seconds
			"cleanup_time_sec" => 483600, // in seconds
		);
	}

	/**
	 * Return all the configurations
	 *
	 * @return array
	 */
	public function getConfig() {
		return array(
			"general" 				=> $this->general,
			"merchant_account" 		=> $this->merchant_account,
			"payment_preferences" 	=> $this->payment_preferences,
			"payment_profiles" 		=> $this->payment_profiles,
			"custom_statuses" 		=> $this->custom_statuses,
			"connection" 			=> $this->connection,
			"fallback_statuses" 	=> $this->fallback_statuses,
			"payment" 				=> $this->payment
		);
	}

	/**
	 * Implemented in case that different merchant profiles will be needed for specific websites
	 *
	 * @param string $website_code (e.g. C, E, B etc)
	 * @return array containing account details
	 */
	public function getWebsiteConfig($website_code = null) {

		return $this->merchant_account($website_code);
	}
}