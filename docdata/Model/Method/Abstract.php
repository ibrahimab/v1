<?php
/**
 * Abstract class used as a base for payment methods
 *
 */
abstract class Model_Method_Abstract {
	/* Payment code which is the unique identifier for the payment method */
	protected $_code; 
	 /* Prefix for Create order arguments */
	const PREFIX_CREATE = 'CO_';
	 /* Prefix for Show order arguments */
	const PREFIX_SHOW = 'SO_';
	/* Prefix for Show order arguments */
	const ORDER_STATUS_SETTING = 'order_status';
	/* Defines how the payments are handled by System */
	const PAYMENT_ACTION_SETTING = 'authorize';
	
	/**
	 * Availability options
	 * Set System availability in order to use their respective functionality
	 */
	protected $_isGateway 				= true;
	protected $_canAuthorize 			= true;  // Set true, if you have authorization step.
	protected $_canCapture 				= false; // Set true, if you payment method allows to perform capture transaction (usally only credit cards methods)
	protected $_canCapturePartial 		= true;
	protected $_canRefund 				= false;  // Set true, if online refunds are available
	protected $_canRefundInvoicePartial = false;
	protected $_canVoid 				= true;  // Set true, if you can cancel authorization via API online
	protected $_canUseInternal 			= true;  // Enables use of method internally (backend)
	protected $_canUseCheckout 			= true;  // Enables use of method for customers (frontend)
	protected $_canUseForMultishipping 	= false; // Set true, if method can be used for shipping to several addresses
	protected $_isInitializeNeeded      = true; // call initialize on method instead of authorize on creation or order
	
	/**
	 * Return the url to redirect which will initiate the order at Docdata
	 * 
	 * @return string
	 */
	public function getOrderPlaceRedirectUrl() {
		//sets payment method code and the '_secure' arg to use secure connection
		$default_args = array(
			'pm_code' => $this->_code
		);
		
		//get payment specific args needed to create an order
		$additional_args_create_order = App::get('helper/data')->addPrefix(
			self::PREFIX_CREATE,
			$this->getAdditionalParametersCreateOrder()
		);
		//get payment specific args needed to show an order (handling the payment in Docdata webmenu)
		$additional_args_show_order = App::get('helper/data')->addPrefix(
			self::PREFIX_SHOW,
			$this->getAdditionalParametersShowOrder()
		);
		
		//combine args and return the correct redirect url
		$args = array_merge($default_args, $additional_args_create_order, $additional_args_show_order);
		return App::getUrl('docdata/payment/redirect', $args);
	}
	
	/**
	 * Return parameters specific to the payment method used for the redirection
	 * These additional parameters are used for the Create Payment Order call
	 * 
	 * @return array
	 */
	public function getAdditionalParametersCreateOrder() {
		return array();
	}


	/**
	 * Return parameters specific to the payment method used for the redirection
	 * These additional parameters are used to for the Docdata webmenu in order to display correct payment screen 
	 * 
	 * @return array
	 */
	public function getAdditionalParametersShowOrder() {
		return array();
	}
	
	/**
	 * Return true if the payment method can be used in the checkout
	 *
	 * @param $quote Quote belonging to current checkout
	 *
	 *
	 * @return boolean True if the payment method can be used in the checkout, otherwise false
	 */
	public function isAvailable($quote = null) {
		// Is the module active?
		$module = App::get('helper/config')->isActive();
		
		if (!$module) {
			return false;
		}
		
		// Does the parent agree that the method is usable?
		$parent = parent::isAvailable($quote);
		return ($parent) ? $this->checkDocdataWebmenu() : false;
	}
	
	/**
	 * Checks if user needs to select payment method before Docdata webmenu
	 *
	 * @return boolean True if user needs to select payment method before Docdata webmenu otherwise false
	 */
	protected function checkDocdataWebmenu() {
		// Do we need to use the webmenu?
		return !App::get('helper/config')->getItem('general/webmenu_active');
	}
	
	/**
	 * Check if the payment method can be used with the store currency
	 *
	 * @param string $currency_code Code for the used currency
	 *
	 *
	 * @return boolean true if the currency is supported, otherwise false
	 */
	public function canUseForCurrency($currency_code) {
		//get currencies defined in config (these are the currencies supported by Docdata)
		$docdata_currencies = explode(',', App::get('helper/config')->getItem('currencies'));
		
		return in_array($currency_code, $docdata_currencies);
	}
	
	/**
	 * Check if this payment method can be used for a specific country
	 *
	 * @param string $country Country of the billing address
	 *
	 *
	 * @return boolean True if the payment method can be used for the specified country, otherwise false
	 */
	public function canUseForCountry($country) {
		//extract countries available for current payment method
		$config_helper          = App::get('helper/config');
		$countries_setting      = $config_helper->getPaymentMethodItem($this->_code, 'regions');
		$available_countries    = explode(',', $countries_setting);
		$european_countries     = explode(',', $config_helper->getItem('european_countries'));
		
		//check if country is supported by payment method
		//in case 'EUR' is in payment method countries the european countries are supported
		//in case 'INT' is in payment method countries it is internationally supported
		if (in_array('INT', $available_countries)
			|| in_array($country, $available_countries)
			|| (in_array('EUR', $available_countries) && in_array($country, $european_countries))
		) {
			return true;
		}

		return false;
	}
	
	/**
	 * Retrieve information from payment configuration
	 * 
	 * @param string $field Field to fetch from the configuration
	 * @param int    $storeId Store ID to fetch the given item from, optional
	 *
	 * @return  mixed
	 */
	public function getConfigData($field, $storeId = null) {
		//in case field is order_status extract it via config settings, used for the initial status of order
		if ($field === self::ORDER_STATUS_SETTING) {
			return App::get('helper/config')->getItem('new', Helper_Config::GROUP_STATUS);
		} else {
			return parent::getConfigData($field, $storeId);
		}
	}
	
	/**
	 * Get config payment action url
	 * Used to universalize payment actions when processing payment place
	 *
	 * @return string
	 */
	public function getConfigPaymentAction() {
		return self::PAYMENT_ACTION_SETTING;
	}
	
	/**
	 * Allows payment method to update data in the create call to Docdata.
	 * 
	 * @param array $call_data Data to be used in the create call
	 * @param Order $order Order where the create call is made for
	 * @param array $additional_params Extra data can be provided via this array
	 * 
	 * @return array Updated call data
	 */
	public function updateCreateCall(array $call_data, Order $order, array $additional_params) {
		return $call_data;
	}
	
}