<?php
class App {

	const
		ERR 	= 400,
		INFO 	= 200,
		WARN 	= 300,
		DEBUG 	= 100;

	private static $objects     = array();
	private static $instance    = null;

	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new App();
		}
		return self::$instance;
	}

	protected function _get($key) {
		return ($this->objects[$key]) ? $this->objects[$key] : null;
	}

	protected function _set($key, $val) {
		$this->objects[$key] = $val;
	}

	public static function get($key) {
		return self::getInstance()->_get($key);
	}

	public static function set($key, $object) {
		return self::getInstance()->_set($key, $object);
	}

	public static function getUrl($url, $param) {
		return $url;
	}

	public static function getRequest() {
		global $request;

		return $request;
	}

	/**
	 * Set messages in the session (error, info, success)
	 *
	 * @param string $message
	 * @param string $type
	 */
	public static function addNotice($message = null, $type = "info") {
		if($message) {
			$_SESSION["FLASH"] = array(
			"message" 	=> $message,
				"type"		=> $type
			);
		}
	}
}

// Set all the Classes
App::set('app/config', new Config());

App::set('helper/data', new Helper_Data());
App::set('helper/config', new Helper_Config());
App::set('helper/currency', new Helper_Currency());
App::set('helper/api_create', new Helper_Api_Create());
App::set('helper/api_webmenu', new Helper_Api_Webmenu());
App::set('helper/api_abstract', new Helper_Api_Abstract());

App::set('model/order', new Order());
App::set('model/docdata', new Model_Docdata());
App::set('model/payment', new Payment());
App::set('model/customer', new Customer());

App::set('model/api_response', new Model_Api_Response());
App::set('model/api_create', new Model_Api_Create());
App::set('model/api_status', new Model_Api_Status());
App::set('model/api_cancel', new Model_Api_Cancel());

App::set('model/method_mastercard', new Model_Method_Mastercard());
App::set('model/method_maestro', new Model_Method_Maestro());
App::set('model/method_visa', new Model_Method_Visa());
App::set('model/method_ideal', new Model_Method_Ideal());
App::set('model/method_mrcash', new Model_Method_Mrcash());
App::set('model/method_docdata', new Model_Method_Docdata());