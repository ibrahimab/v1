<?php
/**
 * Api class for createRequest (creating payment order)
 *
 */
class Model_Api_Create extends Model_Api_Abstract {
	
	/**
	 * @var Model_Docdata Parent Api class
	 */
	private $_api;

	/**
	 * Docdata API method. Send a payment request to Docdata
	 *
	 * @param Model_System $api System class which provides an interface to the eCommerce system we're in
	 * @param Model_Api_Response $response_object Object to access the response of the call
	 * @param array $elements Data for the call
	 *
	 * @return Model_Api_Response_Create
	 */
	public function call(Model_System $api, Model_Api_Response $response_object, array $elements) {
		$this->_api = $api;
		$result = $response_object; 
		$helper = App::get('helper/api_create');
		$orderId = isset($elements["orderId"]) ? $elements["orderId"] : null;
		try {
			$this->_api->log('API call Create: ', self::SEVERITY_DEBUG, $orderId);
			if(isset($elements["orderId"])) {
				$elements["action"] = 'API call Create';
				// Log into the database
				$this->_api->log($elements, self::SEVERITY_DEBUG, $elements["orderId"]);
				unset($elements["orderId"]);
				unset($elements["action"]);
			}
			//perform create call (wrap elements in array as rootelement)
			$client = $this->getConnection($api);
			$client->__soapCall('create', array($elements));

			$result->setResponse(
				$client->__getLastResponse()
			);

		} catch(Exception $ex) {
			$result->setErrorResponse($ex->getMessage());
		}
 
		if ($result->hasError()) {
			//log error 
			$api->log($result->getErrorMessage(), self::SEVERITY_ERROR, $orderId);
		} else {
			// save the payment order key
			$node = $result->getNode('key');

			//only 1 result so get first and extract value by converting to string (simplexmlelement)
			$payment_order_key = (string)$node[0];
			$api->setOrderReference($elements["merchantOrderReference"]);

			$css_id = $helper->getMenuPreference($api->getWebsiteCode());
			$css = $css_id['css']['id'];
			$api->setCssId($css);

			$api->setDocdataPaymentOrderKey($payment_order_key);
			$api->log('A payment order has been created with the key: '.$payment_order_key, self::SEVERITY_INFO, $orderId);

			$api->setOrderStatus(
				array(
					$api::STATUS_NEW => $api->translate("New order payment created at Docdata.")
				)
			);
		}

		return $result;
	}
}