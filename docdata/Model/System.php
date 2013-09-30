<?php
/**
 * Interface for orders/payments 
 *
 */ 
interface Model_System {
	const DATA_PAYMENT = 'payment',
		  DATA_GENERIC = 'generic';

	//    Constant name				  Config name			   System status code		System state code
	const STATUS_NEW 				= 'new',				// pending					new
		  STATUS_STARTED 			= 'pending_payment',	// pending_payment			pending_payment
		  STATUS_PARTIAL_PAID 		= 'pending_payment',	// pending_payment			pending_payment
		  STATUS_PARTIAL_REFUNDED 	= 'pending_refund',		// pending_payment			pending_payment
		  STATUS_CLOSED_PAID 		= 'paid',				// payment_review			payment_review
		  STATUS_CLOSED_REFUNDED 	= 'refunded',			// payment_review			payment_review
		  STATUS_CLOSED_CANCELED 	= 'canceled',			// canceled					canceled
		  STATUS_CHARGEBACK 		= 'charged_back',		// payment_review			payment_review
		  STATUS_ON_HOLD 			= 'on_hold';			// holded					holded

	/**
	 * Uses the order data to create a payment order request
	 *
	 * @param Order $order Order to perform the create call with
	 * @param array $additional_params Additional parameters to use in the create call
	 *
	 * @return Model_Docdata instance of the class Model_Docdata
	 */
	public function createCall(Order $order, array $additional_params);
	/**
	 * Requires the order to cancel and collects the required information for a cancel call from the system and then passes it to the call API
	 *
	 * @param Order $order Order object used to peform actions on
	 *
	 * @return Model_Docdata This instance of the class so additional information may be asked about the result
	 */
	public function cancelCall(Order $order);
	/**
	 * Peforms a status update call with the given order. Any actions required on the order will also be executed with the same instance of this class
	 *
	 * @param Order $order Order to call the status update on
	 * @param array $additional_params Extra parameters to be used in the call
	 *
	 * @return Model_Docdata This instance of the class so additional information may be asked about the result
	 */
	public function statusCall(Order $order, array $additional_params);
	/**
	 * Checks if the last made api call (if any) contained an error
	 *
	 * @return Boolean True if there was an error, Otherwise false.
	 */
	public function hasError();
	/**
	 * Gets the last error message if any
	 *
	 * @return string Returns error message if any, otherwise returns null.
	 */
	public function getErrorMessage();
	/**
	 * Log message into docdata log
	 *
	 * @param mixed $message message to log
	 * @param integer $severity App severity level
	 * 
	 * @return void
	 */
	public function log($message, $severity = App::INFO);
	/**
	 * Translate a string using the host  e-commerce system mechanics and set language
	 *
	 * @param string $string The string to translate
	 *
	 * @return string translated string as processed by the host e-commerce system
	 */
	public function translate($string);
	/**
	 * Retrieves the WSDL of the API
	 *
	 * @return string WSDL uri of the API
	 */
	public function getWsdlUri();
	/**
	 * Checks if debug mode is enabled
	 *
	 * @return boolean True if debug mode is enabled, otherwise False
	 */
	public function isDebugMode();
	/**
	 * Sets the Docdata reference (payment order key) in the current order
	 *
	 * @param string $payment_order_key Docdata payment order key for the current order.
	 *
	 * @return void
	 */
	public function setDocdataPaymentOrderKey($payment_order_key);
	
	/**
	 * Checks confidence level
	 *
	 * @return string of Confidence level
	 */
	public function getConfidenceLevel();
}

class Model_System_Exception extends ErrorException {
	
	const VALIDATION_DATA 	= 20;
	const VALIDATION_EMAIL 	= 21;
	
}