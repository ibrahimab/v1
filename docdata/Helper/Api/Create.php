<?php
/**
 * API helper class for create call
 *
 */
class Helper_Api_Create extends Helper_Api_Abstract {
	const GUEST_SHOPPER_ID = 'GUEST',
		GENDER_UNKNOWN = 'U',
		DEFAULT_ENCODING = 'UTF-8',
		DEFAULT_WEBMENU_LANGUAGE = 'en'; // ISO 639-1

	// match pattern (1, 12, 123, etc 1a, 1bv, 23ab, etc)
	//protected $pattern = "/([0-9]{1,})[\\s]{0,1}([a-z]{0,})/";

	/**
	 * @var string Matches possible house numbers + additions
	 *
	 * When applied to the following example:
	 * Derpaderpastreet 1, 12, 123, 1a, 1bv, 23ab, 1-a, 1-bv, 23-ab Derpaderpastreet
	 *
	 * Will match the numbers + letter additions (with dash) while leaving out
	 * the street. This should cover most international street formats, even when
	 * house numbers may be placed in front of the street.
	 */
	protected $pattern = "/\b(?P<numbers>[0-9]+[\\s-]*[a-z,0-9]{0,2}\\b)\b/i";

	/**
	 * Retrieves the configured merchant data
	 *
	 * @param string $website_code The website code (C, E, B)
	 * @return array Merchant data
	 */
	public function getPaymentPreferences($website_code = "C") {

		$cfg_helper = App::get('helper/config');
		$cfg_group = Helper_Config::GROUP_PAYMENT_PREF;
		$cfg_profile_group = Helper_Config::GROUP_PAYMENT_PROFILE;

		$result = array(
			'profile' => $cfg_helper->getItem($website_code, $cfg_profile_group),
			'numberOfDaysToPay' => $cfg_helper->getItem('number_of_days_to_pay', $cfg_group)
		);

		//add exhortation period 1 if configured
		$period_days = $cfg_helper->getItem('exhortation_period1_number_days', $cfg_group);
		$period_profile = $cfg_helper->getItem('exhortation_period1_profile', $cfg_group);

		if (!empty($period_days) && !empty($period_profile)) {
			$result['exhortation'] = array();
			$result['exhortation']['period1'] = array(
				'numberOfDays' => $period_days,
				'profile' => $period_profile
			);
		}

		//add exhortation period 2 if configured
		$period_days2 = $cfg_helper->getItem('exhortation_period2_number_days', $cfg_group);
		$period_profile2 = $cfg_helper->getItem('exhortation_period2_profile', $cfg_group);

		//in case there is no exhortation 1 the 2nd should not be sent either.
		if (!empty($period_days2) && !empty($period_profile2) && isset($result['exhortation'])) {
			$result['exhortation']['period2'] = array(
				'numberOfDays' => $period_days2,
				'profile' => $period_profile2
			);
		}

		return $result;
	}

	/**
	 * Retrieves the configured menu preference data
	 *
	 * @param string $website_code. Default is "C" for chalet.nl
	 * @return array Preference data
	 */
	public function getMenuPreference($website_code = "C") {
		$result = null;

		$css = App::get('helper/config')->getItem(
			'webmenu_css_id',
			Helper_Config::GROUP_GENERAL
		);

		$css = $css[$website_code];

		if (!empty($css)) {
			$result = array(
				'css' => array(
					'id' => $css
				)
			);
		}

		return $result;
	}

	/**
	 * Retrieves the customer gender
	 *
	 * @param string $gender Gender of the customer
	 *
	 * @return string Docdata gender
	 */
	private function _getDocdataGender($gender = null) {

		switch($gender) :
			case "1" :
				$result = "M";
				break;
			case "2" :
				$result = "F";
				break;
			default:
				$result = self::GENDER_UNKNOWN;
				break;
		endswitch;

		return $result;
	}

	/**
	 * Retrieves Website language
	 *
	 * @param Order $order instance
	 *
	 * @return string Website language
	 */
	public function getLanguage(Order $order) {

		$locale = $order->getLanguage();

		// language must be supported by Docdata, the order will be cancelled if it is not...
		// We might want to make this option configurable per store instead of substituting
		// anything not recognized by english...
		$supported = array('nl','en','de','cz','da','es','fi','fr','hu','it','no','pl','pt','sv');
		if (!in_array($locale, $supported)) {
			$locale = self::DEFAULT_WEBMENU_LANGUAGE;
		}
		return $locale;
	}

