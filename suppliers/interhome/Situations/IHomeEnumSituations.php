<?php
/**
 * File for class IHomeEnumSituations
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumSituations originally named Situations
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumSituations extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'ByTheSea'
	 * @return string 'ByTheSea'
	 */
	const VALUE_BYTHESEA = 'ByTheSea';
	/**
	 * Constant for value 'InAHistoricTown'
	 * @return string 'InAHistoricTown'
	 */
	const VALUE_INAHISTORICTOWN = 'InAHistoricTown';
	/**
	 * Constant for value 'InAMajorCity'
	 * @return string 'InAMajorCity'
	 */
	const VALUE_INAMAJORCITY = 'InAMajorCity';
	/**
	 * Constant for value 'InTheCountryside'
	 * @return string 'InTheCountryside'
	 */
	const VALUE_INTHECOUNTRYSIDE = 'InTheCountryside';
	/**
	 * Constant for value 'InALakesideTown'
	 * @return string 'InALakesideTown'
	 */
	const VALUE_INALAKESIDETOWN = 'InALakesideTown';
	/**
	 * Constant for value 'SomewhereQuiet'
	 * @return string 'SomewhereQuiet'
	 */
	const VALUE_SOMEWHEREQUIET = 'SomewhereQuiet';
	/**
	 * Constant for value 'OnAIsland'
	 * @return string 'OnAIsland'
	 */
	const VALUE_ONAISLAND = 'OnAIsland';
	/**
	 * Constant for value 'Center100'
	 * @return string 'Center100'
	 */
	const VALUE_CENTER100 = 'Center100';
	/**
	 * Constant for value 'Center500'
	 * @return string 'Center500'
	 */
	const VALUE_CENTER500 = 'Center500';
	/**
	 * Constant for value 'Center1000'
	 * @return string 'Center1000'
	 */
	const VALUE_CENTER1000 = 'Center1000';
	/**
	 * Constant for value 'Golf200'
	 * @return string 'Golf200'
	 */
	const VALUE_GOLF200 = 'Golf200';
	/**
	 * Constant for value 'Golf500'
	 * @return string 'Golf500'
	 */
	const VALUE_GOLF500 = 'Golf500';
	/**
	 * Constant for value 'Golf5000'
	 * @return string 'Golf5000'
	 */
	const VALUE_GOLF5000 = 'Golf5000';
	/**
	 * Constant for value 'Lake50'
	 * @return string 'Lake50'
	 */
	const VALUE_LAKE50 = 'Lake50';
	/**
	 * Constant for value 'Lake1000'
	 * @return string 'Lake1000'
	 */
	const VALUE_LAKE1000 = 'Lake1000';
	/**
	 * Constant for value 'Lake10000'
	 * @return string 'Lake10000'
	 */
	const VALUE_LAKE10000 = 'Lake10000';
	/**
	 * Constant for value 'Sea50'
	 * @return string 'Sea50'
	 */
	const VALUE_SEA50 = 'Sea50';
	/**
	 * Constant for value 'Sea1000'
	 * @return string 'Sea1000'
	 */
	const VALUE_SEA1000 = 'Sea1000';
	/**
	 * Constant for value 'Sea10000'
	 * @return string 'Sea10000'
	 */
	const VALUE_SEA10000 = 'Sea10000';
	/**
	 * Constant for value 'Skilift50'
	 * @return string 'Skilift50'
	 */
	const VALUE_SKILIFT50 = 'Skilift50';
	/**
	 * Constant for value 'Skilift500'
	 * @return string 'Skilift500'
	 */
	const VALUE_SKILIFT500 = 'Skilift500';
	/**
	 * Constant for value 'Skilift10000'
	 * @return string 'Skilift10000'
	 */
	const VALUE_SKILIFT10000 = 'Skilift10000';
	/**
	 * Constant for value 'CountryView'
	 * @return string 'CountryView'
	 */
	const VALUE_COUNTRYVIEW = 'CountryView';
	/**
	 * Constant for value 'LakeView'
	 * @return string 'LakeView'
	 */
	const VALUE_LAKEVIEW = 'LakeView';
	/**
	 * Constant for value 'MountainView'
	 * @return string 'MountainView'
	 */
	const VALUE_MOUNTAINVIEW = 'MountainView';
	/**
	 * Constant for value 'SeaView'
	 * @return string 'SeaView'
	 */
	const VALUE_SEAVIEW = 'SeaView';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumSituations::VALUE_NOTSET
	 * @uses IHomeEnumSituations::VALUE_BYTHESEA
	 * @uses IHomeEnumSituations::VALUE_INAHISTORICTOWN
	 * @uses IHomeEnumSituations::VALUE_INAMAJORCITY
	 * @uses IHomeEnumSituations::VALUE_INTHECOUNTRYSIDE
	 * @uses IHomeEnumSituations::VALUE_INALAKESIDETOWN
	 * @uses IHomeEnumSituations::VALUE_SOMEWHEREQUIET
	 * @uses IHomeEnumSituations::VALUE_ONAISLAND
	 * @uses IHomeEnumSituations::VALUE_CENTER100
	 * @uses IHomeEnumSituations::VALUE_CENTER500
	 * @uses IHomeEnumSituations::VALUE_CENTER1000
	 * @uses IHomeEnumSituations::VALUE_GOLF200
	 * @uses IHomeEnumSituations::VALUE_GOLF500
	 * @uses IHomeEnumSituations::VALUE_GOLF5000
	 * @uses IHomeEnumSituations::VALUE_LAKE50
	 * @uses IHomeEnumSituations::VALUE_LAKE1000
	 * @uses IHomeEnumSituations::VALUE_LAKE10000
	 * @uses IHomeEnumSituations::VALUE_SEA50
	 * @uses IHomeEnumSituations::VALUE_SEA1000
	 * @uses IHomeEnumSituations::VALUE_SEA10000
	 * @uses IHomeEnumSituations::VALUE_SKILIFT50
	 * @uses IHomeEnumSituations::VALUE_SKILIFT500
	 * @uses IHomeEnumSituations::VALUE_SKILIFT10000
	 * @uses IHomeEnumSituations::VALUE_COUNTRYVIEW
	 * @uses IHomeEnumSituations::VALUE_LAKEVIEW
	 * @uses IHomeEnumSituations::VALUE_MOUNTAINVIEW
	 * @uses IHomeEnumSituations::VALUE_SEAVIEW
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumSituations::VALUE_NOTSET,IHomeEnumSituations::VALUE_BYTHESEA,IHomeEnumSituations::VALUE_INAHISTORICTOWN,IHomeEnumSituations::VALUE_INAMAJORCITY,IHomeEnumSituations::VALUE_INTHECOUNTRYSIDE,IHomeEnumSituations::VALUE_INALAKESIDETOWN,IHomeEnumSituations::VALUE_SOMEWHEREQUIET,IHomeEnumSituations::VALUE_ONAISLAND,IHomeEnumSituations::VALUE_CENTER100,IHomeEnumSituations::VALUE_CENTER500,IHomeEnumSituations::VALUE_CENTER1000,IHomeEnumSituations::VALUE_GOLF200,IHomeEnumSituations::VALUE_GOLF500,IHomeEnumSituations::VALUE_GOLF5000,IHomeEnumSituations::VALUE_LAKE50,IHomeEnumSituations::VALUE_LAKE1000,IHomeEnumSituations::VALUE_LAKE10000,IHomeEnumSituations::VALUE_SEA50,IHomeEnumSituations::VALUE_SEA1000,IHomeEnumSituations::VALUE_SEA10000,IHomeEnumSituations::VALUE_SKILIFT50,IHomeEnumSituations::VALUE_SKILIFT500,IHomeEnumSituations::VALUE_SKILIFT10000,IHomeEnumSituations::VALUE_COUNTRYVIEW,IHomeEnumSituations::VALUE_LAKEVIEW,IHomeEnumSituations::VALUE_MOUNTAINVIEW,IHomeEnumSituations::VALUE_SEAVIEW));
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