<?php
/**
 * File for class IHomeEnumThemeFilterTypes
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumThemeFilterTypes originally named ThemeFilterTypes
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumThemeFilterTypes extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'Cheepcheep'
	 * @return string 'Cheepcheep'
	 */
	const VALUE_CHEEPCHEEP = 'Cheepcheep';
	/**
	 * Constant for value 'Countryside'
	 * @return string 'Countryside'
	 */
	const VALUE_COUNTRYSIDE = 'Countryside';
	/**
	 * Constant for value 'Familyfriendly'
	 * @return string 'Familyfriendly'
	 */
	const VALUE_FAMILYFRIENDLY = 'Familyfriendly';
	/**
	 * Constant for value 'HolidayVillage'
	 * @return string 'HolidayVillage'
	 */
	const VALUE_HOLIDAYVILLAGE = 'HolidayVillage';
	/**
	 * Constant for value 'LakesAndMountains'
	 * @return string 'LakesAndMountains'
	 */
	const VALUE_LAKESANDMOUNTAINS = 'LakesAndMountains';
	/**
	 * Constant for value 'Nightlife'
	 * @return string 'Nightlife'
	 */
	const VALUE_NIGHTLIFE = 'Nightlife';
	/**
	 * Constant for value 'Selection'
	 * @return string 'Selection'
	 */
	const VALUE_SELECTION = 'Selection';
	/**
	 * Constant for value 'SomewhereQuiet'
	 * @return string 'SomewhereQuiet'
	 */
	const VALUE_SOMEWHEREQUIET = 'SomewhereQuiet';
	/**
	 * Constant for value 'SummerHoliday'
	 * @return string 'SummerHoliday'
	 */
	const VALUE_SUMMERHOLIDAY = 'SummerHoliday';
	/**
	 * Constant for value 'Cities'
	 * @return string 'Cities'
	 */
	const VALUE_CITIES = 'Cities';
	/**
	 * Constant for value 'SuitableForSeniors'
	 * @return string 'SuitableForSeniors'
	 */
	const VALUE_SUITABLEFORSENIORS = 'SuitableForSeniors';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumThemeFilterTypes::VALUE_NOTSET
	 * @uses IHomeEnumThemeFilterTypes::VALUE_CHEEPCHEEP
	 * @uses IHomeEnumThemeFilterTypes::VALUE_COUNTRYSIDE
	 * @uses IHomeEnumThemeFilterTypes::VALUE_FAMILYFRIENDLY
	 * @uses IHomeEnumThemeFilterTypes::VALUE_HOLIDAYVILLAGE
	 * @uses IHomeEnumThemeFilterTypes::VALUE_LAKESANDMOUNTAINS
	 * @uses IHomeEnumThemeFilterTypes::VALUE_NIGHTLIFE
	 * @uses IHomeEnumThemeFilterTypes::VALUE_SELECTION
	 * @uses IHomeEnumThemeFilterTypes::VALUE_SOMEWHEREQUIET
	 * @uses IHomeEnumThemeFilterTypes::VALUE_SUMMERHOLIDAY
	 * @uses IHomeEnumThemeFilterTypes::VALUE_CITIES
	 * @uses IHomeEnumThemeFilterTypes::VALUE_SUITABLEFORSENIORS
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumThemeFilterTypes::VALUE_NOTSET,IHomeEnumThemeFilterTypes::VALUE_CHEEPCHEEP,IHomeEnumThemeFilterTypes::VALUE_COUNTRYSIDE,IHomeEnumThemeFilterTypes::VALUE_FAMILYFRIENDLY,IHomeEnumThemeFilterTypes::VALUE_HOLIDAYVILLAGE,IHomeEnumThemeFilterTypes::VALUE_LAKESANDMOUNTAINS,IHomeEnumThemeFilterTypes::VALUE_NIGHTLIFE,IHomeEnumThemeFilterTypes::VALUE_SELECTION,IHomeEnumThemeFilterTypes::VALUE_SOMEWHEREQUIET,IHomeEnumThemeFilterTypes::VALUE_SUMMERHOLIDAY,IHomeEnumThemeFilterTypes::VALUE_CITIES,IHomeEnumThemeFilterTypes::VALUE_SUITABLEFORSENIORS));
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