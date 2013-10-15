<?php
/**
 * File for class IHomeEnumPropertyTypes
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumPropertyTypes originally named PropertyTypes
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumPropertyTypes extends IHomeWsdlClass
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
	 * Constant for value 'Bungalow'
	 * @return string 'Bungalow'
	 */
	const VALUE_BUNGALOW = 'Bungalow';
	/**
	 * Constant for value 'CastleManor'
	 * @return string 'CastleManor'
	 */
	const VALUE_CASTLEMANOR = 'CastleManor';
	/**
	 * Constant for value 'Chalet'
	 * @return string 'Chalet'
	 */
	const VALUE_CHALET = 'Chalet';
	/**
	 * Constant for value 'DetachedHouse'
	 * @return string 'DetachedHouse'
	 */
	const VALUE_DETACHEDHOUSE = 'DetachedHouse';
	/**
	 * Constant for value 'Farmhouse'
	 * @return string 'Farmhouse'
	 */
	const VALUE_FARMHOUSE = 'Farmhouse';
	/**
	 * Constant for value 'HistoricProperty'
	 * @return string 'HistoricProperty'
	 */
	const VALUE_HISTORICPROPERTY = 'HistoricProperty';
	/**
	 * Constant for value 'HolidayVillage'
	 * @return string 'HolidayVillage'
	 */
	const VALUE_HOLIDAYVILLAGE = 'HolidayVillage';
	/**
	 * Constant for value 'SpecialProperty'
	 * @return string 'SpecialProperty'
	 */
	const VALUE_SPECIALPROPERTY = 'SpecialProperty';
	/**
	 * Constant for value 'Villa'
	 * @return string 'Villa'
	 */
	const VALUE_VILLA = 'Villa';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumPropertyTypes::VALUE_NOTSET
	 * @uses IHomeEnumPropertyTypes::VALUE_APARTMENT
	 * @uses IHomeEnumPropertyTypes::VALUE_BUNGALOW
	 * @uses IHomeEnumPropertyTypes::VALUE_CASTLEMANOR
	 * @uses IHomeEnumPropertyTypes::VALUE_CHALET
	 * @uses IHomeEnumPropertyTypes::VALUE_DETACHEDHOUSE
	 * @uses IHomeEnumPropertyTypes::VALUE_FARMHOUSE
	 * @uses IHomeEnumPropertyTypes::VALUE_HISTORICPROPERTY
	 * @uses IHomeEnumPropertyTypes::VALUE_HOLIDAYVILLAGE
	 * @uses IHomeEnumPropertyTypes::VALUE_SPECIALPROPERTY
	 * @uses IHomeEnumPropertyTypes::VALUE_VILLA
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumPropertyTypes::VALUE_NOTSET,IHomeEnumPropertyTypes::VALUE_APARTMENT,IHomeEnumPropertyTypes::VALUE_BUNGALOW,IHomeEnumPropertyTypes::VALUE_CASTLEMANOR,IHomeEnumPropertyTypes::VALUE_CHALET,IHomeEnumPropertyTypes::VALUE_DETACHEDHOUSE,IHomeEnumPropertyTypes::VALUE_FARMHOUSE,IHomeEnumPropertyTypes::VALUE_HISTORICPROPERTY,IHomeEnumPropertyTypes::VALUE_HOLIDAYVILLAGE,IHomeEnumPropertyTypes::VALUE_SPECIALPROPERTY,IHomeEnumPropertyTypes::VALUE_VILLA));
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