	/**
	 * Retrieves Name data from user
	 *
	 * @param mixed $user_data Object containing user data
	 *
	 * @return string Name data
	 */
	public function getName($user_data) {

		if(!$user_data) return null;

		$initials = $user_data->getInitials();

		if(empty($initials)) {
			$firstnames = explode(' ', trim($user_data->getFirstname()));
			foreach($firstnames as $part) {
				if(!empty($part)) {
					$initials .= substr($part, 0, 1) . '.';
				}
			}
		}

		return array(
			'initials' => utf8_encode($this->_limitLength($initials, 35)), 					// Max 35 chars
			'first' => utf8_encode($this->_limitLength($user_data->getFirstname(), 35)), 	// Max 35 chars
			'last' => utf8_encode($this->_limitLength($user_data->getLastname(), 35))		// Max 35 chars
		);
	}

	/**
	 * Retrieves the shopper data
	 *
	 * @param Order $order Order for which the shopper needs to be extracted
	 *
	 * @return array Shopper data
	 */
	public function getShopper(Order $order) {

		$id = $order->getCustomerId();
		$result = array();

		if ($id) {
			//gets Model_Customer
			$customer = App::get('model/customer')->load($id, $order->getId());

			$result['id'] = $id;
			$result['name'] = $this->getName($customer);
			$result['email'] = $this->_validateEmail($customer->getEmail()); // Max 100 chars
			$result['gender'] = $this->_getDocdataGender($customer->getGender());

			$dob = $customer->getDob();
			if($dob !== null) {
				//extract only date section (time is not needed)
				$sections = explode(' ', $dob);
				$result['dateOfBirth'] = $sections[0];
			}
		} else {
			//gets Order_Address
			$billingAddress = $order->getBillingAddress();

			$result['id'] = self::GUEST_SHOPPER_ID;
			$result['name'] = $this->getName($billingAddress);
			$result['email'] = $this->_validateEmail($billingAddress->getEmail()); // Max 100 chars
			$result['gender'] = $this->_getDocdataGender($order->getCustomerGender());

			$dob = $order->getCustomerDob();
			if($dob !== null) {
				//extract only date section (time is not needed)
				$sections = explode(' ', $dob);
				$result['dateOfBirth'] = $sections[0];
			}
		}

		$result['language'] = array('code' => $this->getLanguage($order));

		return $result;
	}

	/**
	 * Retrieves the total gross amount
	 *
	 * @param Order $order Order for which the shopper needs to be extracted
	 *
	 * @return array Total gross amount data
	 */
	public function getTotalGrossAmount(Order $order) {
		global $request;

		$currency = $order->getOrderCurrencyCode();
		$gross_amount = $order->getBaseTotalDue();

		$payment_type = $request->getParam('payment_type');

		if ($order->getBaseTotalDue() < $order->getGrandTotal()) $payment_type = $order::FULL_PAYMENT;
		switch($payment_type) {
			case $order::ADVANCE_PAYMENT_1:
				$gross_amount = $order->getAdvancePaymentAmount(1);
			break;
			case $order::ADVANCE_PAYMENT_2:
				$gross_amount = $order->getAdvancePaymentAmount(2);
				break;
			case $order::FULL_PAYMENT:
				$gross_amount = $order->getBaseTotalDue();
			break;
		}

		$total_gross_amount = App::get('helper/data')->getAmountInMinorUnit($gross_amount, $currency);
		return array('_' => $total_gross_amount, 'currency' => $currency);
	}

	/**
	 * Retrieves address data from object
	 *
	 * @param Customer $address Address object
	 *
	 * @return array Address data
	 */
	private function _getAddressData(Customer $address) {
		$result = array();

		$company = $address->getCompany();
		if ($company !== null) {
			$result['company'] = $this->_limitLength($company, 35); // Max 35 chars
		}

		// Try to get a docdata/afterpay specific address first
		$street_full = $address->getStreetFull();
		$result['street'] = utf8_encode($this->_limitLength($this->_getStreetFromAddress($street_full), 35)); // Max 35 chars

		$result['houseNumber'] = $this->_limitLength($this->_getStreetNumber($street_full), 35); // Max 35 chars
		$house_nr_add = $this->_getStreetNumberAddition($street_full);
		if (!empty($house_nr_add)) {
			$result['houseNumberAddition'] = $this->_limitLength($house_nr_add, 35); // Max 35 chars
		}

		// suppress spaces in postal code
		$result['postalCode'] = str_replace(' ', '', $address->getPostcode()); // Min 1 character max 50, NMTOKEN
		$result['city'] = utf8_encode($this->_limitLength($address->getCity(), 35)); // Max 35 chars
		$result['country'] = array('code' => $address->getCountryId());

		return $result;
	}

	/**
	 * Retrieves street number from full address
	 *
	 * @param string $street_full Full street
	 *
	 * @return mixed Streetnumber
	 */
	private function _getStreetNumber($street_full) {
		$numbers = $this->_getStreetNumberMatches($street_full);

		if (is_array($numbers) && count($numbers) > 0) {
			return intval($numbers[0]);
		}

		//Docdata requires housenumber, in case none is present add 0 indication.
		return '0';
	}

