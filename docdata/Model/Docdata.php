<?php
/**
 * Api calss for createRequest (creating payment order)
 *
 */
class Model_Docdata implements Model_System {

	/* @var Helper_Config */
	private $_config;
	
	/* @var Order */
	private $_order;

	/* @var Model_Api_Abstract */
	private $_call_api;

	/**
	 * Constructor 
	 *
	 * @return Model_Docdata Class used for interfacing between System and Docdata API
	 */
	public function __construct() {
		$this->_config = App::get('helper/config');
	}
	
	/**
	 * Checks to see if the last call received error
	 *
	 * @return boolean True if last call has error, otherwise False
	 */
	public function hasError() {
		return $this->_call_api !== null && $this->_call_api->hasError();
	}
	
	/**
	 * Gets the last error message if any
	 *
	 * @return string Returns error message if any, otherwise returns null.
	 */
	public function getErrorMessage() {
		if ($this->_call_api !== null) {
			return $this->_call_api->getErrorMessage();
		}
		return null;
	}
	
	/**
	 * Log message into docdata log
	 *
	 * @param mixed $message message to log
	 * @param string $severity severity level
	 * @param int $order_id; If is not null we log the message into the database
	 * 
	 * @return void
	 */
	public function log($message, $severity = Model_Api_Abstract::SEVERITY_INFO, $order_id = null) {
		
		//debug is fallback severity
		$monolog_severity = App::DEBUG;
		
		//map severity to app severity
		switch($severity) {
			case Model_Api_Abstract::SEVERITY_INFO:
				$monolog_severity = App::INFO;
				break;
			case Model_Api_Abstract::SEVERITY_WARN:
				$monolog_severity = App::WARN;
				break;
			case Model_Api_Abstract::SEVERITY_ERROR:
				$monolog_severity = App::ERR;
				break;
			case Model_Api_Abstract::SEVERITY_DEBUG:
				$monolog_severity = App::DEBUG;
				break;
		}

		if($order_id) {
			App::get('helper/data')->dbLog($message, $monolog_severity, $order_id);
		} else {
			App::get('helper/data')->log($message, $monolog_severity);
		}
	}
	
	/**
	 * Translate a string using the System helper class
	 *
	 * @param string $string The string to translate
	 *
	 * @return string Translated string
	 */
	public function translate($string) {
		return __($string);
	}
	
	/**
	 * Uses the order data to create a payment order request
	 *
	 * @param Order $order Order to perform the create call with
	 * @param array $additional_params Additional parameters to use in the create call
	 *
	 * @return Model_Docdata instance of the class Model_Docdata
	 */
	public function createCall(Order $order, array $additional_params) {
            
		// Register given order on object
		$this->_order = $order;
		$helper = App::get('helper/api_create');

		//add elements for the create call
		$call_elements = array();
		$call_elements['version'] = $helper->getApiVersion();
		$call_elements['merchant'] = $helper->getMerchantDetails();
		$call_elements['merchantOrderReference'] = $order->getRealOrderId() . "_" . time();
		$call_elements['paymentPreferences'] = $helper->getPaymentPreferences($order->getWebsiteCode());
		$menu_pref = $helper->getMenuPreference($order->getWebsiteCode());
                
		if ($menu_pref !== null) {
			$call_elements['menuPreferences'] = $menu_pref;
		}
		$call_elements['shopper'] = $helper->getShopper($order);
		$call_elements['totalGrossAmount'] = $helper->getTotalGrossAmount($order);
		$call_elements['billTo'] = $helper->getBillTo($order);

		$accommodation = substr($order->getAccommodationName(), 0, 50);

		$call_elements['description'] = utf8_encode($accommodation); # Max 50 chars
		
		//call payment methods for additional actions
		$payment = $order->getPayment();
		$method = $payment->getMethodInstance()->getCode();
                
		//get model belonging to payment method and use it to update the $call_elements
		$model_ref = App::get('helper/config')->getPaymentMethodItem($method, 'model');
                
		if ($model_ref !== null) {
			$call_elements = App::get($model_ref)->updateCreateCall($call_elements, $order, $additional_params);
		}

		$call_elements['orderId'] = $order->getId();
		$response_object = App::get('model/api_response');
		$this->_call_api = App::get('model/api_create')->call($this, $response_object, $call_elements);

		if(!$this->_call_api->hasError()) {
			$node = $this->_call_api->getNode('key');
			//only 1 result so get first and extract value by converting to string (simplexmlelement)

			$payment_order_key = (string)$node[0];
			$this->_order->setClusterKey($payment_order_key);
		}

        return $this;
	}

