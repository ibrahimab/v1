<?php
/**
 * Api class for cancelRequest (status update for payment order)
 *
 */
class Model_Api_Cancel extends Model_Api_Abstract {

	/**
	 * @var Model_Docdata Parent Api class
	 */
	private $_api;

	/**
	 * Docdata API method. Send a status request to Docdata
	 *
	 * @param Model_System $api System class which provides an interface to the eCommerce system we're in
	 * @param Model_Api_Response $response_object Object to access the response of the call
	 * @param array $elements Data for the call
	 *
	 * @return Model_Api_Response_Cancel
	 */
	public function call(Model_System $api, Model_Api_Response $response_object, array $elements) {
		
		$this->_api = $api;
		$result = $response_object; 
		$orderId = isset($elements["orderId"]) ? $elements["orderId"] : null;
		try {
			$this->_api->log('API call Cancel: ', self::SEVERITY_DEBUG, $orderId);

			if(isset($elements["orderId"])) {
				$elements["action"] = 'API call Cancel';
				// Log into the database
				$this->_api->log($elements, self::SEVERITY_DEBUG, $elements["orderId"]);
				unset($elements["orderId"]);
				unset($elements["action"]);
			}

			//perform cancel call (wrap elements in array as rootelement)
			$client = $this->getConnection($api);
			$client->__soapCall('cancel', array($elements));
			
			$result->setResponse(
				$client->__getLastResponse()
			);
		} catch(Exception $ex) {
			$result->setErrorResponse($ex->getMessage());
		}
		
		if ($result->hasError()) {
			//log error 
			$this->_api->log($result->getErrorMessage(), self::SEVERITY_ERROR, $orderId);
		} else {
			//cancelation was successfull, log result
			$this->_api->log('A payment order has been canceled with the key: ' . $elements['paymentOrderKey'], self::SEVERITY_INFO, $orderId);
		}
		
		return $result;
	}
}