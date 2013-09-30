<?php
/**
 * Abstract helper class for api helpers
 *
 */
class Helper_Api_Abstract {
	//const api version
	const API_VERSION = '1.0';

	/**
	 * Retrieves the current API version
	 *
	 * @return string API version
	 */
	public function getApiVersion() {
		return self::API_VERSION;
	}

	/**
	 * Retrieves the configured merchant data
	 *
	 * @return array Merchant data
	 */
	public function getMerchantDetails($storeId = null) {

		return array(
			// Todo: get from Config class
			'name' => MERCHANT_NAME,
			'password' => MERCHANT_PASSWORD
		);
	}

}