	/**
	 * Requires the order to cancel and collects the required information for a cancel call from the system and then passes it to the call API
	 *
	 * @param Order $order Order object used to perform actions on
	 *
	 * @return Model_Docdata This instance of the class so additional information may be asked about the result
	 */
	public function cancelCall(Order $order) {
		// Register given order on object
		$this->_order = $order;

		// For the cancel call we only need basic info, for which functions are already defined in abstract.
		$helper = App::get('helper/api_abstract');

		// Collect required data for elements in an array
		$call_elements = array(
			'version' => $helper->getApiVersion(),
			'merchant' => $helper->getMerchantDetails($order->getStoreId()),
			'paymentOrderKey' => $order->getClusterKey()
		);

		$call_elements['orderId'] = $order->getId();

		$response_object = App::get('model/api_response');
		// Create call API object, pass self and the call elements
		$this->_call_api = App::get('model/api_cancel')->call($this, $response_object, $call_elements);

		// Return current instance which will fill the communication between the call API and the system in question
		return $this;
	}

	/**
	 * Performs a status update call with the given order. Any actions required on the order will also be executed with the same instance of this class
	 *
	 * @param Order $order Order to call the status update on
	 * @param array $additional_params Extra parameters to be used in the call
	 *
	 * @return Model_Docdata This instance of the class so additional information may be asked about the result
	 */
	public function statusCall(Order $order, array $additional_params) {
		// Register given order on object
		$this->_order = $order;

		// For the status call we only need basic info, for which functions are already defined in abstract.
		$helper = App::get('helper/api_abstract');

		// Collect required data for elements in an array
		$call_elements = array(
			'version' => $helper->getApiVersion(),
			'merchant' => $helper->getMerchantDetails($order->getStoreId()),
			'paymentOrderKey' => $order->getClusterKey()
		);

		$call_elements['orderId'] = $order->getId();

		// Create call API object, pass self and the call elements
		$response_object = App::get('model/api_response');
		$status_api = App::get('model/api_status');
		if (isset($additional_params['QueryOnly']) && $additional_params['QueryOnly'] === true) {
			$status_api->setQueryOnly(true);
		}

		$this->_call_api = $status_api->call($this, $response_object, $call_elements);

		// Return current instance which will fill the communication between the call API and the system in question
		return $this;
	}
	
	/**
	 * Sets the given data on the order, a type may be specified if additional filtering is needed.
	 *
	 * @param mixed $data The data to be processed and saved
	 * @param string $type Anything other than generic fields need additional processing.
	 *
	 * @return void
	 */
	public function setOrderData($data, $type = self::DATA_GENERIC) {

		$order = $this->_order;
		
		switch ($type) {
			case self::DATA_PAYMENT:
				$payment = $order->getPayment();

				// Add payment id to be inserted
				$order->setDocdataPaymentId($data['id']);
	
				// Update method itself, if changed
				$payment->setMethod(
					App::get('helper/config')->getPaymentCodeByCommand(
						$data['paymentMethod']
					)
				);
				break;
			case self::DATA_GENERIC:
				foreach ($data as $field => $value) {
					$order->$field = $value;
				}
				break;
		}

	}

