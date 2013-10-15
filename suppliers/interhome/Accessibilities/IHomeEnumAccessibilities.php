<?php
/**
 * File for class IHomeEnumAccessibilities
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumAccessibilities originally named Accessibilities
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumAccessibilities extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'FamilyFrienldy'
	 * @return string 'FamilyFrienldy'
	 */
	const VALUE_FAMILYFRIENLDY = 'FamilyFrienldy';
	/**
	 * Constant for value 'LiftsInhouse'
	 * @return string 'LiftsInhouse'
	 */
	const VALUE_LIFTSINHOUSE = 'LiftsInhouse';
	/**
	 * Constant for value 'NonSmoking'
	 * @return string 'NonSmoking'
	 */
	const VALUE_NONSMOKING = 'NonSmoking';
	/**
	 * Constant for value 'PetsWelcome'
	 * @return string 'PetsWelcome'
	 */
	const VALUE_PETSWELCOME = 'PetsWelcome';
	/**
	 * Constant for value 'PetsNotAllowed'
	 * @return string 'PetsNotAllowed'
	 */
	const VALUE_PETSNOTALLOWED = 'PetsNotAllowed';
	/**
	 * Constant for value 'SuitableForSeniors'
	 * @return string 'SuitableForSeniors'
	 */
	const VALUE_SUITABLEFORSENIORS = 'SuitableForSeniors';
	/**
	 * Constant for value 'WheelchairAccessible'
	 * @return string 'WheelchairAccessible'
	 */
	const VALUE_WHEELCHAIRACCESSIBLE = 'WheelchairAccessible';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumAccessibilities::VALUE_NOTSET
	 * @uses IHomeEnumAccessibilities::VALUE_FAMILYFRIENLDY
	 * @uses IHomeEnumAccessibilities::VALUE_LIFTSINHOUSE
	 * @uses IHomeEnumAccessibilities::VALUE_NONSMOKING
	 * @uses IHomeEnumAccessibilities::VALUE_PETSWELCOME
	 * @uses IHomeEnumAccessibilities::VALUE_PETSNOTALLOWED
	 * @uses IHomeEnumAccessibilities::VALUE_SUITABLEFORSENIORS
	 * @uses IHomeEnumAccessibilities::VALUE_WHEELCHAIRACCESSIBLE
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumAccessibilities::VALUE_NOTSET,IHomeEnumAccessibilities::VALUE_FAMILYFRIENLDY,IHomeEnumAccessibilities::VALUE_LIFTSINHOUSE,IHomeEnumAccessibilities::VALUE_NONSMOKING,IHomeEnumAccessibilities::VALUE_PETSWELCOME,IHomeEnumAccessibilities::VALUE_PETSNOTALLOWED,IHomeEnumAccessibilities::VALUE_SUITABLEFORSENIORS,IHomeEnumAccessibilities::VALUE_WHEELCHAIRACCESSIBLE));
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