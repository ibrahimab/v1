<?php
/**
 * Generic currency helper class 
 *
 */
class Helper_Currency {
	
	private $_currencies = array(
		'EUR' => array(
			'entity' => 'EUROPE',
			'currency' => 'Euro',
			'numeric_code' => '978',
			'minor_unit' => '2'
		)
	);
	
	/**
	 * Gets amount of minor units for requested currency
	 * 
	 * @param string $currency Currency to use
	 *
	 * @return string Minor units
	 */
	public function getMinorUnits($currency) {
		if(isset($this->_currencies[$currency])) {
			$details = $this->_currencies[$currency];
			return $details['minor_unit'];
		}
		//default 2
		return '2';
	}
}
