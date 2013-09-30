<?php
/**
 * Generic helper class with function relevant to a wider scope of classes withing the Docdata plugin.
 *
 */
use Monolog\Logger;
use Monolog\DBLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\PDOHandler;
use Monolog\Processor\WebProcessor;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter;

class Helper_Data {

	private $_currency_helper;
	
	/**
	 * Add prefix to array keys
	 *
	 * @param string $prefix Prefix to add
	 * @param array $array Array to add the prefix to the first level keys
	 *
	 * @return array
	 */
	public function addPrefix($prefix, $array) {
		$prefixedArray = array();
		foreach ($array as $name => $value) {
			$prefixedArray[$prefix.$name] = $value;
		}
		return $prefixedArray;
	}
	
	/**
	 * Remove prefix from array keys
	 *
	 * @param string $prefix Prefix to remove
	 * @param array $array Array to remove the prefix from the first level
	 *
	 * @return array
	 */
	public function removePrefix($prefix, $array) {
		$unprefixedArray = array();
		foreach ($array as $name => $value) {
			if (strpos($name, $prefix) === 0) {
				$newName = substr($name, strlen($prefix));
				$unprefixedArray[$newName] = $value;
			}
		}
		return $unprefixedArray;
	}
	
	/**
	 * Log message into docdata log
	 *
	 * @param mixed $message message to log
	 * @param integer $severity App severity level
	 * 
	 * @return void
	 */
	public function log($message, $severity = App::INFO) {

		$log = new Logger('docdata');
		$log->pushHandler(new StreamHandler(ROOT . DS . 'log' . DS . 'docdata.log', Logger::DEBUG));
		$log->pushProcessor(new WebProcessor());

		$context = array();

		if(is_array($message)) {
			$formatter = new Formatter\NormalizerFormatter();

			$context = $formatter->format($message);
			$message = "Array Data:";
		}
		if(is_object($message)) {
			$context = array();
			$message = "Object Data: " . var_export($message, true);
		}

		$log->addRecord($severity, $message, $context);

	}

	/**
	 * Log message into database log
	 *
	 * @param mixed $message message to log
	 * @param integer $severity App severity level
	 *
	 * @return void
	 */
	public function dbLog($message, $severity = App::INFO, $orderId = 0) {

		// the default date format is "Y-m-d H:i:s"
		$dateFormat = "d/n/y, H:i:s";
		// the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
		$output = "%datetime% > %level_name% > %message% %context% \n";
		// finally, create a formatter
		$formatter = new LineFormatter($output, $dateFormat);

		// Create a handler
		$stream = new PDOHandler(new PDO('mysql:host='. DB_HOST .';dbname=' . DB_NAME, DB_USER, DB_PASSWORD));
		$stream->setFormatter($formatter);

		$log = new DBLogger('docdata');

		$log->pushHandler($stream);
		$context = array();

		if(is_array($message)) {
			$formatter = new Formatter\NormalizerFormatter();

			$context = $formatter->format($message);
			$message = "Data:";
		}
		if(is_object($message)) {
			$context = array();
			$message = "Object Data: " . var_export($message, true);
		}

		$log->addRecord($severity, $message, $context, $orderId);

	}


	/**
	 * Return a price in the minor unit of the given currency
	 *
	 * @param float $amount amount described in the given currency with decimals
	 * @param string $currency currency used
	 * @return int
	 */
	public function getAmountInMinorUnit($amount, $currency) {
		
		if($this->_currency_helper === null) {
			$this->_currency_helper = App::get('helper/currency');
		}
		
		//get currencies minorunit amount
		$minor_units = $this->_currency_helper->getMinorUnits($currency);
		
		return round($amount * pow(10, $minor_units));
	}
	
	/**
	 * Return a price in the major unit of the given currency
	 *
	 * @param float $amount amount described in the given currency without the decimels
	 * @param string $currency currency used
	 * @return int
	 */
	public function getAmountInMajorUnit($amount, $currency) {
		
		if($this->_currency_helper === null) {
			$this->_currency_helper = App::get('helper/currency');
		}
		
		//get currencies minorunit amount
		$major_units = $this->_currency_helper->getMinorUnits($currency);
		
		return $amount / pow(10, $major_units);
	}
}