	/**
	 * Retrieves street number addition from full address
	 *
	 * @param string $street_full Full street
	 *
	 * @return string Streetnumber addition
	 */
	private function _getStreetNumberAddition($street_full) {
		$numbers = $this->_getStreetNumberMatches($street_full);

		if (is_array($numbers) && count($numbers) > 0) {
			return preg_replace('/^[0-9\\s-]+/', '', $numbers[0]);
		}

		return null;
	}

	/**
	 * Retrieves street from full address
	 *
	 * @param string $street_full Full street
	 *
	 * @return string Street
	 */
	private function _getStreetFromAddress($street_full) {
		if (is_array($street_full)) {

			$street_combined = array();
			//combine all adress lines
			foreach ($street_full as $street_line) {
				if (is_string($street_line)) {
					$street_combined[] = trim($street_line);
				}
			}

			if (empty($street_combined)) {
				//empty array found, return empty
				return '';
			} else {
				$street_full = implode(" ", $street_combined);
			}
		} elseif (!is_string($street_full)) {
			return $street_full;
		}

		$numbers = $this->_getStreetNumberMatches($street_full);

		// From here we figure out what is just the street name using the matches
		foreach ($numbers as $number) {
			// Filter out only the numbers taking into account boundries, and dot/commas after it (which define a boundry)
			$street_full = preg_replace('/\b'.$number.'\b[,\.]?/i', '', $street_full);
		}

		return $street_full;
	}

	/**
	 * Gets the possible street number matches excluding common stuff which might also
	 * be present in a street name (1st, 2nd etc).
	 *
	 * @param string $street_full Full street name
	 *
	 * @return array Array with matches in the given string
	 */
	private function _getStreetNumberMatches($street_full) {
		preg_match_all($this->pattern, $street_full, $matches, PREG_SET_ORDER);

		$final_matches = array();
		// Filter out stuff like
		// 1st, 2nd, 3rd, 4th
		foreach ($matches as $number_entry) {
			$number = $number_entry['numbers'];
			$match = substr(trim($number), -2);
			if ($match !== 'st' && $match !== 'nd' && $match !== 'rd' && $match !== 'th') {
				$final_matches[] = $number;
			}
		}

		return $final_matches;
	}

	/**
	 * Retrieves billing data
	 *
	 * @param Order $order Order containing billing info
	 *
	 * @return array Billing data
	 */
	public function getBillTo(Order $order) {
		$billing_address = $order->getBillingAddress();
		return array(
			'name' => $this->getName($billing_address),
			'address' => $this->_getAddressData($billing_address)
		);
	}

	/**
	 * Tries to retrieve an encoding to use with the given string. The beatifull part is that
	 * mb_detect_encoding will always return something usable by other multibyte strings.
	 *
	 * @param string $string String to evaluate the encoding of
	 *
	 * @return string The encoding extracted for the given string
	 */
	private function _getEncoding($string) {
		$encoding = mb_detect_encoding($string);
		return $encoding ? $encoding : self::DEFAULT_ENCODING;
	}

	/**
	 * Limits the given string to the given amount of characters, based upon the encoding of the string
	 *
	 * @param string $string The string to truncate to $length characters
	 * @param int $length The amount of characters that may be present in $string
	 *
	 * @return string The resulting string after peforming a substring on the given string
	 */
	private function _limitLength($string, $length) {
		$encoding = $this->_getEncoding($string);
		if (mb_strlen($string, $encoding) > $length) {
			App::get('helper/data')->dbLog("The string '$string' will be shortened to $length characters.", App::DEBUG);
		}
		return mb_substr($string, 0, $length, $encoding);
	}

	/**
	 * Validates and returns a given email address, throws an exception if the email is not valid.
	 *
	 * @param string $email A suposed email address to be validated
	 *
	 * @return string The email address given or void in case the exception is also thrown
	 */
	private function _validateEmail($email) {
		// This pattern is the one defined in the XSD
		$pattern = '^[_a-zA-Z0-9\-\+\.]+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*(\.[a-zA-Z]+)$';
		if (!preg_match("/$pattern/", $email)) {
			throw new Model_System_Exception(
				__('The email address you tried to use was not accepted by our payment gateway, please choose another while placing the order again.'),
				Model_System_Exception::VALIDATION_EMAIL
			);
		}

		// Validates on email length
		if (mb_strlen($email, $this->_getEncoding($email)) > 100) {
			throw new Model_System_Exception(
				__('The email address you tried to use is too long for our payment gateway, please select a shorter email address when placing the order again.'),
				Model_System_Exception::VALIDATION_EMAIL
			);
		}

		return $email;
	}

}