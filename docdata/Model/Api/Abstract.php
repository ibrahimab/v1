<?php
/**
 * Abstract class for api models
 *
 */
abstract class Model_Api_Abstract {
	
	const SEVERITY_DEBUG 	= 'debug',
		  SEVERITY_INFO		= 'information',
		  SEVERITY_WARN 	= 'warning',
		  SEVERITY_ERROR 	= 'error';
	
	
	/**
	 * Docdata API method. Send a message request to Docdata
	 *
	 * @param Model_System $api System class which provides an interface to the eCommerce system we're in
	 * @param Model_Api_Response $response_object Object to access the response of the call
	 * @param array $elements Data for the call
	 *
	 * @return Model_Api_Response_Abstract
	 */
	abstract public function call(Model_System $api, Model_Api_Response $response_object, array $elements);
	
	/**
	 * Creates a soap connection
	 *
	 * $return 
	 */
	public function getConnection(Model_System $api) {
		$uri = $api->getWsdlUri();
		
		$options = array('trace' => true,
				'keep_alive' => false);
		
		$encrypt = App::get('helper/config')->getItem('connection/soap_encryption');
		if ($encrypt) {
			$opts = array(
				'ssl' => array('ciphers'=>'RC4-SHA')
			);
			$options['stream_context'] = stream_context_create($opts);
		}
		
		//create new api connection to be certain it has the correct settings
		$connection = new SoapClient(
			$uri,
			$options
		);
		return $connection;
	}
}