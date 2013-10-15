<?php
/**
 * File for class IHomeEnumSpecialOffers
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumSpecialOffers originally named SpecialOffers
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumSpecialOffers extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'AnySpecialOffer'
	 * @return string 'AnySpecialOffer'
	 */
	const VALUE_ANYSPECIALOFFER = 'AnySpecialOffer';
	/**
	 * Constant for value 'EarlyBooker'
	 * @return string 'EarlyBooker'
	 */
	const VALUE_EARLYBOOKER = 'EarlyBooker';
	/**
	 * Constant for value 'LastMinute'
	 * @return string 'LastMinute'
	 */
	const VALUE_LASTMINUTE = 'LastMinute';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumSpecialOffers::VALUE_NOTSET
	 * @uses IHomeEnumSpecialOffers::VALUE_ANYSPECIALOFFER
	 * @uses IHomeEnumSpecialOffers::VALUE_EARLYBOOKER
	 * @uses IHomeEnumSpecialOffers::VALUE_LASTMINUTE
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumSpecialOffers::VALUE_NOTSET,IHomeEnumSpecialOffers::VALUE_ANYSPECIALOFFER,IHomeEnumSpecialOffers::VALUE_EARLYBOOKER,IHomeEnumSpecialOffers::VALUE_LASTMINUTE));
	}
	/**
	 * Method returning the class name
	 * @return string __CLASS__
	 */
	public function __toString()
	{
		return __CLASS__;
	}
}
?>