	/**
	 * Enabled the api classes to suggest a range of statuses, which this function needs to process,
	 * check which state we currently have and whether we can advance it to one of the suggested states.
	 *
	 * @param array $statusses A list of statusses/events as defined in the interface of this class
	 * @param int $captured Captured amount in minor unit if applicable
	 * @param int $refunded Refunded amount in minor unit if applicable
	 *
	 * @return void
	 */
	public function setOrderStatus(array $statusses, $captured = null, $refunded = null) {

		$order = $this->_order;

		// 1. Determine which status to use
		// This function takes an array of statuses since in theory an order may become several states at once
		// Usually that wont happen but in a case where something was paid, then cancelled, the cancel should take precedence
		$final_status = null;
		foreach ($statusses as $status => $_msg) {
			// Make sure the first iteration always sets the status
			if ($final_status === null) {
				$final_status = $status;
				$msg = $_msg;
			}

			// Cancelled status takes precedence over all.
			// An order should not be able to receive cancelled state if for some reason a payment is completed later on,
			// because that payment would not be cancelled, and thus the status would not be added.
			// After cancelled we do not want to change stuff if an order actually looks to be paid already
			if ($status === self::STATUS_CLOSED_CANCELED) {
				$final_status = $status;
				$msg = $_msg;
				break;
			} elseif ($status === self::STATUS_CLOSED_PAID) {
				$final_status = $status;
				$msg = $_msg;
			}
		}

		// If there were multiple statusses give, then log that, because that should be weird
		if (count($statusses) > 1) {
			$this->log("Multiple statusses detected. Selected $final_status for use and going on. Other statusses: ".implode(', ', array_keys($statusses)));
		}
		
		$order_id = $order->getId();

		//do not change an order that is already set to complete state
		if ($order->getState() === Order::STATE_COMPLETE) {
			$this->log("Order [$order_id] received update that was ignored (order is already on state 'complete'). ", App::INFO);
			// Log in database
			$this->log("Order [$order_id] received update that was ignored (order is already on state 'complete'). ", App::INFO, $order_id);
			return;
		}
		
		// Get possible self specified statusses
		$config = $this->_config;
		$status = $config->getItem($final_status, $config::GROUP_STATUS);

		$helper = App::get('helper/data');
		
		// Compare with minor unit for better compatibility
		$order_currency_code = $order->getOrderCurrencyCode();

		// Check if the final status is the same as the current status, which should happen too often
		if ($status === $order->getStatus()) {
			$this->log("Ordernr [$order_id] trying to set the order to $final_status status which it is already on.");
			// Log in database
			$this->log("Ordernr [$order_id] trying to set the order to $final_status status which it is already on.", App::INFO, $order_id);
			return;
		}

		$state = null;

		// 2. Determine what state belongs to that status and what message to add
		switch ($final_status) {
			case self::STATUS_NEW:
				$state = $order::STATE_NEW;
				break;
			case self::STATUS_STARTED:
			case self::STATUS_PARTIAL_PAID:
				//if status of order currently is cancele
				$state = $order::STATE_PROCESSING;
				break;
			case self::STATUS_CLOSED_REFUNDED:
				$state = $order::STATE_PAYMENT_REVIEW;
			case self::STATUS_PARTIAL_REFUNDED:
				// If not yet fully refunded we'll get the right state and be done with it
				$state = $state ? $state : $order::STATE_PENDING_PAYMENT;
				break;
			case self::STATUS_CLOSED_PAID:
				//if status of order currently is canceled
				$due = $order->getBaseTotalDue();
				$ordered = $helper->getAmountInMinorUnit($due, $order_currency_code);

				if ($captured <= $ordered && $due != 0) {
					// only change state if order is actually changed
					$state = $order::STATE_PROCESSING;
				} elseif ($due == 0) {
					$this->log("Order [$order_id] due amount is already 0 ($due), thus it does not need to be registered.", App::INFO);
					// Log in database
					$this->log("Order [$order_id] due amount is already 0 ($due), thus it does not need to be registered.", App::INFO, $order_id);
				} else {
					$this->log("Order [$order_id] set to status PAID but couldn't match due amount with captured amount.", App::ERR);
					// Log in database
					$this->log("Order [$order_id] set to status PAID but couldn't match due amount with captured amount.", App::ERR, $order_id);
				}
				break;
			case self::STATUS_CHARGEBACK:
				//fully refunded is a closed state
				$state = $order::STATE_CLOSED;
				break;
			case self::STATUS_CLOSED_CANCELED:
				$state = $order::STATE_CANCELED;
				break;
			case self::STATUS_ON_HOLD:
				$state = $order::STATE_HOLDED;
				break;
		}
                
		if ($state !== null) {
			// 3. Set the payment status and the message
			$order->getPayment()->setStatus($status, $msg);

			// When the status is "paid" we add the entry to the invoice
			if($final_status == self::STATUS_CLOSED_PAID) {
				$order->setInvoice($helper->getAmountInMajorUnit($captured, $order_currency_code));
			}

		} else {
			$this->log("Ordernr [$order_id] no status change needed", App::INFO);
		}

		if ($state === null && $status !== null) {
			$this->log("Ordernr [$order_id] tried setting order to ". var_export($status, true) ." but couldn't find a matching state.", App::ERR);
			// Log in database
			$this->log("Ordernr [$order_id] tried setting order to ". var_export($status, true) ." but couldn't find a matching state.", App::ERR, $order_id);
		}
	}
	
	/**
	 * Sets the Docdata reference (payment order key) in the current order
	 *
	 * @param string $payment_order_key Docdata payment order key for the current order.
	 *
	 * @return void
	 */
	public function setDocdataPaymentOrderKey($payment_order_key) {
		if ($this->_order !== null) {
			//set key and save order
			$this->_order->setDocdataPaymentOrderKey($payment_order_key);
		}
	}

	/**
	 * Retrieves the WSDL of the API
	 *
	 * @return string WSDL uri of the API
	 */
	public function getWsdlUri() {
		return $this->_config->getWsdlUri();
	}
	
	/**
	 * Checks if debug mode is enabled
	 *
	 * @return boolean True if debug mode is enabled, otherwise False
	 */
	public function isDebugMode() {
		return !$this->_config->isProduction();
	}
	
	/**
	 * Checks confidence level
	 *
	 * @return string of Confidence level
	 */
	public function getConfidenceLevel() {
		return $this->_config->getItem('confidence_level', Helper_Config::GROUP_GENERAL);
	}
}