<?php
/**
 * File for class IHomeEnumActivities
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumActivities originally named Activities
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumActivities extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'Biking'
	 * @return string 'Biking'
	 */
	const VALUE_BIKING = 'Biking';
	/**
	 * Constant for value 'CrossCountrySkiing'
	 * @return string 'CrossCountrySkiing'
	 */
	const VALUE_CROSSCOUNTRYSKIING = 'CrossCountrySkiing';
	/**
	 * Constant for value 'Golfing'
	 * @return string 'Golfing'
	 */
	const VALUE_GOLFING = 'Golfing';
	/**
	 * Constant for value 'Hiking'
	 * @return string 'Hiking'
	 */
	const VALUE_HIKING = 'Hiking';
	/**
	 * Constant for value 'MountainBiking'
	 * @return string 'MountainBiking'
	 */
	const VALUE_MOUNTAINBIKING = 'MountainBiking';
	/**
	 * Constant for value 'Nightlife'
	 * @return string 'Nightlife'
	 */
	const VALUE_NIGHTLIFE = 'Nightlife';
	/**
	 * Constant for value 'NordicWalking'
	 * @return string 'NordicWalking'
	 */
	const VALUE_NORDICWALKING = 'NordicWalking';
	/**
	 * Constant for value 'Riding'
	 * @return string 'Riding'
	 */
	const VALUE_RIDING = 'Riding';
	/**
	 * Constant for value 'Sailing'
	 * @return string 'Sailing'
	 */
	const VALUE_SAILING = 'Sailing';
	/**
	 * Constant for value 'Skiing'
	 * @return string 'Skiing'
	 */
	const VALUE_SKIING = 'Skiing';
	/**
	 * Constant for value 'Snowboarding'
	 * @return string 'Snowboarding'
	 */
	const VALUE_SNOWBOARDING = 'Snowboarding';
	/**
	 * Constant for value 'Surfing'
	 * @return string 'Surfing'
	 */
	const VALUE_SURFING = 'Surfing';
	/**
	 * Constant for value 'Tennis'
	 * @return string 'Tennis'
	 */
	const VALUE_TENNIS = 'Tennis';
	/**
	 * Constant for value 'ThemeParkNearby'
	 * @return string 'ThemeParkNearby'
	 */
	const VALUE_THEMEPARKNEARBY = 'ThemeParkNearby';
	/**
	 * Constant for value 'Toboggan'
	 * @return string 'Toboggan'
	 */
	const VALUE_TOBOGGAN = 'Toboggan';
	/**
	 * Constant for value 'Windsurfing'
	 * @return string 'Windsurfing'
	 */
	const VALUE_WINDSURFING = 'Windsurfing';
	/**
	 * Constant for value 'SkiingSnowboarding'
	 * @return string 'SkiingSnowboarding'
	 */
	const VALUE_SKIINGSNOWBOARDING = 'SkiingSnowboarding';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumActivities::VALUE_NOTSET
	 * @uses IHomeEnumActivities::VALUE_BIKING
	 * @uses IHomeEnumActivities::VALUE_CROSSCOUNTRYSKIING
	 * @uses IHomeEnumActivities::VALUE_GOLFING
	 * @uses IHomeEnumActivities::VALUE_HIKING
	 * @uses IHomeEnumActivities::VALUE_MOUNTAINBIKING
	 * @uses IHomeEnumActivities::VALUE_NIGHTLIFE
	 * @uses IHomeEnumActivities::VALUE_NORDICWALKING
	 * @uses IHomeEnumActivities::VALUE_RIDING
	 * @uses IHomeEnumActivities::VALUE_SAILING
	 * @uses IHomeEnumActivities::VALUE_SKIING
	 * @uses IHomeEnumActivities::VALUE_SNOWBOARDING
	 * @uses IHomeEnumActivities::VALUE_SURFING
	 * @uses IHomeEnumActivities::VALUE_TENNIS
	 * @uses IHomeEnumActivities::VALUE_THEMEPARKNEARBY
	 * @uses IHomeEnumActivities::VALUE_TOBOGGAN
	 * @uses IHomeEnumActivities::VALUE_WINDSURFING
	 * @uses IHomeEnumActivities::VALUE_SKIINGSNOWBOARDING
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumActivities::VALUE_NOTSET,IHomeEnumActivities::VALUE_BIKING,IHomeEnumActivities::VALUE_CROSSCOUNTRYSKIING,IHomeEnumActivities::VALUE_GOLFING,IHomeEnumActivities::VALUE_HIKING,IHomeEnumActivities::VALUE_MOUNTAINBIKING,IHomeEnumActivities::VALUE_NIGHTLIFE,IHomeEnumActivities::VALUE_NORDICWALKING,IHomeEnumActivities::VALUE_RIDING,IHomeEnumActivities::VALUE_SAILING,IHomeEnumActivities::VALUE_SKIING,IHomeEnumActivities::VALUE_SNOWBOARDING,IHomeEnumActivities::VALUE_SURFING,IHomeEnumActivities::VALUE_TENNIS,IHomeEnumActivities::VALUE_THEMEPARKNEARBY,IHomeEnumActivities::VALUE_TOBOGGAN,IHomeEnumActivities::VALUE_WINDSURFING,IHomeEnumActivities::VALUE_SKIINGSNOWBOARDING));
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