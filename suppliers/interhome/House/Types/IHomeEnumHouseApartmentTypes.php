<?php
/**
 * File for class IHomeEnumHouseApartmentTypes
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumHouseApartmentTypes originally named HouseApartmentTypes
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumHouseApartmentTypes extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'Apartment'
	 * @return string 'Apartment'
	 */
	const VALUE_APARTMENT = 'Apartment';
	/**
	 * Constant for value 'DetachedHouse'
	 * @return string 'DetachedHouse'
	 */
	const VALUE_DETACHEDHOUSE = 'DetachedHouse';
	/**
	 * Constant for value 'House'
	 * @return string 'House'
	 */
	const VALUE_HOUSE = 'House';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumHouseApartmentTypes::VALUE_NOTSET
	 * @uses IHomeEnumHouseApartmentTypes::VALUE_APARTMENT
	 * @uses IHomeEnumHouseApartmentTypes::VALUE_DETACHEDHOUSE
	 * @uses IHomeEnumHouseApartmentTypes::VALUE_HOUSE
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumHouseApartmentTypes::VALUE_NOTSET,IHomeEnumHouseApartmentTypes::VALUE_APARTMENT,IHomeEnumHouseApartmentTypes::VALUE_DETACHEDHOUSE,IHomeEnumHouseApartmentTypes::VALUE_HOUSE));
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